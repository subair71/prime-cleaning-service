# Prime Request a Quote

The [Fluent Forms Pro import](exports/fluentforms-quote.json) contains Derick’s two pricing grids, carpet and extra rates, 41 suburb and postcode combinations, custom quote rules, and enquiry workflow. Regular and Deep share a base price. End of Lease includes floor-reachable inside windows and inside cupboards. Zone 2 travel adds $30 on every visit. Regular, Deep and carpet jobs have a $110 minimum, including the lower end of the range.

## Apply to the existing Prime site in LocalWP

1. In Local, select the existing Prime site, start it and open **WP Admin**. With Fluent Forms Pro active, go to **Fluent Forms → Tools → Import Forms**. Choose `fluentforms-quote.json` from this repository’s `docs/native-migration/exports` folder and import it once. Find **Prime | Request a Quote** in All Forms. If a previous copy already exists, review its entries and use its ID in the page block to distinguish versions.
2. Edit the existing quote page. Replace the previous form with a **Fluent Forms** block and select the imported form. The repository’s Prices page uses a native Shortcode block with `[fluentform title="Prime | Request a Quote"]`. Keep the existing page URL and the anchors used by quote buttons. The repository uses `/prices/#estimate` and accepts `/prices/#quote`; the public homepage has also used `#quote`.
3. Review the two notifications in **Settings & Integrations → Email Notifications**. Priced and custom requests go to **info@primedomestic.com.au**. Use the local mail catcher for test submissions. If the site is configured to send via external SMTP or an email API, route test mail locally first. Confirmation includes the entry reference and a WhatsApp photo link. The reference is generated from the native entry ID. Check it against historical references before rollout.
4. Open the quote page using **Open site** in Local and check the prices below, submit both a priced and a custom request, and verify the saved entries, notifications and the iPhone Safari flow. Keep the original form and its entries when switching the page.

WordPress stores the embedded form and the page edit in the local database. A Git pull supplies the export files. Importing the form and updating the existing page applies them to LocalWP.

For empty review installations the [importer](../../tools/import-native-review.php) imports the form when Pro is active and disables its outgoing notifications. With Fluent Forms Free the Prices page offers phone and WhatsApp enquiries.

## Edit prices and service areas

Open **Fluent Forms → Prime | Request a Quote → Edit**. The field names below are admin labels.

| Change | Where to edit |
|---|---|
| Regular or Deep base rate | **Regular and Deep property size** → option calculation value. Deep uses the same amount. A blank price requests a custom quote. |
| End of Lease base rate | **End of Lease property size** → option calculation value. |
| Carpet prices | **Carpet rate per bedroom (AUD)** and **Carpet rate per lounge (AUD)** → default value. Hallway and stairs use calculation values in **Which carpet areas need cleaning?** |
| Extras | **Oven cleaning**, **Optional extras** and **Windows and cupboards** → option calculation values. |
| Surcharge and discount | **How many storeys?** → double-storey calculation value. **How often would you like a clean?** → weekly and fortnightly calculation values. |
| Service minimum and travel | **Minimum job charge (AUD)** and **Zone 2 travel fee (AUD)** → default value. |
| Move or add a suburb | **Suburb and postcode** → option calculation value. Zone 1 uses 0 and Zone 2 uses 1. Choose one zone per suburb and retain its postcode. |
| Confirmation and email | **Settings & Integrations** → confirmation and email notifications. |

Keep stored option values stable when changing rates because visibility rules refer to those values. Update the editable starting-price table on the Prices page whenever its advertised rates change.

## Check the estimate

| Enquiry | Total | Range |
|---|---:|---:|
| Regular, fortnightly, 3 bed and 2 bath, single storey, Carnegie | $204 | $185–$225 |
| Empty End of Lease, 2 bed and 1 bath, single storey, oven, Glen Waverley | $460 | $415–$505 |
| Deep, 4 bed and 2 bath, double storey, fridge, Bentleigh | $505.75 | $455–$555 |
| Carpet only, one bedroom, Carnegie | $110 | $110–$120 |

The configuration passed 480 independent tariff combinations and the three examples supplied by Derick. The isolated WordPress review covered native import/export, mobile-width rendering, service switching, custom enquiry storage, captured notification and the reference-prefilled WhatsApp link. A longer hallway or a flight beyond the supplied carpet limits goes to a manual quote.

To repeat the customer-facing checks on licensed staging, run [the Playwright check](../../tests/native-quote-browser.py) against the quote page. It exercises the four prices, included extras and a priced-to-custom switch without submitting an enquiry. The isolated review had Fluent Forms Free 6.2.17. Live numeric rendering on the licensed host, mailbox delivery, physical iPhone Safari, price-edit handover and historical entry/reference reconciliation still require staging verification.
