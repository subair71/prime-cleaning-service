<?php
/**
 * Shortcode handler for [prime_cleaning_estimator]
 */

defined('ABSPATH') || exit;

class PCE_Shortcode {

    public static function init() {
        add_shortcode('prime_cleaning_estimator', array(__CLASS__, 'render_shortcode'));
    }

    public static function enqueue_assets() {
        wp_enqueue_style(
            'pce-estimator-style',
            plugins_url('assets/css/estimator.css', dirname(__FILE__)),
            array(),
            '1.1.0'
        );

        wp_enqueue_script(
            'pce-estimator-script',
            plugins_url('assets/js/estimator.js', dirname(__FILE__)),
            array(),
            '1.1.0',
            true
        );

        $settings = PCE_Settings::get_all();

        wp_localize_script('pce-estimator-script', 'pceData', array(
            'ajax_url'        => admin_url('admin-ajax.php'),
            'nonce'           => wp_create_nonce('pce_estimator_nonce'),
            'settings'        => array(
                'base_regular'        => floatval($settings['base_regular']),
                'bedroom_rate'        => floatval($settings['bedroom_rate']),
                'bathroom_rate'       => floatval($settings['bathroom_rate']),
                'multiplier_deep'     => floatval($settings['multiplier_deep']),
                'multiplier_lease'    => floatval($settings['multiplier_lease']),
                'office_hourly_rate'  => floatval($settings['office_hourly_rate']),
                'office_min_hours'    => floatval($settings['office_min_hours']),
                'discount_weekly'     => floatval($settings['discount_weekly']),
                'discount_fortnightly'=> floatval($settings['discount_fortnightly']),
                'minimum_charge'      => floatval($settings['minimum_charge']),
                'postcode_prefixes'   => $settings['postcode_prefixes'],
                'allow_manual_quote'  => (bool)$settings['allow_manual_quote'],
                'tax_mode'            => $settings['tax_mode'],
                'tax_disclaimer'      => $settings['tax_disclaimer'],
                'base_suburb'         => $settings['base_suburb'],
                'base_postcode'       => $settings['base_postcode'],
            ),
            'today_date'      => wp_date('Y-m-d'),
            'render_ts'       => time(),
        ));
    }

