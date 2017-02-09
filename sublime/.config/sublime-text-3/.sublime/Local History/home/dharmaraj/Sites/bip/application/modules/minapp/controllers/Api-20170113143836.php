<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public $input_data = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('service');
		$this->load->helper('common');

		$this->load->helper('jwt');
		$this->load->config('jwt');

		$this->load->model('minapp/minapp_model');
		$this->load->model('minapp/api_model');
		$this->load->model('login/login_model');
		$this->load->model('messages/messages_model');
		if($_SERVER["HTTP_HOST"]=="localhost"){
			date_default_timezone_set ("Asia/Kathmandu");
		}
		/**
		 * Using php://input, a read-only stream, instead of PHP_POST variables. This is because the POST’ed data was not submitted as a key=>value pair (unlike normal form submissions). This method takes all of the raw submitted data in one chunk making it ideal for use with JSON as we need it all as one string before breaking it down.
		 */
		$input_data = json_decode(trim(file_get_contents('php://input')), true);
		$this->input_data=(object)$input_data;
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
	}

	public function validateuser()
	{
		$input = array(
			'username' => $this->input_data->username,
			'password' => $this->input_data->password,
			'deviceId' => $this->input_data->deviceId,
			'UrbanAirshipId' => $this->input_data->identificationumber,
			'tokenkey' => $this->input_data->TokenKey,
			'devicetype' => $this->input_data->devicetype,
			'webLogin' => $this->input_data->webLogin,
			'isWebVersion' => $this->input_data->isWebVersion,
			'DeviceUUID' => $this->input_data->DeviceUUID
		);

		$output=$this->api_model->validateuser($input);

		$exceptArray = array("patient_inactive","only_web_version","only_mobile_version");

		if (isset($output) && !in_array($output,$exceptArray)){

			$tokenId    = base64_encode(mcrypt_create_iv(32));
			// $tokenId    = uniqid();
		    $issuedAt   = time();
		    $notBefore  = $issuedAt + 10;             //Adding 10 seconds
		    $expire     = $notBefore + 60;            // Adding 60 seconds
		    $serverName = $this->input->server('HTTP_HOST'); // Retrieve the server name from config file
		    /*
		     * Create the token as an array
		     */
		    $jwt_data = [
		        'iat'  => $issuedAt,         // Issued at: time when the token was generated
		        'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
		        'iss'  => $serverName,       // Issuer
		        'nbf'  => $notBefore,        // Not before
		        'exp'  => $expire,           // Expire
		        'data' => [                  // Data related to the signer user
		            'userId'   => $output->userid // userid from the users table
		        ]
		    ];

		    $secretKey = base64_decode($this->config->item('jwt_key'));
		    $jwt_algorithm = $this->config->item('jwt_algorithm');
			$output->tokenkey = JWT::encode(
				$jwt_data,
				$secretKey,
				$jwt_algorithm
				);

		    echo setokstatus(json_encode($output));

		}else if($output=="patient_inactive"){
			echo seterrorstatus("patient_inactive");
		}else if($output=="only_web_version"){
			echo seterrorstatus("You are set to use only web version of the app.");
		}else if($output=="only_mobile_version"){
			echo seterrorstatus("You are set to use only mobile version of the app.");
		}else{
			echo seterrorstatus("Ogiltigt användarnamn eller lösenord!");
		}

	}

	public function validate_token()
	{
		try {
			$secretKey = base64_decode($this->config->item('jwt_key'));
			$jwt_algorithm = $this->config->item('jwt_algorithm');
			$DecodedDataArray = JWT::decode($this->input_data->tokenkey, $secretKey, array($jwt_algorithm));
			// echo  "{'status' : 'success' ,'data':".json_encode($DecodedDataArray)." }";die();
		} catch (Exception $e) {
			// echo "{'status' : 'fail' ,'msg':'Unauthorized'}";die();
			echo seterrorstatus($e->getMessage());die();
		}

	}

	public function active_tasks()
	{
		$this->validate_token();

		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey
			);

		$data=$this->api_model->getActiveTasks($input);

		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No active tasks");
		}


	}

	public function reminder()
	{
		$this->validate_token();
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey
			);

		$data=$this->api_model->getPatientAppReminder($input);

		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No active reminder");
		}
	}

	public function feedback_message()
	{
		$this->validate_token();
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey
			);

		$data=$this->api_model->getFeedbackMessage($input);

		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No feedback message");
		}
	}

	public function sync_to_server()
	{
		$this->validate_token();
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey,
			'DeviceUUID'=> $this->input_data->DeviceUUID,
			'webLogin'=> trim($this->input_data->webLogin)!="" ? $this->input_data->webLogin : "",
			'offlinedata' => $this->input_data->offlinedata
			);

		$data=$this->api_model->syncDataToServer($input);

		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("Sync failed due to some reasons");
		}
	}

	/*Added by Sabin @ 28th August 2015 >>*/
	public function sync_registrations($first_time=""){
		$this->validate_token();
		$input = array(
				'userid' => $this->input_data->userid,
				'first_sync'=> $first_time=="first" ? true : false,
				'deviceId' => $this->input_data->deviceId,
				'tokenkey' => $this->input_data->tokenkey
			);



		$data=$this->api_model->syncRegistrationData($input);

		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("Sync failed due to some reasons");
		}
	}

	public function newsettings(){
		$this->validate_token();
		$input = array(
				'userid' => $this->input_data->userid,
				'deviceId' => $this->input_data->deviceId,
				'tokenkey' => $this->input_data->tokenkey
			);

		$data=$this->api_model->getNewSettings($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No new messages");
		}
	}


	public function getavailablemodules(){
		$this->validate_token();
		$input = array(
				'userid' => $this->input_data->userid,
				'deviceId' => $this->input_data->deviceId,
				'tokenkey' => $this->input_data->tokenkey
			);

		$data=$this->api_model->getAvailableModules($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No modules enabled");
		}
	}

	public function getservertime(){
		$this->validate_token();
		$input = array(
				'userid' => $this->input_data->userid,
				'deviceId' => $this->input_data->deviceId,
				'tokenkey' => $this->input_data->tokenkey
			);

		$data = new stdClass;
		$data->date = date("Y-m-d");
		$data->hour = date("H");
		$data->minute = date("i");
		$data->second = date("s");
		$data->datetime = date("Y-m-d H:i");
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No new messages");
		}
	}

	public function sync_other_modules($first_time=""){
		$this->validate_token();

		$input = array(
				'userid' => $this->input_data->userid,
				'first_sync'=> $first_time=="first" ? true : false,
				'deviceId' => $this->input_data->deviceId,
				'tokenkey' => $this->input_data->tokenkey
			);





		$data=$this->api_model->syncOtherModules($input);

		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("Sync failed due to some reasons");
		}
	}


	public function sync_user_data($first_time=""){
		$this->validate_token();

		$input = array(
				'userid' => $this->input_data->userid,
				'first_sync'=> $first_time=="first" ? true : false,
				'deviceId' => $this->input_data->deviceId,
				'tokenkey' => $this->input_data->tokenkey
			);

		if(isset($this->input_data->DeviceUUID) && $this->input_data->DeviceUUID!=""){
			$input2 = array('DeviceUUID'=>$this->input_data->DeviceUUID);
			$input = array_merge($input,$input2);
		}

		$data=$this->api_model->syncUserData($input);

		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("Sync failed due to some reasons");
		}
	}


	public function activate_device(){
		$this->validate_token();
		$input = array(
					'verification_code'=> $this->input_data->verification_code,
					'username'=> $this->input_data->username,
					'password'=> $this->input_data->password,
					'deviceId'=> $this->input_data->deviceId,
					'deviceType'=> $this->input_data->devicetype
			);

		$data = $this->api_model->activate_device($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("Invalid verification code or password");
		}
	}




	/*Added by Sabin @ 28th August 2015 <<*/

 }
