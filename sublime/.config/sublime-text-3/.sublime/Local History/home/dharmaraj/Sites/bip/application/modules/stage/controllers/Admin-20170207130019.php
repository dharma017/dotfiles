<?php
/* * ******************************************************************************
  File 			: admin.php
  Puropose		: Main Control file for admin section of STAGE and STEP
  Author 		: Bijay Manandhar
  Created 		: 2011-02-10
  Last Modified		: 2011-02-23
 * ****************************************************************************** */

class Admin extends Admin_Controller {

    function __construct() {
        parent::__construct();
    }

    /*     * ******************************************************************************
      Function 	: Default index page to load / loading list of stage
      Author 		: Bijay Manandhar
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function index() {
        $this->session->unset_userdata('p_id');
        $difficultyId = $this->input->post('difficultyId');
        if (!$difficultyId && !$this->session->userdata('difficulty'))
            $difficultyId = 0;

        if ($difficultyId)
            $this->session->set_userdata('difficulty', $difficultyId);

        $data["difficultyId"] = $this->session->userdata('difficulty');

        $data["main_content"] = 'stage/admin/list';
        $this->load->view('includes/admin/template', $data);
    }


    /*     * ******************************************************************************
      Function	: For listing all list
      Author 		: Bijay Manandhar
      Created 	: 2011-02-10
     * ****************************************************************************** */

    function listAll($difficultyId="") {
        $difficultyId = $this->input->post('difficultyId');
        if (!$difficultyId && !$this->session->userdata('difficulty'))
            $difficultyId = 0;

        if ($difficultyId)
            $this->session->set_userdata('difficulty', $difficultyId);

        $data["difficultyId"] = $this->session->userdata('difficulty');
        $this->load->view('stage/admin/list', $data);
    }

    function choosevideofile() {
        $dir = opendir($config['base_url'] . 'images/uploads/media/video/');

        while ($read = readdir($dir)) {
            if ($read != '.' && $read != '..') {
                $datafile['serverfile'][] = $read;
            }
        }
        closedir($dir);
        $this->load->view('stage/admin/videofromserver', $datafile);
    }

    function copymovevideo() {
        $videname = str_replace(' ', '', $this->input->post('videoname'));
        $filedata = explode('.', $videname);
        $fileName1 = $filedata[0];
        $fdata = @end(explode(".", $videname));
        echo trim($videname);
    }

    /*     * ******************************************************************************
      Function 	: For dislpaying new form for adding stage
      Author 		: Bijay Manandhar
      Created 	: 2011-02-10
     * ****************************************************************************** */

    function add() {
        $this->load->view('stage/admin/add');
    }

    /*     * ******************************************************************************
      Function	: For saving stage to database
      Author 		: Bijay Manandhar
      Created 	: 2011-02-10
     * ****************************************************************************** */

    function addStage() {

        $this->load->library('form_validation');
        $this->form_validation->set_rules('stageTitle','stage title','trim|required|xss_clean|min_length[1]');
        $this->form_validation->set_rules('estimatedTime','estimated time','trim|required|xss_clean|is_natural_no_zero');
        if ($this->form_validation->run()==FALSE) {
            if ($this->input->post('stageId')) {
                $this->editStage();
            }else{
                $this->add();
            }

        }else{
            $difficulty = $this->input->post('difficulty');
            $stageId = $this->stage_model->addStage();

            $this->listAll($difficulty);
        }
    }

    /*     * ******************************************************************************
      Function : For displaying edit form for adding stage
      Author	 : Bijay Manandhar
      Created  : 2011-02-12
     * ****************************************************************************** */

    function editStage() {
        $stageId = $_POST['stageId'];
        $data['stageId'] = $stageId;
        $data['rows'] = $this->stage_model->getstageBystageId($stageId);
        $this->load->view('stage/admin/add', $data);
    }

    /*     * ******************************************************************************
      Function 	: For updating stage
      Author 		: Bijay Manandhar
      Created 	: 2011-02-12
     * ****************************************************************************** */

    function editSave() {
        $this->stage_model->editStage();
    }

    /*     * ******************************************************************************
      Function : For deleteing stage
      Author	 : Bijay Manandhar
      Created	 : 2011-02-12
     * ****************************************************************************** */

    function deleteStage() {
        $stageId = $_POST['stageId'];
        $result = $this->stage_model->deleteStageById($stageId);
        if ($result)
            $this->listAll();
        else
            echo "error";
    }

    /*     * ******************************************************************************
      Function : For to display of stage
      Author	 : Bijay Manandhar
      Created  : 2011-02-13
     * ****************************************************************************** */

    function stageDetail() {
        $stageId = $_POST['stageId'];
        $data['stageId'] = $stageId;
        $this->load->view('stage/admin/detail', $data);
    }

    /* function for stage ends here */

    /*     * ******************************************************************************
      Function : For displaying steps of selected stage
      In 		 : StageId
      Author : Bijay Manandhar
      Created : 2011-02-14
     * ****************************************************************************** */

    function showSteps($stageId='') {
        $stageId_uri = $this->uri->segment(4, 0);
        $stageId_post = $_POST['stageId'];

        if ($stageId_uri)
            $stageId = $stageId_uri;
        if ($stageId_post)
            $stageId = $stageId_post;

        $data['stageId'] = $stageId;
        $this->load->view('stage/admin/step_list', $data);
    }

    function checkActivationTemplate(){
        $response = $this->stage_model->checkActivationTemplate($_POST['stageId']);
        if ($response) {
          echo "1";
        }else{
          echo "0";
        }
    }

    /*     * ******************************************************************************
      Function 	: For displaying steps of selected stage
      Author 		: Bijay Manandhar
      Created 	: 2011-02-16
      Modified On	: 2011-02-22
     * ****************************************************************************** */

    function loadTemplate() { // loading corresponding template form after user select the template
        $templateId = $_POST['templateId'];

        $stageId = $this->input->post('stageId');
        $data['stageId'] = $stageId;

        //for saving step when loading template 2.
        if ($templateId == 2 || $templateId == 3 || $templateId == 8  || $templateId == 9) {
            $step = $this->stage_model->saveStep();
            $data['stepId'] = $step[1];
            $data['published'] = "-1";
        }

        $data['task'] = "addStep";
        $data['templateId'] = $templateId;

        $data['allstage'] = $this->stage_model->getAllStage();

        if ($stageId) {
            $templateHeading = $this->stage_model->getStageNameByStageId($stageId) . '  &raquo; ';
			      $difficultyId = $this->stage_model->getStageDifficultyByStageId($stageId);
        }
        $templateHeading .= $this->lang->line("add_new_step");
        $templateHeading .= '  [' . $this->stage_model->getTemplateNameByTemplateId($templateId) . ']';

        $data['templateHeading'] = $templateHeading;
		    $data['difficultyId'] = $difficultyId;
		    $data['slide_colors'] = $this->stage_model->getAllColour();
        $data['skin_id'] = $this->stage_model->getSkinByDifficultyID($difficultyId);
        $this->load->view('stage/admin/template_' . $templateId, $data);
    }

