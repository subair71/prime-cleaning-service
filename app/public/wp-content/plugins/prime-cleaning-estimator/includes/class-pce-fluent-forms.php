<?php
/** Fluent Forms owns editable contact/enquiry fields; Prime retains authoritative quotes. */
defined('ABSPATH') || exit;

class PCE_Fluent_Forms {
    const OPTION = 'pce_fluent_forms_v1';
    private static $results = [];

    static function init() {
        add_action('admin_menu', [__CLASS__, 'menu']);
        add_action('admin_post_pce_fluent_setup', [__CLASS__, 'handle_setup']);
        add_filter('fluentform/validation_errors', [__CLASS__, 'validate'], 20, 3);
        add_filter('fluentform/insert_response_data', [__CLASS__, 'snapshot'], 20, 2);
        add_action('fluentform/before_insert_submission', [__CLASS__, 'before_insert'], 99, 3);
        add_action('fluentform/before_form_actions_processing', [__CLASS__, 'persist'], 10, 3);
        add_filter('fluentform/submission_confirmation', [__CLASS__, 'confirmation'], 20, 2);
        add_filter('fluentform/honeypot_status', function ($enabled, $id) { return self::kind($id) ? true : $enabled; }, 20, 2);
    }

    static function available() {
        return defined('FLUENTFORM_VERSION') && version_compare(FLUENTFORM_VERSION, '6.0', '>=') && function_exists('wpFluent') && shortcode_exists('fluentform');
    }

    static function kind($id) {
        $settings = get_option(self::OPTION, []);
        foreach (['contact', 'enquiry'] as $kind) if (!empty($settings[$kind]) && (int)$settings[$kind] === (int)$id) return $kind;
        return '';
    }

    static function ready($kind) {
        if (!self::available()) return false;
        $settings = get_option(self::OPTION, []);
        if (empty($settings['enabled']) || empty($settings[$kind])) return false;
        $form = wpFluent()->table('fluentform_forms')->find((int)$settings[$kind]);
        return $form && $form->status === 'published';
    }

    static function render($kind) {
        if (!self::ready($kind)) return '';
        $settings = get_option(self::OPTION, []);
        wp_enqueue_style('pce-fluent-forms', plugins_url('assets/css/fluent-forms.css', PCE_PLUGIN_FILE), ['fluent-form-styles'], PCE_VERSION);
        wp_enqueue_script('pce-fluent-forms', plugins_url('assets/js/fluent-forms.js', PCE_PLUGIN_FILE), ['fluent-form-submission'], PCE_VERSION, true);
        wp_localize_script('pce-fluent-forms', 'primeFluentForms', ['today'=>wp_date('Y-m-d')]);
        return '<div class="prime-fluent-form" data-prime-form="'.esc_attr($kind).'">'.do_shortcode('[fluentform id="'.absint($settings[$kind]).'"]').'</div>';
    }

    /** Reuse the installed vendor's field defaults, including editor metadata. */
    static function field($type, $name, $label, $required = false) {
        static $defaults;
        if ($defaults === null) $defaults = require FLUENTFORM_DIR_PATH.'app/Services/FormBuilder/DefaultElements.php';
        $field = null;
        foreach ($defaults as $group) if (isset($group[$type])) { $field = $group[$type]; break; }
        if (!$field) throw new RuntimeException('Fluent Forms field unavailable: '.$type);
        $field['attributes']['name'] = $name;
        $field['settings']['label'] = $label;
        $field['settings']['admin_field_label'] = $label;
        if ($type !== 'input_hidden') {
            $field['settings']['validation_rules']['required'] = ['value'=>$required, 'message'=>'Please complete this field.', 'global'=>false];
            $field['settings']['container_class'] = in_array($type, ['textarea','terms_and_condition'], true) ? 'prime-ff-full' : 'prime-ff-half';
        }
        $field['uniqElKey'] = 'prime_'.$name;
        return $field;
    }

