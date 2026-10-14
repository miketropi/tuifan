<?php
/**
 * Non-destructive starter-site setup, exposed as `wp tuifan setup`.
 *
 * Nothing here runs on theme activation or on normal web requests: the command
 * exists only inside WP-CLI. Existing pages, posts, terms, navigation and
 * template overrides are reused instead of replaced.
 *
 * @package Tuifan
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

require_once TUIFAN_DIR . '/inc/starter-content.php';

/**
 * Page shells the starter site expects, keyed by slug.
 *
 * @return array<string, array{ title: string, pattern: string, template: string }>
 */
function tuifan_starter_page_definitions(): array {
	return array(
		'home'    => array(
			'title'    => __( 'Home', 'tuifan' ),
			'pattern'  => 'tuifan/home',
			'template' => '',
		),
		'about'   => array(
			'title'    => __( 'About', 'tuifan' ),
			'pattern'  => 'tuifan/about',
			'template' => 'about-us',
		),
		'contact' => array(
			'title'    => __( 'Contact', 'tuifan' ),
			'pattern'  => 'tuifan/contact',
			'template' => 'contact-us',
		),
		'blog'    => array(
			'title'    => __( 'Blog', 'tuifan' ),
			'pattern'  => '',
			'template' => '',
		),
	);
}

/**
 * Starter guide slugs in the intended reading order (newest first).
 *
 * @return list<string>
 */
function tuifan_starter_article_order(): array {
	return array( 'agent-start', 'agent-permissions', 'agent-context', 'agent-evaluation' );
}

/**
 * Starter categories: term slug => display name.
 *
 * @return array<string, string>
 */
function tuifan_starter_categories(): array {
	return array(
		'getting-started' => __( 'Getting started', 'tuifan' ),
		'safety'          => __( 'Safety', 'tuifan' ),
		'workflows'       => __( 'Workflows', 'tuifan' ),
	);
}

/**
 * Author for starter content: the current user, else the first administrator.
 *
 * WP-CLI has no logged-in user by default, which would otherwise leave new
 * posts with `post_author = 0` and break author archives.
 */
function tuifan_starter_author_id(): int {
	$current = get_current_user_id();

	if ( $current > 0 ) {
		return $current;
	}

	$admins = get_users(
		array(
			'role'    => 'administrator',
			'number'  => 1,
			'orderby' => 'ID',
			'order'   => 'ASC',
			'fields'  => 'ID',
		)
	);

	return isset( $admins[0] ) ? (int) $admins[0] : 0;
}

/**
 * Find any post (published or not) by slug.
 *
 * @param string $slug      Post slug.
 * @param string $post_type Post type.
 *
 * @return WP_Post|null
 */
function tuifan_starter_find_post( string $slug, string $post_type = 'page' ): ?WP_Post {
	$found = get_posts(
		array(
			'name'             => $slug,
			'post_type'        => $post_type,
			'post_status'      => array( 'publish', 'draft', 'pending', 'future', 'private', 'trash' ),
			'numberposts'      => 1,
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'suppress_filters' => false,
		)
	);

	if ( isset( $found[0] ) && $found[0] instanceof WP_Post ) {
		return $found[0];
	}

	return null;
}

/**
 * Resolved slugs for the starter objects (a conflict forces `tuifan-{slug}`).
 *
 * @param array<string, mixed> $stored Stored state.
 *
 * @return array{ pages: array<string, string>, posts: array<string, string> }
 */
function tuifan_starter_resolved_slugs( array $stored ): array {
	$slugs = array(
		'pages' => array(),
		'posts' => array(),
	);

	foreach ( array( 'pages', 'posts' ) as $group ) {
		$stored_group = isset( $stored['slugs'][ $group ] ) && is_array( $stored['slugs'][ $group ] ) ? $stored['slugs'][ $group ] : array();

		foreach ( $stored_group as $key => $value ) {
			$slugs[ $group ][ (string) $key ] = (string) $value;
		}
	}

	return $slugs;
}

/**
 * Stored starter object ID, when it still exists.
 *
 * @param array<string, mixed> $stored Stored state.
 * @param string               $group  `pages` or `posts`.
 * @param string               $key    Object key.
 *
 * @return int
 */
