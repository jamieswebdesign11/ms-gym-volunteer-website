<!--/**
* Template Name: Events Page
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
    $eventsHeading = get_field('events_heading');
    if(have_rows('upcoming_events')): ?>
    <div class="services">
        <?php echo $eventsHeading ? '<h2>'. $eventsHeading .'</h2>' : ''; ?>
        <?php while(have_rows('upcoming_events')): the_row(); 
        $heading = get_sub_field('heading');
        $content = get_sub_field('content');
        $image = get_sub_field('image');
        ?>
        <div class="service">
            <div class="service-inner flex-display-align">
                <div class="service-content">
                    <?php echo $heading ? '<h3>'. $heading .'</h3>' : ''; ?>
                    <?php echo $content ? $content : ''; ?>
                </div>
                <div class="service-image">
                    <?php echo $image ? '<img class="img-responsive" src="'. $image['url'] .'" title="'. $image['title'] .'" alt="'. $image['alt'] .'">' : ''; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php endwhile; endif; get_footer(); ?>
