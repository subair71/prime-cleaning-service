# 12 November content implementation — initial inventory

Branch: feature/prime-nov12-content-seo, based on fetched origin/main fe80bf9. Working tree was clean. No AGENTS.md found in project or workspace parents. WordPress, Kadence Free child, native patterns, custom Prime Cleaning Estimator v2; PHP/vanilla JS. Main-only GitHub Pages workflow publishes docs, not WordPress. No production deploy in this task.

| Page | Existing route / source | Change |
|---|---|---|
| Home | /, front-page.php, prime-homepage pattern | Approved copy, 3 services, shared header/footer |
| End of Lease | existing mapped /services/end-of-lease-cleaning/ or /end-of-lease-cleaning/ | Exact checklist, bond explanation, no gallery |
| Regular & Deep | existing /services/regular-home-cleaning/ or /regular-home-cleaning/ | Rhythm cards, Deep section, FAQs |
| Carpet | /carpet-steam-cleaning/ | Method, rates, limitations |
| Prices | /get-a-quote/ | Live data tables, estimator copy |
| About | /about/ | Family story, values, Derick's WWCC |
| Contact | /contact/ | Approved contacts, zones pending approval |
| Blog | /blog/ | Empty state; no navigation until genuine posts |
| Privacy | /privacy-policy/ | Located Privacy Policy v1.2, 8 Nov 2026 |
| Terms | /terms/ | Approved Terms v1.2 not located; draft layout |
| Office | existing /services/office-cleaning/ or /office-cleaning/ | Unpublished Phase 2 design |

## Source precedence / conflicts
- Entire 16-page 12 Nov copy and 4-page 10 Nov proposal reviewed. November 12 removes gallery, client-property imagery and extra service launch links; office remains draft.
- Revised service and estimate boards inspected; retained existing teal/mint/Poppins/Inter architecture.
- Supplied suburb lists explicitly draft: do not install them as approved zones. Existing approved option preserved. Original demonstration postcode prefixes are not approval.
- Minimum now AUD110; initialise once while keeping editable. Grids and all carpet/add-on rates remain unapproved/blank; never substitute legacy demo rates.
- End of Lease safe-reach inside windows and ordinary wall spots are included. Prevent duplicate windows charge; extra wall work requests custom quote until scope boundary approved.
- Use full standard response sentence even where source sections abbreviate it.
- No stock/generated people presented as actual staff. Existing generated interior illustrations contain no client properties; team photograph pending.
- Terms v1.2 requires approved 15% recurring-discount clause. Do not author a contract amendment.
- Privacy wording references hosting, security, cookies, GA4 and embeds; deployment configuration must match before launch. No analytics/review/embed activated without access and consent setup.

## Missing inputs
Approved price grids, carpet and add-on rates; confirmed suburb lists; Google review URL/widget; owned photographs/logo; Terms v1.2 and approved recurring-discount amendment; nominated owner email; SMTP/hosting/GA4/Search Console access. Privacy Policy located, so do not request again.

## Preservation
No SQL import, production writes, enquiry deletion or historical snapshot recalculation. Installer takes page body backups/revisions, reuses IDs/URLs, and runs once per version. Test email transport is blocked in isolated runtime.

SEO implementation follows Rank Math's documented filters (https://rankmath.com/docs/filters-and-hooks/frontend/meta-data/ and https://rankmath.com/docs/filters-and-hooks/admin/sitemap/). This checkout has no active SEO plugin in the disposable runtime, so the fallback owns metadata/core XML sitemap. Rank Math hooks hand ownership to Rank Math when installed, avoiding a second output. Client hosting plugin activation remains to be verified.
