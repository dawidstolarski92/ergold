<?php

if (!defined('_PS_VERSION_')) {
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Location: ../');
}

class InstallDemoData extends Module
{

	public function start($preset) {

		//$this->truncate();
		$installBD = $this->installDB($preset);

		if ($installBD == true) {
			$module = 'revsliderprestashop';
			if (Module::isEnabled($module)) {
				$installFiles = $this->copyFiles($preset);
				if ($installFiles != '') {
					$response = Module::displayError($installFiles);		
				} else {
					$response = Module::displayConfirmation('Import succeed');
				}
			} else {
				$response = Module::displayError('Revolution Slider is not installed');
			}
		} else {
			$response = Module::displayError($installBD);
		}

		return $response;
		
	}

    public function installDB($preset)
    {
        
        $sql_file = dirname(__FILE__).'/'.$preset.'.sql';

		if (!file_exists($sql_file)) {

			return 'There is no import file: '.$sql_file;

		} else if (!$sql = file_get_contents($sql_file)) {

			return 'SQL syntax error in import file: '.$sql_file;

		} else {

			$queries = str_replace('PREFIX_', _DB_PREFIX_, $sql);
			$queries = preg_split("/;\s*[\r\n]+/", $queries);

			foreach ($queries AS $query) {

				if ($query) {

					try {
		            	if (!Db::getInstance()->execute(trim($query))) {
							return 'Error in SQL syntax!';
						}
		            } catch (\Exception $e) {
		            	return 'Error in SQL syntax!';
		            }

				} else {
					return 'Empty Query';	
				}
			}
		}

		return true;

    }

    public function copyFiles($preset) {

    	$remote_file = 'http://alysum5.promokit.eu/data/dummy/'.$preset.'.zip';
    	$local_file = _PS_UPLOAD_DIR_.'files.zip';

    	$msg = '';
		if (!@copy($remote_file, $local_file)) {
			$msg .= "No demo content available for \"".$remote_file."\"";
		} else {
			$err = true;
			if (!Tools::ZipTest($local_file)) {
				$msg .= "Zip file seems to be broken";
			} else {
				$err = true;
				$zip = new ZipArchive;
				$res = $zip->open($local_file);
				if ($res === TRUE) {
				  $zip->extractTo(_PS_MODULE_DIR_);
				  $zip->close();
				  //unlink($local_file);
				} else {
				  $msg .= "Unable to unzip updated files!";
				}
			}
		}
		return $msg;

    }

    public function truncate() {
    	$sqls = array(
    		'TRUNCATE `ps_revslider_attachment_images`',
    		'TRUNCATE `ps_revslider_sliders`',
    		'TRUNCATE `ps_revslider_slides`'
    	);
    	foreach ($sqls as $sql) {
    		Db::getInstance()->execute($sql);
    	}
    }


}