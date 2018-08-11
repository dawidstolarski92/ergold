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
$revSliderVersion = GlobalsRevSlider::SLIDER_REVISION;

$wrapperClass = "";

if (GlobalsRevSlider::$isNewVersion == false) {
    $wrapperClass = " oldwp";
}


$rsop = new RevSliderOperations();
$glval = $rsop->getGeneralSettingsValues();

?>
<?php
$waitstyle = '';
if (@RevsliderPrestashop::getIsset($_REQUEST['update_shop'])) {
    $waitstyle = 'display:block';
}

?>
<div id="waitaminute" style="<?php echo $waitstyle; ?>">
    <div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br><?php _e("Please Wait...", 'revslider'); ?></div>
</div>

<script type="text/javascript">
    var g_uniteDirPlugin = "revslider";
    var g_urlContent = "<?php echo str_replace(array("\n", "\r", chr(10), chr(13)), array(''), content_url()) . "/"; ?>";
    var g_urlAjaxShowImage = "<?php echo RevSliderBase::$url_ajax_showimage; ?>";
    var g_urlAjaxActions = "<?php echo RevSliderBase::$url_ajax_actions; ?>";
    var g_revslider_url = "<?php echo _MODULE_DIR_ . 'revsliderprestashop/'; ?>";
    var g_settingsObj = {};

    var global_grid_sizes = {
        'desktop': '<?php echo RevSliderBase::getVar($glval, 'width', 1230); ?>',
        'notebook': '<?php echo RevSliderBase::getVar($glval, 'width_notebook', 1230); ?>',
        'tablet': '<?php echo RevSliderBase::getVar($glval, 'width_tablet', 992); ?>',
        'mobile': '<?php echo RevSliderBase::getVar($glval, 'width_mobile', 480); ?>'
    };

</script>



<div id="rs-preview-wrapper" style="display: none;">
    <div id="rs-preview-wrapper-inner">
        <div id="rs-preview-info">
            <div class="rs-preview-toolbar">
                <a class="rs-close-preview"><i class="eg-icon-cancel"></i></a>
            </div>

            <div data-type="desktop" class="rs-preview-device_selector_prev rs-preview-ds-desktop selected"></div>									
            <div data-type="notebook" class="rs-preview-device_selector_prev rs-preview-ds-notebook"></div>					
            <div data-type="tablet" class="rs-preview-device_selector_prev rs-preview-ds-tablet"></div>					
            <div data-type="mobile" class="rs-preview-device_selector_prev rs-preview-ds-mobile"></div>

        </div>
        <div class="rs-frame-preview-wrapper">
            <iframe id="rs-frame-preview" name="rs-frame-preview"></iframe>
        </div>
    </div>
</div>
<form id="rs-preview-form" name="rs-preview-form" action="<?php echo RevSliderBase::$url_ajax_actions; ?>" target="rs-frame-preview" method="post">
    <input type="hidden" id="rs-client-action" name="client_action" value="">

    <!-- SPECIFIC FOR SLIDE PREVIEW -->
    <input type="hidden" name="data" value="" id="preview-slide-data">

    <!-- SPECIFIC FOR SLIDER PREVIEW -->
    <input type="hidden" id="preview_sliderid" name="sliderid" value="">
    <input type="hidden" id="preview_slider_markup" name="only_markup" value="">
</form>


<div id="dialog_preview_sliders" class="dialog_preview_sliders" title="Preview Slider" style="display:none;">
    <iframe id="frame_preview_slider" name="frame_preview_slider" style="width: 100%;"></iframe>
</div>

<script type="text/javascript">



<?php
$sds_admin_url = admin_url();

// $sds_admin_upload_url =  _MODULE_DIR_ ."revsliderprestashop/filemanager/dialog.php?type=0&lang=en&popup=0&field_id=0&fldr=&5473a39f286af";
// $sds_admin_upload_url =  admin_url('?view=dialog');
$sds_admin_upload_url = controller_upload_url('&view=dialog');

?>

    var rev_php_ver = '<?php echo phpversion() ?>';

    var g_uniteDirPlagin = "<?php echo RevSliderAdmin::$dir_plugin ?>";

    var g_urlContent = "<?php echo UniteFunctionsWPRev::getUrlContent() ?>";



