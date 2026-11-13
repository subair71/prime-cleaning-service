# Verification — 13 November 2026

## Automated tests: 142 assertions passed

| Suite | Assertions | Coverage |
|---|---:|---|
| `tests/approved-pricing.php` | 39 | Separate grids, studio, Deep, surcharge/discount order, extras/travel, minimum, nearest-AUD5 range, carpet quantities and custom triggers |
| `tests/approved-wordpress-integration.php` | 20 | Real WP/AJAX, validation, sequential references, idempotency, frozen snapshots, failed saves, retained options and URL/ID reuse |
| `tests/nov12-wordpress.php` | 49 | Approved confirmations/emails, owner recipients, custom no-range, WhatsApp reference, 11 page patterns, drafts, indexing, dynamic prices and repeat installer |
| `tests/review-refinements.php` | 34 | Caption/source migration, retained alt/client copy, three CTAs, selected service anchor, idempotency, unchanged legal bodies/settings, placeholder publication gates and statuses |

All changed PHP files passed `php -l`. Changed JavaScript passed `node --check`; `git diff --check` passed. Pricing calculation code is unchanged.

The runtime uses disposable SQLite-backed WordPress, Kadence and the project estimator. Client MySQL and production data were never used. `pre_wp_mail` intercepts every send. Enquiry fixtures use `example.invalid` addresses. Demo prices/suburbs were restored to missing/unapproved after browser tests.

## Actual browser and WordPress checks

- Full-page desktop screenshots for all 11 pages, plus complete 390px mobile page captures. Confirmation evidence includes a full normal page and a cropped custom confirmation viewport. Mobile images are stitched browser viewport captures; the final fixed action bar is retained once. All images were loaded before capture. Final Prices screenshots include calendar/brush icons, not sparkles.
- 22 additional width checks: every page at 320 and 768px. Every checked page: one H1, no document overflow, no broken images and no illustrative caption badge. See JSON evidence.
- Mobile menu open/close, service submenu keyboard Enter/Escape, focus restoration and Carpet navigation link checked. Fixed Call/WhatsApp/Estimate actions displayed. Phone and WhatsApp destinations were inspected without launching external messages/calls.
- All 30 Home/service-page Details accordions opened and closed using Enter.
- Homepage Regular + 2-bedroom selection arrived in estimator query parameters; Carpet hides irrelevant home size. Carpet service-page CTA selected Carpet Steam Only; property inputs were hidden.
- Normal browser enquiry: Regular, 2 bedrooms, 1 bathroom, single-storey, QA zone 1, fixture base AUD200 → AUD180–220. Saved reference `PDC-1027`; frozen range and exact reference-prefilled WhatsApp action observed.
- Custom browser enquiry: Carpet with an unlisted QA suburb. Saved reference `PDC-1028`; empty confirmation range, no zero or numeric price, correct custom message and reference-prefilled WhatsApp.
- Contact form: blank submission produced field errors; completed submission saved `PDC-1029`. No external delivery attempted.
- Authorised admin CSV exporter executed against the disposable database: normal range, custom quote label, null custom bounds and frozen JSON verified. Status changes preserve the saved snapshot; invalid statuses rejected.
- WordPress's own registered core blocks parsed **1,482 blocks across 22 documents** (11 source patterns + 11 stored pages) with **zero invalid blocks**. This verifies serialization/editability; it is not a manual save of every page in Gutenberg.
- Installer executed against the actual review copy and caches cleared. Repeated runs preserve subsequent changes. Privacy/Terms body preservation asserted. Office/Terms remain drafts; Blog has no launch navigation without real posts. Review noindex/robots/sitemap behavior verified.

## Limits and acceptance blockers

No production deployment, external SMTP inbox test, physical iPhone Safari/Android Chrome run, Lighthouse/PageSpeed measurement, real MySQL host validation, Google reviews integration authentication, Search Console/GA4 verification, SSL/DNS/redirect configuration or backup restore was performed. These are pending, not passing claims.

The existing source uses a schematic for travel zones. It is not a verified geographic boundary map. Client-approved suburb mappings are missing. Existing image assets were retained; no new stock licence was acquired. The browser screenshot API intermittently timed out; mobile full pages were therefore assembled from successful viewport captures instead of being labelled as native full-page mobile screenshots.

## Reproduce

Use a disposable installation with the branch theme/plugin active. Run the installer once, then:

```sh
php tests/approved-pricing.php
PRIME_DISPOSABLE_TEST_DB=yes PRIME_TEST_WP_ROOT=/path/to/disposable/wordpress php tests/approved-wordpress-integration.php
PRIME_DISPOSABLE_TEST_DB=yes PRIME_TEST_WP_ROOT=/path/to/disposable/wordpress php tests/nov12-wordpress.php
PRIME_DISPOSABLE_TEST_DB=yes PRIME_TEST_WP_ROOT=/path/to/disposable/wordpress php tests/review-refinements.php
```

For the review suite's preview assertions, the disposable site must have `WP_ENVIRONMENT_TYPE=staging`, `PRIME_REVIEW_PREVIEW=true`, and `blog_public=0`. Never copy the local test harness, fixture database or preview-only draft renderer to production.
