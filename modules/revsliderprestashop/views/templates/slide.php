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
if (!defined('ABSPATH')) {
    exit();
}

//get input
$slideID = RevSliderFunctions::getGetVar("id");

if ($slideID == 'new') { //add new transparent slide
    $sID = (int)(RevSliderFunctions::getGetVar("slider"));
    if ($sID > 0) {
        $revs = new RevSlider();
        $revs->initByID($sID);
        //check if we already have slides, if yes, go to first
        $arrS = $revs->getSlides(false);
        if (empty($arrS)) {
            $slideID = $revs->createSlideFromData(array('sliderid'=>$sID), true);
        } else {
            $slideID = key($arrS);
        }
    }
}

$patternViewSlide = self::getViewUrl("slide", "id=[slideid]");

//init slide object
$slide = new RevSlide();
$slide->initByID($slideID);

$slideParams = $slide->getParams();

$operations = new RevSliderOperations();

//init slider object
$sliderID = $slide->getSliderID();
$slider = new RevSlider();
$slider->initByID($sliderID);
$sliderParams = $slider->getParams();
$arrSlideNames = $slider->getArrSlideNames();

$arrSlides = $slider->getSlides(false);
$arrSlidesWPML = $slider->getSlidesWPML(false, $slide);

$arrSliders = $slider->getArrSlidersShort($sliderID);
$selectSliders = RevSliderFunctions::getHTMLSelect($arrSliders, "", "id='selectSliders'", true);

RevGlobalObject::setVar('slideParams', $slideParams);
RevGlobalObject::setVar('slider', $slider);
RevGlobalObject::setVar('slide', $slide);
RevGlobalObject::setVar('selectSliders', $selectSliders);
RevGlobalObject::setVar('slideID', $slideID);
RevGlobalObject::setVar('operations', $operations);
RevGlobalObject::setVar('arrSlideNames', $arrSlideNames);
RevGlobalObject::setVar('arrSlides', $arrSlides);

//check if slider is template
$sliderTemplate = $slider->getParam("template", "false");

//set slide delay
$sliderDelay = $slider->getParam("delay", "9000");
$slideDelay = $slide->getParam("delay", "");
if (empty($slideDelay)) {
    $slideDelay = $sliderDelay;
}

//add tools.min.js
wp_enqueue_script('tp-tools', _MODULE_DIR_ .'public/assets/js/jquery.themepunch.tools.min.js', array(), RevSliderGlobals::SLIDER_REVISION);

$arrLayers = $slide->getLayers();

//set Layer settings
$cssContent = $operations->getCaptionsContent();

$arrCaptionClasses = $operations->getArrCaptionClasses($cssContent);
//$arrCaptionClassesSorted = $operations->getArrCaptionClasses($cssContent);
$arrCaptionClassesSorted = RevSliderCssParser::getCaptionsSorted();

$arrFontFamily = $operations->getArrFontFamilys($slider);
$arrCSS = $operations->getCaptionsContentArray();
$arrButtonClasses = $operations->getButtonClasses();


$arrAnim = $operations->getFullCustomAnimations();
$arrAnimDefaultIn = $operations->getArrAnimations(false);
$arrAnimDefaultOut = $operations->getArrEndAnimations(false);

$arrAnimDefault = array_merge($arrAnimDefaultIn, $arrAnimDefaultOut);

//set various parameters needed for the page
$width = $sliderParams["width"];
$height = $sliderParams["height"];
$imageUrl = $slide->getImageUrl();
$imageID = $slide->getImageID();
RevGlobalObject::setVar('imageID', $imageID);

$slider_type = $slider->getParam('source_type', 'gallery');

RevGlobalObject::setVar('slider_type', $slider_type);

/**
 * Get Slider params which will be used as default on Slides
 * @since: 5.0
 **/
$def_background_fit = $slider->getParam('def-background_fit', 'cover');
$def_image_source_type = $slider->getParam('def-image_source_type', 'full');
$def_bg_fit_x = $slider->getParam('def-bg_fit_x', '100');
$def_bg_fit_y = $slider->getParam('def-bg_fit_y', '100');
$def_bg_position = $slider->getParam('def-bg_position', 'center center');
$def_bg_position_x = $slider->getParam('def-bg_position_x', '0');
$def_bg_position_y = $slider->getParam('def-bg_position_y', '0');
$def_bg_repeat = $slider->getParam('def-bg_repeat', 'no-repeat');
$def_kenburn_effect = $slider->getParam('def-kenburn_effect', 'off');
$def_kb_start_fit = $slider->getParam('def-kb_start_fit', '100');
$def_kb_easing = $slider->getParam('def-kb_easing', 'Linear.easeNone');
$def_kb_end_fit = $slider->getParam('def-kb_end_fit', '100');
$def_kb_duration = $slider->getParam('def-kb_duration', '10000');
$def_transition = $slider->getParam('def-slide_transition', 'fade');
RevGlobalObject::setVar('def_transition', $def_transition);
$def_transition_duration = $slider->getParam('def-transition_duration', 'default');
RevGlobalObject::setVar('def_transition_duration', $def_transition_duration);
$def_use_parallax = $slider->getParam('use_parallax', 'on');

/* NEW KEN BURN INPUTS */
$def_kb_start_offset_x = $slider->getParam('def-kb_start_offset_x', '0');
$def_kb_start_offset_y = $slider->getParam('def-kb_start_offset_y', '0');
$def_kb_end_offset_x = $slider->getParam('def-kb_end_offset_x', '0');
$def_kb_end_offset_y = $slider->getParam('def-kb_end_offset_y', '0');
$def_kb_start_rotate = $slider->getParam('def-kb_start_rotate', '0');
$def_kb_end_rotate = $slider->getParam('def-kb_end_rotate', '0');
/* END OF NEW KEN BURN INPUTS */

$imageFilename = $slide->getImageFilename();

$style = "height:".$height."px;"; //

$divLayersWidth = "width:".$width."px;";
$divbgminwidth = "min-width:".$width."px;";
$maxbgwidth = "max-width:".$width."px;";

RevGlobalObject::setVar('divbgminwidth', $divbgminwidth);
RevGlobalObject::setVar('maxbgwidth', $maxbgwidth);
RevGlobalObject::setVar('divLayersWidth', $divLayersWidth);

//set iframe parameters
$iframeWidth = $width+60;
$iframeHeight = $height+50;

$iframeStyle = "width:".$iframeWidth."px;height:".$iframeHeight."px;";

$closeUrl = self::getViewUrl(RevSliderAdmin::VIEW_SLIDES, "id=".$sliderID);

