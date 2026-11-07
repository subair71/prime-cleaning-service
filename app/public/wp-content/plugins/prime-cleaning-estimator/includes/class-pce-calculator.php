<?php
/**
 * Price calculation engine for Prime Cleaning Estimator
 */

defined('ABSPATH') || exit;

class PCE_Calculator {

    public static function calculate($params) {
        $settings = PCE_Settings::get_all();

        $service = sanitize_text_field($params['service'] ?? 'regular');
        $valid_services = array('regular', 'deep', 'lease', 'office');
        if (!in_array($service, $valid_services, true)) {
            $service = 'regular';
        }

        $postcode = sanitize_text_field($params['postcode'] ?? '');
        $suburb = sanitize_text_field($params['suburb'] ?? '');
        $is_in_area = PCE_Settings::check_postcode_coverage($postcode);

        $breakdown = array(
            'service'              => $service,
            'service_name'         => self::get_service_name($service),
            'postcode'             => $postcode,
            'suburb'               => $suburb,
            'is_in_area'           => $is_in_area,
            'coverage_note'        => '',
            'base_rate'            => 0.0,
            'bedrooms'             => 0,
            'bedroom_rate'         => 0.0,
            'bedrooms_cost'        => 0.0,
            'bathrooms'            => 0,
            'bathroom_rate'        => 0.0,
            'bathrooms_cost'       => 0.0,
            'office_hours'         => 0.0,
            'office_hourly_rate'   => 0.0,
            'service_multiplier'   => 1.0,
            'raw_subtotal'         => 0.0,
            'frequency'            => 'once',
            'frequency_name'       => 'One-off visit',
            'discount_percent'     => 0.0,
            'discount_amount'      => 0.0,
            'subtotal_after_disc'  => 0.0,
            'minimum_charge'       => floatval($settings['minimum_charge']),
            'minimum_applied'      => false,
            'tax_mode'             => $settings['tax_mode'],
            'tax_amount'           => 0.0,
            'tax_note'             => '',
            'final_total'          => 0.0,
            'is_manual_quote'      => false,
        );

        if (!$is_in_area && !empty($postcode)) {
            $breakdown['coverage_note'] = sprintf(
                __('Postcode %s is outside our standard automated service area (prefixes: %s). We will gladly prepare a custom manual quote.', 'prime-cleaning-estimator'),
                esc_html($postcode),
                esc_html($settings['postcode_prefixes'])
            );
            $breakdown['is_manual_quote'] = true;
        } elseif ($is_in_area) {
            $breakdown['coverage_note'] = sprintf(
                __('Covered by our Malvern East service hub (demo area prefixes: %s).', 'prime-cleaning-estimator'),
                esc_html($settings['postcode_prefixes'])
            );
        } else {
            $breakdown['coverage_note'] = __('Please enter a valid 4-digit Victorian postcode.', 'prime-cleaning-estimator');
        }

        if ($service === 'office') {
            // Office pricing
            $hours = isset($params['office_hours']) ? max(floatval($settings['office_min_hours']), floatval($params['office_hours'])) : floatval($settings['office_min_hours']);
            $hourly_rate = floatval($settings['office_hourly_rate']);
            $raw_subtotal = round($hours * $hourly_rate, 2);

            $frequency = sanitize_text_field($params['frequency'] ?? 'once');
            $disc_pct = 0.0;
            if ($frequency === 'weekly') {
                $disc_pct = floatval($settings['discount_weekly']);
                $breakdown['frequency_name'] = 'Weekly schedule';
            } elseif ($frequency === 'fortnightly') {
                $disc_pct = floatval($settings['discount_fortnightly']);
                $breakdown['frequency_name'] = 'Fortnightly schedule';
            }

            $disc_amt = round(($raw_subtotal * $disc_pct) / 100, 2);
            $subtotal_after = max(0, $raw_subtotal - $disc_amt);

            $breakdown['office_hours'] = $hours;
            $breakdown['office_hourly_rate'] = $hourly_rate;
            $breakdown['raw_subtotal'] = $raw_subtotal;
            $breakdown['frequency'] = $frequency;
            $breakdown['discount_percent'] = $disc_pct;
            $breakdown['discount_amount'] = $disc_amt;
            $breakdown['subtotal_after_disc'] = $subtotal_after;

        } else {
            // Residential: regular, deep, lease
            $bedrooms = isset($params['bedrooms']) ? max(1, min(10, absint($params['bedrooms']))) : 2;
            $bathrooms = isset($params['bathrooms']) ? max(1, min(10, absint($params['bathrooms']))) : 1;
            $base_rate = floatval($settings['base_regular']);
            $bedroom_rate = floatval($settings['bedroom_rate']);
            $bathroom_rate = floatval($settings['bathroom_rate']);

            $bedrooms_cost = round($bedrooms * $bedroom_rate, 2);
            $bathrooms_cost = round($bathrooms * $bathroom_rate, 2);
            $base_home_cost = $base_rate + $bedrooms_cost + $bathrooms_cost;

            $multiplier = 1.0;
            if ($service === 'deep') {
                $multiplier = floatval($settings['multiplier_deep']);
            } elseif ($service === 'lease') {
                $multiplier = floatval($settings['multiplier_lease']);
            }

            $raw_subtotal = round($base_home_cost * $multiplier, 2);

            $frequency = sanitize_text_field($params['frequency'] ?? 'once');
            $disc_pct = 0.0;
            if ($service === 'regular') {
                if ($frequency === 'weekly') {
                    $disc_pct = floatval($settings['discount_weekly']);
                    $breakdown['frequency_name'] = 'Weekly regular service';
                } elseif ($frequency === 'fortnightly') {
                    $disc_pct = floatval($settings['discount_fortnightly']);
                    $breakdown['frequency_name'] = 'Fortnightly regular service';
                }
            } else {
                $frequency = 'once';
                $breakdown['frequency_name'] = 'One-off comprehensive service';
            }

            $disc_amt = round(($raw_subtotal * $disc_pct) / 100, 2);
            $subtotal_after = max(0, $raw_subtotal - $disc_amt);

            $breakdown['base_rate'] = $base_rate;
            $breakdown['bedrooms'] = $bedrooms;
            $breakdown['bedroom_rate'] = $bedroom_rate;
            $breakdown['bedrooms_cost'] = $bedrooms_cost;
            $breakdown['bathrooms'] = $bathrooms;
            $breakdown['bathroom_rate'] = $bathroom_rate;
            $breakdown['bathrooms_cost'] = $bathrooms_cost;
            $breakdown['service_multiplier'] = $multiplier;
            $breakdown['raw_subtotal'] = $raw_subtotal;
            $breakdown['frequency'] = $frequency;
            $breakdown['discount_percent'] = $disc_pct;
            $breakdown['discount_amount'] = $disc_amt;
            $breakdown['subtotal_after_disc'] = $subtotal_after;
        }

        // Check minimum charge
        $final_total = $breakdown['subtotal_after_disc'];
        if ($final_total < $breakdown['minimum_charge']) {
            $breakdown['minimum_applied'] = true;
            $final_total = $breakdown['minimum_charge'];
        }

        // Tax handling
        if ($settings['tax_mode'] === 'inclusive') {
            $breakdown['tax_amount'] = round($final_total / 11, 2);
            $breakdown['tax_note'] = sprintf(__('Includes 10%% GST ($%s)', 'prime-cleaning-estimator'), number_format($breakdown['tax_amount'], 2));
        } elseif ($settings['tax_mode'] === 'exclusive') {
            $tax_add = round($final_total * 0.10, 2);
            $breakdown['tax_amount'] = $tax_add;
            $final_total = round($final_total + $tax_add, 2);
            $breakdown['tax_note'] = sprintf(__('Plus 10%% GST ($%s)', 'prime-cleaning-estimator'), number_format($tax_add, 2));
        } else {
            $breakdown['tax_amount'] = 0.0;
            $breakdown['tax_note'] = esc_html($settings['tax_disclaimer']);
        }

        $breakdown['final_total'] = round($final_total, 2);

        return $breakdown;
    }

    public static function get_service_name($service) {
        $names = array(
            'regular' => __('Regular Home Cleaning', 'prime-cleaning-estimator'),
            'deep'    => __('Deep Cleaning', 'prime-cleaning-estimator'),
            'lease'   => __('End-of-Lease Cleaning', 'prime-cleaning-estimator'),
            'office'  => __('Commercial & Office Cleaning', 'prime-cleaning-estimator'),
        );
        return $names[$service] ?? __('Custom Cleaning Service', 'prime-cleaning-estimator');
    }
}
