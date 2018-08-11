<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

abstract class AFField
{
	/**
	 * Scopes of field
	 *
	 * -- GLOBAL
	 * -- -- CATEGORY
	 * -- -- SHIPPING
	 * -- -- -- PRODUCT
	 *
	 * Scopes are available for inheriting field values.
	 */
	const SCOPE_GLOBAL 		= 1;
	const SCOPE_CATEGORY 	= 2;
	const SCOPE_SHIPPING 	= 4;
	const SCOPE_PRODUCT 	= 8;


	/**
	 * Object id for GLOBAL scope
	 */
	const SCOPE_GLOBAL_ID	= 1;


	/**
	 * Translation of field id to names
	 * for often used fields
	 */
	const FID_TITLE 	= 1;
	const FID_CATEGORY 	= 2;
	const FID_START 	= 3;
	const FID_DURATION 	= 4;
	const FID_QTY 		= 5;
    const FID_ST_PRICE 	= 6;
	const FID_PRICE 	= 8;
	const FID_COUNTRY 	= 9;
	const FID_IMG_1 	= 16;
	const FID_DESC		= 24;
    const FID_DESC_2    = 341;
	const FID_EAN		= 337;

	// FIDs from 16 to 23 and 342 to 349 (16 total)
	public static $FID_IMAGES = array(
		16, 17, 18, 19, 20, 21, 22, 23,
		342, 343, 344, 345, 346, 347, 348, 349
	);

	/**
	 * Add new field value to DB
	 *
	 * @param integer $scope    Scope
	 * @param integer $id       Scope object ID
	 * @param integer $fid 		Allegro field ID
	 * @param string  $value    Value of field
	 */
	public static function add($scope, $id, $fid, $value)
	{
		if(!$scope || !$id || !$fid  || !isset($value)) {
			throw new Exception('Invalid field data.');
		}

		// Flag
		if (is_array($value)) {
			$value = array_sum($value);
		}

		// Insert to DB
		return Db::getInstance()->insert(
			'allegro_field',
			array(
			    'scope'		=> (int)$scope,
			    'id'		=> (int)$id,
			    'fid'		=> (int)$fid,
			    'value'		=> pSql($value, true),
			), true, true, (version_compare(_PS_VERSION_, '1.6.1.0', '>=') 
				? DB::ON_DUPLICATE_KEY 
				: DB::REPLACE)
		);
	}


	/**
	 * Add list of field to DB
	 *
	 * @param integer $scope    Scope
	 * @param integer $id       Scope object ID
	 * @param array   $fieldsList    List of fileds to add
	 */
	public static function addList($scope, $id, $fieldsList, $clear = true)
	{
		if(!is_array($fieldsList)) {
			throw new Exception('Invalid field list.');
		}

		$res = true;

		// Clear fields for given scope and ID
		if ($clear) {
			$res &= self::clear($scope, $id);
		}

		foreach ($fieldsList as $fid => $value) {
			$res &= self::add($scope, $id, $fid, $value);
		}

		return (bool)$res;
	}


	/**
	 * Return raw field value
	 *
	 * @param integer $scope    Scope
	 * @param integer $id       Scope object ID
	 * @param integer $fid Allegro field ID
	 * @return mixed value or false
	 */
	public static function get($fid, $scopesList)
	{
		$res = self::getList(array($fid), $scopesList);

		return isset($res[$fid])
			? $res[$fid]
			: false;
	}


	/**
	 * Get field values for given scopes
	 *
	 * @param array 	$fieldsIdsList 	List of fields
	 * @param array 	$scopeList  	List of scopes & IDs
	 * @param integer 	$masterScope 	Master top priority scope
	 * @return mixed array or bool
	 */
	public static function getList($fieldsIdsList = array(), $scopesList, $masterScope = null)
	{
    	// Create SQL
		$sql = 'SELECT `fid`, `scope`, `value`
			FROM `'._DB_PREFIX_.'allegro_field`
			WHERE `value` IS NOT NULL
			'.(count($fieldsIdsList) ? 'AND `fid` IN ('.implode(',', $fieldsIdsList).')' : '').'
			AND ( # List of groups
		';

		// Iterate each scope, we want to get fields for all scope given
		foreach ($scopesList as $scope => $id) {
			$sql .= '(`scope` = '.(int)$scope.' AND `id` = '.(int)$id.' AND `value` IS NOT NULL) OR';
		}
		// Cut last "OR"
		$sql = substr($sql, 0, -2);
        $sql .= ')';

		// Order is important!
		$sql .= 'ORDER BY `scope` ASC';

		$res = Db::getInstance()->executeS($sql);

		// Group field values by field ID (one field can have multiple values!)
		$fieldValuesGrouped = array();

		foreach ($res as $fieldValue) {
			if ($masterScope) {
				// If scope match we have master value
				if($fieldValue['scope'] == $masterScope) {
					$fieldValuesGrouped[(int)$fieldValue['fid']]['value'] = $fieldValue['value'];
				} else { // If not we have parent value (last value = highest priority)
					$fieldValuesGrouped[(int)$fieldValue['fid']]['parent_value'] = $fieldValue['value'];
				}
				$fieldValuesGrouped[(int)$fieldValue['fid']]['scope'] = $fieldValue['scope'];
			} else {
				$fieldValuesGrouped[(int)$fieldValue['fid']] = $fieldValue['value'];
			}
		}

		return $fieldValuesGrouped;
	}


	/**
	 * Method allow to delete/unset field value
	 *
	 * @param  integer $scope   	Scope
	 * @param  integer $id      	Scope object ID
	 * @param  integer $fid    ID field
	 * @return bool
	 */
	public static function clear($scope, $id, $fid = null)
	{
		return Db::getInstance()->update('allegro_field',
			array('value' => ''/* NULL */),
			'`scope` = '.(int)$scope.'
			AND `id` = '.(int)$id.
			($fid ? ' AND `fid` = '.(int)$fid : ''),
			0, true/* NULL values */
		);
	}
}
