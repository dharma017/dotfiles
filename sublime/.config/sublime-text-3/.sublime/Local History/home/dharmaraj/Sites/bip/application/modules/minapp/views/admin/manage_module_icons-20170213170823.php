<!-- <script src="<?php echo base_url()?>assets/admin/uploadify/jquery.uploadify.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo base_url()?>assets/admin/uploadify/uploadify.css" /> -->
<link href="<?php echo base_url()?>assets/admin/fine-uploader/fine-uploader.css" rel="stylesheet">
    <script src="<?php echo base_url()?>assets/admin/fine-uploader/fine-uploader.min.js"></script>
<div style="min-width:500px; max-width:900px;">
	<div class="reg-header" style="border-bottom:1px dotted #000">Add Medias</div>
	<div class="content" style="margin-top:15px;">
			<div class="uploader">
				<div class="media-sub-head" style="">Images</div>
				<div class="fl" style="margin-right:15px;margin-top:5px;">Upload Icons</div>
				<div class="fl"><div id="uploader"></div><!-- <input type="file" name="imageUpload" id="imageUpload" /> --><span style="margin-top:10px; display:block;">Max 128px X 128px (PNG Only, transparent PNG recommended)</span></div>
				<div class="clear"></div>
				<input type="hidden" id="hid-target-element" value="<?=$target_element?>">
				<div class="uploaded-icon" style="margin-top:20px;">
					<div class="icon-holder">
					<?php
					if(count($icons)>0){
						foreach($icons as $icon){
							$filename = basename($icon);
							$imagePath 	= base_url().'images/uploads/module_icons/'.$filename;
							
							echo '<div class="fl module-icon-thumb"><a href="javascript:void(0)" onclick=setIconForModule("'.$filename.'")><img class="thumbnail" src="'.$imagePath.'" height="48" /></a></div>';
						}
					}
					?>
					
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="audio-upload-section">
				<div class="media-sub-head" style="">Audios</div>
				<div class="fl" style="margin-right:10px;margin-top:5px;">Upload countdown alert audio </div>
				<div class="fl">
					<div id="uploaderaudio"></div><!-- <input type="file" name="audioUpload" id="audioUpload" /> -->
					<span style="margin-top:10px; display:block;">Max file size 1 MB (mp3 file only)</span>
				</div>
				<div class="clear"></div>
				<div class="uploaded-audio">
					<div class="audio-icon-holder">
						<?php
						if(count($audios)>0){
							foreach($audios as $audio){
								$filename = basename($audio);
								if($filename!=="default_alert.mp3"){
									$audioPath 	= base_url().'assets/sound_files/misc/'.$filename;
									echo '<div class="fl">
											<a href="javascript:void(0)" class="play-uploaded-audio" data-audiourl="'.$audioPath.'">
												<img src="'.base_url().'images/admin_icons/sound_play.png" class="playing-normal"  height="32" />
												<img src="'.base_url().'images/admin_icons/sound_pause.png" class="sound-paused" style="display:none"  height="32" />
											</a>
									   	</div>';
								}
								
							}
						}
						?>
					</div>
				</div>
			</div>
	</div>
