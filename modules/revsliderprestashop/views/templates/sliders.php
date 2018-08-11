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

$exampleID = '"slider1"';
$dir = plugin_dir_path();
$arrSliders = RevGlobalObject::getVar('arrSliders');
if (!empty($arrSliders)) {
    $exampleID = '"' . $arrSliders[0]->getAlias() . '"';
}
$outputTemplates = false;
$latest_version = get_option('revslider-latest-version', GlobalsRevSlider::SLIDER_REVISION);
if (version_compare($latest_version, GlobalsRevSlider::SLIDER_REVISION, '>')) {
    
} else {
    
}

?>
<div class='wrap'>
    <div class="clear_both"></div> 
    <div class="title_line" style="margin-bottom:10px">
        <div id="icon-options-general" class="icon32"></div>
        <a href="<?php echo GlobalsRevSlider::LINK_HELP_SLIDERS ?>" class="button-secondary float_right mtop_10 mleft_10" target="_blank"><?php echo RevsliderPrestashop::$lang['help']; ?></a>			
    </div>
    <div class="clear_both"></div> 
    <div class="title_line nobgnopd">
        <div class="view_title">
<?php echo RevsliderPrestashop::$lang['Revolution_Sliders']; ?>
        </div>
        <div class="slider-sortandfilter">
            <span class="slider-listviews slider-lg-views" data-type="rs-listview"><i class="eg-icon-align-justify"></i></span>
            <span class="slider-gridviews slider-lg-views active" data-type="rs-gridview"><i class="eg-icon-th"></i></span>
        </div>
    </div>
    <?php
    $no_sliders = false;
    if (empty($arrSliders)) {

        ?>
        <span style="display:block;margin-top:15px;margin-bottom:15px;">
            <?php
            echo RevsliderPrestashop::$lang['No_Sliders_Found'];
            $no_sliders = true;

            ?>
        </span>
        <?php
    }
    require self::getPathTemplate("sliders_list");

    ?>
    <div style="width:100%;height:50px"></div>
    <!--<div class="title_line nobgnopd"><div class="view_title"><?php echo RevsliderPrestashop::$lang['Revolution_Slider_Temp']; ?></div></div>
    <?php
    $no_sliders = false;
    $arrSlidersTemplates = RevGlobalObject::getVar('arrSlidersTemplates');
    if (empty($arrSlidersTemplates)) {
        echo RevsliderPrestashop::$lang['No_Template_Found'];
        $no_sliders = true;

        ?><br><?php
    }
    $outputTemplates = true;
    RevGlobalObject::setVar('outputTemplates', $outputTemplates);
    RevGlobalObject::setVar('no_sliders', $no_sliders);
    require self::getPathTemplate("sliders_list");

    ?>		
    -->

    <div style="width:100%;height:17px"></div>
    <div class="tab-data">
<?php
require self::getPathTemplate("ps_layout");

?>
    </div>

<!-- <iframe style="width:100%; min-height: 400px; overflow: visible;" src="//smartdatasoft.com/referals/iframe-revolution-prestashop.php"></iframe> -->

    <div style="width:100%;height:50px"></div>
    <div id="dialog_import_slider" title="<?php echo RevsliderPrestashop::$lang['Import_Slider']; ?>" class="dialog_import_slider" style="display:none">
        <form action="<?php echo UniteBaseClassRev::$url_ajax ?>" enctype="multipart/form-data" method="post">
            <br>
            <input type="hidden" name="action" value="revslider_ajax_action">
            <input type="hidden" name="client_action" value="import_slider_slidersview">
<?php echo RevsliderPrestashop::$lang['Choose_import_file']; ?>:   
            <br>
            <input type="file" size="60" name="import_file" class="input_import_slider">
            <br><br>
            <span style="font-weight: 700;"><?php echo RevsliderPrestashop::$lang['CUSTOM_STYLES']; ?></span><br><br>
            <table class="impo_slide">
                <tr>
                    <td><?php echo RevsliderPrestashop::$lang['Custom_Animations']; ?></td>
                    <td><input type="radio" name="update_animations" value="true" checked="checked"> <?php echo RevsliderPrestashop::$lang['overwrite']; ?></td>
                    <td><input type="radio" name="update_animations" value="false"> <?php echo RevsliderPrestashop::$lang['append']; ?></td>
                </tr>
                <tr>
                    <td>Custom Navigations:</td>
                    <td><input type="radio" name="update_navigations" value="true" checked="checked"> <?php echo RevsliderPrestashop::$lang['overwrite']; ?> </td>
                    <td><input type="radio" name="update_navigations" value="false"> <?php echo RevsliderPrestashop::$lang['append']; ?></td>
                </tr>
                <tr>
                    <td><?php echo RevsliderPrestashop::$lang['Static_Styles']; ?></td>
                    <td><input type="radio" name="update_static_captions" value="true"> <?php echo RevsliderPrestashop::$lang['overwrite']; ?></td>
                    <td><input type="radio" name="update_static_captions" value="false"> <?php echo RevsliderPrestashop::$lang['append']; ?></td>
                    <td><input type="radio" name="update_static_captions" value="none" checked="checked"> ignore</td>
                </tr>
            </table>
            <br><br>
            <input type="submit" class='button-primary rev-import-slider-button' value="<?php echo RevsliderPrestashop::$lang['Import_Slider']; ?>">
        </form>		
    </div>

    <div id="dialog_duplicate_slider" class="dialog_duplicate_layer" title="<?php _e('Duplicate', 'revslider'); ?>" style="display:none;">
        <div style="margin-top:14px">
            <span style="margin-right:15px"><?php _e('Title:', 'revslider'); ?></span><input id="rs-duplicate-animation" type="text" name="rs-duplicate-animation" value="" />
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function() {
            RevSliderAdmin.initSlidersListView();
            jQuery('#benefitsbutton').hover(function() {
                jQuery('#benefitscontent').slideDown(200);
            }, function() {
                jQuery('#benefitscontent').slideUp(200);
            })
            jQuery('#tp-validation-box').click(function() {
                jQuery(this).css({cursor: "default"});
                if (jQuery('#rs-validation-wrapper').css('display') == "none") {
                    jQuery('#tp-before-validation').hide();
                    jQuery('#rs-validation-wrapper').slideDown(200);
                }
            })
        });
    </script>
<?php
// @codingStandardsIgnoreEnd
