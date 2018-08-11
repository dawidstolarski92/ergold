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

class RevOperations extends UniteElementsBaseRev
{

    public static function getWildcardsSettings()
    {
        $settings = new UniteSettingsAdvancedRev();

        $slider = new RevSlider();

        $arrOutput = array();

        $arrOutput["default"] = "default";

        $arrSlides = $slider->getArrSlidersWithSlidesShort(RevSlider::SLIDER_TYPE_TEMPLATE);

        $arrOutput = $arrOutput + $arrSlides;



        $settings->addSelect("slide_template", $arrOutput, __("Choose Slide Template", REVSLIDER_TEXTDOMAIN), "default");

        return($settings);
    }

    public static function getWildcardsSettingNames()
    {
        $settings = self::getWildcardsSettings();

        $arrNames = $settings->getArrSettingNamesAndTitles();

        return($arrNames);
    }

    public static function getPostWilcardValues($postID)
    {
        $settings = RevOperations::getWildcardsSettings();

        $settings->updateValuesFromPostMeta($postID);

        $arrValues = $settings->getArrValues();

        return($arrValues);
    }

    public function getButtonClasses()
    {
        $arrButtons = array(
            "red" => "Red Button",
            "green" => "Green Button",
            "blue" => "Blue Button",
            "orange" => "Orange Button",
            "darkgrey" => "Darkgrey Button",
            "lightgrey" => "Lightgrey Button",
        );



        return($arrButtons);
    }

    /**
     * get easing functions array
     */
    public function getArrEasing()
    { //true
        $arrEasing = array(
            "Linear.easeNone" => "Linear.easeNone",
            "Power0.easeIn" => "Power0.easeIn  (linear)",
            "Power0.easeInOut" => "Power0.easeInOut  (linear)",
            "Power0.easeOut" => "Power0.easeOut  (linear)",
            "Power1.easeIn" => "Power1.easeIn",
            "Power1.easeInOut" => "Power1.easeInOut",
            "Power1.easeOut" => "Power1.easeOut",
            "Power2.easeIn" => "Power2.easeIn",
            "Power2.easeInOut" => "Power2.easeInOut",
            "Power2.easeOut" => "Power2.easeOut",
            "Power3.easeIn" => "Power3.easeIn",
            "Power3.easeInOut" => "Power3.easeInOut",
            "Power3.easeOut" => "Power3.easeOut",
            "Power4.easeIn" => "Power4.easeIn",
            "Power4.easeInOut" => "Power4.easeInOut",
            "Power4.easeOut" => "Power4.easeOut",
            "Quad.easeIn" => "Quad.easeIn  (same as Power1.easeIn)",
            "Quad.easeInOut" => "Quad.easeInOut  (same as Power1.easeInOut)",
            "Quad.easeOut" => "Quad.easeOut  (same as Power1.easeOut)",
            "Cubic.easeIn" => "Cubic.easeIn  (same as Power2.easeIn)",
            "Cubic.easeInOut" => "Cubic.easeInOut  (same as Power2.easeInOut)",
            "Cubic.easeOut" => "Cubic.easeOut  (same as Power2.easeOut)",
            "Quart.easeIn" => "Quart.easeIn  (same as Power3.easeIn)",
            "Quart.easeInOut" => "Quart.easeInOut  (same as Power3.easeInOut)",
            "Quart.easeOut" => "Quart.easeOut  (same as Power3.easeOut)",
            "Quint.easeIn" => "Quint.easeIn  (same as Power4.easeIn)",
            "Quint.easeInOut" => "Quint.easeInOut  (same as Power4.easeInOut)",
            "Quint.easeOut" => "Quint.easeOut  (same as Power4.easeOut)",
            "Strong.easeIn" => "Strong.easeIn  (same as Power4.easeIn)",
            "Strong.easeInOut" => "Strong.easeInOut  (same as Power4.easeInOut)",
            "Strong.easeOut" => "Strong.easeOut  (same as Power4.easeOut)",
            "Back.easeIn" => "Back.easeIn",
            "Back.easeInOut" => "Back.easeInOut",
            "Back.easeOut" => "Back.easeOut",
            "Bounce.easeIn" => "Bounce.easeIn",
            "Bounce.easeInOut" => "Bounce.easeInOut",
            "Bounce.easeOut" => "Bounce.easeOut",
            "Circ.easeIn" => "Circ.easeIn",
            "Circ.easeInOut" => "Circ.easeInOut",
            "Circ.easeOut" => "Circ.easeOut",
            "Elastic.easeIn" => "Elastic.easeIn",
            "Elastic.easeInOut" => "Elastic.easeInOut",
            "Elastic.easeOut" => "Elastic.easeOut",
            "Expo.easeIn" => "Expo.easeIn",
            "Expo.easeInOut" => "Expo.easeInOut",
            "Expo.easeOut" => "Expo.easeOut",
            "Sine.easeIn" => "Sine.easeIn",
            "Sine.easeInOut" => "Sine.easeInOut",
            "Sine.easeOut" => "Sine.easeOut",
            "SlowMo.ease" => "SlowMo.ease",
            //add old easings //From here on display none
            "easeOutBack" => "easeOutBack",
            "easeInQuad" => "easeInQuad",
            "easeOutQuad" => "easeOutQuad",
            "easeInOutQuad" => "easeInOutQuad",
            "easeInCubic" => "easeInCubic",
            "easeOutCubic" => "easeOutCubic",
            "easeInOutCubic" => "easeInOutCubic",
            "easeInQuart" => "easeInQuart",
            "easeOutQuart" => "easeOutQuart",
            "easeInOutQuart" => "easeInOutQuart",
            "easeInQuint" => "easeInQuint",
            "easeOutQuint" => "easeOutQuint",
            "easeInOutQuint" => "easeInOutQuint",
            "easeInSine" => "easeInSine",
            "easeOutSine" => "easeOutSine",
            "easeInOutSine" => "easeInOutSine",
            "easeInExpo" => "easeInExpo",
            "easeOutExpo" => "easeOutExpo",
            "easeInOutExpo" => "easeInOutExpo",
            "easeInCirc" => "easeInCirc",
            "easeOutCirc" => "easeOutCirc",
            "easeInOutCirc" => "easeInOutCirc",
            "easeInElastic" => "easeInElastic",
            "easeOutElastic" => "easeOutElastic",
            "easeInOutElastic" => "easeInOutElastic",
            "easeInBack" => "easeInBack",
            "easeInOutBack" => "easeInOutBack",
            "easeInBounce" => "easeInBounce",
            "easeOutBounce" => "easeOutBounce",
            "easeInOutBounce" => "easeInOutBounce",
            "Quad.easeIn" => "Quad.easeIn  (same as Power1.easeIn)",
            "Quad.easeInOut" => "Quad.easeInOut  (same as Power1.easeInOut)",
            "Quad.easeOut" => "Quad.easeOut  (same as Power1.easeOut)",
            "Cubic.easeIn" => "Cubic.easeIn  (same as Power2.easeIn)",
            "Cubic.easeInOut" => "Cubic.easeInOut  (same as Power2.easeInOut)",
            "Cubic.easeOut" => "Cubic.easeOut  (same as Power2.easeOut)",
            "Quart.easeIn" => "Quart.easeIn  (same as Power3.easeIn)",
            "Quart.easeInOut" => "Quart.easeInOut  (same as Power3.easeInOut)",
            "Quart.easeOut" => "Quart.easeOut  (same as Power3.easeOut)",
            "Quint.easeIn" => "Quint.easeIn  (same as Power4.easeIn)",
            "Quint.easeInOut" => "Quint.easeInOut  (same as Power4.easeInOut)",
            "Quint.easeOut" => "Quint.easeOut  (same as Power4.easeOut)",
            "Strong.easeIn" => "Strong.easeIn  (same as Power4.easeIn)",
            "Strong.easeInOut" => "Strong.easeInOut  (same as Power4.easeInOut)",
            "Strong.easeOut" => "Strong.easeOut  (same as Power4.easeOut)"
        );

        return($arrEasing);
    }

    public function getArrSplit()
    { //true
        $arrSplit = array(
            "none" => "No Split",
            "chars" => "Char Based",
            "words" => "Word Based",
            "lines" => "Line Based"
        );

        return($arrSplit);
    }

    public function getArrEndEasing()
    {
        $arrEasing = $this->getArrEasing();

        $arrEasing = array_merge(array("nothing" => "No Change"), $arrEasing);



        return($arrEasing);
    }

    /**
     * get transition array
     */
    public function getArrTransition()
    {
        $arrTransition = array(
            "notselectable1" => "BASICS",
            "notransition" => "No Transition",
            "fade" => "Fade",
            "crossfade" => "Fade Cross",
            "fadethroughdark" => "Fade Through Black",
            "fadethroughlight" => "Fade Through Light",
            "fadethroughtransparent" => "Fade Through Transparent",
            "notselectable2" => "SLIDE SIMPLE",
            "slideup" => "Slide To Top",
            "slidedown" => "Slide To Bottom",
            "slideright" => "Slide To Right",
            "slideleft" => "Slide To Left",
            "slidehorizontal" => "Slide Horizontal (Next/Previous)",
            "slidevertical" => "Slide Vertical (Next/Previous)",
            "notselectable21" => "SLIDE OVER",
            "slideoverup" => "Slide Over To Top",
            "slideoverdown" => "Slide Over To Bottom",
            "slideoverright" => "Slide Over To Right",
            "slideoverleft" => "Slide Over To Left",
            "slideoverhorizontal" => "Slide Over Horizontal (Next/Previous)",
            "slideoververtical" => "Slide Over Vertical (Next/Previous)",
            "notselectable22" => "SLIDE REMOVE",
            "slideremoveup" => "Slide Remove To Top",
            "slideremovedown" => "Slide Remove To Bottom",
            "slideremoveright" => "Slide Remove To Right",
            "slideremoveleft" => "Slide Remove To Left",
            "slideremovehorizontal" => "Slide Remove Horizontal (Next/Previous)",
            "slideremovevertical" => "Slide Remove Vertical (Next/Previous)",
            "notselectable26" => "SLIDING OVERLAYS",
            "slidingoverlayup" => "Sliding Overlays To Top",
            "slidingoverlaydown" => "Sliding Overlays To Bottom",
            "slidingoverlayright" => "Sliding Overlays To Right",
            "slidingoverlayleft" => "Sliding Overlays To Left",
            "slidingoverlayhorizontal" => "Sliding Overlays Horizontal (Next/Previous)",
            "slidingoverlayvertical" => "Sliding Overlays Vertical (Next/Previous)",
            "notselectable23" => "SLOTS AND BOXES",
            "boxslide" => "Slide Boxes",
            "slotslide-horizontal" => "Slide Slots Horizontal",
            "slotslide-vertical" => "Slide Slots Vertical",
            "boxfade" => "Fade Boxes",
            "slotfade-horizontal" => "Fade Slots Horizontal",
            "slotfade-vertical" => "Fade Slots Vertical",
            "notselectable31" => "FADE & SLIDE",
            "fadefromright" => "Fade and Slide from Right",
            "fadefromleft" => "Fade and Slide from Left",
            "fadefromtop" => "Fade and Slide from Top",
            "fadefrombottom" => "Fade and Slide from Bottom",
            "fadetoleftfadefromright" => "To Left From Right",
            "fadetorightfadefromleft" => "To Right From Left",
            "fadetotopfadefrombottom" => "To Top From Bottom",
            "fadetobottomfadefromtop" => "To Bottom From Top",
            "notselectable4" => "PARALLAX",
            "parallaxtoright" => "Parallax to Right",
            "parallaxtoleft" => "Parallax to Left",
            "parallaxtotop" => "Parallax to Top",
            "parallaxtobottom" => "Parallax to Bottom",
            "parallaxhorizontal" => "Parallax Horizontal",
            "parallaxvertical" => "Parallax Vertical",
            "notselectable5" => "ZOOM TRANSITIONS",
            "scaledownfromright" => "Zoom Out and Fade From Right",
            "scaledownfromleft" => "Zoom Out and Fade From Left",
            "scaledownfromtop" => "Zoom Out and Fade From Top",
            "scaledownfrombottom" => "Zoom Out and Fade From Bottom",
            "zoomout" => "ZoomOut",
            "zoomin" => "ZoomIn",
            "slotzoom-horizontal" => "Zoom Slots Horizontal",
            "slotzoom-vertical" => "Zoom Slots Vertical",
            "notselectable6" => "CURTAIN TRANSITIONS",
            "curtain-1" => "Curtain from Left",
            "curtain-2" => "Curtain from Right",
            "curtain-3" => "Curtain from Middle",
            "notselectable7" => "PREMIUM TRANSITIONS",
            "3dcurtain-horizontal" => "3D Curtain Horizontal",
            "3dcurtain-vertical" => "3D Curtain Vertical",
            "cube" => "Cube Vertical",
            "cube-horizontal" => "Cube Horizontal",
            "incube" => "In Cube Vertical",
            "incube-horizontal" => "In Cube Horizontal",
            "turnoff" => "TurnOff Horizontal",
            "turnoff-vertical" => "TurnOff Vertical",
            "papercut" => "Paper Cut",
            "flyin" => "Fly In",
            "notselectable1a" => "RANDOM",
            "random-selected" => "Random of Selected",
            "random-static" => "Random Flat",
            "random-premium" => "Random Premium",
            "random" => "Random Flat and Premium"
        );

        return($arrTransition);
    }

