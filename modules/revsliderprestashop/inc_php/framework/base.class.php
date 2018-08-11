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

class UniteBaseClassRev
{

    protected static $wpdb;
    protected static $table_prefix;
    protected static $mainFile;
    protected static $t;
    protected static $dir_plugin;
    protected static $dir_languages;
    public static $url_plugin;
    public static $static_shortcode_tags;
    protected static $url_ajax;
    public static $url_ajax_actions;
    protected static $url_ajax_showimage;
    protected static $path_settings;
    public static $path_plugin;
    protected static $path_languages;
    protected static $path_temp;
    protected static $path_views;
    protected static $path_templates;
    protected static $path_cache;
    protected static $path_base;
    protected static $is_multisite;
    protected static $debugMode = false;
    protected static $actions = array();
    protected static $admin_scripts = array();
    protected static $front_scripts = array();
    protected static $admin_styles = array();
    protected static $front_styles = array();

    public function __construct($mainFile, $t)
    {
        self::$is_multisite = UniteFunctionsWPRev::isMultisite();

        if (class_exists('RevsliderPrestashop')) {
            self::$wpdb = RevsliderPrestashop::$wpdb;
        } else {
            self::$wpdb = rev_db_class::revDbInstance();
        }

        self::$table_prefix = self::$wpdb->prefix;

        if (UniteFunctionsWPRev::isMultisite()) {
            $blogID = UniteFunctionsWPRev::getBlogID();

            if ($blogID != 1) {
                self::$table_prefix .= $blogID . "_";
            }
        }



        self::$mainFile = $mainFile;

        self::$t = $t;


        //set plugin dirname (as the main filename)

        $info = pathinfo($mainFile);

        $baseName = $info["basename"];

        $filename = str_replace(".php", "", $baseName);



        self::$dir_plugin = $filename;



        self::$url_plugin = plugins_url(self::$dir_plugin) . "/";

        $context = Context::getContext();

        self::$url_ajax = @RevsliderPrestashop::getIsset($context->controller->admin_webpath) ? admin_url() : null;

        self::$url_ajax_actions = self::$url_ajax . "&action=" . self::$dir_plugin . "_ajax_action";

        self::$url_ajax_showimage = self::$url_plugin . "ajax.php?action=" . self::$dir_plugin . "_show_image";

        self::$path_plugin = self::$mainFile . "/";

        self::$path_settings = self::$path_plugin . "settings/";

        self::$path_temp = self::$path_plugin . "temp/";



        //set cache path:

        self::setPathCache();



        self::$path_views = self::$path_plugin . "views/";

        self::$path_templates = self::$path_views . "/templates/";

        self::$path_base = ABSPATH;

        self::$path_languages = self::$path_plugin . "languages/";

        self::$dir_languages = self::$dir_plugin . "/languages/";

        GlobalsRevSlider::$isNewVersion = true;
    }

    private static function setPathCache()
    {
        self::$path_cache = self::$path_plugin . "cache/";

        if (self::$is_multisite) {
            if (!defined("BLOGUPLOADDIR") || !is_dir(BLOGUPLOADDIR)) {
                return false;
            }

            $path = BLOGUPLOADDIR . self::$dir_plugin . "-cache/";

            if (!is_dir($path)) {
                mkdir($path);
            }

            if (is_dir($path)) {
                self::$path_cache = $path;
            }
        }
    }

    public static function setDebugMode()
    {
        self::$debugMode = true;
    }

    public static function psEnqueueStyle($scriptName, $src = '', $deps = array(), $ver = '1.0', $media = 'all', $noscript = false)
    {
        $cadm = count(self::$admin_styles) ? count(self::$admin_styles) : 0;

        $cfrt = count(self::$front_styles) ? count(self::$front_styles) : 0;



        if (is_array($scriptName)) {
            $deps = $scriptName;
        }


        if (is_admin()) {
            self::$admin_styles[$cadm] = new stdClass();


            if (is_string($scriptName)) {
                self::$admin_styles[$cadm]->css = "<link rel='stylesheet' id='{$scriptName}' media='{$media}' href='{$src}' type='text/css' />";
            }

            if ($noscript) {
                self::$admin_styles[$cadm]->css = "<noscript>" . self::$admin_styles[$cadm]->css . "</noscript>";
            }
        } else {
            self::$front_styles[$cfrt] = new stdClass();

            if (is_string($scriptName)) {
                self::$front_styles[$cfrt]->css = "<link rel='stylesheet' id='{$scriptName}' media='{$media}' href='{$src}' type='text/css' />";
            }
        }
    }

