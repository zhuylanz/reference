<?php
function stm_get_layout_options($layout)
{
	$options = call_user_func('stm_theme_options_' . $layout);
	$options = json_decode($options, true);
	$options['show_page_title'] = 'false';
	return $options;
}

function stm_theme_options_car_dealer()
{
	$json = '';
	return $json;
}
function stm_theme_options_listing()
{
	$json = '';
	return $json;
}
function stm_theme_options_motorcycle()
{
	$json = '{
      "boats_header_layout": "car_dealer",
      "top_bar_enable": false,
      "header_bg_color": "#0e1315",
      "header_socials_enable": "facebook,instagram,google-plus,youtube-play",
      "socials_link": "facebook=%23&twitter=&vk=&instagram=%23&behance=&dribbble=&flickr=&git=&linkedin=&pinterest=&yahoo=&delicious=&dropbox=&reddit=&soundcloud=&google=&google-plus=%23&skype=&youtube=&youtube-play=%23&tumblr=&whatsapp=",
      "logo_width": "126",
      "typography_body_color": "#ffffff",
      "typography_menu_color": "#ffffff",
      "typography_heading_color": "#ffffff",
      "listing_directory_title_frontend": "{category_type} {condition} {ca-year} {make} {serie}",
      "show_test_drive": true,
      "show_compare": true,
      "show_share": true,
      "listing_archive": "639",
      "listing_directory_title_default": "Vehicles for sale",
      "show_listing_stock": true,
      "show_listing_compare": true,
      "show_listing_test_drive": true,
      "show_listing_quote": false,
      "show_listing_trade": false,
      "show_listing_calculate": false,
      "show_listing_vin": true,
      "listing_grid_choices": "9,12,18,27",
      "listing_grid_choice": "12",
    }';
	return $json;
}
function stm_theme_options_boats()
{
	$json = '';
	return $json;
}
function stm_theme_options_service()
{
	$json = '';
	return $json;
}
function stm_theme_options_car_rental()
{
	$json = '{
      "0": false,
      "custom_css_post_id": -1,
      "top_bar_login": false,
      "top_bar_address": "",
      "top_bar_menu": false,
      "top_bar_socials_enable": "facebook,twitter,instagram,linkedin,google-plus,youtube-play",
      "socials_link": "facebook=%23&twitter=%23&vk=%23&instagram=%23&behance=%23&dribbble=%23&flickr=%23&git=%23&linkedin=%23&pinterest=%23&yahoo=%23&delicious=%23&dropbox=&reddit=&soundcloud=&google=&google-plus=%23&skype=&youtube=&youtube-play=%23&tumblr=&whatsapp=",
      "top_bar_bg_color": "#2a4045",
      "header_bg_color": "#23393d",
      "top_bar_address_mobile": false,
      "top_bar_working_hours": "",
      "top_bar_working_hours_mobile": false,
      "top_bar_phone": "",
      "top_bar_phone_mobile": false,
      "logo_width": "126",
      "menu_top_margin": "16",
      "socials_widget_enable": "facebook,twitter,instagram,google-plus,youtube-play",
      "footer_socials_enable": "facebook,twitter",
      "footer_bg_color": "#23393d",
      "footer_copyright_color": "#23393d",
      "shop_sidebar": "0",
      "rental_datepick": "210",
      "site_bg_color": "#eeeeee",
      "top_bar_enable": true,
      "typography_body_color": "#2a4045",
      "typography_menu_color": "#232628",
      "typography_heading_color": "#2a4045",
      "order_received": "287",
      "logo_margin_top": "10"
    }';
	return $json;
}
function stm_theme_options_car_magazine()
{
	$json = '{
      "typography_body_font_family": "Open Sans",
      "typography_menu_font_family": "Montserrat",
      "typography_heading_font_family": "Montserrat",
      "typography_h1_font_size": "60",
      "logo_width": "194",
      "top_bar_enable": false,
      "header_socials_enable": "facebook,twitter",
      "socials_link": "facebook=https%3A%2F%2Ffacebook.com&twitter=https%3A%2F%2Ftwitter.com&vk=&instagram=&behance=&dribbble=&flickr=&git=&linkedin=&pinterest=&yahoo=&delicious=&dropbox=&reddit=&soundcloud=&google=&google-plus=&skype=&youtube=&youtube-play=&tumblr=&whatsapp=",
      "google_api_key": "AIzaSyD1WlC7K5CuuX55i6V45Be3o-4ic_KcG2w",
      "typography_h4_font_size": "22",
      "typography_h5_font_size": "20",
      "typography_h6_font_size": "18",
      "events_archive": "grid",
      "events_archive_paginatin_style": "pagination",
      "events_per_page": "3",
      "events_subtitle": "Find interesting trade shows & conferences to attend",
      "events_archive_sidebar_position": "right",
      "review_per_page": "4",
      "review_archive_paginatin_style": "load_more",
      "sidebar_position": "right",
      "listing_archive": "2490",
      "listing_sidebar": "no_sidebar"
    }';
	return $json;
}

