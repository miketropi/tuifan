<?php
/**
 * Integration checks for the Tuifan child theme.
 *
 * Covers the query and content boundaries that are easy to get subtly wrong:
 * combined search/category pagination, reading-time word boundaries, and the
 * non-destructive repeat behaviour of the starter-site seeder.
 *
 * Run from the child theme directory:
 *
 *     source '/path/to/Local Sites/<site>/app/.envrc'
 *     wp eval-file tests/integration-checks.php
 *
 * Every check creates and removes its own disposable rows; the command exits
 * with status 1 when any check fails.
 *
 * Note: `declare( strict_types=1 )` cannot be used here — WP-CLI's
 * `eval-file` wraps the script, so a declare statement would not be the first
 * statement of the compiled script and PHP would fail with a fatal error.
 *
 * @package Tuifan
 */

if ( ! defined( 'ABSPATH' ) ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- CLI-only harness writing to STDERR; WP_Filesystem does not cover console streams.
	fwrite( STDERR, "Run this file with: wp eval-file tests/integration-checks.php\n" );
	exit( 1 );
}

/**
 * Shared pass/fail counters.
 *
 * @return array{pass: int, fail: int}
 */
function &tuifan_check_state(): array {
	static $state = array(
		'pass' => 0,
		'fail' => 0,
	);

	return $state;
}

/**
 * Record and print one assertion result.
 *
 * @param string $label  Human readable assertion label.
 * @param bool   $passed Whether the assertion held.
 * @param string $detail Optional observed value.
 */
function tuifan_check_assert( string $label, bool $passed, string $detail = '' ): void {
	$state = &tuifan_check_state();

	if ( $passed ) {
		++$state['pass'];
	} else {
		++$state['fail'];
	}

	$line = sprintf(
		"%s %s%s\n",
		$passed ? 'PASS' : 'FAIL',
		$label,
		'' !== $detail ? ' — ' . $detail : ''
	);

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- CLI-only harness writing to STDOUT; WP_Filesystem does not cover console streams.
	fwrite( STDOUT, $line );
}

/**
 * Create a disposable draft post.
 *
 * @param string $title   Post title.
 * @param string $content Post content.
 *
 * @return int Post ID (0 on failure).
 */
function tuifan_check_make_post( string $title, string $content ): int {
	$id = wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_status'  => 'draft',
			'post_title'   => $title,
			'post_content' => $content,
		),
		true
	);

	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Delete a disposable post without leaving it in the trash.
 *
 * @param int $post_id Post ID.
 */
function tuifan_check_drop_post( int $post_id ): void {
	if ( $post_id > 0 ) {
		wp_delete_post( $post_id, true );
	}
}

// ---------------------------------------------------------------------------
// 1. Combined search + category pagination keeps every constraint.
// ---------------------------------------------------------------------------

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- CLI harness snapshots $_GET to restore it verbatim; no state change and no nonce applies.
$saved_get = $_GET;
// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Superglobal snapshot restored verbatim below; unslashing or sanitizing would not round-trip the harness's own value.
$saved_server = $_SERVER['REQUEST_URI'] ?? '';

$_GET['s']              = 'context';
$_GET['cat']            = '5';
$_GET['post_type']      = 'post';
$_SERVER['REQUEST_URI'] = '/index.php?s=context&cat=5&post_type=post';

$paged_link = get_pagenum_link( 2, false );

tuifan_check_assert(
	'paged link keeps the search term',
	str_contains( $paged_link, 's=context' ),
	$paged_link
);
tuifan_check_assert(
	'paged link keeps the category filter',
	str_contains( $paged_link, 'cat=5' ),
	$paged_link
);
tuifan_check_assert(
	'paged link keeps the post type',
	str_contains( $paged_link, 'post_type=post' ),
	$paged_link
);

$pagination = tuifan_get_pagination(
	array(
		'total'    => 5,
		'per_page' => 2,
		'current'  => 2,
		'page_url' => static function ( int $page ): string {
			return get_pagenum_link( $page, false );
		},
		'label'    => 'Article pages',
	)
);

