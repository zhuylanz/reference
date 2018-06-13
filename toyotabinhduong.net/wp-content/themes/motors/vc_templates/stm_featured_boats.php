<?php
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
$css_class = (!empty($css)) ? apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' ')) : '';

if(empty($per_page)) {
	$per_page = 4;
}

$args = array(
	'post_type' => stm_listings_post_type(),
	'post_status' => 'publish',
	'posts_per_page' => intval($per_page)
);

$args['meta_query'][] = array(
	'key'     => 'special_car',
	'value'   => 'on',
	'compare' => '='
);

$special_query = new WP_Query($args);

?>

<div class="stm-featured-boats <?php echo esc_attr($css_class); ?>">
	<?php if($special_query->have_posts()): ?>
		<div class="stm-featured-boats-units row row-4 car-listing-row">
			<?php while($special_query->have_posts()): $special_query->the_post(); ?>
				<?php get_template_part('partials/car-filter', 'loop'); ?>
			<?php endwhile; ?>
		</div>
		<div class="text-center">
			<div class="dp-in">
				<a class="button" href="<?php echo esc_url(stm_get_listing_archive_link()); ?>">
					<?php esc_html_e('View All Inventory', 'motors'); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php wp_reset_postdata(); ?>