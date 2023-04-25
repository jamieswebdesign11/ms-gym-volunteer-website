<!--/**
* Template Name: Profile Blog
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
    <?php $the_query = new WP_Query(array('posts_per_page' => 10, 'paged' => get_query_var('paged'), 'category_name' => $blogCategory )); $count = $the_query->found_posts; ?>
    <div class="profile-page">
        <?php while($the_query->have_posts()) : $the_query->the_post(); 
        $contentImage = get_field('featured_image'); $title = $post->post_title; $content = get_field('main_content');
        $excerpt = getTextExcerpt($content, $post->ID, false);
        ?>
        <div class="profile-page-box">
            <?php 
            echo $contentImage ? '<div class="main-blog-img"><img class="img-responsive center-block" src="'. $contentImage['url'] .'" title="'. $contentImage['title'] .'" alt="'. $contentImage['alt'] .'"></div>' : ''; 
            echo '<div class="blog-content-box">';
            echo $title ? '<h2>'. $title .'</h3>' : '';
            echo $excerpt ? '<p>'. $excerpt .'</p>' : ''; 
            echo '<a class="btn" href="'. get_permalink($post->ID) .'" title="Read More">Read More</a>';
            echo '</div>';
            ?>
        </div>
    <?php endwhile; ?>
    </div>
    <?php if($count>10): ?>
    <nav class="pagination">
        <?php pagination_bar(); ?>
    </nav>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
    <?php if(have_rows('sections')): ?>
    <div id="profile-blog-extras" class="profile-blog-extras">
        <div class="profile-blog-extras-inner">
            <?php while(have_rows('sections')): the_row();
                if(get_row_layout() == 'heading'): 
                    $heading = get_sub_field('heading');
                    echo $heading ? '<h2>'. $heading .'</h2>' : '';
                elseif(get_row_layout() == 'content'):
                    $content = get_sub_field('content');
                    echo $content ? '<div class="profile-blog-extras-content-box">'. $content .'</div>' : '';
                elseif(get_row_layout() == 'image_grid'):
                    if(have_rows('image_grid_repeater')):
                        echo '<div class="profile-blog-extras-image-grid">';
                        while(have_rows('image_grid_repeater')): the_row();
                            $image = get_sub_field('image');
                            echo $image ? '<div class="image-grid-box"><img class="img-responsive center-block" src="'. $image['url'] .'" title="'. $image['title'] .'" alt="'. $image['alt'] .'"></div>' : '';
                        endwhile;
                        echo '</div>';
                    endif;
                endif; 
            endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php endwhile; endif; get_footer(); ?>


