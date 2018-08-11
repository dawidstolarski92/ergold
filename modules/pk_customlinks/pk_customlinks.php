<?php
/*
	Module Name: pk_customlinks
	Version: 2.5
	Author: Marek Mnishek
	Author URI: http://promokit.eu
	Copyright (C) 2013 promokit.eu 
*/

if (!defined('_PS_VERSION_'))
	exit;

use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class Pk_CustomLinks extends Module {
	
	protected $error = false;
	
	public function __construct() {

		$this->name = 'pk_customlinks';
		$this->version = '2.3';
		$this->author = 'promokit.eu';
		$this->bootstrap	 = true;
		$this->need_instance = 0;
		$this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

	 	parent::__construct();

		$this->displayName = 'Custom Links';
		$this->description = $this->trans('Add a block with custom links', array(), 'Modules.CustomLinks.Admin');
		$this->confirmUninstall = $this->trans('Are you sure you want to delete all your links?', array(), 'Modules.CustomLinks.Admin');

		$this->check_state = true;
        if (Module::isInstalled('pk_themesettings')) {
            require_once _PS_MODULE_DIR_.'pk_themesettings/inc/common.php';
            $this->check_state = new Pk_ThemeSettings_Common();
        }

	}
	
	public function install() {

		if (!parent::install() ||
			!$this->registerHook('displayNav') || !$this->registerHook('displayHeader') || !$this->registerHook('displayTop') ||
			!Db::getInstance()->execute('
			CREATE TABLE '._DB_PREFIX_.'pk_customlink (
			`id_pk_customlink` int(2) NOT NULL AUTO_INCREMENT, 
			`url` varchar(255) NOT NULL,
			`new_window` TINYINT(1) NOT NULL,
			PRIMARY KEY(`id_pk_customlink`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8') ||
			!Db::getInstance()->execute('
			CREATE TABLE '._DB_PREFIX_.'pk_customlink_shop (
			`id_pk_customlink` int(2) NOT NULL AUTO_INCREMENT, 
			`id_shop` int(2) NOT NULL,
			PRIMARY KEY(`id_pk_customlink`, `id_shop`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8') ||
			!Db::getInstance()->execute('
			CREATE TABLE '._DB_PREFIX_.'pk_customlink_lang (
			`id_pk_customlink` int(2) NOT NULL,
			`id_lang` int(2) NOT NULL,
			`text` varchar(64) NOT NULL,
			PRIMARY KEY(`id_pk_customlink`, `id_lang`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8') ||
			!Configuration::updateValue('PS_CUSTOMLINK_TITLE', array('1' => 'Block link', '2' => 'Bloc lien')) ||
			!Configuration::updateValue('LINK_MYACC', 1) ||
			!Configuration::updateValue('LINK_REG', 1) ||
			!Configuration::updateValue('LINK_MYWTL', 1))
			return false;
		return true;
	}
	
	public function uninstall() {

		if (!parent::uninstall() ||
			!Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'pk_customlink') ||
			!Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'pk_customlink_lang') ||
			!Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'pk_customlink_shop') ||
			!Configuration::deleteByName('PS_CUSTOMLINK_TITLE') ||
			!Configuration::deleteByName('PS_CUSTOMLINK_URL') ||
			!Configuration::deleteByName('LINK_MYACC') ||
			!Configuration::deleteByName('LINK_REG') ||
			!Configuration::deleteByName('LINK_MYWTL'))
			return false;
		return true;

	}
	
	public function hookDisplayNav($params) {

		$params['hook'] = 'displayNav';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name));
        if ($status == true) {

			$sett = array();
			$sett["reg"] = Configuration::get('LINK_REG');
			$sett["myacc"] = Configuration::get('LINK_MYACC');
			$sett["mywtl"] = Configuration::get('LINK_MYWTL');

			$links = $this->getLinks();	
			
			$this->smarty->assign(array(
				'customlinks_links' => $links,
				'title' => Configuration::get('PS_CUSTOMLINK_TITLE', $this->context->language->id),
				'url' => Configuration::get('PS_CUSTOMLINK_URL'),
				'lang' => 'text_'.$this->context->language->id,
				'watchlist' => $this->getWatchList($params),
				'pk_voucherAllowed' => CartRule::isFeatureActive(),
				'pk_returnAllowed' => (int)(Configuration::get('PS_ORDER_RETURN')),
				'main_links' => $sett,
				'tpl' => _PS_THEME_DIR_.'templates/catalog/_partials/miniatures/mini-product.tpl'
			));

			return $this->fetch($this->templateFile);

		}

	}

	public function hookDisplayTop($params) {		

		$params['hook'] = 'displayTop';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name));
        if ($status == true) {

			$sett = array();
			$sett["reg"] = Configuration::get('LINK_REG');
			$sett["myacc"] = Configuration::get('LINK_MYACC');
			$sett["mywtl"] = Configuration::get('LINK_MYWTL');

			$links = $this->getLinks();	
			
			$this->smarty->assign(array(
				'customlinks_links' => $links,
				'title' => Configuration::get('PS_CUSTOMLINK_TITLE', $this->context->language->id),
				'url' => Configuration::get('PS_CUSTOMLINK_URL'),
				'lang' => 'text_'.$this->context->language->id,
				'watchlist' => $this->getWatchList($params),
				'pk_voucherAllowed' => CartRule::isFeatureActive(),
				'pk_returnAllowed' => (int)(Configuration::get('PS_ORDER_RETURN')),
				'main_links' => $sett,
				'tpl' => _PS_THEME_DIR_.'templates/catalog/_partials/miniatures/mini-product.tpl'
			));

			return $this->fetch($this->templateFile);
		}

	}
	
	public function hookDisplayHeader($params) {

		if (Tools::getValue('id_product')) {
			$this->addViewedProduct(Tools::getValue('id_product'));
		}

		$this->context->controller->registerStylesheet($this->name, 'modules/'.$this->name.'/assets/css/styles.css', ['media' => 'all', 'priority' => 150]);
        $this->context->controller->registerJavascript($this->name, 'modules/'.$this->name.'/assets/js/scripts.js', ['position' => 'bottom', 'priority' => 150]);

	}

	public function getLinks() {

		$result = array();
		// Get id and url

		$sql = 'SELECT b.`id_pk_customlink`, b.`url`, b.`new_window`
				FROM `'._DB_PREFIX_.'pk_customlink` b';
		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
			$sql .= ' JOIN `'._DB_PREFIX_.'pk_customlink_shop` bs ON b.`id_pk_customlink` = bs.`id_pk_customlink` AND bs.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
		$sql .= (int)Configuration::get('PS_BLOCKLINK_ORDERWAY') == 1 ? ' ORDER BY `id_pk_customlink` DESC' : '';

		if (!$links = Db::getInstance()->executeS($sql))
			return false;
		$i = 0;
		foreach ($links as $link)
		{
			$result[$i]['id'] = $link['id_pk_customlink'];
			$result[$i]['url'] = $link['url'];
			$result[$i]['newWindow'] = $link['new_window'];
			// Get multilingual text
			if (!$texts = Db::getInstance()->executeS('SELECT `id_lang`, `text` FROM '._DB_PREFIX_.'pk_customlink_lang	WHERE `id_pk_customlink`='.(int)$link['id_pk_customlink']))
				return false;
			foreach ($texts as $text)
				$result[$i]['text_'.$text['id_lang']] = $text['text'];
			$i++;
		}
		return $result;
	}
	
	public function addLink() {

		if (!($languages = Language::getLanguages()))
			 return false;

		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

		if ($id_link = Tools::getValue('id_link')) {

			if (!Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'pk_customlink SET `url` = \''.pSQL($_POST['url']).'\', `new_window` = '.(isset($_POST['newWindow']) ? 1 : 0).' WHERE `id_pk_customlink` = '.(int)$id_link))
				return false;
			if (!Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'pk_customlink_lang WHERE `id_pk_customlink` = '.(int)$id_link))
				return false;
				
			foreach ($languages as $language) {

				if (!empty($_POST['text_'.$language['id_lang']])) {

					if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pk_customlink_lang VALUES ('.(int)$id_link.', '.(int)($language['id_lang']).', \''.pSQL($_POST['text_'.$language['id_lang']]).'\')'))
						return false;
		 	 	}

				else {
					if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pk_customlink_lang VALUES ('.(int)$id_link.', '.$language['id_lang'].', \''.pSQL($_POST['text_'.$id_lang_default]).'\')'))
						return false;
				}

			}

		} else {

			if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pk_customlink VALUES (NULL, \''.pSQL($_POST['url']).'\', '.((isset($_POST['newWindow']) && $_POST['newWindow']) == 'on' ? 1 : 0).')') || !$id_link = Db::getInstance()->Insert_ID())
				return false;

			foreach ($languages as $language) {

				if (!empty($_POST['text_'.$language['id_lang']])) {
					if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pk_customlink_lang 
																VALUES ('.(int)$id_link.', '.(int)$language['id_lang'].', \''.pSQL($_POST['text_'.$language['id_lang']]).'\')'))
						return false;
				} else {
					if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'pk_customlink_lang VALUES ('.(int)$id_link.', '.(int)($language['id_lang']).', \''.pSQL($_POST['text_'.$id_lang_default]).'\')'))
						return false;
				}
			}
		}

		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'pk_customlink_shop WHERE id_pk_customlink='.(int)$id_link);

		if (!Shop::isFeatureActive()) {

			Db::getInstance()->insert('pk_customlink_shop', array(
				'id_pk_customlink' => (int)$id_link,
				'id_shop' => (int)Context::getContext()->shop->id,
			));

		} else{

			$assos_shop = Tools::getValue('checkBoxShopAsso_blocklink');
			if (empty($assos_shop))
				return false;
			foreach ($assos_shop as $id_shop => $row)
				Db::getInstance()->insert('pk_customlink_shop', array(
					'id_pk_customlink' => (int)$id_link,
					'id_shop' => (int)$id_shop,
				));
		}
		return true;
	}

	public function deleteLink() {

		return (Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'pk_customlink WHERE `id_pk_customlink` = '.(int)$_GET['id']) &&
				Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'pk_customlink_shop WHERE `id_pk_customlink` = '.(int)$_GET['id']) &&
				Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'pk_customlink_lang WHERE `id_pk_customlink` = '.(int)$_GET['id']));
	}

	public function getContent() {
		$this->_html = '';

		// Add a link
		if (isset($_POST['submitLinkAdd'])) {

			if (empty($_POST['text_'.Configuration::get('PS_LANG_DEFAULT')]) || empty($_POST['url']))
				$this->_html .= $this->displayError($this->trans('You must fill in all fields', array(), 'Admin.Notifications.Error'));
			elseif (!Validate::isUrl(str_replace('http://', '', $_POST['url'])))
				$this->_html .= $this->displayError($this->trans('Bad URL', array(), 'Admin.Notifications.Error'));
			else {
				if ($this->addLink())
	     	  		$this->_html .= $this->displayConfirmation($this->trans('The link has been added', array(), 'Admin.Notifications.Success'));
				else
					$this->_html .= $this->displayError($this->trans('An error occurred during link creation', array(), 'Admin.Notifications.Error'));
			}
			$this->_clearCache($this->templateFile);
     	}
		
		// Delete a link
		elseif (Tools::getValue('delete_link') && isset($_GET['id'])) {

			if (!is_numeric($_GET['id']) || !$this->deleteLink())
			 	$this->_html .= $this->displayError($this->trans('An error occurred during link deletion', array(), 'Admin.Notifications.Error'));
			else
			 	$this->_html .= $this->displayConfirmation($this->trans('The link has been deleted', array(), 'Admin.Notifications.Success'));
		}

		if (isset($_POST['submitOrderWay'])) {
			if (
				Configuration::updateValue('PS_BLOCKLINK_ORDERWAY', (int)(Tools::getValue('orderWay'))) &&
				Configuration::updateValue('LINK_MYACC', (int)(Tools::getValue('link_myacc'))) &&
				Configuration::updateValue('LINK_REG', (int)(Tools::getValue('link_reg'))) &&
				Configuration::updateValue('LINK_MYWTL', (int)(Tools::getValue('link_mywtl')))
				)
				$this->_html .= $this->displayConfirmation($this->trans('Settings updated', array(), 'Admin.Notifications.Success'));
			else
				$this->_html .= $this->displayError($this->trans('An error occurred during settings set-up', array(), 'Admin.Notifications.Error'));
		}

		$this->_displayForm();
		$this->_list();

		return $this->_html;
	}
	
	private function _displayForm() {

	 	/* Language */
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		$languages = Language::getLanguages(false);
		$divLangName = 'text¤title';
		/* Title */
		$title_url = Configuration::get('PS_CUSTOMLINK_URL');
		if (!Tools::isSubmit('submitLinkAdd')) {

			if ($id_link = (int)Tools::getValue('id_link')) {

				$res = Db::getInstance()->executeS('
				SELECT *
				FROM '._DB_PREFIX_.'pk_customlink b
				LEFT JOIN '._DB_PREFIX_.'pk_customlink_lang bl ON (b.id_pk_customlink = bl.id_pk_customlink)
				WHERE b.id_pk_customlink='.(int)$id_link);
				if ($res)
					foreach ($res as $row)
					{
						$links['text'][(int)$row['id_lang']] = $row['text'];
						$links['url'] = $row['url'];
						$links['new_window'] = $row['new_window'];
					}
			}
			$this->_clearCache($this->templateFile);
		}
		$this->_html .= '
		<script type="text/javascript">
			id_language = Number('.(int)$id_lang_default.');
		</script>
		<style>
			#languages_text br {display: none}
			.displayed_flag, #languages_text {float:left; vertical-align:top; margin-top:3px}
			#languages_text {display: none}
			.tree-folder label {width:auto}
		</style>
		<fieldset>
			<form method="post" action="index.php?controller=adminmodules&configure='.Tools::safeOutput(Tools::getValue('configure')).'&token='.Tools::safeOutput(Tools::getValue('token')).'&tab_module='.Tools::safeOutput(Tools::getValue('tab_module')).'&module_name='.Tools::safeOutput(Tools::getValue('module_name')).'" class="defaultForm form-horizontal">
				<div class="panel" id="fieldset_0">
					<div class="panel-heading"><i class="icon-cogs"></i> '.$this->trans('Add a new link', array(), 'Modules.CustomLinks.Admin').'</div>
					<div class="form-wrapper">
						<input type="hidden" name="id_link" value="'.(int)Tools::getValue('id_link').'" />

						<div class="form-group">
							<label class="control-label col-lg-3">'.$this->trans('Add a new link', array(), 'Modules.CustomLinks.Admin').'</label>
							<div class="col-lg-3">&nbsp;&nbsp;';
							foreach ($languages as $language)
								$this->_html .= '
									<div id="text_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $id_lang_default ? 'block' : 'none').'; float: left;">
										<input type="text" name="text_'.$language['id_lang'].'" id="textInput_'.$language['id_lang'].'" value="'.((isset($links) && isset($links['text'][$language['id_lang']])) ? $links['text'][$language['id_lang']] : '').'" />
									</div>';
								$this->_html .= $this->displayFlags($languages, $id_lang_default, $divLangName, 'text', true);
								$this->_html .= '
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">URL:</label>
							<div class="col-lg-3">
							<input type="text" name="url" id="url" value="'.(isset($links) && isset($links['url']) ? Tools::safeOutput($links['url']) : '').'" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">'.$this->trans('Open in a new window', array(), 'Modules.CustomLinks.Admin').'</label>
							<div class="col-lg-3">
							<input type="checkbox" name="newWindow" id="newWindow" '.((isset($links) && $links['new_window']) ? 'checked="checked"' : '').' />
							</div>
						</div>';
						$shops = Shop::getShops(true, null, true);
						if (Shop::isFeatureActive() && count($shops) > 1) {
							$helper = new HelperForm();
							$helper->id = (int)Tools::getValue('id_link');
							$helper->table = 'blocklink';
							$helper->identifier = 'id_pk_customlink';
				
							$this->_html .= '<label for="shop_association">'.$this->trans('Shop association', array(), 'Modules.CustomLinks.Admin').'</label><div id="shop_association" class="margin-form">'.$helper->renderAssoShop().'</div>';
						}
						$this->_html .= '
						<div class="panel-footer">
							<input type="submit" class="btn btn-default pull-right" name="submitLinkAdd" value="'.$this->trans('Add link', array(), 'Modules.CustomLinks.Admin').'" />
						</div>
					</div>
				</div>
			</form>
		<fieldset class="space">
			<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" class="defaultForm form-horizontal">

				<div class="panel" id="fieldset_0">
					<div class="panel-heading"><i class="icon-cogs"></i> '.$this->trans('Settings', array(), 'Modules.blocklinkfooter').'</div>
					<div class="form-wrapper">
						<div class="form-group">
							<label class="control-label col-lg-3">'.$this->trans('Login/Register', array(), 'Modules.CustomLinks.Admin').'</label>
							<div class="col-lg-3">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="link_reg" id="link_reg_on" value="1" '.(Tools::getValue('link_reg', Configuration::get('LINK_REG')) ? 'checked="checked" ' : '').'/>
									<label for="link_reg_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
									<input type="radio" name="link_reg" id="link_reg_off" value="0" '.(!Tools::getValue('link_reg', Configuration::get('LINK_REG')) ? 'checked="checked" ' : '').'/>
									<label for="link_reg_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">'.$this->trans('My Account', array(), 'Modules.CustomLinks.Admin').'</label>
							<div class="col-lg-3">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="link_myacc" id="link_myacc_on" value="1" '.(Tools::getValue('link_myacc', Configuration::get('LINK_MYACC')) ? 'checked="checked" ' : '').'/>
									<label for="link_myacc_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
									<input type="radio" name="link_myacc" id="link_myacc_off" value="0" '.(!Tools::getValue('link_myacc', Configuration::get('LINK_MYACC')) ? 'checked="checked" ' : '').'/>
									<label for="link_myacc_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">'.$this->trans('Watch List', array(), 'Modules.CustomLinks.Admin').'</label>
							<div class="col-lg-3">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" name="link_mywtl" id="link_mywtl_on" value="1" '.(Tools::getValue('link_mywtl', Configuration::get('LINK_MYWTL')) ? 'checked="checked" ' : '').'/>
								<label for="link_mywtl_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
								<input type="radio" name="link_mywtl" id="link_mywtl_off" value="0" '.(!Tools::getValue('link_mywtl', Configuration::get('LINK_MYWTL')) ? 'checked="checked" ' : '').'/>
								<label for="link_mywtl_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
								<a class="slide-button btn"></a>
							</span>
							</div>
						</div>
						<div class="panel-footer">
							<input type="submit" class="btn btn-default pull-right" name="submitOrderWay" value="'.$this->trans('Update', array(), 'Admin.Global').'" />
						</div>
					</div>
				</div>
			</form>
		</fieldset>';
	}
	
	private function _list() {
		$links = $this->getLinks();
		$languages = Language::getLanguages();
		$token = Tools::safeOutput(Tools::getValue('token'));
		if (!Validate::isCleanHtml($token))
			$token = '';
		if ($links) {

			$this->_html .= '
			<script type="text/javascript">
				var currentUrl = \''.Tools::safeOutput($_SERVER['REQUEST_URI']).'\';
				var token=\''.$token.'\';
				var links = new Array();';
			foreach ($links as $link) {

				$this->_html .= 'links['.$link['id'].'] = new Array(\''.addslashes($link['url']).'\', '.$link['newWindow'];
				foreach ($languages as $language)
					if (isset($link['text_'.$language['id_lang']]))
						$this->_html .= ', \''.addslashes($link['text_'.$language['id_lang']]).'\'';
					else
						$this->_html .= ', \'\'';
				$this->_html .= ');';

	 		}
			$this->_html .= '</script>';
	 	}
		$this->_html .= '
		<div class="defaultForm form-horizontal">
			<div class="panel" id="fieldset_0">
				<div class="panel-heading"><i class="icon-cogs"></i> '.$this->trans('Link list', array(), 'Modules.blocklinkfooter').'</div>
				<div class="form-wrapper">
					<div class="form-group">
						<table class="table">
							<tr>
								<th>ID</th>
								<th>'.$this->trans('Text', array(), 'Modules.CustomLinks.Admin').'</th>
								<th>URL</th>
								<th>'.$this->trans('Actions', array(), 'Modules.CustomLinks.Admin').'</th>
							</tr>';
							
						if (!$links) {
							$this->_html .= '
							<tr>
								<td colspan="3">'.$this->trans('There are no links', array(), 'Modules.CustomLinks.Admin').'</td>
							</tr>';

						} else {

							foreach ($links as $link)
								$this->_html .= '
								<tr>
									<td>'.(int)$link['id'].'</td>
									<td>'.Tools::safeOutput($link['text_'.$this->context->language->id]).'</td>
									<td>
										<a href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&id_link='.(int)$link['id'].'"><img src="../img/admin/edit.gif" alt="" title="" style="cursor: pointer" /></a>
										<a href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&id='.(int)$link['id'].'&delete_link=1"><img src="../img/admin/delete.gif" alt="" title="" style="cursor: pointer" /></a>
									</td>
								</tr>';
						}

						$i = 0;
						$nb = count($languages);
						$idLng = 0;

						while ($i < $nb) {

							if ($languages[$i]['id_lang'] == (int)Configuration::get('PS_LANG_DEFAULT'))
								$idLng = $i;
							$i++;

						}
						$this->_html .= '
						</table>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" id="languageFirst" value="'.(int)$languages[0]['id_lang'].'" />
		<input type="hidden" id="languageNb" value="'.count($languages).'" />';
	}

	public function getWatchList($params) {

		$products = array();
		$assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(new ImageRetriever($this->context->link), $this->context->link, new PriceFormatter(), new ProductColorsRetriever(), $this->context->getTranslator());
		$productsViewed = (isset($params['cookie']->viewed) && !empty($params['cookie']->viewed)) ? array_slice(explode(',', $params['cookie']->viewed), 0, 4) : array();
		
		if (!empty($productsViewed)) {
			foreach ($productsViewed as $product_id) {
				$product_obj = new Product($product_id, false, $this->context->language->id);
				$products[$product_id] = (array)$product_obj;
				$products[$product_id]['id_product'] = $product_id;
				$products[$product_id]['light_list'] = true;
				$products[$product_id] = $presenter->present($presentationSettings, $assembler->assembleProduct($products[$product_id]), $this->context->language);

			}
		}

		return $products;
	}

	public function addViewedProduct($idProduct)
    {
        $arr = array();

        if (isset($this->context->cookie->viewed)) {
            $arr = explode(',', $this->context->cookie->viewed);
        }

        if (!in_array($idProduct, $arr)) {
            $arr[] = $idProduct;

            $this->context->cookie->viewed = trim(implode(',', $arr), ',');
        }
    }

}
