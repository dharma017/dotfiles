<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('user_agent');

        $bip_logged_in = $this->session->userdata('bip_logged_in');
				$this->usertype=$this->session->userdata('logintype');

				if ($this->usertype == 'user')
					$this->usertype = '';

				if(bip_logged_in($bip_logged_in,"admin"))
				{
					$models = array(
						'stage/stage_model',
						'setting/setting_model',
						'paging/paging_model',
						'user/user_model',
						'minapp/minapp_model'
						);

					$this->load->model($models);
					$language_code = $this->session->userdata('language_code');

	    		$file='super_lang.php';

        if (isset($language_code) && $language_code==1) {
        	if ($this->uri->segment(1)==='stage') {
            $this->lang->load('super', 'swedish');
        	}else{
        		$this->lang->load('super', 'english');
        	}
					$l='swedish';
					$this->session->set_userdata('bip_language_code','sv');

     	  }elseif(isset($language_code) && $language_code==3){
           	$this->lang->load('super', 'norwegian');
						$l='norwegian';
						$this->session->set_userdata('bip_language_code','no');
        }else{
		        $this->lang->load('super', 'english');
						$l='english';
						$this->session->set_userdata('bip_language_code','en');
        }

	      if($l!==FALSE && $file!==FALSE && is_dir(APPPATH."language/$l/") && file_exists(APPPATH."language/$l/$file")){
					require(APPPATH."language/$l/$file");
					$new_array = array();
					foreach ($lang as $key => $value) {
					    if (substr($key, 0, 3) == "js_") {
					    	$new_key = str_replace('js_', '', $key);
					        $new_array[$new_key] = $value;
					    }
					}

					$this->session->set_userdata('jsLang', $new_array );

			}

		}

	}
}

/* End of file admin_Controller.php */
/* Location: ./application/controllers/admin_Controller.php */
