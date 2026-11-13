# Verification - 13 November 2026

## Passed: 108 automated checks

| Suite | Checks | Coverage |
|---|---:|---|
| `tests/approved-pricing.php` | 39 | All four services, bedroom/bathroom grids, deep multiplier, double-storey surcharge, recurring discount order, undiscounted extras/travel, minimum, AUD5 rounding, missing rates, furnished EOL, 6+ bedrooms, 5+ bathrooms, unlisted suburb, individual carpet rates, included windows, additional walls |
| `tests/approved-wordpress-integration.php` | 20 | Real WordPress/AJAX, DB records, sequential PDC references, frozen snapshots after rate edits, idempotency, error handling, existing options/schema and URL reuse |
| `tests/nov12-wordpress.php` | 49 | Normal/custom saved range confirmations; exact acknowledgements; owner recipients; no custom zero/stale range; reference-prefilled WhatsApp; all 11 page patterns one H1/no launch Lorem; native block parsing; drafts; staging robots/sitemap; dynamic prices; repeat installer preservation |

All changed PHP files pass `php -l`; estimator JS passes `node --check`; `git diff --check` passes. Tests use an isolated SQLite-backed WordPress runtime, not the client DB. Every email attempt is intercepted with `pre_wp_mail`; no external messages were delivered. Numeric fixture prices were restored to missing/unapproved after testing.

## Browser observations

- Actual WordPress frontend rendered at desktop 1280px and mobile 390px. Captures used a same-origin responsive iframe, not physical devices.
- The page sweep observed one H1 and no document-level horizontal overflow at those widths.
- Mobile Menu opened; fixed Call / WhatsApp / Estimate bar was visible in an 844px-high view. Desktop header, accordions and service layouts were reviewed visually.
- Empty Blog links and Office/Terms links were absent from launch navigation. Captured pages use one canonical and staging noindex. Review images load from local WebP assets.
- PHP pattern parsing is not a full Gutenberg editor-validation test.

## Remaining verification

The preview session disconnected, then browser policy rejected reopening the page. No alternative browser route was used. Outstanding: end-to-end browser submit/validation/back-button/CTA transfer tests; normal and custom confirmation-state screenshots; keyboard/focus and complete accordion interaction sweep; 320px/tablet layouts; real iPhone Safari and Android Chrome; final Privacy formatting screenshot; live SMTP delivery; actual Rank Math installation; cookie-consent/analytics/security configuration on hosting.

No Lighthouse/PageSpeed or LCP result was measured. Mobile PageSpeed >=80 and LCP <2.5s remain hosting acceptance targets, not achieved claims. The page assets use existing WebP imagery and established CSS/JS; production network/cache performance is unverified.

## Reproduce automated checks

Use a disposable WordPress installation with the child theme and estimator plugin active. Never point the integration suites at client enquiries.

```sh
php tests/approved-pricing.php
PRIME_DISPOSABLE_TEST_DB=yes PRIME_TEST_WP_ROOT=/absolute/path/to/disposable/wordpress php tests/approved-wordpress-integration.php
PRIME_DISPOSABLE_TEST_DB=yes PRIME_TEST_WP_ROOT=/absolute/path/to/disposable/wordpress php tests/nov12-wordpress.php
```
