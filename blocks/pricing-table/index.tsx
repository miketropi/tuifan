import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';

export type PricingTableAttributes = {
  eyebrow: string;
  heading: string;
  description: string;
  headingLevel: number;
};

registerBlockType(metadata as BlockConfiguration<PricingTableAttributes>, { edit: Edit, save });
