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

class RevSliderSettingsProduct extends UniteSettingsRevProductRev
{

    public static function setSettingsCustomValues(UniteSettingsRev $settings, $arrValues, $postTypesWithCats = false)
    {
        $arrSettings = $settings->getArrSettings();



        foreach ($arrSettings as $key => $setting) {
            $type = UniteFunctionsRev::getVal($setting, "type");

            if ($type != UniteSettingsRev::TYPE_CUSTOM) {
                continue;
            }

            $customType = UniteFunctionsRev::getVal($setting, "custom_type");



            switch ($customType) {

                case "slider_size":

                    $setting["width"] = UniteFunctionsRev::getVal($arrValues, "width", UniteFunctionsRev::getVal($setting, "width"));

                    $setting["height"] = UniteFunctionsRev::getVal($arrValues, "height", UniteFunctionsRev::getVal($setting, "height"));

                    $arrSettings[$key] = $setting;

                    break;

                case "responsitive_settings":

                    $id = $setting["id"];

                    $setting["w1"] = UniteFunctionsRev::getVal($arrValues, $id . "_w1", UniteFunctionsRev::getVal($setting, "w1"));

                    $setting["w2"] = UniteFunctionsRev::getVal($arrValues, $id . "_w2", UniteFunctionsRev::getVal($setting, "w2"));

                    $setting["w3"] = UniteFunctionsRev::getVal($arrValues, $id . "_w3", UniteFunctionsRev::getVal($setting, "w3"));

                    $setting["w4"] = UniteFunctionsRev::getVal($arrValues, $id . "_w4", UniteFunctionsRev::getVal($setting, "w4"));

                    $setting["w5"] = UniteFunctionsRev::getVal($arrValues, $id . "_w5", UniteFunctionsRev::getVal($setting, "w5"));

                    $setting["w6"] = UniteFunctionsRev::getVal($arrValues, $id . "_w6", UniteFunctionsRev::getVal($setting, "w6"));



                    $setting["sw1"] = UniteFunctionsRev::getVal($arrValues, $id . "_sw1", UniteFunctionsRev::getVal($setting, "sw1"));

                    $setting["sw2"] = UniteFunctionsRev::getVal($arrValues, $id . "_sw2", UniteFunctionsRev::getVal($setting, "sw2"));

                    $setting["sw3"] = UniteFunctionsRev::getVal($arrValues, $id . "_sw3", UniteFunctionsRev::getVal($setting, "sw3"));

                    $setting["sw4"] = UniteFunctionsRev::getVal($arrValues, $id . "_sw4", UniteFunctionsRev::getVal($setting, "sw4"));

                    $setting["sw5"] = UniteFunctionsRev::getVal($arrValues, $id . "_sw5", UniteFunctionsRev::getVal($setting, "sw5"));

                    $setting["sw6"] = UniteFunctionsRev::getVal($arrValues, $id . "_sw6", UniteFunctionsRev::getVal($setting, "sw6"));

                    $arrSettings[$key] = $setting;

                    break;
            }
        }



        $settings->setArrSettings($arrSettings);



        //disable settings by slider type:

        $sliderType = $settings->getSettingValue("slider_type");



        switch ($sliderType) {

            case "fixed":

            case "fullwidth":

            case "fullscreen":

                //hide responsive

                $settingRes = $settings->getSettingByName("responsitive");

                $settingRes["disabled"] = true;

                $settings->updateArrSettingByName("responsitive", $settingRes);

                break;
        }



        switch ($sliderType) {

            case "fixed":

            case "responsitive":

            case "fullscreen":

                //hide autoheight

                $settingRes = $settings->getSettingByName("auto_height");

                $settingRes["disabled"] = true;

                $settings->updateArrSettingByName("auto_height", $settingRes);



                $settingRes = $settings->getSettingByName("force_full_width");

                $settingRes["disabled"] = true;

                $settings->updateArrSettingByName("force_full_width", $settingRes);

                break;
        }

        //change height to max height

        $settingSize = $settings->getSettingByName("slider_size");

        $settingSize["slider_type"] = $sliderType;

        $settings->updateArrSettingByName("slider_size", $settingSize);

        return($settings);
    }

