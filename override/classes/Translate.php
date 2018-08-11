<?php
class Translate extends TranslateCore
{
    /*
    * module: allegro
    * date: 2017-08-24 10:12:46
    * version: 4.1.0.5
    */
    public static function getModuleTranslation($module, $string, $source, $sprintf = null, $js = false)
    {
        $str = parent::getModuleTranslation($module, $string, $source, $sprintf, $js);
       if (($source == 'AdminAllegroProductcontroller' ||
            $source == 'AdminAllegroFieldcontroller' ||
            $source == 'AdminAllegroThemecontroller' ||
            $source == 'AdminAllegroAccountcontroller' ||
            $source == 'AdminAllegroAuctioncontroller' ||
            $source == 'AdminAllegroSynccontroller' ||
            $source == 'AdminAllegroPreferencescontroller') 
        && $str == htmlentities($string)) {
            return parent::getModuleTranslation($module, $string, 'ParentAllegrocontroller', $sprintf, $js);
       } else {
            return $str;
       }
    }
}