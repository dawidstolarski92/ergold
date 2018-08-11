<?php
/**
* 2016 Revolution Slider
*
*  @author    SmatDataSoft <support@smartdatasoft.com>
*  @copyright 2016 SmatDataSoft
*  @license   private
*  @version   5.1.3
*  International Registered Trademark & Property of SmatDataSoft
*/

class UniteDBRev
{

    private $wpdb;
    private $lastRowID;

    public function __construct()
    {
        if (class_exists('RevsliderPrestashop')) {
            $this->wpdb = RevsliderPrestashop::$wpdb;
        } else {
            $this->wpdb = rev_db_class::revDbInstance();
        }
    }

    private function throwError($message, $code = -1)
    {
        UniteFunctionsRev::throwError($message, $code);
    }

    private function checkForErrors($prefix = "")
    {
        $errno = Db::getInstance()->getNumberError();


        if (!empty($errno)) {
            $message = Db::getInstance()->getMsgError();

            $message = $prefix . ' - <b>' . $message . '</b>';

            $this->throwError($message);
        }
    }

    public function insert($table, $arrItems)
    {
        $link = $this->wpdb->insert($table, $arrItems);



        $this->checkForErrors("Insert query error");



        $this->lastRowID = false;

        if (!empty($link)) {
            $this->lastRowID = $link;
        }

        return($this->lastRowID);
    }

    public function getLastInsertID()
    {
        $this->lastRowID = $this->wpdb->insertID();

        return($this->lastRowID);
    }

    public function delete($table, $where)
    {
        UniteFunctionsRev::validateNotEmpty($table, "table name");

        UniteFunctionsRev::validateNotEmpty($where, "where");

        $query = "delete from $table where $where";

        $this->wpdb->query($query);

        $this->checkForErrors("Delete query error");
    }

    public function runSql($query)
    {
        $this->wpdb->query($query);

        $this->checkForErrors("Regular query error");
    }

    public function update($table, $arrItems, $where)
    {
        $response = $this->wpdb->update($table, $arrItems, $where);

        if ($response === false) {
            UniteFunctionsRev::throwError("no update action taken!");
        }

        $this->checkForErrors("Update query error");


        return $response;
    }

    public function fetch($tableName, $where = "", $orderField = "", $groupByField = "", $sqlAddon = "")
    {
        $query = "select * from $tableName";

        if (!empty($where)) {
            $query .= " where $where";
        }

        if (!empty($orderField)) {
            $query .= " order by $orderField";
        }

        if (!empty($groupByField)) {
            $query .= " group by $groupByField";
        }

        if (!empty($sqlAddon)) {
            $query .= " " . $sqlAddon;
        }

        $response = $this->wpdb->getResults($query, ARRAY_A);

        $this->checkForErrors("fetch");

        return($response);
    }

    public function fetchSingle($tableName, $where = "", $orderField = "", $groupByField = "", $sqlAddon = "")
    {
        $response = $this->fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);

        if (empty($response)) {
            $this->throwError("Record not found");
        }

        $record = $response[0];

        return($record);
    }

    public function escape($string)
    {
        $string = esc_sql($string);

        return($string);
    }
}
// @codingStandardsIgnoreStart
class RevSliderDB extends UniteDBRev
{
    // @codingStandardsIgnoreEnd
}
