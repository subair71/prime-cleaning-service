<?php
/**
 * Settings and Rates configuration for Prime Cleaning Estimator
 */

defined('ABSPATH') || exit;

class PCE_Settings {

    const OPTION_KEY = 'pce_estimator_settings';

    public static function defaults() {
        return array(
            // Residential Base Rates
            'base_regular'             => 80.00,
            'bedroom_rate'             => 25.00,
            'bathroom_rate'            => 35.00,
            'multiplier_deep'          => 1.50,
            'multiplier_lease'         => 2.00,
            
            // Office Pricing Inputs
            'office_hourly_rate'       => 55.00,
            'office_min_hours'         => 2.0,
            'office_sqm_rate_per_100'  => 65.00, // alternate option

            // Frequency Discounts (% applied to regular cleaning)
            'discount_weekly'          => 15.0,
            'discount_fortnightly'     => 10.0,
            'discount_monthly'         => 0.0,

            // Minimum Charge
            'minimum_charge'           => 100.00,

            // Service Area Postcode Rules
            'base_suburb'              => 'Malvern East',
            'base_postcode'            => '3145',
            'postcode_prefixes'        => '314, 315',
            'allow_manual_quote'       => 1, // Offer manual enquiry path if out of area

            // Tax Treatment
            'tax_mode'                 => 'demo_unregistered', // 'demo_unregistered', 'inclusive', 'exclusive'
            'tax_rate_percent'         => 10.0,
            'tax_disclaimer'           => 'Demo pricing in AUD. GST registration and tax treatment are subject to business confirmation.',

            // Business Contact & Notifications
            'business_name'            => 'Prime Domestic & Cleaning Services',
            'business_phone'           => '1300 000 000',
            'business_email'           => 'info@example.com',
            'notification_recipient'   => get_option('admin_email'),
            'enable_email_notice'      => 1,
        );
    }

    public static function get_all() {
        $saved = get_option(self::OPTION_KEY, array());
        return wp_parse_args(is_array($saved) ? $saved : array(), self::defaults());
    }

    public static function get($key, $default = null) {
        $all = self::get_all();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function update($settings) {
        $clean = self::sanitize($settings);
        return update_option(self::OPTION_KEY, $clean);
    }

    public static function sanitize($input) {
        $defaults = self::defaults();
        $clean = array();

        // Numerics
        $float_fields = array(
            'base_regular', 'bedroom_rate', 'bathroom_rate', 'multiplier_deep',
            'multiplier_lease', 'office_hourly_rate', 'office_min_hours',
            'office_sqm_rate_per_100', 'discount_weekly', 'discount_fortnightly',
            'discount_monthly', 'minimum_charge', 'tax_rate_percent'
        );

        foreach ($float_fields as $field) {
            if (isset($input[$field]) && is_numeric($input[$field])) {
                $clean[$field] = max(0, round(floatval($input[$field]), 2));
            } else {
                $clean[$field] = $defaults[$field];
            }
        }

        // Strings
        $string_fields = array(
            'base_suburb', 'base_postcode', 'postcode_prefixes',
            'business_name', 'business_phone', 'tax_disclaimer'
        );
        foreach ($string_fields as $field) {
            $clean[$field] = isset($input[$field]) ? sanitize_text_field($input[$field]) : $defaults[$field];
        }

        // Emails
        $clean['business_email'] = !empty($input['business_email']) ? sanitize_email($input['business_email']) : $defaults['business_email'];
        $clean['notification_recipient'] = !empty($input['notification_recipient']) ? sanitize_email($input['notification_recipient']) : $defaults['notification_recipient'];

        // Booleans / Selects
        $clean['allow_manual_quote'] = !empty($input['allow_manual_quote']) ? 1 : 0;
        $clean['enable_email_notice'] = !empty($input['enable_email_notice']) ? 1 : 0;
        
        $allowed_tax_modes = array('demo_unregistered', 'inclusive', 'exclusive');
        $clean['tax_mode'] = isset($input['tax_mode']) && in_array($input['tax_mode'], $allowed_tax_modes, true) ? $input['tax_mode'] : 'demo_unregistered';

        return $clean;
    }

    public static function check_postcode_coverage($postcode) {
        $postcode = trim($postcode);
        if (!preg_match('/^\d{4}$/', $postcode)) {
            return false;
        }

        $prefixes_str = self::get('postcode_prefixes', '314, 315');
        $prefixes = array_map('trim', explode(',', $prefixes_str));

        foreach ($prefixes as $p) {
            $p_clean = preg_replace('/[^0-9]/', '', $p);
            if (!empty($p_clean) && str_starts_with($postcode, $p_clean)) {
                return true;
            }
        }

        return false;
    }
}
