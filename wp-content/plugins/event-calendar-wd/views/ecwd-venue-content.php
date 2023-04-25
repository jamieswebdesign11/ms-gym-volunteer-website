<?php
/**
 * Display for Venue Custom Post Types
 */
$date_format = 'Y-m-d';
$time_format = 'H:i';

if (isset($ecwd_options['date_format']) && $ecwd_options['date_format'] != '') {
    $date_format = $ecwd_options['date_format'];
}
if (isset($ecwd_options['time_format']) && $ecwd_options['time_format'] != '') {
    $time_format = $ecwd_options['time_format'];
}
$time_format .= (isset($ecwd_options['time_type']) ? ' ' . $ecwd_options['time_type'] : '');
if (isset($ecwd_options['time_type']) && $ecwd_options['time_type'] != '') {
    $time_format = str_replace('H', 'g', $time_format);
    $time_format = str_replace('h', 'g', $time_format);
}
$ecwd_social_icons = ($ecwd_options['social_icons'] != '') ? $ecwd_options['social_icons'] : false;

$post_id = $post->ID;
$venue_url = get_permalink($post_id);
$meta = get_post_meta($post_id);
$events = array();

// Load up all post meta data
$ecwd_venue_location = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX . '_venue_location', true);
$ecwd_venue_latlong = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX . '_venue_lat_long', true);
$ecwd_venue_zoom = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX . '_map_zoom', true);
if (!$ecwd_venue_zoom) {
    $ecwd_venue_zoom  = (isset($ecwd_options['gmap_zoom'])) ? $ecwd_options['gmap_zoom'] : 17;
}

$today = ECWD::ecwd_date('Y-m-d', time());

$args = array(
    'numberposts' => - 1,
    'post_type' => ECWD_PLUGIN_PREFIX . '_event',
    'meta_query' => array(
        array(
            'key' => ECWD_PLUGIN_PREFIX . '_event_venue',
            'value' => $post->ID
        )
    ),
    'meta_key' => ECWD_PLUGIN_PREFIX . '_event_date_from',
    'orderby' => 'meta_value',
    'order' => 'ASC'
);
$ecwd_events = get_posts($args);

foreach ($ecwd_events as $ecwd_event) {

    $term_metas = '';
    $categories = get_the_terms($ecwd_event->ID, ECWD_PLUGIN_PREFIX . '_event_category');
    if (is_array($categories)) {
        foreach ($categories as $category) {
            $term_metas = get_option("ecwd_event_category_$category->term_id");
            $term_metas['id'] = $category->term_id;
            $term_metas['name'] = $category->name;
            $term_metas['slug'] = $category->slug;
        }
    }
    $ecwd_event_metas = get_post_meta($ecwd_event->ID, '', true);
    $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_url'] = array(0 => '');
    if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'])) {
        $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'] = array(0 => '');
    }
    if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'])) {
        $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'] = array(0 => '');
    }
    if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'])) {
        $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'] = array(0 => '');
    }
    if (!isset($ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'])) {
        $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'] = array(0 => '');
    }


    $permalink = get_permalink($ecwd_event->ID);


    $events[$ecwd_event->ID] = new ECWD_Event($ecwd_event->ID, 0, $ecwd_event->post_title, $ecwd_event->post_content, $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_location'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_from'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_date_to'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_event_url'][0], $ecwd_event_metas[ECWD_PLUGIN_PREFIX . '_lat_long'][0], $permalink, $ecwd_event, $term_metas, $ecwd_event_metas);
}
//TODO: add options for date range
$d = new ECWD_Display(0, '', '', $today);
$max_date = ECWD::ecwd_date('Y-m-d', strtotime(( ECWD::ecwd_date("Y-m-t", ( strtotime(ECWD::ecwd_date('Y-m-d',time())))) . " +" . ( ( 12 ) ) . " month")));
$events = $d->get_event_days($events, 1, ECWD::ecwd_date('Y-m-d'), $max_date);
$events = $d->events_unique($events);


