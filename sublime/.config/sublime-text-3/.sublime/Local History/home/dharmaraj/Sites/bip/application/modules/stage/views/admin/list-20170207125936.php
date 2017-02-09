<script type="text/javascript">
$(document).ready(function() {

	$(".fancybox").fancybox({
		'width'				: '85%',
		'height'			: '108%',
		'autoScale'			: false,
		'transitionIn'		: 'elastic',
		'transitionOut'		: 'elastic',
		'type'				: 'iframe'
	});

	var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
              $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

		$('#sortable tbody').sortable({
			opacity: 0.6,
			cursor: 'move',
			scrollSensitivity: 40,
			axis: 'y',
			handle: ".handle",
            helper: fixHelperModified,
            update: function (event, ui) {
                var str = $(this).sortable('serialize');
                var stage_id="<?php echo $stageId; ?>";
                var difficultyId="<?php echo $difficultyId; ?>";
                $serializeData = str + '&stage_id='+stage_id;
                // POST to server using $.post or $.ajax
				$.post($sitePath + "/stage/admin/sortStageList", $serializeData, function(data) {
					listStage('','',difficultyId);
				});
            }
        }).disableSelection();

	$(".grid tbody tr").mouseover(
			function(){
				$('.this').css({"background-color":"#ddd","cursor":"pointer"});
			});

		$(".grid tbody tr").mouseout(
				function(){
				$(this).removeAttr("style");
			});

		$('.grid tbody tr:even').addClass('even');
		$('.grid tbody tr:odd').addClass('odd');
		$('.grid tbody tr:last-child').addClass('last');
		$('.grid tbody tr th:first-child, tr td:first-child').addClass('first');
		$('.grid tbody tr th:last-child, tr td:last-child').addClass('last');


});
</script>

<?php
$offset		= (isset($_POST['offset']))?$_POST['offset']:0;
$orderBy	= (isset($_POST['orderBy']))?$_POST['orderBy']:'asc';
$orderAlter	= ($orderBy=='asc')?'desc':'asc';
$datalimit	= DATALIMIT;
$result=$this->stage_model->getAllStagePsychologist($offset,$datalimit,$orderBy,$difficultyId);
?>
<?php
$allStage   =  $result[0];
$totalRows	=  $result[1];
$html='';
$jsfn=array('listStage','"'.$orderBy.'"');
$paging	=	 $this->paging_model->ajaxPaging($totalRows,$datalimit,$jsfn,$offset);


?>

<div id="box" class="box box-100">
  <!-- box full-width -->
  <div class="boxin">
    <div class="header">
      <h3>Stages</h3>
      <a class="savebtns" href="#Add Stage" style="margin-left:10px"  onclick="addstageForm();"><?php echo $this->lang->line("add_stage");?>&nbsp;</a>

      <?php
$difficulty = $this->stage_model->getAllDifficultyByLang();
$this->db->freeDBResource();
$sessDifficulty = $this->session->userdata('difficulty');
?>

