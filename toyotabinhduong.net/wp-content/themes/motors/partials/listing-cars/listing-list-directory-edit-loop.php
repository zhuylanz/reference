<?php
$regular_price_label = get_post_meta(get_the_ID(), 'regular_price_label', true);
$special_price_label = get_post_meta(get_the_ID(),'special_price_label',true);

$badge_text = get_post_meta(get_the_ID(),'badge_text',true);
$badge_bg_color = get_post_meta(get_the_ID(),'badge_bg_color',true);

$price = get_post_meta(get_the_id(),'price',true);
$sale_price = get_post_meta(get_the_id(),'sale_price',true);

$car_price_form_label = get_post_meta(get_the_ID(), 'car_price_form_label', true);

$data_price = '0';

if(!empty($price)) {
	$data_price = $price;
}

if(!empty($sale_price)) {
	$data_price = $sale_price;
}

$mileage = get_post_meta(get_the_id(),'mileage',true);

$data_mileage = '0';

if(!empty($mileage)) {
	$data_mileage = $mileage;
}

$special_car = get_post_meta(get_the_ID(),'special_car', true);
$gallery_video = get_post_meta(get_the_ID(), 'gallery_video', true);

$middle_infos = stm_get_car_archive_listings();

$middle_infos[] = 'location';

$total_infos = count($middle_infos);




$taxonomies = stm_get_taxonomies();

$categories = wp_get_post_terms(get_the_ID(), array_values($taxonomies));

$classes = array();


if (!empty($categories)) {
    foreach ($categories as $category) {
        $classes[] = $category->slug . '-' . $category->term_id;
    }
}

//Lat lang location
$stm_car_location = get_post_meta(get_the_ID(),'stm_car_location', true);
$stm_to_lng = get_post_meta(get_the_ID(),'stm_lng_car_admin', true);
$stm_to_lat = get_post_meta(get_the_ID(),'stm_lat_car_admin', true);


$distance = '';
if(stm_location_validates()) {

	$stm_from_lng = esc_attr(floatval($_GET['stm_lng']));
	$stm_from_lat = esc_attr(floatval($_GET['stm_lat']));

	if(!empty($stm_to_lng) and !empty($stm_to_lat)) {
		$distance = stm_calculate_distance_between_two_points( $stm_from_lat, $stm_from_lng, $stm_to_lat, $stm_to_lng );
	}
}


$show_title_two_params_as_labels = get_theme_mod('show_generated_title_as_label', true);

$car_media = stm_get_car_medias(get_the_id());
$show_compare = get_theme_mod('show_listing_compare', true);

$show_favorite = get_theme_mod('enable_favorite_items', true);

$hide_labels = get_theme_mod('hide_price_labels', true);


if($hide_labels) {
	$hide_labels = 'stm-listing-no-price-labels';
} else {
	$hide_labels = '';
}


$car_views = get_post_meta(get_the_ID(), 'stm_car_views', true);
if(empty($car_views)) {
	$car_views = 0;
}

$asSold = get_post_meta(get_the_ID(), 'car_mark_as_sold', true);

