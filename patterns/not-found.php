<?php
/**
 * Title: Tuifan not found
 * Slug: tuifan/not-found
 * Categories: tuifan
 * Description: Terminal 404 panel: process exit code, stderr output and recovery links.
 * Keywords: 404, not found, error
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
	<p class="tuifan-breadcrumb__trail">: <b>~/unknown</b></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"tuifan-error","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-error">
	<!-- wp:paragraph {"className":"tuifan-eyebrow"} -->
	<p class="tuifan-eyebrow"><?php esc_html_e( 'Process exited with code 404', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph {"className":"tuifan-error__number"} -->
	<p class="tuifan-error__number" aria-hidden="true">[404]</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading"><?php esc_html_e( 'This path doesn&#8217;t exist.', 'tuifan' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"tuifan-lede"} -->
	<p class="tuifan-lede"><?php esc_html_e( 'The page may have moved, or the address took a wrong turn. Let&#8217;s get you back to something useful.', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:group {"className":"tuifan-terminal","layout":{"type":"default"}} -->
	<div class="wp-block-group tuifan-terminal">
		<!-- wp:group {"className":"tuifan-terminal__bar","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
		<div class="wp-block-group tuifan-terminal__bar">
			<!-- wp:paragraph -->
			<p>stderr</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p>ENOENT</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:preformatted -->
		<pre class="wp-block-preformatted"><span class="tuifan-terminal__muted">$ open requested-page</span>
error: no such file or directory
hint: return home or search the archive</pre>
		<!-- /wp:preformatted -->
	</div>
	<!-- /wp:group -->

	<!-- wp:buttons {"className":"tuifan-error__actions"} -->
	<div class="wp-block-buttons tuifan-error__actions">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'cd ~', 'tuifan' ); ?></a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/?s=&post_type=post' ) ); ?>"><?php esc_html_e( 'Search the archive', 'tuifan' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
