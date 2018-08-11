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

class RevSliderTemplate
{

    private $templates_url = 'http://templates.themepunch.tools/';
    private $templates_list = 'revslider/get-list.php';
    private $templates_download = 'revslider/download.php';
    private $templates_server_path = '/revslider/images/';
    private $templates_path = '/views/img/revtemplates/';
    private $templates_path_plugin = 'admin/assets/imports/';

    const SHOP_VERSION = '1.1.0';

    /**
     * Get the Templatelist from servers
     * @since: 5.0.5
     */
    public function getTemplateList($force = false)
    {
        $wp_version = _REV_VERSION_;

        $wpdb = rev_db_class::revDbInstance();

        $last_check = get_option('revslider-templates-check');

//        $is_expire = !(get_transient('revslider_prestashop_slide_update'));

        if ($last_check == false) { //first time called
            $last_check = 172801;
            update_option('revslider-templates-check', time());
        }

        // Get latest Templates
        if (time() - $last_check > 345600 || $force == true) { //4 days
//            set_transient('revslider_prestashop_slide_update', "true", 345600);
            update_option('revslider-templates-check', time());

            $validated = Configuration::get('revslider-valid');
            $code = Configuration::get('revslider-code', '');

            $shop_version = self::SHOP_VERSION;

            if ($validated == 'false') {
                $code = '';
            }

            $rattr = array(
                'code' => $code,
                'shop_version' => urlencode($shop_version),
                'version' => urlencode(RevSliderGlobals::SLIDER_REVISION),
                'product' => urlencode('revslider_prestashop')
            );

            $request = wp_remote_fopen($this->templates_url . $this->templates_list, array(
                'user-agent' => 'PrestaShop/' . $wp_version . '; ',
                'body' => $rattr
            ));

            if ($request !== false) {
                if ($response = maybe_unserialize($request)) {

                    $templates = Tools::jsonDecode($response, true);

                    if (is_array($templates)) {
                        $is_exist = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='revslider_templates_premium_new'");

                        foreach ($templates['slider'] as &$template) {
                            if (isset($template['plugin_require']) && !empty($template['plugin_require'])) {
                                $template['plugin_require'] = Tools::jsonDecode($template['plugin_require'], true);
                            }
                            if(isset($template['description']) && $template['description']){
                                $template['description'] = str_replace("'", '&#39;', $template['description']); //remove single quote with with its equivalent
                            }
                        }

                        $serialized_data = serialize($templates);
//                        $serialized_data = preg_replace_callback('/[\r\n]+/', create_function('$match', 'return "";'), $serialized_data);
//                        $serialized_data = preg_replace_callback('/s:(\d+):"([^"]*)"/', create_function('$match', '$size=strlen($match[2]); return "s:{$size}:\"{$match[2]}\"";'), $serialized_data);
                        if ($is_exist) {
                            $wpdb->query("UPDATE `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` SET `value`='" . $serialized_data . "' WHERE `name`='revslider_templates_premium_new';");
                        } else {
                            $wpdb->query("INSERT INTO `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` (`id`, `name`, `value`) VALUES (NULL, 'revslider_templates_premium_new', '" . $serialized_data . "');");
                        }
                    }
                }
            }

            $this->updateTemplateList();
        }
    }

