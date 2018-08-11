<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

/**
* Class used for generating allegro fileds list
* needed for offer/auction creation
*/
class AllegroFieldBuilder
{
	/**
	 * Allegro fields list
	 **/
	private $fieldsList = array();


	/**
	 * Allegro raw fields list
	 **/
	private $fieldsListRaw = array();


	/**
	 * Allegro fields structure
	 **/
	private $fieldsStruct = array(
        'fid'           	=> null,
        'fvalueInt'     	=> null,
        'fvalueFloat'   	=> null,
        'fvalueString'  	=> null,
        'fvalueImage'   	=> null,
        'fvalueDatetime' 	=> null,
    );
	

	function __construct()
	{
  }


	/**
	 * Returns field structure
	 **/
	private function getFieldStruct()
	{
		return $this->fieldsStruct;
	}


	/**
	 * Add single field to list
	 **/
	public function addField($fieldId, $fieldValue, $override = true)
	{
		if (!is_numeric($fieldId)) {
			throw new Exception("Invalid field ID [{$fieldId}]");
		}

        if (!$this->getField($fieldId) || $override) {
      		// Add to list
      		$this->fieldsListRaw[(int)$fieldId] = $fieldValue;
        }
	}


  /**
   * Remove field from list
   **/
  public function removeField($fieldId)
  {
    if (!is_numeric($fieldId)) {
      throw new Exception("Invalid field ID [{$fieldId}]");
    }

    unset($this->fieldsListRaw[(int)$fieldId]);
  }

    /**
     * Returns value of field
     **/
    public function getField($fieldId)
    {
        return isset($this->fieldsListRaw[$fieldId]) 
            ? $this->fieldsListRaw[$fieldId] 
            : null;
    }


  /**
  * Builds list of fields
  **/
  public function build($raw = false)
  {
    if ($raw) {
      return $this->fieldsListRaw;
    } else {
        foreach ($this->fieldsListRaw as $fieldId => $fieldValue) {

            // Create skeleton
            $field = $this->getFieldStruct();

            // Set field ID
            $field['fid'] = $fieldId;

        
            if (in_array($fieldId, AFField::$FID_IMAGES)) {
                /**
                 * Image base_64 string
                 **/
                $field['fvalueImage'] = (string)$fieldValue;
            } else {

                /**
                * Check if iteger is 32 bit integer
                * SOAP api is working on 32 bit machine
                **/
                $is32bitInt = AllegroTools::is32bitInt($fieldValue);

                $field['fvalueInt'] = $is32bitInt ? (int)$fieldValue : null;
                $field['fvalueFloat'] = (float)$fieldValue;
                $field['fvalueDatetime'] = (int)$fieldValue;
                $field['fvalueString'] = (string)$fieldValue;
            }

            $this->fieldsList[] = $field;
        }

        return $this->fieldsList;
    }
  }
}