    /*     * ******************************************************************************
      Function 	: For saving step [Form]
      Author 		: Bijay Manandhar
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function saveStep() {

    		$this->load->library('form_validation');
      	$this->form_validation->set_error_delimiters('','');

        $this->form_validation->set_rules('title','title','trim|required|xss_clean|min_length[2]');
        $this->form_validation->set_rules('description','description','trim|xss_clean');
        $this->form_validation->set_rules('colourId','colour','required');

        $this->form_validation->set_rules('embed_video','embed video','trim|xss_clean');
        $this->form_validation->set_rules('thumnailtime','thumbnail time','trim|is_natural_no_zero|xss_clean');

        if ($this->input->post('templateId')==4) {
        	$fldLabels = $this->input->post('fldLabel');

        	for ($i=0; $i < count($fldLabels); $i++) {
        		$j = $i + 1;
		        $this->form_validation->set_rules('fldLabel['.$i.']','label text '.$j,'trim|xss_clean|required');
		        $this->form_validation->set_rules('fldRow['.$i.']','number of lines '.$j,'trim|xss_clean|is_natural_no_zero|required');
        	}
        }

        if ($this->input->post('templateId')==5) {
        	$fldQuestions = $this->input->post('fldQuestion');

        	for ($i=0; $i < count($fldQuestions); $i++) {
        		$j = $i + 1;
		        $this->form_validation->set_rules('fldQuestion['.$i.']','
		        	question '.$j,'trim|xss_clean|required');
        	}
        }

        if ($this->input->post('templateId')==10) {
	        $this->form_validation->set_rules('sub_heading','sub heading','trim|xss_clean');
        	$fldLabels = $this->input->post('fldLabel');

        	for ($i=0; $i < count($fldLabels); $i++) {
        		$j = $i + 1;
		        $this->form_validation->set_rules('fldLabel['.$i.']','Behaviour text '.$j,'trim|xss_clean|required');
        	}
        }

        if ($this->input->post('templateId')==11) {

	        $this->form_validation->set_rules('abc_box_1','abc box 1','trim|required|xss_clean');
	        $this->form_validation->set_rules('abc_box_2','abc box 2','trim|required|xss_clean');
	        $this->form_validation->set_rules('abc_box_3','abc box 3','trim|required|xss_clean');

	        $fldLabels = $this->input->post('fldLabel');

        	for ($i=0; $i < count($fldLabels); $i++) {
        		$j = $i + 1;
		        $this->form_validation->set_rules('fldLabel['.$i.']','label text '.$j,'trim|xss_clean|required');
		        $this->form_validation->set_rules('fldRow['.$i.']','number of lines '.$j,'trim|xss_clean|is_natural_no_zero|required');
        	}
        }

        if ($this->input->post('templateId')==13) {

	        $fldLabels = $this->input->post('fldLabel');

        	for ($i=0; $i < count($fldLabels); $i++) {
        		$j = $i + 1;
		        $this->form_validation->set_rules('fldLabel['.$i.']','label text '.$j,'trim|xss_clean|required');
        	}
        }

        if ($this->input->post('templateId')==14) {

	        $this->form_validation->set_rules('textbox_rows','textbox rows','trim|is_natural_no_zero|required|xss_clean');
	        $this->form_validation->set_rules('worksheet_rows','worksheet rows','trim|is_natural_no_zero|required|xss_clean');
	        $this->form_validation->set_rules('margin','margin','trim|is_natural_no_zero|required|xss_clean');

	        $fldLabels = $this->input->post('fldLabel');

        	for ($i=0; $i < count($fldLabels); $i++) {
        		$j = $i + 1;
		        $this->form_validation->set_rules('fldLabel['.$i.']','label text '.$j,'trim|xss_clean|required');
		        $this->form_validation->set_rules('width['.$i.']','width '.$j,'trim|xss_clean|is_natural_no_zero|required');
        	}
        }

        if ($this->input->post('templateId')==16) {

	        $this->form_validation->set_rules('child_abc_box_1','child abc box 1','trim|required|xss_clean');
	        $this->form_validation->set_rules('child_abc_box_2','child abc box 2','trim|required|xss_clean');
	        $this->form_validation->set_rules('child_abc_box_3','child abc box 3','trim|required|xss_clean');

	        $this->form_validation->set_rules('parent_abc_box_1','parent abc box 1','trim|required|xss_clean');
	        $this->form_validation->set_rules('parent_abc_box_2','parent abc box 2','trim|required|xss_clean');
	        $this->form_validation->set_rules('parent_abc_box_3','parent abc box 3','trim|required|xss_clean');

	        $fldLabels = $this->input->post('fldLabel');

        	for ($i=0; $i < count($fldLabels); $i++) {
        		$j = $i + 1;
		        $this->form_validation->set_rules('fldLabel['.$i.']','label text '.$j,'trim|xss_clean|required');
		        $this->form_validation->set_rules('fldRow['.$i.']','number of lines '.$j,'trim|xss_clean|is_natural_no_zero|required');
        	}
        }

        if ($this->input->post('templateId')==17) {

        	if ($this->input->post('group1')=='opt1') {
        		$this->form_validation->set_rules('circle_message_title','circle message title','trim|xss_clean');
        		$this->form_validation->set_rules('circle_message_content','circle message content','trim|xss_clean');
        		$this->form_validation->set_rules('left_circle_font_size','circle message content','trim|xss_clean');
        		$this->form_validation->set_rules('right_circle_font_size','circle message content','trim|xss_clean');

		        $this->form_validation->set_rules('category1[0]','Left Circle Text ','trim|xss_clean|required');
		        $this->form_validation->set_rules('category1[1]','Right Circle Text ','trim|xss_clean|required');

	        	$fld_arr = $this->input->post('fldText1');

	        	for ($i=0; $i < count($fld_arr); $i++) {
	        		$j = $i + 1;
			        $this->form_validation->set_rules('fldText1['.$i.']','Text '.$j,'trim|xss_clean|required');
			        $this->form_validation->set_rules('fldTitle1['.$i.']','Message Title '.$j,'trim|xss_clean|required');
			        $this->form_validation->set_rules('fldMessage1['.$i.']','Message Content '.$j,'trim|xss_clean|required');
	        	}

        	}else{
        		$this->form_validation->set_rules('rect_message_title','rect message title','trim|xss_clean');
        		$this->form_validation->set_rules('rect_message_content','rect message content','trim|xss_clean');

        		$fld_arr = $this->input->post('category2');

	        	for ($i=0; $i < count($fld_arr); $i++) {
	        		$j = $i + 1;
			        $this->form_validation->set_rules('category2['.$i.']','Left Box Text '.$j,'trim|xss_clean|required');
			        $this->form_validation->set_rules('fldText2['.$i.']','Right Box Text '.$j,'trim|xss_clean|required');
			        $this->form_validation->set_rules('fldTitle2['.$i.']','Message Title '.$j,'trim|xss_clean|required');
			        $this->form_validation->set_rules('fldMessage2['.$i.']','Message Content '.$j,'trim|xss_clean|required');
	        	}
        	}

        }

        if ($this->input->post('templateId')==18) {

	        $fldLabels = $this->input->post('fldLabel');

        	for ($i=0; $i < count($fldLabels); $i++) {
        		$j = $i + 1;
		        $this->form_validation->set_rules('fldLabel['.$i.']','label text '.$j,'trim|xss_clean|required');
        	}
        }

        if ($this->input->post('templateId')==22) {

	        $this->form_validation->set_rules('tic_timer_stop_msg','tics timer stop message','trim|xss_clean');
	        $this->form_validation->set_rules('tics_rating_title','tics rating title','trim|xss_clean');
	        $this->form_validation->set_rules('tics_rating_desc','tics rating description','trim|xss_clean');
	        $this->form_validation->set_rules('tics_rate_max_text','tics rate max text','trim|xss_clean');
	        $this->form_validation->set_rules('tics_new_record_title','tics new record title','trim|xss_clean');
	        $this->form_validation->set_rules('tics_new_record_message','tics new record message','trim|xss_clean');
	        $this->form_validation->set_rules('tics_no_new_record_title','tics no new record title','trim|xss_clean');
	        $this->form_validation->set_rules('tics_no_new_record_message','tics no new record message','trim|xss_clean');

	        $this->form_validation->set_rules('rate_interval_title','rate interval title','trim|xss_clean');
	        $this->form_validation->set_rules('rate_interval','rate interval','trim|is_natural|xss_clean');

        }

        if ($this->form_validation->run()==FALSE){
           $errors = validation_errors();
        		echo json_encode($errors);
        }else{
	        $toDo = $this->input->post("toDo");
	        $result = $this->stage_model->saveStep();
	        $stageId = $result[0];

	        // if ($toDo == "save"){
            // redirect(site_url('stage/admin/showSteps/' . $stageId . '/' . $result[1]));
	        // } else if ($toDo == "preview"){
	          $previewStr = $result[3] . '~~~~' . $result[1];
	          echo json_encode(array('success'=>$previewStr,'stageId'=>$stageId));
	        // }


        }

    }

    /*     * ******************************************************************************
      Function 	: For loading editing step Form
      Author 		: Bijay Manandhar
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function editStep() { // loading template form for editing step
        //echo 'edit mode';
        $stepId = $_POST['stepId'];
        $data['stepId'] = $stepId;
        $rows = $this->stage_model->getStepDetailByStepId($stepId);
        $data['task'] = "editStep";
        $data['stepId'] = $rows->id;
        $stageId = $rows->stage_id;
        $data['stageId'] = $stageId;
        $data['stageName'] = $rows->stage_name;
        $data['startEnd'] = $rows->start_end;
        $data['totalSteps'] = $this->stage_model->getTotalStepByStageId($rows->stage_id);
        $data['title'] = $rows->title;
        $data['description'] = $rows->description;
        $data['textPosition'] = $rows->text_position;
        $data['colourId'] = $rows->colour_id;
        $data['iconId'] = $rows->icon_id;
        $data['worksheet_hightlight'] = $rows->worksheet_hightlight;
        $data['iconImage'] = $rows->icon_image;
        $data['templateId'] = $rows->template_id;
        $data['ordering'] = $rows->ordering;
        $data['published'] = $rows->published;
        $data['radioType'] = $rows->radio_type;
        $data['commentBox'] = $rows->comment_box;
        $data['thankYou'] = $rows->thank_you;
        $data['choose_label'] = $rows->template_data;
        // echo "<pre>";print_r($data);exit;

        $data['goal_question_show'] = $rows->goal_question_show;

        $data['confirmMessageEn'] = $rows->confirm_message_en;
        $data['confirmMessageSw'] = $rows->confirm_message_sw;
        $data['sendButton'] = $rows->send_button;
        $data['answerAll'] = $rows->answer_all;
        $data['custom_data'] = $rows->template_data;
        $data['allstage'] = $this->stage_model->getAllStage();
        $templateId = $rows->template_id;
        $data['templateId'] = $templateId;
        $ref_table = $rows->ref_table;
        $data['reference'] = $rows->reference;
        if ($ref_table) {
            if ($rows->reference and $rows->template_id == "7")
                $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $rows->reference, 'id');
            else if ($rows->template_id == "2" || $rows->template_id == "3")
                $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $stepId, 'recordListingID');
            else
                $data['templateData'] = $this->stage_model->getDetailByTblNameStepId('bip_' . $ref_table, $stepId, 'id');
        }
        if ($stageId) {
            $templateHeading = $this->stage_model->getStageNameByStageId($stageId) . '  &raquo; ';
			$difficultyId = $this->stage_model->getStageDifficultyByStageId($stageId);
        }
        $templateHeading .= $this->lang->line("edit_step");
        $templateHeading .= ' [ ' . $this->stage_model->getTemplateNameByTemplateId($templateId) . ' ]';
        $data['templateHeading'] = $templateHeading;

        $data['difficultyId'] = $difficultyId;

        if (in_array($templateId, array(17,20))){
          $encoded_serialized_string = $rows->template_data;
          //to unserialize...
          $array_restored_from_db = unserialize(base64_decode($encoded_serialized_string));
          $data['unserialize_data'] = $array_restored_from_db;
          // echo "<pre>";print_r($data['unserialize_data']);exit;
        }
        if($templateId==6){
            //to unserialize...
            $raw_choosen_step = $rows->template_data;
        }

        if (in_array($templateId, array(4,11,18))) {
          $data['templateMediaData'] = $this->stage_model->getDetailByTblNameStepId('bip_link', $stepId, 'id');
        }

        if ($templateId==18) {
          $data['templateFormData18'] = $this->stage_model->getDetailByTblNamestepIdAllStatus('bip_form', $stepId, 'id');
        }

        if ($templateId==21) {
          $data['templateFormSettings'] = $this->stage_model->getTemplateFormSettings($stepId);
          // echo "<pre>";print_r($data['templateFormSettings']);exit;
        }

        if($templateId==22 || $templateId==23){ //tics template v1

            //to unserialize...
            $raw_tics_data = json_decode(base64_decode($rows->template_data),true);
            $data['tics_data'] = $raw_tics_data["tics"];

        }

        $data['slide_colors'] = $this->stage_model->getAllColour();
        $data['skin_id'] = $this->stage_model->getSkinByDifficultyID($difficultyId);
          // echo "<pre>";print_r($data['slide_colors']);exit;
        $this->load->view('stage/admin/template_' . $templateId, $data);
    }

    /*     * ******************************************************************************
      Function 	: For deleting step
      Author 		: Bijay Manandhar
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function deleteStep() {
        $stepId = $_POST['stepId'];
        $stageId = $_POST['stageId'];
        $this->stage_model->deleteStepById($stepId);
        redirect(site_url('stage/admin/showSteps/' . $stageId));
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
            $firstStep = 1;
            $stageRow = $this->stage_model->getStageByStageId($stageId);
            $firstTemplateData = '
        			'.lang('no_of_steps').': ' . $this->stage_model->getTotalStepByStageId($stageId) . ' <br/>
        			'.lang('estimated_time_in_minutes').':' . $stageRow->estimated_time . ' <br/>
        			'.lang('no_of_exercises_x').' <br/>
        			'.lang('no_of_data_to_send_x').' <br/>
        			';
            $data['firstTemplateData'] = $firstTemplateData;
        } else {
            $stepId = $step_id;
            $firstStep = 0;
        }
        $rows = $this->stage_model->getStepDetailByStepId($stepId);
        //print_r($rows);
        $data['stepKey'] = 0;
        $data['stepId'] = $rows->id;
        $data['firstStep'] = $firstStep;
        $data['stageName'] = $rows->stage_name;
        $data['detailStart'] = $rows->detail_start;
        $data['totalSteps'] = $this->stage_model->getTotalStepByStageId($rows->stage_id);
        $data['title'] = $rows->title;
        $data['description'] = $rows->description;

        $data['current_step_position'] = $this->stage_model->getCurrentStepPosition($rows->stage_id,
        	$rows->id,true);
        $stepRow = $this->db->query("SELECT template_data from bip_step WHERE id='$stepId'")->row();
        $data["raw_choosen_step"] = $stepRow->template_data;


        $checkUrl=base_url();
        $params = explode('.', $checkUrl);
        if(sizeof($params === 3) AND $params[0] == 'https://www') {
                $data['description']=str_replace('https://www.', 'https://www.', $data['description']);
                $data['description']=str_replace('https://', 'https://www.', $data['description']);
                $data['description']=str_replace('https://www.www.', 'https://www.', $data['description']);
            }else{
                $data['description']=str_replace('https://www.', 'https://', $data['description']);
            }

        $data['description'] = str_replace("\n<ul>", "<ul>", $data['description']);
        // $data['description'] = html_entity_decode(nl2br(stripslashes(preg_replace('/(?<=<ul>|<\/li>)\s*?(?=<\/ul>|<li>)/is', '', $data['description']))), ENT_NOQUOTES, 'UTF-8');
        $data['description'] = html_entity_decode(stripslashes(preg_replace('/(?<=<ul>|<\/li>)\s*?(?=<\/ul>|<li>)/is', '', $data['description'])), ENT_NOQUOTES, 'UTF-8');

        $data['textPosition'] = $rows->text_position;

        if (!$rows->colour_id)
            $data['colourId'] = "#D5E165"; // setting default value for color if not in database

        $data['colourId'] = $rows->colour_id;
				$colour_code = $this->stage_model->getColorByID($rows->colour_id);
				$data['colour'] = str_replace('#', '', $colour_code);
        $data['iconImage'] = $rows->icon_image;
        $data['reference'] = $rows->reference;
        $data['radioType'] = $rows->radio_type;
        $data['commentBox'] = $rows->comment_box;
        $data['custom_data'] = $rows->template_data; //added by Sabin on 29th May 2015 for gallery template
        $data['thankYou'] = $rows->thank_you;

        $data['templateId'] = $rows->template_id;
        $data['ordering'] = $rows->ordering;
        $data['confirmMessageEn'] = $rows->confirm_message_en;
        $data['confirmMessageSw'] = $rows->confirm_message_sw;
        $data['sendButton'] = $rows->send_button;
        $data['answerAll'] = $rows->answer_all;
        $ref_table = $rows->ref_table;

        $data['previous_step'] = $this->stage_model->getPrevStep($stepId);
        $data['next_step'] = $this->stage_model->getNextStep($stepId);

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

        if (in_array($rows->template_id, array(17,20,21))){
          $encoded_serialized_string = $rows->template_data;
          //to unserialize...
          $array_restored_from_db = unserialize(base64_decode($encoded_serialized_string));
          $data['unserialize_data'] = $array_restored_from_db;
        }

        if (in_array($rows->template_id, array(4,11,18))){ // to grab media
          $data['templateMediaData'] = $this->stage_model->getDetailByTblNameStepId('bip_link', $stepId, 'id');
        }

        if ($rows->template_id==21) {
          $data['templateFormSettings'] = $this->stage_model->getTemplateFormSettings($stepId);
	     }

       if($rows->template_id==22 || $rows->template_id==23){
          $raw_tics_data = json_decode(base64_decode($rows->template_data),true);
          $data['tics_data'] = $raw_tics_data["tics"];
       }

        if ($rows->template_id==20) {
          $difficultyId = $this->stage_model->getStageDiffByStepId($stepId)->difficulty_id;
        	$data['homeworks'] = $this->minapp_model->getHomeworkByDifficultyId($difficultyId);
        // $data['skills']['feelings'] = $this->minapp_model->getSkillsFeelingsByDifficultyId($difficultyId);
        	$data['skills_modules'] = $this->minapp_model->getSkillsModulesByDifficultyId($difficultyId);
        }

        $data['mode'] = 'preview';

        $difficultyId = $this->stage_model->getStageDiffByStepId($stepId)->difficulty_id;
        $this->load->model('login/login_model');
        $data['skin_code'] = $this->login_model->getSkinCodeById($difficultyId);

        $data['skin_id'] = $this->stage_model->getSkinByDifficultyID($difficultyId);
        if (ENVIRONMENT=='development') {
          echo "preview_template_".$templateid;
        }
        $this->load->view('stage/admin/preview_template_' . $templateid, $data);
    }

    /*     * ******************************************************************************
      Function 	: For enable / disable status of steps
      Author 		: Bijay Manandhar
      Created 	: 2011-02-28
     * ****************************************************************************** */

