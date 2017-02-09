<?php
class Stage extends Public_Controller {

    public $bipPageTitle = "Startsida";

    function __construct() {
        parent::__construct();
        $this->load->model('worksheet/worksheet_model');
        $this->load->model('statistics/statistics_model');
    }


    /*     * ******************************************************************************
      Function 	: Default index page to load / loading list of stage
      Author 		: Bijay Manandhar
      Created 	 : 2011-02-22
     * ****************************************************************************** */

    function index() {
    		$this->close_stage();
        $total_message_temp = $this->session->flashdata('total_message_temp');

        $offset = (isset($_POST['offset'])) ? $_POST['offset'] : 0;
        $orderBy = (isset($_POST['orderBy'])) ? $_POST['orderBy'] : 'asc';
        $orderAlter = ($orderBy == 'asc') ? 'desc' : 'asc';
        $datalimit = DATALIMIT;
				$usertype = getUserType();
        if ($usertype == "Psychologist") {

            // countdown timer set to session
            $timer=$this->statistics_model->get_time();
            $timer_in_seconds=$timer*60;
            $this->session->set_userdata("timer", $timer_in_seconds);

            if ($this->session->userdata("patient_time_track")) {
                $time_spent=getTimer($this->session->userdata("p_id"),$this->session->userdata("patient_time_track"));
                $patient_time_spent[$this->session->userdata("p_id")] = $time_spent;
                if ($this->session->userdata('patient_time_spent')) {
                    $old_patient_time_spent=$this->session->userdata('patient_time_spent');
                    array_push($old_patient_time_spent, $patient_time_spent[$this->session->userdata('p_id')]);
                    $this->session->set_userdata("patient_time_spent",$old_patient_time_spent);
                } else {
                    $this->session->set_userdata("patient_time_spent", $patient_time_spent);
                }

            } else {
                // echo "there";
                $this->session->set_userdata("patient_time_track", "");
                $this->session->set_userdata("p_id", "");
                $this->session->set_userdata("p_name", "");
                $this->session->set_userdata("p_difficulty_id", "");
                $this->session->set_userdata("p_email", "");

            }
           // echo $this->session->userdata('user_id');
            $data['result'] = $this->stage_model->getDashboardData($this->session->userdata("user_id"));
            $data["main_content"] = 'stage/dashboard';

        } else {
            $difficulty_id = $this->session->userdata('difficulty_id');
            $result = $this->stage_model->getAllStageForUser($offset, $datalimit, $orderBy, $difficulty_id);

            $data["allStage"] = $result[0];
            $data["totalRows"] = $result[1];
            $data["difficulty_id"] = $difficulty_id;
            $data["paging"] = $this->paging_model->ajaxPaging($totalRows, $datalimit, $jsfn, $offset);
            $data["main_content"] = 'stage/stage_list';
        }

        $this->load->model('messages/messages_model');

        $data["template_header"] = 'includes/template_header';
        $data["template_footer"] = 'includes/template_footer';

        $this->load->view('includes/template', $data);
    }

    /**
     * update unique page views by user per day
     */
    function update_page_views()
    {
        $user_id=$this->input->post('user_id');
        $user=$this->user_model->getUserByUserId($user_id);
        $psychologist_id=$user->psychologist_id;
        $group_id=$user->group_id;

        $page=$this->input->post('page');

        $from_overview = $this->session->userdata('from_overview');
        // if (!$from_overview)
            $this->statistics_model->update_page_views($psychologist_id,$group_id,$user_id,$page);

        echo "success";
    }

	function personal()
	{
		$data["main_content"] = 'stage/personal';
		$data["template_header"] = 'includes/template_header';
        $data["template_footer"] = 'includes/template_footer';
        $this->load->view('includes/template', $data);
	}

	function cookies() {

		$result =	$this->stage_model->getPageContent(9);
		$data["page_title"] = $result->page_title;
		$data["content"] = $result->content;
		$data["template_header"] = 'includes/template_header';
        $data["template_footer"] = 'includes/template_footer';
		$data["main_content"] = 'stage/cookies';
        $this->load->view('includes/template', $data);
    }

	function getstageforuserPersonal() {
        $offset = (isset($_POST['offset'])) ? $_POST['offset'] : 0;
        $orderBy = (isset($_POST['orderBy'])) ? $_POST['orderBy'] : 'asc';
        $orderAlter = ($orderBy == 'asc') ? 'desc' : 'asc';
        $usertype = getUserType();
		$patientId = $this->session->userdata("p_id");

        $userDetail = $this->user_model->getUserByUserId($patientId);

        $data["locked_stage"] = explode(",", $userDetail->locked_stages);
        if ($usertype == 'Psychologist') {
            $difficultyId = $userDetail->difficulty_id;
            $userDetail->first_name = $this->encryption->decrypt($userDetail->first_name);
            $userDetail->last_name = $this->encryption->decrypt($userDetail->last_name);
            $patient_name = $userDetail->first_name . " " . $userDetail->last_name;

            $this->session->set_userdata("p_id", $patientId);
            $this->session->set_userdata("p_difficulty_id", $difficultyId);

            $this->session->set_userdata("p_name", $patient_name);
            if ($userDetail->email)
                $this->session->set_userdata("p_email", $userDetail - $email);
        }
        else {
            $difficultyId = $this->session->userdata("difficulty_id");
        }

        $datalimit = DATALIMIT;

        $result = $this->stage_model->getAllStageForUser($offset, $datalimit, $orderBy, $difficultyId);

        $data['allStage'] = $result;
        $totalRows = $result[1];

        $data['offset'] = $offset;
        $jsfn = array('getstageforalluser', '"' . $orderBy . '"');

        $paging = $this->paging_model->ajaxPaging($totalRows, $datalimit, $jsfn, $offset);
        $data['paging'] = $paging;
        $this->load->model('messages/messages_model');

        $total_message = $this->messages_model->getTotalNewMessage();
        // $total_message = $this->messages_model->getNewMessage();
        $this->session->set_flashdata('total_message', $total_message);

        $data = $this->load->view("stage/steps/getstagefor_user", $data);
        if ($total_message < 1)
            $total_message = '';
        if ($showMessage != "NoMessage") // no message when lock and unlock function is triggered
            echo $total_message . '|~|~|' . $data;
    }

    function user($patientId){

        $this->session->set_userdata("p_id", $patientId);

        $userDetail = $this->user_model->getUserByUserId($patientId);
        $this->load->model('login/login_model');
        $skin_code = $this->login_model->getSkinCodeById($userDetail->difficulty_id);
        $this->session->set_userdata('skins', $skin_code);

        $this->load->model('minapp/minapp_model');
        $total_app_message = $this->minapp_model->getTotalNewAppMessage();
        $this->session->set_userdata('total_app_message_temp', $total_app_message);

        $this->session->set_userdata( array('minappLink'=>'index.php/minapp/view/'.$userDetail->username) );

        $this->statistics_model->update_psychologist_log($userDetail->psychologist_id,$userDetail->id);
        $this->statistics_model->patient_access_activity($patientId);

        $this->load->model('page/page_model');
				$data['page_detail'] = $this->page_model->getContentByMenuDifficulty(1, $userDetail->difficulty_id);

        $data["main_content"] = 'stage/personal';
        $data["template_header"] = 'includes/template_header';
        $data["template_footer"] = 'includes/template_footer';
        $this->load->view('includes/template', $data);
    }

