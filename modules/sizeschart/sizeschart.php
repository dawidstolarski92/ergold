<?php

class SizesChart extends Module
{

	private $_html = '';

	private $_postErrors = array();
	
	public function __construct()
	{
		$this->name = 'sizeschart';
	if(_PS_VERSION_ > "1.4.0.0"){
		$this->tab = 'administration';
		$this->author = 'RSI';
		$this->need_instance = 0;
		}else
		{
		$this->tab = 'Tools';
		}
		$this->version = '2.0.1';

		parent::__construct();
        if (_PS_VERSION_ > '1.6.0.0') {
            $this->bootstrap = true;
        }		
		$this->displayName = $this->l('Sizes Chart');
		$this->description = $this->l('Display a table with sizes chart - www.catalogo-onlinersi.net');
	

		
	}

	public function install()
	{
		if (!Configuration::updateValue('SIZESCHART_NBR', 1) || !parent::install() || !$this->registerHook('extraright') || !$this->registerHook('header'))
			return false;
        if (_PS_VERSION_ > '1.7.0.0') {
            if (!$this->registerHook('displayProductAdditionalInfo') || !$this->registerHook(
                'displayBeforeBodyClosingTag'
            )
            ) {
                return false;
            }
        }
         if (!Configuration::updateValue(
            'SIZESCHART_SKIP_CAT',
            1
        )
        ) {
            return false;
        }   
            $languages = Language::getLanguages(false);
            $default_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
            $result = array();
            $result2 = array();
            foreach ($languages as $language) {
                $result2[$language['id_lang']] = '<table width="534" border="1" cellspacing="0" id="sizes" >
  <tr>
    <td colspan="6" align="center" bgcolor="#eeeeee"><strong>Sizing chart</strong></td>
  </tr>
  <tr>
    <td width="135" bgcolor="#CCCCCC">&nbsp;</td>
    <td width="71" align="center"><strong>XS</strong></td>
    <td width="71" align="center"><strong>S</strong></td>
    <td width="71" align="center"><strong>M</strong></td>
    <td width="71" align="center"><strong>L</strong></td>
    <td width="71" align="center"><strong>XL</strong></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><strong>US &amp; can</strong></td>
    <td>2</td>
    <td>4</td>
    <td>6</td>
    <td>8</td>
    <td>10</td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><strong>UK &amp; AUS</strong></td>
    <td>6</td>
    <td>8</td>
    <td>10</td>
    <td>12</td>
    <td>14</td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><strong>EUROPE</strong></td>
    <td>34</td>
    <td>36</td>
    <td>38</td>
    <td>40</td>
    <td>42</td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><strong>Bust (cm)</strong></td>
    <td>81</td>
    <td>86</td>
    <td>91</td>
    <td>96</td>
    <td>101</td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><strong>Bust (inch)</strong></td>
    <td>31.9</td>
    <td>33.9</td>
    <td>35.8</td>
    <td>37.8</td>
    <td>39.8</td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><strong>Waist (cm)</strong></td>
    <td>61</td>
    <td>66</td>
    <td>71</td>
    <td>76</td>
    <td>81</td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><strong>Waist (inch)</strong></td>
    <td>24</td>
    <td>26</td>
    <td>28</td>
    <td>29.9</td>
    <td>31.9</td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><strong>Hips (cm)</strong></td>
    <td>896</td>
    <td>91</td>
    <td>96</td>
    <td>101</td>
    <td>106</td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><strong>Hips (in)</strong></td>
    <td>33.9</td>
    <td>35.8</td>
    <td>37.8</td>
    <td>39.8</td>
    <td>41.7</td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#eeeeee"><strong>How to measure yourself</strong></td>
    <td colspan="4" rowspan="4" align="center" valign="middle" ><img src="../sizeschart/model.jpg"/></td>
  </tr>
  <tr>
    <td valign="middle"><span style="font-size: 12px;
height: 165px;
position: relative;
float: left;">With your top and bra off, measure around the fullest part of the bust and under the arms. Keep the tape stright across your back</span></td>
    <td align="center"><strong>Bust</strong></td>
  </tr>
  <tr>
    <td><span style="font-size:12px">While in a relaxed position, measure around the narrowest part of your natural waist. Keep the tape somewaht snug, but still confortably loose</span></td>
    <td align="center"><strong>Waist</strong></td>
  </tr>
  <tr>
    <td><span style="font-size:12px">Stand with your legs and feet together and measure around the fullest part of the hips</span></td>
    <td align="center"><strong>Hips</strong></td>
  </tr>
</table>';
            }
            Configuration::updateValue('SIZESCHART_DESC', $result2, true);		
		return true;
	}
	
