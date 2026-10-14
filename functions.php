<?php
/**
 * Tuifan child theme bootstrap.
 *
 * Loads child-only includes. Parent (Nextora) functions.php is loaded by
 * WordPress automatically; nothing here re-requires parent files.
 *
 * @package Tuifan
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'TUIFAN_VERSION' ) ) {
	define( 'TUIFAN_VERSION', '1.0.0' );
}

if ( ! defined( 'TUIFAN_DIR' ) ) {
	define( 'TUIFAN_DIR', __DIR__ );
}

if ( ! defined( 'TUIFAN_URI' ) ) {
	define( 'TUIFAN_URI', get_stylesheet_directory_uri() );
}

/**
 * Load a child include from `inc/`.
 *
 * @param string $file Basename without the `.php` extension.
 */
function tuifan_require_inc( string $file ): void {
	$path = TUIFAN_DIR . '/inc/' . $file . '.php';

	if ( is_readable( $path ) ) {
		require_once $path;
	}
}

foreach ( array( 'setup', 'assets', 'blocks', 'content', 'starter-site' ) as $tuifan_inc_file ) {
	tuifan_require_inc( $tuifan_inc_file );
}

unset( $tuifan_inc_file );
