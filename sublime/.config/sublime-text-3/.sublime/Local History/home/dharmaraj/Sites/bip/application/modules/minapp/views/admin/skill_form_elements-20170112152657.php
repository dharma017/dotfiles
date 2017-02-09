<?php
$fetch = $this->minapp_model->fetchSkillDetailsExtraById($skillId,$type);
?>
<!--Commented by Sabin-->
<!--<script src="<?=base_url();?>assets/admin/js/tinymce4/js/tinymce/tinymce.min.js"></script>
<script src="<?=base_url();?>assets/admin/js/tinymce4/js/tinymce/themes/modern/theme.min.js"></script>-->
<?php
if($type=="thoughts"){
?>
			<li>
				<label><strong><?=lang("txt_thought_type")?></strong></label>
				<?php
				if($skillId>0){ //don't let this change in edit mode
					echo ucwords($fetch->thought_type);
			    ?>
			    <input type="hidden" name="thought_type" id="thought_type" value="<?=$fetch->thought_type?>">
			    <?php
				}else{
				?>
				<select id="thought_type" name="thought_type" class="my-dropdown" onchange="changeThoughtContent();">
					<option value=""><?=lang("txt_select")?></option>
					<option value="text" <?php echo $fetch->thought_type=="text" ? "selected='selected'" : ""?>>Text</option>
					<option value="sound" <?php echo $fetch->thought_type=="sound" ? "selected='selected'" : ""?>>Sound</option>
				</select>
				<?php } ?>
			</li>
			<li>
				<label><strong><?=lang("txt_headline")?></strong></label>
				<input type="text" id="thought_headline" name="thought_headline" class="inputs" value="<?=$fetch->headline?>" />
			</li>
			<li class="content-text" style="display:none;">
				<div class="fl">
					<label><strong><?=lang("contents")?></strong></label>
				</div>
				<div class="fl">
					<textarea name="thought_text" id="thought_text" class="tinymce" rows="20" cols="8"><?=$fetch->thought_text?></textarea>
				</div>
				<div class="clear"></div>
			</li>
			<li class="content-sound" style="display:none">
				<ul class="adm-form">
					<li>
						<label><strong><?=lang("txt_sound_file")?></strong></label>
						<div class="fl" style="margin-right:10px">
							<button type="button" name="uploadBtn" id="uploadBtn" class="upload-btn">Upload Sound</button><div class="skill-sound-upload" >Uploading...</div>
							<input type="hidden" id="sound_file" name="sound_file" value="<?=$fetch->thought_sound_file?>" />
						</div>
						<div class="uploaded-sound fl" style="display:<?php echo $skillId>0 ? "block" : "none" ?>">
							
							<a href="javascript:void(0)" class="play-pause-sound" data-dowhat="play"><img src="<?=base_url()?>images/admin_icons/sound_play.png" /></a>
							<a href="javascript:void(0)" class="play-pause-sound" data-dowhat="pause" style="display:none"><img src="<?=base_url()?>images/admin_icons/sound_pause.png" /></a>
						</div>
						<div class="clear"></div>
						<div class="progress-holder">
							 <div id="progressOuter" class="progress progress-striped active" style="display:none;">
            <div id="progressBar" class="progress-bar progress-bar-success"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
            </div>
          </div>
						</div>
					</li>
					<li>
						<label><strong><?=lang("txt_audio_player_bg_color")?></strong></label>
						<div>
							<div class="color-chooser fl" style="background-color:#ffffff;border:1px solid #cccccc" >
							<input type="radio" name="player_bg_color" value="ffffff" <?php echo ($fetch->sound_background_color=="ffffff" || $fetch->sound_background_color=="") ? "checked='checked'" :""?> />
							</div>
							<?php
								$getColors = $this->minapp_model->loadColors();
								foreach($getColors as $color){
							?>
								<div class="color-chooser fl" style="background-color:#<?=$color->colour_code?>" >
								<input type="radio" name="player_bg_color" value="<?=$color->colour_code?>" <?php echo ($fetch->sound_background_color==$color->colour_code) ? "checked='checked'" :""?> />
								</div>
							<?php
								}
							?>
							<div class="clear"></div>
						</div>
					</li>
				</ul>
			</li>
<?php	
}else if($type=="exposure"){
	if($skillId>0){
		$ex_id = $skillId;
		$fetchSteps = $this->minapp_model->fetchExposureSteps($ex_id);
	}else{
		$ex_id = "";
		$fetchSteps = array();
	}
?>
			<li>
				<input type="hidden" name="skill_id" id="skill_id" value="<?=$ex_id?>">
				<div><h2><b><?=lang("steps")?></b></h2></div>
				<div>
					<table width="" cellpadding="0" cellspacing="0" border="0" class="grid" id="step-exposure-list-table">
						<thead>
							<th width="10%"><?=lang("txt_reorder")?></th>
							<th width="40%"><?=lang("txt_step_name")?></th>
							<th width="30%"><?=lang("template")?></th>
							<!-- <th width="10%"><?=lang("txt_status")?></th> -->
							<th width="30%"><?=lang("txt_action")?></th>
						</thead>
						<tbody>
						<?php
							if(count($fetchSteps)>0){
								foreach($fetchSteps as $pk=>$step){
						?>
							<tr id="ID_<?=$step->step_id?>" <?php echo ($pk%2==1) ? 'class="odd step-row"': 'class="even step-row"';?>>
								<td class="handle"><?=$step->sort_order?>&nbsp;<img src='../../images/admin_icons/reorder.png' width='18' / ></td>
								<td class="handle" data-stepid="<?=$step->step_id?>">
								<?
									if($step->step_name=="no-title"){
										echo "<i>(Same title as exposure/skill)</i>";
									}else{
										echo stripslashes($step->step_name);
									}
								?></td>
								<td class="handle"><?=lang($step->template)?></td>
								<!-- <td>
									<?php
										if($step->step_status==1){
									?>
											<a href="javascript:void(0)" data-stepid="<?=$step->step_id?>" data-newstatus="0" class="link-green change-exposure-step-status" title="<?=lang("toggle_status")." ".lang("inactive")?>">
												<img src="<?=base_url()?>images/admin_icons/enabled.gif">
											</a>
									<?php
										}else{
									?>
											<a href="javascript:void(0)" data-stepid="<?=$step->step_id?>" data-newstatus="1" class="link-red change-exposure-step-status"  title="<?=lang("toggle_status")." ".lang("active")?>">
												<img src="<?=base_url()?>images/admin_icons/wrong.png">
											</a>
									<?php
										}
									?>
								</td> -->
								<td>
									<a href="javascript:void(0)" class="edit-exposure-steps" data-stepid="<?=$step->step_id?>" data-moduleid="<?=$module_id?>" data-desc="<?=$step->template_desc?>" data-tmplname="<?=$step->template_name?>" data-template='<?=$step->template?>' data-skillid="<?=$skillId?>">
										<img src="<?=base_url()?>images/admin_icons/edit.png">
									</a>
									<a href="javascript:void(0)" class="change-exposure-step-status" data-newstatus="0"   data-stepid="<?=$step->step_id?>" data-moduleid="<?=$module_id?>" data-desc="<?=$step->template_desc?>" data-tmplname="<?=$step->template_name?>" data-template='<?=$step->template?>' data-skillid="<?=$skillId?>">
										<img src="<?=base_url()?>images/admin_icons/delete.png">
									</a>
								</td>
							</tr>
						<?php
								}
							}else{
								echo "<tr><td colspan='5' align='center'>".lang("no_task_avail")."</td></tr>";
							}
						?>
						</tbody>
					</table>	
				</div>
				<div class="new-step"><span class="fl small-hints" style="display:<?php echo $skillId>0 ? "none" : "block"?>">Note: If you are adding new exposure/skills, first you need to save it before you can add steps</span>
					<button type="button" class="fr new-btn-blue add-new-exposure-step" disabled="disabled"><?=lang("txt_new_step")?></button>
					<div class="clear"></div>
				</div>
			</li>
<?php
}else if($type=="skills"){
	if($skillId>0){
		$ex_id = $skillId;
		$fetchSteps = $this->minapp_model->fetchExposureSteps($ex_id);
	}else{
		$ex_id = "";
		$fetchSteps = array();
	}
?>
<li>
				<input type="hidden" name="skill_id" id="skill_id" value="<?=$ex_id?>">
				<div><h2><b><?=lang("steps")?></b></h2></div>
				<div>
					<table width="" cellpadding="0" cellspacing="0" border="0" class="grid" id="step-exposure-list-table">
						<thead>
							<th width="10%"><?=lang("txt_reorder")?></th>
							<th width="40%"><?=lang("txt_step_name")?></th>
							<th width="30%"><?=lang("template")?></th>
							<!-- <th width="10%"><?=lang("txt_status")?></th> -->
							<th width="30%"><?=lang("txt_action")?></th>
						</thead>
						<tbody>
						<?php
							if(count($fetchSteps)>0){
								foreach($fetchSteps as $pk=>$step){
						?>
							<tr id="ID_<?=$step->step_id?>" <?php echo ($pk%2==1) ? 'class="odd step-row"': 'class="even step-row"';?>>
								<td class="handle"><?=$step->sort_order?><img src='../../images/admin_icons/reorder.png' width='18' / ></td>
								<td class="handle" data-stepid="<?=$step->step_id?>">
								<?
									if($step->step_name=="no-title"){
										echo "<i>(Same title as exposure/skill)</i>";
									}else{
										echo stripslashes($step->step_name);
									}
								?>
								</td>
								<td class="handle"><?=lang($step->template)?></td>
								<!-- <td>
									<?php
										if($step->step_status==1){
									?>
											<a href="javascript:void(0)" data-stepid="<?=$step->step_id?>" data-newstatus="0" class="link-green change-exposure-step-status" title="<?=lang("toggle_status")." ".lang("inactive")?>">
												<img src="<?=base_url()?>images/admin_icons/enabled.gif">
											</a>
									<?php
										}else{
									?>
											<a href="javascript:void(0)" data-stepid="<?=$step->step_id?>" data-newstatus="1" class="link-red change-exposure-step-status"  title="<?=lang("toggle_status")." ".lang("active")?>">
												<img src="<?=base_url()?>images/admin_icons/wrong.png">
											</a>
									<?php
										}
									?>
								</td> -->
								<td>
									<a href="javascript:void(0)" class="edit-exposure-steps" data-stepid="<?=$step->step_id?>" data-moduleid="<?=$module_id?>" data-desc="<?=$step->template_desc?>" data-tmplname="<?=$step->template_name?>" data-template='<?=$step->template?>' data-skillid="<?=$skillId?>">
										<img src="<?=base_url()?>images/admin_icons/edit.png">
									</a>
									<a href="javascript:void(0)" class="change-exposure-step-status" data-stepid="<?=$step->step_id?>" data-newstatus="0" data-moduleid="<?=$module_id?>" data-desc="<?=$step->template_desc?>" data-tmplname="<?=$step->template_name?>" data-template='<?=$step->template?>' data-skillid="<?=$skillId?>">
										<img src="<?=base_url()?>images/admin_icons/delete.png">
									</a>
								</td>
							</tr>
						<?php
								}
							}else{
								echo "<tr><td colspan='5' align='center'>".lang("no_task_avail")."</td></tr>";
							}
						?>
						</tbody>
					</table>	
				</div>
				<div class="new-step"><span class="fl small-hints" style="display:<?php echo $skillId>0 ? "none" : "block"?>">Note: If you are adding new exposure/skills, first you need to save it before you can add steps</span>
					<button type="button" class="fr new-btn-blue add-new-exposure-step" disabled="disabled"><?=lang("txt_new_step")?></button>
					<div class="clear"></div>
				</div>
			</li>
<?php	
}
?>
<script src="<?php echo base_url()?>assets/admin/js/SimpleAjaxUploader.min.js" type="text/javascript"></script>
<script type="text/javascript">
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
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code",
     target_list: false,
     content_css: "<?php echo base_url(); ?>assets/admin/css/content.css",
     setup : function(ed) {
	    ed.on('blur', function(e) {
	    	$("#"+ed.id).val(ed.getContent());
	    });
	}
   });