    /**
     * Update the Templatelist, move rs-templates-new into rs-templates
     * @since: 5.0.5
     */
    private function updateTemplateList()
    {
        $wpdb = rev_db_class::revDbInstance();

        $new = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='revslider_templates_premium_new'");
        $cur = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='revslider_templates_premium'");

        $new = ($new) ? $new['value'] : serialize(array());
        $cur = ($cur) ? $cur['value'] : serialize(array());


        $new = unserialize($new);
        $cur = unserialize($cur);

        if ($new !== false && !empty($new) && is_array($new)) {
            if (empty($cur)) {
                $cur = $new;
            } else {
                if (@RevsliderPrestashop::getIsset($new['slider']) && is_array($new['slider'])) {
                    foreach ($new['slider'] as $n) {
                        $found = false;
                        if (@RevsliderPrestashop::getIsset($cur['slider']) && is_array($cur['slider'])) {
                            foreach ($cur['slider'] as $ck => $c) {
                                if ($c['uid'] == $n['uid']) {
                                    if (version_compare($c['version'], $n['version'], '<')) {
                                        $n['is_new'] = true;
                                        $n['push_image'] = true; //push to get new image and replace
                                    }
                                    if (@RevsliderPrestashop::getIsset($c['is_new'])) {
                                        $n['is_new'] = true;
                                    } //is_new will stay until update is done

                                    $n['exists'] = true; //if this flag is not set here, the template will be removed from the list

                                    $cur['slider'][$ck] = $n;
                                    $found = true;
                                    break;
                                }
                            }
                        }

                        if (!$found) {
                            $n['exists'] = true;
                            $cur['slider'][] = $n;
                        }
                    }

                    foreach ($cur['slider'] as $ck => $c) { //remove no longer available Slider
                        if (!@RevsliderPrestashop::getIsset($c['exists'])) {
                            unset($cur['slider'][$ck]);
                        } else {
                            unset($cur['slider'][$ck]['exists']);
                        }
                    }

                    $cur['slides'] = $new['slides']; // push always all slides
                }
            }


            $is_exist = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='revslider_templates_premium'");

            $serialized_data = serialize($cur);

            if ($is_exist) {
                $wpdb->query("UPDATE `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` SET `value`='" . $serialized_data . "' WHERE `name`='revslider_templates_premium';");
            } else {
                $wpdb->query("INSERT INTO `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` (`id`, `name`, `value`) VALUES (NULL, 'revslider_templates_premium', '" . $serialized_data . "');");
            }


            $is_exist_new = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='revslider_templates_premium'");


            if ($is_exist_new) {
                $wpdb->query("UPDATE `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` SET `value`='false' WHERE `name`='revslider_templates_premium_new';");
            } else {
                $wpdb->query("INSERT INTO `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` (`id`, `name`, `value`) VALUES (NULL, 'revslider_templates_premium_new', 'false');");
            }

            $this->updateImages();
        }
    }

    /**
     * Remove the is_new attribute which shows the "update available" button
     * @since: 5.0.5
     */
    public function removeIsNew($uid)
    {
        $cur = get_option('rs-templates', array());

        if (@RevsliderPrestashop::getIsset($cur['slider']) && is_array($cur['slider'])) {
            foreach ($cur['slider'] as $ck => $c) {
                if ($c['uid'] == $uid) {
                    unset($cur['slider'][$ck]['is_new']);
                    break;
                }
            }
        }

        update_option('rs-templates', $cur);
    }

    /**
     * Update the Images get them from Server and check for existance on each image
     * @since: 5.0.5
     */
    private function updateImages()
    {
        $wpdb = rev_db_class::revDbInstance();
        // $templates = get_option('rs-templates', array());
        $templates = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='revslider_templates_premium'");
        $templates = (@RevsliderPrestashop::getIsset($templates['value']) && !empty($templates['value'])) ? unserialize($templates['value']) : array();

        $reload = array();

        if (!empty($templates) && is_array($templates)) {
            if (!empty($templates['slider']) && is_array($templates['slider'])) {
                foreach ($templates['slider'] as $key => $temp) {
                    $file = WP_CONTENT_DIR . $this->templates_path . $temp['img'];



                    if (!file_exists($file)) {
                        $image_data = @Tools::file_get_contents($this->templates_url . $this->templates_server_path . $temp['img']); // Get image data
                        if ($image_data !== false) {
                            $reload[$temp['alias']] = true;
                            @mkdir(dirname($file));
                            @file_put_contents($file, $image_data);
                        } else {//could not connect to server
                        }
                    }
                }
            }
            if (!empty($templates['slides']) && is_array($templates['slides'])) {
                foreach ($templates['slides'] as $key => $temp) {
                    foreach ($temp as $k => $tvalues) {
                        $file = WP_CONTENT_DIR . $this->templates_path . '/' . $tvalues['img'];

                        if (!file_exists($file)) { //update, so load again
                            $image_data = @Tools::file_get_contents($this->templates_url . $this->templates_server_path . $tvalues['img']); // Get image data

                            if ($image_data !== false) {
                                @mkdir(dirname($file));
                                @file_put_contents($file, $image_data);
                            } else {//could not connect to server
                            }
                        } else {//use default image
                        }
                    }
                }
            }
        }

        // update_option('rs-templates', $templates); //remove the push_image
    }

