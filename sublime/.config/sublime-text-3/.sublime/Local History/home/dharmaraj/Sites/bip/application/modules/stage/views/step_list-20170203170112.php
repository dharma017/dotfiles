<script type="text/javascript">
	$(document).ready(function() {
		$('.grid tbody tr:even').addClass('even');
		$('.grid tbody tr:odd').addClass('odd');
		$('.grid tbody tr:last-child').addClass('last');
		$('.grid tbody tr th:first-child, tr td:first-child').addClass('first');
		$('.grid tbody tr th:last-child, tr td:last-child').addClass('last');
		
		$(".grid tbody tr").mouseover(
			function(){
				$(this).css({"background-color":"#ddd","cursor":"pointer"});
			});
		
		$(".grid tbody tr").mouseout(
			function(){
				$(this).removeAttr("style");
			});
		
		
	});
</script>
<div class="middleStructure" id="middle" style="background:url(<?php echo base_url()?>assets/public/css/images/<?php echo $colour;?>/Middle.png) repeat-y">
	<div id="mainWrapper">
		<div  class="heading col1">
			<h2 class="subheading"> </h2>
			<h1 class="mainheading"> <?php echo $title;?> </h1>
		</div>
		<div  class="col3">
			<?php
			if($iconImage)
			{
				echo '<img src="'.base_url().'images/icons/'.$iconImage.'" alt="" />';
			}
			?>
		</div>
		<div id ="contentArea" class="pad10 row clear">
			<div id="rightschek"  style="float:right;">
				<?php
				$offset		= (isset($_POST['offset']))?$_POST['offset']:0;
				$orderBy	= (isset($_POST['orderBy']))?$_POST['orderBy']:'asc';
				$orderAlter	= ($orderBy=='asc')?'desc':'asc';
				$datalimit	= DATALIMIT;
				$stage_title = $this->stage_model->getStageNameByStageId($stageId);
				$result=$this->stage_model->getStepByStageId($stageId,$offset,$datalimit,$orderBy);
				$allStep    =  $result[0];
				//print_r($allStep);
				$totalRows	=  $result[1];
				$html='';
				$jsfn=array('listStage','"'.$orderBy.'"');
				$paging	=	 $this->paging_model->ajaxPaging($totalRows,$datalimit,$jsfn,$offset);
				?>
				<?php if($allStep){ ?>
				<div id="box1-tabular" class="content">
					<form class="plain" action="#" method="post" enctype="multipart/form-data">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
							<thead>
								<tr>
									<th width="30"><?php echo $this->lang->line("sn");?></th>
									<?php //echo $this->lang->line("customer");?>
									<th><a title="sort stage" href="#sortStage" onclick="listStep(<?php echo "'".$orderAlter."', 0"; ?>)"><?php echo $this->lang->line("title");?></a></th>
									<th width="30"><?php echo $this->lang->line("colour");?></th>
									
									
									
									
								</tr>
							</thead>
							<tbody>
								<?php
								$count=1;
								$arr_target_group = $this->config->item('arr_target_group');
								$this->load->library('image_lib');
								
								foreach($allStep as $rows)
								{
									if($rows->published=="1")
										
										$urlLink = site_url('stage/admin/previewTemplate/'.$rows->step_id.'/'.$rows->stage_id);
									$offset++;
									
									
									$html.='<tr>';
									$html.='<td>'.$offset.'</td>';
									$html.='<td>'.$rows->title.'</td>';
									$html.='<td><div style="width:20px;height:20px;background-color:#'.$rows->colour_code.'; border:1px solid #666;"></div></td>';
									
									$html.='<td>'.$rows->template_title.'</td>';
									$html.='<td> <a href="#Move Up" title="Move Up" onclick="sortStep(\''.$rows->stage_id.'\',\''.$rows->step_id.'\',\''.$rows->ordering.'\',\'up\')"><img src="'.base_url().'images/admin_icons/uparrow.png" alt="up"></a> <a href="#Move Down" title="Move down" onclick="sortStep(\''.$rows->stage_id.'\',\''.$rows->step_id.'\',\''.$rows->ordering.'\',\'down\')"><img src="'.base_url().'images/admin_icons/downarrow.png" alt="down"></a></td>';
									$html.='<td>'.$pub_icon.'</td>';
									$html.='</tr>';
									
								}
								
								echo $html;
								
								?>
							</tbody>
						</table>
					</form>
					<?php
					echo $paging;
					echo '<div style=" height:10px; width:100%;"></div>';
					echo ' </div>';
				}
				?>
				
				<!-- .content#box-1-holder -->
				<!--displaying list of templates -->
				
			</div>
		</div>
		<!--end of the main content -->
	</div>
</div>
</div>