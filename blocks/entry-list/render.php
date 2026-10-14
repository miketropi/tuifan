<?php
/**
 * Entry List — dynamic render.
 *
 * `source=main` reuses the already-fetched main query (blog, archive, search).
 * `source=latest` runs its own published-post query, paginated through the
 * `tuifan_page` query var and scoped to the front page.
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

$tuifan_source = isset( $attributes['source'] ) ? (string) $attributes['source'] : 'latest';

if ( ! in_array( $tuifan_source, array( 'latest', 'main' ), true ) ) {
	$tuifan_source = 'latest';
}

$tuifan_per_page = isset( $attributes['postsPerPage'] ) ? (int) $attributes['postsPerPage'] : 2;
$tuifan_per_page = max( 1, min( 20, $tuifan_per_page ) );

$tuifan_heading = isset( $attributes['heading'] ) ? trim( (string) $attributes['heading'] ) : '';

if ( '' === $tuifan_heading && ! isset( $attributes['heading'] ) ) {
	$tuifan_heading = '~/posts — latest entries';
}

$tuifan_is_editor_preview = ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_admin();
$tuifan_uses_main_query   = 'main' === $tuifan_source
	&& ! $tuifan_is_editor_preview
	&& isset( $GLOBALS['wp_query'] )
	&& $GLOBALS['wp_query'] instanceof WP_Query
	&& ( is_home() || is_search() || is_archive() );

$tuifan_posts    = array();
$tuifan_total    = 0;
$tuifan_current  = 1;
$tuifan_page_url = null;

if ( $tuifan_uses_main_query ) {
	/**
	 * Main query, already fetched by WordPress.
	 *
	 * @var WP_Query $tuifan_main_query
	 */
	$tuifan_main_query = $GLOBALS['wp_query'];

	$tuifan_posts   = is_array( $tuifan_main_query->posts ) ? $tuifan_main_query->posts : array();
	$tuifan_total   = (int) $tuifan_main_query->found_posts;
	$tuifan_current = max( 1, (int) get_query_var( 'paged' ) );

	$tuifan_query_per_page = (int) $tuifan_main_query->get( 'posts_per_page' );

	if ( $tuifan_query_per_page > 0 ) {
		$tuifan_per_page = $tuifan_query_per_page;
	}

	$tuifan_page_url = static function ( int $page ): string {
		return (string) get_pagenum_link( $page, false );
	};
} else {
	$tuifan_current = $tuifan_is_editor_preview ? 1 : tuifan_get_latest_page();

	$tuifan_query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $tuifan_per_page,
			'paged'               => $tuifan_current,
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		)
	);

	$tuifan_posts = is_array( $tuifan_query->posts ) ? $tuifan_query->posts : array();
	$tuifan_total = (int) $tuifan_query->found_posts;

	wp_reset_postdata();

	$tuifan_page_url = static function ( int $page ): string {
		return tuifan_get_latest_page_url( $page );
	};
}

/*
 * A page beyond the last one makes WP_Query report zero found posts and zero
 * max pages, so "past the end" is detected by an empty result on page > 1.
 */
$tuifan_out_of_range = $tuifan_current > 1 && $tuifan_total < 1;
$tuifan_rows         = $tuifan_out_of_range ? '' : tuifan_get_entry_rows( $tuifan_posts );
$tuifan_wrapper      = get_block_wrapper_attributes(
	array(
		'class' => 'tuifan-list tuifan-list--' . $tuifan_source,
	)
);

$tuifan_title = '';

if ( '' !== $tuifan_heading ) {
	$tuifan_title .= sprintf(
		'<h2 class="tuifan-section-title__heading">%s</h2>',
		esc_html( $tuifan_heading )
	);
}

$tuifan_title .= sprintf(
	'<span class="tuifan-section-title__count">%s</span>',
	esc_html(
		sprintf(
			/* translators: %s: zero-padded entry count. */
			_n( '%s file', '%s files', $tuifan_total, 'tuifan' ),
			sprintf( '%02d', $tuifan_total )
		)
	)
);

$tuifan_body = '';

if ( '' !== $tuifan_rows ) {
	$tuifan_body = sprintf(
		'<div class="tuifan-entries" style="counter-reset: tuifan-entry %d">%s</div>',
		(int) ( ( $tuifan_current - 1 ) * $tuifan_per_page ),
		$tuifan_rows
	);
} elseif ( $tuifan_out_of_range ) {
	$tuifan_body = tuifan_get_empty_state(
		__( 'No entries found.', 'tuifan' ),
		__( 'That page is past the end of the archive.', 'tuifan' ),
		true,
		(string) call_user_func( $tuifan_page_url, 1 ),
		__( 'Back to the first page', 'tuifan' )
	);
} elseif ( is_search() && $tuifan_uses_main_query ) {
	$tuifan_body = tuifan_get_empty_state(
		__( 'No entries found.', 'tuifan' ),
		__( 'Try a different term or clear the category filter.', 'tuifan' ),
		true,
		tuifan_get_posts_url(),
		__( 'Browse all entries', 'tuifan' )
	);
} else {
	$tuifan_body = tuifan_get_empty_state(
		__( 'No entries found.', 'tuifan' ),
		__( 'Nothing is published here yet.', 'tuifan' )
	);
}

$tuifan_pagination = '' === $tuifan_rows
	? ''
	: tuifan_get_pagination(
		array(
			'total'    => $tuifan_total,
			'per_page' => $tuifan_per_page,
			'current'  => $tuifan_current,
			'page_url' => $tuifan_page_url,
			'label'    => __( 'Article pages', 'tuifan' ),
		)
	);

printf(
	'<div %1$s><div class="tuifan-section-title">%2$s</div>%3$s%4$s</div>',
	$tuifan_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes().
	$tuifan_title, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped while building.
	$tuifan_body, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped while building.
	$tuifan_pagination // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped while building.
);
