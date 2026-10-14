<?php
/**
 * Title: Tuifan home
 * Slug: tuifan/home
 * Categories: tuifan
 * Description: Terminal home shell: hero with the lensed-shadow signal visual, identity strip, latest entries and the live sidebar.
 * Keywords: home, hero, signal, entries
 * Viewport Width: 1400
 *
 * @package Tuifan
 */

?>
<!-- wp:group {"className":"tuifan-breadcrumb tuifan-breadcrumb--home","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group tuifan-breadcrumb tuifan-breadcrumb--home">
	<!-- wp:group {"className":"tuifan-breadcrumb__prompt","layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"baseline"}} -->
	<div class="wp-block-group tuifan-breadcrumb__prompt">
		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'guest@', 'tuifan' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:site-title {"level":0,"className":"tuifan-breadcrumb__site"} /-->
		<!-- wp:paragraph {"className":"tuifan-breadcrumb__trail"} -->
		<p class="tuifan-breadcrumb__trail">: <b>~</b></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph {"className":"tuifan-breadcrumb__aside"} -->
	<p class="tuifan-breadcrumb__aside"><?php esc_html_e( 'Independent notes / code & curiosity', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"tuifan-hero","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-hero">
	<!-- wp:group {"className":"tuifan-hero__copy","layout":{"type":"default"}} -->
	<div class="wp-block-group tuifan-hero__copy">
		<!-- wp:paragraph {"className":"tuifan-eyebrow"} -->
		<p class="tuifan-eyebrow"><?php esc_html_e( 'AI agents, explained', 'tuifan' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":1,"className":"tuifan-hero__title"} -->
		<h1 class="wp-block-heading tuifan-hero__title"><span><?php esc_html_e( 'less noise.', 'tuifan' ); ?></span><span class="tuifan-signal-word"><?php esc_html_e( 'more signal.', 'tuifan' ); ?></span></h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"tuifan-lede tuifan-hero__lede"} -->
		<p class="tuifan-lede tuifan-hero__lede"><?php esc_html_e( 'Put AI agents to work without giving up control. Clear guides to getting started, safer permissions, and results you can verify.', 'tuifan' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"tuifan-command-link-row"} -->
		<p class="tuifan-command-link-row"><a class="tuifan-command-link" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><span class="tuifan-command-link__prompt" aria-hidden="true">$</span> <?php esc_html_e( 'explore ./archive', 'tuifan' ); ?></a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:tuifan/signal-visual {"label":"Accretion field","enableMotion":true} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"tuifan-identity-strip","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group tuifan-identity-strip">
	<!-- wp:paragraph -->
	<p><b><?php esc_html_e( 'human-written ideas.', 'tuifan' ); ?></b> <?php esc_html_e( 'terminal-minded design.', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->
	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'development / terminal / workflow', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"tuifan-grid tuifan-grid--home","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-grid tuifan-grid--home">
	<!-- wp:tuifan/entry-list {"source":"latest","postsPerPage":2,"className":"tuifan-home-list"} /-->

	<!-- wp:template-part {"slug":"sidebar","theme":"tuifan","className":"tuifan-aside--home"} /-->
</div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"tuifan-text-link tuifan-view-all"} -->
<p class="tuifan-text-link tuifan-view-all"><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'View all entries', 'tuifan' ); ?></a></p>
<!-- /wp:paragraph -->
