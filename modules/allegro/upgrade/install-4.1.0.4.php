<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_1_0_4($object, $install = false)
{
    if (Configuration::get('ALLEGRO_3X_UPGRADE') == 1) {
        return true;
    }
    
	Db::getInstance()->Execute('
	    ALTER TABLE `'._DB_PREFIX_.'allegro_account`
	    ADD `token_date_refresh` DATETIME AFTER `token_lifetime`
	');

	Db::getInstance()->Execute('
	    DELETE FROM `'._DB_PREFIX_.'allegro_product`
	    WHERE `country` != 1
	');

	Db::getInstance()->Execute('
	    ALTER TABLE `'._DB_PREFIX_.'allegro_product` DROP INDEX `id_product`
	');

	Db::getInstance()->Execute('
	    ALTER TABLE `'._DB_PREFIX_.'allegro_product` ADD UNIQUE `id_product` (`id_product`, `id_product_attribute`)
	');

	Db::getInstance()->Execute('
	    ALTER TABLE `'._DB_PREFIX_.'allegro_product`
	    DROP COLUMN `country`
	');

	Db::getInstance()->Execute('
	    ALTER TABLE `'._DB_PREFIX_.'allegro_product`
	    DROP COLUMN `deleted`
	');

	return true;
}