function stm_theme_options_car_dealer_two()
{
	$json = '{
	"top_bar_address":"1840 E Garvey Ave South West Covina, CA 91791",
	 "top_bar_working_hours":"Work Hours",
	 "top_bar_menu":false,
	 "top_bar_socials_enable":"facebook,twitter,instagram,linkedin,google-plus,youtube-play",
	 "socials_link":"facebook=https%3A%2F%2Fwww.facebook.com%2F&twitter=https%3A%2F%2Ftwitter.com%2F&vk=&instagram=https%3A%2F%2Fwww.instagram.com%2F&behance=&dribbble=&flickr=&git=&linkedin=https%3A%2F%2Fwww.linkedin.com%2F&pinterest=&yahoo=&delicious=&dropbox=&reddit=&soundcloud=&google=&google-plus=https%3A%2F%2Fplus.google.com%2F&skype=&youtube=https%3A%2F%2Fwww.youtube.com%2F&youtube-play=https%3A%2F%2Fwww.youtube.com%2F&tumblr=&whatsapp=",
	 "listing_filter_position":"right",
	 "listing_directory_title_frontend":"{make} {serie} {ca-year} {body} {condition}",
	 "compare_page":"2144",
	 "site_demo_mode":false,
	 "footer_socials_enable":
	 "facebook,twitter,instagram,linkedin,youtube-play",
	 "show_history":true,
	 "google_api_key":"AIzaSyD1WlC7K5CuuX55i6V45Be3o-4ic_KcG2w",
	 "show_listing_share":true,
	 "show_listing_certified_logo_2":true,
	 "user_image_size_limit":"2000",
	 "distance_search":"5500",
	 "header_sticky":true,
	 "show_generated_title_as_label":true,
	 "user_premoderation":true,
	 "site_style":"site_style_default",
	 "site_style_base_color_listing":"#3350b8",
	 "site_style_secondary_color_listing":"#ffb100",
	 "site_style_base_color":"#4971ff",
	 "site_style_secondary_color":"#ffb129",
	 "site_boxed":false,
	 "typography_body_font_family":"Open Sans",
	 "custom_css":"",
	 "bg_image":"stm-background-customizer-box_img_5",
	 "enable_search":true,
	 "view_type":"grid",
	 "listing_sidebar":"no_sidebar",
	 "dealer_premoderation":false,
	 "enable_location":true,
	 "price_delimeter":" ",
	 "show_certified_logo_1":true,
	 "show_certified_logo_2":true,
	 "stm_show_number":false,
	 "dealer_post_images_limit":"4",
	 "listing_grid_choice":"9",
	 "enable_favorite_items":true,
	 "header_compare_show":true,
	 "header_cart_show":true,
	 "show_print_btn":true,
	 "top_bar_address_mobile":false,
	 "top_bar_working_hours_mobile":true,
	 "top_bar_phone_mobile":true,
	 "distance_measure_unit":"kilometers",
	 "logo_width":"140",
	 "top_bar_login":false,
	 "typography_menu_font_family":"Montserrat",
	 "typography_heading_font_family":"Montserrat",
	 "typography_body_font_size":"16",
	 "typography_h3_font_size":"24",
	 "socials_widget_enable":"facebook,twitter,instagram,dribbble,linkedin,pinterest",
	 "listing_directory_title_default":"Vehicles for sale",
	 "stm_single_car_page":"",
	 "shop_sidebar_position":"right",
	 "show_pdf":false
	 }';
	return $json;
}
