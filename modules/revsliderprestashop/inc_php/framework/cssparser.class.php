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

class UniteCssParserRev
{

    private $cssContent;

    public function __construct()
    {
        
    }

    public function initContent($cssContent)
    {
        $this->cssContent = $cssContent;
    }

    public function getArrClasses($startText = "", $endText = "")
    {
        $content = $this->cssContent;


        if (!empty($startText)) {
            $posStart = strpos($content, $startText);

            if ($posStart !== false) {
                $content = Tools::substr($content, $posStart, Tools::strlen($content) - $posStart);
            }
        }



        //trim from bottom

        if (!empty($endText)) {
            $posEnd = strpos($content, $endText);

            if ($posEnd !== false) {
                $content = Tools::substr($content, 0, $posEnd);
            }
        }



        //get styles

        $lines = explode("\n", $content);

        $arrClasses = array();

        foreach ($lines as $key => $line) {
            $line = trim($line);



            if (strpos($line, "{") === false) {
                continue;
            }



            //skip unnessasary links

            if (strpos($line, ".caption a") !== false) {
                continue;
            }



            if (strpos($line, ".tp-caption a") !== false) {
                continue;
            }



            //get style out of the line

            $class = str_replace("{", "", $line);

            $class = trim($class);



            //skip captions like this: .tp-caption.imageclass img

            if (strpos($class, " ") !== false) {
                continue;
            }



            //skip captions like this: .tp-caption.imageclass:hover, :before, :after

            if (strpos($class, ":") !== false) {
                continue;
            }



            $class = str_replace(".caption.", ".", $class);

            $class = str_replace(".tp-caption.", ".", $class);



            $class = str_replace(".", "", $class);

            $class = trim($class);

            $arrWords = explode(" ", $class);

            $class = $arrWords[count($arrWords) - 1];

            $class = trim($class);



            $arrClasses[] = $class;
        }



        sort($arrClasses);



        return($arrClasses);
    }

    public static function parseCssToArray($css)
    {
        while (strpos($css, '/*') !== false) {
            if (strpos($css, '*/') === false) {
                return false;
            }

            $start = strpos($css, '/*');

            $end = strpos($css, '*/') + 2;

            $css = str_replace(Tools::substr($css, $start, $end - $start), '', $css);
        }



        preg_match_all('/(?ims)([a-z0-9\s\.\:#_\-@]+)\{([^\}]*)\}/', $css, $arr);



        $result = array();

        foreach ($arr[0] as $i => $x) {
            $selector = trim($arr[1][$i]);

            if (strpos($selector, '{') !== false || strpos($selector, '}') !== false) {
                return false;
            }

            $rules = explode(';', trim($arr[2][$i]));

            $result[$selector] = array();

            foreach ($rules as $strRule) {
                if (!empty($strRule)) {
                    $rule = explode(":", $strRule);

                    if (strpos($rule[0], '{') !== false || strpos($rule[0], '}') !== false || strpos($rule[1], '{') !== false || strpos($rule[1], '}') !== false) {
                        return false;
                    }



                    //put back everything but not $rule[0];

                    $key = trim($rule[0]);

                    unset($rule[0]);

                    $values = implode(':', $rule);



                    $result[$selector][trim($key)] = trim(str_replace("'", '"', $values));
                }
            }
        }

        return($result);
    }

