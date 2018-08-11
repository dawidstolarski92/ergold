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

class UniteSettingsProductSidebarRev extends UniteSettingsOutputRev
{

    private $addClass = "";
    private $arrButtons = array();
    private $isAccordion = true;
    private $defaultTextClass;

    const INPUT_CLASS_SHORT = "text-sidebar";
    const INPUT_CLASS_NORMAL = "text-sidebar-normal";
    const INPUT_CLASS_LONG = "text-sidebar-long";
    const INPUT_CLASS_LINK = "text-sidebar-link";

    public function __construct()
    {
        $this->defaultTextClass = self::INPUT_CLASS_SHORT;
    }

    public function setDefaultInputClass($defaultClass)
    {
        $this->defaultTextClass = $defaultClass;
    }

    public function addButton($title, $id, $class = "button-secondary")
    {
        $button = array(
            "title" => $title,
            "id" => $id,
            "class" => $class
        );



        $this->arrButtons[] = $button;
    }

    public function setAddClass($addClass)
    {
        $this->addClass = $addClass;
    }

    protected function drawTextInput($setting)
    {
        $disabled = "";

        $style = "";

        if (@RevsliderPrestashop::getIsset($setting["style"])) {
            $style = "style='" . $setting["style"] . "'";
        }

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $disabled = 'disabled="disabled"';
        }



        $class = UniteFunctionsRev::getVal($setting, "class", $this->defaultTextClass);



        //modify class:

        switch ($class) {

            case "normal":

            case "regular":

                $class = self::INPUT_CLASS_NORMAL;

                break;

            case "long":

                $class = self::INPUT_CLASS_LONG;

                break;

            case "link":

                $class = self::INPUT_CLASS_LINK;

                break;
        }



        if (!empty($class)) {
            $class = "class='$class'";
        }



        $attribs = UniteFunctionsRev::getVal($setting, "attribs");



