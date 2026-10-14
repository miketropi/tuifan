<?php
/**
 * Title: Tuifan about
 * Slug: tuifan/about
 * Categories: tuifan
 * Description: Terminal README-style about page: numbered sections, editorial note and contact link.
 * Keywords: about, readme, introduction
 * Viewport Width: 1200
 *
 * @package Tuifan
 */

?>
<!-- wp:group {"className":"tuifan-page-heading tuifan-page-heading--inline","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-page-heading tuifan-page-heading--inline">
	<!-- wp:paragraph {"className":"tuifan-eyebrow"} -->
	<p class="tuifan-eyebrow">README.md</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading"><?php esc_html_e( 'AI agents. Human judgment.', 'tuifan' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"tuifan-lede"} -->
	<p class="tuifan-lede"><?php esc_html_e( 'Practical understanding for people who want to use AI well.', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">
	<?php
	printf(
		/* translators: %s: site name. */
		esc_html__( '01 / What is %s?', 'tuifan' ),
		esc_html( get_bloginfo( 'name' ) )
	);
	?>
</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'This is a practical reading space for people learning to work with AI agents. The focus is on real tasks: preparing context, choosing permissions, reviewing answers, and knowing when to stay in the loop.', 'tuifan' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'You do not need to be an AI engineer to use these guides. Start with a task you understand, try a reversible workflow, and judge the result against your own sources.', 'tuifan' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php esc_html_e( '02 / What you&#8217;ll find here', 'tuifan' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><strong><?php esc_html_e( 'Getting started:', 'tuifan' ); ?></strong> <?php esc_html_e( 'what agents do, where they help, and how to write a useful brief.', 'tuifan' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e( 'Safety:', 'tuifan' ); ?></strong> <?php esc_html_e( 'permissions, privacy, approval gates, and recovery.', 'tuifan' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong><?php esc_html_e( 'Workflows:', 'tuifan' ); ?></strong> <?php esc_html_e( 'focused context, clean handoffs, and evidence-based evaluation.', 'tuifan' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php esc_html_e( '03 / Why a terminal?', 'tuifan' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'A terminal puts the content first. Clear structure, readable text, and a direct path to what you need. This blog brings that spirit to the web without asking you to learn a command language.', 'tuifan' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"tuifan-note","layout":{"type":"default"}} -->
<div class="wp-block-group tuifan-note">
	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'Editorial approach: explain the trade-offs, distinguish examples from evidence, and link to primary guidance where useful. These articles do not promise error-free automation or replace security advice.', 'tuifan' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"tuifan-text-link"} -->
<p class="tuifan-text-link"><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Start a conversation', 'tuifan' ); ?></a></p>
<!-- /wp:paragraph -->
