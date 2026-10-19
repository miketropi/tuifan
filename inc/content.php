<?php
/**
 * Shared content helpers: reading time, entry rows, pagination, archive filters.
 *
 * Used by the child blocks (`tuifan/entry-list`, `tuifan/archive-controls`,
 * `tuifan/reading-time`) and by the CLI starter-site seeder.
 *
 * @package Tuifan
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reading time in minutes for a post (200 words per minute, minimum 1).
 *
 * Shortcodes, block delimiters and HTML are stripped before counting.
 * Returns 0 when there is no valid post to measure.
 *
 * @param int $post_id Post ID.
 *
 * @return int Minutes, or 0 when unavailable.
 */
function tuifan_get_reading_minutes( int $post_id ): int {
	$post = $post_id > 0 ? get_post( $post_id ) : null;

	if ( ! $post instanceof WP_Post || 'attachment' === $post->post_type ) {
		return 0;
	}

	$text = wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ), true );

	if ( '' === trim( $text ) ) {
		return 1;
	}

	$words = preg_split( '/[\s\x{00A0}]+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
	$count = is_array( $words ) ? count( $words ) : 0;

	return max( 1, (int) ceil( $count / 200 ) );
}

/**
 * Blog (posts page) permalink, falling back to a public post-type query.
 */
function tuifan_get_posts_url(): string {
	$page_for_posts = (int) get_option( 'page_for_posts' );

	if ( $page_for_posts > 0 ) {
		$permalink = get_permalink( $page_for_posts );

		if ( is_string( $permalink ) && '' !== $permalink ) {
			return $permalink;
		}
	}

	return add_query_arg( 'post_type', 'post', home_url( '/' ) );
}

/**
 * Search view URL (blank `s` lists every published post).
 *
 * @param string $term Optional pre-filled search term.
 */
function tuifan_get_search_url( string $term = '' ): string {
	return add_query_arg(
		array(
			'post_type' => 'post',
			's'         => $term,
		),
		home_url( '/' )
	);
}

/**
 * Resolve a local destination used by extended-section patterns.
 *
 * @param string $kind Archive, category, page, or post.
 * @param string $slug Object slug when required.
 */
function tuifan_get_section_pattern_url( string $kind, string $slug = '' ): string {
	if ( 'archive' === $kind ) {
		return tuifan_get_posts_url();
	}

	if ( 'category' === $kind ) {
		$term = get_term_by( 'slug', $slug, 'category' );

		if ( $term instanceof WP_Term ) {
			$url = get_term_link( $term );

			if ( is_string( $url ) && '' !== $url ) {
				return $url;
			}
		}

		$labels = array(
			'getting-started' => __( 'getting started', 'tuifan' ),
			'safety'          => __( 'safety', 'tuifan' ),
			'workflows'       => __( 'workflows', 'tuifan' ),
		);

		return tuifan_get_search_url( $labels[ $slug ] ?? str_replace( '-', ' ', $slug ) );
	}

	if ( 'page' === $kind ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $page instanceof WP_Post && is_post_publicly_viewable( $page ) && '' === $page->post_password ) {
			$url = get_permalink( $page );

			if ( is_string( $url ) && '' !== $url ) {
				return $url;
			}
		}

		return tuifan_get_search_url( $slug );
	}

	if ( 'post' === $kind ) {
		$post = get_page_by_path( $slug, OBJECT, 'post' );

		if ( $post instanceof WP_Post && is_post_publicly_viewable( $post ) && '' === $post->post_password ) {
			$url = get_permalink( $post );

			if ( is_string( $url ) && '' !== $url ) {
				return $url;
			}
		}

		$terms = array(
			'agent-permissions' => __( 'safer permissions', 'tuifan' ),
			'agent-context'     => __( 'better context', 'tuifan' ),
			'agent-evaluation'  => __( 'agent evaluation', 'tuifan' ),
		);

		return tuifan_get_search_url( $terms[ $slug ] ?? str_replace( '-', ' ', $slug ) );
	}

	return tuifan_get_posts_url();
}

/**
 * Remove the parent's prose hook classes from Tuifan post content.
 *
 * WordPress core gives `core/post-content` the `entry-content` wrapper class,
 * and Nextora's `@layer components` prose layer targets
 * `:is(.entry-content, .wp-block-post-content)` with a very high-specificity
 * selector chain. Tuifan owns its own typography (`assets/css/theme.css`
 * `.tuifan-prose`, `.tuifan-article__body`), so those hook tokens are removed
 * from the wrapper tag instead of escalating selectors against them. Only the
 * wrapper's opening tag is touched, never text inside the content.
 *
 * @param string               $block_content Rendered block HTML.
 * @param array<string, mixed> $block         Parsed block.
 */
