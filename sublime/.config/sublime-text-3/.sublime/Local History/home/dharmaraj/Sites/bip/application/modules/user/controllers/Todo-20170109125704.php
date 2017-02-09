<?php
ini_set('memory_limit', '1024M'); // or you could use 1G
class Todo extends CI_Controller {

	/**
	 	php index.php user todo encrypt_decrypt_data bip_admin_user
		php index.php user todo encrypt_decrypt_data _revision_bip_admin_user
		php index.php user todo encrypt_decrypt_data bip_user
		php index.php user todo encrypt_decrypt_data _revision_bip_user
		php index.php user todo encrypt_decrypt_data bip_app_comments
		php index.php user todo encrypt_decrypt_data bip_worksheet_comments
		php index.php user todo encrypt_decrypt_data bip_form_data
		php index.php user todo encrypt_decrypt_data bip_message
		php index.php user todo update_bcrypt_password
		php index.php user todo reset_bcrypt_password
	 */

	function __construct() {
		parent::__construct();
		$this->load->model('user/user_model');
		$this->load->model('messages/messages_model');
		$this->load->library('email');
	}

	function get_bip_info(){
		echo base64_encode(mcrypt_create_iv(32));exit;
		echo "<pre>";print_r($_SERVER);echo "<br>";
		echo "<br>===============================<br>";
		echo "<pre>";print_r($_SESSION);echo "<br>";
		exit;
	}

	function prepare_test_site(){

		if (!is_cli()) return;

		$this->db->query("UPDATE bip_admin_user set sms_login=0,contact_number=''");
		echo $this->db->last_query();echo "\n";

		$this->db->query("UPDATE bip_user set sms_notify=0,email_notify=0,email='',contact_number=''");
		echo $this->db->last_query();echo "\n\n";

		// fix internal links
		$this->db->query("UPDATE `bip_step` SET `description` = replace(description, 'https://barninternetprojektet.se', 'http://bip.dev')");
		echo $this->db->last_query();echo "\n\n";

		$this->db->query("UPDATE `bip_user_tracking` SET `exit_page` = replace(exit_page, 'https://barninternetprojektet.se', 'http://bip.dev')");
		echo $this->db->last_query();echo "\n\n";

		$this->db->query("UPDATE `mysql`.`proc` p SET definer = 'root@localhost' WHERE definer='bip_dbuser@localhost'");

		echo $this->db->last_query();echo "\n\n";

		$this->reset_bcrypt_password();
	}

	function prepare_live_site(){

		if (!is_cli()) return;

		// fix internal links
		$this->db->query("UPDATE `bip_step` SET `description` = replace(description, 'https://barninternetprojektet.se', 'https://bip.zapto.org')");
		echo $this->db->last_query();echo "\n\n";

		$this->db->query("UPDATE `bip_user_tracking` SET `exit_page` = replace(exit_page, 'https://barninternetprojektet.se', 'https://bip.zapto.org')");
		echo $this->db->last_query();echo "\n\n";

		$this->db->query("UPDATE `mysql`.`proc` p SET definer = 'bip_dbuser@localhost' WHERE definer='bip_dbuser@localhost'");

		echo $this->db->last_query();echo "\n\n";

		$this->update_bcrypt_password();
	}

	public function make_test_db_v3()
	{
		if (!is_cli()) return;

		$this->db->query("UPDATE bip_admin_user set sms_login=0,pass='U5Ga0Z1aaNlYHp0MjdEdXJ1aKVVVB1TP',contact_number=''");
		echo $this->db->last_query();echo "\n";

		$this->db->query("UPDATE bip_user set sms_notify=0,email_notify=0,email='',contact_number='',password='U5Ga0Z1aaNlYHp0MjdEdXJ1aKVVVB1TP'");
		echo $this->db->last_query();echo "\n\n";
	}

	/**
	 * reset password with bcrypt hashing
	 * @param  string $new_password [string]
		php index.php user todo reset_bcrypt_password
	 */
	public function reset_bcrypt_password(){

		$new_password = '123456';

		$pass = $this->bcrypt->hash_password($new_password);

		$this->db->query("UPDATE bip_admin_user SET pass='$pass'");
		echo $this->db->last_query();echo "\n";

		$this->db->query("UPDATE bip_user SET password='$pass'");
		echo $this->db->last_query();echo "\n\n";

	}

