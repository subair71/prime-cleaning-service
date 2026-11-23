# Prime Domestic & Cleaning Services

The staging site uses **WordPress.org, Kadence, Kadence Blocks, GenerateBlocks and Fluent Forms Pro**. Every delivered page section is editable in the normal block editor. The custom theme/calculator from the earlier prototype is retained only as migration history and must not be activated as a substitute for the contracted native build.

The [approved page titles and descriptions](docs/native-migration/exports/wordpress-seo.csv) are saved in Rank Math SEO Free on staging. Edit them through each page’s search snippet. The Terms metadata belongs to its draft. Staging uses noindex. Review indexing, canonical URLs and the sitemap on the production domain at launch.

The [mobile staging design](docs/native-migration/exports/Prime-Mobile-Staging-Design.zip) contains the responsive page layouts and the deployed three-step Fluent Forms Pro estimate. Its README covers restoration and maintaining prices across the mobile, desktop and quick estimates.

The [enhanced ten-page staging design](docs/native-migration/exports/Prime-Staging-Design.zip) contains the editable WordPress layouts, Prime’s original round crest references, and an in-place staging update command. Open the README in the ZIP for the upload procedure and estimator details.

The [22 November staging homepage and its restoration package](docs/native-migration/HOME.md) include the original logo, the live Home-to-Prices estimator and both Google review widgets.

The earlier enhancement branch `feature/prime-native-visual-enhancement` refines all ten agreed page layouts, native Kadence typography/spacing, mobile navigation, footer and contact form. A plugin-managed mobile action bar is configured in an isolated review. Native exports, before/after screenshots and a clean-import test are included. It does not deploy to the client site.

The native **Request a Quote** estimator uses Derick’s completed pricing sheet. The homepage and Prices page connect to the form. [Import the form on staging and check the rates](docs/native-migration/ESTIMATOR.md). The staging homepage uses genuine Google reviews and the licensed estimator. Production migration, policy approvals, historical references, email delivery and physical-device acceptance are separate from this staging export. See the [current enhancement review](docs/native-migration/ENHANCEMENT.md) for implemented changes and limitations.

- [Review package, exports and previews](docs/native-migration/README.md)
- [Client decisions and access required](docs/native-migration/DECISIONS.md)
- [Estimator setup and price editing](docs/native-migration/ESTIMATOR.md)
- [Current verification](docs/native-migration/ENHANCEMENT.md#validation-and-limits)
- [Migration and rollback](docs/native-migration/MIGRATION.md)
- [Handover guide](docs/native-migration/HANDOVER.md)

A Git pull does not update a WordPress database. Use the fresh-review importer only on an empty protected review installation; use the existing-site migration procedure for real client data. Never restore this repository's historical `database.sql` over an existing site.

Earlier documents under `docs/approved-pages`, `docs/client-review`, `docs/nov12-review` and custom theme/plugin directories describe superseded implementations. Their approval labels and test results do not establish client approval or Fluent Forms Pro compliance.
