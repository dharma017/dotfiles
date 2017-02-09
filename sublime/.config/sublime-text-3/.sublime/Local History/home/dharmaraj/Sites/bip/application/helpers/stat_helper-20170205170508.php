<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    function _isValidTimeStamp($timestamp)
    {
        return ((string) (int) $timestamp === $timestamp) 
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }

    function _getGroupAverageData($users)
    {
        $groupCharCount=0;
        $count_total_time_logged_in=0;
        $count_average_time_per_login=0;
        $groupLoginCount=0; //between logins
        foreach ($users as $userg) {

            $groupCharCount+=_getTotalCharacterSubmitted($userg->id);

            if ($userg->total_time_in_system>0  && $userg->no_of_login>0) {
                $count_total_time_logged_in+= $userg->total_time_in_system;
                $count_average_time_per_login+= @($userg->total_time_in_system/$userg->no_of_login);
            }

            $group_average_time_between_logins=@(_date_difference($userg->first_login,$userg->last_login)/($userg->no_of_login+$userg->no_of_login_old));
            $group_average_time_between_logins=@round($group_average_time_between_logins);
            $group_average_time_between_logins = ($userg->no_of_login>0) ? $group_average_time_between_logins: 0;
            $groupLoginCount+=$group_average_time_between_logins;

        }

        $groupLoginCount1=@round($groupLoginCount/count($users));

        if ($groupLoginCount1<=0) {
            $groupLoginCount1='0 dag';
        }elseif ($groupLoginCount1>=0 && $groupLoginCount1<=1) {
            $groupLoginCount1=$groupLoginCount1.' dag';
        }else{
            $groupLoginCount1=$groupLoginCount1.' dagar';
        }

        $data['groupCharCount']=$groupCharCount;
        $data['group_total_time_logged_in']=@_secondsToWords($count_total_time_logged_in);
        $data['group_average_time_per_login']=@_secondsToWords($count_average_time_per_login);
        $data['groupLoginCount']=$groupLoginCount1;
        return $data;
    }

    function _getAverageData($user)
    {
        if ($user->total_time_in_system>0 && $user->no_of_login>0) {
            $data['total_time_logged_in']= @_secondsToWords($user->total_time_in_system);
            $data['average_time_per_login']= @_secondsToWords($user->total_time_in_system/$user->no_of_login);
        }else{
            $data['total_time_logged_in']='0 sec';
            $data['average_time_per_login']='0 sec';
        }
        $average_time_between_logins=@(_date_difference($user->first_login,$user->last_login)/($user->no_of_login+$user->no_of_login_old));
        $average_time_between_logins=@round($average_time_between_logins);
        $average_time_between_logins = ($user->no_of_login>0) ? $average_time_between_logins: 0;
        if ($average_time_between_logins<=0) {
            $average_time_between_logins='0 dag';
        }elseif ($average_time_between_logins>=0 && $average_time_between_logins<=1) {
            $average_time_between_logins=$average_time_between_logins.' dag';
        }else{
            $average_time_between_logins=$average_time_between_logins.' dagar';
        }
        $data['average_time_between_logins']=$average_time_between_logins;
        return $data;
    }

	function _getCountFormData($userId)
    {   
    	$CI =& get_instance();
    	$CI->load->model('statistics/statistics_model');
        $formDataArr=$CI->statistics_model->getFormDataByUserIdToCount($userId);
        $total=0;
        $count=0;
        foreach ($formDataArr as $key1 => $formDataJson) {
            $jsonArr=json_decode($formDataJson['message'],true);
            if (empty($jsonArr)) continue;
            $length=0;
            foreach ($jsonArr as $key2 => $value2) {
                if($key2=='reference') continue;
                if($key2=='ladder') continue;
                if (strstr($value2, "~||~")) {
                    $length+=mb_strlen(end(explode('~||~', $value2)))+1;
                }else{
                    $length+=mb_strlen($value2);
                }
            }
            $count+=$length;
            $total=$count;
       }
       return $total;
    }


    function _getCountWorksheetData($userId)
    {
    	$CI =& get_instance();
    	$CI->load->model('statistics/statistics_model');
        $worksheetDataArr=$CI->statistics_model->getWorksheetDataByUserIdToCount($userId);
        $total=0;
        foreach ($worksheetDataArr as $value) {
            $txt= html2text($value['comments']);
            $total+=mb_strlen($txt);
        }
        return $total;
    }

    function _getCountMessageData($userId)
    {
    	$CI =& get_instance();
    	$CI->load->model('statistics/statistics_model');
        $messageDataArr=$CI->statistics_model->getMessageDataByUserIdToCount($userId);
        $total=0;
        foreach ($messageDataArr as $key=>$value) {
            $msg_subject= html2text($value['msg_subject']);
            $msg_subject=str_replace('Re: ', '', $msg_subject);
            $total+=mb_strlen($msg_subject);
            
            $message= html2text($value['message']);echo "<br>";
            if (stristr($message, 'wrote:')) {
                preg_match("/<p>(.+?)<hr \/>/is", $message, $matches);
                $message=html2text($matches[1]);
            }
            $total+=mb_strlen($message);
        }
        return $total;
    }

    function _getCountWeeklyTrainingData($userId)
    {
    	$CI =& get_instance();
    	$CI->load->model('statistics/statistics_model');
        $weeklyTrainingDataArr=$CI->statistics_model->getWeeklyTrainingDataByUserIdToCount($userId);
        $total=0;
        $count=0;
        foreach ($weeklyTrainingDataArr as $key => $value) {
            $length=0;
            foreach ($value as $key1 => $value1) {
                if ($key1=='stage') {
                     $length+= mb_strlen($value1);
                }else{
                    $jsonArr=json_decode($value1,true);
                    $c=0;
                    foreach ($jsonArr as $key2=>$value2) {
                        if (!empty($value2)) {
                            $c+=mb_strlen($value2);
                        }
                    }
                    $length+=$c;
                }
            }
            $count+=$length;
            $total=$count;
        }
        return $total;
    }

    function _getTotalCharacterSubmitted($userId)
    {
        return _getCountFormData($userId)+_getCountWorksheetData($userId)+_getCountMessageData($userId)+_getCountWeeklyTrainingData($userId);
    }

/* End of file stat_helper.php */
/* Location: ./application/helpers/stat_helper.php */