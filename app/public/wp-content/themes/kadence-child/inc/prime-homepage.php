<?php
/** Reference-inspired homepage; presentation and estimator handoff only. */
defined('ABSPATH') || exit;

function prime_home_has_editable_content($content) {
    foreach (parse_blocks((string) $content) as $block) {
        $classes = preg_split('/\s+/', $block['attrs']['className'] ?? '');
        if ('core/group' === $block['blockName'] && in_array('prime-home', $classes, true)) {
            return true;
        }
    }
    return false;
}

function prime_home_pattern_content() {
    ob_start();
    include get_stylesheet_directory() . '/patterns/prime-homepage.php';
    return ob_get_clean();
}

/** Resolve existing pages so edited slugs, subdirectories and plain permalinks work. */
function prime_home_page_url($path) {
    $page = get_page_by_path(trim($path, '/'));
    return $page ? get_permalink($page) : home_url('/' . trim($path, '/') . '/');
}

/** Locate the installed shortcode rather than assume the quote page has not moved. */
function prime_home_estimator_url() {
    if (!shortcode_exists('prime_cleaning_estimator')) {
        return '';
    }
    $quote = get_page_by_path('get-a-quote');
    if ($quote && 'publish' === $quote->post_status && has_shortcode($quote->post_content, 'prime_cleaning_estimator')) {
        return get_permalink($quote);
    }
    foreach (get_pages(array('post_status' => 'publish')) as $page) {
        if (has_shortcode($page->post_content, 'prime_cleaning_estimator')) {
            return get_permalink($page);
        }
    }
    return '';
}

function prime_home_icon($name) {
    // Preserve legacy shortcode references with a simple cleaning tool icon.
    if ($name === 'sparkle') $name = 'brush';
    $paths = array(
        'home' => '<path d="m3 11 9-8 9 8M5 9v12h5v-7h4v7h5V9M16 6V3h3v6"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 10h18M7 14h2m3 0h2m3 0h1M7 17h2m3 0h2"/>',
        'calculator' => '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M8 6h8M8 11h1m6 0h1M8 15h1m6 0h1M8 19h1m6 0h1"/>',
        'shield' => '<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 3"/>',
        'price' => '<circle cx="12" cy="12" r="9"/><path d="M15 8h-4a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4H9m3-10v12"/>',
        'support' => '<path d="M4 13v-2a8 8 0 0 1 16 0v5a4 4 0 0 1-4 4h-2"/><rect x="2" y="11" width="4" height="7" rx="2"/><rect x="18" y="11" width="4" height="7" rx="2"/><path d="M12 20h2"/>',
        'brush' => '<path d="m14 13 7-9a1.5 1.5 0 0 0-2-2l-8 9M5 12l6-1 4 4-1 6c-4 1-9-1-12-4l3-5Zm1 3-2 4m5-3-1 5m4-4v4"/>',
        'key' => '<circle cx="8" cy="8" r="5"/><path d="m12 12 9 9m-3-3 3-3m-6 0 3-3M7 7h.01"/>',
        'office' => '<rect x="5" y="2" width="14" height="20" rx="1"/><path d="M9 6h1m4 0h1M9 10h1m4 0h1M9 14h1m4 0h1M10 22v-4h4v4"/>',
        'star' => '<path d="m12 2 3 7 7 .5-5.5 5 1.5 7-6-3.5L6 21l1-6.5-5-5L9 9l3-7Z"/>',
        'coins' => '<ellipse cx="10" cy="5" rx="7" ry="3"/><path d="M3 5v5c0 1.7 3.1 3 7 3m7-8v4M3 10v5c0 1.7 3.1 3 7 3m-7-3v4c0 1.7 3.1 3 7 3"/><ellipse cx="17" cy="12" rx="5" ry="2"/><path d="M12 12v4c0 1.1 2.2 2 5 2s5-.9 5-2v-4m-10 4v4c0 1.1 2.2 2 5 2s5-.9 5-2v-4"/>',
        'quote' => '<path d="M10 5C5 5 3 9 3 14v5h7v-7H6c0-2 1-4 4-4V5Zm11 0c-5 0-7 4-7 9v5h7v-7h-4c0-2 1-4 4-4V5Z"/>',
    );
    if (!isset($paths[$name])) {
        return '';
    }
    return '<svg class="prime-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[$name] . '</svg>';
}

add_action('init', function () {
    register_nav_menu('prime-home', __('Homepage Navigation', 'kadence-child'));
    register_block_pattern_category('prime', array('label' => __('Prime Cleaning', 'kadence-child')));
    register_block_pattern('prime/reference-homepage', array(
        'title'       => __('Prime — Cleaning Homepage', 'kadence-child'),
        'categories'  => array('prime'),
        'description'=> __('Editable homepage with the existing estimator handoff.', 'kadence-child'),
        'content'    => prime_home_pattern_content(),
    ));
    add_shortcode('prime_home_icon', function ($atts) {
        return prime_home_icon(sanitize_key($atts['name'] ?? 'home'));
    });
    add_shortcode('prime_home_estimator', 'prime_home_estimator_card');
});

add_filter('body_class', function ($classes) {
    if (is_front_page()) {
        $classes[] = 'prime-homepage';
    }
    return $classes;
});

add_action('wp', function () {
    if (is_front_page()) {
        remove_action('kadence_header', 'Kadence\\header_markup');
        add_action('kadence_header', function () {
            get_template_part('template-parts/prime-home-header');
        });
    }
});

