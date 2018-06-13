<?php
    $class = '';
    $fields = stm_get_rental_order_fields_values();

    if(stm_check_rental_date_validation()) {
        $class = 'passed';
    }

    $page = get_queried_object();
    if(!empty($page->ID)) {
        $page = $page->ID;
    }

    $url = get_theme_mod('rental_datepick', '');

    if($page == $url) {
        $class .= ' current';
    }

    $url = get_permalink($url);

?>

<div class="inner <?php echo esc_attr($class); ?>">
    <a href="<?php echo esc_url($url); ?>" class="top heading-font">
        <div class="number">
            <span>1</span>
        </div>
        <label><?php esc_html_e('Your Itinerary', 'motors'); ?></label>
    </a>
    <div class="content">
        <div class="first">
            <h5><?php esc_html_e('Pick up', 'motors'); ?></h5>
            <div class="stm_filled_pickup_location"><?php echo sanitize_text_field($fields['pickup_location']); ?></div>
            <div class="stm_filled_pickup_date"><?php echo sanitize_text_field($fields['pickup_date']); ?></div>
        </div>
        <div class="second">
            <h5 class="second"><?php esc_html_e('Drop off', 'motors'); ?></h5>
            <div class="stm_filled_return_location"><?php echo sanitize_text_field($fields['return_location']); ?></div>
            <div class="stm_filled_return_date"><?php echo sanitize_text_field($fields['return_date']); ?></div>
        </div>
    </div>
</div>