<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

include_once dirname(__FILE__) . '/../ParentAllegroController.php';
include_once dirname(__FILE__) . '/../../allegro.inc.php';

class AdminAllegroProductController extends ParentAllegroController
{
    /**
     * Controller constructor used for products list generation
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'allegro_product';
        $this->primary = 'id_allegro_product';
        $this->className = 'AllegroProduct';
        $this->explicitSelect = true; // HAVING on filter
        $this->allow_export = true;
        $this->bulk_actions = array(
            'enable_stock_sync' => array(
                'text' => $this->l('Enable stock sync'),
                'icon' => 'icon-check',
                'confirm' => $this->l('Enable stock sync?')
            ),
            'disable_stock_sync' => array(
                'text' => $this->l('Disable stock sync'),
                'icon' => 'icon-remove',
                'confirm' => $this->l('Disable stock sync?')
            ),
            'enable_relist' => array(
                'text' => $this->l('Enable relist'),
                'icon' => 'icon-check',
                'confirm' => $this->l('Enable relist?')
            ),
            'disable_relist' => array(
                'text' => $this->l('Disable relist'),
                'icon' => 'icon-remove',
                'confirm' => $this->l('Disable relist?')
            ),
        );

        // Return to right page
        if(isset($_GET['page']) && !isset($_GET['update'.$this->table])) {
            $_POST['submitFilter'.$this->table] = (int)$_GET['page'];
        }

        // PS 1.5
        $this->addRowAction('newoffer');

        // Call "ParentAllegroController" contructor
        parent::__construct();
        parent::initApi();

        // Detect PS 15 version in templates
        $this->tpl_list_vars['is_15'] = !version_compare(_PS_VERSION_, '1.6.0.0', '>=');

        // Multistore infos
        if (empty($this->moduleErrors) && Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->moduleErrors[] = $this->l('In "multistore" mode you need to be in context of single shop (select shop above).');
        }

        // All errors until this point are "critical" co we want to display only errors
        if (empty($this->moduleErrors)) {

            $this->addRowAction('edit');
            $this->addRowAction('create_auction');

            $idShop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? (int)$this->context->shop->id : 1;
            /**
             * id_allegro_product, id_allegro_account, id_shop are needed for indexing
             * If they are empty it mean that we need add product to index
             */
            $this->_join .= '
                RIGHT JOIN `'._DB_PREFIX_.'product` p ON a.`id_product` = p.`id_product` AND a.`id_product_attribute` = 0
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'allegro_product_shop` aps ON (aps.`id_allegro_product` = a.`id_allegro_product` AND aps.id_shop = '.(int)$idShop.')
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.$this->context->language->id.' AND pl.id_shop = '.(int)$idShop.')
                LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND pl.`id_lang` = cl.`id_lang` AND cl.id_shop = '.(int)$idShop.')
                LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0)
                '.StockAvailable::addSqlShopRestriction(null, null, 'sav').'
                LEFT JOIN `'._DB_PREFIX_.'allegro_product_account` apa ON
                    apa.`id_allegro_product` = a.`id_allegro_product` AND
                    apa.`id_allegro_account` = '.(int)$this->api->getAccountId().'
                    AND apa.id_shop = '.(int)$idShop.'
                LEFT JOIN `'._DB_PREFIX_.'allegro_auction` aa ON (
                    aa.`id_allegro_product` = a.`id_allegro_product` 
                    AND aa.`status` < 3
                    AND aa.`id_allegro_account` = '.(int)$this->api->getAccountId().')';


            if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
                $this->_join .= '
                LEFT JOIN `'._DB_PREFIX_.'image_shop` i ON (i.`id_product` = p.`id_product` AND  i.`cover` = 1 AND i.id_shop = '.(int)$idShop.')';
            } else {
                $this->_join .= '
                LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND  i.`cover` = 1)';
            }

            $this->_select .= 'p.`id_product`, p.`reference`, p.`price`, 0 AS `price_final`, p.`active`,
                sav.`quantity` AS `sav_quantity`, 
                i.`id_image`, 
                0 AS id_product_attribute, 
                cl.`name` AS `name_category`,
                aps.*, IFNULL(aps.`stock_sync`, 0) AS stock_sync,
                COUNT(DISTINCT aa.`id_allegro_auction`) AS nb_auction,
                aps.id_shop,
                apa.id_allegro_account,
                a.id_allegro_product';

            $this->_group = 'GROUP BY p.`id_product`, a.`id_allegro_product`';
            $this->_defaultOrderBy = 'id_product';

            $this->fields_list = array();
            $this->fields_list['id_product'] = array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'type' => 'int',
                'filter_key' => 'p!id_product',
            );

            if(Configuration::get('ALLEGRO_DEV_MODE'))
                $this->fields_list['id_allegro_product'] = array(
                    'title' => $this->l('ID allegro'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'type' => 'int',
                );

            if(Configuration::get('ALLEGRO_SHOW_IMAGE'))
                $this->fields_list['image'] = array(
                    'title' => $this->l('Image'),
                    'align' => 'center',
                    'image' => 'p',
                    'image_id' => 'id_product', // Bug in PS 1.6.0.x fixed in .tpl
                    'orderby' => false,
                    'filter' => false,
                    'search' => false
                );
            $this->fields_list['name'] = array(
                'title' => $this->l('Name'),
                'filter_key' => 'pl!name'
            );
            if(Configuration::get('ALLEGRO_SHOW_REFERENCE'))
                $this->fields_list['reference'] = array(
                    'title' => $this->l('Reference'),
                    'align' => 'left',
                );

            $this->fields_list['name_category'] = array(
                'title' => $this->l('Category'),
                'filter_key' => 'cl!name',
            );
            if(Configuration::get('ALLEGRO_SHOW_BASE_PRICE'))
                $this->fields_list['price'] = array(
                    'title' => $this->l('Base price'),
                    'type' => 'price',
                    'align' => 'text-right',
                    'filter_key' => 'p!price'
                );

            $marge = (float)Configuration::get('ALLEGRO_PRICE_PC');
            $this->fields_list['price_final'] = array(
                'title' => $this->l('Price').($marge ? ' ('.$marge.'%)' : ''),
                'type' => 'price',
                'align' => 'text-right',
                'havingFilter' => true,
                'orderby' => false,
                'search' => false
            );

            if (Configuration::get('PS_STOCK_MANAGEMENT')) {
                $this->fields_list['sav_quantity'] = array(
                    'title' => $this->l('Quantity'),
                    'type' => 'int',
                    'align' => 'text-right',
                    'filter_key' => 'sav!quantity',
                    'orderby' => true,
                    'badge_danger' => true,
                );

                $this->fields_list['relist'] = array(
                    'title' => $this->l('Relist'),
                    'align' => 'text-center',
                    'type' => 'select',
                    'list' => 
                        version_compare(_PS_VERSION_, '1.5.6.2', '>')
                        ? array(1 => $this->l('Yes'), 0 => $this->l('No'))
                        : array(),                    
                    'filter_key' => 'apa!relist',
                    'filter_type' => 'int',
                    'callback' => 'printRelist',
                    'class' => 'fixed-width-sm',
                    'remove_onclick' => true,
                );

                $this->fields_list['stock_sync'] = array(
                    'title' => $this->l('Stock sync'),
                    'align' => 'text-center',
                    'type' => 'select',
                    'list' => 
                        version_compare(_PS_VERSION_, '1.5.6.2', '>')
                        ? array(1 => $this->l('Yes'), 0 => $this->l('No'))
                        : array(),                    
                    'filter_key' => 'aps!stock_sync',
                    'filter_type' => 'int',
                    'callback' => 'printSS',
                    'class' => 'fixed-width-sm',
                    'remove_onclick' => true,
                );
            }

            $this->fields_list['nb_auction'] = array(
                'title' => $this->l('Auctions'),
                'align' => 'text-center',
                'filter_type' => 'int',
                'class' => 'fixed-width-sm',
                'havingFilter' => true, // <--
            );

            if(Configuration::get('ALLEGRO_SHOW_STATUS'))
                $this->fields_list['is_active'] = array(
                    'title' => $this->l('Status'),
                    'align' => 'text-center',
                    'type' => 'select',
                    'list' => 
                        version_compare(_PS_VERSION_, '1.5.6.2', '>')
                        ? array(1 => $this->l('Yes'), 0 => $this->l('No'))
                        : array(),                    
                    'filter_key' => 'p!active',
                    'filter_type' => 'int',
                    'callback' => 'printActiveIcon',
                    'class' => 'fixed-width-sm',
                    'orderby' => false
                );
        }
    }


    // PS 1.5
    public function displayNewofferLink($token = null, $id, $name = null)
    {
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
            return '
                <a href="'.self::$currentIndex.'&token='.$this->token.'&id_allegro_product='.(int)$id.'&submitCreateAuction" class="default" title="'.$this->l('New offer').'">
                    <img src="../modules/allegro/AdminAllegro.gif" alt="'.$this->l('New offer').'" />
                </a>';
        }
    }


    /**
     * Method used for create product row "Simulation" link
     *
     * @param  string   $token  Secure token
     * @param  array    $tr     Array containing single product row
     * @return string   HTML
     */
    public function displaySimulationLink($token, $tr)
    {
        return '
        <li>
            <a href="'.self::$currentIndex.'&token='.$this->token.(Tools::getValue('page') 
                ? '&page='.(int)Tools::getValue('page') 
                : '')
                .'&'.$this->identifier.'='.$token
                .'&simulation'.$this->table.'=1" 
                title="'.$this->l('Simulation').'">
                <i class="icon-gavel"></i> '.$this->l('Simulation').'
            </a>
        </li>';
    }


    /**
     * Method used for create product status icon
     *
     * @param  string   $token  Secure token
     * @param  array    $tr     Array containing single product row
     * @return string   HTML
     */
    public function printActiveIcon($token, $tr)
    {
        return '<i class="icon-'.($tr['active'] 
            ? 'check list-action-enable action-enabled' 
            : 'remove list-action-enable action-disabled').'"></i>';
    }


    /**
     * Method used for create product "Sale manager" checkobox
     *
     * @param  string   $token  Secure token
     * @param  array    $tr     Array containing single product row
     * @return string   HTML
     */
    public function printRelist($token, $tr)
    {
        return '<i data-action="relist" class="icon-check-'.($tr['relist'] 
            ? 'sign' 
            : 'empty').' covered" data-id="'.(int)$tr['id_allegro_product'].'"></i>';
    }

    public function printSS($token, $tr)
    {
        return '<i data-action="stockSync" class="icon-check-'.($tr['stock_sync'] 
            ? 'sign' 
            : 'empty').' covered" data-id="'.(int)$tr['id_allegro_product'].'"></i>';
    }


    /**
     * AdminController::initPageHeaderToolbar() override
     *
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        // Disable "Add" button in table header
        unset($this->toolbar_btn['new']);

        $allegroProduct = new AllegroProduct(Tools::getValue('id_allegro_product'));

        // Add "Preview" and "New auction" buttons
        if ($this->display == 'edit') {
            // Preview
            $this->page_header_toolbar_btn['preview'] = array(
                'short' => $this->l('Preview', null, null, false),
                'href' => $this->getPreviewUrl(new Product($allegroProduct->id_product)),
                'desc' => $this->l('Preview', null, null, false),
                'target' => true,
                'class' => 'previewUrl'
            );
            // New auction
            $this->page_header_toolbar_btn['create_auction'] = array(
                'short' => $this->l('Create auction', null, null, false),
                'href' => self::$currentIndex.'&id_allegro_product='.(int)Tools::getValue('id_allegro_product').
                    '&update'.$this->table.'&submitCreateAuction&xkey_tab='.Tools::safeOutput(Tools::getValue('key_tab')).
                    '&token='.$this->token.
                    (Tools::getValue('page') > 1 ? '&page='.(int)Tools::getValue('page') : ''),
                'desc' => $this->l('Create auction', null, null, false),
                'class' => 'create_auction'
            );
            // Simulation
            $this->page_header_toolbar_btn['simulation'] = array(
                'short' => $this->l('Simulation', null, null, false),
                'href' => self::$currentIndex.'&id_allegro_product='.(int)Tools::getValue('id_allegro_product').
                    '&update'.$this->table.'&submitSimulation&xkey_tab='.Tools::safeOutput(Tools::getValue('key_tab')).
                    '&token='.$this->token.
                    (Tools::getValue('page') > 1 ? '&page='.(int)Tools::getValue('page') : ''),
                'desc' => $this->l('Simulation', null, null, false),
                'class' => 'simulation'
            );
        }
    }


    // PS 1.5
    public function initToolbar()
    {
        switch ($this->display) {
            case 'add':
            case 'edit':
                $this->toolbar_btn['save'] = array(
                    'short' => 'Save',
                    'href' => '#',
                    'desc' => $this->l('Save'),
                );

                $this->toolbar_btn['save-and-stay'] = array(
                    'short' => 'SaveAndStay',
                    'href' => '#',
                    'desc' => $this->l('Save and stay'),
                );

                $this->toolbar_btn['create_auction'] = array(
                    'short' => 'create_auction',
                    'href' => self::$currentIndex.'&id_allegro_product='.(int)Tools::getValue('id_allegro_product').
                        '&update'.$this->table.'&submitCreateAuction&xkey_tab='.Tools::safeOutput(Tools::getValue('key_tab')).
                        '&token='.$this->token.
                        (Tools::getValue('page') > 1 ? '&page='.(int)Tools::getValue('page') : ''),
                    'desc' => $this->l('Create auction'),
                );

                $this->toolbar_btn['simulation'] = array(
                    'short' => 'simulation',
                    'href' => self::$currentIndex.'&id_allegro_product='.(int)Tools::getValue('id_allegro_product').
                        '&update'.$this->table.'&submitSimulation&xkey_tab='.Tools::safeOutput(Tools::getValue('key_tab')).
                        '&token='.$this->token.
                        (Tools::getValue('page') > 1 ? '&page='.(int)Tools::getValue('page') : ''),
                    'desc' => $this->l('Simulation'),
                );
            default:
        }
    }


    /**
     * AdminController::initContent() override
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        if (empty($this->moduleErrors)) {
            if(Tools::getIsset('updateallegro_product')) {
                $this->display = 'edit';
            } else {
                if ($this->api) {
                    $categoriesListVer = Configuration::get('ALLEGRO_CATEGORIES_VER', null, 0, 0);
                    // On sanbox import categorie only if category list is empty for given country
                    if (!$this->api->isSandbox() || !$categoriesListVer) {
                        $verKey = $this->api->doQuerySysStatus(array('sysvar' => 3))->info;
                        if(empty($this->errors) && $categoriesListVer !== $verKey) {
                            $error = $this->l('Categories list needs to be updated,').
                                ' <a href="'.self::$currentIndex.'&token='.$this->token.'&updateCategoriesList">'.$this->l('click here').'</a> '.
                                $this->l('to update.');
                            if ($categoriesListVer) {
                                $this->warnings[] = $error;
                            } else {
                                // No categories - diplay hard error
                                $this->errors[] = $error;
                            }
                        }
                    }

                    // Check for updates
                    $latestVersion = Configuration::get('ALLEGRO_LV');
                    if ((int)Configuration::get('ALLEGRO_LAST_UPDATE_CHECK') < time()-(60*60*24)) {
                        Configuration::updateValue('ALLEGRO_LAST_UPDATE_CHECK', time(), false, 0, 0);
                        $latestVersion = (string)@file_get_contents($this->module->ver_url);
                        if($latestVersion) {
                            Configuration::updateValue('ALLEGRO_LV', $latestVersion, false, 0, 0);
                        }
                    }

                    if(version_compare($this->module->version, $latestVersion, '<')) {
                        $this->warnings[] = sprintf($this->l('There is new version of module %s'), $latestVersion);
                    }
                }
            }

            $this->addJs(__PS_BASE_URI__.'modules/allegro/js/form.js');
        }

        if (!$this->ajax) {
            $this->renderAccountsBar();
        }
        parent::initContent();

    }


    /**
     * AdminController::initToolbarTitle() override
     *
     * @see AdminController::initToolbarTitle()
     */
    public function initToolbarTitle()
    {
        parent::initToolbarTitle();

        // Product name in toolbar title
        if ($this->display == 'edit' && ($allegroProduct = new AllegroProduct(Tools::getValue('id_allegro_product')))) {
                $product = new Product($allegroProduct->id_product, false, $this->context->language->id);
                $attrNames = array();
                if($allegroProduct->id_product_attribute) {
                    $c = new Combination($allegroProduct->id_product_attribute);
                    $attributes = $c->getAttributesName($this->context->language->id);
                    foreach ($attributes as $key => $attribute) {
                         $attrNames[] = $attribute['name'];
                    }
                }
                $this->toolbar_title[] = $this->l('Edit:').' '.$product->name.($attrNames ? ' ('.implode(',', $attrNames).')' : '');
        }
    }


    /**
     * AdminController::display() override
     *
     * @see AdminController::display()
     */
    public function display()
    {
        // Custom error/success mesages
        $this->_conf[101] = $this->l('Auction finished sucefully');
        $this->_conf[102] = $this->l('Categories updated sucefully');
        $this->_conf[103] = $this->l('Simulation ended successfully').(Tools::getValue('cost') ? ' ('.$this->l('cost').' '.Tools::getValue('cost').').' : '');
        if($conf = (int)Tools::getValue('conf')) {
            switch ($conf) {
                // Genrate new auction confirmation message
                case 100:
                    $allegroAuction = AllegroAuction::getByAuctionID((float)Tools::getValue('id_auction'));
                    if ($allegroAuction) {
                        $this->_conf[$conf] = $this->createTemplate('message_create_auction.tpl')->assign(array(
                            'allegro_auction' => $allegroAuction,
                        'auction_url' => AllegroAuction::getAuctionUrl((float)$allegroAuction->id_auction, (strtotime($allegroAuction->date_start) > time()), $this->api->isSandbox()),
                        ))->fetch();
                    }
                    break;
                default:
                    break;
            }
        }

        parent::display();
    }


    /**
     * AdminController::initProcess() override
     *
     * @see AdminController::initProcess()
     */
    public function initProcess()
    {
        if (Tools::isSubmit('submitSave') || Tools::isSubmit('submitSaveAndStay')) {
            //if ($this->tabAccess['edit']) {
                if(Tools::isSubmit('submitSaveAndStay'))
                    $this->display = 'edit';
                $this->action = 'save';
            //}
            //else
                //$this->errors[] = $this->l('You do not have permission to edit this.');
        } elseif (Tools::getIsset('submitCreateAuction') || Tools::getIsset('submitSimulation')) {
            $this->display = 'edit';
            $this->action = 'createAuction';
        } elseif (Tools::isSubmit('submitFinishAuction')) {
            $this->finishAuction();
        } elseif (Tools::getValue('action') == 'delete_thumb') {
            $this->display = 'edit';
            $this->deleteThumb();
        } elseif (Tools::isSubmit('updateCategoriesList')) {
            $this->updateCategoriesList();
        } else
            parent::initProcess();
    }


    public function addCombinationsToList()
    {
        $listWithCombinations = array();
        $productsIds = getArrByKey($this->_list, 'id_product');

        $idLang = $this->context->language->id;
        $idShop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? (int)$this->context->shop->id : 1;

        $sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, pa.`id_product`,
            GROUP_CONCAT(DISTINCT agl.`name`, ":",  al.`name` ORDER BY a.`id_attribute` SEPARATOR " ") AS name,
            GROUP_CONCAT(DISTINCT a.`id_attribute` ORDER BY a.`id_attribute`) AS attributes_ids,
            sav.`quantity` AS sav_quantity, IF(sav.`quantity`<=0, 1, 0) AS `badge_danger`,
            (p.`price` + pa.`price`) AS `price`, 0 AS price_final,
            aps.*, apa.`relist`, IFNULL(aps.`stock_sync`, 0) AS stock_sync,
            COUNT(DISTINCT aa.`id_allegro_auction`) AS nb_auction,
            apa.`id_allegro_account`,
            aps.`id_shop`,
            ap.`id_allegro_product`
        FROM `'._DB_PREFIX_.'product_attribute` pa
            '.Shop::addSqlAssociation('product_attribute', 'pa').'
            JOIN `'._DB_PREFIX_.'product` p  ON (p.`id_product` = pa.`id_product`)
            LEFT JOIN `'._DB_PREFIX_.'allegro_product` ap ON ap.`id_product` = pa.`id_product` AND ap.`id_product_attribute` = pa.`id_product_attribute`
            LEFT JOIN `'._DB_PREFIX_.'allegro_product_shop` aps ON (aps.`id_allegro_product` = ap.`id_allegro_product` AND aps.id_shop = '.(int)$idShop.')
            LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
            LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$idLang.')
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$idLang.')
            LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = pa.`id_product` AND sav.`id_product_attribute` = pa.`id_product_attribute`'.
            StockAvailable::addSqlShopRestriction(null, null, 'sav').')
            LEFT JOIN `'._DB_PREFIX_.'allegro_auction` aa ON (
                aa.`id_allegro_product` = ap.`id_allegro_product` 
                AND aa.`status` < 3
                AND aa.`id_allegro_account` = '.(int)$this->api->getAccountId().')
            LEFT JOIN `'._DB_PREFIX_.'allegro_product_account` apa ON
                apa.`id_allegro_product` = ap.`id_allegro_product` AND
                apa.`id_allegro_account` = '.(int)$this->api->getAccountId().'
                AND apa.id_shop = '.(int)$idShop.'
            WHERE pa.`id_product` IN ('.implode(',', $productsIds).')
            GROUP BY pa.`id_product_attribute`';

        $combinations = Db::getInstance()->executeS($sql);

        // Group combinations by id_product
        $combinationsGrouped = array();
        foreach ($combinations as $combination) {
            $combinationsGrouped[(int)$combination['id_product']][] = $combination;
        }

        foreach ($this->_list as $key => &$item) {
            $cmb = null;
            if(isset($combinationsGrouped[(int)$item['id_product']])) {
                $cmb = $combinationsGrouped[(int)$item['id_product']];
                $item['nb_combinations'] = count($cmb);
            }
            $listWithCombinations[] = $item;
            if($cmb) {
                foreach ($cmb as $key => $citem) {
                    $listWithCombinations[] = array_merge($citem, array('id_image' => 0));
                }

            }
        }

        $this->_list = $listWithCombinations;
    }


    /**
     * AdminController::getList() override
     *
     * @see AdminController::getList()
     */
    public function getList(
        $idLang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $idLang_shop = null)
    {
        parent::getList(
            $idLang,
            $orderBy,
            $orderWay,
            $start,
            $limit,
            $this->context->shop->id
        );

        $context = $this->context->cloneContext();

        if (count($this->_list)) {
            if(Configuration::get('ALLEGRO_SHOW_COMBINATIONS')) {
                $this->addCombinationsToList();
            }
            $this->buildIndex();
        }

        $prevNextList = array();
        foreach ($this->_list as $key => &$item) {
   
            $item['price_final'] =  AllegroProduct::genPriceStatic($item['id_product'], $item['id_product_attribute']);

            // Prev-next
            $prevNextList[] = $item['id_allegro_product'];
        }

        // Save list to cookie for "prev"-"next" buttons or edit page
        $this->context->cookie->{'apl'} = implode(',', $prevNextList);
    }

    public function buildIndex()
    {
        if (count($this->_list)) {
            $reloadList = false;
            foreach ($this->_list as $key => $product) {
                if (empty($product['id_allegro_product'])
                    || empty($product['id_allegro_account'])
                    || empty($product['id_shop'])
                ) {
                    $this->addProductToIndex($product);
                    $reloadList = true;
                }
            }

            // If at least one product was indexed - reload list
            if ($reloadList) {
                self::getList($this->context->language->id);
            }
        }
    }


    /**
     * AdminController::renderForm() override
     *
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        if (!($allegroProduct = new AllegroProduct(Tools::getValue('id_allegro_product')))) {
            return;
        }

        // Multistore infos
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->errors[] = $this->l('In "multistore" mode you need to be in context of single shop (select shop above).');
            return;
        }

        $allegroAccount = new AllegroAccount((int)$this->api->getAccountId());

        $idLang = (int)$this->context->language->id;

        $shopProduct = $allegroProduct->product;

        // Previus / next
        $previousProduct = null;
        $nextProduct = null;
        if($this->context->cookie->apl) {
            $apl = explode(',', $this->context->cookie->apl);
            $page = (int)Tools::getValue('page');
            foreach ($apl as $key => $idAllegroProduct) {
                if($allegroProduct->id == $idAllegroProduct) {
                    if(isset($apl[$key-1]))
                        $previousProduct = self::$currentIndex.'&id_allegro_product='.(int)$apl[$key-1].
                            '&update'.$this->table.
                            '&token='.$this->token.
                            ($page > 1 ? '&page='.(int)$page : '');
                    if(isset($apl[$key+1]))
                        $nextProduct = self::$currentIndex.'&id_allegro_product='.(int)$apl[$key+1].
                            '&update'.$this->table.
                            '&token='.$this->token.
                            ($page > 1 ? '&page='.(int)$page : '');
                }
            }
        }

        // Get category
        $idAllegroCategory = AFField::get(AFField::FID_CATEGORY, array(
            AFField::SCOPE_PRODUCT     => (int)$allegroProduct->id,
            AFField::SCOPE_CATEGORY    => (int)$shopProduct->id_category_default
        ));

        $categoryInScopeProd = AFField::get(AFField::FID_CATEGORY, array(
            AFField::SCOPE_PRODUCT     => (int)$allegroProduct->id
        ));

        // Get form fields & values
        try {
            $response = $this->api->doGetSellFormFieldsForCategory(
                array('categoryId' => ($idAllegroCategory ? (int)$idAllegroCategory : _ALLEGRO_GC_))
            );

            $formFields = $this->formatFormFieldsList($response, $idAllegroCategory);

            $formFieldsIDs = array_keys($formFields);
            $formFieldsValues = AFField::getList($formFieldsIDs, array(
                    AFField::SCOPE_PRODUCT => (int)$allegroProduct->id,
                    AFField::SCOPE_CATEGORY => (int)$shopProduct->id_category_default,
                    AFField::SCOPE_GLOBAL => (int)AFField::SCOPE_GLOBAL_ID,
                ),
                AFField::SCOPE_PRODUCT
            );
        } catch (SoapFault $e) {
            // Categories has changed
            if ($e->faultcode == 'ERR_INCORRECT_CATEGORY_ID') {
                if ($categoryInScopeProd) {
                    // If category set for product erese
                    AFField::clear(AFField::SCOPE_PRODUCT, (int)$allegroProduct->id, AFField::FID_CATEGORY);
                    
                    $this->errors[] = sprintf($this->l('Selected category (%s) was deleted or changed, refresh page and select new category.'), $categoryInScopeProd);
                } else {
                    // Else
                    $this->errors[] = sprintf($this->l('Selected category (%s) was deleted or changed, go to category mapping and update category.'), $idAllegroCategory);
                }

                return;

            } else {
                $this->errors[] = $e->faultstring;
                return;
            }
        }

        // Get nested values for each form field
        $auctionTitle = AFField::get(
            AFField::FID_TITLE,
            array(AFField::SCOPE_PRODUCT => $allegroProduct->id)
        );
        $extDesc = AFField::get(
            AFField::FID_DESC,
            array(AFField::SCOPE_PRODUCT => $allegroProduct->id)
        );

        $category_path = AllegroCategory::getCategoryPath($idAllegroCategory);

        // Images
        $allegroImages = $allegroProduct->getImages();
        $productImages = self::getProductImages(
            $allegroProduct->id_product,
            $allegroProduct->id_product_attribute,
            $this->context->language->id
        );
        $productCover = Image::getCover($shopProduct->id);

        // Auctions
        // @todo
        $auctions = Db::getInstance()->executeS('SELECT *
            FROM `'._DB_PREFIX_.'allegro_auction`
            WHERE `id_allegro_product` = '.(int)$allegroProduct->id.'
            AND `id_allegro_account` = '.(int)$this->api->getAccountId().'
            AND `status` < 3
            ORDER BY `id_allegro_auction`
        ');

        foreach ($auctions as $key => &$auction) {
            $auction['link'] = AllegroAuction::getAuctionUrl(
                $auction['id_auction'],
                (strtotime($auction['date_start']) > time()),
                $this->api->isSandbox()
            );
        }

        // Relist accounts
        $relistAccounts = Db::getInstance()->ExecuteS('
            SELECT * FROM `'._DB_PREFIX_.'allegro_product_account`
            WHERE `id_allegro_product` = '.(int)$allegroProduct->id.'
            AND `relist` = 1
            AND `id_shop` = '.(int)$this->context->shop->id.'
        ');
        $relistAccountsIds = array();
        foreach ($relistAccounts as $key => $value) {
            $relistAccountsIds[] = (int)$value['id_allegro_account'];
        }

        $this->addJs(array(
            _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_.'admin/tinymce.inc.js',
            _PS_JS_DIR_.'tinymce.inc.js',
        ));
        $this->addJqueryUI(array(
            'ui.datepicker'
        ));

        $this->addJs(__PS_BASE_URI__.'modules/allegro/js/jq.nfAllegroEditor.js');
        $this->addCSS(__PS_BASE_URI__.'modules/allegro/css/nfAllegroEditor.css');

        // Warranties
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

            $additionalServices = $this->api->restGet(
                'sale/offer-additional-services/groups',
                array('user.id' => (int)$userData->userId)
            );

        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            return;
        }

        $allegroProductAccount = Db::getInstance()->GetRow('
            SELECT * FROM `'._DB_PREFIX_.'allegro_product_account`
            WHERE `id_allegro_product` = '.(int)$allegroProduct->id.'
            AND `id_allegro_account` = '.(int)$this->api->getAccountId().'
            AND `id_shop` = '.(int)$this->context->shop->id.'
        ');

        return $this->createTemplate('form.tpl')->assign(array(
            // Product
            'allegro_product'   => $allegroProduct,
            'allegro_product_account' => $allegroProductAccount,
            'price'             => $allegroProduct->genPrice(),
            'quantity'          => StockAvailable::getQuantityAvailableByProduct(
                $allegroProduct->id_product,
                $allegroProduct->id_product_attribute),
            'allegro_accounts'  => AllegroAccount::getAccounts($this->context->employee->id),
            'allegro_themes'    => AllegroTheme::getThemes(),
            'shipping_pricings' => AllegroShipping::get(),
            // Auction desc., title and product name
            'title_saved'       => $auctionTitle,
            'default_title'     => $allegroProduct->genTitle(),
            'external_desc'     => $extDesc,
            // Auction fields
            'form_fields'       => $formFields,
            'form_fields_values' => $formFieldsValues,
            'allegro_categories' => AllegroCategory::getCategories(),
            'id_allegro_category' => $idAllegroCategory,
            'product_has_category' => (bool)$categoryInScopeProd,
            'allegro_category_path' => implode(' &gt; ', $category_path),
            // Images
            'product_images'    => $productImages,
            'allegro_images'    => $allegroImages,
            'allegro_img_url'   => _PS_BASE_URL_.__PS_BASE_URI__.'modules/allegro/img/product/',
            'images_excl'       => explode(',', $allegroProduct->images_excl),
            'cover_default'     => $productCover['id_image'],
            // Auctions
            'auctions'          => $auctions,
            // Accounts
            'accounts'          => AllegroAccount::getAccounts(null, true, true, 1/*@todel*/),
            'relist_accounts_ids' => $relistAccountsIds,
            // Prev / next
            'previousProduct'   => $previousProduct,
            'nextProduct'       => $nextProduct,
            // TinyMCE
            'ad'                => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
            'iso_tiny_mce'      => $this->context->language->iso_code,
            'is_15'             => !version_compare(_PS_VERSION_, '1.6.0.0', '>='),

            'toolbar_btn'       => $this->toolbar_btn,
            'image_type'        => AllegroTools::getProductImageTypeName(100),

            // Warranties
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
            'additional_services' => (isset($additionalServices->additionalServicesGroups) 
                ? $additionalServices->additionalServicesGroups 
                : null
            ),
            'allegro_login' => $userData->userLogin,

            'PS_STOCK_MANAGEMENT' => Configuration::get('PS_STOCK_MANAGEMENT'),
            'ALLEGRO_PRICE_PC' => (float)Configuration::get('ALLEGRO_PRICE_PC'),

            'key_tab' => Tools::getValue('key_tab'),
        ))->fetch();
    }

    public function getProductImages($idProduct, $idProductAttribute = null, $idLang)
    {
        return Db::getInstance()->executeS('
            SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
            FROM `'._DB_PREFIX_.'image` i
            '.($idProductAttribute && !Configuration::get('ALLEGRO_SHARE_IMAGES') 
                ? 'JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (pai.`id_image` = i.`id_image` AND pai.`id_product_attribute` = '.(int)$idProductAttribute.')' 
                : '').'
            '.Shop::addSqlAssociation('image', 'i').'
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$idLang.')
            WHERE i.`id_product` = '.(int)$idProduct.'
            ORDER BY `position`'
        );
    }


    /**
     * Save allegro product and allegro form fields to DB
     */
    public function processSave()
    {
        if (!($allegroProduct = new AllegroProduct(
            Tools::getValue('id_allegro_product'),
            null,
            $this->context->shop->id)))
            return;

        $shopProduct = new Product((int)$allegroProduct->id_product);

        /* Auction fields */
        AFField::addList(
            AFField::SCOPE_PRODUCT,
            (int)$allegroProduct->id,
            Tools::getValue('field')
        );

        /* Images */
        if (!empty($_FILES['images']['tmp_name'][0])) {

            $path = _ALLEGRO_IMG_DIR_;

            if(!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $image) {

                $tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

                $allegroImage = new AllegroImage();
                $allegroImage->id_allegro_product = $allegroProduct->id;

                if($allegroImage->add() && move_uploaded_file($image, $tmpName)) {
                    // Check file size and memory before save
                    if (ImageManager::checkImageMemoryLimit($tmpName)) {
                        if(ImageManager::resize($tmpName, $path . $allegroImage->id . '.jpg')) {
                            $images_types = ImageType::getImagesTypes('products');
                            foreach ($images_types as $k => $imageType) {
                                ImageManager::resize(
                                    $path . $allegroImage->id . '.jpg',
                                    $path . $allegroImage->id . '-' . stripslashes($imageType['name']) . '.jpg',
                                    (int)$imageType['width'], (int)$imageType['height']
                                );
                            }

                            unlink($tmpName);
                        } else {
                            $this->errors[] = Tools::displayError('An error occurred while uploading the image.');
                            unlink($tmpName);
                            break;
                        }

                    } else
                      $this->errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');

                } else
                    $this->errors[] = Tools::displayError('An error occurred while uploading the image.');
            }
        }


        $allegroProduct->id_allegro_theme = (int)Tools::getValue('id_allegro_theme', 0);
        $allegroProduct->id_allegro_shipping = (int)Tools::getValue('id_allegro_shipping', 0);

        $allegroProduct->relist_min_qty = (int)Tools::getValue('relist_min_qty');
        $allegroProduct->stock_sync = (int)Tools::getValue('stock_sync');
        $allegroProduct->price_sync = (int)Tools::getValue('price_sync');

        if(preg_match("/^(allegro|shop):(\d+)$/", Tools::getValue('image_cover'))) {
            $allegroProduct->image_cover = Tools::getValue('image_cover');
        }

        $allegroProduct->images_excl = implode(',', Tools::getValue('images_excl', array()));

        // Relist
        $relistAccounts = Tools::getValue('relistAccountBox', array());
        Db::getInstance()->update('allegro_product_account', array(
            'relist' => 0,
        ),'id_allegro_product = '.(int)$allegroProduct->id.' AND id_shop = '.(int)$this->context->shop->id);

        // Warranties
        Db::getInstance()->insert('allegro_product_account', array(
                'implied_warranty' => Tools::getValue('implied_warranty'),
                'return_policy' => Tools::getValue('return_policy'),
                'warranty' => Tools::getValue('warranty'),
                'additional_services' => Tools::getValue('additional_services'),

                'id_allegro_product' => (int)$allegroProduct->id,
                'id_shop' => (int)$this->context->shop->id,
                'id_allegro_account' => (int)$this->api->getAccountId(),
            ), false, true, (version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? DB::ON_DUPLICATE_KEY : DB::REPLACE)
        );

        // @TODO ORDER

        foreach ($relistAccounts as $idAllegroAccount) {
            Db::getInstance()->update('allegro_product_account', array(
                'relist' => 1,
            ), 'id_allegro_product = '.(int)$allegroProduct->id.' 
                AND id_shop = '.(int)$this->context->shop->id.'
                AND id_allegro_account = '.(int)$idAllegroAccount);
        }

        if(!$allegroProduct->save()) {
            $this->errors[] = Tools::displayError('An error occurred while updating an object.');
        }

        // Clone catgeory
        if ($cloneIdProduct = Tools::getValue('clone_id_product')) {
            if (!is_numeric($cloneIdProduct) || !$cloneIdProduct) {
                $this->warnings[] = $this->l('Ivalid product ID, enter number ex.: "1234".');
            } else {
                $cloneIdAllegroProduct = AllegroProduct::getIdByPAId($cloneIdProduct);
                if ($cloneIdAllegroProduct) {
                    $cloneProduct = new Product((int)$cloneIdProduct);
                    $cloneFormFieldsValues = AFField::getList(array(), array(
                            AFField::SCOPE_PRODUCT => (int)$cloneIdAllegroProduct,
                            AFField::SCOPE_CATEGORY => (int)$cloneProduct->id_category_default,
                        )
                    );
                }

                if (!isset($cloneFormFieldsValues[AFField::FID_CATEGORY])) {
                    $this->warnings[] = $this->l('The product has no assigned allegro category.');
                } else {
                    /**
                     * Save cloned field for current product
                     * Save only catgory fiels and all category params
                     */
                    foreach ($cloneFormFieldsValues as $fid => $value) {
                        if ($fid == AFField::FID_CATEGORY || $fid > 500) {
                            AFField::add(AFField::SCOPE_PRODUCT, $allegroProduct->id, $fid, $value);
                        }
                    }
                }
            }
        }

        $page = (int)Tools::getValue('page');
        if (empty($this->errors) && empty($this->warnings)) {
            if ($this->display == 'edit') {
                // Save and stay on same form
                $this->confirmations[] = $this->l('Product saved sucefully');
            } else {
                // Default behavior (save and back)
                $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token.($page > 1 ? '&page='.(int)$page : '');
            }
        } else {
            $this->display = 'edit';
        }
    }


    /**
     * Delete allegro product image thumb
     */
    public function deleteThumb()
    {
        $allegroImage = new AllegroImage((int)Tools::getValue('id_allegro_image'));
        $allegroProduct = new AllegroProduct((int)Tools::getValue('id_allegro_product'));

        // If this image is used as cover for product set to default cover
        if($allegroProduct->image_cover === 'allegro:'.$allegroImage->id_allegro_image) {
            $allegroProduct->image_cover = null;
            $allegroProduct->save();
        }

        if ($allegroImage->delete()) {
            $this->redirect_after = self::$currentIndex.'&update'.$this->table.'&id_allegro_product='.
                (int)Tools::getValue('id_allegro_product').'&conf=1&token='.
                $this->token.((int)Tools::getValue('page') > 1 ? '&page='.(int)Tools::getValue('page') : '').
                '&key_tab='.Tools::safeOutput(Tools::getValue('key_tab'));
        }
    }



    /**
     * Finish allegro auction
     *
     * @return mixed
     */
    public function finishAuction()
    {
        parent::finishAuction();

        if (empty($this->errors) && !Tools::getValue('itemId')) {
            $this->confirmations[] = $this->l('Auction finished sucefully');
        }
    }


    /**
     * Update allegro categories list
     *
     * @return mixed
     */
    public function updateCategoriesList()
    {
        // Get categories list
        try {
            $req = $this->api->doGetCatsData();
            $categories = $req->catsList->item;
            $categoriesVer = $req->verStr;
        } catch (SoapFault $e) {
            $this->errors[] =  $e->faultstring;
            return;
        }

        if(isset($categories)) {
            // Delete existing categories
            Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'allegro_category`');

            // Limits
            try {
                Db::getInstance()->Execute('SET @@global.max_allowed_packet = '.(500*1024*1024));
            } catch (Exception $e) {
                // Nope
            }

            $res = true;
            $categories = array_chunk($categories, 2000);
            foreach ($categories as $chunkKey => $categoriesChunk) {
                // Build SQL query
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'allegro_category`
                    (`id_category`, `id_parent`, `name`, `position`, `is_leaf`)
                VALUES';

                foreach ($categoriesChunk as $category) {
                    $sql .= '(
                        '.(int)$category->catId.',
                        '.(int)$category->catParent.',
                        "'.pSql((string)$category->catName).'",
                        '.(int)$category->catPosition.',
                        '.(int)$category->catIsLeaf.'
                    ),';
                }

                // Execute, if success save new version key to config
                $res &= Db::getInstance()->Execute(substr($sql, 0, -1));
            }

            if ($res) {
                Configuration::updateValue('ALLEGRO_CATEGORIES_VER', $categoriesVer, false, 0, 0);
            } else {
                $this->errors[] = $this->l('Unable to update categories list.');
            }

        }

        if(empty($this->errors)) {
            $this->redirect_after = self::$currentIndex.'&conf=102&token='.$this->token;
        }
    }


    public function processCreateAuction()
    {
        $simulation = (bool)Tools::getIsset('submitSimulation');
        $idAllegroProduct = (int)Tools::getValue('id_allegro_product');
        $startTime = Tools::getValue('start_time');

        $startTimestamp = null;
        if ($startTime) {
            if (strtotime($startTime) > time()) {
                $startTimestamp = strtotime($startTime);
            }
        }

        $manager = new AllegroSyncManager($this->api, new AllegroAccount($this->api->getAccountId()));

        try {
            $res = $manager->createAuction($idAllegroProduct, $simulation, $startTimestamp, $this->context->shop->id);
        } catch (Exception $e) {
            if (preg_match("/sections\[(\d)+\]\.items\[(\d)+\]\.content\s/", $e->getMessage())) {
                $this->errors[] = $this->l('Offer description is not valid:').preg_replace(
                    "/sections\[(\d)+\]\.items\[(\d)+\]\.content/", 
                    " ".$this->l('section').": $1, ".$this->l('column').": $2 - ", 
                    $e->getMessage());
            } elseif (isset($e->faultcode) && $e->faultcode === 'ERR_POSTAGE_OPTIONS_VALUES_INCORRECT') {
                 $this->errors[] = $this->l('Postage options invalid.');
            } else {
                $this->errors[] = $e->getMessage();
            }
            return;
        }

        $this->redirect_after = self::$currentIndex.'&token='.$this->token.
            (Tools::getValue('page')  ? '&page='.Tools::getValue('page') : '').
            (Tools::getIsset('updateallegro_product') ? '&updateallegro_product'.
            (Tools::getValue('key_tab') ? '&key_tab='.Tools::safeOutput(Tools::getValue('key_tab')) : '') : '').
            '&id_allegro_product='.(int)Tools::getValue('id_allegro_product');

        if($res instanceof AllegroAuction) {
            $this->redirect_after .= '&conf=100&id_auction='.(float)$res->id_auction;
        } elseif ($simulation) {
            $this->redirect_after .= '&conf=103&cost='.(urlencode(($res->itemPrice ? $res->itemPrice : $this->l('0 zł'))));
        }
    }


    public function ajaxProcessCreateAuction()
    {
        $idAllegroProduct = (int)Tools::getValue('id_allegro_product');
        $manager = new AllegroSyncManager($this->api, new AllegroAccount($this->api->getAccountId()));

        try {
            $res = $manager->createAuction($idAllegroProduct, null, null, $this->context->shop->id);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        } catch (SoapFault $e) {
            $this->errors[] = $e->faultstring;
        }

        if (!empty($this->errors)) {
            $message = $this->errors[0];
        } else {
            $auctionUrl = AllegroAuction::getAuctionUrl((float)$res->id_auction);
            $message = sprintf(
                $this->l('Auction [%s] created sucefully!'), '<a href="'.$auctionUrl.'">'.
                (float)$res->id_auction.'</a>');
        }

        die(Tools::jsonEncode(array(
            'success' => empty($this->errors),
            'msg' => $message,
            'id' => $idAllegroProduct
        )));
    }


    public function ajaxProcessToggleRelist()
    {
        $idAllegroProduct = (int)Tools::getValue('id_allegro_product');

        // Relist accounts
        $relist = (int)Db::getInstance()->GetValue('
            SELECT `relist` FROM `'._DB_PREFIX_.'allegro_product_account`
            WHERE `id_allegro_product` = '.(int)$idAllegroProduct.'
            AND `id_allegro_account` = '.(int)$this->api->getAccountId().'
            AND `id_shop` = '.(int)$this->context->shop->id
        );

        // Update
        Db::getInstance()->insert('allegro_product_account', array(
                'id_allegro_product' => (int)$idAllegroProduct,
                'id_shop' => (int)$this->context->shop->id,
                'id_allegro_account' => (int)$this->api->getAccountId(),
                'relist' => ($relist ? 0 : 1)
            ), false, true, (version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? DB::ON_DUPLICATE_KEY : DB::REPLACE)
        );
    }


    public function ajaxProcessToggleStockSync()
    {
        $idAllegroProduct = (int)Tools::getValue('id_allegro_product');

        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'allegro_product_shop`
            SET `stock_sync` = IF(`stock_sync`=1, 0, 1)
            WHERE `id_allegro_product` = '.(int)$idAllegroProduct.'
            AND `id_shop` = '.(int)$this->context->shop->id.'
        ');
    }


    public function ajaxProcessThemePreview()
    {
        $allegroProduct = new AllegroProduct((int)Tools::getValue('id_allegro_product'));

        $manager = new AllegroSyncManager($this->api);
        $fieldBuilder = new AllegroFieldBuilder();

        $manager->getProductFieldsList($fieldBuilder, $allegroProduct, $this->context->shop->id, true);

        die($this->createTemplate('../content_preview.tpl')->assign(array(
            'fileds_list' => $fieldBuilder->build(true),
        ))->fetch());
    }


    public function getPreviewUrl(Product $product)
    {
        $idLang = Configuration::get('PS_LANG_DEFAULT', null, null, Context::getContext()->shop->id);

        if (!ShopUrl::getMainShopDomain()) {
            return false;
        }

        $isRewriteActive = (bool)Configuration::get('PS_REWRITING_SETTINGS');
        $previewUrl = $this->context->link->getProductLink(
            $product,
            $this->getFieldValue(
                $product,
                'link_rewrite',
                $this->context->language->id
            ),
            Category::getLinkRewrite(
                $this->getFieldValue($product, 'id_category_default'),
                $this->context->language->id
            ),
            null,
            $idLang,
            (int)Context::getContext()->shop->id,
            0,
            $isRewriteActive
        );

        if (!$product->active) {
            $admin_dir = dirname($_SERVER['PHP_SELF']);
            $admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
            $previewUrl .= ((strpos($previewUrl, '?') === false) ? '?' : '&').
                'adtoken='.$this->token.
                '&ad='.$admin_dir.
                '&id_employee='.(int)$this->context->employee->id;
        }

        return $previewUrl;
    }

    /*
    * Bulk actions
    */
    protected function processBulkEnableStockSync()
    {
        $products = Tools::getValue($this->table.'Box', array());
        if(count($products)) {
            Db::getInstance()->update(
                'allegro_product_shop', 
                array('stock_sync' => 1),
                'id_allegro_product IN ('.implode(',', $products).') AND id_shop = '.(int)$this->context->shop->id);
        }

        $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
    }

    protected function processBulkDisableStockSync()
    {
        $products = Tools::getValue($this->table.'Box', array());
        if(count($products)) {
            Db::getInstance()->update(
                'allegro_product_shop', 
                array('stock_sync' => 0),
                'id_allegro_product IN ('.implode(',', $products).') AND id_shop = '.(int)$this->context->shop->id);
        }

        $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
    }

    protected function processBulkEnableRelist()
    {
        $products = Tools::getValue($this->table.'Box', array());
        if(count($products)) {
            Db::getInstance()->update(
                'allegro_product_account', 
                array('relist' => 1),
                'id_allegro_product IN ('.implode(',', $products).') 
                    AND id_shop = '.(int)$this->context->shop->id.'
                    AND id_allegro_account = '.(int)$this->api->getAccountId());
        }

        $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
    }

    protected function processBulkDisableRelist()
    {
        $products = Tools::getValue($this->table.'Box', array());
        if(count($products)) {
            Db::getInstance()->update(
                'allegro_product_account', 
                array('relist' => 0),
                'id_allegro_product IN ('.implode(',', $products).') 
                    AND id_shop = '.(int)$this->context->shop->id.'
                    AND id_allegro_account = '.(int)$this->api->getAccountId());
        }

        $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
    }
}
