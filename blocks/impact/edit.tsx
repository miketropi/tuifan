import {
  HeadingLevelDropdown,
  RichText,
  useBlockProps,
  useInnerBlocksProps,
  store as blockEditorStore,
} from '@wordpress/block-editor';
import type { TemplateArray } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __, _n, sprintf } from '@wordpress/i18n';
import type { ImpactAttributes } from './index';

interface EditProps {
  attributes: ImpactAttributes;
  setAttributes: (attributes: Partial<ImpactAttributes>) => void;
  clientId: string;
}

const TEMPLATE: TemplateArray = [['tuifan/impact-row', {}]];

/**
 * Format the status count the same way `save()` does.
 *
 * @param count Number of impact rows.
 */
function getStatusCountText(count: number): string {
  return sprintf(
    /* translators: %s: zero-padded count of impact rows. */
    _n('%s signal', '%s signals', count, 'tuifan'),
    String(count).padStart(2, '0')
  );
}

export default function Edit({ attributes, setAttributes, clientId }: EditProps) {
  const level = Math.min(4, Math.max(2, attributes.headingLevel || 2)) as 2 | 3 | 4;
  // The count is read live from the inner blocks and mirrored into an
  // attribute: a static `save()` cannot derive it, because validation renders
  // `save` with an empty inner-blocks array.
  const count = useSelect((select) => {
    const store = select(blockEditorStore) as unknown as { getBlockOrder: (id: string) => string[] };
    return store.getBlockOrder(clientId).length;
  }, [clientId]) as unknown as number;

  useEffect(() => {
    if (attributes.statusCount !== count) {
      setAttributes({ statusCount: count });
    }
  }, [attributes.statusCount, count, setAttributes]);

  const inner = useInnerBlocksProps(
    { className: 'tuifan-impact__rows' },
    { allowedBlocks: ['tuifan/impact-row'], template: TEMPLATE, templateLock: false }
  );

  return (
    <section { ...useBlockProps({ className: 'tuifan-section tuifan-impact' }) }>
      <header className="tuifan-section__head">
        <RichText
          tagName="p"
          className="tuifan-section__eyebrow"
          value={ attributes.eyebrow }
          allowedFormats={ [] }
          placeholder={ __('Eyebrow…', 'tuifan') }
          onChange={ (eyebrow: string) => setAttributes({ eyebrow }) }
        />
        <HeadingLevelDropdown
          value={ level }
          options={ [2, 3, 4] }
          onChange={ (headingLevel) => setAttributes({ headingLevel: headingLevel ?? 2 }) }
        />
        <RichText
          tagName={ `h${ level }` as 'h2' | 'h3' | 'h4' }
          className="tuifan-section__heading"
          value={ attributes.heading }
          allowedFormats={ [] }
          placeholder={ __('Section heading…', 'tuifan') }
          onChange={ (heading: string) => setAttributes({ heading }) }
        />
        <RichText
          tagName="p"
          className="tuifan-section__description"
          value={ attributes.description }
          allowedFormats={ ['core/bold', 'core/italic'] }
          placeholder={ __('Section description…', 'tuifan') }
          onChange={ (description: string) => setAttributes({ description }) }
        />
      </header>
      <div className="tuifan-impact__panel">
        <div className="tuifan-impact__status">
          <RichText
            tagName="span"
            value={ attributes.statusLabel }
            allowedFormats={ [] }
            placeholder={ __('Status label…', 'tuifan') }
            onChange={ (statusLabel: string) => setAttributes({ statusLabel }) }
          />
          <span>{ getStatusCountText(count) }</span>
          <RichText
            tagName="span"
            value={ attributes.statusSuffix }
            allowedFormats={ [] }
            placeholder={ __('Status suffix…', 'tuifan') }
            onChange={ (statusSuffix: string) => setAttributes({ statusSuffix }) }
          />
        </div>
        <ol { ...inner } />
      </div>
    </section>
  );
}
