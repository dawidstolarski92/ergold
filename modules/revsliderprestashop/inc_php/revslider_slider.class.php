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

class RevSlider extends UniteElementsBaseRev
{

    const DEFAULT_POST_SORTBY = "ID";
    const DEFAULT_POST_SORTDIR = "DESC";
    const VALIDATE_NUMERIC = "numeric";
    const VALIDATE_EMPTY = "empty";
    const FORCE_NUMERIC = "force_numeric";
    const SLIDER_TYPE_GALLERY = "gallery";
    const SLIDER_TYPE_POSTS = "posts";
    const SLIDER_TYPE_TEMPLATE = "template";
    const SLIDER_TYPE_ALL = "all";

    private $id;
    private $title;
    private $alias;
    public $arrParams;
    private $settings;
    public $arrSlides = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function isInited()
    {
        if (!empty($this->id)) {
            return(true);
        }



        return(false);
    }

    private function validateInited()
    {
        if (empty($this->id)) {
            UniteFunctionsRev::throwError("The slider is not inited!");
        }
    }

    /**
     * init slider by db data
     */
    public function initByDBData($arrData)
    {
        $this->id = $arrData["id"];
        $this->title = $arrData["title"];
        $this->alias = $arrData["alias"];

        $settings = $arrData["settings"];
        $settings = (array) Tools::jsonDecode($settings);

        $this->settings = $settings;

        $params = $arrData["params"];
        $params = (array) Tools::jsonDecode($params);
        $params = RevSliderBase::translateSettingsToV5($params);

        $this->arrParams = $params;
    }

    public function initByID($sliderID)
    {
        UniteFunctionsRev::validateNumeric($sliderID, "Slider ID");

        $sliderID = $this->db->escape($sliderID);



        try {
            $sliderData = $this->db->fetchSingle(GlobalsRevSlider::$table_sliders, "id=$sliderID");
        } catch (Exception $e) {
            UniteFunctionsRev::throwError("Slider with ID: $sliderID Not Found");
        }



        $this->initByDBData($sliderData);
    }

    public function getSliderImgSettings($sliderID)
    {
        UniteFunctionsRev::validateNumeric($sliderID, "Slider ID");

        $sliderID = $this->db->escape($sliderID);



        try {
            $sliderData = $this->db->fetchSingle(GlobalsRevSlider::$table_sliders, "id=$sliderID");
        } catch (Exception $e) {
            UniteFunctionsRev::throwError("Slider with ID: $sliderID Not Found");
        }

        return Tools::jsonDecode($sliderData['params'])->prd_img_size;
    }

    public function initByAlias($alias)
    {
        $alias = $this->db->escape($alias);

        try {
            $where = "alias='$alias' AND `type` != 'template'";

            $sliderData = $this->db->fetchSingle(GlobalsRevSlider::$table_sliders, $where);
        } catch (Exception $e) {
            $arrAliases = $this->getAllSliderAliases();
            $strAliases = "";

            if (!empty($arrAliases)) {
                $strAliases = "'" . implode("' or '", $arrAliases) . "'";
            }


            $errorMessage = "Slider with alias <strong>$alias</strong> not found.";

            if (!empty($strAliases)) {
                $errorMessage .= " <br><br>Maybe you mean: " . $strAliases;
            }



            UniteFunctionsRev::throwError($errorMessage);
        }

        $this->initByDBData($sliderData);
    }

    public function initByMixed($mixed)
    {
        if (is_numeric($mixed)) {
            $this->initByID($mixed);
        } else {
            $this->initByAlias($mixed);
        }
    }

    public function getTitle()
    {
        return($this->title);
    }

    public function getID()
    {
        return($this->id);
    }

    public function getParams()
    {
        return($this->arrParams);
    }
    /*
     * return Slider settings
     * @since: 5.0
     */

    public function getSettings()
    {
        return($this->settings);
    }

    /**
     * update some settings in the slider
     */
    public function updateSetting($arrUpdate)
    {
        $this->validateInited();

        $this->settings = array_merge($this->settings, $arrUpdate);
        $jsonParams = Tools::jsonEncode($this->settings);
        $arrUpdateDB = array();
        $arrUpdateDB["settings"] = $jsonParams;

        $this->db->update(RevSliderGlobals::$table_sliders, $arrUpdateDB, array("id" => $this->id));
    }
    /*
     * return true if slider is favorite
     * @since: 5.0
     */

    public function isFavorite()
    {
        if (!empty($this->settings)) {
            if (@RevsliderPrestashop::getIsset($this->settings['favorite']) && $this->settings['favorite'] == 'true') {
                return true;
            }
        }

        return false;
    }

    public function setParams($arrParams)
    {
        $this->arrParams = $arrParams;
    }

    public function getParam($name, $default = null, $validateType = null, $title = "")
    {
        if ($default === null) {
            $default = "";
        }

        $value = UniteFunctionsRev::getVal($this->arrParams, $name, $default);

        //validation:

        switch ($validateType) {
            case self::VALIDATE_NUMERIC:
            case self::VALIDATE_EMPTY:
                $paramTitle = !empty($title) ? $title : $name;
                if ($value !== "0" && $value !== 0 && empty($value)) {
                    UniteFunctionsRev::throwError("The param <strong>$paramTitle</strong> should not be empty.");
                }
                break;
            case self::VALIDATE_NUMERIC:
                $paramTitle = !empty($title) ? $title : $name;
                if (!is_numeric($value)) {
                    UniteFunctionsRev::throwError("The param <strong>$paramTitle</strong> should be numeric. Now it's: $value");
                }
                break;

            case self::FORCE_NUMERIC:
                if (!is_numeric($value)) {
                    $value = 0;
                    if (!empty($default)) {
                        $value = $default;
                    }
                }
                break;
        }

        return $value;
    }

    public function getAlias()
    {
        return($this->alias);
    }

    public function getShowTitle()
    {
        $showTitle = $this->title;

        return($showTitle);
    }

    public function getShortcode()
    {
        $shortCode = "[rev_slider " . $this->alias . "]";

        return($shortCode);
    }

    public static function isAliasExists($alias)
    {
        $wpdb = rev_db_class::revDbInstance();
        $response = $wpdb->getRow("SELECT * FROM " . GlobalsRevSlider::$table_sliders . " WHERE alias = $alias");
        return(!empty($response));
    }

    private function isAliasExistsInDB($alias)
    {
        $alias = $this->db->escape($alias);



        $where = "alias='$alias'";

        if (!empty($this->id)) {
            $where .= " and id != '" . $this->id . "'";
        }



        $response = $this->db->fetch(GlobalsRevSlider::$table_sliders, $where);

        return(!empty($response));
    }

    /**
     * 
     * validate settings for add
     */
    private function validateInputSettings($title, $alias, $params)
    {
        RevSliderFunctions::validateNotEmpty($title, "title");
        RevSliderFunctions::validateNotEmpty($alias, "alias");

        if ($this->isAliasExistsInDB($alias)) {
            RevSliderFunctions::throwError("Some other slider with alias '$alias' already exists");
        }
    }

    /**
     * 
     * create / update slider from options
     */
    private function createUpdateSliderFromOptions($options, $sliderID = null)
    {
        $arrMain = RevSliderFunctions::getVal($options, "main");
        $params = RevSliderFunctions::getVal($options, "params");

        //trim all input data
        $arrMain = RevSliderFunctions::trimArrayItems($arrMain);

        $params = RevSliderFunctions::trimArrayItems($params);

        $params = array_merge($arrMain, $params);

        $title = RevSliderFunctions::getVal($arrMain, "title");
        $alias = RevSliderFunctions::getVal($arrMain, "alias");

        if (!empty($sliderID)) {
            $this->initByID($sliderID);
        }

        $this->validateInputSettings($title, $alias, $params);

        $jsonParams = Tools::jsonEncode($params);

        //insert slider to database
        $arrData = array();
        $arrData["title"] = $title;
        $arrData["alias"] = $alias;
        $arrData["params"] = $jsonParams;

        if (empty($sliderID)) {    //create slider
            $arrData['settings'] = Tools::jsonEncode(array('version' => 5.0));

            $sliderID = $this->db->insert(RevSliderGlobals::$table_sliders, $arrData);
            return($sliderID);
        } else {    //update slider
            $this->initByID($sliderID);

            $settings = $this->getSettings();
            $settings['version'] = 5.0;
            $arrData['settings'] = Tools::jsonEncode($settings);

            $sliderID = $this->db->update(RevSliderGlobals::$table_sliders, $arrData, array("id" => $sliderID));
        }
    }

    private function deleteSlider()
    {
        $this->validateInited();



        //delete slider

        $this->db->delete(GlobalsRevSlider::$table_sliders, "id=" . $this->id);



        //delete slides

        $this->deleteAllSlides();
    }

    private function deleteAllSlides()
    {
        $this->validateInited();



        $this->db->delete(GlobalsRevSlider::$table_slides, "slider_id=" . $this->id);
    }

    private function deleteStaticSlide()
    {
        $this->validateInited();

        $this->db->delete(GlobalsRevSlider::$table_static_slides, "slider_id=" . $this->id);
    }

    public function getArrSlideChildren($slideID)
    {
        $this->validateInited();

        $arrSlides = $this->getSlidesFromGallery();

        if (!@RevsliderPrestashop::getIsset($arrSlides[$slideID])) {
            UniteFunctionsRev::throwError("Slide with id: $slideID not found in the main slides of the slider. Maybe it's child slide.");
        }



        $slide = $arrSlides[$slideID];

        $arrChildren = $slide->getArrChildren();

        return($arrChildren);
    }

