<div id="tselbox">
	<?php if (empty($treatment_id)): ?>
		<label><strong><?=lang('txt_treatment')?></strong></label>
		<select name="difficulty" id="selDiff" onchange="fillTreatmentForm(this.value);">
			<?php if (!isset($treatment->difficulty_id)): ?>
				<option value=""><?=lang('sel_choose_treat')?></option>
			<?php endif ?>
			<?php
			$sess_permission = $this->stage_model->getPermissionOfPsy();
				$sess_permission = json_decode($sess_permission,true);
				$usertype = $this->session->userdata('user_role_type');
				$logintype = $this->session->userdata('logintype');
				if($usertype == "superadmin" && $logintype == "admin"){
					$difficulties = $this->stage_model->getAllDifficultyByLang();
				}else{
					foreach ($sess_permission['rights_per_difficulty'] as $key => $value) {
						if($value['edit_difficulty'] == 1){
							$diff[] = $key;
						}
					}
					if(!empty($diff) && is_array($diff))
						$diff_page = join(',',$diff);
					$diff_page = rtrim($diff_page,",");
					$difficulties = $this->user_model->getDifficultyNamebyIds($diff_page);
				}
			foreach ($difficulties as $dk => $difficulty):
				echo '<option value="'.$difficulty->id.'">'.$difficulty->difficulty.'</option>';
			endforeach
			?>
		</select>
	<?php else: ?>
		<input type="hidden" id="altDiff" name="difficulty" value="<?=$treatment->difficulty_id?>">
	<?php endif ?>

	<form name="frmSetTreatment" id="frmSetTreatment" method="post">
	<ul class="adm-form diffForm ratingflow1">
	<input type="hidden" name="frm_name" value="frmSetTreatment">
		<li>
			<label for="lbl1"><strong><?=lang('txt_single_rating')?></strong></label>
			<?php if (!empty($treatment->difficulty_id)): ?>
				<input type="radio" id="lbl1" name="rating" value="1" <?php echo ($treatment->rating==1) ? 'checked': '';?> >
			<?php else: ?>
				<input type="radio" id="lbl1" name="rating" value="1" checked="checked" >
			<?php endif ?>
		</li>
		<li>
			<label for="lbl2"><strong><?=lang('txt_double_rating')?></strong></label>
			<input type="radio" id="lbl2" name="rating" value="2" <?php echo ($treatment->rating==2) ? 'checked': '';?> >
		</li>
		<li>
			<input type="button" name="btnSave" id="btnSave" onclick="setTreatment(this.form);" value="<?php echo $this->lang->line("save");?>" class="button" />
			<input type="button" name="btnCancel" id="btnCancel" value="<?php echo $this->lang->line("cancel");?>" onclick="listAllTreatmentSettings();" class="button" />
		</li>
	</ul>
	</form>