    /**
     * Copy a Slide to the Template Slide list
     * @since: 5.0
     */
    public function copySlideToTemplates($slide_id, $slide_title, $slide_settings = array())
    {
        if ((int) ($slide_id) == 0) {
            return false;
        }
        $slide_title = sanitize_text_field($slide_title);
        if (Tools::strlen(trim($slide_title)) < 3) {
            return false;
        }

        $wpdb = RevsliderPrestashop::$wpdb;

        $table_name = RevSliderGlobals::$table_slides;

        $duplicate = $wpdb->getRow("SELECT * FROM $table_name WHERE id = $slide_id", ARRAY_A);

        if (empty($duplicate)) { // slide not found
            return false;
        }

        unset($duplicate['id']);

        $duplicate['slider_id'] = -1; //-1 sets it to be a template
        $duplicate['slide_order'] = -1;

        $params = Tools::jsonDecode($duplicate['params'], true);
        $settings = Tools::jsonDecode($duplicate['settings'], true);

        $params['title'] = $slide_title;
        $params['state'] = 'published';

        if (@RevsliderPrestashop::getIsset($slide_settings['width'])) {
            $settings['width'] = (int) ($slide_settings['width']);
        }
        if (@RevsliderPrestashop::getIsset($slide_settings['height'])) {
            $settings['height'] = (int) ($slide_settings['height']);
        }

        $duplicate['params'] = Tools::jsonEncode($params);
        $duplicate['settings'] = Tools::jsonEncode($settings);

        $response = $wpdb->insert($table_name, $duplicate);

        if ($response) {
            return true;
        }

        return false;
    }

    /**
     * Get all Template Slides
     * @since: 5.0
     */
    public function getTemplateSlides()
    {
        $wpdb = RevsliderPrestashop::$wpdb;

        $table_name = RevSliderGlobals::$table_slides;

        $templates = $wpdb->getResults("SELECT * FROM $table_name WHERE slider_id = -1", ARRAY_A);

        //add default Template Slides here!
        $default = $this->getDefaultTemplateSlides();

        $templates = array_merge($templates, $default);

        if (!empty($templates)) {
            foreach ($templates as $key => $template) {
                $templates[$key]['params'] = Tools::jsonDecode($template['params'], true);
                $templates[$key]['layers'] = Tools::jsonDecode($template['layers'], true);
                $templates[$key]['settings'] = Tools::jsonDecode($template['settings'], true);
            }
        }

        return $templates;
    }

    /**
     * Add default Template Slides that can't be deleted for example. Authors can add their own Slides here through Filter
     * @since: 5.0
     */
    private function getDefaultTemplateSlides()
    {
        $templates = array();
        $templates = $templates;
        return $templates;
    }

    /**
     * get default ThemePunch default Slides
     * @since: 5.0
     */
    public function getThemePunchTemplateSlides($sliders = false)
    {
        $wpdb = RevsliderPrestashop::$wpdb;

        $templates = array();

        $slide_defaults = array(); //

        if ($sliders == false) {
            $sliders = $this->getThemePunchTemplateSliders();
        }
        $table_name = RevSliderGlobals::$table_slides;

        if (!empty($sliders)) {
            foreach ($sliders as $slider) {
                $slides = $this->getThemePunchTemplateDefaultSlides($slider['alias']);

                if (!@RevsliderPrestashop::getIsset($slider['installed'])) {
                    $templates = array_merge($templates, $wpdb->getResults(sprintf("SELECT * FROM $table_name WHERE slider_id = %d", (int)$slider['id']), ARRAY_A));
                } else {
                    $templates = array_merge($templates, $slides);
                }
                if (!empty($templates)) {
                    foreach ($templates as $key => $tmpl) {
                        if (@RevsliderPrestashop::getIsset($slides[$key])) {
                            $templates[$key]['img'] = $slides[$key]['img'];
                        }
                    }
                }

                /* else{
                  $templates = array_merge($templates, array($slide_defaults[$slider['alias']]));
                  } */
            }
        }

        if (!empty($templates)) {
            foreach ($templates as $key => $template) {
                if (!@RevsliderPrestashop::getIsset($template['installed'])) {
                    $template['params'] = (@RevsliderPrestashop::getIsset($template['params'])) ? $template['params'] : '';
                    $template['layers'] = (@RevsliderPrestashop::getIsset($template['layers'])) ? $template['layers'] : '';
                    $template['settings'] = (@RevsliderPrestashop::getIsset($template['settings'])) ? $template['settings'] : '';

                    $templates[$key]['params'] = Tools::jsonDecode($template['params'], true);
                    $templates[$key]['layers'] = Tools::jsonDecode($template['layers'], true);
                    $templates[$key]['settings'] = Tools::jsonDecode($template['settings'], true);
                }
            }
        }

        return $templates;
    }

