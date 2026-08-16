<?php

if (!defined('ABSPATH')) {
    exit;
}

class WSRP_Rating_Form {

    public function __construct() {
        add_shortcode('rating_form', [$this, 'display_form']);
    }

    public function display_form($atts) {
        $atts = shortcode_atts([
            'service' => 'خدمات عامة',
            'title' => 'اترك تقييماً للخدمة'
        ], $atts);

        ob_start();
        ?>
        <div class="wsrp-form-container">
            <h3><?php echo esc_html($atts['title']); ?></h3>
            
            <form id="wsrp-rating-form" class="wsrp-form">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wsrp_nonce'); ?>">
                <input type="hidden" name="service" value="<?php echo esc_attr($atts['service']); ?>">

                <div class="wsrp-form-group">
                    <label for="wsrp-name">الاسم الكامل *</label>
                    <input type="text" id="wsrp-name" name="name" required placeholder="أدخل اسمك الكامل">
                </div>

                <div class="wsrp-form-row">
                    <div class="wsrp-form-group">
                        <label for="wsrp-email">البريد الإلكتروني *</label>
                        <input type="email" id="wsrp-email" name="email" required placeholder="your@email.com">
                    </div>
                    
                    <div class="wsrp-form-group">
                        <label for="wsrp-phone">رقم الهاتف *</label>
                        <input type="tel" id="wsrp-phone" name="phone" required placeholder="+90 XXX XXX XXXX">
                    </div>
                </div>

                <div class="wsrp-form-group">
                    <label>التقييم *</label>
                    <div class="wsrp-rating-input">
                        <input type="radio" name="rating" value="5" id="star5" required>
                        <label for="star5" class="star">★</label>
                        
                        <input type="radio" name="rating" value="4" id="star4">
                        <label for="star4" class="star">★</label>
                        
                        <input type="radio" name="rating" value="3" id="star3">
                        <label for="star3" class="star">★</label>
                        
                        <input type="radio" name="rating" value="2" id="star2">
                        <label for="star2" class="star">★</label>
                        
                        <input type="radio" name="rating" value="1" id="star1">
                        <label for="star1" class="star">★</label>
                    </div>
                </div>

                <div class="wsrp-form-group">
                    <label for="wsrp-comment">التعليق *</label>
                    <textarea id="wsrp-comment" name="comment" required placeholder="شارك تجربتك معنا..." rows="5"></textarea>
                </div>

                <div class="wsrp-form-submit">
                    <button type="submit" class="wsrp-btn-submit">إرسال التقييم</button>
                    <span class="wsrp-loading" style="display:none;">جاري الإرسال...</span>
                </div>

                <div class="wsrp-form-message" style="display:none;"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>