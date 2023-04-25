<?php

class Calendar {

  public $weekstartday = 0;      // week start day (0-6 e.g. 0 = Sunday, 1 = Monday, etc.)
  public $monthselector = true;    // month/year select box (true=show selector,false=show month name)
  public $yearoffset = 5;     // month selector range of years (int)
  public $weeknumbers = false;    // adds a column for week numbers (left,right,false)
  public $weeknumrotate = true;    // rotate week-numbers 90 degrees *currently only firefox is supported* (true,false)
  public $weeknames = 2;      // controls how weekdays are displayed. (1=full,2=abbrev,3=single char)
  public $monthnames = 1;     // controls how months are displayed. (1=full,2=abbrev)
  public $basecolor = '7D9AC0';    // base color for color scheme (hex)
  public $color = 'blue';    //  color for color scheme (hex)
  public $minilinkbase = '';     // base url for links on mini calendar (blank=disabled)
  public $eventlistbg = '10738B';   // event list view bg color (hex)
  public $eventemptytext = 'No additional details for this event.'; // default text in event view when details is empty (string)
  public $dateformat = 'Y.m.d';    // default date format (passed to php date() public function)
  public $list_date_format = 'd.F.l';    // default date format (passed to php date() public function)
  public $timeformat = 'H:i';    // default time format (passed to php date() public function)
  public $font = '"Lucida Grande","Lucida Sans Unicode",sans-serif'; // font used to display the calendar (any css supported value)
  public $linktarget = 'parent';    // link target frame or window (e.g. 'parent.frameName'. Use '_blank' for new window/tab)
  public $listlimit = false;     // limit the number of events in list and mini-list (false or int e.g. 10)
  public $listtitle = ''; //'Event List';   // Title shown when displaying full event list
  public $widget = 0;
  public $search_params = array();
  public $categories = array();
  public $tags = array();
  public $venues = array();
  public $organizers = array();
  public $displays = array('full', 'list', 'week', 'day', 'map', '4day', 'posterboard');
  public $filters = array('categories', 'tags', 'venues', 'organizers');
  public $event_search = 'yes';
  public $displaysName;
  public $eventlinktarget;
  public $event_popup = "no";
  public $start_date = "";
  public $end_date = "";
  public $calendar_head = "yes";
//--------------------------------------------------------------------------------------------
// Weekday names/abbreviations (array must start with Sunday=0)
//--------------------------------------------------------------------------------------------

  public $weekdate;
  public $weekdays = array(
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday"
  );
  public $abbrevweekdays = array(
    "Sun",
    "Mon",
    "Tue",
    "Wed",
    "Thu",
    "Fri",
    "Sat"
  );
  public $weekdayschar = array(
    "Su",
    "Mo",
    "Tu",
    "We",
    "Th",
    "Fr",
    "Sa"
  );
//--------------------------------------------------------------------------------------------
// Other public used. (No need to edit below here)
//--------------------------------------------------------------------------------------------

  public $jd, $year, $month, $day, $displaytype, $numdaysinmonth, $monthstartday, $monthname, $previousmonth, $nextmonth;
  public $events = array();
  public $json_ld = '';

  // types: mini,full,list,mini-list, map, 4day, week, posterboard (default=full)
  public function __construct($type = 'full', $date = '', $staticdisplaytype = false, $widget = 0, $limit = false, $page = 1, $displays = null, $filters = null, $event_search = 'yes', $ecwd_views = array(), $preview = false) {
    add_filter('format_content', 'wptexturize');
    add_filter('format_content', 'convert_smilies');
    add_filter('format_content', 'wpautop');
    add_filter('format_content', 'shortcode_unautop');
    add_filter('format_content', 'prepend_attachment');
    global $wp_version;
    if (isset($wp_version)) {
      if (version_compare($wp_version, '4.4', '>=')) {
	      if (version_compare($wp_version, '5.5', '>=')) {
		      add_filter('format_content', 'wp_filter_content_tags');
	      } else {
		      add_filter('format_content', 'wp_make_content_images_responsive');
	      }
      }
    }
    global $ecwd_options;
    $this->eventemptytext = isset($ecwd_options['event_default_description']) ? $ecwd_options['event_default_description'] : $this->eventemptytext;

    if (isset($ecwd_views['calendar_head']) && $ecwd_views['calendar_head'] == 'no') {
      $this->calendar_head = "no";
      (isset($ecwd_views['start_date'])) ? $this->start_date = $ecwd_views['start_date'] : "";
      (isset($ecwd_views['end_date']) && $ecwd_views['end_date'] != "") ? $this->end_date = $ecwd_views['end_date'] : "";
    }


    if (isset($ecwd_options['date_format']) && $ecwd_options['date_format'] != '') {
      if (isset($ecwd_options['date_format'])) {
        $this->dateformat = $ecwd_options['date_format'];
      }
    }
    if (isset($ecwd_options['list_date_format']) && $ecwd_options['list_date_format'] != '') {
      $this->list_date_format = $ecwd_options['list_date_format'];
    }
    if (isset($ecwd_options['time_format']) && $ecwd_options['time_format'] != '') {
      $this->timeformat = $ecwd_options['time_format'];
    }

    $this->eventlinktarget .= (isset($ecwd_options['events_new_tab']) && $ecwd_options['events_new_tab'] == 1 ? ' target="_blank" ' : '');

    $this->timeformat .= (isset($ecwd_options['time_type']) ? ' ' . $ecwd_options['time_type'] : '');
    if (isset($ecwd_options['time_type']) && $ecwd_options['time_type'] != '') {
      $this->timeformat = str_replace('H', 'g', $this->timeformat);
      $this->timeformat = str_replace('h', 'g', $this->timeformat);
    }
    if (isset($ecwd_options['week_starts']) && $ecwd_options['week_starts'] != '') {
      $this->weekstartday = $ecwd_options['week_starts'];
    }
    if ($preview == false) {
      if ($ecwd_options['events_in_popup'] == '1') {
        $this->event_popup = "yes";
      }
    }
    $this->widget = $widget;
    $this->page = $page;

    if ($displays && is_array($displays) && $displays[0] != '') {
      $displays = array_unique($displays);
      if (($key = array_search('none', $displays)) !== false) {
        unset($displays[$key]);
      }
      $this->displays = $displays;
    }
    if ($filters && is_array($filters) && $filters[0] != '') {
      $filters = array_unique($filters);
      if (($key = array_search('none', $filters)) !== false) {
        unset($filters[$key]);
      }
      $this->filters = $filters;
    }
    if ($staticdisplaytype === true) {
      $this->displaytype = $type;
    } else {
	// calendar display type
      if ($type != '' && !isset($_REQUEST['t'])) {
        $this->displaytype = $type; // if type is not set in querystring set type to passed value
      } else {
        $this->displaytype = sanitize_text_field($_REQUEST['t']); // else set to type passed in querystring
        $this->displaytype = in_array($this->displaytype, array('full', 'mini', 'list', 'mini-list', 'week', 'day', '4day', 'map', 'posterboard')) ? $this->displaytype : 'full';
      }
    }

// calendar date
    if ($date == '' && !isset($_REQUEST['date'])) {
      $date = ECWD::ecwd_date('Y-n-j', time());
    } // set to todays date if no value is set
    if (isset($_REQUEST['date']) && $_REQUEST['date'] != '') {
      $date = sanitize_text_field($_REQUEST['date']);
    } // check if date is in the querystring
    $date = ECWD::ecwd_date('Y-n-j', strtotime($date)); // format the date for parsing
    $date_part = explode('-', $date); // separate year/month/day
    $year = $date_part[0];
    $month = $date_part[1];
    $day = $date_part[2];
    if (!is_archive() && isset($_REQUEST['y']) && $_REQUEST['y'] != '') {
      $year = sanitize_text_field($_REQUEST['y']);
    } // if year is set in querystring it takes precedence
    if (!is_archive() && isset($_REQUEST['m']) && $_REQUEST['m'] != '') {
      $month = sanitize_text_field($_REQUEST['m']);
    } // if month is set in querystring it takes precedence
    if (!is_archive() && isset($_REQUEST['d']) && $_REQUEST['d'] != '') {
      $day = sanitize_text_field($_REQUEST['d']);
    } // if day is set in querystring it takes precedence
// make sure we have year/month/day as int
    if ($year == '') {
      $year = ECWD::ecwd_date('Y', time());
    }
    if ($month == '') {
      $month = ECWD::ecwd_date('n', time()); // set to january if year is known
    }
    if ($day == '') {
      $day = ECWD::ecwd_date('j', time()); // set to the 1st is year and month is known
    }
    
    $this->date = $date;
    $this->month = (int)$month;
    $this->year = (int)$year;
    $this->day = (int)$day;

// find out the number of days in the month
//  $this->numdaysinmonth = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
    $this->numdaysinmonth = $this->cal_days_in_month();
// create a calendar object
    $this->jd = cal_to_jd(CAL_GREGORIAN, $this->month, ECWD::ecwd_date(1, time()), $this->year);

// get the month start day as an int (0 = Sunday, 1 = Monday, etc)
    $this->monthstartday = jddayofweek($this->jd, 0);

// get the month as a name
    $this->monthname = __(ECWD::ecwd_date('F', strtotime($this->date)), 'event-calendar-wd');

//get weekdate
    $this->weekdate = $this->getWeekFirstDayDate($this->date);
            //get month date
    $this->monthdate = $this->getMonthDate($this->date);

//get day date
    $this->daydate = $this->getDayDate($this->date);

    $this->displaysName = array(
                'full' => array(
                    'name' => __('Month', 'event-calendar-wd'),
                    'date' => $this->monthdate
                ),
                'mini' => array(
                    'name' => __('Month', 'event-calendar-wd'),
                    'date' => $this->monthdate
                ),
                'list' => array(
                    'name' => __('List', 'event-calendar-wd'),
                    'date' => $this->monthdate
                ),
      'week' => array(
        'name' => __('Week', 'event-calendar-wd'),
        'date' => $this->weekdate
      ),
      'day' => array(
        'name' => __('Day', 'event-calendar-wd'),
        'date' => $this->daydate
      ),
      'map' => array('name' => __('Map', 'event-calendar-wd'), 'date' => $this->monthdate),
      '4day' => array(
        'name' => __('4 Days', 'event-calendar-wd'),
        'date' => $this->year . '-' . $this->month . '-' . $this->day
      ),
      'posterboard' => array('name' => __('Posterboard', 'event-calendar-wd'), 'date' => $this->monthdate)
    );
    $this->event_search = $event_search;
    $this->listlimit = $limit;
    if ($this->displaytype == 'list') {
      $this->period_for_list = (isset($ecwd_options['period_for_list'])) ? $ecwd_options['period_for_list'] : 1;
    } else {
      $this->period_for_list = 1;
    }
  }

// header area for all displaytypes

