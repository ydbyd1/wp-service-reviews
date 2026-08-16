<?php

if (!defined('ABSPATH')) {
    exit;
}

class WSRP_Settings {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_submenu_page(
            'service-reviews',
            'الإعدادات',
            'الإعدادات',
            'manage_options',
            'service-reviews-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('wsrp_settings_group', 'wsrp_settings');
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }

        $settings = get_option('wsrp_settings', $this->get_default_settings());
        ?>
        <div class="wrap wsrp-settings-page">
            <h1><i class="fas fa-cog"></i> إعدادات نظام التقييمات</h1>
            
            <div class="wsrp-settings-container">
                <!-- Tabs Navigation -->
                <div class="wsrp-tabs-nav">
                    <button class="wsrp-tab-btn active" data-tab="shortcodes">
                        <i class="fas fa-code"></i> الشورت كودز
                    </button>
                    <button class="wsrp-tab-btn" data-tab="display">
                        <i class="fas fa-eye"></i> خيارات الظهور
                    </button>
                    <button class="wsrp-tab-btn" data-tab="functionality">
                        <i class="fas fa-sliders-h"></i> الوظائف
                    </button>
                    <button class="wsrp-tab-btn" data-tab="moderation">
                        <i class="fas fa-check-double"></i> الموافقة
                    </button>
                    <button class="wsrp-tab-btn" data-tab="styling">
                        <i class="fas fa-palette"></i> التصميم
                    </button>
                </div>

                <form method="post" action="options.php" class="wsrp-settings-form">
                    <?php settings_fields('wsrp_settings_group'); ?>

                    <!-- Shortcodes Tab -->
                    <div class="wsrp-tab-content active" id="tab-shortcodes">
                        <div class="wsrp-tab-panel">
                            <h2>الشورت كودز المتاحة</h2>
                            <p class="wsrp-description">استخدم هذه الشورت كودز في صفحاتك ومقالاتك</p>

                            <div class="shortcodes-grid">
                                <!-- Shortcode 1 -->
                                <div class="shortcode-card">
                                    <h3><i class="fas fa-wpforms"></i> نموذج التقييم</h3>
                                    <p class="shortcode-desc">عرض نموذج إضافة التقييمات</p>
                                    <div class="shortcode-box">
                                        <code>[rating_form service="اسم الخدمة" title="اترك تقييماً"]</code>
                                        <button class="copy-btn" data-copy="[rating_form service=\"اسم الخدمة\" title=\"اترك تقييماً\"]">
                                            <i class="fas fa-copy"></i> نسخ
                                        </button>
                                    </div>
                                    <div class="shortcode-params">
                                        <h4>المعاملات:</h4>
                                        <ul>
                                            <li><strong>service</strong>: اسم الخدمة (اختياري)</li>
                                            <li><strong>title</strong>: عنوان النموذج (اختياري)</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Shortcode 2 -->
                                <div class="shortcode-card">
                                    <h3><i class="fas fa-star"></i> عرض التقييمات</h3>
                                    <p class="shortcode-desc">عرض قائمة التقييمات المكتملة</p>
                                    <div class="shortcode-box">
                                        <code>[service_ratings limit="10"]</code>
                                        <button class="copy-btn" data-copy="[service_ratings limit=\"10\"]">
                                            <i class="fas fa-copy"></i> نسخ
                                        </button>
                                    </div>
                                    <div class="shortcode-params">
                                        <h4>المعاملات:</h4>
                                        <ul>
                                            <li><strong>limit</strong>: عدد التقييمات المعروضة (افتراضي: 10)</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Shortcode 3 -->
                                <div class="shortcode-card">
                                    <h3><i class="fas fa-chart-bar"></i> الإحصائيات</h3>
                                    <p class="shortcode-desc">عرض إحصائيات التقييمات</p>
                                    <div class="shortcode-box">
                                        <code>[ratings_stats]</code>
                                        <button class="copy-btn" data-copy="[ratings_stats]">
                                            <i class="fas fa-copy"></i> نسخ
                                        </button>
                                    </div>
                                    <div class="shortcode-params">
                                        <h4>المعاملات:</h4>
                                        <ul>
                                            <li>بدون معاملات</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Shortcode 4 -->
                                <div class="shortcode-card">
                                    <h3><i class="fas fa-star-half-alt"></i> متوسط التقييم</h3>
                                    <p class="shortcode-desc">عرض متوسط التقييم فقط</p>
                                    <div class="shortcode-box">
                                        <code>[rating_average]</code>
                                        <button class="copy-btn" data-copy="[rating_average]">
                                            <i class="fas fa-copy"></i> نسخ
                                        </button>
                                    </div>
                                    <div class="shortcode-params">
                                        <h4>المعاملات:</h4>
                                        <ul>
                                            <li>بدون معاملات</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Display Options Tab -->
                    <div class="wsrp-tab-content" id="tab-display">
                        <div class="wsrp-tab-panel">
                            <h2>خيارات الظهور</h2>
                            
