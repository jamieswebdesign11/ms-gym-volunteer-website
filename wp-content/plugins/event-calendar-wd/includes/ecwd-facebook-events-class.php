<?php

/**
 * Class ECWD_Facebook_Events
 */
class ECWD_Facebook_Events {

	public $id, $calendar_ids, $event_url, $date_format, $time_format, $multiple_day_events, $display_url, $search_query, $expand_recurring, $title, $event, $trasient_key;
	public $events = array();
	public $fevents;
	public $ecwd_facebook_page_id = null;
	// facebook Access token
	private $access_token = '';
	private $import = false;

	/**
	 * Class constructor
	 */
	public function __construct() {
	}

	public function get_facebook_events( $id = null, $page_id = null, $access_token=null ) {
// Set the ID
//		$access_token               = $this->get_access_token( $app_id, $secret_key );
		$ecwd_facebook_access_token = '';
		if ( $id ) {
			$this->calendar_id     = $id;
			$ecwd_facebook_page_id = get_post_meta( $id, ECWD_PLUGIN_PREFIX . '_facebook_page_id', true );
		} else {
			if ( $page_id && $access_token ) {

				$ecwd_facebook_page_id      = $page_id;
				$ecwd_facebook_access_token = $access_token;
				$this->import               = true;
			}
		}

		if(empty($access_token)){
		    if(!$id){
                $fb_import_data = get_option('ecwd_fb_import');
                if(isset($fb_import_data['access_token'])){
                    $access_token = $fb_import_data['access_token'];
                }
            }else{
                $access_token = get_post_meta($id, 'ecwd_facebook_access_token', true);
            }
		}

		if(empty($this->calendar_id)){
			$this->calendar_id = '';
		}

		if ( $ecwd_facebook_page_id ) {
			$this->ecwd_facebook_page_id = $ecwd_facebook_page_id;
			$this->trasient_key          = substr( ECWD_PLUGIN_PREFIX . '_calendar_' . $this->calendar_id.$ecwd_facebook_page_id, 0, 30 );
			$this->access_token          = $ecwd_facebook_access_token;


// Set up all other data based on the ID
			$this->setup_attributes();


// Now create the Event
			$this->compose_calendar_url($access_token);
		}

		return $this->events;
	}

	/**
	 * Set all of the event attributes from the post meta options
	 */
	private function setup_attributes() {
		$date_format = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_date_format', true );
		$time_format = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_time_format', true );

		$this->event_url           = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_event_url', true );
		$this->date_format         = ( ! empty( $date_format ) ? $date_format : get_option( 'date_format' ) );
		$this->time_format         = ( ! empty( $time_format ) ? $time_format : get_option( 'time_format' ) );
		$this->multiple_day_events = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_multi_day_events', true );
		$this->search_query        = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_search_query', true );
		$this->expand_recurring    = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_expand_recurring', true );
		$this->title               = get_the_title( $this->id );
	}

	/**
	 * Create the event URL
	 */
	private function compose_calendar_url($access_token) {
		$args = array();
		global $ecwd_options;
//		if ( ! $this->import && ! empty( $ecwd_options['fb_app_id'] ) && ! empty( $ecwd_options['fb_secret_key'] ) ) {
//			$access_token = $this->get_access_token( $ecwd_options['fb_app_id'], $ecwd_options['fb_secret_key'] );
//		} else {
//			$access_token = $this->access_token;
//		}

		if ( ! empty( $this->ecwd_facebook_page_id ) ) {

			$query = 'https://graph.facebook.com/v2.10/'.$this->ecwd_facebook_page_id.'/events?fields=description,end_time,start_time,name,place,cover,timezone&access_token='.$access_token;

			//$query = 'https://graph.facebook.com/v2.3/' . $this->ecwd_facebook_page_id . '/events/attending/?fields=id,name,description,place,timezone,start_time,end_time,cover&access_token=' . $access_token;

//			$args['timeMin'] = urlencode( $this->get_event_start() );
//
//			$args['timeMax'] = urlencode( $this->get_event_end() );

//			$args['maxResults'] = 10000;

//			$ctz = get_option( 'timezone_string' );

//			if ( ! empty( $ctz ) ) {
//				$args['timeZone'] = $ctz;
//			}

//			if ( ! empty( $this->search_query ) ) {
//				$args['q'] = rawurlencode( $this->search_query );
//			}

//			if ( ! empty( $this->expand_recurring ) ) {
//				$args['singleEvents'] = 'true';
//			}

			if(!empty($_POST['event_limit'])){
				$args['limit'] = $_POST['event_limit'];
				$query = add_query_arg($args, $query);
			}

			$this->display_url = $query;

			if ( isset( $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] ) && $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] == true ) {
				echo '<pre>' . $this->display_url . '</pre><br>';
			}

