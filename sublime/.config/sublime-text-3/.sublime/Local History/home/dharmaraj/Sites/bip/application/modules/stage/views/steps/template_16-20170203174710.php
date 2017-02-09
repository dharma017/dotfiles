<?php $this->load->view("stage/steps/template_header".$display_type.".php");?>
<!--end  of the head area-->
<!--begining of the content area-->
<form method="post" name="frmBip"  id="frmBip">
  <div id ="contentArea" class="imgMarginLeft pad10 row clear">
      <?php
    if($description)
      echo '<div class="clear wrapperfull">'.$description.'</div><div style="clear:both"></div>';

    if ($worksheet) {
        echo $worksheet;
    } else {
        ?>
    <div class="formentry16">
        <?php
        $count = 1;
        $i = 0;
        foreach($templateData as $rows){
            $data = $userDataPost[$i-1]?$userDataPost[$i-1]:$oldData[$rows->id];
                  if ($count =="1") echo '<div class="textOuter"><label for="">Barn</label>';
                  if ($count =="4") echo '<div class="textOuter"><label for="">Förälder</label>';
                    echo '<div class="textABC">';
                    echo '<p>'.$rows->fld_label.'</p>
                       <input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$rows->id.'" />
                        <textarea placeholder="'.lang("txt_write_here").'" name="fld_data[]" id="fld_data_'.$i.'"  class="textAreaABC" />'.$data.'</textarea>
                   </div>';
                  if ($count =="3" || $count =="6") echo '</div>';
            $count ++;
            $i++ ;
        }
        ?>
    </div>
    <script type="text/javascript">
    $(document).ready(function(){
        $('.formentry16').each(function(){
            var highestBox = 0;
            $(this).find('.textABC p').each(function(){
                if($(this).height() > highestBox){
                    highestBox = $(this).height();
                }
            });
            $(this).find('.textABC p').css({"height":highestBox});
            //$(this).find('.formentry16 label').css({"height":highestBox+98});
});
    });
</script>
    <style>
    /*.wrapperfull{width: 100%;}
    .wrapperfull p a{color:#0083C9;}
    .formentry16 label{
        width: 80px;
         font-family: "Trebuchet Ms", Arial;
         font-size: 13px;
        margin-top: 23px;
        float: left;
    }
    .formentry16 div.textOuter{
        float:left;
    }
    .formentry16 div.textABC{
        width:225px; float:left; padding-bottom: 10px;
    }
    .formentry16 div.textABC+div.textABC{padding-left:10px;}
     .formentry16 div.textABC textarea{width: 97%; padding:5px; border:0; height:80px;}
    .formentry16 div.textABC p{width: 100%; text-align: center; font-family: "Trebuchet Ms", Arial; font-size: 13px; float: left; }
    .formentry16{padding:15px 0 40px; float:left;}*/
</style>
    <?php  } ?>
       <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId?>" />
        <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId?>" />
       <?php
	  if($firstStep=="1" && $detailStart=="1")
	  echo '<div style="clear:both; margin-top:15px;">'.$firstTemplateData.'</div>';
	  ?>
  </div>
  </form>

 <!--begining of the footer area-->
     <?php $this->load->view("stage/steps/template_footer".$display_type.".php")?>
<p class="pagebreak"></p>