    private function duplicateSlider($title = false)
    {
        $this->validateInited();

        if ($title === false) {
            //get slider number:
            $response = $this->db->fetch(RevSliderGlobals::$table_sliders);
            $numSliders = count($response);
            $newSliderSerial = $numSliders + 1;

            $newSliderTitle = "Slider" . $newSliderSerial;
            $newSliderAlias = "slider" . $newSliderSerial;
        } else {
            $newSliderTitle = $title;
            $newSliderAlias = sanitize_title($title);

            // Check Duplicate Alias
            $sqlTitle = $this->db->fetch(RevSliderGlobals::$table_sliders, "alias='" . sanitize_title($title) . "'");
            if (!empty($sqlTitle)) {
                $response = $this->db->fetch(RevSliderGlobals::$table_sliders);
                $numSliders = count($response);
                $newSliderSerial = $numSliders + 1;
                $newSliderTitle .= $newSliderSerial;
                $newSliderAlias .= $newSliderSerial;
            }
        }

        //insert a new slider
        $sqlSelect = "select " . GlobalsRevSlider::FIELDS_SLIDER . " from " . GlobalsRevSlider::$table_sliders . " where id=" . $this->id . "";
        $sqlInsert = "insert into " . GlobalsRevSlider::$table_sliders . " (" . GlobalsRevSlider::FIELDS_SLIDER . ") ($sqlSelect)";

        $this->db->runSql($sqlInsert);
        $lastID = $this->db->getLastInsertID();
        UniteFunctionsRev::validateNotEmpty($lastID);

        //update the new slider with the title and the alias values
        $arrUpdate = array();
        $arrUpdate["title"] = $newSliderTitle;
        $arrUpdate["alias"] = $newSliderAlias;

        //update params
        $params = $this->arrParams;
        $params["title"] = $newSliderTitle;
        $params["alias"] = $newSliderAlias;
        $jsonParams = Tools::jsonEncode($params);
        $arrUpdate["params"] = $jsonParams;

        $this->db->update(GlobalsRevSlider::$table_sliders, $arrUpdate, array("id" => $lastID));


        //duplicate slides
        $fields_slide = GlobalsRevSlider::FIELDS_SLIDE;
        $fields_slide = str_replace("slider_id", $lastID, $fields_slide);

        $sqlSelect = "select " . $fields_slide . " from " . GlobalsRevSlider::$table_slides . " where slider_id=" . $this->id;
        $sqlInsert = "insert into " . GlobalsRevSlider::$table_slides . " (" . GlobalsRevSlider::FIELDS_SLIDE . ") ($sqlSelect)";

        $this->db->runSql($sqlInsert);

        //duplicate static slide if exists
        $slide = new RevSlide();
        $staticID = $slide->getStaticSlideID($this->id);
        if ($staticID !== false) {
            $record = $this->db->fetchSingle(GlobalsRevSlider::$table_static_slides, "id=$staticID");
            unset($record['id']);
            $record['slider_id'] = $lastID;

            $this->db->insert(GlobalsRevSlider::$table_static_slides, $record);
        }
    }

    public function duplicateSlide($slideID)
    {
        $slide = new RevSlide();

        $slide->initByID($slideID);

        $order = $slide->getOrder();

        $slides = $this->getSlidesFromGallery();

        $newOrder = $order + 1;

        $this->shiftOrder($newOrder);



        //do duplication

        $sqlSelect = "select " . GlobalsRevSlider::FIELDS_SLIDE . " from " . GlobalsRevSlider::$table_slides . " where id=" . $slideID;

        $sqlInsert = "insert into " . GlobalsRevSlider::$table_slides . " (" . GlobalsRevSlider::FIELDS_SLIDE . ") ($sqlSelect)";



        $this->db->runSql($sqlInsert);

        $lastID = $this->db->getLastInsertID();

        UniteFunctionsRev::validateNotEmpty($lastID);



        //update order

        $arrUpdate = array("slide_order" => $newOrder);



        $this->db->update(GlobalsRevSlider::$table_slides, $arrUpdate, array("id" => $lastID));



        return($lastID);
    }

    private function copyMoveSlide($slideID, $targetSliderID, $operation)
    {
        if ($operation == "move") {
            $targetSlider = new RevSlider();

            $targetSlider->initByID($targetSliderID);

            $maxOrder = $targetSlider->getMaxOrder();

            $newOrder = $maxOrder + 1;

            $arrUpdate = array("slider_id" => $targetSliderID, "slide_order" => $newOrder);



            //update children

            $arrChildren = $this->getArrSlideChildren($slideID);

            foreach ($arrChildren as $child) {
                $childID = $child->getID();

                $this->db->update(GlobalsRevSlider::$table_slides, $arrUpdate, array("id" => $childID));
            }



            $this->db->update(GlobalsRevSlider::$table_slides, $arrUpdate, array("id" => $slideID));
        } else {    //in place of copy
            $newSlideID = $this->duplicateSlide($slideID);
            $this->duplicateChildren($slideID, $newSlideID);
            $this->copyMoveSlide($newSlideID, $targetSliderID, "move");
        }
    }

    private function shiftOrder($fromOrder)
    {
        $where = " slider_id=" . $this->id . " and slide_order >= $fromOrder";

        $sql = "update " . GlobalsRevSlider::$table_slides . " set slide_order=(slide_order+1) where $where";

        $this->db->runSql($sql);
    }

    public function createSliderFromOptions($options)
    {
        $sliderID = $this->createUpdateSliderFromOptions($options, null);

        return($sliderID);
    }

