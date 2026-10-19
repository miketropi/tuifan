import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { _n, sprintf } from '@wordpress/i18n';
import type { ImpactAttributes } from './index';

/**
 * Static save. The status count comes from the synced `statusCount` attribute
 * rather than `innerBlocks`, because validation renders this function with an
 * empty inner-blocks array and a derived count would never validate again.
 */
export default function save({ attributes }: { attributes: ImpactAttributes }) {
  const level = Math.min(4, Math.max(2, attributes.headingLevel || 2)) as 2 | 3 | 4;
  const count = Math.max(0, attributes.statusCount || 0);
  const blockProps = useBlockProps.save({ className: 'tuifan-section tuifan-impact' });
  const hasHead = Boolean(attributes.eyebrow || attributes.heading || attributes.description);
  const countText = sprintf(
    /* translators: %s: zero-padded count of impact rows. */
    _n('%s signal', '%s signals', count, 'tuifan'),
    String(count).padStart(2, '0')
  );

  return (
    <section { ...blockProps }>
      { hasHead && (
        <header className="tuifan-section__head">
          { attributes.eyebrow && (
            <RichText.Content tagName="p" className="tuifan-section__eyebrow" value={ attributes.eyebrow } />
          ) }
          { attributes.heading && (
            <RichText.Content
              tagName={ `h${ level }` as 'h2' | 'h3' | 'h4' }
              className="tuifan-section__heading"
              value={ attributes.heading }
            />
          ) }
          { attributes.description && (
            <RichText.Content tagName="p" className="tuifan-section__description" value={ attributes.description } />
          ) }
        </header>
      ) }
      <div className="tuifan-impact__panel">
        <div className="tuifan-impact__status">
          { attributes.statusLabel && <RichText.Content tagName="span" value={ attributes.statusLabel } /> }
          <span>{ countText }</span>
          { attributes.statusSuffix && <RichText.Content tagName="span" value={ attributes.statusSuffix } /> }
        </div>
        <ol className="tuifan-impact__rows">
          <InnerBlocks.Content />
        </ol>
      </div>
    </section>
  );
}
