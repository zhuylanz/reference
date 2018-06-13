<?php
/*Redirect to theme Welcome screen*/
global $pagenow;

if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated']) && !defined('ENVATO_HOSTED_SITE')) {
	wp_redirect(admin_url("admin.php?page=stm-admin"));
}

/*Theme info*/
function stm_get_theme_info() {
	$theme = wp_get_theme();
	$theme_name = $theme->get('Name');
	$theme_v = $theme->get('Version');

	$theme_info = array(
		'name' => $theme_name,
		'slug' => sanitize_file_name(strtolower($theme_name)),
		'v'    => $theme_v,
	);

	return $theme_info;
}

function stm_get_creds() {

	/*If envato hosted*/
	if ( !defined('ENVATO_HOSTED_SITE') && !defined('SUBSCRIPTION_CODE') ){
		$t = get_option('envato_market', array());
		if( !empty($t['token']) ) {
			$creds['t'] = $t['token'];
		}else{
			$creds['t'] = '';
		}
		$creds['host'] = false;
	}else{
		$creds['t'] = SUBSCRIPTION_CODE;
		$creds['host'] = true;
	}

	return $creds;
}

function stm_check_auth() {

	$creds = stm_get_creds();
	$has_t = get_site_transient('stm_theme_auth');

	if( false === $has_t ) {

		$api_args = array(
			'theme' => STM_ITEM_NAME,
			't' => $creds['t'],
			'host' => $creds['host'],
		);
		$url = add_query_arg( $api_args, STM_API_URL . 'registration/');
		$response = wp_remote_get( $url, array( 'timeout' => 20 ) );

		// Check the response code.
		$response_code = wp_remote_retrieve_response_code( $response );
		$return = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $response_code == '200' ) {
			set_site_transient('stm_theme_auth', $return['confirm_code'] );
			delete_site_transient('stm_auth_notice');
			return $return['confirm_code'];
		}else{
			set_site_transient('stm_auth_notice', $return['message'] );
			delete_site_transient('stm_theme_auth');
			return false;
		}
	}

	return $has_t;
}

function get_package( $item, $ftype ){

	$packages = array();

	$src = get_transient( 'stm_installer_package' );
	if ( false !== $src ) {
		if ( !empty($src[$item]) ) return $src[$item];
		$packages = $src;
	}

	$creds = stm_get_creds();
	$api_args = array(
		'theme' => STM_ITEM_NAME,
		't' => $creds['t'],
		'item' => $item,
		'ftype' => $ftype,
		'host' => $creds['host'],
	);

	$src = add_query_arg( $api_args, STM_API_URL . 'getpackage/');
	$packages[$item] = $src;
	set_transient( 'stm_installer_package', $packages, 300 );

	return $src;
}

function prepare_demo( $layout ){

	$tempDir = get_temp_dir();
	$fzip = $tempDir . $layout .'.zip';
	$fxml = $tempDir . $layout .'.xml';

	if( file_exists($fxml) ){
		return $fxml;
	}

	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	$response = wp_remote_get( get_package($layout, 'zip'), array('timeout' => 30) );
	if ( is_wp_error( $response ) ) {
		return false;
	}
	$body = wp_remote_retrieve_body( $response );

	// file_get_contents if body is empty.
	if ( empty( $body ) ) {
		if ( function_exists( 'ini_get' ) && ini_get( 'allow_url_fopen' ) ) {
			$body = @file_get_contents( get_package($layout, 'zip') );
		}
	}

	if ( ! $wp_filesystem->put_contents( $fzip , $body ) ) {
		@unlink( $fzip );
		$fp = @fopen( $fzip, 'w' );

		@fwrite( $fp, $body );
		@fclose( $fp );
	}

	if ( class_exists( 'ZipArchive' ) ) {
		$zip = new ZipArchive();
		if ( true === $zip->open( $fzip ) ) {
			$zip->extractTo( $tempDir );
			$zip->close();
			return $fxml;
		}
	}

	$unzip = unzip_file( $fzip, $tempDir );
	if($unzip){
		return $fxml;
	}

	return false;
}

function stm_set_creds() {
	if(isset($_POST['stm_registration'])) {
		if(isset($_POST['stm_registration']['token'])) {
			delete_site_transient('stm_theme_auth');
			delete_transient('stm_installer_package');

			$token = array();
			$token['token'] = sanitize_text_field($_POST['stm_registration']['token']);

			update_option('envato_market', $token);

			$check_auth = stm_check_auth();
			if( !empty($check_auth) ){
				$envato_market = Envato_Market::instance();
				$envato_market->items()->set_themes(true);
			}
		}
	}
}

add_action('init', 'stm_set_creds');