    public static function psEnqueueScript($scriptName, $src = '', $deps = array(), $ver = '1.0', $in_footer = false)
    {


        $cadm = count(self::$admin_scripts) ? count(self::$admin_scripts) : 0;

        $cfrt = count(self::$front_scripts) ? count(self::$front_scripts) : 0;

        if (is_array($scriptName)) {
            $deps = $scriptName;
        }

        if (is_admin()) {
            self::$admin_scripts[$cadm] = new stdClass();

            self::$admin_scripts[$cadm]->deps = load_additional_scripts($deps, self::$admin_scripts);

            self::$admin_scripts[$cadm]->footer = $in_footer;



            if (is_string($scriptName) && !empty($src)) {
                self::$admin_scripts[$cadm]->script = "<script id='{$scriptName}' src='{$src}' type='text/javascript'></script>";
            } else {
                $scriptArr = is_array($scriptName) ? $scriptName : array($scriptName);

                $getScripts = load_additional_scripts($scriptArr, self::$admin_scripts);

                if (!empty($getScripts)) {
                    foreach ($getScripts as $id => $src):

                        self::$admin_scripts[$cadm]->script = "<script id='{$id}' src='" . script_url() . $src . "' type='text/javascript'></script>";

                        self::$admin_scripts[$cadm]->footer = $in_footer;

                        $cadm++;

                    endforeach;
                }
            }
        } else {
            self::$front_scripts[$cfrt] = new stdClass();

            self::$front_scripts[$cadm]->deps = load_additional_scripts($deps, self::$front_scripts);

            self::$front_scripts[$cfrt]->footer = $in_footer;

            if (is_string($scriptName) && !empty($src)) {
                self::$front_scripts[$cfrt]->script = "<script id='{$scriptName}' src='{$src}' type='text/javascript'></script>";
            } else {
                $scriptArr = is_array($scriptName) ? $scriptName : array($scriptName);

                $getScripts = load_additional_scripts($scriptArr, self::$front_scripts);

                if (!empty($getScripts)) {
                    foreach ($getScripts as $id => $src):

                        self::$front_scripts[$cadm]->script = "<script id='{$id}' src='" . script_url() . $src . "' type='text/javascript'></script>";

                        self::$front_scripts[$cadm]->footer = $in_footer;

                        $cadm++;

                    endforeach;
                }
            }
        }
    }

    public static function enqueueCss($script)
    {
        echo "\t\n";

        if (@RevsliderPrestashop::getIsset($script->css)) {
            echo $script->css;
        }
    }

    public static function enqueueScript($script)
    {
        if (!empty($script->deps)) {
            foreach ($script->deps as $key => $src):

                echo "<script id='{$key}' type='text/javascript' src='" . script_url() . $src . "'></script>";

            endforeach;
        }

        echo "\t\n";

        if (@RevsliderPrestashop::getIsset($script->script)) {
            echo $script->script;
        }
    }

    public static function revHead()
    {
        if (is_admin() && !empty(self::$admin_styles)) {
            foreach (self::$admin_styles as $script):

                self::enqueueCss($script);

            endforeach;
        } elseif (!is_admin() && !empty(self::$front_styles)) {
            foreach (self::$front_styles as $script):

                self::enqueueCss($script);

            endforeach;
        }

        echo "\t\n";


        if (is_admin() && !empty(self::$admin_scripts)) {
            foreach (self::$admin_scripts as $script):

                if ($script->footer) {
                    continue;
                }

                self::enqueueScript($script);

            endforeach;
        } elseif (!is_admin() && !empty(self::$front_scripts)) {
            foreach (self::$front_scripts as $script):

                if ($script->footer) {
                    continue;
                }

                self::enqueueScript($script);

            endforeach;
        }

        echo "\t\n";
    }

    public static function revFooter()
    {
        if (is_admin() && !empty(self::$admin_scripts)) {
            foreach (self::$admin_scripts as $script):

                if (!$script->footer) {
                    continue;
                }

                self::enqueueScript($script);

            endforeach;
        } elseif (!is_admin() && !empty(self::$front_scripts)) {
            foreach (self::$front_scripts as $script):

                if (!$script->footer) {
                    continue;
                }

                self::enqueueScript($script);

            endforeach;
        }
    }

