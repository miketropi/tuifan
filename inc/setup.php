<?php
/**
 * Child theme setup hooks.
 *
 * Inherited theme supports stay in the parent; nothing is duplicated here.
 *
 * @package Tuifan
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load child translations.
 *
 * Runs after the parent (default priority 10) so child strings win.
 */
function tuifan_setup(): void {
	load_child_theme_textdomain( 'tuifan', TUIFAN_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'tuifan_setup', 20 );

/**
 * Register the child pattern category used by `patterns/*.php`.
 */
function tuifan_register_pattern_categories(): void {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'tuifan',
		array(
			'label'       => __( 'Tuifan', 'tuifan' ),
			'description' => __( 'Terminal-styled page layouts from the Tuifan child theme.', 'tuifan' ),
		)
	);
}
add_action( 'init', 'tuifan_register_pattern_categories' );
