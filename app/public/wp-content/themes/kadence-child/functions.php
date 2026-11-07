<?php
/**
 * Kadence Child Theme functions and definitions
 */

defined('ABSPATH') || exit;

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

// Register navigation menus
register_nav_menus(array(
    'primary' => __('Primary Navigation', 'kadence-child'),
    'mobile'  => __('Mobile Navigation', 'kadence-child'),
    'footer'  => __('Footer Navigation', 'kadence-child'),
));

/**
 * Angular-inspired visual overrides. Loaded after the child theme and plugin CSS.
 * No templates, page content, pricing logic, or estimator handlers are changed.
 */
add_action('wp_enqueue_scripts', function () {
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
