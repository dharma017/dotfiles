<?php
/**
 * LADDER TEMPLATE
 *
 *
 */
$this->load->view("stage/steps/template_header" . $display_type . ".php") ?>

<?php

$theme_img_path = base_url().'images/';
if ($skin_id==3) {
	$theme_img_path = base_url().'images/teenager/';
}
 ?>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/shared/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />


<!--end  of the head area-->
<form action="#" name="frmBip" id="frmBip">
    <!--begining of the content area-->
    <div id ="contentArea" class="imgMarginLeft pad10 row clear  previewTemplate6">
        <?php
        $userDataPost = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['data']"));
        if ($description)
            echo '<div class="clear wrapper600">' . $description . '</div>';
        ?>
        <?php
        if ($worksheet) {
            echo $worksheet;
        } else {
            ?>
            <div class="formentry">
                <div class="childrating">
                    <div  class="col1" id="ladder">
                        <!-- <div id="ladder" style="text-align:center;padding-top:125px;">-->
                        <div id="selected_ladder">

                            <?php
                            if ($userDataPost)
                                $old_ladder = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['ladder']"));
                            elseif ($oldData)
                                $old_ladder = $oldData["ladder"];
                            else
                                $old_ladder = "1";
                            ?>

                            <img onclick="chooseLadder()" alt="ladder 1" src="<?php echo $theme_img_path.'ladder-'.$old_ladder; ?>.png">
                            <input type="hidden" value="1" name="ladder">
                            <br>


                        </div>
                        <?php if ($this->uri->segment(2) != "pdfVersion"): ?>
                            <div id="choose_ladders" style="margin-top:10px; text-align:center" >

                                <a href="<?php echo site_url("stage/chooseLadder"); ?>" class="fancybox btnSmallall " style=" text-align:center"><?= lang('select_ladder') ?></a>
                                <br/></div>
                        <?php endif; ?>


                    </div>
                      <!-- <div id="ladder" class="col1"> <img src="<?php echo base_url() ?>/images/ladder-<?php echo $reference; ?>.png" width="93" height="323" alt="ladder" /></div>-->
                    <div id="rating" class="col2">
                        <?php
                        $userDataPost = $this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['data']");
                        $count = 0;



                        for ($i = 100; $i >= 10; $i = $i - 10) {
                            $data = $userDataPost[$count] ? $userDataPost[$count] : $oldData[$i];
                            //  $data = $oldData[$i];
                            echo ' <p>';
                            if($raw_choosen_step == 1){
										  echo '<label class="number">' . $i/10 .'</label>';
                                        }else{
                                          echo '<label class="number">' . $i . '</label>';  
                                        }
                                             echo'<input type="hidden" value="' . $i . '" name="fld_label[]" />
													  			<textarea placeholder="'.lang("txt_write_here").'" name="fld_data[]" style="float:left; width:460px; height:30px;" class="inputs">' . $data . '</textarea>
										              <label class="childs' . $i . '"></label>
										            </p>';
                            $count++;
                        }
                        ?>

                        <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId ?>" />
                        <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId ?>" />

                    </div>
                </div>

            </div>
        <?php } ?>
    </div>
    <!--end of the content area-->
</form>
<!--begining of the footer area-->
<?php $this->load->view("stage/steps/template_footer" . $display_type . ".php") ?>
<script language="javascript">
    $(".fancybox").fancybox({
        'width'				: '6',
        'height'			: '4',
        'autoScale'			: true,
        'transitionIn'		: 'elastic',
        'transitionOut'		: 'elastic',
        'type'				: 'iframe'
    });
</script>
<?php
$SESS_LADDER = $this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['ladder']");

if (!($SESS_LADDER) && $display_type != "pdf") { //echo "new";
    ?>
    <script language="javascript">
        $(document).ready(function() {
            $.fancybox({
                'href' 				: '<?php echo site_url("stage/chooseLadder"); ?>',
                'width'				: '6',
                'height'			: '4',
                'autoScale'			: false,
                'transitionIn'		: 'elastic',
                'transitionOut'		: 'elastic',
                'type'				: 'iframe'
            });
        });
    </script>
<?php
} else {
    //echo "old";
    ?>

    <script language="javascript">
    		var theme_img_path = "<?php echo $theme_img_path; ?>";
        $(document).ready(function() {
            $("#choose_ladder").show();
            $("#ladder").css({'background':'none','width':'112px','height':'auto','padding':'0'});
            $("#selected_ladder").html('<img src="'+theme_img_path+'ladder-'+<?php echo $SESS_LADDER ?>+'.png" alt="ladder" onClick="chooseLadder()"    /><input type="hidden" name="ladder" value="'+<?php echo $SESS_LADDER ?>+'" /><Br/>');
        });
    </script>
<?php } ?>
<p class="pagebreak"></p>
