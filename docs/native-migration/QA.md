# Validation — 15 November 2026

## Passed

- Eleven native page layouts: 782 blocks parsed by this repository's own WordPress core JavaScript block definitions with **zero invalid blocks**. No Custom HTML blocks, custom shortcodes or child-theme references.
- Fresh import into a second, different-origin WordPress installation containing **only the Kadence parent, Kadence Blocks and Fluent Forms**; custom Prime theme/plugin directories were absent. Media URLs/IDs and contact form references remapped. Another 782 imported blocks parsed with zero invalid blocks.
- **83 native WordPress assertions passed:** parent theme, native-only active plugins, absent custom classes/shortcodes/CSS, page statuses, registered blocks, URL remapping, form fields, honeypot, disabled QA notifications, native menus, draft exclusion and block-content save/restore. This is a programmatic save roundtrip, not an authenticated Gutenberg UI session.
- Repeated importer invocation refused to overwrite existing edits.
- Actual browser blank contact submission produced required-field errors. A synthetic enquiry saved once in the native Fluent Forms entry table and displayed the configured confirmation. No external email notification was enabled.
- Eleven desktop pages: one H1, no document overflow, no broken loaded images, no child-theme/Prime-estimator asset requests. Eleven mobile and eleven tablet frame layouts: one H1 and no horizontal document overflow.
- Kadence mobile drawer opened; its service submenu exposed all three service links.
- All **30** Details/FAQ/inclusion panels opened and closed through the browser.
- PHP syntax checks and Git whitespace checks passed. WordPress core and vendor theme/plugin files were not edited.

Evidence: `exports/block-validation.json`, `exports/runtime-checks.json`, `screenshots/*-checks.json`, and the desktop/mobile/confirmation screenshots. Browser: cloud Chromium; desktop viewport 1348px, mobile iframe outer width 390px (375px content after scrollbar), tablet outer width 768px (753px content). These are responsive-layout tests, not device emulation.

Run the integration checks on a disposable local copy:

```sh
PRIME_DISPOSABLE_TEST_DB=yes php tests/native-wordpress.php /absolute/path/to/review-wordpress/wp-load.php
```

## Not verified / release blockers

- Fluent Forms Pro: full multi-step estimator, all price boundaries, independent price/suburb editing, conditional-state handling, recalculation/tamper resistance, custom quote outputs, duplicate submission/retry recovery, final confirmation and notifications.
- Current client database/content/status reconciliation, real backup restore, frozen historical quote/reference migration and independent archive access. No real customer data was accessed.
- Authenticated browser editing/saving in Gutenberg, Kadence Customizer and the Fluent Forms editor. No signed-in administrator session was available; block serialization and database roundtrip passed instead.
- SEO metadata/Open Graph/schema parity through a native SEO plugin. Original approved copy is preserved for configuration, not active in the parent-only review.
- Real SMTP delivery, notification recipients, production hosting and MySQL. The disposable runtime uses SQLite; no inbox-delivery claim is made.
- Native local Google Font cache failed in the constrained PHP runtime. Review exports use Kadence's normal Google Font option with local loading off; real-host local serving/privacy verification remains outstanding.
- iPhone Safari, physical devices, consent/analytics/Google embeds, production backups/security and live performance. Do not infer these results from Chromium.

Old calculator/bridge tests are retained as legacy specifications; their previous success is **not evidence that a Fluent Forms Pro estimator is implemented**. There are no production credentials, customer rows or synthetic business rates in the migration exports.
