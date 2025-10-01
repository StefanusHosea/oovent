/* OOvent Dashboard Scripts */
jQuery(document).ready(function($) {
    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        // Add chart initialization code here if needed
        console.log('Chart.js is loaded and ready');
    }
    
    // Auto-refresh dashboard stats every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
