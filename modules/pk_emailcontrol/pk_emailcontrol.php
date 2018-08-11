<?php
/**
 * $ModDesc
 * 
 * @version		1.2.1
 * @package		modules
 * @copyright	Copyright (C) March 2012 http://promokit.eu <@email:support@promokit.eu>. All rights reserved.
 * @license		GNU General Public License version 2
 */
if (!defined('_PS_VERSION_'))
	exit;

class Pk_EmailControl extends Module
{

	public $pattern = '/^([A-Z_]*)[0-9]+/';
	public $page_name = '';

	public function __construct()
	{
		$this->name = 'pk_emailcontrol';
		$this->tab = 'Other';
		$this->version = '1.2.1';
		$this->author = 'Promokit Co.';	
		$this->need_instance = 0;
		$this->displayName = 'Email Control';
		$this->description = $this->l('Allows you to manage of emails content.');	
		$this->bootstrap = true;

		parent::__construct();
		$this->templateArchive = "http://promokit.eu/share/venedor/mails.zip";	

		$this->dbTable = _DB_PREFIX_.'pk_emailcontrol';

	}


	public function install()
	{
		$sql = array();
		$sid = (int)Context::getContext()->shop->id;
		$lid = $this->context->language->id;	

		$json_arr = array("email_fb" => 1, "email_tw" => 1, "email_em" => 1, "email_sk" => 1, "email_gp" => 1, "email_yt" => 1, "email_fb_acc" => "", "email_tw_acc" => "", "email_yt_acc" => "", "email_gp_acc" => "", "email_em_acc" => "email01@email.com", "email_em_acc2" => "email02@email.com", "email_sk_acc" => "skype01", "email_sk_acc2" => "skype02", "email_image" => "", "email_adv" => 1, "email_addr" => 1, "email_addr_text" => "United Kingdom Greater London London 02587 Oxford Street 48/188 Working d.: Mon. - Sun. Working h.: 9.00-8.00PM", "email_ph" => 1, "email_ph_acc" => "0203 280 3704", "email_ph_acc2" => "0203 281 3704", "email_oph" => 1, "email_oph_acc" => "445-115-747-38", "email_oph_acc2" => "445-170-029-32");

		$sql[] = 'CREATE TABLE IF NOT EXISTS `'.$this->dbTable.'` (
				`id_setting` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				`id_lang` int(10) unsigned NOT NULL,
				`name` VARCHAR(50),
				`value` VARCHAR(9999),
                  PRIMARY KEY (`id_setting`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		$sqlPart = 'INSERT INTO `'.$this->dbTable.'` (`id_shop`, `id_lang`, `name`, `value`) VALUES ';
		
		$num = count($json_arr);
		$counter = 1;

		foreach ($json_arr as $name => $value) {
		    if ($counter == $num) {$coma = ";";} else {$coma = ",";}
		    $sqlPart .= "(".$sid.", ".$lid.", \"".$name."\", \"".$value."\")".$coma;
		    $counter++;
		}
		$sql[] = $sqlPart;

		if (
			parent::install() && 
			$this->runSql($sql) && 
			$this->copyTemplates($this->templateArchive) &&
			$this->registerHook('sendMailAlterTemplateVars')
			) {
			return true;	
		} else {
			$this->uninstall();
			return false;
		}
	}

	public function uninstall() {
        $sql = array();
		$sql[] = 'DROP TABLE IF EXISTS `'.$this->dbTable.'`';
        if (!parent::uninstall() OR !$this->runSql($sql)) {
        	unlink(_PS_OVERRIDE_DIR_.'classes/Mail.php');
            return FALSE;
        }

        return TRUE;
    }

	public function runSql($sql) {
        foreach ($sql as $s) {
			if (!Db::getInstance()->Execute($s)) return FALSE;
        }
        return TRUE;
    }

	public function hookSendMailAlterTemplateVars($param)
    {
    	$s = $this->getOptions();

        //set default variable values
        $socialNetworksBegin = '<div style="overflow:hidden; margin-bottom:20px; color:#ccc; line-height:17px"><img width="32" heigt="32" style="display:block; float:left; margin-right:10px" src="';
        $socialNetworksMid = '" alt="" /><a style="text-decoration:none; color:#cccccc; line-height:32px" ';
        $socialNetworksEnd = '</a></div>';

        $contactBegin = '<div style="overflow:hidden; margin-bottom:20px; color:#cccccc; line-height:17px"><img width="32" heigt="32" style="display:block; float:left; margin-right:10px" src="';
        $contactEnd = '</div>';

        $path_to_images = '//'.Context::getContext()->shop->domain.Context::getContext()->shop->physical_uri.'modules/pk_emailcontrol/imgs/';

        $param['template_vars']['{adv_img}'] = $param['template_vars']['{social_networks}'] = $param['template_vars']['{email_addr}'] = $s['email_adv_link'] = $param['template_vars']['{email_contacts}'] = ""; 
        $param['template_vars']['{path_img}'] = $path_to_images; 

        // advertising image
        if (($s["email_adv"] == 1) AND ($s["email_image"] != "")) {
            $param['template_vars']['{adv_img}'] = "<a href='".$s['email_adv_link']."'><img width='224' border='0' height='224' src='//".Context::getContext()->shop->domain.Context::getContext()->shop->physical_uri."modules/pk_emailcontrol/imgs/upload/".$s["email_image"]."' alt='' ></a>";
        }

        // contact information
        if ($s["email_ph"] == 1) {
            $param['template_vars']['{email_contacts}'] .= $contactBegin.$path_to_images.'ic_ph.jpg" alt="" />';
            if ($s["email_ph_acc"] != "") {
                $param['template_vars']['{email_contacts}'] .= $s["email_ph_acc"];
            }
            if ($s["email_ph_acc2"] != "") {
                $param['template_vars']['{email_contacts}'] .= "<br>".$s["email_ph_acc2"];
            }
            $param['template_vars']['{email_contacts}'] .= $contactEnd;
        }
        if ($s["email_oph"] == 1) {
            $param['template_vars']['{email_contacts}'] .= $contactBegin.$path_to_images.'ic_mo.jpg" alt="" />';
            if ($s["email_oph_acc"] != "") {
                $param['template_vars']['{email_contacts}'] .= $s["email_oph_acc"];
            }
            if ($s["email_oph_acc2"] != "") {
                $param['template_vars']['{email_contacts}'] .= "<br>".$s["email_oph_acc2"];
            }
            $param['template_vars']['{email_contacts}'] .= $contactEnd;
        }
        if ($s["email_em"] == 1) {
            $param['template_vars']['{email_contacts}'] .= $contactBegin.$path_to_images.'ic_em.jpg" alt="" />';
            if ($s["email_em_acc"] != "") {
                $param['template_vars']['{email_contacts}'] .= $s["email_em_acc"];
            }
            if ($s["email_em_acc2"] != "") {
                $param['template_vars']['{email_contacts}'] .= "<br>".$s["email_em_acc2"];
            }
            $param['template_vars']['{email_contacts}'] .= $contactEnd;
        }
        if ($s["email_sk"] == 1) {
            $param['template_vars']['{email_contacts}'] .= $contactBegin.$path_to_images.'ic_sk.jpg" alt="" />';
            if ($s["email_sk_acc"] != "") {
                $param['template_vars']['{email_contacts}'] .= $s["email_sk_acc"];
            }
            if ($s["email_sk_acc2"] != "") {
                $param['template_vars']['{email_contacts}'] .= "<br>".$s["email_sk_acc2"];
            }
            $param['template_vars']['{email_contacts}'] .= $contactEnd;
        }

        // phisical address of your shop
        if (($s["email_addr"] == 1) AND ($s["email_addr_text"] != "")) {
            $param['template_vars']['{email_addr}'] = $s["email_addr_text"];
        }

        // social accounts
        if (($s["email_fb"] == 1) AND ($s["email_fb_acc"] != "")) {
            $param['template_vars']['{social_networks}'] .= $socialNetworksBegin.$path_to_images.'ic_fb.jpg'.$socialNetworksMid.'href="'.$s["email_fb_acc"].'">'.Mail::l('Like us on Facebook', (int)Context::getContext()->language->id).$socialNetworksEnd;
        }
        if (($s["email_tw"] == 1) AND ($s["email_tw_acc"] != "")) {
            $param['template_vars']['{social_networks}'] .= $socialNetworksBegin.$path_to_images.'ic_tw.jpg'.$socialNetworksMid.'href="'.$s["email_tw_acc"].'">'.Mail::l('Follow us on Twitter', (int)Context::getContext()->language->id).$socialNetworksEnd;
        }
        if (($s["email_gp"] == 1) AND ($s["email_gp_acc"] != "")) {
            $param['template_vars']['{social_networks}'] .= $socialNetworksBegin.$path_to_images.'ic_gp.jpg'.$socialNetworksMid.'href="'.$s["email_gp_acc"].'">'.Mail::l('Circle us on Google+', (int)Context::getContext()->language->id).$socialNetworksEnd;
        }
        if (($s["email_yt"] == 1) AND ($s["email_yt_acc"] != "")) {
            $param['template_vars']['{social_networks}'] .= $socialNetworksBegin.$path_to_images.'ic_yt.jpg'.$socialNetworksMid.'href="'.$s["email_yt_acc"].'">'.Mail::l('View us on Youtube', (int)Context::getContext()->language->id).$socialNetworksEnd;
        }

    }
	

	public function getContent()
	{	
	    $s = $this->getOptions();
	    $sid = (int)Context::getContext()->shop->id;
		$lid = $this->context->language->id;	  
					
		//$this->context->controller->addJS(($this->_path).'js/scripts.js'); // add JS to back office
		$this->context->controller->addCSS(($this->_path).'css/emailcontrol_admin.css'); // add CSS to back office		
	
		$msg = $err = '';			

		$output = '';		

		if (Tools::isSubmit('submitDeleteEmailImg')) {

			$res = $this->deleteImg($s["email_image"], "email_image", $sid, $lid);

			if ($res == "") {$msg .= '<div class="conf confirm"><svg><use xlink:href="#si-ok"></use></svg>'.$this->l('Image removed').'</div>';}
				else {$msg .= '<div class="conf error">'.$res.'</div>';}

		}

		if (Tools::isSubmit('email_image_upload')) {	

			$img = $this->addImage($_FILES, "email_image", $sid, $lid);				
			$msg .= $img["error"].'<div class="conf confirm"><svg><use xlink:href="#si-ok"></use></svg>'.$this->l('Settings updated').'</div>';	

		}

		if (Tools::isSubmit('submitThemeSettings')) {

			$sql = array();
			foreach ($_POST as $key => $value) {
				$sql[] = 'UPDATE `'.$this->dbTable.'` SET value = "'.$value.'" WHERE name = "'.$key.'" AND id_shop = '.$sid.' AND id_lang = '.$lid.';';

			}			
			$e = $this->runSql($sql);												
			if (!$e) {$msg .= '<div class="conf error">'.$this->l('Can\'t write to database').'</div>';}
			$msg .= '<div class="conf confirm"><svg><use xlink:href="#si-ok"></use></svg>'.$this->l('Settings updated').'</div>';	
		}
		
		return $output.$this->displayForm($msg);
		
	}

	private function copyTemplates($source) {

		$file = _PS_MODULE_DIR_.$this->name.'/mail.zip';

		$err = true;
		$msg = "";
		$file_headers = @get_headers($source);
		if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {

			$msg .= "<div class=\"conf error\">There is no file to update!</div>"; 

		} else {
			if (!copy($source, $file)) {
				$msg .= "<div class=\"conf error\">Can't download the file</div>"; 
			} else {
				if (!Tools::ZipTest($file)) {
					$msg .= "<div class=\"conf error\">Zip file seems to be broken</div>";
				} else {
					$zip = new ZipArchive;
					$res = $zip->open($file);
					if ($res === TRUE) {
					  $zip->extractTo(_PS_THEME_DIR_);
					  $zip->close();
					  unlink($file);
					  $err = false;
					} else {
					  $msg .= "<div class=\"conf error\">Unable to unzip email templates</div>";
					}
				}
			}				
		}
		

		if ($err == false)
			$msg .= "<div class=\"conf confirm\"><svg><use xlink:href='#si-ok'></use></svg> Updated Successfull</div>";

		return $msg;
	}

	private function addImage($image, $name, $sid, $lid) {	
		$errors = "";
		
		if (isset($image[$name]) && isset($image[$name]['tmp_name']) && !empty($image[$name]['tmp_name']))
		{

			if ($error = ImageManager::validateUpload($image[$name], Tools::convertBytes(ini_get('upload_max_filesize')))) $errors = $error;

				if ($errors == "Image format not recognized, allowed formats are: .gif, .jpg, .png") {

					$errors = "Images extension wrong!";				

				} elseif ($dot_pos = strrpos($image[$name]['name'], '.')) {

					$imgname = $name;
					$ext = substr($image[$name]['name'], $dot_pos + 1);
					$newname = $name.'-'.(int)$this->context->shop->id;

					if (!move_uploaded_file($image[$name]['tmp_name'], _PS_IMG_DIR_.$newname.'.'.$ext))
						$result["error"] .= $this->l('Error move uploaded file');
					else
						$imgname = $newname;

					$sql = array();
					$sql[] = 'UPDATE `'.$this->dbTable.'` SET value = "'.$imgname.'.'.$ext.'" WHERE name = "'.$name.'" AND id_shop = '.$sid.' AND id_lang = '.$lid.';';
					$this->runSql($sql);					

				}				

		} else {
			$errors = "No image to upload";
		}	


		if ($errors) { 
			$errors = '<div class="conf error">'.$errors.'</div>'; 
		} else $errors = "";			
		$result["error"] = $errors;	

		return $result;

	}

	private function deleteImg($img, $name, $sid, $lid) {

		$err = "";
		
		// Delete the image file
		if (file_exists(_PS_IMG_DIR_.$img)) {
		
			unlink(_PS_IMG_DIR_.$img);

			$sql = array();
			$sql[] = 'UPDATE `'.$this->dbTable.'` SET value = "" WHERE name = "'.$name.'" AND id_shop = '.$sid.' AND id_lang = '.$lid.';';
			$this->runSql($sql);
		} else {
			$err = "No image to delete";
		}
		return $err;

	}

	public function getOptions()	{  // get options from database
		if (!$sett = Db::getInstance()->ExecuteS('SELECT * FROM `'.$this->dbTable.'`')) return false;
		
		foreach ($sett as $key => $item) {			
			foreach ($item as $k => $value) {				
				if ($k == "name") $n = $value;
				if ($k == "value") $v = $value;
				if (isset($v) && isset($n)) $s[$n] = $v;				
			}
		}	
		return $s;
	}

	public function displayForm($message)
	{	
		$s = $this->getOptions();

		$imgPath = 'http://'.Context::getContext()->shop->domain.Context::getContext()->shop->physical_uri.'/img/';					
		return '
		<script>
		$(document).ready(function(){
			setTimeout(function(){
				$(".confirm").fadeOut("800");
			},5000);
		});
	     </script>
		<svg style="display:none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
		<defs>
		<symbol id="si-ok" viewBox="0 0 510 510">
		<path d="M150.45,206.55l-35.7,35.7L229.5,357l255-255l-35.7-35.7L229.5,285.6L150.45,206.55z M459,255c0,112.2-91.8,204-204,204 S51,367.2,51,255S142.8,51,255,51c20.4,0,38.25,2.55,56.1,7.65l40.801-40.8C321.3,7.65,288.15,0,255,0C114.75,0,0,114.75,0,255 s114.75,255,255,255s255-114.75,255-255H459z"/>
		</symbol>
		</defs>
		</svg>
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" class="list_options defaultForm form-horizontal" id="themesettings ts-prefix" method="post">
			<div class="panel" id="fieldset_0">
			<div class="form-wrapper">	
				<div class="tabscontainer">
					<div class="heading">
						<div class="module-title">
						<img src="'.$this->_path.'logo.png" width="16" height="16" alt="" title="" />'.$this->l('Email Control').'
						</div>
						<div class="buttons_section">'.$message.'</div>
					</div>
					<div class="tabcontent" id="tab_content_10">
					<input type="radio" class="hide" name="tab_number" id="tab_10" value="10" />
						<div class="form-wrapper">
							<div class="margin form">
								<div class="form-group">
									<label class="control-label col-lg-3">Facebook</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_fb" id="email_fb_on" value="1" '.(($s["email_fb"] == 1) ? 'checked ' : '').'/>
											<label for="email_fb_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_fb" id="email_fb_off" value="0" '.(($s["email_fb"] == 0) ? 'checked ' : '').'/>
											<label for="email_fb_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">Facebook Link</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_fb_acc" id="email_fb_acc" value="'.$s["email_fb_acc"].'" />
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label class="control-label col-lg-3">Twitter</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_tw" id="email_tw_on" value="1" '.(($s["email_tw"] == 1) ? 'checked ' : '').'/>
											<label for="email_tw_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_tw" id="email_tw_off" value="0" '.(($s["email_tw"] == 0) ? 'checked ' : '').'/>
											<label for="email_tw_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">Twitter Link</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_tw_acc" id="email_tw_acc" value="'.$s["email_tw_acc"].'" />
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label class="control-label col-lg-3">Youtube</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_yt" id="email_yt_on" value="1" '.(($s["email_yt"] == 1) ? 'checked ' : '').'/>
											<label for="email_yt_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_yt" id="email_yt_off" value="0" '.(($s["email_yt"] == 0) ? 'checked ' : '').'/>
											<label for="email_yt_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">Youtube Link</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_yt_acc" id="email_yt_acc" value="'.$s["email_yt_acc"].'" />
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label class="control-label col-lg-3">Google+</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_gp" id="email_gp_on" value="1" '.(($s["email_gp"] == 1) ? 'checked ' : '').'/>
											<label for="email_gp_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_gp" id="email_gp_off" value="0" '.(($s["email_gp"] == 0) ? 'checked ' : '').'/>
											<label for="email_gp_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">Google+ Link</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_gp_acc" id="email_gp_acc" value="'.$s["email_gp_acc"].'" />
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label class="control-label col-lg-3">Emails</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_em" id="email_em_on" value="1" '.(($s["email_em"] == 1) ? 'checked ' : '').'/>
											<label for="email_em_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_em" id="email_em_off" value="0" '.(($s["email_em"] == 0) ? 'checked ' : '').'/>
											<label for="email_em_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">First email</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_em_acc" id="email_em_acc" value="'.$s["email_em_acc"].'" />
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">Second email</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_em_acc2" id="email_em_acc2" value="'.$s["email_em_acc2"].'" />
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label class="control-label col-lg-3">Skype</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_sk" id="email_sk_on" value="1" '.(($s["email_sk"] == 1) ? 'checked ' : '').'/>
											<label for="email_sk_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_sk" id="email_sk_off" value="0" '.(($s["email_sk"] == 0) ? 'checked ' : '').'/>
											<label for="email_sk_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">First Skype</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_sk_acc" id="email_sk_acc" value="'.$s["email_sk_acc"].'" />
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">Second Skype</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_sk_acc2" id="email_sk_acc2" value="'.$s["email_sk_acc2"].'" />
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label class="control-label col-lg-3">Mobile Phone</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_ph" id="email_ph_on" value="1" '.(($s["email_ph"] == 1) ? 'checked ' : '').'/>
											<label for="email_ph_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_ph" id="email_ph_off" value="0" '.(($s["email_ph"] == 0) ? 'checked ' : '').'/>
											<label for="email_ph_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">First Phone</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_ph_acc" id="email_ph_acc" value="'.$s["email_ph_acc"].'" />
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">Second Phone</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_ph_acc2" id="email_ph_acc2" value="'.$s["email_ph_acc2"].'" />
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label class="control-label col-lg-3">Other Phone</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_oph" id="email_oph_on" value="1" '.(($s["email_oph"] == 1) ? 'checked ' : '').'/>
											<label for="email_oph_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_oph" id="email_oph_off" value="0" '.(($s["email_oph"] == 0) ? 'checked ' : '').'/>
											<label for="email_oph_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">First Phone</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_oph_acc" id="email_oph_acc" value="'.$s["email_oph_acc"].'" />
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">Second Phone</label>
									<div class="col-lg-3">
										<input type="text" size="20" name="email_oph_acc2" id="email_oph_acc2" value="'.$s["email_oph_acc2"].'" />
									</div>
								</div>
								<hr/>
							</div>
							<div class="margin form">
								<div class="form-group">
									<label class="control-label col-lg-3">Advertising Image</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_adv" id="email_adv_on" value="1" '.(($s["email_adv"] == 1) ? 'checked ' : '').'/>
											<label for="email_adv_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_adv" id="email_adv_off" value="0" '.(($s["email_adv"] == 0) ? 'checked ' : '').'/>
											<label for="email_adv_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="variant" style="overflow:hidden">
									<label class="control-label col-lg-3"></label>
									<div class="col-lg-9">
								    <div class="swt_container'.(($s["email_adv"] == 0) ? ' hide' : '').'">
										<div class="back_image_container">
											'.(($s["email_image"] != "") ? '<img class="back_image" src="'.$imgPath.$s["email_image"].'">' : 'No Image').'
										</div>									
										<input id="email_image" type="file" name="email_image">
										<input id="eimage" type="submit" class="btn btn-default" name="email_image_upload">
										<input class="button'.(($s["email_image"] == "") ? ' hide' : '').'" type="submit" name="submitDeleteEmailImg" value="'.$this->l('Delete image').'" />
									</div>
									</div>
							    </div>	
							    <hr/>						    
							</div>
							<div class="margin form">
								<div class="form-group">
									<label class="control-label col-lg-3">Show Address</label>
									<div class="col-lg-3">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="email_addr" id="email_addr_on" value="1" '.(($s["email_addr"] == 1) ? 'checked ' : '').'/>
											<label for="email_addr_on">'.$this->trans('Yes', array(), 'Admin.Global').'</label>
											<input type="radio" name="email_addr" id="email_addr_off" value="0" '.(($s["email_addr"] == 0) ? 'checked ' : '').'/>
											<label for="email_addr_off">'.$this->trans('No', array(), 'Admin.Global').'</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
								<div class="form-group'.(($s["email_addr"] == 0) ? ' hide' : '').'">
									<label class="control-label col-lg-3">Address</label>
									<div class="col-lg-9">
										<textarea id="email_addr" name="email_addr_text" class="swt_container" cols="26" rows="3">'.$s["email_addr_text"].'</textarea>
									</div>
								</div>
								
							</div>
							<div class="panel-footer">
								<button type="submit" value="1" id="submitThemeSettings" name="submitThemeSettings" class="btn btn-default pull-right"><i class="process-icon-save"></i> '.$this->trans('Save', array(), 'Modules.Facebook.Admin').'</button>
							</div>

						</div>
					</div>					
				</div>				
			</div>
			</div>
		</form>';
	}

}