<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

include_once dirname(__FILE__) . '/../ParentAllegroController.php';
include_once dirname(__FILE__) . '/../../allegro.inc.php';

class AdminAllegroFieldController extends ParentAllegroController
{
    public $bootstrap = true;

    // Create for PS 1.5
    public $show_page_header_toolbar;
    public $page_header_toolbar_title;

    public function __construct()
    {
        parent::__construct();
        parent::initApi();
    }

    /**
     * AdminController::initContent() override
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        $this->renderAccountsBar();

        if(empty($this->moduleErrors)) {

            $this->initToolbar();
            $this->initPageHeaderToolbar();

            if(Tools::getIsset('addallegro_field') || Tools::getIsset('updateallegro_field')) {
                $this->content .= $this->renderFormCategories();
            } elseif(Tools::getIsset('addallegro_shipping') || Tools::getIsset('updateallegro_shipping')) {
                $this->content .= $this->renderFormShipping();
            } else {
                $this->content .= $this->renderList();
            }

            $this->context->smarty->assign(array(
                'table' => $this->table,
                'current' => self::$currentIndex,
                'token' => $this->token,
                'content' => $this->content,
                'url_post' => self::$currentIndex.'&token='.$this->token,
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn
            ));

            $this->addJs(__PS_BASE_URI__.'modules/allegro/js/form.js');
        } else {
            parent::initContent();
        }

    }


    /**
     * AdminController::initPageHeaderToolbar() override
     *
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        if (!Tools::getIsset('addallegro_field') &&
            !Tools::getIsset('updateallegro_field') &&
            !Tools::getIsset('addallegro_shipping') &&
            !Tools::getIsset('updateallegro_shipping')) {

            $this->page_header_toolbar_btn['new_category_map'] = array(
                'href' => self::$currentIndex.'&addallegro_field&token='.$this->token,
                'desc' => $this->l('Add new category', null, null, false),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['new_shipping_map'] = array(
                'href' => self::$currentIndex.'&addallegro_shipping&token='.$this->token,
                'desc' => $this->l('Add new shipping', null, null, false),
                'icon' => 'process-icon-new'
            );
        } else {
            // Default cancel button - like old back link
            if (!isset($this->no_back) || $this->no_back == false) {
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }

                $this->page_header_toolbar_btn['cancel'] = array(
                    'href' => $back,
                    'desc' => $this->l('Cancel', null, null, false)
                );
            }
        }

        // PS 1.5x
        if (is_callable('parent::initPageHeaderToolbar'))
            parent::initPageHeaderToolbar();
    }

    /**
     * AdminController::renderList() override
     *
     * @see AdminController::renderList()
     */
    public function renderList()
    {
        $this->initListCategories();
        $this->initListShipping();
        $this->initListGlobalFields();
        $this->initListWarranties();
    }


