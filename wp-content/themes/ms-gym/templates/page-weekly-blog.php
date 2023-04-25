<!--/**
* Template Name: Weekly Blog
*/-->
<?php get_header(); if(have_posts()): while(have_posts()): the_post(); 
$blogCategory = get_field('blog_category'); ?>

<?php $mainHeading = get_field('main_heading'); ?>
<div id="interior-banner"> 
    <div class="interior-banner-inner">
        <?php echo $mainHeading ? '<h1>'. $mainHeading .'</h1>' : ''; ?>
    </div>
</div>
<div class="page-content">
    <?php $the_query = new WP_Query(array('posts_per_page' => 5, 'paged' => get_query_var('paged'), 'category_name' => $blogCategory )); $count = $the_query->found_posts; ?>
    <?php while($the_query->have_posts()) : $the_query->the_post(); 
    $contentImage = get_field('featured_image'); $title = $post->post_title; $content = get_field('main_content');
    $excerpt = getTextExcerpt($content, $post->ID, false);
    ?>
    <div class="blog-page-box">
        <?php 
        echo $contentImage ? '<div class="main-blog-img"><img class="img-responsive center-block" src="'. $contentImage['url'] .'" title="'. $contentImage['title'] .'" alt="'. $contentImage['alt'] .'"></div>' : ''; 
        echo $title ? '<h2>'. $title .'</h3>' : '';
        echo $excerpt ? '<p>'. $excerpt .'</p>' : ''; 
        echo '<a class="btn" href="'. get_permalink($post->ID) .'" title="Read More">Read More</a>';
        ?>
    </div>
    <?php endwhile; ?>
    <?php if($count>5): ?>
    <nav class="pagination">
        <?php pagination_bar(); ?>
    </nav>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
</div>

<?php endwhile; endif; get_footer(); ?>


