import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { getSafeSectionUrl } from '../../resources/ts/section-url';
import type { PricingCardAttributes } from './index';

export default function save({ attributes }: { attributes: PricingCardAttributes }) {
  const level = Math.min(6, Math.max(2, attributes.headingLevel || 3)) as 2 | 3 | 4 | 5 | 6;
  const safeUrl = getSafeSectionUrl(attributes.actionUrl);
  const blockProps = useBlockProps.save({
    className: `tuifan-pricing-card${attributes.featured ? ' is-featured' : ''}`,
  });
  const showPriceLine = Boolean(attributes.price || attributes.period);

  return (
    <article { ...blockProps }>
      { attributes.badge && <span className="tuifan-pricing-card__badge">{ attributes.badge }</span> }
      { attributes.name && (
        <RichText.Content
          tagName={ `h${ level }` as 'h2' | 'h3' | 'h4' | 'h5' | 'h6' }
          className="tuifan-pricing-card__name"
          value={ attributes.name }
        />
      ) }
      { showPriceLine && (
        <div className="tuifan-pricing-card__price-line">
          { attributes.price && (
            <RichText.Content tagName="span" className="tuifan-pricing-card__price" value={ attributes.price } />
          ) }
          { attributes.period && (
            <RichText.Content tagName="span" className="tuifan-pricing-card__period" value={ attributes.period } />
          ) }
        </div>
      ) }
      <div className="tuifan-pricing-card__features">
        <InnerBlocks.Content />
      </div>
      { attributes.actionLabel && attributes.actionMode === 'preview' && (
        <details className="tuifan-pricing-card__preview">
          <summary className="tuifan-section__action">{ attributes.actionLabel }</summary>
          { attributes.previewText && <p>{ attributes.previewText }</p> }
        </details>
      ) }
      { attributes.actionLabel && attributes.actionMode !== 'preview' && safeUrl && (
        <a className="tuifan-section__action" href={ safeUrl }>{ attributes.actionLabel }</a>
      ) }
      { attributes.actionLabel && attributes.actionMode !== 'preview' && ! safeUrl && (
        <span className="tuifan-section__action-text">{ attributes.actionLabel }</span>
      ) }
    </article>
  );
}
