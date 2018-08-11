<?php
/**
 * $ModDesc
 * 
 * @version		1.7.0.6
 * @package		modules
 * @copyright	Copyright (C) February 2013 http://promokit.eu <@email:support@promokit.eu>. All rights reserved.
 * @license		GNU General Public License version 2
 */
if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'pk_themesettings/inc/config.php');
include_once(_PS_MODULE_DIR_.'pk_themesettings/inc/confighelper.php');
include_once(_PS_MODULE_DIR_.'pk_themesettings/inc/fontlist.php');
include_once(_PS_ROOT_DIR_.'/src/Core/Addon/Theme/ThemeManager.php');

use Shudrum\Component\ArrayFinder\ArrayFinder;
use PrestaShop\PrestaShop\Core\Addon\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;

class Pk_ThemeSettings extends Module {

	public $pattern = '/^([A-Z_]*)[0-9]+/';
	public $page_name = '';

	public function __construct() {

		$this->displayName = 'Theme Settings';
		$this->description = $this->trans('Add extended theme settings', array(), 'Modules.ThemeSettings.Admin');
		$this->version = "1.7.0.6";
		$this->theme_name = "Alysum";
		$this->theme_version = "5.0.6";
		$this->versions = 'TS v.'.$this->version.' | '.$this->theme_name.' v.'.$this->theme_version.' | PS v.'._PS_VERSION_;		
		$this->name = 'pk_themesettings';		
		$this->author = 'promokit.eu';
		$this->need_instance = 0;
		$this->bootstrap = true;	

		parent::__construct();
		$this->default_config = $this->local_path.'presets/alysum.json';
		$this->customer_config = $this->local_path.'presets/customer_config.json';

		$this->patternsQuantity = 24;
		$this->mdb = _DB_PREFIX_.'pk_theme_settings';
		$this->hdb = _DB_PREFIX_.'pk_theme_settings_hooks';

		$this->errors = "";
		$this->customcssFile = $this->local_path."assets/css/dynamic/customercss".(int)Context::getContext()->shop->id.".css";
		$this->generatedFile = $this->local_path."assets/css/dynamic/generatedcss".(int)Context::getContext()->shop->id.".css";		

		$this->template['main'] = 'module:'.$this->name.'/views/admin/main.tpl';

		Configuration::updateValue('ALYSUM_VER', $this->theme_version);

	}

	public function install() {
			
		$tablesfile = 'sql/install.sql';
		$msg = '<div class="conf confirm">'.$this->l('Demo Data Installed Successfully').'</div>';
		if (!file_exists(dirname(__FILE__).'/'.$tablesfile)) 
			$msg = '<div class="conf error">'.$this->l('There is no sql file.').'</div>';
		else if (!$sql = file_get_contents(dirname(__FILE__).'/'.$tablesfile)) {
			$msg = '<div class="conf error">'.$this->l('There is no sql code.').'</div>';
		} else {
			$queries = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
			$queries = preg_split("/;\s*[\r\n]+/", $queries);
			foreach ($queries AS $query)
				if($query)
					if(!Db::getInstance()->execute(trim($query)))
						$msg = '<div class="conf error">'.$this->l('Error in SQL syntax of Tables').'</div>';

		}

		if (parent::install() && 
			$this->registerHook('displayHeader') &&
			$this->registerHook('displayFooterBefore') &&
			//$this->registerHook('displayAdminProductsExtra') &&
			//$this->registerHook('actionProductUpdate') &&
            $this->registerHook('productTab') &&
            $this->registerHook('productTabContent') &&
            $this->registerHook('comingsoon') &&
            $this->registerHook('footer_top') &&            
            $this->registerHook('footer_bottom') &&
            $this->registerHook('displayBeforeBodyClosingTag') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->installDB()) {
        	$this->installQuickAccess();
        	$this->savePosition(1);
			return true;	
		} else {			
			$this->uninstall();
			return false;		
		}
	}

	public function uninstall() {

        $sql = array();
        $sql[] = 'DELETE FROM `'._DB_PREFIX_.'quick_access` WHERE link = "index.php?controller=AdminModules&configure=pk_themesettings&tab_module=front_office_features&module_name=pk_themesettings"';
        $sql[] = 'DELETE FROM `'._DB_PREFIX_.'quick_access_lang` WHERE name = "'.$this->theme_name.' Settings"';
		$sql[] = 'DROP TABLE IF EXISTS `'.$this->mdb.'`';
		$sql[] = 'DROP TABLE IF EXISTS `'.$this->hdb.'`';
        
        if (!parent::uninstall() OR !$this->runSql($sql)) {
            return false;
        }
        return true;  

    }

    public function installDB() {

    	$sid = (int)Shop::getContextShopID();
    	$start = 'INSERT INTO `'.$this->mdb.'` (`id_shop`, `name`, `value`) VALUES ';
    	$start_hook = 'INSERT INTO `'.$this->hdb.'` (`id_shop`, `hook`, `module`, `ordr`, `value`) VALUES ';

		$string = file_get_contents($this->default_config);
		$json_arr = json_decode($string, true);
		if ($json_arr === null) {
			return $this->displayError($this->l('Config file has syntax error'));
		}

		$sqlPart = $sqlHookPart = '';
		foreach ($json_arr as $name => $value) {
			if ($name == "modules") {
				foreach ($value as $hook => $modules) {
					foreach ($modules as $module => $val) {
						$vals = explode(".", $val);
					    $sqlHookPart .= '('.$sid.', "'.$hook.'", "'.$module.'", "'.$vals[0].'", "'.$vals[1].'"),';

					    if (($id_hook = Hook::getIdByName($hook)) !== false) {
							$mInstance = Module::getInstanceByName($module);
							if (Validate::isLoadedObject($mInstance)) {
								$position = $mInstance->getPosition($id_hook);
								$way = (($vals[0] >= $position) ? 1 : 0);

								if ($position) {
									$res = $mInstance->updatePosition($id_hook, $way, $vals[0]);
								}

								if ($vals[1] == 0) {
									$mInstance->disable();
								} else {
									$mInstance->enable();
								}
							}
						}
					}
				}

			} else {

				if (is_array($value)) {
					$line = "{";
					foreach ($value as $k => $v) {
						$line .= "&quot;".$k."&quot;:&quot;".$v."&quot;,";
					}
					$line .= "}";
					$value = str_replace(',}', '}', $line);
				}
				$sqlPart .= '('.$sid.', "'.$name.'", "'.$value.'"),';
			}
		}

		$sqlPart = substr($sqlPart, 0, -1);
		$sqlHookPart = substr($sqlHookPart, 0, -1);
		$sql[] = $start.$sqlPart.";";
		$sql[] = $start_hook.$sqlHookPart.";";

		if (!$this->runSql($sql))
			return false;

		$this->saveAll();
		return true;

    		
    }

