# Verification and implementation notes

## Test environment

Verified against a separate real WordPress runtime using repository core version 7.1.4, PHP 8.4.27, the existing Kadence parent/child and the existing estimator plugin extended to version 2.0.0. The disposable runtime used the official SQLite integration. The repository's MySQL export, local configuration and real enquiry database were not imported, replaced or edited.

Browser checks used Chrome with 1440 px and 390 px CSS reference widths, plus a 320 px overflow sweep. Full-page images are stitched browser captures of the actual rendered WordPress pages. The desktop viewport was scaled to fit the capture surface and resampled to 1440 px. They are implementation captures, not generated mockups or claims of native iPhone Safari verification.

## Automated checks

| Check | Result |
|---|---|
| Calculator regression suite | 30 passed |
| WordPress integration suite | 20 passed |
| PHP syntax | 21 theme/plugin PHP files passed |
| JavaScript syntax | Approved flow, page controls and legacy controller passed |
| Native WordPress block parse/validation | All 9 patterns passed; no invalid blocks |
| Narrow viewport overflow | All 9 pages plus single post passed at 320 px |

The calculator tests use labelled synthetic rates, never production defaults. They cover independent grids; studios and the 5-bed/4-bath boundary; Deep/Spring; double storey; weekly/fortnightly discount exclusions; carpet-only; add-on quantities; both travel zones; minimum before range; nearest-AUD-5 rounding; no GST; forged client totals; all five custom-quote cases; and blank versus explicit zero.

The WordPress tests exercise real AJAX handlers and database writes: server quote generation; strict date, consent and parking validation; persistence despite mail failure; idempotent retries; a frozen quote after admin rate changes; optional fields; sequential database-derived references; expired-quote recovery; an injected database failure with no false success; legacy settings/calculator; repeat installer safety; hierarchical service route reuse; additive migration preservation; reference-prefilled WhatsApp URL generation; and the historical estimator anchor.

Run the pure calculator check:

```bash
php tests/approved-pricing.php
```

Run the integration suite **only in a disposable WordPress installation**. It writes labelled test records and temporarily changes fixture settings/page parents:

```bash
PRIME_DISPOSABLE_TEST_DB=yes PRIME_TEST_WP_ROOT=/absolute/path/to/disposable/wordpress php tests/approved-wordpress-integration.php
```

Never run that integration command against the user's working or production database.

## Browser checks completed

- Desktop/mobile captures of all nine pages, Blog populated and empty states, and single-post template.
- Service preselection and the existing homepage bedroom query/anchor handoff.
- Property, frequency, add-ons, carpet quantities, suburb and travel steps.
- Range and expandable breakdown shown before contact fields.
- Required and optional enquiry fields; inline validation; failed-save recovery with retained input.
- Same-page success only after a saved record, with actual test references (not hardcoded PDC-1001).
- All five custom-quote outcomes; separate Deep/Spring and Carpet Steam Only screens.
- Contact form saved a labelled enquiry and showed its real reference.
- Blog pagination yielded one remaining article on page 2; category filtering yielded four matching articles.
- Mobile menu opening/Escape closing; keyboard checklist tabs; FAQ/details controls; sticky desktop header.
- Existing office handoff still renders the legacy estimator.
- No horizontal overflow at the checked 320 px layouts; details in `overflow-320.json`.

## Differences from the previous implementation

The old estimator uses scalar room increments, broad postcode prefixes and demonstration settings. Those do not implement the November 10 proposal's separate price grids and approved suburb zones. The approved flow therefore uses a separate `pce_approved_pricing_v2` option and additive handlers in the same plugin. Legacy endpoints, settings, office flow and historical records remain available. No paid form plugin was purchased or introduced.

The original database has hierarchical service pages. Lookup supports those paths before top-level aliases, and the installer stores page IDs so later slug edits remain stable. The homepage template, pattern and styles are unchanged. The shared functions file only loads the new page module and registers menus on `init` to avoid early translation notices.

The screenshots' illustrative photography has been replaced with individual generated WebP assets, not crops of complete design sheets. Every such image remains labelled pending client-owned photography. Before/after panels, review areas and legal copy are explicit placeholders. No ratings, bond guarantee, credentials or insurance claims have been invented.

## Still requiring the client or target environment

- Approved grids, minimum, add-on/carpet rates and the actual suburb list. Empty approved settings currently produce custom quotes.
- Phone/WhatsApp/email/ABN, nominated Gmail, approved response wording, verified reviews, final service checklists and exclusions, business/team content and legal/cancellation wording.
- Client-owned photographs and an approved static service-area map.
- Mail transport configuration and real owner/customer inbox delivery; real WhatsApp app handoff after a number is supplied.
- MySQL/LocalWP verification, administrator CSV download/status interaction on that installation, and a physical iPhone Safari check. The isolated SQLite/browser checks do not substitute for those environments.
- Visual review with final copy and images. Text wrapping and page heights will change with approved content; the implementation follows the supplied composition, spacing and responsive behaviour without claiming an exact pixel identity.

No production submission, merge or deployment was performed. The existing repository workflow publishes `docs/` on a future push to `main`; this feature branch does not trigger that deployment.
