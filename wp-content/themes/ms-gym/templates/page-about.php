<!--/**
* Template Name: About Page
*/-->
<?php get_header(); if(have_posts()): while(have_posts()): the_post(); ?>

<?php $mainHeading = get_field('main_heading'); ?>
<div id="interior-banner"> 
    <div class="interior-banner-inner">
        <?php echo $mainHeading ? '<h1>'. $mainHeading .'</h1>' : ''; ?>
    </div>
</div>
<div class="page-content">
    <?php
    $content = get_field('content');
    echo $content ? '<div class="about-content">'. $content .'</div>' : ''; 
    ?>
</div>

<?php endwhile; endif; get_footer(); ?>
