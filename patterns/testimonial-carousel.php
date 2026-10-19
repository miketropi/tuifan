<?php
/**
 * Title: Testimonial carousel
 * Slug: tuifan/testimonial-carousel
 * Categories: tuifan
 * Description: Editorial quotes in an accessible non-looping carousel.
 * Keywords: testimonials, quotes, carousel, editorial signals
 * Viewport Width: 1400
 *
 * @package Tuifan
 */

$tuifan_leaf          = static fn( string $name, array $attrs, string $html ): string => serialize_block(
	array(
		'blockName'    => $name,
		'attrs'        => $attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => $html,
		'innerContent' => array( $html ),
	)
);
$tuifan_quotes        = array(
	array( __( 'Put AI agents to work without giving up control.', 'tuifan' ), 'devfun.blog', __( 'Home hero', 'tuifan' ), 'CONTROL / PRACTICE' ),
	array( __( 'Practical guides to AI agents, better context, and workflows you can trust.', 'tuifan' ), 'README.md', __( 'Site introduction', 'tuifan' ), 'CONTEXT / TRUST' ),
	array( __( 'Start with the beginner guide, then explore permissions, context, and evaluation.', 'tuifan' ), __( 'Reader shortcuts', 'tuifan' ), __( 'Suggested route', 'tuifan' ), 'START / EXPLORE' ),
);
$tuifan_cards         = array_map(
	static function ( array $quote ) use ( $tuifan_leaf ): string {
		$attrs = array(
			'quote'      => $quote[0],
			'authorName' => $quote[1],
			'authorRole' => $quote[2],
			'tag'        => $quote[3],
		);
		$html  = '<blockquote class="wp-block-tuifan-testimonial-card tuifan-testimonial-card" data-tuifan-carousel-item><span class="tuifan-testimonial-card__mark" aria-hidden="true">“</span><p class="tuifan-testimonial-card__quote">' . esc_html( $quote[0] ) . '</p><footer class="tuifan-testimonial-card__footer"><cite class="tuifan-testimonial-card__author">' . esc_html( $quote[1] ) . '</cite> <span class="tuifan-testimonial-card__role">' . esc_html( $quote[2] ) . '</span> <span class="tuifan-testimonial-card__tag">' . esc_html( $quote[3] ) . '</span></footer></blockquote>';
		return $tuifan_leaf( 'tuifan/testimonial-card', $attrs, $html );
	},
	$tuifan_quotes
);
$tuifan_attrs         = array(
	'eyebrow'       => __( '~/signals — from the demo', 'tuifan' ),
	'heading'       => __( 'The editorial promise, in the site’s own words.', 'tuifan' ),
	'description'   => __( 'These excerpts come directly from the live home page; no reader identities or testimonials were invented.', 'tuifan' ),
	'headingLevel'  => 2,
	'carouselLabel' => __( 'Editorial signals', 'tuifan' ),
);
$tuifan_carousel_meta = sprintf(
	'data-tuifan-carousel-description="%1$s" data-tuifan-carousel-item-label="%2$s" data-tuifan-carousel-range="%3$s"',
	esc_attr__( 'carousel', 'tuifan' ),
	/* translators: 1: current item number, 2: total item count. */
	esc_attr__( 'Item %1$s of %2$s', 'tuifan' ),
	/* translators: 1: first visible item number, 2: last visible item number, 3: total item count. */
	esc_attr__( '%1$s–%2$s / %3$s', 'tuifan' )
);

$tuifan_html = '<section class="wp-block-tuifan-testimonial-carousel tuifan-section tuifan-testimonials" data-tuifan-carousel aria-label="' . esc_attr( $tuifan_attrs['carouselLabel'] ) . '" ' . $tuifan_carousel_meta . '><header class="tuifan-section__head"><p class="tuifan-section__eyebrow">' . esc_html( $tuifan_attrs['eyebrow'] ) . '</p><h2 class="tuifan-section__heading">' . esc_html( $tuifan_attrs['heading'] ) . '</h2><p class="tuifan-section__description">' . esc_html( $tuifan_attrs['description'] ) . '</p></header><div class="tuifan-carousel__viewport" data-tuifan-carousel-viewport tabindex="0" aria-roledescription="' . esc_attr__( 'carousel', 'tuifan' ) . '"><div class="tuifan-carousel__track" data-tuifan-carousel-track>' . implode( '', $tuifan_cards ) . '</div></div><div class="tuifan-carousel__controls" data-tuifan-carousel-controls hidden><button type="button" data-tuifan-carousel-prev aria-label="' . esc_attr__( 'Previous item', 'tuifan' ) . '">←</button><span class="tuifan-carousel__status" data-tuifan-carousel-status aria-live="polite" aria-atomic="true"></span><button type="button" data-tuifan-carousel-next aria-label="' . esc_attr__( 'Next item', 'tuifan' ) . '">→</button></div></section>';
echo $tuifan_leaf( 'tuifan/testimonial-carousel', $tuifan_attrs, $tuifan_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
unset( $tuifan_leaf, $tuifan_quotes, $tuifan_cards, $tuifan_carousel_meta, $tuifan_attrs, $tuifan_html );
