<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class ParentAllegroController extends ModuleAdminController
{
    // For PS 1.5
    public $show_page_header_toolbar;
    public $page_header_toolbar_title;
    public $page_header_toolbar_btn;


    /**
     * Allegro API instance
     * @var AllegroAPI
     */
    protected $api = null;

    protected $moduleErrors = array();
    protected $licenseErrors = array();


    /**
     * AdminController::__construct() override
     *
     * @see AdminController::__construct()
     */
    public function __construct()
    {
        parent::__construct();

        // Check requirements
        if (!extension_loaded("IonCube Loader")) {
            die($this->l('For proper operation of the module requires "IonCube Loader" PHP extension, in most cases you can enable it in the server admin panel.'));
        } elseif (!extension_loaded('soap')) {
            die($this->l('For proper operation of the module requires "SOAP" PHP extension, in most cases you can enable it in the server admin panel.'));
        } elseif (!function_exists('curl_version')) {
            die($this->l('For proper operation of the module requires "cURL" PHP extension.'));
        }
    }


    public function initApi()
    {
        // Get selected account or first available for current employee
        $account = AllegroAccount::getOne(
            Tools::getValue('id_allegro_account', $this->context->cookie->id_allegro_account),
            $this->context->employee->id
        );
        if (!$account) {
            $this->moduleErrors[] = $this->l('No available allegro accounts for current employee, to use module you need add an allegro account in "Accounts" tab.');
        } else {
            try {
                // Create API instance
                $this->api = new AllegroAPI($account);

                $licenseExpDate = APLicenseApi::checkKey($this->api->getLogin(), true);
                if (!$licenseExpDate || is_numeric($licenseExpDate)) {
                    $this->moduleErrors[] = sprintf($this->l('The license for account "%s" has expired or has not been activated yet. For more information please contact us.'), $account->login);
                } elseif ($licenseExpDate && (strtotime($licenseExpDate)-(60*60*24*14)) < time()) {
                    // Expiring licencse warning (14 days before expire)
                    $this->licenseErrors[] = sprintf($this->l('Your license for account "%s" will expire soon (%s).'), $account->login, $licenseExpDate);
                }
            } catch (Exception $e) {
                if ($e->getCode() == 'WSDL') {
                    $this->moduleErrors[] = $this->l('API connection failed, please try again later.');
                } elseif (strpos($e->getMessage(), 'Unable to refresh token') !== false) {
                    $this->moduleErrors[] = $this->l('Unable to refresh token, go to "Accounts" tab and refresh token manually for given account.');
                } else {
                    $this->moduleErrors[] = $e->getMessage();
                }
            }

            // Store account selection to cookie
            $this->context->cookie->id_allegro_account = $account->id_allegro_account;
            unset($account);
        }
    }


    /**
     * AdminController::initContent() override
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        // If any API error display only error : normal display
        if (!empty($this->moduleErrors)) {
            $this->getLanguages();
            $this->initToolbar();
            // PS 1.5
            if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
                $this->initTabModuleList();
                $this->initPageHeaderToolbar();
            }

            $this->context->smarty->assign(array(
                'maintenance_mode' => !(bool)Configuration::get('PS_SHOP_ENABLE'),
                'content' => $this->content,
                'lite_display' => $this->lite_display,
                'url_post' => self::$currentIndex.'&token='.$this->token,
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'title' => $this->page_header_toolbar_title,
                'toolbar_btn' => $this->page_header_toolbar_btn,
                'page_header_toolbar_btn' => '' // No buttons
            ));

            $this->errors = $this->moduleErrors;
            return;
        } else {
            parent::initContent();
        }
    }


    /*
    * PS 1.7x fix
    */
    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($class === null || $class == 'AdminTab') {
            $class = substr(get_class($this), 0, -10);
        } elseif (strtolower(substr($class, -10)) == 'controller') {
            /* classname has changed, from AdminXXX to AdminXXXController, so we remove 10 characters and we keep same keys */
            $class = substr($class, 0, -10);
        }
        return Translate::getAdminTranslation($string, $class, $addslashes, $htmlentities);
    }


    /**
     * Render allegro acount selction bar
     * if we have more than one account available
     */
    protected function renderAccountsBar()
    {
        if ($this->api) {
            $allegro_accounts = AllegroAccount::getAccounts((int)$this->context->employee->id, 1, true);
            if(count($allegro_accounts)) {
                $this->content .= $this->createTemplate('../accounts_bar'.(version_compare(_PS_VERSION_, '1.6.0.0', '>=') ? '' : '_15').'.tpl')->assign(array(
                    'allegro_accounts' => $allegro_accounts,
                    'allegro_account' => new AllegroAccount((int)$this->api->getAccountId()),
                    'new_allegro_account_link' => $this->context->link->getAdminLink('AdminAllegroAccount'),
                    'warning' => !empty($this->licenseErrors[0]) ? $this->licenseErrors[0] : null,
                ))->fetch();
            }
        }
    }


    /**
     * Generate category select
     *
     * @return string
     */
    public function ajaxProcessGetCategorySelect()
    {
        $idParent = (int)Tools::getValue('id_parent');
        $allegroCategories = AllegroCategory::getCategories($idParent);

        $selectTpl = $this->createTemplate('../form/category_select.tpl');
        $selectTpl->assign(array(
            'allegro_categories' => $allegroCategories,
            'id_parent' => $idParent
        ));

        // If no subcategories - final category
        if (!count($allegroCategories)) {
            try {
                $response = $this->api->doGetSellFormFieldsForCategory(
                    array('categoryId' => (int)$idParent)
                );

                $formFields = $this->formatFormFieldsList(
                    $response, 
                    $idParent,
                    array(), 
                    true, 
                    true
                );

            } catch (SoapFault $e) {
                die(Tools::jsonEncode(array('error' => $e->faultstring)));
            }

            $fieldsTpl = $this->createTemplate('../form/fields.tpl');
            $fieldsTpl->assign(array('form_fields' => $formFields));
        }

        die(Tools::jsonEncode(array(
            'select_html' => $selectTpl->fetch(),
            'features_html' => (isset($formFields) ? $fieldsTpl->fetch() : ''))
        ));
    }


    public function finishAuction()
    {
        // "itemId" for "My auctions" tab
        $idAuction = (float)Tools::getValue('itemId', Tools::getValue('id_auction'));

        $manager = new AllegroSyncManager($this->api);

        try {
            $manager->finishAuction($idAuction);
        } catch (SoapFault $e) {
            $this->errors[] = $e->faultstring;
        }
    }


    public function formatFormFieldsList(
        $response,
        $categoryId = null, 
        $filedsIds = array(), 
        $sort = true, 
        $onlyCategoryFields = false

    )
    {
        $onlyGlobal = false;
        if (!$categoryId) {
            $categoryId = (int)_ALLEGRO_GC_;
            $onlyGlobal = true;
        }

        $items = array();


        foreach ($response->sellFormFieldsForCategory->sellFormFieldsList->item as $key => $value) {

            // Skip global fields
            if ($categoryId > 1 && $onlyCategoryFields && (int)$value->sellFormCat === 0) {
                continue;
            }

            // Skip unwanted fields
            if (count($filedsIds) && !in_array((string)$value->sellFormId, $filedsIds)) {
                continue;
            }

            // Skip category fields
            if ($onlyGlobal && (int)$value->sellFormCat) {
                continue;
            }

            $options = array();
            if ($value->sellFormDesc) {

                $names = explode('|', (string)$value->sellFormDesc);
                $values = explode('|', (string)$value->sellFormOptsValues);

                foreach ($names as $key => $name) {
                    // Remove "|-|" empty deprecased options
                    if ($name !== '-') {
                        $options[(int)$values[$key]] = $name;
                    }
                }

                asort($options);
            }

            $items[(int)$value->sellFormId] = array(
                'id_field'      => (int)$value->sellFormId,
                'title'         => (string)$value->sellFormTitle,
                'description'   => null, // Do not use Allegro descrptions because of mess
                'category'      => ($categoryId > 1 ? (int)$value->sellFormCat : false),
                'type'          => (int)$value->sellFormType,
                'type_operator' => (int)$value->sellFormOptions,
                'type_return'   => (int)$value->sellFormResType,
                'required'      => (bool)($value->sellFormOpt == 1),
                'position'      => (int)$value->sellFormPos,
                'options'       => $options,
                'unit'          => (string)$value->sellFormUnit,
            );
        }

        // Sort
        if($sort && count($items)) {
            //uasort($items, 'self::sortAttribs2');
        }

        return $items;
    }

    /**
     * Adds the product to allegro product index
     * 
     * @param array $product 
     * @return int
     */
    protected function addProductToIndex($product)
    {
        // Product
        if (empty($product['id_allegro_product'])) {
            Db::getInstance()->insert(
                'allegro_product', 
                array(
                    'id_product' => (int)$product['id_product'],
                    'id_product_attribute' => (int)$product['id_product_attribute']
                )
            );

            $product['id_allegro_product'] = (int)Db::getInstance()->Insert_ID();
        }

        /**
         *  Insert ignore -product can be deleted from index
         */
        
        // Shop
        if (empty($product['id_shop'])) {
            Db::getInstance()->insert(
                'allegro_product_shop', 
                array(
                    'id_allegro_product' => (int)$product['id_allegro_product'],
                    'id_shop' => (int)$this->context->shop->id
                ), false, true, Db::INSERT_IGNORE
            );
        }

        // Account
        if (empty($product['id_allegro_account'])) {
            Db::getInstance()->insert(
                'allegro_product_account', 
                array(
                    'id_allegro_product' => (int)$product['id_allegro_product'],
                    'id_shop' => (int)$this->context->shop->id,
                    'id_allegro_account' => (int)$this->api->getAccountId(),
                    'relist' => 0
                ), false, true, Db::INSERT_IGNORE
            );
        }

        return $product['id_allegro_product'];
    }
}