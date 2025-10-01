/* OOvent Scanner Scripts */
jQuery(document).ready(function($) {
    var currentTicketNumber = null;
    var html5QrcodeScanner = null;
    
    // Tab switching
    $('.scanner-tab').on('click', function() {
        var tab = $(this).data('tab');
        
        $('.scanner-tab').removeClass('active');
        $(this).addClass('active');
        
        $('.scanner-content').removeClass('active');
        $('.' + tab + '-scanner').addClass('active');
        
        if (tab === 'camera') {
            startScanner();
        } else {
            stopScanner();
        }
    });
    
    // Initialize camera scanner
    function startScanner() {
        if (html5QrcodeScanner) {
            return; // Already running
        }
        
        var config = {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        };
        
        html5QrcodeScanner = new Html5Qrcode("reader");
        
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanError
        ).catch(err => {
            console.error("Unable to start scanner:", err);
            $('#scanner-status').html('<p class="error">Unable to access camera. Please check permissions or use manual entry.</p>');
        });
    }
    
    // Stop camera scanner
    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner = null;
            }).catch(err => {
                console.error("Error stopping scanner:", err);
            });
        }
    }
    
    // Handle successful QR scan
    function onScanSuccess(decodedText, decodedResult) {
        // Validate and display ticket info
        validateTicket(decodedText);
    }
    
    // Handle scan errors (can be ignored for continuous scanning)
    function onScanError(errorMessage) {
        // Ignore errors during scanning
    }
    
    // Validate ticket via AJAX
    function validateTicket(ticketNumber) {
        currentTicketNumber = ticketNumber;
        
        $.ajax({
            url: ooventScanner.ajaxurl,
            type: 'POST',
            data: {
                action: 'oovent_scan_ticket',
                nonce: ooventScanner.nonce,
                ticket_number: ticketNumber
            },
            success: function(response) {
                if (response.success) {
                    displayTicketInfo(response.data);
                    showScannerStatus('Valid ticket found!', 'success');
                } else {
                    if (response.data.already_checked_in) {
                        displayTicketInfo(response.data);
                        showScannerStatus(response.data.message, 'warning');
                    } else {
                        showScannerStatus(response.data.message, 'error');
                        clearTicketInfo();
                    }
                }
            },
            error: function() {
                showScannerStatus('Error validating ticket. Please try again.', 'error');
            }
        });
    }
    
    // Display ticket information
    function displayTicketInfo(data) {
        var ticket = data.ticket;
        var isCheckedIn = ticket.checked_in == 1;
        
        var html = '<div class="ticket-detail-content">';
        html += '<div class="ticket-detail"><strong>Ticket Number:</strong> <code>' + ticket.ticket_number + '</code></div>';
        html += '<div class="ticket-detail"><strong>Event:</strong> ' + data.event_name + '</div>';
        html += '<div class="ticket-detail"><strong>Attendee:</strong> ' + ticket.attendee_name + '</div>';
        html += '<div class="ticket-detail"><strong>Email:</strong> ' + ticket.attendee_email + '</div>';
        
        if (isCheckedIn) {
            html += '<div class="ticket-detail"><strong>Status:</strong> <span class="status-badge checked-in">Checked In</span></div>';
            html += '<div class="ticket-detail"><strong>Checked In At:</strong> ' + ticket.checked_in_at + '</div>';
        } else {
            html += '<div class="ticket-detail"><strong>Status:</strong> <span class="status-badge pending">Pending Check-in</span></div>';
        }
        
        html += '</div>';
        
        $('#ticket-info').html(html);
        $('#checkin-actions').show();
        
        if (isCheckedIn) {
            $('#checkin-ticket-btn').hide();
            $('#undo-checkin-btn').show();
        } else {
            $('#checkin-ticket-btn').show();
            $('#undo-checkin-btn').hide();
        }
    }
    
    // Clear ticket info
    function clearTicketInfo() {
        $('#ticket-info').html('<p class="no-ticket">Scan a ticket to see information</p>');
        $('#checkin-actions').hide();
        currentTicketNumber = null;
    }
    
    // Show scanner status message
    function showScannerStatus(message, type) {
        var statusHtml = '<div class="scanner-status ' + type + '">' + message + '</div>';
        $('#scanner-status').html(statusHtml);
        
        // Auto-hide after 3 seconds for success messages
        if (type === 'success') {
            setTimeout(function() {
                $('#scanner-status').html('');
            }, 3000);
        }
    }
    
    // Manual ticket entry
    $('#scan-manual-ticket').on('click', function() {
        var ticketNumber = $('#manual-ticket-number').val().trim();
        
        if (!ticketNumber) {
            alert('Please enter a ticket number');
            return;
        }
        
        validateTicket(ticketNumber);
    });
    
    // Allow Enter key to submit manual entry
    $('#manual-ticket-number').on('keypress', function(e) {
        if (e.which === 13) {
            $('#scan-manual-ticket').click();
        }
    });
    
    // Check-in ticket
    $('#checkin-ticket-btn').on('click', function() {
        if (!currentTicketNumber) {
            alert('No ticket selected');
            return;
        }
        
        if (!confirm('Check in this ticket?')) {
            return;
        }
        
        $.ajax({
            url: ooventScanner.ajaxurl,
            type: 'POST',
            data: {
                action: 'oovent_checkin_ticket',
                nonce: ooventScanner.nonce,
                ticket_number: currentTicketNumber
            },
            success: function(response) {
                if (response.success) {
                    showScannerStatus('Ticket checked in successfully!', 'success');
                    displayTicketInfo(response.data);
                    addToRecentCheckins(response.data);
                    
                    // Clear after a moment
                    setTimeout(function() {
                        clearTicketInfo();
                        $('#manual-ticket-number').val('');
                    }, 2000);
                } else {
                    showScannerStatus(response.data.message, 'error');
                }
            },
            error: function() {
                showScannerStatus('Error checking in ticket. Please try again.', 'error');
            }
        });
    });
    
    // Undo check-in
    $('#undo-checkin-btn').on('click', function() {
        if (!currentTicketNumber) {
            alert('No ticket selected');
            return;
        }
        
        if (!confirm('Undo check-in for this ticket?')) {
            return;
        }
        
        $.ajax({
            url: ooventScanner.ajaxurl,
            type: 'POST',
            data: {
                action: 'oovent_undo_checkin',
                nonce: ooventScanner.nonce,
                ticket_number: currentTicketNumber
            },
            success: function(response) {
                if (response.success) {
                    showScannerStatus('Check-in undone successfully!', 'success');
                    validateTicket(currentTicketNumber); // Refresh ticket info
                } else {
                    showScannerStatus(response.data.message, 'error');
                }
            },
            error: function() {
                showScannerStatus('Error undoing check-in. Please try again.', 'error');
            }
        });
    });
    
    // Add to recent check-ins table
    function addToRecentCheckins(data) {
        var row = '<tr>';
        row += '<td>Just now</td>';
        row += '<td><code>' + data.ticket.ticket_number + '</code></td>';
        row += '<td>' + data.event_name + '</td>';
        row += '<td>' + data.attendee_name + '</td>';
        row += '</tr>';
        
        $('#recent-checkins-body').prepend(row);
        
        // Keep only 10 rows
        $('#recent-checkins-body tr').slice(10).remove();
    }
    
    // Start scanner on page load if camera tab is active
    if ($('.camera-scanner').hasClass('active')) {
        startScanner();
    }
});
