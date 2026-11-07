<?php
/**
 * Admin interface for Prime Cleaning Estimator
 */

defined('ABSPATH') || exit;

class PCE_Admin {

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'register_menu'));
        add_action('admin_init', array(__CLASS__, 'save_settings_action'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
    }

    public static function register_menu() {
        $counts = PCE_DB::count_enquiries_by_status();
        $badge = $counts['new'] > 0 ? sprintf(' <span class="awaiting-mod update-plugins count-%1$d"><span class="pending-count">%1$d</span></span>', $counts['new']) : '';

        add_menu_page(
            __('Prime Estimator', 'prime-cleaning-estimator'),
            __('Prime Estimator', 'prime-cleaning-estimator') . $badge,
            'manage_options',
            'prime-estimator',
            array(__CLASS__, 'render_admin_page'),
            'dashicons-calculator',
            30
        );
    }

    public static function enqueue_admin_assets($hook) {
        if (strpos($hook, 'prime-estimator') === false) {
            return;
        }

        wp_enqueue_style(
            'pce-admin-style',
            plugins_url('assets/css/admin.css', dirname(__FILE__)),
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'pce-admin-script',
            plugins_url('assets/js/admin.js', dirname(__FILE__)),
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('pce-admin-script', 'pceAdminData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('pce_admin_nonce'),
        ));
    }

    public static function save_settings_action() {
        if (!isset($_POST['pce_save_settings_nonce'])) {
            return;
        }
        if (!current_user_can('manage_options')) {
            return;
        }
        if (!wp_verify_nonce($_POST['pce_save_settings_nonce'], 'pce_save_settings_action')) {
            return;
        }

        $input = $_POST['pce_settings'] ?? array();
        PCE_Settings::update($input);

        add_settings_error('pce_notices', 'pce_settings_saved', __('Estimator rates and settings have been saved.', 'prime-cleaning-estimator'), 'updated');
    }

    public static function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'enquiries';
        $view_enquiry_id = isset($_GET['enquiry_id']) ? absint($_GET['enquiry_id']) : 0;

        ?>
        <div class="wrap pce-admin-wrap">
            <h1 class="wp-heading-inline">Prime Domestic &amp; Cleaning Services &mdash; Estimator</h1>
            <p class="description" style="margin-bottom: 1.5rem;">
                Manage customer quote enquiries, configurable pricing rates, and Melbourne service area parameters.
            </p>

            <?php settings_errors('pce_notices'); ?>

            <nav class="nav-tab-wrapper">
                <a href="<?php echo esc_url(admin_url('admin.php?page=prime-estimator&tab=enquiries')); ?>" class="nav-tab <?php echo $current_tab === 'enquiries' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Quote Enquiries', 'prime-cleaning-estimator'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=prime-estimator&tab=settings')); ?>" class="nav-tab <?php echo $current_tab === 'settings' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Rates &amp; Settings', 'prime-cleaning-estimator'); ?>
                </a>
            </nav>

            <div class="pce-tab-content" style="margin-top: 1.5rem;">
                <?php
                if ($current_tab === 'settings') {
                    self::render_settings_tab();
                } else {
                    if ($view_enquiry_id > 0) {
                        self::render_enquiry_detail($view_enquiry_id);
                    } else {
                        self::render_enquiries_list();
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }

    private static function render_enquiries_list() {
        $filter_status = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : '';
        $counts = PCE_DB::count_enquiries_by_status();
        $enquiries = PCE_DB::get_enquiries(array('status' => $filter_status, 'limit' => 100));
        ?>
        <div class="pce-enquiries-header" style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <ul class="subsubsub" style="margin: 0;">
                <li><a href="<?php echo esc_url(admin_url('admin.php?page=prime-estimator&tab=enquiries')); ?>" class="<?php echo empty($filter_status) ? 'current' : ''; ?>">All (<?php echo $counts['all']; ?>)</a> |</li>
                <li><a href="<?php echo esc_url(admin_url('admin.php?page=prime-estimator&tab=enquiries&status_filter=new')); ?>" class="<?php echo $filter_status === 'new' ? 'current' : ''; ?>">New (<?php echo $counts['new']; ?>)</a> |</li>
                <li><a href="<?php echo esc_url(admin_url('admin.php?page=prime-estimator&tab=enquiries&status_filter=contacted')); ?>" class="<?php echo $filter_status === 'contacted' ? 'current' : ''; ?>">Contacted (<?php echo $counts['contacted']; ?>)</a> |</li>
                <li><a href="<?php echo esc_url(admin_url('admin.php?page=prime-estimator&tab=enquiries&status_filter=quoted')); ?>" class="<?php echo $filter_status === 'quoted' ? 'current' : ''; ?>">Quoted (<?php echo $counts['quoted']; ?>)</a> |</li>
                <li><a href="<?php echo esc_url(admin_url('admin.php?page=prime-estimator&tab=enquiries&status_filter=closed')); ?>" class="<?php echo $filter_status === 'closed' ? 'current' : ''; ?>">Closed (<?php echo $counts['closed']; ?>)</a></li>
            </ul>
        </div>

        <?php if (empty($enquiries)) : ?>
            <div class="notice notice-info inline" style="padding: 1.5rem;">
                <p><strong><?php _e('No quote enquiries found.', 'prime-cleaning-estimator'); ?></strong></p>
                <p><?php _e('When visitors submit requests via the estimator form [prime_cleaning_estimator], they will appear here with full pricing breakdowns.', 'prime-cleaning-estimator'); ?></p>
            </div>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 140px;">Date Received</th>
                        <th>Customer</th>
                        <th>Location</th>
                        <th>Service</th>
                        <th>Estimate</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 100px;">Email Notice</th>
                        <th style="width: 90px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enquiries as $row) : 
                        $status = esc_attr($row['status']);
                        $is_in_area = !empty($row['is_in_area']);
                    ?>
                    <tr>
                        <td><strong>#<?php echo esc_html($row['id']); ?></strong></td>
                        <td><?php echo esc_html(date_i18n('d M Y, g:i a', strtotime($row['created_at']))); ?></td>
                        <td>
                            <strong><?php echo esc_html($row['customer_name']); ?></strong><br>
                            <small><a href="mailto:<?php echo esc_attr($row['customer_email']); ?>"><?php echo esc_html($row['customer_email']); ?></a></small><br>
                            <small><a href="tel:<?php echo esc_attr($row['customer_phone']); ?>"><?php echo esc_html($row['customer_phone']); ?></a></small>
                        </td>
                        <td>
                            <?php echo esc_html($row['suburb']); ?> (<?php echo esc_html($row['postcode']); ?>)<br>
                            <?php if ($is_in_area) : ?>
                                <span class="pce-pill pce-pill-green">In Area</span>
                            <?php else : ?>
                                <span class="pce-pill pce-pill-orange">Outside Area (Manual)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo esc_html(PCE_Calculator::get_service_name($row['service_type'])); ?></strong><br>
                            <?php 
                            $prop = json_decode($row['property_details'], true);
                            if (!empty($prop['office_hours'])) {
                                echo esc_html($prop['office_hours'] . ' hours');
                            } elseif (!empty($prop['bedrooms'])) {
                                echo esc_html($prop['bedrooms'] . ' bed / ' . $prop['bathrooms'] . ' bath');
                            }
                            if (!empty($prop['frequency_name'])) {
                                echo '<br><small style="color:#64748b;">' . esc_html($prop['frequency_name']) . '</small>';
                            }
                            ?>
                        </td>
                        <td>
                            <strong style="font-size: 1.1em; color: #0d9488;">$<?php echo number_format($row['total_amount'], 2); ?> AUD</strong><br>
                            <small style="color: #64748b;">(Estimate)</small>
                        </td>
                        <td>
                            <select class="pce-status-dropdown" data-id="<?php echo esc_attr($row['id']); ?>">
                                <option value="new" <?php selected($status, 'new'); ?>>New</option>
                                <option value="contacted" <?php selected($status, 'contacted'); ?>>Contacted</option>
                                <option value="quoted" <?php selected($status, 'quoted'); ?>>Quoted</option>
                                <option value="closed" <?php selected($status, 'closed'); ?>>Closed</option>
                            </select>
                            <span class="spinner" id="spinner-<?php echo esc_attr($row['id']); ?>" style="float:none; margin:0 4px;"></span>
                        </td>
                        <td>
                            <?php if (!empty($row['email_sent'])) : ?>
                                <span style="color: #16a34a; font-weight:600;">&#10003; Sent</span>
                            <?php else : ?>
                                <span style="color: #dc2626; font-size:0.85em;" title="<?php echo esc_attr($row['email_error'] ?? 'Delivery failed'); ?>">&#9888; Not Sent</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=prime-estimator&tab=enquiries&enquiry_id=' . $row['id'])); ?>" class="button button-small">View Details</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif;
    }

    private static function render_enquiry_detail($id) {
        $row = PCE_DB::get_enquiry($id);
        if (!$row) {
            echo '<div class="notice notice-error"><p>' . __('Enquiry not found.', 'prime-cleaning-estimator') . '</p></div>';
            return;
        }

        $prop = json_decode($row['property_details'], true) ?: array();
        $breakdown = json_decode($row['estimate_breakdown'], true) ?: array();
        ?>
        <p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=prime-estimator&tab=enquiries')); ?>" class="button">&larr; Back to Enquiries List</a>
        </p>

        <div class="pce-detail-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
            <div class="postbox" style="padding: 1.5rem;">
                <h2 style="margin-top:0; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">Customer Details &mdash; Enquiry #<?php echo esc_html($row['id']); ?></h2>
                <table class="form-table" style="margin-top:0;">
                    <tr>
                        <th style="width: 140px;">Customer Name:</th>
                        <td><strong><?php echo esc_html($row['customer_name']); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Email Address:</th>
                        <td><a href="mailto:<?php echo esc_attr($row['customer_email']); ?>"><?php echo esc_html($row['customer_email']); ?></a></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><a href="tel:<?php echo esc_attr($row['customer_phone']); ?>"><?php echo esc_html($row['customer_phone']); ?></a></td>
                    </tr>
                    <tr>
                        <th>Location:</th>
                        <td>
                            <?php echo esc_html($row['suburb']); ?>, VIC <?php echo esc_html($row['postcode']); ?>
                            <br>
                            <?php if (!empty($row['is_in_area'])) : ?>
                                <span class="pce-pill pce-pill-green">Within standard Melbourne service area</span>
                            <?php else : ?>
                                <span class="pce-pill pce-pill-orange">Outside automated service area &mdash; manual quote required</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Preferred Date:</th>
                        <td>
                            <?php echo !empty($row['preferred_date']) ? esc_html(date_i18n('l, d F Y', strtotime($row['preferred_date']))) : '<em>Not specified / Flexible</em>'; ?>
                            <br><small style="color: #64748b;">(Customer requested date &mdash; subject to schedule availability)</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <select class="pce-status-dropdown" data-id="<?php echo esc_attr($row['id']); ?>">
                                <option value="new" <?php selected($row['status'], 'new'); ?>>New</option>
                                <option value="contacted" <?php selected($row['status'], 'contacted'); ?>>Contacted</option>
                                <option value="quoted" <?php selected($row['status'], 'quoted'); ?>>Quoted</option>
                                <option value="closed" <?php selected($row['status'], 'closed'); ?>>Closed</option>
                            </select>
                            <span class="spinner" id="spinner-<?php echo esc_attr($row['id']); ?>" style="float:none;"></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Customer Notes:</th>
                        <td><?php echo !empty($row['notes']) ? nl2br(esc_html($row['notes'])) : '<em>None provided</em>'; ?></td>
                    </tr>
                    <tr>
                        <th>Admin Email Alert:</th>
                        <td>
                            <?php if (!empty($row['email_sent'])) : ?>
                                <span style="color: #16a34a; font-weight:600;">&#10003; Delivered via wp_mail</span>
                            <?php else : ?>
                                <span style="color: #dc2626; font-weight:600;">&#9888; Email notice not sent</span>
                                <?php if (!empty($row['email_error'])) : ?>
                                    <br><small style="color:#dc2626;"><?php echo esc_html($row['email_error']); ?></small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="postbox" style="padding: 1.5rem;">
                <h2 style="margin-top:0; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">Frozen Price Breakdown (Captured at submission)</h2>
                
                <table class="widefat" style="margin-top: 1rem;">
                    <tbody>
                        <tr>
                            <td><strong>Service Type</strong></td>
                            <td style="text-align: right;"><strong><?php echo esc_html($breakdown['service_name'] ?? PCE_Calculator::get_service_name($row['service_type'])); ?></strong></td>
                        </tr>
                        <?php if (!empty($breakdown['bedrooms'])) : ?>
                        <tr>
                            <td>Base Property Rate</td>
                            <td style="text-align: right;">$<?php echo number_format($breakdown['base_rate'] ?? 80, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Bedrooms (<?php echo esc_html($breakdown['bedrooms']); ?> &times; $<?php echo number_format($breakdown['bedroom_rate'] ?? 25, 2); ?>)</td>
                            <td style="text-align: right;">$<?php echo number_format($breakdown['bedrooms_cost'] ?? 0, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Bathrooms (<?php echo esc_html($breakdown['bathrooms']); ?> &times; $<?php echo number_format($breakdown['bathroom_rate'] ?? 35, 2); ?>)</td>
                            <td style="text-align: right;">$<?php echo number_format($breakdown['bathrooms_cost'] ?? 0, 2); ?></td>
                        </tr>
                        <?php endif; ?>

                        <?php if (!empty($breakdown['office_hours'])) : ?>
                        <tr>
                            <td>Office Cleaning Hours (<?php echo esc_html($breakdown['office_hours']); ?> hrs &times; $<?php echo number_format($breakdown['office_hourly_rate'] ?? 55, 2); ?>/hr)</td>
                            <td style="text-align: right;">$<?php echo number_format($breakdown['raw_subtotal'] ?? 0, 2); ?></td>
                        </tr>
                        <?php endif; ?>

                        <?php if (!empty($breakdown['service_multiplier']) && floatval($breakdown['service_multiplier']) > 1) : ?>
                        <tr>
                            <td>Service Multiplier (<?php echo esc_html($breakdown['service']); ?>)</td>
                            <td style="text-align: right;">&times; <?php echo number_format($breakdown['service_multiplier'], 2); ?></td>
                        </tr>
                        <?php endif; ?>

                        <tr>
                            <td><strong>Raw Subtotal</strong></td>
                            <td style="text-align: right;"><strong>$<?php echo number_format($breakdown['raw_subtotal'] ?? $row['total_amount'], 2); ?></strong></td>
                        </tr>

                        <?php if (!empty($breakdown['discount_percent']) && floatval($breakdown['discount_percent']) > 0) : ?>
                        <tr style="color: #0f766e;">
                            <td>Frequency Discount: <?php echo esc_html($breakdown['frequency_name']); ?> (-<?php echo esc_html($breakdown['discount_percent']); ?>%)</td>
                            <td style="text-align: right;">-$<?php echo number_format($breakdown['discount_amount'] ?? 0, 2); ?></td>
                        </tr>
                        <?php endif; ?>

                        <?php if (!empty($breakdown['minimum_applied'])) : ?>
                        <tr style="color: #b45309;">
                            <td>Minimum Service Charge Adjustment Applied</td>
                            <td style="text-align: right;">Floor: $<?php echo number_format($breakdown['minimum_charge'] ?? 100, 2); ?></td>
                        </tr>
                        <?php endif; ?>

                        <tr style="background: #f8fafc; font-size: 1.15em;">
                            <td><strong>Total Estimate (AUD)</strong></td>
                            <td style="text-align: right; color: #0d9488;"><strong>$<?php echo number_format($row['total_amount'], 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top: 1.25rem; padding: 0.75rem 1rem; background: #f1f5f9; border-radius: 6px; font-size: 0.88rem; color: #475569;">
                    <strong>Tax Note:</strong> <?php echo esc_html($breakdown['tax_note'] ?? 'Demo pricing in AUD. Tax treatment subject to confirmation.'); ?>
                </div>

                <p style="margin-top: 1rem; font-size: 0.85rem; color: #64748b;">
                    <em>Notice: Historical enquiry estimates remain frozen exactly as calculated at submission time, even if admin prices are updated subsequently.</em>
                </p>
            </div>
        </div>
        <?php
    }

    private static function render_settings_tab() {
        $settings = PCE_Settings::get_all();
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('pce_save_settings_action', 'pce_save_settings_nonce'); ?>

            <div class="postbox" style="padding: 1.5rem; margin-bottom: 2rem;">
                <h2 style="margin-top: 0;">1. Residential Cleaning Base Rates (AUD)</h2>
                <p class="description">Provisional demo pricing. Adjust to your standard base and room charges.</p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="base_regular">Standard Base Rate ($ AUD)</label></th>
                        <td>
                            <input name="pce_settings[base_regular]" id="base_regular" type="number" step="0.50" min="0" value="<?php echo esc_attr($settings['base_regular']); ?>" class="regular-text">
                            <p class="description">Starting baseline charge per residential booking.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="bedroom_rate">Per Bedroom Rate ($ AUD)</label></th>
                        <td>
                            <input name="pce_settings[bedroom_rate]" id="bedroom_rate" type="number" step="0.50" min="0" value="<?php echo esc_attr($settings['bedroom_rate']); ?>" class="regular-text">
                            <p class="description">Incremental addition per bedroom.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="bathroom_rate">Per Bathroom Rate ($ AUD)</label></th>
                        <td>
                            <input name="pce_settings[bathroom_rate]" id="bathroom_rate" type="number" step="0.50" min="0" value="<?php echo esc_attr($settings['bathroom_rate']); ?>" class="regular-text">
                            <p class="description">Incremental addition per bathroom.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="multiplier_deep">Deep Clean Multiplier</label></th>
                        <td>
                            <input name="pce_settings[multiplier_deep]" id="multiplier_deep" type="number" step="0.05" min="1.0" max="5.0" value="<?php echo esc_attr($settings['multiplier_deep']); ?>" class="regular-text">
                            <p class="description">Factor applied to residential total for comprehensive deep cleans (e.g. 1.5 = 50% more intensive).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="multiplier_lease">End-of-Lease Multiplier</label></th>
                        <td>
                            <input name="pce_settings[multiplier_lease]" id="multiplier_lease" type="number" step="0.05" min="1.0" max="5.0" value="<?php echo esc_attr($settings['multiplier_lease']); ?>" class="regular-text">
                            <p class="description">Factor applied for bond / end-of-lease cleans (e.g. 2.0 = double standard time).</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="postbox" style="padding: 1.5rem; margin-bottom: 2rem;">
                <h2 style="margin-top: 0;">2. Commercial &amp; Office Cleaning Rates</h2>
                <p class="description">Office and commercial enquiries calculate using separate hourly inputs instead of residential bedrooms.</p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="office_hourly_rate">Office Hourly Rate ($ AUD / hr)</label></th>
                        <td>
                            <input name="pce_settings[office_hourly_rate]" id="office_hourly_rate" type="number" step="1.00" min="0" value="<?php echo esc_attr($settings['office_hourly_rate']); ?>" class="regular-text">
                            <p class="description">Standard commercial cleaning hourly rate.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="office_min_hours">Minimum Office Visit (Hours)</label></th>
                        <td>
                            <input name="pce_settings[office_min_hours]" id="office_min_hours" type="number" step="0.5" min="1" max="24" value="<?php echo esc_attr($settings['office_min_hours']); ?>" class="regular-text">
                            <p class="description">Minimum bookable duration for commercial sites (e.g. 2.0 hours).</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="postbox" style="padding: 1.5rem; margin-bottom: 2rem;">
                <h2 style="margin-top: 0;">3. Frequency Discounts &amp; Minimum Charges</h2>
                <p class="description">Discounts apply automatically when customers select recurring schedules.</p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="discount_weekly">Weekly Discount (%)</label></th>
                        <td>
                            <input name="pce_settings[discount_weekly]" id="discount_weekly" type="number" step="1" min="0" max="50" value="<?php echo esc_attr($settings['discount_weekly']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="discount_fortnightly">Fortnightly Discount (%)</label></th>
                        <td>
                            <input name="pce_settings[discount_fortnightly]" id="discount_fortnightly" type="number" step="1" min="0" max="50" value="<?php echo esc_attr($settings['discount_fortnightly']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="minimum_charge">Minimum Charge Floor ($ AUD)</label></th>
                        <td>
                            <input name="pce_settings[minimum_charge]" id="minimum_charge" type="number" step="5" min="0" value="<?php echo esc_attr($settings['minimum_charge']); ?>" class="regular-text">
                            <p class="description">No estimate will fall below this minimum callout threshold.</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="postbox" style="padding: 1.5rem; margin-bottom: 2rem;">
                <h2 style="margin-top: 0;">4. Service Area &amp; Postcode Configuration (Melbourne)</h2>
                <div class="notice notice-warning inline" style="margin-bottom: 1rem;">
                    <p><strong>Note on Postcode Matching:</strong> Postcode prefix matching is a demo pattern match (e.g. 314* matches 3140&ndash;3149) and does not represent an exact geographic road or radius calculation. For unlisted areas, the estimator offers a manual quote enquiry option.</p>
                </div>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="base_suburb">Business Base Suburb</label></th>
                        <td>
                            <input name="pce_settings[base_suburb]" id="base_suburb" type="text" value="<?php echo esc_attr($settings['base_suburb']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="base_postcode">Base Postcode</label></th>
                        <td>
                            <input name="pce_settings[base_postcode]" id="base_postcode" type="text" value="<?php echo esc_attr($settings['base_postcode']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="postcode_prefixes">Covered Postcode Prefixes</label></th>
                        <td>
                            <input name="pce_settings[postcode_prefixes]" id="postcode_prefixes" type="text" value="<?php echo esc_attr($settings['postcode_prefixes']); ?>" class="regular-text">
                            <p class="description">Comma-separated postcode prefixes (e.g. <code>314, 315</code> covers 3141&ndash;3149, 3150&ndash;3159).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Out of Area Handling</th>
                        <td>
                            <label>
                                <input name="pce_settings[allow_manual_quote]" type="checkbox" value="1" <?php checked($settings['allow_manual_quote'], 1); ?>>
                                Allow visitors outside covered prefixes to submit a manual quote enquiry rather than rejecting them.
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="postbox" style="padding: 1.5rem; margin-bottom: 2rem;">
                <h2 style="margin-top: 0;">5. Tax Treatment &amp; Disclaimers</h2>
                <p class="description">Configure how GST is displayed to avoid unverified tax claims.</p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="tax_mode">GST Configuration</label></th>
                        <td>
                            <select name="pce_settings[tax_mode]" id="tax_mode">
                                <option value="demo_unregistered" <?php selected($settings['tax_mode'], 'demo_unregistered'); ?>>Demo / Not GST Registered (No GST charged)</option>
                                <option value="inclusive" <?php selected($settings['tax_mode'], 'inclusive'); ?>>Prices Include 10% GST</option>
                                <option value="exclusive" <?php selected($settings['tax_mode'], 'exclusive'); ?>>Prices Exclude GST (Add 10% at checkout)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="tax_disclaimer">Tax &amp; Estimate Disclaimer</label></th>
                        <td>
                            <input name="pce_settings[tax_disclaimer]" id="tax_disclaimer" type="text" value="<?php echo esc_attr($settings['tax_disclaimer']); ?>" class="large-text">
                            <p class="description">Text shown alongside estimates to clarify demo or tax status.</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="postbox" style="padding: 1.5rem; margin-bottom: 2rem;">
                <h2 style="margin-top: 0;">6. Business Contact Details &amp; Notifications</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="business_name">Business Name</label></th>
                        <td>
                            <input name="pce_settings[business_name]" id="business_name" type="text" value="<?php echo esc_attr($settings['business_name']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="business_phone">Contact Phone (Demo Placeholder)</label></th>
                        <td>
                            <input name="pce_settings[business_phone]" id="business_phone" type="text" value="<?php echo esc_attr($settings['business_phone']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="business_email">Public Contact Email</label></th>
                        <td>
                            <input name="pce_settings[business_email]" id="business_email" type="email" value="<?php echo esc_attr($settings['business_email']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="notification_recipient">Admin Quote Alert Recipient</label></th>
                        <td>
                            <input name="pce_settings[notification_recipient]" id="notification_recipient" type="email" value="<?php echo esc_attr($settings['notification_recipient']); ?>" class="regular-text">
                            <p class="description">Email address that receives instant notifications for new quote enquiries.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Email Alerts</th>
                        <td>
                            <label>
                                <input name="pce_settings[enable_email_notice]" type="checkbox" value="1" <?php checked($settings['enable_email_notice'], 1); ?>>
                                Send automatic email notification via wp_mail when new enquiries arrive.
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <?php submit_button(__('Save All Settings &amp; Rates', 'prime-cleaning-estimator')); ?>
        </form>
        <?php
    }
}
