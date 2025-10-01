<?php
/**
 * Plugin Name: OOvent - WooCommerce Event Manager
 * Plugin URI: https://github.com/StefanusHosea/oovent
 * Description: Transform WooCommerce products into events with QR code tickets, check-in scanner, and comprehensive reporting dashboard
 * Version: 1.0.0
 * Author: OOvent Team
 * Author URI: https://github.com/StefanusHosea
 * Text Domain: oovent
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('OOVENT_VERSION', '1.0.0');
define('OOVENT_PLUGIN_FILE', __FILE__);
define('OOVENT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OOVENT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('OOVENT_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main OOvent Class
 */
final class OOvent {
    
    /**
     * The single instance of the class
     */
    protected static $_instance = null;
    
    /**
     * Main OOvent Instance
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Core includes
        require_once OOVENT_PLUGIN_DIR . 'includes/class-oovent-install.php';
        require_once OOVENT_PLUGIN_DIR . 'includes/class-oovent-event-product.php';
        require_once OOVENT_PLUGIN_DIR . 'includes/class-oovent-ticket.php';
        require_once OOVENT_PLUGIN_DIR . 'includes/class-oovent-qr-code.php';
        require_once OOVENT_PLUGIN_DIR . 'includes/class-oovent-checkin.php';
        require_once OOVENT_PLUGIN_DIR . 'includes/class-oovent-reports.php';
        require_once OOVENT_PLUGIN_DIR . 'includes/class-oovent-email.php';
        
        // Admin includes
        if (is_admin()) {
            require_once OOVENT_PLUGIN_DIR . 'includes/admin/class-oovent-admin.php';
            require_once OOVENT_PLUGIN_DIR . 'includes/admin/class-oovent-scanner.php';
            require_once OOVENT_PLUGIN_DIR . 'includes/admin/class-oovent-dashboard.php';
        }
        
        // AJAX and API
        require_once OOVENT_PLUGIN_DIR . 'includes/class-oovent-ajax.php';
    }
    
    /**
     * Hook into WordPress
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array('OOvent_Install', 'activate'));
        register_deactivation_hook(__FILE__, array('OOvent_Install', 'deactivate'));
        
        add_action('init', array($this, 'init'), 0);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }
    
    /**
     * Init OOvent when WordPress initializes
     */
    public function init() {
        // Load plugin text domain
        load_plugin_textdomain('oovent', false, dirname(OOVENT_PLUGIN_BASENAME) . '/languages');
        
        // Initialize classes
        OOvent_Event_Product::instance();
        OOvent_Ticket::instance();
        OOvent_Checkin::instance();
        OOvent_AJAX::instance();
        
        if (is_admin()) {
            OOvent_Admin::instance();
            OOvent_Scanner::instance();
            OOvent_Dashboard::instance();
        }
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_style('oovent-styles', OOVENT_PLUGIN_URL . 'assets/css/oovent.css', array(), OOVENT_VERSION);
        wp_enqueue_script('oovent-scripts', OOVENT_PLUGIN_URL . 'assets/js/oovent.js', array('jquery'), OOVENT_VERSION, true);
    }
    
    /**
     * Enqueue admin scripts
     */
    public function admin_enqueue_scripts($hook) {
        wp_enqueue_style('oovent-admin-styles', OOVENT_PLUGIN_URL . 'assets/css/admin.css', array(), OOVENT_VERSION);
        wp_enqueue_script('oovent-admin-scripts', OOVENT_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), OOVENT_VERSION, true);
        
        // Enqueue QR scanner on scanner page
        if (strpos($hook, 'oovent-scanner') !== false) {
            wp_enqueue_script('html5-qrcode', 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js', array(), '2.3.8', true);
            wp_enqueue_script('oovent-scanner', OOVENT_PLUGIN_URL . 'assets/js/scanner.js', array('jquery', 'html5-qrcode'), OOVENT_VERSION, true);
            wp_localize_script('oovent-scanner', 'ooventScanner', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('oovent_scanner_nonce')
            ));
        }
        
        // Enqueue Chart.js for dashboard
        if (strpos($hook, 'oovent-dashboard') !== false) {
            wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true);
            wp_enqueue_script('oovent-dashboard', OOVENT_PLUGIN_URL . 'assets/js/dashboard.js', array('jquery', 'chart-js'), OOVENT_VERSION, true);
        }
    }
}

/**
 * Main instance of OOvent
 */
function OOvent() {
    return OOvent::instance();
}

// Check if WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    // Initialize the plugin
    OOvent();
} else {
    // Show admin notice if WooCommerce is not active
    add_action('admin_notices', function() {
        echo '<div class="error"><p><strong>OOvent</strong> requires WooCommerce to be installed and active.</p></div>';
    });
}
