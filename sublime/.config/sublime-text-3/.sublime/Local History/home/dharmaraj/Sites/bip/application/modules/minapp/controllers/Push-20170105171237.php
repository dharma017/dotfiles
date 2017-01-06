<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Push extends CI_Controller {

    private $app_key;
    private $app__master_secret;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('minapp/minapp_model');

        // BIP production
        $this->app_key = 'kF-niefxRgK7RzuD2wyQhw';
        $this->app__master_secret = 'COk20VGmTXu_r2IA65RaDA';
    }

    /**
     * check for each app user for push notification.
     */
    public function index()
    {
        $this->feedback_check();
        $this->reminder1_check();
        $this->reminder2_check();
    }

    /**
     * Urban push notification to IOS and Android platform
     * @param  string $platform     ios,android
     * @param  array  $device_token
     * @return int           receive a 200 response code, with no data in the body. This means that we accepted your request, and are processing it and sending it on to Apple!
     */
    public function urban_push($platform='iphone',$device_token,$msg,$extra = false)
    {
        $device_tokens=explode(',', $device_token);
        switch (strtolower($platform)) {
            case 'iphone':

                $application_key = $this->app_key;
                $application_master_secret = $this->app__master_secret;
                $contents = array();
                $contents['badge'] = "+1";
                $contents['alert'] = $msg;
                $contents['sound'] = "cow";
                $push = array("aps" => $contents,"device_tokens"=>$device_tokens);
                $json = json_encode($push);
                break;
            case 'android':
                $application_key = $this->app_key;
                $application_master_secret = $this->app__master_secret;
                $android = array();
                $android['alert'] = $msg;
                $push = array();
                $push['android'] = $android;
                $push['apids'] = $device_tokens;
                $json = json_encode($push);
                break;
            default:
                echo "\ncurrently only android or ios supported\n";
                exit;
                break;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://go.urbanairship.com/api/push/");
        curl_setopt($ch, CURLOPT_USERPWD, "$application_key:$application_master_secret");
        curl_setopt($ch, CURLOPT_USERPWD, "$application_key:$application_master_secret");
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HEADER, False);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_exec($ch);
        $response = curl_getinfo($ch);
        if($response['http_code'] != 200) {
             echo "Got negative response from server: " . $response['http_code'] . "\n";
         } else {

             echo "Wow, it worked!\n";
         }
        curl_close($ch);
        //sucess response = 200
    }

    public function urban_push_test()
    {
        $application_key = $this->app_key;
        $application_master_secret = $this->app__master_secret;
        $contents = array();
        $contents['badge'] = "+1";
        $contents['alert'] = "hello test";
        $contents['sound'] = "cow";
        $push = array("aps" => $contents,"device_tokens"=>array('0473e5a6347261ec9648ba7a3c748ad9fcefd20bea2621a781097f8d43eca5b1','d50f29ae4ad7e499aa6eb10aa4b39c46c06d989e41bc80ef9a1cfd26d8779991','d50f29ae4ad7e499aa6eb10aa4b39c46c06d989e41bc80ef9a1cfd26d8779991'));
        /*if( false !== $extra ) { $push['url'] = (string) $extra['url']; }*/
        $json = json_encode($push);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://go.urbanairship.com/api/push/");
        curl_setopt($ch, CURLOPT_USERPWD, "$application_key:$application_master_secret");
        curl_setopt($ch, CURLOPT_USERPWD, "$application_key:$application_master_secret");
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HEADER, False);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_exec($ch);
        $response = curl_getinfo($ch);
        if($response['http_code'] != 200) {
             echo "Got negative response from server: " . $response['http_code'] . "\n";
         } else {

             echo "Wow, it worked!\n";
         }
        curl_close($ch);
        //sucess response = 200
    }

    /**
     * Feedback check
     * Has this user done exactly one practice If yes send feedback. If no don’t send feedback.
     */
    public function feedback_check()
    {
        $feedbackUsers= $this->minapp_model->hasExactOnePractice();

        if (!empty($feedbackUsers)) {

            $notifyData = $this->minapp_model->getDefaultNotifcation();
            $sendFeedbackTo=array();

            foreach ($feedbackUsers as $user) {
                $xdays=($user->feedback_status && $user->feedback_xdays>0) ? $user->feedback_xdays: $notifyData->feedback_xdays;
                if ($user->DAYS==$xdays) { //send reminder
                    if ($user->feedback_status) {
                        $userApps=$this->minapp_model->getDeviceTokensPerUser($user->user_id);
                        foreach ($userApps as $app) {
                            $sendFeedbackTo[$app->user_id][$app->devicetype]['user_id']=$user->user_id;
                            $sendFeedbackTo[$app->user_id][$app->devicetype]['message']=(!empty($user->feedback_message)) ? $user->feedback_message: $notifyData->feedback_message;
                            $sendFeedbackTo[$app->user_id][$app->devicetype]['platform']= (!empty($app->devicetype)) ? $app->devicetype: 'iphone';
                            $sendFeedbackTo[$app->user_id][$app->devicetype]['tokenkey']= $app->tokenKeyArr;
                        }
                    }
                }
            }
            $finalPushArr=array();
            foreach ($sendFeedbackTo as $feedbacks) {
                foreach ($feedbacks as $feedback) {
                    $finalPushArr[]=$feedback;
                }
            }
            echo "<br>send feedback to ".json_encode($finalPushArr);

            $extra = array('url' => "1");
            //urban push
            foreach ($finalPushArr as $push) {
                $this->urban_push($push['platform'],$push['tokenkey'],$push['message'],$extra);
            }
        }
    }

    /**
     * Reminder1 check
     * 1.  Does this user have any active practice?
     * 2.  How many days was it since last practice
     * 3.  If the number of days since last practice = X then send reminder.
     * 4.  If the number of days is less OR more than X then don’t send reminder.
     */
    public function reminder1_check()
    {
       $reminder1Users = $this->minapp_model->hasAnyActivePractice(); // output in format: user_id~days_passed
       if (!empty($reminder1Users)) {

           $notifyData = $this->minapp_model->getDefaultNotifcation();
           $sendReminderTo=array();

           foreach ($reminder1Users as $user) {
                 $xdays=($user->reminder1_status && $user->reminder1_xdays>0) ? $user->reminder1_xdays: $notifyData->reminder1_xdays;
                 if ($user->DAYS==$xdays) { //send reminder
                     if ($user->reminder1_status) {
                         $userApps=$this->minapp_model->getDeviceTokensPerUser($user->user_id);

                         foreach ($userApps as $app) {
                            $sendReminderTo[$app->user_id][$app->devicetype]['user_id']=$user->user_id;
                            $sendReminderTo[$app->user_id][$app->devicetype]['message']=(!empty($user->reminder1_message)) ? $user->reminder1_message: $notifyData->reminder1_message;
                            $sendReminderTo[$app->user_id][$app->devicetype]['platform']= (!empty($app->devicetype)) ? $app->devicetype: 'iphone';
                            $sendReminderTo[$app->user_id][$app->devicetype]['tokenkey']= $app->tokenKeyArr;
                        }
                     }
                 }
           }
            $finalPushArr=array();
            foreach ($sendReminderTo as $feedbacks) {
                foreach ($feedbacks as $feedback) {
                    $finalPushArr[]=$feedback;
                }
            }
            echo "<br>send reminder 1 to ".json_encode($finalPushArr);

            $extra = array('url' => "1");
            //urban push
            foreach ($finalPushArr as $push) {
                $this->urban_push($push['platform'],$push['tokenkey'],$push['message'],$extra);
            }
       }
    }

    /**
     * Reminder2 check
     * 1.  Does this user have any active practice?
     * 2.  How many days was it since last practice
     * 3.  If the number of days since last practice = X then send reminder.
     * 4.  If the number of days is less OR more than X then don’t send reminder.
     */
    public function reminder2_check()
    {
       $reminder2Users = $this->minapp_model->hasAnyActivePractice2(); // output in format: user_id~days_passed
       if (!empty($reminder2Users)) {

           $notifyData = $this->minapp_model->getDefaultNotifcation();
           $sendReminderTo=array();

           foreach ($reminder2Users as $user) {
                 $xdays=($user->reminder2_status && $user->reminder2_xdays>0) ? $user->reminder2_xdays: $notifyData->reminder2_xdays;
                 if ($user->DAYS==$xdays) { //send reminder
                     if ($user->reminder2_status) {
                         $userApps=$this->minapp_model->getDeviceTokensPerUser($user->user_id);

                         foreach ($userApps as $app) {
                            $sendReminderTo[$app->user_id][$app->devicetype]['user_id']=$user->user_id;
                            $sendReminderTo[$app->user_id][$app->devicetype]['message']=(!empty($user->reminder2_message)) ? $user->reminder2_message: $notifyData->reminder2_message;
                            $sendReminderTo[$app->user_id][$app->devicetype]['platform']= (!empty($app->devicetype)) ? $app->devicetype: 'iphone';
                            $sendReminderTo[$app->user_id][$app->devicetype]['tokenkey']= $app->tokenKeyArr;
                        }
                     }
                 }
           }
            $finalPushArr=array();
            foreach ($sendReminderTo as $feedbacks) {
                foreach ($feedbacks as $feedback) {
                    $finalPushArr[]=$feedback;
                }
            }
            echo "<br>send reminder 2 to ".json_encode($finalPushArr);

            $extra = array('url' => "1");
            //urban push
            foreach ($finalPushArr as $push) {
                $this->urban_push($push['platform'],$push['tokenkey'],$push['message'],$extra);
            }
       }
    }

}

/* End of file push.php */
/* Location: ./application/modules/minapp/controllers/push.php */
