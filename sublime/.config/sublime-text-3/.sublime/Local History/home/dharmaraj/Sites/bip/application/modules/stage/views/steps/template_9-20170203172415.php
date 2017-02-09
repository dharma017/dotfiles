<?php
/*
 *  TEMPLATE : GOAL TEMPLATE
  REFERENCE TEMPLATE :
 */
$this->load->view("stage/steps/template_header" . $display_type . ".php");
?>
<script language="javascript" src="<?php echo base_url(); ?>assets/public/js/inputjs.js"></script>
<!--end  of the head area-->

<!--begining of the content area-->
<div id ="contentArea" class="imgMarginLeft pad10 row clear previewTemplate9">
    <?php
    if ($description)
        echo '<div class="clear wrapper600">' . $description . '</div>';

    if ($worksheet) {
        echo $worksheet;
    } else {
        ?>
        <div class="formentry1">
            <form method="post" name="frmBip"  id="frmBip" class="formstylish">

                <!-- to assigned source template worksheet id as reference for the goal template -->
                <input type="hidden" name="fld_label[]" value="reference" />
                <input type="hidden" name="fld_data_0"  value="<?php echo $templateData->id ?>" />
                <?php
                $userDataPost = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['data']"));
                $count = 1;


                if ($templateData->message) {
                    $templateMessage = $this->encryption->decrypt($templateData->message);
                	$templateMessage = stripslashes($templateMessage);
                    $templateData = json_decode($templateMessage);

                    foreach ($templateData as $key => $value) {
                        if(!in_array($key,$selectedQuestion)) continue;
                        $userData = $userDataPost[$count] ? $userDataPost[$count] : $oldData[$key];

                        //$userData = $userDataPost[$count];
                        $value_arr = explode("~||~", $userData);
                        if (is_array($value_arr)) {
                            $data = $value_arr[0];
                            $comment = $value_arr[1];
                        } else {
                            $data = $userData;
                        }

                        //if(in_array($key,$selectedQuestion)) {
                        $fld_num_option = $rows->fld_num_option;
                        if (!$fld_num_option)
                            $fld_num_option = "8";
                        echo
                        '<div class="sectionsdivison">
                    <div class="curvestoppart">	</div>
                    <div class="curvesMiddlepart">

                    <label class="title">' . $value . '</label>
                    <input type="hidden" name="fld_label[]" value="' . $key . '"></label>
                    <ul>';

                        for ($i = 1; $i <= $fld_num_option; $i++) {
                            if ($i == $data)
                                $class = "link_head active";
                            else
                                $class = "link_head";

                            echo ' <li class="li_' . $count . '">
                                    <a class="' . $class . '" href="#anchor_' . $count . '" id="' . $i . '" title="' . $count . '">' . $i . '</a>
                            </li>
                       ';
                        }

                        echo '</ul>';
                        if ($commentBox == "1")
                            echo '<div class="clear" style="padding-top:10px;"></div><i>Kommentar <input style="width:500px" class="inputs" name="comment_' . $count . '" value="' . $comment . '" />';

                        echo '<input type="hidden" name="fld_data_' . $count . '" id="fld_data_' . $count . '" value="' . $data . '">';
                        echo '</div></div>';

                        $count++;
                    }
                }

                else {
                    echo lang('no_goals_formulated');
                }
                ?>

                <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId ?>" />
                <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId ?>" />
            </form>
        </div>
<?php } ?>
    <p class="clear">

    </p>
    <?php
    if ($firstStep == "1" && $detailStart == "1")
        echo '<div style="clear:both; margin-top:15px;">' . $firstTemplateData . '</div>';
    ?>
</div>
<!--end of the content area-->
<script language="javascript">
    $('.link_head').click(function() {
          if ($('#form_editable').val()==0) return false;
        var count = $(this).attr("title");
        $('.li_'+count+' a').removeClass("active");
        $(this).addClass("active");
        var values = $(this).attr("id");

        $('#fld_data_'+count).val(values);

    });
</script>

<!--begining of the footer area-->
<?php
if ($display_type == "pdf") {
    echo '<p>&nbsp;</p><div style="line-height:20px;"><i>'.lang('no_goal_no_page').'</i></div>';
}
$this->load->view("stage/steps/template_footer" . $display_type . ".php")
?>
<p class="pagebreak"></p>
