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

class UniteBaseAdminClassRev extends UniteBaseClassRev
{

    const ACTION_ADMIN_MENU = "admin_menu";
    const ACTION_ADMIN_INIT = "admin_init";
    const ACTION_ADD_SCRIPTS = "admin_enqueue_scripts";
    const ACTION_ADD_METABOXES = "add_meta_boxes";
    const ACTION_SAVE_POST = "save_post";
    const ROLE_ADMIN = "admin";
    const ROLE_EDITOR = "editor";
    const ROLE_AUTHOR = "author";

    protected static $master_view;
    protected static $view;
    private static $arrSettings = array();
    private static $arrMenuPages = array();
    private static $tempVars = array();
    private static $startupError = "";
    private static $menuRole = self::ROLE_ADMIN;
    private static $arrMetaBoxes = "";

    public function __construct($mainFile, $t, $defaultView)
    {
        parent::__construct($mainFile, $t);

        //set view

        self::$view = self::getGetVar("view");

        if (empty(self::$view)) {
            self::$view = $defaultView;
        }



        //add internal hook for adding a menu in arrMenus

        self::addAction(self::ACTION_ADMIN_MENU, "addAdminMenu");


        //if not inside plugin don't continue
        //if($this->isInsidePlugin() == true){

        self::addAction(self::ACTION_ADD_SCRIPTS, "addCommonScripts");

        self::addAction(self::ACTION_ADD_SCRIPTS, "onAddScripts");

        //}
        

        self::addActionAjax("ajax_action", "onAjaxAction");

        self::addActionAjax("show_image", "onShowImage");
    }

    public static function sdsInitErrorWarning()
    {
        if (!(int) Configuration::get('PS_SHOP_ENABLE')) {
            echo "<div class='alert alert-warning'>Maintenance mode is enabled. This may cause functional problem at your slider revolution module.</div>";
            if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))) {
                echo "<div class='alert alert-warning'>It's seemed that your IP is not present in Maintenance IP.</div>";
            }
        }

        if (get_magic_quotes_gpc()) {
            echo "<div class='alert alert-warning'>magic_quotes_gpc is enabled. This may cause functional problem at your slider revolution module. Please disable magic_quotes_gpc.</div>";
        }
        if (get_magic_quotes_runtime()) {
            echo "<div class='alert alert-warning'>magic_quotes_runtime is enabled. This may cause functional problem at your slider revolution module. Please disable magic_quotes_runtime.</div>";
        }
        if (!defined('ABSPATH')) {
            echo "<div class='alert alert-warning'>Fatal Error: 'ABSPATH' isn't defined.</div>";
            return;
        }
        if (!is_writable(ABSPATH . '/uploads')) {
            echo "<div class='alert alert-warning'>'" . ABSPATH . "/uploads' folder is not writeable. Change the folder permission.</div>";
        }
        if (!is_writable(ABSPATH . '/views/css/rs-plugin/css')) {
            echo "<div class='alert alert-warning'>'" . ABSPATH . "/views/css/rs-plugin/css' folder is not writeable. Change the folder permission.</div>";
        }
