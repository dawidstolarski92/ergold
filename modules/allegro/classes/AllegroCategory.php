<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class AllegroCategory extends ObjectModel
{
	public $id_allegro_category;
	public $id_category;
	public $id_parent;
	public $name;
	public $position;
    public $is_leaf;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'allegro_category',
		'primary' => 'id_allegro_category',
		'fields' => array(
			'id_allegro_category'	=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'id_category' 			=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'id_parent' 			=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'name' 					=> array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
			'position' 				=> array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'is_leaf'               => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
		),
	);

    public static function getCategories($idParent = 0)
    {
        return Db::getInstance()->ExecuteS('
            SELECT * FROM `' . _DB_PREFIX_ . 'allegro_category`
            WHERE `id_parent` = ' . (int)$idParent . '
            ORDER BY `position`');
    }

    public static function getCategoryPath($idCategory)
    {
        $path = array();

        do {
            $category = Db::getInstance()->getRow('
                SELECT `id_parent`, `name`
                FROM `' . _DB_PREFIX_ . 'allegro_category`
                WHERE `id_category` = '.(int)$idCategory
            );

            if($category) {
                $idCategory = $category['id_parent'];
                $path[] = $category['name'];
            }

        } while ($category);

        return array_reverse($path);
    }
}
