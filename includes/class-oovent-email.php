<?php
/**
 * Email Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_Email {
    
    /**
     * Send ticket email to customer
     */
    public static function send_ticket_email($order_id) {
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return false;
        }
        
        $tickets = OOvent_Ticket::get_tickets_by_order($order_id);
        
        if (empty($tickets)) {
            return false;
        }
        
        $to = $order->get_billing_email();
        $subject = self::get_email_subject($order);
        $message = self::get_email_message($order, $tickets);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        // Attach QR codes
        $attachments = array();
        foreach ($tickets as $ticket) {
            if (!empty($ticket->qr_code_url)) {
                $upload_dir = wp_upload_dir();
                $qr_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $ticket->qr_code_url);
                if (file_exists($qr_path)) {
                    $attachments[] = $qr_path;
                }
            }
        }
        
        return wp_mail($to, $subject, $message, $headers, $attachments);
    }
    
    /**
     * Get email subject
     */
    private static function get_email_subject($order) {
        $subject = get_option('oovent_ticket_email_subject', 'Your Event Ticket - {event_name}');
        
        // Get first event product name
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            if (get_post_meta($product_id, '_is_event', true) === 'yes') {
                $product = wc_get_product($product_id);
                $subject = str_replace('{event_name}', $product->get_name(), $subject);
                break;
            }
        }
        
        return $subject;
    }
    
    /**
     * Get email message
     */
    private static function get_email_message($order, $tickets) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #0073aa; color: white; padding: 20px; text-align: center; }
                .ticket { border: 2px solid #ddd; margin: 20px 0; padding: 20px; background-color: #f9f9f9; }
                .ticket-number { font-size: 18px; font-weight: bold; color: #0073aa; }
                .qr-code { text-align: center; margin: 20px 0; }
                .qr-code img { max-width: 250px; }
                .event-details { margin: 15px 0; }
                .event-details strong { display: inline-block; width: 150px; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1><?php echo get_option('oovent_ticket_email_heading', 'Your Event Ticket'); ?></h1>
                </div>
                
                <p>Dear <?php echo esc_html($order->get_billing_first_name()); ?>,</p>
                
                <p>Thank you for your purchase! Here are your event tickets:</p>
                
                <?php foreach ($tickets as $ticket): 
                    $product = wc_get_product($ticket->product_id);
                    $start_date = get_post_meta($ticket->product_id, '_event_start_date', true);
                    $location = get_post_meta($ticket->product_id, '_event_location', true);
                    $instructions = get_post_meta($ticket->product_id, '_event_instructions', true);
                ?>
                <div class="ticket">
                    <h2><?php echo esc_html($product->get_name()); ?></h2>
                    
                    <div class="event-details">
                        <p><strong>Ticket Number:</strong> <span class="ticket-number"><?php echo esc_html($ticket->ticket_number); ?></span></p>
                        <p><strong>Attendee:</strong> <?php echo esc_html($ticket->attendee_name); ?></p>
                        <?php if ($start_date): ?>
                        <p><strong>Date & Time:</strong> <?php echo esc_html($start_date); ?></p>
                        <?php endif; ?>
                        <?php if ($location): ?>
                        <p><strong>Location:</strong> <?php echo esc_html($location); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($ticket->qr_code_url)): ?>
                    <div class="qr-code">
                        <p><strong>Your QR Code Ticket:</strong></p>
                        <img src="<?php echo esc_url($ticket->qr_code_url); ?>" alt="QR Code Ticket">
                        <p><small>Please present this QR code at the event entrance</small></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($instructions): ?>
                    <div class="instructions">
                        <p><strong>Special Instructions:</strong></p>
                        <p><?php echo nl2br(esc_html($instructions)); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                
                <div class="footer">
                    <p>If you have any questions, please contact us at <?php echo get_option('admin_email'); ?></p>
                    <p>Order #<?php echo $order->get_order_number(); ?></p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
