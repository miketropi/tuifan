<?php
/**
 * Title: CTA
 * Slug: tuifan/cta
 * Categories: tuifan
 * Description: Preview-only newsletter command banner configurable as a real link.
 * Keywords: cta, call to action, newsletter, banner
 * Viewport Width: 1400
 *
 * @package Tuifan
 */

$tuifan_attrs   = array(
	'eyebrow'      => './newsletter',
	'heading'      => __( 'More signal in your inbox.', 'tuifan' ),
	'description'  => __( 'Practical AI-agent guides on better workflows, safer permissions, and results you can trust.', 'tuifan' ),
	'headingLevel' => 2,
	'buttonLabel'  => 'preview --subscribe',
	'buttonUrl'    => '',
	'mode'         => 'preview',
);
$tuifan_html    = '<section class="wp-block-tuifan-cta tuifan-section tuifan-cta"><div class="tuifan-cta__layout"><div><p class="tuifan-section__eyebrow">' . esc_html( $tuifan_attrs['eyebrow'] ) . '</p> <h2 class="tuifan-section__heading">' . esc_html( $tuifan_attrs['heading'] ) . '</h2> <p class="tuifan-section__description">' . esc_html( $tuifan_attrs['description'] ) . '</p></div><div class="tuifan-cta__action"><span aria-hidden="true">$</span><button class="tuifan-cta__button" type="button" disabled>' . esc_html( $tuifan_attrs['buttonLabel'] ) . '</button><p>' . esc_html__( 'Template preview only. Nothing is sent or stored.', 'tuifan' ) . '</p></div></div><span class="tuifan-cta__status" aria-hidden="true">AWAITING_INPUT_</span></section>';
$tuifan_payload = serialize_block(
	array(
		'blockName'    => 'tuifan/cta',
		'attrs'        => $tuifan_attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => $tuifan_html,
		'innerContent' => array( $tuifan_html ),
	)
);

echo $tuifan_payload; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pattern payload produced by serialize_block().

unset( $tuifan_attrs, $tuifan_html, $tuifan_payload );
