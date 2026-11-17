# Visual enhancement review — 17 November 2026

Implemented on `feature/prime-native-visual-enhancement` in a disposable WordPress installation. This is a working visual review and portable migration package, **not a production launch or completed estimator**. The intended hosting account and current client database were unavailable. No live pages, licences, customer records or messages were changed.

## Page changes

All ten agreed pages use the same teal/white palette, Poppins headings, readable dark text, responsive Kadence Row Layout/Section blocks, restrained borders and rounded cards. The existing Office page remains a separate hidden draft. Existing client-site IDs, slugs, states and newer copy must be reconciled before transfer.

| Page | Implemented appearance and behaviour |
|---|---|
| Home | Photo-free pale-teal hero; concise headline; quote fallback early on mobile; aligned trust and three service cards; clear process, supplied Google review link, areas, five FAQs and final contact action. No invented starting prices or reviews. |
| End of Lease | Consistent split introduction, cropped service image, compact inclusion/exclusion cards, readable pricing fallback, FAQs and quote action. |
| Regular House Cleaning | Matching service layout; Deep/Spring remains within Regular. Three-column comparisons become labelled cards on small screens. |
| Carpet Steam Cleaning | Matching introduction, image proportions, inclusion/exclusion presentation, FAQs and quote fallback. |
| Prices & Instant Estimate | Clear status and contact actions while approved native pricing is unavailable; restrained cards and mobile-readable explanations. It does not calculate estimates. |
| About | Refined introduction, consistent image and content spacing. Removed founder section stays out. |
| Contact & Service Areas | Clear contact cards and native Fluent Forms enquiry form with supported autocomplete, readable fields, validation and consent. Dates remain requests. |
| Blog | Consistent introduction and native Query Loop; no fabricated articles. Home latest-post section remains absent while approved posts are unavailable. |
| Privacy Policy | Supplied wording with narrower reading width, consistent headings and paragraph spacing. Hosting/font/privacy claims still need verification on the real host. |
| Terms | Native layout refined but remains draft pending approved wording. No public footer link to the draft. |

Header: desktop main row sticks; mobile header scrolls away. The mobile drawer has a white background, teal/dark text and larger controls. Footer has organised native widgets, Blog and Privacy links, with no street address, hours, availability or developer credit. Galleries remain unpublished.

## Native design settings

| Setting | Value |
|---|---|
| Accent / hover | `#087f80` / `#066667` |
| Headings / body | `#163d44` / `#263d42` |
| Section / border | `#edf8f6` / `#d6e5e3` |
| Content / legal reading width | 1180px / 960px |
| H1 desktop / tablet / mobile | 48 / 38 / 32px, Poppins 600 |
| H2 desktop / tablet / mobile | 32 / 28 / 26px |
| Body | 16px Inter, 1.6 line height |
| Section padding | 64 / 48 / 40px |
| Card padding | 24px desktop, 20px mobile |
| Cards / buttons | 12px / 8px corner radius |
| Service images | 4:3, cover crop, 12px radius |

These are saved through registered block attributes, theme settings and plugin exports. No custom runtime PHP, JavaScript, CSS, HTML sections, child theme or calculator bridge was added. The offline review importer is a one-time transfer tool outside the web root. It is not needed for rendering or everyday editing. Old prototype code remains inactive and retained for migration/history review; it must not be reactivated to fill missing native functionality.

## Plugin inventory and cost

| Component | Review version / cost | Purpose and decision |
|---|---|---|
| WordPress.org | 7.1.4; free | Native administration and content. |
| Kadence Free | 1.6.0; free | Parent theme, typography, palette, header/footer and responsive navigation. |
| Kadence Blocks | 3.9.0; free | Visual row/section spacing, responsive cards and layouts. No Pro block dependency. |
| Fluent Forms Free | 6.2.17; free | Contact enquiries only. Pro estimator is not supplied or simulated. |
| DS Mobile Menu | 1.1.0; free | Review candidate for Call / WhatsApp / Estimate bar with native visibility, safe-area and focus/keyboard handling. Native settings export included. |
| SQLite Database Integration | QA only; free | Disposable review database; not a proposed client production dependency. |
| Fluent Forms Pro | Not installed; plan/purchase deferred | Essential feasibility and licence access remain outstanding; see ESTIMATOR.md. Derick retains business ownership of any later licence. |

No purchase or new renewal commitment was made. No Formidable switch, page builder, review widget, SEO, SMTP, consent, backup, security or image plugin was added without the actual hosting/plugin inventory. The supplied genuine Google review link remains; a live reviews feed is outstanding.

