<div id ="contentArea" class="row clear">
<div id="innercontentArea">
<!-- Added by Sabin @17th July 2015 >> -->
<?php
  $tag = $this->minapp_model->getTagByDifficultyID($user['difficulty_id']);
  if($tag==3){//START => show registration, crisisplan, homeworks, etc contents only if the current patient has self harm enabled.
?>
<script src="<?=base_url();?>assets/admin/js/tinymce4/js/tinymce/tinymce.min.js"></script>
<script src="<?=base_url();?>assets/admin/js/tinymce4/js/tinymce/themes/modern/theme.min.js"></script>
<div class="new-dashboard-box">
  <h1><?php echo lang("txt_registrations"); ?></h1>
  <div class="dashboard-content">
        <div class="fl list-registration">
        <?php
        $fetch_registrations = $this->minapp_model->getPatientsRegistrations($userId);
          if(count($fetch_registrations)>0){
              foreach($fetch_registrations as $reg){

        ?>
            <div class="registration-name fl" <?php echo $usertype=="Psychologist" ? "style='width:300px'": "";?>><?php echo $reg->registration_name?></div><div class="assignment-count fl"><?php echo $reg->total_numbers;?> st</div> <div class="clear"></div>
        <?php
              }

            }else{
                echo "<div>".lang("txt_no_registration_avail")."</div>";
            }
          ?>
        </div>
        <?php
        if ($usertype=='Psychologist'){

          $patients_answer = $this->minapp_model->getPatientSpecialAnswers($userId,$user['difficulty_id']);
          $special_answers = $this->minapp_model->getAllSpecialAnswers($userId,$user['difficulty_id']);
        ?>
        <div class="fr psy-reg-stuffs">
            <div>
              <select class="my-dropdown" id="list-special-answers" multiple="multiple" name="list_special_answer[]">
                <?php
                    foreach($special_answers as $answer){
                      if(is_array($patients_answer)){
                          $selected = in_array($answer->answer_id, $patients_answer) ? "selected='selected'" : "";
                      }else{
                          $selected = "";
                      }
                ?>
                <option value="<?=$answer->answer_id?>" <?php echo $selected;?>><?=$answer->answer?></option>
                <?php
                    }
                ?>
              </select>
            </div>
            <div style="margin-top:15px">
              <?php echo lang("txt_add_patient_special_answer")?>
              <div>
              <input type="text" class="my-input" size="40" name="custom_answer" id="custom_answer" />
              <a href="javascript:void(0)" class="publish-hw p10 save-patient-special-answer"><?=lang("txt_add")?></a>
              </div>
            </div>

        </div>
        <?php
      }
        ?>
        <div class="clear"></div>
        <?php
         if(count($fetch_registrations)>0){
        ?>
        <div class="fl btn-holder">
            <a href="<?=site_url()?>/minapp/fetchPatientAnsweredRegistrations" class="new-btn p10 see-all-registration"  style="width:300px"><?php echo lang("txt_see_all_registrations");?></a>
        </div>
          <?php
          if ($usertype=='Psychologist'){
          ?>
          <div class="fr btn-holder">
              <a href="<?=site_url()?>/minapp/fetchPatientsCustomAnswer" class="new-btn p10 see-patients-answer"  style="width:300px"><?php echo lang("txt_see_patients_answers");?></a>
          </div>
            <?php
          }
            ?>
        <div class="clear"></div>
        <?php
        }
        ?>
  </div>
</div>
<div class="new-dashboard-btns">
<a href="<?=site_url()?>/minapp/viewActivitySkills" data-userid="<?=$userId?>"  class="fl new-btn p10 btn-view-activity-skills"><?=lang("txt_see_activity_skills")?></a>
<a href="<?=site_url()?>/minapp/viewActivityFeelings" data-userid="<?=$userId?>"  class="fl new-btn p10 btn-view-activity-feelings"><?=lang("txt_see_activity_findfeelings")?></a>
<a href="<?=site_url()?>/minapp/viewActivityThoughts" data-userid="<?=$userId?>"  class="fl new-btn p10 btn-view-activity-thoughts"><?=lang("txt_see_activity_thoughts")?></a>
<?php
if ($usertype=='Psychologist'):
?>
<a href="<?=site_url()?>/minapp/showCrisisplanList" data-userid="<?=$userId?>" data-username="<?=$user['username']?>" data-diffid="<?=$user['difficulty_id']?>" class="fr new-btn p10 btn-update-crisis-list" style="margin-right:0;">&nbsp;&nbsp;<?php echo lang("txt_manage_crisis_plan")?>&nbsp;&nbsp;</a>
<a href="<?=site_url()?>/minapp/showActivationModulesList" data-userid="<?=$userId?>" data-username="<?=$user['username']?>" data-diffid="<?=$user['difficulty_id']?>" class="fr new-btn p10 btn-manage-activation-modules" style="float: left; margin-left: 0px;">&nbsp;&nbsp;<?php echo lang("txt_manage_my_activiation")?>&nbsp;&nbsp;</a>
<!-- <a href="<?=site_url()?>/minapp/showHomeworkList" data-userid="<?=$userId?>" data-username="<?=$user['username']?>" data-diffid="<?=$user['difficulty_id']?>" class="fr new-btn p10 btn-manage-homework" style="margin-right:10px;">&nbsp;&nbsp;<?php echo lang("txt_manage_my_homework")?>&nbsp;&nbsp;</a>
 -->

<?php
else:
  if($user["app_web_version"]==1){//show only if user is allowed to view web version. User can either only use web version or a mobile version of an app
?>
<a href="<?=base_url()?>webapp" target="_blank" class="fr new-btn p10" style="width:180px;"><?=lang("txt_mobile_version_app_web")?></a>
<?php
  }
endif; ?>
<div class="clear"></div>
</div>
<?php
//check if current users treatment has any exposure, if it has then only show the exposure box
$hasExposure = $this->minapp_model->checkIfTreatmentHasExposure($user['difficulty_id']);
if($hasExposure->exposure_count>0):
?>
<div class="new-dashboard-box">
  <h1><?php echo lang("exposure"); ?></h1>
  <div class="dashboard-content">
      <div class=""><?=lang("txt_add_new_exposure")?></div>
      <div class=""><input type="text" class="my-input" name="exposure_name" id="exposure_name" style="width:300px" />
      <a href="javascript:void(0)" class="publish-hw p10 add-new-exposure"><?=lang("txt_add")?></a>
      <?php
      if($hasExposure->step_count==0){
      ?>
      <br><div style="font-size:12px;color:red;"><?=lang("txt_empty_exposure")?></div>
      <?php
        }
      ?>
      </div>
      <div class="eposure-list">
          <table class="gridtable grid1 exposure-grid"  id="exposure-list-table" cellpadding="0" cellspacing="0">
              <thead>
                <th width="5%"><?=lang("txt_sn")?></th>
                <th width="25%"><?=lang("exposure")?></th>
                <th width="10%"><?=lang("txt_started_date")?></th>
                <th width="15%"><?=lang("txt_closed_date")?></th>
                <th width="15%"><?=lang("txt_no_of_exposures")?></th>
                <th width="10%"><?=lang("txt_status")?></th>
                <?php if ($usertype=='Psychologist'): ?>
                <th width="10%"><?=lang("txt_complete")?></th>
                <th width="10%"><?=lang("remove")?></th>
              <?php endif;?>
              </thead>
              <tbody class="tbody-exposure">
              <?php
                  $exposures = $this->minapp_model->fetchPatientsExposure($userId,$user['difficulty_id']);
                  if(count($exposures)>0){
                      $sn = 1;
                      foreach($exposures as $exp){
                ?>
                    <tr>
                        <td><?=$sn?></td>
                        <td><?=$exp->exposure_name?></td>
                        <td><?=date("Y-m-d", strtotime($exp->started_date))?></td>
                        <td class="td-closed"><?php
                            if(trim($exp->closed_date)!=""){
                               echo date("Y-m-d", strtotime($exp->closed_date));
                            }
                        ?>
                        </td>
                        <td><?=$exp->no_of_exposures?></td>
                        <td class="td-exposure-status">
                          <?php
                          if($exp->exposure_status==1){
                             echo lang("txt_ongoing");
                          }else if($exp->exposure_status==2){
                             echo lang("txt_completed");
                          }else if($exp->exposure_status==0){
                             echo lang("txt_removed");
                          }
                          ?>
                        </td>
                        <?php if ($usertype=='Psychologist'): ?>
                        <td class="td-complete"><a href="javascript:void(0)" data-exposureid='<?=$exp->exposure_id?>' class='link-complete-exposure'>
                          <?
                            if($exp->exposure_status!=2){
                              echo lang("txt_complete");
                            }
                          ?>
                          </a>
                        </td>
                        <td><a href="javascript:void(0)" data-exposureid='<?=$exp->exposure_id?>' class='link-remove-exposure'><?=lang("remove")?></a></td>
                      <?php endif;?>
                    </tr>
                <?php
                          $sn++;
                      }
                  }else{
                     echo "<tr class='no-exposures'><td colspan='8'>".lang("txt_no_exposures_avail")."</td></tr>";
                  }
              ?>
              </tbody>
          </table>
          <div class="clear"></div>
      </div>
  </div>
</div>
<?php
endif; //End of if for exposure check.

}//END => show registration, crisisplan, homeworks, etc contents only if the current patient has self harm enabled.

