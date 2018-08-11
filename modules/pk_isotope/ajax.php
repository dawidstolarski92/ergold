<?php
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/pk_isotope.php');

$isotope = new pk_isotope();

if (Tools::getValue('type')) {
	$isotope->ajaxCall(Tools::getValue('type'));
}
if (Tools::getValue('cID')) {
	$isotope->getProductsFromCategory(Tools::getValue('cID'));
}
if (Tools::getValue('catID')) {
	$isotope->getCatList(Tools::getValue('catID'));
}
if (Tools::getValue('rem_catID')) {
	$isotope->remCatFromList(Tools::getValue('rem_catID'));
}
if (Tools::getValue('pID')) {
	$isotope->saveData(Tools::getValue('pID'));
}
if (Tools::getValue('rem_pID')) {
	$isotope->removeData(Tools::getValue('rem_pID'));
}
?>