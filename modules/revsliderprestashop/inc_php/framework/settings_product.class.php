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

class UniteSettingsRevProductRev extends UniteSettingsOutputRev
{

    protected function drawTextInput($setting)
    {
        $disabled = "";

        $style = "";

        $readonly = "";


        if (@RevsliderPrestashop::getIsset($setting["style"])) {
            $style = "style='" . $setting["style"] . "'";
        }

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $disabled = 'disabled="disabled"';
        }

        if (@RevsliderPrestashop::getIsset($setting["readonly"])) {
            $readonly = "readonly='readonly'";
        }

        $class = "regular-text";



        if (@RevsliderPrestashop::getIsset($setting["class"]) && !empty($setting["class"])) {
            $class = $setting["class"];

            switch ($class) {

                case "small":

                    $class = "small-text";

                    break;

                case "code":

                    $class = "regular-text code";

                    break;
            }
        }



        if (!empty($class)) {
            $class = "class='$class'";
        }


        echo '<input type="text" ' . $class . ' ' . $style . ' ' . $disabled . '' . $readonly . ' id="' . $setting["id"] . '" name="' . $setting["name"] . '" value="' . $setting["value"] . '" />';
    }

    protected function drawHiddenInput($setting)
    {
        $disabled = "";

        $style = "";

        $readonly = "";



        if (@RevsliderPrestashop::getIsset($setting["style"])) {
            $style = "style='" . $setting["style"] . "'";
        }

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $disabled = 'disabled="disabled"';
        }



        if (@RevsliderPrestashop::getIsset($setting["readonly"])) {
            $readonly = "readonly='readonly'";
        }



        $class = "regular-text";



        if (@RevsliderPrestashop::getIsset($setting["class"]) && !empty($setting["class"])) {
            $class = $setting["class"];

            //convert short classes:

            switch ($class) {

                case "small":

                    $class = "small-text";

                    break;

                case "code":

                    $class = "regular-text code";

                    break;
            }
        }



        if (!empty($class)) {
            $class = "class='$class'";
        }


        echo '<input type="hidden" ' . $class . ' ' . $style . ' ' . $disabled . '' . $readonly . ' id="' . $setting["id"] . '" name="' . $setting["name"] . '" value="' . $setting["value"] . '" />';
    }

    protected function drawImageInput($setting)
    {
        $class = UniteFunctionsRev::getVal($setting, "class");



        if (!empty($class)) {
            $class = "class='$class'";
        }



        $settingsID = $setting["id"];

        $buttonID = $settingsID . "_button";

        $spanPreviewID = $buttonID . "_preview";

        $img = "";

        $value = UniteFunctionsRev::getVal($setting, "value");



        if (!empty($value)) {
            $urlImage = $value;

            $imagePath = UniteFunctionsWPRev::getImageRealPathFromUrl($urlImage);

            if (file_exists($imagePath)) {
                $filepath = UniteFunctionsWPRev::getImagePathFromURL($urlImage);

                $urlImage = UniteBaseClassRev::getImageUrl($filepath, 100, 70, true);
            }



            $img = "<img width='100' height='70' src='$urlImage'></img>";
        }



        echo '<span id="' . $spanPreviewID . '" class="setting-image-preview">' . $img . '</span>';

        echo '<input type="hidden" id="' . $setting["id"] . '" name="' . $setting["name"] . '" value="' . $setting["value"] . '" />';

        echo '<input type="button" id="' . $buttonID . '" class="button-image-select button-primary revblue' . $class . '" value="Choose Image" />';
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

        echo '<input type="text" class="inputColorPicker" id="' . $setting["id"] . '" ' . $style . ' name="' . $setting["name"] . '" value="' . $bgcolor . '" ' . $disabled . ' >';
    }

    //-----------------------------------------------------------------------------------------------
    //draw a date picker

    protected function drawDatePickerInput($setting)
    {
        $date = $setting["value"];


        echo '<input type="text" class="inputDatePicker" id="' . $setting["id"] . '" name="' . $setting["name"] . '" value="' . $date . '" />';
    }

    protected function drawInputs($setting)
    {
        switch ($setting["type"]) {

            case UniteSettingsRev::TYPE_HIDDEN:

                $this->drawHiddenInput($setting);

                break;

            case UniteSettingsRev::TYPE_TEXT:

                $this->drawTextInput($setting);

                break;

            case UniteSettingsRev::TYPE_COLOR:

                $this->drawColorPickerInput($setting);

                break;

            case UniteSettingsRev::TYPE_DATE:

                $this->drawDatePickerInput($setting);

                break;

            case UniteSettingsRev::TYPE_SELECT:

                $this->drawSelectInput($setting);

                break;

            case UniteSettingsRev::TYPE_CHECKLIST:

                $this->drawChecklistInput($setting);

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

            case UniteSettingsRev::TYPE_IMAGE:

                $this->drawImageInput($setting);

                break;

            case UniteSettingsRev::TYPE_CUSTOM:

                if (method_exists($this, "drawCustomInputs") == false) {
                    UniteFunctionsRev::throwError("Method don't exists: drawCustomInputs, please override the class");
                }

                $this->drawCustomInputs($setting);

                break;

            default:

                throw new Exception("wrong setting type - " . $setting["type"]);

                break;
        }
    }

    //-----------------------------------------------------------------------------------------------
    // draw text area input



    protected function drawTextAreaInput($setting)
    {
        $disabled = "";

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $disabled = 'disabled="disabled"';
        }



        $style = "";

        if (@RevsliderPrestashop::getIsset($setting["style"])) {
            $style = "style='" . $setting["style"] . "'";
        }



        $rows = UniteFunctionsRev::getVal($setting, "rows");

        if (!empty($rows)) {
            $rows = "rows='$rows'";
        }



        $cols = UniteFunctionsRev::getVal($setting, "cols");

        if (!empty($cols)) {
            $cols = "cols='$cols'";
        }

        echo '<textarea id="' . $setting["id"] . '" name="' . $setting["name"] . '" ' . $style . ' ' . $disabled . ' ' . $rows . ' ' . $cols . '  >' . $setting["value"] . '</textarea>';

        if (!empty($cols)) {
            echo "<br>";
        }    //break line on big textareas.
    }

    protected function drawRadioInput($setting)
    {
        $items = $setting["items"];

        $settingID = UniteFunctionsRev::getVal($setting, "id");

        $wrapperID = $settingID . "_wrapper";



        $addParams = UniteFunctionsRev::getVal($setting, "addparams");



        $counter = 0;



        $outputHtml = '<span id="' . $wrapperID . '" class="radio_settings_wrapper" ' . $addParams . '>';


        foreach ($items as $value => $text):

            $counter++;

            $radioID = $setting["id"] . "_" . $counter;

            $checked = "";

            if ($value == $setting["value"]) {
                $checked = " checked='checked'";
            }



            $outputHtml .= '<div class="radio_inner_wrapper">';

            $outputHtml .= '<input type="radio" id="' . $radioID . '" value="' . $value . '" name="' . $setting["name"] . '" ' . $checked . '/>';

            $outputHtml .= '<label for="' . $radioID . '" style="cursor:pointer;">' . $text . '</label>';

            $outputHtml .= '</div>';


        endforeach;

        $outputHtml .= '</span>';
        echo $outputHtml;
    }

    protected function drawCheckboxInput($setting)
    {
        $checked = "";

        if ($setting["value"] == true) {
            $checked = 'checked="checked"';
        }

        echo '<input type="checkbox" id="' . $setting["id"] . '" class="inputCheckbox" name="' . $setting["name"] . '" ' . $checked . '/>';
    }

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



        $args = UniteFunctionsRev::getVal($setting, "args");



        $settingValue = $setting["value"];



        if (strpos($settingValue, ",") !== false) {
            $settingValue = explode(",", $settingValue);
        }

        $outputHtml = '<select id="' . $setting["id"] . '" name="' . $setting["name"] . '" ' . $disabled . ' ' . $class . ' ' . $args . '>';


        foreach ($setting["items"] as $value => $text):

            //set selected

            $selected = "";

            $addition = "";

            if (strpos($value, "option_disabled") === 0) {
                $addition = "disabled";
            } else {
                if (is_array($settingValue)) {
                    if (array_search($value, $settingValue) !== false) {
                        $selected = 'selected="selected"';
                    }
                } else {
                    if ($value == $settingValue) {
                        $selected = 'selected="selected"';
                    }
                }
            }


            $outputHtml .= '<option ' . $addition . ' value="' . $value . '" ' . $selected . '>' . $text . '</option>';


        endforeach;


        $outputHtml .= '</select>';
        echo $outputHtml;
    }

    protected function drawChecklistInput($setting)
    {
        $className = "input_checklist";

        if (@RevsliderPrestashop::getIsset($this->arrControls[$setting["name"]])) {
            $className .= " control";
        }



        $class = "";

        if ($className != "") {
            $class = "class='" . $className . "'";
        }

        $disabled = "";

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $disabled = 'disabled="disabled"';
        }

        $args = UniteFunctionsRev::getVal($setting, "args");

        $settingValue = $setting["value"];


        if (strpos($settingValue, ",") !== false) {
            $settingValue = explode(",", $settingValue);
        }


        $style = "z-index:1000;";

        $minWidth = UniteFunctionsRev::getVal($setting, "minwidth");



        if (!empty($minWidth)) {
            $style .= "min-width:" . $minWidth . "px;";

            $args .= " data-minwidth='" . $minWidth . "'";
        }



        $outputHtml = '<select id="' . $setting["id"] . '" name="' . $setting["name"] . '" ' . $disabled . ' multiple ' . $class . ' ' . $args . ' size="1" style="' . $style . '">';


        foreach ($setting["items"] as $value => $text):

            //set selected

            $selected = "";

            $addition = "";

            if (strpos($value, "option_disabled") === 0) {
                $addition = "disabled";
            } else {
                if (is_array($settingValue)) {
                    if (array_search($value, $settingValue) !== false) {
                        $selected = 'selected="selected"';
                    }
                } else {
                    if ($value == $settingValue) {
                        $selected = 'selected="selected"';
                    }
                }
            }

            $outputHtml .= '<option ' . $addition . ' value="' . $value . '" ' . $selected . '>' . $text . '</option>';


        endforeach;

        $outputHtml .= '</select>';
        echo $outputHtml;
    }

    //-----------------------------------------------------------------------------------------------
    //draw hr row

    protected function drawTextRow($setting)
    {



        //set cell style

        $cellStyle = "";

        if (@RevsliderPrestashop::getIsset($setting["padding"])) {
            $cellStyle .= "padding-left:" . $setting["padding"] . ";";
        }



        if (!empty($cellStyle)) {
            $cellStyle = "style='$cellStyle'";
        }



        //set style

        $rowStyle = "";

        if (@RevsliderPrestashop::getIsset($setting["hidden"])) {
            $rowStyle .= "display:none;";
        }



        if (!empty($rowStyle)) {
            $rowStyle = "style='$rowStyle'";
        }



        $outputHtml = '<tr id="' . $setting["id_row"] . '" ' . $rowStyle . ' valign="top">';

        $outputHtml .= '<td colspan="4" align="right" ' . $cellStyle . '>';

        $outputHtml .= '<span class="spanSettingsStaticText">' . $setting["text"] . '</span>';

        $outputHtml .= '</td>';

        $outputHtml .= '</tr>';

        echo $outputHtml;
    }

    //-----------------------------------------------------------------------------------------------
    //draw hr row

    protected function drawHrRow($setting)
    {

        //set hidden

        $rowStyle = "";

        if (@RevsliderPrestashop::getIsset($setting["hidden"])) {
            $rowStyle = "style='display:none;'";
        }



        $class = UniteFunctionsRev::getVal($setting, "class");

        if (!empty($class)) {
            $class = "class='$class'";
        }


        $outputHtml = '<tr id="' . $setting["id_row"] . '" ' . $rowStyle . '>';

        $outputHtml .= '<td colspan="4" align="left" style="text-align:left;">';

        $outputHtml .= '<hr ' . $class . ' />';

        $outputHtml .= '</td>';

        $outputHtml .= '</tr>';

        echo $outputHtml;
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

        if (@RevsliderPrestashop::getIsset($setting["hidden"])) {
            $rowStyle = "display:none;";
        }

        if (!empty($rowStyle)) {
            $rowStyle = "style='$rowStyle'";
        }



        //set text class:

        $class = "";

        if (@RevsliderPrestashop::getIsset($setting["disabled"])) {
            $class = "class='disabled'";
        }



        //modify text:

        $text = UniteFunctionsRev::getVal($setting, "text", "");

        // prevent line break (convert spaces to nbsp)

        $text = str_replace(" ", "&nbsp;", $text);

        switch ($setting["type"]) {

            case UniteSettingsRev::TYPE_CHECKBOX:

                $text = "<label for='" . $setting["id"] . "' style='cursor:pointer;'>$text</label>";

                break;
        }



        //set settings text width:

        $textWidth = "";

        if (@RevsliderPrestashop::getIsset($setting["textWidth"])) {
            $textWidth = 'width="' . $setting["textWidth"] . '"';
        }



        $description = UniteFunctionsRev::getVal($setting, "description");

        $required = UniteFunctionsRev::getVal($setting, "required");



        echo '<tr id="' . $setting["id_row"] . '" ' . $rowStyle . ' ' . $class . ' valign="top">';


        if ($setting['type'] == UniteSettingsRev::TYPE_HIDDEN) {

            echo '<td colspan="3" ' . $cellStyle . '>';

            $this->drawInputs($setting);


            echo '</td>';
        } else {


            echo '<th ' . $textStyle . ' scope="row" ' . $textWidth . '>';

            echo $text . ':';
            echo '</th>';

            echo '<td ' . $cellStyle . '>';


            $this->drawInputs($setting);

            if (!empty($required)):

                echo "<span class='setting_required'>*</span>";

            endif;

            echo '<div class="description_container">';

            if (!empty($description)):

                echo '<span class="description">' . $description . '</span>';

            endif;

            echo '</div>';

            echo '</td>';

            echo '<td class="description_container_in_td">';

            if (!empty($description)):

                echo '<span class="description">' . $description . '</span>';

            endif;

            echo '</td>';
        }

        echo '</tr>';
    }

    //-----------------------------------------------------------------------------------------------
    //draw all settings

    public function drawSettings()
    {
        $this->drawHeaderIncludes();

        $this->prepareToDraw();



        //draw main div

        $lastSectionKey = -1;

        $visibleSectionKey = 0;

        $lastSapKey = -1;



        $arrSections = $this->settings->getArrSections();

        $arrSettings = $this->settings->getArrSettings();



        //draw settings - simple

        if (empty($arrSections)):

            echo "<table class='form-table'>";
            foreach ($arrSettings as $key => $setting) {
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

            echo '</table>';
        else:

            //draw settings - advanced - with sections

            foreach ($arrSettings as $key => $setting):


                //operate sections:

                if (!empty($arrSections) && @RevsliderPrestashop::getIsset($setting["section"])) {
                    $sectionKey = $setting["section"];



                    if ($sectionKey != $lastSectionKey):

                        $arrSaps = $arrSections[$sectionKey]["arrSaps"];


                        if (!empty($arrSaps)) {

                            //close sap

                            if ($lastSapKey != -1):


                                echo '</table></div>';

                            endif;

                            $lastSapKey = -1;
                        }



                        $style = ($visibleSectionKey == $sectionKey) ? "" : "style='display:none'";



                        //close section

                        if ($sectionKey != 0):

                            if (empty($arrSaps)) {
                                echo "</table>";
                            }

                            echo "</div>\n";

                        endif;



                        //if no saps - add table

                        if (empty($arrSaps)):

                            echo '<table class="form-table">';
                        endif;

                    endif;

                    $lastSectionKey = $sectionKey;
                }//end section manage
                //operate saps

                if (!empty($arrSaps) && @RevsliderPrestashop::getIsset($setting["sap"])) {
                    $sapKey = $setting["sap"];

                    if ($sapKey != $lastSapKey) {
                        $sap = $this->settings->getSap($sapKey, $sectionKey);

                        if ($sapKey != 0):

                            echo '</table>';

                        endif;

                        $style = "";

                        $class = "divSapControl";



                        if ($sapKey == 0 || @RevsliderPrestashop::getIsset($sap["opened"]) && $sap["opened"] == true) {
                            $style = "";

                            $class = "divSapControl opened";
                        }


                        echo '<div id="divSapControl_' . $sectionKey . "_" . $sapKey . '" class="' . $class . '">';

                        echo '<h3>' . $sap["text"] . '</h3>';

                        echo '</div>';

                        echo '<div id="divSap_' . $sectionKey . "_" . $sapKey . '" class="divSap" ' . $style . '>';

                        echo '<table class="form-table">';


                        $lastSapKey = $sapKey;
                    }
                }//saps manage
                //draw row:

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

            endforeach;

        endif;

        ?>

        </table>



        <?php
        if (!empty($arrSections)):

            if (empty($arrSaps)) {     //close table settings if no saps
                echo "</table>";
            }

            echo "</div>\n";     //close last section div

        endif;
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

    public function draw($formID = null, $drawForm = false)
    {
        if (empty($formID)) {
            UniteFunctionsRev::throwError("The form ID can't be empty. you must provide it");
        }

        

        $this->formID = $formID;

        echo '<div class="settings_wrapper unite_settings_wide">';

        if ($drawForm == true) {

            echo '<form name="' . $formID . '" id="' . $formID . '">';
            
            $this->drawSettings();
            
            echo '</form>';
        } else {
            $this->drawSettings();
        }

        echo '</div>';
        
    }
}
