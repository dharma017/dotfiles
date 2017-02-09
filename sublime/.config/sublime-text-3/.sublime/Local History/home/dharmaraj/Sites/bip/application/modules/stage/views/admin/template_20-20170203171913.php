<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/admin/uploadify/uploadify.css"/>
<div class="box box-100">
  <!-- box full-width -->
  <div class="boxin">
    <div class="header">
      <h3><?php echo $templateHeading;?> </h3>
    </div>
    <form name="frmAddStep" id="frmAddStep" method="post">
      <?php $this->load->view("stage/admin/template_common_form");?>
      <?php
	if($task=="editStep")
	$downloadData	= $this->stage_model->getDetailByTblNameStepId('bip_download', $stepId,'id');
	?>
	      <div id="container_download"></div>
	      <input type="hidden" id="download_count" value="<?php if($task=="editStep") echo count($downloadData); else echo '1'?>">
	 <?php
	$count = 0;
if($task=="editStep")
{
	foreach($downloadData as $rows)
	{
		$count ++;
		$linkFile = $this->config->item('uploadify_upload_path')."download/".$rows->link_file;
		if($rows->link_file)

		echo '<div id="download_div_'.$count.'" style="border:1px solid #ccc; padding:8px; margin-top:10px;"><strong style="float:left; padding-right:5px; padding-top:2px;">Link Name:</strong><a href="'.base_url().'open_file.php?file_name='.$rows->link_file.'">&nbsp;&nbsp;View File</a> <input type="hidden" name="downloadFileName[]" value="'.$rows->link_file.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" class="inputs" value="'.$rows->link_name.'" name="downloadLinkname[]"> <a href="javascript:removeDownload(\''.$rows->link_file.'\',\'#download_div_'.$count.'\','.$rows->id.')" ><img src="'.base_url().'images/admin_icons/wrong.png"></a></div>';

	}
}
?>
      <?php $this->load->view("stage/admin/template_form_footer");?>
      </ul>
      <div class="rights">
	   <!-- EOF Thumbnail generate according to time -->
	  <div style="width:452px; clear:both;"> Choose your Image to upload (optional)<br />
          <div id="fileUpload"></div>

          <input type="hidden" id="uploadFileName" name="uploadFileName" value="<?php echo $templateData[0]->media;?>">
          <input type="hidden" id="mediaType" name="mediaType" value="<?php echo $templateData[0]->media_type;?>">

    </div>

        <div class="videowrappers" id="videowrappers"  style="width:400px; min-height:100px; height:auto!important; height:100px; text-align:left; position:relative; margin:10px 0; overflow:hidden; ">
          <?php
if($task=="editStep")
{
	if($templateData)
	{
	$thumb=explode('.',$templateData[0]->media);
	if($templateData[0]->media_type=="video")
		{
			if(strpos(strtolower($templateData[0]->media),'.mp3')==true)
				  {
				  $thumb='thumbaudio.jpg';
				  ?>
				  <object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="400" height="24">
				<param name="movie" value="<?php echo base_url().'assets/player/player.swf';?>" />
				<param name="allowfullscreen" value="true" />
				<param name="allowscriptaccess" value="always" />
				<param name="wmode" value="transparent" />

				<embed
				type="application/x-shockwave-flash"
				id="player2"
				name="player2"
				src="<?php echo base_url().'assets/player/player.swf';?>"
				width="400"
				height="24"
				allowscriptaccess="always"
				allowfullscreen="true"
				wmode="transparent"
				flashvars="file=<?php echo base_url().'images/uploads/media/video/'.$templateData[0]->media;?>&image=<?php echo base_url().'images/uploads/thumb/'.$thumb;?>"
				/>
				</object>
				  <?php

				  }
				  else{
				  $thumb=$templateData[0]->image_from_video;
				  ?>
				  <object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="400" height="280">
				<param name="movie" value="<?php echo base_url().'assets/player/player.swf';?>" />
				<param name="allowfullscreen" value="true" />
				<param name="allowscriptaccess" value="always" />
				<param name="wmode" value="transparent" />

				<embed
				type="application/x-shockwave-flash"
				id="player2"
				name="player2"
				src="<?php echo base_url().'assets/player/player.swf';?>"
				width="400"
				height="280"
				allowscriptaccess="always"
				allowfullscreen="true"
				wmode="transparent"
				flashvars="file=<?php echo base_url().'images/uploads/media/video/'.$templateData[0]->media;?>&image=<?php echo base_url().'images/uploads/thumb/'.$thumb;?>"
				/>
				</object>
				  <?php
				  }

		}
		else if($templateData[0]->media_type=="image"){
			if (!empty($templateData[0]->media)) {
				echo '<img align="center"  style="width:padding-left:8px;height:300px;" src="'.$this->config->item('uploadify_upload_path').'media/'.$templateData[0]->media.'">';
			}
		} else{
			if (!empty($templateData[0]->media)) {
				echo $this->config->item('uploadify_upload_path').'media/'.$templateData[0]->media;
			}
		}
	}
}
?>

        </div>
        <div id="media_remove">
            <?php
	if($task=="editStep" && $templateData[0]->media)
		  {
         	 echo '<br/> <a class="savebtns" href="#removeMedia" style="float:right;" onclick="removeMedia(\''.$templateData[0]->media.'\',\''.$templateData[0]->id.'\')"> Remove</a>';
			}
		?>
          </div>
        <input type="hidden" name="linkid" id="linkid" value="<?php echo $templateData[0]->id ?>" />
		 <input type="hidden" name="image_name_fromvideo" id="image_name_fromvideo" value="<?php echo $templateData[0]->image_from_video ?>" />
      </div>
    </form>
  </div>
</div>
<script src="<?php echo base_url()?>assets/admin/uploadify/jquery.uploadify.js" type="text/javascript"></script>
<script type="text/javascript">

function removeFormField(id) {
	var download_count = document.getElementById("download_count").value;
	if(download_count>1)
	{
		download_count = (download_count - 1);
		document.getElementById("download_count").value = download_count;
		$(id).empty();
		$(id).fadeOut();
	}

}
function createImagefromVideo()
{
var timeframe	=$("#thumnailtime").val();
var videonameforimage=$("#videonameforimage").val();
var image_name_fromvideo=$("#image_name_fromvideo").val();
var stepid		=$("#stepId").val();
var linkid		=$("#linkid").val();

if(timeframe=='')
{
alert("Plz fill the time in second.");
return false;
}
var filetype=videonameforimage.split(".");
var filetypedt=filetype[1].toLowerCase();
if(filetypedt =='flv' || filetypedt =='wmv' || filetypedt =='mp4' || filetypedt =='mpg' || filetypedt =='avi' || filetypedt =='mov')
{
}
else
{
	alert("Your file is not video type.")
	return false;
}
$.ajax({
			type:'post',
			async: true,
			url:$sitePath+"/stage/admin/createImagefromVideo",
			data:{"timeframe":timeframe,"videonameforimage":videonameforimage,"stepid":stepid,"linkid":linkid,"thumbimagename":image_name_fromvideo},
					success: function(response)
					{
						var rs=response.split('~~~~~');
						$("#videowrappers").html(rs[0]);
						$("#image_name_fromvideo").val(rs[1]);
					}
			});
}
function removeMedia(fileName,recordId)
{
	if(confirm($jsLang['do_you_want_to_delete_media']))
	{
		$.ajax({
			type:'post',
			async: true,
			url:$sitePath+"/stage/admin/removeMedia",
			data:{"fileName":fileName,"recordId":recordId},
					success: function(response)
					{
					$("#uploadFileName").empty();
					$("#uploadFileName").val('');
					$("#linkid").val('');
					$("#media_remove").fadeOut("slow");
					$("#videowrappers").empty();
					$("#videowrappers").fadeOut("slow");

					}
			});
	}

}

function removeDownload(fileName,divId, recordId)
{
	if(confirm($jsLang['do_you_want_to_delete_download']))
	{
		var download_count = document.getElementById("download_count").value;
		$.ajax({
			type:'post',
			async: true,
			url:$sitePath+"/stage/admin/removeDownload",
			data:{"fileName":fileName,"recordId":recordId,"divId":divId},
					success: function(response)
					{
						$(divId).empty();
						$(divId).fadeOut();
					}
			});
	}
}
var urlcss;

$(document).ready(function() {

	$("#fileUpload").fileUpload({
		'uploader'		: '<?php echo base_url()?>assets/admin/uploadify/uploader.swf',
		'cancelImg'		: '<?php echo base_url()?>assets/admin/uploadify/cancel.png',
		'script'		: '<?php echo $this->config->item('uploadify_path');?>upload.php',
		'folder'		: '<?php echo $this->config->item('uploadify_upload_path');?>media/',
		'buttonText'   	: 'Browse Image',
		'multi'			: false,
		'fileExt': '*.jpg;*.gif;*.png;*jpeg',
		'fileDesc': 'Image Files',
		'sizeLimit'    	: 2223000000,
		'displayData'	: 'speed',
		'auto'         	: true,
		'onProgress'  	: function(event,ID,fileObj,data) {
							$(document)[0].title = 'BIP Admin Panel';
							},
		'scriptData' 	: {'timeframe':$("#thumnailtime").val(),'size':'big','reqwidth':785,'reqheight':2048,'dontresizewidth':785,'dontresizeheight':2048,'thumiamge':$("#image_name_fromvideo").val()},
		'removeCompleted': false,
		'onError'     	: function (event,ID,fileObj,errorObj) {
								console.log("event: "+event);
								console.log("ID : "+ID);
								console.log("fileObj : "+fileObj);
								console.log("errorObj : "+errorObj);
								alert("error occur in upload");
							},
		'onCheck'     : function(event,data,key) {
						$('#file_upload' + key).find('.percentage').text(' - Exists');
						},
		  'onComplete'  : function(event, ID, fileObj, response, data) {
								var rs=response.split('~~~~~');
								var urlcss=$sitePath.replace("index.php","");
								$("#media_remove").show();
								$("#videowrappers").show();

								image  = '<img style="height:300px" src="'+urlcss+'images/uploads/media/'+rs[0]+'">';
								$("#uploadFileName").val(rs[0]);
								$("#videonameforimage").val(rs[0]);
								$("#image_name_fromvideo").val(rs[3])
								$("#mediaType").val(rs[1]);
								$('.fileUploadQueue').html('');
								$("#media_remove").html=("");
								$("#media_remove").html('<a href="#remove_media"  style="float:right;" class="savebtns" onclick="removeMedia(\''+rs[0]+'\',\'\')">Remove</a>');

								if(rs[1]=='image')
								{
									$("#play").hide();
									$("#videowrappers").html('');
									$("#videowrappers").html(image);
									return false;
								}
								else if(rs[1]=='video')
								{

								var thumb=rs[0].split('.');
								if(thumb[1]=="swf")
								{
									$("#play").hide();
									$("#videowrappers").html('hello this test');
									$("#videowrappers").html(rs[0]);
									return false;

								}
								else
								{
									$("#videowrappers").html(rs[2]);
								}
									return false;
								}

							}
	});


		$("#fileDownload").fileUpload({
		'uploader'		: '<?php echo base_url()?>assets/admin/uploadify/uploader.swf',
		'cancelImg'		: '<?php echo base_url()?>assets/admin/uploadify/cancel.png',
		'script'		: '<?php echo $this->config->item('uploadify_path');?>upload.php',
		'folder'		: '<?php echo $this->config->item('uploadify_upload_path');?>download',
		'buttonText'   	: 'Browse File',
		'multi'			: false,
		'sizeLimit'    	: 2223000000,
		'displayData'	: 'speed',
		'auto'         	: true,
		'removeCompleted'	: false,
		'scriptData' 		: {'size':'bigdownload'},
		'onError'     		: function (event,ID,fileObj,errorObj) {

							},
		'onProgress'  		: function(event,ID,fileObj,data) {
							$(document)[0].title = 'BIP Admin Panel';
							},
		 'onComplete'  	 : function(event, ID, fileObj, response, data) {
							var rs=response.split('~~~~~');
							var urlcss=$sitePath.replace("index.php","");
							$('#fileDownloadQueue').html('File Uploaded !! Please type the link name for it.');


							count = ($('#download_count').val())*1+1;

						fileName = '<div id="download_div_'+count+'" style="border:1px solid #ccc; padding:8px; margin-top:10px;"><strong style="float:left; padding-right:5px; padding-top:2px;">Link Name:</strong><a href="<?php echo base_url()?>open_file.php?file_name='+rs[0]+'">&nbsp;&nbsp;View File</a> <input type="hidden" name="downloadFileName[]" value="'+rs[0]+'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text"  class="inputs" value="" name="downloadLinkname[]"> <a href="javascript:removeDownload(\''+rs[0]+'\',\'#download_div_'+count+'\',\'\')" ><img src="'+urlcss+'images/admin_icons/wrong.png"></a></div>';
						$("#container_download").append(fileName);

							$('#fileDownloadQueue').fadeOut("10");
							//$('.fancybox').fancybox().trigger('');
							}
	})

	$('#fileDownloadQueue').html('');


	$("#printUpload").fileUpload({
		'uploader': '<?php echo base_url()?>assets/admin/uploadify/uploader.swf',
		'cancelImg': '<?php echo base_url()?>assets/admin/uploadify/cancel.png',
		'script': '<?php echo $this->config->item('uploadify_path');?>upload.php',
		'folder': '<?php echo $this->config->item('uploadify_upload_path');?>print_images',
		'buttonText': 'Upload Image',
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

			$('#print_div').hide();
		},
		'onComplete': function(event, ID, fileObj, response, data) {
			var rs = response.split('~~~~~');
			$("#print_div").show();
			var urlcss = $sitePath.replace("index.php", "");
			image = '<img style="width:100px;hieght:100px;" src="' + urlcss + 'images/uploads/print_images/' + rs[0] + '">';
			$("#print_div").show();
			$("#print_div").html(image);
			$("#print_image").val(rs[0]);
		}
	});

});

$(".fancybox").fancybox({
//'href' 			: '',
'width'				: 630,
'height'				: '99%',
'autoScale'			: false,
'transitionIn'			: 'elastic',
'transitionOut'		: 'elastic',
'type'				: 'iframe'
});

</script>
