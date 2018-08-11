<?php

class Pk_Twitter extends Module {

	private $_html = '';
	private $_postErrors = array();
	
    public function __construct() {

        $this->name = 'pk_twitter';
        $this->version = 1.4;
        $this->bootstrap = true;
        $this->author = 'promokit.eu';

        parent::__construct();

        /* The parent construct is required for translations */
		$this->page = basename(__FILE__, '.php');
        $this->displayName = 'Twitter Feed';
        $this->description = 'Display Your Twitter Feed';
		$this->full_url = _MODULE_DIR_.$this->name.'/';

		$this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
		
		$this->options = array(
			'pk_twitter_username' => 'PromokitTest',
			'pk_twitter_count' => '2',
			'pk_tw_consumer_key' => 'YibflmnRpCfauwvqUteWg',
			'pk_tw_consumer_secret' => 'liXY1XKnRn0XB6R8YKTc9skiNbmRdUjBq2KVPvCrjY',
			'pk_tw_at' => '2156330221-WGbrSeeO7MZ98rimGbRpB7mLfzyfN1xl7z8s6No',
			'pk_tw_at_secret' => 'NkZb58AXNuF5l1fHf0ykJBAuyL9o7jwq4EmYJgOc0TNqB',
		);

		$this->admin_text = array(
			'pk_twitter_username' => $this->trans('Twitter Username', array(), 'Modules.Twitter.Admin'),
			'pk_twitter_count' => $this->trans('Tweets Number', array(), 'Modules.Twitter.Admin'),
			'pk_tw_consumer_key' => $this->trans('Consumer Key', array(), 'Modules.Twitter.Admin'),
			'pk_tw_consumer_secret' => $this->trans('Consumer Secret', array(), 'Modules.Twitter.Admin'),
			'pk_tw_at' => $this->trans('Access Token', array(), 'Modules.Twitter.Admin'),
			'pk_tw_at_secret' => $this->trans('Access Token Secret', array(), 'Modules.Twitter.Admin'),
		);
	}

    public function install() {

    	foreach ($this->options as $key => $value) {
    		if (!Configuration::updateValue($key, $value))
    			return false;
    	}

        if (!parent::install() OR 
        	!$this->registerHook('displayFooter') OR 
        	!$this->registerHook('displayHeader'))
			return false;
		return true;

    }

    public function getKeys() {
    	
    	$keys = array();
    	foreach ($this->options as $key => $value) {
    		$keys[$key] = Configuration::get($key);
    	}

    	return $keys;

    }

	public function uninstall() {

		foreach ($this->options as $key => $value) {
    		if (!Configuration::deleteByName($key, $value))
    			return false;
    	}
		if (!parent::uninstall())
			return false;
		return true;
	}	

	public function hookDisplayHeader($params) {

		$this->context->controller->registerStylesheet($this->name, 'modules/'.$this->name.'/assets/css/styles.css', ['media' => 'all', 'priority' => 150]);
        $this->context->controller->registerJavascript($this->name, 'modules/'.$this->name.'/assets/js/scripts.js', ['position' => 'bottom', 'priority' => 150]);

	}

    public function hookDisplayFooter($params) {

		foreach ($this->options as $key => $value) {
			if ($key == 'pk_twitter_count' || $key == 'pk_twitter_username')
    			$smarty_opts[$key] = Configuration::get($key);
    	}
    	$smarty_opts['tw_path'] = $this->full_url."ajax.php";
    	$smarty_opts['json_opts'] = json_encode($smarty_opts, JSON_PRETTY_PRINT);
    	$this->smarty->assign($smarty_opts);		
		return $this->fetch($this->templateFile);

	}

	public function renderForm() {

		$fields = array();
		foreach ($this->options as $key => $value) {
    		$fields[$key] = array(
				'type' => 'text',
				'label' => $this->admin_text[$key],
				'name' => $key,
				'class' => 'fixed-width-xxxl'
			);
    	}

		$fields_form = array(
			'form' => array(
				'tinymce' => false,
				'legend' => array(
					'title' => $this->trans('Products type', array(), 'Modules.Products.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => $fields,
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
					'name' => 'pk_twitter_submit',
				)
			),
		);

		$info = '<div class="defaultForm form-horizontal pk_twitter"><div class="panel" id="fieldset_0"><div class="panel-heading"><i class="icon-info"></i>&nbsp;Getting API Keys</div><div class="form-wrapper"><div class="form-group"><label class="control-label col-lg-3">Look At this Video</label><div class="col-lg-9"><iframe width="560" height="315" src="https://www.youtube.com/embed/5PUC9yGS4RI" frameborder="0" allowfullscreen></iframe></div></div></div></div></div>';
		

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->allow_employee_form_lang = true;
		$helper->toolbar_scroll = true;
		$helper->toolbar_btn = $this->initToolbar();
		$helper->title = $this->displayName;
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
		);
		return $helper->generateForm(array($fields_form)).$info;
	}

	private function initToolbar() {

		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->trans('Save', array(), 'Admin.Actions')
		);

		return $this->toolbar_btn;
	}

	public function getConfigFieldsValues() {

		$values = array();
		foreach ($this->options as $key => $value) { // read options from DB
			$values[$key] = Tools::getValue($key, Configuration::get($key));
		}

		return $values;
	}

	public function getContent() {

		$info = "";
		if (Tools::isSubmit('pk_twitter_submit')) {

			$errors = array();

			foreach ($this->options as $key => $value) { // write changes to DB
				if ($key != 'pk_twitter_count') 
					Configuration::updateValue($key, Tools::getValue($key));	
			}

			$nbr = intval(Tools::getValue('pk_twitter_count'));
			if (!$nbr OR $nbr <= 0 OR !Validate::isInt($nbr)) {
				$errors[] = $this->trans('Invalid "Tweets Number"', array(), 'Admin.Notifications.Error');
			} else {
				Configuration::updateValue('pk_twitter_count', $nbr);				
			}

			if (isset($errors) AND sizeof($errors))
				$info .= $this->displayError($errors);
			else
				$info .= $this->displayConfirmation($this->trans('Settings updated', array(), 'Admin.Notifications.Success'));

		}
		return $info.$this->renderForm();
	}	
	
}

?>
