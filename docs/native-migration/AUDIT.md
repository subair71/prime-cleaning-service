# Custom component audit

This audit concerns the supplied repository and isolated review installations. It is not an audit of the client's live database, host, snippets manager or installed must-use plugins.

| Existing dependency | Native review replacement | Status / difference |
|---|---|---|
| `kadence-child/functions.php`, `front-page.php`, `page-prime-approved.php`, custom footer templates | Kadence parent templates and settings | Disabled in review; source retained pending full acceptance |
| `inc/prime-homepage.php`, `prime-pages.php`, `prime-launch.php`, PHP pattern installation | Stored, editable core blocks in all 11 layouts | No render-time PHP helpers or custom pattern installer required |
| `prime-approved-pages.css`, other child CSS and project JS | Block style attributes and Kadence controls | No new custom CSS/JS; normal WordPress/theme/plugin-generated assets remain |
| PHP navigation, service dropdown, mobile toggle | WordPress menu + Kadence header/mobile drawer | Native interactions tested; custom fixed mobile action bar/floating WhatsApp removed; actions remain in page/header content |
| Custom logo SVG helper | Native site title and tagline | Custom home icon omitted pending supplied business logo; mobile title uses Kadence's default title presentation |
| PHP footer | Four native Block widgets + Kadence copyright control | Footer headings/spacing differ slightly; client-editable |
| `[prime_home_estimator]`, `[prime_cleaning_estimator]`, AJAX calculator, custom tokens | Temporary native quote-unavailable card | **Blocked**; full Fluent Forms Pro form still required |
| `PCE_Fluent_Forms`, custom CSS/JS bridge and hooks | Standalone Fluent Forms contact form | Bridge inactive; no custom engine reused |
| Starting-price, carpet-rate, minimum/discount/pricing shortcodes | Native paragraphs/tables/cards | Source defaults only; unapproved rates read “Custom quote”; automatic synchronization is not implemented |
| Contact-details/response/ABN shortcodes | Native text, links and buttons | Completed |
| Service-area shortcode and SVG schematic | Native zone cards | Explicit zones retained; schematic omitted, not replaced with invented boundaries; approved suburb list absent |
| Review custom post type, approval meta, integration filter | Native Quote blocks can hold approved reviews | No genuine reviews in supplied input; existing live reviews must be exported before disabling their custom registration |
| Blog listing/category/query helpers | Query Loop, post template, pagination, no-results blocks | Custom featured-first layout/filter pills/automatic article contents/related-post PHP not reproduced; core Categories/Details/Query blocks are the native editing alternative |
| Custom SEO filters, metadata and schema | Core titles/canonicals and noindex review setting | Full SEO parity pending approved native SEO configuration. Original title/description copy preserved in `exports/seo-copy.json`; do not claim schema/Open Graph parity |
| PCE enquiry table, statuses, frozen quotes, references, mail tracking | Native Fluent Forms entries for **new contact enquiries** | Historical data and estimator workflow not migrated; see below |
| Old Calculated Fields Form plugin | No active dependency | Not used to bypass Fluent Forms Pro |

No runtime code was added to WordPress core, Kadence, Kadence Blocks or Fluent Forms. The SQLite drop-in is a standard test-environment dependency and is not part of the deliverable. No client database, customer rows or credentials are included in the exports.

## Deliberate review differences

Native controls reproduce the teal/mint/navy palette, images, cards, headings, FAQ panels and page structure. Exact font wrapping, card heights, gutters, menu animation and button positions differ. The homepage estimate selector is visibly unavailable pending Pro. No custom CSS was used to disguise these differences. Compare the new screenshots with `docs/client-review/screenshots`.

Poppins/Inter use Kadence's native Google Font setting in the isolated review. Native local-font downloading timed out in the constrained PHP environment, so **Load Google Fonts Locally is off in this export**. Enable and verify it on the actual host before accepting the supplied privacy policy's hosting claims; system fonts are the native alternative. No font loader code was added.

## Historical workflow gaps

Keep `wp_pce_enquiries` (actual table prefix may differ), all related options and original review posts. Never uninstall/delete their data as part of this milestone.

Before real cutover, use the existing enquiry CSV export and retain a complete database backup. The CSV includes references, statuses, customer/property details and frozen quote JSON; it does **not** preserve every email/error/request-key field, so CSV alone is insufficient. Verify row counts, sample snapshots and reference mappings against the database backup.

The old states `new`, `contacted`, `quoted`, `closed`, `won`, `lost`, sequential `PDC-…` references, frozen server quote snapshots, retry/idempotency handling, email handoff/error tracking and review approval UI are not claimed equivalent to Fluent Forms defaults. Establish supported native handling or agree a documented archive/operational alternative. Merely leaving inaccessible tables behind does not satisfy the client's independent-history-access requirement.

## 16 November follow-up

The signed no-photo light-teal hero, supplied logo, native desktop sticky header and fresh-review root URLs are now reconciled. Unconfirmed minimum, bed-making and policy commitments were removed. Current conflicts are in DECISIONS.md; previous assertions about approved source defaults must not be treated as approval. Native Pro status handling is a documented gap, not a paid-licence assumption. See QA-RECONCILIATION.md for current evidence and untested items. The client VentraIP site remains inaccessible and unaudited beyond supplied sources.
