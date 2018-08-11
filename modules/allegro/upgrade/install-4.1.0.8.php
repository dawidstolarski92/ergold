<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_1_0_8($object, $install = false)
{
	Configuration::updateValue('ALLEGRO_STOCK_SYNC', '1', false, 0, 0);

    return true;
}