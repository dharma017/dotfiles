<div class="box box-100">
    <!-- box full-width -->
    <div class="boxin">
        <div class="header">
            <h3><?php echo $templateHeading; ?> </h3>
        </div>
        <form name="frmAddStep" id="frmAddStep" method="post">
            <?php $this->load->view("stage/admin/template_common_form"); ?>
        </ul>
         <div style="clear:both"></div>
            <div id="container_fields" class="templateABC">

                <input type="hidden" id="field_id" value="<?php if ($task == "editStep") echo count($templateData); else echo '1'; ?>">

                <div class="childABC">
                        <label for="">Barn</label>
                        <div><span>A:</span><br/><textarea rows="3" cols="27" name="child_abc_box_1"><?= (!empty($templateData[0]->fld_label)) ? $templateData[0]->fld_label: ''; ?></textarea></div>

                        <div><span>B:</span><br/><textarea rows="3" cols="27" name="child_abc_box_2"><?= (!empty($templateData[1]->fld_label)) ? $templateData[1]->fld_label: ''; ?></textarea></div>

                        <div><span>C:</span><br/><textarea rows="3" cols="27" name="child_abc_box_3"><?= (!empty($templateData[2]->fld_label)) ? $templateData[2]->fld_label: ''; ?></textarea></div>
                    </div>
                    <div style="clear:both"></div>

                    <div class="parentABC">
                        <label for="">Förälder</label>
                        <div><span>A:</span><br/><textarea rows="3" cols="27" name="parent_abc_box_1"><?= (!empty($templateData[3]->fld_label)) ? $templateData[3]->fld_label: ''; ?></textarea></div>

                        <div><span>B:</span><br/><textarea rows="3" cols="27" name="parent_abc_box_2"><?= (!empty($templateData[4]->fld_label)) ? $templateData[4]->fld_label: ''; ?></textarea></div>

                        <div><span>C:</span><br/><textarea rows="3" cols="27" name="parent_abc_box_3"><?= (!empty($templateData[5]->fld_label)) ? $templateData[5]->fld_label: ''; ?></textarea></div>
                    <div>

            </div>

            <div class="clear"></div>
            <?php $this->load->view("stage/admin/template_form_footer"); ?>
        </form>
    </div>
</div>
<style>
    .childABC label,.parentABC label{
        width: 116px;
        font-weight: bold;
        margin-top: 23px;
    }
    .childABC div,.parentABC div{
        width:200px; float:left; padding-right:50px;padding-bottom: 10px;
    }
    .childABC span,.parentABC span{
        margin-left: 60px;
    }
</style>