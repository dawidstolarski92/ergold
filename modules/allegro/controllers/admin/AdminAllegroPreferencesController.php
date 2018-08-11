<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

include_once dirname(__FILE__) . '/../ParentAllegroController.php';
include_once dirname(__FILE__) . '/../../allegro.inc.php';

class AdminAllegroPreferencesController extends ParentAllegroController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->context = Context::getContext();
		$this->className = 'Configuration';
		$this->multishop_context = Shop::CONTEXT_ALL;

		parent::__construct();
		parent::initApi();
		
		if (count($this->moduleErrors)) {
			$this->errors = $this->moduleErrors;
			return;
		}

		// Products list
		$fields = array(
			'ALLEGRO_SHOW_IMAGE' => array(
				'title' => $this->l('Show images on list'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			),
			'ALLEGRO_SHOW_REFERENCE' => array(
				'title' => $this->l('Show reference on list'),
				'u' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			),
			'ALLEGRO_SHOW_BASE_PRICE' => array(
				'title' => $this->l('Show base price on list'),
				'u' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			),
			'ALLEGRO_SHOW_STATUS' => array(
				'title' => $this->l('Show status on list'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			),
			'ALLEGRO_SHOW_COMBINATIONS' => array(
				'title' => $this->l('Show cambinations on list'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			),

            // Form
			'ALLEGRO_SHARE_IMAGES' => array(
				'title' => $this->l('Share images in combinations'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
                'desc' => $this->l('If enabled product images from base product will be used in all combinations.'),
			),
            'ALLEGRO_PRICE_PC' => array(
                'title' => $this->l('Price change'),
                'validation' => 'isFloat',
                'type' => 'text',
                'class' => 'fixed-width-xxl',
                'desc' => $this->l('Allow you to increase / decrease "Buy now" price by % (ex. "-15" = -15%), set "0" to disable.'),
            ),
            'ALLEGRO_PRICE_ROUND' => array(
                'title' => $this->l('Price round'),
                'validation' => 'isInt',
                'cast' => 'intval',
                'type' => 'select',
                'list' => array(
                    array('name' => $this->l('No'), 'value' => 0),
                    array('name' => $this->l('Yes - auto - full'), 'value' => 1),
                    array('name' => $this->l('Yes - up - full'), 'value' => 2),
                    array('name' => $this->l('Yes - down - full'), 'value' => 3),
                    array('name' => $this->l('Yes - auto - .99'), 'value' => 4),
                ),
                'identifier' => 'value',
            ),
            'ALLEGRO_TITLE_GEN_PATTERN' => array(
                'title' => $this->l('Title generator'),
                'validation' => 'isGenericName',
                'type' => 'text',
                'class' => '',
                'size' => '100', // PS 1.5x
                'desc' => $this->l('Allow you to generate title from params, available tags:').' [product_name], [reference], [price], [manufacturer_name], [attributes].',
            ),
		);

		$this->fields_options['product_list'] = array(
			'title' =>	$this->l('Products list / form'),
			'icon' =>	'icon-book',
			'fields' =>	$fields,
			'submit' => array('title' => $this->l('Save')),
		);

		// Themes
		$fields = array(
			'ALLEGRO_THEME_TINYMCE' => array(
				'title' => $this->l('Enable TinyMCE editor in theme form (old format)'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
				'desc' => $this->l('Editor may cut off CSS styles from theme, disable if you have problems with themes.'),
			),
			'ALLEGRO_THEME_HTMLPURIFIER' => array(
				'title' => $this->l('Enable "HTML Purifier"'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
				'desc' => $this->l('This feture helps convert descriptions to allegro standards (recommend).'),
			),
		);

		$this->fields_options['themes'] = array(
			'title' =>	$this->l('Themes'),
			'icon' =>	'icon-book',
			'fields' =>	$fields,
			'submit' => array('title' => $this->l('Save')),
		);

		// Auction creation
        $imagesTypes = ImageType::getImagesTypes('products');
        foreach ($imagesTypes as $key => &$value) {
            $value['type_name'] = $value['name'];
            $value['name'] .= ' ('.$value['width'].'x'.$value['height'].'px)';
        }
        $imagesTypes[] = array('type_name' => '', 'name' => $this->l('Original image (not recommended)'));

		$fields = array(
			'ALLEGRO_NB_IMAGES' => array(
				'title' => $this->l('Max nb. images send to API'),
				'validation' => 'isInt',
				'cast' => 'intval',
				'type' => 'select',
				'list' => array(
					array('name' => 0, 'value' => 0),
					array('name' => 1, 'value' => 1),
                    array('name' => 2, 'value' => 2),
                    array('name' => 3, 'value' => 3),
                    array('name' => 4, 'value' => 4),
                    array('name' => 5, 'value' => 5),
                    array('name' => 6, 'value' => 6),
                    array('name' => 7, 'value' => 7),
                    array('name' => 8, 'value' => 8),
                    array('name' => 9, 'value' => 9),
                    array('name' => 10, 'value' => 10),
                    array('name' => '11 '.$this->l('(business account only)'), 'value' => 11),
                    array('name' => '12 '.$this->l('(business account only)'), 'value' => 12),
                    array('name' => '13 '.$this->l('(business account only)'), 'value' => 13),
                    array('name' => '14 '.$this->l('(business account only)'), 'value' => 14),
                    array('name' => '15 '.$this->l('(business account only)'), 'value' => 15),
                    array('name' => '16 '.$this->l('(business account only)'), 'value' => 16),
					),
				'identifier' => 'value',
				'desc' => $this->l('Max nb. images send to Allegro gallery.'),
			),
            'ALLEGRO_IMAGE_TYPE' => array(
				'title' => $this->l('Type of images sent to API'),
				'validation' => 'isGenericName',
				'type' => 'select',
				'list' => $imagesTypes,
				'identifier' => 'type_name',
				'desc' => $this->l('800x600px recommended.'),
			),
			'ALLEGRO_CUT_TITLE' => array(
				'title' => $this->l('Auto cut title to 50 chars'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
				'desc' => $this->l('If enabled auction title will be automatically cut to 50 characters if needed.'),
			),
			'ALLEGRO_A_LIST_SUGGEST' => array(
				'title' => $this->l('Suggest product on "My auction" tab'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
				'desc' => $this->l('Module will try to find matching product for auction on "My auction" tab.'),
			),
			'ALLEGRO_EAN' => array(
				'title' => $this->l('Send EAN code'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			),
		);

		$this->fields_options['auctions'] = array(
			'title' =>	$this->l('Auctions'),
			'icon' =>	'icon-book',
			'fields' =>	$fields,
			'submit' => array('title' => $this->l('Save')),
		);

		// Stock sync
		$fields = array(
			'ALLEGRO_STOCK_SYNC' => array(
				'title' => $this->l('Stock sync'),
                'validation' => 'isInt',
                'cast' => 'intval',
                'type' => 'select',
                'list' => array(
                    //array('id' => 0, 'name' => $this->l('Disabled')),
                    array('id' => 1, 'name' => $this->l('Bidirectional')),
                    array('id' => 2, 'name' => $this->l('Only store stocks')),
                ),
                'identifier' => 'id',
                'desc' => $this->l('If "Only store stocks" is enabled stocks on auctions will not be updated and auction will not be finished.'),
			),
			'ALLEGRO_FINISH_IF_DISABLED' => array(
				'title' => $this->l('Finish auction if product is disabled'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
				'desc' => $this->l('If you enable this option sync process will finish auction if product in catalog is disabled (no matter stock quantity), also disabled product will not be relisted.'),
			),
		);

		$this->fields_options['stock_sync'] = array(
			'title' =>	$this->l('Stock sync'),
			'icon' =>	'icon-book',
			'fields' =>	$fields,
			'submit' => array('title' => $this->l('Save')),
		);


		// Order import
        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
		$prestaCarriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);

		$fields = array(
            'ALLEGRO_ORDER_SYNC' => array(
                'title' => $this->l('Import orders'),
                'validation' => 'isInt',
                'cast' => 'intval',
                'type' => 'select',
                'list' => array(
                    array('id' => 0, 'name' => $this->l('No')),
                    array('id' => 1, 'name' => $this->l('Yes - only shop products')),
                    array('id' => 2, 'name' => $this->l('Yes - all products')),
                ),
                'identifier' => 'id'
            ),
            'ALLEGRO_ORDER_SYNC_TYPE' => array(
                'title' => $this->l('Import order when'),
                'validation' => 'isInt',
                'cast' => 'intval',
                'type' => 'select',
                'list' => array(
                    array('id' => 2, 'name' => $this->l('Pay form filled')),
                    array('id' => 4, 'name' => $this->l('Order paid')),
                ),
                'identifier' => 'id'
            ),
            'ALLEGRO_ORDER_ID_SHOP' => array(
                'title' => $this->l('Default import shop'),
                'validation' => 'isInt',
                'cast' => 'intval',
                'type' => 'select',
                'list' => Shop::getShops(),
                'identifier' => 'id_shop'
            ),
			'ALLEGRO_CUSTOMER_NEWSLETTER' => array(
				'title' => $this->l('Add customers to newsletter'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			),
			'ALLEGRO_PAYU_NEW' => array(
				'title' => $this->l('PayU status "New"'),
				'validation' => 'isInt',
				'cast' => 'intval',
                'type' => 'select',
                'list' => $statuses,
                'identifier' => 'id_order_state'
			),
			'ALLEGRO_PAYU_FINISHED' => array(
				'title' => $this->l('PayU status "Finished"'),
				'validation' => 'isInt',
				'cast' => 'intval',
                'type' => 'select',
                'list' => $statuses,
                'identifier' => 'id_order_state'
			),
			'ALLEGRO_PAYU_CANCELED' => array(
				'title' => $this->l('PayU status "Canceled"'),
				'validation' => 'isInt',
				'cast' => 'intval',
                'type' => 'select',
                'list' => $statuses,
                'identifier' => 'id_order_state'
			),
			'ALLEGRO_PAYU_ERROR' => array(
				'title' => $this->l('PayU status "Unknown/error"'),
				'validation' => 'isInt',
				'cast' => 'intval',
                'type' => 'select',
                'list' => $statuses,
                'identifier' => 'id_order_state'
			),
			'ALLEGRO_COD' => array(
				'title' => $this->l('Payment status "Cash on delivery"'),
				'validation' => 'isInt',
				'cast' => 'intval',
                'type' => 'select',
                'list' => $statuses,
                'identifier' => 'id_order_state'
			),
			'ALLEGRO_WIRE_TRANSFER' => array(
				'title' => $this->l('Payment status "Wire transfer"'),
				'validation' => 'isInt',
				'cast' => 'intval',
                'type' => 'select',
                'list' => $statuses,
                'identifier' => 'id_order_state'
			),
			'ALLEGRO_CARRIER' => array(
				'title' => $this->l('Order default carrier'),
				'validation' => 'isInt',
				'cast' => 'intval',
                'type' => 'select',
                'list' => $prestaCarriers,
                'identifier' => 'id_carrier',
			),
		);

		$this->fields_options['order_import'] = array(
			'title' =>	$this->l('Order import'),
			'icon' =>	'icon-cogs',
			'fields' =>	$fields,
			'submit' => array('title' => $this->l('Save')),
		);
  

		// Order import - shipping
		try {
		    $allegroCarriers = $this->api->doGetShipmentData()->shipmentDataList->item;
		} catch (Exception $e) {
			$this->errors[] = $this->l('Unable to fetch allegro shipping options.');
		}

		if (isset($allegroCarriers) && count($allegroCarriers) && count($prestaCarriers)) {

			// Add empty value
			array_unshift($prestaCarriers, array('name' => $this->l('- Use default -'), 'id_carrier' => 0));

			$fields = array();

			foreach ($allegroCarriers as $key => $shippingOption) {
				$fields['ALLEGRO_SHIPPING_'.(int)$shippingOption->shipmentId] = array(
					'title' => $shippingOption->shipmentName,
	                'validation' => 'isInt',
	                'cast' => 'intval',
	                'type' => 'select',
	                'list' => $prestaCarriers,
	                'identifier' => 'id_carrier',
	                'desc' => ((int)$shippingOption->shipmentType == 1 ? '' : $this->l('Pay on delivery'))
				);
			}

			$this->fields_options['order_import_shipping'] = array(
				'title' =>	$this->l('Order import - shipping'),
				'icon' =>	'icon-cogs',
				'fields' =>	$fields,
				'submit' => array('title' => $this->l('Save')),
			);
		}


		// Dev mode
		$fields = array(
			'ALLEGRO_DEV_MODE' => array(
				'title' => $this->l('Enable "Dev mode"'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
				'desc' => $this->l('Enable this feature only if you need, may cause problems!'),
			),
		);

        $fields['ALLEGRO_SEND_STATS'] = array(
            'title' => $this->l('Allow to send statistics'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
        );

        $fields['ALLEGRO_LOG'] = array(
            'title' => $this->l('Enable log'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
        );

        $fields['ALLEGRO_NO_EXECUTE_HOOK'] = array(
            'title' => $this->l('Do not execute PrestaShop hooks'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
            'desc' => $this->l('Enable if you want skip PrestaShop hooks executions (actionValidateOrder).'),
        );

        $fields['ALLEGRO_START_TIME'] = array(
            'title' => $this->l('Force start time'),
            'validation' => 'isGenericName',
            'type' => 'text',
            'class' => 'fixed-width-xxl',
			'desc' => $this->l('Ex.: "21-03-2017", "+1 day".'),
        );

        $fields['ALLEGRO_ID_CART'] = array(
            'title' => $this->l('Cart ID (price calculation)'),
            'validation' => 'isUnsignedId',
            'type' => 'text',
            'class' => 'fixed-width-xxl',
        );

        $fields['ALLEGRO_SHIPPING_FORM_FLAT'] = array(
            'title' => $this->l('Flat shipping options form'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
            'desc' => $this->l('Enable if there are missing shipping options.'),
        );

        $fields['ALLEGRO_LEGACY_IMAGES'] = array(
            'title' => $this->l('Images legacy fix'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
            'desc' => $this->l('Enable if you using images lagacy mode.'),
        );

        $fields['ALLEGRO_3X_UPGRADE'] = array(
            'title' => $this->l('Enable upgrade form'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
        );

        $fields['ALLEGRO_CLEANUP'] = array(
            'title' => $this->l('Cleanup'),
            'validation' => 'isBool',
            'cast' => 'intval',
            'type' => 'bool',
            'desc' => $this->l('Be careful, backup recommended.'),
        );

		$this->fields_options['dev_mode'] = array(
			'title' =>	$this->l('Advanced parameters'),
			'icon' =>	'icon-cogs',
			'fields' =>	$fields,
			'submit' => array('title' => $this->l('Save')),
		);
	}

	public function init()
	{
		if (Tools::getValue('ALLEGRO_CLEANUP')) {
			$this->module->cleanup();
		}

		parent::init();
		
        Configuration::updateValue('ALLEGRO_CLEANUP', 0, false, 0, 0);
	}
}