function tuifan_starter_stored_id( array $stored, string $group, string $key ): int {
	$stored_ids = isset( $stored[ $group ] ) && is_array( $stored[ $group ] ) ? $stored[ $group ] : array();
	$id         = isset( $stored_ids[ $key ] ) ? (int) $stored_ids[ $key ] : 0;

	if ( $id < 1 ) {
		return 0;
	}

	$post = get_post( $id );

	return $post instanceof WP_Post ? $id : 0;
}

/**
 * Pattern block markup (evaluated PHP), or an empty string.
 *
 * @param string $pattern_slug Registered pattern slug.
 */
function tuifan_starter_pattern_content( string $pattern_slug ): string {
	if ( '' === $pattern_slug ) {
		return '';
	}

	$registry = WP_Block_Patterns_Registry::get_instance();
	$pattern  = $registry->get_registered( $pattern_slug );

	if ( ! is_array( $pattern ) || empty( $pattern['content'] ) ) {
		return '';
	}

	return (string) $pattern['content'];
}

/**
 * Create (or reuse) one starter page.
 *
 * @param string               $slug           Target slug.
 * @param array<string, mixed> $definition     Page definition.
 * @param int                  $stored_id      Previously stored page ID.
 * @param string               $resolved_slug  Slug stored from an earlier run.
 * @param int                  $author_id      Author for a newly created page.
 * @param array<string, mixed> $report         Report rows (by reference).
 *
 * @return int Page ID.
 */
function tuifan_starter_ensure_page( string $slug, array $definition, int $stored_id, string $resolved_slug, int $author_id, array &$report ): int {
	if ( $stored_id > 0 ) {
		$post = get_post( $stored_id );

		if ( $post instanceof WP_Post && 'page' === $post->post_type && 'publish' === $post->post_status ) {
			$report[ 'page:' . $slug ] = sprintf( 'reused #%d — content untouched', $stored_id );

			return $stored_id;
		}

		$report[ 'page:' . $slug ] = sprintf( 'stored #%d is no longer a published page; recreating', $stored_id );
	}

	$target_slug = '' !== $resolved_slug ? $resolved_slug : $slug;
	$existing    = tuifan_starter_find_post( $target_slug, 'page' );

	if ( $existing instanceof WP_Post && 'page' === $existing->post_type ) {
		if ( 'publish' === $existing->post_status ) {
			$report[ 'page:' . $slug ] = sprintf( 'reused existing published page #%d', (int) $existing->ID );

			return (int) $existing->ID;
		}

		$report[ 'page:' . $slug ] = sprintf( 'left %s #%d untouched; using slug tuifan-%s', $existing->post_status, (int) $existing->ID, $slug );
		$target_slug               = 'tuifan-' . $slug;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_author'  => $author_id,
			'post_title'   => (string) $definition['title'],
			'post_name'    => $target_slug,

			/*
			 * WP block attributes escape `--` as `\u002d`; `wp_insert_post()` unslashes
			 * its input, so pre-slash the document to keep those escapes intact.
			 */
			'post_content' => wp_slash( tuifan_starter_pattern_content( (string) $definition['pattern'] ) ),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		$report[ 'page:' . $slug ] = 'error: ' . $post_id->get_error_message();

		return 0;
	}

	$post_id = (int) $post_id;

	if ( '' !== (string) $definition['template'] ) {
		update_post_meta( $post_id, '_wp_page_template', (string) $definition['template'] );
	}

	$report[ 'page:' . $slug ] = sprintf( 'created #%d as /%s/', $post_id, $target_slug );

	return $post_id;
}

/**
 * Create (or reuse) one starter guide post.
 *
 * @param string               $slug      Stored content slug.
 * @param array<string, mixed> $article   Article document.
 * @param int                  $index     Reading-order index.
 * @param int                  $stored_id Previously stored post ID.
 * @param int                  $author_id Author for a newly created post.
 * @param array<string, mixed> $report    Report rows (by reference).
 *
 * @return int Post ID.
 */