    /**
     * 
     * export slider from data, output a file for download
     */
    public function exportSlider($useDummy = false)
    {
        $this->validateInited();

        $sliderParams = $this->getParamsForExport();
        $arrSlides = $this->getSlidesForExport($useDummy);
        $arrStaticSlide = $this->getStaticSlideForExport($useDummy);

        $usedCaptions = array();
        $usedAnimations = array();
        $usedImages = array();
        $usedVideos = array();
        $usedNavigations = array();

        $cfw = array();
        if (!empty($arrSlides) && count($arrSlides) > 0) {
            $cfw = array_merge($cfw, $arrSlides);
        }
        if (!empty($arrStaticSlide) && count($arrStaticSlide) > 0) {
            $cfw = array_merge($cfw, $arrStaticSlide);
        }


        //remove image_id as it is not needed in export
        if (!empty($arrSlides)) {
            foreach ($arrSlides as $k => $s) {
                if (@RevsliderPrestashop::getIsset($arrSlides[$k]['params']['image_id'])) {
                    unset($arrSlides[$k]['params']['image_id']);
                }
            }
        }
        if (!empty($arrStaticSlide)) {
            foreach ($arrStaticSlide as $k => $s) {
                if (@RevsliderPrestashop::getIsset($arrStaticSlide[$k]['params']['image_id'])) {
                    unset($arrStaticSlide[$k]['params']['image_id']);
                }
            }
        }

        if (!empty($cfw) && count($cfw) > 0) {
            foreach ($cfw as $key => $slide) {
                if (@RevsliderPrestashop::getIsset($slide['params']['image']) && $slide['params']['image'] != '') {
                    $usedImages[$slide['params']['image']] = true;
                } //['params']['image'] background url
                if (@RevsliderPrestashop::getIsset($slide['params']['background_image']) && $slide['params']['background_image'] != '') {
                    $usedImages[$slide['params']['background_image']] = true;
                } //['params']['image'] background url
                if (@RevsliderPrestashop::getIsset($slide['params']['slide_thumb']) && $slide['params']['slide_thumb'] != '') {
                    $usedImages[$slide['params']['slide_thumb']] = true;
                } //['params']['image'] background url
                //html5 video
                if (@RevsliderPrestashop::getIsset($slide['params']['background_type']) && $slide['params']['background_type'] == 'html5') {
                    if (@RevsliderPrestashop::getIsset($slide['params']['slide_bg_html_mpeg']) && $slide['params']['slide_bg_html_mpeg'] != '') {
                        $usedVideos[$slide['params']['slide_bg_html_mpeg']] = true;
                    }
                    if (@RevsliderPrestashop::getIsset($slide['params']['slide_bg_html_webm']) && $slide['params']['slide_bg_html_webm'] != '') {
                        $usedVideos[$slide['params']['slide_bg_html_webm']] = true;
                    }
                    if (@RevsliderPrestashop::getIsset($slide['params']['slide_bg_html_ogv']) && $slide['params']['slide_bg_html_ogv'] != '') {
                        $usedVideos[$slide['params']['slide_bg_html_ogv']] = true;
                    }
                } else {
                    if (@RevsliderPrestashop::getIsset($slide['params']['slide_bg_html_mpeg']) && $slide['params']['slide_bg_html_mpeg'] != '') {
                        $slide['params']['slide_bg_html_mpeg'] = '';
                    }
                    if (@RevsliderPrestashop::getIsset($slide['params']['slide_bg_html_webm']) && $slide['params']['slide_bg_html_webm'] != '') {
                        $slide['params']['slide_bg_html_webm'] = '';
                    }
                    if (@RevsliderPrestashop::getIsset($slide['params']['slide_bg_html_ogv']) && $slide['params']['slide_bg_html_ogv'] != '') {
                        $slide['params']['slide_bg_html_ogv'] = '';
                    }
                }

                //image thumbnail
                if (@RevsliderPrestashop::getIsset($slide['layers']) && !empty($slide['layers']) && count($slide['layers']) > 0) {
                    foreach ($slide['layers'] as $lKey => $layer) {
                        if (@RevsliderPrestashop::getIsset($layer['style']) && $layer['style'] != '') {
                            $usedCaptions[$layer['style']] = true;
                        }
                        if (@RevsliderPrestashop::getIsset($layer['animation']) && $layer['animation'] != '' && strpos($layer['animation'], 'customin') !== false) {
                            $usedAnimations[str_replace('customin-', '', $layer['animation'])] = true;
                        }
                        if (@RevsliderPrestashop::getIsset($layer['endanimation']) && $layer['endanimation'] != '' && strpos($layer['endanimation'], 'customout') !== false) {
                            $usedAnimations[str_replace('customout-', '', $layer['endanimation'])] = true;
                        }
                        if (@RevsliderPrestashop::getIsset($layer['image_url']) && $layer['image_url'] != '') {
                            $usedImages[$layer['image_url']] = true; //image_url if image caption
                        }

                        if (@RevsliderPrestashop::getIsset($layer['type']) && $layer['type'] == 'video') {
                            $video_data = (@RevsliderPrestashop::getIsset($layer['video_data'])) ? (array) $layer['video_data'] : array();

                            if (!empty($video_data) && @RevsliderPrestashop::getIsset($video_data['video_type']) && $video_data['video_type'] == 'html5') {
                                if (@RevsliderPrestashop::getIsset($video_data['urlPoster']) && $video_data['urlPoster'] != '') {
                                    $usedImages[$video_data['urlPoster']] = true;
                                }

                                if (@RevsliderPrestashop::getIsset($video_data['urlMp4']) && $video_data['urlMp4'] != '') {
                                    $usedVideos[$video_data['urlMp4']] = true;
                                }
                                if (@RevsliderPrestashop::getIsset($video_data['urlWebm']) && $video_data['urlWebm'] != '') {
                                    $usedVideos[$video_data['urlWebm']] = true;
                                }
                                if (@RevsliderPrestashop::getIsset($video_data['urlOgv']) && $video_data['urlOgv'] != '') {
                                    $usedVideos[$video_data['urlOgv']] = true;
                                }
                            } elseif (!empty($video_data) && @RevsliderPrestashop::getIsset($video_data['video_type']) && $video_data['video_type'] != 'html5') { //video cover image
                                if (@RevsliderPrestashop::getIsset($video_data['previewimage']) && $video_data['previewimage'] != '') {
                                    $usedImages[$video_data['previewimage']] = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        $arrSliderExport = array("params" => $sliderParams, "slides" => $arrSlides);
        if (!empty($arrStaticSlide)) {
            $arrSliderExport['static_slides'] = $arrStaticSlide;
        }

        $strExport = serialize($arrSliderExport);

        //$strExportAnim = serialize(RevSliderOperations::getFullCustomAnimations());

        $exportname = (!empty($this->alias)) ? $this->alias . '.zip' : "slider_export.zip";

        //add navigations if not default animation
        if (@RevsliderPrestashop::getIsset($sliderParams['navigation_arrow_style'])) {
            $usedNavigations[$sliderParams['navigation_arrow_style']] = true;
        }
        if (@RevsliderPrestashop::getIsset($sliderParams['navigation_bullets_style'])) {
            $usedNavigations[$sliderParams['navigation_bullets_style']] = true;
        }
        if (@RevsliderPrestashop::getIsset($sliderParams['thumbnails_style'])) {
            $usedNavigations[$sliderParams['thumbnails_style']] = true;
        }
        if (@RevsliderPrestashop::getIsset($sliderParams['tabs_style'])) {
            $usedNavigations[$sliderParams['tabs_style']] = true;
        }
        $navs = false;
        if (!empty($usedNavigations)) {
            $navs = RevSliderNavigation::exportNavigation($usedNavigations);
            if ($navs !== false) {
                $navs = serialize($navs);
            }
        }


        $styles = '';
        if (!empty($usedCaptions)) {
            $captions = array();
            foreach ($usedCaptions as $class => $val) {
                $cap = RevSliderOperations::getCaptionsContentArray($class);
                //set also advanced styles here...
                if (!empty($cap)) {
                    $captions[] = $cap;
                }
            }
            $styles = RevSliderCssParser::parseArrayToCss($captions, "\n", true);
        }

        $animations = '';
        if (!empty($usedAnimations)) {
            $animation = array();
            foreach ($usedAnimations as $anim => $val) {
                $anima = RevSliderOperations::getFullCustomAnimationByID($anim);
                if ($anima !== false) {
                    $animation[] = RevSliderOperations::getFullCustomAnimationByID($anim);
                }
            }
            if (!empty($animation)) {
                $animations = serialize($animation);
            }
        }

        $usedImages = array_merge($usedImages, $usedVideos);

        $usepcl = false;
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive;
            $success = $zip->open(RevSliderGlobals::$urlExportZip, ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE);

            if ($success !== true) {
                throwError("Can't create zip file: " . RevSliderGlobals::$urlExportZip);
            }
        } else {
            //fallback to pclzip
            require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');

            $pclzip = new PclZip(RevSliderGlobals::$urlExportZip);

            //either the function uses die() or all is cool
            $usepcl = true;
        }


        //add images to zip
        if (!empty($usedImages)) {
            $upload_dir = RevSliderFunctionsWP::getPathUploads();
            // $upload_dir_multisiteless = wp_upload_dir();
            // $cont_url = $upload_dir_multisiteless['baseurl'];
            // $cont_url_no_www = str_replace('www.', '', $upload_dir_multisiteless['baseurl']);
            // $upload_dir_multisiteless = $upload_dir_multisiteless['basedir'].'/';

            foreach ($usedImages as $file => $val) {
                if ($useDummy == "true") { //only use dummy images
                } else { //use the real images
                    $zip->addFile($upload_dir . $file, 'images/' . $file);
                }
            }
        }

        if (!$usepcl) {
            $zip->addFromString("slider_export.txt", $strExport); //add slider settings
        } else {
            $list = $pclzip->add(array(array(PCLZIP_ATT_FILE_NAME => 'slider_export.txt', PCLZIP_ATT_FILE_CONTENT => $strExport)));
            if ($list == 0) {
                die("ERROR : '" . $pclzip->errorInfo(true) . "'");
            }
        }
        if (Tools::strlen(trim($animations)) > 0) {
            if (!$usepcl) {
                $zip->addFromString("custom_animations.txt", $animations); //add custom animations
            } else {
                $list = $pclzip->add(array(array(PCLZIP_ATT_FILE_NAME => 'custom_animations.txt', PCLZIP_ATT_FILE_CONTENT => $animations)));
                if ($list == 0) {
                    die("ERROR : '" . $pclzip->errorInfo(true) . "'");
                }
            }
        }
        if (Tools::strlen(trim($styles)) > 0) {
            if (!$usepcl) {
                $zip->addFromString("dynamic-captions.css", $styles); //add dynamic styles
            } else {
                $list = $pclzip->add(array(array(PCLZIP_ATT_FILE_NAME => 'dynamic-captions.css', PCLZIP_ATT_FILE_CONTENT => $styles)));
                if ($list == 0) {
                    die("ERROR : '" . $pclzip->errorInfo(true) . "'");
                }
            }
        }
        if (Tools::strlen(trim($navs)) > 0) {
            if (!$usepcl) {
                $zip->addFromString("navigation.txt", $navs); //add dynamic styles
            } else {
                $list = $pclzip->add(array(array(PCLZIP_ATT_FILE_NAME => 'navigation.txt', PCLZIP_ATT_FILE_CONTENT => $navs)));
                if ($list == 0) {
                    die("ERROR : '" . $pclzip->errorInfo(true) . "'");
                }
            }
        }

        $static_css = RevSliderOperations::getStaticCss();
        if (trim($static_css) !== '') {
            if (!$usepcl) {
                $zip->addFromString("static-captions.css", $static_css); //add slider settings
            } else {
                $list = $pclzip->add(array(array(PCLZIP_ATT_FILE_NAME => 'static-captions.css', PCLZIP_ATT_FILE_CONTENT => $static_css)));
                if ($list == 0) {
                    die("ERROR : '" . $pclzip->errorInfo(true) . "'");
                }
            }
        }
        $enable_slider_pack = false;

        if ($enable_slider_pack) { //allow for slider packs the automatic creation of the info.cfg
            if (!$usepcl) {
                $zip->addFromString('info.cfg', md5($this->alias)); //add slider settings
            } else {
                $list = $pclzip->add(array(array(PCLZIP_ATT_FILE_NAME => 'info.cfg', PCLZIP_ATT_FILE_CONTENT => md5($this->alias))));
                if ($list == 0) {
                    die("ERROR : '" . $pclzip->errorInfo(true) . "'");
                }
            }
        }

        if (!$usepcl) {
            $zip->close();
        } else {
            //do nothing
        }


        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=" . $exportname);
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile(RevSliderGlobals::$urlExportZip);

        @unlink(RevSliderGlobals::$urlExportZip); //delete file after sending it to user

        exit();
    }
    /* public function exportSlider($useDummy = false){

      $export_zip = true;

      if(function_exists("unzip_file") == false){

      if( UniteZipRev::isZipExists() == false)

      $export_zip = false;


      }



      if(!class_exists('ZipArchive')) $export_zip = false;




      if($export_zip){

      $zip = new ZipArchive;

      $success = $zip->open(GlobalsRevSlider::$urlExportZip, ZipArchive::OVERWRITE);



      if($success == false)

      throwError("Can't create zip file: ".GlobalsRevSlider::$urlExportZip);



      $this->validateInited();



      $sliderParams = $this->getParamsForExport();

      $arrSlides = $this->getSlidesForExport($useDummy);



      $arrSliderExport = array("params"=>$sliderParams,"slides"=>$arrSlides);



      $strExport = serialize($arrSliderExport);






      $exportname =(!empty($this->alias)) ? $this->alias.'.zip' : "slider_export.zip";



      $usedCaptions = array();

      $usedAnimations = array();

      $usedImages = array();

      if(!empty($arrSlides) && count($arrSlides) > 0){

      foreach($arrSlides as $key => $slide){

      if(@RevsliderPrestashop::getIsset($slide['params']['image']) && $slide['params']['image'] != '') $usedImages[$slide['params']['image']] = true; //['params']['image'] background url



      if(@RevsliderPrestashop::getIsset($slide['layers']) && !empty($slide['layers']) && count($slide['layers']) > 0){

      foreach($slide['layers'] as $lKey => $layer){

      if(@RevsliderPrestashop::getIsset($layer['style']) && $layer['style'] != '') $usedCaptions[$layer['style']] = true;

      if(@RevsliderPrestashop::getIsset($layer['animation']) && $layer['animation'] != '' && strpos($layer['animation'], 'customin') !== false) $usedAnimations[str_replace('customin-', '', $layer['animation'])] = true;

      if(@RevsliderPrestashop::getIsset($layer['endanimation']) && $layer['endanimation'] != '' && strpos($layer['endanimation'], 'customout') !== false) $usedAnimations[str_replace('customout-', '', $layer['endanimation'])] = true;

      if(@RevsliderPrestashop::getIsset($layer['image_url']) && $layer['image_url'] != '') $usedImages[$layer['image_url']] = true; //image_url if image caption

      }

      }

      }

      }



      $styles = '';

      if(!empty($usedCaptions)){

      $captions = array();

      foreach($usedCaptions as $class => $val){

      $captions[] = RevOperations::getCaptionsContentArray($class);

      }

      $styles = UniteCssParserRev::parseArrayToCss($captions, "\n");

      }



      $animations = '';

      if(!empty($usedAnimations)){

      $animation = array();

      foreach($usedAnimations as $anim => $val){

      $anima = RevOperations::getFullCustomAnimationByID($anim);

      if($anima !== false) $animation[] = RevOperations::getFullCustomAnimationByID($anim);



      }

      if(!empty($animation)) $animations = serialize($animation);

      }




      if(!empty($usedImages)){

      $upload_dir = UniteFunctionsWPRev::getPathUploads();



      foreach($usedImages as $file => $val){

      if($useDummy == "true"){ //only use dummy images



      }else{ //use the real images

      $zip->addFile($upload_dir.$file,'images/'.$file);

      }

      }

      }



      $zip->addFromString("slider_export.txt", $strExport); //add slider settings

      if(Tools::strlen(trim($animations)) > 0) $zip->addFromString("custom_animations.txt", $animations); //add custom animations

      if(Tools::strlen(trim($styles)) > 0) $zip->addFromString("dynamic-captions.css", $styles); //add dynamic styles







      $zip->addFile(GlobalsRevSlider::$filepath_static_captions,'static-captions.css'); //add static styles

      $zip->close();



      header("Content-type: application/zip");

      header("Content-Disposition: attachment; filename=".$exportname);

      header("Pragma: no-cache");

      header("Expires: 0");

      readfile(GlobalsRevSlider::$urlExportZip);



      @unlink(GlobalsRevSlider::$urlExportZip); //delete file after sending it to user

      }else{ //fallback, do old export

      $this->validateInited();



      $sliderParams = $this->getParamsForExport();

      $arrSlides = $this->getSlidesForExport();



      $arrSliderExport = array("params"=>$sliderParams,"slides"=>$arrSlides);



      $strExport = serialize($arrSliderExport);



      if(!empty($this->alias))

      $filename = $this->alias.".txt";

      else

      $filename = "slider_export.txt";



      UniteFunctionsRev::downloadFile($strExport,$filename);

      }

      } */

    public function importSliderFromPost($updateAnim = true, $updateStatic = true, $exactfilepath = false, $is_template = false, $single_slide = false, $updateNavigation = true)
    {
        try {
            $sliderID = UniteFunctionsRev::getPostVariable("sliderid");
            $sliderExists = !empty($sliderID);
            if ($sliderExists) {
                $this->initByID($sliderID);
            }
            if ($exactfilepath !== false) {
                $filepath = $exactfilepath;
            } else {
                switch ($_FILES['import_file']['error']) {
                    case UPLOAD_ERR_OK: // upload error
                        break;
                    case UPLOAD_ERR_NO_FILE: // upload no files
                        RevSliderFunctions::throwError(__('No file sent.', 'revslider'));
                        // no break
                    case UPLOAD_ERR_INI_SIZE:
                        // no break
                    case UPLOAD_ERR_FORM_SIZE:
                        RevSliderFunctions::throwError(__('Exceeded filesize limit.', 'revslider'));
                        // no break
                    default:
                        break;
                }
                $filepath = $_FILES["import_file"]["tmp_name"];
            }

            if (file_exists($filepath) == false) {
                UniteFunctionsRev::throwError("Import file not found!!!");
            }
            //check if zip file or fallback to old, if zip, check if all files exist
            if (!class_exists("ZipArchive")) {
                $importZip = false;
            } else {
                $zip = new ZipArchive;
                $importZip = $zip->open($filepath, ZIPARCHIVE::CREATE);
            }
            if ($importZip === true) { //true or integer. If integer, its not a correct zip file
                //check if files all exist in zip
                $slider_export = $zip->getStream('slider_export.txt');
                $custom_animations = $zip->getStream('custom_animations.txt');
                $dynamic_captions = $zip->getStream('dynamic-captions.css');
                $static_captions = $zip->getStream('static-captions.css');
                $navigations_custom = $zip->getStream('navigation.txt');
                if (!$slider_export) {
                    UniteFunctionsRev::throwError("slider_export.txt does not exist!");
                }
                //if(!$custom_animations)  UniteFunctionsRev::throwError("custom_animations.txt does not exist!");
                //if(!$dynamic_captions) UniteFunctionsRev::throwError("dynamic-captions.css does not exist!");
                //if(!$static_captions)  UniteFunctionsRev::throwError("static-captions.css does not exist!");
                $content = '';
                $animations = '';
                $dynamic = '';
                $static = '';
                $navigations = '';
                while (!feof($slider_export)) {
                    $content .= fread($slider_export, 1024);
                }
                if ($custom_animations) {
                    while (!feof($custom_animations)) {
                        $animations .= fread($custom_animations, 1024);
                    }
                }
                if ($dynamic_captions) {
                    while (!feof($dynamic_captions)) {
                        $dynamic .= fread($dynamic_captions, 1024);
                    }
                }
                if ($static_captions) {
                    while (!feof($static_captions)) {
                        $static .= fread($static_captions, 1024);
                    }
                }
                if ($navigations_custom) {
                    while (!feof($navigations_custom)) {
                        $navigations .= fread($navigations_custom, 1024);
                    }
                }
                fclose($slider_export);
                if ($custom_animations) {
                    fclose($custom_animations);
                }
                if ($dynamic_captions) {
                    fclose($dynamic_captions);
                }
                if ($static_captions) {
                    fclose($static_captions);
                }
                if ($navigations_custom) {
                    fclose($navigations_custom);
                }
                //check for images!
            } else { //check if fallback
                //get content array
                $content = @Tools::file_get_contents($filepath);
            }
            if ($importZip === true) { //we have a zip
                $db = new UniteDBRev();
                //update/insert custom animations
                $animations = @unserialize($animations);
                if (!empty($animations)) {
                    foreach ($animations as $key => $animation) { //$animation['id'], $animation['handle'], $animation['params']
                        $exist = $db->fetch(GlobalsRevSlider::$table_layer_anims, "handle = '" . $animation['handle'] . "'");
                        if (!empty($exist)) { //update the animation, get the ID
                            if ($updateAnim == "true") { //overwrite animation if exists
                                $arrUpdate = array();
                                $arrUpdate['params'] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $animation['params'])));
                                $db->update(GlobalsRevSlider::$table_layer_anims, $arrUpdate, array('handle' => $animation['handle']));
                                $id = $exist['0']['id'];
                            } else { //insert with new handle
                                $arrInsert = array();
                                $arrInsert["handle"] = 'copy_' . $animation['handle'];
                                $arrInsert["params"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $animation['params'])));
                                $id = $db->insert(GlobalsRevSlider::$table_layer_anims, $arrInsert);
                            }
                        } else { //insert the animation, get the ID
                            $arrInsert = array();
                            $arrInsert["handle"] = $animation['handle'];
                            $arrInsert["params"] = Tools::stripslashes(Tools::jsonEncode(str_replace("'", '"', $animation['params'])));
                            $id = $db->insert(GlobalsRevSlider::$table_layer_anims, $arrInsert);
                        }
                        //and set the current customin-oldID and customout-oldID in slider params to new ID from $id
                        $content = str_replace(array('customin-' . $animation['id'], 'customout-' . $animation['id']), array('customin-' . $id, 'customout-' . $id), $content);
                    }
                    dmp(__("animations imported!", REVSLIDER_TEXTDOMAIN));
                } else {
                    dmp(__("no custom animations found, if slider uses custom animations, the provided export may be broken...", REVSLIDER_TEXTDOMAIN));
                }
                //overwrite/append static-captions.css
                if (!empty($static)) {
                    if ($updateStatic == "true") { //overwrite file
                        RevOperations::updateStaticCss($static);
                    } else { //append
                        $static_cur = RevOperations::getStaticCss();
                        $static = $static_cur . "\n" . $static;
                        RevOperations::updateStaticCss($static);
                    }
                }
                //overwrite/create dynamic-captions.css
                //parse css to classes
                $dynamicCss = UniteCssParserRev::parseCssToArray($dynamic);
                if (is_array($dynamicCss) && $dynamicCss !== false && count($dynamicCss) > 0) {
                    foreach ($dynamicCss as $class => $styles) {
                        //check if static style or dynamic style
                        $class = trim($class);
                        if ((strpos($class, ':hover') === false && strpos($class, ':') !== false) || //before, after
                            strpos($class, " ") !== false || // .tp-caption.imageclass img or .tp-caption .imageclass or .tp-caption.imageclass .img
                            strpos($class, ".tp-caption") === false || // everything that is not tp-caption
                            (strpos($class, ".") === false || strpos($class, "#") !== false) || // no class -> #ID or img
                            strpos($class, ">") !== false) { //.tp-caption>.imageclass or .tp-caption.imageclass>img or .tp-caption.imageclass .img
                            continue;
                        }
                        //is a dynamic style
                        if (strpos($class, ':hover') !== false) {
                            $class = trim(str_replace(':hover', '', $class));
                            $arrInsert = array();
                            $arrInsert["hover"] = Tools::jsonEncode($styles);
                            $arrInsert["settings"] = Tools::jsonEncode(array('hover' => 'true'));
                        } else {
                            $arrInsert = array();
                            $arrInsert["params"] = Tools::jsonEncode($styles);
                        }
                        //check if class exists
                        $result = $db->fetch(GlobalsRevSlider::$table_css, "handle = '" . $class . "'");
                        if (!empty($result)) { //update
                            $db->update(GlobalsRevSlider::$table_css, $arrInsert, array('handle' => $class));
                        } else { //insert
                            $arrInsert["handle"] = $class;
                            $db->insert(GlobalsRevSlider::$table_css, $arrInsert);
                        }
                    }
                    dmp(__("dynamic styles imported!", REVSLIDER_TEXTDOMAIN));
                } else {
                    dmp(__("no dynamic styles found, if slider uses dynamic styles, the provided export may be broken...", REVSLIDER_TEXTDOMAIN));
                }

                //update/insert custom animations
                // if (!empty($navigations)) {
                // 	$navigations = str_replace("\\\\\\\\", "\\\\", $navigations);
                // 	$navigations = str_replace("\\\\\\\"", "\\\"", $navigations);
                // }
                $navigations = @unserialize($navigations);
                if (!empty($navigations)) {
                    foreach ($navigations as $key => $navigation) {
                        $exist = $db->fetch(RevSliderGlobals::$table_navigation, "handle = '" . $navigation['handle'] . "'");
                        unset($navigation['id']);

                        $rh = $navigation["handle"];
                        if (!empty($exist)) { //create new navigation, get the ID
                            if ($updateNavigation == "true") { //overwrite navigation if exists
                                unset($navigation['handle']);
                                $db->update(RevSliderGlobals::$table_navigation, $navigation, array('handle' => $rh));
                            } else {
                                //insert with new handle
                                $navigation["handle"] = $navigation['handle'] . '-' . date('is');
                                $navigation["name"] = $navigation['name'] . '-' . date('is');
                                $content = str_replace($rh . '"', $navigation["handle"] . '"', $content);
                                $navigation["css"] = str_replace("\\\\\\\\", "\\\\", $navigation["css"]);
                                $navigation["css"] = str_replace("\\\\\\\"", "\\\"", $navigation["css"]);
                                $navigation["css"] = str_replace('.' . $rh, '.' . $navigation["handle"], $navigation["css"]); //change css class to the correct new class
                                $navi_id = $db->insert(RevSliderGlobals::$table_navigation, $navigation);
                            }
                        } else {
                            $navigation["css"] = str_replace("\\\\\\\\", "\\\\", $navigation["css"]);
                            $navigation["css"] = str_replace("\\\\\\\"", "\\\"", $navigation["css"]);

                            $navi_id = $db->insert(RevSliderGlobals::$table_navigation, $navigation);
                        }
                    }
                    dmp(__("navigations imported!", 'revslider'));
                }
            }
//          $content = preg_replace('!s:(\d+):"(.*?)";!', "'s:'.Tools::strlen('$2').':\"$2\";'", $content); //clear errors in string
            $arrSlider = @unserialize($content);
            if (empty($arrSlider)) {
                $content = preg_replace_callback('!s:(\d+):"(.*?)";!', create_function('$match', 'return "s:".Tools::strlen($match[2]).":\"{$match[2]}\";";'), $content); //clear errors in string
                $arrSlider = @unserialize($content);
            }
            if (empty($arrSlider)) {
                UniteFunctionsRev::throwError("Wrong export slider file format! This could be caused because the ZipArchive extension is not enabled.");
            }
            //update slider params
            $sliderParams = $arrSlider["params"];
            $sliderParams["displayhook"] = '';
            $sliderParams["id_shop"] = Context::getcontext()->shop->id;
            if ($sliderExists) {
                $sliderParams["title"] = $this->arrParams["title"];
                $sliderParams["alias"] = $this->arrParams["alias"];
                $sliderParams["id_shop"] = Context::getcontext()->shop->id;
            }
            if (@RevsliderPrestashop::getIsset($sliderParams["background_image"])) {
                $sliderParams["background_image"] = UniteFunctionsWPRev::getImageUrlFromPath($sliderParams["background_image"]);
            }
            $json_params = Tools::jsonEncode($sliderParams);
            //update slider or craete new
            if ($sliderExists) {
                $arrUpdate = array("params" => $json_params);
                $this->db->update(GlobalsRevSlider::$table_sliders, $arrUpdate, array("id" => $sliderID));
            } else {    //new slider
                $arrInsert = array();
                $arrInsert["params"] = $json_params;
                $arrInsert["title"] = UniteFunctionsRev::getVal($sliderParams, "title", "Slider1");
                $arrInsert["alias"] = UniteFunctionsRev::getVal($sliderParams, "alias", "slider1");
                $sliderID = $this->db->insert(GlobalsRevSlider::$table_sliders, $arrInsert);
            }
            //-------- Slides Handle -----------
            //delete current slides
            if ($sliderExists) {
                $this->deleteAllSlides();
            }
            //create all slides
            $arrSlides = $arrSlider["slides"];
            $alreadyImported = array();
            foreach ($arrSlides as $slide) {
                $params = $slide["params"];
                $layers = $slide["layers"];

                //convert params images:
                if (@RevsliderPrestashop::getIsset($params["image"])) {
                    //import if exists in zip folder
                    if (trim($params["image"]) !== '') {
                        if ($importZip === true) { //we have a zip, check if exists
                            $image = $zip->getStream('images/' . $params["image"]);
                            if (!$image) {
                                echo $params["image"] . ' not found!<br>';
                            } else {
                                if (!@RevsliderPrestashop::getIsset($alreadyImported['zip://' . $filepath . "#" . 'images/' . $params["image"]])) {
                                    //$importImage = UniteFunctionsWPRev::importMedia('zip://'.$filepath."#".ABSPATH.'/'.$params["image"], $sliderParams["alias"].'/');
                                    $importImage = UniteFunctionsWPRev::importMedia('zip://' . $filepath . "#" . 'images/' . $params["image"]);
                                    if ($importImage !== false) {
                                        $alreadyImported['zip://' . $filepath . "#" . 'images/' . $params["image"]] = $importImage['path'];
                                        $params["image"] = $importImage['path'];
                                    }
                                } else {
                                    $params["image"] = $alreadyImported['zip://' . $filepath . "#" . 'images/' . $params["image"]];
                                }
                            }
                        }
                    }
                    $params["image"] = UniteFunctionsWPRev::getImageUrlFromPath($params["image"]);
                    if ($rev_image_id = get_image_id_by_url($params["image"])) {
                        $params["image_id"] = $rev_image_id;
                    }
                }
                //convert layers images:
                foreach ($layers as $key => $layer) {
                    if (@RevsliderPrestashop::getIsset($layer["image_url"])) {
                        //import if exists in zip folder
                        if (trim($layer["image_url"]) !== '') {
                            if ($importZip === true) { //we have a zip, check if exists
                                $image_url = $zip->getStream('images/' . $layer["image_url"]);
                                if (!$image_url) {
                                    echo $layer["image_url"] . ' not found!<br>';
                                } else {
                                    if (!@RevsliderPrestashop::getIsset($alreadyImported['zip://' . $filepath . "#" . 'images/' . $layer["image_url"]])) {
                                        $importImage = UniteFunctionsWPRev::importMedia('zip://' . $filepath . "#" . 'images/' . $layer["image_url"]);
                                        if ($importImage !== false) {
                                            $alreadyImported['zip://' . $filepath . "#" . 'images/' . $layer["image_url"]] = $importImage['path'];
                                            $layer["image_url"] = $importImage['path'];
                                        }
                                    } else {
                                        $layer["image_url"] = $alreadyImported['zip://' . $filepath . "#" . 'images/' . $layer["image_url"]];
                                    }
                                }
                            }
                        }
                        $layer["image_url"] = UniteFunctionsWPRev::getImageUrlFromPath($layer["image_url"]);
                        $layers[$key] = $layer;
                    }
                }
                //create new slide
                $arrCreate = array();
                $arrCreate["slider_id"] = $sliderID;
                $arrCreate["slide_order"] = $slide["slide_order"];
                $arrCreate["layers"] = Tools::jsonEncode($layers);
                $arrCreate["params"] = Tools::jsonEncode($params);
                $this->db->insert(GlobalsRevSlider::$table_slides, $arrCreate);
            }

            $this->initByID($sliderID);
            $c_slider = $this;
            RevSliderPluginUpdate::updateCssStyles(); //set to version 5
            RevSliderPluginUpdate::addAnimationSettingsToLayer($c_slider); //set to version 5
            RevSliderPluginUpdate::addStyleSettingsToLayer($c_slider); //set to version 5
            RevSliderPluginUpdate::changeSettingsOnLayers($c_slider); //set to version 5
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            return(array("success" => false, "error" => $errorMessage, "sliderID" => $sliderID));
        }

        return(array("success" => true, "sliderID" => $sliderID));
    }

    /**
     * 
     * update slider from options
     */
    public function updateSliderFromOptions($options)
    {
        $sliderID = RevSliderFunctions::getVal($options, "sliderid");
        RevSliderFunctions::validateNotEmpty($sliderID, "Slider ID");

        $this->createUpdateSliderFromOptions($options, $sliderID);
    }

    private function updateParam($arrUpdate)
    {
        $this->validateInited();



        $this->arrParams = array_merge($this->arrParams, $arrUpdate);

        $jsonParams = Tools::jsonEncode($this->arrParams);

        $arrUpdateDB = array();

        $arrUpdateDB["params"] = $jsonParams;



        $this->db->update(GlobalsRevSlider::$table_sliders, $arrUpdateDB, array("id" => $this->id));
    }

    public function deleteSliderFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "sliderid");

