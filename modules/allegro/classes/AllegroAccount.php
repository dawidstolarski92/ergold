<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class AllegroAccount extends ObjectModel
{
    public $id_allegro_account;
    public $name;
    public $login;
    public $sandbox;
    public $id_currency;
    public $id_language;
    public $id_employee = 0;
    public $access_token;
    public $refresh_token;
    public $token_lifetime;
    public $token_date_refresh = '2016-01-01 01:01:01';
    public $active = 1;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'allegro_account',
        'primary' => 'id_allegro_account',
        'multilang_shop' => true,
        'fields' => array(
            'name'  =>              array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
            'login' =>              array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 32),
            'sandbox' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_currency' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'id_language' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'id_employee' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'access_token' =>       array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'refresh_token' =>      array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'token_lifetime' =>     array('type' => self::TYPE_STRING, 'validate' => 'isDateFormat'),
            'token_date_refresh' => array('type' => self::TYPE_STRING, 'validate' => 'isDateFormat'),
            'active' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );


    /**
     * Get seleced or firts available allegro account object
     *
     * @param  intiger  $id_allegro_account     ID allegro account
     * @param  intiger  $id_employee            ID employee
     * @return mixed AllegroAccount or bool
     */
    public static function getOne($id_allegro_account, $id_employee)
    {
        $allegroAccount = new AllegroAccount((int)$id_allegro_account);

        if ($allegroAccount->id_allegro_account &&
            $allegroAccount->active &&
            (!$allegroAccount->id_employee  || $allegroAccount->id_employee == $id_employee)) {
            return $allegroAccount;
        } else {
            $result = Db::getInstance()->getValue(
                'SELECT `id_allegro_account`
                FROM `'._DB_PREFIX_.'allegro_account`
                WHERE `active` = 1
                AND `id_employee` IN (0, '.(int)$id_employee.')'
            );

            if ($result) {
                return new AllegroAccount((int)$result);
            }
        }

        return false;
    }


    /**
     * Return allegro accounts
     * @param  integer $id_employee ID of employye
     * @param  integer $active      Get only active accounts
     * @return array
     */
    static public function getAccounts($id_employee = null, $active = true)
    {
        // ID employee "0" = available for all
        return Db::getInstance()->ExecuteS('
        	SELECT * FROM `'._DB_PREFIX_.'allegro_account`
        	WHERE active = '.(int)$active
        );
    }

    public function updateToken($accessToken, $refreshToken)
    {
        // @todo exception
        if (!$accessToken || !$refreshToken) {
            return false;
        }

        $this->access_token = $accessToken;
        $this->refresh_token = $refreshToken;
        $this->token_date_refresh = date("Y-m-d H:i:s");

        return $this->update();
    }
}
