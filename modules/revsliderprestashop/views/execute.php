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
$ext_img = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg'); //
$ext = array_merge($ext_img);
$current_path = '';
$thumbs_base_path = '';
$transliteration = false;
$duplicate_files = false;
$create_folders = false;
$delete_folders = false;
$rename_folders = false;
$delete_files = true;
$rename_files = true;
$relative_image_creation = false;
$relative_path_from_current_pos = array('thumb/', 'thumb/');
$relative_image_creation_name_to_prepend = array('', 'test_');
$relative_image_creation_name_to_append = array('_test', '');
$fixed_image_creation = false;
$fixed_path_from_filemanager = array('../test/', '../test1/');
$fixed_image_creation_name_to_prepend = array('', 'test_');
$fixed_image_creation_to_append = array('_test', '');


include('config/config.php');
$context = Context::getContext();
if (!Employee::checkPassword((int) $context->cookie->id_employee, $context->cookie->passwd)) {
    die('forbiden');
}
include('include/utils.php');

Tools::getValue('path_thumb') = $thumbs_base_path . Tools::getValue('path_thumb');
if (!Tools::getValue('path_thumb') && trim(Tools::getValue('path_thumb')) == '')
    die('wrong path');

$thumb_pos = strpos(Tools::getValue('path_thumb'), $thumbs_base_path);
if ($thumb_pos === false || preg_match('/\.{1,2}[\/|\\\]/', Tools::getValue('path_thumb')) !== 0 || preg_match('/\.{1,2}[\/|\\\]/', Tools::getValue('path')) !== 0
)
    die('wrong path');

$language_file = 'lang/en.php';
if (Tools::getValue('lang') && Tools::getValue('lang') != 'undefined' && Tools::getValue('lang') != '') {
    $path_parts = pathinfo(Tools::getValue('lang'));
    if (is_readable('lang/' . $path_parts['basename'] . '.php'))
        $language_file = 'lang/' . $path_parts['basename'] . '.php';
}
require_once $language_file;

$base = $current_path;

if (Tools::getValue('path'))
    $path = $current_path . str_replace("\0", "", Tools::getValue('path'));
else
    $path = $current_path;

$cycle = true;
$max_cycles = 50;
$i = 0;
while ($cycle && $i < $max_cycles) {
    $i++;
    if ($path == $base)
        $cycle = false;

    if (file_exists($path . 'config.php')) {
        require_once($path . 'config.php');
        $cycle = false;
    }
    $path = fix_dirname($path) . '/';
    $cycle = false;
}

$path = $current_path . str_replace("\0", "", Tools::getValue('path'));
$path_thumb = Tools::getValue('path_thumb');
if (Tools::getValue('name')) {
    $name = Tools::getValue('name');
    if (preg_match('/\.{1,2}[\/|\\\]/', $name) !== 0)
        die('wrong name');
}

$info = pathinfo($path);
if (@RevsliderPrestashop::getIsset($info['extension']) && !(Tools::getValue('action') && Tools::getValue('action') == 'delete_folder') && !in_array(Tools::strtolower($info['extension']), $ext))
    die('wrong extension');

