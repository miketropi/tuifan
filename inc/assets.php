<?php
/**
 * Front-end and editor assets.
 *
 * Authored CSS/JS only: `assets/css/*.css` and the esbuild output in
 * `assets/js/site.js`. No build step is required for the PHP side.
 *
 * @package Tuifan
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve a child asset to `{ version, path }`, or null when missing.
 *
 * @param string $rel Relative path from the child theme root.
 *
 * @return array{ version: string, path: string }|null
 */
function tuifan_asset( string $rel ): ?array {
	$path = TUIFAN_DIR . $rel;

	if ( ! is_readable( $path ) ) {
		return null;
	}

	return array(
		'version' => (string) filemtime( $path ),
		'path'    => $path,
	);
}

/**
 * Register the shared progressive carousel runtime for carousel blocks.
 */
function tuifan_register_carousel_script(): void {
	$script = tuifan_asset( '/blocks/testimonial-carousel/view.js' );
	$asset  = TUIFAN_DIR . '/blocks/testimonial-carousel/view.asset.php';

	if ( null === $script || ! is_readable( $asset ) ) {
		return;
	}

	$metadata = require $asset;

	if ( ! is_array( $metadata ) ) {
		return;
	}

	wp_register_script(
		'tuifan-carousel',
		TUIFAN_URI . '/blocks/testimonial-carousel/view.js',
		isset( $metadata['dependencies'] ) && is_array( $metadata['dependencies'] ) ? $metadata['dependencies'] : array(),
		isset( $metadata['version'] ) ? (string) $metadata['version'] : $script['version'],
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'init', 'tuifan_register_carousel_script', 9 );

/**
 * Style handles the child sheet must load after.
 *
 * @return list<string>
 */
function tuifan_style_dependencies(): array {
	if ( wp_style_is( 'nextora-app', 'registered' ) ) {
		return array( 'nextora-app' );
	}

	if ( wp_style_is( 'global-styles', 'registered' ) ) {
		return array( 'global-styles' );
	}

	return array();
}

/**
 * Enqueue the child design layer for the front end and the block editor canvas.
 */
function tuifan_enqueue_theme_style(): void {
	$asset = tuifan_asset( '/assets/css/theme.css' );

	if ( null === $asset ) {
		return;
	}

	wp_enqueue_style(
		'tuifan-theme',
		TUIFAN_URI . '/assets/css/theme.css',
		tuifan_style_dependencies(),
		$asset['version']
	);
}
add_action( 'enqueue_block_assets', 'tuifan_enqueue_theme_style', 30 );

/**
 * Editor-canvas-only parity overrides.
 *
 * The selectors inside `editor.css` are all scoped, so the sheet is inert in
 * the editor chrome; it is also added through `add_editor_style()` because the
 * iframed canvas loads its own document.
 */
function tuifan_enqueue_editor_style(): void {
	$asset = tuifan_asset( '/assets/css/editor.css' );

	if ( null === $asset ) {
		return;
	}

	wp_enqueue_style(
		'tuifan-editor',
		TUIFAN_URI . '/assets/css/editor.css',
		tuifan_style_dependencies(),
		$asset['version']
	);
}
add_action( 'enqueue_block_editor_assets', 'tuifan_enqueue_editor_style', 30 );

/**
 * Enqueue the admin-only canvas parity script.
 *
 * The block editor canvas renders post content, never the page template, so the
 * script mirrors the edited `template` attribute onto the canvas document as
 * `tuifan-canvas-template-<slug>`; `assets/css/editor.css` then widens the
 * canvas for `page-full-width`, matching what `theme.css` does for
 * `.tuifan-prose--wide` on the front end.
 */
function tuifan_enqueue_editor_canvas_script(): void {
	$asset = tuifan_asset( '/assets/js/editor-canvas.js' );

	if ( null === $asset ) {
		return;
	}

	wp_enqueue_script(
		'tuifan-editor-canvas',
		TUIFAN_URI . '/assets/js/editor-canvas.js',
		array(),
		$asset['version'],
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'tuifan_enqueue_editor_canvas_script', 30 );

/**
 * Register the editor stylesheet for the iframed canvas.
 */
add_action(
	'after_setup_theme',
	static function (): void {
		if ( is_readable( TUIFAN_DIR . '/assets/css/editor.css' ) ) {
			add_editor_style( 'assets/css/editor.css' );
		}
	},
	20
);

/**
 * Enqueue the child front-end script (search shortcut + article code copy).
 */
function tuifan_enqueue_site_script(): void {
	$asset = tuifan_asset( '/assets/js/site.js' );

	if ( null === $asset ) {
		return;
	}

	wp_enqueue_script(
		'tuifan-site',
		TUIFAN_URI . '/assets/js/site.js',
		array(),
		$asset['version'],
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'tuifan-site',
		'tuifanSite',
		array(
			'copy'       => __( 'Copy', 'tuifan' ),
			'copied'     => __( 'Copied', 'tuifan' ),
			'copyFailed' => __( 'Copy unavailable. Select the text to copy.', 'tuifan' ),
			'codeLabel'  => __( 'code', 'tuifan' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'tuifan_enqueue_site_script', 30 );

/**
 * Add the child body class used by front-end-only scoping.
 *
 * @param array<int,string> $classes Existing body classes.
 *
 * @return list<string>
 */
function tuifan_body_class( array $classes ): array {
	$classes[] = 'tuifan-theme';

	return $classes;
}
add_filter( 'body_class', 'tuifan_body_class' );