$jsonLayers = RevSliderFunctions::jsonEncodeForClientSide($arrLayers);
$jsonFontFamilys = RevSliderFunctions::jsonEncodeForClientSide($arrFontFamily);
$jsonCaptions = RevSliderFunctions::jsonEncodeForClientSide($arrCaptionClassesSorted);

$arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);

$arrCustomAnim = RevSliderFunctions::jsonEncodeForClientSide($arrAnim);
$arrCustomAnimDefault = RevSliderFunctions::jsonEncodeForClientSide($arrAnimDefault);

//bg type params
$bgType = RevSliderFunctions::getVal($slideParams, 'background_type', 'image');
RevGlobalObject::setVar('bgType', $bgType);

$slideBGColor = RevSliderFunctions::getVal($slideParams, 'slide_bg_color', '#E7E7E7');
RevGlobalObject::setVar('slideBGColor', $slideBGColor);
$divLayersClass = "slide_layers";

$meta_handle = RevSliderFunctions::getVal($slideParams, 'meta_handle', '');

$bgFit = RevSliderFunctions::getVal($slideParams, 'bg_fit', $def_background_fit);
RevGlobalObject::setVar('bgFit', $bgFit);
$bgFitX = (int)(RevSliderFunctions::getVal($slideParams, 'bg_fit_x', $def_bg_fit_x));
RevGlobalObject::setVar('bgFitX', $bgFitX);
$bgFitY = (int)(RevSliderFunctions::getVal($slideParams, 'bg_fit_y', $def_bg_fit_y));
RevGlobalObject::setVar('bgFitY', $bgFitY);

$bgPosition = RevSliderFunctions::getVal($slideParams, 'bg_position', $def_bg_position);
RevGlobalObject::setVar('bgPosition', $bgPosition);
$bgPositionX = (int)(RevSliderFunctions::getVal($slideParams, 'bg_position_x', $def_bg_position_x));
RevGlobalObject::setVar('bgPositionX', $bgPositionX);
$bgPositionY = (int)(RevSliderFunctions::getVal($slideParams, 'bg_position_y', $def_bg_position_y));
RevGlobalObject::setVar('bgPositionY', $bgPositionY);

$slide_parallax_level = RevSliderFunctions::getVal($slideParams, 'slide_parallax_level', '-');
RevGlobalObject::setVar('slide_parallax_level', $slide_parallax_level);
$kenburn_effect = RevSliderFunctions::getVal($slideParams, 'kenburn_effect', $def_kenburn_effect);
RevGlobalObject::setVar('kenburn_effect', $kenburn_effect);
$kb_duration = RevSliderFunctions::getVal($slideParams, 'kb_duration', $def_kb_duration);
RevGlobalObject::setVar('kb_duration', $kb_duration);
$kb_easing = RevSliderFunctions::getVal($slideParams, 'kb_easing', $def_kb_easing);
RevGlobalObject::setVar('kb_easing', $kb_easing);
$kb_start_fit = RevSliderFunctions::getVal($slideParams, 'kb_start_fit', $def_kb_start_fit);
RevGlobalObject::setVar('kb_start_fit', $kb_start_fit);
$kb_end_fit = RevSliderFunctions::getVal($slideParams, 'kb_end_fit', $def_kb_end_fit);
RevGlobalObject::setVar('kb_end_fit', $kb_end_fit);

$ext_width = RevSliderFunctions::getVal($slideParams, 'ext_width', '1920');
RevGlobalObject::setVar('ext_width', $ext_width);
$ext_height = RevSliderFunctions::getVal($slideParams, 'ext_height', '1080');
RevGlobalObject::setVar('ext_height', $ext_height);

$use_parallax = RevSliderFunctions::getVal($slideParams, 'use_parallax', $def_use_parallax);

RevGlobalObject::setVar('use_parallax', $use_parallax);
$parallax_level = array();
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_1", "5");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_2", "10");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_3", "15");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_4", "20");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_5", "25");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_6", "30");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_7", "35");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_8", "40");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_9", "45");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_10", "45");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_11", "46");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_12", "47");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_13", "48");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_14", "49");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_15", "50");
$parallax_level[] =  RevSliderFunctions::getVal($sliderParams, "parallax_level_16", "55");
RevGlobalObject::setVar('parallax_level', $parallax_level);
$parallaxisddd = RevSliderFunctions::getVal($sliderParams, "ddd_parallax", "off");
RevGlobalObject::setVar('parallaxisddd', $parallaxisddd);
$parallaxbgfreeze = RevSliderFunctions::getVal($sliderParams, "ddd_parallax_bgfreeze", "off");
RevGlobalObject::setVar('parallaxbgfreeze', $parallaxbgfreeze);

$slideBGYoutube = RevSliderFunctions::getVal($slideParams, 'slide_bg_youtube', '');
$slideBGVimeo = RevSliderFunctions::getVal($slideParams, 'slide_bg_vimeo', '');
$slideBGhtmlmpeg = RevSliderFunctions::getVal($slideParams, 'slide_bg_html_mpeg', '');
$slideBGhtmlwebm = RevSliderFunctions::getVal($slideParams, 'slide_bg_html_webm', '');
$slideBGhtmlogv = RevSliderFunctions::getVal($slideParams, 'slide_bg_html_ogv', '');

RevGlobalObject::setVar('slideBGYoutube', $slideBGYoutube);
RevGlobalObject::setVar('slideBGVimeo', $slideBGVimeo);
RevGlobalObject::setVar('slideBGhtmlmpeg', $slideBGhtmlmpeg);
RevGlobalObject::setVar('slideBGhtmlwebm', $slideBGhtmlwebm);
RevGlobalObject::setVar('slideBGhtmlogv', $slideBGhtmlogv);


$stream_do_cover = RevSliderFunctions::getVal($slideParams, 'stream_do_cover', 'on');
RevGlobalObject::setVar('stream_do_cover', $stream_do_cover);

$stream_do_cover_both = RevSliderFunctions::getVal($slideParams, 'stream_do_cover_both', 'on');
RevGlobalObject::setVar('stream_do_cover_both', $stream_do_cover_both);