?>
<!-- Added by Sabin @17th July 2015 << -->


<!-- <p>&nbsp;</p> -->
</div>


<?php if ($usertype=='Psychologist'): ?>
<div class="buttonWrapper">
    <?php
      if($tag!=3):
    ?>
    <a href="javascript:void(0)" onclick="assignToUser('<?=$userId?>','<?=$user['username']?>','<?=$user['difficulty_id']?>')">
        <span class="btnbluenormal col1 marginbtnsr"><?=lang('btn_assign_problem_task')?></span>
    </a>
    <?php
      endif;
    ?>

    <a href="javascript:void(0)" onclick="changeReminderSettings('<?=$userId?>','<?=$user['username']?>','<?=$user['difficulty_id']?>')">
            <span class="btnbluenormal col1 marginbtnslr" style=""><?=lang("txt_reminder_btn")?></span>
        </a>

      <!-- Added By Sabin @ 3rd April 2015 START -->
    <!--  <a href="javascript:void(0)" onclick="showRegistrationTaskList('<?=$userId?>','<?=$user['username']?>','<?=$user['difficulty_id']?>')">
        <span class="btnbluenormal col1 marginbtnslr" style="margin-left:20px"><?=lang("txt_registration")?></span>
    </a>  -->
    <!-- Added By Sabin @ 3rd April 2015 END -->

     <!-- Added By Sabin @ 22nd June 2015 START -->
  <!--   <a href="javascript:void(0)" onclick="showHomeworkList('<?=$userId?>','<?=$user['username']?>','<?=$user['difficulty_id']?>')">
        <span class="btnbluenormal col1 marginbtnslr" style="margin-left:20px"><?=lang("txt_manage_my_homework")?></span>
    </a> -->
      <!-- <a href="javascript:void(0)" onclick="showCrisisplanList('<?=$userId?>','<?=$user['username']?>','<?=$user['difficulty_id']?>')">
        <span class="btnbluenormal col1 marginbtnslr" style="margin-left:20px"><?=lang("txt_manage_crisis_plan")?></span>
    </a> -->
    <!-- Added By Sabin @ 22nd June 2015 END -->
