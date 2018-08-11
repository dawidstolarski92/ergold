<?php

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class Pk_VideoBg extends Module
{
	public function __construct()
	{
		$this->name = 'pk_videobg';
		$this->author = 'promokit.eu';
		$this->version = '1.2';

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->trans('Video Background', array(), 'Modules.VideoBg.Admin');
		$this->description = $this->trans('Allows you to add advertising with video background', array(), 'Modules.VideoBg.Admin');
		
		$this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';
		$this->templateFileColumn = 'module:'.$this->name.'/'.$this->name.'-column.tpl';
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

		$this->options = array(
			'pk_videobg_link' => 'https://www.youtube.com/watch?v=YKtW5BiyRwM',
			'pk_videobg_title' => 'Lookbook',
			'pk_videobg_subtitle' => 'spring summer 2017',
			'pk_videobg_url' => 'https://www.youtube.com/watch?v=YKtW5BiyRwM',
			'pk_videobg_text' => 'Quisque volutpat blandit ipsum eget pulvinar. In viverra, mi ac convallis dictum enim. condimentum lorem'
		);

		$this->check_state = true;
        if (Module::isInstalled('pk_themesettings')) {
            require_once _PS_MODULE_DIR_.'pk_themesettings/inc/common.php';
            $this->check_state = new Pk_ThemeSettings_Common();
        }
	}
	
	public function install()
	{
		foreach ($this->options as $key => $value) {
			if (!Configuration::updateValue($key, $value))
				return false;
		}

		return parent::install()
			&& $this->registerHook('displayHeader')
			&& $this->registerHook('content_top')
			&& $this->registerHook('content_bottom')
			&& $this->registerHook('displayLeftColumn')
            && $this->registerHook('displayHome');
	}
	
	public function uninstall()
	{
		// Delete configuration
		foreach ($this->options as $key => $value) {
			if (!Configuration::deleteByName($key))
				return false;
		}

		return parent::uninstall();
	}
	
	public function getContent()
	{
		$html = '';
		// If we try to update the settings
		if (Tools::isSubmit('submitModule')) {	

			foreach ($this->options as $key => $value) {
				if (!Configuration::updateValue($key, Tools::getValue($key)))
					return false;
			}

			if (parent::_clearCache($this->templateFile)) {
			//	$html .= $this->displayConfirmation($this->trans('Cache has been cleaned', array(), 'Admin.Notifications.Success'));
			}

			$html .= $this->displayConfirmation($this->trans('Configuration updated', array(), 'Admin.Notifications.Success'));

		}

		$html .= $this->renderForm();

		return $html;
	}

	public function hookDisplayHeader($params) {

		$this->context->controller->registerStylesheet($this->name, 'modules/'.$this->name.'/assets/css/styles.css', ['media' => 'all', 'priority' => 150]);
        $this->context->controller->registerJavascript($this->name, 'modules/'.$this->name.'/assets/js/scripts.js', ['position' => 'bottom', 'priority' => 150]);

	}

	public function getData($params) {
		
		$smarty_opts = array();
		foreach ($this->options as $key => $value) {
			$smarty_opts['opts'][strtolower($key)] = Configuration::get($key);
		}

		$link = str_replace(array("http:"), "", Configuration::get('pk_videobg_link'));
		$local = false;
		$yt_code = '';

		if (strpos($link,'youtube') !== false)
			if (strpos($link,'watch?v=') !== false) {
				$yt_code = explode('watch?v=', $link);
				$link = str_replace("watch?v=", "embed/", $link);
				//$link = $link."?autoplay=1&amp;controls=0&amp;loop=1&amp;showinfo=0&amp;modestbranding=1&amp;disablekb=1&amp;enablejsapi=1";
			}

		if (strpos($link,'vimeo') !== false)
			if (strpos($link,'//vimeo') !== false)
				$link = str_replace("//vimeo.com", "//player.vimeo.com/video", $link);

		if ((strpos($link,'youtube') == false) && (strpos($link,'vimeo') == false))
			$local = true;

		$smarty_opts['opts']['pk_videobg_local'] = $local;
		$smarty_opts['opts']['pk_videobg_link'] = $link;
		$smarty_opts['opts']['pk_videobg_yt_code'] = (isset($yt_code[1]) ? $yt_code[1] : '');

		$tpl = $this->name;
		if (isset($params['pk_videobg_tpl']) && $params['pk_videobg_tpl'])
			$tpl = $params['pk_videobg_tpl'];

		if (!$this->isCached($this->templateFile, $this->getCacheId($this->name))) {
			$this->smarty->assign($smarty_opts);
		}

	}


	public function hookdisplayHome($params) {

        $params['hook'] = 'displayHome';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getData($params);
			return $this->fetch($this->templateFile, $this->getCacheId($this->name));
        }

    }   

    public function hookcontent_top($params) {

        $params['hook'] = 'content_top';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getData($params);
			return $this->fetch($this->templateFile, $this->getCacheId($this->name));
        }

    }

    public function hookcontent_bottom($params) {

        $params['hook'] = 'content_bottom';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name, 'home' => true));
        if ($status == true) {
            $this->getData($params);
			return $this->fetch($this->templateFile, $this->getCacheId($this->name));
        }

    }
	
	public function hookDisplayLeftColumn($params)
	{
		$this->getData($params);
		return $this->fetch($this->templateFileColumn);
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->trans('Settings', array(), 'Admin.Global'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->trans('Title', array(), 'Admin.Global'),
						'name' => 'pk_videobg_title',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Subtitle', array(), 'Modules.VideoBg.Admin'),
						'name' => 'pk_videobg_subtitle',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Text', array(), 'Admin.Global'),
						'name' => 'pk_videobg_text',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Button URL', array(), 'Modules.VideoBg.Admin'),
						'name' => 'pk_videobg_url',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Video Link', array(), 'Modules.VideoBg.Admin'),
						'name' => 'pk_videobg_link',
						'desc' => "You can use direct links to video from youtube or vimeo. You can also upload video to your server and put direct link here. For example: http://alysum.promokit.eu/upload/videofile.mp4",
					),
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
				)
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		$options = array();
		foreach ($this->options as $key => $value) {
			$options[$key] = Tools::getValue($key, Configuration::get($key));
		}
		return $options;
	}

}