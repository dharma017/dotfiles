<div class="patient-all-registration">
	<div class="header">
		<h1 class="fl"><?php echo lang("txt_registrations")?></h1>
		<div class="fr">
			<a href="<?=site_url()?>/minapp/viewRegistrationgraphs" class="new-btn view-registration-graph" style="width:120px"><?php echo lang("txt_see_graph")?></a>
		</div>
		<div class="clear"></div>
	</div>

	<div class="content">
	<?php
	// echo $reg_graphs;
	foreach($registrations as $regs):
		$getsteps = $this->minapp_model->getStepsInRegistrations($regs->registration_id);
		$bgcolor = "";
	?>
		<div class="box-reg">
			<div class="registration-header"><?php echo $regs->registration_name?></div>
			<div class="registration-content">
				<table class="gridtable grid1" cellpadding="0" cellspacing="0" border="0" width="100%">
					<thead>
						<th width="4%">
							<div class="table-header-big">Del</div>
							<div class="table-header-small">i BIP</div>
						</th>
						<?php
						$st = 1;
						$w = 96/count($getsteps);
						foreach($getsteps as $gs):
						?>
						<th width="<?php echo $w?>%">
							<div class="table-header-big"><?php echo lang("txt_step")." ".$st?></div>
							<div class="table-header-small"><?php echo $gs->step_name?></div>
						</th>
						<?php
						$st++;
						endforeach; //inner foreach end
						?>
					</thead>
					<tbody>

					  <?php
					  	$getassignments = $this->minapp_model->getRegAssignmentIDs($patient_id, $regs->registration_id);
					  	if(count($getassignments)>0){
					  		foreach($getassignments as $assignments):
						  		$bgcolor = $bgcolor=="td-white" ? "td-grey" : "td-white";
						  	?>
						    <tr class="<?php echo $bgcolor?>">
						    <td><?=$assignments->stage_id?></td>
						  	<?php
						  		foreach($getsteps as $gs){
						  			$getanswers = $this->minapp_model->getAssignmentAnswers($assignments->assignment_id,$gs->step_id);
						  			if($gs->template=="steps_datetime"){
						  	?>
						  		<td title="<?=$assignments->incident_time?>"><?php echo $assignments->incident_date." Kl ".date("H",strtotime($assignments->incident_time))?></td>
						  	<?php
						  			}else{
						  	?>
						  		<td><?php echo $getanswers->patients_answer?></td>
						  	<?php
						  			}
						  		}
						  	?>
						  	</tr>
						  	<?php
						  	endforeach;
						}else{
							if($this->session->userdata("user_role_type")=="patient"){
								$not_answered_message = lang("txt_patient_reg_not_answered");
							}else{
								$not_answered_message = lang("txt_psychologist_reg_not_answered");
							}
							echo "<tr><td align='center' colspan='".(count($getsteps)+1)."'>".$not_answered_message."</td>";
						}

					  ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php endforeach; ?>
	</div>

</div>
<script>

$(function(){


      $(".view-registration-graph").fancybox({
          ajax : {
              type  : "POST",
              data  : 'userID=<?=$this->input->post("userID")?>'
          },
          onComplete: function(){

          		var data = JSON.parse('<?=$reg_graphs?>');
          		console.warn('<?=$reg_graphs?>');
          		if(typeof data.labels!="undefined" && typeof data.series!="undefined"){
          			var no_of_labels= data.labels.length; //no. of weeks (no of labels in x axis)
          		var no_of_registrations = data.series.length; //no. of registration (represented by each colored bar)
          		var raw_width = no_of_labels * no_of_registrations * 10; //each bar is 10pixel wide
          		var num_width =  raw_width+(40*no_of_labels); //total width of the graph container, 40px added to each block, 20px left and 20px right
          		num_width = num_width<1120 ? 1120 : num_width;
          		var container_width = num_width+"px";

          		new Chartist.Bar('.ct-chart',data, {
							  seriesBarDistance: 10,
							  high: 10,
							  low: 0,
							  width: container_width,
							  height:"420px",
							  fullWidth: true,
							  axisX: {
							    showGrid: true,
							    scaleMinSpace: 50
							  },
							  axisY: {
							  	 // The offset of the labels to the chart area
							    offset: 40,
							    // Position where labels are placed. Can be set to `start` or `end` where `start` is equivalent to left or top on vertical axis and `end` is equivalent to right or bottom on horizontal axis.
							    position: 'start',
							    // Allows you to correct label positioning on this axis by positive or negative x and y offset.
							    labelOffset: {
							      x: 0,
							      y: 0
							    },
							    // If labels should be shown or not
							    showLabel: true,
							    // If the axis grid should be drawn or not
							    showGrid: true,
							    // Interpolation function that allows you to intercept the value from the axis label

							    // Set the axis type to be used to project values on this axis. If not defined, Chartist.AutoScaleAxis will be used for the Y-Axis, where the high and low options will be set to the global high and low options. This type can be changed to any axis constructor available (e.g. Chartist.FixedScaleAxis), where all axis options should be present here.
							    type: undefined,
							    // This value specifies the minimum height in pixel of the scale steps
							    scaleMinSpace: 50,
							    // Use only integer values (whole numbers) for the scale steps
							    onlyInteger: true
							  }
				},[]);

				var legend = $('#legend').find(".legend-list");
				var b = setTimeout(function(){
						$("svg .ct-series").each(function(index){



							$regcolor = $(this).attr("ct:series-name");
							$split = $regcolor.split("^");
							$reg = $split[0];
							$(this).find(".ct-bar").css("stroke","#"+$split[1]);
							$custom_class = $(this).attr("ct:meta");
							$class = $(this).attr("class");
							$class = $class.replace("ct-series","");
							$class = $class.replace(" ","");

							$check = "<input data-cls='"+$class+"' type='checkbox' checked='checked' class='check-legend' name='"+$custom_class+"' id='"+$custom_class+"' /><label for='"+$custom_class+"' class='label-legend'><span style='background-color:#"+$split[1]+"'></span>"+$reg+"</label>";
							$('#legend').find("ul.legend-list").append("<li class='"+$class+" clear'>"+$check+"</li>");
						});


				},200);



				$(".check-legend").live("click",function(){
					$cls = $(this).attr("data-cls");
					$obj = $("#chart-area").find("svg").find("g");

					if($(this).prop("checked")==true){
						$obj.find("."+$cls).show();
					}else{
						$obj.find("."+$cls).hide();
					}
				});

				/*

				var chart = new Chartist.Bar('.ct-chart', {
				  labels: ['2005', '2006', '2007', '2008'],
				  series: [{
				    name: 'My Test',
				    meta: 'category1',
				    data: [1000, 200, 500, 1000]
				  }, {
				    name: '13000',
				    meta: 'category2',
				    data: [1200, 400, 300, 2000]
				  }, {
				    name: '15000',
				     meta: 'category3',
				    data: [1500, 600, 100, 3000]
				  }]
				});

				var ab= setTimeout(function(){
				  console.info("Hello world");
				 $('.ct-series.ct-series-a[ct|meta="category1"]').hide();
				},3000);
				 */

          		}else{
          			$(".patient-all-registration").find(".content").html("<?=lang('no_task_avail')?>");
          		}
          }
    });


});
</script>
