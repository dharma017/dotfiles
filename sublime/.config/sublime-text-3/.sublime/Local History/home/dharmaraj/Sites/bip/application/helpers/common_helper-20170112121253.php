<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
common function to be used in the projects.
*/
function force_ssl() {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") {
        $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        redirect($url);
        exit;
    }
}

function bip_logged_in($bip_logged_in,$loginType)
	{
		$CI =& get_instance();

		if((!isset($bip_logged_in) || $bip_logged_in != true) || ($CI->session->userdata("logintype")!=$loginType))
		{
			// $CI->session->sess_destroy();
			redirect(site_url("login/logout"));
		}
		else
		{
      /*if (ENVIRONMENT!='production') {
          return true;
      }*/

			if($CI->session->userdata("logintype") == "user" && $CI->session->userdata("bass_completion") == false )
			{
				$CI->load->model('login/login_model');

        $user_id = $CI->session->userdata["user_id"];

        $url = $CI->login_model->getBassUrl();

				$part_id = $CI->login_model->getBassID();

				if(empty($part_id))
				{
					$CI->session->set_userdata(array("bass_completion"=>true));
					return true;
				}

    	  		$bass_curl_url = $url.$part_id;

				$initial_bass_response = get_web_page($bass_curl_url);

				$status_array['bass_curl_url']= $bass_curl_url;
				$status_array['initial_bass_response']= $initial_bass_response;

				// $CI->load->library('logger');
				// $CI->load->library('user_agent');
				// $CI->logger->logAction('bass action', (array)$status_array);

				// $initial_bass_response = "https://webskattning.se/bip/extlogin/dologin.php?UID=3NYpr72cT3ilDZD4vdhuOku7BsGndoT72ImjEdlo6tAyi4sD7z ";

				$pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";

				if($initial_bass_response == '0' || substr($initial_bass_response, 0, 1) === '0')
				{
					// die('bass response 0');
					$CI->session->set_userdata(array("bass_completion"=>true));
					return true;
				}elseif (!preg_match($pattern, $initial_bass_response)) {
					 // die('Not a valid URL');
					$CI->session->set_userdata(array("bass_completion"=>true));
					return true;
				}
				else
				{
					// die('valid URL');
					//make the redirection with our site added
					$return_url = site_url("stage");
					$bass_url = $initial_bass_response."&returnURL=".$return_url;
					$bass_url = preg_replace( '/\s+/', ' ', $bass_url );

					redirect($bass_url);
					return false;
				}
			}

			else
				return true;
		}
	}

	function get_web_page( $url )
	{
		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_USERAGENT      => "spider", // who am i
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		);

		$ch      = curl_init( $url );
		curl_setopt_array( $ch, $options );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );


		return $content;
	}

