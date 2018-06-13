/**
 * Created by Dima on 2/7/2018.
 */
(function ($) {
    "use strict";

    $(document).ready(function () {
        $('.post-content > .gallery').owlCarousel({
            items: 1,
            loop: true,
            margin: 10,
            nav: true
        });

        $('body').on('click', '.features-show-all', function () {
            $(this).toggleClass('rotate_180');
            $('.features_hiden').toggleClass('features_show');
        });

        $('body').on('click', '.recent-show-all', function () {
            $(this).toggleClass('rotate_180');
            $('.recent_hide_categories').toggleClass('active');
        });

        $('.stm_listing_popular_makes').hover(function () {
            $(".stm_listing_popular_makes a").addClass('opacity_07');
        }, function () {
            $(".stm_listing_popular_makes a").removeClass('opacity_07');
        });

        $(".stm_listing_popular_makes a").hover(function () {
            $(this).addClass('opacity_1');
        }, function () {
            $(this).removeClass('opacity_1');
        });


        $(".apsc-icons-wrapper").hover(function () {
            $(".apsc-icons-wrapper .apsc-each-profile a").addClass('opacity_07');
        }, function () {
            $(".apsc-icons-wrapper .apsc-each-profile a").removeClass('opacity_07');
        });

        $(".apsc-icons-wrapper .apsc-each-profile a").hover(function () {
            $(this).addClass('opacity_1');
        }, function () {
            $(this).removeClass('opacity_1');
        });

        $('body').on('click', '.features-cat-list li', function () {
            $('.features-cat-list li').removeClass('active');
            $(this).addClass('active');
            $.ajax({
                url: ajaxurl,
                type: "GET",
                dataType: 'json',
                data: '&category=' + $(this).data('slug') + '&action=stm_ajax_sticky_posts_magazine' + $('#features_posts_wrap').data('action'),
                context: this,
                beforeSend: function (data) {
                    $('.features_posts_wrap').addClass('opacity_07');
                },
                success: function (data) {
                    $('.features_posts_wrap').html(data.html);
                    var w = $('.adsense-200-200').width();
                    $('.adsense-200-200').height(Math.floor(w));
                    $('.features_posts_wrap').removeClass('opacity_07');
                }
            });
        });

        $('body').on('click', '.recent-cat-list li, .recent_hide_categories li', function () {

            $('.recent-show-all').toggleClass('rotate_180');
            $('.recent_hide_categories').toggleClass('active');

            $('.recent-cat-list li, .recent_hide_categories li').removeClass('active');
            $(this).addClass('active');
            $.ajax({
                url: ajaxurl,
                type: "GET",
                dataType: 'json',
                data: '&category=' + $(this).data('slug') + $('#stm_widget_recent_news').data('action'),
                context: this,
                beforeSend: function (data) {
                    $('.recentNewsAnimate').addClass('opacity_07');
                },
                success: function (data) {
                    $('.recentNewsAnimate').html(data.html);
                    $('.recentNewsAnimate').removeClass('opacity_07');
                }
            });
        });

        $('body').on('click', '.event-loop', function () {
            var postId = $(this).data('id');
            $('.event-loop').removeClass('activeEvent');
            $(this).addClass('activeEvent');

            $.ajax({
                url: ajaxurl,
                type: "GET",
                dataType: 'json',
                data: '&action=stm_ajax_get_events&post_id=' + postId,
                context: this,
                beforeSend: function (data) {
                    $('.event-content').addClass('opacity_07');
                },
                success: function (data) {
                    $('.event-content').html(data.html);
                    jQuery("[data-countdown]").each(function() {
                        var $this = jQuery(this), finalDate = $this.data('countdown');
                        $this.countdown(finalDate, function(event) {
                            $this.html(event.strftime("<span class='heading-font'>%D <small>" + countdownDay + "</small></span> "
                                + "<span class='heading-font'>%H <small>" + countdownHrs + "</small></span> "
                                + "<span class='heading-font'>%M <small>" + countdownMin + "</small></span> "
                                + "<span class='heading-font'>%S <small>" + countdownSec + "</small></span>" ));
                        });
                    });
                    setTimeout(function () {
                        $('.event-content').removeClass('opacity_07');
                    }, 200);
                }
            });
        });

        $('.events-list .event-loop:first-child').addClass('activeEvent');
    });

    $(window).load(function () {

        var fW = $('.features-big-wrap').height();
        $('.features_posts_wrap').attr('style', 'min-height: ' + fW + 'px;');

        var w = $('.adsense-200-200').width();
        $('.adsense-200-200').height(Math.floor(w));

        var owlRecent = $('.recent_videos_posts_wrap');

        owlRecent.owlCarousel({
            items: 2,
            loop: true,
            nav: false,
            dots: false,
            margin: 20,
            autoWidth:true,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 1,
                    center: true
                },
                1024: {
                    items: 2,
                    center: false
                }
            }
        });

        $('.recent-owl-nav .next').click(function() {
            owlRecent.trigger('next.owl.carousel');
        });
        $('.recent-owl-nav .prev').click(function() {
            owlRecent.trigger('prev.owl.carousel', [300]);
        });

        $('.recent-videos-wrap-loop a').on('click', function (e) {
            e.preventDefault();

            var link = $(this).attr('href');

            $.fancybox({
                'padding'       : 0,
                'autoScale'     : false,
                'transitionIn'  : 'none',
                'transitionOut' : 'none',
                'title'         : this.title,
                'href'          : link + '?autoplay=1',
                'type'          : 'iframe',
            });

            return false;

        });
    });

})(jQuery);