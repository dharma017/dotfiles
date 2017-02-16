<?php
class Admin extends Admin_Controller
{

   	function __construct()
   	{
		parent::__construct();

		$this->load->model(array('setting/setting_model','minapp/minapp_model','paging/paging_model'));
   	}

   	public function index()
   	{
   		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$data["main_content"] = 'minapp/admin/home';

	 	$this->load->view('includes/admin/template',$data);

 	}

 	/**
 	 * push notification for feedback based on days
 	 */
 	public function savePushNotification()
	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('feedback_message','Feedback message','trim|xss_clean|min_length[2]');
        $this->form_validation->set_rules('feedback_xdays','Feedback after x days','trim|xss_clean|is_natural');
        $this->form_validation->set_rules('reminder1_message','Reminder 1 message','trim|xss_clean|min_length[2]');
        $this->form_validation->set_rules('reminder1_xdays','Reminder 1 after x days','trim|xss_clean|is_natural');
        $this->form_validation->set_rules('reminder2_message','Reminder 2 message','trim|xss_clean|min_length[2]');
        $this->form_validation->set_rules('reminder2_xdays','Reminder 2 after x days','trim|xss_clean|is_natural');
        $this->form_validation->set_error_delimiters('','');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error'=>validation_errors()));
        }else{
            $response=$this->minapp_model->savePushNotification();
            echo json_encode(array('success'=>'success'));

        }
	}

	/**
	 * save push reminder notification per difficulty based on time
	 * @return [type] [description]
	 */
	public function savePushReminder()
	{
        $this->load->library('form_validation');

        $count = $this->input->post('frm_count');
        for ($i=0; $i <$count; $i++) {
            $j = $i+1;
            $this->form_validation->set_rules('p_time_'.$i,'Push reminder time '.$j,'trim|required|xss_clean');
            $this->form_validation->set_rules('p_scnt_'.$i,'Push reminder message '.$j,'trim|required|xss_clean|min_length[2]');
        }
       $this->form_validation->set_error_delimiters('','');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error'=>validation_errors()));
        }else{
            $response=$this->minapp_model->savePushReminder();
            echo json_encode(array('success'=>'success'));

        }
	}


	/*Added by sabin begins here >> */

	/**
	 * Method to list registration task
	 * @author Sabin Chhetri
	 * @date 24th March 2015
	 * @return nothing
	 */
	public function listAllRegistrationTasks(){
		//$data["filterId"] = $this->input->post("filterId")>0 ? $this->input->post("filterId"): DefaultDifficulty();
		$data["filterType"] = "treatment";
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_registration_tasks_view_ajax',$data);
	}


	/**
	 * Show Registration Task form to add new task
	 */
	public function addRegistrationTasksForm(){
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/add_registration_task',$data);

 	}


 	/**
 	 * Save the registration task
 	 */
 	public function addRegistrationTask()
 	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('registration_name','Registration name','trim|required|xss_clean|min_length[2]');
        $this->form_validation->set_rules('difficulty_id[]','Difficulty options','required');
        $this->form_validation->set_rules('bar_color','Color','trim|required|xss_clean|exact_length[6]');
        $this->form_validation->set_error_delimiters('','');
        if ($this->form_validation->run()==FALSE) {
            if ($this->input->post('registration_id')) {
                $this->editRegistrationTask();
            }else{
                $this->addRegistrationTasksForm();
            }
            $res["error_code"] = "error";
            $res["error_msg"] = validation_errors();
            echo json_encode($res);exit;
        }else{
            $this->minapp_model->addRegistrationTask();
        }
 	}

 	public function changeRegistrationStatus(){
 		$this->minapp_model->changeRegistrationStatus();
 	}

 	public function editRegistrationTask()
	{
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$data['task']=$this->minapp_model->getRegistrationById();
		$data['sub_items'] = $this->minapp_model->checkFlowsStepsOnTask();

 		$this->load->view('minapp/admin/add_registration_task',$data);
	}

	public function listAllRegistrationTasksAjax()
	{
		$filterID = DefaultDifficulty();
		$data["filterId"] = $filterID;
		$data["filterType"] = "treatment";
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_registration_tasks_view_ajax',$data);
	}

	public function listAllRegistrationFlowsAjax(){
		$data = "";
		$this->load->view('minapp/admin/manage_registration_tasks_view_ajax',$data);
	}

	public function listAddRegistrationFlow(){
		$data["registration_id"] = $this->input->post("registration_id");
		$data["registration_name"] = $this->minapp_model->getRegistrationById();
		$data["flow"] = $this->minapp_model->getFlowStuffsByRegId();
		$this->load->view('minapp/admin/manage_registration_flow_view_ajax',$data);
	}

	public function listSteps(){
		$data["registration_name"] = $this->minapp_model->getNameById("bip_registration_task","registration_name","registration_id",$this->input->post("registration_id"));
		$data["flow_name"] = $this->minapp_model->getNameById("bip_registration_flows","flow_name","flow_id",$this->input->post("flow_id"));
		$data["difficulty"] = $this->minapp_model->getDifficultyByRegIDs($this->input->post("registration_id"));
		$data["registration_id"] = $this->input->post("registration_id");
		$data["flow_id"] = $this->input->post("flow_id");
		$this->load->view("minapp/admin/manage_registration_steps_view_ajax",$data);
	}

	public function sortRegistrationSteps(){
		return $this->minapp_model->sortRegistrationSteps();
	}

	public function sortRegistrationTasks(){
		return $this->minapp_model->sortRegistrationTasks();
	}

	/**
 	 * Save the registration task
 	 */
 	public function addRegistrationFlow()
 	{
 		$this->minapp_model->addRegistrationFlow();
 	}

 	public function changeFlowStatus(){
 		$this->minapp_model->changeFlowStatus();
 	}

 	public function changeRegStepStatus(){
 		$this->minapp_model->changeRegStepStatus();
 	}


 	public function editFlow(){
 		$this->minapp_model->editFlow();
 	}

 	public function getTemplatePage(){
 		$data["details"] = $this->minapp_model->getTemplateSpecificStuffs();
 		$data["template"] = $this->input->post("template");
 		$data["registration_id"] = $this->input->post("registration_id");
        $data["flow_id"] = $this->input->post("flow_id");
        $data["step_id"] = $this->input->post("step_id");

        $total_steps = $this->minapp_model->totalRegistrationStepRows($this->input->post("registration_id"),$this->input->post("flow_id"));
        if($this->input->post("step_id")>0){
        	$data["total_steps"] = $total_steps > 0 ? $total_steps :1;
        }else{
        	$data["total_steps"] = $total_steps > 0 ? $total_steps+1 :1;
        }

        $current_step = $data["details"]["steps"][0]->sort_order;
        $data["current_step"] = $current_step > 0? $current_step: $total_steps+1 ;
 		$this->load->view("minapp/admin/registration_step_page",$data);
 	}

 	public function removeStepAnswer(){
 		return $this->minapp_model->removeStepAnswer();
 	}

 	public function removeStepAnswerCat(){
 		return $this->minapp_model->removeStepAnswerCat();
 	}

 	public function saveRegistrationSteps(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('step_title','Step title','trim|xss_clean|min_length[2]');
        $this->form_validation->set_rules('answer_text','Answer text','trim|xss_clean');
        $this->form_validation->set_rules('button_text','Button text','trim|xss_clean');
        $this->form_validation->set_rules('step_subheading','Step subheading','trim|xss_clean');
        $this->form_validation->set_rules('step_subheading','Step subheading','trim|xss_clean');
        $this->form_validation->set_rules('answers[]','Answer','trim|xss_clean');
        //$this->form_validation->set_rules('max_selection_allowed','Max selection allowed','trim|xss_clean|is_natural_no_zero');
        $this->form_validation->set_rules('answers_category[]','Answer category','trim|xss_clean');
        $this->form_validation->set_error_delimiters('','');
        if ($this->form_validation->run()==FALSE) {
            echo validation_errors();exit;
        }else{
            return $this->minapp_model->saveRegistrationSteps();
        }
 	}

	public function saveAnswerCategory(){
		return $this->minapp_model->saveAnswerCategory();
	}

	public function showMappingInterface(){
		$data["standard_answers"] = $this->minapp_model->fetchStandardAnswers();
		$this->load->view("minapp/admin/map_custom_answers",$data);
	}

	public function saveStandardAnswer(){
		return $this->minapp_model->saveStandardAnswer();
	}

	public function getCustomAnswersToMap(){
		$data['option_to_map']=$this->input->post("option_to_map");
		$this->load->view('minapp/admin/map_custom_answers_ajax',$data);
	}

	public function mapSelectedAnswers(){
		return $this->minapp_model->mapSelectedAnswers();
	}
	/*Added by Sabin ends here <<*/


 	/**
 	 * shows list of tasks (show more after 50 rows).
 	 * list can be filtered by Treatment.
 	 * once treatment is selected the second drop-down will show problem
 	 * categories of that treatment that will further filter the list.
 	 */
 	public function listAllTasks()
	{
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_tasks_view_outer',$data);
	}

	public function listAllTasksAjax()
	{
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_tasks_view_ajax',$data);
	}

	/**
	 * shows list of problem categories for the available difficulties
	 * The purpose of problem categories is to make it easier
	 * to select the good tasks to practice on
	 */
	public function listAllTreatments()
	{
		$this->load->view('minapp/admin/manage_treatments_view');
	}

	public function listAllTreatmentSettings()
	{
		$this->load->view('minapp/admin/manage_treatment_settings');
	}

 	public function addTasksForm(){
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/add_task',$data);

 	}

 	/**
 	 *  Select treatment (multiple choise)
 	 *  Select problem category within that treatment (multiple choise)
 	 *  Enter the task in a single sentence. (but don't limit the nr of characters)
 	 */
 	public function addTask()
 	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('task','Task name','trim|required|xss_clean|min_length[2]');
        $this->form_validation->set_error_delimiters('','');
        if ($this->form_validation->run() == FALSE) {
            $status = validation_errors();
        }else{
            $this->minapp_model->addTask();
            $status = true;
        }
        echo json_encode($status);exit;
 	}

 	public function addTreatmentsForm(){
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/add_treatment',$data);

 	}

 	public function setTreatmentForm(){
 		$data['difficulties']=$this->minapp_model->getAllDifficultyNotInSettings();
		$this->load->view('minapp/admin/set_treatment',$data);

 	}

 	/**
 	 * Set the rating for difficulty
 	 * single rating = 1
 	 * double rating = 2
 	 */
 	public function setTreatment()
 	{
        $this->load->library('form_validation');

        if ($this->input->post('frm_name')=='frmSetTreatment') {
            $this->form_validation->set_rules('rating','rating','required');            
        }else if ($this->input->post('frm_name')=='frmSlide_1') {
            $this->form_validation->set_rules('anxiety','Anxiety','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('ten','Ten rating text','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('zero','Zero rating text','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('txt_button','Button text','trim|xss_clean|min_length[2]');
        }else if ($this->input->post('frm_name')=='frmSlide1') {
            $this->form_validation->set_rules('slide1_headline','Headline','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide1_text','Description','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide1_button','Button text','trim|xss_clean|min_length[2]');
        }else if ($this->input->post('frm_name')=='frmSlide2') {
            $this->form_validation->set_rules('slide2_headline','Headline','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide2_ten','Ten rating text','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide2_zero','Zero rating text','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide2_button','Button text','trim|xss_clean|min_length[2]');
        }else if ($this->input->post('frm_name')=='frmSlide3') {
            $this->form_validation->set_rules('slide3_headline','Headline','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide3_text','Description','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide3_button','Button text','trim|xss_clean|min_length[2]');
        }else if ($this->input->post('frm_name')=='frmSlide4') {
            $this->form_validation->set_rules('slide4_headline','Headline','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide4_ten','Ten rating text','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide4_zero','Zero rating text','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide4_button','Button text','trim|xss_clean|min_length[2]');
        }else if ($this->input->post('frm_name')=='frmSlide5') {
            $this->form_validation->set_rules('slide5_headline','Anxiety','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide5_time_x','Time x minute','trim|xss_clean|is_natural_no_zero');
            $this->form_validation->set_rules('slide5_time_y','Time y minute','trim|xss_clean|is_natural_no_zero');
            $this->form_validation->set_rules('slide5_time_text1','Time less than x','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide5_time_text2','Time between x and y','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide5_time_text3','Time more than y','trim|xss_clean|min_length[2]');
            $this->form_validation->set_rules('slide5_button','Button text','trim|xss_clean|min_length[2]');
        }else if ($this->input->post('frm_name')=='frmSlide6') {
            $this->form_validation->set_rules('p_time_[]','Practice time','trim|xss_clean|is_natural');
            $this->form_validation->set_rules('p_scnt_1[]','Message','trim|xss_clean|min_length[2]');
        }

        $this->form_validation->set_error_delimiters('','');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error'=>validation_errors()));
        }else{
            $this->minapp_model->setTreatment();
            echo json_encode(array('success'=>'success'));

        }
 	}

 	public function fillDiffForm(){
 		$data=$this->minapp_model->getTreatmentSetting();
 		header('Content-type: application/json');
 		if(empty($data)){
 			$data=array(
 				'anxiety'=>'',
 				'zero'=>'',
 				'ten'=>''
 				);
 		}
 		echo json_encode($data);
 	}

 	public function addTreatment()
 	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('difficulty','Difficulty option','required');
        $this->form_validation->set_rules('problem','Problem category','trim|required|xss_clean|min_length[2]');
        if ($this->form_validation->run() == FALSE) {
            if ($this->input->post('problemId')) {
                $_POST['problem_id'] = $this->input->post('problemId');
                $this->editProblem();
            }else{
                $this->addTreatmentsForm();
            }
        }else{
            $this->minapp_model->addProblemCategory();
            $this->listAllTreatments();
        }
 	}

 	/**
 	 * prevent problem category deletion if problem used in tasks.
 	 * @return boolean
 	 */
 	public function checkProblemInTasks(){
 		$response=$this->minapp_model->checkProblemInTasks();
 		echo $response;
 	}

 	/**
 	 * prevent task deletion if task used in training.
 	 * @return boolean
 	 */
 	public function checkTaskInUser(){
 		$response = $this->minapp_model->countActiveTasks();
		echo $response;
 	}

 	public function deleteProblem()
 	{
		$this->minapp_model->delectProblemById();

		$this->listAllTreatments();
 	}

 	public function deleteTreatmentSetting()
 	{
		$this->minapp_model->deleteTreatmentSettingById();

		$this->listAllTreatmentSettings();
 	}

 	public function deleteTask()
 	{
		$this->minapp_model->delectTaskById();
		// $this->listAllTasks();
 	}

 	public function editTreatmentSetting()
	{

		$data['treatment_id'] = $this->input->post('treatment_id');
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$data['treatment'] = $this->minapp_model->getTreatmentSettingById();
		$data['treatment']->slide6_message = json_decode($data['treatment']->slide6_message,true);
 		$this->load->view('minapp/admin/set_treatment',$data);
	}

	public function editFeedbackMessage()
	{
		$data['treatment'] = $this->minapp_model->getTreatmentSettingById();
		$data['treatment']->slide6_message = json_decode($data['treatment']->slide6_message,true);
 		$this->load->view('minapp/admin/edit_feedback_message',$data);
	}

	public function editProblem()
	{
		$data['problem'] = $this->minapp_model->getProblemById();

		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();

 		$this->load->view('minapp/admin/add_treatment',$data);
	}

	public function editTask()
	{
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();

   		$data['problems']=$this->minapp_model->getAllProblems();

   		$data['task']=$this->minapp_model->getTaskById();

		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();

 		$this->load->view('minapp/admin/add_task',$data);
	}

	/**
	 * get options fields for problem select box dynamically
	 * @return string
	 */
	public function getProblemOptions()
	{
		if($this->input->post('task_id')){
			$task=$this->minapp_model->getTaskById();
			// $probArr=json_decode($task->problem_id,true);
			$probArr=explode(',', $task->problem_id);
		}
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

		/*if(empty($records)){
			$output='<option value="">--Select Problem Category--</option>';
		}*/

		echo $output;
	}


	public function getTreatmentOptions()
	{
		$difficulties=$this->setting_model->getAllDifficultyByLang();
		$output = null;
		foreach ($difficulties as $row)
		{
			$output .= '<option value="'.$row->id.'">'.$row->difficulty.'</option>';
		}

		echo $output;
	}

	public function fillDiffFormNotify()
	{
		$data=$this->minapp_model->getPushSettingByDiffId();
 		header('Content-type: application/json');
 		if(empty($data)){
 			$data=array(
					'feedback_message'=>'',
					'reminder1_message'=>'',
					'reminder2_message'=>'',
					'feedback_xdays'=>'',
					'reminder1_xdays'=>'',
					'reminder2_xdays'=>'',
					'feedback_status'=>'1',
					'reminder1_status'=>'1',
					'reminder2_status'=>'1'
 				);
 		}
 		echo json_encode($data);
	}

	public function fillTreatmentForm()
	{
		$data=$this->minapp_model->getTreatmentSetting();
 		header('Content-type: application/json');
 		if (empty($data)) {
 			$data=array(
				    'rating' => '1',
				    'anxiety' => '',
				    'zero' => '',
				    'ten' => '',
				    'txt_button' => '',
				    'slide1_headline' => '',
				    'slide1_text' => '',
				    'slide1_button' => '',
				    'slide2_headline' => '',
				    'slide2_zero' => '',
				    'slide2_ten' => '',
				    'slide2_button' => '',
				    'slide3_headline' => '',
				    'slide3_text' => '',
				    'slide3_image' => '',
				    'slide3_button' => '',
				    'slide4_headline' => '',
				    'slide4_zero' => '',
				    'slide4_ten' => '',
				    'slide4_button' => '',
				    'slide5_headline' => '',
				    'slide5_text' => '',
				    'slide5_time_x' => '',
				    'slide5_time_y' => '',
				    'slide5_time_text1' => '',
				    'slide5_time_text2' => '',
				    'slide5_time_text3' => '',
				    'slide5_button' => ''
 				);
 		}else{
 			unset($data['id']);
 			unset($data['difficulty_id']);
 		}
 		echo json_encode($data);
	}

	public function fillDiffFormReminder()
	{
		$data=$this->minapp_model->getReminderByDifficulty();

		$output = null;

		$reminderInputs = json_decode($data['app_reminder']);

		if (!empty($reminderInputs)) {
			foreach ($reminderInputs as $k => $value) {
				$params = explode('~~~', $value);
				$output .= '<li>
						<div style="width:100%; float:left;">
							<label class="lblnotify" style="width:212px !important;margin-left: 13px; margin-top: 5px;"><strong>Tid</strong></label>
							<input type="text" class="reminder_time" size="10" name="p_time_'.$k.'" value="'.$params[0].'"  />
						</div>
						<div style="width:100%; float:left; margin-top: 8px;">
							<label class="lblnotify" style="width:212px !important;margin-left: 13px; margin-top: 5px;"><strong>Notifieringsmeddelande</strong></label>
							<input type="text" size="50" name="p_scnt_'.$k.'" value="'.$params[1].'"  />
							<a href="#" class="delmsg"  style="margin:5px 10px 0; float:left;">Ta bort (X)</a>
						</div>
						</li>';
			}
		}
		echo $output;exit;
	}



	public function generateXlsAppReport(){
        $output=$this->minapp_model->getXlsAppReportByGroup();
        echo json_encode($output);
    }

    public function exportxlsAppReport()
    {
        $filename=$this->input->post('filename').'.'.$this->input->post('format');
        // $results= json_decode($this->input->post('content'));
        $results= urldecode($this->input->post('content'));
        $results= json_decode($results);
        // echo "<pre>";print_r($results);exit;
        $trainings=array();
        foreach ($results as $user) {
            $trainings[]=$this->minapp_model->getStatTrainingsByGroup($user);
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
        // echo $xlsView;exit;
        $this->load->helper('download');
        force_download($filename, $xlsView);
    }

    function setCustomMessage(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cancel_message','Cancel message','trim|required|xss_clean|min_length[2]');
        $this->form_validation->set_error_delimiters('','');
        if ($this->form_validation->run() == FALSE) {
            $status = validation_errors();
        }else{
            $this->minapp_model->setCustomMessage();
            $status = true;
        }
        echo json_encode($status);exit;
    }

    /*Added By Sabin @ 21st June 2015 >>*/

    /**
	 * A method to render page where admin can select the option to navigate for bip App v 2
	 * @return none [description]
	 */
	public function appBackendSelector(){
		$data["difficulty"] = $this->setting_model->getAllDifficultyByLang();
		$data["default_difficulty"] = $this->input->cookie('bip_default_difficulty');
		$this->load->view("minapp/admin/app_backend_selector",$data);
	}

    /**
	 * Method to list my homeworks
	 * @author Sabin Chhetri
	 * @date 21st June 2015
	 * @return nothing
	 */
	public function listAllMyhomeworks()
	{
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_my_homework_view_ajax',$data);
	}


	/**
	 * Show My Homework  form to add new item
	 */
	public function addMyHomeworkForm(){
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/add_my_homework',$data);
 	}

 	public function listAllHomeworksAjax(){
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_my_homework_view_ajax',$data);
	}

    public function changeHomeworkStatus(){
 		$this->minapp_model->changeHomeworkStatus();
 	}

 	public function editMyHomeWork(){
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$data['hw']=$this->minapp_model->getMyHomeworkByID();

 		$this->load->view('minapp/admin/add_my_homework',$data);
	}

	/**
 	 * Save the Homework
 	 */
 	public function saveMyHomework()
 	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('headline','Headline','trim|required|xss_clean|min_length[2]');
        $this->form_validation->set_rules('difficulty_id[]','Difficulty options','required');
        $this->form_validation->set_rules('homework_content','Homework content','trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('','');
        if ($this->form_validation->run()==FALSE) {
            if ($this->input->post('homework_id')) {
                $this->editMyHomeWork();
            }else{
                $this->addMyHomeworkForm();
            }
            $res["error_code"] = "error";
            $res["error_msg"] = validation_errors();
            echo json_encode($res);exit;
        }else{
            $this->minapp_model->saveMyHomework();
        }
 	}




 	/**
	 * Method to list crisis plans
	 * @author Sabin Chhetri
	 * @date 21st June 2015
	 * @return nothing
	 */
	public function listAllMycrisisplans(){
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_crisis_plan_view_ajax',$data);
	}


	/**
	 * Show My Homework  form to add new item
	 */
	public function addMyCrisisplanForm(){
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/add_crisis_plan',$data);

 	}

 	public function listAllCrisisplansAjax()
	{
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_crisis_plan_view_ajax',$data);
	}

    public function changeCrisisplanStatus(){
 		$this->minapp_model->changeCrisisplanStatus();
 	}

 	public function editMyCrisisplan()
	{
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$data['cp']=$this->minapp_model->getMyCrisisplanByID();

 		$this->load->view('minapp/admin/add_crisis_plan',$data);
	}

	/**
 	 * Save the crisis plan
 	 */
 	public function saveMyCrisisplan()
 	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('headline','Headline','trim|required|xss_clean|min_length[2]');
        $this->form_validation->set_rules('difficulty_id[]','Difficulty options','required');
        $this->form_validation->set_rules('plan_content','Content','trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('','');
        if ($this->form_validation->run()==FALSE) {
            if ($this->input->post('plan_id')) {
                $this->editMyCrisisplan();
            }else{
                $this->addMyCrisisplanForm();
            }
            $res["error_code"] = "error";
            $res["error_msg"] = validation_errors();
            echo json_encode($res);exit;
        }else{
            $this->minapp_model->saveMyCrisisplan();
        }
 	}
    /*Added By Sabin @ 21st June 2015 <<*/

    //Added By Sabin @ 2nd July 2015 >>
    public function listAllMySkillsModule(){
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$data["filterId"] = $this->input->post("filterId");
		$this->load->view('minapp/admin/manage_my_skills_view_ajax',$data);
	}

    public function listAllModulesAjax()
	{
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_my_skills_view_ajax',$data);
	}


	public function changeSkillModulesStatus(){
 		$this->minapp_model->changeSkillModulesStatus();
 	}


 	public function editMySkillModule()
	{
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$data['cp']=$this->minapp_model->getMySkillModulesByID();

 		$this->load->view('minapp/admin/add_skill_module_plan',$data);
	}

	public function saveMySkillsModule()
 	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('module_name','Module name','trim|required|xss_clean|min_length[2]');
        $this->form_validation->set_rules('module_desc','Description','trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('','');
        if ($this->form_validation->run()==FALSE) {
            if ($this->input->post('module_id')) {
                $this->editMySkillModule();
            }else{
                $this->addSkillModulesForm();
            }
            $res["error_code"] = "error";
            $res["error_msg"] = validation_errors();
            echo json_encode($res);exit;
        }else{
            $this->minapp_model->saveMySkillsModule();
        }
 	}

 	public function addSkillModulesForm(){
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/add_skill_module_plan',$data);

 	}

 	public function sortMySkillsModule(){
		return $this->minapp_model->sortMySkillsModule();
	}

	public function listMySkills(){
		$data["selected_module"] = $this->input->post("module_id");
		$data['modules']=$this->minapp_model->fetchAllModules();
		$this->load->view('minapp/admin/list_my_skills',$data);
	}

	public function listAllSkillsAjax()
	{
		$data['modules']=$this->minapp_model->fetchAllModules();
		$this->load->view('minapp/admin/list_my_skills',$data);
	}

	public function changeSkillStatus(){
 		$this->minapp_model->changeSkillStatus();
 	}

 	public function addSkillForm(){
 		$data["moduleId"] = $this->input->post("module_id");

 		/*IF TYPE IS exposure and its already added then don't let add again.
 		As per specification one Difficulty can have only one exposure master template*/
 		$data["exposure_exist"] = $this->minapp_model->checkIfExposureExistForDifficulty($this->input->post("module_id"));

 		$data['modules']=$this->minapp_model->fetchAllModules();
		$this->load->view('minapp/admin/add_skill_form',$data);
 	}

 	public function skill_form_elements(){
 		$data["type"] = $_GET["type"];
 		$data["skillId"]  = $_GET["skill_id"] > 0 ? $_GET["skill_id"] : 0;
 		$data["module_id"]  = $_GET["module_id"] > 0 ? $_GET["module_id"] : 0;
 		$this->load->view("minapp/admin/skill_form_elements",$data);
 	}

 	public function saveSkills(){
 		$this->minapp_model->saveSkills();
 	}

 	public function editMySkill(){
 		$data["skillId"] = $this->input->post("skill_id");
 		$data["moduleId"] = $this->input->post("module_id");
 		$myskills = $this->minapp_model->fetchSkillDetailsById($data["skillId"]);

 		$data["module_name"] = $this->minapp_model->getModuleNameById($data["moduleId"]);

 		$data["skills"] = $myskills;
 		$data["skillType"] = $myskills->skill_type;

 		$data['modules'] = $this->minapp_model->fetchAllModules();
		$this->load->view('minapp/admin/add_skill_form',$data);
 	}

 	public function listFindFeelings(){
	 	$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_find_feelings_view_ajax',$data);
	}

	public function listAllFeelingsAjax()
	{
		$data['modules']=$this->minapp_model->fetchAllModules();
		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/manage_find_feelings_view_ajax',$data);
	}


	public function changeMyFeelingStatus(){
 		$this->minapp_model->changeMyFeelingStatus();
 	}

 	public function addNewFeelingForm(){
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$this->load->view('minapp/admin/add_my_feelings',$data);

 	}

 	public function saveMyFindFeelings(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('feeling_name','Feeling name','trim|required|xss_clean|min_length[2]');
        $this->form_validation->set_rules('description','Description','trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('','');
        if ($this->form_validation->run()==FALSE) {
            if ($this->input->post('feeling_id')) {
                $this->editMyFindFeeling();
            }else{
                $this->addNewFeelingForm();
            }
            $res["error_code"] = "error";
            $res["error_msg"] = validation_errors();
            echo json_encode($res);exit;
        }else{
            $this->minapp_model->saveMyFindFeelings();
        }
 	}

 	public function editMyFindFeeling(){
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
		$data['cp']=$this->minapp_model->getMyFindFeelingsByID();

 		$this->load->view('minapp/admin/add_my_feelings',$data);
 	}

 	//Added By Sabin @ 2nd July 2015 <<


 	//Added By Sabin @ 12th July 2015 >>
 	public function showCopyRegistrationForm(){
 		$data["source_reg_id"] = $this->input->post("registration_id");
 		$data["source_difficulties"] = $this->input->post("difficulties");
 		$data["source_reg_name"] = $this->minapp_model->getRegistrationNameById($data["source_reg_id"]);
 		$data["source_diff_name"] = $this->minapp_model->getDifficultiesByIDs($data["source_difficulties"]);
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
 		$this->load->view("minapp/admin/copy_registration_form", $data);
 	}

 	public function save_copy_registration(){
 		$this->minapp_model->save_copy_registration();
 	}

 	public function manageSpecialAnswers(){
 		$data['difficulties']=$this->setting_model->getAllDifficultyByLang();
 		$data["special_answers"] = $this->minapp_model->getSpecialAnswersList();
 		$this->load->view("minapp/admin/manage_special_answers",$data);
 	}

 	public function saveSpecialAnswer(){
 		$this->minapp_model->saveSpecialAnswer();
 	}

 	public function updateSpecialAnswer(){
 		$this->minapp_model->updateSpecialAnswer();
 	}

 	public function manageFeelingsDefinition(){
 		$data["definitions"] = $this->minapp_model->manageFeelingsDefinition();
 		$this->load->view("minapp/admin/manage_feelings_definintion",$data);
 	}

 	public function saveFeelingDefinitions(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('primary_feelings','Primary feelings','trim|required|xss_clean');
        $this->form_validation->set_rules('secondary_feelings','Secondary feelings','trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('','');
        if ($this->form_validation->run()==FALSE) {
            $res["status"] = "error";
            $res["message"] = validation_errors();
            echo json_encode($res);exit;
        }else{
            $this->minapp_model->saveFeelingDefinitions();
        }
 	}

 	public function manageModuleIcons(){
 		$data["target_element"] = $this->input->post("target_element");
 		//name of the element where the icon's info (filename) is set while adding module, this is not relevant while uploading new module.


 		$data["icons"] = $this->minapp_model->manageModuleIcons();
 		$data["audios"] = $this->minapp_model->manageModuleSounds();
 		$this->load->view("minapp/admin/manage_module_icons",$data);
 	}

 	public function uploadModuleIcon(){
 		$this->minapp_model->uploadModuleIcon();
 	}
 	//Added By Sabin @ 12th July 2015 <<

 	/*Added by Sabin @ 6th August 2015 >>*/
 	public function exposureTemplateSelector(){
 		$data["skill_id"] = $this->input->post("skill_id");
 		$data["module_id"] = $this->input->post("module_id");
 		$data["fetchtemplates"] = $this->minapp_model->fetchExposureTemplatesIcon();
 		$this->load->view("minapp/admin/exposure_step_template_selector",$data);
 	}

 	public function addeditExposureSteps(){
 		$data["skill_id"] = $this->input->post("skill_id");
 		$data["module_id"] = $this->input->post("module_id");
 		$data["step_id"] = $this->input->post("step_id");
 		$data["template"] = $this->input->post("template");
 		$data["template_desc"] = $this->input->post("template_desc");
 		$data["template_name"] = $this->input->post("template_name");

 		$data["details"] = $this->minapp_model->getExposureTemplateSpecificStuffs();


 		$this->load->view("minapp/admin/add_edit_exposure_steps",$data);
 	}

 	public function saveExposureSteps(){
 		$this->minapp_model->saveExposureSteps();
 	}

 	public function sortExposureSteps(){
		return $this->minapp_model->sortExposureSteps();
	}


	public function changeExposureStepStatus(){
 		$this->minapp_model->changeExposureStepStatus();
 	}

 	public function setDefaultDifficulty(){
 		$this->load->helper('cookie');

 		$difficulty_id = $this->input->post("difficulty_id");
 		$cookie = array(
		    'name'   => 'default_difficulty',
		    'value'  => $difficulty_id,
		    'expire' => '15552000'
		);
		$this->input->set_cookie($cookie);

		exit;
 	}

 	public function saveExposureAnswerCategory(){
		return $this->minapp_model->saveExposureAnswerCategory();
	}


	public function removeExposureStepAnswer(){
 		return $this->minapp_model->removeExposureStepAnswer();
 	}


 	public function removeExposureStepAnswerCat(){
 		return $this->minapp_model->removeExposureStepAnswerCat();
 	}


 	/*For exmpand collapse description template*/
 	public function saveExposureAnswerCategoryForDescription(){
		return $this->minapp_model->saveExposureAnswerCategoryForDescription();
	}

	public function checkIfModuleHasExposure(){
		$exposure_exist = $this->minapp_model->checkIfExposureExistForDifficulty($this->input->post("module_id"));
		echo $exposure_exist;
		exit;
	}

	public function sortFeelings(){
		return $this->minapp_model->sortFeelings();
	}

	public function sortHomeworks(){
		return $this->minapp_model->sortHomeworks();
	}

	public function deleteSpecialAnswer(){
		$answer_id = $this->input->post("answer_id");
		echo $this->minapp_model->deleteSpecialAnswer($answer_id);
		exit;
	}
 	/*Added by Sabin @ 6th August 2015 <<*/
}
