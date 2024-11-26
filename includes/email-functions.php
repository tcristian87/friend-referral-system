<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class ReferralEmailManager {
    private $site_name;
    private $currency_symbol;
    private $referral_options;

    public function __construct() {
        $this->site_name = get_bloginfo('name');
        $this->currency_symbol = get_woocommerce_currency_symbol();
        $this->referral_options = get_option('referral_options');
    }

    private function get_coupon_display($coupon_type) {
        if ($coupon_type === 'percent') {
            return '%';
        } else {
            return $this->currency_symbol;
        }
    }

    public function send_email_to_sender($coupon_code, $friend_name, $sender_name, $sender_email) {
        $coupon_value = isset($this->referral_options['coupon_amount']) && is_numeric($this->referral_options['coupon_amount']) ? $this->referral_options['coupon_amount'] : '';
        $coupon_type_display = $this->get_coupon_display($this->referral_options['coupon_type']);

        $email_subject = sprintf("%s, has used your coupon code!", $friend_name);
        $heading = "Congratulations {$sender_name},";
        $subheading = "{$friend_name} just used the coupon you sent.";
        $content_1 = "Your friend {$friend_name} has used the code you sent. This means that you get a coupon code worth {$coupon_value}{$coupon_type_display}.";
        $content_2 = "You can redeem it by clicking the code below and it will automatically be applied to your cart.";
        $footer = "Thank you for referring {$this->site_name}!";
        $footer_signature_1 = "Kind regards,";
        $footer_signature_2 = "{$this->site_name} Team";

        $logo_url = $this->get_logo_url();

        $message = $this->format_email($heading, $subheading, $content_1, $content_2, $coupon_code, $footer, $footer_signature_1, $footer_signature_2, $logo_url);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($sender_email, $email_subject, $message, $headers);
    }

    public function send_email_to_friend($coupon_code, $friend_name, $friend_email, $sender_name) {
        $coupon_value = isset($this->referral_options['coupon_amount']) && is_numeric($this->referral_options['coupon_amount']) ? $this->referral_options['coupon_amount'] : '';
        $coupon_type_display = $this->get_coupon_display($this->referral_options['coupon_type']);

        $email_subject = "{$friend_name}, a friend has sent you a coupon!";
        $heading = "Congratulations {$friend_name},";
        $subheading = "{$sender_name} has just sent you a coupon.";
        $content_1 = "Your friend {$sender_name} has sent you a coupon worth {$coupon_value}{$coupon_type_display}.";
        $content_2 = "You can use it by clicking on the code below, and it will be automatically applied in your cart.";

        $footer = "Thank you for choosing {$this->site_name}!";
        $footer_signature_1 = "With love!,";
        $footer_signature_2 = "The {$this->site_name} Team";

        $logo_url = $this->get_logo_url();

        $message = $this->format_email($heading, $subheading, $content_1, $content_2, $coupon_code, $footer, $footer_signature_1, $footer_signature_2, $logo_url);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($friend_email, $email_subject, $message, $headers);
    }

    private function get_logo_url() {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');

        if (has_custom_logo() && $logo) {
            return $logo[0];
        } else {
            return site_url() . '/wp-content/uploads/2023/06/cropped-favicon-1.png'; 
        }
    }

    private function format_email($heading, $subheading, $content_1, $content_2, $coupon_code, $footer, $footer_signature_1, $footer_signature_2, $logo_url) {
        ob_start();
    
        include  plugin_dir_path(__FILE__) . 'email-template.php';
    
        $message = ob_get_clean();
        return $message;
    }
    
}
