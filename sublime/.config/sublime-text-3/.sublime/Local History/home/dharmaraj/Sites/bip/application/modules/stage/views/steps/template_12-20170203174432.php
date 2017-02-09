<?php
/*
 * Template : Ladder Follow up Template [User section]
 * Created on :
 * Last Updated :
 * Template Type : Form
 * Created By :  Web Search Professional.
 */
$this->load->view("stage/steps/template_header" . $display_type . ".php")
?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/shared/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/shared/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<!--end  of the head area-->
    <!--begining of the content area-->

    <div id ="contentArea" class="imgMarginLeft pad10 row clear previewTemplate12">
        <?php
        $userDataPost = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['data']"));
        if ($description)
            echo '<div class="clear wrapper600">' . $description . '</div>';
        if ($templateData->message) {

            if ($worksheet) {
                echo $worksheet;
            } else {
                ?>
	<form action="#" name="frmBip" id="frmBip">
                <div class="formentry">
                    <div class="childrating">
                        <div  class="col1" id="ladder">
                            <!-- <div id="ladder" style="text-align:center;padding-top:125px;">-->
                            <div id="selected_ladder">
                                <?php
                                 $templateMessage = $this->encryption->decrypt($templateData->message);
			                	$templateMessage = stripslashes($templateMessage);
			                    $data = json_decode($templateMessage);
                                // $data = json_decode($templateData->message);
                                $old_ladder = $data->ladder;

                                if ($old_ladder):
                                    echo ' <img  alt="ladder 1" src="' . base_url() . '/images/ladder-' . $old_ladder . '.png">';
                                endif;
                                ?>
                                <input type="hidden" value="1" name="ladder">
                                <br>
                            </div>
                        </div>
                          <!-- <div id="ladder" class="col1"> <img src="<?php echo base_url() ?>/images/ladder-<?php echo $reference; ?>.png" width="93" height="323" alt="ladder" /></div>-->
                        <div id="rating" class="col2">

                            <!-- to assigned source template worksheet id as reference for the followup template -->
                            <input type="hidden" value="reference" name="fld_label[]" />
                            <input type="hidden" value="<?php echo $templateData->id; ?>" name="fld_data[]" />

                            <?php
                            $userDataPost = $this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['data']");
                            $count = 0;

                            for ($i = 100; $i >= 10; $i = $i - 10) {

                                echo '
                            <p>
                                <label class="number_dim">' . $i . '</label>
                                <input type="hidden" value="' . $i . '" name="fld_label[]" />
                                <textarea disabled="disabled" readonly="readonly">' . $data->$i . '</textarea>
                                <input type="text" class="small_text inputs" name="fld_data[]" value="' . $userDataPost[$count] . '" />
                            </p>';
                                $count++;
                            }
                            ?>
                        </div>
                    </div>

                </div>
	</form>
                <?php
            }
        } else {
            echo "<div class='formentry1'>".lang('ladder_source_not_found')."</div>";
        }
        ?>
    </div>
    <!--end of the content area-->
    <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId ?>" />
    <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId ?>" />


<!--begining of the footer area-->
<?php
if ($display_type == "pdf") {
    echo '<p>&nbsp;</p><div style="line-height:20px;"><i>'.lang('no_fill_no_dispaly').'</i></div>';
}

$this->load->view("stage/steps/template_footer" . $display_type . ".php")
?>

<p class="pagebreak"></p>
