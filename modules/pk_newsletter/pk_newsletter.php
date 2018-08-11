<?php

if (!defined('_PS_VERSION_'))
	exit;

class Pk_Newsletter extends Module
{
	const GUEST_NOT_REGISTERED = -1;
	const CUSTOMER_NOT_REGISTERED = 0;
	const GUEST_REGISTERED = 1;
	const CUSTOMER_REGISTERED = 2;

	public function __construct()
	{
		$this->name = 'pk_newsletter';
		$this->need_instance = 0;
		$this->bootstrap = true;
		$this->templateFile = 'module:'.$this->name.'/views/templates/hook/'.$this->name.'.tpl';
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

		parent::__construct();

		$this->confirmUninstall = $this->trans('Are you sure you want to delete all your contacts ?', array(), 'Modules.Manufacturers.Admin');
		$this->displayName = $this->trans('Extended Newsletter', array(), 'Modules.Manufacturers.Admin');
        $this->description = $this->trans('Adds a block for newsletter subscription', array(), 'Modules.Manufacturers.Admin');

		$this->version = '2.1';
		$this->author = 'promokit.eu';
		$this->error = false;
		$this->valid = false;
		$this->_files = array(
			'name' => array('newsletter_conf', 'newsletter_voucher'),
			'ext' => array(
				0 => 'html',
				1 => 'txt'
			)
		);

		$this->check_state = true;
        if (Module::isInstalled('pk_themesettings')) {
            require_once _PS_MODULE_DIR_.'pk_themesettings/inc/common.php';
            $this->check_state = new Pk_ThemeSettings_Common();
        }
		
	}