    protected function drawResponsitiveSettings($setting)
    {
        $id = $setting["id"];



        $w1 = UniteFunctionsRev::getVal($setting, "w1");

        $w2 = UniteFunctionsRev::getVal($setting, "w2");

        $w3 = UniteFunctionsRev::getVal($setting, "w3");

        $w4 = UniteFunctionsRev::getVal($setting, "w4");

        $w5 = UniteFunctionsRev::getVal($setting, "w5");

        $w6 = UniteFunctionsRev::getVal($setting, "w6");



        $sw1 = UniteFunctionsRev::getVal($setting, "sw1");

        $sw2 = UniteFunctionsRev::getVal($setting, "sw2");

        $sw3 = UniteFunctionsRev::getVal($setting, "sw3");

        $sw4 = UniteFunctionsRev::getVal($setting, "sw4");

        $sw5 = UniteFunctionsRev::getVal($setting, "sw5");

        $sw6 = UniteFunctionsRev::getVal($setting, "sw6");



        $disabled = (UniteFunctionsRev::getVal($setting, "disabled") == true);



        $strDisabled = "";

        if ($disabled == true) {
            $strDisabled = "disabled='disabled'";
        }



        echo '<table>';

        echo '<tr>';

        echo '<td>';

        _e("Screen Width", REVSLIDER_TEXTDOMAIN);
        echo '1:';

        echo '</td>';

        echo '<td>';

        echo '<input id="' . $id . '_w1" name="' . $id . '_w1" type="text" class="textbox-small" ' . $strDisabled . ' value="' . $w1 . '">';

        echo '</td>';

        echo '<td>';

        _e("Slider Width", REVSLIDER_TEXTDOMAIN);
        echo '1:';

        echo '</td>';

        echo '<td>';

        echo '<input id="' . $id . '_sw1" name="' . $id . '_sw1" type="text" class="textbox-small" ' . $strDisabled . ' value="' . $sw1 . '">';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        _e("Screen Width", REVSLIDER_TEXTDOMAIN);
        echo '2:';

        echo '</td>';

        echo '<td>';

        echo '<input id="' . $id . '_w2" name="' . $id . '_w2" type="text" class="textbox-small" ' . $strDisabled . ' value="' . $w2 . '">';

        echo '</td>';

        echo '<td>';

        _e("Slider Width", REVSLIDER_TEXTDOMAIN);
        echo '2:';

        echo '</td>';

        echo '<td>';

        echo '<input id="' . $id . '_sw2" name="' . $id . '_sw2" type="text" class="textbox-small" ' . $strDisabled . ' value="' . $sw2 . '">';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        _e("Screen Width", REVSLIDER_TEXTDOMAIN);
        echo '3:';

        echo '</td>';

        echo '<td>';

        echo '<input id="' . $id . '_w3" name="' . $id . '_w3" type="text" class="textbox-small" ' . $strDisabled . ' value="' . $w3 . '">';

        echo '</td>';

        echo '<td>';

        _e("Slider Width", REVSLIDER_TEXTDOMAIN);
        echo '3:';

        echo '</td>';

        echo '<td>';

        echo '<input id="' . $id . '_sw3" name="' . $id . '_sw3" type="text" class="textbox-small" ' . $strDisabled . ' value="' . $sw3 . '">';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        _e("Screen Width", REVSLIDER_TEXTDOMAIN);
        echo '4:';

        echo '</td>';

        echo '<td>';

        echo '<input type="text" id="' . $id . '_w4" name="' . $id . '_w4" class="textbox-small" ' . $strDisabled . ' value="' . $w4 . '">';

        echo '</td>';

        echo '<td>';

        _e("Slider Width", REVSLIDER_TEXTDOMAIN);
        echo '4:';

        echo '</td>';

        echo '<td>';

        echo '<input type="text" id="' . $id . '_sw4" name="' . $id . '_sw4" class="textbox-small" ' . $strDisabled . ' value="' . $sw4 . '">';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        _e("Screen Width", REVSLIDER_TEXTDOMAIN);
        echo '5:';

        echo '</td>';

        echo '<td>';

        echo '<input type="text" id="' . $id . '_w5" name="' . $id . '_w5" class="textbox-small" ' . $strDisabled . ' value="' . $w5 . '">';

        echo '</td>';

        echo '<td>';

        _e("Slider Width", REVSLIDER_TEXTDOMAIN);
        echo '5:';

        echo '</td>';

        echo '<td>';

        echo '<input type="text" id="' . $id . '_sw5" name="' . $id . '_sw5" class="textbox-small" ' . $strDisabled . ' value="' . $sw5 . '">';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        _e("Screen Width", REVSLIDER_TEXTDOMAIN);
        echo '6:';

        echo '</td>';

        echo '<td>';

        echo '<input type="text" id="' . $id . '_w6" name="' . $id . '_w6" class="textbox-small" ' . $strDisabled . ' value="' . $w6 . '">';

        echo '</td>';

        echo '<td>';

        _e("Slider Width", REVSLIDER_TEXTDOMAIN);
        echo '6:';

        echo '</td>';

        echo '<td>';

        echo '<input type="text" id="' . $id . '_sw6" name="' . $id . '_sw6" class="textbox-small" ' . $strDisabled . ' value="' . $sw6 . '">';

        echo '</td>';

        echo '</tr>				';



        echo '</table>';
    }