$video_force_cover = RevSliderFunctions::getVal($slideParams, 'video_force_cover', 'on');
RevGlobalObject::setVar('video_force_cover', $video_force_cover);
$video_dotted_overlay = RevSliderFunctions::getVal($slideParams, 'video_dotted_overlay', 'none');
RevGlobalObject::setVar('video_dotted_overlay', $video_dotted_overlay);
$video_ratio = RevSliderFunctions::getVal($slideParams, 'video_ratio', 'none');
RevGlobalObject::setVar('video_ratio', $video_ratio);
$video_loop = RevSliderFunctions::getVal($slideParams, 'video_loop', 'none');
RevGlobalObject::setVar('video_loop', $video_loop);
$video_nextslide = RevSliderFunctions::getVal($slideParams, 'video_nextslide', 'off');
RevGlobalObject::setVar('video_nextslide', $video_nextslide);
$video_allowfullscreen = RevSliderFunctions::getVal($slideParams, 'video_allowfullscreen', 'on');
RevGlobalObject::setVar('video_allowfullscreen', $video_allowfullscreen);
$video_force_rewind = RevSliderFunctions::getVal($slideParams, 'video_force_rewind', 'on');
RevGlobalObject::setVar('video_force_rewind', $video_force_rewind);
$video_speed = RevSliderFunctions::getVal($slideParams, 'video_speed', '1');
RevGlobalObject::setVar('video_speed', $video_speed);
$video_mute = RevSliderFunctions::getVal($slideParams, 'video_mute', 'on');
RevGlobalObject::setVar('video_mute', $video_mute);
$video_volume = RevSliderFunctions::getVal($slideParams, 'video_volume', '100');
RevGlobalObject::setVar('video_volume', $video_volume);
$video_start_at = RevSliderFunctions::getVal($slideParams, 'video_start_at', '');
RevGlobalObject::setVar('video_start_at', $video_start_at);
$video_end_at = RevSliderFunctions::getVal($slideParams, 'video_end_at', '');
RevGlobalObject::setVar('video_end_at', $video_end_at);
$video_arguments = RevSliderFunctions::getVal($slideParams, 'video_arguments', RevSliderGlobals::DEFAULT_YOUTUBE_ARGUMENTS);
RevGlobalObject::setVar('video_arguments', $video_arguments);
$video_arguments_vim = RevSliderFunctions::getVal($slideParams, 'video_arguments_vimeo', RevSliderGlobals::DEFAULT_VIMEO_ARGUMENTS);
RevGlobalObject::setVar('video_arguments_vim', $video_arguments_vim);


/* NEW KEN BURN INPUTS */
$kbStartOffsetX = (int)(RevSliderFunctions::getVal($slideParams, 'kb_start_offset_x', $def_kb_start_offset_x));
RevGlobalObject::setVar('kbStartOffsetX', $kbStartOffsetX);
$kbStartOffsetY = (int)(RevSliderFunctions::getVal($slideParams, 'kb_start_offset_y', $def_kb_start_offset_y));
RevGlobalObject::setVar('kbStartOffsetY', $kbStartOffsetY);
$kbEndOffsetX = (int)(RevSliderFunctions::getVal($slideParams, 'kb_end_offset_x', $def_kb_end_offset_x));
RevGlobalObject::setVar('kbEndOffsetX', $kbEndOffsetX);
$kbEndOffsetY = (int)(RevSliderFunctions::getVal($slideParams, 'kb_end_offset_y', $def_kb_end_offset_y));
RevGlobalObject::setVar('kbEndOffsetY', $kbEndOffsetY);
$kbStartRotate = (int)(RevSliderFunctions::getVal($slideParams, 'kb_start_rotate', $def_kb_start_rotate));
RevGlobalObject::setVar('kbStartRotate', $kbStartRotate);
$kbEndRotate = (int)(RevSliderFunctions::getVal($slideParams, 'kb_end_rotate', $def_kb_start_rotate));
RevGlobalObject::setVar('kbEndRotate', $kbEndRotate);
/* END OF NEW KEN BURN INPUTS*/

$bgRepeat = RevSliderFunctions::getVal($slideParams, 'bg_repeat', $def_bg_repeat);
RevGlobalObject::setVar('bgRepeat', $bgRepeat);

$slideBGExternal = RevSliderFunctions::getVal($slideParams, "slide_bg_external", "");
RevGlobalObject::setVar('slideBGExternal', $slideBGExternal);

$img_sizes = RevSliderBase::getAllImageSizes($slider_type);
RevGlobalObject::setVar('img_sizes', $img_sizes);

$bg_image_size = RevSliderFunctions::getVal($slideParams, 'image_source_type', $def_image_source_type);
RevGlobalObject::setVar('bg_image_size', $bg_image_size);

$style_wrapper = '';
$class_wrapper = '';


switch ($bgType) {
    case "trans":
        $divLayersClass = "slide_layers";
        $class_wrapper = "trans_bg";
    break;
    case "solid":
        $style_wrapper .= "background-color:".$slideBGColor.";";
    break;
    case "image":
        switch ($slider_type) {
            case 'posts':
                $imageUrl = _MODULE_DIR_.'/revsliderprestashop/views/img/images/sources/post.png';
            break;
            case 'woocommerce':
                $imageUrl = _MODULE_DIR_.'/revsliderprestashop/views/img/images/sources/wc.png';
            break;
            case 'facebook':
                $imageUrl = _MODULE_DIR_.'/revsliderprestashop/views/img/images/sources/fb.png';
            break;
            case 'twitter':
                $imageUrl = _MODULE_DIR_.'/revsliderprestashop/views/img/images/sources/tw.png';
            break;
            case 'instagram':
                $imageUrl = _MODULE_DIR_.'/revsliderprestashop/views/img/images/sources/ig.png';
            break;
            case 'flickr':
                $imageUrl = _MODULE_DIR_.'/revsliderprestashop/views/img/images/sources/fr.png';
            break;
            case 'youtube':
                $imageUrl = _MODULE_DIR_.'/revsliderprestashop/views/img/images/sources/yt.png';
            break;
            case 'vimeo':
                $imageUrl = _MODULE_DIR_.'/revsliderprestashop/views/img/images/sources/vm.png';
            break;
        }
        $style_wrapper .= "background-image:url('".$imageUrl."');";
        if ($bgFit == 'percentage') {
            $style_wrapper .= "background-size: ".$bgFitX.'% '.$bgFitY.'%;';
        } else {
            $style_wrapper .= "background-size: ".$bgFit.";";
        }
        if ($bgPosition == 'percentage') {
            $style_wrapper .= "background-position: ".$bgPositionX.'% '.$bgPositionY.'%;';
        } else {
            $style_wrapper .= "background-position: ".$bgPosition.";";
        }
        $style_wrapper .= "background-repeat: ".$bgRepeat.";";
    break;
    case "external":
        $style_wrapper .= "background-image:url('".$slideBGExternal."');";
        if ($bgFit == 'percentage') {
            $style_wrapper .= "background-size: ".$bgFitX.'% '.$bgFitY.'%;';
        } else {
            $style_wrapper .= "background-size: ".$bgFit.";";
        }
        if ($bgPosition == 'percentage') {
            $style_wrapper .= "background-position: ".$bgPositionX.'% '.$bgPositionY.'%;';
        } else {
            $style_wrapper .= "background-position: ".$bgPosition.";";
        }
        $style_wrapper .= "background-repeat: ".$bgRepeat.";";
    break;
}

