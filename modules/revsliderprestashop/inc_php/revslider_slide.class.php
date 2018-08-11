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

class RevSlide extends UniteElementsBaseRev
{

    private $id;
    private $sliderID;
    private $slideOrder;
    private $imageUrl;
    private $imageID;
    private $imageThumb;
    private $imageFilepath;
    private $imageFilename;
    private $params;
    private $arrLayers;
    private $settings;
    private $arrChildren = null;
    private $slider;
    private $static_slide = false;
    private $postData;
    public $templateID;
    private $arrLayers_export;

    public function __construct()
    {
        parent::__construct();
    }

    public function initByData($record)
    {

        // it called for multiple times.
        // may be it is the issue for google font for multilang slide text layers

        if (@RevsliderPrestashop::getIsset($record["id"])) {
            $this->id = $record["id"];
        }
        if (@RevsliderPrestashop::getIsset($record["slider_id"])) {
            $this->sliderID = $record["slider_id"];
        }
        if (@RevsliderPrestashop::getIsset($record["slide_order"])) {
            $this->slideOrder = $record["slide_order"];
        }
        $params = $record["params"];
        if (get_magic_quotes_gpc()) { //changes made 1st Apr 2014
            $params = Tools::stripslashes($params);
        }
        $params = (array) Tools::jsonDecode($params);
        $layers = $record["layers"];


        if (get_magic_quotes_gpc()) { //changes made 1st Apr 2014
            $layers = Tools::stripslashes($layers);
        }


        $layers = str_replace('\\"', "'", $layers);

        $layers = (array) Tools::jsonDecode($layers);

        $layers = UniteFunctionsRev::convertStdClassToArray($layers);

        $settings = (@RevsliderPrestashop::getIsset($record["settings"])) ? $record["settings"] : '[]';
        $settings = (array) Tools::jsonDecode($settings);

        $imageID = UniteFunctionsRev::getVal($params, "image_id");

        //get image url and thumb url
        if (!empty($imageID)) {
            $this->imageID = $imageID;
            $imageUrl = UniteFunctionsWPRev::getUrlAttachmentImage($imageID);
            if (empty($imageUrl)) {
                $imageUrl = UniteFunctionsRev::getVal($params, "image");
            }
            $this->imageThumb = UniteFunctionsWPRev::getUrlAttachmentImage($imageID, UniteFunctionsWPRev::THUMB_MEDIUM);
        } else {
            $imageUrl = UniteFunctionsRev::getVal($params, "image");
        }
        if (is_ssl()) {
            $imageUrl = str_replace("http://", "https://", $imageUrl);
        }
        // dmp($imageUrl);exit();
        //set image path, file and url
        $this->imageUrl = $imageUrl;
        $this->imageFilepath = UniteFunctionsWPRev::getImagePathFromURL($this->imageUrl);
        $realPath = UniteFunctionsWPRev::getPathContent() . $this->imageFilepath;
        if (file_exists($realPath) == false || is_file($realPath) == false) {
            $this->imageFilepath = "";
        }
        $this->imageFilename = basename($this->imageUrl);
        $this->params = $params;
        $this->arrLayers_export = $layers;
        $ijk = 0;
        foreach ($layers as $layer) {
            if (@RevsliderPrestashop::getIsset($layer['image_url']) && !empty($layer['image_url'])) {
                $layers[$ijk]['image_url'] = modify_image_url($layers[$ijk]['image_url']);
            }

            $ijk++;
        }

        $this->arrLayers = $layers;

        $this->settings = $settings;
    }

    private function initBySlide(RevSlide $slide)
    {
        $this->id = "template";

        $this->templateID = $slide->getID();

        $this->sliderID = $slide->getSliderID();

        $this->slideOrder = $slide->getOrder();

        $this->imageUrl = $slide->getImageUrl();

        $this->imageID = $slide->getImageID();

        $this->imageThumb = $slide->getThumbUrl();

        $this->imageFilepath = $slide->getImageFilepath();

        $this->imageFilename = $slide->getImageFilename();

        $this->params = $slide->getParams();

        $this->arrLayers = $slide->getLayers();

        $this->arrChildren = $slide->getArrChildrenPure();
    }

    /**
     * 
     * init slide by post data
     */
    public function initByStreamData($postData, $slideTemplate, $sliderID, $sourceType, $additions)
    {
        $this->postData = array();
        $this->postData = (array) $postData;

        //init by global template
        $this->initBySlide($slideTemplate);

        switch ($sourceType) {
            case 'facebook':
                $this->initByFacebook($sliderID, $additions);
                break;
            case 'twitter':
                $this->initByTwitter($sliderID, $additions);
                break;
            case 'instagram':
                $this->initByInstagram($sliderID);
                break;
            case 'flickr':
                $this->initByFlickr($sliderID);
                break;
            case 'youtube':
                $this->initByYoutube($sliderID, $additions);
                break;
            case 'vimeo':
                $this->initByVimeo($sliderID, $additions);
                break;
            default:
                RevSliderFunctions::throwError(__("Source must be from Stream", 'revslider'));
                break;
        }
    }

    /**
     * init the data for facebook
     * @since: 5.0
     * @change: 5.1.1 Facebook Album
     */
    private function initByFacebook($sliderID, $additions)
    {
        //set some slide params
        $this->id = RevSliderFunctions::getVal($this->postData, 'id');

        $this->params["title"] = RevSliderFunctions::getVal($this->postData, 'name');

        if (@RevsliderPrestashop::getIsset($this->params['enable_link']) && $this->params['enable_link'] == "true" && @RevsliderPrestashop::getIsset($this->params['link_type']) && $this->params['link_type'] == "regular") {
            $link = RevSliderFunctions::getVal($this->postData, 'link');
            $this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
        }

        $this->params["state"] = "published";

        if ($this->params["background_type"] == 'image') { //if image is choosen, use featured image as background
            if ($additions['fb_type'] == 'album') {
                $this->imageUrl = 'https://graph.facebook.com/' . RevSliderFunctions::getVal($this->postData, 'id') . '/picture';
                $this->imageThumb = RevSliderFunctions::getVal($this->postData, 'picture');
            } else {
                $img = $this->getFacebookTimelineImage();
                $this->imageUrl = $img;
                $this->imageThumb = $img;
            }

            if (empty($this->imageUrl)) {
                $this->imageUrl = _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sources/fb.png';
            }

            if (is_ssl()) {
                $this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
            }

            $this->imageFilename = basename($this->imageUrl);
        }

        //replace placeholders in layers:
        $this->setLayersByStreamData($sliderID, 'facebook', $additions);
    }

    /**
     * init the data for twitter
     * @since: 5.0
     */
    private function initByTwitter($sliderID, $additions)
    {
        //set some slide params
        $this->id = RevSliderFunctions::getVal($this->postData, 'id');

        $this->params["title"] = RevSliderFunctions::getVal($this->postData, 'title');

        if (@RevsliderPrestashop::getIsset($this->params['enable_link']) && $this->params['enable_link'] == "true" && @RevsliderPrestashop::getIsset($this->params['link_type']) && $this->params['link_type'] == "regular") {
            $link = 'https://twitter.com/' . $additions['twitter_user'] . '/status/' . RevSliderFunctions::getVal($this->postData, 'id_str');
            $this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
        }

        $this->params["state"] = "published";

        if ($this->params["background_type"] == 'trans' || $this->params["background_type"] == 'image' || $this->params["background_type"] == 'streamtwitter' || $this->params["background_type"] == 'streamtwitterboth') { //if image is choosen, use featured image as background
            $img_sizes = RevSliderBase::getAllImageSizes('twitter');

            $imgResolution = RevSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));
            $this->imageID = RevSliderFunctions::getVal($this->postData, 'id');
            if (!@RevsliderPrestashop::getIsset($img_sizes[$imgResolution])) {
                $imgResolution = key($img_sizes);
            }

            $image_url_array = RevSliderFunctions::getVal($this->postData, 'media');
            $image_url_large = RevSliderFunctions::getVal($image_url_array, 'large');

            $img = RevSliderFunctions::getVal($image_url_large, 'media_url', '');
            $entities = RevSliderFunctions::getVal($this->postData, 'entities');

            if ($img == '') {
                $image_url_array = RevSliderFunctions::getVal($entities, 'media');
                if (is_array($image_url_array) && @RevsliderPrestashop::getIsset($image_url_array[0])) {
                    if (is_ssl()) {
                        $img = RevSliderFunctions::getVal($image_url_array[0], 'media_url_https');
                    } else {
                        $img = RevSliderFunctions::getVal($image_url_array[0], 'media_url');
                    }
                }
            }

            $urls = RevSliderFunctions::getVal($entities, 'urls');
            if (is_array($urls) && @RevsliderPrestashop::getIsset($urls[0])) {
                $display_url = RevSliderFunctions::getVal($urls[0], 'display_url');


                //check if youtube or vimeo is inside
                if (strpos($display_url, 'youtu.be') !== false) {
                    $raw = explode('/', $display_url);
                    $yturl = $raw[1];
                    $this->params["slide_bg_youtube"] = $yturl; //set video for background video
                } elseif (strpos($display_url, 'vimeo.com') !== false) {
                    $raw = explode('/', $display_url);
                    $vmurl = $raw[1];
                    $this->params["slide_bg_vimeo"] = $vmurl; //set video for background video
                }
            }

            $image_url_array = RevSliderFunctions::getVal($entities, 'media');
            if (is_array($image_url_array) && @RevsliderPrestashop::getIsset($image_url_array[0])) {
                $video_info = RevSliderFunctions::getVal($image_url_array[0], 'video_info');
                $variants = RevSliderFunctions::getVal($video_info, 'variants');
                if (is_array($variants) && @RevsliderPrestashop::getIsset($variants[0])) {
                    $mp4 = RevSliderFunctions::getVal($variants[0], 'url');

                    $this->params["slide_bg_html_mpeg"] = $mp4; //set video for background video
                }
            }

            $entities = RevSliderFunctions::getVal($this->postData, 'extended_entities');
            if ($img == '') {
                $image_url_array = RevSliderFunctions::getVal($entities, 'media');
                if (is_array($image_url_array) && @RevsliderPrestashop::getIsset($image_url_array[0])) {
                    if (is_ssl()) {
                        $img = RevSliderFunctions::getVal($image_url_array[0], 'media_url_https');
                    } else {
                        $img = RevSliderFunctions::getVal($image_url_array[0], 'media_url');
                    }
                }
            }

            $urls = RevSliderFunctions::getVal($entities, 'urls');
            if (is_array($urls) && @RevsliderPrestashop::getIsset($urls[0])) {
                $display_url = RevSliderFunctions::getVal($urls[0], 'display_url');


                //check if youtube or vimeo is inside
                if (strpos($display_url, 'youtu.be') !== false) {
                    $raw = explode('/', $display_url);
                    $yturl = $raw[1];
                    $this->params["slide_bg_youtube"] = $yturl; //set video for background video
                } elseif (strpos($display_url, 'vimeo.com') !== false) {
                    $raw = explode('/', $display_url);
                    $vmurl = $raw[1];
                    $this->params["slide_bg_vimeo"] = $vmurl; //set video for background video
                }
            }