function output_file($file, $name, $mime_type='')
{
 /*
 This function takes a path to a file to output ($file),
 the filename that the browser will see ($name) and
 the MIME type of the file ($mime_type, optional).

 If you want to do something on download abort/finish,
 register_shutdown_function('function_name');
 */
 echo $file;
 echo $name;
 if(!is_readable($file)) die(mysql_error().'File not found or inaccessible!');

 $size = filesize($file);
 $name = rawurldecode($name);

 /* Figure out the MIME type (if not specified) */
 $known_mime_types=array(
 	"pdf" => "application/pdf",
 	"txt" => "text/plain",
 	"html"=> "text/html",
 	"htm" => "text/html",
	"exe" => "application/octet-stream",
	"zip" => "application/zip",
	"doc" => "application/msword",
	"xls" => "application/vnd.ms-excel",
	"ppt" => "application/vnd.ms-powerpoint",
	"gif" => "image/gif",
	"png" => "image/png",
	"jpeg"=> "image/jpg",
	"jpg" =>  "image/jpg",
	"php" => "text/plain"
 );

 if($mime_type==''){
	 $file_extension = strtolower(substr(strrchr($file,"."),1));
	 if(array_key_exists($file_extension, $known_mime_types)){
		$mime_type=$known_mime_types[$file_extension];
	 } else {
		$mime_type="application/force-download";
	 };
 };

 @ob_end_clean(); //turn off output buffering to decrease cpu usage

 // required for IE, otherwise Content-Disposition may be ignored
 if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');

 header('Content-Type: ' . $mime_type);
 header('Content-Disposition: attachment; filename="'.$name.'"');
 header("Content-Transfer-Encoding: binary");
 header('Accept-Ranges: bytes');

 /* The three lines below basically make the
    download non-cacheable */
 header("Cache-control: private");
 header('Pragma: private');
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

 // multipart-download and download resuming support
 if(isset($_SERVER['HTTP_RANGE']))
 {
	list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
	list($range) = explode(",",$range,2);
	list($range, $range_end) = explode("-", $range);
	$range=intval($range);
	if(!$range_end) {
		$range_end=$size-1;
	} else {
		$range_end=intval($range_end);
	}

	$new_length = $range_end-$range+1;
	header("HTTP/1.1 206 Partial Content");
	header("Content-Length: $new_length");
	header("Content-Range: bytes $range-$range_end/$size");
 } else {
	$new_length=$size;
	header("Content-Length: ".$size);
 }

 /* output the file itself */
 $chunksize = 1*(1024*1024); //you may want to change this
 $bytes_send = 0;
 if ($file = fopen($file, 'r'))
 {
	if(isset($_SERVER['HTTP_RANGE']))
	fseek($file, $range);

	while(!feof($file) &&
		(!connection_aborted()) &&
		($bytes_send<$new_length)
	      )
	{
		$buffer = fread($file, $chunksize);
		print($buffer); //echo($buffer); // is also possible
		flush();
		$bytes_send += strlen($buffer);
	}
 fclose($file);
 } else die('Error - can not open file.');

die();
}


function replace_swedish_char($text)
{

	if($text)
	{
		$text = str_replace("Ö","&Ouml;",$text);
		$text = str_replace("ö","&ouml;",$text);
		$text = str_replace("Ä","&Auml;",$text);
		$text = str_replace("ä","&auml;",$text);
		$text = str_replace("ë","&euml;",$text);
		$text = str_replace("Ë","&Euml;",$text);
		$text = str_replace("Å","&Aring;",$text);
        $text = str_replace("å","&aring;",$text);

	}
	return $text;


}

function format_date($strDate, $format="") {
    setlocale(LC_TIME, "sve");
    if ($format == "DD MD YYYY")
        return strftime("%d %b %Y", strtotime($strDate));
    else
        return strftime("%d %b %Y  %H:%M", strtotime($strDate));
}

function sendSMS($contact_number, $sms_message = '')
{
	if (ENVIRONMENT=='development') return false;

	$CI =& get_instance();
	$CI->load->library('smshelper');
	$CI->load->model('messages/messages_model');
	if(empty($sms_message))
	{
		$auto_contents= $CI->messages_model->getSMSTemplateMessage(); //for the template message set by the admin
		$sms_message  = nl2br(stripslashes(html_entity_decode($auto_contents)));
	}

	$sms_message = str_replace("<br />","\n",$sms_message);
	$sms_message = str_replace("<br/>","\n",$sms_message);
	$sms_message = str_replace("<br>","\n",$sms_message);

	if(strpos($sms_message,'<hr'))
		$sms_message = strip_tags(trim(substr($sms_message,0,strpos($sms_message,'<hr'))));
	else
		$sms_message = strip_tags(trim($sms_message));

	$sms_message = html_entity_decode($sms_message, ENT_QUOTES, "utf-8");

	if(strlen($sms_message) > 160)
	{
		$len = strlen($sms_message);

		$len = 155 - $len;


		$break_pos = strrpos($sms_message, ' ', $len);//find next space before
		$sms_message = substr($sms_message,0,$break_pos);
	}
	#/*
	//$carlsoo_options = get_option(carlsoo_theme_options);

	$sms_options = array(

		'udmessage'              	 =>                	$sms_message,
		'smssender'              	 =>                	'BIP',
		'multisms'               	 =>                	1,
		'maxmultisms'                =>               	6,
		'compresstext'               =>               	0,
		'operationtype'              =>                	0,
		'flash'               		 =>                	0,
		'deliverystatustype'         =>                	1,
		'deliverystatusaddress'      =>                	'tulipstechno@gmail.com',
		'usereplynumber'             =>                	0
	);

	/* uncomment the following line to enable sms*/
	$msg_id=0;

	if(!empty($contact_number) && !empty($sms_message))
	{
		$msg_id = $CI->smshelper -> send( $contact_number, $sms_options );
	}else{
		$msg_id =48858825000;
	}
	$status = $CI->smshelper->status( $msg_id );

	$sxml = simplexml_load_string($status);
	$json_status = json_encode($sxml);
	$status_array = json_decode($json_status,true);
	$sms_status = $status_array['item']['state'];

	if ($sms_status==0) {

		$status_array['contact_number']= $contact_number;

		// $CI->load->library('logger');
		// $CI->load->library('user_agent');
		// $CI->logger->logAction('sendSMS', (array)$status_array);
	};

}

