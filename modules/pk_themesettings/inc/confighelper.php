<?php

include_once(_PS_MODULE_DIR_.'pk_themesettings/pk_themesettings.php');

class configHelper extends Module {

	public function presets() {

        $presets_dir = _PS_MODULE_DIR_.'/pk_themesettings/presets';
        $presets_list = array();
        if ($handle = opendir($presets_dir)) {

            while (false !== ($file = readdir($handle))) {

                if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'json') {
                    if (filesize($presets_dir.'/'.$file) != false) {
                        $alias = str_replace('.json', '', $file);
                        $name = str_replace(array('_', '-'), ' ', $alias);
                        $presets_list[$alias] = $name;
                    }
                }
            }
            closedir($handle);
        }


		return $presets_list;
	}

	public function fontslist() {

		$fonts_list = array();
		$fonts = get_fonts();

		foreach ($fonts as $font_name) {
			$fonts_list[$font_name] = $font_name;
		}

		return $fonts_list;
	}

	public function font_weight() {
    	$list = array(
    		'100' => '100, Thin',
    		'200' => '200, Extra Light',
    		'300' => '300, Light',
    		'400' => '400, Normal',
    		'500' => '500, Medium',
    		'600' => '600, Semi Bold',
    		'700' => '700, Bold',
    		'800' => '800, Extra Bold',
    		'900' => '900, Black',
    	);
    	return $list;
    }

    public function font_style() {
    	$list = array(
    		'normal' => 'Normal',
    		'italic' => 'Italic',
    	);
    	return $list;
    }

    public function text_transform() {
    	$list = array(
    		'none' => 'None',
    		'uppercase' => 'Uppercase',
    		'lowercase' => 'Lowercase',
    		'capitalize' => 'Capitalize',
    	);
    	return $list;
    }

    public function typography_defaults() {
    	$list = array(
            'font_size'   	=> '13',
            'font_family' 	=> 'Arial',
            'font_weight' 	=> '400',
            'font_style' 	=> 'normal',
            'line_height' 	=> '1',
            'text_transform'=> 'none',
            'color'       	=> '#bdbdbd',
            'letter_spacing'=> '0'
        );
        return $list;	
    }

    public function typography_options() {
    	$list = array(
        	'font_family' => $this->fontslist(),
        	'font_weight' => $this->font_weight(),
        	'font_style' => $this->font_style(),
        	'text_transform' => $this->text_transform(),
        );
        return $list;
    }

    public function getLayouts() {

        $layouts = array(
            'layout-full-width' => 'Full Width - No side columns',
            'layout-left-column' => 'Two Columns, Left column',
            'layout-right-column' => 'Two Columns, Right column',
            'layout-both-columns' => 'Three Columns - One large central column and 2 side columns'
        );

        return $layouts;
        
    }

    public function checkupdates() {

        $update_list = "http://promokit.eu/share/updates/"._THEME_NAME_."/5/update_list.dat";          
        $msg = "";      
        if (!$update = @file_get_contents($update_list)) {
            $msg = false;
        } else {            
            $versions = explode(",", $update);              
            $i = 1;
            foreach ($versions as $key => $version) {
                if (Configuration::get('ALYSUM_VER') < $version) {
                    $msg .= (($i == 1) ? "" : ",").$version;
                    $i++;
                }
            }           
        }

        return $msg;
    }

}