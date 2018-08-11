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

require_once dirname(__FILE__).'/PaczkomatyInpostApi.php';

class PaczkomatyInpostModel
{

	private $api;
	private $module;
	private $email;
	private $password;
	public $errors;

	const PACK_A_DIMENSIONS = '8x38x64 cm';
	const PACK_B_DIMENSIONS = '19x38x64 cm';
	const PACK_C_DIMENSIONS = '41x38x64 cm';
	const CACHE_LIST_MACHINES = 'list_machines';
    const CACHE_LIST_MACHINES_COD = 'list_machines_cod';

	public function __construct(PaczkomatyInpost $module,
		$email = null,
		$password = null)
	{
		$this->api = new PaczkomatyInpostApi();
		$this->email = $email;
		$this->password = $password;
		$this->module = $module;
	}

	public function deletePack($packcode)
	{
		try
		{
			$this->api->cancelPack($this->email,
				$this->password,
				$packcode);
			return true;
		} catch (Exception $ex)
		{
			$this->errors[] = $ex->getMessage();
			return false;
		}
	}

	public function changePacktype($packcode,
		$type)
	{
		try
		{
			$this->api->changePackSize($this->email,
				$this->password,
				$packcode,
				$type);
			return true;
		} catch (Exception $ex)
		{
			$this->errors[] = $ex->getMessage();
			return false;
		}
	}

	public function changeReference($packcode,
		$reference)
	{
		try
		{
			$this->api->setCustomerRef($this->email,
				$this->password,
				$packcode,
				$reference);
			return true;
		} catch (Exception $ex)
		{
			$this->errors[] = $ex->getMessage();
			return false;
		}
	}

	public function getPackStatus($packcode)
	{
		try
		{
			return $this->api->getPackStatus($packcode);
		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			return false;
		}
	}

	public function getInsurances()
	{
        return array(
           array(
				'name' => 'Brak',
				'limit' => '0',
			),
            array(
				'name' => 'Do 5000 zł',
				'limit' => '5000',
			),
            array(
				'name' => 'Do 10000 zł',
				'limit' => '10000',
			),
            array(
				'name' => 'Do 20000 zł',
				'limit' => '20000',
			),
        );
//		try
//		{
//			$response = $this->api->priceList($this->email,
//				$this->password);
//			$insurances = array();
//			$insurances[] = array(
//				'name' => 'Brak',
//				'limit' => '0',
//				'price' => '0'
//			);
//            if(isset($response->insurance) && !empty($response->insurance)){
//                foreach ($response->insurance as $insurance)
//                {
//                    $insurance_new = (array)$insurance;
//                    $insurance_new['name'] = 'Do '.$insurance_new['limit'].' zł (Koszt: '.$insurance_new['price'].' zł)';
//                    $insurances[] = $insurance_new;
//                }
//            }
//			return $insurances;
//		} catch (Exception $e)
//		{
//			$this->errors[] = $e->getMessage();
//			return false;
//		}
	}

	public function getPriceListForCod()
	{
        return array();
//		try
//		{
//			$response = $this->api->priceList($this->email,
//				$this->password);
//			$price_list = array();
//			$price_list['on_delivery_payment'] = (float)$response->on_delivery_payment;
//			$price_list['on_delivery_percentage'] = (float)$response->on_delivery_percentage;
//			$price_list['on_delivery_limit'] = (float)$response->on_delivery_limit;
//			return $price_list;
//		} catch (Exception $e)
//		{
//			$this->errors[] = $e->getMessage();
//			return false;
//		}
	}

	public function getPackTypes()
	{
        return array(
            array('type' => 'A', 'name'=> 'A '. self::PACK_A_DIMENSIONS),
            array('type' => 'B', 'name'=> 'B '. self::PACK_B_DIMENSIONS),
            array('type' => 'C', 'name'=> 'C '. self::PACK_C_DIMENSIONS),
        );
//		try
//		{
//			$response = $this->api->priceList($this->email,
//				$this->password);
//			$packtypes = array();
//            if(isset($response->packtype) && !empty($response->packtype)){
//                foreach ($response->packtype as $packtype)
//                {
//                    $packtype_new = (array)$packtype;
//                    $type = $packtype_new['type'];
//                    $dim = null;
//                    switch ($type)
//                    {
//                        case 'A': $dim = self::PACK_A_DIMENSIONS;
//                            break;
//                        case 'B': $dim = self::PACK_B_DIMENSIONS;
//                            break;
//                        case 'C': $dim = self::PACK_C_DIMENSIONS;
//                            break;
//                    }
//                    $packtype_new['name'] = $type.' '.$dim.' (Koszt: '.$packtype_new['price'].' zł)';
//                    $packtypes[] = $packtype_new;
//                }
//            }
//			return $packtypes;
//		} catch (Exception $e)
//		{
//			$this->errors[] = $e->getMessage();
//			return false;
//		}
	}

