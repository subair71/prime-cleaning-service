<?php defined('ABSPATH') || exit; do_action('kadence_after_content'); ?>
</main>
<?php do_action('kadence_before_footer'); ?>
<footer class="prime-home-footer">
    <div class="prime-shell"><p>&copy; <?php echo esc_html(wp_date('Y')); ?> Prime Domestic &amp; Cleaning Services</p><p>Melbourne, Victoria <span aria-hidden="true">·</span> <a href="<?php echo esc_url(prime_home_page_url('privacy-policy')); ?>">Privacy Policy</a></p></div>
</footer>
<?php do_action('kadence_after_footer'); ?>
</div>
<?php do_action('kadence_after_wrapper'); wp_footer(); ?>
</body>
</html>
