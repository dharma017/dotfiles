<?php

class user_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function getAllUser($offset=0, $datalimit=50, $orderBy='', $psychologistid) {  // order by is no more  used in query. it is feed by session

		$language_code = $this->session->userdata('language_code');

		$psychologistid = ($psychologistid == '') ? 0 : $psychologistid;



		$order_by = $this->session->userdata('userOrderBy');
		$alter_by = $this->session->userdata('AlterBy');
		$search_txt = $this->session->userdata('userSearch') ;
		$permission = $this->session->userdata('permission');


		$userID = $this->session->userdata('user_id');
		$created_by = $this->db->query("select created_by from bip_user where created_by='$userID'")->row();
		$created_by =  $created_by->created_by;
		$orderBy = $order_by . ' ' . $alter_by;

		 if (!trim($orderBy))
			$orderBy = "first_name asc ";

		$strSql = 'SELECT  SQL_CALC_FOUND_ROWS b.*, DATEDIFF(b.last_login,b.first_login) as days, DATEDIFF(b.active_to,now()) as active_remaining_day,tg.group_name,bd.difficulty,bu.first_name as psychologist from bip_user b
						left join bip_group tg on b.group_id = tg.id
						left join bip_difficulty bd on b.difficulty_id = bd.id
						left join bip_user bu on b.psychologist_id = bu.id WHERE 1=1 ';

		$difficulties = '';

		$psychlogist_sql = '';
		if (!empty($permission) )
		{
			$psychlogistId = $this->session->userdata('user_id');
			//  echo $psychlogistId;exit;
			if(!empty($psychlogistId))
				$psychlogist_sql .= ' AND (( b.psychologist_id=' . $psychlogistId.' ) || ( 1 = 1 ';
			else
				$psychlogist_sql .= ' AND (( 1 = 1 ';


			if(is_array($permission->diff_multi) &&  count($permission->diff_multi) > 0)
			{
				if(!empty($permission->diff_multi) && is_array($permission->diff_multi))
				 $difficulties = implode(',',$permission->diff_multi);

				$difficulties = rtrim($difficulties,",");

				if($permission->psycho_manage == 1)
				{
					if(!empty($difficulties))
						$psychlogist_sql .= ' AND (b.difficulty_id in (' . $difficulties.') || b.user_role = 2) ';
					else
						$psychlogist_sql .= ' AND b.user_role =2 ';
				}
				else
					if(!empty($difficulties))
						$psychlogist_sql .= ' AND b.difficulty_id in (' . $difficulties.') ';
					else
						$psychlogist_sql .= ' AND 0 = 1 ';
			}
			else
				$psychlogist_sql .= ' AND 0 = 1 ';

			$psychlogist_sql .= ' )) ';

		}

		$strSql .= $psychlogist_sql;

