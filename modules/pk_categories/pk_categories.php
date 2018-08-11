<?php
/*
*
*  @author Promokit Co. <support@promokit.eu>
*  @copyright  2017 Promokit Co.
*  @version  Release: $Revision: 0 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Promokit Co.
*/

if (!defined('_PS_VERSION_'))
	exit;

class Pk_Categories extends Module {

	function __construct()
	{
		$this->name = 'pk_categories';
		$this->version = '1.0';
		$this->author = 'promokit.eu';
		$this->need_instance = 0;
		$this->bootstrap = 1;
		$this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

		parent::__construct();

		$this->displayName = 'Promokit Categories';
		$this->description = $this->trans('Display Categories List', array(), 'Modules.Categories.Admin');

	}

	public function install() {

		if (!parent::install() OR 
			!$this->registerHook('displayHeader') OR
			!$this->registerHook('content_top') OR
			!$this->registerHook('content_bottom') OR
            !$this->registerHook('displayHome')	OR
            !$this->registerHook('displayBackOfficeHeader')
			) return false;

		return true;

	}


	public function uninstall() {

        return parent::uninstall();

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

		if (Tools::isSubmit('pk_submit')) {

			if (is_array($_POST['category']) && (count($_POST['category']) > 0)) {
				Configuration::updateValue('PK_CL_CATS', serialize($_POST['category']));
			}

		}

		$form = '<form class="defaultForm form-horizontal pk_categories" action="" method="post" enctype="multipart/form-data" novalidate="">
				<input type="hidden" name="submitAddconfiguration" value="1">
					<div class="panel" id="fieldset_0">
						<div class="panel-heading">
							<i class="icon-cogs"></i>&nbsp;Categories
						</div>
						<div class="form-wrapper">
							<ul>'.$this->getCategoriesTree().'</ul>
						</div>
						<div class="panel-footer">
							<button type="submit" value="1" id="pk_submit" name="pk_submit" class="btn btn-default pull-right">
								<i class="process-icon-save"></i> Save
							</button>
						</div>
					</div>
				</form>';

		return $form;
	}

	public function getCategoriesTree() {	

		$category = new Category((int)Configuration::get('PS_HOME_CATEGORY'), $this->context->language->id);
		$categories[0] = $this->getCategories($category);
		$cats = $this->renderCategories($categories);
		return $cats;

	}

	public function renderCategories($categories, $shift = '') {

		$cat_to_show = unserialize(Configuration::get('PK_CL_CATS'));

		$html = "";
		foreach ($categories as $id => $node) {

			$checked = "";
			if (!empty($cat_to_show)) {
				if (in_array($node['id'], $cat_to_show)) {
					$checked = "checked";
				}
			}

			$html .= '<li class="category_'.$node["id"].'">';
			$html .= '<input type="checkbox" name="category['.$node["id"].']" value="'.$node["id"].'" '.$checked.' />'.$node["name"];
			if (count($node['children']) > 0) {
				$html .= '<ul>'.$this->renderCategories($node['children']).'</ul>';
			}
			$html .='</li>';
		}
		return $html;
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
		return $values;

	}

	private function getCategories($category)
    {
        $range = '';
        $maxdepth = Configuration::get('BLOCK_CATEG_MAX_DEPTH');
        if (Validate::isLoadedObject($category)) {
            if ($maxdepth > 0) {
                $maxdepth += $category->level_depth;
            }
            $range = 'AND nleft >= '.(int)$category->nleft.' AND nright <= '.(int)$category->nright;
        }

        $resultIds = array();
        $resultParents = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
			FROM `'._DB_PREFIX_.'category` c
			INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
			INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$this->context->shop->id.')
			WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
			AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
			'.((int)$maxdepth != 0 ? ' AND `level_depth` <= '.(int)$maxdepth : '').'
			'.$range.'
			ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'cs.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC'));
        foreach ($result as &$row) {
            $resultParents[$row['id_parent']][] = &$row;
            $resultIds[$row['id_category']] = &$row;
        }

        return $this->getTree($resultParents, $resultIds, $maxdepth, ($category ? $category->id : null));
    }

    public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
    {
        if (is_null($id_category)) {
            $id_category = $this->context->shop->getCategory();
        }

        $children = [];

        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth)) {
            foreach ($resultParents[$id_category] as $subcat) {
                $children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
            }
        }

        if (isset($resultIds[$id_category])) {
            $link = $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']);
            $name = $resultIds[$id_category]['name'];
            $desc = $resultIds[$id_category]['description'];
        } else {
            $link = $name = $desc = '';
        }

        return [
            'id' => $id_category,
            'link' => $link,
            'name' => $name,
            'desc'=> $desc,
            'children' => $children
        ];
    }

    public function prepare_to_fetch() 
    {
    	$cat_to_show = unserialize(Configuration::get('PK_CL_CATS'));
    	$categories = array();

    	if (!empty($cat_to_show) && is_array($cat_to_show)) {
	    	foreach ($cat_to_show as $key => $id) {
	    		$categories[$id] = new Category((int)$id, $this->context->language->id);
	    		$categories[$id]->image = $this->context->link->getMediaLink(_PS_IMG_.'c/'.$id.'.jpg');
	    		$categories[$id]->link = Context::getContext()->link->getCategoryLink($id, $categories[$id]->link_rewrite);
	    	}
	    }

        $this->smarty->assign(array(
            'categories' => $categories
        ));

    }
	
	public function hookDisplayHeader($params) {

		if ($this->context->controller->php_self == "index") {
			$this->context->controller->addCSS($this->_path.'assets/css/styles.css', 'all');
			$this->context->controller->addJS($this->_path.'assets/js/scripts.js', 'all');
		}

	}

    public function hookdisplayBackOfficeHeader()
    {
    	$this->context->controller->addCSS($this->_path.'assets/css/admin.css', 'all');
    }

    public function hookdisplayHome($params) {

        $params['hook'] = 'displayHome';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->prepare_to_fetch();
			return $this->fetch($this->templateFile);
        }

    }   

    public function hookcontent_top($params) {

        $params['hook'] = 'content_top';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->prepare_to_fetch();
			return $this->fetch($this->templateFile);
        }

    }

    public function hookcontent_bottom($params) {

        $params['hook'] = 'content_bottom';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->prepare_to_fetch();
			return $this->fetch($this->templateFile);
        }

    }

}