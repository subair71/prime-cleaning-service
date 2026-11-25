# Prime Domestic website design guide implementation

These files contain editable WordPress page patterns, Fluent Forms Pro imports and Kadence settings for the November 2026 design. The review used the existing project's logo, service photographs, service-zone diagram, approved pricing inputs and suburb selections. All public-facing page content uses WordPress core blocks. The forms use the Fluent Forms editor for fields, rules, calculations, notifications and confirmations.

## Staging status

The public staging address is https://primedomestic.com.au/staging/. Its existing pages were inspected. The new files were imported into a separate WordPress installation for browser and submission tests. Installation on the hosted staging site still needs a signed-in administrator session. Nothing in this branch deploys to a host or changes existing enquiries.

## Apply the changes in the staging dashboard

1. Confirm the dashboard address ends in /staging/wp-admin/. Take a host backup and note the existing menu locations, widget assignments, page revisions, form IDs and entry counts. Keep the original form definitions and entries. Leave Office Cleaning outside the launch scope.
2. In Fluent Forms → Tools → Import Forms, import each of the three files in the forms directory once. Their titles end in “· Guide”. The Home, Prices and Contact patterns reference these titles using the plugin's standard embed block/shortcode. If a form was previously imported, edit that form or select its intended instance in the page editor. Verify notification recipients in the builder. Route staging tests to a controlled test inbox before enabling delivery.
3. Import the page JSONs through Appearance → Patterns → Manage my patterns → Import from JSON (the location varies by WordPress version). Insert the corresponding pattern into the existing page. Detach the pattern if WordPress inserted it as a synced instance. Replace the earlier layout only after saving a revision and comparing the copy. Use the full-width, unboxed page layout, disable the duplicate page title and use the shared theme header/footer. Keep each page ID, permalink, front-page setting and publication status. Review the two legal exceptions below.
4. In Appearance → Customise, apply the palette, Poppins heading settings, logo, spacing, desktop and mobile header, and footer values recorded in theme/customizer.json. This file is a settings reference. Choose Media Library attachment 7 for the logo. Assign the four footer patterns to the corresponding Footer Builder block-widget areas. Preserve widgets with unrelated content.
5. Set up the Primary and Mobile menus, followed by the fixed actions described below. Verify existing telephone, email, Google Maps and WhatsApp destinations. Choose one contact bar per device and retire an older action bar only after the Kadence replacement works. All internal page and form redirects in this package target the staging installation.
6. Leave optional analytics disabled. Only use the host's cookie controls when consent can actually prevent optional tracking before opt-in. Recheck the current network requests on the hosted staging site. There is no decorative cookie notice.
7. Repeat the browser and submission checks on the real host, then compare page IDs, original form entry counts and backup against the starting state.

| Existing page | Staging ID | Pattern file |
| --- | ---: | --- |
| Home | 12 | pages/home.json |
| End of Lease Cleaning | 14 | pages/end-of-lease-cleaning.json |
| Regular & Deep Cleaning | 15 | pages/regular-house-cleaning.json |
| Carpet Steam Cleaning | 16 | pages/carpet-steam-cleaning.json |
| Prices & Instant Estimate | 24 | pages/prices.json |
| About | 25 | pages/about.json |
| Contact & Service Areas | 26 | pages/contact.json |
| Blog | 27 | pages/blog.json |
| Privacy Policy | 17 | pages/privacy.json |
| Terms of Service | 18, draft | pages/terms.json |

The service photographs reference existing staging attachments 64 (Regular & Deep), 65 (End of Lease), 10 (Carpet) and 8 (About and Journal). The approved static zone diagram is attachment 114. Verify crops in the Image block settings when selecting a replacement.

## Navigation and fixed contact actions

Use Home, Services, Prices, About and Contact in both launch menus. Place End of Lease Cleaning, Regular & Deep Cleaning and Carpet Steam Cleaning beneath Services. Assign the mobile menu to the Kadence drawer and expose its service children. Include Get my estimate, Call us and WhatsApp in that drawer. On desktop the theme header contains the phone action and estimate button. Enable the sticky main row only for desktop. Keep the Blog page out of the header and footer until the first genuine article is published. Add the Terms link after the draft is approved.

