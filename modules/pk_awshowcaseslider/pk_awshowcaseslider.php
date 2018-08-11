<?php

if (!defined('_PS_VERSION_'))
  exit;

class pk_awshowcaseslider extends Module 
{
	protected $maxImageSize = 1048576;

	public function __construct()
	    {
		    $this->name = 'pk_awshowcaseslider';
		    $this->version = '1.3.5';
		    $this->author = 'promokit.eu';
		    $this->need_instance = 1;
			$this->secure_key = Tools::encrypt($this->name);
			$this->bootstrap = true;
	
		    parent::__construct();

		    $this->displayName = $this->trans('Awkward Showcase Slider', array(), 'Modules.pk_awshowcaseslider.Admin');
		    $this->description = $this->trans('Powerfull image slider with points of interest for advertising', array(), 'Modules.pk_awshowcaseslider.Admin');

		    // Paths
			$this->module_path 		= _PS_MODULE_DIR_.$this->name.'/';
			$this->admin_tpl_path 	= _PS_MODULE_DIR_.$this->name.'/views/templates/admin/';
			$this->front_tpl_path	= _PS_MODULE_DIR_.$this->name.'/views/templates/front/';
			$this->hooks_tpl_path	= _PS_MODULE_DIR_.$this->name.'/views/templates/hooks/';
	    }
	
