<?php
$prevNext = stm_custom_prev_next(get_the_ID());

$prevImg = '';
$prevDate = '';
$prevTitle = '';
$prevLink = '';

$nextImg = '';
$nextDate = '';
$nextTitle = '';
$nextLink = '';


if(isset($prevNext['prev'])) {
    $prevImg = get_the_post_thumbnail_url($prevNext['prev']->ID, 'thumbnail');
    $prevDate = get_the_date('d M Y', $prevNext['prev']->ID);
    $prevTitle = $prevNext['prev']->post_title;
    $prevLink = $prevNext['prev']->guid;
}

if(isset($prevNext['next'])) {
    $nextImg = get_the_post_thumbnail_url($prevNext['next']->ID, 'thumbnail');
    $nextDate = get_the_date('d M Y', $prevNext['next']->ID);
    $nextTitle = $prevNext['next']->post_title;
    $nextLink = $prevNext['next']->guid;
}


?>
<div class="stm_prev_next_pagination">
    <div class="left">
        <?php if(!empty($prevLink)): ?>
        <a href="<?php echo esc_url($prevLink); ?>">
            <div class="img">
                <img src="<?php echo $prevImg; ?>" width="65" height="65" />
            </div>
            <div class="post-data">
                <div class="top">
                    <span class="pagi-label heading-font">
                        <?php echo esc_html__('PREVIOUS', 'motors'); ?>
                    </span>
                    <span class="date normal_font">
                        <?php echo $prevDate; ?>
                    </span>
                </div>
                <div class="bottom heading-font">
                    <?php echo esc_html($prevTitle); ?>
                </div>
            </div>
        </a>
        <?php endif; ?>
    </div>
    <div class="right">
        <?php if(!empty($nextLink)): ?>
            <a href="<?php echo esc_url($nextLink); ?>">
                <div class="img">
                    <img src="<?php echo $nextImg; ?>" width="65" height="65" />
                </div>
                <div class="post-data">
                    <div class="top">
                    <span class="pagi-label heading-font">
                        <?php echo esc_html__('NEXT', 'motors'); ?>
                    </span>
                        <span class="date normal_font">
                        <?php echo $nextDate; ?>
                    </span>
                    </div>
                    <div class="bottom heading-font">
                        <?php echo esc_html($nextTitle); ?>
                    </div>
                </div>
            </a>
        <?php endif; ?>
    </div>
</div>

