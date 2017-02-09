<?php
class Push_reminder extends CI_Controller {
	public function __construct() {
		parent::__construct();

		$this->load->config('minapp/parse');
		$this->load->helper('service');
		$this->load->model('minapp/minapp_model');
	}

	public function index() {
		$this->reminderCheck();
	}

	public function get_time(){
		echo "Current Time: ".date('H:i')."<br><br>";
		echo "Time in Sweden: ".date('g:i A')."<br><br>";
		echo "ENVIRONMENT: ".ENVIRONMENT."<br><br>";
	}

	public function get_current_installations(){

		$PARSE_API_ADDRESS = $this->config->item('parse_installations_url');
		$APPLICATION_ID =$this->config->item('parse_app_id');
		$MASTER_KEY =$this->config->item('parse_master_key');
		$REST_API_KEY =$this->config->item('parse_rest_api_key');

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $PARSE_API_ADDRESS);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		// curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = "X-Parse-Application-Id: $APPLICATION_ID";
		$headers[] = "X-Parse-Master-Key: $MASTER_KEY";
		$headers[] = "X-Parse-Rest-Api-Key: $REST_API_KEY";
		// $headers[] = "Content-Type: application/json";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}else{
			$info = curl_getinfo($ch);
  			// echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "<br><br>";
		}

		curl_close ($ch);

		$result_array = json_decode($result,true);


		$installations = array();
		foreach ($result_array as $key => $row_array) {
			foreach ($row_array as $key => $row) {
				$installations[] = $row['installationId'];
			}
		}

		return $installations;
	}

	function send_push_notification($list) {

		echo "Reminder User list<br>".json_encode($list)."<br><br>";

		extract($list);

		$PARSE_API_ADDRESS = $this->config->item('parse_push_url');
		$APPLICATION_ID =$this->config->item('parse_app_id');
		$REST_API_KEY =$this->config->item('parse_rest_api_key');
		$MASTER_KEY =$this->config->item('parse_master_key');

		$data = array(
			"where"=>array(
				"installationId"=>$installationId
				),
			"data"=>array(
				'alert' => $message
				)
			);
		$post = json_encode($data);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://parseapi.back4app.com/push");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = "X-Parse-Application-Id: $APPLICATION_ID";
		$headers[] = "X-Parse-Master-Key: $MASTER_KEY";
		$headers[] = "X-Parse-Rest-Api-Key: $REST_API_KEY";
		$headers[] = "Content-Type: application/json";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}else{
			$info = curl_getinfo($ch);
			echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "<br><br>";
			echo $result."<br><br>";
		}
		curl_close ($ch);
	}

	public function reminderCheck() {

		//current swedish time
		$currentTime = date('H:i');

		$sendUserList = array();

		$reminderUserList = $this->minapp_model->getRegisteredUsers();

		$installations = $this->get_current_installations();

		echo "Current swedish time: ".$currentTime."<br><br>";
		echo "Current Installations: ".json_encode($installations)."<br><br>";

		if (!empty($reminderUserList)) {

			foreach ($reminderUserList as $rk => $user) {

				if (!in_array($user->installationId, $installations)) continue;
				echo "Parse User <br>".json_encode($user)."<br><br>";

				if (empty($user->devicetype)) continue;

				// if psychologist has set to turn off reminder notification for particular user, then don't include him
				if($user->notification_enabled==0) continue;

				$countActiveTasksToNotify = $this->minapp_model->countActiveTasksToNotify($user);

				// skip user that has no active tasks
				//skip only those user who doesnot belong to self harm and has no active tasks.
				if ($countActiveTasksToNotify==0 && $user->tag!=3) continue;

				$app_reminder = $user->app_reminder;

				if (empty($user->app_reminder) || $user->app_reminder=='[]' || $user->app_reminder_type==0) {
					$userReminder = $this->minapp_model->getUserReminder($user->difficulty_id);
					$app_reminder = $userReminder;
				}

				$arr = json_decode($app_reminder, true);

				// skip if no reminder
				if (empty($arr)) continue;

				$newarr = array();
				foreach ($arr as $ak => $av) {
					$explodeItem = explode('~~~', $av);

					$dtime =  strtotime("+1 minute", strtotime($explodeItem[0]));
					$eTime =  date('H:i', $dtime);
					if ($currentTime!=$eTime) {
						continue;
					}

					$newarr['time'] = $explodeItem[0];
					$newarr['message'] = $explodeItem[1];
				}

				if (empty($newarr)) continue;

				$sendUserList[$user->user_id] = $newarr;
				$sendUserList[$user->user_id]['installationId'] = $user->installationId;
				$sendUserList[$user->user_id]['tokenkey'] = $user->tokenkey;
				$sendUserList[$user->user_id]['devicetype'] = strtolower($user->devicetype);
				$sendUserList[$user->user_id]['user_id'] = $user->user_id;
				$sendUserList[$user->user_id]['difficulty_id'] = $user->difficulty_id;

			}

			if (ENVIRONMENT!='development') {
				foreach ($sendUserList as $list) {
					$this->send_push_notification($list);
				}
			}else{
				echo "Reminder send to <br>".json_encode($sendUserList)."<br><br>";
			}

		}
	}
}
