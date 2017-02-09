<?php $this->load->view("stage/steps/template_header".$display_type.".php");?>
<!--end  of the head area-->
<!--begining of the content area-->
  <div id ="contentArea" class="imgMarginLeft pad10 row clear">
  <?php
if($description)
echo '<div class="clear wrapper600">'.$description.'</div><div style="clear:both"></div>';
?>
          <?php
    if ($worksheet) {
        echo $worksheet;
    } else {
        ?>
<form method="post" name="frmBip"  id="frmBip">
    <div class="formentry11">
            <?php
            $count = 1;
            $i = 0;
            $flag = true;
            if($templateData)
                foreach($templateData as $rows)
                {
                       // echo "<pre>";print_r($templateData);echo "</pre>";
                        if($flag)
                          {
                                $row_in_text_field = $rows->fld_row;
                                $row_in_working_sheet = $rows->fld_col;
                                $margin = $rows->fld_margin;
                                $flag = false;
                        }
                }


                if(!is_numeric($row_in_text_field) || !$row_in_text_field)
                        $row_in_text_field = 1;
                if(!is_numeric($row_in_working_sheet) || !$row_in_working_sheet)
                        $row_in_working_sheet = 1;


                /*
                echo '<div class="custom_template"><div class="title_txtarea1">'.$labelText1.'</div>
                <div class="title_txtarea2">'.$labelText2.'</div>
                <div class="title_txtarea3">'.$labelText3.'</div>';
                */


                $chunkedTemplateData = array_chunk($templateData, 100);

                $innerCount = 0;
                foreach($chunkedTemplateData as $key => $innerChunk){
                    echo '<div class="custom_template worksheet template_10 divider_'.$key.'"><div style="width:100%; float:left">';
                    foreach ($innerChunk as $key1 => $rows) {
                            $cnt = $key1+1;
                            // echo "first";
                            // echo $cnt;
                            $width = $rows->fld_width;
                            if ($key1 != 0) {
                            $user_margin = 'margin-left:'.$margin.'%';
                            }
                            echo '<div class="groupWrapper" style="width: '.$width.'%; '.$user_margin.'" ><div class="txt_area'.$cnt.' headline">'.$rows->fld_label.'</div>';


                            $innerCount++;
                            echo '</div>';
                    }

                    echo '</div><div style="width:100%; float:left">';

                $innerCount = 0;
                    foreach ($innerChunk as $key2 => $rows) {
                            $cnt = $key2+1;
                             //echo "second";

                            $width = $rows->fld_width;
                            $user_margin = ($key2==0) ? '': 'margin-left:'.$margin.'%;';
                            echo '<div class="groupWrapper" style="width: '.$width.'%;'.$user_margin.'" >';
                            echo '<div class="grp_txt_area">';
                            for($i = 0; $i < $row_in_working_sheet; $i++){
                                $tabindex= $i+1;
                                $count = $i*10+1+$innerCount;
                                echo '<input type="hidden" name="fld_label[]" id="fld_label_1_'.$count.'" value="'.$count.'" />';
                                echo '<div class="txt_area'.$count.'"><textarea name="fld_data[]" class="form_texarea" rows="'.$row_in_text_field.'" style="width:100%" tabindex = '.$tabindex.'></textarea></div>';
                            }
                            $innerCount++;
                            echo '</div></div>';
                    }
                    echo '</div></div>';
                }
         /*echo '<div class="custom_template worksheet template_10">';

            $chunkedTemplateData = array_chunk($templateData, 2);

            foreach($chunkedTemplateData as $innerChunk){
                echo '<div class="grp_txt_area">';
                foreach ($innerChunk as $key1 => $rows) {
                    for($i = 0; $i < $row_in_working_sheet; $i++){
                        $count = $i*10+1;
                        // $cnt = $key1+1;
                        echo '<div class="txt_area'.$count.'"><div class="txt_area'.$count.' headline">'.$rows->fld_label.'</div>';
                        echo '<input type="hidden" name="fld_label[]" id="fld_label_1_'.$count.'" value="'.$count.'" />';
                        echo '<div class="txt_area'.$count.'"><textarea name="fld_data[]" class="form_texarea" rows="'.$row_in_text_field.'" style="width:100%"></textarea></div>';
                    }
                }
                echo '</div>';
            }

            echo '</div>';*/





            ?>

    </div>
  </form>
      <?php  } ?>
       <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId?>" />
          <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId?>" />
       <?php
	  if($firstStep=="1" && $detailStart=="1")
	  echo '<div style="clear:both; margin-top:15px;">'.$firstTemplateData.'</div>';
	  ?>
  </div>

  <!--end of the content area-->

 <!--begining of the footer area-->
     <?php $this->load->view("stage/steps/template_footer".$display_type.".php")?>
<p class="pagebreak"></p>