//                    if(!is_executable(ABSPATH.'/rs-plugin/fileuploader/uploadify.php')){
//                        echo "<div class='alert alert-warning'>'".ABSPATH."/rs-plugin/fileuploader/uploadify.php' file is not executable. Change the file permission.</div>";
//                    }   
        if (!is_writable(ABSPATH . '/cache')) {
            echo "<div class='alert alert-warning'>'" . ABSPATH . "/cache' folder is not writeable. Change the folder permission.</div>";
        }
    }

    public static function deleteUploadedFile($data)
    {
        if (!@RevsliderPrestashop::getIsset(self::$wpdb)) {
            $wpdb = RevsliderPrestashop::$wpdb;
        } else {
            $wpdb = self::$wpdb;
        }



        $imgdir = ABSPATH . '/uploads/';

        if (@RevsliderPrestashop::getIsset($data['img']) && !empty($data['img']) && file_exists("{$imgdir}/{$data['img']}")) {
            $filename = $data['img'];

            $thumbsize = GlobalsRevSlider::IMAGE_SIZE_THUMBNAIL;

            $mediumsize = GlobalsRevSlider::IMAGE_SIZE_MEDIUM;

            $largesize = GlobalsRevSlider::IMAGE_SIZE_LARGE;

            $filerealname = Tools::substr($filename, 0, strrpos($filename, '.'));

            $fileext = Tools::substr($filename, strrpos($filename, '.'), Tools::strlen($filename) - Tools::strlen($filerealname));



            $images = array($filename);

            $images[] = "{$filerealname}-{$thumbsize}x{$thumbsize}{$fileext}";

            $images[] = "{$filerealname}-{$mediumsize}x{$mediumsize}{$fileext}";

            $images[] = "{$filerealname}-{$largesize}x{$largesize}{$fileext}";

            foreach ($images as $image) {
                @unlink("{$imgdir}{$image}");
            }



            $tablename = $wpdb->prefix . GlobalsRevSlider::TABLE_ATTACHMENT_IMAGES;

            if ($wpdb->query("DELETE FROM {$tablename} WHERE file_name='{$filename}'")) {
                echo Tools::jsonEncode(array(
                    'success' => '1',
                    'output' => self::getUploadedFilesMarkup(self::getUploadedFilesResult())
                ));
            }

            die();
        }
    }

    public static function getLatestUploadedImage()
    {
        if (!@RevsliderPrestashop::getIsset(self::$wpdb)) {
            $wpdb = RevsliderPrestashop::$wpdb;
        } else {
            $wpdb = self::$wpdb;
        }

        $tablename = $wpdb->prefix . GlobalsRevSlider::TABLE_ATTACHMENT_IMAGES;


        $latest = $wpdb->getVar("SELECT file_name FROM {$tablename} ORDER BY ID DESC");

        if (!empty($latest)) {
            return $latest;
        }



        return '';
    }

    public static function getUploadedFilesJson()
    {
        echo Tools::jsonEncode(array(
            'success' => '1',
            'latest' => self::getLatestUploadedImage(),
            'output' => self::getUploadedFilesMarkup(self::getUploadedFilesResult())
        ));

        die();
    }

    public static function getUploadedFilesResult($per_page = 30, $start = 0)
    {
        if (!@RevsliderPrestashop::getIsset(self::$wpdb)) {
            $wpdb = RevsliderPrestashop::$wpdb;
        } else {
            $wpdb = self::$wpdb;
        }



        $tablename = $wpdb->prefix . GlobalsRevSlider::TABLE_ATTACHMENT_IMAGES;

        $db_results = $wpdb->getResults("SELECT * FROM {$tablename} ORDER BY ID DESC LIMIT {$start},{$per_page}");


        $imgdir = ABSPATH . '/uploads/';

        $results = array();



        if (is_dir($imgdir) && !empty($db_results)) {
            foreach ($db_results as $dres) {
                $dres = (object) $dres;

                if (@RevsliderPrestashop::getIsset($dres->file_name) &&
                    !empty($dres->file_name) &&
                    file_exists($imgdir . $dres->file_name)) {
                    $results["{$dres->ID}"] = $dres->file_name;
                }
            }
        }

        //print_r($results);

        return $results;
    }

    public static function getUploadedFilesMarkup($results = array())
    {
        $lan_iso = Context::getcontext()->language->iso_code;
        include_once(_PS_ROOT_DIR_ . '/modules/revsliderprestashop/views/config/config.php');
        include_once(_PS_ROOT_DIR_ . '/modules/revsliderprestashop/views/include/utils.php');

        $upload_dir = __PS_BASE_URI__ . 'modules/revsliderprestashop/uploads/';

        $current_path = _PS_ROOT_DIR_ . '/modules/revsliderprestashop/uploads/';

        $url = uploads_url();

        ob_start();

        if (!empty($results)):

            echo '<div id="divImageList"><ul id="selectable" class="">';

            $num = 0;

            foreach ($results as $id => $filename):

                $thumbsize = GlobalsRevSlider::IMAGE_SIZE_THUMBNAIL;

                $mediumsize = GlobalsRevSlider::IMAGE_SIZE_MEDIUM;

                $largesize = GlobalsRevSlider::IMAGE_SIZE_LARGE;

                $filerealname = Tools::substr($filename, 0, strrpos($filename, '.'));

                $fileext = Tools::substr($filename, strrpos($filename, '.'), Tools::strlen($filename) - Tools::strlen($filerealname));

                $thumbimg = $img = "{$filerealname}-{$thumbsize}x{$thumbsize}{$fileext}";

                $mediumimg = "{$filerealname}-{$mediumsize}x{$mediumsize}{$fileext}";

                $largeimg = "{$filerealname}-{$largesize}x{$largesize}{$fileext}";
                $file_path = $file_path = $current_path . $largeimg;

                $date = filemtime($file_path);
                $size = filesize($file_path);

                $file_infos = pathinfo($file_path);
                $file_ext = $file_infos['extension'];

                $extension_lower = Tools::strtolower($file_ext);

                $is_img = true;

                list($img_width, $img_height, $img_type, $attr) = getimagesize($file_path);

                echo '<li data-image="'.$filename.'" data-image-id="'.$id.'" data-large="'.$upload_dir . $img.'" data-medium="'.$upload_dir . $img.'" data-thumb="'.$upload_dir . $img.'" class="ff-item-type-2 file">';
                echo '<figure data-type="img" data-name="'.$filerealname.'">';
                echo '<a data-function="apply" data-field_id="'.$id.'" data-file="'.$upload_dir . $img .'" class="link-img" href="javascript:void(\'\')">';
                echo '<div class="img-precontainer">';
                echo '<div class="img-container">';
                echo '<span></span>';
                echo '<img alt="'.$img.'" src="'.$upload_dir . $img .'"  class="original " />';
                echo '</div>';
                echo '</div>';
                echo '<div class="img-precontainer-mini original-thumb">';
                echo '<div class="filetype png hide">png</div>';
                echo '<div class="img-container-mini">';
                echo '<span></span>';
                echo '<img src="'.$upload_dir . $img .'" class=" " alt="'.$img.' thumbnails" />';
                echo '</div>';
                echo '</div>';
                echo '</a>';

                echo '<div class="box">';
                echo '<h4 class="ellipsis">';
                echo '<a data-function="apply" data-field_id="" data-file="'.$img.'" class="link" href="javascript:void(\'\')">';
                echo $img.'</a></h4>';
                echo '</div>';

                $date = filemtime($current_path . $img);

                echo '<input type="hidden" class="date" value="'.$date.'"/>';
                echo '<input type="hidden" class="size" value="'.$size.'"/>';
                echo '<input type="hidden" class="extension" value="'.$extension_lower.'"/>';
                echo '<input type="hidden" class="name" value=""/>';

                echo '<div class="file-date">'.date('Y-m-d H:i:s', $date).'</div>';
                echo '<div class="file-size">'.makeSize($size).'</div>';
                echo '<div class="img-dimension">';
                if ($is_img) {
                    echo $img_width . "x" . $img_height;
                }
                echo '</div>';
                echo '<div class="file-extension">'.Tools::safeOutput($extension_lower).'</div>';

                echo '<figcaption>';
                echo '</figcaption>';
                echo '</figure>';
                echo '</li>';

            endforeach;

            echo '</ul></div>';

        endif;

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    public static function setMenuRole($menuRole)
    {
        self::$menuRole = $menuRole;
    }

    public static function setStartupError($errorMessage)
    {
        self::$startupError = $errorMessage;
    }

    private function isInsidePlugin()
    {
        $page = self::getGetVar("page");

        if ($page == self::$dir_plugin) {
            return(true);
        }

        return(false);
    }

    public static function addCommonScripts()
    {
        if (GlobalsRevSlider::$isNewVersion) {
            
        } else {
            
        }
    }

    public static function adminPages()
    {
        
    }

    protected static function isAdminPermissions()
    {
        if (is_admin()) {
            return(true);
        }



        return(false);
    }

    protected static function validateAdminPermissions()
    {
        if (!self::isAdminPermissions()) {
            echo "access denied";

            return(false);
        }
    }

    protected static function setMasterView($masterView)
    {
        self::$master_view = $masterView;
    }

    protected static function requireView($view)
    {
        try {

            //require master view file, and
            RevGlobalObject::setVar('view', $view);
            if (!empty(self::$master_view) && !@RevsliderPrestashop::getIsset(self::$tempVars["is_masterView"])) {
                $masterViewFilepath = self::$path_views . self::$master_view . ".php";

                UniteFunctionsRev::validateFilepath($masterViewFilepath, "Master View");



                self::$tempVars["is_masterView"] = true;

                require $masterViewFilepath;
            } else {  //simple require the view file.
                $viewFilepath = self::$path_views . $view . ".php";



                UniteFunctionsRev::validateFilepath($viewFilepath, "View");

                require $viewFilepath;
            }
        } catch (Exception $e) {
            echo "<br><br>View ($view) Error: <b>" . $e->getMessage() . "</b>";



            if (self::$debugMode == true) {
                dmp($e->getTraceAsString());
            }
        }
    }

    protected static function getPathTemplate($templateName)
    {
        $pathTemplate = self::$path_templates . $templateName . ".php";

        UniteFunctionsRev::validateFilepath($pathTemplate, "Template");

        return($pathTemplate);
    }

    public static function requireSettings($settingsFile)
    {
        try {
            require self::$path_plugin . "settings/$settingsFile.php";
        } catch (Exception $e) {
            echo "<br><br>Settings ($settingsFile) Error: <b>" . $e->getMessage() . "</b>";

            dmp($e->getTraceAsString());
        }
    }

    protected static function getSettingsFilePath($settingsFile)
    {
        $filepath = self::$path_plugin . "settings/$settingsFile.php";

        return($filepath);
    }

    protected static function addMediaUploadIncludes()
    {
        self::addWPScript("thickbox");

        self::addWPStyle("thickbox");

        self::addWPScript("media-upload");
    }

    public static function addAdminMenu()
    {
        $role = "manage_options";

        switch (self::$menuRole) {

            case self::ROLE_AUTHOR:

                $role = "edit_published_posts";

                break;

            case self::ROLE_EDITOR:

                $role = "edit_pages";

                break;

            default:

            case self::ROLE_ADMIN:

                $role = "manage_options";

                break;
        }

        //// var_dump(self::$arrMenuPages);

        foreach (self::$arrMenuPages as $menu) {
            $title = $menu["title"];

            $pageFunctionName = $menu["pageFunction"];


            call_user_func(array(self::$t, $pageFunctionName));
        }
    }

    protected static function addMenuPage($title, $pageFunctionName)
    {
        self::$arrMenuPages[] = array("title" => $title, "pageFunction" => $pageFunctionName);
    }

    public static function getViewUrl($viewName, $urlParams = "")
    {
        $params = "&view=" . $viewName;
        if (!empty($urlParams)) {
            $params .= "&" . $urlParams;
        }
        if (Tools::isSubmit('returnurl')) {
            $link = Tools::getValue('returnurl');
            $links = explode('&view=', $link);
            $link = $links[0];
            $link .= "&view={$viewName}";
            $link .= "&{$urlParams}";
        } else {
            $link = admin_url("admin.php?page=" . self::$dir_plugin . $params);
        }
        return htmlspecialchars_decode(urldecode($link));
    }

    protected static function storeSettings($key, $settings)
    {
        self::$arrSettings[$key] = $settings;
    }

    protected static function getSettings($key)
    {
        if (!@RevsliderPrestashop::getIsset(self::$arrSettings[$key])) {
            UniteFunctionsRev::throwError("Settings $key not found");
        }

        $settings = self::$arrSettings[$key];

        return($settings);
    }

    protected static function addActionAjax($ajaxAction, $eventFunction)
    {
        self::addAction('wp_ajax_' . self::$dir_plugin . "_" . $ajaxAction, $eventFunction);

        self::addAction('wp_ajax_nopriv_' . self::$dir_plugin . "_" . $ajaxAction, $eventFunction);
    }

    /**
     * 
     * echo json ajax response
     */
    private static function ajaxResponse($success, $message, $arrData = null)
    {
        $response = array();
        $response["success"] = $success;
        $response["message"] = $message;

        if (!empty($arrData)) {
            if (gettype($arrData) == "string") {
                $arrData = array("data" => $arrData);
            }

            $response = array_merge($response, $arrData);
        }

        $json = Tools::jsonEncode($response);

        echo $json;
        exit();
    }

    protected static function ajaxResponseData($arrData)
    {
        if (gettype($arrData) == "string") {
            $arrData = array("data" => $arrData);
        }



        self::ajaxResponse(true, "", $arrData);
    }

    protected static function ajaxResponseError($message, $arrData = null)
    {
        self::ajaxResponse(false, $message, $arrData, true);
    }

    protected static function ajaxResponseSuccess($message, $arrData = null)
    {
        self::ajaxResponse(true, $message, $arrData, true);
    }

    protected static function ajaxResponseSuccessRedirect($message, $url)
    {
        $arrData = array("is_redirect" => true, "redirect_url" => $url);



        self::ajaxResponse(true, $message, $arrData, true);
    }
}
