<?php

class setting_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function getAllIcon() {
		$query = $this->db->query("call getAllIcon(@a)");
		$result1 = $query->result();
		$this->db->freeDBResource();

		$query1 = $this->db->query("select @a as totalRows");
		$row1 = $query1->row();
		$this->db->freeDBResource();
		$totalRows = $row1->totalRows;
		$result = array($result1, $totalRows);
		return $result;
	}

	function getAlliconByLang() {
		$language_code = $this->session->userdata('language_code');
		$query = $this->db->query("SELECT * FROM bip_icon where lang_id=? order by icon_name",array($language_code));
		$result1 = $query->result();
		$totalRows = $query->num_rows();
		$result = array($result1, $totalRows);
		return $result;
	}

	function addIcon() {

		$language_code = $this->session->userdata('language_code');

		$iconName = addslashes(htmlspecialchars($this->input->post('iconName')));
		$iconImage = $this->input->post('iconFileName');

		$query = $this->db->query("call addIcon(@a,'$iconName','$iconImage','$language_code')");
		$query1 = $this->db->query("select @a as icon_id");
		$row1 = $query1->row();

		$iconId = $row1->icon_id;
	}

	function delectIconById($iconId) {
		$row = $this->geticonByIconId($iconId);
		$iconImage = $row->icon_image;
		$file = "images/icons/" . $iconImage;

		if (file_exists($file) && ($iconImage))
			unlink($file);
		$query = $this->db->query("call delectIconById($iconId)");
		$this->db->freeDBResource();
		return $file;
	}

	function geticonByIconId($iconId) {
		$query = $this->db->query("call geticonByiconId($iconId)");
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	// difficulty function  begins
	function addDifficulty() {

		$language_code = $this->session->userdata('language_code');

		$difficultyId = htmlspecialchars($this->input->post('difficultyId'));
		$difficultyName = addslashes(htmlspecialchars($this->input->post('difficultyName')));
		$skin_id = $this->input->post('skin_id');
		$sqlCount = 'select count(id) as count_row from bip_difficulty where difficulty = "'.$difficultyName.'"';
		$newStartPageTemplate = $this->input->post("new_start_page");
		$enableAlert = $this->input->post("enable_msg_alert")>0 ? 1 : 0;
		if(trim($newStartPageTemplate)==""){
			 $newStartPageTemplate = 0;
		}
		$query = $this->db->query($sqlCount);
		$row = $query->row();
		$count = $row->count_row;
		$this->db->freeDBResource();

		if ($difficultyId) {

		   // $query = $this->db->query("call updateDifficultyById('$difficultyId','$difficultyName','$language_code','$enableAlert')");
			$query = $this->db->query("UPDATE bip_difficulty SET difficulty=?,lang_id=?, enable_msg_alert=?,skin_id=? WHERE id=?", array($difficultyName, $language_code, $enableAlert, $skin_id, $difficultyId));

		} else {

			$query = $this->db->query("call addDifficulty(@a,'$difficultyName','$language_code','$enableAlert')");
		 //   $query = $this->db->query("INSERT INTO bip_difficulty SET difficulty=?,lang_id=?, enable_msg_alert=?", array($difficultyName, $language_code, $enableAlert));

			$query1 = $this->db->query("select @a as id");

			$row1 = $query1->row();

			$difficultyId = $row1->id;

		$query = $this->db->query("UPDATE bip_difficulty SET skin_id=?,lang_id=?,skin_id=? WHERE id=?",array($skin_id,$language_code,$skin_id,$difficultyId));

			$this->db->freeDBResource();

			// $permission = $this->session->userdata("permission");
			// if(!empty($permission))
			// {
			// 	$diff_multi = $permission->diff_multi;
			// 	if(!is_array($diff_multi))
			// 		$diff_multi = array();
			// 	if(empty($diff_multi) || !in_array($row->id, $diff_multi))
			// 	{

			// 		$diff_multi[] = $row1->id;
			// 		$permission->diff_multi = $diff_multi;
			// 		$this->session->set_userdata(array("permission"=>$permission));
			// 		$permission = json_encode($permission);
			// 		$query = $this->db->query("update bip_user set permission=? where id = ".$this->session->userdata("user_id"),array($permission));
			// 	}
			// }

			//now copy the difficulty from old to new
			//get all the stages of the difficulty and copy to new one
			if($this->input->post('sourceDifficultyId'))
			{
				$sourceDifficultyId = $this->input->post('sourceDifficultyId');
				$destDifficultyId = $row1->id;

				//get all the stages of the original difficulty
				$sql = "select id from bip_stage where difficulty_id = ? and published <> '-1' order by ordering asc";
				$query = $this->db->query($sql,array($sourceDifficultyId));
				$stages = $query->result();

				foreach ($stages as $stage)
				{
					$stageIdSource = $stage->id;
					$published = 1;
					$this->stage_model->copyStage($destDifficultyId, $stageIdSource, $published);
				}
			}


		}
		//echo $this->db->last_query();

		if ($this->input->post('tag')) {
			$tag = join(',',$this->input->post('tag'));
		}else{
			$tag='';
		}

		if ($difficultyId>0) {

			$hide_graph = $this->input->post('hide_graph');
			$hide_number = $this->input->post('hide_number');

			$this->db->query("UPDATE bip_difficulty set hide_graph=?,hide_number=?,tag=? where id = ? ",array($hide_graph,$hide_number,$tag,$difficultyId));
		}

		$this->db->freeDBResource();

	}

	function addOthersDifficulty(){
		if($this->input->post('sourceDifficultyId'))
			{
				$sourceDifficultyId = $this->input->post('sourceDifficultyId');
				$destDifficultyId = $this->input->post('destinationDifficultyId');

				//get all the stages of the original difficulty
				$sql = "select id from bip_stage where difficulty_id = ? and published <> '-1' order by ordering asc";
				$query = $this->db->query($sql,array($sourceDifficultyId));
				$stages = $query->result();

				foreach ($stages as $stage)
				{
					$stageIdSource = $stage->id;
					$published = 1;
					$this->stage_model->copyStage($destDifficultyId, $stageIdSource, $published);
				}
			}
	}

	function delectDifficultyById($difficultyId) {

		$query = $this->db->query("call delectDifficultyById($difficultyId)");
		$this->db->freeDBResource();
		return $file;
	}

	/**
	 * delete difficulty with related data
	 * @param  int $difficultyId
	 * @return [type]               [description]
	 */
	function delectDifficultyByIdCascade($difficultyId) {

		// $query = $this->db->query("call getStageByDifficulty('$difficultyId')");
		// $stages = $query->result();
		// $this->db->freeDBResource();
		// foreach ($stages as $stage) {
		//     $stageIdSource = $stage->id;
		//     $this->stage_model->deleteStageByIdCascade($stageIdSource);
		// }
		$query = $this->db->query("call delectDifficultyById($difficultyId)");
		$this->db->freeDBResource();
		return $file;
	}

	function getAllDifficulty() {
		$query = $this->db->query("call getAllDifficulty()");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getAllDifficultyByLang()
	{
	  $language_code = $this->session->userdata('language_code');
	  $query = $this->db->query("Select * from bip_difficulty where lang_id=? order by  difficulty",array($language_code));
	  $result = $query->result();
	  return $result;
	}

	function getDifficultyById($difficultyId) {
		$query = $this->db->query("call getDifficultyById($difficultyId)");
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	function getGroupById($groupId) {
		$query = $this->db->query("SELECT group_name,notification from bip_group where id=?",array($groupId));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}
	// difficulty function ends

	function getPageList($offset, $limit, $orderby) {
		$query = $this->db->query("call getAllPages('$offset', '$limit', '$orderby',@a)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getContentById($id) {
		$query = $this->db->query("call getContentById('$id')");
		$result = $query->row();
		//echo $id; print_r($result); die();
		$this->db->freeDBResource();
		return $result;
	}

	function savePageData($menuId, $pageTitle, $content) {
		//echo "saving...";
		$query = $this->db->query("call savePageData('$menuId', '$pageTitle', '$content')");
		$this->db->freeDBResource();
	}

	function getAllGroup() {
		$query = $this->db->query("call getAllGroup()");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getAllGroupByLang() {
		$language_code = $this->session->userdata('language_code');
		$query = $this->db->query("SELECT * from bip_group where lang_id = ? order by group_name asc",array($language_code));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function addupdateGroup() {
		$language_code = $this->session->userdata('language_code');
		$groupid = trim($this->input->post('groupId'));
		if ($groupid == '')
			$groupid = 0;

		$groupName = addslashes(htmlspecialchars($this->input->post('groupName')));
		$notification["reminder"] =  (($this->input->post('notification_reminder')));
		$notification["inactive"] = (($this->input->post('notification_inactive')));
		$notification["expiry"] = (($this->input->post('notification_expiry')));



	   $group_notification = json_encode($notification);

		$query = $this->db->query("call addupdateGroup('$groupid','$groupName','$group_notification','$language_code')");
		/*$insert_id =  (!empty($groupid)) ? $groupid: $this->db->insert_id();*/
		$this->db->freeDBResource();
		// return $insert_id;
	}
	function updateUserPermissionByGroup(){
		$groupName = addslashes(htmlspecialchars($this->input->post('groupName')));
		$user_id = $this->session->userdata('user_id');
		$permission = $this->getPermissionOfPsy();
		//$permission=json_encode($permission);
		$permission=json_decode($permission,true);
		$group = $this->db->query("select * from bip_group where group_name = '$groupName'")->row();
		//echo $this->db->last_query();
		$grp_id = $group->id;
		$rights_per_group_arr = array();
		$single_group_arr = array(
		  'treat_patients' => '1',
		  'manage_users' => '1',
		  'create_psychologists' => '1',
		  'manage_psychologists' => '1',
		  'extract_data' => '1',
			);
		$rights_per_group_arr = $single_group_arr;
				$permission['rights_per_group'][$grp_id] = $rights_per_group_arr;
				$permission = json_encode($permission);
		$this->db->query("UPDATE bip_user set permission='$permission' WHERE id= '$user_id'");

	}

	function updateUserPermissionByDifficulty(){
		$DifficultyName = addslashes(htmlspecialchars($this->input->post('difficultyName')));
		$user_id = $this->session->userdata('user_id');
		$permission = $this->getPermissionOfPsy();
		//$permission = $this->session->userdata('permission');
		//dd($permission);
		$permission=json_decode($permission,true);
		//dd($permission);
		$difficulty = $this->db->query("select * from bip_difficulty where difficulty = '$DifficultyName'")->row();
		//print_r($difficulty);exit;
		//echo $this->db->last_query();
		$diff_id = $difficulty->id;
		//echo $diff_id;exit;
		$rights_per_difficulty_arr = array();
		$single_difficulty_arr = array(
		  'edit_difficulty' => '1',
		  'give_rights_to_edit_difficulty' => '1',
			);
		$rights_per_difficulty_arr = $single_difficulty_arr;
				$permission['rights_per_difficulty'][$diff_id] = $rights_per_difficulty_arr;
				$permission = json_encode($permission);
		$this->db->query("UPDATE bip_user set permission='$permission' WHERE id= '$user_id'");

	}
	function deleteUserPermissionByDifficulty(){

		$DifficultyId = $this->input->post('difficultyId');
		$user_id = $this->session->userdata('user_id');
		$permission = $this->getPermissionOfPsy();
		//$permission=json_encode($permission);
		$permission=json_decode($permission,true);
		unset($permission['rights_per_difficulty'][$DifficultyId]);
		$permission = json_encode($permission);
		$this->db->query("UPDATE bip_user set permission='$permission' WHERE id= '$user_id'");

	}
	function deleteUserPermissionByGroup(){

		$GroupId = $this->input->post('groupId');
		$user_id = $this->session->userdata('user_id');
		$permission = $this->getPermissionOfPsy();
		//$permission=json_encode($permission);
		$permission=json_decode($permission,true);
		unset($permission['rights_per_group'][$GroupId]);
		$permission = json_encode($permission);
		$this->db->query("UPDATE bip_user set permission='$permission' WHERE id= '$user_id'");

	}

	function getPermissionOfPsy(){
		$user_id = $this->session->userdata('user_id');
		$query = $this->db->query("select * from bip_user where id = '$user_id'")->row();
		return $query->permission;
	}

	function editGroupByid($groupId) {
		$query = $this->db->query("call editGroupByid($groupId)");
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	function deletegroup() {
		$groupId = $this->input->post('groupId');
		$query = $this->db->query("call deletegroup($groupId)");
		$this->db->freeDBResource();
	}

	function checkdifficultyinstage() {
		$difficultyid = $this->input->post('difficultyId');
		$result = $this->db->query("SELECT count(*) as totalrow from bip_stage where difficulty_id= ? ",array($difficultyid));
		$result = $result->row();
		if ($result->totalrow == 0) {
			echo true;
		} else {
			echo false;
		}
	}

	function checkuseringroup() {
		$groupid = $this->input->post('groupid');
		$result = $this->db->query("SELECT count(*) as totalrow from bip_user where group_id= ?",array($groupid));
		$result = $result->row();
		if ($result->totalrow == 0) {
			echo true;
		} else {
			echo false;
		}
	}

	function checkslideinIcone() {
		$iconId = $this->input->post('iconId');
		$result = $this->db->query("SELECT count(*) as totalrow from bip_step where icon_id= ? ",array($iconId));
		$result = $result->row();
		if ($result->totalrow == 0) {
			echo true;
		} else {
			echo false;
		}
	}

	function saveAutoMessage()
	{
		$auto_contents_sms = $this->input->post('auto_contents_sms');
		$auto_contents_sms_en = $this->input->post('auto_contents_sms_en');
		$auto_contents_sms_no = $this->input->post('auto_contents_sms_no');

		$auto_contents_mail = $this->input->post('auto_contents_mail');
		$auto_contents_mail_en = $this->input->post('auto_contents_mail_en');
		$auto_contents_mail_no = $this->input->post('auto_contents_mail_no');

		$auto_contents_sms = htmlspecialchars(addslashes($auto_contents_sms));
		$auto_contents_sms_en = htmlspecialchars(addslashes($auto_contents_sms_en));
		$auto_contents_sms_no = htmlspecialchars(addslashes($auto_contents_sms_no));

		$auto_contents_mail = htmlspecialchars(addslashes($auto_contents_mail));
		$auto_contents_mail_en = htmlspecialchars(addslashes($auto_contents_mail_en));
		$auto_contents_mail_no = htmlspecialchars(addslashes($auto_contents_mail_no));

		$query = $this->db->query("update bip_auto_message set message = ?,message_en = ?,message_no = ? where id = 1",array($auto_contents_sms,$auto_contents_sms_en,$auto_contents_sms_no));
		$this->db->freeDBResource();
		$query = $this->db->query("update bip_auto_message set message = ?,message_en = ?,message_no = ? where id= 2",array($auto_contents_mail,$auto_contents_mail_en,$auto_contents_mail_no));
		$this->db->freeDBResource();
	}

	function encode5t($str) {
		for ($i = 0; $i < 5; $i++) {
			$str = strrev(base64_encode($str)); //apply base64 first and then reverse the string
		}
		return $str;
	}

	function decode5t($str) {
		for ($i = 0; $i < 5; $i++) {
			$str = base64_decode(strrev($str)); //apply base64 first and then reverse the string}
		}
		return $str;
	}


	function updateNewPassword(){

		$admin_id= $this->session->userdata('user_id');

		$oldPassword = (string) $this->encode5t($this->input->post('old_password'));
		$query = $this->db->query("SELECT id FROM bip_admin_user WHERE pass=? AND id=?",array($oldPassword,$admin_id));

		$row = $query->row();
		if($query->num_rows()<1){
			return false;
		}else{
			$password = (string) $this->encode5t($this->input->post('password'));
			$this->db->query("UPDATE bip_admin_user SET pass=? WHERE id=?",array($password,$admin_id));

			return true;
		}

	}

	function getAccounts(){
		$query = $this->db->query("SELECT * FROM bip_admin_user WHERE status='1'");
		$result = $query->result();
		return $result;
	}

	function getAccount($id){
		$query = $this->db->query("SELECT * FROM bip_admin_user WHERE id=?",array($id));
		$row = $query->row();
		return $row;
	}

	function addAccount(){

		$first_name = $this->input->post('first_name');
		$first_name = $this->encryption->encrypt($first_name);

		$last_name = $this->input->post('last_name');
		$last_name = $this->encryption->encrypt($last_name);

		$username = $this->input->post('username');

		$email = $this->input->post('email');
		$email = $this->encryption->encrypt($email);

		$reset_password = $this->input->post('reset_password');
		//$password = password_hash($this->input->post('autogeneratedpw'), PASSWORD_BCRYPT);
		$password = $this->bcrypt->hash_password($this->input->post('autogeneratedpw'));

		// $password = (string) $this->encode5t($this->input->post('password'));

		$contact_number = $this->input->post('contact_number');
		$contact_number = $this->encryption->encrypt($contact_number);

		$sms_login = $this->input->post('sms_login');
		$error_notify = $this->input->post('error_notify');

		$id = $this->input->post('account_id');

		$visitor_user_id = $this->session->userdata('user_id');
				$visitor_user_role = $this->session->userdata('user_role');
				$visitor_user_name = ($visitor_user_role==3) ? $this->session->userdata('email'): $this->session->userdata('username');
				$visitor_full_name = $this->session->userdata('first_name').' '.$this->session->userdata('last_name');
				$visitor_ip_address = $this->input->ip_address();
				$visitor_comment = '';

		$insertUpdateData = array(
		  'first_name'=>$first_name,
		  'last_name'=>$last_name,
		  'username'=>$username,
		  'email'=>$email,
		  'contact_number'=>$contact_number,
		  'sms_login'=>$sms_login,
		  'error_notify'=>$error_notify,
		  'visitor_user_id'=>$visitor_user_id,
		  'visitor_user_role'=>$visitor_user_role,
		  'visitor_user_name'=>$visitor_user_name,
		  'visitor_full_name'=>$visitor_full_name,
		  'visitor_ip_address'=>$visitor_ip_address,
		  'visitor_comment'=>$visitor_comment
		);

		if (!empty($id)) {
				$_revision_action = 'UPDATE';

			$this->db->where('id',$id);
			$this->db->update('bip_admin_user',$insertUpdateData);

			if ($reset_password) {
			   $this->db->query("UPDATE bip_admin_user set pass=? WHERE id=?",array($password,$id));
			}
		}else{
				$_revision_action = 'INSERT';
			$this->db->insert('bip_admin_user',$insertUpdateData);

			$id = $this->db->insert_id();
			$this->db->query("UPDATE bip_admin_user set pass=? WHERE id=?",array($password,$id));
		}
		$this->updateRevisionAdminTable($id,$_revision_action);

	}

	function updateRevisionAdminTable($id,$_revision_action){

			$visitor_user_id = $this->session->userdata('user_id');
				$visitor_user_role = $this->session->userdata('user_role');
				$visitor_user_name = ($visitor_user_role==3) ? $this->session->userdata('email'): $this->session->userdata('username');

				$visitor_full_name = $this->session->userdata('first_name').' '.$this->session->userdata('last_name');
										$visitor_full_name = $this->encryption->encrypt($visitor_full_name);

				$visitor_ip_address = $this->input->ip_address();
				$visitor_comment = '';

				$this->db->query("UPDATE bip_admin_user SET visitor_user_id	=?,visitor_user_role=?,visitor_user_name=?,visitor_full_name=?,visitor_ip_address=?,visitor_comment=? WHERE id=?",array($visitor_user_id,$visitor_user_role,$visitor_user_name,$visitor_full_name,$visitor_ip_address,$visitor_comment,$id));

			$this->db->query("INSERT INTO _revision_bip_admin_user (id,first_name,last_name,username,email,pass,contact_number,sms_login,error_notify,visitor_user_id,visitor_user_role,visitor_user_name,visitor_full_name,visitor_ip_address,visitor_comment) SELECT id,first_name,last_name,username,email,pass,contact_number,sms_login,error_notify,visitor_user_id,visitor_user_role,visitor_user_name,visitor_full_name,visitor_ip_address,visitor_comment FROM bip_admin_user WHERE id = '$id'");

				$_revision = $this->db->insert_id();
		$this->db->query("UPDATE _revision_bip_admin_user SET _revision_user_id	=?,_revision_action=?,_revision_timestamp=now() WHERE _revision=?",array($id,$_revision_action,$_revision));
	}

	function deleteAccount($id) {
		$this->db->query("UPDATE bip_admin_user SET status=0 WHERE id=?",array($id));
		$this->setting_model->updateRevisionAdminTable($id,'DELETE');
	}

	function checkUsernameExist(){
		$user_id = $_POST['user_id'];
		//echo $user_id;
		$requestedUsername  = $_POST['username'];
		$query = $this->db->query("SELECT id FROM bip_admin_user WHERE username=? AND id!=?",array($requestedUsername,$user_id));
		// echo $this->db->last_query();
		if ($query->num_rows()>0) {
			return true;
		}else{
			return false;
		}
	}
	function checkemailexist(){
		$user_id = $_POST['user_id'];
		$requestedEmail  = $_POST['email'];
		$query = $this->db->query("SELECT id FROM bip_admin_user WHERE email=? AND id!=?",array($requestedEmail,$user_id));
		if ($query->num_rows()>0) {
			// echo $this->db->last_query();
			return true;
		}else{
			/*$query1 = $this->db->query("SELECT id FROM bip_user WHERE email='$requestedEmail' OR username='$requestedEmail'");
			if ($query1->num_rows()>0) {
				// echo $this->db->last_query();
				return true;
			}else{
				// echo $this->db->last_query();
				return false;
			}*/
			return false;
		}
	}

	function getAllSkins(){
			$query = $this->db->query("SELECT * FROM bip_skin");
			$result = $query->result();
			return $result;
		}

		function getSkinNameById($id){
			$query = $this->db->query("SELECT skin_name FROM bip_skin WHERE id=?",array($id));
			$row = $query->row();
			return $row->skin_name;
		}
}

?>
