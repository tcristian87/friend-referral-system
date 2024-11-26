<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class ReferralDBManager {
    private $wpdb;
	private string $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->wpdb->show_errors(true);
        $this->table_name = $wpdb->prefix . 'referral_coupons';
    }

    public function insert_referral($refer_params) {
        $sender_email = $refer_params['sender_email'];
        $sender_name = $refer_params['sender_name'];
        $friend_email = $refer_params['friend_email'];
        $friend_name = $refer_params['friend_name'];
        $friend_coupon = $refer_params['coupon_code'];

        $sender_coupon = '';

        $send_date = gmdate('Y-m-d');
        $send_date_time = strtotime($send_date);

        $options = get_option('referral_options');
        $expiry_date = isset($options['coupon_expiration']) && $options['coupon_expiration'] !== '' ? (int) $options['coupon_expiration'] : null;

        if ($expiry_date !== null) {
            $expiry_timestamp = $send_date_time + ($expiry_date * 24 * 60 * 60);
            $expiry_date = gmdate('Y-m-d', $expiry_timestamp);
        } else {
            $expiry_date = ''; 
        }

        $prepared_statement = $this->wpdb->prepare("INSERT INTO {$this->table_name} (sender_email, sender_name, sender_coupon, friend_email, friend_name, friend_coupon, sent_date, coupon_expiry_date) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", array(
            $sender_email,
            $sender_name,
            $sender_coupon,
            $friend_email,
            $friend_name,
            $friend_coupon,
            $send_date,
            $expiry_date
        ));

        $this->wpdb->query($prepared_statement);
    }
}