   public function postProcess()
    {
        $errors = '';
        $output = '';
        if (Tools::isSubmit('submitUpdate')) {
            $skipcat = Tools::getValue('skipcat');
            $languages = Language::getLanguages(false);
            $default_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
            $result = array();
            $result2 = array();
            foreach ($languages as $language) {
                $result2[$language['id_lang']] = Tools::getValue('desc_'.$language['id_lang']);
            }
            Configuration::updateValue('SIZESCHART_DESC', $result2, true);
            //$this->Writecss($textsize2, $shadow, $color3, $color1);
                if (!empty($skipcat)) {
                    Configuration::updateValue(
                        'SIZESCHART_SKIP_CAT',
                        implode(
                            ',',
                            $skipcat
                        )
                    );

                } elseif (Shop::getContext() == Shop::CONTEXT_SHOP || Shop::getContext() == Shop::CONTEXT_GROUP) {
                    Configuration::deleteFromContext('SIZESCHART_SKIP_CAT');
                }
            $output .= $this->displayConfirmation($this->l('Settings updated').'<br/>');

            if (!$errors) {
                return $output;
            }
        }
    }
	
  
  
 

    public function getContent()
    {
        $errors = '';
        if (_PS_VERSION_ < '1.5.0.0') {
            $this->_html = '

		';
            /* display the module name */
            $this->_html .= '<h2>'.$this->displayName.'</h2>';
            $errors = '';
            /* update the legalwarning xml */
            if (Tools::isSubmit('submitUpdate')) {
                // Forbidden key
                //	$forbidden = array('submitUpdate');

                $llang = Tools::getValue('llang');

                $languages = Language::getLanguages(false);
                $default_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
                $result = array();
                $result2 = array();
                foreach ($languages as $language) {
                    $result2[$language['id_lang']] = Tools::getValue('desc_'.$language['id_lang']);
                }
                Configuration::updateValue('SIZESCHART_DESC', $result2, true);
                $this->_html .= $errors == '' ? $this->displayConfirmation(
                    $this->l('Settings updated successfully')
                ) : $errors;
            }
            /* display the legalwarning's form */
            $this->_displayForm();

            return $this->_html;
        } else {
            return $this->_displayInfo().$this->renderForm().$this->_displayAdds();
        }
    }

    private function _displayInfo()
    {
        return $this->display(
            __FILE__,
            'views/templates/hook/infos.tpl'
        );
    }

    private function _displayAdds()
    {
        $this->context->smarty->assign(
            array(
                'psversion' => _PS_VERSION_
            )
        );
        return $this->display(
            __FILE__,
            'views/templates/hook/adds.tpl'
        );
    }




public function hookHeader($params)
	{
	
		if(_PS_VERSION_ < "1.4.0.0"){
		return $this->display(__FILE__, 'sizeschart-header.tpl');
		}
		if(_PS_VERSION_ > "1.4.0.0" && _PS_VERSION_ < "1.5.0.0")
		{
				Tools::addCSS(__PS_BASE_URI__.'modules/sizeschart/style.css', 'all');	
		}
			if(_PS_VERSION_ > "1.5.0.0" )
		{
		$this->context->controller->addCSS(($this->_path).'style.css', 'all');	
			
		}
		if(_PS_VERSION_ > "1.7.0.0")
		{
		$this->context->controller->addCSS(($this->_path).'js/jquery.fancybox.css', 'all');
		$this->context->controller->addJS(($this->_path).'js/jquery.fancybox.js');	
		}
		
	}
    public function hookdisplayBeforeBodyClosingTag($params)
    {
	 return $this->display(__FILE__, 'sizeschart7.tpl');

	}
   public function hookdisplayProductAdditionalInfo($params)
    {
		return $this->hookExtraRight($params);
	}
	