function tuifan_strip_post_content_hooks( string $block_content, array $block ): string {
	if ( ! isset( $block['blockName'] ) || 'core/post-content' !== $block['blockName'] ) {
		return $block_content;
	}

	$first_tag_end = strpos( $block_content, '>' );

	if ( false === $first_tag_end ) {
		return $block_content;
	}

	$opening = substr( $block_content, 0, $first_tag_end + 1 );
	$rest    = substr( $block_content, $first_tag_end + 1 );

	foreach ( array( 'wp-block-post-content', 'entry-content' ) as $hook_class ) {
		$opening = preg_replace( '/\s*' . preg_quote( $hook_class, '/' ) . '(?![\w-])/', '', $opening );
	}

	return $opening . $rest;
}
add_filter( 'render_block', 'tuifan_strip_post_content_hooks', 10, 2 );

/**
 * Missing featured image: render the intentional artwork-unavailable panel.
 *
 * Scoped to the Tuifan single template: only the `core/post-featured-image`
 * block carrying the `tuifan-post-media` class is affected.
 *
 * @param string               $block_content Rendered block HTML.
 * @param array<string, mixed> $block         Parsed block.
 */
function tuifan_post_media_fallback( string $block_content, array $block ): string {
	if ( ! isset( $block['blockName'] ) || 'core/post-featured-image' !== $block['blockName'] ) {
		return $block_content;
	}

	$classes = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';

	if ( ! str_contains( ' ' . $classes . ' ', ' tuifan-post-media ' ) ) {
		return $block_content;
	}

	if ( '' !== trim( $block_content ) ) {
		return $block_content;
	}

	return tuifan_get_media_fallback( false );
}
add_filter( 'render_block_core/post-featured-image', 'tuifan_post_media_fallback', 10, 2 );

/**
 * Current `source=latest` page number (always >= 1).
 *
 * The value is read straight from the request instead of being registered as a
 * public query var: registering it makes WordPress classify `/?tuifan_page=2`
 * as the blog index (the front page only resolves when the request carries no
 * recognised query vars), which breaks pagination of the home entry list.
 */
function tuifan_get_latest_page(): int {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only pagination value.
	$raw = isset( $_GET['tuifan_page'] ) ? wp_unslash( $_GET['tuifan_page'] ) : 0;
	$page = is_scalar( $raw ) ? (int) $raw : 0;

	// Negative and non-numeric values fall back to the first page (absint()
	// would turn -5 into 5).
	return max( 1, $page );
}

/**
 * URL for a `source=latest` page, scoped to the front page.
 *
 * @param int $page Page number.
 */
function tuifan_get_latest_page_url( int $page ): string {
	if ( $page <= 1 ) {
		return home_url( '/' );
	}

	return add_query_arg( 'tuifan_page', $page, home_url( '/' ) );
}

/**
 * Keep the main blog/search/archive query on the Tuifan entry cadence.
 *
 * Only the front-end main query is touched: secondary queries, admin and REST
 * requests keep the caller's own arguments.
 *
 * @param WP_Query $query Query being prepared.
 */
function tuifan_configure_main_query( $query ): void {
	if ( ! $query instanceof WP_Query ) {
		return;
	}

	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( ! $query->is_main_query() ) {
		return;
	}

	if ( ! ( $query->is_home() || $query->is_search() || $query->is_archive() ) ) {
		return;
	}

	$query->set( 'posts_per_page', 2 );

	if ( $query->is_search() ) {
		$query->set( 'post_type', 'post' );
	}
}
add_action( 'pre_get_posts', 'tuifan_configure_main_query' );

/**
 * Entry "type" label shown before the category.
 *
 * @param WP_Post $post Post object.
 */
function tuifan_get_entry_type_label( WP_Post $post ): string {
	if ( 'post' === $post->post_type ) {
		return __( 'Reader guide', 'tuifan' );
	}

	$type_object = get_post_type_object( $post->post_type );

	if ( $type_object instanceof WP_Post_Type && isset( $type_object->labels->singular_name ) ) {
		return (string) $type_object->labels->singular_name;
	}

	return $post->post_type;
}

