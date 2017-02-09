<?php

class Login_model extends CI_Model {

    function validate_user() {

      $email 		= $this->input->post('username');
      $pass = $this->input->post('password');
      $user_role_type = $this->session->userdata('user_role_type');

		  $login_success = false;

			if ($user_role_type=='superadmin') {
        // $pass = $this->encode5t($pass);
				// superadmin login only
      	$query = $this->db->query("SELECT * FROM bip_admin_user WHERE username='$email' AND status=1");

      	$result = $query->row();

      	if($query->num_rows() == 1)
	      {
	          $permission = "";
	          $usertype = "admin";
	          $user_role_type = 'superadmin';

	          $result = $query->row();
	          $result->user_role = 3;

	          $this->db->freeDBResource();
                //if (!password_verify($pass, $result->pass)) return false;
                if (!$this->bcrypt->check_password($pass,$result->pass)) return false;

                $login_success = true;
	      }

			}else if ($user_role_type=='psychologist') {
				// psychologist login only
        $lang_id = $this->session->userdata('language_code');

		    $strSql = "SELECT * FROM bip_user WHERE username=? ";

		    $strSql .= "AND user_role=2 ";
		    $strSql .= "AND is_deleted=0 ";
            $strSql .= "AND lang_id=? ";
				$strSql .= "AND STATUS='1' AND (CURDATE() BETWEEN STR_TO_DATE(active_from, '%Y-%m-%d') AND STR_TO_DATE(active_to,'%Y-%m-%d'))";

				$query = $this->db->query($strSql,array($email,$lang_id));

				$this->db->freeDBResource();

				if ($query->num_rows() == 1)
				{
						$result = $query->row();
                        //if (!password_verify($pass, $result->password)) return false;
                        if (!$this->bcrypt->check_password($pass,$result->password)) return false;
						$permission = json_decode($result->permission,true);

						$usertype = "Psychologist";
            $user_role_type = 'psychologist';
            $this->session->set_userdata( 'language_code' , $result->lang_id );

            if (!(empty($permission)))
							$usertype = "admin";

            $data = date('Y-m-d h:i:s');
						$first_login = ($result->first_login ? $result->first_login : $data);
						$userId = $result->id;
						$numLogin = $result->no_of_login + 1;
						$strSql = "UPDATE bip_user SET no_of_login=?, last_login=now(),first_login=? WHERE id = ?";
						$this->db->query($strSql,array($numLogin,$first_login,$userId));

						$login_success = true;
				}

			}else {
				// patient login only
        $lang_id = $this->session->userdata('language_code');
				$strSql = "SELECT * FROM bip_user WHERE username=? ";

		    $strSql .= "AND user_role=1 ";
		    $strSql .= "AND is_deleted=0 ";
        $strSql .= "AND lang_id=? ";
				$strSql .= "AND STATUS='1' AND (CURDATE() BETWEEN STR_TO_DATE(active_from, '%Y-%m-%d') AND STR_TO_DATE(active_to,'%Y-%m-%d'))";

				$query = $this->db->query($strSql,array($email,$lang_id));

				$this->db->freeDBResource();

				if ($query->num_rows() == 1)
	      {
	          $result = $query->row();
                //if (!password_verify($pass, $result->password)) return false;
                if (!$this->bcrypt->check_password($pass,$result->password)) return false;
                $skin_code = $this->getSkinCodeById($result->difficulty_id);

	          $usertype = "user";
	          $user_role_type = 'patient';

            $this->session->set_userdata(array('difficulty_id' => $result->difficulty_id,'psychologist_id' => $result->psychologist_id,'sms_notify'=>$result->sms_notify));
            $this->session->set_userdata( 'language_code' , $result->lang_id );

	          $data = date('Y-m-d h:i:s');
	          $first_login = ($result->first_login ? $result->first_login : $data);
	          $userId = $result->id;
	          $numLogin = $result->no_of_login + 1;
	          $strSql = "UPDATE bip_user SET no_of_login=?, last_login=now(),first_login=? WHERE id = ?";
	          $this->db->query($strSql,array($numLogin,$first_login,$userId));

	          $login_success = true;
	      }

			}

			if (!$login_success) return false;

			$bass_completion = $this->getBassConnectionStatus();

            $result->first_name = $this->encryption->decrypt($result->first_name);
            $result->last_name = $this->encryption->decrypt($result->last_name);
            $result->email = $this->encryption->decrypt($result->email);
            $result->contact_number = $this->encryption->decrypt($result->contact_number);

			$userInformation = array(
				'logintype' => $usertype,
				'user_role_type'     => $user_role_type,
				'lang_id'	 => $result->lang_id,
				'email' => $result->email,
				'site_name' => 'bip',
				'user_id' => $result->id,
				'username' => $result->username,
				'first_name' => $result->first_name,
				'last_name' => $result->last_name,
				'contact_number'=>$result->contact_number,
				'permission' => $permission,
				'bip_logged_in' => true,
				'email_notify' => $result->email_notify,
				'user_role' => (!empty($result->user_role)) ? $result->user_role: 3,
				'log_time_from' => date('Y-m-d h:i:s'),
				'skins' => $skin_code,
				'bass_completion' => $bass_completion
			);

	    $this->session->set_userdata($userInformation);

			return true;

    }
    function getBassConnectionStatus(){
        $query=$this->db->query("SELECT bass_completion FROM bip_system_settings WHERE id=?",array(1));
        $row = $query->row();
        $this->db->freeDBResource();
        return ($row->bass_completion) ? true: false;

    }

