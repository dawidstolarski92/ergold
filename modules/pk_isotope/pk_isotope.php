<?php

if (!defined('_PS_VERSION_'))
	exit;

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

class pk_isotope extends Module implements WidgetInterface
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'pk_isotope';
		$this->version = '1.7.1';
		$this->author = 'promokit.eu';
		$this->need_instance = 0;
		$this->DBtable = _DB_PREFIX_.'pk_isotope';
		$this->bootstrap = true;
		$this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

		parent::__construct();

		$this->displayName = $this->trans('Isotope Product Filter', array(), 'Modules.isotope.Admin');
		$this->description = $this->trans('Displays featured, new, special products on your homepage', array(), 'Modules.isotope.Admin');

		$this->check_state = true;
        if (Module::isInstalled('pk_themesettings')) {
            require_once _PS_MODULE_DIR_.'pk_themesettings/inc/common.php';
            $this->check_state = new Pk_ThemeSettings_Common();
        }
	}

	function install()
	{
		if (
			!Configuration::updateValue('ISOTOPE_NBR', 2) || 
			!Configuration::updateValue('ISOTOPE_ADD_METHOD', 1) || 
			!Configuration::updateValue('ISOTOPE_MAX', 8) ||
			!Configuration::updateValue('ISOTOPE_FEA', 1) ||
			!Configuration::updateValue('ISOTOPE_SPE', 1) ||
			!Configuration::updateValue('ISOTOPE_BES', 1) ||
			!Configuration::updateValue('ISOTOPE_NEW', 1) ||
			!Configuration::updateValue('ISOTOPE_COL', 4) ||
			!Configuration::updateValue('ISOTOPE_CAT', '') ||
			!parent::install() || 
			!$this->registerHook('displayHome') ||
			!$this->registerHook('content_top') ||
			!$this->registerHook('content_bottom') ||
			!$this->registerHook('displayHeader'))
			return false;

		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'.$this->DBtable.'`');

		if (!Db::getInstance()->Execute('
				CREATE TABLE `'.$this->DBtable.'` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
					`data` VARCHAR(100), 
					PRIMARY KEY (`id`)
				) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;'))
				return false;
		if (!Db::getInstance()->Execute('
				INSERT INTO `'.$this->DBtable.'` (
					`id`, `data`
				) VALUES (1,1),(2,2);'))
				return false;
		return true;
	}

	public function getContent()
	{
		$output = "";
		if (Tools::isSubmit('isotope_settings'))
		{
			$nbr = (int)(Tools::getValue('nbr'));
			Configuration::updateValue('ISOTOPE_ADD_METHOD', (int)(Tools::getValue('admethod')));
			Configuration::updateValue('ISOTOPE_MAX', (int)(Tools::getValue('isotope_max')));
			Configuration::updateValue('ISOTOPE_FEA', (int)(Tools::getValue('isotope_fea')));
			Configuration::updateValue('ISOTOPE_SPE', (int)(Tools::getValue('isotope_spe')));
			Configuration::updateValue('ISOTOPE_BES', (int)(Tools::getValue('isotope_bes')));
			Configuration::updateValue('ISOTOPE_NEW', (int)(Tools::getValue('isotope_new')));
			Configuration::updateValue('ISOTOPE_COL', (int)(Tools::getValue('isotope_col')));
			Configuration::updateValue('ISOTOPE_CAT', Tools::getValue('isotope_cat'));
		
			if (!$nbr OR $nbr <= 0 OR !Validate::isInt($nbr))
				$errors[] = $this->trans('An invalid number of products has been specified', array(), 'Admin.Notifications.Error');
			else
				Configuration::updateValue('ISOTOPE_NBR', (int)($nbr));
			if (isset($errors) AND sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->trans('Your settings have been updated', array(), 'Admin.Notifications.Success'));

			parent::_clearCache($this->name.'.tpl');
			
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$this->context->controller->addJS($this->_path.'assets/js/admin.js');

		$meth = (int)(Configuration::get('ISOTOPE_ADD_METHOD'));
		$max = (int)(Configuration::get('ISOTOPE_MAX'));
		$col = (int)(Configuration::get('ISOTOPE_COL'));
		$spe = (int)(Configuration::get('ISOTOPE_SPE'));
		$fea = (int)(Configuration::get('ISOTOPE_FEA'));
		$bes = (int)(Configuration::get('ISOTOPE_BES'));
		$new = (int)(Configuration::get('ISOTOPE_NEW'));
		$cat = Configuration::get('ISOTOPE_CAT');

		$cats = explode(",", $cat);
		$cat = implode(",", array_unique($cats));

		$id_lang = Context::getContext()->language->id;

		$categories = Category::getCategories(intval($id_lang), false);		
		$sql = 'SELECT data FROM `'.$this->DBtable.'`';
		$data = Db::getInstance()->executeS($sql);		

		$htmlPrd = '';

		foreach ($data as $key => $item)
			$htmlPrd .= $this->htmlCodeProducts($item["data"], "");		

		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" id="module_form" class="defaultForm form-horizontal">
		<div class="panel" id="fieldset_0">												
			<div class="panel-heading"><i class="icon-cogs"></i> '.$this->trans('Isotope Settings', array(), 'Modules.isotope.Admin').'</div>
			<div class="form-wrapper">
				<div class="form-group'.(($meth == 0) ? ' hide ' : '').'">
					<label class="control-label col-lg-3">'.$this->trans('Featured Products', array(), 'Modules.isotope.Admin').'</label>
					<div class="col-lg-6"><input type="checkbox" name="isotope_fea" id="isotope_fea" value="1" '.(($fea == 1) ? 'checked ' : '').'/></div>
				</div>
				<div class="form-group'.(($meth == 0) ? ' hide ' : '').'">
					<label class="control-label col-lg-3">'.$this->trans('Special Products', array(), 'Modules.isotope.Admin').'</label>
					<div class="col-lg-6"><input type="checkbox" name="isotope_spe" id="isotope_spe" value="1" '.(($spe == 1) ? 'checked ' : '').'/></div>
				</div>
				<div class="form-group'.(($meth == 0) ? ' hide ' : '').'">
					<label class="control-label col-lg-3">'.$this->trans('Bestsellers Products', array(), 'Modules.isotope.Admin').'</label>
					<div class="col-lg-6"><input type="checkbox" name="isotope_bes" id="isotope_bes" value="1" '.(($bes == 1) ? 'checked ' : '').'/></div>
				</div>
				<div class="form-group'.(($meth == 0) ? ' hide ' : '').'">
					<label class="control-label col-lg-3">'.$this->trans('New Products', array(), 'Modules.isotope.Admin').'</label>
					<div class="col-lg-6"><input type="checkbox" name="isotope_new" id="isotope_new" value="1" '.(($new == 1) ? 'checked ' : '').'/></div>
				</div>
				<div class="form-group catlist'.(($meth == 0) ? ' hide ' : '').'">
					<label class="control-label col-lg-3">'.$this->trans('Categories', array(), 'Admin.Global').'</label>
					<div class="col-lg-3">
						'.$this->displayCategoriesSelect($categories, 0).'						
					</div>
					<div class="col-lg-3">
						<div class="tabcategories"><ul>'.$this->getCatList($cat, true).'</ul></div>
						<input type="hidden" name="isotope_cat" class="isotope_cat" value="'.$cat.'">
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="control-label col-lg-3">'.$this->trans('Automatically get products', array(), 'Modules.isotope.Admin').'</label>
					<div class="col-lg-6">
						<input type="radio" name="admethod" id="auto_admethod" value="1" '.(($meth == 1) ? 'checked ' : '').'/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">'.$this->trans('Manually get products', array(), 'Modules.isotope.Admin').'</label>
					<div class="col-lg-6">
						<input type="radio" name="admethod" id="manual_admethod" value="0" '.(($meth == 0) ? 'checked ' : '').'/>
					</div>
				</div>
				<div class="form-group automatical_method'.(($meth == 0) ? ' hide ' : '').'">
					<label class="control-label col-lg-3">'.$this->trans('Define the number of products', array(), 'Modules.isotope.Admin').'</label>
					<div class="col-lg-1">
						<input type="text" size="5" name="nbr" value="'.Tools::safeOutput(Tools::getValue('nbr', (int)(Configuration::get('ISOTOPE_NBR')))).'" />
					</div>
					<label class="control-label col-lg-3" style="clear: left"></label>
					<div class="col-lg-8"><p class="help-block">'.$this->trans('Define the number of each type/category of products that you would like to display on your homepage. Total you will have defined number multiplied to the number of selected types and categories', array(), 'Modules.isotope.Admin').'</p>
					</div>
				</div>
				<div class="form-group automatical_method'.(($meth == 0) ? ' hide ' : '').'">
					<label class="control-label col-lg-3">'.$this->trans('Define the number of products in row', array(), 'Modules.isotope.Admin').'</label>
					<div class="col-lg-1">
						<input type="number" step="1" min="3" max="6" size="5" name="isotope_col" value="'.Tools::safeOutput(Tools::getValue('isotope_col', (int)(Configuration::get('ISOTOPE_COL')))).'" />
					</div>
				</div>
				<div class="form-group'.(($meth == 0) ? ' hide ' : '').'">
					<label class="control-label col-lg-3">'.$this->trans('Define the max number of products', array(), 'Modules.isotope.Admin').'</label>
					<div class="col-lg-1">
						<input type="text" size="5" name="isotope_max" value="'.Tools::safeOutput($max, (int)(Configuration::get('ISOTOPE_MAX'))).'" />
					</div>
					<label class="control-label col-lg-3" style="clear: left"></label>
					<div class="col-lg-8"><p class="help-block">'.$this->trans('Sometimes total number of products can be more than you want. With this option you can set maximum total number of products', array(), 'Modules.isotope.Admin').'</p>
					</div>
				</div>
				<div class="form-group">
					<div class="manually_method'.(($meth == 1) ? ' hide ' : '').'">
						<div class="categoriesArea col-lg-3">
						<label>' . $this->trans('Categories', array(), 'Admin.Global') . '</label>
						'.$this->displayCategoriesSelect($categories, 0).'						
						</div>
						<div id="allprdcts" class="col-lg-4">
							<label>' . $this->trans('All Products from category', array(), 'Modules.isotope.Admin') . '</label>
							<div class="contain"></div>
						</div>
						<div id="selectedprdcts" class="col-lg-5">
							<label>' . $this->trans('Selected Products', array(), 'Modules.isotope.Admin') . '</label>
							<div class="contain">'.$htmlPrd.'</div>
						</div>					
					</div>
				</div>
			</div>
			<div class="panel-footer">
			<input type="hidden" data-path="'._MODULE_DIR_.$this->name.'" id="datapath">
			<button type="submit" value="1" id="module_form_submit_btn" name="isotope_settings" class="btn btn-default pull-right"><i class="process-icon-save"></i> '.$this->trans('Save', array(), 'Admin.Actions').'</button>
			</div>
		</div>
		</form>';
		$this->context->controller->addCSS(($this->_path).'assets/css/bo_isotopeFilter.css', 'all');
		return $output;
	}

	public function ajaxCall() {

		$nb = (int)(Configuration::get('ISOTOPE_NBR'));
		$category = new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id);
		$products["featured"] = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8));
		$products["new"]=Product::getNewProducts((int)Context::getContext()->language->id, 0, ($nb ? $nb : 8));

		if (!empty($products)) {
			foreach($products as $product) {
				if (!empty($product)) {
					foreach($product as $data) {
						$pr_html="";
						$imageData = Image::getCover($data['id_product']);
						$imgLink = $this->context->link->getImageLink($data['link_rewrite'], (int)$data['product_id'].'-'.(int)$imageData['id_image'], 'home_default');
						
						$pr_html.="<li class=\"ajax_block_product new_products isotope-hidden isotope-item\" >";
						$pr_html.="<a href=\"".$data['link']."\" title=\"".$data['name']."\" class=\"product_image\">";
							$pr_html.="<img src=\"".$imgLink."\" alt=\"".$data['name']."\"></a>";
						$pr_html.="<h5 class=\"s_title_block\">";
							$pr_html.="<a href=\"".$data['link']."\" title=\"".$data['name']."\">".$data['name']."</a>";
						$pr_html.="</h5> <div class=\"product_desc\">";
							$pr_html.=$data['description_short'];
						$pr_html.="</div><div>";
							$pr_html.="<p class=\"price_container\"><span class=\"price\">".Product::getPriceStatic((int)$data['id_product'], true, NULL)."</span></p>";
									$pr_html.="<a class=\"exclusive ajax_add_to_cart_button\" rel=\"ajax_id_product_1\" href=\"".$data['link']."\" title=\"Add to cart\">Add to cart</a></div></li>";
						print_r($pr_html);
																		
					}
				}
			}
		}
	

	}

	public function hookDisplayHeader($params)
	{
		if ($this->context->controller->php_self == "index") {
			$this->context->controller->registerStylesheet($this->name, 'modules/'.$this->name.'/assets/css/styles.css', ['media' => 'all', 'priority' => 150]);
			$this->context->controller->registerJavascript($this->name.'-isotope', 'modules/'.$this->name.'/assets/js/jquery.isotope.min.js', ['position'=>'bottom','priority'=>150]);
        	$this->context->controller->registerJavascript($this->name, 'modules/'.$this->name.'/assets/js/scripts.js', ['position' => 'bottom', 'priority' => 151]);
		}

	}

	public function getData($params) {

		$rootCategory = Category::getRootCategory();
		$idLang = (int)Context::getContext()->language->id;
		$categoriesArray = array('0' => array('id_category' => $rootCategory->id_category));
		$bestsellersList = array();
		$nb = (int)(Configuration::get('ISOTOPE_NBR'));
		$max = (int)(Configuration::get('ISOTOPE_MAX'));
		$col = (int)(Configuration::get('ISOTOPE_COL'));
		$meth = Configuration::get('ISOTOPE_ADD_METHOD');
		$categories = "";

		$spe = (int)(Configuration::get('ISOTOPE_SPE'));
		$fea = (int)(Configuration::get('ISOTOPE_FEA'));
		$bes = (int)(Configuration::get('ISOTOPE_BES'));
		$new = (int)(Configuration::get('ISOTOPE_NEW'));
		$cat = Configuration::get('ISOTOPE_CAT');


		if ($meth == 0) {// manual

			$sql = 'SELECT data FROM `'.$this->DBtable.'`';			
			$data = Db::getInstance()->executeS($sql);		
			foreach ($data as $k => $value)
				$listID[$k] = $value["data"];

			$uniqueListID = array_unique($listID);

			$usetax = (Product::getTaxCalculationMethod((int)$this->context->customer->id) != PS_TAX_EXC);
			
			foreach ($uniqueListID as $k => $productID) {

				$product = new Product($productID, true, $idLang);
				$prdcts["unsorted"][$k] = get_object_vars($product);
				$prdcts["unsorted"][$k]["id_product"] = $productID;
			
			}
			$products["unsorted"] = $this->prepareBlocksProducts($prdcts["unsorted"], $params['hook']);
			
		} else { // automatically

			$category = new Category(Context::getContext()->shop->getCategory(), $idLang);

			$orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
			$orderWay = Tools::getProductsOrder('way', Tools::getValue('orderway'));

			$home_id = $category->getRootCategory();
			$categories_id = explode(",", $cat);
			$pids = array();
			$pids_str = null;

			foreach ($categories_id as $key => $id) {

				if (!empty($id)) {

					if (!empty($pids)) // get numbers of existing products
						foreach ($pids as $k => $v)
							$pids_str .= implode(",",$v);

					$prdcts = Product::getProducts($idLang, 0, ($nb ? $nb : 10), $orderBy, $orderWay, $id, true);
					$products[$id] = $this->prepareBlocksProducts($prdcts, $params['hook']);
					if ($prdcts) {
						foreach ($prdcts as $k => $prdct) {
							$pids[$id][$prdct["id_product"]] = $prdct["id_product"];
						}
					}

					$sql = 'SELECT link_rewrite, name FROM `'._DB_PREFIX_.'category_lang` WHERE id_category='.$id.' AND id_shop='.Context::getContext()->shop->id.' AND id_lang='.Context::getContext()->language->id;		
					$catInfo = Db::getInstance()->executeS($sql);	
					$categories[$id] = $catInfo[0];

				}

			}
				
			if ($fea) {

				$p_list = $category->getProducts($idLang, 1, ($nb ? $nb : 10), $orderBy, $orderWay);			

				if (!empty($p_list))
					foreach ($p_list as $key => $value)
						$pids["featured"][$value["id_product"]] = $value["id_product"];

				$products["featured"] = $this->prepareBlocksProducts($p_list, $params['hook']);
				
			}
			
			if ($new) {

				$p_list = Product::getNewProducts($idLang, 0, ($nb ? $nb : 10), false, $orderBy, $orderWay);

				if (!empty($p_list))
					foreach ($p_list as $key => $value)
						$pids["new"][$value["id_product"]] = $value["id_product"];

				$products["new"] = $this->prepareBlocksProducts($p_list, $params['hook']);
			}
			
			if ($spe) {

				$p_list = Product::getPricesDrop($idLang, 0, ($nb ? $nb : 10), false, $orderBy, $orderWay);

				if (!empty($p_list))
					foreach ($p_list as $key => $value)
						$pids["special"][$value["id_product"]] = $value["id_product"];

				$products["new"] = $this->prepareBlocksProducts($p_list, $params['hook']);

			}

			if ($bes) { 
				$products["bestsellers"] = $this->getBestSellers(($nb ? $nb : 10));
				if (!empty($products["bestsellers"]))
					foreach ($products["bestsellers"] as $key => $value)
						$products["bestsellers"][$key]['bestseller'] = 1;
			}


		}	
		
		if (!empty($products)) {
			foreach ($products as $key=>$product)
				if (!empty($product))
					foreach ($product as $k => $val)
						$p[$val["id_product"]] = $val;
		} else 
			$p[0] = "";

		// sort products by types
		if (!empty($p))	{	
			foreach ($p as $k=>$data) {
				if (!empty($data)) {		
					$productReadyList[$data["id_product"]] = $data;
				} else
					$productReadyList[$k] = "";
			} 
			//shuffle($productReadyList);		
		} else {
			$productReadyList = "";
		}
		
		$this->smarty->assign(array(
			'products' => $productReadyList,
			'moduleName' => $this->name,
			'spe' => $spe,
			'fea' => $fea,
			'new' => $new,
			'bes' => $bes,
			'categories' => $categories,
			'isotope_max' => $max,
			'isotope_col' => $col
		));

	}

	private function getBestSellers($nb)
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

        foreach ($products_for_template as $key => $value) {
        	if ($value['manufacturer_name'] == '') {
                $products_for_template[$key]['manufacturer_name'] = Manufacturer::getNameById($value['id_manufacturer']);
            }
        }

        return $products_for_template;
    }


	public function hookdisplayHome($params) {

        $params['hook'] = 'displayHome';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getData($params);
			//return $this->fetch($this->templateFile, $this->getCacheId($this->name));
			return $this->fetch($this->templateFile);
        }

    }   

    public function hookcontent_top($params) {

        $params['hook'] = 'content_top';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getData($params);
			return $this->fetch($this->templateFile);
        }

    }

    public function hookcontent_bottom($params) {

        $params['hook'] = 'content_bottom';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getData($params);
			return $this->fetch($this->templateFile);
        }

    }

	private function recurseCategory($categories, $current, $id_category = 1, $id_selected = 1) {

		global $currentIndex;

		$this->_html .= '<option value="' . $id_category . '"' . (($id_selected == $id_category) ? ' selected="selected"' : '') . '>' . str_repeat('&nbsp;', $current ['infos'] ['level_depth'] * 5) . (_PS_VERSION_ < 1.4 ? self::hideCategoryPosition(stripslashes($current ['infos'] ['name'])) : stripslashes($current ['infos'] ['name'])) . '</option>';
		if (isset($categories [$id_category]))
			foreach ( $categories [$id_category] as $key => $row )
				$this->recurseCategory($categories, $categories [$id_category] [$key], $key, $id_selected);
	}
	private function displayCategoriesSelect($categories, $selected) {
		$this->_html = '';
		$this->_html .= '<select multiple name="id_category" class="categList">';
						$this->recurseCategory($categories, $categories [0] [1], 1, $selected);
		$this->_html .= '</select>';
		return $this->_html;
	}

	private function htmlCodeCategories($prod_id) {

		$orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
		$langID = (int)Context::getContext()->language->id;
		$html = '';

		$products = Product::getProducts($langID, 0, 0, $orderBy, "ASC", $prod_id, true);

		foreach ($products as $key => $data)
			$html .= $this->htmlCodeProducts($data["id_product"], "");

		return $html;
		
	}
	private function htmlCodeProducts($prod_id, $front) {

		$cover = Image::getCover($prod_id);		
		$img = $prod_id.'-'.(int)$cover["id_image"];
		$productName = Product::getProductName($prod_id);

		$sql = 'SELECT link_rewrite, description_short FROM `'._DB_PREFIX_.'product_lang` WHERE id_product='.$prod_id.' AND id_shop='.Context::getContext()->shop->id.' AND id_lang='.Context::getContext()->language->id;		

		$prodData = Db::getInstance()->executeS($sql);		
		
		$html = '';
		if ($front == "front") {			
		} else {			
			$html = "<div data-pid=\"".$prod_id."\" class=\"prodSection\" title=\"".$productName."\"><img src=\"".$this->context->link->getImageLink($prodData[0]["link_rewrite"], $img, 'small_default')."\" alt=\"\" /><span>".substr($productName, 0, 8)."...</span></div>";			
		}

		return $html;

	}
	public function saveData($pID) {
			
		Db::getInstance()->Execute('INSERT INTO `'.$this->DBtable.'` (`data`) VALUES ('.$pID.');');

	}

	public function removeData($rem_pID) {

		Db::getInstance()->Execute('DELETE FROM `'.$this->DBtable.'` WHERE data = '.$rem_pID);	

	}

	public function getProductsFromCategory($cID) {

		$ids = explode(",", $cID);	
		$html = '';

		foreach ($ids as $id)
			$html .= $this->htmlCodeCategories($id);

		print_r($html);
	}

	public function getCatList($cids, $raw = false) {

		$ids = explode(",", $cids);

		$html = "";
		$cats = Category::getCategoryInformations($ids);
		foreach ($cats as $key => $cat)
			$html .= "<li data-id='".$cat['id_category']."'>".$cat['name']."</li>";

		if ($raw == false)
			print_r($html);
		else
			return $html;

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

}