    protected function drawSliderSize($setting)
    {
        $width = UniteFunctionsRev::getVal($setting, "width");

        $height = UniteFunctionsRev::getVal($setting, "height");



        $sliderType = UniteFunctionsRev::getVal($setting, "slider_type");



        $textNormalW = __("Grid Width:", REVSLIDER_TEXTDOMAIN);

        $textNormalH = __("Grid Height:", REVSLIDER_TEXTDOMAIN);



        $textFullWidthW = __("Grid Width:", REVSLIDER_TEXTDOMAIN);

        $textFullWidthH = __("Grid Height:", REVSLIDER_TEXTDOMAIN);



        $textFullScreenW = __("Grid Width:", REVSLIDER_TEXTDOMAIN);

        $textFullScreenH = __("Grid Height:", REVSLIDER_TEXTDOMAIN);



        //set default text (fixed, responsive)

        switch ($sliderType) {

            default:

                $textDefaultW = $textNormalW;

                $textDefaultH = $textNormalH;

                break;

            case "fullwidth":

                $textDefaultW = $textFullWidthW;

                $textDefaultH = $textFullWidthH;

                break;

            case "fullscreen":

                $textDefaultW = $textFullScreenW;

                $textDefaultH = $textFullScreenH;

                break;
        }

        echo '<table>';

        echo '<tr>';

        echo '<td id="cellWidth" data-textnormal="' . $textNormalW . '" data-textfull="' . $textFullWidthW . '" data-textscreen="' . $textFullScreenW . '">';

        echo $textDefaultW;

        echo '</td>';

        echo '<td id="cellWidthInput">';

        echo '<input id="width" name="width" type="text" class="textbox-small" value="' . $width . '">';

        echo '</td>';

        echo '<td id="cellHeight" data-textnormal="' . $textNormalH . '" data-textfull="' . $textFullWidthH . '" data-textscreen="' . $textFullScreenH . '">';

        echo $textDefaultH;

        echo '</td>';

        echo '<td>';

        echo '<input id="height" name="height" type="text" class="textbox-small" value="' . $height . '">';

        echo '</td>';

        echo '</tr>';

        echo '</table>';
    }

    protected function drawCustomInputs($setting)
    {
        $customType = UniteFunctionsRev::getVal($setting, "custom_type");

        switch ($customType) {

            case "slider_size":

                $this->drawSliderSize($setting);

                break;

            case "responsitive_settings":

                $this->drawResponsitiveSettings($setting);

                break;

            default:

                UniteFunctionsRev::throwError("No handler function for type: $customType");

                break;
        }
    }

    private static function getFirstCategory($cats)
    {
        foreach ($cats as $key => $value) {
            if (strpos($key, "option_disabled") === false) {
                return($key);
            }
        }

        return("");
    }

    public static function setCategoryByPostTypes(UniteSettingsRev $settings, $arrValues, $postTypesWithCats, $nameType, $nameCat, $defaultType)
    {
        $postTypes = UniteFunctionsRev::getVal($arrValues, $nameType, $defaultType);

        if (strpos($postTypes, ",") !== false) {
            $postTypes = explode(",", $postTypes);
        } else {
            $postTypes = array($postTypes);
        }



        $arrCats = array();

        $globalCounter = 0;



        $arrCats = array();

        $isFirst = true;

        foreach ($postTypes as $postType) {
            $cats = UniteFunctionsRev::getVal($postTypesWithCats, $postType, array());

            if ($isFirst == true) {
                $firstValue = self::getFirstCategory($cats);

                $isFirst = false;
            }



            $arrCats = array_merge($arrCats, $cats);
        }



        $settingCategory = $settings->getSettingByName($nameCat);

        $settingCategory["items"] = $arrCats;

        $settings->updateArrSettingByName($nameCat, $settingCategory);



        //update value to first category

        $value = $settings->getSettingValue($nameCat);

        if (empty($value)) {
            $settings->updateSettingValue($nameCat, $firstValue);
        }



        return($settings);
    }
}
