# Prime homepage on staging

[Open the staging homepage](https://primedomestic.com.au/staging/) · [Download the LocalWP package](exports/Prime-Homepage-LocalWP.zip) · [Browse the native configuration files](exports/staging-home/)

This is the WordPress homepage published on 22 November 2026. Its header, hero, estimate card, service photos, enquiry steps, About section, Google reviews, service areas, final contact panel and footer are editable blocks. Prime’s original logo appears in the header, About section and footer. The five FAQs are in the footer. The latest-posts query shows up to three posts when articles exist.

## Package contents

| File | What it restores |
| --- | --- |
| `prime-home-pattern.json` | The exact published Home blocks, for use in an existing Home page |
| `wordpress-home-prices.xml` | Home and Prices, their Kadence page settings, three synced starting-price patterns, two Google Reviews widget definitions and five media records |
| `fluentforms-estimators.json` | The active Home quick estimate and detailed Prices estimator, including their native calculations, confirmation settings and configured notifications |
| `media/` | The original Prime logo, three service photographs and the shared theme’s header crest |
| `mobile-actions.json` | DS Mobile Menu settings for Call, WhatsApp and Estimate |
| `Prime-Header-Footer.dat` and `Prime-Footer-Widgets.wie` | The shared Kadence Customizer settings and four footer widget areas used on other pages |

## Restore on another WordPress installation

1. Use the Kadence parent theme with Kadence Blocks, GenerateBlocks, Fluent Forms and licensed Fluent Forms Pro. The review feeds use **Widgets for Google Reviews** (RichPlugins). DS Mobile Menu provides the mobile action bar. Keep the existing site and form entries when updating an established installation.
2. Import the two form definitions in **Fluent Forms → Tools → Import Forms** on a fresh installation. On an existing installation, identify the two active forms first. If they already contain entries, retain them and apply changes to those forms. Record their actual IDs.
3. On a fresh copy, use **Tools → Import → WordPress** with `wordpress-home-prices.xml` to bring in the pages, synced price patterns, review widgets and media records. Assign the content to an existing local author and select the option to download attachments. The supplied images can also be uploaded to the Media Library. On an existing site, import missing dependencies and update the existing Home page with the included pattern. WordPress can skip existing posts during XML import. Use either the imported Home page or the pattern in your existing page.
4. In the Home page’s Kadence settings, select a full-width, unboxed layout, hide the title and vertical padding, and check **Disable Header** and **Disable Footer**. The page supplies its own header and footer. Other pages retain the shared theme layout. Assign Home under **Settings → Reading**.
5. Match the references below to the target installation. In the block editor, reconnect the synced patterns and images, and replace the shortcode IDs. If the quick form has a different ID, update its `#fluentform_8` block style selectors as well. Change staging URLs in Home, Prices, the quick form’s confirmation, theme links, widget links and mobile actions to the target site's URLs. In **Google Reviews**, connect Prime Domestic and Cleaning Services and refresh manually to load its genuine reviews on a fresh site.
6. To restore the shared theme layout elsewhere, import the DAT with **Customizer Export/Import** and the four footer areas with **Widget Importer & Exporter**. Review existing widget areas first to avoid duplicates. Check the theme’s logo, menus and site icon against the target Media Library. Import the mobile JSON in **DS Mobile Menu → Import & Export**.

| Staging ID | Reference |
| --- | --- |
| 8 / 4 | Fluent Forms Home quick estimate / Prices detailed estimate |
| 38 / 40 / 39 | Regular, end-of-lease and carpet starting-price synced patterns |
| 55 / 36 | Google rating summary / Google review carousel |
| 7 / 64 / 65 / 66 | Original logo / regular / end-of-lease / carpet service photos |
| 76 | Shared theme header crest |

The Quick Estimate sends the service and property choices to **Prices**. On another host, edit the destination under the quick form’s **Settings & Integrations → Confirmation Settings** and retain its merge tags, query string and `#estimate` anchor. The ZIP contains form definitions only. Keep the business licence and any site credentials in the target installation.

## Staging checks

The default regular clean for two bedrooms and two bathrooms displayed **A$190–A$230**. Changing to end-of-lease cleaning with three bedrooms and two bathrooms displayed **A$425–A$515** and carried that selection to Prices. The review carousel showed three cards on desktop and one on mobile. The page rendered one header and one footer, and the five FAQ accordions worked. Four original before-and-after pairs are still awaiting client photos.
