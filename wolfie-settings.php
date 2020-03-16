<?php
/*
Plugin Name: Wolfie settings
Plugin URI: https://github.com/CommanderxDoge/wolfie-settings
Description: This plugin adds settings and custom fields and can easly create pages
Version: 1.0
Author: Paweł Witek
Author URI: https://github.com/CommanderxDoge/
Text Domain: ws
License: MIT
*/

define( 'WS_PLUGIN_URL', plugin_dir_url(__FILE__));

class Wolfie_settings {
	public $settings;
	public $settingsArray;
	public function __construct( ) {
		add_action('admin_enqueue_scripts', array( $this , 'wolfie_enqueue_admin_scripts' ));
	}
	public function wolfie_enqueue_admin_scripts() {
		wp_register_script('wolfie-image-picker', plugin_dir_url(__FILE__) . '/assets/js/wolfie-image-picker.js', array('jquery'));
		wp_register_script('wolfie-file-picker', plugin_dir_url(__FILE__) . '/assets/js/wolfie-file-picker.js', array('jquery'));
		wp_register_script('wolfie-gallery-picker', plugin_dir_url(__FILE__) . '/assets/js/wolfie-gallery.js', array('jquery'));
		wp_register_script('jquery-sortable', plugin_dir_url(__FILE__) . '/assets/js/jquery-ui.min.js', array('jquery','wolfie-gallery-picker'));
		wp_register_script('wolfie-colorpicker-alpha-js', plugin_dir_url(__FILE__) . '/assets/js/wp-color-picker-alpha.min.js', array('jquery','wp-color-picker'), false, true );
		wp_register_style('wolfie-settings-css', plugin_dir_url(__FILE__) . '/assets/css/wolfie-settings.css');
		//enqueue everywhere scripts
		wp_enqueue_script('wolfie-admin-js', plugin_dir_url(__FILE__) . '/assets/js/wolfie.js');
		wp_enqueue_style( 'wp-color-picker' ); 
		wp_enqueue_style('wolfie-admin-css', plugin_dir_url(__FILE__) . '/assets/css/admin.css');
	}
	public function register_settings() {
		register_setting( $this->settings, $this->settings );
	}
	public function printSettings() {
		echo '<pre>';
		print_r( $this->settingsArray );
		echo '</pre>';
	}
	public function setSettings($newSettings='wolfie_settings') {
		$this->settings = $newSettings;
		$this->settingsArray = get_option($newSettings);
		add_action('admin_init', function() use($newSettings)  {
			register_setting( $newSettings, $newSettings );	
		});
	}
	public function startForm() {
		echo '<form method="post" action="options.php">';
		settings_fields( $this->settings ); 
		do_settings_sections( $this->settings );
	}
	public function endForm(){
		submit_button();
		echo '</form>';
	}
	public function imagePicker($name, $label=null, $print=false){
		include( plugin_dir_path( __FILE__ ) . '/inc/custom_fields/image-picker.php');
		if($print === true) {
			echo $content;
		}
		return $content;
	}
	public function filePicker($name, $label=null, $print=false){
		include( plugin_dir_path( __FILE__ ) . '/inc/custom_fields/file-picker.php');
		if($print === true) {
			echo $content;
		}
		return $content;
	}
	public function textPicker($name, $label=null, $print=false){
		include( plugin_dir_path( __FILE__ ) . '/inc/custom_fields/text-picker.php');
		if($print === true) {
			echo $content;
		}
		return $content;
	}
	public function colorPicker($name, $label=null, $print=false){
		include( plugin_dir_path( __FILE__ ) . '/inc/custom_fields/color-picker.php');
		if($print === true) {
			echo $content;
		}
		return $content;
	}
	public function galleryPicker($name, $label=null, $print=false) {
		include( plugin_dir_path( __FILE__ ) . '/inc/custom_fields/gallery-picker.php');
		if($print === true) {
			echo $content;
		}
		return $content;
	}	
}
$ws = new Wolfie_settings();
$ws->setSettings('wolfie_settings');
global $ws;