RevGlobalObject::setVar('imageUrl', $imageUrl);
RevGlobalObject::setVar('class_wrapper', $class_wrapper);
RevGlobalObject::setVar('style_wrapper', $style_wrapper);
RevGlobalObject::setVar('divLayersClass', $divLayersClass);

$slideTitle = $slide->getParam("title", "Slide");
$slideOrder = $slide->getOrder();

?>

<script type="text/javascript" src="<?php echo _MODULE_DIR_.'revsliderprestashop/views/js/js/webfontsload.min.js'?>"></script>
<script type="text/javascript">

	var sgfamilies = [];
	<?php
    //<!--  load good font -->
    $operations = new RevSliderOperations();
        RevGlobalObject::setVar('operations', $operations);
    $googleFont = $slider->getParam("google_font", array());
    if (!empty($googleFont)) {
        if (is_array($googleFont)) {
            $fontsstr = '';
//                        $fontsubsets = '&subset=';
                        $count = 0;
            foreach ($googleFont as $key => $font) {
                preg_match('/family=([^\\\']+)/', $font, $match);
                if (@RevsliderPrestashop::getIsset($match[1]) && !empty($match[1])) {
                    $fontsstr .= (Tools::substr($match[1], Tools::strlen($match[1]) - 1) == '\\') ? Tools::substr($match[1], 0, -1) : $match[1];
                    $font = str_replace('&subset=', ':', $fontsstr);
                }
                ?> sgfamilies.push('<?php  echo($font);
                ?>');<?php
                            $count++;
            }
        } else {
            ?>sgfamilies.push('<?php echo str_replace('&subset=', ':', $googleFont);
            ?>');<?php

        }
    }

    //add here all new google fonts of the layers, with full variants and subsets
    $gfsubsets = $slider->getParam("subsets", array());
    $gf = $slider->getUsedFonts(true);
        
        $tempgfonts = '';
    $counts = 0;
        
        foreach ($gf as $gfk => $gfv) {
            $tcf = $gfk.':';
                
            if (!empty($gfv['variants'])) {
                $mgfirst = true;
                foreach ($gfv['variants'] as $mgvk => $mgvv) {
                    if (!$mgfirst) {
                        $tcf .= ',';
                    }
                    $tcf .= $mgvk;
                    $mgfirst = false;
                }
            }
        
            if (!empty($gfv['subsets'])) {
                $mgfirst = true;
                foreach ($gfv['subsets'] as $ssk => $ssv) {
                    //				if($mgfirst) $tcf .= '&subset=';
                if ($mgfirst) {
                    $tcf .= ':';
                }
                    if (!$mgfirst) {
                        $tcf .= ',';
                    }
                    $tcf .= $ssv;

                    $mgfirst = false;
                }
            }

            ?>
                    sgfamilies.push('<?php  echo $tcf;
            ?>');<?php

        }
    ?>
	var callAllIdle_LocalTimeOut;
	function fontLoaderWaitForTextLayers() {		
		if (jQuery('.slide_layer_type_text').length>0) {			
			tpLayerTimelinesRev.allLayerToIdle({type:"text"});
			clearTimeout(callAllIdle_LocalTimeOut);
			callAllIdle_LocalTimeOut = setTimeout(function() {				
				tpLayerTimelinesRev.allLayerToIdle({type:"text"});
			},1250);
		}
		else
			setTimeout(fontLoaderWaitForTextLayers,250);
	}
        
	if (sgfamilies.length)
		tpWebFont.load({
			timeout:10000,
			google:{
				families:sgfamilies
			},
			loading:function() {				
			},
			active:function() {						
				fontLoaderWaitForTextLayers();								
			},
			inactive:function() {														
				fontLoaderWaitForTextLayers();								
			},
		});
   
</script>

<?php

RevGlobalObject::setVar('slide', $slide);

if ($slide->isStaticSlide() || $slider->isSlidesFromPosts()) { //insert sliderid for preview
    ?><input type="hidden" id="sliderid" value="<?php echo $slider->getID();
    ?>" /><?php

}

require self::getPathTemplate('template-selector');

?>

<div class="wrap settings_wrap">
	<div class="clear_both"></div>

	<div class="title_line" style="margin-bottom:0px !important;">
		<div id="icon-options-general" class="icon32"></div>		
		<a href="<?php echo RevSliderGlobals::LINK_HELP_SLIDE; ?>" class="button-primary float_right revblue mtop_10 mleft_10" target="_blank"><?php _e("Help", 'revslider'); ?></a>

	</div>

	<div class="rs_breadcrumbs">
		<a class='breadcrumb-button' href='<?php echo self::getViewUrl("sliders");?>'><i class="eg-icon-th-large"></i><?php _e("All Sliders", 'revslider');?></a>
		<a class='breadcrumb-button' href="<?php echo self::getViewUrl(RevSliderAdmin::VIEW_SLIDER, "id=$sliderID"); ?>"><i class="eg-icon-cog"></i><?php _e('Slider Settings', 'revslider');?></a>
		<a class='breadcrumb-button selected' href="#"><i class="eg-icon-pencil-2"></i><?php _e('Slide Editor ', 'revslider');?>"<?php echo ' '.$slider->getParam("title", ""); ?>"</a>
		<div class="tp-clearfix"></div>


		<!-- FIXED TOOLBAR ON THE RIGHT SIDE -->
		<div class="rs-mini-toolbar">
			<?php
            if (!$slide->isStaticSlide()) {
                $savebtnid="button_save_slide-tb";
                $prevbtn = "button_preview_slide-tb";
                if ($slider->isSlidesFromPosts()) {
                    $prevbtn = "button_preview_slider-tb";
                }
            } else {
                $savebtnid="button_save_static_slide-tb";
                $prevbtn = "button_preview_slider-tb";
            }
            ?>
			<div class="rs-toolbar-savebtn rs-mini-toolbar-button">
				<a class='button-primary revgreen' href='javascript:void(0)' id="<?php echo $savebtnid; ?>" ><i class="rs-icon-save-light" style="display: inline-block;vertical-align: middle;width: 18px;height: 20px;background-repeat: no-repeat;"></i><span class="mini-toolbar-text"><?php _e("Save Slide", 'revslider'); ?></span></a>
			</div>
			
			<div class="rs-toolbar-cssbtn rs-mini-toolbar-button">
				<a class='button-primary revpurple' href='javascript:void(0)' id='button_edit_css_global'><i class="">&lt;/&gt;</i><span class="mini-toolbar-text"><?php _e("CSS Global", 'revslider'); ?></span></a>
			</div>


			<div class="rs-toolbar-slides rs-mini-toolbar-button">
				<?php
                $slider_url = ($sliderTemplate == 'true') ? RevSliderAdmin::VIEW_SLIDER_TEMPLATE : RevSliderAdmin::VIEW_SLIDER;
                ?>
				<a class="button-primary revblue" href="<?php echo self::getViewUrl($slider_url, "id=$sliderID"); ?>" id="link_edit_slides_t"><i class="revicon-cog"></i><span class="mini-toolbar-text"><?php _e("Slider Settings", 'revslider'); ?></span> </a>
				
			</div>
			<div class="rs-toolbar-preview rs-mini-toolbar-button">
				<a class="button-primary revgray" href="javascript:void(0)"  id="<?php echo $prevbtn; ?>" ><i class="revicon-search-1"></i><span class="mini-toolbar-text"><?php _e("Preview", 'revslider'); ?></span></a>
			</div>
			
		</div>
	</div>

	<script>
		jQuery(document).ready(function() {			
			jQuery('.rs-mini-toolbar-button').hover(function() {				
				var btn=jQuery(this),
					txt = btn.find('.mini-toolbar-text');
				punchgs.TweenLite.to(txt,0.2,{width:"100px",ease:punchgs.Linear.easeNone,overwrite:"all"});
				punchgs.TweenLite.to(txt,0.1,{autoAlpha:1,ease:punchgs.Linear.easeNone,delay:0.1,overwrite:"opacity"});
			}, function() {
				var btn=jQuery(this),
					txt = btn.find('.mini-toolbar-text');
				punchgs.TweenLite.to(txt,0.2,{autoAlpha:0,width:"0px",ease:punchgs.Linear.easeNone,overwrite:"all"});				
			});
			var mtb = jQuery('.rs-mini-toolbar'),
				mtbo = mtb.offset().top;
			jQuery(document).on("scroll",function() {
				
				if (mtbo-jQuery(window).scrollTop()<100) 
					mtb.addClass("sticky");
				else
					mtb.removeClass("sticky");
				
			})
		});
	</script>

	<?php

        
    require self::getPathTemplate("slide-selector");
        
        $useWpml = RevGlobalObject::getVar('useWpml');
        $wpmlActive = RevGlobalObject::getVar('wpmlActive');
        
        if ($wpmlActive == true && $useWpml == 'on') {
            require self::getPathTemplate('wpml-selector');
        }
        
    if (!$slide->isStaticSlide()) {
        require self::getPathTemplate('slide-general-settings');
    }

    $operations = new RevSliderOperations();
        RevGlobalObject::setVar('operations', $operations);
    $settings = $slide->getSettings();
    RevGlobalObject::setVar('settings', $settings);
    $enable_custom_size_notebook = $slider->getParam('enable_custom_size_notebook', 'off');
    $enable_custom_size_tablet = $slider->getParam('enable_custom_size_tablet', 'off');
    $enable_custom_size_iphone = $slider->getParam('enable_custom_size_iphone', 'off');
        
        RevGlobalObject::setVar('enable_custom_size_notebook', $enable_custom_size_notebook);
        RevGlobalObject::setVar('enable_custom_size_tablet', $enable_custom_size_tablet);
        RevGlobalObject::setVar('enable_custom_size_iphone', $enable_custom_size_iphone);
        
    $adv_resp_sizes = ($enable_custom_size_notebook == 'on' || $enable_custom_size_tablet == 'on' || $enable_custom_size_iphone == 'on') ? true : false;
        RevGlobalObject::setVar('adv_resp_sizes', $adv_resp_sizes);
    ?>

	<div id="jqueryui_error_message" class="unite_error_message" style="display:none;">
		<?php _e("<b>Warning!!! </b>The jquery ui javascript include that is loaded by some of the plugins are custom made and not contain needed components like 'autocomplete' or 'draggable' function.
		Without those functions the editor may not work correctly. Please remove those custom jquery ui includes in order the editor will work correctly.", 'revslider'); ?>
	</div>

	<div class="edit_slide_wrapper<?php echo ($slide->isStaticSlide()) ? ' rev_static_layers' : ''; ?>">
		<?php
                RevGlobalObject::setVar('style', $style);
        require self::getPathTemplate('slide-stage');
        ?>
		<div style="width:100%;clear:both;height:20px"></div>

		<div id="dialog_insert_icon" class="dialog_insert_icon" title="Insert Icon" style="display:none;"></div>

		<div id="dialog_template_insert" class="dialog_template_help" title="<?php _e('Insert Meta', 'revslider') ?>" style="display:none;">

			<div style="clear: both;"></div>
			<?php
            switch ($slider_type) {
                case 'posts':
                case 'specific_posts':
                case 'woocommerce':
                    ?>
					<table class="table_template_help">
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('product:somemegatag')">%product:somemegatag%</a></td><td>Any custom Tag</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">%title%</a></td><td>Product Name</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('product_price')">%product_price%</a></td><td>Product Price</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('description_short')">%description_short%</a></td><td>Product Description Short</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('description')">%description%</a></td><td>Product Description</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">%link%</a></td><td>The link to the Product</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('addtocart')">%addtocart%</a></td><td>The link to the Product Add to Cart</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('default_category')">%default_category%</a></td><td>Product Category Default</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('date')">%date%</a></td><td>Date created</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_modified')">%date_modified%</a></td><td>Date modified</td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('countdown')">%countdown%</a></td><td>Specials offer CountDown</td></tr>
					</table>
					<?php
                break;
                case 'flickr':
                    ?>
					<table class="table_template_help" id="slide-flickr-template-entry">
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php _e("Post Title", 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php _e("Post content", 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php _e("The link to the post", 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('date')">{{date}}</a></td><td><?php _e("Date created", 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php _e('Username', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('views')">{{views}}</a></td><td><?php _e('Views', 'revslider'); ?></td></tr>
					</table>
					<table class="table_template_help" id="slide-images-template-entry">
						<?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo($img_handle);
                            ?>')">{{image_url_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image URL", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo($img_handle);
                            ?>')">{{image_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image &lt;img /&gt;", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<?php

                        }
                        ?>
					</table>
					<?php
                break;
                case 'instagram':
                    ?>
					<table class="table_template_help" id="slide-instagram-template-entry">
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php _e("Title", 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php _e("Content", 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php _e("Link", 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('date')">{{date}}</a></td><td><?php _e("Date created", 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php _e('Username', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('likes')">{{likes}}</a></td><td><?php _e('Number of Likes', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('num_comments')">{{num_comments}}</a></td><td><?php _e('Number of Comments', 'revslider'); ?></td></tr>
					</table>
					<table class="table_template_help" id="slide-images-template-entry">
						<?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo($img_handle);
                            ?>')">{{image_url_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image URL", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo($img_handle);
                            ?>')">{{image_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image &lt;img /&gt;", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<?php

                        }
                        ?>
					</table>
					<?php
                break;
                case 'twitter':
                    ?>
					<table class="table_template_help" id="slide-twitter-template-entry">
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php _e('Title', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php _e('Content', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php _e("Link", 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a></td><td><?php _e('Pulbishing Date', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php _e('Username', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('retweet_count')">{{retweet_count}}</a></td><td><?php _e('Retweet Count', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('favorite_count')">{{favorite_count}}</a></td><td><?php _e('Favorite Count', 'revslider'); ?></td></tr>
					</table>
					<table class="table_template_help" id="slide-images-template-entry">
						<?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo($img_handle);
                            ?>')">{{image_url_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image URL", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo($img_handle);
                            ?>')">{{image_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image &lt;img /&gt;", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<?php

                        }
                        ?>
					</table>
					<?php
                break;
                case 'facebook':
                    ?>
					<table class="table_template_help" id="slide-facebook-template-entry">
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php _e('Title', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php _e('Content', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php _e('Link', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a></td><td><?php _e('Pulbishing Date', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_modified}}</a></td><td><?php _e('Last Modify Date', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php _e('Username', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('likes')">{{likes}}</a></td><td><?php _e('Number of Likes', 'revslider'); ?></td></tr>
					</table>
					<table class="table_template_help" id="slide-images-template-entry">
						<?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo($img_handle);
                            ?>')">{{image_url_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image URL", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo($img_handle);
                            ?>')">{{image_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image &lt;img /&gt;", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<?php

                        }
                        ?>
					</table>
					<?php
                break;
                case 'youtube':
                    ?>
					<table class="table_template_help" id="slide-youtube-template-entry">
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php _e('Title', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('excerpt')">{{excerpt}}</a></td><td><?php _e('Excerpt', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php _e('Content', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a></td><td><?php _e('Pulbishing Date', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php _e('Link', 'revslider'); ?></td></tr>
					</table>
					<table class="table_template_help" id="slide-images-template-entry">
						<?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo($img_handle);
                            ?>')">{{image_url_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image URL", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo($img_handle);
                            ?>')">{{image_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image &lt;img /&gt;", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<?php

                        }
                        ?>
					</table>
					<?php
                break;
                case 'vimeo':
                    ?>
					<table class="table_template_help" id="slide-vimeo-template-entry">
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php _e('Title', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('excerpt')">{{excerpt}}</a></td><td><?php _e('Excerpt', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php _e('Content', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php _e('The link to the post', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a></td><td><?php _e('Pulbishing Date', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php _e('Username', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('likes')">{{likes}}</a></td><td><?php _e('Number of Likes', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('views')">{{views}}</a></td><td><?php _e('Number of Views', 'revslider'); ?></td></tr>
						<tr><td><a href="javascript:UniteLayersRev.insertTemplate('num_comments')">{{num_comments}}</a></td><td><?php _e('Number of Comments', 'revslider'); ?></td></tr>
					</table>
					<table class="table_template_help" id="slide-images-template-entry">
						<?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo($img_handle);
                            ?>')">{{image_url_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image URL", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo($img_handle);
                            ?>')">{{image_<?php echo($img_handle);
                            ?>}}</a></td><td><?php _e("Image &lt;img /&gt;", 'revslider');
                            echo ' '.$img_name;
                            ?></td></tr>
							<?php

                        }
                        ?>
					</table>
					<?php
                break;
            }
            ?>
			<script type="text/javascript">
			jQuery('document').ready(function() {
				jQuery('.rs-template-settings-tabs li').click(function() {
					var tw = jQuery('.rs-template-settings-tabs .selected'),
						tn = jQuery(this);
					jQuery(tw.data('content')).hide(0);
					tw.removeClass("selected");
					tn.addClass("selected");
					jQuery(tn.data('content')).show(0);
				});
				jQuery('.rs-template-settings-tabs li:first-child').click();
			});
		</script>
		</div>

		<div id="dialog_advanced_css" class="dialog_advanced_css" title="<?php _e('Advanced CSS', 'revslider'); ?>" style="display:none;">
			<div style="display: none;"><span id="rev-example-style-layer">example</span></div>
			<div class="first-css-area">
				<span class="advanced-css-title" style="background:#e67e22"><?php _e('Style from Options', 'revslider'); ?><span style="margin-left:15px;font-size:11px;font-style:italic">(<?php _e('Editable via Option Fields, Saved in the Class:', 'revslider'); ?><span class="current-advance-edited-class"></span>)</span></span>
				<textarea id="textarea_template_css_editor_uneditable" rows="20" cols="81" disabled="disabled"></textarea>
			</div>
			<div class="second-css-area">
				<span class="advanced-css-title"><?php _e('Additional Custom Styling', 'revslider'); ?><span style="margin-left:15px;font-size:11px;font-style:italic">(<?php _e('Appended in the Class:', 'revslider'); ?><span class="current-advance-edited-class"></span>)</span></span>
				<textarea id="textarea_advanced_css_editor" rows="20" cols="81"></textarea>
			</div>
		</div>
		
		<div id="dialog_save_as_css" class="dialog_save_as_css" title="<?php _e('Save As', 'revslider'); ?>" style="display:none;">
			<div style="margin-top:14px">
				<span style="margin-right:15px"><?php _e('Save As:', 'revslider'); ?></span><input id="rs-save-as-css" type="text" name="rs-save-as-css" value="" />
			</div>
		</div>
		 
		<div id="dialog_rename_css" class="dialog_rename_css" title="<?php _e('Rename CSS', 'revslider'); ?>" style="display:none;">
			<div style="margin-top:14px">
				<span style="margin-right:15px"><?php _e('Rename to:', 'revslider'); ?></span><input id="rs-rename-css" type="text" name="rs-rename-css" value="" />
			</div>
		</div>
		 
		<div id="dialog_advanced_layer_css" class="dialog_advanced_layer_css" title="<?php _e('Layer Inline CSS', 'revslider'); ?>" style="display:none;">
			<div class="first-css-area">
				<span class="advanced-css-title" style="background:#e67e22"><?php _e('Advanced Custom Styling', 'revslider'); ?><span style="margin-left:15px;font-size:11px;font-style:italic">(<?php _e('Appended Inline to the Layer Markup', 'revslider'); ?>)</span></span>
				<textarea id="textarea_template_css_editor_layer" name="textarea_template_css_editor_layer"></textarea>
			</div>
		</div>
		
		<div id="dialog_save_as_animation" class="dialog_save_as_animation" title="<?php _e('Save As', 'revslider'); ?>" style="display:none;">
			<div style="margin-top:14px">
				<span style="margin-right:15px"><?php _e('Save As:', 'revslider'); ?></span><input id="rs-save-as-animation" type="text" name="rs-save-as-animation" value="" />
			</div>
		</div>
		
		<div id="dialog_save_animation" class="dialog_save_animation" title="<?php _e('Save Under', 'revslider'); ?>" style="display:none;">
			<div style="margin-top:14px">
				<span style="margin-right:15px"><?php _e('Save Under:', 'revslider'); ?></span><input id="rs-save-under-animation" type="text" name="rs-save-under-animation" value="" />
			</div>
		</div>
		
		<script type="text/javascript">
			
			<?php
            $icon_sets = RevSliderBase::getIconSets();
            $sets = array();
            if (!empty($icon_sets)) {
                $sets = implode("','", $icon_sets);
            }
            ?>

			 var rs_icon_sets = new Array('<?php echo $sets; ?>');
			
			 
			jQuery(document).ready(function() {

				
				
				<?php if (!empty($jsonLayers)) {
    ?>
					//set init layers object
					UniteLayersRev.setInitLayersJson(<?php echo $jsonLayers?>);
				<?php 
} ?>
				
				<?php
                if ($slide->isStaticSlide()) {
                    $arrayDemoLayers = array();
                    $arrayDemoSettings = array();
                    $all_slides = RevGlobalObject::getVar('all_slides');
                    if (!empty($all_slides) && is_array($all_slides)) {
                        foreach ($all_slides as $cSlide) {
                            $arrayDemoLayers[$cSlide->getID()] = $cSlide->getLayers();
                            $arrayDemoSettings[$cSlide->getID()] = $cSlide->getParams();
                        }
                    }
                    $jsonDemoLayers = RevSliderFunctions::jsonEncodeForClientSide($arrayDemoLayers);
                    $jsonDemoSettings = RevSliderFunctions::jsonEncodeForClientSide($arrayDemoSettings);
                    ?>
					//set init demo layers object
					UniteLayersRev.setInitDemoLayersJson(<?php echo $jsonDemoLayers;
                    ?>);
					UniteLayersRev.setInitDemoSettingsJson(<?php echo $jsonDemoSettings;
                    ?>);
					<?php

                } ?>

				<?php if (!empty($jsonCaptions)) {
    ?>
				UniteLayersRev.setInitCaptionClasses(<?php echo $jsonCaptions;
    ?>);
				<?php 
} ?>

				<?php if (!empty($arrCustomAnim)) {
    ?>
				UniteLayersRev.setInitLayerAnim(<?php echo $arrCustomAnim;
    ?>);
				<?php 
} ?>

				<?php if (!empty($arrCustomAnimDefault)) {
    ?>
				UniteLayersRev.setInitLayerAnimsDefault(<?php echo $arrCustomAnimDefault;
    ?>);
				<?php 
} ?>

				<?php if (!empty($jsonFontFamilys)) {
    ?>
				UniteLayersRev.setInitFontTypes(<?php echo $jsonFontFamilys;
    ?>);
				<?php 
} ?>

				<?php if (!empty($arrCssStyles)) {
    ?>
				UniteCssEditorRev.setInitCssStyles(<?php echo $arrCssStyles;
    ?>);
				<?php 
} ?>

				<?php
                $trans_sizes = RevSliderFunctions::jsonEncodeForClientSide($slide->translateIntoSizes());
                ?>
				UniteLayersRev.setInitTransSetting(<?php echo $trans_sizes; ?>);

				UniteLayersRev.init("<?php echo $slideDelay; ?>");
				
								
				UniteCssEditorRev.init();
				
				
				RevSliderAdmin.initGlobalStyles();

				RevSliderAdmin.initLayerPreview();

				RevSliderAdmin.setStaticCssCaptionsUrl('<?php echo addslashes(WP_CONTENT_DIR).'public/assets/css/static-captions.css'; ?>');

				/* var reproduce;
				jQuery(window).resize(function() {
					clearTimeout(reproduce);
					reproduce = setTimeout(function() {
						UniteLayersRev.refreshGridSize();
					},100);
				});*/

				<?php if ($kenburn_effect == 'on') {
    ?>
				jQuery('input[name="kenburn_effect"]:checked').change();
				<?php 
} ?>


				// DRAW  HORIZONTAL AND VERTICAL LINEAR
				var horl = jQuery('#hor-css-linear .linear-texts'),
					verl = jQuery('#ver-css-linear .linear-texts'),
					maintimer = jQuery('#mastertimer-linear .linear-texts'),
					mw = "<?php echo RevGlobalObject::getVar('tempwidth_jq'); ?>";
					mw = parseInt(mw.split(":")[1],0);

				for (var i=-600;i<mw;i=i+100) {
					if (mw-i<100)
						horl.append('<li style="width:'+(mw-i)+'px"><span>'+i+'</span></li>');
					else
						horl.append('<li><span>'+i+'</span></li>');
				}

				for (var i=0;i<2000;i=i+100) {
					verl.append('<li><span>'+i+'</span></li>');
				}

				for (var i=0;i<160;i=i+1) {
					var txt = i+"s";

					maintimer.append('<li><span>'+txt+'</span></li>');
				}

				// SHIFT RULERS and TEXTS and HELP LINES//
				function horRuler() {
					var dl = jQuery('#divLayers'),
						l = parseInt(dl.offset().left,0) - parseInt(jQuery('#thelayer-editor-wrapper').offset().left,0);
					jQuery('#hor-css-linear').css({backgroundPosition:(l)+"px 50%"});
					jQuery('#hor-css-linear .linear-texts').css({left:(l-595)+"px"});
					jQuery('#hor-css-linear .helplines-offsetcontainer').css({left:(l)+"px"});

					jQuery('#ver-css-linear .helplines').css({left:"-15px"}).width(jQuery('#thelayer-editor-wrapper').outerWidth(true)-35);
					jQuery('#hor-css-linear .helplines').css({top:"-15px"}).height(jQuery('#thelayer-editor-wrapper').outerHeight(true)-41);
				}

				horRuler();


				/*jQuery('.my-color-field').wpColorPicker({
					palettes:false,
					height:250,

					border:false,
										
				    change:function(event,ui) {
				    	switch (jQuery(event.target).attr('name')) {
							case "adbutton-color-1":
							case "adbutton-color-2":
							case "adbutton-border-color":
								setExampleButtons();
							break;

							case "adshape-color-1":
							case "adshape-color-2":
							case "adshape-border-color":							
								setExampleShape();
							break;
							case "bg_color":
								var bgColor = jQuery("#slide_bg_color").val();
								jQuery("#divbgholder").css("background-color",bgColor);
								jQuery('.slotholder .tp-bgimg.defaultimg').css({backgroundColor:bgColor});
								jQuery('#slide_selector .list_slide_links li.selected .slide-media-container ').css({backgroundColor:bgColor});
							break;
						}		

						if (jQuery('.layer_selected.slide_layer').length>0) {
							jQuery(event.target).blur().focus();
						}

					},
					clear:function(event,ui) {
						if (jQuery('.layer_selected.slide_layer').length>0) {
							var inp = jQuery(event.target).closest('.wp-picker-input-wrap').find('.my-color-field');
							inp.val("transparent").blur().focus();
						}
					}
								
				});*/

				jQuery('.adb-input').on("change blur focus",setExampleButtons);
				jQuery('.ads-input, input[name="shape_fullwidth"], input[name="shape_fullheight"]').on("change blur focus",setExampleShape);
				jQuery('.ui-autocomplete').on('click',setExampleButtons);

				jQuery('.wp-color-result').on("click",function() {

					if (jQuery(this).hasClass("wp-picker-open"))
						jQuery(this).closest('.wp-picker-container').addClass("pickerisopen");
					else
						jQuery(this).closest('.wp-picker-container').removeClass("pickerisopen");
				});

				jQuery("body").click(function(event) {
					jQuery('.wp-picker-container.pickerisopen').removeClass("pickerisopen");
				})

				// WINDOW RESIZE AND SCROLL EVENT SHOULD REDRAW RULERS
				jQuery(window).resize(horRuler);
				jQuery('#divLayers-wrapper').on('scroll',horRuler);


				jQuery('#toggle-idle-hover .icon-stylehover').click(function() {
					var bt = jQuery('#toggle-idle-hover');
					bt.removeClass("idleisselected").addClass("hoverisselected");
					jQuery('#tp-idle-state-advanced-style').hide();
					jQuery('#tp-hover-state-advanced-style').show();
				});

				jQuery('#toggle-idle-hover .icon-styleidle').click(function() {
					var bt = jQuery('#toggle-idle-hover');
					bt.addClass("idleisselected").removeClass("hoverisselected");
					jQuery('#tp-idle-state-advanced-style').show();
					jQuery('#tp-hover-state-advanced-style').hide();
				});


				jQuery('input[name="hover_allow"]').on("change",function() {
					if (jQuery(this).attr("checked")=="checked") {
						jQuery('#idle-hover-swapper').show();
					} else {
						jQuery('#idle-hover-swapper').hide();
					}
				});


				// HIDE /SHOW  INNER SAVE,SAVE AS ETC..
				jQuery('.clicktoshowmoresub').click(function() {
					jQuery(this).find('.clicktoshowmoresub_inner').show();
				});

				jQuery('.clicktoshowmoresub').on('mouseleave',function() {
					jQuery(this).find('.clicktoshowmoresub_inner').hide();
				});
				
				//arrowRepeater();
				function arrowRepeater() {
					var tw = new punchgs.TimelineLite();
					tw.add(punchgs.TweenLite.from(jQuery('.animatemyarrow'),0.5,{x:-10,opacity:0}),0);
					tw.add(punchgs.TweenLite.to(jQuery('.animatemyarrow'),0.5,{x:10,opacity:0}),0.5);
					
					tw.play(0);
					tw.eventCallback("onComplete",function() {
						tw.restart();
					})
				}
				
				RevSliderSettings.createModernOnOff();

			});

		</script>

	

		<?php
        if (!$slide->isStaticSlide()) {
            ?>
<!--			<a href="javascript:void(0)" id="button_save_slide" class="revgreen button-primary"><div class="updateicon"></div><i class="rs-icon-save-light" style="display: inline-block;vertical-align: middle;width: 18px;height: 20px;background-repeat: no-repeat;margin-right:5px;"></i><?php _e("Save Slide", 'revslider');
            ?></a>

-->
		<?php 
        } else {
            ?>
<!--			<a href="javascript:void(0)" id="button_save_static_slide" class="revgreen button-primary"><div class="updateicon"></div><i class="revicon-arrows-ccw"></i><?php _e("Update Static Layers", 'revslider');
            ?></a>

-->
		<?php 
        } ?>
<!--		<span id="loader_update" class="loader_round" style="display:none;"><?php _e("updating", 'revslider'); ?>...</span>
		<span id="update_slide_success" class="success_message" class="display:none;"></span>
		<a href="<?php echo self::getViewUrl(RevSliderAdmin::VIEW_SLIDER, "id=$sliderID"); ?>" class="button-primary revblue"><i class="revicon-cog"></i><?php _e("Slider Settings", 'revslider'); ?></a>
		<a id="button_close_slide" href="<?php echo $closeUrl?>" class="button-primary revyellow"><div class="closeicon"></div><i class="revicon-list-add"></i><?php _e("Slides Overview", 'revslider'); ?></a>
-->
		<?php
        if (!$slide->isStaticSlide()) {
            ?>
<!--		<a href="javascript:void(0)" id="button_delete_slide" class="button-primary revred" original-title=""><i class="revicon-trash"></i><?php _e("Delete Slide", 'revslider');
            ?></a>
	-->
		<?php 
        } ?>
	</div>

	<div class="vert_sap"></div>

	<div id="dialog_rename_animation" class="dialog_rename_animation" title="<?php _e('Rename Animation', 'revslider'); ?>" style="display:none;">
		<div style="margin-top:14px">
			<span style="margin-right:15px"><?php _e('Rename to:', 'revslider'); ?></span><input id="rs-rename-animation" type="text" name="rs-rename-animation" value="" />
		</div>
	</div>


</div>

<?php
if ($slide->isStaticSlide()) {
    $slideID = $slide->getID();
}

$mslide_list = array();
if(!empty($arrSlidesWPML)){
	foreach($arrSlidesWPML as $arwmpl) {
		if($arwmpl['id'] == $slideID) continue;
		
		$mslide_list[] = array($arwmpl['id'] => $arwmpl['title']);
	}
}
$mslide_list = RevSliderFunctions::jsonEncodeForClientSide($mslide_list);

?>
<script type="text/javascript">
	var g_patternViewSlide = '<?php echo $patternViewSlide; ?>';

	
	var g_messageDeleteSlide = "<?php _e("Delete this slide?", 'revslider'); ?>";
	jQuery(document).ready(function(){
		RevSliderAdmin.initEditSlideView(<?php echo $slideID; ?>, <?php echo $sliderID; ?>, <?php echo ($slide->isStaticSlide()) ? 'true' : 'false'; ?>);
		
		UniteLayersRev.setInitSlideIds(<?php echo $mslide_list; ?>);
        
	});
	var curSlideID = <?php echo $slideID; ?>;
</script>


<?php
require self::getPathTemplate("../system/dialog-copy-move");
// @codingStandardsIgnoreEnd