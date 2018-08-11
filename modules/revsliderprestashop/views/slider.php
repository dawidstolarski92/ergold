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
$sliderTemplate = false;

RevGlobalObject::setVar('sliderTemplate', $sliderTemplate);

$settingsMain = self::getSettings("slider_main");

$settingsParams = self::getSettings("slider_params");



$settingsSliderMain = new RevSliderSettingsProduct();

$settingsSliderParams = new UniteSettingsProductSidebarRev();



//get taxonomies with cats


$productprds = UniteFunctionsWPRev::getPrestaProdCat();

//$postTypesWithCats = RevOperations::getPostTypesWithCatsForClient();	
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

    $settingsMain = RevSliderSettingsProduct::setSettingsCustomValues($settingsMain, $arrFieldsParams);



    //set setting values from the slider

    $settingsMain->setStoredValues($arrFieldsParams);



    $settingsParams->setStoredValues($arrFieldsParams);



    //update short code setting
    //$shortcode = $slider->getShortcode();
    //$settingsMain->updateSettingValue("shortcode",$shortcode);

    $isFromStream = $slider->isSlidesFromStream();

    $slider_type = 'gallery';

    if ($isFromStream !== false) {
        $strSource = RevsliderPrestashop::$lang['Social'];
        $preicon = "revicon-doc";
        $rowClass = "class='row_alt'";

        switch ($isFromStream) {
            case 'facebook':
                $strSource = RevsliderPrestashop::$lang['Facebook'];
                $preicon = "eg-icon-facebook";
                $numReal = $slider->getNumRealSlides(false, 'facebook');
                $slider_type = 'facebook';
                break;
            case 'twitter':
                $strSource = RevsliderPrestashop::$lang['Twitter'];
                $preicon = "eg-icon-twitter";
                $numReal = $slider->getNumRealSlides(false, 'twitter');
                $slider_type = 'twitter';
                break;
            case 'instagram':
                $strSource = RevsliderPrestashop::$lang['Instagram'];
                $preicon = "eg-icon-info";
                $numReal = $slider->getNumRealSlides(false, 'instagram');
                $slider_type = 'instagram';
                break;
            case 'flickr':
                $strSource = RevsliderPrestashop::$lang['Flickr'];
                $preicon = "eg-icon-flickr";
                $numReal = $slider->getNumRealSlides(false, 'flickr');
                $slider_type = 'flickr';
                break;
            case 'youtube':
                $strSource = RevsliderPrestashop::$lang['YouTube'];
                $preicon = "eg-icon-youtube";
                $numReal = $slider->getNumRealSlides(false, 'youtube');
                $slider_type = 'youtube';
                break;
            case 'vimeo':
                $strSource = RevsliderPrestashop::$lang['Vimeo'];
                $preicon = "eg-icon-vimeo";
                $numReal = $slider->getNumRealSlides(false, 'vimeo');
                $slider_type = 'vimeo';
                break;
        }
    }

    $numSlides = $slider->getNumSlides();

    if ((int) ($numSlides) == 0) {
        $first_slide_id = 'new&slider=' . $sliderID;
    } else {
        $slides = $slider->getSlides(false);

        if (!empty($slides)) {
            $first_slide_id = $slides[key($slides)]->getID();

            $first_slide_image_thumb = $slides[key($slides)]->getImageAttributes($slider_type);
        } else {
            $first_slide_id = 'new&slider=' . $sliderID;
        }
    }

    $linksEditSlides = self::getViewUrl(RevSliderAdmin::VIEW_SLIDE, "id=$first_slide_id");

    RevGlobalObject::setVar('linksEditSlides', $linksEditSlides);
    RevGlobalObject::setVar('arrFieldsParams', $arrFieldsParams);
    RevGlobalObject::setVar('slider', $slider);

    $settingsSliderParams->init($settingsParams);

    $settingsSliderMain->init($settingsMain);



    $settingsSliderParams->isAccordion(true);



    require self::getPathTemplate("slider_edit");
} else {

    //set custom type params values:
    //$settingsMain = RevSliderSettingsProduct::setSettingsCustomValues($settingsMain, array(), $postTypesWithCats);

    $settingsMain = RevSliderSettingsProduct::setSettingsCustomValues($settingsMain, array());



    $settingsSliderParams->init($settingsParams);

    $settingsSliderMain->init($settingsMain);



    $settingsSliderParams->isAccordion(true);



    require self::getPathTemplate("slider_new");
}
// @codingStandardsIgnoreEnd