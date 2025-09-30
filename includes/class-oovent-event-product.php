<?php
/**
 * Event Product Type
 */

if (!defined('ABSPATH')) {
    exit;
}

class OOvent_Event_Product {
    
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
        add_filter('product_type_selector', array($this, 'add_event_product_type'));
        add_filter('woocommerce_product_data_tabs', array($this, 'add_event_product_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'add_event_product_panel'));
        add_action('woocommerce_process_product_meta', array($this, 'save_event_product_data'));
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_event_fields'));
    }
    
    /**
     * Add event product type
     */
    public function add_event_product_type($types) {
        $types['event'] = __('Event', 'oovent');
        return $types;
    }
    
    /**
     * Add event data tab
     */
    public function add_event_product_tab($tabs) {
        $tabs['event'] = array(
            'label' => __('Event Details', 'oovent'),
            'target' => 'event_product_data',
            'class' => array('show_if_event'),
            'priority' => 21,
        );
        return $tabs;
    }
    
    /**
     * Add event fields
     */
    public function add_event_fields() {
        global $post;
        
        echo '<div class="options_group show_if_event">';
        
        // Enable event checkbox
        woocommerce_wp_checkbox(array(
            'id' => '_is_event',
            'label' => __('Enable as Event', 'oovent'),
            'description' => __('Check this to enable event features for this product', 'oovent')
        ));
        
        echo '</div>';
    }
    
    /**
     * Add event product data panel
     */
    public function add_event_product_panel() {
        global $post;
        ?>
        <div id="event_product_data" class="panel woocommerce_options_panel">
            <div class="options_group">
                <?php
                // Event start date
                woocommerce_wp_text_input(array(
                    'id' => '_event_start_date',
                    'label' => __('Event Start Date', 'oovent'),
                    'placeholder' => 'YYYY-MM-DD HH:MM',
                    'desc_tip' => true,
                    'description' => __('Enter the event start date and time', 'oovent'),
                    'type' => 'datetime-local',
                ));
                
                // Event end date
                woocommerce_wp_text_input(array(
                    'id' => '_event_end_date',
                    'label' => __('Event End Date', 'oovent'),
                    'placeholder' => 'YYYY-MM-DD HH:MM',
                    'desc_tip' => true,
                    'description' => __('Enter the event end date and time', 'oovent'),
                    'type' => 'datetime-local',
                ));
                
                // Event location
                woocommerce_wp_text_input(array(
                    'id' => '_event_location',
                    'label' => __('Event Location', 'oovent'),
                    'placeholder' => __('Enter event location', 'oovent'),
                    'desc_tip' => true,
                    'description' => __('Physical or virtual location of the event', 'oovent'),
                ));
                
                // Event capacity
                woocommerce_wp_text_input(array(
                    'id' => '_event_capacity',
                    'label' => __('Event Capacity', 'oovent'),
                    'placeholder' => __('Unlimited', 'oovent'),
                    'desc_tip' => true,
                    'description' => __('Maximum number of attendees (leave empty for unlimited)', 'oovent'),
                    'type' => 'number',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min' => '0',
                    ),
                ));
                
                // Event organizer
                woocommerce_wp_text_input(array(
                    'id' => '_event_organizer',
                    'label' => __('Event Organizer', 'oovent'),
                    'placeholder' => __('Enter organizer name', 'oovent'),
                    'desc_tip' => true,
                    'description' => __('Name of the event organizer', 'oovent'),
                ));
                
                // Event type/category
                woocommerce_wp_select(array(
                    'id' => '_event_type',
                    'label' => __('Event Type', 'oovent'),
                    'options' => array(
                        'conference' => __('Conference', 'oovent'),
                        'workshop' => __('Workshop', 'oovent'),
                        'seminar' => __('Seminar', 'oovent'),
                        'meetup' => __('Meetup', 'oovent'),
                        'webinar' => __('Webinar', 'oovent'),
                        'concert' => __('Concert', 'oovent'),
                        'festival' => __('Festival', 'oovent'),
                        'sports' => __('Sports', 'oovent'),
                        'other' => __('Other', 'oovent'),
                    ),
                    'desc_tip' => true,
                    'description' => __('Select the type of event', 'oovent'),
                ));
                
                // Enable QR codes
                woocommerce_wp_checkbox(array(
                    'id' => '_event_enable_qr',
                    'label' => __('Enable QR Code Tickets', 'oovent'),
                    'description' => __('Generate QR code tickets for this event', 'oovent'),
                    'value' => get_post_meta($post->ID, '_event_enable_qr', true) ?: 'yes',
                ));
                
                // Enable check-in
                woocommerce_wp_checkbox(array(
                    'id' => '_event_enable_checkin',
                    'label' => __('Enable Check-in', 'oovent'),
                    'description' => __('Allow attendees to check-in at the event', 'oovent'),
                    'value' => get_post_meta($post->ID, '_event_enable_checkin', true) ?: 'yes',
                ));
                ?>
            </div>
            
            <div class="options_group">
                <?php
                // Event description/agenda
                woocommerce_wp_textarea_input(array(
                    'id' => '_event_agenda',
                    'label' => __('Event Agenda/Schedule', 'oovent'),
                    'placeholder' => __('Enter event agenda or schedule', 'oovent'),
                    'desc_tip' => true,
                    'description' => __('Additional details about the event schedule', 'oovent'),
                ));
                
                // Special instructions
                woocommerce_wp_textarea_input(array(
                    'id' => '_event_instructions',
                    'label' => __('Special Instructions', 'oovent'),
                    'placeholder' => __('Enter any special instructions for attendees', 'oovent'),
                    'desc_tip' => true,
                    'description' => __('Instructions that will be included in the ticket email', 'oovent'),
                ));
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save event product data
     */
    public function save_event_product_data($post_id) {
        // Save is_event
        $is_event = isset($_POST['_is_event']) ? 'yes' : 'no';
        update_post_meta($post_id, '_is_event', $is_event);
        
        // Save event fields
        $fields = array(
            '_event_start_date',
            '_event_end_date',
            '_event_location',
            '_event_capacity',
            '_event_organizer',
            '_event_type',
            '_event_agenda',
            '_event_instructions',
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // Save checkboxes
        $checkbox_fields = array(
            '_event_enable_qr',
            '_event_enable_checkin',
        );
        
        foreach ($checkbox_fields as $field) {
            $value = isset($_POST[$field]) ? 'yes' : 'no';
            update_post_meta($post_id, $field, $value);
        }
    }
}
