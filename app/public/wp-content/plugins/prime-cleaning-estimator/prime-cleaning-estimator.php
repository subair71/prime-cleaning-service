<?php
/**
 * Plugin Name: Prime Cleaning Estimator
 * Plugin URI: https://prime-cleaning-service.local
 * Description: Multi-step cleaning price estimator for Melbourne domestic and commercial services with live pricing and admin quote management.
 * Version: 1.1.0
 * Author: Prime Domestic & Cleaning Services
 * Text Domain: prime-cleaning-estimator
 */

defined('ABSPATH') || exit;

define('PCE_VERSION', '1.1.0');
define('PCE_PLUGIN_FILE', __FILE__);
define('PCE_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Require module classes
require_once PCE_PLUGIN_DIR . 'includes/class-pce-db.php';
require_once PCE_PLUGIN_DIR . 'includes/class-pce-settings.php';
require_once PCE_PLUGIN_DIR . 'includes/class-pce-calculator.php';
require_once PCE_PLUGIN_DIR . 'includes/class-pce-ajax.php';
require_once PCE_PLUGIN_DIR . 'includes/class-pce-admin.php';
require_once PCE_PLUGIN_DIR . 'includes/class-pce-shortcode.php';

// Plugin Activation Hook: Database table creation
register_activation_hook(__FILE__, function () {
    PCE_DB::create_tables();
});

// Initialization
add_action('plugins_loaded', function () {
    // Ensure DB table exists
    if (is_admin()) {
        PCE_DB::create_tables();
    }

    PCE_AJAX::init();
    PCE_Admin::init();
    PCE_Shortcode::init();
});
