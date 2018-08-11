<?php

if (!defined('_PS_VERSION_'))
	exit;

class Pk_Manufacturers extends Module
{
    public function __construct()
    {
        $this->name = 'pk_manufacturers';
        $this->version = 1.2;
		$this->author = 'promokit.eu';
		$this->need_instance = 0;
		$this->bootstrap = true;
		$this->templateFile = 'module:'.$this->name.'/'.$this->name.'.tpl';
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        parent::__construct();

		$this->displayName = $this->trans('Manufacturers Carousel', array(), 'Modules.Manufacturers.Admin');
        $this->description = $this->trans('Displays a block of Manufacturers/Brands', array(), 'Modules.Manufacturers.Admin');

        $this->hooks = array(
			'content_top',
			'content_bottom',
			'displayHome'
		);

		$this->check_state = true;
        if (Module::isInstalled('pk_themesettings')) {
            require_once _PS_MODULE_DIR_.'pk_themesettings/inc/common.php';
            $this->check_state = new Pk_ThemeSettings_Common();
        }

    }

	public function install()
	{
		Configuration::updateValue('MCAROUSEL_DISPLAY_TITLE', 0);
		Configuration::updateValue('MCAROUSEL_DISPLAY_TEXT_NB', 8);
		Configuration::updateValue('MCAROUSEL_DISPLAY_TEXT_NB_VIS', 7);
        return 
        	parent::install() && 
        	$this->registerHook('content_top') &&
			$this->registerHook('content_bottom') &&
            $this->registerHook('displayHome') &&
        	$this->registerHook('displayHeader');
    }

	public function getContent() {

		$output = '';
		if (Tools::isSubmit('submitBlockManufacturers')) {

			$text_list = (int)(Tools::getValue('text_list'));
			$text_nb = (int)(Tools::getValue('text_nb'));
			$text_nb_vis = (int)(Tools::getValue('text_nb_vis'));
			
			if ($text_list && !Validate::isUnsignedInt($text_nb)) {

				$errors[] = $this->trans('Invalid number of elements', array(), 'Admin.Notifications.Error');			

			} else {
				Configuration::updateValue('MCAROUSEL_DISPLAY_TEXT_NB', $text_nb);
				Configuration::updateValue('MCAROUSEL_DISPLAY_TEXT_NB_VIS', $text_nb_vis);
				Configuration::updateValue('MCAROUSEL_DISPLAY_TITLE', $text_list);
			}

			if (isset($errors) && count($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->trans('Settings updated', array(), 'Admin.Notifications.Success'));

			parent::_clearCache($this->templateFile);
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" id="module_form" class="defaultForm form-horizontal">
			<div class="panel" id="fieldset_0">												
				<div class="panel-heading"><i class="icon-cogs"></i> '.$this->trans('Manufacturers Carousel Settings', array(), 'Modules.Manufacturers.Admin').'</div>
				<div class="form-wrapper">
					<div class="form-group">
						<label class="control-label col-lg-3">'.$this->trans('Display Titles', array(), 'Modules.Manufacturers.Admin').'</label>
						<div class="col-lg-6"><input type="checkbox" name="text_list" id="text_list" value="1" '.(Tools::getValue('text_list', Configuration::get('MCAROUSEL_DISPLAY_TITLE')) ? 'checked="checked" ' : '').' /></div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-3">'.$this->trans('Define the number of manufacturers', array(), 'Modules.Manufacturers.Admin').'</label>
						<div class="col-lg-1">
							<input type="text" size="5" name="text_nb" value="'.(int)(Tools::getValue('text_nb', Configuration::get('MCAROUSEL_DISPLAY_TEXT_NB'))).'" />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-3">'.$this->trans('Number of items to show in carousel', array(), 'Modules.Manufacturers.Admin').'</label>
						<div class="col-lg-1">
							<input type="text" size="5" name="text_nb_vis" value="'.(int)(Tools::getValue('text_nb_vis', Configuration::get('MCAROUSEL_DISPLAY_TEXT_NB_VIS'))).'" />
						</div>
					</div>
					<div class="panel-footer">
					<button type="submit" value="1" name="submitBlockManufacturers" class="btn btn-default pull-right"><i class="process-icon-save"></i> '.$this->trans('Save', array(), 'Admin.Actions').'</button>
					</div>
				</div>
			</div>
		</form>';
		return $output;
	}

	public function hookdisplayHeader($params) {

		$this->context->controller->addCSS($this->_path.'assets/css/styles.css', 'all');
		$this->context->controller->addJS($this->_path.'assets/js/scripts.js');

	}

	public function getData() {
		$this->smarty->assign(array(
			'manufacturers' => Manufacturer::getManufacturers(),
			'text_list_nb' => Configuration::get('MCAROUSEL_DISPLAY_TEXT_NB'),
			'text_list_nb_vis' => Configuration::get('MCAROUSEL_DISPLAY_TEXT_NB_VIS'),
			'show_title' => Configuration::get('MCAROUSEL_DISPLAY_TITLE'),
			'display_link_manufacturer' => Configuration::get('PS_DISPLAY_SUPPLIERS'),
		));
	}

	public function hookdisplayHome($params) {

        $params['hook'] = 'displayHome';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name,  'home' => true));
        if ($status == true) {
            if (!$this->isCached($this->templateFile, $this->getCacheId($this->name)))
	 			$this->getData();
	 		return $this->fetch($this->templateFile, $this->getCacheId($this->name));
        }

    }   

    public function hookcontent_top($params) {

        $params['hook'] = 'content_top';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name,  'home' => true));
        if ($status == true) {
            if (!$this->isCached($this->templateFile, $this->getCacheId($this->name)))
	 			$this->getData();
	 		return $this->fetch($this->templateFile, $this->getCacheId($this->name));
        }

    }

    public function hookcontent_bottom($params) {

        $params['hook'] = 'content_bottom';
        $status = $this->check_state->getModuleState(array('hook' => $params['hook'], 'name' => $this->name,  'home' => true));
        if ($status == true) {
            if (!$this->isCached($this->templateFile, $this->getCacheId($this->name)))
	 			$this->getData();
	 		return $this->fetch($this->templateFile, $this->getCacheId($this->name));
        }

    }

}