    /**
     * get default ThemePunch default Slides
     * @since: 5.0
     */
    public function getThemePunchTemplateDefaultSlides($slider_alias)
    {
        $wpdb = RevsliderPrestashop::$wpdb;
        $templates = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='revslider_templates_premium'");
        $templates = (@RevsliderPrestashop::getIsset($templates['value']) && !empty($templates['value'])) ? unserialize($templates['value']) : array();
        $slides = (@RevsliderPrestashop::getIsset($templates['slides']) && !empty($templates['slides'])) ? $templates['slides'] : array();

        return (@RevsliderPrestashop::getIsset($slides[$slider_alias])) ? $slides[$slider_alias] : array();
    }

    /**
     * Get default Template Sliders
     * @since: 5.0
     */
    public function getDefaultTemplateSliders()
    {
        $wpdb = RevsliderPrestashop::$wpdb;

        $sliders = array();
        $check = array();

        $table_name = RevSliderGlobals::$table_sliders;

        //add themepunch default Sliders here
        $check = $wpdb->getResults("SELECT * FROM $table_name WHERE type = 'template'", ARRAY_A);


        /*
         * Example		 
          $sliders['Slider Pack Name'] = array(
          array('title' => 'PJ Slider 1', 'alias' => 'pjslider1', 'width' => 1400, 'height' => 868, 'zip' => 'exwebproduct.zip', 'uid' => 'bde6d50c2f73f8086708878cf227c82b', 'installed' => false, 'img' => RS_PLUGIN_URL .'admin/assets/imports/exwebproduct.jpg'),
          array('title' => 'PJ Classic Slider', 'alias' => 'pjclassicslider', 'width' => 1240, 'height' => 600, 'zip' => 'classicslider.zip', 'uid' => 'a0d6a9248c9066b404ba0f1cdadc5cf2', 'installed' => false, 'img' => RS_PLUGIN_URL .'admin/assets/imports/classicslider.jpg')
          );
         * */

        if (!empty($check) && !empty($sliders)) {
            foreach ($sliders as $key => $the_sliders) {
                foreach ($the_sliders as $skey => $slider) {
                    foreach ($check as $ikey => $installed) {
                        if ($installed['alias'] == $slider['alias']) {
                            $img = $slider['img'];
                            $sliders[$key][$skey] = $installed;

                            $sliders[$key][$skey]['img'] = $img;

                            $sliders[$key]['version'] = (@RevsliderPrestashop::getIsset($slider['version'])) ? $slider['version'] : '';
                            if (@RevsliderPrestashop::getIsset($slider['is_new'])) {
                                $sliders[$key]['is_new'] = true;
                            }

                            $preview = (@RevsliderPrestashop::getIsset($slider['preview'])) ? $slider['preview'] : false;
                            if ($preview !== false) {
                                $sliders[$key]['preview'] = $preview;
                            }

                            break;
                        }
                    }
                }
            }
        }

        return $sliders;
    }

