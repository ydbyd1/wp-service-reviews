(function($) {
    'use strict';

    $(document).ready(function() {
        $('#wsrp-rating-form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('.wsrp-btn-submit');
            const loading = form.find('.wsrp-loading');
            const message = form.find('.wsrp-form-message');

            // التحقق من البيانات
            if (!form[0].checkValidity()) {
                message.html('الرجاء ملء جميع الحقول المطلوبة').addClass('error').show();
                return;
            }

            // تعطيل الزر وإظهار رسالة التحميل
            submitBtn.prop('disabled', true);
            loading.show();
            message.hide();

            // إرسال البيانات
            $.ajax({
                url: wsrp_ajax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'submit_rating',
                    nonce: form.find('input[name="nonce"]').val(),
                    name: form.find('#wsrp-name').val(),
                    email: form.find('#wsrp-email').val(),
                    phone: form.find('#wsrp-phone').val(),
                    service: form.find('input[name="service"]').val(),
                    rating: form.find('input[name="rating"]:checked').val(),
                    comment: form.find('#wsrp-comment').val()
                },
                success: function(response) {
                    if (response.success) {
                        message.html(response.data.message)
                            .removeClass('error').addClass('success').show();
                        form[0].reset();
                    } else {
                        message.html(response.data.message)
                            .removeClass('success').addClass('error').show();
                    }
                },
                error: function() {
                    message.html('حدث خطأ أثناء إرسال التقييم')
                        .removeClass('success').addClass('error').show();
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    loading.hide();
                }
            });
        });

        // تأثيرات النجوم
        $('.wsrp-rating-input label.star').on('mouseover', function() {
            const value = $(this).prev('input').val();
            updateStarDisplay(value);
        });

        $('#wsrp-rating-form').on('mouseout', function() {
            const checked = $(this).find('input[name="rating"]:checked').val();
            if (checked) {
                updateStarDisplay(checked);
            }
        });

        function updateStarDisplay(value) {
            $('.wsrp-rating-input label.star').each(function() {
                const starValue = $(this).prev('input').val();
                if (starValue <= value) {
                    $(this).css('color', '#ffc107');
                } else {
                    $(this).css('color', '#ddd');
                }
            });
        }
    });
})(jQuery);