  public function month_selector() {
    $html = '';


    return $html;
  }

  public function add_terms($type = 'categories', $terms = array() ) {
    if (is_array($terms)) {
      $this->$type = $terms;
    }
  }

  public function addEvent($arr) {
    $this->events[] = $arr;
  }

  public function addEvents($arr) {
    $this->events = $arr;
  }

// next month link

  public function dateDiff($beginDate, $endDate) {
    if ($endDate == '') {
      return 0;
    }
    $fromDate = ECWD::ecwd_date('Y-n-j', strtotime($beginDate));
    $toDate = ECWD::ecwd_date('Y-n-j', strtotime($endDate));
    $date_parts1 = explode('-', $fromDate);
    $date_parts2 = explode('-', $toDate);


    $start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
    $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);

    return $end_date - $start_date;
  }

// previous month link

  public function showcal() {
    global $cal_ID;
    global $ecwd_options;
    $html = '';
    if ($this->calendar_head == "yes") {
      $html .= $this->calendar_head(); // set table head
    }
    $this->seted_days = array();
    if ($this->calendar_head == "no") {
      $start_date = $this->start_date;
      $end_date = $this->end_date;
    } else {
      $start_date = strtotime($this->year . '-' . $this->month . '-1');
      $end_date = ECWD::ecwd_date('Y-m-t', strtotime($this->date));
    }

    if (!in_array($this->displaytype, array('list', 'mini-list', 'week', 'day', '4day', 'map', 'posterboard')) ) { // mini and full cal
      $html .= '<tr>';

// render week number on left
      if ($this->weeknumbers == 'left' && $this->monthstartday != $this->weekstartday) {
        $html .= '<td class="week-number"><span>' . ECWD::ecwd_date('W', strtotime($this->year . '-' . $this->month)) . '</span></td>';
      }

// render previous month cells
      $emptycells = 0;

      $numinrow = 7;

// adjust for weekstartdays
      $weekstartadjust = $this->monthstartday - $this->weekstartday;
      if ($weekstartadjust < 0) {
        $weekstartadjust = $weekstartadjust + $numinrow;
      }

      for ($counter = 0; $counter < $weekstartadjust; $counter++) {
        if ($counter == 0) {
          $thisclass = 'day-without-date week-start';
        } // only on first
        else {
          $thisclass = 'day-without-date';
        }
        if ($this->displaytype == 'full') {
          $html .= $this->calendar_cell(__($this->previousmonth, 'event-calendar-wd'), $thisclass);
        } else {
          $html .= $this->calendar_cell('&nbsp;', $thisclass);
        }
        $emptycells++;
      }

// render days
      $rowcounter = $emptycells;
      $weeknumadjust = $numinrow - ($this->monthstartday - $this->weekstartday);

      for ($counter = 1; $counter <= $this->numdaysinmonth; $counter++) {

        $date = $this->year . '-' . $this->month . '-' . $counter;
// render week number on left
        if ($this->weeknumbers == 'left' && $this->weekstartday == $this->getDay($date, 0)) {
          $adjustweek = $this->calcDate($date, '+' . $weeknumadjust, 'day');
          $adjustweek = $adjustweek['year'] . '-' . $adjustweek['month'] . '-' . $adjustweek['day'];
          $html .= '<td class="week-number"><span>' . ECWD::ecwd_date('W', strtotime($adjustweek)) . '</span></td>';
        }
        $rowcounter++;
        $html .= $this->calendar_cell($counter, 'day-with-date', $date);
        if ($rowcounter % $numinrow == 0) {
// render week number on right
          if ($this->weeknumbers == 'right') {
            $html .= '<td class="week-number"><span>' . ECWD::ecwd_date('W', strtotime($date)) . '</span></td>';
          }
          $html .= "</tr>";
          if ($counter < $this->numdaysinmonth) {
            $html .= "<tr>";
          }
          $rowcounter = 0;
        }
      }

// render next month cells
      $numcellsleft = $numinrow - $rowcounter;
      if ($numcellsleft != $numinrow) {
        for ($counter = 0; $counter < $numcellsleft; $counter++) {
          if ($this->displaytype == 'full') {
            $html .= $this->calendar_cell($this->nextmonth, 'day-without-date');
          } else {
            $html .= $this->calendar_cell('&nbsp;', 'day-without-date');
          }
          $emptycells++;
        }
      }

// render week number on right
      if ($this->weeknumbers == 'right' && $numcellsleft != 7) {
        $html .= '<td class="week-number" style="border-bottom:1px solid #' . $this->bordercolor . ';"><span>' . ECWD::ecwd_date('W', strtotime($date)) . '</span></td>';
      }

      $html .= '</tr>';
    }
    elseif ($this->displaytype == 'week') {
      $html .= '<ul class="week-event-list">';
      $currentWeek = $this->rangeWeek($this->year . '-' . $this->month . '-' . $this->day);
      $date = $currentWeek['start'];

      while (strtotime($date) <= strtotime($currentWeek['end'])) {
        $html .= '<li>' . $this->calendar_cell(ECWD::ecwd_date('d', strtotime($date)), 'day-with-date', ECWD::ecwd_date('Y-n-j', strtotime($date))) . '</li>';
        $date = ECWD::ecwd_date("Y-m-d", strtotime("+1 day", strtotime($date)));
      }
      $html .= '</ul>';
    }
    elseif ($this->displaytype == 'day') {
      $html .= '<ul class="day-event-list">';
      $html .= '<li>' . $this->calendar_cell(ECWD::ecwd_date('d', strtotime($this->year . '-' . $this->month . '-' . $this->day)), 'day-with-date', ECWD::ecwd_date('Y-n-j', strtotime($this->year . '-' . $this->month . '-' . $this->day))) . '</li>';
      $html .= '</ul>';
    }
    elseif ($this->displaytype == '4day') {
      $html .= '<ul class="day4-event-list">';
      $days = $this->range4Days($this->year . '-' . $this->month . '-' . $this->day);
      $date = $days['start'];
      while (strtotime($date) <= strtotime($days['end'])) {
        $html .= '<li>' . $this->calendar_cell(ECWD::ecwd_date('d', strtotime($date)), 'day-with-date', ECWD::ecwd_date('Y-n-j', strtotime($date))) . '</li>';
        $date = ECWD::ecwd_date("Y-m-d", strtotime("+1 day", strtotime($date)));
      }
      $html .= '</ul>';
    }
    else { // event list and map
      if ($this->displaytype == 'map') {
        $map_events = array();
        if (count($this->events) > 0) {
          $i = 0;
          foreach ($this->events as $j => $events) {
            foreach ($events as $t => $event) {
              $all_day_event = (isset($event['all_day_event']) && $event['all_day_event'] == 1) ? true : false;
              if (strtotime($event['from']) >= strtotime($this->year . '-' . $this->month . '-1')) {
                $this->json_ld .= $this->set_json_ld($event, $this->displaytype);
                if ($event['latlong'] != '' && !empty($event['metas']['ecwd_event_venue'])) {
                  if ($event['from'] != '') {
                    $map_events[$i]['from'] = ECWD::ecwd_date($this->dateformat, strtotime($event['from']));
                  }
                  if ($event['to'] != '' && $event['from'] != $event['to']) {
                    $map_events[$i]['to'] = ECWD::ecwd_date($this->dateformat, strtotime($event['to']));
                  }
                  if ($all_day_event) {
                    $map_events[$i]['starttime'] = __('All day', 'event-calendar-wd');
                  } else {
                    if ($event['starttime'] != '') {
                      $map_events[$i]['starttime'] = ECWD::ecwd_date($this->timeformat, strtotime($event['starttime']));
                    }
                    if ($event['endtime'] != '') {
                      $map_events[$i]['endtime'] = ECWD::ecwd_date($this->timeformat, strtotime($event['endtime']));
                    }
                  }
                  $gmap_zoom = (isset($ecwd_options['gmap_zoom'])) ? $ecwd_options['gmap_zoom'] : 17;
                  $map_events[$i]['zoom'] = $gmap_zoom;

                  $url_for_google_map = '';
                  if ($event['location'] != '') {
                    $map_events[$i]['location'] = $event['location'];

                    $url_for_google_map .= 'https://www.google.com/maps/place/';
                    $url_for_google_map .= urlencode($event['location']) . '/';
                    $url_for_google_map .= '@' . $event['latlong'];
                  }
                  $map_events[$i]['google_map_url'] = $url_for_google_map;

                  if ($event['link'] != '') {
                    $map_events[$i]['link'] = $event['link'];
                  }
                  $map_events[$i]['latlong'] = explode(',', $event['latlong']);

                  $map_events[$i]['infow'] = '<div class="ecwd_map_event">';
                  if ($event['permalink']) {
                    $map_events[$i]['infow'] .= '<a href="' . $event['permalink'] . '" ' . $this->eventlinktarget;
                    if (isset($event['color']) && $event['color'] !== '') {
                      $map_events[$i]['infow'] .= ' style= "color:' . $event['color'] . '"';
                    }
                    $map_events[$i]['infow'] .= '>' . $event['title'] . '</a>';
                  } else {
                    $map_events[$i]['infow'] .= '<span class="title"';
                    if (isset($event['color']) && $event['color'] !== '') {
                      $map_events[$i]['infow'] .= ' style= "color:' . esc_attr($event['color']) . '"';
                    }
                    $map_events[$i]['infow'] . '>' . $event['title'] . '<span>';
                  }
                  $map_events[$i]['infow'] .= '<span class="date">';
                  if ($all_day_event) {
                    $map_events[$i]['infow'] .= ECWD::ecwd_date($this->dateformat, strtotime($event['from'])) . ' - ' . ((strtotime($event['to']) != '' && strtotime($event['from']) != strtotime($event['to'])) ? ECWD::ecwd_date($this->dateformat, strtotime($event['to'])) : "") . ' ' . __('All day', 'event-calendar-wd');
                  } else {
                    if ($event['to'] != '' && $event['from'] != $event['to']) {
                      $map_events[$i]['infow'] .= ECWD::ecwd_date($this->dateformat, strtotime($event['from'])) . ' ' . ECWD::ecwd_date($this->timeformat, strtotime($event['starttime']));
                      $map_events[$i]['infow'] .= ' - ';
                      $map_events[$i]['infow'] .= ECWD::ecwd_date($this->dateformat, strtotime($event['to'])) . ' ' . ECWD::ecwd_date($this->timeformat, strtotime($event['endtime']));
                    } else {
                      $map_events[$i]['infow'] .= ECWD::ecwd_date($this->dateformat, strtotime($event['from'])) . ' ' . ECWD::ecwd_date($this->timeformat, strtotime($event['starttime']));
                    }
                  }
                  $map_events[$i]['infow'] .= '</span>';
									
                  $map_events[$i]['infow'] .= ECWD::get_time_zone( $all_day_event );

                  $map_events[$i]['infow'] .= '<span class="location">' . $event['location'] . '</span>';
                  $map_events[$i]['infow'] .= '</div>';
                  $i++;
                }
              }
            }
          }
          if(empty($map_events)){
            $html .= '<div class="event-main-content no-events">' . __('No events', 'event-calendar-wd') . '</div>';
          }else {
            $markers = json_encode($map_events);

            $html .= '<div class="ecwd_map_div" style="width: 100%; height: 100%; min-height: 300px;" ></div>';
            $html .= '<textarea class="ecwd_markers" class="hidden" style="display: none;">' . $markers . '</textarea>';
          }
        } else {
          $html .= '<div class="event-main-content no-events">' . __('No events', 'event-calendar-wd') . '</div>';
        }
      }
      elseif ($this->displaytype == 'posterboard') {
        global $ecwd_options;
        $posterboard_post_class = (isset($ecwd_options['posterboard_fixed_height']) && $ecwd_options['posterboard_fixed_height'] == '1') ? " posterboard_fixed_height" : "";
        $html .= '<div  class="ecwd-poster-board">';
        if ($this->events) {
          foreach ($this->events as $i => $events) {
            foreach ($events as $i => $event) {
              if (!isset($event['color'])) {
                $event['color'] = '#ffffff';
              }
              if (!isset($event['terms']['ecwd_taxonomy_image'])) {
                $event['terms']['ecwd_taxonomy_image'] = '';
              }
              if (strtotime($event['from']) >= $start_date && strtotime($event['from']) <= strtotime($end_date)) {
                $this->json_ld .= $this->set_json_ld($event, $this->displaytype);
                $datenumber1 = substr(ECWD::ecwd_date('d', strtotime($event['date'])), 0, 1);
                $datenumber2 = substr(ECWD::ecwd_date('d', strtotime($event['date'])), 1, 1);
                $dates = '<span class="weekday-block"><span class="weekday">' . __(ECWD::ecwd_date('D', strtotime($event['from'])), 'event-calendar-wd') . '</span>';
                $dates .= '<span class="month">' . __(substr(ECWD::ecwd_date('F', strtotime($event['from'])) , 0, 3),"event-calendar-wd") . '</span></span> ';
                $dates .= '<span class="datenumber1">' . $datenumber1 . '</span> ';
                $dates .= '<span class="datenumber2">' . $datenumber2 . '</span> ';

                $html .= '<div class="ecwd-poster-item">';
                $image = $this->getAndReplaceFirstImage($event['details']);
                $ecwd_has_thumb = has_post_thumbnail($event['id']);
                if ($ecwd_has_thumb || $event['image']) {
                  if ($ecwd_has_thumb) {
                    $html .= '<div class="ecwd-poster-img">' . get_the_post_thumbnail($event['id'], 'thumbnail') . '</div>';
                  } else {
                    $html .= '<div class="ecwd-poster-img"><img src="' . $event['image'] . '" /></div>';
                  }
                } elseif (isset($image['image'])) {
                  $html .= '<div class="ecwd-poster-img"><img src="' . $image['image'] . '" /></div>';
                  $event['details'] = $image['content'];
                }

                $html .= '<div class="ecwd-event-content' . $posterboard_post_class . '"><div class="ecwd-event-details">';
                $html .= '<div class="ecwd-event-header"><div class="date">' . $dates . '</div>';
                if ($this->event_popup == "yes" && get_post_meta($event['id'], '', true)) {
                  $date_data = 'start-date-data="' . $event['date'] . '"';
                  $date_data .= ' end-date-data="' . $event['to'] . '"';
                  $html .= '<h2><span ' . $date_data . ' class="ecwd_open_event_popup event' . $event['id'] . '" style="color: ' . $event['color'] . '">' . $event['title'] . '</span></h2>';
                } else if ($event['permalink'] !== '') {
                  $html .= '<h2><a href="' . $event['permalink'] . '" ' . $this->eventlinktarget . ' style="color: ' . $event['color'] . '">' . $event['title'] . '</a></h2>';
                } else {
                  $html .= '<h2 style="color: ' . $event['color'] . '">' . $event['title'] . '</h2>';
                }
                $html .= '</div>';

                if (isset($event['organizers']) && count($event['organizers']) > 0) {
                  $html .= '<div class="event-organizers">';
                  foreach ($event['organizers'] as $organizer) {
                    if (isset($_GET['iframe']) && intval($_GET['iframe']) == 1) {
                      $permalink = add_query_arg('organizer', '1', $organizer['permalink']);
                    } else {
                      $html .= '<div class="event-organizer"> <a href="' . $organizer['permalink'] . '">' . $organizer['name'] . '</a></div>';
                    }
                  }
                  $html .= '</div>';
                }
                if (isset($event['location']) && $event['location'] !== '') {
                  $html .= '<div class="event-venue">
                              <span>';
                                if (isset($event['venue']['name']) && $event['venue']['name'] !== '') {
                                  $html .= '<a href="' . $event['venue']['permalink'] . '">' . $event['venue']['name'] . '</a>';
                                }
                         $html .= '</span>
                              <div class="address">' . $event['location'] . '</div>
                            </div>';
                }
                $html .= '</div>';
                $desc = $event['details'] ? $event['details'] : $this->eventemptytext;
                $desc = apply_filters('format_content', $desc);
                $html .= '<div class="post-descr"><div class="post-' . $event['id'] . '">' . $desc . '</div></div>';
                $html .= '</div></div>';
              }
            }
          }
        } else {
          $html .= '<div class="event-main-content no-events">' . __('No events', 'event-calendar-wd') . '</div>';
        }

        $html .= '</div>';
      }
      else {
        $pages = 0;
        $page = $this->page;
        $html .= '<ul class="ecwd_list">';
        $page_index = 0;
        $ev_counts = 0;
        $events_for_list = $this->events;
        foreach ($events_for_list as $date_key => $events) {
          $ev_counts += count($events);
          foreach ($events as $event) {
					  $all_day_event = (isset($event['all_day_event']) && $event['all_day_event'] == 1) ? true : false;
            if ($page_index >= $page * $this->listlimit) {
              break 1;
            }
            if ($page_index >= (($page - 1) * $this->listlimit)) {
              if ($this->displaytype == 'list') { // full event list
                $this->json_ld .= $this->set_json_ld($event, $this->displaytype);
                $image_class = '';
                $image = $this->getAndReplaceFirstImage($event['details']);
                if (!has_post_thumbnail($event['id']) && $event['image'] == "") {
                  $image_class = "ecwd-no-image";
                }
                $html .= '<li class="' . $image_class . '">';
                if (!$this->widget) {
                  $html .= '<div class="ecwd-list-date resp">' . __(ECWD::ecwd_date('d', $date_key), 'event-calendar-wd') . '</div>';
                  $event_date = (($this->list_date_format !== 'd.F.l') ? ECWD::ecwd_date($this->list_date_format, $date_key) : (ECWD::ecwd_date('d', $date_key) . '.' . __(ECWD::ecwd_date('F', $date_key), 'event-calendar-wd') . '.' . __(ECWD::ecwd_date('l', $date_key), 'event-calendar-wd')));
                  if ($this->list_date_format !== 'd.F.l') {
                    $month_name = ECWD::ecwd_date('F', strtotime($event['from']));
                    $event_date = str_replace($month_name, __($month_name, 'event-calendar-wd'), $event_date);
                  }
                  $html .= '<div class="ecwd-list-img"><div class="ecwd-list-img-container"><div class="ecwd-list-date web">' . $event_date . '</div>';
                  $html .= '<div class="ecwd-img">';
                  $ecwd_has_thumb = has_post_thumbnail($event['id']);
                  if ($ecwd_has_thumb || $event['image']) {
                    if ($ecwd_has_thumb) {
                      $html .= get_the_post_thumbnail($event['id'], "thumbnail");
                    } else {
                      $html .= '<img src="' . $event['image'] . '" />';
                    }
                  } elseif (isset($image['image'])) {
                    $html .= '<img src="' . $image['image'] . '" />';
                    $event['details'] = $image['content'];
                  }
                  $html .= '</div></div></div>';
                } else {
                  $html .= '<div class="ecwd-list-date">' . __(ECWD::ecwd_date('d', $date_key), 'event-calendar-wd') . '</div>';
                }
                $html .= '<div class="event-main-content">';
                if ($this->event_popup == "yes" && get_post_meta($event['id'], '', true)) {
                  $date_data = 'start-date-data="' . ECWD::ecwd_date('Y-m-d', strtotime($event['date'])) . '"';
                  $date_data .= ' end-date-data="' . ECWD::ecwd_date('Y-m-d', strtotime($event['to'])) . '"';
                  $html .= '<h3 class="event-title"><span '. esc_attr($date_data).' class="ecwd_open_event_popup event' . $event['id'] . '" style="color:' . esc_attr($event['color']) . ';">' . $event['title'] . '</span></h3>';
                } else if ($event['permalink'] != '') {
                  $html .= '<h3 class="event-title"><a href="' . $event['permalink'] . '" ' . $this->eventlinktarget . ' style="color:' . esc_attr($event['color']) . ';">' . $event['title'] . '</a></h3>';
                } else {
                  $html .= '<h3 class="event-title" style="color:' . esc_attr($event['color']) . ';">' . $event['title'] . '</h3>';
                }

                if (isset($event['link']) && $event['link'] !== "") {
                  $link = $event['link'];
                } else if (isset($event['metas']['ecwd_event_url'][0]) && $event['metas']['ecwd_event_url'][0] !== "") {
                  $link = $event['metas']['ecwd_event_url'][0];
                } else {
                  $link = get_post_permalink($event['id']);
                }

                $html .= '<div class="ecwd-list-date-cont">';

                if ($all_day_event) {
                  $eventtime = '<div class="ecwd-time"><span class="metainfo"> ' . __('All day', 'event-calendar-wd');
                  $eventtime .= '</span>';
                  $eventtime .= '</div>';
                } else {
                  if ($event['starttime'] != '') { // event details - hidden until clicked (full)
                    $eventtime = '<div class="ecwd-time"><span class="metainfo"> ' . ECWD::ecwd_date($this->timeformat, strtotime($event['starttime']));
                    if ($event['endtime'] != '' && strtotime($event['endtime']) != strtotime($event['starttime'])) {
                      $eventtime .= "-" . ECWD::ecwd_date($this->timeformat, strtotime($event['endtime']));
                    }
                    $eventtime .= '</span>';
                    $eventtime .= '</div>';
                  }
                }
                $html .= $eventtime;
                if ($event['from'] != '') {  // event details - hidden until clicked (full)
                  $eventdate = '<div class="ecwd-date"><span class="metainfo"> ' . ECWD::ecwd_date($this->dateformat, $date_key);
                  if ($event['to'] != '' && strtotime($event['to']) !== strtotime($event['from'])) {
                    $eventdate .= "-" . ECWD::ecwd_date($this->dateformat, strtotime($event['to']));
                  }
                  $eventdate .= '</span>';
                  $eventdate .= ECWD::get_time_zone( $all_day_event );
                  $eventdate .= '</div>';
                  $html .= $eventdate;
                }
                $html .= '</div>';
                if (isset($event['organizers']) && count($event['organizers']) > 0) {
                  $html .= '<div class="event-organizers"><div class="ecwd-org-cont">';
                  foreach ($event['organizers'] as $organizer) {
                    if (isset($_GET['iframe']) && intval($_GET['iframe']) == 1) {
                      $permalink = add_query_arg('organizer', '1', $organizer['permalink']);
                      $html .= '<div class="event-organizer"> <a href="' . $permalink . '">' . $organizer['name'] . '</a></div>';
                    } else {
                      $html .= '<div class="event-organizer"> <a href="' . $organizer['permalink'] . '">' . $organizer['name'] . '</a></div>';
                    }
                  }
                  $html .= '</div></div>';
                }

                if ($event['location'] !== '') {
                  $location = (isset($event['location'])) ? $event['location'] : "";
                  if (isset($event['venue']['name']) && $event['venue']['name'] !== "") {
                    $name = '<span><a href="' . $event['venue']['permalink'] . '">' . $event['venue']['name'] . '</a></span>';
                  } else {
                    if (isset($event['venue']['permalink'])) {
                      $name = '<span class="ecwd_hidden">' . '<a href="' . $event['venue']['permalink'] . '">' . $location . '</a>' . '</span>';
                    } else {
                      $name = '';
                    }
                  }

                  $html .= '<div class="event-venue">' .
                    '<div class="ecwd-org-cont">' . $name .
                    '<div class="address">' . $location . '</div></div></div>';
                }
                $desc = $event['details'] ? $event['details'] : $this->eventemptytext;
                $desc = apply_filters('format_content', $desc);
                $html .= '<div class="event-content">' . $desc . '</div></div>';
              }
              else { // mini event list
                $html .= '<li style="background:' . $event['color'] . ';"';
                if ($this->minilinkbase !== false) { // enable link (good for linking to full calendar)
                  $html .= ' class="event-link"';
                  if ($this->linktarget == '_blank') {
                    $html .= ' onClick="window.open(\'' . $this->minilinkbase . '\', \'_blank\')"';
                  } else {
                    $html .= ' onClick="' . $this->linktarget . '.location=\'' . $this->minilinkbase . '\'"';
                  }
                } else if ($event['link'] != '') { // enable link (links to event url)
                  $html .= ' class="event-link"';
                  if ($this->linktarget == '_blank') {
                    $html .= 'onClick="window.open(\'' . $event['link'] . '\', \'_blank\')"';
                  } else {
                    $html .= ' onClick="' . $this->linktarget . '.location=\'' . $event['link'] . '\'"';
                  }
                }
              }
              $html .= '</li>';
            }
            $page_index++;
          }
        }

        if ($this->listlimit !== false && $this->listlimit != 0) {
          $offset = ($page - 1) * $this->listlimit;
          $pages = ceil($ev_counts / $this->listlimit);
        }

        if (count($this->events) <= 0) { // if events array is empty
          $html .= '<li >';
          $html .= '<div class="event-content">' . __('No Events', 'event-calendar-wd') . '</div>';
          $html .= '</li>';
        }

        $html .= '</ul>';
///PAGINATION////
        if ($pages > 1) {
          $html .= '<div class="ecwd-pagination">';
          if ($this->page > 1) {
            $html .= '<span class="cpage-span"><a href="?date=' . $this->year . '-' . $this->month . '&t=' . $this->displaytype . '&cpage=' . ($this->page - 1) . '" class="cpage" rel="noindex, nofollow">...</a></span>';
          }
          for ($i = ($this->page - 3); $i < ($this->page + 4); $i++) {
            if ($i <= $pages and $i >= 1) {
              if ($this->page == $i) {
                $html .= '<span class="cpage-span"><span class="page">' . $i . '</span></span>';
              } else {
                $html .= '<span class="cpage-span"><a href="?date=' . $this->year . '-' . $this->month . '&t=' . $this->displaytype . '&cpage=' . $i . '" class="cpage" rel="noindex, nofollow">' . $i . '</a></span>';
              }
            }
          }
          if ($this->page < $pages) {
            $html .= '<span class="cpage-span"><a href="?date=' . $this->year . '-' . $this->month . '&t=' . $this->displaytype . '&cpage=' . ($this->page + 1) . '" class="cpage" rel="noindex, nofollow">...</a></span>';
          }
          $html .= '</div>';
        }
      }
    }
    $html .= $this->calendar_foot();

// remove tabs, line breaks, vertical tabs, null-byte
    $html = $this->stripWhitespace($html);
    if ( !empty($this->json_ld) ) {
      $this->json_ld = '[' . rtrim($this->json_ld, ',') . ']';
      $html .= '<script id="ecwd_ld_json" type="application/ld+json">' . $this->json_ld . '</script>';
    }

    return $html;
  }