The DS Mobile Menu [official listing](https://wordpress.org/plugins/devstack-mobile-menu/) was checked on 17 November. Its supported visual controls include links, labels, icons, sizing, colours, visibility and settings import/export. It requires WordPress 6.5+ / PHP 8.0+; the listing declares testing through 7.0.8, below the 7.1.4 review installation. The review tests passed locally, but this is a new plugin with 10+ active installations and no reviews. Treat it as a review candidate pending iPhone and accessibility acceptance, not an unconditional production recommendation. It adds vendor CSS/JS and WordPress Dashicons; Font Awesome, WooCommerce features and animations are disabled. Performance impact has not been measured on the selected host.

DiceBar was tried and deactivated because its normal footer timing prevented the tested focus-hide and page-space behaviour. WP Bottom Menu was inspected but not selected because the required safe-area/keyboard behaviour was not established. Neither plugin was patched. A DS limitation also remains: its automatic active-link matching can mark Call as the current page on Home; native configuration does not establish correct screen-reader semantics. Resolve through a supported vendor update or an agreed alternative before launch.

## Validation and limits

- **93 native assertions passed** on the updated review and again on a clean import. This includes native page/block registration, contact storage, no active custom calculator, mobile settings, and a programmatic block edit/save/restore. See `exports/enhancement-native-qa.json` and `enhancement-fresh-import-qa.json`.
- **776 page blocks and 18 footer blocks** parsed with zero invalid blocks using the installed WordPress/Kadence JavaScript definitions. This is serializer validation, not a client editor demonstration.
- Six specified global text/button colour pairs meet WCAG AA normal-text contrast; this is not a full rendered accessibility audit. See `exports/enhancement-contrast-qa.json`.
- All **nine public review pages** were visited in desktop Chromium and a 390 × 844 frame (375 CSS px content width due to its scrollbar). No horizontal overflow or loaded broken images were found. Terms remains draft and has serialization/runtime checks only.
- Browser checks passed for mobile menu/submenu, FAQ Enter toggle and visible focus, blank-form errors and a synthetic successful enquiry confirmation. The mobile action bar is 72px high, has full 12px labels, and hides while an input is focused. Contact controls measured 16px text and 46px height. Call/WhatsApp destinations were inspected; no calls or messages were sent.
- Native contact submission checks: five passed. External notifications were disabled. No inbox/SMTP delivery, production spam filtering or customer confirmation email is certified.
- A private copy of the **disposable** database and uploads passed SQLite integrity, logical-data, synthetic-enquiry and media-hash checks. This is not a client-site backup or a full MySQL restore exercise.
- Native local-font serving was enabled, but the review environment rejected the font download and its cached stylesheet still referenced Google-hosted font files. Verify local font files and network requests on hosting before relying on the privacy wording.
- Physical iPhone Safari, home-indicator inset, real on-screen keyboard, rotation, zoom, screen-reader semantics and consent-banner overlap remain untested. Desktop Chromium narrow frames do not prove these.
- PageSpeed and LCP have **not been measured** on the selected host. No 80+ / 2.5s pass is claimed.
- Complete Pro calculations, missing-rate behaviour, frozen historical breakdowns, statuses, references, CSV preservation and reference-prefilled WhatsApp are **not implemented or tested**. The existing approved calculation contract remains unchanged in ESTIMATOR.md. No missing rate is treated as zero.
- Derick's visual editing demonstration is still required, especially all routine price and suburb changes. The current native content is editable; a programmatic check is not owner acceptance.

## Review files

[Before desktop](screenshots/enhancement/before-desktop.jpg) · [After desktop](screenshots/enhancement/after-desktop.jpg) · [After mobile](screenshots/enhancement/after-mobile.jpg) · [Full homepage](screenshots/enhancement/home-full.jpg)

| Page | Desktop | Mobile |
|---|---|---|
| Regular / Deep | [View](screenshots/enhancement/regular-desktop.jpg) | [View](screenshots/enhancement/regular-mobile.jpg) |
| End of Lease | [View](screenshots/enhancement/lease-desktop.jpg) | [View](screenshots/enhancement/lease-mobile.jpg) |
| Carpet | [View](screenshots/enhancement/carpet-desktop.jpg) | [View](screenshots/enhancement/carpet-mobile.jpg) |
| Prices | [View](screenshots/enhancement/prices-desktop.jpg) | [View](screenshots/enhancement/prices-mobile.jpg) |
| About | [View](screenshots/enhancement/about-desktop.jpg) | [View](screenshots/enhancement/about-mobile.jpg) |
| Contact | [View](screenshots/enhancement/contact-desktop.jpg) | [View](screenshots/enhancement/contact-mobile.jpg) |
| Blog | [View](screenshots/enhancement/blog-desktop.jpg) | [View](screenshots/enhancement/blog-mobile.jpg) |
| Privacy | [View](screenshots/enhancement/privacy-desktop.jpg) | [View](screenshots/enhancement/privacy-mobile.jpg) |

These are real WordPress browser captures, not generated design images. The “before” is the previous native repository review, not a capture of the unavailable production website. See HANDOVER.md for exact native editing locations and MIGRATION.md for protected-staging transfer. Access to the intended hosting/WordPress installation, a licensed Pro test environment and approved price/suburb/legal decisions are needed to continue. Launch approval remains required after acceptance.
