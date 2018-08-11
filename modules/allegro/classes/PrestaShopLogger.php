<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

class PrestaShopLogger
{
    public static function addLog($data = null, $b = null, $c = null, $d = null, $e = null, $f = null)
    {
        $log = date('######### Y:m:d H:i:s.'.microtime(true).' #########').PHP_EOL;
        $log .= print_r($data, true).PHP_EOL;

        // Delete old log
        @unlink(_ALLEGRO_LOGS_DIR_.date('D', strtotime('-3 day')).'_ps15.log');

        return file_put_contents(_ALLEGRO_LOGS_DIR_.date('D').'_ps15.log', $log, FILE_APPEND);
    }
}