// month/year select box

  public function calendar_head($content = '') {
    global $cal_ID, $ecwd_options;

    $html = '';
    $html .= '<div class="calendar-head ecwd_calendar_prev_next">';
    $html .= $this->cal_previous(); // previous month link
    $previous_year = $this->calcDate($this->year . '-' . $this->month . '-' . $this->day, '-1', 'year');
    $previous_dateparam = $previous_year['year'] . '-' . $previous_year['month'] . '-' . $previous_year['day'];

    $next_year = $this->calcDate($this->year . '-' . $this->month . '-' . $this->day, '+1', 'year');
    $next_dateparam = $next_year['year'] . '-' . $next_year['month'] . '-' . $next_year['day'];
    if ($this->displaytype == 'week') {
      $previous_dateparam = $this->getWeekFirstDayDate($previous_year['year'] . '-' . $previous_year['month'] . '-' . $previous_year['day']);
      $next_dateparam = $this->getWeekFirstDayDate($next_year['year'] . '-' . $next_year['month'] . '-' . $next_year['day']);
    }
    $year_text = $this->year;
    if ($this->displaytype == 'list') {
      if ($this->period_for_list == 12) {
        $year_text = $this->year . '-' . ($this->year + 1);
      }
    }
    $html .= '<div class="current-month">
					<a href="?date=' . $previous_dateparam . '&t=' . $this->displaytype . '" rel="noindex, nofollow"><</a>&nbsp;
					' . $year_text . '
				&nbsp;<a href="?date=' . $next_dateparam . '&t=' . $this->displaytype . '" rel="noindex, nofollow">></a>
					<div>';
    if ($this->displaytype !== 'week' && $this->displaytype !== '4day' && $this->displaytype !== 'day') {
      if ($this->displaytype == 'list' && $this->period_for_list != 12 && $this->period_for_list != 1) {
        $current_month_sec = strtotime($this->year . '-' . $this->month . '-1');
        $month_to_name = ECWD::ecwd_date("F", strtotime('+' . ($this->period_for_list - 1) . ' months', $current_month_sec));
        $html .= __($this->monthname, 'event-calendar-wd') . '-' . __($month_to_name, 'event-calendar-wd');
      } else {
        $html .= __($this->monthname, 'event-calendar-wd');
      }
    } else {
      if ($this->displaytype !== 'day') {
        if ($this->displaytype == 'week') {
          $currentDays = $this->rangeWeek($this->year . '-' . $this->month . '-' . $this->day);
        } else {
          $currentDays = $this->range4Days($this->year . '-' . $this->month . '-' . $this->day);
        }

        $html .= __($this->getMonth($currentDays['start']), 'event-calendar-wd') . ' ' . ECWD::ecwd_date('d', strtotime($currentDays['start'])) . ' - ' . __($this->getMonth($currentDays['end']), 'event-calendar-wd') . ' ' . ECWD::ecwd_date('d', strtotime($currentDays['end']));
      } else {
        $html .= __($this->monthname, 'event-calendar-wd') . ' ' . $this->day;
      }
    }


    $html .= '		</div>
				</div>';


    $html .= $this->cal_next(); // next month link

    $current_date_link = "?date=" . $this->year . "-" . $this->month . "-" . $this->day;
    $current_date_link .= "&t=" . $this->displaytype;
    $html .= '<input type="hidden" class="ecwd_current_link" value="' . $current_date_link . '" />';

    $html .= '</div>';
    $html .= $this->cal_viewmode();

    if (!in_array($this->displaytype, array(
      "list",
      "mini-list",
      'day',
      'week',
      '4day',
      'map',
      'posterboard'
    ))
    ) { // mini and full cal
      $html .= '
<table class="ecwd_calendar_container ' . $this->displaytype . ' cal_' . $this->color . '" cellpadding="0" cellspacing="0" border="0">

<tr>';
// render week number on left
      if ($this->weeknumbers == 'left') {
        $html .= '<td rowspan="2" class="week-number">&nbsp;</td>';
      }

// render week number on right
      if ($this->weeknumbers == 'right') {
        $html .= '<td rowspan="2" class="week-number">&nbsp;</td>';
      }
      $html .= '
</tr>
<tr>';
      if ($this->weeknames == 1) {
        $weekdays = $this->weekdays; // full
      } else if ($this->weeknames == 3 || $this->displaytype == 'mini') {
        $weekdays = $this->weekdayschar; // single char
      } else {
        $weekdays = $this->abbrevweekdays; // 3 char
      }
      for ($i = 0; $i < count($weekdays); $i++) {
        $di = ($i + $this->weekstartday) % 7;
        $weekday = $weekdays[$di];
        if ($i == 0) {
          $thisclass = 'normal-day-heading week-start';
        } else {
          $thisclass = 'normal-day-heading';
        }
        $html .= $this->calendar_cell(__($weekday, 'event-calendar-wd'), $thisclass); // calendar cells for full & mini
      }
      $html .= '
</tr>
';
    } else { // event list
      $html .= '<div class="ecwd_calendar_container ' . $this->displaytype . '">';

      if ($this->displaytype == 'list') {
        if ($this->listtitle != '') {
          $html .= '<h2 class="list-title">' . $this->listtitle . '</h2>';
        }
      } elseif ($this->displaytype == 'day') {
//$this->day = ;
      } elseif ($this->displaytype == 'week') {

      } elseif ($this->displaytype == '4day') {

      } elseif ($this->displaytype == 'map') {

      } elseif ($this->displaytype == 'posterboard') {

      }

//$html .= '</div>';
    }

    return $html;
  }

