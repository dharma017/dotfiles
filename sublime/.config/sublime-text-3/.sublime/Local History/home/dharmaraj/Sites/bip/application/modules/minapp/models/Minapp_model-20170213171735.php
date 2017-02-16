<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class minapp_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	function checkAppActivated(){

		$total_app_message = $this->getTotalNewAppMessage();
		$this->session->set_userdata('total_app_message_temp', $total_app_message);
		$this->session->set_userdata('app_activated','no');

		if ($this->session->userdata('logintype') == "Psychologist"){
			$this->session->set_userdata('app_activated','yes');
			return true;
		}else{
			$patientId = $this->session->userdata("user_id");

			$query = $this->db->query("SELECT count(*) as total FROM bip_user WHERE id=? AND app_status='1'",array($patientId));
			$row = $query->row();
			if ($row->total>0) {    // logged in as app user
				$this->session->set_userdata('app_activated','yes');
				return true;
			}
		}
		return false;
	}

	function getPushNotifyData()
	{
	   $query=$this->db->query("SELECT * FROM bip_notify_app WHERE difficulty_id='0' LIMIT 1");
	   $row=$query->row();
	   return $row;
	}

	function savePushNotification()
	{
		$feedback_message=$this->input->post('feedback_message');
		$reminder1_message=$this->input->post('reminder1_message');
		$reminder2_message=$this->input->post('reminder2_message');

		$feedback_status = $this->input->post('feedback_status');
		$reminder1_status = $this->input->post('reminder1_status');
		$reminder2_status = $this->input->post('reminder2_status');

		$feedback_xdays = $this->input->post('feedback_xdays');
		$reminder1_xdays = $this->input->post('reminder1_xdays');
		$reminder2_xdays = $this->input->post('reminder2_xdays');

		$difficulty_id= $this->input->post('diffId');

		$insertArray = array(
			$difficulty_id,
			$feedback_message,
			$reminder1_message,
			$reminder2_message,
			$feedback_status,
			$reminder1_status,
			$reminder2_status,
			$feedback_xdays,
			$reminder1_xdays,
			$reminder2_xdays
		);

		$this->db->query("INSERT INTO bip_notify_app (difficulty_id,feedback_message,reminder1_message,reminder2_message,feedback_status,reminder1_status,reminder2_status,feedback_xdays,reminder1_xdays,reminder2_xdays) VALUES (?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE feedback_message=VALUES(feedback_message),reminder1_message=VALUES(reminder1_message),reminder2_message=VALUES(reminder2_message),feedback_status=VALUES(feedback_status),reminder1_status=VALUES(reminder1_status),reminder2_status=VALUES(reminder2_status),feedback_xdays=VALUES(feedback_xdays),reminder1_xdays=VALUES(reminder1_xdays),reminder2_xdays=VALUES(reminder2_xdays)",$insertArray);

		if ($this->db->affected_rows()>0) {
			return true;
		}else{
			return false;
		}
	}

	function setTreatment(){

		$difficulty_id = $this->input->post('difficulty');

		$frm_name = $this->input->post('frm_name');

		switch ($frm_name) {
			case 'frmSetTreatment':
				$rating = $this->input->post('rating');
				$this->db->query("INSERT INTO bip_treatment_app (difficulty_id,rating) VALUES (?,?) ". "ON DUPLICATE KEY UPDATE rating=VALUES(rating)",array($difficulty_id,$rating));
				break;

			case 'frmSlide_1':
				$anxiety = $this->input->post('anxiety');
				$zero = $this->input->post('zero');
				$ten = $this->input->post('ten');
				$txt_button = $this->input->post('txt_button');
				$rating = 1;

				$this->db->query("INSERT INTO bip_treatment_app (difficulty_id,rating,anxiety,zero,ten,txt_button) VALUES (?,?,?,?,?,?) ". "ON DUPLICATE KEY UPDATE rating=1,anxiety=VALUES(anxiety),zero=VALUES(zero),ten=VALUES(ten),txt_button=VALUES(txt_button)",array($difficulty_id,$rating,$anxiety,$zero,$ten,$txt_button));
				break;

			case 'frmSlide1':
				$slide1_headline = $this->input->post('slide1_headline');
				$slide1_text = $this->input->post('slide1_text');
				$slide1_button = $this->input->post('slide1_button');
				$rating = 2;

				$this->db->query("INSERT INTO bip_treatment_app (difficulty_id,rating,slide1_headline,slide1_text,slide1_button) VALUES (?,?,?,?,?) ". "ON DUPLICATE KEY UPDATE rating=2,slide1_headline=VAlUES(slide1_headline),slide1_text=VALUES(slide1_text),slide1_button=VALUES(slide1_button)",array($difficulty_id,$rating,$slide1_headline,$slide1_text,$slide1_button));
				break;

			case 'frmSlide2':
				$slide2_headline = $this->input->post('slide2_headline');
				$slide2_zero = $this->input->post('slide2_zero');
				$slide2_ten = $this->input->post('slide2_ten');
				$slide2_button = $this->input->post('slide2_button');
				$rating = 2;

				$this->db->query("INSERT INTO bip_treatment_app (difficulty_id,rating,slide2_headline,slide2_zero,slide2_ten,slide2_button) VALUES (?,?,?,?,?,?) ". "ON DUPLICATE KEY UPDATE rating=2,slide2_headline=VALUES(slide2_headline),slide2_zero=VALUES(slide2_zero),slide2_ten=VALUES(slide2_ten),slide2_button=VALUES(slide2_button)",array($difficulty_id,$rating,$slide2_headline,$slide2_zero,$slide2_ten,$slide2_button));
				break;

			case 'frmSlide3':
				$slide3_headline = $this->input->post('slide3_headline');
				$slide3_image = $this->input->post('slide3_image');
				$slide3_text = $this->input->post('slide3_text');
				$slide3_button = $this->input->post('slide3_button');
				$slide3_timing = $this->input->post('slide3_timing');
				$rating = 2;

				$this->db->query("INSERT INTO bip_treatment_app (difficulty_id,rating,slide3_headline,slide3_image,slide3_text,slide3_button,slide3_timing) VALUES (?,?,?,?,?,?,?) ". "ON DUPLICATE KEY UPDATE rating=2,slide3_headline=VALUES(slide3_headline),slide3_image=VALUES(slide3_image),slide3_text=VALUES(slide3_text),slide3_button=VALUES(slide3_button),slide3_timing=VALUES(slide3_timing)",array($difficulty_id,$rating,$slide3_headline,$slide3_image,$slide3_text,$slide3_button,$slide3_timing));
				// echo $this->db->last_query();exit;
				break;

			case 'frmSlide4':
				$slide4_headline = $this->input->post('slide4_headline');
				$slide4_zero = $this->input->post('slide4_zero');
				$slide4_ten = $this->input->post('slide4_ten');
				$slide4_button = $this->input->post('slide4_button');
				$rating = 2;

				$this->db->query("INSERT INTO bip_treatment_app (difficulty_id,rating,slide4_headline,slide4_zero,slide4_ten,slide4_button) VALUES (?,?,?,?,?,?) ". "ON DUPLICATE KEY UPDATE rating=2,slide4_headline=VALUES(slide4_headline),slide4_zero=VALUES(slide4_zero),slide4_ten=VALUES(slide4_ten),slide4_button=VALUES(slide4_button)",array($difficulty_id,$rating,$slide4_headline,$slide4_zero,$slide4_ten,$slide4_button));
				break;

			case 'frmSlide5':
				$slide5_headline = $this->input->post('slide5_headline');
				$slide5_time_x = $this->input->post('slide5_time_x');
				$slide5_time_y = $this->input->post('slide5_time_y');
				$slide5_time_text1 = $this->input->post('slide5_time_text1');
				$slide5_time_text2 = $this->input->post('slide5_time_text2');
				$slide5_time_text3 = $this->input->post('slide5_time_text3');
				$slide5_button = $this->input->post('slide5_button');
				$rating = 2;

				$this->db->query("INSERT INTO bip_treatment_app (difficulty_id,rating,slide5_headline,slide5_time_x,slide5_time_y,slide5_time_text1,slide5_time_text2,slide5_time_text3,slide5_button) VALUES (?,?,?,?,?,?,?,?,?) ". "ON DUPLICATE KEY UPDATE rating=2,slide5_headline=VALUES(slide5_headline),slide5_time_x=VALUES(slide5_time_x),slide5_time_y=VALUES(slide5_time_y),slide5_time_text1=VALUES(slide5_time_text1),slide5_time_text2=VALUES(slide5_time_text2),slide5_time_text3=VALUES(slide5_time_text3),slide5_button=VALUES(slide5_button)",array($difficulty_id,$rating,$slide5_headline,$slide5_time_x,$slide5_time_y,$slide5_time_text1,$slide5_time_text2,$slide5_time_text3,$slide5_button));
				break;

			case 'frmSlide6':
				// $slide6_time_x = $this->input->post('slide6_time_x');
				$count = $this->input->post('frm_count');
				$inputs = array();
				for ($i=1; $i <= 100; $i++) {
					$pscnt = utf8_decode($this->input->post('p_scnt_'.$i));
					if (!empty($pscnt)) {
						$inputs[] = $this->input->post('p_time_'.$i).'~~~'.$this->input->post('p_scnt_'.$i);
					}
				}
				// $slide6_message = json_encode($inputs,JSON_UNESCAPED_UNICODE);
				$slide6_message = $this->my_json_encode($inputs);
				$this->db->query("INSERT INTO bip_treatment_app (difficulty_id,slide6_message) VALUES (?,?) ". "ON DUPLICATE KEY UPDATE slide6_message=VALUES(slide6_message)",array($difficulty_id,$slide6_message));
				break;


			default:
				$rating = $this->input->post('rating');
				$this->db->query("INSERT INTO bip_treatment_app (difficulty_id,rating) VALUES (?,?) ". "ON DUPLICATE KEY UPDATE rating=VALUES(rating)",array($difficulty_id,$rating));
				break;
		}
	}

	function my_json_encode($arr)
	{

			//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
			array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
			return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');

	}

	function getTreatmentSetting(){
		$difficulty_id = $this->input->post('diffId');
		$result= $this->db->query("SELECT * FROM bip_treatment_app WHERE difficulty_id=? LIMIT 1",array($difficulty_id));
		$result = $result->row_array();
		return $result;
	}

	function getReminderByDifficulty(){
		$difficulty_id = $this->input->post('diffId');
		$result= $this->db->query("SELECT * FROM bip_push_reminder WHERE difficulty_id=? LIMIT 1",array($difficulty_id));
		$result = $result->row_array();
		return $result;
	}

	function getPushSettingByDiffId(){
		$difficulty_id = $this->input->post('diffId');
		$result= $this->db->query("SELECT feedback_message, reminder1_message, reminder2_message, feedback_xdays, reminder1_xdays, reminder2_xdays, feedback_status, reminder1_status, reminder2_status FROM bip_notify_app WHERE difficulty_id=? LIMIT 1",array($difficulty_id));
		$result = $result->row_array();
		return $result;
	}

	function addProblemCategory() {
		$difficulty_id = $this->input->post('difficulty');
		$problem = addslashes(htmlspecialchars($this->input->post('problem')));

		$problemId=$this->input->post('problemId');
		$problemId=(!empty($problemId)) ? $problemId: 0;

		$query = $this->db->query("call addUpdateProblemCategory('$problemId','$difficulty_id','$problem')");
		$this->db->freeDBResource();
	}

	function addTask() {
		$difficulty_id = join(',',$this->input->post('difficulty_id'));
		$problem_id = join(',',$this->input->post('problem_id'));
		$task = addslashes(htmlspecialchars($this->input->post('task')));

		$taskId=$this->input->post('taskId');

		if ($this->input->post('tag')) {
			$tag = join(',',$this->input->post('tag'));
		}else{
			$tag='';
		}

		if ($taskId) {
			$this->db->query("UPDATE bip_tasks set difficulty_id=?,problem_id=?, task=?,tag=? WHERE id=?",array($difficulty_id,$problem_id,$task,$tag,$taskId));
		}else{
			$this->db->query("INSERT INTO bip_tasks (difficulty_id,problem_id,task,tag) VALUES (?,?,?,?)",array($difficulty_id,$problem_id,$task,$tag));
		}

		$this->db->freeDBResource();
	}

	function getAllTreatmentSettings(){
		$query = $this->db->query("SELECT ta.*,d.difficulty from bip_treatment_app ta
	join bip_difficulty d on d.id=ta.difficulty_id
	order by ta.id desc");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}
    function getAllTreatmentSettingsByDiffID($diffId){
        $query = $this->db->query("SELECT ta.*,d.difficulty from bip_treatment_app ta
    join bip_difficulty d on d.id=ta.difficulty_id where ta.difficulty_id IN ($diffId)
    order by ta.id desc");
        $result = $query->result();
       // echo $this->db->last_query();
        $this->db->freeDBResource();
        return $result;
    }

	function getAllDifficultyNotInSettings(){
		$language_code = $this->session->userdata('language_code');
		 $query = $this->db->query("SELECT
				bd.*
			FROM
				bip_difficulty bd
			LEFT JOIN bip_treatment_app bta ON (bta.difficulty_id = bd.id)
			WHERE
				bd.lang_id = ? AND bta.rating IS NULL",array($language_code));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getAllProblems(){
		$query = $this->db->query("call getAllProblems()");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}
    function getAllProblemsBydiffId($diffId){
        $query = $this->db->query("select * from bip_problem_category where difficulty_id IN ($diffId) ");
        $result = $query->result();
        //echo $this->db->last_query();
        $this->db->freeDBResource();
        //dd($result);
        return $result;
    }

	function getAllTasks($offset=0,$datalimit=50,$orderBy='desc',$diffId=0,$filterId=0,$filterType='treatment'){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;

		$query = $this->db->query("call getAllTasks('$offset','$datalimit','$orderBy','$diffId','$filterId','$filterType',@a)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}
    function getAllTasksBydiffId($offset=0,$datalimit=50,$orderBy='desc',$diffId=0,$filterId=0,$filterType='treatment'){
        if (!$offset) $offset = 0;
        if (!$datalimit) $datalimit = 50;
        if($filterId > 0){
        if($filterType == "treatment"){
        $query = $this->db->query("SELECT * FROM bip_tasks where FIND_IN_SET($filterId,difficulty_id) AND is_deleted = 0 order by id  $orderBy LIMIT $offset,$datalimit ");
    }else{
            $query = $this->db->query("SELECT * FROM bip_tasks where FIND_IN_SET($filterId,problem_id) AND problem_id IN($filterId) AND is_deleted = 0 order by id $orderBy LIMIT $offset,$datalimit");
    }
}else{
     $query = $this->db->query("SELECT * FROM bip_tasks where difficulty_id IN ($diffId) AND is_deleted = 0 order by id  $orderBy LIMIT $offset,$datalimit ");
}
        $result = $query->result();
         //echo $this->db->last_query();
        $this->db->freeDBResource();
        return $result;

    }

	function totalTasksRows($difficulty_id,$problem_id,$type){
		if (!empty($difficulty_id)) {

			if ($type=='problem') {
				$query=$this->db->query("SELECT count(*) as totalrow FROM bip_tasks WHERE difficulty_id=? AND problem_id=?",array($difficulty_id,$problem_id));
			}else{
				$query=$this->db->query("SELECT count(*) as totalrow FROM bip_tasks WHERE difficulty_id=?",array($difficulty_id));
			}

		}else{
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_tasks");
		}

		$row=$query->row();
		return $row->totalrow;
	}

	function checkProblemInTasks(){
		$problem_id = $this->input->post('problem_id');
		$query = $this->db->query("SELECT count(*) as total from bip_tasks WHERE problem_id=?",array($problem_id));
		$row = $query->row();
		if ($row->total>0) {
			return true;
		} else {
			return false;
		}
	}

	function delectProblemById(){
		$problem_id = $this->input->post('problem_id');
		$query = $this->db->query("call deleteProblemById($problem_id)");
		$this->db->freeDBResource();
	}

	function deleteTreatmentSettingById(){
		$treatment_id = $this->input->post('treatment_id');
		$query = $this->db->query("delete from bip_treatment_app where id=?",array($treatment_id));
	}

	function countActiveTasks()
	{
		$task_id=$this->input->post('task_id');
		$query=$this->db->query("SELECT count(*) as total FROM bip_training_app WHERE task_id=?",array($task_id));
		$row= $query->row();
		if ($row->total>0) {
			return true;
		}else{
			return false;
		}
	}

	function delectTaskById(){
		$task_id = $this->input->post('task_id');
		$query = $this->db->query("UPDATE bip_tasks SET is_deleted=1 WHERE id=?",array($task_id));
		$this->db->freeDBResource();
	}

	function getTreatmentSettingById() {
		$treatment_id = $this->input->post('treatment_id');
		$query = $this->db->query("SELECT * FROM bip_treatment_app WHERE id=? LIMIT 1",array($treatment_id));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	function getProblemById() {
		$problem_id = $this->input->post('problem_id');
		$query = $this->db->query("call getProblemById($problem_id)");
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	function getTaskById() {
		$task_id = $this->input->post('task_id');
		$query = $this->db->query("call getTaskById($task_id)");
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	function getTasksPerDifficultyNotSet($userId,$diffId) {
				$likeString = '%,$userId,%';
		$query = $this->db->query("SELECT * from bip_tasks WHERE difficulty_id='$diffId' AND CONCAT(',' , user_id , ',') NOT LIKE ? AND type='admin'",array($diffId,$likeString));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getProblemsPerDifficulty($ids)
	{
		$query = $this->db->query("SELECT id,problem from bip_problem_category WHERE difficulty_id  IN (?)",array($ids));
		$result = $query->result();
		return $result;
	}

	function convert_multi_array($array,$break=1) {
	  $br = $break==0?"":"<br>";
	  $out = implode(" ,$br ",array_map(function($a) {return implode("~",$a);},$array));
	  return $out;
	}

	function convert_to_single_array($array) {
	  $out = implode(",",array_map(function($a) {return implode("~",$a);},$array));
	  return $out;
	}

	function getName($tblname,$ids,$break=1){
		if ($tblname=='bip_difficulty') {
			$query = $this->db->query("SELECT difficulty from $tblname WHERE id  IN (?)",array($ids));
		}else{
			$query = $this->db->query("SELECT problem from $tblname WHERE id IN (?)",array($ids));
		}
		$names = $query->result_array();
		return $this->convert_multi_array($names,$break);
	}

	function getProblemsByAppUser($user_id,$problemId=0)
	{
		$sql="SELECT
			  pc.`problem`
			FROM
			  bip_training_app ta
			  JOIN bip_tasks bt
				ON (bt.id = ta.task_id)
			   JOIN `bip_problem_category` pc ON (pc.id=bt.`problem_id`)
			";
		if ($problemId) {
			$sql.=" WHERE ta.user_id = ? AND pc.id=? GROUP BY bt.`problem_id`";
						$bindArray = array($user_id,$problemId);
					}else{
			$sql.=" WHERE ta.user_id = ? GROUP BY bt.`problem_id`";
						$bindArray = array($user_id);
		}
		$query=$this->db->query($sql,$bindArray);
		$result=$query->result_array();
		return $this->convert_multi_array($result);
	}


	function getStatTrainingsByGroup($user){
			$query = $this->db->query("SELECT
										user_id,count(*) as exposures
									FROM
										`bip_training_app`
									WHERE
										user_id = ?",array($user->id));
			$result = $query->result();

			$psychologist_id=$user->psychologist_id;

			$assignedPids=$this->getAssignedProblems($psychologist_id);

			$arr=array();
			foreach ($result as $k => $res) {

				$arr[$k]['user_id']=$res->user_id;

				$arr[$k]['problems']='';
				if (!empty($assignedPids)) {
					$json=json_decode($assignedPids,true);
					$problemsList= $json[$res->user_id];
					if (!empty($problemsList)) {
						$joinPlist=join(',',$problemsList);
						$arr[$k]['problems']=$this->getName('bip_problem_category',$joinPlist);
					}
				}
				$arr[$k]['name']=$user->username;
				$arr[$k]['difficulty']=$user->difficulty;
				$arr[$k]['started_app']=$user->created_at;
				$arr[$k]['exposures']=$res->exposures;
			}
			if (empty($arr)) {
				$arr[0]['user_id']=$user->id;
				$arr[0]['problems']='';
				$arr[0]['name']=$user->username;
				$arr[0]['difficulty']=$user->difficulty;
				$arr[0]['started_app']='';
				$arr[0]['exposures']=0;
			}
			return $arr;
		}

	function getXlsAppReportByGroup(){

		$group_id = $this->input->post('group_id');

		$query = $this->db->query("SELECT
							bu.id,
							bu.username,
							bu.first_name,
							bu.last_name,
							bu.difficulty_id,
							bd.difficulty,
							bu.psychologist_id,
							bua.created_at,
							(
								SELECT
									count(*) AS count
								FROM
									bip_training_app q1
								WHERE
									q1.user_id = bu.id
								AND practice = '1'
							) AS total
						FROM
							bip_user bu
						LEFT JOIN bip_difficulty bd ON (bd.id = bu.difficulty_id)
						LEFT JOIN bip_user_app bua ON (bua.user_id = bu.id)
					WHERE
						bu.group_id = ? AND
						bua.created_at is not NULL
					GROUP BY bu.username
				",array($group_id));

		return $query->result();

	}

	function getAllAppUsersByParams()
	{
		$userId=$this->session->userdata('user_id');
		$filterId=$this->input->post('filterId');
		$filterType=$this->input->post('filterType');
		if ($filterType=='treatment') {
			if ($filterId)
				$where="AND bu.difficulty_id='$filterId'";
		}elseif($filterType=='problem'){
			$diffId=$this->input->post('diffId');
			$where="AND bu.difficulty_id='$diffId'";
		}
		$sql= "SELECT
					bu.id,
					bu.username,
					bu.first_name,
					bu.last_name,
					bu.difficulty_id,
					bd.difficulty,
					bu.psychologist_id,
					bua.created_at,
					(
						SELECT
							count(*) AS count
						FROM
							bip_training_app q1
						WHERE
							q1.user_id = bu.id
						AND practice = '1'
					) AS total
				FROM
					bip_user bu
				LEFT JOIN bip_difficulty bd ON (bd.id = bu.difficulty_id)
				LEFT JOIN bip_user_app bua ON (bua.user_id = bu.id)
			WHERE
				psychologist_id = ?
				{$where}
			GROUP BY bu.username
			";
		$query=$this->db->query($sql,array($userId));
		$result=$query->result();

		$resultnew=array();
		foreach ($result as $rkey => $rval) {

			$resultnew[$rkey] = new StdClass;

			$psychologist_id = $this->session->userdata("user_id");
			$assignedPids=$this->getAssignedProblems($psychologist_id);

			if (!empty($assignedPids)) {
				$json=json_decode($assignedPids,true);
				$problemsList= $json[$rval->id];
				 if ($filterType=='problem' && $filterId){  // only when filter by problem
					if (empty($problemsList) && $filterId!=0) continue;
					if(!in_array($filterId, $problemsList)) continue;
				 }
				$resultnew[$rkey] = new StdClass;
				if (!empty($problemsList)) {
					$pids=join(',',$problemsList);
					$resultnew[$rkey]->problem = $this->getName('bip_problem_category',$pids);
				}else{
					$resultnew[$rkey]->problem = '';
				}
			}
			$resultnew[$rkey]->id=$rval->id;
			$resultnew[$rkey]->username=$rval->username;
			$resultnew[$rkey]->first_name=$rval->first_name;
			$resultnew[$rkey]->last_name=$rval->last_name;
			$resultnew[$rkey]->difficulty=$rval->difficulty;
			$resultnew[$rkey]->created_at=$rval->created_at;
			$resultnew[$rkey]->total=$rval->total;
		}
		return $resultnew;
	}

	function getUserByUsername($username)
	{
		$query=$this->db->query("SELECT id,username,first_name,last_name,difficulty_id,psychologist_id,patient_access,patient_access_create,app_reminder_type FROM bip_user WHERE username=?",array($username));
		$row=$query->row_array();
		return $row;
	}

	function getUserByUserId($user_id)
	{
		$query=$this->db->query("SELECT id,username,first_name,last_name,difficulty_id,psychologist_id,patient_access,patient_access_create,app_reminder_type,app_web_version FROM bip_user WHERE id=?",array($user_id));
		$row=$query->row_array();
		return $row;
	}

	function getUserNameById($id)
	{
		$query=$this->db->query("SELECT username FROM bip_user WHERE id=? LIMIT 1",array($id));
		$row=$query->row_array();
		return $row['username'];
	}

	function getUserAppTrainings($data)
	{
		extract($data);
				$likeString = '%,'.$id.',%';
		$query=$this->db->query("SELECT
									id,
									task,
									completed
								FROM
									bip_tasks
								WHERE
									CONCAT(',', user_id, ',') LIKE ?",array($likeString));
		$result=$query->result();
		return $result;
	}

	function getTrainingInfo($userId,$taskId)
	{
		$query=$this->db->query("SELECT count(*) as total,min(trainingdatetime) as started_at from bip_training_app WHERE task_id=? AND user_id=? LIMIT 1",array($taskId,$userId));
		$row=$query->row();
		return $row;
	}

	function getExposures($userId,$limit){
		$query = $this->db->query("SELECT
										bta.task_id,
										bt.task,
										bta.trainingdatetime,
										bta.estimatedvalue,
										bta.estimatedvalue_end,
										bta.training_duration,
										bta.type
									FROM
										`bip_training_app` bta
									LEFT JOIN bip_tasks bt ON (bt.id = bta.task_id)
									WHERE
										bta.user_id = ?
									ORDER BY
										bta.trainingdatetime DESC
									LIMIT $limit",array($userId));
		$result=$query->result_array();
		return $result;
	}

	function getStatTrainingsByUserId($user){
		$query = $this->db->query("SELECT
									user_id,count(*) as exposures
								FROM
									`bip_training_app`
								WHERE
									user_id = ?",array($user->id));
		$result = $query->result();

		$psychologist_id=$this->session->userdata('user_id');
		$assignedPids=$this->getAssignedProblems($psychologist_id);

		$arr=array();
		foreach ($result as $k => $res) {

			$arr[$k]['user_id']=$res->user_id;

			$arr[$k]['problems']='';
			if (!empty($assignedPids)) {
				$json=json_decode($assignedPids,true);
				$problemsList= $json[$res->user_id];
				if (!empty($problemsList)) {
					$joinPlist=join(',',$problemsList);
					$arr[$k]['problems']=$this->getName('bip_problem_category',$joinPlist);
				}
			}
			$arr[$k]['name']=$user->first_name.' '.$user->last_name;
			$arr[$k]['difficulty']=$user->difficulty;
			$arr[$k]['started_app']=$user->created_at;
			$arr[$k]['exposures']=$res->exposures;
		}
		if (empty($arr)) {
			$arr[0]['user_id']=$user->id;
			$arr[0]['problems']='';
			$arr[0]['name']=$user->first_name.' '.$user->last_name;
			$arr[0]['difficulty']=$user->difficulty;
			$arr[0]['started_app']='';
			$arr[0]['exposures']=0;
		}
		return $arr;
	}


	function getTaskCompletionInfo($userId,$taskId)
	{
		$query=$this->db->query("SELECT completed from bip_tasks WHERE id=? LIMIT 1",array($taskId));
		$row=$query->row_array();
		return $row['completed'];
	}

	function getTaskPerProblem($ids)
	{
		$query = $this->db->query("SELECT id,task,user_id from bip_tasks WHERE problem_id  IN (?) AND type='admin'",array($ids));
		$result = $query->result();
		return $result;
	}

	function getAssignedProblems($psychologist_id){

		$query=$this->db->query("SELECT assigned FROM bip_problem_assign WHERE psychologist_id=? LIMIT 1",array($psychologist_id));
		$row=$query->row_array();
		return $row['assigned'];
	}

	function assignTaskToUser()
	{
		$userId=$this->input->post('userId');
		$difficulty_id=$this->input->post('diffId');
		$pids=$this->input->post('problem_id');

		$psychologist_id = $this->session->userdata("user_id");
		$query=$this->db->query("SELECT assigned FROM bip_problem_assign WHERE psychologist_id=? LIMIT 1",array($psychologist_id));
		$row=$query->row_array();
		$json=json_decode($row['assigned'],true);

		if (empty($json)) {
			$json=array($userId=>$pids);
		}else{
			$json[$userId] = $pids;
		}
		$newjson=json_encode($json);
		$this->db->query("INSERT INTO bip_problem_assign (psychologist_id,assigned) VALUES (?,?) ". "ON DUPLICATE KEY UPDATE assigned=VALUES(assigned)",array($psychologist_id,$newjson));

		$tids=$this->input->post('task_id');
		if (empty($pids) || empty($tids)) return;
		foreach ($tids as $tid) {
			$this->db->query("UPDATE bip_tasks SET user_id = CONCAT_WS(',',`user_id`, ? ) WHERE id=? AND type='admin'",array($userId,$tid));
		}
	}

	function unassignTaskToUser(){
		$userId=$this->input->post('user_id');
		$taskId=$this->input->post('task_id');

				$replaceString = ','.$userId;
		$this->db->query("UPDATE bip_tasks SET user_id=(SELECT REPLACE(`user_id`, ?, '')) WHERE type='admin' AND id=? AND FIND_IN_SET( ? ,`user_id`)>0",array($replaceString,$taskId,$userId));
	}

	//problem assigned to a user
	function getProblemsPerUser($userId)
	{
			  $likeString = '%,'.$userId.',%';
		$query = $this->db->query("SELECT problem_id from bip_tasks WHERE CONCAT(',' , user_id , ',') LIKE ? AND type='admin'",array($likeString));
		$result = $query->result_array();
		return $this->convert_to_single_array($result);
	}

	//tasks assigned to a user
	function getTaskSelected($userId)
	{
				$likeString = '%,'.$userId.',%';
		$query = $this->db->query("SELECT id from bip_tasks WHERE CONCAT(',' , user_id , ',') LIKE ? AND type='admin'",array($likeString));
		 $result = $query->result_array();
		return $this->convert_to_single_array($result);
	}

	//tasks assigned to a user
	function getTasksPerUser($userId)
	{
			$likeString = '%,'.$userId.',%';
		$query = $this->db->query("SELECT id,task from bip_tasks WHERE CONCAT(',' , user_id , ',') LIKE ? AND type='admin'",array($likeString));
		$result = $query->result();
		return $result;
	}

	function getTasksPerUserNotSet($userId,$diffId)
	{
			$likeString = '%,'.$userId.',%';
		$query = $this->db->query("SELECT id,task from bip_tasks WHERE CONCAT(',' , user_id , ',') NOT LIKE ? AND difficulty_id=? AND type='admin'",array($likeString,$diffId));
		$result = $query->result();
		return $result;
	}

	function getTasksOptionPerUserNotSet($ids,$userId,$diffId)
	{
			$likeString = '%,'.$userId.',%';
		$query = $this->db->query("SELECT id,task,user_id from bip_tasks WHERE FIND_IN_SET(?, problem_id) AND CONCAT(',' , user_id , ',') NOT LIKE ? AND FIND_IN_SET(?, difficulty_id) AND type='admin'",array($ids,$likeString,$diffId));
		$result = $query->result();
		return $result;
	}

	function getTasksOptionPerUserSet($ids,$userId,$diffId)
	{
		$query = $this->db->query("SELECT id,task,user_id from bip_tasks WHERE FIND_IN_SET(?, problem_id) AND FIND_IN_SET(?, difficulty_id) AND type='admin'",array($ids,$diffId));
		$result = $query->result();
		return $result;
	}

	function addTaskByPatient(){
		$userId=$this->input->post('userId');
		$difficulty_id=$this->input->post('diffId');
		$problemId=$this->input->post('problem_id');
		$group=$this->input->post('taskgr');

		$difficulty = $this->setting_model->getDifficultyById($difficulty_id);
		$tag = $difficulty->tag;

		if ($group=='available') { //update
			$taskId=$this->input->post('task_id');
			$this->db->query("UPDATE bip_tasks SET user_id = CONCAT_WS(',',`user_id`, ?) WHERE id=?",array($userId,$taskId));
		}else{ //insert
			$customTask=$this->input->post('custom_task');
			$this->db->query("INSERT into bip_tasks (user_id,difficulty_id,problem_id,task,type,tag) values (CONCAT_WS(',',`user_id`, ?),?,?,?,'user',?)",array($userId,$didifficulty_id,$problemId,$customTask,$tag));
		}
		if ($this->db->affected_rows()>0) {
			return true;
		}else{
			return false;
		}
	}

	function getAllAppCommentByPsy($userId,$psychologistId) {
		$query=$this->db->query("SELECT * FROM bip_app_comments WHERE user_id=? AND psychologist_id=?",array($userId,$psychologistId));
		$result = $query->result();
		return $result;
	}

	function saveAppComment()
	{
		$date = date('Y-m-d H:i:s');
		$userId=$this->input->post('user_id');
		$psychologistId=$this->input->post('psychologist_id');
		$task_id=$this->input->post('task_id');
		$usertype=$this->input->post('usertype');
		$comment = htmlspecialchars(addslashes($this->input->post("comment")));
		$comment = $this->encryption->encrypt($comment);

		$this->db->query("INSERT INTO bip_app_comments (user_id,psychologist_id,task_id,usertype,comments,status_new,posted_on) VALUES (?,?,?,?,?,'1',?)",array($userId,$psychologistId,$task_id,$usertype,$comment,$date));
		if ($this->db->affected_rows()>0) {
			return $this->db->insert_id();
		}
		return false;
	}

	function getAppCommentDetail($id)
	{
		$query = $this->db->query("SELECT * FROM bip_app_comments WHERE id=?",array($id));
		$row = $query->row();
		return $row;
	}

	function getAppUserDetail($id)
	{
		$query=$this->db->query("SELECT id,first_name,last_name,email,username FROM bip_user WHERE id=?",array($id));
		$row=$query->row();
		return $row;
	}

	function deleteAppComment()
	{
	   $messageId= $this->input->post('id');

	   $this->db->query("DELETE FROM bip_app_comments WHERE message_id=?",array($messageId));
	   $this->db->query("OPTIMIZE TABLE  bip_app_comments");

	   $this->db->query("DELETE FROM bip_message WHERE id=?",array($messageId));
	   $this->db->query("OPTIMIZE TABLE  bip_message");
	}

	function notifyMessage($id,$senderId,$receiverId,$type=false)
	{
		$task_id=$this->input->post('task_id');
		$query=$this->db->query("INSERT INTO bip_message set sender_id=?, receiver_id=?,sent_on=now(),status_receiver='0',status_sender='1', message_type=?,is_app='1',task_id=?",array($senderId,$receiverId,$type,$task_id));

		if ($this->db->affected_rows()>0) {
			$messageId = $this->db->insert_id();
			$this->db->query("UPDATE bip_app_comments SET message_id = ? WHERE id=?",array($messageId,$id));
		}
	}

	function getTotalNewAppMessage() {
		$receiver_id = $this->session->userdata("user_id");
		$sender_id = $this->session->userdata("p_id");
		if (!empty($sender_id)) {
			$query = $this->db->query("SELECT COUNT(id) AS total_new FROM bip_message WHERE status_receiver=0 AND sender_id=? AND receiver_id=? AND message_type = 2",array($sender_id,$receiver_id));
		}else{
			$query = $this->db->query("SELECT COUNT(id) AS total_new FROM bip_message WHERE status_receiver=0 AND receiver_id=? AND message_type = 2",array($receiver_id));
		}
		$result = $query->row();
		return $result->total_new;
	}

	function countUserAppMessage($sender_id) {
		$receiver_id = $this->session->userdata("user_id");
		$query = $this->db->query("SELECT COUNT(id) AS total_new FROM bip_message WHERE status_receiver=0 AND sender_id=? AND receiver_id=? AND message_type = 2",array($sender_id,$receiver_id));

		$result = $query->row();
		return $result->total_new;
	}

	function getPollingByTask($usertype,$psychologist_id,$patient_id,$taskId){

		$sender_id=($usertype=='user') ? $psychologist_id: $patient_id;
		$receiver_id=($usertype=='user') ? $patient_id: $psychologist_id;

		 $query = $this->db->query("SELECT COUNT(id) AS total_new FROM bip_message WHERE status_receiver=0 AND sender_id=? AND receiver_id=? AND task_id=? AND message_type = 2",array($sender_id,$receiver_id,$taskId));
		$result = $query->row();
		return $result->total_new;
	}


	function countComments($usertype,$psychologist_id,$user_id,$task_id,$new_status){

		 $query = $this->db->query("SELECT COUNT(id) AS total_new FROM bip_app_comments WHERE usertype!=? AND status_new=? AND user_id=? AND psychologist_id=? AND task_id=?",array($usertype,$new_status,$user_id,$psychologist_id,$task_id));
		$result = $query->row();
		return $result->total_new;
	}

	function countNewCommentForUser($user_id){
		$psychologist_id = $this->session->userdata("user_id");
		$query = $this->db->query("SELECT COUNT(id) AS total_new FROM bip_app_comments WHERE usertype='user' AND status_new='1' AND user_id=? AND psychologist_id=?",array($user_id,$psychologist_id));
		$result = $query->row();
		return $result->total_new;
	}


	function toggleTaskStatus()
	{
		$status = $this->input->post('status'); //reopen or closed
		$userId = $this->input->post('user_id');
		$taskId = $this->input->post('task_id');

		$query=$this->db->query("SELECT completed FROM bip_tasks WHERE id=? LIMIT 1",array($taskId));
		$row=$query->row_array();
		$json=json_decode($row['completed'],true);

		if ($status=='closed') { // add json key value
			if (empty($json)) {
				$json=array($userId=>date('Y-m-d'));
			}else{
				$json[$userId] = date('Y-m-d');
			}
		}else{ //remove json key value
			unset($json[$userId]);
		}
		$newjson=json_encode($json);
		$this->db->query("UPDATE bip_tasks SET completed=? WHERE id=?",array($newjson,$taskId));

		if ($status=='reopen') {
			$this->db->query("UPDATE bip_training_app SET updated_at=now() WHERE task_id=?",array($taskId));
		}
	}

	function markAppCommentRead($usertype,$psychologist_id,$patient_id,$task_id)
	{
		$sender_id=($usertype=='user') ? $psychologist_id: $patient_id;
		$receiver_id=($usertype=='user') ? $patient_id: $psychologist_id;
		$this->db->query("UPDATE bip_message SET status_receiver = 1,read_on = now() WHERE sender_id=? AND receiver_id=? AND status_receiver=0 AND is_app='1' AND task_id=?",array($sender_id,$receiver_id,$task_id));
		if ($this->db->affected_rows()>0) {
			$this->db->query("UPDATE bip_app_comments SET status_new=0 WHERE psychologist_id=? and user_id=? and status_new=1 AND task_id=? and usertype!=?",array($psychologist_id,$patient_id,$task_id,$usertype));
			$total_app_message = $this->getTotalNewAppMessage();
			$this->session->set_userdata('total_app_message_temp', $total_app_message);
		}
	}

	function toggleTaskComment($psychologist_id,$patient_id,$task_id,$usertype)
	{
		$query=$this->db->query("SELECT * FROM bip_app_comments WHERE user_id=? AND psychologist_id=? AND task_id=?",array($patient_id,$psychologist_id,$task_id));
		$result = $query->result();

		$this->markAppCommentRead($usertype,$psychologist_id,$patient_id,$task_id);

		return $result;
	}

	function getGraphInput(){
		$task_id = $this->input->post('task_id');
		$user_id = $this->input->post('user_id');

		$query=$this->db->query("SELECT tokenkey,deviceId,user_id FROM bip_user_app WHERE user_id=? ORDER BY id DESC LIMIT 1",array($user_id));
		$row=$query->row_array();
		$response=array(
			'userid'=>$row['user_id'],
			'tokenkey'=>$row['tokenkey'],
			'deviceId'=>$row['deviceId'],
			'taskid'=>$task_id,
			'lastupdatedate'=>''
			);
		return $response;
	}

	//push notification
	function hasExactOnePractice()
	{
		$query= $this->db->query("SELECT
									count(bta.trainingdatetime) AS one_practice,
									bta.user_id,
									bu.difficulty_id,
									bna.feedback_status,
									bna.feedback_message,
									bna.feedback_xdays,
									DATEDIFF(
										CURDATE(),
										STR_TO_DATE(
											max(bta.trainingdatetime),
											'%Y-%m-%d'
										)
									) AS DAYS
								FROM
									`bip_training_app` bta
								LEFT JOIN bip_user bu ON (bu.id = bta.user_id)
								LEFT JOIN bip_notify_app bna ON (
									bna.difficulty_id = bu.difficulty_id
								)
								GROUP BY
									bta.user_id
								HAVING
									one_practice = 1");
		$result=$query->result();
		return $result;
	}

	function hasAnyActivePractice(){
		$query= $this->db->query("SELECT
									bta.user_id,
									bu.difficulty_id,
									bna.reminder1_status,
									bna.reminder1_message,
									bna.reminder1_xdays,
									max(bta.trainingdatetime) AS last_practice_date,
									DATEDIFF(
										CURDATE(),
										STR_TO_DATE(
											max(bta.trainingdatetime),
											'%Y-%m-%d'
										)
									) AS DAYS
								FROM
									bip_training_app bta
								LEFT JOIN bip_user bu ON (bu.id = bta.user_id)
								LEFT JOIN bip_notify_app bna ON (
									bna.difficulty_id = bu.difficulty_id
								)
								WHERE
									bta.trainingdatetime <= CURRENT_DATE
								GROUP BY
									bta.user_id");
		$result=$query->result();
		return $result;
	}

	function hasAnyActivePractice2(){
		$query= $this->db->query("SELECT
									bta.user_id,
									bu.difficulty_id,
									bna.reminder2_status,
									bna.reminder2_message,
									bna.reminder2_xdays,
									max(bta.trainingdatetime) AS last_practice_date,
									DATEDIFF(
										CURDATE(),
										STR_TO_DATE(
											max(bta.trainingdatetime),
											'%Y-%m-%d'
										)
									) AS DAYS
								FROM
									bip_training_app bta
								LEFT JOIN bip_user bu ON (bu.id = bta.user_id)
								LEFT JOIN bip_notify_app bna ON (
									bna.difficulty_id = bu.difficulty_id
								)
								WHERE
									bta.trainingdatetime <= CURRENT_DATE
								GROUP BY
									bta.user_id");
		$result=$query->result();
		return $result;
	}

	function getDefaultNotifcation()
	{
	   $query = $this->db->query("SELECT * FROM bip_notify_app WHERE difficulty_id='0'");
	   $row=$query->row();
	   return $row;
	}

	function getDefaultTreatmentId()
	{
	   $query = $this->db->query("SELECT id FROM bip_treatment_app WHERE difficulty_id='0'");
	   $row=$query->row();
	   return $row->id;
	}

	function getDeviceTokensPerUser($userId)
	{
		$query=$this->db->query("SELECT GROUP_CONCAT(UrbanAirshipId SEPARATOR ',') as tokenKeyArr,devicetype,user_id FROM `bip_user_app` WHERE user_id=? GROUP BY devicetype",array($userId));

		$result = $query->result();
		return $result;
	}

	function createPatientSpecificTask() {
		$difficulty_id = $this->input->post('difficulty_id');
		$problem_id = $this->input->post('problem_id');

		$tag = $this->input->post('tag');

		$task = addslashes(htmlspecialchars($this->input->post('new_task')));
		$user_id = $this->input->post('user_id');

				$userString = ','.$user_id;

		$query=$this->db->query("INSERT INTO bip_tasks (user_id,difficulty_id,problem_id,task,type,tag) VALUES (?,?,?,?,'psychologist',?)",array($user_id,$difficulty_id,$problem_id,$task,$tag));
	}

	function getPushReminderData()
	{
	   $query=$this->db->query("SELECT * FROM bip_push_reminder WHERE difficulty_id='0' LIMIT 1");
	   $row=$query->row();
	   return $row;
	}

	function savePushReminder(){

		$count = $this->input->post('frm_count');
		$difficulty_id = $this->input->post('difficulty');
		$inputs = array();
		for ($i=0; $i <=$count; $i++) {
			$pscnt = utf8_decode($this->input->post('p_scnt_'.$i));
			if (!empty($pscnt)) {
				$inputs[] = $this->input->post('p_time_'.$i).'~~~'.$this->input->post('p_scnt_'.$i);
			}
		}
		$app_reminder = $this->my_json_encode($inputs);
		$this->db->query("INSERT INTO bip_push_reminder (difficulty_id,app_reminder) VALUES (?,?) ". "ON DUPLICATE KEY UPDATE app_reminder=VALUES(app_reminder)",array($difficulty_id,$app_reminder));

	}

	function changePushReminder()
	{
		$count = $this->input->post('frm_count');

		$difficulty_id = $this->input->post('diffId');
		$userId = $this->input->post('userId');
		$app_reminder_type = $this->input->post('app_reminder_type');

		$inputs = array();

		for ($i=0; $i <=$count; $i++) {
			$pscnt = utf8_decode($this->input->post('p_scnt_'.$i));
			if (!empty($pscnt)) {
				$inputs[] = $this->input->post('p_time_'.$i).'~~~'.$this->input->post('p_scnt_'.$i);
			}
		}

		$app_reminder = $this->my_json_encode($inputs);

		if ($app_reminder_type) {
			$this->db->query("UPDATE bip_user SET app_reminder=?,app_reminder_type=? WHERE id=? AND difficulty_id=?",array($app_reminder,$app_reminder_type,$userId,$difficulty_id));
		}else{
			$this->db->query("UPDATE bip_user SET app_reminder_type=? WHERE id=? AND difficulty_id=?",array($app_reminder_type,$userId,$difficulty_id));
		}
	}

	function getReminderByDifficultyPerUser(){

		$difficulty_id = $this->input->post('diffId');
		$userId = $this->input->post('userId');

		$app_reminder_type = $this->input->post('app_reminder_type');

		if ($app_reminder_type) {
			$query= $this->db->query("SELECT app_reminder FROM bip_user WHERE difficulty_id=? AND id=? LIMIT 1",array($difficulty_id,$userId));
			$row = $query->row_array();
		}else{
			$diffQry = $this->db->query("SELECT app_reminder FROM bip_push_reminder WHERE difficulty_id=?",array($difficulty_id));
			$defaultQry = $this->db->query("SELECT app_reminder FROM bip_push_reminder WHERE difficulty_id=0");

			$row = $diffQry->row_array();
			if (empty($row['app_reminder']) || $row['app_reminder']=='[]') {
				$row = $defaultQry->row_array();
			}
		}

		return $row;

	}

	function getRegisteredUsers()
	{
		$query= $this->db->query("SELECT
							  bu.id AS user_id,
							  bu.username,
							  bua.UrbanAirshipId AS installationId,
							  bua.tokenkey,
							  bua.devicetype,
							  bu.notification_enabled,
							  bd.tag,
							  bu.difficulty_id,
							  bu.app_reminder,
							  bu.app_reminder_type
							FROM
							  bip_user bu
							  INNER JOIN bip_user_app bua
								ON (bua.user_id = bu.id)
							  INNER JOIN bip_difficulty bd
								ON (bd.id = bu.difficulty_id)
							 where bu.notification_enabled = 1");

		$result=$query->result();
		return $result;
	}

	function getUserReminder($difficulty_id){

		$query = $this->db->query("SELECT app_reminder FROM bip_push_reminder WHERE difficulty_id=?",array($difficulty_id));
		 $row = $query->row();
		if($query->num_rows()<1 || $row->app_reminder=='[]' || empty($row->app_reminder)){
			 $newQry = $this->db->query("SELECT app_reminder FROM bip_push_reminder WHERE difficulty_id=0");
			 $row= $newQry->row();
		}

		return $row->app_reminder;
	}

	function setCustomMessage(){

		$difficulty_id = 0;

		$frm_name = $this->input->post('frm_name');
		$extra = array(
			'cancel_message' => $this->input->post('cancel_message')
			);
		$extraJson = json_encode($extra);

		$this->db->query("INSERT INTO bip_treatment_app (difficulty_id) VALUES (?) ". "ON DUPLICATE KEY UPDATE extra=?",array($difficulty_id,$extraJson));

	}

	function getCustomMessage(){
		$query = $this->db->query("SELECT extra FROM bip_treatment_app WHERE difficulty_id = 0");
		$row = $query ->row();

		return json_decode($row->extra);

	}


	/**
	 * Method to fetch all the registration
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   24th March 2015
	 * @param  integer $offset     [description]
	 * @param  integer $datalimit  [description]
	 * @param  string  $orderBy    [description]
	 * @param  integer $diffId     [description]
	 * @param  integer $filterId   [description]
	 * @param  string  $filterType [description]
	 * @return Object              The Recordset
	 */

	function getAllRegistrationTasks($offset=0,$datalimit=50,$orderBy='desc',$diffId=0,$filterId=0,$filterType='treatment'){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;
		$query = $this->db->query("call getAllRegistrationTasks('$offset','$datalimit','$orderBy','$diffId','$filterId','$filterType',@a)");
		$result = $query->result();

		$this->db->freeDBResource();
		return $result;
	}

	function totalRegistrationTasksRows($difficulty_id=""){
		if (!empty($difficulty_id)) {
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_task WHERE FIND_IN_SET(?, difficulty_id)",$difficulty_id);
		}else{
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_task");
		}

		$row=$query->row();
	   return $row->totalrow;
	}


	function changeRegistrationStatus(){
		$registration_id = $this->input->post("registration_id");
		$newstatus = $this->input->post("new_status");
		$current_date = date("Y-m-d H:i:s");

		if($registration_id>0){
			$this->db->query("UPDATE bip_registration_task SET registration_status=? WHERE registration_id=?",array($newstatus,$registration_id));
		}
		$newicon = $newstatus==0?"wrong.png":"enabled.gif";
		$retarry["icon_path"] = urlencode(base_url()."images/admin_icons/".$newicon);
		$writestatus = $newstatus==1?lang("inactive"):lang("active");
		$retarry["tooltip"] = lang("toggle_status")." ".$writestatus;
		echo json_encode($retarry);
		exit;
	}

	/**
	 * Method to save Registration task
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   24th March 2015
	 */
	function addRegistrationTask() {
		$difficulty_id = join(',',$this->input->post('difficulty_id'));
		$registration_name = htmlspecialchars($this->input->post('registration_name'));
		$flow_type = addslashes(htmlspecialchars($this->input->post('flow_type')));
		$bar_color = $this->input->post("bar_color");
		$current_date = date("Y-m-d H:i:s");
		$registrationID=$this->input->post('registration_id');


		if ($registrationID) {
			//chk if duplicate exists
			$chk = $this->db->query("SELECT count(*) as totalRecs FROM bip_registration_task WHERE registration_id!=? AND registration_name=? AND registration_status='1'",array($registrationID,$registration_name))->row();

			if($chk->totalRecs>0){
				$res["error_code"] = "duplicate";
				$res["error_msg"] = lang("txt_item_exist");
			}else{
				$res["error_code"] = "OK";
				$res["error_msg"] = "";

				$this->db->query("UPDATE bip_registration_task set
					difficulty_id=?,
					registration_name=?,
					flow_type=?,
					bar_color=?
					WHERE
					registration_id=?",array($difficulty_id,$registration_name,$flow_type,$bar_color,$registrationID));
			}
		}else{

			$chk = $this->db->query("SELECT count(*) as totalRecs FROM bip_registration_task WHERE registration_name=? AND FIND_IN_SET(difficulty_id,?) AND registration_status='1'",array($registration_name,$difficulty_id))->row();

			$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_task WHERE FIND_IN_SET(difficulty_id,?)",array($difficulty_id))->row();

			$neworder =  $getmax->max_sort_order+1;

			if($chk->totalRecs>0){
				$res["error_code"] = "duplicate";
				$res["error_msg"] = lang("txt_item_exist");
			}else{
				$res["error_code"] = "OK";
				$res["error_msg"] = "";
				$this->db->query("INSERT INTO bip_registration_task (difficulty_id,registration_name,flow_type,added_date,sort_order,bar_color)
					VALUES (?,?,?,?,?,?)",array($difficulty_id,$registration_name,$flow_type,$current_date,$neworder,$bar_color));
			}
		}
		$this->db->freeDBResource();
		echo json_encode($res);
		exit;
	}

	/**
	 * method to get registration by id
	 * @return [type] [description]
	 */
	function getRegistrationById() {
		$registration_id = $this->input->post('registration_id');
		$query = $this->db->query("call getRegistrationById($registration_id)");
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	/**
	 * method to get flows by registration id
	 * @return [type] [description]
	 */
	function getFlowStuffsByRegId() {
		$registration_id = $this->input->post('registration_id');
		$query = $this->db->query("call getFlowStuffsByRegId($registration_id)");
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	/**
	 * Method to check how many flows and/or steps are linked with registration.
	 * @return array Array of flows count and steps count
	 */
	function checkFlowsStepsOnTask(){
	   // $array = array("flows_count"=>4, "steps_count"=>34);
		$registration_id = $this->input->post('registration_id');
		$queryFlows=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_flows WHERE registration_id=?",array($registration_id));
		$querySteps=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_steps WHERE registration_id=?",array($registration_id));
		$array = array("flows_count"=>$queryFlows->row()->totalrow, "steps_count"=>$querySteps->row()->totalrow);
		return $array;
	}

	/**
	 * Method to fetch all the Flows
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   24th March 2015
	 * @param  integer $offset          [description]
	 * @param  integer $datalimit       [description]
	 * @param  string  $orderBy         [description]
	 * @param  integer $registration_id [description]
	 * @return object                   [Recordset]
	 */
	function getAllRegistrationFlows($offset=0,$datalimit=50,$orderBy='desc',$registration_id=0){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;
		$query = $this->db->query("call getAllRegistrationFlows('$offset','$datalimit','$orderBy','$registration_id',@a)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function totalRegistrationFlowsRows($registration_id=""){
		$query=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_flows WHERE registration_id=?",array($registration_id));
		$row=$query->row();
		return $row->totalrow;
	}

	/**
	 * Adds registration flow to database
	 */
	function addRegistrationFlow() {
		$flow_page_title = addslashes(htmlspecialchars($this->input->post('flowpage_title')));
		$flow_name = addslashes(htmlspecialchars($this->input->post('flow_name')));
		$flow_page_id = $this->input->post("flow_page_id");
		$current_date = date("Y-m-d H:i:s");
		$registrationID=$this->input->post('registration_id');
		$fp_id="";
		if($flow_page_id>0){
			$this->db->query("UPDATE bip_registration_flow_page SET flow_page_title=? WHERE flow_page_id=? AND registration_id=?",array($flow_page_title,$flow_page_id,$registrationID));
			$fp_id = $flow_page_id;
		}else{
			$this->db->query("INSERT INTO bip_registration_flow_page SET flow_page_title=?, added_date=?, registration_id=?",array($flow_page_title,$current_date,$registrationID));

			if ($this->db->affected_rows()>0) {
				$fp_id = $this->db->insert_id();
			}
		}

		//now insert flows
		if(trim($flow_name)!="" && $fp_id>0 && $registrationID>0){
			$this->db->query("INSERT INTO bip_registration_flows SET flow_page_id=?, registration_id=?,flow_name=?,added_date=?",array($fp_id,$registrationID,$flow_name,$current_date));
		}

		$this->db->freeDBResource();
	}

	function changeFlowStatus(){
		$flowid = $this->input->post("flow_id");
		$newstatus = $this->input->post("new_status");
		$current_date = date("Y-m-d H:i:s");

		if($flowid>0){
			$this->db->query("UPDATE bip_registration_flows SET flow_status=? WHERE flow_id=?",array($newstatus,$flowid));
		}
		$newicon = $newstatus==0?"wrong.png":"enabled.gif";

		$retarry["icon_path"] = urlencode(base_url()."images/admin_icons/".$newicon);
		$writestatus = $newstatus==1?lang("inactive"):lang("active");
		$retarry["tooltip"] = lang("toggle_status")." ".$writestatus;
		echo json_encode($retarry);
		exit;
	}

	function changeRegStepStatus(){
		$stepid = $this->input->post("step_id");
		$newstatus = $this->input->post("new_status");
		$current_date = date("Y-m-d H:i:s");

		if($stepid>0){
			$this->db->query("UPDATE bip_registration_steps SET step_status=? WHERE step_id=?",array($newstatus,$stepid));
		}
		$newicon = $newstatus==0?"wrong.png":"enabled.gif";
		$retarry["icon_path"] = urlencode(base_url()."images/admin_icons/".$newicon);
		$writestatus = $newstatus==1?lang("inactive"):lang("active");
		$retarry["tooltip"] = lang("toggle_status")." ".$writestatus;
		echo json_encode($retarry);
		exit;
	}

	function editFlow(){
		$new_flow_name = addslashes(htmlspecialchars($this->input->post('new_flow_name')));
		$flow_id = $this->input->post("flow_id");
		$current_date = date("Y-m-d H:i:s");
		if(trim($new_flow_name)!="" && $flow_id>0){
			$this->db->query("UPDATE bip_registration_flows SET flow_name=? WHERE flow_id=? ",array($new_flow_name,$flow_id));
			echo "success";
		}
	}


	function countSteps($registration_id,$flow_id=0){
		if($flow_id>0){
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_steps WHERE registration_id=? AND flow_id=?",array($registration_id,$flow_id));
		}else{
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_steps WHERE registration_id=?",array($registration_id));
		}
		$row=$query->row();
		return $row->totalrow;
	}

	function getNameById($table,$field,$lookup_field,$value){
		$query = $this->db->query("SELECT $field as return_field FROM $table WHERE $lookup_field='$value'");
		$row=$query->row();
		return $row->return_field;
	}

	function getCreatedNameById($table,$lookup_field,$value){
		$query = $this->db->query("SELECT id,first_name,last_name FROM $table WHERE $lookup_field='$value'");
		$row=$query->row();
		return $row;
	}


	 /**
	 * Method to fetch all the steps
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   25th March 2015
	 * @param  integer $offset          [description]
	 * @param  integer $datalimit       [description]
	 * @param  string  $orderBy         [description]
	 * @param  integer $registration_id [description]
	 * @param  integer $flow_id         [description]
	 * @return object                   [Recordset]
	 */
	function getAllRegistrationSteps($offset=0,$datalimit=50,$orderBy='desc',$registration_id=0,$flow_id=0){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;
		$query = $this->db->query("call getAllRegistrationSteps('$offset','$datalimit','$orderBy','$registration_id','$flow_id',@a)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function totalRegistrationStepRows($registration_id="",$flow_id=""){
		if($flow_id>0){
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_steps WHERE registration_id=? AND step_status='1' AND flow_id=?",array($registration_id,$flow_id));
		}else{
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_steps WHERE registration_id=? AND step_status='1'",array($registration_id));
		}
		$row=$query->row();
		return $row->totalrow;
	}

	function getAllActiveTemplates(){
		$query = $this->db->query("SELECT * FROM bip_registration_templates WHERE template_status='1'");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getTemplateSpecificStuffs(){
		$registration_id = $this->input->post("registration_id");
		$flow_id = $this->input->post("flow_id");
		$template = $this->input->post("template");
		$step_id = $this->input->post("step_id");

		$array = array();
		if($step_id>0){
			$query = $this->db->query("SELECT * FROM bip_registration_steps WHERE step_id=? AND step_status='1'",array($step_id));
			$array["steps"] = $query->result();
			$this->db->freeDBResource();
			unset($query);

			if($template=="steps_expand_collapse"){ //then fetch answer categories
			   // $query= $this->db->query("call getAnswerCatByStepID($step_id)");
				$query= $this->db->query("SELECT * FROM bip_registration_answer_category WHERE step_id = ? AND answer_cat_status='1' AND added_by='admin' ORDER BY sort_order ASC", array($step_id));
				$array["answer_categories"] = $query->result();
				$this->db->freeDBResource();
				unset($query);
			}
			//now get answers
			  $query= $this->db->query("SELECT * FROM bip_registration_answers WHERE step_id = ? AND added_by='admin' AND answer_status='1' ORDER BY sort_order ASC", array($step_id));
			  $array["answers"] = $query->result();
			  $this->db->freeDBResource();
			  unset($query);
		}
		return $array;
	}

	function getAnswersByAnswerCat($step_id,$cat_id){
		$query = $this->db->query("SELECT * FROM bip_registration_answers WHERE step_id=? AND answer_cat_id=? AND added_by='admin' AND answer_status='1' ORDER BY sort_order ASC",array($step_id,$cat_id));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getTemplateName($templateCode){

	   $query=$this->db->query("SELECT template_desc FROM bip_registration_templates WHERE registration_id=?",array($registration_id));

		$row=$query->row();
		return $row->totalrow;
	}

	function saveRegistrationSteps(){
		$current_date = date("Y-m-d H:i:s");
		extract($this->input->post());
		if(!$registration_id>0){
			echo "No registration selected";
			exit;
		}

	   // echo "<pre>".print_r($this->input->post(),true)."</pre>"; exit;
	   /* $difficulty_ids = $this->getDifficultyIDbyRegID($registration_id);
		$patients = $this->getAllusersByDifficultyID($difficulty_ids);*/

		$special_case = isset($special_case) ? $special_case : 0;

		$show_date = isset($show_date) ? $show_date : 0;
		$show_time = isset($show_time) ? $show_time : 0;
		$time_format = isset($time_format) ? $time_format : 1;
		$is_multiple_choice = isset($is_multiple_choice) ? $is_multiple_choice : 0;
		$max_selection_allowed = isset($max_selection_allowed) ? $max_selection_allowed : 0;
		$answer_text = isset($answer_text) ? $answer_text : "";
		$button_text = isset($button_text) ? $button_text : "";
		$allow_to_add_answers = isset($allow_to_add_answers) ? $allow_to_add_answers : 0;
		$allow_to_edit_list = isset($allow_to_edit_list) ? $allow_to_edit_list : 0;
		$allow_to_add_answer_category = isset($allow_to_add_answer_category) ? $allow_to_add_answer_category : 0;
		$step_title = addslashes(htmlspecialchars($step_title));
		$step_subheading = addslashes(htmlspecialchars($step_subheading));

		if($template_name=="steps_text"){
			$answer_text = str_replace("http:","",$answer_text);
			$answer_text = str_replace("https:","",$answer_text);
			$answer_text = str_replace("//","http://",$answer_text);
		}

	   $editMode = false;

		if($step_id>0)
		{
			 $arrayData = array($step_title,$step_subheading,$registration_id,$flow_id,$is_multiple_choice,$max_selection_allowed,$template_name,
				$show_date,$show_time,$time_format,$answer_text,$button_text,$allow_to_add_answers,$allow_to_edit_list,$allow_to_add_answer_category,
				$special_case,1,$step_id);
			$this->db->query("UPDATE bip_registration_steps SET
						step_name                       = ?,
						step_subheading                 = ?,
						registration_id                 = ?,
						flow_id                         = ?,
						is_multiple_choice              = ?,
						max_selection_allowed           = ?,
						template                        = ?,
						show_date                       = ?,
						show_time                       = ?,
						time_format                     = ?,
						answer_text                     = ?,
						button_text                     = ?,
						allow_custom_answer             = ?,
						allow_edit                      = ?,
						allow_to_add_answer_category    = ?,
						special_case                    = ?,
						step_status                     = ? WHERE step_id = ?
						",$arrayData);

			$step_id_altered = $step_id;
			$editMode = true;
		}else{
			 $arrayData = array($step_title,$step_subheading,$registration_id,$flow_id,$is_multiple_choice,$max_selection_allowed,$template_name,
				$show_date,$show_time,$time_format,$answer_text,$button_text,$allow_to_add_answers,$allow_to_edit_list,$allow_to_add_answer_category,
				$current_date,$special_case,1);

			$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_steps WHERE registration_id= ? AND flow_id=?",array($registration_id,$flow_id))->row();

			$neworder =  $getmax->max_sort_order+1;
			$this->db->query("INSERT INTO bip_registration_steps SET
						step_name                       = ?,
						step_subheading                 = ?,
						registration_id                 = ?,
						flow_id                         = ?,
						is_multiple_choice              = ?,
						max_selection_allowed           = ?,
						template                        = ?,
						show_date                       = ?,
						show_time                       = ?,
						time_format                     = ?,
						answer_text                     = ?,
						button_text                     = ?,
						allow_custom_answer             = ?,
						allow_edit                      = ?,
						allow_to_add_answer_category    = ?,
						added_date                      = ?,
						special_case                    = ?,
						step_status                     = ?,
						sort_order                      = $neworder
						",$arrayData);
			$step_id_altered =  $this->db->insert_id();
			$editMode = false;
		}


		if ($this->db->affected_rows()>0 || $editMode==true) {
			//save answer category if there is any
			if(count($answers_category)>0){
				for($c = 0; $c<count($answers_category); $c++){
					if($cat_id[$c]>0){ //update
						 $this->db->query("UPDATE bip_registration_answer_category SET
							   answer_cat_name     = ?,
							   step_id             = ?,
							   answer_cat_status   = ?,
							   sort_order          = ?
							   WHERE answer_cat_id = ?
							", array($answers_category[$c],$step_id_altered,1,$answer_cat_order[$c],$cat_id[$c]));
					}
				}
			}
			//now save answers if there is any
			if(count($answers)>0){
				for($k = 0; $k<count($answers); $k++){

					if($answer_id[$k]==0){ //insert
						$this->db->query("INSERT INTO bip_registration_answers SET
							   answer          = ?,
							   step_id         = ?,
							   answer_cat_id   = ?,
							   added_date      = ?,
							   answer_status   = ?,
							   sort_order      = ?,
							   answer_type     = 'standard',
							   created_by      = ?,
							   added_by        = 'admin',
							   belongs_to      ='0'
							",array($answers[$k],$step_id_altered,$answer_cat_id[$k],$current_date,1,$answer_order[$k], $this->session->userdata("user_id")));

						$insert_id = $this->db->insert_id();
						//now save to all patients table.


					}else{ //update
						$this->db->query("UPDATE bip_registration_answers SET
							   answer          = ?,
							   step_id         = ?,
							   answer_cat_id   = ?,
							   answer_status   = ?,
							   sort_order      = ?
								WHERE answer_id = ?
							",array($answers[$k],$step_id_altered,$answer_cat_id[$k],1,$answer_order[$k],$answer_id[$k]));
					}
				}
			}
			echo  "success";
		}else{
			echo "Error in saving records";
		}
		exit;
	}

	function removeStepAnswer(){
		$this->db->query("UPDATE bip_registration_answers SET answer_status=? WHERE answer_id =?",array($this->input->post("dowhat"),$this->input->post("answer_id")));
		if($this->db->affected_rows()>0){
			echo "success";
		}else{
			echo "failed";
		}
	}

	function removeStepAnswerCat(){
		$cat_id = $this->input->post("cat_id");
		$this->db->query("UPDATE bip_registration_answer_category SET answer_cat_status=? WHERE answer_cat_id =?",array($this->input->post("dowhat"),$cat_id));
		if($this->db->affected_rows()>0){
			$this->db->query("UPDATE  bip_registration_answers SET answer_status=? WHERE answer_cat_id=?",array($this->input->post("dowhat"),$cat_id));
			echo "success";
		}else{
			echo "failed";
		}
	}


	function sortRegistrationSteps(){
	   extract($this->input->post());
	   for($k = 0; $k<count($ID); $k++){
			$this->db->query("UPDATE bip_registration_steps SET sort_order='".($k+1)."' WHERE step_id=? AND registration_id=?",array($ID[$k],$registration_id));
	   }
	   echo "success";
	   exit;
	}

	function sortRegistrationTasks(){
	   extract($this->input->post());
	   for($k = 0; $k<count($ID); $k++){
			$od = $offset+$k+1;
			$this->db->query("UPDATE bip_registration_task SET sort_order='".$od."' WHERE registration_id=?",array($ID[$k]));
	   }
	   echo "success";
	   exit;
	}

	function saveAnswerCategory(){
		$current_date = date("Y-m-d H:i:s");
		$array = array();
		extract($this->input->post());
	   /* $difficulty_ids = $this->getDifficultyIDbyRegID($registration_id);
		$patients = $this->getAllusersByDifficultyID($difficulty_ids);*/


		if(!$step_id>0){ //insert steps as well
			$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_steps WHERE registration_id= ?",array($registration_id))->row();
			$neworder =  $getmax->max_sort_order+1;
			$array["sort_order"] = $neworder;
			$array["new_step"]= 1;
			$step_title = addslashes(htmlspecialchars($step_title));
			$step_subheading = addslashes(htmlspecialchars($step_subheading));

			$this->db->query("INSERT INTO bip_registration_steps SET step_name=?, step_subheading=?,registration_id=?,flow_id=?,template=?,added_date='$current_date',sort_order='$neworder'",array($step_title,$step_subheading,$registration_id,$flow_id,$template_name));
			$insert_id = $this->db->insert_id();

			if($insert_id>0){ //now save category
				$step_answer_cat = addslashes(htmlspecialchars($step_answer_cat));
				$this->db->query("INSERT INTO bip_registration_answer_category SET answer_cat_name=?, step_id='$insert_id',added_date='$current_date',sort_order='1',answer_type='standard',created_by=?, added_by='admin', belongs_to='0'",array($step_answer_cat, $this->session->userdata("user_id")));
				$array["cat_id"] = $this->db->insert_id();
				$array["cat_sort_order"]=1;

			}
		}else{
			$insert_id = $step_id;
			$getmaxCat = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_answer_category WHERE step_id= ?",array($insert_id))->row();
			$new_cat_order =  $getmaxCat->max_sort_order+1;

			$step_answer_cat = addslashes(htmlspecialchars($step_answer_cat));
			$this->db->query("INSERT INTO bip_registration_answer_category SET answer_cat_status='1',answer_cat_name=?, step_id=?,added_date='$current_date',sort_order='$new_cat_order',answer_type='standard',created_by=?, added_by='admin', belongs_to='0'",array($step_answer_cat,$insert_id,$this->session->userdata("user_id")));
			$array["cat_id"] = $this->db->insert_id();
			$array["new_step"]= 0;
			$array["cat_sort_order"] = $new_cat_order;
		}
		$array["step_id"] = $insert_id;
		$array["registration_id"] = $registration_id;
		$array["template"] = $template_name;
		$array["template_name"] = lang($template_name);
		$array["flow_id"] = $flow_id;
		echo json_encode($array);
		exit;
	}

	function getRegistrationByDifficultyID($difficulty_id){
		$query = $this->db->query("CALL getRegistrationByDifficultyID($difficulty_id)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getDifficultyIDbyRegID($regID){
		$query = $this->db->query("SELECT difficulty_id FROM bip_registration_task WHERE registration_id=?",array($regID));
		$row = $query->row();
		return $row->difficulty_id;
	}

	function getAllusersByDifficultyID($difficulty_ids){
		$chk = explode(",",$difficulty_ids);
		$count = count($chk);
		$criteria = sprintf("?%s", str_repeat(",?", ($count ? $count-1 : 0)));
		$sql = sprintf("SELECT id FROM bip_user WHERE psychologist_id>0 AND difficulty_id IN(%s)",$criteria);
		$query = $this->db->query($sql,$chk);
		/*echo $this->db->last_query();
		exit;*/
		$result = $query->result();
		return $result;
	}

	function fetchStandardAnswers(){
		$query = $this->db->query("CALL fetchStandardAnswers()");
		$this->db->freeDBResource();
		$result = $query->result();
		return $result;
	}

	function saveStandardAnswer(){
		$answer = trim($this->input->post("answer"));
		$query = $this->db->query("SELECT count(*) as totalrow FROM bip_registration_standard_options WHERE option_name=?",array($answer));
		$row = $query->row();
		if($row->totalrow==0){
			$this->db->query("INSERT INTO bip_registration_standard_options SET option_name=?,added_date=?",array($answer,date("Y-m-d H:i:s")));
			$array["id"] = $this->db->insert_id();
			$array["answer"] = $answer;
		}else{
			$array["id"] = 0;
			$array["answer"] = "";
		}
		echo json_encode($array);
		exit;
	}

	function getCustomAnswersToMap($offset=0,$datalimit=50,$orderBy='desc',$keyword='',$criteria=1,$map_what=1){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;

		$query = $this->db->query("call getCustomAnswersToMap('$offset','$datalimit','$orderBy','$keyword',$criteria,$map_what,@a)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function totalCustomAnswersToMap($keywords){
		$query=$this->db->query("SELECT count(*) as totalrow FROM bip_registration_answers WHERE answer_type='standard' AND MATCH(answer) AGAINST(? IN NATURAL LANGUAGE MODE)",array($keywords));
		$row=$query->row();
		return $row->totalrow;
	}

	function getRegistrationFlowDetailsByStepID($stepID){
		$query = $this->db->query("SELECT st.step_name,reg.registration_name,fl.flow_name FROM bip_registration_steps st
LEFT JOIN bip_registration_task reg ON reg.registration_id = st.registration_id
LEFT JOIN bip_registration_flows fl ON fl.flow_id = st.flow_id
WHERE st.step_id = ?",array($stepID));
		return  $query->row();
	}

	function mapSelectedAnswers(){
		//echo "<pre>".print_r($this->input->post(),true)."</pre>";
		$standard_answer = $this->input->post("standard_answers");
		$custom_answers = $this->input->post("custom_answer_id");
		$map_what = $this->input->post("map_what");
		for($k=0; $k<count($custom_answers); $k++){
			$answer_id = $custom_answers[$k];
			if($map_what==1){
				$this->db->query("UPDATE bip_registration_answers SET mapped_answer_id=? WHERE answer_id=? AND answer_type=='custom'",array($standard_answer,$answer_id));
			}else{
				$this->db->query("UPDATE bip_registration_answer_category SET mapped_cat_id=? WHERE answer_cat_id=? AND answer_type='custom'",array($standard_answer,$answer_id));
			}
		}
		echo lang("msg_map_successful");
		exit;
	}

	function patientInfo($userid){
		$query = $this->db->query("SELECT CONCAT_WS(' ',first_name,last_name) as patient_name FROM bip_user WHERE id=?",array($userid));
		$row = $query->row();
		return $row->patient_name;
	}

	function getFlowsByRegID(){
		$registration_id = $this->input->post("registration_id");
		$query = $this->db->query("CALL getFlowsByRegID($registration_id)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getAllStepsByRegID(){
		$registration_id = $this->input->post("registration_id");
		$flow_id = $this->input->post("flow_id");
		$query = $this->db->query("CALL getAllStepsByRegID($registration_id,$flow_id)");
		$result = $query->result();
		$html = "<table class='gridtable clear registration' cellpadding='0' cellspacing='0' width='782px'>";
		$html .= "<thead>
					<tr>
						<th>".lang("txt_step_name")."</th>
						<th>".lang("template")."</th>
						<th>".lang("txt_customize")."</td>
					</tr>
				</thead><tbody>";
		foreach($result as $rs){
			if($rs->template!="steps_datetime" && $rs->template!="steps_text" && $rs->template!="steps_summary"){
				$edit = "<a href='javascript:void(0)' class='customize' data-template='".$rs->template."' data-stepname='".$rs->step_name."' data-stepid='".$rs->step_id."' data onclick='fetchRegistrationAnswers($(this))'>Edit</a>";
			}else{
				$edit = "&nbsp;";
			}
			$html .="<tr>
						<td>".$rs->step_name."</td>
						<td>".lang($rs->template)."</td>
						<td>".$edit."</td>
					</tr>";
		}
		$html .="</tbody></table>";
		echo $html;
		exit;
	}

	function getRegistrationUserAnswers(){
		$flow_id = $this->input->post("flow_id");
		$step_id = $this->input->post("step_id");
		$user_id = $this->input->post("user_id");
		$template = $this->input->post("template");
		if($template=="steps_expand_collapse"){
			$query = $this->db->query("CALL getRegistrationUserAnswersCat($step_id,$user_id)");
		}else{
			$query  = $this->db->query("CALL getRegistrationUserAnswers($step_id,$user_id)");
		}
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function addRegAnswerCategoryForPatient(){
		$step_id = $this->input->post("step_id");
		$user_id = $this->input->post("patient_id");
		$answer = $this->input->post("new_category");
		$added_by = $this->session->userdata("user_id");
		$added_by_type = "psychologist";

		//first check if same answer exist for the patient for same step.
		$arrayCheck = array($step_id,$user_id,trim($answer));
		$qry = $this->db->query("SELECT answer_cat_name FROM bip_registration_answer_category WHERE step_id=? AND belongs_to=? AND answer_cat_name=?",$arrayCheck);
		$row=$qry->row();
		if(trim($answer)==trim($row->answer_cat_name)){
			$returnArr["errorCode"] = 1;
			$returnArr["errorMessage"] = lang("txt_item_exist");
		}else{
			$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_answer_category WHERE step_id=? AND belongs_to=?",array($step_id,$user_id))->row();
			$neworder =  $getmax->max_sort_order+1;
			$current_date = date("Y-m-d H:i:s");

			$this->db->query("INSERT INTO bip_registration_answer_category SET
								answer_cat_name     = ?,
								step_id             = ?,
								belongs_to          = ?,
								created_by        = ?,
								added_by            = ?,
								added_date          = '$current_date',
								answer_cat_status   = '1',
								sort_order          = '$neworder',
								answer_type         ='custom'
							",array($answer,$step_id,$user_id,$added_by,$added_by_type));


			$insert_id = $this->db->insert_id();
			if($this->db->affected_rows()>0){
				$returnArr["errorCode"] = 0;
				$returnArr["errorMessage"] = "";
				$returnArr["answer_cat_id"] = $insert_id;
				$returnArr["sort_order"] = $neworder;
				$returnArr["answer_cat_name"] = trim($answer);
				$returnArr["answer_type"] = lang("txt_added_by_you");
			}else{
				$returnArr["errorCode"] = 1;
				$returnArr["errorMessage"] = lang("txt_tryagain_later");
			}
		}
		echo json_encode($returnArr);
		exit;
	}

	function addRegAnswerForPatient(){
		$step_id = $this->input->post("step_id");
		$user_id = $this->input->post("patient_id");
		$answer = $this->input->post("new_answer");
		$added_by = $this->session->userdata("user_id");
		$added_by_type = "psychologist";

		//first check if same answer exist for the patient for same step.
		$arrayCheck = array($step_id,$user_id,trim($answer));
		$qry = $this->db->query("SELECT answer FROM bip_registration_answers WHERE step_id=? AND belongs_to=? AND answer=?",$arrayCheck);
		$row=$qry->row();
		if(trim($answer)==trim($row->answer)){
			$returnArr["errorCode"] = 1;
			$returnArr["errorMessage"] = lang("txt_item_exist");
		}else{
			$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_answers WHERE step_id=? AND belongs_to=?",array($step_id,$user_id))->row();
			$neworder =  $getmax->max_sort_order+1;
			$current_date = date("Y-m-d H:i:s");


			$this->db->query("INSERT INTO bip_registration_answers SET
								answer              = ?,
								step_id             = ?,
								belongs_to          = ?,
								created_by          = ?,
								added_by            = ?,
								added_date          = '$current_date',
								answer_status       = '1',
								sort_order          = '$neworder',
								answer_type         = 'custom'
							",array($answer,$step_id,$user_id,$added_by,$added_by_type));

			$insert_id = $this->db->insert_id();
			if($this->db->affected_rows()>0){
				$returnArr["errorCode"] = 0;
				$returnArr["errorMessage"] = "";
				$returnArr["answer_id"] = $insert_id;
				$returnArr["sort_order"] = $neworder;
				$returnArr["answer"] = trim($answer);
				$returnArr["answer_type"] = lang("txt_added_by_you");
			}else{
				$returnArr["errorCode"] = 1;
				$returnArr["errorMessage"] = lang("txt_tryagain_later");
			}
		}
		echo json_encode($returnArr);
		exit;
	}


	function addNewAnswerWithCatForPatient(){
		$step_id = $this->input->post("step_id");
		$user_id = $this->input->post("patient_id");
		$answer = $this->input->post("answer");
		$answer_cat_id = $this->input->post("cat_id");
		$added_by = $this->session->userdata("user_id");
		$added_by_type = "psychologist";

		//first check if same answer exist for the patient for same step.
		$arrayCheck = array($step_id,$user_id,trim($answer),$answer_cat_id);
		$qry = $this->db->query("SELECT answer FROM bip_registration_answers WHERE step_id=? AND belongs_to=? AND answer=? AND answer_cat_id=?",$arrayCheck);
		$row=$qry->row();
		if(trim($answer)==trim($row->answer)){
			$returnArr["errorCode"] = 1;
			$returnArr["errorMessage"] = lang("txt_item_exist");
		}else{
			$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_answers WHERE step_id=? AND belongs_to=?  AND answer_cat_id=?",array($step_id,$user_id,$answer_cat_id))->row();
			$neworder =  $getmax->max_sort_order+1;
			$current_date = date("Y-m-d H:i:s");

			$this->db->query("INSERT INTO bip_registration_answers SET
								answer              = ?,
								step_id             = ?,
								belongs_to          = ?,
								answer_cat_id       = ?,
								created_by          = ?,
								added_by            = ?,
								added_date          = '$current_date',
								answer_status       = '1',
								sort_order          = '$neworder',
								answer_type         = 'custom'
							",array($answer,$step_id,$user_id,$answer_cat_id,$added_by,$added_by_type));

			$insert_id = $this->db->insert_id();
			if($this->db->affected_rows()>0){
				$returnArr["errorCode"] = 0;
				$returnArr["errorMessage"] = "";
				$returnArr["answer_id"] = $insert_id;
				$returnArr["answer_cat_id"] = $answer_cat_id;
				$returnArr["sort_order"] = $neworder;
				$returnArr["answer"] = trim($answer);
				$returnArr["answer_type"] = lang("txt_added_by_you");
			}else{
				$returnArr["errorCode"] = 1;
				$returnArr["errorMessage"] = lang("txt_tryagain_later");
			}
		}
		echo json_encode($returnArr);
		exit;
	}


	function updateCustomRegAnswer(){
		//first check if the answer is in use.
		extract($this->input->post());

		$chk = $this->db->query("SELECT COUNT(*) as totalRecs FROM bip_registration_assignments WHERE answer_id=? AND step_id=? AND patient_id=?",array($answer_id,$step_id,$user_id))->row();
		//echo $this->db->last_query();
		if($chk->totalRecs>0){
			$retArray["errorCode"] = 1;
			$retArray["errorMessage"] = lang("txt_answer_in_use");
		}else{
			$answer = trim($answer);
			$check = $this->db->query("SELECT COUNT(*) chkrec FROM bip_registration_answers WHERE answer=? AND answer_id!=? AND step_id=? AND belongs_to=?",array($answer,$answer_id,$step_id,$user_id))->row();

			if($check->chkrec>0){
				$retArray["errorCode"] = 1;
				$retArray["errorMessage"] = lang("txt_item_exist");

			}else{
				$this->db->query("UPDATE bip_registration_answers SET answer=? WHERE answer_id=? AND step_id=? AND belongs_to=?",array($answer,$answer_id,$step_id,$user_id));
				$retArray["errorCode"] = 0;
				$retArray["errorMessage"] = "";
			}
		}
		echo json_encode($retArray);
		exit;
	}

	function updateCustomRegAnswerCategory(){
		//first check if the answer is in use.
		extract($this->input->post());

		$chk = $this->db->query("SELECT COUNT(*) as totalRecs FROM bip_registration_answers WHERE answer_cat_id=? AND step_id=? AND belongs_to=?",array($answer_cat_id,$step_id,$user_id))->row();
		//echo $this->db->last_query();
		if($chk->totalRecs>0){
			$retArray["errorCode"] = 1;
			$retArray["errorMessage"] = lang("txt_answer_in_use");
		}else{
			$answer_cat_name = trim($answer_cat_name);
			$check = $this->db->query("SELECT COUNT(*) chkrec FROM bip_registration_answer_category WHERE answer_cat_name=? AND answer_cat_id!=? AND step_id=? AND belongs_to=?",array($answer_cat_name,$answer_cat_id,$step_id,$user_id))->row();

			if($check->chkrec>0){
				$retArray["errorCode"] = 1;
				$retArray["errorMessage"] = lang("txt_item_exist");
			}else{
				$this->db->query("UPDATE bip_registration_answer_category SET answer_cat_name=? WHERE answer_cat_id=? AND step_id=? AND belongs_to=?",array($answer_cat_name,$answer_cat_id,$step_id,$user_id));
				$retArray["errorCode"] = 0;
				$retArray["errorMessage"] = "";
			}
		}
		echo json_encode($retArray);
		exit;
	}

	function deleteCustomRegAnswer(){
		//first check if the answer is in use.
		extract($this->input->post());
		$newstatus = $current_status==1 ? 0 : 1;
		$this->db->query("UPDATE bip_registration_answers SET answer_status='".$newstatus."' WHERE answer_id=? AND step_id=? AND belongs_to=?",array($answer_id,$step_id,$user_id));
		if($this->db->affected_rows()>0){
			$retArray["errorCode"] = 0;
			$retArray["errorMessage"] = "";
		}else{
			$retArray["errorCode"] = 1;
			$retArray["errorMessage"] = lang("txt_tryagain_later");
		}
		echo json_encode($retArray);
		exit;
	}

	function deleteCustomRegAnswerCat(){
		 //first check if the answer is in use.
	   extract($this->input->post());
	   $newstatus = $current_status==1 ? 0 : 1;

	   $this->db->query("UPDATE bip_registration_answer_category SET answer_cat_status=? WHERE answer_cat_id=? AND step_id=? AND belongs_to=?",array($newstatus,$answer_cat_id,$step_id,$user_id));
		if($this->db->affected_rows()>0){
			//$this->db->query("UPDATE bip_registration_answers SET answer_status='".$newstatus."' WHERE answer_cat_id=? AND step_id=? AND belongs_to=?",array($answer_cat_id,$step_id,$user_id));
			$retArray["errorCode"] = 0;
			$retArray["errorMessage"] = "";
		}else{
			$retArray["errorCode"] = 1;
			$retArray["errorMessage"] = lang("txt_tryagain_later");
		}
		echo json_encode($retArray);
		exit;
	}

	function sortRegistrationAnswers(){
		extract($this->input->post());
		if(count($ID)>0){
			for($k=0; $k<count($ID); $k++){
				$newsortOrder = $k+1;
				if($cat_id>0){
					$this->db->query("UPDATE bip_registration_answers SET sort_order=? WHERE belongs_to=? AND step_id=? AND answer_id=? AND answer_cat_id=?",array($newsortOrder,$user_id,$step_id,$ID[$k],$cat_id));
				}else{
					$this->db->query("UPDATE bip_registration_answers SET sort_order=? WHERE belongs_to=? AND step_id=? AND answer_id=?",array($newsortOrder,$user_id,$step_id,$ID[$k]));
				}
				//echo $this->db->last_query();
			}
		}

		if(count($CAT)>0){
			for($k=0; $k<count($CAT); $k++){
				$newsortOrder = $k+1;
				$this->db->query("UPDATE bip_registration_answer_category SET sort_order=? WHERE belongs_to=? AND step_id=? AND answer_cat_id=?",array($newsortOrder,$user_id,$step_id,$CAT[$k]));
			}
		}
	}

	function getCustomAnswerByCatID($catid,$stepid,$userid){
		$query = $this->db->query("CALL getCustomAnswerByCatID($catid,$stepid,$userid)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}


	function isAnswerOrCatInUse($id, $type="answer"){
		if($type=="answer"){
			$chk = $this->db->query("SELECT COUNT(*) as totalRecs
								FROM bip_registration_assignments_details
								WHERE answer_id=?",
								array($id))->row();
		}else{
			$chk = $this->db->query("SELECT COUNT(*) AS totalRecs
									FROM bip_registration_assignments_details
									WHERE FIND_IN_SET(answer_id,(SELECT GROUP_CONCAT(answer_id) FROM bip_registration_answers WHERE answer_cat_id=?))",
									array($id))->row();

		}
		if($chk->totalRecs>0){
			return true;
		}else{
			return false;
		}

	}

	/*Added by sabin @21st June 2015 >>*/
	/**
	 * Method to fetch all the homeworks
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   21st June 2015
	 * @param  integer $offset     [description]
	 * @param  integer $datalimit  [description]
	 * @param  string  $orderBy    [description]
	 * @param  integer $diffId     [description]
	 * @param  integer $filterId   [description]
	 * @param  string  $filterType [description]
	 * @return Object              The Recordset
	 */
	function getAllHomeworks($offset=0,$datalimit=50,$orderBy='desc',$diffId=0,$filterId=0,$filterType='treatment'){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;
	   // $query = $this->db->query("call getAllRegistrationTasks('$offset','$datalimit','$orderBy','$diffId','$filterId','$filterType',@a)");
		if($filterId>0){
			$sql = "SELECT * FROM bip_my_homework WHERE FIND_IN_SET(?,difficulty_id) AND homework_status='1' ORDER BY sort_order ASC, updated_at DESC, headline ASC  LIMIT ?,?";
						$bindArray = array($filterId,$offset,$datalimit);
		}else{
			$sql = "SELECT * FROM bip_my_homework WHERE homework_status='1' ORDER BY sort_order ASC, updated_at DESC, headline ASC LIMIT ?,?";
						$bindArray = array($offset,$datalimit);
		}
		$query = $this->db->query($sql,$bindArray);
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function totalHomeworkRows($difficulty_id=""){
		if (!empty($difficulty_id)) {
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_my_homework WHERE difficulty_id=?",array($difficulty_id));
		}else{
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_my_homework");
		}

		$row=$query->row();
		return $row->totalrow;
	}

	function changeHomeworkStatus(){
		$homework_id = $this->input->post("homework_id");
		$newstatus = $this->input->post("new_status");
		$current_date = date("Y-m-d H:i:s");

		if($homework_id>0){
			$this->db->query("UPDATE bip_my_homework SET homework_status=? WHERE homework_id=?",array($newstatus,$homework_id));
		}

		$newicon = $newstatus==0 ? "wrong.png":"enabled.gif";
		$retarry["icon_path"] = urlencode(base_url()."images/admin_icons/".$newicon);
		$writestatus = $newstatus==1?lang("inactive"):lang("active");
		$retarry["tooltip"] = lang("toggle_status")." ".$writestatus;
		echo json_encode($retarry);
		exit;
	}

	/**
	 * method to get homework by id
	 * @return [type] [description]
	 */
	function getMyHomeworkByID() {
		$homework_id = $this->input->post('homework_id');
		$query = $this->db->query("SELECT * FROM bip_my_homework WHERE homework_id=?",array($homework_id));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	 /**
	 * Method to save Registration task
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   24th March 2015
	 */
	function saveMyHomework() {
		$difficulty_id = join(',',$this->input->post('difficulty_id'));
		$headline = htmlspecialchars($this->input->post('headline'));
		$content = $this->input->post('homework_content');
		$current_date = date("Y-m-d H:i:s");
		$homeworkID=$this->input->post('homework_id');


		if ($homeworkID) {
			//chk if duplicate exists

			$res["error_code"] = "OK";
			$res["error_msg"] = "";

			$this->db->query("UPDATE bip_my_homework set
				difficulty_id=?,
				headline=?,
				contents=? WHERE
				homework_id=?",array($difficulty_id,$headline,$content,$homeworkID));

		}else{
			$getmax= $this->db->query("SELECT max(sort_order) as max_sort_order FROM bip_my_homework WHERE  FIND_IN_SET(?,difficulty_id)", array($difficulty_id))->row();
			$newsortorder = $getmax->max_sort_order+1;
			$res["error_code"] = "OK";
			$res["error_msg"] = "";
			$this->db->query("INSERT INTO bip_my_homework (difficulty_id,headline,contents,created_at,hw_type,created_by,added_by,homework_status,sort_order)
				VALUES (?,?,?,?,?,?,?,?,?)",array($difficulty_id,$headline,$content,$current_date,'standard', $this->session->userdata("user_id"),'admin','1',$newsortorder));

		}
		$this->db->freeDBResource();
		echo json_encode($res);
		exit;
	}


	function getHomeworksByDifficultyID($difficulty_id,$user_id){
		$query = $this->db->query("SELECT
								  hw.*,
								  (SELECT
									published_date
								  FROM
									bip_my_homework_assignment
								  WHERE patient_id = ?
									AND homework_id = hw.homework_id LIMIT 1) AS published_date,
								  (SELECT
									is_published
								  FROM
									bip_my_homework_assignment
								  WHERE patient_id = ?
									AND homework_id = hw.homework_id LIMIT 1) AS is_published,
								  (SELECT
									already_viewed
								  FROM
									bip_my_homework_assignment
								  WHERE patient_id = ?
									AND homework_id = hw.homework_id LIMIT 1) AS already_viewed
								FROM
								  bip_my_homework hw
								WHERE hw.homework_status = '1'
								  AND FIND_IN_SET(?, hw.difficulty_id)
								ORDER BY sort_order ASC, published_date ASC,
								  is_published ASC,
								  hw.updated_at DESC,
								  hw.homework_id DESC ", array($user_id, $user_id, $user_id, $difficulty_id));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function saveHwPublishSettings(){

		extract($this->input->post());
		if($publish_now==1){
			$publishing_date = date("Y-m-d H:i:s");
			$delete = $this->db->query("DELETE FROM bip_my_homework_assignment WHERE homework_id=? AND patient_id=?",array($homework_id, $patient_id));

			$resu = $this->db->query("INSERT INTO bip_my_homework_assignment SET
										published_date          = '$publishing_date',
										is_published            = '1',
										homework_id             = '$homework_id',
										patient_id              = '$patient_id',
										published_by            = '$published_by'
									");

		}else{
			$publishing_date = date("Y-m-d H:i:s", strtotime($published_date));
			$delete = $this->db->query("DELETE FROM bip_my_homework_assignment WHERE homework_id=? AND patient_id=?",array($homework_id, $patient_id));

			$resu = $this->db->query("INSERT INTO bip_my_homework_assignment SET
										published_date          = ?,
										is_published            = '0',
										homework_id             = ?,
										patient_id              = ?,
										published_by            = ?
									",array($published_date,$homework_id,$patient_id,$published_by));

		}

		if($resu){
			$res["error_code"] = "OK";
			$res["error_msg"] = "";
		}else{
			 $res["error_code"] = "error";
			$res["error_msg"] = "An error occurred";
		}

		echo json_encode($res);
		exit;

	}

	/**
	 * A method that runs as cron job to publish the homeworks in specified date
	 * @return nothing
	 */
	public function cronPublishHomeworks(){
		$currentdate = date("Y-m-d");
		$sql = "UPDATE bip_my_homework_assignment SET is_published='1' WHERE is_published='0' AND DATE_FORMAT(published_date,'%Y-%m-%d')=?";
		$this->db->query($sql,array($currentdate));
	}




	/**
	 * Method to fetch all the Crisis Plans
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   21st June 2015
	 * @param  integer $offset     [description]
	 * @param  integer $datalimit  [description]
	 * @param  string  $orderBy    [description]
	 * @param  integer $diffId     [description]
	 * @param  integer $filterId   [description]
	 * @param  string  $filterType [description]
	 * @return Object              The Recordset
	 */
	function getAllCrisisplans($offset=0,$datalimit=50,$orderBy='desc',$diffId=0,$filterId=0,$filterType='treatment'){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;
	   // $query = $this->db->query("call getAllRegistrationTasks('$offset','$datalimit','$orderBy','$diffId','$filterId','$filterType',@a)");
		if($filterId>0){
			$sql = "SELECT * FROM bip_my_crisis_plan WHERE plan_type='standard' AND plan_status='1' AND FIND_IN_SET(?,difficulty_id) ORDER BY updated_at DESC, headline ASC  LIMIT ?,?";
						$bindArray = array($filterId,$offset,$datalimit);
		}else{
			$sql = "SELECT * FROM bip_my_crisis_plan WHERE plan_type='standard'  AND plan_status='1' ORDER BY updated_at DESC, headline ASC LIMIT ?,?";
						$bindArray = array($offset,$datalimit);
		}
		$query = $this->db->query($sql,$bindArray);
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function totalCrisisplanRows($difficulty_id=""){
		if (!empty($difficulty_id)) {
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_my_crisis_plan WHERE plan_type='standard' AND plan_status='1' AND difficulty_id=?",array($difficulty_id));
		}else{
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_my_crisis_plan WHERE plan_type='standard' AND plan_status='1'");
		}

		$row=$query->row();
		return $row->totalrow;
	}

	function changeCrisisplanStatus(){
		$plan_id = $this->input->post("plan_id");
		$newstatus = $this->input->post("new_status");
		$current_date = date("Y-m-d H:i:s");

		if($plan_id>0){
			$this->db->query("UPDATE bip_my_crisis_plan SET plan_status=? WHERE plan_id=?",array($newstatus,$plan_id));
		}

		$newicon = $newstatus==0 ? "wrong.png":"enabled.gif";
		$retarry["icon_path"] = urlencode(base_url()."images/admin_icons/".$newicon);
		$writestatus = $newstatus==1?lang("inactive"):lang("active");
		$retarry["tooltip"] = lang("toggle_status")." ".$writestatus;
		echo json_encode($retarry);
		exit;
	}

	/**
	 * method to get homework by id
	 * @return [type] [description]
	 */
	function getMyCrisisplanByID() {
		$plan_id = $this->input->post('plan_id');
		$query = $this->db->query("SELECT * FROM bip_my_crisis_plan WHERE plan_id=?",array($plan_id));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	 /**
	 * Method to save Registration task
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   24th March 2015
	 */
	function saveMyCrisisplan() {
		$difficulty_id = join(',',$this->input->post('difficulty_id'));
		$headline = htmlspecialchars($this->input->post('headline'));
		$content = $this->input->post('plan_content');
		$current_date = date("Y-m-d H:i:s");
		$planID=$this->input->post('plan_id');


		if ($planID) {
			//chk if duplicate exists

			$res["error_code"] = "OK";
			$res["error_msg"] = "";

			$this->db->query("UPDATE bip_my_crisis_plan set
				difficulty_id=?,
				headline=?,
				contents=?,
				plan_type='standard',
				added_by='admin' WHERE
				plan_id=?",array($difficulty_id,$headline,$content,$planID));

		}else{

			$res["error_code"] = "OK";
			$res["error_msg"] = "";
			$this->db->query("INSERT INTO bip_my_crisis_plan (difficulty_id,headline,contents,created_at,plan_type,created_by,added_by,plan_status, belongs_to)
				VALUES (?,?,?,?,?,?,?,?,?)",array($difficulty_id,$headline,$content,$current_date,'standard', $this->session->userdata("user_id"),'admin','1','0'));

		}
		$this->db->freeDBResource();
		echo json_encode($res);
		exit;
	}

	/*Method to fetch crisis plan list only related to patient*/
	function getCrisisplansByDifficultyID($difficulty_id,$user_id){
		$query = $this->db->query("SELECT * FROM bip_my_crisis_plan
									WHERE FIND_IN_SET(?, difficulty_id) AND belongs_to=? AND plan_type='custom'
									ORDER BY updated_at DESC, headline ASC",array($difficulty_id, $user_id));
	   // echo $this->db->last_query(); exit;
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getCustomCrisisPlanById($plan_id){
		$query = $this->db->query("SELECT * FROM bip_my_crisis_plan WHERE plan_type='custom' AND plan_id=?", array($plan_id))->row();
		$this->db->freeDBResource();
		return $query;
	}

	function getStandardCrisisplansByDifficulty($difficulty_id){
		$query = $this->db->query("SELECT * FROM bip_my_crisis_plan
									WHERE FIND_IN_SET(?, difficulty_id) AND plan_type='standard' AND belongs_to='0' AND plan_status='1'
									ORDER BY updated_at DESC, headline ASC",array($difficulty_id));

		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function saveCustomCrisisPlan(){
		$belongs_to = $this->input->post("patient_id");
		$difficulty_id = $this->input->post("difficulty_id");
		$created_by = $this->input->post("published_by");
		$plan_id = $this->input->post("plan_id");
		$headline = $this->input->post("scp_headline");
		$contents = $this->input->post("scp_contents");

		$current_date = date("Y-m-d H:i:s");
		$res = array();
		if($plan_id>0){ //update
			$result = $this->db->query("UPDATE bip_my_crisis_plan SET
											difficulty_id=?,
											headline=?,
											contents=?,
											added_by='psychologist',
											created_by=?
										WHERE plan_type='custom' AND plan_id=?
									", array($difficulty_id,$headline,$contents,$created_by,$plan_id));
			if($result){
				 $res["error_code"] = "OK";
				 $res["error_msg"] = "";
			}else{
				 $res["error_code"] = "Error";
				 $res["error_msg"] = "Couldn't save the record.";
			}
		}else{ //insert
			$result = $this->db->query("INSERT INTO bip_my_crisis_plan SET
												difficulty_id=?,
												headline=?,
												contents=?,
												created_at=?,
												plan_type='custom',
												created_by=?,
												belongs_to=?,
												added_by='psychologist',
												plan_status='1'
										", array($difficulty_id,$headline,$contents,$created_at,$created_by,$belongs_to));
			 if($result){
				 $res["error_code"] = "OK";
				 $res["error_msg"] = "";
			}else{
				 $res["error_code"] = "Error";
				 $res["error_msg"] = "Couldn't save the record.";
			}
		}

		echo json_encode($res);
		exit;

	}

	function changeCustomCrisisPlanStatus(){
		/*
		 var sendData = {
					plan_id: $(this).attr("data-id"),
					patient_id:  $(this).attr("data-user"),
					current_status:  $(this).attr("data-currentstatus"),
				};
		 */
		$plan_id = $this->input->post("plan_id");
		$patient_id = $this->input->post("patient_id");
		$current_status = $this->input->post("current_status");
		$new_status = $current_status==1 ? 0 : 1;

		$result = $this->db->query("UPDATE bip_my_crisis_plan SET
											plan_status=?
										WHERE plan_id=? AND belongs_to=? AND plan_type='custom'
									",array($new_status,$plan_id,$patient_id));

		if($result){
			$res["status"] = "ok";
			$res["message"] = "";
		}else{
			$res["status"] = "error";
			$res["message"] = "Couldn't change status";
		}

		echo json_encode($res);
		exit;
	}
	/*Added by sabin @21st June 2015 <<*/

	/*Added by Sabin @2nd July 2015 >>*/
	/**
	 * Method to fetch all the Skills Modules
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   2nd July 2015
	 * @param  integer $offset     [description]
	 * @param  integer $datalimit  [description]
	 * @param  string  $orderBy    [description]
	 * @param  integer $diffId     [description]
	 * @param  integer $filterId   [description]
	 * @param  string  $filterType [description]
	 * @return Object              The Recordset
	 */
	function getAllModules($offset=0,$datalimit=50,$orderBy='desc',$diffId=0,$filterId=0,$filterType='treatment'){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;
	   // $query = $this->db->query("call getAllRegistrationTasks('$offset','$datalimit','$orderBy','$diffId','$filterId','$filterType',@a)");
		if($filterId>0){
			$sql = "SELECT * FROM bip_v2_modules WHERE  difficulty_id=? AND module_status='1'  ORDER BY sort_order ASC, modified_date DESC, module_name ASC  LIMIT ?,?";
						$bindArray = array($filterId,$offset,$datalimit);
		}else{
			$sql = "SELECT * FROM bip_v2_modules WHERE module_status='1' ORDER BY sort_order ASC, modified_date DESC, module_name ASC LIMIT ?,?";
						$bindArray = array($offset,$datalimit);
		}

		$query = $this->db->query($sql,$bindArray);
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function totalModulesRows($difficulty_id=""){
		if ($difficulty_id>0) {
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_v2_modules WHERE difficulty_id=? AND module_status='1'",array($difficulty_id));
		}else{
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_v2_modules WHERE module_status='1'");
		}

		$row=$query->row();
		return $row->totalrow;
	}


	/**
	 * method to get homework by id
	 * @return [type] [description]
	 */
	function getMySkillModulesByID() {
		$module_id = $this->input->post('module_id');
		$query = $this->db->query("SELECT * FROM bip_v2_modules WHERE module_id=?",array($module_id));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}


	/**
	 * Method to save Modules
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   3rd July 2015
	 */
	function saveMySkillsModule() {
		$difficulty_id = $this->input->post('difficulty_id');
		$org_difficulty_id  = $this->input->post("original_difficulty_id");
		//check if the new difficulty already has exposure. If it has don't let change difficulty_id.

		if($org_difficulty_id>0)
		{
			$check = $this->db->query("SELECT COUNT(*) as countMe FROM `bip_v2_skills` WHERE module_id IN (SELECT module_id FROM bip_v2_modules WHERE difficulty_id=? AND difficulty_id!=?)
	AND skill_type = 'exposure'", array($difficulty_id,$org_difficulty_id))->row();

		   //echo $this->db->last_query();
			if($check->countMe>0){
				$res["error_code"] = "error";
				$res["error_msg"] = "You cannot change the treatment because the target treatment already has Exposure module. There should be only one Exposure per treatment.";
				echo json_encode($res);
				exit;
			}
		}
		$module_name = $this->input->post('module_name');
		$module_desc = htmlspecialchars($this->input->post('module_desc'));
		$module_icon = $this->input->post('icon_file');
		$current_date = date("Y-m-d H:i:s");
		$moduleID=$this->input->post('module_id');


		if ($moduleID) {
			//chk if duplicate exists

			$res["error_code"] = "OK";
			$res["error_msg"] = "";

			$this->db->query("UPDATE bip_v2_modules set
				difficulty_id=?,
				module_name=?,
				module_desc=?,
				module_icon=? WHERE
				module_id=?",array($difficulty_id,$module_name,$module_desc,$module_icon,$moduleID));

		}else{

			$res["error_code"] = "OK";
			$res["error_msg"] = "";
			$getmax= $this->db->query("SELECT max(sort_order) as max_sort_order FROM bip_v2_modules WHERE difficulty_id=?", array($difficulty_id))->row();
			$sortOrder = $getmax->max_sort_order+1;
			$this->db->query("INSERT INTO bip_v2_modules (difficulty_id,module_name,module_desc,module_icon,created_date,module_status,sort_order)
				VALUES (?,?,?,?,?,?,?)",array($difficulty_id,$module_name,$module_desc,$module_icon,$current_date,'1',$sortOrder));

		}
		$this->db->freeDBResource();
		echo json_encode($res);
		exit;
	}


	function changeSkillModulesStatus(){
		$module_id = $this->input->post("module_id");
		$newstatus = $this->input->post("new_status");
		$current_date = date("Y-m-d H:i:s");

		if($module_id>0){
			$up = $this->db->query("UPDATE bip_v2_modules SET module_status=? WHERE module_id=?",array($newstatus,$module_id));
			if($up){ //now mark exposure master template as delete too, as user can add exposure template on another module with same difficulty
				$this->db->query("UPDATE bip_v2_skills SET skill_status='0' WHERE module_id=? and skill_type='exposure'",array($module_id));
			}
		}

		$newicon = $newstatus==0 ? "wrong.png":"enabled.gif";
		$retarry["icon_path"] = urlencode(base_url()."images/admin_icons/".$newicon);
		$writestatus = $newstatus==1?lang("inactive"):lang("active");
		$retarry["tooltip"] = lang("toggle_status")." ".$writestatus;
		echo json_encode($retarry);
		exit;
	}

	function sortMySkillsModule(){
	   extract($this->input->post());
	   for($k = 0; $k<count($ID); $k++){
			$od = $offset+$k+1;
			$this->db->query("UPDATE bip_v2_modules SET sort_order=? WHERE module_id=?",array($od,$ID[$k]));
	   }
	   echo "success";
	   exit;
	}

	function fetchAllModules(){
		$currentDifficulty = DefaultDifficulty();
		if($currentDifficulty>0){
			$query = $this->db->query("Select * from bip_v2_modules WHERE difficulty_id=? ORDER BY module_name", array($currentDifficulty));
		}else{
			$query = $this->db->query("Select * from bip_v2_modules ORDER BY module_name");
		}
		$result = $query->result();
		return $result;
	}


	function getAllSkills($offset=0,$datalimit=50,$orderBy='desc',$moduleId=0){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;

		if($moduleId>0){
			$sql = "SELECT * FROM bip_v2_skills WHERE module_id=? AND skill_status='1' ORDER BY skill_name ASC, last_updated DESC  LIMIT ?,?";
			$query = $this->db->query($sql,array($moduleId,$offset,$datalimit));
		}else{
			$sql = "SELECT * FROM bip_v2_skills WHERE skill_status='1' ORDER BY skill_name ASC, last_updated DESC LIMIT ?,?";
			$query = $this->db->query($sql,array($offset,$datalimit));
		}

		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}


	function totalSkillsRows($moduleId=0){
		if ($moduleId>0) {
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_v2_modules WHERE module_id=?",array($moduleId));
		}else{
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_v2_modules");
		}

		$row=$query->row();
		return $row->totalrow;
	}


	 function changeSkillStatus(){
		$skill_id = $this->input->post("skill_id");
		$newstatus = $this->input->post("new_status");
		$current_date = date("Y-m-d H:i:s");

		if($skill_id>0){
			$this->db->query("UPDATE bip_v2_skills SET skill_status=? WHERE skill_id=?",array($newstatus,$skill_id));
		}

		$newicon = $newstatus==0 ? "wrong.png":"enabled.gif";
		$retarry["icon_path"] = urlencode(base_url()."images/admin_icons/".$newicon);
		$writestatus = $newstatus==1?lang("inactive"):lang("active");
		$retarry["tooltip"] = lang("toggle_status")." ".$writestatus;
		echo json_encode($retarry);
		exit;
	}

	function loadColors(){
		$query = $this->db->query("Select * from bip_colour ORDER BY colour_name");
		$result = $query->result();
		return $result;
	}

	function saveSkills(){
		extract($this->input->post());
		$created_date = date("Y-m-d H:i:s");
		$res = array();
		$ok=0;
		if($skill_id>0){ //edit
			$upd = $this->db->query("UPDATE bip_v2_skills SET skill_name=?, module_id=? WHERE skill_id=?"
									, array($skill_name, $module_id, $skill_id));

			if($skill_type=="thoughts"){
				 $upd_sub = $this->db->query("UPDATE bip_v2_sk_thoughts SET module_id=?, headline=?, thought_text=?, thought_sound_file=?, sound_background_color=? WHERE skill_id=?",
											array($module_id, $thought_headline, $thought_text, $sound_file, isset($player_bg_color) ? $player_bg_color : "9bccf0", $skill_id));

			}


		}else{
			$ins = $this->db->query("INSERT INTO bip_v2_skills (skill_name, module_id, skill_type, created_date,  added_by, created_by, skill_status)
									  VALUES (?,?,?,?,?,?,?)
									", array($skill_name,$module_id,$skill_type,$created_date,'admin',$this->session->userdata("user_id"),'1'));
			if($ins){
				$skill_id = $this->db->insert_id();
				 if($skill_type=="thoughts"){
						$ins_sub = $this->db->query("INSERT INTO bip_v2_sk_thoughts (skill_id,module_id,headline,thought_type,thought_text,thought_sound_file, sound_background_color)
									VALUES (?,?,?,?,?,?,?)
									", array($skill_id, $module_id, $thought_headline, $thought_type, $thought_text, $sound_file, isset($player_bg_color) ? $player_bg_color : "9bccf0" ));

				 }
			}
		}

		$res["error_code"] = "OK";
		$res["error_msg"] = "";
		$res["skill_id"] = $skill_id;
		echo json_encode($res);
		exit;
	}


	function fetchSkillDetailsById($skillId){
		$query = $this->db->query("SELECT * FROM bip_v2_skills WHERE skill_id=?", array($skillId));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	function fetchSkillDetailsExtraById($skillId, $type){
		if($skillId==0) return "";

		if($type=="thoughts"){
			$row = $this->db->query("SELECT * FROM bip_v2_sk_thoughts WHERE skill_id=?", array($skillId))->row();
		}
		$this->db->freeDBResource();
		return $row;
	}

	function getThoughtTypeBySkillID($skillID){
		$row = $this->db->query("SELECT thought_type FROM bip_v2_sk_thoughts WHERE skill_id=?", array($skillID))->row();
		$this->db->freeDBResource();
		return $row->thought_type;
	}


	/**
	 * Method to fetch all the Feelings
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   8th July 2015
	 * @param  integer $offset     [description]
	 * @param  integer $datalimit  [description]
	 * @param  string  $orderBy    [description]
	 * @param  integer $diffId     [description]
	 * @param  integer $filterId   [description]
	 * @param  string  $filterType [description]
	 * @return Object              The Recordset
	 */
	function getAllFindFeelings($offset=0,$datalimit=50,$orderBy='desc',$diffId=0,$filterId=0,$filterType='treatment'){
		if (!$offset) $offset = 0;
		if (!$datalimit) $datalimit = 50;
	   // $query = $this->db->query("call getAllRegistrationTasks('$offset','$datalimit','$orderBy','$diffId','$filterId','$filterType',@a)");
		if($filterId>0){
			$sql = "SELECT * FROM bip_v2_feelings WHERE  FIND_IN_SET(?,difficulty_id) AND feeling_status='1' ORDER BY sort_order ASC  LIMIT ?,?";
						$bindArray = array($filterId,$offset,$datalimit);
		}else{
			$sql = "SELECT * FROM bip_v2_feelings WHERE feeling_status='1'  ORDER BY sort_order ASC LIMIT ?,?";
						$bindArray = array($offset,$datalimit);
		}
		$query = $this->db->query($sql,$bindArray);
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function totalFindFeelingRows($difficulty_id=""){
		if ($difficulty_id>0) {
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_v2_feelings WHERE FIND_IN_SET(?, difficulty_id)  AND feeling_status='1'",array($difficulty_id));
		}else{
			$query=$this->db->query("SELECT count(*) as totalrow FROM bip_v2_feelings  WHERE feeling_status='1'");
		}

		$row=$query->row();
		return $row->totalrow;
	}

	function changeMyFeelingStatus(){
		$feeling_id = $this->input->post("feeling_id");
		$newstatus = $this->input->post("new_status");
		$current_date = date("Y-m-d H:i:s");

		if($feeling_id>0){
			$this->db->query("UPDATE bip_v2_feelings SET feeling_status=? WHERE feeling_id=?",array($newstatus,$feeling_id));
		}

		$newicon = $newstatus==0 ? "wrong.png":"enabled.gif";
		$retarry["icon_path"] = urlencode(base_url()."images/admin_icons/".$newicon);
		$writestatus = $newstatus==1?lang("inactive"):lang("active");
		$retarry["tooltip"] = lang("toggle_status")." ".$writestatus;
		echo json_encode($retarry);
		exit;
	}

	function saveMyFindFeelings(){
		//$difficulty_id = join(',',$this->input->post('difficulty_id'));
		$difficulty_id = $this->input->post('difficulty_id');
		$feeling_name = addslashes(htmlspecialchars($this->input->post('feeling_name')));
		$description = $this->input->post('description');
		$current_date = date("Y-m-d H:i:s");
		$feelingID=$this->input->post('feeling_id');


		if ($feelingID) {
			//chk if duplicate exists

			$res["error_code"] = "OK";
			$res["error_msg"] = "";

			$this->db->query("UPDATE bip_v2_feelings set
				difficulty_id=?,
				feeling_name=?,
				description=?
				WHERE
				feeling_id=?",array($difficulty_id,$feeling_name,$description,$feelingID));

		}else{
			$getMax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_v2_feelings WHERE difficulty_id=?", array($difficulty_id))->row();
			$sortorder = $getMax->max_sort_order+1;
			$res["error_code"] = "OK";
			$res["error_msg"] = "";
			$this->db->query("INSERT INTO bip_v2_feelings (difficulty_id,feeling_name,description,created_at, created_by, feeling_status, sort_order)
				VALUES (?,?,?,?,?,?,?)",array($difficulty_id,$feeling_name,$description,$current_date,$this->session->userdata("user_id"),'1',$sortorder));

		}
		$this->db->freeDBResource();
		echo json_encode($res);
		exit;
	}


	function getMyFindFeelingsByID(){
		$feeling_id = $this->input->post('feeling_id');
		$query = $this->db->query("SELECT * FROM bip_v2_feelings WHERE feeling_id=?",array($feeling_id));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}
	/*Added by Sabin @2nd July 2015 <<*/

	/*Added by Sabin @12th July 2015 >>*/
	function getRegistrationNameById($regID){
		$query = $this->db->query("SELECT registration_name FROM bip_registration_task WHERE registration_id=?",array($regID));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row->registration_name;
	}

	function getDifficultiesByIDs($difficulty_ids){
		$query = $this->db->query("SELECT GROUP_CONCAT(difficulty) AS difficulties FROM bip_difficulty WHERE FIND_IN_SET(id,?)",array($difficulty_ids));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row->difficulties;
	}

	function getDifficultyByRegIDs($regID){
		$query = $this->db->query("SELECT GROUP_CONCAT(difficulty) AS difficulties FROM bip_difficulty WHERE FIND_IN_SET(id,(SELECT difficulty_id FROM bip_registration_task WHERE registration_id=? ))",array($regID));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row->difficulties;
	}

	function save_copy_registration(){
		extract($this->input->post());
		$current_date = date("Y-m-d H:i:s");
		//Save Registration info
		$this->db->query("INSERT INTO bip_registration_task (registration_name, difficulty_id, flow_type, added_date, registration_status)
SELECT registration_name, '".$dest_difficulty_id."', flow_type, '".$current_date."', 1
  FROM bip_registration_task
 WHERE registration_id = ?", array($source_reg_id));

		if ($this->db->affected_rows()>0) {
			$insert_id =  $this->db->insert_id();
			$new_registration_id = $insert_id;

			if($copy_option==2 || $copy_option==3){ //copy steps as well

				$selectSrcSteps = $this->db->query("SELECT * FROM bip_registration_steps WHERE registration_id=?", array($source_reg_id));
				$resultSrcSteps = $selectSrcSteps->result();
				$this->db->freeDBResource();

				if($selectSrcSteps->num_rows()>0){
					foreach($resultSrcSteps as $rss){
						$old_step_id = $rss->step_id;
					$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_steps WHERE registration_id= ?",array($insert_id))->row();
					   $neworder =  $getmax->max_sort_order+1;

				  /*$this->db->query("INSERT INTO bip_registration_steps SET
											step_name                       = '".$rss->step_name."',
											registration_id                 = $insert_id,
											flow_id                         = $rss->flow_id,
											is_multiple_choice              = $rss->is_multiple_choice,
											max_selection_allowed           = $rss->max_selection_allowed,
											template                        = '$rss->template',
											show_date                       = $rss->show_date,
											show_time                       = $rss->show_time,
											time_format                     = $rss->time_format,
											answer_text                     = '$rss->answer_text',
											button_text                     = '$rss->button_text',
											allow_custom_answer             = $rss->allow_custom_answer,
											allow_edit                      = $rss->allow_edit,
											allow_to_add_answer_category    = $rss->allow_to_add_answer_category,
											added_date                      = '$current_date',
											step_status                     = $rss->step_status,
											sort_order                      = $neworder
										");*/

		$this->db->insert('bip_registration_steps',array(
				"step_name"                       => $rss->step_name,
				"registration_id"                 => $insert_id,
				"flow_id"                         => $rss->flow_id,
				"is_multiple_choice"              => $rss->is_multiple_choice,
				"max_selection_allowed"           => $rss->max_selection_allowed,
				"template"                        => $rss->template,
				"show_date"                       => $rss->show_date,
				"show_time"                       => $rss->show_time,
				"time_format"                     => $rss->time_format,
				"answer_text"                     => $rss->answer_text,
				"button_text"                     => $rss->button_text,
				"allow_custom_answer"             => $rss->allow_custom_answer,
				"allow_edit"                      => $rss->allow_edit,
				"allow_to_add_answer_category"    => $rss->allow_to_add_answer_category,
				"added_date"                      => $current_date,
				"step_status"                     => $rss->step_status,
				"sort_order"                      => $neworder
		));

						//now add answers and answer category
						if ($this->db->affected_rows()>0 && $copy_option==3) {
							$step_id =  $this->db->insert_id();

							$dont_copy = "";
							if($dont_copy_custom_answers==1){
								$dont_copy = " AND answer_type='standard'";
							}

							$selectSrcCats = $this->db->query("SELECT * FROM bip_registration_answer_category WHERE step_id=?".$dont_copy,array($old_step_id));

							$srcCategories = $selectSrcCats->result();
							$this->db->freeDBResource();

							if($selectSrcCats->num_rows()>0){

								foreach($srcCategories as $srcCats)
								{
									$getmax = $this->db->query("SELECT IFNULL(0,MAX(sort_order))+1 as max_sort_order FROM bip_registration_answer_category WHERE step_id= ?",array($step_id))->row();
									$cat_sort_order = $getmax->max_sort_order;

									/*$insCat = $this->db->query("INSERT INTO bip_registration_answer_category SET
									  answer_cat_name     = '".$srcCats->answer_cat_name."',
									  step_id             = '$step_id',
									  added_date          = '$current_date',
									  answer_cat_status   = '1',
									  sort_order          = '$cat_sort_order',
									  answer_type         = '".$srcCats->answer_type."',
									  created_by          = '".$this->session->userdata("user_id")."',
									  belongs_to          = '".$srcCats->belongs_to."',
									  added_by            = '".$srcCats->added_by."'
									  ");*/

			$insCat = $this->db->insert('bip_registration_answer_category',array(
												"answer_cat_name"   => $srcCats->answer_cat_name,
												"step_id"           => $step_id,
												"added_date"        => $current_date,
												"answer_cat_status" => '1',
												"sort_order"        => $cat_sort_order,
												"answer_type"       => $srcCats->answer_type,
												"created_by"        => $this->session->userdata("user_id"),
												"belongs_to"        => $srcCats->belongs_to,
												"added_by"          => $srcCats->added_by
			));

									if($this->db->affected_rows()>0 && $insCat){
											$answer_cat_id = $this->db->insert_id();

											$selectSrcAns  = $this->db->query("SELECT * FROM bip_registration_answers WHERE step_id='".$old_step_id."' AND answer_cat_id='".$srcCats->answer_cat_id."'");
											$srcAns = $selectSrcAns->result();
											$this->db->freeDBResource();

											if($selectSrcAns->num_rows()>0)
											{
												   foreach($srcAns as $ans){
														 $getmax = $this->db->query("SELECT IFNULL(0,MAX(sort_order))+1 as max_sort_order FROM bip_registration_answers WHERE step_id= ?",array($step_id))->row();
														 $answer_order = $getmax->max_sort_order;
														 /*$insans = $this->db->query("INSERT INTO bip_registration_answers SET
																	answer          = '$ans->answer',
																	step_id         = '$step_id',
																	answer_cat_id   = '$answer_cat_id',
																	added_date      = '$current_date',
																	answer_status   = '1',
																	sort_order      = '$answer_order',
																	answer_type     = '$ans->answer_type',
																	created_by      = '".$this->session->userdata("user_id")."',
																	belongs_to      = '$ans->belongs_to',
																	added_by        = '$ans->added_by'
																");*/
														$insans = $this->db->insert('bip_registration_answers',array(
																	"answer"        => $ans->answer,
																	"step_id"       => $step_id,
																	"answer_cat_id" => $answer_cat_id,
																	"added_date"    => $current_date,
																	"answer_status" => 1,
																	"sort_order"    => $answer_order,
																	"answer_type"   => $ans->answer_type,
																	"created_by"    => $this->session->userdata("user_id"),
																	"belongs_to"    => $ans->belongs_to,
																	"added_by"      => $ans->added_by
															));

												   }
											}

										 /*   $insans = $this->db->query("INSERT INTO bip_registration_answers
														 (answer, step_id, answer_cat_id, added_date, last_updated,answer_status, sort_order, answer_type,created_by,belongs_to,added_by)
														 SELECT answer,'$step_id',".$answer_cat_id.", '$current_date','$current_date',1,(SELECT IFNULL(0,MAX(sort_order))+1 FROM bip_registration_answers WHERE step_id='$step_id' AND answer_cat_id='$answer_cat_id'),answer_type,".$this->session->userdata("user_id").", belongs_to, added_by
														 FROM bip_registration_answers WHERE step_id='$old_step_id' AND answer_cat_id>0".$dont_copy);*/

									}
								}
							}else{
								 $answer_cat_id = 0;
								 $insans = $this->db->query("INSERT INTO bip_registration_answers
									 (answer, step_id, answer_cat_id, added_date, answer_status, sort_order, answer_type,created_by,belongs_to,added_by)
									 SELECT answer,'$step_id','".$answer_cat_id."', '$current_date',1,(SELECT IFNULL(0,MAX(sort_order))+1 FROM bip_registration_answers WHERE step_id='$step_id' AND answer_cat_id='0'),answer_type,".$this->session->userdata("user_id").", belongs_to, added_by
									 FROM bip_registration_answers WHERE step_id='$old_step_id' AND (answer_cat_id='0' OR answer_cat_id IS NULL)".$dont_copy);

							}



							//insert answer category
						   /* $inscat = $this->db->query("INSERT INTO bip_registration_answer_category
								(answer_cat_name, step_id, added_date, last_updated,answer_cat_status, sort_order, answer_type, created_by, belongs_to, added_by)
SELECT CONCAT('CopyTest-','',answer_cat_name),'$step_id', '$current_date', '$current_date', 1, (SELECT IFNULL(0,MAX(sort_order))+1 FROM bip_registration_answer_category WHERE step_id='$step_id'), answer_type, ".$this->session->userdata("user_id").", belongs_to, added_by
  FROM bip_registration_answer_category
 WHERE step_id = ? ".$dont_copy, array($old_step_id));

							if($this->db->affected_rows()>0 && $inscat)
							{
								$answer_cat_id = $this->db->insert_id();
							}else{
								$answer_cat_id = 0;
							}

							$insans = $this->db->query("INSERT INTO bip_registration_answers
									 (answer, step_id, answer_cat_id, added_date, last_updated,answer_status, sort_order, answer_type,created_by,belongs_to,added_by)
									 SELECT CONCAT('CopyTest-','',answer),'$step_id',".$answer_cat_id.", '$current_date','$current_date',1,(SELECT IFNULL(0,MAX(sort_order))+1 FROM bip_registration_answers WHERE step_id='$step_id' AND answer_cat_id='$answer_cat_id'),answer_type,".$this->session->userdata("user_id").", belongs_to, added_by
									 FROM bip_registration_answers WHERE step_id=? ".$dont_copy, array($old_step_id));*/
						}
					}
				}
			}
			echo $new_registration_id;
			exit;
		}
	}
	/*Added by Sabin @12th July 2015 <<*/

	/*Added by Sabin @17th July 2015 >>*/
	function getPatientsRegistrations($userID){
		$query = $this->db->query("SELECT
									  ra.assignment_id, ra.answered_date,
									  r.registration_name,
									  COUNT(ra.assignment_id) AS total_numbers
									FROM
									  bip_registration_assignments ra
									  INNER JOIN bip_registration_task r
										ON r.registration_id = ra.registration_id
										WHERE ra.patient_id = ?
									   GROUP BY ra.registration_id
										ORDER BY r.sort_order ASC LIMIT 0,3", array($userID));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function fetchPatientAnsweredRegistrations($patientID){
		$query = $this->db->query("SELECT rad.registration_id, rt.registration_name FROM bip_registration_assignments_details rad
INNER JOIN bip_registration_task rt ON rt.registration_id = rad.registration_id
 WHERE rad.registration_id IN (SELECT registration_id FROM bip_registration_assignments WHERE patient_id= ? GROUP BY registration_id) GROUP BY rad.registration_id ORDER BY rt.sort_order ASC", array($patientID));
	   // $query = $this->db->query("SELECT registration_id, registration_name FROM bip_registration_task");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getStepsInRegistrations($registration_id){
		$query = $this->db->query("SELECT step_id,step_name, sort_order, template FROM bip_registration_steps WHERE registration_id=? AND template NOT IN('steps_summary','steps_text') ORDER BY sort_order ASC", array($registration_id));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getRegAssignmentIDs($patientID, $registrationID){
		$query = $this->db->query("SELECT assignment_id, incident_date, incident_time,stage_id FROM bip_registration_assignments WHERE patient_id= ? AND registration_id=?", array($patientID,$registrationID));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getAssignmentAnswers($assignmentID, $stepID){
		$query = $this->db->query("SELECT ad.*,  IF(ans.answer_cat_id>0, GROUP_CONCAT(CONCAT((SELECT answer_cat_name FROM bip_registration_answer_category WHERE answer_cat_id=ans.answer_cat_id),' - ', ans.answer)), GROUP_CONCAT(ans.answer)) AS patients_answer  FROM bip_registration_assignments_details ad INNER JOIN bip_registration_answers ans ON ans.answer_id = ad.answer_id WHERE
ad.assignment_id=? AND ad.step_id=?", array($assignmentID,$stepID));
		$result = $query->row();
	  // echo "ASS ID = $assignmentID, STEP ID =  $stepID<br>";
		$this->db->freeDBResource();
		return $result;
	}

	function fetchPatientsCustomAnswerRegistration($patientID){
		$query = $this->db->query("SELECT
								  st.registration_id,
								  r.registration_name
								FROM
								  bip_registration_steps st
								  INNER JOIN bip_registration_task r
									ON r.registration_id = st.registration_id
								WHERE st.step_id IN
								  (SELECT
									step_id
								  FROM
									bip_registration_answers
								  WHERE answer_type = 'custom'
									AND belongs_to = ? AND answer_status='1'
								  GROUP BY step_id)
									 GROUP BY st.registration_id
								ORDER BY r.last_updated DESC", array($patientID));

		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getPatientsCustomAnswers($regID, $patientID){
		$query = $this->db->query("SELECT ra.answer, ra.step_id, ra.answer_id, ra.added_by, rs.step_name, rs.sort_order FROM bip_registration_answers ra INNER JOIN bip_registration_steps rs ON rs.step_id = ra.step_id
WHERE ra.answer_type = 'custom' AND ra.belongs_to= ? AND rs.registration_id = ? AND ra.answer_status='1' ORDER BY rs.sort_order", array($patientID,$regID));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function deletePatientAnswer($answerID){
		$query = $this->db->query("UPDATE bip_registration_answers SET answer_status='0' WHERE answer_id=?", array($answerID));
		if($this->db->affected_rows()>0){
			echo "success";
		}else{
			echo "Error Occurred";
		}
		exit;
	}


	function updatePatientAnswer(){
		$answer_id = $this->input->post("answer_id");
		$answer = $this->input->post("answer");

		$query = $this->db->query("UPDATE bip_registration_answers SET answer=? WHERE answer_id=?", array($answer, $answer_id));
		echo "success";
		/*if($this->db->affected_rows()>0){
			echo "success";
		}else{
			echo "Nothing changed";
		}*/
		exit;
	}

	/**
	 * Method to fetch universal answers, the answer not linked with any registration or steps. This is used in special case of registration steps.
	 * @return object [answers list]
	 */
	function getSpecialAnswersList(){
		$difficulty_id = $this->input->post("difficulty_id")>0 ? $this->input->post("difficulty_id") : DefaultDifficulty();
		$is_posted = $this->input->post("post");
		if($difficulty_id>0){
			 $query = $this->db->query("SELECT a.answer, a.answer_id, a.answer_status,a.answer_type,a.belongs_to, d.difficulty, d.id FROM bip_registration_answers a INNER JOIN bip_difficulty d ON d.id = a.difficulty_id
								 WHERE a.special_answer='1' AND a.step_id='0' AND a.difficulty_id=? AND a.answer_status='1'  ORDER BY a.answer ASC",array($difficulty_id));
		}else{
			$query = $this->db->query("SELECT a.answer, a.answer_id, a.answer_status,a.answer_type,a.belongs_to, d.difficulty, d.id FROM bip_registration_answers a INNER JOIN bip_difficulty d ON d.id = a.difficulty_id
								 WHERE a.special_answer='1' AND a.step_id='0'  AND a.answer_status='1' ORDER BY d.difficulty ASC, a.answer ASC");
		}
		$result = $query->result();
		$this->db->freeDBResource();
		if($is_posted==true){
			if(count($result)>0){
				$a=1;
				foreach($result as $answer){
					$icon = "";
					if($answer->answer_type=="custom"){
						$user = $this->minapp_model->getUserByUserId($answer->belongs_to);
						$username = $user["first_name"]." ".$user["last_name"]." (".$user["username"].")";
						$icon = "<span><img src='".base_url()."images/admin_icons/usericon.png' title='".$username."'/></span>";
					}

					$class = $class=="alt-col-f1" ? "alt-col-e6" : "alt-col-f1";
					$html .= "<tr class='".$class."'>";
					$html .= "<td>".$a."</td>";
					$html .= "<td>".$icon."</td>";
					$html .= "<td id='row".$answer->answer_id."'><input type='text' data-originalanswer='".$answer->answer."' class='no-border ans-text' value='".$answer->answer."' readonly='readonly' style='width:84%' />
					<span class='controls'>
						<a href='javascript:void(0)' data-id='".$answer->answer_id."' class='update-special-answers'><img src='".base_url()."images/admin_icons/save.png'></a>

					</span>
					</td>";
					//<a class='cancel-upd-spec-answer' href='javascript:void(0)'><img src='".base_url()."images/admin_icons/enabled.gif'></a>
					$html .= "<td>".$answer->difficulty."</td>";
					$html .= "<td>";
					if($answer->answer_status==1){
						$html .= "Active";
					}else{
						$html .= "Inactive";
					}
					$html .= "</td>";
					$html .= "<td>";
					$html .= "<a href='javascript:void(0)' class='edit-spec-ans' data-id='".$answer->answer_id."'><img src='".base_url()."images/admin_icons/edit.png' /></a>";
					$html .=" <a href='javascript:void(0)' class='delete-spec-ans' data-id='".$answer->answer_id."'><img src='".base_url()."images/admin_icons/delete.png' /></a>";
					$html .= "</td>";
					$html .= "</tr>";
					$a++;
				}
			}else{
				$html = "<tr class='no-answer'><td colspan='4' style='text-align:center; padding:10px' >".lang("txt_no_answers")."</td></tr>";
			}
			echo $html;
			exit;
		}else{
			return $result;
		}
	}

	function saveSpecialAnswer(){
		extract($this->input->post());
		$answer = $new_answer;
		$answer = preg_replace('/\s+/', ' ',$answer);

		$added_by = trim($added_by)=="" ? "admin" : $added_by;
		$answer_type = $added_by=="admin" ? "standard" : "custom";
		$belongs_to = $answer_type=="custom" ? $patient_id : 0;

		//first check if same answer exist
		$check = $this->db->query("SELECT * FROM bip_registration_answers WHERE special_answer='1' AND difficulty_id=? AND answer=?", array($difficulty_id, $answer));
		if($check->num_rows()>0){
			$retArray["status"] = "error";
			$retArray["message"] = "The answer with same name exist. Specify different one.";
			echo json_encode($retArray);
			exit;
		}

		$created_date = date("Y-m-d H:i:s");
		$retArray = array();
		$ins = $this->db->query("INSERT INTO bip_registration_answers SET
									answer = ?,
									step_id = '0',
									answer_cat_id = '0',
									added_date = '$created_date',
									answer_status = '1',
									sort_order = '0',
									answer_type = '".$answer_type."',
									created_by = '".$this->session->userdata("user_id")."',
									belongs_to = '".$belongs_to."',
									added_by = ?,
									special_answer = '1',
									difficulty_id = ?
								", array($answer,$added_by,$difficulty_id));
		if($ins){
			$retArray["answer_id"] = $this->db->insert_id();

			$diff = $this->setting_model->getDifficultyById($difficulty_id);
			$retArray["status"] = "ok";
			$retArray["difficulty"] = $diff->difficulty;
			$retArray["answer"] = $answer;

			if($belongs_to>0){
				$fetchSpecialAnswer = $this->db->query("SELECT selected_answers FROM bip_reg_patient_selected_special_answer WHERE patient_id=? AND difficulty_id=?", array($belongs_to,$difficulty_id))->row();
				$specialAns = $fetchSpecialAnswer->selected_answers;
				if(trim($specialAns)!=""){
					$newAns = $specialAns.",".$retArray["answer_id"];
					$this->db->query("UPDATE bip_reg_patient_selected_special_answer SET selected_answers=? WHERE  patient_id=? AND difficulty_id=?", array($newAns,$belongs_to,$difficulty_id));
				}else{
					$newAns = $retArray["answer_id"];
					$this->db->query("INSERT INTO bip_reg_patient_selected_special_answer SET selected_answers=?, patient_id=?, difficulty_id=?", array($newAns,$belongs_to,$difficulty_id));
				}
				//now insert new answer

			}
			$retArray["answer_status"] ="Active";

		}else{
			$retArray["status"] = "error";
			$retArray["message"] = "Error saving an answer.";
		}

		echo json_encode($retArray);
		exit;
	}

	function updateSpecialAnswer(){
		$answer = $this->input->post("answer");
		$answer = preg_replace('/\s+/', ' ',$answer);

		$id = $this->input->post("answer_id");

		//first check if same answer exist
		$check = $this->db->query("SELECT * FROM bip_registration_answers WHERE answer=? AND answer_id!=?", array($answer,$id));
		if($check->num_rows()>0)
		{
			$retArray["status"] = "error";
			$retArray["message"] = "The answer with same name exist. Specify different one.";
			echo json_encode($retArray);
			exit;
		}

		$query = $this->db->query("UPDATE bip_registration_answers SET answer=? WHERE answer_id=?", array($answer,$id));
		$retarr = array();

		if($query){
			$retarr["status"] = "ok";
		}else{
			$retarr["status"] = "error";
			$retarr["message"] = "Error in updating answer.";
		}

		echo json_encode($retarr);
		exit;
	}


	function getAllSpecialAnswers($userid,$difficulty_id){
		$query = $this->db->query("SELECT answer, answer_id, answer_status FROM bip_registration_answers WHERE special_answer='1' AND step_id='0' AND answer_status='1' AND difficulty_id=? AND (answer_type='standard' OR (answer_type='custom' AND belongs_to=?) )", array($difficulty_id, $userid));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function saveSelectedAnswers(){
		extract($this->input->post());
		$del = $this->db->query("DELETE FROM bip_reg_patient_selected_special_answer WHERE patient_id=? AND difficulty_id=?", array($userid,$difficulty_id));
		$query = $this->db->query("INSERT INTO bip_reg_patient_selected_special_answer SET
										patient_id = ?,
										selected_answers = ?,
										difficulty_id = ?
								  ", array($userid,$answers,$difficulty_id));
		$retarr = array();

		if($query){
			$retarr["status"] = "ok";
		}else{
			$retarr["status"] = "error";
			$retarr["message"] = "Error in saving answers.";
		}

		echo json_encode($retarr);
		exit;
	}

	function getPatientSpecialAnswers($patientID, $difficulty_id){
		$query = $this->db->query("SELECT selected_answers FROM bip_reg_patient_selected_special_answer WHERE patient_id=? AND difficulty_id=?", array($patientID,$difficulty_id))->row();
		$this->db->freeDBResource();
		if($query){
			return explode(",",$query->selected_answers);
		}else{
			return "";
		}
	}

	function viewRegistrationgraphs($patientID){
		//$patientID = 1244;
		//get week numbers of current year on the basis of all answered registrations
		$querywn = $this->db->query("SELECT
									  WEEKOFYEAR(answered_date) AS weeknumber,
									  YEAR(answered_date) AS answered_year
									FROM
									  bip_registration_assignments
									WHERE patient_id = ?
									  AND (
										DATE_FORMAT(answered_date,'%Y-%m-%d') <= CURDATE()
										AND DATE_FORMAT(answered_date,'%Y-%m-%d') >= CURDATE() - INTERVAL 6 MONTH
									  )
									GROUP BY weeknumber,
									  answered_year
									ORDER BY answered_date ASC ", array($patientID));
		//echo "<div style='display:none' class='test-west'>";

		//echo "</div>";

		$resultwn = $querywn->result();
		$this->db->freeDBResource();
		$array = array();
		if(count($resultwn)>0){
			foreach($resultwn as $wn){
				$array["labels"][] = "v.".$wn->weeknumber;

			}
		}

		//fetch all answered registration
		$queryreg = $this->db->query("SELECT
									  ra.registration_id,
									  r.registration_name,
									  r.bar_color
									FROM
									  bip_registration_assignments ra
									  INNER JOIN bip_registration_task r
										ON r.registration_id = ra.registration_id
									WHERE ra.patient_id = ? AND  (
										DATE_FORMAT(ra.answered_date,'%Y-%m-%d') <= CURDATE()
										AND DATE_FORMAT(ra.answered_date,'%Y-%m-%d') >= CURDATE() - INTERVAL 6 MONTH
									  )
									GROUP BY ra.registration_id", array($patientID));
		$resultreg = $queryreg->result();
		$this->db->freeDBResource();
		if(count($resultreg)>0){
			$k=0;
			foreach($resultreg as $reg){
				//$array["datasets"][$k]["label"] = $reg->registration_name;
			  //  $array["datasets"][$k]["reg_id"] = $reg->registration_id;

				$array["series"][$k]["id"] = $reg->registration_id;
				$array["series"][$k]["name"] = str_replace("'","&#39;",$reg->registration_name."^".$reg->bar_color);
				$array["series"][$k]["meta"] = "reg-".$reg->registration_id;
				$regcount = array();
				foreach($resultwn as $wn){
					$qry = $this->db->query("SELECT
											  COUNT(*) as cnt
											FROM
											  bip_registration_assignments
											WHERE patient_id = ?
											  AND registration_id = '".$reg->registration_id."'
											  AND WEEKOFYEAR(answered_date) = '".$wn->weeknumber."'
											  AND YEAR(answered_date) = '".$wn->answered_year."'", array($patientID))->row();
					$regcount[] = $qry->cnt;

				}

			   $array["series"][$k]["data"] = $regcount;
			   $k++;
			}
		}

		//echo  json_encode($array); exit;
		return json_encode($array);

	}
	/*Added by Sabin @17th July 2015 <<*/

	/*Added by Sabin @2nd August 2015 >>*/
	function registrationToExcel($patientID){
		$regquery = $this->db->query("SELECT r.registration_name, a.registration_id FROM bip_registration_assignments a INNER JOIN bip_registration_task r ON a.registration_id = r.registration_id WHERE a.patient_id=? AND a.assignment_code!='test' GROUP BY a.registration_id
									 ", array($patientID));

		$regresults = $regquery->result();
		$this->db->freeDBResource();
		return $regresults;
	}

	function manageFeelingsDefinition(){
		$query = $this->db->query("SELECT * FROM bip_v2_feelings_definition LIMIT 1");
		$row = $query->row();
		return $row;
	}

	function saveFeelingDefinitions(){
		$query = $this->db->query("SELECT * FROM bip_v2_feelings_definition LIMIT 1");
		$row = $query->row();
		$current_date = date("Y-m-d H:i:s");

		if($query->num_rows()>0){
			$def_id = $row->def_id;
			$ins = $this->db->query("UPDATE bip_v2_feelings_definition SET primary_feelings=?, secondary_feelings=? WHERE def_id=?", array($def_id,$this->input->post("primary_feelings"), $this->input->post("secondary_feelings")));
		}else{
			$ins= $this->db->query("INSERT INTO bip_v2_feelings_definition SET primary_feelings=?, secondary_feelings=?", array($this->input->post("primary_feelings"), $this->input->post("secondary_feelings")));
		}

		if($ins){
			$array["status"] = "ok";
			$array["message"] = lang("txt_added_feeling_def_success");
		}else{
			$array["status"] = "error";
			$array["message"] = lang("txt_added_feeling_def_fail");
		}

		echo json_encode($array);
		exit;
	}

	function manageModuleIcons(){
		$icon_path = $_SERVER["DOCUMENT_ROOT"].$this->config->item('uploadify_upload_path')."module_icons";
		$g = glob($icon_path."/*.png");
		return $g;
	}

	function manageModuleSounds(){
		$audio_path = $_SERVER["DOCUMENT_ROOT"].$this->config->item('sound_file_path')."misc";
		$g = glob($audio_path."/*.mp3");
		return $g;
	}

	function getAllSteps($regID){
		$query = $this->db->query("SELECT step_id, step_name, template FROM bip_registration_steps WHERE
								template IN('steps_sentence','steps_keywords','steps_expand_collapse') AND
								special_case='0' AND registration_id=?", array($regID));
		$result = $query->result();
		$this->db->freeDBResource();
		$array = array();
		foreach($result as $res){
			$array["step_id"][] = $res->step_id;
			$array["step_name"][] = $res->step_name;
			$array["template"][] = $res->template;
		}

		echo json_encode($array);
		exit;
	}


	function getAllAnswerCats($stepID){
		$query = $this->db->query("SELECT answer_cat_id, answer_cat_name FROM bip_registration_answer_category WHERE
								step_id=?", array($stepID));
		$result = $query->result();
		$this->db->freeDBResource();
		$array = array();
		foreach($result as $res){
			$array["answer_cat_id"][] = $res->answer_cat_id;
			$array["answer_cat_name"][] = $res->answer_cat_name;
		}

		echo json_encode($array);
		exit;
	}

	function saveNewCustomAnswer(){
		extract($this->input->post());
		if($hid_step_template=="steps_expand_collapse"){
			$answer_cat_id = $select_answer_cat;
		}else{
			$answer_cat_id = 0;
		}

		$new_answer = preg_replace('/\s+/', ' ',$new_answer);

		//check if this answer is already added
		$chk= $this->db->query("SELECT COUNT(*) as reccount FROM bip_registration_answers WHERE answer=? AND step_id=? AND answer_cat_id=?", array($new_answer, $select_step,$answer_cat_id))->row();
		$this->db->freeDBResource();
		$array = array();

		if($chk->reccount>0){
			$array["status"] = "error";
			$array["message"] = lang("txt_answer_exist");
		}else{
			$current_date = date("Y-m-d H:i:s");
			$bindarray = array($new_answer, $select_step, $answer_cat_id,$this->session->userdata("user_id"),$patient_id);
			$ins = $this->db->query("INSERT INTO bip_registration_answers SET
										answer            = ?,
										step_id           = ?,
										answer_cat_id     = ?,
										added_date        = '".$current_date."',
										answer_status     = '1',
										sort_order        = '0',
										answer_type       = 'custom',
										created_by        = ?,
										belongs_to        = ?,
										added_by          = 'psychologist',
										mapped_answer_id  = '',
										special_answer    = '0'", $bindarray);

			if($ins){
				$array["status"] = "ok";
				$array["message"] = lang("txt_added_want_more");
			}else{
				$array["status"] = "error";
				$array["message"] = lang("txt_error_add_answer");
			}
		}
		echo json_encode($array);
		exit;
	}

	function checkIfExposureExistForDifficulty($moduleID){
		$difficulty = $this->db->query("SELECT difficulty_id FROM bip_v2_modules WHERE module_id=?", array($moduleID))->row();
		$difficulty_id = $difficulty->difficulty_id;

		//now check if exposure exist
		$chkexposure = $this->db->query("SELECT count(*) as counts FROM bip_v2_skills s INNER JOIN bip_v2_modules m ON m.module_id = s.module_id
						WHERE m.difficulty_id = ? AND s.skill_type = 'exposure'",array($difficulty_id))->row();

		if($chkexposure->counts>0){
			return "yes";
		}else{
			return "no";
		}
	}

	function fetchExposureTemplatesIcon(){
		$query = $this->db->query("SELECT * FROM bip_v2_exposure_templates WHERE template_status='1'");
		$results = $query->result();
		$this->db->freeDBResource();

		return $results;
	}

	function getSkillType($skillID){
		$query = $this->db->query("SELECT skill_type FROM bip_v2_skills WHERE skill_id=?",array($skillID));
		$row = $query->row();
		$this->db->freeDBResource();

		if($row){
			return $row->skill_type;
		}else{
			return "exposure";
		}

	}

	function saveExposureSteps(){
		extract($this->input->post());

		if(!$skill_id>0){
			echo "No skill selected";
			exit;
		}


		$skill_type = $this->getSkillType($skill_id);
		$current_date = date("Y-m-d H:i:s");

		$step_label_10 = isset($step_label_10) ? $step_label_10 : "";
		$step_label_0 = isset($step_label_0) ? $step_label_0 : "";
		$is_multiple_choice = isset($is_multiple_choice) ? $is_multiple_choice : 0;
		$max_selection_allowed = isset($max_selection_allowed) ? $max_selection_allowed : 0;
		$answer_text = isset($answer_text) ? $answer_text : "";
		$alternate_text = isset($alternate_text) ? $alternate_text : "";
		$countdown_title = isset($step_countdown_title) ? $step_countdown_title : "";
		$countdown_desc = isset($countdown_desc) ? $countdown_desc : "";
		$allow_to_add_answers = isset($allow_to_add_answers) ? $allow_to_add_answers : 0;
		$allow_to_edit_list = isset($allow_to_edit_list) ? $allow_to_edit_list : 0;
		$allow_to_add_answer_category = isset($allow_to_add_answer_category) ? $allow_to_add_answer_category : 0;
		$step_title = addslashes(htmlspecialchars($step_title));
		$same_title_as_skill_exposure = isset($same_title_as_skill_exposure) ? $same_title_as_skill_exposure : 0;

		$enable_countdown = isset($enable_countdown) ? $enable_countdown : 0;
		$cntdown_min_minutes = isset($cntdown_min_minutes) ? $cntdown_min_minutes : 0;
		$cntdown_max_minutes = isset($cntdown_max_minutes) ? $cntdown_max_minutes : 0;
		$cntdown_start_title = isset($cntdown_start_title) ? $cntdown_start_title : "";
		$cntdown_start_desc = isset($cntdown_start_desc) ? $cntdown_start_desc : "";
		$cntdown_countdown_desc = isset($cntdown_countdown_desc) ? $cntdown_countdown_desc : "";


		$answer_text = str_replace("http:","",$answer_text);
		$answer_text = str_replace("https:","",$answer_text);
		$answer_text = str_replace("//","http://",$answer_text);


		$countdown_desc = str_replace("http:","",$countdown_desc);
		$countdown_desc = str_replace("https:","",$countdown_desc);
		$countdown_desc = str_replace("//","http://",$countdown_desc);




		if($step_id>0) //UPDATE
		{
			$arrayData = array($step_title, $module_id, $skill_id, $skill_type, $step_label_10, $step_label_0, $is_multiple_choice, $max_selection_allowed,
						  $template, $answer_text, $alternate_text,$countdown_title,$countdown_desc, $allow_to_add_answers, $allow_to_edit_list, $allow_to_add_answer_category,$same_title_as_skill_exposure, $enable_countdown, $cntdown_min_minutes, $cntdown_max_minutes, $cntdown_start_title, $cntdown_start_desc, $cntdown_countdown_desc, 1,$step_id);

			$this->db->query("UPDATE bip_v2_sk_exposure_steps SET
							step_name                       = ?,
							module_id                       = ?,
							skill_id                        = ?,
							skill_type                      = ?,
							step_label_10                   = ?,
							step_label_0                    = ?,
							is_multiple_choice              = ?,
							max_selection_allowed           = ?,
							template                        = ?,
							answer_text                     = ?,
							alternate_text                  = ?,
							countdown_title                 = ?,
							countdown_desc                  = ?,
							allow_custom_answer             = ?,
							allow_edit                      = ?,
							allow_to_add_answer_category    = ?,
							title_same_as_skill_ex_name     = ?,
							enable_countdown                = ?,
							cntdown_min_minutes             = ?,
							cntdown_max_minutes             = ?,
							cntdown_start_title             = ?,
							cntdown_start_desc              = ?,
							cntdown_countdown_desc          = ?,
							step_status                     = ? WHERE step_id = ?", $arrayData);

		   // echo $this->db->last_query();

			$step_id_altered = $step_id;
		}else{

			$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_v2_sk_exposure_steps WHERE skill_id= ?", array($skill_id))->row();

			$neworder =  $getmax->max_sort_order+1;

			$arrayData = array($step_title, $module_id, $skill_id, $skill_type, $step_label_10, $step_label_0, $is_multiple_choice, $max_selection_allowed,
						  $template, $answer_text, $alternate_text,$countdown_title,$countdown_desc, $allow_to_add_answers, $allow_to_edit_list, $allow_to_add_answer_category, $current_date, 1,$same_title_as_skill_exposure, $enable_countdown, $cntdown_min_minutes, $cntdown_max_minutes, $cntdown_start_title, $cntdown_start_desc, $cntdown_countdown_desc);

			$this->db->query("INSERT INTO bip_v2_sk_exposure_steps SET
						step_name                       = ?,
						module_id                       = ?,
						skill_id                        = ?,
						skill_type                      = ?,
						step_label_10                   = ?,
						step_label_0                    = ?,
						is_multiple_choice              = ?,
						max_selection_allowed           = ?,
						template                        = ?,
						answer_text                     = ?,
						alternate_text                  = ?,
						countdown_title                 = ?,
						countdown_desc                  = ?,
						allow_custom_answer             = ?,
						allow_edit                      = ?,
						allow_to_add_answer_category    = ?,
						added_date                      = ?,
						step_status                     = ?,
						title_same_as_skill_ex_name     = ?,
						enable_countdown                = ?,
						cntdown_min_minutes             = ?,
						cntdown_max_minutes             = ?,
						cntdown_start_title             = ?,
						cntdown_start_desc              = ?,
						cntdown_countdown_desc          = ?,
						sort_order                      = $neworder
						", $arrayData);
			$step_id_altered =  $this->db->insert_id();
		}

		if ($step_id_altered>0) {
			//save answer category if there is any
			if(count($answers_category)>0){
				for($c = 0; $c<count($answers_category); $c++){
					if($cat_id[$c]>0){ //update
						 $this->db->query("UPDATE bip_v2_skill_exposure_answer_category SET
							   answer_cat_name     = ?,
							   step_id             = ?,
							   answer_cat_status   = ?,
							   sort_order          = ?
							   WHERE answer_cat_id = ?
							", array($answers_category[$c],$step_id_altered,1,$answer_cat_order[$c],$cat_id[$c]));
					}
				}
			}

			//now save answers if there is any
			if(count($answers)>0){

				for($k = 0; $k<count($answers); $k++){
					$catID = $template=="step_ec_descriptions" ? $cat_id[$k] : $answer_cat_id[$k];

					if($answer_id[$k]==0){ //insert
						$this->db->query("INSERT INTO bip_v2_skill_exposure_answers SET
							   answer          = ?,
							   step_id         = ?,
							   answer_cat_id   = ?,
							   added_date      = ?,
							   answer_status   = ?,
							   sort_order      = ?,
							   answer_type     = 'standard',
							   created_by      = ?,
							   added_by        = 'admin',
							   belongs_to      ='0'
							",array($answers[$k],$step_id_altered,$catID,$current_date,1,$answer_order[$k], $this->session->userdata("user_id")));

						$insert_id = $this->db->insert_id();
						//now save to all patients table.


					}else{ //update
						$this->db->query("UPDATE bip_v2_skill_exposure_answers SET
							   answer          = ?,
							   step_id         = ?,
							   answer_cat_id   = ?,
							   answer_status   = ?,
							   sort_order      = ?
								WHERE answer_id = ?
							",array($answers[$k],$step_id_altered,$catID,1,$answer_order[$k],$answer_id[$k]));
					}
				}
			}

			$array["status"] =  "ok";
			$array["message"] = "";

		}else{

			$array["status"] = "error";
			$array["message"] = "Error in saving records";

		}

		echo json_encode($array);
		exit;
	}

	function fetchExposureSteps($skillID){
		$query = $this->db->query("SELECT IF(s.step_name<>'',s.step_name,'no-title') as step_name, s.template, s.step_id, s.step_status, s.sort_order, t.template_name,t.template_desc FROM bip_v2_sk_exposure_steps s
								 INNER JOIN bip_v2_exposure_templates t ON t.template_display_name = s.template
								 WHERE s.skill_id = ? AND s.step_status='1' ORDER BY s.sort_order ASC ", array($skillID));
	   // echo $this->db->last_query();
		$results = $query->result();
		$this->db->freeDBResource();

		return $results;
	}

	function sortExposureSteps(){
	   extract($this->input->post());
	   for($k = 0; $k<count($ID); $k++){
			$this->db->query("UPDATE bip_v2_sk_exposure_steps SET sort_order='".($k+1)."' WHERE step_id=? AND skill_id=?",array($ID[$k],$skill_id));
	   }
	   echo "success";
	   exit;
	}

	 function changeExposureStepStatus(){
		$stepid = $this->input->post("step_id");
		$newstatus = $this->input->post("new_status");
		$current_date = date("Y-m-d H:i:s");

		if($stepid>0){
			$this->db->query("UPDATE bip_v2_sk_exposure_steps SET step_status='$newstatus' WHERE step_id='$stepid'");
		}
		$newicon = $newstatus==0?"wrong.png":"enabled.gif";
		$retarry["icon_path"] = urlencode(base_url()."images/admin_icons/".$newicon);
		$writestatus = $newstatus==1?lang("inactive"):lang("active");
		$retarry["tooltip"] = lang("toggle_status")." ".$writestatus;
		echo json_encode($retarry);
		exit;
	}

	function fetchExposureStepDetailsByStepID($stepID){
		$query = $this->db->query("SELECT * FROM bip_v2_sk_exposure_steps WHERE step_id=?", array($stepID));
		$row = $query->result();
		$this->db->freeDBResource();
		return $row;
	}

	function getModuleNameById($moduleId){
		$query = $this->db->query("SELECT module_name FROM bip_v2_modules WHERE module_id=?", array($moduleId))->row();
		$this->db->freeDBResource();
		return $query->module_name;
	}
	/*Added by Sabin @2nd August 2015 <<*/

	/*Added by Sabin @12th August 2015 >>*/
	function saveExposureAnswerCategory(){
		$current_date = date("Y-m-d H:i:s");
		$array = array();
		extract($this->input->post());
	   /* $difficulty_ids = $this->getDifficultyIDbyRegID($registration_id);
		$patients = $this->getAllusersByDifficultyID($difficulty_ids);*/
		$template_name = $template;

		if(!$step_id>0){ //insert steps as well
			$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_v2_sk_exposure_steps WHERE skill_id= ?",array($skill_id))->row();
			$neworder =  $getmax->max_sort_order+1;
			$array["sort_order"] = $neworder;
			$array["new_step"]= 1;
			$step_title = addslashes(htmlspecialchars($step_title));

			$this->db->query("INSERT INTO bip_v2_sk_exposure_steps SET skill_type='exposure',step_name=?,skill_id=?,template=?,added_date=?,sort_order=?",array($step_title,$skill_id,$template_name,$current_date,$neworder));
			$insert_id = $this->db->insert_id();

			if($insert_id>0){ //now save category
				$step_answer_cat = addslashes(htmlspecialchars($step_answer_cat));
				$this->db->query("INSERT INTO bip_v2_skill_exposure_answer_category SET answer_cat_name=?, step_id='$insert_id',added_date='$current_date',sort_order='1',answer_type='standard',created_by=?, added_by='admin', belongs_to='0', answer_cat_status='1'",array($step_answer_cat, $this->session->userdata("user_id")));
				$array["cat_id"] = $this->db->insert_id();
				$array["cat_sort_order"]=1;

			}
		}else{
			$insert_id = $step_id;
			$getmaxCat = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_v2_skill_exposure_answer_category WHERE step_id= ?",array($insert_id))->row();
			$new_cat_order =  $getmaxCat->max_sort_order+1;

			$step_answer_cat = addslashes(htmlspecialchars($step_answer_cat));
			$this->db->query("INSERT INTO bip_v2_skill_exposure_answer_category SET answer_cat_name=?, step_id=?,added_date='$current_date',sort_order='$new_cat_order',answer_type='standard',answer_cat_status='1',created_by=?, added_by='admin', belongs_to='0'",array($step_answer_cat,$insert_id,$this->session->userdata("user_id")));
			$array["cat_id"] = $this->db->insert_id();
			$array["new_step"]= 0;
			$array["cat_sort_order"] = $new_cat_order;

		}
		$array["step_id"] = $insert_id;
		$array["skill_id"] = $skill_id;
		$array["template"] = $template_name;
		$array["template_name"] = lang($template_name);
		echo json_encode($array);
		exit;
	}


	function removeExposureStepAnswer(){
		$this->db->query("UPDATE bip_v2_skill_exposure_answers SET answer_status=? WHERE answer_id =?",array($this->input->post("dowhat"),$this->input->post("answer_id")));
		if($this->db->affected_rows()>0){
			echo "success";
		}else{
			echo "failed";
		}
	}


	function getExposureTemplateSpecificStuffs(){
		$skill_id = $this->input->post("skill_id");
		$template = $this->input->post("template");
		$step_id = $this->input->post("step_id");

		$array = array();
		if($step_id>0){
			$getsteps = $this->fetchExposureStepDetailsByStepID($step_id);
			$array["steps"] = $getsteps;
			$this->db->freeDBResource();
			unset($query);

			if($template=="step_ec_words" || $template=="step_ec_sentences"){ //then fetch answer categories
				$query= $this->db->query("SELECT * FROM bip_v2_skill_exposure_answer_category WHERE step_id=? AND answer_cat_status='1' ORDER BY sort_order ASC",array($step_id));
				$array["answer_categories"] = $query->result();
				$this->db->freeDBResource();
				unset($query);

				$query= $this->db->query("SELECT * FROM bip_v2_skill_exposure_answers WHERE step_id = ? AND answer_status='1' ORDER BY sort_order ASC", array($step_id));
				$array["answers"] = $query->result();
				$this->db->freeDBResource();
				unset($query);
			}else if($template=="step_ec_descriptions"){
				$query = $this->db->query("SELECT c.*, a.answer_id, a.answer FROM bip_v2_skill_exposure_answer_category c INNER JOIN bip_v2_skill_exposure_answers a ON a.answer_cat_id=c.answer_cat_id
										  WHERE c.step_id=? AND c.answer_cat_status='1' ORDER BY c.sort_order",array($step_id));
				$array["answer_categories"] = $query->result();
				$this->db->freeDBResource();
				unset($query);
			}else if($template=="step_keywords" || $template=="step_sentences"){
				$query= $this->db->query("SELECT * FROM bip_v2_skill_exposure_answers WHERE step_id = ? AND answer_status='1' ORDER BY sort_order ASC", array($step_id));
				$array["answers"] = $query->result();
				$this->db->freeDBResource();
				unset($query);
			}else{
				$query= $this->db->query("SELECT * FROM bip_v2_skill_exposure_answers WHERE step_id = ? AND answer_status='1' ORDER BY sort_order ASC", array($step_id));
				$array["answers"] = $query->result();
				$this->db->freeDBResource();
				unset($query);
			}
			//now get answers

		}
		return $array;
	}


	function removeExposureStepAnswerCat(){
		$cat_id = $this->input->post("cat_id");
		$this->db->query("UPDATE bip_v2_skill_exposure_answer_category SET answer_cat_status=? WHERE answer_cat_id =?",array($this->input->post("dowhat"),$cat_id));
		if($this->db->affected_rows()>0){
			$this->db->query("UPDATE  bip_v2_skill_exposure_answers SET answer_status=? WHERE answer_cat_id=?",array($this->input->post("dowhat"),$cat_id));
			echo "success";
		}else{
			echo "failed";
		}
	}


	 function getExposureAnswersByAnswerCat($step_id,$cat_id){
		$query = $this->db->query("SELECT * FROM bip_v2_skill_exposure_answers WHERE step_id=? AND answer_cat_id=? AND answer_status='1' ORDER BY sort_order ASC",array($step_id,$cat_id));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}


	function isExposureAnswerOrCatInUse($id, $type="answer"){

		//UPDATE THIS METHOD ONCE WORKED ON APP PART. CHECK WHETHER THE EXPOSURE ANSWER IS USED  WHILE ANSWERING.
		return false;

	}


	 function saveExposureAnswerCategoryForDescription(){
		$current_date = date("Y-m-d H:i:s");
		$array = array();
		extract($this->input->post());
	   /* $difficulty_ids = $this->getDifficultyIDbyRegID($registration_id);
		$patients = $this->getAllusersByDifficultyID($difficulty_ids);*/
		$template_name = $template;

		if(!$step_id>0){ //insert steps as well
			$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_v2_sk_exposure_steps WHERE skill_id= ?",array($skill_id))->row();
			$neworder =  $getmax->max_sort_order+1;
			$array["sort_order"] = $neworder;
			$array["new_step"]= 1;
			$step_title = addslashes(htmlspecialchars($step_title));

			$this->db->query("INSERT INTO bip_v2_sk_exposure_steps SET skill_type='exposure',step_name=?,skill_id=?,template=?,added_date='$current_date',sort_order='$neworder',alternate_text=?",array($step_title,$skill_id,$template_name,$alternate_text));
			$insert_id = $this->db->insert_id();

			if($insert_id>0){ //now save category
				$step_answer_cat = addslashes(htmlspecialchars($step_answer_cat));
				$this->db->query("INSERT INTO bip_v2_skill_exposure_answer_category SET answer_cat_name=?, step_id='$insert_id',added_date='$current_date',sort_order='1',answer_type='standard',created_by=?, added_by='admin', belongs_to='0', answer_cat_status='1'",array($step_answer_cat, $this->session->userdata("user_id")));
				$array["cat_id"] = $this->db->insert_id();
				$array["cat_sort_order"]=1;

				//now save category contents.
				$this->db->query("INSERT INTO bip_v2_skill_exposure_answers SET answer=?, step_id='$insert_id', answer_cat_id='".$array["cat_id"]."', added_date='$current_date', answer_status='1',answer_type='standard',created_by=?,belongs_to='0',added_by='admin'",
								array($step_answer_content,$this->session->userdata("user_id")));
				$array["answer_id"] = $this->db->insert_id();
			}
		}else{
			$insert_id = $step_id;
			$getmaxCat = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_v2_skill_exposure_answer_category WHERE step_id= ?",array($insert_id))->row();
			$new_cat_order =  $getmaxCat->max_sort_order+1;

			$step_answer_cat = addslashes(htmlspecialchars($step_answer_cat));
			$this->db->query("INSERT INTO bip_v2_skill_exposure_answer_category SET answer_cat_name=?, step_id=?,added_date='$current_date',sort_order='$new_cat_order',answer_type='standard',created_by=?, added_by='admin', belongs_to='0', answer_cat_status='1'",array($step_answer_cat,$insert_id,$this->session->userdata("user_id")));
			$array["cat_id"] = $this->db->insert_id();
			$array["new_step"]= 0;
			$array["cat_sort_order"] = $new_cat_order;

			//now save category contents.
				$this->db->query("INSERT INTO bip_v2_skill_exposure_answers SET answer=?, step_id='$insert_id', answer_cat_id='".$array["cat_id"]."', added_date='$current_date', answer_status='1',answer_type='standard',created_by=?,belongs_to='0',added_by='admin'",
								array($step_answer_content,$this->session->userdata("user_id")));
				$array["answer_id"] = $this->db->insert_id();

		}
		$array["step_id"] = $insert_id;
		$array["skill_id"] = $skill_id;
		$array["template"] = $template_name;
		$array["template_name"] = lang($template_name);
		echo json_encode($array);
		exit;
	}

	function fetchPatientsExposure($patientID, $difficultyID){
			$fetchSkillID = $this->db->query("SELECT skill_id FROM bip_v2_skills WHERE skill_type='exposure' AND module_id IN (SELECT module_id FROM bip_v2_modules WHERE FIND_IN_SET(?,difficulty_id))
", array($difficultyID))->row();
			$skill_id = $fetchSkillID->skill_id;
			$this->db->freeDBResource();

			$query = $this->db->query("SELECT p.*, (SELECT COUNT(*) FROM bip_v2_sk_exposure_patients_assignments WHERE exposure_id=p.exposure_id) as no_of_exposures FROM bip_v2_sk_exposure_patients p WHERE p.belongs_to=? AND p.skill_id=? AND p.exposure_status!=0", array($patientID,$skill_id));
			$result = $query->result();
			$this->db->freeDBResource();
			return $result;
	}


	function addNewPatientExposure(){
		//CHECK IF EXPOSURE WITH SAME NAME EXIST
		extract($this->input->post());
		$array = array();
		$exposure_name = preg_replace('/\s+/', ' ',trim($exposure_name));
		$check = $this->db->query("SELECT COUNT(*) as recount FROM bip_v2_sk_exposure_patients WHERE exposure_name=? AND belongs_to=?", array($exposure_name,$user_id))->row();
		if($check->recount>0){
			$array["status"] = "error";
			$array["error_message"] = lang("txt_exposure_exist");
		}else{
			//fetch skill id
			$fetchSkillID = $this->db->query("SELECT skill_id FROM bip_v2_skills WHERE skill_status='1' AND skill_type='exposure' AND module_id IN (SELECT module_id FROM bip_v2_modules WHERE FIND_IN_SET(?,difficulty_id))
	", array($difficulty_id))->row();

			$skill_id = $fetchSkillID->skill_id;
			$this->db->freeDBResource();
			if(!$skill_id>0){ //if there is no active eposure template simple show them message
				$array["status"] = "error";
				$array["error_message"] = "No active exposure template found.";
				echo json_encode($array);
				exit;
			}

			$current_date = date("Y-m-d H:i:s");


		   if($this->session->userdata("user_role_type")=="psychologist"){
				$added_by = "psychologist";
		   }else{
				$added_by = "patient";
		   }

			$ins = $this->db->query("INSERT INTO bip_v2_sk_exposure_patients SET
								exposure_name         =?,
								skill_id              =?,
								started_date          = '$current_date',
								added_date            = '$current_date',
								exposure_status       ='1',
								added_by              = '$added_by',
								added_by_id           = ?,
								belongs_to            = ?
								", array($exposure_name, $skill_id, $this->session->userdata("user_id"), $user_id));

			if($ins){
				$exp_id = $this->db->insert_id();

				$array["status"] = "ok";
				$array["returnvar"]["exposure_name"] = $exposure_name;
				$array["returnvar"]["exposure_id"]   = $exp_id;
				$array["returnvar"]["start_date"] = date("Y-m-d",strtotime($current_date));
				$array["returnvar"]["status"] = lang("txt_ongoing");
				 $array["returnvar"]["complete"] = lang("txt_complete");
				 $array["returnvar"]["remove"] = lang("remove");
			}else{
				$array["status"] = "error";
				$array["error_message"] = lang("txt_tryagain_later");
			}
		}


		echo json_encode($array);
		exit;
	}

	function removePatientExposure(){
		$array = array();
		$del = $this->db->query("UPDATE bip_v2_sk_exposure_patients SET exposure_status='0' WHERE exposure_id=?", array($this->input->post("exposure_id")));

		if($del){
			$array["status"] = "ok";
			$array["error_message"] = "";
		}else{
			$array["status"] = "error";
			$array["error_message"] = lang("txt_tryagain_later");
		}

		echo json_encode($array);
		exit;
	}

	function completePatientExposure(){
		 $array = array();
		$current_date = date("Y-m-d H:i:s");

		$complete = $this->db->query("UPDATE bip_v2_sk_exposure_patients SET exposure_status='2', closed_date='$current_date' WHERE exposure_id=?", array($this->input->post("exposure_id")));

		if($complete){
			$array["status"] = "ok";
			$array["closed_date"] = date("Y-m-d", strtotime($current_date));
			$array["new_status"] = lang("txt_completed");
			$array["error_message"] = "";
		}else{
			$array["status"] = "error";
			$array["new_status"] = lang("txt_ongoing");
			$array["error_message"] = lang("txt_tryagain_later");
		}

		echo json_encode($array);
		exit;
	}


	function checkIfTreatmentHasExposure($difficulty_id){
		$query = $this->db->query("SELECT
				  s.module_id,
				  s.skill_id,
				  COUNT(*) exposure_count,
				  (SELECT
					COUNT(*)
				  FROM
					`bip_v2_sk_exposure_steps`
				  WHERE module_id = s.module_id
					AND skill_id) AS step_count
				FROM
				  `bip_v2_skills` s
				WHERE s.skill_type = 'exposure'
				  AND s.skill_status='1'
				  AND s.module_id IN
				  (SELECT
					module_id
				  FROM
					`bip_v2_modules`
				  WHERE difficulty_id = ?)", array($difficulty_id));
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}
	/*Added by Sabin @12th August 2015 <<*/

	/**
	 * fetch registration by difficulty id
	 * @param  int $difficulty_id
	 * @return object
	 */
	function getRegistrationByDifficultId($difficulty_id){
		$likeString = '%,'.$difficulty_id.',%';
		$query=$this->db->query("SELECT registration_id as id,registration_name as name FROM bip_registration_task WHERE (CONCAT(',' , difficulty_id , ',') LIKE ?)",array($likeString));

		$result = $query->result();
		return $result;
	}

	/**
	 * fetch homework by difficulty id
	 * @param  int $difficulty_id
	 * @return object
	 */
	function getHomeworkByDifficultyId($difficulty_id){
		$likeString = '%,'.$difficulty_id.',%';
		$query=$this->db->query("SELECT homework_id as id,headline as name FROM bip_my_homework WHERE (CONCAT(',' , difficulty_id , ',') LIKE ?)",array($likeString));

		$result = $query->result();
		return $result;
	}

	/**
	 * fetch skills modules by difficulty id
	 * @param  int $difficulty_id
	 * @return object
	 */
	function getSkillsModulesByDifficultyId($difficulty_id){
		$query = $this->db->query("SELECT module_id as id,module_name as name FROM bip_v2_modules WHERE difficulty_id=? AND module_status=1 ORDER BY sort_order ASC",array($difficulty_id));

		$result = $query->result();
		return $result;
	}

	/**
	 * fetch skills feelings (default module) by difficulty id
	 * @param  int $difficulty_id
	 * @return object
	 */
	function getSkillsFeelingsByDifficultyId($difficulty_id){
		$query = $this->db->query("SELECT feeling_id as id,feeling_name as name FROM bip_v2_feelings WHERE difficulty_id=? AND feeling_status=1",array($difficulty_id));

		$result = $query->result();
		return $result;
	}

	 /*Added by sabin @29th September 2015 >> */
	 function getActivityThoughts($userid){
		$query = $this->db->query("SELECT t.headline, t.thought_type, COUNT(*) AS times_used  FROM bip_v2_sk_thoughts_assignments ta INNER JOIN bip_v2_sk_thoughts t ON t.thought_id = ta.thought_id WHERE ta.patient_id=? GROUP BY ta.thought_id", array($userid));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	 }

	 function getActivityFeelings($userid){
		$result = array();
		 $stats = $this->db->query("SELECT
(SELECT COUNT(*) FROM bip_v2_feelings_assignments fa WHERE fa.patient_id=?) AS total_feelings,
(SELECT COUNT(*) AS no_of_days FROM (SELECT DATE_FORMAT(answered_date,'%Y-%m-%d') AS fdate, COUNT(*) FROM bip_v2_feelings_assignments WHERE patient_id=?  GROUP BY fdate) AS d) AS total_days,
(SELECT COUNT(*) FROM bip_v2_feelings_assignments fa WHERE fa.patient_id=? AND feeling_type='1') AS total_primary_Feelings,
(SELECT COUNT(*) FROM bip_v2_feelings_assignments fa WHERE fa.patient_id=? AND feeling_type='2') AS total_secondary_Feelings",array($userid,$userid,$userid,$userid))->row();
		$result["stats"] = $stats;
		$this->db->freeDBResource();

		$query = $this->db->query("SELECT fa.feeling_type, f.feeling_name,DATE_FORMAT(answered_date,'%Y-%m-%d') AS answered_date FROM bip_v2_feelings_assignments fa INNER JOIN bip_v2_feelings f ON f.feeling_id = fa.feeling_id WHERE fa.patient_id=? ORDER BY answered_date DESC", array($userid));
		$result["lists"] = $query->result();
		$this->db->freeDBResource();
		return $result;
	 }

	 function getActivitySkills($userid){
		$query = $this->db->query("SELECT s.skill_name,a.skill_id, (SELECT module_id FROM bip_v2_skills WHERE skill_id=a.skill_id) AS module_id, COUNT(*) AS occurrences FROM bip_v2_sk_skills_assignments a INNER JOIN bip_v2_skills s ON s.skill_id = a.skill_id WHERE  a.patient_id=? GROUP BY a.skill_id ORDER BY module_id DESC,occurrences DESC
", array($userid));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	 }

	function saveOneTimeCode($data){
		extract($data);
		$date = date("Y-m-d H:i:s");
		$checkIfExist = $this->db->query("SELECT COUNT(*) as reccount FROM bip_user_activation_codes WHERE user_id=?", array($patient_id))->row();
		if($checkIfExist->reccount>0){ //Exist so update
			$ins = $this->db->query("UPDATE  bip_user_activation_codes SET
										activation_code = ?,
										device_uuid = '',
										activated_date='',
										generated_date = '$date',
										code_used = '0',
										device_type = '',
										is_activated='0' WHERE user_id=?
									", array($gen_code,$patient_id));
		}else{
			$ins = $this->db->query("INSERT INTO bip_user_activation_codes SET
										user_id = ?,
										activation_code = ?,
										generated_date = '$date',
										code_used = '0',
										is_activated='0'
									", array($patient_id,$gen_code));
		}
	}

	function checkRegistrationInUse($regID){
		$query = $this->db->query("SELECT COUNT(*) recCount FROM bip_registration_assignments WHERE registration_id=?",array($regID))->row();
		if($query->recCount>0){
			return true;
		}else{
			return false;
		}
	}

	function checkRegStepInUse($stepID){
		$query = $this->db->query("SELECT COUNT(*) recCount FROM bip_registration_assignments_details WHERE step_id=?",array($stepID))->row();
		if($query->recCount>0){
			return true;
		}else{
			return false;
		}
	}


	function checkIfSkillInUse($skillID){
		$query = $this->db->query("SELECT (SELECT COUNT(*) FROM `bip_v2_sk_skills_assignments` WHERE skill_id=?) AS count_skills,
(SELECT COUNT(*) FROM `bip_v2_sk_thoughts_assignments` WHERE skill_id=?) AS count_thoughts", array($skillID,$skillID))->row();
	   // echo $this->db->last_query();
		if($query->count_skills>0 || $query->count_thoughts>0){
			return true;
		}else{
			return false;
		}
	}

	function isFeelingInUse($feelingID)
	{
		$query = $this->db->query("SELECT COUNT(*) recCount FROM bip_v2_feelings_assignments WHERE feeling_id=?",array($feelingID))->row();
		if($query->recCount>0){
			return true;
		}else{
			return false;
		}
	}

	function sortFeelings(){
	   extract($this->input->post());
	   for($k = 0; $k<count($ID); $k++){
			$od = $offset+$k+1;
			$this->db->query("UPDATE bip_v2_feelings SET sort_order='".$od."' WHERE feeling_id=?",array($ID[$k]));
	   }
	   echo "success";
	   exit;
	}

	function chkIfModuleHasSkills($modID){
		 $query = $this->db->query("SELECT COUNT(*) recCount FROM bip_v2_skills WHERE module_id=?",array($modID))->row();
		if($query->recCount>0){
			return true;
		}else{
			return false;
		}
	}

	function sortHomeworks(){
	   extract($this->input->post());
	   for($k = 0; $k<count($ID); $k++){
			$od = $offset+$k+1;
			$this->db->query("UPDATE bip_my_homework SET sort_order='".$od."' WHERE homework_id=?",array($ID[$k]));
	   }
	   echo "success";
	   exit;
	}

	function getTagByDifficultyID($diffID){
		 $query = $this->db->query("SELECT tag FROM bip_difficulty WHERE id=?",array($diffID))->row();
		return $query->tag;
	}


	function isHomeworkInUse($hwID){
		$query = $this->db->query("SELECT COUNT(*) recCount FROM bip_my_homework_assignment WHERE homework_id=?",array($hwID))->row();
		if($query->recCount>0){
			return true;
		}else{
			return false;
		}
	}

	function checkifAlreadyActivated($patientID){
		$query = $this->db->query("SELECT COUNT(*) recCount FROM bip_user_activation_codes WHERE user_id=? AND is_activated='1'",array($patientID))->row();
	   // echo $this->db->last_query();
		if($query->recCount>0){
			return "yes";
		}else{
			return "no";
		}
	}

	function patientsHomework($userID){
		$query = $this->db->query("SELECT hw.homework_id FROM bip_my_homework hw INNER JOIN bip_my_homework_assignment hwa ON hwa.homework_id=hw.homework_id WHERE hwa.is_published='1' AND hwa.patient_id=?", array($userID));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}
	/*Added by sabin @29th September 2015 << */

	function countActiveTasksToNotify($user){
		$userId = $user->user_id;
		$query = $this->db->query("SELECT id,task,completed from bip_tasks WHERE CONCAT(',' , user_id , ',') LIKE '%,$userId,%' and completed not like '%$userId%'");

	  $result = $query->result();
	  $count = $query->num_rows();

	  return $query->num_rows();;

	}

	function isNotificationEnabled($userid)
	{
		$query=$this->db->query("SELECT notification_enabled FROM bip_user WHERE id=?", array($userid));
		$row=$query->row_array();
		$returnArr =array();

		if($row["notification_enabled"]==1){
			$returnArr["new_n_st"] = 0;
			$returnArr["new_n_msg"] = lang("txt_notification_disable");
			$returnArr["new_n_label"] = lang("txt_noti_label_disable");
			$returnArr["new_n_class"] = 0;
		}else{
			$returnArr["new_n_st"] = 1;
			$returnArr["new_n_msg"] = lang("txt_notification_enable");
			$returnArr["new_n_label"] = lang("txt_noti_label_enable");
			$returnArr["new_n_class"] = 1;
		}
		$returnArr["status"] = "ok";
		return $returnArr;
	}

	function enableDisableReminder($data){
		extract($data);
		$update = $this->db->query("UPDATE bip_user SET notification_enabled=? WHERE id=?", array($dowhat,$patient_id));

		if($update){
		   return $this->isNotificationEnabled($patient_id);
		}else{
		   $retArray = array();
		   $retArray["status"]="error";
		   return $retArray;
		}
	}

	function deleteSpecialAnswer($answerID){
		$retArray = array();
		$update = $this->db->query("UPDATE bip_registration_answers SET answer_status='0' WHERE answer_id=?", array($answerID));

		if($update){
			$retArray["status"]="ok";
			$retArray["message"] = "";
		}else{
			$retArray["status"]="error";
			$retArray["message"] = "Error deleting the answer";
		}

		return json_encode($retArray);
	}

	function getTicV2DataforXLS($userid){

		$query = $this->db->query("SELECT t.*, (SELECT sp.title FROM bip_step sp INNER JOIN bip_stage sg ON sp.stage_id=sg.id WHERE sp.id = t.step_id) AS step_title, (SELECT sg.stage_title FROM bip_step sp INNER JOIN bip_stage sg ON sp.stage_id=sg.id WHERE sp.id = t.step_id) AS stage_title, l.level_name FROM `bip_tics_v2` t INNER JOIN bip_tics_v2_level l ON l.level_id=t.level_id WHERE t.user_id=? ORDER BY t.recorded_date DESC, t.recorded_time_in_seconds DESC", array($userid));
		return $query->result();
	}

	function getTicV1DataforXLS($userid){
		$query = $this->db->query("SELECT t.*, (SELECT sp.title FROM bip_step sp INNER JOIN bip_stage sg ON sp.stage_id=sg.id WHERE sp.id = t.step_id) AS step_title, (SELECT sg.stage_title FROM bip_step sp INNER JOIN bip_stage sg ON sp.stage_id=sg.id WHERE sp.id = t.step_id) AS stage_title, l.level_name FROM `bip_tics_v1` t INNER JOIN bip_tics_v1_level l ON l.level_id=t.level_id WHERE t.user_id=?  ORDER BY t.recorded_date DESC, t.recorded_time_in_seconds DESC", array($userid));
		return $query->result();
	}

	function getV1RatingsForxls($tic_id){
		$query = $this->db->query("SELECT rating_score, rated_interval, rating_type, is_stop_rating FROM bip_tics_v1_ratings WHERE tic_id=? AND rating_type<>'1' AND is_stop_rating<>'1' ORDER by cast(rated_interval as unsigned) ASC", array($tic_id));
		//fetch all ratings except the overall one;
		return $query->result();
	}

	function getV1overallTicRating($tic_id){
		$query = $this->db->query("SELECT rating_score FROM `bip_tics_v1_ratings` WHERE tic_id=? AND (rating_type=1 OR (rating_type=3 AND is_stop_rating=1))",array($tic_id))->row();

		return $query->rating_score>0 ? $query->rating_score : "-";
	}

	function getMaxNumberofIntervalsTicsV1($userid){
		$query = $this->db->query("SELECT MAX(cnt) as max_interval FROM (SELECT COUNT(*) AS cnt FROM `bip_tics_v1_ratings` WHERE user_id=? AND rating_type<>'1' AND is_stop_rating='0' GROUP BY tic_id ORDER BY cnt DESC) tr", array($userid))->row();
		return $query->max_interval;
	}

}
/* End of file minapp_model.php */
/* Location: ./application/modules/minapp/models/minapp_model.php */
