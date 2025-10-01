<?php
/**
 * Ticket Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_Ticket {
    
    /**
     * Single instance
     */
    protected static $_instance = null;
    
    /**
     * Main instance
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
        add_action('woocommerce_order_status_completed', array($this, 'generate_tickets'));
        add_action('woocommerce_order_status_processing', array($this, 'generate_tickets'));
    }
    
    /**
     * Generate tickets for an order
     */
    public function generate_tickets($order_id) {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return;
        }
        
        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item->get_product_id();
            $product = wc_get_product($product_id);
            
            // Check if product is an event
            $is_event = get_post_meta($product_id, '_is_event', true);
            
            if ($is_event !== 'yes') {
                continue;
            }
            
            $quantity = $item->get_quantity();
            
            // Generate ticket for each quantity
            for ($i = 0; $i < $quantity; $i++) {
                $this->create_ticket($order_id, $product_id, $order);
            }
        }
        
        // Send ticket email
        do_action('oovent_tickets_generated', $order_id);
        OOvent_Email::send_ticket_email($order_id);
    }
    
    /**
     * Create a single ticket
     */
    public function create_ticket($order_id, $product_id, $order) {
        global $wpdb;
        
        // Check if ticket already exists for this combination
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}oovent_tickets 
            WHERE order_id = %d AND product_id = %d",
            $order_id, $product_id
        ));
        
        // Generate unique ticket number
        $ticket_number = $this->generate_ticket_number();
        
        // Generate QR code
        $enable_qr = get_post_meta($product_id, '_event_enable_qr', true);
        $qr_code_url = '';
        
        if ($enable_qr === 'yes' || $enable_qr === '') {
            $qr_code_url = OOvent_QR_Code::save_locally($ticket_number);
        }
        
        // Get attendee info
        $attendee_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $attendee_email = $order->get_billing_email();
        
        // Insert ticket
        $wpdb->insert(
            $wpdb->prefix . 'oovent_tickets',
            array(
                'order_id' => $order_id,
                'product_id' => $product_id,
                'ticket_number' => $ticket_number,
                'qr_code_url' => $qr_code_url,
                'attendee_name' => $attendee_name,
                'attendee_email' => $attendee_email,
                'checked_in' => 0,
                'created_at' => current_time('mysql'),
            ),
            array('%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s')
        );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Generate unique ticket number
     */
    private function generate_ticket_number() {
        global $wpdb;
        
        $prefix = get_option('oovent_ticket_prefix', 'TICK');
        
        do {
            $number = $prefix . '-' . strtoupper(wp_generate_password(12, false));
            
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}oovent_tickets WHERE ticket_number = %s",
                $number
            ));
        } while ($exists > 0);
        
        return $number;
    }
    
    /**
     * Get tickets by order ID
     */
    public static function get_tickets_by_order($order_id) {
        global $wpdb;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}oovent_tickets WHERE order_id = %d",
            $order_id
        ));
    }
    
    /**
     * Get ticket by ticket number
     */
    public static function get_ticket_by_number($ticket_number) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}oovent_tickets WHERE ticket_number = %s",
            $ticket_number
        ));
    }
    
    /**
     * Get tickets by product ID
     */
    public static function get_tickets_by_product($product_id) {
        global $wpdb;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}oovent_tickets WHERE product_id = %d",
            $product_id
        ));
    }
}