// calendar cells for mini and full displaytypes

  public function cal_previous() {
    global $cal_ID;
    $offset = 1;
    if ($this->displaytype == 'list') {
      $offset = $this->period_for_list;
    }
    $previous = $this->calcDate($this->year . '-' . $this->month, '-' . $offset, 'month');
    $dateparam = $previous['year'] . '-' . $previous['month'];
    $prev_date = '';
    if ($this->displaytype == 'day') {
      $previous = $this->calcDate($this->year . '-' . $this->month . '-' . $this->day, '-1', 'day');
      $dateparam = $previous['year'] . '-' . $previous['month'] . '-' . $previous['day'];
      $prev_date = $previous['day'];
    } elseif ($this->displaytype == '4day') {
      $previous = $this->calcDate($this->year . '-' . $this->month . '-' . $this->day, '-4', 'day');
      $dateparam = $previous['year'] . '-' . $previous['month'] . '-' . $previous['day'];
      $prev_date = $previous['day'];
    } elseif ($this->displaytype == 'week') {
      $previous = $this->calcDate($this->year . '-' . $this->month . '-' . $this->day, '-1', 'week');
      $dateparam = $previous['year'] . '-' . $previous['month'] . '-' . $previous['day'];
      $prev_date = $previous['day'];
    }
    $this->previousmonth = ($this->getMonth($previous['year'] . '-' . $previous['month'], $this->monthnames));
    if ($this->widget == 1) {
      $previoustext = '<span><</span>';
    } else {
      if ($this->displaytype == 'list' && $this->period_for_list > 1) {
        $previoustext = '<span><</span><span class="month-name"> ' . __($this->previousmonth, 'event-calendar-wd') . ' ' . $previous['year'] . '</span>';
      } else {
        $previoustext = '<span><</span><span class="month-name"> ' . __($this->previousmonth, 'event-calendar-wd') . ' ' . $prev_date . '</span>';
      }

    }

    $html = '<div class="previous"><a href="?date=' . $dateparam . '&t=' . $this->displaytype . '" rel="noindex, nofollow">' . $previoustext . '</a></div>';

    return $html;
  }

