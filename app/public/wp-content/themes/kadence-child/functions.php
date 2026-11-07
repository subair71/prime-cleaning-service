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
