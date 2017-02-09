<?php
$usertype = getUserType();
	if(count($allStage[0]) > 0){ ?>
   <table cellpadding="0" cellspacing="0" width="<?php if($usertype=='Psychologist') echo '782'; else echo '752';?>px" class="gridtable">
      <thead>
        <tr>
            <th width="268px"><?= lang('from') ?></th>
            <th width="65px" align="center"><?= lang('no_of_steps') ?></th>
            <th width="120px"><?= lang('for_who') ?></th>
            <th width="70px"><?= lang('performed') ?></th>
            <?php if($usertype=='Psychologist') { ?>
                <th width="30px">&nbsp;</th>
				<th width="30px">&nbsp;</th>
            <?php } else {?>
				<th width="50px">&nbsp;</th>
			<?php } ?>
            <th width="20px">&nbsp;</th>
            <th width="20px">&nbsp;</th>
     </tr>
      </thead>
      <tbody>
	<?php
	$count=1;
	$arr_target_group = $this->config->item('arr_target_group');
	$allStage=$allStage[0];
	foreach($allStage as $rows)
	{

		$numStep = $this->stage_model->getTotalStepByStageId($rows->stage_id,"active");

		// $steps_visited = $this->stage_model->getVistedSteps($rows->stage_id);
		// to indicate if the stage is locked or not
		if(is_array($locked_stage))
		{
			if(in_array($rows->stage_id,$locked_stage))
		 $stageLocked = true;
			 else
		 $stageLocked = false;
		}
		// END of // to indicate if the stage is locked or noto

		//td background and condecuted text
		$userActivity = $this->stage_model->checkActivity($rows->stage_id);
		if(($userActivity->end_date) && ($userActivity->end_date!="0000-00-00 00:00:00"))
		{
			$conducted = date("Y-m-d",strtotime($userActivity->end_date));
			if($count==1)
				$visited_class= 'class="visited firstRowGridTable"';
			else
				$visited_class= 'class="visited"';
		}
		else
		{
			$conducted = lang('not_done');
			if($count==1)
				$visited_class= 'class="new firstRowGridTable"';
			else
				$visited_class= 'class="new"';
		}
		//END OF // td background and condecuted text
		// to disable link incase of stage locked
		if($stageLocked)
		{
			$visited_class= 'class="locked"';
			if($usertype!='Psychologist')
				$link = "";
			else
			 	$link = site_url("stage/startStep/$rows->stage_id");// enableing locked stage for phsycho
		}
		else
		{
			$link = site_url("stage/startStep/$rows->stage_id");
		}
		$print_url	=	site_url("stage/pdfVersion/$rows->stage_id");
		$stepOverviewUrl	=	site_url("stage/stepOverviewUrl/$rows->stage_id");
		// end of // to disable link incase of stage locked
		$offset++;

		$html.='<tr id="'.$offset.'">';
		$html.='<td id="'.$offset.'"    '.$visited_class.' onclick="open_link(\''.$link.'\',\''.$rows->stage_id.'\')">'.$rows->stage_title.'</td>';
		$html.='<td align="center" id="'.$offset.'"     '.$visited_class.' onclick="open_link(\''.$link.'\',\''.$rows->stage_id.'\')">'.$numStep.'</td>';
		$html.='<td id="'.$offset.'"     '.$visited_class.' onclick="open_link(\''.$link.'\',\''.$rows->stage_id.'\')">'.$this->lang->line($arr_target_group[$rows->target_group]).'&nbsp;</td>';
			$html .='<td id="'.$offset.'"     '.$visited_class.' onclick="open_link(\''.$link.'\',\''.$rows->stage_id.'\')">'.$conducted.'</td>';
		if($stageLocked)
		$html.='<td id="'.$offset.'"     '.$visited_class.' onclick="open_link(\''.$link.'\',\''.$rows->stage_id.'\')"><strong>'.lang('locked').'</strong></td>';
		else
		$html.='<td id="'.$offset.'"     '.$visited_class.'><a class="startlink" href="#open stage"  onclick="open_link(\''.$link.'\',\''.$rows->stage_id.'\')">'.lang('starta').'</a></td>';
		 if($usertype=='Psychologist') {
		if($stageLocked)
			$html.='<td id="'.$offset.'" '.$visited_class.'><a href="#" onclick="lockStage('.$rows->stage_id.',0)"   class="padlocked" title="'.$offset.'"></a></td>';
		else
			$html.='<td id="'.$offset.'" '.$visited_class.'><a href="#" onclick="lockStage('.$rows->stage_id.',1)" id="padlock_'.$offset.'" style="display:none" class="padlock startlink" ></a></td>';
		 }
		if($stageLocked && $usertype!='Psychologist'){
			$html.='<td id="'.$offset.'" '.$visited_class.' onclick="return false;"><img src="'.base_url().'images/icon_print_BIP.png" width="18"></td>';
			$html.='<td id="'.$offset.'" '.$visited_class.' onclick="return false;">'.lang('overview').'</td>';
		}else{
			$html.='<td id="'.$offset.'" '.$visited_class.'><a target="_blank" href="'.$print_url.'" class="startlink"><img src="'.base_url().'images/icon_print_BIP.png" width="18"></a></td>';
			$html.='<td id="'.$offset.'" '.$visited_class.'><a class="startlink fancybox fancyboxOverview" href="'.$stepOverviewUrl.'">'.lang('overview').'</a></td>';
		}
		$html.='</tr>';
	}
	echo $html;
	?>
      </tbody>
</table>
<!-- .content#box-1-holder -->
<?php echo $paging; ?>
<?php }
else {echo '<div class="clear"></div>Stegen finns inte!';}
?>
<script language="javascript">
$('.gridtable td').bind({
	click: function() {
		// do something on click
	},
	mouseenter: function() {
		id = $(this).attr('id');
		$("#padlock_" + id).show();
	},
	mouseleave: function() {
		id = $(this).attr('id')
		$("#padlock_" + id).hide();
	}
});

$(".fancyboxOverview").fancybox({
	'autoDimensions': false,
	'width'				: 1008,
	'height'			: 728,
	'autoScale'			: false,
	'transitionIn'		: 'elastic',
	'transitionOut'		: 'elastic'
});


function lockStage(stageId, toDo) {
	$.ajax({
		type: "post",
		url: $sitePath + "/stage/lockUnlock/" + toDo,
		data: {
			"stageId": stageId
		},
		success: function(response) {
			$('#box1-tabular').html(response);
		}
	})
}

function open_link(linktoopen, stageId) {
	<?php if ($this->session->userdata('sess_stage_id')): ?>
	var curStageId = '<?php echo $this->session->userdata('sess_stage_id'); ?>' ;
	if (curStageId == stageId) {
		location.href = linktoopen;
	} else {
		if (confirm("Du har inte avslutat <?php echo $this->session->userdata("
			sess_stage_title ");?>, är du säker på att du vill avsluta?\nDin information kommer att sparas.")) {
			$.ajax({
				url: $sitePath + "/stage/close_stage",
				type: "post",
				success: function(response) {
					if (linktoopen)
						location.href = linktoopen;
				}
			})
		}
	} <?php
	else : ?>
		if (linktoopen)
		location.href = linktoopen; <?php endif; ?>
}
</script>
