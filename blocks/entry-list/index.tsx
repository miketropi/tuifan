import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import metadata from './block.json';

export type EntryListAttributes = {
  source: 'latest' | 'main';
  postsPerPage: number;
  heading: string;
};

registerBlockType(metadata as BlockConfiguration<EntryListAttributes>, {
  edit: Edit,
  save: () => null,
});
