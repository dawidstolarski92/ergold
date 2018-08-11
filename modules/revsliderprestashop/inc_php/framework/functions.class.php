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

class UniteFunctionsRev
{
    public static function throwError($message, $code = null)
    {
        if (!empty($code)) {
            throw new Exception($message, $code);
        } else {
            throw new Exception($message);
        }
    }



    public static function downloadFile($str, $filename = "output.txt")
    {

        //output for download

        header('Content-Description: File Transfer');

        header('Content-Type: text/html; charset=UTF-8');

        header("Content-Disposition: attachment; filename=".$filename.";");

        header("Content-Transfer-Encoding: binary");

        header("Content-Length: ".Tools::strlen($str));

        echo $str;

        exit();
    }
    public static function boolToStr($bool)
    {
        if (gettype($bool) == "string") {
            return($bool);
        }

        if ($bool == true) {
            return("true");
        } else {
            return("false");
        }
    }
    public static function strToBool($str)
    {
        if (is_bool($str)) {
            return($str);
        }



        if (empty($str)) {
            return(false);
        }



        if (is_numeric($str)) {
            return($str != 0);
        }



        $str = Tools::strtolower($str);

        if ($str == "true") {
            return(true);
        }



        return(false);
    }

    //------------------------------------------------------------

    // get black value from rgb value

    public static function yiq($r, $g, $b)
    {
        return (($r*0.299)+($g*0.587)+($b*0.114));
    }
    public static function html2rgb($color)
    {
        if (empty($color)) {
            $color = "#000000";
        }



        if ($color[0] == '#') {
            $color = Tools::substr($color, 1);
        }

        if (Tools::strlen($color) == 6) {
            list($r, $g, $b) = array($color[0].$color[1],

                                     $color[2].$color[3],

                                     $color[4].$color[5]);
        } elseif (Tools::strlen($color) == 3) {
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        } else {
            return false;
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array($r, $g, $b);
    }





    //---------------------------------------------------------------------------------------------------

    // convert timestamp to time string

    public static function timestamp2Time($stamp)
    {
        $strTime = date("H:i", $stamp);

        return($strTime);
    }



    //---------------------------------------------------------------------------------------------------

    // convert timestamp to date and time string

    public static function timestamp2DateTime($stamp)
    {
        $strDateTime = date("d M Y, H:i", $stamp);

        return($strDateTime);
    }



    //---------------------------------------------------------------------------------------------------

    // convert timestamp to date string

    public static function timestamp2Date($stamp)
    {
        $strDate = date("d M Y", $stamp);    //27 Jun 2009

        return($strDate);
    }
    /**
     * get value from array. if not - return alternative
     */
    public static function getVal($arr, $key, $altVal = "")
    {
        if (is_array($arr)) {
            if (@RevsliderPrestashop::getIsset($arr[$key])) {
                return($arr[$key]);
            }
        } elseif (is_object($arr)) {
            if (@RevsliderPrestashop::getIsset($arr->$key)) {
                return($arr->$key);
            }
        }
        return($altVal);
    }
    public static function toString($obj)
    {
        return(trim((string)$obj));
    }

    public static function removeUtf8Bom($content)
    {
        $content = str_replace(chr(239), "", $content);

        $content = str_replace(chr(187), "", $content);

        $content = str_replace(chr(191), "", $content);

        $content = trim($content);

        return($content);
    }



    //------------------------------------------------------------

    // get variable from post or from get. get wins.

    public static function getPostGetVariable($name, $initVar = "")
    {
        $var = Tools::getValue($name, $initVar);

        return($var);
    }





    public static function getPostVariable($name, $initVar = "")
    {
        $var = Tools::getValue($name, $initVar);
        return($var);
    }



    public static function getGetVar($name, $initVar = "")
    {
        $var = Tools::getValue($name, $initVar);
        return($var);
    }
    public static function validateFilepath($filepath, $errorPrefix = null)
    {
        if (file_exists($filepath) == true) {
            return(false);
        }

        if ($errorPrefix == null) {
            $errorPrefix = "File";
        }

        $message = $errorPrefix." $filepath not exists!";

        self::throwError($message);
    }



    public static function validateNumeric($val, $fieldName = "")
    {
        self::validateNotEmpty($val, $fieldName);



        if (empty($fieldName)) {
            $fieldName = "Field";
        }



        if (!is_numeric($val)) {
            self::throwError("$fieldName should be numeric ");
        }
    }




    public static function validateNotEmpty($val, $fieldName = "")
    {
        if (empty($fieldName)) {
            $fieldName = "Field";
        }



        if (empty($val) && is_numeric($val) == false) {
            self::throwError("Field <b>$fieldName</b> should not be empty");
        }
    }




    public static function cleanStdClassToArray($arr)
    {
        $arr = (array)$arr;

        $arrNew = array();

        foreach ($arr as $key => $item) {
            $arrNew[$key] = $item;
        }

        return($arrNew);
    }



    public static function checkCreateDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir);
        }



        if (!is_dir($dir)) {
            self::throwError("Could not create directory: $dir");
        }
    }

    public static function checkDeleteFile($filepath)
    {
        if (file_exists($filepath) == false) {
            return(false);
        }



        $success = @unlink($filepath);

        if ($success == false) {
            self::throwError("Failed to delete the file: $filepath");
        }
    }



    //------------------------------------------------------------

    //filter array, leaving only needed fields - also array

    public static function filterArrFields($arr, $fields)
    {
        $arrNew = array();

        foreach ($fields as $field) {
            if (@RevsliderPrestashop::getIsset($arr[$field])) {
                $arrNew[$field] = $arr[$field];
            }
        }

        return($arrNew);
    }



    //------------------------------------------------------------

    //get path info of certain path with all needed fields

    public static function getPathInfo($filepath)
    {
        $info = pathinfo($filepath);



        //fix the filename problem

        if (!@RevsliderPrestashop::getIsset($info["filename"])) {
            $filename = $info["basename"];

            if (@RevsliderPrestashop::getIsset($info["extension"])) {
                $filename = Tools::substr($info["basename"], 0, (-Tools::strlen($info["extension"])-1));
            }

            $info["filename"] = $filename;
        }



        return($info);
    }
    public static function convertStdClassToArray($arr)
    {
        $arr = (array)$arr;



        $arrNew = array();



        foreach ($arr as $key => $item) {
            $item = (array)$item;

            $arrNew[$key] = $item;
        }



        return($arrNew);
    }



    //------------------------------------------------------------

    //save some file to the filesystem with some text

    public static function writeFile($str, $filepath)
    {
        if (is_writable(dirname($filepath)) == false) {
            @chmod(dirname($filepath), 0755);
                    //try to change the permissions
        }



        if (!is_writable(dirname($filepath))) {
            UniteFunctionsRev::throwError("Can't write file \"".$filepath."\", please change the permissions!");
        }



        $fp = fopen($filepath, "w+");

        fwrite($fp, $str);

        fclose($fp);
    }



    //------------------------------------------------------------

    //save some file to the filesystem with some text