    /**
     * track user activity per session
     */
    function bipUserTracking($currentPage=''){

        $session_id = $this->session->userdata('session_id');

        $userId = $this->session->userdata('user_id');
        $agent = getUserAgent();

        $ip = $this->input->ip_address();

        $logout_at = date('Y-m-d H:i:s');

        if (empty($currentPage)) {
            if ($this->agent->is_referral())
                $exit_page = str_replace(base_url().'index.php', '', $this->agent->referrer());
            else
                $exit_page = uri_string();
        }else{
            $exit_page = $currentPage;
        }


        $this->db->query( "INSERT INTO bip_user_tracking (user_id,agent,ip,login_at,session_id) VALUES (?,?,?,now(),?) ". "ON DUPLICATE KEY UPDATE logout_at=?,exit_page=?", array($userId,$agent,$ip,$session_id,$logout_at,$exit_page));
        // echo $this->db->last_query();exit;
    }

    function bipLogAllLoginsToSystem($login_status=0,$remarks=''){

    	// $session_id = $this->session->userdata('session_id');
    	$sms_attempt = $this->session->userdata('sms_attempt');
    	$sms_code_in = ($sms_attempt>0) ? $this->input->post('security_code'): '';
    	$user_id = $this->session->userdata('user_id');
    	$username = $this->session->userdata('username');
    	$username = (!empty($username)) ? $username: $this->session->userdata('email');
    	$user_role = $this->session->userdata('user_role');

    	$sms_code = $this->db->query("SELECT code FROM bip_user_sms WHERE user_id=?",array($user_id))->row()->code;

    	$agent = getUserAgent();

      $ip = $this->input->ip_address();

      // $this->db->query( "INSERT INTO bip_login_activity (user_id,username,user_role,agent,ip,login_status,sms_attempt,sms_code,sms_code_in,session_id,remarks) VALUES ('$user_id','$username','$user_role','$agent','$ip','$login_status','$sms_attempt','$sms_code','$sms_code_in','$session_id','$remarks')");
        // echo $this->db->last_query();exit;
      $insertArr = array(
          'user_id'=> $user_id,
          'username'=> $username,
          'user_role'=> $user_role,
          'agent'=> $agent,
          'ip'=> $ip,
          'login_status'=> $login_status,
          'sms_attempt'=> $sms_attempt,
          'sms_code'=> $sms_code,
          'sms_code_in'=> $sms_code_in,
          'remarks'=> $remarks
      );
      $this->db->insert('bip_login_activity',$insertArr);
    }

    function invalidate_user_status($email, $pw) {

    	 $this->db->freeDBResource();

        $user_role_type = $this->session->userdata('user_role_type');

        if ($user_role_type=='superadmin') {
	        $query = $this->db->query("Select count(*) as totalno from bip_admin_user WHERE email = ? and status=1",array($email));
        }else{
	        $query = $this->db->query("Select count(*) as totalno from bip_user WHERE email = ? AND is_deleted=0",array($email));
        }

        $no = $query->row()->totalno;
        return $no;
    }


	function getBassID() {

        $user_id = $this->session->userdata["user_id"];
        $result = $this->db->query("Select part_id from bip_bass where user_id =?",array($user_id));
        $part_id = $result->row()->part_id;
        $this->db->freeDBResource();
        if(!$part_id)
        {
            $result = $this->db->query("Select username from bip_user where id =? ",array($user_id));
            $part_id = $result->row()->username;
            $this->db->freeDBResource();
        }
        return $part_id;
    }