	public function getListMachines()
	{
		try
		{
			$list_machines = $this->cacheGet(self::CACHE_LIST_MACHINES);
			$uns = unserialize($list_machines);
			if (!$list_machines || empty($uns))
			{
				$list = $this->api->listMachines();
				$machines = array();
				foreach ($list->machine as $machine)
				{
					$machine_new = array();
					$machine_new['name'] = (string)$machine->name;
					$machine_new['label'] = $machine->name.' '.$machine->postcode.' '.$machine->town;
					$machine_new['label'] .= ', '.$machine->street.' '.$machine->buildingnumber;
					$machines[] = $machine_new;
				}
				$this->cacheUpdate(self::CACHE_LIST_MACHINES,
					serialize($machines));
				return $machines;
			}
			else
				return $uns;
		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			return false;
		}
	}

    public function getListMachinesCod()
	{
		try
		{
			$list_machines = $this->cacheGet(self::CACHE_LIST_MACHINES_COD);
			$uns = unserialize($list_machines);
			if (!$list_machines || empty($uns))
			{
				$list = $this->api->listMachines('t');

				$machines = array();
                if(isset($list->machine) && !empty($list->machine)){
                    foreach ($list->machine as $machine)
                    {
                        $machine_new = array();
                        $machine_new['name'] = (string)$machine->name;
                        $machine_new['label'] = $machine->name.' '.$machine->postcode.' '.$machine->town;
                        $machine_new['label'] .= ', '.$machine->street.' '.$machine->buildingnumber;
                        $machines[] = $machine_new;
                    }
                }
				$this->cacheUpdate(self::CACHE_LIST_MACHINES_COD,
					serialize($machines));
				return $machines;
			}
			else
				return $uns;
		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			return false;
		}
	}

    public function getNearestListMachines($post_code, $only_cod = false)
	{
		try
		{
            $list = $this->api->findNearestMachines($post_code, 5,  $only_cod?'t':'');


            $machines = array();
            if(isset($list->machine) && !empty($list->machine)){
                foreach ($list->machine as $machine)
                {
                    $machine_new = array();
                    $machine_new['name'] = (string)$machine->name;
                    $machine_new['label'] = $machine->name.' '.$machine->postcode.' '.$machine->town;
                    $machine_new['label'] .= ', '.$machine->street.' '.$machine->buildingnumber;
                    $machines[] = $machine_new;
                }
            }
            return $machines;

		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			return false;
		}
	}

	public function getMachineInfo($name)
	{
		try
		{
			$machine = $this->api->findMachineByName($name)->machine;
			$machine_new = array();
			$machine_new['name'] = (string)$machine->name;
			$machine_new['address'] = $machine->postcode.' '.$machine->town.', '.$machine->street.' '.$machine->buildingnumber;
			$machine_new['label'] = $machine->name.' '.$machine_new['address'];

			return $machine_new;
		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			return false;
		}
	}

	public function payForPack(PaczkomatyInpostData $pack_data)
	{
		try
		{
			$response = $this->api->payForPack($this->email,
				$this->password,
				$pack_data->packcode);
			if ($response == '1')
			{
                $pack_data->status = PaczkomatyInpostPackStatus::PREPARED;
                $pack_data->paid = true;
                $pack_data->update();
                $pack_data->setAsPaid();
				return true;
			}
			return false;
		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			return false;
		}
	}

	public function getPackSticker($packcode)
	{
		try
		{
			$print_type = Configuration::get(PaczkomatyInpost::KEY_PRINT_TYPE);
			$print_format = Configuration::get(PaczkomatyInpost::KEY_PRINT_FORMAT);
			return $this->api->getSticker($this->email,
					$this->password,
					$packcode,
					$print_type ? $print_type : null,
					$print_format ? $print_format : null);
		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			return false;
		}
	}

