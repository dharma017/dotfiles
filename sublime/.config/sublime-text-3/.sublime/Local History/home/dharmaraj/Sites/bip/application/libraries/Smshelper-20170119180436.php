<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * SMS Helper
 * @package Carlsoo
 *
 *
 * Copyright (c) 2004 - 2008 SMS-Teknik
 * Example to send and SMS message through the SMS Gateway XML API and recieve delivery status message through
 * https://www.smsteknik.se/Member/SMSConnectDirect/SendSMSv3.asp?
 * The message.xml contains a link back to this php file, so that status can be returned.
 * In our case: http://h217n2fls32o931.telia.com:11579/PHP/SEND_GET_STATUS/index.php
 * Messages are logged in /var/www/PHP/SEND_GET_STATUS/incoming/incoming.txt
 *
 * To use this example download libcurl for your OS from http:*curl.haxx.se/download.html
 * Get a package with SSL. You also need to download OpenSSL libraries/DLLS (se bottom of the page).
 * If you have Ubuntu or Debian you can just type: sudo apt-get install php5-curl
 * Of course you can use any Linux package installer.

 * Syntax of http get/post-data:
 * nr = mobile number				                (type=string)
 * ref = SMSID				                        (type=long)
 * state = delivery status			             	(type=string)
 * text = message text				                (type=string)
 * datetime = date and time of delivery		 	(type=string)

 * Following strings can appear in the state tag:
 * DELIVRD 		The message has been delivered to the recipient. Status is final.
 * EXPIRED 		Time for trying delivering the message has been reached. This status is normally final but can in some cases, when
		          	EXPIRED occurred before TTL time has been reached, change to RETRY.
 * UNDELIV 		The message has not been delivered, common reason is absent mobile, full SMS memory etc.
 * UNKNOWN 		Can’t decide status due do lack of information from operator.
 * REJECTED 	  	The message has been rejected by the operator.
 * FAILED 		   	The message has not been delivered, common issue are non valid mobile number. Status in final.
 * vDELETED		   	Used by some operators to specify problems with the recipient number. Status is temporary.
 * INVALID		  	Message is currently in an invalid state but are often re-queued if the message is correctly formatted. Status is therefor temporary.
 * RETRY		     	The message has been retried  Status is temporary.
 * BLANC 		No status yet have being received.
 *
 *
 *
 **/

class smshelper
{

    private $_id = 'SLL BUP';//WebSearch Professional';

    private $_user = 'sms3=TN5Z';//'smsWT@266';

    private $_pass = 'CZ2iP5';//ibCsUd';

    private $_send_service = 'SendSMSv3.asp';

    private $_status_service = 'GetStatusv2.asp';

    public $send_time,$send_date;

    public $msg_id;

    //private static $instance;


    function smshelper ()
    {
        //current time
        $this -> send_time = date ( "h:i:s" , time ( ) );
        //current date
        $this -> send_date = date ( "Y-m-d" , time ( ) );
    }

    /*
  public static function getInstance()
  {
    if ( is_null( self::$instance ) )
    {
      self::$instance = new self();
    }
    return self::$instance;
  }
*/

    function status ( $msg_id=false )
    {
        if(!$msg_id)
            $msg_id = $this->msg_id;

        //$xml =

       $xml =  "<?xml version='1.0' ?>
		<sms-teknik>
			<smsid>$msg_id</smsid>
		</sms-teknik>";

        $result = $this -> httpsPost (
            "https://www.smsteknik.se/Member/SMSConnectDirect/" . $this->_status_service . "?id=" . urlencode ( $this ->_id ) . "&user=" . $this->_user .
                 "&pass=" . urlencode ( $this->_pass ) , $xml );
        return $result;

    }