    static function definition($kind) {
        $simple = $kind === 'contact';
        $fields = [];
        foreach (['name'=>['Full name','input_text','name'], 'phone'=>['Mobile number','input_text','tel'], 'email'=>['Email address','input_email','email'], 'suburb_name'=>['Suburb','input_text','address-level2']] as $key=>$f) {
            $field = self::field($f[1], $key, $f[0], true);
            $field['attributes']['autocomplete'] = $f[2];
            $field['attributes']['maxlength'] = $key === 'phone' ? 25 : 200;
            if ($key === 'phone') $field['attributes']['type'] = 'tel';
            if (!$simple && $key === 'suburb_name') $field['attributes']['readonly'] = true;
            $fields[] = $field;
        }
        if (!$simple) {
            $date = self::field('input_text', 'preferred_date', 'Preferred date', true);
            $date['attributes']['type'] = 'date';
            $fields[] = $date;
            $fields[] = self::field('input_text', 'additional_dates', 'Additional preferred dates (optional)');
            foreach (['time_preference'=>['Time preference (optional)', ['morning'=>'Morning','afternoon'=>'Afternoon']], 'parking'=>['Parking / access', ['available'=>'Parking available','limited'=>'Limited parking','discuss'=>'Please discuss access']], 'condition'=>['Property condition (optional)', ['maintained'=>'Regularly maintained','heavy'=>'Needs extra attention','discuss'=>'Please discuss']], 'pets'=>['Pets (optional)', ['yes'=>'Yes','no'=>'No']]] as $key=>$config) {
                $field = self::field('select', $key, $config[0], $key === 'parking');
                $field['settings']['advanced_options'] = [];
                foreach ($config[1] as $value=>$label) $field['settings']['advanced_options'][] = ['label'=>$label, 'value'=>$value, 'calc_value'=>''];
                $field['settings']['placeholder'] = 'Please select';
                $fields[] = $field;
            }
            $fields[] = self::field('input_text', 'referral', 'How did you hear about us? (optional)');
        }
        $notes = self::field('textarea', 'notes', $simple ? 'Your enquiry' : 'Notes (optional)', $simple);
        $notes['attributes']['maxlength'] = 3000;
        $notes['attributes']['rows'] = 4;
        $fields[] = $notes;
        $consent = self::field('terms_and_condition', 'consent', 'Contact consent', true);
        $consent['settings']['tnc_html'] = 'I agree to Prime Domestic contacting me about this enquiry. See our <a href="'.esc_url(get_privacy_policy_url() ?: home_url('/privacy-policy/')).'">Privacy Policy</a>.';
        $fields[] = $consent;
        foreach (['request_key'=>'Request key', 'quote_token'=>'Quote token', 'prime_estimate_summary'=>'Estimate summary', 'prime_estimate'=>'Frozen estimate'] as $key=>$label) $fields[] = self::field('input_hidden', $key, $label);
        $blank = \FluentForm\App\Models\Form::resolvePredefinedForm(['predefined'=>'blank_form']);
        $definition = json_decode($blank['form_fields'], true);
        $definition['fields'] = $fields;
        $definition['submitButton']['settings']['button_ui']['text'] = $simple ? 'Send message' : 'Send my enquiry →';
        return $definition;
    }

