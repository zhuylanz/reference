<?php
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '));

if (empty($posts_per_page)) {
    $posts_per_page = 6;
}

$args = array(
    'post_type' => 'product',
    'posts_per_page' => intval($posts_per_page),
    'post_status' => 'publish',
    'tax_query' => array(
        array(
            'taxonomy' => 'product_type',
            'field' => 'slug',
            'terms' => 'car_option',
            'operator' => 'NOT IN'
        )
    )
);

$offices = new WP_Query($args);

if ($offices->have_posts()): ?>
    <div class="stm_products_grid_class">
        <?php while ($offices->have_posts()): $offices->the_post();
            $id = get_the_ID();
            $s_title = get_post_meta($id, 'cars_info', true);

            $car_info = stm_get_car_rent_info($id);

            $product = new WC_Product($id);
            $price = $product->get_price();

            ?>

            <div class="stm_product_grid_single">
                <a href="<?php echo esc_url(stm_woo_shop_page_url() . esc_attr('#product-' . get_the_ID())); ?>" class="inner">
                    <div class="stm_top clearfix">
                        <div class="stm_left heading-font">
                            <h3><?php the_title(); ?></h3>
                            <?php if (!empty($s_title)): ?>
                                <div class="s_title"><?php echo sanitize_text_field($s_title); ?></div>
                            <?php endif; ?>

                            <?php if (!empty($price)): ?>
                                <div class="price">
                                    <mark><?php esc_html_e('From', 'motors'); ?></mark>
                                    <?php echo sprintf( __('%s/day', 'motors'), wc_price($price) ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($car_info)): ?>
                            <div class="stm_right">
                                <?php foreach ($car_info as $slug => $info):
                                    $name = $info['value'];
                                    if ($info['numeric']) {
                                        $name = $info['value'] . ' ' . esc_html__($info['name'], 'motors');
                                    }
                                    $font = $info['font'];
                                    ?>
                                    <div class="single_info stm_single_info_font_<?php echo esc_attr($font) ?>">
                                        <i class="<?php echo esc_attr($font); ?>"></i>
                                        <span><?php echo sanitize_text_field($name); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (has_post_thumbnail()): ?>
                        <div class="stm_image">
                            <?php the_post_thumbnail('stm-img-796-466', array('class' => 'img-responsive')); ?>
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
    <?php
    wp_reset_postdata();
endif; ?>