    function toogleStageStatus() {
        $stageId = $this->input->post("stageId");
        $toDo = $this->input->post("toDo");
        $this->stage_model->toogleStageStatus($stageId, $toDo);
        $difficultyId = $this->session->userdata('difficulty_id');

        redirect(site_url('stage/admin/listAll/' . $difficultyId));
    }

    function toogleStatus() {
        $stepId = $this->input->post("stepId");
        $stageId = $this->input->post("stageId");
        $toDo = $this->input->post("toDo");
        $this->stage_model->toogleStatus($stepId, $toDo);

        redirect(site_url('stage/admin/showSteps/' . $stageId));
    }

    /*     * ******************************************************************************
      Function 	: For save menudata in database
      Author 		: Bijay sah
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function addNewTab() {
        $result = $this->stage_model->addNewTab();
    }

    function addtemplatelink() {

    	$this->load->library('form_validation');
    	$this->form_validation->set_error_delimiters('','');
    	$this->form_validation->set_rules('menuTitle','menu title','trim|required|xss_clean|min_length[2]');

    	if ($this->form_validation->run() == FALSE)
      {
      	$errors = validation_errors();
    		echo json_encode(array('success'=>false,'result'=>$errors));
      }
      else
      {
      	$result = $this->stage_model->saveLink();
      	echo json_encode(array('success'=>true,'result'=>$result));
      }

    }

    /*     * ******************************************************************************
      Function 	: For save upload file in databse
      Author 		: Bijay sah
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function saveuploadfileinDb() {

    	$this->load->library('form_validation');
    	$this->form_validation->set_error_delimiters('','');

    	$edit_menu = $this->input->post('edit_menu');

    	if (isset($edit_menu)) {
	      $this->form_validation->set_rules('edit_menu','menu name','trim|required|xss_clean|min_length[2]');
    	}else{
	      $this->form_validation->set_rules('subTitle','title','trim|required|xss_clean|min_length[2]');
    	}

      $this->form_validation->set_rules('description','description','trim|xss_clean');
      $this->form_validation->set_rules('menudescriptiondata','menu description data','trim|xss_clean');
      $this->form_validation->set_rules('embed_video','embed video','trim|xss_clean');
      $this->form_validation->set_rules('thumnailtime','thumbnail time','trim|is_natural_no_zero|xss_clean');

    	$download_link_arr = $this->input->post('downloadFileName');

    	for ($i=0; $i < count($download_link_arr); $i++) {
    		$j = $i + 1;
        $this->form_validation->set_rules('downloadLinkname['.$i.']','link name '.$j,'trim|xss_clean|required');
    	}

      if ($this->form_validation->run() == FALSE)
      {
      	$errors = validation_errors();
    		echo json_encode($errors);
      }
      else
      {
      	$result = $this->stage_model->saveuploadfileinDb();
      	echo json_encode(array('success'=>'success'));
      }


    }

    function getallmenulist() {
        $menulink = $this->stage_model->menulink($this->input->post('stepid'));
        $sn = 1;
        foreach ($menulink as $link) {
            ?>
            <tr id="row_<?php echo $link->id; ?>">
                <td><?php echo $sn; ?></td>
                <td id="linkname_<?php echo $link->id; ?>"><?php echo $link->link_name; ?></td>
                <td><a onclick="sortmenulink('<?php echo $link->step_id; ?>','<?php echo $link->id; ?>','<?php echo $link->recordListingID; ?>','up')" title="Move Up" href="#Move Up"><img alt="up" src="<?php echo base_url(); ?>images/admin_icons/uparrow.png"></a><a onclick="sortmenulink('<?php echo $link->step_id; ?>','<?php echo $link->id; ?>','<?php echo $link->recordListingID; ?>','down')" title="Move down" href="#Move Down"><img alt="down" src="<?php echo base_url(); ?>images/admin_icons/downarrow.png"></a></td>
                <td><span id="editmenulebal_<?php echo $link->id; ?>"><a  onclick="Editmenu('<?php echo $link->link_name; ?>','<?php echo $link->id; ?>');" ><img src="<?php echo base_url(); ?>images/admin_icons/edit.png" title="Edit" alt="Edit"></a></span>&nbsp; <a onclick="deletemenu(<?php echo $link->id; ?>)" ><img src="<?php echo base_url(); ?>images/admin_icons/delete.png" title="Delete" alt="Delete"></a></td>
            </tr>
            <?php
            $sn++;
        }
    }

    /*     * ******************************************************************************
      Function 	: For save upload thumb in databsesave
      Author 		: Bijay sah
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function saveuploadthumbinDb() {
        $result = $this->stage_model->saveuploadthumbinDb();
    }

    public function getlinkdetaildata() {

        $templateId = $this->input->post("templateId");
        $stepId = $this->input->post("stepId");
        $result = $this->stage_model->getlinkdetaildata();
        foreach ($result as $data) {

            if ($data->media_type != 'video') {
                $finaldata = $data->contents . '~~~~' . '<div style="position:absolute; top:0; left:-8px;" ><a href="#" class="savebtns" onclick="deleteImagevideo();">Delete</a></div><img style="width:100px;height:100px;" src="' . base_url() . 'images/uploads/media/' . $data->media . '" />' . '~~~~' . $data->media_type . '~~~~' . $data->thumb_image . '~~~~&nbsp;~~~~' . $data->link_name . '~~~~' . $data->media . '~~~~' . $data->text_position;

            } else {
                if ($data->media) {
                    $thumb = explode('.', $data->media);
                    if ($thumb[1] == 'swf') {
                        $player = '';
                        $player.='<div style="position:absolute; top:0; left:-8px;" ><a href="#" class="savebtns" onclick="deleteImagevideo();">Delete</a></div>';
                        $player.='<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-4" codebase="http://download.macromedia.com" WIDTH="455" height="280" id="FlashContent">';
                        $player.='<PARAM NAME="movie" VALUE="' . base_url() . 'images/uploads/media/video/' . $data->media . '">';
                        $player.='<PARAM NAME="quality" VALUE="high">';
                        $player.='<param name="wmode" value="transparent">';
                        $player.='<PARAM NAME="AllowScriptAccess" VALUE="never">';
                        $player.='<embed width="455" height="280" src="' . base_url() . 'images/uploads/media/video/' . $data->media . '" quality="high" NAME="FlashContent" AllowScriptAccess="never"  wmode="transparent" TYPE="application/x-shockwave-flash"></OBJECT>';
                    } else {
                        if (strpos(strtolower($data->media), '.mp3') == true) {
                            $thumb = 'thumbaudio.jpg';
                            $player = '';
                            $player.='<div style="position:absolute; top:0; left:-8px;" ><a href="#" class="savebtns" onclick="deleteImagevideo();">Delete</a></div>';
                            $player.='<object id="player" style="margin-left:45px;" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="400" height="24">';
                            $player.='<param name="movie" value="' . base_url() . 'assets/player/player.swf"/>';
                            $player.='<param name="allowfullscreen" value="true" />';
                            $player.='<param name="allowscriptaccess" value="always" />';
                            $player.='<param name="flashvars" value="file=' . base_url() . 'images/uploads/media/video/' . $data->media . '&image=' . base_url() . 'images/uploads/thumb/' . $thumb . '" />';
                            $player.='<embed
                    						type="application/x-shockwave-flash"
                    						id="player2"
                    						name="player2"
                    						src="' . base_url() . 'assets/player/player.swf"
                    						width="400" height="24"
                    						allowscriptaccess="always"
                    						allowfullscreen="true"
                    						flashvars="file=' . base_url() . 'images/uploads/media/video/' . $data->media . '&image=' . base_url() . 'images/uploads/thumb/' . $thumb . '"
                    						/>';
                        } else {
                            $thumb = $data->image_from_video;
                            $player = '';
                            $player.='<div style="position:absolute; top:0; left:-8px;" ><a href="#" class="savebtns" onclick="deleteImagevideo();">Delete</a></div>';
                            $player.='<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="455" height="280">';
                            $player.='<param name="movie" value="' . base_url() . 'assets/player/player.swf"/>';
                            $player.='<param name="allowfullscreen" value="true" />';
                            $player.='<param name="allowscriptaccess" value="always" />';
                            $player.='<param name="flashvars" value="file=' . base_url() . 'images/uploads/media/video/' . $data->media . '&image=' . base_url() . 'images/uploads/thumb/' . $thumb . '" />';
                            $player.='<embed
                    						type="application/x-shockwave-flash"
                    						id="player2"
                    						name="player2"
                    						src="' . base_url() . 'assets/player/player.swf"
                    						width="455" height="280"
                    						allowscriptaccess="always"
                    						allowfullscreen="true"
                    						flashvars="file=' . base_url() . 'images/uploads/media/video/' . $data->media . '&image=' . base_url() . 'images/uploads/thumb/' . $thumb . '"
                    						/>';
                        }
                    }
                }
                $finaldata = $data->contents . '~~~~' . $player . '~~~~' . $data->media_type . '~~~~' . $data->thumb_image . '~~~~&nbsp;~~~~' . $data->link_name . '~~~~' . $data->media . '~~~~' . $data->text_position;
            }

        }

        if (in_array($templateId, array(2,3,4,8))) {
          $finaldata .= '~~~~' . $data->embed_video;
        }

        if ($templateId == 8 || $templateId == 2 || $templateId == 3) {
            $finaldata .= '~~~~' . $data->print_image;

            $linkId = trim($this->input->post('menuTitle'));

            $downloadData = $this->stage_model->getDownloadFileBySubStepId($linkId);
            if ($downloadData) {
                $downloadInfo = '<input type="hidden" id="download_count" value="' . count($downloadData) . '">';
                $count = 0;

                foreach ($downloadData as $rows) {
                    $count++;
                    $linkFile = $this->config->item('uploadify_upload_path') . "download/" . $rows->link_file;
                    if ($rows->link_file)
                        $downloadInfo .= '<div id="download_div_' . $count . '" style="border:1px solid #ccc; padding:8px; margin-top:10px;"><strong style="float:left; padding-right:5px; padding-top:2px;">Link Name:</strong><a href="' . base_url() . 'open_file.php?file_name=' . $rows->link_file . '">
                                                    &nbsp;&nbsp;View File</a> <input type="hidden" name="downloadFileName[]" value="' . $rows->link_file . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" class="inputs" value="' . $rows->link_name . '" name="downloadLinkname[]">
                                                        <a href="javascript:removeDownload(\'' . $rows->link_file . '\',\'#download_div_' . $count . '\',' . $rows->id . ')" >
                                                        <img src="' . base_url() . 'images/admin_icons/wrong.png"></a></div>';
                }
                $downloadInfo .= '</div>';
            }
        }
        $finaldata .= '~~~~' . $downloadInfo;

        if (!empty($data->html5_video))
          $finaldata .= '~~~~' . $data->html5_video;

        echo $finaldata;
    }

    function savevideothumbinDb() {
        $stepId = $this->input->post('stepId');
        $filename = $this->input->post('filename');
        $this->stage_model->savevideothumbinDb($stepId, $filename);
        $result = $this->stage_model->listallvideothumb();
    }

    /*     * ******************************************************************************
      Function 	: For Fetch all video thumb file from databse
      Author 		: Bijay sah
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function listallthumb() {

        $result = $this->stage_model->listallvideothumb();
    }

    /* function crope()
      {
      $data['main_content'] = 'admin/upload_crop_v1';
      $this->load->view('includes/admin/template', $data);

      } */
    /*     * ******************************************************************************
      Function 	: For menulist file from databse according to step
      Author 		: Bijay sah
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function listallmenu($case="") {

        $result = $this->stage_model->listallmenu($case);
    }

    /*     * ******************************************************************************
      Function 	: delete the video and thumb from folder and database.
      Author 		: Bijay sah
      Created 	: 2011-02-22
     * ****************************************************************************** */

