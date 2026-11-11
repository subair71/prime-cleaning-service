<?php
/**
 * Title: Prime — Cleaning Homepage
 * Slug: prime/reference-homepage
 * Categories: prime
 * Inserter: false
 *
 * Registered explicitly in inc/prime-homepage.php for older WordPress versions too.
 */
defined('ABSPATH') || exit;
?>
<!-- wp:group {"className":"prime-home"} -->
<div class="wp-block-group prime-home"><!-- wp:group {"className":"prime-hero","tagName":"section"} -->
<section class="wp-block-group prime-hero"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"prime-hero-photo"} -->
<figure class="wp-block-image size-full prime-hero-photo"><img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/prime-cleaning-hero.webp'); ?>" alt="A professional cleaner in a bright living room with a sofa and teal cushions"/><figcaption class="wp-element-caption">Illustrative image</figcaption></figure>
<!-- /wp:image -->
<!-- wp:group {"className":"prime-shell prime-hero-grid"} -->
<div class="wp-block-group prime-shell prime-hero-grid"><!-- wp:group {"className":"prime-hero-copy"} -->
<div class="wp-block-group prime-hero-copy"><!-- wp:paragraph {"className":"prime-eyebrow"} -->
<p class="prime-eyebrow">MELBOURNE HOME &amp; OFFICE CLEANING</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Professional Home &amp; Office Cleaning Services</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"prime-hero-description"} -->
<p class="prime-hero-description">Domestic, deep, end-of-lease and office cleaning for a fresher, more comfortable space. Based in Malvern East, serving Melbourne.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"className":"prime-actions"} -->
<div class="wp-block-buttons prime-actions"><!-- wp:button {"className":"prime-book-button"} -->
<div class="wp-block-button prime-book-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(prime_home_page_url('contact')); ?>">Book a Service</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"prime-quote-button"} -->
<div class="wp-block-button prime-quote-button"><a class="wp-block-button__link wp-element-button" href="#instant-estimator">Get an Instant Quote</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-hero-estimator"} -->
<div class="wp-block-group prime-hero-estimator"><!-- wp:shortcode -->
[prime_home_estimator]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-trust","tagName":"section"} -->
<section class="wp-block-group prime-trust"><!-- wp:group {"className":"prime-shell prime-trust-grid"} -->
<div class="wp-block-group prime-shell prime-trust-grid"><!-- wp:group {"className":"prime-trust-item"} -->
<div class="wp-block-group prime-trust-item"><!-- wp:shortcode -->
[prime_home_icon name="shield"]
<!-- /wp:shortcode -->
<!-- wp:paragraph -->
<p>Trained Staff</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-trust-item"} -->
<div class="wp-block-group prime-trust-item"><!-- wp:shortcode -->
[prime_home_icon name="clock"]
<!-- /wp:shortcode -->
<!-- wp:paragraph -->
<p>Reliable Service</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-trust-item"} -->
<div class="wp-block-group prime-trust-item"><!-- wp:shortcode -->
[prime_home_icon name="price"]
<!-- /wp:shortcode -->
<!-- wp:paragraph -->
<p>Transparent Pricing</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-trust-item"} -->
<div class="wp-block-group prime-trust-item"><!-- wp:shortcode -->
[prime_home_icon name="support"]
<!-- /wp:shortcode -->
<!-- wp:paragraph -->
<p>Customer Support</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-shell prime-services","tagName":"section","anchor":"services"} -->
<section id="services" class="wp-block-group prime-shell prime-services"><!-- wp:group {"className":"prime-section-heading"} -->
<div class="wp-block-group prime-section-heading"><!-- wp:heading -->
<h2 class="wp-block-heading">Our Services</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Cleaning services to keep your home and workplace fresh, comfortable and cared for.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-services-grid"} -->
<div class="wp-block-group prime-services-grid"><!-- wp:group {"className":"prime-service-card","tagName":"article"} -->
<article class="wp-block-group prime-service-card"><!-- wp:group {"className":"prime-service-icon"} -->
<div class="wp-block-group prime-service-icon"><!-- wp:shortcode -->
[prime_home_icon name="brush"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-service-copy"} -->
<div class="wp-block-group prime-service-copy"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Regular Home Cleaning</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Routine cleaning for your home, with a schedule that suits you.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"prime-learn-more"} -->
<p class="prime-learn-more"><a href="<?php echo esc_url(prime_home_page_url('services/regular-home-cleaning')); ?>">Learn More <span aria-hidden="true">→</span></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</article>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-service-card","tagName":"article"} -->
<article class="wp-block-group prime-service-card"><!-- wp:group {"className":"prime-service-icon"} -->
<div class="wp-block-group prime-service-icon"><!-- wp:shortcode -->
[prime_home_icon name="sparkle"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-service-copy"} -->
<div class="wp-block-group prime-service-copy"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Deep Cleaning</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>A detailed clean for the spaces that need extra attention.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"prime-learn-more"} -->
<p class="prime-learn-more"><a href="<?php echo esc_url(prime_home_page_url('services/deep-cleaning')); ?>">Learn More <span aria-hidden="true">→</span></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</article>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-service-card","tagName":"article"} -->
<article class="wp-block-group prime-service-card"><!-- wp:group {"className":"prime-service-icon"} -->
<div class="wp-block-group prime-service-icon"><!-- wp:shortcode -->
[prime_home_icon name="key"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-service-copy"} -->
<div class="wp-block-group prime-service-copy"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">End-of-Lease Cleaning</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Vacate cleaning for your next move and property handover.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"prime-learn-more"} -->
<p class="prime-learn-more"><a href="<?php echo esc_url(prime_home_page_url('services/end-of-lease-cleaning')); ?>">Learn More <span aria-hidden="true">→</span></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</article>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-service-card","tagName":"article"} -->
<article class="wp-block-group prime-service-card"><!-- wp:group {"className":"prime-service-icon"} -->
<div class="wp-block-group prime-service-icon"><!-- wp:shortcode -->
[prime_home_icon name="office"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-service-copy"} -->
<div class="wp-block-group prime-service-copy"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Office Cleaning</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Care for your workspace, meeting rooms and shared areas.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"prime-learn-more"} -->
<p class="prime-learn-more"><a href="<?php echo esc_url(prime_home_page_url('services/office-cleaning')); ?>">Learn More <span aria-hidden="true">→</span></a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</article>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-shell prime-proof","tagName":"section"} -->
<section class="wp-block-group prime-shell prime-proof"><!-- wp:group {"className":"prime-benefits"} -->
<div class="wp-block-group prime-benefits"><!-- wp:heading -->
<h2 class="wp-block-heading">Why Choose Us?</h2>
<!-- /wp:heading -->
<!-- wp:group {"className":"prime-benefit"} -->
<div class="wp-block-group prime-benefit"><!-- wp:group {"className":"prime-benefit-icon"} -->
<div class="wp-block-group prime-benefit-icon"><!-- wp:shortcode -->
[prime_home_icon name="star"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-benefit-copy"} -->
<div class="wp-block-group prime-benefit-copy"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Care for Every Detail</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Cleaning for your home, from everyday spaces to the finer details.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-benefit"} -->
<div class="wp-block-group prime-benefit"><!-- wp:group {"className":"prime-benefit-icon"} -->
<div class="wp-block-group prime-benefit-icon"><!-- wp:shortcode -->
[prime_home_icon name="coins"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-benefit-copy"} -->
<div class="wp-block-group prime-benefit-copy"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Clear &amp; Transparent</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Review your estimate before sending a quote enquiry.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-benefit"} -->
<div class="wp-block-group prime-benefit"><!-- wp:group {"className":"prime-benefit-icon"} -->
<div class="wp-block-group prime-benefit-icon"><!-- wp:shortcode -->
[prime_home_icon name="calendar"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-benefit-copy"} -->
<div class="wp-block-group prime-benefit-copy"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Flexible Enquiries</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Tell us your preferred date. We’ll confirm availability with you.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-reviews","anchor":"reviews"} -->
<div id="reviews" class="wp-block-group prime-reviews"><!-- wp:heading -->
<h2 class="wp-block-heading">What Our Customers Say</h2>
<!-- /wp:heading -->
<!-- wp:group {"className":"prime-review-card"} -->
<div class="wp-block-group prime-review-card"><!-- wp:shortcode -->
[prime_home_icon name="quote"]
<!-- /wp:shortcode -->
<!-- wp:group {"className":"prime-review-copy"} -->
<div class="wp-block-group prime-review-copy"><!-- wp:paragraph {"className":"prime-placeholder-label"} -->
<p class="prime-placeholder-label">REVIEW PLACEHOLDER</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"prime-review-quote"} -->
<p class="prime-review-quote">“Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.”</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"prime-review-note"} -->
<p class="prime-review-note">Customer feedback will appear here once approved.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</section>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-cta-wrap"} -->
<div class="wp-block-group prime-cta-wrap"><!-- wp:group {"className":"prime-cta","tagName":"section"} -->
<section class="wp-block-group prime-cta"><!-- wp:group {"className":"prime-cta-symbol"} -->
<div class="wp-block-group prime-cta-symbol"><!-- wp:shortcode -->
[prime_home_icon name="home"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"prime-cta-copy"} -->
<div class="wp-block-group prime-cta-copy"><!-- wp:heading -->
<h2 class="wp-block-heading">Ready to Get Started?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Book a service or start your personalised cleaning quote today.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:buttons {"className":"prime-actions"} -->
<div class="wp-block-buttons prime-actions"><!-- wp:button {"className":"prime-book-button"} -->
<div class="wp-block-button prime-book-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(prime_home_page_url('contact')); ?>">Book a Service</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"prime-quote-button"} -->
<div class="wp-block-button prime-quote-button"><a class="wp-block-button__link wp-element-button" href="#instant-estimator">Get an Instant Quote</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</section>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
