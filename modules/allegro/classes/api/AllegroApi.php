<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

if (file_exists(dirname(__FILE__).'/../../../AllegroApi.php')) {
	include dirname(__FILE__).'/../../../AllegroApi.php';
} else {
	if (version_compare(PHP_VERSION, '5.6', '<')) {
	    include dirname(__FILE__).'/53.php';
	} else {
	    include dirname(__FILE__).'/56.php';
	}
}
