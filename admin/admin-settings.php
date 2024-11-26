<?php 
namespace LeRa\Admin_Settings;

if ( ! defined( 'ABSPATH' ) ) exit;

class LicenseKeyManager {
    private static $instance = null;
    private $options;
    private function __construct() {
        $this->options = get_option('referral_license_options', []);
    }
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function check_if_prev_version() {
        return !empty($this->options) && !empty($this->options['license_key']);
    }
}


class Referral_Settings {
    public function __construct() {
            $licenseValid = LicenseManager::getInstance()->isLicenseValid();
            if ($licenseValid) {
            add_action('admin_init', array($this, 'settings_init'));
        } 
    }
    public function settings_init() {
        register_setting('referral_settings', 'referral_options');

        add_settings_section(
            'referral_settings_section',
            __('General Settings', 'text-domain'),
            array($this, 'settings_section_callback'),
            'referral_settings_page'
        );

        $this->register_fields();
    }

    
    private function register_fields() {
       
        add_settings_field(
            'referral_coupon_amount',
            __('Coupon Amount', 'text-domain'),
            array($this, 'coupon_amount_callback'),
            'referral_settings_page',
            'referral_settings_section'
        );

        add_settings_field(
            'referral_coupon_type',
            __('Coupon Type', 'text-domain'),
            array($this, 'coupon_type_callback'),
            'referral_settings_page',
            'referral_settings_section'
        );

        add_settings_field(
            'referral_coupon_expiration',
            __('Coupon Expiration (days)', 'text-domain'),
            array($this, 'coupon_expiration_callback'),
            'referral_settings_page',
            'referral_settings_section'
        );

        add_settings_field(
            'referral_individual_use',
            __('Individual Use Only', 'text-domain'),
            array($this, 'individual_use_callback'),
            'referral_settings_page',
            'referral_coupon_restrictions_section'
        );

        add_settings_field(
            'referral_exclude_sale_items',
            __('Exclude Sale Items', 'text-domain'),
            array($this, 'exclude_sale_items_callback'),
            'referral_settings_page',
            'referral_coupon_restrictions_section'
        );

        add_settings_section(
            'referral_coupon_restrictions_section',
            __('Coupon Restrictions', 'text-domain'),
            array($this, 'coupon_restrictions_section_callback'), 
            'referral_settings_page'
        );

        register_setting(
            'referral_plugin_options_group', 
            'referral_options', 
            'referral_options_sanitize'
        );
        
        
    }
    
    public function referral_options_sanitize($input) {
        $new_input = array();
        if(isset($input['license_key']) && !empty($input['license_key'])) {
            $new_input['license_key'] = sanitize_text_field($input['license_key']);
        }
    
        return $new_input;
    }


    public function settings_section_callback() {
        echo '<p>' . esc_html__('Enter your general settings:', 'text-domain') . '</p>';
    }

    public function coupon_amount_callback() { 
        $options = get_option('referral_options');
        ?>
        <input type="number" name="referral_options[coupon_amount]" value="<?php echo isset($options['coupon_amount']) ? esc_attr($options['coupon_amount']) : ''; ?>" min="0" step="any" required>
        <?php
    }

   
    public function coupon_type_callback() { 
            $options = get_option('referral_options', array());

            $coupon_types = array(
                'fixed_cart' => 'Fixed cart discount',
                'percent' => 'Percentage discount',
            );
            ?>
            <select name="referral_options[coupon_type]">
                <?php
                foreach ($coupon_types as $type => $label) {
                    $selected_option = isset($options['coupon_type']) ? $options['coupon_type'] : null;
                    echo '<option value="' . esc_attr($type) . '"' . selected($selected_option, $type, false) . '>' . esc_html($label) . '</option>';
                }
                ?>
            </select>
            <?php
    }

    public function coupon_expiration_callback() {   
        $options = get_option('referral_options');
            ?>
            <input type="number" name="referral_options[coupon_expiration]" value="<?php echo isset($options['coupon_expiration']) ? esc_attr($options['coupon_expiration']) : ''; ?>" min="0" step="1">
            <p class="description">Enter the number of days the coupon is valid.</p>
            <p class="description">If the input is empty, the coupon will have no validity period.</p>

            <?php
    }

    
    public function individual_use_callback() {
        $options = get_option('referral_options');
        $checked = isset($options['individual_use']) && $options['individual_use'] === 'on' ? 'checked' : '';
        echo '<input type="checkbox" name="referral_options[individual_use]" ' . esc_attr($checked) . '> Only allow the coupon to be used individually.';
    }

    public function exclude_sale_items_callback() { 
        $options = get_option('referral_options');
        $checked = isset($options['exclude_sale_items']) && $options['exclude_sale_items'] === 'on' ? 'checked' : '';
    
        echo '<input type="checkbox" name="referral_options[exclude_sale_items]" ' . esc_attr($checked) . '> Exclude sale items from the coupon usage.';
    }

    public function coupon_restrictions_section_callback() {
        echo '<p>' . esc_html__('Configure coupon usage restrictions.', 'text-domain') . '</p>';
    }


}

    class LicenseManager {
        private static $instance = null;
        private $licenseStatusChecker;
    
        private function __construct() {
            $this->licenseStatusChecker = new LSC($this->retrieveLicenseStatus());
        }
    
        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    
        private function retrieveLicenseStatus() {
            $cfg = get_option('referral_license_options');
            if (!empty($cfg) && isset($cfg['eS'], $cfg['eP'])) {
                $encLcsSts = base64_decode($cfg['eS']);
                $key = base64_decode($cfg['eP']);
                return $this->dataProcessor($encLcsSts, $key);
            }
            return null; 
        }
        public function getPaymentDueDate() {
            $statusData = $this->retrieveLicenseStatus();
            if ($statusData) {
               
                $data = json_decode($statusData, true);
                if (is_array($data) && !empty($data[0]['payment_due_date'])) {
                    return $data[0]['payment_due_date'];
                }
            }
            return null;
        }
        
        
        private function dataProcessor($data, $privateKey) {
            $decrypted = '';
            if (openssl_private_decrypt($data, $decrypted, $privateKey)) {
                return $decrypted;
            } else {
                error_log('Data decryption failed'); 
                return false; 
            }
        }
    
        public function isLicenseValid() {
            return $this->licenseStatusChecker->check();

        }
    } 
    
    class LSC {
        private $ls;
    
        public function __construct($st) {
            $this->ls = $st;
        }
    
        public function check() {
            return (bool)$this->ls; 
        }
    };
    function slc_cj() {
        $lm = LicenseManager::getInstance();
        $pdd = $lm->getPaymentDueDate();
    
        if ($pdd) {
            $hn = 'skf_validation';
            $ct = time();
            $ft = (int) $pdd;
    
            if ($ft > $ct) {
                $se = $ft - $ct;
                $st = $ct + $se;
    
                if (!wp_next_scheduled($hn)) {
                    wp_schedule_single_event($ft, $hn);
                } 
            }
        }
    };
    
    add_action('init', __NAMESPACE__.'\slc_cj');