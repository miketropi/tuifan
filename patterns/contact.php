<?php
/**
 * Title: Tuifan contact
 * Slug: tuifan/contact
 * Categories: tuifan
 * Description: Terminal contact page with an intentionally disconnected form template, connection status and delivery panels.
 * Keywords: contact, form, template
 * Viewport Width: 1200
 *
 * @package Tuifan
 */

?>
<!-- wp:group {"className":"tuifan-breadcrumb","layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"baseline"}} -->
<div class="wp-block-group tuifan-breadcrumb">
	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'guest@', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->
	<!-- wp:site-title {"level":0,"className":"tuifan-breadcrumb__site"} /-->
	<!-- wp:paragraph {"className":"tuifan-breadcrumb__trail"} -->
	<p class="tuifan-breadcrumb__trail">: <b>~/contact</b></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"tuifan-page-heading","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-page-heading">
	<!-- wp:paragraph {"className":"tuifan-eyebrow"} -->
	<p class="tuifan-eyebrow"><?php esc_html_e( 'Open a connection', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading"><?php esc_html_e( 'Hello on the other end.', 'tuifan' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"tuifan-lede"} -->
	<p class="tuifan-lede"><?php esc_html_e( 'Have a question about a guide, a correction, or an AI workflow worth exploring? Draft a note below.', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"tuifan-grid","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-grid">
	<!-- wp:tuifan/form-template {"variant":"contact"} /-->

	<!-- wp:group {"className":"tuifan-aside","layout":{"type":"default"}} -->
	<div class="wp-block-group tuifan-aside">
		<!-- wp:group {"className":"tuifan-aside__box","layout":{"type":"default"}} -->
		<div class="wp-block-group tuifan-aside__box">
			<!-- wp:paragraph {"className":"tuifan-aside__heading"} -->
			<p class="tuifan-aside__heading"><?php esc_html_e( 'Connection status', 'tuifan' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:group {"className":"tuifan-aside__body","layout":{"type":"default"}} -->
			<div class="wp-block-group tuifan-aside__body">
				<!-- wp:paragraph -->
				<p><?php esc_html_e( 'mode: template only', 'tuifan' ); ?><br><?php esc_html_e( 'delivery: not connected', 'tuifan' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><?php esc_html_e( 'This form is a presentation template. Nothing is sent or stored, and a delivery service is needed before messages can be sent.', 'tuifan' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"tuifan-aside__box","layout":{"type":"default"}} -->
		<div class="wp-block-group tuifan-aside__box">
			<!-- wp:paragraph {"className":"tuifan-aside__heading"} -->
			<p class="tuifan-aside__heading"><?php esc_html_e( 'Good things to send', 'tuifan' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:group {"className":"tuifan-aside__body","layout":{"type":"default"}} -->
			<div class="wp-block-group tuifan-aside__body">
				<!-- wp:paragraph -->
				<p><?php esc_html_e( 'A helpful correction.', 'tuifan' ); ?><br><?php esc_html_e( 'A topic worth exploring.', 'tuifan' ); ?><br><?php esc_html_e( 'A tool you can&#8217;t stop using.', 'tuifan' ); ?><br><?php esc_html_e( 'A simple hello.', 'tuifan' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
