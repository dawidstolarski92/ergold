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
$sliderID = RevGlobalObject::getVar('sliderID');
$output = RevGlobalObject::getVar('output');

$slider = new RevSlider();
$slider->initByID($sliderID);
$isWpmlExists = true;
$useWpml = $slider->getParam("use_wpml", "off");
$wpmlActive = false;
if ($isWpmlExists && $useWpml == "on") {
    $wpmlActive = true;
    $arrLanguages = UniteWpmlRev::getArrLanguages(false);

    //set current lang to output
    $currentLang = UniteFunctionsRev::getPostGetVariable("lang");

    if (empty($currentLang)) {
        $currentLang = UniteWpmlRev::getCurrentLang();
    }

    if (empty($currentLang)) {
        $currentLang = $arrLanguages[0];
    }

    $output->setLang($currentLang);

    $selectLangChoose = UniteFunctionsRev::getHTMLSelect($arrLanguages, $currentLang, "id='select_langs'", true);
}


$output->setPreviewMode();

//put the output html
//$urlPlugin = "http://yourpluginpath/";
$urlPlugin = RevSliderAdmin::$url_plugin . 'views/';
$urlCSS = "{$urlPlugin}css/rs-plugin/";
$urlJS = "{$urlPlugin}js/rs-plugin/";
$urlPreviewPattern = UniteBaseClassRev::$url_ajax_actions . "&client_action=preview_slider&only_markup=true&sliderid=" . $sliderID . "&lang=[lang]&nonce=[nonce]";
//$nonce = wp_create_nonce("revslider_actions");

$setBase = (is_ssl()) ? "https://" : "http://";

$f = new ThemePunchFonts();
$my_fonts = $f->getAllFonts();

?>
<html>
    <head>
        <script type='text/javascript' src='<?php echo $setBase;

?>ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'></script>
    </head>
    <body style="padding:0px;margin:0px;">
        <?php if ($wpmlActive == true): ?>
            <div style="margin-bottom:10px;text-align:center;">
                <?php _e("Choose language", REVSLIDER_TEXTDOMAIN) ?>: <?php echo $selectLangChoose ?>
            </div>

            <script type="text/javascript">
                var g_previewPattern = '<?php echo $urlPreviewPattern ?>';
                jQuery("#select_langs").change(function() {
                    var lang = this.value;
                    var nonce = "";
                    var pattern = g_previewPattern;
                    var urlPreview = pattern.replace("[lang]", lang).replace("[nonce]", nonce);
                    location.href = urlPreview;
                });

                jQuery('body').on('click', '#rev_replace_images', function() {
                    var from = jQuery('input[name="orig_image_path"]').val();
                    var to = jQuery('input[name="replace_image_path"]').val();

                    jQuery('#rev_script_content').val(jQuery('#rev_script_content').val().replace(from, to));
                    jQuery('#rev_the_content').val(jQuery('#rev_the_content').val().replace(from, to));
                    jQuery('#rev_style_content').val(jQuery('#rev_style_content').val().replace(from, to));
                    jQuery('#rev_head_content').val(jQuery('#rev_head_content').val().replace(from, to));
                });

            </script>
        <?php endif ?>
        <?php
        //UniteBaseClassRev::$url_plugin

        ob_start();

        ?><link rel='stylesheet' href='<?php echo $urlCSS ?>css/settings.css?rev=<?php echo GlobalsRevSlider::SLIDER_REVISION;

        ?>' type='text/css' media='all' />
              <?php
              $http = (is_ssl()) ? 'https' : 'http';

              if (!empty($my_fonts)) {
                  foreach ($my_fonts as $c_font) {

                      ?><link rel='stylesheet' href="<?php echo $http . '://fonts.googleapis.com/css?family=' . strip_tags($c_font['url']);

                      ?>" type='text/css' /><?php
                      echo "\n";
                  }
              }

              ?>
        <script type='text/javascript' src='<?php echo $urlJS ?>js/jquery.themepunch.tools.min.js?rev=<?php echo GlobalsRevSlider::SLIDER_REVISION;

              ?>'></script>
        <script type='text/javascript' src='<?php echo $urlJS ?>js/jquery.themepunch.revolution.min.js?rev=<?php echo GlobalsRevSlider::SLIDER_REVISION;

              ?>'></script>
                <?php
                $head_content = ob_get_clean();
