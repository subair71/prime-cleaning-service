<?php
/**
 * Fully applies native pages, header, footer widgets, theme mods,
 * Kadence global palette, and DS Mobile Menu actions.
 */
if (PHP_SAPI !== 'cli' || !defined('ABSPATH')) {
    exit('Run via WP-CLI only.');
}

$root = dirname(__DIR__) . '/docs/native-migration';
$pages_file = $root . '/exports/page-manifest.json';
$media_file = $root . '/exports/media-manifest.json';

if (!file_exists($pages_file) || !file_exists($media_file)) {
    exit('Export files missing.');
}

$read = fn($file) => json_decode(file_get_contents($root . '/exports/' . $file), true, 512, JSON_THROW_ON_ERROR);

$pages = $read('page-manifest.json');
$media = $read('media-manifest.json');
$source = 'http://127.0.0.1:4173';
$target = untrailingslashit(home_url());

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$replace = [$source => $target];
$image_ids = [];
$site_logo_id = 0;

// 1. Sideload / Identify Media
foreach ($media as $key => $m) {
    $existing = get_posts([
        'post_type' => 'attachment',
        'meta_key' => '_wp_attached_file',
        'meta_value' => '2026/11/' . $m['file'],
        'numberposts' => 1,
    ]);
    if (!$existing) {
        $existing = get_posts([
            'post_type' => 'attachment',
            'title' => pathinfo($m['file'], PATHINFO_FILENAME),
            'numberposts' => 1,
        ]);
    }

    if ($existing) {
        $id = $existing[0]->ID;
    } else {
        $file_path = $root . '/exports/media/' . $m['file'];
        if (file_exists($file_path)) {
            $tmp = wp_tempnam($m['file']);
            copy($file_path, $tmp);
            $id = media_handle_sideload(
                ['name' => $m['file'], 'tmp_name' => $tmp],
                0,
                $m['alt'] ?? ('Prime ' . $key)
            );
            if (is_wp_error($id)) {
                echo 'Media error: ' . $id->get_error_message() . "\n";
                $id = 0;
            }
        } else {
            $id = 0;
        }
    }

    if ($id) {
        if (isset($m['url'])) {
            $replace[$m['url']] = wp_get_attachment_url($id);
        }
        if (isset($m['id'])) {
            $image_ids[$m['id']] = $id;
        }
        if (($m['role'] ?? '') === 'site-logo') {
            $site_logo_id = $id;
        }
        update_post_meta($id, '_wp_attachment_image_alt', $m['alt'] ?? '');
    }
}

// 2. Switch to Kadence theme
switch_theme('kadence');

// 3. Apply Kadence Theme Mods (Header & Footer settings)
$theme_mods = $read('theme-mods-kadence.json');
foreach ($theme_mods as $key => $value) {
    if (in_array($key, ['custom_css_post_id', 'nav_menu_locations', 'sidebars_widgets'], true)) {
        continue;
    }
    $val = json_decode(strtr(wp_json_encode($value, JSON_UNESCAPED_SLASHES), $replace), true);
    set_theme_mod($key, $val);
}

if ($site_logo_id) {
    set_theme_mod('custom_logo', $site_logo_id);
}

// 4. Global Palette
$palette_file = $root . '/exports/kadence-global-palette.json';
if (file_exists($palette_file)) {
    update_option('kadence_global_palette', file_get_contents($palette_file));
}

// 5. Rewrite function for blocks
$rewrite = function ($content) use ($replace, $image_ids) {
    $content = strtr($content, $replace);
    $content = preg_replace_callback('/"id":(\d+)/', function ($matches) use ($image_ids) {
        $old_id = (int) $matches[1];
        return '"id":' . ($image_ids[$old_id] ?? $old_id);
    }, $content);
    $content = preg_replace_callback('/wp-image-(\d+)/', function ($matches) use ($image_ids) {
        $old_id = (int) $matches[1];
        return 'wp-image-' . ($image_ids[$old_id] ?? $old_id);
    }, $content);
    return $content;
};

// 6. Update Pages
$approved_ids = get_option('prime_approved_page_ids', []);
$ids = [];
$updated_count = 0;

