<?php
/**
 * Backup current Customizer settings and import Prime-Single-Footer.dat
 */

if (!defined('ABSPATH')) {
    exit;
}

echo "=== Import Prime-Single-Footer.dat ===\n";

// 1. Initialize WP_Customize_Manager
require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
$wp_customize = new WP_Customize_Manager();
do_action('customize_register', $wp_customize);

// 2. Export Current Settings as Backup
$theme    = get_stylesheet();
$template = get_template();
$mods     = get_theme_mods();

$backup_data = array(
    'template' => $template,
    'mods'     => $mods ? $mods : array(),
    'options'  => array(),
);

$core_options = array(
    'blogname',
    'blogdescription',
    'show_on_front',
    'page_on_front',
    'page_for_posts',
);

$settings = $wp_customize->settings();
foreach ($settings as $key => $setting) {
    if ('option' == $setting->type) {
        if ('widget_' === substr(strtolower($key), 0, 7)) continue;
        if ('sidebars_' === substr(strtolower($key), 0, 9)) continue;
        if (in_array($key, $core_options)) continue;
        $backup_data['options'][$key] = $setting->value();
    }
}

$option_keys = apply_filters('cei_export_option_keys', array());
foreach ($option_keys as $option_key) {
    $backup_data['options'][$option_key] = get_option($option_key);
}

if (function_exists('wp_get_custom_css_post')) {
    $backup_data['wp_css'] = wp_get_custom_css();
}

$project_root = dirname(ABSPATH, 2);
$backup_file = $project_root . '/docs/native-migration/exports/' . $theme . '-backup-before-single-footer-' . date('Ymd-His') . '.dat';
file_put_contents($backup_file, serialize($backup_data));

echo "Pre-import backup saved to:\n";
echo " - " . realpath($backup_file) . "\n";

// 3. Load and inspect Prime-Single-Footer.dat
$import_file = $project_root . '/docs/native-migration/exports/Prime-Single-Footer.dat';
if (!file_exists($import_file)) {
    echo "ERROR: Import file not found at $import_file\n";
    exit(1);
}

$raw_import = file_get_contents($import_file);
$import_data = @unserialize(trim($raw_import), array('allowed_classes' => false));

if (!is_array($import_data) || !isset($import_data['template']) || !isset($import_data['mods'])) {
    echo "ERROR: Invalid import data structure.\n";
    exit(1);
}

if ($import_data['template'] !== $template) {
    echo "ERROR: Import template '{$import_data['template']}' does not match active template '$template'.\n";
    exit(1);
}

echo "\nImporting Prime-Single-Footer.dat:\n";
echo " - Source template: {$import_data['template']}\n";
echo " - Mods count: " . count($import_data['mods']) . "\n";
echo " - Mods keys:\n";
foreach (array_keys($import_data['mods']) as $mod_key) {
    echo "   * $mod_key\n";
}

// Load CEI option class if available
if (defined('CEI_PLUGIN_DIR')) {
    require_once CEI_PLUGIN_DIR . 'classes/class-cei-option.php';
} elseif (file_exists(WP_PLUGIN_DIR . '/customizer-export-import/classes/class-cei-option.php')) {
    require_once WP_PLUGIN_DIR . '/customizer-export-import/classes/class-cei-option.php';
}

// Import custom options if present
if (!empty($import_data['options']) && class_exists('CEI_Option')) {
    foreach ($import_data['options'] as $option_key => $option_value) {
        $option = new CEI_Option($wp_customize, $option_key, array(
            'default'    => '',
            'type'       => 'option',
            'capability' => 'edit_theme_options',
        ));
        $option->import($option_value);
    }
    echo " - Imported custom options.\n";
}

// Import custom CSS if present
if (function_exists('wp_update_custom_css_post') && !empty($import_data['wp_css'])) {
    wp_update_custom_css_post($import_data['wp_css']);
    echo " - Imported custom CSS.\n";
}

// Call customize_save action
do_action('customize_save', $wp_customize);

// Apply mods
$imported_count = 0;
foreach ($import_data['mods'] as $key => $val) {
    do_action('customize_save_' . $key, $wp_customize);
    set_theme_mod($key, $val);
    $imported_count++;
}

// Call customize_save_after action
do_action('customize_save_after', $wp_customize);

echo " - Successfully applied $imported_count theme mods!\n";
echo "=== Import Complete ===\n";
