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

class RevSliderAdmin extends UniteBaseAdminClassRev
{

    const DEFAULT_VIEW = "sliders";
    const VIEW_SLIDER = "slider";
    const VIEW_SLIDER_TEMPLATE = "slider_template";
    const VIEW_SLIDERS = "sliders";
    const VIEW_SLIDES = "slides";
    const VIEW_SLIDE = "slide";

    public function __construct($mainFilepath, $view = true)
    {
        self::addMenuPage('Revolution Slider', "adminPages");

        if ($view) {
            parent::__construct($mainFilepath, $this, self::DEFAULT_VIEW);
        } else {
            parent::__construct($mainFilepath, $this, '');
        }
        $path_view = self::$path_plugin . 'views/css/';
        //set table names

        GlobalsRevSlider::$table_sliders = self::$table_prefix . GlobalsRevSlider::TABLE_SLIDERS_NAME;

        GlobalsRevSlider::$table_slides = self::$table_prefix . GlobalsRevSlider::TABLE_SLIDES_NAME;

        GlobalsRevSlider::$table_static_slides = self::$table_prefix . GlobalsRevSlider::TABLE_STATIC_SLIDES_NAME;

        GlobalsRevSlider::$table_settings = self::$table_prefix . GlobalsRevSlider::TABLE_SETTINGS_NAME;

        GlobalsRevSlider::$table_css = self::$table_prefix . GlobalsRevSlider::TABLE_CSS_NAME;

        GlobalsRevSlider::$table_layer_anims = self::$table_prefix . GlobalsRevSlider::TABLE_LAYER_ANIMS_NAME;

        GlobalsRevSlider::$table_navigation = self::$table_prefix . GlobalsRevSlider::TABLE_NAVIGATION_NAME;

        GlobalsRevSlider::$table_options = self::$table_prefix . GlobalsRevSlider::TABLE_REVSLIDER_OPTIONS_NAME;

        GlobalsRevSlider::$filepath_backup = $path_view . "backup/";

        GlobalsRevSlider::$filepath_captions = $path_view . "rs-plugin/css/captions.css";

        GlobalsRevSlider::$urlCaptionsCSS = Context::getContext()->link->getAdminLink('AdminRevolutionsliderAjax') . '&revControllerAction=captions';

        GlobalsRevSlider::$urlStaticCaptionsCSS = $path_view . "rs-plugin/css/static-captions.css";

        GlobalsRevSlider::$filepath_dynamic_captions = $path_view . "rs-plugin/css/dynamic-captions.css";

        GlobalsRevSlider::$filepath_static_captions = $path_view . "rs-plugin/css/static-captions.css";

        GlobalsRevSlider::$filepath_captions_original = $path_view . "rs-plugin/css/captions-original.css";

        GlobalsRevSlider::$urlExportZip = self::$path_plugin . "export.zip";

        $this->init();
    }

    private function init()
    {
        self::requireSettings("general_settings");

        $generalSettings = self::getSettings("general");

        $role = $generalSettings->getSettingValue("role", UniteBaseAdminClassRev::ROLE_ADMIN);

        self::setMenuRole($role);

        $action = self::getPostGetVar("client_action");

        $data = self::getPostGetVar("data");

        $ajax_action = self::getPostGetVar("action");



        if (!empty($action) or ! empty($data)) {
            self::onAjaxAction();
        } elseif (!empty($ajax_action)) {
            if (@RevsliderPrestashop::getIsset(self::$actions['wp_ajax_' . $ajax_action]) &&
                !empty(self::$actions['wp_ajax_' . $ajax_action])) {
                foreach (self::$actions['wp_ajax_' . $ajax_action] as $callback) {
                    call_user_func(array(__CLASS__, $callback));
                }
            }
        } else {
            if (!empty(self::$view)) {
                if (self::$view != 'fileupload') {

                    if (@RevsliderPrestashop::getIsset(self::$actions['admin_enqueue_scripts']) && !empty(self::$actions['admin_enqueue_scripts'])) {
                        foreach (self::$actions['admin_enqueue_scripts'] as $callback) {
                            call_user_func(array(__CLASS__, $callback));
                        }
                    }
                }


                if (@RevsliderPrestashop::getIsset(self::$actions['admin_menu']) && !empty(self::$actions['admin_menu'])) {
                    foreach (self::$actions['admin_menu'] as $admin_menu_actions) {
                        call_user_func(array(__CLASS__, $admin_menu_actions));
                    }
                }
            }
        }
    }

    public static function customPostFieldsOutput(UniteSettingsProductSidebarRev $output)
    {

        echo '<ul class="revslider_settings">';

        $output->drawSettingsByNames("slide_template");

        echo '</ul>';
    }

    public static function onActivate()
    {
        $rt = self::createDBTables();

        RevSliderPluginUpdate::addV5Styles();

        return $rt;
    }

    public static function createDBTables()
    {
        $res = self::createTable(GlobalsRevSlider::TABLE_SLIDERS_NAME);

        $res &= self::createTable(GlobalsRevSlider::TABLE_SLIDES_NAME);

        $res &= self::createTable(GlobalsRevSlider::TABLE_STATIC_SLIDES_NAME);

        $res &= self::createTable(GlobalsRevSlider::TABLE_SETTINGS_NAME);

        $res &= self::createTable(GlobalsRevSlider::TABLE_CSS_NAME);

        $res &= self::createTable(GlobalsRevSlider::TABLE_LAYER_ANIMS_NAME);

        $res &= self::createTable(GlobalsRevSlider::TABLE_REVSLIDER_OPTIONS_NAME);

        $res &= self::createTable(GlobalsRevSlider::TABLE_ATTACHMENT_IMAGES);

        $res &= self::createTable(GlobalsRevSlider::TABLE_NAVIGATION_NAME);



        return $res;
    }

    public static function deleteDBTables()
    {
        $res = self::deleteDBTable(GlobalsRevSlider::TABLE_SLIDERS_NAME);

        $res &= self::deleteDBTable(GlobalsRevSlider::TABLE_SLIDES_NAME);

        $res &= self::deleteDBTable(GlobalsRevSlider::TABLE_SETTINGS_NAME);
        $res &= self::deleteDBTable(GlobalsRevSlider::TABLE_STATIC_SLIDES_NAME);

        $res &= self::deleteDBTable(GlobalsRevSlider::TABLE_CSS_NAME);

        $res &= self::deleteDBTable(GlobalsRevSlider::TABLE_LAYER_ANIMS_NAME);

        $res &= self::deleteDBTable(GlobalsRevSlider::TABLE_REVSLIDER_OPTIONS_NAME);

        $res &= self::deleteDBTable(GlobalsRevSlider::TABLE_ATTACHMENT_IMAGES);

        $res &= self::deleteDBTable(GlobalsRevSlider::TABLE_NAVIGATION_NAME);

        return $res;
    }