/**
 * Plain-text excerpt for an entry row.
 *
 * Uses the stored excerpt when present, otherwise trims the content to 24 words.
 *
 * @param WP_Post $post Post object.
 */
function tuifan_get_entry_excerpt( WP_Post $post ): string {
	$excerpt = trim( wp_strip_all_tags( (string) $post->post_excerpt ) );

	if ( '' !== $excerpt ) {
		return $excerpt;
	}

	$content = wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ), true );

	return wp_trim_words( $content, 24, '…' );
}

/**
 * Inline arrow icon used by links and pagination.
 *
 * @param string $direction `left`, `right` or `up-right`.
 * @param int    $size      Width/height in pixels.
 */
function tuifan_get_arrow_icon( string $direction = 'right', int $size = 18 ): string {
	$paths = array(
		'left'     => '<path d="M19 12H5"/><path d="m12 19-7-7 7-7"/>',
		'right'    => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'up-right' => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
		'down'     => '<path d="m6 9 6 6 6-6"/>',
	);

	$path = $paths[ $direction ] ?? $paths['right'];

	return sprintf(
		'<svg class="tuifan-icon" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		$size,
		$path
	);
}

/**
 * Intentional "artwork not supplied" panel, matching the reference design.
 *
 * @param bool $compact Compact (4:3, label only) variant used inside entry rows.
 */
function tuifan_get_media_fallback( bool $compact = true ): string {
	$icon = '<svg class="tuifan-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-4-4a2 2 0 0 0-2.8 0L6 21"/></svg>';

	$classes = 'tuifan-media-fallback' . ( $compact ? ' tuifan-media-fallback--small' : '' );

	return sprintf(
		'<div class="%1$s" role="img" aria-label="%2$s">%3$s<span>%4$s</span>%5$s</div>',
		esc_attr( $classes ),
		esc_attr__( 'Feature image placeholder — artwork not supplied', 'tuifan' ),
		$icon,
		esc_html__( 'FEATURE IMAGE', 'tuifan' ),
		$compact ? '' : '<small>' . esc_html__( 'Artwork not supplied', 'tuifan' ) . '</small>'
	);
}

/**
 * Responsive featured image markup, or the intentional fallback panel.
 *
 * @param WP_Post $post    Post object.
 * @param bool    $compact Whether the fallback uses the small variant.
 */
function tuifan_get_entry_media( WP_Post $post, bool $compact = true ): string {
	$image = get_the_post_thumbnail(
		$post,
		'medium_large',
		array(
			'class'    => 'tuifan-entry__image',
			'decoding' => 'async',
			'alt'      => esc_attr( get_the_title( $post ) ),
		)
	);

	if ( is_string( $image ) && '' !== trim( $image ) ) {
		return $image;
	}

	return tuifan_get_media_fallback( $compact );
}

/**
 * Term links for an entry row.
 *
 * @param WP_Post           $post     Post object.
 * @param array<int,string> $taxonomy Taxonomies to include.
 */
function tuifan_get_entry_terms( WP_Post $post, array $taxonomy = array( 'post_tag' ) ): string {
	$markup = '';

	foreach ( $taxonomy as $tax ) {
		if ( ! is_taxonomy_viewable( $tax ) ) {
			continue;
		}

		$terms = get_the_terms( $post, $tax );

		if ( ! is_array( $terms ) || array() === $terms ) {
			continue;
		}

		foreach ( $terms as $term ) {
			if ( ! $term instanceof WP_Term ) {
				continue;
			}

			$link = get_term_link( $term );

			if ( $link instanceof WP_Error ) {
				continue;
			}

			$markup .= sprintf(
				'<a class="tuifan-tag" href="%1$s" rel="tag">%2$s</a>',
				esc_url( (string) $link ),
				esc_html( $term->name )
			);
		}
	}

	return $markup;
}

/**
 * Primary category name for an entry row meta line.
 *
 * @param WP_Post $post Post object.
 */
function tuifan_get_entry_category( WP_Post $post ): string {
	if ( ! is_taxonomy_viewable( 'category' ) ) {
		return '';
	}

	$terms = get_the_terms( $post, 'category' );

	if ( ! is_array( $terms ) || array() === $terms ) {
		return '';
	}

	$term = reset( $terms );

	return $term instanceof WP_Term ? $term->name : '';
}

