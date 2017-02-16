<?php

class Admin extends Admin_Controller
{
   	function __construct()
   	{
		parent::__construct();
		$this->load->model('statistics/statistics_model');
   	}

   	function index()
   	{
		$data["main_content"] = 'setting/admin/home';
	 	$this->load->view('includes/admin/template',$data);
 	}

	function listAllIcon()
	{
		$result = $this->setting_model->getAlliconByLang();
		$data["main_content"] = 'setting/admin/icon_list';
		$this->load->view('setting/admin/icon_list');
	}

	function addIconForm()
	{
		$this->load->view('setting/admin/add_icon');
	}


	function addIcon()
	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('iconName','icon name','trim|required|xss_clean|min_length[5]');
        $this->form_validation->set_rules('iconFileName','icon file','required');
        if ($this->form_validation->run()==FALSE) {
            $this->addIconForm();
        }else{
            $this->setting_model->addIcon();
            $this->listAllIcon();
        }
	}

	function deleteIcon()
	{
		$iconId=$this->input->post('iconId');
		$file_name = $this->setting_model->delectIconById($iconId);

		$this->listAllIcon();

	}


	// difficulty function

	function listAllDifficulty()
	{
		$this->load->view('setting/admin/difficulty_list');
	}

	function listOthersDifficulty()
	{
		$this->load->view('setting/admin/copy_difficulty_list');
	}

	function addDifficultyForm()
	{
		$this->load->view('setting/admin/add_difficulty');
	}

	function copyDifficulty()
	{
		$data['sourceDifficultyId'] = $this->input->post('difficultyId');
		$data['difficultyName'] = $this->input->post('difficultyName');
		$this->load->view('setting/admin/copy_difficulty',$data);
	}

	function copyOthersDifficulty()
	{
		$data['sourceDifficultyId'] = $this->input->post('difficultyId');
		$data['difficultyName'] = $this->input->post('difficultyName');
		$this->load->view('setting/admin/copy_other_difficulty',$data);
	}

	function showStages() {
		$data['difficultyId'] = $this->input->post('difficultyId');
        $this->load->view('setting/admin/copy_stage_list', $data);
    }

	function editDifficulty()
	{

		$difficultyId 	= $this->input->post("difficultyId");
		$row 			= $this->setting_model->getDifficultyById($difficultyId);

		$data['row'] = $row;

		$data['difficultyName'] = $row->difficulty;
		$data['lang_id'] = $row->lang_id;
		$data['difficultyId'] 	= $row->id;

		$this->load->view('setting/admin/add_difficulty',$data);
	}


	function addDifficulty()
	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('difficultyName','lang:difficultyName','trim|required|xss_clean|min_length[2]');
        if ($this->form_validation->run() == FALSE) {
            $this->addDifficultyForm();
        }else{
            $this->setting_model->addDifficulty();
		$this->setting_model->updateUserPermissionByDifficulty();
            $this->listAllDifficulty();
        }
	}

	function addOthersDifficulty()
	{
		$this->setting_model->addOthersDifficulty();
		$this->listOthersDifficulty();
	}

	function deleteDifficulty()
	{
		$difficultyId=$this->input->post('difficultyId');
		$file_name = $this->setting_model->delectDifficultyById($difficultyId);
		$this->listAllDifficulty();
	}

	/**
	 * deleting difficulty along with their related data
	 * (like stages , steps,....)
	 */
	function deleteDifficultyCascade(){
		$difficultyId=$this->input->post('difficultyId');
		$related_stages = $this->setting_model->delectDifficultyByIdCascade($difficultyId);
		$this->listAllDifficulty();
	}

	// code for pages

	function addGroupForm()
	{
		$this->load->view('setting/admin/add_group');
	}
	function getAllGroup()
	{
		$this->load->view('setting/admin/group_list');
	}
	function addGroup()
	{
        $this->load->library('form_validation');
        $this->form_validation->set_rules('groupName','lang:groupName','trim|required|xss_clean|min_length[2]');
        if ($this->form_validation->run() == FALSE) {
           $this->addGroupForm();
        }else{
            $this->setting_model->addupdateGroup();
		$this->setting_model->updateUserPermissionByGroup($groupId);
            $this->getAllGroup();
        }
	}
	function editGroup()
	{
		$groupId                  = 	$this->input->post("groupId");
		$row                         = 	$this->setting_model->editGroupByid($groupId);

		$data['groupName']  = $row->group_name;
                                    $notifications  = json_decode($row->notification);

                                    $data['reminder']     = $notifications->reminder;
                                    $data['inactive']       = $notifications->inactive;
                                    $data['expiry']          = $notifications->expiry;

		$data['groupId']        = $row->id;
		$data['todo']             = "edit";

		$this->load->view('setting/admin/add_group',$data);
	}
	function deletegroup()
	{
		$this->setting_model->deletegroup();
	}
	function checkdifficultyinstage()
	{
		$this->setting_model->checkdifficultyinstage();
	}
	function checkuseringroup()
	{
		$this->setting_model->checkuseringroup();
	}
	function checkslideinIcone()
	{
		$this->setting_model->checkslideinIcone();
	}

	// convert second to HH:MM:SS
	function secondsToHMS($seconds, $padHours = false)
	{
		// start with a blank string
		$hms = "";

		// do the hours first: there are 3600 seconds in an hour, so if we divide
		// the total number of seconds by 3600 and throw away the remainder, we're
		// left with the number of hours in those seconds
		$hours = intval(intval($seconds) / 3600);

		// add hours to $hms (with a leading 0 if asked for)
		$hms .= ($padHours)
			  ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
			  : $hours. ":";

		// dividing the total seconds by 60 will give us the number of minutes
		// in total, but we're interested in *minutes past the hour* and to get
		// this, we have to divide by 60 again and then use the remainder
		$minutes = intval(($seconds / 60) % 60);

		// add minutes to $hms (with a leading 0 if needed)
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";

		// seconds past the minute are found by dividing the total number of seconds
		// by 60 and using the remainder
		$seconds = intval($seconds % 60);

		// add seconds to $hms (with a leading 0 if needed)
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

		// done!
		return $hms;
	}

	function saveAutoMessage()
	{

        $this->load->library('form_validation');

        if ($this->session->userdata('language_code')=='2') {
            $this->form_validation->set_rules('auto_contents_sms_en','automatic sms','trim|required|xss_clean');
            $this->form_validation->set_rules('auto_contents_mail_en','automatic mail','trim|required|xss_clean');
        }elseif($this->session->userdata('language_code')=='3'){
            $this->form_validation->set_rules('auto_contents_sms_no','automatic sms','trim|required|xss_clean');
            $this->form_validation->set_rules('auto_contents_mail_no','automatic mail','trim|required|xss_clean');
        }else{
            $this->form_validation->set_rules('auto_contents_sms','automatic sms','trim|required|xss_clean');
            $this->form_validation->set_rules('auto_contents_mail','automatic mail','trim|required|xss_clean');
        }
        $this->form_validation->set_error_delimiters('','');

        if ($this->form_validation->run()==FALSE) {
            echo json_encode(validation_errors());
        }else{
            $this->setting_model->saveAutoMessage();
            echo json_encode(array('success'=>true));
        }
	}


	function changePasswordForm()
	{
		$this->load->view('setting/admin/change_password');
	}


	function updateNewPassword()
	{
		$response = $this->setting_model->updateNewPassword();

		if (!$response) {
			echo "fail";
		}else{
			echo "success";
		}
	}

	function listAccounts(){
		$this->load->view('setting/admin/account_list');
	}

	function addAccountForm()
	{
		$this->load->view('setting/admin/add_account');
	}

	function addAccount()
	{

		$id = $this->input->post('account_id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name','First name','trim|required|xss_clean|min_length[2]|max_length[12]');
        $this->form_validation->set_rules('last_name','Last name','trim|required|xss_clean');
        $this->form_validation->set_rules('username','username','trim|required|xss_clean|is_unique[bip_admin_user.username.id.'. $id .']');
        $this->form_validation->set_rules('email','email','trim|required|xss_clean|valid_email');

		if ($id>0)
		$this->form_validation->set_rules('autogeneratedpw','lang:autogeneratedpw','trim|xss_clean|min_length[8]');
		else
		  $this->form_validation->set_rules('autogeneratedpw','lang:autogeneratedpw','trim|required|xss_clean|min_length[8]');

        $this->form_validation->set_rules('contact_number','contact number','trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
	        if ($id>0) {
	          $id = $this->input->post('account_id');
				$data['row'] = $this->setting_model->getAccount($id);
				$this->load->view('setting/admin/add_account',$data);
	        }else{
	          $this->addAccountForm();
	        }
        }else{
            $this->setting_model->addAccount();
            $this->listAccounts();
        }
	}

	function editAccount()
	{
		$id = $this->input->post('account_id');
		$this->setting_model->updateRevisionAdminTable($id,'VIEW');
		$data['row'] = $this->setting_model->getAccount($id);
		$this->load->view('setting/admin/add_account',$data);
	}

	function deleteAccount()
	{
		$id=$this->input->post('account_id');
		$this->setting_model->deleteAccount($id);
		$this->listAccounts();
	}

	function checkUsernameExist(){
		$response = $this->setting_model->checkUsernameExist();
		if ($response) {
			echo 'true';
		}else{
			echo 'false';
		}
	}

	function checkEmailExist(){
		$response = $this->setting_model->checkEmailExist();
		if ($response) {
			echo 'false';
		}else{
			echo 'true';
		}
	}

}