                            <table class="form-table wsrp-form-table">
                                <tr>
                                    <th scope="row">عدد التقييمات المعروضة</th>
                                    <td>
                                        <input type="number" 
                                            name="wsrp_settings[ratings_per_page]" 
                                            value="<?php echo isset($settings['ratings_per_page']) ? $settings['ratings_per_page'] : 10; ?>" 
                                            min="1" max="100"
                                        >
                                        <p class="description">عدد التقييمات المعروضة في الصفحة الواحدة</p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">ترتيب التقييمات</th>
                                    <td>
                                        <select name="wsrp_settings[sort_order]">
                                            <option value="newest" <?php selected($settings['sort_order'] ?? 'newest', 'newest'); ?>>الأحدث أولاً</option>
                                            <option value="oldest" <?php selected($settings['sort_order'] ?? 'oldest', 'oldest'); ?>>الأقدم أولاً</option>
                                            <option value="highest_rated" <?php selected($settings['sort_order'] ?? 'highest_rated', 'highest_rated'); ?>>الأعلى تقييماً</option>
                                            <option value="lowest_rated" <?php selected($settings['sort_order'] ?? 'lowest_rated', 'lowest_rated'); ?>>الأقل تقييماً</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">عرض متوسط التقييم</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[show_average]" 
                                            value="1" 
                                            <?php checked($settings['show_average'] ?? 1, 1); ?>
                                        >
                                        <label>عرض متوسط التقييم فوق قائمة التقييمات</label>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">عرض عدد التقييمات</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[show_count]" 
                                            value="1" 
                                            <?php checked($settings['show_count'] ?? 1, 1); ?>
                                        >
                                        <label>عرض عدد التقييمات الإجمالي</label>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">عرض تاريخ التقييم</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[show_date]" 
                                            value="1" 
                                            <?php checked($settings['show_date'] ?? 1, 1); ?>
                                        >
                                        <label>عرض تاريخ إنشاء التقييم</label>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">عرض الفئات</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[show_categories]" 
                                            value="1" 
                                            <?php checked($settings['show_categories'] ?? 1, 1); ?>
                                        >
                                        <label>عرض فئة الخدمة في كل تقييم</label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Functionality Tab -->
                    <div class="wsrp-tab-content" id="tab-functionality">
                        <div class="wsrp-tab-panel">
                            <h2>الوظائف والخيارات</h2>
                            
