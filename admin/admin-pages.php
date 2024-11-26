<?php 
namespace LeRa\Admin_Pages;

if ( ! defined( 'ABSPATH' ) ) exit;


class Referral_Admin_Pages {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_init', array($this, 'register_license_settings'));
    }

    public function add_menu_pages() {
        add_menu_page(
            __('Referral Data', 'friend-referral-system'), 
            __('Referral Data', 'friend-referral-system'),
            'manage_options', 
            'referral_data', 
            array($this, 'display_referral_data_page'),
            'dashicons-buddicons-buddypress-logo',
            99
        );

        add_submenu_page(
            'referral_data', 
            __('Referral Settings', 'friend-referral-system'), 
            __('Settings', 'friend-referral-system'), 
            'manage_options', 
            'referral_settings', 
            array($this, 'display_referral_settings_page')
        );

        add_submenu_page(
            'referral_data',
            __('License', 'friend-referral-system'),
	        __('License', 'friend-referral-system'),
	        'manage_options',
            'referral-plugin-license',
            array($this, 'license_page_callback')
        );
        
        add_submenu_page(
            'referral_data',
	        __('Info', 'friend-referral-system'),
	        __('Info', 'friend-referral-system'),
	        'manage_options',
            'referral_info',     
            array($this,'referral_info_page')
        );
    }

    public function referral_info_page() {
        if ( !\LeRa\Woo_Check\WooCommerce_Checker::is_woocommerce_active() ) {
            echo '<div class="wrap">';
            echo '<h2>' . esc_html__("WooCommerce is not installed or not activated", "friend-referral-system").'</h2>';
            echo '</div>';
            return;
        }

        echo '<div class="wrap">';
        echo '<h2>'. esc_html__("Referral System Information", "friend-referral-system") .'</h2>';
        echo '<p> '. esc_html__("To integrate the referral system into your pages, posts, or widgets, use the following shortcode." , "friend-referral-system") .'</p>';
        echo '<br>'. esc_html__("Click on the button to copy the shortcode to your clipboard and paste it wherever you need.", "friend-referral-system") .'</p>';
        echo '<button type="button" class="button button-primary copy-shortcode-button" style="margin-left: 10px; cursor: pointer;"> '. esc_html__("Copy Shortcode","friend-referral-system").'</button><br><br>';
        echo '<input style="text-align: center;" type="text" value="[referral_shortcode]" id="referral_shortcode" readonly>';
        echo '</div>';
    }


    

    public function display_referral_data_page() {

        if ( !\LeRa\Woo_Check\WooCommerce_Checker::is_woocommerce_active() ) {
            echo '<div class="wrap">';
            echo '<h2>'. esc_html__("WooCommerce is not installed or not activated" , "friend-referral-system").'</h2>';
            echo '</div>';
            return;
        }

        $referral_table = new \LeRa\Referral_List\Referral_List_Table();
        $referral_table->prepare_items();
        ?>
        <div class="wrap">
            <h2><?php echo esc_html__('Referral Data', 'friend-referral-system'); ?></h2>
            <form method="post">
                <input type="hidden" name="page" value="referral_data">
                <?php $referral_table->display(); ?>
            </form>
        </div>
        <?php
    }

    public function display_referral_settings_page() {
        if ( !\LeRa\Woo_Check\WooCommerce_Checker::is_woocommerce_active() ) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<h2>'. esc_html__("WooCommerce is not installed or not activated", "friend-referral-system") .'</h2>';
            echo '</div>';
            return;
        }


        $licenseValid = \LeRa\Admin_Settings\LicenseManager::getInstance()->isLicenseValid();
        if (!$licenseValid) {
            echo '<div class="notice notice-warning is-dismissible">';
	        echo '<p>' . esc_html__("Your plugin license is not activated. Please activate your license to enable all features and settings. Visit the", "friend-referral-system") .
	             ' <a href="' . esc_url(admin_url('admin.php?page=referral-plugin-license')) . '">' . esc_html__("License Activation", "friend-referral-system") . '</a> ' .
	             esc_html__("page to enter your license key and activate your plugin.", "friend-referral-system") . '</p>';

	        echo '<p><a href="' . esc_url(admin_url('admin.php?page=referral-plugin-license')) . '" class="button button-primary">' . esc_html__('Check your license', 'friend-referral-system') . '</a></p>';
            echo '</div>';
            return;
        }
        
        ?>
        <div class="wrap">
            <h2><?php echo esc_html__('Referral Settings', 'friend-referral-system'); ?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('referral_settings');
                do_settings_sections('referral_settings_page');
                submit_button(esc_html__('Save Settings', 'friend-referral-system'));
                ?>
            </form>
        </div>
        <?php
        
    }

    public function license_page_callback() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form id="license_form" method="post" action="options.php">
                <?php
                  
                settings_fields('referral_license_options_group');
                
                do_settings_sections('referral-plugin-license');

                $licenseValid = \LeRa\Admin_Settings\LicenseManager::getInstance()->isLicenseValid();
                $licenseManager = \LeRa\Admin_Settings\LicenseKeyManager::getInstance();
                  if (!$licenseValid && !$licenseManager->check_if_prev_version() ) {
                        submit_button(esc_html__('Save Changes', 'friend-referral-system'));
                  }else if ($licenseManager->check_if_prev_version() && !$licenseValid){
                    submit_button(esc_html__('Check your license status again','friend-referral-system'));
                  }

                
                ?>
            </form>
        </div>
        <?php
    }
    
    public function register_license_settings() {
       
        register_setting(
            'referral_license_options_group',
            'referral_license_options', 
            array($this, 'sanitize_license_options') 
        );

       
        add_settings_section(
            'referral_license_section',
	        __('License Settings', 'friend-referral-system'),
            array($this, 'license_section_description'), 
            'referral-plugin-license' 
        );

      
        add_settings_field(
            'check_license_key',
	        __('License Settings', 'friend-referral-system'),
	        array($this, 'license_key_field_callback'),
            'referral-plugin-license', 
            'referral_license_section' 
        );
    }

    public function sanitize_license_options($input) {
        error_log('Sanitizing license options: ' . print_r($input, true));
        $new_input = [];
        if (isset($input['license_key'])) {
            $new_input['license_key'] = sanitize_text_field($input['license_key']);
        }
        if (isset($input['eS'])) {  
            $new_input['eS'] = sanitize_text_field($input['eS']);  
        }
        if (isset($input['eP'])) { 
            $new_input['eP'] = sanitize_text_field($input['eP']);
        }
        return $new_input;
    }
    
    public function license_section_description() {
        $licenseValid = \LeRa\Admin_Settings\LicenseManager::getInstance()->isLicenseValid();
        $licenseManager = \LeRa\Admin_Settings\LicenseKeyManager::getInstance();
        if (!$licenseValid && !$licenseManager->check_if_prev_version()) {
        echo '<p>'. esc_html__("Enter your license key below to activate the plugin.", "friend-referral-system").'</p>';
        }
    }

    public function license_key_field_callback() {
        $licenseValid = \LeRa\Admin_Settings\LicenseManager::getInstance()->isLicenseValid();
        $licenseManager = \LeRa\Admin_Settings\LicenseKeyManager::getInstance();
       
        $options = get_option('referral_license_options');
            $license_key = isset($options['license_key']) ? esc_attr($options['license_key']) : '';
        if (!$licenseValid && !$licenseManager->check_if_prev_version()) {
            echo '<input type="text" id="referral_license_key" data-lice="'. esc_attr($license_key) .'" name="referral_license_options[license_key]" value="' . esc_attr($license_key) . '" />';
        } else if ($licenseManager->check_if_prev_version() && !$licenseValid){
            echo '<div class="license-status">';
            echo '<p id="check_license_key" data-lice="' . esc_attr($license_key) . '"> '. esc_html__("License Key:","friend-referral-system") .'' . esc_html($license_key) . '</p>';
            echo '<p>'. esc_html__("Your license is currently", "friend-referral-system") . ' <strong style="color:#ff0000;"> ' . esc_html__("INACTIVE", "friend-referral-system") . '</strong>.</p>';
            echo '<p>'. esc_html__("Please check the status on your account at our", "friend-referral-system").' <a href="'.esc_url('https://leratech.ro/my-account').'" target="_blank">'. esc_html__("My Account", "friend-referral-system").'</a>'. esc_html__("page","friend-referral-system").'</p>';
            echo '</div>';            
        }else {
            echo '<div class="referral-settings-confirmation">';
            echo '<p id="check_license_key" data-license="' . esc_attr($license_key) . '">' . esc_html($license_key) . '</p>';
	        echo '<p>' . esc_html__('Your license is active. Thank you for your continued support!', 'friend-referral-system') . '</p>';
	        echo '<p>' . esc_html__('Remember to configure your coupon settings on the', 'friend-referral-system') . ' <a href="' . esc_url(admin_url('admin.php?page=referral_settings')) . '">' . esc_html__('Settings Page', 'friend-referral-system') . '</a>.</p>';
	        echo '<p>' . esc_html__('Explore more products at our', 'friend-referral-system') . ' <a href="' . esc_url('https://leratech.ro/shop') . '" target="_blank">' . esc_html__('Leratech Shop', 'friend-referral-system') . '</a>.</p>';
	        echo '</div>';
        }
    }
    
}


function send_license_key_to_endpoint() {
    $options = get_option('referral_license_options');
    $license_key = isset($options['license_key']) ? $options['license_key'] : null;

    if (!$license_key) {
        error_log('No license key found.');
        return; 
    }

    $endpoint = '';
    $body = array(
        'license_key' => $license_key,
    );

    $response = wp_remote_post($endpoint, array(
        'body' => $body,
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'cookies' => array()
    ));

   
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log("Something went wrong: $error_message");
    } else {
        $response_body = wp_remote_retrieve_body($response);
        error_log('Endpoint response: ' . $response_body);
    }
}