            $image_url_array = RevSliderFunctions::getVal($entities, 'media');
            if (is_array($image_url_array) && @RevsliderPrestashop::getIsset($image_url_array[0])) {
                $video_info = RevSliderFunctions::getVal($image_url_array[0], 'video_info');
                $variants = RevSliderFunctions::getVal($video_info, 'variants');
                if (is_array($variants) && @RevsliderPrestashop::getIsset($variants[0])) {
                    $mp4 = RevSliderFunctions::getVal($variants[0], 'url');
                    $this->params["slide_bg_html_mpeg"] = $mp4; //set video for background video
                }
            }

            if ($img !== '') {
                $this->imageUrl = $img;
                $this->imageThumb = $img;
            }

            //if(empty($this->imageUrl))
            //	return(false);

            if (empty($this->imageUrl)) {
                $this->imageUrl = _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sources/tw.png';
            }

            if (is_ssl()) {
                $this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
            }

            $this->imageFilename = basename($this->imageUrl);
        }

        //replace placeholders in layers:
        $this->setLayersByStreamData($sliderID, 'twitter', $additions);
    }

    /**
     * init the data for instagram
     * @since: 5.0
     */
    private function initByInstagram($sliderID)
    {
        //set some slide params
        $this->id = RevSliderFunctions::getVal($this->postData, 'id');

        $caption = RevSliderFunctions::getVal($this->postData, 'caption');

        $this->params["title"] = RevSliderFunctions::getVal($caption, 'text');

        $link = RevSliderFunctions::getVal($this->postData, 'link');

        if (@RevsliderPrestashop::getIsset($this->params['enable_link']) && $this->params['enable_link'] == "true" && @RevsliderPrestashop::getIsset($this->params['link_type']) && $this->params['link_type'] == "regular") {
            $this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
        }

        $this->params["state"] = "published";

        if ($this->params["background_type"] == 'trans' || $this->params["background_type"] == 'image' || $this->params["background_type"] == 'streaminstagram' || $this->params["background_type"] == 'streaminstagramboth') { //if image is choosen, use featured image as background
            $img_sizes = RevSliderBase::getAllImageSizes('instagram');

            $imgResolution = RevSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));
            if (!@RevsliderPrestashop::getIsset($img_sizes[$imgResolution])) {
                $imgResolution = key($img_sizes);
            }

            $this->imageID = RevSliderFunctions::getVal($this->postData, 'id');
            $imgs = RevSliderFunctions::getVal($this->postData, 'images', array());
            $is = array();
            foreach ($imgs as $k => $im) {
                $is[$k] = $im->url;
            }

            $this->imageUrl = $is[$imgResolution];
            $this->imageThumb = $is['thumbnail'];

            //if(empty($this->imageUrl))
            //	return(false);

            if (empty($this->imageUrl)) {
                $this->imageUrl = _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sources/ig.png';
            }

            if (is_ssl()) {
                $this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
            }

            $this->imageFilename = basename($this->imageUrl);
        }

        $videos = RevSliderFunctions::getVal($this->postData, 'videos');

        if (!empty($videos) && @RevsliderPrestashop::getIsset($videos->standard_resolution) && @RevsliderPrestashop::getIsset($videos->standard_resolution->url)) {
            $this->params["slide_bg_instagram"] = $videos->standard_resolution->url; //set video for background video
            $this->params["slide_bg_html_mpeg"] = $videos->standard_resolution->url; //set video for background video
        }


        //replace placeholders in layers:
        $this->setLayersByStreamData($sliderID, 'instagram');
    }

    /**
     * init the data for flickr
     * @since: 5.0
     */
    private function initByFlickr($sliderID)
    {
        //set some slide params
        $this->id = RevSliderFunctions::getVal($this->postData, 'id');
        $this->params["title"] = RevSliderFunctions::getVal($this->postData, 'title');

        if (@RevsliderPrestashop::getIsset($this->params['enable_link']) && $this->params['enable_link'] == "true" && @RevsliderPrestashop::getIsset($this->params['link_type']) && $this->params['link_type'] == "regular") {
            $link = 'http://flic.kr/p/' . $this->baseEncode(RevSliderFunctions::getVal($this->postData, 'id'));
            $this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
        }

        $this->params["state"] = "published";

        if ($this->params["background_type"] == 'image') { //if image is choosen, use featured image as background
            //facebook check which image size is choosen
            $img_sizes = RevSliderBase::getAllImageSizes('flickr');


            $imgResolution = RevSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));

            $this->imageID = RevSliderFunctions::getVal($this->postData, 'id');
            if (!@RevsliderPrestashop::getIsset($img_sizes[$imgResolution])) {
                $imgResolution = key($img_sizes);
            }

            $is = @array(
                'square' => RevSliderFunctions::getVal($this->postData, 'url_sq'),
                'large-square' => RevSliderFunctions::getVal($this->postData, 'url_q'),
                'thumbnail' => RevSliderFunctions::getVal($this->postData, 'url_t'),
                'small' => RevSliderFunctions::getVal($this->postData, 'url_s'),
                'small-320' => RevSliderFunctions::getVal($this->postData, 'url_n'),
                'medium' => RevSliderFunctions::getVal($this->postData, 'url_m'),
                'medium-640' => RevSliderFunctions::getVal($this->postData, 'url_z'),
                'medium-800' => RevSliderFunctions::getVal($this->postData, 'url_c'),
                'large' => RevSliderFunctions::getVal($this->postData, 'url_l'),
                'original' => RevSliderFunctions::getVal($this->postData, 'url_o')
            );

            $this->imageUrl = (@RevsliderPrestashop::getIsset($is[$imgResolution])) ? $is[$imgResolution] : '';
            $this->imageThumb = (@RevsliderPrestashop::getIsset($is['thumbnail'])) ? $is['thumbnail'] : '';

            //if(empty($this->imageUrl))
            //	return(false);

            if (empty($this->imageUrl)) {
                $this->imageUrl = _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sources/fr.png';
            }

            if (is_ssl()) {
                $this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
            }

            $this->imageFilename = basename($this->imageUrl);

//            if (!empty($thumbID))
//                $this->setImageByImageURL($thumbID);
        }
        //replace placeholders in layers:
        $this->setLayersByStreamData($sliderID, 'flickr');
    }

    /**
     * init the data for youtube
     * @since: 5.0
     */
    private function initByYoutube($sliderID, $additions)
    {

        //set some slide params
        $snippet = RevSliderFunctions::getVal($this->postData, 'snippet');
        $resource = RevSliderFunctions::getVal($snippet, 'resourceId');

        if ($additions['yt_type'] == 'channel') {
            $link_raw = RevSliderFunctions::getVal($this->postData, 'id');
            $link = RevSliderFunctions::getVal($link_raw, 'videoId');
        } else {
            $link_raw = RevSliderFunctions::getVal($snippet, 'resourceId');
            $link = RevSliderFunctions::getVal($link_raw, 'videoId');
        }


        if (@RevsliderPrestashop::getIsset($this->params['enable_link']) && $this->params['enable_link'] == "true" && @RevsliderPrestashop::getIsset($this->params['link_type']) && $this->params['link_type'] == "regular") {
            if ($link !== '') {
                $link = '//youtube.com/watch?v=' . $link;
            }

            $this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
        }

        $this->params["slide_bg_youtube"] = $link; //set video for background video


        switch ($additions['yt_type']) {
            case 'channel':
                $id = RevSliderFunctions::getVal($this->postData, 'id');
                $this->id = RevSliderFunctions::getVal($id, 'videoId');
                break;
            case 'playlist':
                $this->id = RevSliderFunctions::getVal($resource, 'videoId');
                break;
        }
        if ($this->id == '') {
            $this->id = 'not-found';
        }

        $this->params["title"] = RevSliderFunctions::getVal($snippet, 'title');

        $this->params["state"] = "published";

        if ($this->params["background_type"] == 'trans' || $this->params["background_type"] == 'image' || $this->params["background_type"] == 'streamyoutube' || $this->params["background_type"] == 'streamyoutubeboth') { //if image is choosen, use featured image as background
            //facebook check which image size is choosen
            $img_sizes = RevSliderBase::getAllImageSizes('youtube');

            $imgResolution = RevSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));

            $this->imageID = RevSliderFunctions::getVal($resource, 'videoId');
            if (!@RevsliderPrestashop::getIsset($img_sizes[$imgResolution])) {
                $imgResolution = key($img_sizes);
            }

            $thumbs = RevSliderFunctions::getVal($snippet, 'thumbnails');
            $is = array();
            if (!empty($thumbs)) {
                foreach ($thumbs as $name => $vals) {
                    $is[$name] = RevSliderFunctions::getVal($vals, 'url');
                }
            }

            $this->imageUrl = (@RevsliderPrestashop::getIsset($is[$imgResolution])) ? $is[$imgResolution] : '';
            $this->imageThumb = (@RevsliderPrestashop::getIsset($is['medium'])) ? $is['medium'] : '';

            //if(empty($this->imageUrl))
            //	return(false);

            if (empty($this->imageUrl)) {
                $this->imageUrl = _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sources/yt.png';
            }

            if (is_ssl()) {
                $this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
            }

            $this->imageFilename = basename($this->imageUrl);

//            if (!empty($thumbID))
//                $this->setImageByImageURL($thumbID);
        }
        //replace placeholders in layers:
        $this->setLayersByStreamData($sliderID, 'youtube', $additions);
    }

    /**
     * init the data for vimeo
     * @since: 5.0
     */
    private function initByVimeo($sliderID, $additions)
    {

        //set some slide params
        $this->id = RevSliderFunctions::getVal($this->postData, 'id');
        $this->params["title"] = RevSliderFunctions::getVal($this->postData, 'title');

        if (@RevsliderPrestashop::getIsset($this->params['enable_link']) && $this->params['enable_link'] == "true" && @RevsliderPrestashop::getIsset($this->params['link_type']) && $this->params['link_type'] == "regular") {
            $link = RevSliderFunctions::getVal($this->postData, 'url');
            $this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
        }

        $this->params["slide_bg_vimeo"] = RevSliderFunctions::getVal($this->postData, 'url');

        $this->params["state"] = "published";

        if ($this->params["background_type"] == 'trans' || $this->params["background_type"] == 'image' || $this->params["background_type"] == 'streamvimeo' || $this->params["background_type"] == 'streamvimeoboth') { //if image is choosen, use featured image as background
            //facebook check which image size is choosen
            $img_sizes = RevSliderBase::getAllImageSizes('vimeo');
            $imgResolution = RevSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));

            $this->imageID = RevSliderFunctions::getVal($this->postData, 'id');
            if (!@RevsliderPrestashop::getIsset($img_sizes[$imgResolution])) {
                $imgResolution = key($img_sizes);
            }

            $is = array();

            foreach ($img_sizes as $handle => $name) {
                $is[$handle] = RevSliderFunctions::getVal($this->postData, $handle);
            }


            $this->imageUrl = (@RevsliderPrestashop::getIsset($is[$imgResolution])) ? $is[$imgResolution] : '';
            $this->imageThumb = (@RevsliderPrestashop::getIsset($is['thumbnail'])) ? $is['thumbnail'] : '';

            //if(empty($this->imageUrl))
            //	return(false);

            if (empty($this->imageUrl)) {
                $this->imageUrl = _MODULE_DIR_ . 'revsliderprestashop/views/img/images/sources/vm.png';
            }

            if (is_ssl()) {
                $this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
            }

            $this->imageFilename = basename($this->imageUrl);

