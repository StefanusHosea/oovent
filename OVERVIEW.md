# OOvent - Complete WordPress Event Plugin

## 📦 What is OOvent?

OOvent is a professional WordPress plugin that transforms WooCommerce into a powerful event management system. It enables you to sell event tickets, generate QR codes, scan attendees at the door, and analyze your event's success - all from within your WordPress dashboard.

## ✨ Perfect For

- **Conference Organizers** - Manage attendee check-ins efficiently
- **Workshop Hosts** - Track capacity and participant engagement
- **Concert Venues** - Handle ticket validation and entry management
- **Seminar Providers** - Organize professional events with ease
- **Meetup Groups** - Coordinate gatherings with ticketing
- **Festival Planners** - Manage large-scale events
- **Sports Events** - Control stadium or venue entry
- **Webinar Hosts** - Organize online events with registration

## 🎯 Key Features at a Glance

| Feature | Description |
|---------|-------------|
| **Event Products** | Turn any WooCommerce product into an event with custom fields |
| **QR Tickets** | Automatic QR code generation for every ticket |
| **Email Delivery** | Beautiful HTML emails with QR codes attached |
| **Check-in Scanner** | Real-time camera scanner or manual entry |
| **Dashboard** | Comprehensive analytics and statistics |
| **Reports** | Track check-ins, revenue, and capacity |
| **Multi-Ticket** | Support for multiple tickets per order |
| **Capacity Management** | Set and monitor event capacity limits |

## 🚀 Getting Started

### Quick Installation
1. Upload to `/wp-content/plugins/oovent/`
2. Activate plugin in WordPress
3. Ensure WooCommerce is active
4. Navigate to **OOvent** menu

### Create Your First Event
1. Products > Add New
2. Enable as Event
3. Add event details
4. Set price & publish
5. Test with a purchase

See [QUICKSTART.md](QUICKSTART.md) for detailed steps.

## 📊 Dashboard Overview

The OOvent dashboard provides instant insights:

```
┌─────────────────┬─────────────────┬─────────────────┐
│  Total Events   │  Total Tickets  │   Checked In    │
│       12        │       485       │       327       │
└─────────────────┴─────────────────┴─────────────────┘
┌─────────────────┬─────────────────┬─────────────────┐
│    Pending      │  Check-in Rate  │  Total Revenue  │
│      158        │     67.42%      │   $12,450.00    │
└─────────────────┴─────────────────┴─────────────────┘
```

## 🎫 Ticket Generation Flow

```
Purchase → Order Completed → Tickets Generated → Email Sent → QR Codes Ready
```

Each ticket includes:
- Unique ticket number
- QR code (300x300px PNG)
- Event details
- Attendee information
- Check-in instructions

## 📱 Scanner Interface

Two methods for check-in:

**Camera Scanner**
- Point camera at QR code
- Instant validation
- Auto-detect tickets
- Works on mobile & desktop

**Manual Entry**
- Type ticket number
- Quick keyboard entry
- Backup method
- Works without camera

## 📈 Analytics & Reports

### Overall Statistics
- Total events created
- Total tickets sold
- Check-in completion rate
- Revenue tracking
- Pending check-ins

### Event-Specific
- Tickets sold vs capacity
- Check-in percentage
- Revenue per event
- Attendee list
- Timeline analysis

### Export Options
- CSV export for all tickets
- Event-specific exports
- Attendee data
- Check-in logs

## 🔒 Security Features

- ✅ Nonce verification on all AJAX calls
- ✅ User capability checks (manage_woocommerce)
- ✅ Input sanitization and validation
- ✅ Output escaping to prevent XSS
- ✅ SQL prepared statements
- ✅ IP address logging for check-ins
- ✅ Unique ticket number generation
- ✅ Secure file storage

## 🛠 Technical Details

**Requirements:**
- WordPress 5.8+
- WooCommerce 5.0+
- PHP 7.4+
- MySQL 5.6+

**Database:**
- 2 custom tables
- Product meta integration
- Efficient indexing
- Clean uninstall option

**Performance:**
- Optimized queries
- Conditional script loading
- Minimal overhead
- Cache-friendly

## 📁 Plugin Structure

