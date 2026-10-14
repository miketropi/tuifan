import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import type { SignalVisualAttributes } from './index';

interface EditProps {
  attributes: SignalVisualAttributes;
  setAttributes: (attributes: Partial<SignalVisualAttributes>) => void;
}

/**
 * Editor preview: the static lensed-shadow composition.
 *
 * The WebGL enhancement runs on the front end only, so the editor never starts
 * a render loop.
 */
export default function Edit({ attributes, setAttributes }: EditProps) {
  const blockProps = useBlockProps();

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Signal', 'tuifan')}>
          <TextControl
            label={__('Label', 'tuifan')}
            value={attributes.label}
            onChange={(label: string) => setAttributes({ label })}
            help={__('Shown along the right edge of the stage.', 'tuifan')}
          />
          <ToggleControl
            label={__('Enable motion', 'tuifan')}
            checked={attributes.enableMotion}
            onChange={(enableMotion: boolean) => setAttributes({ enableMotion })}
            help={__(
              'Draws a still composition when disabled. Visitors who prefer reduced motion always get a still frame.',
              'tuifan'
            )}
          />
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <ServerSideRender block="tuifan/signal-visual" attributes={attributes} />
      </div>
    </>
  );
}