// calendar footer for all displaytypes

  public function calcDate($startdate, $increment, $unit) {
    if ($unit == 'month') {
      $startdate = ECWD::ecwd_date('Y-m-15', strtotime($startdate));
    }
    $date = ECWD::ecwd_date("Y-n-j", strtotime(ECWD::ecwd_date("Y-n-j", strtotime($startdate)) . " " . $increment . " " . $unit));
    $date = explode('-', $date);
    $newdate = array(
      'year' => $date[0],
      'month' => $date[1],
      'day' => $date[2]
    );

    return $newdate;
  }

  public function getWeekFirstDayDate($date) {
    if (strtotime($date) == strtotime(ECWD::ecwd_date('Y-m-d', time()))) {
      if ($this->weekstartday == 0) {
        return ECWD::ecwd_date('Y-n-j', strtotime(strtolower($this->weekdays[$this->weekstartday]) . " previous week", strtotime($date)));
      }

      return ECWD::ecwd_date('Y-n-j', strtotime(strtolower($this->weekdays[$this->weekstartday]) . " this week", strtotime($date)));
    } else {
      $first_monday_of_month = strtotime('First ' . $this->weekdays[$this->weekstartday] . ' of ' . ECWD::ecwd_date('F o', strtotime($date)));
      if (ECWD::ecwd_date('Y-n-j', $first_monday_of_month) == '1970-1-1' || ECWD::ecwd_date('Y-n-j', $first_monday_of_month) == '1969-12-31') {
        $first_monday_of_month = strtotime(ECWD::ecwd_date('Y-m-d', strtotime(ECWD::ecwd_date('F o', strtotime($date)) . ' first ' . $this->weekdays[$this->weekstartday])));
      }
      if (ECWD::ecwd_date('j', $first_monday_of_month) > 1) {
        $previous_monday = strtotime("-1 week", $first_monday_of_month);

        return ECWD::ecwd_date('Y-n-j', $previous_monday);
      } else {
        return ECWD::ecwd_date('Y-n-j', $first_monday_of_month);
      }
    }
  }

  public function getMonthDate($date) {
    if (ECWD::ecwd_date('m', strtotime($date)) === ECWD::ecwd_date('m',time())) {
      return ECWD::ecwd_date('Y-n-j', time());
    }
    if ($this->displaytype == 'week' && strtotime($date) !== strtotime(ECWD::ecwd_date('Y-m-d', time()))) {
      return ECWD::ecwd_date('Y-n-j', strtotime("+1 week", strtotime($date)));
    } elseif ($this->displaytype == '4day' && strtotime($date) !== strtotime(ECWD::ecwd_date('Y-m-d',time()))) {
      return ECWD::ecwd_date('Y-n-j', strtotime("+3 day", strtotime($date)));
    }

    return $date;
  }

  public function getDayDate($date) {
    if (strtotime($date) == strtotime(ECWD::ecwd_date('Y-m-d', time()))) {
      return $date;
    } else {
      if ($this->displaytype == 'full' || $this->displaytype == 'month' || $this->displaytype == 'mini' || $this->displaytype == 'map' || $this->displaytype == 'posterboard' || $this->displaytype == 'list') {
        return ECWD::ecwd_date('Y-n-j', strtotime(ECWD::ecwd_date('Y-m-1'), strtotime($date)));
      } elseif ($this->displaytype == 'week') {
        return ECWD::ecwd_date('Y-n-j', strtotime(strtolower($this->weekdays[$this->weekstartday]) . " this week", strtotime($date)));
      } else {
        return $date;
      }
    }
  }

// add an event to the events array

  public function getMonth($date, $type = 1) {
    $date = ECWD::ecwd_date('Y-n-j', strtotime($date));
    $date_parts = explode('-', $date);
    $jd = cal_to_jd(CAL_GREGORIAN, $date_parts[1], $date_parts[2], $date_parts[0]);

    return __(jdmonthname($jd, $type));
  }

// pulls everything together and returns the calendar for all displaytypes

  public function cal_next() {
    global $cal_ID;
    $html = '';
    $offset = 1;
    if ($this->displaytype == 'list') {
      $offset = $this->period_for_list;
    }
    $next = $this->calcDate($this->year . '-' . $this->month . '-' . $this->day, '+' . $offset, 'month');
    $next_date = '';
    $dateparam = $next['year'] . '-' . $next['month'];
    if ($this->displaytype == 'day') {
      $next = $this->calcDate($this->year . '-' . $this->month . '-' . $this->day, '+1', 'day');
      $dateparam = $next['year'] . '-' . $next['month'] . '-' . $next['day'];
      $next_date = $next['day'];
    } elseif ($this->displaytype == '4day') {
      $next = $this->calcDate($this->year . '-' . $this->month . '-' . $this->day, '+4', 'day');
      $dateparam = $next['year'] . '-' . $next['month'] . '-' . $next['day'];
      $next_date = $next['day'];
    } elseif ($this->displaytype == 'week') {
      $next = $this->calcDate($this->year . '-' . $this->month . '-' . $this->day, '+1', 'week');
      $dateparam = $next['year'] . '-' . $next['month'] . '-' . $next['day'];
      $next_date = $next['day'];
    }
    $this->nextmonth = ($this->getMonth($next['year'] . '-' . $next['month'], $this->monthnames));

    if ($this->widget == 1) {
      $nexttext = '<span>></span>';
    } else {
      $divider = $this->monthselector === false ? '&nbsp;|&nbsp;' : '';
      if ($this->displaytype == 'list' && $this->period_for_list > 1) {
        $nexttext = '<span class="month-name">' . $next_date . ' ' . $divider . __($this->nextmonth, 'event-calendar-wd') . ' ' . $next['year'] . ' </span><span>></span>';
      } else {
        $nexttext = '<span class="month-name">' . $next_date . ' ' . $divider . __($this->nextmonth, 'event-calendar-wd') . ' </span><span>></span>';
      }
    }
    $html = '<div class="next"><a href="?date=' . $dateparam . '&t=' . $this->displaytype . '" rel="noindex, nofollow">' . $nexttext . '</a></div>';

    return $html;
  }

