    <div class="formentry">
      <form method="post" name="frmBip"  id="frmBip">
        <?php
		//$userLabelPost = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['label']")); 
			//$userDataPost =($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['data']")); 
			//echo "bijay data";
			$subheading_textPrev	=	$this->stage_model->getsubHeadingTextprev($stepId);
			$textPrev				=	$subheading_textPrev->textfield_prev;
			//echo '<pre>';
			//print_r($subheading_textPrev);
			
		echo '<h2 class="subheader">'.$subheading_textPrev->fld_name.'</h2>';
		
		$i = 0;
if($templateData)
{
	foreach($templateData as $key => $value)
	{
		$i++;
		if($textPrev == 1){
		echo '<p id="user_form"><label class="qsn" style="display:none;">  '.$this->stage_model->getValueOfFormById($key)->fld_label.'</label><textarea  name="fld_data[]" style="width:615px; padding:5px;" readonly="readonly">'.(stripslashes($value)).'</textarea><input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$key.'" />';
		echo '</p>';
		}
		else
		{
		echo '<p id="user_form"> '.$this->stage_model->getValueOfFormById($key)->fld_label.'<br /><input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$key.'" />';
		echo '</p>';
		}
		
	}
}
else
{
	echo 'Data Not Found';
}
?>       