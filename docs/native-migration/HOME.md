# Prime homepage for LocalWP

This package contains the page pattern and both Fluent Forms exports. Use it on the existing Prime site with the Kadence theme, Kadence Blocks, Fluent Forms and Fluent Forms Pro active.

## Install in WordPress

1. Unzip **Prime-Homepage-LocalWP.zip**. Open the Prime site in Local and click **WP Admin**.
2. Open **Fluent Forms → Tools → Import Forms** and import `fluentforms-home-quick.json` once. Look for **Prime | Quick Estimate** under **All Forms**.
3. If **Prime | Request a Quote** is new to this installation, import `fluentforms-quote.json`. If the form already holds enquiries, keep that form and its entries. Open its existing service and property size dropdowns. Under **Advanced Options → Dynamic Default Value**, enter the values in the table below, then save. Keep each form title unique so the page can find it.
4. In the Home page editor, open the top-right options menu and choose **Manage patterns**. You can also open `/wp-admin/edit.php?post_type=wp_block` from the local site's address. Choose **Import from JSON** (or **Import pattern from JSON** in the pattern manager) and upload `prime-home-pattern.json`.
5. Open **Pages → Home → Edit**. Use **List View** to select the existing page-body blocks and replace them with **Patterns → My patterns → Prime Homepage**. This unsynced pattern gives Home its own editable blocks. Preview the page, then click **Update** or **Save**. The site-wide Kadence header and footer appear automatically.
6. If adding the page to a fresh site, choose **Home** under **Settings → Reading → A static page**. The review uses the Kadence full-width, unboxed page layout with the page title hidden. Its three service cards link to the existing Prime service pages.

| Existing detailed form field name | Dynamic Default Value |
|---|---|
| `service` | `{get.prime_service}` |
| `home_size` | `{get.prime_size}` |
| `lease_size` | `{get.prime_size}` |

## Check the result

- A one-off regular clean for two bedrooms and two bathrooms shows **$190–$230** before extras and travel. Deep cleaning for the same property shows **$285–$345**.
- End of lease for two bedrooms and one bathroom shows **$305–$375**. Choose **Continue to detailed estimate** and confirm those selections appear in the detailed form.
- A layout outside the approved price grid requests a custom estimate. Carpet shows the per-bedroom rate and job minimum, with room and stair quantities available in the detailed form.
- Test the full priced enquiry with the site's licensed Fluent Forms Pro, a local mail catcher and an iPhone in Safari before publishing. A successful enquiry displays the confirmation and reference. Prime confirms booking arrangements separately.

## Make changes after handover

Edit section order, spacing, fonts, colours, icons and links in **Pages → Home**. The forms are embedded using the plugin's native title-based shortcode. If several forms share a title, use the Fluent Forms block to select the intended form by ID.

Edit the quick ranges and their conditions in **Prime | Quick Estimate**. Edit the calculation grids, extras, travel and notifications in **Prime | Request a Quote**. When a rate changes, update both forms and the relevant starting price on the service card.

The homepage uses the approved pricing and links to Prime's Google reviews page. The reference's sample testimonials and sample customer confirmation have been replaced by that link and the form's real submission confirmation.
