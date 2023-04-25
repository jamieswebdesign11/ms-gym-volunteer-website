<?php
/**
 * Created by PhpStorm.
 * User: lusinda
 * Date: 7/20/15
 * Time: 7:23 PM
 */


if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

get_header(); ?>
	<div id="ecwd-events-pg-template">
		<?php ecwd_events_before_html(); ?>
		<?php ecwd_get_view(); ?>
		<?php ecwd_events_after_html(); ?>
	</div> <!-- #ecwd-events-pg-template -->
<?php get_footer(); ?>
