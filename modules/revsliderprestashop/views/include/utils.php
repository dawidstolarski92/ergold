<?php
// @codingStandardsIgnoreStart

$context = Context::getContext();
if (!Employee::checkPassword((int) $context->cookie->id_employee, $context->cookie->passwd)) {
    die('forbiden');
}

function deleteDir($dir)
{
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDir($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}

function duplicate_file($old_path, $name)
{
    if (file_exists($old_path)) {
        $info = pathinfo($old_path);
        $new_path = $info['dirname'] . "/" . $name . "." . $info['extension'];
        if (file_exists($new_path)) {
            return false;
        }
        return copy($old_path, $new_path);
    }
}

function rename_file($old_path, $name, $transliteration)
{
    $name = fix_filename($name, $transliteration);
    if (file_exists($old_path)) {
        $info = pathinfo($old_path);
        $new_path = $info['dirname'] . "/" . $name . "." . $info['extension'];
        if (file_exists($new_path)) {
            return false;
        }
        return rename($old_path, $new_path);
    }
}

function rename_folder($old_path, $name, $transliteration)
{
    $name = fix_filename($name, $transliteration);
    if (file_exists($old_path)) {
        $new_path = fix_dirname($old_path) . "/" . $name;
        if (file_exists($new_path)) {
            return false;
        }
        return rename($old_path, $new_path);
    }
}

function makeSize($size)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $u = 0;
    while ((round($size / 1024) > 0) && ($u < 4)) {
        $size = $size / 1024;
        $u++;
    }
    return (number_format($size, 0) . " " . $units[$u]);
}

function foldersize($path)
{
    $total_size = 0;
    $files = scandir($path);
    $cleanPath = rtrim($path, '/') . '/';

    foreach ($files as $t) {
        if ($t <> "." && $t <> "..") {
            $currentFile = $cleanPath . $t;
            if (is_dir($currentFile)) {
                $size = foldersize($currentFile);
                $total_size += $size;
            } else {
                $size = filesize($currentFile);
                $total_size += $size;
            }
        }
    }

    return $total_size;
}

function create_folder($path = false, $path_thumbs = false)
{
    $oldumask = umask(0);
    if ($path && !file_exists($path)) {
        mkdir($path, 0777, true);
    } // or even 01777 so you get the sticky bit set
    if ($path_thumbs && !file_exists($path_thumbs)) {
        mkdir($path_thumbs, 0777, true) or die("$path_thumbs cannot be found");
    } // or even 01777 so you get the sticky bit set
    umask($oldumask);
}

function check_files_extensions_on_path($path, $ext)
{
    if (!is_dir($path)) {
        $fileinfo = pathinfo($path);
        if (!in_array(mb_strtolower($fileinfo['extension']), $ext)) {
            unlink($path);
        }
    } else {
        $files = scandir($path);
        foreach ($files as $file) {
            check_files_extensions_on_path(trim($path, '/') . "/" . $file, $ext);
        }
    }
}

function check_files_extensions_on_phar($phar, &$files, $basepath, $ext)
{
    foreach ($phar as $file) {
        if ($file->isFile()) {
            if (in_array(mb_strtolower($file->getExtension()), $ext)) {
                $files[] = $basepath . $file->getFileName();
            }
        } elseif ($file->isDir()) {
            $iterator = new DirectoryIterator($file);
            check_files_extensions_on_phar($iterator, $files, $basepath . $file->getFileName() . '/', $ext);
        }
    }
}

function fix_filename($str, $transliteration)
{
    if ($transliteration) {
        //	if( function_exists( 'transliterator_transliterate' ) )
//	{
//	   $str = transliterator_transliterate( 'Accents-Any', $str );
//	}
//	else
//	{
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
//	}

        $str = preg_replace("/[^a-zA-Z0-9\.\[\]_| -]/", '', $str);
    }

    $str = str_replace(array('"', "'", "/", "\\"), "", $str);
    $str = strip_tags($str);

    // Empty or incorrectly transliterated filename.
    // Here is a point: a good file UNKNOWN_LANGUAGE.jpg could become .jpg in previous code.
    // So we add that default 'file' name to fix that issue.
    if (strpos($str, '.') === 0) {
        $str = 'file' . $str;
    }

    return trim($str);
}

function fix_dirname($str)
{
    return str_replace('~', ' ', dirname(str_replace(' ', '~', $str)));
}

function fix_strtoupper($str)
{
    if (function_exists('mb_strtoupper')) {
        return mb_strtoupper($str);
    } else {
        return Tools::strtoupper($str);
    }
}

function fix_strtolower($str)
{
    if (function_exists('mb_strtoupper')) {
        return mb_strtolower($str);
    } else {
        return Tools::strtolower($str);
    }
}

function fix_path($path, $transliteration)
{
    $info = pathinfo($path);
    if (($s = strrpos($path, '/')) !== false) {
        $s++;
    }
    if (($e = strrpos($path, '.') - $s) !== Tools::strlen($info['filename'])) {
        $info['filename'] = Tools::substr($path, $s, $e);
        $info['basename'] = Tools::substr($path, $s);
    }
    $tmp_path = $info['dirname'] . DIRECTORY_SEPARATOR . $info['basename'];

    $str = fix_filename($info['filename'], $transliteration);
    if ($tmp_path != "") {
        return $tmp_path . DIRECTORY_SEPARATOR . $str;
    } else {
        return $str;
    }
}

function base_url()
{
    return sprintf(
        "%s://%s", @RevsliderPrestashop::getIsset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['HTTP_HOST']
    );
}

function config_loading($current_path, $fld)
{
    if (file_exists($current_path . $fld . ".config")) {
        require_once($current_path . $fld . ".config");
        return true;
    }
    echo "!!!!" . $parent = fix_dirname($fld);
    if ($parent != "." && !empty($parent)) {
        config_loading($current_path, $parent);
    }

    return false;
}
// @codingStandardsIgnoreEnd
