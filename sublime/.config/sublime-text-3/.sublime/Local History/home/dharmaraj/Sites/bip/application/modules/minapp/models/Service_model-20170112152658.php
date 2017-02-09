<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class service_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	function encode5t($str) {
        for ($i = 0; $i < 5; $i++) {
            $str = strrev(base64_encode($str)); //apply base64 first and then reverse the string
        }
        return $str;
    }

    function smsVerify($userId)
    {
        $query = $this->db->query("call getUserByUserId($userId)");
        $row = $query->row();
        $this->db->freeDBResource();
        if (!empty($row->contact_number)) {
                $token = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 5);
                sendSMS($row->contact_number, $token);
                $this->login_model->insertSmsValidation($row,$token);
		        $smsArr= array(
		        		'sms'=>'true',
		        		'code'=>$token
		        	);
        }else{
        	$smsArr=array('sms'=>'false');
        }
        return $smsArr;
    }

	function validateuser($data)
	{
		$username=$data['username'];
		$password=$data['password'];
		$deviceId=$data['deviceId'];
		$UrbanAirshipId=$data['UrbanAirshipId'];
		$devicetype=$data['devicetype'];
		$tokenkey=$data['tokenkey'];

		if (empty($deviceId)) return false;

			$strSql = "SELECT * FROM bip_user WHERE username='$username' ";
			$strSql .= "AND status='1' AND app_status='1' AND user_role='1' AND (CURDATE() BETWEEN STR_TO_DATE(active_from, '%Y-%m-%d') AND STR_TO_DATE(active_to,'%Y-%m-%d'))";

			$query = $this->db->query($strSql);

			if ($query->num_rows() == 1) {
				$data = $query->row();

                if (!$this->bcrypt->check_password($password,$data->password)) return false;

				if (empty($devicetype)) { //postman only
					$q=$this->db->query("SELECT UUID() NewUUID");
					if($q->num_rows()>0){
						foreach($q->result() as $r){
							$tokenkey=$r->NewUUID;
						}
					}

				}

				$userId=$data->id;

				$lastlogdate = date("Y-m-d");
				$this->db->query("INSERT INTO bip_user_app (user_id,deviceId, UrbanAirshipId, devicetype,tokenkey,created_at,updated_at) VALUES (?,?,?,?,?,?,now()) ". "ON DUPLICATE KEY UPDATE UrbanAirshipId=VALUES(UrbanAirshipId),tokenkey=VALUES(tokenkey),devicetype=VALUES(devicetype),deviceId=VALUES(deviceId)",array($userId,$deviceId,$UrbanAirshipId,$devicetype,$tokenkey,$lastlogdate));
				// echo $this->db->last_query();
				$response = new stdClass;
				$response->tokenkey=$tokenkey;
				$response->Name=$data->first_name.' '.$data->last_name;
				$response->userid=$data->id;
				$response->hide_graph= $this->isHideGraph($data->difficulty_id);
				$newstartpage = $this->db->query("SELECT new_start_page FROM bip_difficulty WHERE id=?",array($data->difficulty_id))->row();
				$response->new_start_page = $newstartpage->new_start_page;

				$flow = $this->getTreatmentFlow($data->difficulty_id);
				$default = $this->getTreatmentFlow(0);

				$rating1 = array(
					'type'=> 1,
					'anxiety' => (!empty($flow->anxiety)) ? $flow->anxiety: $default->anxiety,
					'zero' => (!empty($flow->zero)) ? $flow->zero: $default->zero,
					'ten' => (!empty($flow->ten)) ? $flow->ten: $default->ten,
					'txt_button' => (!empty($flow->txt_button)) ? $flow->txt_button: $default->txt_button,
				 );

				$rating2 = array(
					'type'=> 2,
					'1' => array(
						'headline'=>(!empty($flow->slide1_headline)) ? $flow->slide1_headline: $default->slide1_headline,
						'text'=>(!empty($flow->slide1_text)) ? $flow->slide1_text: $default->slide1_text,
						'button'=>(!empty($flow->slide1_button)) ? $flow->slide1_button: $default->slide1_button,
					),
					'2' => array(
						'headline'=>(!empty($flow->slide2_headline)) ? $flow->slide2_headline: $default->slide2_headline,
						'zero'=>(!empty($flow->slide2_zero)) ? $flow->slide2_zero: $default->slide2_zero,
						'ten'=>(!empty($flow->slide2_ten)) ? $flow->slide2_ten: $default->slide2_ten,
						'button'=>(!empty($flow->slide2_button)) ? $flow->slide2_button: $default->slide2_button,
					),
					'3' => array(
						'headline'=>(!empty($flow->slide3_headline)) ? $flow->slide3_headline:  $default->slide3_headline,
						'image'=> (!empty($flow->slide3_image)) ? base_url().'images/uploads/app_images/'.$flow->slide3_image: base_url().'images/uploads/app_images/'.$default->slide3_image,
						'text'=>(!empty($flow->slide3_text)) ? $flow->slide3_text: $default->slide3_text,
						'button'=>(!empty($flow->slide3_button)) ? $flow->slide3_button: $default->slide3_button,
						'timing'=>(!empty($flow->slide3_timing)) ? $flow->slide3_timing: $default->slide3_timing,
					),
					'4' => array(
						'headline'=>(!empty($flow->slide4_headline)) ? $flow->slide4_headline: $default->slide4_headline,
						'zero'=>(!empty($flow->slide4_zero)) ? $flow->slide4_zero: $default->slide4_zero,
						'ten'=>(!empty($flow->slide4_ten)) ? $flow->slide4_ten: $default->slide4_ten,
						'button'=>(!empty($flow->slide4_button)) ? $flow->slide4_button: $default->slide4_button,
					),
					'5' => array(
						'headline'=>(!empty($flow->slide5_headline)) ? $flow->slide5_headline: $default->slide5_headline,
						'conditional_text'=>array(
							'1'=>(!empty($flow->slide5_time_text1)) ? $flow->slide5_time_text1: $default->slide5_time_text1,
							'2'=>(!empty($flow->slide5_time_text2)) ? $flow->slide5_time_text2: $default->slide5_time_text2,
							'3'=>(!empty($flow->slide5_time_text3)) ? $flow->slide5_time_text3: $default->slide5_time_text3,
							),
						'compare'=>array(
							'x'=>(!empty($flow->slide5_time_x)) ? $flow->slide5_time_x: $default->slide5_time_x,
							'y'=>(!empty($flow->slide5_time_y)) ? $flow->slide5_time_y: $default->slide5_time_y,
							),
						'button'=>(!empty($flow->slide5_button)) ? $flow->slide5_button: $default->slide5_button,
					),
				 );
				if ($flow->rating==1) {
					$response->training = json_decode(json_encode($rating1), FALSE);
				} else {
					$response->training = json_decode(json_encode($rating2), FALSE);
				}

				//added by Sabin @16th April 2015
				$response->hasRegistration = $this->userHaveRegistrations($data->difficulty_id);
				$response->homeworks = $this->homeworkCounts($data->id, $data->difficulty_id);
				$response->crisisplans = $this->crisisplanCounts($data->id, $data->difficulty_id);
				// echo "<pre>";print_r($response);exit;

				/*$smsData=$this->smsVerify($userId);
				$response->sms=$smsData['sms'];
				if (!empty($smsData['code'])) {
					$response->code=$smsData['code'];
				}*/
				return $response;
			}
	}

	function validateUserToken($userid,$deviceId,$tokenkey){
		// $query=$this->db->query("SELECT * FROM bip_user_app WHERE user_id='$userid' AND deviceId='$deviceId' AND tokenkey='$tokenkey'");
		$query=$this->db->query("SELECT * FROM bip_user_app WHERE user_id=? AND deviceId=?",array($userid,$deviceId));
		if ($query->num_rows()>0) {
			return true;
		}else{
			return false;
		}
	}

	function saveTraining($data){
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			if ($trainingId==0) {
				$lastlogdate = date("Y-m-d");

				if ($type==1) {
					$this->db->query("INSERT INTO bip_training_app (user_id,task_id,trainingdatetime,comment, estimatedvalue, practice,created_at,updated_at) VALUES (?,?,?,?,?,1,?,now())",array($userid,$taskId,$trainingdatetime,$comment,$estimatedvalue,$lastlogdate));

				}else{
					$this->db->query("INSERT INTO bip_training_app (user_id,task_id,trainingdatetime,comment, estimatedvalue,estimatedvalue_end,training_duration,type, practice,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,1,?,now())",array($userid,$taskId,$trainingdatetime,$comment,$estimatedvalue,$estimatedvalue_end,$training_duration,$type,$lastlogdate));
				}
				$response->trainingId = $this->db->insert_id();
			}else{
				if ($type==1) {
					$this->db->query("UPDATE bip_training_app SET trainingdatetime=?,comment=?,estimatedvalue=?,practice='1' WHERE id=?",array($trainingdatetime,$comment,$estimatedvalue,$trainingId));
				}else{
					$this->db->query("UPDATE bip_training_app SET trainingdatetime=?,comment=?,estimatedvalue=?,estimatedvalue_end=?,training_duration=?,type=?,practice='1' WHERE id=?",array($trainingdatetime,$comment,$estimatedvalue,$estimatedvalue_end,$training_duration,$type,$trainingId));
				}
				$response->trainingId = $trainingId;
			}
			if ($this->db->affected_rows()>0) {

				if (!empty($played_time)) {
					$status_arr = array('played_time'=>$played_time,'played_at'=>date('Y-m-d'));
					$status_json = json_encode($status_arr);
					$this->db->query("UPDATE bip_user_app SET reminder_status=? WHERE user_id=?",array($status_json,$userid));
				}

				if (!empty($comment)) {
					$this->notifyCommentToPsy($userid,$taskId,$comment);
				}
			}
			$response->message = $this->getSuccessMessage($userid,$taskId);
			return $response;
			// return "Bra jobbat";
		}
		$response->message="Token Key Expired";
		return $response;
	}

	function getSuccessMessage($userId,$taskId)
	{
		$trainData = $this->getTrainingByTaskId($userId,$taskId);
		$practice  = $trainData->total;

		$query = $this->db->query("SELECT
			bta.slide6_message
		FROM
			`bip_user` bu
		LEFT JOIN bip_difficulty bd ON (bd.id = bu.difficulty_id)
		LEFT JOIN bip_treatment_app bta ON (
			bta.difficulty_id = bu.difficulty_id
		)
		WHERE
			bu.id = '$userId' LIMIT 1");

		$row=$query->row();
		$s6 = json_decode($row->slide6_message,true);

		$treatment_id = $this->getDefaultTreatmentId();
		$altQry = $this->db->query("SELECT slide6_message FROM bip_treatment_app WHERE id=? AND difficulty_id = 0",array($treatment_id));
		$altRow = $altQry->row();
		$as6 = json_decode($altRow->slide6_message,true);

		$alertMsg = (!empty($s6)) ? $s6: $as6;

		$sarr=array();
		foreach ($alertMsg as $sval) {
			$sexp = explode('~~~', $sval);
			$sarr[$sexp[0]] = $sexp[1];
		}

		$msg = array();
		foreach ($sarr as $k => $v) {
			if ($practice<=$k) $msg[] = $v;
		}

		if (empty($msg)) {
			foreach ($sarr as $k => $v) {
				if ($practice>=$k) $msg[] = $v;
			}
			$message = end($msg);
		}else{
			$message = $msg[0];
		}

		return $message;
	}

	function getDefaultTreatmentId()
    {
       $query = $this->db->query("SELECT id FROM bip_treatment_app WHERE difficulty_id='0'");
       $row=$query->row();
       return $row->id;
    }

	function notifyCommentToPsy($userId,$taskId,$comment)
	{
		$query=$this->db->query("SELECT psychologist_id,CONCAT(first_name,' ',last_name) as sender_name FROM bip_user WHERE id=? LIMIT 1",array($userId));
		$row=$query->row();
		$psychologistId=$row->psychologist_id;

		$date = date('Y-m-d H:i:s');
        $usertype='user';
        $comment = htmlspecialchars(addslashes($comment));

        $this->db->query("INSERT INTO bip_message set sender_id=?, receiver_id=?,sent_on=now(),status_receiver='0',status_sender='1', message_type='2',is_app='1',task_id=?",array($userId,$psychologistId,$taskId));
        if ($this->db->affected_rows()>0) {
            $messageId = $this->db->insert_id();
	        $this->db->query("INSERT INTO bip_app_comments (user_id,psychologist_id,task_id,usertype,comments,status_new,posted_on,message_id) VALUES (?,?,?,?,?,'1',?,?)",array($userId,$psychologistId,$taskId,$usertype,$comment,$date,$messageId));
        }
	}

	function getTrainingByTaskId($userid,$taskid){
		$query=$this->db->query("SELECT id FROM bip_training_app WHERE user_id=? AND task_id=? AND practice!='0'",array($userid,$taskid));
		$row = new stdClass();
		if ($query->num_rows()>0) {
			$row->total=$query->num_rows();
			if (!empty($row->total)) {
				$row->hastraings="true";
			}else{
				$row->hastraings="false";
			}
		}else{
			$row->total=0;
			$row->hastraings="false";

		}
		return $row;
	}

	function getTreatmentFlow($diffId){
		$query=$this->db->query("SELECT * FROM bip_treatment_app WHERE difficulty_id=?",array($diffId));
		$row=$query->row();
		return $row;
	}

	function getSettingDataByDiffId($diffId){
		$query=$this->db->query("SELECT anxiety,zero,ten FROM bip_treatment_app WHERE difficulty_id=?",array($diffId));
		$row=$query->row();
		return $row;
	}

	/**
	 * Active tasks that are assigned to target user plus not closed.
	 */
	function getActiveTasks($data){

		extract($data);

		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {

			$query=$this->db->query("SELECT id,difficulty_id FROM bip_user WHERE id=? LIMIT 1",array($userId));

			if ($query->num_rows()>0) {

				$row=$query->row();
				$diffId=$row->difficulty_id;

				$reminder = $this->getPatientAppReminder($userid,$diffId);

				$hide_graph = $this->isHideGraph($diffId);
				$hide_number = $this->isHideNumber($diffId);

				$likeString = '%,'.$userid.',%';

				$query1=$this->db->query("SELECT id AS taskid,
										       task AS heading,
										       tag,
										       completed
										FROM bip_tasks
										WHERE (CONCAT(',' , user_id , ',') LIKE ?)",array($likeString));
				$result=$query1->result_array();

				$settingData= $this->getSettingDataByDiffId($diffId);
				//retrive anxiety,zero,ten

				$newresult=array();
				foreach ($result as $rk => $rval) {
					$json=json_decode($rval['completed'],true);
					if (is_array($json) && array_key_exists($userid, $json)) continue;
					$newresult[]=$rval;
				}
				$newdata=array();
				foreach ($newresult as $k => $v) {
					$newdata[$k]['taskid'] = $taskid = $v['taskid'];
					if ($v['tag']=='1,2') {
						$v['tag']='3';
					}

					$newdata[$k]['tag'] = $v['tag'];
					$newdata[$k]['heading']=$v['heading'];

					$trainData= $this->getTrainingByTaskId($userid,$taskid);

					$newdata[$k]['practice']=$trainData->total;
					$newdata[$k]['hastraings']=$trainData->hastraings;

					$newdata[$k]['reminder']=$reminder['reminder'];
					$newdata[$k]['first_reminder']=$reminder['first_reminder'];
					$newdata[$k]['played_count']=$reminder['played_count'];

					$newdata[$k]['hide_graph']=$hide_graph;
					$newdata[$k]['hide_number']=$hide_number;

					$estimateData = array(

						'userid' => $userid,
						'deviceId' => $deviceId,
						'tokenkey' => $tokenkey,
						'taskId' => $taskid
					);

					$estimate = $this->Getestimatesfromstart($estimateData);

					$estimateArrStr = $estimate->Estimates;
					$estimateArrStr = str_replace('[', '', $estimateArrStr);
					$estimateArrStr = str_replace(']', '', $estimateArrStr);
					if (empty($estimateArrStr)) {
						$estimateCount = 0;
					}else{
						$estimateArr = explode(',', $estimateArrStr);
						$estimateCount = count($estimateArr);
					}

					$newdata[$k]['TodayDays']= $estimate->TodayDays;
					$newdata[$k]['Estimates']= $estimateCount;

				}
				return $newdata;

			}
		}
	}

	function getOldTrainings($data)
	{
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$mQuery=$this->db->query("SELECT MAX(trainingdatetime) as trainingdatetime FROM `bip_training_app` WHERE user_id = ? AND task_id = ? LIMIT 1",array($userid,$taskId));
			$row=$mQuery->row();
			$futureDate= $row->trainingdatetime;

			$query=$this->db->query("SELECT id as trainingId,task_id as taskid,DATE_FORMAT(trainingdatetime, '%Y-%m-%d %H:%i') as trainingdatetime,estimatedvalue,estimatedvalue_end,training_duration,type,comment FROM bip_training_app WHERE user_id=? AND task_id=? AND trainingdatetime <= ? AND trainingdatetime!='0000-00-00 00:00:00' AND practice=1 ORDER BY trainingdatetime DESC",array($userid,$taskId,$futureDate));
			if ($query->num_rows()>0) {
				$result=$query->result();
				$data=$result;
				return $data;
			}
		}
	}

	function GetActivityperweek($data){
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$query=$this->db->query("SELECT
							task_id,
							MIN(DATE_FORMAT(trainingdatetime, '%Y-%m-%d')) AS startdate,
							CURDATE() AS todaydate,
							DATEDIFF(
								CURDATE(),
								MIN(DATE_FORMAT(trainingdatetime, '%Y-%m-%d'))
							) AS TodayDays,
							FLOOR(
								DATEDIFF(
									CURDATE(),
									MIN(DATE_FORMAT(trainingdatetime, '%Y-%m-%d'))
								) / 7
							) as NoOfWeek,
						(SELECT count(*) FROM bip_training_app WHERE user_id=? AND task_id=? AND practice=1 AND trainingdatetime<CURDATE() + INTERVAL 1 DAY) as TotalNoPractice
						FROM
							`bip_training_app`
						WHERE
							user_id = ?
						AND task_id = ?
						AND trainingdatetime<CURDATE() + INTERVAL 1 DAY
						AND trainingdatetime!='0000-00-00 00:00:00' AND trainingdatetime>='2013-01-01'
						 AND practice=1
						",array($userid,$taskId,$userid,$taskId));
			if ($query->num_rows()>0) {
				$result=$query->result();

				$result[0]->TodayDays=$result[0]->TodayDays+1;
				$result[0]->NoOfWeek=$result[0]->NoOfWeek+1;

				$totalWeek=$result[0]->NoOfWeek;
				$activeFrom=$result[0]->startdate;
				$weekAry=createDateRangeArray($activeFrom,$totalWeek);
				// $weekAry=array_reverse($weekAry);
				$weeksData=array();
				foreach ($weekAry as $wval) {
					$weekstart=$wval['start'];
					$weekend=$wval['end'];
						$weeksData[]=$this->db->query("SELECT count(*) AS weeksData FROM bip_training_app WHERE user_id = ? AND task_id = ? AND trainingdatetime!='0000-00-00 00:00:00' AND trainingdatetime>='2013-01-01' AND trainingdatetime BETWEEN ? AND ?",array($userid,$taskId,$weekstart,$weekend))->row()->weeksData;
				}
				$weeksData=join(',',$weeksData);
				$result[0]->weeksData='['.$weeksData.']';

				foreach ($result as $value) {
					$response=$value;
				}
				return $response;
			}
		}
	}

	function Getestimatesfromstart($data){
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$query=$this->db->query("SELECT
							task_id,
							MIN(DATE_FORMAT(trainingdatetime, '%Y-%m-%d')) AS startdate,
							CURDATE() AS todaydate,
							DATEDIFF(
								CURDATE(),
								MIN(DATE_FORMAT(trainingdatetime, '%Y-%m-%d'))
							) AS TodayDays
						FROM
							`bip_training_app`
						WHERE
							user_id = ?
						AND task_id = ?
						AND trainingdatetime<CURDATE() + INTERVAL 1 DAY
						AND trainingdatetime!='0000-00-00 00:00:00' AND trainingdatetime>='2013-01-01'
						AND practice=1
						",array($userid,$taskId));
			if ($query->num_rows()>0) {
				$result=$query->result();

				$result[0]->TodayDays=$result[0]->TodayDays+1;

				$query1=$this->db->query("SELECT estimatedvalue as count FROM `bip_training_app` WHERE user_id=? AND task_id=? AND trainingdatetime!='0000-00-00 00:00:00' AND trainingdatetime>='2013-01-01' AND trainingdatetime<CURDATE() + INTERVAL 1 DAY AND `status`='yes' AND practice=1 ORDER BY trainingdatetime ASC",array($userid,$taskId));
				$result1=$query1->result_array();
				// echo "<pre>";print_r($result1);exit;
				$Estimates=$this->convert_to_single_array($result1);
				$result[0]->Estimates='['.$Estimates.']';
				foreach ($result as $value) {
					$response=$value;
				}
				return $response;
			}
		}
	}

	function convert_to_single_array($array) {
      $out = implode(",",array_map(function($a) {return implode("~",$a);},$array));
      return $out;
    }

	function GetActivityperday($data){
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$query=$this->db->query("SELECT
							MIN(DATE_FORMAT(trainingdatetime, '%Y-%m-%d')) AS startdate,
							CURDATE() AS todaydate,
							DATEDIFF(
								CURDATE(),
								MIN(DATE_FORMAT(trainingdatetime, '%Y-%m-%d'))
							) AS TodayDays,
							FLOOR(
								DATEDIFF(
									CURDATE(),
									MIN(DATE_FORMAT(trainingdatetime, '%Y-%m-%d'))
								) / 7
							) as NoOfWeek,
						(SELECT count(*) FROM bip_training_app WHERE user_id=? AND practice=1 AND trainingdatetime<CURDATE() + INTERVAL 1 DAY) as TotalNoPractice
						FROM
							`bip_training_app`
						WHERE
							user_id = ?
							AND trainingdatetime<CURDATE() + INTERVAL 1 DAY
						AND trainingdatetime!='0000-00-00 00:00:00' AND trainingdatetime>='2013-01-01'
						AND practice=1
						",array($userid,$userid));
			if ($query->num_rows()>0) {
				$result=$query->result();

				$result[0]->TodayDays=$result[0]->TodayDays+1;
				$result[0]->NoOfWeek=$result[0]->NoOfWeek+1;

				$totalWeek=$result[0]->NoOfWeek;
				$activeFrom=$result[0]->startdate;
				$weekAry=createDateRangeArray($activeFrom,$totalWeek);
				// $weekAry=array_reverse($weekAry);
				$weeksData=array();
				foreach ($weekAry as $wval) {
					$weekstart=$wval['start'];
					$weekend=$wval['end'];
						$weeksData[]=$this->db->query("SELECT count(*) AS weeksData FROM bip_training_app WHERE user_id = ? AND trainingdatetime!='0000-00-00 00:00:00' AND trainingdatetime>='2013-01-01' AND trainingdatetime BETWEEN ? AND ?",array($userid,$weekstart,$weekend))->row()->weeksData;
				}
				$weeksData=join(',',$weeksData);
				$result[0]->weeksData='['.$weeksData.']';

				foreach ($result as $value) {
					$response=$value;
				}
				return $response;
			}
		}
	}

	function getPatientAppReminder($user_id,$difficulty_id){

		$userQry = $this->db->query("SELECT app_reminder FROM bip_user WHERE id=?",array($user_id));

		$row = $userQry->row();

        if($userQry->num_rows()<1 || $row->app_reminder=='[]' || empty($row->app_reminder)){

	        $query = $this->db->query("SELECT app_reminder FROM bip_push_reminder WHERE difficulty_id=?",array($difficulty_id));
	        $row = $query->row();

	        if($query->num_rows()<1 || $row->app_reminder=='[]' || empty($row->app_reminder)){
	             $newQry = $this->db->query("SELECT app_reminder FROM bip_push_reminder WHERE difficulty_id=0");
	             $row= $newQry->row();
	        }

        }

        $app_reminder_arr = json_decode($row->app_reminder);

        $time = array();

        if (!empty($app_reminder_arr)) {

        	foreach ($app_reminder_arr as $k => $v) {
		        $params = explode('~~~', $v);
		        array_push($time, $params[0]);
        	}

        	usort($time, function($a, $b) {
			  $ad = new DateTime($a);
			  $bd = new DateTime($b);

			  if ($ad == $bd) {
			    return 0;
			  }

			  return $ad < $bd ? 1 : -1;
			});

			$time = array_reverse($time);

        }

        $first_index_time = $time[0];

        $appQry = $this->db->query("SELECT reminder_status FROM bip_user_app WHERE user_id=?",array($user_id));
        $row = $appQry->row();

        $status_arr = json_decode($row->reminder_status,true);
        $played_time = $status_arr['played_time'];
        $played_at = $status_arr['played_at'];

        if (strtotime(date("Y-m-d")) > strtotime($played_at)) {
        	$played_time = '';
        }

    	if (!empty($played_time)) {

	    	$key = array_search($played_time, $time);

	    	$newTime = array(); // new time to sent
		    $oldTime = array(); //to count old time

	    	if (is_numeric($key)) {

		    	foreach ($time as $tk => $tv) {
		    		if ($tk>$key)
						array_push($newTime, $tv);
					else
						array_push($oldTime, $tv);
		    	}
	    	}else{
	    		foreach ($time as $tk => $tv) {
		    		if (strtotime($tv)>strtotime($played_time))
						array_push($newTime, $tv);
					else
						array_push($oldTime, $tv);
		    	}
	    	}

    	}else{
    		usort($time, create_function('$a, $b', 'return strcmp($a->hour, $b->hour);'));

        	$time = array_reverse($time);

    		$newTime = $time;
    	}
        if (count($newTime)<1) {
        	$newTime = null;
        }

        $played_count = (count($oldTime)>=1) ? count($oldTime): 0;
        $timelist = array('reminder'=>$newTime,'first_reminder'=>$first_index_time,'played_count'=>$played_count);

        return $timelist;
    }

	function isHideGraph($difficultyId) {
        $query = $this->db->query("SELECT hide_graph FROM bip_difficulty WHERE id=?",array($difficultyId));
        $row = $query->row();
        $this->db->freeDBResource();

        if ($row->hide_graph>0) {
        	return "true";
        }else{
        	return "false";
        }
    }

    /**
     * hide antal in pain reporting
     */
    function isHideNumber($difficultyId) {
        $query = $this->db->query("SELECT hide_number FROM bip_difficulty WHERE id=?",array($difficultyId));
        $row = $query->row();
        $this->db->freeDBResource();

        if ($row->hide_number>0) {
        	return "true";
        }else{
        	return "false";
        }
    }

    function getPatientDifficulty($userId){

    	$query = $this->db->query("SELECT difficulty_id FROM bip_user WHERE id=?",array($userId));
        $row = $query->row();
        $this->db->freeDBResource();

       	return $row->difficulty_id;
    }

    /*Added By Sabin Starts >>*/
	/**
	 * Method that will check whether the patient associated with difficulty is linked with any registration.
	 * @param  [int] $difficulty_id [Difficulty ID]
	 * @return [Array]                [description]
	 * @author Sabin Chhetri <sabin@tulipstechnologies.com>
	 * @date   16th April 2015
	 */
	function userHaveRegistrations($difficulty_id){
		$query = $this->db->query("SELECT COUNT(*) AS totalRows FROM bip_registration_task WHERE  FIND_IN_SET(?,difficulty_id) AND registration_status='1'",array($difficulty_id));
		$row = $query->row();
        $this->db->freeDBResource();
        return $row->totalRows>0 ?  true: false;
	}


	function fetchRegistrations($data){
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$query=$this->db->query("SELECT difficulty_id FROM bip_user WHERE id=? LIMIT 1",array($userid));
			$row=$query->row();
			$difficulty_id=$row->difficulty_id;

			if ($difficulty_id>0)
			{
				//dont fetch empty registration tasks.
				$chunkCheckNotEmpty = "AND (SELECT COUNT(*) FROM bip_registration_steps WHERE registration_id=r.registration_id)>0";

				if($show=="old"){
					//$queryReg = $this->db->query("SELECT r.* FROM bip_registration_task r WHERE FIND_IN_SET(?, r.difficulty_id) AND r.registration_status = '1' AND r.registration_id IN (SELECT registration_id FROM bip_registration_assignments WHERE patient_id=?)  $chunkCheckNotEmpty ORDER BY r.last_updated DESC",array($difficulty_id,$userid));
					$queryReg = $this->db->query("SELECT a.*,r.*,DATE_FORMAT(a.answered_date,'%e %b %Y @ %h:%i %p') AS formatted_answer_date FROM bip_registration_assignments a INNER JOIN bip_registration_task r ON r.registration_id = a.registration_id  WHERE a.patient_id=?  ORDER BY a.answered_date DESC, r.sort_order ASC",array($userid));
				}else{
					$queryReg = $this->db->query("SELECT r.* FROM bip_registration_task r WHERE FIND_IN_SET(?, r.difficulty_id) AND r.registration_status = '1'  $chunkCheckNotEmpty ORDER BY r.sort_order ASC, r.last_updated DESC",array($difficulty_id));
				}

				$result = $queryReg->result();

				//echo $this->db->last_query()."-------------";

				$registrations = array();
				foreach($result as $key=>$value){
					$registrations[$key] = $value;
				}

				return $registrations;
			}
		}
	}

	function fetchRegistrationDetails($data){
		extract($data);
		if($this->validateUserToken($userid,$deviceId,$tokenkey)){
			if($flow_type==2){ //multiple flows
				//don't list already answered flow here
				//echo "SHOW = ".$show;
				if($show=="old"){
					$queryflow = $this->db->query("SELECT f.*, p.flow_page_title FROM bip_registration_flows f INNER JOIN bip_registration_flow_page p ON f.flow_page_id = p.flow_page_id WHERE f.flow_status=1 AND f.registration_id=? AND flow_id IN(SELECT flow_id FROM bip_registration_assignments WHERE registration_id=? AND patient_id=?) ORDER BY last_updated DESC",array($registration_id,$registration_id,$userid));
				}else{
					$queryflow = $this->db->query("SELECT f.*, p.flow_page_title FROM bip_registration_flows f INNER JOIN bip_registration_flow_page p ON f.flow_page_id = p.flow_page_id WHERE f.flow_status=1 AND f.registration_id=? AND flow_id NOT IN(SELECT flow_id FROM bip_registration_assignments WHERE registration_id=? AND patient_id=?) ORDER BY last_updated DESC",array($registration_id,$registration_id,$userid));
				}

				$result = $queryflow->result();
				$reg_flows = array();
				foreach($result as $key=>$value){
					$reg_flows[$key] = $value;
					//$qry = $this->db->query("SELECT * FROM bip_registration_steps WHERE step_status='1' AND registration_id=? AND flow_id=?",array($registration_id));
				}

				return $reg_flows;
			}
		}
	}

	function fetchRegistrationSteps($data){
		extract($data);
		if($this->validateUserToken($userid,$deviceId,$tokenkey)){
			if($flow_type==2){
				$query = $this->db->query("SELECT * FROM bip_registration_steps WHERE flow_id=? AND registration_id=? AND step_status='1' ORDER BY sort_order ASC",array($flow_id,$registration_id));
			}else{
				$query = $this->db->query("SELECT * FROM bip_registration_steps WHERE registration_id=? AND step_status='1' ORDER BY sort_order ASC",array($registration_id));
			}

			$result = $query->result();
			//echo $this->db->last_query();
			$stepsArr = array();
			$a = 0;
			foreach($result as $rs){
				$stepsArr[$a]["step_id"] = $rs->step_id;
				$stepsArr[$a]["step_name"] = $rs->step_name;
				$stepsArr[$a]["registration_id"] = $registration_id;
				$stepsArr[$a]["flow_id"] = $rs->flow_id;
				$stepsArr[$a]["is_multiple_choice"] = $rs->is_multiple_choice;
				$stepsArr[$a]["max_selection_allowed"] = $rs->max_selection_allowed;
				$stepsArr[$a]["template"] = $rs->template;
				$stepsArr[$a]["show_date"] = $rs->show_date;
				$stepsArr[$a]["show_time"] = $rs->show_time;
				$stepsArr[$a]["current_date"] = date("d M Y");
				$stepsArr[$a]["current_time"] = date("H:i");
				$stepsArr[$a]["hid_date"] = date("Y-m-d");
				$stepsArr[$a]["hid_time"] = date("H:i:s");
				$stepsArr[$a]["time_format"] = $rs->time_format;
				$stepsArr[$a]["answer_text"] = html_entity_decode($rs->answer_text);
				$stepsArr[$a]["button_text"] = $rs->button_text;
				$stepsArr[$a]["allow_custom_answer"] = $rs->allow_custom_answer;
				$stepsArr[$a]["allow_edit"] = $rs->allow_edit;
				$stepsArr[$a]["allow_to_add_answer_category"] = $rs->allow_to_add_answer_category;
				$stepsArr[$a]["sort_order"] = $rs->sort_order;
				$stepsArr[$a]["show_order"] = $a+1;

				if($rs->template=="steps_expand_collapse"){ //if step is expand/collapse type, then fetch category as well
					//$qryAnsCat = $this->db->query("SELECT * FROM bip_registration_custom_answer_category WHERE step_id=? AND user_id=? ORDER BY sort_order ASC",array($rs->step_id,$userid));
					$qryAnsCat = $this->db->query("SELECT *,1 as sortkey  FROM bip_registration_answer_category WHERE step_id=? AND answer_type='standard' AND answer_cat_status='1'
						UNION SELECT *, 2 AS sortkey FROM bip_registration_answer_category WHERE step_id=? AND answer_type='custom' AND belongs_to=? AND answer_cat_status='1'
						ORDER BY sortkey ASC, sort_order ASC",array($rs->step_id,$rs->step_id,$userid));
					//$stepsArr[$a]["last_query"] = $this->db->last_query();
					$resultCat = $qryAnsCat->result();
					$b=0;
					foreach($resultCat as $rsc){
						$stepsArr[$a]["category"][$b]["answer_cat_id"]=$rsc->answer_cat_id;
						$stepsArr[$a]["category"][$b]["answer_cat_name"]=$rsc->answer_cat_name;
						$qryAns = $this->db->query("SELECT *, 1 as sortkey FROM bip_registration_answers WHERE step_id=? AND answer_type='standard' AND answer_cat_id=? AND answer_status='1'
								  UNION SELECT *, 2 as sortkey FROM bip_registration_answers WHERE step_id=? AND answer_type='custom' AND answer_cat_id=? AND belongs_to=? AND answer_status='1'
						 		  ORDER BY sortkey ASC, sort_order ASC",array($rs->step_id,$rsc->answer_cat_id,$rs->step_id,$rsc->answer_cat_id,$userid));
						$resultAns = $qryAns->result();
						$c=0;
						foreach($resultAns as $rsa){
							$stepsArr[$a]["category"][$b]["answers"][$c]["answer_id"] = $rsa->answer_id;
							$stepsArr[$a]["category"][$b]["answers"][$c]["answer"] = $rsa->answer;

							$c++;
						}
						$b++;
					}
				}else{
					$qryAns = $this->db->query("SELECT *,1 as sortkey FROM bip_registration_answers WHERE step_id=? AND answer_type='standard' AND answer_status='1'
								UNION SELECT *, 2 as sortkey FROM bip_registration_answers WHERE step_id=? AND answer_type='custom' AND belongs_to=? AND answer_status='1'
					 			ORDER BY sortkey ASC, sort_order ASC",array($rs->step_id,$rs->step_id,$userid));

					//$stepsArr[$a]["last_query"] = $this->db->last_query();
					$resultAns = $qryAns->result();
					$b=0;
					foreach($resultAns as $rsa){
						$stepsArr[$a]["answers"][$b]["answer_id"] = $rsa->answer_id;
						$stepsArr[$a]["answers"][$b]["answer"] = $rsa->answer;

						$b++;
					}
				}
				$a++;
			}
			$strReturnVar["steps"] = $stepsArr;

			//if user is viewing earlier registrations, then fetch details of the assignment too
			if($assignment_id>0){ //User has already answered the requested registration.
				//get old assignment
				$getassignmentinfo = $this->db->query("SELECT *,DATE_FORMAT(incident_date,'%e %b %Y') AS f_incident_date,DATE_FORMAT(incident_time,'%H:%i') as f_incident_time FROM bip_registration_assignments WHERE assignment_id=?",array($assignment_id));
				$strReturnVar["old_assignment"]["assignment"] = $getassignmentinfo->row_array();

				$fetchassignments = $this->db->query("SELECT * FROM bip_registration_assignments_details WHERE assignment_id=?",array($assignment_id));
				$result = $fetchassignments->result();
				$existing_assignments = array();
				foreach($result as $key=>$value){
					$existing_assignments[$key] = $value;
				}
				$strReturnVar["old_assignment"]["details"] = $existing_assignments;
			}else{
				$strReturnVar["old_assignment"] = null;
			}

			return $strReturnVar;
		}
	}

	function saveRegistration($data){
		extract($data);
		if($this->validateUserToken($userid,$deviceId,$tokenkey))
		{

			$step_data = array();
			parse_str($form_data,$step_data);

			$answers = $step_data["step_answers"];
			$newCatArray = array();
			$registration_id = $step_data["registration_id"];
			$assign_id = $step_data["reg_assignment_id"];
			$flow_id = $step_data["reg_flow_id"];
			$uniqid = date("YmdHis")+strtotime("now");
			$current_date = date("Y-m-d H:i:s");

			$sanswer = array();
			foreach($answers as $answer){
				$sanswer[] = array_filter($answer);
			}

			//echo "<pre>".print_r($step_data,true)."</pre>"; exit;


			$step_answers = array();
   			array_walk_recursive($sanswer, function($a) use (&$step_answers) { $step_answers[] = $a; });

   			/*echo "<pre>";
   			print_r($step_answers);
   			echo "</pre>"; exit;*/

   			if($assign_id>0){ //records being edited
   				$ins_main = $this->db->query("UPDATE bip_registration_assignments SET
												incident_date = ?,
												incident_time = ?,
												answered_date = '".$current_date."' WHERE assignment_id=?
											",array($step_data["incident_date"],$step_data["incident_time"],$assign_id));
   			}else{

				$ins_main = $this->db->query("INSERT INTO bip_registration_assignments SET
												assignment_code = '".$uniqid."',
												registration_id = ?,
												flow_id = ?,
												patient_id = ?,
												incident_date = ?,
												incident_time = ?,
												answered_date = '".$current_date."'
											",array($registration_id,$flow_id,$userid,$step_data["incident_date"],$step_data["incident_time"]));
			}
			if($ins_main){
				$assignment_id = ($assign_id>0) ? $assign_id : $this->db->insert_id();
				$this->db->query("DELETE FROM bip_registration_assignments_details WHERE assignment_id=?",array($assignment_id));
				//foreach start
				foreach($step_answers as $step_answer){
						$sa = json_decode($step_answer);

						if($sa->is_answer_category==1){
							$getmaxCat = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_answer_category WHERE step_id= ? AND belongs_to=? AND answer_type='custom'",array($sa->step_id,$userid))->row();
		            			$new_cat_order =  $getmaxCat->max_sort_order+1;

		            			//Now insert new answer category
		            			$ins_ans_cat = $this->db->query("INSERT INTO bip_registration_answer_category SET
		            											answer_cat_name =?,
		            											step_id = ?,
		            											belongs_to = ?,
		            											created_by = ?,
		            											added_by	='patient',
		            											added_date	= '".date("Y-m-d H:i:s")."',
		            											last_updated ='".date("Y-m-d H:i:s")."',
		            											answer_cat_status = 1,
		            											sort_order 	=?
		            											",array($sa->custom_answer_cat,$sa->step_id,$userid,$userid,$new_cat_order));
		            			$new_cat_id = $this->db->insert_id();
		            			$temp_id = $sa->temp_id;
		            			$newCatArray[$temp_id] = $new_cat_id;

						}else{
							$getmax = $this->db->query("SELECT MAX(sort_order) as max_sort_order FROM bip_registration_answers WHERE step_id= ? AND belongs_to=? AND answer_type='custom'",array($sa->step_id,$userid))->row();
		            		$neworder =  $getmax->max_sort_order+1;

							if($sa->is_member_of_new_cat==1){ //The answer is member of new category, hence the answer is custom
								$insert_answer = $this->db->query("INSERT INTO bip_registration_answers SET
													answer = ?,
													step_id = ?,
													answer_cat_id = ?,
													belongs_to = ?,
													created_by = ?,
													added_by = 'patient',
													added_date = '".date("Y-m-d H:i:s")."',
													last_updated = '".date("Y-m-d H:i:s")."',
													answer_status = '1',
													answer_type = 'custom',
													sort_order = '".$neworder."'",array($sa->custom_answer,$sa->step_id,$newCatArray[$sa->temp_id],$userid,$userid));

								$answer_id = $this->db->insert_id();
								$step_id = $sa->step_id;
							}else{
								if($sa->is_custom_answer==1)
								{
									$insert_answer = $this->db->query("INSERT INTO bip_registration_answers SET
														answer = ?,
														step_id = ?,
														answer_cat_id = ?,
														belongs_to = ?,
														created_by = ?,
														added_by = 'patient',
														added_date = '".date("Y-m-d H:i:s")."',
														last_updated = '".date("Y-m-d H:i:s")."',
														answer_status = '1',
														answer_type	='custom',
														sort_order = '".$neworder."'",array($sa->custom_answer,$sa->step_id,isset($sa->answer_cat_id)?$sa->answer_cat_id:0,$userid,$userid));

									$answer_id = $this->db->insert_id();
									$step_id = $sa->step_id;
								}else{
									$answer_id = $sa->answer_id;
									$step_id = $sa->step_id;
								}

							}

							if(!isset($sa->is_checked) || (isset($sa->is_checked) && $sa->is_checked===true))
							{
								$sql = $this->db->query("INSERT INTO bip_registration_assignments_details SET
									assignment_id = ?,
									registration_id =?,
									flow_id =?,
									step_id =?,
									answer_id =?,
									assignment_code = ?",
									array($assignment_id,$registration_id,$flow_id,$step_id,$answer_id,$uniqid));
							}

						}
					}
				}
				//foreach end
			}



		return array("success");
	}
	/*Added by sabin ends <<*/

	/*Added by sabin 25th June >>*/
	function homeworkCounts($userID, $difficultyID){
		$res = $this->db->query("SELECT (SELECT  COUNT(*) FROM bip_my_homework_assignment ha
									LEFT JOIN bip_my_homework hw ON hw.homework_id= ha.homework_id
								WHERE FIND_IN_SET(?,hw.difficulty_id) AND ha.patient_id=? AND ha.is_published='1') AS total_homeworks,
									(SELECT  COUNT(*) FROM bip_my_homework_assignment ha
										LEFT JOIN bip_my_homework hw ON hw.homework_id= ha.homework_id
									WHERE FIND_IN_SET(?,hw.difficulty_id) AND ha.patient_id=? AND ha.is_published='1' AND already_viewed='0') AS new_homeworks",array($difficultyID,$userID,$difficultyID,$userID))->row();

		$arr["total_homeworks"] = $res->total_homeworks;
		$arr["new_homeworks"] = $res->new_homeworks;
		return $arr;
	}

	function crisisplanCounts($userID, $difficultyID){
		$res = $this->db->query("SELECT (SELECT
							  COUNT(*)
							FROM
							  bip_my_crisis_plan
							WHERE belongs_to = ?
							  AND difficulty_id = ? AND plan_type = 'custom'
							  AND plan_status = '1') AS total_crisis_plans,
							  (SELECT
							  COUNT(*)
							FROM
							  bip_my_crisis_plan
							WHERE belongs_to = ?
							  AND difficulty_id = ? AND plan_type = 'custom'
							  AND plan_status = '1' AND already_read='0')  AS new_crisis_plans",array($userID,$difficultyID,$userID,$difficultyID))->row();

		$arr["total_crisis_plans"] = $res->total_crisis_plans;
		$arr["new_crisis_plans"] = $res->new_crisis_plans;
		return $arr;
	}


	function fetchHomeworks($data){
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$query=$this->db->query("SELECT difficulty_id FROM bip_user WHERE id=? LIMIT 1",array($userid));
			$row=$query->row();
			$difficulty_id=$row->difficulty_id;

			if ($difficulty_id>0)
			{
				$res= $this->db->query("SELECT
										  hw.headline, hw.contents, hw.homework_id, ha.already_viewed, ha.assignment_id,
										  CONCAT_WS(' ',SwedishDayName(DATE_FORMAT(ha.published_date,'%W')),DATE_FORMAT(ha.published_date,'%d %M %Y')) AS published_date
										FROM
										  bip_my_homework_assignment ha
										  LEFT JOIN bip_my_homework hw
										    ON hw.homework_id = ha.homework_id
										WHERE FIND_IN_SET(
										    ?,
										    hw.difficulty_id
										  )
										  AND ha.patient_id = ?
										  AND ha.is_published = '1'
										ORDER BY ha.already_viewed ASC, ha.published_date DESC", array($difficulty_id,$userid));

				$result = $res->result();

				//echo $this->db->last_query()."-------------";

				$homeworks = array();

				foreach($result as $key=>$value){
					$homeworks[$key] = $value;
				}

				return $homeworks;

			}
		}
	}

	function markHomeworkRead($data){
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$res = $this->db->query("UPDATE bip_my_homework_assignment SET already_viewed='1' WHERE assignment_id=? AND patient_id=?", array($assignmentId,$userid));
			if($res){
				return "success";
			}
		}
	}



	function fetchCrisisplans($data){
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$query=$this->db->query("SELECT difficulty_id FROM bip_user WHERE id=? LIMIT 1",array($userid));
			$row=$query->row();
			$difficulty_id=$row->difficulty_id;

			if ($difficulty_id>0)
			{
				$res= $this->db->query("SELECT
										  cp.headline,
										  cp.contents,
										  cp.plan_id,
										  cp.already_read,
										  cp.updated_at
										FROM
										  bip_my_crisis_plan cp
										WHERE FIND_IN_SET(?, cp.difficulty_id)
										  AND belongs_to = ?
										  AND plan_status = '1'
										ORDER BY cp.already_read ASC,
										  cp.updated_at DESC ", array($difficulty_id, $userid));

				$result = $res->result();

				//echo $this->db->last_query()."-------------";

				$crisisplans = array();

				foreach($result as $key=>$value){
					$crisisplans[$key] = $value;
				}

				return $crisisplans;

			}
		}
	}

	function markCrisisplanRead($data){
		extract($data);
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$res = $this->db->query("UPDATE bip_my_crisis_plan SET already_read='1' WHERE plan_id=? AND belongs_to=?", array($planId,$userid));
			if($res){
				return "success";
			}
		}
	}
	/*Added by sabin 25th June <<*/
}
