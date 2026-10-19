<?php
/**
 * Posts Carousel — dynamic render.
 *
 * The markup is built as strings and printed once, matching the child theme's
 * other dynamic renderers: every value is escaped where it is interpolated and
 * the whole payload is emitted by a single `printf()`.
 *
 * @package Tuifan
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tuifan_count       = max( 1, min( 12, isset( $attributes['postsToShow'] ) ? (int) $attributes['postsToShow'] : 4 ) );
$tuifan_category_id = isset( $attributes['categoryId'] ) ? max( 0, (int) $attributes['categoryId'] ) : 0;
$tuifan_order_by    = isset( $attributes['orderBy'] ) && in_array( $attributes['orderBy'], array( 'date', 'title' ), true ) ? (string) $attributes['orderBy'] : 'date';
$tuifan_order_raw   = isset( $attributes['order'] ) ? strtoupper( (string) $attributes['order'] ) : 'DESC';
$tuifan_order       = in_array( $tuifan_order_raw, array( 'ASC', 'DESC' ), true ) ? $tuifan_order_raw : 'DESC';
$tuifan_level       = max( 2, min( 4, isset( $attributes['headingLevel'] ) ? (int) $attributes['headingLevel'] : 2 ) );
$tuifan_eyebrow     = isset( $attributes['eyebrow'] ) ? trim( (string) $attributes['eyebrow'] ) : '';
$tuifan_heading     = isset( $attributes['heading'] ) ? trim( (string) $attributes['heading'] ) : '';
$tuifan_description = isset( $attributes['description'] ) ? trim( (string) $attributes['description'] ) : '';
$tuifan_carousel    = isset( $attributes['carouselLabel'] ) ? trim( (string) $attributes['carouselLabel'] ) : '';
$tuifan_label       = '' !== $tuifan_carousel ? $tuifan_carousel : __( 'Reading queue', 'tuifan' );

$tuifan_query_args = array(
	'post_type'              => 'post',
	'post_status'            => 'publish',
	'has_password'           => false,
	'posts_per_page'         => $tuifan_count,
	'ignore_sticky_posts'    => true,
	'no_found_rows'          => true,
	'orderby'                => $tuifan_order_by,
	'order'                  => $tuifan_order,
	'update_post_meta_cache' => true,
	'update_post_term_cache' => true,
);

$tuifan_invalid_category = false;

/*
 * A category that no longer exists must render the empty state instead of
 * silently widening the query to every published post.
 */
if ( $tuifan_category_id > 0 ) {
	$tuifan_term = get_term( $tuifan_category_id, 'category' );

	if ( $tuifan_term instanceof WP_Term ) {
		$tuifan_query_args['cat'] = $tuifan_category_id;
	} else {
		$tuifan_invalid_category = true;
	}
}

$tuifan_posts = array();

if ( ! $tuifan_invalid_category ) {
	$tuifan_query = new WP_Query( $tuifan_query_args );
	$tuifan_posts = array_values( array_filter( $tuifan_query->posts, static fn( $post ): bool => $post instanceof WP_Post ) );
}

$tuifan_head = '';

if ( '' !== $tuifan_eyebrow || '' !== $tuifan_heading || '' !== $tuifan_description ) {
	$tuifan_head .= '<header class="tuifan-section__head">';

	if ( '' !== $tuifan_eyebrow ) {
		$tuifan_head .= '<p class="tuifan-section__eyebrow">' . wp_kses_post( $tuifan_eyebrow ) . '</p>';
	}

	if ( '' !== $tuifan_heading ) {
		$tuifan_head .= '<h' . $tuifan_level . ' class="tuifan-section__heading">' . wp_kses_post( $tuifan_heading ) . '</h' . $tuifan_level . '>';
	}

	if ( '' !== $tuifan_description ) {
		$tuifan_head .= '<p class="tuifan-section__description">' . wp_kses_post( $tuifan_description ) . '</p>';
	}

	$tuifan_head .= '</header>';
}

$tuifan_cards = '';