    public static function render_shortcode($atts = array()) {
        self::enqueue_assets();

        $settings = PCE_Settings::get_all();
        $min_date = wp_date('Y-m-d');

        ob_start();
        ?>
        <div class="pce-wrapper" id="pce-estimator-container">
            <!-- Header Card -->
            <div class="pce-header-banner">
                <span class="pce-kicker">PRIME DOMESTIC &amp; CLEANING SERVICES</span>
                <h2 class="pce-title">Instant Cleaning Price Estimator</h2>
                <p class="pce-subtitle">
                    Based in Malvern East (3145) &mdash; serving Melbourne metro &amp; eastern suburbs.
                    Get an instant estimate in seconds.
                </p>
                <div class="pce-demo-badge">
                    <span class="dashicons dashicons-info"></span> Sample demo rates &mdash; all estimates are subject to final confirmation.
                </div>
            </div>

            <!-- Multi-step Progress Bar -->
            <div class="pce-steps-nav" role="tablist" aria-label="Estimator Steps">
                <div class="pce-step-item pce-step-active" data-step="1">
                    <span class="pce-step-num">1</span>
                    <span class="pce-step-text">Location</span>
                </div>
                <div class="pce-step-divider"></div>
                <div class="pce-step-item" data-step="2">
                    <span class="pce-step-num">2</span>
                    <span class="pce-step-text">Service</span>
                </div>
                <div class="pce-step-divider"></div>
                <div class="pce-step-item" data-step="3">
                    <span class="pce-step-num">3</span>
                    <span class="pce-step-text">Property</span>
                </div>
                <div class="pce-step-divider"></div>
                <div class="pce-step-item" data-step="4">
                    <span class="pce-step-num">4</span>
                    <span class="pce-step-text">Estimate</span>
                </div>
                <div class="pce-step-divider"></div>
                <div class="pce-step-item" data-step="5">
                    <span class="pce-step-num">5</span>
                    <span class="pce-step-text">Enquiry</span>
                </div>
            </div>

            <!-- Form Container -->
            <form id="pce-estimator-form" class="pce-form-card" novalidate>
                <!-- Anti-spam Honeypot (hidden from human visitors) -->
                <div style="display:none !important;" aria-hidden="true">
                    <label for="pce_hp_url">Do not fill this field</label>
                    <input type="text" id="pce_hp_url" name="pce_hp_url" tabindex="-1" autocomplete="off">
                </div>

                <!-- STEP 1: LOCATION -->
                <div class="pce-step-panel pce-panel-active" data-panel="1">
                    <h3 class="pce-step-heading">Step 1 &mdash; Where is the cleaning required?</h3>
                    <p class="pce-step-desc">
                        Our primary hub is in <strong>Malvern East VIC 3145</strong>. Enter your suburb and 4-digit postcode to check coverage.
                    </p>

                    <div class="pce-field-row">
                        <div class="pce-field-col">
                            <label for="pce_suburb" class="pce-label">Suburb <span class="pce-req">*</span></label>
                            <input type="text" id="pce_suburb" name="suburb" placeholder="e.g. Malvern East, Glen Iris, Hawthorn" required>
                        </div>
                        <div class="pce-field-col">
                            <label for="pce_postcode" class="pce-label">Postcode (VIC) <span class="pce-req">*</span></label>
                            <input type="text" id="pce_postcode" name="postcode" placeholder="e.g. 3145" maxlength="4" pattern="[0-9]{4}" required inputmode="numeric">
                        </div>
                    </div>

                    <!-- Coverage Feedback Box -->
                    <div id="pce-coverage-box" class="pce-coverage-alert" style="display:none;"></div>

                    <div class="pce-panel-actions">
                        <button type="button" class="pce-btn pce-btn-next" data-goto="2">Continue to Services &rarr;</button>
                    </div>
                </div>

                <!-- STEP 2: SERVICE SELECTION -->
                <div class="pce-step-panel" data-panel="2" style="display:none;">
                    <h3 class="pce-step-heading">Step 2 &mdash; Choose your cleaning service</h3>
                    <p class="pce-step-desc">Select the type of cleaning you require.</p>

                    <div class="pce-service-cards">
                        <label class="pce-radio-card pce-selected">
                            <input type="radio" name="service" value="regular" checked>
                            <div class="pce-card-content">
                                <div class="pce-card-title">Regular Home Cleaning</div>
                                <div class="pce-card-desc">Scheduled weekly, fortnightly, or one-off maintenance cleaning for bedrooms, living spaces, kitchens, and bathrooms.</div>
                                <span class="pce-pill pce-pill-teal">Popular Choice</span>
                            </div>
                        </label>

                        <label class="pce-radio-card">
                            <input type="radio" name="service" value="deep">
                            <div class="pce-card-content">
                                <div class="pce-card-title">Deep Cleaning</div>
                                <div class="pce-card-desc">Intensive top-to-bottom clean focusing on buildup, skirtings, detail surfaces, and neglected corners.</div>
                                <span class="pce-pill pce-pill-blue">Spring / Detail Clean</span>
                            </div>
                        </label>

                        <label class="pce-radio-card">
                            <input type="radio" name="service" value="lease">
                            <div class="pce-card-content">
                                <div class="pce-card-title">End-of-Lease Cleaning</div>
                                <div class="pce-card-desc">Comprehensive vacate cleaning tailored to agent checklists for tenancy bond handovers.</div>
                                <span class="pce-pill pce-pill-orange">Bond Handover</span>
                            </div>
                        </label>

                        <label class="pce-radio-card">
                            <input type="radio" name="service" value="office">
                            <div class="pce-card-content">
                                <div class="pce-card-title">Commercial &amp; Office Cleaning</div>
                                <div class="pce-card-desc">Tailored workspace, workstation, meeting room, and staff kitchen cleaning calculated by estimated hours.</div>
                                <span class="pce-pill pce-pill-purple">Workplace &amp; Commercial</span>
                            </div>
                        </label>
                    </div>

                    <div class="pce-panel-actions">
                        <button type="button" class="pce-btn pce-btn-back" data-goto="1">&larr; Back</button>
                        <button type="button" class="pce-btn pce-btn-next" data-goto="3">Continue to Details &rarr;</button>
                    </div>
                </div>

                <!-- STEP 3: PROPERTY & FREQUENCY -->
                <div class="pce-step-panel" data-panel="3" style="display:none;">
                    <h3 class="pce-step-heading">Step 3 &mdash; Property Scope &amp; Frequency</h3>
                    <p class="pce-step-desc" id="pce-step3-desc">Provide details about your property size to tailor the estimate.</p>

                    <!-- Residential Scope (Visible for regular, deep, lease) -->
                    <div id="pce-residential-fields">
                        <div class="pce-field-row">
                            <div class="pce-field-col">
                                <label for="pce_bedrooms" class="pce-label">Number of Bedrooms</label>
                                <select id="pce_bedrooms" name="bedrooms">
                                    <option value="1">1 Bedroom / Studio</option>
                                    <option value="2" selected>2 Bedrooms</option>
                                    <option value="3">3 Bedrooms</option>
                                    <option value="4">4 Bedrooms</option>
                                    <option value="5">5 Bedrooms</option>
                                    <option value="6">6+ Bedrooms</option>
                                </select>
                            </div>
                            <div class="pce-field-col">
                                <label for="pce_bathrooms" class="pce-label">Number of Bathrooms</label>
                                <select id="pce_bathrooms" name="bathrooms">
                                    <option value="1" selected>1 Bathroom</option>
                                    <option value="2">2 Bathrooms</option>
                                    <option value="3">3 Bathrooms</option>
                                    <option value="4">4 Bathrooms</option>
                                    <option value="5">5+ Bathrooms</option>
                                </select>
                            </div>
                        </div>

                        <div class="pce-field-row" id="pce-res-frequency-row">
                            <div class="pce-field-col-full">
                                <label class="pce-label">Service Frequency</label>
                                <div class="pce-frequency-options">
                                    <label class="pce-freq-pill pce-freq-selected">
                                        <input type="radio" name="frequency" value="once" checked>
                                        <span>One-Off Clean</span>
                                    </label>
                                    <label class="pce-freq-pill" id="pce-pill-fortnightly">
                                        <input type="radio" name="frequency" value="fortnightly">
                                        <span>Fortnightly <small class="pce-disc-badge">Save <?php echo esc_html($settings['discount_fortnightly']); ?>%</small></span>
                                    </label>
                                    <label class="pce-freq-pill" id="pce-pill-weekly">
                                        <input type="radio" name="frequency" value="weekly">
                                        <span>Weekly <small class="pce-disc-badge">Save <?php echo esc_html($settings['discount_weekly']); ?>%</small></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commercial / Office Scope (Visible only when Office selected) -->
                    <div id="pce-office-fields" style="display:none;">
                        <div class="pce-field-row">
                            <div class="pce-field-col">
                                <label for="pce_office_hours" class="pce-label">Estimated Service Duration (Hours)</label>
                                <input type="number" id="pce_office_hours" name="office_hours" min="<?php echo esc_attr($settings['office_min_hours']); ?>" max="40" step="0.5" value="<?php echo esc_attr($settings['office_min_hours']); ?>">
                                <p class="pce-field-hint">Minimum <?php echo esc_html($settings['office_min_hours']); ?> hours per visit. Standard rate: $<?php echo esc_html($settings['office_hourly_rate']); ?>/hr AUD.</p>
                            </div>
                            <div class="pce-field-col">
                                <label class="pce-label">Commercial Schedule</label>
                                <select name="office_frequency" id="pce_office_frequency">
                                    <option value="weekly">Weekly Regular Service (Save <?php echo esc_html($settings['discount_weekly']); ?>%)</option>
                                    <option value="fortnightly">Fortnightly Service (Save <?php echo esc_html($settings['discount_fortnightly']); ?>%)</option>
                                    <option value="once" selected>One-off / Casual Clean</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pce-panel-actions">
                        <button type="button" class="pce-btn pce-btn-back" data-goto="2">&larr; Back</button>
                        <button type="button" class="pce-btn pce-btn-next" data-goto="4">Calculate Estimate &rarr;</button>
                    </div>
                </div>

                <!-- STEP 4: INSTANT ESTIMATE BREAKDOWN -->
                <div class="pce-step-panel" data-panel="4" style="display:none;">
                    <h3 class="pce-step-heading">Step 4 &mdash; Your Indicative Price Estimate</h3>
                    
                    <div class="pce-estimate-display">
                        <div class="pce-total-banner">
                            <div class="pce-total-label">Estimated Price &mdash; Subject to Confirmation</div>
                            <div class="pce-total-price" id="pce-display-total">$0.00 AUD</div>
                            <div class="pce-tax-clarification" id="pce-display-tax"><?php echo esc_html($settings['tax_disclaimer']); ?></div>
                        </div>

                        <!-- Price Breakdown Table -->
                        <div class="pce-breakdown-card">
                            <h4 class="pce-breakdown-title">Transparent Price Breakdown</h4>
                            <table class="pce-breakdown-table">
                                <tbody id="pce-breakdown-rows">
                                    <!-- Rendered dynamically by JavaScript -->
                                </tbody>
                            </table>

                            <div id="pce-min-charge-notice" class="pce-min-badge" style="display:none;">
                                Minimum callout threshold applied ($<?php echo esc_html($settings['minimum_charge']); ?> AUD minimum).
                            </div>
                        </div>

                        <div class="pce-coverage-summary" id="pce-area-recap">
                            <!-- Coverage note -->
                        </div>
                    </div>

                    <div class="pce-panel-actions">
                        <button type="button" class="pce-btn pce-btn-back" data-goto="3">&larr; Modify Details</button>
                        <button type="button" class="pce-btn pce-btn-next" data-goto="5">Request This Quote &rarr;</button>
                    </div>
                </div>

                <!-- STEP 5: ENQUIRY CONTACT DETAILS -->
                <div class="pce-step-panel" data-panel="5" style="display:none;">
                    <h3 class="pce-step-heading">Step 5 &mdash; Confirm &amp; Send Your Quote Enquiry</h3>
                    <p class="pce-step-desc">
                        Review your details below and submit your request. We will review your requirements and confirm availability.
                    </p>

                    <div class="pce-recap-box">
                        <div class="pce-recap-row">
                            <span>Service Requested:</span>
                            <strong id="pce-recap-service">Regular Home Cleaning</strong>
                        </div>
                        <div class="pce-recap-row">
                            <span>Location:</span>
                            <strong id="pce-recap-loc">&mdash;</strong>
                        </div>
                        <div class="pce-recap-row">
                            <span>Indicative Estimate:</span>
                            <strong id="pce-recap-amount" style="color: #0d9488;">$0.00 AUD</strong>
                        </div>
                    </div>

                    <div class="pce-field-row">
                        <div class="pce-field-col">
                            <label for="pce_name" class="pce-label">Full Name <span class="pce-req">*</span></label>
                            <input type="text" id="pce_name" name="name" required placeholder="e.g. Jane Doe">
                        </div>
                        <div class="pce-field-col">
                            <label for="pce_email" class="pce-label">Email Address <span class="pce-req">*</span></label>
                            <input type="email" id="pce_email" name="email" required placeholder="e.g. jane@example.com">
                        </div>
                    </div>

                    <div class="pce-field-row">
                        <div class="pce-field-col">
                            <label for="pce_phone" class="pce-label">Phone Number <span class="pce-req">*</span></label>
                            <input type="tel" id="pce_phone" name="phone" required placeholder="e.g. 0400 000 000">
                        </div>
                        <div class="pce-field-col">
                            <label for="pce_date" class="pce-label">Preferred Date (Optional)</label>
                            <input type="date" id="pce_date" name="preferred_date" min="<?php echo esc_attr($min_date); ?>">
                            <p class="pce-field-hint">Note: Preferred date is a schedule request, not a confirmed booking.</p>
                        </div>
                    </div>

                    <div class="pce-field-row">
                        <div class="pce-field-col-full">
                            <label for="pce_notes" class="pce-label">Specific Requirements / Notes (Optional)</label>
                            <textarea id="pce_notes" name="notes" rows="3" placeholder="e.g. key lockbox location, pets on site, specific areas requiring extra attention..."></textarea>
                        </div>
                    </div>

                    <div class="pce-consent-box">
                        <label class="pce-checkbox-label">
                            <input type="checkbox" id="pce_consent" name="consent" value="1" required>
                            <span>I consent to Prime Domestic &amp; Cleaning Services contacting me via email or phone regarding this cleaning quote request.</span>
                        </label>
                    </div>

                    <div id="pce-submit-error" class="pce-alert-error" style="display:none;"></div>

                    <div class="pce-panel-actions">
                        <button type="button" class="pce-btn pce-btn-back" data-goto="4">&larr; Back to Estimate</button>
                        <button type="submit" id="pce-submit-btn" class="pce-btn pce-btn-submit">
                            <span class="pce-btn-text">Submit Quote Enquiry</span>
                            <span class="pce-spinner" style="display:none;"></span>
                        </button>
                    </div>
                </div>

                <!-- CONFIRMATION / SUCCESS PANEL -->
                <div class="pce-step-panel" id="pce-success-panel" style="display:none;">
                    <div class="pce-success-card">
                        <div class="pce-success-icon">&#10003;</div>
                        <h3 class="pce-success-title">Quote Enquiry Received!</h3>
                        <p class="pce-success-lead">
                            Thank you, <span id="pce-success-name"></span>! Your cleaning quote enquiry (<span id="pce-success-ref"></span>) has been successfully recorded.
                        </p>
                        
                        <div class="pce-success-details">
                            <p><strong>Service:</strong> <span id="pce-success-service"></span></p>
                            <p><strong>Estimated Amount:</strong> AUD $<span id="pce-success-amount"></span></p>
                            <p id="pce-success-manual-note" style="display:none; color:#c2410c;">
                                <em>Notice: Because your address is outside our automated coverage area, our team will review travel logistics and provide an exact custom quotation.</em>
                            </p>
                            <p><strong>Next Steps:</strong> We will review our schedule and contact you shortly to confirm timing and finalize your quotation.</p>
                        </div>

                        <div style="margin-top: 2rem;">
                            <button type="button" class="pce-btn pce-btn-secondary" id="pce-btn-restart">Calculate Another Estimate</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
