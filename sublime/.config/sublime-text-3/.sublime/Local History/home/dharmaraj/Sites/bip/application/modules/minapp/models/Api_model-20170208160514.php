<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class api_model extends CI_Model {

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

    function decode5t($str) {
        for ($i = 0; $i < 5; $i++) {
            $str = base64_decode(strrev($str)); //apply base64 first and then reverse the string}
        }
        return $str;
    }

	function validateuser($data)
	{
		$username=$data['username'];
		$password=$data['password'];
		$deviceId=$data['deviceId'];
		$UrbanAirshipId=$data['UrbanAirshipId'];
		$devicetype=$data['devicetype'];
		$tokenkey=$data['tokenkey'];
		$webLogin = $data["webLogin"];
		$DeviceUUID = $data["DeviceUUID"];
		$isWebVersion = $data["isWebVersion"];

		if (empty($deviceId)) return false;
			if($webLogin=="true" || trim($webLogin)==""){

				$strSql = "SELECT * FROM bip_user WHERE username=? ";
				$strSql .= "AND status='1' AND app_status='1' AND user_role='1' AND (CURDATE() BETWEEN STR_TO_DATE(active_from, '%Y-%m-%d') AND STR_TO_DATE(active_to,'%Y-%m-%d'))";
				$bindArray = array($username);
			}else{
				$strSql = "SELECT u.* FROM bip_user u INNER JOIN bip_user_activation_codes c ON c.user_id=u.id WHERE c.device_uuid=? ";
				$strSql .= "AND u.status='1' AND u.app_status='1' AND u.user_role='1' AND (CURDATE() BETWEEN STR_TO_DATE(u.active_from, '%Y-%m-%d') AND STR_TO_DATE(u.active_to,'%Y-%m-%d'))";
				$bindArray = array($DeviceUUID);
			}
			$query = $this->db->query($strSql,$bindArray);

			//echo $this->db->last_query();

			if ($query->num_rows() == 1) {
				$user_data = $query->row();

			                if (!$this->bcrypt->check_password($password,$user_data->password)) return "patient_inactive";

				//check if user is logging in from mobile but he is only allowed to login from web version of the app, then don't let user login from app
				//below check greater than 10 to make sure the variable doesnot consist of blank space, or a null value. This should have long value.
				if(strlen($UrbanAirshipId)>10)//means user logged in from device
				{
					$loggedinVia = "device";
				}else{
					$loggedinVia = "web";
				}

				if($loggedinVia=="device" && $user_data->app_web_version==1){//user is set to use web version of app only, but he is trying to login from device
					return "only_web_version";
				}
// dd($user_data);
				if($loggedinVia=="web"  && $user_data->app_web_version==0){//user is set to use mobile app version only, but he is trying to login from web.
					return "only_mobile_version";
				}



				if (empty($devicetype)) { //postman only
					$q=$this->db->query("SELECT UUID() NewUUID");
					if($q->num_rows()>0){
						foreach($q->result() as $r){
							$tokenkey=$r->NewUUID;
						}
					}
				}

				$userId=$user_data->id;

				$lastlogdate = date("Y-m-d");
				$this->db->query("INSERT INTO bip_user_app (user_id,deviceId, UrbanAirshipId, devicetype,tokenkey,created_at,updated_at) VALUES (?,?,?,?,?,?,now()) ". "ON DUPLICATE KEY UPDATE UrbanAirshipId=VALUES(UrbanAirshipId),tokenkey=VALUES(tokenkey),devicetype=VALUES(devicetype),deviceId=VALUES(deviceId)",array($userId,$deviceId,$UrbanAirshipId,$devicetype,$tokenkey,$lastlogdate));

				$response = $this->getUserInformation($user_data,$deviceId,$tokenkey);

				$this->db->query("UPDATE bip_user SET last_sync_date=now() WHERE id=?",array($userId));

				$response->password = $password;

				return $response;

			}else{ // this is added by Sabin, we should know whether user type incorrect login credentials or simply the app for them are not activated
				$strSql = "SELECT * FROM bip_user WHERE username=? AND password=? ";
				$query = $this->db->query($strSql,array($username,$password));
				if($query->num_rows() == 1){
						return "patient_inactive";
				}
			}
	}

	function getUserInformation($user_data,$deviceId,$tokenkey){
				$response = new stdClass;
				$response->userid=$user_data->id;
				$response->username=$user_data->username;
				$response->password=$this->decode5t($user_data->password);
				$response->Name=$this->encryption->decrypt($user_data->first_name).' '.$this->encryption->decrypt($user_data->last_name);

				$user_app_row = $this->db->query("SELECT stage_number FROM bip_user_app WHERE user_id=?",array($user_data->id))->row();
			    $last_updated_json = $user_app_row->stage_number;
			    $json_arr = json_decode($last_updated_json,true);
        		$response->stage_number = $json_arr['number'];
        		$response->message_count = $this->totalMessage($user_data->id);

				$newstartpage = $this->db->query("SELECT tag, enable_msg_alert,hide_graph FROM bip_difficulty WHERE id=?",array($user_data->difficulty_id))->row();
				$response->new_start_page = $newstartpage->tag;
				$response->enable_msg_alert = $newstartpage->enable_msg_alert;
				$response->hide_graph = $newstartpage->hide_graph;



				$flow = $this->getTreatmentFlow($user_data->difficulty_id);
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

				$inputObj = array(
				'userid' => $user_data->id,
				'deviceId' => $deviceId,
				'tokenkey' => $tokenkey
				);

				$response->flow_rating = $flow->rating;
				$response->reminder = $this->getPatientAppReminder($inputObj);
				$response->feedback = $this->getFeedbackMessage($inputObj);

				$response->hasRegistration = $this->userHaveRegistrations($user_data->difficulty_id);
				$response->specialAnswers = $this->fetchSpecialAnswers($user_data->id, $user_data->difficulty_id);
				$response->homeworks = $this->homeworkCounts($user_data->id, $user_data->difficulty_id);
				$response->crisisplans = $this->crisisplanCounts($user_data->id, $user_data->difficulty_id);
				$response->available_modules = $this->getActiveModules($inputObj);
				return $response;
	}

	function totalMessage($userId) {

        $query = $this->db->query("SELECT COUNT(id) AS total_new FROM bip_message WHERE patient_inbox=1 AND status_receiver=0 AND receiver_id=? AND message_type=0",array($userId));
        $result = $query->row();
        return $result->total_new;
    }

    function appMsgAlertEnabled($userId){
    	$get = $this->db->query("SELECT difficulty_id FROM bip_user WHERE id=?", array($userId))->row();
    	$difficulty_id = $get->difficulty_id;

    	$query = $this->db->query("SELECT enable_msg_alert FROM bip_difficulty WHERE id=?",array($difficulty_id));
        $result = $query->row();
        return $result->enable_msg_alert;
    }

	function fetchSpecialAnswers($userid,$diffid){
		//FETCH PATIENT SPECIFIC SPECIAL ANSWERS
  		$psaQry = $this->db->query("SELECT * FROM bip_reg_patient_selected_special_answer WHERE patient_id=? AND difficulty_id=?",array($userid,$diffid));
  		$psaRow = $psaQry->row();
  		$this->db->freeDBResource();
  		return $psaRow->selected_answers;
	}

	function validateUserToken($userid,$deviceId,$tokenkey,$deviceUUID=""){
		// $query=$this->db->query("SELECT * FROM bip_user_app WHERE user_id='$userid' AND deviceId='$deviceId' AND tokenkey='$tokenkey'");
		/*if($deviceUUID!=""){
			$query=$this->db->query("SELECT * FROM bip_user_activation_codes WHERE user_id=? AND device_uuid=? AND is_activated='1'", array($userid,$deviceUUID));
			if ($query->num_rows()>0) {
				return true;
			}else{
				return false;
			}
		}else{
			$query=$this->db->query("SELECT * FROM bip_user_app WHERE user_id=? AND (deviceId=? || deviceId='1')",array($userid,$deviceId));
			if ($query->num_rows()>0) {
				return true;
			}else{
				return false;
			}
		}*/

		// no needed to check , check in controller for a api request
		return true;
	}


	function syncDataToServer($data)
	{
		// echo "<pre>";print_r($data);exit;
		extract($data);

		//Don't let sync data from old previously activated devices. That means if user activates new device and he tries to sync with old device then don't let user sync
		if($webLogin!=""){
			$chkvalidDevice = $this->db->query("SELECT * FROM bip_user_activation_codes WHERE user_id=? AND device_uuid=?", array($userid,$DeviceUUID));
			if(!$chkvalidDevice->num_rows()>0){
				$returnarr["error"] = "error";
				$returnarr["sql"] = $this->db->last_query();
				$returnarr["error_code"] = "404";
				$returnarr["error_message"] = "Device not valid";

				return $returnarr;
				exit;
			}
		}

		$current_date = date("Y-m-d H:i:s");
		//SYNC TRAINING DATA
		// save sync training data
		if(count($offlinedata['training'])>0){
				foreach ($offlinedata['training'] as $k1 => $training) {
					$taskId = $training['taskid'];

						$trainingId = $training['trainingId'];
						$trainingdatetime = $training['trainingdatetime'];
						$comment = $training['comment'];
						$estimatedvalue = $training['estimatedvalue'];
						$estimatedvalue_end = $training['estimatedvalue_end'];
						$training_duration = $training['training_duration'];
						$type = $training['type'];
						$comment = $training['comment'];
						$edited = $training['edited'];

						if (is_numeric($trainingId) && $trainingId >0 ) { // old training update only
							if ($edited>0) {
								if ($type==1) {
									$this->db->query("UPDATE bip_training_app SET trainingdatetime=?,comment=?,estimatedvalue=?,practice='1' WHERE id=?",array($trainingdatetime,$comment,$estimatedvalue,$trainingId));
								}else{
									$this->db->query("UPDATE bip_training_app SET trainingdatetime=?,comment=?,estimatedvalue=?,estimatedvalue_end=?,training_duration=?,type=?,practice='1' WHERE id=?",array($trainingdatetime,$comment,$estimatedvalue,$estimatedvalue_end,$training_duration,$type,$trainingId));
								}
							}

						}else{ // new training insert only
							$lastlogdate = date("Y-m-d");
							if ($type==1) {
								$this->db->query("INSERT INTO bip_training_app (user_id,task_id,trainingdatetime,comment, estimatedvalue, practice,created_at,updated_at) VALUES (?,?,?,?,?,1,?,now())",array($userid,$taskId,$trainingdatetime,$comment,$estimatedvalue,$lastlogdate));

							}else{
								$this->db->query("INSERT INTO bip_training_app (user_id,task_id,trainingdatetime,comment, estimatedvalue,estimatedvalue_end,training_duration,type, practice,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,1,?,now())",array($userid,$taskId,$trainingdatetime,$comment,$estimatedvalue,$estimatedvalue_end,$training_duration,$type,$lastlogdate));
							}

							// insert comment only for new training excluding training update
							if (!empty($comment)) {
								$this->notifyCommentToPsy($userid,$taskId,$comment);
							}

						}
				}
		}




		//SAVE REGISTRATION DATA START
		//answers
		$answers = $offlinedata["Registraion"]["answers"];
		$diffid = $this->getPatientDifficulty($userid);
		if(count($answers)>0){
			foreach($answers as $k2=>$answer){
				if($answer["answer_id"]==0){ //insert new answers
					$ins = $this->db->query("INSERT INTO bip_registration_answers SET
												answer            = ?,
												step_id           = ?,
												answer_cat_id     = ?,
												added_date        = '$current_date',
												answer_status     = '1',
												sort_order        = ?,
												answer_type       = 'custom',
												created_by        = ?,
												belongs_to        = ?,
												added_by          = 'patient',
												mapped_answer_id  = '0',
												special_answer    = ?,
												app_answer_id     = ?,
												difficulty_id	  = ?
											", array($answer["answer"], $answer["step_id"], $answer["answer_cat_id"]>0 ? $answer["answer_cat_id"]:0, $answer["sort_order"], $userid, $userid, is_numeric($answer["special_answer"]) ? $answer["special_answer"] : 0, $answer["app_answer_id"], ($answer["special_answer"]==1) ? $diffid : 0));
					if($ins){
						$answerid = $this->db->insert_id();
						if($answer["special_answer"]==1){
							$selectedSpecialAns = $this->db->query("SELECT selected_answers FROM bip_reg_patient_selected_special_answer WHERE patient_id=?",array($userid))->row();
							if($selectedSpecialAns->selected_answers!=""){
									$exSel = explode(",", $selectedSpecialAns->selected_answers);
									array_push($exSel, $answerid);

									$selected_answers = implode(",", $exSel);
							}else{
								$selected_answers = $answerid;
							}

							$this->db->query("DELETE FROM bip_reg_patient_selected_special_answer WHERE patient_id=?", array($userid));
							$this->db->query("INSERT INTO  bip_reg_patient_selected_special_answer SET selected_answers=?, patient_id=?, difficulty_id=?", array($selected_answers,$userid,$diffid));
						}
					}
				}else{ //edit
					$this->db->query("UPDATE  bip_registration_answers SET
												answer  			= ?,
												step_id  			= ?,
												answer_cat_id  		= ?,
												answer_status  		= '1',
												sort_order  		= ?,
												answer_type  		= 'custom',
												created_by  		= ?,
												belongs_to  		= ?,
												added_by  			= 'patient',
												mapped_answer_id  	= '0',
												special_answer  	= ? WHERE answer_id = ?
											", array($answer["answer"], $answer["step_id"], $answer["answer_cat_id"]>0 ? $answer["answer_cat_id"]:0, $answer["sort_order"], $userid, $userid, $answer["special_answer"], $answer["answer_id"]));
				}
			}
		}
		//answer cat
		$answercat = $offlinedata["Registraion"]["answercat"];
		if(count($answercat)>0){
			foreach($answercat as $k3=>$answer_cat){
				if($answer_cat["answer_cat_id"]==0){ //insert
					$ins = $this->db->query("INSERT INTO bip_registration_answer_category SET
													answer_cat_name		= ?,
													step_id				= ?,
													added_date			= '$current_date',
													answer_cat_status	= '1',
													sort_order			= ?,
													answer_type			= 'custom',
													created_by			= ?,
													belongs_to			= ?,
													added_by			= 'patient',
													mapp_cat_id			= '0'
											", array($answer_cat["answer_cat_name"], $answer_cat["step_id"], $answer_cat["sort_order"], $userid, $userid));
					if($ins)
					{
							$answer_cat_id = $this->db->insert_id();
							$answers = $answer_cat["answer"];
							foreach($answers as $k2=>$answer)
							{
								if($answer["answer_id"]==0){ //insert new answers
									$this->db->query("INSERT INTO bip_registration_answers SET
																answer	  			= ?,
																step_id	  			= ?,
																answer_cat_id	  	= ?,
																added_date	  		= '$current_date',
																answer_status	  	= '1',
																sort_order	  		= ?,
																answer_type	  		= 'custom',
																created_by	  		= ?,
																belongs_to	  		= ?,
																added_by	  		= 'patient',
																mapped_answer_id	= '0',
																special_answer	  	= '0',
																app_answer_id		= ?
															", array($answer["answer"], $answer["step_id"], $answer_cat_id, $answer["sort_order"], $userid, $userid, $answer["app_answer_id"]));
								}else{ //edit
									$this->db->query("UPDATE  bip_registration_answers SET
																answer	  			= ?,
																step_id	  			= ?,
																answer_cat_id	  	= ?,
																answer_status	  	= '1',
																sort_order	  		= ?,
																answer_type	  		= 'custom',
																created_by	  		= ?,
																belongs_to	  		= ?,
																added_by	  		= 'patient',
																mapped_answer_id	= '0',
																special_answer	  	= '0' WHERE answer_id = ?
															", array($answer["answer"], $answer["step_id"], $answer_cat_id, $answer["sort_order"], $userid, $userid, $answer["answer_id"]));
								}
							}
					}
				}else{ //edit
					$ins = $this->db->query("UPDATE  bip_registration_answer_category SET
													answer_cat_name		= ?,
													step_id				= ?,
													added_date			= '$current_date',
													answer_cat_status	= '1',
													sort_order			= ?,
													answer_type			= 'custom',
													created_by			= ?,
													belongs_to			= ?,
													added_by			= 'patient',
													mapp_cat_id			= '0' WHERE answer_cat_id = ?
											", array($answer_cat["answer_cat_name"], $answer_cat["step_id"], $answer_cat["sort_order"], $userid, $userid, $answer_cat["answer_cat_id"]));
					if($ins){
							$answer_cat_id = $answer_cat["answer_cat_id"];
							$answers = $answer_cat["answer"];
							foreach($answers as $k2=>$answer)
							{
								if($answer["answer_id"]==0){ //insert new answers
									$this->db->query("INSERT INTO bip_registration_answers SET
																answer	  			= ?,
																step_id	  			= ?,
																answer_cat_id	  	= ?,
																added_date	  		= '$current_date',
																answer_status	  	= '1',
																sort_order	  		= ?,
																answer_type	  		= 'custom',
																created_by	  		= ?,
																belongs_to	  		= ?,
																added_by	  		= 'patient',
																mapped_answer_id	= '0',
																special_answer	  	= '0',
																app_answer_id		= ?
															", array($answer["answer"], $answer["step_id"], $answer_cat_id, $answer["sort_order"], $userid, $userid, $answer["app_answer_id"]));
								}else{ //edit
									$this->db->query("UPDATE  bip_registration_answers SET
																answer	  			= ?,
																step_id	  			= ?,
																answer_cat_id	  	= ?,
																answer_status	  	= '1',
																sort_order	  		= ?,
																answer_type	  		= 'custom',
																created_by	  		= ?,
																belongs_to	  		= ?,
																added_by	  		= 'patient',
																mapped_answer_id	= '0',
																special_answer	  	= '0' WHERE answer_id = ?
															", array($answer["answer"], $answer["step_id"], $answer_cat_id, $answer["sort_order"], $userid, $userid, $answer["answer_id"]));
								}
							}
					}
				}
			}
		}

		//registration assignments
		$regassignments = $offlinedata["Registraion"]["patientAssignment"];
		if(count($regassignments)>0){
			foreach($regassignments as $assignments){
				if($assignments["assignment_id"]==0){//insert
						$insAss = $this->db->query("INSERT INTO bip_registration_assignments SET
													assignment_code    = 'app',
													registration_id    = ?,
													flow_id            = '0',
													patient_id         = ?,
													incident_date      = ?,
													incident_time      = ?,
													answered_date      = ?,
													date_only		   = ?,
													stage_id           = ?", array($assignments["registration_id"], $userid, $assignments["incident_date"], $assignments["incident_time"], $assignments["answered_date"], $assignments["date_only"], $assignments["stage_id"]));
						if($insAss){
								$assignment_id = $this->db->insert_id();
								$assdetails  = $assignments["assignment_detail"];
								foreach($assdetails as $aDetails){
										if($aDetails["answer_id"]==0){
											$ChkanswerID = $this->db->query("SELECT answer_id FROM bip_registration_answers WHERE app_answer_id=? LIMIT 1",array($aDetails["app_answer_id"]))->row();
											$answerID = $ChkanswerID->answer_id;
										}else{
											$answerID = $aDetails["answer_id"];
										}
										$this->db->query("INSERT INTO bip_registration_assignments_details SET
																assignment_id		= '$assignment_id',
																registration_id		= ?,
																flow_id				= '0',
																step_id				= ?,
																answer_id			= ?,
																assignment_code		= 'app'
														", array($aDetails["registration_id"], $aDetails["step_id"], $answerID));
								}
						}
				}else{//edit
					$updateAss =  $this->db->query("UPDATE bip_registration_assignments SET
													assignment_code    = 'app',
													registration_id    = ?,
													flow_id            = '0',
													patient_id         = ?,
													incident_date      = ?,
													incident_time      = ?,
													answered_date      = ?,
													date_only		   = ?,
													stage_id           = ? where assignment_id=?", array($assignments["registration_id"], $userid, $assignments["incident_date"], $assignments["incident_time"], $assignments["answered_date"], $assignments["date_only"], $assignments["stage_id"], $assignments["assignment_id"]));
					$assignment_id  = $assignments["assignment_id"];

					//first delete details and re insert it
					$this->db->query("DELETE FROM bip_registration_assignments_details WHERE assignment_id=?", array($assignment_id));
					$assdetails  = $assignments["assignment_detail"];
					foreach($assdetails as $aDetails){
							if($aDetails["answer_id"]==0){
								$ChkanswerID = $this->db->query("SELECT answer_id FROM bip_registration_answers WHERE app_answer_id=? LIMIT 1",array($aDetails["app_answer_id"]))->row();
								$answerID = $ChkanswerID->answer_id;
							}else{
								$answerID = $aDetails["answer_id"];
							}
							$this->db->query("INSERT INTO bip_registration_assignments_details SET
													assignment_id		= '$assignment_id',
													registration_id		= ?,
													flow_id				= '0',
													step_id				= ?,
													answer_id			= ?,
													assignment_code		= 'app'
											", array($aDetails["registration_id"], $aDetails["step_id"], $answerID));
					}
				}
			}
		}

		//HOMEWORK ASSIGNMENTS
		$homeworks_assignments = $offlinedata["Registraion"]["homework_assignment"];
		if(count($homeworks_assignments)>0){
			foreach($homeworks_assignments as $hwa){
				$patient_id = $hwa["patient_id"];
				$already_viewed = $hwa["already_viewed"];
				$homework_id = $hwa["homework_id"];
				$assignment_id = $hwa["assignment_id"];
				$updatechk = $this->db->query("UPDATE bip_my_homework_assignment SET already_viewed=? WHERE patient_id=? AND homework_id=? AND assignment_id=?", array($already_viewed,$patient_id,$homework_id,$assignment_id));
			}
		}

		//SAVE REGISTRATION DATA END

		//OTHER MODULES START

		//feelings assignments
		$feelingAssignments = $offlinedata["other_modules"]["feelingAssignments"];
		foreach($feelingAssignments as $assign){
			$insFeeling = $this->db->query("INSERT INTO bip_v2_feelings_assignments SET
												feeling_id = ?,
												patient_id = ?,
												answered_date = ?,
												module_version = ?,
												feeling_type = ?
												", array($assign["feeling_id"],$userid,$assign["answered_date"],$assign["module_version"],$assign["feeling_type"]));
		}

		//thoughts assignments
		$thoughtAssignments = $offlinedata["other_modules"]["thoughtAssignments"];
		foreach($thoughtAssignments as $assign){
			$insThoughts = $this->db->query("INSERT INTO bip_v2_sk_thoughts_assignments SET
												thought_id = ?,
												skill_id = ?,
												patient_id = ?,
												times_used = ?
												", array($assign["thought_id"], $assign["skill_id"],$userid,$assign["times_used"]));
		}

		//exposure assignments
		$exposure_assignments = $offlinedata["other_modules"]["exposureAssignments"];
		foreach($exposure_assignments as $exp_assignment){
			$insExAss = $this->db->query("INSERT INTO bip_v2_sk_exposure_patients_assignments SET
				                       		exposure_id = ?,
				                       		date_answered = ?,
				                       		patient_id = ?,
				                       		rating = ?,
				                       		countdown_timer = ?,
				                       		countdown_completed= ?
										", array($exp_assignment["exposure_id"], $exp_assignment["date_answered"],$userid, $exp_assignment["rating"], $exp_assignment["countdown_timer"], $exp_assignment["countdown_completed"]));
			if($insExAss){
					$exp_ass_id = $this->db->insert_id();
					$ex_ass_details = $exp_assignment["assignment_detail"];
					foreach($ex_ass_details as $ead){
							$this->db->query("INSERT INTO bip_v2_sk_exposure_patients_assignments_details SET
												assignment_id = '$exp_ass_id',
												exposure_id = ?,
												step_id = ?,
												answer_id = ?
											", array($ead["exposure_id"], $ead["step_id"], $ead["answer_id"]));
					}
			}
		}//exposure assignment end

		//skill Assignments
		$skill_assignments = $offlinedata["other_modules"]["skillAssignments"];
		foreach($skill_assignments as $sass){
				$insSkAss = $this->db->query("INSERT INTO bip_v2_sk_skills_assignments SET
												skill_id = ?,
												date_answered = ?,
												patient_id = ?,
												rating = ?,
					                       		countdown_timer = ?,
					                       		countdown_completed= ?
											", array($sass["skill_id"], $sass["date_answered"], $userid, $sass["rating"], $sass["countdown_timer"], $sass["countdown_completed"]));
				if($insSkAss){
						$sk_ass_id = $this->db->insert_id();
						$sk_ass_details = $sass["assignment_detail"];
						foreach($sk_ass_details as $sad){
							$this->db->query("INSERT INTO bip_v2_sk_skills_assignments_details SET
												assignment_id = '$sk_ass_id',
												skill_id = ?,
												step_id = ?,
												answer_id = ?
											", array($sad["skill_id"], $sad["step_id"], $sad["answer_id"]));
						}
				}
		}

		//OTHER MODULES END

		$this->db->query("UPDATE bip_registration_answers SET app_answer_id='0'");

		// get new data to sync in app for offline mode call
		$response = new stdClass;
		$response = $this->getSyncData($userid,$deviceId,$tokenkey);
		$response->registration_stuffs = $this->syncUserData(array(
									'userid' => $userid,
									'first_sync'=> false,
									'deviceId' => $deviceId,
									'tokenkey' => $tokenkey
								),1);


		$this->db->query("UPDATE bip_user SET last_sync_date=now() WHERE id=?",array($userid));



		return $response;
	}

	/**
	 * @param  get data to sync in app in response
	 * @return object
	 */
	function getSyncData($userid,$deviceId,$tokenkey)
	{
		$userQry = $this->db->query("SELECT last_sync_date,difficulty_id FROM bip_user WHERE id=?",array($userid));
		$row = $userQry->row();
		// dd($row);

		$last_sync_date = $row->last_sync_date;
		$difficulty_id = $row->difficulty_id;

		$hide_graph = $this->isHideGraph($difficulty_id);

		$likeString = '%,'.$userid.',%';

		$taskQry=$this->db->query("SELECT id AS taskid,
								       task AS heading,
								       tag,
								       completed
								FROM bip_tasks
								WHERE  is_deleted=0 AND difficulty_id=? AND CONCAT(',' , user_id , ',') LIKE ? AND updated_at > ?",array($difficulty_id,$likeString,$last_sync_date));
		$result1=$taskQry->result_array();

		$settingData= $this->getSettingDataByDiffId($diffId);

		$newresult=array();
		$taskIds = array();

		foreach ($result1 as $rk => $rval) {
			$json=json_decode($rval['completed'],true);

			if (is_array($json) && array_key_exists($userid, $json)){
				array_push($taskIds, $rval['taskid']);
			}else{
				$newresult[]=$rval;
			}
		}
		$newdata=array();
		foreach ($newresult as $k => $v) {
			$newdata[$k]['taskid'] = $taskid = $v['taskid'];
			if ($v['tag']=='1,2') {
				$v['tag']='3';
			}

			$newdata[$k]['tag'] = $v['tag'];
			$newdata[$k]['heading']=$v['heading'];
			$newdata[$k]['hide_graph']=$hide_graph;
		}

		$trainingQry = $this->db->query("SELECT * FROM bip_training_app WHERE user_id=? AND updated_at > ?",array($userid,$last_sync_date));
		$result2 = $trainingQry->result_array();

		$userString = '%,'.$userid.',%';

		$deletedTaskQry = $this->db->query("SELECT id FROM bip_tasks WHERE is_deleted=1 AND CONCAT(',' , user_id , ',') LIKE ? AND updated_at > ?",array($userString,$last_sync_date));
		$result3 = $deletedTaskQry->result_array();


		foreach ($result3 as $key => $rval) {
			array_push($taskIds, $rval['id']);
		}

		$response = new stdClass;

		// get user response
		$user_query = $this->db->query("SELECT * FROM bip_user WHERE id=? AND status='1' AND app_status='1' AND user_role='1'",array($userid));
		$user_data = $user_query->row();

		$response = $this->getUserInformation($user_data,$deviceId,$tokenkey);

		// get additional response
		$response->tasks = $newdata;
		$response->trainings = $result2;
		$response->deletedTasks = $taskIds;

		return $response;

	}

	/*ADDED BY SABIN >>*/
	function isValidUserDevice($userid, $deviceUUID){

	}

	function syncUserData($data,$avoidTraining=0){
		extract($data);

		// if ($first_sync) {
		// 	$timestamp = strtotime('-6 years');
		// 	$old_time_stamp = date('Y-m-d H:i:s', $timestamp);
		// 	$this->db->query("UPDATE bip_user SET last_sync_date='$old_time_stamp' WHERE id=?", array($userid));
		// }

		$arraystuff = array();
		$returnArr = array();
		if(isset($DeviceUUID) && $DeviceUUID!=""){
			$validateID = $DeviceUUID;
		}else{
			$validate = "";
		}


		if ($this->validateUserToken($userid,$deviceId,$tokenkey,$validate)) {
			$userQry = $this->db->query("SELECT last_sync_date,difficulty_id FROM bip_user WHERE id=?", array($userid));
			$row = $userQry->row();

			/*$last_sync_date = $row->last_sync_date;
			$difficulty_id = $row->difficulty_id;*/
			$arraystuff["difficulty_id"] = $row->difficulty_id;
			$arraystuff["last_sync_date"] = $row->last_sync_date;
			$arraystuff["first_sync"] = $first_sync;
			$arraystuff["userid"] = $userid;
			$arraystuff["deviceId"] = $deviceId;
			$arraystuff["tokenkey"] = $tokenkey;

			$flow = $this->getTreatmentFlow($row->difficulty_id);
			$default = $this->getTreatmentFlow(0);

			$returnArr["registration_task"] = $this->syncRegistrationData($arraystuff);
			$returnArr["other_modules"] = $this->syncOtherModules($arraystuff);
			if($avoidTraining==0){
				$returnArr["active_tasks"] = $this->getActiveTasks($arraystuff);
			}

			$returnArr["slide3_image"] = (!empty($flow->slide3_image)) ? base_url().'images/uploads/app_images/'.$flow->slide3_image: base_url().'images/uploads/app_images/'.$default->slide3_image;

			$returnArr["countdown_audio"] = $this->getCountdownAudios();
			$returnArr["last_synced_on"] = $row->last_sync_date;

			if($avoidTraining==0){
				//$this->db->query("UPDATE bip_user SET last_sync_date=now() WHERE id=?", array($userid));
			}
		}else{
			$returnArr["error"] = 1;
			$returnArr["error_message"] = "Invalid Token";
		}

		return json_decode(json_encode($returnArr));

	}


	function getCountdownAudios(){
		$audioURL = "";
		$directPath = DOCUMENT_ROOT."assets/sound_files/misc/countdown_alert.mp3";
		if(is_file($directPath)){
			$audioURL = base_url()."assets/sound_files/misc/countdown_alert.mp3";
		}else{
			$audioURL = "";
		}
		return $audioURL;
	}

	//method to sync device's data to server
	function syncToServer($data){
		extract($data);

		$returnArray = array();

		if($this->validateUserToken($userid,$deviceId,$tokenkey)){
			$current_date = date("Y-m-d H:i:s");
			//TRAINING START
			$trainingIDs = array();
			$gettraining = $offlinedata->training;
			foreach($getraining as $training){
				if($training->edited==0 && $training->trainingId==0){//insert

						$ins = $this->db->query("INSERT INTO bip_training_app SET
					                            'user_id' =?,
												'comment' = ?,
												'edited' = ?,
												'type' = ?,
												'task_id' = ?,
												'trainingdatetime' = ?,
												'estimatedvalue' = ?,
												'training_duration' = ?,
												'estimatedvalue_end' = ?,
												'created_at' = '".$current_date."',
												'updated_at' = '".$current_date."'
											", array($userid, $training->comment, $training->edited, $training->type, $training->taskid, $training->trainingdatetime,  $training->estimatedvalue, $training->training_duration, $training->estimatedvalue_end));
						if($ins){
							array_push($trainingIDs, '\''.$this->db->insert_id().'\'');
						}
				}else{ //edit
					$upd = $this->db->query("UPDATE bip_training_app SET
					                            'user_id' =?,
												'comment' = ?,
												'edited' = ?,
												'type' = ?,
												'task_id' = ?,
												'trainingdatetime' = ?,
												'estimatedvalue' = ?,
												'training_duration' = ?,
												'estimatedvalue_end' = ?,
												'updated_at' = '".$current_date."' WHERE id=?
											", array($userid, $training->comment, $training->edited, $training->type, $training->taskid, $training->trainingdatetime,  $training->estimatedvalue, $training->training_duration, $training->estimatedvalue_end, $training->trainingId));
				}

			}
			//now fetch newly added data;
			if(count($trainingIDs)>0){
				$trnQry = $this->db->query("SELECT * FROM bip_training_app WHERE id IN(".implode(",", $trainingIDs).")", array($userid));
		  		$trnResult = $trnQry->result_array();
		  		$this->db->freeDBResource();
		  		if($trnResult){
			  		foreach($trnResult as $key=>$value){
			  			$returnArray["training"][$key] = $value;
			  		}
			  	}else{
			  		$returnArray["training"] = null;
			  	}
			}else{
				$returnArray["training"] = null;
			}


			//TRAINING END
		}else{
			$returnArray["training"] = null;
		}
		return $returnArray;
	}

	/*Method to fetch all registration data for offline mode*/
	function syncRegistrationData($data){
		extract($data);

		$arrRegistration = array();


		$userQry = $this->db->query("SELECT last_sync_date,difficulty_id FROM bip_user WHERE id=?", array($userid));
		$row = $userQry->row();



		$last_sync_date = $row->last_sync_date;
		$difficulty_id = $row->difficulty_id;

		/*REGISTRATION TASK*/
		//get registration
		//get registration id related to difficulty,


		//fetch Registration ids linked with difficultys
		$RegIDs = array();
		$RegIDsQry = $this->db->query("SELECT registration_id FROM bip_registration_task WHERE registration_status='1' AND FIND_IN_SET(?,difficulty_id)",array($difficulty_id));
  		$RegIDsResult = $RegIDsQry->result_array();
  		$this->db->freeDBResource();
  		if($RegIDsResult){
				foreach($RegIDsResult as $key=>$value){
					array_push($RegIDs, '\''.$value["registration_id"].'\'');
  				}
  		}

  		//fetch step ids based on retrieved registration ids
  		if(count($RegIDs)>0){
	  		$StepIDs = array();
	  		$StepIDsQry = $this->db->query("SELECT step_id FROM bip_registration_steps WHERE step_status='1' AND registration_id IN (".implode(",", $RegIDs).")");
	  		$StepIDsResult = $StepIDsQry->result_array();
	  		$this->db->freeDBResource();
	  		if($StepIDsResult){
					foreach($StepIDsResult as $key=>$value){
						array_push($StepIDs, '\''.$value["step_id"].'\'');
	  				}
	  		}
  		}else{
  			$StepIDs = array();
  		}

  		//fetch patient registration assignments
  		$AssID = array();
		$regAssIDqry = $this->db->query("SELECT * FROM bip_registration_assignments WHERE patient_id=?", array($userid));
  		$regAssIDResult = $regAssIDqry->result_array();
  		$this->db->freeDBResource();
  		if($regAssIDResult){
			foreach($regAssIDResult as $key=>$value){
	  			array_push($AssID, '\''.$value["assignment_id"].'\'');
	  		}
  		}



  		if($first_sync==true){
			$regQuery = $this->db->query("SELECT * FROM bip_registration_task WHERE registration_status='1' AND FIND_IN_SET(?,difficulty_id)",array($difficulty_id));
		}else{
			$regQuery = $this->db->query("SELECT * FROM bip_registration_task WHERE registration_status='1' AND FIND_IN_SET(?,difficulty_id) AND last_updated>?",array($difficulty_id,$last_sync_date));
		}
		$regResult = $regQuery->result_array();
		$this->db->freeDBResource();


  		if($regResult){
	  		foreach($regResult as $key=>$value){
	  			$arrRegistration["registration_module"]["registrations"][$key] = @str_replace("'", "&#39;", $value);
	  		}
  		}else{
  			$arrRegistration["registration_module"]["registrations"] = null;
  		}

  		//get registration steps
  		//Remove condition  AND special_case='0' after new feature has been implemented

  		if(count($RegIDs)>0){

	  		if($first_sync==true){
	  			$stepQry = $this->db->query("SELECT * FROM bip_registration_steps WHERE step_status='1' AND registration_id IN (".implode(",", $RegIDs).")");
	  		}else{
	  			$stepQry = $this->db->query("SELECT * FROM bip_registration_steps WHERE step_status='1' AND registration_id IN (".implode(",", $RegIDs).") AND last_updated>'$last_sync_date'");
	  		}
	  		$arrRegistration["registration_module"]["step_query"] = $this->db->last_query();

	  		$stepResult = $stepQry->result_array();
			$this->db->freeDBResource();

			if($stepResult){
				foreach($stepResult as $key=>$value){
		  			$arrRegistration["registration_module"]["steps"][$key] = @str_replace("'", "&#39;", $value);
		  		}
	  		}else{
	  			$arrRegistration["registration_module"]["steps"] = null;
	  		}
  		}

  		$Stps = array_unique($StepIDs);


  		//now fetch answers and answer category
  		if(count($Stps)>0){
	  		if($first_sync==true){
	  			$catQry = $this->db->query("SELECT * FROM bip_registration_answer_category WHERE answer_cat_status='1' AND step_id IN (".implode(",", $Stps).") AND (belongs_to=? OR answer_type='standard')", array($userid));
	  		}else{
	  			$catQry = $this->db->query("SELECT * FROM bip_registration_answer_category WHERE answer_cat_status='1' AND step_id IN (".implode(",", $Stps).") AND (belongs_to=? OR answer_type='standard') AND last_updated>'$last_sync_date'", array($userid));
	  		}
	  		$catResult = $catQry->result_array();
			$this->db->freeDBResource();
			//echo "<br>".$this->db->last_query()."<br>";
			if($catResult){
				foreach($catResult as $key=>$value){
		  			$arrRegistration["registration_module"]["answer_category"][$key] = @str_replace("'", "&#39;", $value);
		  		}
	  		}else{
	  			$arrRegistration["registration_module"]["answer_category"] = null;
	  		}


	  		//Now fetch Answers both step associated and special answers
	  		if($first_sync==true){
	  			$ansQry = $this->db->query("SELECT * FROM bip_registration_answers WHERE answer_status='1' AND ((step_id IN (".implode(",", $Stps).") AND (belongs_to=? OR answer_type='standard')) OR (special_answer='1' AND difficulty_id=?))", array($userid,$difficulty_id));
	  		}else{
	  			$ansQry = $this->db->query("SELECT * FROM bip_registration_answers WHERE answer_status='1' AND ((step_id IN (".implode(",", $Stps).") AND (belongs_to=? OR answer_type='standard')) OR (special_answer='1' AND difficulty_id=?)) AND last_updated>?", array($userid,$difficulty_id,$last_sync_date));
	  		}
	  		$ansResult = $ansQry->result_array();
			$this->db->freeDBResource();
			if($ansResult){
				foreach($ansResult as $key=>$value){
		  			$arrRegistration["registration_module"]["answers"][$key] = @str_replace("'", "&#39;", $value);
		  		}
		  	}else{
		  		$arrRegistration["registration_module"]["answers"] = null;
		  	}
  		}else{
  			$arrRegistration["registration_module"]["answer_category"] = null;
  			$arrRegistration["registration_module"]["answers"] = null;
  		}



  		//REGISTRATION ASSIGNMENTS - THE ASSIGNMENT PATIENT HAS ALREADY DONE


  		if($first_sync==true){
  			$regAssQry = $this->db->query("SELECT * FROM bip_registration_assignments WHERE patient_id=?", array($userid));
  		}else{
  			$regAssQry = $this->db->query("SELECT * FROM bip_registration_assignments WHERE  patient_id=? AND last_updated>'$last_sync_date'", array($userid));
  		}
  		$regAssResult = $regAssQry->result_array();
  		$this->db->freeDBResource();
  		if($regAssResult){
			foreach($regAssResult as $key=>$value){
	  			$arrRegistration["registration_module"]["patients"]["assignments"][$key] = $value;
	  		}
  		}else{
  			$arrRegistration["registration_module"]["patients"]["assignments"] = null;
  		}

  		//assignment details
  		if(count($AssID)>0){
	  		$regAssDetQry = $this->db->query("SELECT * FROM bip_registration_assignments_details WHERE assignment_id IN (".implode(",", $AssID).")");
	  		$regAssDetResult = $regAssDetQry->result_array();
	  		$this->db->freeDBResource();
	  		if($regAssDetResult){
				foreach($regAssDetResult as $key=>$value){
		  			$arrRegistration["registration_module"]["patients"]["assignment_details"][$key] = $value;
		  		}
		  	}else{
		  		$arrRegistration["registration_module"]["patients"]["assignment_details"] = null;
		  	}
  		}else{
  			$arrRegistration["registration_module"]["patients"]["assignment_details"] = null;
  		}





  		//fetch assigned homeworks
  		$hwAssignmentIDs = array();
  		if($first_sync==true){
  			$assignedHwQry = $this->db->query("SELECT * FROM bip_my_homework_assignment WHERE patient_id=?", array($userid));
  		}else{
  			$assignedHwQry = $this->db->query("SELECT * FROM bip_my_homework_assignment WHERE patient_id=?", array($userid)); // AND last_updated>'$last_sync_date'
  		}
  		$assignedHwResults = $assignedHwQry->result_array();
  		$this->db->freeDBResource();
  		if($assignedHwResults){
	  		foreach($assignedHwResults as $key=>$value){
	  			$arrRegistration["homework_module"]["homework_assignments"][$key] = $value;
	  			array_push($hwAssignmentIDs, '\''.$value["homework_id"].'\'');
	  		}
	  	}else{
	  		$arrRegistration["homework_module"]["homework_assignments"] = null;
	  	}


	  	/*HOMEWORKS*/
  		/*if($first_sync==true){
  			$hwQry = $this->db->query("SELECT * FROM bip_my_homework WHERE FIND_IN_SET($difficulty_id,difficulty_id) AND added_by='admin'");
  		}else{
  			if(count($hwAssignmentIDs)>0){
  				$hwQry = $this->db->query("SELECT * FROM bip_my_homework WHERE FIND_IN_SET($difficulty_id,difficulty_id) AND added_by='admin' AND (updated_at>'$last_sync_date' OR homework_id IN (".implode(",", $hwAssignmentIDs)."))");
  			}else{
  				$hwQry = $this->db->query("SELECT * FROM bip_my_homework WHERE FIND_IN_SET($difficulty_id,difficulty_id) AND added_by='admin' AND updated_at>'$last_sync_date'");
  			}
  		}*/

  		if(count($hwAssignmentIDs)>0){
  			$hwQry = $this->db->query("SELECT * FROM bip_my_homework where homework_status='1' AND homework_id IN (".implode(",", $hwAssignmentIDs).")");
  			$hwResult = $hwQry->result_array();
	  		$this->db->freeDBResource();
	  		if($hwResult){
		  		foreach($hwResult as $key=>$value){
		  			$arrRegistration["homework_module"]["homeworks"][$key] = str_replace("'", "&#39;", $value);
		  		}
		  	}else{
		  		$arrRegistration["homework_module"]["homeworks"] = null;
		  	}
  		}else{
  			$arrRegistration["homework_module"]["homeworks"] = null;
  		}





  		/*CRISIS PLAN*/
  		if($first_sync==true){
  			$crisisQry = $this->db->query("SELECT * FROM bip_my_crisis_plan WHERE plan_status='1' AND  belongs_to=?", array($userid));
  		}else{
  			$crisisQry = $this->db->query("SELECT * FROM bip_my_crisis_plan WHERE plan_status='1' AND updated_at>'$last_sync_date' AND belongs_to=?", array($userid));
  		}
  		$crisisResult = $crisisQry->result_array();
  		$this->db->freeDBResource();
  		if($crisisResult){
	  		foreach($crisisResult as $key=>$value){
	  			$arrRegistration["crisis_plan"][$key] = str_replace("'", "&#39;", $value);
	  		}
	  	}else{
	  		$arrRegistration["crisis_plan"] = null;
	  	}

  		//return json_decode(json_encode($arrRegistration));
  		return $arrRegistration;
	}


	function getNewSettings($data){
		extract($data);
		$response = new stdClass;

		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$response->new_messages = $this->totalMessage($userid);
			$response->enable_msg_alert = $this->appMsgAlertEnabled($userid);
			$response->hide_graph = $this->isGraphHidden($userid);

			$inputObj = array(
				'userid' => $userid,
				'deviceId' => $deviceId,
				'tokenkey' => $tokenkey
			);

			$response->available_modules = $this->getActiveModules($inputObj);

			return $response;
		}

		$response->message="Token Key Expired";
		return $response;
	}

	function getAvailableModules($data){
		extract($data);
		$response = new stdClass;

		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {

			$inputObj = array(
				'userid' => $userid,
				'deviceId' => $deviceId,
				'tokenkey' => $tokenkey
			);

			$response->available_modules = $this->getActiveModules($inputObj);

			return $response;
		}

		$response->message="Token Key Expired";
		return $response;
	}

	//now modules (skills, thoughts, feelings, exposures etc) and other stuff
	function syncOtherModules($data){
		extract($data);
		$arrModules = array();

		$userQry = $this->db->query("SELECT last_sync_date,difficulty_id FROM bip_user WHERE id=?", array($userid));
		$row = $userQry->row();

		$last_sync_date = $row->last_sync_date;
		$difficulty_id = $row->difficulty_id;

		$arrModules["modules"]["default"]["module_name"] = "Känslospaning";
		//get all feelings related to difficulty


		if($first_sync==true){
  			$feelingsQry = $this->db->query("SELECT * FROM bip_v2_feelings WHERE feeling_status='1' AND difficulty_id='".$difficulty_id."'");
  		}else{
  			$feelingsQry = $this->db->query("SELECT * FROM bip_v2_feelings WHERE feeling_status='1' AND difficulty_id='".$difficulty_id."' AND last_updated>'".$last_sync_date."'");
  		}
  		$feelingResult = $feelingsQry->result_array();
		$this->db->freeDBResource();
		if($feelingResult){
			foreach($feelingResult as $key=>$value){
	  			$arrModules["modules"]["default"]["feelings"][$key]  = str_replace("'", "&#39;", $value);
	  		}
	  	}else{
	  		$arrModules["modules"]["default"]["feelings"] = null;
	  	}

	  	//feeling assignments
	  	if($first_sync==true){
	  		$fassignmentQry = $this->db->query("SELECT * FROM bip_v2_feelings_assignments WHERE patient_id=?", array($userid));
	  	}else{
	  		$fassignmentQry = $this->db->query("SELECT * FROM bip_v2_feelings_assignments WHERE patient_id=? AND last_updated>'$last_sync_date'", array($userid));
	  	}
	  	$fassignmentResult = $fassignmentQry->result_array();
		$this->db->freeDBResource();
		if($fassignmentResult){
			foreach($fassignmentResult as $key=>$value){
	  			$arrModules["modules"]["feelings"]["assignment"][$key]  = str_replace("'","&#039;",$value);
	  		}
	  	}else{
	  		$arrModules["modules"]["feelings"]["assignment"] = null;
	  	}

  		//fetch other modules
  		if($first_sync==true){
  			$modQry = $this->db->query("SELECT * FROM bip_v2_modules WHERE difficulty_id='$difficulty_id' AND module_status='1'");
  		}else{
  			$modQry = $this->db->query("SELECT * FROM bip_v2_modules WHERE difficulty_id='$difficulty_id' AND module_status='1' AND  modified_date>'$last_sync_date'");
  		}
  		$modResult = $modQry->result_array();
  		$this->db->freeDBResource();

  		if($modResult){
	  		foreach($modResult as $key=>$value){
	  			$arrModules["modules"]["others"][$key] =str_replace("'","&#039;",$value);
	  			if(trim($value["module_icon"])!=""){
	  				$directPath = DOCUMENT_ROOT."images/uploads/module_icons/".$value["module_icon"];
	  				if(is_file($directPath)){
						$arrModules["modules"]["others"][$key]['assetURL'] = base_url()."images/uploads/module_icons/".$value["module_icon"];
	  				}else{
	  					$arrModules["modules"]["others"][$key]['assetURL'] = "";
	  				}
	  			}else{
	  				$arrModules["modules"]["others"][$key]['assetURL'] = "";
	  			}

	  		}
  		}else{
  			$arrModules["modules"]["others"] = null;
  		}

  		//get module ids for active difficulty
  		$moduleIDs = array();
  		$modIDQry = $this->db->query("SELECT module_id FROM bip_v2_modules WHERE difficulty_id=?  AND module_status='1'", array($difficulty_id));
  		$modIDResult = $modIDQry->result_array();
  		$this->db->freeDBResource();
  		if($modIDResult){
				foreach($modIDResult as $key=>$value){
					array_push($moduleIDs, '\''.$value["module_id"].'\'');
  				}
  		}

  		//get skill ids for active difficulty's module
  		if(count($moduleIDs)>0){
	  		$skillIDs = array();
	  		$skillIDQry = $this->db->query("SELECT skill_id FROM bip_v2_skills WHERE module_id IN(".implode(",", $moduleIDs).") AND skill_status='1'");
	  		$skillIDResult = $skillIDQry->result_array();
	  		$this->db->freeDBResource();
	  		if($skillIDResult){
					foreach($skillIDResult as $key=>$value){
						array_push($skillIDs, '\''.$value["skill_id"].'\'');
	  				}
	  		}
  		}else{
  			$skillIDs = array();
  		}

  		//get exposure ids for the skills above.
  		/*$exposureID = array();
  		$exposureIDQry = $this->db->query("SELECT exposure_id FROM bip_v2_sk_exposure_patients WHERE skill_id IN(".implode(",", $skillIDs).")");
  		$exposureIDResult = $exposureIDQry->result_array();
  		$this->db->freeDBResource();
  		if($exposureIDResult){
				foreach($exposureIDResult as $key=>$value){
					array_push($exposureID, '\''.$value["exposure_id"].'\'');
  				}
  		}*/


  		//fetch exposure steps id
  		if(count($skillIDs)>0){
	  		$stepIDs = array();
	  		$stepIDsQry = $this->db->query("SELECT step_id FROM bip_v2_sk_exposure_steps WHERE skill_id IN(".implode(",", $skillIDs).")  AND step_status='1'");
	  		$stepIDsResult = $stepIDsQry->result_array();
	  		$this->db->freeDBResource();
	  		if($stepIDsResult){
					foreach($stepIDsResult as $key=>$value){
						array_push($stepIDs, '\''.$value["step_id"].'\'');
	  				}
	  		}
  		}else{
  			$stepIDs = array();
  		}

  		//fetch skills
  		if(count($moduleIDs)>0){
	  		if($first_sync==true){
	  			$skillsQry = $this->db->query("SELECT * FROM bip_v2_skills WHERE module_id IN(".implode(",", $moduleIDs).")  AND skill_status='1'");
	  		}else{
	  			$skillsQry = $this->db->query("SELECT * FROM bip_v2_skills WHERE module_id IN(".implode(",", $moduleIDs).")  AND skill_status='1' AND last_updated>'$last_sync_date'");
	  		}
	  		$skillResult = $skillsQry->result_array();
	  		$this->db->freeDBResource();


	  		if($skillResult){
		  		foreach($skillResult as $key=>$value){
		  			$arrModules["modules"]["skills"][$key] = str_replace("'","&#039;",$value);
		  		}
		  	}else{
		  		$arrModules["modules"]["skills"] = null;
		  	}
  		}else{
  			$arrModules["modules"]["skills"] = null;
  		}

  		//fetch thoughts
  		if(count($skillIDs)>0){
  			if($first_sync==true){
	  			$thoughtsQry = $this->db->query("SELECT * FROM bip_v2_sk_thoughts WHERE skill_id IN(".implode(",", $skillIDs).")");
  			}else{
  				$thoughtsQry = $this->db->query("SELECT * FROM bip_v2_sk_thoughts WHERE skill_id IN(".implode(",", $skillIDs).") AND last_updated>'$last_sync_date'");
  			}

	  		$thoughtsResult = $thoughtsQry->result_array();
	  		$this->db->freeDBResource();
	  		if($thoughtsResult){
		  		foreach($thoughtsResult as $key=>$value){
		  			$arrModules["modules"]["thoughts"][$key] = str_replace("'","&#039;",$value);
		  			if(trim($value["thought_sound_file"])!=""){
		  				$arrModules["modules"]["thoughts"][$key]['sound_url'] = base_url()."assets/sound_files/thoughts/".$value["thought_sound_file"];
		  			}else{
		  				$arrModules["modules"]["thoughts"][$key]['sound_url'] = "";
		  			}
		  		}
		  	}else{
		  		$arrModules["modules"]["thoughts"] = null;
		  	}
  		}else{
  			$arrModules["modules"]["thoughts"] = null;
  		}

  		//thought assignments
  		if($first_sync==true){
	  		$thoughtAssignmentQry = $this->db->query("SELECT * FROM bip_v2_sk_thoughts_assignments WHERE patient_id=?", array($userid));
	  	}else{
	  		$thoughtAssignmentQry = $this->db->query("SELECT * FROM bip_v2_sk_thoughts_assignments WHERE patient_id=? AND last_updated>'$last_sync_date'", array($userid));
	  	}



	  	$thougAssignmentResults = $thoughtAssignmentQry->result_array();
		$this->db->freeDBResource();
		if($thougAssignmentResults){
			foreach($thougAssignmentResults as $key=>$value){
	  			$arrModules["modules"]["thoughts_assignment"][$key]  = str_replace("'","&#039;",$value);
	  		}
	  	}else{
	  		$arrModules["modules"]["thoughts_assignment"] = null;
	  	}


  		//fetch patient specific exposures

  		if(count($skillIDs)>0){
	  		if($first_sync==true){
	  			$expPatientQry = $this->db->query("SELECT * FROM bip_v2_sk_exposure_patients WHERE skill_id IN(".implode(",", $skillIDs).")");
	  		}else{
	  			$expPatientQry = $this->db->query("SELECT * FROM bip_v2_sk_exposure_patients WHERE skill_id IN(".implode(",", $skillIDs).")  AND last_updated>'$last_sync_date'");
	  		}



	  		$expPatientResult = $expPatientQry->result_array();
	  		$this->db->freeDBResource();
	  		if($expPatientResult){
		  		foreach($expPatientResult as $key=>$value){
		  			$arrModules["exposure"]["patient_exposure"][$key] = str_replace("'","&#039;",$value);
		  		}
		  	}else{
		  		$arrModules["exposure"]["patient_exposure"] = null;
		  	}
  		}else{
  			$arrModules["exposure"]["patient_exposure"] = null;
  		}

  		//fetch patient answered assignments
  		$AssID = array();
  		if($first_sync==true){
  			$exPatientAssQry = $this->db->query("SELECT * FROM bip_v2_sk_exposure_patients_assignments WHERE patient_id=?", array($userid));
  		}else{
  			$exPatientAssQry = $this->db->query("SELECT * FROM bip_v2_sk_exposure_patients_assignments WHERE patient_id=? AND last_updated>'$last_sync_date'", array($userid));
  		}
  		$exPatientAssResult = $exPatientAssQry->result_array();
  		$this->db->freeDBResource();
  		if($exPatientAssResult){
	  		foreach($exPatientAssResult as $key=>$value){
	  			$arrModules["exposure"]["patient_exposure_assignments"][$key] = str_replace("'","&#039;",$value);
	  			array_push($AssID, '\''.$value["assignment_id"].'\'');
	  		}
  		}else{
  			$arrModules["exposure"]["patient_exposure_assignments"] = null;
  		}

  		//fetch patient's answered assignment details
  		if(count($AssID)>0){
	  		$exPatientAssDetailsQry = $this->db->query("SELECT * FROM bip_v2_sk_exposure_patients_assignments_details WHERE assignment_id IN (".implode(",", $AssID).")");
	  		$exPatientAssDetailsResult = $exPatientAssDetailsQry->result_array();
	  		$this->db->freeDBResource();
	  		if($exPatientAssDetailsResult){
		  		foreach($exPatientAssDetailsResult as $key=>$value){
		  			$arrModules["exposure"]["patient_exposure_assignments_details"][$key] = str_replace("'","&#039;",$value);
		  		}
		  	}else{
		  		$arrModules["exposure"]["patient_exposure_assignments_details"] = null;
		  	}
  		}else{
  			$arrModules["exposure"]["patient_exposure_assignments_details"] = null;
  		}

  		//fetch skills assignment
  		$skillAssID = array();
  		if($first_sync==true){
  			$skillsAssQry = $this->db->query("SELECT * FROM bip_v2_sk_skills_assignments WHERE patient_id=?", array($userid));
  		}else{
  			$skillsAssQry = $this->db->query("SELECT * FROM bip_v2_sk_skills_assignments WHERE patient_id=? AND last_updated>'$last_sync_date'", array($userid));
  		}
  		$skillsAssResult = $skillsAssQry->result_array();
  		$this->db->freeDBResource();

  		if($skillsAssResult){
	  		foreach($skillsAssResult as $key=>$value){
	  			$arrModules["skills"]["assignments"][$key] = str_replace("'","&#039;",$value);
	  			array_push($skillAssID, '\''.$value["assignment_id"].'\'');
	  		}
  		}else{
  			$arrModules["skills"]["assignments"] = null;
  		}

  		//fetch skills assignment details
  		if(count($skillAssID)>0){
	  		$skillsAssDetailsQry = $this->db->query("SELECT * FROM bip_v2_sk_skills_assignments_details WHERE assignment_id IN (".implode(",", $skillAssID).")");
	  		$skillsAssDetailsResult = $skillsAssDetailsQry->result_array();
	  		$this->db->freeDBResource();
	  		if($skillsAssDetailsResult){
		  		foreach($skillsAssDetailsResult as $key=>$value){
		  			$arrModules["skills"]["assignment_details"][$key] = str_replace("'","&#039;",$value);
		  		}
		  	}else{
		  		$arrModules["skills"]["assignment_details"] = null;
		  	}
  		}else{
  			$arrModules["skills"]["assignment_details"] = null;
  		}


  		//Fetch exposure master steps
  		if(count($skillIDs)>0){
	  		if($first_sync==true){
	  			$exStepsQry = $this->db->query("SELECT * FROM bip_v2_sk_exposure_steps WHERE skill_id IN(".implode(",", $skillIDs).") AND step_status='1'");
	  		}else{
	  			$exStepsQry = $this->db->query("SELECT * FROM bip_v2_sk_exposure_steps WHERE skill_id IN(".implode(",", $skillIDs).") AND step_status='1' AND last_updated>'$last_sync_date'");
	  		}
	  		$exStepResult = $exStepsQry->result_array();
	  		$this->db->freeDBResource();

	  		if($exStepResult){
		  		foreach($exStepResult as $key=>$value){
		  			$arrModules["exposure"]["steps"][$key] = str_replace("'","&#039;",$value);
		  		}
		  	}else{
		  		$arrModules["exposure"]["steps"] = null;
		  	}
  		}else{
  			$arrModules["exposure"]["steps"] = null;
  		}

  		//fetch exposure master answer cats
  		if(count($stepIDs)>0){
	  		if($first_sync==true){
	  			$exAnsCatsQry = $this->db->query("SELECT * FROM bip_v2_skill_exposure_answer_category WHERE step_id IN(".implode(",", $stepIDs).") AND (belongs_to=? OR answer_type='standard' AND answer_cat_status='1')", array($userid));
	  		}else{
	  			$exAnsCatsQry = $this->db->query("SELECT * FROM bip_v2_skill_exposure_answer_category WHERE step_id IN(".implode(",", $stepIDs).") AND (belongs_to=? OR answer_type='standard') AND answer_cat_status='1' AND last_updated>'$last_sync_date'", array($userid));
	  		}
	  		$exAnsCatsResult = $exAnsCatsQry->result_array();
	  		$this->db->freeDBResource();

	  		if($exAnsCatsResult){
		  		foreach($exAnsCatsResult as $key=>$value){
		  			$arrModules["exposure"]["answer_cats"][$key] = str_replace("'","&#039;",$value);
		  		}
		  	}else{
		  		$arrModules["exposure"]["answer_cats"] = null;
		  	}

	  		//fetch exposure master answers
	  		if($first_sync==true){
	  			$exAnsQry = $this->db->query("SELECT * FROM bip_v2_skill_exposure_answers WHERE step_id IN(".implode(",", $stepIDs).") AND (belongs_to=? OR answer_type='standard' AND answer_status='1')", array($userid));
	  		}else{
	  			$exAnsQry = $this->db->query("SELECT * FROM bip_v2_skill_exposure_answers WHERE step_id IN(".implode(",", $stepIDs).") AND (belongs_to=? OR answer_type='standard') AND answer_status='1' AND last_updated>'$last_sync_date'", array($userid));
	  		}

	  		$exAnsResult = $exAnsQry->result_array();
	  		$this->db->freeDBResource();

	  		if($exAnsResult){
		  		foreach($exAnsResult as $key=>$value){
		  			$arrModules["exposure"]["answers"][$key] = str_replace("'","&#039;",$value);
		  		}
		  	}else{
		  		$arrModules["exposure"]["answers"] = null;
		  	}
  		}else{
  			$arrModules["exposure"]["answer_cats"] = null;
  			$arrModules["exposure"]["answers"] = null;
  		}





		//get Feeling definition
  		if($first_sync==true){
			$defQry = $this->db->query("SELECT * FROM bip_v2_feelings_definition ORDER BY last_updated DESC LIMIT 1");
  		}else{
  			//$defQry = $this->db->query("SELECT * FROM bip_v2_feelings_definition ORDER BY last_updated DESC LIMIT 1");
  			$defQry = $this->db->query("SELECT * FROM bip_v2_feelings_definition WHERE last_updated>'$last_sync_date' ORDER BY last_updated DESC LIMIT 1");
  		}
		$defResult = $defQry->row();
		$this->db->freeDBResource();
		if($defResult){
			$arrModules["feeling_definitions"]["def_id"] = $defResult->def_id;
			$arrModules["feeling_definitions"]["primary"] = $defResult->primary_feelings;
			$arrModules["feeling_definitions"]["secondary"] = $defResult->secondary_feelings;
		}else{
			$arrModules["feeling_definitions"] = 0;
		}


		//echo "<pre>".print_r($arrModules,true)."</pre>"; exit;
		//return json_decode(json_encode($arrModules));
		return $arrModules;
	}
	/*ADDED BY SABIN <<*/

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

	function getFeedbackMessage($data)
	{
		extract($data);
		// if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$query = $this->db->query("SELECT
					bta.slide6_message
				FROM
					`bip_user` bu
				LEFT JOIN bip_difficulty bd ON (bd.id = bu.difficulty_id)
				LEFT JOIN bip_treatment_app bta ON (
					bta.difficulty_id = bu.difficulty_id
				)
				WHERE
					bu.id = ? LIMIT 1",array($userid));

				$row=$query->row();
				if ($query->num_rows()>0 && $row->slide6_message!="[]" && trim($row->slide6_message)!="") {
					$messages = json_decode($row->slide6_message,true);
				}else{
					$treatment_id = $this->getDefaultTreatmentId();
					$altQry = $this->db->query("SELECT slide6_message FROM bip_treatment_app WHERE id=? AND difficulty_id = 0",array($treatment_id));
					$altRow = $altQry->row();
					$messages = json_decode($altRow->slide6_message,true);
				}

				$response = new stdClass;
				$response->rating=array();
				$response->message=array();

				if (!empty($messages)) {
				foreach ($messages as $key => $message) {
					$tmp = explode('~~~', $message);
					array_push($response->rating, $tmp[0]);
					array_push($response->message, $tmp[1]);
				}
				}

				return $response;
		// }

		// $response->message="Token Key Expired";
		// return $response;
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
			bu.id = ? LIMIT 1",array($userId));

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
		$query=$this->db->query("SELECT anxiety,zero,ten,rating FROM bip_treatment_app WHERE difficulty_id=?",array($diffId));
		$row=$query->row();
		return $row;
	}

	/**
	 * Active tasks that are assigned to target user plus not closed.
	 */
	function getActiveTasks($data){

		extract($data);

		//if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {

			$query=$this->db->query("SELECT id,difficulty_id FROM bip_user WHERE id=? LIMIT 1",array($userid));

			if ($query->num_rows()>0) {

				$row=$query->row();
				$diffId=$row->difficulty_id;

				$settingData= $this->getSettingDataByDiffId($diffId);

				$difficulty_tag = $this->db->query("SELECT tag FROM bip_difficulty WHERE id=? LIMIT 1",array($row->difficulty_id))->row()->tag;

				$hide_graph = $this->isHideGraph($diffId);

				$likeString = '%,'.$userid.',%';
				$query1=$this->db->query("SELECT id AS taskid,
										       task AS heading,
										       tag,
										       completed
										FROM bip_tasks
										WHERE (CONCAT(',' , user_id , ',') LIKE ?)",array($likeString));

				$result=$query1->result_array();

				$newresult=array();
				foreach ($result as $rk => $rval) {
					$json=json_decode($rval['completed'],true);
					if (is_array($json) && array_key_exists($userid, $json)) continue;
					$rval['heading'] = @str_replace("'", "&#39;", $rval['heading']);
					$newresult[]=$rval;
				}

				$newdata=array();
				$newdata['training_begin_date'] = $this->getTraningStartDate($userid);
				$count = 0;
				foreach ($newresult as $v) {

					if ($v['tag']!=$difficulty_tag) {
						continue;
					}

					$newdata['task'][$count]['taskid'] = $taskid = $v['taskid'];
					if ($v['tag']=='1,2') {
						$v['tag']='3';
					}

					$newdata['task'][$count]['tag'] = $v['tag'];
					$newdata['task'][$count]['heading']=$v['heading'];
					$newdata['task'][$count]['hide_graph']=$hide_graph;

					$newdata['task'][$count]['training'] = $this->getOldTrainings($userid,$deviceId,$tokenkey,$taskid);
					$count++;
				}

				return $newdata;

			}
		//}
	}

	function getTraningStartDate($user_id){
		$query = $this->db->query("SELECT MIN(DATE_FORMAT(trainingdatetime, '%Y-%m-%d')) as training_begin_date from bip_training_app where user_id = ? and trainingdatetime!='0000-00-00 00:00:00'",array($user_id));
		$row = $query->row();

		$training_begin_date = '';

		if ($query->num_rows()>0) {
			$training_begin_date = $row->training_begin_date;
		}

		return $training_begin_date;

	}

	function getOldTrainings($userid,$deviceId,$tokenkey,$taskId)
	{
		if ($this->validateUserToken($userid,$deviceId,$tokenkey)) {
			$mQuery=$this->db->query("SELECT MAX(trainingdatetime) as trainingdatetime FROM `bip_training_app` WHERE user_id = ? AND task_id = ? LIMIT 1",array($userid,$taskId));
			$row=$mQuery->row();
			$futureDate= $row->trainingdatetime;

			$query=$this->db->query("SELECT id as trainingId,DATE_FORMAT(trainingdatetime, '%Y-%m-%d %H:%i') as trainingdatetime,estimatedvalue,estimatedvalue_end,training_duration,type,comment,edited FROM bip_training_app WHERE user_id=? AND task_id=? AND trainingdatetime <= ? AND trainingdatetime!='0000-00-00 00:00:00' AND practice=1 ORDER BY trainingdatetime DESC",array($userid,$taskId,$futureDate));
			if ($query->num_rows()>0) {
				$data=$query->result();
				return $data;
			}
		}
	}

	function convert_to_single_array($array) {
      $out = implode(",",array_map(function($a) {return implode("~",$a);},$array));
      return $out;
    }

	function getPatientAppReminder($data){

		extract($data);

		$userQry = $this->db->query("SELECT app_reminder,difficulty_id FROM bip_user WHERE id=?",array($userid));

		$row = $userQry->row();

		$difficulty_id = $row->difficulty_id;

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

        return $time;

       /* $first_index_time = $time[0];

        $appQry = $this->db->query("SELECT reminder_status FROM bip_user_app WHERE user_id='$user_id'");
        $row = $appQry->row();*/

        // $status_arr = json_decode($row->reminder_status,true);
        /*$played_time = $status_arr['played_time'];
        $played_at = $status_arr['played_at'];*/

        /*if (strtotime(date("Y-m-d")) > strtotime($played_at)) {
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

    		$newTime = $time;
    	}
        if (count($newTime)<1) {
        	$newTime = null;
        }else{
        	usort($newTime, create_function('$a, $b', 'return strcmp($a->hour, $b->hour);'));
        	$newTime = array_reverse($newTime);
        }

        $played_count = (count($oldTime)>=1) ? count($oldTime): 0;
        // $timelist = array('reminder'=>$newTime,'first_reminder'=>$first_index_time,'played_count'=>$played_count);
        $timelist = $newTime;*/

        // return $timelist;
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

    function getPatientDifficulty($userId){

    	$query = $this->db->query("SELECT difficulty_id FROM bip_user WHERE id=?",array($userId));
        $row = $query->row();
        $this->db->freeDBResource();

       	return $row->difficulty_id;
    }

    /**
     * get modules status to enable or disable on app
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    function getActiveModules($data)
		{
				extract($data);

				$row = $this->db->query("SELECT active_modules,manual_active_modules FROM bip_user_app WHERE user_id = ?",array($userid))->row();

				if (!empty($row->active_modules)) {
					$available_modules = json_decode($row->active_modules,true);
				}else{
					$available_modules = array(
					'registration'=>0,
					'homework_module'=>0,
					'homework_id'=>array(),
					'crisis_plan'=>0,
					'my_skills'=>0,
					'my_feelings'=>0,
					'other_modules'=>array()
					);
				}

				/* modules enabled by psychologist per patient */
				if (!empty($row->manual_active_modules)) {
					$manual_active_modules = json_decode($row->manual_active_modules,true);

					$isHomeworkEnabled = 0;
					$isMySkillsEnabled = 0;
					$isMyFeelingsEnabled = 0;
					foreach ($manual_active_modules as $module_key => $module_value) {
						if ($module_key=='registration' && $module_value)
							$available_modules['registration']=1;

						if ($module_key=='homework_module' && $module_value){
						 $available_modules['homework_module']=1;
						 $isHomeworkEnabled = 1;
						}

						if ($module_key=='homework_id' && !empty($module_value) && $isHomeworkEnabled){
							foreach ($module_value as $homework_id) {
								array_push($available_modules['homework_id'], $homework_id);
							}
						}

						if ($module_key=='crisis_plan' && $module_value)
						 $available_modules['crisis_plan']=1;

						if ($module_key=='my_skills' && $module_value){
						 $available_modules['my_skills']=1;
						 $isMySkillsEnabled = 1;
						}

						if ($isMySkillsEnabled) {
							if ($module_key=='my_feelings' && $module_value){
							 	$available_modules['my_feelings']=1;
							 	$isMyFeelingsEnabled = 1;
							}

							if ($available_modules['my_feelings'] && $module_key=='my_feelings_option' && $isMyFeelingsEnabled)
									 $available_modules['my_feelings']=$module_value;

							if ($module_key=='other_modules' && !empty($module_value)){
								foreach ($module_value as $module_id) {
									array_push($available_modules['other_modules'], $module_id);
								}
							}

						}

					}



					$other_module_ids  = array_unique($available_modules['other_modules']);

					$homework_ids = array_unique($available_modules['homework_id']);
					$homework_ids = array_values($homework_ids);
					$available_modules['other_modules'] = array_values($other_module_ids);
					$available_modules['homework_id'] = $homework_ids;

					if ($available_modules['homework_module'] && !empty($available_modules['homework_id'])) {
						$user = $this->minapp_model->getUserByUserId($userid);
						$psychologist_id = $user['psychologist_id'];

						$publishing_date = date("Y-m-d H:i:s");
						foreach ($available_modules['homework_id'] as $k => $homework_id) {
							$this->db->query( "INSERT INTO bip_my_homework_assignment (published_date,is_published,homework_id,patient_id,published_by) VALUES (?,1,?,?,?) ". "ON DUPLICATE KEY UPDATE is_published=1",array($publishing_date,$homework_id,$userid,$psychologist_id));

						}
					}


				}

				return $available_modules;
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
			$query=$this->db->query("SELECT difficulty_id FROM bip_user WHERE id='$userid' LIMIT 1");
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

	function isGraphHidden($userId){
		$get = $this->db->query("SELECT difficulty_id FROM bip_user WHERE id=?", array($userId))->row();
    	$difficulty_id = $get->difficulty_id;

    	$query = $this->db->query("SELECT hide_graph FROM bip_difficulty WHERE id=?",array($difficulty_id));
        $result = $query->row();
        return $result->hide_graph;
	}

	function activate_device($data){
		extract($data);
		$password = $this->encode5t($password);
		$activation_date = date("Y-m-d H:i:s");
		$update = $this->db->query("UPDATE bip_user_activation_codes c INNER JOIN bip_user u ON c.user_id = u.id SET
				device_uuid= ?,
				activated_date='".$activation_date."',
				code_used='1',
				is_activated='1',
				device_type= ? WHERE
				c.is_activated='0' AND
				c.activation_code= ? AND
				u.username = ? AND
				u.password= ?", array($deviceId,$deviceType,$verification_code,$username,$password));


		if($this->db->affected_rows()>0){
			//Now make entry inactive if same device is activated before
			$this->db->query("DELETE FROM bip_user_activation_codes WHERE device_uuid = ? AND activation_code!= ? ", array($deviceId, $verification_code));
			//return value
			$arry = array(
						"is_activated"=>1,
						"device_id"=>$deviceId,
						"activation_date"=>$activation_date
					);
			return $arry;
		}
	}
	/*Added by sabin 25th June <<*/

}
