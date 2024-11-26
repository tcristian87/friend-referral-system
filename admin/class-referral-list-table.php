<?php 

namespace LeRa\Referral_List;

if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Referral_List_Table extends \WP_List_Table 
{
    public function __construct(){
        parent::__construct(array(
            'singular' => 'referral',
            'plural'   => 'referrals',
            'ajax'     => false
        ));
    }

    public function get_columns(){
        return array(
            'id' => 'ID',
            'sender_email' => 'Sender Email',
            'sender_name' => 'Sender Name',
            'sender_coupon' => 'Sender Coupon',
            'friend_email' => 'Friend Email',
            'friend_name' => 'Friend Name',
            'friend_coupon' => 'Friend Coupon',
            'friend_coupon_status' => 'Friend Coupon Status',
            'sent_date' => 'Sent Date',
            'coupon_expiry_date' => 'Coupon Expiry Date',
        );
    }

    public function prepare_items(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'referral_coupons';
	    $table_name = esc_sql($table_name);

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $offset = ($current_page - 1) * $per_page;

        $this->items = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name ORDER BY 'id' 'DESC' LIMIT %d OFFSET %d", $per_page, $offset),
            ARRAY_A
        );
    }

    public function column_default($item, $column_name){
        if ($column_name == 'friend_coupon_status') {
            return esc_html($this->check_coupon_usage_status($item['friend_coupon']));
        }
        return esc_html($item[$column_name]);
    }

   public function check_coupon_usage_status($coupon_code) {
        global $wpdb;

        $coupon_code = sanitize_text_field($coupon_code);
      
        $coupon_post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish'",
            $coupon_code
        ));
    
        if (!$coupon_post_id) {
            return esc_html__("Coupon not found or inactive.", "friend-referral-system");

        }
    
        $usage_count = $wpdb->get_var($wpdb->prepare(
            "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = 'usage_count'",
            $coupon_post_id
        ));
    
        if (intval($usage_count) > 0) {
            return esc_html__("Used", "firend-referral-system");
        } else {
            return esc_html__("Available", "friend-referral-system");
        }
    }
    
    public function get_sortable_columns() {
        return array(
            'id' => array('id', false),
            'sent_date' => array('sent_date', false),
            'coupon_expiry_date' => array('coupon_expiry_date', false),
        );
    }
    
    
};