//--------------------------------------------------------------------------------------------
// Helper Functions
//--------------------------------------------------------------------------------------------
//

  public function cal_viewmode() {
    $monthType = ($this->widget == 1) ? 'mini' : 'full';

    if ($this->widget == 1) {
      $html = ' <div class="ecwd_calendar_view_dropdown cal_tabs_' . $this->color . '" ><a class="ecwd-dropdown-toggle" data-toggle="ecwd-dropdown">';
      $html .= $this->displaysName[$this->displaytype]['name'];
      $html .= '</a>';
                $widgetDisplays = array(
                    'mini',
                    'list',
                    'week',
                    'day'
                );
      $html .= '<div class="ecwd-dropdown-menu">';
      foreach ($widgetDisplays as $display) {
        if ($display !== 'none' && isset($this->displaysName[$display])) {
          $html .= '<div class="type">' . '<a href="?date=' . $this->displaysName[$display]['date'] . '&t=' . $display . '" rel="noindex, nofollow">' . $this->displaysName[$display]['name'] . '</a>' . '</div>';
        }
      }

      $html .= '</div></div>';
    } else {
      $html = ' <div class="ecwd_calendar_view_tabs cal_tabs_' . $this->color . '" >';
      $html .= '<div class="filter-container">
                        <div class="filter-arrow-left">&laquo;</div>
                        <div class="filter-arrow-right">&raquo;</div>';


      if (count($this->displays) > 1) {
        $html .= '<ul>';
        foreach ($this->displays as $display) {
          if ($display !== 'none' && isset($this->displaysName[$display])) {
            $html .= '<li class="type';
            if ($this->displaytype == $display) {
              $html .= ' ecwd-selected-mode';
              $html .= '">' . '<a>' . $this->displaysName[$display]['name'] . '</a>' . '</li>';
            } else {
              $html .= '">' . '<a href="?date=' . $this->displaysName[$display]['date'] . '&t=' . $display . '" rel="noindex, nofollow">' . $this->displaysName[$display]['name'] . '</a>' . '</li>';
            }
          }
        }
        $html .= '</ul>';
      }
      $html .= '</div>';
      if ($this->event_search == 'yes') {
        $svalue = '';
        if (isset($this->search_params['query']) && $this->search_params['query']) {
          $svalue = $this->search_params['query'];
        }
        $html .= '<div class="ecwd-search">
                        <input class="ecwd-search" name="search" type="text" value="' . $svalue . '">'
          . '<button class="btn btn-default ecwd-search-submit"><span class="glyphicon glyphicon-search"></span><i class="fa fa-search"></i></button>
                </div>';
      }
      $html .= '</div>';
    }

    return $html;
  }

