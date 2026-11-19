# Native WordPress migration — review milestone

The native page package includes the editable Request a Quote estimator and Derick’s approved prices. Follow the [staging installation and acceptance checks](ESTIMATOR.md) before publishing.

For an existing LocalWP site, [import the updated header and footer](HANDOVER.md#applying-the-header-and-footer-to-prime-in-localwp). See the footer on [mobile](screenshots/prime-header-footer-mobile.webp) and [desktop](screenshots/prime-header-footer-desktop.webp). See the [17 November page design and plugin review](ENHANCEMENT.md) for earlier layouts.

## What is implemented

- Ten enhanced agreed page layouts plus the preserved hidden Office draft, made from registered WordPress/Kadence blocks, with no Custom HTML blocks, custom shortcodes or project CSS/JS. Kadence Blocks is active and available in the visual editor; these layouts use responsive Kadence Row Layout/Section blocks and core blocks where those controls suffice.
- Plugin-managed Call/WhatsApp/Estimate mobile actions are configured in the isolated review; physical iPhone and active-link accessibility acceptance remain outstanding.
- Standard Kadence parent theme; native Customizer palette, typography, header button, desktop menu, mobile drawer and four footer widget areas. Menus and footer content are editable through WordPress.
- Client-supplied logo and existing service images copied into the Media Library; active pages no longer reference child-theme assets.
- Native Fluent Forms contact form, consent, required-field validation, entry storage, honeypot and confirmation. Its normal export is included. No custom bridge, tokens, hidden calculator payload or custom form CSS/JS.
- Blog uses the native Query Loop. Derick’s supplied Google review link is included; the genuine live review feed remains unconfigured. No invented reviews or sample ratings are published. Office and Terms originals remain drafts. The supplied approved Privacy copy is published in the clean review; existing-site publication states must be preserved during the real migration.
- The Prices page embeds the native Fluent Forms estimator. Free-only review installations use the phone and WhatsApp fallback.

Both the parent theme and native plugins work with the custom child theme and Prime estimator disabled. A second import test used an installation where those custom directories do not exist. The legacy files remain in this branch for rollback until the complete estimator/history replacements pass acceptance. Do not activate them as a workaround for missing Pro.

## Review and files

- [Component audit and differences](AUDIT.md)
- [Installation, existing-site migration and rollback](MIGRATION.md)
- [Estimator specification and blockers](ESTIMATOR.md)
- [Client handover draft](HANDOVER.md)
- [Validation and limitations](QA.md)
- [Native WordPress content export](exports/wordpress-content.xml)
- [Native quote-form export](exports/fluentforms-quote.json)
- [Native contact-form export](exports/fluentforms-contact.json)
- [Native quote-enquiry form export](exports/fluentforms-quote-enquiry.json)
- [Combined Fluent Forms export](exports/fluentforms-forms.json)

`pages/*.html` contains serialized **editable WordPress blocks**, not custom HTML sections. `exports/` contains native configuration, page/media manifests and the original image files. These files are necessary because a Git pull does not update the WordPress database.

The offline importer under `tools/` configures a fresh review site through WordPress and Fluent Forms APIs once. It is **not a plugin**, is never installed in the web root, and is not needed to render or edit the resulting site. It refuses production, indexed sites, existing content/forms/entries, and repeated imports. It does not activate or emulate Pro.

## Current portable previews

[Home](previews/home.html) · [Regular](previews/regular.html) · [End of Lease](previews/lease.html) · [Carpet](previews/carpet.html) · [Prices](previews/prices.html) · [About](previews/about.html) · [Contact](previews/contact.html) · [Blog](previews/blog.html) · [Privacy](previews/privacy.html). These are static renders of native WordPress output, not pages to paste into HTML blocks. Forms/scripts are disabled. See [preview limitations](previews/README.md).

## Historical screenshots

Desktop captures are full pages; mobile captures show the first 900 pixels of each 390px review frame. Frames use desktop Chromium, not iPhone Safari. Draft screenshots are disposable review copies; the original pages remain unpublished and the copies are excluded from the exports.

| Page | Desktop | Mobile |
|---|---|---|
| Home | [View](screenshots/home-desktop.jpg) | [View](screenshots/home-mobile.jpg) |
| Regular / Deep | [View](screenshots/regular-desktop.jpg) | [View](screenshots/regular-mobile.jpg) |
| End of Lease | [View](screenshots/lease-desktop.jpg) | [View](screenshots/lease-mobile.jpg) |
| Carpet | [View](screenshots/carpet-desktop.jpg) | [View](screenshots/carpet-mobile.jpg) |
| Prices | [View](screenshots/prices-desktop.jpg) | [View](screenshots/prices-mobile.jpg) |
| About | [View](screenshots/about-desktop.jpg) | [View](screenshots/about-mobile.jpg) |
| Contact | [View](screenshots/contact-desktop.jpg) | [View](screenshots/contact-mobile.jpg) |
| Blog | [View](screenshots/blog-desktop.jpg) | [View](screenshots/blog-mobile.jpg) |
| Privacy | [View](screenshots/privacy-desktop.jpg) | [View](screenshots/privacy-mobile.jpg) |
| Office draft | [View](screenshots/office-desktop.jpg) | [View](screenshots/office-mobile.jpg) |
| Terms draft | [View](screenshots/terms-desktop.jpg) | [View](screenshots/terms-mobile.jpg) |
