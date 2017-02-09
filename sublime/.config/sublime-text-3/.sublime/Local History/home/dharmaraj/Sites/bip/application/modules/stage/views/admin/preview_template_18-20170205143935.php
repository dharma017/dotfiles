<?php $this->load->view("stage/admin/template_header.php")?>
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
<div id ="contentArea" class="imgMarginLeft pad10 row clear tmpl<?php echo $textPosition?> previewTemplate18">

		<?php
					$data['commonTemplateData'] = $templateMediaData;
				 	echo $this->load->view('stage/parts/_section_text_position',$data,false);
				 ?>
    <div class="formentry11">
        <div id="stepLadder">
            <?php
            $count = count($templateData);
            $templateData = array_reverse($templateData);
            foreach($templateData as $rows)    {

                if(!empty($rows->fld_label))
                {
                    echo '<div class="textBoxABC textBoxABC-'.$count.'"><span class="roundClass">'.$count.'</span>';
                    echo '<input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$rows->id.'" />';
                    echo '<textarea placeholder="'.$rows->fld_label.'" name="fld_data[]" id="fld_data_'.$i.'" class="form_texarea" rows="3"  /></textarea>';
                    echo '</div>';
                }

                $count --;
            }
            ?>
        </div>
    </div>
    <?php
    if($firstStep=="1" && $detailStart=="1")
        echo '<div style="clear:both; margin-top:15px;">'.$firstTemplateData.'</div>';
    ?>

</div>
<!--end of the content area-->
<div class="clear"></div>
</div>
<?php $this->load->view("stage/admin/template_footer.php")?>
