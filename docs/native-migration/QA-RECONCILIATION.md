# Verification — 16 November 2026

This is a **review milestone, not launch acceptance**. Baseline source commit: `646c620eda3da66c8cb982d683414cdfc7372e23` (PR #7 merged). No client-host database, media, settings or enquiries were accessible. The historical repository SQL is not a current client backup.

## Implemented and checked

- Native light-teal Home hero with no photograph; client-supplied logo imported through Media Library; desktop sticky header enabled through Kadence's own setting. Mobile header scrolls away as requested.
- Fresh-review slugs aligned to `/prices/`, `/privacy/`, `/regular-house-cleaning/`; internal page/footer/contact-consent links updated. This does not migrate existing live URLs.
- Unconfirmed AUD110 minimum, bed-making, cancellation deadline and 24/72-hour free re-clean promises removed from review copy. EOL window/wall inclusions deferred to each written quote. Terms and Office remain unpublished. No gallery/client-property publication added.
- **89/89 native WordPress integration assertions passed**: native theme/plugins, no custom runtime classes/shortcodes/CSS, imported logo, header settings, page status, native registered blocks, menus, required form configuration, and programmatic block-save/restore.
- **787 blocks across 11 layouts: zero invalid blocks**, parsed against this repository's WordPress core JavaScript block definitions in JSDOM 26.1.0. This is serialization validation, not visual/browser editing. JSDOM emitted 10 unsupported-modern-CSS parser warnings; no CSS-rendering assertion is made.
- **5/5 native contact-service assertions passed**: normal blank fields rejected; blank not saved; one valid synthetic enquiry saved; supplied values preserved; confirmation contains no Google review request. A direct call to the plugin's submission service was used, with its normal empty honeypot input. No HTTP/AJAX/browser or inbox-delivery result is implied.
- Local database/media backup copied to a separate restore location. SQLite integrity `ok`, full logical database match, all four media file hashes matched, two synthetic enquiries retained. **89/89 integration assertions also passed against the restored database.** This is not a VentraIP/MySQL backup or live restoration test.
- PHP lint and Git whitespace checks passed. Native WXR content export regenerated; actual page HTML rendered to portable review snapshots.

Evidence is in `exports/reconciliation-*.json`. Runtime: WordPress 7.1.4, Kadence 1.6.0, Kadence Blocks 3.9.0, Fluent Forms Free 6.2.17, PHP 8.4.24, standard SQLite test drop-in. No vendor/core files were edited; the drop-in is not a production dependency. Tests and one-time migration tools remain outside the WordPress web root.

## Still blocked / not tested

| Requirement | Current result / next evidence |
|---|---|
| Live-site audit and full backup | Missing access to intended VentraIP installation. Preserve holding page and obtain a verified current backup before modifying it. |
| Pro estimator and 10+ cases | Not implemented. `acceptance-cases.csv` contains 12 numeric fixtures and 8 branch/preservation/retry cases, all explicitly NOT RUN. Confirm policy choices and use an authorised Pro demonstration before purchase. |
| Frozen price snapshots / references / statuses | Not verified. Contact text preservation is not saved-price testing. See feasibility and decisions. |
| Owner/customer email and WhatsApp confirmation | QA notifications disabled; no external messages sent. Test Workspace delivery, failure/retry and exact reference-prefilled links on staging/devices. |
| Fixed mobile Call/WhatsApp/Estimate bar | No no-code implementation validated with installed native stack. Ordinary call/WhatsApp/quote links are present; they do not satisfy a fixed bar. |
| Genuine live Google reviews | Client-supplied review link added to Home. No fake reviews added. Live review feed configuration and load behaviour remain to be verified. |
| Current responsive preview / iPhone Safari | This environment could not connect its browser to the local server; browser policy also blocks local file URLs. No bypass attempted. Portable snapshots are available for user review, but no fresh visual/device result is claimed. Older screenshots are historical. |
| Performance | **Not measured.** Required mobile PageSpeed ≥80 on Home and three service pages, LCP <2.5s on selected live hosting. Record date, URL, device profile and launch configuration when measured. |
| Client visual editing demonstration | Programmatic page-save checks passed. Live text, price and suburb/travel UI demonstrations remain outstanding. |
| Production SEO/security/privacy | Native SEO configuration, analytics/consent, actual host backups, local fonts and final privacy claims remain to be verified on staging. |

## Reproduce the implemented checks

On a disposable noindex local installation with native dependencies, run:

```sh
PRIME_DISPOSABLE_TEST_DB=yes php tests/native-wordpress.php /absolute/path/to/wp-load.php
PRIME_DISPOSABLE_TEST_DB=yes php tests/native-contact.php /absolute/path/to/wp-load.php
```

The contact test writes a synthetic entry. Keep all notifications disabled and use a disposable database. These checks refuse a non-local or indexed installation. Export and reconcile historical real enquiries separately; never delete them to satisfy a fresh-import test.