	/**
	 * reset password with bcrypt hashing
	 * @param  string $new_password [string]
		php index.php user todo update_bcrypt_password
	 */
	public function update_bcrypt_password(){

		$admin_users = $this->db->query("SELECT id,pass FROM bip_admin_user")->result();
		// dd($admin_users);

		foreach ($admin_users as $admin_row) {

			$decoded_admin_pass = $this->user_model->decode5t($admin_row->pass);
			$bcrypt_pass = $this->bcrypt->hash_password($decoded_admin_pass);

			echo "UPDATE bip_admin_user SET pass='$decoded_admin_pass'";echo "\n";

			$this->db->query("UPDATE bip_admin_user SET pass='$bcrypt_pass' WHERE id='$admin_row->id'");
			echo $this->db->last_query();echo "\n\n";
		}

		echo "\n\n";

		$users = $this->db->query("SELECT id,password FROM bip_user")->result();

		foreach ($users as $user_row) {

			$decoded_user_pass = $this->user_model->decode5t($user_row->password);
			$bcrypt_user_pass = $this->bcrypt->hash_password($decoded_user_pass);

			echo "UPDATE bip_user SET password='$decoded_user_pass'";echo "\n";

			$this->db->query("UPDATE bip_user SET password='$bcrypt_user_pass' WHERE id='$user_row->id'");
			echo $this->db->last_query();echo "\n\n";
		}

	}

	/**
	 * reset password as version 3
	 * @param  string $new_password [string]
		php index.php user todo reset_password_v3 123456
	 */
	public function reset_password_v3($new_password = "123456"){

		$pass = 'U5Ga0Z1aaNlYHp0MjdEdXJ1aKVVVB1TP';
		// $pass = $this->bcrypt->hash_password($new_password);
		$this->db->query("UPDATE bip_admin_user SET pass='$pass'");
		echo "<br>".$this->db->last_query();
		$this->db->query("UPDATE bip_user SET password='$pass'");
		echo "<br>".$this->db->last_query();
		die("<br>password changed to $new_password");
	}

	/**
	 * decrypt old password
	 */
	function decrypt_old_password()
	{
		$password = "=AFVxI0VsR2dSxmTYF2R0d1UGB3RURlQD1UMWBTZEpkV"; // insert (fredrik) F"b)anLen
		echo "old password is ".$this->user_model->decode5t($password);exit;
	}

