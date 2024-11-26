<?php 
namespace LeRa\Referral_Content; 

if ( ! defined( 'ABSPATH' ) ) exit;


class ReferralSystem {
 
    public function __construct() {
        add_shortcode('referral_shortcode', array($this, 'display_referral_content'));
    }

    public function display_referral_content() {
        global $wpdb;
        $output = '';


        $currency_symbol = get_woocommerce_currency_symbol();
        $referral_options = get_option('referral_options');

        $coupon_value = isset($referral_options['coupon_amount']) && is_numeric($referral_options['coupon_amount']) ? esc_html($referral_options['coupon_amount']) : '';
        $coupon_type_display = isset($referral_options['coupon_type']) && $referral_options['coupon_type'] === 'percent' ? '%' : esc_html($currency_symbol);
        
        if (!isset($_POST['submit'])) {
            $output .= $this->generate_form($coupon_value, $coupon_type_display);
        } else {
            $output .= $this->process_form_submission();
        }

        return $output;
    }

    private function generate_form($coupon_value, $coupon_type_display) {
        $licenseValid = \LeRa\Admin_Settings\LicenseManager::getInstance()->isLicenseValid();
        // // Ensure WooCommerce is active
        \LeRa\Woo_Check\WooCommerce_Checker::ensure_woocommerce_active();
        if (!empty($coupon_value) && $licenseValid) {
            $form_html = '<div class="refer_form_wrapper">';
            $form_html .= '<form class="refer_a_friend" method="post" action="">';
	        $form_html .= wp_nonce_field('referral_form_action', 'referral_form_nonce', true, false); // Nonce for Security
            $form_html .= '<div class="form-row name-email-input">';
            $form_html .= '<input type="text" class="referer_input friend_name" name="friend_name" placeholder="' . esc_attr__("Your friend's names", 'friend-referral-system') . '" required>';
            $form_html .= '<input type="email" class="referer_input friend_email" name="friend_email" placeholder="' . esc_attr__("Your friend\'s email", 'friend-referral-system').'" required>';
            $form_html .= '</div>';
            /* translators: %1s %2s dynamic display the coupon value and the  currency */
            $form_html .= '<input type="submit" class="refer_submit" name="submit" value="'. sprintf(esc_html__("Give %1\$s %2\$s off" , 'friend-referral-system'), esc_html($coupon_value) , esc_html($coupon_type_display)) . '">';
            $form_html .= '</form>';
            $form_html .= '</div>';
            return $form_html;
        } else {
            $form_html = '<div class="notice notice-warning">';
            $form_html .= '<p>' . esc_html__('The referral system is currently unavailable. Please contact the site administrator for more details.', 'friend-referral-system') . '</p>';
            $form_html .= '</div>';
            return $form_html;
        }
    }
    

    private function process_form_submission() {
        global $wpdb;
        $output = '';

		// Verify nonce for CSRF protection

	    if(!isset($_POST['referral_form_nonce']) || !wp_verify_nonce($_POST['referral_form_nonce'], 'referral_form_action')){
	    	return '<p>' . esc_html__("Security check failed. Please try again", 'friend-referral-system');
	    }

        $friend_name = isset($_POST['friend_name']) ? sanitize_text_field(wp_unslash($_POST['friend_name'])): '';
        $friend_email = isset($_POST['friend_email']) ? sanitize_email(wp_unslash($_POST['friend_email'])) : '';
        $sender_email = esc_html(wp_get_current_user()->user_email);

        $existing_user_id = email_exists($friend_email);
        $existing_referral = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}referral_coupons WHERE sender_email = %s AND friend_email = %s", $sender_email, $friend_email));

        if ($existing_user_id) {
            $output .= '<div class="refer_form_wrapper"><p class="error-message">' . esc_html__("A user with this email " , "friend-referral-system") .  $friend_email .  esc_html__("is already registered. Please recommend us to another friend. ", "friend-referral-system").'</p></div>';
        } elseif ($existing_referral) {
            $output .= '<div class="refer_form_wrapper"><p class="error-message">'. esc_html__("You have already sent a coupon to ", "friend-referral-system") . $friend_email . esc_html__(". Please recommend us to another friend." , "friend-referral-system").'</p></div>';
        } else {
            $couponManager = new \CouponManager();


            $coupon_code = $couponManager->generate_referral_coupon($friend_email);
            $refer_params = array(
                'sender_name' => wp_get_current_user()->display_name,
                'sender_email' => $sender_email,
                'friend_name' => $friend_name,
                'friend_email' => $friend_email,
                'coupon_code' => $coupon_code,
            );
            $referralManager = new \ReferralDBManager();
            $referralManager->insert_referral($refer_params);
            
            $emailManager = new \ReferralEmailManager();
            $emailManager->send_email_to_friend($coupon_code, $friend_name, $friend_email, esc_html(wp_get_current_user()->display_name));

            $output .= '<div class="refer_form_wrapper"><span>'. esc_html__("The coupon code has been sent to your friend!", "friend-referral-system").'</span>';
			$output .=  '<button type="button" id="code_copy" value="' . esc_attr($coupon_code) . '">' . esc_html($coupon_code) . '</button>' ;
			$output  .= '<small class="code_copy_status">'. esc_html__("Click on the coupon code to copy it.", "friend-referral-system") .' </small></div>';
        }

        return $output;
    }

}

new ReferralSystem();