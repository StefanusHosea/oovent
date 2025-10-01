/* OOvent Admin Scripts */
jQuery(document).ready(function($) {
    // Toggle event fields visibility based on product type
    $('select#product-type').change(function() {
        if ($(this).val() == 'event') {
            $('.show_if_event').show();
            $('#event_product_data').show();
        }
    }).change();
    
    // Handle event checkbox
    $('#_is_event').change(function() {
        if ($(this).is(':checked')) {
            $('.show_if_event').show();
            $('#event_product_data').show();
        } else {
            $('.show_if_event').hide();
        }
    });
});