	public function preparePack(PaczkomatyInpostData $pack_data,
		$auto_labels = false)
	{
		/* Można przygotowac tylko niezdefiniowane paczki */
		if (!Validate::isLoadedObject($pack_data) || $pack_data->status != PaczkomatyInpostPackStatus::UNDEFINED)
			return false;


		$packs_info = array();
		$packs_info[] = $this->createPackInfo(
			$pack_data->id,
			$pack_data->receiver_email,
			Configuration::get(PaczkomatyInpost::KEY_EMAIL),
			$pack_data->receiver_mobile,
			$pack_data->cod == 1 ? $pack_data->receiver_machine_cod : $pack_data->receiver_machine,
			null,
			$pack_data->self_send == PaczkomatyInpost::SEND_TYPE_PACZKOMAT ? $pack_data->sender_machine : null,
			$pack_data->packtype,
			$pack_data->insurance,
			$pack_data->cod == 1 ? $pack_data->cod_value : null,
			$pack_data->reference_number,
			null);

		$pack_info = $this->createDeliveryPack($auto_labels ? 1 : 0,
			$pack_data->self_send,
			$packs_info);

		if ($pack_info !== false)
		{
			if (!empty($pack_info))
				foreach ($pack_info as $pack)
				{
					if (isset($pack->error))
					{
						$this->errors[] = (string)$pack->error;
						return false;
					}
					$pack_data->paid = $auto_labels;
					$pack_data->status = $auto_labels ? PaczkomatyInpostPackStatus::PREPARED : PaczkomatyInpostPackStatus::CREATED;
					$pack_data->status_date = date('Y-m-d H:i:s');
					$pack_data->packcode = (string)$pack->packcode;
					$pack_data->calculated_charge = (float)$pack->calculatedcharge;
					$pack_data->customer_delivering_code = (string)$pack->customerdeliveringcode;
					if ($pack_data->update())
						return $this->module->displayConfirmation('Pomyślnie przygotowano przesyłkę');
					else
					{
						$message = 'Pomyślnie przygotowano przesyłkę, lecz wystąpił błąd z bazą danych sklepu i dane o przesyłce zostały utracone.';
						$message .= 'Zaloguj się do managera przesyłek, usuń wygenerowaną paczkę i spróbuj ponownie';
						return $this->module->displayConfirmation($message);
					}
				}
			else
				return false;
		}
		return false;
	}

	private function createPackInfo($id,
		$addressee_email,
		$sender_email,
		$phone_num,
		$box_machine_name,
		$alternative_box_machine_name,
		$sender_box_machine_name,
		$pack_type,
		$insurance_amount,
		$on_delivery_amount,
		$customer_ref,
		$sender_address)
	{
		$data = array(
			'id' => $id,
			'addresseeEmail' => $addressee_email,
			'senderEmail' => $sender_email,
			'phoneNum' => $phone_num,
			'boxMachineName' => $box_machine_name,
			'alternativeBoxMachineName' => $alternative_box_machine_name,
            'senderBoxMachineName' => is_null($sender_box_machine_name) ? null : $sender_box_machine_name,
			'packType' => $pack_type,
			'insuranceAmount' => $insurance_amount,
			'onDeliveryAmount' => $on_delivery_amount,
			'customerRef' => $customer_ref,
			'senderAddress' => $sender_address
		);
        return $data;
	}

	private function createDeliveryPack($auto_labels,
		$self_send,
		$packs_info)
	{
		$data = array(
			'autoLabels' => $auto_labels,
			'selfSend' => $self_send,
			'packsInfo' => $packs_info
		);
		unset($data['packsInfo']);
		$xml = new SimpleXMLElement('<paczkomaty/>');

		foreach ($data as $key => $v)
		{
			if (!is_null($v))
				$xml->addChild($key,
					$v);
		}

		if (is_array($packs_info) && !empty($packs_info))
		{
			foreach ($packs_info as $pack)
			{
				$current = $xml->addChild('pack');
				foreach ($pack as $key => $v)
				{
					if (!is_null($v))
						$current->addChild($key,
							$v);
				}
			}
		}
		try
		{
            $dom = dom_import_simplexml($xml);
            $xml = $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
			return $this->api->createDeliveryPacks($this->email,
					$this->password,
					$xml);
		} catch (Exception $ex)
		{
			$this->errors[] = $ex->getMessage();
			return false;
		}
	}