//returns proper usertype to be used in the admin

function getUserType()
{
	$CI =& get_instance();
	$usertype = $CI->session->userdata('logintype');
	if($usertype == "admin")
	{
		$user_permission = $CI->session->userdata("permission");
		if(!empty($user_permission))
			$usertype = "Psychologist";
	}

	return $usertype;
}

/**
 * check notification status time is between reminder week.
 * @param  date $start_date
 * @param  date $end_date
 * @param  date $date_from_user
 * @return boolean
 */
 function check_in_range($start_date, $end_date, $date_from_user)
{
  // Convert to timestamp
  $start_ts = strtotime($start_date);
  $end_ts = strtotime($end_date);
  $user_ts = strtotime($date_from_user);

  // Check that user date is between start & end
  return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
}


/**
 * create reminder weeks dates period in array format starting from activation date.
 * @param  date $activeFrom
 * @param  int $totalWeek  total reminder week
 * @return array
 */
 function createDateRangeArray($activeFrom,$totalWeek)
{
    $start  = new DateTime($activeFrom);
	$end    = new DateTime('now');
	$interval = DateInterval::createFromDateString('7 day');
	$period   = new DatePeriod($start, $interval, $end);

	$arrayRange=array();
	$newRange=array();
	foreach ($period as $dt)
	{
	    array_push($arrayRange, $dt->format("Y-m-d"));
	}

	foreach ($arrayRange as $key => $value) {
		$date = strtotime($value);
		$date = strtotime("+7 day", $date);
		$end=date('Y-m-d', $date);
		if ($key>$totalWeek) continue;
		$newRange[$key] = array("start"=>$value,"end"=>$end);

	}
	return $newRange;
}


/**
 * get difference between start timer & current time
 * @param  datetime $startTimer
 * @param  integer $patientId
 * @return datetime
 */
function getTimer($patientId,$startTimer){
	$timer= $startTimer[$patientId];
	$now = new DateTime('now');
	$exp = new DateTime($timer);
	$interval = $now->diff($exp);
    return $diff = $interval->format('%Y-%M-%D %H:%I:%S');
}

/**
 * calcuate no. of days between two datetime
 * @param  datetime $startDate
 * @param  datetime $endDate
 * @return integer            days
 */
function _date_difference($startDate,$endDate){
	$date1 = new DateTime($startDate);
	$date2 = new DateTime($endDate);
	$interval = $date1->diff($date2);

	return $interval->days;
}

/**
 * convert secons to human readable time format
 * @param  int  $seconds
 * @param  boolean $padHours
 * @return time            hours:minute:seconds
 */
