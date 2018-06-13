<?php

$blog_show_excerpt = get_theme_mod('blog_show_excerpt', false);

?>
<div class="col-md-4 col-sm-4 col-xs-12">
	<div class="post-grid-single-unit">
		<?php if(has_post_thumbnail()): ?>
			<div class="image">
				<a href="<?php the_permalink() ?>">
					<!--Video Format-->
					<?php if(get_post_format(get_the_ID()) == 'video'): ?>
						<div class="video-preview">
							<i class="fa fa-film"></i><?php esc_html_e('Video', 'motors'); ?>
						</div>
					<?php endif; ?>
					<!--Sticky Post-->
					<?php if(is_sticky(get_the_id())): ?>
						<div class="sticky-post heading-font"><?php esc_html_e('Sticky Post','motors'); ?></div>
					<?php endif; ?>
					<?php the_post_thumbnail( 'stm-img-350-181', array( 'class' => 'img-responsive' ) ); ?>
				</a>
			</div>
		<?php else: ?>
			<?php if(is_sticky(get_the_id())): ?>
				<div class="sticky-post blog-post-no-image heading-font"><?php esc_html_e('Sticky','motors'); ?></div>
			<?php endif; ?>
		<?php endif; ?>
		<div class="content">
			<div class="title-relative">
				<a href="<?php the_permalink() ?>">
					<?php $title = stm_trim_title(85,'...'); ?>
					<?php if(!empty($title)): ?>
						<h4 class="title"><?php echo esc_attr($title); ?></h4>
					<?php endif; ?>
				</a>
			</div>
			<?php if($blog_show_excerpt): ?>
				<div class="blog-posts-excerpt">
					<?php the_excerpt(); ?>
					<div>
						<a href="<?php the_permalink(); ?>"><?php esc_html_e('Continue reading', 'motors'); ?></a>
					</div>
				</div>
			<?php endif; ?>
			<div class="post-meta-bottom">
				<div class="blog-meta-unit">
					<i class="stm-icon-date"></i>
					<span><?php echo get_the_date(); ?></span>
				</div>
				<div class="blog-meta-unit comments">
					<a href="<?php comments_link(); ?>" class="post_comments">
						<i class="stm-icon-message"></i> <?php comments_number(); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>