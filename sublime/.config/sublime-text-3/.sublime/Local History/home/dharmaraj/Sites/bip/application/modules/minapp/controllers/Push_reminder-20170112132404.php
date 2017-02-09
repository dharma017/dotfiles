<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Push_reminder extends CI_Controller
{
	public function __construct() {
		parent::__construct();

		$this->load->helper('service');
		$this->load->model('minapp/minapp_model');
	}

	/**
	 * check for each app user for push notification.
	 */
	public function index() {
		$this->reminderCheck();
	}

	function getTime(){
		echo "<br><br>Time in Sweden <br>".date('g:i A')."<br>";
		echo "<br><br>ENVIRONMENT <br>".ENVIRONMENT."<br>";
	}

	function parseNotify($list) {

		echo "<br>Push reminder sent to <br>".json_encode($list)."<br>";

		extract($list);

		$APPLICATION_ID =$this->config->item('parse_app_id');
		$REST_API_KEY =$this->config->item('parse_rest_api_key');
		$MASTER_KEY =$this->config->item('parse_master_key');
		$PARSE_API_ADDRESS = $this->config->item('parse_api_address');

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
		$headers[] = "X-Parse-Application-Id: 83UvumCLXDMrfNYtxS78YKViHRsd8MZikaHkosAI";
		$headers[] = "X-Parse-Master-Key: xhcrYQoTpLWLawP8C5VFgAwyDonwj2U43avkOGyf";
		$headers[] = "X-Parse-Rest-Api-Key: i4ZfjpOHWQ6NHiWJqabyFQtRQ36T08Kako1NFYHO";
		$headers[] = "Content-Type: application/json";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
				}else{
					$info = curl_getinfo($ch);
		  			echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "\n";
		  			echo $result."\n";
				}
		curl_close ($ch);
	}

	public function reminderCheck() {

		$sendUserList = array();

		$reminderUserList = $this->minapp_model->getRegisteredUsers();

		echo "<br>Current Swedish Time ".date('H:i');
		echo "<br>-----------------------------------<br>";

		$currentTime = date('H:i');

		if (!empty($reminderUserList)) {

			foreach ($reminderUserList as $rk => $user) {

				if (empty($user->devicetype)) continue;

					if($user->notification_enabled==0) continue; // if psychologist has set to turn off reminder notification for particular user, then don't include him

					$countActiveTasksToNotify = $this->minapp_model->countActiveTasksToNotify($user);

					echo "<br>====================================================================<br>";
					// skip user that has no active tasks
					if ($countActiveTasksToNotify==0 && $user->tag!=3){ //skip only those user who doesnot belong to self harm and has no active tasks.
						echo "Skipped User ".json_encode($user);
						continue;
					}else{
						echo "User ".json_encode($user);
					}
					echo "<br>Total Active Tasks ".$countActiveTasksToNotify;

					$app_reminder = $user->app_reminder;

					if (empty($user->app_reminder) || $user->app_reminder=='[]' || $user->app_reminder_type==0) {
						$userReminder = $this->minapp_model->getUserReminder($user->difficulty_id);
						$app_reminder = $userReminder;
					}

					$arr = json_decode($app_reminder, true);

					// skip if no reminder
					if (empty($arr)) {
						echo "<br>Skipped Reminder ".json_encode($arr);
						continue;
					}else{
						echo "<br>Reminder ".json_encode($arr);
					}

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

					echo "<br><br>====================================================================<br>";
				}

				echo "<br><br>-------------Parse Notify----------------------";
				echo "-----------------------------------<br>";
				if (ENVIRONMENT!='development') {
					foreach ($sendUserList as $list) {
						$this->parseNotify($list);
					}
				}

				echo "<br><br>----------------------------------------------------------------------<br>Push Sent User List<br>";
				echo json_encode($sendUserList)."<br>";

			}
		}

		public function reminderToSent($reminder) {

			$arr = json_decode($reminder, true);

			$newarr = array();
			foreach ($arr as $ak => $av) {
				$explodeItem = explode('~~~', $av);
				if (!_isTimeBetween($explodeItem[0])) {
					continue;
				}

				//converting am/pm to 24hours time format
				// $newarr[$ak]['24time'] = date("H:i", strtotime($explodeItem[0]));
				$newarr[$ak]['time'] = $explodeItem[0];
				$newarr[$ak]['message'] = $explodeItem[1];
				$newarr[$ak]['skip'] = (_isTimeBetween($explodeItem[0])) ? 0 : 1;
			}
			return $newarr;
		}
	}

	/* End of file push.php */

	/* Location: ./application/modules/minapp/controllers/push.php */
