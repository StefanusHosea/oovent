<?php
/**
 * AJAX Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_AJAX {
    
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
        add_action('wp_ajax_oovent_scan_ticket', array($this, 'scan_ticket'));
        add_action('wp_ajax_oovent_checkin_ticket', array($this, 'checkin_ticket'));
        add_action('wp_ajax_oovent_undo_checkin', array($this, 'undo_checkin'));
        add_action('wp_ajax_oovent_get_ticket_info', array($this, 'get_ticket_info'));
    }
    
    /**
     * Scan ticket
     */
    public function scan_ticket() {
        check_ajax_referer('oovent_scanner_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'oovent')));
        }
        
        $ticket_number = isset($_POST['ticket_number']) ? sanitize_text_field($_POST['ticket_number']) : '';
        
        if (empty($ticket_number)) {
            wp_send_json_error(array('message' => __('Ticket number is required', 'oovent')));
        }
        
        $validation = OOvent_QR_Code::validate($ticket_number);
        
        if ($validation['valid']) {
            $ticket = $validation['ticket'];
            $product = wc_get_product($ticket->product_id);
            
            wp_send_json_success(array(
                'message' => __('Valid ticket', 'oovent'),
                'ticket' => $ticket,
                'event_name' => $product ? $product->get_name() : '',
                'attendee_name' => $ticket->attendee_name,
                'attendee_email' => $ticket->attendee_email,
            ));
        } else {
            wp_send_json_error(array(
                'message' => $validation['message'],
                'already_checked_in' => isset($validation['already_checked_in']) ? $validation['already_checked_in'] : false,
                'ticket' => isset($validation['ticket']) ? $validation['ticket'] : null,
            ));
        }
    }
    
    /**
     * Check in ticket
     */
    public function checkin_ticket() {
        check_ajax_referer('oovent_scanner_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'oovent')));
        }
        
        $ticket_number = isset($_POST['ticket_number']) ? sanitize_text_field($_POST['ticket_number']) : '';
        $notes = isset($_POST['notes']) ? sanitize_text_field($_POST['notes']) : '';
        
        if (empty($ticket_number)) {
            wp_send_json_error(array('message' => __('Ticket number is required', 'oovent')));
        }
        
        $result = OOvent_Checkin::check_in($ticket_number, get_current_user_id(), $notes);
        
        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
    
    /**
     * Undo check-in
     */
    public function undo_checkin() {
        check_ajax_referer('oovent_scanner_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'oovent')));
        }
        
        $ticket_number = isset($_POST['ticket_number']) ? sanitize_text_field($_POST['ticket_number']) : '';
        $notes = isset($_POST['notes']) ? sanitize_text_field($_POST['notes']) : '';
        
        if (empty($ticket_number)) {
            wp_send_json_error(array('message' => __('Ticket number is required', 'oovent')));
        }
        
        $result = OOvent_Checkin::undo_checkin($ticket_number, get_current_user_id(), $notes);
        
        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
    
    /**
     * Get ticket info
     */
    public function get_ticket_info() {
        check_ajax_referer('oovent_scanner_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'oovent')));
        }
        
        $ticket_number = isset($_POST['ticket_number']) ? sanitize_text_field($_POST['ticket_number']) : '';
        
        if (empty($ticket_number)) {
            wp_send_json_error(array('message' => __('Ticket number is required', 'oovent')));
        }
        
        $ticket = OOvent_Ticket::get_ticket_by_number($ticket_number);
        
        if (!$ticket) {
            wp_send_json_error(array('message' => __('Ticket not found', 'oovent')));
        }
        
        $product = wc_get_product($ticket->product_id);
        
        wp_send_json_success(array(
            'ticket' => $ticket,
            'event_name' => $product ? $product->get_name() : '',
        ));
    }
}
