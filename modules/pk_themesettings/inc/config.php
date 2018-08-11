<?php

class Pk_ThemeSettings_Config extends Module {

	public function getOptionsArray() {

		$helper = new configHelper();

		$pk_options = array(
			'general' => array(
				'title' => "General",
				'icon' => "general",
				'options_list' => array(
					array(
						'type' => 'separator',
						'label' => $this->trans('Buttons', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'gs_body_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => 'body',
						'label' => $this->trans('General Typography', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'gs_titles_font',
						'type' => 'select',
						'input_type' => '',
						'css_rule' => 'font-family',
						'output' => 'h2, h3, h4, h5, h6',
						'label' => $this->trans('Titles Font', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => 'Arial',
		                'options' => $helper->fontslist()
					),
					array(
						'name' => 'button_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => 'body .btn, .bt_compare',
						'label' => $this->trans('Button Typography', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'button_border_color',
						'type' => 'color',
						'input_type' => 'color',
						'output' => 'body .btn, .bt_compare',
						'css_rule' => 'border-color',
						'label' => $this->trans('Button Border Color', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#313537',
					),
					array(
						'name' => 'button_color',
						'type' => 'color',
						'input_type' => 'color',
						'output' => 'body .btn, .bt_compare',
						'css_rule' => 'background-color',
						'label' => $this->trans('Button Color', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#313537',
					),
					array(
						'name' => 'button_text_color_hover',
						'type' => 'color',
						'input_type' => 'color',
						'output' => 'body .btn:hover, .bt_compare:hover',
						'css_rule' => 'color',
						'label' => $this->trans('Button Text Color on Hover', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#666666',
					),
					array(
						'name' => 'button_color_hover',
						'type' => 'color',
						'input_type' => 'color',
						'output' => 'body .btn:hover, .bt_compare:hover',
						'css_rule' => 'background-color',
						'label' => $this->trans('Button Color on Hover', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#666666',
					),
					array(
						'name' => 'button_border_color_hover',
						'type' => 'color',
						'input_type' => 'color',
						'output' => 'body .btn:hover, .bt_compare:hover',
						'css_rule' => 'border-color',
						'label' => $this->trans('Button Border Color on Hover', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#666666',
					),
					array(
						'type' => 'separator',
						'label' => $this->trans('Common Settings', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'type' => 'radio',
						'name' => 'toTop',
						'label' => $this->trans('"Scroll to Top" Button', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Show',
							'0' => 'Hide'
						)
					),
					array(
						'name' => 'page_width',
						'type' => 'input',
						'input_type' => 'number',
						'output' => '#pattern .page-width, .menu_width, .flexmenu_ul',
						'css_rule' => 'max-width',
						'label' => $this->trans('Page Width', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '36',
					),
					array(
						'type' => 'radio',
						'name' => 'latin_ext',
						'label' => $this->trans('Use Latin Extended Symbols', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'type' => 'radio',
						'name' => 'cyrillic',
						'label' => $this->trans('Use Cyrillic Symbols', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'type' => 'radio',
						'name' => 'gs_cookie_message',
						'label' => $this->trans('Show Message about cookies', array(), 'Modules.ThemeSettings.Admin').' (<a target="_blank" href="http://www.cookielaw.org/the-cookie-law/">For EU shops</a>)',
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'type' => 'radio',
						'name' => 'gs_sticky_menu',
						'label' => $this->trans('Sticky Menu', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'gs_google_api_key',
						'type' => 'input',
						'input_type' => 'text',
						'label' => '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">'.$this->trans('Google API Key', array(), 'Modules.ThemeSettings.Admin').'</a>',
						'default' => '',
					),
					array(
						'name' => 'gs_theme_updates',
						'type' => 'theme_update',
						'label' => $this->trans('Theme Updates', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => '',
		                'options' => $helper->checkupdates()
					),
				),
			),
			'presets' => array(
				'title' => "Presets",
				'icon' => "preset",
				'options_list' => array(
					array(
						'name' => 'preset',
						'type' => 'radio_image',
		                'options' => $helper->presets()
					),
				),
			),
			'logo' => array(
				'title' => "Logo",
				'icon' => "logo",
				'options_list' => array(
					array(
						'name' => 'logo_position',
						'type' => 'radio',
						'label' => $this->trans('Align Logo', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'logo-center',
						'class' => 'both_act',
						'options' => array(
							'logo-left' => 'Left',
							'logo-center' => 'Center'
						)
					),
					array(
						'name' => 'logo_type',
						'type' => 'radio',
						'label' => $this->trans('Logo Type', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'text',
						'class' => 'both_act',
						'options' => array(
							'text' => 'Text',
							'image' => 'Image'
						)
					),
					array(
						'name' => 'logo_text',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Logo Text', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'Alysum',
					),
					array(
						'name' => 'logo_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '#header span.logo',
						'label' => $this->trans('Logo Typography', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
				),
			),
			'header' => array(
				'title' => "Header",
				'icon' => "header",
				'options_list' => array(
					array(
						'type' => 'separator',
						'label' => $this->trans('General', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'header_type',
						'type' => 'select',
						'label' => $this->trans('Header Type', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 1,
						'options' => array(
							'1' => 'Header #1',
							'2' => 'Header #2',
							'3' => 'Header #3',
							'4' => 'Header #4',
							'5' => 'Header #5',
						)
					),
					array(
						'name' => 'header_position',
						'type' => 'radio',
						'label' => $this->trans('Header Position Absolute (Homepage Only)', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'header_static',
						'class' => 'both_act',
						'options' => array(
							'header_absolute' => 'Yes',
							'header_static' => 'No'
						)
					),
					array(
						'type' => 'separator',
						'label' => $this->trans('Top Bar', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'top_bar',
						'type' => 'radio',
						'label' => $this->trans('Display Top Bar', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'top_bar_short_message',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Top Bar Short Message', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'Now Shipping to Canada',
					),
					array(
						'name' => 'top_bar_background',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.header-top',
						'css_rule' => 'background-color',
						'label' => $this->trans('Top Bar Background', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#EEEEEE',
					),
					array(
						'name' => 'top_bar_height',
						'type' => 'input',
						'input_type' => 'number',
						'output' => '.header-top > .page-width',
						'css_rule' => 'height',
						'label' => $this->trans('Top Bar Height', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '36',
					),
					array(
						'name' => 'top_bar_text',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.header-top',
						'label' => $this->trans('Top Bar Typography', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'type' => 'separator',
						'label' => $this->trans('Middle Header', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'middle_bar_height',
						'type' => 'input',
						'input_type' => 'number',
						'output' => '.header-main > .page-width',
						'css_rule' => 'height',
						'label' => $this->trans('Middle Header Height', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '150',
					),
					array(
						'name' => 'hdr_middle_bar_background',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.header-main',
						'css_rule' => 'background-color',
						'label' => $this->trans('Header Background', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'transparent',
					),
					array(
						'name' => 'middle_bar_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.header-main',
						'label' => $this->trans('Middle Bar Typography', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'type' => 'separator',
						'label' => $this->trans('Flex Menu', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'menu_bar_height',
						'type' => 'input',
						'input_type' => 'number',
						'output' => '.flexmenu-container',
						'css_rule' => 'height',
						'label' => $this->trans('Menu Bar Height', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'auto',
					),
					array(
						'name' => 'menu_background',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.flexmenu-container',
						'css_rule' => 'background-color',
						'label' => $this->trans('Menu Background', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'transparent',
					),
					array(
						'name' => 'menu_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.flexmenuitem > a',
						'label' => $this->trans('Top Bar Typography', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'submenu_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.submenu',
						'label' => $this->trans('Menu Items Typography', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'type' => 'separator',
						'label' => $this->trans('Search Bar', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'hdr_search_bar_background',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '#header #search_widget input[type="text"]',
						'css_rule' => 'background-color',
						'label' => $this->trans('Search Bar Background', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#ffffff',
					),
					array(
						'name' => 'hdr_search_bar_border',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '#header #search_widget input[type="text"]',
						'css_rule' => 'border-color',
						'label' => $this->trans('Search Bar Border', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#f2f2f2',
					),
				),
			),
			'footer' => array(
				'title' => "Footer",
				'icon' => "footer",
				'options_list' => array(
					array(
						'type' => 'separator',
						'label' => $this->trans('Footer Main', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'footer_main_background',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.footer-main',
						'css_rule' => 'background-color',
						'label' => $this->trans('Footer Background', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#323232',
					),
					array(
						'name' => 'footer_main_font',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.footer-main',
						'label' => $this->trans('Main Footer Font', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'footer_main_title',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.footer-main h4',
						'label' => $this->trans('Main Footer Title', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'footer_main_link_color',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '#footer .footer-main a',
						'css_rule' => 'color',
						'label' => $this->trans('Footer Links Color', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#f2f2f2',
					),
					array(
						'type' => 'separator',
						'label' => $this->trans('Footer Bottom', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'footer_bottom',
						'type' => 'radio',
						'label' => $this->trans('Display Footer Bottom', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'footer_bottom_align',
						'type' => 'radio',
						'css_rule' => 'justify-content',
						'output' => '.footer-bottom .page-width',
						'label' => $this->trans('Align Footer Elements', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'flex-start',
						'options' => array(
							'flex-start' => 'Left',
							'center' => 'Center'
						)
					),
					array(
						'name' => 'footer_bottom_text',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Footer Bottom Message', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
					array(
						'name' => 'footer_bottom_social',
						'type' => 'radio',
						'label' => $this->trans('Display Icons of Social Networks', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'footer_bottom_pcards',
						'type' => 'radio',
						'label' => $this->trans('Display Accepted Payments Systems', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'footer_bottom_background',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.footer-bottom',
						'css_rule' => 'background-color',
						'label' => $this->trans('Footer Bottom Background', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#323232',
					),
					array(
						'name' => 'footer_bottom_font',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.footer-bottom',
						'label' => $this->trans('Bottom Footer Font', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'footer_bottom_height',
						'type' => 'input',
						'input_type' => 'number',
						'output' => '.footer-bottom',
						'css_rule' => 'height',
						'label' => $this->trans('Footer Bottom Height', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '80',
					),
				),
			),
			'homepage' => array(
				'title' => "Home Page",
				'icon' => "home",
				'options_list' => array(
					array(
						'type' => 'separator',
						'label' => $this->trans('Layouts', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'homepage_layout',
						'type' => 'select',
						'input_type' => '',
						'label' => $this->trans('Homepage Layout', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => '',
		                'options' => $helper->getLayouts()
					),
					array(
						'type' => 'separator',
						'label' => $this->trans('Typography', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'homepage_module_title',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.module-title',
						'label' => $this->trans('Home Page Module Title', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
				),
			),
			'category_page' => array(
				'title' => "Category Page",
				'icon' => "category",
				'options_list' => array(
					array(
						'name' => 'cp_listing_view',
						'type' => 'radio',
						'label' => $this->trans('Product Listing', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'grid',
						'options' => array(
							'grid' => 'Grid',
							'list' => 'List'
						)
					),
					array(
						'name' => 'cp_category_preview',
						'type' => 'radio',
						'label' => $this->trans('Show Category Image', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'grid',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'cp_category_description',
						'type' => 'radio',
						'label' => $this->trans('Show Category Description', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'grid',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'cp_only_filter',
						'type' => 'radio',
						'label' => $this->trans('Show Only Filter in Sidebar', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
				),
			),
			'product_page' => array(
				'title' => "Product Page",
				'icon' => "product-page",
				'options_list' => array(
					array(
						'name' => 'pp_price',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '#product .price',
						'label' => $this->trans('Product Price', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'pp_share',
						'type' => 'radio',
						'label' => $this->trans('Show Share Buttons', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
				),
			),
			'product_miniature' => array(
				'title' => "Product Miniature",
				'icon' => "product",
				'options_list' => array(
					array(
						'name' => 'pm_qw_button',
						'type' => 'radio',
						'label' => $this->trans('Display Quick View Button', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_colors',
						'type' => 'radio',
						'label' => $this->trans('Display Color Options', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_labels',
						'type' => 'radio',
						'label' => $this->trans('Display Labels', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_countdown',
						'type' => 'radio',
						'label' => $this->trans('Display Countdown Timer', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_hover_image',
						'type' => 'radio',
						'label' => $this->trans('Second Image on Hover', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_button_color',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.product-thumbnail .btn',
						'css_rule' => 'background-color',
						'label' => $this->trans('Buttons Color', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#313537',
					),
					array(
						'name' => 'pm_button_color_hover',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.product-thumbnail .btn:hover',
						'css_rule' => 'background-color',
						'label' => $this->trans('Buttons Hover Color', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#313537',
					),
					array(
						'name' => 'pm_title',
						'type' => 'radio',
						'label' => $this->trans('Display Product Title', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_title_multiline',
						'type' => 'radio',
						'label' => $this->trans('Product Title Multiline', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_title_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.product-title',
						'label' => $this->trans('Product Title Font', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'pm_brand',
						'type' => 'radio',
						'label' => $this->trans('Display Product Manufacturer', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_brand_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.product-brand',
						'label' => $this->trans('Manufacturer Title Font', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'pm_desc',
						'type' => 'radio',
						'label' => $this->trans('Display Product Description', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_desc_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.product-description-short',
						'label' => $this->trans('Product Description Font', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'pm_price',
						'type' => 'radio',
						'label' => $this->trans('Display Product Price', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_price_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.price',
						'label' => $this->trans('Product Price Font', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'pm_old_price_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.regular-price',
						'label' => $this->trans('Product Regular Price Font', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
					array(
						'name' => 'pm_details_layout',
						'type' => 'select',
						'input_type' => '',
						'label' => $this->trans('Product Details Layout', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => '1',
		                'options' => array(
		                	'pm_details_layout1' => 'Style 1',
		                	'pm_details_layout2' => 'Style 2',
		                	'pm_details_layout3' => 'Style 3',
		                ),
					),
					array(
						'type' => 'separator',
						'label' => $this->trans('Rating Stars', array(), 'Modules.ThemeSettings.Admin'),
					),
					array(
						'name' => 'pm_stars',
						'type' => 'radio',
						'label' => $this->trans('Display Product Rating', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pm_stars_color',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.max-rating .svgic',
						'css_rule' => 'color',
						'label' => $this->trans('Default Stars Color', array(), 'Modules.ThemeSettings.Admin'),
						'default' => 'transparent',
					),
					array(
						'name' => 'pm_stars_color_active',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.cut-stars .svgic',
						'css_rule' => 'color',
						'label' => $this->trans('Active Stars Color', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#afafaf',
					),
					array(
						'name' => 'pm_labels_color',
						'type' => 'color',
						'input_type' => 'color',
						'output' => '.product-miniature .product-flags li',
						'css_rule' => 'background-color',
						'label' => $this->trans('Labels Color', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '#313537',
					),
					array(
						'name' => 'pm_labels_typography',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.product-miniature .product-flags li',
						'label' => $this->trans('Labels Typography', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
				),
			),
			'sidebar' => array(
				'title' => "Sidebar",
				'icon' => "sidebar",
				'options_list' => array(
					array(
						'name' => 'sb_title',
						'type' => 'typography',
						'input_type' => '',
						'css_rule' => '',
						'output' => '.sidebar .module-title',
						'label' => $this->trans('Sidebar Modules Titles', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => $helper->typography_defaults(),
		                'options' => $helper->typography_options()
					),
				),
			),
			'social_accounts' => array(
				'title' => "Social Accounts",
				'icon' => "facebook",
				'options_list' => array(
					array(
						'name' => 'sa_facebook',
						'type' => 'radio',
						'label' => $this->trans('Facebook', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'sa_facebook_link',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Facebook Account', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
					array(
						'name' => 'sa_twitter',
						'type' => 'radio',
						'label' => $this->trans('Twitter', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'sa_twitter_link',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Twitter Account', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
					array(
						'name' => 'sa_pinterest',
						'type' => 'radio',
						'label' => $this->trans('Pinterest', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'sa_pinterest_link',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Pinterest Account', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
					array(
						'name' => 'sa_linkedin',
						'type' => 'radio',
						'label' => $this->trans('Linkedin', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'sa_linkedin_link',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Linkedin Account', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
					array(
						'name' => 'sa_instagram',
						'type' => 'radio',
						'label' => $this->trans('Instagram', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'sa_instagram_link',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Instagram Account', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
					array(
						'name' => 'sa_flickr',
						'type' => 'radio',
						'label' => $this->trans('Flickr', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'sa_flickr_link',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Flickr Account', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
					array(
						'name' => 'sa_gplus',
						'type' => 'radio',
						'label' => $this->trans('Google Plus', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'sa_gplus_link',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Google Plus Account', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
					array(
						'name' => 'sa_youtube',
						'type' => 'radio',
						'label' => $this->trans('Youtube', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'sa_youtube_link',
						'type' => 'input',
						'input_type' => 'text',
						'label' => $this->trans('Youtube Account', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
				),
			),
			'payment_icons' => array(
				'title' => "Payment Icons",
				'icon' => "paypal",
				'options_list' => array(
					array(
						'name' => 'pay_paypal',
						'type' => 'radio',
						'label' => $this->trans('PayPal', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_skrill',
						'type' => 'radio',
						'label' => $this->trans('Skrill', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_visa',
						'type' => 'radio',
						'label' => $this->trans('Visa', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_am_exp',
						'type' => 'radio',
						'label' => $this->trans('American Express', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_mastercard',
						'type' => 'radio',
						'label' => $this->trans('Mastercard', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_maestro',
						'type' => 'radio',
						'label' => $this->trans('Maestro', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_discover',
						'type' => 'radio',
						'label' => $this->trans('Discover', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_cirrus',
						'type' => 'radio',
						'label' => $this->trans('Cirrus', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_direct',
						'type' => 'radio',
						'label' => $this->trans('Direct', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_solo',
						'type' => 'radio',
						'label' => $this->trans('Solo', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_switch',
						'type' => 'radio',
						'label' => $this->trans('Switch', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'pay_wu',
						'type' => 'radio',
						'label' => $this->trans('Western Union', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
				),
			),
			'maintenance' => array(
				'title' => "Maintenance Mode",
				'icon' => "maintenance",
				'options_list' => array(
					array(
						'name' => 'mt_maintenance',
						'type' => 'radio',
						'label' => $this->trans('Enable Shop', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '0',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'mt_countdown',
						'type' => 'radio',
						'label' => $this->trans('Show Countdown', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'mt_notify',
						'type' => 'radio',
						'label' => $this->trans('Show Form', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '1',
						'options' => array(
							'1' => 'Yes',
							'0' => 'No'
						)
					),
					array(
						'name' => 'mt_date_until',
						'type' => 'date',
						'label' => $this->trans('Until Date', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '01/01/2020',
					),
					array(
						'name' => 'mt_sendnotification',
						'type' => 'button',
						'label' => $this->trans('Send Notification to Subscribers', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
						'options' => ''
					),
				),
			),
			'demo_content' => array(
				'title' => "Demo Content",
				'icon' => "import",
				'options_list' => array(
					array(
						'name' => 'dc_preset_to_import',
						'type' => 'select_and_submit',
						'input_type' => '',
						'label' => $this->trans('Select demo to import', array(), 'Modules.ThemeSettings.Admin'),
						'default'  => 'alysum',
		                'options' => $helper->presets()
					),
				),
			),
			'customer_css' => array(
				'title' => "Customer CSS",
				'icon' => "css",
				'options_list' => array(
					array(
						'name' => 'customer_css',
						'type' => 'textarea',
						'code' => 1,
						'label' => $this->trans('CSS Styles', array(), 'Modules.ThemeSettings.Admin'),
						'default' => '',
					),
				),
			),
		);
		return $pk_options;
	}	

}