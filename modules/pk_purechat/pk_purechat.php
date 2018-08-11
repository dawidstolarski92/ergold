<?php

class pk_purechat extends Module {

	public function __construct() {
		$this->name = 'pk_purechat';
		$this->version = '1.1';
		$this->need_instance = 0;
		$this->author = 'promokit.eu';
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = 'PureChat';
		$this->description = 'Integrate Purechat on your shop';
		$this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';

		$this->affiliateurl = 'http://www.purechat.com';
	}

	function install()
	{
		if (!parent::install()
			|| !$this->registerHook('footer')
			|| !$this->registerHook('displayHeader')
			|| !Configuration::updateValue('PK_PURECHAT', ''))
			return false;
		return true;
	}

	function uninstall()
	{
		if (!Configuration::deleteByName('PK_PURECHAT') OR !parent::uninstall())
			return false;
		return true;
	}

	public function getContent($tab = 'AdminModules')
	{

        $cookie = Context::getContext()->cookie;
        $currentIndex = AdminController::$currentIndex;

		$token = Tools::getAdminToken($tab.(int)Tab::getIdFromClassName($tab).(int)$cookie->id_employee);
		if (Tools::isSubmit('submitConf')) {
			$purechatscript = trim(Tools::getValue('purechatscript'));

			if(Configuration::updateValue('PK_PURECHAT', $purechatscript))
				Tools::redirectAdmin($currentIndex.'&modulename='.$this->name.'&configure='.$this->name.'&conf=6&token='.$token);
		}
		return $this->displayForm();
	}

	public function displayForm()	{

		$iso = Language::getIsoById($this->context->language->id);
		return '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" id="module_form" class="defaultForm form-horizontal">
		<div class="panel" id="fieldset_0">		
			<div class="panel-heading"><i class="icon-cogs"></i> '.$this->trans('PureChat Settings', array(), 'Modules.isotope.Admin').'</div>								
			<div class="form-wrapper">
				<div class="form-group">
					<label class="control-label col-lg-3">PureChat ID:</label>
					<div class="col-lg-6"><input type="text" name="purechatscript" id="purechatscript" value="'.Configuration::get('PK_PURECHAT').'"/></div>
					<label class="control-label col-lg-3" style="clear:left"></label>
					<div class="col-lg-8"><p class="help-block">Registering on <a href="'.$this->affiliateurl.'" target="_blank" style="color:orange"><b>PureChat</b></a> to get the ID.</p></div>
				</div>
				<div class="panel-footer">
				<button type="submit" value="1" id="module_form_submit_btn" name="submitConf" class="btn btn-default pull-right"><i class="process-icon-save"></i> '.$this->trans('Save', array(), 'Admin.Actions').'</button>
				</div>
			</div>
		</div>
		</form>';
	}

	function hookFooter($params) {
		$pure = Configuration::get('PK_PURECHAT');
		if ($pure) {			
			$this->smarty->assign(array('purechat' => $pure));
			return $this->fetch($this->templateFile);
		}
	}

	public function hookDisplayHeader() {
		$this->context->controller->addJS($this->_path.'assets/js/scripts.js');
	}
}

?>
