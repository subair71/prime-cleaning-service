<?php
/**
 * Database management for Prime Cleaning Estimator
 */

defined('ABSPATH') || exit;

class PCE_DB {

    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'pce_enquiries';
    }

    public static function create_tables() {
        global $wpdb;
        $table_name = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'new',
            customer_name varchar(150) NOT NULL,
            customer_email varchar(150) NOT NULL,
            customer_phone varchar(50) NOT NULL,
            suburb varchar(100) NOT NULL,
            postcode varchar(10) NOT NULL,
            service_type varchar(50) NOT NULL,
            is_in_area tinyint(1) NOT NULL DEFAULT 1,
            property_details longtext NOT NULL,
            estimate_breakdown longtext NOT NULL,
            total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            preferred_date date DEFAULT NULL,
            notes text,
            email_sent tinyint(1) NOT NULL DEFAULT 0,
            email_error text,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public static function insert_enquiry($data) {
        global $wpdb;
        $table_name = self::get_table_name();

        $insert_data = array(
            'created_at'         => current_time('mysql'),
            'status'             => 'new',
            'customer_name'      => sanitize_text_field($data['name'] ?? ''),
            'customer_email'     => sanitize_email($data['email'] ?? ''),
            'customer_phone'     => sanitize_text_field($data['phone'] ?? ''),
            'suburb'             => sanitize_text_field($data['suburb'] ?? ''),
            'postcode'           => sanitize_text_field($data['postcode'] ?? ''),
            'service_type'       => sanitize_text_field($data['service_type'] ?? 'regular'),
            'is_in_area'         => !empty($data['is_in_area']) ? 1 : 0,
            'property_details'   => wp_json_encode($data['property_details'] ?? array()),
            'estimate_breakdown' => wp_json_encode($data['estimate_breakdown'] ?? array()),
            'total_amount'       => floatval($data['total_amount'] ?? 0),
            'preferred_date'     => !empty($data['preferred_date']) ? sanitize_text_field($data['preferred_date']) : null,
            'notes'              => sanitize_textarea_field($data['notes'] ?? ''),
            'email_sent'         => !empty($data['email_sent']) ? 1 : 0,
            'email_error'        => !empty($data['email_error']) ? sanitize_textarea_field($data['email_error']) : null,
        );

        $formats = array(
            '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%f', '%s', '%s', '%d', '%s'
        );

        $result = $wpdb->insert($table_name, $insert_data, $formats);
        if ($result === false) {
            return false;
        }

        return $wpdb->insert_id;
    }

    public static function update_enquiry_status($id, $status) {
        global $wpdb;
        $allowed = array('new', 'contacted', 'quoted', 'closed');
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        return $wpdb->update(
            self::get_table_name(),
            array('status' => $status),
            array('id' => absint($id)),
            array('%s'),
            array('%d')
        );
    }

    public static function get_enquiries($args = array()) {
        global $wpdb;
        $table_name = self::get_table_name();

        $where = array('1=1');
        $params = array();

        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $params[] = sanitize_text_field($args['status']);
        }

        $where_sql = implode(' AND ', $where);
        $order = !empty($args['order']) && strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        $limit = isset($args['limit']) ? absint($args['limit']) : 50;
        $offset = isset($args['offset']) ? absint($args['offset']) : 0;

        $sql = "SELECT * FROM $table_name WHERE $where_sql ORDER BY created_at $order LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;

        $prepared = $wpdb->prepare($sql, $params);
        return $wpdb->get_results($prepared, ARRAY_A);
    }

    public static function get_enquiry($id) {
        global $wpdb;
        $table_name = self::get_table_name();
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", absint($id));
        return $wpdb->get_row($sql, ARRAY_A);
    }

    public static function count_enquiries_by_status() {
        global $wpdb;
        $table_name = self::get_table_name();
        $results = $wpdb->get_results("SELECT status, COUNT(*) as count FROM $table_name GROUP BY status", ARRAY_A);
        $counts = array('all' => 0, 'new' => 0, 'contacted' => 0, 'quoted' => 0, 'closed' => 0);
        if ($results) {
            foreach ($results as $r) {
                $st = $r['status'];
                $cnt = (int)$r['count'];
                $counts[$st] = $cnt;
                $counts['all'] += $cnt;
            }
        }
        return $counts;
    }
}
