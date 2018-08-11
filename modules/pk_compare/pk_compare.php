<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once(_PS_MODULE_DIR_.'pk_compare/classes/CompareProduct.php');

class Pk_Compare extends Module {

    public function __construct() {

        $this->name = 'pk_compare';
        $this->author = 'Promokit Co.';
        $this->version = '1.0';
        $this->need_instance = 0;
        $this->controllers = array('compare');
        $this->max_to_compare = 4;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Compare Products', array(), 'Modules.TextBlock');
        $this->description = $this->trans('Add possibility to compare products', array(), 'Modules.TextBlock');

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);


    }

    public function install() {

        return parent::install() &&
            $this->installDB() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('compareProducts') &&
            $this->registerHook('compareButton') &&
            $this->registerHook('displayAfterBodyOpeningTag');

    }

    public function uninstall() {

        return parent::uninstall() && $this->uninstallDB();

    }

    public function installDB() {

        $response = true;
        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'compare` (
          `id_compare` int(10) unsigned NOT NULL auto_increment,
          `id_customer` int(10) unsigned NOT NULL,
          PRIMARY KEY (`id_compare`)
        ) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'compare_product` (
          `id_compare` int(10) unsigned NOT NULL,
          `id_product` int(10) unsigned NOT NULL,
          `date_add` datetime NOT NULL,
          `date_upd` datetime NOT NULL,
          PRIMARY KEY (`id_compare`,`id_product`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        foreach ($sql as $query) {
            $response = Db::getInstance()->execute($query);
        }

        return $response;
    }

    public function uninstallDB() {

        $ret = Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'compare`') && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'compare_product`');

        return $ret;
    }

    public function hookDisplayHeader() {

        $this->context->controller->registerStylesheet($this->name, 'modules/'.$this->name.'/assets/css/styles.css', ['media' => 'all', 'priority' => 150]);
        $this->context->controller->registerJavascript($this->name, 'modules/'.$this->name.'/assets/js/scripts.js', ['position' => 'bottom', 'priority' => 150]);

    }

    public function hookCompareButton($params) {  

        $compare = new CompareProduct();
        $compared_products = $compare->getCompareProducts($this->context->cookie->id_compare);

        $this->smarty->assign(array(
            'product_id' => $params['product_id'],
            'in_compare' => is_array($compared_products) ? $compared_products : array(),
        ));
        return $this->fetch('module:'.$this->name.'/views/templates/hook/product_button.tpl');

    }

    public function hookCompareProducts($params) {  
    
        $data = $this->getIds();
        $this->smarty->assign(array(
            'total_in_compare' => $data['num'],
            'comparator_max_item' => $this->max_to_compare,
            'page_link' => Context::getContext()->link->getModuleLink('pk_compare', 'compare')
        ));

        return $this->fetch('module:'.$this->name.'/views/templates/hook/compare_products.tpl');

    }

    public function hookdisplayAfterBodyOpeningTag($params) {

        $data = $this->getIds();
        $this->smarty->assign(array(
            'ids' => $data['ids'],
            'total_in_compare' => $data['num'],
            'comparator_max_item' => $this->max_to_compare
        )); 

        return $this->fetch('module:'.$this->name.'/views/templates/hook/after_body.tpl');

    }

    public function getIds() {

        $data = array();
        $compare = new CompareProduct();
        $ids = $compare->getCompareProducts($this->context->cookie->id_compare);
        if (is_array($ids) && !empty($ids)) {
            $data['num'] = count($ids);
            $data['ids'] = json_encode($ids);
        } else {
            $data['num'] = 0;
            $data['ids'] = '[]';
        }
        return $data;
        
    }

    
}