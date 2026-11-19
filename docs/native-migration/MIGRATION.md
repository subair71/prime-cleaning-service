# Installation and rollback

## Fresh isolated review (tested)

Use an empty local/staging WordPress, a disposable database, noindex enabled, Kadence parent 1.6.0, Kadence Blocks 3.9.0, DS Mobile Menu (`devstack-mobile-menu`) 1.1.0 and Fluent Forms Free 6.2.17 or the licensed target version. The checked source WordPress version is 7.1.4. Pro remains required for the estimator; installing this package does not supply or emulate it.

Activate the native plugins, set `WP_ENVIRONMENT_TYPE` to `local`/`staging` in the review environment and disable search-engine indexing. With the repository available **outside the WordPress web root**, run:

```sh
PRIME_NATIVE_REVIEW=1 wp --path=/absolute/path/to/review-wordpress eval-file /absolute/path/to/repository/tools/import-native-review.php
```

The importer refuses existing non-default content/forms, any Fluent Forms entries, production/indexed sites, active custom estimator and repeated imports. It imports the three service images and the client logo through the Media Library, imports the contact form through Fluent Forms' own import service, remaps URLs/image/form IDs, creates the 11 block pages, restores native Kadence/widget settings, imports the mobile-action settings through the plugin's own sanitizers and builds the menu. It sets Home and the supplied Privacy page, Melbourne timezone and readable permalinks. It does not send notifications, install paid products, restore a database, import customers or deploy anything.

The supported WXR and Fluent Forms JSON exports are also included for manual migration. WXR is a **content export, not a full backup**. Its source attachment URLs are localhost and cannot be fetched by another host. Use the tested fresh-review importer to sideload the bundled images and remap their IDs, or upload the four supplied image files and reconnect Image/Cover blocks through the editor. Never assume importing XML alone restores media/theme/form configuration.

Review the saved blocks and native settings. No PHP/JS generator needs to remain installed. Keep staging private using the hosting platform's normal controls; WordPress noindex is not access protection.

## Existing client site — not executed

Do not run the fresh-review importer against the existing site. Its refusal is intentional. A Git branch does not isolate WordPress databases or uploads.

1. Create a host/LocalWP backup of the **current** database and `wp-content`, and verify that it can be restored into a separate environment. Do not import the repository's old `database.sql`. No current client database was accessible in this task, so no real-site backup is claimed.
2. Export current WordPress content; Kadence/child settings, menus/widgets and SEO metadata; Fluent Forms definitions and entries; actual approved pricing/suburb options; and the existing enquiry CSV. Retain the entire custom enquiry table and review posts in the database backup. Store backups privately, outside Git.
3. Clone into isolated staging. Inventory actual page IDs, parent paths, slugs, statuses, approved legal copy, genuine reviews, plugin/snippet/MU-plugin list and notifications. Compare against this package; do not overwrite client edits with source defaults.
4. Transfer each matching native page layout to the existing page using the block editor's block-copy/paste facility or supported content tools. Reconnect existing Media Library images. Preserve page IDs, parent paths, slugs, publication state and newer approved copy. Do not recreate a nested service page at a new root URL.
5. Configure the parent Kadence controls from the provided settings or reproduce them in the Customizer. Keep current menus/widget IDs when mapping native areas; do not blindly overwrite options containing installation-specific IDs. Import the native contact form; use its new actual ID when inserting the supported Fluent Forms block/shortcode.
   Configure DS Mobile Menu through its native Import & Export screen using `exports/mobile-actions.json`; remap the Estimate URL to the real staging page. Confirm Call and WhatsApp values. Its production use remains conditional on the limitations in ENHANCEMENT.md.
6. Import the native quote definition and select the imported form on the existing quote page. Follow [the estimator guide](ESTIMATOR.md) for priced and custom acceptance cases and the price-editing demonstration. Configure notifications once, with approved recipients/sender; use intercepted mail until SMTP is ready. Resolve historical workflow and SEO gaps listed in `AUDIT.md`.
7. Only after replacements pass, switch to parent Kadence and deactivate the custom estimator/bridge on staging. Remove obsolete shortcodes, snippets, custom CSS and theme-asset references from stored content. Do not delete legacy tables/customer records. Verify every page and enquiry journey with custom code disabled.
8. Run browser, accessibility, iPhone Safari, native-editor save, Pro pricing/tamper/duplicate/error-recovery, SMTP, backups and hosting checks. Enable native local-font serving on the real host and verify the privacy-policy claims. Keep the PR draft until all gaps are resolved.

Production cutover is blocked by the unresolved acceptance items and missing hosting access. No production operation has been performed.

## Rollback

The real migration must start with the verified backup above. Before a later cutover, record exact active theme/plugin lists, native page revisions, form IDs and configuration.

On staging, restore pre-migration pages/settings/media and reactivate the recorded original theme/plugin versions if needed. Preserve enquiries received since the backup; never restore an old whole database over new customer submissions. Reconcile/export those records first. A Git checkout alone cannot restore content, options, menus, form settings or customer history.

This branch retains legacy runtime files until Pro/history acceptance. The previous bridge documentation is historical, not a contract-compliant fallback. For the disposable fresh review, recovery is simply recreating its empty database and reimporting; do not use that procedure on a client site.
