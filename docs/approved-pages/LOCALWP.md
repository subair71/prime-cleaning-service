# Update the existing LocalWP site

This is an update to the existing site, not a fresh installation. Git updates files; page content, active theme, options and enquiry records live in the LocalWP database. Pulling a branch alone does not install the editable page layouts.

## 1. Confirm the actual site folder

In Local, select **Prime Cleaning Service → Go to site folder**. Confirm that the served WordPress directory is `app/public` and that it contains `wp-config.php` and `wp-content`.

Open Git Bash in the existing repository and run:

```bash
git rev-parse --show-toplevel
git status --short
git branch --show-current
git remote -v
```

The repository's `app/public/wp-content` must be the directory Local serves. If your Git checkout is elsewhere, updating that checkout will not change the Local site. Keep unrelated local changes; do not use `reset --hard` or delete the site.

Before applying to your working Local site, take a Local export/database backup. **Do not import the repository's `database.sql` and do not replace `wp-config.php`** for this update. The existing homepage, enquiries and settings must remain in your current database.

## 2. Fetch the review branch

In the repository, with local changes safely retained:

```bash
git fetch origin
git switch --track origin/feature/prime-approved-page-designs
```

If the feature branch already exists locally, use these instead:

```bash
git switch feature/prime-approved-page-designs
git pull --ff-only origin feature/prime-approved-page-designs
```

If Git reports overlapping local changes, stop and reconcile those files; do not force the switch. This branch is for local review. No merge or production deployment is required.

If the checkout is separate from Local, copy the **contents** of these two updated directories into the same directories inside the Local site's `app/public/wp-content`, allowing file replacement but retaining other themes/plugins:

- `themes/kadence-child/`
- `plugins/prime-cleaning-estimator/`

Do not copy the repository's full `app/public` folder or SQL export over the existing site.

## 3. Activate the existing theme and plugin

Start the site in Local and open WP Admin.

- **Appearance → Themes:** ensure the Kadence parent is installed and **Kadence Child – Prime Cleaning** (`kadence-child`) is active. Do not replace the child with a fresh theme.
- **Plugins:** ensure **Prime Cleaning Estimator** is active. The updated plugin reports version **2.0.0**.
- Reload WP Admin once. The plugin adds a nullable unique request key to the existing enquiries table; it does not clear or recreate the existing records.

Optional checks in **Local → Open site shell**:

```bash
wp option get stylesheet
wp plugin status prime-cleaning-estimator
wp option get pce_schema_version
```

Expected stylesheet: `kadence-child`. Expected schema version after a successful load: `2`.

## 4. Install the editable page layouts once

Go to **Appearance → Prime approved pages → Install approved layouts**.

The installer:

- Reuses mapped existing page IDs, parent relationships, URLs and publication statuses, including `/services/end-of-lease-cleaning/` and `/services/regular-home-cleaning/` from the original database.
- Saves the previous body in `_prime_preapproved_content` and a WordPress revision before installing native blocks.
- Creates missing pages only; newly created Privacy Policy and Terms pages remain drafts.
- Leaves the homepage, extra service pages, blog posts, enquiries, legacy pricing options and unrelated settings intact.
- Records installed pages in `prime_approved_page_ids`. Running it again does not overwrite subsequent page edits.

The new layouts replace the **displayed** body of mapped pages. Original copy remains in the backup/revision for review and reuse; it is not automatically merged into the new layout. Review any approved existing copy and move it into the appropriate new blocks.

Existing mapped routes can render a layout fallback immediately after the files are updated, but the installer is required to put editable blocks into their page records and create missing pages.

Open **Pages → All Pages** and edit each installed page with the native block editor. Keep the outer `prime-approved-content` group. Replace labelled placeholders with approved content and photographs. Publish new legal pages only after the client supplies/approves the wording.

Do not change **Settings → Reading → Homepage**. The existing Blog/Posts-page assignment is supported; a regular `/blog/` page also works without changing Reading settings.

## 5. Enter approved prices and business details

Open **Prime Estimator → Approved pricing**.

Enter the client-approved End of Lease and Regular grids, minimum charge, add-on rates, carpet rates and suburb list. A blank applicable price deliberately leads to a custom quote. Existing legacy/demo rates are not copied into the new grids.

The suburb input format is:

```text
Approved suburb name | four-digit postcode | 1 or 2
```

Zone 1 is 0–15 km with no travel charge. Zone 2 is 15–25 km with the editable AUD 30 default. Use the approved list; do not infer approval from postcode prefixes. The static map placeholder must be replaced with an approved static asset once boundaries are supplied.

Confirm the editable 1.5 Deep/Spring multiplier, 15% double-storey surcharge and 15% recurring discount. Add business phone, WhatsApp international digits, business email, nominated Gmail, business-domain From address, ABN, reviews link and approved response/discount wording. Blank contact values remain visibly unavailable rather than linking to invented details.

Configure the existing mail transport/SMTP for the approved domain and verify delivery to both owner destinations and a test customer inbox. The plugin records mail handoff failures without losing the saved enquiry. A successful `wp_mail` handoff does not prove inbox delivery.

## 6. Refresh and verify

- **Settings → Permalinks → Save Changes**, retaining the current permalink structure.
- Purge any existing WordPress/cache-plugin cache. Stop/start the Local site if PHP OPcache retains older files.
- Hard refresh the browser (`Ctrl+Shift+R`) or use a private window.
- Open the service page from WP Admin's **View** link, which confirms its actual URL.
- Check an approved estimate and all relevant custom-quote cases, then submit a labelled test enquiry.
- Check **Prime Estimator → Enquiries** for the saved record, frozen breakdown and reference. Verify status updates and CSV export on your Local/MySQL environment.
- Check the phone and WhatsApp links on a real iPhone Safari after approved numbers are configured.

If the site still looks unchanged, check the active branch, the served folder, child-theme activation, the installer step and cache in that order. GitHub Pages under `docs/` is an older static preview, not the running WordPress site.

## Existing estimator compatibility

`[prime_cleaning_estimator]` displays the approved flow. Existing homepage links using `prime_service`, `prime_bedrooms` and `#pce-estimator-container` remain supported. `[prime_cleaning_estimator mode="legacy"]` and the existing `?prime_service=office` handoff retain the legacy estimator, settings and endpoints. No standalone Deep/Spring page is added or existing extra page deleted.

## Rollback without losing records

Switch back to the previous code revision only after retaining local work. Restore a page's original body from its WordPress revision or the `_prime_preapproved_content` backup. Do not roll back the entire database to restore a visual layout: that could remove newer enquiries. The added nullable request-key column can remain when using older code.