    /**
     * get default ThemePunch default Sliders
     * @since: 5.0
     */
    public function getThemePunchTemplateSliders()
    {
        $wpdb = RevsliderPrestashop::$wpdb;

        $sliders = array();

        $table_name = RevSliderGlobals::$table_sliders;

        //add themepunch default Sliders here
        $sliders = $wpdb->getResults("SELECT * FROM $table_name WHERE type = 'template'", ARRAY_A);

        $defaults = $wpdb->getRow("SELECT * FROM `" . $wpdb->prefix . RevSliderGlobals::TABLE_REVSLIDER_OPTIONS_NAME . "` WHERE `name`='revslider_templates_premium'");

        $defaults = ($defaults) ? unserialize($defaults['value']) : array();

        $defaults = (@RevsliderPrestashop::getIsset($defaults['slider'])) ? $defaults['slider'] : array();

        if (!empty($sliders)) {
            if (!empty($defaults)) {
                foreach ($defaults as $key => $slider) {
                    foreach ($sliders as $ikey => $installed) {
                        if ($installed['alias'] == $slider['alias']) {
                            $img = $slider['img'];
                            $preview = (@RevsliderPrestashop::getIsset($slider['preview'])) ? $slider['preview'] : false;
                            $defaults[$key] = $installed;

                            $defaults[$key]['img'] = $img;
                            $defaults[$key]['version'] = $slider['version'];
                            $defaults[$key]['cat'] = $slider['cat'];
                            $defaults[$key]['filter'] = $slider['filter'];

                            if (@RevsliderPrestashop::getIsset($slider['is_new'])) {
                                $defaults[$key]['is_new'] = true;
                                $defaults[$key]['zip'] = $slider['zip'];
                                $defaults[$key]['width'] = $slider['width'];
                                $defaults[$key]['height'] = $slider['height'];
                                $defaults[$key]['uid'] = $slider['uid'];
                            }

                            if ($preview !== false) {
                                $defaults[$key]['preview'] = $preview;
                            }
                            break;
                        }
                    }
                }
            }
        }

        return $defaults;
    }

    /**
     * check if image was uploaded, if yes, return path or url
     * @since: 5.0.5
     */
    public function checkFilePath($image, $url = false)
    {
        $upload_dir = WP_CONTENT_DIR . $this->templates_path; // Set upload folder

        $file = $upload_dir . '/' . $image;

        if (file_exists($file)) { //downloaded image first, for update reasons
            $image = _MODULE_DIR_ . 'revsliderprestashop' . $this->templates_path . $image; //server path
        } else {

            //jump to a default image here?
            $image = false;
        }

        return $image;
    }

    /**
     * output markup for the import template, the zip was not yet improted
     * @since: 5.0
     */
    public function writeImportTemplateMarkup($template)
    {
        $template['img'] = $this->checkFilePath($template['img'], true);
        if ($template['img'] == '') {
            //set default image
        }

        //check for version and compare, only allow download if version is high enough
        $deny = '';
        if (@RevsliderPrestashop::getIsset($template['required'])) {
            if (Tools::version_compare(RevSliderGlobals::SLIDER_REVISION, $template['required'], '<')) {
                $deny = ' deny_download';
            }
        }

        echo '<div data-src="' . $template['img'] . '" class="template_slider_item_import' . $deny . '"
            data-gridwidth="' . $template['width'] . '"
			data-gridheight="' . $template['height'] . '"
			data-zipname="' . $template['zip'] . '"
			data-uid="' . $template['uid'] . '" ';

        if ($deny !== '') { //add needed version number here
            echo 'data-versionneed="' . $template['required'] . '" ';
        }
        echo '>';

        echo '<div class="not-imported-overlay"></div>';
        echo '<div style="position:absolute;top:10px;right:10px;width:35px;text-align:right;z-index:2">				';
        echo '<div class="icon-install_slider"></div>';
        echo '</div>';

        echo '</div>';
        echo '<div style="position:absolute;top:10px;right:50px;width:35px;text-align:right;z-index:2">';

        if (@RevsliderPrestashop::getIsset($template['preview']) && $template['preview'] !== '') {

            echo '<a class="icon-preview_slider" href="' . $template['preview'] . '" target="_blank"></a>';
        }

        echo '</div>';
    }

    /**
     * output markup for the import template, the zip was not yet imported
     * @since: 5.0
     */
    public function writeImportTemplateMarkupSlide($template)
    {
        $template['img'] = $this->checkFilePath($template['img'], true);

        if ($template['img'] == '') {
            //set default image
        }
        //check for version and compare, only allow download if version is high enough
        $deny = '';
        if (@RevsliderPrestashop::getIsset($template['required'])) {
            if (version_compare(RevSliderGlobals::SLIDER_REVISION, $template['required'], '<')) {
                $deny = ' deny_download';
            }
        }


        echo '<div class="template_slide_item_import">';
        echo '<div class="template_slide_item_img' . $deny . '" ';
        echo 'data-src="' . $template['img'] . '" ';
        echo 'data-gridwidth="' . $template['width'] . '" ';
        echo 'data-gridheight="' . $template['height'] . '" ';
        echo 'data-zipname="' . $template['zip'] . '" ';
        echo 'data-uid="' . $template['uid'] . '"';
        echo 'data-slidenumber="' . $template['number'] . '" ';

        if ($deny !== '') { //add needed version number here
            echo 'data-versionneed="' . $template['required'] . '" ';
        }

        echo '>';
        echo '<div class="not-imported-overlay"></div>';
        echo '</div>';
        echo '<div style="position:absolute;top:10px;right:10px;width:100%;text-align:right;z-index:2">';
        echo '<div class="icon-install_slider"></div>';
        echo '</div>';
        echo '<div class="template_title">';
        echo (@RevsliderPrestashop::getIsset($template['title'])) ? $template['title'] : '';
        echo '</div>';
        echo '</div>';
    }

