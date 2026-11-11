<?php defined('ABSPATH') || exit; ?>
<header class="prime-home-header">
    <div class="prime-shell prime-header-inner">
        <a class="prime-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Prime Domestic &amp; Cleaning Services — Home">
            <?php echo prime_home_icon('home'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <span><strong>Prime Domestic</strong><span>&amp; Cleaning Services</span></span>
        </a>
        <button class="prime-menu-toggle" type="button" aria-expanded="false" aria-controls="prime-home-navigation" hidden><span class="prime-menu-lines" aria-hidden="true"></span><span class="prime-menu-label">Menu</span></button>
        <nav class="prime-home-nav" id="prime-home-navigation" aria-label="Primary navigation">
            <?php wp_nav_menu(array('theme_location' => 'prime-home', 'container' => false, 'menu_class' => 'prime-nav-list', 'fallback_cb' => 'prime_home_default_menu', 'depth' => 1)); ?>
            <a class="prime-button prime-header-book" href="<?php echo esc_url(prime_home_page_url('contact')); ?>"><?php echo prime_home_icon('calendar'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Book a Service</a>
        </nav>
    </div>
</header>
