<?php
$id = get_the_ID();
$date_start = get_post_meta($id, 'date_start', true);
$date_prev = (!empty($date_start)) ? motors_get_formatted_date($date_start, 'd M Y') : '';

//activeEvent
?>
<div class="event-loop" data-id="<?php echo $id?>">
    <div class="img">
        <?php echo get_the_post_thumbnail($id,'thumbnail'); ?>
    </div>
    <div class="event-data">
        <div class="event-title">
            <h5><?php the_title(); ?></h5>
        </div>
        <div class="magazine-loop-Date">
            <i class="stm-icon-ico_mag_calendar"></i>
            <div class="normal-font"><?php echo $date_prev; ?></div>
        </div>
    </div>
</div>
