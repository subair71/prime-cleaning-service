# Prime Domestic & Cleaning Services

Current work uses **WordPress.org, Kadence Free, native/Kadence blocks and Fluent Forms**. Every delivered page section is editable in the normal block editor. The custom theme/calculator from the earlier prototype is retained only as migration history and must not be activated as a substitute for the contracted native build.

The current enhancement branch `feature/prime-native-visual-enhancement` refines all ten agreed page layouts, native Kadence typography/spacing, mobile navigation, footer and contact form. A plugin-managed mobile action bar is configured in an isolated review. Native exports, before/after screenshots and a clean-import test are included. It does not deploy to the client site.

**Launch is blocked:** complete Fluent Forms Pro estimator, client-site backups/migration, approved rates/policies, historical status/reference handling, real reviews, email delivery and physical-device/hosting acceptance remain incomplete. See the [current enhancement review](docs/native-migration/ENHANCEMENT.md) for implemented changes and limitations.

- [Review package, exports and previews](docs/native-migration/README.md)
- [Client decisions and access required](docs/native-migration/DECISIONS.md)
- [Fluent Forms feasibility](docs/native-migration/ESTIMATOR.md)
- [Current verification](docs/native-migration/ENHANCEMENT.md#validation-and-limits)
- [Migration and rollback](docs/native-migration/MIGRATION.md)
- [Handover guide](docs/native-migration/HANDOVER.md)

A Git pull does not update a WordPress database. Use the fresh-review importer only on an empty protected review installation; use the existing-site migration procedure for real client data. Never restore this repository's historical `database.sql` over an existing site.

Earlier documents under `docs/approved-pages`, `docs/client-review`, `docs/nov12-review` and custom theme/plugin directories describe superseded implementations. Their approval labels and test results do not establish client approval or Fluent Forms Pro compliance.