function stm_convert_memory($size) {
	$l   = substr( $size, -1 );
	$ret = substr( $size, 0, -1 );
	switch ( strtoupper( $l ) ) {
		case 'P':
			$ret *= 1024;
		case 'T':
			$ret *= 1024;
		case 'G':
			$ret *= 1024;
		case 'M':
			$ret *= 1024;
		case 'K':
			$ret *= 1024;
	}
	return $ret;
}

function stm_theme_support_url() {
	return 'https://stylemixthemes.com/';
}

function stm_get_admin_images_url($image) {
	return esc_url(get_template_directory_uri() . '/assets/admin/images/' . $image);
}

// Add hidden price before user can update plugin
function stm_add_genuine_price_hidden()
{
    add_meta_box('stm_genuine_price', 'stm genuine price', 'stm_genuine_price_hidden', stm_listings_post_type());
}

add_action('add_meta_boxes', 'stm_add_genuine_price_hidden');

function stm_genuine_price_hidden()
{
}

add_action('init', 'stm_patching_redirect');

function stm_patching_redirect()
{
    $patched = get_option('stm_tax_patched', '');

    /*If already patched*/
    if (!empty($patched)) {
        return false;
    }

    $patching = false;
    if (isset($_POST['action']) and $_POST['action'] == 'stm_admin_patch_price') {
        $patching = true;
    }

    $theme = stm_get_theme_info();

    $listings_created = wp_count_posts(stm_listings_post_type());
    if (!is_wp_error($listings_created)) {
        if (empty($listings_created->publish)) {
            $patched = stm_patch_status('stm_tax_patched', 'dismiss_patch');
        }
    } else {
        $patched = stm_patch_status('stm_tax_patched', 'dismiss_patch');
    }

    /*if patch in progress*/
    $current_patching = false;
    if (isset($_GET['page']) and $_GET['page'] == 'stm-admin-patching') {
        $current_patching = true;
    }

    if (empty($patched) and !$current_patching and !$patching) {
        wp_redirect(esc_url_raw(admin_url('admin.php?page=stm-admin-patching')));
        exit;
    }
}

function stm_patch_status($patch_name, $status)
{
    update_option($patch_name, $status);
    return $status;
}

function stm_admin_patch_price()
{
    $r = array();
    $offset = intval($_POST['offset']);

	global $wpdb;
    $options = $wpdb->get_results("SELECT * FROM " . $wpdb->options . " WHERE option_name LIKE '%stm_parent_taxonomy%' LIMIT 10 OFFSET 0");

    $new_offset = $offset + count($options);

	if (count($options) == 0) {
		$new_offset = 'none';
		stm_patch_status('stm_tax_patched', 'updated');
	} else {
		$opt = array();
		foreach ($options as $val) {
			$parseOpt = explode("_", $val->option_name);
			$opt[] = $parseOpt;
			if(!empty($val->option_value)) {
				update_term_meta(end($parseOpt), 'stm_parent', $val->option_value);
			}
			delete_option($val->option_name);
		}
	}

    $r['offset'] = $new_offset;

    $r = json_encode($r);
    echo $r;

    exit();
}

add_action('wp_ajax_stm_admin_patch_price', 'stm_admin_patch_price');


function stm_admin_patch_location()
{
	$key = get_theme_mod('google_api_key', '');

	if (!$key) {
		$r['error'] = 'No api key. Breaking.';
	}

	$offset = intval($_POST['offset']);
	$new_offset = $offset;

	$posts = get_posts(array('post_type' 	=> 'listings',
		'post_status' 	=> 'publish',
		'posts_per_page'=> 5,
		'offset'		=> $offset,
		'meta_query' 	=> array(
			array(
				'key' 		=> 'stm_lat_car_admin',
				'compare' 	=> 'NOT EXISTS'
			),
		),
	));

	if (empty($posts)) {
		$new_offset = 'none';
	} else {
		$new_offset += count($posts);

		foreach ($posts as $post) {
			if (get_post_meta($post->ID, 'stm_lat_car_admin', true) && get_post_meta($post->ID, 'stm_lat_car_admin', true)) {
				continue;
			}

			$address = get_post_meta($post->ID, 'stm_car_location', true);
			if (!$address) {
				continue;
			}

			$r = wp_remote_get("https://maps.googleapis.com/maps/api/geocode/json?" . http_build_query(compact('address', 'key')));
			$r = json_decode($r['body'], true);
			if ($r['status'] != 'OK') {
				continue;
			}

			$coor = false;
			foreach ($r['results'] as $result) {
				if ($result['geometry']['location']) {
					$coor = $result['geometry']['location'];
					break;
				}
			}

			if ($coor) {
				$r['error_message'] = '';
				update_post_meta($post->ID, 'stm_lat_car_admin', $coor['lat']);
				update_post_meta($post->ID, 'stm_lng_car_admin', $coor['lng']);
			} else {
				$new_offset = 'none';
				$r['error'] = 'No Lat&Lng found in google api response.';
			}
		}
	}

	$r['offset'] = $new_offset;

	$r = json_encode($r);
	echo $r;

	exit();
}

