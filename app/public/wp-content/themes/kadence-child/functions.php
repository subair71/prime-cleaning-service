<?php
/**
 * Kadence Child Theme functions and definitions
 */

defined('ABSPATH') || exit;

require_once get_stylesheet_directory() . '/inc/prime-homepage.php';

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'kadence-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme('kadence')->get('Version')
    );
    wp_enqueue_style(
        'kadence-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('kadence-parent-style'),
        '1.0.2'
    );
}, 20);

// Add custom body class
add_filter('body_class', function ($classes) {
    $classes[] = 'prime-cleaning-site';
    return $classes;
});

// Register after translations are available (WordPress 6.7+).
add_action('init', function () {
register_nav_menus(array(
    'primary' => __('Primary Navigation', 'kadence-child'),
    'mobile'  => __('Mobile Navigation', 'kadence-child'),
    'footer'  => __('Footer Navigation', 'kadence-child'),
));
});

/**
 * Angular-inspired visual overrides. Loaded after the child theme and plugin CSS.
 * No templates, page content, pricing logic, or estimator handlers are changed.
 */
add_action('wp_enqueue_scripts', function () {
    if (is_front_page()) {
        return;
    }
    $file = get_stylesheet_directory() . '/prime-angular-redesign.css';
    if (is_readable($file)) {
        wp_enqueue_style(
            'prime-angular-redesign',
            get_stylesheet_directory_uri() . '/prime-angular-redesign.css',
            array('kadence-child-style'),
            (string) filemtime($file)
        );
    }
}, 100);

/**
 * Styling hooks only: no post content, options, shortcodes or form handlers change.
 * Use slugs rather than database IDs so restored/staging installs work too.
 */
function prime_cleaning_page_classes($classes) {
    if (is_admin() || !is_page()) {
        return $classes;
    }
    $page = get_queried_object();
    if (!($page instanceof WP_Post)) {
        return $classes;
    }
    $profiles = array(
        'home' => 'home',
        'services' => 'services',
        'regular-home-cleaning' => 'service',
        'deep-cleaning' => 'service',
        'end-of-lease-cleaning' => 'service',
        'office-cleaning' => 'service',
        'about' => 'about',
        'contact' => 'contact',
        'get-a-quote' => 'quote',
        'privacy-policy' => 'privacy',
    );
    $profile = is_front_page() ? 'home' : ($profiles[$page->post_name] ?? 'standard');
    $classes[] = 'prime-page-enhanced';
    $classes[] = 'prime-page-' . $profile;
    // Ignore comments so an example/commented H1 cannot suppress a real title.
    $content = preg_replace('/<!--[\s\S]*?-->/', '', (string) $page->post_content);
    $has_title = (bool) preg_match('/<h1\b[^>]*>/i', (string) $content);
    $classes[] = $has_title ? 'prime-title-in-content' : 'prime-title-required';
    return array_values(array_unique($classes));
}
add_filter('body_class', 'prime_cleaning_page_classes', 20);

add_action('wp_enqueue_scripts', function () {
    if (!is_page() || is_admin() || is_front_page()) {
        return;
    }
    $file = get_stylesheet_directory() . '/prime-page-enhancements.css';
    if (!is_readable($file)) {
        return;
    }
    $dependencies = array('kadence-child-style');
    if (wp_style_is('prime-angular-redesign', 'enqueued')) {
        $dependencies[] = 'prime-angular-redesign';
    }
    wp_enqueue_style(
        'prime-page-enhancements',
        get_stylesheet_directory_uri() . '/prime-page-enhancements.css',
        $dependencies,
        (string) filemtime($file)
    );
}, 120);

require_once get_stylesheet_directory() . "/inc/prime-pages.php";