    /** Explicit, idempotent setup. Never overwrite forms edited in the visual builder. */
    static function setup() {
        if (!self::available()) return new WP_Error('dependency', 'Install and activate Fluent Forms 6.x first.');
        $settings = get_option(self::OPTION, []);
        foreach (['contact', 'enquiry'] as $kind) {
            if (!empty($settings[$kind]) && wpFluent()->table('fluentform_forms')->find((int)$settings[$kind])) continue;
            $form = \FluentForm\App\Models\Form::create(\FluentForm\App\Models\Form::prepare([
                'title'=>'Prime — '.($kind === 'contact' ? 'Contact' : 'Quote enquiry'),
                'form_fields'=>wp_json_encode(self::definition($kind)), 'status'=>'published'
            ]));
            $formSettings = \FluentForm\App\Models\Form::getFormsDefaultSettings();
            $formSettings['confirmation']['messageToShow'] = 'Thank you. Your enquiry has been saved. Your preferred date is a request, not a confirmed booking.';
            $formSettings['confirmation']['samePageFormBehavior'] = 'hide_form';
            $formSettings['layout']['cssClassName'] = 'prime-ff-fields';
            \FluentForm\App\Models\FormMeta::persist($form->id, 'formSettings', $formSettings);
            // The existing Prime notification workflow sends once, with frozen prices and a reference.
            \FluentForm\App\Models\FormMeta::persist($form->id, 'notifications', []);
            $settings[$kind] = (int)$form->id;
            update_option(self::OPTION, $settings, false);
        }
        $settings['enabled'] = true;
        update_option(self::OPTION, $settings, false);
        return $settings;
    }

    static function payload($data) {
        $p = [];
        foreach (['name','phone','email','suburb_name','preferred_date','additional_dates','time_preference','parking','condition','pets','referral','notes','quote_token','request_key'] as $key) $p[$key] = isset($data[$key]) && is_scalar($data[$key]) ? (string)$data[$key] : '';
        $p['consent'] = !empty($data['consent']);
        return $p;
    }

    static function validate($errors, $data, $form) {
        $kind = self::kind($form->id);
        if (!$kind) return $errors;
        $p = self::payload($data);
        $extra = PCE_Flow::validate_contact($p, $kind === 'contact');
        if (!preg_match('/^[a-zA-Z0-9-]{24,80}$/', $p['request_key'])) $extra['request_key'] = 'Refresh this page before sending your enquiry.';
        if ($kind === 'enquiry' && !get_transient('pce_quote_'.$p['quote_token']) && !self::prior($p)) $extra['quote_token'] = 'Your estimate has expired. Use Back to review your estimate, then try again. Your details will stay here.';
        foreach ($extra as $key=>$message) $errors[$key][] = $message;
        return $errors;
    }

    static function snapshot($data, $formId) {
        if (!self::kind($formId)) return $data;
        $p = self::payload($data);
        $quote = self::kind($formId) === 'enquiry' ? get_transient('pce_quote_'.$p['quote_token']) : false;
        // Never store a browser-supplied price, service or snapshot as authoritative.
        $data['prime_estimate'] = $quote ? wp_json_encode($quote) : '';
        $data['prime_estimate_summary'] = $quote && $quote['state'] === 'estimate' ? 'AUD '.$quote['low'].'–'.$quote['high'] : 'Custom quote required';
        if ($quote && !empty($quote['suburb_details'])) $data['suburb_name'] = $quote['suburb_details']['name'];
        return $data;
    }

