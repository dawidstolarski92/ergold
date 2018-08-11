<?php

if (!defined('_PS_VERSION_'))
	exit;

class pk_instafeed extends Module
{
	private $_html = '';
	private $_postErrors = array();

    function __construct()
    {
		$this->name = 'pk_instafeed';
		$this->version = '1.4';
		$this->author = 'promokit.eu';

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->trans('Instagram Feed', array(), 'Modules.instafeed.Admin');
		$this->description = $this->trans('Shows instagram images by hashtag.', array(), 'Modules.instafeed.Admin');
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => '1.7.9');
		$this->templateFile = 'module:pk_instafeed/pk_instafeed.tpl';

		if (Tools::usingSecureMode())
			$domain = Tools::getShopDomainSsl(true);
		else
			$domain = Tools::getShopDomain(true);

		$this->redirect_url = $domain.__PS_BASE_URI__.'modules/'.$this->name.'/api.php';

		$this->check_state = true;
        if (Module::isInstalled('pk_themesettings')) {
            require_once _PS_MODULE_DIR_.'pk_themesettings/inc/common.php';
            $this->check_state = new Pk_ThemeSettings_Common();
        }
	}

	public function install()
	{	

		if (
			parent::install() == false
			|| $this->registerHook('displayHeader') == false
			|| $this->registerHook('content_top')
			|| $this->registerHook('content_bottom')
            || $this->registerHook('displayHome')		
			|| Configuration::updateValue('PK_INSTA_API_CODE', "7ed9053c49574ad3949e41c8a3fd5e8a") == false
			|| Configuration::updateValue('PK_INSTA_AT', "") == false
			|| Configuration::updateValue('PK_INSTA_TEMP_CODE', "") == false
			|| Configuration::updateValue('PK_INSTA_CONTENT_TYPE', "tagged") == false
			|| Configuration::updateValue('PK_INSTA_REDIRECT_URL', $this->redirect_url) == false
			|| Configuration::updateValue('PK_INSTA_HASHTAG', "sky") == false
			|| Configuration::updateValue('PK_INSTA_API_SECRET', "ae0e9bb78fd043ce9f32182381dd4cc8") == false
			|| Configuration::updateValue('PK_INSTA_USERNAME', "prestashop") == false
			|| Configuration::updateValue('PK_INSTA_API_CALLBACK', "http://localhost") == false
			|| Configuration::updateValue('PK_INSTA_SORTBY', "none") == false
			|| Configuration::updateValue('PK_INSTA_NUMBER', "10") == false	
			|| Configuration::updateValue('PK_INSTA_NUMBER_VIS', "4") == false
			|| Configuration::updateValue('PK_INSTA_LINKS', true) == false
			|| Configuration::updateValue('PK_INSTA_LIKES', true) == false
			|| Configuration::updateValue('PK_INSTA_COMMENTS', true) == false
			|| Configuration::updateValue('PK_INSTA_CAPTION', true) == false
			|| Configuration::updateValue('PK_INSTA_CAROUSEL', true) == false
			|| Configuration::updateValue('PK_INSTA_BACKGROUND', false) == false
			|| Configuration::updateValue('PK_INSTA_AUTOSCROLL', true) == false
			|| Configuration::updateValue('PK_INSTA_COLOR', false) == false
			)
			return false;
		return true;	
	}
	
	public function uninstall()
	{
		return 
			Configuration::deleteByName('PK_INSTA_API_CODE') &&
			Configuration::deleteByName('PK_INSTA_AT') &&
			Configuration::deleteByName('PK_INSTA_CONTENT_TYPE') &&
			Configuration::deleteByName('PK_INSTA_TEMP_CODE') &&
			Configuration::deleteByName('PK_INSTA_REDIRECT_URL') &&
			Configuration::deleteByName('PK_INSTA_HASHTAG') &&
			Configuration::deleteByName('PK_INSTA_API_SECRET') &&
			Configuration::deleteByName('PK_INSTA_API_CALLBACK') &&
			Configuration::deleteByName('PK_INSTA_USERNAME') &&
			Configuration::deleteByName('PK_INSTA_SORTBY') &&
			Configuration::deleteByName('PK_INSTA_NUMBER') &&
			Configuration::deleteByName('PK_INSTA_NUMBER_VIS') &&
			Configuration::deleteByName('PK_INSTA_LINKS') &&
			Configuration::deleteByName('PK_INSTA_LIKES') &&
			Configuration::deleteByName('PK_INSTA_COMMENTS') &&
			Configuration::deleteByName('PK_INSTA_CAPTION') &&
			Configuration::deleteByName('PK_INSTA_CAROUSEL') &&
			Configuration::deleteByName('PK_INSTA_BACKGROUND') &&
			Configuration::deleteByName('PK_INSTA_AUTOSCROLL') &&
			Configuration::deleteByName('PK_INSTA_COLOR') &&
			parent::uninstall();
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

		if (Tools::isSubmit('pk_ig_submit')) {
			Configuration::updateValue('PK_INSTA_API_CODE', Tools::getValue('PK_INSTA_API_CODE'));
			Configuration::updateValue('PK_INSTA_AT', Tools::getValue('PK_INSTA_AT'));
			Configuration::updateValue('PK_INSTA_TEMP_CODE', Tools::getValue('PK_INSTA_TEMP_CODE'));
			Configuration::updateValue('PK_INSTA_CONTENT_TYPE', Tools::getValue('PK_INSTA_CONTENT_TYPE'));
			Configuration::updateValue('PK_INSTA_REDIRECT_URL', Tools::getValue('PK_INSTA_REDIRECT_URL'));
			Configuration::updateValue('PK_INSTA_HASHTAG', 	str_replace('#', '', Tools::getValue('PK_INSTA_HASHTAG')));
			Configuration::updateValue('PK_INSTA_API_SECRET', Tools::getValue('PK_INSTA_API_SECRET'));
			Configuration::updateValue('PK_INSTA_API_CALLBACK', Tools::getValue('PK_INSTA_API_CALLBACK'));
			Configuration::updateValue('PK_INSTA_USERNAME', Tools::getValue('PK_INSTA_USERNAME'));
			Configuration::updateValue('PK_INSTA_SORTBY', Tools::getValue('PK_INSTA_SORTBY'));
			Configuration::updateValue('PK_INSTA_NUMBER', Tools::getValue('PK_INSTA_NUMBER'));
			Configuration::updateValue('PK_INSTA_NUMBER_VIS', Tools::getValue('PK_INSTA_NUMBER_VIS'));
			Configuration::updateValue('PK_INSTA_LINKS', Tools::getValue('PK_INSTA_LINKS'));
			Configuration::updateValue('PK_INSTA_LIKES', Tools::getValue('PK_INSTA_LIKES'));
			Configuration::updateValue('PK_INSTA_COMMENTS', Tools::getValue('PK_INSTA_COMMENTS'));
			Configuration::updateValue('PK_INSTA_CAPTION', Tools::getValue('PK_INSTA_CAPTION'));	
			Configuration::updateValue('PK_INSTA_CAROUSEL', Tools::getValue('PK_INSTA_CAROUSEL'));	
			Configuration::updateValue('PK_INSTA_BACKGROUND', Tools::getValue('PK_INSTA_BACKGROUND'));
			Configuration::updateValue('PK_INSTA_AUTOSCROLL', Tools::getValue('PK_INSTA_AUTOSCROLL'));
			Configuration::updateValue('PK_INSTA_COLOR', Tools::getValue('PK_INSTA_COLOR'));
			$output .= $this->displayConfirmation($this->trans('Settings updated', array(), 'Admin.Notifications.Success'));
			$id_shop = (int)$this->context->shop->id;

			if (isset($_FILES['insta-bg']) && isset($_FILES['insta-bg']['tmp_name']) && !empty($_FILES['insta-bg']['tmp_name'])) {
				$img = dirname(__FILE__).'/img/instabg_'.$id_shop.'.jpg';
				if (file_exists($img))
					unlink($img);
				
				if ($error = ImageManager::validateUpload($_FILES['insta-bg']))
					$output .= $error;

				elseif (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['insta-bg']['tmp_name'], $tmp_name))
					return false;			

				elseif (!ImageManager::resize($tmp_name, $img))
					$output .= $this->displayError($this->trans('An error occurred while attempting to upload the image', array(), 'Admin.Notifications.Error'));

				if (isset($tmp_name))
					unlink($tmp_name);

			}

			$at = Tools::getValue('PK_INSTA_AT');
			$client_id = Tools::getValue('PK_INSTA_API_CODE');
			$verif = Tools::getValue('PK_INSTA_TEMP_CODE');
			$url = Tools::getValue('PK_INSTA_REDIRECT_URL');
			$secret = Tools::getValue('PK_INSTA_API_SECRET');

			if (empty($at)) {
				if ($client_id != "" && $verif != "" && $url != "" && $secret != "") {
					$resp = $this->instagram_authorize($url, $client_id, $verif, $secret);
					if (isset($resp->error_message)) {
						$output .= $this->displayError($resp->error_message.' Try to change "Redirect URL" to "http://localhost" in both settings and than copy Matching code from address bar', array(), 'Admin.Notifications.Error');
					} else {
						Configuration::updateValue('PK_INSTA_AT', $resp->access_token);
					}
				}
			}

			if (empty($verif)) {
				if ($client_id != "" && $url != "") {
					$this->get_verification_code($client_id, $url);
				}
			}

			parent::_clearCache($this->templateFile);

		}
		$img = "";
		$rev = date("H").date("i").date("s");
		if (file_exists(dirname(__FILE__).'/img/instabg_'.$this->context->shop->id.'.jpg'))
			$img = '<div class="panel"><div class="panel-heading"><i class="icon-cogs"></i>&nbsp;Instagram Background Image</div><div class="form-wrapper"><div class="form-group" id="instabg" style="overflow:hidden"><div class="col-lg-12"><div class="form-group"><div class="col-sm-6"><img src="'.$this->_path.'img/instabg_'.$this->context->shop->id.'.jpg?'.$rev.' alt="" style="max-width:400px; height:auto; width:auto; height:150px;" /></div></div></div></div></div></div>';

		return $output.$this->renderForm().$img;
	}

	public function hookdisplayHome($params) {

        $params['hook'] = 'displayHome';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->prepare_to_fetch();
			return $this->fetch($this->templateFile);
        }

    }   

    public function hookcontent_top($params) {

        $params['hook'] = 'content_top';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->prepare_to_fetch();
			return $this->fetch($this->templateFile);
        }

    }

    public function hookcontent_bottom($params) {

        $params['hook'] = 'content_bottom';
        $status = $this->check_state(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->prepare_to_fetch();
			return $this->fetch($this->templateFile);
        }

    }

	public function prepare_to_fetch() {

		//if (!$this->isCached($this->templateFile, $this->getCacheId($this->name))) {

			$bgimg = "";
			if (file_exists(dirname(__FILE__).'/img/instabg_'.$this->context->shop->id.'.jpg'))
				$bgimg = "img/instabg_".$this->context->shop->id.".jpg";

			$this->context->smarty->assign(array(
				'pk_ig' => $this->getValuesFromDB(),
				'pk_ig_suffix' => "middle",
				'this_path' => $this->_path,
				'insta_bg' => $bgimg
			));

		//}
		
	}

	public function hookHeader($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {
			$this->context->controller->addJS(($this->_path).'assets/js/instafeed.min.js');
			$this->context->controller->addJS(($this->_path).'assets/js/init.js');
			$this->context->controller->addCSS(($this->_path).'assets/css/styles.css', 'all');
		}
	}

	public function renderForm()
	{
		$fields_form_01 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->trans('Instagram Personal Data', array(), 'Modules.instafeed.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->trans('Redirect URL', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_REDIRECT_URL',
						'class' => 'fixed-width-xxl',
						'required' => false,
						'desc' => $this->trans('Put this redirect URL to your Instagram Client Settings', array(), 'Modules.instafeed.Admin')
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Client ID', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_API_CODE',
						'class' => 'fixed-width-xxl',
						'required' => true,
						'desc' => $this->trans('Put your Instagram Client ID', array(), 'Modules.instafeed.Admin')
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Client Secret', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_API_SECRET',
						'class' => 'fixed-width-xxl',
						'required' => true,
						'desc' => $this->trans('Put your Instagram Client Secret', array(), 'Modules.instafeed.Admin')
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Matching Code', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_TEMP_CODE',
						'class' => 'fixed-width-xxl',
						'desc' => $this->trans('Leave this field empty and click to "Save" button. You will be redirected to a page with generated Matching code. Copy that code, click to "Back" link and paste it to this field', array(), 'Modules.instafeed.Admin')
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Access Token', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_AT',
						'class' => 'fixed-width-xxl access-token',
						'desc' => $this->trans('Access Token generates automatically when Matching code exist', array(), 'Modules.instafeed.Admin')
					),	
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
				)
			),
		);
		$fields_form_02 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->trans('Module Appearance', array(), 'Modules.instafeed.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(					
					array(
						'type' => 'select',
						'label' => $this->trans('Feed content by', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_CONTENT_TYPE',
						'class' => 'fixed-width-xxl',
						'desc' => $this->trans('Display images by hashtag or by username', array(), 'Modules.instafeed.Admin'),
						'options' => array(
							'query' => array(
								array(
									'id' => 'tagged',
									'name' => $this->trans('Hashtag', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'user',
									'name' => $this->trans('Username', array(), 'Modules.instafeed.Admin')),
							),
							'id' => 'id',
							'name' => 'name'
						)
					),			
					array(
						'type' => 'text',
						'label' => $this->trans('Hashtag #', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_HASHTAG',
						'class' => 'fixed-width-xxl',
						'desc' => $this->trans('Name of a hashtag to get. Please notice! You can show images by hashtag only from your Instagram account', array(), 'Modules.instafeed.Admin')
					),	
					array(
						'type' => 'text',
						'label' => $this->trans('Username', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_USERNAME',
						'class' => 'fixed-width-xxl',
						'desc' => $this->trans('Your instagram username', array(), 'Modules.instafeed.Admin')
					),
					array(
						'type' => 'select',
						'label' => $this->trans('Feed content', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_SORTBY',
						'class' => 'fixed-width-xxl',
						'desc' => $this->trans('Sort the images in a set order', array(), 'Modules.instafeed.Admin'),
						'options' => array(
							'query' => array(
								array(
									'id' => 'none',
									'name' => $this->trans('None', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'most-recent',
									'name' => $this->trans('Most Recent', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'least-recent',
									'name' => $this->trans('Least Recent', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'most-liked',
									'name' => $this->trans('Most Liked', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'least-liked',
									'name' => $this->trans('Least Liked', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'most-commented',
									'name' => $this->trans('Most Commented', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'least-commented',
									'name' => $this->trans('Least Commented', array(), 'Modules.instafeed.Admin')),
								array(
									'id' => 'random',
									'name' => $this->trans('Random', array(), 'Modules.instafeed.Admin')),
							),
							'id' => 'id',
							'name' => 'name'
						)
					),	
					array(
						'type' => 'text',
						'label' => $this->trans('Number of images', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_NUMBER',
						'class' => 'fixed-width-xxl',
						'desc' => $this->trans('How many images you want to take from instagram', array(), 'Modules.instafeed.Admin')
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Visible images', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_NUMBER_VIS',
						'class' => 'fixed-width-xxl',
						'desc' => $this->trans('How many images you want to see in carousel', array(), 'Modules.instafeed.Admin')
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Images with links', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_LINKS',
						'desc' => $this->trans('Wrap the images with a link to the photo on Instagram', array(), 'Modules.instafeed.Admin'),
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->trans('Yes', array(), 'Admin.Global')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->trans('No', array(), 'Admin.Global')
									)
								),
						),
					array(
						'type' => 'switch',
						'label' => $this->trans('Show image likes number', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_LIKES',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->trans('Yes', array(), 'Admin.Global')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->trans('No', array(), 'Admin.Global')
									)
								),
						),
					array(
						'type' => 'switch',
						'label' => $this->trans('Show image comments number', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_COMMENTS',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->trans('Yes', array(), 'Admin.Global')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->trans('No', array(), 'Admin.Global')
									)
								),
						),
					array(
						'type' => 'switch',
						'label' => $this->trans('Show image captions', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_CAPTION',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->trans('Yes', array(), 'Admin.Global')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->trans('No', array(), 'Admin.Global')
									)
								),
						),	
					array(
						'type' => 'switch',
						'label' => $this->trans('Display images in carousel', array(), 'Modules.instafeed.Admin'),
						'desc' => $this->trans('Use carousel or just a list of instagram images', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_CAROUSEL',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->trans('Yes', array(), 'Admin.Global')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->trans('No', array(), 'Admin.Global')
									)
								),
						),		
					array(
						'type' => 'switch',
						'label' => $this->trans('Carousel autorotate', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_AUTOSCROLL',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->trans('Yes', array(), 'Admin.Global')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->trans('No', array(), 'Admin.Global')
									)
								),
						),	
					array(
						'type' => 'switch',
						'label' => $this->trans('Light color of the text', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_COLOR',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->trans('Yes', array(), 'Admin.Global')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->trans('No', array(), 'Admin.Global')
									)
								),
						),	
					array(
						'type' => 'switch',
						'label' => $this->trans('Display background for module', array(), 'Modules.instafeed.Admin'),
						'name' => 'PK_INSTA_BACKGROUND',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->trans('Yes', array(), 'Admin.Global')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->trans('No', array(), 'Admin.Global')
									)
								),
						),
					array(
						'type' => 'file',
						'label' => $this->trans('Background Image', array(), 'Modules.instafeed.Admin'),
						'name' => 'insta-bg',
						'value' => true
					),			
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
				)
			),
		);

		$bkimg = "";
		if (file_exists(dirname(__FILE__).'/img/instabg_'.$this->context->shop->id.'.jpg'))
			$bkimg = ShopUrl::getBaseURI()."/modules".$this->name."/img/instabg_".$this->context->shop->id.".jpg";
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'pk_ig_submit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getValuesFromDB(),
			'insta-bg' => $bkimg
		);
		return $helper->generateForm(array($fields_form_01, $fields_form_02));
	}

	public function getValuesFromDB()
	{

		return array(
			'PK_INSTA_API_CODE' => (Configuration::get('PK_INSTA_API_CODE') ? Configuration::get('PK_INSTA_API_CODE'): ""),
			'PK_INSTA_REDIRECT_URL' => (Configuration::get('PK_INSTA_REDIRECT_URL') ? Configuration::get('PK_INSTA_REDIRECT_URL'): ""),
			'PK_INSTA_AT' => (Configuration::get('PK_INSTA_AT') ? Configuration::get('PK_INSTA_AT'): ""),
			'PK_INSTA_TEMP_CODE' => (Configuration::get('PK_INSTA_TEMP_CODE') ? Configuration::get('PK_INSTA_TEMP_CODE'): ""),
			'PK_INSTA_CONTENT_TYPE' => (Configuration::get('PK_INSTA_CONTENT_TYPE') ? Configuration::get('PK_INSTA_CONTENT_TYPE'):""),
			'PK_INSTA_HASHTAG' => (Configuration::get('PK_INSTA_HASHTAG') ? Configuration::get('PK_INSTA_HASHTAG'): ""),
			'PK_INSTA_API_SECRET' => (Configuration::get('PK_INSTA_API_SECRET') ? Configuration::get('PK_INSTA_API_SECRET'): ""),
			'PK_INSTA_API_CALLBACK' => (Configuration::get('PK_INSTA_API_CALLBACK') ? Configuration::get('PK_INSTA_API_CALLBACK'): ""),			
			'PK_INSTA_USERNAME' => (Configuration::get('PK_INSTA_USERNAME') ? Configuration::get('PK_INSTA_USERNAME'): ""),
			'PK_INSTA_SORTBY' => (Configuration::get('PK_INSTA_SORTBY') ? Configuration::get('PK_INSTA_SORTBY'): ""),
			'PK_INSTA_NUMBER' => (Configuration::get('PK_INSTA_NUMBER') ? Configuration::get('PK_INSTA_NUMBER'): ""),
			'PK_INSTA_NUMBER_VIS' => (Configuration::get('PK_INSTA_NUMBER_VIS') ? Configuration::get('PK_INSTA_NUMBER_VIS'): ""),
			'PK_INSTA_LINKS' => (Configuration::get('PK_INSTA_LINKS') ? Configuration::get('PK_INSTA_LINKS'): ""),
			'PK_INSTA_LIKES' => (Configuration::get('PK_INSTA_LIKES') ? Configuration::get('PK_INSTA_LIKES'): ""),
			'PK_INSTA_COMMENTS' => (Configuration::get('PK_INSTA_COMMENTS') ? Configuration::get('PK_INSTA_COMMENTS'): ""),
			'PK_INSTA_CAPTION' => (Configuration::get('PK_INSTA_CAPTION') ? Configuration::get('PK_INSTA_CAPTION'): ""),
			'PK_INSTA_CAROUSEL' => (Configuration::get('PK_INSTA_CAROUSEL') ? Configuration::get('PK_INSTA_CAROUSEL'): ""),
			'PK_INSTA_BACKGROUND' => (Configuration::get('PK_INSTA_BACKGROUND') ? Configuration::get('PK_INSTA_BACKGROUND'): 0),
			'PK_INSTA_AUTOSCROLL' => (Configuration::get('PK_INSTA_AUTOSCROLL') ? Configuration::get('PK_INSTA_AUTOSCROLL'): ""),
			'PK_INSTA_COLOR' => (Configuration::get('PK_INSTA_COLOR') ? Configuration::get('PK_INSTA_COLOR'): ""),
			'PK_INSTA_USERID' => $this->getuserid()	
		);
	}

	public function getuserid() {		

		$at = Configuration::get('PK_INSTA_AT');
		if (empty($at)) {
			return 0;
		} else {
			$explode = explode('.', $at);
			if (is_numeric($explode[0])) {
				return $explode[0];
			} else {
				return 0;
			}
		}

	}

	public function get_verification_code($client_id, $redirect_uri) {
		header('Location: https://api.instagram.com/oauth/authorize/?' . http_build_query(array(
	        'client_id' => $client_id,
	        'redirect_uri' => $redirect_uri,
	        'response_type' => 'code',
	        'scope' => 'basic public_content'
	    )));
	    exit;	
	}

	public function instagram_authorize($redirect_uri, $client_id, $code, $client_secret) {

		$apiData = array(
		  'client_id'       => $client_id,
		  'client_secret'   => $client_secret,
		  'grant_type'      => 'authorization_code',
		  'redirect_uri'    => $redirect_uri,
		  'code'            => $code
		);

		$apiHost = 'https://api.instagram.com/oauth/access_token';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiHost);
		curl_setopt($ch, CURLOPT_POST, count($apiData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$jsonData = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($jsonData);
		return $response;
		
	}

}