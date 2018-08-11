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

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('RevsliderPrestashop')) {
    Module::getInstanceByName('revsliderprestashop');
}

include_once(_PS_MODULE_DIR_.'revsliderprestashop/revslider_admin.php');

class AdminRevolutionsliderAjaxController extends ModuleAdminController
{

    protected $_ajax_results;

    protected $_ajax_stripslash;

    protected $_filter_whitespace;

    protected $lushslider_model;

    public function __construct()
    {
        $this->display_header = false;
        $this->display_footer = false;
        $this->content_only   = true;
        parent::__construct();
        $this->_ajax_results['error_on'] = 1;
        // Let's include Lushslider Model
    }
    public function init()
    {

        // Process POST | GET
        $this->initProcess();
    }
    /**
     * 
     * @throws Exception
     */
    public function initProcess()
    {
     
        $revAction = Tools::getValue('revControllerAction');
        
//        if(!empty($revAction))
        $loadTemplate = false;
        
        
        new RevSliderAdmin(_PS_MODULE_DIR_.'revsliderprestashop', $loadTemplate);
        
        switch ($revAction) {
            
            case 'uploadimage':
                $this->revUploader();
                break;
            case 'captions':
                
                $db = new UniteDBRev();

                $styles = $db->fetch(GlobalsRevSlider::$table_css);

                header("Content-Type: text/css; charset=utf-8");
               
                echo UniteCssParserRev::parseDbArrayToCss($styles, "\n");

                break;
            
            default:
                
                break;
            
        }
        
        die();
    }
    private function revUploader()
    {
        $key = Tools::getValue('security_key');

        if (empty($key) ||
                Tools::encrypt(GlobalsRevSlider::MODULE_NAME) != $key) {
            echo Tools::jsonEncode(array('error_on' => 1,
                'error_details' => 'Security Error'));
            die();
        }
        
        $targetFolder = ABSPATH.'/uploads/';

        $info = pathinfo($_FILES['Filedata']['name']);
        $NewFileName = preg_replace_callback('/[^a-zA-Z0-9_\-]+/', create_function('$match', 'return "-";'), $info['filename']);
        if (!empty($_FILES)) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            
            $targetPath = $targetFolder;
            
            // Validate the file type
            $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
    

            if (in_array($info['extension'], $fileTypes)) {
                 $worked = UniteFunctionsWPRev::importMediaImg($tempFile, $targetPath, $NewFileName.'.'.$info['extension']);
                if (!empty($worked)) {
                    echo '1';
                }
            } else {
                echo '0';
            }
        }
    }

    protected function bindToAjaxRequest($post_method = false)
    {
        if (!$this->isXmlHttpRequest()) {
            die('We Only Accept Ajax Request');
        }
        // Also Restricted to POST method
        if ($post_method) {
            if (!@RevsliderPrestashop::getIsset($_SERVER['REQUEST_METHOD']) or 'POST' != $_SERVER['REQUEST_METHOD']) {
                die('Only POST Request Method is allowed');
            }
        }
        return true;
    }
     /* Ends bindToAjaxRequest() */
}