	/**
	 * encryption 2-way user data
	 * @param  string $table
	 * @param  string $todo  optional
	 */
	function encrypt_decrypt_data($table,$todo='encrypt') {

		switch ($table) {

		case 'bip_admin_user':

			// encrypt admin table
			$admin_users = $this->db->query("SELECT id,first_name,last_name,contact_number,email FROM bip_admin_user")->result();

			foreach ($admin_users as $row) {

				if ($todo=='encrypt') {
					$first_name = $this->encryption->encrypt($row->first_name);
					$last_name = $this->encryption->encrypt($row->last_name);
					$email = $this->encryption->encrypt($row->email);
					$contact_number = $this->encryption->encrypt($row->contact_number);

				}else{
					$first_name = $this->encryption->decrypt($row->first_name);
					$last_name = $this->encryption->decrypt($row->last_name);
					$email = $this->encryption->decrypt($row->email);
					$contact_number = $this->encryption->decrypt($row->contact_number);
				}
				$sql = "UPDATE bip_admin_user SET first_name='$first_name',last_name='$last_name',email='$email',contact_number='$contact_number' where id='$row->id'";
				$this->db->query($sql);
				echo $this->db->last_query();echo "<br>";
			}

			break;

		case 'bip_user':

			// encrypt user table
			$users = $this->db->query("SELECT id,first_name,last_name,contact_number,email FROM bip_user")->result();

			foreach ($users as $row) {

				if ($todo=='encrypt') {
					$first_name = $this->encryption->encrypt($row->first_name);
					$last_name = $this->encryption->encrypt($row->last_name);
					$email = $this->encryption->encrypt($row->email);
					$contact_number = $this->encryption->encrypt($row->contact_number);
				}else{
					$first_name = $this->encryption->decrypt($row->first_name);
					$last_name = $this->encryption->decrypt($row->last_name);
					$email = $this->encryption->decrypt($row->email);
					$contact_number = $this->encryption->decrypt($row->contact_number);
				}

				$this->db->query("UPDATE bip_user SET first_name='$first_name',last_name='$last_name',email='$email',contact_number='$contact_number' where id='$row->id'");
				echo $this->db->last_query();echo "<br>";
			}

			break;


		case '_revision_bip_admin_user':

			// encrypt admin table
			$admin_users = $this->db->query("SELECT id,first_name,last_name,contact_number,email,visitor_full_name FROM _revision_bip_admin_user")->result();

			foreach ($admin_users as $row) {

				if ($todo=='encrypt') {
					$first_name = $this->encryption->encrypt($row->first_name);
					$last_name = $this->encryption->encrypt($row->last_name);
					$email = $this->encryption->encrypt($row->email);
					$contact_number = $this->encryption->encrypt($row->contact_number);
					$visitor_full_name = $this->encryption->encrypt($row->visitor_full_name);

				}else{
					$first_name = $this->encryption->decrypt($row->first_name);
					$last_name = $this->encryption->decrypt($row->last_name);
					$email = $this->encryption->decrypt($row->email);
					$contact_number = $this->encryption->decrypt($row->contact_number);
					$visitor_full_name = $this->encryption->decrypt($row->visitor_full_name);
				}
				$sql = "UPDATE _revision_bip_admin_user SET first_name='$first_name',last_name='$last_name',email='$email',contact_number='$contact_number',visitor_full_name='$visitor_full_name' where id='$row->id'";
				$this->db->query($sql);
				echo $this->db->last_query();echo "<br>";
			}

			break;

		case '_revision_bip_user':

			// encrypt user table
			$users = $this->db->query("SELECT id,first_name,last_name,contact_number,email,visitor_full_name FROM _revision_bip_user")->result();

			foreach ($users as $row) {

				if ($todo=='encrypt') {
					$first_name = $this->encryption->encrypt($row->first_name);
					$last_name = $this->encryption->encrypt($row->last_name);
					$email = $this->encryption->encrypt($row->email);
					$contact_number = $this->encryption->encrypt($row->contact_number);
					$visitor_full_name = $this->encryption->encrypt($row->visitor_full_name);
				}else{
					$first_name = $this->encryption->decrypt($row->first_name);
					$last_name = $this->encryption->decrypt($row->last_name);
					$email = $this->encryption->decrypt($row->email);
					$contact_number = $this->encryption->decrypt($row->contact_number);
					$visitor_full_name = $this->encryption->decrypt($row->visitor_full_name);
				}

				$this->db->query("UPDATE _revision_bip_user SET first_name='$first_name',last_name='$last_name',email='$email',contact_number='$contact_number',visitor_full_name='$visitor_full_name' where id='$row->id'");
				echo $this->db->last_query();echo "<br>";
			}

			break;

		case 'bip_form_data':


			$form_data_results = $this->db->query("SELECT id,message FROM bip_form_data")->result();

			foreach($form_data_results as $row)
			{
				if ($todo=='encrypt') {
					$message = $this->encryption->encrypt($row->message);
				}else{
					$message = $this->encryption->decrypt($row->message);
				}
				$this->db->query("UPDATE bip_form_data SET message=? WHERE id=?",array($message,$row->id));
				echo $this->db->last_query();echo "<br>";
			}

			break;

		case 'bip_message':

			$message_results = $this->db->query("SELECT id,msg_subject,message FROM bip_message")->result();
			foreach($message_results as $row)
			{
				if ($todo=='encrypt') {
					$message = $this->encryption->encrypt($row->message);
					$msg_subject = $this->encryption->encrypt($row->msg_subject);
				}else{
					$message = $this->encryption->decrypt($row->message);
					$msg_subject = $this->encryption->decrypt($row->msg_subject);
				}
				$this->db->query("UPDATE bip_message SET msg_subject=?,message=? WHERE id=?",array($msg_subject,$message,$row->id));
				echo $this->db->last_query();echo "<br>";
			}

			break;


		case 'bip_worksheet_comments':

			$message_results = $this->db->query("SELECT id,comments FROM bip_worksheet_comments")->result();
			foreach($message_results as $row)
			{
				if ($todo=='encrypt') {
					$comments = $this->encryption->encrypt($row->comments);
				}else{
					$comments = $this->encryption->decrypt($row->comments);
				}
				$this->db->query("UPDATE bip_worksheet_comments SET comments=? WHERE id=?",array($comments,$row->id));
				echo $this->db->last_query();echo "<br>";
			}

			break;

		case 'bip_app_comments':

			$message_results = $this->db->query("SELECT id,comments FROM bip_app_comments")->result();
			foreach($message_results as $row)
			{
				if ($todo=='encrypt') {
					$comments = $this->encryption->encrypt($row->comments);
				}else{
					$comments = $this->encryption->decrypt($row->comments);
				}
				$this->db->query("UPDATE bip_app_comments SET comments=? WHERE id=?",array($comments,$row->id));
				echo $this->db->last_query();echo "<br>";
			}

			break;

		default:
			echo "No table provided in url";
			break;
		}

	}
}