<div id="difficulty" style="float:right;margin-bottom:5px;">
<strong> Choose Difficulty :</strong>
 <select id="difficultychoose" onchange="listStage('','',this.value)" class="drop">
 <option value="00" <?php if(!$sessDifficulty) echo 'selected="selected"';?>> All Difficulties</option>
   <?php foreach($difficulty as $data){?>
    <option value="<?php echo $data->id;?>" <?php if($sessDifficulty==$data->id) echo 'selected="selected"';?>><?php echo $data->difficulty; ?></option>
    <?php }?>

   </select>
  </div>
      </div>
    <div id="box1-tabular" class="content clear">
      <form class="plain" action="#" method="post" enctype="multipart/form-data">
        <?php   if($allStage){	  ?>
        <table width="1000px" cellpadding="0" cellspacing="0" border="0"  class="grid" id="sortable">
          <thead>
            <tr>
              <th width="30px"><?php echo $this->lang->line("sn");?></th>
              <th width="310px"><a title="sort stage" href="#Sort Stage" onclick="listStage(<?php echo "'".$orderAlter."', 0"; ?>)"><?php echo $this->lang->line("title");?></a></th>
              <th width="120px"><?php echo $this->lang->line("steps");?></th>
              <th width="120px"><?php echo $this->lang->line("estimated_time");?></th>
			 <th width="80px">Avg. Time</th>
              <th width="150px"><?php echo $this->lang->line("target_group");?></th>
              <th width="50px"><?php echo $this->lang->line("published");?></th>
              <th width="140px"><?php echo $this->lang->line("setting_stage");?></th>
            </tr>
          </thead>
          <tbody>
            <?php
$count=1;
$arr_target_group = $this->config->item('arr_target_group');
$permission = $this->stage_model->getPermissionOfPsy();
$permission = json_decode($permission,true);
foreach($allStage as $rows)
{
	if($rows->published=="1")
	$pub_icon = '<img src="'.base_url().'images/admin_icons/right.png" alt="'.$this->lang->line("edit").'">';
	else
	$pub_icon = '<img src="'.base_url().'images/admin_icons/wrong.png" alt="'.$this->lang->line("edit").'">';

	$offset++;
	$numStep = $this->stage_model->getTotalStepByStageId($rows->stage_id);
	$html.='<tr id="ID_'.$rows->stage_id.'">';
	$html.='<td onclick="showSteps('.$rows->stage_id.')">'.$offset.'</td>';
	if ($difficultyId>0) {
		$html.='<td  class="handle" onclick="showSteps('.$rows->stage_id.')">'.$rows->stage_title.'</a>';
	}else{
		$html.='<td onclick="showSteps('.$rows->stage_id.')">'.$rows->stage_title.'</a>';
	}
	$html.='<td>';

	if($numStep>0)
		$html .='<a href="#showSteps|'.$rows->stage_id.'" onclick="showSteps('.$rows->stage_id.')">Show Step ('.$numStep.')</a></td>';
	else
		$html .='<a href="#Add Steps" onclick="showSteps('.$rows->stage_id.')">Add Step</a></td>';


	$html.='<td  onclick="showSteps('.$rows->stage_id.')">'.$rows->estimated_time.'&nbsp;Min</td>';
	$avgstagetime = $this->stage_model->averageStageTime($rows->stage_id);
	$avgstagetimedata='';
	foreach ($avgstagetime->result() as $rowavg)
			$avgstagetimedata=$rowavg->average_stage_time;
			$avgstagetimedata =(round($avgstagetimedata) > 0) ? round($avgstagetimedata,2).' Min' : '-';

	 $html.='<td  onclick="showSteps('.$rows->stage_id.')">&nbsp;'.$avgstagetimedata.'&nbsp;</td>';
	$html.='<td  onclick="showSteps('.$rows->stage_id.')">'.$this->lang->line($arr_target_group[$rows->target_group]).'&nbsp;</td>';
	if ($rows->published=="1") {
		$html.='<td  onclick="toogleStageStatus('.$rows->stage_id.',0)" title="Deactivate Step">'.$pub_icon.'</td>';
	}
	else{
		$html.='<td  onclick="toogleStageStatus('.$rows->stage_id.',1)" title="Activate Step">'.$pub_icon.'</td>';
	}
	//$html.='<td  onclick="showSteps('.$rows->stage_id.')">'.$pub_icon.'</td>';
	$html.='<td>';

	//check if permitted to edit
	foreach ($permission['rights_per_difficulty'] as $key => $value) {
		if($value['edit_difficulty']==1){
		$difficulties[]=$key;
	}
	}
	$html .= '<span style="width:44px;display:inline-block">';
	if(empty($permission) || (is_array($permission['rights_per_difficulty']) && in_array($rows->difficulty_id,$difficulties)) )
		$html.='<a href="#Edit Stage" class="linkStyle" onclick="editStage('.$rows->stage_id.');" title="Edit Stage"><img src="'.base_url().'images/admin_icons/edit.png" alt="'.$this->lang->line("edit").'"></a>  &nbsp;
	<a href="#Delete Stage" class="linkStyle" onclick="deleteStage('.$rows->stage_id.');" title="Delete Stage"><img src="'.base_url().'images/admin_icons/delete.png" alt="'.$this->lang->line("delete").'"></a>';

	$html .= '</span>';
	$html.=' &nbsp;<a href="#Copy Stage" class="linkStyle" onclick="copyStage('.$rows->stage_id.');" title="Copy Stage"><img src="'.base_url().'images/admin_icons/copy.png" alt="'.$this->lang->line("copyStage").'"></a>';
	 if($numStep>0)
	$html.=' &nbsp;<a class="fancybox" id="fancybox_'.$rows->stage_id.'" href="'.site_url('stage/admin/previewTemplate/0/'.$rows->stage_id.'').'" class="linkStyle" onclick="" title="Preview Stage"><img src="'.base_url().'images/admin_icons/preview.png" alt="'.$this->lang->line("preview").'"></a>';
	$html.='</td></tr>';

}
echo $html;
?>
          </tbody>
        </table>
      </form>
    </div>

    <!-- .content#box-1-holder -->
    <?php
echo $paging;
?>
    <?php }
      else
{
	echo '<h2 align="center" style=" margin-top:30px;">'.$this->lang->line("stage_doesnot_exists").' </h3>';
}
    ?>
  </div>
</div>