	private function installDB()
		{
	
			Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pk_awshowcaseslider`');
    		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pk_awshowcaseslider_options`');
	
			if (!Db::getInstance()->Execute('
				CREATE TABLE `'._DB_PREFIX_.'pk_awshowcaseslider` (
					`id_slide` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_shop` int(10) unsigned NOT NULL,
					`id_lang` int(10) unsigned NOT NULL,
					`id_order` int(10) unsigned NOT NULL,
					`lang_iso` VARCHAR(5),
					`title` VARCHAR(100),
					`url` VARCHAR(100),
					`video_url` VARCHAR(100),
					`video` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`target` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`image` VARCHAR(100),
					`alt` VARCHAR(100),
					`caption` VARCHAR(300),
					`active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					PRIMARY KEY (`id_slide`, `id_shop`)
			    ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;'))
				return false;

			if (!Db::getInstance()->Execute('
				CREATE TABLE `'._DB_PREFIX_.'pk_awshowcaseslider_points` (
					`id_rec` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_coord` VARCHAR(100),
					`id_shop` int(10) unsigned NOT NULL,
					`id_lang` int(10) unsigned NOT NULL,
					`id_slide` int(10) unsigned NOT NULL,
					`coordinateX` int(10) unsigned NOT NULL,
					`coordinateY` int(10) unsigned NOT NULL,
					`point_type` VARCHAR(10),
					`product_name` VARCHAR(100),
					`product_link_rewrite` VARCHAR(100),
					`product_image` VARCHAR(100),
					`product_image_link` VARCHAR(400),
					`id_product` int(10),
					`point_text` VARCHAR(100),
					PRIMARY KEY (`id_rec`)
			    ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;'))
				return false;
	
			if (!Db::getInstance()->Execute('
				CREATE TABLE `'._DB_PREFIX_.'pk_awshowcaseslider_options` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_shop` int(10) unsigned NOT NULL,
					`effect` VARCHAR(300),
					`current` VARCHAR(300),
					`slices` int(3) NOT NULL DEFAULT \'15\',
					`cols` int(3) NOT NULL DEFAULT \'8\',
					`rows` int(3) NOT NULL DEFAULT \'4\',
					`speed` int(4) NOT NULL DEFAULT \'1000\',
					`pause` int(4) NOT NULL DEFAULT \'3500\',
					`manual` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					`hover` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					`buttons` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`control` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					`thumbnail` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`random` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`start_slide` int(2) unsigned NOT NULL DEFAULT 0,
					`single` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					`width` int(4) unsigned NOT NULL DEFAULT \'0\',
					`height` int(4) unsigned NOT NULL DEFAULT \'0\',
					`front` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					PRIMARY KEY (`id`, `id_shop`)
		        ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;'))
				return false;	
			return true;
		}
	
	private function insertOptions()
	{
		$id_shop = (int)$this->context->shop->id;
		$activeLanguages = Language::getLanguages(true);

		if (!Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'pk_awshowcaseslider_options` (
				`id_shop`, `effect`
			) VALUES (
				'.$id_shop.', "horizontal,vertical,fade");'))
			return false;	

		foreach($activeLanguages as $lang) {
			$sql = "INSERT INTO `"._DB_PREFIX_."pk_awshowcaseslider` (`id_shop`, `id_lang`, `id_order`, `lang_iso`, `title`, `url`, `video_url`, `video`, `target`, `image`, `alt`, `caption`, `active`) VALUES
				(".$id_shop.", ".$lang['id_lang'].", 1, '".$lang['iso_code']."', 'Don’t miss out!', '#', '', 0, 0, 'slide01.jpg', '', '<h4>mid season sale</h4>\r\n<p>Donec varius gravida nulla, non vehicula turpis bibendum vitae aliquam nisi est...</p>\r\n<a href=\"#\" class=\"btn\">Shop Now</a>', 1),
				(".$id_shop.", ".$lang['id_lang'].", 2, '".$lang['iso_code']."', 'Discover the New', '#', '', 0, 0, 'slide02.jpg', '', '<h4>Spring Collection</h4>\r\n<p>Lorem ipsum dolor sit amet, consectetur dipiscing elit. Donec libero metus etiam erat tortor...</p>\r\n<a href=\"#\" class=\"btn\">Take a Look</a>', 1);";
			if (!Db::getInstance()->Execute($sql))
					return false;
		}
		return true;
	}
	
	public function install()
	    {
			if (parent::install() && 
				$this->installDB() && 
				$this->insertOptions() && 
				$this->registerHook('displayHeader') &&
				$this->registerHook('content_top') &&
				$this->registerHook('content_bottom') &&
	            $this->registerHook('displayHome') &&
				$this->registerHook('displayBackOfficeHeader')){
				return true;
			}else{
				$this->uninstall();
				return false;
			}
		}
	
	public function uninstall()
		{
			$image = Db::getInstance()->ExecuteS('SELECT image FROM `'._DB_PREFIX_.'pk_awshowcaseslider`');
	
			//foreach($image as $img)
				//$this->_deleteImages($img['image']);
	
			if (!Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pk_awshowcaseslider`') OR
	    		!Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pk_awshowcaseslider_options`') OR
	    		!Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pk_awshowcaseslider_points`') OR
				!parent::uninstall())
				return false;
			return true;	
		}		
	
	public function getContent()
		{
			//$this->context->smarty->assign('error', false);
			//$this->context->smarty->assign('confirmation', 0);

			if (Tools::isSubmit('submitMinicOptions')){
				$this->_handleOptions();
			} elseif (Tools::isSubmit('submitNewSlide')){
				$this->_handleNewSlide();
			} elseif (Tools::isSubmit('editSlide')){
				$this->_handleEditSlide();
			} elseif (Tools::isSubmit('deleteSlide')) {
				$this->_handleDeleteSlide();
			}
			return $this->_displayForm();
		}
	
	private function _displayForm() {	

		$defaultLanguage = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
		$activeLanguages = Language::getLanguages(true);
		$allLanguages = Language::getLanguages(false);
		$id_shop = (int)$this->context->shop->id;
		$options = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'pk_awshowcaseslider_options`');
		$slides = array();
		$coordinates = array();

		foreach($activeLanguages as $k=>$lang){
			$slides[$lang['iso_code']] = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'pk_awshowcaseslider` WHERE id_lang ='.$lang['id_lang'].' AND id_shop = '.$id_shop.' ORDER BY id_order ASC');
			$coordinates[$lang['iso_code']] = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'pk_awshowcaseslider_points` WHERE id_lang ='.$lang['id_lang'].' AND id_shop = '.$id_shop);				
		}
		

		$this->context->smarty->assign('slider', array(
			'options' => array(
				'effect' => (!empty($options['effect'])) ? explode(',', $options['effect']) : NULL,
				'current' => (!empty($options['current'])) ? explode(',', $options['current']) : NULL,
				'slices' => $options['slices'],
				'cols' => $options['cols'],
				'rows' => $options['rows'],
				'speed' => $options['speed'],
				'pause' => $options['pause'],
				'manual' => $options['manual'],
				'hover' => $options['hover'],
				'buttons' => $options['buttons'],
				'control' => $options['control'],
				'thumbnail' => $options['thumbnail'],
				'random' => $options['random'],
				'startSlide' => $options['start_slide'],
				'single' => $options['single'],
				'width' => $options['width'],
				'height' => $options['height'],
				'front' => $options['front']
			),
			'slides' => $slides,
			'coordinates' => $coordinates,
			'lang' => array(
				'default' => $defaultLanguage,
				'default_iso' => $defaultLanguage,
				'default_name' => $defaultLanguage,
				'all' => $activeLanguages,
				'lang_dir' => _THEME_LANG_DIR_,
				'user' => $this->context->language->id
			),				
			'tpl' => array(
            	'options' => _PS_MODULE_DIR_.$this->name.'/views/templates/admin/admin-options.tpl',
            	'new' => _PS_MODULE_DIR_.$this->name.'/views/templates/admin/admin-new.tpl',
            	'slides' => _PS_MODULE_DIR_.$this->name.'/views/templates/admin/admin-slides.tpl',
            	'feedback' => _PS_MODULE_DIR_.$this->name.'/views/templates/admin/admin-feedback.tpl',
            	'bug' => _PS_MODULE_DIR_.$this->name.'/views/templates/admin/admin-bug.tpl'
        	),
			'postAction' => 'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module=advertising_marketing&module_name='.$this->name.'',
			'sortUrl' => _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/ajax_'.$this->name.'.php?action=updateOrder&secure_key='.$this->secure_key,
			'id_shop' => (int)$this->context->shop->id,
			'rootpath' => $this->_path
		));	

		$this->smarty->assign('minic', array(
			'admin_tpl_path' => $this->admin_tpl_path,
			'front_tpl_path' => $this->front_tpl_path,
			'hooks_tpl_path' => $this->hooks_tpl_path,

			'info' => array(
				'module'	=> $this->name,
            	'name'      => Configuration::get('PS_SHOP_NAME'),
        		'domain'    => Configuration::get('PS_SHOP_DOMAIN'),
        		'email'     => Configuration::get('PS_SHOP_EMAIL'),
        		'version'   => $this->version,
            	'psVersion' => _PS_VERSION_,
        		'server'    => $_SERVER['SERVER_SOFTWARE'],
        		'php'       => phpversion(),
        		'mysql' 	=> Db::getInstance()->getVersion(),
        		'theme' 	=> _THEME_NAME_,
        		'userInfo'  => $_SERVER['HTTP_USER_AGENT'],
        		'today' 	=> date('Y-m-d'),
        		'module'	=> $this->name,
        		'context'	=> (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 0) ? 1 : ($this->context->shop->getTotalShops() != 1) ? $this->context->shop->getContext() : 1,
			)
		));

		return $this->display(__FILE__, 'views/templates/admin/admin.tpl');
	}

	private function _handleOptions()
		{		
			$id_shop = (int)$this->context->shop->id;
			$effects = Tools::getValue('nivo_effect');
			if (!empty($effects)) {
				$effect = implode(',', Tools::getValue('nivo_effect'));
			} else {
				$effect = "";
			}
			$current = '';
			if(Tools::getValue('nivo_current') != '')
				$current = implode(',', Tools::getValue('nivo_current'));
		
			if(!Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'pk_awshowcaseslider_options` SET 
					effect = "'.$effect.'",
					current = "'.$current.'",
					slices = "'.(int)Tools::getValue('slices').'",
					cols = "'.(int)Tools::getValue('cols').'",
					rows = "'.(int)Tools::getValue('rows').'",
					speed = "'.(int)Tools::getValue('speed').'",
					pause = "'.(int)Tools::getValue('pause').'",
					manual = "'.(int)Tools::getValue('manual').'",
					hover = "'.(int)Tools::getValue('hover').'",
					buttons = "'.(int)Tools::getValue('buttons').'",
					control = "'.(int)Tools::getValue('control').'",
					thumbnail = "'.(int)Tools::getValue('thumbnail').'",					
					random = "'.(int)Tools::getValue('random').'",
					start_slide = "'.(int)Tools::getValue('startSlide').'",
					single = "'.(int)Tools::getValue('single').'",
					width = "'.(int)Tools::getValue('width').'",
					height = "'.(int)Tools::getValue('height').'",
					front = "'.(int)Tools::getValue('front').'" 
				WHERE id = 1
					')){
				$this->context->smarty->assign('error', $this->trans('An error occurred while saving data. I`m sure this is a DataBase error', array(), 'Modules.pk_awshowcaseslider.Admin'));
				return false;
			}

			$this->context->smarty->assign('confirmation', $this->trans('Saved successfull', array(), 'Modules.pk_awshowcaseslider.Admin'));		
			return true;
		}
	
	private function _handleNewSlide()
		{
			$languages = Language::getLanguages(false);		
			$id_lang = (int)Tools::getValue('language');
			$lang = Language::getLanguage($id_lang);
			$id_shop = (int)$this->context->shop->id;
			$lastSlideID = Db::getInstance()->ExecuteS('SELECT id_slide, id_order FROM `'._DB_PREFIX_.'pk_awshowcaseslider` WHERE id_lang = '.$id_lang.' AND id_shop = '.$id_shop.' ORDER BY id_slide DESC LIMIT 1');
			$currentSlideID = ($lastSlideID) ? $lastSlideID[0]['id_slide']+1 : 1;
			$currentOrderID = ($lastSlideID) ? $lastSlideID[0]['id_order']+1 : 1 ;
		
			if(!empty($_FILES['image']['name'])){
			//	$this->context->smarty->assign('error', $this->trans('Image needed, please choose one.'));
			//	return false;
				$image = $this->_resizer($_FILES['image'], Tools::getValue('imageName'));
				if(!$image)
					return false;
			} else {
				$image = "";
				}
						
		
			$insert = Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'pk_awshowcaseslider` ( 
					id_shop, id_lang, id_order, lang_iso, title, url, target, image, alt, caption 
				) VALUES ( 
					"'.$id_shop.'",
					"'.(int)Tools::getValue('language').'",
					"'.$currentOrderID.'",
					"'.$lang['iso_code'].'",
					"'.Tools::getValue('title').'",
					"'.Tools::getValue('url').'",
					"'.(int)Tools::getValue('target').'",
					"'.$image.'",
					"'.Tools::getValue('alt').'",
					"'.pSQL(Tools::getValue('caption'), true).'")
				');

			if(!$insert){
				$this->_deleteImages($image);
				$this->context->smarty->assign('error', $this->trans('An error occured while saving data', array(), 'Modules.pk_awshowcaseslider.Admin'));	
				return false;	
			}	
		
			$this->context->smarty->assign('confirmation', $this->trans('New slide added successfull', array(), 'Modules.pk_awshowcaseslider.Admin'));
		}
	private function _handleEditSlide()
		{	
			$langIso = Tools::getValue('slideIso');
			$newImage = '';
		
			if(!empty($_FILES['newImage']['name'])){
				$image = $this->_resizer($_FILES['newImage']);
				if(empty($image))
					return false;
				$newImage = 'image = "'.$image.'",';
			}
		
			$update = Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'pk_awshowcaseslider` SET 
					title = "'.Tools::getValue('title').'",
					url = "'.Tools::getValue('url').'",
					video_url = "'.Tools::getValue('video_url').'",
					video = "'.(int)Tools::getValue('video').'",
					target = "'.(int)Tools::getValue('target').'",
					'.$newImage.'
					alt = "'.Tools::getValue('alt').'",
					caption = "'.pSQL(Tools::getValue('caption'), true).'",
					active = "'.(int)Tools::getValue('isActive').'"
				WHERE id_slide = '.(int)Tools::getValue('slideId'));
		
			if(!$update){
				$this->_deleteImages(Tools::getValue('image'));			
				$this->context->smarty->assign('error', $this->trans('An error occured while saving data', array(), 'Modules.pk_awshowcaseslider.Admin'));	
				return false;			
			}
		
			if(!empty($_FILES['newImage']['name'])){
				$this->_deleteImages(Tools::getValue('oldImage'));
			}
		
			$this->context->smarty->assign('confirmation', $this->trans('Saved succsessfull', array(), 'Modules.pk_awshowcaseslider.Admin'));
		}
	
	public function _handleDeleteSlide()
		{
			$id_shop = (int)$this->context->shop->id;
			Db::getInstance()->delete(_DB_PREFIX_.'pk_awshowcaseslider', 'id_slide = '.(int)Tools::getValue('slideId'));
		
			if(Db::getInstance()->Affected_Rows() == 1){
				Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'pk_awshowcaseslider` 
					SET id_order = id_order-1 
					WHERE (
						id_order > '.Tools::getValue('orderId').' AND 
						lang_iso = "'.Tools::getValue('slideIso').'" AND 
						id_shop = '.$id_shop.')
				');
		
				$this->_deleteImages(Tools::getValue('oldImage'));
				$this->context->smarty->assign('confirmation', $this->trans('Deleted succsessfull', array(), 'Modules.pk_awshowcaseslider.Admin'));
			}else{
				$this->context->smarty->assign('error', $this->trans('Cant delete slide data from database', array(), 'Modules.pk_awshowcaseslider.Admin'));
			}
		}
	
	private function _resizer($image, $newName = NULL)
		{

			$path = '../modules/'.$this->name.'/uploads/';
			$pathThumb = '../modules/'.$this->name.'/uploads/thumbs/';	

			// Check if thumb dir is exists and create if not
			if(!file_exists($pathThumb) && !is_dir($pathThumb))
				mkdir($pathThumb);

			// Replace whitesapce
			$imageName = explode('.', str_replace(' ', '_', $image['name']));
			$name = $imageName[0].'.'.$imageName[1];
			// Replace unwanted chars
			if($newName){
				$unwanted_chars = array(
					'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                    'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                    'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                    'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                    'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'Ğ'=>'G', 'İ'=>'I', 'Ş'=>'S', 'ğ'=>'g', 'ı'=>'i', 
                    'ş'=>'s', 'ü'=>'u',
                );
				$nameB = strtr( $newName, $unwanted_chars );
				$name = str_replace(' ', '_', $nameB).'.'.$imageName[1];
			}

			// if new name is empty and picture is exists create a new name
			if(file_exists($path.$name) && $newName == NULL){
				$name = $imageName[0].date('-i-s').'.'.$imageName[1];
			}

			// Check image size and format
			if($error = ImageManager::validateUpload($image, $this->maxImageSize)){
				$this->context->smarty->assign('error', $error);
				return;
			}

			// Move image
			if(!ImageManager::resize($image['tmp_name'], dirname(__FILE__).'/uploads/'.$name)){
				$this->context->smarty->assign('error', $this->trans('An error occured during the upload, please check the permissions', array(), 'Modules.pk_awshowcaseslider.Admin'));
				unlink($tmpName);
				return;
			}			

			// Create thumbnail for slider
			$imgSize = getimagesize($path.$name);
			if($imgSize[0] >= $imgSize[1]){
				// Resize based on width
				$imgWidth = 116;
				$imgHeight = $imgWidth*$imgSize[1]/$imgSize[0];
			}else{
				// Resize based on height
				$imgHeight = 88;
				$imgWidth = ($imgSize[0]/100)*(5000/$imgSize[1]);
			}

			// Actual resize
			if(!ImageManager::resize($path.$name, $pathThumb.$name, (int)$imgWidth, (int)$imgHeight)){
				$this->context->smarty->assign('error', $this->trans('An error occurred during the image upload. Please check the upload directory permission in the module folder', array(), 'Modules.pk_awshowcaseslider.Admin'));
				return;
			}

			return $name;
		}	
	
	public function getImg($id, $link) {
		$cover = Image::getCover($id);
		$imgUrl = $this->context->link->getImageLink($link, $cover["id_image"], 'home_default');
		return $imgUrl;
	}
	public function getProdCover($id) {
		return Image::getCover($id);
	}
	
	private function _deleteImages($image)
		{

			$path = '../modules/'.$this->name.'/uploads/';
			$pathThumb = '../modules/'.$this->name.'/uploads/thumbs/';	
			
			if(file_exists($path.$image)){
				if(!unlink($path.$image) || !unlink($pathThumb.$image))
					$this->context->smarty->assign('error', $this->trans('Cant delete images, please check permissions', array(), 'Modules.pk_awshowcaseslider.Admin'));			
			}else{
				//$this->context->smarty->assign('error', $this->trans('Image doesn`t exists!'));
			}
		}
	
	public function hookDisplayBackOfficeHeader()
	{
		// Check if module is loaded
		if (Tools::getValue('configure') != $this->name)
			return false;

		// CSS
		//$this->context->controller->addCSS($this->_path.'views/css/elusive-icons/elusive-webfont.css');
		$this->context->controller->addCSS($this->_path.'views/js/plugins/tipsy/tipsy.css');
		$this->context->controller->addCSS($this->_path.'views/css/style.css');
		$this->context->controller->addCSS($this->_path.'views/css/admin.css');
		// JS
		$this->context->controller->addJquery();
		$this->context->controller->addJS($this->_path.'views/js/plugins/jquery.transit/jquery.transit-0.9.9.min.js');
		$this->context->controller->addJS($this->_path.'views/js/plugins/tipsy/jquery.tipsy.js');
		$this->context->controller->addJS($this->_path.'views/js/jquery-ui-1.9.0.custom.min.js');
		$this->context->controller->addJS($this->_path.'views/js/admin.js');

	}

	public function getData($position) {

		$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$id_shop = $this->context->shop->id;

		$options = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'pk_awshowcaseslider_options`');

		if($options['single'] == 1)
			$id_lang = $this->context->language->id;
		$slides = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'pk_awshowcaseslider` WHERE (id_lang ='.$id_lang.' AND id_shop = '.$id_shop.' AND active = 1) ORDER BY id_order ASC');		
		$coordinates = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'pk_awshowcaseslider_points` WHERE id_lang ='.$id_lang.' AND id_shop = '.$id_shop);	

		foreach ($coordinates as $id => $data) { // add price to array				
			if ($data['id_product'] != "") {
				$coordinates[$id]['price'] = Product::getPriceStatic($data['id_product']);
				$coordinates[$id]['product_name'] = substr($coordinates[$id]['product_name'], 0, 13)."...";
			}
		}
		

		$this->context->smarty->assign('slides', $slides);
		$this->context->smarty->assign('coordinates', $coordinates);		
		$this->context->smarty->assign('minicSlider', array(
			'options' => array(
				'current' => $options['current'],
				'slices' => $options['slices'],
				'cols' => $options['cols'],
				'rows' => $options['rows'],
				'speed' => $options['speed'],
				'pause' => $options['pause'],
				'manual' => $options['manual'],
				'hover' => $options['hover'],
				'buttons' => $options['buttons'],
				'control' => $options['control'],
				'thumbnail' => $options['thumbnail'],
				'random' => $options['random'],
				'startSlide' => $options['start_slide'],
				'single' => $options['single'],
				'width' => $options['width'],
				'height' => $options['height'],
				'front' => $options['front']
			),
			'path' => array(
				//'images' => $this->_path.'uploads/',
				//'thumbs' => $this->_path.'uploads/thumbs/'		
				'images' => '//'.Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/uploads/',
				'thumbs' => '//'.Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/uploads/thumbs/'			
			),
			'position' => $position
		));

	}

	public function hookDisplayHeader() {
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {
			$this->context->controller->addCSS($this->_path.'views/js/plugins/jquery.bxslider/jquery.bxslider.css');
			$this->context->controller->addJS($this->_path.'views/js/plugins/jquery.bxslider/jquery.bxslider-mod.js');
			$this->context->controller->addJS($this->_path.'views/js/plugins/jquery.bxslider/init.js');
		}
	}

	public function hookdisplayHome($params) {

        $params['hook'] = 'displayHome';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getData($params);
	 		return $this->fetch('module:'.$this->name.'/views/templates/front/front.tpl');
        }

    }   

    public function hookcontent_top($params) {

        $params['hook'] = 'content_top';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getData($params);
	 		return $this->fetch('module:'.$this->name.'/views/templates/front/front.tpl');
        }

    }

    public function hookcontent_bottom($params) {

        $params['hook'] = 'content_bottom';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getData($params);
	 		return $this->fetch('module:'.$this->name.'/views/templates/front/front.tpl');
        }

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