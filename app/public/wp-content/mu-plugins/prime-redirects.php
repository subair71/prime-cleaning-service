<?php
/**
 * Plugin Name: Prime Legacy Redirects
 * Description: Redirects /prices/ and /prices/#estimate to /get-a-quote/
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('template_redirect', function () {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if ($uri === 'prices') {
        wp_safe_redirect(home_url('/get-a-quote/'), 301);
        exit;
    }
});
