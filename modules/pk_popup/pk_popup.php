<?php

if (!defined('_PS_VERSION_'))
	exit;

class Pk_Popup extends Module
{
	private $_html = '';
	private $_postErrors = array();
	const GUEST_NOT_REGISTERED = -1;
	const CUSTOMER_NOT_REGISTERED = 0;
	const GUEST_REGISTERED = 1;
	const CUSTOMER_REGISTERED = 2;

    function __construct() {

		$this->name = 'pk_popup';
		$this->version = '1.2';
		$this->author = 'promokit.eu';
		$this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->trans('Popup Message', array(), 'Modules.Popup.Admin');
		$this->description = $this->trans('Shows popup window with your message', array(), 'Modules.Popup.Admin');

	}

	public function install() {		

		$this->context->controller->getLanguages();
		foreach ($this->context->controller->_languages as $language)
			Configuration::updateValue('PK_POPUP_TEXT_'.(int)$language['id_lang'] = (int)$language['id_lang'], '');

		if (
			parent::install() == false
			|| $this->registerHook('displayHeader') == false
			|| $this->registerHook('displayFooter') == false
			|| Configuration::updateValue('PK_POPUP_WIDTH', 770) == false
			|| Configuration::updateValue('PK_POPUP_HEIGHT', 460) == false
			|| Configuration::updateValue('PK_POPUP_NEWSLETTER', true) == false
			|| Configuration::updateValue('PK_POPUP_BG', true) == false
			)
			return false;
		return true;	
	}
	
	public function uninstall()
	{
		$this->context->controller->getLanguages();
		foreach ($this->context->controller->_languages as $language)
			Configuration::deleteByName('PK_POPUP_TEXT_'.(int)$language['id_lang'] = (int)$language['id_lang']);

		return 
			Configuration::deleteByName('PK_POPUP_WIDTH') &&
			Configuration::deleteByName('PK_POPUP_HEIGHT') &&
			Configuration::deleteByName('PK_POPUP_NEWSLETTER') &&
			Configuration::deleteByName('PK_POPUP_BG') &&
			parent::uninstall();
	}