    /**
     * Init Allegro categories list
     */
    public function initListCategories()
    {
        $this->toolbar_title = $this->l('Categories mapping');
        // reset actions and query vars
        $this->actions = array();
        unset($this->fields_list,
            $this->_select,
            $this->_join,
            $this->_group,
            $this->_filterHaving,
            $this->_filter
        );

        $this->table = 'allegro_field';
        $this->list_id = 'allegro_field';
        $this->identifier = 'id';

        $this->_defaultOrderBy = 'id';
        $this->_default_pagination = 10;
        $this->_pagination = array(10, 50, 100, 300, 1000);

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false,
            ),
            'shop_category_name' => array(
                'title' => $this->l('Shop category'),
                'width' => 'auto',
                'orderby' => false,
                'search' => false,
            ),
            'service_category_name' => array(
                'title' => $this->l('Allegro category'),
                'width' => 'auto',
                'orderby' => false,
                'search' => false,
            ),
        );

        $this->bulk_actions = array();

        $this->_select = ' a.`id` AS `id_category`, a.`value`,
        cl.`name` shop_category_name, ac.`name` AS service_category_name';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON (cl.`id_category` = a.`id`
                    AND cl.`id_shop` = ' . (int)$this->context->shop->id . '
                    AND cl.`id_lang` = ' . (int)$this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'allegro_category` ac
                ON (ac.`id_category` = a.`value`)';
        $this->_where = '
            AND a.`scope` = '.AFField::SCOPE_CATEGORY.'
            AND a.`fid` = '.AFField::FID_CATEGORY.'
            AND a.`value` IS NOT NULL';

        $this->_group = 'GROUP BY a.`id`';

        $this->postProcess();
        $this->initToolbar();

        $this->content .= parent::renderList();
    }


    /**
     * Init Allegro shipping pricing lists
     */
    public function initListShipping()
    {
        $this->toolbar_title = $this->l('Shipping pricing');
        // reset actions and query vars
        $this->actions = array();
        unset($this->fields_list,
            $this->_select,
            $this->_join,
            $this->_where,
            $this->_group,
            $this->_filterHaving,
            $this->_filter
        );

        $this->table = 'allegro_shipping';
        $this->list_id = 'allegro_shipping';
        $this->identifier = 'id_allegro_shipping';

        $this->_defaultOrderBy = 'id_allegro_shipping';
        $this->_default_pagination = 10;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_allegro_shipping' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'orderby' => false,
                'search' => false,
            ),
            'default' => array(
                'title' => $this->l('Is default'),
                'orderby' => false,
                'search' => false,
                'activeVisu' => true,
            ),
        );

        $this->bulk_actions = array();

        $this->specificConfirmDelete = $this->l('Delete shipping pricing?');
        $this->_orderBy = '';

        $this->postProcess();
        $this->initToolbar();

        $this->content .= parent::renderList();
    }


    /**
     * Init Allegro global field form
     */
    public function initListGlobalFields()
    {
        try {
            $response = $this->api->doGetSellFormFieldsForCategory(
                array('categoryId' => (int)_ALLEGRO_GC_)
            );

            $formFields = $this->formatFormFieldsList(
                $response, 
                null, 
                array_map('intval', explode(',', _ALLEGRO_FIELDS_GLOBAL_))
            );

            // Get values from DB
            $formFieldsIDs = array_keys($formFields);
            $formFieldsValues = AFField::getList($formFieldsIDs, array(
                    AFField::SCOPE_GLOBAL => (int)AFField::SCOPE_GLOBAL_ID,
                ),
                AFField::SCOPE_GLOBAL
            );
        } catch (SoapFault $e) {
            $this->errors[] = $e->faultstring;
            return;
        }

        $this->content .= $template = $this->createTemplate('global.tpl')->assign(array(
            'form_fields' => $formFields,
            'form_fields_values' => $formFieldsValues,
        ))->fetch();
    }


    public function initListWarranties()
    {
        try {
            // Get user data ("UserID")
            $userData = $this->api->doGetMyData()->userData;

            $impliedWarranties = $this->api->restGet(
                'after-sales-service-conditions/implied-warranties',
                array('sellerId' => (int)$userData->userId)
            );

            $returnPolicies = $this->api->restGet(
                'after-sales-service-conditions/return-policies',
                array('sellerId' => (int)$userData->userId)
            );

            $warranties = $this->api->restGet(
                'after-sales-service-conditions/warranties',
                array('sellerId' => (int)$userData->userId)
            );
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            return;
        }
        
        $this->content .= $template = $this->createTemplate('warranties.tpl')->assign(array(
            'implied_warranties' => (isset($impliedWarranties->impliedWarranties) 
                ? $impliedWarranties->impliedWarranties 
                : null
            ),
            'return_policies' => (isset($returnPolicies->returnPolicies) 
                ? $returnPolicies->returnPolicies 
                : null
            ),
            'warranties' => (isset($warranties->warranties) 
                ? $warranties->warranties 
                : null
            ),
            'ALLEGRO_IMPLIED_WARRANTY' => Configuration::get('ALLEGRO_IMPLIED_WARRANTY_'.(int)$this->api->getAccountId()),
            'ALLEGRO_RETURN_POLICY' => Configuration::get('ALLEGRO_RETURN_POLICY_'.(int)$this->api->getAccountId()),
            'ALLEGRO_WARRANTY' => Configuration::get('ALLEGRO_WARRANTY_'.(int)$this->api->getAccountId()),
            'allegro_login' => $userData->userLogin,
        ))->fetch();
    }


    /**
     * Render Allegro category - shop category mapping form
     */
    public function renderFormCategories()
    {
        $fields = Tools::getValue('field');
        $idCategory = (int)Tools::getValue('id');

        $idAllegroCategory = null;

        $template = $this->createTemplate('category.tpl');
        $this->addJs(__PS_BASE_URI__.'modules/allegro/js/form.js');

        // Category tree
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            $tree = new HelperTreeCategories('categories-tree', $this->l('Choose category'));
            $tree->setInputName('categories')
                    ->setRootCategory((int)Configuration::get('PS_ROOT_CATEGORY'))
                    ->setUseCheckBox(true)
                    ->setUseSearch(true)
                    ->setSelectedCategories(($idCategory ? array((int)$idCategory) : array()));

            if (method_exists('HelperTreeCategories', 'setFullTree')) {
                 $tree->setFullTree(true);
            }
            
            // Hotfix
            try {
                $tree = $tree->render();
            } catch (Exception $e) {
                $tree = '<input type="text" name="categories" /> (hotfix - insert category ID)';
            }
        } else {
            $helper = new Helper();
            $tree = $helper->renderCategoryTree(null, ($idCategory ? array((int)$idCategory) : array()), 'categories', true, false, array(), false, true);
        }

        // Update category
        $categoryPath = null;
        if ($idCategory) {
            // Get allegro category for shop category
            $idAllegroCategory = AFField::get(
                AFField::FID_CATEGORY,
                array(AFField::SCOPE_CATEGORY => $idCategory)
            );
            $categoryPath = AllegroCategory::getCategoryPath($idAllegroCategory);
        }

        try {
            $response = $this->api->doGetSellFormFieldsForCategory(
                array('categoryId' => $idAllegroCategory ? (int)$idAllegroCategory : (int)_ALLEGRO_GC_)
            );

            $formFields = $this->formatFormFieldsList($response, (int)$idAllegroCategory);
            $formFieldsIDs = array_keys($formFields);

            $formFieldsValues = AFField::getList($formFieldsIDs, array(
                    AFField::SCOPE_CATEGORY => (int)$idCategory,
                    AFField::SCOPE_GLOBAL => (int)AFField::SCOPE_GLOBAL_ID,
                ),
                AFField::SCOPE_CATEGORY
            );
        } catch (SoapFault $e) {
            if ($e->faultcode == 'ERR_INCORRECT_CATEGORY_ID') {
                $this->warnings[] = sprintf($this->l('Selected category (%s) was deleted or changed, update category.'), $idAllegroCategory);
                $idCategory = null;
                $idAllegroCategory = null;
                $formFields = array();
                $formFieldsValues = array();
            } else {
                $this->errors[] = $e->faultstring;
                return;
            }
        }

        $formFieldsGlobal = array();
        foreach ($formFields as $fid => $value) {
            if(in_array($fid, explode(',', _ALLEGRO_FIELDS_CATEGORY_))) {
                $formFieldsGlobal[] = $value;
            }
            if (!$value['category']) {
                unset($formFields[$fid]);
            }
        }

        $template->assign(array(
            'id_allegro_category'   => $idAllegroCategory,
            'allegro_categories'    => AllegroCategory::getCategories(),
            'categories_tree'       => $tree,
            'allegro_category_path' => $categoryPath ? implode(' &gt; ', $categoryPath) : null,
            'form_fields'           => $formFields,
            'form_fields_global'    => $formFieldsGlobal,
            'form_fields_values'    => $formFieldsValues
        ));

        return $template->fetch();
    }


    /**
     * Render shipping form
     */
    public function renderFormShipping()
    {
        $id_allegro_shipping = Tools::getValue('id_allegro_shipping');

        $template = $this->createTemplate('shipping.tpl');

        $maxSFid = 300;

        try {
            $response = $this->api->doGetSellFormFieldsForCategory(
                array('categoryId' => (int)_ALLEGRO_GC_)
            );
            $formFields = $this->formatFormFieldsList($response);

            $formFieldsIDs = array_keys($formFields);
            $formFieldsValues = AFField::getList(
                $formFieldsIDs, 
                array(
                    AFField::SCOPE_SHIPPING => (int)$id_allegro_shipping,
                    AFField::SCOPE_GLOBAL => (int)AFField::SCOPE_GLOBAL_ID,
                ),
                AFField::SCOPE_SHIPPING
            );

        } catch (SoapFault $e) {
            $this->errors[] = $e->faultstring;
            return;
        }

        $formFieldsGlobal = array();
        foreach ($formFields as $key => $value) {
            if(in_array($value['id_field'], explode(',', _ALLEGRO_FIELDS_SHIPPING_))) {
                $formFieldsGlobal[] = $value;
            }
            if (!in_array($value['id_field'], range(36, $maxSFid))) {
                unset($formFields[$key]);
            }
        }

        if($id_allegro_shipping) {
            $template->assign(array(
                'allegro_shipping' => Db::getInstance()->GetRow('
                    SELECT * FROM `' . _DB_PREFIX_ . 'allegro_shipping`
                    WHERE `id_allegro_shipping` = ' . (int)$id_allegro_shipping))
            );
        }

        $template->assign(array(
            'form_fields'           => $formFields,
            'form_fields_global'    => $formFieldsGlobal,
            'form_fields_values'    => $formFieldsValues,
            'max_s_fid'             => $maxSFid,
            'flat_form'             => Configuration::get('ALLEGRO_SHIPPING_FORM_FLAT'),
        ));

        return $template->fetch();
    }


    /**
     * AdminController::postProcess() override
     *
     * @see AdminController::postProcess()
     */
    public function postProcess()
    {
        // CATEGORY : save
        if(Tools::isSubmit('submitSaveCategory')) {

            $categories_ids = Tools::getValue('categories');
            $fields = Tools::getValue('field');

            if (!is_array($categories_ids) && $categories_ids) {
                $categories_ids = array((int)$categories_ids);
            }

            if(!$categories_ids)
                $this->errors[] = $this->l('Select store category');
            elseif(!isset($fields['2']) || !$fields['2'])
                $this->errors[] = $this->l('Select service category');
            else {
                foreach ($categories_ids as $id_category) {
                    // Add list of fields to DB
                    AFField::addList(AFField::SCOPE_CATEGORY, $id_category, $fields);
                }
            }

            if (empty($this->errors)) {
                if ($back = Tools::getValue('back')) {
                    $this->redirect_after = $this->context->link->getAdminLink($back);
                } else {
                    $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
                }
            }

        // CATEGORY : delete
        } elseif(Tools::isSubmit('deleteallegro_field')) {
            if (AFField::clear(
                AFField::SCOPE_CATEGORY,
                (int)Tools::getValue('id')
                )
            ) {
                $this->redirect_after = self::$currentIndex.'&conf=2&token='.$this->token;
            } else {
                $this->errors[] = $this->l('Unable to delete category');
            }

        // SHIPPING : save
        } elseif(Tools::isSubmit('submitSaveShipping')) {
            $name = Tools::getValue('name');
            $default = (int)Tools::getValue('default');

            $allegroShipping = new AllegroShipping((int)Tools::getValue('id_allegro_shipping'));
            $allegroShipping->name = Tools::getValue('name');
            $allegroShipping->default = (int)Tools::getValue('default');

            if (!$allegroShipping->name || strlen($allegroShipping->name) > 32) {
                $this->errors[] = $this->l('Name can not be empty or longer than 32 characters.');
            } elseif (!Validate::isGenericName($allegroShipping->name)) {
                $this->errors[] = $this->l('Name is invalid.');
            }

            if (empty($this->errors)) {
                if (!$allegroShipping->save() || 
                    !AFField::addList(
                        AFField::SCOPE_SHIPPING,
                        $allegroShipping->id,
                        Tools::getValue('field'))
                ) {
                    $this->errors[] = $this->l('Unable to save shipping options');
                }
            }

            if(empty($this->errors)) {
                if ($back = Tools::getValue('back')) {
                    $this->redirect_after = $this->context->link->getAdminLink($back);
                } else {
                    $this->redirect_after =
                        self::$currentIndex.'&conf='.
                        (Tools::getValue('id_allegro_shipping') ? 4 : 3).
                        '&token='.$this->token;
                }
            }

        // SHIPPING : delete
        } elseif(Tools::getIsset('deleteallegro_shipping')) {

            $allegroShipping = new AllegroShipping((int)Tools::getValue('id_allegro_shipping'));
            AFField::clear(AFField::SCOPE_SHIPPING, $allegroShipping->id);
            if ($allegroShipping->delete()) {
                $this->redirect_after = self::$currentIndex.'&conf=2&token='.$this->token;
            }

        // GLOBALS : save
        } elseif (Tools::isSubmit('submitSaveGlobal')) {

            if (AFField::addList(
                    AFField::SCOPE_GLOBAL,
                    AFField::SCOPE_GLOBAL_ID,
                    Tools::getValue('field')
                )
            ) {
                $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
            } else {
                $this->errors[] = $this->l('Unable to save global fields');
            }

        // WARRANTIES : save
        } elseif (Tools::isSubmit('submitSaveWarranties')) {
            Configuration::updateValue('ALLEGRO_IMPLIED_WARRANTY_'.(int)$this->api->getAccountId(), Tools::getValue('implied_warranty', ''), false, 0, 0);
            Configuration::updateValue('ALLEGRO_RETURN_POLICY_'.(int)$this->api->getAccountId(), Tools::getValue('return_policy', ''), false, 0, 0);
            Configuration::updateValue('ALLEGRO_WARRANTY_'.(int)$this->api->getAccountId(), Tools::getValue('warranty', ''), false, 0, 0);
        } else {
            parent::postProcess();
        }
    }
}