tuifan_check_assert(
	'pagination reports the truthful range',
	str_contains( $pagination, '03–04 / 05 entries' ),
	'expected range 03–04 / 05 entries'
);
tuifan_check_assert(
	'pagination links carry the search and category constraints',
	str_contains( $pagination, 's=context' ) && str_contains( $pagination, 'cat=5' )
);

$zero_pagination = tuifan_get_pagination(
	array(
		'total'    => 0,
		'per_page' => 2,
		'current'  => 1,
		'page_url' => static function ( int $page ): string {
			return '/?p=' . $page;
		},
		'label'    => 'Article pages',
	)
);

tuifan_check_assert(
	'pagination is omitted when a single page holds everything',
	'' === trim( $zero_pagination )
);

$_GET                   = $saved_get;
$_SERVER['REQUEST_URI'] = $saved_server;

// ---------------------------------------------------------------------------
// 2. Reading time word boundaries and minimum.
// ---------------------------------------------------------------------------

$word_post = static function ( int $words ): int {
	return tuifan_check_make_post(
		'Tuifan reading-time probe',
		implode( ' ', array_fill( 0, $words, 'word' ) )
	);
};

$empty_id     = tuifan_check_make_post( 'Tuifan empty probe', "<p> \n\t </p>" );
$two_hundred  = $word_post( 200 );
$two_oh_one   = $word_post( 201 );
$four_hundred = $word_post( 400 );
$markup_id    = tuifan_check_make_post(
	'Tuifan markup probe',
	'[gallery ids="1,2"]<!-- wp:paragraph --><p>two words</p><!-- /wp:paragraph -->'
);

tuifan_check_assert(
	'empty content still reports one minute',
	1 === tuifan_get_reading_minutes( $empty_id ),
	'id ' . $empty_id
);
tuifan_check_assert(
	'200 words report one minute',
	1 === tuifan_get_reading_minutes( $two_hundred ),
	'id ' . $two_hundred
);
tuifan_check_assert(
	'201 words report two minutes',
	2 === tuifan_get_reading_minutes( $two_oh_one ),
	'id ' . $two_oh_one
);
tuifan_check_assert(
	'400 words report two minutes',
	2 === tuifan_get_reading_minutes( $four_hundred ),
	'id ' . $four_hundred
);
tuifan_check_assert(
	'shortcodes and block delimiters are not counted as words',
	1 === tuifan_get_reading_minutes( $markup_id ),
	'id ' . $markup_id
);
tuifan_check_assert(
	'an unknown post reports no reading time',
	0 === tuifan_get_reading_minutes( 999999 ),
	'id 999999'
);

foreach ( array( $empty_id, $two_hundred, $two_oh_one, $four_hundred, $markup_id ) as $probe_id ) {
	tuifan_check_drop_post( $probe_id );
}

// ---------------------------------------------------------------------------
// 3. Seeder repeat behaviour leaves an existing page alone.
// ---------------------------------------------------------------------------

$page_id = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => 'Tuifan seeder probe',
		'post_name'    => 'tuifan-seeder-probe',
		'post_content' => '<!-- wp:paragraph --><p>edited by the site owner</p><!-- /wp:paragraph -->',
	),
	true
);

if ( is_wp_error( $page_id ) ) {
	tuifan_check_assert( 'seeder probe page created', false, $page_id->get_error_message() );
} else {
	$page_id  = (int) $page_id;
	$report   = array();
	$resolved = tuifan_starter_ensure_page(
		'home',
		array(
			'title'    => 'Home',
			'pattern'  => 'tuifan/home',
			'template' => '',
		),
		$page_id,
		'tuifan-seeder-probe',
		get_current_user_id(),
		$report
	);

	$probe = get_post( $page_id );

	tuifan_check_assert(
		'repeat seeding reuses the stored page id',
		$page_id === $resolved,
		'returned ' . $resolved
	);
	tuifan_check_assert(
		'repeat seeding keeps the edited content',
		$probe instanceof WP_Post && str_contains( $probe->post_content, 'edited by the site owner' ),
		$probe instanceof WP_Post ? 'title ' . $probe->post_title : 'missing post'
	);
	tuifan_check_assert(
		'repeat seeding records the reuse in its report',
		str_contains( (string) ( $report['page:home'] ?? '' ), 'reused' ),
		(string) ( $report['page:home'] ?? 'no report entry' )
	);

	tuifan_check_drop_post( $page_id );
}

