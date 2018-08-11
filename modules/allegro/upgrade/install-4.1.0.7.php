<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_1_0_7($object, $install = false)
{
	if (Configuration::get('ALLEGRO_3X_UPGRADE') == 1) {
		return true;
	}
	
	try {
	    @Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_product` ADD `cache_relist_error` VARCHAR(255)');
	    @Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_order` ADD `invoice` INT(1)');	
	} catch (Exception $e) {
		// Notning
	}

    return true;
}