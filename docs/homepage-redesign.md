# Prime homepage redesign — 11 November 2026

The existing Kadence child theme now renders the reference-inspired homepage: white navigation, photographic hero, compact estimator entry card, light teal trust strip, four existing cleaning services, benefits, an explicitly labelled review placeholder, and teal booking banner. Poppins and Inter are served locally. No additional page builder or premium plugin is required.

## Changed files

All production changes are inside `app/public/wp-content/themes/kadence-child/`:

| File | Purpose |
| --- | --- |
| `functions.php` | Loads the homepage module; retains earlier styling on other pages. |
| `front-page.php` | Uses the new homepage by default and supports saved Gutenberg content. |
| `inc/prime-homepage.php` | Registers the editable pattern, icons, compact estimator shortcode, homepage menu, assets and responsive image sources. |
| `patterns/prime-homepage.php` | Editable core Gutenberg groups, headings, paragraphs, image, buttons and shortcodes. |
| `template-parts/prime-home-header.php` | Homepage branding and accessible navigation. |
| `footer-prime-home.php` | Homepage footer without invented contact details. |
| `prime-homepage.css` | Scoped responsive design and editor styling. |
| `assets/js/prime-homepage.js` | Menu toggle, Escape/outside-click handling and office field presentation. |
| `assets/js/prime-estimator-handoff.js` | Prefills the existing estimator's service and bedroom fields through their existing change events. |
| `assets/fonts/` | Local Inter and Poppins files, including OFL licences. |
| `assets/images/` | Responsive illustrative hero imagery and lightweight button icons. |

`docs/homepage-preview/` contains a static export of the WordPress-rendered design plus a desktop screenshot. Its estimator is explicitly disabled because GitHub Pages does not run WordPress/PHP. The existing `docs/index.html` preview is preserved. The existing Pages workflow can publish the new preview directory.

## Apply in WordPress

1. Pull this commit into the existing WordPress installation, or deploy the complete updated `kadence-child` directory. Keep **Kadence Child - Prime Cleaning** active.
2. Keep the existing **Home** page selected under **Settings → Reading → Homepage**. The repository's database export already has this setting. The new `front-page.php` displays the redesign immediately, without importing a database or overwriting existing page content.
3. For Gutenberg editing, open **Pages → Home**, preserve the old content as a revision if desired, then replace the old blocks with **Patterns → Prime Cleaning → Prime — Cleaning Homepage** and save. Keep the outer group's `prime-home` class. This makes saved Home blocks the source of the page instead of the default theme pattern. Text, links, service descriptions, image and buttons can then be edited normally. The estimator and icons are small theme shortcodes.
4. The default homepage menu contains the requested six links. To customise it, assign a menu to **Appearance → Menus → Homepage Navigation**. Existing primary/mobile menus remain available on other pages.
5. Clear any WordPress/server page cache after deploying. No plugin installation, database migration or pricing configuration is required for this design.

The hero is generated illustrative photography, not a claim that the person pictured is a member of staff. It can be replaced through the Image block after inserting the pattern. The review area intentionally has no names, ratings or invented feedback; replace its clearly labelled Lorem Ipsum only with approved content.

## Estimator integration

The compact card sends only `prime_service` and, for residential services, `prime_bedrooms` to the published page containing `[prime_cleaning_estimator]`. The adapter accepts the four existing service keys and the existing UI's bedroom options (1–5 and 6+). Office cleaning keeps the existing office-hours workflow. Location, bathrooms, frequency, availability and enquiry details remain in the existing multi-step form.

**Calculate Price** continues into that form; it does not claim a price from incomplete inputs. Its note explains that location and further details are needed. The existing controller, rate settings, calculation class, nonce, AJAX endpoint, server validation, enquiry storage and notifications are unchanged. If the estimator plugin or published shortcode page is missing, the card becomes a labelled, disabled demo. Query-based/plain WordPress permalinks are supported.

## Verification

- Loaded the repository's WordPress core, Kadence child theme, Kadence Blocks, Calculated Fields Form and Prime Cleaning Estimator in an isolated temporary SQLite-backed test installation; rendered the real homepage and quote templates.
- Validated all **99 blocks** in the homepage pattern with WordPress block parsing: no invalid blocks.
- PHP syntax checks passed for all six changed/new PHP files; JavaScript syntax and CSS parsing passed; `git diff --check` passed.
- Verified desktop rendering and tablet/phone iframe viewports at **768, 390 and 320 px**, including no horizontal overflow, one-column phone service cards, menu opening and Escape closing.
- Followed the homepage's **Deep Cleaning / 4 bedrooms** selection into the existing estimator and completed its calculation step using its existing sample settings. Verified the office handoff hides residential controls and preserves the office workflow.
- Tested **30 service/size combinations**, including invalid query values, with the real unchanged estimator controller; verified plain-permalink query preservation and the missing-plugin demo fallback in WordPress.
- Confirmed one H1, labelled controls, image alt text, loaded imagery, 16 px select text, visible focus states and touch-sized primary controls.
- Confirmed no changes to the estimator plugin, `database.sql`, existing page content, original footer, existing configuration or other page templates.

Native iPhone Safari and Android devices were not available; browser checks used Chromium at responsive widths. Live email delivery and production quote submission were not exercised. The temporary test database and runtime dependencies are not part of the repository.

## Asset provenance

Hero files: `assets/images/prime-cleaning-hero*.webp`. Generated specifically for this homepage using the supplied reference's photographic composition: bright Melbourne-style living room, beige sofa, teal cushions, professional cleaner in plain teal clothing, subject at approximately 60% width, wide composition with space for copy and estimator. No text, logos or claims were generated into the image. Optimised responsive WebP derivatives are included. Fonts are redistributed under their included SIL Open Font Licences.
