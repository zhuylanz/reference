<?php
$includes = get_template_directory() . '/admin/includes/';
$admin = get_template_directory() . '/admin/';
define('STM_ITEM_NAME', 'Motors');
define('STM_API_URL', 'https://panel.stylemixthemes.com/api/');

/*Connect Envato market plugin.*/
if(!class_exists('Envato_Market')) {
	require_once($includes . 'envato-market/envato-market.php');
}

require_once $admin . 'layout_config.php';
require_once $includes . 'theme.php';
require_once $includes . 'admin_screens.php';

add_action('init', 'motors_remove_woo_redirect', 10);

function motors_remove_woo_redirect() {
    delete_transient( '_wc_activation_redirect' );
}