<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

if (version_compare(PHP_VERSION, '5.6', '<')) {
    include dirname(__FILE__).'/54.php';
} else {
    include dirname(__FILE__).'/56.php';
}
