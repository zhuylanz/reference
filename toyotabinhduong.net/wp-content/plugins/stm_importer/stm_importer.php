<?php
/*
Plugin Name: STM Importer
Plugin URI: http://stylemixthemes.com/
Description: STM Importer
Author: Stylemix Themes
Author URI: http://stylemixthemes.com/
Text Domain: stm_importer
Version: 4.3
*/

define('STM_CONFIGURATIONS_PATH', dirname(__FILE__));
require_once(STM_CONFIGURATIONS_PATH . '/helpers/content.php');
require_once(STM_CONFIGURATIONS_PATH . '/helpers/theme_options.php');
require_once(STM_CONFIGURATIONS_PATH . '/helpers/slider.php');
require_once(STM_CONFIGURATIONS_PATH . '/helpers/widgets.php');
require_once(STM_CONFIGURATIONS_PATH . '/helpers/set_content.php');

function stm_demo_import_content()
{

    $layout = 'car_dealer';

    if(!empty($_GET['demo_template'])){
        $layout = sanitize_title($_GET['demo_template']);
    }

	update_option('stm_motors_chosen_template', $layout);

    /*Import content*/
    stm_theme_import_content($layout);

    /*Import theme options*/
    update_option('stm_theme_options', stm_get_layout_options($layout));

    /*Import sliders*/
    stm_theme_import_sliders($layout);

    /*Import Widgets*/
    stm_theme_import_widgets($layout);

    /*Set menu and pages*/
    stm_set_content_options($layout);

    do_action('stm_importer_done', $layout);

	wp_send_json(array(
		'url' => get_home_url('/'),
		'title' => esc_html__('View site', 'stm_domain'),
		'theme_options_title' => esc_html__('Theme options', 'stm_domain'),
		'theme_options' => esc_url_raw(admin_url('customize.php'))
	));
    die();

}

add_action('wp_ajax_stm_demo_import_content', 'stm_demo_import_content');