//            if (!empty($thumbID))
//                $this->setImageByImageURL($thumbID);
        }
        //replace placeholders in layers:

        $this->setLayersByStreamData($sliderID, 'vimeo', $additions);
    }

    /**
     * replace layer placeholders by stream data
     * @since: 5.0
     */
    private function setLayersByStreamData($sliderID, $stream_type, $additions = array())
    {
        $attr = $this->returnStreamData($stream_type, $additions);

        foreach ($this->arrLayers as $key => $layer) {
            $text = RevSliderFunctions::getVal($layer, "text");


            $text = $this->setStreamData($text, $attr, $stream_type, $additions);

            $layer["text"] = $text;

            //set link actions to the stream data
            $layer['layer_action'] = (array) $layer['layer_action'];
            if (@RevsliderPrestashop::getIsset($layer['layer_action'])) {
                if (@RevsliderPrestashop::getIsset($layer['layer_action']['image_link']) && !empty($layer['layer_action']['image_link'])) {
                    foreach ($layer['layer_action']['image_link'] as $jtsk => $jtsval) {
                        $layer['layer_action']['image_link'][$jtsk] = $this->setStreamData($layer['layer_action']['image_link'][$jtsk], $attr, $stream_type, $additions, true);
                    }
                }
            }
            $this->arrLayers[$key] = $layer;
        }

        //set params to the stream data
        for ($mi = 1; $mi <= 10; $mi++) {
            $pa = $this->getParam('params_' . $mi, '');
            $pa = $this->setStreamData($pa, $attr, $stream_type, $additions);
            $this->setParam('params_' . $mi, $pa);
        }
    }

    public function setStreamData($text, $attr, $stream_type, $additions = array(), $is_action = false)
    {
        $img_sizes = RevSliderBase::getAllImageSizes($stream_type);

        $text = str_replace(array('%title%', '{{title}}'), $attr['title'], $text);
        $text = str_replace(array('%excerpt%', '{{excerpt}}'), $attr['excerpt'], $text);
        $text = str_replace(array('%alias%', '{{alias}}'), $attr['alias'], $text);
        $text = str_replace(array('%content%', '{{content}}'), $attr['content'], $text);
        $text = str_replace(array('%link%', '{{link}}'), $attr['link'], $text);
        $text = str_replace(array('%date_published%', '{{date_published}}', '%date%', '{{date}}'), $attr['date'], $text);
        $text = str_replace(array('%date_modified%', '{{date_modified}}'), $attr['date_modified'], $text);
        $text = str_replace(array('%author_name%', '{{author_name}}'), $attr['author_name'], $text);
        $text = str_replace(array('%num_comments%', '{{num_comments}}'), $attr['num_comments'], $text);
        $text = str_replace(array('%catlist%', '{{catlist}}'), $attr['catlist'], $text);
        $text = str_replace(array('%taglist%', '{{taglist}}'), $attr['taglist'], $text);
        $text = str_replace(array('%likes%', '{{likes}}'), $attr['likes'], $text);
        $text = str_replace(array('%retweet_count%', '{{retweet_count}}'), $attr['retweet_count'], $text);
        $text = str_replace(array('%favorite_count%', '{{favorite_count}}'), $attr['favorite_count'], $text);
        $text = str_replace(array('%views%', '{{views}}'), $attr['views'], $text);

        if ($stream_type == 'twitter' && $is_action === false) {
            $text = RevSliderBase::addWrapAroundUrl($text);
        }

        switch ($stream_type) {
            case 'facebook':
                foreach ($img_sizes as $img_handle => $img_name) {
                    if (!@RevsliderPrestashop::getIsset($attr['img_urls'])) {
                        $attr['img_urls'] = array();
                    }
                    if (!@RevsliderPrestashop::getIsset($attr['img_urls'][$img_handle])) {
                        $attr['img_urls'][$img_handle] = array();
                    }
                    if (!@RevsliderPrestashop::getIsset($attr['img_urls'][$img_handle]['url'])) {
                        $attr['img_urls'][$img_handle]['url'] = '';
                    }
                    if (!@RevsliderPrestashop::getIsset($attr['img_urls'][$img_handle]['tag'])) {
                        $attr['img_urls'][$img_handle]['tag'] = '';
                    }

                    if ($additions['fb_type'] == 'album') {
                        $text = str_replace(array('%image_url_' . $img_handle . '%', '{{image_url_' . $img_handle . '}}'), $attr['img_urls'][$img_handle]['url'], $text);
                        $text = str_replace(array('%image_' . $img_handle . '%', '{{image_' . $img_handle . '}}'), $attr['img_urls'][$img_handle]['tag'], $text);
                    } else {
                        $text = str_replace(array('%image_url_' . $img_handle . '%', '{{image_url_' . $img_handle . '}}'), $attr['img_urls']['url'], $text);
                        $text = str_replace(array('%image_' . $img_handle . '%', '{{image_' . $img_handle . '}}'), $attr['img_urls']['tag'], $text);
                    }
                }
                break;
            case 'youtube':
            case 'vimeo':
            //$text = str_replace(array('%image_url_'.$img_handle.'%', '{{image_url_'.$img_handle.'}}'), @$attr['img_urls'][$img_handle]['url'], $text);
            //$text = str_replace(array('%image_'.$img_handle.'%', '{{image_'.$img_handle.'}}'), @$attr['img_urls'][$img_handle]['tag'], $text);
            case 'twitter':
            case 'instagram':
            case 'flickr':
                foreach ($img_sizes as $img_handle => $img_name) {
                    if (!@RevsliderPrestashop::getIsset($attr['img_urls'])) {
                        $attr['img_urls'] = array();
                    }
                    if (!@RevsliderPrestashop::getIsset($attr['img_urls'][$img_handle])) {
                        $attr['img_urls'][$img_handle] = array();
                    }
                    if (!@RevsliderPrestashop::getIsset($attr['img_urls'][$img_handle]['url'])) {
                        $attr['img_urls'][$img_handle]['url'] = '';
                    }
                    if (!@RevsliderPrestashop::getIsset($attr['img_urls'][$img_handle]['tag'])) {
                        $attr['img_urls'][$img_handle]['tag'] = '';
                    }

                    $text = str_replace(array('%image_url_' . $img_handle . '%', '{{image_url_' . $img_handle . '}}'), $attr['img_urls'][$img_handle]['url'], $text);
                    $text = str_replace(array('%image_' . $img_handle . '%', '{{image_' . $img_handle . '}}'), $attr['img_urls'][$img_handle]['tag'], $text);
                }
                break;
        }

        return $text;
    }

    public function returnStreamData($stream_type, $additions = array())
    {
        $attr = array();
        $attr['title'] = '';
        $attr['excerpt'] = '';
        $attr['alias'] = '';
        $attr['content'] = '';
        $attr['link'] = '';
        $attr['date'] = '';
        $attr['date_modified'] = '';
        $attr['author_name'] = '';
        $attr['num_comments'] = '';
        $attr['catlist'] = '';
        $attr['taglist'] = '';
        $attr['likes'] = '';
        $attr['retweet_count'] = '';
        $attr['favorite_count'] = '';
        $attr['views'] = '';
        $attr['img_urls'] = array();

        $img_sizes = RevSliderBase::getAllImageSizes($stream_type);

        switch ($stream_type) {
            case 'facebook':
                if ($additions['fb_type'] == 'album') {
                    $attr['title'] = RevSliderFunctions::getVal($this->postData, 'name');
                    $attr['content'] = RevSliderFunctions::getVal($this->postData, 'name');
                    $attr['link'] = RevSliderFunctions::getVal($this->postData, 'link');
                    $attr['date'] = RevSliderFunctionsWP::convertPostDate(RevSliderFunctions::getVal($this->postData, 'created_time'), true);
                    $attr['date_modified'] = RevSliderFunctionsWP::convertPostDate(RevSliderFunctions::getVal($this->postData, 'updated_time'), true);
                    $author_name_raw = RevSliderFunctions::getVal($this->postData, 'from');
                    $attr['author_name'] = $author_name_raw->name;
                    $likes_data = RevSliderFunctions::getVal($this->postData, 'likes');
                    $attr['likes'] = count(RevSliderFunctions::getVal($likes_data, 'data'));
                    $fb_img_thumbnail = RevSliderFunctions::getVal($this->postData, 'picture');
                    $fb_img = 'https://graph.facebook.com/' . RevSliderFunctions::getVal($this->postData, 'id') . '/picture';

                    $attr['img_urls']['full'] = array(
                        'url' => $fb_img,
                        'tag' => '<img src="' . $fb_img . '" data-no-retina />'
                    );
                    $attr['img_urls']['thumbnail'] = array(
                        'url' => $fb_img_thumbnail,
                        'tag' => '<img src="' . $fb_img_thumbnail . '" data-no-retina />'
                    );
                } else {
                    $attr['title'] = RevSliderFunctions::getVal($this->postData, 'message');
                    $attr['content'] = RevSliderFunctions::getVal($this->postData, 'message');
                    $post_url = explode('_', RevSliderFunctions::getVal($this->postData, 'id'));
                    $attr['link'] = 'https://www.facebook.com/' . $additions['fb_user_id'] . '/posts/' . $post_url[1];
                    $attr['date'] = RevSliderFunctionsWP::convertPostDate(RevSliderFunctions::getVal($this->postData, 'created_time'), true);
                    $attr['date_modified'] = RevSliderFunctionsWP::convertPostDate(RevSliderFunctions::getVal($this->postData, 'updated_time'), true);
                    $author_name_raw = RevSliderFunctions::getVal($this->postData, 'from');
                    $attr['author_name'] = $author_name_raw->name;
                    $likes_data = RevSliderFunctions::getVal($this->postData, 'likes');

                    $likes_data = RevSliderFunctions::getVal($likes_data, 'summary');
                    $likes_data = RevSliderFunctions::getVal($likes_data, 'total_count');

                    $attr['likes'] = (int) ($likes_data);
                    $img = $this->getFacebookTimelineImage();
                    $attr['img_urls'] = array(
                        'url' => $img,
                        'tag' => '<img src="' . $img . '" data-no-retina />'
                    );
                }
                break;
            case 'twitter':
                $user = RevSliderFunctions::getVal($this->postData, 'user');
                $attr['title'] = RevSliderFunctions::getVal($this->postData, 'text');
                $attr['content'] = RevSliderFunctions::getVal($this->postData, 'text');
                $attr['link'] = 'https://twitter.com/' . $additions['twitter_user'] . '/status/' . RevSliderFunctions::getVal($this->postData, 'id_str');
                $attr['date'] = RevSliderFunctionsWP::convertPostDate(RevSliderFunctions::getVal($this->postData, 'created_at'), true);
                $attr['author_name'] = RevSliderFunctions::getVal($user, 'screen_name');
                $attr['retweet_count'] = RevSliderFunctions::getVal($this->postData, 'retweet_count', '0');
                $attr['favorite_count'] = RevSliderFunctions::getVal($this->postData, 'favorite_count', '0');
                $image_url_array = RevSliderFunctions::getVal($this->postData, 'media');
                $image_url_large = RevSliderFunctions::getVal($image_url_array, 'large');
                $img = RevSliderFunctions::getVal($image_url_large, 'media_url', '');
                if ($img == '') {
                    $entities = RevSliderFunctions::getVal($this->postData, 'entities');
                    $image_url_array = RevSliderFunctions::getVal($entities, 'media');
                    if (is_array($image_url_array) && @RevsliderPrestashop::getIsset($image_url_array[0])) {
                        if (is_ssl()) {
                            $img = RevSliderFunctions::getVal($image_url_array[0], 'media_url_https');
                        } else {
                            $img = RevSliderFunctions::getVal($image_url_array[0], 'media_url');
                        }

                        $image_url_large = $image_url_array[0];
                    }
                }
                if ($img == '') {
                    $entities = RevSliderFunctions::getVal($this->postData, 'extended_entities');
                    $image_url_array = RevSliderFunctions::getVal($entities, 'media');
                    if (is_array($image_url_array) && @RevsliderPrestashop::getIsset($image_url_array[0])) {
                        if (is_ssl()) {
                            $img = RevSliderFunctions::getVal($image_url_array[0], 'media_url_https');
                        } else {
                            $img = RevSliderFunctions::getVal($image_url_array[0], 'media_url');
                        }

                        $image_url_large = $image_url_array[0];
                    }
                }
                if ($img !== '') {
                    $w = RevSliderFunctions::getVal($image_url_large, 'w', '');
                    $h = RevSliderFunctions::getVal($image_url_large, 'h', '');
                    $attr['img_urls']['large'] = array(
                        'url' => $img,
                        'tag' => '<img src="' . $img . '" width="' . $w . '" height="' . $h . '" data-no-retina />'
                    );
                }
                break;
            case 'instagram':
                $caption = RevSliderFunctions::getVal($this->postData, 'caption');
                $user = RevSliderFunctions::getVal($this->postData, 'user');

                $attr['title'] = RevSliderFunctions::getVal($caption, 'text');
                $attr['content'] = RevSliderFunctions::getVal($caption, 'text');
                $attr['link'] = RevSliderFunctions::getVal($this->postData, 'link');
                $attr['date'] = RevSliderFunctionsWP::convertPostDate(RevSliderFunctions::getVal($this->postData, 'created_time'), true);
                $attr['author_name'] = RevSliderFunctions::getVal($user, 'username');

                $likes_raw = RevSliderFunctions::getVal($this->postData, 'likes');
                $attr['likes'] = RevSliderFunctions::getVal($likes_raw, 'count');

                $comments_raw = RevSliderFunctions::getVal($this->postData, 'comments');
                $attr['num_comments'] = RevSliderFunctions::getVal($comments_raw, 'count');

                $inst_img = RevSliderFunctions::getVal($this->postData, 'images', array());
                foreach ($inst_img as $key => $img) {
                    $attr['img_urls'][$key] = array(
                        'url' => $img->url,
                        'tag' => '<img src="' . $img->url . '" width="' . $img->width . '" height="' . $img->height . '" data-no-retina />'
                    );
                }
                break;
            case 'flickr':
                $attr['title'] = RevSliderFunctions::getVal($this->postData, 'title');
                $tc = RevSliderFunctions::getVal($this->postData, 'description');
                $attr['content'] = RevSliderFunctions::getVal($tc, '_content');
                $attr['date'] = RevSliderFunctionsWP::convertPostDate(RevSliderFunctions::getVal($this->postData, 'datetaken'));
                $attr['author_name'] = RevSliderFunctions::getVal($this->postData, 'ownername');
                $attr['link'] = 'http://flic.kr/p/' . $this->baseEncode(RevSliderFunctions::getVal($this->postData, 'id'));
                $attr['views'] = RevSliderFunctions::getVal($this->postData, 'views');

                $attr['img_urls'] = @array(
                    'square' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_sq'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_sq') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_sq') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_sq') . '" data-no-retina />'),
                    'large-square' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_q'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_q') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_q') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_q') . '"  data-no-retina />'),
                    'thumbnail' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_t'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_t') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_t') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_t') . '"  data-no-retina />'),
                    'small' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_s'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_s') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_s') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_s') . '"  data-no-retina />'),
                    'small-320' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_n'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_n') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_n') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_n') . '"  data-no-retina />'),
                    'medium' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_m'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_m') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_m') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_m') . '"  data-no-retina />'),
                    'medium-640' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_z'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_z') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_z') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_z') . '"  data-no-retina />'),
                    'medium-800' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_c'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_c') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_c') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_c') . '"  data-no-retina />'),
                    'large' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_l'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_l') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_l') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_l') . '"  data-no-retina />'),
                    'original' => array('url' => RevSliderFunctions::getVal($this->postData, 'url_o'), 'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, 'url_o') . '" width="' . RevSliderFunctions::getVal($this->postData, 'width_o') . '" height="' . RevSliderFunctions::getVal($this->postData, 'height_o') . '"  data-no-retina />')
                );
                break;
            case 'youtube':
                $snippet = RevSliderFunctions::getVal($this->postData, 'snippet');
                $attr['title'] = RevSliderFunctions::getVal($snippet, 'title');
                $attr['excerpt'] = RevSliderFunctions::getVal($snippet, 'description');
                $attr['content'] = RevSliderFunctions::getVal($snippet, 'description');
                $attr['date'] = RevSliderFunctionsWP::convertPostDate(RevSliderFunctions::getVal($snippet, 'publishedAt'));

                if ($additions['yt_type'] == 'channel') {
                    $link_raw = RevSliderFunctions::getVal($this->postData, 'id');
                    $attr['link'] = RevSliderFunctions::getVal($link_raw, 'videoId');
                    if ($attr['link'] !== '') {
                        $attr['link'] = '//youtube.com/watch?v=' . $attr['link'];
                    }
                } else {
                    $link_raw = RevSliderFunctions::getVal($this->postData, 'resourceId');
                    $attr['link'] = RevSliderFunctions::getVal($link_raw, 'videoId');
                    if ($attr['link'] !== '') {
                        $attr['link'] = '//youtube.com/watch?v=' . $attr['link'];
                    }
                }

                $thumbs = RevSliderFunctions::getVal($snippet, 'thumbnails');
                $attr['img_urls'] = array();
                if (!empty($thumbs)) {
                    foreach ($thumbs as $name => $vals) {
                        $attr['img_urls'][$name] = array(
                            'url' => RevSliderFunctions::getVal($vals, 'url'),
                        );
                        switch ($additions['yt_type']) {
                            case 'channel':
                                $attr['img_urls'][$name]['tag'] = '<img src="' . RevSliderFunctions::getVal($vals, 'url') . '" data-no-retina />';
                                break;
                            case 'playlist':
                                $attr['img_urls'][$name]['tag'] = '<img src="' . RevSliderFunctions::getVal($vals, 'url') . '" width="' . RevSliderFunctions::getVal($vals, 'width') . '" height="' . RevSliderFunctions::getVal($vals, 'height') . '" data-no-retina />';
                                break;
                        }
                    }
                }
                break;
            case 'vimeo':
                $attr['title'] = RevSliderFunctions::getVal($this->postData, 'title');
                $attr['excerpt'] = RevSliderFunctions::getVal($this->postData, 'description');
                $attr['content'] = RevSliderFunctions::getVal($this->postData, 'description');
                $attr['date'] = RevSliderFunctionsWP::convertPostDate(RevSliderFunctions::getVal($this->postData, 'upload_date'));
                $attr['likes'] = RevSliderFunctions::getVal($this->postData, 'stats_number_of_likes');
                $attr['views'] = RevSliderFunctions::getVal($this->postData, 'stats_number_of_plays');
                $attr['num_comments'] = RevSliderFunctions::getVal($this->postData, 'stats_number_of_comments');
                $attr['link'] = RevSliderFunctions::getVal($this->postData, 'url');
                $attr['author_name'] = RevSliderFunctions::getVal($this->postData, 'user_name');

                $attr['img_urls'] = array();
                if (!empty($img_sizes)) {
                    foreach ($img_sizes as $name => $vals) {
                        $attr['img_urls'][$name] = array(
                            'url' => RevSliderFunctions::getVal($this->postData, $name),
                            'tag' => '<img src="' . RevSliderFunctions::getVal($this->postData, $name) . '" data-no-retina />'
                        );
                    }
                }

                break;
        }

        return $attr;
    }

    public function findBiggestPhoto($image_urls, $wanted_size, $avail_sizes)
    {
        if (!$this->isEmpty(@$image_urls[$wanted_size])) {
            return $image_urls[$wanted_size];
        }
        $wanted_size_pos = array_search($wanted_size, $avail_sizes);
        for ($i = $wanted_size_pos; $i < 7; $i++) {
            if (!$this->isEmpty(@$image_urls[$avail_sizes[$i]])) {
                return $image_urls[$avail_sizes[$i]];
            }
        }
        for ($i = $wanted_size_pos; $i >= 0; $i--) {
            if (!$this->isEmpty(@$image_urls[$avail_sizes[$i]])) {
                return $image_urls[$avail_sizes[$i]];
            }
        }
    }

    public function isEmpty($stringOrArray)
    {
        if (is_array($stringOrArray)) {
            foreach ($stringOrArray as $value) {
                if (!$this->isEmpty($value)) {
                    return false;
                }
            }
            return true;
        }

        return !Tools::strlen($stringOrArray);  // this properly checks on empty string ('')
    }

    public function getFacebookTimelineImage()
    {
        $object_id = RevSliderFunctions::getVal($this->postData, 'object_id', '');
        $picture = RevSliderFunctions::getVal($this->postData, 'picture', '');
        if (!empty($object_id)) {
            return 'https://graph.facebook.com/' . RevSliderFunctions::getVal($this->postData, 'object_id', '') . '/picture';
        } elseif (!empty($picture)) {
            $image_url = $this->decodeFacebookUrl(RevSliderFunctions::getVal($this->postData, 'picture', ''));
            $image_url = parse_str(parse_url($image_url, PHP_URL_QUERY), $array);
            $image_url = explode('&', $array['url']);
            return $image_url[0];
        }
        return '';
    }

    private function decodeFacebookUrl($url)
    {
        $url = str_replace('u00253A', ':', $url);
        $url = str_replace('\u00255C\u00252F', '/', $url);
        $url = str_replace('u00252F', '/', $url);
        $url = str_replace('u00253F', '?', $url);
        $url = str_replace('u00253D', '=', $url);
        $url = str_replace('u002526', '&', $url);
        return $url;
    }

    /**
     * Encode the flickr ID for URL (base58)
     *
     * @since    5.0
     * @param    string    $num 	flickr photo id
     */
    private function baseEncode($num, $alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ')
    {
        $base_count = Tools::strlen($alphabet);
        $alphabet = str_split($alphabet);
        $encoded = '';
        while ($num >= $base_count) {
            $div = $num / $base_count;
            $mod = ($num - ($base_count * (int) ($div)));
            $encoded = $alphabet[$mod] . $encoded;
            $num = (int) ($div);
        }
        if ($num) {
            $encoded = $alphabet[$num] . $encoded;
        }
        return $encoded;
    }

    public function initByPostData($postData, RevSlide $slideTemplate, $sliderID)
    {
        $this->postData = $this->postData;

        $postID = $postData['id_product'];

        $arrWildcardsValues = RevOperations::getPostWilcardValues($postID);

        $slideTemplateID = UniteFunctionsRev::getVal($arrWildcardsValues, "slide_template");

        if (!empty($slideTemplateID) && is_numeric($slideTemplateID)) {

            //init by local template, if fail, init by global (slider) template

            try {
                $slideTemplateLocal = new RevSlide();

                $slideTemplateLocal->initByID($slideTemplateID);

                $this->initBySlide($slideTemplateLocal);
            } catch (Exception $e) {
                $this->initBySlide($slideTemplate);
            }
        } else {
            $this->initBySlide($slideTemplate);
        }

        $this->id = $postID;

        $this->params["title"] = UniteFunctionsRev::getVal($postData, "post_title");

        // if($this->params['enable_link'] == "true" && $this->params['link_type'] == "regular"){
        // $link = get_permalink($postID);
        // $this->params["link"] = str_replace("%link%", $link, $this->params["link"]);
        // $this->params["link"] = str_replace('-', '_REVSLIDER_', $this->params["link"]);
        // $arrMatches = array();
        // preg_match('/%product:\w+%/', $this->params["link"], $arrMatches);
        // foreach($arrMatches as $match){
        // 	$meta = str_replace("%product:", "", $match);
        // 	$meta = str_replace("%","",$meta);
        // 	$meta = str_replace('_REVSLIDER_', '-', $meta);
        // 	if(@RevsliderPrestashop::getIsset($postData[$meta]) && !empty($postData[$meta])){
        // 		$metaValue = $postData[$meta];
        // 		$this->params["link"] = str_replace($match,$metaValue,$this->params["link"]);
        // 	}
        // }
        // $this->params["link"] = str_replace('_REVSLIDER_','-',$this->params["link"]);
        // }

        $status = $postData["active"];

        if ($status == 1) {
            $this->params["state"] = "published";
        } else {
            $this->params["state"] = "unpublished";
        }



        $RevSlider = new RevSlider();
        $getSliderImgSettings = $RevSlider->getSliderImgSettings($sliderID);

        if (!empty($postID)) {
            $this->setImageByImageID($postID, $getSliderImgSettings);
        }

        //replace placeholders in layers:

        $this->setLayersByPostData($postData, $sliderID);
    }

    private function setImageSrc($postData = array())
    {
        $link = new Link();
        $lnk = $link->getImageLink($postData['link_rewrite'], $postData['id_image']); //Give here extra argument in Image type
        if (@RevsliderPrestashop::getIsset($lnk) && !empty($lnk)) {
            return 'http://' . htmlspecialchars_decode($lnk);
        } else {
            return false;
        }
    }

    private function setCountDown($postData = array())
    {
        $html = '';
        if (@RevsliderPrestashop::getIsset($postData) && @RevsliderPrestashop::getIsset($postData['specific_prices']) && !empty($postData['specific_prices'])) {
            $id_product = $postData['id_product'];
            $specific_prices = $postData['specific_prices'];
            $to_time = $specific_prices['to'];
            $to_time_str = strtotime($to_time);
            $to_time_y = date("Y", $to_time_str);
            $to_time_m = date("m", $to_time_str);
            $to_time_d = date("d", $to_time_str);
            $to_time_h = date("H", $to_time_str);
            $to_time_i = date("i", $to_time_str);
            $to_time_s = date("s", $to_time_str);
            $from_time = $specific_prices['from'];
            $now_time = date("Y-m-d H:i:s");
            if ($now_time <= $to_time && $now_time >= $from_time) {
                $html .= '<div class="product_count_down">
						<span class="turning_clock"></span>
						<div class="count_holder_small">
						<div class="count_info">
						</div>
						<div id="sds_rev_countdown_' . $id_product . '" class="count_content clearfix">
						</div>
						<div class="clear"></div>
						</div>
						</div>';
                $html .= "<script type='text/javascript'>
						$(function() {
                                      $('#sds_rev_countdown_" . $id_product . "').countdown({
                                          until: new Date(" . $to_time_y . "," . $to_time_m . " - 1," . $to_time_d . "," . $to_time_h . "," . $to_time_i . "," . $to_time_s . "), compact: false});
                                  });
		</script>";
//start add countdown CSS & JS Files
                $countdown_js = __PS_BASE_URI__ . 'modules/revsliderprestashop/js/countdown/jquery.countdown.js';
                $countdown_css = __PS_BASE_URI__ . 'modules/revsliderprestashop/css/countdown/countdown.css';
                Context::getcontext()->controller->addJs($countdown_js);
                Context::getcontext()->controller->addCSS($countdown_css);
// end start countdown JS
            }
        }
        return $html;
    }

    private function setLayersByPostData($postData, $sliderID)
    {
        $priceDisplay = Product::getTaxCalculationMethod((int) Context::getcontext()->cookie->id_customer);
        if (!$priceDisplay) {
            $productprice = Tools::displayPrice($postData["price"], Context::getContext()->currency);
        } else {
            $productprice = Tools::displayPrice($postData["price_tax_exc"], Context::getContext()->currency);
        }

        $postID = $postData["id_product"];



        $countdown = $this->setCountDown($postData);

        // $imgsrc = $this->setImageSrc($postData);

        $title = UniteFunctionsRev::getVal($postData, "name");

        $excerpt_limit = $this->getSliderParam($sliderID, "excerpt_limit", 55, RevSlider::VALIDATE_NUMERIC);

        $excerpt_limit = (int) $excerpt_limit;

        $description = Tools::substr($postData["description"], $excerpt_limit);

        $description_short = $postData["description_short"];

        // $alias = UniteFunctionsRev::getVal($postData, "post_name");
        // $content = UniteFunctionsRev::getVal($postData, "post_content");
        //$link = get_permalink($postID);
        $link = $postData["link"];

        $date_add = $postData["date_add"];

        //$date_add = UniteFunctionsWPRev::convertPostDate($date_add);

        $date_upd = $postData["date_upd"];

        //$date_upd = UniteFunctionsWPRev::convertPostDate($date_upd);

        $default_category = $postData["default_category"];

        $linkobj = new Link();

        $addtocart = $linkobj->getPageLink('cart', false, null, "add=1&amp;id_product=" . $postID, false);

        foreach ($this->arrLayers as $key => $layer) {
            $text = UniteFunctionsRev::getVal($layer, "text");
            $text = str_replace("%title%", $title, $text);
            $text = str_replace("%description_short%", $description_short, $text);
            $text = str_replace("%description%", $description, $text);
            $text = str_replace("%link%", $link, $text);
            $text = str_replace("%addtocart%", $addtocart, $text);
            $text = str_replace("%countdown%", $countdown, $text);
            // $text = str_replace("%imgsrc%", $imgsrc, $text);
            $text = str_replace("%date%", $date_add, $text);
            $text = str_replace("%date_modified%", $date_upd, $text);
            $text = str_replace("%product_price%", $productprice, $text);
            $text = str_replace("%default_category%", $default_category, $text);

            $arrMatches = array();
            $text = str_replace('-', '_REVSLIDER_', $text);

            preg_match_all('/%product:\w+%/', $text, $arrMatches);

            foreach ($arrMatches as $matched) {
                foreach ($matched as $match) {
                    $meta = str_replace("%product:", "", $match);
                    $meta = str_replace("%", "", $meta);
                    $meta = str_replace('_REVSLIDER_', '-', $meta);
                    if (@RevsliderPrestashop::getIsset($postData[$meta]) && !empty($postData[$meta])) {
                        $metaValue = $postData[$meta];
                        $text = str_replace($match, $metaValue, $text);
                    }
                }
            }
            $text = str_replace('_REVSLIDER_', '-', $text);

// start hook exec
            $extra_hook_meta_exec = array();
            Hook::exec('actionsdsrevinsertmetaexec', array(
                'extra_hook_meta_exec' => &$extra_hook_meta_exec,
                'id_product' => &$postID,
            ));
            if (@RevsliderPrestashop::getIsset($extra_hook_meta_exec) && !empty($extra_hook_meta_exec)) {
                foreach ($extra_hook_meta_exec as $svalue) {
                    $hook_title = "%" . $svalue['title'] . "%";
                    $hook_exec = $svalue['exec'];
                    $text = str_replace($hook_title, $hook_exec, $text);
                }
            }
// end hook exec
            $layer["text"] = $text;
            $this->arrLayers[$key] = $layer;
        }
    }

    public function initByID($slideid)
    {

        if (strpos($slideid, 'static_') !== false) {
            $this->static_slide = true;
            $sliderID = str_replace('static_', '', $slideid);

            UniteFunctionsRev::validateNumeric($sliderID, "Slider ID");

            $sliderID = $this->db->escape($sliderID);
            $record = $this->db->fetch(GlobalsRevSlider::$table_static_slides, "slider_id=$sliderID");

            if (empty($record)) {
                //create a new static slide for the Slider and then use it
                $slide_id = $this->createSlide($sliderID, "", true);

                $record = $this->db->fetch(GlobalsRevSlider::$table_static_slides, "slider_id=$sliderID");

                $this->initByData($record[0]);
            } else {
                $this->initByData($record[0]);
            }
        } else {
            UniteFunctionsRev::validateNumeric($slideid, "Slide ID");
            $slideid = $this->db->escape($slideid);
            $record = $this->db->fetchSingle(GlobalsRevSlider::$table_slides, "id=$slideid");
            $this->initByData($record);
        }
    }

    public function initByStaticID($slideid)
    {
        UniteFunctionsRev::validateNumeric($slideid, "Slide ID");
        $slideid = $this->db->escape($slideid);
        $record = $this->db->fetchSingle(GlobalsRevSlider::$table_static_slides, "id=$slideid");

        $this->initByData($record);
    }

    public function getStaticSlideID($sliderID)
    {
        UniteFunctionsRev::validateNumeric($sliderID, "Slider ID");

        $sliderID = $this->db->escape($sliderID);
        $record = $this->db->fetch(GlobalsRevSlider::$table_static_slides, "slider_id=$sliderID");
        if (empty($record)) {
            return false;
        } else {
            return $record[0]['id'];
        }
    }

    private function setImageByImageID($postID, $img_type = '')
    {
        $prdid_image = Product::getCover($postID);

        if (sizeof($prdid_image) > 0) {
            $prdimage = new Image($prdid_image['id_image']);

            $prdimage_url = _PS_BASE_URL_ . _THEME_PROD_DIR_;
            $prdimage_url .= $prdimage->getExistingImgPath() . (!empty($img_type) ? "-{$img_type}" : '') . ".jpg";
        }

        //$this->imageID = $imageID;
        $this->imageID = 0;

        //$this->imageUrl = UniteFunctionsWPRev::getUrlAttachmentImage($imageID);
        $this->imageUrl = $prdimage_url;

        // $this->imageThumb = UniteFunctionsWPRev::getUrlAttachmentImage($imageID,UniteFunctionsWPRev::THUMB_MEDIUM);
        $this->imageThumb = $prdimage_url;

        if (empty($this->imageUrl)) {
            return(false);
        }

        $this->params["background_type"] = "image";

        if (is_ssl()) {
            $this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
        }

        // $this->imageFilepath = UniteFunctionsWPRev::getImagePathFromURL($this->imageUrl);
        $this->imageFilepath = $prdimage_url;

        //$realPath = UniteFunctionsWPRev::getPathContent().$this->imageFilepath;
        $realPath = $prdimage_url;

        if (file_exists($realPath) == false || is_file($realPath) == false) {
            $this->imageFilepath = "";
        }

        $this->imageFilename = basename($this->imageUrl);
    }

    public function setArrChildren($arrChildren)
    {
        $this->arrChildren = $arrChildren;
    }

    public function getArrChildren()
    {
        $this->validateInited();
        if ($this->arrChildren === null) {
            $slider = new RevSlider();
            $slider->initByID($this->sliderID);
            $this->arrChildren = $slider->getArrSlideChildren($this->id);
        }
        return($this->arrChildren);
    }

    public function isFromPost()
    {
        return !empty($this->postData);
    }

    public function getPostData()
    {
        return($this->postData);
    }

    public function getArrChildrenPure()
    {
        return($this->arrChildren);
    }

    public function isParent()
    {
        $parentID = $this->getParam("parentid", "");

        return(!empty($parentID));
    }

    public function getLang()
    {
        $lang = $this->getParam("lang", "all");
        return($lang);
    }

    public function getParentSlide()
    {
        $parentID = $this->getParam("parentid", "");

        if (empty($parentID)) {
            return($this);
        }

        $parentSlide = new RevSlide();

        $parentSlide->initByID($parentID);

        return($parentSlide);
    }

    /**
     * return parent slide id
     * @since: 5.0
     */
    public function getParentSlideID()
    {
        $parentID = $this->getParam("parentid", "");

        return $parentID;
    }

    public function getArrChildrenIDs()
    {
        $arrChildren = $this->getArrChildren();

        $arrChildrenIDs = array();

        foreach ($arrChildren as $child) {
            $childID = $child->getID();

            $arrChildrenIDs[] = $childID;
        }



        return($arrChildrenIDs);
    }

    public function getArrChildrenLangs($includeParent = true)
    {
        $this->validateInited();

        $slideID = $this->id;

        if ($includeParent == true) {
            $lang = $this->getParam("lang", "all");

            $arrOutput = array();

            $arrOutput[] = array("slideid" => $slideID, "lang" => $lang, "isparent" => true);
        }



        $arrChildren = $this->getArrChildren();



        foreach ($arrChildren as $child) {
            $childID = $child->getID();

            $childLang = $child->getParam("lang", "all");

            $arrOutput[] = array("slideid" => $childID, "lang" => $childLang, "isparent" => false);
        }



        return($arrOutput);
    }

    public function getArrChildLangCodes($includeParent = true)
    {
        $arrLangsWithSlideID = $this->getArrChildrenLangs($includeParent);

        $arrLangCodes = array();

        foreach ($arrLangsWithSlideID as $item) {
            $lang = $item["lang"];

            $arrLangCodes[$lang] = $lang;
        }



        return($arrLangCodes);
    }

    public function getID()
    {
        return($this->id);
    }

    /**
     * get slide title
     */
    public function getTitle()
    {
        return($this->getParam("title", "Slide"));
    }

    public function temPostTypes()
    {
        return($this->slider->arrParams['post_types']);
    }

    public function getOrder()
    {
        $this->validateInited();

        return($this->slideOrder);
    }

    public function getLayers()
    {
        $this->validateInited();

        return($this->arrLayers);
    }

    public function getLayersForExport($useDummy = false)
    {
        $this->validateInited();

        $arrLayersNew = array();

        foreach ($this->arrLayers_export as $key => $layer) {
            $imageUrl = UniteFunctionsRev::getVal($layer, "image_url");


            if (!empty($imageUrl)) {
                //                $layer["image_url"] = UniteFunctionsWPRev::getImagePathFromURL($layer["image_url"]);
                $layer["image_url"] = 'uploads/' . basename($layer["image_url"]);
            }


            $arrLayersNew[] = $layer;
        }


        return($arrLayersNew);
    }

    public function getParamsForExport()
    {
        $arrParams = $this->getParams();

        $urlImage = UniteFunctionsRev::getVal($arrParams, "image");

        if (!empty($urlImage)) {
            $arrParams["image"] = UniteFunctionsWPRev::getImagePathFromURL($urlImage);
        }



        return($arrParams);
    }

    public function getLayersNormalizeText()
    {
        $arrLayersNew = array();

        foreach ($this->arrLayers as $key => $layer) {
            $text = $layer["text"];

            $text = addslashes($text);

            $layer["text"] = $text;

            $arrLayersNew[] = $layer;
        }



        return($arrLayersNew);
    }

    /**
     * get real slides number, from posts, social streams ect.
     */
    public function getNumRealSlides($publishedOnly = false, $type = 'post')
    {
        $numSlides = count($this->arrSlides);

        switch ($type) {
            case 'post':
                $this->getSlidesFromPosts($publishedOnly);
                $numSlides = count($this->arrSlides);
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

    public function getParams()
    {
        $this->validateInited();

        return($this->params);
    }

    /**
     * get slide settings
     * @since: 5.0
     */
    public function getSettings()
    {
        $this->validateInited();
        return($this->settings);
    }

    public function getParam($name, $default = null)
    {
        if ($default === null) {
            if (!array_key_exists($name, $this->params)) {
                UniteFunctionsRev::throwError("The param <b>$name</b> not found in slide params.");
            }

            $default = "";
        }



        return UniteFunctionsRev::getVal($this->params, $name, $default);
    }

    /**
     * set parameter
     * @since: 5.0
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    public function getImageFilename()
    {
        return($this->imageFilename);
    }

    public function getImageFilepath()
    {
        return($this->imageFilepath);
    }

    public function getImageUrl()
    {
        return($this->imageUrl);
    }

    public function getImageID()
    {
        return($this->imageID);
    }

    public function getThumbUrl()
    {
        $thumbUrl = $this->imageUrl;



        $size = GlobalsRevSlider::IMAGE_SIZE_MEDIUM;

        $filename = basename($thumbUrl);



        $filerealname = Tools::substr($filename, 0, strrpos($filename, '.'));

        $fileext = Tools::substr($filename, strrpos($filename, '.'), Tools::strlen($filename) - Tools::strlen($filerealname));



        $nthumbUrl = str_replace($filename, "{$filerealname}-{$size}x{$size}{$fileext}", $thumbUrl);



        if (!empty($this->imageThumb)) {
            $nthumbUrl = $thumbUrl = $this->imageThumb;
        }









        //$nthumbUrl = str_replace($filename,"{$filerealname}-{$size}x{$size}{$fileext}",$thumbUrl);

        return($nthumbUrl);
    }

    public function getSliderID()
    {
        return($this->sliderID);
    }

    private function getSliderParam($sliderID, $name, $default, $validate = null)
    {
        if (empty($this->slider)) {
            $this->slider = new RevSlider();

            $this->slider->initByID($sliderID);
        }



        $param = $this->slider->getParam($name, $default, $validate);



        return($param);
    }

    private function validateSliderExists($sliderID)
    {
        $slider = new RevSlider();

        $slider->initByID($sliderID);
    }

    private function validateInited()
    {
        if (empty($this->id)) {
            UniteFunctionsRev::throwError("The slide is not inited!!!");
        }
    }

    public function createSlide($sliderID, $obj = "", $static = false)
    {
        $imageID = null;

        if (is_array($obj)) {
            $urlImage = UniteFunctionsRev::getVal($obj, "url");
            $imageID = UniteFunctionsRev::getVal($obj, "id");
        } else {
            $urlImage = $obj;
        }

        //get max order
        $slider = new RevSlider();
        $slider->initByID($sliderID);
        $maxOrder = $slider->getMaxOrder();
        $order = $maxOrder + 1;

        $params = array();
        if (!empty($urlImage)) {
            $params["background_type"] = "image";
            $params["image"] = $urlImage;
            if (!empty($imageID)) {
                $params["image_id"] = $imageID;
            }
        } else { //create transparent slide
            $params["background_type"] = "trans";
        }

        $jsonParams = Tools::jsonEncode($params);


        $arrInsert = array("params" => $jsonParams,
            "slider_id" => $sliderID,
            "layers" => ""
        );

        if (!$static) {
            $arrInsert["slide_order"] = $order;
        }

        if (!$static) {
            $slideID = $this->db->insert(GlobalsRevSlider::$table_slides, $arrInsert);
        } else {
            $slideID = $this->db->insert(GlobalsRevSlider::$table_static_slides, $arrInsert);
        }

        return($slideID);
    }

    public function updateSlideImageFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "slider_id");

        $slider = new RevSlider();

        $slider->initByID($sliderID);



        $slideID = UniteFunctionsRev::getVal($data, "slide_id");

        $urlImage = UniteFunctionsRev::getVal($data, "url_image");

        UniteFunctionsRev::validateNotEmpty($urlImage);

        $imageID = UniteFunctionsRev::getVal($data, "image_id");

        $this->initByID($slideID);

        $arrUpdate = array();

        $arrUpdate["image"] = $urlImage;

        $arrUpdate["image_id"] = $imageID;

        $this->updateParamsInDB($arrUpdate);

        return($urlImage);
    }

    private function updateParamsInDB($arrUpdate = array())
    {
        $this->validateInited();

        $this->params = array_merge($this->params, $arrUpdate);

        $jsonParams = Tools::jsonEncode($this->params);



        $arrDBUpdate = array("params" => $jsonParams);



        $this->db->update(GlobalsRevSlider::$table_slides, $arrDBUpdate, array("id" => $this->id));
    }

    private function updateLayersInDB($arrLayers = null)
    {
        $this->validateInited();



        if ($arrLayers === null) {
            $arrLayers = $this->arrLayers;
        }



        $jsonLayers = Tools::jsonEncode($arrLayers);

        $arrDBUpdate = array("layers" => $jsonLayers);



        $this->db->update(GlobalsRevSlider::$table_slides, $arrDBUpdate, array("id" => $this->id));
    }

    public function updateParentSlideID($parentID)
    {
        $arrUpdate = array();

        $arrUpdate["parentid"] = $parentID;

        $this->updateParamsInDB($arrUpdate);
    }

    private function sortLayersByOrder($layer1, $layer2)
    {
        $layer1 = (array) $layer1;

        $layer2 = (array) $layer2;



        $order1 = UniteFunctionsRev::getVal($layer1, "order", 1);

        $order2 = UniteFunctionsRev::getVal($layer2, "order", 2);

        if ($order1 == $order2) {
            return(0);
        }



        return($order1 > $order2);
    }

    /**
     * 
     * go through the layers and fix small bugs if exists
     */
    private function normalizeLayers($arrLayers)
    {
        usort($arrLayers, array($this, "sortLayersByOrder"));

        $arrLayersNew = array();
        foreach ($arrLayers as $key => $layer) {
            $layer = (array) $layer;
            //set type
            $type = RevSliderFunctions::getVal($layer, "type", "text");
            $layer["type"] = $type;
            if (!is_object($layer["left"])) {
                $layer["left"] = (object) $layer["left"];
            }
            if (!is_object($layer["top"])) {
                $layer["top"] = (object) $layer["top"];
            }
            //normalize position:
            if (is_object($layer["left"])) {
                foreach ($layer["left"] as $key => $val) {
                    $layer["left"]->$key = round($val);
                }
            } else {
                $layer["left"] = round($layer["left"]);
            }
            if (is_object($layer["top"])) {
                foreach ($layer["top"] as $key => $val) {
                    $layer["top"]->$key = round($val);
                }
            } else {
                $layer["top"] = round($layer["top"]);
            }

            //unset order
            unset($layer["order"]);

            //modify text
            $layer["text"] = stripcslashes($layer["text"]);

            $arrLayersNew[] = $layer;
        }

        return($arrLayersNew);
    }

    private function normalizeParams($params)
    {
        $urlImage = UniteFunctionsRev::getVal($params, "image_url");


        $params["image_id"] = UniteFunctionsRev::getVal($params, "image_id");



        $params["image"] = $urlImage;

        unset($params["image_url"]);



        if (@RevsliderPrestashop::getIsset($params["video_description"])) {
            $params["video_description"] = UniteFunctionsRev::normalizeTextareaContent($params["video_description"]);
        }



        return($params);
    }

    public function updateSlideFromData($data)
    {
        $slideID = RevSliderFunctions::getVal($data, "slideid");
        $this->initByID($slideID);

        //treat params
        $params = RevSliderFunctions::getVal($data, "params");
        $params = $this->normalizeParams($params);

        //preserve old data that not included in the given data
        $params = array_merge($this->params, $params);
        //treat layers
        $layers = RevSliderFunctions::getVal($data, "layers");

        if (gettype($layers) == "string") {
            $layersStrip = Tools::stripslashes($layers);
            $layersDecoded = Tools::jsonDecode($layersStrip);
            if (empty($layersDecoded)) {
                $layersDecoded = Tools::jsonDecode($layers);
            }

            $layers = RevSliderFunctions::convertStdClassToArray($layersDecoded);
        }

        if (empty($layers) || gettype($layers) != "array") {
            $layers = array();
        }


        $layers = $this->normalizeLayers($layers);

        $settings = RevSliderFunctions::getVal($data, "settings");

        $arrUpdate = array();
        $arrUpdate["layers"] = Tools::jsonEncode($layers);
        $arrUpdate["params"] = Tools::jsonEncode($params);
        $arrUpdate["settings"] = Tools::jsonEncode($settings);

        $this->db->update(RevSliderGlobals::$table_slides, $arrUpdate, array("id" => $this->id));



        // RevOperations::updateDynamicCaptions();
    }

    public function updateStaticSlideFromData($data)
    {
        $slideID = UniteFunctionsRev::getVal($data, "slideid");
        $this->initByStaticID($slideID);

        //treat layers
        $layers = UniteFunctionsRev::getVal($data, "layers");

        if (gettype($layers) == "string") {
            $layersStrip = Tools::stripslashes($layers);
            $layersDecoded = Tools::jsonDecode($layersStrip);
            if (empty($layersDecoded)) {
                $layersDecoded = Tools::jsonDecode($layers);
            }

            $layers = UniteFunctionsRev::convertStdClassToArray($layersDecoded);
        }

        if (empty($layers) || gettype($layers) != "array") {
            $layers = array();
        }

        $layers = $this->normalizeLayers($layers);

        $arrUpdate = array();
        $arrUpdate["layers"] = Tools::jsonEncode($layers);

        $this->db->update(GlobalsRevSlider::$table_static_slides, $arrUpdate, array("id" => $this->id));
    }

    public function deleteSlide()
    {
        $this->validateInited();



        $this->db->delete(GlobalsRevSlider::$table_slides, "id='" . $this->id . "'");
    }

    public function deleteChildren()
    {
        $this->validateInited();

        $arrChildren = $this->getArrChildren();

        foreach ($arrChildren as $child) {
            $child->deleteSlide();
        }
    }

    public function deleteSlideFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "sliderID");

        $slider = new RevSlider();

        $slider->initByID($sliderID);


        //delete slide
        $slideID = UniteFunctionsRev::getVal($data, "slideID");

        $this->initByID($slideID);

        $this->deleteChildren();

        $this->deleteSlide();




        RevOperations::updateDynamicCaptions();
    }

    public function setParams($params)
    {
        $params = $this->normalizeParams($params);

        $this->params = $params;
    }

    public function setLayers($layers)
    {
        $layers = $this->normalizeLayers($layers);
        $this->arrLayers = $layers;
    }

    public function toggleSlideStatFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "slider_id");

        $slider = new RevSlider();

        $slider->initByID($sliderID);



        $slideID = UniteFunctionsRev::getVal($data, "slide_id");



