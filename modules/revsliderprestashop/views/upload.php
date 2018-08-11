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

// @codingStandardsIgnoreStart

$current_path = '';
$ext_img = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg'); //
$ext=array_merge($ext_img);
include('config/config.php');
include('include/utils.php');


/* revolution add */

include_once(_PS_MODULE_DIR_ . 'revsliderprestashop/revprestashoploader.php');
include_once(_PS_MODULE_DIR_ . 'revsliderprestashop/revslider_admin.php');

/* end revolution add */

//Tools::getValue('path') = $current_path . Tools::getValue('path');
//Tools::getValue('path_thumb') = $thumbs_base_path . Tools::getValue('path_thumb');

$storeFolder = $current_path.Tools::getValue('path');
//$storeFolderThumb = $thumbs_base_path.Tools::getValue('path_thumb');

//$path_pos = strpos($storeFolder, $current_path);
//$thumb_pos = strpos(Tools::getValue('path_thumb'), $thumbs_base_path);

//if ($path_pos === false || $thumb_pos === false || preg_match('/\.{1,2}[\/|\\\]/', Tools::getValue('path_thumb')) !== 0 || preg_match('/\.{1,2}[\/|\\\]/', Tools::getValue('path')) !== 0)
//    die('wrong path');

if (empty($storeFolder)) {
    die('wrong path');
}

$path = $storeFolder;
$cycle = true;
$max_cycles = 50;
$i = 0;
while ($cycle && $i < $max_cycles) {
    $i++;
    if ($path == $current_path) {
        $cycle = false;
    }
    if (file_exists($path . 'config.php')) {
        require_once($path . 'config.php');
        $cycle = false;
    }
    $path = fix_dirname($path) . '/';
}

if (!empty($_FILES)) {
    $info = pathinfo($_FILES['file']['name']);
    if (@RevsliderPrestashop::getIsset($info['extension']) && in_array(fix_strtolower($info['extension']), $ext)) {
        $tempFile = $_FILES['file']['tmp_name'];
//
//		$targetPath = $storeFolder;
//		$targetPathThumb = $storeFolderThumb;
//		$_FILES['file']['name'] = fix_filename($_FILES['file']['name'], $transliteration);
//
//		$file_name_splitted = explode('.', $_FILES['file']['name']);
//		array_pop($file_name_splitted);
//		$_FILES['file']['name'] = implode('-', $file_name_splitted).'.'.$info['extension'];
//
//		if (file_exists($targetPath.$_FILES['file']['name']))
//		{
//			$i = 1;
//			$info = pathinfo($_FILES['file']['name']);
//			while (file_exists($targetPath.$info['filename'].'_'.$i.'.'.$info['extension']))
//			{
//				$i++;
//			}
//			$_FILES['file']['name'] = $info['filename'].'_'.$i.'.'.$info['extension'];
//		}
//		$targetFile = $_FILES['file']['name'];
//		$targetFile = $targetPath.$_FILES['file']['name'];
//		$targetFileThumb = $targetPathThumb.$_FILES['file']['name'];






        if (in_array(fix_strtolower($info['extension']), $ext_img) && @getimagesize($tempFile) != false) {
            $is_img = true;
        } else {
            $is_img = false;
        }

        if ($is_img) {

            /* revolution odl system */

            $targetFolder = ABSPATH . '/uploads/';
//        		$randnum = rand(0000000,9999999);
//				$sds_time = time();
//        		$NewFileName = $randnum.'-'.$sds_time;
            $NewFileName = preg_replace_callback('/[^a-zA-Z0-9_\-]+/', create_function('$match', 'return "-";'), $info['filename']);


//                        $tempFile = $_FILES['file']['tmp_name'];
            $targetPath = $targetFolder;

            // Validate the file type
            $fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // File extensions
//                $fileParts = pathinfo($_FILES['file']['name']);


            if (in_array($info['extension'], $fileTypes)) {
                $worked = UniteFunctionsWPRev::importMediaImg($tempFile, $targetPath, $NewFileName . '.' . $info['extension']);
                if (!empty($worked)) {
                    echo '1';
                }
            } else {
                echo '0';
            }
        }
    } else {
        header('HTTP/1.1 406 file not permitted', true, 406);
        exit();
    }
} else {
    header('HTTP/1.1 405 Bad Request', true, 405);
    exit();
}
if (Tools::isSubmit('submit')) {
    $query = http_build_query(
            array(
                'type' => Tools::getValue('type'),
                'lang' => Tools::getValue('lang'),
                'popup' => Tools::getValue('popup'),
                'field_id' => Tools::getValue('field_id'),
                'fldr' => Tools::getValue('fldr'),
            )
    );
//    header('location: dialog.php?' . $query);
    Tools::redirect(get_url().'/views/dialog.php?' . $query);
}

// @codingStandardsIgnoreEnd