function _secondsToHMS($seconds, $padHours = false) {
    // start with a blank string
    $hms = "";

    // do the hours first: there are 3600 seconds in an hour, so if we divide
    // the total number of seconds by 3600 and throw away the remainder, we're
    // left with the number of hours in those seconds
    $hours = intval(intval($seconds) / 3600);

    // add hours to $hms (with a leading 0 if asked for)
    $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" : $hours . ":";

    // dividing the total seconds by 60 will give us the number of minutes
    // in total, but we're interested in *minutes past the hour* and to get
    // this, we have to divide by 60 again and then use the remainder
    $minutes = intval(($seconds / 60) % 60);

    // add minutes to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":";

    // seconds past the minute are found by dividing the total number of seconds
    // by 60 and using the remainder
    $seconds = intval($seconds % 60);

    // add seconds to $hms (with a leading 0 if needed)
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    // done!
    return $hms;
}


function _sec2Time($time){
  if(is_numeric($time)){
    $value = array(
      "years" => 0, "days" => 0, "hours" => 0,
      "minutes" => 0, "seconds" => 0,
    );
    if($time >= 31556926){
      $value["years"] = floor($time/31556926);
      $time = ($time%31556926);
    }
    if($time >= 86400){
      $value["days"] = floor($time/86400);
      $time = ($time%86400);
    }
    if($time >= 3600){
      $value["hours"] = floor($time/3600);
      $time = ($time%3600);
    }
    if($time >= 60){
      $value["minutes"] = floor($time/60);
      $time = ($time%60);
    }
    $value["seconds"] = floor($time);
    echo "<pre>";print_r($value);exit;
    return (array) $value;
  }else{
    return (bool) FALSE;
  }
}

function _secondsToWords($seconds)
{
    $ret = "";

    /*** get the days ***/
    $days = intval(intval($seconds) / (3600*24));
    if($days> 0)
    {
        $ret .= "$days"."days ";
    }

    /*** get the hours ***/
    $hours = (intval($seconds) / 3600) % 24;
    if($hours > 0)
    {
        $ret .= "$hours"."h ";
    }

    /*** get the minutes ***/
    $minutes = (intval($seconds) / 60) % 60;
    if($minutes > 0)
    {
        $ret .= "$minutes"."m ";
    }

    /*** get the seconds ***/
    $seconds = intval($seconds) % 60;
    if ($seconds > 0) {
        $ret .= "$seconds"."s ";
    }

    return $ret;
}

// strip javascript, styles, html tags, normalize entities and spaces
// based on http://www.php.net/manual/en/function.strip-tags.php#68757
function html2text($html){
	$text = $html;
	static $search = array(
		'@<script.+?</script>@usi',  // Strip out javascript content
		'@<style.+?</style>@usi',    // Strip style content
		'@<!--.+?-->@us',            // Strip multi-line comments including CDATA
		'@</?[a-z].*?\>@usi',         // Strip out HTML tags
	);
	$text = preg_replace($search, ' ', $text);
	// normalize common entities
	$text = normalizeEntities($text);
	// decode other entities
	$text = html_entity_decode($text, ENT_QUOTES, 'utf-8');
	// normalize possibly repeated newlines, tabs, spaces to spaces
	$text = preg_replace('/\s+/u', ' ', $text);
	$text = trim($text);
	// we must still run htmlentities on anything that comes out!
	// for instance:
	// <<a>script>alert('XSS')//<<a>/script>
	// will become
	// <script>alert('XSS')//</script>
	return $text;
}

