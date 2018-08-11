<?php
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/pk_newsletter.php');
$newslett = new pk_newsletter();
$email = Tools::getValue('email');

if ($email != "") {
	$data = $newslett->newsletterAjaxRegistration($email);
	echo $data;
} else {
	echo "Something goes wrong";
}

?>