<?php $tagArr=explode(',', $row->tag);
?>

<ul class="adm-form">
  <form name="frmAddDifficulty" id="frmAddDifficulty" method="post">

    <?php echo validation_errors('<li class="txt-left error_msg">', '</li>'); ?>
    <li>
      <label class="tasklabel"><strong>Difficulty Name: </strong></label>
      <input name="difficultyName" id="difficultyName" value="<?php echo set_value('difficultyName',$difficultyName);?>" type="text" maxlength="255" size="50" class="inputs"/>
	  <div id="errorDifficultyName"></div>
    </li>
    <li>
          <label class="tasklabel"><strong>Select Skin:</strong></label>
	        <select name="skin_id" id="difficulty" >
          <?php
						 	$skins = $this->setting_model->getAllSkins();

							foreach($skins as $key => $skin)
							{
								$selected = ($row->skin_id==$skin->id) ? 'selected="selected"': '';
								echo '<option '.$selected.' value='.$skin->id.'>'.$skin->skin_name.'</option>';
							}
						?>
	          </select>
		</li>
    <li class="chk-li">
      <label class="tasklabel"><strong>Tag</strong></label>

      <input class="tagme" id="traningsuppgift" type="checkbox" name="tag[]"  <?=(in_array(1, $tagArr) || empty($row->id)) ? 'checked="checked"': ''?> value="1">
      <label for="traningsuppgift" style="padding:3px;">Träningsuppgift</label>

      <input class="tagme" id="smartmatning" type="checkbox" name="tag[]"  <?=(in_array(2, $tagArr)) ? 'checked="checked"': ''?> value="2">
      <label for="smartmatning" style="padding:3px;">Smärtmätning</label>

      <input class="tagme" id="sjalvskada" type="checkbox" name="tag[]"  <?=(in_array(3, $tagArr)) ? 'checked="checked"': ''?> value="3">
      <label for="sjalvskada" style="padding:3px;">Självskada</label>

    </li>
    <li class="li-hide-graph">
        <label for="hide_graph" class="tasklabel"><strong>Hide Graph</strong></label>
        <input type="hidden" name="hide_graph" value="0">
        <input id="hide_graph" type="checkbox" name="hide_graph" <?=($row->hide_graph) ? 'checked="checked"': ''?> value="1">
     </li>
   <!--  <li>
      <label for="new_start_page" class="tasklabel"><strong>App Startpage</strong></label>
      <select id="new_start_page" name="new_start_page" class="sel-dd">
          <option value="1" <?php echo ($row->new_start_page==1)?" selected='selected'":""?>>Self Harm Start page</option>
          <option value="0" <?php echo ($row->new_start_page==0)?" selected='selected'":""?>>Don't set Start page</option>
      </select>
    </li> -->
    <li>
      <label for="enable-app-msg-alert" class="tasklabel"><strong>Alert</strong></label>
      <input type="checkbox" id="enable-app-msg-alert" name="enable_msg_alert" value="1" <?php echo ($row->enable_msg_alert==1)?" checked='checked'":""?> /><br/ >
      <span class="hints" style="margin-left:20px;line-height:2em;">Toggle this to enable/disable alert banner in app for new messages.</span>
    </li>
     <li style="display:none">
        <label for="hide_number" class="tasklabel"><strong>Hide Number</strong></label>
        <input type="hidden" name="hide_number" value="0">
        <input id="hide_number" type="checkbox" name="hide_number" <?=($row->hide_number) ? 'checked="checked"': ''?> value="1">
      </li>
      <li>
        <input type="button" name="btnSave" id="btnSave" value="<?php echo $this->lang->line("save");?>"  onclick="addDifficulty();" class="button" />
        <input type="button" name="btnCancel" id="btnCancel" value="<?php echo $this->lang->line("cancel");?>" onclick="listDifficulty();" class="button" />
    </li>
   <input type="hidden" value="<?php echo $difficultyId;?>" name="difficultyId" id="difficultyId" />
   <input type="hidden" value="1" id="checkDifficultyName"/>
  </form>
</ul>
<style>
  #frmAddDifficulty label.error {
    float: right;
    width: 425px;
  }
</style>
<script>
$(function(){
  <?php
  if($row->tag==3){
    ?>
    $(".li-hide-graph").hide();
    <?php
  }else{
  ?>
  $(".li-hide-graph").show();
  <?php

  }
  ?>
 // $(".tagme").trigger("click")
  $(".chk-li input:checkbox").click(function(){
      var group = "input:checkbox[name='"+$(this).attr("name")+"']";
      $(group).attr("checked",false);
      $(this).attr("checked",true);
  });

  $(".tagme").click(function(){
      $val = $(this).val();
      if($(this).is(":checked")==true && $val==3){
          $(".li-hide-graph").hide();
      }else{
        $(".li-hide-graph").show();
      }
  });
})

</script>