</div>
<?php else: ?>
	<div class="buttonWrapper">
  <?php if ($user['patient_access_create'] && $tag!=3): ?>
    <a href="javascript:void(0)" onclick="addTaskToMobile('<?=$userId?>','<?=$user['username']?>','<?=$user['difficulty_id']?>')">
        <span class="btnbluenormal col1 marginbtnsr"><?=lang('btn_assign_task_mobil')?></span>
    </a>
  <?php endif ?>

    <?php if ($user['patient_access']): ?>
     <a href="javascript:void(0)" onclick="changeReminderSettings('<?=$userId?>','<?=$user['username']?>','<?=$user['difficulty_id']?>')">
            <span class="btnbluenormal col1 marginbtnslr" style="margin-left:20px"><?php echo lang("txt_reminder_btn")?></span>
        </a>
    <?php endif ?>
</div>
<?php endif ?>
<?php
if($tag!=3):
?>
<h2 class="tableTitle blackBar"><?=lang('txt_ur_training')?></h2>
  <div style="float:right">
      <div class="text12">
          <img style="margin-top: -3px;" src="<?php echo base_url() ?>assets/public/css/images/green_dot.png" align="ABSMIDDLE" />Ny kommentar &nbsp;&nbsp;
          <img style="margin-top: -3px;" src="<?php echo base_url() ?>assets/public/css/images/grey_dot.png" align="ABSMIDDLE"/>Läst kommentar
      </div>
  </div>
    <?php if(!empty($tasks)) { ?>
        <table class="gridtable" cellspacing="0" cellpadding="0" border="0" width="782px">
            <thead>
                <tr>
                    <th><?=lang('txt_sn')?></th>
                    <th>&nbsp;</th>
                    <th><?=lang('txt_task')?></th>
                    <th><?=lang('txt_started_at')?></th>
                    <th><?=lang('txt_completed')?></th>
                    <th><?=lang('txt_activity')?></th>
                    <th><?=lang('txt_status')?></th>
                    <?php if ($usertype=='Psychologist'): ?>
                    <th>&nbsp;</th>
                    <?php endif ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $tk => $task):

                $training = $this->minapp_model->getTrainingInfo($userId,$task->id);
                $poll = $this->minapp_model->getPollingByTask($usertype,$user['psychologist_id'],$user['id'],$task->id);

                $jsonStr = $this->minapp_model->getTaskCompletionInfo($userId,$task->id);
                $json  = json_decode($jsonStr,true);

                $countNewComment = $this->minapp_model->countComments($usertype,$user['psychologist_id'],$user['id'],$task->id,'1');
                $countOldComment = $this->minapp_model->countComments($usertype,$user['psychologist_id'],$user['id'],$task->id,'0');

                if ($countNewComment > 0) :
                    $comment_status = '<img style="margin-top: 6px;" src="' . base_url() . 'assets/public/css/images/green_dot.png"> ';
                elseif ($countOldComment) :
                    $comment_status = '<img style="margin-top: 6px;" src="' . base_url() . 'assets/public/css/images/grey_dot.png"> ';
                else:
                    $comment_status = '&nbsp;';
                endif;
                $viewLink = base_url().'index.php/minapp/activityReport/'.$user['username'].'/'.$task->id;
             ?>
                <tr data-link="<?=$viewLink?>" class="activity_tr">
                    <td><?=$tk+1?></td>
                    <td><?=$comment_status?></td>
                    <td><?=$task->task?></td>
                    <td><?php echo ($training->started_at<'2013-01-01') ? '': date("Y-m-d",strtotime($training->started_at));?></td>
                    <td><?=$json[$userId]?></td>
                    <td class="noactivity_td"><a class="graphshow" href="javascript:void(0);" data-total='<?=$training->total?>' data-userid='<?=$userId?>' data-taskid='<?=$task->id?>'><?=lang('txt_see_activity')?></a></td>
                    <td>
                         <?php if (is_array($json) && array_key_exists($userId, $json)): ?>
                             <?=lang('txt_inactive')?>
                         <?php else: ?>
                            <?=lang('txt_active')?>
                         <?php endif ?>
                    </td>
                    <?php if ($usertype=='Psychologist'): ?>
                         <?php if (is_array($json) && array_key_exists($userId, $json)): ?>
                            <td class="noactivity_td">
                            <a href="javascript:void(0);" onclick="toggleTaskStatus('reopen','<?=$userId?>','<?=$task->id?>');"><?=lang('txt_reactivate')?>
                            </a>
                            </td>
                         <?php else: ?>
                            <td class="noactivity_td"><a href="javascript:void(0);" onclick="toggleTaskStatus('closed','<?=$userId?>','<?=$task->id?>');"><?=lang('txt_deactivate')?></a></td>
                         <?php endif ?>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php
    } else {
        echo '<p>No training activity recorded.</p>';
    }
    ?>
