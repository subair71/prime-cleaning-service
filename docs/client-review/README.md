# Prime client review — 13 November 2026

Branch: `feature/prime-client-review-refinements`, based on main `eb5755c` (merged November 12 content). Review only: no merge or production deployment.

[Open the screenshot gallery](index.html) · [Proposal checklist](CHECKLIST.md) · [Tests and limitations](QA.md) · [WordPress editing guide](EDITING.md) · [Client input workbook](Prime_Client_Inputs.xlsx)

## Completed refinements

- Removed the illustrative image-caption badges from the stored WordPress image blocks and source patterns. Removed sparkle branding from legacy theme styles/footer and replaced estimator sparkles with calendar/brush icons. Inspected the three in-use image originals: no embedded marks needed image editing. Existing project interior assets remain; no stock person is presented as the Prime team.
- Refined shared spacing, headings, service cards, menus, contact actions, legal tables and tablet/narrow-phone layouts. Completed Call / WhatsApp / Estimate bands. The existing Carpet Steam Cleaning URL, approved method, dynamic area rates, guidance, exclusions, FAQs and minimum remain intact.
- Added responsive reviews on Home and About. Lorem Ipsum preview cards contain no customer identities, stars, counts, dates or verification claims. They require an explicit preview constant, a non-production environment and disabled indexing. Production hides placeholders. Genuine reviews can be added using the WordPress block editor with a checked Google source and approval flag.
- Preserved approved copy, legal bodies, pricing calculations, settings, enquiry records, original page IDs and URLs. A versioned update removes only known caption badges and adds missing contact/estimate actions. Re-running preserves later edits and saves prior content/revisions.
- Improved homepage handoff: Carpet hides irrelevant Home Size; other service/bedroom selections remain in the URL and open the full estimator at its anchor. Fixed Escape focus handling for navigation and excluded marked sample blog posts from the listing.

## Review evidence

All 11 requested pages have full-page desktop and mobile screenshots. Desktop images are browser full-page captures. Mobile images are stitched from the actual WordPress frontend in a 390×844 responsive iframe; fixed actions appear once at the bottom. This is Chromium responsive QA, not an iPhone Safari run.

The normal full-page and custom confirmation-viewport screenshots show real saved enquiries in the disposable QA database, using clearly marked **test rates and a test suburb**. They are not approved client prices. Numeric fixtures were removed after testing. No email, WhatsApp message or phone call was sent.

142 automated assertions passed, plus native core-block validation, actual admin CSV export checks, 30 keyboard accordion checks, browser form submissions and 320/390/768/desktop responsive checks. See QA.md for exact scope and limits.

The actual WordPress review copy was synchronised and its object/rewrite caches cleared. A local preview was used during this session. No public preview was published; copy this feature branch to a private client review host using EDITING.md.

## Outstanding client inputs

1. Approved End of Lease and Regular grids; carpet/add-on rates; confirmed suburb-to-zone mappings. Blank prices still request a custom quote.
2. Google review URL and genuine review content or selected integration configuration.
3. Approved Terms v1.2 with the recurring 15% discount amendment. Terms remains a draft. Privacy v1.2 is already retained.
4. Business-owned logo and non-client photographs, including an actual team photo if desired. Existing interior assets are retained from the project; this task did not acquire or certify a new stock licence.
5. Extra End of Lease wall-cleaning scope, nominated owner address, approved sender and SMTP/hosting access.
6. Hosting-specific SEO/analytics/cookie/security/backup setup and device/performance acceptance checks.

Lorem Ipsum remains only in the private review cards and the unpublished Office draft. The Terms draft has a missing-document notice. Missing prices and suburb mappings are represented honestly, without invented values or coverage.