/**
 * Build the numbered entry rows.
 *
 * @param array<int,WP_Post> $posts Posts to render.
 *
 * @return string Rows markup, or an empty string when there are no posts.
 */
function tuifan_get_entry_rows( array $posts ): string {
	$markup = '';

	foreach ( $posts as $post ) {
		if ( ! $post instanceof WP_Post ) {
			continue;
		}

		$permalink = get_permalink( $post );

		if ( ! is_string( $permalink ) || '' === $permalink ) {
			continue;
		}

		$type_label = tuifan_get_entry_type_label( $post );
		$category   = tuifan_get_entry_category( $post );
		$excerpt    = tuifan_get_entry_excerpt( $post );
		$terms      = tuifan_get_entry_terms( $post );
		$minutes    = tuifan_get_reading_minutes( (int) $post->ID );

		$meta_parts = array();
		if ( '' !== $type_label ) {
			$meta_parts[] = '<span class="tuifan-entry__type">' . esc_html( $type_label ) . '</span>';
		}
		if ( '' !== $category ) {
			$meta_parts[] = '<span>/ ' . esc_html( strtolower( $category ) ) . '</span>';
		}

		$meta = '' !== implode( '', $meta_parts )
			? '<div class="tuifan-entry__meta">' . implode( '', $meta_parts ) . '</div>'
			: '';

		$bottom = '';

		if ( '' !== $terms ) {
			$bottom .= '<div class="tuifan-entry__tags">' . $terms . '</div>';
		}

		if ( $minutes > 0 ) {
			$bottom .= sprintf(
				'<span class="tuifan-entry__time">%s</span>',
				esc_html(
					sprintf(
						/* translators: %d: reading time in minutes. */
						_n( '%d min read', '%d min read', $minutes, 'tuifan' ),
						$minutes
					)
				)
			);
		}

		if ( '' !== $bottom ) {
			$bottom = '<div class="tuifan-entry__bottom">' . $bottom . '</div>';
		}

		$markup .= sprintf(
			'<article class="tuifan-entry">' .
				'<div class="tuifan-entry__media">%1$s</div>' .
				'<div class="tuifan-entry__copy">%2$s' .
					'<h3 class="tuifan-entry__title"><a href="%3$s">%4$s</a></h3>' .
					'<p class="tuifan-entry__excerpt">%5$s</p>%6$s' .
				'</div>' .
			'</article>',
			tuifan_get_entry_media( $post ),
			$meta,
			esc_url( $permalink ),
			esc_html( get_the_title( $post ) ),
			esc_html( $excerpt ),
			$bottom
		);
	}

	return $markup;
}

/**
 * Server-rendered pagination: range, previous, numbers, next.
 *
 * @param array{total:int,per_page:int,current:int,page_url:callable,label:string} $args Pagination arguments.
 *
 * @return string Markup, or an empty string when a single page holds everything.
 */
function tuifan_get_pagination( array $args ): string {
	$total    = max( 0, (int) $args['total'] );
	$per_page = max( 1, (int) $args['per_page'] );
	$current  = max( 1, (int) $args['current'] );
	$page_url = $args['page_url'];
	$label    = (string) $args['label'];
	$pages    = (int) ceil( $total / $per_page );

	if ( $pages <= 1 ) {
		return '';
	}

	$current = min( $current, $pages );

	$start = $total > 0 ? ( ( $current - 1 ) * $per_page ) + 1 : 0;
	$end   = min( $current * $per_page, $total );

	$range = sprintf(
		'%02d–%02d / %02d %s',
		$start,
		$end,
		$total,
		__( 'entries', 'tuifan' )
	);

	$controls = '';

	if ( $current > 1 ) {
		$controls .= sprintf(
			'<a class="tuifan-pagination__step" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( (string) call_user_func( $page_url, $current - 1 ) ),
			esc_attr__( 'Previous page', 'tuifan' ),
			tuifan_get_arrow_icon( 'left' )
		);
	} else {
		$controls .= sprintf(
			'<span class="tuifan-pagination__step is-disabled" aria-hidden="true">%s</span>',
			tuifan_get_arrow_icon( 'left' )
		);
	}

	for ( $page = 1; $page <= $pages; $page++ ) {
		if ( $page === $current ) {
			$controls .= sprintf(
				'<a class="tuifan-pagination__number" href="%1$s" aria-current="page">%2$s</a>',
				esc_url( (string) call_user_func( $page_url, $page ) ),
				esc_html( sprintf( '%02d', $page ) )
			);
			continue;
		}

		$controls .= sprintf(
			'<a class="tuifan-pagination__number" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( (string) call_user_func( $page_url, $page ) ),
			/* translators: %d: page number. */
			esc_attr( sprintf( __( 'Page %d', 'tuifan' ), $page ) ),
			esc_html( sprintf( '%02d', $page ) )
		);
	}

	if ( $current < $pages ) {
		$controls .= sprintf(
			'<a class="tuifan-pagination__step" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( (string) call_user_func( $page_url, $current + 1 ) ),
			esc_attr__( 'Next page', 'tuifan' ),
			tuifan_get_arrow_icon( 'right' )
		);
	} else {
		$controls .= sprintf(
			'<span class="tuifan-pagination__step is-disabled" aria-hidden="true">%s</span>',
			tuifan_get_arrow_icon( 'right' )
		);
	}

	return sprintf(
		'<nav class="tuifan-pagination" aria-label="%1$s"><span class="tuifan-pagination__range" role="status" aria-live="polite">%2$s</span><div class="tuifan-pagination__controls">%3$s</div></nav>',
		esc_attr( $label ),
		esc_html( $range ),
		$controls
	);
}

