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

$slider = new RevSlider();
$arrSliders = $slider->getArrSliders();
$arrSlidersTemplates = $slider->getArrSliders();
$addNewLink = self::getViewUrl(RevSliderAdmin::VIEW_SLIDER);
$addNewTemplateLink = self::getViewUrl(RevSliderAdmin::VIEW_SLIDER_TEMPLATE);

RevGlobalObject::setVar('arrSliders', $arrSliders);
RevGlobalObject::setVar('arrSlidersTemplates', $arrSlidersTemplates);
RevGlobalObject::setVar('addNewLink', $addNewLink);
RevGlobalObject::setVar('addNewTemplateLink', $addNewTemplateLink);


require self::getPathTemplate("sliders");
require self::getPathTemplate('template-slider-selector');
require self::getPathTemplate('sliders-dashboard');


RevGlobalObject::reset();