//        if ($slider->isSlidesFromPosts()) {
//
//            $postData = UniteFunctionsWPRev::getPost($slideID);
//
//
//
//            $oldState = $postData["post_status"];
//
//            $newState = ($oldState == UniteFunctionsWPRev::STATE_PUBLISHED) ? UniteFunctionsWPRev::STATE_DRAFT : UniteFunctionsWPRev::STATE_PUBLISHED;
//
//
//
//            //update the state in wp
//
//            UniteFunctionsWPRev::updatePostState($slideID, $newState);
//
//
//
//            //return state:
//
//            $newState = ($newState == UniteFunctionsWPRev::STATE_PUBLISHED) ? "published" : "unpublished";
//        } else {

        $this->initByID($slideID);



        $state = $this->getParam("state", "published");

        $newState = ($state == "published") ? "unpublished" : "published";



        $arrUpdate = array();

        $arrUpdate["state"] = $newState;



        $this->updateParamsInDB($arrUpdate);
//        }



        return($newState);
    }

    private function updateLangFromData($data)
    {
        $slideID = UniteFunctionsRev::getVal($data, "slideid");

        $this->initByID($slideID);



        $lang = UniteFunctionsRev::getVal($data, "lang");



        $arrUpdate = array();

        $arrUpdate["lang"] = $lang;

        $this->updateParamsInDB($arrUpdate);



        $response = array();

        $response["url_icon"] = UniteWpmlRev::getFlagUrl($lang);

        $response["title"] = UniteWpmlRev::getLangTitle($lang);

        $response["operation"] = "update";



        return($response);
    }

    private function addLangFromData($data)
    {
        $sliderID = UniteFunctionsRev::getVal($data, "sliderid");

        $slideID = UniteFunctionsRev::getVal($data, "slideid");

        $lang = UniteFunctionsRev::getVal($data, "lang");



        //duplicate slide

        $slider = new RevSlider();

        $slider->initByID($sliderID);

        $newSlideID = $slider->duplicateSlide($slideID);



        //update new slide

        $this->initByID($newSlideID);



        $arrUpdate = array();

        $arrUpdate["lang"] = $lang;

        $arrUpdate["parentid"] = $slideID;

        $this->updateParamsInDB($arrUpdate);



        $urlIcon = UniteWpmlRev::getFlagUrl($lang);

        $title = UniteWpmlRev::getLangTitle($lang);



        $newSlide = new RevSlide();

        $newSlide->initByID($slideID);

        $arrLangCodes = $newSlide->getArrChildLangCodes();

        $isAll = UniteWpmlRev::isAllLangsInArray($arrLangCodes);



        $html = "<li>

								<img id=\"icon_lang_" . $newSlideID . "\" class=\"icon_slide_lang\" src=\"" . $urlIcon . "\" title=\"" . $title . "\" data-slideid=\"" . $newSlideID . "\" data-lang=\"" . $lang . "\">

								<div class=\"icon_lang_loader loader_round\" style=\"display:none\"></div>								

							</li>";



        $response = array();

        $response["operation"] = "add";

        $response["isAll"] = $isAll;

        $response["html"] = $html;



        return($response);
    }

    private function deleteSlideFromLangData($data)
    {
        $slideID = UniteFunctionsRev::getVal($data, "slideid");

        $this->initByID($slideID);

        $this->deleteSlide();



        $response = array();

        $response["operation"] = "delete";

        return($response);
    }

    public function doSlideLangOperation($data)
    {
        $operation = UniteFunctionsRev::getVal($data, "operation");

        switch ($operation) {

            case "add":

                $response = $this->addLangFromData($data);

                break;

            case "delete":

                $response = $this->deleteSlideFromLangData($data);

                break;

            case "update":

            default:

                $response = $this->updateLangFromData($data);

                break;
        }



        return($response);
    }

    public function getUrlImageThumb()
    {



        //get image url by thumb

        if (!empty($this->imageID)) {
            $urlImage = UniteFunctionsWPRev::getUrlAttachmentImage($this->imageID, UniteFunctionsWPRev::THUMB_MEDIUM);
        } else {

            //get from cache

            if (!empty($this->imageFilepath)) {
                $urlImage = UniteBaseClassRev::getImageUrl($this->imageFilepath, 200, 100, true);
            } else {
                $urlImage = $this->imageUrl;
            }
        }



        if (empty($urlImage)) {
            $urlImage = $this->imageUrl;
        }



        return($urlImage);
    }

    public function getImageAttributes($slider_type)
    {
        $params = $this->params;

        $bgType = UniteBaseClassRev::getVar($params, "background_type", "transparent");
        $bgColor = UniteBaseClassRev::getVar($params, "slide_bg_color", "transparent");

        $bgFit = UniteBaseClassRev::getVar($params, "bg_fit", "cover");
        $bgFitX = (int) (UniteBaseClassRev::getVar($params, "bg_fit_x", "100"));
        $bgFitY = (int) (UniteBaseClassRev::getVar($params, "bg_fit_y", "100"));

        $bgPosition = UniteBaseClassRev::getVar($params, "bg_position", "center top");
        $bgPositionX = (int) (UniteBaseClassRev::getVar($params, "bg_position_x", "0"));
        $bgPositionY = (int) (UniteBaseClassRev::getVar($params, "bg_position_y", "0"));

        $bgRepeat = UniteBaseClassRev::getVar($params, "bg_repeat", "no-repeat");

        $bgStyle = ' ';
        if ($bgFit == 'percentage') {
            $bgStyle .= "background-size: " . $bgFitX . '% ' . $bgFitY . '%;';
        } else {
            $bgStyle .= "background-size: " . $bgFit . ";";
        }
        if ($bgPosition == 'percentage') {
            $bgStyle .= "background-position: " . $bgPositionX . '% ' . $bgPositionY . '%;';
        } else {
            $bgStyle .= "background-position: " . $bgPosition . ";";
        }
        $bgStyle .= "background-repeat: " . $bgRepeat . ";";

        $thumb = '';
        $thumb_on = UniteBaseClassRev::getVar($params, "thumb_for_admin", 'off');

        switch ($slider_type) {
            case 'gallery':
                $imageID = UniteBaseClassRev::getVar($params, "image_id");
                if (empty($imageID)) {
                    $thumb = UniteBaseClassRev::getVar($params, "image");

                    $imgID = UniteBaseClassRev::getImageIdByUrl($thumb);
                    if ($imgID !== false) {
                        $thumb = UniteFunctionsWPRev::getUrlAttachmentImage($imgID, UniteFunctionsWPRev::THUMB_MEDIUM);
                    }
                } else {
                    $thumb = UniteFunctionsWPRev::getUrlAttachmentImage($imageID, UniteFunctionsWPRev::THUMB_MEDIUM);
                }

                if ($thumb_on == 'on') {
                    $thumb = UniteBaseClassRev::getVar($params, "slide_thumb", '');
                }

                break;
            case 'posts':
                $thumb = get_url() . '/views/img/images/sources/post.png';
                $bgStyle = 'background-size: cover;';
                break;
            case 'woocommerce':
                $thumb = get_url() . '/views/img/images/sources/wc.png';
                $bgStyle = 'background-size: cover;';
                break;
            case 'facebook':
                $thumb = get_url() . '/views/img/images/sources/fb.png';
                $bgStyle = 'background-size: cover;';
                break;
            case 'twitter':
                $thumb = get_url() . '/views/img/images/sources/tw.png';
                $bgStyle = 'background-size: cover;';
                break;
            case 'instagram':
                $thumb = get_url() . '/views/img/images/sources/ig.png';
                $bgStyle = 'background-size: cover;';
                break;
            case 'flickr':
                $thumb = get_url() . '/views/img/images/sources/fr.png';
                $bgStyle = 'background-size: cover;';
                break;
            case 'youtube':
                $thumb = get_url() . '/views/img/images/sources/yt.png';
                $bgStyle = 'background-size: cover;';
                break;
            case 'vimeo':
                $thumb = get_url() . '/views/img/images/sources/vm.png';
                $bgStyle = 'background-size: cover;';
                break;
        }


        if ($thumb == '') {
            $thumb = UniteBaseClassRev::getVar($params, "image");
        }

        $bg_fullstyle = '';
        $bg_extraClass = '';
        $data_urlImageForView = '';

        //if($bgType=="image" || $bgType=="streamvimeo" || $bgType=="streamyoutube" || $bgType=="streaminstagram" || $bgType=="html5") {
        $data_urlImageForView = $thumb;
        $bg_fullstyle = $bgStyle;
        //}

        if ($bgType == "solid") {
            if ($thumb_on == 'off') {
                $bg_fullstyle = 'background-color:' . $bgColor . ';';
                $data_urlImageForView = '';
            } else {
                $bg_fullstyle = 'background-size: cover;';
            }
        }

        if ($bgType == "trans" || $bgType == "transparent") {
            $bg_extraClass = 'mini-transparent';
            $bg_fullstyle = 'background-size: inherit; background-repeat: repeat;';
        }

        return array(
            'url' => $data_urlImageForView,
            'class' => $bg_extraClass,
            'style' => $bg_fullstyle
        );
    }

    public function replaceImageUrls($urlFrom, $urlTo)
    {
        $this->validateInited();



        $urlImage = UniteFunctionsRev::getVal($this->params, "image");



        if (strpos($urlImage, $urlFrom) !== false) {
            $imageNew = str_replace($urlFrom, $urlTo, $urlImage);

            $this->params["image"] = $imageNew;

            $this->updateParamsInDB();
        }





        // update image url in layers

        $isUpdated = false;

        foreach ($this->arrLayers as $key => $layer) {
            $type = UniteFunctionsRev::getVal($layer, "type");

            if ($type == "image") {
                $urlImage = UniteFunctionsRev::getVal($layer, "image_url");

                if (strpos($urlImage, $urlFrom) !== false) {
                    $newUrlImage = str_replace($urlFrom, $urlTo, $urlImage);

                    $this->arrLayers[$key]["image_url"] = $newUrlImage;

                    $isUpdated = true;
                }
            }
        }



        if ($isUpdated == true) {
            $this->updateLayersInDB();
        }
    }

    /**
     * get all used fonts in the current Slide
     * @since: 5.1.0
     */
    public function getUsedFonts($full = false)
    {
        $this->validateInited();

        $op = new RevSliderOperations();
        $fonts = array();
        $all_fonts = $op->getArrFontFamilys();

        if (!empty($this->arrLayers)) {
            foreach ($this->arrLayers as $key => $layer) {
                $def = (array) RevSliderFunctions::getVal($layer, 'deformation', array());
                $font = RevSliderFunctions::getVal($def, 'font-family', '');
                $static = (array) RevSliderFunctions::getVal($layer, 'static_styles', array());

                foreach ($all_fonts as $f) {
                    if (Tools::strtolower(str_replace(array('"', "'", ' '), '', $f['label'])) == Tools::strtolower(str_replace(array('"', "'", ' '), '', $font)) && $f['type'] == 'googlefont') {
                        if (!@RevsliderPrestashop::getIsset($fonts[$f['label']])) {
                            $fonts[$f['label']] = array('variants' => array(), 'subsets' => array());
                        }
                        if ($full) { //if full, add all.
                            //switch the variants around here!
                            $mv = array();
                            if (!empty($f['variants'])) {
                                foreach ($f['variants'] as $fvk => $fvv) {
                                    $mv[$fvv] = $fvv;
                                }
                            }
                            $fonts[$f['label']] = array('variants' => $mv, 'subsets' => $f['subsets']);
                        } else { //Otherwise add only current font-weight plus italic or not
                            $fw = (array) RevSliderFunctions::getVal($static, 'font-weight', '400');
                            $fs = RevSliderFunctions::getVal($def, 'font-style', '');

                            if ($fs == 'italic') {
                                foreach ($fw as $mf => $w) {
                                    //we check if italic is available at all for the font!
                                    if ($w == '400') {
                                        if (array_search('italic', $f['variants']) !== false) {
                                            $fw[$mf] = 'italic';
                                        }
                                    } else {
                                        if (array_search($w . 'italic', $f['variants']) !== false) {
                                            $fw[$mf] = $w . 'italic';
                                        }
                                    }
                                }
                            }

                            foreach ($fw as $mf => $w) {
                                $fonts[$f['label']]['variants'][$w] = true;
                            }

                            $fonts[$f['label']]['subsets'] = $f['subsets']; //subsets always get added, needs to be done then by the Slider Settings
                        }
                        break;
                    }
                }
            }
        }

        return $fonts;
    }

    public function changeTransition($transition)
    {
        $this->validateInited();



        $this->params["slide_transition"] = $transition;

        $this->updateParamsInDB();
    }

    public function changeTransitionDuration($transitionDuration)
    {
        $this->validateInited();



        $this->params["transition_duration"] = $transitionDuration;

        $this->updateParamsInDB();
    }

    public function isStaticSlide()
    {
        return $this->static_slide;
    }

    /**
     * Returns all layer attributes that can have more than one setting due to desktop, tablet, mobile sizes
     * @since: 5.0
     */
    public static function translateIntoSizes()
    {
        return array(
            'align_hor',
            'align_vert',
            'top',
            'left',
            'font-size',
            'line-height',
            'font-weight',
            'color',
            'max_width',
            'max_height',
            'whitespace',
            'video_height',
            'video_width',
            'scaleX',
            'scaleY'
        );
    }

    /**
     * Translates all values that need more than one setting
     * @since: 5.0
     */
    public function translateLayerSizes($layers)
    {
        $translation = self::translateIntoSizes();

        if (!empty($layers)) {
            foreach ($layers as $l => $layer) {
                foreach ($translation as $trans) {
                    if (@RevsliderPrestashop::getIsset($layers[$l][$trans])) {
                        if (!is_array($layers[$l][$trans])) {
                            $layers[$l][$trans] = array('desktop' => $layers[$l][$trans]);
                        }
                    }
                }
            }
        }

        return $layers;
    }

    /**
     * Check if Slide Exists with given ID
     * @since: 5.0
     */
    public static function isSlideByID($slideid)
    {
        $db = new RevSliderDB();
        try {
            if (strpos($slideid, 'static_') !== false) {
                $sliderID = str_replace('static_', '', $slideid);

                RevSliderFunctions::validateNumeric($sliderID, "Slider ID");

                $sliderID = $db->escape($sliderID);
                $record = $db->fetch(RevSliderGlobals::$table_static_slides, "slider_id=$sliderID");

                if (empty($record)) {
                    return false;
                }

                return true;
            } else {
                $slideid = $db->escape($slideid);
                $record = $db->fetchSingle(RevSliderGlobals::$table_slides, "id=$slideid");

                if (empty($record)) {
                    return false;
                }

                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * set layers from client, do not normalize as this results in loosing the order
     * @since: 5.0
     */
    public function setLayersRaw($layers)
    {
        $this->arrLayers = $layers;
    }

    /**
     * save layers to the database
     * @since: 5.0
     */
    public function saveLayers()
    {
        $this->validateInited();
        $table = ($this->static_slide) ? RevSliderGlobals::$table_static_slides : RevSliderGlobals::$table_slides;
        $this->db->update($table, array('layers' => Tools::jsonEncode($this->arrLayers)), array('id' => $this->id));
    }

    /**
     * save params to the database
     * @since: 5.0
     */
    public function saveParams()
    {
        $this->validateInited();
        $table = ($this->static_slide) ? RevSliderGlobals::$table_static_slides : RevSliderGlobals::$table_slides;
        $this->db->update($table, array('params' => Tools::jsonEncode($this->params)), array('id' => $this->id));
    }

    /**
     * update the title of a Slide by Slide ID
     * @since: 5.0
     * */
    public function updateTitleByID($data)
    {
        if (!isset($data['slideID']) || !isset($data['slideTitle'])) {
            return false;
        }

        $this->initByID($data['slideID']);

        $arrUpdate = array();
        $arrUpdate['title'] = $data['slideTitle'];

        $this->updateParamsInDB($arrUpdate);
    }

    /**
     * get layers in json format
     * since: 5.0
     */
    public function getLayerIDByUniqueId($unique_id)
    {
        $this->validateInited();

        foreach ($this->arrLayers as $l) {

            $uid = RevSliderFunctions::getVal($l, 'unique_id');
            if ($uid == $unique_id) {
                return RevSliderFunctions::getVal($l, 'attrID');
            }
        }

        return '';
    }
}

// @codingStandardsIgnoreStart
class RevSliderSlide extends RevSlide
{
    // @codingStandardsIgnoreEnd
}
