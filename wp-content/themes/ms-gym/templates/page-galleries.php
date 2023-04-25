<!--/**
* Template Name: Galleries Page
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
    if(have_rows('galleries')): 
        echo '<div class="galleries">';
            while(have_rows('galleries')): the_row(); 
            $galleryHeading = get_sub_field('gallery_heading');
            $galleryContent = get_sub_field('gallery_content'); 
                echo '<div class="gallery-container">';
                    echo $galleryHeading ? '<h2>'. $galleryHeading .'</h2>' : '';
                    echo $galleryContent ? '<div class="gallery-content">'. $galleryContent .'</div>' : '';
                    if(have_rows('gallery_items')):
                        echo '<div class="light-gallery">';
                            while(have_rows('gallery_items')): the_row();
                            $mediaType = get_sub_field('media_type');
                            $image = get_sub_field('image');
                            $videoURL = get_sub_field('video_url');
                            $title = get_sub_field('title');
                            $description = get_sub_field('description');
                                    if($mediaType == true) {
                                        echo '<a class="gallery-video" data-src="'. $videoURL .'" data-sub-html=".caption">';
                                            echo '<img />';
                                            echo '<div class="caption"><h4>'. $title .'</h4>'. $description .'</div>';
                                        echo '</a>';
                                    } else {
                                        echo '<div data-sub-html=".caption" data-src="'. $image['url'] .'">';
                                            echo '<a href="#"><img src="'. $image['url'] .'"></a>';
                                            echo '<div class="caption"><h4>'. $title .'</h4>'. $description .'</div>';
                                        echo '</div>';
                                    }
                            endwhile;
                        echo '</div>';
                    endif;    
                echo '</div>';
            endwhile;
        echo '</div>';
    endif;
    ?>
</div>

<?php endwhile; endif; get_footer(); ?>