    public static function checkCopyCaptionsCSS()
    {
        if (file_exists(GlobalsRevSlider::$filepath_captions) == false) {
            copy(GlobalsRevSlider::$filepath_captions_original, GlobalsRevSlider::$filepath_captions);
        }

        if (!file_exists(GlobalsRevSlider::$filepath_captions) == true) {
            self::setStartupError("Can't copy <b>captions-original.css </b> to <b>captions.css</b> in <b> plugins/revslider/rs-plugin/css </b> folder. Please try to copy the file by hand or turn to support.");
        }
    }

    public static function enqueueFileUploaderScripts()
    {
        $html = '';
        $js_uri = array();
        $css_uri = array();
        $css_uri[] = self::$url_plugin . "/rs-plugin/fileuploader/uploadify.css";
        $css_uri[] = self::$url_plugin . "/css/bootstrap.min.css";
        $css_uri[] = self::$url_plugin . "/css/jui/new/jquery-ui-1.10.3.custom.css";
        $js_uri[] = self::$url_plugin . 'js/admin.js';
        $js_uri[] = self::$url_plugin . 'js/jquery-ui/jquery-ui-1.10.3.custom.js';
        $js_uri[] = self::$url_plugin . 'rs-plugin/fileuploader/jquery.uploadify.min.js';
        $js_uri[] = self::$url_plugin . 'js/bootstrap.min.js';
        foreach ($css_uri as $css) {
            $html .= '<link href="' . $css . '" rel="stylesheet" type="text/css"/>';
        }
        foreach ($js_uri as $js) {
            $html .= '<script type="text/javascript" src="' . $js . '"></script>';
        }
        return $html;
    }

    public static function onAddScripts()
    {
        self::addStyle("edit_layers", "edit_layers");

        self::addMediaUploadIncludes();
    }

    public static function adminPages()
    {
        parent::adminPages();

        rev_head();

        //require styles by view

        switch (self::$view) {

            case self::VIEW_SLIDERS:

            case self::VIEW_SLIDER:

            case self::VIEW_SLIDER_TEMPLATE:

                self::requireSettings("slider_settings");

                break;

            case self::VIEW_SLIDES:

                break;

            case self::VIEW_SLIDE:

                break;
        }

        self::setMasterView("master_view");

        self::requireView(self::$view);

        rev_footer();
    }
    /*

     * Remove Tables

     * 

     * 

     */

    public static function deleteDBTable($tableName)
    {
        if (!@RevsliderPrestashop::getIsset(self::$wpdb)) {
            $wpdb = RevsliderPrestashop::$wpdb;
        } else {
            $wpdb = self::$wpdb;
        }



        $tableName = $wpdb->prefix . $tableName;

        $sql = "DROP TABLE IF EXISTS {$tableName}";

        $q = $wpdb->query($sql);

        if ($q) {
            return true;
        }
    }

    public static function createTable($tableName)
    {
        $parseCssToDb = false;

//
//			$checkIfTableExists = $wpdb->getRow("SELECT COUNT(*) AS exist
//					FROM information_schema.tables
//					WHERE table_schema = '".DB_NAME."' 
//					AND table_name = '".self::$table_prefix.GlobalsRevSlider::TABLE_CSS_NAME."';");
//			if($checkIfTableExists->exist > 0){
//				//check if database is empty
//				$result = $wpdb->getRow("SELECT COUNT( DISTINCT id ) AS NumberOfEntrys FROM ".self::$table_prefix.GlobalsRevSlider::TABLE_CSS_NAME);
//				if($result->NumberOfEntrys == 0) $parseCssToDb = true;
//			}
//						
//			
//			//if table exists - don't create it.
//			
//			if(UniteFunctionsWPRev::isDBTableExists($tableRealName))
//				return(false);
//			
//			$charset_collate = '';
//					
//			if(method_exists($wpdb, "get_charset_collate"))
//				$charset_collate = $wpdb->get_charset_collate();
//			else{
//				if ( ! empty($wpdb->charset) )
//					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
//				if ( ! empty($wpdb->collate) )
//					$charset_collate .= " COLLATE $wpdb->collate";
//			}
//			

        if (!@RevsliderPrestashop::getIsset(self::$wpdb)) {
            $wpdb = RevsliderPrestashop::$wpdb;
        } else {
            $wpdb = self::$wpdb;
        }



        $tableRealName = $wpdb->prefix . $tableName;

        switch ($tableName) {

            case GlobalsRevSlider::TABLE_ATTACHMENT_IMAGES:



                $sql = "CREATE TABLE IF NOT EXISTS {$tableRealName}(

                            ID INT(10) NOT NULL AUTO_INCREMENT,

                            file_name VARCHAR(100) NOT NULL,

                            PRIMARY KEY (ID)

                        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";



                break;



            case GlobalsRevSlider::TABLE_SLIDERS_NAME:

                $sql = "CREATE TABLE IF NOT EXISTS " . $tableRealName . " (

							  id int(9) NOT NULL AUTO_INCREMENT,

							  title tinytext NOT NULL,

							  alias tinytext,

							  params MEDIUMTEXT NOT NULL,

							  settings MEDIUMTEXT NULL,

							  type varchar(191) NOT NULL,

							  PRIMARY KEY (id)

							) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

                break;

            case GlobalsRevSlider::TABLE_NAVIGATION_NAME:

                $sql = "CREATE TABLE IF NOT EXISTS " . $tableRealName . " (

							  id int(9) NOT NULL AUTO_INCREMENT,

							  name varchar(191) NOT NULL,

							  handle varchar(191) NOT NULL,

							  css MEDIUMTEXT NOT NULL,

							  markup MEDIUMTEXT NOT NULL,

							  settings MEDIUMTEXT,

							  PRIMARY KEY (id)

							) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

                break;