    function deletevideothumb($stepId="") {

        $result = $this->stage_model->deletevideothumb();
        $stepid = $this->input->post('stepIdforsort');
        if ($stepid) {
            $data["data"] = $this->stage_model->menulink($stepid);
        }
        $this->load->view("stage/admin/menulinklist.php", $data);
    }

    function deletetabmenu($stepId="") {

        $result = $this->stage_model->deletetabmenu();
    }

    /*     * ******************************************************************************
      Function 	: For sorting stage
      Author 		: Bijay Manandhar
      Created 	: 2011-05-24
     * ****************************************************************************** */

    function sortStage() {
        //print_r('<pre>'.$_POST.'</pre>');
        $stageId = $this->input->post("stageId");
        $difficultyId = $this->input->post("difficultyId");
        $curPosition = $this->input->post("curPosition");
        $toDo = $this->input->post("toDo");
        //echo "sortStage($stageId, $difficultyId ,$curPosition,$toDo)";
        $this->stage_model->sortStage($stageId, $difficultyId, $curPosition, $toDo);
        redirect(site_url('stage/admin/listAll/' . $difficultyId));
    }

    /*     * ******************************************************************************
      Function 	: For sorting steps
      Author 		: Bijay Manandhar
      Created 	: 2011-02-28
     * ****************************************************************************** */