?>
<div
	class="<?php echo esc_attr($hide_labels); ?> listing-list-loop listing-list-loop-edit <?php if(!empty($asSold)) echo esc_attr('as_sold'); ?> <?php echo esc_attr(get_post_status(get_the_id())); ?> stm-listing-directory-list-loop stm-isotope-listing-item all <?php print_r(implode(' ', $classes)); ?>"
	data-price="<?php echo esc_attr($data_price) ?>"
    data-date="<?php echo get_the_date('Ymdhi') ?>"
    data-mileage="<?php echo esc_attr($data_mileage); ?>"
    <?php if(isset($distance)): ?>
        data-distance="<?php echo esc_attr(floatval($distance)); ?>"
    <?php endif; ?>
	>

		<div class="image">

			<!--Hover blocks-->
			<!---Media-->
			<div class="stm-car-medias">
				<?php if(!empty($car_media['car_photos_count'])): ?>
					<div class="stm-listing-photos-unit stm-car-photos-<?php echo get_the_id(); ?>">
						<i class="stm-service-icon-photo"></i>
						<span><?php echo $car_media['car_photos_count']; ?></span>
					</div>

					<script type="text/javascript">
						jQuery(document).ready(function(){

							jQuery(".stm-car-photos-<?php echo get_the_id(); ?>").click(function() {
								jQuery.fancybox.open([
									<?php foreach($car_media['car_photos'] as $car_photo): ?>
										{
											href  : "<?php echo esc_url($car_photo); ?>"
										},
									<?php endforeach; ?>
								], {
									padding: 0
								}); //open
							});
						});

					</script>
				<?php endif; ?>
				<?php if(!empty($car_media['car_videos_count'])): ?>
					<div class="stm-listing-videos-unit stm-car-videos-<?php echo get_the_id(); ?>">
						<i class="fa fa-film"></i>
						<span><?php echo $car_media['car_videos_count']; ?></span>
					</div>

					<script type="text/javascript">
						jQuery(document).ready(function(){

							jQuery(".stm-car-videos-<?php echo get_the_id(); ?>").click(function() {
								jQuery.fancybox.open([
									<?php foreach($car_media['car_videos'] as $car_video): ?>
									{
										href  : "<?php echo esc_url($car_video); ?>"
									},
									<?php endforeach; ?>
								], {
									type: 'iframe',
									padding: 0
								}); //open
							}); //click
						}); //ready

					</script>
				<?php endif; ?>
			</div>
			<!--Compare-->
			<?php if(!empty($show_compare) and $show_compare): ?>
				<div class="stm-listing-compare" data-id="<?php echo esc_attr(get_the_id()); ?>" data-title="<?php echo stm_generate_title_from_slugs(get_the_id(),false); ?>">
					<i class="stm-service-icon-compare-new"></i>
				</div>
			<?php endif; ?>

			<!--Favorite-->
			<?php if(!empty($show_favorite) and $show_favorite): ?>
				<div class="stm-listing-favorite" data-id="<?php echo esc_attr(get_the_id()); ?>">
					<i class="stm-service-icon-staricon"></i>
				</div>
			<?php endif; ?>

			<div class="stm-car-views">
				<i class="fa fa-eye"></i>
				<?php echo esc_attr($car_views); ?>
			</div>

			<?php if(get_post_status(get_the_id()) != 'pending'): ?>
				<div class="stm_edit_disable_car heading-font">
					<?php if($asSold == 'on'): ?>
						<a href="<?php echo esc_url(add_query_arg(array('stm_unmark_as_sold_car' => get_the_ID()), stm_get_author_link(''))); ?>" class="as_sold">
							<?php esc_html_e('Unmark as sold', 'motors'); ?><i class="fa fa-check-square-o" aria-hidden="true"></i>
						</a>
					<?php else : ?>
						<a href="<?php echo esc_url(add_query_arg(array('stm_mark_as_sold_car' => get_the_ID()), stm_get_author_link(''))); ?>">
							<?php esc_html_e('Mark as sold', 'motors'); ?><i class="fa fa-check-square-o" aria-hidden="true"></i>
						</a>
					<?php endif; ?>
					<a href="<?php echo esc_url(stm_get_add_page_url('edit', get_the_ID())); ?>">
						<?php esc_html_e('Edit', 'motors'); ?><i class="fa fa-pencil"></i>
					</a>
					<?php if(get_post_status(get_the_id()) == 'draft'): ?>
						<a href="<?php echo esc_url(add_query_arg(array('stm_enable_user_car' => get_the_ID()), stm_get_author_link(''))); ?>" class="enable_list"><?php esc_html_e('Enable', 'motors'); ?><i class="fa fa-eye"></i></a>
					<?php else: ?>
						<a href="<?php echo esc_url(add_query_arg(array('stm_disable_user_car' => get_the_ID()), stm_get_author_link(''))); ?>" class="disable_list" data-id="<?php esc_attr(get_the_ID()); ?>"><?php esc_html_e('Disable', 'motors'); ?><i class="fa fa-eye-slash"></i></a>
					<?php endif; ?>
				</div>
			<?php else: ?>
				<div class="stm_edit_pending_car">
					<h4><?php esc_html_e('Pending', 'motors'); ?></h4>
					<div class="stm-dots"><span></span><span></span><span></span></div>
					<a href="<?php echo esc_url(stm_get_add_page_url('edit', get_the_ID())); ?>">
						<?php esc_html_e('Edit', 'motors'); ?>
						<i class="fa fa-pencil"></i>
					</a>
					<a class="stm-delete-confirmation" href="<?php echo esc_url(add_query_arg(array('stm_move_trash_car' => get_the_ID()), stm_get_author_link(''))); ?>" data-title="<?php the_title(); ?>">
						<?php esc_html_e('Delete', 'motors'); ?>
						<i class="fa fa-trash-o"></i>
					</a>
				</div>
			<?php endif; ?>

			<?php if(get_post_status(get_the_id()) == 'draft'): ?>
				<div class="stm_car_move_to_trash">
					<a class="stm-delete-confirmation" href="<?php echo esc_url(add_query_arg(array('stm_move_trash_car' => get_the_ID()), stm_get_author_link(''))); ?>" data-title="<?php the_title(); ?>">
						<i class="fa fa-trash-o"></i>
					</a>
				</div>
			<?php endif; ?>

			<a href="<?php the_permalink() ?>" class="rmv_txt_drctn">
				<div class="image-inner">
					<?php if(has_post_thumbnail()): ?>
						<?php
							$img = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'stm-img-796-466');
						?>
						<img
							data-original="<?php echo esc_url($img[0]); ?>"
							src="<?php echo esc_url(get_stylesheet_directory_uri().'/assets/images/plchldr350.png'); ?>"
							class="lazy img-responsive"
							alt="<?php the_title(); ?>"
						/>

					<?php else : ?>
						<img
							src="<?php echo esc_url(get_stylesheet_directory_uri().'/assets/images/plchldr350.png'); ?>"
							class="img-responsive"
							alt="<?php esc_html_e('Placeholder', 'motors'); ?>"
						/>
					<?php endif; ?>
				</div>
			</a>
		</div>


		<div class="content">
			<div class="meta-top">
				<?php if($hide_labels and !empty($price)): ?>
					<?php
						if(!empty($sale_price)) {
							$price = $sale_price;
						};
					?>
					<div class="price">
						<div class="normal-price">
							<?php if(!empty($car_price_form_label)): ?>
								<span class="heading-font"><?php echo esc_attr($car_price_form_label); ?></span>
							<?php else: ?>
								<span class="heading-font"><?php echo esc_attr(stm_listing_price_view($price)); ?></span>
							<?php endif; ?>
						</div>
					</div>

				<?php else: ?>
					<?php if(!empty($price) and !empty($sale_price) and $price != $sale_price):?>
						<div class="price discounted-price">
							<div class="regular-price">
								<?php if(!empty($special_price_label)): ?>
									<span class="label-price"><?php echo esc_attr($special_price_label); ?></span>
								<?php endif; ?>
								<?php echo esc_attr(stm_listing_price_view($price)); ?>
							</div>

							<div class="sale-price">
								<?php if(!empty($regular_price_label)): ?>
									<span class="label-price"><?php echo esc_attr($regular_price_label); ?></span>
								<?php endif; ?>
								<span class="heading-font"><?php echo esc_attr(stm_listing_price_view($sale_price)); ?></span>
							</div>
						</div>
					<?php elseif(!empty($price)): ?>
						<div class="price">
							<div class="normal-price">
								<?php if(!empty($regular_price_label)): ?>
									<span class="label-price"><?php echo esc_attr($regular_price_label); ?></span>
								<?php endif; ?>
								<?php if(!empty($car_price_form_label)): ?>
									<span class="heading-font"><?php echo esc_attr($car_price_form_label); ?></span>
								<?php else: ?>
									<span class="heading-font"><?php echo esc_attr(stm_listing_price_view($price)); ?></span>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<div class="title heading-font">
					<a href="<?php the_permalink() ?>" class="rmv_txt_drctn">
						<?php echo stm_generate_title_from_slugs(get_the_id(),$show_title_two_params_as_labels); ?>
					</a>
				</div>
			</div>
			<?php if(!empty($middle_infos)): ?>

				<div class="meta-middle">
					<div class="meta-middle-row clearfix">
						<?php $counter = 0; ?>
						<?php foreach($middle_infos as $middle_info_key => $middle_info): ?>
							<?php
							if($middle_info != 'location'):
								$data_meta = get_post_meta(get_the_id(), $middle_info['slug'], true);
								$data_value = '';
							?>
							<?php if(!empty($data_meta) and $data_meta != 'none' and $middle_info['slug'] != 'price'):
								if(!empty($middle_info['numeric']) and $middle_info['numeric']):
									$affix = '';
									if(!empty($middle_info['number_field_affix'])) {
										$affix = esc_html__($middle_info['number_field_affix'], 'motors');
									}
									$data_value = ucfirst($data_meta) . ' ' . $affix;
								else:
									$data_meta_array = explode(',',$data_meta);
									$data_value = array();

									if(!empty($data_meta_array)){
										foreach($data_meta_array as $data_meta_single) {
											$data_meta = get_term_by('slug', $data_meta_single, $middle_info['slug']);
											if(!empty($data_meta->name)) {
												$data_value[] = esc_attr($data_meta->name);
											}
										}
									}

								endif;

							endif;
							endif //location;
							?>

							<?php if($middle_info == 'location'): $data_value = ''; ?>
								<?php if(!empty($stm_car_location) or !empty($distance)): ?>
									<div class="meta-middle-unit font-exists drive">
										<div class="meta-middle-unit-top">
											<div class="icon"><i class="stm-service-icon-pin_big"></i></div>
											<div class="name"><?php esc_html_e('Distance', 'motors'); ?></div>
										</div>

										<div class="value">
											<?php if(!empty($distance)): ?>
												<div
													class="stm-tooltip-link"
													data-toggle="tooltip"
													data-placement="bottom"
													title="<?php echo $distance; ?>">
													<?php echo $distance; ?>
												</div>

											<?php else: ?>
												<div
													class="stm-tooltip-link"
													data-toggle="tooltip"
													data-placement="bottom"
													title="<?php echo $stm_car_location; ?>">
													<?php echo $stm_car_location; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="meta-middle-unit meta-middle-divider"></div>
									<?php $counter++; ?>
								<?php endif; ?>
							<?php endif; ?>

							<?php if(!empty($data_value) and $data_value != ''): ?>


								<?php if($middle_info['slug'] != 'price' and !empty($data_meta)): ?>
									<?php $counter++; ?>
									<div class="meta-middle-unit <?php if(!empty($middle_info['font'])){ echo esc_attr('font-exists');} ?> <?php echo esc_attr($middle_info['slug']); ?>">
										<div class="meta-middle-unit-top">
											<?php if(!empty($middle_info['font'])): ?>
												<div class="icon"><i class="<?php echo esc_attr($middle_info['font']); ?>"></i></div>
											<?php endif; ?>
											<div class="name"><?php esc_html_e($middle_info['single_name'],'motors'); ?></div>
										</div>

										<div class="value">
											<?php
												if(is_array($data_value)){
													if(count($data_value) > 1) { ?>
														<div
															class="stm-tooltip-link"
															data-toggle="tooltip"
															data-placement="bottom"
															title="<?php echo esc_attr(implode(', ', $data_value)); ?>">
															<?php echo esc_attr(implode(', ', $data_value)); ?>
														</div>
													<?php } else {
														echo esc_attr(implode(', ', $data_value));
													}
												} else {
													echo esc_attr($data_value);
												}
											?>
										</div>
									</div>
									<div class="meta-middle-unit meta-middle-divider"></div>
								<?php endif; ?>


								<?php if($counter%4==0): ?>
									</div>
									<?php
										$row_no_filled = $total_infos - ($counter + 1);
										if($row_no_filled < 5) {
											$row_no_filled = 'stm-middle-info-not-filled';
										} else {
											$row_no_filled = '';
										}
									?>
									<div class="meta-middle-row <?php echo esc_attr($row_no_filled); ?> clearfix">
								<?php endif; ?>

							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="meta-bottom">
				<?php get_template_part('partials/listing-cars/listing-directive-list-loop', 'actions'); ?>
			</div>

			<a href="<?php the_permalink(); ?>" class="stm-car-view-more button visible-xs"><?php esc_html_e('View more', 'motors'); ?></a>
		</div>

</div>
