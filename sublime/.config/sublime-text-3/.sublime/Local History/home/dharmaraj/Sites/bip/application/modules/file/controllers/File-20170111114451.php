<?php
class File extends Public_Controller 
{
	public $bipPageTitle = "Ladda ner";

   	function __construct()
   	{
		parent::__construct();
		$this->load->model('file/file_model');
	}   
	
   	function index()
   	{	
		$usertype = getUserType();
		
		if($usertype=='Psychologist')	
			$difficultyId 	 = $this->session->userdata("p_difficulty_id");
		else
			$difficultyId 	 = $this->session->userdata("difficulty_id");

		$offset=($this->input->post('offset')!='')?$this->input->post('offset') : 0;
		$datalimit=DATALIMIT;
		$orderBy='asc';
	
		$result = $this->file_model->listfile($difficultyId,$offset,$datalimit,$orderBy);
		$data["file_data"]		=	$result[0];

		$totalRows				=	$result[1];
		$data['sn']				=	$offset+1;
		$jsfn					=	array('filedatapaging','"'.$orderBy.'"');
		$paging					=	$this->paging_model->ajaxPaging($totalRows,$datalimit,$jsfn,$offset);
		$data['paging']			=  $paging;

		// echo "<pre>";print_r($this->session->userdata);exit;		
		if ($this->session->userdata("p_id")) {
			$patientId = $this->session->userdata("p_id");
		}else{
			$patientId = $this->session->userdata("user_id");
		}
		$userDetail = $this->user_model->getUserByUserId($patientId);
		$data["locked_files"] = explode(",", $userDetail->locked_files);

		$data["main_content"]	= 'file/list';
		$data["template_header"]= 'includes/template_header';
		$data["template_footer"]= 'includes/template_footer';
	
		$this->load->view('includes/template',$data);  
 	}
	
	function listfile()
	{
		$offset=($this->input->post('offset')!='')?$this->input->post('offset') : 0;
		$datalimit=DATALIMIT;
		$orderBy='asc';
		$usertype = getUserType();
		
		if($usertype=='Psychologist')	
			$difficultyId 	 = $this->session->userdata("p_difficulty_id");
		else
			$difficultyId 	 = $this->session->userdata("difficulty_id");
		$result = $this->file_model->listfile($difficultyId,$offset,$datalimit,$orderBy);
	
		$data["file_data"]=$result[0];
		$totalRows=$result[1];
		$data['sn']=$offset+1;
		$jsfn=array('filedatapaging','"'.$orderBy.'"');
		$paging	=	 $this->paging_model->ajaxPaging($totalRows,$datalimit,$jsfn,$offset);
		$data['paging']=$paging;
		$data['offset']=$offset;
	
	$this->load->view('file/ajaxlist',$data);  
	}

	function lockUnlock($todo) {
        $fileId = $this->input->post("fileId");
        $this->file_model->lockUnlock($fileId, $todo);
        // $this->listfile();
        echo "success";
    }
	
	
	
}
