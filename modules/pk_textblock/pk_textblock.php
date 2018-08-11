<?php
/*
* 2007-2015 PrestaShop
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

//use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

//class Pk_TextBlock extends Module implements WidgetInterface
class Pk_TextBlock extends Module
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'pk_textblock';
        $this->author = 'Promokit Co.';
        $this->version = '1.0';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Promokit Text Block', array(), 'Modules.TextBlock');
        $this->description = $this->trans('Integrates custom text blocks anywhere in your store front', array(), 'Modules.TextBlock');

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';
        $this->db_lang = _DB_PREFIX_.'pk_textblock_lang';

        $this->hooks = array(
            'content_top',
            'content_bottom',
            'displayHome',
            'displayFooter'
        );

    }

    public function install()
    {
        return  parent::install() &&
            $this->installDB() &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('content_top') &&
            $this->registerHook('content_bottom') &&
            $this->registerHook('displayFooter') &&
            Configuration::updateValue('PK_TB_HOOK', 'displayHome') &&
            $this->installFixtures();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDB();
    }

    public function installDB()
    {
        $return = true;
        $return &= Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'.$this->db_lang.'` (
                `id_info` INT UNSIGNED NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned DEFAULT NULL,
                `hook` text NOT NULL,
                `text` text NOT NULL
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        return $return;
    }

    public function uninstallDB($drop_table = true)
    {
        $ret = true;
        if ($drop_table) {
            $ret &= Db::getInstance()->execute('DROP TABLE IF EXISTS `'.$this->db_lang.'`');
        }

        return $ret;
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

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('saveps_customtext')) {

            if (!Tools::getValue('text_'.(int)Configuration::get('PS_LANG_DEFAULT'), false)) {
                $output = $this->displayError($this->trans('Please fill out all fields.', array(), 'Admin.Notifications.Error')) . $this->renderForm();
            } else {
                $update = $this->processSaveCustomText();

                if (!$update) {
                    $output = '<div class="alert alert-danger conf error">'
                        .$this->trans('An error occurred on saving.', array(), 'Admin.Notifications.Error')
                        .'</div>';
                }

                $this->_clearCache($this->templateFile);
            }
        }
        if (Tools::isSubmit('pk_submit_hook')) {

            Configuration::updateValue('PK_TB_HOOK', Tools::getValue('hook'));

        }

        return $output.$this->renderForm();
    }

    public function processSaveCustomText()
    {
        $id = Tools::getValue('id_info');
        $id_shop = $this->context->shop->id;
        $sql = array();

        $current_hook = Tools::getValue('hook');
        if ($current_hook !== false) {
            $hook = $current_hook;
            Configuration::updateValue('PK_TB_HOOK', $hook);
        } else {
            $hook = Configuration::get('PK_TB_HOOK');
        }

        foreach (Language::getLanguages(false) as $lang) {
            $text = Tools::getValue('text_'.$lang['id_lang']);
            /*
            $update = $this->isRowExist('FROM `'.$this->mdb.'` WHERE name = "'.$key.'" AND id_shop = '.$sid);

            if ($update) {
                $sql[] = 'UPDATE `'.$this->mdb.'` SET value = "'.$value.'" WHERE name = "'.$key.'" AND id_shop = '.$sid.';';
            } else {
                $sql[] = 'INSERT INTO `'.$this->mdb.'` (`id_shop`, `name`, `value`) VALUES ('.$sid.', "'.$key.'", "'.$value.'");';
            }
            */

            $sql[$lang['id_lang']]['update'] = "UPDATE `".$this->db_lang."` SET text = '".$text."' WHERE id_info = ".$id." AND id_lang = ".$lang['id_lang']." AND hook = '".$hook."' AND id_shop = ".$id_shop.";";
            //$sql[$lang['id_lang']]['insert'] = "UPDATE `".$this->db_lang."` l, `".$this->db."` m SET l.text = '".$text."' WHERE l.id_info = ".$id." AND l.id_lang = ".$lang['id_lang']." AND l.hook = '".$hook."' AND m.id_shop = ".$id_shop.";";
        }
        
        foreach ($sql as $id => $query) {
            if (!Db::getInstance()->Execute($query['update'])) {
                return false;
            }
        }

        return true;
    }

    protected function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $hook_list = array();
        foreach ($this->hooks as $hook) {
            $hook_list[] = array('id' => $hook, 'name' => $hook);
        }

        $fields_form_hook = array(
            'form' => array(
                'tinymce' => false,
                'legend' => array(
                    'title' => $this->trans('Text Block Hook', array(), 'Modules.TextBlock'),
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Select hook to configure', array(), 'Modules.TextBlock.Admin'),
                        'desc' => $this->trans('Save selected hook before configuration', array(), 'Modules.TextBlock.Admin'),
                        'name' => 'hook',
                        'options' => array(
                            'query' => $hook_list,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save Hook', array(), 'Admin.Actions'),
                    'name' => 'pk_submit_hook',
                )
            )
        );
        $fields_form = array(
            'form' => array(
                'tinymce' => true,
                'legend' => array(
                    'title' => $this->trans('Text Block Content', array(), 'Modules.TextBlock'),
                ),
                'input' => array(
                    'id_info' => array(
                        'type' => 'hidden',
                        'name' => 'id_info'
                    ),
                    'content' => array(
                        'type' => 'textarea',
                        'label' => $this->trans('Content', array(), 'Modules.TextBlock'),
                        'lang' => true,
                        'name' => 'text',
                        'cols' => 40,
                        'rows' => 10,
                        'class' => 'rte',
                        'autoload_rte' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                    'name' => 'saveps_customtext',
                )
            )
        );

        if (Shop::isFeatureActive() && Tools::getValue('id_info') == false) {
            $fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso_theme'
            );
        }


        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;

        $hook = Tools::getValue('PK_TB_HOOK', Configuration::get('PK_TB_HOOK'));
        $helper->fields_value = $this->getFormValues();

        return $helper->generateForm(array($fields_form_hook, $fields_form));
    }

    public function getFormValues()
    {
        $fields_value = array();
        $id_info = 1;

        foreach (Language::getLanguages(false) as $lang) {

            $sql = 'SELECT `text` FROM `'.$this->db_lang.'` WHERE `id_shop` = '.(int)$this->context->shop->id.' AND `id_lang` ='.(int)$lang['id_lang'].'  AND `hook` = "'.Configuration::get('PK_TB_HOOK').'"';
            $response = Db::getInstance()->getRow($sql);
            $fields_value['text'][(int)$lang['id_lang']] = $response['text'];
        }

        $fields_value['hook'] = Configuration::get('PK_TB_HOOK');
        $fields_value['id_info'] = $id_info;

        return $fields_value;
    }
    /*
    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId($this->name))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId($this->name));
    }osinx0@gmail.com
    */
    public function getWidgetVariables($params = null, array $configuration = [])
    {
        $sql = 'SELECT `id_info`, `hook`, `id_shop`, `text`
            FROM `'.$this->db_lang.'`
            WHERE `id_lang` = '.(int)$this->context->language->id.' AND `id_shop` = '.(int)$this->context->shop->id.' AND  `hook` = "'.$params['hook'].'"';

        $content = Db::getInstance()->getRow($sql);
        $replace = array('[' => '<', ']' => '>');
        $content = str_replace(array_keys($replace), array_values($replace), $content);

        $smarty_opts = array(
            'text_block' => $content,
            'text_block_hook' => Configuration::get('PK_TB_HOOK')
        );
        $this->smarty->assign($smarty_opts);
    }

    public function hookdisplayHome($params) {

        $params['hook'] = 'displayHome';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getWidgetVariables($params);
            //return $this->fetch($this->templateFile, $this->getCacheId($this->name));
            return $this->fetch($this->templateFile);
        }

    }  

    public function hookdisplayFooter($params) {

        $params['hook'] = 'displayFooter';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => false));
        if ($status == true) {
            $this->getWidgetVariables($params);
            return $this->fetch($this->templateFile);
        }

    } 

    public function hookcontent_top($params) {

        $params['hook'] = 'content_top';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getWidgetVariables($params);
            return $this->fetch($this->templateFile);
        }

    }

    public function hookcontent_bottom($params) {

        $params['hook'] = 'content_bottom';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getWidgetVariables($params);
            return $this->fetch($this->templateFile);
        }

    }
    

    public function installFixtures()
    {
        $sql = $sql_lang = array();
        $path = _MODULE_DIR_.$this->name.'/images/';

        $tab_texts = array(
            1 => array(
                'content_top' =>array(
                    'text' => '<div class="first-message"><h6 style="text-align: center;">ALYSUM IS A PREMIUM ECOMMERCE THEME.</h6><p style="text-align: center;"><span>Quisque euismod pretium lacinia. Vivamus sollicitudin placerat sit amet sagittis. Mauris ac ante porta, pellentesque lacus.</span></p></div>'
                ),
                'content_bottom' =>array(
                    'text' => '<h4 class="module-title"><span>Shop in Alysum</span></h4><div class="description-block flex-container"><div class="desc-block-01"><div class="desc-item-01"><svg class="svgic svgic-rocket"><use xlink:href="#si-rocket"></use></svg><h6>FREE DELIVERY</h6><div class="desc-block-text">Cras pellentesque, nisi ac tempus pellentesque, orci sem commodo urna</div></div><div class="desc-item-02"><svg class="svgic svgic-shuffle"><use xlink:href="#si-shuffle"></use></svg><h6>FREE, EASY EXCHANGES</h6><div class="desc-block-text">Nam eget urna id tellus venenatis ullamcorper quis ut augue sagittis sed</div></div></div><div class="desc-block-02"><div class="desc-item-01"><svg class="svgic svgic-support"><use xlink:href="#si-support"></use></svg><h6>24/H CUSTOMER SERVICE</h6><div class="desc-block-text">Pellentesque habitant morbi tristue senectus et netus et malesuada fames</div></div><div class="desc-item-02"><svg class="svgic svgic-gift"><use xlink:href="#si-gift"></use></svg><h6>GIFT CARD</h6><div class="desc-block-text">Cras in semper massa, vel rutrum ligula. Aenean consectetur nisl a ante</div></div></div><div class="desc-block-02"><div class="desc-item-01"><svg class="svgic svgic-lock"><use xlink:href="#si-lock"></use></svg><h6>PAYMENT SECURED</h6><div class="desc-block-text">Sed in mi purus. Morbi augue ex, congue a augue ut, hendrerit suscipit</div></div><div class="desc-item-02"><svg class="svgic svgic-back"><use xlink:href="#si-back"></use></svg><h6>14-DAY RETURNS</h6><div class="desc-block-text">Sed in mi purus. Morbi augue ex, congue a augue ut, hendrerit suscipit</div></div></div></div>'
                ),
                'displayHome' =>array(
                    'text' => '<div class="txt-block"><div class="txt-block-01 relative"><a href="#"> <img src="'.$this->context->link->getMediaLink($path.'txt-01.jpg').'" width="100" height="100" alt="demo-image" /></a><div class="txt-block-text"><span class="txt-subtitle">EXCLUSIVE</span> <span class="txt-title">discounts<br /> & <strong>OFFERS</strong></span></div></div><div class="txt-block-02 relative"><a href="#"> <img src="'.$this->context->link->getMediaLink($path.'txt-02.jpg').'" width="100" height="100" alt="demo-image" /></a><div class="txt-block-text"><span class="txt-subtitle">SHOP NOW</span> <span class="txt-title">hottest news<br /><strong>#ALYSUM</strong></span></div></div><div class="txt-block-right"><div class="txt-block-03 relative"><a href="#"> <img src="'.$this->context->link->getMediaLink($path.'txt-03.jpg').'" width="100" height="100" alt="demo-image" /></a><div class="txt-block-text"><span class="txt-subtitle">SEE THE GREAT</span> <span class="txt-title">attention<br />to <strong>DETAILS</strong></span></div></div><div class="txt-block-04 relative"><a href="#"> <img src="'.$this->context->link->getMediaLink($path.'txt-04.jpg').'" width="100" height="100" alt="demo-image" /></a><div class="txt-block-text"><span class="txt-subtitle">DISCOVER</span> <span class="txt-title">Style that<br /><strong>INSPIRES</strong></span></div></div></div></div>'
                ),
                'displayFooter' =>array(
                    'text' => '<div class="text-block-wrap"><h4>Contact details</h4><div class="tb-sect"><div class="tb-sect-icon">[svg class="svgic"]<use xlink:href="#si-phone"></use>[/svg]</div><div class="tb-sect-text">0203 - 980 - 14 - 79<br />0203 - 478 - 12 - 96</div></div><div class="tb-sect"><div class="tb-sect-icon">[svg class="svgic"]<use xlink:href="#si-email"></use>[/svg]</div><div class="tb-sect-text">auray_shop@gmail.com<br />auray@hotmail.com</div></div><div class="tb-sect"><div class="tb-sect-icon">[svg class="svgic"]<use xlink:href="#si-skype"></use>[/svg]</div><div class="tb-sect-text">auray_shop_contact<br />auray_support</div></div></div>'
                ),
            ),
        );

        $shops_ids = Shop::getShops(true, null, true);

        foreach ($tab_texts as $id => $tab) {

            foreach (Language::getLanguages(false) as $lang) {

                foreach ($shops_ids as $id_shop) {

                    foreach ($this->hooks as $hook) {
                        $sql_lang[] = "(".$id.", ".$lang['id_lang'].", ".$id_shop.", '".$hook."', '".$tab[$hook]['text']."')"; 
                    }

                }

            }

        }
        
        $line = "INSERT INTO `".$this->db_lang."` (`id_info`, `id_lang`, `id_shop`, `hook`, `text`) VALUES ";
        foreach ($sql_lang as $key => $bit) {
            $separator = ',';
            if ($key == 0) {
                $separator = '';
            }
            $line .= $separator.$bit;
        }
        
        if (!Db::getInstance()->Execute($line))
            return false;

        return true;
    }

    public function isRowExist($sql) {

        $check_module = Db::getInstance()->ExecuteS('SELECT EXISTS(SELECT 1 '.$sql.');');
        return reset($check_module[0]);

    }

}