<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/controllers/CronAllegroSyncController.php';

// Check secure key if not CLI
if (php_sapi_name() !== "cli") {
    if (Tools::getValue('key') !== substr(md5(_COOKIE_KEY_), 0, 16)) {
        die();
    }
}

CronAllegroSyncController::relist();
