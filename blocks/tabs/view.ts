export {};

/**
 * Progressive enhancement for saved `tuifan/tabs` markup.
 *
 * The saved markup stays readable without JavaScript: every panel is visible
 * with its own label heading and links. This script adds the tablist, hides the
 * inactive panels and wires the ARIA relationship.
 */
let instance = 0;

function uniqueId(prefix: string): string {
  let value = '';

  do {
    value = `tuifan-${prefix}-${++instance}`;
  } while (document.getElementById(value));

  return value;
}

function init(root: HTMLElement): void {
  if (root.dataset.tuifanTabsReady === 'true') {
    return;
  }

  const panelsHost = root.querySelector<HTMLElement>(':scope > .tuifan-tabs__panels');
  const panels = panelsHost
    ? Array.from(panelsHost.querySelectorAll<HTMLElement>(':scope > [data-tuifan-tab-panel]'))
    : [];

  if (!panelsHost || panels.length === 0) {
    return;
  }

  const fallbackTemplate = root.dataset.tuifanTabFallback || 'Tab %d';
  const list = document.createElement('div');
  list.className = 'tuifan-tabs__tablist';
  list.setAttribute('role', 'tablist');
  list.setAttribute('aria-label', root.dataset.tuifanTabListLabel || 'Field guide');

  const tabs = panels.map((panel, index) => {
    const tab = document.createElement('button');
    const tabId = uniqueId('tab');
    const panelId = uniqueId('panel');
    const label = (panel.dataset.tuifanTabLabel || '').trim();

    tab.type = 'button';
    tab.className = 'tuifan-tabs__tab';
    tab.textContent = label || fallbackTemplate.replace('%d', String(index + 1));
    tab.id = tabId;
    tab.setAttribute('role', 'tab');
    tab.setAttribute('aria-controls', panelId);

    panel.id = panelId;
    panel.setAttribute('role', 'tabpanel');
    panel.setAttribute('aria-labelledby', tabId);
    panel.tabIndex = 0;

    list.append(tab);

    return tab;
  });

  /*
   * The stacked layout flag is set on a descendant of the container, so it is
   * read from the panels host rather than from the container itself.
   */
  const orientation = (): 'horizontal' | 'vertical' =>
    getComputedStyle(panelsHost).getPropertyValue('--tuifan-tabs-orientation').trim() === 'vertical'
      ? 'vertical'
      : 'horizontal';

  const activate = (index: number, focus = false): void => {
    tabs.forEach((tab, tabIndex) => {
      const selected = tabIndex === index;

      tab.setAttribute('aria-selected', String(selected));
      tab.tabIndex = selected ? 0 : -1;
      panels[tabIndex].hidden = !selected;
    });

    list.setAttribute('aria-orientation', orientation());

    if (focus) {
      tabs[index].focus();
    }
  };

  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => activate(index));

    tab.addEventListener('keydown', (event: KeyboardEvent) => {
      const vertical = orientation() === 'vertical';
      const rtl = getComputedStyle(root).direction === 'rtl';
      let target = -1;

      if (event.key === 'Home') {
        target = 0;
      } else if (event.key === 'End') {
        target = tabs.length - 1;
      } else if (vertical && event.key === 'ArrowDown') {
        target = (index + 1) % tabs.length;
      } else if (vertical && event.key === 'ArrowUp') {
        target = (index - 1 + tabs.length) % tabs.length;
      } else if (!vertical && event.key === 'ArrowRight') {
        target = (index + (rtl ? -1 : 1) + tabs.length) % tabs.length;
      } else if (!vertical && event.key === 'ArrowLeft') {
        target = (index + (rtl ? 1 : -1) + tabs.length) % tabs.length;
      }

      if (target >= 0) {
        event.preventDefault();
        activate(target, true);
      }
    });
  });

  panelsHost.before(list);
  root.classList.add('is-enhanced');
  root.dataset.tuifanTabsReady = 'true';

  new ResizeObserver(() => list.setAttribute('aria-orientation', orientation())).observe(root);

  activate(0);
}

function boot(): void {
  document.querySelectorAll<HTMLElement>('[data-tuifan-tabs]').forEach(init);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}
