<?php
namespace LeRa\Woo_Check;


if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WooCommerce_Checker' ) ) {
    class WooCommerce_Checker {

        /**
         * Check if WooCommerce is installed and activated
         *
         * @return bool True if WooCommerce is installed and activated, false otherwise
         */
        public static function is_woocommerce_active() {
            return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
        }

        
        /**
         * Display a message if WooCommerce is not active
         *
         * @return void
         */
        public static function ensure_woocommerce_active() {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! self::is_woocommerce_active() ) {
                // Properly enqueue admin notices and escape output
                add_action( 'admin_notices', function() {
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo esc_html__( 'WooCommerce is not installed or not activated. Please install and activate WooCommerce to use this feature.', 'friend-referral-system' ); ?></p>
                    </div>
                    <?php
                });
                return;
            }
        }
    }
}