    public static function parseDbArrayToCss($cssArray, $nl = "\n\r")
    {
        $css = '';
        $deformations = self::getDeformationCssTags();

        $transparency = array(
            'color' => 'color-transparency',
            'background-color' => 'background-transparency',
            'border-color' => 'border-transparency'
        );

        $check_parameters = array(
            'border-width' => 'px',
            'border-radius' => 'px',
            'padding' => 'px',
            'font-size' => 'px',
            'line-height' => 'px'
        );

        foreach ($cssArray as $id => $attr) {
            $stripped = '';
            if (strpos($attr['handle'], '.tp-caption') !== false) {
                $stripped = trim(str_replace('.tp-caption', '', $attr['handle']));
            }

            $attr['advanced'] = Tools::jsonDecode($attr['advanced'], true);

            $styles = Tools::jsonDecode(str_replace("'", '"', $attr['params']), true);
            $styles_adv = $attr['advanced']['idle'];

            $css.= $attr['handle'];
            if (!empty($stripped)) {
                $css.= ', ' . $stripped;
            }
            $css.= " {" . $nl;
            if (is_array($styles) || is_array($styles_adv)) {
                if (is_array($styles)) {
                    foreach ($styles as $name => $style) {
                        if (in_array($name, $deformations)) {
                            continue;
                        }

                        if (!is_array($name) && @RevsliderPrestashop::getIsset($transparency[$name])) { //the style can have transparency!
                            if (@RevsliderPrestashop::getIsset($styles[$transparency[$name]]) && $style !== 'transparent') {
                                $style = RevSliderFunctions::hex2rgba($style, $styles[$transparency[$name]] * 100);
                            }
                        }
                        if (!is_array($name) && @RevsliderPrestashop::getIsset($check_parameters[$name])) {
                            $style = RevSliderFunctions::addMissingVal($style, $check_parameters[$name]);
                        }


                        if (is_array($style)) {
                            $style = implode(' ', $style);
                        }
                        $css.= $name . ':' . $style . ";" . $nl;
                    }
                }
                if (is_array($styles_adv)) {
                    foreach ($styles_adv as $name => $style) {
                        if (in_array($name, $deformations)) {
                            continue;
                        }
                        if (is_array($style)) {
                            $style = implode(' ', $style);
                        }
                        $css.= $name . ':' . $style . ";" . $nl;
                    }
                }
            }
            $css.= "}" . $nl . $nl;

            //add hover
            $setting = Tools::jsonDecode($attr['settings'], true);
            if (@RevsliderPrestashop::getIsset($setting['hover']) && $setting['hover'] == 'true') {
                $hover = Tools::jsonDecode(str_replace("'", '"', $attr['hover']), true);
                $hover_adv = $attr['advanced']['hover'];

                if (is_array($hover) || is_array($hover_adv)) {
                    $css.= $attr['handle'] . ":hover";
                    if (!empty($stripped)) {
                        $css.= ', ' . $stripped . ':hover';
                    }
                    $css.= " {" . $nl;
                    if (is_array($hover)) {
                        foreach ($hover as $name => $style) {
                            if (in_array($name, $deformations)) {
                                continue;
                            }

                            if (!is_array($name) && @RevsliderPrestashop::getIsset($transparency[$name])) { //the style can have transparency!
                                if (@RevsliderPrestashop::getIsset($hover[$transparency[$name]]) && $style !== 'transparent') {
                                    $style = RevSliderFunctions::hex2rgba($style, $hover[$transparency[$name]] * 100);
                                }
                            }
                            if (!is_array($name) && @RevsliderPrestashop::getIsset($check_parameters[$name])) {
                                $style = RevSliderFunctions::addMissingVal($style, $check_parameters[$name]);
                            }

                            if (is_array($style)) {
                                $style = implode(' ', $style);
                            }
                            $css.= $name . ':' . $style . ";" . $nl;
                        }
                    }
                    if (is_array($hover_adv)) {
                        foreach ($hover_adv as $name => $style) {
                            if (in_array($name, $deformations)) {
                                continue;
                            }
                            if (is_array($style)) {
                                $style = implode(' ', $style);
                            }
                            $css.= $name . ':' . $style . ";" . $nl;
                        }
                    }
                    $css.= "}" . $nl . $nl;
                }
            }
        }
        return $css;
    }

