<div id="footer" class="clear rowssz">
<div class="wrapperNew">
	<?php
			// echo "next".$next_step;
	if($previous_step)
	{
		echo '<a href="'.site_url("stage/admin/previewTemplate/$previous_step").'"  id="previous" class="col1">'.lang('back').'</a>';
	}

	if($firstStep=="1" && $next_step)
			{ // first step
				echo '<a href="'.site_url("stage/admin/previewTemplate/$next_step").'"  id="start" class="col3">'.lang('starta').'</a>';
			}
			
			else if($firstStep=="1" && !$next_step) { // first step with single step
				echo '<a href="#"  id="start" class="col3">'.lang('starta').'</a>';

			} else if(!$next_step && $firstStep=="0" ) { // last step
				echo ' <a href="#"  id="last" class="col3" >'.lang('last').'</a>';

			} else if($next_step && $firstStep=="0") { // middle steps
				echo '<a href="'.site_url("stage/admin/previewTemplate/$next_step").'"  id="next"  class="col3">'.lang('next').'</a>';
			} ?>
		</div>
		</div>
	</div>
</div>
</div>
<div class="bottomStructure" id="bottom" style="background:url(<?php echo base_url()?>assets/public/css/images/<?php echo $colour;?>/Bottom.png) no-repeat left top;"></div>
<div>
</div>
</body>
</html>