    function getstageforuser($patientId="", $showMessage='') {
        $offset = (isset($_POST['offset'])) ? $_POST['offset'] : 0;
        $orderBy = (isset($_POST['orderBy'])) ? $_POST['orderBy'] : 'asc';
        $orderAlter = ($orderBy == 'asc') ? 'desc' : 'asc';
        //$datalimit	= DATALIMIT;

        $usertype = getUserType();
		if(empty($patientId))
			$patientId = $this->input->post('patient_id');

        if (!$patientId) {
            $patientId = $this->session->userdata("p_id");
        }

        /*$current_time = date("Y-m-d H:i:s");
        $time_track[$patientId] = $current_time;

        if ($this->session->userdata('patient_time_track')) {
            $old_patient_time_track=$this->session->userdata('patient_time_track');
            array_push($old_patient_time_track, $time_track[$patientId]);
            $this->session->set_userdata("patient_time_track", $old_patient_time_track);
        }else{
            $this->session->set_userdata("patient_time_track", $time_track);
        }*/


        $userDetail = $this->user_model->getUserByUserId($patientId);

        $data["locked_stage"] = explode(",", $userDetail->locked_stages);
        if ($usertype == 'Psychologist') {
            $difficultyId = $userDetail->difficulty_id;
            $userDetail->first_name = $this->encryption->decrypt($userDetail->first_name);
            $userDetail->last_name = $this->encryption->decrypt($userDetail->last_name);
            $patient_name = $userDetail->first_name . " " . $userDetail->last_name;

            $this->session->set_userdata("p_id", $patientId);
            $this->session->set_userdata("p_difficulty_id", $difficultyId);

            $this->session->set_userdata("p_name", $patient_name);

            if ($userDetail->email)
                $this->session->set_userdata("p_email", $userDetail - $email);
        }
        else {
            $difficultyId = $this->session->userdata("difficulty_id");
        }

        $datalimit = DATALIMIT;

        $result = $this->stage_model->getAllStageForUser($offset, $datalimit, $orderBy, $difficultyId);
        $data['allStage'] = $result;
        $totalRows = $result[1];

        $data['offset'] = $offset;
        $jsfn = array('getstageforalluser', '"' . $orderBy . '"');

        $paging = $this->paging_model->ajaxPaging($totalRows, $datalimit, $jsfn, $offset);
        $data['paging'] = $paging;
        $this->load->model('messages/messages_model');

        $total_message = $this->messages_model->getTotalNewMessage();
        // $total_message = $this->messages_model->getNewMessage();
        $this->session->set_flashdata('total_message', $total_message);

        $data = $this->load->view("stage/steps/getstagefor_user", $data);
        if ($total_message < 1)
            $total_message = '';
        if ($showMessage != "NoMessage") // no message when lock and unlock function is triggered
            echo $total_message . '|~|~|' . $data;
    }

    function usernotification() {
        $this->load->model('messages/messages_model');
        $data['notification'] = $this->messages_model->getNotification($this->uri->segment(4));
        $data['user_id'] = $this->uri->segment(4);
        $userDetail = $this->user_model->getUserByUserId($this->uri->segment(4));
        $userDetail->first_name = $this->encryption->decrypt($userDetail->first_name);
        $userDetail->last_name = $this->encryption->decrypt($userDetail->last_name);
        $data['user_name'] = $userDetail->first_name . " " . $userDetail->last_name;
        $data['main_content'] = 'stage/notification';
        $this->load->view('includes/blank_template', $data);
    }

    function saveNotification() {
        $this->load->model('messages/messages_model');
        $result = $this->messages_model->saveNotification($this->input->post('user_id'), trim($this->input->post('notification')));
        redirect($this->input->post('current_url'));
    }

    function listAll() {

        $offset = (isset($_POST['offset'])) ? $_POST['offset'] : 0;
        $orderBy = (isset($_POST['orderBy'])) ? $_POST['orderBy'] : 'asc';
        $orderAlter = ($orderBy == 'asc') ? 'desc' : 'asc';
        $datalimit = DATALIMIT;

        $difficulty_id = $this->session->userdata('difficulty_id');
        if ($this->usertype == 'Psychologist ') {

        }
        $result = $this->stage_model->getAllStageForUser($offset, $datalimit, $orderBy, $difficulty_id);

        $data["allStage"] = $result[0];
        $data["totalRows"] = $result[1];
        $data["difficulty_id"] = $difficulty_id;

        $data["paging"] = $this->paging_model->ajaxPaging($totalRows, $datalimit, $jsfn, $offset);


        $data["main_content"] = 'stage/stage_list';
        $this->load->view('includes/template', $data);
    }

