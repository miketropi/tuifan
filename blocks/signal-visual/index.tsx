import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import metadata from './block.json';

export type SignalVisualAttributes = {
  label: string;
  enableMotion: boolean;
};

registerBlockType(metadata as BlockConfiguration<SignalVisualAttributes>, {
  edit: Edit,
  save: () => null,
});
