<?php $this->load->view("stage/steps/template_header" . $display_type . ".php"); ?>
<!--end  of the head area-->
<!--begining of the content area-->
<div id ="contentArea" class="imgMarginLeft pad10 row clear previewTemplate10">
    <?php
    if ($description)
        echo '<div class="clear wrapper600">' . $description . '</div>';
    echo '<h2 class="subheader">' . $templateData[0]->fld_name . '</h5>'
    ?>
        <?php
    if ($worksheet) {
        echo $worksheet;
    } else {
        ?>
    <form method="post" name="frmBip"  id="frmBip">
        <div class="formentry">

            <?php
            $userDataLabel = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['label']"));

            $userDataPost = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['data']"));
            //echo '<pre>';
            //print_r( $userDataLabel);
            //echo '</pre>';
            // 	echo '<pre>';
            //	print_r($oldData);
            //echo '</pre>';

            $count = 1;
            $i = 0;
            foreach ($templateData as $rows) {
                $i++;


                if ($templateData[0]->textfield_prev != 1):
                    //if($userDataLabel[$i-1])
                    //$data = $userDataLabel[$i-1]?$userDataLabel[$i-1]:$oldData[$rows->id];
                    //echo '------------'.'+++++++'.$rows->id;
                    if ($userDataLabel) {
                        for ($m = 0; $m <= count($userDataLabel); $m++) {
                            if ($rows->id == $userDataLabel[$m]) {
                                $data = true;
                                break;
                            }else
                                $data = false;
                        }
                    }
                    else {

                        if ($oldData) {
                            if (array_key_exists($rows->id, $oldData)) {
                                $data = true;
                            } else {
                                $data = false;
                            }
                        }
                    }


                    if ($data) {
                        $checked = "checked";
                        $disabled = '';
                    } else {
                        $checked = "";
                        $disabled = 'disabled="disabled"';
                    }

                    $checked = ($data == $rows->id) ? "checked" : "";
                    //echo '***'.$checked;
                    echo '<input type="hidden"  name="fld_label[]" id="fld_label_' . $i . '" ' . $disabled . ' value="' . $rows->id . '" />';
                    echo '<p style="margin-bottom:10px" class="overflows"><span class="spacings checkboxer"><input name="fld_data[]" id="fld_data' . $i . '" type="checkbox" ' . $checked . '  value="1"  onClick="return EnableCheckBox(this.id)" /></span><label class="label_info" for="fld_data' . $i . '">' . $rows->fld_label . '</label></p>';
                else :
                    $data = $userDataPost[$i - 1] ? $userDataPost[$i - 1] : $oldData[$rows->id];
                    echo '<input type="hidden"  name="fld_label[]" id="fld_label_' . $i . '" value="' . $rows->id . '" />';
                    echo '<p><input name="fld_data[]" id="fld_data' . $i . '" type="text" class="inputser" value="' . $data . '" /></p>';
                endif;

                $count++;
            }
            ?>     

            <!--begining of the footer area-->
            <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId ?>" />
            <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId ?>" />

    </form>
    <?php } ?>
</div>
</div>

            <?php if ($sendButton == "1") {
                ?>
    <p class="clear" style=" padding-top:10px;"> 

    </p>
<?php } ?> 
<?php
if ($firstStep == "1" && $detailStart == "1")
    echo '<div style="clear:both; margin-top:15px;">' . $firstTemplateData . '</div>';
?>
<!--end of the content area-->
<!--begining of the footer area-->
<?php $this->load->view("stage/steps/template_footer" . $display_type . ".php") ?>
</div>
<p class="pagebreak"></p>
<script>
    function EnableCheckBox(id_data){
        inputLabelId=id_data;
        LabelId=inputLabelId.replace('data','label_');
        if($('#'+id_data+':checked').val()==1){	
            document.getElementById(LabelId).disabled = false;
        }
        else
        {
            document.getElementById(LabelId).disabled = true;
        }
    }
</script>
<!--end of the content area-->


