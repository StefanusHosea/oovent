# OOvent Quick Start Guide

Get started with OOvent in 5 minutes!

## Step 1: Installation

1. Download the plugin from GitHub or upload the zip file
2. Navigate to **Plugins > Add New** in WordPress admin
3. Click **Upload Plugin** and select the zip file
4. Click **Install Now** and then **Activate Plugin**

**Important:** Make sure WooCommerce is installed and activated first!

## Step 2: Create Your First Event

1. Go to **Products > Add New**
2. Enter product details:
   - **Title:** "WordPress Conference 2024"
   - **Description:** Add your event details
   - **Price:** Set your ticket price
   
3. Scroll down to **Product Data** section
4. Check **Enable as Event** checkbox
5. Click on **Event Details** tab
6. Fill in event information:
   - Start Date: 2024-12-15 09:00
   - End Date: 2024-12-15 17:00
   - Location: "Convention Center, New York"
   - Capacity: 100 (optional)
   - Organizer: "Your Organization Name"
   - Event Type: Conference
   
7. Add event agenda or special instructions
8. Click **Publish**

## Step 3: Test with a Purchase

1. Visit your event product page on the frontend
2. Add to cart and complete checkout
3. Use a test email address you can access
4. Complete the order

## Step 4: Check Your Email

After the order is completed, check your email for:
- Order confirmation from WooCommerce
- Event ticket email from OOvent with QR codes attached

## Step 5: Use the Scanner

1. Go to **OOvent > Scanner** in WordPress admin
2. You'll see two tabs:
   - **Camera Scanner:** Click to activate your camera
   - **Manual Entry:** Type ticket numbers manually
   
3. Try scanning the QR code from your email (take a screenshot or display on another device)
4. Or manually enter the ticket number shown in your email
5. Click **Check In** to mark the attendee as checked in

## Step 6: View Dashboard

1. Go to **OOvent > Dashboard**
2. View your statistics:
   - Total Events
   - Total Tickets
   - Check-in Rate
   - Revenue
   - Recent Check-ins

## Common Use Cases

### Conference/Seminar
- Set capacity for room limits
- Add detailed agenda in Event Agenda field
- Include special instructions (parking, dress code, etc.)

### Workshop
- Set limited capacity for small groups
- Include materials needed in instructions
- Set precise start/end times

### Concert/Festival
- Enable multiple ticket tiers using variations
- Set high capacity
- Add location and parking information

### Webinar
- Set location as "Online - Zoom"
- Include meeting link in special instructions
- No capacity limit needed

## Tips for Success

1. **Test First:** Create a test event and order to familiarize yourself
2. **Mobile Scanner:** The scanner works best on mobile devices for in-person events
3. **Manual Entry:** Use manual entry as a backup if cameras don't work
4. **Capacity Planning:** Monitor capacity usage in the event stats box
5. **Email Timing:** Tickets are sent when order status is "Processing" or "Completed"

## Troubleshooting

### Tickets Not Generated
- Check order status (must be Processing or Completed)
- Verify product has "Enable as Event" checked
- Check WooCommerce email settings

### QR Codes Not Showing
- Ensure "Enable QR Code Tickets" is checked on the event
- Check if QR code images are in wp-content/uploads/oovent-qr-codes/
- Verify server can access Google Charts API

### Scanner Not Working
- Grant camera permissions in your browser
- Try different browser (Chrome recommended)
- Use manual entry as alternative
- Check HTTPS (camera requires secure connection)

### Check-in Not Working
- Verify user has "manage_woocommerce" capability
- Clear browser cache
- Check browser console for errors

## Next Steps

- Explore the reporting dashboard
- Export tickets to CSV
- Customize email templates
- Add multiple events
- Monitor check-in rates

## Support

For issues or questions:
- GitHub Issues: https://github.com/StefanusHosea/oovent/issues
- Documentation: See README.md

## Video Tutorial

(Coming soon - Link to video walkthrough)

---

**Happy Event Managing! 🎉**
