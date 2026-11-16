# Fluent Forms feasibility — purchase not recommended yet

**The Pro estimator is not implemented or accepted.** Free 6.2.17 is installed only in a disposable local review. The contact-form export is not an estimator. No licensed Pro package, intended-host access or approved final pricing/suburb decisions were available. Do not buy a licence on the strength of the previous custom calculator's tests.

The signed November 10 proposal and reconciled client decisions are the source of truth. Legacy PHP is implementation history, not approval. `pricing-source-defaults.json` contains historical defaults only; do not import it as current prices.

## Verified documentation versus unproved behaviour

| Requirement | Evidence / proposed native configuration | Acceptance state |
|---|---|---|
| Arithmetic and rounding | Native numeric calculations document arithmetic, round, max and min. Use native option calculation values and clearly named rate fields. | Documented capability; exact Pro form not run. |
| Separate grids / Deep shared base | Separate Regular/EOL selection values, conditional bedroom/bathroom branches; Deep references the Regular result. Routine price edits must change one rate value, not formulas or duplicate tables. | Must demonstrate with Pro. No unsupported import fabricated. |
| Estimate before contact | Native multi-step/conditional fields with estimate step before name/phone/email. | Must test Pro navigation, validation and state resets. |
| Missing rates / custom quotes | Explicit native conditions, not treating empty/unknown selections as zero. | Must test every branch and suppress all numeric output. |
| Reference confirmation | `{submission.serial_number}` / `{submission.id}` are documented smartcodes; a literal PDC prefix is possible. | Exact initial sequence, global uniqueness and legacy continuity unproved. |
| Entry storage / CSV | Native entry management and export are documented; basic contact entry roundtrip checked in the earlier milestone only. | Frozen calculated breakdown and label/rate preservation after edits require Pro testing. |
| New / Contacted / Won / Lost | Official default entry states are read/unread/trash; custom statuses are documented through PHP in functions.php. | Conflicts with no custom code. Obtain a supported no-code solution or written alternative agreement. |
| Owner/customer emails | Native notifications and confirmation templates. | Configure with approved recipients on staging; test real Workspace delivery and failures. QA notifications remain disabled. |
| Reference-prefilled WhatsApp | Confirmation rich-text link with native submission smartcode and encoded fixed message. | Exact smartcode expansion and iPhone/Android opening untested. No message sent. |
| Saved price integrity | Native submitted values may be stored, but documentation alone does not establish server recalculation, immutable item labels or retry idempotency. | Test tampering, changed rate definitions, resends, duplicate submits and CSV after edits. Do not describe a submitted value as a confirmed price. |
| Dynamic service-card from-prices | No supported cross-page linkage to the form established. Derick permitted manual prices on November 6 if automation required custom code. | Use ordinary editable blocks and a documented duplicate-display checklist only after approved rates are supplied. |

Official references, reviewed 16 November 2026:
- https://fluentforms.com/docs/numeric-calculation-in-fluent-forms/
- https://fluentforms.com/docs/fluentform_entry_statuses/
- https://fluentforms.com/docs/shortcodes-in-confirmation-settings/
- https://fluentforms.com/docs/conditional-confirmation-message-in-fluent-forms/
- https://fluentforms.com/docs/managing-entries-in-fluent-forms/

## Calculation contract to validate

1. Select the approved Regular or EOL bedroom × bathroom cell (studio = 0 bedrooms; 0–5 bedrooms and 1–4 bathrooms).
2. Deep uses Regular ×1.5, without intermediate AUD5 rounding unless the proposed change is approved.
3. Double-storey adds 15% of the applicable base; Regular weekly/fortnightly subtracts 15% of base plus surcharge. No recurring discount on Deep, EOL, extras, carpet or travel.
4. Add selected, approved extras and carpet quantities, then travel (zone1:0; zone2:AUD30) from explicit approved suburb membership, not postcode-distance guessing.
5. Apply the approved minimum. Display ±10% rounded to nearest AUD5. The proposed lower-bound clamp is a separate decision, not assumed.
6. 6+ bedrooms, 5+ bathrooms, furnished EOL, unlisted/outside suburbs or any required missing price must use a custom-quote path without a numeric estimate.

Confirm inclusion conflicts, heavy-condition behaviour, minimum and all rates in `DECISIONS.md`. Amounts are AUD; do not add GST contrary to the client instructions. No payment gateway, customer account, live booking calendar, web photo upload or Phase 2 service.

## Required native implementation demonstration

Using a vendor demo or an existing authorised Pro installation, show separate grids, every custom-quote path, one reusable Regular rate powering Deep, a pre-contact estimate, saved breakdown preservation and the reference/status workflow. Resolve all essential gaps before recommending purchase. Do not send a vendor enquiry without authorisation.

After configuration, demonstrate through the UI: text edit; Regular/EOL cell change; carpet/add-on change; minimum/travel change; suburb membership change. Check that old enquiries and confirmations retain the original values. Export the actual tested Pro form through Fluent Forms → Tools → Export Forms.

`acceptance-cases.csv` contains synthetic expected results for the later Pro tests. Every Pro result is NOT RUN. Arithmetic fixtures are not client rates or proof that a Fluent Forms implementation works.