//		public static function writeDebug($str,$filepath="debug.txt",$showInputs = true){
//
//			$post = print_r($_POST,true);			
//
//			$server = print_r($_SERVER,true);
//
//			
//
//			if(getType($str) == "array")
//
//				$str = print_r($str,true);
//
//			
//
//			if($showInputs == true){
//
//				$output = "--------------------"."\n";
//
//				$output .= $str."\n";
//
//				$output .= "Post: ".$post."\n";
//
//			}else{
//
//				$output = "---"."\n";
//
//				$output .= $str . "\n";
//
//			}
//
//						
//
//			if(!empty($_GET)){
//
//				$get = print_r($_GET,true);			
//
//				$output .= "Get: ".$get."\n";
//
//			}
//
//			
//
//			//$output .= "Server: ".$server."\n";
//
//			
//
//			$fp = fopen($filepath,"a+");
//
//			fwrite($fp,$output);
//
//			fclose($fp);
//
//		}






    public static function clearDebug($filepath = "debug.txt")
    {
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }




    public static function writeDebugError(Exception $e, $filepath = "debug.txt")
    {
        $message = $e->getMessage();

        $trace = $e->getTraceAsString();



        $output = $message."\n";

        $output .= $trace."\n";



        $fp = fopen($filepath, "a+");

        fwrite($fp, $output);

        fclose($fp);
    }


    public static function addToFile($str, $filepath)
    {
        if (!is_writable(dirname($filepath))) {
            UniteFunctionsRev::throwError("Can't write file \"".$filepath."\", please change the permissions!");
        }



        $fp = fopen($filepath, "a+");

        fwrite($fp, "---------------------\n");

        fwrite($fp, $str."\n");

        fclose($fp);
    }

    private static function checkPHPVersion()
    {
        $strVersion = phpversion();

        $version = (float)$strVersion;

        if ($version < 5) {
            throw new Exception("You must have php5 and higher in order to run the application. Your php version is: $version");
        }
    }

    private static function validateGD()
    {
        if (function_exists('gd_info') == false) {
            throw new Exception("You need GD library to be available in order to run this application. Please turn it on in php.ini");
        }
    }
    public static function isJsonActivated()
    {
        return(function_exists('Tools::jsonEncode'));
    }
    public static function jsonEncodeForClientSide($arr)
    {
        $json = "";
        if (!empty($arr)) {
            $json = Tools::jsonEncode($arr);
            $json = addslashes($json);
        }
        $json = "'".$json."'";
        return($json);
    }

    public static function jsonDecodeFromClientSide($data)
    {

//			$data = Tools::stripslashes($data);
//                        
//			$data = str_replace('&#092;"','\"',$data);

        $data = Tools::jsonDecode($data, true);

//			$data = (array)$data;

        return($data);
    }





    //--------------------------------------------------------------

    //validate if some directory is writable, if not - throw a exception

    private static function validateWritable($name, $path, $strList, $validateExists = true)
    {
        if ($validateExists == true) {

            //if the file/directory doesn't exists - throw an error.

            if (file_exists($path) == false) {
                throw new Exception("$name doesn't exists");
            }
        } else {

            //if the file not exists - don't check. it will be created.

            if (file_exists($path) == false) {
                return(false);
            }
        }



        if (is_writable($path) == false) {
            chmod($path, 0755);        //try to change the permissions

            if (is_writable($path) == false) {
                $strType = "Folder";

                if (is_file($path)) {
                    $strType = "File";
                }

                $message = "$strType $name is doesn't have a write permissions. Those folders/files must have a write permissions in order that this application will work properly: $strList";

                throw new Exception($message);
            }
        }
    }




    //--------------------------------------------------------------

    //Get url of image for output

    public static function getImageOutputUrl($filename, $width = 0, $height = 0, $exact = false)
    {

        //exact validation:

        if ($exact == "true" && (empty($width) || empty($height))) {
            self::throwError("Exact must have both - width and height");
        }



        $url = CMGlobals::$URL_GALLERY."?img=".$filename;

        if (!empty($width)) {
            $url .= "&w=".$width;
        }



        if (!empty($height)) {
            $url .= "&h=".$height;
        }



        if ($exact == true) {
            $url .= "&t=exact";
        }



        return($url);
    }
    public static function getFileList($path, $ext = "")
    {
        $dir = scandir($path);

        $arrFiles = array();

        foreach ($dir as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            if (!empty($ext)) {
                $info = pathinfo($file);

                $extension = UniteFunctionsRev::getVal($info, "extension");

                if ($ext != Tools::strtolower($extension)) {
                    continue;
                }
            }

            $filepath = $path . "/" . $file;

            if (is_file($filepath)) {
                $arrFiles[] = $file;
            }
        }

        return($arrFiles);
    }
    public static function getFoldersList($path)
    {
        $dir = scandir($path);

        $arrFiles = array();

        foreach ($dir as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $filepath = $path . "/" . $file;

            if (is_dir($filepath)) {
                $arrFiles[] = $file;
            }
        }

        return($arrFiles);
    }
    public static function trimArrayItems($arr)
    {
        if (gettype($arr) != "array") {
            UniteFunctionsRev::throwError("trimArrayItems error: The type must be array");
        }



        foreach ($arr as $key => $item) {
            if (is_array($item)) {
                foreach ($item as $key => $value) {
                    $arr[$key][$key] = trim($value);
                }
            } else {
                $arr[$key] = trim($item);
            }
        }



        return($arr);
    }
    public static function getUrlContents($url, $arrPost = array(), $method = "post", $debug = false)
    {
        $ch = curl_init();

        $timeout = 0;



        $strPost = '';

        foreach ($arrPost as $key => $value) {
            if (!empty($strPost)) {
                $strPost .= "&";
            }

            $value = urlencode($value);

            $strPost .= "$key=$value";
        }





        //set curl options

        if (Tools::strtolower($method) == "post") {
            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $strPost);
        } else {    //get

            $url .= "?".$strPost;
        }



        //remove me

        //Functions::addToLogFile(SERVICE_LOG_SERVICE, "url", $url);



        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);



        $headers = array();

        $headers[] = "User-Agent:Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8";

        $headers[] = "Accept-Charset:utf-8;q=0.7,*;q=0.7";

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);



        $response = curl_exec($ch);



        if ($debug == true) {
            dmp($url);

            dmp($response);

            exit();
        }



        if ($response == false) {
            throw new Exception("getUrlContents Request failed");
        }



        curl_close($ch);

        return($response);
    }
    public static function getHtmlLink($link, $text, $id = "", $class = "")
    {
        if (!empty($class)) {
            $class = " class='$class'";
        }

        if (!empty($id)) {
            $id = " id='$id'";
        }

        $html = "<a href=\"$link\"".$id.$class.">$text</a>";

        return($html);
    }
    public static function getHTMLSelect($arr, $default = "", $htmlParams = "", $assoc = false)
    {
        $html = "<select $htmlParams>";

        foreach ($arr as $key => $item) {
            $selected = "";



            if ($assoc == false) {
                if ($item == $default) {
                    $selected = " selected ";
                }
            } else {
                if (trim($key) == trim($default)) {
                    $selected = " selected ";
                }
            }





            if ($assoc == true) {
                $html .= "<option $selected value='$key'>$item</option>";
            } else {
                $html .= "<option $selected value='$item'>$item</option>";
            }
        }

        $html.= "</select>";

        return($html);
    }
    public static function arrayToAssoc($arr, $field = null)
    {
        $arrAssoc = array();



        foreach ($arr as $item) {
            if (empty($field)) {
                $arrAssoc[$item] = $item;
            } else {
                $arrAssoc[$item[$field]] = $item;
            }
        }



        return($arrAssoc);
    }
    public static function assocToArray($assoc)
    {
        $arr = array();

        foreach ($assoc as $item) {
            $arr[] = $item;
        }



        return($arr);
    }
    public static function normalizeTextareaContent($content)
    {
        if (empty($content)) {
            return($content);
        }

        $content = Tools::stripslashes($content);

        $content = trim($content);

        return($content);
    }
    public static function getRandomArrayItem($arr)
    {
        $numItems = count($arr);

        $rand = rand(0, $numItems-1);

        $item = $arr[$rand];

        return($item);
    }

    public static function deleteDir($path, $deleteOriginal = true, $arrNotDeleted = array(), $originalPath = "")
    {
        if (empty($originalPath)) {
            $originalPath = $path;
        }
        if (getType($path) == "array") {
            $arrPaths = $path;

            foreach ($path as $singlePath) {
                $arrNotDeleted = self::deleteDir($singlePath, $deleteOriginal, $arrNotDeleted, $originalPath);
            }

            return($arrNotDeleted);
        }



        if (!file_exists($path)) {
            return($arrNotDeleted);
        }



        if (is_file($path)) {
            $deleted = unlink($path);

            if (!$deleted) {
                $arrNotDeleted[] = $path;
            }
        } else {    //delete directory

            $arrPaths = scandir($path);

            foreach ($arrPaths as $file) {
                if ($file == "." || $file == "..") {
                    continue;
                }

                $filepath = realpath($path."/".$file);

                $arrNotDeleted = self::deleteDir($filepath, $deleteOriginal, $arrNotDeleted, $originalPath);
            }



            if ($deleteOriginal == true || $originalPath != $path) {
                $deleted = @rmdir($path);

                if (!$deleted) {
                    $arrNotDeleted[] = $path;
                }
            }
        }



        return($arrNotDeleted);
    }
    public static function copyDir($source, $dest, $rel_path = "", $blackList = null)
    {
        $full_source = $source;

        if (!empty($rel_path)) {
            $full_source = $source."/".$rel_path;
        }



        $full_dest = $dest;

        if (!empty($full_dest)) {
            $full_dest = $dest."/".$rel_path;
        }



        if (!is_dir($full_source)) {
            self::throwError("The source directroy: '$full_source' not exists.");
        }



        if (!is_dir($full_dest)) {
            mkdir($full_dest);
        }



        $files = scandir($full_source);

        foreach ($files as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }



            $path_source = $full_source."/".$file;

            $path_dest = $full_dest."/".$file;



            //validate black list

            $rel_path_file = $file;

            if (!empty($rel_path)) {
                $rel_path_file = $rel_path."/".$file;
            }



            //if the file or folder is in black list - pass it

            if (array_search($rel_path_file, $blackList) !== false) {
                continue;
            }



            //if file - copy file

            if (is_file($path_source)) {
                copy($path_source, $path_dest);
            } else {        //if directory - recursive copy directory

                if (empty($rel_path)) {
                    $rel_path_new = $file;
                } else {
                    $rel_path_new = $rel_path."/".$file;
                }



                self::copyDir($source, $dest, $rel_path_new, $blackList);
            }
        }
    }
    public static function getTextIntro($text, $limit)
    {
        $arrIntro = explode(' ', $text, $limit);



        if (count($arrIntro)>=$limit) {
            array_pop($arrIntro);

            $intro = implode(" ", $arrIntro);

            $intro = trim($intro);

            if (!empty($intro)) {
                $intro .= '...';
            }
        } else {
            $intro = implode(" ", $arrIntro);
        }



        $intro = preg_replace('`\[[^\]]*\]`', '', $intro);

        return($intro);
    }



    /**
     * add missing px/% to value, do also for object and array
     * @since: 5.0
     **/
    public static function addMissingVal($obj, $set_to = 'px')
    {
        if (is_array($obj)) {
            foreach ($obj as $key => $value) {
                if (strpos($value, $set_to) === false) {
                    $obj[$key] = $value.$set_to;
                }
            }
        } elseif (is_object($obj)) {
            foreach ($obj as $key => $value) {
                if (strpos($value, $set_to) === false) {
                    $obj->$key = $value.$set_to;
                }
            }
        } else {
            if (strpos($obj, $set_to) === false) {
                $obj .= $set_to;
            }
        }

        return $obj;
    }


    /**
     * normalize object with device informations depending on what is enabled for the Slider
     * @since: 5.0
     **/
    public static function normalizeDeviceSettings($obj, $enabled_devices, $return = 'obj', $set_to_if = array())
    { //array -> from -> to
        /*desktop
        notebook
        tablet
        mobile*/

        if (!empty($set_to_if)) {
            foreach ($obj as $key => $value) {
                foreach ($set_to_if as $from => $to) {
                    if (trim($value) == $from) {
                        $obj->$key = $to;
                    }
                }
            }
        }

        $inherit_size = self::getBiggestDeviceSetting($obj, $enabled_devices);
        if ($enabled_devices['desktop'] == 'on') {
            if (!@RevsliderPrestashop::getIsset($obj->desktop) || $obj->desktop === '') {
                $obj->desktop = $inherit_size;
            } else {
                $inherit_size = $obj->desktop;
            }
        } else {
            $obj->desktop = $inherit_size;
        }

        if ($enabled_devices['notebook'] == 'on') {
            if (!@RevsliderPrestashop::getIsset($obj->notebook) || $obj->notebook === '') {
                $obj->notebook = $inherit_size;
            } else {
                $inherit_size = $obj->notebook;
            }
        } else {
            $obj->notebook = $inherit_size;
        }

        if ($enabled_devices['tablet'] == 'on') {
            if (!@RevsliderPrestashop::getIsset($obj->tablet) || $obj->tablet === '') {
                $obj->tablet = $inherit_size;
            } else {
                $inherit_size = $obj->tablet;
            }
        } else {
            $obj->tablet = $inherit_size;
        }

        if ($enabled_devices['mobile'] == 'on') {
            if (!@RevsliderPrestashop::getIsset($obj->mobile) || $obj->mobile === '') {
                $obj->mobile = $inherit_size;
            } else {
                $inherit_size = $obj->mobile;
            }
        } else {
            $obj->mobile = $inherit_size;
        }

        switch ($return) {
            case 'obj':
                //order according to: desktop, notebook, tablet, mobile
                $new_obj = new stdClass();
                $new_obj->desktop = $obj->desktop;
                $new_obj->notebook = $obj->notebook;
                $new_obj->tablet = $obj->tablet;
                $new_obj->mobile = $obj->mobile;
                return $new_obj;
                break;
            case 'html-array':
                if ($obj->desktop === $obj->notebook && $obj->desktop === $obj->mobile && $obj->desktop === $obj->tablet) {
                    return $obj->desktop;
                } else {
                    return "['".@$obj->desktop."','".@$obj->notebook."','".@$obj->tablet."','".@$obj->mobile."']";
                }
                break;
        }

        return $obj;
    }


    /**
     * return biggest value of object depending on which devices are enabled
     * @since: 5.0
     **/
    public static function getBiggestDeviceSetting($obj, $enabled_devices)
    {
        if ($enabled_devices['desktop'] == 'on') {
            if (@RevsliderPrestashop::getIsset($obj->desktop) && $obj->desktop != '') {
                return $obj->desktop;
            }
        }

        if ($enabled_devices['notebook'] == 'on') {
            if (@RevsliderPrestashop::getIsset($obj->notebook) && $obj->notebook != '') {
                return $obj->notebook;
            }
        }

        if ($enabled_devices['tablet'] == 'on') {
            if (@RevsliderPrestashop::getIsset($obj->tablet) && $obj->tablet != '') {
                return $obj->tablet;
            }
        }

        if ($enabled_devices['mobile'] == 'on') {
            if (@RevsliderPrestashop::getIsset($obj->mobile) && $obj->mobile != '') {
                return $obj->mobile;
            }
        }

        return '';
    }


    /**
     * change hex to rgba
     */
    public static function hex2rgba($hex, $transparency = false)
    {
        if ($transparency !== false) {
            $transparency = ($transparency > 0) ? number_format(($transparency / 100), 2, ".", "") : 0;
        } else {
            $transparency = 1;
        }

        $hex = str_replace("#", "", $hex);

        if (Tools::strlen($hex) == 3) {
            $r = hexdec(Tools::substr($hex, 0, 1).Tools::substr($hex, 0, 1));
            $g = hexdec(Tools::substr($hex, 1, 1).Tools::substr($hex, 1, 1));
            $b = hexdec(Tools::substr($hex, 2, 1).Tools::substr($hex, 2, 1));
        } elseif (self::isrgb($hex)) {
            return $hex;
        } else {
            $r = hexdec(Tools::substr($hex, 0, 2));
            $g = hexdec(Tools::substr($hex, 2, 2));
            $b = hexdec(Tools::substr($hex, 4, 2));
        }

        return 'rgba('.$r.', '.$g.', '.$b.', '.$transparency.')';
    }


    public static function isrgb($rgba)
    {
        if (strpos($rgba, 'rgb') !== false) {
            return true;
        }

        return false;
    }


    /**
     * change rgba to hex
     * @since: 5.0
     */
    public static function rgba2hex($rgba)
    {
        if (Tools::strtolower($rgba) == 'transparent') {
            return $rgba;
        }

        $temp = explode(',', $rgba);
        $rgb = array();
        if (count($temp) == 4) {
            unset($temp[3]);
        }
        foreach ($temp as $val) {
            $t = dechex(preg_replace('/[^\d.]/', '', $val));
            if (Tools::strlen($t) < 2) {
                $t = '0'.$t;
            }
            $rgb[] = $t;
        }

        return '#'.implode('', $rgb);
    }


    /**
     * get transparency from rgba
     * @since: 5.0
     */
    public static function getTransFromRgba($rgba, $in_percent = false)
    {
        if (Tools::strtolower($rgba) == 'transparent') {
            return 100;
        }

        $temp = explode(',', $rgba);
        if (count($temp) == 4) {
            return ($in_percent) ? preg_replace('/[^\d.]/', '', $temp[3]) : preg_replace('/[^\d.]/', "", $temp[3]) * 100;
        }
        return 100;
    }


    public static function getResponsiveSize($slider)
    {
        $operations = new RevSliderOperations();
        $arrValues = $operations->getGeneralSettingsValues();

        $enable_custom_size_notebook = $slider->slider->getParam('enable_custom_size_notebook', 'off');
        $enable_custom_size_tablet = $slider->slider->getParam('enable_custom_size_tablet', 'off');
        $enable_custom_size_iphone = $slider->slider->getParam('enable_custom_size_iphone', 'off');
        $adv_resp_sizes = ($enable_custom_size_notebook == 'on' || $enable_custom_size_tablet == 'on' || $enable_custom_size_iphone == 'on') ? true : false;

        if ($adv_resp_sizes == true) {
            $width = $slider->slider->getParam("width", 1240, RevSlider::FORCE_NUMERIC);
            $width .= ','. $slider->slider->getParam("width_notebook", 1024, RevSlider::FORCE_NUMERIC);
            $width .= ','. $slider->slider->getParam("width_tablet", 778, RevSlider::FORCE_NUMERIC);
            $width .= ','. $slider->slider->getParam("width_mobile", 480, RevSlider::FORCE_NUMERIC);
            $height = $slider->slider->getParam("height", 868, RevSlider::FORCE_NUMERIC);
            $height .= ','. $slider->slider->getParam("height_notebook", 768, RevSlider::FORCE_NUMERIC);
            $height .= ','. (int)($slider->slider->getParam("height_tablet", 960, RevSlider::FORCE_NUMERIC));
            $height .= ','. (int)($slider->slider->getParam("height_mobile", 720, RevSlider::FORCE_NUMERIC));

            $responsive = (@RevsliderPrestashop::getIsset($arrValues['width'])) ? $arrValues['width'] : '1240';
            $def = (@RevsliderPrestashop::getIsset($arrValues['width'])) ? $arrValues['width'] : '1240';

            $responsive.= ',';
            if ($enable_custom_size_notebook == 'on') {
                $responsive.= (@RevsliderPrestashop::getIsset($arrValues['width_notebook'])) ? $arrValues['width_notebook'] : '1024';
                $def = (@RevsliderPrestashop::getIsset($arrValues['width_notebook'])) ? $arrValues['width_notebook'] : '1024';
            } else {
                $responsive.= $def;
            }
            $responsive.= ',';
            if ($enable_custom_size_tablet == 'on') {
                $responsive.= (@RevsliderPrestashop::getIsset($arrValues['width_tablet'])) ? $arrValues['width_tablet'] : '778';
                $def = (@RevsliderPrestashop::getIsset($arrValues['width_tablet'])) ? $arrValues['width_tablet'] : '778';
            } else {
                $responsive.= $def;
            }
            $responsive.= ',';
            if ($enable_custom_size_iphone == 'on') {
                $responsive.= (@RevsliderPrestashop::getIsset($arrValues['width_mobile'])) ? $arrValues['width_mobile'] : '480';
                $def = (@RevsliderPrestashop::getIsset($arrValues['width_mobile'])) ? $arrValues['width_mobile'] : '480';
            } else {
                $responsive.= $def;
            }

            return array(
                'level' => $responsive,
                'height' => $height,
                'width' => $width
            );
        } else {
            $responsive = (@RevsliderPrestashop::getIsset($arrValues['width'])) ? $arrValues['width'] : '1240';
            $def = (@RevsliderPrestashop::getIsset($arrValues['width'])) ? $arrValues['width'] : '1240';
            $responsive.= ',';
            $responsive.= (@RevsliderPrestashop::getIsset($arrValues['width_notebook'])) ? $arrValues['width_notebook'] : '1024';
            $responsive.= ',';
            $responsive.= (@RevsliderPrestashop::getIsset($arrValues['width_tablet'])) ? $arrValues['width_tablet'] : '778';
            $responsive.= ',';
            $responsive.= (@RevsliderPrestashop::getIsset($arrValues['width_mobile'])) ? $arrValues['width_mobile'] : '480';

            return array(
                'visibilitylevel' => $responsive,
                'height' => $slider->slider->getParam("height", "868", RevSlider::FORCE_NUMERIC),
                'width' => $slider->slider->getParam("width", "1240", RevSlider::FORCE_NUMERIC)
            );
        }
    }
}

    
/**
* New class name as fallback
*/
// @codingStandardsIgnoreStart
class RevSliderFunctions extends UniteFunctionsRev
{
    // @codingStandardsIgnoreEnd
}
