<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
set_include_path(get_include_path() . PATH_SEPARATOR . 'libraries');

$module_instance = Module::getInstanceByName('psinpost');

if (Tools::isSubmit('pass')) {
	$module_instance->doUpdate(Tools::getValue('pass'));
}
