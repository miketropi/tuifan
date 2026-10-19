import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';

export type ImpactAttributes = {
  eyebrow: string;
  heading: string;
  description: string;
  headingLevel: number;
  statusLabel: string;
  statusSuffix: string;
  /**
   * Number of `tuifan/impact-row` children, kept in sync by the editor.
   *
   * A static `save()` cannot read the live inner blocks reliably: validation
   * renders `save` with an empty inner-blocks array, so a derived count would
   * persist markup that never validates again.
   */
  statusCount: number;
};

registerBlockType(metadata as BlockConfiguration<ImpactAttributes>, { edit: Edit, save });
