<ul class="stm_top-menu">
<?php

	$depth = (stm_is_motorcycle()) ? 2 : 1;

	wp_nav_menu( array(
			'menu'              => 'top_bar',
			'theme_location'    => 'top_bar',
			'depth'             => $depth,
			'container'         => false,
			'menu_class'        => 'top-bar-menu clearfix',
			'items_wrap'        => '%3$s',
			'fallback_cb' => false
		)
	);
?>
</ul>