    public function updateDBsettigs($new_config = false) {
    	// $new_config - used when you need to change preset 
    	
    	$config_file = $this->default_config;
    	if ($new_config != false)
    		$config_file = $this->local_path.'presets/'.$new_config.'.json';

    	$sql = array();
    	$sid = (int)Shop::getContextShopID();    	    	
		$string = file_get_contents($config_file);

		//$json_arr = json_decode(html_entity_decode($string), true);
		$json_arr = json_decode($string, true);
		if($json_arr === null)
			return '<div class="conf error">'.$this->l('Config syntax error').'</div>';
		
		$s = $this->getOptions("updateDBsettigs");
    	$s["modules"] = $this->getModulesState();

		$sqlPart = $sqlHookPart = '';

		foreach ($json_arr as $name => $value) {	

			if ($name == 'modules')	{

				if (!empty($value))	{				
					foreach ($value as $hook => $modules) {
						foreach ($modules as $module => $val) {
							$vals = explode(".", $val);

							//$sqlHookPart .= "UPDATE `".$this->hdb."` SET `value` = '".$vals[1]."', `ordr` = '".$vals[0]."' WHERE `hook` = '".$hook."' AND `module` = '".$module."' AND `id_shop` = '".$sid."';";
							$update = $this->isRowExist('FROM `'.$this->hdb.'` WHERE `hook`="'.$hook.'" AND `module`="'.$module.'" AND `id_shop` = '.$sid);

							if ($update) {
								$sqlHookPart .= "UPDATE `".$this->hdb."` SET `value` = '".$vals[1]."', `ordr` = '".$vals[0]."' WHERE `hook` = '".$hook."' AND `module` = '".$module."' AND `id_shop` = '".$sid."';";
							} else {
								$sqlHookPart = 'INSERT INTO `'.$this->hdb.'` (`id_shop`, `hook`, `module`, `ordr`, `value`) VALUES ('.$sid.', "'.$hook.'", "'.$module.'", "'.$vals[0].'", "'.$vals[1].'");';
							}
							

						}
					}
				}

			} else {

				if (is_array($value)) {
					$value = json_encode($value);
				}
				$sqlPart .= "UPDATE `".$this->mdb."` SET `value` = '".$value."' WHERE `name` = '".$name."' AND `id_shop` = '".$sid."';";
				/*
				$update = $this->isRowExist('FROM `'.$this->mdb.'` WHERE value = "'.$value.'" AND name = '.$name.' AND id_shop = '.$sid);

				if ($update) {
					$sqlPart .= "UPDATE `".$this->mdb."` SET `value` = '".$value."' WHERE `name` = '".$name."' AND `id_shop` = '".$sid."';";
				} else {
					$sqlPart = 'INSERT INTO `'.$this->mdb.'` (`id_shop`, `name`, `value`) VALUES ('.$sid.', "'.$name.'", "'.$value.'");';
				}
				*/

			}

		}

		$sql[] = $sqlPart;
		$sql[] = $sqlHookPart;				

		$this->runSql($sql);
    
		return;

	}

	public function runSql($sql) {
		//Db::getInstance()->query("FLUSH QUERY CACHE");
        foreach ($sql as $s) {
        	if (!empty($s)) {
				if (!$resp = Db::getInstance()->Execute($s)) {
					return false;
				}
			}
        }
        return true;
    }

    public function installDemo($preset)
    {
   		include_once(_PS_MODULE_DIR_.$this->name.'/inc/import/InstallDemoData.php');
   		$import = new InstallDemoData();
   		$response = $import->start($preset);
   		return $response;
    }

	public function getContent() {	

		$this->injectDisplayBackOfficeHeader();			

	    $s = $this->getOptions("getContent");
	    $sid = $this->context->shop->id;
	
		$msg = '';

		if (Tools::isSubmit('dc_preset_to_import_submit')) {

			$msg .= $this->installDemo(Tools::getValue("dc_preset_to_import"));

		}

		if (Tools::isSubmit('submitDeleteImgConf')) {
			$msg .= $this->deleteImg($s["back_image"], "back_image", (int)(Tools::getValue("tab_number")), $sid);
		}


		if (Tools::isSubmit('submitDeleteEmailImg')) {
			$msg .= $this->deleteImg($s["email_image"], "email_image", (int)(Tools::getValue("tab_number")), $sid);
		}


		if (Tools::isSubmit('removePreset')) {

			if (isset($_POST['removePreset'])) {
				$preset = $this->local_path.'presets/'.$_POST['removePreset'].'.json';
				$this->deleteFile($preset);
			}

		}

		if (Tools::isSubmit('savePreset'))	{

			//$s = $this->getOptions();
			$s['preset'] = 'customer_config';
			$s['modules'] = $this->getModulesState();
			$json = json_encode($s);

			$this->savePreset($this->customer_config, $json);

		}
		if (Tools::isSubmit('resetThemeSettings'))	{	

			if (!$this->updateDBsettigs()) {

				$msg .= $this->displayError($this->l('Can not write to DB'));

			} else {

				$this->saveAll();
				$msg .= $this->displayConfirmation($this->l('Settings reseted'));

			}

		}			
		
		if (Tools::isSubmit('back_image_upload')) {	

			$img = $this->addImage($_FILES, "back_image", $sid, $this->context->language->id, (int)(Tools::getValue('tab_number')));				
			$msg .= $img["error"].'<div class="conf confirm">'.$this->l('Settings updated').'</div>';	

		}

		if (Tools::isSubmit('savePresetToFile')) {

			$json = Tools::getValue("preset_to_import");
			$config = json_decode($json);
			if (isset($config->preset)) {
				$this->savePreset($this->local_path.'presets/'.$config->preset.'.json', $json);
			} else {
				$msg .= $this->displayError($this->l('There is an error in preset'));
			}

		}

		if (Tools::isSubmit('submitThemeSettings')) {

			unset($_POST['submitThemeSettings']);
			unset($_POST['savePreset']);
			unset($_POST['removePreset']);
			unset($_POST['importPreset']);
			unset($_POST['savePresetToFile']); // exclude submit button values

			$optionsToSave = $_POST;
			$sql = array();

			if (isset($optionsToSave['preset'])) {
				if ($optionsToSave['preset'] != $s['preset']) {

					$msg .= $this->updateDBsettigs($optionsToSave['preset']); // update preset	
					$optionsToSave = $this->getOptions("getContent.new_preset");

				} 
			}

			foreach ($optionsToSave as $key => $value) {	
				
				if (($key != "mt_maintenance") && ($key != "customer_css") && ($key != "modules") && ($key != "ordr")) {
					if (is_array($value)) {
						$value = htmlspecialchars(json_encode($value), ENT_COMPAT,'UTF-8', true);
					}
					$value = str_replace('"', '\'', $value);

					$update = $this->isRowExist('FROM `'.$this->mdb.'` WHERE name = "'.$key.'" AND id_shop = '.$sid);

					if ($update) {
						$sql[] = 'UPDATE `'.$this->mdb.'` SET value = "'.$value.'" WHERE name = "'.$key.'" AND id_shop = '.$sid.';';
					} else {
						$sql[] = 'INSERT INTO `'.$this->mdb.'` (`id_shop`, `name`, `value`) VALUES ('.$sid.', "'.$key.'", "'.$value.'");';
					}

				} elseif ($key == "mt_maintenance") {

					Configuration::updateValue('PS_SHOP_ENABLE', $value);						

				} elseif ($key == "customer_css") {

					$this->cssWriter($value);

				} elseif ($key == "modules") {

					foreach ($value as $hook => $modules) {
						foreach ($modules as $module => $val) {

							if ($this->isPkModules($module)) {
								$update = $this->isRowExist('FROM `'.$this->hdb.'` WHERE hook = "'.$hook.'" AND module = "'.$module.'" AND id_shop = '.$sid);
							} else {
								$update = true;
							}
							
							if ($update) {
								$sql[] = 'UPDATE `'.$this->hdb.'` SET `ordr` = "'.(int)$optionsToSave["ordr"][$hook][$module].'", value = '.(int)$val.' WHERE hook = "'.$hook.'" AND module = "'.$module.'" AND id_shop = '.$sid.';';
							} else {
								$sql[] = 'INSERT INTO `'.$this->hdb.'` (`id_shop`, `hook`, `module`, `ordr`, `value`) VALUES ('.$sid.', "'.$hook.'", "'.$module.'", "'.(int)$optionsToSave["ordr"][$hook][$module].'", "'.(int)$val.'");';
							}
							
						}
					}

				}
/*
				if (($key == "ready_year") || ($key == "ready_month") || ($key == "ready_day") || ($key == "ready_hour") || ($key == "ready_min")) {

					if ($s[$key] != $value) {
						$sql[] = 'UPDATE `'.$this->mdb.'` SET value = "'.round(microtime(true)).'" WHERE name = "date_set" AND id_shop = '.$sid.';';
					}

				}
*/

			}

			$this->runSql($sql);		
			$this->saveAll();

			if ($this->errors) {
				$errors = $this->displayError($this->errors); 
			} else {
				$errors = '';	
			} 
			
			$msg .= $errors.$this->displayConfirmation($this->l('Settings updated'));	

		}

		if (Tools::isSubmit('theme_update')) {
			$list = Tools::getValue("versions");
			$versions = explode(",", $list);
			foreach ($versions as $version) {
				$msg .= $this->themeUpdate($version);
			}
		}

		if (Tools::isSubmit('mt_sendnotification')) {
			
			$msg .= $this->sendNotification();
			
		}

		return $this->displayForm($msg);
		
	}

