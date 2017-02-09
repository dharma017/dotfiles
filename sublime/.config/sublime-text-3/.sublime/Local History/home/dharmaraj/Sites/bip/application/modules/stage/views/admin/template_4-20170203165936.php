<script type="text/javascript">
function addFormFields() {
	
	var field_id = document.getElementById("field_id").value;	//alert(field_id);
	id = (field_id*1)+1;

	$("#container_fields").append('<div class="box_form"  id="row' + id + '" style="display:block;"><ul  class="lists" style="clear:both"><li><label><strong>Label Text:</strong></label> <textarea name="fldLabel[]" id="fldLabel' + id + '" class="texts" rows="1" cols="50" /></textarea></li><li><label><strong>Num. of Rows:</strong></label> <input type="text" value="" name="fldRow[]" id="fldRow' + id + '" style="width:30px;"   class="inputrs" /></li></ul><a href="#" onClick="removeFormFieldLabel(\'#row' + id + '\'); return false;" class="remover">Remove</a></div>');
	
	//$('#row' + id).show();
	//$('#row' + id).slideDown({
	//	speed:1000
	//});
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

<div class="box box-100"> 
  <!-- box full-width -->
  <div class="boxin">
    <div class="header">
      <h3><?php echo $templateHeading;	?> </h3>
    </div>
    <form name="frmAddStep" id="frmAddStep" method="post">
      
      <?php $this->load->view("stage/admin/template_common_form");?>
      </ul>     
      <?php $this->load->view("stage/parts/_parts_common_video");?>
      
      <div style="clear:both"></div>
            
      <div id="container_fields">
        <input type="hidden" id="field_id" value="<?php if($task=="editStep") echo count($templateData); else echo '1'?>">
        <?php
          if($task=="editStep")
          {
        $count = 0;
        	foreach($templateData as $rows)
        	{
        		$count++;
        		?>
                <div class="box_form"  id="row<?php echo $count;?>">
                <?php if($count>1) echo '<a href="#" onClick="removeFormFieldLabel(\'#row'.$count.'\'); return false;" class="remover">Remove</a>';?>
                  <ul class="lists">
                    <li >
                      <label><strong>Label Text:</strong></label>
                      <textarea name="fldLabel[]" id="fldLabel<?php echo $count;?>"   class="texts" rows="1" cols="50"><?php echo $rows->fld_label;?></textarea>
                    </li>
                    <li  >
                      <label><strong>Num. of Lines:</strong></label>
                      <input type="text" value="<?php echo $rows->fld_row;?>" name="fldRow[]"  style="width:30px;" id="fldRow<?php echo $count;?>"  class="inputs" />
                    </li>
           
            
                  </ul>
                </div>
                <?php }

         }
         else 
         {?>
                <div class="box_form"  id="row1">
               
                  <ul class="lists" style="clear:both;">
                 
                    <li >
                      <label><strong>Label Text:</strong></label>
                      <textarea name="fldLabel[]" id="fldLabel1"   class="texts" rows="1" cols="50"></textarea>
                    </li>
                    <li>
                      <label><strong>Num. of Rows:</strong></label>
                      <input type="text" value="" name="fldRow[]" id="fldRow1" class="inputrs" style="width:30px;" />
                    </li>
                   
                    
                  </ul>
                </div>
        
        

        <?php } ?>
      </div>

       <input type="button" value="Add More Field &raquo;" id="addFields" class="savebtns"  style=" margin-left:0;" onClick="addFormFields()" />
       <div class="clear"></div>
      <?php $this->load->view("stage/admin/template_form_footer");?>
    </form>
  </div>
</div>
