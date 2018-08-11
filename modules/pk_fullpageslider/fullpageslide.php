<?php

class fullpageslide extends ObjectModel
{
	public $title;
	public $description;
	public $url;
	public $legend;
	public $image;

	public $text_animation;
	public $text_delay;
	public $text_align;
	public $text_width;
	public $text_speed;
	public $text_x;
	public $text_y;

	public $subimage01;
	public $subimage01_state;
	public $subimage01_animation;
	public $subimage01_delay;
	public $subimage01_speed;
	public $subimage01_x;
	public $subimage01_y;

	public $subimage02;
	public $subimage02_state;
	public $subimage02_animation;
	public $subimage02_delay;
	public $subimage02_speed;
	public $subimage02_x;
	public $subimage02_y;

	public $subimage03;
	public $subimage03_state;
	public $subimage03_animation;
	public $subimage03_delay;
	public $subimage03_speed;
	public $subimage03_x;
	public $subimage03_y;

	public $active;
	public $position;
	public $id_shop;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'pk_fullpageslider_slides',
		'primary' => 'id_slides',
		'multilang' => true,
		'fields' => array(
			'active' =>			array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			'position' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),

			// Lang fields
			'description' =>	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4000),
			'title' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
			'legend' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
			'url' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'required' => true, 'size' => 255),
			'image' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),

			'text_animation'=>array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
			'text_align' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'text_delay' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'text_width' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'text_speed' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'text_x' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'text_y' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),

			'subimage01' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			'subimage01_state' =>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			'subimage01_animation'=>array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
			'subimage01_delay' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'subimage01_speed' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'subimage01_x' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'subimage01_y' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),

			'subimage02' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			'subimage02_state' =>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			'subimage02_animation'=>array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
			'subimage02_delay' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'subimage02_speed' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'subimage02_x' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'subimage02_y' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),

			'subimage03' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			'subimage03_state' =>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			'subimage03_animation'=>array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
			'subimage03_delay' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'subimage03_speed' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'subimage03_x' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'subimage03_y' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
		)
	);

	public function __construct($id_slide = null, $id_lang = null, $id_shop = null, Context $context = null)
	{
		parent::__construct($id_slide, $id_lang, $id_shop);
	}

	public function add($autodate = true, $null_values = false)
	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;

		$res = parent::add($autodate, $null_values);
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'pk_fullpageslider` (`id_shop`, `id_slides`)
			VALUES('.(int)$id_shop.', '.(int)$this->id.')'
		);
		return $res;
	}

	public function delete()
	{
		$res = true;

		$skip = array("slide01.jpg", "slide02.jpg", "slide03.jpg", "slide04.jpg", "slide05.jpg");

		$images = $this->image;
		foreach ($images as $image)
		{
			if (!in_array($image, $skip))
				if (preg_match('/sample/', $image) === 0)
					if ($image && file_exists(dirname(__FILE__).'/images/'.$image))
						$res &= @unlink(dirname(__FILE__).'/images/'.$image);
		}

		$res &= $this->reOrderPositions();

		$res &= Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'pk_fullpageslider`
			WHERE `id_slides` = '.(int)$this->id
		);

		$res &= parent::delete();
		return $res;
	}

	public function reOrderPositions()
	{
		$id_slide = $this->id;
		$context = Context::getContext();
		$id_shop = $context->shop->id;

		$max = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT MAX(hss.`position`) as position
			FROM `'._DB_PREFIX_.'pk_fullpageslider_slides` hss, `'._DB_PREFIX_.'pk_fullpageslider` hs
			WHERE hss.`id_slides` = hs.`id_slides` AND hs.`id_shop` = '.(int)$id_shop
		);

		if ((int)$max == (int)$id_slide)
			return true;

		$rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hss.`position` as position, hss.`id_slides` as id_slide
			FROM `'._DB_PREFIX_.'pk_fullpageslider_slides` hss
			LEFT JOIN `'._DB_PREFIX_.'pk_fullpageslider` hs ON (hss.`id_slides` = hs.`id_slides`)
			WHERE hs.`id_shop` = '.(int)$id_shop.' AND hss.`position` > '.(int)$this->position
		);

		foreach ($rows as $row)
		{
			$current_slide = new fullpageslide($row['id_slide']);
			--$current_slide->position;
			$current_slide->update();
			unset($current_slide);
		}

		return true;
	}

	public static function getAssociatedIdsShop($id_slide)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_shop`
			FROM `'._DB_PREFIX_.'pk_fullpageslider` hs
			WHERE hs.`id_slides` = '.(int)$id_slide
		);

		if (!is_array($result))
			return false;

		$return = array();

		foreach ($result as $id_shop)
			$return[] = (int)$id_shop['id_shop'];

		return $return;
	}

}
