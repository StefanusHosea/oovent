<?php
/**
 * Dashboard Admin Page
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_Dashboard {
    
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
     * Render dashboard
     */
    public static function render() {
        $stats = OOvent_Reports::get_overall_stats();
        $top_events = OOvent_Reports::get_top_events(5);
        $recent_checkins = OOvent_Reports::get_recent_checkins(10);
        ?>
        <div class="wrap oovent-dashboard">
            <h1><?php _e('OOvent Dashboard', 'oovent'); ?></h1>
            
            <div class="oovent-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['total_events']); ?></h3>
                        <p><?php _e('Total Events', 'oovent'); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🎫</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['total_tickets']); ?></h3>
                        <p><?php _e('Total Tickets', 'oovent'); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['checked_in']); ?></h3>
                        <p><?php _e('Checked In', 'oovent'); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['pending_checkin']); ?></h3>
                        <p><?php _e('Pending Check-in', 'oovent'); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['checkin_rate']); ?>%</h3>
                        <p><?php _e('Check-in Rate', 'oovent'); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <h3><?php echo wc_price($stats['total_revenue']); ?></h3>
                        <p><?php _e('Total Revenue', 'oovent'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="oovent-dashboard-grid">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><?php _e('Top Events', 'oovent'); ?></h2>
                    </div>
                    <div class="section-content">
                        <?php if (empty($top_events)): ?>
                            <p><?php _e('No events found.', 'oovent'); ?></p>
                        <?php else: ?>
                            <table class="widefat">
                                <thead>
                                    <tr>
                                        <th><?php _e('Event', 'oovent'); ?></th>
                                        <th><?php _e('Tickets Sold', 'oovent'); ?></th>
                                        <th><?php _e('Checked In', 'oovent'); ?></th>
                                        <th><?php _e('Rate', 'oovent'); ?></th>
                                        <th><?php _e('Actions', 'oovent'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_events as $event): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo esc_html($event['name']); ?></strong>
                                        </td>
                                        <td><?php echo esc_html($event['ticket_count']); ?></td>
                                        <td><?php echo esc_html($event['stats']['checked_in']); ?></td>
                                        <td><?php echo esc_html($event['stats']['checkin_rate']); ?>%</td>
                                        <td>
                                            <a href="<?php echo admin_url('post.php?post=' . $event['product_id'] . '&action=edit'); ?>" class="button button-small"><?php _e('Edit', 'oovent'); ?></a>
                                            <a href="<?php echo admin_url('admin.php?page=oovent-scanner&event=' . $event['product_id']); ?>" class="button button-small button-primary"><?php _e('Scanner', 'oovent'); ?></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><?php _e('Recent Check-ins', 'oovent'); ?></h2>
                    </div>
                    <div class="section-content">
                        <?php if (empty($recent_checkins)): ?>
                            <p><?php _e('No recent check-ins.', 'oovent'); ?></p>
                        <?php else: ?>
                            <table class="widefat">
                                <thead>
                                    <tr>
                                        <th><?php _e('Time', 'oovent'); ?></th>
                                        <th><?php _e('Event', 'oovent'); ?></th>
                                        <th><?php _e('Attendee', 'oovent'); ?></th>
                                        <th><?php _e('Ticket #', 'oovent'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_checkins as $checkin): ?>
                                    <tr>
                                        <td><?php echo esc_html(human_time_diff(strtotime($checkin->checked_in_at), current_time('timestamp'))) . ' ' . __('ago', 'oovent'); ?></td>
                                        <td><?php echo esc_html($checkin->event_name); ?></td>
                                        <td><?php echo esc_html($checkin->attendee_name); ?></td>
                                        <td><code><?php echo esc_html($checkin->ticket_number); ?></code></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-actions">
                <a href="<?php echo admin_url('admin.php?page=oovent-scanner'); ?>" class="button button-primary button-hero"><?php _e('Open Scanner', 'oovent'); ?></a>
                <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="button button-secondary button-hero"><?php _e('Manage Events', 'oovent'); ?></a>
            </div>
        </div>
        
        <style>
            .oovent-dashboard {
                max-width: 1400px;
            }
            .oovent-stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin: 30px 0;
            }
            .stat-card {
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .stat-icon {
                font-size: 40px;
            }
            .stat-content h3 {
                margin: 0;
                font-size: 32px;
                color: #0073aa;
            }
            .stat-content p {
                margin: 5px 0 0;
                color: #666;
                font-size: 14px;
            }
            .oovent-dashboard-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin: 20px 0;
            }
            .dashboard-section {
                background: #fff;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .section-header {
                padding: 15px 20px;
                border-bottom: 1px solid #ccd0d4;
                background: #f9f9f9;
            }
            .section-header h2 {
                margin: 0;
                font-size: 18px;
            }
            .section-content {
                padding: 20px;
            }
            .dashboard-actions {
                margin: 30px 0;
                text-align: center;
            }
            .dashboard-actions .button {
                margin: 0 10px;
            }
            @media (max-width: 782px) {
                .oovent-stats-grid {
                    grid-template-columns: 1fr;
                }
                .oovent-dashboard-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <?php
    }
}
