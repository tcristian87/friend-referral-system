<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class CouponManager {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    private function generate_coupon_code_string() {
        $seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ' . '0123456789');
        shuffle($seed);
        $coupon_code = '';
        foreach (array_rand($seed, 14) as $k) $coupon_code .= $seed[$k];
        return $coupon_code;
    }

    private function check_coupon_exists($coupon_code) {
        $existing_coupon_codes = $this->wpdb->get_col("SELECT post_name FROM {$this->wpdb->posts} WHERE post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_name ASC");
        return in_array(strtolower($coupon_code), $existing_coupon_codes);
    }

    private function create_coupon($coupon_code, $coupon_amount, $coupon_type, $expiry_date, $email) {
        $coupon = [
            'post_title' => $coupon_code,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        ];
        $new_coupon_id = wp_insert_post($coupon);
        update_post_meta($new_coupon_id, 'discount_type', $coupon_type);
        update_post_meta($new_coupon_id, 'coupon_amount', $coupon_amount);
        update_post_meta($new_coupon_id, 'individual_use', 'yes');
        update_post_meta($new_coupon_id, 'exclude_sale_items', 'no');
        update_post_meta($new_coupon_id, 'usage_limit', '1');
        update_post_meta($new_coupon_id, 'usage_limit_per_user', '1');
        update_post_meta($new_coupon_id, 'customer_email', [$email]);
        update_post_meta($new_coupon_id, 'expiry_date', $expiry_date);
        update_post_meta($new_coupon_id, 'apply_before_tax', 'yes');
        update_post_meta($new_coupon_id, 'free_shipping', 'no');
        
        $coupon = new \WC_Coupon($new_coupon_id);
        $coupon->save_meta_data();

        return $coupon_code;
    }

    public function generate_sender_coupon($sender_email) {
        $coupon_code = $this->generate_coupon_code_string();
        while ($this->check_coupon_exists($coupon_code)) {
            $coupon_code = $this->generate_coupon_code_string();
        }

        $options = get_option('referral_options');
        $amount = $options['coupon_amount'] ?? '';
        $discount_type = $options['coupon_type'] ?? '';
        $expiry_date = $this->calculate_expiry_date($options['coupon_expiration'] ?? null);

        return $this->create_coupon($coupon_code, $amount, $discount_type, $expiry_date, $sender_email);
    }

    public function generate_referral_coupon($referral_email) {
        return $this->generate_sender_coupon($referral_email); 
    }

    private function calculate_expiry_date($days) {
        if ($days !== null) {
            $now = strtotime(gmdate('Y-m-d'));
            $expiry_timestamp = $now + ($days * 24 * 60 * 60);
            return gmdate('Y-m-d', $expiry_timestamp);
        }
        return '';
    }
}

