<?php

function motors_layout_plugins($layout, $get_layouts = false)
{
    $required = array(
        'stm-post-type',
        'stm_vehicles_listing',
        'custom_icons_by_stylemixthemes',
        'stm_importer',
        'js_composer',
        'revslider',
        'add-to-any',
        'breadcrumb-navxt',
        'contact-form-7',
        'instagram-feed',
        'mailchimp-for-wp',
    );

    $plugins = array(
        'car_magazine' => array(
            'accesspress-social-counter',
            'stm_motors_events',
            'stm_motors_review'
        ),
        'service' => array(
            'bookly-responsive-appointment-booking-tool',
        ),
        'listing' => array(
            'subscriptio',
            'wordpress-social-login',
            'woocommerce'
        ),
        'car_dealer' => array(
            'woocommerce',
        ),
        'car_dealer_two' => array(
            'woocommerce',
        ),
        'motorcycle' => array(
            'woocommerce',
        ),
        'boats' => array(
            'woocommerce',
        ),
        'car_rental' => array(
            'woocommerce',
        )
    );

    if ($get_layouts) return $plugins;

    return array_merge($required, $plugins[$layout]);
}