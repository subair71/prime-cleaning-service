<?php
/**
 * Backup current Customizer settings and import Prime-Header-Footer.dat
 */

if (!defined('ABSPATH')) {
    exit;
}

echo "=== Customizer Export/Import Automation ===\n";

// 1. Initialize WP_Customize_Manager
require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
$wp_customize = new WP_Customize_Manager();
do_action('customize_register', $wp_customize);

// 2. Export Current Settings as Backup
$theme    = get_stylesheet();
$template = get_template();
$charset  = get_option('blog_charset');
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
$backup_file = $project_root . '/docs/native-migration/exports/' . $theme . '-backup-' . date('Ymd-His') . '.dat';
$default_export_file = $project_root . '/docs/native-migration/exports/' . $theme . '-export.dat';

$serialized_backup = serialize($backup_data);
file_put_contents($backup_file, $serialized_backup);
file_put_contents($default_export_file, $serialized_backup);

echo "Backup exported successfully:\n";
echo " - File: " . realpath($backup_file) . "\n";
echo " - Default export: " . realpath($default_export_file) . "\n";
echo " - Total mods in backup: " . count($backup_data['mods']) . "\n";
echo " - Total options in backup: " . count($backup_data['options']) . "\n";

// 3. Import Prime-Header-Footer.dat
$user_upload = 'C:/Users/Admin/.gemini/antigravity/brain/b608a12a-9d05-4ab0-9205-e0e91414654e/.user_uploaded/media_1795085428860_20781145.dat';
$repo_file = $project_root . '/docs/native-migration/exports/Prime-Header-Footer.dat';

$import_file = file_exists($user_upload) ? $user_upload : $repo_file;
if (!file_exists($import_file)) {
    echo "ERROR: Import file not found.\n";
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

echo "\nImporting Prime-Header-Footer.dat...\n";
echo " - Source template: {$import_data['template']}\n";
echo " - Mods to import: " . count($import_data['mods']) . "\n";

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