            case GlobalsRevSlider::TABLE_SLIDES_NAME:

                $sql = "CREATE TABLE IF NOT EXISTS " . $tableRealName . " (

								  id int(9) NOT NULL AUTO_INCREMENT,

								  slider_id int(9) NOT NULL,

								  slide_order int not NULL,	

								  params MEDIUMTEXT NOT NULL,

								  layers MEDIUMTEXT NOT NULL,

								  settings MEDIUMTEXT NOT NULL,

								  PRIMARY KEY (id)

								) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

                break;
            case GlobalsRevSlider::TABLE_STATIC_SLIDES_NAME:
                $sql = "CREATE TABLE IF NOT EXISTS " . $tableRealName . " (
								  id int(9) NOT NULL AUTO_INCREMENT,
								  slider_id int(9) NOT NULL,
								  params MEDIUMTEXT NOT NULL,
								  layers MEDIUMTEXT NOT NULL,
								  PRIMARY KEY (id)
								) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
                break;
            case GlobalsRevSlider::TABLE_SETTINGS_NAME:

                $sql = "CREATE TABLE IF NOT EXISTS " . $tableRealName . " (

								  id int(9) NOT NULL AUTO_INCREMENT,

								  general MEDIUMTEXT NOT NULL,

								  params MEDIUMTEXT NOT NULL,

								  PRIMARY KEY (id)

								) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

                break;

            case GlobalsRevSlider::TABLE_CSS_NAME:

                $sql = "CREATE TABLE IF NOT EXISTS " . $tableRealName . " (

								  id int(9) NOT NULL AUTO_INCREMENT,

								  handle TEXT NOT NULL,

								  settings MEDIUMTEXT,

								  hover MEDIUMTEXT,

								  params MEDIUMTEXT NOT NULL,

								  advanced MEDIUMTEXT,

								  PRIMARY KEY (id)

								) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

                $parseCssToDb = true;

                break;

            case GlobalsRevSlider::TABLE_LAYER_ANIMS_NAME:

                $sql = "CREATE TABLE IF NOT EXISTS " . $tableRealName . " (

								  id int(9) NOT NULL AUTO_INCREMENT,

								  handle TEXT NOT NULL,

								  params TEXT NOT NULL,

								  PRIMARY KEY (id)

								) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

                break;

            case GlobalsRevSlider::TABLE_REVSLIDER_OPTIONS_NAME:

                $sql = "CREATE TABLE IF NOT EXISTS " . $tableRealName . " (

								  id int(9) NOT NULL AUTO_INCREMENT,

								  name VARCHAR(32) NOT NULL,

								  value MEDIUMTEXT NOT NULL,

								  PRIMARY KEY (id)

								) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

                break;



            default:

                UniteFunctionsRev::throwError("table: $tableName not found");

                break;
        }

        $q = $wpdb->query($sql);

        return $q;
    }

    public static function sdsCaptionCssInit($parseCssToDb)
    {
        if ((bool) $parseCssToDb === true) {
            $revOperations = new RevOperations();
            $revOperations->importCaptionsCssContentArray();
            $revOperations->moveOldCaptionsCss();
            $revOperations->updateDynamicCaptions(true);
            return true;
        }
    }

    /**
     *
     * import slideer handle (not ajax response)
     */
    private static function importSliderHandle($viewBack = null, $updateAnim = true, $updateStatic = true, $updateNavigation = true)
    {
        dmp(__("importing slider settings and data...", 'revslider'));

        $slider = new RevSlider();
        $response = $slider->importSliderFromPost($updateAnim, $updateStatic, false, false, false, $updateNavigation);
        $sliderID = $response["sliderID"];

        if (empty($viewBack)) {
            $viewBack = self::getViewUrl(self::VIEW_SLIDER, "id=" . $sliderID);
            if (empty($sliderID)) {
                $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
            }
        }

        //handle error
        if ($response["success"] == false) {
            $message = $response["error"];
            dmp("<b>Error: " . $message . "</b>");
            echo RevSliderFunctions::getHtmlLink($viewBack, __("Go Back", 'revslider'));
        } else {    //handle success, js redirect.
            dmp(__("Slider Import Success, redirecting...", 'revslider'));
            echo "<script>location.href='$viewBack'</script>";
        }
        exit();
    }

    /**
     * Toggle Favorite State of Slider
     * @since: 5.0
     */
    public static function toggleFavoriteById($id)
    {
        $id = (int) ($id);
        if ($id === 0) {
            return false;
        }

        if (!@RevsliderPrestashop::getIsset(self::$wpdb)) {
            $wpdb = RevsliderPrestashop::$wpdb;
        } else {
            $wpdb = self::$wpdb;
        }


        $table_name = $wpdb->prefix . RevSliderGlobals::TABLE_SLIDERS_NAME;

        //check if ID exists
        $slider = $wpdb->getRow("SELECT settings FROM $table_name WHERE id = $id", ARRAY_A);

        if (empty($slider)) {
            return __('Slider not found', 'revslider');
        }

        $settings = Tools::jsonDecode($slider['settings'], true);

        if (!@RevsliderPrestashop::getIsset($settings['favorite']) || $settings['favorite'] == 'false' || $settings['favorite'] == false) {
            $settings['favorite'] = 'true';
        } else {
            $settings['favorite'] = 'false';
        }

        $response = $wpdb->update($table_name, array('settings' => Tools::jsonEncode($settings)), array('id' => $id));

        if ($response === false) {
            return __('Slider setting could not be changed', 'revslider');
        }

        return true;
    }

    /**
     * import slider from TP servers
     * @since: 5.0.5
     */
    private static function importSliderOnlineTemplateHandle($viewBack = null, $updateAnim = true, $updateStatic = true, $single_slide = false)
    {
        dmp(__("downloading template slider from server...", 'revslider'));

        $uid = esc_attr(RevSliderFunctions::getPostVariable('uid'));

        if ($uid == '') {
            dmp(__("ID missing, something went wrong. Please try again!", 'revslider'));
            echo RevSliderFunctions::getHtmlLink($viewBack, __("Go Back", 'revslider'));
            exit;
        } else {
            //send request to TP server and download file
            $tmp = new RevSliderTemplate();

            $filepath = $tmp->downloadTemplate($uid);

            if ($filepath !== false && !is_array($filepath)) {
                // check if Slider Template was already imported. If yes, remove the old Slider Template as we now do an "update" (in reality we delete and insert again)
                // get all template sliders
                $tmp_slider = $tmp->getThemePunchTemplateSliders();

                foreach ($tmp_slider as $tslider) {
                    if (isset($tslider['uid']) && $uid == $tslider['uid']) {
                        if (!isset($tslider['installed'])) { //slider is installed
                            //delete template Slider!
                            $mSlider = new RevSlider();
                            $mSlider->initByID($tslider['id']);

                            $mSlider->deleteSlider();
                            //remove the update flag from the slider

                            $tmp->removeIsNew($uid);
                        }
                        break;
                    }
                }

                $slider = new RevSlider();
                $response = $slider->importSliderFromPost($updateAnim, $updateStatic, $filepath, $uid, $single_slide);

                $tmp->deleteTemplate($uid);

                if ($single_slide === false) {
                    if (empty($viewBack)) {
                        $sliderID = $response["sliderID"];
                        $viewBack = self::getViewUrl(self::VIEW_SLIDER, "id=" . $sliderID);
                        if (empty($sliderID)) {
                            $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                        }
                    }
                }

                //handle error
                if ($response["success"] == false) {
                    $message = $response["error"];
                    dmp("<b>Error: " . $message . "</b>");
                    echo RevSliderFunctions::getHtmlLink($viewBack, __("Go Back", 'revslider'));
                } else { //handle success, js redirect.
                    dmp(__("Slider Import Success, redirecting...", 'revslider'));
                    echo "<script>location.href='$viewBack'</script>";
                }
            } else {
                if (is_array($filepath)) {
                    dmp($filepath['error']);
                } else {
                    dmp(__("Could not download from server. Please try again later!", 'revslider'));
                }
                echo RevSliderFunctions::getHtmlLink($viewBack, __("Go Back", 'revslider'));
                exit;
            }
        }

        exit;
    }

    public static function onAjaxAction()
    {
        $slider = new RevSlider();

        $slide = new RevSlide();

        $operations = new RevOperations();

        $action = self::getPostGetVar("client_action");

        $data = self::getPostGetVar("data");

        try {

            switch ($action) {
                //start hook setting
                case 'update_shop':
                    $template = new RevSliderTemplate();
                    $template->getTemplateList(true);

                    self::ajaxResponseSuccess(__("Templates Updated", 'revslider'), array());
                    // self::ajaxResponseSuccessRedirect(__("Templates Updated",'revslider'), self::getViewUrl(self::VIEW_SLIDE));
                    break;
                case 'add_new_preset':

                    if (!@RevsliderPrestashop::getIsset($data['settings']) || !@RevsliderPrestashop::getIsset($data['values'])) {
                        self::ajaxResponseError('Missing values to add preset', false);
                    }

                    $result = $operations->addPresetSetting($data);

                    if ($result === true) {
                        $presets = $operations->getPresetSettings();

                        self::ajaxResponseSuccess('Preset created', array('data' => $presets));
                    } else {
                        self::ajaxResponseError($result, false);
                    }

                    exit;
                    break;
                case 'update_preset':

                    if (!@RevsliderPrestashop::getIsset($data['name']) || !@RevsliderPrestashop::getIsset($data['values'])) {
                        self::ajaxResponseError(__('Missing values to update preset', 'revslider'), false);
                    }

                    $result = $operations->updatePresetSetting($data);

                    if ($result === true) {
                        $presets = $operations->getPresetSettings();

                        self::ajaxResponseSuccess(__('Preset updated', 'revslider'), array('data' => $presets));
                    } else {
                        self::ajaxResponseError($result, false);
                    }

                    exit;
                    break;
                case 'remove_preset':

                    if (!@RevsliderPrestashop::getIsset($data['name'])) {
                        self::ajaxResponseError(__('Missing values to remove preset', 'revslider'), false);
                    }

                    $result = $operations->removePresetSetting($data);

                    if ($result === true) {
                        $presets = $operations->getPresetSettings();

                        self::ajaxResponseSuccess(__('Preset deleted', 'revslider'), array('data' => $presets));
                    } else {
                        self::ajaxResponseError($result, false);
                    }

                    exit;
                    break;
                case 'add_new_hook':
                    $f = new SdsRevHooksClass();

                    $result = $f->addNewHook($data);

                    if ($result === true) {
                        self::ajaxResponseSuccessRedirect(
                            __("Hook successfully created!", REVSLIDER_TEXTDOMAIN),
                            self::getViewUrl("sliders")
                        );
                    } else {
                        self::ajaxResponseError($result, false);
                    }
                    break;
                case 'removes_hooks':
                    if (!@RevsliderPrestashop::getIsset($data['hookname'])) {
                        self::ajaxResponseError(__('Hook not found', REVSLIDER_TEXTDOMAIN), false);
                    }

                    $f = new SdsRevHooksClass();

                    $result = $f->removeHookByHookname($data['hookname']);

                    if ($result === true) {
                        self::ajaxResponseSuccess(__("Hook successfully removed!", REVSLIDER_TEXTDOMAIN), array('data' => $result));
                    } else {
                        self::ajaxResponseError($result, false);
                    }
                    break;
                // end hook setting
                case "export_slider":
                    $sliderID = self::getGetVar("sliderid");
                    $dummy = self::getGetVar("dummy");
                    $slider->initByID($sliderID);
                    $slider->exportSlider($dummy);
                    break;
                case "import_slider":
                    $updateAnim = self::getPostGetVar("update_animations");
                    $updateNav = self::getPostGetVar("update_navigations");
                    $updateStatic = self::getPostGetVar("update_static_captions");
                    self::importSliderHandle(null, $updateAnim, $updateStatic, $updateNav);
                    break;
                case "import_slider_slidersview":
                    $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                    $updateAnim = self::getPostGetVar("update_animations");
                    $updateNav = self::getPostGetVar("update_navigations");
                    $updateStatic = self::getPostGetVar("update_static_captions");
                    self::importSliderHandle($viewBack, $updateAnim, $updateStatic, $updateNav);
                    break;
                case "import_slider_online_template_slidersview":
                    $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                    self::importSliderOnlineTemplateHandle($viewBack, 'true', 'none');
                    break;
                case "import_slide_online_template_slidersview":
                    $redirect_id = (self::getPostGetVar("redirect_id"));
                    $viewBack = self::getViewUrl(self::VIEW_SLIDE, "id=$redirect_id");
                    $slidenum = (int) (self::getPostGetVar("slidenum"));
                    $sliderid = (int) (self::getPostGetVar("slider_id"));

                    self::importSliderOnlineTemplateHandle($viewBack, 'true', 'none', array('slider_id' => $sliderid, 'slide_id' => $slidenum));
                    break;
                case "import_slider_template_slidersview":
                    $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                    $updateAnim = self::getPostGetVar("update_animations");
                    $updateStatic = self::getPostGetVar("update_static_captions");
                    self::importSliderTemplateHandle($viewBack, $updateAnim, $updateStatic);
                    break;
                case "import_slide_template_slidersview":

                    $redirect_id = (self::getPostGetVar("redirect_id"));
                    $viewBack = self::getViewUrl(self::VIEW_SLIDE, "id=$redirect_id");
                    $updateAnim = self::getPostGetVar("update_animations");
                    $updateStatic = self::getPostGetVar("update_static_captions");
                    $slidenum = (int) (self::getPostGetVar("slidenum"));
                    $sliderid = (int) (self::getPostGetVar("slider_id"));

                    self::importSliderTemplateHandle($viewBack, $updateAnim, $updateStatic, array('slider_id' => $sliderid, 'slide_id' => $slidenum));
                    break;

                case "create_slider":
                    $data = $operations->modifyCustomSliderParams($data);

                    $newSliderID = $slider->createSliderFromOptions($data);

                    $slideID = $slider->createSlideFromData(array("sliderid" => $newSliderID), true);

                    self::ajaxResponseSuccessRedirect(__("Slider created", 'revslider'), self::getViewUrl(self::VIEW_SLIDE, 'id=' . $slideID . '&slider=' . ($newSliderID))); //redirect to slide now

                    break;

                case "update_slider":

                    $data = $operations->modifyCustomSliderParams($data);
                    $slider->updateSliderFromOptions($data);
                    self::ajaxResponseSuccess(__("Slider updated", 'revslider'));

                    break;



                case "delete_slider":
                case "delete_slider_stay":

                    $isDeleted = $slider->deleteSliderFromData($data);

                    if (is_array($isDeleted)) {
                        $isDeleted = implode(', ', $isDeleted);
                        self::ajaxResponseError(__("Template can't be deleted, it is still being used by the following Sliders: ", 'revslider') . $isDeleted);
                    } else {
                        if ($action == 'delete_slider_stay') {
                            self::ajaxResponseSuccess(__("Slider deleted", 'revslider'));
                        } else {
                            self::ajaxResponseSuccessRedirect(__("Slider deleted", 'revslider'), self::getViewUrl(self::VIEW_SLIDERS));
                        }
                    }
                    break;
                case "duplicate_slider":

                    $slider->duplicateSliderFromData($data);

                    self::ajaxResponseSuccessRedirect(__("Success! Refreshing page...", 'revslider'), self::getViewUrl(self::VIEW_SLIDERS));
                    break;

                case "add_slide":
                case "add_bulk_slide":
//						$numSlides = $slider->createSlideFromData($data);
                    $slideid = $slider->createSlideFromData($data, true);

                    $sliderID = $data["sliderid"];

//						if($numSlides == 1){
//							$responseText = __("Slide Created",'revslider');
//						}else{
//							$responseText = $numSlides . " ".__("Slides Created",'revslider');
//						}
                    $responseText = __("Slide Created", 'revslider');
                    $urlRedirect = self::getViewUrl(self::VIEW_SLIDE, "id={$slideid}&slider={$sliderID}");
                    self::ajaxResponseSuccessRedirect($responseText, $urlRedirect);

                    break;

                case "add_slide_fromslideview":

                    $slideID = $slider->createSlideFromData($data, true);

                    $urlRedirect = self::getViewUrl(self::VIEW_SLIDE, "id=$slideID");

                    $responseText = __("Slide Created, redirecting...", REVSLIDER_TEXTDOMAIN);

                    self::ajaxResponseSuccessRedirect($responseText, $urlRedirect);

                    break;

                case 'copy_slide_to_slider':
                    $slideID = (@RevsliderPrestashop::getIsset($data['redirect_id'])) ? $data['redirect_id'] : -1;

                    if ($slideID === -1) {
                        RevSliderFunctions::throwError(__('Missing redirect ID!', 'revslider'));
                    }

                    $return = $slider->copySlideToSlider($data);

                    if ($return !== true) {
                        RevSliderFunctions::throwError($return);
                    }

                    $urlRedirect = self::getViewUrl(self::VIEW_SLIDE, "id=$slideID");
                    $responseText = __("Slide copied to current Slider, redirecting...", 'revslider');
                    self::ajaxResponseSuccessRedirect($responseText, $urlRedirect);
                    break;

                case "update_slide":

                    $slide->updateSlideFromData($data);
                    self::ajaxResponseSuccess(__("Slide updated", 'revslider'));

                    break;
                case "update_static_slide":
                    $slide->updateStaticSlideFromData($data);
                    self::ajaxResponseSuccess(__("Static Global Layers updated", REVSLIDER_TEXTDOMAIN));
                    break;
                case "delete_slide":
                case "delete_slide_stay":
                    $isPost = $slide->deleteSlideFromData($data);
                    if ($isPost) {
                        $message = __("Post deleted", 'revslider');
                    } else {
                        $message = __("Slide deleted", 'revslider');
                    }

                    $sliderID = RevSliderFunctions::getVal($data, "sliderID");
                    if ($action == 'delete_slide_stay') {
                        self::ajaxResponseSuccess($message);
                    } else {
                        self::ajaxResponseSuccessRedirect($message, self::getViewUrl(self::VIEW_SLIDES, "id={$sliderID}"));
                    }
                    break;

                case "duplicate_slide":
                case "duplicate_slide_stay":
                    $return = $slider->duplicateSlideFromData($data);
                    if ($action == 'duplicate_slide_stay') {
                        self::ajaxResponseSuccess(__("Slide duplicated", 'revslider'), array('id' => $return[1]));
                    } else {
                        self::ajaxResponseSuccessRedirect(__("Slide duplicated", 'revslider'), self::getViewUrl(self::VIEW_SLIDE, "id={$return[1]}&slider=" . $return[0]));
                    }
                    break;
                case "copy_move_slide":
                case "copy_move_slide_stay":
                    $sliderID = $slider->copyMoveSlideFromData($data);
                    if ($action == 'copy_move_slide_stay') {
                        self::ajaxResponseSuccess(__("Success!", 'revslider'));
                    } else {
                        self::ajaxResponseSuccessRedirect(__("Success! Refreshing page...", 'revslider'), self::getViewUrl(self::VIEW_SLIDE, "id=new&slider=$sliderID"));
                    }
                    break;

                case "add_slide_to_template":
                    $template = new RevSliderTemplate();
                    if (!@RevsliderPrestashop::getIsset($data['slideID']) || (int) ($data['slideID']) == 0) {
                        RevSliderFunctions::throwError(__('No valid Slide ID given', 'revslider'));
                        exit;
                    }
                    if (!@RevsliderPrestashop::getIsset($data['title']) || Tools::strlen(trim($data['title'])) < 3) {
                        RevSliderFunctions::throwError(__('No valid title given', 'revslider'));
                        exit;
                    }
                    if (!@RevsliderPrestashop::getIsset($data['settings']) || !@RevsliderPrestashop::getIsset($data['settings']['width']) || !@RevsliderPrestashop::getIsset($data['settings']['height'])) {
                        RevSliderFunctions::throwError(__('No valid title given', 'revslider'));
                        exit;
                    }

                    $return = $template->copySlideToTemplates($data['slideID'], $data['title'], $data['settings']);

                    if ($return == false) {
                        RevSliderFunctions::throwError(__('Could not save Slide as Template', 'revslider'));
                        exit;
                    }

                    //get HTML for template section
                    ob_start();

//						$rs_disable_template_script = true; //disable the script output of template selector file

                    include(WP_CONTENT_DIR . '/views/templates/template-selector.php');

                    $html = ob_get_contents();

                    ob_clean();
                    ob_end_clean();

                    self::ajaxResponseSuccess(__('Slide added to Templates', 'revslider'), array('HTML' => $html));
                    exit;
                    break;
                    break;

                case "get_static_css":

                    $contentCSS = $operations->getStaticCss();

                    self::ajaxResponseData($contentCSS);

                    break;

                case "get_dynamic_css":

                    $contentCSS = $operations->getDynamicCss();

                    self::ajaxResponseData($contentCSS);

                    break;

                case "insert_captions_css":

                    $arrCaptions = $operations->insertCaptionsContentData($data);

                    if ($arrCaptions !== false) {
                        $db = new RevSliderDB();
                        $styles = $db->fetch(RevSliderGlobals::$table_css);
                        $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                        $styles = RevSliderCssParser::compressCss($styles);
                        $custom_css = RevSliderOperations::getStaticCss();
                        $custom_css = RevSliderCssParser::compressCss($custom_css);

                        $arrCSS = $operations->getCaptionsContentArray();
                        $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
                        $arrCssStyles = $arrCSS;

                        self::ajaxResponseSuccess(__("CSS saved", 'revslider'), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    }

                    RevSliderFunctions::throwError(__('CSS could not be saved', 'revslider'));
                    exit();
                    break;
                case "update_captions_css":

                    $arrCaptions = $operations->updateCaptionsContentData($data);

                    if ($arrCaptions !== false) {
                        $db = new RevSliderDB();
                        $styles = $db->fetch(RevSliderGlobals::$table_css);
                        $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                        $styles = RevSliderCssParser::compressCss($styles);
                        $custom_css = RevSliderOperations::getStaticCss();
                        $custom_css = RevSliderCssParser::compressCss($custom_css);

                        $arrCSS = $operations->getCaptionsContentArray();
                        $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
                        $arrCssStyles = $arrCSS;

                        self::ajaxResponseSuccess(__("CSS saved", 'revslider'), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    }

                    RevSliderFunctions::throwError(__('CSS could not be saved', 'revslider'));
                    exit();
                    break;

                case "update_captions_advanced_css":

                    $arrCaptions = $operations->updateAdvancedCssData($data);
                    if ($arrCaptions !== false) {
                        $db = new RevSliderDB();
                        $styles = $db->fetch(RevSliderGlobals::$table_css);
                        $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                        $styles = RevSliderCssParser::compressCss($styles);
                        $custom_css = RevSliderOperations::getStaticCss();
                        $custom_css = RevSliderCssParser::compressCss($custom_css);

                        $arrCSS = $operations->getCaptionsContentArray();
                        $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
                        $arrCssStyles = $arrCSS;

                        self::ajaxResponseSuccess(__("CSS saved", 'revslider'), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    }

                    RevSliderFunctions::throwError(__('CSS could not be saved', 'revslider'));
                    exit();
                    break;

                case "rename_captions_css":
                    //rename all captions in all sliders with new handle if success
                    $arrCaptions = $operations->renameCaption($data);

                    $db = new RevSliderDB();
                    $styles = $db->fetch(RevSliderGlobals::$table_css);
                    $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                    $styles = RevSliderCssParser::compressCss($styles);
                    $custom_css = RevSliderOperations::getStaticCss();
                    $custom_css = RevSliderCssParser::compressCss($custom_css);

                    $arrCSS = $operations->getCaptionsContentArray();
                    $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
                    $arrCssStyles = $arrCSS;

                    self::ajaxResponseSuccess(__("Class name renamed", 'revslider'), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    break;

                case "delete_captions_css":
                    $arrCaptions = $operations->deleteCaptionsContentData($data);

                    $db = new RevSliderDB();
                    $styles = $db->fetch(RevSliderGlobals::$table_css);
                    $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                    $styles = RevSliderCssParser::compressCss($styles);
                    $custom_css = RevSliderOperations::getStaticCss();
                    $custom_css = RevSliderCssParser::compressCss($custom_css);

                    $arrCSS = $operations->getCaptionsContentArray();
                    $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
                    $arrCssStyles = $arrCSS;

                    self::ajaxResponseSuccess(__("Style deleted!", 'revslider'), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    break;

                case "update_static_css":
                    $staticCss = $operations->updateStaticCss($data);

                    $db = new RevSliderDB();
                    $styles = $db->fetch(RevSliderGlobals::$table_css);
                    $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                    $styles = RevSliderCssParser::compressCss($styles);
                    $custom_css = RevSliderOperations::getStaticCss();
                    $custom_css = RevSliderCssParser::compressCss($custom_css);

                    self::ajaxResponseSuccess(__("CSS saved", 'revslider'), array("css" => $staticCss, 'compressed_css' => $styles . $custom_css));
                    break;

                case "insert_custom_anim":
                    $arrAnims = $operations->insertCustomAnim($data); //$arrCaptions =
                    self::ajaxResponseSuccess(__("Animation saved", 'revslider'), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
                    break;
                case "update_custom_anim":
                    $arrAnims = $operations->updateCustomAnim($data);
                    self::ajaxResponseSuccess(__("Animation saved", 'revslider'), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
                    break;
                case "update_custom_anim_name":
                    $arrAnims = $operations->updateCustomAnimName($data);
                    self::ajaxResponseSuccess(__("Animation saved", 'revslider'), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
                    break;
                case "delete_custom_anim":
                    $arrAnims = $operations->deleteCustomAnim($data);
                    self::ajaxResponseSuccess(__("Animation deleted", 'revslider'), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
                    break;

                case "update_slides_order":
                    $slider->updateSlidesOrderFromData($data);
                    self::ajaxResponseSuccess(__("Order updated", 'revslider'));
                    break;
                case "change_slide_title":
                    $slide->updateTitleByID($data);
                    self::ajaxResponseSuccess(__('Title updated', 'revslider'));
                    break;
                case "change_slide_image":
                    $slide->updateSlideImageFromData($data);
                    $sliderID = RevSliderFunctions::getVal($data, "slider_id");
                    self::ajaxResponseSuccessRedirect(__("Slide changed", 'revslider'), self::getViewUrl(self::VIEW_SLIDE, "id=new&slider=$sliderID"));
                    break;
                case "preview_slide":
                    $operations->putSlidePreviewByData($data);
                    break;

                case "preview_slider":
                    $sliderID = RevSliderFunctions::getPostGetVariable("sliderid");
                    $do_markup = RevSliderFunctions::getPostGetVariable("only_markup");

                    if ($do_markup == 'true') {
                        $operations->previewOutputMarkup($sliderID);
                    } else {
                        $operations->previewOutput($sliderID);
                    }
                    break;
                case "toggle_slide_state":
                    $currentState = $slide->toggleSlideStatFromData($data);
                    self::ajaxResponseData(array("state" => $currentState));
                    break;

                case "toggle_hero_slide":
                    $currentHero = $slider->setHeroSlide($data);
                    self::ajaxResponseSuccess(__('Slide is now the new active Hero Slide', 'revslider'));
                    break;

                case "slide_lang_operation":
                    $responseData = $slide->doSlideLangOperation($data);
                    self::ajaxResponseData($responseData);
                    break;

                case "update_general_settings":
                    $operations->updateGeneralSettings($data);
                    self::ajaxResponseSuccess(__("General settings updated", 'revslider'));
                    break;

                case "update_posts_sortby":
                    $slider->updatePostsSortbyFromData($data);
                    self::ajaxResponseSuccess(__("Sortby updated", 'revslider'));
                    break;

                case "replace_image_urls":
                    $slider->replaceImageUrlsFromData($data);
                    self::ajaxResponseSuccess(__("Image urls replaced", 'revslider'));
                    break;

                case "reset_slide_settings":
                    $slider->resetSlideSettings($data);
                    self::ajaxResponseSuccess(__("Settings in all Slides changed", 'revslider'));
                    break;

                case "activate_purchase_code":
                    $result = false;
                    if (!empty($data['code'])) {
                        $result = $operations->checkPurchaseVerification($data);
                    } else {
                        RevSliderFunctions::throwError(__('The API key, the Purchase Code and the Username need to be set!', 'revslider'));
                        exit();
                    }

                    if ($result) {
                        self::ajaxResponseSuccessRedirect(__("Purchase Code Successfully Activated", 'revslider'), self::getViewUrl(self::VIEW_SLIDERS));
                    } else {
                        RevSliderFunctions::throwError(__('Purchase Code is invalid', 'revslider'));
                    }
                    break;

                case "deactivate_purchase_code":
                    $result = $operations->doPurchaseDeactivation($data);

                    if ($result) {
                        self::ajaxResponseSuccessRedirect(__("Successfully removed validation", 'revslider'), self::getViewUrl(self::VIEW_SLIDERS));
                    } else {
                        RevSliderFunctions::throwError(__('Could not remove Validation!', 'revslider'));
                    }
                    break;

                case 'dismiss_notice':
                    update_option('revslider-valid-notice', 'false');
                    self::ajaxResponseSuccess(__(".", 'revslider'));
                    break;
                case 'dismiss_dynamic_notice':
                    $notices_discarded = get_option('revslider-notices-dc', array());
                    $notices_discarded[] = (trim($data['id']));
                    update_option('revslider-notices-dc', $notices_discarded);

                    self::ajaxResponseSuccess(__(".", 'revslider'));
                    break;

                case "update_text":

                    self::updateSettingsText();

                    self::ajaxResponseSuccess(__("All files successfully updated", REVSLIDER_TEXTDOMAIN));

                    break;

                case 'toggle_favorite':
                    if (@RevsliderPrestashop::getIsset($data['id']) && (int) ($data['id']) > 0) {
                        $return = self::toggleFavoriteById($data['id']);
                        if ($return === true) {
                            self::ajaxResponseSuccess(__('Setting Changed!', 'revslider'));
                        } else {
                            $error = $return;
                        }
                    } else {
                        $error = __('No ID given', 'revslider');
                    }
                    self::ajaxResponseError($error);
                    break;
                case "subscribe_to_newsletter":
                    if (@RevsliderPrestashop::getIsset($data['email']) && !empty($data['email'])) {
                        $return = ThemePunch_Newsletter::subscribe($data['email']);

                        if ($return !== false) {
                            if (!@RevsliderPrestashop::getIsset($return['status']) || $return['status'] === 'error') {
                                $error = (@RevsliderPrestashop::getIsset($return['message']) && !empty($return['message'])) ? $return['message'] : __('Invalid Email', 'revslider');
                                self::ajaxResponseError($error);
                            } else {
                                self::ajaxResponseSuccess(__("Success! Please check your Emails to finish the subscription", 'revslider'), $return);
                            }
                        } else {
                            self::ajaxResponseError(__('Invalid Email/Could not connect to the Newsletter server', 'revslider'));
                        }
                    } else {
                        self::ajaxResponseError(__('No Email given', 'revslider'));
                    }
                    break;

                case "unsubscribe_to_newsletter":
                    if (@RevsliderPrestashop::getIsset($data['email']) && !empty($data['email'])) {
                        $return = ThemePunch_Newsletter::unsubscribe($data['email']);

                        if ($return !== false) {
                            if (!@RevsliderPrestashop::getIsset($return['status']) || $return['status'] === 'error') {
                                $error = (@RevsliderPrestashop::getIsset($return['message']) && !empty($return['message'])) ? $return['message'] : __('Invalid Email', 'revslider');
                                self::ajaxResponseError($error);
                            } else {
                                self::ajaxResponseSuccess(__("Success! Please check your Emails to finish the process", 'revslider'), $return);
                            }
                        } else {
                            self::ajaxResponseError(__('Invalid Email/Could not connect to the Newsletter server', 'revslider'));
                        }
                    } else {
                        self::ajaxResponseError(__('No Email given', 'revslider'));
                    }
                    break;

                case 'change_specific_navigation':
                    $nav = new RevSliderNavigation();

                    $found = false;
                    $navigations = $nav->getAllNavigations();

                    foreach ($navigations as $navig) {
                        if ($data['id'] == $navig['id']) {
                            $found = true;
                            break;
                        }
                    }
                    if ($found) {
                        $nav->createUpdateNavigation($data, $data['id']);
                    } else {
                        $nav->createUpdateNavigation($data);
                    }

                    self::ajaxResponseSuccess(__('Navigation saved/updated', 'revslider'), array('navs' => $nav->getAllNavigations()));

                    break;

                case 'change_navigations':
                    $nav = new RevSliderNavigation();
                    // // var_dump($data);
                    $nav->createUpdateFullNavigation($data);

                    self::ajaxResponseSuccess(__('Navigations updated', 'revslider'), array('navs' => $nav->getAllNavigations()));
                    break;

                case 'delete_navigation':
                    $nav = new RevSliderNavigation();

                    if (@RevsliderPrestashop::getIsset($data) && (int) ($data) > 0) {
                        $return = $nav->deleteNavigation($data);

                        if ($return !== true) {
                            self::ajaxResponseError($return);
                        } else {
                            self::ajaxResponseSuccess(__('Navigation deleted', 'revslider'), array('navs' => $nav->getAllNavigations()));
                        }
                    }

                    self::ajaxResponseError(__('Wrong ID given', 'revslider'));
                    break;

                case "get_facebook_photosets":
                    if (!empty($data['url'])) {
                        $facebook = new RevSliderFacebook();
                        $return = $facebook->get_photo_set_photos_options($data['url'], $data['album'], $data['app_id'], $data['app_secret']);
                        if (!empty($return)) {
                            self::ajaxResponseSuccess(__('Successfully fetched Facebook albums', 'revslider'), array('html' => implode(' ', $return)));
                        } else {
                            $error = __('Could not fetch Facebook albums', 'revslider');
                            self::ajaxResponseError($error);
                        }
                    } else {
                        self::ajaxResponseSuccess(__('Cleared Albums', 'revslider'), array('html' => implode(' ', $return)));
                    }
                    break;

                case "get_flickr_photosets":
                    if (!empty($data['url']) && !empty($data['key'])) {
                        $flickr = new RevSliderFlickr($data['key']);
                        $user_id = $flickr->get_user_from_url($data['url']);
                        $return = $flickr->get_photo_sets($user_id, $data['count'], $data['set']);
                        if (!empty($return)) {
                            self::ajaxResponseSuccess(__('Successfully fetched flickr photosets', 'revslider'), array("data" => array('html' => implode(' ', $return))));
                        } else {
                            $error = __('Could not fetch flickr photosets', 'revslider');
                            self::ajaxResponseError($error);
                        }
                    } else {
                        if (empty($data['url']) && empty($data['key'])) {
                            self::ajaxResponseSuccess(__('Cleared Photosets', 'revslider'), array('html' => implode(' ', $return)));
                        } elseif (empty($data['url'])) {
                            $error = __('No User URL - Could not fetch flickr photosets', 'revslider');
                            self::ajaxResponseError($error);
                        } else {
                            $error = __('No API KEY - Could not fetch flickr photosets', 'revslider');
                            self::ajaxResponseError($error);
                        }
                    }
                    break;

                case "get_youtube_playlists":
                    if (!empty($data['id'])) {
                        $youtube = new RevSliderYoutube(trim($data['api']), trim($data['id']));
                        $return = $youtube->get_playlist_options($data['playlist']);
                        self::ajaxResponseSuccess(__('Successfully fetched YouTube playlists', 'revslider'), array("data" => array('html' => implode(' ', $return))));
                    } else {
                        $error = __('Could not fetch YouTube playlists', 'revslider');
                        self::ajaxResponseError($error);
                    }
                    break;

                case 'rs_get_store_information':

                    $code = get_option('revslider-code', '');
                    $shop_version = RevSliderTemplate::SHOP_VERSION;

                    $validated = get_option('revslider-valid', 'false');
                    if ($validated == 'false') {
                        $api_key = '';
                        $username = '';
                        $code = '';
                    }

                    $rattr = array(
                        'code' => urlencode($code),
                        'product' => urlencode('revslider'),
                        'shop_version' => urlencode($shop_version),
                        'version' => urlencode(RevSliderGlobals::SLIDER_REVISION)
                    );

                    $request = wp_remote_fopen('http://templates.themepunch.tools/revslider/store.php');

                    $response = '';

                    if ($request !== false) {
                        $response = Tools::jsonDecode(@$request, true);
                    }

                    self::ajaxResponseData(array("data" => $response));
                    break;

                case 'delete_uploaded_image':

                    self::deleteUploadedFile($data);

                    break;

                case 'get_uploaded_images':

                    self::getUploadedFilesJson();

                    break;


                default:

                    self::ajaxResponseError("wrong ajax action: $action ");

                    break;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();

            if ($action == "preview_slide" || $action == "preview_slider") {
                echo $message;

                exit();
            }



            self::ajaxResponseError($message);
        }



        //it's an ajax action, so exit
        //self::ajaxResponseError("No response output on <b> $action </b> action. please check with the developer.");

        exit();
    }
}
