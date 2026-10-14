<?php
/**
 * Register the child theme blocks and inserter category.
 *
 * Parent blocks are registered by the parent theme; nothing here re-registers
 * or duplicates them.
 *
 * @package Tuifan
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register every `blocks/<slug>/block.json` in the child theme.
 */
function tuifan_register_blocks(): void {
	$blocks_dir = TUIFAN_DIR . '/blocks';

	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	$block_dirs = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

	if ( false === $block_dirs ) {
		return;
	}

	foreach ( $block_dirs as $block_dir ) {
		if ( is_readable( $block_dir . '/block.json' ) ) {
			register_block_type( $block_dir );
		}
	}
}
add_action( 'init', 'tuifan_register_blocks' );

/**
 * Add the Tuifan block category to the inserter.
 *
 * @param array<int, array<string, mixed>> $categories Existing categories.
 *
 * @return array<int, array<string, mixed>>
 */
function tuifan_register_block_categories( array $categories ): array {
	foreach ( $categories as $category ) {
		if ( isset( $category['slug'] ) && 'tuifan' === $category['slug'] ) {
			return $categories;
		}
	}

	array_unshift(
		$categories,
		array(
			'slug'  => 'tuifan',
			'title' => __( 'Tuifan Blocks', 'tuifan' ),
			'icon'  => 'editor-code',
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'tuifan_register_block_categories', 10, 1 );
