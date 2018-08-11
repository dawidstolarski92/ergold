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

function upgrade_module_1_2_2($module)
{
	doNothing($module);
	$sql = 'UPDATE `'._DB_PREFIX_.'paczkomatyinpost` SET `cod`=\'-1\' WHERE status = \'UNDEFINED\' AND cod = 0';
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