</div>
	<div id="rating1" class="desc">
	<form name="frmSlide_1" id="frmSlide_1" method="post">
	<ul class="adm-form diffForm ratingflow2">
        <?php echo validation_errors('<li class="txt-left error_msg">','</li>');?>
		<input type="hidden" name="frm_name" value="frmSlide_1">
		<li><label for=""><h3><?=lang('txt_slide')?></h3></label></li>
		<li>
			<label><strong><?=lang('txt_anxiety')?></strong></label>
			<input name="anxiety" id="anxiety" type="text" value="<?=$treatment->anxiety?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_ten')?></strong></label>
			<input name="ten" id="ten" type="text" value="<?=$treatment->ten?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_zero')?></strong></label>
			<input name="zero" id="zero" type="text" value="<?=$treatment->zero?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_button')?></strong></label>
			<input name="txt_button" id="txt_button" type="text" value="<?=$treatment->txt_button?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<input type="button" name="btnSave" id="btnSave" onclick="setTreatment(this.form);" value="<?php echo $this->lang->line("save");?>" class="button" />
		</li>
	</ul>
	</form>
	</div>

	<div id="rating2" class="desc" style="display:none;">
	<form name="frmSlide1" id="frmSlide1" method="post">
	<ul class="adm-form diffForm ratingflow2">
		<input type="hidden" name="frm_name" value="frmSlide1">
		<li><label for=""><h3><?=lang('txt_slide1')?></h3></label></li>
		<li>
			<label><strong><?=lang('txt_headline')?></strong></label>
			<input name="slide1_headline" id="slide1_headline" type="text" value="<?=$treatment->slide1_headline?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_description')?></strong></label>
			<textarea name="slide1_text" id="slide1_text" cols="51" rows="4"><?=$treatment->slide1_text?></textarea>
		</li>
		<li>
			<label><strong><?=lang('txt_button')?></strong></label>
			<input name="slide1_button" id="slide1_button" type="text" value="<?=$treatment->slide1_button?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<input type="button" name="btnSave" id="btnSave" onclick="setTreatment(this.form);" value="<?php echo $this->lang->line("save");?>"  class="button" />
		</li>
	</ul>
	</form>

	<form name="frmSlide2" id="frmSlide2" method="post">
	<ul class="adm-form diffForm ratingflow2">
		<input type="hidden" name="frm_name" value="frmSlide2">
		<li><label for=""><h3><?=lang('txt_slide2')?></h3></label></li>
		<li>
			<label><strong><?=lang('txt_headline')?></strong></label>
			<input name="slide2_headline" id="slide2_headline" type="text" value="<?=$treatment->slide2_headline?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_ten')?></strong></label>
			<input name="slide2_ten" id="slide2_ten" type="text" value="<?=$treatment->slide2_ten?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_zero')?></strong></label>
			<input name="slide2_zero" id="slide2_zero" type="text" value="<?=$treatment->slide2_zero?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_button')?></strong></label>
			<input name="slide2_button" id="slide2_button" type="text" value="<?=$treatment->slide2_button?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<input type="button" name="btnSave" id="btnSave" onclick="setTreatment(this.form);" value="<?php echo $this->lang->line("save");?>"  class="button" />
		</li>
	</ul>
	</form>

	<form name="frmSlide3" id="frmSlide3" method="post">
	<ul class="adm-form diffForm ratingflow2">
		<input type="hidden" name="frm_name" value="frmSlide3">
		<li><label for=""><h3><?=lang('txt_slide3')?></h3></label></li>
		<li>
			<label><strong><?=lang('txt_headline')?></strong></label>
			<input name="slide3_headline" id="slide3_headline" type="text" value="<?=$treatment->slide3_headline?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_image')?>:</strong></label>
			<div id="fileUpload"></div>
			<div id="icon_div" style=" margin-left:116px; margin-top:10px; margin-bottom:15px;">
			<?php if (!empty($treatment->slide3_image)): ?>
				<img style="width:100px;hieght:100px;" src="<?=base_url().'images/uploads/app_images/'.$treatment->slide3_image?>" alt="">
			<?php endif ?>
			</div>
			<input id="slide3_image" type="hidden"  name="slide3_image" value="<?=$treatment->slide3_image?>">
			<div id="uploaded_file"></div>
		</li>
		<li>
			<label><strong><?=lang('txt_description')?></strong></label>
			<textarea name="slide3_text" id="slide3_text" cols="51" rows="4"><?=$treatment->slide3_text?></textarea>
		</li>
		<li>
			<label><strong><?=lang('txt_button')?></strong></label>
			<input name="slide3_button" id="slide3_button" type="text" value="<?=$treatment->slide3_button?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong>Timing</strong></label>
			<input type="hidden" name="slide3_timing" value="0">
			<input name="slide3_timing" id="slide3_timing" type="checkbox" <?php echo ($treatment->slide3_timing==1) ? 'checked="checked"': '';?> value="1"/>
		</li>
		<li>
			<input type="button" name="btnSave" id="btnSave" onclick="setTreatment(this.form);" value="<?php echo $this->lang->line("save");?>"  class="button" />
		</li>
	</ul>
	</form>
	<form name="frmSlide4" id="frmSlide4" method="post">
	<ul class="adm-form diffForm ratingflow2">
		<input type="hidden" name="frm_name" value="frmSlide4">
		<li><label for=""><h3><?=lang('txt_slide4')?></h3></label></li>
		<li>
			<label><strong><?=lang('txt_headline')?></strong></label>
			<input name="slide4_headline" id="slide4_headline" type="text" value="<?=$treatment->slide4_headline?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_ten')?></strong></label>
			<input name="slide4_ten" id="slide4_ten" type="text" value="<?=$treatment->slide4_ten?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_zero')?></strong></label>
			<input name="slide4_zero" id="slide4_zero" type="text" value="<?=$treatment->slide4_zero?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_button')?></strong></label>
			<input name="slide4_button" id="slide4_button" type="text" value="<?=$treatment->slide4_button?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<input type="button" name="btnSave" id="btnSave" onclick="setTreatment(this.form);" value="<?php echo $this->lang->line("save");?>"  class="button" />
		</li>
	</ul>
	</form>
	<form name="frmSlide5" id="frmSlide5" method="post">
	<ul class="adm-form diffForm ratingflow2">
		<input type="hidden" name="frm_name" value="frmSlide5">
		<li><label for=""><h3><?=lang('txt_slide5')?></h3></label></li>
		<li>
			<label><strong><?=lang('txt_headline')?></strong></label>
			<input name="slide5_headline" id="slide5_headline" type="text" value="<?=$treatment->slide5_headline?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_timeX')?></strong></label>
			<input name="slide5_time_x" id="slide5_time_x" type="text" value="<?=$treatment->slide5_time_x?>" maxlength="2" size="2" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_timeY')?></strong></label>
			<input name="slide5_time_y" id="slide5_time_y" type="text" value="<?=$treatment->slide5_time_y?>" maxlength="2" size="2" class="inputs"/>
		</li>
		<li>
			<label><strong><?=lang('txt_time_text1')?></strong></label>
			<textarea name="slide5_time_text1" id="slide5_time_text1" cols="51" rows="4"><?=$treatment->slide5_time_text1?></textarea>
		</li>
		<li>
			<label><strong><?=lang('txt_time_text2')?></strong></label>
			<textarea name="slide5_time_text2" id="slide5_time_text2" cols="51" rows="4"><?=$treatment->slide5_time_text2?></textarea>
		</li>
		<li>
			<label><strong><?=lang('txt_time_text3')?></strong></label>
			<textarea name="slide5_time_text3" id="slide5_time_text3" cols="51" rows="4"><?=$treatment->slide5_time_text3?></textarea>
		</li>
		<li>
			<label><strong><?=lang('txt_button')?></strong></label>
			<input name="slide5_button" id="slide5_button" type="text" value="<?=$treatment->slide5_button?>" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<input type="button" name="btnSave" id="btnSave" onclick="setTreatment(this.form);" value="<?php echo $this->lang->line("save");?>"  class="button" />
		</li>
	</ul>
	</form>
	</div>

	<?php if ($treatment->difficulty_id<1): ?>
	<div class="clear"></div>
	<div class="norating">
		<?php $extra = $this->minapp_model->getCustomMessage(); ?>
		<form name="frmExtra" id="frmExtra" method="post">
		<fieldset>
		<legend>Custom Message</legend>
			<ul class="adm-form diffForm">
				<input type="hidden" name="frm_name" value="frmExtra">
				<li><label for=""><h3></h3></label></li>
				<li>
					<label><strong>Cancel Message</strong></label>
					<input name="cancel_message" id="cancel_message" type="text" value="<?=$extra->cancel_message?>" maxlength="255" size="50" class="inputs"/>
				</li>
				<li>
					<input type="button" name="btnSave" id="btnSave" onclick="setCustomMessage(this.form);" value="<?php echo $this->lang->line("save");?>"  class="button" />
				</li>
			</ul>
		</fieldset>
		</form>
	</div>
	<?php endif ?>