                            <table class="form-table wsrp-form-table">
                                <tr>
                                    <th scope="row">السماح بالتقييمات المجهولة</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[allow_anonymous]" 
                                            value="1" 
                                            <?php checked($settings['allow_anonymous'] ?? 1, 1); ?>
                                        >
                                        <label>السماح للزوار بتقييم بدون حساب</label>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">السماح بتقييمات متعددة من نفس الشخص</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[allow_multiple_ratings]" 
                                            value="1" 
                                            <?php checked($settings['allow_multiple_ratings'] ?? 0, 1); ?>
                                        >
                                        <label>السماح لنفس الشخص بتقييم أكثر من مرة</label>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">طول التعليق الأدنى</th>
                                    <td>
                                        <input type="number" 
                                            name="wsrp_settings[min_comment_length]" 
                                            value="<?php echo isset($settings['min_comment_length']) ? $settings['min_comment_length'] : 10; ?>" 
                                            min="0" max="500"
                                        >
                                        <p class="description">الحد الأدنى لعدد الأحرف في التعليق (0 = بدون حد)</p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">طول التعليق الأقصى</th>
                                    <td>
                                        <input type="number" 
                                            name="wsrp_settings[max_comment_length]" 
                                            value="<?php echo isset($settings['max_comment_length']) ? $settings['max_comment_length'] : 1000; ?>" 
                                            min="50" max="10000"
                                        >
                                        <p class="description">الحد الأقصى لعدد الأحرف في التعليق</p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">تفعيل البحث والتصفية</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[enable_search_filter]" 
                                            value="1" 
                                            <?php checked($settings['enable_search_filter'] ?? 1, 1); ?>
                                        >
                                        <label>السماح بالبحث والتصفية عبر التقييمات</label>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">تفعيل الفرز حسب التقييم</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[enable_rating_sort]" 
                                            value="1" 
                                            <?php checked($settings['enable_rating_sort'] ?? 1, 1); ?>
                                        >
                                        <label>السماح بفرز التقييمات من الأعلى إلى الأقل والعكس</label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Moderation Tab -->
                    <div class="wsrp-tab-content" id="tab-moderation">
                        <div class="wsrp-tab-panel">
                            <h2>إعدادات الموافقة والإشراف</h2>
                            
                            <table class="form-table wsrp-form-table">
                                <tr>
                                    <th scope="row">نظام الموافقة على التقييمات</th>
                                    <td>
                                        <select name="wsrp_settings[moderation_type]">
                                            <option value="auto_approve" <?php selected($settings['moderation_type'] ?? 'auto_approve', 'auto_approve'); ?>>الموافقة التلقائية</option>
                                            <option value="manual_review" <?php selected($settings['moderation_type'] ?? 'manual_review', 'manual_review'); ?>>المراجعة اليدوية (الافتراضي)</option>
                                        </select>
                                        <p class="description">اختر كيفية التعامل مع التقييمات الجديدة</p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">إرسال بريد عند تقييم جديد</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[email_on_new_rating]" 
                                            value="1" 
                                            <?php checked($settings['email_on_new_rating'] ?? 1, 1); ?>
                                        >
                                        <label>إرسال إشعار بريدي عند ورود تقييم جديد</label>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">حجب الكلمات المسيئة</th>
                                    <td>
                                        <textarea name="wsrp_settings[blocked_words]" rows="4" cols="50" placeholder="كلمة واحدة في كل سطر"><?php echo isset($settings['blocked_words']) ? $settings['blocked_words'] : ''; ?></textarea>
                                        <p class="description">أدخل الكلمات المسيئة التي تريد حجب التقييمات التي تحتويها (واحدة في كل سطر)</p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">رسالة تأكيد التقييم</th>
                                    <td>
                                        <textarea name="wsrp_settings[rating_message]" rows="3" cols="50"><?php echo isset($settings['rating_message']) ? $settings['rating_message'] : 'شكراً على تقييمك! سيتم عرضه بعد موافقة الإدارة.'; ?></textarea>
                                        <p class="description">الرسالة التي تظهر بعد إرسال التقييم</p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">عدد التقييمات المسموحة يومياً من نفس IP</th>
                                    <td>
                                        <input type="number" 
                                            name="wsrp_settings[ratings_per_ip_per_day]" 
                                            value="<?php echo isset($settings['ratings_per_ip_per_day']) ? $settings['ratings_per_ip_per_day'] : 5; ?>" 
                                            min="1" max="100"
                                        >
                                        <p class="description">حد أقصى لعدد التقييمات من نفس عنوان IP في اليوم الواحد</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Styling Tab -->
                    <div class="wsrp-tab-content" id="tab-styling">
                        <div class="wsrp-tab-panel">
                            <h2>إعدادات التصميم</h2>
                            
