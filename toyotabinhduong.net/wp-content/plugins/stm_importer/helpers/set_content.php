<?php
function stm_set_content_options( $chosen_template ) {
	/*Set menus*/
    $locations = get_theme_mod('nav_menu_locations');
    $menus = wp_get_nav_menus();

    if (!empty($menus)) {
        foreach ($menus as $menu) {
            if (is_object($menu)) {
                switch ($menu->name) {
                    case 'Primary menu':
                        $locations['primary'] = $menu->term_id;
                        break;
                    case 'Top bar menu':
                        $locations['top_bar'] = $menu->term_id;
                        break;
                    case 'Bottom menu':
                        $locations['bottom_menu'] = $menu->term_id;
                        break;
                }
            }
        }
    }

    set_theme_mod('nav_menu_locations', $locations);
    set_theme_mod('listing_sidebar', 'no_sidebar');

	//Set pages
	update_option( 'show_on_front', 'page' );

    $inventory_page = get_page_by_title('Inventory');
    if (isset($inventory_page->ID)) {
        set_theme_mod('listing_archive', $inventory_page->ID);
    }

    /*Woocomerce set default pages*/
    if($chosen_template == 'car_dealer' || $chosen_template == 'car_dealer_two' || $chosen_template == 'boats' || $chosen_template == 'motorcycle'){
        $checkout_page = get_page_by_title( 'Checkout' );
        if ( isset( $checkout_page->ID ) ) {
            update_option( 'woocommerce_checkout_page_id', $checkout_page->ID );
        }
        $cart_page = get_page_by_title( 'Cart' );
        if ( isset( $cart_page->ID ) ) {
            update_option( 'woocommerce_cart_page_id', $cart_page->ID );
        }
        $shop_page = get_page_by_title( 'Shop' );
        if ( isset( $shop_page->ID ) ) {
            update_option( 'woocommerce_shop_page_id', $shop_page->ID );
            update_option( 'woocommerce_single_image_width', 327 );
            update_option( 'woocommerce_thumbnail_image_width', 150 );
        }

        $account_page = get_page_by_title( 'My Account' );
        if ( isset( $account_page->ID ) ) {
            update_option( 'woocommerce_myaccount_page_id', $account_page->ID );
        }
    }
    /*Woocomerce set default pages*/

    // Car dealer
    if ($chosen_template == 'car_dealer') {
        stm_update_listing_options_listing_layout();
        $front_page = get_page_by_title('Front page');
        if (isset($front_page->ID)) {
            update_option('page_on_front', $front_page->ID);
        }

        $blog_page = get_page_by_title('Newsroom');
        if (isset($blog_page->ID)) {
            update_option('page_for_posts', $blog_page->ID);
        }
    }

    // Service
    if ($chosen_template == 'service') {
        $front_page = get_page_by_title('Home page');
        if (isset($front_page->ID)) {
            update_option('page_on_front', $front_page->ID);
        }
    }

    // Listing
    if ($chosen_template == 'listing') {

        stm_update_listing_options_listing_layout();

        $front_page = get_page_by_title('Home page');
        if (isset($front_page->ID)) {
            update_option('page_on_front', $front_page->ID);
        }

        $blog_page = get_page_by_title('Blog');
        if (isset($blog_page->ID)) {
            update_option('page_for_posts', $blog_page->ID);
        }

        $dealers = get_page_by_title('Dealers list');
        if (isset($dealers->ID)) {
            set_theme_mod('dealer_list_page', $dealers->ID);
        }

        $compare = get_page_by_title('Compare');
        if (isset($compare->ID)) {
            set_theme_mod('compare_page', $compare->ID);
        }

        $optionCat = get_option('stm_vehicle_listing_options');
        $optionCat[5]['listing_taxonomy_parent'] = 'make';
        update_option('stm_vehicle_listing_options', $optionCat);

        $termmeta = json_decode(file_get_contents(STM_CONFIGURATIONS_PATH . '/helpers/model_json.json'));
        foreach ($termmeta as $key => $value) {
            update_term_meta($value->term_id, $value->meta_key, $value->meta_value);
        }
    }

    // Boats
    if ($chosen_template == 'boats') {
        stm_update_boats_options_listing_layout();

        $front_page = get_page_by_title('Home');
        if (isset($front_page->ID)) {
            update_option('page_on_front', $front_page->ID);
        }

        $blog_page = get_page_by_title('Newsroom');
        if (isset($blog_page->ID)) {
            update_option('page_for_posts', $blog_page->ID);
        }
    }

    // Motorcycle
    if ($chosen_template == 'motorcycle') {
        stm_update_motorcycle_options_listing_layout();

        $front_page = get_page_by_title('Home');
        if (isset($front_page->ID)) {
            update_option('page_on_front', $front_page->ID);
        }

        $blog_page = get_page_by_title('Newsroom');
        if (isset($blog_page->ID)) {
            update_option('page_for_posts', $blog_page->ID);
        }
    }

    // Rental
    if ($chosen_template == 'car_rental') {
        stm_update_options_rental_layout();

        $pages = array(
            'woocommerce_shop_page_id' => 'Reservation',
            'woocommerce_cart_page_id' => 'Cart',
            'woocommerce_checkout_page_id' => 'Checkout',
            'woocommerce_myaccount_page_id' => 'Checkout',
            'woocommerce_terms_page_id' => 'Terms',
            'page_on_front' => 'Home page',
            'rental_datepick' => 'Date Reservation',
            'order_received' => 'Policy'
        );

        foreach($pages as $key => $page) {
            $get_page = get_page_by_title($page);
            if(isset($get_page->ID)) {
                update_option($key, $get_page->ID);
            }
        }

        delete_transient( 'woocommerce_cache_excluded_uris' );

        /*Force woocommerce to update shop archive*/
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
        $wp_rewrite->flush_rules();
    }

    // Magazine
    if ($chosen_template == 'car_magazine') {
        stm_update_listing_options_listing_layout();

        $front_page = get_page_by_title('Home page');
        if (isset($front_page->ID)) {
            update_option('page_on_front', $front_page->ID);
        }

        $blog_page = get_page_by_title('News');
        if (isset($blog_page->ID)) {
            update_option('page_for_posts', $blog_page->ID);
        }


        if (class_exists('RevSlider')) {
            $main_slider = get_template_directory() . '/inc/demo/magazine_home_slider.zip';

            if (file_exists($main_slider)) {
                $slider = new RevSlider();
                $slider->importSliderFromPost(true, true, $main_slider);
            }
        }

        set_theme_mod('site_style', 'site_style_custom');
        stm_print_styles_color();
    }

    // Dealer Two
    if ($chosen_template == 'car_dealer_two') {

        stm_update_listing_options_listing_layout();

        $front_page = get_page_by_title('Home page');
        if (isset($front_page->ID)) {
            update_option('page_on_front', $front_page->ID);
        }

        $blog_page = get_page_by_title('Blog');
        if (isset($blog_page->ID)) {
            update_option('page_for_posts', $blog_page->ID);
        }

        $compare = get_page_by_title('Compare');
        if (isset($compare->ID)) {
            set_theme_mod('compare_page', $compare->ID);
        }

        $optionCat = get_option('stm_vehicle_listing_options');
        $optionCat[5]['listing_taxonomy_parent'] = 'make';
        update_option('stm_vehicle_listing_options', $optionCat);

        $termmeta = json_decode(file_get_contents(STM_CONFIGURATIONS_PATH . '/helpers/model_json.json'));
        foreach ($termmeta as $key => $value) {
            update_term_meta($value->term_id, $value->meta_key, $value->meta_value);
        }

        set_theme_mod('listing_filter_position', 'right');
        set_theme_mod('site_style', 'site_style_custom');
        update_option( 'woocommerce_catalog_columns', 3 );
        stm_print_styles_color();
    }

    /*update genuine price*/
    $args = array(
        'post_type' => stm_listings_post_type(),
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );

    $q = new WP_Query($args);
    if($q->have_posts()) {
        while($q->have_posts()) {
            $q->the_post();
            $id = get_the_ID();
            $price = get_post_meta($id, 'price', true);
            $sale_price = get_post_meta($id, 'sale_price', true);

            if(!empty($sale_price)) {
                $price = $sale_price;
            }

            if(!empty($price)) {
                update_post_meta($id, 'stm_genuine_price', $price);
            }
        }
    }

    $a2aUpdOpt = array(
        'display_in_posts_on_front_page' => -1,
        'display_in_posts_on_archive_pages' => -1,
        'display_in_excerpts' => -1,
        'display_in_posts' => -1,
        'display_in_pages' => -1,
        'display_in_attachments' => -1,
        'display_in_feed' => -1,
        'display_in_cpt_stm_office' => -1,
        'display_in_cpt_sidebar' => -1,
        'display_in_cpt_test_drive_request' => -1,
        'display_in_cpt_listings' => -1,
        'display_in_cpt_product' => -1
    );

    $a2aGetOpt = get_option('addtoany_options');

    if(!empty($a2aGetOpt)) {
        $upd = array_replace($a2aGetOpt, $a2aUpdOpt);
        update_option('addtoany_options', $upd);
    }
}

