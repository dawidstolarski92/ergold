<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

/**
* Class used for generating offer description
* in new (JSON) and legacy format
**/
class AllegroContentBuilder
{
	/**
	 * Base content (JSON | HTML)
	 **/
	private $content;


	/**
	 * Content type
	 **/
	private $type;


	/**
	 * Allegro product object
	 **/
	private $allegroProduct;


	/**
	 * Allegro fields list
	 **/
	private $fieldsList;


	/**
	 * SMARTY object instance
	 **/
	private $smarty;


	/**
	 * HTMLPurifier object instance
	 **/
	private $htmlPurifier;


	/**
	 * Normalized photo fields
	 **/
	private $photoFieldsMap = array(
        'PHOTO_1'  => 'PHOTO_FID_16',
        'PHOTO_2'  => 'PHOTO_FID_17',
        'PHOTO_3'  => 'PHOTO_FID_18',
        'PHOTO_4'  => 'PHOTO_FID_19',
        'PHOTO_5'  => 'PHOTO_FID_20',
        'PHOTO_6'  => 'PHOTO_FID_21',
        'PHOTO_7'  => 'PHOTO_FID_22',
        'PHOTO_8'  => 'PHOTO_FID_23',
        'PHOTO_9'  => 'PHOTO_FID_342',
        'PHOTO_10' => 'PHOTO_FID_343',
        'PHOTO_11' => 'PHOTO_FID_344',
        'PHOTO_12' => 'PHOTO_FID_345',
        'PHOTO_13' => 'PHOTO_FID_346',
        'PHOTO_14' => 'PHOTO_FID_347',
        'PHOTO_15' => 'PHOTO_FID_348',
        'PHOTO_16' => 'PHOTO_FID_349',
    );

	const TYPE_JSON = 1;
	const TYPE_HTML = 2;

	const COL_TEXT = 'TEXT';
	const COL_IMAGE = 'IMAGE';
	
	function __construct($type, $content, AllegroProduct $allegroProduct, $fieldsList)
	{
		$this->type = (int)$type;
		$this->allegroProduct = $allegroProduct;
		$this->fieldsList = $fieldsList;

		if ($this->type == self::TYPE_JSON) {
			$this->content = json_decode($content);

			$jsonError = json_last_error();
			if ($jsonError !== 0) {
				throw new Exception("Error parsing JSON (error code: {$jsonError})");
			}

			$this->initHtmlPurifier();
		} elseif ($this->type == self::TYPE_HTML) {
			if (!is_string($content)) {
				throw new Exception("Content should be a string");
			}

			$this->content = $content;
		}

		$this->initSmarty();
	}


	/**
	 * Init SMARTY class
	 **/
	private function initSmarty()
	{
		$this->smarty = new Smarty();

        smartyRegisterFunction($this->smarty, 'modifier', 'truncate', 'smarty_modifier_truncate');
        smartyRegisterFunction($this->smarty, 'modifier', 'cleanHtml', 'smartyCleanHtml');

        $idLang = AllegroTools::getLangId();

        $this->smarty->assign(array(
            'link' => new Link(null, 'http://'),
            'allegro_product' => $this->allegroProduct,
            'product' => $this->allegroProduct->product,
            'product_combination' => $this->allegroProduct->combination,
            'images' => $this->allegroProduct->getAPImages(),
            'allegro_img_url' => _PS_BASE_URL_.__PS_BASE_URI__.'modules/allegro/img/product/',
            'stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
            'customizationFields' => $this->allegroProduct->product->customizable 
            	? $this->allegroProduct->product->getCustomizationFields($idLang) 
            	: false,
            'manufacturer' => new Manufacturer((int)$this->allegroProduct->product->id_manufacturer, $idLang),
            'features' => $this->allegroProduct->product->getFrontFeatures($idLang),
            'attachments' => $this->allegroProduct->product->cache_has_attachments
            	? $this->allegroProduct->product->getAttachments($idLang) 
            	: array(),
            'allegro_fields' => $this->fieldsList,
            'attributes_names' => $this->allegroProduct->getAttributesNames(),
        ));
	}


