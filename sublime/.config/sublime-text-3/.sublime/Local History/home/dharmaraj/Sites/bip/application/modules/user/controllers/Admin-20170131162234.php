<?php

class Admin extends Admin_Controller {

    function __construct() {
        parent::__construct();
         $this->load->model('statistics/statistics_model');
    }

    function index() {

        // setting default value when user click on menu
        $this->session->set_userdata(array('userSearch' => ''));
        $this->session->set_userdata(array('AlterBy' => 'asc'));
        $this->session->set_userdata(array('userOrderBy' => 'first_name'));

        $data["main_content"] = 'user/admin/list';
        $data["search_txt"] = $this->input->post('search_txt');
        $this->load->view('includes/admin/template', $data);
    }

    function listAllUser() {
        $usertype = $this->session->userdata('logintype');
        $psychologistid = ($usertype == 'Psychologist') ? $this->session->userdata('user_id') : '';
        $data["search_txt"] = $this->input->post('search_txt');
        $data['psychologist_id'] = $this->input->post('psychologist_id');
        $data['difficulty_id'] = $this->input->post('difficulty_id');
        $data['group_id'] = $this->input->post('group_id');
        $this->load->view('user/admin/list', $data);
    }

    function filterUserByParams() {
        $usertype = $this->session->userdata('logintype');
        $data["search_txt"] = $this->input->post('search_txt');

        $data['psychologist_id'] = $this->input->post('psychologist_id');
        $data['difficulty_id'] = $this->input->post('difficulty_id');
        $data['group_id'] = $this->input->post('group_id');

        $this->load->view('user/admin/list', $data);
    }

    function listUserByPsychologist() {
        $usertype = $this->session->userdata('logintype');
        //$psychologistid = ($usertype == 'Psychologist') ? $this->session->userdata('user_id') : '';
        $data["search_txt"] = $this->input->post('search_txt');
        $data['psychologist_id'] = $this->input->post('psychologist_id');
        $this->load->view('user/admin/list', $data);
    }

    function listUserByDifficulty() {
        $usertype = $this->session->userdata('logintype');
        //$diffcultyid = ($usertype == 'Psychologist') ? $this->session->userdata('user_id') : '';
        $data["search_txt"] = $this->input->post('search_txt');
        $data['difficulty_id'] = $this->input->post('difficulty_id');
        $this->load->view('user/admin/list', $data);
    }

    function listUserByGroup() {
        $usertype = $this->session->userdata('logintype');
        //$groupid = ($usertype == 'Psychologist') ? $this->session->userdata('user_id') : '';
        $data["search_txt"] = $this->input->post('search_txt');
        $data['group_id'] = $this->input->post('group_id');
        $this->load->view('user/admin/list', $data);
    }

    function addUserForm() {
        $userId = $this->session->userdata('user_id');
        $data['group'] = $this->user_model->getAllGroupByLang();
        $data['psychology'] = $this->user_model->getAllpsychologyByLangHavingTreatPatientPermission();
        $data['selected_grp'] = $this->input->post('selected_grp_id');
        $data['rows']->permission = $this->user_model->getUserByUserId($userId)->permission;

        $this->load->view('user/admin/add_user', $data);
    }
    function get_selected_grp(){
       $data['group'] = $this->user_model->getAllGroupByLang();
       $selected_grp = $this->input->post('selected_grp_id');
        $psychology = json_decode(json_encode($this->user_model->getAllpsychologyByLangHavingTreatPatientPermission($selected_grp)),true);
    //echo $data['selected_grp'];exit;

        $filteredPsycho=[];
        $permission=[];

        foreach ($psychology as $key => $pyscho) {

                $perm=['permission'=>json_decode($pyscho['permission'],true),'psyid'=>$pyscho['id']];
                $permission[] = $perm;
        }
        // echo $selected_grp;
        $group_arr=[];
         // print_r($permission);
        foreach($permission as $keys =>$val){
    // echo "test";

            if(isset($val['permission']['rights_per_group'][$selected_grp])){

                $group_arr[]= $val['psyid'];
            }
        }

        // print_r($group_arr);
         foreach ($psychology as $key => $pyscho) {
               if(!in_array($pyscho['id'],$group_arr)){
                    continue;
                }
                $pyscho['first_name'] = $this->encryption->decrypt($pyscho['first_name']);
                $pyscho['last_name'] = $this->encryption->decrypt($pyscho['last_name']);
                 $filteredPsycho[]= $pyscho;
        }


        echo json_encode($filteredPsycho);
    }

