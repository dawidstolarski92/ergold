<?php
class Pk_ThemeSettings_Common {

	public function _construct() {		

	}

	public function getModuleState($args)   {  // get options from database

		$context = Context::getContext();
        if ( (isset($args['home']) ) && ($args['home'] == true) ) {
            if ($context->controller->php_self != "index") {
                return false;
            }
        }

        $sql = 'SELECT value FROM `'._DB_PREFIX_.'pk_theme_settings_hooks` WHERE hook = "'.$args['hook'].'" AND module = "'.$args['name'].'" AND id_shop = '.(int)$context->shop->id.';';

        if (!$sett = Db::getInstance()->ExecuteS($sql)) 
            return true;

        return $sett[0]["value"];

    }

}