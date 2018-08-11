<?php
/**
 * 2016 Smart Soft.
 *
 *  @author    Marcin Kubiak <zlecenie@poczta.onet.pl>
 *  @copyright Smart Soft
 *  @license   Commercial License
 *  International Registered Trademark & Property of Smart Soft
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_1_8($object)
{
    $query = 'ALTER TABLE `'._DB_PREFIX_.'advancedexport` ADD `start_id` int(10) NOT NULL DEFAULT 0 after `last_exported_id`';
    $return = Db::getInstance()->execute($query);

    $query = 'ALTER TABLE `'._DB_PREFIX_.'advancedexport` ADD `end_id` int(10) NOT NULL DEFAULT 0 after `start_id`';
    $return &= Db::getInstance()->execute($query);

    return $return;
}
