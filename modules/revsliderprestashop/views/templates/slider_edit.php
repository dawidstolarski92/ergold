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

//check existing slider data:
$sliderID = self::getGetVar('id');

$arrFieldsParams = array();

$uslider = new RevSlider();

$slider = new RevSlider();
$slider->initByID($sliderID);

//get setting fields
$settingsFields = $slider->getSettingsFields();
$arrFieldsMain = $settingsFields['main'];
$arrFieldsParams = $settingsFields['params'];

$is_edit = true;

RevGlobalObject::setVar('is_edit', $is_edit);
RevGlobalObject::setVar('sliderID', $sliderID);

require self::getPathTemplate("slider_main_options");

?>

<script type="text/javascript">
	var g_jsonTaxWithCats = '{}';

	jQuery(document).ready(function(){
		RevSliderAdmin.initEditSliderView();
	});
</script>