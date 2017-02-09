<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (ENVIRONMENT=='development')
			 $this->output->enable_profiler(FALSE);
		}

}

/* End of file mY_Controller.php */
/* Location: ./application/controllers/mY_Controller.php */