    static function prior($p) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare('SELECT id FROM '.PCE_DB::get_table_name().' WHERE request_key=%s', hash('sha256', $p['request_key'])));
    }

    /** A retry reuses the Fluent entry as well as Prime's unique request key. */
    static function before_insert($insert, $data, $form) {
        if (!self::kind($form->id)) return;
        $p = self::payload($data);
        $key = hash('sha256', $form->id.':'.$p['request_key']);
        $lock = 'pce_ff_lock_'.$key;
        if (!add_option($lock, time(), '', false)) {
            if ((int)get_option($lock) < time()-120) delete_option($lock);
            wp_send_json(['errors'=>['request_key'=>['Your enquiry is being processed. Please retry in a moment.']]], 409);
        }
        register_shutdown_function(function () use ($lock) { delete_option($lock); });
        $entryId = get_transient('pce_ff_entry_'.$key);
        if ($entryId && wpFluent()->table('fluentform_submissions')->find((int)$entryId)) {
            self::persist((int)$entryId, $data, $form);
            wp_send_json_success(['insert_id'=>(int)$entryId, 'result'=>self::confirmation([], $form), 'error'=>'']);
        }
    }

    static function persist($entryId, $data, $form) {
        $kind = self::kind($form->id);
        if (!$kind) return;
        $p = self::payload($data);
        set_transient('pce_ff_entry_'.hash('sha256', $form->id.':'.$p['request_key']), (int)$entryId, DAY_IN_SECONDS);
        $result = PCE_Flow::persist($p, $kind === 'contact');
        if (is_wp_error($result)) wp_send_json(['errors'=>['request_key'=>[$result->get_error_message()]]], 422);
        self::$results[$form->id] = $result;
        \FluentForm\App\Helpers\Helper::setSubmissionMeta($entryId, 'prime_reference', $result['reference'], $form->id);
    }

    static function confirmation($return, $form) {
        $r = self::$results[$form->id] ?? null;
        if (!$r) return $return;
        $message = '<h3>Thanks '.esc_html($r['first_name']).'!</h3><p>Your reference: <strong>'.esc_html($r['reference']).'</strong></p>';
        if ($r['range']) $message .= '<p>Your estimate: AUD '.esc_html($r['range']['low']).'–'.esc_html($r['range']['high']).'</p>';
        else $message .= '<p>This one needs a proper look, so we’ll come back to you with a quote.</p>';
        $message .= '<p>We aim to reply within 2 hours during the day, and always within 24 hours.</p><p>Your preferred date is a request, not a confirmed booking.</p>';
        if ($r['whatsapp']) $message .= '<p><a href="'.esc_url($r['whatsapp']).'">Send photos on WhatsApp →</a></p>';
        return ['message'=>$message, 'action'=>'hide_form'];
    }

    static function menu() {
        add_submenu_page('prime-estimator', 'Fluent Forms', 'Fluent Forms', 'manage_options', 'pce-fluent-forms', [__CLASS__, 'admin_page']);
        // Also reachable under Settings if the legacy parent slug changes.
        add_options_page('Prime Fluent Forms', 'Prime Fluent Forms', 'manage_options', 'pce-fluent-forms', [__CLASS__, 'admin_page']);
    }

    static function admin_page() {
        if (!current_user_can('manage_options')) return;
        echo '<div class="wrap"><h1>Prime Fluent Forms</h1><p>Use the visual Fluent Forms editor for contact and quote-enquiry fields. Prime keeps the existing design, price engine, enquiry statuses and notification tracking.</p>';
        if (!self::available()) {
            echo '<p>Install and activate <a href="'.esc_url(admin_url('plugin-install.php?s=fluentform&tab=search&type=term')).'">Fluent Forms</a> (free, version 6.x).</p></div>';
            return;
        }
        $settings = get_option(self::OPTION, []);
        foreach (['contact'=>'Contact', 'enquiry'=>'Quote enquiry'] as $kind=>$label) if (!empty($settings[$kind])) echo '<p><a href="'.esc_url(admin_url('admin.php?page=fluent_forms&route=editor&form_id='.(int)$settings[$kind])).'">Edit '.$label.' form</a></p>';
        echo '<p>Current mode: <strong>'.(!empty($settings['enabled']) ? 'Fluent Forms' : 'Original forms').'</strong>. Setup preserves existing forms and page content. Keep field names and the hidden integration fields.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'">';
        wp_nonce_field('pce_fluent_setup');
        echo '<input type="hidden" name="action" value="pce_fluent_setup">';
        submit_button('Create missing forms and enable Fluent Forms');
        echo '<button class="button" name="disable" value="1">Use original forms (rollback)</button></form></div>';
    }

    static function handle_setup() {
        if (!current_user_can('manage_options')) wp_die('Not authorised.');
        check_admin_referer('pce_fluent_setup');
        if (isset($_POST['disable'])) { $settings = get_option(self::OPTION, []); $settings['enabled'] = false; update_option(self::OPTION, $settings, false); }
        else { $result = self::setup(); if (is_wp_error($result)) wp_die(esc_html($result->get_error_message())); }
        wp_safe_redirect(admin_url('options-general.php?page=pce-fluent-forms'));
        exit;
    }
}
