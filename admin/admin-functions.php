<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_ajax_update_license_status', 'update_license_status');
add_action('wp_ajax_nopriv_update_license_status', 'update_license_status');


function update_license_status() {

    $options = get_option('referral_license_options', []);
    if (!is_array($options)) {
        $options = [];  
    }
    
    if (isset($_POST['licenseKey'])) {
        $options['license_key'] = sanitize_text_field(wp_unslash($_POST['licenseKey']));
    }
    if (isset($_POST['eS'])) {
        $options['eS'] = sanitize_text_field(wp_unslash($_POST['eS']));
    }
    if (isset($_POST['eP'])) {
        $options['eP'] = sanitize_text_field(wp_unslash($_POST['eP']));
    }

    if (isset($_POST['stts'])){
        $options['eS'] = sanitize_text_field(wp_unslash($_POST['stts']));
    }

    update_option('referral_license_options', $options);
    wp_send_json_success('License data updated');
}

add_action('admin_init', 'skf_validation2');

function skf_validation2() {
    $options = get_option('referral_license_options');
    if (empty($options) || !isset($options['license_key'])) {
        error_log('No license key found in options');
        return;
    }

    $license_key = $options['license_key'];
    $url = 'https://www.leratech.ro/wp-json/referral/v1/validate-license-status';
    $url = add_query_arg('license_key', $license_key, $url);


    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        error_log('Error making API call: ' . $response->get_error_message());
        return;
    }

    $response_body = wp_remote_retrieve_body($response);
    $result = json_decode($response_body, true);
    $options = get_option('referral_license_options', []);
    if (!is_array($options)) {
        $options = [];  
    }
    if(!$result['valid']){
        $options['eS'] = $result['data'];
            update_option('referral_license_options', $options);
        }else {
            return;
        }

};