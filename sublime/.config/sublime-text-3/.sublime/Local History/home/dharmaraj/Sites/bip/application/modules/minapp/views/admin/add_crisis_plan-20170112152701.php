<!--Commented by Sabin-->
<!--<script src="<?=base_url();?>assets/admin/js/tinymce4/js/tinymce/tinymce.min.js"></script>
<script src="<?=base_url();?>assets/admin/js/tinymce4/js/tinymce/themes/modern/theme.min.js"></script>-->
<script type="text/javascript">
   $(function(){
		populatePreview();
   });


  tinyMCE.baseURL = "<?php echo base_url(); ?>assets/admin/js/tinymce4/js/tinymce";
  tinyMCE.suffix = '.min';
  tinymce.init({
      selector: "textarea.tinymce",
      theme: "modern",
      skin : 'lightgray',
      width: 450,
      height: 350,
      menubar : false,
      paste_as_text: true,
      resize: false,
      convert_urls: false,
      extended_valid_elements: 'a[href|target=_self]',
       plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
     target_list: false,
     content_css: "<?php echo base_url(); ?>assets/admin/css/content.css",
     setup : function(ed) {
	    ed.on('blur', function(e) {
	        $("#plan_content").val(ed.getContent());
	        populatePreview();
	    });

	     ed.on('keyup', function(e){
	    	 $("#plan_content").val(ed.getContent());
	    	 populatePreview();
	    });
	}
   });
  
  $("#headline").keyup(function(){
  		populatePreview();
  });

  $("button[role='presentation'], div.mce-menu-item").live("click",function(){
  		var content = tinyMCE.get('plan_content').getContent();
  		$("#plan_content").val(content);
  		populatePreview();
  });


  function populatePreview(){
  	$(".pvw-hline").html($("#headline").val());

  	$(".pvw-contents").css("max-height",331-$(".pvw-hline").height()+"px");
  	if($.trim($("#plan_content").val())!=""){
  		$(".pvw-contents").html($("#plan_content").val()).show();
  	}else{
  		$(".pvw-contents").empty().hide();
  	}
  }
</script>

<div class="reg-header">
<?php echo lang("txt_manage_crisis_plan")?>
</div>
<div class="fl">
<form name="frmAddMyCrisisplan" id="frmAddMyCrisisplan" method="post">
	<ul class="adm-form">
	
	<li>
			<label><strong><?=lang('difficulty')?></strong></label>
			<?php
			if(trim($cp->difficulty_id)!=""){
				$diffArr=explode(',', $cp->difficulty_id);
			}else{
				$diffArr = array(DefaultDifficulty());
			}
			?>
			<?php
			$sess_permission = $this->session->userdata('permission');
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
			 ?>
			<select name="difficulty_id[]" id="cp_difficulty_id" class="difficultyCls required" multiple="multiple">
				<?php foreach ($difficulties as $dk => $difficulty):
					
				?>
				<option value="<?=$difficulty->id?>" <?=(isset($diffArr) && in_array($difficulty->id, $diffArr)) ? 'selected="selected"': ''?>>&nbsp;<?=$difficulty->difficulty?></option>
			<?php endforeach ?>
		</select>
	</li>

	<li>
		<label><strong><?=lang('txt_headline')?></strong></label>
		<input name="headline" id="headline" type="text" value="<?=stripslashes($cp->headline)?>" maxlength="255" size="50" class="inputs"/>
	</li>

	<li>
		<div class="fl">
		<label><strong>Content</strong></label>
		</div>
		<div class="fl">
		<textarea name="plan_content" id="plan_content" class="tinymce" rows="20" cols="8"><?=$cp->contents?></textarea>
		</div>
		<div class="clear"></div>
	</li>


<li class="last-li">
	<input type="hidden" value="<?=$cp->plan_id?>" name="plan_id" id="plan_id">
	<input type="button" name="btnSave" id="btnSave" value="<?php echo $this->lang->line("save");?>"  onclick="saveMyCrisisplan();" class="button" />
	<input type="button" name="btnCancel" id="btnCancel" value="<?php echo $this->lang->line("cancel");?>" onclick="$('#selTreatment1').trigger('change');$('#selhide').hide();$('#regfilter').show(); destroyTinyMCE(); " class="button" />
</li>	
</ul>
</form>
</div>

<div class="fr" style="margin-right:20px;">
	<div class="page-preview">
		<h1>Preview</h1>
		<div class="phone-case">
			<div class="phone-preview-overlay hide"></div>
			<div class="phone-preview hc">
				<div class="pvw-hline"></div>
				<div class="pvw-contents"></div>
			</div>
		</div>
	</div>
</div>
		
<div class="clear"></div>