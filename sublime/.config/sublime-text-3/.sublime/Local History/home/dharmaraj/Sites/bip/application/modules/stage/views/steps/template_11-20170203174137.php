<?php $this->load->view("stage/steps/template_header".$display_type.".php");?>
<script language="javascript" src="<?php echo base_url(); ?>assets/public/js/inputjs.js"></script>
<!--end  of the head area-->
<!--begining of the content area-->
  <div id ="contentArea" class="imgMarginLeft pad10 row clear tmpl<?php echo $textPosition?> previewTemplate11">

 <?php
					$data['commonTemplateData'] = $templateMediaData;
				 	echo $this->load->view('stage/parts/_section_text_position',$data,false);
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
            foreach($templateData as $rows)    {
                //echo "<pre>";print_r($templateData);echo "</pre>";
                $dataVal = $userDataPost[$i-1]?$userDataPost[$i-1]:$oldData[$rows->id];
                if($rows->fld_abc_top=="1"){
                      echo
                      '<div class="textPanelABC">
                        <p>'.$rows->fld_label.'</p>
                           <input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$rows->id.'" />
                        <textarea placeholder="'.lang("txt_write_here").'" name="fld_data[]" id="fld_data_'.$i.'"  class="textAreaABC" />'.$dataVal.'</textarea>

                       </div>';
                     if($count<3)
                              echo '<div class="arrowABC" style="float:left;width:28px;position:relative;"><img class="templateEightImg" src="'.base_url().'/assets/public/css/images/arrow_abc.png" style="position:absolute;bottom:14px;"></div>';
                        if($count=="3") echo "<div style='clear:both'></div><br />";
                }
                else {
          if(!empty($rows->fld_label))
          {
            echo '<div class="textBoxABC"><p>'.$rows->fld_label.'</p>
              <input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$rows->id.'" />';
            if($rows->fld_row==1 || !$rows->fld_row)
            {
               echo '<input placeholder="'.lang("txt_write_here").'" name="fld_data[]"  style="width:762px" id="fld_data_'.$i.'" />';
            }
            else if($rows->fld_row>1)
            {
               echo '<textarea placeholder="'.lang("txt_write_here").'" name="fld_data[]" style="width:762px"  id="fld_data_'.$i.'" class="form_texarea" rows="'.$rows->fld_row.'"  />'.$dataVal.'</textarea>';
            }

            echo '</div>';
          }
                }
                $count ++;
$i++ ;
            }

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
   <script type="text/javascript">
  $(document).ready(function(){

    $('.formentry11').each(function(){
        var highestBox = 0;

        $(this).find('.textPanelABC p').each(function(){
            if($(this).height() > highestBox){
                highestBox = $(this).height();
            }
        });

        $(this).find('.textPanelABC p').css({"height":highestBox});
        $(this).find('.arrowABC').css({"height":highestBox+98});
    });
});
  </script>

 <!--begining of the footer area-->
     <?php $this->load->view("stage/steps/template_footer".$display_type.".php")?>
<p class="pagebreak"></p>
