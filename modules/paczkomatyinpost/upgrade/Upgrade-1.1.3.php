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
function upgrade_module_1_1_3($module)
{
	Configuration::updateValue(PaczkomatyInpost::KEY_CARRIER_ANY, 1);
	Configuration::deleteByName('PACZKOMATYINPOST_UPDATE');
	if ($module->registerHook('displayHeader'))
		return true;
	return false;
}