    /**
     * output markup for template
     * @since: 5.0
     */
    public function writeTemplateMarkup($template, $slider_id = false)
    {
        $params = $template['params'];
        $settings = $template['settings'];
        $slide_id = $template['id'];
        $title = str_replace("'", "", RevSliderBase::getVar($params, 'title', 'Slide'));
        if ($slider_id !== false) {
            $title = '';
        } //remove Title if Slider

        $width = RevSliderBase::getVar($settings, "width", 1240);
        $height = RevSliderBase::getVar($settings, "height", 868);

        $bgType = RevSliderBase::getVar($params, "background_type", "transparent");
        $bgColor = RevSliderBase::getVar($params, "slide_bg_color", "transparent");

        $bgFit = RevSliderBase::getVar($params, "bg_fit", "cover");
        $bgFitX = (int) (RevSliderBase::getVar($params, "bg_fit_x", "100"));
        $bgFitY = (int) (RevSliderBase::getVar($params, "bg_fit_y", "100"));

        $bgPosition = RevSliderBase::getVar($params, "bg_position", "center center");
        $bgPositionX = (int) (RevSliderBase::getVar($params, "bg_position_x", "0"));
        $bgPositionY = (int) (RevSliderBase::getVar($params, "bg_position_y", "0"));

        $bgRepeat = RevSliderBase::getVar($params, "bg_repeat", "no-repeat");

        $bgStyle = ' ';
        if ($bgFit == 'percentage') {
            if ((int) ($bgFitY) == 0 || (int) ($bgFitX) == 0) {
                $bgStyle .= "background-size: cover;";
            } else {
                $bgStyle .= "background-size: " . $bgFitX . '% ' . $bgFitY . '%;';
            }
        } else {
            $bgStyle .= "background-size: " . $bgFit . ";";
        }
        if ($bgPosition == 'percentage') {
            $bgStyle .= "background-position: " . $bgPositionX . '% ' . $bgPositionY . '%;';
        } else {
            $bgStyle .= "background-position: " . $bgPosition . ";";
        }
        $bgStyle .= "background-repeat: " . $bgRepeat . ";";


        if (@RevsliderPrestashop::getIsset($template['img'])) {
            $thumb = $this->checkFilePath($template['img'], true);
        } else {
            $imageID = RevSliderBase::getVar($params, "image_id");
            if (empty($imageID)) {
                $thumb = RevSliderBase::getVar($params, "image");

                $imgID = RevSliderBase::getImageIdByUrl($thumb);
                if ($imgID !== false) {
                    $thumb = RevSliderFunctionsWP::getUrlAttachmentImage($imgID, RevSliderFunctionsWP::THUMB_MEDIUM);
                }
            } else {
                $thumb = RevSliderFunctionsWP::getUrlAttachmentImage($imageID, RevSliderFunctionsWP::THUMB_MEDIUM);
            }

            if ($thumb == '') {
                $thumb = RevSliderBase::getVar($params, "image");
            }
        }


        $bg_fullstyle = '';
        $bg_extraClass = '';
        $data_urlImageForView = '';

        if (@RevsliderPrestashop::getIsset($template['img'])) {
            $data_urlImageForView = 'data-src="' . $thumb . '"';
        } else {
            if ($bgType == 'image' || $bgType == 'vimeo' || $bgType == 'youtube' || $bgType == 'html5') {
                $data_urlImageForView = 'data-src="' . $thumb . '"';
                $bg_fullstyle = ' style="' . $bgStyle . '" ';
            }

            if ($bgType == "solid") {
                $bg_fullstyle = ' style="background-color:' . $bgColor . ';" ';
            }

            if ($bgType == "trans" || $bgType == "transparent") {
                $bg_extraClass = 'mini-transparent';
            }
        }


        echo '<div class="template_slide_single_element" style="display:inline-block">';
        echo '<div ' . $data_urlImageForView . ' class="' . (($slider_id !== false) ? 'template_slider_item' : 'template_item') . ' ' . $bg_extraClass . '" ' . $bg_fullstyle . ' ';
        echo 'data-gridwidth="' . $width . '" ';
        echo 'data-gridheight="' . $height . '" ';
        if ($slider_id !== false) {

            echo 'data-sliderid="' . $slider_id . '"';
        } else {

            echo 'data-slideid="' . $slide_id . '"';
        }


        echo '>';

        echo '<div class="not-imported-overlay"></div>			';
        echo '<div style="position:absolute;top:10px;right:10px;width:35px;text-align:right;z-index:2"><div class="icon-add_slider"></div></div>';

        echo '</div>';
        echo '<div style="position:absolute;top:10px;right:50px;width:35px;text-align:right;z-index:2">				';
        if (@RevsliderPrestashop::getIsset($template['preview']) && $template['preview'] !== '') {
            echo '<a class="icon-preview_slider" href="' . $template['preview'] . '" target="_blank"></a>';
        }
        echo '</div>';
        if ($slider_id == false) {
            echo '<div class="template_title">' . $title . '</div>';
        }
        echo '</div>';
    }

