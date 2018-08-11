<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class AllegroProduct extends ObjectModel
{
	public $id;
	public $id_product;
	public $id_product_attribute;
	public $id_allegro_theme = 0;
	public $id_allegro_shipping = 0;
	public $image_cover;
	public $images_excl;
	public $relist_min_qty;
	public $stock_sync;
	public $price_sync;
	public $cache_relist_error;

	// Multishop object update fix
	public $update_fields = array();

    // Cache product & combination object
    public $product = null;
    public $combination = null;

    const IMAGE_SHOP = 'shop';
    const IMAGE_ALLEGRO = 'allegro';

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'allegro_product',
		'primary' => 'id_allegro_product',
		'fields' => array(
			//'country' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_product' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'id_product_attribute' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_allegro_theme' => 		array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'id_allegro_shipping' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'image_cover' => 			array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'images_excl' => 			array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'cache_relist_error' => 	array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            /* Shop fields */
            'relist_min_qty' => 	   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'shop' => true),
			'stock_sync' => 		   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'shop' => true),
			'price_sync' => 	       array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'shop' => true),
		),
	);

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        // PS 1.5.1.x
        // @todo
        if ($id && method_exists('Shop', 'addTableAssociation')) {
            Shop::addTableAssociation('allegro_product', array('type' => 'shop'));
        }

        parent::__construct($id, $id_lang, $id_shop);

        $this->product = new Product((int)$this->id_product, false, AllegroTools::getLangId());

        if ($this->id_product_attribute) {
            $this->combination = new Combination((int)$this->id_product_attribute);
        }
    }


	public function getAPImages($idLang = null/* @deprecated*/)
	{
		$imagesExcl = array();
		if ($this->images_excl) {
			$imagesExcl = explode(',', $this->images_excl);
		}

        $images = Db::getInstance()->executeS('
			SELECT DISTINCT image_shop.`cover`, i.`id_image` AS id, "shop" AS src, i.`position`
				FROM `'._DB_PREFIX_.'image` i
                '.(!Configuration::get('ALLEGRO_SHARE_IMAGES') && $this->id_product_attribute ? '
                JOIN `'._DB_PREFIX_.'product_attribute_image` pai
                    ON (pai.`id_image` = i.`id_image`
                    AND pai.`id_product_attribute` = '.(int)$this->id_product_attribute.')' : '').'
				'.Shop::addSqlAssociation('image', 'i').'
				WHERE i.`id_product` = '.(int)$this->id_product.
				(count($imagesExcl) ? ' AND i.`id_image` NOT IN ('.implode(',', $imagesExcl).')' : '').'
			UNION ALL
			SELECT NULL, ai.`id_allegro_image` AS id, "allegro" AS src, ai.`id_allegro_image` AS position
				FROM `'._DB_PREFIX_.'allegro_image` ai
				WHERE ai.`id_allegro_product` = '.(int)$this->id.'
        ');

        // Set allegro cover
        list($imageFrom, $id) = null;
        if ($this->image_cover) {
	        list($imageFrom, $id) = explode(':', $this->image_cover);
	    }

        foreach ($images as $key => &$image) {
        	if ($id && $imageFrom == $image['src'] && $id == $image['id']) {
        		$image['cover'] = 1;
        		$image['position'] = 0;
        	} elseif (!$id && $image['cover']) {
        		$image['cover'] = 1;
        		$image['position'] = 0;
        	} else {
        		$image['cover'] = 0;
        	}
        }

        // Sort by position (allegro images by ID)
		usort($images, function($a, $b) {
		    return $a['position'] > $b['position'];
		});

        return $images;
	}


	public function getImages()
	{
		return Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'allegro_image`
			WHERE `id_allegro_product` = '.(int)$this->id
		);
	}

	/**
	 * Generate price in PLN
	 * 
	 * Product catalog price * allegro marge,
	 * price is rounded depends on preferences
	 **/
	public function genPrice()
	{
        return self::genPriceStatic($this->id_product, $this->id_product_attribute);
	}

    public static function genPriceStatic($id_product, $id_product_attribute = null)
    {
        $priceBN = (float)Product::getPriceStatic(
            $id_product, 
            true,   // Use tax
            $id_product_attribute, 
            2,      // Decimals
            null,   // Divisor
            false,  // Only reduc
            true,   // Use reduc
            1,      // Quantity
            false,  // DEPRECATED
            null,   // Customer ID
            (int)Configuration::get('ALLEGRO_ID_CART'), 
            null,   // Address ID
            $nothing, // Specific price
            true,   // Ecotax
            true,   // Use group reduction
            null,   // Context
            false   // Use customer price
        );

        /**
         * Convert price to PLN if
         * default currency is not PLN
         **/
        $currency = Currency::getDefaultCurrency();
        if ($currency->iso_code !== 'PLN') {
            $currency = new Currency(Currency::getIdByIsoCode('PLN'));

            if (!$currency->id) {
                throw new Exception('Currency "PLN" does not exists');
            }
        }
        $priceBN = $priceBN * (float)$currency->conversion_rate;

        /**
         * Add price marge
         **/
        if ($marge = (float)Configuration::get('ALLEGRO_PRICE_PC')) {
            $priceBN = $priceBN * (($marge+100)/100);
        }

        /**
         * Price rounding
         **/
        $round = (int)Configuration::get('ALLEGRO_PRICE_ROUND');
        switch ($round) {
            case 1:
                $priceBN = round($priceBN);
                break;
            case 2:
                $priceBN = ceil($priceBN);
                break;
            case 3:
                $priceBN = floor($priceBN);
                break;
            case 4:
                $priceBN = (round($priceBN)-0.01);
                break;
            default:
                $priceBN = round($priceBN, 2);
                break;
         }

        return $priceBN;
    }


	/**
	 * Generate offer title
	 **/
    public function genTitle($noCut = false)
    {
        $titleGenPattern = Configuration::get('ALLEGRO_TITLE_GEN_PATTERN');

        if ($titleGenPattern) {
        	
        	$idLang = Language::getIdByIso('pl');
        	$product = new Product((int)$this->id_product, false, $idLang);
			/**
			 * Get attributes names
			 **/
			$c = null;
		    $attrNames = array();
		    if ($this->id_product_attribute) {
		        $c = new Combination($this->id_product_attribute);
		        $attributes = $c->getAttributesName($idLang);
		        foreach ($attributes as $key => $attribute) {
		             $attrNames[] = $attribute['name'];
		        }
		    }  

            $title = str_replace(
                array(
                    '[product_name]',
                    '[manufacturer_name]',
                    '[attributes]',
                    '[reference]',
                    '[price]',
                ),
                array(
                    $product->name,
                    $product->manufacturer_name,
                    count($attrNames)
                    	? implode(',', $attrNames)
                    	: '',
                    ($c && $c->reference // If exists use combinaton reference
                    	? $c->reference 
                    	: $product->reference),
                    AllegroTools::formatPrice($this->genPrice()),
                ),
                $titleGenPattern
            );
        } else {
            $title = $product->name.(count($attrNames) ? ' '.implode(',', $attrNames) : '');
        }

        // Replace one or more whitespace with single whitespace
        $title = preg_replace('/\s+/', ' ', $title);

        // Remove non printable chars (PS 1.7x fix)
        $title = preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', $title);

        // Cut
        if (Configuration::get('ALLEGRO_CUT_TITLE') && !$noCut) {
        	$title = mb_substr($title, 0, 50);
        }

        return $title;
    }


	/**
	 * Get product/combination stock quantity
	 **/
	public function getQuantity($idShop)
	{
		return (int)StockAvailable::getQuantityAvailableByProduct(
            (int)$this->id_product,
            (int)$this->id_product_attribute,
            (int)$idShop
        );
	}


    /**
     * Get EAN 13 code
     **/
    public function getEan13()
    {
        if ($this->combination && $this->combination->ean13) {
            return $this->combination->ean13;
        } elseif ($this->product->ean13) {
            return $this->product->ean13;
        }

        return null;
    }


    /**
     * Returns allegro theme ID
     **/
    public function getThemeId()
    {
        if ($this->id_allegro_theme > 0) {
            return $this->id_allegro_theme;
        } elseif($this->id_allegro_theme == 0) {
            if ($theme = AllegroTheme::getDefault(true)) {
                return $theme->id;
            }
        }

        return false;
    }


    public function getAttributesNames()
    {
        $idLang = Language::getIdByIso('pl');

        if (!$this->id_product_attribute) {
            return false;
        }

        return Db::getInstance()->GetValue('
            SELECT GROUP_CONCAT(agl.`name`, \' - \',al.`name` ORDER BY agl.`id_attribute_group` SEPARATOR \', \') as attribute_designation
            FROM `'._DB_PREFIX_.'product_attribute_combination` pac
            LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$idLang.')
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$idLang.')
            WHERE pac.id_product_attribute = '.(int)$this->id_product_attribute.'
            GROUP BY pac.id_product_attribute'
        );
    }

    public static function getIdByPAId($idProduct, $idProductAttribute = 0)
    {
        return Db::getInstance()->getValue('
            SELECT `id_allegro_product`
            FROM `'._DB_PREFIX_.'allegro_product`
            WHERE `id_product` = '.(int)$idProduct.'
            AND `id_product_attribute` = '.(int)$idProductAttribute
        );
    }
}