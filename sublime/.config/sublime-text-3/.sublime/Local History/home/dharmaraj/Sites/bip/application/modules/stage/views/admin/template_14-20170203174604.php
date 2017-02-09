<script type="text/javascript">
function addFormFields() {

    var field_id = document.getElementById("field_id").value;   //alert(field_id);
    id = (field_id*1)+1;

    $("#container_fields").append('<div class="box_form"  id="row' + id + '" style="display:block;"><ul  class="lists" style="clear:both"><li><label><strong>Label Text:</strong></label> <textarea name="fldLabel[]" id="fldLabel' + id + '" class="texts" rows="2" cols="50" /></textarea></li><li><label><strong>Width in % :</strong></label> <input type="text" value="" name="width[]" id="width' + id + '" style="width:30px; margin-top:5px;"   class="inputrs widthInput" /></li></ul><a href="#" onClick="removeFormFieldLabel(\'#row' + id + '\'); return false;" class="remover">Remove</a></div>');

    document.getElementById("field_id").value = id;
}

function removeFormFieldLabel(id) {

    var field_id = document.getElementById("field_id").value;
    if(field_id>=1)
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
            <h3><?php echo $templateHeading; ?> </h3>
        </div>
        <form name="frmAddStep" id="frmAddStep" method="post">
            <?php $this->load->view("stage/admin/template_common_form"); ?>



 <div style="clear:both"></div>
            <div id="container_fields">

                <input type="hidden" id="field_id" name="field_id" value="<?php if ($task == "editStep")
                echo count($templateData); else
                echo '1'; ?>">

                <?php


                if ($task == "editStep") {
                    $flag = true;

                     //echo "<pre>"; print_r($templateData); echo "</pre>";
                    foreach ($templateData as $tk => $rows)
                    {
                        if($flag)
                        {
                            $textbox_rows = $rows->fld_row;
                            $worksheet_rows = $rows->fld_col;
                            $margin = $rows->fld_margin;
                            $flag = false;
                        }

                        ?>

                        <?php if ($tk==0): ?>


                        <div class="box_form">
                        <ul class="lists" style="clear:both;">
                                <li>
                                    <label><strong>Number of rows inside text fields:</strong>
                                        <input type="text" value="<?php echo $textbox_rows; ?>" name="textbox_rows" class="inputrs" style="width:30px; float:none; display:inline-block; margin-left: 5px;" />
                                    </label>
                                </li>

                            </ul>
                        </div>
                        <div class="box_form">
                            <ul class="lists" style="clear:both;">
                                <li>
                                    <label><strong>Number of rows in working sheet:</strong>
                                        <input type="text" value="<?php echo $worksheet_rows; ?>" name="worksheet_rows" class="inputrs" style="width:30px; float:none; display:inline-block; margin-left: 5px;" />
                                    </label>
                                </li>

                            </ul>
                        </div>

                        <div class="box_form">
                            <ul class="lists" style="clear:both;">
                                <li>
                                    <label><strong>Margin between boxes in %:</strong>
                                        <input type="text" value="<?php echo $margin; ?>" name="margin" class="inputrs" style="width:30px; float:none; display:inline-block; margin-left: 5px;" /> <small>(for example: 1% = 7.85px)</small>
                                    </label>
                                </li>

                            </ul>
                        </div>
                        <?php endif ?>

                        <div class="box_form"  id="row<?php echo $rows->ordering; ?>">
                        <?php if($rows->ordering>1) echo '<a href="#" onClick="removeFormFieldLabel(\'#row'.$rows->ordering.'\'); return false;" class="remover">Remove</a>';?>

                            <ul class="lists">
                                <li>

                                    <label><strong>Label Text <?php echo $rows->ordering; ?>:</strong></label>
                                    <textarea name="fldLabel[]"  class="texts" rows="2" cols="50"><?php echo $rows->fld_label; ?></textarea>

                                </li>
                                <li>
                                <label><strong>width in %:</strong></label>
                                 <input type="text" value="<?php echo $rows->fld_width; ?>" name="width[]" id="width1" class="inputrs widthInput" style="width:30px; margin-top:5px;" />
                            </li>
                            </ul>
                        </div>
                    <?php
                    }

                }
                else {
                        $textbox_rows = '';
                        $worksheet_rows = '';
                        $margin = '';
                    ?>


                    <div class="box_form">
                        <ul class="lists" style="clear:both;">
                                <li>
                                    <label><strong>Number of rows inside text fields:</strong>
                                        <input type="text" value="<?php echo $textbox_rows; ?>" name="textbox_rows" class="inputrs" style="width:30px; float:none; display:inline-block; margin-left: 5px;" />
                                    </label>
                                </li>

                            </ul>
                        </div>
                        <div class="box_form">
                            <ul class="lists" style="clear:both;">
                                <li>
                                    <label><strong>Number of rows in working sheet:</strong>
                                        <input type="text" value="<?php echo $worksheet_rows; ?>" name="worksheet_rows" class="inputrs" style="width:30px; float:none; display:inline-block; margin-left: 5px;" />
                                    </label>
                                </li>

                            </ul>
                        </div>
                        <div class="box_form">
                            <ul class="lists" style="clear:both;">
                                <li>
                                    <label><strong>Margin between columns in %:</strong>
                                        <input type="text" value="<?php echo $margin; ?>" name="margin" class="inputrs" style="width:30px; float:none; display:inline-block; margin-left: 5px;" /> <small>(for example: 1% = 7.85px)</small>
                                    </label>
                                </li>

                            </ul>
                        </div>

                    <div class="box_form"  id="row1">

                        <ul class="lists" style="clear:both;">

                            <li>
                                <label><strong>Label Text 1</strong></label>
                                <textarea name="fldLabel[]"  class="texts" rows="2" cols="50"></textarea>
                            </li>
                            <li>
                                <label><strong>width in %:</strong></label>
                                 <input type="text" name="width[]" id="width1" class="inputrs widthInput" style="width:30px; margin-top:5px;" />
                            </li>
						</ul>
					</div>



        <?php } ?>


    </div>

            <input type="button" value="Add More Field &raquo;" id="addFields" class="savebtns"  style=" margin-left:0;" onClick="addFormFields()" />
            <div class="clear"></div>
            <?php $this->load->view("stage/admin/template_form_footer"); ?>
        </form>
    </div>
</div>