</div>
<?php include_once("./assets/admin/fine-uploader/templates/default.html"); ?>
<script>
$(function(){
		var isPlaying = 0;
		var myAudio = new Audio();
		$(".play-uploaded-audio").live("click", function(){
			var rand = Math.random();
			 myAudio.src = $(this).attr("data-audiourl")+"?rand="+rand;
			var obj = $(this);
			if(isPlaying==0){
				isPlaying =1;
				obj.find(".playing-normal").hide();
				obj.find(".sound-paused").show();
				myAudio.play();
			}else{
				isPlaying = 0;
				obj.find(".playing-normal").show();
				obj.find(".sound-paused").hide();
				myAudio.pause();
			}
			myAudio.addEventListener('ended',function(){
				isPlaying = 0;
				obj.find(".playing-normal").show();
				obj.find(".sound-paused").hide();
			});
		});

		// $("#imageUpload").fileUpload({
	 //          'uploader': '<?php echo base_url()?>assets/admin/uploadify/uploader.swf',
	 //          'cancelImg': '<?php echo base_url()?>assets/admin/uploadify/cancel.png',
	 //          'script': '<?php echo $this->config->item('uploadify_path');?>upload_module_icon.php',
	 //          'folder': '<?php echo $this->config->item('uploadify_upload_path');?>module_icons',
	 //          'buttonText': 'Upload Icon',
	 //          'multi': true,
	 //          'fileExt': '*.png',
	 //          'fileDesc': 'Image Files',
	 //          'sizeLimit': 2097152,
	 //          'auto': true,
	 //          'removeCompleted': true,
	 //          'onProgress': function(event, ID, fileObj, data) {
	 //            $(document)[0].title = 'BIP Admin Panel';
	 //          },
	 //          'scriptData': {
	 //            'size': 'big',
	 //            'reqwidth': 128,
	 //            'reqheight': 128,
	 //            'dontresizewidth': 128,
	 //            'dontresizeheight': 128
	 //          },
	 //          'onError': function(event, ID, fileObj, errorObj) {

	 //          },
	 //          'onSelect': function(event, ID, fileObj) {
	            
	 //          },
	 //          'onComplete': function(event, ID, fileObj, response, data) {
	 //          	var rs = response.split("~~~~~");
	 //            $(".uploaded-icon").find(".icon-holder").append(rs[1]);
	 //          }
	 //      });


		// $("#audioUpload").fileUpload({
		//           'uploader': '<?php echo base_url()?>assets/admin/uploadify/uploader.swf',
		//           'cancelImg': '<?php echo base_url()?>assets/admin/uploadify/cancel.png',
		//           'script': '<?php echo $this->config->item('uploadify_path');?>upload_audios.php',
		//           'folder': '/<?php echo $this->config->item('sound_file_path');?>misc',
		//           'buttonText': 'Upload Sound',
		//           'multi': false,
		//           'fileExt': '*.mp3',
		//           'fileDesc': 'Audio Files',
		//           'sizeLimit': 1048576,
		//           'auto': true,
		//           'removeCompleted': true,
		//           'onProgress': function(event, ID, fileObj, data) {
		//             $(document)[0].title = 'BIP Admin Panel';
		//           },
		//           'onError': function(event, ID, fileObj, errorObj) {

		//           },
		//           'onSelect': function(event, ID, fileObj) {
		            
		//           },
		//           'onComplete': function(event, ID, fileObj, response, data) {
		//           	var rs = response.split("~~~~~");
		//             $(".uploaded-audio").find(".audio-icon-holder").html(rs[1]);
		//           }
		//       });




 var manualUploader = new qq.FineUploader({
        element: document.getElementById("uploader"),
         //multiple: false,
        //  validation: {
        //     acceptFiles: 'image/png',
        //     allowedExtensions: ['png'],
        //     //itemLimit: 5,
        //     //sizeLimit: 2097152,
        //     image: {
        //     	maxHeight: 128,
        //     	maxWidth: 128,
             	 
        // }
         //},
        request: {
            endpoint: '<?php echo $this->config->item('uploadify_path');?>upload_module_icon.php',
           params: {
                //folder: '<?php echo $this->config->item('uploadify_upload_path');?>module_icons',
            }

        },
        deleteFile: {
            enabled: false,
            // forceConfirm: true,
            // confirmMessage: 'Are you sure Dude ?',
            // endpoint: "php-traditional-server-master/endpoint.php",     
        },
        chunking: {
            enabled: true,
            concurrent: {
                enabled: true
            },
            success: {
                endpoint: "php-traditional-server-master/endpoint.php?done"
            }
        },
        resume: {
            enabled: true
        },
        retry: {
            enableAuto: true,
            showButton: true
        },
        callbacks:{
        	onComplete:function(id,name,responseJSON,xhr){ 
        		  
        		//alert(JSON.stringify(responseJSON));
        		if(responseJSON['success']==true)
        		{
				$(".uploaded-icon").find(".icon-holder").append(responseJSON['uuid']);
        		}
        		
        	}
        }

    });


 var manualUploaderAudio = new qq.FineUploader({
        element: document.getElementById("uploaderaudio"),
         multiple: false,
         validation: {
            acceptFiles: 'audio/mp3',
            allowedExtensions: ['mp3'],
            itemLimit: 1,
            sizeLimit: 1048576,
         },
        request: {
            endpoint: '<?php echo $this->config->item('uploadify_path');?>upload_audios.php',
           params: {
                folder: '<?php echo $this->config->item('sound_file_path');?>misc',
            }

        },
        deleteFile: {
            enabled: false,
            // forceConfirm: true,
            // confirmMessage: 'Are you sure Dude ?',
            // endpoint: "php-traditional-server-master/endpoint.php",     
        },
        chunking: {
            enabled: true,
            concurrent: {
                enabled: true
            },
            success: {
                endpoint: "php-traditional-server-master/endpoint.php?done"
            }
        },
        resume: {
            enabled: true
        },
        retry: {
            enableAuto: true,
            showButton: true
        },
        callbacks:{
        	onComplete:function(id,name,responseJSON,xhr){ 
        		  
        		//alert(JSON.stringify(responseJSON));
        		if(responseJSON['success']==true)
        		{
				$(".uploaded-audio").find(".audio-icon-holder").html(responseJSON['uuid']);
        		}
        		
        	}
        }

    });



});
 


function setIconForModule(filename){
	var targetElement = $("#hid-target-element").val();

	if($.trim(targetElement)==""){
		return false;
	}else{
		$("#"+targetElement).val(filename);
		$imagePath = '<?=base_url()?>images/uploads/module_icons/'+filename;

		$(".module-uploaded-icon").find(".icon-holder").html('<img class="thumbnail" src="'+$imagePath+'" style="margin-left:115px;margin-top:15px;display: block;" height="48" />');
		$.fancybox.close();
	}
}
</script>