// replace encoded and double encoded entities to equivalent unicode character
// also see /app/bookmarkletPopup.js
function normalizeEntities($text) {
	static $find = array();
	static $repl = array();
	if (!count($find)) {
		// build $find and $replace from map one time
		$map = array(
			array('\'', 'apos', 39, 'x27'), // Apostrophe
			array('\'', '‘', 'lsquo', 8216, 'x2018'), // Open single quote
			array('\'', '’', 'rsquo', 8217, 'x2019'), // Close single quote
			array('"', '“', 'ldquo', 8220, 'x201C'), // Open double quotes
			array('"', '”', 'rdquo', 8221, 'x201D'), // Close double quotes
			array('\'', '‚', 'sbquo', 8218, 'x201A'), // Single low-9 quote
			array('"', '„', 'bdquo', 8222, 'x201E'), // Double low-9 quote
			array('\'', '′', 'prime', 8242, 'x2032'), // Prime/minutes/feet
			array('"', '″', 'Prime', 8243, 'x2033'), // Double prime/seconds/inches
			array(' ', 'nbsp', 160, 'xA0'), // Non-breaking space
			array('-', '‐', 8208, 'x2010'), // Hyphen
			array('-', '–', 'ndash', 8211, 150, 'x2013'), // En dash
			array('--', '—', 'mdash', 8212, 151, 'x2014'), // Em dash
			array(' ', ' ', 'ensp', 8194, 'x2002'), // En space
			array(' ', ' ', 'emsp', 8195, 'x2003'), // Em space
			array(' ', ' ', 'thinsp', 8201, 'x2009'), // Thin space
			array('*', '•', 'bull', 8226, 'x2022'), // Bullet
			array('*', '‣', 8227, 'x2023'), // Triangular bullet
			array('...', '…', 'hellip', 8230, 'x2026'), // Horizontal ellipsis
			array('°', 'deg', 176, 'xB0'), // Degree
			array('€', 'euro', 8364, 'x20AC'), // Euro
			array('¥', 'yen', 165, 'xA5'), // Yen
			array('£', 'pound', 163, 'xA3'), // British Pound
			array('©', 'copy', 169, 'xA9'), // Copyright Sign
			array('®', 'reg', 174, 'xAE'), // Registered Sign
			array('™', 'trade', 8482, 'x2122') // TM Sign
		);
		foreach ($map as $e) {
			for ($i = 1; $i < count($e); ++$i) {
				$code = $e[$i];
				if (is_int($code)) {
					// numeric entity
					$regex = "/&(amp;)?#0*$code;/";
				}
				elseif (preg_match('/^.$/u', $code)/* one unicode char*/) {
					// single character
					$regex = "/$code/u";
				}
				elseif (preg_match('/^x([0-9A-F]{2}){1,2}$/i', $code)) {
					// hex entity
					$regex = "/&(amp;)?#x0*" . substr($code, 1) . ";/i";
				}
				else {
					// named entity
					$regex = "/&(amp;)?$code;/";
				}
				$find[] = $regex;
				$repl[] = $e[0];
			}
		}
	} // end first time build
	return preg_replace($find, $repl, $text);
}

function search_array($needle, $haystack) {
     if(in_array($needle, $haystack)) {
          return true;
     }
     foreach($haystack as $element) {
          if(is_array($element) && search_array($needle, $element))
               return true;
     }
   return false;
}

function file_write($filename) {
	$CI =& get_instance();
    if (!is_writable($filename)) {
        if (!chmod($filename, 0777)) {
        	$error = "File permission error, please contact your site support.";
        	$CI->session->set_flashdata('file_error',$error);
        }
    }else{
    	$success = "File saved successfully";
        $CI->session->set_flashdata('msg_success',$success);
    }
    return true;
}

function getUserAgent()
{
	$CI =& get_instance();
   if ($CI->agent->is_browser())
    {
        $agent = $CI->agent->browser().' '.$CI->agent->version();
    }
    elseif ($CI->agent->is_robot())
    {
        $agent = $CI->agent->robot();
    }
    elseif ($CI->agent->is_mobile())
    {
        $agent = $CI->agent->mobile();
    }
    else
    {
        $agent = 'Unidentified User Agent';
    }

    return $agent;
}


function DefaultDifficulty(){
	$CI =& get_instance();

	$diff = $CI->input->cookie("bip_default_difficulty");
	$setdifficulty = $diff>0 ? $diff : 0;
	return $setdifficulty;
}

function getHtml5Video($name){
	$CI =& get_instance();
	return base_url().$CI->config->item("html5_path").$CI->session->userdata("bip_language_code").'/'.$name.'/'.$name.'.html';
}

function dd($data) {
   echo "<pre>";
      print_r($data);
   exit;
}
