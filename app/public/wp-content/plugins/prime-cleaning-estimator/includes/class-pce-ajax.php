<?php
/**
 * AJAX endpoints for Prime Cleaning Estimator
 */

defined('ABSPATH') || exit;

class PCE_AJAX {

    public static function init() {
        // Calculation endpoint (authenticated and guest)
        add_action('wp_ajax_pce_calculate', array(__CLASS__, 'handle_calculate'));
        add_action('wp_ajax_nopriv_pce_calculate', array(__CLASS__, 'handle_calculate'));

        // Quote submission endpoint (authenticated and guest)
        add_action('wp_ajax_pce_submit_quote', array(__CLASS__, 'handle_submit_quote'));
        add_action('wp_ajax_nopriv_pce_submit_quote', array(__CLASS__, 'handle_submit_quote'));

        // Admin status update endpoint
        add_action('wp_ajax_pce_update_status', array(__CLASS__, 'handle_update_status'));
    }

    public static function handle_calculate() {
        check_ajax_referer('pce_estimator_nonce', 'nonce');
        if (sanitize_key($_POST['service'] ?? '') === 'office' && !current_user_can('manage_options')) wp_send_json_error(['message'=>'Office cleaning is not available in Phase 1.'], 422);

        $params = array(
            'service'      => sanitize_text_field($_POST['service'] ?? 'regular'),
            'postcode'     => sanitize_text_field($_POST['postcode'] ?? ''),
            'suburb'       => sanitize_text_field($_POST['suburb'] ?? ''),
            'bedrooms'     => isset($_POST['bedrooms']) ? absint($_POST['bedrooms']) : 2,
            'bathrooms'    => isset($_POST['bathrooms']) ? absint($_POST['bathrooms']) : 1,
            'frequency'    => sanitize_text_field($_POST['frequency'] ?? 'once'),
            'office_hours' => isset($_POST['office_hours']) ? floatval($_POST['office_hours']) : 2.0,
        );

        $breakdown = PCE_Calculator::calculate($params);
        wp_send_json_success($breakdown);
    }

