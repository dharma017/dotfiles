<?php
$result=$this->setting_model->getAlliconByLang();

$allicon   =  $result[0];
$totalRows	=  $result[1];
$html='';
if($allicon){	
?>
<div style="clear:both"></div>
<form class="plain" action="#" method="post" enctype="multipart/form-data">
<?php 
$count=1;
$arr_target_group = $this->config->item('arr_target_group');
foreach($allicon as $rows)
{
$html.='<div class="imggallert"><div class="imgDiv" ><img src="'.base_url().$this->config->item('icon_path').$rows->icon_image.'"> </div>';
$html.='<br/>'.$rows->icon_name;
$html.=' <a href="#Delete Icon" class="link_style" onclick="checkslideinIcone('.$rows->id.');"><br />
	
Delete</a></div>';
 	
	/*
	$html.='<li><img src="'.base_url().$this->config->item('icon_path').$rows->icon_image.'" > <span class="iconame">'.$rows->icon_name.'</span><a href="#deleteicon" class="link_style" onclick="deleteIcon('.$rows->id.');">Delete</a></li>';*/
}

echo $html;

?>

</form>
<?php

}
else
{
	echo '<div style=" margin-top:30px;"><span class="title" > '.$this->lang->line("icon_doesnot_exists").' </span></div>';
}
?>
<script type="text/javascript">
function checkslideinIcone(iconId)
{
	$path=$sitePath+"/setting/admin/checkslideinIcone"; 
	
	$.ajax({
		url:$path,
		type:'post',
		data:{'iconId':iconId},
		async: true,
		success : function(response) {
			if(response == 1){
				if(confirm($jsLang['do_you_want_to_delete_icon']))
					deleteIcon(iconId);}
			else
				{
				alert('Icon are linked with slides so You can not delete.');
				}
		}
				
	});
}

</script>
