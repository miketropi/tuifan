import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import type { TestimonialCarouselAttributes } from './index';

export default function save({ attributes }: { attributes: TestimonialCarouselAttributes }) {
  const level = Math.min(4, Math.max(2, attributes.headingLevel || 2)) as 2 | 3 | 4;
  const label = attributes.carouselLabel || __('Testimonials', 'tuifan');
  const blockProps = useBlockProps.save({
    className: 'tuifan-section tuifan-testimonials',
    'data-tuifan-carousel': '',
    'aria-label': label,
    'data-tuifan-carousel-description': __('carousel', 'tuifan'),
    'data-tuifan-carousel-item-label': __('Item %1$s of %2$s', 'tuifan'),
    'data-tuifan-carousel-range': __('%1$s–%2$s / %3$s', 'tuifan'),
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
      <div
        className="tuifan-carousel__viewport"
        data-tuifan-carousel-viewport=""
        tabIndex={ 0 }
        aria-roledescription={ __('carousel', 'tuifan') }
      >
        <div className="tuifan-carousel__track" data-tuifan-carousel-track="">
          <InnerBlocks.Content />
        </div>
      </div>
      <div className="tuifan-carousel__controls" data-tuifan-carousel-controls="" hidden>
        <button type="button" data-tuifan-carousel-prev="" aria-label={ __('Previous item', 'tuifan') }>←</button>
        <span
          className="tuifan-carousel__status"
          data-tuifan-carousel-status=""
          aria-live="polite"
          aria-atomic="true"
        />
        <button type="button" data-tuifan-carousel-next="" aria-label={ __('Next item', 'tuifan') }>→</button>
      </div>
    </section>
  );
}
