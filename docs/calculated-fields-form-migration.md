# Calculated Fields Form migration specification

Status: preparation only. CFF is activated locally but no forms exist yet. Do not remove the current estimator until replacements are tested.

## Quote estimator fields
Location: required suburb and four-digit postcode; covered prefixes from WordPress settings (defaults 314 and 315); outside area must allow manual enquiry.
Service: regular, deep, lease, office.
Residential: bedrooms 1–10, bathrooms 1–10.
Office: hours at least office_min_hours.
Frequency: once, weekly, fortnightly; discounts for regular and office only.
Estimate: breakdown, minimum charge, tax mode, manual quote flag.
Enquiry: name, email, phone, preferred date, notes, consent; anti-spam controls.

## Exact price parity
Residential subtotal = (base_regular + bedrooms*bedroom_rate + bathrooms*bathroom_rate) * (1 for regular, multiplier_deep for deep, multiplier_lease for lease).
Office subtotal = max(hours, office_min_hours)*office_hourly_rate.
Apply weekly or fortnightly discount to regular/office only. Round discounts to cents. Apply minimum_charge before GST. Inclusive GST displays total/11; exclusive GST adds 10%; demo_unregistered displays disclaimer and adds no GST. Read actual saved settings, not hardcoded demo defaults. Recalculate and validate server-side.

## Contact form
Preserve current Contact page copy, fields, consent, and notification recipient.

## Migration gates
1. Create CFF forms in the installed plugin and record real form IDs. Verify plugin edition supports saving submissions and notifications.
2. Map all existing labels, required fields, conditions and formulas. Never trust client-submitted calculated totals.
3. Preserve historical wp_pce_enquiries records, price snapshots, statuses, admin workflow, and mail tracking. Ensure new submissions persist once.
4. Test four services, postcode coverage, discounts, minimum charge, tax modes, date validation, spam checks, email, and iPhone Safari.
5. Back up database, then replace existing shortcodes only after tests pass. Keep original plugin available for rollback.

No live WordPress pages or plugins are modified by this document.
