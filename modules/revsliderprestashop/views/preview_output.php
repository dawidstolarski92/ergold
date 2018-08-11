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
$isWpmlExists = UniteWpmlRev::isWpmlExists();
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
$urlPlugin = RevSliderAdmin::$url_plugin . 'views/';
$urlCSS = "{$urlPlugin}css/rs-plugin/";
$urlJS = "{$urlPlugin}js/rs-plugin/";

$urlPreviewPattern = UniteBaseClassRev::$url_ajax_actions . "&client_action=preview_slider&sliderid=" . $sliderID . "&lang=[lang]&nonce=[nonce]";
//$nonce = wp_create_nonce("revslider_actions");

$setBase = (is_ssl()) ? "https://" : "http://";

$f = new ThemePunchFonts();
$my_fonts = $f->getAllFonts();

?>
<html>
    <head>

        <link rel='stylesheet' href='<?php echo $urlCSS ?>css/settings.css?rev=<?php echo GlobalsRevSlider::SLIDER_REVISION;

?>' type='text/css' media='all' />
              <?php
              $db = new UniteDBRev();

              $styles = $db->fetch(GlobalsRevSlider::$table_css);
              $styles = UniteCssParserRev::parseDbArrayToCss($styles, "\n");
              $styles = UniteCssParserRev::compressCss($styles);

              echo '<style type="text/css">' . $styles . '</style>'; //.$stylesinnerlayers
//					$http = (is_ssl()) ? 'https' : 'http';

              if (!empty($my_fonts)) {
                  foreach ($my_fonts as $c_font) {

                      ?>
                <link rel='stylesheet' href="<?php echo '//fonts.googleapis.com/css?family=' . strip_tags($c_font['url']);

                      ?>" type='text/css' />
                      <?php
                  }
              }

              $custom_css = RevOperations::getStaticCss();
              echo '<style type="text/css">' . UniteCssParserRev::compressCss($custom_css) . '</style>';

              ?>

        <!--<script type='text/javascript' src='<?php echo $setBase ?>ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'></script>-->
        <script type='text/javascript' src='//code.jquery.com/jquery-latest.min.js'></script>

        <script type='text/javascript' src='<?php echo $urlJS ?>js/jquery.themepunch.tools.min.js?rev=<?php echo GlobalsRevSlider::SLIDER_REVISION;

              ?>'></script>
        <script type='text/javascript' src='<?php echo $urlJS ?>js/jquery.themepunch.revolution.min.js?rev=<?php echo GlobalsRevSlider::SLIDER_REVISION;

              ?>'></script>
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
            </script>
        <?php 
        endif; 
        $output->putSliderBase($sliderID);
        ?>

    </body>
</html>
<?php
exit();
// @codingStandardsIgnoreEnd