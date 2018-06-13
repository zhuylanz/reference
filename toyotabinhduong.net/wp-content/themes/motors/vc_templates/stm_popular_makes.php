<?php
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);
if(!empty($css)) $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '));

$args = array(
    'orderby' => 'count',
    'order' => 'DESC',
    'hide_empty' => false,
    'pad_counts' => true
);

$terms = get_terms('make', $args);
?>
<div class="stm_popular_makes_unit">
    <div class="clearfix">
        <?php if (!empty($title)): ?>
            <div class="stm_popular_makes_title">
                <h3><?php echo esc_html($title); ?></h3>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($terms)): ?>
        <div class="stm_listing_popular_makes">

            <?php $i = 0;
            foreach ($terms as $term): ?>
                <?php $image = get_term_meta($term->term_id, 'stm_image', true);
                //Getting limit for frontend without showing all
                if ($limit > $i):
                    $image = wp_get_attachment_image_src($image, 'full');
                    $category_image = (isset($image[0])) ? $image[0] : "http://motors.stylemixthemes.com/demo/wp-content/uploads/2015/12/placeholder.gif"; ?>
                    <a href="<?php echo esc_url( stm_get_listing_archive_link( array( $term->taxonomy => $term->slug ), '' ) ); ?>" class="stm_listing_popular_makes_single"
                       title="<?php echo esc_attr($term->name); ?>">
                        <div class="inner">
                            <div class="image">
                                <img src="<?php echo esc_url($category_image); ?>"
                                     alt="<?php echo esc_attr($term->name); ?>"/>
                            </div>
                            <div class="name heading-font"><?php echo esc_attr($term->name); ?> <span class="count">(<?php echo $term->count; ?>)</span></div>
                        </div>
                    </a>
                <?php else: continue; ?>
                <?php endif; ?>
                <?php $i++;
            endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="description">
        <?php echo $content; ?>
    </div>
</div>