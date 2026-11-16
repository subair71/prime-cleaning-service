# Prime Domestic & Cleaning Services

Current work uses **WordPress.org, Kadence Free, native/Kadence blocks and Fluent Forms**. Every delivered page section is editable in the normal block editor. The custom theme/calculator from the earlier prototype is retained only as migration history and must not be activated as a substitute for the contracted native build.

The native migration from PR #7 is already merged. The follow-up branch `feature/prime-native-reconciliation` corrects the no-photo light-teal hero, imports the client logo, enables Kadence's desktop sticky header, aligns fresh-review URLs with the signed proposal and removes unresolved pricing/policy claims. It does not deploy to the client site.

**Launch is blocked:** Fluent Forms Pro feasibility, current-site backups/migration, final pricing/policies, native status/reference handling, fixed mobile actions, real reviews, emails and device/hosting acceptance remain incomplete.

- [Review package, exports and previews](docs/native-migration/README.md)
- [Client decisions and access required](docs/native-migration/DECISIONS.md)
- [Fluent Forms feasibility](docs/native-migration/ESTIMATOR.md)
- [Current verification](docs/native-migration/QA-RECONCILIATION.md)
- [Migration and rollback](docs/native-migration/MIGRATION.md)
- [Handover guide](docs/native-migration/HANDOVER.md)

A Git pull does not update a WordPress database. Use the fresh-review importer only on an empty protected review installation; use the existing-site migration procedure for real client data. Never restore this repository's historical `database.sql` over an existing site.

Earlier documents under `docs/approved-pages`, `docs/client-review`, `docs/nov12-review` and custom theme/plugin directories describe superseded implementations. Their approval labels and test results do not establish client approval or Fluent Forms Pro compliance.
