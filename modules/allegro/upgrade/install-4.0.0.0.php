<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_0_0_0($object, $install = false)
{
	// Delete old config
    Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` LIKE "ALLEGRO_%"');

    // Rename old tables (except categories table)
    Db::getInstance()->Execute('
        RENAME TABLE `'._DB_PREFIX_.'allegro_account` 
        TO `'._DB_PREFIX_.'allegro_account_v3`;');

    Db::getInstance()->Execute('
        RENAME TABLE `'._DB_PREFIX_.'allegro_theme` 
        TO `'._DB_PREFIX_.'allegro_theme_v3`;');

    Db::getInstance()->Execute('
        RENAME TABLE `'._DB_PREFIX_.'allegro_auction` 
        TO `'._DB_PREFIX_.'allegro_auction_v3`;');

    Db::getInstance()->Execute('
        RENAME TABLE `'._DB_PREFIX_.'allegro_form` 
        TO `'._DB_PREFIX_.'allegro_form_v3`;');

    // Delete old tabs
    $object->uninstallModuleTab('AdminAllegro');
    $object->uninstallModuleTab('AdminAllegroNewAuctions');
    $object->uninstallModuleTab('AdminAllegroThemes');
    $object->uninstallModuleTab('AdminAllegroAccounts');
    $object->uninstallModuleTab('AdminAllegroSettings');
    $object->uninstallModuleTab('AdminAllegroUpdate');

    // Uninstall module
    $object->uninstall();

    // Fresh install
    $object->install();

	Configuration::updateValue('ALLEGRO_3X_UPGRADE', 1, false, 0, 0);
    Configuration::updateValue('ALLEGRO_INSTALLED_VERSION', $object->version, false, 0, 0);

	return true;
}