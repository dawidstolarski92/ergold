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

$current_path = '';
$ext_img = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg'); //
$ext=array_merge($ext_img);
include('config/config.php');
include('include/utils.php');

if (preg_match('/\.{1,2}[\/|\\\]/', Tools::getValue('path')) !== 0) {
    die('wrong path');
}

if (strpos(Tools::getValue('name'), DIRECTORY_SEPARATOR) !== false) {
    die('wrong path');
}

$path = $current_path.Tools::getValue('path');
$name = Tools::getValue('name');

$info = pathinfo($name);
if (!in_array(fix_strtolower($info['extension']), $ext)) {
    die('wrong extension');
}

header('Pragma: private');
header('Cache-control: private, must-revalidate');
header('Content-Type: application/octet-stream');
header('Content-Length: '.(string)filesize($path.$name));
header('Content-Disposition: attachment; filename="'.($name).'"');
readfile($path.$name);

exit;
