# Changelog

All notable changes to the Tuifan child theme are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-10-14

Initial release. Child theme of Nextora, targeting WordPress 6.6+ (`theme.json` v3) and PHP 8.1+.

### Added

**Blocks** (all dynamic, prefix `tuifan/`, inserter category "Tuifan Blocks"):

- `tuifan/entry-list` — numbered terminal entry rows with meta, excerpt, tags and reading time. `source: main` reuses the real main query on blog, archive and search views; `source: latest` runs its own query paginated through the `tuifan_page` query var. Server-rendered range and previous/numbered/next pagination.
- `tuifan/archive-controls` — server-rendered GET search field and category filter links that preserve the current search term and reset pagination.
- `tuifan/reading-time` — reading time computed at 200 words per minute (minimum 1) for the post in block context; renders nothing without a valid post context.
- `tuifan/signal-visual` — static SVG lensed-shadow composition enhanced by a local Three.js ray-bending canvas; honours `prefers-reduced-motion`, pauses when offscreen or hidden, and keeps the static figure when WebGL is unavailable.
- `tuifan/form-template` — presentation-only contact and newsletter template: no `<form>` element, every control disabled, notice always visible, nothing sent or stored.

**Templates, parts and patterns**:

- Templates: `front-page`, `page`, `page-full-width`, `about-us`, `contact-us`, `home`, `archive`, `index`, `search`, `single`, `404`.
- Template parts: `header`, `footer`, `footer-newsletter`, `sidebar`, `comments`.
- Patterns: `tuifan/home`, `tuifan/about`, `tuifan/contact`, `tuifan/archive`, `tuifan/search`, `tuifan/not-found`, in a "Tuifan" pattern category.
- Content helpers in `inc/content.php`: reading time, entry rows, server-rendered pagination, archive/search category filters, and main-query configuration (2 posts per page on blog, archive and search; searches limited to the `post` post type).

**Design tokens and presets**:

- `theme.json` v3: OKLCH palette (`base`, `surface`, `contrast`, `paragraph`, `border`, `primary`, `primary-hover`, `accent-text`, `on-accent`, `hover`, `secondary`, `warning`, `danger`, `transparent`), eight font sizes, nine spacing sizes, `contentSize` 760px, `wideSize` 1376px, zero root padding.
- IBM Plex Mono 300/400/500 self-hosted in `assets/fonts/ibm-plex-mono/` with the SIL OFL licence bundled as `OFL.txt`.
- Style variations: `styles/terminal.json` ("Tuifan Terminal"), `styles/color-scheme/terminal-purple.json` ("Terminal Purple"), `styles/font-preset/terminal-mono.json` ("Terminal Mono").
- Design layers: `assets/css/theme.css` (front end and editor canvas) and `assets/css/editor.css` (editor-canvas parity).

**Starter-site seeder**:

- `wp tuifan setup` (WP-CLI only; never runs on activation or on web requests). Exports a database backup outside the theme first, then creates or reuses the pages `home`/`about`/`contact`/`blog`, the categories "Getting started", "Safety" and "Workflows", the four seeded guides (`agent-start`, `agent-permissions`, `agent-context`, `agent-evaluation`) with comments closed, the `Tuifan Primary` `wp_navigation` entity, a Tuifan header template part referencing it, and sets `show_on_front` / `page_on_front` / `page_for_posts`.
- Repeat runs are idempotent: created IDs are stored in the `tuifan_starter_ids` option, existing pages, posts and terms are never overwritten, and a customized header part is preserved.

**Front-end behaviour** (`resources/ts/site.ts` → `assets/js/site.js`):

- `/` focuses the search field or navigates to the search view, ignored inside inputs, textareas, selects, `contenteditable` and for modified or composing keystrokes.
- Code-copy buttons for article code blocks, announcing "Copy unavailable. Select the text to copy." when the Clipboard API is unavailable.
- Instant (non-animated) table-of-contents jumps only when the visitor requests reduced motion.

**Build pipeline**:

- `scripts/build-blocks.mjs` (esbuild 0.25.0) emits `blocks/*/index.js` + `index.asset.php`, `blocks/signal-visual/view.js` + `view.asset.php`, and `assets/js/site.js`; `npm run typecheck` runs `tsc --noEmit` in strict mode.
- `@wordpress/*` imports are externalised to `window.wp.*` and JSX compiles to `window.wp.element.createElement`, so React is not bundled. Generated JS is committed and must never be hand-edited.

**Developer tooling**:

- `phpcs.xml.dist` plus dev-only Composer dependencies for PHP_CodeSniffer with the `WordPress`, `WordPress-Core`, `WordPress-Docs`, `WordPress-Extra` and `PHPCompatibilityWP` rulesets, the `tuifan` global prefix requirement and the `tuifan` text domain. Exposes `composer phpcs` and `composer phpcbf`.
- `tests/integration-checks.php` (`wp eval-file tests/integration-checks.php`) covering combined search + category pagination, reading-time word boundaries, seeder repeat behaviour and the presentation-only form templates; creates and removes its own rows and exits with status 1 on failure.

### Notes

- Requires the Nextora parent theme; the parent's optional public colour/font switcher stays disabled (it loads only when the `NEXTORA_COLOR_SCHEME_SWITCHER` constant is `true`) and is not required.
- Intentional deviations from the design reference: article body copy is 15px / 1.85, the contact and newsletter forms are presentation-only templates, and the footer `404` link intentionally points at the designed not-found view.
- The child theme strips the parent prose hook classes (`entry-content`, `wp-block-post-content`) from the `core/post-content` wrapper and owns its own typography instead.
- Licensing: theme GPL-2.0-or-later; bundled IBM Plex Mono fonts under SIL OFL 1.1; Three.js (MIT) is bundled into the signal-visual view script.