/**
 * Empty state for entry lists.
 *
 * @param string $message    Headline.
 * @param string $hint       Supporting sentence.
 * @param bool   $with_link  Whether to append a recovery link.
 * @param string $link_url   Recovery link URL; falls back to the blog index.
 * @param string $link_label Recovery link label.
 */
function tuifan_get_empty_state( string $message, string $hint = '', bool $with_link = true, string $link_url = '', string $link_label = '' ): string {
	$markup = sprintf(
		'<div class="tuifan-empty"><h2>%s</h2>',
		esc_html( $message )
	);

	if ( '' !== $hint ) {
		$markup .= sprintf( '<p>%s</p>', esc_html( $hint ) );
	}

	if ( $with_link ) {
		$markup .= sprintf(
			'<a class="tuifan-text-link" href="%1$s">%2$s%3$s</a>',
			esc_url( '' !== $link_url ? $link_url : tuifan_get_posts_url() ),
			esc_html( '' !== $link_label ? $link_label : __( 'Browse all entries', 'tuifan' ) ),
			tuifan_get_arrow_icon( 'right' )
		);
	}

	return $markup . '</div>';
}

/**
 * Category filter links for archive controls and search.
 *
 * Terms come from `get_categories( array( 'hide_empty' => true ) )`; links keep
 * the current search term on search views and reset pagination. When the
 * taxonomy is unavailable only the "All" filter is returned.
 *
 * @return list<array{ label: string, url: string, active: bool }>
 */
function tuifan_get_category_filters(): array {
	$is_search = is_search();
	$raw_term  = get_search_query( false );
	$term      = is_string( $raw_term ) ? $raw_term : '';
	$current   = 0;

	if ( is_category() ) {
		$queried = get_queried_object();

		if ( $queried instanceof WP_Term ) {
			$current = (int) $queried->term_id;
		}
	} else {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter value.
		$current = isset( $_GET['cat'] ) ? absint( wp_unslash( $_GET['cat'] ) ) : absint( get_query_var( 'cat' ) );
	}

	$all_url = $is_search
		? tuifan_get_search_url( $term )
		: tuifan_get_posts_url();

	$filters = array(
		array(
			'label'  => __( 'All', 'tuifan' ),
			'url'    => $all_url,
			'active' => 0 === $current,
		),
	);

	$terms = get_categories( array( 'hide_empty' => true ) );

	if ( ! is_array( $terms ) || array() === $terms ) {
		return $filters;
	}

	foreach ( $terms as $term_object ) {
		if ( ! $term_object instanceof WP_Term ) {
			continue;
		}

		if ( $is_search ) {
			$args = array(
				'cat'       => (int) $term_object->term_id,
				'post_type' => 'post',
				's'         => $term,
			);

			$url = add_query_arg( $args, home_url( '/' ) );
		} else {
			$term_url = get_term_link( $term_object );

			if ( $term_url instanceof WP_Error ) {
				continue;
			}

			$url = (string) $term_url;
		}

		$filters[] = array(
			'label'  => $term_object->name,
			'url'    => $url,
			'active' => (int) $term_object->term_id === $current,
		);
	}

	return $filters;
}