    public static function getRandomTransition()
    {
        $arrTrans = self::getArrTransition();

        unset($arrTrans["random"]);

        $trans = array_rand($arrTrans);



        return($trans);
    }

    public static function getDefaultTransition()
    {
        $arrValues = self::getGeneralSettingsValues();

        return 'random';
    }

    /**
     * get animations array
     */
    public static function getArrAnimations($all = true)
    {
        $arrAnimations = array(
        );

        $arrAnimations['custom'] = array('handle' => __('## Custom Animation ##', 'revslider'));
        $arrAnimations['v5s'] = array('handle' => '-----------------------------------');
        $arrAnimations['v5'] = array('handle' => __('- VERSION 5.0 ANIMATIONS -', 'revslider'));
        $arrAnimations['v5e'] = array('handle' => '-----------------------------------');

        $arrAnimations['LettersFlyInFromBottom'] = array('handle' => 'LettersFlyInFromBottom', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"-35deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['LettersFlyInFromLeft'] = array('handle' => 'LettersFlyInFromLeft', 'params' => '{"movex":"[-105%]","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0deg","rotationz":"-90deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['LettersFlyInFromRight'] = array('handle' => 'LettersFlyInFromRight', 'params' => '{"movex":"[105%]","movey":"inherit","movez":"0","rotationx":"45deg","rotationy":"0deg","rotationz":"90deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['LettersFlyInFromTop'] = array('handle' => 'LettersFlyInFromTop', 'params' => '{"movex":"inherit","movey":"[-100%]","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"35deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['MaskedZoomOut'] = array('handle' => 'MaskedZoomOut', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"2","scaley":"2","skewx":"0","skewy":"0","captionopacity":"0","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power2.easeOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['PopUpSmooth'] = array('handle' => 'PopUpSmooth', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"0.9","scaley":"0.9","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"top","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['RotateInFromBottom'] = array('handle' => 'RotateInFromBottom', 'params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"90deg","scalex":"2","scaley":"2","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['RotateInFormZero'] = array('handle' => 'RotateInFormZero', 'params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"-20deg","rotationy":"-20deg","rotationz":"0deg","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","easing":"Power3.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskFromBottom'] = array('handle' => 'SlideMaskFromBottom', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"0","mask":"true","mask_x":"0px","mask_y":"[100%]","easing":"Power2.easeInOut","speed":"2000","split":"none","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskFromLeft'] = array('handle' => 'SlideMaskFromLeft', 'params' => '{"movex":"[-100%]","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskFromRight'] = array('handle' => 'SlideMaskFromRight', 'params' => '{"movex":"[100%]","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskFromTop'] = array('handle' => 'SlideMaskFromTop', 'params' => '{"movex":"inherit","movey":"[-100%]","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothPopUp_One'] = array('handle' => 'SmoothPopUp_One', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"0.8","scaley":"0.8","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"top","easing":"Power4.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothPopUp_Two'] = array('handle' => 'SmoothPopUp_Two', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"0.9","scaley":"0.9","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"top","easing":"Power2.easeOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothMaskFromRight'] = array('handle' => 'SmoothMaskFromRight', 'params' => '{"movex":"[-175%]","movey":"0px","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"1","mask":"true","mask_x":"[100%]","mask_y":"0","easing":"Power3.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothMaskFromLeft'] = array('handle' => 'SmoothMaskFromLeft', 'params' => '{"movex":"[175%]","movey":"0px","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"1","mask":"true","mask_x":"[-100%]","mask_y":"0","easing":"Power3.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothSlideFromBottom'] = array('handle' => 'SmoothSlideFromBottom', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"[100%]","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"5"}', 'settings' => array('version' => '5.0'));

        $arrAnimations['v4s'] = array('handle' => '-----------------------------------');
        $arrAnimations['v4'] = array('handle' => __('- VERSION 4.0 ANIMATIONS -', 'revslider'));
        $arrAnimations['v4e'] = array('handle' => '-----------------------------------');
        $arrAnimations['noanim'] = array('handle' => 'No-Animation', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['tp-fade'] = array('handle' => 'Fade-In', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"0"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['sft'] = array('handle' => 'Short-from-Top', 'params' => '{"movex":"inherit","movey":"-50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['sfb'] = array('handle' => 'Short-from-Bottom', 'params' => '{"movex":"inherit","movey":"50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['sfl'] = array('handle' => 'Short-From-Left', 'params' => '{"movex":"-50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['sfr'] = array('handle' => 'Short-From-Right', 'params' => '{"movex":"50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['lfr'] = array('handle' => 'Long-From-Right', 'params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['lfl'] = array('handle' => 'Long-From-Left', 'params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['lft'] = array('handle' => 'Long-From-Top', 'params' => '{"movex":"inherit","movey":"top","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['lfb'] = array('handle' => 'Long-From-Bottom', 'params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewfromleft'] = array('handle' => 'Skew-From-Long-Left', 'params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"45px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewfromright'] = array('handle' => 'Skew-From-Long-Right', 'params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewfromleftshort'] = array('handle' => 'Skew-From-Short-Left', 'params' => '{"movex":"-200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewfromrightshort'] = array('handle' => 'Skew-From-Short-Right', 'params' => '{"movex":"200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['randomrotate'] = array('handle' => 'Random-Rotate-and-Scale', 'params' => '{"movex":"{-250,250}","movey":"{-150,150}","movez":"inherit","rotationx":"{-90,90}","rotationy":"{-90,90}","rotationz":"{-360,360}","scalex":"{0,1}","scaley":"{0,1}","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));


        if ($all) {
            $arrAnimations['vss'] = array('handle' => '--------------------------------------');
            $arrAnimations['vs'] = array('handle' => __('- SAVED CUSTOM ANIMATIONS -', 'revslider'));
            $arrAnimations['vse'] = array('handle' => '--------------------------------------');

            //$custom = RevSliderOperations::getCustomAnimations('customin');
            ////////////////////////////////////////////////////////////////////////
            ///////////////////////////// Need to work on it ///////////////////////
            ////////////////////////////////////////////////////////////////////////
            // $custom = RevSliderOperations::getCustomAnimationsFullPre('customin');
            $custom = array();

            $arrAnimations = array_merge($arrAnimations, $custom);
        }

        foreach ($arrAnimations as $key => $value) {
            if (!@RevsliderPrestashop::getIsset($value['params'])) {
                continue;
            }

            $t = Tools::jsonDecode(str_replace("'", '"', $value['params']), true);
            if (!empty($t)) {
                $arrAnimations[$key]['params'] = $t;
            }
        }

        return($arrAnimations);
    }

    /**
     * get "end" animations array
     */
    public static function getArrEndAnimations($all = true)
    {
        $arrAnimations = array();
        $arrAnimations['custom'] = array('handle' => __('## Custom Animation ##', 'revslider'));
        $arrAnimations['auto'] = array('handle' => __('Automatic Reverse', 'revslider'));
        $arrAnimations['v5s'] = array('handle' => '-----------------------------------');
        $arrAnimations['v5'] = array('handle' => __('- VERSION 5.0 ANIMATIONS -', 'revslider'));
        $arrAnimations['v5e'] = array('handle' => '-----------------------------------');

        $arrAnimations['BounceOut'] = array('handle' => 'BounceOut', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"0deg","scalex":"0.7","scaley":"0.7","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"true","mask_x":"0","mask_y":"0","easing":"Back.easeIn","speed":"500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['Fade-Out-Long'] = array('handle' => 'Fade-Out-Long', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","easing":"Power2.easeIn","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToBottom'] = array('handle' => 'SlideMaskToBottom', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"nothing","speed":"300","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToLeft'] = array('handle' => 'SlideMaskToLeft', 'params' => '{"movex":"[-100%]","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"Power3.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToRight'] = array('handle' => 'SlideMaskToRight', 'params' => '{"movex":"[100%]","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"Power3.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToTop'] = array('handle' => 'SlideMaskToTop', 'params' => '{"movex":"inherit","movey":"[-100%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"nothing","speed":"300","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlurpOut'] = array('handle' => 'SlurpOut', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"0deg","scalex":"0.7","scaley":"0.7","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"0","mask_y":"0","easing":"Power3.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothCropToBottom'] = array('handle' => 'SmoothCropToBottom', 'params' => '{"movex":"inherit","movey":"[175%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"Power2.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));

        $arrAnimations['v4s'] = array('handle' => '-----------------------------------');
        $arrAnimations['v4'] = array('handle' => __('- VERSION 4.0 ANIMATIONS -', 'revslider'));
        $arrAnimations['v4e'] = array('handle' => '-----------------------------------');
        $arrAnimations['noanimout'] = array('handle' => 'No-Out-Animation', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['fadeout'] = array('handle' => 'Fade-Out', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"0"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['stt'] = array('handle' => 'Short-To-Top', 'params' => '{"movex":"inherit","movey":"-50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['stb'] = array('handle' => 'Short-To-Bottom', 'params' => '{"movex":"inherit","movey":"50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['stl'] = array('handle' => 'Short-To-Left', 'params' => '{"movex":"-50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['str'] = array('handle' => 'Short-To-Right', 'params' => '{"movex":"50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltr'] = array('handle' => 'Long-To-Right', 'params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltl'] = array('handle' => 'Long-To-Left', 'params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltt'] = array('handle' => 'Long-To-Top', 'params' => '{"movex":"inherit","movey":"top","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltb'] = array('handle' => 'Long-To-Bottom', 'params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtoleft'] = array('handle' => 'Skew-To-Long-Left', 'params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"45px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtoright'] = array('handle' => 'Skew-To-Long-Right', 'params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtorightshort'] = array('handle' => 'Skew-To-Short-Right', 'params' => '{"movex":"200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtoleftshort'] = array('handle' => 'Skew-To-Short-Left', 'params' => '{"movex":"-200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['randomrotateout'] = array('handle' => 'Random-Rotate-Out', 'params' => '{"movex":"{-250,250}","movey":"{-150,150}","movez":"inherit","rotationx":"{-90,90}","rotationy":"{-90,90}","rotationz":"{-360,360}","scalex":"{0,1}","scaley":"{0,1}","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));

        if ($all) {
            $arrAnimations['vss'] = array('handle' => '--------------------------------------');
            $arrAnimations['vs'] = array('handle' => __('- SAVED CUSTOM ANIMATIONS -', 'revslider'));
            $arrAnimations['vse'] = array('handle' => '--------------------------------------');
            //$custom = RevSliderOperations::getCustomAnimations('customout');
            ////////////////////////////////////////////////////////////////////////
            ///////////////////////////// Need to work on it ///////////////////////
            ////////////////////////////////////////////////////////////////////////
            // $custom = RevSliderOperations::getCustomAnimationsFullPre('customout');
            $custom = array();

            $arrAnimations = array_merge($arrAnimations, $custom);
        }

        foreach ($arrAnimations as $key => $value) {
            if (!@RevsliderPrestashop::getIsset($value['params'])) {
                continue;
            }

            $t = Tools::jsonDecode(str_replace("'", '"', $value['params']), true);
            if (!empty($t)) {
                $arrAnimations[$key]['params'] = $t;
            }
        }
        return($arrAnimations);
    }

    public static function insertCustomAnim($anim)
    {
        if (@RevsliderPrestashop::getIsset($anim['handle'])) {
            $db = new UniteDBRev();



            $arrInsert = array();

            $arrInsert["handle"] = $anim['handle'];

            unset($anim['handle']);



            $arrInsert["params"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $anim)));



            $db->insert(GlobalsRevSlider::$table_layer_anims, $arrInsert);
        }

        $arrAnims = array();

        $arrAnims['customin'] = RevOperations::getCustomAnimations();

        $arrAnims['customout'] = RevOperations::getCustomAnimations('customout');

        $arrAnims['customfull'] = RevOperations::getFullCustomAnimations();



        return $arrAnims;
    }

    public static function updateCustomAnim($anim)
    {
        if (@RevsliderPrestashop::getIsset($anim['handle'])) {
            $db = new UniteDBRev();

            $handle = $anim['handle'];

            unset($anim['handle']);



            $arrUpdate = array();

            $arrUpdate['params'] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $anim)));



            $db->update(GlobalsRevSlider::$table_layer_anims, $arrUpdate, array('handle' => $handle));
        }

        $arrAnims = array();

        $arrAnims['customin'] = RevOperations::getCustomAnimations();

        $arrAnims['customout'] = RevOperations::getCustomAnimations('customout');

        $arrAnims['customfull'] = RevOperations::getFullCustomAnimations();



        return $arrAnims;
    }

    public static function deleteCustomAnim($rawID)
    {
        if (trim($rawID) != '') {
            $db = new UniteDBRev();

            $id = str_replace(array('customin-', 'customout'), array('', ''), $rawID);

            $db->delete(GlobalsRevSlider::$table_layer_anims, "id = '" . mysql_real_escape_string($id) . "'");
        }

        $arrAnims = array();

        $arrAnims['customin'] = RevOperations::getCustomAnimations();

        $arrAnims['customout'] = RevOperations::getCustomAnimations('customout');

        $arrAnims['customfull'] = RevOperations::getFullCustomAnimations();



        return $arrAnims;
    }

    public static function getCustomAnimations($pre = 'customin')
    {
        $db = new UniteDBRev();



        $customAnimations = array();



        $result = $db->fetch(GlobalsRevSlider::$table_layer_anims);

        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $customAnimations[$pre . '-' . $value['id']] = $value['handle'];
            }
        }



        return $customAnimations;
    }

    public static function getFullCustomAnimations()
    {
        $db = new UniteDBRev();



        $customAnimations = array();



        $result = $db->fetch(GlobalsRevSlider::$table_layer_anims);

        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $customAnimations[$key]['id'] = $value['id'];

                $customAnimations[$key]['handle'] = $value['handle'];

                $customAnimations[$key]['params'] = Tools::jsonDecode(str_replace("'", '"', $value['params']), true);
            }
        }



        return $customAnimations;
    }

    public static function getCustomAnimationByHandle($handle)
    {
        $db = new UniteDBRev();



        $result = $db->fetch(GlobalsRevSlider::$table_layer_anims, "handle = '" . $handle . "'");

        if (!empty($result)) {
            return Tools::jsonDecode(str_replace("'", '"', $result[0]['params']), true);
        }



        return false;
    }

    public static function getFullCustomAnimationByID($id)
    {
        $db = new UniteDBRev();



        $result = $db->fetch(GlobalsRevSlider::$table_layer_anims, "id = '" . $id . "'");



        if (!empty($result)) {
            $customAnimations = array();

            $customAnimations['id'] = $result[0]['id'];

            $customAnimations['handle'] = $result[0]['handle'];

            $customAnimations['params'] = Tools::jsonDecode(str_replace("'", '"', $result[0]['params']), true);

            return $customAnimations;
        }



        return false;
    }

    /**
     * parse animation params
     * 5.0.5: added (R) for reverse
     */
    public static function parseCustomAnimationByArray($animArray, $is = 'start')
    {
        $retString = '';

        $reverse = (@RevsliderPrestashop::getIsset($animArray['x_' . $is . '_reverse']) && $animArray['x_' . $is . '_reverse'] == true) ? '(R)' : ''; //movex reverse
        if (@RevsliderPrestashop::getIsset($animArray['x_' . $is]) && $animArray['x_' . $is] !== '' && $animArray['x_' . $is] !== 'inherit') {
            $retString.= 'x:' . $animArray['x_' . $is] . $reverse . ';';
        } //movex
        $reverse = (@RevsliderPrestashop::getIsset($animArray['y_' . $is . '_reverse']) && $animArray['y_' . $is . '_reverse'] == true) ? '(R)' : ''; //movey reverse
        if (@RevsliderPrestashop::getIsset($animArray['y_' . $is]) && $animArray['y_' . $is] !== '' && $animArray['y_' . $is] !== 'inherit') {
            $retString.= 'y:' . $animArray['y_' . $is] . $reverse . ';';
        } //movey
        if (@RevsliderPrestashop::getIsset($animArray['z_' . $is]) && $animArray['z_' . $is] !== '' && $animArray['z_' . $is] !== 'inherit') {
            $retString.= 'z:' . $animArray['z_' . $is] . ';';
        } //movez

        $reverse = (@RevsliderPrestashop::getIsset($animArray['x_rotate_' . $is . '_reverse']) && $animArray['x_rotate_' . $is . '_reverse'] == true) ? '(R)' : ''; //rotationx reverse
        if (@RevsliderPrestashop::getIsset($animArray['x_rotate_' . $is]) && $animArray['x_rotate_' . $is] !== '' && $animArray['x_rotate_' . $is] !== 'inherit') {
            $retString.= 'rX:' . $animArray['x_rotate_' . $is] . $reverse . ';';
        } //rotationx
        $reverse = (@RevsliderPrestashop::getIsset($animArray['y_rotate_' . $is . '_reverse']) && $animArray['y_rotate_' . $is . '_reverse'] == true) ? '(R)' : ''; //rotationy reverse
        if (@RevsliderPrestashop::getIsset($animArray['y_rotate_' . $is]) && $animArray['y_rotate_' . $is] !== '' && $animArray['y_rotate_' . $is] !== 'inherit') {
            $retString.= 'rY:' . $animArray['y_rotate_' . $is] . $reverse . ';';
        } //rotationy
        $reverse = (@RevsliderPrestashop::getIsset($animArray['z_rotate_' . $is . '_reverse']) && $animArray['z_rotate_' . $is . '_reverse'] == true) ? '(R)' : ''; //rotationz reverse
        if (@RevsliderPrestashop::getIsset($animArray['z_rotate_' . $is]) && $animArray['z_rotate_' . $is] !== '' && $animArray['z_rotate_' . $is] !== 'inherit') {
            $retString.= 'rZ:' . $animArray['z_rotate_' . $is] . $reverse . ';';
        } //rotationz

        if (@RevsliderPrestashop::getIsset($animArray['scale_x_' . $is]) && $animArray['scale_x_' . $is] !== '' && $animArray['scale_x_' . $is] !== 'inherit') { //scalex
            $reverse = (@RevsliderPrestashop::getIsset($animArray['scale_x_' . $is . '_reverse']) && $animArray['scale_x_' . $is . '_reverse'] == true) ? '(R)' : ''; //scalex reverse
            $retString.= 'sX:';
            $retString.= ($animArray['scale_x_' . $is] == 0) ? 0 : $animArray['scale_x_' . $is];
            $retString.= $reverse;
            $retString.= ';';
        }
        if (@RevsliderPrestashop::getIsset($animArray['scale_y_' . $is]) && $animArray['scale_y_' . $is] !== '' && $animArray['scale_y_' . $is] !== 'inherit') { //scaley
            $reverse = (@RevsliderPrestashop::getIsset($animArray['scale_y_' . $is . '_reverse']) && $animArray['scale_y_' . $is . '_reverse'] == true) ? '(R)' : ''; //scaley reverse
            $retString.= 'sY:';
            $retString.= ($animArray['scale_y_' . $is] == 0) ? 0 : $animArray['scale_y_' . $is];
            $retString.= $reverse;
            $retString.= ';';
        }

        $reverse = (@RevsliderPrestashop::getIsset($animArray['skew_x_' . $is . '_reverse']) && $animArray['skew_x_' . $is . '_reverse'] == true) ? '(R)' : ''; //skewx reverse
        if (@RevsliderPrestashop::getIsset($animArray['skew_x_' . $is]) && $animArray['skew_x_' . $is] !== '' && $animArray['skew_x_' . $is] !== 'inherit') {
            $retString.= 'skX:' . $animArray['skew_x_' . $is] . $reverse . ';';
        } //skewx
        $reverse = (@RevsliderPrestashop::getIsset($animArray['skew_y_' . $is . '_reverse']) && $animArray['skew_y_' . $is . '_reverse'] == true) ? '(R)' : ''; //skewy reverse
        if (@RevsliderPrestashop::getIsset($animArray['skew_y_' . $is]) && $animArray['skew_y_' . $is] !== '' && $animArray['skew_y_' . $is] !== 'inherit') {
            $retString.= 'skY:' . $animArray['skew_y_' . $is] . $reverse . ';';
        } //skewy

        if (@RevsliderPrestashop::getIsset($animArray['opacity_' . $is]) && $animArray['opacity_' . $is] !== '' && $animArray['opacity_' . $is] !== 'inherit') { //captionopacity
            $retString.= 'opacity:';
            $retString.= ($animArray['opacity_' . $is] == 0) ? 0 : $animArray['opacity_' . $is] / 100;
            $retString.= ';';
        }

        if ($retString == '') { //we do not have animations set, so set them here
        }

        if ($is == 'start') {
            $retString .= 's:' . RevSliderFunctions::getVal($animArray, 'speed', 300) . ';';
            $retString .= 'e:' . RevSliderFunctions::getVal($animArray, 'easing', 'easeOutExpo') . ';';
        } else {
            $es = RevSliderFunctions::getVal($animArray, 'endspeed');
            $ee = trim(RevSliderFunctions::getVal($animArray, 'endeasing'));
            if (!empty($es)) {
                $retString .= 's:' . $es . ';';
                if (!empty($ee) && $ee !== 'nothing') {
                    $retString .= 'e:' . $ee . ';';
                }
            }
        }

        return $retString;
    }

    /**
     * parse mask params
     * @since: 5.0
     */
    public static function parseCustomMaskByArray($animArray, $is = 'start')
    {
        $retString = '';
        $reverse = (@RevsliderPrestashop::getIsset($animArray['mask_x_' . $is . '_reverse']) && $animArray['mask_x_' . $is . '_reverse'] == true) ? '(R)' : '';
        if (@RevsliderPrestashop::getIsset($animArray['mask_x_' . $is]) && $animArray['mask_x_' . $is] !== '') {
            $retString.= 'x:' . $animArray['mask_x_' . $is] . $reverse . ';';
        }
        $reverse = (@RevsliderPrestashop::getIsset($animArray['mask_y_' . $is . '_reverse']) && $animArray['mask_y_' . $is . '_reverse'] == true) ? '(R)' : '';
        if (@RevsliderPrestashop::getIsset($animArray['mask_y_' . $is]) && $animArray['mask_y_' . $is] !== '') {
            $retString.= 'y:' . $animArray['mask_y_' . $is] . $reverse . ';';
        }
        if (@RevsliderPrestashop::getIsset($animArray['mask_speed_' . $is]) && $animArray['mask_speed_' . $is] !== '') {
            $retString.= 's:' . $animArray['mask_speed_' . $is] . ';';
        }
        if (@RevsliderPrestashop::getIsset($animArray['mask_ease_' . $is]) && $animArray['mask_ease_' . $is] !== '') {
            $retString.= 'e:' . $animArray['mask_ease_' . $is] . ';';
        }

        return $retString;
    }

    public function getArrCaptionClasses($contentCSS)
    {
        $parser = new UniteCssParserRev();

        $parser->initContent($contentCSS);

        $arrCaptionClasses = $parser->getArrClasses();

        return($arrCaptionClasses);
    }

    /**
     *
     * get all font family types
     */
    public function getArrFontFamilys($slider = false)
    {

        //Web Safe Fonts
        $fonts = array(
            // GOOGLE Loaded Fonts
            array('type' => 'websafe', 'version' => __('Loaded Google Fonts', 'revslider'), 'label' => 'Dont Show Me'),
            //Serif Fonts
            array('type' => 'websafe', 'version' => __('Serif Fonts', 'revslider'), 'label' => 'Georgia, serif'),
            array('type' => 'websafe', 'version' => __('Serif Fonts', 'revslider'), 'label' => '"Palatino Linotype", "Book Antiqua", Palatino, serif'),
            array('type' => 'websafe', 'version' => __('Serif Fonts', 'revslider'), 'label' => '"Times New Roman", Times, serif'),
            //Sans-Serif Fonts
            array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => 'Arial, Helvetica, sans-serif'),
            array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => '"Arial Black", Gadget, sans-serif'),
            array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => '"Comic Sans MS", cursive, sans-serif'),
            array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => 'Impact, Charcoal, sans-serif'),
            array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif'),
            array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => 'Tahoma, Geneva, sans-serif'),
            array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => '"Trebuchet MS", Helvetica, sans-serif'),
            array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => 'Verdana, Geneva, sans-serif'),
            //Monospace Fonts
            array('type' => 'websafe', 'version' => __('Monospace Fonts', 'revslider'), 'label' => '"Courier New", Courier, monospace'),
            array('type' => 'websafe', 'version' => __('Monospace Fonts', 'revslider'), 'label' => '"Lucida Console", Monaco, monospace')
        );

        /* if($slider !== false){
          $font_custom = $slider->getParam("google_font","");

          if(!is_array($font_custom)) $font_custom = array($font_custom); //backwards compability

          if(is_array($font_custom)){
          foreach($font_custom as $key => $curFont){
          $font = $this->cleanFontStyle(Tools::stripslashes($curFont));

          if($font != false)
          $font_custom[$key] = array('version' => __('Depricated Google Fonts', 'revslider'), 'label' => $font);
          else
          unset($font_custom[$key]);
          }
          $fonts = array_merge($font_custom, $fonts);
          }
          } */
        $googlefonts = array();
        include(WP_CONTENT_DIR . '/inc_php/googlefonts.php');


        foreach ($googlefonts as $f => $val) {
            $fonts[] = array('type' => 'googlefont', 'version' => __('Google Fonts', 'revslider'), 'label' => $f, 'variants' => $val['variants'], 'subsets' => $val['subsets']);
        }

        return $fonts;
    }

    public function cleanFontStyle($font)
    {
        $url = preg_match('/href=["\']?([^"\'>]+)["\']?/', $font, $match);

        if (!@RevsliderPrestashop::getIsset($match[1])) {
            return false;
        }

        $info = parse_url($match[1]);

        if (@RevsliderPrestashop::getIsset($info['query'])) {
            $font = str_replace(array('family=', '+'), array('', ' '), $info['query']);

            $font = explode(':', $font);

            return (strpos($font['0'], ' ') !== false) ? '"' . $font['0'] . '"' : $font['0'];
        }



        return false;
    }

    private function getHtmlSelectCaptionClasses($contentCSS)
    {
        $arrCaptions = $this->getArrCaptionClasses($contentCSS);

        $htmlSelect = UniteFunctionsRev::getHTMLSelect($arrCaptions, "", "id='layer_caption' name='layer_caption'", true);

        return($htmlSelect);
    }

    public function getCaptionsContent()
    {
        $result = $this->db->fetch(GlobalsRevSlider::$table_css);

        $contentCSS = UniteCssParserRev::parseDbArrayToCss($result);

        return($contentCSS);
    }

    public static function getCaptionsContentArray($handle = false)
    {
        $db = new UniteDBRev();

        $result = $db->fetch(GlobalsRevSlider::$table_css);

        $contentCSS = UniteCssParserRev::parseDbArrayToArray($result, $handle);


        return($contentCSS);
    }

    public static function getStaticCss()
    {
        // if ( is_multisite() ){
        // 	if(!get_site_option('revslider-static-css')){
        // 		$contentCSS = @Tools::file_get_contents(GlobalsRevSlider::$filepath_static_captions);
        // 		self::updateStaticCss($contentCSS);
        // 	}
        // 	$contentCSS = get_site_option('revslider-static-css', '');
        // }else{
        // 	if(!get_option('revslider-static-css')){
        // 		$contentCSS = @Tools::file_get_contents(GlobalsRevSlider::$filepath_static_captions);
        // 		self::updateStaticCss($contentCSS);
        // 	}
        // 	$contentCSS = get_option('revslider-static-css', '');
        // }
        // return($contentCSS);

        $contentCSS = @Tools::file_get_contents(GlobalsRevSlider::$filepath_static_captions);

        return($contentCSS);
    }

    public static function updateStaticCss($content)
    {
        $content = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), trim($content));

        UniteFunctionsRev::writeFile($content, GlobalsRevSlider::$filepath_static_captions);

        $static = self::getStaticCss();



        return $static;
    }

    public function getDynamicCss()
    {
        $contentCSS = Tools::file_get_contents(GlobalsRevSlider::$filepath_dynamic_captions);

        return($contentCSS);
    }

    public function revClearQuote($content)
    {
        return str_replace("'", '"', $content);
    }

    /**
     *
     * insert captions css file content
     * @return new captions html select
     */
    public function insertCaptionsContentData($content)
    {
        if (!@RevsliderPrestashop::getIsset($content['handle']) || !@RevsliderPrestashop::getIsset($content['idle']) || !@RevsliderPrestashop::getIsset($content['hover'])) {
            return false;
        } // || !@RevsliderPrestashop::getIsset($content['advanced'])

        $db = new RevSliderDB();

        $handle = $content['handle'];

        if (!@RevsliderPrestashop::getIsset($content['hover'])) {
            $content['hover'] = '';
        }
        if (!@RevsliderPrestashop::getIsset($content['advanced'])) {
            $content['advanced'] = array();
        }
        if (!@RevsliderPrestashop::getIsset($content['advanced']['idle'])) {
            $content['advanced']['idle'] = array();
        }
        if (!@RevsliderPrestashop::getIsset($content['advanced']['hover'])) {
            $content['advanced']['hover'] = array();
        }

        $arrInsert = array();
        $arrInsert["handle"] = '.tp-caption.' . $handle;
        $arrInsert["params"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $content['idle'])));
        $arrInsert["hover"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $content['hover'])));

        if (!@RevsliderPrestashop::getIsset($content['settings'])) {
            $content['settings'] = array();
        }
        $content['settings']['version'] = 'custom';
        $content['settings']['translated'] = '5'; // translated to version 5 currently
        $arrInsert["settings"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $content['settings'])));

