<?php
$usertype = getUserType();
$patientId = $this->session->userdata("p_id");
$userId = $this->session->userdata("user_id");


$data["patient_id"] = $patientId;
$data["user_type"] = $this->session->userdata("user_role_type");
//echo "<pre>".print_r($loginReportData,true)."</pre>"; exit;
//$login_message = json_decode($rows->message,true);
$rows->first_name = $this->encryption->decrypt($rows->first_name);
$rows->last_name = $this->encryption->decrypt($rows->last_name);
$rows->message = $this->encryption->decrypt($rows->message);
$rows->message = stripslashes($rows->message);
//dd($rows->message);
$data["templateData"] = json_decode($rows->message);
$data['selectedQuestion'] = $rows->selectedQuestion;
$data["templateId"] = $rows->templateId;
$data["stepId"] = $rows->step_id;
$data["templateForm"] = $this->stage_model->getDetailByTblNameStepId('bip_form', $rows->step_id, 'id');
$stepId = $data["stepId"];

//$data["formData"]	  = $this->worksheet_model->getFormDetailByStepId($rows->stepId);
// assigning the worksheet template categrory as per the template type id
if ($rows->templateId == "4") {
    $templateType = "text";
} else if ($rows->templateId == "10") {
    $templateType = "checkbox";
    $stageId = $this->session->userdata('sess_stage_id');
    $this->session->unset_userdata("SESS_USER_DATA[$stageId][$stepId]['label']");

    $this->session->unset_userdata("SESS_USER_DATA[$stageId][$stepId]['data']");

} else if ($rows->templateId == "5" || $rows->templateId == "7" || $rows->templateId == "8 ") {
    $templateType = "radio";
} else if ($rows->templateId == "9") {
    $templateType = "radio_9";
} else if ($rows->templateId == "11") {
    $templateType = "11";
} else if ($rows->templateId == "6") {
    $templateType = "ladder";
} else if ($rows->templateId == "11") {
    $templateType = "abc";
} else if ($rows->templateId == "12") {
    $templateType = "ladder_followup";
} else if ($rows->templateId == "13") {
    $templateType = "13";
} else if ($rows->templateId == "14") {
    $templateType = "14";
} else if ($rows->templateId == "16") {
    $templateType = "abc_16";
} else if ($rows->templateId == "18") {
    $templateType = "18";
} else if ($rows->templateId == "21") {
    $templateType = "21";
}else if ($rows->templateId == "22") {
    $templateType = "22";
}else if ($rows->templateId == "23") {
    $templateType = "23";
}

if ($view_type != "ajax") {
	echo '<div class="formentry">
    <form method="post" name="frmBip"  id="frmBip">';
}
?>


<?php if (!empty($rows->templateId)): ?>

<ul class="form_dataEmail ws_template_<?php echo $templateType?>">
    <?php if ($usertype == 'Psychologist') { ?>
        <li>
            <label><strong><?= lang('worksheet_username') ?></strong></label>
            : <?php echo $rows->first_name . ' ' . $rows->last_name; ?> </li>
    <?php } ?>
    <li>
        <label><strong><?= lang('part') ?>:&nbsp;</strong></label>
        <?php echo $rows->stage; ?></li>
    <li>
        <label><strong><?= lang('txt_slide') ?>:&nbsp; </strong> </label>
        <?php echo $rows->step; ?></li>
        <?php if($created_date->send_date){ ?>
        <li>
        <label><strong><?= lang('created_on') ?>:&nbsp; </strong> </label>
        <?php echo format_date($created_date->send_date); ?></li> <?php }
        if($rows->last_updated){ ?>
        <li>
        <label><strong><?= lang('last_updated_on') ?>:&nbsp; </strong> </label>
        <?php echo format_date($rows->last_updated); ?></li> <?php } ?>
            <li id="containerFormData">
        <?php $this->load->view("worksheet/template/template_" . $templateType, $data);
        ?>
    </li>
</ul>
<?php else: ?>
<ul class="form_dataEmail ws_template_<?php echo $templateType?>">
    <li>
        <label><strong>Option :&nbsp; </strong></label>
        <?php
        foreach ($loginReportData as $p) {
    $login_message = json_decode($p->message,true);
    if($login_message['option'] == 0){
                        $msg="I did not get the SMS-code";
                    }else if($login_message['option'] == 1)
                    {
                        $msg="Got the SMS-code, but it did not work";
                    }else{
                        $msg="Other";
                    }
    echo $msg;
    }
    ?>
    </li>

       <?php
        foreach ($loginReportData as $p) {
            if($login_message['message'] !== ""){
            echo '<li>
        <label><strong>Message :&nbsp;</strong> </label>';
    $login_message = json_decode($p->message,true);
    echo $login_message['message'];
}
    }
    ?>
    </li>

</ul>
<?php endif ?>

<?php if ($view_type != "ajax") { ?>
    <input type="hidden" name="templateId" id="templateId" value="<?php echo $rows->templateId; ?>" />
    <input type="hidden" name="worksheet_id" id="worksheet_id" value="<?php echo $cur_id ?>"  />
    <input type="hidden" name="stepId" id="stepId" value="<?php echo $rows->step_id; ?>" />
    <input type="button" value="<?= lang('save') ?>"  id="update_worksheet" class="btnsikall" style="">
    <input type="hidden" value="0"  id="form_editable">

    <!-- </form> -->
    <!-- </form></div> -->
<?php }?>
<?php if ($view_type != "ajax") { echo '</form></div>'; } ?>
<?php if ($view_type == "ajax") die(); ?>
