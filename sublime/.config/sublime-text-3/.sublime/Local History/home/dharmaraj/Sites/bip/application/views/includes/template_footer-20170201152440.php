</div>
<div id="footer" class="clear row">
	<div class="footerlogo">
		<img src="<?php echo base_url();?>images/footer_logo.png" />
	</div>

	<div id="cookiesLink">
		<?php
		$usertype = $this->session->userdata('logintype');
		$sess_permission = $this->session->userdata('permission');
		if($usertype == 'user'):
			?>
		<a href="<?php echo site_url('stage/cookies/'); ?>"><?= lang('om_cookies') ?> &raquo;</a>
	<?php endif;

	if($usertype == 'admin' && !empty($sess_permission['rights_per_group'])):
		?>
	<a id="psytoadmin" href="<?php echo site_url('stage/admin/'); ?>"><?=lang("txt_admin_panel")?> &raquo;</a>
<?php endif;
$usertype = '';
?>
</div>
<?php
		// echo "next".$next_step;
if($previous_step)
{
	echo '<a href="'.site_url("stage/viewStep/$stageId/$previous_step").'"  id="previous" class="col1">'.lang('back').'</a>';
}

if($firstStep=="1")
	echo '<a href="'.site_url("stage").'"  id="previous" class="col1">'.lang('back').'</a>';

if($firstStep=="1" && $next_step)
		{ // first step
			echo '<a href="'.site_url("stage/viewStep/$stageId/$next_step").'"  id="start" class="col3">'.lang('starta').'</a>';
		}

		else if($firstStep=="1" && !$next_step) { // first step with single step
			echo '<a href="#"  id="start" class="col3">'.lang('starta').'</a>';

		} else if(!$next_step && $firstStep=="0" ) { // last step
			echo ' <a href="#"  id="last" class="col3" onclick="completeStage('.$stageId.')">'.lang('last').'</a>';

		} else if($next_step && $firstStep=="0") { // middle steps
			echo '<a href="'.site_url("stage/viewStep/$stageId/$next_step").'"  id="next"  class="col3">'.lang('next').'</a>';
		} ?>
	</div>
</div>
</div>



</div>
<div class="bottomStructure" id="bottom" style="background:url(<?php echo base_url()?>assets/public/css/images/grey/Bottom.png) no-repeat left top;"></div>
</div>
<div>
</div>
