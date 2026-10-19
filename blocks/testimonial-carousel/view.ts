export {};

/**
 * Progressive enhancement for the testimonial and posts carousels.
 *
 * Both carousel parents ship the same DOM contract, so one script drives every
 * `[data-tuifan-carousel]` root: native overflow scrolling stays the base
 * behaviour and the controls are only revealed once this script has a valid
 * root to enhance.
 */
const ROOT_SELECTOR = '[data-tuifan-carousel]';

/** Distance in px under which a target card counts as reached. */
const SETTLE_THRESHOLD = 4;

interface Carousel {
  root: HTMLElement;
  viewport: HTMLElement;
  items: HTMLElement[];
  controls: HTMLElement;
  previous: HTMLButtonElement;
  next: HTMLButtonElement;
  status: HTMLElement;
}

function format(template: string, ...values: Array<string | number>): string {
  return values.reduce<string>(
    (result, value, index) => result.replace(`%${index + 1}$s`, String(value)),
    template
  );
}

function pad(value: number): string {
  return String(value).padStart(2, '0');
}

function visibleCount(viewport: HTMLElement): number {
  const value = Number.parseInt(
    getComputedStyle(viewport).getPropertyValue('--tuifan-carousel-visible'),
    10
  );

  return Number.isFinite(value) ? Math.max(1, value) : 1;
}

/**
 * Signed inline-axis distance from the viewport edge to a card.
 *
 * Chromium keeps `scrollLeft` in `[-max, 0]` for RTL, so moving forward needs a
 * negative delta there; LTR uses the mirrored positive value.
 */
function signedOffset(viewport: HTMLElement, item: HTMLElement): number {
  const viewportRect = viewport.getBoundingClientRect();
  const itemRect = item.getBoundingClientRect();

  return getComputedStyle(viewport).direction === 'rtl'
    ? itemRect.right - viewportRect.right
    : itemRect.left - viewportRect.left;
}

function nearestIndex(carousel: Carousel): number {
  let closest = 0;
  let distance = Number.POSITIVE_INFINITY;

  carousel.items.forEach((item, index) => {
    const current = Math.abs(signedOffset(carousel.viewport, item));

    if (current < distance) {
      closest = index;
      distance = current;
    }
  });

  return closest;
}

function init(root: HTMLElement): void {
  if (root.dataset.tuifanCarouselReady === 'true') {
    return;
  }

  const viewport = root.querySelector<HTMLElement>('[data-tuifan-carousel-viewport]');
  const track = root.querySelector<HTMLElement>('[data-tuifan-carousel-track]');
  const controls = root.querySelector<HTMLElement>('[data-tuifan-carousel-controls]');
  const previous = root.querySelector<HTMLButtonElement>('[data-tuifan-carousel-prev]');
  const next = root.querySelector<HTMLButtonElement>('[data-tuifan-carousel-next]');
  const status = root.querySelector<HTMLElement>('[data-tuifan-carousel-status]');
  const items = track
    ? Array.from(track.children).filter(
        (child): child is HTMLElement =>
          child instanceof HTMLElement && child.hasAttribute('data-tuifan-carousel-item')
      )
    : [];

  if (!viewport || !track || !controls || !previous || !next || !status || items.length === 0) {
    return;
  }

  const carousel: Carousel = { root, viewport, items, controls, previous, next, status };
  const itemLabel = root.dataset.tuifanCarouselItemLabel || 'Item %1$s of %2$s';
  const rangeLabel = root.dataset.tuifanCarouselRange || '%1$s–%2$s / %3$s';
  const roleDescription = root.dataset.tuifanCarouselDescription || 'carousel';
  const reducedMotion = (): boolean => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  let frame = 0;
  let index = 0;
  /** Target index while an animated scroll is in flight; -1 when settled. */
  let pending = -1;

  items.forEach((item, itemIndex) => {
    item.setAttribute('aria-roledescription', roleDescription);
    item.setAttribute('aria-label', format(itemLabel, itemIndex + 1, items.length));
  });

  const maximumIndex = (): number =>
    Math.max(0, items.length - Math.min(visibleCount(viewport), items.length));

  const render = (): void => {
    const maximum = maximumIndex();

    index = Math.min(maximum, Math.max(0, index));
    previous.disabled = index <= 0;
    next.disabled = index >= maximum;
    controls.hidden = maximum === 0;
    status.textContent = format(
      rangeLabel,
      pad(index + 1),
      pad(Math.min(items.length, index + Math.min(visibleCount(viewport), items.length))),
      pad(items.length)
    );
  };

  const measure = (): void => {
    frame = 0;

    if (pending >= 0) {
      index = Math.min(maximumIndex(), pending);

      if (Math.abs(signedOffset(viewport, items[index])) < SETTLE_THRESHOLD) {
        pending = -1;
      }
    } else {
      index = Math.min(maximumIndex(), nearestIndex(carousel));
    }

    render();
  };

  const schedule = (): void => {
    if (!frame) {
      frame = window.requestAnimationFrame(measure);
    }
  };

  const moveTo = (target: number, smooth = true): void => {
    const maximum = maximumIndex();

    index = Math.min(maximum, Math.max(0, target));
    pending = index;
    render();

    const behavior = smooth && !reducedMotion() ? 'smooth' : 'auto';

    viewport.scrollBy({ left: signedOffset(viewport, items[index]), behavior });
    schedule();
  };

  previous.addEventListener('click', () => moveTo(index - 1));
  next.addEventListener('click', () => moveTo(index + 1));

  // Manual scrolling takes over from any pending target immediately.
  ['pointerdown', 'wheel', 'touchstart'].forEach((type) =>
    viewport.addEventListener(type, () => { pending = -1; }, { passive: true })
  );

  viewport.addEventListener('scroll', schedule, { passive: true });

  viewport.addEventListener('keydown', (event: KeyboardEvent) => {
    if (event.target !== viewport) {
      return;
    }

    const rtl = getComputedStyle(viewport).direction === 'rtl';

    if (event.key === 'Home') {
      event.preventDefault();
      moveTo(0);
    } else if (event.key === 'End') {
      event.preventDefault();
      moveTo(items.length);
    } else if (event.key === 'ArrowRight') {
      event.preventDefault();
      moveTo(index + (rtl ? -1 : 1));
    } else if (event.key === 'ArrowLeft') {
      event.preventDefault();
      moveTo(index + (rtl ? 1 : -1));
    }
  });

  new ResizeObserver(schedule).observe(root);

  root.dataset.tuifanCarouselReady = 'true';
  render();
}

function boot(): void {
  document.querySelectorAll<HTMLElement>(ROOT_SELECTOR).forEach(init);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}
