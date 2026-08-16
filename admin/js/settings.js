// Settings Page JavaScript

JQuery(document).ready(function($) {
    // Tab switching
    $('.wsrp-tab-btn').on('click', function(e) {
        e.preventDefault();
        const tabId = $(this).data('tab');
        
        // Remove active class from all buttons and contents
        $('.wsrp-tab-btn').removeClass('active');
        $('.wsrp-tab-content').removeClass('active');
        
        // Add active class to clicked button and corresponding content
        $(this).addClass('active');
        $('#tab-' + tabId).addClass('active');
    });

    // Copy shortcode
    $('.copy-btn').on('click', function(e) {
        e.preventDefault();
        const text = $(this).data('copy');
        
        // Create a temporary textarea
        const textarea = $('<textarea>').val(text).appendTo('body');
        textarea.select();
        
        try {
            document.execCommand('copy');
            
            // Show success message
            const originalText = $(this).html();
            $(this).html('<i class="fas fa-check"></i> تم النسخ!').css('background', '#00b894');
            
            setTimeout(() => {
                $(this).html(originalText).css('background', '');
            }, 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
        
        textarea.remove();
    });

    // Show confirmation before saving
    $('#wsrp-settings-form').on('submit', function(e) {
        console.log('Settings form submitted');
    });

    // Color picker preview
    $('input[type="color"]').on('change', function() {
        const previewClass = $(this).attr('name');
        console.log('Color changed: ' + previewClass);
    });
});
