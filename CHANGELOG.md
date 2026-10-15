# Changelog

All notable changes to the Tuifan child theme are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- `.wp-block-group.tuifan-article` grid is now fluid: `grid-template-columns: minmax(0, 1fr) minmax(240px, 300px)` replaces the fixed `minmax(0, 760px)` content track, so the reading column fills the shell instead of stopping at 760px (1004px at a 1440px viewport).
- `.wp-block-tuifan-form-template` no longer caps itself at `max-width: 760px`; the block fills its grid column (1044px in the contact pattern at a 1440px viewport).
- Article and page paragraphs fill their column: the `max-width: 65ch` cap on `.tuifan-prose p` / `li` and `.tuifan-article__body p` / `li` is gone, so body copy now runs the full reading column (1004px, ~75–107 characters per line on `/agent-context/` at 1440px, against 585px before). `overflow-wrap: break-word` still prevents overflow at narrow widths, and the other measures in the sidebar-free text blocks are unchanged (`p.tuifan-lede` 60ch, `p.tuifan-entry__excerpt` 65ch, `p.tuifan-hero__lede` 48ch, `p.tuifan-newsletter__description` 52ch, `.tuifan-page-heading h1` 23ch).
- Single-post spacing: `.wp-block-group.tuifan-article` drops `margin-bottom: 48px` for `padding-bottom: 3rem` (the margin was already inert — WordPress global styles zero `margin-block-end` on flow-layout children — so this adds a real 48px below the grid), `.wp-block-group.tuifan-article__feature` gets a 2.5rem bottom margin (was a dead `36px`, zeroed by the same reset, hence the block class in the selector), and `.tuifan-next-article` drops `min-height` from 100px to 1rem, so the previous/next rows size to their content (131px → 56.5px each).
- Two more `min-height` resets: `a.tuifan-command-link` (home hero) drops `48px` (75px → 49.1px tall, so the row sizes to its text) and `.tuifan-directory li a` drops `44px` (64px → 40.4px). The parent list item `.tuifan-directory li` keeps its own `min-height: 44px`, so the rows stay 44px tall with the anchor centred inside.
- Comment separators moved onto the body: `.wp-block-group.tuifan-comment` drops `border-bottom` (padding stays `20px 0`), so consecutive comments are now divided by the `.wp-block-comment-content` box's own 1px top/bottom borders. The reply link is right-aligned (`.wp-block-comment-reply-link { text-align: right }`, flush to the container edge for both the top-level and the nested comment) and set to `0.8rem` (`.comment-reply-link`, measured 15px → 12.8px).
- Comment bodies get their own rule: `.tuifan-comment .wp-block-comment-content` is `padding: 1rem 0`, `margin: 1rem 0` and `border: solid; border-width: 1px 0` (measured 16px/16px, 1px top and bottom in `currentColor`, no side borders). The selector carries `.tuifan-comment` because WordPress global styles zero `margin-block` on flow-layout children — a bare `.wp-block-comment-content` ties with `:root :where(.is-layout-flow) > *` and loses on load order.
- 404 surfaces: `.tuifan-terminal pre` sets `border: none`, removing the 1px frame the parent theme's `theme.json` puts on `core/preformatted` (the child's type selector outranks the generated `:root :where(.wp-block-preformatted)` rule); the panel's own top/bottom border and the 24px `pre` padding stay. The outline button in the same pattern regains the core padding compensation, `padding: calc(0.667em + 2px) calc(1.333em + 2px)` (measured 8.671px × 17.29px → 10.671px × 19.329px at the 13px button font, 185.8px × 38.9px box), which the global-styles `:root :where(.wp-block-button …)` rules were overriding with the uncompensated `0.667em 1.333em`.
- Table frame colour now follows the text colour: `theme.json` `styles.blocks.core/table.border.color` is `initial` (was `var(--wp--preset--color--border)`), so the generated `:root :where(.wp-block-table > table)` rule emits `border-color: initial`. The frame resolves to `currentColor` (`oklch(0.91 0.008 250)`, was `oklch(0.33 0.008 250)`), matching the cells' own top/left borders.
- Archive filters are denser and spaced on both sides: `.tuifan-filters` is `margin: 18px 0` (was `18px 0 0`), so the gap to the entry grid below is 18px (was 0), and `.tuifan-filter` drops `min-height: 44px` (42.4px per chip, was 66px; the row is 42.4px at ≥600px, 92.8px wrapped at 430px). The neighbouring `.wp-block-tuifan-archive-controls` bottom margin stays inert — WordPress global styles zero it.
- Tighter controls and tables: `.tuifan-article__sidebar .nextora-toc__link` and `.tuifan-nav .wp-block-navigation-item__content` drop `min-height: 44px` (measured 60px → 33.4px per TOC row, 66px → 45.8px per nav item; both are content-box, so the 44px was adding to the padding), table cells drop `border-bottom` / `border-right`, and `.tuifan-page-heading h1` drops `max-width: 23ch` and gains `margin-bottom: 0.5rem` (title 607px → 850px wide, 8px gap to the excerpt). Cell borders need explicit `0` because core ships `.wp-block-table td, .wp-block-table th { border: 1px solid }`; the cells' top and left borders remain, so the collapsed grid still reads as a grid in the text colour.
- Sidebar table of contents is flush left: `.tuifan-article__sidebar .nextora-toc__list` drops its 12px bottom padding, `.tuifan-article__sidebar .nextora-toc__item` drops the parent's `0.25rem 0 0.25rem 0.75rem` padding, and `.nextora-toc__item--depth-0` / `--depth-1` drop the parent's `0.75rem` / `1.75rem` `padding-left`. Nesting depth is still shown by the sub-list border and its `18px`/`10px` inset.
- Active table-of-contents entry no longer uses the parent's left-border accent: `.tuifan-article__sidebar .nextora-toc__item` sets `border-left: 0` (the parent paints the accent into that border through an inline style, so a zero width is what removes it) and the active label is coloured with `--wp-preset--color--accent-text` on a `--hover` background instead. Because the parent adds `is-active` only under `prefers-reduced-motion`, `installTocCurrentItem()` in `resources/ts/site.ts` mirrors the parent's scroll-spy onto `tuifan-toc__item--current` for the animated path; both selectors share the rule.

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
