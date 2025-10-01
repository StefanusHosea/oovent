<?php
/**
 * Admin functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_Admin {
    
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
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
    }
    
    /**
     * Add admin menu
     */
    public function add_menu() {
        add_menu_page(
            __('OOvent Events', 'oovent'),
            __('OOvent', 'oovent'),
            'manage_woocommerce',
            'oovent',
            array($this, 'dashboard_page'),
            'dashicons-tickets-alt',
            56
        );
        
        add_submenu_page(
            'oovent',
            __('Dashboard', 'oovent'),
            __('Dashboard', 'oovent'),
            'manage_woocommerce',
            'oovent',
            array($this, 'dashboard_page')
        );
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
        // This is handled by OOvent_Dashboard class
        if (class_exists('OOvent_Dashboard')) {
            OOvent_Dashboard::render();
        }
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        // Add ticket meta box to orders
        add_meta_box(
            'oovent_order_tickets',
            __('Event Tickets', 'oovent'),
            array($this, 'render_order_tickets_meta_box'),
            'shop_order',
            'normal',
            'high'
        );
        
        // Add event stats meta box to products
        add_meta_box(
            'oovent_event_stats',
            __('Event Statistics', 'oovent'),
            array($this, 'render_event_stats_meta_box'),
            'product',
            'side',
            'default'
        );
    }
    
    /**
     * Render order tickets meta box
     */
    public function render_order_tickets_meta_box($post) {
        $order_id = $post->ID;
        $tickets = OOvent_Ticket::get_tickets_by_order($order_id);
        
        if (empty($tickets)) {
            echo '<p>' . __('No event tickets for this order.', 'oovent') . '</p>';
            return;
        }
        
        echo '<table class="widefat">';
        echo '<thead><tr>';
        echo '<th>' . __('Ticket #', 'oovent') . '</th>';
        echo '<th>' . __('Event', 'oovent') . '</th>';
        echo '<th>' . __('Attendee', 'oovent') . '</th>';
        echo '<th>' . __('Status', 'oovent') . '</th>';
        echo '<th>' . __('QR Code', 'oovent') . '</th>';
        echo '</tr></thead><tbody>';
        
        foreach ($tickets as $ticket) {
            $product = wc_get_product($ticket->product_id);
            $status = $ticket->checked_in ? 
                '<span style="color: green;">✓ ' . __('Checked In', 'oovent') . '</span>' : 
                '<span style="color: orange;">○ ' . __('Pending', 'oovent') . '</span>';
            
            echo '<tr>';
            echo '<td><code>' . esc_html($ticket->ticket_number) . '</code></td>';
            echo '<td>' . esc_html($product ? $product->get_name() : 'N/A') . '</td>';
            echo '<td>' . esc_html($ticket->attendee_name) . '</td>';
            echo '<td>' . $status . '</td>';
            echo '<td>';
            if (!empty($ticket->qr_code_url)) {
                echo '<a href="' . esc_url($ticket->qr_code_url) . '" target="_blank">' . __('View QR', 'oovent') . '</a>';
            }
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    }
    
    /**
     * Render event stats meta box
     */
    public function render_event_stats_meta_box($post) {
        $product_id = $post->ID;
        $is_event = get_post_meta($product_id, '_is_event', true);
        
        if ($is_event !== 'yes') {
            echo '<p>' . __('This is not an event product.', 'oovent') . '</p>';
            return;
        }
        
        $stats = OOvent_Checkin::get_stats($product_id);
        
        echo '<div class="oovent-stats">';
        echo '<p><strong>' . __('Total Tickets:', 'oovent') . '</strong> ' . $stats['total'] . '</p>';
        echo '<p><strong>' . __('Checked In:', 'oovent') . '</strong> ' . $stats['checked_in'] . '</p>';
        echo '<p><strong>' . __('Pending:', 'oovent') . '</strong> ' . $stats['pending'] . '</p>';
        echo '<p><strong>' . __('Check-in Rate:', 'oovent') . '</strong> ' . $stats['percentage'] . '%</p>';
        
        $capacity = get_post_meta($product_id, '_event_capacity', true);
        if ($capacity) {
            $used = round(($stats['total'] / $capacity) * 100, 2);
            echo '<p><strong>' . __('Capacity Used:', 'oovent') . '</strong> ' . $stats['total'] . '/' . $capacity . ' (' . $used . '%)</p>';
        }
        
        echo '<p><a href="' . admin_url('admin.php?page=oovent-scanner&event=' . $product_id) . '" class="button button-primary">' . __('Check-in Scanner', 'oovent') . '</a></p>';
        echo '</div>';
    }
}
