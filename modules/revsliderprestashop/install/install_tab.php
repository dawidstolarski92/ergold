<?php
/**
* 2016 Revolution Slider
*
*  @author    SmatDataSoft <support@smartdatasoft.com>
*  @copyright 2016 SmatDataSoft
*  @license   private
*  @version   5.1.3
*  International Registered Trademark & Property of SmatDataSoft
*/

$tabvalue = array(
    array(
        'class_name' => 'AdminRevolutionslider',
        'id_parent' => 0,
        'module' => '',
        'name' => 'Revolution Slider',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminRevolutionsliderSettings',
        'id_parent' => 'AdminRevolutionslider',
        'module' => 'revsliderprestashop',
        'name' => 'Configure',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminRevolutionsliderNavigation',
        'id_parent' => 'AdminRevolutionslider',
        'module' => 'revsliderprestashop',
        'name' => 'Navigation',
        'active' => 1,
    ),
    array(
        'class_name' => 'AdminRevolutionsliderAjax',
        'id_parent' => -1,
        'module' => 'revsliderprestashop',
        'name' => 'Revolution Ajax Controller',
        'active' => 0,
    ),
    array(
        'class_name' => 'AdminRevolutionsliderUpload',
        'id_parent' => -1,
        'module' => 'revsliderprestashop',
        'name' => 'Revolution Upload Manager',
        'active' => 0,
    ),
);
