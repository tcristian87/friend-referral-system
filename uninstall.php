<?php
/** If uninstall not called from WordPress, exit */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/** Check if the current user has the required capabilities */
if ( ! current_user_can( 'manage_options' ) ) {
	exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'referral_coupons';
$table_name = esc_sql($table_name);
$wpdb->query("DROP TABLE IF EXISTS $table_name");

delete_option('referral_options');