foreach ($pages as $p) {
    $page_file = $root . '/' . $p['file'];
    if (!file_exists($page_file)) {
        continue;
    }

    $raw_content = file_get_contents($page_file);
    $final_content = $rewrite($raw_content);

    $page_obj = null;
    if (!empty($approved_ids[$p['key']])) {
        $page_obj = get_post($approved_ids[$p['key']]);
    }
    if (!$page_obj) {
        $page_obj = get_page_by_path($p['slug']);
    }
    if (!$page_obj && $p['key'] === 'home' && get_option('page_on_front')) {
        $page_obj = get_post(get_option('page_on_front'));
    }

    if ($page_obj) {
        wp_save_post_revision($page_obj->ID);
        wp_update_post([
            'ID' => $page_obj->ID,
            'post_content' => $final_content,
        ]);
        $ids[$p['key']] = $page_obj->ID;
        $approved_ids[$p['key']] = $page_obj->ID;
        echo "Updated [{$p['key']}] (ID: {$page_obj->ID}) -> {$p['title']}\n";
        $updated_count++;
    } else {
        $new_id = wp_insert_post([
            'post_type' => 'page',
            'post_title' => $p['title'],
            'post_name' => $p['slug'],
            'post_status' => $p['status'],
            'post_content' => $final_content,
        ]);
        if (!is_wp_error($new_id)) {
            $ids[$p['key']] = $new_id;
            $approved_ids[$p['key']] = $new_id;
            echo "Created [{$p['key']}] (ID: {$new_id}) -> {$p['title']}\n";
            $updated_count++;
        }
    }
}
update_option('prime_approved_page_ids', $approved_ids);

// 7. Configure Footer Widgets
$widgets = $read('widget-block.json');
foreach ($widgets as &$widget) {
    if (is_array($widget) && isset($widget['content'])) {
        $widget['content'] = $rewrite($widget['content']);
    }
}
unset($widget);
update_option('widget_block', $widgets);
wp_set_sidebars_widgets($read('sidebars-widgets.json'));
echo "Configured 4 footer widget areas.\n";

// 8. Configure DS Mobile Menu (devstack-mobile-menu)
if (class_exists('DSMobileMenu\Ajax\MenuItemsAjax')) {
    $bar = json_decode(strtr(wp_json_encode($read('mobile-actions.json'), JSON_UNESCAPED_SLASHES), $replace), true);
    $bar_sanitize = new DSMobileMenu\Ajax\MenuItemsAjax();
    update_option('dsmm_active_layout', $bar['dsmm_active_layout']);
    update_option('dsmm_menu_items', $bar_sanitize->sanitize_items($bar['dsmm_menu_items']));
    update_option('dsmm_visibility_rules', $bar_sanitize->sanitize_visibility_rules($bar['dsmm_visibility_rules']));
    update_option('dsmm_general_settings', $bar_sanitize->sanitize_general_settings($bar['dsmm_general_settings']));
    $style_sanitize = new DSMobileMenu\Ajax\StyleAjax(new DSMobileMenu\Core\StyleGenerator());
    update_option('dsmm_style_settings', $style_sanitize->sanitize($bar['dsmm_style_settings']));
    update_option('dsmm_wc_settings', $bar['dsmm_wc_settings']);
    (new DSMobileMenu\Core\StyleGenerator())->generate();
    echo "Configured DS Mobile Menu bottom action bar.\n";
}

// 9. Configure Navigation Menus
$menu_name = 'Prime main navigation';
$menu = wp_get_nav_menu_object($menu_name);
if (!$menu) {
    $menu_id = wp_create_nav_menu($menu_name);
    if (!is_wp_error($menu_id)) {
        $menu = wp_get_nav_menu_object($menu_id);
    }
}
if ($menu && !is_wp_error($menu)) {
    // Clear existing menu items in this menu to rebuild cleanly
    $current_items = wp_get_nav_menu_items($menu->term_id);
    if ($current_items) {
        foreach ($current_items as $item) {
            wp_delete_post($item->ID, true);
        }
    }
    $add = function ($key, $label, $parent = 0) use ($menu, $ids) {
        if (empty($ids[$key])) return 0;
        return wp_update_nav_menu_item($menu->term_id, 0, [
            'menu-item-title'     => $label,
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $ids[$key],
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => $parent,
        ]);
    };
    $add('home', 'Home');
    $parent = wp_update_nav_menu_item($menu->term_id, 0, [
        'menu-item-title'  => 'Services',
        'menu-item-type'   => 'custom',
        'menu-item-url'    => '#',
        'menu-item-status' => 'publish',
    ]);
    foreach (['regular' => 'Regular & Deep Cleaning', 'lease' => 'End of Lease Cleaning', 'carpet' => 'Carpet Steam Cleaning'] as $key => $label) {
        $add($key, $label, $parent);
    }
    foreach (['prices' => 'Prices', 'about' => 'About', 'contact' => 'Contact'] as $key => $label) {
        $add($key, $label);
    }
    set_theme_mod('nav_menu_locations', ['primary' => $menu->term_id, 'mobile' => $menu->term_id]);
    echo "Configured Prime main navigation menu with dropdowns.\n";
}

wp_cache_flush();
flush_rewrite_rules(false);

echo "Successfully completed header, footer, mobile actions, and page enhancements!\n";
