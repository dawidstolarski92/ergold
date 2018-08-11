<?php

class Pk_Testimonials extends Module
{
	private $_html;

	public function __construct()
	{
		$this->name = 'pk_testimonials';
		$this->version = '1.7';
		$this->author = 'promokit.eu';
		$this->bootstrap = true;

		parent::__construct();
		$this->displayName = 'Testimonials';
		$this->description = $this->trans('Create a customer testimonials', array(), 'Modules.Testimonials.Admin');
			
		$this->in = $this->name.'/views/templates/front/';
		$this->templateFiles = array(
			'addtestimonial' => 'module:'.$this->in.'addtestimonial.tpl',
			'column' => 'module:'.$this->in.'blocktestimonial-column.tpl',
			'blocktestimonial' => 'module:'.$this->in.'blocktestimonial.tpl',
			'displaytestimonials' => 'module:'.$this->in.'displaytestimonials.tpl',
			'testimonials' => 'module:'.$this->in.'testimonials.tpl'
		);
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

		$this->options = array(
			'testimonial_captcha' => '0',
			'testimonial_perpage' => '10',
			'testimonial_perblock' => '2',
            'testimonial_captcha_pub' => '12345',
            'testimonial_captcha_priv' => '678910',
            'testimonial_display_img' => '1',
            'testimonial_max_img' => '80'
		);

	}


