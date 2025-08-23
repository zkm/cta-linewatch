<?php
function secs_to_string ($secs, $long=false) {
  // reset hours, mins, and secs we'll be using
  $hours = 0;
  $mins = 0;
  $secs = intval ($secs);
  $t = array(); // hold all 3 time periods to return as string
  
  // take care of mins and left-over secs
  if ($secs >= 60) {
    $mins += (int) floor ($secs / 60);
    $secs = (int) $secs % 60;
        
    // now handle hours and left-over mins    
    if ($mins >= 60) {
      $hours += (int) floor ($mins / 60);
      $mins = $mins % 60;
    }
    // we're done! now save time periods into our array
    $t['hours'] = (intval($hours) < 10) ? "0" . $hours : $hours;
    $t['mins'] = (intval($mins) < 10) ? "0" . $mins : $mins;
  }

  // what's the final amount of secs?
  $t['secs'] = (intval ($secs) < 10) ? "0" . $secs : $secs;
  
  // decide how we should name hours, mins, sec
  $str_hours = ($long) ? "hour" : "hour";

  $str_mins = ($long) ? "minute" : "min";
  $str_secs = ($long) ? "second" : "sec";

  // build the pretty time string in an ugly way
  $time_string = "";
  $time_string .= ($t['hours']) ? $t['hours'] . " $str_hours" . ((intval($t['hours']) == 1) ? "" : "s") : "";
  $time_string .= ($t['mins']) ? (($t['hours']) ? ", " : "") : "";
  $time_string .= ($t['mins']) ? $t['mins'] . " $str_mins" . ((intval($t['mins']) == 1) ? "" : "s") : "";
  $time_string .= ($t['hours'] || $t['mins']) ? (($t['secs'] > 0) ? ", " : "") : "";
  $time_string .= ($t['secs']) ? $t['secs'] . " $str_secs" . ((intval($t['secs']) == 1) ? "" : "s") : "";

  return empty($time_string) ? 0 : $time_string;
}

// do the same as above in "hh:mm:ss" format
function secs_to_string_compact ($secs) {
  // grab the string return by the above function
  // and format begin formatting it
  $str = secs_to_string ($secs);
  

  if (!$str) return 0;
    
    $hour_pos = strpos ($str, "hour");
    $min_pos = strpos ($str, "min");
    $sec_pos = strpos ($str, "sec");
    
    $h = ($hour_pos) ? intval (substr ($str, 0, $hour_pos)) : 0;
    $m = ($min_pos) ? intval (substr ($str, $min_pos - 3, $min_pos)) : 0;
    $s = ($sec_pos) ? intval (substr ($str, $sec_pos - 3, $sec_pos)) : 0;
    
    $h = ($h < 10) ? "0" . $h : $h;
    $m = ($m < 10) ? "0" . $m : $m;
    $s = ($s < 10) ? "0" . $s : $s;
    
    return ("$h:$m:$s");
}


/**
 * Convert seconds to human readable format
 * in Year, Month, Day, Hour, Minute and Second
 * @param int $sec The second value
 * @return array The human readable format in an associative array
 */
function itg_sec_to_h($sec) {
    //make the $sec unsigned
    $sec = intval(abs($sec));
    //initialize the return array
    $ret = array(
        'year'      => 0,
        'month'     => 0,
        'day'       => 0,
        'hour'      => 0,
        'minute'    => 0,
        'second'    => 0,
    );

    //check if given second is zero
    if(0 == $sec)
        return $ret;

    //initialize the unit array
    $units = array(
        'year'      => 365*24*60*60,
        'month'     => 30*24*60*60,
        'day'       => 24*60*60,
        'hour'      => 60*60,
        'minute'    => 60,
        'second'    => 1,
    );

    //calculate the year, month, day, hour, minute, second
    foreach($units as $unit => $val) {
        $value = floor($sec/$val);
        $ret[$unit] = $value;
        $sec -= $value*$val;
    }

    return $ret;
}

/**
 * Calculate the difference between two dates
 * Prefers the PHP 5.3 DateTime OOP
 * If PHP version is less than 5.3 it uses procedural method instead
 * @param string $date_one The first date in H:i:s Y-m-d format
 * @param string $date_two The second date in H:i:s Y-m-d format
 * @return array the array containing year, month, day, hour, minute and second information. Access it like $arr['year'] etc.
 */
function itg_cal_difference_date($date_one, $date_two) {
    if(version_compare('5.3', phpversion(), '<=')) {
        $d_one = new DateTime($date_one);
        $d_two = new DateTime($date_two);
        $d_diff = $d_one->diff($d_two);
        return array(
            'year' => $d_diff->y,
            'month' => $d_diff->m,
            'day' => $d_diff->d,
            'hour' => $d_diff->h,
            'minute' => $d_diff->i,
            'second' => $d_diff->s,
        );
    } else {
        $d_one = strtotime($date_one);
        $d_two = strtotime($date_two);

        $sec = $d_one - $d_two;

        //make the $sec unsigned
        $sec = intval(abs($sec));
        //initialize the return array
        $ret = array(
            'year'      => 0,
            'month'     => 0,
            'day'       => 0,
            'hour'      => 0,
            'minute'    => 0,
            'second'    => 0,
        );

        //check if given second is zero
        if(0 == $sec)
            return $ret;

        //initialize the unit array
        $units = array(
            'year'      => 365*24*60*60,
            'month'     => 30*24*60*60,
            'day'       => 24*60*60,
            'hour'      => 60*60,
            'minute'    => 60,
            'second'    => 1,
        );

        //calculate the year, month, day, hour, minute, second
        foreach($units as $unit => $val) {
            $value = floor($sec/$val);
            $ret[$unit] = $value;
            $sec -= $value*$val;
        }

        return $ret;

    }
}