<?php

/**
 * Class ECWD_Ical_Events
 */
class ECWD_Ical_Events {

	public $id, $calendar_ids, $event_url, $date_format, $time_format, $multiple_day_events, $display_url, $search_query, $expand_recurring, $title, $event, $trasient_key;
	public $events = array();
	public $gevents;
	public $import = false;
	public $calendar_ical_url = null;
	public $timezone = "";
	public $timezone_offset = "";

	/**
	 * Class constructor
	 */
	public function __construct() {

	}

	public function import_ical_events( $id ) {
		$this->import = 1;

		return $this->get_events( '', '', $id );
	}

	public function get_events( $id = '', $query = '', $calendar_ical_url = '' ) {
		global $ecwd_options;
		$term_metas = array();

		if ( isset( $ecwd_options['ical_def_cat'] ) ) {
			$ci = 0;
			foreach ( $ecwd_options['ical_def_cat'] as $setted_cat ) {
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
		$trasient_expiration = ( isset( $ecwd_options['ical_update'] ) && ( (int) $ecwd_options['ical_update'] ) ) ? $ecwd_options['ical_update'] : 12;
// Set the ID
		$this->calendar_id  = $id;
		$this->query        = $query;
		$this->search_query = $query;
		$this->trasient_key = substr( ECWD_PLUGIN_PREFIX . '_calendar_' . $this->calendar_id.$this->calendar_id, 0, 30 );
		if ( ! $this->import ) {
			$calendar_ical_url = get_post_meta( $this->calendar_id, ECWD_PLUGIN_PREFIX . '_calendar_ical', true );
		}

		if ( $calendar_ical_url ) {
			$this->calendar_ical_url = $calendar_ical_url;
			if ( isset( $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] ) && $_GET[ ECWD_PLUGIN_PREFIX . '_debug' ] == true ) {
				echo '<pre>' . $this->calendar_ical_url . '</pre><br>';
			}


			if ( ( false == $events = get_transient( $this->trasient_key ) ) || $this->import ) {
				$ical = new ICalReader( $this->calendar_ical_url );
				$icalevents = $ical->events();
				$events = $ical->sortEventsWithOrder( $icalevents['VEVENT'] );

				if ( ! $this->import ) {
					set_transient( $this->trasient_key, $events, 60 * 60 * $trasient_expiration );
				}
				$this->timezone = isset($icalevents["VTIMEZONE"]) ? $icalevents["VTIMEZONE"]["TZID"] : "";
				$this->timezone_offset = isset($icalevents["STANDARD"]) ? $icalevents["STANDARD"]["TZOFFSETFROM"] : "";
			}

			if ( $events ) {
				foreach ( $events as $event ) {
					$all_day = (strpos( $event['DTSTART'], "T" ) !== false) ? 0 : 1;
					$metas      = array();
					$repeat     = '';
					$until      = '';
					$days       = '';
					$start_time = strtotime( $event['DTSTART'] );
					if ( isset( $event['RRULE'] ) ) {
						$repeating_rule = explode( ':', $event['RRULE'] );
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

						switch ( $repeat_array['FREQ'] ) {
							case 'YEARLY':

								$until    = '2050/01/01';
								$interval = isset( $repeat_array['INTERVAL'] ) ? $repeat_array['INTERVAL'] : 1;
								if ( isset( $repeat_array['UNTIL'] ) ) {
									$until = ECWD::ecwd_date( 'Y/m/d', strtotime( $repeat_array['UNTIL'] ) );
								} else {
									$until = '';
									if ( isset( $repeat_array['COUNT'] ) && $repeat_array['COUNT'] > 0 ) {

										$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * $repeat_array['COUNT'] . " year" ) );
									}

								}

								$metas = array(
									ECWD_PLUGIN_PREFIX . '_event_repeat_event'        => array( 'yearly' ),
									ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until' => array( $until ),
									ECWD_PLUGIN_PREFIX . '_event_repeat_how'          => array( $interval ),
									ECWD_PLUGIN_PREFIX . '_event_repeat_year_on_days' => array( 1 )
								);


								break;
							case 'MONTHLY':
								$until    = '2025/01/01';
								$interval = isset( $repeat_array['INTERVAL'] ) ? $repeat_array['INTERVAL'] : 1;
								if ( isset( $repeat_array['UNTIL'] ) ) {
									$until = ECWD::ecwd_date( 'Y/m/d', strtotime( $repeat_array['UNTIL'] ) );
								} else {
									$until    = '2025/01/01';
									if ( isset( $repeat_array['COUNT'] ) && $repeat_array['COUNT'] > 0 ) {
										$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * ($repeat_array['COUNT']-1) . " month" ) );
									}

								}
								if ( ! isset( $repeat_array['BYDAY'] ) ) {
									$metas = array(
										ECWD_PLUGIN_PREFIX . '_event_repeat_event'         => array( 'monthly' ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until'  => array( $until ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_how'           => array( $interval ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_month_on_days' => array( 1 )
									);
								} else {
									$by_day = $repeat_array['BYDAY'][0];

									$by_day_week_number = substr( $by_day, 0, 1 );
									$ordinal            = $ordinal_array[ $by_day_week_number ];

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
										$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * ($repeat_array['COUNT']-1) . " week" ) );
									}

								}

								if ( ! isset( $repeat_array['BYDAY'] ) ) {
									$metas = array(
										ECWD_PLUGIN_PREFIX . '_event_repeat_event'        => array( 'weekly' ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until' => array( $until ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_how'          => array( $interval ),
										ECWD_PLUGIN_PREFIX . '_event_day'                 => array( 1 )
									);
								} else {
									$by_day = $repeat_array['BYDAY'];
									$days   = serialize( $this->convert_week_days( $repeat_array['BYDAY'] ) );
									$metas  = array(
										ECWD_PLUGIN_PREFIX . '_event_repeat_event'        => array( 'weekly' ),
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
									$until    = '2020/01/01';
									if ( isset( $repeat_array['COUNT'] ) && $repeat_array['COUNT'] > 0 ) {
										$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * ($repeat_array['COUNT']-1) . " day" ) );
									}

								}
								$metas = array(
									ECWD_PLUGIN_PREFIX . '_event_repeat_event'        => array( 'daily' ),
									ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until' => array( $until ),
									ECWD_PLUGIN_PREFIX . '_event_repeat_how'          => array( $interval ),
								);
								break;
							default:
								$until    = '2999/01/01';
								$interval = isset( $repeat_array['INTERVAL'] ) ? $repeat_array['INTERVAL'] : 1;
								if ( isset( $repeat_array['UNTIL'] ) ) {
									$until = ECWD::ecwd_date( 'Y/m/d', strtotime( $repeat_array['UNTIL'] ) );
								} else {
									$until    = '2999/01/01';
									if ( isset( $repeat_array['COUNT'] ) && $repeat_array['COUNT'] > 0 ) {
										$until = ECWD::ecwd_date( "Y/m/d", strtotime( ECWD::ecwd_date( "Y/m/d", $start_time ) . " +" . $interval * ($repeat_array['COUNT']-1) . " week" ) );
									}

								}
								if ( ! isset( $repeat_array['BYDAY'] ) ) {
									$metas = array(
										ECWD_PLUGIN_PREFIX . '_event_repeat_event'        => array( 'weekly' ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until' => array( $until ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_how'          => array( $interval ),
										ECWD_PLUGIN_PREFIX . '_event_day'                 => array( 1 )
									);
								} else {
									$by_day = $repeat_array['BYDAY'];
									$days   = serialize( $this->convert_week_days( $repeat_array['BYDAY'] ) );
									$metas  = array(
										ECWD_PLUGIN_PREFIX . '_event_repeat_event'        => array( 'weekly' ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_repeat_until' => array( $until ),
										ECWD_PLUGIN_PREFIX . '_event_repeat_how'          => array( $interval ),
										ECWD_PLUGIN_PREFIX . '_event_day'                 => array( $days )
									);

								}
								break;

						}


					}
					$metas[ECWD_PLUGIN_PREFIX . '_all_day_event'][0] = $all_day;

					$this->events[ $event['UID'] ] = new ECWD_Event( $event['UID'], $this->calendar_id, ( isset( $event['SUMMARY'] ) ? $event['SUMMARY'] : '' ), ( isset( $event['DESCRIPTION'] ) ? $event['DESCRIPTION'] : '' ), ( isset( $event['LOCATION'] ) ? $event['LOCATION'] : '' ), ( isset( $event['DTSTART'] ) ? strtotime( $event['DTSTART'] ) : '' ), ( isset( $event['DTEND'] ) ? strtotime( $event['DTEND'] ) : '' ), ( isset( $event['URL'] ) ? $event['URL'] : '' ), '', '', '', $term_metas, $metas );

				}

			}


		}

		return $this->events;
	}

	/**
	 * Set all of the event attributes from the post meta options
	 */
	private function setup_attributes() {

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
			$days[ $key ] = $weekdays[ $day ];
		}

		return $days;
	}

	/**
	 * Create the event URL
	 */
	private function compose_calendar_url() {

	}

	/**
	 * Make remote call to get the event data
	 */
	private function get_event_data( $url ) {
	}


}
