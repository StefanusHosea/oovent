<?php
/**
 * Check-in Scanner Admin Page
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_Scanner {
    
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
    }
    
    /**
     * Add admin menu
     */
    public function add_menu() {
        add_submenu_page(
            'oovent',
            __('Check-in Scanner', 'oovent'),
            __('Scanner', 'oovent'),
            'manage_woocommerce',
            'oovent-scanner',
            array($this, 'render_page')
        );
    }
    
    /**
     * Render scanner page
     */
    public function render_page() {
        ?>
        <div class="wrap oovent-scanner-page">
            <h1><?php _e('Check-in Scanner', 'oovent'); ?></h1>
            
            <div class="oovent-scanner-container">
                <div class="scanner-section">
                    <h2><?php _e('Scan QR Code', 'oovent'); ?></h2>
                    
                    <div class="scanner-tabs">
                        <button class="scanner-tab active" data-tab="camera"><?php _e('Camera Scanner', 'oovent'); ?></button>
                        <button class="scanner-tab" data-tab="manual"><?php _e('Manual Entry', 'oovent'); ?></button>
                    </div>
                    
                    <div class="scanner-content camera-scanner active">
                        <div id="reader" style="width: 100%; max-width: 600px;"></div>
                        <div id="scanner-status" class="scanner-status"></div>
                    </div>
                    
                    <div class="scanner-content manual-scanner">
                        <div class="manual-entry-form">
                            <input type="text" id="manual-ticket-number" placeholder="<?php esc_attr_e('Enter ticket number', 'oovent'); ?>" class="regular-text">
                            <button type="button" id="scan-manual-ticket" class="button button-primary"><?php _e('Check Ticket', 'oovent'); ?></button>
                        </div>
                    </div>
                </div>
                
                <div class="ticket-info-section">
                    <h2><?php _e('Ticket Information', 'oovent'); ?></h2>
                    <div id="ticket-info" class="ticket-info-box">
                        <p class="no-ticket"><?php _e('Scan a ticket to see information', 'oovent'); ?></p>
                    </div>
                    
                    <div id="checkin-actions" class="checkin-actions" style="display: none;">
                        <button type="button" id="checkin-ticket-btn" class="button button-primary button-large"><?php _e('Check In', 'oovent'); ?></button>
                        <button type="button" id="undo-checkin-btn" class="button button-secondary" style="display: none;"><?php _e('Undo Check-in', 'oovent'); ?></button>
                    </div>
                </div>
            </div>
            
            <div class="recent-checkins-section">
                <h2><?php _e('Recent Check-ins', 'oovent'); ?></h2>
                <table class="widefat" id="recent-checkins-table">
                    <thead>
                        <tr>
                            <th><?php _e('Time', 'oovent'); ?></th>
                            <th><?php _e('Ticket #', 'oovent'); ?></th>
                            <th><?php _e('Event', 'oovent'); ?></th>
                            <th><?php _e('Attendee', 'oovent'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="recent-checkins-body">
                        <?php
                        $recent = OOvent_Reports::get_recent_checkins(10);
                        if (empty($recent)) {
                            echo '<tr><td colspan="4">' . __('No recent check-ins', 'oovent') . '</td></tr>';
                        } else {
                            foreach ($recent as $checkin) {
                                echo '<tr>';
                                echo '<td>' . esc_html($checkin->checked_in_at) . '</td>';
                                echo '<td><code>' . esc_html($checkin->ticket_number) . '</code></td>';
                                echo '<td>' . esc_html($checkin->event_name) . '</td>';
                                echo '<td>' . esc_html($checkin->attendee_name) . '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <style>
            .oovent-scanner-page {
                max-width: 1200px;
            }
            .oovent-scanner-container {
                display: flex;
                gap: 20px;
                margin: 20px 0;
            }
            .scanner-section, .ticket-info-section {
                flex: 1;
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .scanner-tabs {
                margin-bottom: 20px;
                border-bottom: 1px solid #ccd0d4;
            }
            .scanner-tab {
                padding: 10px 20px;
                background: none;
                border: none;
                border-bottom: 3px solid transparent;
                cursor: pointer;
                font-size: 14px;
            }
            .scanner-tab.active {
                border-bottom-color: #0073aa;
                font-weight: 600;
            }
            .scanner-content {
                display: none;
            }
            .scanner-content.active {
                display: block;
            }
            .manual-entry-form {
                display: flex;
                gap: 10px;
                align-items: center;
            }
            #manual-ticket-number {
                flex: 1;
            }
            .scanner-status {
                margin-top: 20px;
                padding: 15px;
                border-radius: 4px;
            }
            .scanner-status.success {
                background: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
            }
            .scanner-status.error {
                background: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
            }
            .scanner-status.warning {
                background: #fff3cd;
                border: 1px solid #ffeeba;
                color: #856404;
            }
            .ticket-info-box {
                min-height: 200px;
                padding: 20px;
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .ticket-info-box .no-ticket {
                text-align: center;
                color: #666;
                margin-top: 80px;
            }
            .ticket-detail {
                margin: 10px 0;
                padding: 10px;
                background: #fff;
                border-left: 3px solid #0073aa;
            }
            .ticket-detail strong {
                display: inline-block;
                width: 150px;
            }
            .checkin-actions {
                margin-top: 20px;
                text-align: center;
            }
            .checkin-actions button {
                margin: 5px;
            }
            .recent-checkins-section {
                margin-top: 30px;
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .status-badge {
                padding: 4px 8px;
                border-radius: 3px;
                font-size: 12px;
                font-weight: 600;
            }
            .status-badge.checked-in {
                background: #d4edda;
                color: #155724;
            }
            .status-badge.pending {
                background: #fff3cd;
                color: #856404;
            }
        </style>
        <?php
    }
}
