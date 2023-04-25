<!--/**
* Template Name: Calendar Page
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
    echo $content ? $content : ''; 
    ?>
</div>

<?php endwhile; endif; get_footer(); ?>
