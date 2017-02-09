<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('seterrorstatus'))
{
    function seterrorstatus($message)
    {
        return "{\"status\":\"error\",\"message\":\"".$message."\"}";
    }
}
if ( ! function_exists('setoktatus'))
{
    function setokstatus($data)
    {
        return "{\"status\":\"ok\",\"data\":".$data."}";
    }
    function setokstatustrainings($data,$trainData)
    {
        return "{\"status\":\"ok\",\"data\":".$data.",\"old_trainings\":".$trainData."}";
    }
}

if ( ! function_exists('render'))
{
    function render($value){
        if($value === null){
            $value = '';
        }
        return $value;
    }
}

if (!function_exists('_isTimeBetween')) {
    function _isTimeBetween($current_time)
    {
        // date_default_timezone_set ("Asia/Kathmandu");
        $timeA = date('h:i A');
        $timeB = date("h:i A", strtotime("+5 minutes"));

        if (strtotime($current_time) > strtotime($timeA) && strtotime($current_time) <= strtotime($timeB)) {
            return true;
        } else {
            return false;
        }
    }
}


function recursive_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
        $current_key=$key;
        if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value) !== false)) {
            return $current_key;
        }
    }
    return false;
}

/**
 * The scheduled time cannot be in the past, and can be up to two weeks in the future. It can be an ISO 8601 date with a date, time, and timezone, as in the example above, or it can be a numeric value representing a UNIX epoch time in seconds (UTC).
 * @param  time format
 * @return string
 */
function dateISO8601($time)
{
  // date_default_timezone_set('Asia/Kathmandu');

  $datetime = new DateTime($time);

  $str = $datetime->format('c');

  return $str;
}