	public function savePosition($num) {

		$sql = array('UPDATE `'.$this->mdb.'` SET value = "'.$num.'" WHERE name = "tab_number" AND id_shop = '.(int)Context::getContext()->shop->id.';');
		$this->runSql($sql);

	}

	public function savePreset($file, $data) {
		
		$fp = fopen($file, 'w');

		if (fwrite($fp, $data) === FALSE) {
			return $this->trans('Can\'t save preset', array(), 'Modules.Pk_ThemeSettings.Admin');
		}

		fclose($fp);
		return true;

	}

	public function deleteFile($file) {

		if (file_exists($file)) {
	        return unlink($file);
	    }

	}

	public function saveAll() {

		$freshDB = $this->getOptions("saveAll");

		$this->updateModulesState();
		$this->saveTheme($freshDB['homepage_layout']); // homepage layout
		$this->savePosition((int)(Tools::getValue('tab_number'))); // save back office active tab
		$this->cssWriter();
		
	}

	public function isRowExist($sql) {

		$check_module = Db::getInstance()->ExecuteS('SELECT EXISTS(SELECT 1 '.$sql.');');
		return reset($check_module[0]);

	}

	public function saveTheme($theme_details) {
		
		$theme = $this->context->shop->theme;
		$theme_repository = (new ThemeManagerBuilder($this->context, Db::getInstance()))->buildRepository();
		$themeInstance = $theme_repository->getInstanceByName($theme->getName());
		
		$assignedLayouts = $themeInstance->getPageLayouts();
		$assignedLayouts['index'] = $theme_details;
		$themeInstance->setPageLayouts($assignedLayouts);

		$theme_manager = (new ThemeManagerBuilder($this->context, Db::getInstance()))->build();
		$theme_manager->saveTheme($themeInstance);

	}