// ---------------------------------------------------------------------------
// 4. Presentation-only forms and context-free reading time.
// ---------------------------------------------------------------------------

$forms = do_blocks(
	'<!-- wp:tuifan/form-template /-->' .
	'<!-- wp:tuifan/form-template {"variant":"newsletter"} /-->'
);

preg_match_all( '/id="(tuifan-form-\d+)-notice"/', $forms, $notice_ids );

tuifan_check_assert(
	'form templates render no form element',
	! str_contains( $forms, '<form' )
);
tuifan_check_assert(
	'form template instances use unique element ids',
	count( $notice_ids[1] ) === count( array_unique( $notice_ids[1] ) ) && count( $notice_ids[1] ) >= 2,
	implode( ',', $notice_ids[1] )
);
tuifan_check_assert(
	'form template controls are disabled',
	substr_count( $forms, ' disabled' ) >= 8 && substr_count( $forms, 'type="button" disabled' ) >= 2
);
tuifan_check_assert(
	'form template always shows its notice',
	str_contains( $forms, 'Nothing is sent or stored.' )
);
tuifan_check_assert(
	'reading time renders nothing without a post context',
	'' === trim( do_blocks( '<!-- wp:tuifan/reading-time /-->' ) )
);

// ---------------------------------------------------------------------------
// 5. Home entry-list pagination resolves through the front page.
// ---------------------------------------------------------------------------

tuifan_check_assert(
	'the first latest page is the site root',
	home_url( '/' ) === tuifan_get_latest_page_url( 1 ),
	tuifan_get_latest_page_url( 1 )
);
tuifan_check_assert(
	'later latest pages add the tuifan_page parameter',
	str_contains( tuifan_get_latest_page_url( 2 ), 'tuifan_page=2' ),
	tuifan_get_latest_page_url( 2 )
);

$public_query_vars = isset( $GLOBALS['wp']->public_query_vars ) ? $GLOBALS['wp']->public_query_vars : array();

tuifan_check_assert(
	'tuifan_page is not a public query var (it would move the front page to the blog index)',
	! in_array( 'tuifan_page', (array) $public_query_vars, true ),
	'public query vars: ' . count( (array) $public_query_vars )
);

$saved_page_param = $_GET['tuifan_page'] ?? null;

$_GET['tuifan_page'] = '3';
tuifan_check_assert( 'the page parameter is read from the request', 3 === tuifan_get_latest_page() );

$_GET['tuifan_page'] = '-5';
tuifan_check_assert( 'a negative page parameter becomes page 1', 1 === tuifan_get_latest_page() );

$_GET['tuifan_page'] = '99';
$out_of_range        = do_blocks( '<!-- wp:tuifan/entry-list {"source":"latest","postsPerPage":2} /-->' );

tuifan_check_assert(
	'a page past the end offers the first-page link',
	str_contains( $out_of_range, 'past the end of the archive' ) && str_contains( $out_of_range, 'Back to the first page' ),
	'empty-state text found'
);
tuifan_check_assert(
	'a page past the end does not repeat the last page',
	! str_contains( $out_of_range, 'tuifan-entry__title' )
);

if ( null === $saved_page_param ) {
	unset( $_GET['tuifan_page'] );
} else {
	$_GET['tuifan_page'] = $saved_page_param;
}

// ---------------------------------------------------------------------------
// 6. Template parts do not nest landmark elements.
// ---------------------------------------------------------------------------

$header_part_html = do_blocks( '<!-- wp:template-part {"slug":"header","theme":"tuifan"} /-->' );
$footer_part_html = do_blocks( '<!-- wp:template-part {"slug":"footer","theme":"tuifan"} /-->' );

tuifan_check_assert(
	'the header part renders a single banner landmark',
	1 === substr_count( $header_part_html, '<header' ),
	'header elements: ' . substr_count( $header_part_html, '<header' )
);
tuifan_check_assert(
	'the footer part renders a single contentinfo landmark',
	1 === substr_count( $footer_part_html, '<footer' ),
	'footer elements: ' . substr_count( $footer_part_html, '<footer' )
);

