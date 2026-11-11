<?php
/** Homepage presentation. Existing database content is never overwritten. */
defined('ABSPATH') || exit;

get_header();
?>
<div id="primary" class="prime-home-content">
    <div id="main">
        <?php
        if (have_posts()) {
            the_post();
        }
        if (prime_home_has_editable_content(get_the_content())) {
            the_content();
        } else {
            // The same core-block pattern can be inserted into Home in Gutenberg.
            echo do_shortcode(do_blocks(prime_home_pattern_content())); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme pattern, escaped at source.
        }
        ?>
    </div>
</div>
<?php get_footer('prime-home');
