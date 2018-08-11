<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class AllegroTools
{
	/**
	 * Format price to "pl" format
	 **/
	public static function formatPrice($price)
	{
		if (function_exists('money_format')) {
			return money_format('%.2i', $price).' zł';
		} else {
			return str_replace((string)round($price, 2), '.', ',').' zł';
		}
	}

	
	/**
	 * Check if given int is 32 bit integer
	 **/
	public static function is32bitInt($int)
	{
		return is_numeric($int) && $int <= 2147483647;
	}
	

	/**
	 * Returns product images types
	 **/
	public static function getProductImageTypes($width = null)
	{
		return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'image_type`
            WHERE `products` = 1
            '.($width ? 'AND `width` >= '.(int)$width : '').'
            ORDER BY `width` ASC, `height` ASC
            '.($width ? 'LIMIT 1' : '')
        );
	}


	/**
	 * Returns product image type name by min. width
	 **/
	public static function getProductImageTypeName($width)
	{
		return Db::getInstance()->getValue(
            'SELECT `name` FROM `'._DB_PREFIX_.'image_type`
            WHERE `products` = 1
            AND `width` >= '.(int)$width.'
            ORDER BY `width` ASC, `height` ASC'
        );
	}

	
	/**
	 * Returns language Id
	 **/
	public static function getLangId()
	{
		$id = (int)Language::getIdByIso('pl');
		if (!$id) {
			$id = (int)Configuration::get('PS_LANG_DEFAULT');
		}

		return $id;
	}
}