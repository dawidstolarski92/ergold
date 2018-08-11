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

class UniteBaseFrontClassRev extends UniteBaseClassRev
{

    const ACTION_ENQUEUE_SCRIPTS = "wp_enqueue_scripts";

    public function __construct($mainFile, $t)
    {
        parent::__construct($mainFile, $t);

//			self::addAction(self::ACTION_ENQUEUE_SCRIPTS, "onAddScripts");
    }
}
