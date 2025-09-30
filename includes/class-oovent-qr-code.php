<?php
/**
 * QR Code Generation
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_QR_Code {
    
    /**
     * Generate QR code for ticket
     */
    public static function generate($ticket_number, $size = 300) {
        // Use Google Charts API for QR code generation (free and reliable)
        $data = urlencode($ticket_number);
        $qr_url = "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl={$data}&choe=UTF-8";
        
        // Option to use local generation in the future
        return apply_filters('oovent_qr_code_url', $qr_url, $ticket_number, $size);
    }
    
    /**
     * Save QR code image locally
     */
    public static function save_locally($ticket_number, $size = 300) {
        $upload_dir = wp_upload_dir();
        $qr_dir = $upload_dir['basedir'] . '/oovent-qr-codes';
        
        // Create directory if it doesn't exist
        if (!file_exists($qr_dir)) {
            wp_mkdir_p($qr_dir);
        }
        
        $filename = sanitize_file_name($ticket_number) . '.png';
        $filepath = $qr_dir . '/' . $filename;
        
        // Get QR code from API
        $qr_url = self::generate($ticket_number, $size);
        $qr_data = file_get_contents($qr_url);
        
        if ($qr_data) {
            file_put_contents($filepath, $qr_data);
            return $upload_dir['baseurl'] . '/oovent-qr-codes/' . $filename;
        }
        
        return false;
    }
    
    /**
     * Validate QR code data
     */
    public static function validate($ticket_number) {
        global $wpdb;
        
        $ticket = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}oovent_tickets WHERE ticket_number = %s",
            $ticket_number
        ));
        
        if (!$ticket) {
            return array(
                'valid' => false,
                'message' => __('Invalid ticket number', 'oovent')
            );
        }
        
        if ($ticket->checked_in) {
            return array(
                'valid' => false,
                'message' => __('Ticket already checked in', 'oovent'),
                'ticket' => $ticket,
                'already_checked_in' => true
            );
        }
        
        return array(
            'valid' => true,
            'ticket' => $ticket,
            'message' => __('Valid ticket', 'oovent')
        );
    }
}
