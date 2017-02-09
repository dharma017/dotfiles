<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct() {
		parent::__construct();

		$this->config->load('recaptcha');

		$this->load->model('login/login_model');
		$this->load->model('worksheet/worksheet_model');
		$this->load->library('user_agent');
		$this->load->helper('cookie');

		if (ENVIRONMENT=='production') {
			force_ssl();
		}

	}

	function index() {

		$segment1 = $this->uri->segment(1);
		$segment2 = $this->uri->segment(2);
		$this->session->set_userdata('popUp' , 0);

		$login_attempt = $this->session->userdata('login_attempt');
		if (!$login_attempt) {
		  $this->session->set_userdata( 'login_attempt' , 0 );
		}

		$this->session->unset_userdata('user_role_type');
		$this->session->unset_userdata('login_form');
		$this->session->unset_userdata('login_from');

		if (in_array($segment1, array('en','no'))) { //english and norwegian routes
		   if ($this->uri->segment(2)=='superadmin') {
			$this->session->set_userdata( 'user_role_type' , 'superadmin' );
			$this->session->set_userdata( 'login_form' , 'login/superadmin' );
		  }elseif ($this->uri->segment(2)=='psychologist') {
			$this->session->set_userdata( 'user_role_type' , 'psychologist' );
			$this->session->set_userdata( 'login_form' , 'login/psychologist' );
		  }else{
			$this->session->set_userdata( 'user_role_type' , 'patient' );
			$this->session->set_userdata( 'login_form' , 'login/patient' );
		  }
		  if ($segment1=='en') {
			$this->session->set_userdata( 'language_code' , 2 );
			 $this->lang->load('super', 'english');
			 $l='english';
		  }else{
			 $this->session->set_userdata( 'language_code' , 3 );
			$this->lang->load('super', 'norwegian');
			$l='norwegian';
		  }
		}else{ //swedish routes
		  $this->session->set_userdata( 'language_code' , 1 );
		  $this->lang->load('super', 'swedish');
		  $l='swedish';

		  if ($this->uri->segment(1)=='superadmin') {
			$this->session->set_userdata( 'user_role_type' , 'superadmin' );
			$this->session->set_userdata( 'login_form' , 'login/superadmin' );
		  }elseif ($this->uri->segment(1)=='psychologist') {
			$this->session->set_userdata( 'user_role_type' , 'psychologist' );
			$this->session->set_userdata( 'login_form' , 'login/psychologist' );
		  }else{
			$this->session->set_userdata( 'user_role_type' , 'patient' );
			$this->session->set_userdata( 'login_form' , 'login/patient' );
		  }

		}

		$file='super_lang.php';

		 if($l!==FALSE && $file!==FALSE && is_dir(APPPATH."language/$l/") && file_exists(APPPATH."language/$l/$file")){
								require(APPPATH."language/$l/$file");

								$js_more_array = array('yes','no','save','close');

								$new_array = array();
								foreach ($lang as $key => $value) {
									if (substr($key, 0, 3) == "js_") {
										$new_key = str_replace('js_', '', $key);
										$new_array[$new_key] = $value;
									}

									if (in_array($key, $js_more_array)) {
										$new_array[$key] = $value;
									}

								}

								$this->session->set_userdata('jsLang', $new_array );
						}

		// echo "<pre>";print_r($this->session->all_userdata());exit;
		$this->session->set_userdata('login_from', current_url());


				$difficulty_id = $this->input->post("difficulty_id");
				$cookie = array(
					'name'   => 'login_from',
					'value'  => current_url(),
					'expire' => '15552000'
				);
				$this->input->set_cookie($cookie);

		// echo "<pre>";print_r($this->session->all_userdata());exit;

		$bip_logged_in = $this->session->userdata('bip_logged_in');
		if (isset($bip_logged_in) && $bip_logged_in == true) {
			redirect(site_url('stage'));
		} else {
			$data["main_content"] = $this->session->userdata('login_form');
			$data["template_header"] = 'includes/template_header_sms';
			$data["template_footer"] = 'includes/template_footer_sms';
			$this->load->view('includes/template', $data);
		}


	}

