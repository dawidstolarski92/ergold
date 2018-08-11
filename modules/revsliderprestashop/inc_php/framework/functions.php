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

//---------------------------------------------------------------------------------------------------------------------	

if (!function_exists("dmp")) {

    function dmp($str)
    {
        echo "<div align='left'>";

        echo "<pre>";

        print_r($str);

        echo "</pre>";

        echo "</div>";
    }
}
