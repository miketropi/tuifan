/**
 * Editor-canvas parity for the selected page template.
 *
 * The block editor canvas renders the post content, not the page template, so
 * nothing inside the canvas reflects `page-full-width.html`'s wider content
 * column: core caps every top-level block at `theme.json`'s `contentSize`
 * (760px) through
 * `.block-editor-block-list__layout.is-root-container > :where(:not(.alignleft):not(.alignright):not(.alignfull))`.
 * On the front end that same content sits inside
 * `templates/page-full-width.html`'s `tuifan-prose--wide` group, which the
 * theme raises to `wideSize` (1376px) in `assets/css/theme.css`.
 *
 * This script mirrors the edited `template` post attribute onto the canvas
 * document element as `tuifan-canvas-template-<slug>`, so
 * `assets/css/editor.css` can apply the same width without touching the front
 * end. It runs in the admin document (the canvas is a same-origin iframe) and
 * re-runs whenever the store changes or the canvas reloads.
 */

export {};

const CANVAS_CLASS_PREFIX = 'tuifan-canvas-template-';
const CANVAS_IFRAME = 'iframe[name="editor-canvas"]';
const DEFAULT_TEMPLATE = 'default';

/**
 * Slug of the template currently selected in the editor sidebar.
 *
 * Block-theme values are either `slug` or `theme//slug`; the theme prefix is
 * dropped so the class stays stable across themes.
 *
 * `getEditedPostAttribute()` throws while the edited entity config is still
 * loading (for example `The entity being edited (postType, undefined) does not
 * have a loaded config`), and store subscribers run during every dispatch of
 * the boot sequence, so the read is guarded: a failed read means "no template
 * known yet" and the next store change applies the real value.
 */
function selectedTemplateSlug(): string {
  try {
    const value = window.wp?.data?.select('core/editor')?.getEditedPostAttribute?.('template');

    if ('string' !== typeof value || '' === value) {
      return DEFAULT_TEMPLATE;
    }

    return value.split('//').pop() || DEFAULT_TEMPLATE;
  } catch {
    return DEFAULT_TEMPLATE;
  }
}

function canvasDocument(): Document | null {
  return document.querySelector<HTMLIFrameElement>(CANVAS_IFRAME)?.contentDocument ?? null;
}

function applyTemplateClass(): void {
  /*
   * The canvas iframe exists before its document is parsed, and a document
   * without a `documentElement` has no class list to write to; store
   * subscribers run throughout that window, so both are checked here.
   */
  const target = canvasDocument()?.documentElement;

  if (!target) {
    return;
  }

  const next = CANVAS_CLASS_PREFIX + selectedTemplateSlug();

  if (target.classList.contains(next)) {
    return;
  }

  Array.from(target.classList).forEach((token) => {
    if (token.startsWith(CANVAS_CLASS_PREFIX)) {
      target.classList.remove(token);
    }
  });

  target.classList.add(next);
}

function boot(): void {
  applyTemplateClass();

  window.wp?.data?.subscribe?.(applyTemplateClass);

  // The canvas iframe reloads when the editor switches modes; `load` does not
  // bubble, so it is captured here.
  document.addEventListener('load', applyTemplateClass, true);
}

if ('loading' === document.readyState) {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}
