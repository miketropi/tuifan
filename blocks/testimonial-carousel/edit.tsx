import {HeadingLevelDropdown,InspectorControls,RichText,useBlockProps,useInnerBlocksProps} from '@wordpress/block-editor';
import {PanelBody,TextControl} from '@wordpress/components';
import type {TemplateArray} from '@wordpress/blocks';
import {__} from '@wordpress/i18n';
import type {TestimonialCarouselAttributes} from './index';
interface Props{attributes:TestimonialCarouselAttributes;setAttributes:(value:Partial<TestimonialCarouselAttributes>)=>void}
const TEMPLATE:TemplateArray=[['tuifan/testimonial-card',{}]];
export default function Edit({attributes,setAttributes}:Props){
 const level=Math.min(4,Math.max(2,attributes.headingLevel||2)) as 2|3|4;
 const blockProps=useBlockProps({className:'tuifan-section tuifan-testimonials'});
 const inner=useInnerBlocksProps({className:'tuifan-carousel__track tuifan-testimonials__editor-track'},{allowedBlocks:['tuifan/testimonial-card'],template:TEMPLATE,templateLock:false});
 return <section {...blockProps}><InspectorControls><PanelBody title={__('Carousel settings','tuifan')}><TextControl label={__('Accessible label','tuifan')} value={attributes.carouselLabel} onChange={(carouselLabel:string)=>setAttributes({carouselLabel})}/></PanelBody></InspectorControls><header className="tuifan-section__head"><RichText tagName="p" className="tuifan-section__eyebrow" value={attributes.eyebrow} allowedFormats={[]} placeholder={__('Eyebrow…','tuifan')} onChange={(eyebrow:string)=>setAttributes({eyebrow})}/><HeadingLevelDropdown value={level} options={[2,3,4]} onChange={(headingLevel)=>setAttributes({headingLevel:headingLevel??2})}/><RichText tagName={`h${level}` as 'h2'|'h3'|'h4'} className="tuifan-section__heading" value={attributes.heading} allowedFormats={[]} placeholder={__('Section heading…','tuifan')} onChange={(heading:string)=>setAttributes({heading})}/><RichText tagName="p" className="tuifan-section__description" value={attributes.description} allowedFormats={['core/bold','core/italic']} placeholder={__('Section description…','tuifan')} onChange={(description:string)=>setAttributes({description})}/></header><div {...inner}/></section>;
}
