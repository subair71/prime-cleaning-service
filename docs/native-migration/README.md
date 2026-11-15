# Native WordPress migration — review milestone

**Not ready to merge, deploy or hand over.** The page/layout migration is implemented and verified in isolated WordPress installations. The complete estimator is **not implemented**: licensed Fluent Forms Pro, approved rates/suburbs and the actual installation/database were not available. This supersedes the earlier Free-plugin/custom-calculator bridge, which does not meet the client’s contract.

Branch: `feature/prime-fluent-forms-builder` · Draft PR: #7 · 15 November 2026.

## What is implemented

- Eleven page layouts made from registered WordPress blocks, with no Custom HTML blocks, custom shortcodes or project CSS/JS. Kadence Blocks is active and available in the visual editor; these layouts use core blocks where those controls suffice.
- Standard Kadence parent theme; native Customizer palette, typography, header button, desktop menu, mobile drawer and four footer widget areas. Menus and footer content are editable through WordPress.
- Existing service images copied into the Media Library; active pages no longer reference child-theme assets.
- Native Fluent Forms contact form, consent, required-field validation, entry storage, honeypot and confirmation. Its normal export is included. No custom bridge, tokens, hidden calculator payload or custom form CSS/JS.
- Blog uses the native Query Loop. Genuine review content was not supplied; no invented reviews or sample ratings are published. Office and Terms originals remain drafts. The supplied approved Privacy copy is published in the clean review; existing-site publication states must be preserved during the real migration.
- Clear unavailable-estimator message and phone/WhatsApp quote actions in the review. This is a visible **temporary gap**, not an estimator replacement. Instant-calculation claims were softened where necessary; original approved wording remains in Git history.

Both the parent theme and native plugins work with the custom child theme and Prime estimator disabled. A second import test used an installation where those custom directories do not exist. The legacy files remain in this branch for rollback until the complete estimator/history replacements pass acceptance. Do not activate them as a workaround for missing Pro.

## Review and files

- [Component audit and differences](AUDIT.md)
- [Installation, existing-site migration and rollback](MIGRATION.md)
- [Estimator specification and blockers](ESTIMATOR.md)
- [Client handover draft](HANDOVER.md)
- [Validation and limitations](QA.md)
- [Native WordPress content export](exports/wordpress-content.xml)
- [Native contact-form export](exports/fluentforms-contact.json)

`pages/*.html` contains serialized **editable WordPress blocks**, not custom HTML sections. `exports/` contains native configuration, page/media manifests and the original image files. These files are necessary because a Git pull does not update the WordPress database.

The offline importer under `tools/` configures a fresh review site through WordPress and Fluent Forms APIs once. It is **not a plugin**, is never installed in the web root, and is not needed to render or edit the resulting site. It refuses production, indexed sites, existing content/forms/entries, and repeated imports. It does not activate or emulate Pro.

## Screenshots

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
