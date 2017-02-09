<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/admin/uploadify/uploadify.css"/>
<script src="<?php echo base_url()?>assets/admin/uploadify/jquery.uploadify.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#fileUpload").fileUpload({
		'uploader'				: '<?php echo base_url()?>assets/admin/uploadify/uploader.swf',
		'cancelImg'				: '<?php echo base_url()?>assets/admin/uploadify/cancel.png',
		'script'				: '<?php echo $this->config->item('uploadify_path');?>upload.php',
		'folder'				: '<?php echo $this->config->item('icon_upload_path');?>',
		'buttonText'    		: 'Browse Image',
		'multi'					: false,
		'fileExt'    	    	: '*.jpg;*.gif;*.png;*jpeg',
		'fileDesc'   			: 'Image Files',
		'sizeLimit'     		: 19097152,
		'auto'           		: true,
		'removeCompleted'		: true,
		'onProgress'  			: function(event,ID,fileObj,data) {
								$(document)[0].title = 'BIP Admin Panel';
								},
		'scriptData' 			: {'size':'small','reqwidth':165,'reqheight':128,'dontresizewidth':165,'dontresizeheight':128},
		'onError'     			: function (event,ID,fileObj,errorObj) {

								},
		'onSelect'      	: function(event,ID,fileObj) {

								$('#icon_div').hide();
								},
		  'onComplete'   	: function(event, ID, fileObj, response, data) {
								//alert(response);

								//$("#demo-box").hide();
								var rs=response.split('~~~~~');
								var urlcss=$sitePath.replace("index.php","");
								$("#icon_div").show();
								//console.log(rs[0]);
								image  = '<img src="'+urlcss+'images/icons/'+rs[0]+'">';
								$("#icon_div").show();
								$("#icon_div").html(image);
								$("#iconFileName").val(rs[0]);
								$('#fileDownloadQueue').html('File Uploaded !!');
								$('#notice').hide();
							}
	});

});
</script>

<ul class="adm-form">
	<form name="frmAddicon" id="frmAddicon" method="post">
        <?php echo validation_errors('<li class="txt-left error_msg">','</li>');?>
		<li>
			<label><strong>Name: </strong></label>
			<input name="iconName" id="iconName" type="text" maxlength="255" size="50" class="inputs"/>
		</li>
		<li>
			<label><strong>Upload icon image:</strong></label>
			<div id="fileUpload"></div>
			<span id="notice">Use Image with width 165px and height 128px for best result</span>
			<div id="icon_div" style=" margin-left:116px; margin-top:10px; margin-bottom:15px; display:none;"></div>
			<input id="iconFileName" type="hidden"  name="iconFileName">
			<p>&nbsp;</p>
		</li>
		<li>
			<input type="button" name="btnSave" id="btnSave" value="<?php echo $this->lang->line("save");?>"  onclick="addIcon();" class="button" />
			<input type="button" name="btnCancel" id="btnCancel" value="<?php echo $this->lang->line("cancel");?>" onclick="listIcon();" class="button" />
		</li>
	</form>
</ul>
