<?php

class Cron extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('user/user_model');
		$this->load->model('messages/messages_model');
		$this->load->library('email');
	}

	function getEnvironment(){
		echo ENVIRONMENT;exit;
	}

	function index() {
		if (ENVIRONMENT=='development')
			die("cron disabled for development enviroment");
		echo "<p> Getting psychologist list for email </p>";
		$psychologist_list = $this->user_model->get_psycholoist_to_send_email();

		echo '<pre>';
		print_r($psychologist_list);
		echo '</pre>';
		$message = '';
		foreach ($psychologist_list as $rows_psycho):
			$psycho_id = $rows_psycho->id;
			$email = $rows_psycho->email;
			$psycho_name = $rows_psycho->full_name;
			$total_worksheet = $this->user_model->get_total_worksheet_by_psycho_id($psycho_id);
			$total_message = $this->user_model->get_total_message_by_psycho_id($psycho_id);
			$total_comment = $this->user_model->get_total_comments_by_psycho_id($psycho_id);

			echo
			'TOTAL MESSAGE : '.$total_message.'
			TOTAL COMMENT :'.$total_comment.'
			TOTAL WORKSHEET :'.$total_worksheet;


			$message = ($total_comment > 0) ? '<p>Användare har kommenterat ett eller flera arbetsblad i BIP</p>' : '';
			$message .= ($total_worksheet > 0) ? '<p>Användare har lagt till ett eller flera nya arbetsblad i BIP</p>' : '';
			$message .= ($total_message > 0) ? '<p>Du har ett eller flera nya meddelanden i BIP</p>' : '';

			if ($message):
				$mail_subject = "Notification from BIP";
				$today = format_date(date('Y-m-j'));
				$mail_message =
						'<html><head>
								<style>
									body {font:normal 14px/20px "Trebuchet MS", Arial, Helvetica, sans-serif; color:#333; }
								</style>
							</head>
							<body><p>' . $message . '</p>
							Med v&#228;nlig h&#228;lsning,<br/>
							BIP - Barninternetprojektet
							</body></html>';

				$config['mailtype'] = 'html';
				$config['wordwrap'] = TRUE;
				$config['charset'] = 'utf-8';

				$this->email->initialize($config);

				$this->email->from('noreply@barninternetprojektet.se', 'BIP - Barninternetprojektet');

				if (ENVIRONMENT=='production') {
					$this->email->to($email);
				}else{
					$this->email->to('rabintulips@gmail.com');
				}

				$this->email->subject($mail_subject);
				$this->email->message($mail_message);


				if (!$this->email->send()) {
					$response = $this->email->print_debugger();
					// $this->logger->logAction('cron/index', (array)$response);
				}

			endif;


		endforeach;
	}


	function email_log() {
		$this->email->clear();
		$config['mailtype'] = 'html';
		$config['wordwrap'] = TRUE;
		$config['charset'] = 'utf-8';

		$this->email->initialize($config);

		$this->email->from('noreply@barninternetspsykiatri.se', 'BIP - Barninternetpsykiatri');

		if (ENVIRONMENT=='production') {
			$this->email->to($email);
		}else{
			$this->email->to('rabintulips@gmail.com');
		}
		$this->email->subject($mail_subject);
		$this->email->message($mail_message);

		$file = '/var/www/barninternetprojektet.se/application/logs/log-' . date('Y-m-d') . '.php';
		echo $file;
		$this->email->attach($file);

		if (!$this->email->send()) {
			$response = $this->email->print_debugger();
			// $this->logger->logAction('cron/email_log', (array)$response);
		}
	}

	function cronSMS()
	{
		if (ENVIRONMENT=='development')
			die("cron disabled for development enviroment");
		echo '<pre>';
		//mail and sms for pending Messages only
		$users = $this->user_model->get_message_pending();
		echo '/*************************************************/';
		echo '<br/>';
		echo 'Users Getting Pending Message Notification';
		echo '<br/>';
		echo '/*************************************************/';
		echo '<br/>';
		// print_r($users);

		$psychologistEmail = 'noreply@barninternetprojektet.se';

		foreach($users as $user)
		{
			echo '<br/>';

	  if ($user->sms_notify || $user->email_notify) {

		  $patient_inbox = 0;

				if($user->email_notify == 1)
				{
					echo 'Email To:'.$user->email;
					echo '<br/>';
					$tbl_message = nl2br(stripslashes(html_entity_decode($user->message)));
					if(!empty($user->email)){
						if(filter_var( $user->email, FILTER_VALIDATE_EMAIL )){
							$this->messages_model->sendMailByPsychologist($psychologistEmail, $user->email, $user->subject, $tbl_message);

						}
					}
				}

				if($user->sms_notify == 1)
				{
					echo 'SMS To:'.$user->contact_number;
					echo '<br/>';
		  $tbl_message = htmlspecialchars(addslashes($user->message));
					sendSMS($user->contact_number, $tbl_message);
			  // $message_id = $this->messages_model->sendMessageWithSMS($receiverId, $subject, $tbl_message, $sms_notify, $email_notify, $notify_now, $notify_date,$patient_inbox);
				}

	  }else{
		$message_id = $user->message_id;
		$patient_inbox = 1;
				$this->db->query("UPDATE bip_message SET patient_inbox='$patient_inbox' WHERE id='$message_id'");
	  }

		}

		/*echo '<br/>';
		echo 'section for the worksheet changes';
		echo '<br/>';
		echo '<br/>';*/

		//mail and SMS for change in the worksheet and new message
		/*$users = $this->user_model->getUserforCronSMS();
		print_r($users);
		foreach($users as $user)
		{
			$mail_message = $this->messages_model->getMailTemplateMessage($user->lang_id);
			$mail_message  = nl2br(stripslashes(html_entity_decode($mail_message)));
			echo '<br/>';
			echo 'new user:'.$user->contact_number.'::'.$user->email;
			echo '<br/>';
			if(!empty($user->email))
			if(filter_var($user->email, FILTER_VALIDATE_EMAIL ))
				$this->messages_model->sendMailByPsychologist($psychologistEmail, $user->email, 'Notification from BIP', $mail_message);
			if(!empty($user->contact_number)){
				$auto_contents= $this->messages_model->getSMSTemplateMessage($user->lang_id);
				sendSMS($user->contact_number,$auto_contents);
			}
		}
		echo '</pre>';*/

	}
}