// ---------------------------------------------------------------------------
// 7. Extended-section queries and local pattern links stay public and scoped.
// ---------------------------------------------------------------------------

$section_fixture_ids = array();
$section_term_id     = 0;
$section_empty_id    = 0;
$section_upload      = '';
$section_attachment  = 0;
$section_original    = $GLOBALS['post'] ?? null;

try {
	$section_term  = wp_insert_term( 'Tuifan carousel fixture ' . wp_generate_uuid4(), 'category' );
	$section_empty = wp_insert_term( 'Tuifan empty fixture ' . wp_generate_uuid4(), 'category' );

	if ( ! is_wp_error( $section_term ) && ! is_wp_error( $section_empty ) ) {
		$section_term_id  = (int) $section_term['term_id'];
		$section_empty_id = (int) $section_empty['term_id'];
		$published        = array(
			array( 'Zulu fixture', '2024-01-04 12:00:00' ),
			array( 'Alpha fixture', '2024-01-03 12:00:00' ),
			array( 'Mike fixture', '2024-01-02 12:00:00' ),
			array( 'Echo fixture <script>alert(1)</script>', '2024-01-01 12:00:00' ),
		);

		foreach ( $published as $row ) {
			$section_new_id = wp_insert_post(
				array(
					'post_type'     => 'post',
					'post_status'   => 'publish',
					'post_title'    => $row[0],
					'post_excerpt'  => '<img src=x onerror=alert(1)> fixture excerpt',
					'post_content'  => 'Fixture body.',
					'post_date'     => $row[1],
					'post_date_gmt' => get_gmt_from_date( $row[1] ),
				),
				true
			);

			if ( ! is_wp_error( $section_new_id ) ) {
				$section_fixture_ids[] = (int) $section_new_id;
				wp_set_post_categories( (int) $section_new_id, array( $section_term_id ) );
			}
		}

		foreach ( array( 'draft', 'private' ) as $section_status ) {
			$section_new_id = wp_insert_post(
				array(
					'post_type'    => 'post',
					'post_status'  => $section_status,
					'post_title'   => ucfirst( $section_status ) . ' secret fixture',
					'post_content' => 'Secret.',
				),
				true
			);
			if ( ! is_wp_error( $section_new_id ) ) {
				$section_fixture_ids[] = (int) $section_new_id;
				wp_set_post_categories( (int) $section_new_id, array( $section_term_id ) );
			}
		}

		$passworded = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_password' => 'secret',
				'post_title'    => 'Password secret fixture',
				'post_content'  => 'Secret.',
			),
			true
		);
		if ( ! is_wp_error( $passworded ) ) {
			$section_fixture_ids[] = (int) $passworded;
			wp_set_post_categories( (int) $passworded, array( $section_term_id ) );
		}

		$render_carousel = static function ( array $attrs ): string {
			return do_blocks(
				serialize_block(
					array(
						'blockName'    => 'tuifan/posts-carousel',
						'attrs'        => $attrs,
						'innerBlocks'  => array(),
						'innerHTML'    => '',
						'innerContent' => array(),
					)
				)
			);
		};

		$date_html = $render_carousel(
			array(
				'categoryId'  => $section_term_id,
				'postsToShow' => 2,
				'orderBy'     => 'date',
				'order'       => 'DESC',
			)
		);
		tuifan_check_assert( 'posts carousel applies the requested count and date order', str_contains( $date_html, 'Zulu fixture' ) && str_contains( $date_html, 'Alpha fixture' ) && ! str_contains( $date_html, 'Mike fixture' ) );

		$title_html = $render_carousel(
			array(
				'categoryId'  => $section_term_id,
				'postsToShow' => 2,
				'orderBy'     => 'title',
				'order'       => 'ASC',
			)
		);
		tuifan_check_assert( 'posts carousel applies title ASC order', strpos( $title_html, 'Alpha fixture' ) < strpos( $title_html, 'Echo fixture' ) && ! str_contains( $title_html, 'Mike fixture' ) );

		$all_html = $render_carousel(
			array(
				'categoryId'  => $section_term_id,
				'postsToShow' => 99,
				'orderBy'     => 'date',
				'order'       => 'DESC',
			)
		);
		tuifan_check_assert( 'posts carousel excludes draft, private, and passworded posts', ! str_contains( $all_html, 'secret fixture' ) );
		tuifan_check_assert( 'posts carousel escapes sensitive title and excerpt markup', ! str_contains( $all_html, '<script>' ) && ! str_contains( $all_html, '<img src=x' ) && str_contains( $all_html, '&lt;script&gt;' ) );
		tuifan_check_assert( 'posts carousel uses intentional media fallback without a thumbnail', str_contains( $all_html, 'tuifan-media-fallback' ) );
		$section_current_post = $GLOBALS['post'] ?? null;
		tuifan_check_assert( 'posts carousel does not change the global post', $section_original === $section_current_post );

		$empty_html = $render_carousel(
			array(
				'categoryId'  => $section_empty_id,
				'postsToShow' => 4,
			)
		);
		tuifan_check_assert( 'empty category renders the explicit empty state without navigation', str_contains( $empty_html, 'No entries found.' ) && ! str_contains( $empty_html, 'data-tuifan-carousel-controls' ) );

		wp_delete_term( $section_empty_id, 'category' );
		$deleted_html = $render_carousel(
			array(
				'categoryId'  => $section_empty_id,
				'postsToShow' => 4,
			)
		);
		tuifan_check_assert( 'deleted category does not broaden to unrelated posts', str_contains( $deleted_html, 'No entries found.' ) && ! str_contains( $deleted_html, 'Zulu fixture' ) );
		$section_empty_id = 0;

		$unknown             = tuifan_get_section_pattern_url( 'unknown' );
		$section_archive_url = tuifan_get_posts_url();
		tuifan_check_assert( 'unknown pattern URL kind falls back to the local archive', $unknown === $section_archive_url, $unknown );
		tuifan_check_assert( 'missing pattern page uses a local search fallback', str_contains( tuifan_get_section_pattern_url( 'page', 'definitely-missing-tuifan-page' ), 's=definitely-missing-tuifan-page' ) );

		$page_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_name'   => 'tuifan-pattern-page-fixture',
				'post_title'  => 'Pattern page fixture',
			),
			true
		);
		if ( ! is_wp_error( $page_id ) ) {
			$section_fixture_ids[] = (int) $page_id;
			tuifan_check_assert( 'pattern URL helper resolves a public local page', get_permalink( $page_id ) === tuifan_get_section_pattern_url( 'page', 'tuifan-pattern-page-fixture' ) );
		}

		$protected_id = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_name'     => 'tuifan-protected-guide',
				'post_title'    => 'Protected pattern fixture',
				'post_password' => 'secret',
			),
			true
		);
		if ( ! is_wp_error( $protected_id ) ) {
			$section_fixture_ids[] = (int) $protected_id;
			$protected_url         = tuifan_get_section_pattern_url( 'post', 'tuifan-protected-guide' );
			parse_str( (string) wp_parse_url( $protected_url, PHP_URL_QUERY ), $protected_query );
			tuifan_check_assert( 'pattern URL helper rejects passworded guide posts', 'tuifan protected guide' === ( $protected_query['s'] ?? '' ), $protected_url );
		}
	}
} finally {
	foreach ( array_reverse( $section_fixture_ids ) as $section_fixture_id ) {
		wp_delete_post( $section_fixture_id, true );
	}

	if ( $section_term_id > 0 ) {
		wp_delete_term( $section_term_id, 'category' );
	}

	if ( $section_empty_id > 0 ) {
		wp_delete_term( $section_empty_id, 'category' );
	}

	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- The harness deliberately restores the global post it snapshotted before rendering.
	$GLOBALS['post'] = $section_original;
}

