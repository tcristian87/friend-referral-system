<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'send_license_key_to_endpoint');

function send_license_key_to_endpoint() {
   
    $options = get_option('referral_license_options');
    $license_key = isset($options['license_key']) ? $options['license_key'] : null;

   
    if (!$license_key) {
        error_log('No license key found.');
        return; 
    }

   
    $endpoint = 'https://example.com/api/validate_license';

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