```
oovent/
├── Core Files
│   ├── oovent.php (Main plugin)
│   ├── uninstall.php (Clean removal)
│   └── LICENSE.txt
│
├── Documentation
│   ├── README.md (Complete guide)
│   ├── QUICKSTART.md (5-min setup)
│   ├── ARCHITECTURE.md (Technical)
│   ├── CHANGELOG.md (Version history)
│   └── CONTRIBUTING.md (Dev guide)
│
├── Includes
│   ├── Installation & Setup
│   ├── Event Product Type
│   ├── Ticket Management
│   ├── QR Code Generation
│   ├── Check-in System
│   ├── Email Templates
│   ├── Reports & Analytics
│   └── AJAX Handlers
│
├── Admin
│   ├── Dashboard Page
│   ├── Scanner Interface
│   └── Settings & Meta Boxes
│
└── Assets
    ├── CSS (Styles)
    └── JS (Functionality)
```

## 🎨 Customization

### Hooks Available
```php
// Actions
do_action('oovent_tickets_generated', $order_id);
do_action('oovent_ticket_checked_in', $ticket, $user_id);

// Filters
apply_filters('oovent_qr_code_url', $url, $ticket_number);
apply_filters('oovent_ticket_email_subject', $subject, $order);
```

### CSS Classes
- `.oovent-event-details` - Event information display
- `.oovent-ticket-info` - Ticket information box
- `.oovent-qr-code` - QR code container
- `.stat-card` - Dashboard statistics cards

## 🌟 Use Case Examples

### Conference Management
- Create event with 500 capacity
- Set start/end dates over multiple days
- Add conference agenda
- Include venue location
- Check attendees in at registration desk

### Workshop Series
- Multiple events (products)
- Limited capacity per workshop
- Track individual attendance
- Monitor completion rates
- Export attendee lists

### Concert/Festival
- High-capacity events
- Fast check-in required
- Multiple ticket tiers (variations)
- Real-time attendance tracking
- Revenue analysis

## 📞 Support & Community

- **Documentation:** Full docs in README.md
- **Issues:** GitHub Issues
- **Contributing:** See CONTRIBUTING.md
- **License:** GPL v2 or later

## 🗺 Roadmap

### Planned Features
- [ ] Bulk check-in functionality
- [ ] Advanced filtering in reports
- [ ] Multi-event packages
- [ ] Waiting list management
- [ ] Automated reminder emails
- [ ] Mobile check-in app
- [ ] Printable badges
- [ ] iCal export
- [ ] Google Calendar integration
- [ ] Custom ticket templates

## 💡 Pro Tips

1. **Test First** - Create test events before going live
2. **Mobile Scanner** - Use phone/tablet for in-person events
3. **Backup Method** - Always enable manual entry
4. **Monitor Capacity** - Watch the capacity utilization percentage
5. **Export Data** - Regular CSV exports for backup
6. **Email Testing** - Test ticket emails before the event

## 🏆 Why Choose OOvent?

| Feature | OOvent | Competitors |
|---------|--------|-------------|
| WooCommerce Integration | ✅ Native | ⚠️ Limited |
| QR Code Tickets | ✅ Automatic | ⚠️ Manual |
| Real-time Scanner | ✅ Built-in | ❌ Add-on |
| Check-in Logs | ✅ Full audit trail | ⚠️ Basic |
| Reporting Dashboard | ✅ Comprehensive | ⚠️ Basic |
| Price | ✅ Free & Open Source | 💰 Paid |
| Customizable | ✅ Hooks & Filters | ❌ Limited |

## 📄 License

OOvent is open source software licensed under GPL v2 or later. This means you can:
- ✅ Use it for free
- ✅ Modify the code
- ✅ Distribute modified versions
- ✅ Use it commercially

## 🙏 Credits

Built with:
- WordPress & WooCommerce
- html5-qrcode library (QR scanning)
- Google Charts API (QR generation)
- Chart.js (Data visualization)

---

**Ready to manage events like a pro? Install OOvent today!**

[Download](https://github.com/StefanusHosea/oovent) | [Documentation](README.md) | [Quick Start](QUICKSTART.md)
