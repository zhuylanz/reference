<?php
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '));

if (!empty($filter_selected)):

    $filter_selected_info = stm_get_all_by_slug($filter_selected);
    $multiply = false;
    if (!empty($filter_selected_info['listing_rows_numbers'])) {
        $multiply = true;
    }

    $args = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => false,
        'pad_counts' => true
    );

    $terms = get_terms($filter_selected, $args);

    $terms_images = array();
    $terms_text = array();
    if (!empty($terms)) {
        foreach ($terms as $term) {
            $image = get_term_meta($term->term_id, 'stm_image', true);
            if (empty($image)) {
                $terms_text[] = $term;
            } else {
                $terms_images[] = $term;
            }
        };
    }

    if (empty($limit)) {
        $limit = 20;
    }
    ?>
    <div class="stm_icon_filter_unit">
        <div class="clearfix">
            <?php if (!empty($duration)): ?>
                <div class="stm_icon_filter_label">
                    <?php echo esc_attr($duration); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($content)): ?>
                <div class="stm_icon_filter_title"><?php echo wpb_js_remove_wpautop($content, true); ?></div>
            <?php endif; ?>
        </div>

        <?php if (!empty($terms)): ?>
            <div
                class="stm_listing_icon_filter stm_listing_icon_filter_<?php echo esc_attr($per_row); ?> text-<?php echo esc_attr($align); ?>">

                <?php
                $icon_visiblity = 'non-visible';
                $i = 0;
                foreach ($terms_images as $term): ?>
                    <?php $image = get_term_meta($term->term_id, 'stm_image', true);
                    //Getting limit for frontend without showing all
                    if ($limit > $i):
                        $image = wp_get_attachment_image_src($image, 'stm-img-190-132');
                        $category_image = $image[0]; ?>
                        <a href="<?php echo esc_url( stm_get_listing_archive_link( array( $filter_selected => $term->slug ), $multiply ) ); ?>" class="stm_listing_icon_filter_single"
                           title="<?php echo esc_attr($term->name); ?>">
                            <div class="inner">
                                <div class="image">
                                    <img src="<?php echo esc_url($category_image); ?>"
                                         alt="<?php echo esc_attr($term->name); ?>"/>
                                </div>
                                <div class="name"><?php echo esc_attr($term->name); ?> <span
                                        class="count">(<?php echo $term->count; ?>)</span></div>
                            </div>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo stm_get_listing_archive_link( array( $filter_selected => $term->slug ) ); ?>"
                           class="stm_listing_icon_filter_single non-visible"
                           title="<?php echo esc_attr($term->name); ?>">
                            <div class="inner">
                                <div class="name">
                                    <?php echo esc_attr($term->name); ?>
                                    <span class="count">(<?php echo $term->count; ?>)</span></div>
                            </div>
                        </a>
                    <?php endif; ?>
                    <?php $i++; ?>
                <?php endforeach; ?>
                <?php foreach ($terms_text as $term): ?>
                    <a href="<?php echo esc_url( stm_get_listing_archive_link( array( $filter_selected => $term->slug ), $multiply ) ); ?>"
                       class="stm_listing_icon_filter_single <?php echo esc_attr($icon_visiblity); ?>"
                       title="<?php echo esc_attr($term->name); ?>">
                        <div class="inner">
                            <div class="name">
                                <?php echo esc_attr($term->name); ?>
                                <span class="count">(<?php echo $term->count; ?>)</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php
endif;
?>