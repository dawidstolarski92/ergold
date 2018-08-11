<?php
/**
 * 2016 Revolution Slider
 *
 *  @author    SmatDataSoft <support@smartdatasoft.com>
 *  @copyright 2016 SmatDataSoft
 *  @license   private
 *  @version   5.1.7
 *  International Registered Trademark & Property of SmatDataSoft
 */

$validated = Configuration::get('revslider-valid');
$activewidgetclass = $validated === 'true' ? "rs-status-green-wrap" : "rs-status-red-wrap";
$code = Configuration::get('revslider-code');
// @codingStandardsIgnoreStart
?>
<!-- VALIDATION WIDGET -->
<div class="rs-dash-widget">
    <div class="rs-dash-title-wrap <?php echo $activewidgetclass; ?>">
        <div class="rs-dash-title"><?php _e("Plugin Activation", 'revslider'); ?></div>
        <div class="rs-dash-title-button rs-status-red"><i class="icon-not-registered"></i><?php _e("Not Activated", 'revslider'); ?></div>
        <div class="rs-dash-title-button rs-status-green"><i class="icon-no-problem-found"></i><?php _e("Plugin Activated", 'revslider'); ?></div>
    </div>

    <div class="rs-dash-widget-inner rs-dash-widget-deregistered" <?php echo ($validated !== 'true') ? '' : 'style="display: none;"'; ?>>
        <div class="rs-dash-icon rs-dash-refresh"></div>
        <div class="rs-dash-content-with-icon">
            <div class="rs-dash-strong-content"><?php _e("Live Updates", 'revslider'); ?></div>
            <div><?php _e("Fresh versions directly to your admin", 'revslider'); ?></div>
        </div>
        <div class="rs-dash-content-space"></div>
        <div class="rs-dash-icon rs-dash-ticket"></div>
        <div class="rs-dash-content-with-icon">
            <div class="rs-dash-strong-content"><?php _e("Ticket Support", 'revslider'); ?></div>
            <div><?php _e("Direct help from our qualified support team", 'revslider'); ?></div>
        </div>
        <div class="rs-dash-content-space"></div>				
        <div class="rs-dash-icon rs-dash-gift"></div>
        <div class="rs-dash-content-with-icon">
            <div class="rs-dash-strong-content"><?php _e("Free Premium Sliders", 'revslider'); ?></div>
            <div><?php _e("Exclusive new slider exports for our direct customers", 'revslider'); ?></div>
        </div>

        <div class="rs-dash-bottom-wrapper">
            <span id="rs-validation-activate-step-a" class="rs-dash-button"><?php _e('Register Slider Revolution', 'revslider'); ?></a>
        </div>
    </div>

    <div class="rs-dash-widget-inner rs-dash-widget-registered" <?php echo ($validated === 'true') ? '' : 'style="display: none;position:absolute;top:60px;left:0px;"'; ?>>

        <div class="rs-dash-icon rs-dash-credit"></div>
        <div class="rs-dash-content-with-icon">
            <div class="rs-dash-strong-content"><?php _e("Purchase Code", 'revslider'); ?></div>
            <div><?php echo htmlspecialchars_decode(__("You can learn how to find your purchase key <a target='_blank' href='http://www.themepunch.com/faq/where-to-find-the-purchase-code/'>here</a>", 'revslider')); ?></div>
        </div>
        <div class="rs-dash-content-space"></div>
        <?php if (!RS_DEMO) { ?>				
            <input type="text" name="rs-validation-token" class="rs-dashboard-input" style="width:100%" value="<?php echo $code; ?>" <?php echo ($validated === 'true') ? ' readonly="readonly"' : ''; ?> style="width: 350px;" />
            <div class="rs-dash-content-space"></div>

            <?php if ($validated == 'true') {

                ?>
                <div><?php _e("In order to register your purchase code on another domain, deregister <br>it first by clicking the button below.", 'revslider'); ?></div>				
                <?php } else {

                ?>
                <div><?php _e("Reminder ! One registration per Website. If registered elsewhere please deactivate that registration first.", 'revslider'); ?></div>				
                <?php
            }

            ?>

            <div class="rs-dash-bottom-wrapper">
                <span style="display:none" id="rs_purchase_validation" class="loader_round"><?php _e('Please Wait...', 'revslider'); ?></span>					
                <a href="javascript:void(0);" <?php echo ($validated !== 'true') ? '' : 'style="display: none;"'; ?> id="rs-validation-activate" class="rs-dash-button"><?php _e('Register the code', 'revslider'); ?></a>				
                <a href="javascript:void(0);" <?php echo ($validated === 'true') ? '' : 'style="display: none;"'; ?> id="rs-validation-deactivate" class="rs-dash-button"><?php _e('Deregister the code', 'revslider'); ?></a>
            </div>					
<?php } ?>
    </div>		

    <script>
        $(document).ready(function () {
            $('#rs-validation-activate-step-a').click(function () {
                punchgs.TweenLite.to($('.rs-dash-widget-inner.rs-dash-widget-deregistered'), 0.5, {autoAlpha: 1, x: "-100%", ease: punchgs.Power3.easeInOut});
                punchgs.TweenLite.fromTo($('.rs-dash-widget-inner.rs-dash-widget-registered'), 0.5, {display: "block", autoAlpha: 0, left: 400}, {autoAlpha: 1, left: 0, ease: punchgs.Power3.easeInOut});
            })
        });
    </script>
</div><!-- END OF VALIDATION WIDGET -->
<?php
// @codingStandardsIgnoreEnd