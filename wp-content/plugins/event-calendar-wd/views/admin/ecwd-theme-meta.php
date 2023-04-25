<?php
/**
 * Display for Calendar post metas
 */
global $post;

$post_id = $post->ID;

// Load up all post meta data
$theme_params = json_decode(get_post_meta($post->ID, 'ecwd_theme_params', true), true);
if(!$theme_params){
    $theme_params = $default_theme;
}

if(empty($theme_params['ecwd_cal_font_style'])){
  $theme_params['ecwd_cal_font_style'] = "italic";
}

$theme_reset =  get_post_meta($post->ID, 'ecwd_theme_name', true);

?>

<div id="ecwd-display-options-wrap">
    <div class="ecwd-meta-control">
        <?php if($theme_reset){ ?>
            <div>
                <a href="post.php?post=<?php echo $post_id;?>&action=edit&theme=reset"><?php _e('Reset Theme', 'event-calendar-wd')?></a>
            </div>
        <?php } ?>
        <div id="ecwd-tabs">

            <ul class="ecwd-tabs">
                <li><a href="#general"><?php _e('General', 'event-calendar-wd');?></a></li>
                <li><a href="#header"><?php _e('Header', 'event-calendar-wd');?></a></li>
                <li><a href="#views"><?php _e('Views', 'event-calendar-wd');?></a></li>
                <li><a href="#filter"><?php _e('Filter', 'event-calendar-wd');?></a></li>
                <li><a href="#weeks"><?php _e('Week days', 'event-calendar-wd');?></a></li>
                <li><a href="#days"><?php _e('Days', 'event-calendar-wd');?></a></li>
                <li><a href="#events"><?php _e('Events', 'event-calendar-wd');?></a></li>
                <li><a href="#pagination"><?php _e('Pagination', 'event-calendar-wd');?></a></li>
            </ul>
            <div id="general">
                <div class="ecwd-tab-inner">
                    <p class="description">
                        <?php _e('In this section you can make changes for the calendar overall display.', 'event-calendar-wd'); ?>
                    </p>
                    <div>
                        <label><?php _e('Calendar width', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_width" value="<?php echo $theme_params['ecwd_width']; ?>" />
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Calendar border color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_cal_border_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_cal_border_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_cal_border_color']; ?>"/>
                    </div>
                    <div>
                        <label> <?php _e('Border width', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_cal_border_width" value="<?php echo $theme_params['ecwd_cal_border_width']; ?>" /> px
                    </div>
                    <div>
                        <label> <?php _e('Border radius', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_cal_border_radius" value="<?php echo $theme_params['ecwd_cal_border_radius']; ?>" /> px
                    </div>
                </div>
            </div>
            <div id="header">
                <div class="ecwd-tab-inner">
                    <p class="description">
                        <?php _e('In this section you can make the changes within the calendar header (current, next and previous month display, navigation and overall color scheme).', 'event-calendar-wd'); ?>
                    </p>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Header color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_cal_header_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_cal_header_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_cal_header_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Header border color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_cal_header_border_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_cal_header_border_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_cal_header_border_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Current year color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_current_year_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_current_year_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_current_year_color']; ?>"/>
                    </div>
                    <div>
                        <label> <?php _e('Current year font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_current_year_font_size" value="<?php echo $theme_params['ecwd_current_year_font_size']; ?>" /> px
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Current month color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_current_month_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_current_month_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_current_month_color']; ?>"/>
                    </div>
                    <div>
                        <label> <?php _e('Current month font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_current_month_font_size" value="<?php echo $theme_params['ecwd_current_month_font_size']; ?>" /> px
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Next, Prev links color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_next_prev_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_next_prev_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_next_prev_color']; ?>"/>
                    </div>
                    <div>
                        <label> <?php _e('Next, Prev links font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_next_prev_font_size" value="<?php echo $theme_params['ecwd_next_prev_font_size']; ?>" /> px
                    </div>
                </div>
            </div>
            <div id="views">
                <div class="ecwd-tab-inner">
                    <p class="description">
                        <?php _e('In this section you can make changes within the view tabs below the main header.', 'event-calendar-wd'); ?>
                    </p>
                    <div class="with_ecwd_color">
                        <label> <?php _e('View tabs background color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_view_tabs_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_view_tabs_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_view_tabs_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('View tabs border color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_view_tabs_border_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_view_tabs_border_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_view_tabs_border_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Current tab color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_view_tabs_current_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_view_tabs_current_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_view_tabs_current_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('View tabs text color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_view_tabs_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_view_tabs_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_view_tabs_text_color']; ?>"/>
                    </div>
                    <div>
                        <label> <?php _e('View tabs text font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_view_tabs_font_size" value="<?php echo $theme_params['ecwd_view_tabs_font_size']; ?>" /> px
                    </div>
                    <div class="with_ecwd_color">
                        <label class="with_ecwd_color"> <?php _e('Current tab text color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_view_tabs_current_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_view_tabs_current_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_view_tabs_current_text_color']; ?>"/>
                    </div>
                </div>

                <div class="ecwd-tab-inner">
                    <div class="with_ecwd_color">
                        <label> <?php _e('Search background color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_search_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_search_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_search_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Search icon color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_search_icon_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_search_icon_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_search_icon_color']; ?>"/>
                    </div>
                </div>
            </div>
            <div id="filter">
                <div class="ecwd-tab-inner">
                    <p class="description">
                        <?php _e('In this section you can make color changes within the filtering drop-down box.', 'event-calendar-wd'); ?>
                    </p>
                    <div><h3><?php _e('Header', 'event-calendar-wd');?></h3></div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters header background color (top)', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_header_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_header_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_header_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters header background color (left)', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_header_left_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_header_left_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_header_left_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters header text color (top)', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_header_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_header_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_header_text_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters header text color (left)', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_header_left_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_header_left_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_header_left_text_color']; ?>"/>
                    </div>

                    <div><h3><?php _e('Sections', 'event-calendar-wd');?></h3></div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters section background color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters section border color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_border_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_border_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_border_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters sction arrow color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_arrow_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_arrow_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_arrow_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters section text color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_text_color']; ?>"/>
                    </div>
                    <div>
                        <label> <?php _e('Filters section text font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_filter_font_size" value="<?php echo $theme_params['ecwd_filter_font_size']; ?>" /> px
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters reset text color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_reset_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_reset_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_reset_text_color']; ?>"/>
                    </div>
                    <div>
                        <label> <?php _e('Filters reset text font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_filter_reset_font_size" value="<?php echo $theme_params['ecwd_filter_reset_font_size']; ?>" /> px
                    </div>


                    <div><h3><?php _e('Items', 'event-calendar-wd');?></h3></div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters background color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_item_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_item_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_item_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters border color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_item_border_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_item_border_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_item_border_color']; ?>"/>
                    </div>

                    <div class="with_ecwd_color">
                        <label> <?php _e('Filters text color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_filter_item_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_filter_item_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_filter_item_text_color']; ?>"/>
                    </div>
                    <div>
                        <label> <?php _e('Filters text font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_filter_item_font_size" value="<?php echo $theme_params['ecwd_filter_item_font_size']; ?>" /> px
                    </div>
                </div>
            </div>
            <div id="weeks">
                <div class="ecwd-tab-inner">
                    <p class="description">
                        <?php _e('In this section you can make color and font size changes within the tabs displaying the titles of the week days.', 'event-calendar-wd'); ?>
                    </p>
                    <div class="with_ecwd_color">
                        <label> <?php _e('Week days background color', 'event-calendar-wd');?> </label>
                        <input type="text" name="ecwd_week_days_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_week_days_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_week_days_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Week days border color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_week_days_border_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_week_days_border_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_week_days_border_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Week days text color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_week_days_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_week_days_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_week_days_text_color']; ?>"/>
                    </div>
                    <div>
                        <label><?php _e('Week days text font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_week_days_font_size" value="<?php echo $theme_params['ecwd_week_days_font_size']; ?>" /> px
                    </div>
                </div>
            </div>
            <div id="days">
                <div class="ecwd-tab-inner">
                    <p class="description">
                        <?php _e('In this section you can make changes within the cells displaying the days of the week/month.', 'event-calendar-wd'); ?>
                    </p>
                    <div class="with_ecwd_color">
                        <label><?php _e('Cell background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_cell_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_cell_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_cell_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Cell border color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_cell_border_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_cell_border_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_cell_border_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Weekend cell background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_cell_weekend_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_cell_weekend_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_cell_weekend_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Prev/Next month cell background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_cell_prev_next_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_cell_prev_next_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_cell_prev_next_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Day number background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_day_number_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_day_number_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_day_number_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Day text color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_day_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_day_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_day_text_color']; ?>"/>
                    </div>
                    <div>
                        <label><?php _e('Day text font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_day_font_size" value="<?php echo $theme_params['ecwd_day_font_size']; ?>" /> px
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Current day cell background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_current_day_cell_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_current_day_cell_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_current_day_cell_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Current day number background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_current_day_number_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_current_day_number_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_current_day_number_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Current day text color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_current_day_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_current_day_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_current_day_text_color']; ?>"/>
                    </div>
                </div>
            </div>
            <div id="events">
                <div class="ecwd-tab-inner">
                    <p class="description">
                        <?php _e('In this section you can make changes within the cells displaying events in various views, as well as the event detail display tooltip.', 'event-calendar-wd'); ?>
                    </p>
                    <div class="with_ecwd_color">
                        <label><?php _e('Event title color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_title_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_event_title_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_event_title_color']; ?>"/>
                    </div>
                    <div>
                        <label><?php _e('Event title font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_title_font_size" value="<?php echo $theme_params['ecwd_event_title_font_size']; ?>" /> px
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Event details background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_details_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_event_details_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_event_details_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Event details border color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_details_border_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_event_details_border_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_event_details_border_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Event details text color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_details_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_event_details_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_event_details_text_color']; ?>"/>
                    </div>
<!--                    <div>-->
<!--                        <label>--><?php //_e('Event details text font size', 'ecwd');?><!--</label>-->
<!--                        <input type="text" name="ecwd_event_details_font_size" value="--><?php //echo $ecwd_event_details_font_size; ?><!--" /> px-->
<!--                    </div>-->
                </div>
                <div class="ecwd-tab-inner">
                    <div class="with_ecwd_color">
                        <label><?php _e('Event date background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_list_view_date_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_event_list_view_date_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_event_list_view_date_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Event date text color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_list_view_date_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_event_list_view_date_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_event_list_view_date_text_color']; ?>"/>
                    </div>
                    <div>
                        <label><?php _e('Event date font size', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_list_view_date_font_size" value="<?php echo $theme_params['ecwd_event_list_view_date_font_size']; ?>" /> px
                    </div>

                    <div class="with_ecwd_color">
                        <label><?php _e('Posterboard view date background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_posterboard_view_date_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_event_posterboard_view_date_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_event_posterboard_view_date_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Posterboard view date text color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_event_posterboard_view_date_text_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_event_posterboard_view_date_text_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_event_posterboard_view_date_text_color']; ?>"/>
                    </div>
                    <div>
                      <label><?php _e('Font Style', 'event-calendar-wd');?></label>
                      <select style = "width: 150px" name="ecwd_cal_font_style" class="style-selector"">
                      <option class="normal" value="normal" style="font-style: normal" <?php selected($theme_params['ecwd_cal_font_style'], "normal", true); ?>>normal</option>
                      <option class="italic" value="italic" style="font-style: italic" <?php selected($theme_params['ecwd_cal_font_style'], "italic", true); ?>>italic</option>
                      <option class="oblique" value="oblique" style="font-style: oblique" <?php selected($theme_params['ecwd_cal_font_style'], "oblique", true); ?>>oblique</option>
                      <option class="inherit" value="inherit" style="font-style: inherit" <?php selected($theme_params['ecwd_cal_font_style'], "inherit", true); ?>>inherit</option>
                      <option class="initial" value="initial" style="font-style: initial" <?php selected($theme_params['ecwd_cal_font_style'], "initial", true); ?>>initial</option>
                      <option class="unset" value="unset" style="font-style: unset" <?php selected($theme_params['ecwd_cal_font_style'], "unset", true); ?>>unset</option>
                      </select>
                    </div>
                </div>
            </div>
            <div id="pagination">
                <div class="ecwd-tab-inner">
                    <p class="description">
                        <?php _e('In this section you can make changes for the pagination', 'event-calendar-wd'); ?>
                    </p>
                    <div class="with_ecwd_color">
                        <label><?php _e('Page numbers background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_page_numbers_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_page_numbers_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_page_numbers_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Current page background color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_current_page_bg_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_current_page_bg_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_current_page_bg_color']; ?>"/>
                    </div>
                    <div class="with_ecwd_color">
                        <label><?php _e('Page number color', 'event-calendar-wd');?></label>
                        <input type="text" name="ecwd_page_number_color" class="ecwd_colour" value="<?php echo $theme_params['ecwd_page_number_color']; ?>"  style="background-color: <?php echo $theme_params['ecwd_page_number_color']; ?>"/>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

