<?php
/**
 * Check-in Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_Checkin {
    
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
        // Add hooks if needed
    }
    
    /**
     * Check in a ticket
     */
    public static function check_in($ticket_number, $user_id = null, $notes = '') {
        global $wpdb;
        
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        // Validate ticket
        $validation = OOvent_QR_Code::validate($ticket_number);
        
        if (!$validation['valid']) {
            return $validation;
        }
        
        $ticket = $validation['ticket'];
        
        // Update ticket
        $updated = $wpdb->update(
            $wpdb->prefix . 'oovent_tickets',
            array(
                'checked_in' => 1,
                'checked_in_at' => current_time('mysql'),
                'checked_in_by' => $user_id,
            ),
            array('id' => $ticket->id),
            array('%d', '%s', '%d'),
            array('%d')
        );
        
        if ($updated === false) {
            return array(
                'success' => false,
                'message' => __('Failed to check in ticket', 'oovent')
            );
        }
        
        // Log check-in
        self::log_checkin($ticket->id, $user_id, 'checkin', $notes);
        
        // Get updated ticket info
        $ticket = OOvent_Ticket::get_ticket_by_number($ticket_number);
        $product = wc_get_product($ticket->product_id);
        
        do_action('oovent_ticket_checked_in', $ticket, $user_id);
        
        return array(
            'success' => true,
            'message' => __('Ticket checked in successfully', 'oovent'),
            'ticket' => $ticket,
            'event_name' => $product ? $product->get_name() : '',
            'attendee_name' => $ticket->attendee_name,
        );
    }
    
    /**
     * Undo check-in
     */
    public static function undo_checkin($ticket_number, $user_id = null, $notes = '') {
        global $wpdb;
        
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $ticket = OOvent_Ticket::get_ticket_by_number($ticket_number);
        
        if (!$ticket) {
            return array(
                'success' => false,
                'message' => __('Ticket not found', 'oovent')
            );
        }
        
        // Update ticket
        $updated = $wpdb->update(
            $wpdb->prefix . 'oovent_tickets',
            array(
                'checked_in' => 0,
                'checked_in_at' => null,
                'checked_in_by' => null,
            ),
            array('id' => $ticket->id),
            array('%d', '%s', '%d'),
            array('%d')
        );
        
        if ($updated === false) {
            return array(
                'success' => false,
                'message' => __('Failed to undo check-in', 'oovent')
            );
        }
        
        // Log undo
        self::log_checkin($ticket->id, $user_id, 'undo_checkin', $notes);
        
        do_action('oovent_ticket_checkin_undone', $ticket, $user_id);
        
        return array(
            'success' => true,
            'message' => __('Check-in undone successfully', 'oovent')
        );
    }
    
    /**
     * Log check-in action
     */
    private static function log_checkin($ticket_id, $user_id, $action, $notes = '') {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'oovent_checkin_logs',
            array(
                'ticket_id' => $ticket_id,
                'user_id' => $user_id,
                'action' => $action,
                'notes' => $notes,
                'ip_address' => self::get_ip_address(),
                'created_at' => current_time('mysql'),
            ),
            array('%d', '%d', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Get IP address
     */
    private static function get_ip_address() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    /**
     * Get check-in stats for product
     */
    public static function get_stats($product_id) {
        global $wpdb;
        
        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}oovent_tickets WHERE product_id = %d",
            $product_id
        ));
        
        $checked_in = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}oovent_tickets WHERE product_id = %d AND checked_in = 1",
            $product_id
        ));
        
        return array(
            'total' => (int) $total,
            'checked_in' => (int) $checked_in,
            'pending' => (int) ($total - $checked_in),
            'percentage' => $total > 0 ? round(($checked_in / $total) * 100, 2) : 0,
        );
    }
}
