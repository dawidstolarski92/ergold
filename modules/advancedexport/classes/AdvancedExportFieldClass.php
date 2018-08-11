<?php
/**
 * 2016 Smart Soft.
 *
 *  @author    Marcin Kubiak
 *  @copyright Smart Soft
 *  @license   Commercial License
 *  International Registered Trademark & Property of Smart Soft
 */

class AdvancedExportFieldClass extends ObjectModel
{
    public $id;
    public $tab;
    public $name;
    public $field;
    public $table;
    public $alias;
    public $as;
    public $attribute;
    public $return;
    public $import;
    public $import_name;
    public $import_combination;
    public $import_combination_name;
    public $isCustom;

    public static $definition = array(
        'table' => 'advancedexportfield',
        'primary' => 'id_advancedexportfield',
    );

    public static function getDefaultCombinationImportFields($tab)
    {
        $query = 'SELECT * FROM `'._DB_PREFIX_."advancedexportfield`
                  WHERE tab = '" .$tab."'
                  AND import_combination > 0
                  ORDER BY import_combination";

        $fields = Db::getInstance()->ExecuteS($query);
        $result = array();

        foreach ($fields as $field) {
            $result[] = $field['field'];
        }

        return $result;
    }

    public static function getDefaultImportFields($tab)
    {
        $query = 'SELECT * FROM `'._DB_PREFIX_."advancedexportfield`
                  WHERE tab = '" .$tab."'
                  AND import > 0
                  ORDER BY import";

        $fields = Db::getInstance()->ExecuteS($query);
        $result = array();

        foreach ($fields as $field) {
            $result[] = $field['field'];
        }

        return $result;
    }

    public static function getDefaultCombinationFields($tab)
    {
        $query = 'SELECT * FROM `'._DB_PREFIX_."advancedexportfield`
                  WHERE tab = '" .$tab."'
                  AND import_combination > 0";

        $fields = Db::getInstance()->ExecuteS($query);
        $result = array();

        foreach ($fields as $field) {
            $result[] = $field['field'];
        }

        return $result;
    }

    public function copyFromPost()
    {
        /* Classical fields */
        foreach ($_POST as $key => $value) {
            if (key_exists($key, $this) and $key != 'id_'.$this->table) {
                $this->{$key} = $value;
            }
        }
    }

    public static function getAllFields($tab)
    {
        $query = 'SELECT * FROM `'._DB_PREFIX_."advancedexportfield`
                  WHERE tab = '" .$tab."'";

        $fields = Db::getInstance()->ExecuteS($query);
        $result = array();

        foreach ($fields as $field) {
            $result[$field['field']] = $field;
        }

        return $result;
    }

    public static function getNumberOfRows($tab)
    {
        $query = 'SELECT COUNT(*) FROM `'._DB_PREFIX_."advancedexportfield`
                  WHERE tab = '" .$tab."'";

        $result = Db::getInstance()->ExecuteS($query);

        return $result[0]['COUNT(*)'];
    }

    public static function getAllFieldsWithPagination($tab, $limit, $start)
    {
        $query = 'SELECT * FROM `'._DB_PREFIX_."advancedexportfield`
                  WHERE tab = '" .$tab."'";

        $query .= ' LIMIT '.$limit;
        if ($limit != false && $limit > 0) {
            $query .= ' OFFSET '.$start * $limit;
        }

        $fields = Db::getInstance()->ExecuteS($query);

        return $fields;
    }

    public function getFields()
    {
        parent::validateFields();
        $fields = null;
        $fields['tab'] = (string) ($this->tab);
        $fields['name'] = (string) ($this->name);
        $fields['field'] = (string) ($this->field);
        $fields['table'] = (string) ($this->table);
        $fields['alias'] = (string) ($this->alias);
        $fields['return'] = (string) ($this->return);
        $fields['as'] = (string) ($this->as);
        $fields['attribute'] = (bool) ($this->attribute);
        $fields['import'] = (bool) ($this->import);
        $fields['import_combination'] = (bool) ($this->import_combination);
        $fields['import_combination_name'] = (string) ($this->import_combination_name);
        $fields['import_name'] = (string) ($this->import_name);
        $fields['import_name'] = (string) ($this->import_name);
        $fields['isCustom'] = (bool) ($this->isCustom);

        return $fields;
    }
}