    function getBassUrl() {

		$user_id = $this->session->userdata["user_id"];
        $query = $this->db->query("SELECT
                        bu.difficulty_id,
                        bq.query_url
                    FROM
                        bip_user bu
                    LEFT JOIN bip_bass_query bq ON (
                        bq.difficulty_id = bu.difficulty_id
                    )
                    WHERE
                        bu.id = ? LIMIT 1",array($user_id));
        $row = $query->row();
        if (empty($row->query_url)) {
            $query1 = $this->db->query("SELECT query_url FROM bip_bass_query WHERE difficulty_id=0 LIMIT 1");
            $row = $query1->row();
        }

        return $row->query_url;
    }



    function validate_admin() {
        $username = $this->input->post('username');
        $pass = $this->encode5t($this->input->post('password'));
        //echo "Email :$email |  Password : $pass";
        $query = $this->db->query("call checkAdminLoginByEmail('$username','$pass')");
        if ($query->num_rows() == 1) {
            echo 'valid user';
            return $query->row();
        }
        else
            return false;

        $query->free_result();

        //$this->db->where('username', $this->input->post('username'));
        //this->db->where('password', md5($this->input->post('password')));
        //$query = $this->db->get('membership');
        //if($query->num_rows() == 1)
        //{
        //return true;
        //}
    }

    function create_member() {

        $new_member_insert_data = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'email_address' => $this->input->post('email_address'),
            'username' => $this->input->post('username'),
            'password' => md5($this->input->post('password'))
        );

        $insert = $this->db->insert('membership', $new_member_insert_data);
        return $insert;
    }

    function get_member_details($id=false) {
        if (!$id) {
            // Set Active Record where to the current session's username
            if ($this->session->userdata('username')) {
                $this->db->where('username', $this->session->userdata('username'));
            } else {
                // Return a non logged in person from accessing member profile dashboard
                return false;
            }
        } else {
            // get the user by id
            $this->db->where('id', $id);
        }
        // Find all records that match this query
        $query = $this->db->get('membership');
        // In this case because we don't have a check set for unique username
        // we will return the last user created with selected username.
        if ($query->num_rows() > 0) {
            // Get the last row if there are more than one
            $row = $query->last_row();
            // Assign the row to our return array
            $data['id'] = $row->id;
            $data['first_name'] = $row->first_name;
            $data['last_name'] = $row->last_name;
            // Return the user found
            return $data;
        } else {
            // No results found
            return false;
        }
    }

    function bip_logged_in() {

        $bip_logged_in = $this->session->userdata('bip_logged_in');
        //echo $this->session->userdata('bip_logged_in');

        if (!isset($bip_logged_in) || $bip_logged_in != true) {
            //if(!$this->session->userdata('bip_logged_in'))
            echo 'You don\'t have permission to access this page. <a href="../login">Login >></a>';
            //die();		d
            //$this->load->view('login_form');
        } else {
            echo 'You are logged in !!';
        }
    }

