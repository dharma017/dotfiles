<?php $this->load->view("stage/admin/template_header.php")?>
<!--end  of the head area-->
<!--begining of the content area-->
<div id ="contentArea" class="  pad10 row clear previewTemplate10">
  <?php
  if($description)
    echo '<div class="clear wrapper600">'.$description.'</div>';
  echo '<h2 class="subheader">'.$templateData[0]->fld_name.'</h2>'
  ?>
  <div class="formentry">
    <form>
      <?php
      $count=1;
      foreach($templateData as $rows)
      {
        if($templateData[0]->textfield_prev !=1):
          echo '<p class="clear overflows"><label class="label_info" ><span class="spacings checkboxer" ><input name="" type="checkbox" value="" /></span> '.$rows->fld_label.'</label>';
        else :
          echo '<p><span  class="spacings"></span><label><input style="margin-right:5px;" name="" type="text" class="inputs" value="" /></label>';
        endif;
        echo '</p>';
        $count++;
      }
      ?>      </form>
    </div>
    <?php
    if($firstStep=="1" && $detailStart=="1")
      echo '<div style="clear:both; margin-top:15px;">'.$firstTemplateData.'</div>';
    ?>
  </div>
  <!--end of the content area-->
  </div>
  <!--begining of the footer area-->
  <?php $this->load->view("stage/admin/template_footer.php")?>