import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import type { TabsAttributes } from './index';

export default function save({ attributes }: { attributes: TabsAttributes }) {
  const level = Math.min(4, Math.max(2, attributes.headingLevel || 2)) as 2 | 3 | 4;
  const blockProps = useBlockProps.save({
    className: 'tuifan-section tuifan-tabs',
    'data-tuifan-tabs': '',
    'data-tuifan-tab-fallback': __('Tab %d', 'tuifan'),
    'data-tuifan-tab-list-label': attributes.tabListLabel || __('Field guide', 'tuifan'),
  });
  const hasHead = Boolean(attributes.eyebrow || attributes.heading || attributes.description);

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
      <div className="tuifan-tabs__panels">
        <InnerBlocks.Content />
      </div>
    </section>
  );
}