    function logoutset() {
        $user_id = $this->session->userdata('user_id');
        $query = $this->db->query("call selectlastlogin_time('$user_id')");
        //echo "selectlastlogin_time('$user_id')";
        $row1 = $query->row();
        //print_r($row1);
        $this->db->freeDBResource();

        //echo time() ."|". strtotime($row1->last_login);
        /*
          $logged_in_time=time() - strtotime($row1->last_login);

          //$lastlogin=$
          $strSql = "update bip_user SET total_time_in_system=total_time_in_system+".$logged_in_time." WHERE id = '$user_id'";
          //die();

          $this->db->query($strSql);
          //echo "update bip_user SET total_time_in_system=total_time_in_system+".$logged_in_time." WHERE email = '$email'";

          $this->db->freeDBResource();
         */
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

    // functin to update user total time
    function updateDuration($user_id, $time) {
        // echo "user: ".$user_id.'=> time: '.$time;exit;
        if (!$user_id)
            return false;
        $query = $this->db->query("UPDATE bip_user set total_time_in_system = (IFNULL(total_time_in_system, 0)  + $time) where id=?",array($user_id));
        // $query = $this->db->query("UPDATE bip_user set total_time_in_system = total_time_in_system + '$time' where id='$user_id'");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function hasPhoneNumber()
    {
        $userId = $this->session->userdata("user_id");
        $query = $this->db->query("call getUserByUserId($userId)");
        $row = $query->row();
        $row->contact_number = $this->encryption->decrypt($row->contact_number);
        $row->contact_number_1 = $this->encryption->decrypt($row->contact_number_1);
        $this->db->freeDBResource();
        return $row;
    }

    function hasAdminPhoneNumber(){
        $userId = $this->session->userdata("user_id");
        $query = $this->db->query("SELECT * FROM bip_admin_user WHERE id=? AND status=1",array($userId));
        $row = $query->row();
        $row->contact_number = $this->encryption->decrypt($row->contact_number);
        $row->contact_number_1 = $this->encryption->decrypt($row->contact_number_1);
        $this->db->freeDBResource();
        return $row;
    }

    function checkSmsOff(){

        $email = $this->input->post('username');

        if(filter_var($email, FILTER_VALIDATE_EMAIL)) { //superadmin
             $query = $this->db->query("SELECT contact_number FROM bip_admin_user WHERE email=? AND sms_login=?",array($email,0));
             if($query->num_rows() > 0){
                return true;
            }else{
                return false;
            }
        }
        else { //superadmin vs user
            $query1 = $this->db->query("SELECT contact_number FROM bip_admin_user WHERE email=? AND sms_login=0",array($email));
            if ($query1->num_rows()>0) {
                return true;
            }else{
                $query2 = $this->db->query("SELECT contact_number FROM bip_user WHERE username=? AND contact_number=''",array($email));
                if ($query2->num_rows() > 0) {
                    return true;
                }else{
                    return false;
                }
            }

        }

    }

    function checkSmsActivated()
    {
        $userId = $this->session->userdata("user_id");
        $query = $this->db->query("SELECT * FROM bip_user_sms WHERE user_id=? AND activate=1",array($userId));
        if ($query->num_rows()>0) { //activated
            return true;
        }else{
            return false;
        }
    }

    function insertSmsValidation($user,$code)
    {
        $user_id=$user->id;
        $contact_number=$user->contact_number;
        $contact_number = $this->encryption->encrypt($contact_number);
        $contact_number_1=$user->contact_number_1;
        $contact_number_1 = $this->encryption->encrypt($contact_number_1);
        $user_role= (!empty($user->user_role)) ? $user->user_role: 3;

        //$this->session->set_userdata('popUp', 0);

        // $this->db->query("INSERT INTO bip_user_sms (user_id,contact_number,contact_number_1,code,time_sent,activate,user_role) VALUES ('$user_id','$contact_number','$contact_number_1','$code',now(),0,'$user_role') ". "ON DUPLICATE KEY UPDATE code='$code',time_sent=now(),activate=0,contact_number='$contact_number',contact_number_1='$contact_number_1'");

        $sql = "INSERT INTO bip_user_sms    (user_id,contact_number,contact_number_1,code,time_sent,activate,user_role) VALUES (?,?,?,?,now(),0,?) ". "ON DUPLICATE KEY UPDATE code=VALUES(code),time_sent=now(),activate=0,contact_number=VALUES(contact_number),contact_number_1=VALUES(contact_number_1)";

        $this->db->query($sql,array(
          $user_id,
          $contact_number,
          $contact_number_1,
          $code,
          $user_role
        ));
    }

    function validate_code()
    {
        $code=$this->input->post('security_code');
        $userId = $this->session->userdata("user_id");
        $user_role = $this->session->userdata("user_role");

        $query=$this->db->query("UPDATE bip_user_sms SET activate=1 WHERE user_id=? AND user_role=? AND code=? AND time_sent >= NOW()-INTERVAL 5 MINUTE",array($userId,$user_role,$code));
        if ($this->db->affected_rows()>0) {
            return true;
        }else{
            return false;;
        }
    }


    function validate_user_language($username,$password){

        $this->db->freeDBResource();

        $lang_id = $this->session->userdata('language_code');

        $password = $this->encode5t($password);

        $query = $this->db->query("Select id from bip_user WHERE username = ? AND password=? and is_deleted=0 and lang_id=?",array($username,$password,$lang_id));

        if ($query->num_rows>0) {
            return true;
        }else{
            return false;
        }
    }
     function validate_email_password($username,$password){
        $this->db->freeDBResource();

        $password = $this->encode5t($password);

        $query = $this->db->query("Select id from bip_user WHERE username = ? AND password=? and is_deleted=0",array($username,$password));

        if ($query->num_rows>0) {
            return true;
        }else{
            return false;
        }
     }
    function checkCodeMatch(){
        $code=$this->input->post('security_code');
        $userId = $this->session->userdata("user_id");
        $query = $this->db->query("SELECT * FROM bip_user_sms WHERE user_id=? AND code=?",array($userId,$code));
        if ($query->num_rows()>0) { //code found
            return true;
        }else{
            return false;
        }
    }

    function getSkinCodeById($id){
    	$skin_id = $this->getSkinByDifficultyID($id);
			$query = $this->db->query("SELECT skin_code FROM bip_skin WHERE id=?",array($skin_id));
			$row = $query->row();
			return $row->skin_code;
		}

		function getSkinByDifficultyID($id){
			$query = $this->db->query("SELECT skin_id FROM bip_difficulty WHERE id=?",array($id));
			$row = $query->row();
			return $row->skin_id;
		}

        function SaveLoginReport($user_id,$value){
            $session_id = $this->session->userdata("session_id");
            $date = date("Y-m-d H:i:s");
            $query = $this->db->query("INSERT INTO bip_form_data (user_id, message, send_date, last_updated, session_id, status) VALUES (?, ?, ?, ?,?, '1')",array($user_id,$value,$date,$date,$session_id));
            //echo "<pre>";print_r($query);exit;
		}

}
