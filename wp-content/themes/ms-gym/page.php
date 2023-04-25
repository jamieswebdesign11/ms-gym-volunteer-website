<?php
/**
 * The Template for displaying all default template pages
 *
 * @package WordPress
 * @subpackage layout11
 * @since layout11 1.0
 */ ?>
<?php get_header('inner'); ?>
<?php 
	if ( have_posts() ) {
		while ( have_posts() ) {
		the_post(); ?>
		   
	<main>
		<div class="container main-inner">
            <div class="row">
                <section class="col-xs-12 col-sm-12 col-md-12 col-lg-12 about-us-content">					
                    <?php the_content(); ?>
                </section>
            </div>
		</div>
	</main>
<?php	} // end while
	} // end if
?>
	<?php get_footer('inner'); ?>