<?php

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'pk_fullpageslider/fullpageslide.php');

class pk_fullpageslider extends Module
{
	private $_html = '';
	private $default_speed = 1500;
	private $default_pause = 5000;
	private $default_loop = 0;
	private $default_nav = 1;
	private $default_navpos = 0;
	private $default_infinite = 0;

	public function __construct()
	{
		$this->name = 'pk_fullpageslider';
		$this->version = '1.1';
		$this->author = 'promokit.eu';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);
		$this->bootstrap = true;
		$this->fs = _DB_PREFIX_.'pk_fullpageslider';
		$this->fs_slides = _DB_PREFIX_.'pk_fullpageslider_slides';
		$this->fs_slides_lang = _DB_PREFIX_.'pk_fullpageslider_slides_lang';

		parent::__construct();

		$this->displayName = 'Full Page Slider';
		$this->description = $this->trans('Each slide takes whole page space', array(), 'Modules.flexmenu.Admin');
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => '1.7.9');
	}

	/**
	 * @see Module::install()
	 */
	public function install()
	{
		/* Adds Module */
		if (parent::install() &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('displayTopColumn') &&
			$this->registerHook('actionShopDataDuplication'))
		{
			$shops = Shop::getContextListShopID();
			$shop_groups_list = array();

			/* Setup each shop */
			foreach ($shops as $shop_id)
			{
				$shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);

				if (!in_array($shop_group_id, $shop_groups_list))
					$shop_groups_list[] = $shop_group_id;

				/* Sets up configuration */
				$res = Configuration::updateValue('FPS_SPEED', $this->default_speed, false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_PAUSE', $this->default_pause, false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_LOOP', $this->default_loop, false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_NAV', $this->default_nav, false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_NAVPOS', $this->default_navpos, false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_INFINITE', $this->default_infinite, false, $shop_group_id, $shop_id);
			}

			/* Sets up Shop Group configuration */
			if (count($shop_groups_list))
			{
				foreach ($shop_groups_list as $shop_group_id)
				{
					$res = Configuration::updateValue('FPS_SPEED', $this->default_speed, false, $shop_group_id);
					$res &= Configuration::updateValue('FPS_PAUSE', $this->default_pause, false, $shop_group_id);
					$res &= Configuration::updateValue('FPS_LOOP', $this->default_loop, false, $shop_group_id);
					$res &= Configuration::updateValue('FPS_NAV', $this->default_nav, false, $shop_group_id, $shop_id);
					$res &= Configuration::updateValue('FPS_NAVPOS', $this->default_navpos, false, $shop_group_id, $shop_id);
					$res &= Configuration::updateValue('FPS_INFINITE', $this->default_infinite, false, $shop_group_id, $shop_id);
				}
			}

			/* Sets up Global configuration */
			$res = Configuration::updateValue('FPS_SPEED', $this->default_speed);
			$res &= Configuration::updateValue('FPS_PAUSE', $this->default_pause);
			$res &= Configuration::updateValue('FPS_LOOP', $this->default_loop);
			$res &= Configuration::updateValue('FPS_NAV', $this->default_nav);
			$res &= Configuration::updateValue('FPS_NAVPOS', $this->default_navpos);
			$res &= Configuration::updateValue('FPS_INFINITE', $this->default_infinite);

			/* Creates tables */
			$res &= $this->createTables();

			/* Adds samples */
			if ($res)
				$this->installSamples();

			return (bool)$res;
		}

		return false;
	}

	/**
	 * Adds samples
	 */
	private function installSamples()
	{
		$languages = Language::getLanguages(false);
		$s = (int)$this->context->shop->id;
		$sql = array();
		$sql[] = "INSERT INTO `"._DB_PREFIX_."pk_fullpageslider` (`id_slides`, `id_shop`) VALUES (1, ".$s."),(2, ".$s."),(3, ".$s."),(4, ".$s."),(5, ".$s.");";
		$sql[] = "INSERT INTO `"._DB_PREFIX_."pk_fullpageslider_slides` (`id_slides`, `position`, `active`, `text_animation`, `text_delay`, `text_align`, `text_width`, `text_speed`, `text_x`, `text_y`, `subimage01_state`, `subimage01_animation`, `subimage01_delay`, `subimage01_speed`, `subimage01_x`, `subimage01_y`, `subimage02_state`, `subimage02_animation`, `subimage02_delay`, `subimage02_speed`, `subimage02_x`, `subimage02_y`, `subimage03_state`, `subimage03_animation`, `subimage03_delay`, `subimage03_speed`, `subimage03_x`, `subimage03_y`) VALUES
			(1, 0, 1, 'fadeInDown', 1000, 1, 42, 1000, 55, 33, 0, 'fadeInDown', 0, 500, 0, 0, 0, 'bounceIn', 0, 500, 0, 0, 0, 'bounce', 0, 500, 0, 0),
			(2, 0, 1, 'bounceInLeft', 500, 0, 40, 1000, 1, 33, 1, 'bounceIn', 1500, 500, 33, 0, 1, 'bounceInLeft', 1500, 1000, 0, 0, 1, 'bounceInUp', 1000, 1000, 0, 67),
			(3, 0, 1, 'bounceInRight', 1000, 1, 40, 1500, 57, 33, 0, 'bounce', 0, 500, 0, 0, 0, 'bounce', 0, 500, 0, 0, 0, 'bounce', 0, 500, 0, 0),
			(4, 0, 1, 'fadeInLeft', 1000, 0, 44, 1000, 0, 33, 0, 'bounce', 0, 0, 0, 0, 0, 'bounce', 0, 0, 0, 0, 0, 'bounce', 0, 0, 0, 0),
			(5, 0, 1, 'zoomIn', 1000, 1, 40, 1000, 59, 33, 1, 'bounceInDown', 1000, 1000, 51, 0, 1, 'fadeInRight', 1500, 1000, 74, 0, 1, 'bounceInRight', 1700, 600, 57, 70);";
		foreach ($languages as $l)
			$sql[] = "INSERT INTO `"._DB_PREFIX_."pk_fullpageslider_slides_lang` (`id_slides`, `id_lang`, `title`, `description`, `legend`, `url`, `image`, `subimage01`, `subimage02`, `subimage03`) VALUES
				(1, ".$l['id_lang'].", 'Finish your look', '<h2>Finish your look</h2>\n<h6>with hottest jewelry trend</h6>', 'sample-1', '#', 'slide01.jpg', '', '', ''),
				(2, ".$l['id_lang'].", '#1 look urban edge', '<h2><em>#1</em> look urban edge</h2>\n<h6>for ultra-chic, city-girl style</h6>', 'sample-2', '#', 'slide02.jpg', 'slide02_subimg01.png', 'slide02_subimg02.png', 'slide02_subimg03.png'),
				(3, ".$l['id_lang'].", '#2 look true blue', '<h2><em>#2</em> look<br /> true blue</h2>\n<h6>escape the grey days...</h6>', 'sample-3', '#', 'slide03.jpg', '', '', ''),
				(4, ".$l['id_lang'].", '#3 look full gloss', '<h2><em>#3</em> look<br /> full gloss</h2>\n<h6>style for every occation</h6>', 'sample-4', '#', 'slide04.jpg', '', '', ''),
				(5, ".$l['id_lang'].", '#4 look all gold', '<h2><em>#4</em> look<br />all gold</h2>\n<h6>are you ready to inspire...</h6>', 'sample-5', '#', 'slide05.jpg', 'slide04_subimg01.png', 'slide04_subimg02.png', 'slide04_subimg03.png');";

		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
					return false;

		return true;

	}

	/**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
		/* Deletes Module */
		if (parent::uninstall())
		{
			/* Deletes tables */
			$res = $this->deleteTables();

			/* Unsets configuration */
			$res &= Configuration::deleteByName('FPS_SPEED');
			$res &= Configuration::deleteByName('FPS_PAUSE');
			$res &= Configuration::deleteByName('FPS_LOOP');
			$res &= Configuration::deleteByName('FPS_NAV');
			$res &= Configuration::deleteByName('FPS_NAVPOS');
			$res &= Configuration::deleteByName('FPS_INFINITE');

			return (bool)$res;
		}

		return false;
	}

	/**
	 * Creates tables
	 */
	protected function createTables()
	{
		/* Slides */
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS '.$this->fs.' (
				`id_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_slides`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* Slides configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'.$this->fs_slides.'` (
			  `id_slides` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `position` int(10) unsigned NOT NULL DEFAULT \'0\',
			  `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `text_animation` varchar(255) NOT NULL DEFAULT \'bounce\',
			  `text_delay` int(9) NOT NULL DEFAULT \'0\',
			  `text_width` int(9) NOT NULL DEFAULT \'40\',
			  `text_align` int(2) NOT NULL DEFAULT \'1\',
			  `text_speed` int(9) NOT NULL DEFAULT \'500\',
			  `text_x` int(2) NOT NULL DEFAULT \'0\',
			  `text_y` int(2) NOT NULL DEFAULT \'0\',
			  `subimage01_state` int(2) NOT NULL DEFAULT \'0\',
			  `subimage01_animation` varchar(255) NOT NULL DEFAULT \'bounce\',
			  `subimage01_delay` int(9) NOT NULL DEFAULT \'0\',
			  `subimage01_speed` int(9) NOT NULL DEFAULT \'500\',
			  `subimage01_x` int(2) NOT NULL DEFAULT \'0\',
			  `subimage01_y` int(2) NOT NULL DEFAULT \'0\',
			  `subimage02_state` int(2) NOT NULL DEFAULT \'0\',
			  `subimage02_animation` varchar(255) NOT NULL DEFAULT \'bounce\',
			  `subimage02_delay` int(9) NOT NULL DEFAULT \'0\',
			  `subimage02_speed` int(9) NOT NULL DEFAULT \'500\',
			  `subimage02_x` int(2) NOT NULL DEFAULT \'0\',
			  `subimage02_y` int(2) NOT NULL DEFAULT \'0\',
			  `subimage03_state` int(2) NOT NULL DEFAULT \'0\',
			  `subimage03_animation` varchar(255) NOT NULL DEFAULT \'bounce\',
			  `subimage03_delay` int(9) NOT NULL DEFAULT \'0\',
			  `subimage03_speed` int(9) NOT NULL DEFAULT \'500\',
			  `subimage03_x` int(2) NOT NULL DEFAULT \'0\',
			  `subimage03_y` int(2) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`id_slides`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* Slides lang configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'.$this->fs_slides_lang.'` (
			  `id_slides` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  `legend` varchar(255) NOT NULL,
			  `url` varchar(255) NOT NULL,
			  `image` varchar(255) NOT NULL,
			  `subimage01` varchar(255) NOT NULL,
			  `subimage02` varchar(255) NOT NULL,
			  `subimage03` varchar(255) NOT NULL,
			  PRIMARY KEY (`id_slides`,`id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		return $res;
	}

	/**
	 * deletes tables
	 */
	protected function deleteTables()
	{
		$slides = $this->getSlides();
		foreach ($slides as $slide) {
			$to_del = new fullpageslide($slide['id_slide']);
			$to_del->delete();
		}

		return Db::getInstance()->execute('DROP TABLE IF EXISTS '.$this->fs.', `'.$this->fs_slides.'`, `'.$this->fs_slides_lang.'`;');
	}

	public function getContent()
	{
		$this->context->controller->addCSS($this->_path.'css/animate.css');
		$this->context->controller->addJS($this->_path.'js/script.js');
		$this->_html .= $this->headerHTML();

		/* Validate & process */
		if (Tools::isSubmit('submitSlide') || 
			Tools::isSubmit('delete_id_slide') ||
			Tools::isSubmit('submitSlider') ||
			Tools::isSubmit('changeStatus'))
		{
			if ($this->_postValidation())
			{
				$this->_postProcess();
				$this->_html .= $this->renderForm();
				$this->_html .= $this->renderList();
			}
			else
				$this->_html .= $this->renderAddForm();

			$this->clearCache();
		}
		elseif (Tools::isSubmit('addSlide') || (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide'))))
		{
			if (Tools::isSubmit('addSlide'))
				$mode = 'add';
			else
				$mode = 'edit';

			if ($mode == 'add')
			{
				if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL)
					$this->_html .= $this->renderAddForm();
				else
					$this->_html .= $this->getShopContextError(null, $mode);
			}
			else
			{
				$associated_shop_ids = fullpageslide::getAssociatedIdsShop((int)Tools::getValue('id_slide'));
				$context_shop_id = (int)Shop::getContextShopID();

				if ($associated_shop_ids === false)
					$this->_html .= $this->getShopAssociationError((int)Tools::getValue('id_slide'));
				else if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL && in_array($context_shop_id, $associated_shop_ids))
				{
					if (count($associated_shop_ids) > 1)
						$this->_html = $this->getSharedSlideWarning();
					$this->_html .= $this->renderAddForm();
				}
				else
				{
					$shops_name_list = array();
					foreach ($associated_shop_ids as $shop_id)
					{
						$associated_shop = new Shop((int)$shop_id);
						$shops_name_list[] = $associated_shop->name;
					}
					$this->_html .= $this->getShopContextError($shops_name_list, $mode);
				}
			}
		}
		else // Default viewport
		{
			$this->_html .= $this->getWarningMultishopHtml().$this->getCurrentShopInfoMsg().$this->renderForm();

			if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL)
				$this->_html .= $this->renderList();
		}

		return $this->_html;
	}

	private function _postValidation()
	{
		$errors = array();

		/* Validation for Slider configuration */
		if (Tools::isSubmit('submitSlider'))
		{
			if (!Validate::isInt(Tools::getValue('FPS_SPEED')) || !Validate::isInt(Tools::getValue('FPS_PAUSE')))
				$errors[] = $this->trans('Invalid values', array(), 'Admin.Notifications.Error');
		} /* Validation for status */
		elseif (Tools::isSubmit('changeStatus'))
		{
			if (!Validate::isInt(Tools::getValue('id_slide')))
				$errors[] = $this->trans('Invalid slide', array(), 'Admin.Notifications.Error');
		}
		/* Validation for Slide */
		elseif (Tools::isSubmit('submitSlide'))
		{
			/* Checks state (active) */
			if (!Validate::isInt(Tools::getValue('active_slide')) || (Tools::getValue('active_slide') != 0 && Tools::getValue('active_slide') != 1))
				$errors[] = $this->trans('Invalid slide state', array(), 'Admin.Notifications.Error');
			/* Checks position */
			if (!Validate::isInt(Tools::getValue('position')) || (Tools::getValue('position') < 0))
				$errors[] = $this->trans('Invalid slide position', array(), 'Admin.Notifications.Error');

			/* Checks description delay */
			if (!Validate::isInt(Tools::getValue('text_width')))
				$errors[] = $this->trans('Invalid "Description Width" value', array(), 'Admin.Notifications.Error');
			/* Checks description width */
			if (!Validate::isInt(Tools::getValue('text_delay')))
				$errors[] = $this->trans('Invalid "Description Delay" value', array(), 'Admin.Notifications.Error');
			/* Checks text speed */
			if (!Validate::isInt(Tools::getValue('text_speed')))
				$errors[] = $this->trans('Invalid "Description Speed" value', array(), 'Admin.Notifications.Error');
			/* Checks text X position */
			if (!Validate::isInt(Tools::getValue('text_x')))
				$errors[] = $this->trans('Invalid "Description Position X" value', array(), 'Admin.Notifications.Error');
			/* Checks text Y position */
			if (!Validate::isInt(Tools::getValue('text_y')))
				$errors[] = $this->trans('Invalid "Description Position Y" value', array(), 'Admin.Notifications.Error');

			/* Checks subimage01 delay */
			if (Tools::getValue('subimage01_state') != 0) {
				if (!Validate::isInt(Tools::getValue('subimage01_delay')))
					$errors[] = $this->trans('Invalid "SubImage 01 Delay" value', array(), 'Admin.Notifications.Error');
				/* Checks subimage01 speed */
				if (!Validate::isInt(Tools::getValue('subimage01_speed')))
					$errors[] = $this->trans('Invalid "SubImage 01 Speed" value', array(), 'Admin.Notifications.Error');
				/* Checks subimage01 X position */
				if (!Validate::isInt(Tools::getValue('subimage01_x')))
					$errors[] = $this->trans('Invalid "SubImage 01 Position X" value', array(), 'Admin.Notifications.Error');
				/* Checks subimage01 Y position */
				if (!Validate::isInt(Tools::getValue('subimage01_y')))
					$errors[] = $this->trans('Invalid "SubImage 01 Position Y" value', array(), 'Admin.Notifications.Error');
			}

			/* Checks subimage02 delay */
			if (Tools::getValue('subimage02_state') != 0) {
				if (!Validate::isInt(Tools::getValue('subimage02_delay')))
					$errors[] = $this->trans('Invalid "SubImage 02 Delay" value', array(), 'Admin.Notifications.Error');
				/* Checks subimage02 speed */
				if (!Validate::isInt(Tools::getValue('subimage02_speed')))
					$errors[] = $this->trans('Invalid "SubImage 02 Speed" value', array(), 'Admin.Notifications.Error');
				/* Checks subimage02 X position */
				if (!Validate::isInt(Tools::getValue('subimage02_x')))
					$errors[] = $this->trans('Invalid "SubImage 02 Position X" value', array(), 'Admin.Notifications.Error');
				/* Checks subimage02 Y position */
				if (!Validate::isInt(Tools::getValue('subimage02_y')))
					$errors[] = $this->trans('Invalid "SubImage 02 Position Y" value', array(), 'Admin.Notifications.Error');
			}

			/* Checks subimage03 delay */
			if (Tools::getValue('subimage02_state') != 0) {
				if (!Validate::isInt(Tools::getValue('subimage03_delay')))
					$errors[] = $this->trans('Invalid "SubImage 03 Delay" value', array(), 'Admin.Notifications.Error');
				/* Checks subimage03 speed */
				if (!Validate::isInt(Tools::getValue('subimage03_speed')))
					$errors[] = $this->trans('Invalid "SubImage 03 Speed" value', array(), 'Admin.Notifications.Error');
				/* Checks subimage03 X position */
				if (!Validate::isInt(Tools::getValue('subimage03_x')))
					$errors[] = $this->trans('Invalid "SubImage 03 Position X" value', array(), 'Admin.Notifications.Error');
				/* Checks subimage03 Y position */
				if (!Validate::isInt(Tools::getValue('subimage03_y')))
					$errors[] = $this->trans('Invalid "SubImage 03 Position Y" value', array(), 'Admin.Notifications.Error');
			}
			/* If edit : checks id_slide */
			if (Tools::isSubmit('id_slide'))
			{
				//d(var_dump(Tools::getValue('id_slide')));
				if (!Validate::isInt(Tools::getValue('id_slide')) && !$this->slideExists(Tools::getValue('id_slide')))
					$errors[] = $this->trans('Invalid slide ID', array(), 'Admin.Notifications.Error');
			}
			/* Checks title/url/legend/description/image */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::strlen(Tools::getValue('title_'.$language['id_lang'])) > 255)
					$errors[] = $this->trans('The title is too long', array(), 'Admin.Notifications.Error');
				if (Tools::strlen(Tools::getValue('legend_'.$language['id_lang'])) > 255)
					$errors[] = $this->trans('The caption is too long', array(), 'Admin.Notifications.Error');
				if (Tools::strlen(Tools::getValue('url_'.$language['id_lang'])) > 255)
					$errors[] = $this->trans('The URL is too long', array(), 'Admin.Notifications.Error');
				if (Tools::strlen(Tools::getValue('description_'.$language['id_lang'])) > 4000)
					$errors[] = $this->trans('The description is too long', array(), 'Admin.Notifications.Error');
				if (Tools::strlen(Tools::getValue('url_'.$language['id_lang'])) > 0 && !Validate::isUrl(Tools::getValue('url_'.$language['id_lang'])))
					$errors[] = $this->trans('The URL format is not correct', array(), 'Admin.Notifications.Error');
				if (Tools::getValue('image_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_'.$language['id_lang'])))
					$errors[] = $this->trans('Invalid filename', array(), 'Admin.Notifications.Error');
				if (Tools::getValue('subimage01_state') != 0) {
					if (Tools::getValue('subimage01_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('subimage01_'.$language['id_lang'])))
					$errors[] = $this->trans('Invalid filename', array(), 'Admin.Notifications.Error');
				}
				if (Tools::getValue('subimage02_state') != 0) {	
					if (Tools::getValue('subimage02_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('subimage02_'.$language['id_lang'])))
					$errors[] = $this->trans('Invalid filename', array(), 'Admin.Notifications.Error');
				}
				if (Tools::getValue('subimage03_state') != 0) {
					if (Tools::getValue('subimage03_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('subimage03_'.$language['id_lang'])))
					$errors[] = $this->trans('Invalid filename', array(), 'Admin.Notifications.Error');
				}
				if (Tools::getValue('image_old_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_old_'.$language['id_lang'])))
					$errors[] = $this->trans('Invalid filename', array(), 'Admin.Notifications.Error');
			}

			/* Checks title/url/legend/description for default lang */
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('title_'.$id_lang_default)) == 0)
				$errors[] = $this->trans('The title is not set', array(), 'Admin.Notifications.Error');
			if (Tools::strlen(Tools::getValue('legend_'.$id_lang_default)) == 0)
				$errors[] = $this->trans('The caption is not set', array(), 'Admin.Notifications.Error');
			if (Tools::strlen(Tools::getValue('url_'.$id_lang_default)) == 0)
				$errors[] = $this->trans('The URL is not set', array(), 'Admin.Notifications.Error');
			if (!Tools::isSubmit('has_picture') && (!isset($_FILES['image_'.$id_lang_default]) || empty($_FILES['image_'.$id_lang_default]['tmp_name'])))
				$errors[] = $this->trans('The image is not set', array(), 'Admin.Notifications.Error');
			if (Tools::getValue('image_old_'.$id_lang_default) && !Validate::isFileName(Tools::getValue('image_old_'.$id_lang_default)))
				$errors[] = $this->trans('The image is not set', array(), 'Admin.Notifications.Error');
		} /* Validation for deletion */
		elseif (Tools::isSubmit('delete_id_slide') && (!Validate::isInt(Tools::getValue('delete_id_slide')) || !$this->slideExists((int)Tools::getValue('delete_id_slide'))))
			$errors[] = $this->trans('Invalid slide ID', array(), 'Admin.Notifications.Error');

		/* Display errors if needed */
		if (count($errors)) {
			$this->_html .= $this->displayError(implode('<br />', $errors));
			return false;
		}


		/* Returns if validation is ok */
		return true;
		
	}

	private function _postProcess()
	{
		$errors = array();
		$shop_context = Shop::getContext();
		$this->cssGenerator(Tools::getValue('id_slide'));
		/* Processes Slider */
		if (Tools::isSubmit('submitSlider'))
		{
			$shop_groups_list = array();
			$shops = Shop::getContextListShopID();

			foreach ($shops as $shop_id)
			{
				$shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);

				if (!in_array($shop_group_id, $shop_groups_list))
					$shop_groups_list[] = $shop_group_id;

				$res = Configuration::updateValue('FPS_SPEED', (int)Tools::getValue('FPS_SPEED'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_PAUSE', (int)Tools::getValue('FPS_PAUSE'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_LOOP', (int)Tools::getValue('FPS_LOOP'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_NAV', (int)Tools::getValue('FPS_NAV'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_NAVPOS', (int)Tools::getValue('FPS_NAVPOS'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue('FPS_INFINITE', (int)Tools::getValue('FPS_INFINITE'), false, $shop_group_id, $shop_id);
			}

			/* Update global shop context if needed*/
			switch ($shop_context)
			{
				case Shop::CONTEXT_ALL:
					$res = Configuration::updateValue('FPS_SPEED', (int)Tools::getValue('FPS_SPEED'));
					$res &= Configuration::updateValue('FPS_PAUSE', (int)Tools::getValue('FPS_PAUSE'));
					$res &= Configuration::updateValue('FPS_LOOP', (int)Tools::getValue('FPS_LOOP'));
					$res &= Configuration::updateValue('FPS_NAV', (int)Tools::getValue('FPS_NAV'));
					$res &= Configuration::updateValue('FPS_NAVPOS', (int)Tools::getValue('FPS_NAVPOS'));
					$res &= Configuration::updateValue('FPS_INFINITE', (int)Tools::getValue('FPS_INFINITE'));
					if (count($shop_groups_list))
					{
						foreach ($shop_groups_list as $shop_group_id)
						{
						$res = Configuration::updateValue('FPS_SPEED', (int)Tools::getValue('FPS_SPEED'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_PAUSE', (int)Tools::getValue('FPS_PAUSE'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_LOOP', (int)Tools::getValue('FPS_LOOP'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_NAV', (int)Tools::getValue('FPS_NAV'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_NAVPOS', (int)Tools::getValue('FPS_NAVPOS'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_INFINITE', (int)Tools::getValue('FPS_INFINITE'), false, $shop_group_id);
						}
					}
					break;
				case Shop::CONTEXT_GROUP:
					if (count($shop_groups_list))
					{
						foreach ($shop_groups_list as $shop_group_id)
						{
						$res = Configuration::updateValue('FPS_SPEED', (int)Tools::getValue('FPS_SPEED'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_PAUSE', (int)Tools::getValue('FPS_PAUSE'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_LOOP', (int)Tools::getValue('FPS_LOOP'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_NAV', (int)Tools::getValue('FPS_NAV'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_NAVPOS', (int)Tools::getValue('FPS_NAVPOS'), false, $shop_group_id);
						$res &= Configuration::updateValue('FPS_INFINITE', (int)Tools::getValue('FPS_INFINITE'), false, $shop_group_id);
						}
					}
					break;
			}

			$this->clearCache();

			if (!$res)
				$errors[] = $this->displayError($this->trans('The configuration could not be updated', array(), 'Admin.Notifications.Error'));
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=6&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		} /* Process Slide status */
		elseif (Tools::isSubmit('changeStatus') && Tools::isSubmit('id_slide'))
		{
			$slide = new fullpageslide((int)Tools::getValue('id_slide'));
			if ($slide->active == 0)
				$slide->active = 1;
			else
				$slide->active = 0;
			$res = $slide->update();
			$this->clearCache();
			$this->_html .= ($res ? $this->displayConfirmation($this->trans('Configuration updated', array(), 'Admin.Notifications.Success')) : $this->displayError($this->trans('The configuration could not be updated', array(), 'Admin.Notifications.Error')));
		}
		/* Processes Slide */
		elseif (Tools::isSubmit('submitSlide'))
		{
			/* Sets ID if needed */
			if (Tools::getValue('id_slide'))
			{
				$slide = new fullpageslide((int)Tools::getValue('id_slide'));
				if (!Validate::isLoadedObject($slide))
				{
					$this->_html .= $this->displayError($this->trans('Invalid slide ID', array(), 'Admin.Notifications.Error'));
					return false;
				}
			}
			else
				$slide = new fullpageslide();
			/* Sets position */
			$slide->position = (int)Tools::getValue('position');
			/* Sets active */
			$slide->active = (int)Tools::getValue('active_slide');

			$slide->text_animation = Tools::getValue('text_animation');
			$slide->text_width = (int)Tools::getValue('text_width');
			$slide->text_align = (int)Tools::getValue('text_align');
			$slide->text_delay = (int)Tools::getValue('text_delay');
			$slide->text_speed = (int)Tools::getValue('text_speed');
			$slide->text_x = (int)Tools::getValue('text_x');
			$slide->text_y = (int)Tools::getValue('text_y');

			$slide->subimage01_state = Tools::getValue('subimage01_state');
			$slide->subimage01_animation = Tools::getValue('subimage01_animation');
			$slide->subimage01_delay = (int)Tools::getValue('subimage01_delay');
			$slide->subimage01_speed = (int)Tools::getValue('subimage01_speed');
			$slide->subimage01_x = (int)Tools::getValue('subimage01_x');
			$slide->subimage01_y = (int)Tools::getValue('subimage01_y');

			$slide->subimage02_state = Tools::getValue('subimage02_state');
			$slide->subimage02_animation = Tools::getValue('subimage02_animation');
			$slide->subimage02_delay = (int)Tools::getValue('subimage02_delay');
			$slide->subimage02_speed = (int)Tools::getValue('subimage02_speed');
			$slide->subimage02_x = (int)Tools::getValue('subimage02_x');
			$slide->subimage02_y = (int)Tools::getValue('subimage02_y');

			$slide->subimage03_state = Tools::getValue('subimage03_state');
			$slide->subimage03_animation = Tools::getValue('subimage03_animation');
			$slide->subimage03_delay = (int)Tools::getValue('subimage03_delay');
			$slide->subimage03_speed = (int)Tools::getValue('subimage03_speed');
			$slide->subimage03_x = (int)Tools::getValue('subimage03_x');
			$slide->subimage03_y = (int)Tools::getValue('subimage03_y');

			/* Sets each langue fields */
			$languages = Language::getLanguages(false);

			$imgs = array('image', 'subimage01', 'subimage02', 'subimage03');
			$types = array('jpg', 'gif', 'jpeg', 'png');

			foreach ($languages as $language)
			{
				$slide->title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
				$slide->url[$language['id_lang']] = Tools::getValue('url_'.$language['id_lang']);
				$slide->legend[$language['id_lang']] = Tools::getValue('legend_'.$language['id_lang']);
				$slide->description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']);

				/* Uploads image and sets slide */
				foreach ($imgs as $img) {

					$f = $_FILES[$img.'_'.$language['id_lang']];
					$tmp = $_FILES[$img.'_'.$language['id_lang']]['tmp_name'];
					$nm = $_FILES[$img.'_'.$language['id_lang']]['name'];

					$type = Tools::strtolower(Tools::substr(strrchr($nm, '.'), 1));
					$imagesize = @getimagesize($tmp);

					if (isset($f) && isset($tmp) && !empty($tmp) && !empty($imagesize) &&
						in_array(Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), $types) && 
						in_array($type, $types)) {

						$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
						$salt = sha1(microtime());

						if ($error = ImageManager::validateUpload($f))
							$errors[] = $error;
						elseif (!$temp_name || !move_uploaded_file($tmp, $temp_name))
							return false;
						elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/images/'.$salt.'_'.$nm, null, null, $type))
							$errors[] = $this->displayError($this->trans('An error occurred during the image upload process', array(), 'Admin.Notifications.Error'));

						if (isset($temp_name))
							@unlink($temp_name);

						if ($img == "image")
							$slide->image[$language['id_lang']] = $salt.'_'.$nm;
						if ($img == "subimage01")
							$slide->subimage01[$language['id_lang']] = $salt.'_'.$nm;
						if ($img == "subimage02")
							$slide->subimage02[$language['id_lang']] = $salt.'_'.$nm;
						if ($img == "subimage03")
							$slide->subimage03[$language['id_lang']] = $salt.'_'.$nm;
					}
					elseif (Tools::getValue($img.'_old_'.$language['id_lang']) != '') {
						if ($img == "image")
							$slide->image[$language['id_lang']] = Tools::getValue($img.'_old_'.$language['id_lang']);
						if ($img == "subimage01")
							$slide->subimage01[$language['id_lang']] = Tools::getValue($img.'_old_'.$language['id_lang']);
						if ($img == "subimage02")
							$slide->subimage02[$language['id_lang']] = Tools::getValue($img.'_old_'.$language['id_lang']);
						if ($img == "subimage03")
							$slide->subimage03[$language['id_lang']] = Tools::getValue($img.'_old_'.$language['id_lang']);
					}

				}
				
			}
			//print_r($slide);

			/* Processes if no errors  */
			if (!$errors)
			{
				/* Adds */
				if (!Tools::getValue('id_slide'))
				{
					if (!$slide->add())
						$errors[] = $this->displayError($this->trans('The slide could not be added', array(), 'Admin.Notifications.Error'));
				}
				/* Update */
				elseif (!$slide->update())
					$errors[] = $this->displayError($this->trans('The slide could not be updated', array(), 'Admin.Notifications.Error'));
				
				if (Tools::getValue('id_slide'))
					if ($resp = $this->cssWriter() != true)
						$errors[] = $resp;

				$this->clearCache();
			}
		} /* Deletes */
		elseif (Tools::isSubmit('delete_id_slide'))
		{
			$slide = new fullpageslide((int)Tools::getValue('delete_id_slide'));
			$res = $slide->delete();
			$this->clearCache();
			if (!$res)
				$this->_html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		}

		/* Display errors if needed */
		if (count($errors))
			$this->_html .= $this->displayError(implode('<br />', $errors));
		elseif (Tools::isSubmit('submitSlide') && Tools::getValue('id_slide'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		elseif (Tools::isSubmit('submitSlide'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
	}

	public function getSlidesettingsById()
	{

		$id_shop = $this->context->shop->id;

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT fs.`id_slides`, fs.`text_animation`, fs.`text_delay`, fs.`text_width`, fs.`text_align`, fs.`text_speed`, fs.`text_y`, fs.`text_x`, fs.`subimage01_state`, fs.`subimage01_animation`, fs.`subimage01_delay`, fs.`subimage01_speed`, fs.`subimage01_y`, fs.`subimage01_x`, fs.`subimage02_state`, fs.`subimage02_animation`, fs.`subimage02_delay`, fs.`subimage02_speed`, fs.`subimage02_y`, fs.`subimage02_x`, fs.`subimage03_state`, fs.`subimage03_animation`, fs.`subimage03_delay`, fs.`subimage03_speed`, fs.`subimage03_y`, fs.`subimage03_x`
			FROM '.$this->fs_slides.' fs
			LEFT JOIN '.$this->fs.' fss ON (fs.id_slides = fss.id_slides)
			WHERE fss.`id_shop` = '.(int)$id_shop.' AND fs.`active` = 1');

		foreach ($results as $key => $row) {
			$res[$row["id_slides"]] = $row;
		}

		return $res;
	}

	public function cssGenerator() {

		$sett = $this->getSlidesettingsById();
		$css = "";

		foreach ($sett as $key => $val) {
			$css .= "#section".$key." .slide-content {
				text-align:".(($val['text_align'] == 1) ? 'left' : 'right').";
				width:".$val['text_width']."%;
				top:".$val['text_y']."%;
				left:".$val['text_x']."%;
				animation-delay: ".($val['text_delay']/1000)."s;
				-webkit-animation-delay: ".($val['text_delay']/1000)."s;
				animation-duration: ".($val['text_speed']/1000)."s;
				-webkit-animation-duration: ".($val['text_speed']/1000)."s;
			}\n";
			$css .= "#section".$key." .subimage01 {
				top:".$val['subimage01_y']."%;
				left:".$val['subimage01_x']."%;
				animation-delay: ".($val['subimage01_delay']/1000)."s;
				-webkit-animation-delay: ".($val['subimage01_delay']/1000)."s;
				animation-duration: ".($val['subimage01_speed']/1000)."s;
				-webkit-animation-duration: ".($val['subimage01_speed']/1000)."s;
			}\n";
			$css .= "#section".$key." .subimage02 {
				top:".$val['subimage02_y']."%;
				left:".$val['subimage02_x']."%;
				animation-delay: ".($val['subimage02_delay']/1000)."s;
				-webkit-animation-delay: ".($val['subimage02_delay']/1000)."s;
				animation-duration: ".($val['subimage02_speed']/1000)."s;
				-webkit-animation-duration: ".($val['subimage02_speed']/1000)."s;
			}\n";
			$css .= "#section".$key." .subimage03 {
				top:".$val['subimage03_y']."%;
				left:".$val['subimage03_x']."%;
				animation-delay: ".($val['subimage03_delay']/1000)."s;
				-webkit-animation-delay: ".($val['subimage03_delay']/1000)."s;
				animation-duration: ".($val['subimage03_speed']/1000)."s;
				-webkit-animation-duration: ".($val['subimage03_speed']/1000)."s;
			}\n";	
		}
		
		return $css;
	}


	public function cssWriter() {
		
		$css_file = $_SERVER["DOCUMENT_ROOT"].$this->_path."css/shop".(int)Context::getContext()->shop->id.".css";

		if (!$f = @fopen($css_file, 'w')) {
			return $this->displayError($this->trans('Can\'t open css file!', array(), 'Admin.Notifications.Error'));
		} else {	
			$data = $this->cssGenerator();
			if (fwrite($f, $data) === FALSE)
				return $this->displayError($this->trans('Can\'t write settings!', array(), 'Admin.Notifications.Error'));
			fclose($f);
			return true;
		}

	}

	private function _prepareHook()
	{
		if (!$this->isCached($this->name.'.tpl', $this->getCacheId()))
		{
			$subimgs = array('subimage01','subimage02','subimage03');
			$slides = $this->getSlides(true);

			if (is_array($slides))
				foreach ($slides as &$slide)
				{

					foreach ($subimgs as $key => $value) {
						$slide['sizes'][$value] = @getimagesize((dirname(__FILE__).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$slide[$value]));

						if (isset($slide['sizes'][$value][3]) && $slide['sizes'][$value][3])
						$slide['size'][$value] = $slide['sizes'][$value][3];
					}
					
					$slide['sizes']['mainimg'] = @getimagesize((dirname(__FILE__).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$slide['image']));

					if (isset($slide['sizes']['mainimg'][3]) && $slide['sizes']['mainimg'][3])
						$slide['size']['mainimg'] = $slide['sizes']['mainimg'][3];

				}

			if (!$slides)
				return false;

			$this->smarty->assign(array('fps_slides' => $slides));
		}

		return true;
	}

	public function hookdisplayHeader($params)
	{
		$status = $this->getModuleState("displayTopColumn");
		if ($status == 1) {
			if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index')
				return;
			$this->context->controller->addCSS($this->_path.'css/animate.css');
			$this->context->controller->addCSS($this->_path.'css/'.$this->name.'.css');
			$this->context->controller->addCSS($this->_path."css/shop".(int)Context::getContext()->shop->id.".css");
			$this->context->controller->addJS($this->_path.'js/jquery.fullPage.min.js');
			$this->context->controller->addJS($this->_path.'js/'.$this->name.'.js');

			$titles = $this->getSlidesTitles();

			$slider = array(
				'speed' => Configuration::get('FPS_SPEED'),
				'pause' => Configuration::get('FPS_PAUSE'),
				'loop' => (bool)Configuration::get('FPS_LOOP'),
				'nav' => Configuration::get('FPS_NAV'),
				'navpos' => (bool)Configuration::get('FPS_NAVPOS'),
				'infinite' => (bool)Configuration::get('FPS_INFINITE'),
				'titles' => $titles
			);

			$this->smarty->assign('fps', $slider);
			return $this->display(__FILE__, 'header.tpl');
		}
	}

	public function hookdisplayTopColumn($params)
	{
		$status = $this->getModuleState("displayTopColumn");
		if ($status == 1) {
			if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index')
				return;

			if (!$this->_prepareHook())
				return false;
			return $this->display(__FILE__, $this->name.'.tpl', $this->getCacheId());
		}
	}

	public function clearCache()
	{
		$this->_clearCache($this->name.'.tpl');
	}

	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
			INSERT IGNORE INTO '.$this->fs.' (id_slides, id_shop)
			SELECT id_slides, '.(int)$params['new_id_shop'].'
			FROM '.$this->fs.'
			WHERE id_shop = '.(int)$params['old_id_shop']
		);
		$this->clearCache();
	}

	public function headerHTML()
	{
		if (Tools::getValue('controller') != 'AdminModules' && Tools::getValue('configure') != $this->name)
			return;

		$this->context->controller->addJqueryUI('ui.sortable');
		/* Style & js for fieldset 'slides configuration' */
		$html = '<script type="text/javascript">
			$(function() {
				var $mySlides = $("#slides");
				$mySlides.sortable({
					opacity: 0.6,
					cursor: "move",
					update: function() {
						var order = $(this).sortable("serialize") + "&action=updateSlidesPosition";
						$.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'", order);
						}
					});
				$mySlides.hover(function() {
					$(this).css("cursor","move");
					},
					function() {
					$(this).css("cursor","auto");
				});
			});
		</script>';

		return $html;
	}

	public function getNextPosition()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT MAX(hss.`position`) AS `next_position`
			FROM `'.$this->fs_slides.'` hss, '.$this->fs.' hs
			WHERE hss.`id_slides` = hs.`id_slides` AND hs.`id_shop` = '.(int)$this->context->shop->id
		);

		return (++$row['next_position']);
	}

	public function getSlidesTitles() {

		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT fsl.`title` 
			FROM '.$this->fs_slides_lang.' fsl
			LEFT JOIN '.$this->fs.' fs ON (fsl.id_slides = fs.id_slides)
			LEFT JOIN '.$this->fs_slides.' fss ON (fss.id_slides = fs.id_slides)
			WHERE fs.id_shop = '.(int)$id_shop.'
			AND fsl.id_lang = '.(int)$id_lang.' 
			AND fss.`active` = 1'
		);
		
	}

	public function getSlides($active = null)
	{
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_slides` as id_slide, hssl.`image`, hss.`position`, hss.`active`, hssl.`title`,
			hssl.`url`, hssl.`legend`, hssl.`description`, hssl.`image`, hssl.`subimage01`, hss.`text_speed`, hss.`text_width`, hss.`text_align`, hss.`text_delay`, hss.`text_x`, hss.`text_y`, hss.`text_animation`, hss.`subimage01_speed`, hss.`subimage01_delay`, hss.`subimage01_x`, hss.`subimage01_y`, hss.`subimage01_state`, hss.`subimage01_animation`, hssl.`subimage02`, hss.`subimage02_speed`, hss.`subimage02_delay`, hss.`subimage02_x`, hss.`subimage02_y`, hss.`subimage02_state`, hss.`subimage02_animation`, hssl.`subimage03`, hss.`subimage03_speed`, hss.`subimage03_delay`, hss.`subimage03_x`, hss.`subimage03_y`, hss.`subimage03_state`, hss.`subimage03_animation`
			FROM '.$this->fs.' hs
			LEFT JOIN '.$this->fs_slides.' hss ON (hs.id_slides = hss.id_slides)
			LEFT JOIN '.$this->fs_slides_lang.' hssl ON (hss.id_slides = hssl.id_slides)
			WHERE id_shop = '.(int)$id_shop.'
			AND hssl.id_lang = '.(int)$id_lang.
			($active ? ' AND hss.`active` = 1' : ' ').'
			ORDER BY hss.position'
		);
	}

	public function getAllImagesBySlidesId($id_slides, $active = null, $id_shop = null)
	{
		$this->context = Context::getContext();
		$images = array();

		if (!isset($id_shop))
			$id_shop = $this->context->shop->id;

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hssl.`image`, hssl.`subimage01`, hssl.`subimage02`, hssl.`subimage03`, hssl.`id_lang`
			FROM '.$this->fs.' hs
			LEFT JOIN '.$this->fs_slides.' hss ON (hs.id_slides = hss.id_slides)
			LEFT JOIN '.$this->fs_slides_lang.' hssl ON (hss.id_slides = hssl.id_slides)
			WHERE hs.`id_slides` = '.(int)$id_slides.' AND hs.`id_shop` = '.(int)$id_shop.
			($active ? ' AND hss.`active` = 1' : ' ')
		);

		foreach ($results as $result) {
			$images[$result['id_lang']] = $result['image'];
			//$images["sub01"][$result['id_lang']] = $result['subimage01'];
		}

		return $images;
	}

	public function displayStatus($id_slide, $active)
	{
		$title = ((int)$active == 0 ? $this->trans('Disabled', array(), 'Admin.Global') : $this->trans('Enabled', array(), 'Admin.Global'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&id_slide='.(int)$id_slide.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}

	public function slideExists($id_slide)
	{
		$req = 'SELECT hs.`id_slides` as id_slide
				FROM '.$this->fs.' hs
				WHERE hs.`id_slides` = '.(int)$id_slide;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

		return ($row);
	}

	// the list of images in the first settings page
	public function renderList()
	{
		$slides = $this->getSlides();
		foreach ($slides as $key => $slide)
		{
			$slides[$key]['status'] = $this->displayStatus($slide['id_slide'], $slide['active']);
			$associated_shop_ids = fullpageslide::getAssociatedIdsShop((int)$slide['id_slide']);
			if ($associated_shop_ids && count($associated_shop_ids) > 1)
				$slides[$key]['is_shared'] = true;
			else
				$slides[$key]['is_shared'] = false;
		}

		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'slides' => $slides,
				'image_baseurl' => $this->_path.'images/'
			)
		);

		return $this->display(__FILE__, 'list.tpl');
	}

	public function renderAddForm()
	{
		//$images = $this->getAllImagesBySlidesId();
		if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide'))) {
			$slide = new fullpageslide((int)Tools::getValue('id_slide'));
		} else
			$slide = new fullpageslide();

		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		
		foreach (Language::getLanguages(false) as $lang) {
			if (!file_exists(dirname(__FILE__).'/images/'.$slide->image[$lang['id_lang']])) 
				$slide->image[$lang['id_lang']] = "demo.jpg";

			if (!file_exists(dirname(__FILE__).'/images/'.$slide->subimage01[$lang['id_lang']])) 
				$slide->subimage01[$lang['id_lang']] = "demo.jpg";

			if (!file_exists(dirname(__FILE__).'/images/'.$slide->subimage02[$lang['id_lang']]))
				$slide->subimage02[$lang['id_lang']] = "demo.jpg";

			if (!file_exists(dirname(__FILE__).'/images/'.$slide->subimage03[$lang['id_lang']]))
				$slide->subimage03[$lang['id_lang']] = "demo.jpg";
		}

		$effects = array();
		$css_effects = array("bounce","flash","pulse","rubberBand","shake","swing","tada","wobble","bounceIn","bounceInDown","bounceInLeft","bounceInRight","bounceInUp","bounceOut","bounceOutDown","bounceOutLeft","bounceOutRight","bounceOutUp","fadeIn","fadeInDown","fadeInDownBig","fadeInLeft","fadeInLeftBig","fadeInRight","fadeInRightBig","fadeInUp","fadeInUpBig","fadeOut","fadeOutDown","fadeOutDownBig","fadeOutLeft","fadeOutLeftBig","fadeOutRight","fadeOutRightBig","fadeOutUp","fadeOutUpBig","lightSpeedIn","lightSpeedOut","rotateIn","rotateInDownLeft","rotateInDownRight","rotateInUpLeft","rotateInUpRight","rotateOut","rotateOutDownLeft","rotateOutDownRight","rotateOutUpLeft","rotateOutUpRight","hinge","rollIn","rollOut","zoomIn","zoomInDown","zoomInLeft","zoomInRight","zoomInUp","zoomOut","zoomOutDown","zoomOutLeft","zoomOutRight","zoomOutUp","slideInDown","slideInLeft","slideInRight","slideInUp","slideOutDown","slideOutLeft","slideOutRight","slideOutUp");

		foreach ($css_effects as $key => $value) {
			$effects[$key]['id'] = $value;
			$effects[$key]['name'] = $value;
		}

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->trans('Slide information'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->trans('Enabled', array(), 'Admin.Global'),
						'name' => 'active_slide',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'file_lang',
						'label' => $this->trans('Main Image', array(), 'Modules.flexmenu.Admin'),
						'name' => 'image',
						'lang' => true,
						'thumb' => $slide->image,
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Slide title', array(), 'Modules.flexmenu.Admin'),
						'name' => 'title',
						'lang' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Target URL', array(), 'Modules.flexmenu.Admin'),
						'name' => 'url',
						'lang' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Caption', array(), 'Admin.Global'),
						'name' => 'legend',
						'lang' => true,
					),
					array(
						'type' => 'textarea',
						'label' => $this->trans('Description', array(), 'Admin.Global'),
						'name' => 'description',
						'autoload_rte' => true,
						'lang' => true,
					),
					array(
						'type' => 'select',
						'label' => $this->trans('Description Animation', array(), 'Modules.flexmenu.Admin'),
						'name' => 'text_animation',
						'options' => array(
							'query' => $effects,
							'id' => 'id',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Description Width', array(), 'Modules.flexmenu.Admin'),
						'name' => 'text_width',
						'suffix' => '%',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Text Align', array(), 'Modules.flexmenu.Admin'),
						'name' => 'text_align',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'ta_on',
								'value' => 1,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'ta_off',
								'value' => 0,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Description Delay', array(), 'Modules.flexmenu.Admin'),
						'name' => 'text_delay',
						'suffix' => 'milliseconds',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Description Speed', array(), 'Modules.flexmenu.Admin'),
						'name' => 'text_speed',
						'suffix' => 'milliseconds',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Description Position X', array(), 'Modules.flexmenu.Admin'),
						'name' => 'text_x',
						'suffix' => '%',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Description Position Y', array(), 'Modules.flexmenu.Admin'),
						'name' => 'text_y',
						'suffix' => '%',
						'lang' => false,
						'class' => 'short-input',
					),
					
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
				)
			),
		);
		$fields_form_sub_01 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->trans('SubImage 1', array(), 'Modules.flexmenu.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->trans('Enabled', array(), 'Admin.Global'),
						'name' => 'subimage01_state',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'file_lang',
						'label' => $this->trans('Image', array(), 'Admin.Global'),
						'name' => 'subimage01',
						'lang' => true,
						'thumb' => $slide->subimage01,
					),
					array(
						'type' => 'select',
						'label' => $this->trans('Animation', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage01_animation',
						'options' => array(
							'query' => $effects,
							'id' => 'id',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Delay', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage01_delay',
						'suffix' => 'milliseconds',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Speed', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage01_speed',
						'suffix' => 'milliseconds',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Position X', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage01_x',
						'suffix' => '%',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Position Y', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage01_y',
						'suffix' => '%',
						'lang' => false,
						'class' => 'short-input',
					),
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
				)
			),
		);
		$fields_form_sub_02 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->trans('SubImage 2', array(), 'Modules.flexmenu.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->trans('Enabled', array(), 'Admin.Global'),
						'name' => 'subimage02_state',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'file_lang',
						'label' => $this->trans('Image', array(), 'Admin.Global'),
						'name' => 'subimage02',
						'lang' => true,
						'thumb' => $slide->subimage02,
					),
					array(
						'type' => 'select',
						'label' => $this->trans('Animation', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage02_animation',
						'options' => array(
							'query' => $effects,
							'id' => 'id',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Delay', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage02_delay',
						'suffix' => 'milliseconds',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Speed', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage02_speed',
						'suffix' => 'milliseconds',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Position X', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage02_x',
						'suffix' => '%',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Position Y', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage02_y',
						'suffix' => '%',
						'lang' => false,
						'class' => 'short-input',
					),
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
				)
			),
		);

		$fields_form_sub_03 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->trans('SubImage 3', array(), 'Modules.flexmenu.Admin'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->trans('Enabled', array(), 'Admin.Global'),
						'name' => 'subimage03_state',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'file_lang',
						'label' => $this->trans('Image', array(), 'Admin.Global'),
						'name' => 'subimage03',
						'lang' => true,
						'thumb' => $slide->subimage03,
					),
					array(
						'type' => 'select',
						'label' => $this->trans('Animation', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage03_animation',
						'options' => array(
							'query' => $effects,
							'id' => 'id',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Delay', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage03_delay',
						'suffix' => 'milliseconds',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Speed', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage03_speed',
						'suffix' => 'milliseconds',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Position X', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage03_x',
						'suffix' => '%',
						'lang' => false,
						'class' => 'short-input',
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Position Y', array(), 'Modules.flexmenu.Admin'),
						'name' => 'subimage03_y',
						'suffix' => '%',
						'lang' => false,
						'class' => 'short-input',
					),
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
				)
			),
		);

		if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide')))
		{
			$slide = new fullpageslide((int)Tools::getValue('id_slide'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_slide');
			$fields_form['form']['images'] = $slide->image;

			$fields_form['form']['text_animation'] = $slide->text_animation;
			$fields_form['form']['text_speed'] = $slide->text_speed;
			$fields_form['form']['text_delay'] = $slide->text_delay;
			$fields_form['form']['text_width'] = $slide->text_width;
			$fields_form['form']['text_align'] = $slide->text_align;
			$fields_form['form']['text_x'] = $slide->text_x;
			$fields_form['form']['text_y'] = $slide->text_y;

			$fields_form_sub_01['form']['subimage01'] = $slide->subimage01;
			$fields_form_sub_01['form']['subimage01_state'] = $slide->subimage01_state;
			$fields_form_sub_01['form']['subimage01_animation'] = $slide->subimage01_animation;
			$fields_form_sub_01['form']['subimage01_speed'] = $slide->subimage01_speed;
			$fields_form_sub_01['form']['subimage01_delay'] = $slide->subimage01_delay;
			$fields_form_sub_01['form']['subimage01_x'] = $slide->subimage01_x;
			$fields_form_sub_01['form']['subimage01_y'] = $slide->subimage01_y;

			$fields_form_sub_02['form']['subimage02'] = $slide->subimage02;
			$fields_form_sub_02['form']['subimage02_state'] = $slide->subimage02_state;
			$fields_form_sub_02['form']['subimage02_animation'] = $slide->subimage02_animation;
			$fields_form_sub_02['form']['subimage02_speed'] = $slide->subimage02_speed;
			$fields_form_sub_02['form']['subimage02_delay'] = $slide->subimage02_delay;
			$fields_form_sub_02['form']['subimage02_x'] = $slide->subimage02_x;
			$fields_form_sub_02['form']['subimage02_y'] = $slide->subimage02_y;

			$fields_form_sub_03['form']['subimage03'] = $slide->subimage03;
			$fields_form_sub_03['form']['subimage03_state'] = $slide->subimage03_state;
			$fields_form_sub_03['form']['subimage03_animation'] = $slide->subimage03_animation;
			$fields_form_sub_03['form']['subimage03_speed'] = $slide->subimage03_speed;
			$fields_form_sub_03['form']['subimage03_delay'] = $slide->subimage03_delay;
			$fields_form_sub_03['form']['subimage03_x'] = $slide->subimage03_x;
			$fields_form_sub_03['form']['subimage03_y'] = $slide->subimage03_y;

			$has_picture = true;

			foreach (Language::getLanguages(false) as $lang)
				if (!isset($slide->image[$lang['id_lang']]))
					$has_picture &= false;

			if ($has_picture)
				$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'has_picture');
		} 
		

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitSlide';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'images/'
		);

		$helper->override_folder = '/';

		$languages = Language::getLanguages(false);
		
		if (count($languages) > 1)
			return $this->getMultiLanguageInfoMsg().$helper->generateForm(array($fields_form, $fields_form_sub_01, $fields_form_sub_02, $fields_form_sub_03));
		else
			return $helper->generateForm(array($fields_form, $fields_form_sub_01, $fields_form_sub_02, $fields_form_sub_03));
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->trans('Settings', array(), 'Admin.Global'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->trans('Speed', array(), 'Modules.flexmenu.Admin'),
						'name' => 'FPS_SPEED',
						'suffix' => 'milliseconds',
						'class' => 'short-input',
						'desc' => $this->trans('The duration of the transition between two slides', array(), 'Modules.flexmenu.Admin')
					),
					array(
						'type' => 'text',
						'label' => $this->trans('Pause', array(), 'Modules.flexmenu.Admin'),
						'name' => 'FPS_PAUSE',
						'suffix' => 'milliseconds',
						'class' => 'short-input',
						'desc' => $this->trans('The delay between two slides', array(), 'Modules.flexmenu.Admin')
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Auto play', array(), 'Modules.flexmenu.Admin'),
						'name' => 'FPS_LOOP',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->trans('Enabled', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->trans('Disabled', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Navigation', array(), 'Modules.flexmenu.Admin'),
						'name' => 'FPS_NAV',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->trans('Show', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->trans('Hide', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Navigation Position', array(), 'Modules.flexmenu.Admin'),
						'name' => 'FPS_NAVPOS',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->trans('Left', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->trans('Right', array(), 'Admin.Global')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->trans('Infinite Scroll', array(), 'Modules.flexmenu.Admin'),
						'name' => 'FPS_INFINITE',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->trans('Yes', array(), 'Admin.Global')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->trans('No', array(), 'Admin.Global')
							)
						),
					)
				),
				'submit' => array(
					'title' => $this->trans('Save', array(), 'Admin.Actions'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitSlider';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		$id_shop_group = Shop::getContextShopGroupID();
		$id_shop = Shop::getContextShopID();

		return array(
			'FPS_SPEED' => Tools::getValue('FPS_SPEED', Configuration::get('FPS_SPEED', null, $id_shop_group, $id_shop)),
			'FPS_PAUSE' => Tools::getValue('FPS_PAUSE', Configuration::get('FPS_PAUSE', null, $id_shop_group, $id_shop)),
			'FPS_LOOP' => Tools::getValue('FPS_LOOP', Configuration::get('FPS_LOOP', null, $id_shop_group, $id_shop)),
			'FPS_NAV' => Tools::getValue('FPS_NAV', Configuration::get('FPS_NAV', null, $id_shop_group, $id_shop)),
			'FPS_NAVPOS' => Tools::getValue('FPS_NAVPOS', Configuration::get('FPS_NAVPOS', null, $id_shop_group, $id_shop)),
			'FPS_INFINITE' => Tools::getValue('FPS_INFINITE', Configuration::get('FPS_INFINITE', null, $id_shop_group, $id_shop)),
		);
	}

	public function getAddFieldsValues()
	{
		$fields = array();

		if (Tools::isSubmit('id_slide') && $this->slideExists((int)Tools::getValue('id_slide')))
		{
			$slide = new fullpageslide((int)Tools::getValue('id_slide'));
			$fields['id_slide'] = (int)Tools::getValue('id_slide', $slide->id);
		}
		else
			$slide = new fullpageslide();
//		print_r($slide);

		$fields['active_slide'] = Tools::getValue('active_slide', $slide->active);

		$fields['text_animation'] = Tools::getValue('text_animation', $slide->text_animation);
		$fields['text_width'] = Tools::getValue('text_width', $slide->text_width);
		$fields['text_align'] = Tools::getValue('text_align', $slide->text_align);
		$fields['text_delay'] = Tools::getValue('text_delay', $slide->text_delay);
		$fields['text_speed'] = Tools::getValue('text_speed', $slide->text_speed);
		$fields['text_x'] = Tools::getValue('text_x', $slide->text_x);
		$fields['text_y'] = Tools::getValue('text_y', $slide->text_y);

		$fields['subimage01_state'] = Tools::getValue('subimage01_state', $slide->subimage01_state);
		$fields['subimage01_animation'] = Tools::getValue('subimage01_animation', $slide->subimage01_animation);
		$fields['subimage01_delay'] = Tools::getValue('subimage01_delay', $slide->subimage01_delay);
		$fields['subimage01_speed'] = Tools::getValue('subimage01_speed', $slide->subimage01_speed);
		$fields['subimage01_x'] = Tools::getValue('subimage01_x', $slide->subimage01_x);
		$fields['subimage01_y'] = Tools::getValue('subimage01_y', $slide->subimage01_y);

		$fields['subimage02_state'] = Tools::getValue('subimage02_state', $slide->subimage02_state);
		$fields['subimage02_animation'] = Tools::getValue('subimage02_animation', $slide->subimage02_animation);
		$fields['subimage02_delay'] = Tools::getValue('subimage02_delay', $slide->subimage02_delay);
		$fields['subimage02_speed'] = Tools::getValue('subimage02_speed', $slide->subimage02_speed);
		$fields['subimage02_x'] = Tools::getValue('subimage02_x', $slide->subimage02_x);
		$fields['subimage02_y'] = Tools::getValue('subimage02_y', $slide->subimage02_y);

		$fields['subimage03_state'] = Tools::getValue('subimage03_state', $slide->subimage03_state);
		$fields['subimage03_animation'] = Tools::getValue('subimage03_animation', $slide->subimage03_animation);
		$fields['subimage03_delay'] = Tools::getValue('subimage03_delay', $slide->subimage03_delay);
		$fields['subimage03_speed'] = Tools::getValue('subimage03_speed', $slide->subimage03_speed);
		$fields['subimage03_x'] = Tools::getValue('subimage03_x', $slide->subimage03_x);
		$fields['subimage03_y'] = Tools::getValue('subimage03_y', $slide->subimage03_y);

		$fields['has_picture'] = true;

		$languages = Language::getLanguages(false);

		foreach ($languages as $lang)
		{
			$fields['image'][$lang['id_lang']] = Tools::getValue('image_'.(int)$lang['id_lang']);
			$fields['subimage01'][$lang['id_lang']] = Tools::getValue('subimage01_'.(int)$lang['id_lang']);
			$fields['subimage02'][$lang['id_lang']] = Tools::getValue('subimage02_'.(int)$lang['id_lang']);
			$fields['subimage03'][$lang['id_lang']] = Tools::getValue('subimage03_'.(int)$lang['id_lang']);
			$fields['title'][$lang['id_lang']] = Tools::getValue('title_'.(int)$lang['id_lang'], $slide->title[$lang['id_lang']]);
			$fields['url'][$lang['id_lang']] = Tools::getValue('url_'.(int)$lang['id_lang'], $slide->url[$lang['id_lang']]);
			$fields['legend'][$lang['id_lang']] = Tools::getValue('legend_'.(int)$lang['id_lang'], $slide->legend[$lang['id_lang']]);
			$fields['description'][$lang['id_lang']] = Tools::getValue('description_'.(int)$lang['id_lang'], $slide->description[$lang['id_lang']]);
		}

		return $fields;
	}

	private function getMultiLanguageInfoMsg()
	{
		return '<p class="alert alert-warning">'.
					$this->trans('Since multiple languages are activated on your shop, please mind to upload your image for each one of them', array(), 'Modules.flexmenu.Admin').
				'</p>';
	}

	private function getWarningMultishopHtml()
	{
		if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL)
			return '<p class="alert alert-warning">'.
						$this->trans('You cannot manage slides items from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit', array(), 'Modules.flexmenu.Admin').
					'</p>';
		else
			return '';
	}

	private function getShopContextError($shop_contextualized_name, $mode)
	{
		if (is_array($shop_contextualized_name))
			$shop_contextualized_name = implode('<br/>', $shop_contextualized_name);

		if ($mode == 'edit')
			return '<p class="alert alert-danger">'.
							sprintf($this->trans('You can only edit this slide from the shop(s) context: %s', array(), 'Modules.flexmenu.Admin'), $shop_contextualized_name).
					'</p>';
		else
			return '<p class="alert alert-danger">'.
							sprintf($this->trans('You cannot add slides from a "All Shops" or a "Group Shop" context', array(), 'Modules.flexmenu.Admin')).
					'</p>';
	}

	private function getShopAssociationError($id_slide)
	{
		return '<p class="alert alert-danger">'.
						sprintf($this->trans('Unable to get slide shop association information (id_slide: %d)', array(), 'Modules.flexmenu.Admin'), (int)$id_slide).
				'</p>';
	}


	private function getCurrentShopInfoMsg()
	{
		$shop_info = null;

		if (Shop::isFeatureActive())
		{
			if (Shop::getContext() == Shop::CONTEXT_SHOP)
				$shop_info = sprintf($this->trans('The modifications will be applied to shop: %s', array(), 'Modules.flexmenu.Admin'), $this->context->shop->name);
			else if (Shop::getContext() == Shop::CONTEXT_GROUP)
				$shop_info = sprintf($this->trans('The modifications will be applied to this group: %s', array(), 'Modules.flexmenu.Admin'), Shop::getContextShopGroup()->name);
			else
				$shop_info = $this->trans('The modifications will be applied to all shops and shop groups', array(), 'Modules.flexmenu.Admin');

			return '<div class="alert alert-info">'.
						$shop_info.
					'</div>';
		}
		else
			return '';
	}

	private function getSharedSlideWarning()
	{
		return '<p class="alert alert-warning">'.
					$this->trans('This slide is shared with other shops! All shops associated to this slide will apply modifications made here', array(), 'Modules.flexmenu.Admin').
				'</p>';
	}

	public function getModuleState($hook)	{  // get module state from database
		if (Module::isInstalled('pk_themesettings')) {
			if (!$sett = Db::getInstance()->ExecuteS('SELECT value FROM `'._DB_PREFIX_.'pk_theme_settings_hooks` WHERE hook = "'.$hook.'" AND module = "'.$this->name.'" AND id_shop = '.(int)$this->context->shop->id.';')) 
				return false;		
			return $sett[0]["value"];
		} else {
			return true;
		} 
	}
}
