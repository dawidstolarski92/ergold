<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

if (preg_match('/(controller=Admin(Allegro|Modules|Module))|(module|allegro)/', $_SERVER['REQUEST_URI'])) {
    include_once dirname(__FILE__).'/allegro.inc.php';
}

class Allegro extends Module
{
    private static $_tabs = array(
        'AdminAllegroProduct'       => array('en' => 'Products',    'pl' => 'Produkty'),
        'AdminAllegroField'         => array('en' => 'Fields',      'pl' => 'Kategorie i parametry'),
        'AdminAllegroTheme'         => array('en' => 'Themes',      'pl' => 'Szablony'),
        'AdminAllegroAccount'       => array('en' => 'Accounts',    'pl' => 'Konta'),
        'AdminAllegroAuction'       => array('en' => 'My auctions', 'pl' => 'Moje aukcje'),
        'AdminAllegroSync'          => array('en' => 'Synchronization', 'pl' => 'Synchronizacja'),
        'AdminAllegroPreferences'   => array('en' => 'Preferences', 'pl' => 'Preferencje'),
    );

    public function __construct()
    {
        $this->name = 'allegro';
        $this->tab = 'administration';
        $this->version = '4.1.1.4'; // 12.12.2017
        $this->author = 'addonsPresta.com';
        $this->ver_url = 'https://addonspresta.com/ver.php?module=allegro4';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Allegro');
        $this->description = $this->l('Integration with Allegro auction site.');
        $this->confirmUninstall = $this->l('Are you sure you want to unistall?');

        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => '1.7.9.9');

