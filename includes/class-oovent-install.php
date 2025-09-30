<?php
/**
 * Installation related functions and actions
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_Install {
    
    /**
     * Hook in methods
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'check_version'), 5);
    }
    
    /**
     * Check plugin version and run installer if needed
     */
    public static function check_version() {
        if (!defined('IFRAME_REQUEST') && get_option('oovent_version') !== OOVENT_VERSION) {
            self::install();
            do_action('oovent_updated');
        }
    }
    
    /**
     * Install OOvent
     */
    public static function activate() {
        if (!is_blog_installed()) {
            return;
        }
        
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('OOvent requires WooCommerce to be installed and active.');
        }
        
        self::install();
        
        do_action('oovent_activated');
    }
    
    /**
     * Deactivate OOvent
     */
    public static function deactivate() {
        do_action('oovent_deactivated');
    }
    
    /**
     * Install plugin
     */
    private static function install() {
        if (!is_blog_installed()) {
            return;
        }
        
        // Create tables
        self::create_tables();
        
        // Create default options
        self::create_options();
        
        // Update version
        update_option('oovent_version', OOVENT_VERSION);
        
        // Set default settings
        self::create_default_settings();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Set up the database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $tables = array(
            // Tickets table
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}oovent_tickets (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                order_id bigint(20) NOT NULL,
                product_id bigint(20) NOT NULL,
                ticket_number varchar(100) NOT NULL,
                qr_code_url text,
                attendee_name varchar(255),
                attendee_email varchar(255),
                checked_in tinyint(1) DEFAULT 0,
                checked_in_at datetime DEFAULT NULL,
                checked_in_by bigint(20) DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY ticket_number (ticket_number),
                KEY order_id (order_id),
                KEY product_id (product_id)
            ) $charset_collate;",
            
            // Check-in logs table
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}oovent_checkin_logs (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                ticket_id bigint(20) NOT NULL,
                user_id bigint(20) NOT NULL,
                action varchar(50) NOT NULL,
                notes text,
                ip_address varchar(100),
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY ticket_id (ticket_id),
                KEY user_id (user_id)
            ) $charset_collate;"
        );
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        foreach ($tables as $table) {
            dbDelta($table);
        }
    }
    
    /**
     * Default options
     */
    private static function create_options() {
        add_option('oovent_version', OOVENT_VERSION);
    }
    
    /**
     * Create default settings
     */
    private static function create_default_settings() {
        $default_settings = array(
            'oovent_ticket_email_subject' => 'Your Event Ticket - {event_name}',
            'oovent_ticket_email_heading' => 'Your Event Ticket',
            'oovent_enable_qr_codes' => 'yes',
            'oovent_qr_code_size' => '300',
            'oovent_ticket_prefix' => 'TICK',
            'oovent_enable_checkin_notifications' => 'yes',
        );
        
        foreach ($default_settings as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
}

OOvent_Install::init();
