import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import metadata from './block.json';

registerBlockType(metadata as unknown as BlockConfiguration, {
  edit: Edit,
  save: () => null,
});