function tuifan_starter_ensure_article( string $slug, array $article, int $index, int $stored_id, int $author_id, array &$report ): int {
	$post_id = $stored_id;

	if ( $post_id > 0 ) {
		$stored_post = get_post( $post_id );

		if ( $stored_post instanceof WP_Post && 'post' === $stored_post->post_type && 'publish' === $stored_post->post_status ) {
			$report[ 'post:' . $slug ] = sprintf( 'reused stored post #%d', $post_id );
		} else {
			$report[ 'post:' . $slug ] = sprintf( 'stored #%d is no longer a published post; recreating', $post_id );
			$post_id                   = 0;
		}
	}

	if ( $post_id < 1 ) {
		$existing = tuifan_starter_find_post( $slug, 'post' );

		if ( $existing instanceof WP_Post && 'post' === $existing->post_type && 'publish' === $existing->post_status ) {
			$post_id                   = (int) $existing->ID;
			$report[ 'post:' . $slug ] = sprintf( 'reused existing published post #%d — content untouched', $post_id );
		}
	}

	if ( $post_id < 1 ) {
		$base_gmt      = time();
		$post_date_gmt = gmdate( 'Y-m-d H:i:s', $base_gmt - ( $index * 60 ) );

		$post_id = wp_insert_post(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'post_author'    => $author_id,
				'post_title'     => (string) $article['title'],
				'post_excerpt'   => (string) $article['excerpt'],
				'post_content'   => wp_slash( (string) $article['content'] ),
				'post_name'      => $slug,
				'comment_status' => 'closed',
				'post_date'      => get_date_from_gmt( $post_date_gmt ),
				'post_date_gmt'  => $post_date_gmt,
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			$report[ 'post:' . $slug ] = 'error: ' . $post_id->get_error_message();

			return 0;
		}

		$post_id                   = (int) $post_id;
		$report[ 'post:' . $slug ] = sprintf( 'created #%d as /%s/', $post_id, $slug );
	}

	$category = get_term_by( 'slug', (string) $article['category'], 'category' );

	if ( $category instanceof WP_Term ) {
		wp_set_object_terms( $post_id, array( (int) $category->term_id ), 'category', false );
	}

	$tags = isset( $article['tags'] ) && is_array( $article['tags'] ) ? array_map( 'strval', $article['tags'] ) : array();

	if ( array() !== $tags ) {
		wp_set_object_terms( $post_id, $tags, 'post_tag', false );
	}

	$current_post = get_post( $post_id );

	if ( $current_post instanceof WP_Post && 'closed' !== $current_post->comment_status ) {
		wp_update_post(
			array(
				'ID'             => $post_id,
				'comment_status' => 'closed',
			)
		);

		$report[ 'comments:' . $slug ] = 'closed for the starter guide';
	}

	return $post_id;
}

/**
 * Navigation block markup for the `Tuifan Primary` entity.
 *
 * @param int                $home_id Home page ID.
 * @param int                $blog_id Blog page ID.
 * @param int                $about_id About page ID.
 * @param int                $contact_id Contact page ID.
 * @param array<string, int> $article_ids Article IDs by slug.
 */
function tuifan_starter_navigation_content( int $home_id, int $blog_id, int $about_id, int $contact_id, array $article_ids ): string {
	$search_url = tuifan_get_search_url();

	$link = static function ( array $attributes ): string {
		return '<!-- wp:navigation-link ' . wp_json_encode( $attributes ) . ' /-->';
	};

	$submenu = array();

	$submenu[] = $link(
		array(
			'label' => __( 'All guides', 'tuifan' ),
			'type'  => 'page',
			'kind'  => 'post-type',
			'url'   => $search_url,
		)
	);

	foreach ( tuifan_starter_categories() as $term_slug => $name ) {
		$term = get_term_by( 'slug', $term_slug, 'category' );

		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		$term_url = get_term_link( $term );

		if ( $term_url instanceof WP_Error ) {
			continue;
		}

		$submenu[] = $link(
			array(
				'label' => $name,
				'type'  => 'category',
				'kind'  => 'taxonomy',
				'id'    => (int) $term->term_id,
				'url'   => (string) $term_url,
			)
		);
	}

	$guide_labels = array(
		'agent-start'       => __( 'Your first AI agent', 'tuifan' ),
		'agent-permissions' => __( 'Set safer permissions', 'tuifan' ),
		'agent-context'     => __( 'Build better context', 'tuifan' ),
		'agent-evaluation'  => __( 'Evaluate the results', 'tuifan' ),
	);

	$first = true;

	foreach ( $guide_labels as $slug => $label ) {
		$post_id = isset( $article_ids[ $slug ] ) ? (int) $article_ids[ $slug ] : 0;

		if ( $post_id < 1 ) {
			continue;
		}

		$permalink = get_permalink( $post_id );

		$attributes = array(
			'label' => $label,
			'type'  => 'post',
			'kind'  => 'post-type',
			'id'    => $post_id,
			'url'   => is_string( $permalink ) ? $permalink : '',
		);

		if ( $first ) {
			$attributes['className'] = 'is-tuifan-submenu-divider';
			$first                   = false;
		}

		$submenu[] = $link( $attributes );
	}

	$content  = $link(
		array(
			'label' => '~/home',
			'type'  => 'page',
			'kind'  => 'post-type',
			'id'    => $home_id,
			'url'   => (string) get_permalink( $home_id ),
		)
	);
	$content .= "\n" . '<!-- wp:navigation-submenu ' . wp_json_encode(
		array(
			'label' => '/guides',
			'type'  => 'page',
			'kind'  => 'post-type',
			'url'   => (string) get_permalink( $blog_id ),
		)
	) . ' -->' . "\n";
	$content .= implode( "\n", array_map( static fn( string $item ): string => "\t" . $item, $submenu ) ) . "\n";
	$content .= '<!-- /wp:navigation-submenu -->';
	$content .= "\n" . $link(
		array(
			'label' => '/about',
			'type'  => 'page',
			'kind'  => 'post-type',
			'id'    => $about_id,
			'url'   => (string) get_permalink( $about_id ),
		)
	);
	$content .= "\n" . $link(
		array(
			'label' => '/contact',
			'type'  => 'page',
			'kind'  => 'post-type',
			'id'    => $contact_id,
			'url'   => (string) get_permalink( $contact_id ),
		)
	);
	$content .= "\n" . $link(
		array(
			'label'     => 'search',
			'type'      => 'page',
			'kind'      => 'post-type',
			'url'       => $search_url,
			'className' => 'tuifan-nav__search',
		)
	);

	return $content;
}

