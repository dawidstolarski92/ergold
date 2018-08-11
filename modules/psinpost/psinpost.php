<?php

set_include_path(get_include_path() . PATH_SEPARATOR .  dirname(__FILE__) . '/libraries');
include('Zend/Pdf.php');

if (!defined('_PS_VERSION_')) exit;
define('_INPOST_TPL_DIR_', dirname(__FILE__).'/views/templates/');
define('_INPOST_URI_', _MODULE_DIR_.'psinpost/');
define('_INPOST_JS_URI_', _INPOST_URI_.'js/');
define('_INPOST_IMG_URI_', _INPOST_URI_.'img/');
define('_INPOST_PDF_URI_', _INPOST_URI_.'pdf.php');
define('_INPOST_AJAX_URI_', _INPOST_URI_.'ajax.php');
define('idebug', 1);

class PSInpost extends CarrierModule {
	public $module_url;
	public static $errors = array();
	public $id_carrier;
	const INPOST_MAX_H = 40;
	const INPOST_MAX_L = 60;
	const INPOST_MAX_D = 40;
	const INPOST_MAX_W = 30;
	const INPOST_MOD_NAME = 'INPOST_MOD_NAME'; 
	const INPOST_EKO_ID = 'INPOST_EKO_ID'; 
	const INPOST_EKO_COD_ID = 'INPOST_EKO_COD_ID';
	const INPOST_EKO_PRICE = 'INPOST_EKO_PRICE'; 
	const INPOST_EKO_COD_PRICE = 'INPOST_EKO_COD_PRICE';
	const INPOST_API_LOGIN = 'INPOST_API_LOGIN';
	const INPOST_API_PASS = 'INPOST_API_PASS';
	const INPOST_API_URL = 'INPOST_KURI_API_URL';
	const INPOST_UBEZ = 'INPOST_UBEZ';
	const INPOST_WAGA = 'INPOST_WAGA';
	const INPOST_DLUG = 'INPOST_DLUG';
	const INPOST_SZER = 'INPOST_SZER';
	const INPOST_WYS = 'INPOST_WYS';
	const defCena = 10;
	const defUrl = 'http://api.inpost.opennet.pl/api.asmx?WSDL';
	const defH = 10; 
	const defL = 10; 
	const defD = 10; 
	const defW = 1;
	const trackUrl = 'https://inpost.pl/pl/pomoc/znajdz-przesylke?parcel=@';
	const trackUrl1 = 'https://inpost.pl/pl/pomoc/znajdz-przesylke?';
	const upd_from = 4;
	const upd_to = 5; 
	const pass = 'Inpost';
	
	public function __construct() {
    	$this->name = 'psinpost';
    	$this->tab = 'shipping_logistics';
    	$this->version = '2.2';
    	$this->author = 'Marcin Bogdanski @ Opennet';
    	$this->need_instance = 1;
    	$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7'); 
    	$this->bootstrap = true;
 
 		parent::__construct();
 
    	$this->displayName = $this->l('PSInpost');
    	$this->description = $this->l('Inpost carriers');
    	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
 
 		$warns = array();
    	if(Configuration::get(self::INPOST_API_URL) == self::defUrl) $warns[] = 'Konfiguracja demonstracyjna - ustaw dane do API zgodnie z umową z Inpost';
    	if(!extension_loaded('soap')) $warns[] = 'Brak modułu php soap niezbędnego do działania wtyczki';
		if (version_compare(PHP_VERSION, '5.3.6', '<')) $warns[] = 'Zalecana wersja PHP przynajmniej 5.3.6';
		if(count($warns) > 0) $this->warning = join('<br/>', $warns);
  	}

	public function install() {
		if(!parent::install() || !$this->registerHook('adminOrder') || !$this->registerHook('paymentTop') || !$this->registerHook('updateCarrier')) return false;
		if(!($this->createTables() && $this->createCarrier(self::INPOST_EKO_ID, 'Inpost - kurier') && $this->createCarrier(self::INPOST_EKO_COD_ID, 'Inpost - kurier za pobraniem'))) return false;
		if(!$this->installTab()) return false;
		if(!Configuration::updateValue(self::INPOST_EKO_PRICE, 10)) return false;
		if(!Configuration::updateValue(self::INPOST_EKO_COD_PRICE, 10)) return false;
		if(!Configuration::updateValue(self::INPOST_API_LOGIN, "demo")) return false;
		if(!Configuration::updateValue(self::INPOST_API_PASS, "demo")) return false;
		if(!Configuration::updateValue(self::INPOST_API_URL, self::defUrl)) return false;
		if(!Configuration::updateValue(self::INPOST_UBEZ, 0)) return false;
		if(!Configuration::updateValue(self::INPOST_WAGA, 1)) return false;
		if(!Configuration::updateValue(self::INPOST_DLUG, 10)) return false;
		if(!Configuration::updateValue(self::INPOST_SZER, 10)) return false;
		if(!Configuration::updateValue(self::INPOST_WYS, 10)) return false;
		return true;
	}

	private function createTables() {
		$sql = '
        	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_. 'inpost_label'.'` (
            `id_label` int(11) NOT NULL AUTO_INCREMENT,
            `id_order` int(11) NOT NULL,
            `pdf` mediumblob,
            `api_parcel_id` varchar(255) NOT NULL,
            `api_tracking` varchar(255) NOT NULL,
            `date_ins` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_label`),
            UNIQUE KEY `id_order` (`id_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
    	if(!Db::getInstance()->execute($sql)) return false;
    	return true;
	}

	private function createCarrier($id, $name) {
  		$id_carrier = (int)Configuration::get($id);
        $carrier = Carrier::getCarrierByReference((int)$id_carrier);

        if ($id_carrier && Validate::isLoadedObject($carrier))
        	if (!$carrier->deleted) return true;
            else {
            	$carrier->deleted = 0;
                return (bool)$carrier->save();
            }
  		
  		$carrier = new Carrier();
        $carrier->name = $name;
        $carrier->active = 1;
        $carrier->is_free = 0;
        $carrier->shipping_handling = 1;
        $carrier->shipping_external = 1;
        $carrier->shipping_method = 1;
        $carrier->max_width = self::INPOST_MAX_L;
        $carrier->max_height = self::INPOST_MAX_H;
        $carrier->max_depth = self::INPOST_MAX_D;
        $carrier->max_weight = self::INPOST_MAX_W;
        $carrier->grade = 0;
        $carrier->is_module = 1;
        $carrier->need_range = 1;
        $carrier->range_behavior = 1;
        $carrier->external_module_name = $this->name;
        $carrier->url = self::trackUrl;

        $delay = array();
        foreach (Language::getLanguages(false) as $language)
        	$delay[$language['id_lang']] = $name;
        $carrier->delay = $delay;

        if (!$carrier->save()) return false;
  		
  		$range_obj = $carrier->getRangeObject();
        $range_obj->id_carrier = (int)$carrier->id;
        $range_obj->delimiter1 = 0;
        $range_obj->delimiter2 = self::INPOST_MAX_W;
        if (!$range_obj->save()) return false;

        if (!self::assignGroups($carrier)) return false;
		if (!self::createZone($carrier->id)) return false;
		if (!self::createDelivery($carrier->id, $range_obj->id)) return false;
        if (!Configuration::updateValue($id, (int)$carrier->id)) return false;

  		return true;
	}

