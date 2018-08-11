<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

// Init session
if (!isset($_SESSION) && version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
    session_start();
}

class AllegroApiCache
{
	public static function store($key, $data, $expire = 10800)
	{
		$_SESSION[md5($key)] = array('data' => $data, 'expire' => (time()+(int)$expire));
	}

	public static function fetch($key)
	{
		if(isset($_SESSION[md5($key)]) && $_SESSION[md5($key)]['expire'] > time())
			return $_SESSION[md5($key)]['data'];
		return false;
	}

	public static function clean($key)
	{
		unset($_SESSION[md5($key)]);
	}
}
