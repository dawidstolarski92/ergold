<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');

$module_instance = Module::getInstanceByName('psinpost');

if(Tools::isSubmit('json')) {
	$json = Tools::jsonDecode(file_get_contents('php://input'), true);
//	error_log(print_r($json, true));
	if (!isset($json['token']) || $json['token'] != sha1(_COOKIE_KEY_.$module_instance->name)) {
		//echo "Bad token: " . Tools::getValue('token') . '!=' . sha1(_COOKIE_KEY_.$module_instance->name);
		exit;
	}

	if (isset($json['createLabel'])) {
		$resp = $module_instance->createLabelJson($json);
		if ($resp === false) {
    		die(Tools::jsonEncode(array(
        		'error' => reset(PSInpost::$errors)
			)));
    	}

		$id_inpost = $resp[0];
		$tracking = $resp[1];
		die(Tools::jsonEncode(array(
			'error' => false,
			'id_inpost' => (int)$id_inpost,
			'tracking' => $tracking
		)));
	}
}
else {
	if (!Tools::isSubmit('token') || (Tools::isSubmit('token')) && Tools::getValue('token') != sha1(_COOKIE_KEY_.$module_instance->name)) {
		//echo "Bad token: " . Tools::getValue('token') . '!=' . sha1(_COOKIE_KEY_.$module_instance->name);
		exit;
	}

	if (Tools::isSubmit('createLabel')) {
		$resp = $module_instance->createLabel(Tools::getValue('id_order'), Tools::getValue('inpost_kwota'), Tools::getValue('inpost_adres'), Tools::getValue('inpost_kod'), Tools::getValue('inpost_miasto'), Tools::getValue('inpost_uwagi'), Tools::getValue('inpost_ubezp'), Tools::getValue('inpost_waga'), Tools::getValue('inpost_dlug'), Tools::getValue('inpost_szer'), Tools::getValue('inpost_wys'), Tools::getValue('inpost_nst'), Tools::getValue('inpost_mail'), Tools::getValue('inpost_sms'));
		if ($resp === false) {
    		die(Tools::jsonEncode(array(
        		'error' => reset(PSInpost::$errors)
			)));
    	}

		$id_inpost = $resp[0];
		$tracking = $resp[1];
		die(Tools::jsonEncode(array(
			'error' => false,
			'id_inpost' => (int)$id_inpost,
			'tracking' => $tracking
		)));
	}
}
