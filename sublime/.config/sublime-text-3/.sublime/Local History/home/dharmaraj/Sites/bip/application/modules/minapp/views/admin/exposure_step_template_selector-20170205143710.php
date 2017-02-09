<?php 
	$tmpl_img_path = base_url()."images/exposure_templates/";
?>
<div class="fancy-header"><?=lang("txt_select_template")?></div>
<div class="fancy-wrapper">
	
	<?php
	if(count($fetchtemplates)>0){
		foreach($fetchtemplates as $templates){
	?>
		<div class="fl template-holder">
			<div><a href="javascript:void(0)" class="add-edit-exposure-steps" data-moduleid="<?=$module_id?>" data-desc="<?=$templates->template_desc?>" data-tmplname="<?=$templates->template_name?>" data-template='<?=$templates->template_display_name?>' data-skillid="<?=$skill_id?>"><img src="<?=$tmpl_img_path.$templates->template_image?>" /></a></div>
			<div class="tmpl-desc"><?=$templates->template_desc?></div>
		</div>
	<?php
		}
	}
	?>
	<div class="clear"></div>
</div>
<script>

	$(function(){
		/*$(".add-edit-exposure-steps").fancybox({
			href: "<?=site_url()?>/minapp/admin/addeditExposureSteps",
			width:1100,
			height:700,
			autoDimensions: false,
			ajax: {
				data: "template="+$(this).attr("data-template")+"&skill_id="+$(this).attr("data-skillid"),
				type: "post"
			}
		});*/

		$(".add-edit-exposure-steps").click(function(){

			$module_id = $(this).attr("data-moduleid");
			$template = $(this).attr("data-template");
			$skill_id = $(this).attr("data-skillid");
			$template_name = $(this).attr("data-tmplname");
			$template_desc = $(this).attr("data-desc");

			var str = {
				"module_id": $module_id,
				"template": $template,
				"skill_id": $skill_id,
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
				onClosed: function(){
					tinyMCE.remove(); //execCommand('mceRemoveControl', true, 'answer_text');
				},
				onComplete: function(){
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
						toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent |  link image media",
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
				}
			});
		});
	});
</script>