add_action('wp_enqueue_scripts', function () {
    $directory = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();
    if (is_front_page()) {
        wp_enqueue_style('prime-homepage', $uri . '/prime-homepage.css', array('kadence-child-style'), (string) filemtime($directory . '/prime-homepage.css'));
        wp_enqueue_script('prime-homepage', $uri . '/assets/js/prime-homepage.js', array(), (string) filemtime($directory . '/assets/js/prime-homepage.js'), true);
    }
    // This small, optional adapter only sets existing form controls. No pricing or API changes.
    $post = get_queried_object();
    if (is_page() && $post instanceof WP_Post && has_shortcode($post->post_content, 'prime_cleaning_estimator')) {
        wp_enqueue_script('prime-home-estimator-handoff', $uri . '/assets/js/prime-estimator-handoff.js', array('pce-estimator-script'), (string) filemtime($directory . '/assets/js/prime-estimator-handoff.js'), true);
    }
}, 130);

add_action('after_setup_theme', function () {
    add_theme_support('editor-styles');
    add_editor_style('prime-homepage.css');
});

// Add responsive sources at render time, keeping Gutenberg's saved image valid.
add_filter('render_block_core/image', function ($html, $block) {
    if (false === strpos($block['attrs']['className'] ?? '', 'prime-hero-photo')) {
        return $html;
    }
    $tags = new WP_HTML_Tag_Processor($html);
    if ($tags->next_tag('IMG')) {
        $tags->set_attribute('fetchpriority', 'high');
        $tags->set_attribute('loading', 'eager');
        $base = get_stylesheet_directory_uri() . '/assets/images/';
        if ($tags->get_attribute('src') === $base . 'prime-cleaning-hero.webp') {
            $tags->set_attribute('srcset', $base . 'prime-cleaning-hero-640.webp 640w, ' . $base . 'prime-cleaning-hero-960.webp 960w, ' . $base . 'prime-cleaning-hero.webp 1920w');
            $tags->set_attribute('sizes', '(max-width: 780px) 1100px, 100vw');
            $tags->set_attribute('width', '1920');
            $tags->set_attribute('height', '640');
        }
    }
    return $tags->get_updated_html();
}, 10, 2);

/** Dynamic form: only existing service keys and bedroom inputs cross into the real estimator. */
function prime_home_estimator_card() {
    $action = prime_home_estimator_url();
    $demo = empty($action);
    $action_query = array();
    if (!$demo) {
        parse_str((string) wp_parse_url($action, PHP_URL_QUERY), $action_query);
    }
    ob_start();
    ?>
    <aside id="instant-estimator" class="prime-estimator" aria-labelledby="prime-estimator-title">
        <div class="prime-estimator-heading">
            <?php echo prime_home_icon('calculator'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <div><h2 id="prime-estimator-title">What does your place need?</h2><p>Choose your clean. We’ll take it from here.</p></div>
        </div>
        <form class="prime-quick-quote" action="<?php echo esc_url($demo ? '#instant-estimator' : $action . '#pce-estimator-container'); ?>" method="get" data-demo="<?php echo $demo ? 'true' : 'false'; ?>">
            <?php foreach ($action_query as $key => $value) : if (!is_scalar($value)) { continue; } ?>
                <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>">
            <?php endforeach; ?>
            <label for="prime-service">Select Service</label>
            <select id="prime-service" name="prime_service">
                <option value="regular">Regular Home Cleaning</option>
                <option value="deep">Deep Cleaning</option>
                <option value="lease">End-of-Lease Cleaning</option>
                <option value="carpet">Carpet Steam Cleaning</option>
            </select>
            <div class="prime-home-size">
                <label for="prime-bedrooms">Home Size</label>
                <select id="prime-bedrooms" name="prime_bedrooms">
                    <?php for ($count = 0; $count <= 6; $count++) : ?>
                        <option value="<?php echo esc_attr($count); ?>" <?php selected($count, 2); ?>><?php echo esc_html($count === 6 ? '6+ bedrooms' : ($count === 0 ? 'Studio' : ($count === 1 ? '1 bedroom' : $count . ' bedrooms'))); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <p class="prime-office-note" hidden>Tell us your office hours and requirements in the next step.</p>
            <button type="submit" class="prime-button prime-calculate" <?php disabled($demo); ?>>Get my estimate <span aria-hidden="true">→</span></button>
            <p class="prime-estimator-note" aria-live="polite"><?php echo $demo ? 'Demo only — online estimates are not available yet.' : 'Continue with your location and details to calculate.'; ?></p>
        </form>
    </aside>
    <?php
    return ob_get_clean();
}

function prime_home_default_menu() {
    $items = array(
        'Home' => home_url('/'),
        'Services' => prime_home_page_url('services'),
        'Price Estimator' => prime_home_estimator_url() ?: '#instant-estimator',
        'About' => prime_home_page_url('about'),
        'Reviews' => '#reviews',
        'Contact' => prime_home_page_url('contact'),
    );
    echo '<ul class="prime-nav-list">';
    foreach ($items as $label => $url) {
        echo '<li><a href="' . esc_url($url) . '"' . ('Home' === $label ? ' aria-current="page"' : '') . '>' . esc_html($label) . '</a></li>';
    }
    echo '</ul>';
}
