<div class="boxin addstage box-reminder" id="box-tasks">
    <form name="frmReminder" id="frmReminder" method="post" accept-charset="utf-8" enctype="multipart/form-data">
        <label><strong>Påminnelser</strong></label>
        <div style="margin-top:15px;margin-bottom:20px;">
        <a class="publish-hw p10 enable-disable-reminder <?php echo $notification['new_n_class']==1 ? "inactive":""?>" data-newstatus="<?php echo $notification['new_n_st']?>" href="javascript:void(0)"><?php echo $notification["new_n_label"]?></a>
        <span class='not-alert-msg' style="display:none;"><?php echo $notification['new_n_msg']?></span>
        </div>
        <div class="optbtn" style="margin-top:10px">
            <span style="width:224px;display:inline-block;"><input type="radio" name="app_reminder_type" <?php echo ($user['app_reminder_type'] == "0") ? 'checked="checked"' : ''; ?> value="0" id="rd1" checked><label for="rd1">Default påminnelse</label></span>
            <span style="width:224px;display:inline-block;"><input type="radio" name="app_reminder_type" <?php echo ($user['app_reminder_type'] == "1") ? 'checked="checked"' : ''; ?> value="1" id="rd2"><label for="rd2">Ställ in tid för påminnelse</label></span>
        </div>
        <ul class="adm-form diffForm">
            <li>
                <ul id="rem_scents">
                    <li> <label for=""></label> </li>
                </ul>
            </li>

             <li style="margin:10px 0px 15px 319px;clear:both">
                <input type="button" name="btnSave" id="btnSave" onclick="savePushReminder('<?=$user_id?>');" value="<?php echo $this->lang->line("save");?>"  class="button" />
                <input type="button" name="btnCancel" id="btnCancel" value="<?= lang('cancel') ?>" onclick="view('<?=$username?>')" class="button">
            </li>
        </ul>
        <input type="hidden" name="username" id="username" value="<?=$username?>">
        <input type="hidden" name="userId" id="userId" value="<?=$user_id?>">
        <input type="hidden" name="diffId" id="diffId" value="<?=$diffId?>">
    </form>
</div>


<style type="text/css">
.optbtn input[type="radio"] {
  margin-top: -1px;
  vertical-align: middle;
  margin-right: 5px;
}
</style>

<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/shared/timepicker/jquery.ui.timepicker.css"/>
<script type="text/javascript" src="<?php echo base_url() ?>assets/shared/timepicker/jquery.ui.timepicker.js"></script>

<script type="text/javascript">
    $(function() {

        var i,
            scntDiv = $('#rem_scents'),
            addmsgHtml = '<span class="addmsg"><a href="#" id="addScnt"  style="width:100%; float:left;">[+] Lägg till påminnelse</a></span>';

        $('.addmsg').remove();
        $( "#rem_scents" ).append(addmsgHtml);

        $(".enable-disable-reminder").live("click", function(){
                $newStatus = $(this).attr("data-newstatus");
                $obj = $(this);
                if(confirm($(".not-alert-msg").html())){
                        $.ajax({
                            url: $sitePath + "/minapp/enableDisableReminder",
                            type: "POST",
                            data: "dowhat="+$newStatus+"&patient_id=<?=$user_id?>",
                            dataType: 'json',
                            beforeSend: function(){},
                            success: function(data){

                                if(data.status=="ok"){
                                    $obj.html(data.new_n_label);
                                    $obj.attr("data-newstatus",data.new_n_st);
                                    $(".not-alert-msg").html(data.new_n_msg);
                                    $obj.removeClass("inactive");
                                    if(data.new_n_class==1){
                                        $obj.addClass("inactive");
                                    }
                                }
                            }
                        });
                }
        });

        $('#addScnt').live('click', function() {

                $('.addmsg').remove();

                i = $('#rem_scents li').size();

                $('<li> <div style="width:100%; float:left;"><label class="" style="width:212px;margin-left: 13px; margin-top: 0px;"><b>Tid:</b></label><input type="text" style="height:24px;padding:4px;" class="reminder_time" size="10" name="p_time_' + i +'" value=""  /></div> <div style="width:100%; float:left; margin-top: 8px;"><label  class=""style="width:212px;margin-left: 13px; margin-top: 0px;"><b>Notifieringsmeddelande:</b></label><input style="height:24px;padding:4px; margin-left: 13px;"type="text" size="50" name="p_scnt_' + i +'" value=""  /><a href="#" class="delmsg"  style="margin:5px 10px 0;">Ta bort (X)</a> </div> </li>').appendTo(scntDiv);
                i++;

                $( "#rem_scents" ).append(addmsgHtml);

                $('.reminder_time').timepicker({
                    minutes: {
                        interval: 10
                    },
                    showPeriodLabels: false,
                    showNowButton: true,
                    showDeselectButton: false,
                    defaultTime: '', // removes the highlighted time for when the input is empty.
                    showCloseButton: true
                });

                return false;
        });

        $('.delmsg').live('click', function() {
            $('.addmsg').remove();
            $(this).closest('li').remove();

            i = $('#rem_scents li').size();

            if( i > 0 ) {
                i--;
            }
            $( "#rem_scents" ).append(addmsgHtml);
            return false;
        });

        $('.box-reminder input[name=app_reminder_type]').change(function(){
            var app_reminder_type = $(this).val();
            fillDiffFormReminder(app_reminder_type);
        })

        var app_reminder_type = $('.box-reminder input[name=app_reminder_type]:checked').val();
        fillDiffFormReminder(app_reminder_type);
    });

</script>
