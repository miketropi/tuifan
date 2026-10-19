<?php
/**
 * Title: FAQ
 * Slug: tuifan/faq
 * Categories: tuifan
 * Description: Native frequently asked question disclosures and reader shortcuts.
 * Keywords: faq, faps, questions, answers
 * Viewport Width: 1400
 *
 * @package Tuifan
 */

$tuifan_leaf    = static fn( string $name, array $attrs, string $html ): string => serialize_block(
	array(
		'blockName'    => $name,
		'attrs'        => $attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => $html,
		'innerContent' => array( $html ),
	)
);
$tuifan_items   = array(
	array( __( 'Who is this for?', 'tuifan' ), __( 'For curious readers who want practical AI-agent guides, better context, safer permissions, and workflows they can trust.', 'tuifan' ), true ),
	array( __( 'Do I need to know how to code?', 'tuifan' ), __( 'No. Start with “Your first AI agent,” then choose the safety or workflow guides when you want more depth.', 'tuifan' ), false ),
	array( __( 'Is the newsletter connected?', 'tuifan' ), __( 'No. The demo labels newsletter registration as preview-only; email addresses are not sent or stored.', 'tuifan' ), false ),
	array( __( 'How do I search the guides?', 'tuifan' ), __( 'Open the archive or press / on the live site to focus search. Use Tab to move through interactive controls.', 'tuifan' ), false ),
);
$tuifan_details = array_map(
	static function ( array $item ) use ( $tuifan_leaf ): string {
		$paragraph = $tuifan_leaf( 'core/paragraph', array(), '<p>' . esc_html( $item[1] ) . '</p>' );
		$html      = '<details class="wp-block-details"' . ( $item[2] ? ' open' : '' ) . '><summary>' . esc_html( $item[0] ) . '</summary>' . $paragraph . '</details>';
		return $tuifan_leaf(
			'core/details',
			array(
				'summary'     => $item[0],
				'showContent' => $item[2],
			),
			$html
		);
	},
	$tuifan_items
);
$tuifan_attrs   = array(
	'eyebrow'      => __( '~/faq — reader shortcuts', 'tuifan' ),
	'heading'      => __( 'Find your way through the archive.', 'tuifan' ),
	'description'  => __( 'Begin with the first agent, then move through permissions, context, and evaluation.', 'tuifan' ),
	'headingLevel' => 2,
);
$tuifan_html    = '<section class="wp-block-tuifan-faq tuifan-section tuifan-faq" data-tuifan-faq><div class="tuifan-faq__layout"><header class="tuifan-section__head"><p class="tuifan-section__eyebrow">' . esc_html( $tuifan_attrs['eyebrow'] ) . '</p> <h2 class="tuifan-section__heading">' . esc_html( $tuifan_attrs['heading'] ) . '</h2> <p class="tuifan-section__description">' . esc_html( $tuifan_attrs['description'] ) . '</p></header><div class="tuifan-faq__items">' . implode( '', $tuifan_details ) . '</div></div></section>';
echo $tuifan_leaf( 'tuifan/faq', $tuifan_attrs, $tuifan_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
unset( $tuifan_leaf, $tuifan_items, $tuifan_details, $tuifan_attrs, $tuifan_html );
