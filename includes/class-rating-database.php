<?php

if (!defined('ABSPATH')) {
    exit;
}

class WSRP_Rating_Database {

    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'service_ratings';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            customer_name VARCHAR(100) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            customer_email VARCHAR(100) NOT NULL,
            service_name VARCHAR(150) NOT NULL,
            rating INT(2) NOT NULL DEFAULT 5,
            comment LONGTEXT NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            is_approved TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY rating (rating),
            KEY is_approved (is_approved),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public static function insert_rating($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';

        $wpdb->insert(
            $table_name,
            [
                'customer_name' => sanitize_text_field($data['name']),
                'customer_phone' => sanitize_text_field($data['phone']),
                'customer_email' => sanitize_email($data['email']),
                'service_name' => sanitize_text_field($data['service']),
                'rating' => intval($data['rating']),
                'comment' => sanitize_textarea_field($data['comment']),
                'ip_address' => self::get_client_ip(),
                'is_approved' => 0
            ],
            ['%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d']
        );

        return $wpdb->insert_id;
    }

    public static function get_ratings($approved_only = true, $limit = 10, $offset = 0) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';

        $where = $approved_only ? "WHERE is_approved = 1" : "";
        $query = $wpdb->prepare(
            "SELECT * FROM $table_name $where ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        );

        return $wpdb->get_results($query);
    }

    public static function get_all_ratings($limit = 50, $offset = 0) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';

        $query = $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        );

        return $wpdb->get_results($query);
    }

    public static function get_rating_by_id($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            intval($id)
        ));
    }

    public static function update_rating($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';

        $wpdb->update(
            $table_name,
            [
                'customer_name' => sanitize_text_field($data['name']),
                'customer_phone' => sanitize_text_field($data['phone']),
                'customer_email' => sanitize_email($data['email']),
                'service_name' => sanitize_text_field($data['service']),
                'rating' => intval($data['rating']),
                'comment' => sanitize_textarea_field($data['comment']),
                'is_approved' => isset($data['is_approved']) ? intval($data['is_approved']) : 0
            ],
            ['id' => intval($id)],
            ['%s', '%s', '%s', '%s', '%d', '%s', '%d'],
            ['%d']
        );
    }

    public static function delete_rating($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';

        $wpdb->delete($table_name, ['id' => intval($id)], ['%d']);
    }

    public static function approve_rating($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';

        $wpdb->update(
            $table_name,
            ['is_approved' => 1],
            ['id' => intval($id)],
            ['%d'],
            ['%d']
        );
    }

    public static function count_ratings($approved_only = true) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';

        $where = $approved_only ? "WHERE is_approved = 1" : "";
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where");
    }

    public static function get_average_rating() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'service_ratings';

        $avg = $wpdb->get_var("SELECT AVG(rating) FROM $table_name WHERE is_approved = 1");
        return $avg ? round($avg, 1) : 0;
    }

    private static function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return sanitize_text_field($ip);
    }
}
?>