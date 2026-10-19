import {
  HeadingLevelDropdown,
  InspectorControls,
  RichText,
  useBlockProps,
  useInnerBlocksProps,
} from '@wordpress/block-editor';
import { Notice, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { TemplateArray } from '@wordpress/blocks';
import { getSafeSectionUrl } from '../../resources/ts/section-url';
import type { PricingCardAttributes } from './index';

interface EditProps {
  attributes: PricingCardAttributes;
  setAttributes: (attributes: Partial<PricingCardAttributes>) => void;
}

const LIST_TEMPLATE: TemplateArray = [['core/list', {}]];

export default function Edit({ attributes, setAttributes }: EditProps) {
  const level = Math.min(6, Math.max(2, attributes.headingLevel || 3)) as 2 | 3 | 4 | 5 | 6;
  const invalidUrl = attributes.actionMode === 'link' && !!attributes.actionUrl && !getSafeSectionUrl(attributes.actionUrl);
  const blockProps = useBlockProps({
    className: `tuifan-pricing-card${attributes.featured ? ' is-featured' : ''}`,
  });
  const listProps = useInnerBlocksProps(
    { className: 'tuifan-pricing-card__features' },
    { allowedBlocks: ['core/list'], template: LIST_TEMPLATE, templateLock: false }
  );

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Card settings', 'tuifan')}>
          <ToggleControl label={__('Featured card', 'tuifan')} checked={attributes.featured} onChange={(featured: boolean) => setAttributes({ featured })} />
          <TextControl label={__('Badge', 'tuifan')} value={attributes.badge} onChange={(badge: string) => setAttributes({ badge })} />
          <SelectControl label={__('Action mode', 'tuifan')} value={attributes.actionMode} options={[
            { label: __('Link', 'tuifan'), value: 'link' },
            { label: __('Preview disclosure', 'tuifan'), value: 'preview' },
          ]} onChange={(actionMode: string) => setAttributes({ actionMode: actionMode as PricingCardAttributes['actionMode'] })} />
          {attributes.actionMode === 'link' && <TextControl label={__('Action URL', 'tuifan')} value={attributes.actionUrl} onChange={(actionUrl: string) => setAttributes({ actionUrl })} />}
          {attributes.actionMode === 'preview' && <TextControl label={__('Preview disclosure', 'tuifan')} value={attributes.previewText} onChange={(previewText: string) => setAttributes({ previewText })} />}
          <HeadingLevelDropdown value={level} options={[2, 3, 4, 5, 6]} onChange={(headingLevel) => setAttributes({ headingLevel: headingLevel ?? 3 })} />
        </PanelBody>
      </InspectorControls>
      <article {...blockProps}>
        {attributes.badge && <span className="tuifan-pricing-card__badge">{attributes.badge}</span>}
        <RichText tagName={`h${level}` as 'h2' | 'h3' | 'h4' | 'h5' | 'h6'} className="tuifan-pricing-card__name" value={attributes.name} allowedFormats={[]} placeholder={__('Card name…', 'tuifan')} onChange={(name: string) => setAttributes({ name })} />
        <div className="tuifan-pricing-card__price-line">
          <RichText tagName="span" className="tuifan-pricing-card__price" value={attributes.price} allowedFormats={[]} placeholder={__('Price or label…', 'tuifan')} onChange={(price: string) => setAttributes({ price })} />
          <RichText tagName="span" className="tuifan-pricing-card__period" value={attributes.period} allowedFormats={[]} placeholder={__('Period or note…', 'tuifan')} onChange={(period: string) => setAttributes({ period })} />
        </div>
        <div {...listProps} />
        <RichText tagName="span" className="tuifan-section__action-text" value={attributes.actionLabel} allowedFormats={[]} placeholder={__('Action label…', 'tuifan')} onChange={(actionLabel: string) => setAttributes({ actionLabel })} />
        {invalidUrl && <Notice status="warning" isDismissible={false}>{__('Enter a safe HTTP, HTTPS, email, phone, fragment, or local relative URL.', 'tuifan')}</Notice>}
      </article>
    </>
  );
}
