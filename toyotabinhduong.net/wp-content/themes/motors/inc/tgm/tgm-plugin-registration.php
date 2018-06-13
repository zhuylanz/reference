<?php

require_once dirname(__FILE__) . '/tgm-plugin-activation.php';

add_action('tgmpa_register', 'stm_require_plugins');

function stm_require_plugins()
{
    $plugins_path = get_template_directory() . '/inc/tgm/plugins';
    $plugins = array(
		array(
            'name' => 'STM Post Type',
            'slug' => 'stm-post-type',
			'source' => get_package( 'stm-post-type', 'zip' ),
            'required' => true,
            'version' => '4.0'
        ),
		array(
            'name' => 'GDPR Compliance & Cookie Consent',
            'slug' => 'stm-gdpr-compliance',
			'source' => get_package( 'stm-gdpr-compliance', 'zip' ),
            'required' => false,
            'version' => '1.0'
        ),
        array(
            'name' => 'Motors - Classified Listings',
            'slug' => 'stm_vehicles_listing',
			'source' => get_package( 'stm_vehicles_listing', 'zip' ),
            'required' => true,
            'version' => '5.9'
        ),
        array(
            'name' => 'Custom Icons by Stylemixthemes',
            'slug' => 'custom_icons_by_stylemixthemes',
            'source' => $plugins_path . '/custom_icons_by_stylemixthemes.zip',
            'required' => true,
            'version' => '1.4'
        ),
        array(
            'name' => 'STM Importer',
            'slug' => 'stm_importer',
			'source' => get_package( 'stm_importer', 'zip' ),
            'required' => true,
            'version' => '4.3'
        ),
        array(
            'name' => 'WPBakery Visual Composer',
            'slug' => 'js_composer',
            'source' => $plugins_path . '/js_composer.zip',
            'required' => true,
            'version' => '5.4.7',
            'external_url' => 'http://vc.wpbakery.com'
        ),
        array(
            'name' => 'Revolution Slider',
            'slug' => 'revslider',
            'source' => $plugins_path . '/revslider.zip',
            'required' => false,
            'version' => '5.4.7.3',
            'external_url' => 'http://www.themepunch.com/revolution/'
        ),
		array(
			'name' => 'AddToAny Share Buttons',
			'slug' => 'add-to-any',
			'required' => false,
			'force_activation' => false,
		),
        array(
            'name' => 'Breadcrumb NavXT',
            'slug' => 'breadcrumb-navxt',
            'required' => false,
            'force_activation' => false,
        ),
        array(
            'name' => 'Contact Form 7',
            'slug' => 'contact-form-7',
            'required' => false,
            'force_activation' => false,
        ),
        array(
            'name' => 'Instagram Feed',
            'slug' => 'instagram-feed',
            'required' => false,
            'external_url' => 'http://smashballoon.com/instagram-feed/'
        ),
        array(
            'name' => 'MailChimp for WordPress',
            'slug' => 'mailchimp-for-wp',
            'required' => false,
            'external_url' => 'https://mc4wp.com/'
        )
    );

    if(stm_is_magazine()){
        $plugins[] = array(
                'name' => 'AccessPress Social Counter',
                'slug' => 'accesspress-social-counter',
                'required' => true,
                'external_url' => 'http://accesspressthemes.com'
            );
        $plugins[] = array(
            'name' => 'Motors - events',
            'slug' => 'stm_motors_events',
			'source' => get_package( 'stm_motors_events', 'zip' ),
            'required' => true,
            'version' => '1.1'
        );
        $plugins[] = array(
            'name' => 'Motors - review',
            'slug' => 'stm_motors_review',
			'source' => get_package( 'stm_motors_review', 'zip' ),
            'required' => true,
            'version' => '1.1'
        );
    }

    if(!stm_is_magazine()){
        $plugins[] = array(
                'name' => 'Woocommerce',
                'slug' => 'woocommerce',
                'required' => false,
                'force_activation' => false,
        );
    }

    if(stm_is_service()){
        $plugins[] = array(
            'name' => 'Bookly Lite',
            'slug' => 'bookly-responsive-appointment-booking-tool',
            'required' => false,
            'force_activation' => false,
        );
    }

    /*If classified*/
    if(stm_is_listing()){
        $plugins[] = array(
            'name' => 'Subscriptio',
            'slug' => 'subscriptio',
            'source' => $plugins_path . '/subscriptio.zip',
            'version' => '2.3.8',
            'required' => true,
        );
        $plugins[] = array(
            'name' => 'WordPress Social Login',
            'slug' => 'wordpress-social-login',
            'required' => true,
        );
    }

    $config = array(
        'id' => 'tgm_message_update_new3r',
        'strings' => array(
            'nag_type' => 'update-nag'
        )
    );

    tgmpa($plugins, $config);

}

