import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import metadata from './block.json';

export type FormTemplateAttributes = {
  variant: 'contact' | 'newsletter';
  buttonLabel: string;
  notice: string;
};

registerBlockType(metadata as BlockConfiguration<FormTemplateAttributes>, {
  edit: Edit,
  save: () => null,
});
