<?php

class AdminPsInpostController extends ModuleAdminController {
	public $asso_type = 'shop';
    protected $statuses_array = array();

	public function __construct() {
    	$this->bootstrap = true;
    	$this->table = "order";
    	$this->className = 'Order';
    	$this->lang = false;
    	$this->module = 'psinpost';
    	$this->addRowAction('view');
        $this->explicitSelect = true;
        $this->allow_export = false;
        $this->deleted = false;
    	$this->context = Context::getContext();

        $this->_select = '
                a.id_currency,
                a.id_order AS id_pdf,
                CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
                osl.`name` AS `osname`,
                os.`color`,
                IF((SELECT so.id_order FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
                country_lang.name as cname,
                IF(a.valid, 1, 0) badge_success';

        $this->_join = '
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
                INNER JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
                INNER JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
                INNER JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.(int)$this->context->language->id.')
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
                LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')
                LEFT JOIN `'._DB_PREFIX_.'carrier` d ON (d.`id_carrier` = a.`id_carrier`)
               	LEFT JOIN `'._DB_PREFIX_.'inpost_label` l ON (l.`id_order` = a.`id_order`)';
        $this->_where = ' AND d.name regexp \'inpost\'';
        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }
        
        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'reference' => array(
                'title' => $this->l('Reference')
            ),
            'new' => array(
                'title' => $this->l('New client'),
                'align' => 'text-center',
                'type' => 'bool',
                'tmpTableFilter' => true,
                'orderby' => false,
                'callback' => 'printNewCustomer'
            ),
            'customer' => array(
                'title' => $this->l('Customer'),
                'havingFilter' => true,
            ),
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, array(
                'company' => array(
                    'title' => $this->l('Company'),
                    'filter_key' => 'c!company'
                ),
            ));
        }
        
        $this->fields_list = array_merge($this->fields_list, array(
            'total_paid_tax_incl' => array(
                'title' => $this->l('Total'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true
            ),
            'payment' => array(
                'title' => $this->l('Payment')
            ),
            'osname' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname'
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'id_pdf' => array(
                'title' => $this->l('PDF'),
                'align' => 'text-center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true
            )
        ));
        
        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_ORDER;

        $this->bulk_actions = array(
            'updateOrderStatus' => array('text' => $this->l('Change Order Status'), 'icon' => 'icon-refresh'),
            'createLabels' => array('text' => 'Utwórz etykiety', 'icon' => 'icon-save'),
            'editLabels' => array('text' => 'Edytuj i utwórz etykiety', 'icon' => 'icon-save'),
            'printLabels' => array('text' => 'Drukuj etykiety', 'icon' => 'icon-print'),
            'printSlips' => array('text' => 'Drukuj listy przewozowe', 'icon' => 'icon-print')
        );
        
    	parent::__construct();
	}