    public static function parseArrayToCss($cssArray, $nl = "\n\r")
    {
        $css = '';



        if (!empty($cssArray)) {
            foreach ($cssArray as $id => $attr) {
                $styles = (array) $attr['params'];

                $css.= $attr['handle'] . " {" . $nl;

                if (is_array($styles) && !empty($styles)) {
                    foreach ($styles as $name => $style) {
                        if ($name == 'background-color' && strpos($style, 'rgba') !== false) { //rgb && rgba
                            $rgb = explode(',', str_replace('rgba', 'rgb', $style));

                            unset($rgb[count($rgb) - 1]);

                            $rgb = implode(',', $rgb) . ')';

                            $css.= $name . ':' . $rgb . ";" . $nl;
                        }
                        if (is_array($style)) {
                            $style = implode(' ', $style);
                        }
                        $css.= $name . ':' . $style . ";" . $nl;

//                                                    $css.= $name.':'.str_replace('"', '\'', $style).";".$nl;
                    }
                }

                $css.= "}" . $nl . $nl;



                //add hover

                $setting = (array) $attr['settings'];

                if (@$setting['hover'] == 'true') {
                    $hover = (array) $attr['hover'];

                    if (is_array($hover)) {
                        $css.= $attr['handle'] . ":hover {" . $nl;

                        foreach ($hover as $name => $style) {
                            if ($name == 'background-color' && strpos($style, 'rgba') !== false) { //rgb && rgba
                                $rgb = explode(',', str_replace('rgba', 'rgb', $style));

                                unset($rgb[count($rgb) - 1]);

                                $rgb = implode(',', $rgb) . ')';

                                $css.= $name . ':' . $rgb . ";" . $nl;
                            }

                            $css.= $name . ':' . $style . ";" . $nl;

                            //$css.= $name.':'.str_replace('"', '\'', $style).";".$nl;
                        }

                        $css.= "}" . $nl . $nl;
                    }
                }
            }
        }



        return $css;
    }

    public static function parseStaticArrayToCss($cssArray, $nl = "\n")
    {
        $css = '';

        foreach ($cssArray as $class => $styles) {
            $css.= $class . " {" . $nl;

            if (is_array($styles) && !empty($styles)) {
                foreach ($styles as $name => $style) {
                    $css.= $name . ':' . $style . ";" . $nl;
                }
            }

            $css.= "}" . $nl . $nl;
        }

        return $css;
    }

    public static function parseDbArrayToArray($cssArray, $handle = false)
    {
        if (!is_array($cssArray) || empty($cssArray)) {
            return false;
        }


        foreach ($cssArray as $key => $css) {
            if ($handle != false) {
                if ($cssArray[$key]['handle'] == '.tp-caption.' . $handle) {

//						$cssArray[$key]['params'] = Tools::jsonDecode(str_replace("'", '"', $css['params']));
//						$cssArray[$key]['params'] = Tools::jsonDecode(str_replace("\"", '\'', $css['params']));
                    $cssArray[$key]['params'] = Tools::jsonDecode($css['params']);

//						$cssArray[$key]['hover'] = Tools::jsonDecode(str_replace("'", '"', $css['hover']));
                    $cssArray[$key]['hover'] = Tools::jsonDecode($css['hover']);

                    $cssArray[$key]['settings'] = Tools::jsonDecode(str_replace("'", '"', $css['settings']));

                    return $cssArray[$key];
                } else {
                    unset($cssArray[$key]);
                }
            } else {
                $cssArray[$key]['params'] = Tools::jsonDecode(str_replace("'", '"', $css['params']));
//					$cssArray[$key]['params'] = Tools::jsonDecode(str_replace("\"", '\'', $css['params']));
//					$cssArray[$key]['params'] = Tools::jsonDecode($css['params']);

                $cssArray[$key]['hover'] = Tools::jsonDecode(str_replace("'", '"', $css['hover']));
//					$cssArray[$key]['hover'] = Tools::jsonDecode(str_replace("\"", '\'', $css['hover']));
//					$cssArray[$key]['hover'] = Tools::jsonDecode($css['hover']);

                $cssArray[$key]['settings'] = Tools::jsonDecode(str_replace("'", '"', $css['settings']));
            }
//                                echo "<pre>";
//                                print_r($cssArray[$key]);
//                                echo "</pre>";
        }



        return $cssArray;
    }

    public static function compressCss($buffer)
    {
        $buffer = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $buffer);

        $arr = array("\r\n", "\r", "\n", "\t", "  ", "    ", "    ");
        $rep = array("", "", "", "", " ", " ", " ");
        $buffer = str_replace($arr, $rep, $buffer);

        $buffer = preg_replace("/\s*([\{\}:,])\s*/", "$1", $buffer);

        $buffer = str_replace(';}', "}", $buffer);

