<?php
/**
 * Child Theme footer template - Modern Premium Design
 */

defined('ABSPATH') || exit;

do_action('kadence_after_content');
?>
	</main><!-- #inner-wrap -->
	<?php do_action('kadence_before_footer'); ?>
    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="footer-grid">
            <div class="footer-col" style="max-width: 320px;">
                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">

                    <strong style="color:#ffffff; font-size:1.15rem; letter-spacing:-0.02em;">PRIME DOMESTIC</strong>
                </div>
                <p style="font-size: 0.92rem; line-height: 1.65; color: #94a3b8; margin: 0 0 1rem 0;">
                    Structured residential, vacate, and commercial office cleaning services based in Malvern East, delivering reliable standards across Melbourne.
                </p>
                <div style="display:inline-block; font-size: 0.78rem; color: #5eead4; background: rgba(13,148,136,0.15); border: 1px solid rgba(94,234,212,0.25); padding: 0.25rem 0.65rem; border-radius: 9999px;">
                    Malvern East 3145 Hub
                </div>
            </div>

            <div class="footer-col">
                <h4>Cleaning Services</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/services/regular-home-cleaning/')); ?>">Regular Home Cleaning</a></li>
                    <li><a href="<?php echo esc_url(home_url('/services/deep-cleaning/')); ?>">Deep Cleaning Service</a></li>
                    <li><a href="<?php echo esc_url(home_url('/services/end-of-lease-cleaning/')); ?>">End-of-Lease Vacate Clean</a></li>
                    <li><a href="<?php echo esc_url(home_url('/services/office-cleaning/')); ?>">Commercial &amp; Office Cleaning</a></li>
                    <li><a href="<?php echo esc_url(home_url('/services/')); ?>">All Services Directory &rarr;</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Service Area &amp; Hours</h4>
                <ul>
                    <li><strong>Hub:</strong> Malvern East VIC 3145</li>
                    <li><strong>Coverage:</strong> Melbourne Metro &amp; Eastern Corridors</li>
                    <li><strong>Mon &ndash; Fri:</strong> 7:30am &ndash; 6:00pm</li>
                    <li><strong>Saturday:</strong> 8:00am &ndash; 4:00pm</li>
                    <li><strong>Sunday:</strong> Scheduled by appointment</li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Direct Contact</h4>
                <ul>
                    <li><strong>Phone:</strong> <a href="tel:1300000000">1300 000 000 (Demo)</a></li>
                    <li><strong>Email:</strong> <a href="mailto:info@example.com">info@example.com (Demo)</a></li>
                    <li>
                        <a href="<?php echo esc_url(home_url('/get-a-quote/')); ?>" class="pce-btn-primary" style="margin-top:0.75rem; font-size:0.88rem; padding:0.65rem 1.25rem;">
                            Instant Estimator &rarr;
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div>
                &copy; <?php echo esc_html(date_i18n('Y')); ?> Prime Domestic &amp; Cleaning Services. All rights reserved.
                &nbsp;|&nbsp;
                <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy Policy</a>
                &nbsp;|&nbsp;
                <span>Melbourne, Victoria</span>
            </div>
            <div>
                <a href="<?php echo esc_url(wp_login_url()); ?>" class="login-link" rel="nofollow">Admin Sign In</a>
            </div>
        </div>
    </footer>
    <?php do_action('kadence_after_footer'); ?>
</div><!-- #wrapper -->
<?php do_action('kadence_after_wrapper'); ?>

<?php wp_footer(); ?>
</body>
</html>
