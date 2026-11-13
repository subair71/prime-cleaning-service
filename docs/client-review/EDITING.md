# WordPress content and pricing guide

## Apply this feature branch to a private review installation

1. Back up the site's database and `wp-content`. Update the existing `kadence-child` theme and `prime-cleaning-estimator` plugin from this branch. Keep the Kadence parent. Do not import `database.sql` or replace the site's settings/enquiries.
2. Open **Appearance → Prime approved pages → Install approved layouts**. The November 12 install is retained; this branch then applies a separate November 13 refinement once. It reuses page IDs/routes, backs up changed bodies in post meta and saves revisions. It removes only recognised image-caption badges, adds missing Call CTAs and estimator anchors, and preserves later edits. Privacy and Terms bodies are not transformed.
3. Clear WordPress page/object caches and the host/CDN cache. Hard-refresh and check the actual Pages content and frontend. The installer flushes object/rewrite caches; host cache purge is separate.
4. Keep staging private and noindex. Do not copy any disposable QA files, database or mu-plugins. Do not deploy to production as part of this review.

## Edit pages and reviews

Use **Pages → Edit** in the native block editor. Headings, paragraphs, cards, images, accordions and buttons remain core blocks. Dynamic price/review/form shortcodes should remain in their Shortcode blocks. Installed page content is independent of the source pattern; changing a PHP pattern does not silently replace later page edits.

Shared header/footer markup lives in `inc/prime-page-components.php`; shared design rules in `prime-approved-pages.css`.

For real reviews, open **Customer reviews → Add genuine Google review**:

1. Enter the customer's genuine first name and initial as the title; copy the genuine review body into paragraphs in the block editor.
2. Add the HTTPS Google review source URL in the source panel. Confirm the exact wording/author using the approval checkbox.
3. Publish only the verified real entry. Up to six published, approved entries with valid Google sources appear on Home/About. Drafts, missing sources and Lorem Ipsum do not render publicly. Ratings, counts and dates are deliberately not invented.
4. Add the overall Google review URL under **Prime Estimator → Approved Pricing**. Existing embed integrations can connect through `prime_reviews_integration_html`; none was available to configure here.

To show the Lorem Ipsum card design on a private staging review only, set `WP_ENVIRONMENT_TYPE` to `staging`, define `PRIME_REVIEW_PREVIEW` as `true`, and disable search-engine indexing. All three conditions are required. Leave the preview constant absent/false on production. The block editor stores real reviews; preview Lorem Ipsum is not customer content.

## Edit prices and business details

Use `Prime_Client_Inputs.xlsx` to collect approved rates, suburb zones and remaining content. Amber cells are client inputs. The workbook does not update WordPress automatically.

Open **Prime Estimator → Approved Pricing**. Enter the separate End of Lease and Regular bedroom × bathroom grids, four carpet rates, extras, approved suburb rows and business contacts. Studio is bedroom 0. **Blank means unavailable and requests a custom quote; zero is a deliberate zero rate.**

The minimum is editable (AUD110), as are the Deep multiplier (1.5), double-storey surcharge (15%), recurring discount (15%) and outer-zone travel fee (AUD30). The calculation order and ±10% nearest-AUD5 range remain unchanged. EOL safe-reach inside windows are already included; do not charge them again. Extra wall work remains a custom quote until its boundary is approved.

Suburbs use `Name | Postcode | Zone`, where 1 is free travel and 2 uses the travel fee. Do not treat the November 12 draft lists as approved. Starting prices update automatically. Price edits affect new estimates, not saved historical snapshots.

Keep the approved phone/WhatsApp/email/ABN, no-GST and payment-on-completion wording. Keep the full response promise: “We aim to reply within 2 hours during the day, and always within 24 hours.” Configure the business sender, nominated owner and SMTP together before testing real delivery.

## Enquiries and launch restrictions

Use **Prime Estimator** to review enquiries and set New, Contacted, Won or Lost. The original legacy statuses remain readable. Export CSV from **Approved Pricing**. A custom quote should display “Custom quote required”, never a zero price. Do not manually edit frozen snapshot JSON.

Keep Office unpublished and outside launch navigation/estimator. Keep Terms unpublished until approved Terms v1.2 and the discount amendment are supplied. The approved Privacy text is already present. Publish a genuine post to reveal Blog navigation. Do not add a before/after gallery, client-property photographs or stock people labelled as the real Prime team.
