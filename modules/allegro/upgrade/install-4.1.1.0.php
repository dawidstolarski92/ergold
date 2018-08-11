<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_1_1_0($object, $install = false)
{
	if (Configuration::get('ALLEGRO_3X_UPGRADE') == 1) {
		return true;
	}
	
	// No more country in allegro fields table
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_field` DROP COLUMN `country`');
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_field` DROP INDEX `country`');
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_field` ADD CONSTRAINT `unique_1` UNIQUE (`scope`, `id`, `fid`)');

	// No more country in allegro category table
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_category` DROP COLUMN `country`');
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_category` DROP INDEX `id_category`');
    
    Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_category` ADD `is_leaf` INT(1)');

    // Force reload categories
    Configuration::updateValue('ALLEGRO_CATEGORIES_VER', '0', false, 0, 0);

    // Order sync for product
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_product` DROP COLUMN `order_sync`');
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_product_shop` DROP COLUMN `order_sync`');

	// Product warranty
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_product_account` ADD `implied_warranty` VARCHAR(255) DEFAULT NULL');
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_product_account` ADD `return_policy` VARCHAR(255) DEFAULT NULL');
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_product_account` ADD `warranty` VARCHAR(255) DEFAULT NULL');
	
	// No more country in allegro accounts
	Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'allegro_account` WHERE `country` != 1');
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_account` DROP COLUMN `country`');

	// No more SOAP auth (login + password)
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_account` DROP COLUMN `password`');

    return true;
}