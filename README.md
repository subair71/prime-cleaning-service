# Prime Domestic & Cleaning Services — Melbourne

**Client review refinements (13 November 2026):** [Full-page screenshots, proposal checklist, test results and editing guide](docs/client-review/README.md). Feature branch only; no merge or production deployment.

**Previous content review (13 November 2026):** [November 12 implementation, screenshots, tests and editing guide](docs/nov12-review/README.md). This feature branch is for review only; see its outstanding client inputs and browser-QA limits. Do not use the older demo instructions below as launch approval.

A modern, professional WordPress website and interactive cleaning-price estimator built for **Derick’s business, Prime Domestic & Cleaning Services**, based in Malvern East (VIC 3145) and serving Melbourne's metropolitan and eastern suburbs.

**Homepage redesign (11 November 2026):** The existing Kadence child theme now includes the reference-inspired teal homepage and an editable Gutenberg pattern. See [implementation and WordPress setup notes](docs/homepage-redesign.md), the [visual preview](docs/homepage-preview/index.html), and the [desktop screenshot](docs/homepage-preview/prime-homepage-desktop.png). The existing estimator plugin and database are unchanged.

**Approved remaining pages (feature branch):** See the [implementation gallery and proposal checklist](docs/approved-pages/README.md), [LocalWP update steps](docs/approved-pages/LOCALWP.md) and [verification report](docs/approved-pages/QA.md). The approved estimator now uses separate price grids and approved suburb zones. Older setup/feature descriptions below describe the original demonstration implementation. For an existing site, follow the new update guide and do **not** import `database.sql`.

---

## 🌟 Highlights

- **Clean, Modern Premium Design:** Restrained navy, teal, and slate palette with high contrast, generous whitespace, and mobile-first responsiveness (320px+).
- **Custom Kadence Child Theme (`kadence-child`):** Extends the Kadence theme with design tokens, glassmorphic navigation, sticky header, customized footer, and iPhone Safari touch optimizations (preventing viewport auto-zoom with 16px+ inputs and 48px touch targets).
- **Custom Estimator Plugin (`prime-cleaning-estimator`):**
  - Shortcode: `[prime_cleaning_estimator]`
  - Multi-step interactive flow (Location &rarr; Service &rarr; Scope & Frequency &rarr; Live Estimate &rarr; Secure Enquiry).
  - Postcode pattern matching against Malvern East hub (demo prefixes: `314*`, `315*`) with friendly **manual quote fallback** for outside areas.
  - Separate commercial/office duration workflow (omits irrelevant residential room questions).
  - Server-side price recalculation (never trusts browser prices).
  - Anti-spam honeypot and timing defenses.
  - Frozen quote storage in custom `wp_pce_enquiries` database table.
  - Notification tracking via `wp_mail`.
- **WordPress Admin Dashboard (`Prime Estimator`):**
  - **Quote Enquiries:** Review customer quotes with frozen price snapshots and update follow-up statuses (`New`, `Contacted`, `Quoted`, `Closed`).
  - **Rates & Settings:** Real-time editable base rates, room increments, office hourly rates, frequency discounts, minimum charge floor, tax treatment disclaimer, and contact info.
- **Published Pages:**
  - `Home (/)` — Hero with trust metrics strip, service pillars, 3-step workflow, Melbourne suburb cloud, and FAQs.
  - `Services (/services/)` — Directory of cleaning offerings with provisional notices.
  - `Regular Home Cleaning (/services/regular-home-cleaning/)` — Routine domestic scope with recurring savings.
  - `Deep Cleaning (/services/deep-cleaning/)` — Restorative spring and detailed descaling checklists.
  - `End-of-Lease Cleaning (/services/end-of-lease-cleaning/)` — Agent-ready tenancy vacate specifications.
  - `Office Cleaning (/services/office-cleaning/)` — Commercial workspace, kitchenette, and washroom care.
  - `Get a Quote (/get-a-quote/)` — Interactive multi-step estimator widget.
  - `About (/about/)` — Neutral business overview with Malvern East operational base.
  - `Contact (/contact/)` — Direct contact cards, operating hours, and general enquiry form.
  - `Privacy Policy (/privacy-policy/)` — Draft compliant with Australian Privacy Principles (APPs).

---

## 📁 Repository Structure

```
prime-cleaning-service/
├── app/
│   └── public/
│       └── wp-content/
│           ├── themes/
│           │   ├── kadence/                  # Base parent theme
│           │   └── kadence-child/            # Custom child theme (styles, functions, footer)
│           └── plugins/
│               └── prime-cleaning-estimator/ # Custom multi-step estimator plugin
├── database.sql                              # Full MySQL database dump (pages, options, rates, enquiries)
├── .gitignore                                # Excludes core WordPress & local server configs
└── README.md                                 # Project documentation & setup instructions
```

---

## 🚀 Setup & Installation Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/subair71/prime-cleaning-service.git
   ```

2. **WordPress & Local Setup:**
   - Place `wp-content/themes/kadence-child` into your WordPress themes directory.
   - Place `wp-content/plugins/prime-cleaning-estimator` into your WordPress plugins directory.
   - Activate Kadence and **Kadence Child - Prime Cleaning**.
   - Activate the **Prime Cleaning Estimator** plugin (it will automatically create the `wp_pce_enquiries` database table).

3. **Database Import (Optional for 1-to-1 recreation):**
   - Import `database.sql` into your local MySQL database.
   - Run search-replace if using a different domain:
     ```bash
     wp search-replace "http://prime-cleaning-service.local" "http://your-domain.local"
     ```

4. **Access the Estimator Shortcode:**
   - Embed `[prime_cleaning_estimator]` on any page or use `/get-a-quote/`.

---

## 🔒 Security & Quality Standards

- **Server-Side Validation:** All quote submissions re-run calculation engine server-side before persisting to DB.
- **Spam Defenses:** Includes invisible honeypot field and submission timestamp thresholds.
- **Timezone Integrity:** Past preferred dates are blocked using the site's configured WordPress timezone.
- **Syntax Verified:** Zero syntax or lint errors across all PHP, CSS, and JS components (`php -l` verified).
- **Vanilla JavaScript:** 100% lightweight vanilla JS without React/Angular/jQuery dependencies.

---

## 📝 License & Attribution

Designed and developed for **Prime Domestic & Cleaning Services** (Melbourne, VIC).
All rights reserved.
