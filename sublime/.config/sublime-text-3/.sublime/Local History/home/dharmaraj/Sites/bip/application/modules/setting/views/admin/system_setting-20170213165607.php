<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/shared/css/jquery-ui.css"/>
<script type="text/javascript"  src="<?php echo base_url(); ?>assets/shared/js/jquery-ui.min.js"></script>
<style type="text/css">
	.timer-ul input{
		width: 56px;
	}
	.timer-ul a img{
		padding: 4px;
	}
</style>
<?php

$systemSettingsRow=$this->statistics_model->get_system_settings();
$bassStatus = ($systemSettingsRow->bass_completion) ? 'checked="checked"':'';

?>
<form id="frmAddSystemSettings" method="post" name="frmAddSystemSettings">
<fieldset>
<ul class="adm-form timer-ul">
	<li>
		<label class="label" for="bass_completion"><strong>Turn off Bass Connection :</strong></label>
		<input type="hidden" name="bass_completion" value="0">
		<input type="checkbox" class="inputs" id="bass_completion" name="bass_completion" value="1" <?php echo $bassStatus ?> />
	</li>
	<li>
		<label class="label" for="timer"><strong>Timer (in minutes) :</strong></label>
		<input type="text"  class="inputs" id="timer" name="timer" value="<?=$systemSettingsRow->timer?>"/>
	</li>
</ul>

</fieldset>

<?php echo '<li><label></label><br/><input type="button" value="save" onClick="saveSystemSettings();" class="button" /></li></ul>';
 ?>

</form>
<script type="text/javascript">

$(document).ready(function() {
    // time input field masking
    $("#timer").setMask("999");
});


</script>
