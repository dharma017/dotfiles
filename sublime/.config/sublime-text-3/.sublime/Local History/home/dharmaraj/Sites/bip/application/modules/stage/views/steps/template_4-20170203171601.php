<?php $this->load->view("stage/steps/template_header".$display_type.".php");?>
<script language="javascript" src="<?php echo base_url(); ?>assets/public/js/inputjs.js"></script>
<!--end  of the head area-->
<!--begining of the content area-->
  <div id ="contentArea" class="imgMarginLeft pad10 row clear tmpl<?php echo $textPosition?> previewTemplate4">
<?php
					$data['commonTemplateData'] = $templateMediaData;
				 	echo $this->load->view('stage/parts/_section_text_position',$data,false);
				 ?>

    <?php
    if ($worksheet) {
	echo $worksheet;
    } else { ?>
        <div class="formentry">
            <form method="post" name="frmBip"  id="frmBip">
                <?php
                $userDataPost = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['data']"));

                $i = 0;
                foreach ($templateData as $rows) {
                    $i++;
                    $data = $userDataPost[$i - 1] ? $userDataPost[$i - 1] : $oldData[$rows->id];
                    echo '<p><label class="qsn">  ' . $rows->fld_label . '</label><br /><input type="hidden" name="fld_label[]" id="fld_label_' . $i . '" value="' . $rows->id . '" />';

                    if ($rows->fld_row == 1 || !$rows->fld_row) {
                        echo '<input placeholder="'.lang("txt_write_here").'" type="text" name="fld_data[]" id="fod_data_' . $i . '" class="inputs" value="' . $data . '" />';
                    } else if ($rows->fld_row > 1) {
                        echo '<textarea placeholder="'.lang("txt_write_here").'" name="fld_data[]" id="fld_data_' . $i . '" class="form_texarea" rows="' . $rows->fld_row . '"  />' . $data . '</textarea>';
                    }

                    echo '</p>';
                }
                ?>
                <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId ?>" />
                <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId ?>" />

            </form>
        </div>
        <?php } if ($sendButton == "1") { ?>
        <p class="clear" style=" padding-top:10px;">

        </p>
        <?php
    }

    if ($firstStep == "1" && $detailStart == "1")
        echo '<div style="clear:both; margin-top:15px;">' . $firstTemplateData . '</div>';
    ?>
</div>
<!--end of the content area-->

<!--begining of the footer area-->
<?php $this->load->view("stage/steps/template_footer" . $display_type . ".php") ?>
<p class="pagebreak"></p>
