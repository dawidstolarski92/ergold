<?php
/**
 * LICENCE
 *
 * ALL RIGHTS RESERVED.
 * YOU ARE NOT ALLOWED TO COPY/EDIT/SHARE/WHATEVER.
 *
 * IN CASE OF ANY PROBLEM CONTACT AUTHOR.
 *
 *  @author    Tomasz Dacka (kontakt@tomaszdacka.pl)
 *  @copyright PrestaHelp.com
 *  @license   ALL RIGHTS RESERVED
 */

require_once dirname(__FILE__).'/../../paczkomatyinpost.php';
require_once dirname(__FILE__).'/../../classes/PaczkomatyInpostData.php';
require_once dirname(__FILE__).'/../../classes/PaczkomatyInpostModel.php';

class AdminPaczkomatyInpostController extends ModuleAdminController
{

	private $ps16;
	public $module = null;
	protected $lang_config = array();

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'paczkomatyinpost';
		$this->identifier = 'id_cart';
		$this->className = 'PaczkomatyInpostData';
		$this->_defaultOrderBy = 'id_order';
		$this->_orderBy = 'id_order';
		$this->_orderWay = 'DESC';
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$this->default_form_language = $lang->id;
		$this->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		
		$this->lang = false;
		$this->bootstrap = true;
		$this->explicitSelect = true;
		$this->list_no_link = true;
		$this->list_simple_header = false;
//		$this->addRowAction('edit');
//		$this->addRowAction('delete');
		parent::__construct();
		$this->bulk_actions = array(
			'checkStatuses' => array(
				'text' => $this->l('Sprawdź aktualne statusy'),
			),
			'payForPacks' => array(
				'text' => $this->l('Opłać'),
				'confirm' => $this->l('Zostaną opłacone tylko przygotowane przesyłki.')
			),
			'printLabels' => array(
				'text' => $this->l('Drukuj etykiety'),
				'confirm' => $this->l('Zostaną pobrane etykiety tylko dla paczek, które zostały opłacone.')
			),
			'callCarrier' => array(
				'text' => $this->l('Zamów kuriera'),
				'confirm' => $this->l('Zamówienie kuriera dla ilości paczek 5+ jest bezpłatne.\nOpłata zgodnie z obowiązującym cennikiem.')
			),
			'printDelivery' => array(
				'text' => $this->l('Drukuj potwierdzenie nadania'),
			),
			'printDispatch' => array(
				'text' => $this->l('Drukuj potwierdzenie odbioru paczek'),
				'confirm' => $this->l('Funkcja ta pobiera tylko jedno potwierdzenie. nDla wielu zwróci potwierdzenie przypisane do pierwszej z nich.')
			)
		);

