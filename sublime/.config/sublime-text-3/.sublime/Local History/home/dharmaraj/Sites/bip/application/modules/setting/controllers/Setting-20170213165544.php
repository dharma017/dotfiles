<?php

class Setting extends Public_Controller
{

   	function __construct()
   	{
		parent::__construct();
		$this->load->model('setting/setting_model');
   	}


	function index()
   	{
		$data["main_content"] = 'setting/admin/home';
	 	$this->load->view('includes/setting/template',$data);
 	}

	function listAllIcon()
	{
		$result = $this->setting_model->getAllicon();
		$data["main_content"] = 'setting/admin/icon_list';
		$this->load->view('setting/setting/icon_list');
	}

	function addIconForm()
	{
		$this->load->view('setting/setting/add_icon');
	}


	function addIcon()
	{
		$this->setting_model->addIcon();
		$this->listAllIcon();
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
		$result = $this->setting_model->getAllicon();
		$data["main_content"] = 'setting/admin/difficulty_list';
		$this->load->view('setting/setting/difficulty_list');
	}

	function addDifficultyForm()
	{

		$this->load->view('setting/setting/add_difficulty');
	}

	function editDifficulty()
	{

		$difficultyId 	= $this->input->post("difficultyId");
		$row = $this->setting_model->getDifficultyById($difficultyId);

		$data['difficultyName'] = $row->difficulty;
		$data['difficultyId'] 	= $row->id;
		$this->load->view('setting/setting/add_difficulty',$data);
	}

	function addDifficulty()
	{
    $this->load->library('form_validation');
    $this->form_validation->set_rules('difficultyName','lang:difficultyName','trim|required|xss_clean|min_length[1]');
    if ($this->form_validation->run() == FALSE) {
       $this->addDifficultyForm();
    }else{
        $this->setting_model->addDifficulty();
		$this->setting_model->updateUserPermissionByDifficulty();
        $this->listAllDifficulty();
    }
	}

	function deleteDifficulty()
	{
		$difficultyId=$this->input->post('difficultyId');
		$file_name = $this->setting_model->delectDifficultyById($difficultyId);
		$this->setting_model->deleteUserPermissionByDifficulty($difficultyId);		
		$this->listAllDifficulty();
	}

	// code for pages

	function addGroupForm()
	{
		$data['todo']		= "add";
		$this->load->view('setting/setting/add_group',$data);
	}

	function getAllGroup()
	{
		$this->load->view('setting/setting/group_list');
	}

	function addGroup()
	{
      $this->load->library('form_validation');
      $this->form_validation->set_rules('groupName','lang:groupName','trim|required|xss_clean|min_length[1]');
      if ($this->form_validation->run() == FALSE) {
         $this->addGroupForm();
      }else{
	$groupId = $this->setting_model->addupdateGroup();
	$this->setting_model->updateUserPermissionByGroup($groupId);
        $this->getAllGroup();
      }
	}

	function editGroup()
	{
		$groupId 	= 	$this->input->post("groupId");
		$row 		= 	$this->setting_model->editGroupByid($groupId);

		$data['groupName'] = $row->group_name;
		$data['groupId']   = $row->id;
		$data['todo']	   = "edit";
		//echo "ate".$data['todo'];
		$this->load->view('setting/setting/add_group',$data);
	}

	function deletegroup()
	{
		$groupId = $this->input->post("groupId");
		$this->setting_model->deletegroup();
		$this->setting_model->deleteUserPermissionByGroup($groupId);
	}

	function checkuseringroup()
	{
		$this->setting_model->checkuseringroup();
	}

        function checkdifficultyinstage()
	{
		$this->setting_model->checkdifficultyinstage();
	}
}
