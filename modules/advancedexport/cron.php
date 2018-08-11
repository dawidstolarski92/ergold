<?php
/**
 * 2016 Smart Soft.
 *
 *  @author    Marcin Kubiak
 *  @copyright Smart Soft
 *  @license   Commercial License
 *  International Registered Trademark & Property of Smart Soft
 */

include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/advancedexport.php';

if (Tools::getIsset('secure_key')) {
    $secureKey = Configuration::get('ADVANCEDEXPORT_SECURE_KEY');
    if (!empty($secureKey) and $secureKey === Tools::getValue('secure_key')) {
        include dirname(__FILE__).'/../../init.php';

        $ae = new AdvancedExport();
        $ae->cronTask();
    }
}
