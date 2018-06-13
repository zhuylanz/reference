<?php
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

if ( empty( $number_of_posts ) ) {
    $number_of_posts = 1;
}

$args = array(
    'post_type'      => 'post',
    'posts_per_page' => $number_of_posts,
    'ignore_sticky_posts' => true,
);

$r = new WP_Query( $args );

$idObj = get_category_by_slug('slide');

$args = (!empty($category_selected)) ? array('hide_empty' => true, 'include' => $category_selected) : array('hide_empty' => true, 'exclude' => array($idObj->term_id));

$catList = get_categories($args);

$ajaxUrl ='&posts_per_page=' . $number_of_posts . '&action=stm_ajax_get_recent_posts_magazine';

?>

<div id="stm_widget_recent_news" data-action="<?php echo $ajaxUrl; ?>" class="stm_widget_recent_news" >
    <div class="recent-top">
        <div class="left">
            <?php if(!empty($title)): ?>
                <h4><?php echo esc_attr($title); ?></h4>
            <?php endif; ?>
        </div>
        <div class="right">
            <ul class="cat-list recent-cat-list">
                <li class="recent_news_cat active" data-slug="all" >
                    <span class="heading-font"><?php echo esc_html__('All News', 'motors'); ?></span>
                </li>
                <?php for($q=0;$q<2;$q++) : if(isset($catList[$q])) :?>
                    <li class="recent_news_cat" data-slug="<?php echo esc_html($catList[$q]->slug); ?>">
                        <span class="heading-font"><?php echo esc_html($catList[$q]->name); ?></span>
                    </li>
                <?php endif; endfor; ?>
            </ul>
            <?php if(count($catList) > 2) :?>
                <div class="recent-show-all">
                    <span class="btn-show"></span>
                </div>
                <ul class="recent_hide_categories">
                    <?php for($c=2;$c<count($catList);$c++) : ?>
                        <li class="recent_news_cat" data-slug="<?php echo esc_html($catList[$c]->slug); ?>">
                            <span class="heading-font"><?php echo esc_html($catList[$c]->name); ?></span>
                        </li>
                    <?php endfor; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="recentNewsAnimate">
        <?php
        if($r->have_posts()) {
            while($r->have_posts()) {
                $r->the_post();
                get_template_part('partials/blog/content-list-magazine-loop');
            }
        }
        ?>
    </div>
</div>

<?php wp_reset_postdata(); ?>