	public function install() {

		foreach ($this->options as $key => $value) {
    		if (!Configuration::updateValue($key, $value))
    			return false;
    	}

 	 	if (parent::install() == false 
            OR $this->registerHook('displayLeftColumn') == false
			OR $this->registerHook('displayHeader') == false
			OR $this->registerHook('displayHome') == false
            OR $this->registerHook('content_bottom') == false
            OR $this->registerHook('content_top') == false
			OR $this->registerHook('displayBackOfficeHeader') == false
        	)
 	 		return false;
 	 	
	 	if (!Db::getInstance()->Execute('
		CREATE TABLE '._DB_PREFIX_.'testimonials (
			`testimonial_id` int(5) NOT NULL AUTO_INCREMENT,
			`testimonial_title` varchar(64) NOT NULL DEFAULT \'My Testimonial\',
			`testimonial_submitter_name` varchar(50) NOT NULL DEFAULT \'anonymous\',
			`testimonial_submitter_email` varchar(50) NOT NULL DEFAULT \'anonymous@mail.com\',
			`testimonial_main_message` text NOT NULL,
			`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		     `status` char(8) NOT NULL DEFAULT \'Disabled\',
			PRIMARY KEY(`testimonial_id`)
		) ENGINE=MyISAM default CHARSET=utf8'))
	 			return false;

	 		if (!Db::getInstance()->Execute("
	 		INSERT INTO `"._DB_PREFIX_."testimonials` VALUES (1, 'Summary', 'Marek', 'marek@mail.com', 'Ipsum dolor sit amet, consectetur adipiscing elit. Nulla interdum tincidunt felis, id mattis nisi mattis in. Etiam vehicula sem et augue mattis congue. Vivamus consequat congue purus, non imperdiet nulla rhoncus eu. Donec vehicula lor', '2014-05-15 15:49:24', 'Enabled');
		INSERT INTO `"._DB_PREFIX_."testimonials` VALUES (2, 'summary', 'Fred', 'fred@email.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla interdum tincidunt felis, id mattis nisi mattis in. Etiam vehicula sem et augue mattis congue. Vivamus consequat congue purus, non imperdiet nulla rhoncus eu. Donec vehicula lor', '2014-05-15 15:48:45', 'Enabled');"))
	 			return false;
	 		
	 		return true;
  	}

	public function uninstall() {

        if (!parent::uninstall() OR 
        	!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'testimonials`;'))
        return false;

    	foreach ($this->options as $key => $value) {
    		if (!Configuration::deleteByName($key))
    			return false;
    	}

        return true;
	}

    /**
	 Function for cleaning text input fields
	**/

    public function cleanInput ($text) {  //clean the inputs

		$text = trim($text);
		$text = strip_tags($text);
		$text = htmlspecialchars($text, ENT_QUOTES);

		return ($text); //output clean text

	}
		
		
    /**
	 Function for validating text input fields
	**/

	public function field_validator($field_descr, $field_data, $min_length="", $max_length="", $field_required=1) {

		$errors = array();
		if(!$field_data && !$field_required){ return; }

		# check for required fields
		if ($field_required && empty($field_data)) {
			return false;
		}

		# field data min length checking:
		if ($min_length) {
			if (strlen($field_data) < $min_length) {
				return false;
			}
		}

		 # field data max length checking:
		if ($max_length) {
			if (strlen($field_data) > $max_length) {
				return false;
			}
       	} else {
			echo "ok";       		
       	}
	   
		return true;
	
	}
	
	/** Function for check file ext **/
	public function checkImageExt() { 

		$allowedextlist = array('jpg', 'png', 'jpeg');
		$notallowedextlist = array('php', 'php3', 'php4', 'phtml','exe');
		$fileName = strtolower($_FILES['testimonial_img']['name']); //check the correct extension
		if(!in_array(end(explode('.', $fileName)), $allowedextlist)) {
			echo "false";
			return false;
		}

		return true;
              
	}		
	
	/** Function for uploading file **/
	
	public function uploadImage(){
	
		$uploadpath = "upload";

		//upload the files
		move_uploaded_file($_FILES["testimonial_img"]["tmp_name"],
		_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$uploadpath.DIRECTORY_SEPARATOR.$_FILES["testimonial_img"]["name"]);

		//store the path for displaying the image
		$testimonial_img = $uploadpath ."/".$_FILES["testimonial_img"]["name"];
		$testimonial_img = addslashes($testimonial_img);

		return $testimonial_img; //return image path 
			 
	}


    /** Function for checking file size **/

    public function checkfileSize() {

       $MAX_SIZE = (Configuration::get('testimonial_max_img') * 1024);
       
       if ( $_FILES["testimonial_img"]["size"] > $MAX_SIZE )
           return false;
       return true;
    }

	/** Function for writing testimonials **/
	
    public function writeTestimonial($testimonial_title, $testimonial_submitter_name, $testimonial_submitter_email, $testimonial_main_message) {

        $db = Db::getInstance();
		$result = $db->Execute('INSERT INTO `'._DB_PREFIX_.'testimonials` ( `testimonial_title`, `testimonial_submitter_name`, `testimonial_submitter_email`, `testimonial_main_message`) VALUES
			("'.$testimonial_title.'"
			,"'.$testimonial_submitter_name.'"
			,"'.$testimonial_submitter_email.'"
			,"'.$testimonial_main_message.'"
                )');

		if ($result)
			return true;

		return false;

    }


	public function displayTestimonials() {

		//if (!$this->isCached($this->templateFiles['displaytestimonials'], $this->getCacheId($this->name))) {

			$output = array(); // create an array named $output to store our testimonials. We will read the from the DB
			$nextpage = $prevpage = "";
			$db = Db::getInstance(); // create and object to represent the database
			$result = $db->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'testimonials`;'); // Query to count the total number of testimonials		
			if ($result == true) {
				$numrows = 0;
				foreach ($result as $key => $row) {
			 		$numrows++;
			 	}		
			} else {
				$numrows = 1;
			}
			
			// number of rows to show per page
			$rowsperpage = Configuration::get('testimonial_perpage');

			// find out total pages
			$totalpages = ceil($numrows / $rowsperpage);
			// get the current page or set a default
			if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
			   // cast var as int
			   $currentpage = (int) $_GET['currentpage'];
			} else {
			   // default page num
			   $currentpage = 1;
			} // end if

			// the offset of the list, based on current page 
			$offset = ($currentpage - 1) * $rowsperpage;
			
		   	// get the info from the db 
			$result = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'testimonials` WHERE status = "enabled" ORDER BY testimonial_id DESC LIMIT '.$offset.', '.$rowsperpage.';'); // Query to return the testimonials on that page
			// while there are rows to be fetched...
			foreach ($result as $key => $data) {
	     		$time = $result[$key]["date_added"];
	     		$t = explode(" ", $time);
	     		$result[$key]["date_added"] = date("d F Y", strtotime($t[0]));     		
	     	} 
			if ($result != false) {
				foreach ($result as $key => $row) {
					$results[] = $row;
					$time = $result[$key]["date_added"];
		     		$t = explode(" ", $time);
		     		$result[$key]["date_added"] = date("d F Y", strtotime($t[0]));  				 		
			 	}
			} else {
				$results[] = "";
			}
					 
		 	/****** pagination links ******/
			// range of num links to show
			$range = 3;

			// if not on page 1, don't show back links
			if ($currentpage > 1) {
			   // show << link to go back to page 1
			   
			   // get previous page num
			   $prevpage = $currentpage - 1;
			   // show < link to go back to 1 page
			} // end if 

			// if not on last page, show forward and last page links        
			if ($currentpage != $totalpages) {
			   // get next page
			   $nextpage = $currentpage + 1;
		
			} // end if
			/****** end pagination links ******/
			$this->smarty->assign(array(
				'http_host' => $_SERVER['HTTP_HOST'],
				'this_path' => $this->_path,
				'base_dir'=> __PS_BASE_URI__,
				'testimonials' => $results,
				'currentpage' => $currentpage,
				'prevpage' => $prevpage,
				'nextpage' => $nextpage,
				'totalpages' => $totalpages
			));
		//}
					  
		//return $this->fetch($this->templateFiles['displaytestimonials'], $this->getCacheId($this->name));
		return $this->fetch($this->templateFiles['displaytestimonials']);

	 }

	public function displayrandomTestimonial() {

		$result = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'testimonials` where status = "enabled" ORDER BY date_added DESC LIMIT '.Configuration::get('testimonial_perblock'));
		foreach ($result as $key => $data) {
			$time = $result[$key]["date_added"];
			$t = explode(" ", $time);
			$result[$key]["date_added"] = strftime('%d %B, %G', strtotime($t[0])); 		
			$result[$key]["avatar"] = $this->get_avatar($data["testimonial_submitter_email"]);     		
		}     	     	
		return $result;

	}    
      
