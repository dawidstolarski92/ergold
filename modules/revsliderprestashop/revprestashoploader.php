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

if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}
$dir = _PS_MODULE_DIR_ . 'revsliderprestashop';
if (!defined('ABSPATH')) {
    define('ABSPATH', $dir);
}
define('WP_CONTENT_DIR', $dir);

if (!defined('ARRAY_A')) {
    define('ARRAY_A', true);
}


define('OBJECT', false);

$currentFolder = $dir;

$folderIncludes = "{$currentFolder}/inc_php/framework/";



// include db class

require_once $currentFolder . '/inc_php/revslider_db.class.php'; // added by rakib on 2nd Jan, 2013
////include bases

require_once $folderIncludes . 'base.class.php';

require_once $folderIncludes . 'elements_base.class.php';

require_once $folderIncludes . 'base_admin.class.php';

//include frameword files

require_once $folderIncludes . 'include_framework.php';

// include front base

require_once $folderIncludes . 'base_front.class.php';


////include product files

require_once $currentFolder . '/inc_php/revslider_settings_product.class.php';

require_once $currentFolder . '/inc_php/revslider_globals.class.php';

require_once $currentFolder . '/inc_php/revslider_operations.class.php';

require_once $currentFolder . '/inc_php/revslider_navigation.class.php';

require_once $currentFolder . '/inc_php/revslider_slider.class.php';

require_once $currentFolder . '/inc_php/revslider_output.class.php';

require_once $currentFolder . '/inc_php/revslider_slide.class.php';

require_once $currentFolder . '/inc_php/revslider_params.class.php';


require_once $currentFolder . '/inc_php/fonts.class.php'; //punchfonts

require_once $currentFolder . '/inc_php/hooks.class.php'; //prestashop hooks

require_once $currentFolder . '/inc_php/revslider_template.class.php';

require_once $currentFolder . '/inc_php/external-sources.class.php';

function bloginfo($prop)
{
    switch ($prop) {

        case 'charset':

            echo "UTF-8";

            break;

        default:
            break;
    }
}

function wp_upload_dir()
{
    return array('basedir' => ABSPATH);
}

function rev_get_token()
{
    $token = Context::getcontext()->controller->token;
    if (@RevsliderPrestashop::getIsset($token)) {
        return $token;
    }
    return false;
}

function is_multisite()
{
    if (Shop::isFeatureActive()) {
        return true;
    } else {
        return false;
    }
}

function is_ssl()
{
    $is_secure = Tools::usingSecureMode();
    if (is_bool($is_secure)) {
        return $is_secure;
    } elseif ($is_secure == 'https') {
        return true;
    }
    return false;
}

function is_admin()
{
    $admin = @RevsliderPrestashop::getIsset(Context::getContext()->controller->admin_webpath);
    return $admin;
}

function rev_title()
{
    if (is_admin()) {
        echo "Revolution Slider";
        return;
    }

    echo "Homepage";
}

function load_additional_scripts($deps = array(), $parent = false)
{
    if (empty($deps) || !is_array($deps)) {
        return false;
    }

    $load = array();

    foreach ($deps as $dep) {

        switch ($dep) {

            case 'jquery':

                $load[$dep] = 'js/jquery-1.9.1.min.js';

                break;

            default:

                break;
        }
    }

    return $load;
}

function get_url($link = '')
{
    $url = '//' . Tools::getHttpHost() . _MODULE_DIR_ . "revsliderprestashop";

    return $url;
}

function plugin_dir_path($link = '')
{
    $url = Context::getcontext()->shop->getBaseURL() . "modules/revsliderprestashop/";

    return $url;
}

function uploads_url($src = '')
{
    return get_url() . '/uploads/' . $src;
}

function script_url()
{
    return get_url() . '/';
}

function controller_upload_url($link = '')
{
    $hash = Tools::encrypt(GlobalsRevSlider::MODULE_NAME);
    $cntrl = Context::getContext()->link->getAdminLink('AdminRevolutionsliderUpload') . '&security_key=' . $hash;
    $url = $cntrl . $link;
    return $url;
}

