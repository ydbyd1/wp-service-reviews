<?php

if (!defined('ABSPATH')) {
    exit;
}

class WSRP_Admin_Menu {

    public function __construct() {
        add_action('admin_init', [$this, 'handle_admin_actions']);
    }

    public function handle_admin_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // الموافقة على التقييم
        if (isset($_GET['action']) && $_GET['action'] === 'approve' && isset($_GET['id'])) {
            check_admin_referer('wsrp_approve_' . $_GET['id']);
            WSRP_Rating_Database::approve_rating(intval($_GET['id']));
        }

        // حذف التقييم
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            check_admin_referer('wsrp_delete_' . $_GET['id']);
            WSRP_Rating_Database::delete_rating(intval($_GET['id']));
        }
    }

    public function render_page() {
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
        ?>
        <div class="wrap">
            <h1>تقييمات الخدمات</h1>
            
            <div class="nav-tab-wrapper">
                <a href="?page=service-reviews&tab=all" class="nav-tab <?php echo $tab === 'all' ? 'nav-tab-active' : ''; ?>">
                    جميع التقييمات
                </a>
                <a href="?page=service-reviews&tab=pending" class="nav-tab <?php echo $tab === 'pending' ? 'nav-tab-active' : ''; ?>">
                    بانتظار الموافقة
                </a>
                <a href="?page=service-reviews&tab=approved" class="nav-tab <?php echo $tab === 'approved' ? 'nav-tab-active' : ''; ?>">
                    الموافق عليها
                </a>
                <a href="?page=service-reviews&tab=stats" class="nav-tab <?php echo $tab === 'stats' ? 'nav-tab-active' : ''; ?>">
                    الإحصائيات
                </a>
            </div>

            <?php
            switch ($tab) {
                case 'pending':
                    $this->render_pending_ratings();
                    break;
                case 'approved':
                    $this->render_approved_ratings();
                    break;
                case 'stats':
                    $this->render_statistics();
                    break;
                default:
                    $this->render_all_ratings();
            }
            ?>
        </div>
        <?php
    }

    private function render_all_ratings() {
        $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $ratings = WSRP_Rating_Database::get_all_ratings($per_page, $offset);
        
        $this->render_ratings_table($ratings);
    }

    private function render_pending_ratings() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';
        $ratings = $wpdb->get_results("SELECT * FROM $table_name WHERE is_approved = 0 ORDER BY created_at DESC");
        
        ?>
        <div class="wsrp-pending-ratings">
            <h2>التقييمات بانتظار الموافقة</h2>
            <?php
            if (!empty($ratings)) {
                foreach ($ratings as $rating) {
                    $this->render_rating_detail($rating);
                }
            } else {
                echo '<p>لا توجد تقييمات بانتظار الموافقة</p>';
            }
            ?>
        </div>
        <?php
    }

    private function render_approved_ratings() {
        $ratings = WSRP_Rating_Database::get_ratings(true, 50);
        
        ?>
        <div class="wsrp-approved-ratings">
            <h2>التقييمات الموافق عليها</h2>
            <?php
            if (!empty($ratings)) {
                foreach ($ratings as $rating) {
                    $this->render_rating_detail($rating);
                }
            } else {
                echo '<p>لا توجد تقييمات موافق عليها</p>';
            }
            ?>
        </div>
        <?php
    }

    private function render_statistics() {
        $total = WSRP_Rating_Database::count_ratings(false);
        $approved = WSRP_Rating_Database::count_ratings(true);
        $pending = $total - $approved;
        $avg_rating = WSRP_Rating_Database::get_average_rating();
        
        ?>
        <div class="wsrp-statistics">
            <div class="wsrp-stat-box">
                <h3>إجمالي التقييمات</h3>
                <p class="wsrp-stat-number"><?php echo esc_html($total); ?></p>
            </div>
            
            <div class="wsrp-stat-box">
                <h3>الموافق عليها</h3>
                <p class="wsrp-stat-number"><?php echo esc_html($approved); ?></p>
            </div>
            
            <div class="wsrp-stat-box">
                <h3>بانتظار الموافقة</h3>
                <p class="wsrp-stat-number"><?php echo esc_html($pending); ?></p>
            </div>
            
            <div class="wsrp-stat-box">
                <h3>متوسط التقييم</h3>
                <p class="wsrp-stat-number"><?php echo esc_html($avg_rating); ?> ⭐</p>
            </div>
        </div>
        <?php
    }

    private function render_ratings_table($ratings) {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الهاتف</th>
                    <th>الخدمة</th>
                    <th>التقييم</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($ratings)) {
                    foreach ($ratings as $rating) {
                        ?>
                        <tr>
                            <td><?php echo esc_html($rating->customer_name); ?></td>
                            <td><?php echo esc_html($rating->customer_email); ?></td>
                            <td><?php echo esc_html($rating->customer_phone); ?></td>
                            <td><?php echo esc_html($rating->service_name); ?></td>
                            <td>
                                <span class="wsrp-rating-badge">
                                    <?php echo str_repeat('⭐', intval($rating->rating)); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $rating->is_approved ? '<span style="color:green;">✓ موافق عليه</span>' : '<span style="color:orange;">⏳ بانتظار</span>'; ?>
                            </td>
                            <td><?php echo date_i18n('j F Y', strtotime($rating->created_at)); ?></td>
                            <td>
                                <button class="wsrp-btn-view" data-id="<?php echo esc_attr($rating->id); ?>">عرض</button>
                                <?php if (!$rating->is_approved) : ?>
                                    <a href="<?php echo wp_nonce_url('?page=service-reviews&action=approve&id=' . $rating->id, 'wsrp_approve_' . $rating->id); ?>" class="wsrp-btn-approve">موافقة</a>
                                <?php endif; ?>
                                <a href="<?php echo wp_nonce_url('?page=service-reviews&action=delete&id=' . $rating->id, 'wsrp_delete_' . $rating->id); ?>" class="wsrp-btn-delete" onclick="return confirm('هل أنت متأكد من الحذف؟');">حذف</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="8" style="text-align:center;">لا توجد تقييمات</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <?php
    }

    private function render_rating_detail($rating) {
        ?>
        <div class="wsrp-rating-card">
            <div class="wsrp-card-header">
                <h3><?php echo esc_html($rating->customer_name); ?></h3>
                <span class="wsrp-rating-stars"><?php echo str_repeat('⭐', intval($rating->rating)); ?></span>
            </div>
            
            <div class="wsrp-card-body">
                <p><strong>البريد الإلكتروني:</strong> <?php echo esc_html($rating->customer_email); ?></p>
                <p><strong>الهاتف:</strong> <?php echo esc_html($rating->customer_phone); ?></p>
                <p><strong>عنوان IP:</strong> <?php echo esc_html($rating->ip_address); ?></p>
                <p><strong>الخدمة:</strong> <?php echo esc_html($rating->service_name); ?></p>
                <p><strong>التعليق:</strong></p>
                <div class="wsrp-comment-box"><?php echo wp_kses_post($rating->comment); ?></div>
                <p class="wsrp-card-date"><?php echo date_i18n('j F Y H:i', strtotime($rating->created_at)); ?></p>
            </div>
            
            <div class="wsrp-card-actions">
                <?php if (!$rating->is_approved) : ?>
                    <a href="<?php echo wp_nonce_url('?page=service-reviews&action=approve&id=' . $rating->id, 'wsrp_approve_' . $rating->id); ?>" class="button button-primary">موافقة</a>
                <?php endif; ?>
                <a href="<?php echo wp_nonce_url('?page=service-reviews&action=delete&id=' . $rating->id, 'wsrp_delete_' . $rating->id); ?>" class="button button-secondary" onclick="return confirm('هل أنت متأكد من الحذف؟');">حذف</a>
            </div>
        </div>
        <?php
    }
}
?>