	public function install()
	{
		if (parent::install() == false || 
			!$this->registerHook('content_top') ||
            !$this->registerHook('content_bottom') ||
            !$this->registerHook('displayHome') ||
			!$this->registerHook('displayHeader')
		)
		return false;

		Configuration::updateValue('NW_SALT_EXT_EXT', Tools::passwdGen(16));
		Configuration::updateValue('NW_FB', 'promokit.eu');
		Configuration::updateValue('NW_TW', 'Mnishek');
		Configuration::updateValue('NW_YT', 'PromokitEu');
		Configuration::updateValue('NW_GP', '114216189760484036393');
		Configuration::updateValue('NW_ADV_LINK', '#');
		Configuration::updateValue('NW_ADV_IMG', 'promo.jpg');
		Configuration::updateValue('NW_ADV_IMG_NEWS', 'promo_news.jpg');
		Configuration::updateValue('NW_ADV_IMG_SOC', 'promo_soc.jpg');

		return Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter` (
			`id` int(6) NOT NULL AUTO_INCREMENT,
			`id_shop` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`id_shop_group` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`email` varchar(255) NOT NULL,
			`newsletter_date_add` DATETIME NULL,
			`ip_registration_newsletter` varchar(15) NOT NULL,
			`http_referer` VARCHAR(255) NULL,
			`active` TINYINT(1) NOT NULL DEFAULT \'0\',
			PRIMARY KEY(`id`)
		) ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8');
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'newsletter');
	}

	private function _addImg($image, $name, $var)
	{		
		$errors = "";
		if (!$err = ImageManager::validateUpload($image["name"], 4000000) ) {
			$errors = $err;
		} elseif ($dot_pos = strrpos($image['name'], '.')) {			
			$ext = substr($image['name'], $dot_pos + 1);
			$newname = $name.'-'.(int)$this->context->shop->id;

			if (!move_uploaded_file($image['tmp_name'], _PS_MODULE_DIR_.$this->name.'/images/'.$newname.'.'.$ext)) {
				$errors .= $this->l('Error move uploaded file');
			} else {
				Configuration::updateValue($var, $newname.'.'.$ext);
			}
		}				

		if ($errors) { 
			$errors = '<div class="conf error">'.$errors.'</div>'; 
		} else 
			$errors = "";			
		return $errors;
	}

	public function getContent()
	{
		//$this->postProcess();
		//$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_html = "";

		if (Tools::isSubmit('submitUpdate'))
		{
			if (isset($_POST['new_page']) && Validate::isBool((int)$_POST['new_page']))
				Configuration::updateValue('NW_CONFIRMATION_NEW_PAGE_EXT', $_POST['new_page']);

			if (isset($_POST['conf_email']) && Validate::isBool((int)$_POST['conf_email']))
				Configuration::updateValue('NW_CONFIRMATION_EMAIL_EXT', pSQL($_POST['conf_email']));

			if (isset($_POST['verif_email']) && Validate::isBool((int)$_POST['verif_email']))
				Configuration::updateValue('NW_VERIFICATION_EMAIL_EXT', (int)$_POST['verif_email']);

			if (!empty($_POST['voucher']) && !Validate::isDiscountName($_POST['voucher']))
				$this->_html .= '<div class="alert">'.$this->l('Voucher code is invalid').'</div>';
			else
			{
				Configuration::updateValue('NW_VOUCHER_CODE_EXT', pSQL($_POST['voucher']));
				$this->_html .= '<div class="conf ok">'.$this->l('Updated').'</div>';
			}
			Configuration::updateValue('NW_FB', (($_POST['facebook_url'] != '') ? $_POST['facebook_url']: ''));
			Configuration::updateValue('NW_TW', (($_POST['twitter_url'] != '') ? $_POST['twitter_url']: ''));
			Configuration::updateValue('NW_YT', (($_POST['youtube_url'] != '') ? $_POST['youtube_url']: ''));
			Configuration::updateValue('NW_GP', (($_POST['gplus_url'] != '') ? $_POST['gplus_url']: ''));	
			//print_r($_FILES);
			if (isset($_FILES['adv_img']) && isset($_FILES['adv_img']['tmp_name']) && !empty($_FILES['adv_img']['tmp_name'])) {
				$this->_addImg($_FILES['adv_img'], "promo", "NW_ADV_IMG");
			}

			if (isset($_FILES['adv_img_newsletter']) && isset($_FILES['adv_img_newsletter']['tmp_name']) && !empty($_FILES['adv_img_newsletter']['tmp_name'])) {
				$this->_addImg($_FILES['adv_img_newsletter'], "promo_news", "NW_ADV_IMG_NEWS");
			}

			if (isset($_FILES['adv_img_socialize']) && isset($_FILES['adv_img_socialize']['tmp_name']) && !empty($_FILES['adv_img_socialize']['tmp_name'])) {
				$this->_addImg($_FILES['adv_img_socialize'], "promo_soc", "NW_ADV_IMG_SOC");
			}

			if ($link = Tools::getValue('adv_link')) {
				Configuration::updateValue('NW_ADV_LINK', $link);
				$this->adv_link = htmlentities($link, ENT_QUOTES, 'UTF-8');
			}
			if ($title = Tools::getValue('adv_title')) {
				Configuration::updateValue('NW_ADV_TITLE', $title);
				$this->adv_title = htmlentities($title, ENT_QUOTES, 'UTF-8');
			}			
		}
		return $this->_displayForm();
	}

	public function _displayForm()
	{
		$rev = "?".date("H").date("i").date("s");
		$this->adv_link = $this->adv_img = $this->adv_title = $this->adv_img_newsletter = $this->adv_img_socialize = "";
		$this->output = '<label for="adv_img">'.$this->l('Change image').'&nbsp;&nbsp;</label>
					<input id="adv_img" type="file" name="adv_img" />
					<strong>Recommended Image dimensions is 406X406px</strong>
				<br/>
				<a href="'.$this->adv_link.'" target="_blank" title="'.$this->adv_title.'">';			
				$this->output .= '<img src="'.__PS_BASE_URI__.'modules/'.$this->name.'/images/'.Configuration::get('NW_ADV_IMG').$rev.'" alt="'.$this->adv_title.'" title="'.$this->adv_title.'" style="height:163px;margin:10px 0 0 260px;width:auto"/>';
				$this->output .= '
				</a>
				<br/>
				<br/>				
				<br class="clear"/>
				<label for="adv_link">'.$this->l('Image link').'&nbsp;&nbsp;</label>
					<input id="adv_link" type="text" name="adv_link" value="'.$this->adv_link.'" />
					<br class="clear"/>
				<br/>				
					<br class="clear"/>
					<label for="adv_title">'.$this->l('Title').'&nbsp;&nbsp;</label>
					<input id="adv_title" type="text" name="adv_title" value="'.$this->adv_title.'" />				
				<br class="clear"/>';
				
		$this->_html .= '
		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" style="position:relative">
			<fieldset>
				<h4>'.$this->l('Social networks accounts:').'</h4>
				<label for="facebook_url">'.$this->l('Facebook ID : ').'</label>
				<input type="text" id="facebook_url" name="facebook_url" value="'.((Configuration::get('NW_FB') != "") ? Configuration::get('NW_FB') : "").'" />
				<div class="clear">&nbsp;</div>		
				<label for="twitter_url">'.$this->l('Twitter Usermane : ').'</label>
				<input type="text" id="twitter_url" name="twitter_url" value="'.((Configuration::get('NW_TW') != "") ? Configuration::get('NW_TW') : "").'" />
				<div class="clear">&nbsp;</div>

				<label for="youtube_url">'.$this->l('Youtube Usermane : ').'</label>
				<input type="text" id="youtube_url" name="youtube_url" value="'.((Configuration::get('NW_YT') != "") ? Configuration::get('NW_YT') : "").'" />
				<div class="clear">&nbsp;</div>

				<label for="gplus_url">'.$this->l('Google+ ID : ').'</label>
				<input type="text" id="gplus_url" name="gplus_url" value="'.((Configuration::get('NW_GP') != "") ? Configuration::get('NW_GP') : "").'" /><br/><br/>
				<label for="adv_img_socialize">'.$this->l('Change image').'&nbsp;&nbsp;</label>
					<input id="adv_img_socialize" type="file" name="adv_img_socialize" /> 
					<br/> <strong style="margin: 10px 0 0 227px;font-weight: normal;">Recommended Image dimensions is 406X406px</strong>
					<br/>';		
				$this->_html .= '<img src="'.__PS_BASE_URI__.'modules/'.$this->name.'/images/'.Configuration::get('NW_ADV_IMG_SOC').$rev.'" alt="'.$this->adv_title.'" title="'.$this->adv_title.'" style="height:163px;margin:10px 0 0 260px;width:auto"/>';
				$this->_html .= '<hr style="border-bottom:2px solid #ccc" />
				

				<h4>'.$this->l('Newsletter settings:').'</h4>
				<label>'.$this->l('Display configuration in a new page?').'</label>
				<div class="margin-form">
					<input type="radio" name="new_page" value="1" '.(Configuration::get('NW_CONFIRMATION_NEW_PAGE_EXT') ? 'checked="checked" ' : '').'/> '.$this->l('yes').' 
					<input type="radio" name="new_page" value="0" '.(!Configuration::get('NW_CONFIRMATION_NEW_PAGE_EXT') ? 'checked="checked" ' : '').'/> '.$this->l('no').'
				</div>
				<div class="clear"></div>				
				<label>'.$this->l('Send verfication e-mail after subscription?').'</label>
				<div class="margin-form">
					<input type="radio" name="verif_email" value="1" '.(Configuration::get('NW_VERIFICATION_EMAIL_EXT') ? 'checked="checked" ' : '').'/> '.$this->l('yes').' 
					<input type="radio" name="verif_email" value="0" '.(!Configuration::get('NW_VERIFICATION_EMAIL_EXT') ? 'checked="checked" ' : '').'/>'.$this->l('no').'
				</div>
				<div class="clear"></div>
				<label>'.$this->l('Send confirmation e-mail after subscription?').'</label>
				<div class="margin-form">
					<input type="radio" name="conf_email" value="1" '.(Configuration::get('NW_CONFIRMATION_EMAIL_EXT') ? 'checked="checked" ' : '').'/> '.$this->l('yes').' 
					<input type="radio" name="conf_email" value="0" '.(!Configuration::get('NW_CONFIRMATION_EMAIL_EXT') ? 'checked="checked" ' : '').'/> '.$this->l('no').'
				</div>
				<div class="clear"></div>
				<label>'.$this->l('Welcome voucher code').'</label>
				<div class="margin-form">
					<input type="text" name="voucher" value="'.Configuration::get('NW_VOUCHER_CODE_EXT').'" />
					<p>'.$this->l('Leave blank for disabling').'</p>
				</div><label for="adv_img_newsletter">'.$this->l('Change image').'&nbsp;&nbsp;</label>
					<input id="adv_img_newsletter" type="file" name="adv_img_newsletter" /> <strong>Recommended Image dimensions is 406X406px</strong>
				<br/>';
			
					$this->_html .= '<img src="'.__PS_BASE_URI__.'modules/'.$this->name.'/images/'.Configuration::get('NW_ADV_IMG_NEWS').$rev.'" 
					alt="'.$this->adv_title.'" title="'.$this->adv_title.'" style="height:163px; margin:10px 0 0 260px;width:auto"/>';
				$this->_html .= '<hr style="border-bottom:2px solid #ccc" />
				

				<h4>Advertising</h4>
				'.$this->output.'
				<input type="submit" name="submitUpdate" value="'.$this->l('Update').'" class="button" />
			</fieldset>
			
		</form>';

		return $this->_html;
	}

	/**
	 * Check if this mail is registered for newsletters
	 *
	 * @param unknown_type $customerEmail
	 * @return int -1 = not a customer and not registered
	 * 				0 = customer not registered
	 * 				1 = registered in block
	 * 				2 = registered in customer
	 */
	private function isNewsletterRegistered($customerEmail)
	{
		$sql = 'SELECT `email`
				FROM '._DB_PREFIX_.'newsletter
				WHERE `email` = \''.pSQL($customerEmail).'\'
				AND id_shop = '.$this->context->shop->id;

		if (Db::getInstance()->getRow($sql))
			return self::GUEST_REGISTERED;

		$sql = 'SELECT `newsletter`
				FROM '._DB_PREFIX_.'customer
				WHERE `email` = \''.pSQL($customerEmail).'\'
				AND id_shop = '.$this->context->shop->id;

		if (!$registered = Db::getInstance()->getRow($sql))
			return self::GUEST_NOT_REGISTERED;

		if ($registered['newsletter'] == '1')
			return self::CUSTOMER_REGISTERED;

		return self::CUSTOMER_NOT_REGISTERED;
	}

	/**
	 * Register in block newsletter
	 */
	public function newsletterAjaxRegistration($email = '')
	{

		//echo $email.$action;
		$register_status = $this->isNewsletterRegistered($email);

		$email = pSQL($email);
		if (!$this->isRegistered($register_status))
		{
			if (Configuration::get('NW_VERIFICATION_EMAIL_EXT'))
			{
				// create an unactive entry in the newsletter database
				if ($register_status == self::GUEST_NOT_REGISTERED)
					$this->registerGuest($email, false);

				if (!$token = $this->getToken($email, $register_status))
					echo'{"tp":"err", "msg": "Error during subscription"}';

				$this->sendVerificationEmail($email, $token);

				//echo'A verification email has been sent. Please check your email.';
			}
			else
			{
				if ($this->register($email, $register_status))
					echo '{"tp":"success", "msg": "Subscription successful"}';
				else
					echo '{"tp":"err", "msg": "Error during subscription"}';

				if ($code = Configuration::get('NW_VOUCHER_CODE_EXT'))
					$this->sendVoucher($email, $code);

				if (Configuration::get('NW_CONFIRMATION_EMAIL_EXT'))
					$this->sendConfirmationEmail($email);
			}
		} else {
			echo '{"tp":"err", "msg": "Email already registered"}';
		}

	}
	private function newsletterRegistration($email = '', $action = '')
	{

		if (!empty($email) && Validate::isEmail($email)) {
			$_POST['email'] = $email;
			$_POST['action'] = $action;
		}

		if (empty($_POST['email']) || !Validate::isEmail($_POST['email']))
			return $this->error = $this->l('Invalid e-mail address');

		/* Unsubscription */
		else if ($_POST['action'] == '1')
		{
			$register_status = $this->isNewsletterRegistered($_POST['email']);
			if ($register_status < 1)
				return $this->error = $this->l('E-mail address not registered');
			else if ($register_status == self::GUEST_REGISTERED)
			{
				if (!Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'newsletter WHERE `email` = \''.pSQL($_POST['email']).'\' AND id_shop = '.$this->context->shop->id))
					return $this->error = $this->l('Error during unsubscription');
				return $this->valid = $this->l('Unsubscription successful');
			}
			else if ($register_status == self::CUSTOMER_REGISTERED)
			{
				if (!Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer SET `newsletter` = 0 WHERE `email` = \''.pSQL($_POST['email']).'\' AND id_shop = '.$this->context->shop->id))
					return $this->error = $this->l('Error during unsubscription');
				return $this->valid = $this->l('Unsubscription successful');
			}
		}
		/* Subscription */
		else if ($_POST['action'] == '0')
		{
			$register_status = $this->isNewsletterRegistered($_POST['email']);
			//if ($register_status > 0)
			//	return $this->error = $this->l('E-mail address already registered');

			$email = pSQL($_POST['email']);
			if (!$this->isRegistered($register_status))
			{
				if (Configuration::get('NW_VERIFICATION_EMAIL_EXT'))
				{
					// create an unactive entry in the newsletter database
					if ($register_status == self::GUEST_NOT_REGISTERED)
						$this->registerGuest($email, false);

					if (!$token = $this->getToken($email, $register_status))
						return $this->error = $this->l('Error during subscription');

					$this->sendVerificationEmail($email, $token);

					return $this->valid = $this->l('A verification email has been sent. Please check your email.');
				}
				else
				{
					if ($this->register($email, $register_status))
						$this->valid = $this->l('Subscription successful');
					else
						return $this->error = $this->l('Error during subscription');

					if ($code = Configuration::get('NW_VOUCHER_CODE_EXT'))
						$this->sendVoucher($email, $code);

					if (Configuration::get('NW_CONFIRMATION_EMAIL_EXT'))
						$this->sendConfirmationEmail($email);
				}
			}
		}
	}

	/**
	 * Return true if the registered status correspond to a registered user
	 * @param int $register_status
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
	 * Subscribe an email to the newsletter. It will create an entry in the newsletter table
	 * or update the customer table depending of the register status
	 *
	 * @param unknown_type $email
	 * @param unknown_type $register_status
	 */
	protected function register($email, $register_status)
	{
		if ($register_status == self::GUEST_NOT_REGISTERED)
		{
			if (!$this->registerGuest(Tools::getValue('email')))
				return false;
		}
		else if ($register_status == self::CUSTOMER_NOT_REGISTERED)
		{
		 	if (!$this->registerUser(Tools::getValue('email')))
	 			return false;
		}

		return true;
	}

	/**
	 * Subscribe a customer to the newsletter
	 *
	 * @param string $email
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
	 * Subscribe a guest to the newsletter
	 *
	 * @param string $email
	 * @param bool $active
	 * @return bool
	 */
	protected function registerGuest($email, $active = true)
	{
		$sql = 'INSERT INTO '._DB_PREFIX_.'newsletter (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
				VALUES
				('.$this->context->shop->id.',
				'.$this->context->shop->id_shop_group.',
				\''.pSQL($email).'\',
				NOW(),
				\''.pSQL(Tools::getRemoteAddr()).'\',
				(
					SELECT c.http_referer
					FROM '._DB_PREFIX_.'connections c
					WHERE c.id_guest = '.(int)$this->context->customer->id.'
					ORDER BY c.date_add DESC LIMIT 1
				),
				'.(int)$active.'
				)';

		return Db::getInstance()->execute($sql);
	}


	public function activateGuest($email)
	{
		return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'newsletter`
						SET `active` = 1
						WHERE `email` = \''.pSQL($email).'\''
				);
	}

	/**
	 * Returns a guest email by token
	 * @param string $token
	 * @return string email
	 */
	protected function getGuestEmailByToken($token)
	{
		$sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'newsletter`
				WHERE MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT_EXT')).'\')) = \''.pSQL($token).'\'
				AND `active` = 0';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Returns a customer email by token
	 * @param string $token
	 * @return string email
	 */
	protected function getUserEmailByToken($token)
	{
		$sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'customer`
				WHERE MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT_EXT')).'\')) = \''.pSQL($token).'\'
				AND `newsletter` = 0';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Return a token associated to an user
	 *
	 * @param string $email
	 * @param string $register_status
	 */
	protected function getToken($email, $register_status)
	{
		if (in_array($register_status, array(self::GUEST_NOT_REGISTERED, self::GUEST_REGISTERED)))
		{
			$sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT_EXT')).'\')) as token
					FROM `'._DB_PREFIX_.'newsletter`
					WHERE `active` = 0
					AND `email` = \''.pSQL($email).'\'';
		}
		else if ($register_status == self::CUSTOMER_NOT_REGISTERED)
		{
			$sql = 'SELECT MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT_EXT')).'\' )) as token
					FROM `'._DB_PREFIX_.'customer`
					WHERE `newsletter` = 0
					AND `email` = \''.pSQL($email).'\'';
		}

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Ends the registration process to the newsletter
	 *
	 * @param string $token
	 */
	public function confirmEmail($token)
	{
		$activated = false;

		if ($email = $this->getGuestEmailByToken($token))
			$activated = $this->activateGuest($email);
		else if ($email = $this->getUserEmailByToken($token))
			$activated = $this->registerUser($email);

		if (!$activated)
			return $this->l('Email already registered or invalid');

		if ($discount = Configuration::get('NW_VOUCHER_CODE_EXT'))
			$this->sendVoucher($email, $discount);

		if (Configuration::get('NW_CONFIRMATION_EMAIL_EXT'))
			$this->sendConfirmationEmail($email);

		return $this->l('Thank you for subscribing to our newsletter.');
	}

	/**
	 * Send an email containing a voucher code
	 * @param string $email
	 * @param string $discount
	 * @return bool
	 */
	protected function sendVoucher($email, $code)
	{
		return Mail::Send($this->context->language->id, 'newsletter_voucher', Mail::l('Newsletter voucher', $this->context->language->id), array('{discount}' => $code), $email, null, null, null, null, null, dirname(__FILE__).'/mails/');
	}

	/**
	 * Send a confirmation email
	 * @param string $email
	 * @return bool
	 */
	protected function sendConfirmationEmail($email)
	{
		return	Mail::Send($this->context->language->id, 'newsletter_conf', Mail::l('Newsletter confirmation', $this->context->language->id), array(), pSQL($email), null, null, null, null, null, dirname(__FILE__).'/mails/');
	}

	/**
	 * Send a verification email
	 * @param string $email
	 * @param string $token
	 * @return bool
	 */
	protected function sendVerificationEmail($email, $token)
	{
		$verif_url = Context::getContext()->link->getModuleLink($this->name, 'verification', array(
			'token' => $token,
		));
		return Mail::Send($this->context->language->id, 'newsletter_verif', Mail::l('Email verification', $this->context->language->id), array('{verif_url}' => $verif_url), $email, null, null, null, null, null, dirname(__FILE__).'/mails/');
	}

	public function hookDisplayRightColumn($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}

	private function _prepareHook($params)
	{
		if (Tools::isSubmit('submitNewsletter'))
		{
			$this->newsletterRegistration();
			if ($this->error)
			{
				$this->smarty->assign(array('color' => 'red',
						'msg' => $this->error,
						'nw_value' => isset($_POST['email']) ? pSQL($_POST['email']) : false,
						'nw_error' => true,
						'action' => $_POST['action'])
				);
			}
			else if ($this->valid)
			{
				$this->smarty->assign(array('color' => 'green',
						'msg' => $this->valid,
						'nw_error' => false)
				);
			}
		}
		$this->smarty->assign('this_path', $this->_path);
		$this->smarty->assign(array(
			'facebook_url' => Configuration::get('NW_FB'),
			'twitter_url' => Configuration::get('NW_TW'),
			'youtube_url' => Configuration::get('NW_YT'),
			'gplus_url' => Configuration::get('NW_GP'),
			'image' =>		"//".Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/images/'.Configuration::get('NW_ADV_IMG'),
			'image_soc' =>		"//".Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/images/'.Configuration::get('NW_ADV_IMG_SOC'),
			'image_news' =>		"//".Tools::getMediaServer($this->name)._MODULE_DIR_.$this->name.'/images/'.Configuration::get('NW_ADV_IMG_NEWS'),
			'adv_link' =>	htmlentities(Configuration::getGlobalValue('NW_ADV_LINK'), ENT_QUOTES, 'UTF-8'),
			'adv_title' =>	htmlentities(Configuration::getGlobalValue('NW_ADV_TITLE'), ENT_QUOTES, 'UTF-8')
		));
	}

	public function hookcontent_top($params) {

		$params['hook'] = 'content_top';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
		if ($status == 1) {
			$this->_prepareHook($params);
			return $this->fetch($this->templateFile);
		}

	}

	public function hookcontent_bottom($params) {

		$params['hook'] = 'content_bottom';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
		if ($status == 1) {
			$this->_prepareHook($params);
			return $this->fetch($this->templateFile);
		}

	}

	public function hookDisplayHome($params) {

		$params['hook'] = 'displayHome';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
		if ($status == 1) {
			$this->_prepareHook($params);
			return $this->fetch($this->templateFile);
		}

	}

	public function hookDisplayHeader($params) {

		if ($this->context->controller->php_self == "index") {
			$this->context->controller->registerStylesheet($this->name, 'modules/'.$this->name.'/assets/css/styles.css', ['media' => 'all', 'priority' => 150]);
	        $this->context->controller->registerJavascript($this->name, 'modules/'.$this->name.'/assets/js/scripts.js', ['position' => 'bottom', 'priority' => 150]);
	    }

	}


}