    function sortStep() {
        //print_r('<pre>'.$_POST.'</pre>');
        $stageId = $this->input->post("stageId");
        $stepId = $this->input->post("stepId");
        $curPosition = $this->input->post("curPosition");
        $toDo = $this->input->post("toDo");
        $this->stage_model->sortStep($stageId, $stepId, $curPosition, $toDo);
        redirect(site_url('stage/admin/showSteps/' . $stageId));
    }

    /*     * ******************************************************************************
      Function 	: For sorting sub steps
      Author 		: Bijay Manandhar
      Created 	: 2011-03-28
     * ****************************************************************************** */

    function sortSubStep() {

        $stepId = $this->input->post("stepId");
        $subStepId = $this->input->post("subStepId");
        $curPosition = $this->input->post("curPosition");
        $toDo = $this->input->post("toDo");
        $data['data'] = $this->stage_model->sortSubStep($stepId, $subStepId, $curPosition, $toDo);

        //print_r($data);
        //die;
        $this->load->view("stage/admin/tablinklist", $data);

        //redirect(site_url('stage/admin/showSubSteps/'.$stageId));
    }

    function sortStageSteps(){
      $i = 1;

      $newOrder = $this->input->post("ID");
      $stageId = $this->input->post("stage_id");

			foreach ($newOrder as $stepId) {
          $this->db->query("UPDATE bip_step set ordering = '$i' where id=$stepId");
			    $i++;
			}
    }

    function sortStageList()
    {
      $i = 1;

      $newOrder = $this->input->post("ID");

      foreach ($newOrder as $stageId) {
          $this->db->query("UPDATE bip_stage set ordering = '$i' where id=$stageId");
          $i++;
      }
    }

    /*     * ******************************************************************************
      Function 	: For preview stage
      Author 		: Bijay Manandhar
      Created 	: 2011-02-28
     * ****************************************************************************** */

    function previewStage() {
        $stageId = $this->input->post("stageId");
        $firstStep = $this->stage_model->getFirstStep($stageId);
    }

    /*     * ******************************************************************************
      Function 	: For dublicating steps into new stage
      Author 		: Bijay Manandhar
      Created 	: 2011-02-28
     * ****************************************************************************** */

    function copyStep() {
        $data["stepId"] = $this->input->post("stepId");
        $data["stepTitle"] = $this->stage_model->getStepNameByStepId($this->input->post("stepId"));
        $data["stageId"] = $this->input->post("stageId");
        $data["templateId"] = $this->input->post("templateId");
        $this->load->view('stage/admin/copy_step', $data);
    }

    /*     * ******************************************************************************
      Function 	: Saving Step from copystep() process
      Author 		: Bijay Manandhar
      Created 	: 2011-02-28
     * ****************************************************************************** */