    function addUser() {

      $userId = $this->input->post('userId');

      $this->load->library('form_validation');
      $this->form_validation->set_rules('username','lang:username','trim|required|xss_clean|min_length[2]');
      $this->form_validation->set_rules('oldUsername','lang:username','trim|xss_clean');

      if ($userId>0)
	      $this->form_validation->set_rules('autogeneratedpw','lang:autogeneratedpw','trim|xss_clean|min_length[5]');
	  else
	      $this->form_validation->set_rules('autogeneratedpw','lang:autogeneratedpw','trim|required|xss_clean|min_length[5]');

      $this->form_validation->set_rules('firstName','lang:firstName','trim|required|xss_clean');
      $this->form_validation->set_rules('lastName','lang:lastName','trim|required|xss_clean');
      $this->form_validation->set_rules('email', 'lang:email', 'trim|valid_email');
      $this->form_validation->set_rules('address','lang:address','trim|xss_clean');
      $this->form_validation->set_rules('city','lang:city','trim|xss_clean');
      $this->form_validation->set_rules('contact_number','lang:contact_number','trim|xss_clean');
      $this->form_validation->set_rules('contact_number_1','lang:contact_number_1','trim|xss_clean');
      $this->form_validation->set_rules('from','lang:from','trim|required|xss_clean');
      $this->form_validation->set_rules('to','lang:to','trim|required|xss_clean');

      if ($this->form_validation->run() == FALSE) {
        $userId = $this->input->post('userId');
        if ($userId>0) {
	        $data['task'] = "edit_user";
	        $data['rows'] = $this->user_model->getUserByUserId($userId);
	        $data['group'] = $this->user_model->getAllGroupByLang();
	        $edit_psy_id = $this->input->post('userId');
	        $data['edit_psy_id'] = $this->input->post('userId');
	        $data['psy']->permission = $this->user_model->getUserByUserId($edit_psy_id)->permission;
	        $data['psychology'] = $this->user_model->getAllpsychologyByLang();
	        $data['current']="edit";
	        $this->load->view('user/admin/add_user', $data);
        }else{
          $this->addUserForm();
        }
      }else{
        $this->user_model->addUser();
        echo "success";
            // $this->listAllUser();
      }
    }

    function createPassword() {
        $pw = $this->user_model->createPassword(8);
        echo $pw;
    }

    function editUser() {
        $userId = $_POST['userId'];
        $this->user_model->updateRevisionUserTable($userId,'VIEW');
        $data['task'] = "edit_user";
        $data['rows'] = $this->user_model->getUserByUserId($userId);
        $data['group'] = $this->user_model->getAllGroupByLang();
        $edit_psy_id = $this->input->post('userId');
        $data['edit_psy_id'] = $this->input->post('userId');
        $data['psy']->permission = $this->user_model->getUserByUserId($edit_psy_id)->permission;
        $data['psychology'] = $this->user_model->getAllpsychologyByLang();
        $data['current']="edit";
        $this->load->view('user/admin/add_user', $data);
    }

    function editSave() {
        $this->user_model->addUser();
        $this->listAllUser();
    }

    function deleteUser() {
        $userId = $this->input->post('userId');
        $this->user_model->delectUserById($userId);

        $this->listAllUser();
    }

    function checkUsername() {
        $username = $this->input->post('username');
        $userExists = $this->user_model->checkUsername($username);
        echo $userExists;
    }

    function changeUsername($userId) {
        $data['userId'] = $userId;
        $this->load->view("user/admin/change_username", $data);
    }

    function getPsychologyByDifficulty() {
        $difficultyid = trim($this->input->post('difficultyid'));
        $rsdata = $this->user_model->getPsychologyByDifficulty($difficultyid);
        if ($rsdata) {
            foreach ($rsdata as $data) {
                echo '<option value="' . $data->id . '">' . $data->first_name . ' ' . $data->last_name . '</option>';
            }
        } else {
            echo '<option value="">No Psychologist Available</option>';
        }
    }

    function checkdeleteUser() {
        $this->user_model->checkdeleteUser();
    }

    function FormExportExcel() {
        $result = $this->user_model->listgroup();
        $data['result'] = $result;
        $this->load->view('user/admin/group', $data);
    }

    function generateXlsReport($groupId) {

        $data["stepList"] = $this->user_model->getStepsForXls($groupId);
        $data["groupId"] = $groupId;

        $strSql = "SELECT group_name FROM bip_group WHERE id='$groupId'";
        $query = $this->db->query($strSql);
        $result = $query->row();
        $groupName = str_ireplace(" ", "_", $result->group_name);
        $this->load->view("user/admin/excel", $data);
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=BIP_" . $groupName . "_" . time() . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        //$result=$this->user_model->exportexcelact_group($this->uri->segment(4));
    }

	function checkdifficultyName()
	{
		$difficultyName = $this->input->post('difficultyName');
        $difficultyExists = $this->user_model->checkdifficultyName($difficultyName);
        echo $difficultyExists;
	}
}