    /**
     * Download template by UID (also validates if download is legal)
     * @since: 5.0.5
     */
    public function downloadTemplate($uid)
    {
        $wp_version = _PS_VERSION_;

        $uid = esc_attr($uid);

        $code = Configuration::get('revslider-code');
        $shop_version = self::SHOP_VERSION;

        $validated = Configuration::get('revslider-valid');
        if ($validated == 'false') {
            $code = '';
        }

        $rattr = array(
            'code' => urlencode($code),
            'shop_version' => urlencode($shop_version),
            'version' => urlencode(RevSliderGlobals::SLIDER_REVISION),
            'uid' => urlencode($uid),
            'product' => urlencode('revslider_prestashop'),
        );
        $siteurl = Context::getcontext()->shop->getBaseURL();
        $upload_dir = wp_upload_dir(); // Set upload folder
        // Check folder permission and define file location
        if (wp_mkdir_p($upload_dir['basedir'] . $this->templates_path)) { //check here to not flood the server
            $request = wp_remote_post($this->templates_url . $this->templates_download, array(
                'method' => 'POST',
                'user-agent' => 'Prestashop/' . $wp_version . '; ' . $siteurl,
                'body' => $rattr,
                'headers' => array(
                    'Accept-Encoding' => 'deflate;q=1.0, compress;q=0.5, gzip;q=0.5',
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                ),
                'timeout' => 45,
            ));

            if (RevGlobalObject::getIsset($request['body'])) {
                if ($response = $request['body']) {
                    if ($response !== 'invalid') {
                        //add stream as a zip file
                        $file = $upload_dir['basedir'] . $this->templates_path . '/' . $uid . '.zip';
                        @mkdir(dirname($file));
                        $ret = @file_put_contents($file, $response);
                        if ($ret !== false) {
                            //return $file so it can be processed. We have now downloaded it into a zip file
                            return $file;
                        } else {//else, print that file could not be written
                            return array('error' => __('Can\'t write the file into the uploads folder of WordPress, please change permissions and try again!', 'revslider'));
                        }
                    }
                }
            }//else, check for error and print it to customer
        } else {
            return array('error' => __('Can\'t write into the uploads folder of WordPress, please change permissions and try again!', 'revslider'));
        }

        return false;
    }

    /**
     * Delete the Template file
     * @since: 5.0.5
     */
    public function deleteTemplate($uid)
    {
        $uid = esc_attr($uid);

        $upload_dir = wp_upload_dir(); // Set upload folder
        // Check folder permission and define file location
        if (wp_mkdir_p($upload_dir['basedir'] . $this->templates_path)) {
            $file = $upload_dir['basedir'] . $this->templates_path . '/' . $uid . '.zip';

            if (file_exists($file)) {
                //delete file
                return unlink($file);
            }
        }

        return false;
    }
}
