<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_1_1_3($object, $install = false)
{
	if (Configuration::get('ALLEGRO_3X_UPGRADE') == 1) {
		return true;
	}

	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_product_account` ADD `additional_services` VARCHAR(255) NOT NULL');

    Configuration::updateValue('ALLEGRO_THEME_HTMLPURIFIER', '1', false, 0, 0);

    return true;
}