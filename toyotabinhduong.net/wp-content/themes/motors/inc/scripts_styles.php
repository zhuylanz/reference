<?php

$theme_info = wp_get_theme();
define( 'STM_THEME_VERSION', ( WP_DEBUG ) ? time() : $theme_info->get( 'Version' ) );

if ( ! is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'stm_load_theme_ss' );
}

function stm_load_theme_ss() {

    // Styles
	//Fonts
	$typography_body_font_family    = get_theme_mod( 'typography_body_font_family' );
	$typography_heading_font_family = get_theme_mod( 'typography_heading_font_family' );

	$layout = stm_get_current_layout();
	$upload_dir = wp_upload_dir();
	$stm_upload_dir = $upload_dir['baseurl'] . '/stm_uploads';

	//Main font if user hasn't chosen anything
	if ( empty( $typography_body_font_family ) or empty($typography_heading_font_family)) {
		wp_enqueue_style( 'stm_default_google_font', stm_default_google_fonts_enqueue(), null, STM_THEME_VERSION, 'all' );
	}
	
	wp_enqueue_style( 'stm-boostrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', null, STM_THEME_VERSION, 'all' );
	
	wp_enqueue_style( 'stm-theme-font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.min.css', null, STM_THEME_VERSION, 'all' );
	
	wp_enqueue_style( 'stm-select2', get_template_directory_uri() . '/assets/css/select2.min.css', null, STM_THEME_VERSION, 'all' );

    wp_enqueue_style('fancybox', get_template_directory_uri() . '/assets/css/jquery.fancybox.css', array());
	
	wp_enqueue_style( 'stm-datetimepicker', get_template_directory_uri() . '/assets/css/jquery.stmdatetimepicker.css', null, STM_THEME_VERSION, 'all' );
	
	wp_enqueue_style( 'stm-jquery-ui-css', get_template_directory_uri() . '/assets/css/jquery-ui.css', null, STM_THEME_VERSION, 'all' );

    wp_enqueue_style( 'stm-theme-service-icons', get_template_directory_uri() . '/assets/css/service-icons.css', null, STM_THEME_VERSION, 'all' );

    wp_enqueue_style( 'stm-theme-boat-icons', get_template_directory_uri() . '/assets/css/boat-icons.css', null, STM_THEME_VERSION, 'all' );

    wp_enqueue_style( 'stm-theme-moto-icons', get_template_directory_uri() . '/assets/css/motorcycle/icons.css', null, STM_THEME_VERSION, 'all' );

    wp_enqueue_style( 'stm-theme-rental-icons', get_template_directory_uri() . '/assets/css/rental/icons.css', null, STM_THEME_VERSION, 'all' );

    wp_enqueue_style( 'stm-theme-magazine-icons', get_template_directory_uri() . '/assets/css/magazine/magazine-icon-style.css', null, STM_THEME_VERSION, 'all' );

	/*Deregister theme styles and scripts*/
	wp_deregister_style( 'listings-frontend' );
	wp_deregister_style( 'listings-add-car' );
	wp_deregister_script( 'listings-add-car' );

	if( get_theme_mod( 'site_style', 'site_style_default' ) != 'site_style_default' and is_dir( $upload_dir['basedir'] . '/stm_uploads' )  ) {
        wp_enqueue_style( 'stm-skin-custom', $stm_upload_dir . '/skin-custom.css', null, get_option('stm_custom_style', '4'), 'all' );

        if(stm_is_listing()) {
            wp_enqueue_style('stm-custom-scrollbar', get_template_directory_uri() . '/assets/css/listing/jquery.mCustomScrollbar.css', null, STM_THEME_VERSION, 'all');
        }

    } else {
        if($layout == 'boats') {
            wp_enqueue_style( 'stm-theme-style-boats', get_template_directory_uri() . '/assets/css/boats/app.css', null, STM_THEME_VERSION, 'all' );
        } elseif($layout == 'motorcycle') {
            wp_enqueue_style( 'stm-theme-style-sass', get_template_directory_uri() . '/assets/css/motorcycle/app.css', null, STM_THEME_VERSION, 'all' );
        } else {
			wp_enqueue_style( 'stm-theme-style-sass', get_template_directory_uri() . '/assets/css/app.css', null, STM_THEME_VERSION, 'all' );
            if(stm_is_listing()) {
                wp_enqueue_style('stm-theme-style-listing-sass', get_template_directory_uri() . '/assets/css/listing/app.css', null, STM_THEME_VERSION, 'all');
                wp_enqueue_style('stm-custom-scrollbar', get_template_directory_uri() . '/assets/css/listing/jquery.mCustomScrollbar.css', null, STM_THEME_VERSION, 'all');
            } elseif($layout == 'car_magazine') {
				wp_enqueue_style('stm-theme-style-magazine-sass', get_template_directory_uri() . '/assets/css/magazine/app.css', null, STM_THEME_VERSION, 'all');
			} elseif($layout == 'car_dealer_two') {
				wp_enqueue_style('stm-theme-style-dealer-two-sass', get_template_directory_uri() . '/assets/css/dealer_two/app.css', null, STM_THEME_VERSION, 'all');
			}
		}

        if(stm_is_rental()) {
            wp_enqueue_style( 'stm-theme-style-rental', get_template_directory_uri().'/assets/css/rental/app.css', null, STM_THEME_VERSION, 'all' );
        }
	}

	wp_enqueue_style( 'stm-theme-frontend-customizer', get_template_directory_uri() . '/assets/css/frontend_customizer.css', null, STM_THEME_VERSION, 'all' );

	// Animations
	wp_enqueue_style( 'stm-theme-style-animation', get_template_directory_uri() . '/assets/css/animation.css', null, STM_THEME_VERSION, 'all' );

	if( get_theme_mod( 'site_style' ) && get_theme_mod( 'site_style' ) != 'site_style_default' && get_theme_mod( 'site_style' ) != 'site_style_custom' ){
		wp_enqueue_style( STM_THEME_SLUG . '-' . get_theme_mod( 'site_style' ) );
	}

	// Theme main stylesheet
	wp_enqueue_style( 'stm-theme-style', get_template_directory_uri().'/style.css', null, STM_THEME_VERSION, 'all' );
	

	// Scripts
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	$google_api_key = get_theme_mod( 'google_api_key', '' );
    $google_marker_cluster = 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js';
    if( !empty($google_api_key) ) {
		$google_api_map = 'https://maps.googleapis.com/maps/api/js?key='.$google_api_key.'&';
	} else {
		$google_api_map = 'https://maps.googleapis.com/maps/api/js?';
	}

	if(stm_is_listing() or stm_is_boats() || stm_is_dealer_two()) {
		$google_api_map .= 'libraries=places';
	}

	$callback = '';
    if(function_exists('is_plugin_active')) {
        if (is_plugin_active('stm_motors_events/stm_motors_events.php')) {
            $callback = '&callback=initGoogleScripts';
        }
    }

	wp_register_script('stm_marker_cluster', $google_marker_cluster, array( 'jquery' ), STM_THEME_VERSION, true);
	wp_register_script( 'stm_gmap', $google_api_map . $callback . '&language=' . get_bloginfo( 'language' ), array( 'jquery' ), STM_THEME_VERSION, true );
	wp_register_script( 'custom_scrollbar', get_template_directory_uri() . '/assets/js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), STM_THEME_VERSION, true );

	wp_register_script( 'stm_grecaptcha', 'https://www.google.com/recaptcha/api.js?onload=stmMotorsCaptcha&render=explicit', array( 'jquery' ), STM_THEME_VERSION, true );

	wp_enqueue_script( 'stm-classie', get_template_directory_uri() . '/assets/js/classie.js', 'jquery', STM_THEME_VERSION, false );

	wp_enqueue_script( 'stm-jquerymigrate', get_template_directory_uri() . '/assets/js/jquery-migrate-1.2.1.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	
	wp_enqueue_script( 'stm-bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	
	wp_enqueue_script( 'stm-isotope', get_template_directory_uri() . '/assets/js/isotope.pkgd.min.js', array( 'jquery', 'imagesloaded' ), STM_THEME_VERSION, true );
	
	wp_enqueue_script( 'stm-lazyload', get_template_directory_uri() . '/assets/js/jquery.lazyload.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	
	wp_enqueue_script( 'stm-jquery-ui-core', get_template_directory_uri() . '/assets/js/jquery-ui.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	
	wp_enqueue_script( 'stm-jquery-touch-punch', get_template_directory_uri() . '/assets/js/jquery.touch.punch.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	
	wp_enqueue_script( 'stm-select2-js', get_template_directory_uri() . '/assets/js/select2.full.min.js', array( 'jquery' ), STM_THEME_VERSION, true );

    wp_enqueue_script('fancybox', get_template_directory_uri() . '/assets/js/jquery.fancybox.pack.js', array('jquery'), null, true);
	
	wp_enqueue_script( 'stm-uniform-js', get_template_directory_uri() . '/assets/js/jquery.uniform.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	
	wp_enqueue_script( 'stm-datetimepicker-js', get_template_directory_uri() . '/assets/js/jquery.stmdatetimepicker.js', array( 'jquery' ), STM_THEME_VERSION, true );

	wp_enqueue_script( 'stm-vivus', get_template_directory_uri() . '/assets/js/vivus.min.js', array( 'jquery' ), STM_THEME_VERSION, false );

	wp_enqueue_script( 'jquery-cookie', get_template_directory_uri() . '/assets/js/jquery.cookie.js', array( 'jquery' ), STM_THEME_VERSION, false );

	wp_enqueue_script( 'stm-typeahead', get_template_directory_uri() . '/assets/js/typeahead.jquery.min.js', array( 'jquery' ), STM_THEME_VERSION, true );

	wp_enqueue_script( 'stm-theme-scripts', get_template_directory_uri() . '/assets/js/app.js', array( 'jquery' ), STM_THEME_VERSION, true );

	if(stm_is_magazine()) wp_enqueue_script( 'stm-magazine-theme-scripts', get_template_directory_uri() . '/assets/js/magazine_scripts.js', array( 'jquery' ), STM_THEME_VERSION, true );

	wp_add_inline_script( 'stm-theme-scripts', get_theme_mod('footer_custom_scripts', '') );
	
	wp_register_script( 'stm-countUp.min.js', get_template_directory_uri() . '/assets/js/countUp.min.js', array( 'jquery' ), STM_THEME_VERSION, true );

	//Enable scroll js only if user wants header be fixed
	$fixed_header = get_theme_mod( 'header_sticky', true );
	if ( ! empty( $fixed_header ) and $fixed_header ) {
		wp_enqueue_script( 'stm-theme-scripts-header-scroll', get_template_directory_uri() . '/assets/js/app-header-scroll.js', array( 'jquery' ), STM_THEME_VERSION, true );
	}

	if(stm_pricing_enabled()) {
		wp_enqueue_script( 'stm-theme-user-sidebar', get_template_directory_uri() . '/assets/js/app-user-sidebar.js', array( 'jquery' ), STM_THEME_VERSION, true );
		wp_enqueue_script( 'jquery.countdown.js', get_template_directory_uri() . '/assets/js/jquery.countdown.min.js', array( 'jquery' ), STM_THEME_VERSION, true );
	}

	if(stm_is_magazine()) {
        wp_enqueue_script( 'jquery.countdown.js', get_template_directory_uri() . '/assets/js/jquery.countdown.min.js', array( 'jquery' ), STM_THEME_VERSION, true );

        wp_enqueue_script( 'vue_min', get_template_directory_uri() . '/assets/js/vue.min.js', array( 'stm-typeahead' ), STM_THEME_VERSION, false );
        wp_enqueue_script( 'vue_resource', get_template_directory_uri() . '/assets/js/vue-resource.js', array( 'stm-typeahead' ), STM_THEME_VERSION, false );
        wp_enqueue_script( 'vue_app', get_template_directory_uri() . '/assets/js/vue-app.js', array( 'stm-typeahead' ), STM_THEME_VERSION, false );
    }
	
	$smooth_scroll = get_theme_mod( 'smooth_scroll', false );
	if( !empty($smooth_scroll) and $smooth_scroll) {
		wp_enqueue_script( 'stm-smooth-scroll', get_template_directory_uri() . '/assets/js/smoothScroll.js', array( 'jquery' ), STM_THEME_VERSION, true );
	}

	wp_enqueue_script( 'stm-theme-scripts-ajax', get_template_directory_uri() . '/assets/js/app-ajax.js', array( 'jquery' ), STM_THEME_VERSION, true );

	wp_enqueue_script( 'stm-load-image', get_template_directory_uri() . '/assets/js/load-image.all.min.js', array(), STM_THEME_VERSION, true );

	wp_enqueue_script( 'stm-theme-sell-a-car', get_template_directory_uri() . '/assets/js/sell-a-car.js', array( 'jquery', 'stm-load-image' ), STM_THEME_VERSION, true );

	wp_enqueue_script( 'stm-theme-script-filter', get_template_directory_uri() . '/assets/js/filter.js', array( 'jquery' ), STM_THEME_VERSION, true );

	if(stm_is_listing() or stm_is_boats() ||stm_is_dealer_two()) {
        wp_enqueue_script('stm_marker_cluster');

        $handle = 'stm_gmap';
        $list = 'enqueued';
        if (wp_script_is( $handle, $list )) {
        } else {
            wp_enqueue_script( 'stm_gmap' );
        }

        wp_enqueue_script( 'stm-google-places', get_template_directory_uri() . '/assets/js/stm-google-places.js', 'stm_gmap', STM_THEME_VERSION, true );
        wp_enqueue_script('custom_scrollbar');
		wp_enqueue_script( 'stm-cascadingdropdown', get_template_directory_uri() . '/assets/js/jquery.cascadingdropdown.js', array( 'jquery' ), STM_THEME_VERSION );
    } elseif(stm_is_magazine()) {
		wp_enqueue_script( 'stm_gmap' );
		wp_enqueue_script( 'stm-google-places', get_template_directory_uri() . '/assets/js/stm-google-places.js', 'stm_gmap', STM_THEME_VERSION, true );
	}

    wp_localize_script( 'stm-theme-scripts', 'stm_i18n', array(
        'remove_from_compare' => __( 'Remove from compare', 'motors' ),
        'remove_from_favorites' => __( 'Remove from favorites', 'motors' ),
    ) );
}

// Admin styles
function stm_admin_styles() {
	wp_enqueue_style( 'stm-theme-admin-service-icons', get_template_directory_uri() . '/assets/css/service-icons.css', null, STM_THEME_VERSION, 'all' );
	
	wp_enqueue_style( 'stm-theme-admin-styles', get_template_directory_uri() . '/assets/css/admin.css', null, STM_THEME_VERSION, 'all' );

	wp_enqueue_style( 'stm-theme-fonticonpicker-css', get_template_directory_uri() . '/assets/css/jquery.fonticonpicker.min.css', null, STM_THEME_VERSION, 'all' );

	wp_enqueue_script( 'stm-theme-fonticonpicker', get_template_directory_uri() . '/assets/js/jquery.fonticonpicker.min.js', 'jquery', STM_THEME_VERSION, true );

	wp_enqueue_style( 'stm-theme-service-icons', get_template_directory_uri() . '/assets/css/service-icons.css', null, STM_THEME_VERSION, 'all' );

	wp_enqueue_style( 'stm-theme-boats-icons', get_template_directory_uri() . '/assets/css/boat-icons.css', null, STM_THEME_VERSION, 'all' );

	wp_enqueue_style( 'stm-theme-moto-icons', get_template_directory_uri() . '/assets/css/motorcycle/icons.css', null, STM_THEME_VERSION, 'all' );

	wp_enqueue_style( 'stm-theme-rental-icons', get_template_directory_uri() . '/assets/css/rental/icons.css', null, STM_THEME_VERSION, 'all' );

	wp_enqueue_style( 'stm-theme-magazine-icons', get_template_directory_uri() . '/assets/css/magazine/magazine-icon-style.css', null, STM_THEME_VERSION, 'all' );
}

add_action( 'admin_enqueue_scripts', 'stm_admin_styles' );

if(!function_exists('motors_enqueue_scripts_styles')) {
    function motors_enqueue_scripts_styles($fileName) {
        if(file_exists(get_stylesheet_directory() . '/assets/css/vc_template_styles/' . $fileName . '.css')) {
            wp_enqueue_style($fileName, get_stylesheet_directory_uri() . '/assets/css/vc_template_styles/' . $fileName . '.css', null, STM_THEME_VERSION, 'all');
        }

        if(file_exists(get_stylesheet_directory() . '/assets/js/vc_template_scripts/' . $fileName . '.js')) {
            wp_enqueue_script( $fileName, get_stylesheet_directory_uri() . '/assets/js/vc_template_scripts/' . $fileName . '.js', 'jquery', STM_THEME_VERSION, true );
        }
    }
}

// Default Google fonts enqueue
if( !function_exists('stm_default_google_fonts_enqueue')) {
	function stm_default_google_fonts_enqueue() {
		$fonts_url = '';

		if(stm_is_motorcycle()) {
			$montserrat = _x( 'on', 'Exo 2 font: on or off', 'motors' );
		} else {
			$montserrat = _x( 'on', 'Montserrat font: on or off', 'motors' );
		}
		$open_sans = _x( 'on', 'Open Sans font: on or off', 'motors' );
		 
		if ( 'off' !== $montserrat || 'off' !== $open_sans ) {
			$font_families = array();
			 
			if ( 'off' !== $montserrat ) {
				if(stm_is_motorcycle()) {
					$font_families[] = 'Exo 2:400,300,500,600,700';
				} else {
					$font_families[] = 'Montserrat:400,500,600,700';
				}

			}
			 
			if ( 'off' !== $open_sans ) {
				$font_families[] = 'Open Sans:300,400,700';
			}

			$query_args = array(
				'family' => urlencode( implode( '|', $font_families ) ),
				'subset' => urlencode( 'latin,latin-ext' )
			);
			 
			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}
		 
		return esc_url_raw( $fonts_url );
	}
}