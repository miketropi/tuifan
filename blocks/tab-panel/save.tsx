import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import type { TabPanelAttributes } from './index';

export default function save({ attributes }: { attributes: TabPanelAttributes }) {
  const blockProps = useBlockProps.save({
    className: 'tuifan-tabs__panel tuifan-tab-panel',
    'data-tuifan-tab-panel': '',
    'data-tuifan-tab-label': attributes.label,
  });

  return (
    <article { ...blockProps }>
      <RichText.Content tagName="p" className="tuifan-tabs__panel-label" value={ attributes.label } />
      <div className="tuifan-tab-panel__body">
        <InnerBlocks.Content />
      </div>
    </article>
  );
}