    public function prepare_to_fetch($hook) {

		$smarty_opts = array(
			'displayImage' => Configuration::get('testimonial_display_img'),
			'this_path' => $this->_path,
			'hookn' => "wide-"
		);

		if (file_exists(dirname(__FILE__).'/assets/images/testimonial_bg_'.$this->context->shop->id.'.jpg')) {
				$smarty_opts['testimonial_bg'] = Tools::getShopProtocol().Context::getContext()->shop->domain.Context::getContext()->shop->physical_uri.'modules/'.$this->name.'/assets/images/testimonial_bg_'.$this->context->shop->id.'.jpg';
		}

		$testimonials = $this->displayrandomTestimonial();
        if (!empty($testimonials)) {

        	$smarty_opts['testims'] = $testimonials;

        } else {
			
			$smarty_opts['testimonial_submitter_name'] = '';	        	

        }

        $this->smarty->assign($smarty_opts);
        return true;

    }

	public function hookDisplayLeftColumn() { //display a block link to the front office testimonials page
	
        $testimonials = $this->displayrandomTestimonial();
        $this->smarty->assign('displayImage', 0);
        if (!empty($testimonials)) {
        	$this->smarty->assign(array(
				'this_path' => $this->_path,
				'testims' => $testimonials,
				'hookn' => 'leftcol'
			));
        } else {
        	$this->smarty->assign(array(
				'this_path' => $this->_path,
        		'testimonial_submitter_name' => '',
        		'hookn' => 'leftcol'
			));
        }
		return $this->fetch($this->templateFiles['column']);

	}

