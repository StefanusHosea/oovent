# Changelog

All notable changes to OOvent will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024

### Added
- Initial release of OOvent
- Event product type for WooCommerce
- Event-specific fields: dates, location, capacity, organizer, type
- Automatic QR code generation for tickets
- Unique ticket number generation
- Email notifications with QR code tickets
- Real-time camera-based QR code scanner
- Manual ticket entry option
- Check-in and undo check-in functionality
- Check-in logging with IP tracking
- Comprehensive admin dashboard
- Overall statistics display (events, tickets, check-ins, revenue)
- Event-specific analytics
- Top events reporting
- Recent check-ins tracking
- Check-in rate calculation
- Capacity utilization tracking
- CSV export for tickets
- Meta boxes for orders (ticket display)
- Meta boxes for products (event statistics)
- Admin menu structure
- Database tables for tickets and check-in logs
- Multiple event types support
- Event agenda and instructions fields
- Attendee management
- Mobile-responsive admin interface
- Tab-based scanner interface
- Real-time status updates

### Security
- Nonce verification for all AJAX calls
- User capability checks
- Input sanitization and validation
- Output escaping
- Secure database queries with prepared statements

### Performance
- Efficient database queries
- Optimized QR code generation
- Lazy loading of admin scripts
- Conditional script loading

## [Unreleased]

### Planned Features
- Bulk check-in functionality
- Advanced filtering in reports
- Multi-event ticket packages
- Waiting list management
- Automated reminder emails
- Attendee check-in app (mobile)
- Printable attendee badges
- Event calendar view
- iCal export
- Google Calendar integration
- Custom ticket templates
- Multi-language support
- Advanced analytics with charts
- Export to PDF
- Seat assignment
- Custom check-in questions
- Post-event surveys
