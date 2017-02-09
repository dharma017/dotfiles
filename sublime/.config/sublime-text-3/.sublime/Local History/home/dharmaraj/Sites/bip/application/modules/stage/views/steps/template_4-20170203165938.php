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
    		echo '<form method="post" name="frmBip"  id="frmBip">';
        echo $worksheet;
    } else {
        ?>
        <div class="formentry">
            <form method="post" name="frmBip"  id="frmBip">
            <!-- <form method="post" name="frmBip"  id="frmBip"> -->

                <?php
                //$userLabelPost = ($this->session->userdata("SESS_USER_DATA[$stageId][$stepId]['label']"));
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

            <!-- </form> -->
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
 <style type="text/css">
    /*#contentArea{margin-bottom: 0}
    #footer{padding-top: 25px;}
    #leftpanels.yframe{padding-left: 35px;}
    #contentArea.tmpl2 .wrapper600{padding-bottom: 20px; display:inline-block;}
    #contentArea.tmpl3 .wrapper600{padding-bottom: 0px; padding-top: 20px; display:inline-block;}
    .imgMarginLeft.tmpl2 img,.imgMarginLeft.tmpl3 img{margin-left:0!important;}
    .textwrapp{display: table; padding-right: 25px;}
    .tmpl1+.textwrapp{padding-left: 35px;}
    .tmpl3{padding-top: 6px;}
    #rightschek iframe{border: 2px solid white;}
    #contentArea +.textwrapp p{
        float: left;
    }
 .form_texarea {
    padding: 5px;
    width: 615px;
    margin-top: 5px;
}
.formentry p input.inputs {
    height: 30px;
    margin-top: 5px;
    padding: 0 5px;
    width: 615px;
}
*/
</style>
