# Client handover draft

**Page editing is prepared. Estimator/pricing handover is not complete. Do not present this milestone as final contractual delivery.**

| Change | Normal WordPress location |
|---|---|
| Page text, images, buttons, cards | Pages → choose page → Edit; select a block; use its toolbar and Settings panel; Save |
| Colours, fonts, page width | Appearance → Customize → Colors & Fonts / General |
| Header, quote button, mobile menu | Appearance → Customize → Header |
| Service menu items | Appearance → Menus → Prime main navigation |
| Footer copy | Appearance → Widgets → Footer 1–4; footer layout in Customize → Footer |
| Contact fields and consent | Fluent Forms → Prime — Native contact → Edit |
| New contact enquiries | Fluent Forms → select form → Entries; use native entry export |
| Notifications/confirmation | Form → Settings & Integrations; review sender, recipients and SMTP before enabling notifications |
| Mobile action labels, links and icons | DS Mobile Menu → Menu Builder |
| Mobile bar colours, height and safe-area support | DS Mobile Menu → Appearance; visibility in Visibility; settings backup in Import & Export |
| FAQs | Pages → select the Details block; edit its summary or inner answer paragraph |
| Blog articles | Posts → Add New; the Blog Query Loop displays published posts |
| Reviews | Home → Google reviews section edits the supplied review link. Live review widget setup is pending; never add sample ratings/testimonials |

Use List View to select nested sections/cards. The source HTML files are migration material; everyday page editing does not require opening them. Theme/plugin-generated styles are normal WordPress behavior.

The Office and Terms drafts should stay unpublished until approved. Review the actual site's Privacy publication state before migration. The clean review includes the supplied Privacy v1.2 copy, but hosting/privacy claims still require verification on the intended host.

**Prices, suburbs and estimator entries** are managed in Fluent Forms. Use the [field-by-field editing guide and staging acceptance cases](ESTIMATOR.md). The old Prime settings menu and the previous Free/custom bridge are not acceptable handover solutions.

**Old enquiries:** the current package does not migrate customer records. Retain the complete database backup and enquiry CSV; verify a supported independent archive/access method before retiring the custom admin UI.

## Reconciliation update — 16 November

Fresh-review page URLs follow the signed scope: `/`, `/regular-house-cleaning/`, `/end-of-lease-cleaning/`, `/carpet-steam-cleaning/`, `/prices/`, `/about/`, `/contact/`, `/blog/`, `/privacy/`, `/terms/` (draft). Office is a separate hidden Phase 2 draft. Preserve actual existing URLs until a redirect plan is approved.

Site logo: Appearance → Customize → Site Identity/Header Logo. Desktop sticky behaviour: Customize → Header → Sticky Header; main row enabled, mobile header scrolls away. The review now includes a plugin-managed mobile bottom bar; physical iPhone and active-link accessibility acceptance remain unresolved.

Home hero: Pages → Home → List View → first Kadence Row Layout block; edit text/buttons directly. Its light teal background is a native block setting, with no image. Service inclusions, FAQ answers and price-display blocks are edited on their corresponding pages. Until final pricing is confirmed, price displays deliberately request a custom quote. Do not enter workbook model values as approved rates.

See DECISIONS.md for withheld claims and QA-RECONCILIATION.md for actual tests. A programmatic block-save test is not the requested client UI demonstration; text/price/suburb demonstrations remain outstanding.

## Visual enhancement update — 17 November

For section spacing, use Pages → Edit → List View → select **Row Layout** → block Settings → responsive Padding and Columns. For a card, select its **Section** block and use Background, Border, Border Radius and Padding. Use the desktop/tablet/mobile selectors in the native controls. Select an Image block for Replace, Crop/Aspect Ratio and alt text. Do not edit the serialized source files for routine changes.

The service comparisons now use labelled cards; edit their Heading and Paragraph blocks. Home’s quote card opens the Prices page. See ESTIMATOR.md for the current form and ENHANCEMENT.md for the earlier visual review.

## Applying the header and footer to Prime in LocalWP

1. In the Prime site's WordPress dashboard, open **Plugins → Add New Plugin**. Install and activate **Customizer Export/Import** by Beaver Builder.
2. Open **Appearance → Customize → Export/Import**. Click **Export** to download a copy of the current theme settings. For a complete rollback point, use Local’s site export before importing.
3. Under **Import**, choose [Prime-Header-Footer.dat](exports/Prime-Header-Footer.dat), leave **Download and import image files?** unchecked and click **Import**. Open the site in a fresh tab.
4. Check the desktop phone link and **Request a quote** button, then open **Menu** on a narrow screen. At the footer, check the WhatsApp button, services, company links and contact details.

The preset expects Prime’s Kadence parent theme and the existing four footer widget areas. It places Footer 1 in the upper brand row and Footer 2–4 in the lower row. It sets the desktop phone and footer WhatsApp destinations. The site supplies its own logo, navigation menus, quote URL, copyright and widget text. Import the preset once when adopting the design. Importing it again reapplies its layout and style settings.

| To edit | Where to go |
| --- | --- |
| Desktop service area and phone label | Appearance → Customize → Header → Top Row → HTML or Social |
| Phone or WhatsApp destination | Appearance → Customize → General → Social Links |
| Quote button or mobile Menu button | Appearance → Customize → Header → Button or the mobile Trigger |
| Footer brand, services, company and contact text | Appearance → Widgets → Footer 1 through Footer 4 |
| Footer WhatsApp label, row colours and spacing | Appearance → Customize → Footer → Social or the relevant row |

The plugin transfers the preset. Kadence renders the design and provides the editing controls afterward.
