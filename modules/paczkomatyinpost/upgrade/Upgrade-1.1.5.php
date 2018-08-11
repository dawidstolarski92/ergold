<?php
/**
 * LICENCE
 *
 * ALL RIGHTS RESERVED.
 * YOU ARE NOT ALLOWED TO COPY/EDIT/SHARE/WHATEVER.
 *
 * IN CASE OF ANY PROBLEM CONTACT AUTHOR.
 *
 *  @author    Tomasz Dacka (kontakt@tomaszdacka.pl)
 *  @copyright PrestaHelp.com
 *  @license   ALL RIGHTS RESERVED
 */
require_once dirname(__FILE__).'/../paczkomatyinpost.php';

function upgrade_module_1_1_5($module)
{
    doNothing($module);
    $sql = 'ALTER TABLE `'._DB_PREFIX_.'paczkomatyinpost` CHANGE `receiver_machine` `receiver_machine` CHAR(10)';
    $sql .= ', CHANGE `receiver_machine_cod` `receiver_machine_cod` CHAR(10)';
    $sql .= ', CHANGE `sender_machine` `sender_machine` CHAR(10)';
    if (Db::getInstance()->execute($sql))
        return true;
    return false;
}
if (!function_exists('doNothing')) {
    function doNothing($a)
    {
        return $a;
    }
}
