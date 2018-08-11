<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class AllegroShipping extends ObjectModel
{
	public $id_allegro_shipping;
	public $name;
	public $default;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'allegro_shipping',
		'primary' => 'id_allegro_shipping',
		'fields' => array(
			'name'	                => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'default' 			    => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
		),
	);

    public function add($auto_date = true, $null_values = false)
    {
        if ($this->default) {
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.self::$definition['table'].'` SET `default` = 0');
        }

        return parent::add($null_values);
    }

    public function update($null_values = false)
    {
        if ($this->default) {
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.self::$definition['table'].'` SET `default` = 0');
        }

        return parent::update($null_values);
    }

    public function delete()
    {
        if ($this->default) {
            $res = parent::delete();
            self::setDefault();
        } else {
            $res = parent::delete();
        }

        return $res;
    }

    public static function setDefault($id = null)
    {
        return Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_ .self::$definition['table'].'` SET `default` = 1'.($id ? ' WHERE `'.self::$definition['primary'].'` = '.(int)$id : '').' ORDER BY `'.self::$definition['primary'].'` DESC LIMIT 1');
    }

    public static function getDefault($returnObj = false)
    {
        $data = Db::getInstance()->GetRow('SELECT * FROM `'._DB_PREFIX_ .self::$definition['table'].'` WHERE `default` = 1');

        if (!empty($data) && $returnObj) {
            $data = new AllegroShipping((int)$data[self::$definition['primary']]);
        }

        return $data;
    }

    public static function get($onlyDefault = false)
    {
        return Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_ .'allegro_shipping` '.($onlyDefault ? 'WHERE `default` = 1' : ''));
    }
}