    public static function handle_submit_quote() {
        check_ajax_referer('pce_estimator_nonce', 'nonce');
        if (sanitize_key($_POST['service'] ?? '') === 'office' && !current_user_can('manage_options')) wp_send_json_error(['message'=>'Office cleaning is not available in Phase 1.'], 422);

        // Honeypot spam check
        if (!empty($_POST['pce_hp_url'])) {
            wp_send_json_error(array('message' => __('Spam detected.', 'prime-cleaning-estimator')));
        }

        // Timing spam check (must take at least 2 seconds)
        $rendered_time = isset($_POST['pce_render_ts']) ? absint($_POST['pce_render_ts']) : 0;
        if ($rendered_time > 0 && (time() - $rendered_time) < 2) {
            wp_send_json_error(array('message' => __('Submission too fast. Please retry.', 'prime-cleaning-estimator')));
        }

        // Consent check
        if (empty($_POST['consent'])) {
            wp_send_json_error(array('message' => __('Please confirm your consent to be contacted regarding this quote.', 'prime-cleaning-estimator')));
        }

        // Required customer info
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $suburb = sanitize_text_field($_POST['suburb'] ?? '');
        $postcode = sanitize_text_field($_POST['postcode'] ?? '');
        $service = sanitize_text_field($_POST['service'] ?? 'regular');
        $preferred_date = sanitize_text_field($_POST['preferred_date'] ?? '');
        $notes = sanitize_textarea_field($_POST['notes'] ?? '');

        if (empty($name)) {
            wp_send_json_error(array('message' => __('Please provide your name.', 'prime-cleaning-estimator')));
        }
        if (empty($email) || !is_email($email)) {
            wp_send_json_error(array('message' => __('Please provide a valid email address.', 'prime-cleaning-estimator')));
        }
        if (empty($phone)) {
            wp_send_json_error(array('message' => __('Please provide a contact phone number.', 'prime-cleaning-estimator')));
        }
        if (empty($postcode) || !preg_match('/^\d{4}$/', $postcode)) {
            wp_send_json_error(array('message' => __('Please provide a 4-digit postcode.', 'prime-cleaning-estimator')));
        }

        // Preferred date validation (must not be in the past based on WP site timezone)
        if (!empty($preferred_date)) {
            $site_timezone = wp_timezone();
            $today = new DateTime('today', $site_timezone);
            $selected_dt = DateTime::createFromFormat('Y-m-d', $preferred_date, $site_timezone);

            if (!$selected_dt || $selected_dt < $today) {
                wp_send_json_error(array('message' => __('The preferred date cannot be in the past.', 'prime-cleaning-estimator')));
            }
        }

        // Server-side price recalculation (Never trust client prices)
        $calc_params = array(
            'service'      => $service,
            'postcode'     => $postcode,
            'suburb'       => $suburb,
            'bedrooms'     => isset($_POST['bedrooms']) ? absint($_POST['bedrooms']) : 2,
            'bathrooms'    => isset($_POST['bathrooms']) ? absint($_POST['bathrooms']) : 1,
            'frequency'    => sanitize_text_field($_POST['frequency'] ?? 'once'),
            'office_hours' => isset($_POST['office_hours']) ? floatval($_POST['office_hours']) : 2.0,
        );

        $breakdown = PCE_Calculator::calculate($calc_params);

        $property_details = array(
            'service'      => $service,
            'service_name' => $breakdown['service_name'],
            'bedrooms'     => $calc_params['bedrooms'],
            'bathrooms'    => $calc_params['bathrooms'],
            'frequency'    => $calc_params['frequency'],
            'frequency_name' => $breakdown['frequency_name'],
            'office_hours' => $calc_params['office_hours'],
        );

        $enquiry_data = array(
            'name'               => $name,
            'email'              => $email,
            'phone'              => $phone,
            'suburb'             => $suburb,
            'postcode'           => $postcode,
            'service_type'       => $service,
            'is_in_area'         => $breakdown['is_in_area'],
            'property_details'   => $property_details,
            'estimate_breakdown' => $breakdown,
            'total_amount'       => $breakdown['final_total'],
            'preferred_date'     => !empty($preferred_date) ? $preferred_date : null,
            'notes'              => $notes,
            'email_sent'         => 0,
            'email_error'        => null,
        );

        // Save enquiry to database
        $enquiry_id = PCE_DB::insert_enquiry($enquiry_data);
        if (!$enquiry_id) {
            wp_send_json_error(array('message' => __('Could not save enquiry. Please try again or contact us directly.', 'prime-cleaning-estimator')));
        }

        // Email notification attempt
        $settings = PCE_Settings::get_all();
        $email_sent = 0;
        $email_error = '';

        if (!empty($settings['enable_email_notice']) && !empty($settings['notification_recipient'])) {
            $to = $settings['notification_recipient'];
            $subject = sprintf('[New Quote Enquiry #%d] %s — %s', $enquiry_id, $name, $breakdown['service_name']);
            
            $body  = "New cleaning quote enquiry received:\r\n\r\n";
            $body .= "Enquiry ID: #" . $enquiry_id . "\r\n";
            $body .= "Customer: " . $name . "\r\n";
            $body .= "Email: " . $email . "\r\n";
            $body .= "Phone: " . $phone . "\r\n";
            $body .= "Location: " . $suburb . " " . $postcode . " (" . ($breakdown['is_in_area'] ? 'In service area' : 'OUTSIDE area — manual quote') . ")\r\n";
            $body .= "Service: " . $breakdown['service_name'] . "\r\n";
            if ($service === 'office') {
                $body .= "Estimated Hours: " . $calc_params['office_hours'] . " hrs\r\n";
            } else {
                $body .= "Property: " . $calc_params['bedrooms'] . " Bed / " . $calc_params['bathrooms'] . " Bath\r\n";
                $body .= "Frequency: " . $breakdown['frequency_name'] . "\r\n";
            }
            $body .= "Estimated Amount: AUD $" . number_format($breakdown['final_total'], 2) . ($breakdown['is_manual_quote'] ? ' (Subject to manual quote)' : '') . "\r\n";
            $body .= "Preferred Date: " . (!empty($preferred_date) ? $preferred_date : 'Flexible / Not specified') . "\r\n";
            $body .= "Notes: " . (!empty($notes) ? $notes : 'None') . "\r\n\r\n";
            $body .= "View enquiry in WordPress admin: " . admin_url('admin.php?page=prime-estimator&tab=enquiries&enquiry_id=' . $enquiry_id) . "\r\n";

            $headers = array('Content-Type: text/plain; charset=UTF-8');
            if (!empty($email)) {
                $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
            }

            // Note: wp_mail return value is not guaranteed proof of inbox delivery
            $mail_result = @wp_mail($to, $subject, $body, $headers);
            if ($mail_result) {
                $email_sent = 1;
            } else {
                global $phpmailer;
                $email_error = (isset($phpmailer) && is_object($phpmailer) && !empty($phpmailer->ErrorInfo)) 
                    ? $phpmailer->ErrorInfo 
                    : 'wp_mail returned false or local mail transport error';
            }

            // Update email status on record
            global $wpdb;
            $wpdb->update(
                PCE_DB::get_table_name(),
                array('email_sent' => $email_sent, 'email_error' => $email_error),
                array('id' => $enquiry_id),
                array('%d', '%s'),
                array('%d')
            );
        }

        wp_send_json_success(array(
            'enquiry_id'     => $enquiry_id,
            'customer_name'  => esc_html($name),
            'service_name'   => esc_html($breakdown['service_name']),
            'total_amount'   => number_format($breakdown['final_total'], 2),
            'is_in_area'     => $breakdown['is_in_area'],
            'is_manual'      => $breakdown['is_manual_quote'],
            'preferred_date' => esc_html($preferred_date),
            'message'        => __('Your enquiry has been received successfully! Our team will review your details and be in touch promptly.', 'prime-cleaning-estimator')
        ));
    }

    public static function handle_update_status() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'prime-cleaning-estimator')));
        }
        check_ajax_referer('pce_admin_nonce', 'nonce');

        $id = isset($_POST['enquiry_id']) ? absint($_POST['enquiry_id']) : 0;
        $status = sanitize_text_field($_POST['status'] ?? 'new');

        $updated = PCE_DB::update_enquiry_status($id, $status);
        if ($updated !== false) {
            wp_send_json_success(array('status' => $status, 'message' => __('Status updated.', 'prime-cleaning-estimator')));
        } else {
            wp_send_json_error(array('message' => __('Could not update status.', 'prime-cleaning-estimator')));
        }
    }
}
