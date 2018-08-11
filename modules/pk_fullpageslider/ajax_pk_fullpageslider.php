<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('pk_fullpageslider.php');

$context = Context::getContext();
$home_slider = new pk_fullpageslider();
$slides = array();

if (!Tools::isSubmit('secure_key') || Tools::getValue('secure_key') != $home_slider->secure_key || !Tools::getValue('action'))
	die(1);

if (Tools::getValue('action') == 'updateSlidesPosition' && Tools::getValue('slides')) {

	$slides = Tools::getValue('slides');

	foreach ($slides as $position => $id_slide) {
		$res = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'fullpageslider_slides` SET `position` = '.(int)$position.' WHERE `id_slides` = '.(int)$id_slide);
	}

	$home_slider->clearCache();

}