	public function sendNotification() {
		
		$readyEmails = $this->getEmails();
		$sid = $this->context->shop->id;
		$lid = $this->context->language->id;

		if (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL', null, null, $sid)))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL', null, null, $sid);
		else {
			if (file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $sid)))
				$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $sid);
			else
				$vars['{shop_logo}'] = '';
		}
		ShopUrl::cacheMainDomainForShop($sid);
		/* don't attach the logo as */

		if (isset($logo))
			$vars['{shop_logo}'] = ImageManager::getMimeTypeByExtension($logo);
		$vars['{email_menu_item}'] = 'color:#ffffff; font-size:15px; line-height:46px; mso-line-height-rule:exactly; font-family: \'Times new roman\'; text-transform:uppercase; text-decoration:none;';
		$vars["{shop_name}"] = Configuration::get('PS_SHOP_NAME');
		$vars["{shop_url}"] = Context::getContext()->link->getPageLink('index', true, $lid);

		$response = Mail::Send(
			$lid,
			'opening',
			Mail::l('Shop Opening', $lid),
			$vars,
			$readyEmails,
			null,
			null,
			null,
			null,
			null,
			_PS_MODULE_DIR_.$this->name."/mails/",
			false,
			$sid
		);

		if ($response == 0) {
			$msg = $this->displayError("Email has not been sent");
		} else {
			$msg = $this->displayConfirmation($response);
		}
		return $msg;

	}

	public function getEmails() {

		$file = _PS_MODULE_DIR_.$this->name."/maintenance/emails.txt";

		if (!$fileHolder = @fopen($file, 'r')) {
			$readyEmails = "Cant open storage file";
		} else {	
			$readyEmails = "";
			$filecontents = file_get_contents($file);
			$emails = explode(";", $filecontents);
			foreach ($emails as $email)
				if (Validate::isEmail($email))
					$filtered[] = $email;

			if (!empty($filtered))
				$readyEmails = array_unique($filtered);
			fclose($fileHolder);
		}
		return $readyEmails;

	}

	public function updateModulesState() {
		$mState = $this->getModulesState();
		$skip = array("pk_themesettings", "ph_simpleblog", "ph_recentposts");
		$disabledList = $enabledList = array();
		// move module to necessary hooks if it is not there
		foreach ($mState as $hook => $modules)
			foreach ($modules as $module => $state) {
				$val = explode(".", $state);
				if ($val[1] == 1) { // if module is enabled
					if (($id_hook = Hook::getIdByName($hook)) !== false) {
						$mInstance = Module::getInstanceByName($module);
						if (Validate::isLoadedObject($mInstance)) {
							$position = $mInstance->getPosition($id_hook);
							if (!$position) // if module is not in our hook
								if ($mInstance->isHookableOn($hook))
									$mInstance->registerHook($hook);
						}
					}
				}
			}

		// order modules like in config
		foreach ($mState as $hook => $modules)
			foreach ($modules as $module => $state) {
				$val = explode(".", $state);
				if ($val[1] == 1) {
					if (($id_hook = Hook::getIdByName($hook)) !== false) {
						$mInstance = Module::getInstanceByName($module);
						if (Validate::isLoadedObject($mInstance)) {
							$position = $mInstance->getPosition($id_hook);
							$way = (($val[0] >= $position) ? 1 : 0);
							if ($position)
								$res = $mInstance->updatePosition($id_hook, $way, $val[0]);
						}
					}
				}
				if ($val[1] == 0)
					$disabledList[$module] = $val[1];
				else
					$enabledList[$module] = $val[1];
			}

		foreach ($disabledList as $name => $state)
			if (array_key_exists($name, $enabledList))
				unset($disabledList[$name]);
		/* disable module in case it is not enabled in any hook
		foreach ($disabledList as $name => $state)
			if (($this->isEn($name) == "enabled") && (!in_array($name, $skip))) {
				$mInstance = Module::getInstanceByName($name);
				if (Validate::isLoadedObject($mInstance))
					$mInstance->disable();
			}
		*/
		foreach ($enabledList as $name => $state)
			if ($this->isEn($name) == "disabled") {
				$mInstance = Module::getInstanceByName($name);
				if (Validate::isLoadedObject($mInstance))
					$mInstance->enable();
			}

			// put theme settings to last position
		if (($id_hook = Hook::getIdByName("displayHeader")) !== false) {
			$mInstance = Module::getInstanceByName($this->name);
			if (Validate::isLoadedObject($mInstance)) {
				$position = $mInstance->getPosition($id_hook);
				$sql = 'SELECT MAX(`position`) AS position FROM `'._DB_PREFIX_.'hook_module` WHERE `id_hook` = '.(int)$id_hook.' AND `id_shop` = '.(int)Context::getContext()->shop->id;
				if (!$max_pos = Db::getInstance()->getValue($sql))
					$max_pos = 0;
				if (($position) && ($position < $max_pos))
					$res = $mInstance->updatePosition($id_hook, 1, $max_pos+1);
			}
		}

	}

	private function themeUpdate($ver) {

		$err = false;
		$msg = "";
		$archive = "http://promokit.eu/share/updates/".strtolower($this->theme_name)."/5.".$ver.".zip";
		$filecontents = file_get_contents($archive);

		if ($filecontents == false) {
			$msg .= $this->displayError("There is no file to update!"); 
		} else {		
			$file = _PS_MODULE_DIR_.$this->name.'/update'.$ver.'.zip';			
			if (!@copy($archive, $file)) {
				$msg .= $this->displayError("No update file to download"); 
			} else {
				$err = true;
				if (!Tools::ZipTest($file)) {
					$msg .= $this->displayError("Update file seems to be broken");
				} else {
					$err = true;
					$zip = new ZipArchive;
					$res = $zip->open($file);
					if ($res === TRUE) {
					  $zip->extractTo(_PS_ROOT_DIR_.'/');
					  $zip->close();
					  unlink($file);

					  $update_folder = _PS_MODULE_DIR_.$this->name.'/upgrade';
					  if ($handle = opendir($update_folder)) {
						    while (false !== ($entry = readdir($handle)))
						        if ($entry != "." && $entry != ".." && $entry != "index.php")
						            $versions[] = str_replace(".php", "", $entry);
						    closedir($handle);
					  }
					  foreach ($versions as $version)
							if ($version > $this->version)
								include $update_folder.'/'.$version.'.php';

					} else {
					  $msg .= $this->displayError("Unable to unzip updated files!");
					}
				}
			}				

			if ($err == true) {
				$msg .= $this->displayError("Theme has been updated successfully");	
			}
		}

		return $msg;

	}

	private function addImage($image, $name, $sid, $lid, $tab_num) {	
		$errors = "";
		if (isset($image[$name]) && isset($image[$name]['tmp_name']) && !empty($image[$name]['tmp_name']))
		{			
			if ($error = ImageManager::validateUpload($image[$name], Tools::convertBytes(ini_get('upload_max_filesize')))) 
				$errors = $error;

				if ($errors == "Image format not recognized, allowed formats are: .gif, .jpg, .png") {

					$errors = "Images extension wrong!";				

				} elseif ($dot_pos = strrpos($image[$name]['name'], '.')) {

					$imgname = $name;
					$ext = substr($image[$name]['name'], $dot_pos + 1);
					$newname = $name.'-'.(int)$this->context->shop->id;

					if (!move_uploaded_file($image[$name]['tmp_name'], _PS_MODULE_DIR_.$this->name.'/assets/images/upload/'.$newname.'.'.$ext)) {
						$result["error"] .= $this->l('Error move uploaded file');
					}
					else {
						$imgname = $newname;
					}

					$sql = array();
					$sql[] = 'UPDATE `'.$this->mdb.'` SET value = "'.$imgname.'.'.$ext.'" WHERE name = "'.$name.'" AND id_shop = '.$sid.';';
					$this->runSql($sql);					

				}				

		} else
			$errors = "No image to upload";

		$this->savePosition($tab_num);

		if ($errors)
			$errors = '<div class="conf error">'.$errors.'</div>'; 
		else 
			$errors = "";			
		$result["error"] = $errors;	

		return $result;

	}

	private function deleteImg($img, $name, $tab_num, $sid) {

		if (file_exists(_PS_MODULE_DIR_.$this->name.'/assets/images/upload/'.$img)) {

			unlink(_PS_MODULE_DIR_.$this->name.'/assets/images/upload/'.$img);

			$sql = array();
			$sql[] = 'UPDATE `'.$this->mdb.'` SET value = "" WHERE name = "'.$name.'" AND id_shop = '.$sid.';';
			$sql[] = 'UPDATE `'.$this->mdb.'` SET value = "'.$tab_num.'" WHERE name = "tab_number" AND id_shop = '.$sid.';';
			$this->runSql($sql);
			$msg = '<div class="conf confirm">'.$this->l('Image removed').'</div>';

		} else
			$msg = '<div class="conf error">'.$this->l('No image to delete').'</div>';

		return $msg;

	}

	public function cssWriter($data = false) {
		
		$files = array();

		if ($data != false)
			$files[0] = $this->customcssFile;

		$files[1] = $this->generatedFile;

		foreach ($files as $key => $file) {

			if ($key == 0)				
				$styles = $data;
			if ($key == 1)
				$styles = $this->cssGenerator();	

			if (!$fileHolder = @fopen($file, 'w')) {
				$this->errors .= $this->l('Cant open settings file!');
			} else {
				if (fwrite($fileHolder, $styles) === FALSE)
					$this->errors .= $this->l('Cant write settings!');
				fclose($fileHolder);
			}
		}		

	}

	public function addUnitsforCSSRule($rule, $value) {

		$rules_with_units_px = array('font-size', 'height', 'max-width', 'width');
		$rules_with_units_em = array('letter-spacing', 'line-height');	

		$unit = '';
		if (in_array($rule, $rules_with_units_px) && $value != 'auto') {
			$unit = 'px';
		}
		if (in_array($rule, $rules_with_units_em) && $value != 'auto') {
			$unit = 'em';
		}

		return $unit;
		
	}

	public function cssGenerator() {			

		$conf = new Pk_ThemeSettings_Config();
		$pk_options = $conf->getOptionsArray();
		$db_options = $this->getOptions();
		$css = '';

		foreach($pk_options as $options) {

			foreach($options['options_list'] as $option) {

				if (isset($option['output']) && !empty($option['output'])) {

					$typo_css_rules = '';

					if ($option['type'] == 'typography') {

						foreach ($option['default'] as $typo_opt => $typo_val) {

							$rule = str_replace("_", "-", $typo_opt);
							
							if (!empty($db_options[$option['name']][$typo_opt])) {
								$typo_val = $db_options[$option['name']][$typo_opt];
							}

							if ($rule == 'font-family') {
								$typo_val = "'".$typo_val."'";
							}

							$unit = $this->addUnitsforCSSRule($rule, $typo_val);
							$typo_css_rules .= $rule.':'.$typo_val.$unit.';';

						}

					} else {

						$unit = '';
						$value = $db_options[$option['name']];

						if (isset($option['css_rule'])) {
							$unit = $this->addUnitsforCSSRule($option['css_rule'], $value);
						}

						if ($option['css_rule'] == 'font-family') {
							$value = "'".$db_options[$option['name']]."'";
						}
						$typo_css_rules = $option['css_rule'].":".$value.$unit;


					}

					$css .= $option['output']."{".$typo_css_rules."}\n";


				}
			}
		}

		return $css;
	}

	public function maintenanceDate($s)	{  // get sizes

		$ready_date = "";		
		$el = array('min', 'hour', 'day', 'month', 'year');
		$monthes = array("0"=>"January","1"=>"February","2"=>"March","3"=>"April","4"=>"May","5"=>"June","6"=>"July","7"=>"August","8"=>"September","9"=>"October","10"=>"November","11"=>"December");

		foreach ($el as $key => $value) {
			$from = 0;
			$ready_date .= '<select name="ready_'.$value.'">';
			switch($value){
				case 'min':	
					$until = 59;
					$ready_date .= "<option disabled='disabled'>".$value."</option>";
					break;
				case 'hour':
					$until = 23;
					$ready_date .= "<option disabled='disabled'>".$value."</option>";
					break;
				case 'day':	
					$until = 31;
					$ready_date .= "<option disabled='disabled'>".$value."</option>";
					break;
				case 'month':
					$until = 11;
					$ready_date .= "<option disabled='disabled'>".$value."</option>";
					break;
				case 'year':
					$from = date("Y");$until = (date("Y")+1);
					$ready_date .= "<option disabled='disabled'>".$value."</option>";
					break;
				default:
					break;
			}						
			for ($time=$from; $time <= $until; $time++) {				
				if ($value == "month") $tm = $monthes[$time]; else $tm = $time;
				$ready_date .= '<option '.(($time == $s["ready_".$value.""]) ? 'selected' : '').' value="'.$time.'">'.$tm.'</option>';
			}					
			$ready_date .= '</select>';
		}					

		return $ready_date;

	}


	public function getOptions($who = false)	{  // get options from database
		$query = 'SELECT * FROM `'.$this->mdb.'` WHERE id_shop = '.(int)$this->context->shop->id.';';		
		if (!$sett = Db::getInstance()->ExecuteS($query)) {
			$this->installDB();
			if (!$sett = Db::getInstance()->ExecuteS($query)) { // try once again
				return false;
			}
		}

		foreach ($sett as $key => $item) {
			foreach ($item as $k => $value) {	
				if ($k == "name") $n = $value;
				if ($k == "value") $v = $value;
				if (isset($v) && isset($n)) {
					if (isset($v[0]) && $v[0] == '{') {
						$v = (array)json_decode(htmlspecialchars_decode($v));
					}
					$s[$n] = $v;			
				}	
			}
		}

		return $s;
	}

	public function getModulesState()	{  // get options from database
		
		if (!$sett = Db::getInstance()->ExecuteS('SELECT * FROM `'.$this->hdb.'` WHERE id_shop = '.$this->context->shop->id.';'))
			return false;

		foreach ($sett as $key => $section) {
			$s[$section["hook"]][$section["module"]] = $section["ordr"].".".$section["value"];
		}

		return $s;
	}

	public function readCustomCSS()	{  // get sizes

		if (!$fileHolder = @fopen($this->customcssFile, 'r')) {
			$this->errors .= "Can't open css file!";
			return false;
		} else {
			$custom_css = "";
			if (file_exists($this->customcssFile)) {
				if (filesize($this->customcssFile) > 0) {
					$custom_css = fread($fileHolder, filesize($this->customcssFile));
					fclose($fileHolder);
				}
			}
			return $custom_css;
		}

	}

	public function getModules()	{  // get sizes		

		$mState = $this->getModulesState();	
		$this->getPkModules();
		
		$token = Tools::getAdminToken('AdminModules'.(Tab::getIdFromClassName('AdminModules')).$this->context->employee->id);

		if (!empty($mState)) {
			foreach ($mState as $key => $value) {
				$hooks[] = $key;
			}
		}

		$details = array(
			'token' => $token
		);

		if (isset($hooks)) {

			foreach ($hooks as $hook) {

				if ($id_hook = Hook::getIdByName($hook)) {

					$modules = Hook::getModulesFromHook($id_hook);

					if ($modules) {

						$details['hooks'][$hook]['modules'] = $modules;
						$details['hooks'][$hook]['hook_id'] = Hook::getIdByName($hook);

						foreach ($modules as $id => $options) {

							$curr = array(0,0);
							if (isset($mState[$hook][$options['name']])) {
								$curr = explode(".", $mState[$hook][$options['name']]);
							}

							$details['hooks'][$hook]['modules'][$id]['current'] = $curr;

						}
					}
				}	
			}
		}

		return $details;

	}

	public function isPkModules($module) {

		$allModules = $this->getPkModules();
		if (in_array($module, $allModules)) {
			return true;	
		}
		return false;
	}

	public function getPkModules() {

		$availableModules = Module::getModulesDirOnDisk();
		foreach ($availableModules as $id => $name) {
			if (strpos($name, 'pk_') === false && $name != 'revsliderprestashop' && $name != 'xipblog') {
				unset($availableModules[$id]);
			}
		}
		return $availableModules;
			
	}


	public function ajaxProcessUpdateModPositions($id_module, $id_hook, $way, $positions)
	{
		$position = (is_array($positions)) ? array_search($id_hook.'_'.$id_module, $positions) : null;
		$module = Module::getInstanceById($id_module);
		if (Validate::isLoadedObject($module)) {
			if ($module->updatePosition($id_hook, $way, $position)) {
				die(true);
			} else {
				die('{ "hasError" : true, "errors" : "Cannot update module position." }');
			}
		} else {
			die('{ "hasError" : true, "errors" : "This module cannot be loaded." }');
		}
	}

	public function displayForm($message) {


		//$fnts = json_decode(file_get_contents( 'https://www.googleapis.com/webfonts/v1/webfonts?key=YOURKEY' ));
		//foreach ($fnts->items as $key => $font) echo $font->family."\n";	
		$s = $this->getOptions();
		$mState = $this->getModulesState();
		$modules_list = $this->getModules();		

		$id_shop = (int)Context::getContext()->shop->id;
		$rev = date("H").date("i").date("s")."\n";	
		$imgPath = $_SERVER['DOCUMENT_ROOT'].$this->_path.'/assets/images/upload/';

		$email_list = $this->getEmails();
		$emails = "";
		if ($email_list)
			foreach ($email_list as $email)
				$emails .= "<span>".$email."</span>";
			
		$sett_str = $styles = "";
		for ($i = 0; $i < $this->patternsQuantity; $i++) { // get patterns

			if ($s["pattern"] == $i) {
				$checked = "checked=\"maxchecked\"";
				$ptClass = "selected";
			} else {
				$checked = "";
				$ptClass = "";
			}
			if ($i == 0) {
				$title = "title=\"No pattern\"";
			} else {
				$title = "";
			}
			$sett_str .= "<label ".$title." for=\"back_".$i."\" class=\"cell back_".$i." ".$ptClass."\" onclick=\"$('#ptrns label').removeClass('selected');$(this).addClass('selected');\">							
			<input type=\"radio\" id=\"back_".$i."\" name=\"pattern\" value=\"".$i."\" class=\"var\" $checked /></label>";
		}

		$protocol = Tools::getShopProtocol();
		
		$link_params = "rel=\"stylesheet\" type=\"text/css\" href=\"".$protocol."fonts.googleapis.com/css?family=";
		$fontFiles = "";

		$logo = "";
		if (file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop)))
			$logo = __PS_BASE_URI__."img/".Configuration::get('PS_LOGO', null, null, $id_shop);
