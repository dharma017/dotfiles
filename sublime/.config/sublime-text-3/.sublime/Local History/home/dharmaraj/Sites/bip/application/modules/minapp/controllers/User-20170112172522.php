<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public $input_data = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('service');
		$this->load->helper('common');
		$this->load->model('minapp/service_model');

		/**
		 * Using php://input, a read-only stream, instead of PHP_POST variables. This is because the POST’ed data was not submitted as a key=>value pair (unlike normal form submissions). This method takes all of the raw submitted data in one chunk making it ideal for use with JSON as we need it all as one string before breaking it down.
		 */
		$input_data = json_decode(trim(file_get_contents('php://input')), true);
		$this->input_data=(object)$input_data;
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
	}


	public function index()
	{
		$this->load->view('service/user');
	}

	public function validateuser()
	{
		// echo "<pre>";print_r($this->input_data);exit;
		$input = array(
			'username' => $this->input_data->username,
			'password' => $this->input_data->password,
			'deviceId' => $this->input_data->deviceId,
			'UrbanAirshipId' => $this->input_data->identificationumber,
			'tokenkey' => $this->input_data->TokenKey,
			'devicetype' => $this->input_data->devicetype
			);

		//for sms login
		$this->load->model('login/login_model');

		$data=$this->service_model->validateuser($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("Ogiltigt användarnamn eller lösenord!");
		}
	}

	public function activeTasks()
	{
		$input = array(

			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey
			);

		$data=$this->service_model->getActiveTasks($input);

		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No active tasks");
		}

	}

	public function getTreatmentReminder()
	{
		$input = array(

			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey
			);

		$difficulty_id = $this->service_model->getPatientDifficulty($input['userid']);

		$data=$this->service_model->getPatientAppReminder($input['userid'],$difficulty_id);

		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No reminder available");
		}

	}

	public function saveTraining()
	{
		$lastUpdateDate = date("Y-m-d");
		$input = array(

			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey,

			'taskId' => $this->input_data->taskid,
			'trainingId' => $this->input_data->trainingId,
			'trainingdatetime' => date("Y-m-d h:i:s",strtotime($this->input_data->trainingdatetime)),
			'lastupdatedate' => date("Y-m-d",strtotime($lastUpdateDate . ' + 1 day')),
			'estimatedvalue' => $this->input_data->estimatedvalue,
			'estimatedvalue_end' => $this->input_data->estimatedvalue_end,
			'training_duration' => $this->input_data->training_duration,
			'type' => $this->input_data->type,
			'comment' => $this->input_data->comment,
			'played_time' => $this->input_data->played_time
			);
		$data=$this->service_model->saveTraining($input);

		if (isset($data)){
			$data1=$this->service_model->getOldTrainings($input);
			if (isset($data1)) {
				echo setokstatustrainings(json_encode($data),json_encode($data1));
			}else{
				echo setokstatus(json_encode($data));
			}
		}else{
			echo seterrorstatus(json_encode($data));
		}

	}

	public function getOldTrainings()
	{
		$lastUpdateDate = date("Y-m-d");
		$tomorrow_timestamp = strtotime('+1 day', strtotime($lastUpdateDate));

		$historyDate = (!empty($this->input_data->lastupdatedate)) ? date("Y-m-d",strtotime($this->input_data->lastupdatedate)): date("Y-m-d",$tomorrow_timestamp);
		$input = array(

			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey,

			'taskId' => $this->input_data->taskid,
			'lastupdatedate' => $historyDate
			);
		$data=$this->service_model->getOldTrainings($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("Du har inte övat ännu");
		}
	}

	public function GetActivityperweek()
	{
		$input = array(

			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey,

			'taskId' => $this->input_data->taskid
			);
		$data=$this->service_model->GetActivityperweek($input);
		if (isset($data)){
			echo setokstatus(str_replace(array('"[',']"'), array('[',']'), json_encode($data)));
		}else{
			echo seterrorstatus("Invalid data");
		}
	}

	public function Getestimatesfromstart()
	{
		$input = array(

			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey,

			'taskId' => $this->input_data->taskid
			);
		$data=$this->service_model->Getestimatesfromstart($input);
		if (isset($data)){
			echo setokstatus(str_replace(array('"[',']"'), array('[',']'), json_encode($data)));
		}else{
			echo seterrorstatus("Invalid data");
		}
	}

	public function GetActivityperday()
	{
		$input = array(

			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey
			);
		$data=$this->service_model->GetActivityperday($input);
		if (isset($data)){
			echo setokstatus(str_replace(array('"[',']"'), array('[',']'), json_encode($data)));
		}else{
			echo seterrorstatus("Invalid data");
		}

	}

	/*Added by Sabin begins >>*/
	/**
	 * Method to fetch Registrations for users
	 * @return [type] [description]
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   17th April 2015
	 */
	public function fetchRegistrations(){
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'show'	=> $this->input_data->show,
			'tokenkey' => $this->input_data->tokenkey
			);

		$data=$this->service_model->fetchRegistrations($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No Registrations");
		}

	}


	public function fetchRegistrationDetails(){
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey,
			'flow_type' => $this->input_data->flow_type,
			'registration_id' => $this->input_data->registration_id,
			'show' => $this->input_data->show
			);

		$data=$this->service_model->fetchRegistrationDetails($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No Registration Details");
		}
	}


	public function fetchRegistrationSteps(){
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey,
			'flow_type' => $this->input_data->flow_type,
			'flow_id'	=> $this->input_data->flow_id,
			'assignment_id' => $this->input_data->assignment_id,
			'registration_id' => $this->input_data->registration_id
			);

		$data=$this->service_model->fetchRegistrationSteps($input);
		if (isset($data) && !empty($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No Registration Steps");
		}
	}

	public function saveRegistration(){
		
		$input = array(
			"userid" => $this->input_data->userid,
			"deviceId" => $this->input_data->deviceId,
			"tokenkey" => $this->input_data->tokenkey,
			"form_data" => $this->input_data->form_data
		);

		$data=$this->service_model->saveRegistration($input);
		if (isset($data) && !empty($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No Registration Steps");
		}
	}
	/*Added by Sabin Ends << */

	/*Added by Sabin @ 25th June 2015 >>*/
	/**
	 * Method to fetch homeworks for users
	 * @return [type] [description]
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   22nd June 2015
	 */
	public function fetchHomeworks(){
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey
			);

		$data=$this->service_model->fetchHomeworks($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No Homeworks");
		}

	}

	public function markHomeworkRead(){
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey,
			'assignmentId' => $this->input_data->assignmentId
			);

		$data=$this->service_model->markHomeworkRead($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("Error");
		}
	}


	/**
	 * Method to fetch crisis plans for users
	 * @return [type] [description]
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   22nd June 2015
	 */
	public function fetchCrisisplans(){
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey
			);

		$data=$this->service_model->fetchCrisisplans($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("No Crisis Plans");
		}

	}

	public function markCrisisplanRead(){
		$input = array(
			'userid' => $this->input_data->userid,
			'deviceId' => $this->input_data->deviceId,
			'tokenkey' => $this->input_data->tokenkey,
			'planId' => $this->input_data->planId
			);

		$data=$this->service_model->markCrisisplanRead($input);
		if (isset($data)){
			echo setokstatus(json_encode($data));
		}else{
			echo seterrorstatus("Error");
		}
	}
	/*Added by Sabin @ 25th June 2015 <<*/
}

/* End of file user.php */
/* Location: ./application/modules/minapp/controllers/user.php */
