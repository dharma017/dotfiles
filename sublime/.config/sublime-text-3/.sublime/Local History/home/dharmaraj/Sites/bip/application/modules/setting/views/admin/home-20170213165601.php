<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/admin/js/form-validator/jquery.validate.password.css"/>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/js/form-validator/jquery.validate.password.js"></script>
<script language="javascript">
// Tabs switching
$(document).ready(function(){
		$('#box1 .content#box-difficulty').hide(); // hide content related to inactive tab by default
		$('#box1 .content#box-pages').hide();
		$('#box1 .content#box-group').hide();
		$('#box1 .content#box-timer').hide();
		$('#box1 .content#box-account').hide();
		$('#box1 .content#box-auto_message').hide();
		$('#box1 .content#box-copy').hide();
		$('#box1 .content#box-account').hide();

		$('#box1 .header ul a').click(function(){
			$('#button_container a').hide(); //
			$('#box1 #'+$(this).attr('title')).css('display','inline-block'); // and
			$('#box1 .header ul a').removeClass('active');
			$(this).addClass('active'); // make clicked tab active
			$('#box1 .content').hide(); // hide all content
			$('#box1 #'+$(this).attr('rel')).show(); // and show content related to clicked tab
			return false;
		});
	});
</script>
<div id="box1" class="box box-100"  style="margin:28px 0 0;padding:0px"><!-- box full-width -->
  <div class="boxin">
    <div class="header">
    <div id="button_container">
      <h3 style="float:left;">Settings</h3>
      <a id="Icon" class="savebtns" href="#" style="margin-left:10px; display:inline-block;"  onclick="addIconForm();">Add Icons&nbsp;</a>
		<?php
			$permission = $this->setting_model->getPermissionOfPsy();
			$permission = json_decode($permission,true);
			if(empty($permission) || $permission['other_rights']['create_new_difficulty'] == 1)
			{
				echo '<a id="Difficulty" class="savebtns" href="#" style="margin-left:10px; display:none;" onclick="addDifficultyForm();">Add Difficulty&nbsp;</a>';
			}
			if(empty($permission) || $permission['other_rights']['create_new_group'] == 1)
			{
				echo '<a id="Group" class="savebtns" href="#" style="margin-left:10px; display:none;" onclick="addGroupForm();">Add Group&nbsp;</a>';
			}
		?>
		<a id="Account" class="savebtns" href="#" style="margin-left:10px; display:none;"  onclick="addAccountForm();">Add Account</a>
    </div>
      <ul style="float:right">
        <li><a rel="box-icon" href="#Icon" class="active" title="Icon">Icon</a></li>
        <li><a rel="box-difficulty" href="#Difficulty" onclick="listDifficulty();" title="Difficulty">Difficulty</a></li>
		<li><a rel="box-group" href="#group"  title="Group">Group</a></li>

		<?php if(empty($permission))
				echo '<li><a rel="box-auto_message" href="#auto_message"  title="Automated Message">Automated Message</a></li>';
		?>

		<li><a rel="box-timer" href="#timer"  title="setting">Settings</a></li>
		<li><a rel="box-copy" href="#copy" onclick="listOthersDifficulty();" title="Copy Treatment">Copy Treatment</a></li>

		<?php if (empty($permission)): ?>
			<li><a rel="box-account" href="#Account" onclick="listAccounts();" title="Account">Superadmin Accounts</a></li>
		<?php endif; ?>

      </ul>
    </div>


	    <div id="box-icon" class="content"><!-- content box icon for tab switching -->
	       <?php $this->load->view('setting/admin/icon_list');?>
	    </div>

	    <div id="box-difficulty" class="content"><!-- content box difficulty for tab switching -->
	       <?php $this->load->view('setting/admin/difficulty_list');?>
	    </div>

	    <div id="box-group" class="content"><!-- content box difficulty for tab switching -->
	        <?php $this->load->view('setting/admin/group_list');?>
	    </div>

		<?php if (empty($permission)): ?>
		<div id="box-auto_message" class="content"><!-- content box sms message for tab switching -->
	        <?php $this->load->view('setting/admin/auto_message');?>
	    </div>
	    <?php endif; ?>

	    <div id="box-timer" class="content"><!-- content box difficulty for tab switching -->
	        <?php $this->load->view('setting/admin/system_setting');?>
	    </div>

		<div id="box-copy" class="content">
	        <?php $this->load->view('setting/admin/copy_difficulty_list');?>
	    </div>

	    <?php if (empty($permission)): ?>
	    <div id="box-account" class="content">
	        <?php echo $this->load->view('setting/admin/account_list');?>
	    </div>

	    <?php endif; ?>

  </div>
</div>
