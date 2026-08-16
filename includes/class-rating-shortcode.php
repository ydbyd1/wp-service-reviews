<?php

if (!defined('ABSPATH')) {
    exit;
}

class WSRP_Rating_Shortcode {

    public function __construct() {
        add_shortcode('service_ratings', [$this, 'display_ratings']);
    }

    public function display_ratings($atts) {
        $atts = shortcode_atts([
            'limit' => 10,
            'service' => ''
        ], $atts);

        $ratings = WSRP_Rating_Database::get_ratings(true, intval($atts['limit']));
        $avg_rating = WSRP_Rating_Database::get_average_rating();
        $count = WSRP_Rating_Database::count_ratings(true);

        ob_start();
        ?>
        <div class="wsrp-ratings-container">
            <div class="wsrp-ratings-header">
                <h3>تقييمات العملاء</h3>
                <div class="wsrp-average-rating">
                    <div class="wsrp-stars">
                        <?php echo $this->render_stars($avg_rating); ?>
                    </div>
                    <div class="wsrp-rating-text">
                        <span class="wsrp-avg-number"><?php echo esc_html($avg_rating); ?></span>
                        <span class="wsrp-rating-count">(<?php echo esc_html($count); ?> تقييم)</span>
                    </div>
                </div>
            </div>

            <div class="wsrp-ratings-list">
                <?php if (!empty($ratings)) : ?>
                    <?php foreach ($ratings as $rating) : ?>
                        <div class="wsrp-rating-item">
                            <div class="wsrp-rating-header">
                                <h4 class="wsrp-customer-name"><?php echo esc_html($rating->customer_name); ?></h4>
                                <div class="wsrp-rating-stars">
                                    <?php echo $this->render_stars($rating->rating); ?>
                                </div>
                            </div>
                            <p class="wsrp-rating-comment"><?php echo wp_kses_post($rating->comment); ?></p>
                            <p class="wsrp-rating-service"><?php echo esc_html($rating->service_name); ?></p>
                            <p class="wsrp-rating-date"><?php echo date_i18n('j F Y', strtotime($rating->created_at)); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="wsrp-no-ratings">لا توجد تقييمات حالياً</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_stars($rating) {
        $rating = floatval($rating);
        $stars = '';
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<span class="wsrp-star full">★</span>';
            } elseif ($i - 0.5 <= $rating) {
                $stars .= '<span class="wsrp-star half">★</span>';
            } else {
                $stars .= '<span class="wsrp-star empty">★</span>';
            }
        }
        
        return $stars;
    }
}
?>