        UniteFunctionsRev::validateNotEmpty($sliderID, "Slider ID");

        $this->initByID($sliderID);



        //check if template

        $isTemplate = $this->getParam("template", "false");

        if ($isTemplate == "true") {

            //check if template is used by other post sliders

            $stillUsing = array();

            $arrSliders = $this->getArrSliders();

            if (!empty($arrSliders)) {
                foreach ($arrSliders as $slider) {
                    if ($slider->isSlidesFromPosts() && $slider->getParam("slider_template_id", false) !== false) {
                        $stillUsing[] = $slider->getParam("title");
                    }
                }
            }

            if (!empty($stillUsing)) {
                return $stillUsing;
            } //if not empty, template is used by other sliders! Name which ones
        }



        $this->deleteSlider();



        return true;
    }

    public function duplicateSliderFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "sliderid");

        UniteFunctionsRev::validateNotEmpty($sliderID, "Slider ID");

        $this->initByID($sliderID);

        $this->duplicateSlider(RevSliderFunctions::getVal($data, "title"));
    }

    public function duplicateSlideFromData($data)
    {



        //init the slider
        $sliderID = RevSliderFunctions::getVal($data, "sliderID");
        RevSliderFunctions::validateNotEmpty($sliderID, "Slider ID");
        $this->initByID($sliderID);

        //get the slide id
        $slideID = RevSliderFunctions::getVal($data, "slideID");
        RevSliderFunctions::validateNotEmpty($slideID, "Slide ID");
        $newSlideID = $this->duplicateSlide($slideID);

        $this->duplicateChildren($slideID, $newSlideID);

        return(array($sliderID, $newSlideID));
    }

    private function duplicateChildren($slideID, $newSlideID)
    {
        $arrChildren = $this->getArrSlideChildren($slideID);



        foreach ($arrChildren as $childSlide) {
            $childSlideID = $childSlide->getID();

            //duplicate

            $duplicatedSlideID = $this->duplicateSlide($childSlideID);



            //update parent id

            $duplicatedSlide = new RevSlide();

            $duplicatedSlide->initByID($duplicatedSlideID);

            $duplicatedSlide->updateParentSlideID($newSlideID);
        }
    }

    /**
     * copy slide from one Slider to the given Slider ID
     * @since: 5.0
     */
    public function copySlideToSlider($data)
    {
        $wpdb = RevsliderPrestashop::$wpdb;

        $sliderID = (int) (RevSliderFunctions::getVal($data, "slider_id"));
        RevSliderFunctions::validateNotEmpty($sliderID, "Slider ID");
        $slideID = (int) (RevSliderFunctions::getVal($data, "slide_id"));
        RevSliderFunctions::validateNotEmpty($slideID, "Slide ID");

        $tableSliders = $wpdb->prefix . RevSliderGlobals::TABLE_SLIDERS_NAME;
        $tableSlides = $wpdb->prefix . RevSliderGlobals::TABLE_SLIDES_NAME;

        //check if ID exists
        $add_to_slider = $wpdb->getRow("SELECT * FROM $tableSliders WHERE id = $sliderID", ARRAY_A);

        if (empty($add_to_slider)) {
            return __('Slide could not be duplicated', 'revslider');
        }

        //get last slide in slider for the order
        $slide_order = $wpdb->getRow("SELECT * FROM $tableSlides WHERE slider_id = $sliderID ORDER BY slide_order DESC", ARRAY_A);
        $order = (empty($slide_order)) ? 1 : $slide_order['slide_order'] + 1;

        $slide_to_copy = $wpdb->getRow("SELECT * FROM $tableSlides WHERE id = $slideID", ARRAY_A);

        if (empty($slide_to_copy)) {
            return __('Slide could not be duplicated', 'revslider');
        }

        unset($slide_to_copy['id']); //remove the ID of Slide, as it will be a new Slide
        $slide_to_copy['slider_id'] = $sliderID; //set the new Slider ID to the Slide
        $slide_to_copy['slide_order'] = $order; //set the next slide order, to set slide to the end

        $response = $wpdb->insert($tableSlides, $slide_to_copy);

        if ($response === false) {
            return __('Slide could not be copied', 'revslider');
        }

        return true;
    }

    public function copyMoveSlideFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "sliderID");

        UniteFunctionsRev::validateNotEmpty($sliderID, "Slider ID");

        $this->initByID($sliderID);



        $targetSliderID = UniteFunctionsRev::getVal($data, "targetSliderID");

        UniteFunctionsRev::validateNotEmpty($sliderID, "Target Slider ID");

        $this->initByID($sliderID);







        if ($targetSliderID == $sliderID) {
            UniteFunctionsRev::throwError("The target slider can't be equal to the source slider");
        }



        $slideID = UniteFunctionsRev::getVal($data, "slideID");



        UniteFunctionsRev::validateNotEmpty($slideID, "Slide ID");



        $operation = UniteFunctionsRev::getVal($data, "operation");



        $this->copyMoveSlide($slideID, $targetSliderID, $operation);



        return($sliderID);
    }

    /**
     * create a slide from input data
     */
    public function createSlideFromData($data, $returnSlideID = false)
    {
        $sliderID = RevSliderFunctions::getVal($data, "sliderid");
        $obj = RevSliderFunctions::getVal($data, "obj");

        RevSliderFunctions::validateNotEmpty($sliderID, "Slider ID");
        $this->initByID($sliderID);

        if (is_array($obj)) {    //multiple
            foreach ($obj as $item) {
                $slide = new RevSlide();
                $slideID = $slide->createSlide($sliderID, $item);
            }

            return(count($obj));
        } else {    //signle
            $urlImage = $obj;
            $slide = new RevSlide();
            $slideID = $slide->createSlide($sliderID, $urlImage);
            if ($returnSlideID == true) {
                return($slideID);
            } else {
                return(1);
            }    //num slides -1 slide created
        }
    }

    public function updateSlidesOrderFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "sliderID");

        $arrIDs = UniteFunctionsRev::getVal($data, "arrIDs");

        UniteFunctionsRev::validateNotEmpty($arrIDs, "slides");



        $this->initByID($sliderID);



