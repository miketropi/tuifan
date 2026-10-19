<?php
/**
 * Title: Tabs
 * Slug: tuifan/tabs
 * Categories: tuifan
 * Description: Accessible field-guide tabs with native editable content.
 * Keywords: tabs, field guide, control, context, evidence
 * Viewport Width: 1400
 *
 * @package Tuifan
 */

/*
 * Native block payloads: every child is a real block structure, never raw HTML
 * inside a block, so the editor can validate and edit each line of copy.
 */
$tuifan_leaf = static fn( string $name, array $attrs, string $html ): array => array(
	'blockName'    => $name,
	'attrs'        => $attrs,
	'innerBlocks'  => array(),
	'innerHTML'    => $html,
	'innerContent' => array( $html ),
);

$tuifan_parent = static function ( string $name, array $attrs, array $children, string $open, string $close ): array {
	return array(
		'blockName'    => $name,
		'attrs'        => $attrs,
		'innerBlocks'  => $children,
		'innerHTML'    => '',
		'innerContent' => array_merge(
			array( $open ),
			array_fill( 0, count( $children ), null ),
			array( $close )
		),
	);
};

$tuifan_ordered_list = static function ( array $rows ) use ( $tuifan_leaf, $tuifan_parent ): array {
	$items = array();

	foreach ( $rows as $row ) {
		$items[] = $tuifan_leaf( 'core/list-item', array(), '<li>' . esc_html( $row ) . '</li>' );
	}

	return $tuifan_parent( 'core/list', array( 'ordered' => true ), $items, '<ol class="wp-block-list">', '</ol>' );
};

$tuifan_data = array(
	array(
		'label'    => '01 / CONTROL',
		'kicker'   => __( 'permission model / bounded access', 'tuifan' ),
		'heading'  => __( 'Give the agent a task. Not the keys.', 'tuifan' ),
		'body'     => __( 'Name what the agent can reach, where it must pause, and how access ends. Start with a reversible task and keep consequential actions behind explicit approval.', 'tuifan' ),
		'action'   => __( 'Read safer permissions', 'tuifan' ),
		'slug'     => 'agent-permissions',
		'terminal' => 'CONTROL.CFG',
		'state'    => 'READ_ONLY',
		'rows'     => array( 'scope = one useful task', 'access = minimum required', 'approval = before high impact', 'exit = revoke after task' ),
		'status'   => __( 'Boundary defined', 'tuifan' ),
	),
	array(
		'label'    => '02 / CONTEXT',
		'kicker'   => __( 'source pack / focused input', 'tuifan' ),
		'heading'  => __( 'Better context produces better answers.', 'tuifan' ),
		'body'     => __( 'Give the agent the smallest complete source pack. State the goal, name the constraints, surface uncertainty, and leave a clean handoff for the next session.', 'tuifan' ),
		'action'   => __( 'Read the context guide', 'tuifan' ),
		'slug'     => 'agent-context',
		'terminal' => 'CONTEXT.PACK',
		'state'    => 'FOCUSED',
		'rows'     => array( 'goal = observable outcome', 'sources = relevant material', 'limits = explicit constraints', 'handoff = decisions + unknowns' ),
		'status'   => __( 'Signal isolated', 'tuifan' ),
	),
	array(
		'label'    => '03 / EVIDENCE',
		'kicker'   => __( 'evaluation loop / observable proof', 'tuifan' ),
		'heading'  => __( 'Review the evidence, not the polish.', 'tuifan' ),
		'body'     => __( 'Test the agent on everyday work. Trace important claims to their source, inspect the path it took, and keep failure cases visible instead of smoothing them away.', 'tuifan' ),
		'action'   => __( 'Read the evaluation guide', 'tuifan' ),
		'slug'     => 'agent-evaluation',
		'terminal' => 'EVAL.LOG',
		'state'    => 'TRACEABLE',
		'rows'     => array( 'task = representative work', 'trace = source + reasoning path', 'criteria = visible acceptance test', 'failures = recorded, not hidden' ),
		'status'   => __( 'Evidence reviewable', 'tuifan' ),
	),
);

