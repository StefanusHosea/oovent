<?php
/**
 * Uninstall OOvent
 * 
 * This file is executed when the plugin is deleted via the WordPress admin
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Option to delete all data on uninstall
// Set to false if you want to keep data after uninstall
$delete_data = false;

if ($delete_data) {
    // Delete tables
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}oovent_tickets");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}oovent_checkin_logs");
    
    // Delete options
    delete_option('oovent_version');
    delete_option('oovent_ticket_email_subject');
    delete_option('oovent_ticket_email_heading');
    delete_option('oovent_enable_qr_codes');
    delete_option('oovent_qr_code_size');
    delete_option('oovent_ticket_prefix');
    delete_option('oovent_enable_checkin_notifications');
    
    // Delete post meta for all products
    $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE '_event%' OR meta_key = '_is_event'");
    
    // Delete uploaded QR codes
    $upload_dir = wp_upload_dir();
    $qr_dir = $upload_dir['basedir'] . '/oovent-qr-codes';
    
    if (file_exists($qr_dir)) {
        $files = glob($qr_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($qr_dir);
    }
}
