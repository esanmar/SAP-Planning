<?php

/**
 * Check if the given DateTime object is a business day.
 *
 * @param DateTime $date
 * @return bool
 */
function isBusinessDay(DateTime $date)
{
    //Weekends
    if ($date->format('N') > 5) {
        return false;
    }

    //Hard coded public Holidays
    $holidays = [
        "Pilar"      => new DateTime(date('Y') . '-10-12'),
        "Santos"           => new DateTime(date('Y') . '-11-02'),
        "Conts"            => new DateTime(date('Y') . '-12-07'),
        "Inm"           => new DateTime(date('Y') . '-12-08'),
        "Navidad" => new DateTime(date('Y') . '-12-25'),
    ];

    foreach ($holidays as $holiday) {
        if ($holiday->format('Y-m-d') === $date->format('Y-m-d')) {
            return false;
        }
    }

    //December company holidays
    if (new DateTime(date('Y') . '-12-15') <= $date && $date <= new DateTime((date('Y') + 1) . '-01-08')) {
        return false;
    }

    // Other checks can go here

    return true;
}

/**
 * Get the available business time between two dates (in seconds).
 *
 * @param $start
 * @param $end
 * @return mixed
 */
function businessTime($start, $end)
{
    $start = $start instanceof \DateTime ? $start : new DateTime($start);
    $end = $end instanceof \DateTime ? $end : new DateTime($end);
    $dates = [];

    $date = clone $start;

    while ($date <= $end) {

        $datesEnd = (clone $date)->setTime(23, 59, 59);

        if (isBusinessDay($date)) {
            $dates[] = (object)[
                'start' => clone $date,
                'end'   => clone ($end < $datesEnd ? $end : $datesEnd),
            ];
        }

        $date->modify('+1 day')->setTime(0, 0, 0);
    }

    return array_reduce($dates, function ($carry, $item) {

        $businessStart = (clone $item->start)->setTime(8, 0, 0);
        $businessEnd = (clone $item->start)->setTime(16, 30, 0);

        $start = $item->start < $businessStart ? $businessStart : $item->start;
        $end = $item->end > $businessEnd ? $businessEnd : $item->end;

        //Diff in seconds
        return $carry += max(0, $end->getTimestamp() - $start->getTimestamp());
    }, 0);
}

$seconds = businessTime('2020-08-06 10:00:00', '2020-08-10 15:00:00');

echo $seconds;