    /*     * ******************************************************************************
      Function 	: For previwing of  step
      In 		: stepId [query string]
      Author 		: Bijay Manandhar
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function startStep($stageId="") {

        $this->session->set_userdata('from_overview', false);

        $this->session->set_userdata('bip_session', $this->session->userdata('session_id'));
        $this->stage_model->addActivity($stageId, $this->session->userdata('bip_session'));


        $usertype = getUserType();
        if ($usertype != 'psychologist') {
            // echo "<pre>";print_r($this->session->all_userdata());exit;
            $this->stage_model->updateStageNumber($stageId);
        }


		redirect(site_url() . "/stage/viewStep/" . $stageId);
    }

    function endStep($stageId="") {

    }

    function viewStep() { // displaying of preview of template in fancy box
        $stageId = $this->uri->segment(3);
        $stepId = $this->uri->segment(4);
        $summary = $this->uri->segment(5);
		$preview = $this->input->get("preview");
        $task = $this->input->post("task");

		$data['preview'] = $preview;
		if($preview == 'preview')
		{
			echo '<input type="hidden" value="'.$preview.'" id="preview">';
		}
        if($preview == 'edit_worksheet')
        {
            echo '<input type="hidden" value="'.$preview.'" id="preview">';
        }
        if ($stageId) {
            $firstStepId = $this->stage_model->getFirstStep($stageId);
        }

        if (!$firstStepId) {  // in case there are no active steps to show
            echo '<script language="javascript">alert("Aktiva steg saknas"); location.href="' . base_url() . '";</script>';
        } else {

            // $this->stage_model->reorderStep($stageId); // reorder step each time first slide opens for error in ordering . @todo: find some better solution for this.
            if (!$stepId)
                $stepId = $firstStepId;
            $firstStep = false;

            if ((!$stepId || $stepId == "0" || $stepId == $firstStepId) && $stageId) {
                //echo 'IN FIRST STEP';
                $stepId = $firstStepId;

                if (!$summary)
                    $this->session->set_userdata("sess_stage", time());

                $firstStep = 1;
                $stageRow = $this->stage_model->getStageByStageId($stageId);
                $firstTemplateData = '
                                    '.lang('no_of_steps').': ' . $this->stage_model->getTotalStepByStageId($stageId, "active") . ' <br/>
                                    '.lang('estimated_time_in_minutes').':' . $stageRow->estimated_time . ' <br/>
                                    '.lang('no_of_exercises_x').' <br/>
                                    '.lang('no_of_data_to_send_x').' <br/>
                                    ';
                $data['firstTemplateData'] = $firstTemplateData;
                $this->session->set_userdata("sess_stage_id", $stageRow->id);
                $this->session->set_userdata("sess_stage_title", $stageRow->stage_title);

            }
            else {
                $firstStep = 0;
            }

            $rows = $this->stage_model->getStepDetailByStepId($stepId);


            if ($rows->published != "1") {
                $stepId = $this->stage_model->getNextStep($stepId);
                $rows = $this->stage_model->getStepDetailByStepId($stepId);
            }
            // echo "<pre>";print_r($rows);exit;
            $userId = $this->session->userdata('user_id');

            if ($rows->ref_table == "form") {
                $worksheet_id = $this->stage_model->getFormIdByStepIdUserId($userId, $rows->id);
                $data_worksheet["cur_id"] = $worksheet_id;

                if ($worksheet_id) {
                    $data_worksheet['rows'] = $this->worksheet_model->getFormDataById($worksheet_id);
                    $data_worksheet['rows']->selectedQuestion =  explode(",", $rows->goal_question_show);

                    if ($data_worksheet['rows']) {
                        // setting worksheet and its comment as read
                        $data_worksheet['comments_data'] = $this->worksheet_model->getAllCommentByStepId($rows->id);
                        $data_worksheet['archive_data'] = $this->worksheet_model->getArchiveFormData($worksheet_id, $rows->id);
                        $data_worksheet['step_data'] = $rows;

                        if ($data_worksheet['rows']) {
														$data_worksheet['radioType'] = $rows->radio_type;
														$data_worksheet['view_mode'] = "stage";
														$data_worksheet['templateForm'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $rows->ref_table, $stepId, 'id');

														$data['worksheet'] = $this->load->view('worksheet/view_detail', $data_worksheet, true);
                        }
                    }
                }
            }

            $data['stepKey'] = 0;
            $data['stepId'] = $rows->id;

						$this->session->set_userdata('sess_step_id_new' ,$rows->id);

            $data['stepId'] = $rows->id;
            $data['stageId'] = $stageId;
            $data['firstStep'] = $firstStep;
            $data["custom_data"] = $rows->template_data;
            $data['stageName'] = $rows->stage_name;
            $data['detailStart'] = $rows->detail_start;
            $data['totalSteps'] = $this->stage_model->getTotalStepByStageId($rows->stage_id, "active");
            $data['title'] = $rows->title;
            $data['description'] = $rows->description;

            $data['current_step_position'] = $this->stage_model->getCurrentStepPosition($rows->stage_id,
        	$rows->id);

            $checkUrl=base_url();
            $params = explode('.', $checkUrl);

            if(sizeof($params === 3) AND $params[0] == 'http://www') {
                $data['description']=str_replace('http://www.', 'http://www.', $data['description']);
                $data['description']=str_replace('http://', 'http://www.', $data['description']);
                $data['description']=str_replace('http://www.www.', 'http://www.', $data['description']);
            }else{
                $data['description']=str_replace('http://www.', 'http://', $data['description']);
            }

            $data['description'] = str_replace("\n<ul>", "<ul>", $data['description']);
            // $data['description'] = html_entity_decode(nl2br(stripslashes(preg_replace('/(?<=<ul>|<\/li>)\s*?(?=<\/ul>|<li>)/is', '', $data['description']))), ENT_NOQUOTES, 'UTF-8');
            $data['description'] = html_entity_decode(stripslashes(preg_replace('/(?<=<ul>|<\/li>)\s*?(?=<\/ul>|<li>)/is', '', $data['description'])), ENT_NOQUOTES, 'UTF-8');
            $data['textPosition'] = $rows->text_position;
            if (!$rows->text_position)
                $data['textPosition'] = "1";

            if (!$rows->colour_id)
                $data['colourId'] = "#D5E165"; // setting default value for color if not in database

            $data['colourId'] = $rows->colour_id;
            // $data['colour'] = $rows->colour_name;
            $colour_code = $this->stage_model->getColorByID($rows->colour_id);
			$data['colour'] = str_replace('#', '', $colour_code);
              // echo "<pre>";print_r($rows);exit;
            $data['iconImage'] = $rows->icon_image;
            $data['reference'] = $rows->reference;
            $data['commentBox'] = $rows->comment_box;
            $data['templateId'] = $rows->template_id;
            $data['ordering'] = $rows->ordering;
            $data['confirmMessageEn'] = $rows->confirm_message_en;
            $data['confirmMessageSw'] = $rows->confirm_message_sw;
            $data['sendButton'] = $rows->send_button;
            $data['answerAll'] = $rows->answer_all;
			$data['radioType'] = $rows->radio_type;
            $data['selectedQuestion'] = explode(",", $rows->goal_question_show);
            $ref_table = $rows->ref_table;
            $data["src"] = $this->uri->segment(5);  // to check its from summary page or stage

            $data['show_next'] = true;

            if (is_numeric($stepId)) {
				$data['previous_step'] = $this->stage_model->getPrevStep($stepId);
				$data['next_step'] = $this->stage_model->getNextStep($stepId);

                if ($this->session->userdata('from_overview')) {
                    $steps_visited = $this->stage_model->getVistedSteps($stageId);
                    $visitedArr = array();
                    foreach ($steps_visited as $sk => $svalue) {
                        $visitedArr[$sk]=$svalue['step_id'];
                    }
                    $user_role_type = $this->session->userdata('user_role_type');
                    if (!in_array($data['next_step'], $visitedArr) && $user_role_type=='psychologist') {
                        $data['show_next'] = false;
                    }
                }

            }


            $data['thankYouSlide'] = $this->stage_model->getThankyouPage($stageId);

            $data['downloadData'] = $this->stage_model->getDownloadFileByStepId($stepId);


            if ($ref_table) {
                if ($rows->template_id == "7") { // delete this once template 9 is fully working
                    $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $rows->reference, 'id');
                } else if ($rows->template_id == "9" || $rows->template_id == "12") {
                    $data['templateData'] = $this->stage_model->getFormDataByStepId($rows->reference, 'id');
                } else if ($rows->template_id == "2" || $rows->template_id == "3" || $rows->template_id == "8") {
                    $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $stepId, 'recordListingID');
                } else {
                    $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $stepId, 'id');
                }
            }

             if (!empty($rows->template_id) && in_array($rows->template_id, array(17,20,21))){
		          $encoded_serialized_string = $rows->template_data;
		          //to unserialize...
		          $array_restored_from_db = unserialize(base64_decode($encoded_serialized_string));
		          $data['unserialize_data'] = $array_restored_from_db;
	        	}

             if ($rows->template_id==21) {
                  $data['templateFormSettings'] = $this->stage_model->getTemplateFormSettings($stepId);
            }
            if ($rows->template_id==6) {
            $stepRow = $this->db->query("SELECT template_data from bip_step WHERE id='$stepId'")->row();
        $data["raw_choosen_step"] = $stepRow->template_data;
            }
            if (in_array($rows->template_id, array(4,11,18))){ // to grab media
              $data['templateMediaData'] = $this->stage_model->getDetailByTblNameStepId('bip_link', $stepId, 'id');
            }


            // show minapp if activation template detects during slide navigation.
						if ($this->session->userdata('logintype')=='user' && $rows->template_id==15) {
                $this->stage_model->activateAppStatus();
            }

             if (ENVIRONMENT=='development') {
              // echo "template_".$rows->template_id;
	     			}

             if($rows->template_id==22){
                  $raw_tics_data = json_decode(base64_decode($rows->template_data),true);
                  $data['tics_data'] = $raw_tics_data["tics"];
                  $data["tics_best_score"] = $this->stage_model->getTicsBestScoreV1($rows->template_id,$rows->id);
            }

            if($rows->template_id==23){
                  $raw_tics_data = json_decode(base64_decode($rows->template_data),true);
                  $data['tics_data'] = $raw_tics_data["tics"];
                  $data["tics_best_score"] = $this->stage_model->getTicsBestScoreV2($rows->template_id,$rows->id);
            }

            // activate modules for app version 2
			if ($this->session->userdata('logintype')=='user' && $rows->template_id==20) {
                $this->stage_model->activateAppModules($rows->template_data);
            }
            // dd($this->session->all_userdata());
            $difficultyId = $this->stage_model->getStageDiffByStepId($stepId)->difficulty_id;
            if ($preview == 'preview' && $this->session->userdata('difficulty')) {
                $difficultyId = $this->session->userdata('difficulty');
            }elseif ($preview == 'preview' && $this->session->userdata('difficulty_id')) {
                $difficultyId = $this->session->userdata('difficulty_id');
            }

            $this->load->model('login/login_model');
            $skin_code = $this->login_model->getSkinCodeById($difficultyId);
            $this->session->set_userdata('skins', $skin_code);

            $data['skin_id'] = $this->stage_model->getSkinByDifficultyID($difficultyId);

            if ($rows->template_id) {
                $data["main_content"] = 'stage/steps/template_' . $rows->template_id;
                $this->load->view('includes/template', $data);
            } else {
                redirect(base_url());
            }
        }
    }

    function manualActivationModules(){
    	$this->stage_model->activateAppModules();
    }

    function chooseLadder() {
        $this->load->view("stage/steps/choose_ladder");
    }

    function emailForm($no_email=0)
    {
    	$templateId = $this->input->post('templateId');
		$this->emailFormValidation();
    }

    function emailFormValidation()
    {
    	$templateId = $this->input->post('templateId');

    	$this->load->library('form_validation');
    	$this->form_validation->set_error_delimiters('','');

    	if (in_array($templateId,array(4,5,6,9,10,11,12,13,14,16,18,21))) {

    		$fld_arr = $this->input->post('fld_label');
    		if (!isset($fld_arr)) {
    			$this->form_validation->set_rules('stepId',lang('step'),'required');
    		}else{
		    	for ($i=0; $i < count($fld_arr); $i++) {
		    		$j = $i + 1;

		    		if ($this->input->post('templateId')==9) {
				        $this->form_validation->set_rules('comment_'.$i,lang('comment').$j,'trim|xss_clean');
		    		}elseif($this->input->post('templateId')==12){
		    			$this->form_validation->set_rules('fld_data['.$i.']',lang('msg_frm_data').$j,'trim|xss_clean');
		    		}else{
				        $this->form_validation->set_rules('fld_data['.$i.']',lang('msg_frm_data').$j,'trim|xss_clean');
		    		}

		    	}
    		}

    	}

    	if ($this->form_validation->run() == FALSE)
		{
			$errors["status"] = "invalidate";
			if (validation_errors()) {
		     	$errors["message"] = validation_errors();
			}else{
		     	$errors["message"] = lang('validation_not_checked');
			}
			echo json_encode($errors);
		}
		else
		{
			$this->emailFormProcess();
		}
    }

    function emailFormProcess() {
        $arr_label = $this->input->post("fld_label");
        $arr_data = $this->input->post("fld_data");
        $templateId = htmlspecialchars($this->input->post("templateId"));

        if (empty($arr_data) && in_array($templateId, array(21))) {
        	$response["formDataId"] = $formDataId;
			$response["status"] = "success";
			echo json_encode($response);
        	return;
        }
        $stepId = htmlspecialchars($this->input->post("stepId"));
        $stepDetail = $this->stage_model->getStepDetailByStepId($stepId);
        $stageId = $stepDetail->stage_id;
        $worksheetId = $this->input->post("worksheet_id");

        if ($source == "worksheet")
            $array_json_data = array();

        // form template + ladder follow up template
        if ($templateId == "12") {
            for ($i = 0; $i < count($arr_label); $i++) {
                if ((trim($arr_data[$i]) && trim($arr_label[$i]))) {
                    $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
                }
            }
        }
        //
        else if ($templateId == "4") {
            for ($i = 0; $i < count($arr_label); $i++) {
                if ((trim($arr_data[$i]) && trim($arr_label[$i]))) {
                    $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
                }else
                {
                    $arr_data[$i] = '';
                    $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
                }
            }
        }
        //
        else if ($templateId == "14") {
            for ($i = 0; $i < count($arr_label); $i++) {
                if ((trim($arr_data[$i]) && trim($arr_label[$i]))) {
                    $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
                }else
                {
                    $arr_data[$i] = '';
                    $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
                }
            }
        }else if ($templateId == "18") {
            for ($i = 0; $i < count($arr_label); $i++) {
                if ((trim($arr_data[$i]) && trim($arr_label[$i]))) {
                    $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
                }else
                {
                    $arr_data[$i] = '';
                    $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
                }
            }
        }

        //
        else if ($templateId == "11" ||$templateId == "13" || $templateId == "16") {
            for ($i = 0; $i < count($arr_label); $i++) {
                if ((trim($arr_data[$i]) && trim($arr_label[$i]))) {
                    $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
                }
            }
        }

        //
        else if ($templateId == "10") {
            if (count($arr_label) != 0) {
                for ($i = 0; $i < count($arr_label); $i++) {
                    if (trim($arr_data[$i]) && trim($arr_label[$i])) {
                        $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
                    }
                }
            }
            else
                $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[0]))] = '';
        }

        // goal and radio templates
        else if ($templateId == "5" || $templateId == "7" || $templateId == "9") {
            $arr_data = array();
            for ($i = 0; $i < count($arr_label); $i++) {
                if (trim($this->input->post("fld_data_$i"))) {
                    $data = replace_swedish_char(htmlspecialchars($this->input->post("fld_data_$i")));

                    if ($templateId == "9") {
                        $comment = $this->input->post("comment_$i");
                        if ($comment)
                            $data = $data . "~||~" . $comment;
                    }

                    array_push($arr_data, $data);
                }

                if (trim($arr_label[$i]) && $data) {
                    $array_json_data[replace_swedish_char(htmlspecialchars($arr_label[$i]))] = $data;
                }
            }
        }

        // ladder template
        else if ($templateId == "6") {
            $ladder = htmlspecialchars($this->input->post("ladder"));
            $array_json_data["ladder"] = $ladder;
            if (!$worksheetId)
                $this->session->set_userdata("SESS_USER_DATA[$stageId][$stepId]['ladder']", $ladder);

            for ($i = 0; $i < count($arr_label); $i++) {
                if (trim($arr_data[$i]) && trim($arr_label[$i]))
                    $array_json_data[htmlspecialchars($arr_label[$i])] = replace_swedish_char(htmlspecialchars($arr_data[$i]));
            }
        }

        // drag n drop column template
		else if ($templateId == "21") {
			if (count($arr_data)>0) {
				$array_json_data = $arr_data;
			}

        }
        // assigning values and label in session
        $this->session->set_userdata("SESS_USER_DATA[$stageId][$stepId]['label']", $arr_label);
        $this->session->set_userdata("SESS_USER_DATA[$stageId][$stepId]['data']", $arr_data);
        // }

        $MailBody = $message_head . $message . $message_footer;

        if (count($array_json_data) > 0)
            $jsonData = addslashes(json_encode($array_json_data));

        $usertype = getUserType();

        if ($usertype == 'Psychologist')
            $userid = $this->session->userdata("p_id");
        else
            $userid = $this->session->userdata("user_id");

        if ($worksheetId) {

            if ($this->stage_model->checkFormDataForChanges($worksheetId, $stepId, $array_json_data)) { // check if new data is different from old one
                $formDataId = $this->stage_model->addFormToDB($stepId, $userid, $jsonData, 'worksheet', @a);
            }
        } else {
            if (trim($jsonData)) {
                if ($this->stage_model->checkFormDataForChanges('', $stepId, $array_json_data)) {// check if new data is different from old one
                    $formDataId = $this->stage_model->addFormToDB($stepId, $userid, $jsonData, 'stage', @a);

                    //send mail for new worksheet creation (if worksheet highlighted)
                    $worksheet_hightlight = $stepDetail->worksheet_hightlight;
                    if ($templateId == "4" && $worksheet_hightlight==1 && $usertype != 'Psychologist') {
                        $user_id = $this->session->userdata("user_id");
                        $allUser = $this->user_model->getPsychologistIdByUserId();

                        $senderList = $allUser[0];
                        foreach ($senderList as $rows) {
                            $psychoEmail = $rows->email;
                            $psychoEmailNotify = $rows->email_notify;
                        }

                        // email notify by patient to psychologist
                        if (isset($psychoEmailNotify) && $psychoEmailNotify==1) { //psychogist email notify check or not
                            $sender_id=$user_id;
                            $receiverEmail = $psychoEmail;
                            $subject = "New worksheet created";
                            $this->load->model("messages/messages_model");
                            $this->messages_model->sendMailByPatient($sender_id,$receiverEmail,$subject);
                        }

                    }
                } else {
                    $formDataId = $this->stage_model->updateFormToDB($stepId, $userid);
                }
            }
        }
        $usertype = getUserType();

        if ($usertype == 'Psychologist')
            $userid = $this->session->userdata("p_id");
        else
            $userid = $this->session->userdata("user_id");
        $user = $this->user_model->getUserByUserId($userid);
	    if($user->communication == 1){
	        $this->db->query("UPDATE bip_form_data SET status = '0' WHERE user_id='$userid' and step_id='$stepId'");
	    }
	    $response["formDataId"] = $formDataId;
		$response["status"] = "success";
		echo json_encode($response);exit;

    }

    function updateFormData($print_stage_id='0') {
        $formdataid = $this->input->post('formdataid');
        $formid = $this->input->post('formid');
        $message = $this->input->post('message');
        $stepId = $this->input->post('stepid');
        $textvalue = $this->input->post('textvalue');

        unset($message[$formid]);
        $jsonData = json_encode($message);
        $this->stage_model->updateFormData($formdataid, $jsonData);

        $stageId = $this->session->userdata('sess_stage_id');


        $userDataLabel = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['label']"));

        foreach ($userDataLabel as $key => $value) {
            if ($value == $formid)
                unset($userDataLabel[$key]);
        }

        $this->session->set_userdata("SESS_USER_DATA[$stageId][$stepId]['label']", $userDataLabel);

        $userDataPost = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['data']"));

        foreach ($userDataPost as $key => $value) {
            if ($value == $textvalue)
                unset($userDataPost[$key]);
        }

        $this->session->set_userdata("SESS_USER_DATA[$stageId][$stepId]['data']", $userDataPost);


        if ($print_stage_id == "1"):
            $data = array('stage_id' => trim($stageId));
            header('Content-Type: application/json;charset=UTF-8');
            echo json_encode($data);
            //redirect(site_url() . "/stage/stageSummary/$stageId");
            die();
        endif;

        //redirect(site_url() . "/stage/stageSummary/$stageId");
        //exit();
    }

    function summaryPage($stageId) {
		/*need to work on*/
        // updating in database
        $sessionId = $this->session->userdata('bip_session');
        $this->stage_model->completeStage($stageId, $sessionId);
        // redirecting to the home page..
        redirect(site_url() . "/stage/stageSummary/$stageId");
    }






	function stageSummary($stageId) {

		$show_summary = $this->stage_model->getShowSummaryStageId($stageId);

		//to implement for the show summary feature
		//work to do
		//by santosh

		if($show_summary != 1)
		{

            $this->session->unset_userdata("sess_stage_id");
            $this->session->set_userdata("sess_stage_id", "");
            $this->session->unset_userdata("sess_stage");
            $this->session->unset_userdata("SESS_USER_DATA");
            //redirect(site_url("stage/completeStage"));
            redirect(site_url("stage/thankYou/$stageId"));
        }

        $difficultyId = $this->session->userdata("difficulty_id");
        $userId = $this->session->userdata("user_id");
        // ID 8 denotes summary page stored in database.
        $strSql = "SELECT * FROM bip_pages WHERE menu_id = \"8\" AND difficulty_id='$difficultyId'";
        $query = $this->db->query($strSql);
        $result = $query->row();

        $data["title"] = $result->page_title;
        $data["content"] = (stripslashes(html_entity_decode(nl2br($result->content))));
        $data["stageId"] = $stageId;

        $data["formData"] = $this->stage_model->getAllFormDataBySessId($stageId);

        $data["main_content"] = 'stage/steps/summary';

        $strSql = "SELECT id FROM bip_user_activity WHERE user_id='$userId' AND stage_id='$stageId' AND STATUS=\"1\"";
        $query1 = $this->db->query($strSql);

        if (count($data["formData"]) > 0 && $query1->num_rows() <= 1) {
            $this->load->view('includes/template', $data);
        } else {
            $this->session->unset_userdata("sess_stage_id");
            $this->session->set_userdata("sess_stage_id", "");
            $this->session->unset_userdata("sess_stage");
            $this->session->unset_userdata("SESS_USER_DATA");
            //redirect(site_url("stage/completeStage"));
            redirect(site_url("stage/thankYou/$stageId"));
        }
    }

    function sendToPshychologist($stageId) {
        //$stageId	  = $this->input->post("stageId");
        $stageDetail = $this->stage_model->getstageByStageId($stageId);
        $stageTitle = $stageDetail->stage_title;


        $allUser = $this->user_model->getPsychologistIdByUserId();


        $senderList = $allUser[0];
        //print_r($senderList);
        foreach ($senderList as $rows) {
            $psychoId = $rows->id;
            $psychoFname = $rows->first_name;
            $psychoLname = $rows->last_name;
            $psychoEmail = $rows->email;
        }


        $firstName = $this->session->userdata('first_name');
        $lastName = $this->session->userdata('last_name');
        $fullName = $firstName . " " . $lastName;

        $MailHead = '<html><head><style>body {font:normal 16px/22px "Georgia",Times, serif; color:#0C7B9F}</style></head><body>';

        //	$MailBody		= 'Kära '.$psychoFname.' '.$psychoLname.', <br />
        $MailBody = ' <br />Användare <b>' . $fullName . '</b> har avslutat  "<b>' . $stageTitle . '</b>"  ' . date("Y-m-j") . '.<p></u><br> Vänligen gå till fliken <a href="' . base_url() . 'index.php/worksheet">Mina svar</a> för att ge feedback p&aring; uppgiften. <br/>
			Med vänlig hälsning,<br/> BIP
			';

        $MailFooter = '</body></html>';

        //echo $MailBody; exit();
        $this->email->message($MailHead . $MailBody . $MailFooter);

        /* $emailUser		= $this->session->userdata('email_user');
          $firstName 		= $this->session->userdata('first_name');
          $lastName 		= $this->session->userdata('last_name');
          $fullName 		= $firstName." ".$lastName;
          $this->email->from($emailUser,$fullName);
         */


        $this->email->from('noreply@barninternetspsykiatri.se', 'BIP - Barninternetpsykiatri');
        //$mailCC = "bijay.manandhar@websearchpro.net";
        //$this->email->to($mailCC);
        $this->email->to($psychoEmail);
        //$this->email->bcc("hemant@websearchpro.net");


        $subject = $fullName . "  har avslutat \"" . $stageTitle . "\"";
        $this->email->subject($subject);

        // sending email
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['charset'] = "utf-8";
        $this->email->initialize($config);

        if (!$this->email->send()) {
            $response = $this->email->print_debugger();
            // $this->logger->logAction('sendMailByPsychologist', (array)$response);
        }

        // sending notification in BIP message

        $this->load->model("messages/messages_model");
        $this->messages_model->sendMessage($psychoId, $subject, $MailBody, 1);
        redirect(site_url("stage/thankYou/$stageId"));
    }

    function thankYou($stageId) {
        $this->session->userdata("sess_stage", "");
        $this->session->userdata("SESS_USER_DATA", "");
        $this->session->unset_userdata("sess_stage");
        $this->session->unset_userdata("SESS_USER_DATA");


        $thankYouSlide = $this->stage_model->getThankyouPage($stageId);

        if($this->session->userdata("sess_stage_id"))
				{
					$this->session->unset_userdata("sess_stage_id");
					$this->session->set_userdata("sess_stage_id", "");
				}

        if ($thankYouSlide)
            redirect(site_url("stage/viewStep/$stageId/$thankYouSlide/thankyou"));
        else
            redirect(site_url("stage/completeStage/$stageId"));
    }

    function completeStage($stageId) {

    	if (empty($stageId)) {
	    	$stageId = $this->input->post('stageId');
    	}

    	$sessionId = $this->session->userdata('bip_session');
    	if (!empty($stageId)) {
	      $this->stage_model->completeStage($stageId, $sessionId);
    	}

      $this->session->set_userdata("sess_stage", "");
      $this->session->set_userdata("SESS_USER_DATA", "");

      $this->session->unset_userdata("sess_stage");
      $this->session->unset_userdata("SESS_USER_DATA");

		if($this->session->userdata('logintype') == 'user')
			redirect(site_url("stage"));
		else
			redirect(site_url("stage/personal"));
    }



    function videolinkdetail($linkId="", $viewType="") {
        $data["result"] = $this->stage_model->videolinkdetail($linkId);
        if ($viewType == "pdf")
            return $this->load->view('stage/admin/menudetailvideo', $data, TRUE);
        else
            $this->load->view('stage/admin/menudetailvideo', $data);
    }

    function menulinkdetail($linkId="", $viewType="") {

        $data["result"] = $this->stage_model->videolinkdetail($linkId);

        if ($viewType == "pdf")
            return $this->load->view('stage/admin/menudetail_link', $data, TRUE);
        else
            $this->load->view('stage/admin/menudetail_link', $data);
    }

    function previewSubTemplate($id='', $stepId="", $viewType="") { // displaying of preview of template in fancy box
        $firstSubStepId = $this->stage_model->getFirstSubStep($stepId);
        $templateData = $this->stage_model->getlinkdetaildata($stepId, $id);

        $data['prevSubStep'] = $this->stage_model->getPrevSubStep($id);
        $data['nextSubStep'] = $this->stage_model->getNextSubStep($id);

        $data["subPageId"] = $templateData[0]->id;
        $data["subTitle"] = $templateData[0]->link_name;
        $data["print_image"] = $templateData[0]->print_image;
        $data["stepId"] = $templateData[0]->step_id;
        $data["contents"] = $templateData[0]->contents;
        $data["media"] = $templateData[0]->media;
        $data["ordering"] = $templateData[0]->ordering;
        $data["media_type"] = $templateData[0]->media_type;
        $data["html5_video"] = $templateData[0]->html5_video;

        $data["downloadData"] = $this->stage_model->getDownloadFileBySubStepId($templateData[0]->id);

        $stepId = $templateData[0]->step_id;
        $query = $this->db->query("call listallmenu('$stepId')");
        $this->db->freeDBResource();
        $result = $query->result();
        $data["subStepData"] = $result;
        $data["totalSubStep"] = count($result);

        if ($viewType == "pdf")
            return $this->load->view("stage/parts/_parts_sub_page", $data, TRUE);
        else
            $this->load->view("stage/steps/sub_page", $data);
    }

    function close_stage() {
        $this->session->set_userdata("sess_stage_id", "");
        $this->session->set_userdata("sess_stage_title", "");
        $this->session->unset_userdata('sess_stage_id');
        $this->session->unset_userdata('sess_stage_title');

        $this->session->unset_userdata("sess_stage_id");
        $this->session->set_userdata("sess_stage_id", "");
        $this->session->unset_userdata("sess_stage");
        $this->session->unset_userdata("SESS_USER_DATA");
    }

    function lockUnlock($todo) {
        $stageId = $this->input->post("stageId");
        $this->stage_model->lockUnlock($stageId, $todo);
		$patientId = $this->session->userdata("p_id");
        $this->getstageforuser($patientId, 'NoMessage');
    }

    function pdfVersion($stageId) {

        $result = $this->stage_model->getStepByStageId($stageId, $offset = 0, $datalimit = 9999999, $orderBy = 'asc');
        $allStep = $result[0];
        $totalStep = $result[1];

        if ($stageId) {
            $firstStepId = $this->stage_model->getFirstStep($stageId);
        }
        $this->load->view('includes/header_print');
        $count = 0;
        foreach ($allStep as $stepKey => $stepId) {
                $rows = $this->stage_model->getStepDetailByStepId($stepId->id);
            if ($rows->published != "1") {
                continue;
            }
            // echo $count;
            //echo '<pre>'; print_r($rows->published);
        $count ++;
            $stepId = $rows->id;
            $data['status'] = $rows->publish;

            $data['stepKey'] = $stepKey;
            $data['stepId'] = $rows->id;
            $data['stageId'] = $stageId;
            $data["custom_data"] = $rows->template_data;
            $data['firstStep'] = $firstStep;
            $data['stageName'] = $rows->stage_name;
            $data['detailStart'] = $rows->detail_start;
            $data['current_step_position_count'] = $count;
            // $data['totalSteps'] = $result[1];
            $data['totalSteps'] = $this->stage_model->getTotalStepByStageId($rows->stage_id, "active");
            $data['title'] = $rows->title;
            $data['description'] = $rows->description;

            $data['description'] = str_replace("\n<ul>", "<ul>", $data['description']);
            $data['description'] = nl2br(stripslashes(preg_replace('/(?<=<ul>|<\/li>)\s*?(?=<\/ul>|<li>)/is', '', $data['description'])));
            $data['textPosition'] = $rows->text_position;
            if (!$rows->text_position)
                $data['textPosition'] = "1";

            if (!$rows->colour_id)
                $data['colourId'] = "#D5E165"; // setting default value for color if not in database

            $data['colourId'] = $rows->colour_id;

            if (!$rows->colour_id)
                $data['colourId'] = "#D5E165"; // setting default value for color if not in database
            // $data['colour'] = $rows->colour_name;
            $data['colourId'] = $rows->colour_id;
            // $data['colour'] = $rows->colour_name;
            $colour_code = $this->stage_model->getColorByID($rows->colour_id);
            $data['colour'] = str_replace('#', '', $colour_code);

            $difficultyId = $this->stage_model->getStageDiffByStepId($stepId)->difficulty_id;
            $data['skin_id'] = $this->stage_model->getSkinByDifficultyID($difficultyId);

            $data['iconImage'] = $rows->icon_image;
            $data['reference'] = $rows->reference;
            $data['commentBox'] = $rows->comment_box;
            $data['templateId'] = $rows->template_id;
            $data['ordering'] = $rows->ordering;
            $data['confirmMessageEn'] = $rows->confirm_message_en;
            $data['confirmMessageSw'] = $rows->confirm_message_sw;
            $data['sendButton'] = $rows->send_button;
            $data['answerAll'] = $rows->answer_all;
            $data['thank_you'] = $rows->thank_you;
            $data['selectedQuestion'] = explode(",", $rows->goal_question_show);
            $ref_table = $rows->ref_table;
            $data["src"] = $this->uri->segment(5);  // to check its from summary page or stage

            $data['view_type'] = "pdf";
			//for template 9
            //$data['templateForm'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $rows->ref_table, $stepId, 'id');
            if (is_numeric($stepId)) {
                $data['previous_step'] = $this->stage_model->getPrevStep($stepId);
                $data['next_step'] = $this->stage_model->getNextStep($stepId);
            }
		if ($data['current_step_position_count'] <= $data['totalSteps']) {
            if ($ref_table) {
                if ($rows->template_id == "7") { // delete this once template 9 is fully working
                    $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $rows->reference, 'id');
                } else if ($rows->template_id == "9" || $rows->template_id == "12") {
                    $data['templateData'] = $this->stage_model->getFormDataByStepId($rows->reference, 'id');
                } else if ($rows->template_id == "2" || $rows->template_id == "3") {
                    $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $stepId, 'recordListingID');
                } else {
                    $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $stepId, 'id');
                }
            }
            if ($rows->template_id) {
                $data["display_type"] = 'pdf';
                $data["main_content"] = 'stage/steps/template_' . $rows->template_id;
                if ($rows->template_id == "2") {
                    foreach ($data['templateData'] as $linkdata) {
                        $linkId = $linkdata->id;
                        $data["linkId"] = $linkdata->id;

                        $data["linkData"] = $this->menulinkdetail($linkdata->id, "pdf");
                        $data["css_menu"] = "";
                        $data["css_menu"] = array($linkId => "activea");
                        $this->load->view('includes/template_print', $data);
                    }
                } elseif ($rows->template_id == "3") {
                    foreach ($data['templateData'] as $linkdata) {
                        $linkId = $linkdata->id;
                        $data["linkId"] = $linkdata->id;

                        $data["linkData"] = $this->videolinkdetail($linkdata->id, "pdf");
                        $data["css_menu"] = "";
                        $data["css_menu"] = array($linkId => "activea");
                        $this->load->view('includes/template_print', $data);
                    }
                } elseif ($rows->template_id == "8") {
                    foreach ($data['templateData'] as $linkdata) {

                        $data["subPageId"] = $linkdata->id;
                        $data["subPageData"] = $this->previewSubTemplate($linkdata->id, $rows->id, "pdf");
                        $this->load->view('includes/template_print', $data);
                    }

                } elseif (in_array($rows->template_id, array(4,11,18))){ // to grab media
		              $data['templateMediaData'] = $this->stage_model->getDetailByTblNameStepId('bip_link', $stepId, 'id');
		              $this->load->view('includes/template_print', $data);
                } elseif (in_array($rows->template_id, array(17,20,21))) {
                  $encoded_serialized_string = $rows->template_data;
				          //to unserialize...
				          $array_restored_from_db = unserialize(base64_decode($encoded_serialized_string));
				          $data['unserialize_data'] = $array_restored_from_db;

			             if ($rows->template_id==21)
			                  $data['templateFormSettings'] = $this->stage_model->getTemplateFormSettings($stepId);

                  $this->load->view('includes/template_print', $data);
                }
                else
                    $this->load->view('includes/template_print', $data);
            }
		}
        }
		 $this->load->view("stage/steps/footer_pdf_view");
    }

    function getStageSummary($stageId) {
        $this->load->view('includes/header_print');

        $userId = $this->session->userdata('user_id');

        $data['inboxs'] = $this->stage_model->getUserStageInboxMail($userId, $stageId);

        $data["main_content"] = 'user/save_mail';
        $data["template_header"] = 'includes/template_header';
        $data["template_footer"] = 'includes/template_footer';
        $this->load->view('includes/template_print', $data);
    }

    function hide_phone_notification() {
        $user_id = $this->input->post("user_id");
        echo $this->stage_model->update_reminder_notification($user_id);
    }

    function read_inactive_status() {
        $user_id = $this->input->post("user_id");
        echo $this->stage_model->read_inactive_status($user_id);
    }

	/*     * ******************************************************************************
      Function 	: For previwing of  step
      In 		 	: stepId [query string]
      Author 		: Bijay Manandhar
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function previewTemplate($step_id='') { // displaying of preview of template in fancy box
        $stepId = $this->uri->segment(4);
        $stageId = $this->input->post("stageId");
        if (!$stageId)
            $stageId = $this->uri->segment(5);
        $task = $this->input->post("task");

        if ($stageId)
            $firstStepId = $this->stage_model->getFirstStep($stageId);

        $firstStep = false;

        if ((!$stepId || $stepId == "0" || $stepId == $firstStepId) && $stageId) {
            //echo 'IN FIRST STEP';
            $stepId = $firstStepId;
            //echo "stepid:".$firstStepId;
            $firstStep = 1;
            $stageRow = $this->stage_model->getStageByStageId($stageId);
            $firstTemplateData = '
			Antal steg: ' . $this->stage_model->getTotalStepByStageId($stageId) . ' <br/>
			Uppskattad tid i minuter:' . $stageRow->estimated_time . ' <br/>
			Antal övningar: x <br/>
			Antal uppgifter att skicka in: x <br/>
			';
            $data['firstTemplateData'] = $firstTemplateData;
        } else {
            $stepId = $step_id;
            $firstStep = 0;
        }
        $rows = $this->stage_model->getStepDetailByStepId($stepId);
        $data['stepId'] = $rows->id;
        $data['firstStep'] = $firstStep;
        $data['stageName'] = $rows->stage_name;
        $data['detailStart'] = $rows->detail_start;
        $data['totalSteps'] = $this->stage_model->getTotalStepByStageId($rows->stage_id);
        $data['title'] = $rows->title;
        $data['description'] = ($rows->description);
        $data['description'] = str_replace("\n<ul>", "<ul>", $data['description']);
        $data['description'] = (nl2br(stripslashes(preg_replace('/(?<=<ul>|<\/li>)\s*?(?=<\/ul>|<li>)/is', '', $data['description']))));
        $data['textPosition'] = $rows->text_position;

        if (!$rows->colour_id)
            $data['colourId'] = "#D5E165"; // setting default value for color if not in database

        $data['colourId'] = $rows->colour_id;
        $data['colour'] = $rows->colour_name;
        $data['iconImage'] = $rows->icon_image;
        $data['reference'] = $rows->reference;
        $data['radioType'] = $rows->radio_type;
        $data['commentBox'] = $rows->comment_box;
        $data['thankYou'] = $rows->thank_you;
        $data['ispreview'] = true;

        $data['templateId'] = $rows->template_id;
        $data['ordering'] = $rows->ordering;
        $data['confirmMessageEn'] = $rows->confirm_message_en;
        $data['confirmMessageSw'] = $rows->confirm_message_sw;
        $data['sendButton'] = $rows->send_button;
        $data['answerAll'] = $rows->answer_all;
        $ref_table = $rows->ref_table;

        $data['previous_step'] = "";//$this->stage_model->getPrevStep($stepId);
        $data['next_step'] = "";//$this->stage_model->getNextStep($stepId);

        $data['downloadData'] = $this->stage_model->getDownloadFileByStepId($stepId);
        $templateid = $rows->template_id;
        if ($ref_table) {
            if ($rows->template_id == "7")
                $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $rows->reference, 'id');
            else if ($rows->template_id == "9" || $rows->template_id == "12") {
                if ($rows->goal_question_show) {
                    $json_goal_question_show = explode(',', $rows->goal_question_show);
                    $Querydata = '';

                    for ($im = 0; $im < count($json_goal_question_show); $im++) {
                        if ($im <= count($json_goal_question_show) - 2)
                            $comma = ",";
                        else
                            $comma = "";
                        $Querydata .=$json_goal_question_show[$im] . '' . $comma;
                    }

                    $data5 = $this->db->query("SELECT *  from bip_form where id IN(" . $Querydata . ")");
                    $result5 = $data5->result();

                    $data['templateData'] = $result5 = $data5->result();
                }
                else {
                    //echo 'No Question Selected !';
                }
            } else if ($rows->template_id == "2" || $rows->template_id == "3" || $rows->template_id == "8")
                $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $stepId, 'recordListingID');
            else
                $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $stepId, 'id');
        }

        $this->load->view('stage/admin/preview_template_' . $templateid, $data);
    }

    function overview()
    {
        $this->session->unset_userdata('p_id');
        $this->session->unset_userdata('skins');

        $this->session->unset_userdata('minappLink');

        $this->load->model('minapp/minapp_model');
        $total_app_message = $this->minapp_model->getTotalNewAppMessage();
        $this->session->set_userdata('total_app_message_temp', $total_app_message);
        $usertype = getUserType();
        if ($usertype == "Psychologist") {
	        $this->session->unset_userdata("skins");
	      }

        redirect('stage');
    }

    function stepOverviewUrl($stage_id){

        $data['stage_id']=$stage_id;

        $data['colour'] = "grey";

        $data['step_lists']=$this->stage_model->getStepListByStageId($stage_id);

        $data['stage_name']=$this->stage_model->getStageNameByStageId($stage_id);

        $data['steps_visited'] = $this->stage_model->getVistedSteps($stage_id);

        $this->session->set_userdata('from_overview', true);

        $data["main_content"] = 'stage/parts/_parts_step_overview';

        $this->load->view('includes/template', $data);
    }

     public function update_spent_time(){
        $patient_id=$this->input->post('patient_id');

        $user=$this->user_model->getUserByUserId($patient_id);
        $psychologist_id=$user->psychologist_id;
        $group_id=$user->group_id;

        $spent_time=$this->input->post('time');
        $this->load->model('statistics/statistics_model');
        $this->statistics_model->update_spent_time($psychologist_id,$group_id,$patient_id,$spent_time);
        echo "success";

    }

    //Added by sabin >>
    //Date: 3rd January 2016
    public function saveTicV1(){
        $retVar =  $this->stage_model->saveTicV1();
        echo json_encode($retVar);
        exit;
    }

    public function fetchTop10Tics(){

         extract($this->input->post());
         $data["result"] = $this->stage_model->fetchTop10TicsV1($template_id,$step_id,$level_id);
         $data["template_id"] = $template_id;
         $data["step_id"] = $step_id;
         $data["level_id"] = $level_id;
         $data["total_exercise"] = $this->stage_model->getTotalTicExercises($template_id,$step_id,$level_id,1);
         $this->load->view("tics_top10_list",$data);
    }

    public function fetchTop10TicsMore(){
         extract($this->input->post());
         $result = $this->stage_model->fetchTop10TicsV1($template_id,$step_id,$level_id);
         echo json_encode($result);
         exit;
    }

    public function deleteTicV1(){
        $tic_id = $this->input->post("tic_id");
        $user_id = $this->session->userdata("p_id");

        $delete = $this->db->query("DELETE FROM bip_tics_v1 WHERE tics_id=? AND user_id=?", array($tic_id,$user_id));

        if($delete){
            echo "success";
            exit;
        }else{
            echo "error";
            exit;
        }
    }

    public function addTicsLevel($ticVersion=2){
        $data["levels"] = $this->stage_model->getTicsLevel($ticVersion);
        $data["tic_version"] = $ticVersion;
        $this->load->view("add_tics_level",$data);
    }

    public function toggleLevelDefaultStatus(){
         $this->stage_model->toggleLevelDefaultStatus();
    }

    public function saveTicsLevel(){
    	$this->load->library('form_validation');
    	$this->form_validation->set_error_delimiters('','');
    	$this->form_validation->set_rules('level_name', 'level name', 'trim|xss_clean|required');

    	if ($this->form_validation->run() == FALSE)
		{
			$errors["status"] = "error";
	     	$errors["message"] = validation_errors();
			echo json_encode($errors);
		}
		else
		{
	        $this->stage_model->saveTicsLevel();
		}

    }

    public function updateTicsLevel(){
        $this->load->library('form_validation');
    	$this->form_validation->set_error_delimiters('','');
    	$this->form_validation->set_rules('level_name', 'level name', 'trim|xss_clean|required');

    	if ($this->form_validation->run() == FALSE)
		{
			$errors["status"] = "error";
	     	$errors["message"] = validation_errors();
			echo json_encode($errors);
		}
		else
		{
	        $this->stage_model->updateTicsLevel();
		}
    }

    public function addPatientsTicsLevel(){
    	$this->load->library('form_validation');
    	$this->form_validation->set_error_delimiters('','');
    	$this->form_validation->set_rules('new_level', 'lang:new_level', 'trim|xss_clean|required|min_length[2]');

    	if ($this->form_validation->run() == FALSE)
		{
			$errors["status"] = "error";
	     	$errors["message"] = validation_errors();
			echo json_encode($errors);
		}
		else
		{
	        return $this->stage_model->addPatientsTicsLevel();
		}
    }

    public function sortAssignedTicsLevels(){
        return $this->stage_model->sortAssignedTicsLevels();
    }

    public function assignTicsLevelToPatient(){
        return $this->stage_model->assignTicsLevelToPatient();
    }

    public function getUnassignedTicLevels(){
        $get_tics_level = $this->stage_model->getTicsLevelForPatient();
        $retvar = "";
        if($get_tics_level){
                $retvar = "<option value='-1'>".lang("txt_tics_select_from_example")."</option>";
                foreach($get_tics_level  as $level){
                    $retvar .="<option value='".$level->level_id."'>".$level->level_name."</option>";

                }
        }
        echo $retvar;
        exit;
    }

    public function saveUpdatedLevel(){
    	$this->load->library('form_validation');
    	$this->form_validation->set_error_delimiters('','');
    	$this->form_validation->set_rules('level', 'lang:level', 'trim|xss_clean|required|min_length[2]');

    	if ($this->form_validation->run() == FALSE)
		{
			$errors["status"] = "invalidate";
	     	$errors["message"] = validation_errors();
			echo json_encode($errors);
		}
		else
		{
	        $this->stage_model->saveUpdatedLevel();
		}
    }

    public function unassignTicLevels(){
        return $this->stage_model->unassignTicLevels();
    }

    public function saveTicV2(){
        $retVar =  $this->stage_model->saveTicV2();
        echo json_encode($retVar);
        exit;
    }

    public function fetchTop10TicsV2(){
        extract($this->input->post());
         $data["result"] = $this->stage_model->fetchTop10TicsV2($template_id,$step_id,$level_id);
         $data["template_id"] = $template_id;
         $data["step_id"] = $step_id;
         $data["level_id"] = $level_id;
         $data["total_exercise"] = $this->stage_model->getTotalTicExercises($template_id,$step_id,$level_id,2);
         $this->load->view("tics_top10_list_v2",$data);
    }

    public function fetchTop10TicsV2More(){
         extract($this->input->post());
         $result = $this->stage_model->fetchTop10TicsV2($template_id,$step_id,$level_id);
         echo json_encode($result);
         exit;
    }

    public function ticsTop10V1worksheet(){
         extract($this->input->post());
         $result = $this->stage_model->fetchTop10TicsV1($template_id,$step_id,$level_id);
         $total_exercise = $this->stage_model->getTotalTicExercises($template_id,$step_id,$level_id,1);
         $str = "";

         if($result){
            $cnt = 1;

            foreach($result as $res){

                $str .='<li class="li-'.$res->tics_id.'">
                            <div class="serial-no fl">'.$cnt.'</div>
                            <div class="score-time fl">'.$res->recorded_time.'</div>
                            <div class="recorded-date fl">'.date("Y-m-d", strtotime($res->recorded_date)).'</div>';

                if($user_type!="patient"){

                $str .= '<div class="delete-tic-score fr">
                    <div class="delete-box delete-score" data-ticid="'.$res->tics_id.'">
                    X
                    </div>
                </div>';

                }

               $str .= '<div class="clear"></div>
            </li>';

                $cnt++;
            }
         }else{
            $str = "<li style='width:100%'>".lang("txt_no_tics_available")."</li";
         }
         echo $str."<input type='hidden' id='hid-total-tics' value='".$total_exercise."' />";
         exit;
    }


    public function ticsTop10V2worksheet(){
         extract($this->input->post());
         $result = $this->stage_model->fetchTop10TicsV2($template_id,$step_id,$level_id);
         $total_exercise = $this->stage_model->getTotalTicExercises($template_id,$step_id,$level_id,2);

         $str = "";

         if($result){
            $cnt = 1;

            foreach($result as $res){
                $tic_rate = $this->stage_model->calculateTicRate($res->recorded_time_in_seconds, $res->no_of_tics);


                $totalMin = $this->stage_model->getTotalTimeAndTics($res->recorded_time_in_seconds, $res->no_of_tics);


                $str .= "<li>
                    <div class='serial-no fl'>".$cnt."</div>
                    <div class='score-time fl'>".$tic_rate."</div>
                    <div class='tic-stats fl'>".$totalMin."</div>
                    <div class='recorded-date fl'>".date("Y-m-d", strtotime($res->recorded_date))."</div>";

                if($user_type!=="patient"){
                    $str .="<div class='delete-tic-score fr'>
                                <div class='delete-box delete-score' data-ticid='".$res->tics_id."'>
                                X
                                </div>
                            </div>";
                }

                $str .="<div class='clear'></div></li>";
                $cnt++;
            }
         }else{
            $str = "<li style='width:100%'>".lang("txt_no_tics_available")."</li";
         }
         echo $str."<input type='hidden' id='hid-total-tics' value='".$total_exercise."' />";
         exit;
    }

    public function deleteTicV2(){
        $tic_id = $this->input->post("tic_id");
        $role_type = $this->session->userdata("user_role_type");
        if($role_type=="patient"){
            $user_id = $this->session->userdata("user_id");
        }else{
            $user_id = $this->session->userdata("p_id");
        }

        $delete = $this->db->query("DELETE FROM bip_tics_v2 WHERE tics_id=? AND user_id=?", array($tic_id,$user_id));

        if($delete){
            echo "success";
            exit;
        }else{
            echo "error";
            exit;
        }
    }

    public function saveTicV2Ratings(){
        extract($this->input->post());
        $role_type = $this->session->userdata("user_role_type");
        if($role_type=="patient"){
            $user_id = $this->session->userdata("user_id");
        }else{
            $user_id = $this->session->userdata("p_id");
        }

        $update = $this->db->query("UPDATE bip_tics_v2 SET rating_score=? WHERE tics_id=? AND user_id=?", array($ratings,$tic_id,$user_id));
        //echo $this->db->last_query();
        if($update){
            echo "success";
            exit;
        }else{
            echo "error";
            exit;
        }
    }

    public function viewTicsGraph(){
        $fetch = $this->input->post("fetch");
        $data["popup_title"] = $this->input->post("popup_title");
        $data["graph_items"] = $this->stage_model->getTicsDataForGraph($fetch);
        $data["feature_type"] = $this->input->post("feature_type");
        $data["fetch"] = $fetch;
        $data["details"] = $this->stage_model->fetchStepStageNameByStepID($this->input->post("step_id"));
        $data["tic_level"] = $this->input->post("level_name");
        $this->load->view("tics_graph_worksheet",$data);
    }

    public function ticRatingBox(){
        extract($this->input->post());
        $data["rating_desc"] = $rating_desc;
        $data["rating_title"] = $rating_title;
        $data["rate_min_text"] = $rate_min_text;
        $data["rate_max_text"] = $rate_max_text;
        $data["message"] = $message;
        $data["message_title"] = $message_title;
        $data["record_time"] = $record_time;
        $data["save_tics"] = $save_tics;
        $data["tic_version"] = $tic_version>0 ? 1 : 2;
        $data["tic_interval"] = $tic_interval>0 ? $tic_interval : 0;
        $data["record_msg_only"] = false;
        if($record_msg_only==1) $data["record_msg_only"] = true;
        $this->load->view("tics_rating_box",$data);
    }

    public function deleteTicsLevel(){
        $this->stage_model->deleteTicsLevel();
    }

    public function sessiontimeout($time){

        $lastActivity = $this->session->userdata('last_activity');
        $configtimeout = $this->config->item("sess_expiration");
        $sessonExpireson = $lastActivity+$configtimeout;

        $this->session->set_userdata('last_activity', time());

        echo "Updating session on every call";
       /* $threshold = $sessonExpireson - 360;
        $current_time = time();

        if($current_time>=$threshold){
            $this->session->set_userdata('last_activity', time()+3600);
            echo "Last Activity =".$this->session->userdata("last_activity");
            echo "\nSESSION RE-REGISTERED. USER ID = ".$this->session->userdata("user_id");
        }else{
            echo "\nNOT REGISTERED";
        }*/

        exit;
    }
    // << Added by Sabin

}