                            <table class="form-table wsrp-form-table">
                                <tr>
                                    <th scope="row">اللون الأساسي</th>
                                    <td>
                                        <input type="color" 
                                            name="wsrp_settings[primary_color]" 
                                            value="<?php echo isset($settings['primary_color']) ? $settings['primary_color'] : '#6c5ce7'; ?>"
                                        >
                                        <p class="description">اللون المستخدم في الأزرار والعناصر المهمة</p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">لون النجوم</th>
                                    <td>
                                        <input type="color" 
                                            name="wsrp_settings[stars_color]" 
                                            value="<?php echo isset($settings['stars_color']) ? $settings['stars_color'] : '#ffc107'; ?>"
                                        >
                                        <p class="description">لون النجوم في التقييمات</p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">عرض بطاقات التقييمات</th>
                                    <td>
                                        <select name="wsrp_settings[card_style]">
                                            <option value="grid" <?php selected($settings['card_style'] ?? 'grid', 'grid'); ?>>عرض شبكة (Grid)</option>
                                            <option value="list" <?php selected($settings['card_style'] ?? 'list', 'list'); ?>>عرض قائمة (List)</option>
                                        </select>
                                        <p class="description">طريقة عرض التقييمات</p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">عرض ظل البطاقة</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[show_shadow]" 
                                            value="1" 
                                            <?php checked($settings['show_shadow'] ?? 1, 1); ?>
                                        >
                                        <label>إضافة ظل أسفل بطاقات التقييمات</label>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">تفعيل الرسوم المتحركة</th>
                                    <td>
                                        <input type="checkbox" 
                                            name="wsrp_settings[enable_animations]" 
                                            value="1" 
                                            <?php checked($settings['enable_animations'] ?? 1, 1); ?>
                                        >
                                        <label>تفعيل الحركات الانتقالية والتأثيرات</label>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">مسافة بين التقييمات</th>
                                    <td>
                                        <input type="number" 
                                            name="wsrp_settings[card_spacing]" 
                                            value="<?php echo isset($settings['card_spacing']) ? $settings['card_spacing'] : 20; ?>" 
                                            min="5" max="50"
                                        > px
                                        <p class="description">المسافة بين بطاقات التقييمات بالبكسل</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php submit_button('حفظ الإعدادات', 'primary', 'submit', true, ['id' => 'wsrp-submit-btn']); ?>
                </form>
            </div>
        </div>
        <?php
    }

    private function get_default_settings() {
        return [
            'ratings_per_page' => 10,
            'sort_order' => 'newest',
            'show_average' => 1,
            'show_count' => 1,
            'show_date' => 1,
            'show_categories' => 1,
            'allow_anonymous' => 1,
            'allow_multiple_ratings' => 0,
            'min_comment_length' => 10,
            'max_comment_length' => 1000,
            'enable_search_filter' => 1,
            'enable_rating_sort' => 1,
            'moderation_type' => 'manual_review',
            'email_on_new_rating' => 1,
            'blocked_words' => '',
            'rating_message' => 'شكراً على تقييمك! سيتم عرضه بعد موافقة الإدارة.',
            'ratings_per_ip_per_day' => 5,
            'primary_color' => '#6c5ce7',
            'stars_color' => '#ffc107',
            'card_style' => 'grid',
            'show_shadow' => 1,
            'enable_animations' => 1,
            'card_spacing' => 20
        ];
    }
}

new WSRP_Settings();
?>