foreach ( $tuifan_posts as $tuifan_post ) {
	$tuifan_post_title = trim( get_the_title( $tuifan_post ) );
	$tuifan_title      = '' !== $tuifan_post_title ? $tuifan_post_title : __( '(Untitled)', 'tuifan' );
	$tuifan_permalink  = (string) get_permalink( $tuifan_post );
	$tuifan_category   = tuifan_get_entry_category( $tuifan_post );
	$tuifan_minutes    = tuifan_get_reading_minutes( (int) $tuifan_post->ID );
	$tuifan_excerpt    = tuifan_get_entry_excerpt( $tuifan_post );
	$tuifan_author     = (string) get_the_author_meta( 'display_name', (int) $tuifan_post->post_author );
	$tuifan_date       = (string) get_the_date( '', $tuifan_post );
	$tuifan_machine    = (string) get_the_date( DATE_W3C, $tuifan_post );
	$tuifan_terms      = tuifan_get_entry_terms( $tuifan_post );
	$tuifan_media      = has_post_thumbnail( $tuifan_post )
		? (string) get_the_post_thumbnail( $tuifan_post, 'medium_large', array( 'loading' => 'lazy' ) )
		: tuifan_get_media_fallback( false );

	$tuifan_meta = '';

	if ( '' !== $tuifan_category ) {
		$tuifan_meta .= '<span>' . esc_html( $tuifan_category ) . '</span>';
	}

	$tuifan_meta .= sprintf(
		'<span>%s</span>',
		esc_html(
			sprintf(
				/* translators: %d: estimated reading time in minutes. */
				__( '%d min read', 'tuifan' ),
				$tuifan_minutes
			)
		)
	);

	$tuifan_byline = '';

	if ( '' !== $tuifan_author || '' !== $tuifan_date ) {
		$tuifan_byline .= '<div class="tuifan-post-card__byline">';

		if ( '' !== $tuifan_author ) {
			$tuifan_byline .= '<span>' . esc_html( $tuifan_author ) . '</span>';
		}

		if ( '' !== $tuifan_date ) {
			$tuifan_byline .= '<time datetime="' . esc_attr( $tuifan_machine ) . '">' . esc_html( $tuifan_date ) . '</time>';
		}

		$tuifan_byline .= '</div>';
	}

	$tuifan_cards .= '<article class="tuifan-post-card" data-tuifan-carousel-item>';
	$tuifan_cards .= '<div class="tuifan-post-card__media">' . $tuifan_media . '</div>';
	$tuifan_cards .= '<div class="tuifan-post-card__body">';
	$tuifan_cards .= '<div class="tuifan-post-card__meta">' . $tuifan_meta . '</div>';
	$tuifan_cards .= '<h3 class="tuifan-post-card__title"><a href="' . esc_url( $tuifan_permalink ) . '">' . esc_html( $tuifan_title ) . '</a></h3>';

	if ( '' !== $tuifan_excerpt ) {
		$tuifan_cards .= '<p class="tuifan-post-card__excerpt">' . esc_html( $tuifan_excerpt ) . '</p>';
	}

	$tuifan_cards .= $tuifan_byline;

	if ( '' !== $tuifan_terms ) {
		$tuifan_cards .= '<div class="tuifan-post-card__tags">' . $tuifan_terms . '</div>';
	}

	$tuifan_cards .= sprintf(
		'<a class="tuifan-section__action tuifan-post-card__action" href="%1$s" aria-label="%2$s">%3$s</a>',
		esc_url( $tuifan_permalink ),
		/* translators: %s: post title. */
		esc_attr( sprintf( __( 'Read guide: %s', 'tuifan' ), $tuifan_title ) ),
		esc_html__( 'Read guide', 'tuifan' )
	);
	$tuifan_cards .= '</div></article>';
}

$tuifan_body = '';

if ( empty( $tuifan_posts ) ) {
	$tuifan_body = tuifan_get_empty_state(
		__( 'No entries found.', 'tuifan' ),
		__( 'No published posts match this selection.', 'tuifan' ),
		false
	);
} else {
	$tuifan_body  = '<div class="tuifan-carousel__viewport" data-tuifan-carousel-viewport tabindex="0" aria-roledescription="';
	$tuifan_body .= esc_attr__( 'carousel', 'tuifan' );
	$tuifan_body .= '"><div class="tuifan-carousel__track" data-tuifan-carousel-track>' . $tuifan_cards . '</div></div>';
	$tuifan_body .= '<div class="tuifan-carousel__controls" data-tuifan-carousel-controls hidden>';
	$tuifan_body .= '<button type="button" data-tuifan-carousel-prev aria-label="' . esc_attr__( 'Previous item', 'tuifan' ) . '">' . tuifan_get_arrow_icon( 'left' ) . '</button>';
	$tuifan_body .= '<span class="tuifan-carousel__status" data-tuifan-carousel-status aria-live="polite" aria-atomic="true"></span>';
	$tuifan_body .= '<button type="button" data-tuifan-carousel-next aria-label="' . esc_attr__( 'Next item', 'tuifan' ) . '">' . tuifan_get_arrow_icon() . '</button>';
	$tuifan_body .= '</div>';
}

$tuifan_wrapper = get_block_wrapper_attributes(
	array(
		'class'                            => 'tuifan-section tuifan-posts-carousel',
		'data-tuifan-carousel'             => '',
		'aria-label'                       => $tuifan_label,
		'data-tuifan-carousel-description' => __( 'carousel', 'tuifan' ),
		/* translators: 1: current item number, 2: total item count. */
		'data-tuifan-carousel-item-label'  => __( 'Item %1$s of %2$s', 'tuifan' ),
		/* translators: 1: first visible item number, 2: last visible item number, 3: total item count. */
		'data-tuifan-carousel-range'       => __( '%1$s–%2$s / %3$s', 'tuifan' ),
	)
);

printf(
	'<section %1$s>%2$s%3$s</section>',
	$tuifan_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes().
	$tuifan_head, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped while building; attributes use wp_kses_post().
	$tuifan_body // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped while building.
);
