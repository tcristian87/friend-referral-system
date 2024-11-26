<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action('template_redirect', 'apply_coupon_from_url');

function apply_coupon_from_url()
{

    if (is_admin() || is_ajax()) {
        return; 
    }
    
    $url_path = wp_parse_url(wp_unslash(isset($_SERVER['REQUEST_URI'])), PHP_URL_PATH);
    $url_segments = explode('/', rtrim($url_path, '/'));

    if (in_array('refer', $url_segments) && count($url_segments) > 2) {
        $coupon_code = sanitize_text_field($url_segments[2]);

      

        if (is_coupon_valid($coupon_code)) {
          
            error_log($coupon_code);
            if (function_exists('WC') && WC()->cart) {
                
                WC()->cart->apply_coupon($coupon_code);
            }

            wp_safe_redirect(wc_get_cart_url());
            exit;
        }
    }
}

function is_coupon_valid($coupon_code)
{
    $coupon = new \WC_Coupon($coupon_code);
    $discounts = new \WC_Discounts(WC()->cart);
    $response = $discounts->is_coupon_valid($coupon);

    return is_wp_error($response) ? false : true;
}
