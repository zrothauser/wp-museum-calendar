<?php

require_once 'vendor/autoload.php';

date_default_timezone_set('America/New_York');

class CMOA_Event {

  protected $_timezone = 'America/New_York';

  function __construct($post) {
    global $ai1ec_registry;
    $this->ai1ec_registry = $ai1ec_registry;
    $this->details = $ai1ec_registry->get('model.event', $post->ID);

    $options = array('extension' => '.html');
    $this->mustache = new Mustache_Engine(['loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/templates', $options)]);
  }

  function get_calendar_timezone() {
    return $this->_timezone;
  }

  /*
	*  recurrence
	*
	*  This function will return the events recurrence value "Daily/Weekly/etc"
  *
	*  @type	function
	*  @date	11/09/16
	*  @since	1.1.0
	*
	*  @return string || false
	*/

  public function recurrence() {
    $frequency_pattern = '/^FREQ=([^;]+);/';
    preg_match($frequency_pattern, $this->details->get('recurrence_rules'), $freq_matches);
    return isset($freq_matches[1]) ? $freq_matches[1] : false;
  }

  /*
  *  has_end_date
  *
  *  This function will return true if a recurring event has an explicit end date
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return boolean
  */

  public function has_end_date() {
    $until_pattern = '/UNTIL=([^;]+);/';
    preg_match($until_pattern, $this->details->get('recurrence_rules'), $until_matches);
    return isset($until_matches[1]);
  }

  /*
  *  event_instances
  *
  *  This function will return an array of WP DB objects from the event_instances table
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return array of query results
  */

  public function event_instances() {
    global $wpdb;
    $instances_table_name = $wpdb->prefix . 'ai1ec_event_instances';

    $query = "SELECT id, start, end ".
      "FROM {$instances_table_name} " .
      "WHERE post_id = %d";

    $args = array(
      $this->details->get('post_id')
    );

    $results = $wpdb->get_results($wpdb->prepare($query, $args));
    return $results;
	}

  /*
  *  last_event_end
  *
  *  This function will return the end timestamp of an event, or the end
  *  timestamp of the last instance of an event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return timestamp string
  */

  public function last_event_end() {
    if($this->recurrence() !== false && $this->has_end_date()) {
      return new Ai1ec_Date_Time($this->ai1ec_registry, end($this->event_instances())->end, $this->_timezone);
    }
    else {
      return $this->details->get('end');
    }
  }

  /*
  *  all_day
  *
  *  This function will return true if an event is marked as an all-day event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return boolean
  */

  public function all_day() {
    return $this->details->get('allday') == 1;
  }

  /*
  *  has_no_end_time
  *
  *  This function will return true if an event is marked as having no end time
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return boolean
  */

  public function has_no_end_time() {
    return $this->details->get('instant_event') == 1;
  }

  /*
  *  start_date
  *
  *  This function will return the formatted start month and day of an event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return date string
  */

  public function start_date() {
    return $this->details->get('start')->format('M j');
  }

  /*
  *  start_date_iso
  *
  *  This function will return the formatted ISO start date of an event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return date string
  */

  public function start_date_iso() {
    return $this->details->get('start')->format('c');
  }

  /*
  *  start_year
  *
  *  This function will return the formatted start year of an event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return date string
  */

  public function start_year() {
    return $this->details->get('start')->format('Y');
  }

  /*
  *  start_time
  *
  *  This function will return the formatted start time of an event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return date string
  */

  public function start_time() {
    return $this->details->get('start')->format('g:i a');
  }

  /*
  *  end_date
  *
  *  This function will return the formatted end month and day of an event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return date string
  */

  public function end_date() {
    return $this->last_event_end()->format('M j');
  }

  /*
  *  end_year
  *
  *  This function will return the formatted end year of an event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return date string
  */

  public function end_year() {
    return $this->last_event_end()->format('Y');
  }

  /*
  *  end_time
  *
  *  This function will return the formatted end time of an event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return date string
  */

  public function end_time() {
    return $this->last_event_end()->format('g:i a');
  }

  /*
  *  in_same_year
  *
  *  This function will return true if the start and end years are the same for an event
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return boolean
  */

  public function in_same_year() {
    return $this->start_year() == $this->end_year();
  }

  /*
  *  formatted_instances
  *
  *  This function will return an array of formatted date strings for all event instances
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return array
  */

  public function formatted_instances() {
    $formatted_events = array_map(function($event) {
      $date = new Ai1ec_Date_Time($this->ai1ec_registry, $event->end, $this->_timezone);
      return $date->format('M j, Y');
    }, $this->event_instances());
    return $formatted_events;
  }

  /*
  *  display_dates
  *
  *  This function will conditionally render an html template based on event conditions
  *
  *  @type	function
  *  @date	11/09/16
  *  @since	1.1.0
  *
  *  @return html template string
  */

  public function display_dates() {
    if($this->recurrence() !== false) {
      $template = $this->has_end_date() ? 'date_range' : 'recurring';
    }
    elseif($this->details->get('recurrence_rules')) {
      $template = 'multi';
    }
    elseif ($this->all_day() || $this->has_no_end_time()) {
      $template = 'start_date';
    }
    elseif ($this->details->get('start')->format('Ymd') != $this->details->get('end')->format('Ymd')) {
      $template = 'mixed_range';
    }
    else {
      $template = 'single';
    }

    return $this->mustache->render("$template.html", $this);
  }
}
?>
