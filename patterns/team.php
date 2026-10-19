<?php
/**
 * Title: Team
 * Slug: tuifan/team
 * Categories: tuifan
 * Description: Three editable people or editorial topic cards with local links.
 * Keywords: team, people, topics, editorial map
 * Viewport Width: 1400
 *
 * @package Tuifan
 */

$tuifan_leaf    = static fn( string $name, array $attrs, string $html ): string => serialize_block(
	array(
		'blockName'    => $name,
		'attrs'        => $attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => $html,
		'innerContent' => array( $html ),
	)
);
$tuifan_about   = tuifan_get_section_pattern_url( 'page', 'about' );
$tuifan_contact = tuifan_get_section_pattern_url( 'page', 'contact' );
$tuifan_data    = array(
	array( 'R_', __( 'getting-started / 01 guide', 'tuifan' ), __( 'Start with the first agent', 'tuifan' ), __( 'Choose a useful task, write a clear brief, and review the result before taking action.', 'tuifan' ), 'getting-started' ),
	array( 'E_', __( 'safety / 01 guide', 'tuifan' ), __( 'Set safer permissions', 'tuifan' ), __( 'Define practical boundaries, approval gates, and a way back when something goes wrong.', 'tuifan' ), 'safety' ),
	array( 'Q_', __( 'workflows / 02 guides', 'tuifan' ), __( 'Build context. Evaluate results.', 'tuifan' ), __( 'Make uncertainty visible, keep a clean handoff, check evidence, and track failures.', 'tuifan' ), 'workflows' ),
);
$tuifan_cards   = array_map(
	static function ( array $item ) use ( $tuifan_leaf, $tuifan_about, $tuifan_contact ): string {
		$links     = array(
			array(
				'label' => sprintf(
					/* translators: %s: topic name, for example "getting started". */
					__( 'View %s guides', 'tuifan' ),
					str_replace( '-', ' ', $item[4] )
				),
				'url'   => tuifan_get_section_pattern_url( 'category', $item[4] ),
				'icon'  => 'arrow',
			),
			array(
				'label' => __( 'About', 'tuifan' ),
				'url'   => $tuifan_about,
				'icon'  => 'arrow',
			),
			array(
				'label' => __( 'Contact', 'tuifan' ),
				'url'   => $tuifan_contact,
				'icon'  => 'arrow',
			),
		);
		$attrs     = array(
			'monogram'     => $item[0],
			'name'         => $item[2],
			'role'         => $item[1],
			'bio'          => $item[3],
			'imageId'      => 0,
			'imageUrl'     => '',
			'imageAlt'     => '',
			'imageWidth'   => 72,
			'imageHeight'  => 72,
			'links'        => $links,
			'headingLevel' => 3,
		);
		$link_html = '';
		foreach ( $links as $link ) {
			$link_html .= '<a href="' . esc_url( $link['url'] ) . '" aria-label="' . esc_attr( $link['label'] ) . '"><svg aria-hidden="true" focusable="false" viewBox="0 0 18 18"><path d="M3 9h11m-4-4 4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.4"></path></svg><span>' . esc_html( $link['label'] ) . '</span></a>';
		}
		$html = '<article class="wp-block-tuifan-team-card tuifan-team-card"><div class="tuifan-team-card__identity"><span class="tuifan-team-card__monogram">' . esc_html( $item[0] ) . '</span> <span class="tuifan-team-card__role">' . esc_html( $item[1] ) . '</span></div><h3 class="tuifan-team-card__name">' . esc_html( $item[2] ) . '</h3> <p class="tuifan-team-card__bio">' . esc_html( $item[3] ) . '</p> <nav class="tuifan-team-card__links" aria-label="' . esc_attr( $item[2] ) . '">' . $link_html . '</nav><span class="tuifan-team-card__corner" aria-hidden="true"></span></article>';
		return $tuifan_leaf( 'tuifan/team-card', $attrs, $html );
	},
	$tuifan_data
);
$tuifan_attrs   = array(
	'eyebrow'      => __( '~/topics — editorial map', 'tuifan' ),
	'heading'      => __( 'Three routes through the guide library.', 'tuifan' ),
	'description'  => __( 'The demo publishes no team roster or social profiles, so these cards map its real topic areas and link only to verified site routes.', 'tuifan' ),
	'headingLevel' => 2,
);
$tuifan_html    = '<section class="wp-block-tuifan-team tuifan-section tuifan-team"><header class="tuifan-section__head"><p class="tuifan-section__eyebrow">' . esc_html( $tuifan_attrs['eyebrow'] ) . '</p><h2 class="tuifan-section__heading">' . esc_html( $tuifan_attrs['heading'] ) . '</h2><p class="tuifan-section__description">' . esc_html( $tuifan_attrs['description'] ) . '</p></header><div class="tuifan-team__grid">' . implode( '', $tuifan_cards ) . '</div></section>';
echo $tuifan_leaf( 'tuifan/team', $tuifan_attrs, $tuifan_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
unset( $tuifan_leaf, $tuifan_about, $tuifan_contact, $tuifan_data, $tuifan_cards, $tuifan_attrs, $tuifan_html );