        $arrInsert["advanced"] = array();
        $arrInsert["advanced"]['idle'] = $content['advanced']['idle'];
        $arrInsert["advanced"]['hover'] = $content['advanced']['hover'];
        $arrInsert["advanced"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $arrInsert["advanced"])));

        $db->insert(RevSliderGlobals::$table_css, $arrInsert);

        //output captions array
        $arrCaptions = RevSliderCssParser::getCaptionsSorted();

        return($arrCaptions);
    }

    public function revAddslashes($content)
    {
        if (is_array($content) && !get_magic_quotes_gpc()) {
            foreach ($content as $key => $cont) {
                $content[$key] = addslashes($cont);
            }
        }

        return $content;
    }

    public function updateCaptionsContentData($content)
    {
        if (@RevsliderPrestashop::getIsset($content['handle'])) {
            $db = new UniteDBRev();

            $handle = $content['handle'];

            $arrUpdate = array();

            //$arrUpdate["params"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $content['params'])));
            //$arrUpdate["params"] = Tools::stripslashes(Tools::jsonEncode($content['params']));
            //$arrUpdate["hover"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', @$content['hover'])));
            //$arrUpdate["settings"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', @$content['settings'])));


            $arrUpdate["params"] = Tools::jsonEncode($this->revClearQuote(@$content['params']));

            $arrUpdate["hover"] = Tools::jsonEncode($this->revClearQuote(@$content['hover']));

            $arrUpdate["settings"] = Tools::jsonEncode($this->revClearQuote(@$content['settings']));

            if (get_magic_quotes_gpc()) {
                $arrUpdate["params"] = Tools::stripslashes(Tools::jsonEncode($content['params']));

                $arrUpdate["hover"] = Tools::stripslashes(Tools::jsonEncode(@$content['hover']));

                $arrUpdate["settings"] = Tools::stripslashes(Tools::jsonEncode(@$content['settings']));
            }



            $result = $db->update(GlobalsRevSlider::$table_css, $arrUpdate, array('handle' => '.tp-caption.' . $handle));
        }



        $this->updateDynamicCaptions();



        //output captions array

        $operations = new RevOperations();

        $cssContent = $operations->getCaptionsContent();

        $arrCaptions = $operations->getArrCaptionClasses($cssContent);

        return($arrCaptions);
    }

    public function deleteCaptionsContentData($handle)
    {
        $db = new UniteDBRev();



        $db->delete(GlobalsRevSlider::$table_css, "handle='.tp-caption." . $handle . "'");



        $this->updateDynamicCaptions();



        //output captions array

        $operations = new RevOperations();

        $cssContent = $operations->getCaptionsContent();

        $arrCaptions = $operations->getArrCaptionClasses($cssContent);

        return($arrCaptions);
    }

    public static function updateDynamicCaptions($full = false)
    {
        if ($full) {
            $captions = array();
            $captions = RevOperations::getCaptionsContentArray();
            $styles = UniteCssParserRev::parseArrayToCss($captions, "\n");
            UniteFunctionsRev::writeFile($styles, GlobalsRevSlider::$filepath_dynamic_captions);
        } else {
            $slider = new RevSlider();
            $arrSliders = $slider->getArrSliders();
            $classes = array();
            if (!empty($arrSliders)) {
                foreach ($arrSliders as $slider) {
                    try {
                        $slides = $slider->getSlides();
                        if (!empty($slides)) {
                            foreach ($slides as $slide) {
                                $layers = $slide->getLayers();
                                if (!empty($layers)) {
                                    foreach ($layers as $layer) {
                                        if (@RevsliderPrestashop::getIsset($layer['style'])) {
                                            if (!empty($layer['style'])) {
                                                $classes[$layer['style']] = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $errorMessage = "ERROR: " . $e->getMessage();
                    }
                }
            }
            if (!empty($classes)) {
                $captions = array();
                foreach ($classes as $class => $val) {
                    $captionCheck = RevOperations::getCaptionsContentArray($class);
                    if (!get_magic_quotes_gpc()) {
                        $captionCheck = str_replace('"', "'", $captionCheck);
                    } else {
                        $captionCheck = str_replace('"', "'", $captionCheck);
                        $captionCheck = str_replace('\\\\', "", $captionCheck);
                    }
                    if (!empty($captionCheck)) {
                        $captions[] = $captionCheck;
                    }
                }
                $styles = UniteCssParserRev::parseArrayToCss($captions, "\n");
                UniteFunctionsRev::writeFile($styles, GlobalsRevSlider::$filepath_dynamic_captions);
            }
        }
    }

    public static function getCaptionsCssContentArray()
    {
        if (file_exists(GlobalsRevSlider::$filepath_captions)) {
            $contentCSS = Tools::file_get_contents(GlobalsRevSlider::$filepath_captions);
        } elseif (file_exists(GlobalsRevSlider::$filepath_captions_original)) {
            $contentCSS = Tools::file_get_contents(GlobalsRevSlider::$filepath_captions_original);
        } elseif (file_exists(GlobalsRevSlider::$filepath_backup . 'captions.css')) {
            $contentCSS = Tools::file_get_contents(GlobalsRevSlider::$filepath_backup . 'captions.css');
        } elseif (file_exists(GlobalsRevSlider::$filepath_backup . 'captions-original.css')) {
            $contentCSS = Tools::file_get_contents(GlobalsRevSlider::$filepath_backup . 'captions-original.css');
        } else {
            UniteFunctionsRev::throwError("No captions.css found! This installation is incorrect, please make sure to reupload the Slider Revolution plugin and try again!");
        }
        $result = UniteCssParserRev::parseCssToArray($contentCSS);
        return($result);
    }

    public static function importCaptionsCssContentArray()
    {
        $db = new UniteDBRev();

        $css = self::getCaptionsCssContentArray();

        $dbprefix = _DB_PREFIX_;

        $static = array();

        if (is_array($css) && $css !== false && count($css) > 0) {
            foreach ($css as $class => $styles) {
                $class = trim($class);
                if ((strpos($class, ':hover') === false && strpos($class, ':') !== false) ||
                    strpos($class, " ") !== false ||
                    strpos($class, ".tp-caption") === false ||
                    (strpos($class, ".") === false || strpos($class, "#") !== false) ||
                    strpos($class, ">") !== false) {
                    $static[$class] = $styles;
                    continue;
                }

                if (strpos($class, ':hover') !== false) {
                    $class = trim(str_replace(':hover', '', $class));

                    $arrInsert = array();

                    $arrInsert["hover"] = Tools::jsonEncode($styles);

                    $arrInsert["settings"] = Tools::jsonEncode(array('hover' => 'true'));
                } else {
                    if (@RevsliderPrestashop::getIsset($styles['font-family']) && !empty($styles['font-family'])) {
                        $styles['font-family'] = str_replace("'", '"', $styles['font-family']);
                    }

                    $arrInsert = array();

                    $arrInsert["params"] = Tools::jsonEncode($styles);
                }

                //check if class exists



                $result = $db->fetch(GlobalsRevSlider::$table_css, "handle = '" . $class . "'");



                if (!empty($result)) { //update
                    $db->update(GlobalsRevSlider::$table_css, $arrInsert, array('handle' => $class));
                } else { //insert
                    $arrInsert["handle"] = $class;

                    $db->insert(GlobalsRevSlider::$table_css, $arrInsert);
                }
            }
        }



        if (!empty($static)) { //save static into static-captions.css
            $css = UniteCssParserRev::parseStaticArrayToCss($static);

            $static_cur = RevOperations::getStaticCss(); //get the open sans line!
            //$css = $static_cur."\n".$css;



            self::updateStaticCss($css);
        }
    }

    public static function moveOldCaptionsCss()
    {
        if (file_exists(GlobalsRevSlider::$filepath_captions_original)) {
            $success = @rename(GlobalsRevSlider::$filepath_captions_original, GlobalsRevSlider::$filepath_backup . '/captions-original.css');
        }



        if (file_exists(GlobalsRevSlider::$filepath_captions)) {
            $success = @rename(GlobalsRevSlider::$filepath_captions, GlobalsRevSlider::$filepath_backup . '/captions.css');
        }
    }

    public function previewOutput($sliderID, $output = null)
    {
        if ($sliderID == "empty_output") {
            $this->loadingMessageOutput();
            exit();
        }

        if ($output == null) {
            $output = new RevSliderOutput();
        }

        RevGlobalObject::setVar('sliderID', $sliderID);
        RevGlobalObject::setVar('output', $output);

        require_once ABSPATH . '/views/preview_output.php';
    }

    public function previewOutputMarkup($sliderID, $output = null)
    {
        if ($sliderID == "empty_output") {
            $this->loadingMessageOutput();
            exit();
        }

        if ($output == null) {
            $output = new RevSliderOutput();
        }

        RevGlobalObject::setVar('sliderID', $sliderID);
        RevGlobalObject::setVar('output', $output);

        require_once ABSPATH . '/views/preview_output_markup.php';
    }

    public function loadingMessageOutput()
    {
        echo '<div class="message_loading_preview">';
        _e("Loading Preview...", REVSLIDER_TEXTDOMAIN);
        echo '</div>';
    }

    public function putSlidePreviewByData($data)
    {
        if ($data == "empty_output") {
            $this->loadingMessageOutput();
            exit();
        }

        $data = UniteFunctionsRev::jsonDecodeFromClientSide($data);
        $slideID = $data["slideid"];
        $slide = new RevSlide();
        $slide->initByID($slideID);
        $sliderID = $slide->getSliderID();

        $output = new RevSliderOutput();
        $output->setOneSlideMode($data);

        $this->previewOutput($sliderID, $output);
    }

    public function updateGeneralSettings($data)
    {
        $strSettings = serialize($data);

        $params = new RevSliderParams();

        $params->updateFieldInDB("general", $strSettings);
    }

    public static function getGeneralSettingsValues()
    {
        $params = new RevSliderParams();

        $strSettings = $params->getFieldFromDB("general");



        $arrValues = array();

        if (!empty($strSettings)) {
            $arrValues = unserialize($strSettings);
        }



        return($arrValues);
    }

    public function updateLangFilter($data)
    {
        $lang = UniteFunctionsRev::getVal($data, "lang");

        $sliderID = UniteFunctionsRev::getVal($data, "sliderid");

        $context = Context::getContext();

        if (!@RevsliderPrestashop::getIsset($context->cookie)) {
            return(false);
        }

        $context->cookie->__set('revslider_lang_filter', $lang);

        return($sliderID);
    }

    public function getLangFilterValue()
    {
        $context = Context::getContext();

        if (!@RevsliderPrestashop::getIsset($context->cookie->revslider_lang_filter) || empty($context->cookie->revslider_lang_filter)) {
            return("all");
        }



//		$langFitler = UniteFunctionsRev::getVal($_SESSION, "revslider_lang_filter","all");
        $langFitler = $context->cookie->revslider_lang_filter;



        return($langFitler);
    }

    public function modifyCustomSliderParams($data)
    {
        $settigns = new UniteSettingsRev();



        $arrNames = array("width", "height",
            "responsitive_w1", "responsitive_sw1",
            "responsitive_w2", "responsitive_sw2",
            "responsitive_w3", "responsitive_sw3",
            "responsitive_w4", "responsitive_sw4",
            "responsitive_w5", "responsitive_sw5",
            "responsitive_w6", "responsitive_sw6");



        $arrMain = $data["main"];

        foreach ($arrNames as $name) {
            if (array_key_exists($name, $arrMain)) {
                $arrMain[$name] = $settigns->modifyValueByDatatype($arrMain[$name], UniteSettingsRev::DATATYPE_NUMBER);
            }
        }



        $arrMain["fullscreen_offset_container"] = $settigns->modifyValueByDatatype($arrMain["fullscreen_offset_container"], UniteSettingsRev::DATATYPE_STRING);



        //$arrMain["auto_height"] = $settigns->modifyValueByDatatype($arrMain["auto_height"], UniteSettingsRev::DATATYPE_STRING);

        $data["main"] = $arrMain;



        return($data);
    }

    public static function getPostTypesWithCatsForClient()
    {
        $arrPostTypes = UniteFunctionsWPRev::getPostTypesWithCats();



        $globalCounter = 0;



        $arrOutput = array();

        foreach ($arrPostTypes as $postType => $arrTaxWithCats) {
            $arrCats = array();

            foreach ($arrTaxWithCats as $tax) {
                $taxName = $tax["name"];

                $taxTitle = $tax["title"];

                $globalCounter++;

                $arrCats["option_disabled_" . $globalCounter] = "---- " . $taxTitle . " ----";

                foreach ($tax["cats"] as $catID => $catTitle) {
                    $arrCats[$taxName . "_" . $catID] = $catTitle;
                }
            }

            $arrOutput[$postType] = $arrCats;
        }


        return($arrOutput);
    }

    public static function getCleanFontImport($font)
    {
        $setBase = (is_ssl()) ? "https://" : "http://";

        if (strpos($font, "href=") === false) {
            //fallback for old versions
            return '<link href="' . $setBase . 'fonts.googleapis.com/css?family=' . $font . '" rel="stylesheet" type="text/css" media="all" />';
            //id="rev-google-font"
        } else {
            $font = str_replace(array('http://', 'https://'), array($setBase, $setBase), $font);

            return Tools::stripslashes($font);
        }
    }

    public function checkPurchaseVerification($data)
    {
        $wp_version = _PS_VERSION_;
        $siteurl = Context::getcontext()->shop->getBaseURL();
        $response = wp_remote_post('http://updates.themepunch.tools/activate.php', array(
            'method' => 'POST',
            'user-agent' => 'Prestashop/' . $wp_version . '; ' . $siteurl,
            'body' => array(
                'code' => urlencode($data['code']),
                'product' => urlencode('revslider_prestashop'),
            ),
            'headers' => array()
        ));

        $response_code = wp_remote_retrieve_response_code($response);
        $version_info = wp_remote_retrieve_body($response);

        if ($response_code != 200 || empty($version_info)) {
            return false;
        }

        if ($version_info == 'valid') {
            Configuration::updateValue('revslider-valid', 'true');
            Configuration::updateValue('revslider-code', $data['code']);
            return true;
        } elseif ($version_info == 'exist') {
            RevSliderFunctions::throwError(__('Purchase Code already registered!', 'revslider'));
        } else {
            return false;
        }
    }

    public function doPurchaseDeactivation($data)
    {
        $wp_version = _PS_VERSION_;

        $code = Configuration::get('revslider-code');

        $siteurl = Context::getcontext()->shop->getBaseURL();

        $response = wp_remote_post('http://updates.themepunch.tools/deactivate.php', array(
            'method' => 'POST',
            'user-agent' => 'Prestashop/' . $wp_version . '; ' . $siteurl,
            'body' => array(
                'code' => urlencode($code),
                'product' => urlencode('revslider_prestashop')
            ),
            'headers' => array()
        ));

        $response_code = wp_remote_retrieve_response_code($response);
        $version_info = wp_remote_retrieve_body($response);

        if ($response_code != 200 || empty($version_info)) {
            return false;
        }

        if ($version_info == 'valid') {
            Configuration::updateValue('revslider-valid', 'false');
            return true;
        } else {
            return false;
        }
    }

    /**
     * these are the specific slider settings, which the user can switch between, for easier usage
     * @since: 5.0
     */
    public static function getPresetSettings()
    {
        /**
         * List of Elements based on version 5.0 (may be incomplete)
          arrows_always_on "true"
          auto_height "off"
          background_color "#333"
          background_dotted_overlay "none"
          background_image "http://server.local/revslider/wp-content/uploads/"
          bg_fit "cover"
          bg_position "center center"
          bg_repeat "no-repeat"
          bullets_align_hor "center"
          bullets_align_vert "bottom"
          bullets_always_on "true"
          bullets_direction "horizontal"
          bullets_offset_hor "0"
          bullets_offset_vert "20"
          bullets_space "5"
          carousel_borderr "0"
          carousel_borderr_unit "px"
          carousel_fadeout "off"
          carousel_hposition "center"
          carousel_infinity false
          carousel_maxitems "3"
          carousel_maxrotation "0"
          carousel_rotation "off"
          carousel_scale "off"
          carousel_scaledown "50"
          carousel_space "0"
          carousel_stretch "off"
          carousel_varyrotate "off"
          carousel_varyscale "off"
          carousel_vposition "center"
          client_action "import_slider"
          custom_css ""
          custom_javascript "jQuery(window).on('scrol...return ismobile;\n }"
          delay "9000"
          disable_kenburns_on_mobile "off"
          disable_on_mobile "off"
          disable_parallax_mobile "off"
          drag_block_vertical "off"
          enable_arrows "on"
          enable_bullets "on"
          enable_progressbar "off"
          enable_tabs "off"
          enable_thumbnails "off"
          export_dummy_images false
          first_transition_active "on"
          first_transition_duration "300"
          first_transition_slot_amount "7"
          first_transition_type "fade"
          full_screen_align_force "off"
          fullscreen_min_height ""
          fullscreen_offset_container ""
          fullscreen_offset_size ""
          hide_all_layers_under "0"
          hide_arrows "200"
          hide_arrows_on_mobile "off"
          hide_bullets "200"
          hide_bullets_on_mobile "off"
          hide_defined_layers_under "0"
          hide_slider_under "0"
          hide_tabs "200"
          hide_thumbs "200"
          hide_thumbs_delay_mobile "1500"
          hide_thumbs_on_mobile "off"
          hide_thumbs_under_resolution "0"
          image_source_type "full"
          jquery_noconflict "on"
          js_to_body "true"
          keyboard_navigation "off"
          lazy_load_type "none"
          leftarrow_align_hor "left"
          leftarrow_align_vert "center"
          leftarrow_offset_hor "10"
          leftarrow_offset_vert "0"
          loop_slide "off"
          margin_bottom "0"
          margin_left "0"
          margin_right "0"
          margin_top "0"
          min_height "0"
          navigation_arrow_style "round"
          navigation_bullets_style "round"
          next_slide_on_window_focus "off"
          nonce "a638dbd494"
          output_type "none"
          padding "0"
          parallax_bg_freeze "off"
          parallax_level_1 "5"
          parallax_level_10 "50"
          parallax_level_2 "10"
          parallax_level_3 "15"
          parallax_level_4 "20"
          parallax_level_5 "25"
          parallax_level_6 "30"
          parallax_level_7 "35"
          parallax_level_8 "40"
          parallax_level_9 "45"
          parallax_type "mouse"
          position "center"
          progress_height "5"
          progress_opa "15"
          progressbar_color "#000000"
          rightarrow_align_hor "right"
          rightarrow_align_vert "center"
          rightarrow_offset_hor "10"
          rightarrow_offset_vert "0"
          shadow_type "0"
          show_alternate_image ""
          show_alternative_type "off"
          show_background_image "on"
          show_timerbar "top"
          shuffle "on"
          simplify_ie8_ios4 "off"
          sliderid "56"
          span_tabs_wrapper "off"
          span_thumbnails_wrapper "off"
          spinner_color "#FFFFFF"
          start_js_after_delay "0"
          start_with_slide "1"
          stop_after_loops "0"
          stop_at_slide "2"
          stop_on_hover "on"
          stop_slider "on"
          swipe_min_touches "1"
          swipe_velocity "75"
          tabs_align_hor "center"
          tabs_align_vert "bottom"
          tabs_always_on "true"
          tabs_amount "5"
          tabs_direction "horizontal"
          tabs_height "50"
          tabs_inner_outer "inner"
          tabs_offset_hor "0"
          tabs_offset_vert "20"
          tabs_padding "5"
          tabs_space "5"
          tabs_style "custom"
          tabs_width "100"
          tabs_wrapper_color "transparent"
          tabs_wrapper_opacity "5"
          thumb_amount "4"
          thumb_height "75"
          thumb_width "120"
          thumbnail_direction "horizontal"
          thumbnails_align_hor "center"
          thumbnails_align_vert "bottom"
          thumbnails_inner_outer "inner"
          thumbnails_offset_hor "0"
          thumbnails_offset_vert "20"
          thumbnails_padding "5"
          thumbnails_space "5"
          thumbnails_wrapper_color "transparent"
          thumbnails_wrapper_opacity "5"
          thumbs_always_on "true"
          touchenabled "on"
          update_animations "true"
          update_static_captions "true"
          use_parallax "off"
          use_spinner "0"
          use_wpml false
         * */
        $presets = array();

        //ThemePunch default presets are added here directly
        //preset -> standardpreset || heropreset || carouselpreset

        $presets[] = array(
            'settings' => array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/slideshow_auto_layout.png', 'name' => 'Slideshow-Auto', 'preset' => 'standardpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'on',
                'stop_slider' => 'off',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'round',
                'arrows_always_on' => 'true',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'on',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'on',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'round',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '100',
                'thumb_height' => '50',
                'thumb_width_min' => '100',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '20',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'auto',
                'width' => '1240',
                'width_notebook' => '1024',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '600',
                'height_notebook' => '600',
                'height_tablet' => '500',
                'height_mobile' => '400',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );

        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/slideshow_auto_layout.png', 'name' => 'Slideshow-Full-Width', 'preset' => 'standardpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'on',
                'stop_slider' => 'off',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'round',
                'arrows_always_on' => 'true',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'on',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'on',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'round',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '100',
                'thumb_height' => '50',
                'thumb_width_min' => '100',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '20',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullwidth',
                'width' => '1240',
                'width_notebook' => '1024',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '600',
                'height_notebook' => '600',
                'height_tablet' => '500',
                'height_mobile' => '400',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );

        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/slideshow_auto_layout.png', 'name' => 'Slideshow-Full-Screen', 'preset' => 'standardpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'on',
                'stop_slider' => 'off',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'round',
                'arrows_always_on' => 'true',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'on',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'on',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'round',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '100',
                'thumb_height' => '50',
                'thumb_width_min' => '100',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '20',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullscreen',
                'width' => '1240',
                'width_notebook' => '1024',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '600',
                'height_notebook' => '600',
                'height_tablet' => '500',
                'height_mobile' => '400',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );

        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/thumb_auto1.png', 'name' => 'Thumbs-Bottom-Auto', 'preset' => 'standardpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'off',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'navbar',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'on',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'on',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '50',
                'thumb_height' => '50',
                'thumb_width_min' => '50',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '20',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'auto',
                'width' => '1240',
                'width_notebook' => '1024',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '600',
                'height_notebook' => '600',
                'height_tablet' => '500',
                'height_mobile' => '400',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );

        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/thumbs_left_auto.png', 'name' => 'Thumbs-Left-Auto', 'preset' => 'standardpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'off',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'navbar',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'right',
                'leftarrow_align_vert' => 'bottom',
                'leftarrow_offset_hor' => '40',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'bottom',
                'rightarrow_offset_hor' => '0',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'on',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'vertical',
                'thumb_width' => '50',
                'thumb_height' => '50',
                'thumb_width_min' => '50',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'on',
                'thumbs_under_hidden' => '778',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'left',
                'thumbnails_align_vert' => 'center',
                'thumbnails_offset_hor' => '20',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'auto',
                'width' => '1240',
                'width_notebook' => '1024',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '600',
                'height_notebook' => '600',
                'height_tablet' => '500',
                'height_mobile' => '400',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );

        $presets[] = array(
            'settings' => array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/thumbs_right_auto.png', 'name' => 'Thumbs-Right-Auto', 'preset' => 'standardpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'off',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'navbar',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'bottom',
                'leftarrow_offset_hor' => '0',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'left',
                'rightarrow_align_vert' => 'bottom',
                'rightarrow_offset_hor' => '40',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'on',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'vertical',
                'thumb_width' => '50',
                'thumb_height' => '50',
                'thumb_width_min' => '50',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'on',
                'thumbs_under_hidden' => '778',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'right',
                'thumbnails_align_vert' => 'center',
                'thumbnails_offset_hor' => '20',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'auto',
                'width' => '1240',
                'width_notebook' => '1024',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '600',
                'height_notebook' => '600',
                'height_tablet' => '500',
                'height_mobile' => '400',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/scroll_fullscreen.png', 'name' => 'Vertical-Bullet-Full-Screen', 'preset' => 'standardpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'off',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'off',
                'navigation_arrow_style' => 'navbar',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'bottom',
                'leftarrow_offset_hor' => '0',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'left',
                'rightarrow_align_vert' => 'bottom',
                'rightarrow_offset_hor' => '40',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'on',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'vertical',
                'bullets_always_on' => 'false',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'right',
                'bullets_align_vert' => 'center',
                'bullets_offset_hor' => '30',
                'bullets_offset_vert' => '0',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'vertical',
                'thumb_width' => '50',
                'thumb_height' => '50',
                'thumb_width_min' => '50',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'on',
                'thumbs_under_hidden' => '778',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'right',
                'thumbnails_align_vert' => 'center',
                'thumbnails_offset_hor' => '20',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullscreen',
                'width' => '1240',
                'width_notebook' => '1024',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '600',
                'height_notebook' => '600',
                'height_tablet' => '500',
                'height_mobile' => '400',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/wide_fullscreen.png', 'name' => 'Wide-Full-Screen', 'preset' => 'heropreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'off',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'off',
                'navigation_arrow_style' => 'navbar',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'bottom',
                'leftarrow_offset_hor' => '0',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'left',
                'rightarrow_align_vert' => 'bottom',
                'rightarrow_offset_hor' => '40',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'on',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'vertical',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'right',
                'bullets_align_vert' => 'center',
                'bullets_offset_hor' => '30',
                'bullets_offset_vert' => '0',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'vertical',
                'thumb_width' => '50',
                'thumb_height' => '50',
                'thumb_width_min' => '50',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'on',
                'thumbs_under_hidden' => '778',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'right',
                'thumbnails_align_vert' => 'center',
                'thumbnails_offset_hor' => '20',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullscreen',
                'width' => '1400',
                'width_notebook' => '1240',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '868',
                'height_notebook' => '768',
                'height_tablet' => '960',
                'height_mobile' => '720',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/wide_fullscreen.png', 'name' => 'Wide-Full-Width', 'preset' => 'heropreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'off',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'off',
                'navigation_arrow_style' => 'navbar',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'bottom',
                'leftarrow_offset_hor' => '0',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'left',
                'rightarrow_align_vert' => 'bottom',
                'rightarrow_offset_hor' => '40',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'on',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'vertical',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'right',
                'bullets_align_vert' => 'center',
                'bullets_offset_hor' => '30',
                'bullets_offset_vert' => '0',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'vertical',
                'thumb_width' => '50',
                'thumb_height' => '50',
                'thumb_width_min' => '50',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'on',
                'thumbs_under_hidden' => '778',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'right',
                'thumbnails_align_vert' => 'center',
                'thumbnails_offset_hor' => '20',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullwidth',
                'width' => '1400',
                'width_notebook' => '1240',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '600',
                'height_notebook' => '500',
                'height_tablet' => '400',
                'height_mobile' => '400',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/wide_fullscreen.png', 'name' => 'Regular-Full-Screen', 'preset' => 'heropreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'off',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'off',
                'navigation_arrow_style' => 'navbar',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'bottom',
                'leftarrow_offset_hor' => '0',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'left',
                'rightarrow_align_vert' => 'bottom',
                'rightarrow_offset_hor' => '40',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'on',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'vertical',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'right',
                'bullets_align_vert' => 'center',
                'bullets_offset_hor' => '30',
                'bullets_offset_vert' => '0',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'vertical',
                'thumb_width' => '50',
                'thumb_height' => '50',
                'thumb_width_min' => '50',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'on',
                'thumbs_under_hidden' => '778',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'right',
                'thumbnails_align_vert' => 'center',
                'thumbnails_offset_hor' => '20',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullscreen',
                'width' => '1240',
                'width_notebook' => '1024',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '868',
                'height_notebook' => '768',
                'height_tablet' => '960',
                'height_mobile' => '720',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/wide_fullscreen.png', 'name' => 'Regular-Full-Width', 'preset' => 'heropreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'off',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'off',
                'navigation_arrow_style' => 'navbar',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'bottom',
                'leftarrow_offset_hor' => '0',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'left',
                'rightarrow_align_vert' => 'bottom',
                'rightarrow_offset_hor' => '40',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'on',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'vertical',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'right',
                'bullets_align_vert' => 'center',
                'bullets_offset_hor' => '30',
                'bullets_offset_vert' => '0',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '5',
                'span_thumbnails_wrapper' => 'off',
                'thumbnails_wrapper_color' => 'transparent',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '5',
                'thumbnails_space' => '5',
                'thumbnail_direction' => 'vertical',
                'thumb_width' => '50',
                'thumb_height' => '50',
                'thumb_width_min' => '50',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'on',
                'thumbs_under_hidden' => '778',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'right',
                'thumbnails_align_vert' => 'center',
                'thumbnails_offset_hor' => '20',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'off',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'off',
                'carousel_maxrotation' => '0',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '50',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullwidth',
                'width' => '1240',
                'width_notebook' => '1024',
                'width_tablet' => '778',
                'width_mobile' => '480',
                'height' => '600',
                'height_notebook' => '500',
                'height_tablet' => '400',
                'height_mobile' => '300',
                'enable_custom_size_notebook' => 'on',
                'enable_custom_size_tablet' => 'on',
                'enable_custom_size_iphone' => 'on',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_javascript' => '',
                'custom_css' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/cover_carousel_thumbs.png', 'name' => 'Cover-Flow-Thumbs', 'preset' => 'carouselpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'navbar-old',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'on',
                'thumbnails_padding' => '20',
                'span_thumbnails_wrapper' => 'on',
                'thumbnails_wrapper_color' => '#000000',
                'thumbnails_wrapper_opacity' => '15',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '9',
                'thumbnails_space' => '10',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '60',
                'thumb_height' => '60',
                'thumb_width_min' => '60',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'outer-bottom',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'off',
                'carousel_space' => '-150',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '5',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'on',
                'carousel_rotation' => 'on',
                'carousel_varyrotate' => 'on',
                'carousel_maxrotation' => '65',
                'carousel_scale' => 'on',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '55',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullwidth',
                'width' => '600',
                'width_notebook' => '600',
                'width_tablet' => '600',
                'width_mobile' => '600',
                'height' => '600',
                'height_notebook' => '600',
                'height_tablet' => '600',
                'height_mobile' => '600',
                'enable_custom_size_notebook' => 'off',
                'enable_custom_size_tablet' => 'off',
                'enable_custom_size_iphone' => 'off',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_css' => '',
                'custom_javascript' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/cover_carousel_endless.png', 'name' => 'Cover-Flow-Infinite', 'preset' => 'carouselpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'round',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '20',
                'span_thumbnails_wrapper' => 'on',
                'thumbnails_wrapper_color' => '#000000',
                'thumbnails_wrapper_opacity' => '15',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '9',
                'thumbnails_space' => '10',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '60',
                'thumb_height' => '60',
                'thumb_width_min' => '60',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'outer-bottom',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'on',
                'carousel_space' => '-150',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'on',
                'carousel_rotation' => 'on',
                'carousel_varyrotate' => 'on',
                'carousel_maxrotation' => '65',
                'carousel_scale' => 'on',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '55',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullwidth',
                'width' => '600',
                'width_notebook' => '600',
                'width_tablet' => '600',
                'width_mobile' => '600',
                'height' => '600',
                'height_notebook' => '600',
                'height_tablet' => '600',
                'height_mobile' => '600',
                'enable_custom_size_notebook' => 'off',
                'enable_custom_size_tablet' => 'off',
                'enable_custom_size_iphone' => 'off',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_css' => '',
                'custom_javascript' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/flat_carousel_thumbs.png', 'name' => 'Flat-Infinite-Thumbs', 'preset' => 'carouselpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => '#111111',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'navbar',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'on',
                'thumbnails_padding' => '20',
                'span_thumbnails_wrapper' => 'on',
                'thumbnails_wrapper_color' => '#222222',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '9',
                'thumbnails_space' => '10',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '60',
                'thumb_height' => '60',
                'thumb_width_min' => '60',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'outer-bottom',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'on',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'on',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'on',
                'carousel_maxrotation' => '65',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '55',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullwidth',
                'width' => '720',
                'width_notebook' => '720',
                'width_tablet' => '720',
                'width_mobile' => '720',
                'height' => '405',
                'height_notebook' => '405',
                'height_tablet' => '405',
                'height_mobile' => '405',
                'enable_custom_size_notebook' => 'off',
                'enable_custom_size_tablet' => 'off',
                'enable_custom_size_iphone' => 'off',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_css' => '',
                'custom_javascript' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/flat_carousel.png', 'name' => 'Flat-Infinite', 'preset' => 'carouselpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => '#111111',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'uranus',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'off',
                'thumbnails_padding' => '20',
                'span_thumbnails_wrapper' => 'on',
                'thumbnails_wrapper_color' => '#222222',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '9',
                'thumbnails_space' => '10',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '60',
                'thumb_height' => '60',
                'thumb_width_min' => '60',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'outer-bottom',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'on',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'on',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'on',
                'carousel_maxrotation' => '65',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '55',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullwidth',
                'width' => '720',
                'width_notebook' => '720',
                'width_tablet' => '720',
                'width_mobile' => '720',
                'height' => '405',
                'height_notebook' => '405',
                'height_tablet' => '405',
                'height_mobile' => '405',
                'enable_custom_size_notebook' => 'off',
                'enable_custom_size_tablet' => 'off',
                'enable_custom_size_iphone' => 'off',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_css' => '',
                'custom_javascript' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/flat_carousel_thumbs_left.png', 'name' => 'Flat-Thumbs-Left', 'preset' => 'carouselpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => '#111111',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'uranus',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'on',
                'thumbnails_padding' => '20',
                'span_thumbnails_wrapper' => 'on',
                'thumbnails_wrapper_color' => '#222222',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '9',
                'thumbnails_space' => '10',
                'thumbnail_direction' => 'vertical',
                'thumb_width' => '60',
                'thumb_height' => '60',
                'thumb_width_min' => '60',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'outer-left',
                'thumbnails_align_hor' => 'left',
                'thumbnails_align_vert' => 'top',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'on',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'on',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'on',
                'carousel_maxrotation' => '65',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '55',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullwidth',
                'width' => '720',
                'width_notebook' => '720',
                'width_tablet' => '720',
                'width_mobile' => '720',
                'height' => '405',
                'height_notebook' => '405',
                'height_tablet' => '405',
                'height_mobile' => '405',
                'enable_custom_size_notebook' => 'off',
                'enable_custom_size_tablet' => 'off',
                'enable_custom_size_iphone' => 'off',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_css' => '',
                'custom_javascript' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/carousel_thumbs_right_fullscreen.png', 'name' => 'Full-Screen-Thumbs-Right', 'preset' => 'carouselpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => '#111111',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'uranus',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'on',
                'thumbnails_padding' => '20',
                'span_thumbnails_wrapper' => 'on',
                'thumbnails_wrapper_color' => '#222222',
                'thumbnails_wrapper_opacity' => '100',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '9',
                'thumbnails_space' => '10',
                'thumbnail_direction' => 'vertical',
                'thumb_width' => '60',
                'thumb_height' => '60',
                'thumb_width_min' => '60',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'outer-right',
                'thumbnails_align_hor' => 'right',
                'thumbnails_align_vert' => 'top',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'on',
                'carousel_space' => '0',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => 'px',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '3',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'on',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'on',
                'carousel_maxrotation' => '65',
                'carousel_scale' => 'off',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '55',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullscreen',
                'width' => '900',
                'width_notebook' => '720',
                'width_tablet' => '720',
                'width_mobile' => '720',
                'height' => '720',
                'height_notebook' => '405',
                'height_tablet' => '405',
                'height_mobile' => '405',
                'enable_custom_size_notebook' => 'off',
                'enable_custom_size_tablet' => 'off',
                'enable_custom_size_iphone' => 'off',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_css' => '',
                'custom_javascript' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/cover_carousel_thumbs.png', 'name' => 'Cover-Flow-Full-Screen', 'preset' => 'carouselpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'first_transition_active' => 'on',
                'first_transition_type' => 'fade',
                'first_transition_duration' => '1500',
                'first_transition_slot_amount' => '7',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'navbar-old',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'on',
                'thumbnails_padding' => '20',
                'span_thumbnails_wrapper' => 'on',
                'thumbnails_wrapper_color' => '#000000',
                'thumbnails_wrapper_opacity' => '15',
                'thumbnails_style' => 'navbar',
                'thumb_amount' => '9',
                'thumbnails_space' => '10',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '60',
                'thumb_height' => '60',
                'thumb_width_min' => '60',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'on',
                'carousel_space' => '-150',
                'carousel_borderr' => '0',
                'carousel_borderr_unit' => '%',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '5',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'on',
                'carousel_rotation' => 'on',
                'carousel_varyrotate' => 'on',
                'carousel_maxrotation' => '65',
                'carousel_scale' => 'on',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '55',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullscreen',
                'width' => '800',
                'width_notebook' => '600',
                'width_tablet' => '600',
                'width_mobile' => '600',
                'height' => '800',
                'height_notebook' => '600',
                'height_tablet' => '600',
                'height_mobile' => '600',
                'enable_custom_size_notebook' => 'off',
                'enable_custom_size_tablet' => 'off',
                'enable_custom_size_iphone' => 'off',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_css' => '',
                'custom_javascript' => '',
            ),
        );
        $presets[] = array(
            'settings' =>
            array('class' => '', 'image' => _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sliderpresets/carousel_full_rounded.png', 'name' => 'Cover-Flow-Rounded', 'preset' => 'carouselpreset'),
            'values' =>
            array(
                'next_slide_on_window_focus' => 'off',
                'delay' => '9000',
                'start_js_after_delay' => '0',
                'image_source_type' => 'full',
                0 => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                'start_with_slide' => '1',
                'first_transition_active' => 'on',
                'first_transition_type' => 'fade',
                'first_transition_duration' => '1500',
                'first_transition_slot_amount' => '7',
                'stop_on_hover' => 'off',
                'stop_slider' => 'on',
                'stop_after_loops' => '0',
                'stop_at_slide' => '1',
                'shuffle' => 'off',
                'viewport_start' => 'wait',
                'viewport_area' => '80',
                'enable_progressbar' => 'on',
                'background_dotted_overlay' => 'none',
                'background_color' => 'transparent',
                'padding' => '0',
                'show_background_image' => 'off',
                'background_image' => '',
                'bg_fit' => 'cover',
                'bg_repeat' => 'no-repeat',
                'bg_position' => 'center center',
                'position' => 'center',
                'use_spinner' => '-1',
                'spinner_color' => '#FFFFFF',
                'enable_arrows' => 'on',
                'navigation_arrow_style' => 'round',
                'arrows_always_on' => 'false',
                'hide_arrows' => '200',
                'hide_arrows_mobile' => '1200',
                'hide_arrows_on_mobile' => 'off',
                'arrows_under_hidden' => '600',
                'hide_arrows_over' => 'off',
                'arrows_over_hidden' => '0',
                'leftarrow_align_hor' => 'left',
                'leftarrow_align_vert' => 'center',
                'leftarrow_offset_hor' => '30',
                'leftarrow_offset_vert' => '0',
                'rightarrow_align_hor' => 'right',
                'rightarrow_align_vert' => 'center',
                'rightarrow_offset_hor' => '30',
                'rightarrow_offset_vert' => '0',
                'enable_bullets' => 'off',
                'navigation_bullets_style' => 'round-old',
                'bullets_space' => '5',
                'bullets_direction' => 'horizontal',
                'bullets_always_on' => 'true',
                'hide_bullets' => '200',
                'hide_bullets_mobile' => '1200',
                'hide_bullets_on_mobile' => 'on',
                'bullets_under_hidden' => '600',
                'hide_bullets_over' => 'off',
                'bullets_over_hidden' => '0',
                'bullets_align_hor' => 'center',
                'bullets_align_vert' => 'bottom',
                'bullets_offset_hor' => '0',
                'bullets_offset_vert' => '30',
                'enable_thumbnails' => 'on',
                'thumbnails_padding' => '20',
                'span_thumbnails_wrapper' => 'on',
                'thumbnails_wrapper_color' => '#000000',
                'thumbnails_wrapper_opacity' => '0',
                'thumbnails_style' => 'preview1',
                'thumb_amount' => '9',
                'thumbnails_space' => '10',
                'thumbnail_direction' => 'horizontal',
                'thumb_width' => '60',
                'thumb_height' => '60',
                'thumb_width_min' => '60',
                'thumbs_always_on' => 'false',
                'hide_thumbs' => '200',
                'hide_thumbs_mobile' => '1200',
                'hide_thumbs_on_mobile' => 'off',
                'thumbs_under_hidden' => '0',
                'hide_thumbs_over' => 'off',
                'thumbs_over_hidden' => '0',
                'thumbnails_inner_outer' => 'inner',
                'thumbnails_align_hor' => 'center',
                'thumbnails_align_vert' => 'bottom',
                'thumbnails_offset_hor' => '0',
                'thumbnails_offset_vert' => '0',
                'enable_tabs' => 'off',
                'tabs_padding' => '5',
                'span_tabs_wrapper' => 'off',
                'tabs_wrapper_color' => 'transparent',
                'tabs_wrapper_opacity' => '5',
                'tabs_style' => '',
                'tabs_amount' => '5',
                'tabs_space' => '5',
                'tabs_direction' => 'horizontal',
                'tabs_width' => '100',
                'tabs_height' => '50',
                'tabs_width_min' => '100',
                'tabs_always_on' => 'false',
                'hide_tabs' => '200',
                'hide_tabs_mobile' => '1200',
                'hide_tabs_on_mobile' => 'off',
                'tabs_under_hidden' => '0',
                'hide_tabs_over' => 'off',
                'tabs_over_hidden' => '0',
                'tabs_inner_outer' => 'inner',
                'tabs_align_hor' => 'center',
                'tabs_align_vert' => 'bottom',
                'tabs_offset_hor' => '0',
                'tabs_offset_vert' => '20',
                'touchenabled' => 'on',
                'drag_block_vertical' => 'off',
                'swipe_velocity' => '75',
                'swipe_min_touches' => '50',
                'swipe_direction' => 'horizontal',
                'keyboard_navigation' => 'off',
                'keyboard_direction' => 'horizontal',
                'mousescroll_navigation' => 'off',
                'carousel_infinity' => 'on',
                'carousel_space' => '-150',
                'carousel_borderr' => '50',
                'carousel_borderr_unit' => '%',
                'carousel_padding_top' => '0',
                'carousel_padding_bottom' => '0',
                'carousel_maxitems' => '5',
                'carousel_stretch' => 'off',
                'carousel_fadeout' => 'on',
                'carousel_varyfade' => 'on',
                'carousel_rotation' => 'off',
                'carousel_varyrotate' => 'on',
                'carousel_maxrotation' => '65',
                'carousel_scale' => 'on',
                'carousel_varyscale' => 'off',
                'carousel_scaledown' => '55',
                'carousel_hposition' => 'center',
                'carousel_vposition' => 'center',
                'use_parallax' => 'on',
                'disable_parallax_mobile' => 'off',
                'parallax_type' => 'mouse',
                'parallax_origo' => 'slidercenter',
                'parallax_speed' => '2000',
                'parallax_level_1' => '2',
                'parallax_level_2' => '3',
                'parallax_level_3' => '4',
                'parallax_level_4' => '5',
                'parallax_level_5' => '6',
                'parallax_level_6' => '7',
                'parallax_level_7' => '12',
                'parallax_level_8' => '16',
                'parallax_level_9' => '10',
                'parallax_level_10' => '50',
                'lazy_load_type' => 'smart',
                'seo_optimization' => 'none',
                'simplify_ie8_ios4' => 'off',
                'show_alternative_type' => 'off',
                'show_alternate_image' => '',
                'jquery_noconflict' => 'off',
                'js_to_body' => 'false',
                'output_type' => 'none',
                'jquery_debugmode' => 'off',
                'slider_type' => 'fullwidth',
                'width' => '800',
                'width_notebook' => '600',
                'width_tablet' => '600',
                'width_mobile' => '600',
                'height' => '800',
                'height_notebook' => '600',
                'height_tablet' => '600',
                'height_mobile' => '600',
                'enable_custom_size_notebook' => 'off',
                'enable_custom_size_tablet' => 'off',
                'enable_custom_size_iphone' => 'off',
                'main_overflow_hidden' => 'off',
                'auto_height' => 'off',
                'min_height' => '',
                'custom_css' => '',
                'custom_javascript' => '',
            ),
        );

        //add the presets made from customers
        // $customer_presets = get_option('revslider_presets', array());
        $customer_presets = array();

        $wpdb = RevsliderPrestashop::$wpdb;

        $tableRealName = $wpdb->prefix . GlobalsRevSlider::TABLE_REVSLIDER_OPTIONS_NAME;

        $sql = "SELECT * FROM `" . $tableRealName . "` WHERE name='revslider_presets';";
        $q = $wpdb->getRow($sql);

        if ($q) {
            $customer_presets = unserialize($q['value']);
        }


        $presets = array_merge($presets, $customer_presets);

        // $presets = apply_filters('revslider_slider_presets', $presets);

        foreach ($presets as $key => $preset) {
            if ((int) ($preset['settings']['image']) > 0) {
                $img = wp_get_attachment_image_src($preset['settings']['image'], 'medium');
                $presets[$key]['settings']['image'] = ($img !== false) ? $img['0'] : '';
            }
        }

        return $presets;
    }

    /**
     * 
     * @since: 5.0
     * */
    public static function addPresetSetting($data)
    {
        if (!@RevsliderPrestashop::getIsset($data['settings']) || !@RevsliderPrestashop::getIsset($data['values'])) {
            return 'Missing values to add preset';
        }

        // $data = Tools::jsonDecode($data);
        $data['settings']['custom'] = true;

        $wpdb = RevsliderPrestashop::$wpdb;


        $tableRealName = $wpdb->prefix . GlobalsRevSlider::TABLE_REVSLIDER_OPTIONS_NAME;

        $sql = "SELECT * FROM `" . $tableRealName . "` WHERE name='revslider_presets';";

        $q = $wpdb->getRow($sql);




        $customer_presets = array();



        if ($q !== false) {
            $customer_presets = unserialize($q['value']);
            $customer_presets[] = array(
                'settings' => $data['settings'],
                'values' => $data['values']
            );

            $customer_presets = serialize($customer_presets);

            $sql = "UPDATE `" . $tableRealName . "` SET `value`='" . $customer_presets . "' WHERE name='revslider_presets';";

            $q = $wpdb->query($sql);
        } else {
            $customer_presets[] = array(
                'settings' => $data['settings'],
                'values' => $data['values']
            );
            $customer_presets = serialize($customer_presets);

            $sql = "INSERT INTO `" . $tableRealName . "` (`id`, `name`, `value`) VALUES (NULL, 'revslider_presets', '" . $customer_presets . "');";

            $q = $wpdb->query($sql);
        }









        // update_option('revslider_presets', $customer_presets);

        return true;
    }

    /**
     * @since: 5.0
     * */
    public static function removePresetSetting($data)
    {
        if (!@RevsliderPrestashop::getIsset($data['name'])) {
            return __('Missing values to remove preset', 'revslider');
        }

        $wpdb = RevsliderPrestashop::$wpdb;

        $tableRealName = $wpdb->prefix . GlobalsRevSlider::TABLE_REVSLIDER_OPTIONS_NAME;

        $sql = "SELECT * FROM `" . $tableRealName . "` WHERE name='revslider_presets';";

        $q = $wpdb->getRow($sql);

        $customer_presets = array();

        if ($q !== false) {
            $customer_presets = unserialize($q['value']);
        }


        if (!empty($customer_presets)) {
            foreach ($customer_presets as $key => $preset) {
                if ($preset['settings']['name'] == $data['name']) {
                    unset($customer_presets[$key]);
                    break;
                }
            }
        }

        $customer_presets = serialize($customer_presets);

        $sql = "UPDATE `" . $tableRealName . "` SET `value`='" . $customer_presets . "' WHERE name='revslider_presets';";

        $q = $wpdb->query($sql);


        return true;
    }

    /**
     * @since: 5.0
     * */
    public static function updatePresetSetting($data)
    {
        if (!@RevsliderPrestashop::getIsset($data['name'])) {
            return __('Missing values to update preset', 'revslider');
        }

        $wpdb = RevsliderPrestashop::$wpdb;

        $tableRealName = $wpdb->prefix . GlobalsRevSlider::TABLE_REVSLIDER_OPTIONS_NAME;

        $sql = "SELECT * FROM `" . $tableRealName . "` WHERE name='revslider_presets';";

        $q = $wpdb->getRow($sql);

        $customer_presets = array();

        if ($q !== false) {
            $customer_presets = unserialize($q['value']);
        }

        if (!empty($customer_presets)) {
            foreach ($customer_presets as $key => $preset) {
                if ($preset['settings']['name'] == $data['name']) {
                    $customer_presets[$key]['values'] = $data['values'];
                    break;
                }
            }
        }

        $customer_presets = serialize($customer_presets);

        $sql = "UPDATE `" . $tableRealName . "` SET `value`='" . $customer_presets . "' WHERE name='revslider_presets';";

        $q = $wpdb->query($sql);


        return true;
    }

    public static function getPerformance($val, $min, $max)
    {
        if ($val == 0) {
            $val = 1;
        }
        $arr = array();
        //print_r(($max-$min)."/".($val-$min)."=");
        $arr["proc"] = (($max - $min) / ($val - $min)) * 100;
        //print_r($arr["proc"]."  --> ");


        if ($arr["proc"] > 100) {
            $arr["proc"] = 100;
        }
        if ($arr["proc"] < 0) {
            $arr["proc"] = 0;
        }

        if ($arr["proc"] < 35) {
            $arr["col"] = "slow";
        } elseif ($arr["proc"] < 75) {
            $arr["col"] = "ok";
        } else {
            //print_r($arr["proc"]." <br>");


            $arr["col"] = "fast";
        }



        return $arr;
    }

    /**
     * view the estimated speed of the Slider
     * @since: 5.0
     */
    public static function getSliderSpeed($sliderID)
    {
        RevGlobalObject::setVar('sliderID', $sliderID);
        require_once ABSPATH . '/views/slider_speed.php';
    }

    /**
     * update captions advanced css
     * @return: new captions html select
     * @since: 5.0
     */
    public function updateAdvancedCssData($data)
    {
        if (!isset($data['handle']) || !isset($data['styles']) || !isset($data['type'])) {
            return false;
        }
        if ($data['type'] !== 'idle' && $data['type'] !== 'hover') {
            return false;
        }

        $db = new RevSliderDB();

        //get current styles
        $styles = $db->fetchSingle(RevSliderGlobals::$table_css, '`handle` = "' . $db->escape($data['handle']) . '"');

        if (!empty($styles)) {
            if (!isset($styles['advanced'])) {
                $styles['advanced'] = '';
            }

            $adv = Tools::jsonDecode(str_replace("'", '"', $styles['advanced']), true);

            if (!isset($adv['idle'])) {
                $adv['idle'] = array();
            }
            if (!isset($adv['hover'])) {
                $adv['hover'] = array();
            }

            $adv[$data['type']] = $data['styles'];


            $arrUpdate = array();

            $arrUpdate['advanced'] = Tools::jsonEncode(str_replace("'", '"', $adv));

            $result = $db->update(RevSliderGlobals::$table_css, $arrUpdate, array('handle' => $data['handle']));

            //output captions array
            $arrCaptions = RevSliderCssParser::getCaptionsSorted();

            return($arrCaptions);
        } else {
            return false;
        }
    }
// @codingStandardsIgnoreStart
}

class RevSliderOperations extends RevOperations
{
    // @codingStandardsIgnoreEnd
}