add_action('wp_ajax_stm_admin_patch_location', 'stm_admin_patch_location');

function stm_admin_patch_cat_image()
{
    $r = array();
    $offset = intval($_POST['offset']);

    global $wpdb;
    $options = $wpdb->get_results("SELECT * FROM " . $wpdb->options . " WHERE option_name LIKE '%stm_taxonomy_listing_image%' LIMIT 10 OFFSET 0");

    $new_offset = $offset + count($options);

    if (count($options) == 0) {
        $new_offset = 'none';
        stm_patch_status('stm_category_image_patched', 'updated');
    } else {
        $opt = array();
        foreach ($options as $val) {
            $parseOpt = explode("_", $val->option_name);
            $opt[] = $parseOpt;
            if(!empty($val->option_value) && is_numeric($val->option_value)) {
                update_term_meta(end($parseOpt), 'stm_image', $val->option_value);
            }
            delete_option($val->option_name);
        }
    }

    $r['offset'] = $new_offset;
    $r['cats'] = $opt;

    $r = json_encode($r);
    echo $r;

    exit();
}

add_action('wp_ajax_stm_admin_patch_cat_image', 'stm_admin_patch_cat_image');

/*Plugin installer via tgmpa*/
add_action('wp_ajax_motors_install_plugin', 'motors_install_plugin');
function motors_install_plugin()
{
    $r = array();
    $plugins = stm_require_plugins_popup(true);
    $layout = sanitize_text_field($_GET['layout']);
    $layout_plugins_config = motors_layout_plugins($layout);
    $layout_plugins = array();

    foreach($plugins as $plugin_name => $plugin_info) {
        if(in_array($plugin_name, $layout_plugins_config)) {
            $layout_plugins[$plugin_name] = $plugin_info;
        }
    }

    $plugins = $layout_plugins;

    $plugin_slug = sanitize_text_field($_GET['plugin']);

    if (!current_user_can('install_plugins')) {
        return;
    }

    /*if install demo*/
    if($plugin_slug === 'import_demo') {
        $r['import_demo'] = true;
        wp_send_json($r);
    }

    /*No plugin*/
    if (empty($plugin_slug) and !empty($plugins[$plugin_slug])) {
        wp_send_json(array('error' => esc_html__('Error occured', 'pearl')));
        exit;
    }

    require_once ABSPATH . 'wp-load.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
    require_once get_template_directory() . '/admin/includes/stm_upgrader_skin.php';


    $plugin_upgrader = new Plugin_Upgrader(new STM_Plugin_Upgrader_Skin(array('plugin' => $plugin_slug)));
    $plugin_info = $plugins[$plugin_slug];
    $next = motors_get_next($plugins, $plugin_slug);


    if (!empty($plugin_info['source'])) {
        $source = $plugin_info['source'];
    } else {
        $response = plugins_api('plugin_information', array('slug' => $plugin_slug));
        if(!is_wp_error($response) and !empty($response->download_link)) {
            $source = $response->download_link;
        }
    };

    $r['source'] = $source;

    if(!empty($source)) {
        $installed = (motors_check_plugin_active($plugin_slug)) ? true : $plugin_upgrader->install($source);
        if(is_wp_error($installed)) {
            $r['error'] = $installed->get_error_message();
        } else {
            if(!empty($next)) {
                $r['next'] = $next['slug'];
            }
            motors_activate_plugin($plugin_slug);
            $r['installed'] = true;
            $r['activated'] = true;
            $r['plugin_slug'] = $plugin_slug;
        }
    }

    /*Check if last*/
    if(end($plugins) === $plugin_info) $r['import_demo'] = true;

    wp_send_json($r);
    exit;
}

function motors_check_plugin_active($slug) {
    return is_plugin_active(motors_get_plugin_main_path($slug));
}

function motors_activate_plugin($slug) {
    activate_plugin(motors_get_plugin_main_path($slug));
}

function motors_get_plugin_main_path($slug) {
    $plugin_data = get_plugins('/' . $slug);
    if(!empty($plugin_data)) {
        $plugin_file = array_keys($plugin_data);
        $plugin_path = $slug . '/' . $plugin_file[0];
    } else {
        $plugin_path = false;
    }
    return $plugin_path;
}


function motors_get_next($array, $key)
{
    $currentKey = key($array);
    while ($currentKey !== null && $currentKey != $key) {
        next($array);
        $currentKey = key($array);
    }
    return next($array);
}