			$this->get_event_data( $query );
		} else {

		}
	}

	/**
	 * Make remote call to get the event data
	 */
	private function get_event_data( $url ) {
		global $ecwd_options;
		$trasient_expiration = ( isset( $ecwd_options['fb_update'] ) && ( (int) $ecwd_options['fb_update'] ) ) ? $ecwd_options['fb_update'] : 12;
		$term_metas          = array();

		if ( isset( $ecwd_options['fb_def_cat'] ) ) {
			$ci = 0;
			foreach ( $ecwd_options['fb_def_cat'] as $setted_cat ) {
				$category_id = (int) $setted_cat;
				if ( $category_id ) {
					$category                  = $term = get_term_by( 'id', $category_id, ECWD_PLUGIN_PREFIX . '_event_category' );
					$term_metas[ $ci ]         = get_option( "ecwd_event_category_$category->term_id" );
					$term_metas[ $ci ]['id']   = $category->term_id;
					$term_metas[ $ci ]['name'] = $category->name;
					$term_metas[ $ci ]['slug'] = $category->slug;
				}
				$ci ++;
			}

		}
		// check for available transient data
		if ( isset( $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] ) && $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] == true ) {
			delete_transient( ECWD_PLUGIN_PREFIX . '_calendar_' . $this->id );
		}
		if ( false !== get_transient( $this->trasient_key ) && ! $this->import ) {
			$this->fevents = get_transient( $this->trasient_key );
		} else {
			$raw_data = wp_remote_get( $url );

			if ( ! is_wp_error( $raw_data ) ) {
				if ( $raw_data['response']['message'] == "OK" ) {
					if ( ! empty( $raw_data['body'] ) ) {
						//If there are some entries (events) to process
						$fb_events     = json_decode( $raw_data['body'], true );
						$this->fevents = $fb_events;

						if ( ! $fb_events ) {
							$this->error = __( 'Some data was retrieved, but could not be parsed successfully. Please ensure your event settings are correct.', 'event-calendar-wd' );
						}
						if ( ! $this->import ) {
							set_transient( $this->trasient_key, $fb_events, 60 * 60 * $trasient_expiration );
						}
					} else {
						$this->error = 'An error has occured.';
						echo 'An error has occured.';
					}
				} else {
					//Generate an error message from the returned WP_Error

					if (!empty($raw_data['body']) && empty($this->error)) {
						$fb_error = json_decode($raw_data['body'], true);
						if (!empty($fb_error['error']['message'])) {
							$this->error = $fb_error['error']['message'];

							echo '<div class="wdm_message error updated notice is-dismissible wdd-message">'
								. '<p>' . $fb_error['error']['message'] . '</p>' .
								'</div>';

						}
					} else if (empty($this->error)) {
						$this->error = 'Please ensure your facebook page ID is correct.';
					}
					//echo 'Your facebook access token has expired.';
				}
			}
		}

		if ( $this->fevents ) {
			foreach ( $this->fevents['data'] as $event ) {
				$this->event              = new stdClass();
				$this->event->id          = ( isset( $event['id'] ) ? esc_html( $event['id'] ) : '' );
				$this->event->title       = ( isset( $event['name'] ) ? esc_html( $event['name'] ) : '' );
				$this->event->description = ( isset( $event['description'] ) ? esc_html( $event['description'] ) : '' );
				$this->event->image       = ( isset( $event['cover']['source'] ) ? esc_html( $event['cover']['source'] ) : '' );
				$this->event->url         = ( isset( $event['id'] ) ? 'https://facebook.com/events/' . esc_html( $event['id'] ) : '' );

				$location                 = ( isset( $event['place']['name'] ) ? esc_html( $event['place']['name'] ) : '' );
				if(!empty($event['place']['location']['latitude']) && !empty($event['place']['location']['longitude'])){
					$latlong = 	$event['place']['location']['latitude'].','.$event['place']['location']['longitude'];
				}else{
					$latlong = '';
				}

				if ( isset( $event['start_time'] ) ) {
					$start_time = $this->iso_to_ts( $event['start_time'] );
				} else {
					$start_time = null;
				}
				$this->event->date_from = $start_time;

				if ( isset( $event['end_time'] ) ) {
					$end_time = $this->iso_to_ts( $event['end_time'] );
				} else {
					$end_time = $start_time;
				}
				$this->event->date_to = $end_time;
				//Create a Spider_Event using the above data. Add it to the array of events
				if ( $this->import ) {
					$this->calendar_id = 1;
				}


				$this->events[ $this->event->id ] = new ECWD_Event( $this->event->id, $this->calendar_id, $this->event->title, $this->event->description, $location, $start_time, $end_time, $this->event->url, $latlong, '', '', $term_metas, '', $this->event->image );

			}
		}

		if ( ! empty( $this->error ) ) {
			if ( current_user_can( 'manage_options' ) && isset( $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] ) && $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] == true ) {
				echo $this->error;

				return;
			}
		} else {
		}
	}

	/**
	 * Convert an ISO date/time to a UNIX timestamp
	 */
	private function iso_to_ts( $iso ) {
		sscanf( $iso, "%u-%u-%uT%u:%u:%uZ", $year, $month, $day, $hour, $minute, $second );

		return mktime( $hour, $minute, $second, $month, $day, $year );
	}

	private function get_event_start() {

		$start    = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_event_start', true );
		$interval = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_event_start_interval', true );

		switch ( $interval ) {
			case 'days':
				return ECWD::ecwd_date( 'c', time() - ( $start * 86400 ) );
			case 'months':
				return ECWD::ecwd_date( 'c', time() - ( $start * 2629743 ) );
			case 'years':
				return ECWD::ecwd_date( 'c', time() - ( $start * 31556926 ) );
		}

// fall back just in case. Falls back to 1 year ago
		return ECWD::ecwd_date( 'c', time() - 31556926 );
	}

	private function get_event_end() {

		$end      = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_event_end', true );
		$interval = get_post_meta( $this->id, ECWD_PLUGIN_PREFIX . '_event_end_interval', true );

		switch ( $interval ) {
			case 'days':
				return ECWD::ecwd_date( 'c', time() + ( $end * 86400 ) );
			case 'months':
				return ECWD::ecwd_date( 'c', time() + ( $end * 2629743 ) );
			case 'years':
				return ECWD::ecwd_date( 'c', time() + ( $end * 31556926 ) );
		}

// Falls back to 1 year ahead just in case
		return ECWD::ecwd_date( 'c', time() + 31556926 );
	}

	function get_builder() {
		$this->builder = get_post( $this->id )->post_content;

		return $this->builder;
	}

	private function get_access_token( $app_id, $secret_key ) {

		$at_data = wp_remote_get( 'https://graph.facebook.com/oauth/access_token?client_id=' . $app_id . '&client_secret=' . $secret_key . '&grant_type=client_credentials' );
		if ( ! is_wp_error( $at_data ) ) {
			$at_value = $at_data['body'];
			$at_value = json_decode($at_value);

			if(isset($at_value->access_token)){
				return $at_value->access_token;
			}else if(isset($at_value->error)){
				$this->error = $at_value->error->message;
			}
			return "";
		}

		return array();
	}

}