/**
 * Header template-part content: the theme file with a navigation `ref`.
 *
 * @param int $navigation_id Navigation entity ID.
 */
function tuifan_starter_header_content( int $navigation_id ): string {
	$path = TUIFAN_DIR . '/parts/header.html';

	if ( ! is_readable( $path ) ) {
		return '';
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file; wp_remote_get() is for remote URLs.
	$header = (string) file_get_contents( $path );

	$navigation_comment = '<!-- wp:navigation ' . wp_json_encode(
		array(
			'ref'         => $navigation_id,
			'overlayMenu' => 'mobile',
			'className'   => 'tuifan-nav',
			'layout'      => array(
				'type'           => 'flex',
				'justifyContent' => 'right',
				'flexWrap'       => 'wrap',
			),
		)
	) . ' /-->';

	$updated = preg_replace(
		'/<!-- wp:navigation .*?-->.*?<!-- \/wp:navigation -->/s',
		$navigation_comment,
		$header,
		1
	);

	return is_string( $updated ) ? $updated : $header;
}

/**
 * Existing child theme template/part overrides, so nothing is silently masked.
 *
 * @return array<string, list<string>> Map of post type to `slug (status)`.
 */
function tuifan_starter_existing_overrides(): array {
	$overrides = array(
		'templates'      => array(),
		'template_parts' => array(),
	);

	foreach ( array(
		'wp_template'      => 'templates',
		'wp_template_part' => 'template_parts',
	) as $post_type => $key ) {
		$posts = get_posts(
			array(
				'post_type'        => $post_type,
				'post_status'      => 'any',
				'numberposts'      => -1,
				'suppress_filters' => false,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Filtering the theme's own template records by the wp_theme taxonomy; there is no other supported query path.
				'tax_query'        => array(
					array(
						'taxonomy' => 'wp_theme',
						'field'    => 'name',
						'terms'    => get_stylesheet(),
					),
				),
			)
		);

		foreach ( $posts as $post ) {
			if ( $post instanceof WP_Post ) {
				$overrides[ $key ][] = sprintf( '%s (%s)', $post->post_name, $post->post_status );
			}
		}
	}

	return $overrides;
}

/**
 * Run the non-destructive Tuifan starter-site setup.
 *
 * ## EXAMPLES
 *
 *     wp tuifan setup
 *
 * @param array<int,string>     $args       Positional arguments (unused).
 * @param array<string, string> $assoc_args Associative arguments (unused).
 */
function tuifan_starter_site_setup( array $args = array(), array $assoc_args = array() ): void {
	unset( $args, $assoc_args );

	if ( 'tuifan' !== get_stylesheet() ) {
		WP_CLI::error( 'Activate the Tuifan theme (wp theme activate tuifan) before running this command.' );
	}

	$stored = get_option( 'tuifan_starter_ids', array() );
	$stored = is_array( $stored ) ? $stored : array();
	$slugs  = tuifan_starter_resolved_slugs( $stored );

	WP_CLI::log( '== Current state (before changes) ==' );

	$category_count = wp_count_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $category_count ) ) {
		$category_count = 0;
	}

	$state = array(
		'stylesheet'     => get_stylesheet(),
		'show_on_front'  => (string) get_option( 'show_on_front' ),
		'page_on_front'  => (string) get_option( 'page_on_front' ),
		'page_for_posts' => (string) get_option( 'page_for_posts' ),
		'permalink'      => (string) get_option( 'permalink_structure' ),
		'site_name'      => (string) get_option( 'blogname' ),
		'pages'          => (string) wp_count_posts( 'page' )->publish,
		'posts'          => (string) wp_count_posts( 'post' )->publish,
		'categories'     => (string) $category_count,
		'stored_ids'     => (string) wp_json_encode( $stored ),
	);

	$rows = array();

	foreach ( $state as $key => $value ) {
		$rows[] = array(
			'key'   => $key,
			'value' => $value,
		);
	}

	WP_CLI\Utils\format_items( 'table', $rows, array( 'key', 'value' ) );

	$overrides = tuifan_starter_existing_overrides();

	foreach ( $overrides as $key => $found ) {
		if ( array() === $found ) {
			WP_CLI::log( sprintf( 'Existing child %s overrides: none', str_replace( '_', ' ', $key ) ) );

			continue;
		}

		WP_CLI::log( sprintf( 'Existing child %s overrides (preserved): %s', str_replace( '_', ' ', $key ), implode( ', ', $found ) ) );
	}

	$backup_path = dirname( rtrim( ABSPATH, '/' ) ) . '/tuifan-backup-' . gmdate( 'Ymd-His' ) . '.sql';

	WP_CLI::log( sprintf( 'Creating database backup at %s', $backup_path ) );
	WP_CLI::runcommand( 'db export ' . escapeshellarg( $backup_path ), array( 'launch' => false ) );

	if ( ! is_readable( $backup_path ) || 0 === (int) filesize( $backup_path ) ) {
		WP_CLI::error( 'Database backup failed; no changes were made.' );
	}

	$report    = array();
	$author_id = tuifan_starter_author_id();

	// Pages.
	$page_ids = array();

	foreach ( tuifan_starter_page_definitions() as $slug => $definition ) {
		$page_ids[ $slug ] = tuifan_starter_ensure_page(
			$slug,
			$definition,
			tuifan_starter_stored_id( $stored, 'pages', $slug ),
			$slugs['pages'][ $slug ] ?? '',
			$author_id,
			$report
		);
	}

	// Categories.
	foreach ( tuifan_starter_categories() as $term_slug => $name ) {
		$existing = get_term_by( 'slug', $term_slug, 'category' );

		if ( $existing instanceof WP_Term ) {
			$report[ 'category:' . $term_slug ] = sprintf( 'reused #%d', (int) $existing->term_id );

			continue;
		}

		$created = wp_insert_term( $name, 'category', array( 'slug' => $term_slug ) );

		$report[ 'category:' . $term_slug ] = is_wp_error( $created )
			? 'error: ' . $created->get_error_message()
			: sprintf( 'created #%d', (int) $created['term_id'] );
	}

	// Guides.
	$articles    = tuifan_get_starter_articles();
	$article_ids = array();
	$order       = tuifan_starter_article_order();

	foreach ( $order as $index => $slug ) {
		if ( ! isset( $articles[ $slug ] ) ) {
			continue;
		}

		$article_ids[ $slug ] = tuifan_starter_ensure_article(
			$slug,
			$articles[ $slug ],
			(int) $index,
			tuifan_starter_stored_id( $stored, 'posts', $slug ),
			$author_id,
			$report
		);
	}

	// Navigation entity.
	$navigation_id = 0;
	$stored_nav    = isset( $stored['navigation'] ) ? (int) $stored['navigation'] : 0;

	if ( $stored_nav > 0 && get_post( $stored_nav ) instanceof WP_Post ) {
		$navigation_id = $stored_nav;
	}

	if ( $navigation_id < 1 ) {
		$navigation_id = (int) wp_insert_post(
			array(
				'post_type'    => 'wp_navigation',
				'post_status'  => 'publish',
				'post_title'   => 'Tuifan Primary',
				'post_name'    => 'tuifan-primary',
				'post_content' => wp_slash(
					tuifan_starter_navigation_content(
						$page_ids['home'],
						$page_ids['blog'],
						$page_ids['about'],
						$page_ids['contact'],
						$article_ids
					)
				),
			),
		);

		$report['navigation'] = sprintf( 'created #%d', $navigation_id );
	} else {
		$report['navigation'] = sprintf( 'reused #%d', $navigation_id );
	}

	// Header template part, referencing the navigation entity.
	$header_part_id = 0;
	$theme          = get_stylesheet();
	$existing_parts = get_posts(
		array(
			'post_type'        => 'wp_template_part',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'suppress_filters' => false,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Filtering the theme's own template-part records by the wp_theme taxonomy; there is no other supported query path.
			'tax_query'        => array(
				array(
					'taxonomy' => 'wp_theme',
					'field'    => 'name',
					'terms'    => $theme,
				),
			),
		)
	);

	foreach ( $existing_parts as $existing_part ) {
		if ( $existing_part instanceof WP_Post && 'header' === $existing_part->post_name ) {
			$header_part_id = (int) $existing_part->ID;
			break;
		}
	}

	$stored_part_id = isset( $stored['template_parts']['header'] ) ? (int) $stored['template_parts']['header'] : 0;

	if ( $header_part_id > 0 ) {
		$report['header_part'] = $stored_part_id === $header_part_id
			? sprintf( 'reused Tuifan header part #%d (navigation ref preserved)', $header_part_id )
			: 'existing Tuifan header customization preserved; navigation created but not substituted into the customized header';
	} elseif ( $navigation_id > 0 ) {
		$header_part_id = (int) wp_insert_post(
			array(
				'post_type'    => 'wp_template_part',
				'post_status'  => 'publish',
				'post_title'   => 'Header',

				/*
				 * The template-part theme lives in the `wp_theme` taxonomy; `post_name`
				 * stays the part slug so this record overrides `parts/header.html`.
				 */
				'post_name'    => 'header',
				'post_content' => wp_slash( tuifan_starter_header_content( $navigation_id ) ),
			)
		);

		if ( $header_part_id > 0 ) {
			wp_set_object_terms( $header_part_id, $theme, 'wp_theme' );
			wp_set_object_terms( $header_part_id, 'header', 'wp_template_part_area' );

			$report['header_part'] = sprintf( 'created #%d with navigation ref #%d', $header_part_id, $navigation_id );
		}
	}

	// Front page + posts page.
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_ids['home'] );
	update_option( 'page_for_posts', $page_ids['blog'] );

	$report['options'] = sprintf(
		'show_on_front=page, page_on_front=%d, page_for_posts=%d',
		$page_ids['home'],
		$page_ids['blog']
	);

	// Persist resolved IDs so repeat runs stay idempotent.
	$page_slugs = array();
	$post_slugs = array();

	foreach ( $page_ids as $slug => $id ) {
		$post = $id > 0 ? get_post( $id ) : null;

		if ( $post instanceof WP_Post ) {
			$page_slugs[ $slug ] = (string) $post->post_name;
		}
	}

	foreach ( $article_ids as $slug => $id ) {
		$post = $id > 0 ? get_post( $id ) : null;

		if ( $post instanceof WP_Post ) {
			$post_slugs[ $slug ] = (string) $post->post_name;
		}
	}

	update_option(
		'tuifan_starter_ids',
		array(
			'pages'          => $page_ids,
			'posts'          => $article_ids,
			'navigation'     => $navigation_id,
			'template_parts' => array( 'header' => $header_part_id ),
			'slugs'          => array(
				'pages' => $page_slugs,
				'posts' => $post_slugs,
			),
		),
		false
	);

	$report_rows = array();

	foreach ( $report as $key => $value ) {
		$report_rows[] = array(
			'item'   => $key,
			'result' => (string) $value,
		);
	}

	WP_CLI\Utils\format_items( 'table', $report_rows, array( 'item', 'result' ) );

	WP_CLI::success( 'Tuifan starter site configured.' );
}

WP_CLI::add_command( 'tuifan setup', 'tuifan_starter_site_setup' );