	private function assignGroups($carrier) {
		$groups = array();
        foreach (Group::getGroups((int)Context::getContext()->language->id) as $group) $groups[] = $group['id_group'];
        
        if (version_compare(_PS_VERSION_, '1.5.5', '<')) {
        	if(!self::setGroupsOld((int)$carrier->id, $groups)) return false;
        }
        else {
        	if(!$carrier->setGroups($groups)) return false;
        }
        return true;
		//return $carrier->setGroups($groups);
	}

    protected static function setGroupsOld($id_carrier, $groups) {
    	foreach ($groups as $id_group)
        	if(!Db::getInstance()->execute('
            	INSERT INTO `' . _DB_PREFIX_ . 'carrier_group` (`id_carrier`, `id_group`)
                VALUES ("' . (int)$id_carrier . '", "' . (int)$id_group . '")
             	')) return false;
        return true;
	}


    private static function createZone($id_carrier) {
    	return DB::getInstance()->Execute('
        	INSERT INTO `'._DB_PREFIX_.'carrier_zone`
            (`id_carrier`, `id_zone`)
            VALUES
            ("'.(int)$id_carrier.'", "1")');
    }

    private static function createDelivery($id_carrier, $id_range) {
    	return DB::getInstance()->Execute('
        	INSERT INTO `'._DB_PREFIX_.'delivery`
            (`id_carrier`, `id_range_weight`, `id_zone`, `price`)
            VALUES
            ("'.(int)$id_carrier.'", "'.(int)$id_range.'", "1", "10")');
    }

    public function installTab() {
    	$id_parent = (int)Tab::getIdFromClassName('AdminParentShipping');
		if(!$id_parent) return false;

    	$tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminPsInpost';
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang)
        	$tab->name[$lang['id_lang']] = 'Inpost';

        $tab->id_parent = $id_parent;
        $tab->module = $this->name;
        return $tab->add();
    }

    private static function removeZone($id_carrier) {
    	return DB::getInstance()->Execute('
        	DELETE FROM `'._DB_PREFIX_.'carrier_zone`
            WHERE `id_carrier` = "'.(int)$id_carrier.'"
        	');
    }

	public function uninstall() {
  		if(!parent::uninstall()) return false;
  		if(!$this->uninstallTab()) return false;
  		return (bool)self::deleteCarrier((int)Configuration::get(self::INPOST_EKO_ID)) && (bool)self::deleteCarrier((int)Configuration::get(self::INPOST_EKO_COD_ID));
	}

    public function uninstallTab() {
    	$id_tab = (int)Tab::getIdFromClassName('AdminPsInpost');

        if($id_tab) {
        	$tab = new Tab($id_tab);
            return $tab->delete();
        }
        //else return false;
        return true;
    }

	public function getOrderShippingCost($params, $shipping_cost) {
		if(Configuration::get('INPOST_RANGES')) return $shipping_cost;
		return $this->getOrderShippingCostExternal($params);
	}

	public function getOrderShippingCostExternal($params) {
		$cena = null;
 		if($this->id_carrier == (int)(Configuration::get(self::INPOST_EKO_ID))) $cena = Configuration::get(self::INPOST_EKO_PRICE);
 		if($this->id_carrier == (int)(Configuration::get(self::INPOST_EKO_COD_ID))) $cena = Configuration::get(self::INPOST_EKO_COD_PRICE);
 		if($cena == null) $cena = self::defCena;
 		if($cena == 0) $cena = false;
 		return $cena;
	}

	public function hookUpdateCarrier($params) {
  		$id_carrier_old = (int)($params['id_carrier']);
  		$id_carrier_new = (int)($params['carrier']->id);
  		if($id_carrier_old == (int)(Configuration::get(self::INPOST_EKO_ID))) Configuration::updateValue(self::INPOST_EKO_ID, $id_carrier_new);
  		if($id_carrier_old == (int)(Configuration::get(self::INPOST_EKO_COD_ID))) Configuration::updateValue(self::INPOST_EKO_COD_ID, $id_carrier_new);
	}

	public function getContent() {
    	$output = null;
 
    	if (Tools::isSubmit('submit'.$this->name)) {
        	$login = strval(Tools::getValue('INPOST_API_LOGIN'));
        	if (!$login || empty($login) || !Validate::isGenericName($login)) $output .= $this->displayError($this->l('Nieprawidłowy login'));
        	else Configuration::updateValue('INPOST_API_LOGIN', $login);

        	$pass = strval(Tools::getValue('INPOST_API_PASS'));
        	if (!$pass || empty($pass)) $output .= $this->displayError($this->l('Nieprawidłowe hasło'));
        	else Configuration::updateValue('INPOST_API_PASS', $pass);

        	$url = strval(Tools::getValue('INPOST_API_URL'));
        	if (!$url || empty($url)) $output .= $this->displayError($this->l('Nieprawidłowy adres API'));
        	else Configuration::updateValue('INPOST_KURI_API_URL', $url);

        	Configuration::updateValue('INPOST_RANGES', strval(Tools::getValue('INPOST_RANGES_on')));

        	$cena1 = strval(Tools::getValue('INPOST_EKO_PRICE'));
        	if (!is_numeric($cena1) || ($cena1 < 0)) $output .= $this->displayError($this->l('Nieprawidłowa cena'));
        	else Configuration::updateValue('INPOST_EKO_PRICE', $cena1);

        	$cena2 = strval(Tools::getValue('INPOST_EKO_COD_PRICE'));
        	if (!is_numeric($cena2) || ($cena2 < 0)) $output .= $this->displayError($this->l('Nieprawidłowa cena COD'));
        	else Configuration::updateValue('INPOST_EKO_COD_PRICE', $cena2);
        	
        	$ubez = strval(Tools::getValue('INPOST_UBEZ'));
        	if (!is_numeric($ubez) || ($ubez < 0)) $output .= $this->displayError($this->l('Nieprawidłowa kwota ubezpieczenia'));
        	else Configuration::updateValue('INPOST_UBEZ', $ubez);

        	$waga = strval(Tools::getValue('INPOST_WAGA'));
        	if (!is_numeric($waga) || ($waga < 0)) $output .= $this->displayError($this->l('Nieprawidłowa waga'));
        	else Configuration::updateValue('INPOST_WAGA', $waga);

        	$dlug = strval(Tools::getValue('INPOST_DLUG'));
        	if (!is_numeric($dlug) || ($dlug < 0)) $output .= $this->displayError($this->l('Nieprawidłowa długość'));
        	else Configuration::updateValue('INPOST_DLUG', $dlug);

        	$szer = strval(Tools::getValue('INPOST_SZER'));
        	if (!is_numeric($szer) || ($szer < 0)) $output .= $this->displayError($this->l('Nieprawidłowa szerokość'));
        	else Configuration::updateValue('INPOST_SZER', $szer);

        	$wys = strval(Tools::getValue('INPOST_WYS'));
        	if (!is_numeric($wys) || ($wys < 0)) $output .= $this->displayError($this->l('Nieprawidłowa wysokość'));
        	else Configuration::updateValue('INPOST_WYS', $wys);

        	Configuration::updateValue('INPOST_REQ_REF', strval(Tools::getValue('INPOST_REQ_REF_on')));
        	Configuration::updateValue('INPOST_REQ_SMS', strval(Tools::getValue('INPOST_REQ_SMS_on')));
        	Configuration::updateValue('INPOST_REQ_MAIL', strval(Tools::getValue('INPOST_REQ_MAIL_on')));
        	Configuration::updateValue('INPOST_NST', strval(Tools::getValue('INPOST_NST_on')));
        	Configuration::updateValue('INPOST_ZAW', strval(Tools::getValue('INPOST_ZAW_on')));
        	Configuration::updateValue('INPOST_NBUFOR', strval(Tools::getValue('INPOST_NBUFOR_on')));
        	Configuration::updateValue('INPOST_MPACK', strval(Tools::getValue('INPOST_MPACK_on')));
        	
        	if(Tools::getValue('INPOST_CBUFOR_on')) {
    			DB::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'inpost_label` SET `pdf`=NULL');        		
        	}

        	Configuration::updateValue('INPOST_NAD_NAZ', strval(Tools::getValue('INPOST_NAD_NAZ')));
        	Configuration::updateValue('INPOST_NAD_ADR', strval(Tools::getValue('INPOST_NAD_ADR')));
        	Configuration::updateValue('INPOST_NAD_MIASTO', strval(Tools::getValue('INPOST_NAD_MIASTO')));
        	Configuration::updateValue('INPOST_NAD_KOD', strval(Tools::getValue('INPOST_NAD_KOD')));
        	Configuration::updateValue('INPOST_NAD_OSOBA', strval(Tools::getValue('INPOST_NAD_OSOBA')));
        	Configuration::updateValue('INPOST_NAD_TEL', strval(Tools::getValue('INPOST_NAD_TEL')));
        	Configuration::updateValue('INPOST_NAD_EMAIL', strval(Tools::getValue('INPOST_NAD_EMAIL')));
    	}
    	return $output.$this->displayForm();
   	}
	
	public function displayForm() {
    // Get default language
    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
     
    // Init Fields form array
    $fields_form[0]['form'] = array(
        'legend' => array(
            'title' => $this->l('Settings'),
        ),
        'input' => array(
            array(
                'type' => 'text',
                'label' => 'Inpost API - login',
                'name' => 'INPOST_API_LOGIN',
                'size' => 20,
                'required' => true
            ),
            array(
                'type' => 'password',
                'label' => 'Inpost API - hasło',
                'name' => 'INPOST_API_PASS',
                'size' => 20,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => 'Inpost API - url',
                'name' => 'INPOST_API_URL',
                'size' => 200,
                'required' => true,
                'desc' => 'Po uzyskaniu z Inpost loginu i hasła zmień url na https://api-kurier.inpost.pl/api/api.asmx?WSDL'
            ),
            array(
            	'type' => 'checkbox',
                'name' => 'INPOST_RANGES',
                'values' => array(
                	'query' => array(
                    	array(
                        	'id' => 'on',
                            'name' => 'Ceny w/g przedziałów',
                            'val' => '1'
                        ),
                    ),
               		'id' => 'id',
                	'name' => 'name'
          		)
            ),
            array(
                'type' => 'text',
                'label' => 'Cena przesyłki',
                'name' => 'INPOST_EKO_PRICE',
                'size' => 20,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => 'Cena przesyłki pobraniowej',
                'name' => 'INPOST_EKO_COD_PRICE',
                'size' => 50,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => 'Domyślna kwota ubezpieczenia',
                'name' => 'INPOST_UBEZ',
                'size' => 50,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => 'Domyślna waga (kg)',
                'name' => 'INPOST_WAGA',
                'size' => 50,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => 'Domyślna wysokość (cm)',
                'name' => 'INPOST_WYS',
                'size' => 50,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => 'Domyślna długość (cm)',
                'name' => 'INPOST_DLUG',
                'size' => 50,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => 'Domyślna szerokość (cm)',
                'name' => 'INPOST_SZER',
                'size' => 50,
                'required' => true
            ),
            array(
            	'type' => 'checkbox',
                'name' => 'INPOST_REQ_REF',
                'values' => array(
                	'query' => array(
                    	array(
                        	'id' => 'on',
                            'name' => 'Drukować numer zamówienia na etykiecie',
                            'val' => '1'
                        ),
                    ),
               		'id' => 'id',
                	'name' => 'name'
          		)
            ),
            array(
            	'type' => 'checkbox',
                'name' => 'INPOST_REQ_SMS',
                'values' => array(
                	'query' => array(
                    	array(
                        	'id' => 'on',
                            'name' => 'Powiadomienie SMS',
                            'val' => '1'
                        ),
                    ),
               		'id' => 'id',
                	'name' => 'name'
          		)
            ),
            array(
            	'type' => 'checkbox',
                'name' => 'INPOST_REQ_MAIL',
                'values' => array(
                	'query' => array(
                    	array(
                        	'id' => 'on',
                            'name' => 'Powiadomienie e-mail',
                            'val' => '1'
                        ),
                    ),
               		'id' => 'id',
                	'name' => 'name'
          		)
            ),
            array(
            	'type' => 'checkbox',
                'name' => 'INPOST_NST',
                'values' => array(
                	'query' => array(
                    	array(
                        	'id' => 'on',
                            'name' => 'Domyślnie włączony NST',
                            'val' => '1'
                        ),
                    ),
               		'id' => 'id',
                	'name' => 'name'
          		)
            ),
            array(
            	'type' => 'checkbox',
                'name' => 'INPOST_ZAW',
                'values' => array(
                	'query' => array(
                    	array(
                        	'id' => 'on',
                            'name' => 'Drukowanie zawartości w polu uwagi',
                            'val' => '1'
                        ),
                    ),
               		'id' => 'id',
                	'name' => 'name'
          		)
            ),
            array(
            	'type' => 'checkbox',
                'name' => 'INPOST_NBUFOR',
                'values' => array(
                	'query' => array(
                    	array(
                        	'id' => 'on',
                            'name' => 'Nie zapisuj etykiet w bazie',
                            'val' => '1'
                        ),
                    ),
               		'id' => 'id',
                	'name' => 'name'
          		)
            ),
            array(
            	'type' => 'checkbox',
                'name' => 'INPOST_CBUFOR',
                'values' => array(
                	'query' => array(
                    	array(
                        	'id' => 'on',
                            'name' => 'Wyczyść bazę etykiet',
                            'val' => '1'
                        ),
                    ),
               		'id' => 'id',
                	'name' => 'name'
          		)
            ),
            array(
            	'type' => 'checkbox',
                'name' => 'INPOST_MPACK',
                'values' => array(
                	'query' => array(
                    	array(
                        	'id' => 'on',
                            'name' => 'Obsługa etykiet wielopaczkowych',
                            'val' => '1'
                        ),
                    ),
               		'id' => 'id',
                	'name' => 'name'
          		)
            ),
            array(
                'type' => 'text',
                'label' => 'Nazwa nadawcy',
                'name' => 'INPOST_NAD_NAZ',
                'size' => 40,
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => 'Adres nadawcy',
                'name' => 'INPOST_NAD_ADR',
                'size' => 120,
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => 'Miasto',
                'name' => 'INPOST_NAD_MIASTO',
                'size' => 20,
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => 'Kod pocztowy',
                'name' => 'INPOST_NAD_KOD',
                'size' => 8,
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => 'Osoba',
                'name' => 'INPOST_NAD_OSOBA',
                'size' => 40,
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => 'Telefon',
                'name' => 'INPOST_NAD_TEL',
                'size' => 20,
                'required' => false
            ),
            array(
                'type' => 'text',
                'label' => 'Email',
                'name' => 'INPOST_NAD_EMAIL',
                'size' => 40,
                'required' => false
            ),
        ),
        'submit' => array(
            'title' => $this->l('Save'),
            'class' => 'button'
        )
    );
     
    $helper = new HelperForm();
     
    // Module, token and currentIndex
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
     
    // Language
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;
     
    // Title and toolbar
    $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit'.$this->name;
    $helper->toolbar_btn = array(
        'save' =>
        array(
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
            '&token='.Tools::getAdminTokenLite('AdminModules'),
        ),
        'back' => array(
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to list')
        )
    );
     
    // Load current value
    $helper->fields_value['INPOST_API_LOGIN'] = Configuration::get('INPOST_API_LOGIN');
    $helper->fields_value['INPOST_API_PASS'] = Configuration::get('INPOST_API_PASS');
    $helper->fields_value['INPOST_API_URL'] = Configuration::get('INPOST_KURI_API_URL') ? Configuration::get('INPOST_KURI_API_URL') : Configuration::get('INPOST_API_URL');
    $helper->fields_value['INPOST_RANGES_on'] = Configuration::get('INPOST_RANGES');
    $helper->fields_value['INPOST_EKO_PRICE'] = Configuration::get('INPOST_EKO_PRICE');
    $helper->fields_value['INPOST_EKO_COD_PRICE'] = Configuration::get('INPOST_EKO_COD_PRICE');
    $helper->fields_value['INPOST_UBEZ'] = Configuration::get('INPOST_UBEZ');
    $helper->fields_value['INPOST_WAGA'] = Configuration::get('INPOST_WAGA');
    $helper->fields_value['INPOST_WYS'] = Configuration::get('INPOST_WYS');
    $helper->fields_value['INPOST_DLUG'] = Configuration::get('INPOST_DLUG');
    $helper->fields_value['INPOST_SZER'] = Configuration::get('INPOST_SZER');
    $helper->fields_value['INPOST_REQ_REF_on'] = Configuration::get('INPOST_REQ_REF');
    $helper->fields_value['INPOST_REQ_SMS_on'] = Configuration::get('INPOST_REQ_SMS');
    $helper->fields_value['INPOST_REQ_MAIL_on'] = Configuration::get('INPOST_REQ_MAIL');
    $helper->fields_value['INPOST_NST_on'] = Configuration::get('INPOST_NST');
    $helper->fields_value['INPOST_ZAW_on'] = Configuration::get('INPOST_ZAW');
    $helper->fields_value['INPOST_NBUFOR_on'] = Configuration::get('INPOST_NBUFOR');
    $helper->fields_value['INPOST_MPACK_on'] = Configuration::get('INPOST_MPACK');
    $helper->fields_value['INPOST_NAD_NAZ'] = Configuration::get('INPOST_NAD_NAZ');
    $helper->fields_value['INPOST_NAD_ADR'] = Configuration::get('INPOST_NAD_ADR');
    $helper->fields_value['INPOST_NAD_MIASTO'] = Configuration::get('INPOST_NAD_MIASTO');
    $helper->fields_value['INPOST_NAD_KOD'] = Configuration::get('INPOST_NAD_KOD');
    $helper->fields_value['INPOST_NAD_OSOBA'] = Configuration::get('INPOST_NAD_OSOBA');
    $helper->fields_value['INPOST_NAD_TEL'] = Configuration::get('INPOST_NAD_TEL');
    $helper->fields_value['INPOST_NAD_EMAIL'] = Configuration::get('INPOST_NAD_EMAIL');
     
    return $helper->generateForm($fields_form);
}

	private static function deleteCarrier($id_carrier) {
    	if (!$id_carrier) return true;

        $carrier = Carrier::getCarrierByReference($id_carrier);

        if(!Validate::isLoadedObject($carrier)) return true;
		if ($carrier->deleted) return true;
		
        $carrier->deleted = 1;
        return (bool)$carrier->save();
	}

	public static function getCOD($order) {
		$pobranie = 0;
		if(idebug) error_log(print_r($order, true));
 		if($order->module == 'cashondelivery') $pobranie = $order->total_products_wt + $order->total_shipping_tax_incl + $order->total_wrapping_tax_incl - $order->total_discounts;
		return $pobranie;
	}

	private static function getIdOrderCarrier($order) {
        if (version_compare(_PS_VERSION_, '1.5.5', '<')) {
	        return (int)Db::getInstance()->getValue('
    	        SELECT `id_order_carrier`
                FROM `'._DB_PREFIX_.'order_carrier`
                WHERE `id_order` = '.(int)$order->id);
        }
        else {
        	return (int)$order->getIdOrderCarrier();
        }		
	}

	public function hookAdminOrder($params) {
		$order = new Order((int)$params['id_order']);
		$carrier = new Carrier((int)$order->id_carrier);
		if(($order->id_carrier != (int)(PSInpost::getConf(self::INPOST_EKO_COD_ID, $order))) && ($order->id_carrier != (int)(PSInpost::getConf(self::INPOST_EKO_ID, $order))) && (stripos($carrier->name, 'Inpost') === false)) return '';
		$address_r = new Address($order->id_address_delivery);
		$order_carrier = new OrderCarrier(PSInpost::getIdOrderCarrier($order));
		$pobranie = PSInpost::getCOD($order);
		$aprod = $order->getProducts();

		$waga = $order_carrier->weight;
		$wys = 0;
		$szer = 0;
		$dlug = 0;
		if(count($aprod) == 1) {
			$prod = array_pop($aprod);
			$wys = $prod['height'];
			$szer = $prod['depth'];
			$dlug = $prod['width'];
		}
		
		if($waga <= 0) $waga = PSInpost::getConf('INPOST_WAGA', $order);
		if($wys <= 0) $wys = PSInpost::getConf('INPOST_WYS', $order);
		if($szer <= 0) $szer = PSInpost::getConf('INPOST_SZER', $order);
		if($dlug <= 0) $dlug = PSInpost::getConf('INPOST_DLUG', $order);
		if($waga <= 0) $waga = self::defW;
		if($wys <= 0) $wys = self::defH;
		if($szer <= 0) $szer = self::defD;
		if($dlug <= 0) $dlug = self::defL;
		$ubez = PSInpost::getConf('INPOST_UBEZ', $order);
		$nst = PSInpost::getConf('INPOST_NST', $order) ? "checked" : "";
		$mail = PSInpost::getConf('INPOST_REQ_MAIL', $order) ? "checked" : "";
		$sms = PSInpost::getConf('INPOST_REQ_SMS', $order) ? "checked" : "";

		$customer = new Customer((int)$order->id_customer);
		$this->context->controller->addJqueryUI(array(
			'ui.core',
			'ui.widget',
			'ui.accordion'
			));

			$this->context->controller->addJqueryPlugin('scrollTo');
			$this->context->controller->addJS(_INPOST_JS_URI_.'adminOrder.js');
			$id_label = $this->getLabelByOrder((int)$params['id_order']);
			if(!$id_label) $id_label = '';
			$this->context->smarty->assign(array(
				'order' => $order,
				'inpost_ajax_uri' => _INPOST_AJAX_URI_,
				'inpost_img_uri' => _INPOST_IMG_URI_,
				'inpost_token' => sha1(_COOKIE_KEY_.$this->name),
				'id_label' => $id_label,
				'inpost_kwota' => $pobranie,
				'inpost_adres' => $address_r->address1 . ($address_r->address2 != '' ? (' ' . $address_r->address2) : ''),
				'inpost_kod' => trim($address_r->postcode),
				'inpost_miasto' => $address_r->city,
				'inpost_uwagi' => PSInpost::genUwagi($order),
				'inpost_ubez' => $ubez,
				'inpost_waga' => $waga,
				'inpost_szer' => $szer,
				'inpost_dlug' => $dlug,
				'inpost_wys' => $wys,
				'inpost_nst' => $nst,
				'inpost_mail' => $mail,
				'inpost_sms' => $sms,
				'inpost_track_url' => PSInpost::genTrackLink($this->getTrackByOrder((int)$params['id_order']))
			));
		if(PSInpost::getConf('INPOST_MPACK', $order)) {
			if(version_compare(_PS_VERSION_, '1.6', '<')) return $this->context->smarty->fetch(_INPOST_TPL_DIR_.'admin/adminOrderMpack15.tpl');
			return $this->context->smarty->fetch(_INPOST_TPL_DIR_.'admin/adminOrderMpack.tpl');
		}
		else {
			if(version_compare(_PS_VERSION_, '1.6', '<')) return $this->context->smarty->fetch(_INPOST_TPL_DIR_.'admin/adminOrder15.tpl');
			return $this->context->smarty->fetch(_INPOST_TPL_DIR_.'admin/adminOrder.tpl');			
		}
	}

	public static function genUwagi($order) {
		$ret = PSInpost::getConf('INPOST_REQ_REF', $order) ? 'Zamówienie ' . $order->reference : '';
		
		if(PSInpost::getConf('INPOST_ZAW', $order)) {
			$oprod = array(); 
			$aprod = $order->getProducts();
			foreach($aprod as $prod) {
				if(idebug) error_log(print_r($prod, true));
				$oprod[] = $prod['product_name'] . ($prod['product_quantity'] != 1 ? ' (' . $prod['product_quantity'] . ' szt)' : '');
			}
			$ret .= ($ret ? ' / ' : '') . implode(', ', $oprod);			
		}
		
		return $ret;
	}

	public function createLabelJson($json) {		
		if(idebug) error_log('json=' . print_r($json, true));
		$resp = $this->createLabel($json['id_order'], $json['inpost_kwota'], $json['inpost_adres'], $json['inpost_kod'], $json['inpost_miasto'], $json['inpost_uwagi'], $json['inpost_ubezp'], null, null, null, null, null, $json['inpost_mail'], $json['inpost_sms'], $json['paczki']);
		return $resp;
	}

	public function createLabel($id_order, $kwota = 'undefined', $i_adr = 'undefined', $i_kod = 'undefined', $i_mi = 'undefined', $i_uwg = 'undefined', $i_ubezp = 'undefined', $i_waga = 'undefined', $i_dlug = 'undefined', $i_szer = 'undefined', $i_wys = 'undefined', $i_nst = 'undefined', $i_mail = 'undefined', $i_sms = 'undefined', $paczki = null) {
        try {
			if(!extension_loaded('soap')) {
				PSInpost::$errors[] = 'Brak modułu PHP-soap';
				if(idebug) error_log('brak mod');
	        	return false;
			}
			$log = "";
			$order = new Order((int)$id_order);
			$log .= print_r($order, true);
			$customer = new Customer((int)$order->id_customer);
			$log .= print_r($customer, true);
	// 		$cart = new Cart((int)$order->id_cart);
	//		$log .= print_r($cart, true);
			$shop = new Shop($order->id_shop);
			$log .= print_r($shop, true);
			$address_r = new Address($order->id_address_delivery);
			$log .= print_r($address_r, true);
			$order_carrier = new OrderCarrier(PSInpost::getIdOrderCarrier($order));
			$log .= print_r($order_carrier, true);
			$aprod = $order->getProducts();
			$log .= print_r($aprod, true);
			
			$url = PSInpost::getConf('INPOST_KURI_API_URL', $order) ? PSInpost::getConf('INPOST_KURI_API_URL', $order) : PSInpost::getConf('INPOST_API_URL', $order);
			if(!is_string($url) || (substr($url, 0, 4) != "http")) $url = 'https://api-kurier.inpost.pl/api/api.asmx?WSDL';
	        $client = new SoapClient($url);
	        $log .= "URL=$url\n";

			$waga = $order_carrier->weight;
			$wys = 0;
			$szer = 0;
			$dlug = 0;
			if(count($aprod) == 1) {
				$prod = array_pop($aprod);
				$wys = $prod['height'];
				$szer = $prod['depth'];
				$dlug = $prod['width'];
			}
			
			if($waga <= 0) $waga = PSInpost::getConf('INPOST_WAGA', $order);
			if($wys <= 0) $wys = PSInpost::getConf('INPOST_WYS', $order);
			if($szer <= 0) $szer = PSInpost::getConf('INPOST_SZER', $order);
			if($dlug <= 0) $dlug = PSInpost::getConf('INPOST_DLUG', $order);
			if($waga <= 0) $waga = self::defW;
			if($wys <= 0) $wys = self::defH;
			if($szer <= 0) $szer = self::defD;
			if($dlug <= 0) $dlug = self::defL;

			$ubez = ($i_ubezp != 'undefined' && $i_ubezp != '' ? $i_ubezp : PSInpost::getConf('INPOST_UBEZ', $order));
			if($ubez <= 0) $ubez = 0;
			if($i_ubezp != 'undefined' && $i_ubezp != '' && !is_numeric($i_ubezp)) {
				PSInpost::$errors[] = 'Ubezpieczenie - błędny format liczby';
	        	return false;
			}
			if($i_waga != 'undefined' && $i_waga != '' && !is_numeric($i_waga)) {
				PSInpost::$errors[] = 'Waga - błędny format liczby';
	        	return false;
			}
			if($i_wys != 'undefined' && $i_wys != '' && !is_numeric($i_wys)) {
				PSInpost::$errors[] = 'Wysokość - błędny format liczby';
	        	return false;
			}
			if($i_dlug != 'undefined' && $i_dlug != '' && !is_numeric($i_dlug)) {
				PSInpost::$errors[] = 'Długość - błędny format liczby';
	        	return false;
			}
			if($i_szer != 'undefined' && $i_szer != '' && !is_numeric($i_szer)) {
				PSInpost::$errors[] = 'Szerokość - błędny format liczby';
	        	return false;
			}
	
			$format = 0;
	//		$format = $this->getConfigData('labelFormat');
			if(!$format) $format = 7;
			
			$metoda = 38;
			$pobranie = PSInpost::getCOD($order);
	 		if($kwota != 'undefined' && $kwota != '') $pobranie = $kwota;
			$dg = PSInpost::getReadyDate();
			if($i_nst == 'undefined') $i_nst = PSInpost::getConf('INPOST_NST', $order);
			if($i_mail == 'undefined') $i_mail = PSInpost::getConf('INPOST_REQ_MAIL', $order);
			if($i_sms == 'undefined') $i_sms = PSInpost::getConf('INPOST_REQ_SMS', $order);
	
	        $params = array(
	        	"token" => array(
	      			"UserName" => PSInpost::getConf('INPOST_API_LOGIN', $order),
	      			"Password" => PSInpost::getConf('INPOST_API_PASS', $order)
	      		),
	      		"ShipmentRequest" => array(
		            "ServiceId" => $metoda,
	            	"ShipFrom" => array(
	            		"PointId" => "",
	                	"ClientTaxId" => '',
	                	"Name" => PSInpost::getConf('INPOST_NAD_NAZ', $order) != '' ? PSInpost::getConf('INPOST_NAD_NAZ', $order) : PSInpost::getConf('PS_SHOP_NAME', $order),
	                	"Address" => PSInpost::getConf('INPOST_NAD_ADR', $order) != '' ? PSInpost::getConf('INPOST_NAD_ADR', $order) : (PSInpost::getConf('PS_SHOP_ADDR1', $order) . (PSInpost::getConf('PS_SHOP_ADDR2', $order) != '' ? ' ' . PSInpost::getConf('PS_SHOP_ADDR2', $order) : '')),
	                	"City" => PSInpost::getConf('INPOST_NAD_MIASTO', $order) != '' ? PSInpost::getConf('INPOST_NAD_MIASTO', $order) : PSInpost::getConf('PS_SHOP_CITY', $order),
	                	"PostCode" => trim(PSInpost::getConf('INPOST_NAD_KOD', $order) != '' ? PSInpost::getConf('INPOST_NAD_KOD', $order) : PSInpost::getConf('PS_SHOP_CODE', $order)),
	                	"CountryCode" => 'PL',
	                	"Person" => PSInpost::getConf('INPOST_NAD_OSOBA', $order) != '' ? PSInpost::getConf('INPOST_NAD_OSOBA', $order) : PSInpost::getConf('PS_SHOP_NAME', $order),
	                	"Contact" => PSInpost::getConf('INPOST_NAD_TEL', $order) != '' ? PSInpost::getConf('INPOST_NAD_TEL', $order) : PSInpost::getConf('PS_SHOP_PHONE', $order),
		                "Email" => PSInpost::getConf('INPOST_NAD_EMAIL', $order) != '' ? PSInpost::getConf('INPOST_NAD_EMAIL', $order) : PSInpost::getConf('PS_SHOP_EMAIL', $order),
	                	"IsPrivatePerson" => false
	            	),
	            	"ShipTo" => array(
	            		"PointId" => "",
	                	"ClientTaxId" => $address_r->vat_number,
		                "Name" => $address_r->company != '' ? $address_r->company : $address_r->firstname . ' ' . $address_r->lastname,
	    	            "Address" => $i_adr != 'undefined' ? $i_adr : ($address_r->address1 . ($address_r->address2 != '' ? (' ' . $address_r->address2) : '')),
	        	        "City" => $i_mi != 'undefined' ? $i_mi : $address_r->city,
	            	    "PostCode" => trim($i_kod != 'undefined' ? $i_kod : $address_r->postcode),
	                	"CountryCode" => 'PL',
	                	"Person" => ($address_r->firstname . $address_r->lastname) != '' ? $address_r->firstname . ' ' . $address_r->lastname : $address_r->company,
		                "Contact" => $address_r->phone_mobile != '' ? $address_r->phone_mobile : $address_r->phone,
	    	            "Email" => $customer->email,
	                	"IsPrivatePerson" => true
	            	),
		            "Parcels" => isset($paczki) ? array() : array(
	    	            "Parcel" => array(
	        	            "Type" => 'Package',
	            	        "Weight" => $i_waga != 'undefined' && $i_waga != '' ? $i_waga : $waga,
	                	    "D" => $i_dlug != 'undefined' && $i_dlug != '' ? $i_dlug : $dlug,
	                    	"W" => $i_wys != 'undefined' && $i_wys != '' ? $i_wys : $wys,
		                    "S" => $i_szer != 'undefined' && $i_szer != '' ? $i_szer : $szer,
		                    "IsNST" => ($i_nst == 1)
	    	            )
	        	    ),
	            	"COD" => array(
	                	"Amount" => $pobranie,
		                "RetAccountNo" => 0
	    	        ),
	    	        "InsuranceAmount" => $pobranie != 0 ? $pobranie : $ubez,
	            	"MPK" => "",
	            	"ContentDescription" => $i_uwg != 'undefined' ? $i_uwg : PSInpost::genUwagi($order),
	            	"rabateCoupon" => 0,
	            	"LabelFormat" => 'PDF',
	            	"AdditionalServices" => array()
	      		)      		
	        );
	        if(isset($paczki)) {
	        	$params['ShipmentRequest']['Parcels']['Parcel'] = array();
	        	foreach($paczki as $paka) {
	        		$params['ShipmentRequest']['Parcels']['Parcel'][] = array(
//	        			"" => array(
	        	    		"Type" => 'Package',
	            	    	"Weight" => $paka['waga'],
	                		"D" => $paka['dlug'],
	                    	"W" => $paka['wys'],
		                	"S" => $paka['szer'],
		                	"IsNST" => $paka['nst']
//	    	        	)
	        		);
	        	}
	        }
	        if($i_mail) $params['ShipmentRequest']['AdditionalServices']['AdditionalService'][] = array('Code' => 'EMAIL');
			if($i_sms) $params['ShipmentRequest']['AdditionalServices']['AdditionalService'][] = array('Code' => 'SMS');
	        
	        $log .= print_r($params, true);
	        if(idebug) PrestaShopLogger::addLog(str_replace("\n", '|', $log), 1, null, 'Inpost');
	        if(idebug) error_log($log);
		
        	$response = $client->__soapCall("CreateShipment", array($params));        	
			if(!isset($response->CreateShipmentResult->responseDescription) || ($response->CreateShipmentResult->responseDescription != 'Success')) {
				if(idebug) error_log(print_r($response, true));
	        	PSInpost::$errors[] = "Order #$id_order: " . '[' . $response->CreateShipmentResult->responseCode . '] ' . $response->CreateShipmentResult->responseDescription;
	        	return false;
			}
			$api_id = $response->CreateShipmentResult->PackageNo;
			if(isset($paczki)) {
				$pdfs = array();
				$tr = array();
				if(is_object($response->CreateShipmentResult->ParcelData->Label)) $response->CreateShipmentResult->ParcelData->Label = array($response->CreateShipmentResult->ParcelData->Label);
				foreach($response->CreateShipmentResult->ParcelData->Label as $label) {
					$pdfs[] = $label->MimeData;
					$tr[] = $label->ParcelID;
					$label->MimeData = "[" . strlen($label->MimeData) . "]";	// Aby w logu nie było śmieci
				}
				$pdf = new Zend_Pdf();
				foreach($pdfs as $p) {
					$pdf1 = Zend_Pdf::parse($p);
					foreach ($pdf1->pages as $page) {
  						$pdf->pages[] = clone $page;
					}
				}
	
				$shippingLabelContent = $pdf->render();
				$track = join(',', $tr);
			}
			else {
				$shippingLabelContent = $response->CreateShipmentResult->ParcelData->Label->MimeData;
				$track = $response->CreateShipmentResult->ParcelData->Label->ParcelID;
				$response->CreateShipmentResult->ParcelData->Label->MimeData = "[" . strlen($shippingLabelContent) . "]";	// Aby w logu nie było śmieci	
			}
	        if(idebug) PrestaShopLogger::addLog(str_replace("\n", '|', print_r($response, true)), 1, null, 'Inpost');
			if(idebug) error_log(print_r($response, true));
			$order_carrier->tracking_number = '';
	        $order_carrier->update();
	        $order->shipping_number = $api_id;
            $order->update();
	        $resp = array();
	        $resp[] = PSInpost::savePdf($order, $shippingLabelContent, $track, $api_id);
	        $resp[] = PSInpost::genTrackLink($track);
			return $resp[0] ? $resp : false;
        } catch(Exception $e) {
        	PSInpost::$errors[] = "Order #$id_order: " . $e->getMessage();
        	return false;
        }
	}

	public static function getConf($n, $order) {
		return Configuration::get($n, null, null, $order->id_shop);
	}

	private function savePdf($order, $pdf, $track, $api_id) {
		$id = PSInpost::getLabelByOrder($order->id);
		
		$epdf = DB::getInstance()->_escape($pdf);
		$pdf_sql = '"' . $epdf . '"';

		if($id) {	// Update
//			if(PSInpost::getConf('INPOST_NBUFOR', $order)) $pdf_sql = 'NULL';
    		if(!DB::getInstance()->Execute('
        		UPDATE `'._DB_PREFIX_.'inpost_label` SET
           		`pdf`=' . $pdf_sql . ',
           		`api_parcel_id`="' . $api_id . '",
           		`api_tracking`="' . $track . '",
           		`date_upd`=now()
           		WHERE id_label=' . $id . '
            	')) {
					PSInpost::$errors[] = "Order #" . $order->id . ": " . 'Database error!';
					return false;		            	
            	}
			return $id;
		}
		// Insert
    	if(!DB::getInstance()->Execute('
        	INSERT INTO `'._DB_PREFIX_.'inpost_label`
            (`id_order`, `pdf`, `api_parcel_id`, `api_tracking`, `date_ins`, `date_upd`)
            VALUES
            ("' . (int)$order->id . '",
           	' . $pdf_sql . ',
           	"' . $api_id . '",
           	"' . $track . '",
           	now(),
           	now()
            )')) {
				PSInpost::$errors[] = "Order #" . $order->id . ": " . 'Database error!';
				return false;		            	
            }

		return PSInpost::getLabelByOrder($order->id);
	}

	private function escape_string($s) {
		if(function_exists('mysqli_real_escape_string')) return mysqli_real_escape_string(DB::getInstance()->getLink(), $s);
		if(function_exists('mysql_real_escape_string')) return mysql_real_escape_string($s);
		return "X";
	}

    public function getLabelByOrder($id_order) {
    	return Db::getInstance()->getValue('
        	SELECT `id_label`
            FROM `'._DB_PREFIX_.'inpost_label`
            WHERE `id_order`='.(int)$id_order
            );
    }

    public function getTrackByOrder($id_order) {
    	$row =  Db::getInstance()->getRow('
        	SELECT `api_tracking`,`api_parcel_id`
            FROM `'._DB_PREFIX_.'inpost_label`
            WHERE `id_order`='.(int)$id_order
            );
//        error_log(print_r($row, true));
        $ret = $row['api_tracking'];
        if(!$ret) $ret = $row['api_parcel_id'];
        return $ret;
    }

    public function getLabelPdf($id_label) {
    	$row = Db::getInstance()->getRow('
        	SELECT `id_order`,`pdf`,`api_parcel_id`
            FROM `'._DB_PREFIX_.'inpost_label`
            WHERE `id_label`='.(int)$id_label
            );
		$pdf = $row['pdf'];
		$id_order = $row['id_order'];
		$order = new Order((int)$id_order);
		$nbuf = PSInpost::getConf('INPOST_NBUFOR', $order);
		if(($pdf != null) && $nbuf) {
			DB::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'inpost_label` SET `pdf`=NULL WHERE id_label=' . $id_label);
		}
		if($pdf == null) {
			$pdf = $this->getLabelPdfApi($row['api_parcel_id'], $order);
			$epdf = DB::getInstance()->_escape($pdf);
			if(!$nbuf) DB::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'inpost_label` SET `pdf`="' . $epdf . '" WHERE id_label=' . $id_label);
		}
        return $pdf;
    }

	public function getLabelPdfApi($api_parcel_id, $order) {
		$url = PSInpost::getConf('INPOST_KURI_API_URL', $order) ? PSInpost::getConf('INPOST_KURI_API_URL', $order) : PSInpost::getConf('INPOST_API_URL', $order);
		if(!is_string($url) || (substr($url, 0, 4) != "http")) $url = 'https://api-kurier.inpost.pl/api/api.asmx?WSDL';
        $client = new SoapClient($url);

        $params = array(
        	"token" => array(
      			"UserName" => PSInpost::getConf('INPOST_API_LOGIN', $order),
      			"Password" => PSInpost::getConf('INPOST_API_PASS', $order)
      		),
      		"request" => array(
		           "PackageNo" =>  array(
	           			"string" => $api_parcel_id
		           )
	      	)      		
	    );
	    $log = print_r($params, true);
	    if(idebug) PrestaShopLogger::addLog(str_replace("\n", '|', $log), 1, null, 'Inpost');
	    if(idebug) error_log($log);
		
        $response = $client->__soapCall("GetLabel", array($params));
        //if(idebug) error_log(print_r($response, true));        	
		if(!isset($response->GetLabelResult->responseDescription) || ($response->GetLabelResult->responseDescription != 'Success')) {
        	return false;
		}
		if(is_array($response->GetLabelResult->LabelData->Label)) {
			$pdfs = array();
			$tr = array();
			foreach($response->GetLabelResult->LabelData->Label as $label) {
				$pdfs[] = $label->MimeData;
				$label->MimeData = "[" . strlen($label->MimeData) . "]";	// Aby w logu nie było śmieci
			}
			$pdf = new Zend_Pdf();
			foreach($pdfs as $p) {
				$pdf1 = Zend_Pdf::parse($p);
				foreach ($pdf1->pages as $page) {
					$pdf->pages[] = clone $page;
				}
			}
	
			$shippingLabelContent = $pdf->render();
		}
		else {
			$shippingLabelContent = $response->GetLabelResult->LabelData->Label->MimeData;
			$response->GetLabelResult->LabelData->Label->MimeData = "[" . strlen($shippingLabelContent) . "]";	// Aby w logu nie było śmieci	
		}
	    if(idebug) PrestaShopLogger::addLog(str_replace("\n", '|', print_r($response, true)), 1, null, 'Inpost');
		if(idebug) error_log(print_r($response, true));
		return $shippingLabelContent;
	}

	private function getReadyDate($h = 0) {
		$ro = $h;
		if(!is_numeric($ro)) $ro = 2;
		$d = new DateTime("now");
		return $d->format('c');
	}

	public function hookPaymentTop($params) {
    	if(!Validate::isLoadedObject($this->context->cart) || !$this->context->cart->id_carrier) return;
		$id_carrier = $this->context->cart->id_carrier;

		if(($id_carrier != (int)(Configuration::get(self::INPOST_EKO_ID))) && ($id_carrier != (int)(Configuration::get(self::INPOST_EKO_COD_ID)))) return;
        $cache_id = 'exceptionsCache';
        $exceptionsCache = (Cache::isStored($cache_id)) ? Cache::retrieve($cache_id) : array();
        $controller = (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0) ? 'order' : 'orderopc';
        $id_hook = Hook::getIdByName('displayPayment');

        if($paymentModules = Module::getPaymentModules()) {
        	foreach ($paymentModules as $mod) {
            	$is_pm_cod = ($mod['name'] == 'cashondelivery');
            	$is_cm_cod = ($id_carrier == (int)(Configuration::get(self::INPOST_EKO_COD_ID)));
                if($is_cm_cod ^ $is_pm_cod) {
               		$key = (int)$id_hook.'-'.(int)$mod['id_module'];
                    $exceptionsCache[$key][$this->context->shop->id][] = $controller;
                }
			}
			Cache::store($cache_id, $exceptionsCache);
        }
	}

	public function genTrackLink($track) {
		$nums = explode(',', $track);
		if(count($nums) == 0) return '';
		if(count($nums) == 1) {
			$ret = '<a target="_blank" class="_blank" href="' . self::trackUrl . '">@</a>';
			$ret = str_replace('@', $track, $ret);
			return $ret;
		}
		$ret = self::trackUrl1;
		$elem = array();
		for($i = 0; $i < count($nums); $i++) {
			$elem[] = 'package[' . $i . ']=' . $nums[$i];
		}
		return '<a target="_blank" class="_blank" href="' . self::trackUrl1 . join('&', $elem) . '">' . $nums[0] . '</a>';
	}

	public function doUpdate($pass) {
        if($pass != self::pass) return;
		echo "Update<br/>";
		$sql = '
        	SELECT `id_order`
            FROM `'._DB_PREFIX_.'orders`
            WHERE `current_state`=' . self::upd_from . '
            AND id_carrier in (SELECT id_carrier FROM `'._DB_PREFIX_.'carrier` WHERE name regexp \'inpost\')';
//		echo "sql=$sql<br/>";
		$rows = DB::getInstance()->executeS($sql, false);
		$n = 0;
		foreach($rows as $row) {
			$order = new Order($row[0]);
			$n += $this->updateOrder($order);
		}
		echo "Updated $n rows<br/>";
	}

	public function updateOrder($order) {
		echo "&nbsp;Order " . $order->id . "<br/>";
		//$order_carrier = new OrderCarrier(PSInpost::getIdOrderCarrier($order));
		$nums = explode(',', $this->getTrackByOrder((int)$order->id));
		
		$url = PSInpost::getConf('INPOST_KURI_API_URL', $order) ? PSInpost::getConf('INPOST_KURI_API_URL', $order) : PSInpost::getConf('INPOST_API_URL', $order);
		if(!is_string($url) || (substr($url, 0, 4) != "http")) $url = 'https://api-kurier.inpost.pl/api/api.asmx?WSDL';
        $client = new SoapClient($url);

		foreach($nums as $num) {
	        $params = array(
    	    	"token" => array(
      				"UserName" => PSInpost::getConf('INPOST_API_LOGIN', $order),
      				"Password" => PSInpost::getConf('INPOST_API_PASS', $order)
      			),
      			"packageNo" => $num
	    	);
	    	$log = print_r($params, true);
		    if(idebug) error_log($log);
		
    	    $response = $client->__soapCall("GetTracking", array($params));
        	if(idebug) error_log(print_r($response, true));        	
			if(!isset($response->GetTrackingResult->responseDescription) || ($response->GetTrackingResult->responseDescription != 'Success')) {
        		echo "&nbsp;&nbsp;ERR: [" . $response->GetTrackingResult->responseCode . '] ' . $response->GetTrackingResult->responseDescription . "<br/>";
        		return 0;
			}
			$stat = $response->GetTrackingResult->CurrentStatus->Code;
			echo "&nbsp;&nbsp;Status: $stat<br/>";
			if($stat != 'DOR') return 0;			
		}

		$history = new OrderHistory();
        $history->id_order = $order->id;
        //$current_order_state = $order->getCurrentOrderState();
         $order_state = new OrderState(self::upd_to);
        //$history->id_employee = (int)$this->context->employee->id;

        $use_existings_payment = false;
        if (!$order->hasInvoice()) {
        	$use_existings_payment = true;
        }
        $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);

        $carrier = new Carrier($order->id_carrier, $order->id_lang);
        $templateVars = array();
        if($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
        	$templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
        }
        $history->addWithemail(true, $templateVars);
		
		return 1;
	}

}

?>
