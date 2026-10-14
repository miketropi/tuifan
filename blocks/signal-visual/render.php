<?php
/**
 * Signal Visual — dynamic render.
 *
 * Prints a real static composition (black shadow, dotted accretion disk, upper
 * and lower lensed arcs) that stays visible without WebGL, JavaScript or
 * motion. The front-end view script only enhances it.
 *
 * @package Tuifan
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Saved inner HTML.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tuifan_label = isset( $attributes['label'] ) ? trim( (string) $attributes['label'] ) : '';

if ( '' === $tuifan_label ) {
	$tuifan_label = __( 'Accretion field', 'tuifan' );
}

$tuifan_motion    = ! isset( $attributes['enableMotion'] ) || (bool) $attributes['enableMotion'];
$tuifan_uid       = wp_unique_id( 'tuifan-signal-' );
$tuifan_glow_id   = $tuifan_uid . '-glow';
$tuifan_axis_text = sprintf(
	/* translators: 1: configurable label, 2: motion state. */
	__( '%1$s / %2$s', 'tuifan' ),
	$tuifan_label,
	$tuifan_motion ? __( 'LIVE', 'tuifan' ) : __( 'STILL', 'tuifan' )
);

$tuifan_description = sprintf(
	/* translators: %s: configurable label. */
	__( '%s: a black-hole visualization with a dark shadow, a gravitationally lensed dotted accretion disk and a brighter approaching side.', 'tuifan' ),
	$tuifan_label
);

if ( $tuifan_motion ) {
	$tuifan_description .= ' ' . __( 'Pointer movement gently changes the viewing angle.', 'tuifan' );
}

$tuifan_description .= ' ' . __( 'Reduced motion shows a still composition.', 'tuifan' );

$tuifan_svg = sprintf(
	'<svg class="tuifan-signal__figure" viewBox="0 0 440 440" preserveAspectRatio="xMidYMid meet" aria-hidden="true" focusable="false">' .
		'<defs><radialGradient id="%1$s" cx="50%%" cy="50%%" r="50%%">' .
			'<stop offset="0%%" stop-color="var(--wp--preset--color--accent-text)" stop-opacity="0.22"/>' .
			'<stop offset="70%%" stop-color="var(--wp--preset--color--accent-text)" stop-opacity="0.04"/>' .
			'<stop offset="100%%" stop-color="var(--wp--preset--color--accent-text)" stop-opacity="0"/>' .
		'</radialGradient></defs>' .
		'<ellipse cx="220" cy="220" rx="208" ry="150" fill="url(#%1$s)"/>' .
		'<ellipse cx="220" cy="220" rx="168" ry="44" fill="none" stroke="var(--wp--preset--color--accent-text)" stroke-opacity="0.9" stroke-width="1.5" stroke-dasharray="2 7"/>' .
		'<ellipse cx="220" cy="220" rx="156" ry="34" fill="none" stroke="var(--wp--preset--color--accent-text)" stroke-opacity="0.45" stroke-width="1" stroke-dasharray="2 7"/>' .
		'<path d="M46 208 A 174 78 0 0 1 394 208" fill="none" stroke="var(--wp--preset--color--accent-text)" stroke-width="2" stroke-opacity="0.85"/>' .
		'<path d="M46 232 A 174 78 0 0 0 394 232" fill="none" stroke="var(--wp--preset--color--accent-text)" stroke-width="2" stroke-opacity="0.6"/>' .
		'<circle cx="220" cy="220" r="64" fill="var(--wp--preset--color--base)"/>' .
		'<circle cx="220" cy="220" r="64" fill="none" stroke="var(--wp--preset--color--accent-text)" stroke-opacity="0.5" stroke-width="1"/>' .
	'</svg>',
	esc_attr( $tuifan_glow_id )
);

$tuifan_wrapper = get_block_wrapper_attributes(
	array(
		'class'              => 'tuifan-signal',
		'data-tuifan-motion' => $tuifan_motion ? '1' : '0',
	)
);

printf(
	'<div %1$s><div class="tuifan-signal__stage" role="img" aria-label="%2$s" data-tuifan-signal-stage="1">%3$s' .
		'<span class="tuifan-signal__axis" aria-hidden="true">%4$s</span>' .
		'<span class="tuifan-signal__caption" aria-hidden="true"><span>%5$s</span><span>%6$s</span></span>' .
	'</div></div>',
	$tuifan_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes().
	esc_attr( $tuifan_description ),
	$tuifan_svg, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static, escaped composition.
	esc_html( $tuifan_axis_text ),
	esc_html__( 'FIG. 01 / LENSED SHADOW', 'tuifan' ),
	$tuifan_motion ? esc_html__( 'MOVE → ORBIT', 'tuifan' ) : esc_html__( 'STATIC', 'tuifan' )
);