$tuifan_panels = array_map(
	static function ( array $panel ) use ( $tuifan_leaf, $tuifan_parent, $tuifan_ordered_list ): array {
		$url = tuifan_get_section_pattern_url( 'post', $panel['slug'] );

		$copy = $tuifan_parent(
			'core/group',
			array( 'className' => 'tuifan-tab-panel__copy' ),
			array(
				$tuifan_leaf( 'core/paragraph', array( 'className' => 'tuifan-tab-panel__kicker' ), '<p class="tuifan-tab-panel__kicker">' . esc_html( $panel['kicker'] ) . '</p>' ),
				$tuifan_leaf( 'core/heading', array( 'level' => 3 ), '<h3 class="wp-block-heading">' . esc_html( $panel['heading'] ) . '</h3>' ),
				$tuifan_leaf( 'core/paragraph', array(), '<p>' . esc_html( $panel['body'] ) . '</p>' ),
				$tuifan_parent(
					'core/buttons',
					array(),
					array(
						$tuifan_leaf( 'core/button', array(), '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '">' . esc_html( $panel['action'] ) . '</a></div>' ),
					),
					'<div class="wp-block-buttons">',
					'</div>'
				),
			),
			'<div class="wp-block-group tuifan-tab-panel__copy">',
			'</div>'
		);

		$terminal = $tuifan_parent(
			'core/group',
			array( 'className' => 'tuifan-terminal' ),
			array(
				$tuifan_leaf( 'core/paragraph', array(), '<p><strong>' . esc_html( $panel['terminal'] ) . '</strong> / ' . esc_html( $panel['state'] ) . '</p>' ),
				$tuifan_ordered_list( $panel['rows'] ),
				$tuifan_leaf( 'core/paragraph', array( 'className' => 'tuifan-terminal__status' ), '<p class="tuifan-terminal__status">' . esc_html( $panel['status'] ) . '</p>' ),
			),
			'<div class="wp-block-group tuifan-terminal">',
			'</div>'
		);

		return $tuifan_leaf(
			'tuifan/tab-panel',
			array( 'label' => $panel['label'] ),
			'<article class="wp-block-tuifan-tab-panel tuifan-tabs__panel tuifan-tab-panel" data-tuifan-tab-panel data-tuifan-tab-label="' . esc_attr( $panel['label'] ) . '"><p class="tuifan-tabs__panel-label">' . esc_html( $panel['label'] ) . '</p><div class="tuifan-tab-panel__body">' . serialize_blocks( array( $copy, $terminal ) ) . '</div></article>'
		);
	},
	$tuifan_data
);

$tuifan_attrs = array(
	'eyebrow'      => __( '~/field-guide — choose a focus', 'tuifan' ),
	'heading'      => __( 'One practice. Three operating modes.', 'tuifan' ),
	'description'  => __( 'Switch between the three recurring themes in the demo. Each view points to the guide that develops the practice.', 'tuifan' ),
	'headingLevel' => 2,
	'tabListLabel' => __( 'Field guide', 'tuifan' ),
);

/* translators: %d: position of a tab whose panel has no label. */
$tuifan_tab_fallback = esc_attr__( 'Tab %d', 'tuifan' );

$tuifan_html = '<section class="wp-block-tuifan-tabs tuifan-section tuifan-tabs" data-tuifan-tabs data-tuifan-tab-fallback="' . $tuifan_tab_fallback . '" data-tuifan-tab-list-label="' . esc_attr( $tuifan_attrs['tabListLabel'] ) . '"><header class="tuifan-section__head"><p class="tuifan-section__eyebrow">' . esc_html( $tuifan_attrs['eyebrow'] ) . '</p><h2 class="tuifan-section__heading">' . esc_html( $tuifan_attrs['heading'] ) . '</h2><p class="tuifan-section__description">' . esc_html( $tuifan_attrs['description'] ) . '</p></header><div class="tuifan-tabs__panels">' . serialize_blocks( $tuifan_panels ) . '</div></section>';

$tuifan_payload = $tuifan_leaf( 'tuifan/tabs', $tuifan_attrs, $tuifan_html );

echo serialize_block( $tuifan_payload ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pattern payload; every value is escaped where it is interpolated.

unset( $tuifan_leaf, $tuifan_parent, $tuifan_ordered_list, $tuifan_data, $tuifan_panels, $tuifan_attrs, $tuifan_html, $tuifan_payload );