	/**
	 * Init HTML Purifier
	 **/
	private function initHtmlPurifier()
	{
		/**
		 * PS include own HTMLPurifier (depends on user config)
		 * so we must check if class not already exist.
		 **/
		if (!class_exists('HTMLPurifier')) {
			include_once dirname(__FILE__).'/../libs/HTMLPurifier/HTMLPurifier.auto.php';
		}

        $config = HTMLPurifier_Config::createDefault();

        $config->set('HTML.TidyLevel', 'heavy');
        $config->set('AutoFormat.AutoParagraph', true);
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('Core.EscapeInvalidChildren', true);
        $config->set('HTML.Allowed', 'p,h1,h2,ul,ol,li,b');

        $cacheDir = dirname(__FILE__) . '/../../../cache/purifier';

        if (is_writable($cacheDir)) {
            $config->set('Cache.SerializerPath', $cacheDir);
        }

        $this->htmlPurifier = new HTMLPurifier($config);
	}


	/**
	 * Build and return valid JSON or HTML content
	 **/
	public function build($preview = false)
	{
		switch ($this->type) {
			case self::TYPE_JSON:
				return $this->buildJson($preview);
				break;
			
			case self::TYPE_HTML:
				return $this->buildLegacy();
				break;
		}
	}


	/**
	 * Build JSON content
	 **/
	private function buildJson($preview)
	{
		// @todo
		$final = array();

		/**
		 * Iterate over each section and column
		 **/
		foreach ($this->content as $sectionId => &$section) {
			$finalRow = array();
			foreach ($section as $colId => &$col) {
				/**
				 * Photo section
				 * 
				 * Replace photo tag ex. PHOTO_5 by allegro style tag
				 * If preview replace tag by html <img... tag
				 **/
				if (preg_match("/PHOTO_\d{1,2}/", $col)) {
					$imageFieldId = (int)preg_replace('/\D/', '', $this->photoFieldsMap[$col]);

					// Check if image exists in fields list
					if (!empty($this->fieldsList[$imageFieldId])) {
						$finalRow['items'][] = array(
                            'type'  => self::COL_IMAGE,
                            'url'   => ($preview 
	                            ? $this->fieldsList[$imageFieldId]
	                            : $this->photoFieldsMap[$col])
                        );
					}
				} else {
				/**
				 * HTML section
				 * 
				 * Cleanup html and process theme markers
				 **/
					$col = $this->processMarkers($col);
					$col = $this->fixEntities($col);
					$col = $this->purifyHtml($col);

					if (trim($col) !== '') {
					    $finalRow['items'][] = array(
	                        'type'      => self::COL_TEXT,
	                        'content'   => $col
	                    );
					}
				}
			}

			// If empty section - remove
			if (!empty($finalRow)) {
				$final['sections'][] = $finalRow;
			}
		}

		return $final;
	}


	/**
	 * Build legacy content (HTML)
	 **/
	private function buildLegacy()
	{
		return $this->processMarkers($this->content);
	}


	/**
	 * Build HTML preview
	 **/
	private function buildPreview()
	{
		// @todo
	}


	/**
	 * Purify HTML using HTML Purifier Class
	 * 
	 * Remove all non allowed HTML tags
	 * and all attributes, clean html entities etc.
	 **/
	private function purifyHtml($html)
	{
		// @todo
		$html = preg_replace("/<strong>(.*?)<\/strong>/", "<b>$1</b>", $html);
		$html = preg_replace("/<h3>(.*?)<\/h3>/", "<h2>$1</h2>", $html);
		$html = preg_replace("/<h4>(.*?)<\/h4>/", "<h2>$1</h2>", $html);

        return $this->htmlPurifier->purify($html);
	}


