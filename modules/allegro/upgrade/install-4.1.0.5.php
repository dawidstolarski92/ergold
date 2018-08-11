<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_1_0_5($object, $install = false)
{
    if (Configuration::get('ALLEGRO_3X_UPGRADE') == 1) {
        return true;
    }
    
	$res = true;
    $res &= Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_auction` CHANGE `id_allegro_auction` `id_auction` bigint(20)');
    $res &= Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_auction` DROP PRIMARY KEY');
    $res &= Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_auction` ADD `id_allegro_auction` INT(11) AUTO_INCREMENT PRIMARY KEY FIRST');
    $res &= Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_auction` ADD CONSTRAINT unique_1 UNIQUE (`id_auction`, `id_allegro_product`, `id_allegro_account`)');

    $res &= Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_shipping` DROP COLUMN `country`');
    $res &= Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_shipping` ADD `default` INT(1)');
    AllegroShipping::setDefault();

    $res &= Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_theme` DROP COLUMN `country`');
    $res &= Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_theme` ADD `default` INT(1)');

    return $res;
}