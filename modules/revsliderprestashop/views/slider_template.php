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

// @codingStandardsIgnoreStart
$sliderTemplate = true;



$settingsMain = self::getSettings("slider_main");

$settingsParams = self::getSettings("slider_params");



$settingsSliderMain = new RevSliderSettingsProduct();

$settingsSliderParams = new UniteSettingsProductSidebarRev();



//get taxonomies with cats
//$postTypesWithCats = RevOperations::getPostTypesWithCatsForClient();
$productprds = UniteFunctionsWPRev::getPrestaProdCat();

$postTypesWithCats = array();
$postTypesWithCats['product'] = $productprds;



$jsonTaxWithCats = UniteFunctionsRev::jsonEncodeForClientSide($postTypesWithCats);


//check existing slider data:

$sliderID = self::getGetVar("id");



if (!empty($sliderID)) {
    $slider = new RevSlider();

    $slider->initByID($sliderID);



    //get setting fields

    $settingsFields = $slider->getSettingsFields();

    $arrFieldsMain = $settingsFields["main"];

    $arrFieldsParams = $settingsFields["params"];



    //modify arrows type for backword compatability

    $arrowsType = UniteFunctionsRev::getVal($arrFieldsParams, "navigation_arrows");

    switch ($arrowsType) {

        case "verticalcentered":

            $arrFieldsParams["navigation_arrows"] = "solo";

            break;
    }



    //set custom type params values:

    $settingsMain = RevSliderSettingsProduct::setSettingsCustomValues($settingsMain, $arrFieldsParams, $postTypesWithCats);



    //set setting values from the slider

    $settingsMain->setStoredValues($arrFieldsParams);



    $settingsParams->setStoredValues($arrFieldsParams);



    //update short code setting
    // $shortcode = $slider->getShortcode();
    // $settingsMain->updateSettingValue("shortcode",$shortcode);



    $linksEditSlides = self::getViewUrl(RevSliderAdmin::VIEW_SLIDES, "id=$sliderID");



    $settingsSliderParams->init($settingsParams);

    $settingsSliderMain->init($settingsMain);



    $settingsSliderParams->isAccordion(true);



    require self::getPathTemplate("slider_edit");
} else {

    //set custom type params values:

    $settingsMain = RevSliderSettingsProduct::setSettingsCustomValues($settingsMain, array(), $postTypesWithCats);



    $settingsSliderParams->init($settingsParams);

    $settingsSliderMain->init($settingsMain);



    $settingsSliderParams->isAccordion(true);



    require self::getPathTemplate("slider_new");
}
// @codingStandardsIgnoreEnd