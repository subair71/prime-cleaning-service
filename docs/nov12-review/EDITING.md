# Editing and updating WordPress

## Apply this feature branch to a review installation

1. Back up that installation's database and `wp-content`. Copy/update the existing `kadence-child` theme and `prime-cleaning-estimator` plugin from this branch. Keep the Kadence parent active/available. Do not import `database.sql`, replace the website, or copy the disposable QA database/mu-plugins.
2. In WordPress, open **Appearance > Prime approved pages** and run **Install approved layouts** once. The November 12 installer updates page bodies, reuses IDs/slugs/parents, saves prior bodies/statuses and revisions, applies supplied contact/minimum defaults once, and keeps price grids, approved suburb mappings and enquiries. Re-running preserves subsequent edits.
3. Verify existing URLs in Pages. The runtime used `/`, `/end-of-lease-cleaning/`, `/regular-home-cleaning/`, `/carpet-steam-cleaning/`, `/get-a-quote/`, `/about/`, `/contact/`, `/blog/`, `/privacy-policy/`. Existing `/services/.../` routes are reused when present; no URL migration is required. Office/Terms remain drafts.
4. Clear WordPress object/page cache and host/CDN cache, then hard refresh. The installer flushes rewrites and WordPress object cache; host cache still needs its own purge. Check that the **November 12 content** appears in the actual Pages editor and frontend, not just in PHP source.
5. Keep the review host private and noindex. Do not merge or deploy this branch to production until client inputs and outstanding QA are resolved.

## Edit content

Use **Pages > Edit** and the native block editor. Sections, headings, checklists/details, cards, policy text and buttons are editable blocks. Shared header/footer are in `inc/prime-page-components.php`. `inc/prime-launch.php` supplies initial page patterns; editing a pattern does not overwrite already installed page content. For later approved copy changes, update the actual page blocks or implement a separate versioned migration with a backup.

For legal pages, paste only client-approved text. Before running the installer on a different existing site, verify its Terms body; if approved Terms already exist, set post meta `_prime_legal_approved=1` to preserve them. Review the Privacy page's existing publication status; the installer preserves it. Do not publish Terms until v1.2 and the discount amendment are approved. Never publish Office during Phase 1.

## Prices and service settings

Open **Prime Estimator > Approved Pricing** (the v2 settings page). Enter the two independent grids; studio is bedroom 0. Edit each carpet/add-on rate, minimum (currently AUD110), deep multiplier (1.5), surcharge (15%), recurring discount (15%) and outer travel fee (AUD30). Blank means unapproved/missing and requests a custom quote. A zero is an intentional zero amount, so do not use zero as a placeholder.

Add approved suburb rows in the displayed `Name | Postcode | Zone` format; zone 1 is free travel and zone 2 uses the travel fee. Do not treat the PDF's draft suburb lists or old demonstration postcode prefixes as approved. Starting prices and price tables read these settings automatically. New quotes use current settings; historical enquiries retain their frozen breakdown.

Update phone/email/WhatsApp/ABN/review URL in the same approved settings. Keep the full standard response sentence. Review enquiries in the existing admin screen, use New/Contacted/Won/Lost, and export CSV there. Do not edit saved JSON price snapshots.

## Blog, images and SEO

Publish a real approved post to reveal Blog navigation/homepage listing. Use non-client owned/appropriately licensed imagery, accurate alt text and empty alt for decoration. Do not label illustrative people as the actual team.

The supplied SEO copy is centralised in `inc/prime-seo.php`. Use Rank Math Free as the single SEO owner when installed; no second plugin is needed. Configure HTTPS production URLs, cookie consent, GA4 and Search Console with client access. Check sitemap and robots on the actual host before launch; staging intentionally blocks indexing. No campaigns, conversion-event tracking or suburb landing pages were added.