//Add default taxonomies for the first theme activating
//Only if user dont have them already
function stm_update_options_rental_layout() {
    $stm_listings_update_options = array(
        0 => array(
            'single_name' => 'Seat',
            'plural_name' => 'Seats',
            'slug' => 'drive',
            'font' => 'stm-rental-seats',
            'numeric' => 1,
            'use_on_single_listing_page' => '',
            'use_on_car_listing_page' => 1,
            'use_on_car_archive_listing_page' => '',
            'use_on_single_car_page' => '',
            'use_on_car_filter' => '',
            'use_on_car_modern_filter' => '',
            'use_on_car_modern_filter_view_images' => '',
            'use_on_car_filter_links' => '',
            'number_field_affix' => '',
            'slider' => '',
            'use_on_tabs' => '',
            'use_in_footer_search' => '',
            'use_on_directory_filter_title' => '',
            'listing_taxonomy_parent' => 'fuel-economy',
            'listing_rows_numbers_enable' => '',
            'listing_rows_numbers' => '',
            'enable_checkbox_button' => '',
            'show_in_admin_column' => '',
        ),
        1 => array(
            'single_name' => 'Bag',
            'plural_name' => 'Bags',
            'slug' => 'fuel-economy',
            'font' => 'stm-rental-bag',
            'numeric' => 1,
            'use_on_single_listing_page' => '',
            'use_on_car_listing_page' => 1,
            'use_on_car_archive_listing_page' => '',
            'use_on_single_car_page' => '',
            'use_on_car_filter' => '',
            'use_on_car_modern_filter' => '',
            'use_on_car_modern_filter_view_images' => '',
            'use_on_car_filter_links' => '',
            'number_field_affix' => '',
            'slider' => '',
            'use_on_tabs' => '',
            'use_in_footer_search' => '',
            'use_on_directory_filter_title' => '',
            'listing_taxonomy_parent' => '',
            'listing_rows_numbers_enable' => '',
            'listing_rows_numbers' => '',
            'enable_checkbox_button' => '',
            'show_in_admin_column' => '',
        ),
        2 => array(
            'single_name' => 'Door',
            'plural_name' => 'Doors',
            'slug' => 'exterior-color',
            'font' => 'stm-rental-door',
            'numeric' => 1,
            'use_on_single_listing_page' => '',
            'use_on_car_listing_page' => 1,
            'use_on_car_archive_listing_page' => '',
            'use_on_single_car_page' => '',
            'use_on_car_filter' => '',
            'use_on_car_modern_filter' => '',
            'use_on_car_modern_filter_view_images' => '',
            'use_on_car_filter_links' => '',
            'number_field_affix' => '',
            'slider' => '',
            'use_on_tabs' => '',
            'use_in_footer_search' => '',
            'use_on_directory_filter_title' => '',
            'listing_taxonomy_parent' => '',
            'listing_rows_numbers_enable' => '',
            'listing_rows_numbers' => '',
            'enable_checkbox_button' => '',
            'show_in_admin_column' => '',
        ), 3 => array(
            'single_name' => 'Feature',
            'plural_name' => 'Features',
            'slug' => 'interior-color',
            'font' => 'stm-rental-ac',
            'numeric' => '',
            'use_on_single_listing_page' => '',
            'use_on_car_listing_page' => 1,
            'use_on_car_archive_listing_page' => '',
            'use_on_single_car_page' => '',
            'use_on_car_filter' => '',
            'use_on_car_modern_filter' => '',
            'use_on_car_modern_filter_view_images' => '',
            'use_on_car_filter_links' => '',
            'number_field_affix' => '',
            'slider' => '',
            'use_on_tabs' => '',
            'use_in_footer_search' => '',
            'use_on_directory_filter_title' => '',
            'listing_taxonomy_parent' => '',
            'listing_rows_numbers_enable' => '',
            'listing_rows_numbers' => '',
            'enable_checkbox_button' => '',
            'show_in_admin_column' => '',
        ),
    );
    update_option('stm_vehicle_listing_options', $stm_listings_update_options);
}

