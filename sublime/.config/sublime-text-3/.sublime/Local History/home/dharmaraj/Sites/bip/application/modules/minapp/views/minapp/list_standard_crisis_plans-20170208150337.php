 <script src="<?=base_url();?>assets/admin/js/tinymce4/js/tinymce/tinymce.min.js"></script>
<script src="<?=base_url();?>assets/admin/js/tinymce4/js/tinymce/themes/modern/theme.min.js"></script>
 <div class="list-standard-cp">
	 <div><b><?=lang("txt_standard_crisis_plan")?></b></div>
	 <div class="hint"><?=lang("txt_select_standard_crisis_plan")?></div>
	 <table cellpadding="0" cellspacing="0" width="782px" class="gridtable">
	      <thead>
	        <tr>
	            <th width="248px"><?=lang("txt_headline")?></th>
	            <th width="120px"><?=lang("txt_action")?></th>
	     </tr>
	      </thead>
	      <tbody>
	      <?php
	      	if($standard_crisis_plans){
	      		foreach($standard_crisis_plans as $scp){
	      			echo "<tr>";
	      				echo "<td>";
	      					echo $scp->headline;
			      			
	      				echo "</td>";
	      				echo "<td>
	      				    <div class='hid-headline' style='display:none;'>".$scp->headline."</div>
	      				    <div class='hid-contents' style='display:none;'>".$scp->contents."</div>
	      					<a class='publish-hw-nolink show-scp-to-edit' href='#content-scp'>Select</a>
	      					</td>";
	      			echo "</tr>";
	      		}
	      	}else{
	      		echo "<tr><td colspan='3'>".lang("txt_no_crisis_plan_avail")."</td></tr>";
	      	}
	      ?>
	      </tbody>
	</table>
	<div style="display:none">
		<div id="content-scp">
			<form id="frmSaveCustomCrisisPlan" name="frmSaveCustomCrisisPlan" method="post">
				 <ul class="adm-form fb-label">
				 	<li>
				 		<label style="width:80px"><?=lang("txt_headline")?></label>
				 		<input type="text" id="scp_headline" style="padding:5px;border:1px solid #ccc;" name="scp_headline" size="58" />
				 	</li>
				 	<li>
				 		<div style="float:left;"><label style="width:80px"><?=lang("contents")?></label></div>
				 		<div style="float:left;">
				 		<textarea class="tinymce" id="scp_contents" rows="20" cols="8" style="padding:5px;border:1px solid #ccc;" name="scp_contents"></textarea>
				 		</div>
				 		<div style="clear:both"></div>
				 	</li>
				 	<li class="last-li">
				 	<label style="width:80px">&nbsp;</label>
				 	<input type="hidden" value="<?=$patient_id?>" name="patient_id" id="patient_id">
				 	<input type="hidden" value="<?=$difficulty_id?>" name="difficulty_id" id="difficulty_id">
				 	<input type="hidden" value="<?=$published_by?>" name="published_by" id="published_by">
				 	<input type="hidden" value="" name="plan_id" id="plan_id">

						<input type="button" name="btnSave" id="btnSave" value="<?php echo $this->lang->line("save");?>" class="button btn-save-crisis-plan" />
						<input type="button" name="btnCancel" id="btnCancel" value="<?php echo $this->lang->line("cancel");?>" onclick="$('.add-crisis-plan').trigger('click')" class="button" />
				 	</li>
				 </ul>
			</form>
		</div>
	</div>
</div>
<script>
$(function(){

	$("#frmSaveCustomCrisisPlan").validate({
        rules: {
            scp_headline: {
                required: true
            },
            scp_contents: {
                required: true
            }
        },
        messages: {
            scp_headline: {
                required: $jsLang['required']
            },
            scp_contents: {
                required: $jsLang['required']
            },
        }
    });

	$(".btn-save-crisis-plan").live("click", function(){
		$.ajax({
			url: $sitePath + "/minapp/saveCustomCrisisPlan",
			data: $("#frmSaveCustomCrisisPlan").serialize(),
			type: "POST",
			dataType: "json",
			beforeSend: function(){
				if (!$("#frmSaveCustomCrisisPlan").valid()) {
	                $("#preLoader").hide();
	                return false;
            	}

			},
			success:function(response){
				if(response.error_code=="OK"){
					showCrisisplanList('<?=$patient_id?>','<?=$username?>','<?=$difficulty_id?>');
					$.fancybox.close();
				}else{
					alert(response.error_msg);
				}
			}
		});
	});
	
     $(".show-scp-to-edit").each(function(){
     		var obj = $(this);
     		obj.fancybox({
     			modal: true,
     			onComplete: function(){
            		$headline = obj.prev(".hid-contents").prev(".hid-headline").html();
            		$contents = obj.prev(".hid-contents").html();
		            $("#scp_headline").val($headline);
		            $("#scp_contents").val($contents).trigger("change");
		            tinyMCE.baseURL = "<?php echo base_url(); ?>assets/admin/js/tinymce4/js/tinymce";
					  tinyMCE.suffix = '.min';
					  tinymce.init({
					      selector: "textarea.tinymce",
					      theme: "modern",
					      skin : 'lightgray',
					      width: 440,
					      height: 340,
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
						        $("#scp_contents").val(ed.getContent());
						    });
						}
					   });
		            //tinyMCE.activeEditor.setContent($contents);
		        }
		    });
     });
})
</script>