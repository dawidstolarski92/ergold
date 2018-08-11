<?php
/*
*
*  @author Promokit Co. <support@promokit.eu>
*  @copyright  2013 Promokit Co.
*  @version  Release: $Revision: 0 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Promokit Co.
*/

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\BestSales\BestSalesProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class Pk_Products extends Module implements WidgetInterface {

	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'pk_products';
		$this->version = '1.6';
		$this->author = 'promokit.eu';
		$this->need_instance = 0;
		$this->bootstrap = 1;
		$this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->trans('Promokit Products', array(), 'Modules.Products.Admin');
		$this->description = $this->trans('Display Products in different views', array(), 'Modules.Products.Admin');

		$this->hooks = array(
			'content_top',
			'content_bottom',
			'displayHome'
		);

		$this->options = array(
			'content_top' => array(
				'TYPE_ACTIVE' => 'featured',
				'PRODUCTS_FEA' => true,
				'PRODUCTS_NEW' => false,
				'PRODUCTS_SPE' => false,
				'PRODUCTS_BES' => false,
				'PK_RANDOM' => false,
				'PK_WIDE' => false,
				'PK_HIGHLIGHTED' => false,
				'PK_HIGHLIGHTED_PRODUCT' => 1,
				'PK_NBR_VIS' => 5,
				'PK_NBR' => 6,
			),
			'content_bottom' => array(
				'TYPE_ACTIVE' => 'special',
				'PRODUCTS_FEA' => false,
				'PRODUCTS_NEW' => false,
				'PRODUCTS_SPE' => true,
				'PRODUCTS_BES' => false,
				'PK_RANDOM' => false,
				'PK_WIDE' => false,
				'PK_HIGHLIGHTED' => false,
				'PK_HIGHLIGHTED_PRODUCT' => 1,
				'PK_NBR_VIS' => 5,
				'PK_NBR' => 6,
			),
			'displayHome' => array(
				'TYPE_ACTIVE' => 'new',
				'PRODUCTS_FEA' => false,
				'PRODUCTS_NEW' => true,
				'PRODUCTS_SPE' => false,
				'PRODUCTS_BES' => false,
				'PK_RANDOM' => false,
				'PK_WIDE' => false,
				'PK_HIGHLIGHTED' => false,
				'PK_HIGHLIGHTED_PRODUCT' => 1,
				'PK_NBR_VIS' => 5,
				'PK_NBR' => 6,
			),
		);

	}

	public function install() {

		if (!parent::install() OR
			!$this->registerHook('displayHeader') OR
			!$this->registerHook('content_top') OR
			!$this->registerHook('content_bottom') OR
            !$this->registerHook('displayHome')
			) return false;

		foreach ($this->options as $hook => $options) {
			foreach ($options as $key => $value) {
				if (!Configuration::updateValue($hook.'__'.$key, $value))
					return false;
			}
		}
		if (!Configuration::updateValue('PK_HOOK', 'displayHome'))
			return false;

		return true;

	}


	public function uninstall() {

		foreach ($this->options as $hook => $options) {
			foreach ($options as $key => $value) {
				if (!Configuration::deleteByName($hook.'__'.$key)) {
					return false;
				}
			}
		}

        if (!parent::uninstall()) {
            return false;
        }

        return true;
	}

	public function check_state($args)
    {
        if (Module::isInstalled('pk_themesettings')) {
            require_once _PS_MODULE_DIR_.'pk_themesettings/inc/common.php';
            $check_state = new Pk_ThemeSettings_Common();
            return $check_state->getModuleState($args);
        } else {
            return true;
        }
    }

	public function getContent() {

		$info = '';

		if (Tools::isSubmit('pk_products_submit')) {

			$errors = array();

			if (Tools::getValue('PK_HOOK') != Configuration::get('PK_HOOK')) {

				Configuration::updateValue('PK_HOOK', Tools::getValue('PK_HOOK'));//save only hook

			} else {

				foreach ($this->options as $hook => $options) { // write changes to DB
					if (Tools::getValue('PK_HOOK') == $hook) {
					foreach ($options as $key => $value) { // write changes to DB
						if ($key != 'PK_NBR' && $key != 'PK_NBR_VIS' && $key != 'PK_HOOK') { // skip some options to validate
							Configuration::updateValue(Tools::getValue('PK_HOOK').'__'.$key, Tools::getValue($key));
						}
					}
					}
				}

				$nbr = intval(Tools::getValue('PK_NBR'));
				$nbr_vis = intval(Tools::getValue('PK_NBR_VIS'));


				if (!$nbr OR $nbr <= 0 OR !Validate::isInt($nbr) OR ($nbr < $nbr_vis))
					$errors[] = $this->trans('Invalid "Carousel Products Number"', array(), 'Admin.Notifications.Error');
				else
					Configuration::updateValue(Tools::getValue('PK_HOOK').'__PK_NBR', $nbr);


				if (!$nbr_vis OR $nbr_vis <= 0 OR !Validate::isInt($nbr_vis) OR ($nbr < $nbr_vis))
					$errors[] = $this->trans('Invalid "Carousel Visible Products Number"', array(), 'Admin.Notifications.Error');
				else
					Configuration::updateValue(Tools::getValue('PK_HOOK').'__PK_NBR_VIS', $nbr_vis);


				if (isset($errors) AND sizeof($errors))
					$info .= $this->displayError(implode('<br />', $errors));
				else
					$info .= $this->displayConfirmation($this->trans('Settings updated', array(), 'Admin.Notifications.Success'));


			}

			parent::_clearCache($this->templateFile);

		}


		return $info.$this->renderForm();
	}

	public function renderForm() {

		$hook_list = array();
		foreach ($this->hooks as $hook) {
			$hook_list[] = array('id' => $hook, 'name' => $hook);
		}

		$allProducts = $this->getAllProducts();

		$fields_form_00 = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->trans('Available Hooks', array(), 'Modules.Products.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->trans('Select hook to configure', array(), 'Modules.Products.Admin'),
						'desc' => $this->trans('Save selected hook before configuration', array(), 'Modules.Products.Admin'),
						'name' => 'PK_HOOK',
						'options' => array(
							'query' => $hook_list,
							'id' => 'id',
							'name' => 'name'
						)
					),
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
					'name' => 'pk_products_submit',
				)
			),
		);

		$fields_form_01 = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->trans('Products type', array(), 'Modules.Products.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->trans('New Products', array(), 'Modules.Products.Admin'),
						'name' => 'PRODUCTS_NEW',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Featured Products', array(), 'Modules.Products.Admin'),
						'name' => 'PRODUCTS_FEA',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Special Products', array(), 'Modules.Products.Admin'),
						'name' => 'PRODUCTS_SPE',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Bestsellers Products', array(), 'Modules.Products.Admin'),
						'name' => 'PRODUCTS_BES',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
					'name' => 'pk_products_submit',
				)
			),
		);
		$fields_form_02 = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->trans('Products Display Mode', array(), 'Modules.Products.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->trans('Products Sorting: Random', array(), 'Modules.Products.Admin'),
						'name' => 'PK_RANDOM',
						'is_bool' => true,
						'class' => 'fixed-width-xxl',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Full width', array(), 'Modules.Products.Admin'),
						'name' => 'PK_WIDE',
						'is_bool' => true,
						'class' => 'fixed-width-xxl',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Full Width', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('Fixed Width', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Show Hightlighted Product', array(), 'Modules.Products.Admin'),
						'name' => 'PK_HIGHLIGHTED',
						'is_bool' => true,
						'class' => 'fixed-width-xxl',
						'values' => array(
							array(
								'id' => 'hl_on',
								'value' => true,
								'label' => $this->trans('Show', array(), 'Admin.Global')
							),
							array(
								'id' => 'hl_off',
								'value' => false,
								'label' => $this->trans('Hide', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'select',
						'label' => $this->trans('Select product', array(), 'Modules.Products.Admin'),
						'desc' => $this->trans('This product will be hightlighted', array(), 'Modules.Products.Admin'),
						'name' => 'PK_HIGHLIGHTED_PRODUCT',
						'options' => array(
							'query' => $allProducts,
							'id' => 'id',
							'name' => 'name'
						)
					),
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
					'name' => 'pk_products_submit',
				)
			),
		);

		$fields_form_03 = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->trans('Carousel Settings', array(), 'Modules.Products.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->trans('Active Tab by Default', array(), 'Modules.Products.Admin'),
						'name' => 'TYPE_ACTIVE',
						'options' => array(
							'query' => array(
								array(
									'id' => 'featured',
									'name' => $this->trans('Featured', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'special',
									'name' => $this->trans('Special', array(), 'Modules.instafeed.Admin')),
								//array(
								//	'id' => 'bestsellers',
									//'name' => $this->trans('Bestsellers', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'new',
									'name' => $this->trans('New', array(), 'Modules.instafeed.Admin')),
							),
							'id' => 'id',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Carousel Products Number', array(), 'Modules.Products.Admin'),
						'name' => 'PK_NBR',
						'class' => 'fixed-width-sm'
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Carousel Visible Products Number', array(), 'Modules.Products.Admin'),
						'name' => 'PK_NBR_VIS',
						'class' => 'fixed-width-sm'
					),	/*
					array(
						'type' => 'switch',
						'label' => $this->trans('Change product image on hover', array(), 'Modules.Products.Admin'),
						'name' => 'PK_PRODUCT_HOVER',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Show countdown', array(), 'Modules.Products.Admin'),
						'name' => 'PK_COUNTDOWN',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Show product Description', array(), 'Modules.Products.Admin'),
						'name' => 'PK_PRODUCT_DESC',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Show product labels', array(), 'Modules.Products.Admin'),
						'name' => 'PK_PRODUCT_LABELS',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Show product color variations', array(), 'Modules.Products.Admin'),
						'name' => 'PK_PRODUCT_COLORS',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Show product manufacturer', array(), 'Modules.Products.Admin'),
						'name' => 'PK_PRODUCT_BRAND',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					*/
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
					'name' => 'pk_products_submit',
				)
			),
		);


		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		//$helper->languages = $languages;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->allow_employee_form_lang = true;
		$helper->toolbar_scroll = true;
		$helper->toolbar_btn = $this->initToolbar();
		$helper->title = $this->displayName;
		//$helper->submit_action = 'pk_products_submit';
		$hook = Tools::getValue('PK_HOOK', Configuration::get('PK_HOOK'));
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues($hook),
		);

		return $helper->generateForm(array($fields_form_00, $fields_form_01, $fields_form_02, $fields_form_03));
	}

	private function initToolbar() {

		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->trans('Save', array(), 'Admin.Actions')
		);

		return $this->toolbar_btn;
	}

	public function getConfigFieldsValues($hook) {

		$values = array();
		foreach ($this->options[$hook] as $key => $value) { // read options from DB
			$values[$key] = Configuration::get($hook.'__'.$key);
			$values['PK_HOOK'] = $hook;
		}

		return $values;
	}

	private function getAllProducts() {

		$products = new Product();
		$all = $products->getProducts($this->context->language->id, 0, 0, 'id_product', 'DESC');

		$return = array();
		foreach ($all as $id => $product) {
			$return[$id]['id'] = $product['id_product'];
			$return[$id]['name'] = $product['name'];
		}

		return $return;
	}

	public function start() {
		return microtime(true);
	}
	public function end($start) {
		$time_end = microtime(true);
		$execution_time = ($time_end - $start);
		echo '<b>Total Execution Time:</b> '.$execution_time.' Seconds';
	}

	public function getProducts($params) {

		//if (!$this->isCached($this->templateFile, $this->getCacheId($this->name))) {
			//$start = $this->start();
			$options = $this->getConfigFieldsValues($params['hook']);

			$idLang = $this->context->language->id;
			$orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
	   		$orderWay = Tools::getProductsOrder('way', Tools::getValue('orderway'));

			if ($options['PRODUCTS_NEW']) {
				$new = Product::getNewProducts($idLang, 0, ($options['PK_NBR'] ? $options['PK_NBR'] : 10), false, $orderBy, $orderWay); /*get new products*/
				$product_kit["new"] = $this->prepareBlocksProducts($new, $params['hook']);
			}
			if ($options['PRODUCTS_FEA']) {
				$category = new Category(Context::getContext()->shop->getCategory(), Configuration::get('PS_LANG_DEFAULT'));
				if ($options['PK_RANDOM'] > 0) { /* get random products	*/
		            $featured = $category->getProducts($idLang, 1, ($options['PK_NBR'] ? $options['PK_NBR'] : 10), $orderBy, $orderWay, false, true, true, ($options['PK_NBR'] ? $options['PK_NBR'] : 10));	/* get featured products	*/
		        } else {
		            $featured = $category->getProducts($idLang, 1, ($options['PK_NBR'] ? $options['PK_NBR'] : 10), $orderBy, $orderWay);	 /* get featured 	*/
		        }
		        foreach ($featured as $key => $product) {
		        	//print_r($product);
		        	if ($product['manufacturer_name'] == '') {
		        		$featured[$key]['manufacturer_name'] = 'test';
		        	}
		        }

				$product_kit["featured"] = $this->prepareBlocksProducts($featured, $params['hook']);
				//echo "<div class='hidden'>";print_r($product_kit["featured"]);echo "</div>";
			}
			if ($options['PRODUCTS_SPE']) {
				$specials = Product::getPricesDrop($idLang, 0, $options['PK_NBR'], false, $orderBy, $orderWay);
				$product_kit["special"] = $this->prepareBlocksProducts($specials, $params['hook']);
			}
			if ($options['PRODUCTS_BES']) {
				$product_kit["bestsellers"] = $this->getBestSellers(($options['PK_NBR'] ? $options['PK_NBR'] : 10));
			}

			$tabs = array(
				'featured' => $this->trans('Featured', array(), 'Admin.Global'),
				'special' => $this->trans('Special', array(), 'Admin.Global'),
				'bestsellers' => $this->trans('Bestsellers', array(), 'Admin.Global'),
				'new' => $this->trans('New', array(), 'Admin.Global')
			);

			$pr_kit = $pr_type = array();

			if (isset($product_kit)) {

				foreach ($product_kit as $key => $value)
					if ($key == $options['TYPE_ACTIVE']) {
						$pr_kit[$key] = $value;
						$pr_type[$key] = $key;
					}

				foreach ($product_kit as $key => $value)
					if ($key != $options['TYPE_ACTIVE']) {
						$pr_kit[$key] = $value;
						$pr_type[$key] = $key;
					}

			}


			// prepare smarty variables
			$smarty_opts = array(
				'pk_pr_hook' => $params['hook'],
				'nonce' => rand(10000, 99999),
			);
			$smarty_opts['bundle'][$params['hook']] = array(
				'tabs' => $tabs,
				'products_kit' => $pr_kit,
				'products_types' => $pr_type
			);

			if ($options['PK_HIGHLIGHTED'] == 1) {
				if ($options['PK_HIGHLIGHTED_PRODUCT']) {

					$first_selected = reset($pr_kit);

					$pr_kit_four_products[0] = array_slice($first_selected, 0, 4);

                    $product = new Product((int)$options['PK_HIGHLIGHTED_PRODUCT'], true, $idLang, $this->context->shop->id);

                    if (Validate::isLoadedObject($product) && isset($product->name[Context::getContext()->language->id])) {

                    	$product = array((array)$product);
                    	$product[0]['id_product'] = $product[0]['id'];
                        $smarty_opts['bundle'][$params['hook']] = array(
							'hl_product' => $this->prepareBlocksProducts( $product, $params['hook'] ),
							'products_kit' => $pr_kit_four_products,
						);
                    }
                }
			}

			foreach ($this->options as $hook => $options) {
				if ($params['hook'] == $hook) {
					foreach ($options as $key => $value) { // write changes to DB
						$smarty_opts['bundle'][$hook]['opts'][strtolower($key)] = Configuration::get($hook.'__'.$key);
					}
				}
			}

			$smarty_opts['json_opts'] = json_encode($smarty_opts['bundle'][$params['hook']]['opts'], JSON_PRETTY_PRINT);
			$this->context->smarty->assign($smarty_opts);
			//$this->end($start);
		//}

	}

	protected function getBestSellers($nb)
    {

        $searchProvider = new BestSalesProductSearchProvider(
            $this->context->getTranslator()
        );

        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

        $nProducts = (int) Configuration::get('PS_BLOCK_BESTSELLERS_TO_DISPLAY');

        $query
            ->setResultsPerPage($nb)
            ->setPage(1)
        ;

        $query->setSortOrder(SortOrder::random());

        $result = $searchProvider->runQuery(
            $context,
            $query
        );

        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $products_for_template = [];

        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return $products_for_template;
    }

	public function prepareBlocksProducts($block, $hook) {

        $blocks_for_template = [];
        $products_for_template = [];

        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(new ImageRetriever($this->context->link), $this->context->link, new PriceFormatter(), new ProductColorsRetriever(), $this->context->getTranslator());
        $products_for_template = [];
        if ($block){
            foreach ($block as $key => $rawProduct) {

                $products_for_template[$key] = $presenter->present($presentationSettings, $assembler->assembleProduct($rawProduct), $this->context->language);
                $products_for_template[$key]['quantity_wanted'] = 1;
                if ($products_for_template[$key]['manufacturer_name'] == '') {
	                $products_for_template[$key]['manufacturer_name'] = Manufacturer::getNameById($rawProduct['id_manufacturer']);
	            }
            }
        }

        return $products_for_template;
    }

    public function hookdisplayHome($params) {

        $params['hook'] = 'displayHome';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getProducts($params);
			//return $this->fetch($this->templateFile, $this->getCacheId($this->name));
			return $this->fetch($this->templateFile);
        }

    }

    public function hookcontent_top($params) {

        $params['hook'] = 'content_top';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getProducts($params);
			return $this->fetch($this->templateFile);
        }

    }

    public function hookcontent_bottom($params) {

        $params['hook'] = 'content_bottom';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getProducts($params);
			return $this->fetch($this->templateFile);
        }

    }

	public function hookDisplayHeader($params) {

		if ($this->context->controller->php_self == "index") {
			$this->context->controller->addCSS($this->_path.'assets/css/styles.css', 'all');
			$this->context->controller->addJS($this->_path.'assets/js/scripts.js', 'all');
		}

	}

	public function renderWidget($hookName = null, array $configuration = []) {

        $this->smarty->assign(
        	$this->getWidgetVariables($hookName, $configuration)
        );
        return $this->fetch($this->templateFile);

    }

    public function getWidgetVariables($hookName = null, array $configuration = []) {

        return $configuration;

    }

}

?>