// returns month from passed date (string), $type: 0=number,1=full(January,February,etc),2=abbreviation(Jan,Feb,etc)

  public function calendar_cell($day, $class, $date = '', $style = '') {
    global $cal_ID, $ecwd_options;
    $addclass = '';
    if (strpos($class, 'normal-day-heading') !== false) {
      $tag = 'th';
    } else {
      $tag = 'td';
    }
    $month_view_events_count = (isset($ecwd_options['event_count_per_cell'])) ? intval($ecwd_options['event_count_per_cell']) : 3;
    if ($day != '') {
      $bgColor = '';
      $cellevents = array();
      if (!$date && is_int($day)) {
        $date = $this->year . '-' . $this->month . '-' . $day;
      }
      if (isset($this->events[strtotime($date)])) {
        $all_events = array_reverse($this->events[strtotime($date)]);
      } else {
        $all_events = array();
      }
      if (is_array($all_events) && $date) { // events array populated from addEvent()
        foreach ($all_events as $event) {
          $color = $event['color'];
          $title = $event['title'];
          $link = ($event['link'] == "" && isset($event['metas']['ecwd_event_url'][0])) ? $event['metas']['ecwd_event_url'][0] : $event['link'];
          $eventdate = $event['date'];
          $from = strtotime($event['from']);
          $to = strtotime($event['to']);
          $starttime = $event['starttime'];
          $endtime = $event['endtime'];
          $details = $event['details'];
          $location = $event['location'];
          $venue = $event['venue'];
          $organizers = $event['organizers'];
          $terms = $event['terms'];
          $all_day_event = $event['all_day_event'];
          $permalink = (empty($event['permalink']) && !empty($event['second_link'])) ? $event['second_link'] : $event['permalink'];
          $image = $event['image'];
          $latlong = $event['latlong'];
          $id = $event['id'];
          if ($date == ECWD::ecwd_date('Y-n-j', strtotime($eventdate))) {
            $cellevents[] = array(
              'color' => $color,
              'title' => $title,
              'link' => ECWD::add_http($link),
              'date' => $eventdate,
              'from' => $from,
              'to' => $to,
              'id' => $id,
              'starttime' => $starttime,
              'endtime' => $endtime,
              'details' => $details,
              'location' => $location,
              'all_day_event' => $all_day_event,
              'latlong' => $latlong,
              'terms' => $terms,
              'venue' => $venue,
              'organizers' => $organizers,
              'permalink' => $permalink,
              'image' => $image,
            );
            $this->json_ld .= $this->set_json_ld( $cellevents[0] );
          }
        }
      }
// sort by starttime for the cell
      if (count($cellevents) > 0) {
        $cellevents = $this->arraySort($cellevents, 'starttime');
      }

      if ($date == ECWD::ecwd_date('Y-n-j')) {
        $addclass .= ' current-day'; // if processing the current day
      }

      if (in_array($this->getDay($date), array(
        'Saturday',
        'Sunday'
      ))) {
        $addclass .= ' weekend'; // if a weekend
      }

      if ($this->getDay($date, 0) == $this->weekstartday) {
        $addclass .= ' week-start'; // if the weekstartday
      }

      if ($this->arraySearch($date, $cellevents) !== false) {
        $addclass .= ' has-events'; // if the date has events
      } else if (strpos($class, 'normal-day-heading') === false) {
        $addclass .= ' no-events'; // no events
      }

      $combinedclass = $class . $addclass; // combine all classes

      $html = '<' . $tag . ' class="' . $combinedclass . '" data-date="' . $date . '"';

// check/set links for mini calendar
      if ($this->minilinkbase != '' && $this->displaytype == 'mini') {
        if ($this->linktarget == '_blank') {
          $html .= ' onClick="window.open(\'' . $this->minilinkbase . '?date=' . $date . '\', \'_blank\')"';
        } else {
          $html .= ' onClick="' . $this->linktarget . '.location=\'' . $this->minilinkbase . '?date=' . $date . '\'"';
        }
      }
    }
    if ($style) {
      $html .= ' style="' . $style . '"';
    }
    if ($this->displaytype == 'mini') {
      $content = $day;
    } elseif ($this->widget && $this->displaytype != 'mini') {
      $content = '<div class="ecwd-week-date">' . ECWD::ecwd_date('d', strtotime($date)) . '</div>';
    } elseif (($this->displaytype == 'week' || $this->displaytype == 'day' || $this->displaytype == '4day') && !$this->widget) {
//here
      $event_date = (($this->list_date_format !== 'd.F.l') ? ECWD::ecwd_date($this->list_date_format, strtotime($date)) : (ECWD::ecwd_date('d', strtotime($date)) . '.' . __(ECWD::ecwd_date('F', strtotime($date)), 'event-calendar-wd') . '.' . __(ECWD::ecwd_date('l', strtotime($date)), 'event-calendar-wd')));
      if ($this->list_date_format !== 'd.F.l') {
        $month_name = ECWD::ecwd_date('F', strtotime($date));
        $event_date = str_replace($month_name, __($month_name, 'event-calendar-wd'), $event_date);
      }
      $content = '<div class="ecwd-week-date resp">' . __(ECWD::ecwd_date('d', strtotime($date)), 'event-calendar-wd') . '</div><div class="ecwd-week-date web"">' . $event_date . '</div>';
    } else {
      $content = '<div class="day-number">' . $day . '</div>'; // day number or prev/next month cell content
    }
    if (count($cellevents) > 0) {
      $content .= '<ul class="events">';
      $eventcontent = '';
      foreach ($cellevents as $i => $cellevent) {
	  $all_day_event = (isset($cellevent['all_day_event']) && $cellevent['all_day_event'] == 1) ? true : false;

        $li_class = '';
        if ($i > ($month_view_events_count-1) && $this->displaytype !== 'mini') {
          $li_class = 'inmore';
        }
        $eventcontent .= '<li style="';
        if (is_array($cellevent['terms'])) {
          if (isset($cellevent['color']) && $cellevent['color'] !== '') {
            $eventcontent .= 'background-color: ' . $cellevent['color'] . '; ';
          }
        }
        $eventcontent .= '" class="' . $li_class . ' ' . $i;
        if (isset($cellevent['terms']['ecwd_taxonomy_image']) && $cellevent['terms']['ecwd_taxonomy_image'] == '') {
          $eventcontent .= ' no-cat-image ';
        }
        $eventcontent .= '">';
        if ($this->displaytype != 'mini') {
          if (is_array($cellevent['terms'])) {
            foreach ($cellevent['terms'] as $terms) {
              if (isset($terms['ecwd_taxonomy_image']) && $terms['ecwd_taxonomy_image'] != '') {
                $eventcontent .= '<img class="ecwd-event-cat-icon" src="' . $terms['ecwd_taxonomy_image'] . '" />';
                break;
                              }
                            }
                        }
          if ($this->event_popup == "yes" && get_post_meta($event['id'], '', true)) {
            $date_data = 'start-date-data="' . ECWD::ecwd_date("Y-m-d", strtotime($cellevent['date'])) . '"';
            $date_data .= ' end-date-data="' . ECWD::ecwd_date("Y-m-d", $cellevent['to']).'"';
            $eventcontent .= '<span '.$date_data.' class="ecwd_open_event_popup event' . $cellevent['id'] . '">' . $cellevent['title'] . '</span>';
          } elseif ($cellevent['permalink']) {
            if (isset($_GET['iframe']) && intval($_GET['iframe']) == 1) {
              $permalink = add_query_arg('iframe', '1', $cellevent['permalink']);
              $eventcontent .= '<a href="' . $permalink . '" ' . $this->eventlinktarget . '><span>' . $cellevent['title'] . '</span></a>';
            } else {
              $eventcontent .= '<a href="' . $cellevent['permalink'] . '" ' . $this->eventlinktarget . '><span>' . $cellevent['title'] . '</span></a>';
            }
          } else {
            $eventcontent .= '<span>' . $cellevent['title'] . '</span>';
          }
        }
        $this->seted_days[$cellevent['id']] = $date;
        $ecwd_settings_general = get_option("ecwd_settings_events");
        $show_events_detail_hover = true;
        if ($ecwd_settings_general && isset($ecwd_settings_general["show_events_detail"])) {
          $show_events_detail = $ecwd_settings_general["show_events_detail"];
          if (intval($show_events_detail) === 0) {
            $show_events_detail_hover = false;
          }
        }
        if ($show_events_detail_hover) {
          echo "<input type='hidden' class='show_event_hover_info'>";
        }
        $eventcontent .= '<div class="event-details-container"><div class="ecwd-event-arrow"></div><div class="event-details">';
        if ($cellevent['title'] != '') {
          $eventcontent .= '<div class="event-details-title">';
          if (isset($cellevent['color']) && $cellevent['color'] !== '') {
            $eventcontent .= ' <span class="event-metalabel" style="background:' . esc_attr($cellevent['color']) . '"></span>
                                         <h5 style="color:' . esc_attr($cellevent['color']) . '">';
            if ($this->event_popup == "yes" && get_post_meta($event['id'], '', true)) {
              $date_data = 'start-date-data="' . $event['date'] . '"';
              $date_data .= ' end-date-data="' . $event['to'] . '"';
              $eventcontent .= '<span ' . esc_attr($date_data) . ' class="ecwd_open_event_popup event' . $cellevent['id'] . '">' . $cellevent['title'] . '</span>';
            } else if (isset($cellevent['permalink']) && $cellevent['permalink'] !== '') {
              if (isset($_GET['iframe']) && intval($_GET['iframe']) == 1) {
                $permalink = add_query_arg('iframe', '1', $cellevent['permalink']);
                $eventcontent .= '<a href="' . $permalink . '" ' . $this->eventlinktarget . ' style="color: ' . esc_attr($cellevent['color']) . '">' . $cellevent['title'] . '</a>';
              } else {
                $eventcontent .= '<a href="' . $cellevent['permalink'] . '" ' . $this->eventlinktarget . ' style="color: ' . esc_attr($cellevent['color']) . '">' . $cellevent['title'] . '</a>';
              }
            } else {
              $eventcontent .= $cellevent['title'];
            }
            $eventcontent .= '</h5>
                                         ';
          } else {
            $eventcontent .= ' <span class="event-metalabel"></span><h5>';
            if ($this->event_popup == "yes" && get_post_meta($event['id'], '', true)) {
              $date_data = 'start-date-data="' . $event['date'] . '"';
              $date_data .= ' end-date-data="' . $event['to'] . '"';
              $eventcontent .= '<span ' . esc_attr($date_data) . ' class="ecwd_open_event_popup event' . $cellevent['id'] . '">' . $cellevent['title'] . '</span>';
            } else if (isset($cellevent['permalink']) && $cellevent['permalink'] !== '') {
              if (isset($_GET['iframe']) && intval($_GET['iframe']) == 1) {
                $permalink = add_query_arg('iframe', '1', $cellevent['permalink']);
                $eventcontent .= '<a href="' . $permalink . '" ' . $this->eventlinktarget . '>' . $cellevent['title'] . '</a>';
              } else {
                $eventcontent .= '<a href="' . $cellevent['permalink'] . '" ' . $this->eventlinktarget . '>' . $cellevent['title'] . '</a>';
              }
            } else {
              $eventcontent .= $cellevent['title'];
            }
            $eventcontent .= '</h5>';
          }
          $eventcontent .= ' </div>';
        }
        if ($all_day_event) {
          $eventtime = '<div class="ecwd-time"><span class="metainfo">' . __('All day', 'event-calendar-wd') . '</span></div>';
          $eventcontent .= $eventtime;
        } else {
          if ($cellevent['starttime'] != '') { // event details - hidden until clicked (full)
            $eventtime = '<div class="ecwd-time">
                  <span class="metainfo">' . ECWD::ecwd_date($this->timeformat, strtotime($cellevent['starttime']));
                  if ($cellevent['endtime'] != '' && strtotime($cellevent['endtime']) !== strtotime($cellevent['starttime'])) {
                    $eventtime .= "-" . ECWD::ecwd_date($this->timeformat, strtotime($cellevent['endtime']));
                  }
            $eventtime .= '</span>';
            $eventtime .= '</div>';
            $eventcontent .= $eventtime;
          }
        }
        if ($cellevent['from'] != '') { // event details - hidden until clicked (full)
          $eventdate = '<div class="ecwd-date"><span class="metainfo"> ' . ECWD::ecwd_date($this->dateformat, strtotime($cellevent['date']));
          if ($cellevent['to'] != '' && $cellevent['to'] != $cellevent['from']) {
            $eventdate .= "-" . ECWD::ecwd_date($this->dateformat, $cellevent['to']);
          }
          $eventdate .= '</span>';
          $eventdate .= ECWD::get_time_zone( $all_day_event );
          $eventdate .= '</div>';
          $eventcontent .= $eventdate;
        }
        if (isset($cellevent['organizers']) && count($cellevent['organizers']) > 0) {
          $eventcontent .= '<div class="event-organizers">';
          foreach ($cellevent['organizers'] as $organizer) {
            if (isset($_GET['iframe']) && intval($_GET['iframe']) == 1) {
              $permalink = add_query_arg('organizer', '1', $organizer['permalink']);
              $eventcontent .= '<div class="event-organizer"> <a href="' . $permalink . '">' . $organizer['name'] . '</a></div>';
            } else {
              $eventcontent .= '<div class="event-organizer"> <a href="' . $organizer['permalink'] . '">' . $organizer['name'] . '</a></div>';
            }
          }
          $eventcontent .= '</div>';
        }
        if ($cellevent['location'] !== '') {
          $eventcontent .= '<div class="event-venue">';
          if (isset($cellevent['venue']['name'])) {
            if (isset($_GET['iframe']) && intval($_GET['iframe']) == 1) {
              $permalink = add_query_arg('venue', '1', $cellevent['venue']['permalink']);
              $eventcontent .= '<div class="ecwd-venue"><span><a href="' . $permalink . '">' . $cellevent['venue']['name'] . '</a></span></div>';
            } else {
              $eventcontent .= '<div class="ecwd-venue" ><span><a href="' . $cellevent['venue']['permalink'] . '">' . $cellevent['venue']['name'] . '</a></span></div>';
            }
          }
          if (isset($cellevent['location']) && $cellevent['location'] != '') {
            $eventcontent .= '<div class="ecwd-location"><span>' . esc_html($cellevent['location']) . '</span></div>';
          }
          $eventcontent .= '</div>';
        }

        if (isset($cellevent['link']) && $cellevent['link'] != '') {
          $eventcontent .= '<div class="ecwd-link"><a href="' . esc_url($cellevent['link']) . '">' . esc_html($cellevent['link']) . '</a></div>';
        }

	      $cellevent['details'] = ($cellevent['details'] == '') ? $this->eventemptytext : $cellevent['details'];
        $image = $this->getAndReplaceFirstImage($cellevent['details']);

        $ecwd_has_thumb = has_post_thumbnail($cellevent['id']);
        if ($cellevent['details'] != '' || $ecwd_has_thumb || $cellevent['image']) {
          $eventcontent .= '<div  class="ecwd-detalis">';
          if ($ecwd_has_thumb || $cellevent['image']) {
            if ($ecwd_has_thumb) {
              $eventcontent .= get_the_post_thumbnail($cellevent['id'], 'thumbnail');
            } else {
              $eventcontent .= '<img src="' . $cellevent['image'] . '" />';
            }
          } elseif (isset($image['image'])) {
            $eventcontent .= '<img src="' . $image['image'] . '" />';
            $cellevent['details'] = $image['content'];
          }
          $desc = $cellevent['details'] ? $cellevent['details'] : $this->eventemptytext;
          $desc = apply_filters('format_content', $desc);
          $eventcontent .= $desc . '</div>';
        }
        $eventcontent .= '</div><div class="ecwd-event-arrow-right"></div>';
        $eventcontent .= '</li> ';
      }

      $content .= apply_filters('format_content', $eventcontent);

      if ($i > ($month_view_events_count-1) && $this->displaytype !== 'mini') {
        $content .= '<li class="ecwd-calendar-more-event">
                    <span class="ecwd-calendar-event-add">
                       <span class="more_events_link">' . __('More events', 'event-calendar-wd') . '</span>
                    </span>
                    <div class="ecwd-more-events-container">
                      <ul class="events more_events">' . $eventcontent . '</ul>
                      <input type="hidden" class="ecwd-more-event-title" value="' . ECWD::ecwd_date($this->dateformat, strtotime($date)) . '" />
                    </div>
                 </li>';
      }

      $content .= '</ul>';


      if ($this->displaytype == 'week' || $this->displaytype == 'day' || $this->displaytype == '4day') {
        if (count($cellevents) > 0) {
          $html = '';
          if (!$this->widget) {
            $event_date = (($this->list_date_format !== 'd.F.l') ? ECWD::ecwd_date($this->list_date_format, strtotime($date)) : (ECWD::ecwd_date('d', strtotime($date)) . '.' . __(ECWD::ecwd_date('F', strtotime($date)), 'event-calendar-wd') . '.' . __(ECWD::ecwd_date('l', strtotime($date)), 'event-calendar-wd')));
            if ($this->list_date_format !== 'd.F.l') {
              $month_name = ECWD::ecwd_date('F', strtotime($date));
              $event_date = str_replace($month_name, __($month_name, 'event-calendar-wd'), $event_date);
            }


            $html = '<div class="ecwd-week-date resp"  style="background:#' . $this->eventlistbg . '">' . ECWD::ecwd_date('d', strtotime($date)) . '</div><div class="ecwd-week-date web"">' . $event_date . '</div>';

          } else {
            $html = '<div class="ecwd-week-date">' . ECWD::ecwd_date('d', strtotime($date)) . '</div>';
          }
          $html .= '<div class="event-main-content">';

          foreach ($cellevents as $cellevent) {
            $image_class = '';
            $image = $this->getAndReplaceFirstImage($cellevent['details']);
            $ecwd_has_thumb = has_post_thumbnail($cellevent['id']);
            if (!$ecwd_has_thumb && $cellevent['image'] == "") {
              $image_class = "ecwd-no-image";
            }
            $html .= '<div class="event-container ' . $image_class . '">';
            if (!$this->widget) {
              $html .= '<div class="ecwd-list-img"><div class="ecwd-list-img-container">';
              $html .= '<div class="ecwd-img">';
              if ($ecwd_has_thumb || $cellevent['image']) {

                if ($ecwd_has_thumb) {
                  $html .= get_the_post_thumbnail($cellevent['id'],'thumb');
                } else {
                  $html .= '<img src="' . $cellevent['image'] . '" />';
                }
              } elseif (isset($image['image'])) {
                $html .= '<img src="' . $image['image'] . '" />';
                $cellevent['details'] = $image['content'];
              }
              $html .= '</div></div></div>';
            }
            if ($this->event_popup == "yes" && get_post_meta($cellevent['id'], '', true)) {
              $date_data = 'start-date-data="' . ECWD::ecwd_date("Y-m-d", strtotime($cellevent['date'])) . '"';
              $date_data .= ' end-date-data="' . ECWD::ecwd_date("Y-m-d", $cellevent['to']).'"';
              $html .= '<h3 class="event-title"><span '.$date_data.' class="ecwd_open_event_popup event' . $cellevent['id'] . '"';
              if (isset($cellevent['color']) && $cellevent['color'] !== '') {
                $html .= ' style="color:' . $cellevent['color'] . ';"';
              }
              $html .= '>' . $cellevent['title'] . '</span></h3>';
            } else if ($cellevent['permalink'] != '') {
              $html .= '<h3 class="event-title"> <a href="' . $cellevent['permalink'] . '" ' . $this->eventlinktarget;
              if (isset($cellevent['color']) && $cellevent['color'] !== '') {
                $html .= ' style="color:' . $cellevent['color'] . ';"';
              }
              $html .= '>' . $cellevent['title'] . '</a></h3>';
            } else {
              $html .= '<h3 class="event-title"';
              if (isset($cellevent['color']) && $cellevent['color'] !== '') {
                $html .= 'style="color:' . $cellevent['color'] . ';"';
              }
              $html .= '>' . $cellevent['title'] . '</h3>';
            }
            $html .= '<div class="ecwd-list-date-cont">';
            if ($all_day_event) {
              $eventtime = '<div class="ecwd-time"><span class="metainfo event-time"> ' . __('All day', 'event-calendar-wd') .'</span></div>';
              $html .= $eventtime;
            } else {
              if ($cellevent['starttime'] != '') { // event details - hidden until clicked (full)
                $eventtime = '<div class="ecwd-time"><span class="metainfo event-time"> ' . ECWD::ecwd_date($this->timeformat, strtotime($cellevent['starttime']));
                if ($cellevent['endtime'] != '' && $cellevent['endtime'] != $cellevent['starttime']) {
                  $eventtime .= "-" . ECWD::ecwd_date($this->timeformat, strtotime($cellevent['endtime']));
                }
                $eventtime .= '</span>';
                $eventtime .= '</div>';
                $html .= $eventtime;
              }
            }
            if ($cellevent['from'] != '') {
              $eventdate = '<div class="ecwd-date"><span class="metainfo"> ' . ECWD::ecwd_date($this->dateformat, strtotime($cellevent['date']));
              if ($cellevent['to'] != '' && $cellevent['to'] != $cellevent['from']) {
                $eventdate .= "-" . ECWD::ecwd_date($this->dateformat, $cellevent['to']);
              }
              $eventdate .= '</span>';
              $eventdate .= ECWD::get_time_zone( $all_day_event );
              $eventdate .= '</div>';
              $html .= $eventdate;
            }
            $html .= '</div>';
            if (isset($cellevent['organizers']) && count($cellevent['organizers']) > 0) {
              $html .= '<div class="event-organizers">';
              foreach ($cellevent['organizers'] as $organizer) {
                if (isset($_GET['iframe']) && intval($_GET['iframe']) == 1) {
                  $permalink = add_query_arg('organizer', '1', $organizer['permalink']);
                  $html .= '<div class="event-organizer"><a href="' . $permalink . '">' . $organizer['name'] . '</a></div>';
                } else {
                  $html .= '<div class="event-organizer"> <a href="' . $organizer['permalink'] . '">' . $organizer['name'] . '</a></div>';
                }
              }
              $html .= '</div>';
            }
            if (isset($cellevent['location']) && $cellevent['location'] !== '') {
              $html .= '<div class="event-venue">';
	              if (isset($cellevent['venue']['name']) && $cellevent['venue']['name'] !== '') {
	                $html .= '<span><a href="' . $cellevent['venue']['permalink'] . '">' . $cellevent['venue']['name'] . '</a></span>';
	              }
                $html .= '<div class="address">' . $cellevent['location'] . '</div>
              </div>';
            }



            $desc = $cellevent['details'] ? $cellevent['details'] : $this->eventemptytext;
            $desc = apply_filters('format_content', $desc);
            $html .= '<div class="event-content">' . $desc . '</div></div>';
          }
          $html .= '</div>';

          return $html;
        }
      }
    } else {
      if ($this->displaytype == 'week' || $this->displaytype == 'day' || $this->displaytype == '4day') {
        $content .= '<div class="event-main-content no-events">' . __('No events', 'event-calendar-wd') . '</div>';
      }
    }

    $html .= '>' . $content . '</' . $tag . '>';

    return $html;
  }

  public function arraySort($a, $subkey) {
    foreach ($a as $k => $v) {
      $b[$k] = strtolower($v[$subkey]);
    }
    asort($b);
    foreach ($b as $key => $val) {
      $c[] = $a[$key];
    }
    return $c;
  }

