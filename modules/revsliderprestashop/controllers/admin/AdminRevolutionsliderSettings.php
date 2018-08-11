<?php
/**
 * 2016 Revolution Slider
 *
 *  @author    SmatDataSoft <support@smartdatasoft.com>
 *  @copyright 2016 SmatDataSoft
 *  @license   private
 *  @version   5.1.7
 *  International Registered Trademark & Property of SmatDataSoft
 */

class AdminRevolutionsliderSettingsController extends ModuleAdminController
{

    public function __construct()
    {
        $module = "revsliderprestashop";
        Tools::redirectAdmin('index.php?controller=AdminModules&configure=' . $module . '&token=' . Tools::getAdminTokenLite('AdminModules'));
    }
}