<script>
$sitePath ="<?php echo site_url ();?>";
$baseUrl = "<?php echo base_url ();?>";
</script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/admin/uploadify/uploadify.css"/>
<script src="<?php echo base_url()?>assets/admin/uploadify/jquery.uploadify.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	var selectedVal = "";
	var selected = $("input[type='radio'][name='rating']:checked");
	if (selected.length > 0) {
		selectedVal = selected.val();
		$("div.desc").hide();
		$("#rating" + selectedVal).show();
	}

	$("input[name$='rating']").click(function() {
		var test = $(this).val();

		$("div.desc").hide();
		$("#rating" + test).show();
	});

	$("#slide5_time_x,#slide5_time_y").setMask("99");

	$('#uploaded_file').hide();
	$("#fileUpload").fileUpload({
		'uploader': '<?php echo base_url()?>assets/admin/uploadify/uploader.swf',
		'cancelImg': '<?php echo base_url()?>assets/admin/uploadify/cancel.png',
		'script': '<?php echo $this->config->item('uploadify_path');?>upload.php',
		'folder': '<?php echo $this->config->item('uploadify_upload_path');?>app_images',
		'buttonText': 'Upload animation',
		'multi': false,
		'fileExt': '*.jpg;*.gif;*.png;*jpeg',
		'fileDesc': 'Image Files',
		'sizeLimit': 19097152,
		'auto': true,
		'removeCompleted': true,
		'onProgress': function(event, ID, fileObj, data) {
			$(document)[0].title = 'BIP Admin Panel';
		},
		'scriptData': {
			'size': 'big',
			'reqwidth': 600,
			'reqheight': 600,
			'dontresizewidth': 600,
			'dontresizeheight': 600
		},
		'onError': function(event, ID, fileObj, errorObj) {

		},
		'onSelect': function(event, ID, fileObj) {

			$('#icon_div').hide();
		},
		'onComplete': function(event, ID, fileObj, response, data) {
			var rs = response.split('~~~~~');
			$("#icon_div").show();
			var urlcss = $sitePath.replace("index.php", "");
			image = '<img style="width:100px;hieght:100px;" src="' + urlcss + 'images/uploads/app_images/' + rs[0] + '">';
			$("#icon_div").show();
			$("#icon_div").html(image);
			$("#slide3_image").val(rs[0]);
			$('#uploaded_file').html('Upload Sucessfully. click "Save" Button to save the attached file.');
		}
	});

});
</script>
<style type="text/css">
	.norating{
		float: right;
		width: 475px;
	}
	fieldset{
		display: block;
		-webkit-margin-start: 2px;
		-webkit-margin-end: 2px;
		-webkit-padding-before: 0.35em;
		-webkit-padding-start: 0.75em;
		-webkit-padding-end: 0.75em;
		-webkit-padding-after: 0.625em;
		border: 2px groove threedface;
		border-image-source: initial;
		border-image-slice: initial;
		border-image-width: initial;
		border-image-outset: initial;
		border-image-repeat: initial;
		min-width: -webkit-min-content;
	}
</style>