if (Tools::getValue('action')) {

    switch (Tools::getValue('action')) {
        case 'delete_file':
            if ($delete_files) {
                unlink($path);
                if (file_exists($path_thumb))
                    unlink($path_thumb);

                $info = pathinfo($path);
                if ($relative_image_creation) {
                    foreach ($relative_path_from_current_pos as $k => $path) {
                        if ($path != '' && $path[Tools::strlen($path) - 1] != '/')
                            $path .= '/';
                        if (file_exists($info['dirname'] . '/' . $path . $relative_image_creation_name_to_prepend[$k] . $info['filename'] . $relative_image_creation_name_to_append[$k] . '.' . $info['extension']))
                            unlink($info['dirname'] . '/' . $path . $relative_image_creation_name_to_prepend[$k] . $info['filename'] . $relative_image_creation_name_to_append[$k] . '.' . $info['extension']);
                    }
                }

                if ($fixed_image_creation) {
                    foreach ($fixed_path_from_filemanager as $k => $path) {
                        if ($path != '' && $path[Tools::strlen($path) - 1] != '/')
                            $path .= '/';
                        $base_dir = $path . substr_replace($info['dirname'] . '/', '', 0, Tools::strlen($current_path));
                        if (file_exists($base_dir . $fixed_image_creation_name_to_prepend[$k] . $info['filename'] . $fixed_image_creation_to_append[$k] . '.' . $info['extension']))
                            unlink($base_dir . $fixed_image_creation_name_to_prepend[$k] . $info['filename'] . $fixed_image_creation_to_append[$k] . '.' . $info['extension']);
                    }
                }
            }
            break;
        case 'delete_folder':
            if ($delete_folders) {
                if (is_dir($path_thumb))
                    deleteDir($path_thumb);
                if (is_dir($path)) {
                    deleteDir($path);
                    if ($fixed_image_creation) {
                        foreach ($fixed_path_from_filemanager as $k => $paths) {
                            if ($paths != '' && $paths[Tools::strlen($paths) - 1] != '/')
                                $paths .= '/';
                            $base_dir = $paths . substr_replace($path, '', 0, Tools::strlen($current_path));
                            if (is_dir($base_dir))
                                deleteDir($base_dir);
                        }
                    }
                }
            }
            break;
        case 'create_folder':
            if ($create_folders)
                create_folder(fix_path($path, $transliteration), fix_path($path_thumb, $transliteration));
            break;
        case 'rename_folder':
            if ($rename_folders) {
                $name = fix_filename($name, $transliteration);
                $name = str_replace('.', '', $name);

                if (!empty($name)) {
                    if (!rename_folder($path, $name, $transliteration))
                        die(lang_Rename_existing_folder);
                    rename_folder($path_thumb, $name, $transliteration);
                    if ($fixed_image_creation) {
                        foreach ($fixed_path_from_filemanager as $k => $paths) {
                            if ($paths != '' && $paths[Tools::strlen($paths) - 1] != '/')
                                $paths .= '/';
                            $base_dir = $paths . substr_replace($path, '', 0, Tools::strlen($current_path));
                            rename_folder($base_dir, $name, $transliteration);
                        }
                    }
                }
                else
                    die(lang_Empty_name);
            }
            break;
        case 'rename_file':
            if ($rename_files) {
                $name = fix_filename($name, $transliteration);
                if (!empty($name)) {
                    if (!rename_file($path, $name, $transliteration))
                        die(lang_Rename_existing_file);
                    rename_file($path_thumb, $name, $transliteration);
                    if ($fixed_image_creation) {
                        $info = pathinfo($path);
                        foreach ($fixed_path_from_filemanager as $k => $paths) {
                            if ($paths != '' && $paths[Tools::strlen($paths) - 1] != '/')
                                $paths .= '/';
                            $base_dir = $paths . substr_replace($info['dirname'] . '/', '', 0, Tools::strlen($current_path));
                            if (file_exists($base_dir . $fixed_image_creation_name_to_prepend[$k] . $info['filename'] . $fixed_image_creation_to_append[$k] . '.' . $info['extension']))
                                rename_file($base_dir . $fixed_image_creation_name_to_prepend[$k] . $info['filename'] . $fixed_image_creation_to_append[$k] . '.' . $info['extension'], $fixed_image_creation_name_to_prepend[$k] . $name . $fixed_image_creation_to_append[$k], $transliteration);
                        }
                    }
                }
                else
                    die(lang_Empty_name);
            }
            break;
        case 'duplicate_file':
            if ($duplicate_files) {
                $name = fix_filename($name, $transliteration);
                if (!empty($name)) {
                    if (!duplicate_file($path, $name))
                        die(lang_Rename_existing_file);
                    duplicate_file($path_thumb, $name);
                    if ($fixed_image_creation) {
                        $info = pathinfo($path);
                        foreach ($fixed_path_from_filemanager as $k => $paths) {
                            if ($paths != '' && $paths[Tools::strlen($paths) - 1] != '/')
                                $paths .= '/';
                            $base_dir = $paths . substr_replace($info['dirname'] . '/', '', 0, Tools::strlen($current_path));
                            if (file_exists($base_dir . $fixed_image_creation_name_to_prepend[$k] . $info['filename'] . $fixed_image_creation_to_append[$k] . '.' . $info['extension']))
                                duplicate_file($base_dir . $fixed_image_creation_name_to_prepend[$k] . $info['filename'] . $fixed_image_creation_to_append[$k] . '.' . $info['extension'], $fixed_image_creation_name_to_prepend[$k] . $name . $fixed_image_creation_to_append[$k]);
                        }
                    }
                }
                else
                    die(lang_Empty_name);
            }
            break;
        default:
            die('wrong action');
    }
}
// @codingStandardsIgnoreEnd