function stm_update_listing_options_listing_layout()
{
    $stm_listings_update_options = array(
        1 => array(
            'single_name' => 'Condition',
            'plural_name' => 'Conditions',
            'slug' => 'condition',
            'font' => '',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_map_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => true,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => true,
        ),
        2 => array(
            'single_name' => 'Body',
            'plural_name' => 'Bodies',
            'slug' => 'body',
            'font' => 'stm-service-icon-body_type',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => true,
            'use_on_map_page' => false,
            'use_on_car_filter' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => true,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'listing_rows_numbers' => 'two_cols',
            'enable_checkbox_button' => false,
        ),
        3 => array(
            'single_name' => 'Make',
            'plural_name' => 'Makes',
            'slug' => 'make',
            'font' => '',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_map_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => true,
            'use_on_car_modern_filter_view_images' => true,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => true,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => true,
        ),
        5 => array(
            'single_name' => 'Model',
            'plural_name' => 'Models',
            'slug' => 'serie',
            'font' => '',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_map_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => true,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => true,
        ),
        6 => array(
            'single_name' => 'Mileage',
            'plural_name' => 'Mileages',
            'slug' => 'mileage',
            'font' => 'stm-icon-road',
            'numeric' => true,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => true,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_map_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'number_field_affix' => 'mi',
            'enable_checkbox_button' => false,
        ),
        7 => array(
            'single_name' => 'Fuel type',
            'plural_name' => 'Fuel types',
            'slug' => 'fuel',
            'font' => 'stm-icon-fuel',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_map_page' => false,
            'use_on_car_filter' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
        ),
        8 => array(
            'single_name' => 'Engine',
            'plural_name' => 'Engines',
            'slug' => 'engine',
            'font' => 'stm-icon-engine_fill',
            'numeric' => true,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_map_page' => true,
            'use_on_car_filter' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        9 => array(
            'single_name' => 'Year',
            'plural_name' => 'Years',
            'slug' => 'ca-year',
            'font' => 'stm-icon-road',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_map_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => true,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
        ),
        10 => array(
            'single_name' => 'Price',
            'plural_name' => 'Prices',
            'slug' => 'price',
            'font' => 'stm-icon-road',
            'numeric' => true,
            'slider' => true,
            'use_on_single_listing_page' => true,
            'use_on_car_listing_page' => true,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => false,
            'use_on_map_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => true,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
        ),
        11 => array(
            'single_name' => 'Fuel consumption',
            'plural_name' => 'Fuel consumptions',
            'slug' => 'fuel-consumption',
            'font' => 'stm-icon-fuel',
            'numeric' => true,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => true,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_map_page' => false,
            'use_on_car_filter' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
        ),
        12 => array(
            'single_name' => 'Transmission',
            'plural_name' => 'Transmission',
            'slug' => 'transmission',
            'font' => 'stm-icon-transmission_fill',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => true,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => true,
            'use_on_map_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => true,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
        ),
        13 => array(
            'single_name' => 'Drive',
            'plural_name' => 'Drives',
            'slug' => 'drive',
            'font' => 'stm-icon-drive_2',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => true,
            'use_on_map_page' => false,
            'use_on_car_filter' => false,
            'use_on_car_modern_filter' => true,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
        ),
        14 => array(
            'single_name' => 'Fuel economy',
            'plural_name' => 'Fuel economy',
            'slug' => 'fuel-economy',
            'font' => '',
            'numeric' => true,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_map_page' => false,
            'use_on_car_filter' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
        ),
        15 => array(
            'single_name' => 'Exterior Color',
            'plural_name' => 'Exterior Colors',
            'slug' => 'exterior-color',
            'font' => 'stm-service-icon-color_type',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => true,
            'use_on_map_page' => false,
            'use_on_car_filter' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
        ),
        16 => array(
            'single_name' => 'Interior Color',
            'plural_name' => 'Interior Colors',
            'slug' => 'interior-color',
            'font' => 'stm-service-icon-color_type',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => true,
            'use_on_map_page' => false,
            'use_on_car_filter' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
        )
    );
    update_option('stm_vehicle_listing_options', $stm_listings_update_options);
}

function stm_update_motorcycle_options_listing_layout()
{
    $stm_listings_update_options = array(
        1 => array(
            'single_name' => 'Condition',
            'plural_name' => 'Conditions',
            'slug' => 'condition',
            'font' => '',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_car_filter' => true,
            'use_on_tabs' => false,
            'use_on_car_modern_filter' => true,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => true,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        2 => array(
            'single_name' => 'Type',
            'plural_name' => 'Types',
            'slug' => 'body',
            'font' => '',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => true,
            'use_on_car_filter' => true,
            'use_on_tabs' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        3 => array(
            'single_name' => 'Category',
            'plural_name' => 'Categories',
            'slug' => 'category_type',
            'font' => '',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => true,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => true,
            'use_on_car_filter' => true,
            'use_on_tabs' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => true,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        4 => array(
            'single_name' => 'Brand',
            'plural_name' => 'Brands',
            'slug' => 'make',
            'font' => '',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_car_filter' => true,
            'use_on_tabs' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => true,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => true,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        5 => array(
            'single_name' => 'Model',
            'plural_name' => 'Models',
            'slug' => 'serie',
            'font' => 'icomoon-settings',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        6 => array(
            'single_name' => 'Mileage',
            'plural_name' => 'Mileages',
            'slug' => 'mileage',
            'font' => '',
            'numeric' => true,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => true,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_car_filter' => true,
            'use_on_tabs' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'number_field_affix' => 'ml',
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        7 => array(
            'single_name' => 'Engine',
            'plural_name' => 'Engines',
            'slug' => 'engine',
            'font' => '',
            'numeric' => true,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_car_filter' => false,
            'use_on_tabs' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        8 => array(
            'single_name' => 'Year',
            'plural_name' => 'Years',
            'slug' => 'ca-year',
            'font' => '',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => true,
            'use_on_car_filter' => true,
            'use_on_tabs' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        9 => array(
            'single_name' => 'Price',
            'plural_name' => 'Prices',
            'slug' => 'price',
            'font' => '',
            'numeric' => true,
            'slider' => true,
            'use_on_single_listing_page' => true,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        10 => array(
            'single_name' => 'Color',
            'plural_name' => 'Colors',
            'slug' => 'exterior-color',
            'font' => '',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_car_filter' => false,
            'use_on_tabs' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
    );
    update_option('stm_vehicle_listing_options', $stm_listings_update_options);
}

function stm_update_boats_options_listing_layout()
{
    $stm_listings_update_options = array(
        1 => array(
            'single_name' => 'Make',
            'plural_name' => 'Makes',
            'slug' => 'make',
            'font' => '',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter' => true,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        2 => array(
            'single_name' => 'Model',
            'plural_name' => 'Models',
            'slug' => 'serie',
            'font' => '',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        3 => array(
            'single_name' => 'Condition',
            'plural_name' => 'Conditions',
            'slug' => 'condition',
            'font' => '',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => true,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
        ),
        4 => array(
            'single_name' => 'Length',
            'plural_name' => 'Length',
            'slug' => 'length_range',
            'font' => 'stm-boats-icon-size',
            'numeric' => true,
            'slider' => true,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => true,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'number_field_affix' => '',
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        5 => array(
            'single_name' => 'Year',
            'plural_name' => 'Years',
            'slug' => 'ca-year',
            'font' => 'stm-icon-date',
            'numeric' => true,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        6 => array(
            'single_name' => 'Price',
            'plural_name' => 'Prices',
            'slug' => 'price',
            'font' => '',
            'numeric' => true,
            'slider' => true,
            'use_on_single_listing_page' => true,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        7 => array(
            'single_name' => 'Boat type',
            'plural_name' => 'Boat types',
            'slug' => 'boat-type',
            'font' => '',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => false,
            'use_on_car_archive_listing_page' => false,
            'use_on_single_car_page' => false,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => true,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        8 => array(
            'single_name' => 'Fuel type',
            'plural_name' => 'Fuel types',
            'slug' => 'fuel',
            'font' => 'stm-icon-fuel',
            'numeric' => false,
            'slider' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => true,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_car_filter' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
        9 => array(
            'single_name' => 'Hull material',
            'plural_name' => 'Hull materials',
            'slug' => 'hull_material',
            'font' => 'stm-boats-icon-sail',
            'numeric' => false,
            'use_on_single_listing_page' => false,
            'use_on_car_listing_page' => true,
            'use_on_car_archive_listing_page' => true,
            'use_on_single_car_page' => true,
            'use_on_car_modern_filter' => false,
            'use_on_car_modern_filter_view_images' => false,
            'use_on_car_filter' => true,
            'use_on_car_filter_links' => false,
            'use_on_directory_filter_title' => false,
            'enable_checkbox_button' => false,
            'use_in_footer_search' => false,
        ),
    );
    update_option('stm_vehicle_listing_options', $stm_listings_update_options);
}