function stm_require_plugins_popup($return = false)
{
    $plugins_path = get_template_directory() . '/inc/tgm/plugins';
    $plugins = array(
		'stm-post-type' => array(
            'name' => 'STM Post Type',
            'slug' => 'stm-post-type',
			'icon' => 'stm-configurations',
			'source' => get_package( 'stm-post-type', 'zip' ),
            'required' => true,
            'version' => '4.0'
        ),
        'stm-gdpr-compliance' => array(
            'name' => 'GDPR Compliance & Cookie Consent',
            'slug' => 'stm-gdpr-compliance',
            'icon' => 'stm-configurations',
            'source' => get_package( 'stm-gdpr-compliance', 'zip' ),
            'required' => false,
            'version' => '1.0'
        ),
        'stm_vehicles_listing' => array(
            'name' => 'Motors - Classified Listings',
            'slug' => 'stm_vehicles_listing',
            'icon' => 'default',
			'source' => get_package( 'stm_vehicles_listing', 'zip' ),
            'required' => true,
            'version' => '5.9'
        ),
        'custom_icons_by_stylemixthemes' => array(
            'name' => 'Custom Icons by Stylemixthemes',
            'slug' => 'custom_icons_by_stylemixthemes',
            'icon' => 'default',
            'source' => $plugins_path . '/custom_icons_by_stylemixthemes.zip',
            'required' => true,
            'version' => '1.4'
        ),
        'stm_importer' => array(
            'name' => 'STM Importer',
            'slug' => 'stm_importer',
            'icon' => 'stm-configurations',
			'source' => $plugins_path . '/stm_importer.zip',
            'required' => true,
            'version' => '4.3'
        ),
        'js_composer' => array(
            'name' => 'WPBakery Visual Composer',
            'slug' => 'js_composer',
            'icon' => 'js_composer',
            'source' => $plugins_path . '/js_composer.zip',
            'required' => true,
            'version' => '5.4.7',
            'external_url' => 'http://vc.wpbakery.com'
        ),
        'revslider' => array(
            'name' => 'Revolution Slider',
            'slug' => 'revslider',
            'icon' => 'revslider',
            'source' => $plugins_path . '/revslider.zip',
            'required' => false,
            'version' => '5.4.7.3',
            'external_url' => 'http://www.themepunch.com/revolution/'
        ),
        'add-to-any' => array(
			'name' => 'AddToAny Share Buttons',
			'slug' => 'add-to-any',
            'icon' => 'add-to-any',
			'required' => false,
			'force_activation' => false,
		),
        'breadcrumb-navxt' => array(
            'name' => 'Breadcrumb NavXT',
            'slug' => 'breadcrumb-navxt',
            'icon' => 'breadcrumb-navxt',
            'required' => false,
            'force_activation' => false,
        ),
        'contact-form-7' => array(
            'name' => 'Contact Form 7',
            'slug' => 'contact-form-7',
            'icon' => 'contact-form-7',
            'required' => false,
            'force_activation' => false,
        ),
        'instagram-feed' => array(
            'name' => 'Instagram Feed',
            'slug' => 'instagram-feed',
            'icon' => 'instagram-feed',
            'required' => false,
            'external_url' => 'http://smashballoon.com/instagram-feed/'
        ),
        'mailchimp-for-wp' => array(
            'name' => 'MailChimp for WordPress',
            'slug' => 'mailchimp-for-wp',
            'icon' => 'mailchimp-for-wp',
            'required' => false,
            'external_url' => 'https://mc4wp.com/'
        ),
        'accesspress-social-counter' => array(
            'name' => 'AccessPress Social Counter',
            'slug' => 'accesspress-social-counter',
            'icon' => 'accesspress-social-counter',
            'required' => true,
            'external_url' => 'http://accesspressthemes.com'
        ),
        'stm_motors_events' => array(
            'name' => 'Motors - events',
            'slug' => 'stm_motors_events',
            'icon' => 'default',
            'source' => get_package( 'stm_motors_events', 'zip' ),
            'required' => true,
            'version' => '1.1'
        ),
        'stm_motors_review' => array(
            'name' => 'Motors - review',
            'slug' => 'stm_motors_review',
            'icon' => 'default',
            'source' => get_package( 'stm_motors_review', 'zip' ),
            'required' => true,
            'version' => '1.1'
        ),
        'woocommerce' => array(
            'name' => 'Woocommerce',
            'slug' => 'woocommerce',
            'icon' => 'woocommerce',
            'required' => false,
            'force_activation' => false,
        ),
        'bookly-responsive-appointment-booking-tool' => array(
            'name' => 'Bookly Lite',
            'slug' => 'bookly-responsive-appointment-booking-tool',
            'icon' => 'bookly-responsive-appointment-booking-tool',
            'required' => false,
            'force_activation' => false,
        ),
        'subscriptio' => array(
            'name' => 'Subscriptio',
            'slug' => 'subscriptio',
            'icon' => 'subscriptio',
            'source' => $plugins_path . '/subscriptio.zip',
            'version' => '2.3.8',
            'required' => true,
        ),
        'wordpress-social-login' => array(
            'name' => 'WordPress Social Login',
            'slug' => 'wordpress-social-login',
            'icon' => 'wordpress-social-login',
            'required' => true,
        ),
    );

    if($return) {
        return $plugins;
    } else {
        $config = array(
            'id' => 'tgm_message_update_new3r',
            'is_automatic' => true,
            'strings' => array(
                'nag_type' => 'update-nag'
            )
        );

        tgmpa($plugins, $config);
    }
}