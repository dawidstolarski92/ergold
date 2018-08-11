<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

define('_APP_URL_', 'http'.(Configuration::get('PS_SSL_ENABLED') ? 's' : '').'://'.$_SERVER['HTTP_HOST'].'/');

if (!defined('_ALLEGRO_CACHE_')) {
    define('_ALLEGRO_CACHE_', true);
}
define('_ALLEGRO_SSL_VERIFY_',      false);
define('_ALLEGRO_SOAP_CACHE_',      0);

define('_ALLEGRO_IMG_DIR_',         dirname(__FILE__).'/img/product/');
define('_ALLEGRO_IMG_FRAME_DIR_',   dirname(__FILE__).'/img/frame/');
define('_ALLEGRO_LOGS_DIR_',        dirname(__FILE__).'/logs/');
define('_ALLEGRO_THEME_TPL_DIR_',   dirname(__FILE__).'/views/theme/');

define('_ALLEGRO_FIELDS_GLOBAL_', 	'4,5,11,32,10,12,13,14,340,27,33,34,4,28,15,29,35');
define('_ALLEGRO_FIELDS_PRODUCT_', 	'13,340,27,15,14');
define('_ALLEGRO_FIELDS_CATEGORY_', '28,15,340');
define('_ALLEGRO_FIELDS_SHIPPING_', '13,27,35');

define('_ALLEGRO_GC_',              253722);

@ini_set('default_socket_timeout', 60);
@set_time_limit(0);

if (extension_loaded("IonCube Loader") || php_sapi_name() == "cli") {
    include_once dirname(__FILE__).'/classes/api/AllegroApi.php';
}

include_once dirname(__FILE__).'/classes/AllegroApiCache.php';
include_once dirname(__FILE__).'/classes/AllegroProduct.php';
include_once dirname(__FILE__).'/classes/AllegroAuction.php';
include_once dirname(__FILE__).'/classes/AllegroImage.php';
include_once dirname(__FILE__).'/classes/AllegroCategory.php';
include_once dirname(__FILE__).'/classes/AllegroShipping.php';
include_once dirname(__FILE__).'/classes/AllegroField.php';
include_once dirname(__FILE__).'/classes/AllegroAccount.php';
include_once dirname(__FILE__).'/classes/AllegroTheme.php';
include_once dirname(__FILE__).'/classes/CoverManager.php';

include_once dirname(__FILE__).'/classes/AllegroFieldBuilder.php';
include_once dirname(__FILE__).'/classes/AllegroContentBuilder.php';
include_once dirname(__FILE__).'/classes/AllegroTools.php';


include_once dirname(__FILE__).'/controllers/AllegroSyncManager.php';

if (!function_exists('dd'))
{
    function dd($data) {var_dump($data); die();}
}

function getArrByKey($arr, $key)
{
    $ret = array();
    foreach ($arr as $value) {
        if (isset($value[$key])) {
            $ret[] = $value[$key];
        }
    }
    return $ret;
}

// @todo PS 1.5
if (!class_exists('PrestaShopLogger'))
{
    include_once dirname(__FILE__).'/classes/PrestaShopLogger.php';
}

if (!function_exists('mb_substr'))
{
    function mb_substr($str, $st, $len = 0) {
        return substr($str, $st, $len = 0);
    }
}