</script>
<script>
var myAudio;

<?php 
  if($skillId>0){
  	if(trim($fetch->thought_sound_file)!=""){
 ?>
 myAudio = new Audio("<?=base_url()?>assets/sound_files/thoughts/<?=$fetch->thought_sound_file?>");
 <?php
 	}
  }
?>

function validateSkillForm(){
	$type = $("#skill_type").val();
	if($type=="thoughts"){
		
		$thought_type = $("#thought_type").val();
		$('#frmAddSkill').data('validator', null);
		$("#frmAddSkill").unbind('validate');

		$("#frmAddSkill").validate({
                rules: {
                	skill_name:{
                		required: true
                	},
                    thought_headline: {
                        required: true
                    },
                    thought_text: {
                        required: $thought_type=="text"? true : false 
                    },
                    sound_file: {
                        required: $thought_type=="sound" ? true : false
                    }
                },
                messages: {
                	skill_name:{
                		required: $jsLang['required']
                	},
                    thought_headline: {
                        required: $jsLang['required']
                    },
                    thought_text: {
                        required: $jsLang['required']
                    },
                    sound_file: {
                        required: $jsLang['required']
                    }
                }
        });
	}else if($type=="exposure" || $type=="skills"){
		$('#frmAddSkill').data('validator', null);
		$("#frmAddSkill").unbind('validate');

		$("#frmAddSkill").validate({
                rules: {
                	skill_name:{
                		required: true
                	}
                },
                messages: {
                	skill_name:{
                		required: $jsLang['required']
                	}
                }
        });
	}
}