    protected static function addAction($action, $eventFunction)
    {
        if (!@RevsliderPrestashop::getIsset(self::$actions[$action])) {
            self::$actions[$action] = array();
            self::$actions[$action][0] = $eventFunction;
        } else {
            self::$actions[$action][count(self::$actions[$action])] = $eventFunction;
        }
    }

    protected static function addScriptAbsoluteUrl($scriptPath, $handle)
    {
        wp_enqueue_script($handle, $scriptPath, array('jquery'), '', false);
    }

    protected static function addScriptAbsoluteUrlWaitForOther($scriptPath, $handle, $waitfor = array())
    {
        wp_enqueue_script($handle, $scriptPath, $waitfor);
    }

    protected static function addScript($scriptName, $folder = "js", $handle = null)
    {
        if ($handle == null) {
            $handle = self::$dir_plugin . "-" . $scriptName;
        }
        $scriptPath = self::$url_plugin . $folder . "/" . $scriptName . ".js";


        wp_enqueue_script($handle, $scriptPath, array(), '', false);
    }

    protected static function addScriptWaitFor($scriptName, $folder = "js", $handle = null, $waitfor = array())
    {
        if ($handle == null) {
            $handle = self::$dir_plugin . "-" . $scriptName;
        }


        wp_enqueue_script($handle, self::$url_plugin . $folder . "/" . $scriptName . ".js?rev=" . GlobalsRevSlider::SLIDER_REVISION, $waitfor);
    }

    protected static function addScriptCommon($scriptName, $handle = null, $folder = "js")
    {
        if ($handle == null) {
            $handle = $scriptName;
        }


        self::addScript($scriptName, $folder, $handle);
    }

    protected static function addWPScript($scriptName)
    {
        wp_enqueue_script($scriptName);
    }

    protected static function addStyle($styleName, $handle = null, $folder = "css")
    {
        if ($handle == null) {
            $handle = self::$dir_plugin . "-" . $styleName;
        }
    }

    protected static function addDynamicStyle($styleName, $handle = null, $folder = "css")
    {
        if ($handle == null) {
            $handle = self::$dir_plugin . "-" . $styleName;
        }
    }

    protected static function addStyleCommon($styleName, $handle = null, $folder = "css")
    {
        if ($handle == null) {
            $handle = $styleName;
        }
    }

    protected static function addStyleAbsoluteUrl($styleUrl, $handle)
    {
        
    }

    protected static function addWPStyle($styleName)
    {
        
    }

    public static function getImageUrl($filepath, $width = null, $height = null, $exact = false, $effect = null, $effect_param = null)
    {
        $urlImage = UniteImageViewRev::getUrlThumb(self::$url_ajax_showimage, $filepath, $width, $height, $exact, $effect, $effect_param);
        return($urlImage);
    }

    public static function onShowImage()
    {
        $img = Tools::getValue('img');

        if (empty($img)) {
            die('Image doesn\'t exists!');
        }

        $pathImages = UniteFunctionsWPRev::getPathContent();

        $urlImages = UniteFunctionsWPRev::getUrlContent();


        try {
            $imageView = new UniteImageViewRev(self::$path_cache, $pathImages, $urlImages);

            $imageView->showImageFromGet();
        } catch (Exception $e) {
            header("status: 500");

            echo $e->getMessage();

            exit();
        }
    }

    protected static function getPostVar($key, $defaultValue = "")
    {
        $val = Tools::getValue($key, $defaultValue);
        return($val);
    }

    public static function getGetVar($key, $defaultValue = "")
    {
        $val = Tools::getValue($key, $defaultValue);
        return($val);
    }

    protected static function getPostGetVar($key, $defaultValue = "")
    {
        $val = Tools::getValue($key, $defaultValue);
        return($val);
    }

    public static function getVar($arr, $key, $defaultValue = "")
    {
        $val = $defaultValue;

        if (@RevsliderPrestashop::getIsset($arr[$key])) {
            $val = $arr[$key];
        }

        return($val);
    }

