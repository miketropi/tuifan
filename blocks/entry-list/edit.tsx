import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import type { EntryListAttributes } from './index';

interface EditProps {
  attributes: EntryListAttributes;
  setAttributes: (attributes: Partial<EntryListAttributes>) => void;
}

const SOURCES = [
  { label: __('Main query (blog, archives, search)', 'tuifan'), value: 'main' },
  { label: __('Latest published posts', 'tuifan'), value: 'latest' },
];

/**
 * Editor preview for the numbered entry rows.
 *
 * `source: main` follows the real main query on the front end; in the editor a
 * standalone published-post query is previewed instead of the admin query.
 */
export default function Edit({ attributes, setAttributes }: EditProps) {
  const blockProps = useBlockProps();

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Entries', 'tuifan')}>
          <SelectControl
            label={__('Source', 'tuifan')}
            value={attributes.source}
            options={SOURCES}
            onChange={(source: string) =>
              setAttributes({ source: source as EntryListAttributes['source'] })
            }
            help={__(
              'Main query reuses the current archive or search result set without re-querying it.',
              'tuifan'
            )}
          />
          <RangeControl
            label={__('Posts per page', 'tuifan')}
            value={attributes.postsPerPage}
            min={1}
            max={10}
            onChange={(postsPerPage: number | undefined) =>
              setAttributes({ postsPerPage: postsPerPage ?? 2 })
            }
            help={__('Used by the latest-posts source and by the editor preview.', 'tuifan')}
          />
          <TextControl
            label={__('Section heading', 'tuifan')}
            value={attributes.heading}
            onChange={(heading: string) => setAttributes({ heading })}
          />
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <ServerSideRender block="tuifan/entry-list" attributes={attributes} />
      </div>
    </>
  );
}
