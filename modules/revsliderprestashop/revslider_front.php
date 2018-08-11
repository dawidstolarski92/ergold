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

class RevSliderFront extends UniteBaseFrontClassRev
{

    public function __construct($mainFilepath)
    {
        parent::__construct($mainFilepath, $this);

        //set table names

        GlobalsRevSlider::$table_sliders = self::$table_prefix . GlobalsRevSlider::TABLE_SLIDERS_NAME;

        GlobalsRevSlider::$table_slides = self::$table_prefix . GlobalsRevSlider::TABLE_SLIDES_NAME;

        GlobalsRevSlider::$table_static_slides = self::$table_prefix . GlobalsRevSlider::TABLE_STATIC_SLIDES_NAME;

        GlobalsRevSlider::$table_settings = self::$table_prefix . GlobalsRevSlider::TABLE_SETTINGS_NAME;

        GlobalsRevSlider::$table_css = self::$table_prefix . GlobalsRevSlider::TABLE_CSS_NAME;

        GlobalsRevSlider::$table_navigation = self::$table_prefix . GlobalsRevSlider::TABLE_NAVIGATION_NAME;

        GlobalsRevSlider::$table_layer_anims = self::$table_prefix . GlobalsRevSlider::TABLE_LAYER_ANIMS_NAME;
    }

    public static function onAddScripts()
    {
        $operations = new RevOperations();

        $arrValues = $operations->getGeneralSettingsValues();

        $includesGlobally = UniteFunctionsRev::getVal($arrValues, "includes_globally", "on");

        $includesFooter = UniteFunctionsRev::getVal($arrValues, "js_to_footer", "off");

        $strPutIn = UniteFunctionsRev::getVal($arrValues, "pages_for_includes");

        $isPutIn = RevSliderOutput::isPutIn($strPutIn, true);
    }

    public function putJavascript()
    {

        //$urlPlugin = UniteBaseClassRev::$url_plugin."rs-plugin/";
    }
}