    /**
     * Get all images sizes + custom added sizes
     */
    public static function getAllImageSizes($type = 'gallery')
    {
        $custom_sizes = array();

        switch ($type) {
            case 'flickr':
                $custom_sizes = array(
                    'original' => __('Original', 'revslider'),
                    'large' => __('Large', 'revslider'),
                    'large-square' => __('Large Square', 'revslider'),
                    'medium' => __('Medium', 'revslider'),
                    'medium-800' => __('Medium 800', 'revslider'),
                    'medium-640' => __('Medium 640', 'revslider'),
                    'small' => __('Small', 'revslider'),
                    'small-320' => __('Small 320', 'revslider'),
                    'thumbnail' => __('Thumbnail', 'revslider'),
                    'square' => __('Square', 'revslider')
                );
                break;
            case 'instagram':
                $custom_sizes = array(
                    'standard_resolution' => __('Standard Resolution', 'revslider'),
                    'thumbnail' => __('Thumbnail', 'revslider'),
                    'low_resolution' => __('Low Resolution', 'revslider')
                );
                break;
            case 'twitter':
                $custom_sizes = array(
                    'large' => __('Standard Resolution', 'revslider')
                );
                break;
            case 'facebook':
                $custom_sizes = array(
                    'full' => __('Original Size', 'revslider'),
                    'thumbnail' => __('Thumbnail', 'revslider')
                );
                break;
            case 'youtube':
                $custom_sizes = array(
                    'default' => __('Default', 'revslider'),
                    'medium' => __('Medium', 'revslider'),
                    'high' => __('High', 'revslider'),
                    'standard' => __('Standard', 'revslider'),
                    'maxres' => __('Max. Res.', 'revslider')
                );
                break;
            case 'vimeo':
                $custom_sizes = array(
                    'thumbnail_small' => __('Small', 'revslider'),
                    'thumbnail_medium' => __('Medium', 'revslider'),
                    'thumbnail_large' => __('Large', 'revslider'),
                );
                break;
            case 'gallery':
            default:
                $img_orig_sources = array(
                    'full' => __('Original Size', 'revslider'),
                    'thumbnail' => __('Thumbnail', 'revslider'),
                    'medium' => __('Medium', 'revslider'),
                    'large' => __('Large', 'revslider')
                );
                $custom_sizes = $img_orig_sources;
                break;
        }

        return $custom_sizes;
    }

    /**
     * retrieve the image id from the given image url
     */
    public static function getImageIdByUrl($image_url)
    {
        return get_image_id_by_url($image_url);
    }

    protected static function updateSettingsText()
    {
        $filelist = UniteFunctionsRev::getFileList(self::$path_settings, "xml");

        foreach ($filelist as $file) {
            $filepath = self::$path_settings . $file;

            UniteFunctionsWPRev::writeSettingLanguageFile($filepath);
        }
    }

