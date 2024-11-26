<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function referral_enqueue_styles() {
    $css_file_path = plugin_dir_url(dirname(__FILE__)) . 'public/css/style.css';
    wp_register_style('referral-style', $css_file_path, array(), '1.0');
    wp_enqueue_style('referral-style');

    $script_url = plugin_dir_url(dirname(__FILE__)) . 'public/js/custom.js';
    wp_enqueue_script('referral-custom-js', $script_url, array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'referral_enqueue_styles');


function referral_admin_scripts() {

    wp_enqueue_script('jquery');
    wp_enqueue_script('referral-admin-script', plugin_dir_url(dirname(__FILE__)). '/admin/js/admin-script.js', array('jquery'), '1.0', true);
    wp_enqueue_style('admin-style', plugin_dir_url(dirname(__FILE__)). '/admin/css/style.css', array(), '1.0');

}
add_action('admin_enqueue_scripts', 'referral_admin_scripts');

