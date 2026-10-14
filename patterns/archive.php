<?php
/**
 * Title: Tuifan archive
 * Slug: tuifan/archive
 * Categories: tuifan
 * Description: Terminal archive listing: contextual title, search controls, numbered entry rows and the live sidebar.
 * Keywords: archive, blog, entries, search, listing
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
	<p class="tuifan-breadcrumb__trail">: <b>~/posts</b></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"tuifan-page-heading","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-page-heading">
	<!-- wp:paragraph {"className":"tuifan-eyebrow"} -->
	<p class="tuifan-eyebrow"><?php esc_html_e( 'All entries', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1,"className":"tuifan-blog-heading"} -->
	<h1 class="wp-block-heading tuifan-blog-heading"><?php esc_html_e( '~/blog — all entries', 'tuifan' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:query-title {"type":"archive","level":1,"showPrefix":false,"className":"tuifan-archive-heading"} /-->

	<!-- wp:paragraph {"className":"tuifan-lede"} -->
	<p class="tuifan-lede"><?php esc_html_e( 'Every published guide, newest first. Filter by topic or search the archive.', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:tuifan/archive-controls /-->

<!-- wp:group {"className":"tuifan-grid","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-grid">
	<!-- wp:tuifan/entry-list {"source":"main"} /-->

	<!-- wp:template-part {"slug":"sidebar","theme":"tuifan"} /-->
</div>
<!-- /wp:group -->