    function copyStepSave() {
        //print_r($_post); die();
        $stepIdSource = $this->input->post("stepId");
        $destStageId = $this->input->post("sourceStageId");
        $rows = $this->stage_model->getStepDetailBystepId($stepIdSource);
        $this->stage_model->copyStep($rows, $destStageId, $stepIdSource);

        $difficultyId = $this->input->post("difficulty");
        if ($difficultyId)
            $this->session->set_userdata('difficulty', $difficultyId);

        redirect(site_url('stage/admin/showSteps/' . $destStageId));
    }


	/*     * ******************************************************************************
      Function 	: For dublicating stages into new difficulty
      Author 		: Santosh KC
      Created 	: 2013-05-26
     * ****************************************************************************** */

    function copyStage() {

        $data["stageTitle"] = $this->stage_model->getStageNameByStageId($this->input->post("stageId"));
        $data["stageId"] = $this->input->post("stageId");
        $this->load->view('stage/admin/copy_stage', $data);
    }

    function copyOthersStage() {

        $data["stageTitle"] = $this->stage_model->getStageNameByStageId($this->input->post("stageId"));
        $data["stageId"] = $this->input->post("stageId");
        $data["difficultyId"] = $this->input->post("difficultyId");
        $this->load->view('stage/admin/copy_others_stage', $data);
    }

    /*     * ******************************************************************************
      Function 	: Saving Stage from copystage() process
      Author 		: Santosh KC
      Created 	: 2013-05-26
     * ****************************************************************************** */

    function copyStageSave() {
		$stageIdSource = $this->input->post("stageId");
		$destDifficultyId = $this->input->post('difficulty');
              $this->stage_model->copyStage($destDifficultyId, $stageIdSource);
		$data["difficultyId"] = $this->session->userdata('difficulty');
		$this->load->view('stage/admin/list', $data);
    }

    function copyOthersStageSave() {
      $stageIdSource = $this->input->post("stageId");
      $destDifficultyId = $this->input->post('difficulty');

      $redirectDifficultyId = $this->input->post('redirectDifficultyId');

      $this->stage_model->copyStage($destDifficultyId, $stageIdSource);
      $this->session->set_userdata('difficulty', $destDifficultyId);

      echo $response = $redirectDifficultyId;

    }


    function removeDownload() {
        $recordId = $this->input->post("recordId");
        $file_name = $this->input->post("fileName");

        if ($recordId) {
            //echo "Removing Download Record from step ".$recordId;
            $this->stage_model->deleteDownload($recordId);
        }
        $file = "images/uploads/download/" . $file_name;

        /*if (file_exists($file) && ($file)) {
            unlink($file);
        }*/
    }

    function removeMedia() {
        $recordId = $this->input->post("recordId");
        $file_name = $this->input->post("fileName");
        if ($recordId) {
            echo "Removing Download Record from step " . $recordId;
            $this->stage_model->deleteMedia($recordId);
        }
        $file = "images/uploads/media/" . $file_name;
        echo $file . "num" . $this->stage_model->checkMedia($file_name);
        /*if (($this->stage_model->checkMedia($file_name) <= 1) && (file_exists($file)) && ($file)) {
            unlink($file);
        }*/
    }