</div>
<div id="showComment">
</div>
<?php
endif;
?>
<script type="text/javascript">

    $(document).ready(function() {

      $('.activity_tr td').not('.noactivity_td').click(function(e) {
        window.location.href = $(this).parent().attr('data-link');
      });

      $("#list-special-answers").dropdownchecklist({
            emptyText: "<i><?=lang('txt_select_special_answers')?></i>",
            width: 300,
            onItemClick: function(checkbox,selector){
              $selval = checkbox.val();

              if(checkbox.prop("checked")==true){
                $("#"+selector.id+" option[value='"+$selval+"']").prop("selected",true);
              }else{
                $("#"+selector.id+" option[value='"+$selval+"']").prop("selected",false);
              }
              saveSelectedAnswers(selector);
            }
      });

      $(".add-new-exposure").click(function(){
          $exposure_name = $.trim($("#exposure_name").val());
          if($exposure_name=="") return false;

          $.ajax({
               url: $sitePath + "/minapp/minapp/addNewPatientExposure",
               data: "exposure_name="+$exposure_name+"&user_id=<?=$userId?>&difficulty_id=<?=$user['difficulty_id']?>",
               type: "post",
               dataType: "json",
               beforeSend: function(){
                  $.fancybox.showActivity();
               },
               success: function(data){
                  if(data.status=="ok"){
                    var userType = "<?=$usertype?>";
                    $var = data.returnvar;
                    $trs = $("table").find("tbody.tbody-exposure").find("tr").length+1;
                    $html ="";

                    $html += "<tr>";
                    $html += "<td>"+$trs+"</td>";
                    $html += "<td>"+$var.exposure_name+"</td>";
                    $html += "<td>"+$var.start_date+"</td>";
                    $html += "<td class='td-closed'>&nbsp;</td>";
                    $html += "<td>0</td>";
                    $html += "<td>"+$var.status+"</td>";

                    if(userType=="Psychologist"){
                      $html += "<td class='td-complete'><a href='javascript:void(0)' data-exposureid='"+$var.exposure_id+"' class='link-complete-exposure'>"+$var.complete+"</a></td>";
                      $html += "<td><a href='javascript:void(0)' data-exposureid='"+$var.exposure_id+"' class='link-remove-exposure'>"+$var.remove+"</a></td>";
                    }

                    $html += "</tr>";

                    $("table").find("tbody.tbody-exposure").find("tr.no-exposures").remove();
                    $("table").find("tbody.tbody-exposure").append($html);

                    $("#exposure_name").val("");

                    $.fancybox.hideActivity();

                    $("table").find("tbody.tbody-exposure").find("tr").each(function(index){
                      $(this).children("td:first").html(index+1);
                    });
                  }else{
                     $.fancybox.hideActivity();
                      alert(data.error_message);
                  }
               }
          });

      });


      $(".link-remove-exposure").live("click",function(){
            $exposure_id  = $(this).attr("data-exposureid");
            $obj = $(this);
            if(confirm($jsLang["remove_exposure"])){
                  $.ajax({
                      url: $sitePath + "/minapp/minapp/removePatientExposure",
                      data: "exposure_id="+$exposure_id,
                      type: "post",
                      dataType: "json",
                      beforeSend: function(){
                           $.fancybox.showActivity();
                      },
                      success: function(data){
                        if(data.status=="ok"){
                            $obj.closest("tr").fadeOut("slow", function(){
                                $(this).remove();
                                 $("table").find("tbody.tbody-exposure").find("tr").each(function(index){
                                    $(this).children("td:first").html(index+1);
                                });
                            });

                        }else{
                            alert(data.error_message);
                        }

                        $.fancybox.hideActivity();
                      }
                  });
            }
      });


      $(".link-complete-exposure").live("click",function(){
            $exposure_id  = $(this).attr("data-exposureid");
            $obj = $(this);
            if(confirm($jsLang["complete_exposure"])){
                  $.ajax({
                      url: $sitePath + "/minapp/minapp/completePatientExposure",
                      data: "exposure_id="+$exposure_id,
                      type: "post",
                      dataType: "json",
                      beforeSend: function(){
                           $.fancybox.showActivity();
                      },
                      success: function(data){
                        if(data.status=="ok"){
                            $obj.closest("tr").find("td.td-closed").html(data.closed_date);
                            $obj.closest("tr").find("td.td-exposure-status").html(data.new_status);
                            $obj.closest("tr").find("td.td-complete").empty();
                        }else{
                            alert(data.error_message);
                        }

                        $.fancybox.hideActivity();
                      }
                  });
            }
      });

      $(".save-patient-special-answer").click(function(){
            $answer = $.trim($("#custom_answer").val());
            if($answer==""){
              alert("Empty field submitted.");
              return false;
            }

            $.ajax({
              url: $sitePath + "/minapp/minapp/saveSpecialAnswer",
              data: "new_answer="+$answer+"&added_by=psychologist&patient_id=<?=$userId?>&difficulty_id=<?=$user['difficulty_id']?>",
              type: "post",
              dataType: "json",
              success: function(data){
                if(data.status=="ok"){
                  $("#custom_answer").val("");
                  $("#list-special-answers").dropdownchecklist("destroy");
                  $("#list-special-answers").append("<option value='"+data.answer_id+"' selected='selected'>"+data.answer+"</option>");
                  $("#list-special-answers").dropdownchecklist( { emptyText: "<i><?=lang('txt_select_special_answers')?></i>", width: 300 } );
                }else{
                  alert(data.message);
                }
              }
          });
      });

      /*$(".see-all-registration").click(function(){
            var userID = "<?=$userId?>";
            $.
      });*/

      $(".see-all-registration").fancybox({
          ajax : {
              type  : "POST",
              data  : 'userID=<?=$userId?>'
          }
      });


      $(".see-patients-answer").fancybox({
          ajax : {
              type  : "POST",
              data  : 'userID=<?=$userId?>&difficulty_id=<?=$user["difficulty_id"]?>'
          }
      });


      $(".btn-update-crisis-list").fancybox({
          ajax: {
              type : "POST",
              data : "user_id="+$(".btn-update-crisis-list").attr("data-userid")+"&username="+$(".btn-update-crisis-list").attr("data-username")+"&diffId="+$(".btn-update-crisis-list").attr("data-diffid"),
              beforeSend: function(){
                 destroyTinyMCE();
              }

          }
      });



      $(".btn-manage-homework").fancybox({
          ajax: {
              type: "POST",
              data: "user_id="+$(".btn-manage-homework").attr("data-userid")+"&username="+$(".btn-manage-homework").attr("data-username")+"&diffId="+$(".btn-manage-homework").attr("data-diffid")
          }
      });


      $(".btn-manage-activation-modules").fancybox({
      		autoDimensions:false,
      		width: 800,
      		height: 600,
	        ajax: {
		              type: "POST",
		              data: "user_id="+$(".btn-manage-activation-modules").attr("data-userid")+"&username="+$(".btn-manage-activation-modules").attr("data-username")+"&diffId="+$(".btn-manage-activation-modules").attr("data-diffid"),
		               complete: function(jqXHR, textStatus) {
					            $("#selHomework").dropdownchecklist( { firstItemChecksAll:false,emptyText: "<i>Select Homework </i>", width: 310 } );
											$("#selModules").dropdownchecklist( { firstItemChecksAll:false,emptyText: "<i>Select Modules </i>", width: 310 } );
											toggleModuleCheckboxes();
											$.fancybox.resize();
					        }
		          }
      });

      $(".btn-view-activity-thoughts").fancybox({
            ajax: {
                type: "POST",
                data: "user_id="+$(".btn-view-activity-thoughts").attr("data-userid")
            }
      });


      $(".btn-view-activity-feelings").fancybox({
          ajax: {
              type: "POST",
              data: "user_id="+$(".btn-view-activity-feelings").attr("data-userid")
          }
      });


      $(".btn-view-activity-skills").fancybox({
          ajax: {
              type: "POST",
              data: "user_id="+$(".btn-view-activity-skills").attr("data-userid")
          }
      });



      $printurl=$baseUrl+"assets/app?json=";
      $('.graphshow').click(function() {
          if ($(this).attr('data-total')==0) {
            alert('No practice yet.');return false;
          }
          $.ajax(
          {
              type:'post',
              url: $sitePath+"/minapp/minapp/getGraphInput",
              dataType: 'json',
              cache: false,
              data:{
                  task_id:$(this).attr('data-taskid'),
                  user_id:$(this).attr('data-userid')
              },
              success: function(response)
              {
                  $str_json = JSON.stringify(response);
                  $url = $printurl+encodeURIComponent($str_json);
                  $.fancybox({
                      'width'             : 480,
                      'height'            : 425,
                      'autoScale'         : false,
                      'transitionIn'      : 'none',
                      'transitionOut'     : 'none',
                      'type'              : 'iframe',
                      'href'              : $url
                  });
              }
          });
      });
    });

function saveSelectedAnswers(selector){
    var values = "";
        for( i=0; i < selector.options.length; i++ ) {
            if (selector.options[i].selected && (selector.options[i].value != "")) {
                if ( values != "" ) values += ",";
                values += selector.options[i].value;
            }
        }

    $.ajax({
        url: $sitePath + "/minapp/minapp/saveSelectedAnswers",
        type: "post",
        data: "answers="+values+"&userid=<?=$userId?>&difficulty_id=<?=$user['difficulty_id']?>",
        dataType: "json",
        success: function(data){
          if(data.status=="ok"){

          }else{
              alert(data.message);
          }
        }
    });
}

function destroyTinyMCE(){
    console.warn("TinyMCE Destroyed!!");
    if(typeof tinyMCE!="undefined") tinyMCE.remove();
}
</script>

