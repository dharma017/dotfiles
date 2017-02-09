<?php $this->load->view("stage/steps/template_header".$display_type.".php");?>

<!--end  of the head area-->
<!--begining of the content area-->
  <div id ="contentArea" class="imgMarginLeft pad10 row clear template_13">
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

            $totalRows = count($templateData);
            switch ($totalRows) {
              case 3:
                  $countAplha = 'three';
                  break;
              case 4:
                  $countAplha = 'four';
                  break;
              case 5:
                  $countAplha = 'five';
                  break;
              case 6:
                  $countAplha = 'six';
                  break;

              default:

                break;
            }
            //echo $totalRows;
            //echo "$radioType";
            if($radioType == 1)
                $arrowClass = $countAplha.'-field-double';
            else
                $arrowClass ='';

            echo '<div  class="'.$countAplha.'-field '.$arrowClass.'">';
            if($totalRows >= 3 && $totalRows <= 6)
            {

              $formData = $this->stage_model->getTemplate13FromData($stepId);
              // echo "<pre>";print_r($formData);exit;

                $count = 1;
                foreach($templateData as $rows)
                {
                    // $data = $userDataPost[$i-1]?$userDataPost[$i-1]:$oldData[$rows->id];

                  $data = $formData[$count-1];


                    echo '<div style="clear:both"></div>';
                    echo '<input type="hidden" name="fld_label[]" id="fld_label_'.$count.'" value="'.$rows->id.'" />';

                    echo '<div class="textbox-wrapper '.$countAplha.'-field-'.$count.'">';
                    echo '<span class="input-caption">'.$rows->fld_label.'</span>';
                    echo '<span class="input-wrapper"><textarea placeholder="'.lang("txt_write_here").'" name="fld_data[]" id="fld_data_'.$count.'" class="textbox-background">'.$data.'</textarea></span>';
                    echo '</div>';
                    $count ++;
                }
            }
            echo '</div>';
        echo "</div>";
        echo "</form>";
        }
            ?>
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

<style type="text/css">
/*ul.form_dataEmail li{
	overflow: visible;
}
#containerFormData{
	position: relative;
	height: 485px;
}
#containerFormData .formentry{
	position: absolute;
	left: -50px;
}
#frmBip textarea{
	overflow: auto;
	overflow-x:hidden;
	resize:none!important;
	height: 57px;
	background:#FFF;
	box-shadow: inset 1px 2px 3px 1px #666;
	padding-top: 2px;
}
.textbox-wrapper{
	height: 100px;
}*/
</style>