// add/subtract day,week,month,days from startdate

  public function getDay($date, $type = 1) {
    $date = ECWD::ecwd_date('Y-n-j', strtotime($date));
    $date_parts = explode('-', $date);
    $jd = cal_to_jd(CAL_GREGORIAN, $date_parts[1], $date_parts[2], $date_parts[0]);

    return jddayofweek($jd, $type);
  }

// recursive array search returns the key of occurance (int) or false if not found

  public function arraySearch($needle, $haystack, $index = null) {
    $aIt = new RecursiveArrayIterator($haystack);
    $it = new RecursiveIteratorIterator($aIt);
    while ($it->valid()) {
      if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) {
        return $aIt->key();
      }
      $it->next();
    }

    return false;
  }

  public function getKey($array, $member, $value) {
    foreach ($array as $k => $v) {
      if ($v->$member == $value) {
        return $k;
      }
    }

    return false;
  }

  //return current week start and end dates

  public function rangeWeek($datestr) {
    $res['start'] = ECWD::ecwd_date("Y-m-d", strtotime($datestr));
    $res['end'] = ECWD::ecwd_date("Y-m-d", strtotime("+6 day", strtotime($datestr)));

    return $res;
  }

  public function range4Days($date) {
    $res['start'] = ECWD::ecwd_date("Y-m-d", strtotime($date));
    $res['end'] = ECWD::ecwd_date("Y-m-d", strtotime("+3 day", strtotime($date)));

    return $res;
  }

  public function calendar_foot() {
    if (in_array($this->displaytype, array(
      'full',
      'mini'
    ))) {
      $html = '</table>';
    } else {
      $html = '</div>';
    }

    return $html;
  }

// Removes tabs, line breaks, vertical tabs, null-byte. Everything but a regular space.

  public function stripWhitespace($c) {
    $c = str_replace(array(
      "\n",
      "\r",
      "\t",
      "\o",
      "\xOB"
    ), '', $c);

    return trim($c);
  }

// sorts an associative array by values of passed key

  public function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // check hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { // if a proper hex code e.g. #RRGGBB
      $colorVal = hexdec($hexStr);
      $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
      $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
      $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { // if shorthand notation e.g #RGB
      $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
      $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
      $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
      return false; // invalid hex color code
    }

// returns the rgb string or the associative array
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
  }

  public function getAndReplaceFirstImage($content) {
    global $ecwd_options;
    $first_img = '';
    if ($ecwd_options) {
      if (!isset($ecwd_options["move_first_image"]) || intval($ecwd_options["move_first_image"]) === 0) {
        return array(
          'image' => "",
          'content' => $content
        );
      }
    }
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
    if (isset($matches [1] [0])) {
      $first_img = $matches [1] [0];
    }

    if (empty($first_img)) { //Defines a default image
      return false;
    } else {
      //preg_replace('/<img[^>]+\>/i', '', $content);
      $content = $this->replaceFirstImage($content);
    }

    return array(
      'image' => $first_img,
      'content' => $content
    );
  }

  public function replaceFirstImage($content) {
    $content = preg_replace("/<img[^>]+\>/i", " ", $content, 1);

    return $content;
  }

  public function cal_days_in_month() {
    $date_str = $this->year . '-' . $this->month . '-01';
    $date = ECWD::ecwd_date('t', strtotime($date_str));

    return intval($date);
  }

  /**
   * @param array $args
   *
   * @return array|string
   */
  public function set_json_ld( $args = array(), $displaytype = '' ) {
    if ( !empty($args) ) {
      $startDate = ($args['all_day_event'] == 0) ? ECWD::ecwd_date('Y-m-d', $args['from']) . ' ' . $args['starttime'] : ECWD::ecwd_date('Y-m-d', $args['from']);
      $endDate = ($args['all_day_event'] == 0) ? ECWD::ecwd_date('Y-m-d', $args['to']) . ' ' . $args['endtime'] : ECWD::ecwd_date('Y-m-d', $args['to']);
      if ( !empty($displaytype) && ($displaytype == 'list' || 'posterboard') ) {
        $startDate = ($args['all_day_event'] == 0) ? $args['from'] . ' ' . $args['starttime'] : $args['from'];
        $endDate = ($args['all_day_event'] == 0) ? $args['to'] . ' ' . $args['endtime'] : $args['to'];
      }
      $data = array(
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $args['title'],
        'description' => strip_tags(preg_replace('/\\r|\\n|(\[(.*)\])/i', "", $args['details'])),
        'image' => get_the_post_thumbnail_url($args['id']),
        'startDate' => $startDate,
        'endDate' => $endDate,
        'eventAttendanceMode' => 'https://schema.org/MixedEventAttendanceMode',
        'eventStatus' => 'https://schema.org/EventScheduled'
      );
      if ( !empty($args['venue']['name']) && !empty($args['location']) ) {
        $data['location'] = array(
          '@type' => 'Place',
          'name' => $args['venue']['name'],
          'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => $args['location']
          )
        );
      }
      if ( !empty($args['organizers']) ) {
        foreach ( $args['organizers'] as $organizer ) {
          $data['performer'][] = array(
            '@type' => 'Person',
            'name' => $organizer['name'],
            'description' => '',
            'telephone' => '',
            'sameAs' => '',
          );
        }
      }
      $json_ld = json_encode($data) . ',';

      return $json_ld;
    }
  }
}
