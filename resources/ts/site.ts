/**
 * Tuifan front-end behaviour.
 *
 * Deliberately small: the `/` search shortcut (focus or navigate to the
 * server-generated search URL) and the article code-copy control. Everything
 * else on the site works without JavaScript.
 */

const SEARCH_INPUT_SELECTOR = '.tuifan-search__input, input[type="search"][name="s"]';
const SEARCH_LINK_SELECTOR = '[data-tuifan-search-link], .tuifan-nav__search a';
const SEARCH_AUTOFOCUS_SELECTOR = '[data-tuifan-autofocus="1"]';
const ARTICLE_SELECTOR = '.tuifan-article';
const CODE_SELECTOR = 'pre.wp-block-code';
const TOC_LINK_SELECTOR = '.tuifan-article__sidebar .nextora-toc__link';
const TOC_ITEM_SELECTOR = '.nextora-toc__item';
const TOC_CURRENT_CLASS = 'tuifan-toc__item--current';
/** Same margin as the parent block's scroll-spy, so both agree on the entry. */
const TOC_ROOT_MARGIN = '-10% 0px -70% 0px';
const COPY_LABEL_RESET_MS = 1800;
const MANUAL_COPY_MESSAGE = 'Copy unavailable. Select the text to copy.';

const strings: TuifanSiteStrings = {
  copy: 'Copy',
  copied: 'Copied',
  copyFailed: MANUAL_COPY_MESSAGE,
  codeLabel: 'code',
  ...(window.tuifanSite ?? {}),
};

function isEditableTarget(node: EventTarget | null): boolean {
  if (!(node instanceof HTMLElement)) {
    return false;
  }

  const tag = node.tagName;

  if ('INPUT' === tag || 'TEXTAREA' === tag || 'SELECT' === tag) {
    return true;
  }

  return node.isContentEditable || node.closest('[contenteditable="true"]') !== null;
}

function installSearchShortcut(): void {
  document.addEventListener('keydown', (event: KeyboardEvent) => {
    if ('/' !== event.key || event.metaKey || event.ctrlKey || event.altKey || event.isComposing) {
      return;
    }

    if (isEditableTarget(event.target) || isEditableTarget(document.activeElement)) {
      return;
    }

    const input = document.querySelector<HTMLInputElement>(SEARCH_INPUT_SELECTOR);
    const link = document.querySelector<HTMLAnchorElement>(SEARCH_LINK_SELECTOR);

    if (input) {
      event.preventDefault();
      input.focus();
      input.select();

      return;
    }

    if (link) {
      event.preventDefault();
      window.location.assign(link.href);
    }
  });
}

function installSearchAutofocus(): void {
  const input = document.querySelector<HTMLInputElement>(SEARCH_AUTOFOCUS_SELECTOR);

  if (input) {
    input.focus({ preventScroll: true });
  }
}

async function writeClipboard(text: string): Promise<void> {
  if (!navigator.clipboard || 'function' !== typeof navigator.clipboard.writeText) {
    throw new Error('clipboard-unavailable');
  }

  await navigator.clipboard.writeText(text);
}

function enhanceCodeBlock(pre: HTMLElement): void {
  if (pre.closest('.tuifan-code-block')) {
    return;
  }

  const wrapper = document.createElement('div');
  wrapper.className = 'tuifan-code-block';

  const bar = document.createElement('div');
  bar.className = 'tuifan-code-bar';

  const label = document.createElement('span');
  label.className = 'tuifan-code-bar__label';
  label.textContent = strings.codeLabel;

  const button = document.createElement('button');
  button.type = 'button';
  button.className = 'tuifan-copy';
  button.textContent = strings.copy;

  const status = document.createElement('p');
  status.className = 'tuifan-code-status';
  status.setAttribute('role', 'status');
  status.setAttribute('aria-live', 'polite');

  bar.append(label, button);

  // Place the wrapper first, then move the block into it (moving an ancestor
  // into its own detached descendant throws a hierarchy error).
  pre.before(wrapper);
  wrapper.append(bar, pre, status);

  button.addEventListener('click', async () => {
    const code = pre.querySelector('code') ?? pre;
    const text = code.textContent ?? '';

    try {
      await writeClipboard(text);
      button.textContent = strings.copied;
      status.textContent = strings.copied;

      window.setTimeout(() => {
        button.textContent = strings.copy;
      }, COPY_LABEL_RESET_MS);
    } catch {
      status.textContent = strings.copyFailed;
    }
  });
}

function installCodeCopy(): void {
  const article = document.querySelector(ARTICLE_SELECTOR);

  if (!article) {
    return;
  }

  article.querySelectorAll<HTMLElement>(CODE_SELECTOR).forEach(enhanceCodeBlock);
}

/**
 * Reduced motion only: jump to table-of-contents anchors without animation.
 *
 * The parent block scrolls smoothly on every TOC click; with reduced motion
 * requested, that is intercepted in the capture phase so the jump is instant.
 * Nothing changes for visitors who have not asked for reduced motion.
 */
function installReducedMotionToc(): void {
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  document.addEventListener(
    'click',
    (event: MouseEvent) => {
      if (!reducedMotion.matches || event.defaultPrevented || event.button !== 0) {
        return;
      }

      const target = event.target;

      if (!(target instanceof Element)) {
        return;
      }

      const link = target.closest<HTMLAnchorElement>('.tuifan-article__sidebar .nextora-toc__link');

      if (!link || !link.hash) {
        return;
      }

      const heading = document.getElementById(decodeURIComponent(link.hash.slice(1)));

      if (!heading) {
        return;
      }

      event.stopPropagation();
      event.preventDefault();

      heading.scrollIntoView({ behavior: 'instant', block: 'start' });
      heading.setAttribute('tabindex', '-1');
      heading.focus({ preventScroll: true });
      window.history.pushState(null, '', link.hash);
    },
    true
  );
}

/**
 * Mirror the table-of-contents scroll-spy onto a child-owned class.
 *
 * The parent block marks the active item with inline styles on the front end and
 * adds `is-active` only when reduced motion is requested, so a stylesheet cannot
 * colour the active label. This marks the same entry from the headings the
 * rendered links already point at, using the parent's root margin so both agree.
 */
function installTocCurrentItem(): void {
  const headings = new Map<Element, HTMLElement>();

  for (const link of document.querySelectorAll<HTMLAnchorElement>(TOC_LINK_SELECTOR)) {
    if (link.hash.length < 2) {
      continue;
    }

    const heading = document.getElementById(decodeURIComponent(link.hash.slice(1)));
    const item = link.closest<HTMLElement>(TOC_ITEM_SELECTOR);

    if (heading && item) {
      headings.set(heading, item);
    }
  }

  if (!headings.size || 'function' !== typeof IntersectionObserver) {
    return;
  }

  let current: HTMLElement | null = null;

  const observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) {
          continue;
        }

        const item = headings.get(entry.target);

        if (!item || item === current) {
          continue;
        }

        if (current) {
          current.classList.remove(TOC_CURRENT_CLASS);
        }

        current = item;
        item.classList.add(TOC_CURRENT_CLASS);

        return;
      }
    },
    { rootMargin: TOC_ROOT_MARGIN, threshold: 0 }
  );

  headings.forEach((_item, heading) => observer.observe(heading));
}

function boot(): void {
  installSearchShortcut();
  installSearchAutofocus();
  installCodeCopy();
  installReducedMotionToc();
  installTocCurrentItem();
}

if ('loading' === document.readyState) {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}
