<!--/**
* Template Name: Home Page
*/-->
<?php get_header(); if(have_posts()): while(have_posts()): the_post(); ?>

<div id="banner" data-height="False" data-parallax="True" data-parallax-value="on"> 
    <div class="banner-inner"> 
        <?php if(have_rows('slider')): ?> 
        <div id="carousel-slider" class="carousel fade" data-ride="carousel"> 
            <div class="carousel-inner"> 
                <?php $i=0; while(have_rows('slider')): the_row(); $i++; 
                $image = get_sub_field('image'); ?> 
                <?php echo $image ? '<div class="'. ($i==1 ? 'item active' : 'item') .'"><div class="slider-img-container"><img class="slider-img" src="'. $image['url'] .'" alt="'. $image['alt'] .'" title="'. $image['title'] .'"></div></div>' : ''; ?> 
                <?php endwhile; ?> 
            </div> 
        </div> 
        <?php endif; ?> 
    </div>
</div>
<div class="page-content">
    <?php if(have_rows('ctas')): ?>
    <div id="CTAs">
        <div class="CTAs-inner flex-display">
            <?php while(have_rows('ctas')): the_row(); 
            $icon = get_sub_field('icon');
            $text = get_sub_field('text');
            $link = get_sub_field('link');
            ?>
            <div class="CTA">
                <?php
                echo $link ? '<a href="'. $link['url'] .'" title="'. $link['title'] .'" alt="'. $link['title'] .'"'. ($link['target'] ? ' target="_blank"' : '') .'>' : '';
                echo $icon ? $icon : '';
                echo $text ? '<span class="cta-text">'. $text .'</span>' : '';
                echo $link ? '</a>' : ''; 
                ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    <div id="main">
        <div class="main-inner">
            <?php 
            $mainHeading = get_field('main_heading'); 
            $mainContent = get_field('main_content');
            echo $mainHeading ? '<h1>'. $mainHeading .'</h1>' : '';
            echo $mainContent ? $mainContent : ''; 
            ?>
        </div>
    </div>
    <div id="quote">
        <?php 
        $quote = get_field('quote'); 
        $quoteImg = get_field('quote_image');
        echo $quote ? '<div class="quote-inner" style="background-image: url('. $quoteImg['url'] .')">'. $quote .'</div>' : ''; 
        ?>
    </div>
    <?php $testimonialsHeading = get_field('testimonials_heading'); 
    $the_query = new WP_Query(array('category_name' => 'testimonial' )); ?>
    <div id="testimonials">
        <?php echo $testimonialsHeading ? '<h2>'. $testimonialsHeading .'</h2>' : ''; ?>
        <div class="testimonials-inner">
            <?php while($the_query->have_posts()) : $the_query->the_post();
            $title = $post->post_title; 
            $content = get_field('main_content');
            $excerpt = getTextExcerpt($content, $post->ID, false);
            ?>
            <div class="testimonial">
                <?php echo $content ? '<div class="testimonial-content">'. $excerpt .'</div>' : ''; ?>
                <?php echo $title ? '<span class="testimonial-author">'. $title .'</span>' : ''; ?>
                <?php echo '<a class="testimonial-button btn" href="'. get_permalink($post->ID) .'" title="Read More">Read More</a>'; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php $joinFormShortcode = get_field('join_form_shortcode', 'options'); ?>
<div class="join-form-popup">
    <div class="join-form-popup-inner">
        <?php echo $joinFormShortcode ? $joinFormShortcode : ''; ?>
        <a class="join-form-popup--close"></a>
    </div>
</div>

<?php endwhile; endif; get_footer(); ?>
