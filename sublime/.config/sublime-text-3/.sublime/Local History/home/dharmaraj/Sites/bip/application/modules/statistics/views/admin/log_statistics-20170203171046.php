<?php
$users = $this->user_model->listNormalUsers();
$groups = $this->user_model->getAllGroupByLang();
$psychologists = $this->user_model->getAllpsychologyByLang();
?>
<?php $userId = $this->session->userdata('user_id');
$usertype = $this->session->userdata('user_role_type');
//echo $usertype;exit;
$logintype = $this->session->userdata('logintype');
$sess_permission = $this->user_model->getUserByUserId($userId)->permission;
$sess_permission = json_decode($sess_permission,true);
$group_permission = $sess_permission['rights_per_group'];
		//print_r($group_permission);exit;
foreach ($group_permission as $key => $value) {
	$psy_all_grp[] = $key;
	if($value['extract_data'] == 1){
		$group[] = $key;
	}
}
		//print_r($psy_all_grp);
if(!empty($group) && is_array($group)){
	$grp = join(',',$group);
	$grp = rtrim($grp,",");
}
			//print_r($grp);
$psy_grp = $this->user_model->getGroupOFPsy($grp);
$psy_arr = array();
foreach ($psychologists as $key => $psy) {
				//$per = json_encode($psy->permission['rights_per_group']);
	$per = json_decode($psy->permission,true);
				//print_r($per['rights_per_group']);
	foreach ($per['rights_per_group'] as $group_id => $group_values) {
					//echo $group_id;
					//print_r($grp);
		if (in_array($group_id, $group)) {
			$psy_arr_not_unique[] = $psy;
		}
	}
}
foreach ($psy_arr_not_unique as $psy_stat) {
	$hash = $psy_stat->id;
    $psy_arr[$hash] = $psy_stat;
}
			//dd($psy_arr);
			//$grp_psy = $this->user_model->getPsychologyByGroup($grp);
			//dd($psy_grp);
?>
<div id="box-log" class="content">

<form id="frmAddStat" method="post" name="frmAddStat" action="<?=base_url().'index.php/statistics/admin/generate_log_report/'?>" onsubmit="return validateLogStatForm()">
<fieldset>
    <ul class="adm-form">

        <div class="">
        <li>
            <label class="label"><strong>Psychologist</strong></label>
            <select name="psy-select" id="psy-select-log">
                <option value="">Select Psychologist</option>
				<?php if($usertype == "superadmin" && $logintype == "admin"){
									foreach($psychologists as $user){
$user->first_name = $this->encryption->decrypt($user->first_name);
                    $user->last_name = $this->encryption->decrypt($user->last_name);
 ?>
                    <option value="<?=$user->id?>"><?php echo $user->first_name.' '.$user->last_name; ?></option>
							<?php }}else{ 
									foreach($psy_arr as $user){
$user->first_name = $this->encryption->decrypt($user->first_name);
                    $user->last_name = $this->encryption->decrypt($user->last_name);
 ?>
									<option value="<?=$user->id?>"><?php echo $user->first_name.' '.$user->last_name; ?></option>
							<?php }} ?>
            </select>
        </li>
        <li style="display:none">
            <label class="label"><strong>User</strong></label>
            <select name="psy-usr-select" id="psy-usr-select-log">
                <option value="">Select User</option>
            </select>
        </li>

        </div>

        <div class="date-li">
         <li>
            <label class="label"><strong>Date from:</strong></label>
            <input type="text" class="inputs from" readonly='true' name="from_date" value=""/>
            <label style="margin-left: 47px; width: 51px;padding-top: 5px !important;"><strong>Date to:</strong></label>
            <input type="text" name="to_date" readonly='true' class="inputs to" value="" />
        </li>
        </div>

        <input type="hidden" id="radioType" name="radioType" value="opt3">

        <li><input type="submit" value="Generate Report" class="buttonlng generatexls"></li>

    </ul>
</fieldset>
</form>
</div>