        if (Tools::getIsset('ajax')) {
            $this->handleAjaxAction();
        }
    }

    public function install()
    {
        // Save employe for stock moves etc.
        Configuration::updateValue('ALLEGRO_EMPLOYEE', (int)$this->context->employee->id, false, 0, 0);

        $deflang = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages();

        $ret = true;

        $cart = new Cart();
        $cart->id_currency = (int)$this->context->currency->id;
        $cart->id_lang = (int)$this->context->language->id;

        if ($cart->add()) {
            Configuration::updateValue('ALLEGRO_ID_CART', $cart->id, false, 0, 0);
        } else {
            $ret &= false;
        }

        $this->dropTables();

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_account` (
                `id_allegro_account` int(11) AUTO_INCREMENT,
                `name` varchar(32) NOT NULL,
                `login` varchar(32) NOT NULL,
                `sandbox` int(1) NOT NULL,
                `id_currency` int(11) NOT NULL,
                `id_language` int(11) NOT NULL,
                `id_employee` int(11) NOT NULL,
                `access_token` text NOT NULL,
                `refresh_token` text NOT NULL,
                `token_lifetime` datetime NOT NULL,
                `token_date_refresh` datetime NOT NULL,
                `active` int(1) NOT NULL,
                PRIMARY KEY(`id_allegro_account`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_auction` (
              `id_allegro_auction` int(11) NOT NULL AUTO_INCREMENT,
              `id_auction` bigint(20) NOT NULL,
              `id_allegro_product` int(11) NOT NULL,
              `id_allegro_account` int(11) NOT NULL,
              `id_shop` int(11) NOT NULL DEFAULT '1',
              `title` varchar(255) CHARACTER SET utf8 NOT NULL,
              `duration` int(2) NOT NULL,
              `date_start` datetime DEFAULT NULL,
              `is_standard` int(1) NOT NULL,
              `quantity` int(9) NOT NULL,
              `price` decimal(20,6) NOT NULL,
              `cost_info` varchar(255) CHARACTER SET utf8 NOT NULL,
              `status` int(1) NOT NULL,
              `date_add` datetime NOT NULL,
              `date_upd` datetime NOT NULL,
                PRIMARY KEY(`id_allegro_auction`),
                UNIQUE KEY `unique_1` (`id_auction`, `id_allegro_product`, `id_allegro_account`),
                INDEX `id_allegro_product` (`id_allegro_product`),
                INDEX `status` (`status`),
                INDEX `id_allegro_account` (`id_allegro_account`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_category` (
                `id_allegro_category` int(11) AUTO_INCREMENT,
                `id_category` int(11) NOT NULL,
                `id_parent` int(11) NOT NULL,
                `name` varchar(255) CHARACTER SET utf8 NOT NULL,
                `position` int(11) NOT NULL,
                `is_leaf` int(1) NOT NULL,
                PRIMARY KEY(`id_allegro_category`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_field` (
                `scope` int(2) NOT NULL,
                `id` int(12) NOT NULL,
                `fid` int(12) NOT NULL,
                `value` text CHARACTER SET utf8,
                UNIQUE KEY `unique_1` (`scope`, `id`, `fid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_image` (
                `id_allegro_image` int(11) AUTO_INCREMENT,
                `id_allegro_product` int(11) NOT NULL,
                PRIMARY KEY(`id_allegro_image`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_order` (
                `id_allegro_order` int(11) AUTO_INCREMENT,
                `id_order` int(11) NOT NULL,
                `form_id` int(11) NOT NULL,
                `buyer_id` int(11) NOT NULL,
                `buyer_email` varchar(255) NOT NULL,
                `buyer_login` varchar(255) NOT NULL,
                `gd_address` text NOT NULL,
                `gd_info` text NOT NULL,
                `carrier_id` int(11) NOT NULL,
                `carrier_name` varchar(255) NOT NULL,
                `invoice` INT(1),
                PRIMARY KEY(`id_allegro_order`),
                UNIQUE KEY `form_id` (`form_id`, `id_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_product` (
                `id_allegro_product` int(11) AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `id_product_attribute` int(11) NOT NULL,
                `id_allegro_theme` int(11) NOT NULL DEFAULT '0',
                `id_allegro_shipping` int(11) DEFAULT NULL,
                `image_cover` varchar(32) DEFAULT NULL,
                `images_excl` text,
                `relist_min_qty` int(11) NOT NULL DEFAULT '0',
                `stock_sync` int(4) NOT NULL DEFAULT '0',
                `price_sync` int(4) NOT NULL DEFAULT '0',
                `cache_relist_error` VARCHAR(255),
                PRIMARY KEY(`id_allegro_product`),
                UNIQUE KEY `id_product` (`id_product`,`id_product_attribute`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_product_account` (
                `id_allegro_product` int(11) NOT NULL,
                `id_shop` int(11) NOT NULL,
                `id_allegro_account` int(11) NOT NULL,
                `relist` int(1) NOT NULL,
                `implied_warranty` varchar(252) DEFAULT NULL,
                `return_policy` varchar(255) DEFAULT NULL,
                `warranty` varchar(255) DEFAULT NULL,
                `additional_services` VARCHAR(255) NOT NULL,
                UNIQUE KEY `key` (`id_allegro_product`,`id_shop`,`id_allegro_account`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_product_shop` (
                `id_allegro_product` int(11) NOT NULL,
                `id_shop` int(11) NOT NULL,
                `relist_min_qty` int(11) NOT NULL DEFAULT '0',
                `stock_sync` int(1) NOT NULL,
                `price_sync` int(1) NOT NULL,
                `order_sync` int(1) NOT NULL,
                UNIQUE KEY `key` (`id_allegro_product`,`id_shop`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_shipping` (
                `id_allegro_shipping` int(11) AUTO_INCREMENT,
                `name` varchar(32) NOT NULL,
                `default` int(1),
                PRIMARY KEY(`id_allegro_shipping`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        if (!Db::getInstance()->Execute("
            CREATE TABLE `"._DB_PREFIX_."allegro_theme` (
                `id_allegro_theme` int(11) AUTO_INCREMENT,
                `name` varchar(32) NOT NULL,
                `content` text NOT NULL,
                `smarty` int(1) NOT NULL,
                `format` int(1) NOT NULL,
                `active` int(1) NOT NULL,
                `default` int(1),
                PRIMARY KEY(`id_allegro_theme`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"))
                $ret &= false;

        $mainTab = $this->installModuleTab('AdminAllegro', array($deflang => 'Allegro'), 0, false);

        if($mainTab) {
            foreach (self::$_tabs as $class => $tab) {
                $tabNamesArray = array();
                foreach ($tab as $tabIso => $tabName) {
                    foreach ($languages as $language) {
                        if ($language['iso_code'] == $tabIso) {
                            $tabNamesArray[$language['id_lang']] = $tabName;
                        }
                    }
                }

                if (!key_exists($deflang, $tabNamesArray)) {
                    $tabNamesArray[$deflang] = $tab['en'];
                }

                $this->installModuleTab($class, $tabNamesArray, $mainTab);
            }
        } else {
            $ret &= false;
        }

        $imageTypeCover = Db::getInstance()->GetValue('
            SELECT `name` FROM `'._DB_PREFIX_.'image_type`
            WHERE `products` = 1
            AND `width` >= 800
            ORDER BY `width`
        ');

        if (!$imageTypeCover) {
            $imageTypeCover = 'thickbox_default';
        }

        Configuration::updateValue('ALLEGRO_SHOW_IMAGE',        '1', false, 0, 0);
        Configuration::updateValue('ALLEGRO_SHOW_REFERENCE',    '0', false, 0, 0);
        Configuration::updateValue('ALLEGRO_SHOW_BASE_PRICE',   '0', false, 0, 0);
        Configuration::updateValue('ALLEGRO_SHOW_STATUS',       '1', false, 0, 0);
        Configuration::updateValue('ALLEGRO_SHOW_COMBINATIONS', '1', false, 0, 0);
        Configuration::updateValue('ALLEGRO_SHARE_IMAGES',      '1', false, 0, 0);
        Configuration::updateValue('ALLEGRO_NB_IMAGES',         '10', false, 0, 0);
        Configuration::updateValue('ALLEGRO_IMAGE_TYPE',        $imageTypeCover, false, 0, 0);
        Configuration::updateValue('ALLEGRO_CUT_TITLE',         '1', false, 0, 0);
        Configuration::updateValue('ALLEGRO_ORDER_SYNC',        '0', false, 0, 0);
        Configuration::updateValue('ALLEGRO_ORDER_SYNC_TYPE',   '2', false, 0, 0);
        Configuration::updateValue('ALLEGRO_ORDER_ID_SHOP',     (int)$this->context->shop->id, false, 0, 0);
        Configuration::updateValue('ALLEGRO_CUSTOMER_NEWSLETTER', '0', false, 0, 0);

        Configuration::updateValue('ALLEGRO_PAYU_NEW',          '1', false, 0, 0);
        Configuration::updateValue('ALLEGRO_PAYU_FINISHED',     '2', false, 0, 0);
        Configuration::updateValue('ALLEGRO_PAYU_CANCELED',     '7', false, 0, 0);
        Configuration::updateValue('ALLEGRO_PAYU_ERROR',        '8', false, 0, 0);
        Configuration::updateValue('ALLEGRO_COD',               '13', false, 0, 0);
        Configuration::updateValue('ALLEGRO_WIRE_TRANSFER',     '10', false, 0, 0);
        Configuration::updateValue('ALLEGRO_CARRIER',           '0', false, 0, 0);

        Configuration::updateValue('ALLEGRO_CATEGORIES_VER_1',   '', false, 0, 0);
        Configuration::updateValue('ALLEGRO_CATEGORIES_VER_56',  '', false, 0, 0);

        Configuration::updateValue('ALLEGRO_SEND_STATS',         '1', false, 0, 0);

        Configuration::updateValue('ALLEGRO_PRICE_PC',           '0', false, 0, 0);
        Configuration::updateValue('ALLEGRO_TITLE_GEN_PATTERN',  '[product_name] [attributes]', false, 0, 0);
        Configuration::updateValue('ALLEGRO_PRICE_ROUND',        '0', false, 0, 0);
        Configuration::updateValue('ALLEGRO_A_LIST_SUGGEST',     '0', false, 0, 0);
        Configuration::updateValue('ALLEGRO_THEME_TINYMCE',      '0', false, 0, 0);
        Configuration::updateValue('ALLEGRO_LEGACY_IMAGES',      '1', false, 0, 0);
        Configuration::updateValue('ALLEGRO_STOCK_SYNC',         '1', false, 0, 0);

        Configuration::updateValue('ALLEGRO_THEME_HTMLPURIFIER', '1', false, 0, 0);

        // Demo theme
        $allegroTheme = new AllegroTheme();
        $allegroTheme->name = $this->l('Demo theme (old)');
        $allegroTheme->content = @file_get_contents(dirname(__FILE__).'/doc/demo_theme.html');
        $allegroTheme->smarty = 0;
        $allegroTheme->format = 0;
        $allegroTheme->active = 1;
        $allegroTheme->save();

        // Demo theme (new)
        $allegroTheme = new AllegroTheme();
        $allegroTheme->name = $this->l('Demo theme (new format)');
        $allegroTheme->content = @file_get_contents(dirname(__FILE__).'/doc/demo_theme_new.html');
        $allegroTheme->smarty = 0;
        $allegroTheme->format = 1;
        $allegroTheme->active = 1;
        $allegroTheme->default = 1;
        $allegroTheme->save();

        if (!parent::install()
            || !Configuration::updateValue('ALLEGRO_INSTALLED_VERSION', $this->version, false, 0, 0)
            || !$this->registerHook('displayBackOfficeHeader')
            //|| !$this->registerHook('displayAdminOrderLeft')
            || !$this->registerHook('displayAdminOrder')/* PS 1.5 */)
            $ret &= false;

        if (!$ret) {
            $this->uninstall();
        }

        return $ret;
    }


    public function uninstall()
    {
        $this->uninstallModuleTab('AdminAllegro');
        foreach (self::$_tabs as $class => $tab) {
            $this->uninstallModuleTab($class);
        }

        $this->dropTables();

        Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` LIKE "ALLEGRO_%"');

        if (!parent::uninstall()
            || !$this->unregisterHook('displayBackOfficeHeader')
            || !$this->unregisterHook('displayAdminOrder')/* PS 1.5 */) {
            return false;
        }

        return true;
    }


    private function dropTables()
    {
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_account`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_auction`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_category`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_field`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_image`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_order`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_product`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_product_account`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_product_shop`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_shipping`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'allegro_theme`');  
    }


    public function installModuleTab($tabClass, $tabName, $idTabParent, $active = 1)
    {
        $tab = new Tab();
        $tab->name = $tabName;
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $idTabParent;
        $tab->active = $active;

        if (!$tab->add())
            return false;

        return $tab->id;
    }


    public function uninstallModuleTab($tabClass)
    {
        $idTab = Tab::getIdFromClassName($tabClass);

        if ($idTab) {
            $tab = new Tab($idTab);
            return $tab->delete();
        }

        return false;
    }


    public function hookDisplayBackOfficeHeader()
    {
        // Non native controller fix
        if (method_exists($this->context->controller, 'addCSS')) {
            $this->context->controller->addCSS(($this->_path).'/css/admin.css');
            $this->context->controller->addJS(($this->_path).'/js/admin.js');

            if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
                $this->context->controller->addCSS(($this->_path).'/css/admin_15.css');
            }
        }
    }


    public function hookDisplayAdminOrder($params)
    {
        $allegro_order = Db::getInstance()->GetRow(
            'SELECT *
            FROM `'._DB_PREFIX_.'allegro_order`
            WHERE `id_order` = '.(int)$params['id_order']
        );

        if($allegro_order['gd_address'])
            $allegro_order['gd_address'] = unserialize($allegro_order['gd_address']);

        if($allegro_order) {
            $this->context->smarty->assign(array(
                'allegro_order' => $allegro_order,
            ));

            return $this->display(__FILE__, 'admin_vieworder.tpl');
        }
    }


    public function hookDisplayAdminOrderLeft($params)
    {
        return $this->hookDisplayAdminOrder($params);
    }


    public function getKey()
    {
        return substr(md5(_COOKIE_KEY_), 0, 16);
    }


    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitAllegro')) == true ||
            ((bool)Tools::isSubmit('submitAllegroSandbox')) == true) {
            $this->postProcess();
        } elseif ((bool)Tools::getValue('import')) {
            $this->processImport();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $this->renderForm();
    }


    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAllegroModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($this->getConfigForm());
    }


    protected function getBaseLink($id_shop = null, $ssl = true, $relative_protocol = false)
    {
        $ssl_enable = (bool)Configuration::get('PS_SSL_ENABLED');

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }

        if ($relative_protocol) {
            $base = '//'.($ssl && $ssl_enable ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl && $ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        }

        return $base.$shop->getBaseURI();
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $currentIndex = $this->context->link->getAdminLink('AdminModules', true)
            .'&configure='.$this->name.'&module_name='.$this->name;
        
        $status = (int)Configuration::get('ALLEGRO_3X_UPGRADE');

        $array = array();

        if (!$status) {
            $array = array(
                array(
                    'form' => array(
                        'legend' => array(
                        'title' => $this->l('REST API'),
                        'icon' => 'icon-cogs',
                        ),
                        'description' => '<b>'.$this->l('Before you start using integration you need to register application.').
                            '</b><br /><br />'.
                            $this->l('To register application go to').
                            ' <a href="https://credentials.allegroapi.io/">https://credentials.allegroapi.io/</a> '.
                            $this->l('login to your allegro account and fill registration form.').
                            '<br />'.$this->l('Your').' <b>Redirect URI</b> '.$this->l('is domain of your store').
                            ' (<a href="'.$this->getBaseLink().'">'.$this->getBaseLink().'</a>).
                            <br /><br />
                            <b>'.$this->l('In case of error "Invalid redirect..." check your domain.').'</b>',
                        'input' => array(
                            array(
                                'col' => 6,
                                'type' => 'text',
                                'name' => 'ALLEGRO_CLIENT_ID',
                                'label' => $this->l('Client ID'),
                            ),
                            array(
                                'col' => 6,
                                'type' => 'text',
                                'name' => 'ALLEGRO_CLIENT_SECRET',
                                'label' => $this->l('Client Secret'),
                            ),
                            array(
                                'col' => 6,
                                'type' => 'text',
                                'name' => 'ALLEGRO_API_KEY',
                                'label' => $this->l('API Key'),
                            ),
                        ),
                        'submit' => array(
                            'title' => $this->l('Save'),
                            'name' => 'submitAllegro',
                        ),
                    ),
                ),
                array(
                    'form' => array(
                        'legend' => array(
                        'title' => $this->l('REST API Sandbox (optional)'),
                        'icon' => 'icon-cogs',
                        ),
                        'description' => '<b>'.$this->l('To use sandbox environment').
                            ' (<a href="http://webapisandbox.pl/">http://webapisandbox.pl/</a>) '.
                            $this->l('you need to register aplication on this page:').
                            ' <a href="https://credentials-sandbox.allegroapi.io/">https://credentials-sandbox.allegroapi.io/</a></b>',
                        'warning' => $this->l('Currently the allegro sandbox is not working properly and contains many errors. Please do not report problems linked to the malfunctioning of the module on the sandbox environment.'),
                        'input' => array(
                            array(
                                'col' => 6,
                                'type' => 'text',
                                'name' => 'ALLEGRO_SANDBOX_CLIENT_ID',
                                'label' => $this->l('Client ID'),
                            ),
                            array(
                                'col' => 6,
                                'type' => 'text',
                                'name' => 'ALLEGRO_SANDBOX_CLIENT_SECRET',
                                'label' => $this->l('Client Secret'),
                            ),
                            array(
                                'col' => 6,
                                'type' => 'text',
                                'name' => 'ALLEGRO_SANDBOX_API_KEY',
                                'label' => $this->l('API Key'),
                            ),
                        ),
                        'submit' => array(
                            'title' => $this->l('Save'),
                            'name' => 'submitAllegroSandbox',
                        ),
                    ),
                ),
            );
        }

        // Upgrade 3.x form
        if ($status) {

            $this->smarty->assign(array(
                'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&module_name='.$this->name,
            ));

            $html = $this->display(__FILE__, 'form.tpl');


            array_unshift($array, array(
                'form' => array(
                    'legend' => array(
                    'title' => $this->l('UPGRADE FROM 3.x'),
                    'icon' => 'icon-cogs',
                    ),
                    'description' => '<b>'.$this->l('You have installed the 3.x version so you can move most of the data from the previous version.').
                        '</b><br /><br />'.
                        $this->l('To do that use links below, you do not have to import everything but import in order as below. You can also skip this and start using module now.').'
                        <ol>
                            <li><a class="btn btn-default ajaxcall-recurcive" '.(!($status&2) ? ' href="'.$currentIndex.'&ajax=1&import=themes"' : 'href="#" style="text-decoration: line-through;"').'>'.$this->l('Allegro themes').'</a></li>
                            <li><a class="btn btn-default ajaxcall-recurcive" '.(!($status&4) ? ' href="'.$currentIndex.'&ajax=1&import=accounts"' : 'href="#" style="text-decoration: line-through;"').'>'.$this->l('Allegro accounts').'</a></li>
                            <li><a class="btn btn-default ajaxcall-recurcive" '.(!($status&8) ? ' href="'.$currentIndex.'&ajax=1&import=products"' : 'href="#" style="text-decoration: line-through;"').'>'.$this->l('Product data (selected images, themes etc.)').'</a></li>
                            <li><a class="btn btn-default ajaxcall-recurcive" '.(!($status&16) ? ' href="'.$currentIndex.'&ajax=1&import=pparams"' : 'href="#" style="text-decoration: line-through;"').'>'.$this->l('Product params (allegro params like category, features etc.)').'</a></li>
                            <li><a class="btn btn-default ajaxcall-recurcive" '.(!($status&32) ? ' href="'.$currentIndex.'&ajax=1&import=extimages"' : 'href="#" style="text-decoration: line-through;"').'>'.$this->l('Product external images (loaded on FTP)').'</a></li>
                            <li><a class="btn btn-default ajaxcall-recurcive" '.(!($status&64) ? ' href="'.$currentIndex.'&ajax=1&import=auctions"' : 'href="#" style="text-decoration: line-through;"').'>'.$this->l('Auctions mapping (accounts import required)').'</a></li>
                        </ol>
                        <p>&nbsp;</p>
                        <p>
                            <a class="btn btn-default" href="'.$currentIndex.'&import=finish">'.$this->l('Finish update & start using module').'</a>
                            '.$this->l('or').' <a class="btn btn-default" href="'.$currentIndex.'&import=revert">'.$this->l('revert & try again').'</a>
                        </p>'.$html,
                    'input' => array(
                        array(
                            'label' => $this->l('Product data - images selection'),
                            'desc' => $this->l('In version 4.x of module all images are set to product by default - no need to set it manually.'),
                            'name' => 'ALLEGRO_UPGRADE_PDATA_IMAGES',
                            'type' => 'select',
                            'options' => array(
                                'query' => array(
                                    array('id' => 0, 'name' => $this->l('Do not import')),
                                    array('id' => 1, 'name' => $this->l('Import').' '.$this->l('(not recommended - see manual)')),
                                ),
                                'id' => 'id',
                                'name' => 'name',
                            ),
                            'identifier' => 'id'
                        ),
                        array(
                            'label' => $this->l('Product params'),
                            'desc' => $this->l('In version 4.x of module you can set global params and use category mapping.'),
                            'name' => 'ALLEGRO_UPGRADE_PPARMAS_CPARAMS',
                            'type' => 'select',
                            'options' => array(
                                'query' => array(
                                    array('id' => 0, 'name' => $this->l('Import only category params')),
                                    array('id' => 1, 'name' => $this->l('Import all params').' '.$this->l('(not recommended - see manual)')),
                                ),
                                'id' => 'id',
                                'name' => 'name',
                            ),
                            'identifier' => 'id'
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'name' => 'submitAllegroSandbox',
                    ),
                ),
            ));
        }

        return $array;
    }


    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'ALLEGRO_CLIENT_ID' => Configuration::get('ALLEGRO_CLIENT_ID'),
            'ALLEGRO_CLIENT_SECRET' => Configuration::get('ALLEGRO_CLIENT_SECRET'),
            'ALLEGRO_API_KEY' => Configuration::get('ALLEGRO_API_KEY'),

            'ALLEGRO_SANDBOX_CLIENT_ID' => Configuration::get('ALLEGRO_SANDBOX_CLIENT_ID'),
            'ALLEGRO_SANDBOX_CLIENT_SECRET' => Configuration::get('ALLEGRO_SANDBOX_CLIENT_SECRET'),
            'ALLEGRO_SANDBOX_API_KEY' => Configuration::get('ALLEGRO_SANDBOX_API_KEY'),

            'ALLEGRO_UPGRADE_PDATA_IMAGES' => Configuration::get('ALLEGRO_UPGRADE_PDATA_IMAGES'),
            'ALLEGRO_UPGRADE_PPARMAS_CPARAMS' => Configuration::get('ALLEGRO_UPGRADE_PPARMAS_CPARAMS'),
        );
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, trim(Tools::getValue($key)), false, 0, 0);
        }

        // If we have API credentials enable admin tab
        $idTab = Db::getInstance()->GetValue('
            SELECT `id_tab` FROM `'._DB_PREFIX_.'tab`
            WHERE `class_name` = "AdminAllegro" 
            AND `id_parent` = 0');

        if ($idTab) {
            $tab = new Tab((int)$idTab);

            $tab->active = 0;
            if ((Configuration::get('ALLEGRO_CLIENT_ID') &&
                Configuration::get('ALLEGRO_CLIENT_SECRET') &&
                Configuration::get('ALLEGRO_API_KEY'))
                ||
                (Configuration::get('ALLEGRO_SANDBOX_CLIENT_ID') &&
                Configuration::get('ALLEGRO_SANDBOX_CLIENT_SECRET') &&
                Configuration::get('ALLEGRO_SANDBOX_API_KEY'))) {
                $tab->active = 1;
            }

            $tab->save();
        }

        // Enable module on all shops
        if (Shop::isFeatureActive()) {
	        @Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'module_shop` (id_module, id_shop, enable_device)
	            SELECT '.(int)$this->id.', id_shop, 7 FROM `'._DB_PREFIX_.'shop` WHERE deleted = 0
	        ');
        }
    }


    protected function processImport()
    {
        if ($import = Tools::getValue('import')) {
            $status = (int)Configuration::get('ALLEGRO_3X_UPGRADE');

            switch ($import) {
                case 'revert':

                    Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'allegro_account`');
                    Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'allegro_auction`');
                    Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'allegro_field`');
                    Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'allegro_image`');
                    Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'allegro_product`');
                    Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'allegro_theme`');

                    Configuration::updateValue('ALLEGRO_3X_UPGRADE', 1, false, 0, 0);
                    break;
                case 'finish':
                    Configuration::updateValue('ALLEGRO_3X_UPGRADE', 0, false, 0, 0);
                    break;
                default:
                    break;
            }
        }
    }


    protected function rebuildIndex($country, $force = true)
    {
        // Products
        Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'allegro_product` (id_product, id_product_attribute)
            SELECT id_product, 0 FROM `'._DB_PREFIX_.'product`
        ');

        // Combinations
        Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'allegro_product` (id_product, id_product_attribute)
            SELECT id_product, id_product_attribute FROM `'._DB_PREFIX_.'product_attribute`
        ');

        // Product shop
        Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'allegro_product_shop` (`id_allegro_product`, `id_shop`)
                SELECT ap.`id_allegro_product`, ps.`id_shop` FROM `'._DB_PREFIX_.'allegro_product` ap
                JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.`id_product` = ap.`id_product` AND ap.`id_product_attribute` = 0)
        ');

        // Product combination shop
        if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'allegro_product_shop` (`id_allegro_product`, `id_shop`)
                    SELECT ap.`id_allegro_product`, pas.`id_shop` FROM `'._DB_PREFIX_.'allegro_product` ap
                    JOIN `'._DB_PREFIX_.'product_attribute_shop` pas
                        ON (pas.`id_product` = ap.`id_product` AND pas.`id_product_attribute` = ap.`id_product_attribute`)
            ');
        } else {
            // No pas.`id_product` column 
            Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'allegro_product_shop` (`id_allegro_product`, `id_shop`)
                    SELECT ap.`id_allegro_product`, pas.`id_shop` FROM `'._DB_PREFIX_.'allegro_product` ap
                    JOIN `'._DB_PREFIX_.'product_attribute_shop` pas
                        ON (pas.`id_product_attribute` = ap.`id_product_attribute`)
            ');
        }
    }


    public function cleanup()
    {   
        // Delete finished auctions and auctions created by deleted accounts
        Db::getInstance()->Execute('
            DELETE FROM `'._DB_PREFIX_.'allegro_auction`
            WHERE `status` = 3
            OR `id_allegro_account` NOT IN (
                SELECT `id_allegro_account` FROM `'._DB_PREFIX_.'allegro_account`
            )
        ');

        // Delete allegro categories
        Db::getInstance()->Execute('
            DELETE FROM `'._DB_PREFIX_.'allegro_category`
        ');

        Configuration::updateValue('ALLEGRO_CATEGORIES_VER_1', '', false, 0, 0);

        $_SESSION = array();
    }

    private function handleAjaxAction()
    {
        $status = (int)Configuration::get('ALLEGRO_3X_UPGRADE');

        switch (Tools::getValue('import')) {

            case 'themes':

                if (!($status&2)) {
                    $themes = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'allegro_theme_v3`');

                    foreach ($themes as $key => $t) {
                        $theme = new AllegroTheme();
                        $theme->name = $t['name'];
                        $theme->content = $t['content'];
                        $theme->active = (int)$t['active'];
                        $theme->format = 0;

                        if ($theme->add()) {
                            $tmap = @unserialize(Configuration::get('ALLEGRO_UPGRADE_TMAP', array()));
                            $tmap[(int)$t['id_allegro_theme']] = $theme->id;
                            Configuration::updateValue('ALLEGRO_UPGRADE_TMAP', serialize($tmap), false, 0, 0);
                        }
                    }
                    
                    Configuration::updateValue('ALLEGRO_3X_UPGRADE', $status+2, false, 0, 0);
                }

                die(json_encode(array('continue' => 0)));

            case 'accounts':

                if (!($status&4)) {
                    $accounts = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'allegro_account_v3`');

                    foreach ($accounts as $key => $a) {
                        $account = new AllegroAccount();
                        $account->name = $a['name'];
                        $account->login = $a['login'];
                        $account->country = 1; // To del
                        $account->id_currency = (int)$a['id_currency'];
                        $account->id_language = (int)$a['id_language'];
                        $account->active = 0;

                        if ($account->add()) {
                            $amap = @unserialize(Configuration::get('ALLEGRO_UPGRADE_AMAP', array()));
                            $amap[(int)$a['id_allegro_account']] = $account->id;
                            Configuration::updateValue('ALLEGRO_UPGRADE_AMAP', serialize($amap), false, 0, 0);
                        }
                    }
                    
                    Configuration::updateValue('ALLEGRO_3X_UPGRADE', $status+4, false, 0, 0);
                }

                die(json_encode(array('continue' => 0)));

            case 'products':

                $cursor = (int)Tools::getValue('cursor');
                $stepSize = 10000;

                // Index products
                if ($cursor === 0) {
                    $this->rebuildIndex(1/* Allegro */);
                }

                // Get list
                $pparams = Db::getInstance()->ExecuteS('
                    SELECT * FROM `'._DB_PREFIX_.'allegro_form_v3`
                    WHERE `form_id` = 0
                    LIMIT '.($stepSize*$cursor).', '.($stepSize).'
                ');
               
                if (count($pparams) && !($status&8)) {

                    $tmap = @unserialize(Configuration::get('ALLEGRO_UPGRADE_TMAP', array()));

                    foreach ($pparams as $key => $p) {

                        if ($p['value']) {
                            $pV = unserialize($p['value']);
                        } else {
                            $pV[] = null;
                        }
                        if (isset($pV['int']) && is_array($pV['int'])) {
                            $pV['int'] = array_sum($pV['int']);
                        }

                        $pFId = (int)$p['form_id'];

                        if ($pFId == 0) {

                            $allegroProductId = AllegroProduct::getIdByPAId($p['id_product'], $p['id_product_attribute'], $p['country_id']);
                            
                            if ($allegroProductId && isset($pV['array'])) {
                            
                                $aProduct = new AllegroProduct((int)$allegroProductId);

                                if (isset($tmap[(int)$pV['array']['id_theme']])) {
                                    $aProduct->id_allegro_theme = (int)$tmap[(int)$pV['array']['id_theme']];
                                }

                                // Images
                                if (Configuration::get('ALLEGRO_UPGRADE_PDATA_IMAGES')) {
                                    if ((int)$pV['array']['cover']) {
                                        $aProduct->image_cover = 'shop:'.(int)$pV['array']['cover'];
                                    }

                                    if (count($pV['array']['images'])) {
                                        $images = Db::getInstance()->getValue('
                                            SELECT GROUP_CONCAT(i.`id_image`)
                                            FROM `'._DB_PREFIX_.'image` i
                                            '.Shop::addSqlAssociation('image', 'i').'
                                            WHERE i.`id_product` = '.(int)$aProduct->id_product.'
                                            AND i.`id_image` NOT IN ('.implode(',', $pV['array']['images']).')'
                                        );

                                        $aProduct->images_excl = $images;
                                    }
                                }

                                $aProduct->save();
                            }

                       }
                    }
                } else {
                    Configuration::updateValue('ALLEGRO_3X_UPGRADE', $status+8, false, 0, 0);
                    die(json_encode(array('continue' => 0, 'cursor' => 0)));
                }

                die(json_encode(array('continue' => 1, 'cursor' => $cursor+1)));

                break;

            case 'pparams':

                $cursor = (int)Tools::getValue('cursor');
                $stepSize = 10000;


                $ALL_PARAMS = (int)Configuration::get('ALLEGRO_UPGRADE_PPARMAS_CPARAMS');

                $fidWhiteList = array(
                    1, // Name
                    4, // Duration
                    5, // Quantity
                    6, // Starting price
                    7, // Minimal price
                    8, // Price
                    12, // Shipping pay
                    13, // Shipping params
                    15, // Promo
                    24, // Product info
                    27, // Shipping & payment info
                    28, // Type
                    340 // Shipping time
                );

                $tmap = @unserialize(Configuration::get('ALLEGRO_UPGRADE_TMAP', array()));

                // Index products
                if ($cursor === 0) {
                    $this->rebuildIndex(1/* Allegro */);
                }

                // Get list
                $pparams = Db::getInstance()->ExecuteS('
                    SELECT * FROM `'._DB_PREFIX_.'allegro_form_v3` 
                    WHERE value IS NOT NULL
                    LIMIT '.($cursor*$stepSize).', '.$stepSize
                );

                if (count($pparams) && !($status&16)) {
                    
                    foreach ($pparams as $key => $p) {
                        if ($p['value']) {
                            $pV = unserialize($p['value']);
                        } else {
                            continue;
                        }
                        if (isset($pV['int']) && is_array($pV['int'])) {
                            $pV['int'] = array_sum($pV['int']);
                        }

                        $pFId = (int)$p['form_id'];

                        $allegroProductId = AllegroProduct::getIdByPAId($p['id_product'], $p['id_product_attribute'], $p['country_id']);

                        // Product params
                        if ($p['form_id'] == 0 /* ext desc */) {
                            // Do nothing
                        } 
                        // FIDs
                        else { 
                            if ($allegroProductId) {

                                $pVVal = reset($pV);

                                // Skip global fields
                                if (($pFId == 2 /* Category */ || $pFId > 700) || ($ALL_PARAMS && in_array($pFId, $fidWhiteList))) {
                                    Db::getInstance()->Execute('
                                        INSERT INTO `'._DB_PREFIX_.'allegro_field` (`scope`, `id`, `fid`, `value`)
                                        VALUES (
                                            '.(int)AFField::SCOPE_PRODUCT.', 
                                            '.(int)$allegroProductId.', 
                                            '.(int)$pFId.', 
                                            '.($pVVal == null ? 'NULL' : '"'.pSql($pVVal).'"').')
                                        ON DUPLICATE KEY UPDATE `value` = '.($pVVal == null ? 'NULL' : '"'.pSql($pVVal).'"').'');
                                }
                            }
                        }
                    }
                } else {
                    Configuration::updateValue('ALLEGRO_3X_UPGRADE', $status+16, false, 0, 0);
                    die(json_encode(array('continue' => 0)));
                }

                die(json_encode(array('continue' => 1, 'cursor' => $cursor+1)));
               
            case 'extimages':

                if (!($status&32)) {
        
                    foreach (glob(dirname(__FILE__).'/auction_img/{*/,*/*/}*.{jpg,png,gif}', GLOB_BRACE) as $filename) {

                        $path = _ALLEGRO_IMG_DIR_;

                        if(!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }

                        // Extract data from path
                        $outputArray = array();
                        preg_match("/auction_img\/(\d+\/)(\d+\/)*(\d+|cover).(jpg|png|gif)/", $filename, $outputArray);

                        $idProduct = (int)$outputArray[1];
                        $idProductAttribute = (int)$outputArray[2];
                        $index = (int)$outputArray[3];
                        $extension = $outputArray[4];
                        $allegroProductId = AllegroProduct::getIdByPAId($idProduct, $idProductAttribute, 1);

                        if (!$allegroProductId) {
                            continue;
                        }

                        $tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

                        $allegroImage = new AllegroImage();
                        $allegroImage->id_allegro_product = $allegroProductId;

                        if($allegroImage->add() && copy($filename, $tmpName)) {
                            // Check file size and memory before save
                            if (ImageManager::checkImageMemoryLimit($tmpName)) {
                                if(ImageManager::resize($tmpName, $path.$allegroImage->id.'.jpg')) {
                                    $images_types = ImageType::getImagesTypes('products');
                                    foreach ($images_types as $k => $imageType) {
                                        ImageManager::resize(
                                            $path.$allegroImage->id.'.jpg',
                                            $path.$allegroImage->id.'-'.stripslashes($imageType['name']).'.jpg',
                                            (int)$imageType['width'], (int)$imageType['height']
                                        );
                                    }

                                    if ($index == 'cover') {
                                        $aProduct = new AllegroProduct($allegroProductId);
                                        $aProduct->image_cover = 'allegro:'.$allegroImage->id;
                                        $aProduct->save();
                                    }
                                }
                                unlink($tmpName);
                            } 
                        }
                    }

                    Configuration::updateValue('ALLEGRO_3X_UPGRADE', $status+32, false, 0, 0);
                }

                die(json_encode(array('continue' => 0)));
            
            case 'auctions':

                $cursor = (int)Tools::getValue('cursor');
                $stepSize = 10000;

                // Get list
                $auctions = Db::getInstance()->ExecuteS('
                    SELECT * FROM `'._DB_PREFIX_.'allegro_auction_v3` 
                    WHERE `status` < 3
                    LIMIT '.($stepSize*$cursor).', '.($stepSize).'
                ');

                if (count($auctions) && !($status&64)) {
                
                    $amap = @unserialize(Configuration::get('ALLEGRO_UPGRADE_AMAP', array()));

                    foreach ($auctions as $key => $a) {
                        $allegroProductId = AllegroProduct::getIdByPAId($a['id_product'], $a['id_product_attribute'], $a['country_id']);

                        if ($allegroProductId && isset($amap[(int)$a['id_account']])) {
                            $aAuction = new AllegroAuction();
                            $aAuction->id_auction = (float)$a['id_auction'];
                            $aAuction->id_allegro_product = (int)$allegroProductId;
                            $aAuction->id_allegro_account = (int)$amap[(int)$a['id_account']];
                            $aAuction->id_shop = (int)$a['id_shop'];
                            $aAuction->duration = 0;
                            $aAuction->date_start = date("Y-m-d H:i:s");
                            $aAuction->is_standard = 0;
                            $aAuction->title = '--';
                            $aAuction->quantity = ((int)$a['sell_qty']-(int)$a['sold_qty']);
                            $aAuction->price = 0;
                            $aAuction->cost_info = '--';
                            $aAuction->status = AllegroAuction::STATUS_NEW;
                            $aAuction->date_add = date("Y-m-d H:i:s");
                            $aAuction->date_upd = date("Y-m-d H:i:s");

                            $aAuction->add();
                        }
                    }
                } else {
                    Configuration::updateValue('ALLEGRO_3X_UPGRADE', $status+64, false, 0, 0);
                    die(json_encode(array('continue' => 0)));
                }

                die(json_encode(array('continue' => 1, 'cursor' => $cursor+1)));

            default:
                break;
        }
    }
}