    function downloadFile() {
        $file = $_REQUEST["file"];
        $name = $_REQUEST["name"];
        $ext = strtolower(substr(strrchr($name, '.'), 1));

        $mime_types = array();
        $mime_types['ai'] = 'application/postscript';
        $mime_types['asx'] = 'video/x-ms-asf';
        $mime_types['au'] = 'audio/basic';
        $mime_types['avi'] = 'video/x-msvideo';
        $mime_types['bmp'] = 'image/bmp';
        $mime_types['css'] = 'text/css';
        $mime_types['doc'] = 'application/msword';
        $mime_types['eps'] = 'application/postscript';
        $mime_types['exe'] = 'application/octet-stream';
        $mime_types['gif'] = 'image/gif';
        $mime_types['htm'] = 'text/html';
        $mime_types['html'] = 'text/html';
        $mime_types['ico'] = 'image/x-icon';
        $mime_types['jpe'] = 'image/jpeg';
        $mime_types['jpeg'] = 'image/jpeg';
        $mime_types['jpg'] = 'image/jpeg';
        $mime_types['js'] = 'application/x-javascript';
        $mime_types['mid'] = 'audio/mid';
        $mime_types['mov'] = 'video/quicktime';
        $mime_types['mp3'] = 'audio/mpeg';
        $mime_types['mpeg'] = 'video/mpeg';
        $mime_types['mpg'] = 'video/mpeg';
        $mime_types['pdf'] = 'application/pdf';
        $mime_types['pps'] = 'application/vnd.ms-powerpoint';
        $mime_types['ppt'] = 'application/vnd.ms-powerpoint';
        $mime_types['ps'] = 'application/postscript';
        $mime_types['pub'] = 'application/x-mspublisher';
        $mime_types['qt'] = 'video/quicktime';
        $mime_types['rtf'] = 'application/rtf';
        $mime_types['svg'] = 'image/svg+xml';
        $mime_types['swf'] = 'application/x-shockwave-flash';
        $mime_types['tif'] = 'image/tiff';
        $mime_types['tiff'] = 'image/tiff';
        $mime_types['txt'] = 'text/plain';
        $mime_types['wav'] = 'audio/x-wav';
        $mime_types['wmf'] = 'application/x-msmetafile';
        $mime_types['xls'] = 'application/vnd.ms-excel';
        $mime_types['zip'] = 'application/zip';
        $mime = $mime_types[$ext];

        $dir = "images/uploads/download/";
        if ((isset($name)) && (file_exists($dir . $name))) {
            $file_name = $name;
            header('Pragma: public');  // required
            header('Expires: 0');  // no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($dir . $file_name)) . ' GMT');
            header('Cache-Control: private', false);
            header('Content-Type: ' . $mime);
            header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($dir . $file_name)); // provide file size
            header('Connection: close');
            readfile($dir . $file_name);  // push it out
            exit();
        } else {
            echo "No file selected";
        } //end if
    }

    function managelinkpopup($stepId="") {
        $data["stepId"] = $stepId;
        $rows = $this->stage_model->getStepDetailByStepId($stepId);
        $data['stepId'] = $rows->id;
        $stageId = $rows->stage_id;
        $data["menulink"] = $this->stage_model->menulink($this->uri->segment(4));
        $difficultyId = $this->stage_model->getStageDifficultyByStageId($stageId);
        $data['difficultyId'] = $difficultyId;
        $this->load->view('stage/admin/managelinkpopup', $data);
    }

    function managevideopopup() {
        $data["stepId"] = $this->uri->segment(4);
        $data["menulink"] = $this->stage_model->menulink($this->uri->segment(4));
        $this->load->view('stage/admin/managevideopopup', $data);
    }

    function deleteImagevideo() {
        $result = $this->stage_model->deletevideoimage();
    }

    function deleteImageOnly() {
        $result = $this->stage_model->deleteImageOnly();
    }

    function videolinkdetail() {
        $data["result"] = $this->stage_model->videolinkdetail();
        $this->load->view('stage/admin/menudetailvideo', $data);
    }

    function menulinkdetail() {
        $data["result"] = $this->stage_model->videolinkdetail();
        $this->load->view('stage/admin/menudetail_link', $data);
    }

    function managetabpopup($stepId="") {
        $data["menulink"] = $this->stage_model->menulink($this->uri->segment(4));
        $data["stepId"] = $stepId;
        $this->db->freeDBResource();
        $this->load->stage_model->reorderTab($this->uri->segment(4));

        $this->load->view('stage/admin/managetabpopup', $data);
    }

    function getStageByDifficulty() {
        $difficulty = $this->input->post("difficulty");
        $sourceTemplateId = $this->input->post("templateId");
        $allStage = $this->stage_model->getStageByDifficulty($difficulty);
        $this->db->freeDBResource();
        if ($allStage) {
            echo
            '<label><strong>' . $this->lang->line("stage") . '</strong></label>
			<select id="sourceStageId" name="sourceStageId" onchange="getStepListByStageId(this.value,' . $sourceTemplateId . ')">
			<option value="">Choose Stage</option>
			';

            foreach ($allStage as $rows) {
                if ($rows->stage_title)
                    echo '<option value="' . $rows->id . '">' . $rows->stage_title . '</option>';
            }
            echo '</select>';
        }
        else {
            echo 'No Stages Available for Selected Difficulty !';
        }
    }

    function getStepListByStageId() {
        $stageId = $this->input->post("stageId");
        $templateId = $this->input->post("templateId");
        $allStep = $this->stage_model->getStepListByStageId($stageId, $templateId);
        $this->db->freeDBResource();
        if ($allStep) {
            echo
            '<label><strong>' . $this->lang->line("step") . 'Steps</strong></label>';
            if ($templateId == "4")
                echo '<select id="sourceStepId" name="sourceStepId" onchange="showdetalgoalforstep(this.value)"><option value="">Choose Select</option>';
            else
                echo '<select id="sourceStepId" name="sourceStepId"><option value="">Choose Select</option>';

            foreach ($allStep as $rows) {
                if ($rows->title)
                    echo '<option value="' . $rows->id . '">' . $rows->title . '</option>';
            }
            echo '</select>';
        }
        else {
            echo 'No Stages Available for Selected Difficulty !';
        }
    }

    function chooseLadder() {
        $this->load->view("stage/admin/choose_ladder");
    }

    function updatemenuname() {
        $rsdata = $this->stage_model->updatemenuname();
        echo trim($rsdata);
    }

    function updateRecordsListings() {
        $rs = $this->stage_model->updateRecordsListings();
    }

    function sortmenulink() {
        $rs = $this->stage_model->sortmenulink();
        $data['data'] = $rs;

        $this->load->view("stage/admin/menulinklist.php", $data);
    }

    function previewSubTemplate($id='', $stepId="") { // displaying of preview of template in fancy box
        $firstSubStepId = $this->stage_model->getFirstSubStep($stepId);
        $templateData = $this->stage_model->getlinkdetaildata($stepId, $id);

        $data['prevSubStep'] = $this->stage_model->getPrevSubStep($id);
        $data['nextSubStep'] = $this->stage_model->getNextSubStep($id);

        $data["subPageId"] = $templateData[0]->id;
        $data["subTitle"] = $templateData[0]->link_name;
        $data["stepId"] = $templateData[0]->step_id;
        $data["contents"] = $templateData[0]->contents;
        $data["media"] = $templateData[0]->media;
        $data["ordering"] = $templateData[0]->ordering;
        $data["media_type"] = $templateData[0]->media_type;
        $data["html5_video"] = $templateData[0]->html5_video;

        $data["downloadData"] = $this->stage_model->getDownloadFileBySubStepId($templateData[0]->id);

        //($data);
        $stepId = $templateData[0]->step_id;
        $query = $this->db->query("call listallmenu('$stepId')");
        $this->db->freeDBResource();
        $result = $query->result();
        $data["subStepData"] = $result;
        $data["totalSubStep"] = count($result);
        $this->load->view("stage/admin/sub_page", $data);
    }

    function createImagefromVideo() {
        //$this->load->model('stage/videosize_model');
        $timeforimage = $this->input->post("timeframe");
        $timeframe = gmdate("H:i:s", $timeforimage);
        $stepId = $this->input->post("stepid");
        $linkid = $this->input->post("linkid");

        $fileName = $this->input->post("videonameforimage");
        $ext = @end(explode('.', $fileName));
        $fileName1 = str_replace('.' . $ext, "", $fileName);
        $fileName1 = time() . '' . $fileName1 . '.jpg';

        //$file = $config['root_pathmy']."images/uploads/media/video/".$fileName;
        //$movie = new videosize_model($file);
        //$height = $this->videosize_model->getFrameHeight();
        //$width = $this->videosize_model->getFrameWidth();
        //echo '-----'.$width.'-------'.$height;
        $videolength = $this->get_video_dimensions($config['root_pathmy'] . "images/uploads/media/video/" . $fileName);

        //echo '------'.$videolength['width'].'---------'.$videolength['height'];

        /*if ($this->input->post("thumbimagename") != '')
            @unlink($config['base_url'] . "images/uploads/thumb/" . $this->input->post("thumbimagename"));*/

        $result = $this->stage_model->updateimagefromvideo($linkid, $fileName1);


        //$ffmpeg = $config['root_pathmy'].'ffmpeg.exe';
        $image_source_path = $config['root_pathmy'] . "images/uploads/media/video/" . $fileName;
        $width = $videolength['width'];
        $height = $videolength['height'];
        $image_cmd = " -r 1 -ss " . $timeframe . " -t 00:00:01 -s " . $width . "x" . $height . "   -f image2 ";
        $dest_image_path = $config['root_pathmy'] . "images/uploads/thumb/" . $fileName1 . "";
        $str_command = "ffmpeg   -i " . $image_source_path . $image_cmd . $dest_image_path;
        shell_exec($str_command);


        if (strpos(strtolower($fileName), '.mp3') == true) {
            $thumb = 'thumbaudio.jpg';
        } else {
            $thumb = $fileName1;
        }
        $url = base_url();
        $player = '';
        $player.='<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="455" height="285">';
        $player.='<param name="movie" value="' . $url . 'assets/player/player.swf"/>';
        $player.='<param name="allowfullscreen" value="true" />';
        $player.='<param name="allowscriptaccess" value="always" />';
        $player.='<param name="flashvars" value="file=' . $url . 'images/uploads/media/video/' . $fileName . '&image=' . $url . 'images/uploads/thumb/' . $thumb . '" />';
        $player.='<embed
			type="application/x-shockwave-flash"
			id="player2"
			name="player2"
			src="' . $url . 'assets/player/player.swf"
			width="455"
			height="285"
			allowscriptaccess="always"
			allowfullscreen="true"
			flashvars="file=' . $url . 'images/uploads/media/video/' . $fileName . '&image=' . $url . 'images/uploads/thumb/' . $thumb . '"
		/>';
        echo $player . '~~~~~' . $fileName1;
    }

    function get_video_dimensions($video = false) {

        if (file_exists($video)) {
            //$ffmpeg = $config['root_pathmy'].'ffmpeg.exe';
            $command = 'ffmpeg  -i ' . $video . ' -vstats 2>&1';
            $output = shell_exec($command);

            $result = @ereg('[0-9]?[0-9][0-9][0-9]x[0-9][0-9][0-9][0-9]?', $output, $regs);

            if (isset($regs [0])) {
                $vals = (explode('x', $regs [0]));
                $width = $vals [0] ? $vals [0] : null;
                $height = $vals [1] ? $vals [1] : null;
                return array('width' => $width, 'height' => $height);
            } else {
                return false;
            }
        } else {

            return false;
        }
    }

    function showdetalgoalforstep() {
        $this->stage_model->showdetalgoalforstep();
    }

	/********************************************************************************
	File 			: admin.php
	Puropose		: Provide the URL to view the steps of the difficulty in javascript file for the tinyMCE internal Link
	********************************************************************************/

    function getStepListTinyMCE($difficulty = 0 )
    {

        if(!$difficulty || !is_numeric($difficulty))
         return null;

        $allSteps = $this->stage_model->getStepListTinyMCE($difficulty);

        $site_url = utf8_encode(site_url("stage")."/viewStep/");

         $output = ''; // Here we buffer the JavaScript code we want to send to the browser.

         $result = array();

        if($allSteps)
        {
            foreach ($allSteps as $k=>$step) {

                $text = $step->stage_title.' : '.$step->step_title;
                $x = str_replace($arr, "", strip_tags(nl2br(htmlspecialchars_decode($text))));
                $y = $site_url.$step->stage_id.'/'.$step->step_id.'?preview=preview';

                $result[$k]['title']=$x;
                $result[$k]['value']=$y;

            }
        }
        $output = json_encode($result);
        echo $output;
    }

    function savePrintImageInDb() {
        $result = $this->stage_model->savePrintImageInDb();
    }

    function managewordspopup21(){
        $stepId = $data["stepId"] = $this->uri->segment(4);
        $data["columns"] = $this->stage_model->getTemplateFormSettings($this->uri->segment(4));
        $data["words"] = $this->stage_model->getTempate21FormData($this->uri->segment(4));

        $step_row = $this->db->query("SELECT template_data FROM bip_step WHERE id='$stepId'")->row();

        if (!empty($step_row->template_data)) {
	        $array_restored_from_db = unserialize(base64_decode($step_row->template_data));
	        $data['unserialize_data'] = $array_restored_from_db;
        }else{
	        $data['unserialize_data'] = '';
        }

        $this->load->view('stage/admin/managewordspopup21', $data);

    }

    function addColumn() {
        $result = $this->stage_model->addColumn();
    }

    function addWord() {
        $result = $this->stage_model->addWord();
    }

    function updateSelectColumn(){
      $step_id = $this->input->post('step_id');
      $columns = $this->stage_model->getTemplateFormSettings($step_id);

      $output = null;
      $output .='<option value="">Select Column</option>';
      foreach ($columns as $row)
      {
        $output .= '<option value="'.$row['id'].'">'.$row['fld_label'].'</option>';
      }

      echo $output;
    }

    function updateWordsList(){
      $step_id = $this->input->post('step_id');
      $words = $this->stage_model->getTempate21FormData($step_id);

      $output = null;
      foreach ($words as $key=>$word)
      {
        $columnName = $this->stage_model->getColumnNameById($word['fld_name']);
        // $bool = ($word['fld_bool']) ? 'true': 'false';
        $editImage = base_url().'images/admin_icons/edit.png';
        $deleteImage = base_url().'images/admin_icons/delete.png';
        $count = $key+1;

        $output.= '<tr id="row_'.$word['id'].'"> <td>'.$count.'</td> <td id="rowheadline'.$word['id'].'"> '.$word['fld_label'].' </td> <td id="rowcolumntype'.$word['id'].'"> '.$columnName.'</td> <td><span id="editmenulebal_'.$word['id'].'"><a  onclick="editWord('.$word['id'].');" ><img src="'.$editImage.'" title="Edit" alt="Edit"></a></span>&nbsp; <a onclick="deleteWord('.$word['id'].')" ><img src="'.$deleteImage.'" title="Delete" alt="Delete"></a></td></tr>';
      }

      echo $output;
    }

    function updateColumnList(){
      $step_id = $this->input->post('step_id');
      $columns = $this->stage_model->getTemplateFormSettings($step_id);

      $output = null;
      foreach ($columns as $key=>$column)
      {
        $columnName = $column['fld_label'];
        $columnColor = $column['fld_name'];
        $columnBackground = '<span style="width: 20px;padding:3px;margin-left:10px; display:inline-block;background-color:#'.$columnColor.'">&nbsp;</span>';

        // $bool = ($column['fld_bool']) ? 'true': 'false';
        $editImage = base_url().'images/admin_icons/edit.png';
        $deleteImage = base_url().'images/admin_icons/delete.png';
        $count = $key+1;

        $output.= '<tr id="row_'.$column['id'].'"> <td>'.$count.'</td> <td id="rowheadline'.$column['id'].'"> '.$columnName.' </td> <td id="rowcolor'.$column['id'].'"> '.$columnBackground.'</td> <td><span id="editmenulebal_'.$column['id'].'"><a  onclick="editColumn('.$column['id'].');" ><img src="'.$editImage.'" title="Edit" alt="Edit"></a></span>&nbsp; <a onclick="deleteColumn('.$column['id'].')" ><img src="'.$deleteImage.'" title="Delete" alt="Delete"></a></td></tr>';
      }

      echo $output;
    }


    function editColumn() {
        $id = $this->input->post('id');
        $query = $this->db->query("SELECT * FROM bip_form_settings WHERE id='$id'");
        $row = $query->row_array();
        $this->db->freeDBResource();
        echo json_encode($row);
    }

    function editWord() {
        $id = $this->input->post('id');
        $query = $this->db->query("SELECT * FROM bip_form WHERE id='$id'");
        $row = $query->row_array();
        $row['fld_bool_text'] = (!empty($row['fld_bool'])) ? 'true': 'false';
        $this->db->freeDBResource();
        echo json_encode($row);
    }

    function deleteColumn(){
      $id = $this->input->post('id');

      $query = $this->db->query("SELECT * FROM bip_form WHERE fld_name='$id'");
      $row = $query->row();

      if ($query->num_rows()>0) {
        echo "Column is linked with one or more word so You can not delete";
      }else{
        $this->db->query("DELETE from bip_form_settings where id=$id");
        if ($this->db->affected_rows()) {
          echo "success";
        }else{
          echo 'Error! ID ['.$id.'] not found';
        }

      }

    }

    function deleteWord(){
      $id = $this->input->post('id');
      $query = $this->db->query("DELETE from bip_form where id=$id");
      if ($this->db->affected_rows()) {
        echo "success";
      }else{
        echo 'Error! ID ['.$id.'] not found';
      }
    }

    function updateTemplate21Data(){
      $this->stage_model->updateTemplate21Data();
    }

