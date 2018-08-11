<?php
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/pk_themesettings.php');
$ats = new Pk_ThemeSettings();

//if (!Tools::isSubmit('secure_key') || Tools::getValue('secure_key') != $home_slider->secure_key || !Tools::getValue('action'))
//	die(1);
if (Tools::getValue('theme_settings') == "get") {
	echo json_encode($theme_settings = $ats->getOptions());
}
if (Tools::getValue('getImage') == 1) {
	$pid = Tools::getValue('pid');
	$lrw = Tools::getValue('link_rewrite');
	$inm = Tools::getValue('imgName');
	echo json_encode($ats->getImg($pid, $lrw, $inm));
}
if (Tools::getValue('setEmail') == 1) {
	echo json_encode(Tools::getValue('cs-email'));
}
if (Tools::getValue('getImgByAttr') == 1) {
	$imgattr = Tools::getValue('attrid');
	$lrw = Tools::getValue('link_rewrite');
	$pid = Tools::getValue('pid');
	$inm = Tools::getValue('imgName');
	echo json_encode($ats->getImgByAttr($pid, $lrw, $inm, $imgattr));
}
if (Tools::getValue('id') != "") {
	$customer = Tools::getValue('customer');
	$pids = Tools::getValue('id');
	$product_ids = explode(',', $pids);
	$lang_id = Tools::getValue('lang_id');
	$imgName = Tools::getValue('imgName');
	foreach ($product_ids as $key => $id) {		

		if (!Validate::isUnsignedId($id)) die(Tools::displayError());
		$validate = Configuration::get('PRODUCT_COMMENTS_MODERATE');

		// average grade
		$gradeRaw = Db::getInstance()->getRow('
		SELECT (SUM(pc.`grade`) / COUNT(pc.`grade`)) AS grade
		FROM `'._DB_PREFIX_.'product_comment` pc
		WHERE pc.`id_product` = '.(int)$id.'
		AND pc.`deleted` = 0'.($validate == '1' ? ' AND pc.`validate` = 1' : ''));
		if (empty($gradeRaw)) $grade = 0;
		if (!empty($gradeRaw)) $grade = $gradeRaw["grade"];

		/* comments number */
		$nbrRaw = Db::getInstance()->getRow('
		SELECT COUNT(*) AS "nbr"
		FROM `'._DB_PREFIX_.'product_comment` pc
		WHERE `id_product` = '.(int)($id).($validate == '1' ? ' AND `validate` = 1' : ''));
		if (empty($nbrRaw)) $nbr = 0;
		if (!empty($nbrRaw)) $nbr = $nbrRaw["nbr"];
		
		$data["grade"][$id] = (int)$grade;
		$data["commentsNbr"][$id] = (int)$nbr;

		$imgholder = Image::getImages($lang_id, $id);				
		foreach ($imgholder as $num => $dt) {
			if ($dt["cover"] != 1) {				
				$link_rewrite = Db::getInstance()->executeS('SELECT `link_rewrite` FROM `'._DB_PREFIX_.'product_lang` WHERE id_lang='.$lang_id.' AND id_shop='.(int)Context::getContext()->shop->id.' AND id_product='.$id);
				$data["subimage"][$id] = $ats->getImLink($link_rewrite[0]["link_rewrite"], $id.'-'.$dt["id_image"], $imgName);
				//$data["subimage"][$id] = $link_rewrite[0]["link_rewrite"];
				break;	
			} else {
				$data["subimage"][$id] = "no_image";
			}
		}			

		$data["modules"]["installed"]["wishlist"] = $ats->isInst(Tools::getValue('wishlist'));
		$data["modules"]["installed"]["favorites"] = $ats->isInst(Tools::getValue('favorites'));
		$data["modules"]["enabled"]["wishlist"] = $ats->isEn(Tools::getValue('wishlist'));
		$data["modules"]["enabled"]["favorites"] = $ats->isEn(Tools::getValue('favorites'));

	}	
	echo json_encode($data);
}

if (Tools::getValue('action') == "updatePositions") {
	$id_hook = Tools::getValue('id_hook');	
	$id_module = Tools::getValue('id_module');	
	$way = Tools::getValue('way');
	$positions = Tools::getValue(strval($id_hook));
	$ats->ajaxProcessUpdateModPositions($id_module, $id_hook, $way, $positions);	
}

?>