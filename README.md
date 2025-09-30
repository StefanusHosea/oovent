# OOvent - WooCommerce Event Manager

A comprehensive WordPress plugin that transforms WooCommerce products into events with QR code tickets, check-in scanner capabilities, and a powerful reporting dashboard.

## Features

### 🎫 Event Management
- Convert any WooCommerce product into an event
- Event-specific fields: start/end date, location, capacity, organizer
- Multiple event types: Conference, Workshop, Seminar, Meetup, Webinar, Concert, Festival, Sports
- Event agenda and special instructions for attendees
- Capacity management and tracking

### 📱 QR Code Tickets
- Automatic QR code generation for each ticket
- Unique ticket numbers for security
- QR codes attached to order confirmation emails
- Beautiful ticket email templates
- Support for multiple tickets per order

### ✅ Check-in Scanner
- Real-time camera-based QR code scanner
- Manual ticket number entry option
- Instant ticket validation
- Check-in status tracking
- Undo check-in capability
- Check-in logs with IP address and user tracking
- Recent check-ins display

### 📊 Comprehensive Reporting Dashboard
- Overall statistics: total events, tickets, check-ins, revenue
- Check-in rate percentage
- Top events by ticket sales
- Recent check-ins timeline
- Event-specific analytics
- Capacity utilization tracking
- Export tickets to CSV

### 🎨 User-Friendly Interface
- Clean, modern admin dashboard
- Easy-to-use scanner interface
- Tab-based navigation
- Real-time status updates
- Mobile-responsive design

## Requirements

- WordPress 5.8 or higher
- WooCommerce 5.0 or higher
- PHP 7.4 or higher
- Modern browser with camera access (for QR scanning)

## Installation

1. Download the plugin files or clone this repository
2. Upload the `oovent` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Make sure WooCommerce is installed and activated
5. Navigate to **OOvent** in the WordPress admin menu

## Usage

### Creating an Event

1. Go to **Products > Add New** in WordPress admin
2. Create a product as usual (name, description, price, etc.)
3. Check the **Enable as Event** checkbox in the product data section
4. Click the **Event Details** tab
5. Fill in event information:
   - Start Date & Time
   - End Date & Time
   - Location
   - Capacity (optional)
   - Organizer
   - Event Type
   - Agenda/Schedule
   - Special Instructions
6. Configure QR code and check-in settings
7. Publish the product

### Managing Tickets

When a customer purchases an event product:
- Tickets are automatically generated upon order completion/processing
- Each ticket receives a unique ticket number
- QR codes are generated and saved
- Customer receives an email with ticket details and QR codes
- Tickets can be viewed in the order details (admin)

### Using the Check-in Scanner

1. Go to **OOvent > Scanner** in WordPress admin
2. Choose scanning method:
   - **Camera Scanner**: Use device camera to scan QR codes
   - **Manual Entry**: Type ticket number manually
3. Scan or enter a ticket number
4. View ticket information and attendee details
5. Click **Check In** to mark the ticket as checked in
6. Recent check-ins appear in the table below

### Viewing Reports

1. Go to **OOvent > Dashboard** in WordPress admin
2. View overall statistics:
   - Total Events
   - Total Tickets Sold
   - Checked In Count
   - Pending Check-ins
   - Check-in Rate
   - Total Revenue
3. Review top events by ticket sales
4. See recent check-ins timeline
5. Access individual event reports from product edit page

### Event Statistics on Product Page

When editing an event product:
- View a **Event Statistics** box in the sidebar
- See total tickets, check-ins, pending, and check-in rate
- Monitor capacity utilization
- Quick link to check-in scanner

## Plugin Structure

```
oovent/
├── oovent.php                          # Main plugin file
├── includes/
│   ├── class-oovent-install.php        # Installation & database setup
│   ├── class-oovent-event-product.php  # Event product type
│   ├── class-oovent-ticket.php         # Ticket management
│   ├── class-oovent-qr-code.php        # QR code generation
│   ├── class-oovent-checkin.php        # Check-in functionality
│   ├── class-oovent-reports.php        # Reports & analytics
│   ├── class-oovent-email.php          # Email templates
│   ├── class-oovent-ajax.php           # AJAX handlers
│   └── admin/
│       ├── class-oovent-admin.php      # Admin interface
│       ├── class-oovent-scanner.php    # Scanner page
│       └── class-oovent-dashboard.php  # Dashboard page
├── assets/
│   ├── css/
│   │   ├── oovent.css                  # Frontend styles
│   │   └── admin.css                   # Admin styles
│   └── js/
│       ├── oovent.js                   # Frontend scripts
│       ├── admin.js                    # Admin scripts
│       ├── scanner.js                  # Scanner functionality
│       └── dashboard.js                # Dashboard scripts
└── README.md
```

## Database Tables

### oovent_tickets
Stores all event tickets with QR codes and check-in status.

### oovent_checkin_logs
Logs all check-in actions for audit trail.

## Hooks & Filters

### Actions
- `oovent_activated` - Fired when plugin is activated
- `oovent_deactivated` - Fired when plugin is deactivated
- `oovent_tickets_generated` - Fired after tickets are generated
- `oovent_ticket_checked_in` - Fired when a ticket is checked in
- `oovent_ticket_checkin_undone` - Fired when check-in is undone

### Filters
- `oovent_qr_code_url` - Modify QR code URL
- `oovent_ticket_email_subject` - Customize ticket email subject
- `oovent_ticket_email_message` - Customize ticket email content

## Settings

Access settings at **OOvent > Settings** (if settings page is added):
- Ticket email subject template
- Ticket email heading
- Enable/disable QR codes globally
- QR code size
- Ticket number prefix
- Enable check-in notifications

## Shortcodes

(Future enhancement - can add shortcodes for displaying event info on frontend)

## Support

For issues, feature requests, or contributions, please visit:
[https://github.com/StefanusHosea/oovent](https://github.com/StefanusHosea/oovent)

## License

This plugin is licensed under the GPL v2 or later.

## Credits

- QR Code Generation: Google Charts API
- QR Code Scanner: html5-qrcode library
- Charts: Chart.js

## Changelog

### Version 1.0.0
- Initial release
- Event product type
- QR code ticket generation
- Check-in scanner with camera support
- Comprehensive reporting dashboard
- Email notifications with tickets
- Check-in logging and analytics