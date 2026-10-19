import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';

export type PricingCardAttributes = {
  name: string;
  price: string;
  period: string;
  badge: string;
  actionLabel: string;
  actionUrl: string;
  previewText: string;
  featured: boolean;
  actionMode: 'link' | 'preview';
  headingLevel: number;
};

registerBlockType(metadata as BlockConfiguration<PricingCardAttributes>, { edit: Edit, save });
