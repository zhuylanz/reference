<?php
if (empty($_COOKIE['compare_ids'])) {
    $cars_in_compare = array();
} else {
    $cars_in_compare = $_COOKIE['compare_ids'];
}


$stock_number = get_post_meta(get_the_id(), 'stock_number', true);
$car_brochure = get_post_meta(get_the_ID(), 'car_brochure', true);

$certified_logo_1 = get_post_meta(get_the_ID(), 'certified_logo_1', true);
$certified_logo_2 = get_post_meta(get_the_ID(), 'certified_logo_2', true);

//Show car actions

$show_print_btn = get_theme_mod('show_print_btn', false);

$show_stock = get_theme_mod('show_stock', true);
$show_test_drive = (!stm_is_magazine()) ? get_theme_mod('show_test_drive', true) : false;
$show_compare = get_theme_mod('show_compare', true);
$show_share = get_theme_mod('show_share', true);
$show_pdf = get_theme_mod('show_pdf', true);
$show_certified_logo_1 = get_theme_mod('show_certified_logo_1', false);
$show_certified_logo_2 = get_theme_mod('show_certified_logo_2', false);
?>

<div class="single-car-actions">
    <ul class="list-unstyled clearfix">

        <!--Stock num-->
        <?php if (!empty($stock_number) and !empty($show_stock) and $show_stock): ?>
            <li>
                <div class="stock-num heading-font"><span><?php echo esc_html__('stock', 'motors'); ?>
                        # </span><?php echo esc_attr($stock_number); ?></div>
            </li>
        <?php endif; ?>

        <!--Schedule-->
        <?php if (!empty($show_test_drive) and $show_test_drive): ?>
            <li>
                <a href="#" class="car-action-unit stm-schedule" data-toggle="modal" data-target="#test-drive">
                    <i class="stm-icon-steering_wheel"></i>
                    <?php esc_html_e('Schedule Test Drive', 'motors'); ?>
                </a>
            </li>
        <?php endif; ?>

        <!--COmpare-->
        <?php if (!empty($show_compare) and $show_compare): ?>
            <li>
                <?php if (in_array(get_the_ID(), $cars_in_compare)): ?>
                    <a
                        href="#"
                        class="car-action-unit add-to-compare stm-added"
                        data-id="<?php echo esc_attr(get_the_ID()); ?>"
                        data-action="remove">
                        <i class="stm-icon-added stm-unhover"></i>
                        <span class="stm-unhover"><?php esc_html_e('in compare list', 'motors'); ?></span>
                        <div class="stm-show-on-hover">
                            <i class="stm-icon-remove"></i>
                            <?php esc_html_e('Remove from list', 'motors'); ?>
                        </div>
                    </a>
                <?php else: ?>
                    <a
                        href="#"
                        class="car-action-unit add-to-compare"
                        data-id="<?php echo esc_attr(get_the_ID()); ?>"
                        data-action="add">
                        <i class="stm-icon-add"></i>
                        <?php esc_html_e('Add to compare', 'motors'); ?>
                    </a>
                <?php endif; ?>
            </li>
        <?php endif; ?>

        <!--PDF-->
        <?php if (!empty($show_pdf) and $show_pdf): ?>
            <?php if (!empty($car_brochure)): ?>
                <li>
                    <a
                        href="<?php echo esc_url(wp_get_attachment_url($car_brochure)); ?>"
                        class="car-action-unit stm-brochure"
                        title="<?php esc_html_e('Download brochure', 'motors'); ?>"
                        download>
                        <i class="stm-icon-brochure"></i>
                        <?php esc_html_e('Car brochure', 'motors'); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>


        <!--Share-->
        <?php if (!empty($show_share) and $show_share): ?>
            <li class="stm-shareble">

                <a
                    href="#"
                    class="car-action-unit stm-share"
                    title="<?php esc_html_e('Share this', 'motors'); ?>"
                    download>
                    <i class="stm-icon-share"></i>
                    <?php esc_html_e('Share this', 'motors'); ?>
                </a>

				<?php if( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ): ?>
				<div class="stm-a2a-popup">
					<?php echo do_shortcode('[addtoany url="'.get_the_permalink(get_the_ID()).'" title="'.get_the_title(get_the_ID()).'"]'); ?>
				</div>
				<?php endif; ?>
            </li>
        <?php endif; ?>

        <!--Print button-->
        <?php if (!empty($show_print_btn) and $show_print_btn): ?>
            <li>
                <a href="javascript:window.print()" class="car-action-unit stm-car-print heading-font">
                    <i class="fa fa-print"></i>
                    <?php echo esc_html__('Print page', 'motors'); ?>
                </a>
            </li>
        <?php endif; ?>

        <!--Certified Logo 1-->
        <?php if (!empty($certified_logo_1) and !empty($show_certified_logo_1) and $show_certified_logo_1): ?>
            <?php
            $certified_logo_1 = wp_get_attachment_image_src($certified_logo_1, 'stm-img-255-135');
            $logo_1_link = get_post_meta(get_the_ID(), 'history_link', true);
            if (!empty($certified_logo_1[0])) {
                $certified_logo_1 = $certified_logo_1[0];
            }
            ?>
            <li class="certified-logo-1">
                <?php if (!empty($logo_1_link)): ?>
                <a href="<?php echo esc_url($logo_1_link); ?>" target="_blank">
                    <?php endif; ?>
                    <img src="<?php echo esc_url($certified_logo_1); ?>"
                         alt="<?php esc_html_e('Logo 1', 'motors'); ?>"/>
                    <?php if (!empty($logo_1_link)): ?>
                </a>
            <?php endif; ?>
            </li>
        <?php endif; ?>

        <!--Certified Logo 2-->
        <?php if (!empty($certified_logo_2) and !empty($show_certified_logo_2) and $show_certified_logo_2): ?>
            <?php
            $certified_logo_2 = wp_get_attachment_image_src($certified_logo_2, 'stm-img-255-135');
            if (!empty($certified_logo_2[0])) {
                $certified_logo_2 = $certified_logo_2[0];
            }
            $logo_2_link = get_post_meta(get_the_ID(), 'certified_logo_2_link', true);
            ?>
            <li class="certified-logo-2">
                <?php if (!empty($logo_2_link)): ?>
                <a href="<?php echo esc_url($logo_2_link); ?>" target="_blank">
                    <?php endif; ?>
                    <img src="<?php echo esc_url($certified_logo_2); ?>"
                         alt="<?php esc_html_e('Logo 2', 'motors'); ?>"/>
                    <?php if (!empty($logo_2_link)): ?>
                </a>
            <?php endif; ?>
            </li>
        <?php endif; ?>

    </ul>
</div>