        echo '<input type="text" ' . $attribs . ' ' . $class . ' ' . $style . ' ' . $disabled . ' id="' . $setting["id"] . '" name="' . $setting["name"] . '" value="' . $setting["value"] . '" />';
    }

    //-----------------------------------------------------------------------------------------------
    //draw multiple text boxes as input

    protected function drawMultipleText($setting)
    {
        $disabled = "";

        $style = "";

        if (@RevsliderPrestashop::getIsset($setting["style"])) {
            $style = "style='" . $setting["style"] . "'";
        }

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $disabled = 'disabled="disabled"';
        }



        $class = UniteFunctionsRev::getVal($setting, "class", $this->defaultTextClass);



        //modify class:

        switch ($class) {

            case "normal":

            case "regular":

                $class = self::INPUT_CLASS_NORMAL;

                break;

            case "long":

                $class = self::INPUT_CLASS_LONG;

                break;

            case "link":

                $class = self::INPUT_CLASS_LINK;

                break;
        }



        if (!empty($class)) {
            $class = "class='$class'";
        }



        $attribs = UniteFunctionsRev::getVal($setting, "attribs");

        $values = $setting["value"];

        if (!empty($values) && is_array($values)) {
            foreach ($values as $key => $value) {

                echo '<div class="fontinput_wrapper">';
                echo '<input type="text" ' . $attribs . ' ' . $class . ' ' . $style . ' ' . $disabled . ' id="' . $setting["id"] . '_' . $key . '" name="' . $setting["name"] . '[]" value="' . Tools::stripslashes($value) . '" /> <a href="javascript:void(0);" data-remove="' . $setting["id"] . '_' . $key . '" class="remove_multiple_text"><i class="revicon-trash redicon withhover"></i></a>';
                echo '</div>';
            }
        } else { //fallback to old version
            $key = 0;

            echo '<div class="fontinput_wrapper">';
            echo '<input type="text" ' . $attribs . ' ' . $class . ' ' . $style . ' ' . $disabled . ' id="' . $setting["id"] . '_' . $key . '" name="' . $setting["name"] . '[]" value="' . Tools::stripslashes($setting["value"]) . '" /> <a href="javascript:void(0);" data-remove="' . $setting["id"] . '_' . $key . '" class="remove_multiple_text"><i class="revicon-trash redicon withhover"></i></a>';
            echo '</div>';
        }


        echo '<div class="' . $setting["id"] . '_TEMPLATE" style="display: none;">';
        echo '<div class="fontinput_wrapper">';
        echo '<input type="text" ' . $attribs . ' ' . $class . ' ' . $style . ' id="##ID##" name="##NAME##[]" value="" /> <a href="javascript:void(0);" data-remove="##ID##" class="remove_multiple_text"><i class="revicon-trash redicon withhover"></i></a>';
        echo '</div>';
        echo '</div>';


        echo '<script type="text/javascript">';
        echo 'UniteAdminRev.setMultipleTextKey("' . $setting["id"] . '", "' . $key . '");';

        echo '</script>';
    }

    //-----------------------------------------------------------------------------------------------
    //draw a color picker

    protected function drawColorPickerInput($setting)
    {
        $bgcolor = $setting["value"];

        $bgcolor = str_replace("0x", "#", $bgcolor);

        // set the forent color (by black and white value)

        $rgb = UniteFunctionsRev::html2rgb($bgcolor);

        $bw = UniteFunctionsRev::yiq($rgb[0], $rgb[1], $rgb[2]);

        $color = "#000000";

        if ($bw < 128) {
            $color = "#ffffff";
        }





        $disabled = "";

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $color = "";

            $disabled = 'disabled="disabled"';
        }



        $style = "style='background-color:$bgcolor;color:$color'";

        echo '<input type="text" class="inputColorPicker" id="' . $setting["id"] . '" ' . $style . ' name="' . $setting["name"] . '" value="' . $bgcolor . '" ' . $disabled . ' />';
    }

    //-----------------------------------------------------------------------------------------------
    // draw setting input by type

    protected function drawInputs($setting)
    {
        switch ($setting["type"]) {

            case UniteSettingsRev::TYPE_TEXT:

                $this->drawTextInput($setting);

                break;

            case UniteSettingsRev::TYPE_COLOR:

                $this->drawColorPickerInput($setting);

                break;

            case UniteSettingsRev::TYPE_SELECT:

                $this->drawSelectInput($setting);

                break;

            case UniteSettingsRev::TYPE_CHECKBOX:

                $this->drawCheckboxInput($setting);

                break;

            case UniteSettingsRev::TYPE_RADIO:

                $this->drawRadioInput($setting);

                break;

            case UniteSettingsRev::TYPE_TEXTAREA:

                $this->drawTextAreaInput($setting);

                break;

            case UniteSettingsRev::TYPE_CUSTOM:

                $this->drawCustom($setting);

                break;

            case UniteSettingsRev::TYPE_BUTTON:

                $this->drawButtonSetting($setting);

                break;

            case UniteSettingsRev::TYPE_MULTIPLE_TEXT:

                $this->drawMultipleText($setting);

                break;

            default:

                throw new Exception("wrong setting type - " . $setting["type"]);

                break;
        }
    }

    //-----------------------------------------------------------------------------------------------
    //draw advanced order box

    protected function drawOrderboxAdvanced($setting)
    {
        $items = $setting["items"];

        if (!is_array($items)) {
            $this->throwError("Orderbox error - the items option must be array (items)");
        }



        //get arrItems modify items by saved value



        if (!empty($setting["value"]) &&
            getType($setting["value"]) == "array" &&
            count($setting["value"]) == count($items)):



            $savedItems = $setting["value"];

            //make assoc array by id:

            $arrItems = $arrAssoc = array();

            foreach ($items as $item) {
                $arrAssoc[$item[0]] = $item[1];
            }



            foreach ($savedItems as $item) {
                $value = $item["id"];

                $text = $value;

                if (@RevsliderPrestashop::getIsset($arrAssoc[$value])) {
                    $text = $arrAssoc[$value];
                }

                $arrItems[] = array($value, $text, $item["enabled"]);
            }
        else:

            $arrItems = $items;

        endif;



        echo '<ul class="orderbox_advanced" id="' . $setting["id"] . '">';


        foreach ($arrItems as $arrItem) {
            switch (getType($arrItem)) {

                case "string":

                    $value = $arrItem;

                    $text = $arrItem;

                    $enabled = true;

                    break;

                case "array":

                    $value = $arrItem[0];

                    $text = (count($arrItem) > 1) ? $arrItem[1] : $arrItem[0];

                    $enabled = (count($arrItem) > 2) ? $arrItem[2] : true;

                    break;

                default:

                    $this->throwError("Error in setting:" . $setting . ". unknown item type.");

                    break;
            }

            $checkboxClass = $enabled ? "div_checkbox_on" : "div_checkbox_off";


            echo '<li>';
            echo '<div class="div_value">' . $value . '</div>';
            echo '<div class="div_checkbox ' . $checkboxClass . '"></div>';
            echo '<div class="div_text">' . $text . '</div>';
            echo '<div class="div_handle"></div>';
            echo '</li>';
        }



        echo '</ul>';
    }

    //-----------------------------------------------------------------------------------------------
    //draw order box

    protected function drawOrderbox($setting)
    {
        $items = $setting["items"];



        //get arrItems by saved value

        $arrItems = array();



        if (!empty($setting["value"]) &&
            getType($setting["value"]) == "array" &&
            count($setting["value"]) == count($items)) {
            $savedItems = $setting["value"];



            foreach ($savedItems as $value) {
                $text = $value;

                if (@RevsliderPrestashop::getIsset($items[$value])) {
                    $text = $items[$value];
                }

                $arrItems[] = array("value" => $value, "text" => $text);
            }
        } else {
            foreach ($items as $value => $text) {
                $arrItems[] = array("value" => $value, "text" => $text);
            }
        }



        echo '<ul class="orderbox" id="' . $setting["id"] . '">';

        foreach ($arrItems as $item) {
            $itemKey = $item["value"];

            $itemText = $item["text"];



            $value = (getType($itemKey) == "string") ? $itemKey : $itemText;


            echo '<li>';
            echo '<div class="div_value">' . $value . '</div>';
            echo '<div class="div_text">' . $itemText . '</div>';
            echo '</li>';
        }



        echo '</ul>';
    }

    public function drawButtonSetting($setting)
    {
        $class = UniteFunctionsRev::getVal($setting, "class");

        if (!empty($class)) {
            $class = "class='$class'";
        }



        echo '<input type="button" id="' . $setting["id"] . '" value="' . $setting["value"] . '" ' . $class . '>';
    }

    //-----------------------------------------------------------------------------------------------
    // draw text area input

    protected function drawTextAreaInput($setting)
    {
        $disabled = "";

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $disabled = 'disabled="disabled"';
        }



        //set style

        $style = UniteFunctionsRev::getVal($setting, "style");

        if (!empty($style)) {
            $style = "style='" . $style . "'";
        }



        //set class

        $class = UniteFunctionsRev::getVal($setting, "class");

        if (!empty($class)) {
            $class = "class='$class'";
        }



        echo '<textarea id="' . $setting["id"] . '" ' . $class . ' name="' . $setting["name"] . '" ' . $style . ' ' . $disabled . '>' . $setting["value"] . '</textarea>';
    }

    //-----------------------------------------------------------------------------------------------
    // draw radio input

    protected function drawRadioInput($setting)
    {
        $items = $setting["items"];

        $counter = 0;

        $id = $setting["id"];

        $isDisabled = UniteFunctionsRev::getVal($setting, "disabled", false);

        echo '<span id="' . $id . '" class="radio_wrapper">';

        foreach ($items as $value => $text):

            $counter++;

            $radioID = $id . "_" . $counter;

            $checked = "";

            if ($value == $setting["value"]) {
                $checked = " checked";
            }



            $disabled = "";

            if ($isDisabled == true) {
                $disabled = 'disabled="disabled"';
            }



            echo '<div class="radio_inner_wrapper">';
            echo '<input type="radio" id="' . $radioID . '" value="' . $value . '" name="' . $setting["name"] . '" ' . $disabled . ' ' . $checked . '/>';
            echo '<label for="' . $radioID . '" style="cursor:pointer;">' . __($text) . '</label>';
            echo '</div>';

        endforeach;



        echo '</span>';
    }

    //-----------------------------------------------------------------------------------------------
    // draw checkbox

    protected function drawCheckboxInput($setting)
    {
        $checked = "";

        if ($setting["value"] == true) {
            $checked = 'checked="checked"';
        }



        echo '<input type="checkbox" id="' . $setting["id"] . '" class="inputCheckbox" name="' . $setting["name"] . '" ' . $checked . '/>';
    }

    //-----------------------------------------------------------------------------------------------
    //draw select input

    protected function drawSelectInput($setting)
    {
        $className = "";

        if (@RevsliderPrestashop::getIsset($this->arrControls[$setting["name"]])) {
            $className = "control";
        }

        $class = "";

        if ($className != "") {
            $class = "class='" . $className . "'";
        }



        $disabled = "";

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $disabled = 'disabled="disabled"';
        }

        echo '<select id="' . $setting["id"] . '" name="' . $setting["name"] . '" ' . $disabled . ' ' . $class . '>';


        foreach ($setting["items"] as $value => $text):

            $text = __($text, REVSLIDER_TEXTDOMAIN);

            $selected = "";

            if ($value == $setting["value"]) {
                $selected = 'selected="selected"';
            }



            echo '<option value="' . $value . '" ' . $selected . '>' . $text . '</option>';


        endforeach;


        echo '</select>';
    }

    protected function drawCustom($setting)
    {
        dmp($setting);

        exit();
    }

    protected function drawTextRow($setting)
    {
        $cellStyle = "";

        if (@RevsliderPrestashop::getIsset($setting["padding"])) {
            $cellStyle .= "padding-left:" . $setting["padding"] . ";";
        }



        if (!empty($cellStyle)) {
            $cellStyle = "style='$cellStyle'";
        }



        //set style

        $rowStyle = "";

        if (@RevsliderPrestashop::getIsset($setting["hidden"]) && $setting["hidden"] == true) {
            $rowStyle .= "display:none;";
        }



        if (!empty($rowStyle)) {
            $rowStyle = "style='$rowStyle'";
        }



        echo '<span class="spanSettingsStaticText">' . html_entity_decode($setting["text"]) . '</span>';
    }

    //-----------------------------------------------------------------------------------------------
    //draw hr row

    protected function drawHrRow($setting)
    {

        //set hidden

        $rowStyle = "";

        if (@RevsliderPrestashop::getIsset($setting["hidden"]) && $setting["hidden"] == true) {
            $rowStyle = "style='display:none;'";
        }

        echo '<li id="' . $setting["id"] . '_row" class="hrrow">';

        echo '<hr />';

        echo '</li>';
    }

    //-----------------------------------------------------------------------------------------------
    //draw settings row

    protected function drawSettingRow($setting)
    {

        //set cellstyle:

        $cellStyle = "";

        if (@RevsliderPrestashop::getIsset($setting[UniteSettingsRev::PARAM_CELLSTYLE])) {
            $cellStyle .= $setting[UniteSettingsRev::PARAM_CELLSTYLE];
        }

        //set text style:

        $textStyle = $cellStyle;

        if (@RevsliderPrestashop::getIsset($setting[UniteSettingsRev::PARAM_TEXTSTYLE])) {
            $textStyle .= $setting[UniteSettingsRev::PARAM_TEXTSTYLE];
        }

        if ($textStyle != "") {
            $textStyle = "style='" . $textStyle . "'";
        }

        if ($cellStyle != "") {
            $cellStyle = "style='" . $cellStyle . "'";
        }

        //set hidden

        $rowStyle = "";

        if (@RevsliderPrestashop::getIsset($setting["hidden"]) && $setting["hidden"] == true) {
            $rowStyle = "display:none;";
        }

        if (!empty($rowStyle)) {
            $rowStyle = "style='$rowStyle'";
        }



        //set row class:

        $rowClass = "";

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $rowClass = "class='disabled'";
        }





        //modify text:

        $text = UniteFunctionsRev::getVal($setting, "text", "");

        $text = __($text, REVSLIDER_TEXTDOMAIN);



        // prevent line break (convert spaces to nbsp)

        $text = str_replace(" ", "&nbsp;", $text);



        if ($setting["type"] == UniteSettingsRev::TYPE_CHECKBOX) {
            $text = "<label for='{$setting["id"]}'>{$text}</label>";
        }



        //set settings text width:

        $textWidth = "";

        if (@RevsliderPrestashop::getIsset($setting["textWidth"])) {
            $textWidth = 'width="' . $setting["textWidth"] . '"';
        }



        $description = UniteFunctionsRev::getVal($setting, "description");

        // $description = __($description,REVSLIDER_TEXTDOMAIN);
        if (@RevsliderPrestashop::getIsset(RevsliderPrestashop::$lang[$description])) {
            $description = RevsliderPrestashop::$lang[$description];
        }



        $unit = UniteFunctionsRev::getVal($setting, "unit");

        $unit = __($unit, REVSLIDER_TEXTDOMAIN);



        $required = UniteFunctionsRev::getVal($setting, "required");



        $addHtml = UniteFunctionsRev::getVal($setting, UniteSettingsRev::PARAM_ADDTEXT);

        $addHtmlBefore = UniteFunctionsRev::getVal($setting, UniteSettingsRev::PARAM_ADDTEXT_BEFORE_ELEMENT);

        //set if draw text or not.

        $toDrawText = true;

        if ($setting["type"] == UniteSettingsRev::TYPE_BUTTON || $setting["type"] == UniteSettingsRev::TYPE_MULTIPLE_TEXT) {
            $toDrawText = false;
        }



        $settingID = $setting["id"];

        $attribsText = UniteFunctionsRev::getVal($setting, "attrib_text");



        $info = ($toDrawText == true && $description !== '') ? ' <div class="setting_info">i</div>' : '';



        echo '<li id="' . $settingID . '_row" ' . $rowStyle . " " . $rowClass . '>';



        if ($toDrawText == true):

            echo '<div id="' . $settingID . '_text" class="setting_text" title="' . $description . '" ' . $attribsText . '>' . $text . $info . '</div>';

        endif;



        if (!empty($addHtmlBefore)):

            echo '<div class="settings_addhtmlbefore">' . $addHtmlBefore . '</div>';

        endif;



        echo '<div class="setting_input">';

        $this->drawInputs($setting);

        echo '</div>';
        if (!empty($unit)):

            echo '<div class="setting_unit">' . $unit . '</div>';

        endif;

        if (!empty($required)):

            echo '<div class="setting_required">*</div>';

        endif;

        if (!empty($addHtml)):

            echo '<div class="settings_addhtml">' . $addHtml . '</div>';

        endif;

        echo '<div class="clear"></div>';

        echo '</li>';


        if ($setting['name'] == 'shadow_type') { //For shadow types, add box with shadow types
            $this->drawShadowTypes($setting['value']);
        }
    }

    private function groupSettingsIntoSaps()
    {
        $arrSections = $this->settings->getArrSections();

        $arrSaps = $arrSections[0]["arrSaps"];

        $arrSettings = $this->settings->getArrSettings();

        foreach ($arrSettings as $key => $setting) {
            $sapID = $setting["sap"];

            if (@RevsliderPrestashop::getIsset($arrSaps[$sapID]["settings"])) {
                $arrSaps[$sapID]["settings"][] = $setting;
            } else {
                $arrSaps[$sapID]["settings"] = array($setting);
            }
        }

        return($arrSaps);
    }

    private function drawButtons()
    {
        foreach ($this->arrButtons as $key => $button) {
            if ($key > 0) {
                echo "<span class='hor_sap'></span>";
            }

            echo UniteFunctionsRev::getHtmlLink("#", $button["title"], $button["id"], $button["class"]);
        }
    }

    public function drawSetting($setting, $state = null)
    {
        if (gettype($setting) == "string") {
            $setting = $this->settings->getSettingByName($setting);
        }



        switch ($state) {

            case "hidden":

                $setting["hidden"] = true;

                break;
        }



        switch ($setting["type"]) {

            case UniteSettingsRev::TYPE_HR:

                $this->drawHrRow($setting);

                break;

            case UniteSettingsRev::TYPE_STATIC_TEXT:

                $this->drawTextRow($setting);

                break;

            default:

                $this->drawSettingRow($setting);

                break;
        }
    }

    public function drawSettingsByNames($arrSettingNames, $state = null)
    {
        if (gettype($arrSettingNames) == "string") {
            $arrSettingNames = explode(",", $arrSettingNames);
        }



        foreach ($arrSettingNames as $name) {
            $this->drawSetting($name, $state);
        }
    }

    public function drawSettings()
    {
        $this->prepareToDraw();
        $this->drawHeaderIncludes();


        $arrSaps = $this->groupSettingsIntoSaps();

        $class = "postbox unite-postbox";
        if (!empty($this->addClass)) {
            $class .= " " . $this->addClass;
        }

        //draw wrapper
        echo "<div class='settings_wrapper'>";

        //draw settings - advanced - with sections
        foreach ($arrSaps as $key => $sap):

            //set accordion closed
            $style = "";
            if ($this->isAccordion == false) {
                $h3Class = "class='no-accordion'";
            } else {
                $h3Class = "";
                if ($key > 0) {
                    $style = "style='display:none;'";
                    $h3Class = "class='box_closed'";
                }
            }

            $text = $sap["text"];
            $icon = $sap["icon"];
            $text = __($text, REVSLIDER_TEXTDOMAIN);


            echo '<div class="' . $class . '">';
            echo '<h3 ' . $h3Class . '><i style="float:left;margin-top:4px;font-size:14px;" class="' . $icon . '"></i>';
            if ($this->isAccordion == true):
                echo '<div class="postbox-arrow"></div>';
            endif;

            echo '<span>' . $text . '</span>';
            echo '</h3>';

            echo '<div class="inside" ' . $style . ' >';
            echo '<ul class="list_settings">';

            foreach ($sap["settings"] as $setting) {
                $this->drawSetting($setting);
            }

            echo '</ul>';

            if (!empty($this->arrButtons)) {


                echo '<div class="clear"></div>';
                echo '<div class="settings_buttons">';

                $this->drawButtons();


                echo '</div>	';
                echo '<div class="clear"></div>';
            }

            echo '<div class="clear"></div>';
            echo '</div>';
            echo '</div>';

        endforeach;

        echo "</div>";    //wrapper close
    }

    //-----------------------------------------------------------------------------------------------
    // draw sections menu

    public function drawSections($activeSection = 0)
    {
        if (!empty($this->arrSections)):

            echo "<ul class='listSections' >";

            for ($i = 0; $i < count($this->arrSections); $i++):

                $class = "";

                if ($activeSection == $i) {
                    $class = "class='selected'";
                }

                $text = $this->arrSections[$i]["text"];

                echo '<li ' . $class . '><a onfocus="this.blur()" href="#' . ($i + 1) . '"><div>' . $text . '</div></a></li>';

            endfor;

            echo "</ul>";

        endif;



        //call custom draw function:

        if ($this->customFunction_afterSections) {
            call_user_func($this->customFunction_afterSections);
        }
    }

    private function putAccordionInit()
    {

        echo '<script type="text/javascript">';

        echo 'jQuery(document).ready(function() {';

        echo 'UniteSettingsRev.initAccordion("' . $this->formID . '");';

        echo '});';

        echo '</script>';
    }

    public function isAccordion($activate)
    {
        $this->isAccordion = $activate;
    }

    public function draw($formID = null)
    {
        if (empty($formID)) {
            UniteFunctionsRev::throwError("You must provide formID to side settings.");
        }



        $this->formID = $formID;

        if (!empty($formID)) {


            echo '<form name="' . $formID . '" id="' . $formID . '">';

            $this->drawSettings();

            echo '</form>';
        } else {
            $this->drawSettings();
        }



        if ($this->isAccordion == true) {
            $this->putAccordionInit();
        }
    }

    public function drawShadowTypes($current)
    {



        echo '<li  class="shadowTypes shadowType-0"' . ($current == 0) ? ' style="display: none;"' : '' . '>';

        echo '<img class="shadowTypes shadowType-1" src="' . UniteBaseClassRev::$url_plugin . 'images/shadow1.png"' . ($current == 1) ? '' : ' style="display: none;"' . ' width="271" />';

        echo '<img class="shadowTypes shadowType-2" src="' . UniteBaseClassRev::$url_plugin . 'images/shadow2.png"' . ($current == 2) ? '' : ' style="display: none;"' . ' width="271" />';

        echo '<img class="shadowTypes shadowType-3" src="' . UniteBaseClassRev::$url_plugin . 'images/shadow3.png"' . ($current == 3) ? '' : ' style="display: none;"' . ' width="271" />';

        echo '</li>';

        echo '<script type="text/javascript">';


        echo 'jQuery("#shadow_type").change(function() {';

        echo 'var sel = jQuery(this).val();';

        echo 'jQuery(".shadowTypes").hide();';

        echo 'if (sel != "0") {';

        echo 'jQuery(".shadowType-0").show();';

        echo 'jQuery(".shadowType-" + sel).show();';

        echo '}';

        echo '});';

        echo '</script>';
    }

    public function drawCssEditor()
    {

        echo '<div id="css_editor_wrap" title="' . __("Style Editor", REVSLIDER_TEXTDOMAIN) . '" style="display:none;">';

        echo '<div class="tp-present-wrapper-parent"><div class="tp-present-wrapper"><div class="tp-present-caption"><div id="css_preview" class="">example</div></div></div></div>';

        echo '<ul class="list_idlehover">';

        echo '<li><a href="javascript:void(0)" id="change-type-idle" class="change-type selected"><span class="nowrap">Idle</span></a></li>';

        echo '<li><a href="javascript:void(0)" id="change-type-hover" class="change-type"><span class="nowrap">Hover</span></a></li>					';

        echo '<div style="clear:both"></div>';

        echo '</ul>';

        echo '<div id="css-editor-accordion">';

        echo '<h3>' . __("Simple Editor:", REVSLIDER_TEXTDOMAIN) . '</h3>';

        echo '<div class="css_editor_novice_wrap">';

        echo '<table style="border-spacing:0px">';

        echo '<tr class="css-edit-enable css-edit-title"><td colspan="4"><input class="css_edit_novice" type="checkbox" name="css_allow" /> ' . __("enable ", REVSLIDER_TEXTDOMAIN) . ' <span id="css_editor_allow"></span></td></tr>';

        echo '<tr class="css-edit-enable css-edit-title topborder"><td colspan="4"></td></tr>';

        echo '<tr class="css-edit-title"><td colspan="4">Font</td></tr>';

        echo '<tr>';

        echo '<td>' . __("Family:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<input class="css_edit_novice" style="width:160px; line-height:17px;margin-top:3px;" id="font_family" type="text" name="css_font-family" value="" />';

        echo '<div id="font_family_down" class="ui-state-default ui-corner-all" style="margin-right:0px"><span class="ui-icon ui-icon-arrowthick-1-s"></span></div>';

        echo '</td>';

        echo '<td>' . __("Size:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<div id="font-size-slider"></div>';

        echo '<input class="css_edit_novice" type="hidden" name="css_font-size" value="" disabled="disabled" />';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>' . __("Color:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td><input type="text" name="css_color" data-linkto="color" style="width:160px" class="inputColorPicker css_edit_novice w100" value="" /></td>';



        echo '<td>' . __("Line-Height:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<div id="line-height-slider"></div>';

        echo '<input class="css_edit_novice" type="hidden" name="css_line-height" value="" disabled="disabled" />';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>' . __("Padding:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<div class="sub_main_wrapper">';

        echo '<div class="subslider_wrapper"><input class="css_edit_novice pad-input sub-input" type="text" name="css_padding[]" value="" /><div class="subslider"></div></div>';

        echo '<div class="subslider_wrapper"><input class="css_edit_novice pad-input sub-input" type="text" name="css_padding[]" value="" /><div class="subslider"></div></div>';

        echo '<div class="subslider_wrapper"><input class="css_edit_novice pad-input sub-input" type="text" name="css_padding[]" value="" /><div class="subslider"></div></div>';

        echo '<div class="subslider_wrapper"><input class="css_edit_novice pad-input sub-input" type="text" name="css_padding[]" value="" /><div class="subslider"></div></div>';

        echo '<div style="clear:both"></div>';

        echo '</div>';

        echo '</td>';

        echo '<td>' . __("Weight:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<div id="font-weight-slider"></div>';

        echo '<input class="css_edit_novice" type="hidden" name="css_font-weight" value="" disabled="disabled" />';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>' . __("Style:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td><input type="checkbox" name="css_font-style" class="css_edit_novice" /> ' . __("italic", REVSLIDER_TEXTDOMAIN) . '</td>';



        echo '<td>' . __("Decoration:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<select class="css_edit_novice w100" style="cursor:pointer" name="css_text-decoration">';

        echo '<option value="none">none</option>';

        echo '<option value="underline">underline</option>';

        echo '<option value="overline">overline</option>';

        echo '<option value="line-through">line-through</option>';

        echo '</select>';

        echo '</td>';

        echo '</tr>';

        echo '<tr class="css-edit-title noborder"><td colspan="4"></td></tr>							';

        echo '<tr class="css-edit-title"><td colspan="4">Background</td></tr>';

        echo '<tr>';

        echo '<td>' . __("Color:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<input type="text" name="css_background-color" style="width:160px;float:left" data-linkto="background-color" class="inputColorPicker css_edit_novice" value="" />';

        echo '<a href="javascript:void(0);" id="reset-background-color"><i class="revicon-ccw editoricon" style="float:left"></i></a>';

        echo '</td>';

        echo '<td>' . __("Transparency:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<div id="background-transparency-slider"></div>';

        echo '<input class="css_edit_novice" type="hidden" name="css_background-transparency" value="" disabled="disabled" />';

        echo '</td>';

        echo '</tr>';

        echo '<tr class="css-edit-title noborder"><td colspan="4"></td></tr>							';

        echo '<tr class="css-edit-title"><td colspan="4">Border</td></tr>';

        echo '<tr>';

        echo '<td>' . __("Color:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<input type="text" name="css_border-color-show" data-linkto="border-color" style="width:160px;float:left" class="inputColorPicker css_edit_novice" value="" />';

        echo '<input type="hidden" name="css_border-color" class="css_edit_novice" value="" disabled="disabled" />';

        echo '<a href="javascript:void(0);" id="reset-border-color"><i class="revicon-ccw editoricon" style="float:left"></i></a>';

        echo '</td>';

        echo '<td>' . __("Width:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<div id="border-width-slider"></div>';

        echo '<input class="css_edit_novice" type="hidden" name="css_border-width" value="" disabled="disabled" />';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>' . __("Style:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<select class="css_edit_novice w100" style="cursor:pointer" name="css_border-style">';

        echo '<option value="none">none</option>';

        echo '<option value="dotted">dotted</option>';

        echo '<option value="dashed">dashed</option>';

        echo '<option value="solid">solid</option>';

        echo '<option value="double">double</option>';

        echo '</select>';

        echo '</td>';

        echo '<td>' . __("Radius:", REVSLIDER_TEXTDOMAIN) . '</td>';

        echo '<td>';

        echo '<div class="sub_main_wrapper">										';

        echo '<div class="subslider_wrapper"><input class="css_edit_novice corn-input sub-input" type="text" name="css_border-radius[]" value="" /><div class="subslider"></div></div>';

        echo '<div class="subslider_wrapper"><input class="css_edit_novice corn-input sub-input" type="text" name="css_border-radius[]" value="" /><div class="subslider"></div></div>';

        echo '<div class="subslider_wrapper"><input class="css_edit_novice corn-input sub-input" type="text" name="css_border-radius[]" value="" /><div class="subslider"></div></div>';

        echo '<div class="subslider_wrapper"><input class="css_edit_novice corn-input sub-input" type="text" name="css_border-radius[]" value="" /><div class="subslider"></div></div>';

        echo '<div style="clear:both"></div>';

        echo '</div>';

        echo '</td>';

        echo '</tr>';

        echo '</table>';

        echo '<div class="css_editor-disable-inputs">&nbsp;</div>';

        echo '</div>';

        echo '<h3 class="notopradius" style="margin-top:20px">' . __("Advanced Editor:", REVSLIDER_TEXTDOMAIN) . '</h3>';

        echo '<div>';

        echo '<textarea id="textarea_edit_expert" rows="20" cols="81"></textarea>';

        echo '</div>';

        echo '</div>';

        echo '</div>';



        echo '<div id="dialog-change-css" title="' . __("Save Styles", REVSLIDER_TEXTDOMAIN) . '" style="display:none;">';

        echo '<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 50px 0;"></span>';

        _e('Overwrite the current selected class ', REVSLIDER_TEXTDOMAIN);

        echo '"<span id="current-class-handle"></span>"';

        _e(' or save the styles as a new class?', REVSLIDER_TEXTDOMAIN);

        echo '</p>';

        echo '</div>';



        echo '<div id="dialog-change-css-save-as" title="' . __("Save As", REVSLIDER_TEXTDOMAIN) . '" style="display:none;">';

        echo '<p>';

        _e('Save as class:', REVSLIDER_TEXTDOMAIN);
        echo '<br />';

        echo '<input type="text" name="css_save_as" value="" />';

        echo '</p>';

        echo '</div>';
    }

    public function drawGlobalCssEditor()
    {

        echo '<div id="css_static_editor_wrap" title="' . __("Global Style Editor", REVSLIDER_TEXTDOMAIN) . '" style="display:none;">';

        echo '<div id="css-static-accordion">';

        echo '<h3>' . __("Dynamic Styles (Not Editable):", REVSLIDER_TEXTDOMAIN) . '</h3>';

        echo '<div class="css_editor_novice_wrap">';

        echo '<textarea id="textarea_show_dynamic_styles" rows="20" cols="81"></textarea>';

        echo '</div>';

        echo '<h3 class="notopradius" style="margin-top:20px">' . __("Static Styles:", REVSLIDER_TEXTDOMAIN) . '</h3>';

        echo '<div>';

        echo '<textarea id="textarea_edit_static" rows="20" cols="81"></textarea>';

        echo '</div>';

        echo '</div>';

        echo '</div>';


        echo '<div id="dialog-change-css-static" title="' . __("Save Static Styles", REVSLIDER_TEXTDOMAIN) . '" style="display:none;">';

        echo '<p>';
        echo '<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 50px 0;"></span>';
        _e('Overwrite current static styles?', REVSLIDER_TEXTDOMAIN);
        echo '</p>';

        echo '</div>';
    }
}
