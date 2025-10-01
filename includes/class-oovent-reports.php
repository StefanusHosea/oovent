<?php
/**
 * Reports and Analytics
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_Reports {
    
    /**
     * Get overall statistics
     */
    public static function get_overall_stats() {
        global $wpdb;
        
        $total_events = $wpdb->get_var(
            "SELECT COUNT(DISTINCT product_id) FROM {$wpdb->prefix}oovent_tickets"
        );
        
        $total_tickets = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}oovent_tickets"
        );
        
        $checked_in_tickets = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}oovent_tickets WHERE checked_in = 1"
        );
        
        $total_revenue = $wpdb->get_var(
            "SELECT SUM(t.total) 
            FROM {$wpdb->prefix}wc_order_product_lookup t
            INNER JOIN {$wpdb->prefix}postmeta pm ON t.product_id = pm.post_id
            WHERE pm.meta_key = '_is_event' AND pm.meta_value = 'yes'"
        );
        
        return array(
            'total_events' => (int) $total_events,
            'total_tickets' => (int) $total_tickets,
            'checked_in' => (int) $checked_in_tickets,
            'pending_checkin' => (int) ($total_tickets - $checked_in_tickets),
            'checkin_rate' => $total_tickets > 0 ? round(($checked_in_tickets / $total_tickets) * 100, 2) : 0,
            'total_revenue' => $total_revenue ? floatval($total_revenue) : 0,
        );
    }
    
    /**
     * Get event-specific statistics
     */
    public static function get_event_stats($product_id) {
        global $wpdb;
        
        $tickets = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}oovent_tickets WHERE product_id = %d",
            $product_id
        ));
        
        $total = count($tickets);
        $checked_in = 0;
        
        foreach ($tickets as $ticket) {
            if ($ticket->checked_in) {
                $checked_in++;
            }
        }
        
        // Get revenue for this event
        $revenue = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(t.total) 
            FROM {$wpdb->prefix}wc_order_product_lookup t
            WHERE t.product_id = %d",
            $product_id
        ));
        
        // Get capacity
        $capacity = get_post_meta($product_id, '_event_capacity', true);
        
        return array(
            'total_tickets' => $total,
            'checked_in' => $checked_in,
            'pending' => $total - $checked_in,
            'checkin_rate' => $total > 0 ? round(($checked_in / $total) * 100, 2) : 0,
            'revenue' => $revenue ? floatval($revenue) : 0,
            'capacity' => $capacity ? (int) $capacity : null,
            'capacity_used' => $capacity ? round(($total / $capacity) * 100, 2) : null,
        );
    }
    
    /**
     * Get check-in timeline for an event
     */
    public static function get_checkin_timeline($product_id, $interval = 'hour') {
        global $wpdb;
        
        $format = $interval === 'hour' ? '%Y-%m-%d %H:00:00' : '%Y-%m-%d';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE_FORMAT(checked_in_at, %s) as period, COUNT(*) as count
            FROM {$wpdb->prefix}oovent_tickets
            WHERE product_id = %d AND checked_in = 1
            GROUP BY period
            ORDER BY period",
            $format,
            $product_id
        ));
        
        return $results;
    }
    
    /**
     * Get top events by tickets sold
     */
    public static function get_top_events($limit = 10) {
        global $wpdb;
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT product_id, COUNT(*) as ticket_count
            FROM {$wpdb->prefix}oovent_tickets
            GROUP BY product_id
            ORDER BY ticket_count DESC
            LIMIT %d",
            $limit
        ));
        
        $events = array();
        foreach ($results as $result) {
            $product = wc_get_product($result->product_id);
            if ($product) {
                $events[] = array(
                    'product_id' => $result->product_id,
                    'name' => $product->get_name(),
                    'ticket_count' => (int) $result->ticket_count,
                    'stats' => self::get_event_stats($result->product_id),
                );
            }
        }
        
        return $events;
    }
    
    /**
     * Get recent check-ins
     */
    public static function get_recent_checkins($limit = 20) {
        global $wpdb;
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT t.*, p.post_title as event_name
            FROM {$wpdb->prefix}oovent_tickets t
            LEFT JOIN {$wpdb->prefix}posts p ON t.product_id = p.ID
            WHERE t.checked_in = 1
            ORDER BY t.checked_in_at DESC
            LIMIT %d",
            $limit
        ));
        
        return $results;
    }
    
    /**
     * Export tickets to CSV
     */
    public static function export_tickets_csv($product_id = null) {
        global $wpdb;
        
        $filename = 'oovent-tickets-' . date('Y-m-d-His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, array(
            'Ticket Number',
            'Event',
            'Attendee Name',
            'Attendee Email',
            'Order ID',
            'Checked In',
            'Checked In At',
            'Created At'
        ));
        
        // Query
        if ($product_id) {
            $tickets = $wpdb->get_results($wpdb->prepare(
                "SELECT t.*, p.post_title as event_name
                FROM {$wpdb->prefix}oovent_tickets t
                LEFT JOIN {$wpdb->prefix}posts p ON t.product_id = p.ID
                WHERE t.product_id = %d
                ORDER BY t.created_at DESC",
                $product_id
            ));
        } else {
            $tickets = $wpdb->get_results(
                "SELECT t.*, p.post_title as event_name
                FROM {$wpdb->prefix}oovent_tickets t
                LEFT JOIN {$wpdb->prefix}posts p ON t.product_id = p.ID
                ORDER BY t.created_at DESC"
            );
        }
        
        // Data
        foreach ($tickets as $ticket) {
            fputcsv($output, array(
                $ticket->ticket_number,
                $ticket->event_name,
                $ticket->attendee_name,
                $ticket->attendee_email,
                $ticket->order_id,
                $ticket->checked_in ? 'Yes' : 'No',
                $ticket->checked_in_at,
                $ticket->created_at
            ));
        }
        
        fclose($output);
        exit;
    }
}
