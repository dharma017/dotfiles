<div class="boxin">
    <ul class="addstage" id="box-tasks">
        <form name="frmAddTask" id="frmAddTask" method="post">

            <li>
                <label class="label"><strong>Problemområde</strong></label>
                <select name="problem_id" class="required" id="selProblem">
                    <option value="0"><?=lang('js_sel_problem')?></option>
                    <?php foreach ($problems as $pk => $problem):
                    if(!in_array($problem->id, $problemsList)) continue;
                     ?>
                        <option value="<?=$problem->id?>"><?=$problem->problem?></option>
                    <?php endforeach ?>
                </select>
            </li>

            <li style="margin-top:15px;">

                <label class="label" for="taskgr"><strong><input type="radio" name="taskgr" id="taskgr" value="available" style="float:left;margin-top:5px;" checked="checked">Välj uppgift</strong></label>
                <select name="task_id" class="required" id="selTask">
                    <option value="0"><?=lang('js_sel_task')?></option>
                </select>
            </li>

            <li style="margin-top:10px;">

                <label class="label" for="taskgr1"><strong><input type="radio" name="taskgr" id="taskgr1" value="custom" style="float:left;margin-top:5px">Egen uppgift</strong></label>
                <input class="inputs" id="cinputs" type="text" name="custom_task" value="" style="height:19px" />
            </li>

            <li style="clear:left;  margin-bottom:15px;">
                <input type="button" name="btnSave" id="btnSave" value="<?= lang('save') ?>" onclick="addTaskByPatient('<?=$user_id?>');" class="button">
                <input type="button" name="btnCancel" id="btnCancel" value="<?= lang('cancel') ?>" onclick="view('<?=$username?>')" class="button">
            </li>

            <input type="hidden" name="userId" id="userId" value="<?=$user_id?>">
            <input type="hidden" name="diffId" id="diffId" value="<?=$diffId?>">
            <input type="hidden" name="username" id="username" value="<?=$username?>">

        </form>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#cinputs').attr("disabled","disabled");
    $('#selTask').removeAttr("disabled");

    $('#taskgr1').click(function()
    {
        $('#cinputs').removeAttr("disabled");
        $('#selTask').attr("disabled","disabled");
        $('#tagdv').show();
    });

    $('#taskgr').click(function()
    {
        $('#cinputs').attr("disabled","disabled");
        $('#selTask').removeAttr("disabled");
        $('#tagdv').hide();
    });

    $('#selProblem').change(function() {
    var newVal = $(this).val();
    var userId = $('#userId').val();
    var diffId = $('#diffId').val();
    $path=$sitePath+"/minapp/getTaskOptions";
        $.ajax({
            url: $path,
            type: 'post',
            data: {newVal: newVal,user_id:userId,diffId:diffId},
            success:function(resp) {
                resp='<option value="0">'+$jsLang['sel_task']+'</option>'+resp;
                $('#selTask').html(resp);
            },
            error: function(){
                console.log('error');
            }
        });
    });

});
</script>