    /**
     * translates removed settings from Slider Settings from version <= 4.x to 5.0
     * @since: 5.0
     * */
    public static function translateSettingsToV5($settings)
    {
        if (@RevsliderPrestashop::getIsset($settings['navigaion_type'])) {
            switch ($settings['navigaion_type']) {
                case 'none': // all is off, so leave the defaults
                    break;
                case 'bullet':
                    $settings['enable_bullets'] = 'on';
                    $settings['enable_thumbnails'] = 'off';
                    $settings['enable_tabs'] = 'off';

                    break;
                case 'thumb':
                    $settings['enable_bullets'] = 'off';
                    $settings['enable_thumbnails'] = 'on';
                    $settings['enable_tabs'] = 'off';
                    break;
            }
            unset($settings['navigaion_type']);
        }

        if (@RevsliderPrestashop::getIsset($settings['navigation_arrows'])) {
            $settings['enable_arrows'] = ($settings['navigation_arrows'] == 'solo' || $settings['navigation_arrows'] == 'nexttobullets') ? 'on' : 'off';
            unset($settings['navigation_arrows']);
        }

        if (@RevsliderPrestashop::getIsset($settings['navigation_style'])) {
            $settings['navigation_arrow_style'] = $settings['navigation_style'];
            $settings['navigation_bullets_style'] = $settings['navigation_style'];
            unset($settings['navigation_style']);
        }

        if (@RevsliderPrestashop::getIsset($settings['navigaion_always_on'])) {
            $settings['arrows_always_on'] = $settings['navigaion_always_on'];
            $settings['bullets_always_on'] = $settings['navigaion_always_on'];
            $settings['thumbs_always_on'] = $settings['navigaion_always_on'];
            unset($settings['navigaion_always_on']);
        }

        if (@RevsliderPrestashop::getIsset($settings['hide_thumbs']) && !@RevsliderPrestashop::getIsset($settings['hide_arrows']) && !@RevsliderPrestashop::getIsset($settings['hide_bullets'])) { //as hide_thumbs is still existing, we need to check if the other two were already set and only translate this if they are not set yet
            $settings['hide_arrows'] = $settings['hide_thumbs'];
            $settings['hide_bullets'] = $settings['hide_thumbs'];
        }

        if (@RevsliderPrestashop::getIsset($settings['navigaion_align_vert'])) {
            $settings['bullets_align_vert'] = $settings['navigaion_align_vert'];
            $settings['thumbnails_align_vert'] = $settings['navigaion_align_vert'];
            unset($settings['navigaion_align_vert']);
        }

        if (@RevsliderPrestashop::getIsset($settings['navigaion_align_hor'])) {
            $settings['bullets_align_hor'] = $settings['navigaion_align_hor'];
            $settings['thumbnails_align_hor'] = $settings['navigaion_align_hor'];
            unset($settings['navigaion_align_hor']);
        }

        if (@RevsliderPrestashop::getIsset($settings['navigaion_offset_hor'])) {
            $settings['bullets_offset_hor'] = $settings['navigaion_offset_hor'];
            $settings['thumbnails_offset_hor'] = $settings['navigaion_offset_hor'];
            unset($settings['navigaion_offset_hor']);
        }

        if (@RevsliderPrestashop::getIsset($settings['navigaion_offset_hor'])) {
            $settings['bullets_offset_hor'] = $settings['navigaion_offset_hor'];
            $settings['thumbnails_offset_hor'] = $settings['navigaion_offset_hor'];
            unset($settings['navigaion_offset_hor']);
        }

        if (@RevsliderPrestashop::getIsset($settings['navigaion_offset_vert'])) {
            $settings['bullets_offset_vert'] = $settings['navigaion_offset_vert'];
            $settings['thumbnails_offset_vert'] = $settings['navigaion_offset_vert'];
            unset($settings['navigaion_offset_vert']);
        }

        if (@RevsliderPrestashop::getIsset($settings['show_timerbar']) && !@RevsliderPrestashop::getIsset($settings['enable_progressbar'])) {
            if ($settings['show_timerbar'] == 'hide') {
                $settings['enable_progressbar'] = 'off';
                $settings['show_timerbar'] = 'top';
            } else {
                $settings['enable_progressbar'] = 'on';
            }
        }

        return $settings;
    }

    /**
     * explodes google fonts and returns the number of font weights of all fonts
     * @since: 5.0
     * */
    public static function getFontWeightCount($string)
    {
        $string = explode(':', $string);

        $nums = 0;

        if (count($string) >= 2) {
            $string = $string[1];
            if (strpos($string, '&') !== false) {
                $string = explode('&', $string);
                $string = $string[0];
            }

            $nums = count(explode(',', $string));
        }

        return $nums;
    }

    /**
     * strip slashes recursive
     * @since: 5.0
     */
    public static function stripslashesDeep($value)
    {
        if (is_array($value)) {
            $value = array_map(array(__CLASS__, 'stripslashesDeep'), $value);
        } elseif (is_object($value)) {
            $vars = get_object_vars($value);
            foreach ($vars as $key => $data) {
                $value->{$key} = self::stripslashesDeep($data);
            }
        } elseif (is_string($value)) {
            $value = Tools::stripslashes($value);
        }

        return $value;
    }

    /**
     * get all the icon sets used in Slider Revolution
     * @since: 5.0
     * */
    public static function getIconSets()
    {
        $icon_sets = array();
        $icon_sets = self::setIconSets($icon_sets);

        return $icon_sets;
    }

    /**
     * add default icon sets of Slider Revolution
     * @since: 5.0
     * */
    public static function setIconSets($icon_sets)
    {
        $icon_sets[] = 'fa-icon-';
        $icon_sets[] = 'pe-7s-';

        return $icon_sets;
    }

    /**
     * add "a" tags to links within a text
     * @since: 5.0
     */
    public static function addWrapAroundUrl($text)
    {
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        // Check if there is a url in the text
        if (preg_match($reg_exUrl, $text, $url)) {
            // make the urls hyper links
            return preg_replace($reg_exUrl, '<a href="' . $url[0] . '" rel="nofollow" target="_blank">' . $url[0] . '</a>', $text);
        } else {
            // if no urls in the text just return the text
            return $text;
        }
    }
}

// @codingStandardsIgnoreStart
class RevSliderBase extends UniteBaseClassRev
{
    // @codingStandardsIgnoreEnd
}