// ---------------------------------------------------------------------------
// 8. Extended-section patterns keep their payload shape and local links.
// ---------------------------------------------------------------------------

$section_pattern_expectations = array(
	'tuifan/pricing-table'        => array(
		'block'    => 'tuifan/pricing-table',
		'children' => 3,
		'nested'   => 'tuifan/pricing-card',
	),
	'tuifan/testimonial-carousel' => array(
		'block'    => 'tuifan/testimonial-carousel',
		'children' => 3,
		'nested'   => 'tuifan/testimonial-card',
	),
	'tuifan/team'                 => array(
		'block'    => 'tuifan/team',
		'children' => 3,
		'nested'   => 'tuifan/team-card',
	),
	'tuifan/impact'               => array(
		'block'    => 'tuifan/impact',
		'children' => 3,
		'nested'   => 'tuifan/impact-row',
	),
	'tuifan/tabs'                 => array(
		'block'    => 'tuifan/tabs',
		'children' => 3,
		'nested'   => 'tuifan/tab-panel',
	),
	'tuifan/faq'                  => array(
		'block'    => 'tuifan/faq',
		'children' => 4,
		'nested'   => 'core/details',
	),
	'tuifan/posts-carousel'       => array(
		'block'    => 'tuifan/posts-carousel',
		'children' => 0,
		'nested'   => '',
	),
	'tuifan/cta'                  => array(
		'block'    => 'tuifan/cta',
		'children' => 0,
		'nested'   => '',
	),
);