/*

function work_hours_diff($date1,$date2) {
    if ($date1>$date2) { $tmp=$date1; $date1=$date2; $date2=$tmp; unset($tmp); $sign=-1; } else $sign = 1;
    if ($date1==$date2) return 0;

    $days = 0;
    $working_days = array(1,2,3,4,5); // Monday-->Friday
    $working_hours = array(8.5, 17.5); // from 8:30(am) to 17:30
    $current_date = $date1;
    $beg_h = floor($working_hours[0]); $beg_m = ($working_hours[0]*60)%60;
    $end_h = floor($working_hours[1]); $end_m = ($working_hours[1]*60)%60;

    // setup the very next first working timestamp

    if (!in_array(date('w',$current_date) , $working_days)) {
        // the current day is not a working day

        // the current timestamp is set at the begining of the working day
        $current_date = mktime( $beg_h, $beg_m, 0, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
        // search for the next working day
        while ( !in_array(date('w',$current_date) , $working_days) ) {
            $current_date += 24*3600; // next day
        }
    } else {
        // check if the current timestamp is inside working hours

        $date0 = mktime( $beg_h, $beg_m, 0, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
        // it's before working hours, let's update it
        if ($current_date<$date0) $current_date = $date0;

        $date3 = mktime( $end_h, $end_m, 59, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
        if ($date3<$current_date) {
            // outch ! it's after working hours, let's find the next working day
            $current_date += 24*3600; // the day after
            // and set timestamp as the begining of the working day
            $current_date = mktime( $beg_h, $beg_m, 0, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
            while ( !in_array(date('w',$current_date) , $working_days) ) {
                $current_date += 24*3600; // next day
            }
        }
    }

    // so, $current_date is now the first working timestamp available...

    // calculate the number of seconds from current timestamp to the end of the working day
    $date0 = mktime( $end_h, $end_m, 59, date('n',$current_date), date('j',$current_date), date('Y',$current_date) );
    $seconds = $date0-$current_date+1;

    printf("\nFrom %s To %s : %d hours\n",date('d/m/y H:i',$date1),date('d/m/y H:i',$date0),$seconds/3600);

    // calculate the number of days from the current day to the end day

    $date3 = mktime( $beg_h, $beg_m, 0, date('n',$date2), date('j',$date2), date('Y',$date2) );
    while ( $current_date < $date3 ) {
        $current_date += 24*3600; // next day
        if (in_array(date('w',$current_date) , $working_days) ) $days++; // it's a working day
    }
    if ($days>0) $days--; //because we've allready count the first day (in $seconds)

    printf("\nFrom %s To %s : %d working days\n",date('d/m/y H:i',$date1),date('d/m/y H:i',$date3),$days);

    // check if end's timestamp is inside working hours
    $date0 = mktime( $beg_h, 0, 0, date('n',$date2), date('j',$date2), date('Y',$date2) );
    if ($date2<$date0) {
        // it's before, so nothing more !
    } else {
        // is it after ?
        $date3 = mktime( $end_h, $end_m, 59, date('n',$date2), date('j',$date2), date('Y',$date2) );
        if ($date2>$date3) $date2=$date3;
        // calculate the number of seconds from current timestamp to the final timestamp
        $tmp = $date2-$date0+1;
        $seconds += $tmp;
        printf("\nFrom %s To %s : %d hours\n",date('d/m/y H:i',$date2),date('d/m/y H:i',$date3),$tmp/3600);
    }

    // calculate the working days in seconds

    $seconds += 3600*($working_hours[1]-$working_hours[0])*$days;

    printf("\nFrom %s To %s : %d hours\n",date('d/m/y H:i',$date1),date('d/m/y H:i',$date2),$seconds/3600);

    return $sign * $seconds/3600; // to get hours
}

date_default_timezone_set("America/Los_Angeles");
$dt2 = strtotime("2012-01-01 05:25:00");
$dt1 = strtotime("2012-01-19 12:40:00");
echo work_hours_diff($dt1 , $dt2 );

function get_working_hours($from,$to)
{
    // timestamps
    $from_timestamp = strtotime($from);
    $to_timestamp = strtotime($to);

    // work day seconds
    $workday_start_hour = 9;
    $workday_end_hour = 17;
    $workday_seconds = ($workday_end_hour - $workday_start_hour)*3600;

    // work days beetwen dates, minus 1 day
    $from_date = date('Y-m-d',$from_timestamp);
    $to_date = date('Y-m-d',$to_timestamp);
    $workdays_number = count(get_workdays($from_date,$to_date))-1;
    $workdays_number = $workdays_number<0 ? 0 : $workdays_number;

    // start and end time
    $start_time_in_seconds = date("H",$from_timestamp)*3600+date("i",$from_timestamp)*60;
    $end_time_in_seconds = date("H",$to_timestamp)*3600+date("i",$to_timestamp)*60;

    // final calculations
    $working_hours = ($workdays_number * $workday_seconds + $end_time_in_seconds - $start_time_in_seconds) / 86400 * 24;

    return $working_hours;
}

function get_workdays($from,$to) 
{
    // arrays
    $days_array = array();
    $skipdays = array("Saturday", "Sunday");
    $skipdates = get_holidays();

    // other variables
    $i = 0;
    $current = $from;

    if($current == $to) // same dates
    {
        $timestamp = strtotime($from);
        if (!in_array(date("l", $timestamp), $skipdays)&&!in_array(date("Y-m-d", $timestamp), $skipdates)) {
            $days_array[] = date("Y-m-d",$timestamp);
        }
    }
    elseif($current < $to) // different dates
    {
        while ($current < $to) {
            $timestamp = strtotime($from." +".$i." day");
            if (!in_array(date("l", $timestamp), $skipdays)&&!in_array(date("Y-m-d", $timestamp), $skipdates)) {
                $days_array[] = date("Y-m-d",$timestamp);
            }
            $current = date("Y-m-d",$timestamp);
            $i++;
        }
    }

    return $days_array;
}

function get_holidays() 
{
    // arrays
    $days_array = array();

    // You have to put there your source of holidays and make them as array...
    // For example, database in Codeigniter:
    // $days_array = $this->my_model->get_holidays_array();

    return $days_array;
}


function getWorkingDays($startDate,$endDate,$holidays){
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)

        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
}

//Example:

$holidays=array("2008-12-25","2008-12-26","2009-01-01");

echo getWorkingDays("2008-12-22","2009-01-02",$holidays);
// => will return 7


function biss_hours($start, $end){

    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $periodInterval = new DateInterval( "PT1H" );

    $period = new DatePeriod( $startDate, $periodInterval, $endDate );
    $count = 0;

    foreach($period as $date){

    $startofday = clone $date;
    $startofday->setTime(8,30);

    $endofday = clone $date;
    $endofday->setTime(17,30);

        if($date > $startofday && $date <= $endofday && !in_array($date->format('l'), array('Sunday','Saturday'))){

            $count++;
        }

    }
	
	//Get seconds of Start time
	$start_d = date("Y-m-d H:00:00", strtotime($start));
	$start_d_seconds = strtotime($start_d);
	$start_t_seconds = strtotime($start);
	$start_seconds = $start_t_seconds - $start_d_seconds;
							
	//Get seconds of End time
	$end_d = date("Y-m-d H:00:00", strtotime($end));
	$end_d_seconds = strtotime($end_d);
	$end_t_seconds = strtotime($end);
	$end_seconds = $end_t_seconds - $end_d_seconds;
									
	$diff = $end_seconds-$start_seconds;
	
	if($diff!=0):
		$count--;
	endif;
		
	$total_min_sec = date('i:s',$diff);
	
	return $count .":".$total_min_sec;
}

$start = '2014-06-23 12:30:00';
$end = '2014-06-27 15:45:00';

$go = biss_hours($start,$end);
echo $go;

*/

?>