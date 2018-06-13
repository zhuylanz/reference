<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

global $wpdb;
$options = $wpdb->get_results("SELECT * FROM " . $wpdb->options . " WHERE option_name LIKE '%stm_taxonomy_listing_image%'");

$theme = stm_get_theme_info();
$theme_name = $theme['name'];
$patched = get_option('stm_category_image_patched', '');
$stm_price_num_patched = get_option('stm_price_num_patched', '0');
$listings_created = count($options);
?>
<div class="wrap about-wrap stm-admin-wrap  stm-admin-support-screen">
    <?php stm_get_admin_tabs('patching'); ?>
    <?php if(empty($patched)): ?>
    <div class="stm-admin-important-notice stm-admin-important-patch-notice">

        <p class="about-description">
            <div><p><?php esc_html_e( 'Categories needs to be updated. Please click the button below and wait.', 'motors' ); ?></p></div>
    <form id="stm_start_price_patch">
        <input type="hidden" id="stm_price_patch_offset" name="offset" value="<?php echo intval($stm_price_num_patched); ?>">
        <input type="hidden" name="action" value="stm_admin_patch_cat_image">
        <button type="submit" class="button button-large button-primary stm-admin-button">
            <?php esc_html_e('Patch', 'motors'); ?>
        </button>
    </form>
    </p>
</div>
    <div class="stm-patch-price-stat">
        <?php if(!is_wp_error($listings_created)):
            if(!empty($listings_created)): ?>
                <h4><?php esc_html_e('Patch in progress, dont reload the page.' , 'motors') ?></h4>
                <span class="offset"><?php echo intval($stm_price_num_patched) ?></span>/<span class="total"><?php echo intval($listings_created); ?></span>
            <?php endif;
        endif; ?>
    </div>
<?php else: ?>
    <p class="about-description">
    <h4><?php esc_html_e('Your categories are up-to-date.', 'motors') ?></h4>
    </p>
<?php endif; ?>
</div>


<!--Patching price code-->
<script type="text/javascript">
    jQuery(document).ready(function() {
        var $ = jQuery;
        $('#stm_start_price_patch').on('submit', function(e) {
            e.preventDefault();
            var offset = $(this).find('#stm_price_patch_offset').val();

            $('.stm-admin-important-patch-notice').slideUp();
            $('.stm-patch-price-stat').slideDown();

            stmCatImgPatchingAjax(offset);
        });

        function stmCatImgPatchingAjax(offset) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    'offset' : offset,
                    'action' : 'stm_admin_patch_cat_image'
                },
                method: 'POST',
                dataType: 'json',
                context: this,
                success: function(data){
                    if(data.offset != 'none') {
                        $('.stm-patch-price-stat .offset').text(data.offset);
                        stmCatImgPatchingAjax(data.offset);
                    } else {
                        window.location.reload();
                    }
                }
            });
        }
    });
</script>