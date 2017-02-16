<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Minapp extends Public_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('minapp/minapp_model','messages/messages_model','setting/setting_model'));

	}

	public function index()
	{
		$usertype = getUserType();
		if ($usertype=='Psychologist') {
			$this->listUsers();
		} else {
			$username = $this->session->userdata('username');
			$this->view($username);
		}

	}

	public function listUsers()
	{
		$this->session->unset_userdata('minappLink');

		$total_app_message = $this->minapp_model->getTotalNewAppMessage();
		$this->session->set_userdata('total_app_message_temp', $total_app_message);

		$data["main_content"] = 'minapp/minapp/nouserlist';
		$data["template_header"] = 'includes/template_header';
		$data["template_footer"] = 'includes/template_footer';

		$data['app_users']=$this->minapp_model->getAllAppUsersByParams();
		$this->load->view('includes/template', $data);

	}

	public function view($username)
	{
			$usertype = getUserType();

		$data["main_content"] = 'minapp/minapp/user_details';
		$data["template_header"] = 'includes/template_header';
		$data["template_footer"] = 'includes/template_footer';

		// $data['userId']=$data['user']['id'];
		if ($usertype=='Psychologist')
			$data['userId']=$this->session->userdata('p_id');
		else
			$data['userId']=$this->session->userdata('user_id');

		// $data['user']=$this->minapp_model->getUserByUsername($username);
		$data['user']=$this->minapp_model->getUserByUserId($data['userId']);

		$data['usertype'] = getUserType();
		$data['username'] = $username;


		$total_app_message = $this->minapp_model->getTotalNewAppMessage();
		$this->session->set_userdata('total_app_message_temp', $total_app_message);

		$this->session->set_userdata( array('minappLink'=>'index.php/minapp/view/'.$username) );

		$data['tasks']=$this->minapp_model->getUserAppTrainings($data['user']);
		// echo "<pre>";print_r($data['tasks']);exit;

		$this->load->view('includes/template', $data);
	}

	public function filterUserByType()
	{
		$data['app_users']=$this->minapp_model->getAllAppUsersByParams();
		$this->load->view('minapp/minapp/userlist_ajax', $data);
	}

	public function generatexls(){

		$_POST['diffId'] = $difficulty_id= $this->input->post('difficulty_id');
		$problem_id= $this->input->post('problem_id');
		if ($difficulty_id && $problem_id) {
		   $_POST['filterType'] = 'problem';
		   $_POST['filterId'] = $problem_id;
		}elseif($difficulty_id && !$problem_id){
		   $_POST['filterType'] = 'treatment';
		   $_POST['filterId'] = $difficulty_id;
		}
		$output=$this->minapp_model->getAllAppUsersByParams();
		echo json_encode($output);
	}

	public function exportxls()
	{
		$filename=$this->input->post('filename').'.'.$this->input->post('format');
		$results= json_decode($this->input->post('content'));
		// echo "<pre>";print_r($results);exit;
		$trainings=array();
		foreach ($results as $user) {
			$trainings[]=$this->minapp_model->getStatTrainingsByUserId($user);
		}
		// echo "<pre>";print_r($trainings);exit;
		$newarr=array();
		foreach ($trainings as $training) {
			foreach ($training as $train) {
				$newarr[]=$train;
			}
		}
		$data['results']=$newarr;
		// echo "<pre>";print_r($newarr);exit;
		$xlsView=$this->load->view("minapp/minapp/excel_stats_app_user", $data,true);
		// echo "<pre>";print_r($xlsView);exit;
		$this->load->helper('download');
		force_download($filename, $xlsView);
	}

	function getProblemOptions()
	{
		$json=$this->input->post('newVal');
		$ids=(is_array($json)) ? join(',',$json): $json;
		$records=$this->minapp_model->getProblemsPerDifficulty($ids);
		$output = null;
		foreach ($records as $row)
		{
			if($this->input->post('task_id')){
				$status=(in_array($row->id, $probArr)) ? 'selected="selected"': '';
				$output .= '<option value="'.$row->id.'" '.$status.'>'.$row->problem.'</option>';
			}else{
				$output .= '<option value="'.$row->id.'">'.$row->problem.'</option>';

			}
		}
		echo $output;
	}

	/**
	 * assign task to patients by Psychologist form
	 */
	public function assignToUserForm()
	{
		$data['usertype'] = getUserType();

		$data['diffId']=$this->input->post('diffId');
		$data['user_id']=$this->input->post('user_id');
		$data['username']=$this->input->post('username');

		$data['problems']=$this->minapp_model->getProblemsPerDifficulty($data['diffId']);

		$psychologist_id = $this->session->userdata("user_id");

		$assignedPids=$this->minapp_model->getAssignedProblems($psychologist_id);
		if (!empty($assignedPids)) {

			$json=json_decode($assignedPids,true);
			$data['problemsList']=$json[$data['user_id']];
		}

		$this->load->view('minapp/minapp/assign_tasks', $data);
	}

	/**
	 * add task to mobile by patient form
	 */
	public function addTaskToMobileForm()
	{
		$data['usertype'] = getUserType();

		$data['diffId']=$this->input->post('diffId');
		$data['user_id']=$this->input->post('user_id');
		$data['username']=$this->input->post('username');

		$data['problems']=$this->minapp_model->getProblemsPerDifficulty($data['diffId']);

		$data['user']=$this->minapp_model->getUserByUsername($data['username']);

		$assignedPids=$this->minapp_model->getAssignedProblems($data['user']['psychologist_id']);
		if (!empty($assignedPids)) {

			$json=json_decode($assignedPids,true);

			$data['problemsList']=$json[$data['user_id']];
		}

		$this->load->view('minapp/minapp/add_user_task', $data);
	}

	function getTaskOptions()
	{
		$user_id=$this->input->post('user_id');
		$diffId=$this->input->post('diffId');

		$json=$this->input->post('newVal'); //problem ids
		$newrecords=array();
		$newrecords1=array();

		if (is_array($json)) { // psychologist
			foreach($json as $k => $id) {
				$records=$this->minapp_model->getTasksOptionPerUserNotSet($id,$user_id,$diffId);
				$newrecords1[]=$records;
			}
			foreach ($newrecords1 as $new) {
				foreach ($new as $val) {
					$newrecords[]=$val;
				}
			}
		}else{  // patient
			$newrecords=$this->minapp_model->getTasksOptionPerUserNotSet($json,$user_id,$diffId);
		}
		$output = null;
		foreach ($newrecords as $row)
		{
			$output .= '<option value="'.$row->id.'">'.$row->task.'</option>';
		}
		echo $output;
	}

	public function assignTaskToUser()
	{
		$this->minapp_model->assignTaskToUser();
	}

	public function unassignTaskToUser()
	{
		$this->minapp_model->unassignTaskToUser();
	}

	public function addTaskByPatient()
	{
		$response = $this->minapp_model->addTaskByPatient();
		echo $response;
	}

	public function submitAppComment()
	{
		$newId = $this->minapp_model->saveAppComment();

		$usertype = getUserType();

		$comment = $this->minapp_model->getAppCommentDetail($newId);

		$commentorId=($comment->usertype=='user') ? $comment->user_id: $comment->psychologist_id;

		$sender= $commentor=$this->minapp_model->getAppUserDetail($commentorId);

		$commentor->first_name = $this->encryption->decrypt($commentor->first_name);
		$commentor->last_name = $this->encryption->decrypt($commentor->last_name);
		$comment->comments = $this->encryption->decrypt($comment->comments);

		$commentClass=($usertype=='Psychologist') ? "commentContentAlt": "commentContent";

		$receiverId=($usertype=='Psychologist') ? $comment->user_id: $comment->psychologist_id;

		$receiver=$this->minapp_model->getAppUserDetail($receiverId);

		$this->minapp_model->notifyMessage($newId,$sender->id,$receiver->id,2);

		echo '
			<div class="' . $commentClass . '" id="container_' . $newId . '">
				<a  onclick="deleteCommentTraining(' . $newId . ')"><div class="close" onclick="close" id=""></div></a>
					<p>
						<label>
							' . $commentor->first_name . ' ' . $commentor->last_name . '
						</label>
						<span class="commentDescrip">
							' . nl2br($comment->comments) . '
							<span class="date">
								' . $comment->posted_on . '
							</span>
						</span>
					</p>
				</div>

				';

	}

	public function deleteAppComment()
	{
		$this->minapp_model->deleteAppComment();
	}

	public function toggleTaskStatus()
	{
		$this->minapp_model->toggleTaskStatus();
	}

	public function polling(){
		echo $this->session->userdata('total_app_message_temp');
	}

	public function getGraphInput()
	{
		$data=$this->minapp_model->getGraphInput();
		header('Content-type: application/json');
		echo json_encode($data);
	}

	public function activityReport($username,$task_id)
	{
		$data["main_content"] = 'minapp/minapp/activity_report';
		$data["template_header"] = 'includes/template_header';
		$data["template_footer"] = 'includes/template_footer';

		$usertype = $data['usertype'] = getUserType();
		$data['task_id'] = $task_id;
		$data['username'] = $username;
		$data['user']=$this->minapp_model->getUserByUsername($username);

		$psychologist_id = $data['user']['psychologist_id'];
		$patient_id = $data['user']['id'];

		$data['comments'] = $this->minapp_model->toggleTaskComment($psychologist_id,$patient_id,$task_id,$usertype);

		$data['training'] = $this->minapp_model->getTrainingInfo($patient_id,$task_id);

		$this->load->view('includes/template', $data);
	}

	public function createPatientSpecificTask()
	{
		$this->minapp_model->createPatientSpecificTask();
	}

	/**
	 * change reminder setting for each patient
	 */
	public function changeReminderSettings()
	{
		$data['usertype'] = getUserType();

		$data['diffId']=$this->input->post('diffId');
		$data['user_id']=$this->input->post('user_id');
		$data['username']=$this->input->post('username');

		$data["notification"] = $this->minapp_model->isNotificationEnabled($data['user_id']);

		$data['user']=$this->minapp_model->getUserByUsername($data['username']);

		$psychologist_id = $this->session->userdata("user_id");

		$this->load->view('minapp/minapp/change_reminder', $data);

	}

	/**
	 * populate reminder for psychologist/patient type
	 */
	public function fillDiffFormReminder()
	{
		$data=$this->minapp_model->getReminderByDifficultyPerUser();

		$output = null;

		$reminderInputs = json_decode($data['app_reminder']);

		$app_reminder_type = $this->input->post('app_reminder_type');

		if (!empty($reminderInputs)) {
			foreach ($reminderInputs as $k => $value) {
				$params = explode('~~~', $value);
				if ($app_reminder_type) { //individual reminder
					$output .= '<li>
							<div style="width:100%; float:left;">
								<label class="" style="width:212px;margin-left: 13px; margin-top: 0px;"><b>Tid:</b></label>
								<input style="height:24px;padding:4px;" type="text" class="reminder_time" size="10" name="p_time_'.$k.'" value="'.$params[0].'"  />
							</div>
							<div style="width:100%; float:left; margin-top: 8px;">
								<label class="" style="width:212px;margin-left: 13px; margin-top: 0px;"><b>Notifieringsmeddelande:</b></label>
								<input type="text"  style="height:24px;padding:4px;margin-left: 13px; " size="50" name="p_scnt_'.$k.'" value="'.$params[1].'"  />
								<a href="#" class="delmsg"  style="margin:5px 10px 0; ">Ta bort (X)</a>
							</div>
							</li>';

				}else{ //global reminder
					$output .= '<li>
							<div style="width:100%; float:left;">
								<label class="" style="width:212px;margin-left: 13px; margin-top: 0px;"><b>Tid:</b></label>
								<span>'.$params[0].'</span>
							</div>
							<div style="width:100%; float:left; margin-top: 8px;">
								<label class="" style="width:212px;margin-left: 13px; margin-top: 0px;"><b>Notifieringsmeddelande:</b></label>
								<span>'.$params[1].'</span>
							</div>
							</li>';
				}
			}
		}
		echo $output;exit;
	}

	/**
	 * save push reminder notification per difficulty based on time by psychologist or patient
	 */
	public function savePushReminder()
	{
			$this->load->library('form_validation');

			$count = $this->input->post('frm_count');
			for ($i=0; $i <$count; $i++) {
				$this->form_validation->set_rules('p_time_'.$i,'lang:time','trim|required|xss_clean');
				$this->form_validation->set_rules('p_scnt_'.$i,'Notifieringsmeddelande','trim|required|xss_clean');
			}
	  $this->form_validation->set_error_delimiters('','');

	  if ($this->form_validation->run() == FALSE) {
		 echo json_encode(array('error'=>validation_errors()));
	  }else{

		$response=$this->minapp_model->changePushReminder();
		echo json_encode(array('success'=>'success'));
	  }

	}

	/*Added by sabin @ 3rd April 2015*/
	/**
	 * Method to list Registration task related to the patient
	 * @return none
	 */
	public function showRegistrationTaskList()
	{
		$data['usertype'] = getUserType();

		$data['diffId']=$this->input->post('diffId');
		$data['user_id']=$this->input->post('user_id');
		$data['username']=$this->input->post('username');

		$data['user']=$this->minapp_model->getUserByUsername($data['username']);

		$psychologist_id = $this->session->userdata("user_id");
		$data['registration_tasks'] = $this->minapp_model->getRegistrationByDifficultyID($data['diffId']);

		$this->load->view('minapp/minapp/registration_task', $data);

	}

	public function getFlowsByRegID(){
		$flows = $this->minapp_model->getFlowsByRegID();
		if(count($flows)>0){
			$html = "<option value=''>".lang("txt_select")."</option>";
			foreach($flows as $flow){
				$html .="<option data-regid='".$flow->registration_id."' value='".$flow->flow_id."'>".$flow->flow_name."</option>";
			}
			echo $html;
		}else{
			echo "failed";
		}
		exit;
	}

	public function getRegistrationUserAnswers(){
		$data["answers"] = $this->minapp_model->getRegistrationUserAnswers();
		$data["step_name"] = $this->input->post("step_name");
		$data["step_id"] = $this->input->post("step_id");
		$data["patient_id"] = $this->input->post("user_id");
		$data["template"] = $this->input->post("template");
		$this->load->view("minapp/minapp/registration_user_answers",$data);

	}

	public function getAllStepsByRegID(){
		return $this->minapp_model->getAllStepsByRegID();
	}

	public function addRegAnswerForPatient(){
		return $this->minapp_model->addRegAnswerForPatient();
	}

	public function addNewAnswerWithCatForPatient(){
		return $this->minapp_model->addNewAnswerWithCatForPatient();
	}

	public function updateCustomRegAnswer(){
		return $this->minapp_model->updateCustomRegAnswer();
	}

	public function updateCustomRegAnswerCategory(){
		return $this->minapp_model->updateCustomRegAnswerCategory();
	}

	public function deleteCustomRegAnswer(){
		return $this->minapp_model->deleteCustomRegAnswer();
	}

	public function deleteCustomRegAnswerCat(){
		return $this->minapp_model->deleteCustomRegAnswerCat();
	}

	public function sortRegistrationAnswers(){
		return $this->minapp_model->sortRegistrationAnswers();
	}

	public function addRegAnswerCategoryForPatient(){
		return $this->minapp_model->addRegAnswerCategoryForPatient();
	}


	/*Added by sabin @ 22nd June 2015 >>*/
	/**
	 * Method to list Homeworks related to the patient
	 * @return none
	 */
	public function showHomeworkList()
	{
		$data['usertype'] = getUserType();

		$data['diffId']=$this->input->post('diffId');
		$data['user_id']=$this->input->post('user_id');
		$data['username']=$this->input->post('username');

		$data['user']=$this->minapp_model->getUserByUsername($data['username']);

		$data["psychologist_id"] = $this->session->userdata("user_id");
		$data['homeworks'] = $this->minapp_model->getHomeworksByDifficultyID($data['diffId'],$data['user_id']);

		$this->load->view('minapp/minapp/my_homeworks', $data);

	}

		public function showActivationModulesList()
	{
		$difficultyId = $this->input->post('diffId');
		$patient_id = $this->input->post('user_id');

		$row = $this->db->query("SELECT manual_active_modules FROM bip_user_app WHERE user_id='$patient_id'")->row();
		$data['unserialize_data'] = json_decode($row->manual_active_modules,true);

		$data['homeworks'] = $this->minapp_model->getHomeworkByDifficultyId($difficultyId);
		$data['skills_modules'] = $this->minapp_model->getSkillsModulesByDifficultyId($difficultyId);

		$this->load->view('minapp/minapp/update_my_activation_modules', $data);

	}

	public function manualActivationModules()
	{
		$patient_id = $this->session->userdata('p_id');
			//to safely serialize
		$json = json_encode($_POST);

		//first check entry exist or not
		$check = $this->db->query("SELECT count(*) as cnt FROM bip_user_app WHERE user_id='$patient_id'")->row();
		if($check->cnt>0){
			$this->db->query("UPDATE bip_user_app set manual_active_modules='$json' WHERE user_id= '$patient_id'");
		}else{
			$this->db->query("INSERT INTO bip_user_app SET user_id= '$patient_id', manual_active_modules='$json', created_at='".date("Y-m-d")."', updated_at='".date("Y-m-d H:i:s")."'");
		}

	}

	public function formPublishHomework(){
		//"&publishedBy="+$(this).attr("data-pubby")
		$data["patient_id"] = $this->input->post("patientID");
		$data["homework_id"] = $this->input->post("homeworkID");
		$data["published_date"] = $this->input->post("pubDate");
		$data["published_by"] = $this->input->post("publishedBy");
		$data["difficulty_id"] = $this->input->post("difficultyID");
		$data["username"] = $this->input->post("userName");
		$this->load->view("minapp/minapp/form_publish_homework",$data);
	}

	public function saveHwPublishSettings(){
		$this->minapp_model->saveHwPublishSettings();
	}


	/**
	 * Method to list Crisis plans related to the patient
	 * @return none
	 */
	public function showCrisisplanList()
	{
		$data['usertype'] = getUserType();

		$data['diffId']=$this->input->post('diffId');
		$data['user_id']=$this->input->post('user_id');
		$data['username']=$this->input->post('username');

		$data['user']=$this->minapp_model->getUserByUsername($data['username']);

		$data["psychologist_id"] = $this->session->userdata("user_id");
		$data['crisisplans'] = $this->minapp_model->getCrisisplansByDifficultyID($data['diffId'],$data['user_id']);

	  /*  $data["patient_id"] = $this->input->post("patientID");
		$data["published_by"] = $this->input->post("publishedBy");
		$data["difficulty_id"] = $this->input->post("difficultyID");
		$data["username"] = $this->input->post("userName");*/
		$data["standard_crisis_plans"] = $this->minapp_model->getStandardCrisisplansByDifficulty($data["diffId"]);

		$this->load->view('minapp/minapp/my_crisis_plans', $data);

	}

	/*Method to list out Standard crisis plan so that psychologist can create new one for the patient*/
	 public function listStandardCrisisPlans(){
		/*'difficultyID=<?=$diffId?>&userName=<?=$username?>&patientID=<?=$user_id?>&publishedBy=<?=$psychologist_id?>'*/
		$data["patient_id"] = $this->input->post("patientID");
		$data["published_by"] = $this->input->post("publishedBy");
		$data["difficulty_id"] = $this->input->post("difficultyID");
		$data["username"] = $this->input->post("userName");
		$data["standard_crisis_plans"] = $this->minapp_model->getStandardCrisisplansByDifficulty($data["difficulty_id"]);
		$this->load->view("minapp/minapp/list_standard_crisis_plans",$data);
	}

	public function saveCustomCrisisPlan(){
		$this->minapp_model->saveCustomCrisisPlan();
	}

	public function changeCustomCrisisPlanStatus(){
		$this->minapp_model->changeCustomCrisisPlanStatus();
	}

	public function showEditCustomCrisisPlan(){
		$data["patient_id"] = $this->input->post("patientID");
		$data["published_by"] = $this->input->post("publishedBy");
		$data["difficulty_id"] = $this->input->post("difficultyID");
		$data["plan_id"] = $this->input->post("plan_id");
		$data["username"] = $this->input->post("userName");
		$data["contents"] = $this->minapp_model->getCustomCrisisPlanById($data["plan_id"]);
		$this->load->view("minapp/minapp/edit_custom_crisis_plan",$data);
	}
	/*Added by sabin @ 22nd June 2015 <<*/

	/*Added by sabin @20th July 2015 >> */
	public function fetchPatientAnsweredRegistrations(){
		$patient_id = $this->input->post("userID");
		$data["patient_id"] = $patient_id;
		$data["registrations"] = $this->minapp_model->fetchPatientAnsweredRegistrations($patient_id);
		$data["reg_graphs"] = $this->minapp_model->viewRegistrationgraphs($patient_id);
		$this->load->view("minapp/minapp/fetch_patient_all_registration",$data);
	}

	public function fetchPatientsCustomAnswer(){
		$patient_id = $this->input->post("userID");
		$data["patient_id"] = $patient_id;
		$data["difficulty_id"] = $this->input->post("difficulty_id");
		$data["registrations"] = $this->minapp_model->fetchPatientsCustomAnswerRegistration($patient_id);
		$this->load->view("minapp/minapp/fetch_patient_custom_answers",$data);
	}

	public function deletePatientAnswer(){
		$this->minapp_model->deletePatientAnswer($this->input->post("answerID"));
	}

	public function updatePatientAnswer(){
		$this->minapp_model->updatePatientAnswer();
	}

	public function saveSpecialAnswer(){
		$this->minapp_model->saveSpecialAnswer();
	}

	public function saveSelectedAnswers(){
		$this->minapp_model->saveSelectedAnswers();
	}

	public function viewRegistrationgraphs(){
		$patient_id = $this->input->post("userID");
		$data["patient_id"] = $patient_id;
		$this->load->view("minapp/minapp/view_registration_graph",$data);
	}

	public function registrationToExcel($patientID){
		$data["registrations"] = $this->minapp_model->registrationToExcel($patientID);
		$data["patientID"] = $patientID;
		$username = $this->minapp_model->getUserNameById($patientID);
		$data["username"] = $username;
		$this->load->view("minapp/minapp/registration_to_excel",$data);
	   // exit;

		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=BIP_" . $username . "_" . time() . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
	}
	/*Added by sabin @20th July 2015 << */

	/*Added by sabin @5th August 2015 >> */
	public function addNewRegAnswers(){
		$data["patient_id"] = $this->input->post("userID");
		$data["registrations"] = $this->minapp_model->getRegistrationByDifficultyID($this->input->post("difficulty_id"));
		$this->load->view("minapp/minapp/add_new_registration_answer",$data);
	}

	public function getAllSteps(){
		$registration_id = $this->input->post("registration_id");
		$this->minapp_model->getAllSteps($registration_id);
	}

	public function getAllAnswerCats(){
		$step_id = $this->input->post("step_id");
		$this->minapp_model->getAllAnswerCats($step_id);
	}

	public function saveNewCustomAnswer(){
		$this->minapp_model->saveNewCustomAnswer();
	}

	public function addNewPatientExposure(){
		$this->minapp_model->addNewPatientExposure();
	}

	public function removePatientExposure(){
		$this->minapp_model->removePatientExposure();
	}

	public function completePatientExposure(){
		$this->minapp_model->completePatientExposure();
	}
	/*Added by sabin @5th August 2015 << */

	/*Added by sabin @29th September 2015 >> */
	public function viewActivityThoughts(){
		$data['user_id']=$this->input->post('user_id');

		$data['activity_thoughts'] = $this->minapp_model->getActivityThoughts($data['user_id']);

		$this->load->view('minapp/minapp/view_activity_thoughts', $data);
	}

	public function viewActivityFeelings(){
		$data['user_id']=$this->input->post('user_id');

		$feelings = $this->minapp_model->getActivityFeelings($data['user_id']);

		$data['activity_stats'] = $feelings["stats"];
		$data["activity_list"] = $feelings["lists"];

		$this->load->view('minapp/minapp/view_activity_feelings', $data);
	}

	public function viewActivitySkills(){
		$data['user_id']=$this->input->post('user_id');

		$data['activity_skills'] = $this->minapp_model->getActivitySkills($data['user_id']);

		$this->load->view('minapp/minapp/view_activity_skills', $data);
	}

	public function generateOneTimeCode(){

		$data["patient_id"] = $this->input->post("patient_id")>0 ? $this->input->post("patient_id") : 0;
		$data["is_already_activated"] = $this->minapp_model->checkifAlreadyActivated($data["patient_id"]);
		if($data["patient_id"]>0){

			$data["error"] = "";
		}else{
			$data["error"] = "No patient Selected";
		}
		$this->load->view('minapp/minapp/one_time_login_code', $data);

	}

	public function manageTicLevels($ticVersion=2){
	  //  echo "<pre>".print_r($this->session->userdata,true)."</pre>"; exit;
		$userroletype = strtolower($this->session->userdata("user_role_type"));
		if($userroletype=="patient"){
			$data["patient_id"] = $this->session->userdata("user_id");
		}else{
			$data["patient_id"] = $this->input->post("patient_id")>0 ? $this->input->post("patient_id") : 0;
		}
		$data["tic_version"] = $ticVersion;
		$data["user_type"] = strtolower(getUserType());
		$data["userroletype"] = $userroletype;
		if($data["patient_id"]>0){

			$data["error"] = "";
		}else{
			$data["error"] = "No patient Selected";
		}
		$this->load->view('minapp/minapp/manage_tic_levels', $data);

	}

	public function exportIicsToXLS($type=0){
		//$type=1 , only between estimates, type=2 final estimates, type=0 both
		$userid = $this->session->userdata("p_id");

		$data["tic_data_2"] = $this->minapp_model->getTicV2DataforXLS($userid);
		$data["tic_data_1"] = $this->minapp_model->getTicV1DataforXLS($userid);
		$data["user_details"] = $this->minapp_model->getUserByUserId($userid);
		$data["tics_show_type"] = $type;

		//for v1
		$data["max_intervals"] = $this->minapp_model->getMaxNumberofIntervalsTicsV1($userid);
		$view = 'minapp/minapp/excel_stats_tics';

		$xlsView=$this->load->view($view, $data,true);

		if (ENVIRONMENT=='development') {
			echo $xlsView;exit;
		}

		$this->load->helper('download');
		force_download('export_tics.xls', $xlsView);
	}

	public function generateCode(){
		$data["gen_code"] = mt_rand(10000000, 99999999);
		$data["patient_id"] = $this->input->post("patient_id");
		$this->minapp_model->saveOneTimeCode($data);
		echo $data["gen_code"];
		exit;
	}

	public function enableDisableReminder(){
		$data["dowhat"] = $this->input->post("dowhat");
		$data["patient_id"] = $this->input->post("patient_id");

		$return = $this->minapp_model->enableDisableReminder($data);

		echo json_encode($return);
		exit;

	}

	/*Added by sabin @29th September 2015 << */

	/**
	 * override activation for patient that is set by superadmin in template
	 * @return [type] [description]
	 */
}

/* End of file minapp.php */
/* Location: ./application/modules/minapp/controllers/minapp.php */