/**
     * Method to remove gallery image
     * @author Sabin Chhetri
     * @created 19th May 2015
     * @return [string] ["success" or "failure"]
     */
    public function remove_gallery_image(){
        $fileNameToDelete = $this->input->post("image");
        $filePath = $_SERVER['DOCUMENT_ROOT'].$this->config->item('uploadify_upload_path')."gallery_images/";
        $delete = @unlink($filePath.$fileNameToDelete);
        if($delete){
          echo "success";
        }else{
          echo "failure";
        }
        exit;
    }

    /**
     * A method to sort gallery image
     * @return [string] [description]
     * @author  Sabin Chhetri @ 20th May 2015 <sabin@tulipstechnologies.com>
     */
    public function sort_gallery(){
        $return = $this->stage_model->sort_gallery();
        if($return){
            echo "success";
        }else{
            echo "failure";
        }
        exit;
    }

    /**
     * advanced activation module selection
     * @param  int $stepId
     */
    function activate_modules_popup($stepId="") {

        $data["stepId"] = $stepId;

        $difficultyId = $this->session->userdata('difficulty');

        $data['homeworks'] = $this->minapp_model->getHomeworkByDifficultyId($difficultyId);
        // $data['skills']['feelings'] = $this->minapp_model->getSkillsFeelingsByDifficultyId($difficultyId);
        $data['skills_modules'] = $this->minapp_model->getSkillsModulesByDifficultyId($difficultyId);

        $query = $this->db->query("SELECT ordering,template_data FROM bip_step WHERE id='$stepId' AND template_data!=''");

        $data['previous_step_data'] = $this->getPreviousActivationTemplateData($stepId);

        if ($query->num_rows()>0) {
	        $data['unserialize_data'] = $this->getEncodedTemplatData($stepId);
        }else{
	        $data['unserialize_data'] = $data['previous_step_data'];
        }

        // echo "<pre>";print_r($data);exit;

        $this->load->view('stage/admin/activate_modules_popup', $data);
    }

    function getPreviousActivationTemplateData($stepId)
    {

    	$difficultyId = $this->session->userdata('difficulty');

    	// get stage id from current step id
    	$valueStepData = $this->db->query("SELECT stage_id FROM bip_step WHERE id='$stepId'")->row();
    	$currentStageId =  $valueStepData->stage_id;

    	$currentStageData = $this->db->query("SELECT ordering FROM bip_stage WHERE id='$currentStageId'")->row();
    	$currentStagePosition =  $currentStageData->ordering;

    	// get current step template data , 2nd offset while sorting desc
    	$stepQry = $this->db->query("SELECT * FROM bip_step WHERE stage_id='$currentStageId' and template_id=20 and published=1 order by ordering desc limit 1,1");
    	if ($stepQry->num_rows()>0) {
	    	$row = $stepQry->row();
	    	$template_data= $row->template_data;
    	}else{
    		$prevStepQuery =$this->db->query("SELECT bs.id as stage_id,bst.id as step_id,bst.title,bs.ordering as stage_ordering,bst.ordering as step_ordering,bst.template_data FROM bip_stage bs
					left join bip_step bst on (bst.stage_id = bs.id)
					 WHERE bs.difficulty_id='$difficultyId' and bst.template_id=20 and bst.template_data!='' and bst.id!='$stepId' and bs.published=1 and bst.published=1 and bs.ordering<'$currentStagePosition'
					order by bs.ordering desc,bst.ordering desc limit 1")->row();

    		$template_data = $prevStepQuery->template_data;
    	}
    	// echo $this->db->last_query();

    	// decode serialize template data
    	if (!empty($template_data)) {
	    	$encoded_serialized_string = $template_data;
	    	$array_restored_from_db = unserialize(base64_decode($encoded_serialized_string));
    	}else{
    		$array_restored_from_db = array();
    	}

    	return $array_restored_from_db;
    }

    function getEncodedTemplatData($stepId){

        if (!$stepId) return;

        $query = $this->db->query("SELECT template_data FROM bip_step WHERE id='$stepId'");
        $row = $query->row();
        $encoded_serialized_string = $row->template_data;
        //to unserialize...
        $array_restored_from_db = unserialize(base64_decode($encoded_serialized_string));
        $unserialize_data = $array_restored_from_db;
        return $unserialize_data;
    }

    /**
     * save template specific activation modules
     * @return response
     */
    function saveActivationModules(){
      $result = $this->stage_model->saveActivationModules();
    }

}