	public function hookExtraRight($params)
	{
		global $smarty,$cookie;
        $skcat = Configuration::get('SIZESCHART_SKIP_CAT');
        $skcat = explode(
            ',',
            $skcat
        );
        $skcat = array_map(
            'intval',
            $skcat
        );
        $id_product = (int)(Tools::getValue('id_product'));
        @$product = $params['product'];

        $id_category_default = Product::getProductCategories($id_product);
            $bFound = (count(
                array_intersect(
                    $skcat,
                    $id_category_default
                )
            )) ? 'true' : 'false';

		
		$smarty->assign(array(
				'psversion' =>_PS_VERSION_,
		));
if( $bFound == 'true')
			return $this->display(__FILE__, 'sizeschart.tpl');
	}

    public function getConfigFieldsValues()
    {

        $languages = Language::getLanguages(false);
        $fields_values = array(
            'skipcat' => Tools::getValue(
                'skipcat',
                explode(
                    ',',
                    Configuration::get('SIZESCHART_SKIP_CAT')
                )
            ),
        );
        foreach ($languages as $lang) {

            $fields_values['desc'][$lang['id_lang']] = Tools::getValue(
                'desc_',
                Configuration::get(
                    'SIZESCHART_DESC',
                    $lang['id_lang']
                )
            );
        }
		
        return $fields_values;
    }
    public function renderForm()
    {
        $this->postProcess();
        $root = Category::getRootCategory();
        $selected_cat = explode(
            ',',
            Configuration::get('SIZESCHART_SKIP_CAT')
        );
        $tree = new HelperTreeCategories('categories-treeview');
        $tree->setUseCheckBox(true)
             ->setAttribute(
                 'is_category_filter',
                 $root->id
             )
             ->setRootCategory($root->id)
             ->setSelectedCategories($selected_cat)
             ->setUseSearch(true)
             ->setInputName('skipcat');
        $categoryTreeCol1 = $tree->render();

        $types2 = Category::getCategories(
            $this->context->language->id,
            true,
            false
        );
        foreach ($types2 as $key => $type) {
            $types2[$key]['label'] = $type['name'];
        }
        $token = Tools::getAdminTokenLite('AdminModules');
        $back = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&token='.$token;
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-image'
                ),
                'input' => array(

                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Size Chart content'),
                        'name' => 'desc',
                        'lang' => true,
                        'desc' => $this->l('You can put text, images, links'),
                        'cols' => 80,
                        'rows' => 8,
                        'autoload_rte' => true,
                        'class' => 'rte',
                    ),
                    array(
                        'type' => 'categories_select',
                        'label' => $this->l('Shop category to include'),
                        'desc' => $this->l(
                            'Select the categories you want to add'
                        ),
                        'name' => 'skipcat',
                        'category_tree' => $categoryTreeCol1 //This is the category_tree called in form.tpl
                    ),
				),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
   
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitUpdate';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }	
	
	   public function displayFrontForm($params)

    {
        global $cookie, $smarty;
	        $descsc = Configuration::get(
            'SIZESCHART_DESC',
            $this->context->language->id
        );
      
		$this->context->smarty->assign(
            array(
                'descsc' => $descsc,
            )
        );
    
$smarty->assign(array(
				'base' => '<img src="../sizeschart/model.jpg"/>',
		));
        
 
        return $this->display(__FILE__, 'sizeschart2.tpl');

    }

}
