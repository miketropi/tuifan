import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
export type TestimonialCarouselAttributes={eyebrow:string;heading:string;description:string;headingLevel:number;carouselLabel:string};
registerBlockType(metadata as BlockConfiguration<TestimonialCarouselAttributes>,{edit:Edit,save});
