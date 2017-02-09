<script type="text/javascript">
    function addFormFields() {
  
        var field_id = document.getElementById("field_id").value,        
            id = (field_id*1)+1,
            placeholderId = id-1;


        if (id>5) {
            alert('Max 5 allowed');return false;
        }

        var placeholder = $('#default_lbl'+placeholderId).val();

        $("#container_fields").append('<div class="box_form"  id="row' + id + '" style="display:block;"><ul  class="lists" style="clear:both"><li><label><strong>Label Text:</strong></label> <textarea name="fldLabel[]" id="fldLabel' + id + '"  placeholder="'+placeholder+'" class="texts" rows="1" cols="50" /></textarea></li></ul><a href="#" onClick="removeFormFieldLabel(\'#row' + id + '\'); return false;" class="remover">Remove</a></div>');

        document.getElementById("field_id").value = id;
    }

    function removeFormFieldLabel(id) {

        var field_id = document.getElementById("field_id").value;
        if(field_id>1)
        {
            field_id = (field_id - 1);
            document.getElementById("field_id").value = field_id;
            $(id).remove();
            $(id).fadeOut();
    
        }
  
    }
</script>
<?php 
$default_lbl = array(
'Skriv första steget här.',
'Skriv andra steget här.',
'Skriv tredje steget här.',
'Skriv fjärde steget här.',
'Skriv femte steget här.'
    );
 ?>
<div class="box box-100">     
    <div class="boxin">
        <div class="header">
            <h3><?php echo $templateHeading; ?> </h3>
        </div>
        <form name="frmAddStep" id="frmAddStep" method="post">
            <?php $this->load->view("stage/admin/template_common_form"); ?>
        </ul>

        <?php $this->load->view("stage/parts/_parts_common_video_18");?>

         <div style="clear:both"></div>
        
            <div id="container_fields">
                <?php for ($j=0; $j < count($default_lbl); $j++) {  ?>
                    <input type="hidden" name="default_lbl[]" id="default_lbl<?php echo $j;?>" value="<?php echo $default_lbl[$j] ?>">
                <?php } ?>

                <input type="hidden" id="field_id" value="<?php if ($task == "editStep")
                echo count($templateFormData18); else
                echo '1'; ?>">

                <?php
                if ($task == "editStep") {
                    $count = 0;
                    foreach ($templateFormData18 as $key => $rows) {
                        $placeholder = $default_lbl[$count];
                        $checked = ($rows->status==1) ? 'checked="checked"': '';
                        $count++;                        
                         ?>
                        <div class="box_form"  id="row<?php echo $count; ?>">

                                <ul class="lists">
                                    <li>
                                        <label><strong>Label Text:</strong></label>
                                        <textarea name="fldLabel[]" placeholder="<?php echo $placeholder; ?>" id="fldLabel<?php echo $count; ?>" class="texts" rows="1" cols="50"><?php echo $rows->fld_label; ?></textarea>
                                    </li>    
                                    <li>
                                        <label><strong>Show/Hide:</strong></label>
                                        <input type="hidden" name="fldStatus[<?php echo $key ?>]" value="0">
                                        <input type="checkbox" <?php echo $checked; ?> value="1" name="fldStatus[<?php echo $key ?>]" id="fldStatus<?php echo $count; ?>" class="inputs" />
                                    </li>
                                </ul>
                            </div>
                            <?php                        
                    }
                }
                else {
                    ?>                    
                    <div style="clear:both"></div>
                        <?php
                            $count = 0;
                            for ($i=0; $i < 5; $i++) {
                            $placeholder = $default_lbl[$count];
                            $count++;     
                            $checked = ( $count>5 ) ? '': 'checked="checked"';
                          ?>
                            <div class="box_form"  id="row<?php echo $count; ?>">
                                <ul class="lists" style="clear:both;">
                                    <li>
                                        <label><strong>Label Text:</strong></label>
                                        <textarea name="fldLabel[]" id="fldLabel<?php echo $count; ?>" placeholder="<?php echo $placeholder; ?>" class="texts" rows="1" cols="50"></textarea>
                                    </li>
                                    <li>
                                        <label><strong>Show/Hide:</strong></label>
                                        <input type="hidden" name="fldStatus[<?php echo $i ?>]" value="0">
                                        <input type="checkbox" <?php echo $checked; ?> value="1" name="fldStatus[<?php echo $i; ?>]" id="fldStatus<?php echo $count; ?>"  class="inputs" />
                                    </li>
                                </ul>
                            </div>      
                        <?php } ?>
                    
            <?php } ?>
            </div>            

            <div class="clear"></div>
            <?php $this->load->view("stage/admin/template_form_footer"); ?>
        </form>
    </div>
</div>
