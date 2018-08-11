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

class RevDbEngine
{

    public static $wpdb;
    public $mysqli;
    //$dbh;

    public $prefix;

    public function __construct()
    {
        $this->prefix = _DB_PREFIX_;
    }

    public function realEscape($string)
    {
        return Db::getInstance()->escape($string);
    }

    public function _escape($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    $data[$k] = $this->_escape($v);
                } else {
                    $data[$k] = $this->realEscape($v);
                }
            }
        } else {
            $data = $this->realEscape($data);
        }



        return $data;
    }

    public function query($sql)
    {

        //if($query = $this->mysqli->query($sql))
        $query = Db::getInstance()->execute($sql);
        if ($query) {
            return true;
        }

        return false;
    }

    public function update($table, $data, $where = '', $limit = 0, $null_values = false, $use_cache = true, $add_prefix = false)
    {
        $wherestr = '';
        $c = 0;

        $sql = "UPDATE {$table} SET ";

        if (!empty($data)) {
            foreach ($data as $k => $d) {
                if ($c > 0) {
                    $sql .= ', ';
                }

                if (is_string($d)) {
                    $sql .= "$k=\"" . addslashes($d) . "\"";
                } else {
                    $sql .= "$k=$d";
                }

                $c++;
            }
        }

        $sql .= " ";

        $c = 0;

        if (!empty($where) && is_array($where)) {
            $sql .= "WHERE ";

            foreach ($where as $k => $val) {
                if ($c > 0) {
                    $wherestr .= " AND ";
                }

                $wherestr .= "{$k}=";
                if (is_string($val)) {
                    $wherestr .= '"' . $this->_escape($val) . '"';
                } else {
                    $wherestr .= $val;
                }

                $c++;
            }
            $sql .= $wherestr;
        }

//        if(Db::getInstance()->update($table, $this->_escape($data), $wherestr , $limit, $null_values, $use_cache, $add_prefix))
//                return true;
        if (Db::getInstance()->execute($sql)) {
            return true;
        }

        return false;
    }

    public function insert($table, $data, $null_values = false, $use_cache = true, $type = 1, $add_prefix = false)
    {
        $c = 0;

        $cols = '';
        $vals = '';

        $sql = "INSERT INTO {$table}";

        if (!empty($data)) {
            $cols .= '(';
            $vals .= ' VALUES(';
            foreach ($data as $k => $d) {
                if ($c > 0) {
                    $cols .= ', ';
                    $vals .= ', ';
                }
                $cols .= $k;

                if (is_string($d)) {
                    //                    $vals .= "\"".addslashes($d)."\"";
                    $vals .= "'" . addslashes($d) . "'";
                } else {
                    $vals .= $d;
                }

                $c++;
            }
            $cols .= ')';
            $vals .= ')';
        }

        $sql .= "{$cols} {$vals}";

        if (Db::getInstance()->execute($sql)) {
            return $this->insertID();
        }

        return false;
    }

    public function insertID()
    {
        return Db::getInstance()->Insert_ID();
    }

    public function getVar($sql, $assoc = false)
    {
        $query = Db::getInstance()->getValue($sql);

        if (!empty($query)) {
            return $query;
        }

        return false;
    }

    public function getRow($sql, $assoc = false)
    {
        $query = Db::getInstance()->getRow($sql);

        if ($query) {
            return $query;
        }

        return false;
    }

    public function getResults($sql, $assoc = false)
    {
        $query = Db::getInstance()->ExecuteS($sql, true, false);

        if (!empty($query)) {
            return $query;
        }

        if (empty($query) && $assoc == ARRAY_A) {
            return array();
        }


        return false;
    }

    public static function revDbInstance()
    {
        if (!self::$wpdb instanceof RevDbEngine) {
            return self::$wpdb = new RevDbEngine();
        }

        return self::$wpdb;
    }
}

// @codingStandardsIgnoreStart
class rev_db_class extends RevDbEngine
{
    // @codingStandardsIgnoreEnd
}
