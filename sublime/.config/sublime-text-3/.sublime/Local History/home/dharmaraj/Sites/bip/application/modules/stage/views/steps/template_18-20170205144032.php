<?php $this->load->view("stage/steps/template_header".$display_type.".php");?>
<script language="javascript" src="<?php echo base_url(); ?>assets/public/js/inputjs.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
  $('[placeholder]').focus(function() {
      var input = $(this);
      if (input.val() == input.attr('placeholder')) {
        input.val('');
        input.removeClass('placeholder');
      }
    }).blur(function() {
      var input = $(this);
      if (input.val() == '' || input.val() == input.attr('placeholder')) {
        input.addClass('placeholder');
        input.val(input.attr('placeholder'));
      }
    }).blur().parents('form').submit(function() {
      $(this).find('[placeholder]').each(function() {
        var input = $(this);
        if (input.val() == input.attr('placeholder')) {
          input.val('');
        }
      })
    });
})
</script>
<!--end  of the head area-->
<!--begining of the content area-->
  <div id ="contentArea" class="imgMarginLeft pad10 row clear previewTemplate18 tmpl<?php echo $textPosition?>">
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
    <div id="stepLadder">
            <?php
            $count = count($templateData);
            $i = count($templateData);
            $templateData = array_reverse($templateData);
            foreach($templateData as $rows)    {

            $dataVal = $userDataPost[$i-1]?$userDataPost[$i-1]:$oldData[$rows->id];

  					if(!empty($rows->fld_label))
  					{
  						echo '<div class="textBoxABC textBoxABC-'.$count.'"><span class="roundClass">'.$count.'</span>';
  						echo '<input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$rows->id.'" />';
  						echo '<textarea placeholder="'.$rows->fld_label.'" name="fld_data[]" id="fld_data_'.$i.'" class="form_texarea" rows="3"  />'.$dataVal.'</textarea>';
  						echo '</div>';
  					}


                $count--;
			$i-- ;
            }

            ?>

    </div>
    </div>
       <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId?>" />
          <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId?>" />
  </form>
      <?php  } ?>
       <?php
	  if($firstStep=="1" && $detailStart=="1")
	  echo '<div style="clear:both; margin-top:15px;">'.$firstTemplateData.'</div>';
	  ?>
  </div>

  <!--end of the content area-->
 <!--begining of the footer area-->
<?php $this->load->view("stage/steps/template_footer".$display_type.".php")?>
<p class="pagebreak"></p>
