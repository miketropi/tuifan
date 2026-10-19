import {
  HeadingLevelDropdown,
  RichText,
  useBlockProps,
  useInnerBlocksProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import type { TemplateArray } from '@wordpress/blocks';
import type { PricingTableAttributes } from './index';

interface EditProps {
  attributes: PricingTableAttributes;
  setAttributes: (attributes: Partial<PricingTableAttributes>) => void;
}

const TEMPLATE: TemplateArray = [['tuifan/pricing-card', {}]];

export default function Edit({ attributes, setAttributes }: EditProps) {
  const level = Math.min(4, Math.max(2, attributes.headingLevel || 2)) as 2 | 3 | 4;
  const blockProps = useBlockProps({ className: 'tuifan-section tuifan-pricing' });
  const innerBlocksProps = useInnerBlocksProps(
    { className: 'tuifan-pricing__grid' },
    { allowedBlocks: ['tuifan/pricing-card'], template: TEMPLATE, templateLock: false }
  );

  return (
    <section {...blockProps}>
      <header className="tuifan-section__head">
        <RichText
          tagName="p"
          className="tuifan-section__eyebrow"
          value={attributes.eyebrow}
          allowedFormats={[]}
          placeholder={__('Eyebrow…', 'tuifan')}
          onChange={(eyebrow: string) => setAttributes({ eyebrow })}
        />
        <div className="tuifan-section__heading-control">
          <HeadingLevelDropdown
            value={level}
            options={[2, 3, 4]}
            onChange={(headingLevel) => setAttributes({ headingLevel: headingLevel ?? 2 })}
          />
        </div>
        <RichText
          tagName={`h${level}` as 'h2' | 'h3' | 'h4'}
          className="tuifan-section__heading"
          value={attributes.heading}
          allowedFormats={[]}
          placeholder={__('Section heading…', 'tuifan')}
          onChange={(heading: string) => setAttributes({ heading })}
        />
        <RichText
          tagName="p"
          className="tuifan-section__description"
          value={attributes.description}
          allowedFormats={['core/bold', 'core/italic']}
          placeholder={__('Section description…', 'tuifan')}
          onChange={(description: string) => setAttributes({ description })}
        />
      </header>
      <div {...innerBlocksProps} />
    </section>
  );
}
