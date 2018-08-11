<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
@include_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/sizeschart.php');

if(_PS_VERSION_ > "1.5.0.0"){

$context = Context::getContext();

}

$errors = array();
	global $params;
$sizeschart = new SizesChart();
echo $sizeschart->displayFrontForm($params);