function admin_url($link = '')
{
    preg_match('/\?(.*)$/', $link, $found);

    $arr = array(
        'configure' => 'revsliderprestashop',
        'module_name' => 'revsliderprestashop',
        'tab_module' => 'front_office_features',
    );

    $url = Context::getContext()->link->getAdminLink('AdminModules');

    if (@RevsliderPrestashop::getIsset($found[1]) && !empty($found[1])) {

        $level1 = explode('&', $found[1]);

        foreach ($level1 as $level2) {
            $lv2 = explode('=', $level2);
            $arr[$lv2[0]] = $lv2[1];
        }
    }

    $url .= '&' . http_build_query($arr);

    return $url;
}

function plugins_url($file = '')
{
    if (!empty($file)) {
        return get_url(dirname($file));
    }

    return ABSPATH;
}

function content_url($link = '')
{
    return get_url($link);
}

function rev_media_folder()
{
    $folder = _PS_ROOT_DIR_ . '/img/cms/revolution/';
    if (!file_exists($folder)) {
        if (!mkdir($folder, 0755, true)) {
            $folder = _PS_ROOT_DIR_ . 'img/cms/';
        } else {
            $folder = _PS_ROOT_DIR_ . 'img/cms/revolution/';
        }
    }
    return $folder;
}

function rev_media_folderuri()
{
    $folder = _PS_ROOT_DIR_ . '/img/cms/revolution/';
    if (!file_exists($folder)) {
        $folder = __PS_BASE_URI__ . 'img/cms/';
    } else {
        $folder = __PS_BASE_URI__ . 'img/cms/revolution/';
    }
    return $folder;
}

function rev_media_url($link = '')
{
    // return get_url($link);
    $folder = rev_media_folderuri() . $link;
    return $folder;
}

function get_template_directory_uri()
{
    return get_url();
}

function get_image_real_size($image)
{
    $filepath = ABSPATH . '/uploads/' . $image;
    if (file_exists($filepath)) {
        return list($width, $height) = getimagesize($filepath);
    }
    return false;
}

function get_image_id_by_url($image)
{
    $wpdb = rev_db_class::revDbInstance();

    $tablename = $wpdb->prefix . GlobalsRevSlider::TABLE_ATTACHMENT_IMAGES;

    $image = basename($image);

    $id = $wpdb->getVar("SELECT ID FROM {$tablename} WHERE file_name='{$image}'");

    return $id;
}

function get_attached_file($file)
{
    $filepath = ABSPATH . "/uploads/{$file}";
    return file_exists($filepath) ? $filepath : false;
}

function wp_get_attachment_image_src($attach_id, $size = 'thumbnail', $args = array())
{
    $wpdb = rev_db_class::revDbInstance();

    $tablename = $wpdb->prefix . GlobalsRevSlider::TABLE_ATTACHMENT_IMAGES;

    $filename = $wpdb->getVar("SELECT file_name FROM {$tablename} WHERE ID={$attach_id}");



    if (!empty($filename)) {
        $filerealname = Tools::substr($filename, 0, strrpos($filename, '.'));

        $fileext = Tools::substr($filename, strrpos($filename, '.'), Tools::strlen($filename) - Tools::strlen($filerealname));

        $newfilename = $filerealname;

        if (gettype($size) == 'string') {
            switch ($size) {

                case "thumbnail":

                    $px = GlobalsRevSlider::IMAGE_SIZE_THUMBNAIL;

                    $newfilename .= "-{$px}x{$px}";

                    break;

                case "medium":

                    $px = GlobalsRevSlider::IMAGE_SIZE_MEDIUM;

                    $newfilename .= "-{$px}x{$px}";

                    break;

                case "large":

                    $px = GlobalsRevSlider::IMAGE_SIZE_LARGE;

                    $newfilename .= "-{$px}x{$px}";

                    break;

                default:
                    break;
            }

            $newfilename .= $fileext;

            $imagesize = get_image_real_size($newfilename);

            return array(uploads_url($newfilename), $imagesize[0], $imagesize[1]);
            // return array(rev_media_url($newfilename),$imagesize[0],$imagesize[1]);
        }
    }
    return false;
}

