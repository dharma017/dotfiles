<?php echo validation_errors('<div class="error_msg">', '</div>'); ?>

  <form name="frmAddGroup" id="frmAddGroup" method="post">
<ul class="adm-form">
    <li>
       <label><?= lang('txt_group_name') ?>: </label>
      <input name="groupName" id="groupName" value="<?php echo set_value('groupName',$groupName);?>" type="text" maxlength="255" size="50" class="inputs"/>
    </li>

    <li>
	    <label>&nbsp;</label>
      <input type="button" name="btnSave" id="btnSave" value="<?php echo $this->lang->line("save");?>"  onclick="addGroup('<?php echo $todo;?>');" class="button" />
      <input type="button" name="btnCancel" id="btnCancel" value="<?php echo $this->lang->line("cancel");?>" onclick="listGroup();" class="button" />
    </li>
   <input type="hidden" value="<?php echo $groupId;?>" name="groupId" id="groupId" />
</ul>
  </form>