/*
		$options = $this->getCMSOptions(1, 1, (int)$s["maintanance_cms_page"]);
*/

		$conf = new Pk_ThemeSettings_Config();
		$pk_options = $conf->getOptionsArray();

		foreach ($pk_options as $tab => $options) {
			foreach ($options['options_list'] as $key => $value) {
				if ($value['type'] != 'separator') { // skip separator
					if ($value['type'] == 'typography') {
						foreach ($value['default'] as $k => $v) {
							$pk_options[$tab]['options_list'][$key]['current'][$k] = (isset($s[$value['name']][$k]) ? $s[$value['name']][$k] : $v );	
						}
					} else {
						$pk_options[$tab]['options_list'][$key]['current'] = (isset($s[$value['name']]) ? $s[$value['name']] : $value['default'] );
					}
				}
			}
		}

		$pk_options['maintenance']['options_list'][0]['current'] = Configuration::get('PS_SHOP_ENABLE');
		$pk_options['homepage']['content'] = $modules_list;
		$pk_options['customer_css']['options_list'][0]['current'] = $this->readCustomCSS();

		$this->context->smarty->assign(
			array(
				's' => $s,
				'path' => $this->_path,
				'imgs' => $this->_path.'assets/images/',
				'tpl_path' => _PS_MODULE_DIR_.$this->name.'/views/admin/',
				'action' => Tools::safeOutput($_SERVER['REQUEST_URI']),
				'message' => $message,
				'fontFiles' => "",
				'sett_str' => $sett_str,
				'token' => Tools::getAdminToken('AdminModules'.(Tab::getIdFromClassName('AdminModules')).$this->context->employee->id),
				//'set' => $set,
				'logo' => $logo,
				'sections' => $pk_options,
				'pk_modules' => $this->getPkModules(),
				'version' => $this->versions
		  	)
		);

		return $this->fetch(_PS_MODULE_DIR_.$this->name.'/views/admin/main.tpl');
		
	}

	private function getCMSOptions($parent = 0, $depth = 1, $curr) {
		$id_lang = (int)Context::getContext()->language->id;		
		$pages = $this->getCMSPages((int)$parent, (int)$id_lang);

		$opts = "";
		foreach ($pages as $page) {
			$opts .= '<option '.(($page["id_cms"] == $curr) ? "selected": "").' value="'.$page['id_cms'].'">'.$page['meta_title'].'</option>';
		}
		return $opts;
	}

	private function getCMSPages($id_cms_category, $id_lang) {
		$id_shop = (int)Context::getContext()->shop->id;
		$sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
			FROM `' . _DB_PREFIX_ . 'cms` c
			INNER JOIN `' . _DB_PREFIX_ . 'cms_shop` cs
			ON (c.`id_cms` = cs.`id_cms`)
			INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` cl
			ON (c.`id_cms` = cl.`id_cms`)
			WHERE c.`id_cms_category` = ' . (int)$id_cms_category . '
			AND cs.`id_shop` = ' . (int)$id_shop . '
			AND cl.`id_lang` = ' . (int)$id_lang . '
			AND c.`active` = 1
			ORDER BY `position`';

		return Db::getInstance()->executeS($sql);
	}

	function prepare_smarty($control, $theme_settings=null) {
		
		if (!$theme_settings)
			$theme_settings = $this->getOptions("prepare_smarty");

		$theme_settings['theme_name'] = $this->theme_name;
		$theme_settings['modules'] = $this->getModulesState();
		$theme_settings['shopID'] = (int)Context::getContext()->shop->id;
		//$theme_settings['ts_path'] = rtrim($_SERVER['DOCUMENT_ROOT'], "/").$this->_path;
		$theme_settings['cat_img_path'] = _PS_CAT_IMG_DIR_;
		$theme_settings['ts_path'] = _PS_ROOT_DIR_."/modules/".$this->name."/";	
		$theme_settings['cookie_page'] = Context::getContext()->link->getCMSLink(11);

		$theme_settings['used_fonts'] = $this->getusedfonts($theme_settings);
		$theme_settings['mini_cart'] = _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/templates/catalog/_partials/miniatures/mini-product.tpl';

		// assign cookie variables to see them in any place of the theme 
		$this->setCookies($theme_settings);
		
		return $theme_settings;

	}

	public function setCookies($settings) {
		
		setcookie('ts_cart_link', $this->context->link->getPageLink('cart', true));
		setcookie('ts_order_link', $this->context->link->getPageLink('order', true));
    	setcookie('ts_is_catalog', Configuration::isCatalogMode());
    	setcookie('ts_token', Tools::getToken(false));
    	setcookie('cp_listing_view', $settings['cp_listing_view']);

    	foreach ($settings as $key => $value) {
    		if (strpos($key, 'pm_') !== false && !is_array($value)) {
    			setcookie($key, $value);
    		}
    	}

	}

	public function getusedfonts($db_options) {

		$systemFonts = array("Arial", "Tahoma", "Georgia", "Times New Roman", "Verdana", "FontAwesome");

		$cyrillic = $latin_ext = $subset = "";
		$allfonts = array();

		$conf = new Pk_ThemeSettings_Config();
		$pk_options = $conf->getOptionsArray();

		foreach ($db_options as $option => $value) {
			if (is_array($value)) {
				foreach ($value as $sub_option => $sub_value) {
					if ($sub_option == 'font_family') {
						$allfonts[] = $this->fontNameAdaptation($sub_value);
					}
				}	
			}
		}

		if ($db_options["cyrillic"] == true) {
			$subset = "&subset=";
			$cyrillic = "cyrillic";
		}

		if ($db_options["latin_ext"] == true) {
			$subset = "&subset=";
			$latin_ext = (($cyrillic == "") ? "" : ",")."latin-ext";
		}
		
		$allfonts = array_unique($allfonts);
		foreach ($allfonts as $key => $font) {
			if (!in_array($font, $systemFonts)) {

				$style = '';
				if ($font == 'Roboto') {
					$style = ':100,400,500,500i,900';	
				}
				$fonts[] = $font.$style;

			}
		}

		if (isset($fonts)) {
			$font_str = implode("%7C", $fonts);
			return $font_str.$subset.$cyrillic.$latin_ext;
		}
		return false;
	}

	public function fontNameAdaptation($name) {

		return str_replace( ' ', '+', $name );

	}

	public function isInst($name) {	

		$return = "not_installed";
		if (Module::isInstalled($name))
			$return = "installed";

		return $return;
	}

	public function isEn($name) {	

		$return = "disabled";
		$id_module = Module::getModuleIdByName($name);
		if (Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module_shop` WHERE `id_module` = '.(int)$id_module.' AND `id_shop` = '.(int)Context::getContext()->shop->id))
			$return = "enabled";

		return $return;

	}

	public function getImLink($link_rewrite, $img_id, $imgName) {	

		return $this->context->link->getImageLink($link_rewrite, $img_id, $imgName);
		
	}

	public function getImg($product_id, $link_rewrite, $imgName, $imgAttr = false) {	
		if ($imgAttr == false) {
			$imgAttr = Image::getCover($product_id);	
			$imgAttr = $imgAttr["id_image"];
		}
		$img = $this->getImLink($link_rewrite, (int)$imgAttr["id_image"], $imgName);
		return $img;
	}	
	
	public function getImgByAttr($product_id, $link_rewrite, $imgName, $imgattr) {
		$imgid = Image::getImages($this->context->language->id, $product_id, $imgattr);
		if (!empty($imgid[0]))
			$img = $this->getImLink($link_rewrite, (int)$imgid[0]["id_image"], $imgName);
		else
			$img = false;

		return $img;
	}	
	
	public function getSocialAccouts($ts) {	
		$soc = array();
		if ($ts["sa_facebook"] == 1) $soc["facebook"] = $ts["sa_facebook_link"];
		if ($ts["sa_twitter"] == 1) $soc["twitter"] = $ts["sa_twitter_link"];
		if ($ts["sa_gplus"] == 1) $soc["gplus"] = $ts["sa_gplus_link"];
		if ($ts["sa_youtube"] == 1) $soc["youtube"] = $ts["sa_youtube_link"];
		if ($ts["sa_flickr"] == 1) $soc["flickr"] = $ts["sa_flickr_link"];
		if ($ts["sa_instagram"] == 1) $soc["instagram"] = $ts["sa_instagram_link"];
		if ($ts["sa_pinterest"] == 1) $soc["pinterest"] = $ts["sa_pinterest_link"];
		if ($ts["sa_linkedin"] == 1) $soc["linkedin"] = $ts["sa_linkedin_link"];
		return $soc;
	}

	public function getPaymentIcons($ts) {	
		$pay = array();
		if ($ts["pay_visa"] == 1) $pay["visa"] = $ts["pay_visa"];
		if ($ts["pay_am_exp"] == 1) $pay["am_exp"] = $ts["pay_am_exp"];
		if ($ts["pay_mastercard"] == 1) $pay["mastercard"] = $ts["pay_mastercard"];
		if ($ts["pay_paypal"] == 1) $pay["paypal"] = $ts["pay_paypal"];
		if ($ts["pay_maestro"] == 1) $pay["maestro"] = $ts["pay_maestro"];
		if ($ts["pay_discover"] == 1) $pay["discover"] = $ts["pay_discover"];
		if ($ts["pay_cirrus"] == 1) $pay["cirrus"] = $ts["pay_cirrus"];
		if ($ts["pay_direct"] == 1) $pay["direct"] = $ts["pay_direct"];
		if ($ts["pay_solo"] == 1) $pay["solo"] = $ts["pay_solo"];
		if ($ts["pay_switch"] == 1) $pay["switch"] = $ts["pay_switch"];
		if ($ts["pay_wu"] == 1) $pay["wu"] = $ts["pay_wu"];
		return $pay;
	}

	/* CUSTOM HOOKS */
	public function hookcomingsoon($params)
	{

		$theme_settings = $this->getOptions("hookcomingsoon");
	    $cs["status"] = Configuration::get('PS_SHOP_ENABLE');
	    if (isset($theme_settings['mt_date_until'])) {
	    	$date_array = explode('/', $theme_settings['mt_date_until']);
	    	$cs['day'] = $date_array[0];
	    	$cs['month'] = (int)$date_array[1]-1;
	    	$cs['year'] = $date_array[2];
	    }

	    if (isset($theme_settings['mt_notify'])) {
		    $cs['notify'] = $theme_settings['mt_notify'];
		}
		if (isset($theme_settings['mt_countdown'])) {
		    $cs['countdown'] = $theme_settings['mt_countdown'];
		}

		$this->context->smarty->assign(
		  array(
		      'cs' => $cs,	      
		      'module_dir' => $this->context->link->getBaseLink().'modules/'.$this->name,
		      'mainURL' => $this->context->link->getBaseLink()
		  )
		);
		
		return $this->fetch('module:'.$this->name.'/views/frontend/comingsoon.tpl');
	}

	public function hookfooter_bottom($params) {

		$ts = $this->getOptions("hookfooter_bottom");
		
		$smarty = array("module_dir" => $this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/assets/images/payment_icons/32/');

		if ($ts['footer_bottom_pcards'] == 1) {
			$smarty["pay"] = $this->getPaymentIcons($ts);
		}
		if ($ts['footer_bottom_social'] == 1) {
			$smarty["soc"] = $this->getSocialAccouts($ts);
		}
		
		$this->context->smarty->assign($smarty);

		return $this->fetch('module:'.$this->name.'/views/frontend/footer_bottom.tpl');

	}

	public function hookfooter_top($params)
	{

		$s = $this->getModulesState();
		$state = explode(".", $s["footer_top"][$this->name]);
	 	if ($state[1] == 1) {
	 		
	 		if (!$this->isCached('views/frontend/products.tpl')) {
				$lid = $this->context->language->id;
				$category = new Category(Context::getContext()->shop->getCategory(), $lid);

				$this->context->smarty->assign(array(
					"ts_fea" => $category->getProducts($lid, 0, 2),
					"ts_spe" => Product::getPricesDrop($lid, 0, 2),
					"ts_new" => Product::getNewProducts($lid, 0, 2)
				));
			}
			return $this->fetch('module:'.$this->name.'/views/frontend/products.tpl');
		}
	}

	public function productVideo($params) {
		$s = $this->getOptions("productVideo");
		$id_product = Tools::getValue('id_product');
		$sid = (int)Context::getContext()->shop->id;
		$lid = $this->context->language->id;
		$getVideo = Db::getInstance()->ExecuteS('SELECT `video` FROM `'._DB_PREFIX_.'pk_product_extratabs` WHERE  id_product = '.$id_product.' AND shop_id = '.$sid.' AND lang_id = '.$lid);
	        $this->context->smarty->assign(array(
	            'pk_video_id' => $getVideo[0]["video"],
	            'id_lang' => $lid,
	            'languages' => Language::getLanguages(true)
	        ));

        if ($s["product_video"] == 1)
	        return $this->fetch(__FILE__, 'views/frontend/video.tpl');
	    else
	    	return false;

	}		

	/*	BACKOFFICE HOOKS	*/
    /*
    public function hookActionProductUpdate($params) {
    	return false;

        $sql = array();
        $id_product = (int)$params['product']->id;
        $languages = Language::getLanguages();        
        if ($id_product) {
        	//if (isset($_GET['updateproduct'])) {
	        foreach ($languages as $id_lang => $language) {

	        	$custom_tab = addslashes(Tools::getValue('custom_tab_'.$language["id_lang"]));
	        	$custom_tab_name = Tools::getValue('custom_tab_name_'.$language["id_lang"]);
	        	$video_id = Tools::getValue('video_id_'.$language["id_lang"]); 

	        	$check = Db::getInstance()->ExecuteS('SELECT id_pet, custom_tab_name, custom_tab, video FROM `'._DB_PREFIX_.'pk_product_extratabs` WHERE id_product = '.$id_product.' AND shop_id = '.(int)Context::getContext()->shop->id.' AND lang_id = '.$language["id_lang"].';');

	        	if (!empty($check)) {

	        		$sql[] = 'UPDATE `'._DB_PREFIX_.'pk_product_extratabs` SET custom_tab_name = "'.$custom_tab_name.'", custom_tab = \''.$custom_tab.'\', video="'.$video_id.'" WHERE id_product = '.$id_product.' AND shop_id = '.(int)Context::getContext()->shop->id.' AND lang_id = '.$language["id_lang"].';';

	    		} else {

	    			$sql[] = 'INSERT INTO `'._DB_PREFIX_.'pk_product_extratabs` (`id_product`, `shop_id`, `lang_id`, `video`, `custom_tab_name`, `custom_tab`) VALUES ('.$id_product.', '.(int)Context::getContext()->shop->id.', '.$language["id_lang"].', "123", "name", "content")';	
	    			}    			
	        }                
	        $this->runSql($sql);
	    	}
	 //   }
        
    }
    */
    
    /*	FRONTPAGE HOOKS	*/
    public function hookProductTab($params) {
    	return false;
    	$s = $this->getOptions("hookProductTab");
        $id_product = Tools::getValue('id_product');
        $sid = (int)Context::getContext()->shop->id;
		$lid = $this->context->language->id;
        $getCustomTab = Db::getInstance()->ExecuteS('SELECT `custom_tab_name`, `custom_tab` FROM `'._DB_PREFIX_.'pk_product_extratabs` WHERE  id_product = '.$id_product.' AND shop_id = '.$sid.' AND lang_id = '.$lid);        
        $getVideo = Db::getInstance()->ExecuteS('SELECT `video` FROM `'._DB_PREFIX_.'pk_product_extratabs` WHERE  id_product = '.$id_product.' AND shop_id = '.$sid.' AND lang_id = '.$lid);
        $tab = "";
        
        if (($s["custom_tab"]) && isset($getCustomTab[0]["custom_tab_name"]) && (!empty($getCustomTab[0]['custom_tab'])) ) {
            $tab .= "<h3 class='page-product-heading' data-title=\"12\"><span>".(($getCustomTab[0]["custom_tab_name"] == '') ? "Custom Tab" : $getCustomTab[0]["custom_tab_name"])."</span></h3>";
        }

        if (($s["product_video"] == 1) && (!empty($getVideo)))
        	if ($getVideo[0]["video"] != "")
            	$tab .= "<h3 class='page-product-heading' data-title=\"13\"><span>".$this->l('Video')."</span></h3>";

	    return $tab;
    }

    public function hookProductTabContent($params) {
    	return false;
    	$s = $this->getOptions("hookProductTabContent");
        $id_product = Tools::getValue('id_product');
        $sid = (int)Context::getContext()->shop->id;
		$lid = $this->context->language->id;
        $getCustomTab = Db::getInstance()->ExecuteS('SELECT `custom_tab`, `custom_tab_name` FROM `'._DB_PREFIX_.'pk_product_extratabs` WHERE  id_product = '.$id_product.' AND shop_id = '.$sid.' AND lang_id = '.$lid);
       	$getVideo = Db::getInstance()->ExecuteS('SELECT `video` FROM `'._DB_PREFIX_.'pk_product_extratabs` WHERE  id_product = '.$id_product.' AND shop_id = '.$sid.' AND lang_id = '.$lid);
        if (isset($getCustomTab[0])) {
            $this->context->smarty->assign(array(
                'pk_custom_tab' => $getCustomTab[0]["custom_tab"],
                'pk_custom_tab_name' => $getCustomTab[0]["custom_tab_name"],
                'pk_video_id' => $getVideo[0]["video"]
            ));
        }
	    return $this->fetch(__FILE__, 'views/frontend/customcontent.tpl');	    
    }

    public function installQuickAccess(){

      $qick_access = new QuickAccess();
      $qick_access->link = 'index.php?controller=AdminModules&configure='.$this->name.'&tab_module=front_office_features&module_name='.$this->name;
      $qick_access->new_window = false;

      $languages = Language::getLanguages(false);
      foreach ($languages as $language)
          $qick_access->name[$language['id_lang']]= $this->theme_name.' Settings';

      $qick_access->add();  
      if(!$qick_access->id)
            return FALSE;
        
      return true;
    }

	public function hookDisplayHeader($params) {	

		$theme_settings = $this->getOptions();

		$themeUrl = __PS_BASE_URI__.'themes/'.strtolower(_THEME_NAME_).'/css/';
		$this->context->controller->addCSS($this->_path.'assets/css/styles.css', 'all');
		$this->context->controller->addCSS($this->local_path."assets/css/presets/preset".$theme_settings["preset"].".css", 'all');
		$this->context->controller->addCSS($this->generatedFile, 'all');
		if (file_exists($this->customcssFile)) {
			if (filesize($this->customcssFile) != false) {
				$this->context->controller->addCSS($this->customcssFile, 'all');
			}
		}
		if (!isset($this->context->controller->php_self) || $this->context->controller->php_self == 'category')
			$this->context->controller->addJS($this->_path.'assets/js/background-check.js');
		$this->context->controller->addJS($this->_path.'assets/js/commonscripts.js');		
		$actualAction = $this->context->controller->php_self;

		$smarty_options = $this->prepare_smarty("head", $theme_settings);

		if (isset($theme_settings['pm_details_layout'])) {
			$page = $this->context->smarty->tpl_vars['page'];
			if (isset($theme_settings['pm_details_layout'])) {
				$page->value['body_classes'][$theme_settings['pm_details_layout']] = true;
			}
			if (isset($theme_settings['header_position'])) {
				$page->value['body_classes'][$theme_settings['header_position']] = true;
			}
			if (isset($theme_settings['cp_only_filter']) && $theme_settings['cp_only_filter'] == 1) {
				$page->value['body_classes']['cp-only-filter'] = true;
			}
			$this->context->smarty->assign((array)$page);
		}

		$this->context->smarty->assign(array(
			'pkts' => $smarty_options,
			'shopID' => (int)$this->context->shop->id
		));
		
	}

	public function hookDisplayFooterBefore($params) {

		$theme_settings = $this->getOptions();
		$smarty_options = $this->prepare_smarty("head", $theme_settings);
		$pk_json = $this->prepare_json($smarty_options);

		$this->context->smarty->assign(array(
			'pk_json' => $pk_json
		));
		
		return ($this->fetch('module:'.$this->name.'/views/frontend/config.tpl'));

	}

	public function hookDisplayBeforeBodyClosingTag($params) {

		if ( (isset($_COOKIE['cookie-message']) && $_COOKIE['cookie-message'] != 0) || !isset($_COOKIE['cookie-message']) ) {
			return ($this->fetch('module:'.$this->name.'/views/frontend/cookie-message.tpl'));		
		}
		
	}

	public function injectDisplayBackOfficeHeader() {

		$this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/jquery.colorpicker.js');
		$this->context->controller->addJqueryPlugin('sortable');		
		$this->context->controller->addJqueryUI('ui.datepicker');
		$this->context->controller->addJS($this->_path.'assets/js/ace/ace.js'); // add JS to back office
		$this->context->controller->addJS($this->_path.'assets/js/admin.js'); // add JS to back office
		$this->context->controller->addCSS($this->_path.'assets/css/admin.css'); // add CSS to back office
		
	}

	public function prepare_json($options) {
		$exclude = array('fonts_list', 'defaultFonts', 'systemFonts', 'selectors', 'modules', 'used_fonts', 'sizes');
		foreach ($options as $key => $value) {
			if (in_array($key, $exclude)) {
				unset($options[$key]);
			}
		}
		$json = json_encode($options, JSON_PRETTY_PRINT);
		return $json;
	}

}