	public function getDispatchPoints()
	{
		$xml = new SimpleXMLElement('<paczkomaty/>');
		$xml->addChild('dispatchPointStatus',
			'ACTIVE');
		try
		{
			$response = $this->api->getDispatchPoints($this->email,
				$this->password,
				$xml->asXML());
			$total = $response->count;
			if ($total == 0)
			{
				$this->errors[] = 'Nie zdefiniowano punktów odbioru paczek dla kuriera. Zaloguj się do managera paczek i ustaw odpowiednie.';
				return false;
			}
			$results = array();
			foreach ($response->result as $point)
			{
				$results[] = array(
					'name' => (string)$point->name,
					'dispatchPointStatus' => (string)$point->dispatchPointStatus,
					'postCode' => (string)$point->postCode,
					'street' => (string)$point->street,
					'town' => (string)$point->town,
					'building' => (string)$point->building,
					'flat' => (string)$point->flat,
					'phoneNumber' => (string)$point->phoneNumber,
					'email' => (string)$point->email,
					'comments' => (string)$point->comments,
					'availabilityHours' => (string)$point->availabilityHours,
					'customerEmail' => (string)$point->customerEmail,
					'label' => (string)$point->name
				);
			}
			uasort($results,
				array(
				__CLASS__,
				'sortDispatchPoints'));
			return $results;
		} catch (Exception $ex)
		{
			$this->errors[] = $ex->getMessage();
			return false;
		}
	}

	public function createDispatchOrder(array $packs_data)
	{
		$dispatch_point = Configuration::get(PaczkomatyInpost::KEY_DEFAULT_DISPATCH_POINT);
		if (empty($dispatch_point))
		{
			$this->errors[] = 'Aby zamówić kuriera musisz wybrać domyślny punkt odbioru dla kuriera';
			return false;
		}
		$xml = new SimpleXMLElement('<paczkomaty/>');

		$xml->addChild('dispatchPointName',
			$dispatch_point);
		foreach ($packs_data as $pack)
			$xml->addChild('parcelCodes',
				$pack->packcode);

		try
		{
			$response = $this->api->createDispatchOrder($this->email,
				$this->password,
				$xml->asXML());

			$id = (string)$response->dispatchOrderId;
			foreach ($packs_data as $data)
			{
				$data->dispatch_order_id = $id;
				$data->update();
			}
			$this->getDispatchOrderPrintout($id);
			return true;
		} catch (Exception $ex)
		{
			$this->errors[] = $ex->getMessage();
			return false;
		}
	}

	public function getDispatchOrderPrintout($dispatch_order_id)
	{
		$xml = new SimpleXMLElement('<paczkomaty/>');
		$xml->addChild('dispatchOrderId',
			$dispatch_order_id);
		try
		{
			$pdf = $this->api->getDispatchOrderPrintout($this->email,
				$this->password,
				$xml->asXML());
			$this->module->downloadPdf('Potwierdzenie Odbioru #'.$dispatch_order_id,
				$pdf);
		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			return false;
		}
	}

	public function getConfirmPrintout(array $packs)
	{
		$xml = new SimpleXMLElement('<paczkomaty/>');
		foreach ($packs as $packcode)
		{
			$pack = $xml->addChild('pack');
			$pack->addChild('packcode',
				$packcode);
		}
		try
		{
			$pdf = $this->api->getConfirmPrintout($this->email,
				$this->password,
				$xml->asXML());
			$this->module->downloadPdf('Paczkomaty Potwierdzenie nadania - '.date('H:i:s d-m-Y'),
				$pdf);
		} catch (Exception $e)
		{
			$this->errors[] = $e->getMessage();
			return false;
		}
	}

	public static function sortDispatchPoints($a,
		$b)
	{
		return strcasecmp($a['name'],
			$b['name']);
	}

	private function cacheIsValid($type)
	{
		$file_date = dirname(__FILE__).'/../cache/'.$type.'_date.cache';
		if (!file_exists($file_date))
			return false;
		$last_update = date('Y-m-d',
			Tools::file_get_contents($file_date));
		$now = date('Y-m-d',
			time());
		if ($now != $last_update)
			return false;
		return true;
	}

	private function cacheUpdate($type,
		$content)
	{
		$file = dirname(__FILE__).'/../cache/'.$type.'.cache';
		$file_date = dirname(__FILE__).'/../cache/'.$type.'_date.cache';

		file_put_contents($file_date,
			time());
		file_put_contents($file,
			$content);
	}

	private function cacheGet($type)
	{
		if (!$this->cacheIsValid($type))
			return false;
		else
			return Tools::file_get_contents(dirname(__FILE__).'/../cache/'.$type.'.cache');
	}
}