		$this->_select = 'a.id_cart, o.id_order as id_order, a.dispatch_order_id,';
		$this->_select .= " IF(a.dispatch_order_id=NULL OR a.dispatch_order_id='', 0, 1) as dispatch";
		$this->_where = "AND a.status <> 'UNDEFINED'";
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_cart` = a.`id_cart`)';
		$statuses_list = PaczkomatyInpostPackStatus::getLabels();
		$packsizes = array(
			'A' => 'A (8x38x64 cm)',
			'B' => 'B (19x38x64 cm)',
			'C' => 'C (41x38x64 cm)',
		);
		$this->fields_list = array(
			'id_order' => array(
				'title' => $this->l('Numer zamówienia'),
				'align' => 'center',
				'width' => 25,
				'filter_key' => 'o!id_order'),
			'packcode' => array(
				'title' => $this->l('Numer paczki'),
				'align' => 'center'),
			'reference_number' => array(
				'title' => $this->l('Numer referencyjny'),
				'align' => 'center'),
			'paid' => array(
				'title' => $this->l('Opłacona'),
				'align' => 'center',
				'type' => 'bool',
				'callback' => 'getImage'),
			'self_send' => array(
				'title' => $this->l('Nadanie w paczkomacie'),
				'callback' => 'displaySelfSend',
				'align' => 'center',
				'type' => 'bool',
				'width' => 25),
			'dispatch' => array(
				'title' => $this->l('Zlecony odbiór przez kuriera?'),
				'callback' => 'displayDispatchOrder',
				'type' => 'bool',
				'align' => 'center'),
			'cod' => array(
				'title' => $this->l('Pobranie'),
				'callback' => 'displayCod',
				'type' => 'bool',
				'align' => 'center'),
			'status' => array(
				'title' => $this->l('Status'),
				'callback' => 'displayPackStatus',
				'align' => 'center',
				'type' => 'select',
				'filter_key' => 'a!status',
				'list' => $statuses_list,
			),
			'packtype' => array(
				'title' => $this->l('Rozmiar'),
				'width' => 35,
				'align' => 'center',
				'type' => 'select',
				'list' => $packsizes,
				'filter_key' => 'a!packtype'
			),
			'receiver_email' => array(
				'title' => $this->l('Odbiorca'),
				'callback' => 'displayReceiver',
				'width' => 'auto',
				'align' => 'center'),
			'receiver_mobile' => array(
				'title' => $this->l('Nr. komórkowy odbiorcy'),
				'width' => 'auto',
				'align' => 'center'),
			'receiver_machine' => array(
				'title' => $this->l('Paczkomat odbiorcy'),
				'callback' => 'displayReceiverMachine',
				'align' => 'center'),
			'sender_machine' => array(
				'title' => $this->l('Paczkomat nadawcy'),
				'callback' => 'displaySenderMachine',
				'align' => 'center'),
			'status_date' => array(
				'title' => $this->l('Data ostatniej zmiany statusu'),
				'width' => 150,
				'type' => 'datetime',
				'align' => 'center'),
		);

		$this->ps16 = Tools::version_compare(_PS_VERSION_,
				'1.6.0.0',
				'>=');
		
	}

	public function renderForm()
	{
		$message = 'Dodawanie przesyłek odbywa się w podglądzie danego zamówienia<br/>Kliknij <strong><a href="';
		$message .= $this->context->link->getAdminLink('AdminOrders').'">tutaj</a></strong> by przejść do zamówień';
		$this->informations[] = $message;
	}

	public function processBulkCheckStatuses()
	{
		$result = true;
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			foreach ($this->boxes as $id)
			{
				$data = new PaczkomatyInpostData((int)$id);
				if (empty($data->packcode))
					continue;
				else
				{
					$response = $this->module->checkPackStatus($data);
					if ($response)
						$this->confirmations[] = $response;
				}
			}
		}
		return $result;
	}

	public function processBulkPrintLabels()
	{
		$result = true;
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			$packcodes = array();
			foreach ($this->boxes as $id)
			{
				$data = new PaczkomatyInpostData((int)$id);
				if ($data->isEditable() || $data->isPrepared() || empty($data->packcode))
					continue;
				else
					$packcodes[] = $data->packcode;
			}

			$stickers = $this->module->api->getPackSticker($packcodes);
			if ($stickers != false)
				$this->module->downloadPdf('Paczkomaty Etykiety - '.date('H:i:s d-m-Y'),
					$stickers);
			else
				$this->module->catchErrors();
		}
		return $result;
	}

	public function processBulkPrintDispatch()
	{
		$result = true;
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			foreach ($this->boxes as $id)
			{
				$data = new PaczkomatyInpostData((int)$id);
				if (empty($data->dispatch_order_id) || empty($data->packcode))
					continue;
				else
					$this->module->api->getDispatchOrderPrintout($data->dispatch_order_id);
			}
		}
		$this->module->catchErrors();
		return $result;
	}

	public function processBulkPrintDelivery()
	{
		$result = true;
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			$packcodes = array();
			foreach ($this->boxes as $id)
			{
				$data = new PaczkomatyInpostData((int)$id);
				if (empty($data->packcode))
					continue;
				else
					$packcodes[] = $data->packcode;
			}
			$this->module->api->getConfirmPrintout($packcodes);
		}
		return $result;
	}

	public function processBulkPayForPacks()
	{
		$result = true;
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			foreach ($this->boxes as $id)
			{
				$data = new PaczkomatyInpostData((int)$id);
				if (!$data->isPrepared() || empty($data->packcode))
					continue;
				else
					$this->module->api->payForPack($data);
			}
		}
		$this->module->catchErrors();
		return $result;
	}

	public function processBulkCallCarrier()
	{
		$result = true;
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			$packs_data = array();
			$total = count($this->boxes);
			foreach ($this->boxes as $id)
			{
				$data = new PaczkomatyInpostData((int)$id);
				if (!$data->isCallableForCarrier() || empty($data->packcode))
					continue;
				else
					$packs_data[] = $data;
			}

			if ($total != count($packs_data))
			{
				$error = 'Nie do wszystkich paczek można zamówić kuriera.';
				$error .= 'Wybierz te opłacone, gotowe do nadania oraz te, których jeszcze nie zlecono do odbioru.';
				$this->module->api->errors[] = $error;
				$result = false;
			}
			else
				$this->module->api->createDispatchOrder($packs_data);
		}
		$this->module->catchErrors();
		return $result;
	}

	public function displayDispatchOrder($status,
		$data)
	{
		if ($data['self_send'])
			return 'n/d';
		if ($status)
			return $this->getImage().'<br/>#'.$data['dispatch_order_id'];

		return $this->getImage(false);
	}

	public function displayPackStatus($status)
	{
		return PaczkomatyInpostPackStatus::getLabel($status);
	}

	public function displayReceiver($email)
	{
		$customers = Customer::getCustomersByEmail($email);
		if (empty($customers))
			return $email;
		$customer = $customers[0];
		if (empty($customer['firstname']))
			return $email;
		return $customer['firstname'].' '.$customer['lastname'].' ('.$email.')';
	}

	public function displayCod($cod,
		$data)
	{
		self::doNothing($cod);
		$id = (int)$data['id_cart'];
		$pack = new PaczkomatyInpostData($id);
		if ($pack->cod == 1)
			return $this->getImage().'<br/>('.$pack->cod_value.' zł)';
		else
			return $this->getImage(false);
	}

	public function displayReceiverMachine($machine,
		$data)
	{
		$pack = new PaczkomatyInpostData((int)$data['id_cart']);
		$machine = '';
		if ($pack->cod == 1)
			$machine = $pack->receiver_machine_cod;
		else
			$machine = $pack->receiver_machine;
		$info = $this->module->api->getMachineInfo($machine);
		if ($info != false)
			return $info['label'];

		return $machine;
	}

	public function displaySenderMachine($machine,
		$data)
	{
		$pack = new PaczkomatyInpostData((int)$data['id_cart']);
		if ($pack->self_send)
		{
			$info = $this->module->api->getMachineInfo($machine);
			if ($info != false)
				return $info['label'];

			return $machine;
		}
		return '-';
	}

	public function displaySelfSend($self_send,
		$data)
	{
		self::doNothing($self_send);
		$id = (int)$data['id_cart'];
		$pack = new PaczkomatyInpostData(($id));
		return $this->getImage($pack->self_send);
	}

	public function displayOrderNumber($id_cart,
		$data)
	{
		$data = OrderCore::getOrderByCartId($id_cart);
		return $data['id_order'];
	}

	public function getImage($enabled = true)
	{
		if ($enabled)
			return '<img src="../img/admin/enabled.gif" alt="Tak" title="Tak"/>';
		return '<img src="../img/admin/disabled.gif" alt="Nie" title="Nie"/>';
	}

	/**
	 * Just do nothing.
	 */
	public static function doNothing($a)
	{
		return $a;
	}
}
