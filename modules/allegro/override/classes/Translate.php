<?php

class Translate extends TranslateCore
{
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