<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_1_1_4($object, $install = false)
{
	if (Configuration::get('ALLEGRO_3X_UPGRADE') == 1) {
		return true;
	}

	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_auction` ADD INDEX `id_allegro_product` (`id_allegro_product`)');
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_auction` ADD INDEX `status` (`status`)');
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'allegro_auction` ADD INDEX `id_allegro_account` (`id_allegro_account`)');

    return true;
}