import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import type { FormTemplateAttributes } from './index';

interface EditProps {
  attributes: FormTemplateAttributes;
  setAttributes: (attributes: Partial<FormTemplateAttributes>) => void;
}

const VARIANTS = [
  { label: __('Contact (name, email, message)', 'tuifan'), value: 'contact' },
  { label: __('Newsletter (email)', 'tuifan'), value: 'newsletter' },
];

/**
 * Editor controls for the disconnected form template.
 *
 * Only presentation can be changed: the block never gains a delivery path.
 */
export default function Edit({ attributes, setAttributes }: EditProps) {
  const blockProps = useBlockProps();
  const buttonPlaceholder =
    attributes.variant === 'newsletter'
      ? __('Subscribe', 'tuifan')
      : __('Send message', 'tuifan');

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Template', 'tuifan')}>
          <SelectControl
            label={__('Variant', 'tuifan')}
            value={attributes.variant}
            options={VARIANTS}
            onChange={(variant: string) =>
              setAttributes({ variant: variant as FormTemplateAttributes['variant'] })
            }
            help={__('Switches the field set and the default button label.', 'tuifan')}
          />
          <TextControl
            label={__('Button label', 'tuifan')}
            value={attributes.buttonLabel}
            placeholder={buttonPlaceholder}
            onChange={(buttonLabel: string) => setAttributes({ buttonLabel })}
            help={__('Empty uses the variant default.', 'tuifan')}
          />
          <TextControl
            label={__('Notice', 'tuifan')}
            value={attributes.notice}
            onChange={(notice: string) => setAttributes({ notice })}
            help={__(
              'Shown next to the controls. Keep it explicit that nothing is delivered.',
              'tuifan'
            )}
          />
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <ServerSideRender block="tuifan/form-template" attributes={attributes} />
      </div>
    </>
  );
}
