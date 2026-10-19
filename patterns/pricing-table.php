<?php
/**
 * Title: Pricing table
 * Slug: tuifan/pricing-table
 * Categories: tuifan
 * Description: Three editable reader paths or pricing cards with native feature lists.
 * Keywords: pricing, plans, reader paths, cards
 * Viewport Width: 1400
 *
 * @package Tuifan
 */

$tuifan_block   = static fn( string $name, array $attrs, string $html ): string => serialize_block(
	array(
		'blockName'    => $name,
		'attrs'        => $attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => $html,
		'innerContent' => array( $html ),
	)
);
$tuifan_list    = static function ( array $items ) use ( $tuifan_block ): string {
	$children = array_map( static fn( string $item ): string => $tuifan_block( 'core/list-item', array(), '<li>' . esc_html( $item ) . '</li>' ), $items );
	return $tuifan_block( 'core/list', array(), '<ul class="wp-block-list">' . implode( '', $children ) . '</ul>' );
};
$tuifan_card    = static function ( array $attrs, array $features ) use ( $tuifan_block, $tuifan_list ): string {
	$classes = 'wp-block-tuifan-pricing-card tuifan-pricing-card' . ( ! empty( $attrs['featured'] ) ? ' is-featured' : '' );
	$action  = '';
	if ( 'preview' === $attrs['actionMode'] ) {
		$action = '<details class="tuifan-pricing-card__preview"><summary class="tuifan-section__action">' . esc_html( $attrs['actionLabel'] ) . '</summary><p>' . esc_html( $attrs['previewText'] ) . '</p></details>';
	} else {
		$action = '<a class="tuifan-section__action" href="' . esc_url( $attrs['actionUrl'] ) . '">' . esc_html( $attrs['actionLabel'] ) . '</a>';
	}
	$html  = '<article class="' . esc_attr( $classes ) . '">';
	$html .= '' !== $attrs['badge'] ? '<span class="tuifan-pricing-card__badge">' . esc_html( $attrs['badge'] ) . '</span>' : '';
	$html .= '<h3 class="tuifan-pricing-card__name">' . esc_html( $attrs['name'] ) . '</h3><div class="tuifan-pricing-card__price-line"><span class="tuifan-pricing-card__price">' . esc_html( $attrs['price'] ) . '</span><span class="tuifan-pricing-card__period">' . esc_html( $attrs['period'] ) . '</span></div><div class="tuifan-pricing-card__features">' . $tuifan_list( $features ) . '</div>' . $action . '</article>';
	return $tuifan_block( 'tuifan/pricing-card', $attrs, $html );
};
$tuifan_archive = tuifan_get_section_pattern_url( 'archive' );
$tuifan_contact = tuifan_get_section_pattern_url( 'page', 'contact' );
$tuifan_cards   = array(
	$tuifan_card(
		array(
			'name'         => __( 'archive', 'tuifan' ),
			'price'        => '$0',
			'period'       => __( '/ public reader access', 'tuifan' ),
			'badge'        => '',
			'actionLabel'  => __( 'View all entries', 'tuifan' ),
			'actionUrl'    => $tuifan_archive,
			'previewText'  => '',
			'featured'     => false,
			'actionMode'   => 'link',
			'headingLevel' => 3,
		),
		array( __( 'Four practical AI-agent guides', 'tuifan' ), __( 'Getting started, safety, and workflows', 'tuifan' ), __( 'Search and keyboard navigation', 'tuifan' ) )
	),
	$tuifan_card(
		array(
			'name'         => __( 'newsletter', 'tuifan' ),
			'price'        => __( 'preview', 'tuifan' ),
			'period'       => __( '/ no registration connected', 'tuifan' ),
			'badge'        => __( 'Recommended', 'tuifan' ),
			'actionLabel'  => __( 'See newsletter preview', 'tuifan' ),
			'actionUrl'    => '',
			'previewText'  => __( 'Template preview only. Nothing is sent or stored.', 'tuifan' ),
			'featured'     => true,
			'actionMode'   => 'preview',
			'headingLevel' => 3,
		),
		array( __( 'Better workflow notes', 'tuifan' ), __( 'Safer permission guidance', 'tuifan' ), __( 'Results you can verify', 'tuifan' ) )
	),
	$tuifan_card(
		array(
			'name'         => __( 'say hello', 'tuifan' ),
			'price'        => __( 'contact', 'tuifan' ),
			'period'       => __( '/ questions and corrections', 'tuifan' ),
			'badge'        => '',
			'actionLabel'  => __( 'Contact the blog', 'tuifan' ),
			'actionUrl'    => $tuifan_contact,
			'previewText'  => '',
			'featured'     => false,
			'actionMode'   => 'link',
			'headingLevel' => 3,
		),
		array( __( 'Ask about a guide', 'tuifan' ), __( 'Share a correction', 'tuifan' ), __( 'Send a thoughtful note', 'tuifan' ) )
	),
);
$tuifan_attrs   = array(
	'eyebrow'      => __( '~/access — reader paths', 'tuifan' ),
	'heading'      => __( 'Choose how you want to explore the archive.', 'tuifan' ),
	'description'  => __( 'The live demo does not announce paid plans. These paths reflect its public archive, newsletter preview, and contact route.', 'tuifan' ),
	'headingLevel' => 2,
);
$tuifan_html    = '<section class="wp-block-tuifan-pricing-table tuifan-section tuifan-pricing"><header class="tuifan-section__head"><p class="tuifan-section__eyebrow">' . esc_html( $tuifan_attrs['eyebrow'] ) . '</p><h2 class="tuifan-section__heading">' . esc_html( $tuifan_attrs['heading'] ) . '</h2><p class="tuifan-section__description">' . esc_html( $tuifan_attrs['description'] ) . '</p></header><div class="tuifan-pricing__grid">' . implode( '', $tuifan_cards ) . '</div></section>';
echo $tuifan_block( 'tuifan/pricing-table', $tuifan_attrs, $tuifan_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
unset( $tuifan_block, $tuifan_list, $tuifan_card, $tuifan_archive, $tuifan_contact, $tuifan_cards, $tuifan_attrs, $tuifan_html );