function GetLinkobj()
{
    $ret = array();
    if (Tools::usingSecureMode()) {
        $useSSL = true;
    } else {
        $useSSL = false;
    }
    $protocol_link = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 'https://' : 'http://';
    $protocol_content = (@RevsliderPrestashop::getIsset($useSSL) and $useSSL and Configuration::get('PS_SSL_ENABLED') and Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 'https://' : 'http://';
    $link = new Link($protocol_link, $protocol_content);
    $ret['protocol_link'] = $protocol_link;
    $ret['protocol_content'] = $protocol_content;
    $ret['obj'] = $link;
    return $ret;
}

function modify_image_url($img_src = '')
{
    $lnk = GetLinkobj();
    $img_pathinfo = pathinfo($img_src);
    $mainstr = $img_pathinfo['basename'];
    $static_url = __PS_BASE_URI__ . 'modules/revsliderprestashop/uploads/' . $mainstr;
    return $lnk['protocol_content'] . Tools::getMediaServer($static_url) . $static_url;
}

function modify_layer_image($img_src = '')
{
    $lnk = GetLinkobj();
    $img_pathinfo = pathinfo($img_src);
    $mainstr = $img_pathinfo['basename'];
    $static_url = __PS_BASE_URI__ . 'modules/revsliderprestashop/uploads/' . $mainstr;
    return $lnk['protocol_content'] . Tools::getMediaServer($static_url) . $static_url;
}

function wp_enqueue_script($scriptName, $src = '', $deps = array(), $ver = '1.0', $in_footer = false)
{
    UniteBaseClassRev::psEnqueueScript($scriptName, $src, $deps, $ver, $in_footer);
}

function wp_enqueue_style($handle, $src = '', $deps = array(), $ver = '', $media = 'all', $noscript = false)
{
    UniteBaseClassRev::psEnqueueStyle($handle, $src, $deps, $ver, $media, $noscript);
}

function rev_head()
{
    UniteBaseClassRev::revHead();
}

function rev_footer()
{
    UniteBaseClassRev::revFooter();
}
if (!function_exists('__')) {

    function __($text, $textdomain = '')
    {
        $mod = RevsliderPrestashop::getInstance();
        return $mod->l($text);
    }
}

if (!function_exists('_e')) {

    function _e($text, $textdomain = '')
    {
        // $mod = new RevsliderPrestashop();
        $mod = RevsliderPrestashop::getInstance();
        echo $mod->l($text);
    }
}

function esc_sql($data)
{
    $wpdb = rev_db_class::revDbInstance();
    return $wpdb->_escape($data);
}

function putRevSlider($data, $putIn = "")
{
    $operations = new RevOperations();

    $arrValues = $operations->getGeneralSettingsValues();

    $includesGlobally = UniteFunctionsRev::getVal($arrValues, "includes_globally", "on");

    $strPutIn = UniteFunctionsRev::getVal($arrValues, "pages_for_includes");

    $isPutIn = RevSliderOutput::isPutIn($strPutIn, true);

    if ($isPutIn == false && $includesGlobally == "off") {
        $output = new RevSliderOutput();

        $option1Name = "Include RevSlider libraries globally (all pages/posts)";

        $option2Name = "Pages to include RevSlider libraries";

        $output->putErrorMessage(__("If you want to use the PHP function \"putRevSlider\" in your code please make sure to check \" ", REVSLIDER_TEXTDOMAIN) . $option1Name . __(" \" in the backend's \"General Settings\" (top right panel). <br> <br> Or add the current page to the \"", REVSLIDER_TEXTDOMAIN) . $option2Name . __("\" option box."));

        return(false);
    }

// var_dump($data);

    RevSliderOutput::putSlider($data, $putIn);
}

// @codingStandardsIgnoreStart
class sdsconfig
{

    public $ocdb;

    public static function getval($key, $store_id = 0, $group = 'config')
    {
        $value = Configuration::get($key);
        if (@RevsliderPrestashop::getIsset($value)) {
            return $value;
        } else {
            return false;
        }
    }

    public static function setval($key, $value = '', $group = 'config', $store_id = 0, $serialized = 0)
    {
        $value = serialize($value);
        if (Configuration::updateValue($key, $value)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getcaptioncss($tabl)
    {
        $wpdb = rev_db_class::revDbInstance();
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $tabl;
        $value = $wpdb->getResults($sql);
        if (@RevsliderPrestashop::getIsset($value)) {
            return $value;
        } else {
            return false;
        }
    }

    public static function getgeneratecss()
    {
        $getcss = self::getcaptioncss(GlobalsRevSlider::TABLE_CSS_NAME);

        $value = UniteCssParserRev::parseDbArrayToCss($getcss, "\n");
        if (@RevsliderPrestashop::getIsset($value)) {
            return $value;
        } else {
            return false;
        }
    }

    public static function getgeneratecssfile()
    {
        $csscontent = sdsconfig::getgeneratecss();
        $cache_filename = RevSliderAdmin::$path_plugin . 'rs-plugin/css/captions.css';
        file_put_contents($cache_filename, $csscontent);
        chmod($cache_filename, 0777);
    }

    public static function getLayouts()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'meta` m INNER JOIN `' . _DB_PREFIX_ . 'meta_lang` ml ON(m.`id_meta` = ml.`id_meta` AND ml.`id_lang` = ' . (int) Context::getContext()->language->id . ' AND ml.`id_shop` = ' . (int) Context::getContext()->shop->id . ')';
        $meta = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        return $meta;
    }

    public static function getrevslide()
    {
        $result = array();
        $wpdb = rev_db_class::revDbInstance();
        $sql = "SELECT * FROM " . $wpdb->prefix . GlobalsRevSlider::TABLE_SLIDERS_NAME;
        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        //$data = $wpdb->getResults($sql);
        if (!empty($data)) {
            $i = 0;
            foreach ($data as $val) {
                $result[$i]['id'] = $val['id'];
                $result[$i]['title'] = $val['title'];
                $i = $i + 1;
            }
        }
        if (!empty($result)) {
            return $result;
        } else {
            return false;
        }
    }

    public static function get_current_store()
    {
        $store_id = (int) Context::getContext()->shop->id;
        if (!@RevsliderPrestashop::getIsset($store_id)) {
            $store_id = 1;
        }
        return $store_id;
    }

    public static function getNameById($id)
    {
        $sql = 'SELECT name
                FROM ' . _DB_PREFIX_ . 'shop_group
                WHERE id_shop_group = ' . $id;
        return Db::getInstance()->getValue($sql);
    }
}

// @codingStandardsIgnoreEnd
function wp_mkdir_p($target)
{
    $wrapper = null;

    // Strip the protocol.
    if (wp_is_stream($target)) {
        list($wrapper, $target) = explode('://', $target, 2);
    }

    // From php.net/mkdir user contributed notes.
    $target = str_replace('//', '/', $target);

    // Put the wrapper back on the target.
    if ($wrapper !== null) {
        $target = $wrapper . '://' . $target;
    }

    /*
     * Safe mode fails with a trailing slash under certain PHP versions.
     * Use rtrim() instead of untrailingslashit to avoid formatting.php dependency.
     */
    $target = rtrim($target, '/');
    if (empty($target)) {
        $target = '/';
    }

    if (file_exists($target)) {
        return @is_dir($target);
    }

    // We need to find the permissions of the parent folder that exists and inherit that.
    $target_parent = dirname($target);
    while ('.' != $target_parent && !is_dir($target_parent)) {
        $target_parent = dirname($target_parent);
    }

    // Get the permission bits.
    if ($stat = @stat($target_parent)) {
        $dir_perms = $stat['mode'] & 0007777;
    } else {
        $dir_perms = 0777;
    }

    if (@mkdir($target, $dir_perms, true)) {

        /*
         * If a umask is set that modifies $dir_perms, we'll have to re-set
         * the $dir_perms correctly with chmod()
         */
        if ($dir_perms != ($dir_perms & ~umask())) {
            $folder_parts = explode('/', Tools::substr($target, Tools::strlen($target_parent) + 1));
            for ($i = 1, $c = count($folder_parts); $i <= $c; $i++) {
                @chmod($target_parent . '/' . implode('/', array_slice($folder_parts, 0, $i)), $dir_perms);
            }
        }

        return true;
    }

    return false;
}

function wp_is_stream($path)
{
    $wrappers = stream_get_wrappers();
    $wrappers_re = '(' . join('|', $wrappers) . ')';

    return preg_match("!^$wrappers_re://!", $path) === 1;
}

function __checked_selected_helper($helper, $current, $echo, $type)
{
    if ((string) $helper === (string) $current) {
        $result = " $type='$type'";
    } else {
        $result = '';
    }

    if ($echo) {
        echo $result;
    } else {
        return $result;
    }
}

function selected($selected, $current = true, $echo = true)
{
    return __checked_selected_helper($selected, $current, $echo, 'selected');
}

function checked($checked, $current = true, $echo = true)
{
    return __checked_selected_helper($checked, $current, $echo, 'checked');
}

function size_format($bytes, $decimals = 0)
{
    $quant = array(
        // ========================= Origin ====
        'TB' => 1099511627776, // pow( 1024, 4)
        'GB' => 1073741824, // pow( 1024, 3)
        'MB' => 1048576, // pow( 1024, 2)
        'kB' => 1024, // pow( 1024, 1)
        'B' => 1, // pow( 1024, 0)
    );

    foreach ($quant as $unit => $mag) {
        if (doubleval($bytes) >= $mag) {
            return number_format_i18n($bytes / $mag, $decimals) . ' ' . $unit;
        }
    }

    return false;
}

function number_format_i18n($number, $decimals = 0)
{
    $formatted = number_format($number, absint($decimals), '.', ',');

    return $formatted;
}

function absint($maybeint)
{
    return abs((int) ($maybeint));
}

function current_time($type, $gmt = 0)
{
    switch ($type) {
        case 'mysql':
            return ($gmt) ? gmdate('Y-m-d H:i:s') : gmdate('Y-m-d H:i:s', (time() + (0 * (60 * 60))));
        case 'timestamp':
            return ($gmt) ? time() : time() + (0 * (60 * 60));
        default:
            return ($gmt) ? date($type) : date($type, time() + (0 * (60 * 60)));
    }
}

function get_transient($option_name)
{
    $main_opt_name = "_trns_{$option_name}";

    $return = false;

    $wpdb = rev_db_class::revDbInstance();



    $result = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='{$main_opt_name}'");
    $return_temp = unserialize(Tools::stripslashes($result['value']));

    if ($result && is_array($result)) {
        if ($return_temp['reset_time'] >= time()) {
            $return = $return_temp['data'];
        }
    }
    return $return;
}

function set_transient($option_name, $option_value, $reset_time = 1200)
{
    $main_opt_name = "_trns_{$option_name}";
    $wpdb = rev_db_class::revDbInstance();

    $serialized_data = array();

    $serialized_data['reset_time'] = time() + $reset_time;

    $serialized_data['data'] = $option_value;

    $serialized_data = addslashes(serialize($serialized_data));

    $is_exist = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='{$main_opt_name}'");

    $result_temp = unserialize(Tools::stripslashes($is_exist['value']));

    if (!$is_exist || $result_temp['reset_time'] < time()) {
        if ($is_exist) {
            $wpdb->query("UPDATE `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` SET `value`='" . $serialized_data . "' WHERE `name`='{$main_opt_name}';");
        } else {
            $wpdb->query("INSERT INTO `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` (`id`, `name`, `value`) VALUES (NULL, '" . $main_opt_name . "', '" . $serialized_data . "');");
        }
    }
}

function wp_remote_fopen($Url)
{
    $UserAgentList = array();
    $UserAgentList[] = "Mozilla/4.0 (compatible; MSIE 6.0; X11; Linux i686; en) Opera 8.01";
    $UserAgentList[] = "Mozilla/5.0 (compatible; Konqueror/3.3; Linux) (KHTML, like Gecko)";
    $UserAgentList[] = "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2";
    $UserAgentList[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9.2.25) Gecko/20111212 Firefox/3.6.25";
    $UserAgentList[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.52.7 (KHTML, like Gecko) Version/5.1.2 Safari/534.52.7";
    $UserAgentList[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; Win64; x64; SV1; .NET CLR 2.0.50727)";
    $UserAgentList[] = "Mozilla/5.0 (Windows NT 6.1; rv:8.0.1) Gecko/20100101 Firefox/8.0.1";
    $UserAgentList[] = "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.63 Safari/535.7";

    $hcurl = curl_init();

    curl_setopt($hcurl, CURLOPT_URL, $Url);
    curl_setopt($hcurl, CURLOPT_USERAGENT, $UserAgentList[array_rand($UserAgentList)]);
    curl_setopt($hcurl, CURLOPT_TIMEOUT, 60);
    curl_setopt($hcurl, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($hcurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($hcurl, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($hcurl);
    curl_close($hcurl);

    return $result;
}

function smart_merge_attrs($pairs, $atts)
{
    $atts = (array) $atts;
    $out = array();
    foreach ($pairs as $name => $default) {
        if (array_key_exists($name, $atts)) {
            $out[$name] = $atts[$name];
        } else {
            $out[$name] = $default;
        }
    }
    return $out;
}

function maybe_unserialize($original)
{
    if (is_serialized($original)) { // don't attempt to unserialize data that wasn't serialized going in
        return @unserialize($original);
    }
    return $original;
}

function is_serialized($data, $strict = true)
{
    // if it isn't a string, it isn't serialized.
    if (!is_string($data)) {
        return false;
    }
    $data = trim($data);
    if ('N;' == $data) {
        return true;
    }
    if (Tools::strlen($data) < 4) {
        return false;
    }
    if (':' !== $data[1]) {
        return false;
    }
    if ($strict) {
        $lastc = Tools::substr($data, -1);
        if (';' !== $lastc && '}' !== $lastc) {
            return false;
        }
    } else {
        $semicolon = strpos($data, ';');
        $brace = strpos($data, '}');
        // Either ; or } must exist.
        if (false === $semicolon && false === $brace) {
            return false;
        }
        // But neither must be in the first X characters.
        if (false !== $semicolon && $semicolon < 3) {
            return false;
        }
        if (false !== $brace && $brace < 4) {
            return false;
        }
    }
    $token = $data[0];
    switch ($token) {
        case 's':
            if ($strict) {
                if ('"' !== Tools::substr($data, -2, 1)) {
                    return false;
                }
            } elseif (false === strpos($data, '"')) {
                return false;
            }
        // or else fall through
        case 'a':
        case 'O':
            return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'b':
        case 'i':
        case 'd':
            $end = $strict ? '$' : '';
            return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
    }
    return false;
}

function sanitize_title($title)
{
    $raw_title = $title;

    $title = Tools::strtolower($title);

    $title = str_replace(' ', '-', $title);

    $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);

    return $title;
}

function wp_strip_all_tags($string, $remove_breaks = false)
{
    $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
    $string = strip_tags($string);

    if ($remove_breaks) {
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
    }

    return trim($string);
}

function wp_pre_kses_less_than($text)
{
    return preg_replace_callback('%<[^>]*?((?=<)|>|$)%', 'wp_pre_kses_less_than_callback', $text);
}

function sanitize_text_field($str)
{
    $filtered = $str;

    if (strpos($filtered, '<') !== false) {
        $filtered = wp_pre_kses_less_than($filtered);
        // This will strip extra whitespace for us.
        $filtered = wp_strip_all_tags($filtered, true);
    } else {
        $filtered = trim(preg_replace('/[\r\n\t ]+/', ' ', $filtered));
    }

    $found = false;
    while (preg_match('/%[a-f0-9]{2}/i', $filtered, $match)) {
        $filtered = str_replace($match[0], '', $filtered);
        $found = true;
    }

    if ($found) {
        // Strip out the whitespace that may now exist after removing the octets.
        $filtered = trim(preg_replace('/ +/', ' ', $filtered));
    }

    return $filtered;
}

function throwError($message, $code = null)
{
    UniteFunctionsRev::throwError($message, $code);
}

function update_option($key, $value)
{
    $wpdb = rev_db_class::revDbInstance();
    $is_exist = $wpdb->getVar("SELECT id FROM `{$wpdb->prefix}" . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='{$key}'");
    if (is_array($value)) {
        $value = serialize($value);
    }
    if (!empty($is_exist)) {
        $wpdb->query("UPDATE `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` SET `value`='{$value}' WHERE `id`={$is_exist} AND `name`='{$key}';");
    } else {
        $wpdb->query("INSERT INTO `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` (`name`, `value`) VALUES ('{$key}', '{$value}');");
    }

    return true;
}

function get_option($key, $default = false)
{
    $wpdb = rev_db_class::revDbInstance();

    $value = $wpdb->getVar("SELECT value FROM `{$wpdb->prefix}" . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='{$key}'");

    return $value !== false ? $value : $default;
}

function wp_remote_get($url, $args = array())
{
    $obj = new RevGlobalObject();
    if (is_callable(array($obj, 'getHttpCurl'))) {
        return $obj->getHttpCurl($url, $args);
    }
    return false;
}

function wp_remote_post($url, $args = array())
{
    return wp_remote_get($url, $args);
}
if (!defined('RS_PLUGIN_URL')) {
    define('RS_PLUGIN_URL', get_url() . '/');
}

/**
 * Retrieve only the response code from the raw response.
 *
 * Will return an empty array if incorrect parameter value is given.
 *
 * @since 2.7.0
 *
 * @param array $response HTTP response.
 * @return int|string The response code as an integer. Empty string on incorrect parameter given.
 */
function wp_remote_retrieve_response_code($response)
{

    if (!isset($response['info']['http_code']) || !is_array($response['info'])) {
        return '';
    }

    return $response['info']['http_code'];
}

/**
 * Retrieve only the body from the raw response.
 *
 * @since 2.7.0
 *
 * @param array $response HTTP response.
 * @return string The body of the response. Empty string if no body or incorrect parameter given.
 */
function wp_remote_retrieve_body($response)
{
    if (!isset($response['body'])) {
        return '';
    }

    return $response['body'];
}

/**
 * safe output for html attributes
 * @return string
 */
function esc_attr($string)
{
    return Tools::safeOutput($string);
}

// @codingStandardsIgnoreStart
class RevGlobalObject
{

    public static $objOperation;
    public static $dynamicObj = array();
    public $headers;
    public $body;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->headers = '';
        $this->body = '';
    }

    /**
     * 
     * @param type $url
     * @param type $args
     * @return mixed data
     */
    public function getHttpCurl($url, $args)
    {
        if (function_exists('curl_init')) {
            $defaults = array(
                'method' => 'GET',
                'timeout' => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(
                    'Authorization' => 'Basic ',
                    'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                    'Accept-Encoding' => 'gzip'
                ),
                'body' => array(),
                'cookies' => array(),
                'user-agent' => 'Prestashop/' . _PS_VERSION_,
                'header' => false,
                'sslverify' => true,
            );

            $args = smart_merge_attrs($defaults, $args);

            $curl_timeout = ceil($args['timeout']);
            $curl = curl_init();

            if ($args['httpversion'] == '1.0') {
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            } else {
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            }
            curl_setopt($curl, CURLOPT_USERAGENT, $args['user-agent']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);

            $ssl_verify = $args['sslverify'];
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, ( $ssl_verify === true ) ? 2 : false );
            if ($ssl_verify) {
                curl_setopt($curl, CURLOPT_CAINFO, ABSPATH . '/views/ssl/ca-bundle.crt');
            }

            curl_setopt($curl, CURLOPT_HEADER, $args['header']);
            /*
             * The option doesn't work with safe mode or when open_basedir is set, and there's
             * a bug #17490 with redirected POST requests, so handle redirections outside Curl.
             */
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
            if (defined('CURLOPT_PROTOCOLS')) { // PHP 5.2.10 / cURL 7.19.4
                curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            }


            $http_headers = array();
            foreach ($args['headers'] as $key => $value) {
                $http_headers[] = "{$key}: {$value}";
            }
            if (is_array($args['body']) || is_object($args['body'])) {
                $args['body'] = http_build_query($args['body']);
            }
            $http_headers[] = 'Content-Length: ' . Tools::strlen($args['body']);

            curl_setopt($curl, CURLOPT_HTTPHEADER, $http_headers);

            switch ($args['method']) {
                case 'HEAD':
                    curl_setopt($curl, CURLOPT_NOBODY, true);
                    break;
                case 'POST':
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $args['body']);
                    break;
                case 'PUT':
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $args['body']);
                    break;
                default:
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $args['method']);
                    if (!is_null($args['body'])) {
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $args['body']);
                    }
                    break;
            }
            curl_setopt($curl, CURLOPT_HEADERFUNCTION, array($this, 'streamHeaders'));
            curl_setopt($curl, CURLOPT_WRITEFUNCTION, array($this, 'streamBody'));
            curl_exec($curl);

            $responseBody = $this->body;
            $responseHeader = $this->headers;

            if (self::shouldDecode($responseHeader) === true) {
                $responseBody = self::decompress($responseBody);
            }
            $this->body = '';
            $this->headers = '';

            $error = curl_error($curl);
            $errorcode = curl_errno($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            $response = array('body' => $responseBody, 'headers' => $responseHeader, 'info' => $info, 'error' => $error, 'errno' => $errorcode);

            return $response;
        }
        return false;
    }

    private function streamHeaders($handle, $headers)
    {
        $this->headers .= $headers;
        return Tools::strlen($headers);
    }

    private function streamBody($handle, $data)
    {
        $data_length = strlen($data);
        $this->body .= $data;
        // Upon event of this function returning less than strlen( $data ) curl will error with CURLE_WRITE_ERROR.
        return $data_length;
    }

    /**
     * Decompression of deflated string.
     *
     * Will attempt to decompress using the RFC 1950 standard, and if that fails
     * then the RFC 1951 standard deflate will be attempted. Finally, the RFC
     * 1952 standard gzip decode will be attempted. If all fail, then the
     * original compressed string will be returned.
     *
     * @since 2.8.0
     *
     * @static
     *
     * @param string $compressed String to decompress.
     * @param int $length The optional length of the compressed data.
     * @return string|bool False on failure.
     */
    public static function decompress($compressed, $length = null)
    {

        if (empty($compressed))
            return $compressed;

        if (false !== ( $decompressed = @gzinflate($compressed) ))
            return $decompressed;

        if (false !== ( $decompressed = self::compatibleGzinflate($compressed) ))
            return $decompressed;

        if (false !== ( $decompressed = @gzuncompress($compressed) ))
            return $decompressed;

        if (function_exists('gzdecode')) {
            $decompressed = @gzdecode($compressed);

            if (false !== $decompressed)
                return $decompressed;
        }

        return $compressed;
    }

    /**
     * Whether the content be decoded based on the headers.
     *
     * @since 2.8.0
     *
     * @static
     *
     * @param array|string $headers All of the available headers.
     * @return bool
     */
    public static function shouldDecode($headers)
    {
        if (is_array($headers)) {
            if (array_key_exists('content-encoding', $headers) && !empty($headers['content-encoding']))
                return true;
        } elseif (is_string($headers)) {
            return ( stripos($headers, 'content-encoding:') !== false );
        }

        return false;
    }

    /**
     * Decompression of deflated string while staying compatible with the majority of servers.
     *
     * Certain Servers will return deflated data with headers which PHP's gzinflate()
     * function cannot handle out of the box. The following function has been created from
     * various snippets on the gzinflate() PHP documentation.
     *
     * Warning: Magic numbers within. Due to the potential different formats that the compressed
     * data may be returned in, some "magic offsets" are needed to ensure proper decompression
     * takes place. For a simple progmatic way to determine the magic offset in use, see:
     * https://core.trac.wordpress.org/ticket/18273
     *
     * @since 2.8.1
     * @link https://core.trac.wordpress.org/ticket/18273
     * @link http://au2.php.net/manual/en/function.gzinflate.php#70875
     * @link http://au2.php.net/manual/en/function.gzinflate.php#77336
     *
     * @static
     *
     * @param string $gzData String to decompress.
     * @return string|bool False on failure.
     */
    public static function compatibleGzinflate($gzData)
    {

        // Compressed data might contain a full header, if so strip it for gzinflate().
        if (substr($gzData, 0, 3) == "\x1f\x8b\x08") {
            $i = 10;
            $flg = ord(substr($gzData, 3, 1));
            if ($flg > 0) {
                if ($flg & 4) {
                    list($xlen) = unpack('v', substr($gzData, $i, 2));
                    $i = $i + 2 + $xlen;
                }
                if ($flg & 8)
                    $i = strpos($gzData, "\0", $i) + 1;
                if ($flg & 16)
                    $i = strpos($gzData, "\0", $i) + 1;
                if ($flg & 2)
                    $i = $i + 2;
            }
            $decompressed = @gzinflate(substr($gzData, $i, -8));
            if (false !== $decompressed)
                return $decompressed;
        }

        // Compressed data from java.util.zip.Deflater amongst others.
        $decompressed = @gzinflate(substr($gzData, 2));
        if (false !== $decompressed)
            return $decompressed;

        return false;
    }

    public static function getOpInstance()
    {
        if (!self::$objOperation instanceof RevOperations) {
            self::$objOperation = new RevOperations();
        }
        return self::$objOperation;
    }

    public static function getIsset($variable)
    {
        return isset($variable);
    }

    public static function setVar($key = null, $value = null)
    {
        if (!empty($key) && !is_null($value)) {
            self::$dynamicObj[$key] = $value;
        }
    }

    public static function getVar($key = null)
    {
        if (@RevsliderPrestashop::getIsset(self::$dynamicObj[$key])) {
            return self::$dynamicObj[$key];
        }
        return null;
    }

    public static function reset()
    {
        self::$dynamicObj = array();
    }
}

// @codingStandardsIgnoreEnd