$section_patterns = WP_Block_Patterns_Registry::get_instance();

foreach ( $section_pattern_expectations as $section_slug => $section_expectation ) {
	$section_pattern = $section_patterns->get_registered( $section_slug );

	if ( ! is_array( $section_pattern ) ) {
		tuifan_check_assert( 'pattern registered: ' . $section_slug, false, 'not registered' );
		continue;
	}

	$section_blocks    = parse_blocks( (string) $section_pattern['content'] );
	$section_root      = $section_blocks[0] ?? array();
	$section_root_name = (string) ( $section_root['blockName'] ?? '' );

	tuifan_check_assert(
		'pattern ' . $section_slug . ' keeps its root block',
		$section_expectation['block'] === $section_root_name,
		$section_root_name
	);

	$section_children    = $section_root['innerBlocks'] ?? array();
	$section_child_count = count( $section_children );

	tuifan_check_assert(
		'pattern ' . $section_slug . ' keeps its children',
		$section_expectation['children'] === $section_child_count,
		'children: ' . $section_child_count
	);

	if ( '' !== $section_expectation['nested'] ) {
		$section_nested_ok = true;

		foreach ( $section_children as $section_child ) {
			$section_child_name = (string) ( $section_child['blockName'] ?? '' );

			if ( $section_expectation['nested'] !== $section_child_name ) {
				$section_nested_ok = false;
			}
		}

		tuifan_check_assert(
			'pattern ' . $section_slug . ' nests ' . $section_expectation['nested'],
			$section_nested_ok
		);
	}

	$section_default_class = 'wp-block-' . str_replace( '/', '-', $section_slug );
	$section_rendered      = do_blocks( (string) $section_pattern['content'] );
	$section_class_pattern = '/class="[^"]*\b' . preg_quote( $section_default_class, '/' ) . '\b/';

	tuifan_check_assert(
		'pattern ' . $section_slug . ' renders its own default class',
		1 === preg_match( $section_class_pattern, $section_rendered ),
		$section_default_class
	);

	tuifan_check_assert(
		'pattern ' . $section_slug . ' stays in the tuifan category',
		in_array( 'tuifan', (array) $section_pattern['categories'], true )
	);
}

$section_pricing  = $section_patterns->get_registered( 'tuifan/pricing-table' );
$section_li_count = 0;

foreach ( parse_blocks( (string) $section_pricing['content'] )[0]['innerBlocks'] as $section_card ) {
	$section_li_count += substr_count( serialize_block( $section_card ), '<!-- wp:list-item' );
}

tuifan_check_assert(
	'pricing pattern keeps every feature list item',
	9 === $section_li_count,
	'list items: ' . $section_li_count
);

// ---------------------------------------------------------------------------
// Summary.
// ---------------------------------------------------------------------------

$state = &tuifan_check_state();
$total = $state['pass'] + $state['fail'];

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- CLI-only harness writing to STDOUT; WP_Filesystem does not cover console streams.
fwrite(
	STDOUT,
	sprintf(
		"\n%d checks: %d passed, %d failed\n",
		$total,
		$state['pass'],
		$state['fail']
	)
);

if ( $state['fail'] > 0 ) {
	exit( 1 );
}

exit( 0 );