		if ($search_txt) {
		   $strSql .= ' AND (b.username like "%' . $search_txt . '%" OR  b.first_name like "%' . $search_txt . '%"  OR b.last_name like "%' . $search_txt . '%"  OR
							bu.username like "%' . $search_txt . '%" OR  bu.first_name like "%' . $search_txt . '%"  OR bu.last_name like "%' . $search_txt . '%"  OR
							bd.difficulty like "%' . $search_txt . '%"  OR b.address like "%' . $search_txt . '%"  OR b.city like "%' . $search_txt . '%"  OR LOWER(tg.group_name)  like LOWER("%' . $search_txt . '%") )';
		}
		$strSql .= ' AND b.is_deleted=0 AND b.lang_id= '.$language_code.' ORDER BY ' . $orderBy . ' LIMIT  ' . $offset . ',' . $datalimit;


		$query = $this->db->query($strSql);
		//echo $this->db->last_query();
		$result1 = $query->result();
		$this->db->freeDBResource();

		$query1 = $this->db->query("SELECT FOUND_ROWS() as totalRows");
		$row1 = $query1->row();
		$this->db->freeDBResource();
		$totalRows = $row1->totalRows;
		$result = array($result1, $totalRows);
		return $result;
	}

	function getAllUser222($offset=0, $datalimit=50, $orderBy) {
		$logintype = $this->session->userdata('logintype');
		$usertype = $this->session->userdata('user_role_type');
		$psychologist_id = $this->input->post('psychologist_id');
		$difficulty_id = $this->input->post('difficulty_id');
		$group_id = $this->input->post('group_id');
		//$this->db->freeDBResource();
		$language_code = $this->session->userdata('language_code');
		$order_by = $this->session->userdata('userOrderBy');
		$alter_by = $this->session->userdata('AlterBy');
		$search_txt = $this->input->post('search_txt') ;
		$permission = $this->session->userdata('permission');
			$permission=json_encode($permission);
			$permission=json_decode($permission,true);
		$orderBy = $order_by . ' ' . $alter_by;
		 if (!trim($orderBy))
			$orderBy = "first_name asc ";
		$where = '(';
		$whereArr = array();
		if (!empty($permission)) {
			$psychlogistId = $this->session->userdata('user_id');
				$indexes=[];
				foreach ($permission['rights_per_group'] as $key => $diff_data) {
					$indexes[]=$key;
				}
			//  echo $psychlogistId;exit;
				if(!empty($indexes) && is_array($indexes))
					$grp_multi = join(',',$indexes);
				$grp_multi = rtrim($grp_multi,",");
				$managepsychogrpids=[];
				$manageusergrpids=[];
				if($grp_multi){
					foreach ($permission['rights_per_group'] as $key => $diff_data) {
						if($permission['rights_per_group'][$key]['manage_psychologists'] == 1)

						{
								if($where!="("){
									$where.=" OR ";
								}
								// $where .= "(( b.user_role = 2) and   find_in_set('$key', cast(b.group_id as char)) > 0 )   ";
								$where .= "(( b.user_role = 2))   ";
								$managepsychogrpids[]=$key;
						}
						if($permission['rights_per_group'][$key]['manage_users'] == 1){
							if($where!="("){
									$where.=" OR ";
								}
							$where .= "((b.user_role = 1) and   find_in_set('$key', cast(b.group_id as char)) > 0 )   ";
							$manageusergrpids[]=$key;
						}
						if($permission['rights_per_group'][$key]['treat_patients'] == 1){
							if($where!="("){
									$where.=" OR ";
								}
							$where .= "((b.user_role = 1 and b.psychologist_id=".$psychlogistId.") and find_in_set('$key', cast(b.group_id as char)) > 0 )   ";
						}

					}
			}
		}
		if($where=="("){
			$where="";
		}else{
			$where.=" OR b.id = ".$psychlogistId.") and";
		}
		$where .= "  b.is_deleted=0 AND b.lang_id='$language_code'";
		if ($psychologist_id || $difficulty_id || $group_id) {
			if ($psychologist_id){
			 	$where1 = " (b.psychologist_id='$psychologist_id' OR b.id='$psychologist_id')";
				array_push($whereArr, $where1);
			}
			if ($difficulty_id && !is_array($difficulty_id)){
			 $where2 = "  b.difficulty_id='$difficulty_id'";
				array_push($whereArr, $where2);
			}

			 if ($group_id && !is_array($group_id)){

			 	$where3 = "  b.group_id='$group_id'";
				array_push($whereArr, $where3);
			}
			if(sizeof($whereArr)>0){
				$where .=" and ";
			}
			$where .= implode(' AND ', $whereArr);
		}
			$strSql = "SELECT  SQL_CALC_FOUND_ROWS b.*, DATEDIFF(b.last_login,b.first_login) as days, DATEDIFF(b.active_to,now()) as active_remaining_day,tg.group_name,bd.difficulty,bu.first_name as psychologist from bip_user b
			left join bip_group tg on b.group_id = tg.id
			left join bip_difficulty bd on b.difficulty_id = bd.id
			left join bip_user bu on b.psychologist_id = bu.id  WHERE $where";
			$difficulties = '';
			if ($search_txt) {
				$strSql .= ' AND (b.username like "%' . $search_txt . '%" OR  b.first_name like "%' . $search_txt . '%"  OR b.last_name like "%' . $search_txt . '%"  OR
				bu.username like "%' . $search_txt . '%" OR  bu.first_name like "%' . $search_txt . '%"  OR bu.last_name like "%' . $search_txt . '%"  OR
				bd.difficulty like "%' . $search_txt . '%"  OR b.address like "%' . $search_txt . '%"  OR b.city like "%' . $search_txt . '%"  OR LOWER(tg.group_name)  like LOWER("%' . $search_txt . '%") )';
			}
			$strSql .=" order by $orderBy LIMIT $offset, $datalimit ";
			$query = $this->db->query($strSql);
			if($grp_multi || ($usertype == "superadmin" && $logintype == "admin") ){
				$result1 = $query->result();
			}else{
				$result1=new stdClass;
			}
			//echo $this->db->last_query();
			$result1=json_decode(json_encode($result1),true);
			foreach ($result1 as $key => $res) {
				if($res['user_role']==2){
						$groupid=$res['group_id'];
						$grparr=explode(",", $groupid);
						$check=false;
						// $groupid=$this->getAllGroupByLang();
						// $grparr=explode(",", $groupid->id);
						/*foreach($grparr as $g){
							if(in_array($g, $managepsychogrpids)){
								$check=true;
								break;
							}
						}*/
						if($managepsychogrpids){
							foreach($grparr as $g){
								$check=true;
								break;
							}
						}
						if($check==true)
							$result1[$key]['editpermission']=true;
						else
							$result1[$key]['editpermission']=false;

				}else if($res['user_role']==1){
						$groupid=$res['group_id'];
						// echo $groupid;
						// print_r($manageusergrpids);
						// echo "<br />";
						if(in_array($groupid,$manageusergrpids))
							$result1[$key]['editpermission']=true;
						else
							$result1[$key]['editpermission']=false;
				}else{
					$result1[$key]['editpermission']=true;

				}
			}
			$result1=json_decode(json_encode($result1));
			$this->db->freeDBResource();

			if($grp_multi || ($usertype == "superadmin" && $logintype == "admin")){
				$query1 = $this->db->query("SELECT FOUND_ROWS() as totalRows");
				$row1 = $query1->row();
				$this->db->freeDBResource();
				$totalRows = $row1->totalRows;
			}else{
				$totalRows=0;
			}
			$result = array($result1, $totalRows);
			return $result;
		}
		function getPsyInfo(){
			$user_id = $this->session->userdata('user_id');
			$query = $this->db->query("select * from bip_user where id='$user_id'")->row();
			return $query;
		}
		function getAllUserOfPsy($offset=0, $datalimit=50, $orderBy) {
			$psychologist_id = $this->input->post('psychologist_id');
			$difficulty_id = $this->input->post('difficulty_id');
			$group_id = $this->input->post('group_id');
			$language_code = $this->session->userdata('language_code');
			$order_by = $this->session->userdata('userOrderBy');
			$alter_by = $this->session->userdata('AlterBy');
			$search_txt = $this->input->post('search_txt') ;
			$permission = $this->session->userdata('permission');
			$permission=json_encode($permission);
			$permission=json_decode($permission,true);
			$orderBy = $order_by . ' ' . $alter_by;
			if (!trim($orderBy))
				$orderBy = "first_name asc ";
			$where = '';
			$whereArr = array();
	//echo '<pre>';print_r($permission);exit();
			if (!empty($permission)) {
				$psychlogistId = $this->session->userdata('user_id');
				$indexes=[];
				foreach ($permission['rights_per_group'] as $key => $grp_data) {
					if($grp_data['manage_psychologists'] == 1){
						$indexes1[]=$key;
					}else{
						$indexes2[] = $key;
					}

				}

				if(!empty($indexes1) && is_array($indexes1))
					$grp_multi1 = join(',',$indexes1);
				$grp_multi1 = rtrim($grp_multi1,",");
				if(!empty($indexes2) && is_array($indexes2))
					$grp_multi2 = join(',',$indexes2);
				$grp_multi2 = rtrim($grp_multi2,",");
				if($grp_multi1)
				{
					$where .= "(b.group_id IN ($grp_multi1) AND b.psychologist_id = '$psychlogistId' || b.created_by ='$psychlogistId' || b.user_role = 2) AND ";
				}elseif($grp_multi2){
					$where .= "(b.group_id IN ($grp_multi2) AND b.psychologist_id = '$psychlogistId' || b.id = $psychlogistId) AND ";
				}
			}
			$where .= " b.is_deleted=0 AND b.lang_id='$language_code'";
			if ($psychologist_id || $difficulty_id || $group_id) {
		$where .= " AND ";
				if ($psychologist_id){
					$where1 = " (b.psychologist_id='$psychologist_id' OR b.id='$psychologist_id' OR b.created_by='$psychologist_id')";
					array_push($whereArr, $where1);
				}
		if ($difficulty_id){
		$where2 = "  b.difficulty_id='$difficulty_id'";
		array_push($whereArr, $where2);
		}
		if ($group_id){
		$where3 = "  b.group_id='$group_id'";
		array_push($whereArr, $where3);
	}
	$where .= implode(' AND ', $whereArr);
}
		$strSql = "SELECT  SQL_CALC_FOUND_ROWS b.*, DATEDIFF(b.last_login,b.first_login) as days, DATEDIFF(b.active_to,now()) as active_remaining_day,tg.group_name,bd.difficulty,bu.first_name as psychologist from bip_user b
			left join bip_group tg on b.group_id = tg.id
			left join bip_difficulty bd on b.difficulty_id = bd.id
			left join bip_user bu on b.psychologist_id = bu.id  WHERE $where";
			$difficulties = '';
		if ($search_txt) {
		   $strSql .= ' AND (b.username like "%' . $search_txt . '%" OR  b.first_name like "%' . $search_txt . '%"  OR b.last_name like "%' . $search_txt . '%"  OR
							bu.username like "%' . $search_txt . '%" OR  bu.first_name like "%' . $search_txt . '%"  OR bu.last_name like "%' . $search_txt . '%"  OR
							bd.difficulty like "%' . $search_txt . '%"  OR b.address like "%' . $search_txt . '%"  OR b.city like "%' . $search_txt . '%"  OR LOWER(tg.group_name)  like LOWER("%' . $search_txt . '%") )';
		}
		 $strSql .=" order by $orderBy LIMIT $offset, $datalimit ";
		$query = $this->db->query($strSql);
		$result1 = $query->result();
		//echo $this->db->last_query();
		$this->db->freeDBResource();
		$query1 = $this->db->query("SELECT FOUND_ROWS() as totalRows");
		$row1 = $query1->row();
		$this->db->freeDBResource();
		$totalRows = $row1->totalRows;
		$result = array($result1, $totalRows);
		return $result;
	}

	function getUserSelectList(){

			$permission = $this->session->userdata('permission');

	$permission=json_decode(json_encode($permission),true);
	foreach ($permission['rights_per_difficulty'] as $key => $diff_data) {
		$indexes[]=$key;
	}
	if(!empty($indexes) && is_array($indexes))
		$diff_multi = join(',',$indexes);
			$diff_multi = rtrim($diff_multi,",");

		$query = $this->db->query("SELECT b.difficulty_id,
						       bd.difficulty,
						       b.psychologist_id,
						       b.first_name,
						       b.last_name,
						       b.group_id,
						       tg.group_name
						FROM bip_user b
						LEFT JOIN bip_group tg ON b.group_id = tg.id
						LEFT JOIN bip_difficulty bd ON b.difficulty_id = bd.id
						WHERE b.is_deleted=0
						  AND b.lang_id='1'
						  AND b.user_role=1
						  AND b.psychologist_id!=0
						  AND b.difficulty_id IN (?) ",array($diff_multi));
		$result = $query->result();

		$selectList = new stdClass();

		$difficulty_id_arr = array();
		$group_id_arr = array();
		$psychologist_id_arr = array();

		foreach ($result as $row) {
			$difficulty_id_arr[] = $row->difficulty_id;
			$group_id_arr[] = $row->group_id;
			$psychologist_id_arr[] = $row->psychologist_id;
			$difficulty_arr[] = $row->difficulty;
			$group_arr[] = $row->group_name;
			$psychologist_arr[] = $this->getUserFullName($row->psychologist_id);
		}

		$selectList->difficulty = array_unique($difficulty_arr);
		$selectList->group_name = array_unique($group_arr);
		$selectList->psychologist = array_unique($psychologist_arr);
		$selectList->difficulty_ids = array_unique($difficulty_id_arr);
		$selectList->group_ids = array_unique($group_id_arr);
		$selectList->psychologist_ids = array_unique($psychologist_id_arr);
		//echo "<pre>";print_r($selectList);exit;
		return $selectList;
}
function getUserSelectListPsy(){
	$permission = $this->session->userdata('permission');
	$permission=json_decode(json_encode($permission),true);
	$psy_id = $this->session->userdata('user_id');
	foreach ($permission['rights_per_group'] as $key => $grp_data) {
		if($grp_data['manage_users']== 1){
		$indexes[]=$key;
	}
	}
	if(!empty($indexes) && is_array($indexes))
		$grp_multi = join(',',$indexes);
	$grp_multi = rtrim($grp_multi,",");
	if($grp_multi){
	$query = $this->db->query("SELECT b.difficulty_id,
		b.id,
		bd.difficulty,
		b.psychologist_id,
		b.first_name,
		b.last_name,
		b.group_id,
		tg.group_name
		FROM bip_user b
		LEFT JOIN bip_group tg ON b.group_id = tg.id
		LEFT JOIN bip_difficulty bd ON b.difficulty_id = bd.id
		WHERE (b.is_deleted=0
		AND b.lang_id='1'
		AND b.user_role=1
		AND b.psychologist_id!=0
		AND b.group_id IN ($grp_multi)) OR b.psychologist_id =$psy_id");
	$result = $query->result();
}


	$selectListPsy = new stdClass();
	$difficulty_id_arr = array();
	$group_id_arr = array();
	$psychologist_id_arr = array();
	foreach ($result as $row) {
		$psychologist_id_arr[] = $row->psychologist_id;
		$psychologist_arr[] = $this->getUserFullName($row->psychologist_id);
	}
	$selectListPsy->psychologist = array_unique($psychologist_arr);
	$selectListPsy->psychologist_ids = array_unique($psychologist_id_arr);
		//echo "<pre>";print_r($selectListPsy);exit;
	//echo $this->db->last_query();
	return $selectListPsy;
}
function getUserSelectListDiff(){
	$permission = $this->session->userdata('permission');
	$permission=json_decode(json_encode($permission),true);
	foreach ($permission['rights_per_difficulty'] as $key => $diff_data) {
		$indexes[]=$key;
	}
	if(!empty($indexes) && is_array($indexes))
		$diff_multi = join(',',$indexes);
	$diff_multi = rtrim($diff_multi,",");
	//echo $diff_multi;die;
	if($diff_multi){
	$query = $this->db->query("SELECT * from bip_difficulty where id IN ($diff_multi)");
	$result = $query->result();
}
	//echo $this->db->last_query();
	//echo "<pre>"; print_r($result);die;
	$selectListDiff = new stdClass();
	$difficulty_id_arr = array();
	//$group_id_arr = array();
	//$psychologist_id_arr = array();
	foreach ($result as $row) {
		$difficulty_id_arr[] = $row->id;
		//$psychologist_arr[] = $this->getUserFullName($row->psychologist_id);
	}
	$selectListDiff->difficulty_ids = array_unique($difficulty_id_arr);
		//echo "<pre>";print_r($selectListDiff);exit;
	return $selectListDiff;
}
function getSelectListGrp(){
	$permission = $this->session->userdata('permission');
	$permission=json_decode(json_encode($permission),true);
	foreach ($permission['rights_per_group'] as $key => $grp_data) {
		$indexes[]=$key;
	}
	if(!empty($indexes) && is_array($indexes))
		$grp_multi = join(',',$indexes);
	$grp_multi = rtrim($grp_multi,",");
	//echo $grp_multi;die;
	if($grp_multi){
	$query = $this->db->query("SELECT * from bip_group where id IN ($grp_multi) order by group_name asc");
	$result = $query->result();
}
	//echo $this->db->last_query();
	//echo "<pre>"; print_r($result);die;
	$selectListGrp = new stdClass();
	//$difficulty_id_arr = array();
	$group_id_arr = array();
	//$psychologist_id_arr = array();
	foreach ($result as $row) {
		$group_id_arr[] = $row->id;
		//$psychologist_arr[] = $this->getUserFullName($row->psychologist_id);
	}
	$selectListGrp->group_ids = array_unique($group_id_arr);
		//echo "<pre>";print_r($selectListGrp);exit;
	return $selectListGrp;
	}

	function getUserFullName($id){
		$row = $this->db->query("SELECT first_name,last_name FROM bip_user WHERE id=?",array($id))->row();
        $row->first_name = $this->encryption->decrypt($row->first_name);
        $row->last_name = $this->encryption->decrypt($row->last_name);
		return $row->first_name.' '.$row->last_name;
	}

	function getDifficultyNamebyId($id){
		$row = $this->db->query("SELECT difficulty FROM bip_difficulty WHERE id=?",array($id))->row();
		return $row->difficulty;


	}

	function getDifficultyNamebyIds($diff_multi){
		$query = $this->db->query("SELECT * FROM bip_difficulty WHERE id IN ($diff_multi)");
		$result = $query->result();
		return $result;


	}
	function getGroupNamebyId($id){
		$row = $this->db->query("SELECT group_name FROM bip_group WHERE id=?",array($id))->row();
		return $row->group_name;


	}

	function getAllUserPsycho($offset=0, $datalimit=50, $orderBy, $psychologistid) {

	}

	function encode5t($str) {
		for ($i = 0; $i < 5; $i++) {
			$str = strrev(base64_encode($str)); //apply base64 first and then reverse the string
		}
		return $str;
	}

	//function to decrypt the string
	function decode5t($str) {
		for ($i = 0; $i < 5; $i++) {
			$str = base64_decode(strrev($str)); //apply base64 first and then reverse the string}
		}
		return $str;
	}

	function getAllDifficultyByLang()
		{
			$language_code = $this->session->userdata('language_code');
			$query = $this->db->query("Select * from bip_difficulty where lang_id='$language_code' order by  difficulty",array($language_code));
			$result = $query->result();
			return $result;
		}

	function createPassword($length = 8, $add_dashes = false, $available_sets = 'luds') {
		$sets = array();
		if(strpos($available_sets, 'l') !== false)
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'u') !== false)
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'd') !== false)
			$sets[] = '23456789';
		if(strpos($available_sets, 's') !== false)
			$sets[] = '!@#$%&*?';

		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}

		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
			$password .= $all[array_rand($all)];

		$password = str_shuffle($password);

		if(!$add_dashes)
			return $password;

		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while(strlen($password) > $dash_len)
		{
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}

	function addUser() {
		$language_code = $this->session->userdata('language_code');

		// $password = (string) $this->encode5t($this->input->post('autogeneratedpw'));
        // $password = password_hash($this->input->post('autogeneratedpw'), PASSWORD_BCRYPT);
        $reset_password = $this->input->post('reset_password');
        //$password = password_hash($this->input->post('autogeneratedpw'), PASSWORD_BCRYPT);
        $password = $this->bcrypt->hash_password($this->input->post('autogeneratedpw'));

		//$user_type        = $this->input->post('userType');
		$username = (string) $this->input->post('username');
		$oldUsername = (string) $this->input->post('oldUsername');

		$firstName = (string) $this->input->post('firstName');
		$lastName = (string) $this->input->post('lastName');
		$difficulty_db = (int) $this->input->post('difficulty');
		$address = $this->input->post('address');
		$city = $this->input->post('city');
		$contact_number = $this->input->post('contact_number');
		$contact_number_1 = $this->input->post('contact_number_1');
		$sms_notify = $this->input->post('sms_notify');

		$app_status = $this->input->post('app_status');
		$app_web_version = $this->input->post('app_web_version');
		$patient_access = $this->input->post('patient_access');
		$patient_access_create = $this->input->post('patient_access_create');

		$email = $this->input->post('email');
		$status = (int) $this->input->post('status');

		$group_db = (int) $this->input->post('group');

		$email_notify = (int) $this->input->post('email_notify');


		$user_role = (int) $this->input->post('user_role');
		$created_by = $this->session->userdata('user_id');
		$communication = (int) $this->input->post('communication');
		$active_from = (string) $this->input->post('from');
		$active_to = (string) $this->input->post('to');

			/*$permission = array(
		'diff_multi' => $this->input->post('diff_multi'),
		'user_manage' => (int) $this->input->post('user_manage'),
		'diff_create' => (int) $this->input->post('diff_create'),
		'diff_manage' => (int) $this->input->post('diff_manage'),
		'group_manage' => (int) $this->input->post('group_manage'),
		'psycho_manage' => (int) $this->input->post('psycho_manage')
			);*/
			$group_id = $this->input->post('group_id');
			// dd($_POST['group_id']);
			//echo '<pre>';print_r($group_id);
			$difficulty_id = $this->input->post('difficulty_id');
			$rights_per_group_arr = array();
			$grouparrfordb=[];
			foreach ($group_id as $key => $group) {
				//echo $key;
				$single_group_arr = array(
					'treat_patients' => $this->input->post('treat_patients_'.$key.''),
					'manage_users' => $this->input->post('manage_users_'.$key.''),
					'create_psychologists' => $this->input->post('create_psychologists_'.$key.''),
					'manage_psychologists' => $this->input->post('manage_psychologists_'.$key.''),
					'extract_data' => $this->input->post('extract_data_'.$key.''),
					);
				  //echo '<pre>';print_r($single_group_arr);
				if (in_array('1',$single_group_arr)) {
					$rights_per_group_arr[$group] = $single_group_arr;
					$grouparrfordb[]=$group;
				}
			}
			// echo "<pre>";
			// print_r($rights_per_group_arr);
			// echo "</pre>";

			$rights_per_difficulty_arr = array();
			foreach ($difficulty_id as $key => $difficulty) {
				$single_difficulty_arr = array(
					'edit_difficulty' => $this->input->post('edit_difficulty_'.$key.''),
					'give_rights_to_edit_difficulty' => $this->input->post('give_rights_to_edit_difficulty_'.$key.'')
					);
				if (in_array('1',$single_difficulty_arr)) {
					$rights_per_difficulty_arr[$difficulty] = $single_difficulty_arr;
				}
			}
			$permission = array(
				'rights_per_group' => $rights_per_group_arr,
				'rights_per_difficulty' => $rights_per_difficulty_arr,
				'other_rights' => array(
					'create_new_difficulty' => (int) $this->input->post('create_new_difficulty'),
					'create_new_group' => (int) $this->input->post('create_new_group')
					)
		);
		$permission = json_encode($permission);
		if ($user_role == 1)
			$psychology = $this->input->post('psychologist');
		else {
			$psychology = '';
			$psy_group_id = implode(',',$grouparrfordb);

			// $psy_group_id = rtrim($psy_group_id,",");

			$group_db = $psy_group_id;

			$difficulty_db = 0;
		}


		$userId = $this->input->post('userId');

		$visitor_user_id = $this->session->userdata('user_id');
		$visitor_user_role = $this->session->userdata('user_role');
		$visitor_user_name = ($visitor_user_role==3) ? $this->session->userdata('email'): $this->session->userdata('username');
		$visitor_full_name = $this->session->userdata('first_name').' '.$this->session->userdata('last_name');
		$visitor_ip_address = $this->input->ip_address();
		$visitor_comment = '';

		if ($userId) {
			$checkQuery = $this->db->query("SELECT id,password from bip_user WHERE username=?",array($username));
			if ($checkQuery->num_rows() > 0 && $username != $oldUsername) {
				echo '<script>alert("Username already exists !");</script>';
				return false;
			} else {

				$userRow = $checkQuery->row();
        		$password = ($reset_password==0) ? $userRow->password : $password;

		        $firstName = $this->encryption->encrypt($firstName);
		        $lastName = $this->encryption->encrypt($lastName);
		        $email = $this->encryption->encrypt($email);
		        $contact_number = $this->encryption->encrypt($contact_number);
		        $contact_number_1 = $this->encryption->encrypt($contact_number_1);

				$query = $this->db->query("call updateUser('$userId','$username','$contact_number','$contact_number_1','$sms_notify','$email','$password','$firstName','$lastName','$difficulty_db', '$address','$city','$group_db','$email_notify' ,'$psychology','$user_role','$created_by','$communication','$permission','$active_from','$active_to','$status','$language_code','$app_status','$app_web_version','$patient_access','$patient_access_create','$visitor_user_id','$visitor_user_role','$visitor_user_name','$visitor_full_name','$visitor_ip_address','$visitor_comment')");

				$this->updateRevisionUserTable($userId,'UPDATE');

				return true;
			}
		} else {
			$checkQuery = $this->db->query("SELECT id from bip_user WHERE username=?",array($username));
			if ($checkQuery->num_rows() > 0) {
				echo '<script>alert("Username already exists !");</script>';
				return false;
			} else {

				$firstName = $this->encryption->encrypt($firstName);
			    $lastName = $this->encryption->encrypt($lastName);
			    $email = $this->encryption->encrypt($email);
			    $contact_number = $this->encryption->encrypt($contact_number);
			    $contact_number_1 = $this->encryption->encrypt($contact_number_1);

				$query = $this->db->query("call addUser(@a,'$username','$contact_number','$contact_number_1','$sms_notify','$email','$password','$firstName','$lastName','$difficulty_db', '$address','$city','$group_db','$email_notify' ,'$psychology','$user_role','$created_by','$communication','$permission','$active_from','$active_to','$status','$language_code','$app_status','$app_web_version','$patient_access','$patient_access_create','$visitor_user_id','$visitor_user_role','$visitor_user_name','$visitor_full_name','$visitor_ip_address','$visitor_comment')");

				$query1 = $this->db->query("select @a as userId");
	      $row1 = $query1->row();

       	$userId = $row1->userId;
				$this->updateRevisionUserTable($userId,'INSERT');

				return true;
			}
		}
	}

	function updateRevisionUserTable($id,$_revision_action){

				$this->db->freeDBResource();

				$visitor_user_id = $this->session->userdata('user_id');
				$visitor_user_role = $this->session->userdata('user_role');
				$visitor_user_name = ($visitor_user_role==3) ? $this->session->userdata('email'): $this->session->userdata('username');

				$visitor_full_name = $this->session->userdata('first_name').' '.$this->session->userdata('last_name');
				$visitor_full_name = $this->encryption->encrypt($visitor_full_name);

				$visitor_ip_address = $this->input->ip_address();

				$visitor_comment = '';

				$this->db->query("UPDATE bip_user SET visitor_user_id	=?,visitor_user_role=?,visitor_user_name=?,visitor_full_name=?,visitor_ip_address=?,visitor_comment=? WHERE id=?",array($visitor_user_id,$visitor_user_role,$visitor_user_name,$visitor_full_name,$visitor_ip_address,$visitor_comment,$id));

    		$this->db->query("INSERT INTO _revision_bip_user (id,username, password, user_type, first_name, last_name, difficulty_id, contact_number, sms_notify, address, city, email, join_date, num_login, group_id, email_notify, psychologist_id, locked_stages, permission, user_role, active_from, active_to, last_login, first_login, no_of_login, no_of_login_old, total_time_in_system, total_time_in_system_old, status, notification, notification_status, other_info, inactive_last_check, locked_files, app_status, reminder_sent, lang_id, is_deleted, app_reminder, app_reminder_type, patient_access, patient_access_create, notification_enabled, app_web_version, last_sync_date, visitor_user_id, visitor_ip_address, visitor_user_name, visitor_full_name, visitor_user_role, visitor_comment) SELECT id,username, password, user_type, first_name, last_name, difficulty_id, contact_number, sms_notify, address, city, email, join_date, num_login, group_id, email_notify, psychologist_id, locked_stages, permission, user_role, active_from, active_to, last_login, first_login, no_of_login, no_of_login_old, total_time_in_system, total_time_in_system_old, status, notification, notification_status, other_info, inactive_last_check, locked_files, app_status, reminder_sent, lang_id, is_deleted, app_reminder, app_reminder_type, patient_access, patient_access_create, notification_enabled, app_web_version, last_sync_date, visitor_user_id, visitor_ip_address, visitor_user_name, visitor_full_name, visitor_user_role, visitor_comment FROM bip_user WHERE id = ?",array($id));

				$_revision = $this->db->insert_id();
        $this->db->query("UPDATE _revision_bip_user SET _revision_user_id	=?,_revision_action=?,_revision_timestamp=now() WHERE _revision=?",array($id,$_revision_action,$_revision));

	}

	function checkUsername($username) {

		$user_id = $this->input->post('user_id');

		/*$query = $this->db->query("SELECT id FROM bip_admin_user WHERE email='$username' AND status=1");
		if ($query->num_rows()>0) {
			return "1";
		}else{*/
			$query1 = $this->db->query("SELECT id FROM bip_user WHERE email=? OR username=? AND id!=?",array($username,$username,$user_id));
			if ($query1->num_rows()>0) {
				return "1";
			}else{
				return "0";
			}
		// }
	}

	function checkDifficultyName($difficultyName)
	{
		$query = $this->db->query("select id from  bip_difficulty where difficulty = ?",array($difficultyName));
		//$row	= $query->row();
		if ($query->num_rows() > 0)
			return "1";
		else
			return "0";

		$this->db->freeDBResource();
	}

	function delectUserById($userId) {

		$visitor_user_id = $this->session->userdata('user_id');
		$visitor_user_role = $this->session->userdata('user_role');
		$visitor_user_name = ($visitor_user_role==3) ? $this->session->userdata('email'): $this->session->userdata('username');
		$visitor_full_name = $this->session->userdata('first_name').' '.$this->session->userdata('last_name');
		$visitor_ip_address = $this->input->ip_address();
		$visitor_comment = '';

		$query = $this->db->query("UPDATE bip_user SET is_deleted=1,status=0,visitor_user_id=?,visitor_user_role=?,visitor_user_name=?,visitor_full_name=?,visitor_ip_address=?,visitor_comment=? WHERE id=?",array($visitor_user_id,$visitor_user_role,$visitor_user_name,$visitor_full_name,$visitor_ip_address,$visitor_comment,$userId));
		$this->updateRevisionUserTable($userId,'DELETE');
		$this->db->freeDBResource();
	}

	function removeUserById($userId) {
		$psychologistid = $this->session->userdata('user_id');
		$query = $this->db->query("call removeUserById($userId,$psychologistid)");
		// $this->logger->logAction('user deleted','deleted user id is '.$userId);
		$this->db->freeDBResource();
	}

	function getUserByUserId($userId) {
		$query = $this->db->query("call getUserByUserId($userId)");
		$row = $query->row();
		$this->db->freeDBResource();
		return $row;
	}

	function getAllGroup() {
		$query = $this->db->query("call getAllGroup($userId)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

		function getAllGroupByLang()
		{
			$language_code = $this->session->userdata('language_code');
			$query = $this->db->query("Select * from bip_group where lang_id=? order by  group_name",array($language_code));
			$result = $query->result();
			$this->db->freeDBResource();
			return $result;
		}

	function getAllpsychology() {
		$query = $this->db->query("call getAllpsychology($userId)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getAllpsychologyByLang() {
		$language_code = $this->session->userdata('language_code');
		$query = $this->db->query("SELECT * from bip_user where user_role=2 AND is_deleted=0 AND lang_id=?",array($language_code));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getPsychologistIdByUserId() {
		$userId = $this->session->userdata('user_id');
		$query = $this->db->query("call getPsychologistIdByUserId('$userId')");
		$result = $query->result();
		$this->db->freeDBResource();
		$totalRows = array("");
		$result = array($result, $totalRows);
		return $result;
	}

	function getPsychologyByDifficulty($difficultyid='') {
		if (isset($difficultyid) && $difficultyid != '')
			$query = $this->db->query("call getDifficultyByPsychology($difficultyid)");
		else
			$query = $this->db->query("call getAllpsychology()");
		//echo $this->db->last_query();
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

					function getPsychologyByGroup($grp){
						$query = $this->db->query("SELECT * from bip_user where group_id IN ($grp) and user_role=2");
						$result = $query->result();
						$this->db->freeDBResource();
						echo $this->db->last_query();
						return $result;
					}
	function getIdByEmail($email) {
		$query = $this->db->query("call getIdByEmail('$email')");
		$result = $query->row();
		$this->db->freeDBResource();
		return $result;
	}

	function getAllStagewithdiff($difficultyid, $userid) {
		$result = $this->db->query("select count(*) as total_stage from bip_stage where difficulty_id=? group by difficulty_id",array($difficultyid));
		$result = $result->row();
		$total_stage = $result->total_stage;
		$this->db->freeDBResource();

		$result1 = $this->db->query("select *  from bip_user_activity where user_id=? and status=1 group by stage_id,user_id",array($userid));
		$result1 = $result1->result();

		if ($total_stage == '')
			$total_stage = 0;

		$datacompleted_stage = count($result1) . '/' . $total_stage;
		return $datacompleted_stage;
		$this->db->freeDBResource();
	}

	function checkdeleteUser() {
		$userid = $this->input->post('userid');
		$result = $this->db->query("SELECT count(*) as totalrow from bip_user where psychologist_id=? ",array($userid));
		$result = $result->row();
		if ($result->totalrow == 0) {
			echo true;
		} else {
			echo false;
		}
	}

	function listgroup() {
		$query = $this->db->query("call getAllGroup()");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function listNormalUsers() {
		$query = $this->db->query("SELECT * from bip_user where user_role=1");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}
	function getGroupOFPsy($grp){
		$usertype = $this->session->userdata('user_role_type');
		//echo $usertype;exit;
		if($grp){
		$query = $this->db->query("SELECT * from bip_group where id IN ($grp)");
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
		}	elseif($usertype!="superadmin"){
			echo "You don't have permission!";exit;
		}

	}
	function listNormalUsersByGroup($groupId) {
		$query = $this->db->query("SELECT *,TIME_FORMAT(SEC_TO_TIME(total_time_in_system),'%H:%i:%s') as total_time_logged_in from bip_user where user_role=1 AND group_id=?",array($groupId));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getTotalByGroup($groupId) {
		$query = $this->db->query("SELECT *,TIME_FORMAT(SEC_TO_TIME(sum(total_time_in_system)),'%H:%i:%s') as total_group_time,sum(no_of_login) as total_login,max(last_login) as max_last_login from bip_user where user_role=1 AND group_id=?",array($groupId));
		$result = $query->row();
		$this->db->freeDBResource();
		return $result;
	}

	function getPsychologistDetailByID($userId){
		$query = $this->db->query("SELECT *,TIME_FORMAT(SEC_TO_TIME(total_time_in_system),'%H:%i:%s') as total_time_logged_in from bip_user where user_role=2 AND id=?",array($userId));
		$result = $query->row();
		$this->db->freeDBResource();
		return $result;
	}

	function listPsychologistUsersById($psychologist_id,$group_id){
		$query = $this->db->query("SELECT * from bip_user where psychologist_id=? AND group_id=?",array($psychologist_id,$group_id));
		$result = $query->result();
		$this->db->freeDBResource();
		return $result;
	}

	function getStepsForXls($groupId) {

		$strSql = "SELECT step_id, step.title as step_title, st.stage_title , template_id
					FROM bip_form_data fd, bip_user u,bip_step step,bip_stage st
					WHERE u.id=fd.user_id AND step.stage_id=st.id AND fd.step_id=step.id AND u.group_id=? AND step.template_id='5'
					group by fd.step_id ORDER by st.ordering, step.ordering";

		$query = $this->db->query($strSql,array($groupId));
		$result = $query->result();
		return $result;
	}

	function getUserForXls($groupId) {
		$strSql = "SELECT DISTINCT fd.user_id, u.first_name, u.last_name from bip_form_data fd, bip_user u WHERE u.id=fd.user_id AND u.group_id=? order by user_id";

		$query = $this->db->query($strSql,array($groupId));
		$result = $query->result();
		return $result;
		/*
		  $sql_stage=$this->db->query("select us.first_name,us.id,step.title,st.stage_title,bfrd.message from bip_user us,bip_step step,bip_stage st,bip_form_data bfrd where us.id=bfrd.user_id and step.id=bfrd.step_id and step.stage_id=st.id group by us.id order by bfrd.step_id");
		  $resultstage=$sql_stage->result();
		  $this->db->freeDBResource();
		 */
	}

	function getUserByStepId($stepId, $groupId) {
		$userId = $this->session->userdata('user_id');
		$userType = getUserType();

		$strSql = "SELECT DISTINCT fd.user_id, u.first_name, u.last_name from bip_form_data fd, bip_user u
						WHERE u.id=fd.user_id AND fd.step_id=? and u.group_id=?";

		if ($userType == "Psychologist"):
			$strSql .= " and u.psychologist_id=?";
			$bindArray = array($stepId,$groupId,$userId);
		else:
			$bindArray = array($stepId,$groupId);
		endif;

		$strSql .= " order by first_name";

		$query = $this->db->query($strSql,$bindArray);
		$result = $query->result();
		return $result;
	}

	function getFormDataByUserId($userId) {
		$strSql = "SELECT step.title,st.stage_title,fd.message FROM
				bip_step step,bip_stage st,bip_form_data fd
					WHERE step.id=fd.step_id and step.stage_id=st.id
					ORDER by fd.send_date";
		$query = $this->db->query($strSql);
		$result = $query->result();
		return $result;
	}

	function getFormDataByUserIdFormId($userId, $formId) {
		$strSql = "SELECT ";
		$query = $this->db->query($strSql);
		$result = $query->result();
		return $result;
	}

	function getQuestionByStepId($stepId) {
		$strSql = "SELECT f.fld_label, f.id as form_id FROM
				bip_form f
					WHERE f.step_id='$stepId' AND fld_vas_excel=\"1\"";

		$query = $this->db->query($strSql);
		$result = $query->result();
		return $result;
	}

	function getFormDataByStepId($stepId) {
		$strSql = "SELECT u.first_name,u.last_name, fd.message, f.fld_label, f.id as form_id FROM
					bip_form f, bip_form_data fd
					WHERE f.step.id=fd.step_id and u.id=fd.user_id and fd.step_id=?";

		$query = $this->db->query($strSql,array($stepId));
		$result = $query->result();
		return $result;
	}

	function getUserDataByStepIdUserId($stepId, $userId) {
		$strSql = "SELECT fd.message FROM
				bip_form_data fd
				WHERE fd.step_id=? AND fd.user_id =?";
		$query = $this->db->query($strSql,array($stepId,$userId));
		$result = $query->row();
		return $result;
	}

	// by sujeet
	function getUserInboxMail($userId, $psycologistId) {
		$strSql = $this->db->query("select bip_out.*,
						bip_user.username as sender_username,
						bip_user.first_name as sender_first_name,
						bip_user.last_name as sender_last_name,
						bip_user.address as sender_address,
						bip_user.city as sender_city,
						bip_user.email as sender_email
							from (select bip_msg.*  from (select * from bip_message where receiver_id=? and message_type='0' ) as bip_msg
								join bip_user on bip_msg.receiver_id = bip_user.id  where bip_user.psychologist_id	= ? ) as bip_out
								join bip_user on bip_out.sender_id = bip_user.id",array($userId,$psycologistId));
		return $strSql->result();
	}

	function getUserOutboxMail($userId, $psycologistId) {
		$strSql = $this->db->query("select bip_out.*,
						bip_user.username as receiver_username,
						bip_user.first_name as receiver_first_name,
						bip_user.last_name as receiver_last_name,
						bip_user.address as receiver_address,
						bip_user.city as receiver_city,
						bip_user.email as receiver_email
							from (select bip_msg.*  from (select * from bip_message where sender_id=? and message_type='0'  ) as bip_msg
								join bip_user on bip_msg.sender_id = bip_user.id  where bip_user.psychologist_id	= ?) as bip_out
								join bip_user on bip_out.receiver_id = bip_user.id",array($userId,$psycologistId));
		return $strSql->result();
	}

	/**
	 * function for sending emails as cron jobs
	 */

	/**
	 * get all psychlogist list to send email
	 */
	function get_psycholoist_to_send_email() {
		$strSql = 'SELECT id,CONCAT(first_name," ",last_name) as full_name, email, status, active_from, active_to  FROM bip_user where user_role = "2"
				   AND status="1" and active_from<CURDATE() AND active_to>CURDATE()
				   AND email_notify = "1" ';

		$query = $this->db->query($strSql);
		return $query->result();
	}

	function get_email_detail_by_psychologist_id($psycho_id) {
//        $strSql = 'SELECT U.id, CONCAT(first_name," ",last_name) as full_name, W.total_worksheet, M.total_message, C.total_comment FROM bip_user U
//LEFT JOIN (SELECT count(*) as total_worksheet, user_id FROM bip_form_data where send_date>CURDATE()-100 GROUP BY user_id) W on U.id = W.user_id
//LEFT JOIN (SELECT count(*) as total_message, receiver_id, sender_id FROM bip_message WHERE sent_on>CURDATE()-100 AND message_type="0" AND receiver_id ="'.$psycho_id.'" GROUP BY receiver_id) M ON U.id = M.sender_id
//LEFT JOIN (SELECT count(*) as total_comment, user_id FROM bip_worksheet_comments WHERE posted_on>CURDATE()-100 GROUP BY user_id) C ON U.id = C.user_id
//WHERE U.psychologist_id = "'.$psycho_id.'" AND (total_worksheet >0 OR total_message>0 OR total_comment>0)';
		$strSql = 'SELECT U.id, CONCAT(first_name," ",last_name) as full_name, W.total_worksheet, M.total_message, C.total_comment FROM bip_user U
				LEFT JOIN (SELECT count(*) as total_worksheet, user_id FROM bip_form_data where user_id IN (select id from bip_user where psychologist_id= ? ) AND send_date>CURDATE()-100 GROUP BY user_id) W on U.id = W.user_id
				LEFT JOIN (SELECT count(*) as total_message, receiver_id FROM bip_message WHERE sent_on>CURDATE()-100 AND message_type="0" AND receiver_id =? GROUP BY receiver_id) M ON U.id = M.receiver_id
				LEFT JOIN (SELECT count(*) as total_comment, user_id FROM bip_worksheet_comments WHERE user_id IN (select id from bip_user where psychologist_id= ? ) AND posted_on>CURDATE()-100 GROUP BY user_id) C ON U.id = C.user_id
				WHERE U.psychologist_id = ? AND (total_worksheet >0 OR total_message>0 OR total_comment>0)';
		$query = $this->db->query($strSql,array($psycho_id,$psycho_id,$psycho_id,$psycho_id));
		return $query->result();
	}

	function get_emails_by_psychologist_id($psycho_id) {
		$strSql = 'SELECT id, message.* FROM bip_user
					INNER JOIN
							(SELECT concat(bip_user.first_name," ",bip_user.last_name) as sender, sender_id, receiver_id, sent_on, msg_subject, message,status_receiver, message_type  FROM bip_message
							INNER JOIN bip_user on bip_message.sender_id = bip_user.id
							WHERE receiver_id=? AND sent_on > CURDATE()-100  and message_type= "0") message
					ON bip_user.psychologist_id = message.receiver_id
					where psychologist_id = ?
					order by sent_on desc
				';
		$query = $this->db->query($strSql,array($psycho_id,$psycho_id));
		return $query->result();
	}

	function get_total_worksheet_by_psycho_id($psycho_id) {
		$strSql = 'SELECT COUNT(*) as total_worksheet from bip_form_data where user_id IN (select id from bip_user where psychologist_id=?) AND send_date>CURDATE()-1 AND status="1"';
		$query = $this->db->query($strSql,array($psycho_id));
		$data = $query->row();
		$worksheet = $data->total_worksheet;

		$strSql = 'SELECT COUNT(*) as total_training from bip_weekly_training where user_id IN (select id from bip_user where psychologist_id=?) AND created_on>CURDATE()-1 and status_new="1"';
		$query = $this->db->query($strSql,array($psycho_id));
		$data = $query->row();
		$training = $data->total_worksheet;

		$total_worksheet = $worksheet + $training;
		return $total_worksheet;
	}

	function get_total_message_by_psycho_id($psycho_id) {
		$strSql = 'SELECT COUNT(*) as total_message from bip_message where receiver_id =? AND  message_type="0" AND  sent_on>CURDATE()-1 and status_receiver="0"';
		$query = $this->db->query($strSql,array($psycho_id));
		$data = $query->row();
		return $data->total_message;
	}

	function get_total_comments_by_psycho_id($psycho_id) {
		$strSql = 'SELECT COUNT(*) as total_comment from bip_worksheet_comments where user_id IN (select id from bip_user where psychologist_id=?) AND posted_on>CURDATE()-1 and wc_status="1"';
		$query = $this->db->query($strSql,array($psycho_id));
		$data = $query->row();
		$worksheet_comment = $data->total_comment;

		$strSql = 'SELECT COUNT(*) as total_comment from bip_training_comments where user_id IN (select id from bip_user where psychologist_id=?) AND posted_on>CURDATE()-1 and status_new="1"';
		$query = $this->db->query($strSql,array($psycho_id));
		$data = $query->row();
		$training_comments = $data->total_comment;
		$total_comment = $worksheet_comment + $training_comments;

		return $total_comment;
	}

	function updatePsychologistDifficulty()
	{
		echo '<pre>';
		$sql = 'select id from bip_difficulty where 1=1';
		$query = $this->db->query($sql);
		$difficulties = $query->result_array();

		$sql = 'select id, permission from bip_user where user_role = 2 and id = 347';

		$query = $this->db->query($sql);
		$psychlogists = $query->result();
		foreach($psychlogists as $psychlogist)
		{
			$permission = json_decode($psychlogist->permission);

			$new_permission = array("diff_multi"=>array());
			$flag = false;
			if(is_array($permission->diff_multi) && count($permission->diff_multi) > 0)
			foreach($permission->diff_multi as $difficultyId)
			{
				if(in_array(array("id"=>$difficultyId),$difficulties))
				{
					$new_permission['diff_multi'][] = $difficultyId;
				}
				else
					$flag = true;
			}
			if($flag)
			{

				$new_permission['user_manage'] = $permission->user_manage ;
				$new_permission['diff_create'] = $permission->diff_create;
				$new_permission['diff_manage'] = $permission->diff_manage;
				$new_permission['group_manage'] = $permission->group_manage ;
				$new_permission['psycho_manage'] = $permission->psycho_manage ;

				$json_permission = json_encode($new_permission);
				$psychlogist_id = $psychlogist->id;
				$query = $this->db->query("update bip_user set permission=? where id =? ",array($json_permission,$psychlogist_id));
				$this->db->freeDBResource();

				echo $json_permission;
				print_r($permission);
				print_r($new_permission);
				echo '<br/>';
				echo '<br/>';
			}

		}

	}

	function get_message_pending()
	{
		// replace bmp.notify_date = date(now()+interval 1 day) to debug
		$sql = "select bu.lang_id,bu.contact_number as contact_number, bu.email as email, bmp.sms_notify as sms_notify, bmp.email_notify as email_notify, bm.msg_subject as subject, bm.message as message,bm.id as message_id from bip_user bu inner join bip_message bm on bu.id = bm.receiver_id inner join bip_message_pending bmp on bm.id = bmp.message_id where bmp.notify_date > bu.last_login  and bmp.notify_date = date(now()) and bu.active_to >= now() and bu.user_role=1";
			// $sql.=" and bu.sms_notify = 1 and (bmp.sms_notify = 1 or bmp.email_notify = 1)";
		$query = $this->db->query($sql);
		return $query->result();
	}

	function getUserforCronSMS()
	{
		$sql = "select bu.lang_id,distinct (bu.id) , bu.contact_number as contact_number, bu.email as email
			from bip_user bu
			inner join bip_message bm on bu.id = bm.receiver_id
			inner join bip_form_data bf on bu.id = bf.user_id
			inner join bip_worksheet_comments bwc on bwc.worksheet_id = bf.id
			where bu.user_role=1 and bu.sms_notify = 1 and bu.active_to >= now() and
			((bm.sent_on > bu.last_login and bm.status_receiver = 0) or
			(bwc.posted_on > bu.last_login and bwc.wc_status = 1))";
		$query = $this->db->query($sql);
		return $query->result();
	}

	function get_lang_name($id)
	{
		$query = $this->db->query("SELECT lang_name FROM bip_language WHERE lang_id= ? ",array($id))->row();
		return $query->lang_name;
	}

	function has_manage_users_admin_rights($user)
	{
		$psychologistid = $this->session->userdata('user_id');
		$sess_permission = $this->session->userdata('permission');
		//if user is patient
		if ($user->user_role==1) {
			$group_id = $user->group_id;
			if ($sess_permission['rights_per_group'][$group_id]['manage_users']) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	function getAllpsychologyOfGroupByLang($group_id) {

		$language_code = $this->session->userdata('language_code');
		$query = $this->db->query("SELECT * from bip_user b where user_role=2 AND is_deleted=0 AND lang_id='$language_code' and  find_in_set('$group_id', cast(b.group_id as char)) > 0  order by first_name asc");
		$result = $query->result();
		// echo $this->db->last_query();
		// print_r($result);
		$this->db->freeDBResource();
		return $result;
	}

	function getAllpsychologyByLangHavingTreatPatientPermission($group_id){

		$all_psy = $this->getAllpsychologyOfGroupByLang($group_id);
		$psy_arr=[];


		foreach ($all_psy as $psy) {

			$permission = json_decode($psy->permission,true);

			//dd($permission);
			if(in_array($psy->id, $psy_arr)){
				continue;
			}
			if(isset($permission['rights_per_group'][$group_id])){
				if($permission['rights_per_group'][$group_id]['treat_patients']==1){
					$psy_arr[]=$psy->id;
					$psy_having_treat_patient_per[] = $psy;
				}
			}
			// foreach ($permission['rights_per_group'] as $key => $grp_details) {
			// 	if($grp_details['treat_patients'] == 1){
			// 		print_r( $psy_arr);


			// 	}
			// }
		}

		return $psy_having_treat_patient_per;

	}
	function getPermissionOfPsy(){
        $user_id = $this->session->userdata('user_id');
        $query = $this->db->query("select * from bip_user where id = '$user_id'")->row();
        return $query->permission;
	}

	function get_sorted_psychologist_list(){
		$Psychology = $this->getAllpsychologyByLang();
		$p_full_name_list = array();
		foreach ($Psychology as $key => $psy_row) {
			$p_full_name_list[$psy_row->id] = $this->encryption->decrypt($psy_row->first_name).' '.$this->encryption->decrypt($psy_row->last_name);
		}
		asort($p_full_name_list);
		//dd($p_full_name_list);
		return $p_full_name_list;
	}

}