        return $buffer;
    }

    /**
     * Defines the default CSS Classes, can be given a version number to order them accordingly
     * @since: 5.0
     * */
    public static function defaultCssClasses()
    {
        $default = array(
            '.tp-caption.medium_grey' => '4',
            '.tp-caption.small_text' => '4',
            '.tp-caption.medium_text' => '4',
            '.tp-caption.large_text' => '4',
            '.tp-caption.very_large_text' => '4',
            '.tp-caption.very_big_white' => '4',
            '.tp-caption.very_big_black' => '4',
            '.tp-caption.modern_medium_fat' => '4',
            '.tp-caption.modern_medium_fat_white' => '4',
            '.tp-caption.modern_medium_light' => '4',
            '.tp-caption.modern_big_bluebg' => '4',
            '.tp-caption.modern_big_redbg' => '4',
            '.tp-caption.modern_small_text_dark' => '4',
            '.tp-caption.boxshadow' => '4',
            '.tp-caption.black' => '4',
            '.tp-caption.noshadow' => '4',
            '.tp-caption.thinheadline_dark' => '4',
            '.tp-caption.thintext_dark' => '4',
            '.tp-caption.largeblackbg' => '4',
            '.tp-caption.largepinkbg' => '4',
            '.tp-caption.largewhitebg' => '4',
            '.tp-caption.largegreenbg' => '4',
            '.tp-caption.excerpt' => '4',
            '.tp-caption.large_bold_grey' => '4',
            '.tp-caption.medium_thin_grey' => '4',
            '.tp-caption.small_thin_grey' => '4',
            '.tp-caption.lightgrey_divider' => '4',
            '.tp-caption.large_bold_darkblue' => '4',
            '.tp-caption.medium_bg_darkblue' => '4',
            '.tp-caption.medium_bold_red' => '4',
            '.tp-caption.medium_light_red' => '4',
            '.tp-caption.medium_bg_red' => '4',
            '.tp-caption.medium_bold_orange' => '4',
            '.tp-caption.medium_bg_orange' => '4',
            '.tp-caption.grassfloor' => '4',
            '.tp-caption.large_bold_white' => '4',
            '.tp-caption.medium_light_white' => '4',
            '.tp-caption.mediumlarge_light_white' => '4',
            '.tp-caption.mediumlarge_light_white_center' => '4',
            '.tp-caption.medium_bg_asbestos' => '4',
            '.tp-caption.medium_light_black' => '4',
            '.tp-caption.large_bold_black' => '4',
            '.tp-caption.mediumlarge_light_darkblue' => '4',
            '.tp-caption.small_light_white' => '4',
            '.tp-caption.roundedimage' => '4',
            '.tp-caption.large_bg_black' => '4',
            '.tp-caption.mediumwhitebg' => '4',
            '.tp-caption.MarkerDisplay' => '5.0',
            '.tp-caption.Restaurant-Display' => '5.0',
            '.tp-caption.Restaurant-Cursive' => '5.0',
            '.tp-caption.Restaurant-ScrollDownText' => '5.0',
            '.tp-caption.Restaurant-Description' => '5.0',
            '.tp-caption.Restaurant-Price' => '5.0',
            '.tp-caption.Restaurant-Menuitem' => '5.0',
            '.tp-caption.Furniture-LogoText' => '5.0',
            '.tp-caption.Furniture-Plus' => '5.0',
            '.tp-caption.Furniture-Title' => '5.0',
            '.tp-caption.Furniture-Subtitle' => '5.0',
            '.tp-caption.Gym-Display' => '5.0',
            '.tp-caption.Gym-Subline' => '5.0',
            '.tp-caption.Gym-SmallText' => '5.0',
            '.tp-caption.Fashion-SmallText' => '5.0',
            '.tp-caption.Fashion-BigDisplay' => '5.0',
            '.tp-caption.Fashion-TextBlock' => '5.0',
            '.tp-caption.Sports-Display' => '5.0',
            '.tp-caption.Sports-DisplayFat' => '5.0',
            '.tp-caption.Sports-Subline' => '5.0',
            '.tp-caption.Instagram-Caption' => '5.0',
            '.tp-caption.News-Title' => '5.0',
            '.tp-caption.News-Subtitle' => '5.0',
            '.tp-caption.Photography-Display' => '5.0',
            '.tp-caption.Photography-Subline' => '5.0',
            '.tp-caption.Photography-ImageHover' => '5.0',
            '.tp-caption.Photography-Menuitem' => '5.0',
            '.tp-caption.Photography-Textblock' => '5.0',
            '.tp-caption.Photography-Subline-2' => '5.0',
            '.tp-caption.Photography-ImageHover2' => '5.0',
            '.tp-caption.WebProduct-Title' => '5.0',
            '.tp-caption.WebProduct-SubTitle' => '5.0',
            '.tp-caption.WebProduct-Content' => '5.0',
            '.tp-caption.WebProduct-Menuitem' => '5.0',
            '.tp-caption.WebProduct-Title-Light' => '5.0',
            '.tp-caption.WebProduct-SubTitle-Light' => '5.0',
            '.tp-caption.WebProduct-Content-Light' => '5.0',
            '.tp-caption.FatRounded' => '5.0',
            '.tp-caption.NotGeneric-Title' => '5.0',
            '.tp-caption.NotGeneric-SubTitle' => '5.0',
            '.tp-caption.NotGeneric-CallToAction' => '5.0',
            '.tp-caption.NotGeneric-Icon' => '5.0',
            '.tp-caption.NotGeneric-Menuitem' => '5.0',
            '.tp-caption.MarkerStyle' => '5.0',
            '.tp-caption.Gym-Menuitem' => '5.0',
            '.tp-caption.Newspaper-Button' => '5.0',
            '.tp-caption.Newspaper-Subtitle' => '5.0',
            '.tp-caption.Newspaper-Title' => '5.0',
            '.tp-caption.Newspaper-Title-Centered' => '5.0',
            '.tp-caption.Hero-Button' => '5.0',
            '.tp-caption.Video-Title' => '5.0',
            '.tp-caption.Video-SubTitle' => '5.0',
            '.tp-caption.NotGeneric-Button' => '5.0',
            '.tp-caption.NotGeneric-BigButton' => '5.0',
            '.tp-caption.WebProduct-Button' => '5.0',
            '.tp-caption.Restaurant-Button' => '5.0',
            '.tp-caption.Gym-Button' => '5.0',
            '.tp-caption.Gym-Button-Light' => '5.0',
            '.tp-caption.Sports-Button-Light' => '5.0',
            '.tp-caption.Sports-Button-Red' => '5.0',
            '.tp-caption.Photography-Button' => '5.0',
            '.tp-caption.Newspaper-Button-2' => '5.0'
        );

//			$default = apply_filters('revslider_mod_default_css_handles', $default);

        return $default;
    }

    /**
     * Defines the deformation CSS which is not directly usable as pure CSS
     * @since: 5.0
     * */
    public static function getDeformationCssTags()
    {
        return array(
            'x',
            'y',
            'z',
            'skewx',
            'skewy',
            'scalex',
            'scaley',
            'opacity',
            'xrotate',
            'yrotate',
            '2d_rotation',
            'layer_2d_origin_x',
            'layer_2d_origin_y',
            '2d_origin_x',
            '2d_origin_y',
            'pers',
            'color-transparency',
            'background-transparency',
            'border-transparency',
            'css_cursor',
            'speed',
            'easing',
            'corner_left',
            'corner_right',
            'parallax'
        );
    }

    public static function getCaptionsSorted()
    {
        $db = new RevSliderDB();
        $styles = $db->fetch(RevSliderGlobals::$table_css, '', 'handle ASC');

        $arr = array('5.0' => array(), 'Custom' => array(), '4' => array());

        foreach ($styles as $style) {
            $setting = Tools::jsonDecode($style['settings'], true);

            if (!@RevsliderPrestashop::getIsset($setting['type'])) {
                $setting['type'] = 'text';
            }

            if (array_key_exists('version', $setting) && @RevsliderPrestashop::getIsset($setting['version'])) {
                $arr[Tools::ucfirst($setting['version'])][] = array('label' => trim(str_replace('.tp-caption.', '', $style['handle'])), 'type' => $setting['type']);
            }
        }

        $sorted = array();
        foreach ($arr as $version => $class) {
            foreach ($class as $name) {
                $sorted[] = array('label' => $name['label'], 'version' => $version, 'type' => $name['type']);
            }
        }



        return $sorted;
    }
}
// @codingStandardsIgnoreStart
class RevSliderCssParser extends UniteCssParserRev
{
    // @codingStandardsIgnoreEnd
}