function old_browser() {
		echo '
			<!--[if gt IE 6]>
			<script>
			location.replace("<?php echo site_url("login"); ?>");
			</script>
			<![endif]-->
			';
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('bip_logged_in');
		$this->session->sess_destroy();
		$data["main_content"] = 'login/old_browser';
		$data["browser_type"] = 'outdated';
		$data["template_header"] = 'includes/template_header';
		$data["template_footer"] = 'includes/template_footer';
		$this->load->view('includes/template', $data);
	}


	/**
	 * form to submit sms security code
	 * and perform validation on security code
	 */
	function smsForm() {

		$this->lang->load('super', 'swedish');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('security_code', lang('security'), 'trim|required|xss_clean');
		if ($this->form_validation->run()==true) {

			$sms_attempt = $this->session->userdata('sms_attempt');
			$sms_attempt++;
			$this->session->set_userdata('sms_attempt', $sms_attempt);

			$query=$this->login_model->validate_code();
			if ($query) {
				$this->session->set_userdata( 'bip_logged_in', true );

				$this->login_model->bipLogAllLoginsToSystem(1);
				if ($this->session->userdata('user_role_type')=='superadmin') {
					redirect('stage/admin');
				}else{
					redirect('stage');
				}

			}else{
				if (!$this->login_model->checkCodeMatch()) {
					$this->session->set_flashdata('msg', lang('security_code_invalid'));
							$this->login_model->bipLogAllLoginsToSystem(0,lang('security_code_invalid'));
							$this->session->set_userdata('popUp' , 0);
				}else{
					$this->session->set_flashdata('msg', lang('security_code_timeout'));
							$this->login_model->bipLogAllLoginsToSystem(0,lang('security_code_timeout'));
				}

				redirect(site_url('login/smsForm'));
			}
		}
		$data['login_form'] = "loginForm";
		$data["main_content"] = 'login/sms_form';
		$data["template_header"] = 'includes/template_header_sms';
		$data["template_footer"] = 'includes/template_footer_sms';
		$this->load->view('includes/template', $data);
	}

	function smsFormEng() {

		$this->lang->load('super', 'english');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('security_code', lang('security'), 'trim|required|xss_clean');
		if ($this->form_validation->run()==true) {
			$sms_attempt = $this->session->userdata('sms_attempt');
			$sms_attempt++;
			$this->session->set_userdata('sms_attempt', $sms_attempt);
			$query=$this->login_model->validate_code();
			if ($query) {
				$this->session->set_userdata( 'bip_logged_in', true );

				$this->login_model->bipLogAllLoginsToSystem(1);
				 redirect('stage');

			}else{
				if (!$this->login_model->checkCodeMatch()) {
					$this->session->set_flashdata('msg', lang('security_code_invalid'));
							$this->login_model->bipLogAllLoginsToSystem(0,lang('security_code_invalid'));
							$this->session->set_userdata('popUp' , 0);
				}else{
					$this->session->set_flashdata('msg', lang('security_code_timeout'));
							$this->login_model->bipLogAllLoginsToSystem(0,lang('security_code_timeout'));
				}

				redirect(site_url('login/smsFormEng'));
			}
		}
		$data['login_form'] = "loginFormEng";
		$data["main_content"] = 'login/sms_form';
		$data["template_header"] = 'includes/template_header_sms';
		$data["template_footer"] = 'includes/template_footer_sms';
		$this->load->view('includes/template', $data);
	}


	 function smsFormNorway() {

		$this->lang->load('super', 'norwegian');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('security_code', lang('security'), 'trim|required|xss_clean');
		if ($this->form_validation->run()==true) {
			$sms_attempt = $this->session->userdata('sms_attempt');
	$sms_attempt++;
	$this->session->set_userdata('sms_attempt', $sms_attempt);
			$query=$this->login_model->validate_code();
			if ($query) {
				$this->session->set_userdata( 'bip_logged_in', true );

				$this->login_model->bipLogAllLoginsToSystem(1);

				redirect('stage');

			}else{
				if (!$this->login_model->checkCodeMatch()) {
					$this->session->set_flashdata('msg', lang('security_code_invalid'));
							$this->login_model->bipLogAllLoginsToSystem(0,lang('security_code_invalid'));
							$this->session->set_userdata('popUp' , 0);
				}else{
					$this->session->set_flashdata('msg', lang('security_code_timeout'));
							$this->login_model->bipLogAllLoginsToSystem(0,lang('security_code_timeout'));
				}

				redirect(site_url('login/smsFormNorway'));
			}
		}
		$data['login_form'] = "loginFormNorway";
		$data["main_content"] = 'login/sms_form';
		$data["template_header"] = 'includes/template_header_sms';
		$data["template_footer"] = 'includes/template_footer_sms';
		$this->load->view('includes/template', $data);
	}

	function smsRedirect(){

		if ($this->session->userdata('language_code')==2) {
			$redirectSms = site_url("login/smsFormEng");
		}elseif ($this->session->userdata('language_code')==3) {
			$redirectSms = site_url("login/smsFormNorway");
		}else{
			$redirectSms = site_url("login/smsForm");
		}

		// enabling sms feature
		if ($this->session->userdata('user_role_type')=='superadmin') {
			$vUser=$this->login_model->hasAdminPhoneNumber();
			if (!empty($vUser->contact_number) && $vUser->sms_login==1) {
					$this->session->unset_userdata('bip_logged_in');
					//generate the unique activation code
					$token = substr(str_shuffle(str_repeat("23456789ABCDEFGHJKMNPQRSTUVWXYZ", 5)), 0, 5);
					$this->login_model->insertSmsValidation($vUser,$token);

					if (ENVIRONMENT!='development')
					sendSMS($vUser->contact_number, $token);

					$this->login_model->bipLogAllLoginsToSystem(0,lang('txt_sms_code_sent_first_time'));
					redirect($redirectSms);
			}else{
					$this->login_model->bipLogAllLoginsToSystem(1);
				redirect('stage/admin');
			}
		}else{
			$vUser=$this->login_model->hasPhoneNumber();
			if (!empty($vUser->contact_number) || !empty($vUser->contact_number_1)) {
					$this->session->unset_userdata('bip_logged_in');
					//generate the unique activation code
					$token = substr(str_shuffle(str_repeat("23456789ABCDEFGHJKMNPQRSTUVWXYZ", 5)), 0, 5);
					$this->login_model->insertSmsValidation($vUser,$token);
						if (!empty($vUser->contact_number) && !empty($vUser->contact_number_1)) {
							$this->session->set_userdata('popUp' , 1);
						}
						elseif (!empty($vUser->contact_number) && empty($vUser->contact_number_1)){
							sendSMS($vUser->contact_number, $token);
						}
						else{
							sendSMS($vUser->contact_number_1, $token);
						}

					$this->session->set_userdata('showPopup',1);
							$this->login_model->bipLogAllLoginsToSystem(0,lang('txt_sms_code_sent_first_time'));
					redirect($redirectSms);
			}else{
				$this->login_model->bipLogAllLoginsToSystem(1);
				redirect('stage');
			}
		}

	}

	/**
	 * verify app activation and refresh notification for app
	 * @return void
	 */
	function perfomPollingApp(){
		$this->load->model('minapp/minapp_model');
		$this->minapp_model->checkAppActivated();
	}

	/**
	 * switch language only for superadmin login
	 */
	function switchLanguage()
	{
		$data["main_content"] = 'login/choose_language';
		$data["template_header"] = 'includes/template_header_sms';
		$data["template_footer"] = 'includes/template_footer_sms';
		$this->load->view('includes/template', $data);
	}

	/**
	 * set language chosen in session for superadmin
	 */
	function setLanguage()
	{
		$language = $this->input->post('language_code');
		$this->session->set_userdata( 'language_code' , $language );
		echo "success";
	}

	function switchToLanguage($language_code)
	{
		$this->session->unset_userdata('difficulty');
		$this->session->set_userdata( 'language_code' , $language_code );
		if ($this->agent->is_referral())
		{
			$referrer= $this->agent->referrer();
			$segments = explode('/',$referrer);
			if ($language_code==2 && in_array("minapp", $segments)) {
				redirect ('stage/admin');
			}else{
				redirect($referrer);
			}
		}else{
			redirect ('stage/admin');
		}
	}


	function validate_user() {

		if ($this->session->userdata('language_code')==2) {
			$this->lang->load('super', 'english');
		}elseif ($this->session->userdata('language_code')==3) {
			$this->lang->load('super', 'norwegian');
		}else{
			$this->lang->load('super', 'swedish');
		}

		$this->load->library('form_validation');

		$post_param_user = 'bip_un_'.$this->input->post('number');
		$post_param_pass = 'bip_pw_'.$this->input->post('number');

		$_POST['username'] = $this->input->post($post_param_user);
		$_POST['password'] = $this->input->post($post_param_pass);


		$this->form_validation->set_rules($post_param_user, lang('username'), 'trim|required|xss_clean');
		$this->form_validation->set_rules($post_param_pass, lang('password'), 'trim|required|xss_clean');

		if (isset($_POST['g-recaptcha-response']) && $this->config->item('g_captcha_set')) {
			$this->form_validation->set_rules('g-recaptcha-response', 'reCAPTCHA', 'callback_captcha_check');
		}

		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_userdata( 'login_attempt' , 1 );
			$this->index();
		}
		else
		{
			// die('there');
			$data["main_content"] = $this->session->userdata('login_form');
			$data["template_header"] = 'includes/template_header_sms';
			$data["template_footer"] = 'includes/template_footer_sms';
			$this->load->view('includes/template', $data);

				$this->session->set_userdata('sms_attempt', 0);
			$language = $this->input->post('selectLanguage');

			if (!$language)
				$language = "swedish";

			$email = $this->input->post('username');
			$pass = $this->input->post('password');

			if ($email && $pass) {

				$query = $this->login_model->validate_user();

				if ($query) { // if the user's credentials validated...

					if ($this->session->userdata('user_role_type')!='superadmin') {

						$this->login_model->bipUserTracking();

						$this->perfomPollingApp();

						if ($this->session->userdata('user_role')==1 || ($this->session->userdata('user_role')==2 && $this->session->userdata('email_notify')==1)) {
							$this->load->model('messages/messages_model');
							$total_message = $this->messages_model->getTotalNewMessage();
							$this->session->set_flashdata('total_message_temp', $total_message);
						}

					   if ($this->session->userdata('logintype') == "user") {
							$this->load->model('worksheet/worksheet_model');
							$totalNewSheet = $this->worksheet_model->getNewSheet();
							$this->session->set_flashdata('totalNewSheet', $totalNewSheet);
						}

					}

					//sms authentication check for all user type
					$this->smsRedirect();

				}
				else { // incorrect username or password
				  $email  = $this->input->post('username');
				  $pass  = $this->input->post('password');
					$query = $this->login_model->invalidate_user_status($email, $pass);
					if ($query == 1) {
						$this->session->set_flashdata('msg', lang('user_expired_contact_system'));
					} else {
						if ($this->login_model->validate_email_password($email, $pass)) {
							 if (!$this->login_model->validate_user_language($email, $pass)){
								 $this->session->set_flashdata('msg', lang('invalid_username_or_password'));
									}else{
										$this->session->set_flashdata('msg', lang('invalid_username_or_password'));
											}

									}else{
									$this->session->set_flashdata('msg', lang('invalid_username_or_password'));
									}

					}

					$isSmsOff = $this->login_model->checkSmsOff();
					if ($isSmsOff) {
						$this->session->set_userdata( 'login_attempt' , 1 );
					}

					redirect($this->agent->referrer());
				}
			} else {


				$this->session->set_userdata( 'login_attempt' , 1 );
				$this->session->set_flashdata('msg', 'ac'.lang('type_valid_user'));
				redirect($this->agent->referrer());
			}
		}

	}

	function captcha_check($captcha){

			if(!$captcha){
				$this->form_validation->set_message('captcha_check', 'Please check the the captcha form');
				return FALSE;
			}

			$secret_key = $this->config->item('g_secret_key');

			$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret_key."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);

			if($response.success==false)
			{
			   $this->form_validation->set_message('msg', 'You are spammer ! Get the @$%K out');
			   return false;
			}

			return true;
	}

	public function bip_logged_in() {

		$bip_logged_in = $this->session->userdata('bip_logged_in');
		//echo $this->session->userdata('bip_logged_in');

		if (!isset($bip_logged_in) || $bip_logged_in != true) {
			//if(!$this->session->userdata('bip_logged_in'))
			echo 'You don\'t have permission to access this page. <a href="../login">Login >></a>';
			//die();
			//$this->load->view('login_form');
		} else {
			echo 'You are logged in !!';
		}
	}

	function signup() {
		$data['main_content'] = 'signup_form';
		$this->load->view('includes/template', $data);
	}

	function create_member() {
		$this->load->library('form_validation');

		// field name, error message, validation rules
		$this->form_validation->set_rules('first_name', 'Name', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');


		if ($this->form_validation->run() == FALSE) {
			$this->load->view('signup_form');
		} else {
			$this->load->model('membership_model');

			if ($query = $this->membership_model->create_member()) {
				$data['main_content'] = 'signup_successful';
				$this->load->view('includes/template', $data);
			} else {
				$this->load->view('signup_form');
			}
		}
	}

	function logout() {

		$user_role_type = $this->session->userdata('user_role_type');

		$this->load->model('login_model');
		$this->trackDuration();

		if ($this->session->userdata('user_role_type')!='superadmin') {
			$this->login_model->bipUserTracking();
		}

		$this->session->unset_userdata('username');
		$this->session->unset_userdata('log_time_from');
		$this->session->unset_userdata('bip_logged_in');
		$this->session->unset_userdata('login_attempt');

		$this->session->sess_destroy();

		$login_from = $this->input->cookie('bip_login_from');

				redirect($login_from);
	}

	function cp() {
		if ($this->session->userdata('username')) {
			// load the model for this controller
			$this->load->model('membership_model');
			// Get User Details from Database
			$user = $this->membership_model->get_member_details();
			if (!$user) {
				// No user found
				return false;
			} else {
				// display our widget
				$this->load->view('user_widget', $user);
			}
		} else {
			// There is no session so we return nothing
			return false;
		}
	}

	function forgotPassword() {
		$data['main_content'] = 'forget_password';
		$this->load->view('includes/admin/template', $data);
		//$this->load->view("forget_password");
	}

	function forgotPasswordProcess() {
		$userName = $this->input->post("username");
		if ($this->login_model->user_exists($userName)) {
			$randCode = substr(md5($userName . rand(100, 999) . date("jmy"), 5, 15));
			$this->login_model->updateCode($randCode, $userName);
			$this->load->library('email');

			$message = "
				<html>
				<head>
				</head>
				<body>
				Dear $firstName $lastName,<br/>
				You  have requested for forget password for your account. Please follow the below link to change your  password.<br/><br/>

				</body>
				</html>";

			$this->email->from('noreply@barninternetprojektet.se', 'BIP Administrator');
			$this->email->to($userName);
			//$this->email->bcc('bijay.manandhar@websearchpro.net');

			$this->email->subject('Forget Password Retrive');
			$this->email->message($Message);

			$this->email->send();

			//echo $this->email->print_debugger();
		}
	}

	function trackDuration() {
		$now = date('Y-m-d h:i:s');
		$lastact = $this->session->userdata('log_time_from');
		$user_id = $this->session->userdata('user_id');
		// difference in seconds
		$difference = abs(strtotime($now." UTC") - strtotime($lastact." UTC"));
		if ($this->login_model->updateDuration($user_id, $difference)) {
			// echo "logout time: ".strtotime($now." UTC")."<br>login time: ".strtotime($lastact." UTC")."<br>difference: ".$difference;exit;
			$this->session->set_userdata(array('log_time_from' => $now));
		}
	}


	function changePassword()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('old_passsword', 'Old Password', 'trim|required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');


		if ($this->form_validation->run() == FALSE) {
			$this->load->view('change_password');
		} else {


		}
	}
	function SaveLoginReport(){
		$user_id = $this->session->userdata('user_id');
		$json = array("option"=>$_POST['option'],"message"=>replace_swedish_char(htmlspecialchars($_POST['message']))
		);
		$value=json_encode($json, true);
		$this->login_model->SaveLoginReport($user_id,$value);
	}

	function SendSMSChoosen(){
		$choosen_number = $_POST['option'];
		echo $choosen_number;
		$vUser=$this->login_model->hasPhoneNumber();
		$token = substr(str_shuffle(str_repeat("23456789ABCDEFGHJKMNPQRSTUVWXYZ", 5)), 0, 5);
		$this->login_model->insertSmsValidation($vUser,$token);
		sendSMS($choosen_number, $token);
	}

	function error_report(){

		$data["main_content"] = 'login/error_report';

		if ($this->session->userdata('language_code')==2) {
			$this->lang->load('super', 'english');
		}elseif ($this->session->userdata('language_code')==3) {
			$this->lang->load('super', 'norwegian');
		}else{
			$this->lang->load('super', 'swedish');
		}

		if ($this->session->userdata('user_role_type')=='superadmin') {
			$this->load->view('includes/admin/template', $data);
		}else{

			$this->load->view('includes/template', $data);
		}
	}

	function notify_all_errors()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('subject','lang:subject','trim|required|xss_clean|min_length[2]');
		$this->form_validation->set_rules('message','lang:message','trim|xss_clean');

		if ($this->form_validation->run() == FALSE) {
			$this->error_report();
		}else{
			$this->load->library('email');

			$mail_subject = $this->input->post('subject');
			$message = $this->input->post('message');
			$message = (!empty($message)) ? $message: 'An error occurred';
			$mail_message =
			'<html><head>
			<style>
				body {font:normal 14px/20px "Trebuchet MS", Arial, Helvetica, sans-serif; color:#333; }
			</style>
		</head>
		<body><p>' . $message . '</p>
			Med v&#228;nlig h&#228;lsning,<br/>
			BIP - Internetpsykiatri
		</body></html>';

		$config['mailtype'] = 'html';
		//$config['mailpath'] = '/usr/sbin/sendmail';
		//$config['charset'] = 'iso-8859-1';
		$config['wordwrap'] = TRUE;
		$config['charset'] = 'utf-8';

		$this->email->initialize($config);

		$this->email->from('noreply@barninternetprojektet.se', 'BIP Administrator');

		$accounts = $this->db->query("SELECT email FROM bip_admin_user WHERE status='1' AND error_notify='1'")->result();
		if (!empty($accounts)) {
			foreach ($accounts as $key => $account) {
				$to_mail = $this->encryption->decrypt($account->email);
				$this->email->to($to_mail);
			}
		}else{
			$this->email->to('dharmaraj@tulipstechnologies.com');
		}

		$this->email->subject($mail_subject);

		$filename = 'log-'.date('Y-m-d-H-i-s').'.php';
		$attachment = APPPATH . 'logs/'.'log-'.date('Y-m-d').'.php';
		$this->email->attach($attachment,'attachment', $filename);
		$this->email->message($mail_message);


		$this->email->send();

		$this->session->set_flashdata('msg', 'Error reported successfully');
		// echo $this->email->print_debugger();exit;
		redirect(site_url('login/error_report'));

	}
	}
}