	public function getContent()
	{

		$this->context->controller->getLanguages();
		$css = "<style>#popup-bg-images-thumbnails img {max-width:400px; height:auto;}</style>";
		$output = '';
		if (Tools::isSubmit('pk_popup_submit')) {

			Configuration::updateValue('PK_POPUP_WIDTH', (int)Tools::getValue('PK_POPUP_WIDTH'));
			Configuration::updateValue('PK_POPUP_HEIGHT', (int)Tools::getValue('PK_POPUP_HEIGHT'));
			Configuration::updateValue('PK_POPUP_NEWSLETTER', (bool)Tools::getValue('PK_POPUP_NEWSLETTER'));
			Configuration::updateValue('PK_POPUP_BG', (bool)Tools::getValue('PK_POPUP_BG'));

			foreach ($this->context->controller->_languages as $language)
				Configuration::updateValue('PK_POPUP_TEXT_'.(int)$language['id_lang'], Tools::getValue('PK_POPUP_TEXT_'.(int)$language['id_lang']));

			$output .= $this->displayConfirmation($this->trans('Settings updated', array(), 'Admin.Notifications.Success'));
			$id_shop = (int)$this->context->shop->id;

			if (isset($_FILES['popup-bg']) && isset($_FILES['popup-bg']['tmp_name']) && !empty($_FILES['popup-bg']['tmp_name'])) {
				$img = dirname(__FILE__).'/assets/img/popupbg_'.$id_shop.'.jpg';
				if (file_exists($img))
					unlink($img);
				
				if ($error = ImageManager::validateUpload($_FILES['popup-bg']))
					$errors .= $error;

				elseif (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['popup-bg']['tmp_name'], $tmp_name))
					return false;			

				elseif (!ImageManager::resize($tmp_name, $img))
					$errors .= $this->displayError($this->trans('An error occurred while attempting to upload the image', array(), 'Admin.Notifications.Error'));

				if (isset($tmp_name))
					unlink($tmp_name);

			}
			$this->_clearCache($this->name.'.tpl');

		}
		return $css.$output.$this->renderForm();
	}

	public function hookDisplayFooter($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {
			//if (!$this->isCached($this->templateFile, $this->getCacheId($this->name))) {
				$bkimg = "";
				$rev = date("H").date("i").date("s")."\n";
				if (file_exists(dirname(__FILE__).'/assets/img/popupbg_'.$this->context->shop->id.'.jpg'))
					$bkimg = "assets/img/popupbg_".$this->context->shop->id.".jpg";
				$this->context->smarty->assign(array(
					'pk_ppp' => $this->getConfigFromDB(),
					'popup_bg' => $bkimg
				));		
			//}
			//return $this->fetch($this->templateFile, $this->getCacheId($this->name));
			return $this->fetch($this->templateFile);
		}
	}

	public function hookDisplayHeader($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {
			$this->context->controller->addJqueryPlugin('fancybox');
			$this->context->controller->addJS($this->_path.'assets/js/scripts.js');
			$this->context->controller->addCSS($this->_path.'assets/css/styles.css', 'all');
		}
	}

	public function renderForm()
	{

		$rev = date("H").date("i").date("s")."\n";
		$bkimg = "";
		if (file_exists(dirname(__FILE__).'/assets/img/popupbg_'.$this->context->shop->id.'.jpg'))
			$bkimg = $this->_path."/assets/img/popupbg_".$this->context->shop->id.".jpg?".$rev;

		$fields_form = array(
			'form' => array(
				'tinymce' => true,
				'legend' => array(
					'title' => $this->trans('Module Appearance', array(), 'Modules.Popup.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(							
					array(
						'type' => 'text',
						'label' => $this->trans('Width of popup window', array(), 'Modules.Popup.Admin'),
						'name' => 'PK_POPUP_WIDTH',
						'class' => 'fixed-width-xxl'
					),	
					array(
						'type' => 'text',
						'label' => $this->trans('Height of popup window', array(), 'Modules.Popup.Admin'),
						'name' => 'PK_POPUP_HEIGHT',
						'class' => 'fixed-width-xxl'
					),/*
					array(
						'type' => 'textarea',
						'label' => $this->trans('Popup content', array(), 'Modules.Popup.Admin'),
						'name' => 'PK_POPUP_TEXT',
						'rows' => 10,
						'cols' => 40,
						'lang' => true,
						'class' => 'rte',
                    	'autoload_rte' => true,
					),*/
					array(
						'type' => 'switch',
						'label' => $this->trans('Show Newsletter form in popup', array(), 'Modules.Popup.Admin'),
						'name' => 'PK_POPUP_NEWSLETTER',
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
						'label' => $this->trans('Show background image', array(), 'Modules.Popup.Admin'),
						'name' => 'PK_POPUP_BG',
						'is_bool' => true,
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => true,
										'label' => $this->trans('Yes', array(), 'Admin.Global')
									),
									array(
										'id' => 'active_off',
										'value' => false,
										'label' => $this->trans('No', array(), 'Admin.Global')
									)
								),
						),
					array(
						'type' => 'file',
						'label' => $this->trans('Background Image', array(), 'Modules.Popup.Admin'),
						'name' => 'popup-bg',
						'value' => true,
						'thumb' => $bkimg
					),			
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
				)
			),
		);
		

		$languages = Language::getLanguages(false);
		foreach ($languages as $k => $language)
			$languages[$k]['is_default'] = (int)$language['id_lang'] == Configuration::get('PS_LANG_DEFAULT');

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->languages = $languages;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->allow_employee_form_lang = true;
		$helper->toolbar_scroll = true;
		$helper->toolbar_btn = $this->initToolbar();
		$helper->title = $this->displayName;
		$helper->submit_action = 'pk_popup_submit';
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'popup-bg' => $bkimg
		);
		return $helper->generateForm(array($fields_form));
	}

	private function initToolbar()
	{
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->trans('Save', array(), 'Admin.Actions')
		);

		return $this->toolbar_btn;
	}
	
	public function getConfigFieldsValues()
	{
		$values = array(
			'PK_POPUP_WIDTH' => Tools::getValue('PK_POPUP_WIDTH', Configuration::get('PK_POPUP_WIDTH')),
			'PK_POPUP_HEIGHT' => Tools::getValue('PK_POPUP_HEIGHT', Configuration::get('PK_POPUP_HEIGHT')),
			'PK_POPUP_NEWSLETTER' => Tools::getValue('PK_POPUP_NEWSLETTER', Configuration::get('PK_POPUP_NEWSLETTER')),
			'PK_POPUP_BG' => Tools::getValue('PK_POPUP_BG', Configuration::get('PK_POPUP_BG')),
		);

		$this->context->controller->getLanguages();
		foreach ($this->context->controller->_languages as $language)
			$values['PK_POPUP_TEXT'][$language['id_lang']] = Configuration::get('PK_POPUP_TEXT_'.$language['id_lang']);

		return $values;
	}

	public function getConfigFromDB()
	{
		$lid = $this->context->language->id;
		return array(
			'PK_POPUP_WIDTH' => (Configuration::get('PK_POPUP_WIDTH') ? Configuration::get('PK_POPUP_WIDTH'): "400"),
			'PK_POPUP_HEIGHT' => (Configuration::get('PK_POPUP_HEIGHT') ? Configuration::get('PK_POPUP_HEIGHT'): "400"),
			'PK_POPUP_NEWSLETTER' => (Configuration::get('PK_POPUP_NEWSLETTER') ? Configuration::get('PK_POPUP_NEWSLETTER'): false),
			'PK_POPUP_TEXT' => (Configuration::get('PK_POPUP_TEXT_'.$lid) ? Configuration::get('PK_POPUP_TEXT_'.$lid): false),
			'PK_POPUP_BG' => (Configuration::get('PK_POPUP_BG') ? Configuration::get('PK_POPUP_BG'): 0),
			'PK_POPUP_PATH' => Tools::getShopProtocol().Context::getContext()->shop->domain.Context::getContext()->shop->physical_uri.'modules/pk_popup/ajax.php'
		);
	}

	/**
	 * Check if this mail is registered for newsletters
	 *
	 * @param string $customer_email
	 *
	 * @return int -1 = not a customer and not registered
	 *                0 = customer not registered
	 *                1 = registered in block
	 *                2 = registered in customer
	 */
	private function isNewsletterRegistered($customer_email)
	{
		$sql = 'SELECT `email`
                FROM '._DB_PREFIX_.'emailsubscription
                WHERE `email` = \''.pSQL($customer_email).'\'
                AND id_shop = '.$this->context->shop->id;

        if (Db::getInstance()->getRow($sql)) {
            return self::GUEST_REGISTERED;
        }

        $sql = 'SELECT `newsletter`
                FROM '._DB_PREFIX_.'customer
                WHERE `email` = \''.pSQL($customer_email).'\'
                AND id_shop = '.$this->context->shop->id;

        if (!$registered = Db::getInstance()->getRow($sql)) {
            return self::GUEST_NOT_REGISTERED;
        }

        if ($registered['newsletter'] == '1') {
            return self::CUSTOMER_REGISTERED;
        }

        return self::CUSTOMER_NOT_REGISTERED;
	}

	/**
	 * Return true if the registered status correspond to a registered user
	 *
	 * @param int $register_status
	 *
	 * @return bool
	 */
	protected function isRegistered($register_status)
	{
		return in_array(
			$register_status,
			array(self::GUEST_REGISTERED, self::CUSTOMER_REGISTERED)
		);
	}

	/**
	 * Subscribe a guest to the newsletter
	 *
	 * @param string $email
	 * @param bool   $active
	 *
	 * @return bool
	 */
	protected function registerGuest($email, $active = true)
	{
		$sql = 'INSERT INTO '._DB_PREFIX_.'emailsubscription (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
                VALUES
                ('.$this->context->shop->id.',
                '.$this->context->shop->id_shop_group.',
                \''.pSQL($email).'\',
                NOW(),
                \''.pSQL(Tools::getRemoteAddr()).'\',
                (
                    SELECT c.http_referer
                    FROM '._DB_PREFIX_.'connections c
                    WHERE c.id_guest = '.(int) $this->context->customer->id.'
                    ORDER BY c.date_add DESC LIMIT 1
                ),
                '.(int) $active.'
                )';

        return Db::getInstance()->execute($sql);
	}

	/**
	 * Return a token associated to an user
	 *
	 * @param string $email
	 * @param string $register_status
	 */
	protected function getToken($email, $register_status)
	{
		if (in_array($register_status, array(self::GUEST_NOT_REGISTERED, self::GUEST_REGISTERED))) {
            $sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) as token
                    FROM `'._DB_PREFIX_.'emailsubscription`
                    WHERE `active` = 0
                    AND `email` = \''.pSQL($email).'\'';
        } elseif ($register_status == self::CUSTOMER_NOT_REGISTERED) {
            $sql = 'SELECT MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\' )) as token
                    FROM `'._DB_PREFIX_.'customer`
                    WHERE `newsletter` = 0
                    AND `email` = \''.pSQL($email).'\'';
        }

        return Db::getInstance()->getValue($sql);
	}

	/**
	 * Send a verification email
	 *
	 * @param string $email
	 * @param string $token
	 *
	 * @return bool
	 */
	protected function sendVerificationEmail($email, $token)
	{
		$verif_url = Context::getContext()->link->getModuleLink(
			'pk_popup', 'verification', array(
				'token' => $token,
			)
		);

		return Mail::Send($this->context->language->id, 'pk_popup_verif', Mail::l('Email verification', $this->context->language->id), array('{verif_url}' => $verif_url), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}

	/**
	 * Subscribe an email to the newsletter. It will create an entry in the newsletter table
	 * or update the customer table depending of the register status
	 *
	 * @param string $email
	 * @param int    $register_status
	 */
	protected function register($email, $register_status)
	{
		if ($register_status == self::GUEST_NOT_REGISTERED)
			return $this->registerGuest($email);

		if ($register_status == self::CUSTOMER_NOT_REGISTERED)
			return $this->registerUser($email);

		return false;
	}

	/**
	 * Subscribe a customer to the newsletter
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	protected function registerUser($email)
	{
		$sql = 'UPDATE '._DB_PREFIX_.'customer
                SET `newsletter` = 1, newsletter_date_add = NOW(), `ip_registration_newsletter` = \''.pSQL(Tools::getRemoteAddr()).'\'
                WHERE `email` = \''.pSQL($email).'\'
                AND id_shop = '.$this->context->shop->id;

        return Db::getInstance()->execute($sql);
	}

	/**
	 * Send an email containing a voucher code
	 *
	 * @param $email
	 * @param $code
	 *
	 * @return bool|int
	 */
	protected function sendVoucher($email, $code)
	{
		return Mail::Send($this->context->language->id, 'pk_popup_voucher', Mail::l('Newsletter voucher', $this->context->language->id), array('{discount}' => $code), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}

	/**
	 * Send a confirmation email
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	protected function sendConfirmationEmail($email)
	{
		return Mail::Send($this->context->language->id, 'pk_popup_conf', Mail::l('Newsletter confirmation', $this->context->language->id), array(), pSQL($email), null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}


	/**
	 * Register in block newsletter
	 */
	public function newsletterRegistration($email)
	{
		if (empty($email) || !Validate::isEmail($email)) {
            echo $this->error = $this->trans('Invalid email address.', array(), 'Shop.Notifications.Error');
            return;
        }

        $register_status = $this->isNewsletterRegistered($email);
    
        if ($register_status > 0) {
            echo $this->error = $this->trans('This email address is already registered.', array(), 'Modules.Popup.Shop');
            return;
        }

        $email = pSQL($email);
        if (!$this->isRegistered($register_status)) {
            if (Configuration::get('NW_VERIFICATION_EMAIL')) {
                // create an unactive entry in the newsletter database
                if ($register_status == self::GUEST_NOT_REGISTERED) {
                    $result = $this->registerGuest($email, false);
                }

                if (!$token = $this->getToken($email, $register_status)) {
                    echo $this->error = $this->trans('An error occurred during the subscription process.', array(), 'Modules.Popup.Shop');
                    return;
                }

                $this->sendVerificationEmail($email, $token);

                echo $this->valid = $this->trans('A verification email has been sent. Please check your inbox.', array(), 'Modules.Popup.Shop');
                return;

            } else {
                if ($this->register($email, $register_status)) {
                    echo $this->valid = $this->trans('You have successfully subscribed to this newsletter.', array(), 'Modules.Popup.Shop');
                } else {
                    echo $this->error = $this->trans('An error occurred during the subscription process.', array(), 'Modules.Popup.Shop');
                }

                if ($code = Configuration::get('NW_VOUCHER_CODE')) {
                    echo $this->sendVoucher($email, $code);
                }

                if (Configuration::get('NW_CONFIRMATION_EMAIL')) {
                    echo $this->sendConfirmationEmail($email);
                }
            }
        }

	}

}