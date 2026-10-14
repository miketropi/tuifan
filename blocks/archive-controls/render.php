<?php
/**
 * Archive Controls — dynamic render.
 *
 * A real GET form (no JavaScript required) plus category filter links that
 * keep the current search term and reset pagination.
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

$tuifan_uid       = wp_unique_id( 'tuifan-search-' );
$tuifan_filters   = tuifan_get_category_filters();
$tuifan_search    = (string) get_search_query( false );
$tuifan_current   = is_category() ? (int) get_queried_object_id() : absint( get_query_var( 'cat' ) );
$tuifan_autofocus = is_search();
$tuifan_wrapper   = get_block_wrapper_attributes( array( 'class' => 'tuifan-controls' ) );

$tuifan_form  = '';
$tuifan_form .= sprintf(
	'<form class="tuifan-search" role="search" method="get" action="%1$s">',
	esc_url( home_url( '/' ) )
);
$tuifan_form .= sprintf(
	'<label class="screen-reader-text" for="%1$s">%2$s</label>',
	esc_attr( $tuifan_uid ),
	esc_html__( 'Search posts', 'tuifan' )
);
$tuifan_form .= '<span class="tuifan-search__prefix" aria-hidden="true">/</span>';
$tuifan_form .= sprintf(
	'<input class="tuifan-search__input" id="%1$s" type="search" name="s" value="%2$s" placeholder="%3$s" autocomplete="off"%4$s />',
	esc_attr( $tuifan_uid ),
	esc_attr( $tuifan_search ),
	esc_attr__( 'Try “permissions” or “context”…', 'tuifan' ),
	$tuifan_autofocus ? ' data-tuifan-autofocus="1"' : ''
);
$tuifan_form .= '<input type="hidden" name="post_type" value="post" />';

if ( $tuifan_current > 0 ) {
	$tuifan_form .= sprintf(
		'<input type="hidden" name="cat" value="%d" />',
		$tuifan_current
	);
}

$tuifan_form .= sprintf(
	'<button class="tuifan-search__submit" type="submit">%1$s<span class="tuifan-search__submit-label">%2$s</span></button>',
	tuifan_get_arrow_icon( 'right' ),
	esc_html__( 'Search', 'tuifan' )
);
$tuifan_form .= '</form>';

$tuifan_filter_markup = '';

if ( count( $tuifan_filters ) > 1 ) {
	$tuifan_filter_markup .= sprintf(
		'<div class="tuifan-filters" role="group" aria-label="%s">',
		esc_attr__( 'Filter by topic', 'tuifan' )
	);

	foreach ( $tuifan_filters as $tuifan_filter ) {
		$tuifan_filter_markup .= sprintf(
			'<a class="tuifan-filter%1$s" href="%2$s"%3$s>%4$s</a>',
			$tuifan_filter['active'] ? ' is-active' : '',
			esc_url( (string) $tuifan_filter['url'] ),
			$tuifan_filter['active'] ? ' aria-current="true"' : '',
			esc_html( (string) $tuifan_filter['label'] )
		);
	}

	$tuifan_filter_markup .= '</div>';
}

printf(
	'<div %1$s>%2$s%3$s</div>',
	$tuifan_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes().
	$tuifan_form, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped while building.
	$tuifan_filter_markup // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped while building.
);
