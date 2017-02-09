<?php
if ($commonTemplateData) {

	if ($textPosition == "2") // top text
	    echo '<div class="wrapper600">' . $description . '</div>';

	if (trim($description) && $textPosition == "1") $noWrapper = "noWrapper";

	if ($textPosition == "1") //left text
        echo '<div class="textwrapp '.$noWrapper.'">';
?>

<div id="rightschek" <?php if (trim($description) && $textPosition == "1") echo 'class="mediaTopofText"'; ?>>
    <?php
    if (!empty($commonTemplateData[0]->media)) {
	    $img_filename = $this->config->item('uploadify_upload_path') . "media/" . $commonTemplateData[0]->media;
			$img_dimension = @getimagesize($img_filename);
			if ($img_dimension[0] < 455) {
				$imageName =$img_filename;
			}else{
				$imageName =base_url().'resizer.php?src='.$img_filename.'&w=455&zc=0';
			}
    }else{
    	$imageName = '';
    }

    $video = base_url() . 'images/uploads/media/video' . $commonTemplateData[0]->media;

    if(!empty($commonTemplateData[0]->embed_video)){ // show embed video if available.

        $lftCls='yframe';
        $video_url=parse_video($commonTemplateData[0]->embed_video);

        if ($this->uri->segment(2) == "pdfVersion" && !empty($commonTemplateData[0]->print_image)) {
            echo '<img  class="" alt="Video File" src="' . base_url() . 'resize.php?img=images/uploads/print_images/' . $commonTemplateData[0]->print_image . '&w=455&h=280" style="border:1px #fff solid">';
        }else{
             if ($textPosition==1) {
                echo embed_video($commonTemplateData[0]->embed_video,$video_url,455,255);
            }else{
                echo embed_video($commonTemplateData[0]->embed_video,$video_url,785,435);
            }
        }

		    }elseif(!empty($commonTemplateData[0]->html5_video)) {
		    	$iframeWidth = ($textPosition==1) ? '455px': '785px';
		       echo '<div style="width:'.$iframeWidth.'">
		       <iframe style="visibility:hidden;" onload="setIframeHeight(this);" src="'.getHtml5Video($commonTemplateData[0]->html5_video).'">></iframe>
		       </div>';

		    }elseif($commonTemplateData[0]->media_type=='video') {

		        $video = base_url() . 'images/uploads/media/video/' . $commonTemplateData[0]->media;
		        $thumb = end(explode('.', $commonTemplateData[0]->media));

        echo '  ';

        if ($thumb == "swf") {// if swf files

				            $video_dimension = @getimagesize($video);
				            $width = $video_dimension[0];
				  $height = $video_dimension[1];
				            if ($width > 782 || !$width)
				  {
				    $height = @ceil($height*782/$width);
				                $width = "782";
				  }


            if ($this->uri->segment(2) != "pdfVersion") :
                ?>
                <div  class="videowrappers1 swffile <?php if ($textPosition == "1")
                    echo ' margin_left'; ?>">
                    <script src="<?php echo base_url() ?>Scripts/swfobject_modified.js" type="text/javascript"></script>
                    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="<?php echo $width ?>" height="<?php echo $height; ?>" id="FlashID" title="bip">
                        <param name="movie" value="<?php echo $video; ?>">
                        <param name="quality" value="high">
                        <param name="wmode" value="transparent">
                        <param name="swfversion" value="8.0.35.0">
                        <!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don't want users to see the prompt. -->
                        <param name="expressinstall" value="<?php echo base_url() ?>Scripts/expressInstall.swf">
                        <!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
                        <!--[if !IE]>-->
                        <object type="application/x-shockwave-flash" data="<?php echo $video; ?>" width="<?php echo $width ?>" height="<?php echo $height; ?>">
                            <!--<![endif]-->
                            <param name="quality" value="high">
                            <param name="wmode" value="transparent">
                            <param name="swfversion" value="8.0.35.0">
                            <param name="expressinstall" value="<?php echo base_url() ?>Scripts/expressInstall.swf">
                            <!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
                            <div>
                                <h4>Content on this page requires a newer version of Adobe Flash Player.</h4>
                                <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" width="112" height="33" /></a></p>
                            </div>
                            <!--[if !IE]>-->
                        </object>
                        <!--<![endif]-->
                    </object>
                    <script type="text/javascript">
                        swfobject.registerObject("FlashID");
								        swfobject.addLoadEvent(function(){
								          $('.videowrappers1').removeClass('swffile');
								        });
                    </script>
                </div>
                <?php
            else:
                if ($this->uri->segment(2) == "pdfVersion" && !empty($commonTemplateData[0]->print_image)) {
                    echo '<img  class="" alt="Video File" src="' . base_url() . 'resize.php?img=images/uploads/print_images/' . $commonTemplateData[0]->print_image . '&w=455&h=280" style="border:1px #fff solid">';
                }else{?>
                    <div style="width:<?php echo $width ?>; height:<?php echo $height; ?>;" class=" <?php if ($textPosition == "1")
                    echo ' margin_left'; ?>"> <img src="<?php echo base_url() ?>images/no_swf_file.png"> </div>
                <?php }
            endif;
        }
        else { // if video files
            if (strpos(strtolower($commonTemplateData[0]->media), '.mp3') == true) {
                $thumb = 'thumbaudio.jpg';
                ?>
                <div style="text-align:center" class="videowrappers1 <?php if ($textPosition == "1")
            echo ' margin_left'; ?>">
                    <?php if ($this->uri->segment(2) != "pdfVersion") : ?>
                        <object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="455" height="24">
                            <param name="movie" value="<?php echo base_url() . 'assets/player/player.swf'; ?>" />
                            <param name="allowfullscreen" value="true" />
                            <param name="allowscriptaccess" value="always" />
                            <param name="flashvars" value="file=<?php echo base_url() . 'images/uploads/media/video/' . $commonTemplateData[0]->media; ?>&image=<?php echo base_url() . 'images/uploads/thumb/' . $thumb; ?>" />
                            <embed
                                type="application/x-shockwave-flash"
                                id="player2"
                                name="player2"
                                src="<?php echo base_url() . 'assets/player/player.swf'; ?>"
                                width="455"
                                height="24"
                                allowscriptaccess="always"
                                allowfullscreen="true"
                                flashvars="file=<?php echo base_url() . 'images/uploads/media/video/' . $commonTemplateData[0]->media; ?>&image=<?php echo base_url() . 'images/uploads/thumb/' . $thumb; ?>"
                                />
                        </object>
                        <?php
                    else:
                        if ($this->uri->segment(2) == "pdfVersion" && !empty($commonTemplateData[0]->print_image)) {
                        echo '<img  class="" alt="Video File" src="' . base_url() . 'resize.php?img=images/uploads/print_images/' . $commonTemplateData[0]->print_image . '&w=455&h=280" style="border:1px #fff solid">';
                        }else{
                        $thumb_img = base_url() . 'images/uploads/media/video/' . $commonTemplateData[0]->media . '&image=' . base_url() . 'images/uploads/thumb/' . $thumb;
                            echo '<img alt="Audio File" src="' . $thumg_img . '">';
                        }
                    endif;
                    ?>
                </div>
                <?php
            }
            else {
                $thumb = $commonTemplateData[0]->image_from_video;
                ?>

                <?php if ($this->uri->segment(2) != "pdfVersion") : ?>
                    <div style="text-align:center" class="videowrappers <?php if ($textPosition == "1")
                echo ' margin_left'; ?>">
                        <object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="455" height="280">
                            <param name="movie" value="<?php echo base_url() . 'assets/player/player.swf'; ?>" />
                            <param name="allowfullscreen" value="true" />
                            <param name="allowscriptaccess" value="always" />
                            <param name="flashvars" value="file=<?php echo base_url() . 'images/uploads/media/video/' . $commonTemplateData[0]->media; ?>&image=<?php echo base_url() . 'images/uploads/thumb/' . $thumb; ?>" />
                            <embed
                                type="application/x-shockwave-flash"
                                id="player2"
                                name="player2"
                                src="<?php echo base_url() . 'assets/player/player.swf'; ?>"
                                width="455"
                                height="280"
                                allowscriptaccess="always"
                                allowfullscreen="true"
                                flashvars="file=<?php echo base_url() . 'images/uploads/media/video/' . $commonTemplateData[0]->media; ?>&image=<?php echo base_url() . 'images/uploads/thumb/' . $thumb; ?>"
                                />
                        </object>
                    </div>
                    <?php
                else:
                    $thumbs = 'images/uploads/thumb/' . $thumb;
                    if (file_exists(base_url().$thumbs)) {
                        echo '<img alt="Video File" src="' . base_url() . 'resize.php?img=' . $thumbs . '&w=455&h=280" style="border:1px #fff solid">';
                    }else{
                        if (!empty($commonTemplateData[0]->print_image)) {
                            echo '<img  class="" alt="Video File" src="' . base_url() . 'resize.php?img=images/uploads/print_images/' . $commonTemplateData[0]->print_image . '&w=455&h=280" style="border:1px #fff solid">';
                        }
                    }
                endif;
                ?>

                <?php
            }
        } // end of else flv files
    } // end of if video
    else { // if image section
        echo '<div class="imgWrapper">';
        if (!empty($imageName)) {
	       echo '<img style="text-align:center" ' . $imgStyle . ' src="' . $imageName . '">';
        }
        echo '</div>';
    }

    if ($textPosition == "3") //bottom text
        echo '<div class="clear wrapper600">' . $description.'</div>';

		echo '</div> <!--close div of right side-->';

    if ($textPosition == "1") //left text
        echo $description.'</div>';


} // end of if template data
else // no media only text case
    echo '<div class="wrapper600">' . $description . '</div>';
