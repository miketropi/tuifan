<?php
/**
 * Form Template — dynamic render.
 *
 * Intentionally disconnected UI: no `<form>`, no endpoint, no storage, no
 * validation flow. Every control is disabled and the notice stays visible.
 *
 * @package Tuifan
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Saved inner HTML.
 * @var WP_Block             $block      Block instance.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tuifan_variant = isset( $attributes['variant'] ) ? (string) $attributes['variant'] : 'contact';

if ( ! in_array( $tuifan_variant, array( 'contact', 'newsletter' ), true ) ) {
	$tuifan_variant = 'contact';
}

$tuifan_uid           = wp_unique_id( 'tuifan-form-' );
$tuifan_notice_id     = $tuifan_uid . '-notice';
$tuifan_is_newsletter = 'newsletter' === $tuifan_variant;

$tuifan_default_button = $tuifan_is_newsletter
	? __( 'Subscribe', 'tuifan' )
	: __( 'Send message', 'tuifan' );

$tuifan_button_label = isset( $attributes['buttonLabel'] ) ? trim( (string) $attributes['buttonLabel'] ) : '';

if ( '' === $tuifan_button_label ) {
	$tuifan_button_label = $tuifan_default_button;
}

$tuifan_notice = isset( $attributes['notice'] ) ? trim( (string) $attributes['notice'] ) : '';

/*
 * The block.json default is the canonical English string; translate it when the
 * editor value has not been customized.
 */
if ( '' === $tuifan_notice || 'Template preview only. Nothing is sent or stored.' === $tuifan_notice ) {
	$tuifan_notice = __( 'Template preview only. Nothing is sent or stored.', 'tuifan' );
}

$tuifan_legend = $tuifan_is_newsletter
	? __( 'Newsletter signup template, preview only', 'tuifan' )
	: __( 'Contact form template, preview only', 'tuifan' );

$tuifan_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'tuifan-form tuifan-form--' . $tuifan_variant,
	)
);

$tuifan_field = static function ( string $type, string $key, string $label, string $placeholder, array $extra = array() ) use ( $tuifan_uid ): string {
	$input_id = $tuifan_uid . '-' . $key;

	$attributes = '';
	foreach ( $extra as $name => $value ) {
		$attributes .= sprintf( ' %s="%s"', esc_attr( (string) $name ), esc_attr( (string) $value ) );
	}

	if ( 'textarea' === $type ) {
		$control = sprintf(
			'<textarea class="tuifan-field__input tuifan-field__input--textarea" id="%1$s" rows="6" placeholder="%2$s" disabled%3$s></textarea>',
			esc_attr( $input_id ),
			esc_attr( $placeholder ),
			$attributes
		);
	} else {
		$control = sprintf(
			'<input class="tuifan-field__input" id="%1$s" type="%2$s" placeholder="%3$s" disabled%4$s />',
			esc_attr( $input_id ),
			esc_attr( $type ),
			esc_attr( $placeholder ),
			$attributes
		);
	}

	return sprintf(
		'<div class="tuifan-field"><label class="tuifan-field__label" for="%1$s">%2$s</label>%3$s</div>',
		esc_attr( $input_id ),
		esc_html( $label ),
		$control
	);
};

$tuifan_fields = '';

if ( $tuifan_is_newsletter ) {
	$tuifan_fields .= $tuifan_field(
		'email',
		'email',
		__( 'Email address', 'tuifan' ),
		__( 'you@example.com', 'tuifan' ),
		array(
			'autocomplete' => 'email',
			'maxlength'    => '254',
		)
	);
} else {
	$tuifan_fields .= $tuifan_field(
		'text',
		'name',
		__( '01 / Your name', 'tuifan' ),
		__( 'How should I call you?', 'tuifan' ),
		array(
			'autocomplete' => 'name',
			'maxlength'    => '100',
		)
	);
	$tuifan_fields .= $tuifan_field(
		'email',
		'email',
		__( '02 / Email address', 'tuifan' ),
		__( 'you@example.com', 'tuifan' ),
		array(
			'autocomplete' => 'email',
			'maxlength'    => '254',
		)
	);
	$tuifan_fields .= $tuifan_field(
		'textarea',
		'message',
		__( '03 / Your message', 'tuifan' ),
		__( 'What’s on your mind? (at least 10 characters)', 'tuifan' ),
		array(
			'maxlength' => '5000',
		)
	);
}

$tuifan_button = sprintf(
	'<button class="tuifan-form__submit%1$s" type="button" disabled>%2$s%3$s</button>',
	$tuifan_is_newsletter ? '' : ' wp-element-button',
	esc_html( $tuifan_button_label ),
	tuifan_get_arrow_icon( $tuifan_is_newsletter ? 'right' : 'up-right' )
);

if ( $tuifan_is_newsletter ) {
	$tuifan_body = sprintf(
		'<div class="tuifan-form__row">%1$s%2$s</div>',
		$tuifan_fields,
		$tuifan_button
	);
} else {
	$tuifan_body = $tuifan_fields . $tuifan_button;
}

printf(
	'<div %1$s><fieldset class="tuifan-form__fields" disabled aria-describedby="%2$s"><legend class="screen-reader-text">%3$s</legend>%4$s</fieldset><p class="tuifan-form__notice" id="%2$s">%5$s</p></div>',
	$tuifan_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes().
	esc_attr( $tuifan_notice_id ),
	esc_html( $tuifan_legend ),
	$tuifan_body, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped while building.
	esc_html( $tuifan_notice )
);
