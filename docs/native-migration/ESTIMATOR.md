# Fluent Forms Pro estimator — blocked, not implemented

No licensed Pro installation was available. Only Fluent Forms Free 6.2.17 was verified. `fluentforms-contact.json` is a **contact form only**. There is no estimator export, price-editor demonstration, server-side calculation claim or Pro acceptance result.

Source of truth for the migration is `includes/class-pce-approved.php` and the latest approved client settings/documents. `exports/pricing-source-defaults.json` preserves source defaults for reference; it is **not** a backup of live prices. No synthetic QA prices were included.

## Inputs still required

- Licensed Fluent Forms Pro package/activation in the intended development installation.
- Actual current WordPress database/configuration and historical enquiries, with a backup.
- Approved Regular and End of Lease grids, four carpet rates, five add-on rates and explicit suburb/postcode/zone mapping. None were present in the supplied source defaults. Do not overwrite any rates that exist in the client's actual database.
- Approved notification sender/owner addresses and SMTP configuration. Do not use an assumed recipient.

## Native implementation acceptance

Build Service → Property → Extras → Suburb → Estimate → Enquiry → Confirmation using the installed Pro version's supported editor controls. Verify its field types, condition evaluation, hidden-field behavior, formula functions and submission handling before selecting a configuration. No custom PHP/JS/CSS, code snippets, custom endpoints or copied calculator code may remain active.

Label each editable rate clearly. Prefer native calculation values/options or named numeric rate fields, referenced by formulas. Routine price changes must not require rewriting formulas. A grid lookup design must demonstrate that the Regular rate is shared by Regular and Deep, rather than copied into two separate price tables. If the native UI cannot support that maintainably, record the precise limitation instead of adding code.

| Rule | Required behavior |
|---|---|
| Base grids | Separate Regular and End of Lease: bedrooms 0–5 (0 = studio), bathrooms 1–4. No guessed values. |
| Large homes | 6+ bedrooms or 5+ bathrooms → custom quote |
| Deep / Spring | Regular base × 1.5 source default; no recurring discount |
| Double storey | 15% of the base (Deep-adjusted where applicable), rounded to cents; not carpet-only |
| Regular recurrence | Weekly/fortnightly: 15% off base **plus storey surcharge**; not add-ons/travel |
| Add-ons/carpet | Add quantities × respective rate after base adjustments; quantity integer 0–99; carpet-only needs at least one quantity |
| Lease inclusions | Safe-reach inside windows are included: never charge that add-on; ordinary wall spots included, extra wall scope → custom quote |
| Furnished lease | Custom quote |
| Zones | Approved name + postcode list only; zone 1 travel 0, zone 2 travel AUD30 source default; unknown/outside → custom quote |
| Minimum | Apply AUD110 source default to subtotal **after** add-ons/carpet/travel |
| Indicative range | Total × 0.9 and × 1.1, each rounded to nearest AUD5; match original rounding boundaries |
| Missing applicable value | Custom quote, null numeric estimate; an unavailable rate is never treated as zero |
| Tax/payment | No GST added; equipment/products included; bank transfer/PayID on completion; no confirmed booking/price from an enquiry |

Original order, expressed as a specification rather than assumed Fluent Forms formula syntax:

1. Choose base grid; apply Deep multiplier if needed.
2. Compute and round the storey surcharge.
3. Compute and round eligible recurring discount on base + surcharge.
4. Add selected extras, carpet amounts and approved zone travel.
5. Apply minimum; compute rounded low/high range.
6. If any custom-quote condition or required missing rate applies, suppress numeric totals and breakdown.

Keep every accepted entry's submitted service/property/extra selections and displayed indicative estimate. Test altered calculated values, omitted fields, changed hidden flags, validation errors and duplicate/retried submissions. Determine whether the installed Pro version recalculates on the server. A browser-submitted amount must not be described as an authoritative confirmed quote.

## Required client-maintenance demonstration

In the actual WordPress UI, change and restore one Regular grid cell, one Lease cell, one carpet rate, one add-on, minimum/travel, a discount and a suburb mapping. Verify resulting estimates and custom-quote paths after every change. Export the verified final Pro form. This demonstration is still outstanding.

Starting prices in the native review are ordinary blocks, not automatically linked to Fluent Forms. Until a supported native synchronization method is verified, use this explicit manual checklist: Home service cards; Regular/Lease hero price; Carpet hero and rate cards; Prices table/minimum/travel notice; Regular discount cards/FAQ; Home/Contact service-zone wording. Edit matching displays whenever a rate or business rule changes, or remove starting amounts and link directly to the estimator. Do not promise a single-price source across pages until this is resolved.
