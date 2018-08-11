<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class AllegroTheme extends ObjectModel
{
    public $id_allegro_theme;
    public $name;
    public $content;
    public $smarty = 0;
    public $format = 1;
    public $default;
    public $active = 1;

    const IMAGE_EXT = 'png';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'allegro_theme',
        'primary' => 'id_allegro_theme',
        'multilang_shop' => false,
        'fields' => array(
            'name'      => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
            'content'   => array('type' => self::TYPE_HTML, 'validate' => 'isAnything', 'required' => true),
            'smarty'    => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'format'    => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'default'   => array( 'type' => self::TYPE_INT, 'validate' => 'isInt'),
            'active'    => array( 'type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        )
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
        return Db::getInstance()->Execute(
            'UPDATE `'._DB_PREFIX_ .self::$definition['table'].'` 
            SET `default` = 1'.($id ? ' WHERE `'.self::$definition['primary'].'` = '.(int)$id : '').' 
            ORDER BY `'.self::$definition['primary'].'` DESC LIMIT 1'
        );
    }


    public static function getDefault($returnObj = false)
    {
        $data = Db::getInstance()->GetRow('SELECT * FROM `'._DB_PREFIX_ .self::$definition['table'].'` WHERE `default` = 1');

        if (!empty($data) && $returnObj) {
            $data = new AllegroTheme((int)$data['id_allegro_theme']);
        }

        return $data;
    }

    static public function getThemes($onlyDefault = false, $onlyActive = true)
    {
        return Db::getInstance()->ExecuteS('
            SELECT * FROM `'._DB_PREFIX_.'allegro_theme` WHERE 1'.($onlyDefault ? ' AND `default` = 1' : '').($onlyActive ? ' AND `active` = 1' : '')
        );
    }


    /**
     * Count auction title chars
     **/
    public static function countChars($str)
    {
        return strlen($str);
    }


    /**
     * Return theme images (paths)
     **/
    public function getImages()
    {
        $images = array();
        foreach (range(1, 16) as $imageIndex) {
            $imagePath = _ALLEGRO_IMG_FRAME_DIR_.(int)$this->id.'-image'.$imageIndex.'.'.self::IMAGE_EXT;
            if (file_exists($imagePath)) {
                $images[] = $imagePath;
            }
        }

        return $images;
    }
}