class Wolfie_page {
	public $args; 	  						//its array
	public $pageName; 						//its string
	public $customFields; 					//its array
	public $dashicon;						//its string
	public $settings='wolfie_settings'; 	//its string
	public function setSettings($settings){
		$this->settings = $settings;
	}
	public function setFields($customFields) {
		$this->customFields = $customFields;
	}
	public function setPage($pageName, $args, $dashicon=''){
		include( plugin_dir_path( __FILE__ ) . '/inc/helpers/helpers.php');
		if(!empty($args['settings'])){
			$this->settings = $args['settings'];
		} else  {
			$this->settings = 'wolfie_settings';
		}
		//this is main scope here settings should be registered but not fcking working
		$wolfie_page_settings = new Wolfie_settings();
		$wolfie_page_settings->setSettings('wolfie_settings');

		$this->pageName = $pageName;
		$this->dashicon = $dashicon;
		$this->args = $args;
		add_action( 'admin_menu', function(){
			$pageName = $this->pageName;
			$dashicon = $this->dashicon;
			$customFields = $this->customFields;
			$args = $this->args;
			if(empty( $args['dashicon']) && isset($this->dashicon) ) {
				$dashicon = '';
			} else {
				$dashicon = $args['dashicon'];
			}
			if(empty( $args['settings']) && isset($this->settings) ) {
				$settings = 'wolfie_settings';
			} else {
				$settings = $args['settings'];
			}
			//here register settings if settings are set
			$ws = new Wolfie_settings();
			$ws->setSettings($settings);
			$fn = function() use($args, $ws, $pageName){ 
				$customFields = is_fields($args);
				$unified = unifyString($args['page_name']);
				$first = true;
				do_action('wolfie_page_' . $unified);
				if(isset($args['page_body']) && $args['page_body'] === false) 
					return;
				// set content to tabs
				if(is_fields($args)) {
					$tabs = [
						'general' => [],
					];
					$i = 0;
					foreach ($customFields as $index => $array) {
						$tab_name = (isset($array['tab'])) ? $array['tab'] : 'general' ;
						if($tab_name) {
							if($array['type'] === 'image'){
								$field = $ws->imagePicker($array['name'], $array['desc']);
							} elseif($array['type'] === 'gallery') {
								$field = $ws->galleryPicker($array['name'], $array['desc']);
							} elseif($array['type'] === 'file') {
								$field = $ws->filePicker($array['name'], $array['desc']);
							} elseif($array['type'] === 'text') {
								$field = $ws->textPicker($array['name'], $array['desc']);
							} elseif($array['type'] === 'color') {
								$field = $ws->colorPicker($array['name'], $array['desc']);
							} else {
								$field = '';
							}
							$tabs[$tab_name][$i] = $field;
							$i++;
						}
					}
				}
				//set html on page
				echo '<div class="wolfie-container">';
				echo '<h1>'. $pageName .'</h1>';
				echo '<div class="wolfie-row">';
				if(is_tabs($args)) {					
					echo '<div class="wolfie-col col-2">';
					if(!empty($customFields)) {						
						echo '<ul class="wolfie-tabs">';
						foreach($tabs as $tab_name => $arr ) {
							$active = ($first) ? 'active' : '' ;
							$tab_name_u = unifyString($tab_name);
							echo '<li class="'.$tab_name.' '.$active.'" data-tab="wolfie_'.$tab_name_u.'">';
							echo $tab_name;
							echo '</li>';
							$first = false;
						}
						echo '</ul>';
					}
					echo '</div>';
				}
				echo '<div class="wolfie-col col-7">';
				echo '<div class="wolfie-settings">';
				$ws->startForm();
				if(is_fields($args)) {
					foreach($tabs as $tab_name => $arr ) {
						$active = ($first) ? 'active' : '' ;
						$tab_name_u = unifyString($tab_name);
						echo '<div class="wolfie_tab_container wolfie_'.$tab_name_u.' '.$active.'">';
						if(is_tabs($args)) {
							echo '<h2 class="tab-title">' . $tab_name . '</h2>';
						}
						foreach ($arr as $index => $field) {
							echo $field;
						}
						echo '</div>';
						$first = false;
					}
				}
				$ws->endForm();
				echo '</div>';
				echo '</div>';
				echo '<div class="wolfie-col col-3">'; ?>
				<div class="wolfie-information">
					<div class="box">
						<div class="wolfie-header">
							<div class="wolfie-row">
								<div class="quote">
									<i class="fa fa-quote-right" aria-hidden="true"></i>
									<p>Standing on the giants shoulders, let you see more!</p>
								</div><!-- /quote -->
								<div class="owner-wrapper" style="width:80px;height:80px;">
									<img class="owner" src="<?php echo WS_PLUGIN_URL . 'assets/img/pawel-witek.jpg' ?>">
								</div><!-- /owner-wrapper -->
							</div><!-- /wolfie-row -->
							<p style="text-align: right;">Paweł Witek CEO at <a href="https://wolfiesites.com">wolfiesites.com</a></p>
						</div><!-- /header -->
					</div><!-- /box -->
				</div><!-- /wolfie-main-options -->
				<?php
				do_action('wolfie_information');
				echo '</div>';
				echo '</div>';
				echo '</div>';
			};
			add_menu_page( 
				__( $pageName, 'wolfie_settings' ),
				$pageName,
				'manage_options',
				$pageName,
				$fn,
				$dashicon,
				99
			); 
		} );
	}
}

$pw = new Wolfie_page();
$args = [
	'page_name' => 'Wolfie Settings', //required
	'page_body' => true, //optional if set to false options wont show. You can use action hook wolfie_page_['page_name']
	'tabs' => true, //optional default true
	'settings' => 'wolfie_settings',
	'custom_fields' => [
		[	
			'type' => 'text',
			'name' => 'to-jest-text',
			'desc' => 'Add some text',
		],
		[	
			'type' => 'color',
			'name' => 'colorpicker',
			'desc' => 'Pick some color',
		],
		[	
			'type' => 'whatever',
			'name' => 'colorpicker2',
			'desc' => 'Pick some color2',
		],
		[	
			'type' => 'image',
			'name' => 'test',
			'desc' => 'Add image for the ulotka',
		],
		[	
			'type' => 'file',
			'name' => 'test2',
			'desc' => 'Add image for the ulotka 2', //optional
			'tab' => 'social icons'
		],
		[	
			'type' => 'gallery',
			'name' => 'test3',
			'desc' => 'Add images to display on kontakt page', //optional
		],
	],
	'dashicon' => plugin_dir_url(__FILE__) . 'assets/img/wolf.png'
];
$pw->setPage('Wolfie Settings', $args);
//above default page settings




/*
* Below new custom pages
*/
$pw = new Wolfie_page();
$args = [
	'page_name' => 'Incolt Settings',
	'page_body' => true, //if set to false options will not be displayed. You can use action hook wolfie_page_['page_name']
	'tabs' => false,
	'settings' => 'incolt_settings',
	'custom_fields' => [
		[	
			'type' => 'image',
			'name' => 'test',
			'desc' => 'Add image for the ulotka'
		],
	],
	''
];
$pw->setPage('Incolt Settings', $args);