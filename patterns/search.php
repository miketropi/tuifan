<?php
/**
 * Title: Tuifan search
 * Slug: tuifan/search
 * Categories: tuifan
 * Description: Terminal search view: server-rendered search field, live category filters, results and the sidebar.
 * Keywords: search, find, archive, results
 * Viewport Width: 1400
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
	<p class="tuifan-breadcrumb__trail">: <b>~/search</b></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"tuifan-page-heading","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-page-heading">
	<!-- wp:paragraph {"className":"tuifan-eyebrow"} -->
	<p class="tuifan-eyebrow"><?php esc_html_e( 'Find something useful', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading"><?php esc_html_e( 'Search the archive.', 'tuifan' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"tuifan-lede"} -->
	<p class="tuifan-lede"><?php esc_html_e( 'Find a guide by task, topic, or question.', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:tuifan/archive-controls /-->

<!-- wp:group {"className":"tuifan-grid","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-grid">
	<!-- wp:tuifan/entry-list {"source":"main","heading":"~/results"} /-->

	<!-- wp:template-part {"slug":"sidebar","theme":"tuifan"} /-->
</div>
<!-- /wp:group -->
