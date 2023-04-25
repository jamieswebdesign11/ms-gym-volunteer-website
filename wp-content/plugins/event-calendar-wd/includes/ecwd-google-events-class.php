<?php

/**
 * ECWD_Google_Events
 */
class ECWD_Google_Events {

	public $id, $calendar_ids, $event_url, $date_format, $time_format, $multiple_day_events, $display_url, $search_query, $expand_recurring, $title, $event, $trasient_key;
	public $events = array();
	public $gevents;
	public $calendar_google_id = null;
	public $timezones = array();
// Google API Key
	private $api_key = '';
	private $import = false;
	private $query = '';

	/**
	 * Class constructor
	 */
	public function __construct() {

	}


	public function import_google_events( $cal_id, $api_key ) {
		if ( ! $cal_id || ! $api_key ) {
			return;
		} else {
			$this->calendar_google_id = $cal_id;
			$this->api_key            = $api_key;
			$this->calendar_id        = '';
			$this->import             = true;
			$this->setup_attributes();

// Now create the Event
			$this->compose_calendar_url();

			return $this->events;
		}

	}

	public function get_google_events( $id, $query = '' ) {
// Set the ID
		$this->calendar_id  = $id;
		$this->query        = $query;
		$this->search_query = $query;
		$calendar_google_id = get_post_meta( $this->calendar_id, ECWD_PLUGIN_PREFIX . '_calendar_id', true );


		if ( $calendar_google_id ) {
			$this->calendar_google_id = $calendar_google_id;
			$this->trasient_key       = substr( ECWD_PLUGIN_PREFIX . '_calendar_' .$this->calendar_id.$this->calendar_google_id, 0, 30 );
// Set up all other data based on the ID
			$this->setup_attributes();

// Now create the Event
			$this->compose_calendar_url();


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
	private function compose_calendar_url() {
		$args = array();
		global $ecwd_options, $gcal_event_timezones_set;

    $gcal_event_timezones_set = isset($_POST['gcal_event_timezones_set']) ? $_POST['gcal_event_timezones_set'] : 0;

		if (isset($ecwd_options['time_zone'])) {
        $timezone = in_array($ecwd_options['time_zone'], timezone_identifiers_list()) ? $ecwd_options['time_zone'] : "Europe/London";
    }else{
        $timezone = in_array(@ini_get('date.timezone'), timezone_identifiers_list()) ? ini_get('date.timezone') : 'Europe/London';
    }

		if ( ! $this->import && ! empty( $ecwd_options['api_key'] ) ) {
			$api_key = $ecwd_options['api_key'];
		} else {
			$api_key = $this->api_key;
		}
		if ( ! empty( $this->calendar_google_id ) ) {

			$query = 'https://www.googleapis.com/calendar/v3/calendars/' . $this->calendar_google_id . '/events';

// Set API key
			$query .= '?key=' . $api_key;

			$args['timeMin'] = urlencode( $this->get_event_start() );

			$args['timeMax'] = urlencode( $this->get_event_end() );

			$args['maxResults'] = 10000;

			if($timezone && $gcal_event_timezones_set){
				$args['timeZone'] = $timezone;
			}

			if ( ! empty( $this->search_query ) ) {
				$args['q'] = rawurlencode( $this->search_query );
			}

			if ( empty( $this->expand_recurring ) ) {
				$args['singleEvents'] = 'true';
			}

			$query             = add_query_arg( $args, $query );
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
		$term_metas = array();
		if ( isset( $ecwd_options['gcal_def_cat'] ) ) {
			$ci = 0;
			foreach ( $ecwd_options['gcal_def_cat'] as $setted_cat ) {
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
		$trasient_expiration = ( isset( $ecwd_options['gcal_update'] ) && ( (int) $ecwd_options['gcal_update'] ) ) ? $ecwd_options['gcal_update'] : 12;

// check for available transient data
		 //delete_transient($this->trasient_key);

		if ( false !== get_transient( $this->trasient_key ) && !$this->import) {
			$this->gevents = get_transient( $this->trasient_key );
		} else {
			$raw_data = wp_remote_get( $url );
//If $raw_data is a WP_Error, something went wrong
			if ( ! is_wp_error( $raw_data ) ) {
//Attempt to convert the returned JSON into an array
				$raw_data = json_decode( $raw_data['body'], true );
				
				if ( ! isset( $raw_data['error'] ) ) {
//If decoding was successful
					if ( ! empty( $raw_data ) ) {
//If there are some entries (events) to process
//if ( isset( $raw_data['event']['entry'] ) ) {
						$this->gevents = $raw_data['items'];
						set_transient( $this->trasient_key, $raw_data['items'], ( 60 * 60 * $trasient_expiration ) );
					} else {
//json_decode failed
//$this->error = __('Some data was retrieved, but could not be parsed successfully. Please ensure your event settings are correct.', 'ecwd');
					}
				} else {
					$this->error = __( 'An error has occured.', 'event-calendar-wd' );
					$this->error .= '<pre>' . $raw_data['error']['message'] . '</pre>';
				}
			} else {
//Generate an error message from the returned WP_Error
				$this->error = $raw_data->get_error_message() . __( ' Please ensure your calendar ID is correct.', 'event-calendar-wd' );
			}
		}

		if ( $this->gevents ) {

			foreach ( $this->gevents as $event ) {
				$timezone = isset($raw_data["timeZone"]) ? $raw_data["timeZone"] : "";
				$all_day     = 0;
				$this->event = new stdClass();
				if ( isset( $event["start"] ) && isset( $event["start"]['timeZone'] ) ) {
					$timezone = $event["start"]['timeZone'];
				}

				if ( isset( $event['summary'] ) ) {
					$location = ( isset( $event['location'] ) ? esc_html( $event['location'] ) : '' );
					if ( $this->query == '' || stripos( $location, $this->query ) !== false || stripos( $event['organizer']['displayName'], $this->query ) !== false || stripos( $event['summary'], $this->query ) !== false ) {
						
						$this->event->id          = ( isset( $event['id'] ) ? esc_html( $event['id'] ) : '' );
						$this->event->title       = ( isset( $event['summary'] ) ? esc_html( $event['summary'] ) : '' );
						$this->event->description = ( isset( $event['description'] ) ? esc_html( $event['description'] ) : '' );
						$this->event->url         = ( isset( $event['htmlLink'] ) ? esc_url( $event['htmlLink'] ) : '' );
						$this->timezones[$this->event->id]    = $timezone;
						if ( isset( $event['start']['dateTime'] ) ) {
							$start_time = $this->iso_to_ts( $event['start']['dateTime'] );
						} else if ( isset( $event['start']['date'] ) ) {
							$start_time = $this->iso_to_ts( $event['start']['date'] );
							$all_day    = 1;
						} else {
							$start_time = null;
						}
						$this->event->date_from = $start_time;

						if ( isset( $event['end']['dateTime'] ) ) {
							$end_time = $this->iso_to_ts( $event['end']['dateTime'] );
						} else if ( isset( $event['end']['date'] ) ) {
							$end_time = $this->iso_to_ts( $event['end']['date'] );
              $end_time = strtotime('-1 day', $end_time);
						} else {
							$end_time = null;
						}
						$this->event->date_to = $end_time;
						$metas                = array();
						$repeat               = '';
						$until                = '';
						$days                 = '';
						$count                = 0;
						$repeat_array         = array();
						if ( isset( $event['recurrence'] ) ) {


            if(isset($event['recurrence'][0]) && strpos($event['recurrence'][0], 'RRULE') !==false){
              $repeating_rule = explode( ':', $event['recurrence'][0] );
            }elseif(isset($event['recurrence'][1]) && strpos($event['recurrence'][1], 'RRULE') !==false){
               $repeating_rule = explode( ':', $event['recurrence'][1] );
            }else{
              continue;
            }

							$repeating_rule = array_pop( $repeating_rule );
							$repeating_rule = explode( ';', $repeating_rule );

							$repeat_array = array();
							foreach ( $repeating_rule as $row ) {
								list( $key, $value ) = explode( '=', $row );

								if ( $key == 'BYDAY' ) {
									$value = explode( ',', $value );
								}

								$repeat_array[ $key ] = $value;

							}

							$repeating_event_limit = 300;
							$weekday_short_array   = array( 'SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA' );
							$weekday_medium_array  = array(
								'sunday',
								'monday',
								'tuesday',
								'wednesday',
								'thursday',
								'friday',
								'saturday'
							);
							$ordinal_array         = array( 'zero', 'first', 'second', 'third', 'fourth', 'last' );
							$timestamp             = ( $start_time );
							$elapsed_time          = ( $end_time ) - $timestamp;
							$count                 = 1;
             

							switch ( $repeat_array['FREQ'] ) {
								case 'YEARLY':
									$until    = '2050/01/01';
									$interval = isset( $repeat_array['INTERVAL'] ) ? $repeat_array['INTERVAL'] : 1;
									if ( isset( $repeat_array['UNTIL'] ) ) {
										$until = ECWD::ecwd_date( 'Y/m/d', strtotime( $repeat_array['UNTIL'] ) );
									} else {
										$until = '';
										if ( isset( $repeat_array['COUNT'] ) && $repeat_array['COUNT'] > 0 ) {
											$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * ($repeat_array['COUNT']-1) . " year" ));
										}

									}
									$metas     = array(
										ECWD_PLUGIN_PREFIX . '_event_repeat_event'         => array( 'yearly' ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until'  => array( $until ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_how'           => array( $interval ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_year_on_days' => array( 1 )
									);
									break;
								case 'MONTHLY':
									$until    = '2025/01/01';
									$interval = isset( $repeat_array['INTERVAL'] ) ? $repeat_array['INTERVAL'] : 1;
									if ( isset( $repeat_array['UNTIL'] ) ) {
										$until = ECWD::ecwd_date( 'Y/m/d', strtotime( $repeat_array['UNTIL'] ) );
									} else {
										$until = '';
										if ( isset( $repeat_array['COUNT'] ) && $repeat_array['COUNT'] > 0 ) {
											$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * ($repeat_array['COUNT']-1) . " month" ));
										}

									}
									if ( !isset( $repeat_array['BYDAY'] ) ) {
										$timestamp = strtotime( ECWD::ecwd_date( 'c', $timestamp ) . " +{$interval} month" );
										$metas     = array(
											ECWD_PLUGIN_PREFIX . '_event_repeat_event'         => array( 'monthly' ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until'  => array( $until ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_how'           => array( $interval ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_month_on_days' => array( 1 )
										);
									} else {
										$by_day = $repeat_array['BYDAY'][0];

										$by_day_week_number = substr( $by_day, 0, 1 );
										$ordinal            = isset($ordinal_array[ $by_day_week_number ]) ? $ordinal_array[ $by_day_week_number ] : "";

										$by_day_weekday = substr( $by_day, 1 );
										$day_index      = array_search( $by_day_weekday, $weekday_short_array );
										$dayname        = $weekday_medium_array[ $day_index ];


										$metas = array(
											ECWD_PLUGIN_PREFIX . '_event_repeat_event'         => array( 'monthly' ),
											ECWD_PLUGIN_PREFIX . '_monthly_list_monthly'       => array( $ordinal ),
											ECWD_PLUGIN_PREFIX . '_monthly_week_monthly'       => array( $dayname ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until'  => array( $until ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_how'           => array( $interval ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_month_on_days' => array( 2 ),
										);
									}
									break;
								case 'WEEKLY':
  
									$until    = '2020/01/01';
                  
									$interval = isset( $repeat_array['INTERVAL'] ) ? $repeat_array['INTERVAL'] : 1;
									if ( isset( $repeat_array['UNTIL'] ) ) {
										$until = ECWD::ecwd_date( 'Y/m/d', strtotime( $repeat_array['UNTIL'] ) );
									} else {
										if ( isset( $repeat_array['COUNT'] ) && $repeat_array['COUNT'] > 0 ) {
											$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * ($repeat_array['COUNT']-1) . " week" ));
										}
									}
                  

									if ( !isset( $repeat_array['BYDAY'] ) ) {
										$metas     = array(
											ECWD_PLUGIN_PREFIX . '_event_repeat_event'         => array( 'weekly' ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until'  => array( $until ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_how'           => array( $interval ),
											ECWD_PLUGIN_PREFIX . '_event_day' => array( 1 )
										);
									} else {
										$by_day = $repeat_array['BYDAY'];
		                                $days = serialize($this->convert_week_days($repeat_array['BYDAY']));
										$metas = array(
											ECWD_PLUGIN_PREFIX . '_event_repeat_event'         => array( 'weekly' ),
		                                    ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until' => array( $until ),
		                                    ECWD_PLUGIN_PREFIX . '_event_repeat_how'          => array( $interval ),
		                                    ECWD_PLUGIN_PREFIX . '_event_day'                 => array( $days )
										);

									}
									break;
								case 'DAILY':

									$until    = '2020/01/01';
									$interval = isset( $repeat_array['INTERVAL'] ) ? $repeat_array['INTERVAL'] : 1;
									if ( isset( $repeat_array['UNTIL'] ) ) {
										$until = ECWD::ecwd_date( 'Y/m/d', strtotime( $repeat_array['UNTIL'] ) );
									} else {
										if ( isset( $repeat_array['COUNT'] ) && $repeat_array['COUNT'] > 0 ) {
											$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * ($repeat_array['COUNT']-1) . " day" ));
										}

									}
									$metas     = array(
										ECWD_PLUGIN_PREFIX . '_event_repeat_event'         => array( 'daily' ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until'  => array( $until ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_how'           => array( $interval ),
									);
									break;
								default:
									$until    = '2050/01/01';
									$interval = isset( $repeat_array['INTERVAL'] ) ? $repeat_array['INTERVAL'] : 1;
									if ( isset( $repeat_array['UNTIL'] ) ) {
										$until = ECWD::ecwd_date( 'Y/m/d', strtotime( $repeat_array['UNTIL'] ) );
									} else {
										if ( isset( $repeat_array['COUNT'] ) && $repeat_array['COUNT'] > 0 ) {
											$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * ($repeat_array['COUNT']-1) . " week" ));
										}

									}
									if ( !isset( $repeat_array['BYDAY'] ) ) {
										$metas     = array(
											ECWD_PLUGIN_PREFIX . '_event_repeat_event'         => array( 'weekly' ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until'  => array( $until ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_how'           => array( $interval ),
											ECWD_PLUGIN_PREFIX . '_event_day' => array( 1 )
										);
									} else {
										$by_day = $repeat_array['BYDAY'];
										$days = serialize($this->convert_week_days($repeat_array['BYDAY']));
										$metas = array(
											ECWD_PLUGIN_PREFIX . '_event_repeat_event'         => array( 'weekly' ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until' => array( $until ),
											ECWD_PLUGIN_PREFIX . '_event_repeat_how'          => array( $interval ),
											ECWD_PLUGIN_PREFIX . '_event_day'                 => array( $days )
										);

									}
								break;

							}


						}

						$metas[ ECWD_PLUGIN_PREFIX . '_all_day_event' ][0] = $all_day;
						//Create a Spider_Event using the above data. Add it to the array of events
            
            
						$this->events[ $this->event->id ] = new ECWD_Event( $this->event->id, $this->calendar_id, $this->event->title, $this->event->description, $location, $start_time, $end_time, $this->event->url, '', '', '', $term_metas, $metas );

					}
				}
			}
		}
		if ( ! empty( $this->error ) ) {
//			add_settings_error('ecwd_gcal',  333, $this->error);
			if ( current_user_can( 'manage_options' ) && isset( $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] ) && $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] == true ) {
				echo $this->error;

				return;
			}
		} else {

		}
	}

	public function insert_event( $id ) {
		if ( ! empty( $ecwd_options['api_key'] ) ) {
			$api_key = $ecwd_options['api_key'];
		} else {
			$api_key = $this->api_key;
		}
		$calendar_google_id = get_post_meta( $id, ECWD_PLUGIN_PREFIX . '_gcalendar_id', true );
		if ( $calendar_google_id ) {

			$query = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_google_id . '/events/quickAdd';
// Set API key
			$query .= '?key=' . $api_key;
			$data['calendarId'] = $calendar_google_id;
			$data['email']      = 'lusinda7@gmail.com';
			$data['password']   = '';
			$data['end']        = '2015-01-23 02:00:00';
			$data['start']      = '2015-01-22 02:00:00';
			$data['text']       = 'Lusinda\'s  test';

			$response = wp_remote_post( $query, array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => $data,
				'cookies'     => array()
			) );
			echo '<pre>';			
			die;
		}
	}

	/**
	 * Convert an ISO date/time to a UNIX timestamp
	 */
	private function iso_to_ts( $iso ) {
		sscanf( $iso, "%u-%u-%uT%u:%u:%uZ", $year, $month, $day, $hour, $minute, $second );

		return mktime( $hour, $minute, $second, $month, $day, $year );
	}

	private function convert_week_days( $days ) {
		$weekdays = array(
			'MO' => 'monday',
			'TU' => 'tuesday',
			'WE' => 'wednesday',
			'TH' => 'thursday',
			'FR' => 'friday',
			'SA' => 'saturday',
			'SU' => 'sunday'
		);
		foreach ( $days as $key => $day ) {
			if ( isset( $weekdays[ $day ] ) ) {
				$days[ $key ] = $weekdays[ $day ];
			}
		}

		return $days;
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

}