//					ob_end_clean();

                ob_start();

                $custom_css = RevOperations::getStaticCss();
                echo $custom_css . "\n\n";

                echo '/*****************' . "\n";
                echo ' ** ' . __('CAPTIONS CSS', REVSLIDER_TEXTDOMAIN) . "\n";
                echo ' ****************/' . "\n\n";
                $db = new UniteDBRev();
                $styles = $db->fetch(GlobalsRevSlider::$table_css);
                echo UniteCssParserRev::parseDbArrayToCss($styles, "\n");

                $style_content = ob_get_clean();

//					ob_end_clean();

                ob_start();

                $output->putSliderBase($sliderID);

                $content = ob_get_clean();

//					ob_end_clean();

                $script_content = Tools::substr($content, strpos($content, '<script type="text/javascript">'), strpos($content, '</script>') + 9 - strpos($content, '<script type="text/javascript">'));
                $content = htmlentities(str_replace($script_content, '', $content));
                $script_content = str_replace('				', '', $script_content);
                $script_content = str_replace(array('<script type="text/javascript">', '</script>'), '', $script_content);

                ?>
        <style>
            body 	 { font-family:sans-serif; font-size:12px;}
            textarea { background:#f1f1f1; border:#ddd; font-size:10px; line-height:16px; margin-bottom:40px; padding:10px;}
            .rev_cont_title { color:#000; text-decoration:none;font-size:14px; line-height:24px; font-weight:800;background: #D5D5D5;padding: 10px;}
            .rev_cont_title a,
            .rev_cont_title a:visited { margin-left:25px;font-size:12px;line-height:12px;float:right;background-color:#8e44ad; color:#fff; padding:8px 10px;text-decoration:none;}
            .rev_cont_title a:hover	  { background-color:#9b59b6}
        </style>
        <p><?php $dir = uploads_url();

                ?>
            <?php _e('Replace image path:', REVSLIDER_TEXTDOMAIN);

            ?> <?php _e('From:', REVSLIDER_TEXTDOMAIN);

            ?> <input type="text" name="orig_image_path" value="<?php echo $dir;

            ?>" /> <?php _e('To:', REVSLIDER_TEXTDOMAIN);

            ?> <input type="text" name="replace_image_path" value="" /> <input id="rev_replace_images" type="button" name="replace_images" value="<?php _e('Replace', REVSLIDER_TEXTDOMAIN);

            ?>" />
        </p>

        <div class="rev_cont_title"><?php _e('Header', REVSLIDER_TEXTDOMAIN);

            ?> <a class="button-primary revpurple export_slider_standalone copytoclip" data-idt="rev_head_content"  href="javascript:void(0);" original-title=""><?php _e('Mark to Copy', REVSLIDER_TEXTDOMAIN);

            ?></a><div style="clear:both"></div></div>
        <textarea id="rev_head_content" readonly="true" style="width: 100%; height: 100px; color:#3498db"><?php echo $head_content;

            ?></textarea>
        <div class="rev_cont_title"><?php _e('CSS', REVSLIDER_TEXTDOMAIN);

            ?><a class="button-primary revpurple export_slider_standalone copytoclip" data-idt="rev_style_content"  href="javascript:void(0);" original-title=""><?php _e('Mark to Copy', REVSLIDER_TEXTDOMAIN);

            ?></a></div>
        <textarea id="rev_style_content" readonly="true" style="width: 100%; height: 100px;"><?php echo $style_content;

            ?></textarea>
        <div class="rev_cont_title"><?php _e('Body', REVSLIDER_TEXTDOMAIN);

            ?><a class="button-primary revpurple export_slider_standalone copytoclip" data-idt="rev_the_content"  href="javascript:void(0);" original-title=""><?php _e('Mark to Copy', REVSLIDER_TEXTDOMAIN);

            ?></a></div>
        <textarea id="rev_the_content" readonly="true" style="width: 100%; height: 100px;"><?php echo $content;

            ?></textarea>
        <div class="rev_cont_title"><?php _e('Script', REVSLIDER_TEXTDOMAIN);

            ?><a class="button-primary revpurple export_slider_standalone copytoclip" data-idt="rev_script_content"  href="javascript:void(0);" original-title=""><?php _e('Mark to Copy', REVSLIDER_TEXTDOMAIN);

            ?></a></div>
        <textarea id="rev_script_content" readonly="true" style="width: 100%; height: 100px;"><?php echo $script_content;

            ?></textarea>

        <script>
            jQuery('body').on('click', '.copytoclip', function() {
                jQuery("#" + jQuery(this).data('idt')).select();
            });
        </script>
    </body>
</html>
<?php
exit();
// @codingStandardsIgnoreEnd