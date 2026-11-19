# Prime homepage for LocalWP

The Home pattern uses Kadence and WordPress blocks. The Quick Estimate and the detailed homepage estimate are native Fluent Forms. The detailed form places the property choices beside the live price and custom-quote messages on desktop. On narrow screens, the choices come first, followed by the estimate and contact fields. The existing Prices page continues to use its own Request a Quote form.

## Apply the homepage

1. Extract **Prime-Homepage-LocalWP.zip**. Open the existing Prime site in LocalWP with Kadence, Kadence Blocks, Fluent Forms and Fluent Forms Pro active.
2. In **Fluent Forms → Tools → Import Forms**, import `fluentforms-home-quick.json` if **Prime | Quick Estimate** is absent. Import `fluentforms-home-detailed.json` once to add **Prime | Homepage Estimate**. Existing entries stay with their original forms. If you already have a homepage estimate with entries, keep it and choose the intended form by ID in the page editor.
3. Under **Appearance → Patterns → Manage my patterns → Import from JSON**, import `prime-home-pattern.json`. On installations without that menu shortcut, open the pattern manager at `/wp-admin/edit.php?post_type=wp_block`.
4. Edit the existing **Home** page. In List View, replace its body blocks with **My patterns → Prime Homepage**. In **Fluent Forms → All Forms**, copy the shortcode shown next to each homepage form. In the pattern’s two **Shortcode** blocks, replace the matching title-based shortcode with the one you copied. Saving with the actual IDs lets Fluent Forms load its native column and input styles in the page header. Use the full-width, unboxed Kadence page layout, turn off the page title and disable vertical content padding. Choose the existing Home page under **Settings → Reading** if a static homepage has not been assigned.
5. The pattern uses the three service photos and Prime logo from the project. To host them with WordPress, upload the files in `media/` and choose **Replace → Open Media Library** for each Image block. The logo occupies the team-photo position until Derick supplies the original team photo. The reviews section links to Prime on Google. For a three-card feed, connect Prime's listing through a Google Reviews widget, select a three-column layout and insert its provided block above the existing Google button.
6. Preview before saving. Quick Estimate opens the same Home page at the detailed form with the selected service and property size. The homepage form starts with a regular weekly clean, two bedrooms and two bathrooms in Malvern East. The quick estimate describes its one-off rate. Custom-quote conditions and the final entry confirmation use the approved form rules.

### Existing Quick Estimate installations

If you already have the quick form, retain its entries and update its **Settings & Integrations → Confirmation Settings**. Select **To a Custom URL**. Set the URL to `{embed_post.permalink}?prime_service={inputs.quick_service}`, enable the query string, and enter `prime_size={inputs.quick_beds}%20%C2%B7%20{inputs.quick_baths}#estimator-section`. These are Fluent Forms’ built-in merge tags. The separate query string preserves both selections on import.

## Match the shared header and footer

The page body uses the theme's existing header and footer. The included Customizer export configures the utility bar, navigation, estimate button, four widget columns and one copyright row. Applying these theme settings also updates the shared header and footer on other pages.

- In **Appearance → Customise → Export/Import**, export the current settings, then import `Prime-Header-Footer.dat`. The Export/Import panel is provided by **Customizer Export/Import**. Retain the existing site logo and the primary and mobile menu assignments. The header estimate button links to the estimator on Home from any page.
- Four existing Kadence Footer widget areas hold **About**, **Services**, **Quick links** and **Contact**. On empty Footer 1–4 areas, import `Prime-Footer-Widgets.wie` with **Widget Importer & Exporter**. For populated areas, move their previous blocks into **Inactive Widgets** before importing, so the content appears once. These two import utilities can be deactivated after setup.
- Privacy and terms links use the published Prime policy addresses. The local Terms draft can stay in review. The page body adds no mobile action bar. Keep the site's existing mobile actions in their plugin.

## Check the rates and enquiry flow

| Selection | Approved one-off range before extras and travel |
| --- | --- |
| Regular, 2 bedrooms and 2 bathrooms | $190–$230 |
| Deep, 2 bedrooms and 2 bathrooms | $285–$345 |
| End of lease, 2 bedrooms and 1 bathroom | $305–$375 |

The regular card starts at $110, end of lease at $250 and carpet at $45 per bedroom with a $110 job minimum. The estimate includes the approved grid, frequency, storey, travel, add-on rates and custom-quote conditions. A price change needs updating in the homepage form, the separate Prices form and the matching quick ranges.

Use the licensed staging installation to submit one priced and one custom enquiry, inspect their entries, and verify the business notifications. The homepage receives entries in **Prime | Homepage Estimate**. Notifications and customer confirmations use the same settings as the approved detailed form. Use a local mail catcher when testing.
