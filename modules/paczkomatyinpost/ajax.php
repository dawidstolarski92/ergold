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

include_once('../../config/config.inc.php');
include_once('../../init.php');

require_once('paczkomatyinpost.php');

if ($_POST['ajax'])
{
	$id_cart = (int)Tools::getValue('id_cart');
	$machine = Tools::getValue('machine');
	if (Tools::isSubmit('updateMachine'))
		PaczkomatyInpost::updateData($machine);

	else if (Tools::isSubmit('updateMachineCod'))
		PaczkomatyInpost::updateData(null, $machine);
}