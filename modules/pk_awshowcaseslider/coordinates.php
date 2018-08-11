<?php
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/pk_awshowcaseslider.php');

$module = new pk_awShowcaseSlider();

$action = Tools::getValue('action');
$id_slide = Tools::getValue('id_slide');
$idcoord = Tools::getValue('idcoord');
$coordX = Tools::getValue('coordX');
$coordY = Tools::getValue('coordY');
$idlang = Tools::getValue('idlang');
$idshop = Tools::getValue('idshop');
$idprod = Tools::getValue('idprod');
$text = Tools::getValue('text');

if ($action == "add") {
	$sql = 'INSERT INTO `'._DB_PREFIX_.'pk_awshowcaseslider_points` (`id_shop`, `id_slide`, `id_lang`, `id_coord`, `coordinateX`, `coordinateY`) VALUES ('.$idshop.', '.$id_slide.', '.$idlang.', '.$idcoord.', '.$coordX.', '.$coordY.');';
	if (!Db::getInstance()->Execute( $sql )) echo "sql error";
	echo $idcoord;
}
if ($action == "remove") {
	$sql = 'DELETE FROM `'._DB_PREFIX_.'pk_awshowcaseslider_points` WHERE `id_coord` = "'.$idcoord.'";';
	if (!Db::getInstance()->Execute( $sql )) {echo "no action";} else {echo $idcoord;}
}
if ($action == "addProduct") {

	$sql_name = 'SELECT name, link_rewrite FROM `'._DB_PREFIX_.'product_lang` WHERE id_product='.$idprod.' AND id_lang='.$idlang.' AND id_shop='.$idshop;	

	$name = Db::getInstance()->ExecuteS( $sql_name );
	if (!$name) echo "sql error";	
	
	$imgUrl["url"] = $module->getImg($idprod, $name[0]["link_rewrite"]);	
	$imgUrl["name"] = $name[0]["name"];	
	$imgID = $module->getProdCover($idprod);	

	$sql = 'UPDATE `'._DB_PREFIX_.'pk_awshowcaseslider_points` SET 
		id_product='.$idprod.', 
		point_type="product", 
		product_name="'.$name[0]["name"].'", 
		product_link_rewrite="'.$name[0]["link_rewrite"].'", 
		product_image="'.$imgID["id_image"].'", 
		product_image_link="'.$imgUrl["url"].'" 
		WHERE id_slide = '.$id_slide.' AND 
				id_shop='.$idshop.' AND 
				id_lang='.$idlang.' AND 
				id_coord='.$idcoord;	

	if (!Db::getInstance()->Execute( $sql ))  echo "sql error";

	print_r(json_encode($imgUrl)); // return product data

}
if ($action == "addText") {
	$sql = 'UPDATE `'._DB_PREFIX_.'pk_awshowcaseslider_points` SET point_text="'.$text.'", point_type="text" WHERE id_slide='.$id_slide.' AND id_shop='.$idshop.' AND id_lang='.$idlang.' AND id_coord='.$idcoord;
	if (!Db::getInstance()->Execute( $sql ))  echo "sql error";
	
}	


?>