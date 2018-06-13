<?php
$postId = get_the_ID();

$price = stm_listing_price_view(get_post_meta($postId, 'stm_genuine_price', true));
$hwy = get_post_meta($postId, 'highway_mpg', true);
$cwy = get_post_meta($postId, 'sity_mpg', true);

$reviewId = get_post_id_by_meta_k_v('review_car', $postId);

if(!is_null($reviewId)) {

    $performance = get_post_meta($reviewId, 'performance', true);
    $comfort = get_post_meta($reviewId, 'comfort', true);
    $interior = get_post_meta($reviewId, 'interior', true);
    $exterior = get_post_meta($reviewId, 'exterior', true);

    $ratingSumm = (($performance + $comfort + $interior + $exterior) / 4);

}

?>
<div class="middle_info <?php if(!is_null($reviewId)) echo 'middle-rating'; ?>">
    <div class="car_info">
        <?php if(!empty($startAt)): ?>
            <div class="starting-at normal-font">
                <?php echo esc_html__('Starting at', 'stm_motors_review'); ?>
            </div>
        <?php endif; ?>
        <div class="price heading-font">
            <?php echo esc_html($price); ?>
        </div>
        <?php if(empty($startAt)): ?>
            <div class="mpg normal-font">
                <?php echo esc_html($hwy) . esc_html__('Hwy', 'stm_motors_review') . ' / ' . esc_html($cwy) . esc_html__('City', 'stm_motors_review'); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if(!is_null($reviewId)) :?>
        <div class="rating">
            <div class="rating-stars">
                <i class="rating-empty"></i>
                <i class="rating-color" style="width: <?php echo $ratingSumm * 20; ?>%;"></i>
            </div>
            <div class="rating-text heading-font">
                <?php echo sprintf(esc_html__('%s out of 5.0', 'stm_motors_review'), $ratingSumm); ?>
            </div>
            <div class="rating-details-popup">
                <ul class="rating-params">
                    <li>
                        <span class="normal-font"><?php echo esc_html__('Performance', 'stm_motors_review')?></span>
                        <div class="rating-stars">
                            <i class="rating-empty"></i>
                            <i class="rating-color" style="width: <?php echo $performance * 20; ?>%;"></i>
                        </div>
                    </li>
                    <li>
                        <span class="normal-font"><?php echo esc_html__('Comfort', 'stm_motors_review')?></span>
                        <div class="rating-stars">
                            <i class="rating-empty"></i>
                            <i class="rating-color" style="width: <?php echo $comfort * 20; ?>%;"></i>
                        </div>
                    </li>
                    <li>
                        <span class="normal-font"><?php echo esc_html__('Interior', 'stm_motors_review')?></span>
                        <div class="rating-stars">
                            <i class="rating-empty"></i>
                            <i class="rating-color" style="width: <?php echo $interior * 20; ?>%;"></i>
                        </div>
                    </li>
                    <li>
                        <span class="normal-font"><?php echo esc_html__('Exterior', 'stm_motors_review')?></span>
                        <div class="rating-stars">
                            <i class="rating-empty"></i>
                            <i class="rating-color" style="width: <?php echo $exterior * 20; ?>%;"></i>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="no-review normal-font">
            <?php echo esc_html__('No reviews for this Vehicle', 'stm_motors_review'); ?>
        </div>
    <?php endif; ?>
</div>