function changeThoughtContent(){
	$thought_type = $("#thought_type").val();
	if($thought_type=="text"){
		$(".content-text").show();
		$(".content-sound").hide();
		$(".save-skills").show();
	}else if($thought_type=="sound"){
		$(".content-text").hide();
		$(".content-sound").show();
		$(".save-skills").show();
	}else{
		$(".content-text").hide();
		$(".content-sound").hide();
		$(".save-skills").hide();
	}
}

$(function(){
	
	if($("#skill_type").val()=="thoughts"){ //call the function only if the skill type is "Thoughts"
		changeThoughtContent();
	}

	if($("#skill_id").val()>0){
		$(".add-new-exposure-step").removeAttr("disabled");
	}else{
		$(".add-new-exposure-step").removeAttr("disabled").attr("disabled","disabled");
	}
	
	
	

	$(".add-new-exposure-step").fancybox({
		href: "<?=site_url()?>/minapp/admin/exposureTemplateSelector",
		width:1004,
		height:700,
		autoDimensions: false,
		ajax:{
			type: "post",
			data: "module_id="+$("#module_id").val()+"&skill_id="+$("#skill_id").val(),
			beforeSend:function(){
				$("#preLoader").hide();
				$.fancybox.hideActivity();
			}
		},
		onClosed: function(){
			editMySkill($("#skill_id").val(), $("#module_id").val());
		}
	}).resize();

	
	if($("#uploadBtn").length>0){
		var btn = $("#uploadBtn"),
			progressBar = document.getElementById('progressBar'),
	      	progressOuter = document.getElementById('progressOuter');
	     
		var uploader = new ss.SimpleUpload({
	        button: btn,
	        url: '<?php echo $this->config->item('uploadify_path');?>upload_thought_sound.php',
	        name: 'uploadfile',
	        multipart: true,
		    hoverClass: 'upload-btn-hover',
		    focusClass: 'upload-btn-hover',
		    allowedExtensions: ['mp3'],
		    disabledClass: 'upload-btn-disabled',
	        responseType: 'json',
	        startXHR: function() {
	            progressOuter.style.display = 'block'; // make progress bar visible
	            this.setProgressBar( progressBar );
	        },
	        onSubmit: function() {
	        	btn.attr("disabled","disabled");
	            btn.html('Uploading...'); // change button text to "Uploading..."
	          },
	        onComplete: function( filename, response ) {
	            btn.html('Upload Sound');
	            btn.removeAttr("disabled");
	            progressOuter.style.display = 'none'; // hide progress bar when upload is completed

	            if ( !response ) {
	                alert('Unable to upload file');
	                return;
	            }

	            if ( response.success === true ) {
	               $(".save-skills").removeAttr("disabled");
	          		$(".skill-sound-upload").hide();

	          		
	  				myAudio = new Audio("<?=base_url()?>assets/sound_files/thoughts/"+response.uploaded_file);
	  				$("#sound_file").val(response.uploaded_file);
	      			
	      			$(".uploaded-sound").show();

	            } else {
	                if ( response.msg )  {
	                   alert(response.msg);

	                } else {
	                    alert('An error occurred and the upload failed.');
	                }
	            }
	          },
	        onError: function() {
	          //  progressOuter.style.display = 'none';
	           alert('Unable to upload file');
	          }
		});
	}

	/*$("#soundUpload").fileUpload({
          'uploader': '<?php echo base_url()?>assets/admin/uploadify/uploader.swf',
          'cancelImg': '<?php echo base_url()?>assets/admin/uploadify/cancel.png',
          'script': '<?php echo $this->config->item('uploadify_path');?>upload_thought_sound.php',
          'buttonText': 'Upload Sound',
          'multi': false,
          'fileExt': '*.mp3;*.ogg;',
          'fileDesc': 'Audio Files',
          'sizeLimit': 15728640,
          'auto': true,
          'removeCompleted': true,
          'onProgress': function(event, ID, fileObj, data) {
            $(document)[0].title = 'BIP Admin Panel';
            
          },
          
          'onError': function(event, ID, fileObj, errorObj) { 
          },
          'onSelect': function(event, ID, fileObj) {
            	$(".skill-sound-upload").show();
          },
          'onComplete': function(event, ID, fileObj, response, data) {
          		$(".save-skills").removeAttr("disabled");
          		$(".skill-sound-upload").hide();

          		var ret = response.split("^");
          		if(ret[0]=="ok"){
          			if(typeof ret[1]!="undefined"){
          				myAudio = new Audio("<?=base_url()?>assets/sound_files/thoughts/"+ret[1]);
          				$("#sound_file").val(ret[1]);
          			}
          			$(".uploaded-sound").show();
          		}else{
          			$(".uploaded-sound").hide();
          			alert(ret[1]);
          		}
          		$(".save-skills").removeAttr("disabled");
          }
 	});*/

	$(".play-pause-sound").click(function(){
		$dowhat = $(this).attr("data-dowhat");
		if($dowhat=="play"){
			$(this).hide();
			$(".play-pause-sound[data-dowhat='pause']").show();
			myAudio.volume = 0.2;
			myAudio.play();
		}else{
			$(this).hide();
			$(".play-pause-sound[data-dowhat='play']").show();
			myAudio.pause();
		}
		myAudio.addEventListener('ended',function(){
			$(".play-pause-sound[data-dowhat='play']").show();
			$(".play-pause-sound[data-dowhat='pause']").hide();
		})
	});


	$(".change-exposure-step-status").click(function(){

			var obj = $(this);

			var deleteSkillExposure = false;
		    if(confirm("Are you sure you want to delete this item?")){
		        if(obj.attr("data-inuse")==1){
		            if(confirm("This item is already in use. Do you want to delete this?")){
		                deleteSkillExposure = true;
		            }
		        }else{
		            deleteSkillExposure = true;
		        }
		    }

		    if(deleteSkillExposure==true){
		    	var stepID = obj.attr("data-stepid");
			    var newStatus = obj.attr("data-newstatus");
			    $.ajax({
			        url: $sitePath + "/minapp/admin/changeExposureStepStatus",
			        type: "POST",
			        async:false,
			        data: "step_id="+stepID+"&new_status="+newStatus,
			        beforeSend:function(){
			             $("#preLoader").show();
			        },
			        success:function(data){
			            $("#preLoader").hide();
			            /*$status = newStatus==0?1:0;
			            obj.attr("data-newstatus",$status);
			            var d = $.parseJSON(data);
			            obj.find("img").attr("src",decodeURIComponent(d.icon_path));
			            obj.attr("title",d.tooltip);*/
			            obj.parent("td").parent("tr").fadeOut("slow",function(){
			            	$(this).remove();
			            });
			        }
			    });
		    }
		   
	});

	var fixExposureHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index)
            {
              $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

		$('#step-exposure-list-table tbody').sortable({
			opacity: 0.6,
			cursor: 'move',
			scrollSensitivity: 40,
            axis: 'y',
			handle: ".handle",
            helper: fixExposureHelperModified,
            update: function (event, ui) {
                var str = $(this).sortable('serialize');
                var skill_id="<?php echo $skill_id; ?>";
               
                $serializeData = str + '&skill_id=<?=$skillId?>';
                console.warn($serializeData);
                // POST to server using $.post or $.ajax
				$.post( $sitePath+"/minapp/admin/sortExposureSteps", $serializeData, function( data ) {
					//showSteps(stage_id);
					$(".step-row").each(function(index){ //renumbering the column
				        $(this).children("td:first").html((index+1)+"<img src='../../images/admin_icons/reorder.png' width='18' style='margin-left:5px' / >");
				        if(index%2==1){
				        	$class = "odd";
				        }else{
				        	$class = "even";
				        }
				        $(this).removeClass("odd").removeClass("even").addClass($class);
				    });
                });
            }
        }).disableSelection();


        $(".edit-exposure-steps").click(function(){

			$module_id = $(this).attr("data-moduleid");
			$template = $(this).attr("data-template");
			$skill_id = $(this).attr("data-skillid");
			$template_name = $(this).attr("data-tmplname");
			$template_desc = $(this).attr("data-desc");
			$step_id  = $(this).attr("data-stepid");

			var str = {
				"module_id": $module_id,
				"template": $template,
				"skill_id": $skill_id,
				"step_id": $step_id,
				"template_desc": $template_desc,
				"template_name": $template_name
			};

			$.fancybox({
				href: "<?=site_url()?>/minapp/admin/addeditExposureSteps",
				width:1100,
				height:700,
				autoDimensions: false,
				ajax: {
					data: str,
					type: "post",
					beforeSend: function(){
						$("#preLoader").hide();
						$.fancybox.hideActivity();
					}
				},
				onComplete: function(){
					$alternateTextHeight = $(".pvw-alternate-text").height();
    				$(".phone-preview").find(".contents").css("height",(285-$alternateTextHeight)+"px");
					tinyMCE.baseURL = "<?php echo base_url(); ?>assets/admin/js/tinymce4/js/tinymce";
					tinyMCE.suffix = '.min';
					tinymce.init({
						selector: "#answer_text, #step_answer_content",
						theme: "modern",
						skin : 'lightgray',
						width: 440,
						height: 250,
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
						toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link ",
						target_list: false,
						content_css: "<?php echo base_url(); ?>assets/admin/css/content.css",
						setup : function(ed) {
						    ed.on('blur', function(e) {
						    	 	$(".text-holder").html(ed.getContent());
							    	if($("#answer_text").length>0){
		        						$("#answer_text").val(ed.getContent());
		        					}

		        					if($("#step_answer_content").length>0){
		        						$("#step_answer_content").val(ed.getContent());
		        					}
						    });
						}
					});
				},
				onClosed: function(){
					destroyTinyMCE();
				}
			});
		});
})
</script>
