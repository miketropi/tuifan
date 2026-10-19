<?php
/**
 * Title: Posts carousel
 * Slug: tuifan/posts-carousel
 * Categories: tuifan
 * Description: Dynamic reading queue populated from published WordPress posts.
 * Keywords: posts, carousel, blog, reading queue
 * Viewport Width: 1400
 *
 * @package Tuifan
 */

$tuifan_attrs   = array(
	'eyebrow'       => __( '~/posts — reading queue', 'tuifan' ),
	'heading'       => __( 'Continue from the question already on your mind.', 'tuifan' ),
	'description'   => '',
	'headingLevel'  => 2,
	'postsToShow'   => 4,
	'categoryId'    => 0,
	'orderBy'       => 'date',
	'order'         => 'DESC',
	'carouselLabel' => __( 'Reading queue', 'tuifan' ),
);
$tuifan_payload = serialize_block(
	array(
		'blockName'    => 'tuifan/posts-carousel',
		'attrs'        => $tuifan_attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => '',
		'innerContent' => array(),
	)
);

echo $tuifan_payload; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pattern payload produced by serialize_block().

unset( $tuifan_attrs, $tuifan_payload );
