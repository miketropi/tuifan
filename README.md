# Tuifan

Tuifan is a Gutenberg block theme for WordPress, built as a child of the [Nextora](https://beplusthemes.com/) parent theme. It renders a terminal / reader-guide aesthetic on a near-black shell: IBM Plex Mono typography, numbered entry rows, OKLCH design tokens, and five dynamic blocks (`tuifan/*`) that render entirely on the server. All layout and colour decisions live in `theme.json` and the design layer in `assets/css/theme.css`, so blocks stay thin and the site works without JavaScript. Forms are presentational by design, and the starter content is installed only on demand through WP-CLI.

![Tuifan home page: terminal shell with the “less noise. more signal.” hero, the lensed-shadow visual, numbered entry rows and the live sidebar](https://pub-0645c3b9d3674132af6b362484df0f3c.r2.dev/tuifan/preview.webp)

- **Version:** 1.0.0
- **Template:** `nextora` (child theme)
- **Text domain:** `tuifan`
- **License:** GPL-2.0-or-later

## Requirements

| Requirement | Value | Source |
| --- | --- | --- |
| WordPress | 6.6 or newer (`theme.json` schema v3) | `style.css` (`Requires at least: 6.6`) |
| PHP | 8.1 or newer | `style.css` (`Requires PHP: 8.1`) |
| Parent theme | Nextora, installed and available | `style.css` (`Template: nextora`) |
| Node.js | 18 or newer — build only | `node_modules/esbuild` (`engines.node: >=18`) |
| WP-CLI | optional — starter-site seeder and integration checks | `inc/starter-site.php`, `tests/integration-checks.php` |
| Composer | optional, dev only — PHP CodeSniffer tooling | `composer.json` |

Notes:

- The parent Nextora theme must be present; Tuifan never re-requires or re-registers parent files.
- The parent's guest-facing colour/font switcher loads only when the `NEXTORA_COLOR_SCHEME_SWITCHER` constant is defined as `true` (`inc/features/color-scheme-switcher/load.php` in the parent). Tuifan does not define it, so the switcher stays off. Tuifan does not depend on it.
- Node.js is required only to rebuild `blocks/*/index.js`, `blocks/*/view.js` and `assets/js/site.js`. Those files are committed, so a running site needs no build step.
- Composer is required only for `vendor/bin/phpcs`. `node_modules/` and `vendor/` are development dependencies and are never loaded by WordPress at runtime.

## Install & activate

```bash
# 1. Place the child theme next to its parent
#    wp-content/themes/nextora  (parent)
#    wp-content/themes/tuifan   (this theme)

# 2. Activate the child theme
wp theme activate tuifan

# 3. Install the starter site (optional, WP-CLI only)
wp tuifan setup
```

On a [Local](https://localwp.com/) site, WP-CLI needs the site environment for database access:

```bash
source '/Users/hieuhuynh/Local Sites/dev/app/.envrc'
wp theme status tuifan     # expect: Status: Active, Version: 1.0.0
```

Activating the theme runs no content changes: `wp tuifan setup` is the only path that creates pages or posts, and it never runs on activation or on a web request.

## Design tokens & presets

### `theme.json`

| Area | Value |
| --- | --- |
| Schema | `version: 3`, `appearanceTools: true` |
| Layout | `contentSize: 760px`, `wideSize: 1376px` |
| Root padding | zero (`useRootPaddingAwareAlignments: false`) |
| Font family | `ibm-plex-mono` → `"IBM Plex Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace` |
| Font sizes | `small` 12px, `base` 15px, `medium` 15px, `medium-plus` 18px, `large` 22px, `x-large` 32px, `xx-large` 44px, `hero` `clamp(46px, 6.7vw, 112px)` |
| Spacing sizes | `05` 8px, `10` 16px, `15` 20px, `20` 24px, `30` 32px, `40` 48px, `50` 64px, `60` 80px, `70` 96px |
| Gradients | disabled (`gradients: []`, `defaultGradients: false`, `defaultPalette: false`) |
| Base typography | 400 weight, `line-height: 1.7` |
| Headings | 300 weight, `letter-spacing: -0.02em`; h4 500 weight |

Palette slugs: `base`, `surface`, `contrast`, `paragraph`, `border`, `primary`, `primary-hover`, `accent-text`, `on-accent`, `hover`, `secondary`, `warning`, `danger`, `transparent`.

### Fonts

IBM Plex Mono 300/400/500 is self-hosted and declared in `theme.json` (`fontFace`, `fontDisplay: swap`) from:

```
assets/fonts/ibm-plex-mono/300.ttf
assets/fonts/ibm-plex-mono/400.ttf
assets/fonts/ibm-plex-mono/500.ttf
assets/fonts/ibm-plex-mono/OFL.txt
```

Licence: SIL Open Font License 1.1 (see `OFL.txt`).

### Presets (Site Editor → Styles)

| File | Title | Kind |
| --- | --- | --- |
| `styles/terminal.json` | Tuifan Terminal | full site style variation |
| `styles/color-scheme/terminal-purple.json` | Terminal Purple | colour scheme variation |
| `styles/font-preset/terminal-mono.json` | Terminal Mono | font variation (`body`/`heading` → `ibm-plex-mono`) |

WordPress scans `styles/` recursively, so all three files appear in the Site Editor regardless of their subdirectory. The parent's optional public switcher is not needed to select them.

### Style layers

| File | Scope |
| --- | --- |
| `assets/css/theme.css` | Front end and block editor canvas; enqueued as `tuifan-theme` on `enqueue_block_assets` |
| `assets/css/editor.css` | Editor canvas parity overrides; enqueued as `tuifan-editor` and registered through `add_editor_style()` |

Both enqueue after `nextora-app` (or `global-styles`) when that handle is registered.

## Blocks

All five blocks are dynamic (`render.php`), prefix `tuifan/`, category **Tuifan Blocks** (`tuifan` category added via `block_categories_all`). Sources live in `blocks/<slug>/` with `block.json`, `index.tsx`, `edit.tsx`, `render.php`, `style.css` (plus `view.ts` for the signal visual).

| Block | Purpose | Key attributes |
| --- | --- | --- |
| `tuifan/entry-list` | Numbered terminal entry rows with meta, excerpt, tags, reading time, server-rendered pagination | `source` (`latest` \| `main`, default `latest`), `postsPerPage` (number, default 2, clamped 1–20), `heading` (string, default `~/posts — latest entries`) |
| `tuifan/archive-controls` | Server-rendered GET search field plus category filter links that preserve the current search term and reset pagination | none (block has no attributes) |
| `tuifan/reading-time` | Computed reading time in minutes for the post in block context | none; reads `postId` / `postType` via `usesContext` |
| `tuifan/signal-visual` | Static SVG lensed-shadow composition, enhanced by a local Three.js ray-bending canvas | `label` (string, default `Accretion field`), `enableMotion` (boolean, default `true`) |
| `tuifan/form-template` | Presentation-only contact/newsletter template | `variant` (`contact` \| `newsletter`, default `contact`), `buttonLabel` (string, default empty), `notice` (string, default `Template preview only. Nothing is sent or stored.`) |

### `tuifan/entry-list`

- `source: main` reuses the already-fetched main query on blog, archive and search views (falls back to its own query inside the editor preview and REST requests).
- `source: latest` runs its own published-post query, paginated through the `tuifan_page` query var and scoped to the front page.
- Pagination is rendered on the server as a range readout (`01–02 / 04 entries`, `role="status"`) followed by previous / numbered / next controls with disabled placeholders at the ends.
- Empty states cover out-of-range pages, empty search results and empty archives.

### `tuifan/archive-controls`

A real `role="search"` GET form posting to the site root with a hidden `post_type=post` field (and a hidden `cat` when a category is active), plus an "All" filter and one link per non-empty category. On search views the filter links keep the search term.

### `tuifan/reading-time`

Computes `ceil(words / 200)` on text with shortcodes, block delimiters and HTML stripped, minimum 1 minute. Prints nothing when there is no valid post in context.

### `tuifan/signal-visual`

`render.php` always prints a static composition (dark shadow, dotted accretion disk, upper and lower lensed arcs) with an accessible description. `view.ts` → `view.js` adds a WebGL canvas on top of it, honours `prefers-reduced-motion`, pauses rendering when the block is offscreen (`IntersectionObserver`) or the tab is hidden (`visibilitychange`), and leaves the static figure in place when WebGL or JavaScript is unavailable.

### `tuifan/form-template`

No `<form>` element, no endpoint, no storage, no validation flow: every control is `disabled`, the whole fieldset is `disabled` and described by the always-visible notice. Nothing is sent or stored — delivery is intentionally out of scope.

### Intentional deviations from the design reference

- Article body copy is 15px / 1.85 line height.
- Contact and newsletter forms are presentation-only templates.
- The footer `404` link intentionally points at the designed not-found view (`/404`).

## Templates, parts & patterns

### Templates (`templates/`)

| Template | Composition |
| --- | --- |
| `404.html` | `tuifan/not-found` pattern: process exit code, stderr output, recovery links |
| `about-us.html` | header, content in the constrained `.tuifan-prose` group, `sidebar` part, footer — selectable as a page template |
| `archive.html` | `tuifan/archive` pattern: query title, `tuifan/archive-controls`, `tuifan/entry-list`, `sidebar` part |
| `contact-us.html` | header, content, footer — page template for the contact page |
| `front-page.html` | renders the assigned front page content (`core/post-content`) |
| `home.html` | posts index (blog) built from the `tuifan/archive` pattern |
| `index.html` | fallback archive, same `tuifan/archive` pattern |
| `page.html` | page content inside the constrained `.tuifan-prose` group |
| `page-full-width.html` | as `page`, but the content group is `tuifan-prose--wide` with a default (unconstrained) layout |
| `search.html` | `tuifan/search` pattern: search field, category filters, `tuifan/entry-list`, `sidebar` part |
| `single.html` | header, `tuifan/reading-time`, article body, sticky `nextora/table-of-contents` aside (`selector: .tuifan-article__body`), `comments` part, footer |

### Template parts (`parts/`)

| Part | Contents |
| --- | --- |
| `header.html` | Brand, `core/navigation` menus, search entry point (`.tuifan-nav__search`) |
| `footer.html` | Full-width footer band, includes `footer-newsletter`, site title/tagline, and the intentional `/404` link |
| `footer-newsletter.html` | Newsletter shell built on `tuifan/form-template` |
| `sidebar.html` | Sidebar column: `README.md` panel, `./topics` categories list, reader shortcuts. Used by the `tuifan/archive`, `tuifan/home` and `tuifan/search` patterns and by `about-us` |
| `comments.html` | Comments title, comment template, pagination and comment form |

### Patterns (`patterns/`)

Category **Tuifan** (`tuifan`), registered in `inc/setup.php`.

| Pattern | Surface |
| --- | --- |
| `tuifan/home` | Landing shell: hero with signal visual, identity strip, latest entries, sidebar |
| `tuifan/about` | README-style about page: numbered sections, editorial note, contact link |
| `tuifan/contact` | Contact page with the disconnected form template, connection status and delivery panels |
| `tuifan/archive` | Archive listing: contextual title, search controls, entry rows, sidebar |
| `tuifan/search` | Search view: server-rendered search field, category filters, results, sidebar |
| `tuifan/not-found` | 404 panel: process exit code, stderr output, recovery links |

## Build & typecheck

```bash
npm install          # build-only dependencies; nothing from node_modules is loaded at runtime
npm run build        # esbuild 0.25.0 through scripts/build-blocks.mjs
npm run watch        # same build in watch mode (inline sourcemaps, unminified)
npm run typecheck    # tsc --noEmit, strict
```

Build outputs:

| Input | Output |
| --- | --- |
| `blocks/<slug>/index.tsx` | `blocks/<slug>/index.js` + `index.asset.php` |
| `blocks/<slug>/view.ts` | `blocks/<slug>/view.js` + `view.asset.php` |
| `resources/ts/site.ts` | `assets/js/site.js` |

Build behaviour:

- `@wordpress/*` imports are externalised to `window.wp.*` globals; JSX compiles to `window.wp.element.createElement` / `.Fragment`, so React is never bundled into a block (`blocks/entry-list/index.js` contains zero React references).
- `index.asset.php` lists only the `@wordpress/*` handles the block's sources actually import — for example `wp-block-editor`, `wp-blocks`, `wp-components`, `wp-i18n`, `wp-server-side-render`.
- Script versions are 12-character SHA-1 content hashes of the emitted file, not timestamps.
- The Three.js view bundle is built without WP externals; Three.js 0.185.1 is bundled into `blocks/signal-visual/view.js`.

Generated JS is committed. Never hand-edit `blocks/*/index.js`, `blocks/*/view.js` or `assets/js/site.js` — rebuild instead.

## Starter site (`wp tuifan setup`)

A non-destructive seeder, available only inside WP-CLI (`inc/starter-site.php` returns early when `WP_CLI` is undefined, so it never runs on activation or on web requests).

```bash
source '/Users/hieuhuynh/Local Sites/dev/app/.envrc'   # Local only: DB access for WP-CLI
wp tuifan setup
```

Sequence:

1. Errors out if `get_stylesheet()` is not `tuifan`.
2. Prints the current state (front page options, permalinks, page/post/term counts, stored starter IDs) and any existing child overrides that will be preserved.
3. Exports a database backup **outside the theme** to `dirname(ABSPATH)/tuifan-backup-YYYYmmdd-HHMMSS.sql` and aborts without changes if the file is missing or empty.
4. Starts the first administrator as the author when WP-CLI has no logged-in user.

Creates or reuses:

| Item | Detail |
| --- | --- |
| Pages | `home`, `about`, `contact`, `blog` — built from `tuifan/home`, `tuifan/about`, `tuifan/contact` patterns; `about` uses the `about-us` template, `contact` the `contact-us` template |
| Categories | `getting-started` ("Getting started"), `safety` ("Safety"), `workflows` ("Workflows") |
| Guides | `agent-start`, `agent-permissions`, `agent-context`, `agent-evaluation` — core block documents, comments closed, seeded in reading order |
| Navigation | `wp_navigation` entity `Tuifan Primary` (slug `tuifan-primary`) |
| Header part | Tuifan header template part referencing that navigation entity |
| Options | `show_on_front=page`, `page_on_front=<home>`, `page_for_posts=<blog>` |

Idempotency: created IDs are stored in the `tuifan_starter_ids` option (pages, posts, navigation, header part, resolved slugs) and reused on repeat runs. Existing pages, posts and terms are never overwritten, and a customized header template part is preserved — the navigation entity is created but not substituted into it. Slug conflicts fall back to a `tuifan-{slug}` variant.

## Front-end behaviour

Authored in `resources/ts/site.ts`, bundled to `assets/js/site.js`, deferred, footer-loaded, and localised as `tuifanSite` (`copy`, `copied`, `copyFailed`, `codeLabel`). Everything else on the site works without JavaScript.

| Behaviour | Detail |
| --- | --- |
| `/` search shortcut | Focuses and selects `.tuifan-search__input` (or `input[type="search"][name="s"]`); if no field exists, navigates to the search link it finds — `[data-tuifan-search-link]` or `.tuifan-nav__search a`, the class the header part renders. Ignored inside inputs, textareas, selects, `contenteditable`, and for modified (`meta`/`ctrl`/`alt`) or composing keystrokes. |
| Search autofocus | Focuses the field marked `data-tuifan-autofocus="1"` (rendered by `archive-controls` on search views) without scrolling. |
| Code copy | Wraps each `pre.wp-block-code` inside `.tuifan-article` in a `.tuifan-code-block` bar with a Copy button and a `role="status"` live region. Copy resets the label after 1800 ms; when the Clipboard API is unavailable the status announces `Copy unavailable. Select the text to copy.` |
| Reduced-motion table of contents | When `prefers-reduced-motion: reduce` matches, clicks on `.tuifan-article__sidebar .nextora-toc__link` are intercepted in the capture phase to jump instantly (no animation), then move focus to the target heading and update the URL hash. Visitors who have not requested reduced motion keep the parent's smooth scrolling. |

## Testing & QA checklist

Build and static checks:

```bash
npm run build        # expect: per-block index.js/index.asset.php, view.js, assets/js/site.js
npm run typecheck    # expect: no output from tsc --noEmit
for f in functions.php inc/*.php blocks/*/render.php tests/*.php; do php -l "$f"; done   # expect: No syntax errors
```

Coding standards (dev only — `composer install` first):

```bash
composer phpcs       # same as: vendor/bin/phpcs
composer phpcbf      # auto-fix what the sniffs can fix
```

`phpcs.xml.dist` runs the `WordPress`, `WordPress-Core`, `WordPress-Docs`, `WordPress-Extra` and `PHPCompatibilityWP` rulesets over the theme's PHP, requires the `tuifan` global prefix and the `tuifan` text domain, and excludes `vendor/`, `node_modules/`, `assets/` and the generated `*.asset.php` files. Current standing at 1.0.0: 15 errors and 39 warnings across 7 files, 42 of them auto-fixable.

Integration checks (WP-CLI, needs database access):

```bash
source '/Users/hieuhuynh/Local Sites/dev/app/.envrc'
wp eval-file tests/integration-checks.php   # expect: one PASS line per assertion, exit 0
```

`tests/integration-checks.php` covers combined search + category pagination, reading-time word boundaries, seeder repeat behaviour and the presentation-only form templates. Every check creates and removes its own rows, and the command exits with status 1 when an assertion fails. See `tests/README.md`. These checks complement the browser smoke tests below; they do not replace them.

Local runtime checks:

```bash
source '/Users/hieuhuynh/Local Sites/dev/app/.envrc'
wp theme status tuifan       # expect: Status: Active, Version: 1.0.0
curl -I http://dev.local/    # expect: HTTP/1.1 200 OK
```

Manual smoke list:

- [ ] Front page renders the hero signal visual as a static figure before the canvas draws; the figure stays visible with JavaScript disabled.
- [ ] Blog, category and search views show numbered entry rows and server-rendered pagination; `main` lists honour 2 posts per page.
- [ ] Pressing `/` on the front end focuses the search field and does not trigger while typing in an input.
- [ ] Article code blocks copy with the button, and announce the manual-copy message when the Clipboard API is unavailable.
- [ ] With reduced motion enabled, table-of-contents links jump without animation.
- [ ] Contact and newsletter forms render disabled controls, no `<form>` element, and the always-visible notice.
- [ ] A post without a featured image shows the dashed `FEATURE IMAGE` / `Artwork not supplied` panel.
- [ ] Site Editor → Styles lists Tuifan Terminal, Terminal Purple and Terminal Mono.
- [ ] `wp tuifan setup` run twice reports `reused` for existing objects on the second run.

## File map

```
tuifan/
├── style.css                     Theme header (metadata + child pointer)
├── theme.json                    Design tokens: palette, fonts, sizes, spacing, layout
├── functions.php                 Bootstrap: constants and inc/ loading
├── assets/
│   ├── css/theme.css             Design layer (front end + editor canvas)
│   ├── css/editor.css            Editor-canvas parity overrides
│   ├── fonts/ibm-plex-mono/      300/400/500 TTF + OFL.txt
│   └── js/site.js                Built front-end script (generated)
├── blocks/                       One directory per block: block.json, index.tsx,
│   ├── entry-list/               edit.tsx, render.php, style.css, built index.js
│   ├── archive-controls/         + index.asset.php; signal-visual adds view.ts/view.js
│   ├── reading-time/
│   ├── signal-visual/
│   └── form-template/
├── inc/
│   ├── setup.php                 Textdomain, pattern category
│   ├── assets.php                Style/script enqueues, body class
│   ├── blocks.php                Block registration, Tuifan Blocks category
│   ├── content.php               Reading time, entry rows, pagination, filters, main query
│   ├── starter-site.php          `wp tuifan setup` (WP-CLI only)
│   └── starter-content.php       Four seeded guide documents as core block markup
├── parts/                        header, footer, footer-newsletter, sidebar, comments
├── patterns/                     tuifan/home, about, contact, archive, search, not-found
├── resources/ts/                 site.ts (source), globals.d.ts, wordpress-extras.d.ts
├── scripts/build-blocks.mjs      esbuild pipeline
├── styles/                       terminal.json, color-scheme/, font-preset/
├── templates/                    11 block templates
├── tests/                        integration-checks.php + README.md (WP-CLI checks)
├── languages/                    Child translation files (loaded via load_child_theme_textdomain)
├── tsconfig.json                 Strict typecheck config for tsc --noEmit
├── package.json                  Build scripts and npm devDependencies
├── phpcs.xml.dist                WordPress coding-standards ruleset
├── composer.json                 Dev-only PHPCS tooling (`composer phpcs` / `phpcbf`)
└── vendor/, node_modules/        Development dependencies (not loaded at runtime)
```

## Licensing

- **Theme (Tuifan):** GPL-2.0-or-later — see the `License` header in `style.css` and <https://www.gnu.org/licenses/gpl-2.0.html>.
- **IBM Plex Mono (bundled):** SIL Open Font License 1.1 — see `assets/fonts/ibm-plex-mono/OFL.txt` (<https://scripts.sil.org/OFL>).
- **Three.js** is bundled into `blocks/signal-visual/view.js` as a build dependency (MIT).
- The parent theme Nextora is licensed GPL v2 or later by its own authors and is not redistributed here.
