<?php
/**
* 
* 
* 
*
* @link         https://leratech.ro 
* @since        1.0
* @package      Friend Referral System
*
*
* Plugin Name:  Friend Referral System
* Description:  Implement a referral system with coupons.
* Text Domain:  friend-referral-system
* Version:      1.0
* Author:       LERATECH
* Author URI:   https://leratech.ro/
* License:      GPLv2 or later
* License URI:  http://www.gnu.org/licenses/gpl-2.0.html
* 
* 
*/

namespace LeRa\Referral_System;

if ( ! defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path( __FILE__ ) . 'admin/woocommerce-check.php';

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin/class-referral-list-table.php';
    require_once plugin_dir_path(__FILE__) . 'admin/admin-pages.php';
    require_once plugin_dir_path(__FILE__) . 'admin/admin-settings.php';
    require_once plugin_dir_path(__FILE__) . 'admin/admin-functions.php';
    
    new \LeRa\Admin_Settings\Referral_Settings();
    new \LeRa\Admin_Pages\Referral_Admin_Pages();
    
} 

 // Ensure WooCommerce is active
if ( class_exists('WooCommerce_Checker') && !\LeRa\Woo_Check\WooCommerce_Checker::is_woocommerce_active() ) {
    return;
}

require_once plugin_dir_path(__FILE__) . 'includes/coupon-functions.php';

require_once plugin_dir_path(__FILE__) . 'includes/assets.php';

require_once plugin_dir_path(__FILE__) . 'includes/coupon-management.php';

require_once plugin_dir_path(__FILE__) . 'public/shortcode-referral-content.php';

require_once plugin_dir_path(__FILE__) . 'includes/email-functions.php';

require_once plugin_dir_path(__FILE__) . 'includes/db-functions.php';

require_once plugin_dir_path(__FILE__) . 'admin/admin-settings.php';


register_activation_hook(__FILE__, __NAMESPACE__.'\referral_system_activation');


function referral_system_activation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'referral_coupons';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_email VARCHAR(255) NOT NULL,
        sender_name VARCHAR(255) NOT NULL,
        sender_coupon VARCHAR(255) NOT NULL,
        friend_email VARCHAR(255) NOT NULL,
        friend_name VARCHAR(255) NOT NULL,
        friend_coupon VARCHAR(255) NOT NULL,
        friend_coupon_status VARCHAR(255) NOT NULL,
        sent_date TIMESTAMP NOT NULL,
        coupon_expiry_date TIMESTAMP NOT NULL
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
};
