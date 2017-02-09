<script type="text/javascript">

    $(function() {

        var _method = '<?php echo $task;?>';
        if(_method == 'addStep'){
            for(i=0;i<3;i++){
                addFormFields();
            }
        }

            $( "#container_fields" ).sortable({
                placeholder: "ui-state-highlight"
            });
            // $( "#container_fields" ).disableSelection();
        });

    function addFormFields() {

        var field_id = document.getElementById("field_id").value;
        id = parseInt(field_id)+1;
		if( id <= 6)
		{
			$("#container_fields").append('<div class="box_form ui-state-default"  id="row' + id + '" style="display:block;"><ul  class="lists" style="clear:both"><li><label><strong>Label text:</strong></label> <textarea name="fldLabel[]" class="texts" rows="1" cols="50" /></textarea></li></ul><a href="#" class="remover">Remove</a></div>');

			document.getElementById("field_id").value = id;

			if(id == 6)
			{
				$('#addFields').hide();
			}
		}
		else
		{
			$('#addFields').hide();
		}
    }

    $(document).unbind('click').on('click','.remover',function(e){
        e.preventDefault();
        var field_id = document.getElementById("field_id").value;

        if(field_id>3)
        {
            field_id = parseInt(field_id) - 1;
            document.getElementById("field_id").value = field_id;
            $(this).parent('div').remove().fadeOut();
			$('#addFields').show();
        }else{
            alert('Must have at least 3 label box');return false;
        }
    });
</script>
<div class="box box-100">
    <!-- box full-width -->
    <div class="boxin">
        <div class="header">
            <h3><?php echo $templateHeading; ?> </h3>
        </div>
        <form name="frmAddStep" id="frmAddStep" method="post">
            <?php $this->load->view("stage/admin/template_common_form"); ?>
<li style="font-weight :700;"><?=lang('box_are_movable');?></li>

</ul>
 <div style="clear:both"></div>
            <div id="container_fields">

                <input type="hidden" id="field_id" value="<?php if ($task == "editStep")
                echo count($templateData); else
                echo '0'; ?>">

                <?php


                if ($task == "editStep") {
                    $count = 0;
                    foreach ($templateData as $rows) {
                        $count++;
                    ?>



        <div class="box_form ui-state-default">
                <?php
                        echo '<a href="#" class="remover">Remove</a>';
                ?>
                        <ul class="lists">
                            <li>
                                <label><strong>Label Text</strong></label>
                                <textarea name="fldLabel[]" class="texts" rows="1" cols="50"><?php echo $rows->fld_label; ?></textarea>
                            </li>

                        </ul>
                    </div>
                    <?php
                }

            }
 ?>
            </div>

            <input type="button" value="Add More Field &raquo;" id="addFields" class="savebtns"  style=" margin-left:0;<?php if ($count >= 6) echo 'display:none;'?>" onClick="addFormFields()" />
            <div class="clear"></div>
<?php $this->load->view("stage/admin/template_form_footer"); ?>
        </form>
    </div>
</div>
