<?php 
/*
Plugin Name: Posts to QR code
Plugin URI: https://saifulislam.me
Description: Display QR code under any post
Version: 1.0
Author: Saiful
Author URI: https://saifulislam.me
License: GPLv2 or later
Text Domain: posts-to-qrcode
Domain Path: /languages/
*/

/*function wordcount_activation_hook(){}
register_activation_hook(__FILE__,"wordcount_activation_hook");

function wordcount_deactivation_hook(){}
register_deactivation_hook(__FILE__,"wordcount_deactivation_hook");*/

function pqrc_load_text_domain(){
    load_plugin_textdomain( 'posts-to-qrcodet', false, dirname( __FILE__ ) . "/languages" );
}
add_action('plugins_loaded', 'pqrc_load_text_domain');


function pqrc_posts_to_qr_code($content){
	
	$current_post_id = get_the_ID();
	$current_post_url = get_the_permalink($current_post_id);
	$current_post_title = get_the_title($current_post_id);
	$current_post_type = get_post_type($current_post_id);
	
	/**
	* Post tyle check
	*/
	$exclude_post_types = apply_filters('pqrc_exclude_posts_tyles', array());
	if(in_array($current_post_type, $exclude_post_types)){
		return $content;
	}
	
	/**
	* Dimention
	*/
	$height = get_option('pqrc_height');
	$width = get_option('pqrc_width');
	
	$height = $height? $height : 170;
	$width = $width? $width : 170;
	$dimention = apply_filters('pqrc_image_dimention', "{$width}x{$height}");
	
	$image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&ecc=L&qzone=1&data=%s', $dimention, $current_post_url);
	$content .= sprintf("<div class='pqrcode'><img src='%s' alt='%s' /></div>",$image_src, $current_post_title);
	return $content;
}
add_filter('the_content', 'pqrc_posts_to_qr_code');

// posts-to-qr code height and width settings from dashboard
function pqrc_settings_init(){
	add_settings_field('pqrc_width', __('QR Code width', 'posts-to-qrcode'), 'pqrc_display_width','general');
	register_setting('general','pqrc_width', array('sanitize_callback' => 'esc_attr') );
	
	add_settings_field('pqrc_height', __('QR Code Height','posts-to-qrcode'), 'pqrc_display_height', 'general');
	register_setting('general','pqrc_height', array('sanitize_callback' => 'esc_attr'));
	
}

function pqrc_display_width(){
	$width = get_option('pqrc_width');
	printf("<input type='text' id='%s' name='%s' value='%s' />", 'pqrc_width','pqrc_width',$width);
}

function pqrc_display_height(){
	$height = get_option('pqrc_height');
	printf("<input type='text' id='%s' name='%s' value='%s' />", 'pqrc_height','pqrc_height',$height);
}


add_action('admin_init','pqrc_settings_init');