//			$isFromPosts = $this->isSlidesFromPosts();



        foreach ($arrIDs as $index => $slideID) {
            $order = $index + 1;

            $arrUpdate = array("slide_order" => $order);

            $where = array("id" => $slideID);

            $this->db->update(GlobalsRevSlider::$table_slides, $arrUpdate, $where);
        }//end foreach
        //update sortby
//			if(@RevsliderPrestashop::getIsset($isFromPosts) && $isFromPosts){
//
//				$arrUpdate = array();
//
//				$arrUpdate["post_sortby"] = UniteFunctionsWPRev::SORTBY_MENU_ORDER;
//
//				$this->updateParam($arrUpdate);
//
//			} 
    }

    public function getSettingsFields()
    {
        $this->validateInited();



        $arrMain = array();

        $arrMain["title"] = $this->title;

        $arrMain["alias"] = $this->alias;



        $arrRespose = array("main" => $arrMain,
            "params" => $this->arrParams);



        return($arrRespose);
    }

    /**
     * get all used fonts in the current Slider
     * @since: 5.1.0
     */
    public function getUsedFonts($full = false)
    {
        $this->validateInited();
        $gf = array();
        $mslides = $this->getSlides(true);
        if (!empty($mslides)) {
            foreach ($mslides as $key => $ms) {

                $mf = $ms->getUsedFonts($full);
                if (!empty($mf)) {
                    foreach ($mf as $mfk => $mfv) {
                        if (!@RevsliderPrestashop::getIsset($gf[$mfk])) {
                            $gf[$mfk] = $mfv;
                        } else {
                            foreach ($mfv['variants'] as $mfvk => $mfvv) {
                                $gf[$mfk]['variants'][$mfvk] = true;
                            }
                        }
                        $gf[$mfk]['slide'][] = array('id' => $ms->getID(), 'title' => $ms->getTitle());
                    }
                }
            }
        }

        return $gf;
    }

    /**
     * get slides from gallery
     * force from gallery - get the slide from the gallery only
     */
    public function getSlides($publishedOnly = false)
    {
        $arrSlides = $this->getSlidesFromGallery($publishedOnly);

        return($arrSlides);
    }

    private function getSlidesFromPosts($publishedOnly = false)
    {
        $slideTemplates = $this->getSlidesFromGallery($publishedOnly);


        $slideTemplates = UniteFunctionsRev::assocToArray($slideTemplates);

        if (count($slideTemplates) == 0) {
            return array();
        }

        $sourceType = $this->getParam("source_type", "gallery");

        switch ($sourceType) {

            case "posts":

                $arrPosts = $this->getPostsFromCategoies($publishedOnly);

                break;

            case "specific_posts":

                $arrPosts = $this->getPostsFromSpecificList();

                break;

            default:

                UniteFunctionsRev::throwError("getSlidesFromPosts error: This source type must be from posts.");

                break;
        }

        $arrSlides = array();

        $templateKey = 0;

        $numTemplates = count($slideTemplates);

        $slideTemplate = $slideTemplates[$templateKey];

        foreach ($arrPosts as $postData) {

            //advance the templates

            $templateKey++;

            if ($templateKey == $numTemplates) {
                $templateKey = 0;
            }

            $slide = new RevSlide();

            $slide->initByPostData($postData, $slideTemplate, $this->id);

            $arrSlides[] = $slide;
        }

        $this->arrSlides = $arrSlides;

        return($arrSlides);
    }

    /**
     * get slides from posts
     */
    public function getSlidesFromStream($publishedOnly = false)
    {
        $slideTemplates = $this->getSlidesFromGallery($publishedOnly);
        $slideTemplates = RevSliderFunctions::assocToArray($slideTemplates);

        if (count($slideTemplates) == 0) {
            return array();
        }

        $arrPosts = array();

        $max_allowed = 999999;
        $sourceType = $this->getParam("source_type", "gallery");
        $additions = array('fb_type' => 'album');
        switch ($sourceType) {
            case "facebook":
                $facebook = new RevSliderFacebook($this->getParam('facebook-transient', '1200'));
                if ($this->getParam('facebook-type-source', 'timeline') == "album") {
                    $arrPosts = $facebook->get_photo_set_photos($this->getParam('facebook-album'), $this->getParam('facebook-count', 10), $this->getParam('facebook-app-id'), $this->getParam('facebook-app-secret'));
                } else {
                    $user_id = $facebook->get_user_from_url($this->getParam('facebook-page-url'));
                    $arrPosts = $facebook->get_photo_feed($user_id, $this->getParam('facebook-app-id'), $this->getParam('facebook-app-secret'), $this->getParam('facebook-count', 10));
                    $additions['fb_type'] = $this->getParam('facebook-type-source', 'timeline');
                    $additions['fb_user_id'] = $user_id;
                }

                if (!empty($arrPosts)) {
                    foreach ($arrPosts as $k => $p) {
                        if (!@RevsliderPrestashop::getIsset($p->status_type)) {
                            continue;
                        }

                        if (in_array($p->status_type, array("wall_post"))) {
                            unset($arrPosts[$k]);
                        }
                    }
                }
                $max_posts = $this->getParam('facebook-count', '25', self::FORCE_NUMERIC);
                $max_allowed = 25;
                break;
            case "twitter":
                $twitter = new RevSliderTwitter($this->getParam('twitter-consumer-key'), $this->getParam('twitter-consumer-secret'), $this->getParam('twitter-access-token'), $this->getParam('twitter-access-secret'), $this->getParam('twitter-transient', '1200'));
                $arrPosts = $twitter->get_public_photos($this->getParam('twitter-user-id'), $this->getParam('twitter-include-retweets'), $this->getParam('twitter-exclude-replies'), $this->getParam('twitter-count'), $this->getParam('twitter-image-only'));
                $max_posts = $this->getParam('twitter-count', '500', self::FORCE_NUMERIC);
                $max_allowed = 500;
                $additions['twitter_user'] = $this->getParam('twitter-user-id');
                break;
            case "instagram":
                $instagram = new RevSliderInstagram($this->getParam('instagram-access-token'), $this->getParam('instagram-transient', '1200'));
                $search_user_id = $this->getParam('instagram-user-id');
                $arrPosts = $instagram->get_public_photos($search_user_id, $this->getParam('instagram-count'));
                $max_posts = $this->getParam('instagram-count', '33', self::FORCE_NUMERIC);
                $max_allowed = 33;
                break;
            case "flickr":
                $flickr = new RevSliderFlickr($this->getParam('flickr-api-key'), $this->getParam('flickr-transient', '1200'));
                switch ($this->getParam('flickr-type')) {
                    case 'publicphotos':
                        $user_id = $flickr->get_user_from_url($this->getParam('flickr-user-url'));
                        $arrPosts = $flickr->get_public_photos($user_id, $this->getParam('flickr-count'));
                        break;
                    case 'gallery':
                        $gallery_id = $flickr->get_gallery_from_url($this->getParam('flickr-gallery-url'));
                        $arrPosts = $flickr->get_gallery_photos($gallery_id, $this->getParam('flickr-count'));
                        break;
                    case 'group':
                        $group_id = $flickr->get_group_from_url($this->getParam('flickr-group-url'));
                        $arrPosts = $flickr->get_group_photos($group_id, $this->getParam('flickr-count'));
                        break;
                    case 'photosets':
                        $arrPosts = $flickr->get_photo_set_photos($this->getParam('flickr-photoset'), $this->getParam('flickr-count'));
                        break;
                }
                $max_posts = $this->getParam('flickr-count', '99', self::FORCE_NUMERIC);
                break;
            case 'youtube':
                $channel_id = $this->getParam('youtube-channel-id');
                $youtube = new RevSliderYoutube($this->getParam('youtube-api'), $channel_id, $this->getParam('youtube-transient', '1200'));

                if ($this->getParam('youtube-type-source') == "playlist") {
                    $arrPosts = $youtube->show_playlist_videos($this->getParam('youtube-playlist'), $this->getParam('youtube-count'));
                } else {
                    $arrPosts = $youtube->show_channel_videos($this->getParam('youtube-count'));
                }
                $additions['yt_type'] = $this->getParam('youtube-type-source', 'channel');
                $max_posts = $this->getParam('youtube-count', '25', self::FORCE_NUMERIC);
                $max_allowed = 50;
                break;
            case 'vimeo':
                $vimeo = new RevSliderVimeo($this->getParam('vimeo-transient', '1200'));
                $vimeo_type = $this->getParam('vimeo-type-source');

                switch ($vimeo_type) {
                    case 'user':
                        $arrPosts = $vimeo->get_vimeo_videos($vimeo_type, $this->getParam('vimeo-username'));
                        break;
                    case 'channel':
                        $arrPosts = $vimeo->get_vimeo_videos($vimeo_type, $this->getParam('vimeo-channelname'));
                        break;
                    case 'group':
                        $arrPosts = $vimeo->get_vimeo_videos($vimeo_type, $this->getParam('vimeo-groupname'));
                        break;
                    case 'album':
                        $arrPosts = $vimeo->get_vimeo_videos($vimeo_type, $this->getParam('vimeo-albumid'));
                        break;
                    default:
                        break;
                }
                $additions['vim_type'] = $this->getParam('vimeo-type-source', 'user');
                $max_posts = $this->getParam('vimeo-count', '25', self::FORCE_NUMERIC);
                $max_allowed = 60;
                break;
            default:
                RevSliderFunctions::throwError("getSlidesFromStream error: This source type must be from stream.");
                break;
        }

        if ($max_posts < 0) {
            $max_posts *= -1;
        }


        while (count($arrPosts) > $max_posts || count($arrPosts) > $max_allowed) {
            array_pop($arrPosts);
        }

        $arrSlides = array();

        $templateKey = 0;
        $numTemplates = count($slideTemplates);

        if (empty($arrPosts)) {
            RevSliderFunctions::throwError(__('Failed to load Stream', 'revslider'));
        }

        foreach ($arrPosts as $postData) {
            $slideTemplate = $slideTemplates[$templateKey];


            //advance the templates
            $templateKey++;
            if ($templateKey == $numTemplates) {
                $templateKey = 0;
            }

            $slide = new RevSlide();
            $slide->initByStreamData($postData, $slideTemplate, $this->id, $sourceType, $additions);

            $arrSlides[] = $slide;
        }

        $this->arrSlides = $arrSlides;

        return($arrSlides);
    }

    public function getSlidesFromGallery($publishedOnly = false, $allwpml = false)
    {
        $this->validateInited();

        $arrSlides = array();

        $arrSlideRecords = $this->db->fetch(GlobalsRevSlider::$table_slides, "slider_id=" . $this->id, "slide_order");

        $arrIdsAssoc = $arrChildren = array();

        if (!empty($arrSlideRecords)) {

            foreach ($arrSlideRecords as $record) {
                $slide = new RevSlide();

                $slide->initByData($record);

                $slideID = $slide->getID();

                $arrIdsAssoc[$slideID] = true;

                if ($publishedOnly == true) {
                    $state = $slide->getParam("state", "published");

                    if ($state == "unpublished") {
                        continue;
                    }
                }

                $parentID = $slide->getParam("parentid", "");

                if (!empty($parentID)) {
                    $lang = $slide->getParam("lang", "");

                    if (!@RevsliderPrestashop::getIsset($arrChildren[$parentID])) {
                        $arrChildren[$parentID] = array();
                    }

                    $arrChildren[$parentID][] = $slide;
                    if (!$allwpml) {
                        continue; //skip adding to main list
                    }
                }



                //init the children array

                $slide->setArrChildren(array());



                $arrSlides[$slideID] = $slide;
            }
        }



        //add children array to the parent slides

        foreach ($arrChildren as $parentID => $arr) {
            if (!@RevsliderPrestashop::getIsset($arrSlides[$parentID])) {
                continue;
            }

            $arrSlides[$parentID]->setArrChildren($arr);
        }



        $this->arrSlides = $arrSlides;

        return($arrSlides);
    }

    public function getArrSlidesFromGalleryShort()
    {
        $arrSlides = $this->getSlidesFromGallery();

        $arrOutput = array();

        $coutner = 0;

        foreach ($arrSlides as $slide) {
            $slideID = $slide->getID();

            $outputName = "Slide $coutner";

            $title = $slide->getParam("title", "");

            $coutner++;



            if (!empty($title)) {
                $outputName .= " - ($title)";
            }



            $arrOutput[$slideID] = $outputName;
        }



        return($arrOutput);
    }

    /**
     * 
     * get slides for output
     * one level only without children
     */
    public function getSlidesForOutput($publishedOnly = false, $lang = 'all')
    {
        $isSlidesFromPosts = $this->isSlidesFromPosts();
        $isSlidesFromStream = $this->isSlidesFromStream();

        if ($isSlidesFromPosts) {
            $arrParentSlides = $this->getSlidesFromPosts($publishedOnly);
        } elseif ($isSlidesFromStream !== false) {
            $arrParentSlides = $this->getSlidesFromStream($publishedOnly);
        } else {
            $arrParentSlides = $this->getSlides($publishedOnly);
        }

        if ($lang == 'all' || $isSlidesFromPosts || $isSlidesFromStream) {
            return($arrParentSlides);
        }

        $arrSlides = array();
        foreach ($arrParentSlides as $parentSlide) {
            $parentLang = $parentSlide->getLang();
            if ($parentLang == $lang) {
                $arrSlides[] = $parentSlide;
            }

            $childAdded = false;
            $arrChildren = $parentSlide->getArrChildren();
            foreach ($arrChildren as $child) {
                $childLang = $child->getLang();
                if ($childLang == $lang) {
                    $arrSlides[] = $child;
                    $childAdded = true;
                    break;
                }
            }

            if ($childAdded == false && $parentLang == "all") {
                $arrSlides[] = $parentSlide;
            }
        }

        return($arrSlides);
    }

    public function getArrSlideNames()
    {
        if (empty($this->arrSlides)) {
            $this->getSlidesFromGallery();
        }



        $arrSlideNames = array();



        foreach ($this->arrSlides as $number => $slide) {
            $slideID = $slide->getID();

            $filename = $slide->getImageFilename();

            $slideTitle = $slide->getParam("title", "Slide");

            $slideName = $slideTitle;

            if (!empty($filename)) {
                $slideName .= " ($filename)";
            }



            $arrChildrenIDs = $slide->getArrChildrenIDs();



            $arrSlideNames[$slideID] = array("name" => $slideName, "arrChildrenIDs" => $arrChildrenIDs, "title" => $slideTitle);
        }

        return($arrSlideNames);
    }

    public function getSlidesNumbersByIDs($publishedOnly = false)
    {
        if (empty($this->arrSlides)) {
            $this->getSlides($publishedOnly);
        }



        $arrSlideNumbers = array();



        $counter = 0;



        if (empty($this->arrSlides)) {
            return $arrSlideNumbers;
        }



        foreach ($this->arrSlides as $slide) {
            $counter++;

            $slideID = $slide->getID();

            $arrSlideNumbers[$slideID] = $counter;
        }

        return($arrSlideNumbers);
    }

    private function getParamsForExport()
    {
        $exportParams = $this->arrParams;



        //modify background image

        $urlImage = UniteFunctionsRev::getVal($exportParams, "background_image");

        if (!empty($urlImage)) {
            $exportParams["background_image"] = $urlImage;
        }



        return($exportParams);
    }

    public function getSlidesForExport($useDummy = false)
    {
        $arrSlides = $this->getSlidesFromGallery();

        $arrSlidesExport = array();

        foreach ($arrSlides as $slide) {
            $slideNew = array();

            $slideNew["params"] = $slide->getParamsForExport();

            $slideNew["slide_order"] = $slide->getOrder();

            $slideNew["layers"] = $slide->getLayersForExport($useDummy);

            $arrSlidesExport[] = $slideNew;
        }



        return($arrSlidesExport);
    }

    public function getStaticSlideForExport($useDummy = false)
    {
        $arrSlidesExport = array();

        $slide = new RevSlide();

        $staticID = $slide->getStaticSlideID($this->id);
        if ($staticID !== false) {
            $slideNew = array();
            $slide->initByStaticID($staticID);
            $slideNew["params"] = $slide->getParamsForExport();
            $slideNew["slide_order"] = $slide->getOrder();
            $slideNew["layers"] = $slide->getLayersForExport($useDummy);
            $arrSlidesExport[] = $slideNew;
        }

        return($arrSlidesExport);
    }

    public function getNumSlides($publishedOnly = false)
    {
        if ($this->arrSlides == null) {
            $this->getSlides($publishedOnly);
        }

        $numSlides = count($this->arrSlides);

        return($numSlides);
    }

    /**
     * get real slides number, from posts, social streams ect.
     */
    public function getNumRealSlides($publishedOnly = false, $type = 'post')
    {
        $numSlides = count($this->arrSlides);

        switch ($type) {
            case 'post':
                if ($this->getParam('fetch_type', 'cat_tag') == 'next_prev') {
                    $numSlides = 2;
                } else {
                    $this->getSlidesFromPosts($publishedOnly);
                    $numSlides = count($this->arrSlides);
                }
                break;
            case 'facebook':
                $numSlides = $this->getParam('facebook-count', count($this->arrSlides));
                break;
            case 'twitter':
                $numSlides = $this->getParam('twitter-count', count($this->arrSlides));
                break;
            case 'instagram':
                $numSlides = $this->getParam('instagram-count', count($this->arrSlides));
                break;
            case 'flickr':
                $numSlides = $this->getParam('flickr-count', count($this->arrSlides));
                break;
            case 'youtube':
                $numSlides = $this->getParam('youtube-count', count($this->arrSlides));
                break;
            case 'vimeo':
                $numSlides = $this->getParam('vimeo-count', count($this->arrSlides));
                break;
        }

        return($numSlides);
    }

    /**
     * get sliders array - function don't belong to the object!
     */
    public function getArrSliders($orders = false, $templates = 'neither')
    {
        $order_fav = false;
        if ($orders !== false && key($orders) != 'favorite') {
            $order_direction = reset($orders);
            $do_order = key($orders);
        } else {
            $do_order = 'id';
            $order_direction = 'ASC';
            if (is_array($orders) && key($orders) == 'favorite') {
                $order_direction = reset($orders);
                $order_fav = true;
            }
        }
        $where = "`type` != 'template'";

        $response = $this->db->fetch(RevSliderGlobals::$table_sliders, $where, $do_order, '', $order_direction);

        $arrSliders = array();
        foreach ($response as $arrData) {
            $slider = new RevSlider();
            $slider->initByDBData($arrData);

            /*
              This part needs to stay for backwards compatibility. It is used in the update process from v4x to v5x
             */
            if ($templates === true) {
                if ($slider->getParam("template", "false") == "false") {
                    continue;
                }
            } elseif ($templates === false) {
                if ($slider->getParam("template", "false") == "true") {
                    continue;
                }
            }

            $arrSliders[] = $slider;
        }

        if ($order_fav === true) {
            $temp = array();
            $temp_not = array();
            foreach ($arrSliders as $key => $slider) {
                if ($slider->isFavorite()) {
                    $temp_not[] = $slider;
                } else {
                    $temp[] = $slider;
                }
            }
            $arrSliders = array();
            $arrSliders = ($order_direction == 'ASC') ? array_merge($temp, $temp_not) : array_merge($temp_not, $temp);
        }

        return($arrSliders);
    }

    public function getAllSliderAliases()
    {
        $where = "";



        $response = $this->db->fetch(GlobalsRevSlider::$table_sliders, $where, "id");



        $arrAliases = array();

        foreach ($response as $arrSlider) {
            $arrAliases[] = $arrSlider["alias"];
        }



        return($arrAliases);
    }

    public function getArrSlidersShort($exceptID = null, $filterType = self::SLIDER_TYPE_ALL)
    {
        $arrSliders = $this->getArrSliders();

        $arrShort = array();

        foreach ($arrSliders as $slider) {
            $id = $slider->getID();

            $isFromPosts = $slider->isSlidesFromPosts();

            $isTemplate = $slider->getParam("template", "false");



            //filter by gallery only

            if ($filterType == self::SLIDER_TYPE_POSTS && $isFromPosts == false) {
                continue;
            }



            if ($filterType == self::SLIDER_TYPE_GALLERY && $isFromPosts == true) {
                continue;
            }



            //filter by template type

            if ($filterType == self::SLIDER_TYPE_TEMPLATE && $isTemplate == "false") {
                continue;
            }



            //filter by except

            if (!empty($exceptID) && $exceptID == $id) {
                continue;
            }



            $title = $slider->getTitle();

            $arrShort[$id] = $title;
        }

        return($arrShort);
    }

    public function getArrSlidersWithSlidesShort($filterType = self::SLIDER_TYPE_ALL)
    {
        $arrSliders = self::getArrSlidersShort(null, $filterType);



        $output = array();

        foreach ($arrSliders as $sliderID => $sliderName) {
            $slider = new RevSlider();

            $slider->initByID($sliderID);



            $isFromPosts = $slider->isSlidesFromPosts();

            $isTemplate = $slider->getParam("template", "false");



            //filter by gallery only

            if ($filterType == self::SLIDER_TYPE_POSTS && $isFromPosts == false) {
                continue;
            }



            if ($filterType == self::SLIDER_TYPE_GALLERY && $isFromPosts == true) {
                continue;
            }



            //filter by template type

            if ($filterType == self::SLIDER_TYPE_TEMPLATE && $isTemplate == "false") {
                continue;
            }



            $sliderTitle = $slider->getTitle();

            $arrSlides = $slider->getArrSlidesFromGalleryShort();



            foreach ($arrSlides as $slideID => $slideName) {
                $output[$slideID] = $sliderName . ", " . $slideName;
            }
        }



        return($output);
    }

    public function getMaxOrder()
    {
        $this->validateInited();

        $maxOrder = 0;

        $arrSlideRecords = $this->db->fetch(GlobalsRevSlider::$table_slides, "slider_id=" . $this->id, "slide_order desc", "", "limit 1");

        if (empty($arrSlideRecords)) {
            return($maxOrder);
        }

        $maxOrder = $arrSlideRecords[0]["slide_order"];



        return($maxOrder);
    }

    public function getStartWithSlideSetting()
    {
        $numSlides = $this->getNumSlides();



        $startWithSlide = $this->getParam("start_with_slide", "1");

        if (is_numeric($startWithSlide)) {
            $startWithSlide = (int) $startWithSlide - 1;

            if ($startWithSlide < 0) {
                $startWithSlide = 0;
            }



            if ($startWithSlide >= $numSlides) {
                $startWithSlide = 0;
            }
        } else {
            $startWithSlide = 0;
        }



        return($startWithSlide);
    }

    public function isSlidesFromPosts()
    {
        $this->validateInited();

        $sourceType = $this->getParam("source_type", "gallery");

        if ($sourceType == "posts" || $sourceType == "specific_posts") {
            return true;
        }

        return(false);
    }

    /**
     * return if the slides source is from stream
     */
    public function isSlidesFromStream()
    {
        $this->validateInited();
        $sourceType = $this->getParam("source_type", "gallery");
        if ($sourceType != "posts" && $sourceType != "specific_posts" && $sourceType != "woocommerce" && $sourceType != "gallery") {
            return($sourceType);
        }

        return(false);
    }

    private function getPostsFromCategoies($publishedOnly = false)
    {
        $this->validateInited();
        $catIDs = $this->getParam("post_category");


        // $data = UniteFunctionsWPRev::getCatAndTaxData($catIDs);

        $taxonomies = '';

        // $taxonomies = $data["tax"];
        // $catIDs = $data["cats"];

        $sortBy = $this->getParam("post_sortby", self::DEFAULT_POST_SORTBY);

        $sortDir = $this->getParam("posts_sort_direction", self::DEFAULT_POST_SORTDIR);

        $maxPosts = $this->getParam("max_slider_posts", "30");

        if (empty($maxPosts) || !is_numeric($maxPosts)) {
            $maxPosts = -1;
        }

        $postTypes = $this->getParam("post_types", "any");

        //set direction for custom order
        if ($sortBy == UniteFunctionsWPRev::SORTBY_MENU_ORDER) {
            $sortDir = UniteFunctionsWPRev::ORDER_DIRECTION_ASC;
        }

        //Events integration
        $arrAddition = array();
        if ($publishedOnly == true) {
            $arrAddition["post_status"] = UniteFunctionsWPRev::STATE_PUBLISHED;
        }

        // $product = new Product(1,true,Context::getcontext()->language->id,Context::getcontext()->shop->id);

        $arrPosts = UniteFunctionsWPRev::getRevPostDataArray($catIDs, $sortBy, $sortDir, $maxPosts, $postTypes, $taxonomies, $arrAddition);

        // $arrPosts = UniteFunctionsWPRev::getPostsByCategory($catIDs,$sortBy,$sortDir,$maxPosts,$postTypes,$taxonomies,$arrAddition);

        return($arrPosts);
    }

    private function getPostsFromSpecificList()
    {
        $strPosts = $this->getParam("posts_list", "");

        $arrPosts = UniteFunctionsWPRev::getPostsByIDs($strPosts);



        return($arrPosts);
    }

    public function updatePostsSortbyFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "sliderID");

        $sortBy = UniteFunctionsRev::getVal($data, "sortby");

        UniteFunctionsRev::validateNotEmpty($sortBy, "sortby");



        $this->initByID($sliderID);

        $arrUpdate = array();

        $arrUpdate["post_sortby"] = $sortBy;



        $this->updateParam($arrUpdate);
    }

    public function replaceImageUrlsFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "sliderid");

        $urlFrom = UniteFunctionsRev::getVal($data, "url_from");

        UniteFunctionsRev::validateNotEmpty($urlFrom, "url from");

        $urlTo = UniteFunctionsRev::getVal($data, "url_to");



        $this->initByID($sliderID);

        $arrSildes = $this->getSlides();

        foreach ($arrSildes as $slide) {

            //$slide1 = new RevSlide();

            $slide->replaceImageUrls($urlFrom, $urlTo);
        }
    }

    public function resetSlideSettings($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "sliderid");

        $this->initByID($sliderID);

        $arrSildes = $this->getSlides();

        foreach ($arrSildes as $slide) {
            if (trim($data['reset_transitions']) != '') {
                $slide->changeTransition($data['reset_transitions']);
            }

            if ((int) ($data['reset_transition_duration']) > 0) {
                $slide->changeTransitionDuration($data['reset_transition_duration']);
            }
        }
    }

    /**
     * set new hero slide id for the Slider
     * @since: 5.0
     */
    public function setHeroSlide($data)
    {
        $sliderID = RevSliderFunctions::getVal($data, "slider_id");
        RevSliderFunctions::validateNotEmpty($sliderID, "Slider ID");
        $this->initByID($sliderID);

        $new_slide_id = RevSliderFunctions::getVal($data, "slide_id");
        RevSliderFunctions::validateNotEmpty($new_slide_id, "Hero Slide ID");

        $this->updateParam(array('hero_active' => (int) $new_slide_id));

        return($new_slide_id);
    }

    /**
     * get slides from gallery respecting wpml
     * force from gallery - get the slide from the gallery only
     */
    public function getSlidesWPML($publishedOnly = false, $slide = false)
    {

        $arrSlides = $this->getSlides($publishedOnly);

        $mslide_list = array();

        //check if WPML is active and change the ID of Slide depending on that.
        if (RevSliderWpml::isWpmlExists() && is_object($slide) && $this->getParam('use_wpml', 'off') == 'on') {
            $lang = $slide->getParam('lang', 'all');

            if (!empty($arrSlides)) {
                foreach ($arrSlides as $at_slide) {
                    $langs = $at_slide->getArrChildrenLangs();

                    if (!empty($langs) && is_array($langs)) {
                        foreach ($langs as $l) {
                            if ($l['lang'] == $lang) {
                                $mslide_list[] = array('id' => $l['slideid'], 'title' => $at_slide->getParam('title', 'Slide'));
                            }
                        }
                    }
                }
            }
            //get cur lang of slide
        } else {
            if (!empty($arrSlides)) {
                foreach ($arrSlides as $at_slide) {
                    $mslID = $at_slide->getID();

                    $mslide_list[] = array('id' => $mslID, 'title' => $at_slide->getParam('title', 'Slide'));
                }
            }
        }

        return($mslide_list);
    }
}

// @codingStandardsIgnoreStart
class RevSliderSlider extends RevSlider
{
    // @codingStandardsIgnoreEnd
}
