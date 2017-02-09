<?php $this->load->view("stage/admin/template_header.php")?>
<script language="javascript" src="<?php echo base_url(); ?>assets/public/js/inputjs.js"></script>
<!--end  of the head area-->
<div id ="contentArea" class="imgMarginLeft pad10 row clear tmpl<?php echo $textPosition?> previewTemplate11">
 <?php
					$data['commonTemplateData'] = $templateMediaData;
				 	echo $this->load->view('stage/parts/_section_text_position',$data,false);
				 ?>
    <div class="formentry11">
        <form>
            <?php
            $count = 1;
            foreach($templateData as $rows)    {
                if($rows->fld_abc_top=="1"){
                    echo
                    '<div class="textPanelABC">
                    <p>'.$rows->fld_label.'</p>
                    <input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$rows->id.'" />
                    <textarea name="fld_data[]" id="fld_data_'.$i.'"  class="textAreaABC" /></textarea>
                </div>';
                if ($count < 3)
                    echo '<div class="arrowABC" style="float:left;width:28px;position:relative;"><img src="' . base_url() . '/assets/public/css/images/arrow_abc.png" style="position:absolute;bottom:26px;"></div>';
                if ($count == "3")
                    echo "<div style='clear:both'></div><br>";
            }
            else {
                if(!empty($rows->fld_label))
                {
                    echo '<div class="textBoxABC"><p>'.$rows->fld_label.'</p>';
                    if($rows->fld_row==1 || !$rows->fld_row)
                    {
                        echo '<input type="text"  style="width:762px" />';
                    }
                    else if($rows->fld_row>1)
                    {
                        echo '<textarea  style="width:763px"  rows="'.$rows->fld_row.'"></textarea>';
                    }
                    echo '</div>';
                }
            }
            $count ++;
        }
        ?>
    </form>
</div>
<?php
if($firstStep=="1" && $detailStart=="1")
    echo '<div style="clear:both; margin-top:15px;">'.$firstTemplateData.'</div>';
?>

</div>
</div>
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
<style type="text/css">
    .arrowABC img{
        margin-left:0!important;
    }
</style>

<!--begining of the footer area-->
<?php $this->load->view("stage/admin/template_footer.php")?>
