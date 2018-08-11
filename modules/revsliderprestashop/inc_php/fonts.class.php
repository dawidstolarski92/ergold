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

if (!class_exists('ThemePunchFonts')) {

    class ThemePunchFonts
    {

        public function addNewFont($new_font)
        {
            if (!@RevsliderPrestashop::getIsset($new_font['url']) || Tools::strlen($new_font['url']) < 3) {
                return __('Wrong parameter received', REVSLIDER_TEXTDOMAIN);
            }
            if (!@RevsliderPrestashop::getIsset($new_font['handle']) || Tools::strlen($new_font['handle']) < 3) {
                return __('Wrong handle received', REVSLIDER_TEXTDOMAIN);
            }

            $fonts = unserialize(sdsconfig::getval('tp-google-fonts'));

            if (!empty($fonts)) {
                foreach ($fonts as $font) {
                    if ($font['handle'] == $new_font['handle']) {
                        return __('Font with handle already exist, choose a different handle', REVSLIDER_TEXTDOMAIN);
                    }
                }
            }

            $new = array('url' => $new_font['url'], 'handle' => $new_font['handle']);

            $fonts[] = $new;

            $do = sdsconfig::setval('tp-google-fonts', $fonts);
            if ($do) {
                return true;
            }
        }

        public function editFontByHandle($edit_font)
        {
            if (!@RevsliderPrestashop::getIsset($edit_font['handle']) || Tools::strlen($edit_font['handle']) < 3) {
                return __('Wrong Handle received', REVSLIDER_TEXTDOMAIN);
            }
            if (!@RevsliderPrestashop::getIsset($edit_font['url']) || Tools::strlen($edit_font['url']) < 3) {
                return __('Wrong Params received', REVSLIDER_TEXTDOMAIN);
            }

            $fonts = $this->getAllFonts();

            if (!empty($fonts)) {
                foreach ($fonts as $key => $font) {
                    if ($font['handle'] == $edit_font['handle']) {
                        $fonts[$key]['handle'] = $edit_font['handle'];
                        $fonts[$key]['url'] = $edit_font['url'];

                        $do = sdsconfig::setval('tp-google-fonts', $fonts);

                        return true;
                    }
                }
            }

            return false;
        }

        public function removeFontByHandle($handle)
        {
            $fonts = $this->getAllFonts();

            if (!empty($fonts)) {
                foreach ($fonts as $key => $font) {
                    if ($font['handle'] == $handle) {
                        unset($fonts[$key]);
                        $do = sdsconfig::setval('tp-google-fonts', $fonts);
                        return true;
                    }
                }
            }

            return __('Font not found! Wrong handle given.', REVSLIDER_TEXTDOMAIN);
        }

        public function getAllFonts()
        {
            $fonts = unserialize(sdsconfig::getval('tp-google-fonts'));
            return $fonts;
        }

        public function getAllFontsHandle()
        {
            $fonts = array();
            $font = unserialize(sdsconfig::getval('tp-google-fonts'));
            if (!empty($font)) {
                foreach ($font as $f) {
                    $fonts[] = $f['handle'];
                }
            }
            return $fonts;
        }

        public function registerFonts()
        {
            $fonts = $this->getAllFonts();
            if (!empty($fonts)) {
                $http = (is_ssl()) ? 'https' : 'http';
                foreach ($fonts as $font) {
                    if ($font !== '') {
                        $font_url = $http . '://fonts.googleapis.com/css?family=' . $font['url'];
                        Context::getcontext()->controller->addCSS($font_url);
                    }
                }
            }
        }

        public static function propagateDefaultFonts()
        {
            $default = array(
                array('url' => 'Open+Sans:300,400,600,700,800', 'handle' => 'open-sans'),
                array('url' => 'Raleway:100,200,300,400,500,600,700,800,900', 'handle' => 'raleway'),
                array('url' => 'Droid+Serif:400,700', 'handle' => 'droid-serif')
            );
            $fonts = unserialize(sdsconfig::getval('tp-google-fonts'));
            if (!empty($fonts)) {
                foreach ($default as $d_key => $d_font) {
                    $found = false;
                    foreach ($fonts as $font) {
                        if ($font['handle'] == $d_font['handle']) {
                            $found = true;
                            break;
                        }
                    }
                    if ($found == false) {
                        $fonts[] = $default[$d_key];
                    }
                }
                sdsconfig::setval('tp-google-fonts', $fonts);
            } else {
                sdsconfig::setval('tp-google-fonts', $default);
            }
        }
    }

}