//        var ajaxurl = g_urlContent+'ajax.php?returnurl=<?php echo urlencode(htmlspecialchars_decode($sds_admin_url)) ?>';
    ajaxurl += '&returnurl=<?php echo urlencode(htmlspecialchars_decode($sds_admin_url)) ?>';


    var uploadurl = '<?php echo htmlspecialchars_decode($sds_admin_upload_url) ?>';



    var g_urlAjaxShowImage = "<?php echo htmlspecialchars_decode(UniteBaseClassRev::$url_ajax_showimage) ?>";



    var g_urlAjaxActions = "<?php echo htmlspecialchars_decode(UniteBaseClassRev::$url_ajax_actions) ?>";



    var g_settingsObj = {};


    // Preview Scripts
    jQuery('body').on('click', '.rs-preview-device_selector_prev', function() {
        var btn = jQuery(this);
        jQuery('.rs-preview-device_selector_prev.selected').removeClass("selected");
        btn.addClass("selected");

        var w = parseInt(global_grid_sizes[btn.data("type")], 0);
        if (w > 1450)
            w = 1450;
        jQuery('#rs-preview-wrapper-inner').css({maxWidth: w + "px"});

    });

    jQuery(window).resize(function() {
        var ww = jQuery(window).width();
        if (global_grid_sizes)
            jQuery.each(global_grid_sizes, function(key, val) {
                if (ww <= parseInt(val, 0)) {
                    jQuery('.rs-preview-device_selector_prev.selected').removeClass("selected");
                    jQuery('.rs-preview-device_selector_prev[data-type="' + key + '"]').addClass("selected");
                }
            })
    })


    /* SHOW A WAIT FOR PROGRESS */
    function showWaitAMinute(obj) {
        var wm = jQuery('#waitaminute');
        // SHOW AND HIDE WITH DELAY
        if (obj.delay != undefined) {

            punchgs.TweenLite.to(wm, 0.3, {autoAlpha: 1, ease: punchgs.Power3.easeInOut});
            punchgs.TweenLite.set(wm, {display: "block"});

            setTimeout(function() {
                punchgs.TweenLite.to(wm, 0.3, {autoAlpha: 0, ease: punchgs.Power3.easeInOut, onComplete: function() {
                        punchgs.TweenLite.set(wm, {display: "block"});
                    }});
            }, obj.delay)
        }

        // SHOW IT
        if (obj.fadeIn != undefined) {
            punchgs.TweenLite.to(wm, obj.fadeIn / 1000, {autoAlpha: 1, ease: punchgs.Power3.easeInOut});
            punchgs.TweenLite.set(wm, {display: "block"});
        }

        // HIDE IT
        if (obj.fadeOut != undefined) {

            punchgs.TweenLite.to(wm, obj.fadeOut / 1000, {autoAlpha: 0, ease: punchgs.Power3.easeInOut, onComplete: function() {
                    punchgs.TweenLite.set(wm, {display: "block"});
                }});
        }

        // CHANGE TEXT
        if (obj.text != undefined) {
            switch (obj.text) {
                case "progress1":

                    break;
                default:
                    wm.html('<div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br>' + obj.text + '</div>');
                    break;
            }
        }
    }



</script>



<div id="div_debug"></div>



<div class='unite_error_message' id="error_message" style="display:none;"></div>



<div class='unite_success_message' id="success_message" style="display:none;"></div>



<div id="viewWrapper" class="view_wrapper<?php echo $wrapperClass ?>">



<?php
$view = RevGlobalObject::getVar('view');
RevSliderAdmin::requireView($view);

?>



</div>



<div id="divColorPicker" style="display:none;"></div>



<?php RevSliderAdmin::requireView("system/video_dialog") ?>

<?php RevSliderAdmin::requireView("system/update_dialog") ?>

<?php RevSliderAdmin::requireView("system/general_settings_dialog") ?>



<div class="tp-plugin-version">
    <div class="smartsupport" style="float: left;">Open ticket in our <a href="https://smartdatasoft.zendesk.com" target="_blank"><strong>support</strong></a> system if you found issues. Follow our <a href="http://smartdatasoft.com/doc/ps/revolution/" target="_blank"><strong>documentation</strong></a> page to get usability informations.</div>
    <div class="rev_copyright" style="float: right;">&copy; All rights reserved, <a href="http://themepunch.com" target="_blank">Themepunch</a>  ver. <?php echo $revSliderVersion ?></div>
</div>

<?php if (GlobalsRevSlider::SHOW_DEBUG == true): ?>

    Debug Functions (for developer use only):

    <br><br>

    <a id="button_update_text" class="button-primary revpurple" href="javascript:void(0)">Update Text</a>

<?php endif;
// @codingStandardsIgnoreEnd