<?php
/*** The template for displaying 404 pages (Not Found).*/ ?>
<?php get_header(); ?>
<main>
	<div class="container main-inner">
		<div class="row">
			<article id="post-0" class="post error404 no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'layout11' ); ?></h1>
				</header>
                <div class="entry-content">
					<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. <a href="/" title="Back To Home" alt="Back To Homepage Link">Back To Home</a>', 'layout11' ); ?></p>
				</div>
			</article>
		</div>
	</div>
</main>
<?php get_footer(); ?>