    /* send sms*/
    function send ( $reciever_number, $new_settings = array() )
    {


        extract (
            $this -> overwrite_default (
                array (

                    'operationtype' =>0 , //  <!-- 0=Text, 1=Wap-push, 2=vCard, 3=vCalender, 4=Binary -->
                    'flash' => 0 , // <!-- 0=Normal message, 1=Flash message (160 char limit)-->

                    'multisms' => 1 ,  //<!-- 0=disabled (160 char), 1=enabled (up to 804 char) -->
                    'maxmultisms' => 0 ,  //<!-- 0=disabled, 1-6 SMS count  -->


                    'compresstext' => 0 ,  //<!-- 0=”One small house”, 1=”OneSmallHouse”  -->


                    'udh' => '' ,  //<udh></udh>!-- User data header  -->
                    'udmessage' => 'message' ,  //<!-- Message text eller message data  -->
                    'smssender' => 'barninternetprojektet' ,  //<!-- Sender  -->


                    'deliverystatustype' => 0,  //<!-- 0=Off, 1=E-mail, 2=HTTP GET, 3=HTTP POST, 4=XML  -->
                    //'deliverystatusaddress'   =>  'http://h217n2fls32o931.telia.com:8180/PHP/SEND_GET_STATUS/index.php';//<!-- URL eller E-mail address  -->
                    'deliverystatusaddress' => 'tthemant@gmail.com' ,
                    'usereplynumber' => 0 ,  //<!-- 0=disabled, 1=enabled  -->
                    'usereplyforwardtype' =>0 ,  //<!-- 0=Off, 1=E-mail, 2=HTTP GET, 3=HTTP POST, 4=XML  -->
                    'usereplyforwardurl' => '' ,  //<!-- URL eller E-mail address  -->
                    'usereplycustomid' => '' ,  //<!-- Pass custom ID (max 100 characters)  -->
                    'usee164' => 0 )//<!-- Number check (E164) 0=No check, 1=Check  -->
 , $new_settings ) );


        $sms_xml = "<sms-teknik>
        <operationtype>$operationtype</operationtype>			                      <!-- 0=Text, 1=Wap-push, 2=vCard, 3=vCalender, 4=Binary -->
        <flash>$flash</flash> 					                      <!-- 0=Normal message, 1=Flash message (160 char limit)-->
        <multisms>$multisms</multisms> 				                              <!-- 0=disabled (160 char), 1=enabled (up to 804 char) -->
        <maxmultisms>$maxmultisms</maxmultisms> 			                              <!-- 0=disabled, 1-6 SMS count  -->
        <compresstext>$compresstext</compresstext> 			                              <!-- 0=â€One small houseâ€, 1=â€OneSmallHouseâ€  -->
        <send_date>$this->send_date</send_date> 			                      <!-- Must have the format yyyy-mm-dd [optional param]  -->
        <send_time>$this->send_time</send_time> 			                      <!-- Must have the format hh:mm:ss [optional param]  -->
        <udh>$udh</udh>					                              <!-- User data header  -->
        <udmessage>$udmessage</udmessage>     	 		      <!-- Message text eller message data  -->
        <smssender>$smssender</smssender> 			                      <!-- Sender  -->
        <deliverystatustype>$deliverystatustype</deliverystatustype> 		                      <!-- 0=Off, 1=E-mail, 2=HTTP GET, 3=HTTP POST, 4=XML  -->
        <deliverystatusaddress>$deliverystatusaddress</deliverystatusaddress> 		              <!-- URL eller E-mail address  -->
        <usereplynumber>$usereplynumber</usereplynumber>			                      <!-- 0=disabled, 1=enabled  -->
        <usereplyforwardtype>$usereplyforwardtype</usereplyforwardtype>		              	      <!-- 0=Off, 1=E-mail, 2=HTTP GET, 3=HTTP POST, 4=XML  -->
        <usereplyforwardurl>$usereplyforwardurl</usereplyforwardurl>		                      <!-- URL eller E-mail address  -->
        <usereplycustomid>$usereplycustomid</usereplycustomid>		                     	      <!-- Pass custom ID (max 100 characters)  -->
        <usee164>$usee164</usee164>				                              <!-- Number check (E164) 0=No check, 1=Check  -->
        <items>

        	         <recipient>
        		                   <orgaddress>$reciever_number</orgaddress>
        	         </recipient>
        </items>
        </sms-teknik>";




       /* $result = $this -> httpsPost (
            "https://www.smsteknik.se/Member/SMSConnectDirect/" . $this -> _status_service . "?id=" . urlencode ( $this -> _id ) . "&user=" . $this -> _user .
                 urlencode ( $this -> _pass ) , $sms_xml );
        */
        $result = $this->httpsPost("https://www.smsteknik.se/Member/SMSConnectDirect/" . $this->_send_service . "?id=".urlencode($this->_id)."&user=".$this->_user."&pass=" . urlencode($this->_pass) , $sms_xml);
        return $result; //msg id
    }

    /* http post function*/
    function httpsPost ( $Url, $strRequest )
    {

        // Initialisation
        $ch = curl_init ( );
        // Set parameters
        curl_setopt ( $ch , CURLOPT_URL , $Url );
        // Return a variable instead of posting it directly
        curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , 1 );
        // Active the POST method
        curl_setopt ( $ch , CURLOPT_POST , 1 );
        // Request
        curl_setopt ( $ch , CURLOPT_POSTFIELDS , $strRequest );
        curl_setopt ( $ch , CURLOPT_SSL_VERIFYPEER , 0 );
        curl_setopt ( $ch , CURLOPT_SSL_VERIFYHOST , 0 );
        // execute the connexion
        $result = curl_exec ( $ch );
        // Close it
        curl_close ( $ch );
        return $result;
    }

    /*use default array overwrite defaults with $atts array if supplied*/
    function overwrite_default ( $defaults, $atts )
    {

        $atts = ( array ) $atts;
        $out = array ();
        foreach ( $defaults as $name => $default ) {
            if ( array_key_exists ( $name , $atts ) )
                $out [ $name ] = $atts [ $name ];
            else
                $out [ $name ] = $default;
        }
        return $out;
    }

}
