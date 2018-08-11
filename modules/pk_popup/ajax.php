<?php
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/pk_popup.php');
$ppp = new pk_popup();

echo $ppp->newsletterRegistration(Tools::getValue('email'));