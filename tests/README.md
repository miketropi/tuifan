# Tuifan integration checks

`integration-checks.php` exercises the child theme boundaries that are easy to
get subtly wrong and are not covered by the front-end smoke tests:

1. **Combined search + category pagination** — a paged link must keep `s`,
   `cat` and `post_type`; the range string must report real counts; pagination
   must not render for a single page.
2. **Reading time** — 0 words → 1 minute, 200 → 1, 201 → 2, 400 → 2, shortcode
   and block markup ignored, unknown post → 0.
3. **Seeder repeat behaviour** — `tuifan_starter_ensure_page()` reuses a stored
   published page, leaves edited content untouched, and reports the reuse.
4. **Presentation-only templates** — no `<form>` element, disabled controls, a
   always-visible notice, unique element ids per instance, and no reading time
   outside a post context.

Every check creates the rows it needs and deletes them again, so the suite is
safe to run on a working site.

## Running

WP-CLI needs the Local site environment for database access:

```bash
cd wp-content/themes/tuifan
source '/path/to/Local Sites/<site>/app/.envrc'
wp eval-file tests/integration-checks.php
```

Output is one `PASS`/`FAIL` line per assertion plus a summary; the command exits
with status `1` when any assertion fails.

## Notes

- `declare( strict_types=1 )` cannot be used in this file: `wp eval-file` wraps
  the script, so the declaration would not be the first statement of the
  compiled script.
- The checks call theme functions directly, so the child theme must be active
  (it is the only supported configuration).
- These checks complement, and do not replace, the browser smoke tests of the
  seven views, the reduced-motion behaviour and the block editor session.
