# OOvent Plugin Architecture

## Overview

OOvent is a modular WordPress plugin that extends WooCommerce to support event management with QR code ticketing and check-in capabilities.

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     WordPress Core                          │
├─────────────────────────────────────────────────────────────┤
│                     WooCommerce                             │
├─────────────────────────────────────────────────────────────┤
│                      OOvent Plugin                          │
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │
│  │   Frontend   │  │    Admin     │  │   Database   │    │
│  └──────────────┘  └──────────────┘  └──────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

## Core Components

### 1. Main Plugin File (oovent.php)
- Plugin initialization
- Class loading
- Hook registration
- Script/style enqueuing
- WooCommerce dependency check

### 2. Installation (class-oovent-install.php)
- Database table creation
- Default options setup
- Activation/deactivation hooks
- Version management

### 3. Event Product (class-oovent-event-product.php)
- Custom product type
- Event-specific fields
- Product data tabs
- Meta box integration
- Data saving

### 4. Ticket Management (class-oovent-ticket.php)
- Ticket generation on order completion
- Unique ticket number creation
- Ticket retrieval methods
- Order-ticket association

### 5. QR Code (class-oovent-qr-code.php)
- QR code generation via API
- Local storage of QR images
- Ticket validation
- Security checks

### 6. Check-in (class-oovent-checkin.php)
- Check-in processing
- Undo functionality
- Activity logging
- Statistics generation

### 7. Email (class-oovent-email.php)
- Ticket email templates
- QR code attachment
- Email customization
- HTML formatting

### 8. Reports (class-oovent-reports.php)
- Overall statistics
- Event-specific analytics
- Top events calculation
- CSV export
- Timeline data

### 9. AJAX Handler (class-oovent-ajax.php)
- Scanner endpoints
- Ticket validation API
- Check-in API
- Security verification

### 10. Admin (class-oovent-admin.php)
- Menu registration
- Meta boxes
- Admin interface
- Order ticket display

### 11. Scanner (class-oovent-scanner.php)
- Scanner page rendering
- Camera interface
- Manual entry form
- Recent check-ins display

### 12. Dashboard (class-oovent-dashboard.php)
- Statistics dashboard
- Top events display
- Recent activity
- Quick actions

## Data Flow

### Ticket Creation Flow
```
Customer Purchase → Order Completed
    ↓
WooCommerce Hook Triggered
    ↓
OOvent_Ticket::generate_tickets()
    ↓
For Each Event Product:
    - Generate Unique Ticket Number
    - Create QR Code (via OOvent_QR_Code)
    - Save to Database
    - Store QR Code Image
    ↓
OOvent_Email::send_ticket_email()
    ↓
Customer Receives Email with QR Codes
```

### Check-in Flow
```
Scanner Page Loaded
    ↓
User Scans QR Code / Enters Ticket Number
    ↓
JavaScript: AJAX Call to oovent_scan_ticket
    ↓
OOvent_AJAX::scan_ticket()
    ↓
OOvent_QR_Code::validate()
    ↓
Display Ticket Information
    ↓
User Clicks "Check In"
    ↓
JavaScript: AJAX Call to oovent_checkin_ticket
    ↓
OOvent_Checkin::check_in()
    ↓
Update Database
    ↓
Log Check-in Action
    ↓
Return Success Response
    ↓
Update UI (show checked-in status)
```

## Database Schema

### oovent_tickets
```sql
id              bigint(20)      Primary Key, Auto Increment
order_id        bigint(20)      Foreign Key to orders
product_id      bigint(20)      Foreign Key to products
ticket_number   varchar(100)    Unique ticket identifier
qr_code_url     text           URL to QR code image
attendee_name   varchar(255)    Attendee full name
attendee_email  varchar(255)    Attendee email
checked_in      tinyint(1)      Check-in status (0/1)
checked_in_at   datetime        Check-in timestamp
checked_in_by   bigint(20)      User who checked in
created_at      datetime        Ticket creation time
```

### oovent_checkin_logs
```sql
id              bigint(20)      Primary Key, Auto Increment
ticket_id       bigint(20)      Foreign Key to tickets
user_id         bigint(20)      User performing action
action          varchar(50)     checkin/undo_checkin
notes           text           Optional notes
ip_address      varchar(100)    IP address
created_at      datetime        Action timestamp
```

### Product Meta (Event Fields)
```
_is_event               yes/no
_event_start_date       datetime
_event_end_date         datetime
_event_location         text
_event_capacity         number
_event_organizer        text
_event_type             conference/workshop/etc
_event_agenda           longtext
_event_instructions     longtext
_event_enable_qr        yes/no
_event_enable_checkin   yes/no
```

## JavaScript Architecture

### Frontend (oovent.js)
- Event countdown timers
- Frontend interactivity
- Future enhancements

### Admin (admin.js)
- Product type field toggling
- Event field visibility
- Admin UI enhancements

### Scanner (scanner.js)
- Camera initialization
- QR code scanning
- AJAX communication
- Real-time updates
- Tab switching
- Manual entry handling

### Dashboard (dashboard.js)
- Chart initialization
- Auto-refresh
- Data visualization

## Security Measures

1. **Input Validation**
   - Sanitize all user inputs
   - Validate data types
   - Check required fields

2. **Output Escaping**
   - Escape all HTML output
   - Prevent XSS attacks
   - Use WordPress escaping functions

3. **Nonce Verification**
   - AJAX nonce checks
   - Form nonce verification
   - Action verification

4. **Capability Checks**
   - User permission validation
   - Role-based access control
   - Admin-only features

5. **Database Security**
   - Prepared statements
   - SQL injection prevention
   - Data sanitization

## Hooks & Filters

### Actions (do_action)
- `oovent_activated`
- `oovent_deactivated`
- `oovent_updated`
- `oovent_tickets_generated`
- `oovent_ticket_checked_in`
- `oovent_ticket_checkin_undone`

### Filters (apply_filters)
- `oovent_qr_code_url`
- `oovent_ticket_email_subject`
- `oovent_ticket_email_message`

## Integration Points

### WooCommerce
- Product types
- Order hooks
- Email system
- Admin interfaces

### WordPress
- Post meta
- User capabilities
- Admin menus
- AJAX API
- Upload directory
- Database API

## Performance Considerations

1. **Database Queries**
   - Indexed columns
   - Prepared statements
   - Efficient joins

2. **Asset Loading**
   - Conditional loading
   - Script dependencies
   - Minification ready

3. **Caching**
   - Transient API ready
   - Object caching compatible

4. **Image Storage**
   - Local QR storage
   - Upload directory usage
   - File optimization

## Extensibility

The plugin is designed to be extensible through:
- WordPress hooks and filters
- Class methods
- Template overrides
- CSS/JS customization
- Database schema additions

## Future Enhancements

- REST API endpoints
- Mobile app integration
- Webhook support
- Advanced reporting
- Multi-event packages
- Custom ticket designs
