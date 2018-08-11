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

/**
 * PaczkomatyInpostData
 */
class PaczkomatyInpostData extends ObjectModel
{

    public $status;
    public $dispatch_order_id;
    public $status_date;
    public $packcode;
    public $paid;
    public $calculated_charge;
    public $customer_delivering_code;
    public $receiver_email;
    public $receiver_mobile;
    public $receiver_machine;
    public $receiver_machine_cod;
    public $packtype;
    public $self_send;
    public $sender_machine;
    public $reference_number;
    public $insurance;
    public $cod;
    public $cod_value;
    public $date_add;
    public $date_upd;
    public static $definition = array(
        'table' => 'paczkomatyinpost',
        'primary' => 'id_cart',
        'fields' => array(
            'status' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false),
            'dispatch_order_id' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false),
            'status_date' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'required' => false),
            'packcode' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false),
            'paid' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => false),
            'calculated_charge' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false),
            'customer_delivering_code' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false),
            'receiver_email' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'required' => false),
            'receiver_mobile' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isPhoneNumber',
                'required' => false),
            'receiver_machine' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false),
            'receiver_machine_cod' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false),
            'packtype' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false),
            'self_send' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => false),
            'sender_machine' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false),
            'reference_number' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => false),
            'insurance' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false),
            'cod' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => false),
            'cod_value' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate')
        )
    );

    public static function createNew($id)
    {
        if (!Validate::isLoadedObject(new self($id)))
            Db::getInstance()->insert(self::$definition['table'], array(
                'id_cart' => (int)$id,
                'cod' => -1,
                'status' => pSQL(PaczkomatyInpostPackStatus::UNDEFINED),
                'date_add' => pSQL(date('Y-m-d H:i:s')),
                'date_upd' => pSQL(date('Y-m-d H:i:s'))
            ));
        return new self($id);
    }

    public function deletePack()
    {
        $this->status = PaczkomatyInpostPackStatus::UNDEFINED;
        $this->status_date = null;
        $this->packcode = null;
        $this->calculated_charge = null;
        $this->customer_delivering_code = null;
        if ($this->update())
            return true;

        return false;
    }

    public function isEditable()
    {
        if ($this->status == PaczkomatyInpostPackStatus::UNDEFINED)
            return true;
        return false;
    }

    public function isPrepared()
    {
        if ($this->status == PaczkomatyInpostPackStatus::CREATED)
            return true;
        return false;
    }

    public function isCallableForCarrier()
    {
        if ($this->status == PaczkomatyInpostPackStatus::PREPARED && $this->paid && !$this->self_send && empty($this->dispatch_order_id))
            return true;
        return false;
    }

    public function isPreparable()
    {
        return $this->isEditable();
    }

    public function canBeDeleted()
    {
        if ($this->isPrepared() || $this->status == PaczkomatyInpostPackStatus::EXPIRED || $this->status == PaczkomatyInpostPackStatus::LABELEXPIRED)
            return true;
        return false;
    }

    public function setAsPrepared()
    {
        if (Configuration::get(PaczkomatyInpost::KEY_CUSTOMER_NOTIFY) == PaczkomatyInpost::OPTION_CUSTOMER_NOTIFY_PREPARE) {
            PHPITools::sendCustomerNotify($this->id, $this->packcode, $this->getLinkToStatus());
        }

        if (Configuration::get(PaczkomatyInpost::KEY_STATUS_PREPARE_UPDATE)) {
            PHPITools::changeOrderState($this->id, (int)Configuration::get(PaczkomatyInpost::KEY_STATUS_PREPARE), $this->getLinkToStatus());
        }
    }

    public function setAsPaid()
    {
        if (Configuration::get(PaczkomatyInpost::KEY_CUSTOMER_NOTIFY) == PaczkomatyInpost::OPTION_CUSTOMER_NOTIFY_PAID) {
            PHPITools::sendCustomerNotify($this->id, $this->packcode, $this->getLinkToStatus());
        }

        if (Configuration::get(PaczkomatyInpost::KEY_STATUS_PAID_UPDATE)) {
            PHPITools::changeOrderState($this->id, (int)Configuration::get(PaczkomatyInpost::KEY_STATUS_PAID), $this->getLinkToStatus());
        }
    }

    public function getLinkToStatus()
    {
        return 'https://paczkomaty.pl/pl/znajdz-paczke?parcel='.$this->packcode;
    }
}