The fixed controls have editable patterns in theme/mobile-actions.json and theme/desktop-whatsapp.json. On a staging installation with Kadence Theme Kit Pro and Hooked Elements enabled, configure separate site-wide Fixed Section elements. Use bottom placement on mobile with visible Call, WhatsApp and Estimate labels and bottom-right placement on desktop for WhatsApp. Restrict each element to the appropriate device widths. Use the business dialler and WhatsApp destinations and the staging Prices page already present in the patterns. The footer setting reserves 88 px on mobile. Verify 44 px minimum tap targets, the last form field, submit button, footer links and iPhone safe area on the host.

The isolated review had the Kadence parent theme. Fixed positioning was unavailable there. The menu and inline contact links work in the review. Its screenshots show the placement actually achieved.

## Editing estimates

The detailed form has one responsive four-step flow. The service and bedroom controls reveal the applicable bathroom selector. Each bathroom selector stores that bedroom row's approved amounts in its option calculation values. Blank rates route to manual review. The Home mini-estimator has its own native conditional range summaries. When prices change, update both forms in the dashboard and verify the affected combinations. Public tables explain the basis of pricing and direct visitors to the estimator. There are no illustrative price tables.

Weekly and fortnightly Regular discounts apply to the base plus the applicable double-storey surcharge. Extras and outer-zone travel are added afterwards. The form applies the $110 minimum before calculating the range and rounds each endpoint to the nearest $5. A $110 subtotal therefore displays $100–$120. A manually reviewed enquiry omits the price from the result and both emails. Estimates save selected inputs, price components, subtotal, endpoints and preferred date. The generated reference is used for both the confirmation and its WhatsApp destination.

Use Fluent Forms → Entries to open a submission. Use the form builder to change rates, extras, suburb zone values and notifications. After editing, submit both a priced case and a manual case. Check the entry, customer message and internal notification. Preferred dates remain requests until the team confirms them.

## Legal and editorial approval

The staging Privacy Policy is version 1.2 and has 16 sections. The review pattern preserves those sections and omits the named privacy officer to follow the design brief. Obtain approval for that deletion before replacing the published policy. The Terms pattern is a draft outline. It contains no invented cancellation fee, lockout charge or binding conditions. Complete it with the approved wording before publication. The Journal Query Loop is ready for genuine posts. The review uses a direct link to the business's genuine Google listing.

## Review evidence and open checks

The review ran WordPress with the Kadence parent theme and Fluent Forms Pro. All ten pages were opened at 1440, 768, 390 and 320 CSS pixels in desktop Chromium 138. Desktop/mobile full-page captures and estimator interaction captures were taken from that installation. The legal captures are draft previews. Browser checks covered page overflow, original media loading, the service menu, the scrolling mobile header, the sticky desktop header, field validation, standard and custom confirmations, saved entry fields, the Home hand-off and reference-prefilled WhatsApp links. Test emails were captured locally. Hosted mail delivery, the installed Pro fixed actions, their clearance, physical iPhone Safari and Android Chrome remain pending.

| Review case | Subtotal | Displayed range (AUD) |
| --- | ---: | --- |
| Regular, 3 bedrooms, 2 bathrooms, fortnightly, Carnegie | $204.00 | $185–$225 |
| Regular, 2 bedrooms, 1 bathroom, weekly, double-storey, fridge and outer-zone travel | $226.40 | $205–$250 |
| Deep, 4 bedrooms, 2 bathrooms, double-storey and fridge | $505.75 | $455–$555 |
| Empty end of lease, 2 bedrooms, 1 bathroom, standard oven and outer-zone travel | $460.00 | $415–$505 |
| Carpet-only, 1 bedroom in the inner zone | $110.00 | $100–$120 |
| Carpet-only, 3 bedrooms, 1 lounge, hallway and stairs | $275.00 | $250–$305 |
| Weekly Regular case above with 2 carpet bedrooms added | $316.40 | $285–$350 |
| Furnished end of lease, 6+ bedrooms, 5+ bathrooms, blank applicable grid cell, unpriced double oven, or unlisted suburb | Manual review | Price omitted |

The most visible differences from the guide are the Fluent Forms progress indicator, field and calendar styling, and the static desktop help panel beside the estimate. The form shows its calculated breakdown in the result step. The fixed contact actions and unapproved Terms body remain to be checked separately. Device-specific column spacing, text wrapping and button proportions can also differ from the previews.
