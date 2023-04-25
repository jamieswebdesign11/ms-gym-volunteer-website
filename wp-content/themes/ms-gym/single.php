<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */ ?>
<?php get_header(); if(have_posts()): while(have_posts()): the_post(); 
$title = get_the_title(); $content = get_field('main_content'); $default_content = get_the_content();
?>

<div id="interior-banner"> 
    <div class="interior-banner-inner">
        <?php echo $title ? '<h1>'. $title .'</h1>' : ''; ?>
    </div>
</div>

<div class="page-content">
    <div class="blog-page">
        <?php echo $content ? $content : ''; ?>
        <?php echo $default_content ? $default_content : ''; ?>
    </div>
</div>
<?php endwhile; endif; get_footer(); ?>
