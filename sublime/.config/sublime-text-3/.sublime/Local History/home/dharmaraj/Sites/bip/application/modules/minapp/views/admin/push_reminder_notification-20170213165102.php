<?php
// $reminder_data=$this->minapp_model->getPushReminderData();
$sess_permission = $this->stage_model->getPermissionOfPsy();
                $sess_permission = json_decode($sess_permission,true);
                $usertype = $this->session->userdata('user_role_type');
                $logintype = $this->session->userdata('logintype');
                if($usertype == "superadmin" && $logintype == "admin"){
                    $difficulties = $this->stage_model->getAllDifficultyByLang();
                }else{
                    foreach ($sess_permission['rights_per_difficulty'] as $key => $value) {
                        if($value['edit_difficulty'] == 1){
                            $diff[] = $key;
                        }
                    }
                    if(!empty($diff) && is_array($diff))
                        $diff_page = join(',',$diff);
                    $diff_page = rtrim($diff_page,",");
                    $difficulties = $this->user_model->getDifficultyNamebyIds($diff_page);
                } ?>
<div id="box-reminder" style="display:none;">
	<form name="frmReminder" id="frmReminder" method="post" accept-charset="utf-8" enctype="multipart/form-data">
	<ul class="adm-form diffForm">
        <li>
        <label class="lblnotify" style="margin-left:18px"><strong><?=lang('txt_treatment')?></strong></label>
            <select name="difficulty" id="selDiff1" onchange="fillDiffFormReminder(this.value);">
                <option value="0">Default Group</option>
                <?php foreach ($difficulties as $dk => $difficulty): ?>
                    <option value="<?=$difficulty->id?>"><?=$difficulty->difficulty?></option>
                <?php endforeach ?>
            </select>
        </li>

        <li>
            <ul id="rem_scents">
                <li> <label for=""></label> </li>
            </ul>
        </li>

		<li style="clear:both;">
			<input type="button" name="btnSave" id="btnSave" onclick="savePushReminder();" value="<?php echo $this->lang->line("save");?>"  class="button" />
		</li>
	</ul>
	</form>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/shared/timepicker/jquery.ui.timepicker.css"/>
<script type="text/javascript" src="<?php echo base_url() ?>assets/shared/timepicker/jquery.ui.timepicker.js"></script>

<script type="text/javascript">
	$(function() {

		var i,
            scntDiv = $('#rem_scents'),
            addmsgHtml = '<span class="addmsg"><a href="#" id="addScnt"  style="width:100%; float:left;">[+] Lägg till påminnelse</a></span>';

        $('.addmsg').remove();
        $( "#rem_scents" ).append(addmsgHtml);

        $('#addScnt').live('click', function() {

                $('.addmsg').remove();

                i = $('#rem_scents li').size();

                $('<li> <div style="width:100%; float:left;"><label class="lblnotify" style="width:212px !important;margin-left: 13px; margin-top: 5px;"><strong>Tid</strong></label><input type="text" class="reminder_time" size="10" name="p_time_' + i +'" value=""  /></div> <div style="width:100%; float:left; margin-top: 8px;"><label class="lblnotify" style="width:212px !important;margin-left: 13px; margin-top: 5px;"><strong>Notifieringsmeddelande</strong></label><input type="text" size="50" name="p_scnt_' + i +'" value="" style="margin-left: 13px;"/><a href="#" class="delmsg"  style="margin:5px 10px 0; float:left;">Ta bort (X)</a> </div> </li>').appendTo(scntDiv);
                i++;

                $( "#rem_scents" ).append(addmsgHtml);

                $('.reminder_time').timepicker({
                    minutes: { interval: 10 },
                    showPeriodLabels: false,
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

        fillDiffFormReminder(0);

	});
</script>
