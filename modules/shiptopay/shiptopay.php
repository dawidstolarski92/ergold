<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2017 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER
 * support@mypresta.eu
 */
class ShipToPay extends Module
{
    function __construct()
    {
        $this->name = 'shiptopay';
        $this->tab = 'administration';
        $this->version = '1.7.2';
        $this->author = 'MyPresta.eu';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Ship To Pay');
        $this->description = $this->l('Module to assign payments for selected carriers with Virtual Products support');
        $this->checkIfCheckoutDirExists();
    }

    private function checkIfCheckoutDirExists()
    {
        if (!file_exists('../override/classes/checkout'))
        {
            mkdir('../override/classes/checkout', 0777, true);
        }
    }

    private function installdb()
    {
        $prefix = _DB_PREFIX_;
        $statements = array();
        $statements[] = 'CREATE TABLE `' . _DB_PREFIX_ . 'shiptopay` (`id_shop` INT(11) NOT NULL, `id_carrier` INT(11) NOT NULL, `id_payment` INT(11) NOT NULL, UNIQUE KEY `key` (`id_shop`, `id_carrier`, `id_payment`))';

        foreach ($statements as $statement)
        {
            if (!Db::getInstance()->Execute($statement))
            {
                return false;
            }
        }
        return true;
    }

    function install()
    {
        if (!parent::install() || !$this->installdb() || !$this->registerHook('actionCarrierUpdate'))
        {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DROP TABLE ' . _DB_PREFIX_ . 'shiptopay') || !$this->unregisterHook('actionCarrierUpdate'))
        {
            return false;
        }
        return true;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitUpdateShipToPay'))
        {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'shiptopay` WHERE `id_shop` = ' . (int)$this->context->shop->id);
            foreach ($_POST as $key => $value)
            {
                if (substr($key, 0, 9) === 'SHIPTOPAY')
                {
                    $explode = explode('_', $key);
                    Db::getInstance()->execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'shiptopay` VALUES (' . (int)$this->context->shop->id . ', ' . (int)$explode[1] . ', ' . (int)$explode[2] . ')');
                }
            }
            $this->_html .= $this->displayConfirmation($this->l('Settings updated successfully.'));
        }
    }

    public function getContent()
    {
        if (Configuration::get('PS_DISABLE_OVERRIDES') == true) {
            $this->context->controller->errors[] = $this->l('Overrides are disabled. Ship to pay module may not work properly without overrides').'. <a href="'.$this->context->link->getAdminLink('AdminPerformance').'">'.$this->l('Enable overrides').'</a>';
        }
        $this->_html = '';
        $this->postProcess();
        $helper = $this->initForm();
        $this->_html .= $this->displayInfo() . $helper->generateForm($this->fields_form);
        return $this->_html;
    }

    public function displayInfo()
    {
        return $this->display(__file__, 'views/shiptopay.tpl');
    }


    private function initForm()
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $k => $language)
        {
            $languages[$k]['is_default'] = (int)$language['id_lang'] == Configuration::get('PS_LANG_DEFAULT');
        }
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $languages;
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = true;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'submitUpdateShipToPay';
        $helper->tpl_vars = array(
            'fields_value' => array()
        );
        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->displayName,
                'icon' => 'icon-cogs'
            ),
            'submit' => array(
                'name' => 'submitUpdateShipToPay',
                'title' => $this->l('Save '),
            ),
            'input' => array()
        );

        $payment_modules = array();
        $modules = Module::getModulesOnDisk(true);
        foreach ($modules as $module)
        {
            if ($module->tab == 'payments_gateways')
            {
                if ($module->id)
                {
                    $payment_modules[] = array(
                        'name' => $module->displayName . ' (' . $module->name . ')',
                        'id' => $module->id
                    );
                }
            }
        }
        //VIRTUAL PRODUCTS SUPPORT
        $this->fields_form[0]['form']['input'][] = array(
            'type' => 'checkbox',
            'label' => $this->l('Virtual products'),
            'name' => 'SHIPTOPAY_' . '0',
            'values' => array(
                'query' => $payment_modules,
                'id' => 'id',
                'name' => 'name'
            )
        );

        foreach ($payment_modules as $module)
        {
            //$helper->tpl_vars['fields_value']['SHIPTOPAY_' . $carrier['id_carrier'] . '_' . $module['id']] = $this->getShipToPay($carrier['id_carrier'], $module['id']);
            $helper->tpl_vars['fields_value']['SHIPTOPAY_' . '0' . '_' . $module['id']] = $this->getShipToPay(0, $module['id']);
        }

        return $helper;
    }

    private function getShipToPay($id_carrier, $id_payment)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'shiptopay` WHERE `id_shop` = ' . (int)$this->context->shop->id . ' AND `id_carrier` = ' . (int)$id_carrier . ' AND `id_payment` = ' . (int)$id_payment);
    }
}