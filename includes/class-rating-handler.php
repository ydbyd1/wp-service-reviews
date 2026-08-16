<?php

if (!defined('ABSPATH')) {
    exit;
}

class WSRP_Rating_Handler {

    public function __construct() {
        add_action('wp_ajax_nopriv_submit_rating', [$this, 'submit_rating']);
        add_action('wp_ajax_submit_rating', [$this, 'submit_rating']);
    }

    public function submit_rating() {
        check_ajax_referer('wsrp_nonce', 'nonce');

        // التحقق من البيانات
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['rating']) || empty($_POST['comment'])) {
            wp_send_json_error(['message' => 'جميع الحقول مطلوبة']);
        }

        $rating = intval($_POST['rating']);
        if ($rating < 1 || $rating > 5) {
            wp_send_json_error(['message' => 'التقييم يجب أن يكون من 1 إلى 5']);
        }

        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'phone' => sanitize_text_field($_POST['phone']),
            'email' => sanitize_email($_POST['email']),
            'service' => !empty($_POST['service']) ? sanitize_text_field($_POST['service']) : 'خدمة عامة',
            'rating' => $rating,
            'comment' => sanitize_textarea_field($_POST['comment'])
        ];

        $id = WSRP_Rating_Database::insert_rating($data);

        if ($id) {
            wp_send_json_success([
                'message' => 'شكراً على تقييمك! سيتم عرضه بعد موافقة الإدارة',
                'rating_id' => $id
            ]);
        } else {
            wp_send_json_error(['message' => 'حدث خطأ أثناء حفظ التقييم']);
        }
    }
}
?>