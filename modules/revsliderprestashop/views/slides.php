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
$operations = new RevOperations();
$sliderID = RevSliderAdmin::getGetVar("id");
if (empty($sliderID)) {
    UniteFunctionsRev::throwError("Slider ID not found");
}
$slider = new RevSlider();
$slider->initByID($sliderID);
$sliderParams = $slider->getParams();
$arrSliders = $slider->getArrSlidersShort($sliderID);
$selectSliders = UniteFunctionsRev::getHTMLSelect($arrSliders, "", "id='selectSliders'", true);
$numSliders = count($arrSliders);
//set iframe parameters	
$width = $sliderParams["width"];
$height = $sliderParams["height"];
$iframeWidth = $width + 60;
$iframeHeight = $height + 50;
$iframeStyle = "width:" . $iframeWidth . "px;height:" . $iframeHeight . "px;";
$arrSlides = $slider->getSlides(false);
$numSlides = count($arrSlides);
$linksSliderSettings = RevSliderAdmin::getViewUrl(RevSliderAdmin::VIEW_SLIDER, "id=$sliderID");
$patternViewSlide = RevSliderAdmin::getViewUrl("slide", "id=[slideid]");

RevGlobalObject::setVar('arrSlides', $arrSlides);
RevGlobalObject::setVar('numSliders', $numSliders);
RevGlobalObject::setVar('numSlides', $numSlides);
RevGlobalObject::setVar('slider', $slider);
RevGlobalObject::setVar('operations', $operations);
RevGlobalObject::setVar('linksSliderSettings', $linksSliderSettings);
RevGlobalObject::setVar('selectSliders', $selectSliders);
RevGlobalObject::setVar('patternViewSlide', $patternViewSlide);
RevGlobalObject::setVar('sliderID', $sliderID);
RevGlobalObject::setVar('iframeStyle', $iframeStyle);

//treat in case of slides from gallery
if ($slider->isSlidesFromPosts() == false) {
    $templateName = "slides_gallery";
    $isWpmlExists = UniteWpmlRev::isWpmlExists();
    $useWpml = $slider->getParam("use_wpml", "off");
    $wpmlActive = false;
    if ($isWpmlExists && $useWpml == "on") {
        $wpmlActive = true;
        $urlIconDelete = RevSliderAdmin::$url_plugin . "views/img/images/icon-trash.png";
        $urlIconEdit = RevSliderAdmin::$url_plugin . "views/img/images/icon-edit.png";
        $urlIconPreview = RevSliderAdmin::$url_plugin . "views/img/images/preview.png";
        $textDelete = RevsliderPrestashop::$lang['Delete_Slide'];
        $textEdit = RevsliderPrestashop::$lang['Edit_Slide'];
        $textPreview = RevsliderPrestashop::$lang['Preview_Slide'];
        $htmlBefore = "";
        $htmlBefore .= "<li class='item_operation operation_delete'><a data-operation='delete' href='javascript:void(0)'>" . "\n";
        $htmlBefore .= "<img src='" . $urlIconDelete . "'/> " . $textDelete . "\n";
        $htmlBefore .= "</a></li>" . "\n";
        $htmlBefore .= "<li class='item_operation operation_edit'><a data-operation='edit' href='javascript:void(0)'>" . "\n";
        $htmlBefore .= "<img src='" . $urlIconEdit . "'/> " . $textEdit . "\n";
        $htmlBefore .= "</a></li>" . "\n";
        $htmlBefore .= "<li class='item_operation operation_preview'><a data-operation='preview' href='javascript:void(0)'>" . "\n";
        $htmlBefore .= "<img src='" . $urlIconPreview . "'/> " . $textPreview . "\n";
        $htmlBefore .= "</a></li>" . "\n";
        $htmlBefore .= "<li class='item_operation operation_sap'>" . "\n";
        $htmlBefore .= "<div class='float_menu_sap'></div>" . "\n";
        $htmlBefore .= "</a></li>" . "\n";
        $langFloatMenu = UniteWpmlRev::getLangsWithFlagsHtmlList("id='slides_langs_float' class='slides_langs_float'", $htmlBefore);
        RevGlobalObject::setVar('langFloatMenu', $langFloatMenu);
    }
    RevGlobalObject::setVar('wpmlActive', $wpmlActive);
} else {
    $templateName = "slides_posts";
    $sourceType = $slider->getParam("source_type", "posts");
    $showSortBy = ($sourceType == "posts") ? true : false;
    RevGlobalObject::setVar('showSortBy', $showSortBy);
    //get button links
    $urlNewPost = UniteFunctionsWPRev::getUrlNewPost();
    $linkNewPost = UniteFunctionsRev::getHtmlLink($urlNewPost, RevsliderPrestashop::$lang['New_Post'], "button_new_post", "button-primary revblue", true);
    RevGlobalObject::setVar('linkNewPost', $linkNewPost);
    //get ordering
    $arrSortBy = UniteFunctionsWPRev::getArrSortBy();
    $sortBy = $slider->getParam("post_sortby", RevSlider::DEFAULT_POST_SORTBY);
    $selectSortBy = UniteFunctionsRev::getHTMLSelect($arrSortBy, $sortBy, "id='select_sortby'", true);
    RevGlobalObject::setVar('selectSortBy', $selectSortBy);
}

require RevSliderAdmin::getPathTemplate($templateName);

RevGlobalObject::reset();
// @codingStandardsIgnoreEnd