<?php
/**
 * Reading Time — dynamic render.
 *
 * Prints nothing when there is no valid post in context.
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

$tuifan_context_post_id = 0;

if ( isset( $block->context['postId'] ) ) {
	$tuifan_context_post_id = (int) $block->context['postId'];
}

if ( $tuifan_context_post_id < 1 ) {
	$tuifan_context_post_id = (int) get_the_ID();
}

$tuifan_minutes = tuifan_get_reading_minutes( $tuifan_context_post_id );

if ( $tuifan_minutes < 1 ) {
	return '';
}

$tuifan_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'tuifan-reading-time',
	)
);

printf(
	'<div %1$s>%2$s</div>',
	$tuifan_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes().
	esc_html(
		sprintf(
			/* translators: %d: reading time in minutes. */
			_n( '%d min read', '%d min read', $tuifan_minutes, 'tuifan' ),
			$tuifan_minutes
		)
	)
);
