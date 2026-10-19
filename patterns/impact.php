<?php
/**
 * Title: Impact
 * Slug: tuifan/impact
 * Categories: tuifan
 * Description: Ordered reader outcomes and judgment signals.
 * Keywords: impact, outcomes, steps, judgment path
 * Viewport Width: 1400
 *
 * @package Tuifan
 */

$tuifan_leaf  = static fn( string $name, array $attrs, string $html ): string => serialize_block(
	array(
		'blockName'    => $name,
		'attrs'        => $attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => $html,
		'innerContent' => array( $html ),
	)
);
$tuifan_data  = array(
	array( __( 'Start with a bounded task.', 'tuifan' ), __( 'Choose work that is useful, reversible, and easy to review before the agent takes action.', 'tuifan' ) ),
	array( __( 'Give the model focused context.', 'tuifan' ), __( 'Provide the right sources, state the constraints, and make the expected outcome explicit.', 'tuifan' ) ),
	array( __( 'Review evidence, not presentation.', 'tuifan' ), __( 'Test the result against real tasks and visible criteria instead of trusting a polished response.', 'tuifan' ) ),
);
$tuifan_rows  = array_map(
	static function ( array $row ) use ( $tuifan_leaf ): string {
		$attrs = array(
			'title'        => $row[0],
			'description'  => $row[1],
			'headingLevel' => 3,
		);
		$html  = '<li class="wp-block-tuifan-impact-row tuifan-impact-row"><span class="tuifan-impact-row__number" aria-hidden="true"></span><div><h3 class="tuifan-impact-row__title">' . esc_html( $row[0] ) . '</h3> <p class="tuifan-impact-row__description">' . esc_html( $row[1] ) . '</p></div><span class="tuifan-impact-row__arrow" aria-hidden="true">→</span></li>';
		return $tuifan_leaf( 'tuifan/impact-row', $attrs, $html );
	},
	$tuifan_data
);
$tuifan_attrs = array(
	'eyebrow'      => __( '~/impact — reader outcomes', 'tuifan' ),
	'heading'      => __( 'Use AI with clearer judgment.', 'tuifan' ),
	'description'  => __( 'Three practical habits from the demo, arranged as a compact signal path from task definition to review.', 'tuifan' ),
	'headingLevel' => 2,
	'statusLabel'  => __( 'Judgment path', 'tuifan' ),
	'statusSuffix' => __( 'ready', 'tuifan' ),
	'statusCount'  => 3,
);
$tuifan_count = sprintf(
	/* translators: %s: zero-padded count of impact rows. */
	_n( '%s signal', '%s signals', 3, 'tuifan' ),
	'03'
);
$tuifan_html = '<section class="wp-block-tuifan-impact tuifan-section tuifan-impact"><header class="tuifan-section__head"><p class="tuifan-section__eyebrow">' . esc_html( $tuifan_attrs['eyebrow'] ) . '</p><h2 class="tuifan-section__heading">' . esc_html( $tuifan_attrs['heading'] ) . '</h2><p class="tuifan-section__description">' . esc_html( $tuifan_attrs['description'] ) . '</p></header><div class="tuifan-impact__panel"><div class="tuifan-impact__status"><span>' . esc_html( $tuifan_attrs['statusLabel'] ) . '</span><span>' . esc_html( $tuifan_count ) . '</span><span>' . esc_html( $tuifan_attrs['statusSuffix'] ) . '</span></div><ol class="tuifan-impact__rows">' . implode( '', $tuifan_rows ) . '</ol></div></section>';
echo $tuifan_leaf( 'tuifan/impact', $tuifan_attrs, $tuifan_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
unset( $tuifan_leaf, $tuifan_data, $tuifan_rows, $tuifan_attrs, $tuifan_count, $tuifan_html );
