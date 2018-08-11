<?php
/*
* @author    addonsPresta.com <mail@addonspresta.com>
* @copyright 2017 addonsPresta.com
*/

include_once dirname(__FILE__) . '/../ParentAllegroController.php';
include_once dirname(__FILE__) . '/../../allegro.inc.php';

class AdminAllegroThemeController extends ParentAllegroController
{
    /**
     * Set bootstrap theme
     * @var boolean
     */
	public $bootstrap = true;

    private $form_switch = 'radio';

    /**
     * AdminController::__construct() override
     *
     * @see AdminController::__construct()
     */
	public function __construct()
	{
	 	$this->table = 'allegro_theme';
	 	$this->className = 'AllegroTheme';
	 	//$this->identifier = 'id_allegro_theme';
	 	$this->lang = false;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

        $this->form_switch = (version_compare(_PS_VERSION_, '1.6.0.0', '>=') 
            ? 'switch' 
            : 'radio'
        );

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            ),
            'enableSelection' => array('text' => $this->l('Enable selection')),
            'disableSelection' => array('text' => $this->l('Disable selection'))
        );

		$this->fields_list = array(
			'id_allegro_theme' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto'
			),
            'format' => array(
                'title' => $this->l('New format'),
                'width' => 'auto',
                'align' => 'center',
                'callback' => 'printFormat',
                'type' => 'bool',
            ),
            'default' => array(
                'title' => $this->l('Is default'),
                'activeVisu' => 1,
                'type' => 'bool',
                'align' => 'center',
                'orderby' => false,
            ),
			'active' => array(
				'title' => $this->l('Status'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false,
				'width' => 25
			)
		);
	}


    public function printFormat($value, $row)
    {
        return ($value ? $this->l('Yes') : $this->l('No'));
    }

    /**
     * AdminController::renderList() override
     *
     * @see AdminController::renderList()
     */
	public function renderList()
	{
		$this->displayInformation(
			'<p><b>'.$this->l('On this page you can manage your allegro themes.').'</b></p>
			<p>'.$this->l('Using themes you can import product description, images, features and any other informations to offer description.').'</p>'
		);

		return parent::renderList();
	}


    /**
     * AdminController::renderForm() override
     *
     * @see AdminController::renderForm()
     */
	public function renderForm()
	{
		if (!($allegro_theme = $this->loadObject(true)))
			return;

		$image = _ALLEGRO_IMG_FRAME_DIR_.$allegro_theme->id.'.png';

		if(file_exists($image))
			$image_url = '../modules/allegro/img/frame/'.$allegro_theme->id.'.png';
		else
			$image_url = false;

		$image_size = file_exists($image) ? filesize($image) / 1000 : false;

        $input[] = array(
            'type' => 'text',
            'label' => $this->l('Theme name'),
            'name' => 'name',
            'size' => 50,
            'required' => true,
        );

        $oldFormat = 
            Tools::getIsset('old') || // add
            (Tools::getIsset('format') && !Tools::getValue('format')) || // save
            ($allegro_theme && !$allegro_theme->format); //edit
        if ($oldFormat) {
            $allegro_theme->format = 0;
        }

        if ($oldFormat) {
            $input[] = array(
                'type' => 'textarea',
                'label' => $this->l('Theme content'),
                'name' => 'content',
                'id' => 'content_html', // #content ID is used in admin theme
                //'class' => 'rte_light', // No class in PS < 1.6.1.0
                'cols' => 100,
                'rows' => 20,
                'autoload_rte' => (bool)Configuration::get('ALLEGRO_THEME_TINYMCE'),
                'required' => true,
                'desc' => $this->l('Theme HTML content, list of available markers available in the').' '.$this->l('documentation').'.'
            );

        } else {
            $this->addJs(__PS_BASE_URI__.'modules/allegro/js/tinymce/tinymce.min.js');

            $this->addJs(__PS_BASE_URI__.'modules/allegro/js/jq.nfAllegroEditor.js');
            $this->addCSS(__PS_BASE_URI__.'modules/allegro/css/nfAllegroEditor.css');
            
            $template = $this->createTemplate('../new_theme.tpl')->assign(
            array(
                'dev_mode' => Configuration::get('ALLEGRO_DEV_MODE'),
            ))->fetch();

            $input[] = array(
                'type' => 'text',
                'label' => $this->l('Theme editor'),
                'name' => 'content',
                'id' => 'content_html', // #content ID is used in admin theme
                'class' => 'rte_light', // No class in PS < 1.6.1.0
                'required' => true,
                'desc' => $template
            );
        }            

        // Only if edit old theme
        if (!$allegro_theme->format) {
            $input[] = array(
                'type' => $this->form_switch,
                'label' => $this->l('New format'),
                'name' => 'format',
                'values' => array(
                    array(
                        'id' => 'format_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                    ), 
                    array(
                        'id' => 'format_on',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                    )
                ),
                'desc' => $this->l('Form need to be saved to see effect, be careful old and new format are not compatible - change will clear theme content.')
            );
        }

        $input[] = array(
            'type' => 'file',
            'label' => $this->l('Cover frame'),
            'name' => 'image',
            'image' => $image_url ? '<img src="' . $image_url . '?rand=' . time() . '" style="max-height: 100px; width: auto" />' : false,
            'size' => $image_size,
            'display_image' => true,
            'delete_url' => self::$currentIndex.'&'.$this->identifier.'='.$allegro_theme->id.'&token='.$this->token.'&deleteImage=1',
            'col' => 6,
            'desc' => $this->l('You can add a transparent frame (*.png file only) that will be applied to the cover of the auction')
        );

        $commonImages = range(1, 5);
        foreach ($commonImages as $key => $id) {

            // Load image if exists
            $image = _ALLEGRO_IMG_FRAME_DIR_.$allegro_theme->id.'-image'.$id.'.png';
            $imageUrl = false;
            if(file_exists($image)) {
                $imageUrl = '../modules/allegro/img/frame/'.$allegro_theme->id.'-image'.$id.'.png';
            }

            $delUrl = self::$currentIndex.'&'.$this->identifier.'='.$allegro_theme->id.'&token='.$this->token.'&deleteImage=image'.$id.'';
            $imgTag = '<img src="'.$imageUrl.'?rand=' . time() . '" style="max-height: 50px; width: auto" />';

            $input[] = array(
                'type' => 'file',
                'label' => $this->l('Image').' '.$id,
                'name' => 'image'.$id,
                'image' => $imageUrl ? $imgTag : false,
                'size' => file_exists($image) ? filesize($image) / 1000 : false,
                'display_image' => true,
                'delete_url' => $delUrl,
                'col' => 6,
                'desc' => ($id == count($commonImages) ? '
                    <div class="alert alert-info">'.$this->l('You can upload common images for theme (beta).').'</div>
                ' : null).(version_compare(_PS_VERSION_, '1.6.0.0', '<') && file_exists($image) ? $imgTag.'<a href="'.$delUrl.'">('.$this->l('delete').')</a>' : ''),
            );
        }

        $input[] = array(
            'type' => $this->form_switch,
            'label' => $this->l('Is default'),
            'name' => 'default',
            'values' => array(
                array(
                    'id' => 'default_on',
                    'value' => 1,
                    'label' => $this->l('Yes')
                ), 
                array(
                    'id' => 'default_on',
                    'value' => 0,
                    'label' => $this->l('No')
                )
            ),
        );

        $this->fields_form = array(
        	'tinymce' => $oldFormat,
			'legend' => array(
				'title' => $this->l('Theme'),
				'icon' => 'icon-picture-o'
			),
			'input' => $input
		);

		$this->fields_form['submit'] = array(
			'title' => $this->l('   Save   '),
			'class' => 'btn btn-default pull-right'
		);

		$this->fields_form['save_and_stay'] = array(
			'title' => $this->l('   Save and stay   '),
			'class' => 'btn btn-default pull-right'
		);

		return parent::renderForm();
	}


    /**
     * AdminController::initPageHeaderToolbar() override
     *
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        if ($this->display !== 'edit') {
            $this->page_header_toolbar_btn['new_allegro_theme_format'] = array(
                'href' => self::$currentIndex.'&addallegro_theme&token='.$this->token,
                'desc' => $this->l('Add theme', null, null, false),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['new_allegro_theme'] = array(
                'href' => self::$currentIndex.'&addallegro_theme&old&token='.$this->token,
                'desc' => $this->l('Add theme (old format)', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
    * PrestaShop 1.5x only
    */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'add':
            case 'edit':
            default:
                $this->toolbar_btn['new_allegro_theme_format'] = array(
                'href' => self::$currentIndex.'&addallegro_theme&token='.$this->token,
                'desc' => $this->l('Add theme', null, null, false),
                'icon' => 'process-icon-new'
            );
                $this->toolbar_btn['new_allegro_theme'] = array(
                'href' => self::$currentIndex.'&addallegro_theme&old&token='.$this->token,
                'desc' => $this->l('Add theme (old format)', null, null, false),
                'icon' => 'process-icon-new'
            );
        }
    }



    /**
     * AdminController::postProcess() override
     *
     * @see AdminController::postProcess()
     */
	public function postProcess()
	{
		if (Tools::getValue('deleteImage')) {
			$this->processForceDeleteImage();
		}

		return parent::postProcess();
	}


	/**
	 * Delete theme image
	 * @return void
	 */
	public function processForceDeleteImage()
	{
        if ($obj = $this->loadObject(true)) {
            $imageRef = Tools::getValue('deleteImage');
            @unlink(_ALLEGRO_IMG_FRAME_DIR_.(int)$obj->id.($imageRef != 1 ? '-'.$imageRef : '').'.png');
        }
	}


    /**
     * AdminController::postImage() override
     *
     * @see AdminController::postImage()
     */
	protected function postImage($id)
	{
		if (($obj = $this->loadObject(true)) && isset($_FILES) && count($_FILES)) {
            foreach ($_FILES as $id => $file) {

                if ($file['error'] || !$file['size']) {
                    continue;
                }

                if (preg_match("/^image((\d){1,2})?$/", $id)) {
                    if (($id == 'image' && $file['type'] != 'image/png')) {
                        $this->errors[] = Tools::displayError('Only *.png (transparent) files are supported as frame.');
                    } elseif ($file['type'] != 'image/png' && $file['type'] != 'image/jpg' & $file['type'] != 'image/jpeg') {
                        $this->errors[] = Tools::displayError('Only .png or .jpg images are supported.');
                    } else {
        				if (!ImageManager::resize(
        					$file['tmp_name'],
        					_ALLEGRO_IMG_FRAME_DIR_.(int)$obj->id.($id == 'image' ? '' : '-'.$id).'.png',
        					null,
        					null,
        					'png',
        					true
        				)) {
                            $this->errors[] = Tools::displayError('Unable to resize image.');
                        }
                    }
    			}
            }
		}
	}
}