	/**
	 * Process theme markers
	 * 
	 * Handle new markers ex. [name|param:value]
	 **/
	private function processMarkers($html)
	{
		$this->processRetroMarkers($html);

		$output = '';
        $outputArray = array();
        preg_match_all("/\[([a-z_0-9]+)(?:\|)?([a-z_]+:[a-z0-9_,:]+)?]/", $html, $outputArray);

        foreach ($outputArray[0] as $key => $marker) {

            $name = $outputArray[1][$key];
            $params = $outputArray[2][$key];

            $tplFile = _ALLEGRO_THEME_TPL_DIR_.$name.'.tpl';

            if (file_exists($tplFile)) {

                if($params) {
                    $params = explode(',', $params);
                    $paramsSplit = array();
                    foreach ($params as $param) {
                        $param = explode(':', $param);
                        $paramsSplit[$param[0]] = $param[1];
                    }

                    $this->smarty->assign($paramsSplit);
                };

                $output = $this->smarty->fetch($tplFile);
            }

            $html = str_replace($marker, $output, $html);
        }

        return $html;
	}


	/**
	 * Handle retrocompatibilty with 2.x-3.0.x themes ex. {param}
	 **/
	private function processRetroMarkers($html)
	{
        $retroMarkers = array(
            '{auctionTitle}' 		=> '[auction_title]',
            '{productDescription}' 	=> '[product_description]',
            '{productDescriptionShort}' => '[product_description_short]',
            '{productDescriptionExternal}' => '[description_external]',
            '{productName}' 		=> '[product_name]',
            '{productWeight}' 		=> '[product_weight]',
            '{productEan13}' 		=> '[product_ean13]',
            '{productReference}' 	=> '[product_reference]',
            '{productAttributes}' 	=> (int)$this->allegroProduct->id_product_attribute,
            '{productPrice}' 		=> '[product_price]',
            '{auctionTitle}' 		=> '[auction_title]',
            '{imagesExternal}' 		=> '[auction_images]',
            '{gallery}' 			=> '[auction_images]',
            '{idManufacturer}' 		=> '[manufacturer_id]',
            '{manufacturerName}' 	=> '[manufacturer_name]',
            '{idProduct}' 			=> '[product_id]',
            '{idProductAttribute}' 	=> '[product_attribute_id]',
            '{idServiceCategory}' 	=> '[allegro_category_id]',
            '{productFetures}' 		=> '[product_features]',
        );

      	// Get product images type
        $imagesTypes = AllegroTools::getProductImageTypes();

        foreach ($imagesTypes as $type) {
            $type['width'] <= 800 ? $l = $type : null;
        }

        foreach ($imagesTypes as $type) {
            $retroMarkers['{images_'.$type['name'].'}'] = '[images|type:'.$type['name'].',type_link:'.$l['name'].']';
            $retroMarkers['{imageCover_'.$type['name'].'}'] = '[image|index:0,type:'.$type['name'].',type_link:'.$l['name'].']';

            // {image_n_type}
            for ($i=0; $i < 100; $i++) { 
                $retroMarkers['{image_'.$i.'_'.$type['name'].'}'] = '[image|index:'.$i.',type:'.$type['name'].',type_link:'.$l['name'].']';
            }
        }

        return strtr($html, $retroMarkers);
	}


	/**
	 * Fix HTML entities
	 * 
	 * Convert all HTML entities except: " & ' < >
	 **/
	private function fixEntities($html)
	{
        $replace = array(
            '&amp;' 	=> '|AMP|', 	// &
            '&#38;' 	=> '|AMP|', 
            '&quot;' 	=> '|QUOT|', 	// "
            '&#34;' 	=> '|QUOT|',
            '&lt;' 		=> '|LT|', 		// <
            '&#60;' 	=> '|LT|', 
            '&gt;' 		=> '|GT|',  	// >
            '&#62;' 	=> '|GT|',
            '&apos;' 	=> '|APOS|', 	// '
            '&#39;' 	=> '|APOS|', 
        );

        // Replace allowed entities by tmp tags
        $html = strtr($html, $replace);
        // Convert all HTML entities to their applicable characters (except allowed)
        $html = html_entity_decode($html);
        // Restore allowed entities
        $html = strtr($html, array_flip($replace));

        return $html;
	}
}