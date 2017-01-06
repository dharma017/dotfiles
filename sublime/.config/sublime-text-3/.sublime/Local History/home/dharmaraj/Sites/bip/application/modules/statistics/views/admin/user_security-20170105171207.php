<?php
$usertype = $this->session->userdata('user_role_type');
$logintype = $this->session->userdata('logintype');
	$users = $this->user_model->listNormalUsers();
	$psychologists = $this->user_model->getAllpsychologyByLang();
	$superadmins 	= $this->setting_model->getAccounts();
?>
<?php $userId = $this->session->userdata('user_id');
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
//dd($psy_arr_not_unique);
foreach ($psy_arr_not_unique as $psy_stat) {
	$hash = $psy_stat->id;
    $psy_arr[$hash] = $psy_stat;
}
//$psy_arr = array_unique($psy_arr);
			//dd($psy_arr);
			//$grp_psy = $this->user_model->getPsychologyByGroup($grp);
			//dd($psy_grp);
?>
<div id="box-security" class="content">

<form id="frmAddStat" method="post" name="frmAddStat" action="<?=base_url().'index.php/statistics/admin/generate_security_report/'?>" onsubmit="return validateAccessActivityForm()">
<fieldset>
	<ul class="adm-form access-role">
		<li>
			<div style="float:left">
				<label class="label"><strong>Report</strong></label>
				<select name="role-select" id="role-select">
						<option selected value="1">Patient</option>
						<option value="2">Psychologist</option>
						<?php if($usertype == "superadmin" && $logintype == "admin"){ ?>
						<option value="3">Superadmin</option>
						<?php } ?>
				</select>
				</div>

				<div style="float:right;margin-right: 319px;">
				<input name="all_report" type="hidden" value="0">
				<input name="all_report" type="checkbox" id="all_report" value="1">

				<label  style="width: auto; padding: 4px;" class="label" for="all_report"><strong>All Report</strong></label>
		</div>
		</li>

		<li class="role-superadmin desc">
			<div style="float:left">
			<label class="label"><strong>Superadmin</strong></label>
			<select name="super-select" id="super-select">
				<option value="">Select Superadmin</option>
                <?php foreach ($superadmins as $user):
                    $user->first_name = $this->encryption->decrypt($user->first_name);
                    $user->last_name = $this->encryption->decrypt($user->last_name);
                ?>
					<option value="<?=$user->id?>"><?php echo $user->first_name.' '.$user->last_name; ?></option>
				<?php endforeach ?>
			</select>
			</div>
		</li>

		<li class="role-psychologist desc">
			<div style="float:left">
			<label class="label"><strong>Psychologist</strong></label>
			<select name="psy-select" id="psy-select">
				<option value="">Select Psychologist</option>
				<<?php if($usertype == "superadmin" && $logintype == "admin"){
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
			</div>
		</li>

		<li class="user-li role-user desc">
			<label class="label"><strong>Patient</strong></label>
			<select name="psy-usr-select" id="psy-usr-select">
				<option value="">Select Patient</option>
			</select>

		</li>

		<div class="date-li">
		 <li>
			<label class="label"><strong>Date from:</strong></label>
			<input type="text" class="inputs from" readonly='true' name="from_date" value=""/>
			<label style="margin-left: 47px; width: 51px;padding-top: 5px !important;"><strong>Date to:</strong></label>
			<input type="text" name="to_date" readonly='true' class="inputs to" value="" />
		</li>
		</div>

		<li>
		<div style="float:left;margin: -7px;">
			<input checked name="report_type" type="radio" id="login_log" value="login" style="margin: 7px;">
			<label  style="width: auto;" class="label" for="login_log"><strong>Login Log</strong></label>
		</div>
		</li>

		<?php if($usertype == "superadmin" && $logintype == "admin"){ ?>
		<li>
		<div style="float:left;margin: -7px;">
			<input name="report_type" type="radio" id="access_log" value="access" style="margin: 7px;">
			<label  style="width: auto;" class="label" for="access_log"><strong>Access Log</strong></label>
		</div>
		</li>
		<?php } ?>

		<input type="hidden" id="radioType" name="radioType" value="opt6">

		<li><input type="submit" value="Generate Report" class="buttonlng generatexls"></li>


	</ul>
</fieldset>
</form>
</div>