	public function renderList() {
		$this->context->smarty->assign(array(
			'inpost_token' => sha1(_COOKIE_KEY_.'psinpost'),
            'inpost_ajax_uri' => _INPOST_AJAX_URI_,
            'inpost_pdf_uri' => _INPOST_PDF_URI_
		));
        if (Tools::isSubmit('submitBulkeditLabels'.$this->table)) {
            if (Tools::getIsset('cancel')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }

            $this->tpl_list_vars['editLabels_mode'] = true;
            $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $this->tpl_list_vars['POST'] = $_POST;
            $this->tpl_list_vars['ids'] = $_POST['orderBox'];
            $this->tpl_list_vars['errors'] = PSInpost::$errors;
            $refs = array();
            $val_kwoty = array();
            $val_adresy = array();
            $val_kody = array();
            $val_miasta = array();
            $val_uwagi = array();
            $val_ubez = array();
            foreach($_POST['orderBox'] as $id) {
				$order = new Order((int)$id);
				$address_r = new Address($order->id_address_delivery);
				$refs[$id] = $order->reference;
				$val_kwoty[$id] = PSInpost::getCOD($order);
				$val_adresy[$id] = $address_r->address1 . ($address_r->address2 != '' ? (' ' . $address_r->address2) : '');
				$val_kody[$id] = trim($address_r->postcode);
				$val_miasta[$id] = $address_r->city;				
				$val_uwagi[$id] = PSInpost::genUwagi($order);
				$val_ubez[$id] = Configuration::get('INPOST_UBEZ');
            }
            $this->tpl_list_vars['refs'] = $refs;
            $this->tpl_list_vars['val_kwoty'] = $val_kwoty;
            $this->tpl_list_vars['val_adresy'] = $val_adresy;
            $this->tpl_list_vars['val_kody'] = $val_kody;
            $this->tpl_list_vars['val_miasta'] = $val_miasta;
            $this->tpl_list_vars['val_uwagi'] = $val_uwagi;
            $this->tpl_list_vars['val_ubez'] = $val_ubez;
    	}
        if (Tools::isSubmit('submitBulkupdateOrderStatus'.$this->table)) {
            if (Tools::getIsset('cancel')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }

            $this->tpl_list_vars['updateOrderStatus_mode'] = true;
            $this->tpl_list_vars['order_statuses'] = $this->statuses_array;
            $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $this->tpl_list_vars['POST'] = $_POST;
    	}
        if (Tools::isSubmit('submitBulkprintLabels'.$this->table)) {
            if (Tools::getIsset('cancel')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
            $this->tpl_list_vars['printLabels_mode'] = true;
            $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $ids = array();
            foreach($_POST['orderBox'] as $id) {
            	$id_label = $this->getLabelByOrderId($id);
            	if($id_label != null) $ids[] = $id_label;
            }
            $this->tpl_list_vars['ids'] = join(',', $ids);
    	}
        if (Tools::isSubmit('submitBulkprintSlips'.$this->table)) {
            if (Tools::getIsset('cancel')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
            $this->tpl_list_vars['printSlips_mode'] = true;
            $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $ids = array();
            foreach($_POST['orderBox'] as $id) {
	        	$order = new Order($id);
        		if (!Validate::isLoadedObject($order)) {
            		continue;
        		}
            	if($order->delivery_number) $ids[] = $id;
            }
            $this->tpl_list_vars['ids'] = join(',', $ids);
    	}
        if (Tools::isSubmit('submitBulkcreateLabels'.$this->table)) {
            if (Tools::getIsset('cancel')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
            $this->tpl_list_vars['createLabels_mode'] = true;
            $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $this->tpl_list_vars['errors'] = PSInpost::$errors;
    	}
        return parent::renderList();
    }
	
	public function printPDFIcons($id_order, $tr) {
        static $valid_order_state = array();

        $order = new Order($id_order);
        if (!Validate::isLoadedObject($order)) {
            return '';
        }

        if (!isset($valid_order_state[$order->current_state])) {
            $valid_order_state[$order->current_state] = Validate::isLoadedObject($order->getCurrentOrderState());
        }

        if (!$valid_order_state[$order->current_state]) {
            return '';
        }

        $tpl = $this->context->smarty->createTemplate(__DIR__ . '/../../views/templates/admin/_print_pdf_icon.tpl');

        $tpl->assign(array(
            'order' => $order,
   			'label' => $this->getLabelByOrder($order),
            'tr' => $tr,
            'link' => Context::getContext()->link,
        ));

        
		return $tpl->fetch();
    }
	
	public function printNewCustomer($id_order, $tr) {
        return ($tr['new'] ? $this->l('Yes') : $this->l('No'));
    }
	
	public static function setOrderCurrency($echo, $tr) {
        $order = new Order($tr['id_order']);
        return Tools::displayPrice($echo, (int)$order->id_currency);
    }
	
	public function initToolbar() {
        $res = parent::initToolbar();
        unset($this->toolbar_btn['new']);
        return $res;
    }

 	public function processBulkUpdateOrderStatus() {
		AdminOrdersController::processBulkUpdateOrderStatus();
 	}

 	public function processBulkEditLabels() {
 		$module_instance = Module::getInstanceByName('psinpost');
		if(Tools::isSubmit('submitEditLabels')) {
			$errare = array();
			foreach (Tools::getValue('orderBox') as $id) {
	 			if(!$module_instance->createLabel($id, $_POST['inpost_form_kwota_' . $id], $_POST['inpost_form_adres_' . $id], $_POST['inpost_form_kod_' . $id], $_POST['inpost_form_miasto_' . $id], $_POST['inpost_form_uwagi_' . $id], $_POST['inpost_form_ubezp_' . $id])) $errare[] = $id;
			}
			if (!count(PSInpost::$errors)) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
            $_POST['orderBox'] = $errare;
		}
 	}

 	public function processBulkCreateLabels() {
 		$module_instance = Module::getInstanceByName('psinpost');
 		foreach($_POST['orderBox'] as $id) {
 			$module_instance->createLabel($id);
 		}
 	}

 	public function processBulkPrintLabels() {
 	}

 	public function processBulkPrintSlips() {
 	}

    public function getLabelByOrder($order) {
    	return Db::getInstance()->getValue('
        	SELECT `id_label`
            FROM `'._DB_PREFIX_.'inpost_label`
            WHERE `id_order`='.(int)$order->id
            );
    }
	
    public function getLabelByOrderId($id_order) {
    	return Db::getInstance()->getValue('
        	SELECT `id_label`
            FROM `'._DB_PREFIX_.'inpost_label`
            WHERE `id_order`='.(int)$id_order
            );
    }

	public function renderView() {
		$id_order = Tools::getValue('id_order');
		Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrders').'&vieworder&id_order='.(int)$id_order);
	}
}


?>