	public function hookRightColumn() {

		return $this->hookDisplayLeftColumn();

	}

	public function hookDisplayHome($params) {

        $params['hook'] = 'displayHome';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
        	$this->prepare_to_fetch($params['hook']);
            return $this->fetch($this->templateFiles['blocktestimonial']);
        }

    }   

    public function hookcontent_top($params) {

        $params['hook'] = 'content_top';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
        	$this->prepare_to_fetch($params['hook']);
            return $this->fetch($this->templateFiles['blocktestimonial']);
        }

    }

    public function hookcontent_bottom($params) {
        $params['hook'] = 'content_bottom';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
        	$this->prepare_to_fetch($params['hook']);
            return $this->fetch($this->templateFiles['blocktestimonial']);
        }

    }
	
	public function hookDisplayHeader() {

		$this->page_name = Dispatcher::getInstance()->getController();
		if (intval(Configuration::get('testimonial_captcha') == 1) && ($this->page_name == 'addtestimonial'))
			$this->context->controller->registerJavascript('http://www.google.com/recaptcha/api/js/recaptcha_ajax.js', ['position' => 'bottom', 'priority' => 150]);

		//if ($this->page_name == 'addtestimonial')
			$this->context->controller->registerJavascript($this->name, 'modules/'.$this->name.'/assets/js/scripts.js', ['position' => 'bottom', 'priority' => 150]);

		$this->context->controller->registerStylesheet($this->name, 'modules/'.$this->name.'/assets/css/styles.css', ['media' => 'all', 'priority' => 150]);
        

	}
	 

	public function getContent() {

		if (isset($_POST['Enable']) OR isset($_POST['Disable']) OR isset($_POST['Delete']) OR isset($_POST['Update'])  OR isset($_POST['submitConfig']) OR isset($_POST['Backup'])) { 
			$this->_postProcess();
		}

		$this->_html = $this->_displayConfigForm();
		$this->_html .= $this->getadminTestimonials();
		$this->context->controller->addJS($this->_path.'assets/js/admin.js');
		return $this->_html;
    }

	private function _postProcess() {

          if (Tools::isSubmit('submitConfig')) {

          		$id_shop = (int)$this->context->shop->id;
                $reCaptcha = Tools::getValue('reCaptcha');
                if ($reCaptcha != 0 AND $reCaptcha != 1)
                    $output .= '<div class="alert error">'.$this->trans('recaptcha : Invalid choice', array(), 'Modules.Testimonials.Admin').'</div>';
                else
                    Configuration::updateValue('testimonial_captcha', intval($reCaptcha));

				$recaptchaPub = strval(Tools::getValue('recaptchaPub'));

				if (!$recaptchaPub OR empty($recaptchaPub))
               		$this->_html .= '<div class="alert error">'.$this->trans('Please enter your public key', array(), 'Modules.Testimonials.Admin').'</div>';

				else
					Configuration::updateValue('testimonial_captcha_pub', strval($recaptchaPub));

				$recaptchaPriv = strval(Tools::getValue('recaptchaPriv'));
               
				if (!$recaptchaPriv OR empty($recaptchaPriv))
					$this->_html .= '<div class="alert error">'.$this->trans('Please enter your private key', array(), 'Modules.Testimonials.Admin').'</div>';
				else
					Configuration::updateValue('testimonial_captcha_priv', strval($recaptchaPriv));

				$perPage = strval(Tools::getValue('perPage'));

                if (!$perPage OR empty($perPage))
                	$this->_html .= '<div class="alert error">'.$this->trans('Please enter the amount of testimonials per page', array(), 'Modules.Testimonials.Admin').'</div>';
                else
                	Configuration::updateValue('testimonial_perpage', strval($perPage));

				$perBlock = strval(Tools::getValue('perBlock'));

				if (!$perBlock OR empty($perBlock))
					$this->_html .= '<div class="alert error">'.$this->trans('Please enter the amount of testimonials in the module', array(), 'Modules.Testimonials.Admin').'</div>';
				else
					Configuration::updateValue('testimonial_perblock', strval($perBlock));

				$displayImage = strval(Tools::getValue('displayImage'));

				if ($displayImage != 0 AND $displayImage != 1)
					$this->_html .= '<div class="alert error">'.$this->trans('Please select whether to allow users to upload the Testimonial Image', array(), 'Modules.Testimonials.Admin').'</div>';
				else
					Configuration::updateValue('testimonial_display_img', strval($displayImage));

				if (isset($_FILES['testimonialsbg']) && isset($_FILES['testimonialsbg']['tmp_name']) && !empty($_FILES['testimonialsbg']['tmp_name'])) {

					$img = dirname(__FILE__).'/assets/images/testimonial_bg_'.(int)$id_shop.'.jpg';

					if (file_exists($img))
						unlink($img);
					
					if ($error = ImageManager::validateUpload($_FILES['testimonialsbg']))
						$errors .= $error;

					elseif (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['testimonialsbg']['tmp_name'], $tmp_name))
						return false;			

					elseif (!ImageManager::resize($tmp_name, $img))
						$errors .= $this->displayError($this->trans('An error occurred while attempting to upload the image', array(), 'Admin.Notifications.Error'));

					if (isset($tmp_name))
						unlink($tmp_name);

				}

				parent::_clearCache($this->templateFiles['blocktestimonial']);

			}

			if (isset($_POST['Backup'])) {
                $result = Db::getInstance()->ExecuteS("SELECT * from `"._DB_PREFIX_."testimonials`");
                if ($result == true) {

                	$filename = dirname(__FILE__).'/backup.csv';
                    $fp = fopen($filename, 'w');

                	foreach ($result as $key => $res) {
                        fputcsv($fp,$res);                                                
                	}

                	$this->_html .= $this->displayConfirmation($this->trans('The .CSV file has been successfully exported', array(), 'Admin.Notifications.Success') );
                	fclose($fp);
                }

                else
                    $this->_html .= $this->displayError($this->trans('No Testimonials to Backup', array(), 'Admin.Notifications.Error'));
			}

			if (isset($_POST['Delete'])) {
			
				foreach($_POST['moderate'] as $check => $val) {
                 $deleted=Db::getInstance()->Execute('
                 DELETE FROM `'._DB_PREFIX_.'testimonials`
                 WHERE testimonial_id =  "'.($val).'"
                 ');
             	}
           	}

         	if (isset($_POST['Enable'])) {

                 foreach($_POST['moderate'] as  $check => $val){
                     $enabled=Db::getInstance()->Execute('
                     UPDATE `'._DB_PREFIX_.'testimonials`
                     SET `status` = "Enabled"
                     WHERE `testimonial_id` = "'.($val).'"');
                 }
             }

			if (isset($_POST['Disable'])) {

				foreach($_POST['moderate'] as  $check => $val) {

					$disabled=Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'testimonials`
						SET `status` = "Disabled"
						WHERE `testimonial_id` = "'.($val).'"');
				}
			}
			   
			if (isset($_POST['Update'])) {
				foreach($_POST['moderate'] as  $check => $val) {

					$testimonial_main_message =  "testimonial_main_message_".$val;
					$testimonial_main_message = $_POST[$testimonial_main_message];

					$update=Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'testimonials`
						SET `testimonial_main_message` = "'.$testimonial_main_message.'"
						WHERE `testimonial_id` = "'.($val).'"');
				}
			}
			   
			   
		return $this->_html;

     }
	 
	public function backupFile(){  //check if backup file exists
		if (file_exists(dirname(__FILE__).'/backup.csv'))
			return true;
		return false;
	}
	 
	 
	public function _displayConfigForm(){

 		$rev = date("H").date("i").date("s")."\n";

 		if (file_exists(dirname(__FILE__).'/assets/images/testimonial_bg_'.$this->context->shop->id.'.jpg'))
 			$this->smarty->assign('testimonial_bg', $this->_path.'assets/images/testimonial_bg_'.$this->context->shop->id.'.jpg?'.$rev);
 		else 
 			$this->smarty->assign('testimonial_bg', $this->_path.'assets/images/demo.jpg');

		$this->smarty->assign('base_dir', __PS_BASE_URI__);
		$this->smarty->assign('requestUri', $_SERVER['REQUEST_URI']);
		$this->smarty->assign('recaptcha', Configuration::get('testimonial_captcha'));
		$this->smarty->assign('recaptchaPriv', Configuration::get('testimonial_captcha_priv'));
		$this->smarty->assign('recaptchaPub', Configuration::get('testimonial_captcha_pub'));
		$this->smarty->assign('recaptchaPerpage', Configuration::get('testimonial_perpage'));
		$this->smarty->assign('recaptchaPerBlock', Configuration::get('testimonial_perblock'));
        $this->smarty->assign('maximagesize', Configuration::get('testimonial_max_img'));
        $this->smarty->assign('displayImage', Configuration::get('testimonial_display_img'));
        $this->smarty->assign('backupfileExists', $this->backupFile());

		return $this->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/displayadmincfgForm.tpl');

	}


	public function getadminTestimonials() {

		$results = null;
		$testimonials = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'testimonials` ORDER BY date_added DESC');
		 // while there are rows to be fetched...
	 	foreach ($testimonials as $key => $testimonial) {
	 		$results[] = $testimonial;
	 	}
		
		$this->smarty->assign(array(
              'testimonials' => $results,
		      'requestUri', $_SERVER['REQUEST_URI'],
              'http_host', $_SERVER['HTTP_HOST'],
		      'base_dir', __PS_BASE_URI__,
		      'this_path' => $this->_path
		));
		return $this->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/displayadmintestimonialsForm.tpl');

    }

    public function hookDisplayBackOfficeHeader() {
		// Check if module is loaded
		if (Tools::getValue('configure') != $this->name)
			return false;

		// CSS
		$this->context->controller->addCSS($this->_path.'assets/css/admin.css');
		
	}

	public function get_avatar( $email, $size = '70', $default = 'mystery', $alt = false ) {

		if ( false === $alt)
			$safe_alt = '';
		else
			$safe_alt = esc_attr( $alt );

		if ( !is_numeric($size) )
			$size = '96';

		if ( !empty($email) )
			$email_hash = md5( strtolower( trim( $email ) ) );

		if ( Tools::usingSecureMode() ) {
			$host = 'https://secure.gravatar.com';
		} else {
			if ( !empty($email) )
				$host = sprintf( "http://%d.gravatar.com", ( hexdec( $email_hash[0] ) % 2 ) );
			else
				$host = 'http://0.gravatar.com';
		}

		if ( 'mystery' == $default )
			$default = "$host/avatar/ad516503a11cd5ca435acc9bb6523536"; // ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
		elseif ( 'blank' == $default )
			$default = includes_url('images/blank.gif');
		elseif ( !empty($email) && 'gravatar_default' == $default )
			$default = '';
		elseif ( 'gravatar_default' == $default )
			$default = "$host/avatar/?s={$size}";
		elseif ( empty($email) )
			$default = "$host/avatar/?d=$default&amp;s={$size}";
		elseif ( strpos($default, 'http://') === 0 )
			$default = add_query_arg( 's', $size, $default );

		if ( !empty($email) ) {
			$out = "$host/avatar/";
			$out .= $email_hash;
			//$out .= '?s='.$size;
			$out .= '&amp;d=' . urlencode( $default );

			$avatar = "<img alt='{$safe_alt}' src='{$out}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
		} else {
			$avatar = "<img alt='{$safe_alt}' src='{$default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
		}

		return $avatar;
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

}
?>