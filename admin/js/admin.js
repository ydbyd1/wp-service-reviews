(function($) {
    'use strict';

    $(document).ready(function() {
        // عرض تفاصيل التقييم في modal
        $('.wsrp-btn-view').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            
            // يمكن استخدام WP Modal أو dialog
            alert('تفاصيل التقييم #' + id);
        });
    });
})(jQuery);