$venue_meta_template = '<div class="%s"><span>%s:</span><span>%s</span></div>';
$venue_meta_link_template = '<div class="%s"><span>%s:</span><a href="%s">%s</a></div>';

$ecwd_venue_phone = get_post_meta($post_id, 'ecwd_venue_meta_phone', true);
$ecwd_venue_website = get_post_meta($post_id, 'ecwd_venue_meta_website', true);
$ecwd_venue_website = ECWD::add_http($ecwd_venue_website);
$ecwd_venue_show_map = get_post_meta($post_id, 'ecwd_venue_show_map', true);



?>


<div class="ecwd-venue">
    <?php
    if (isset($_GET['venue']) && intval($_GET['venue']) == 1) {
        echo'<a id="ecwd_back_link" href="#">' . __('Back', 'event-calendar-wd') . '</a>';
        echo '<h3>' . $post->post_title . '</h3>';
    }

    if (!empty($ecwd_venue_location)) {
        echo sprintf($venue_meta_template, "ecwd_venue_location", __('Location', 'event-calendar-wd'), esc_html($ecwd_venue_location));
    }

    if (!empty($ecwd_venue_phone)) {
        echo sprintf($venue_meta_template, "ecwd_venue_phone", __('Phone', 'event-calendar-wd'), esc_html($ecwd_venue_phone));
    }

    if (!empty($ecwd_venue_website)) {
        echo sprintf($venue_meta_link_template, "ecwd_venue_website", __('Website', 'event-calendar-wd'), esc_url($ecwd_venue_website), esc_html($ecwd_venue_website));
    }

    echo '<div class="ecwd_venue_description">' . wpautop($post->post_content) . '</div>';
    ?>
    <div class="ecwd-venue-ext">
        <?php do_action('ecwd_view_ext') ?>
    </div>
    <?php if ($ecwd_social_icons) { ?>
        <div class="ecwd-social">
            <span class="share-links">
                <a href="https://twitter.com/intent/tweet?text=<?php echo get_permalink($post_id) ?>" class="ecwd-twitter"
                   target="_blank" data-original-title="Tweet It">
                    <span class="visuallyhidden">Twitter</span></a>

                <a href="https://www.facebook.com/sharer.php?u=<?php echo get_permalink($post_id) ?>" class="ecwd-facebook"
                   target="_blank" data-original-title="Share on Facebook">
                    <span class="visuallyhidden">Facebook</span></a>

            </span>
        </div>
    <?php } ?>


    <?php
    if ($ecwd_venue_latlong && $ecwd_venue_show_map === '1') {
        $url_for_google_map = '';
        if ($ecwd_venue_location != '') {
            $url_for_google_map .= 'https://www.google.com/maps/place/';
            $url_for_google_map .= urlencode($ecwd_venue_location) . '/';
            $url_for_google_map .= '@' . $ecwd_venue_latlong;
        }
        $map_events = array();
        $map_events[0]['latlong'] = explode(',', esc_html($ecwd_venue_latlong));
        $map_events[0]['zoom'] = intval($ecwd_venue_zoom);
        $map_events[0]['infow'] = '<div class="ecwd_map_venue">';
        $map_events[0]['infow'] .= '<div class="name">' . $post->post_title . '</div>';
        $map_events[0]['infow'] .= '<div class="location">' . esc_html($ecwd_venue_location) . '</div>';
        $map_events[0]['infow'] .= '</div>';
        $map_events[0]['google_map_url'] = esc_url($url_for_google_map);
        $markers = json_encode($map_events);
        ?>
        <div class="ecwd-show-map">
            <div id="ecwd_map_div" class="ecwd_map_div">
            </div>
            <textarea id="ecwd_markers" class="hidden" style="display: none;"><?php echo $markers; ?></textarea>
        </div>
        <?php
    }
    do_action('ecwd_show_related_events', $events);
    ?>    

</div>
<script id="ecwd_script_handler" type="text/javascript">
    if (typeof ecwd_js_init_call == "object") {
        ecwd_js_init_call = new ecwd_js_init();
        ecwd_js_init_call.showMap();
    }
</script>
