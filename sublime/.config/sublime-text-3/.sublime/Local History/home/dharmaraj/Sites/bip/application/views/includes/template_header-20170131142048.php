<?php

$skins = $this->session->userdata('skins');
$segment2 =  $this->uri->segment(2);
if (!empty($skins) && $skins!='default') {
	 $activeTitle = (!empty($segment2) && $segment2=='viewStep') ? 'active': '';
	 $activeStart = (empty($segment2) || $segment2!='viewStep') ? 'active': '';
}else{
 	$activeTitle = '';
 	$activeStart = '';
}

$language_code = $this->session->userdata('language_code');
$colour = "grey";
if ($this->session->userdata("bip_logged_in")) {
	$this->load->model('messages/messages_model');
	$this->load->model('worksheet/worksheet_model');
	$user_type = $this->session->userdata("logintype");
	if($user_type == "admin")
	{
		$user_permission = $this->session->userdata("permission");
		if(!empty($user_permission))
			$user_type = "Psychologist";
	}

	$notificationCounter = $this->stage_model->getDashboardDataCount();
}
// dd($notificationCounter);

$minappLink = $this->session->userdata('minappLink');
$minappChangedLink = (!empty($minappLink)) ? base_url() .$minappLink: site_url('minapp');
?>
<div id="structuralWrapper" class="<?php echo 'color-'.$colour;?>  <?php echo ($user_type == 'Psychologist')  ? 'psycho': 'patient'?>">
	<div class="wrapperNew">
		<div id="logo"><a href="#"></a></div>
		<div class="tabLayer">
			<?php if ($this->session->userdata('sess_stage_id') && $this->session->userdata("bip_logged_in")) {
				$sess_step_id  = $this->session->userdata('sess_step_id_new');
				if(!empty($sess_step_id))
					$stage_link = site_url("stage/viewStep/".$this->session->userdata('sess_stage_id')."/".$sess_step_id);
				else
					$stage_link = site_url("stage/viewStep/".$this->session->userdata('sess_stage_id'));
			?>
			<div id="flexible">
				<span class="cross"></span>
				<a class="<?php echo $activeTitle;?>" href="<?php echo $stage_link; ?>"><span><?php echo $this->session->userdata("sess_stage_title"); ?></span></a>
			</div>
			<?php } ?>
			<div id="startTab">
				<span><a class="<?php echo $activeStart;?>" href="<?php $usertype = getUserType(); if($usertype == 'Psychologist') echo site_url("stage/personal"); else echo site_url("stage"); ?>"><?= lang('start') ?></a></span>
			</div>
		</div>
		<?php if (($this->session->userdata('bip_logged_in')) && ($this->session->userdata("user_id"))) { ?>
		<div  class="logout">
			<span class="textWelcome">
			<?= lang('logged_in_as') ?>: <?php echo $this->session->userdata('username'); ?>
			</span>
			<a  href="<?php echo site_url("login/logout") ?>"><?= lang('logout') ?></a>
		</div>
		<?php } ?>
	</div>
	<div class="topStructure" id="top" style="background:url(<?php echo base_url() ?>assets/public/css/images/<?php echo $colour; ?>/Top.png) no-repeat left top;"></div>
	<div class="middleStructure" id="middle" style="background:url(<?php echo base_url() ?>assets/public/css/images/<?php echo $colour; ?>/Middle.png) repeat-y">
		<div id="mainWrapper">
			<div class="wrapperNew">
				<div class="<?php
					if ($this->session->userdata("bip_logged_in")) {
						echo"notificationDiv";
					} else {
						echo"notificationDivs";
					}
					?> row <?php if($user_type == 'Psychologist') echo 'psycho';?>">
					<ul
						<?php
						$query = $this->db->query("select * from bip_user where id='" . $this->session->userdata("user_id") . "' and difficulty_id in (55,56) ");
						if ($query->num_rows != 0 && $user_type != 'Psychologist') {
							echo 'style="width:auto;"';
						} elseif ($user_type != 'Psychologist')
						echo 'style="width:auto;"';
						?>>
						<?php if ($user_type == 'Psychologist'):?>
						<li id="stage">
							<a href="<?php echo site_url("stage") ?>" alt="<?= lang('overview') ?>" title="<?= lang('overview') ?>" <?php if (($this->uri->segment(1) == "stage") && ($this->uri->segment(2) != "personal"))
								echo 'class="active"'; ?>>
								<span><?= lang('overview') ?></span>
							</a>
						</li>
						<li id="stage_personal" class="stopClick">
							<a href="<?php echo site_url("stage/personal") ?>" alt="<?= lang('start') ?>" title="<?= lang('start') ?>" <?php if (($this->uri->segment(1) == "stage") && ($this->uri->segment(2) == "personal"))
								echo 'class="active"'; ?>>
								<span><?= lang('start') ?></span>
							</a>
						</li>
						<?php else: ?>
						<li id="stage_personal" class="stopClick">
							<a href="<?php echo site_url("stage") ?>" alt="<?= lang('start') ?>" title="<?= lang('start') ?>" <?php if ($this->uri->segment(1) == "stage")
								echo 'class="active"'; ?>>
								<span><?= lang('start') ?></span>
							</a>
						</li>
						<?php endif;?>
						<li id="msg" class="stopClick">
							<a href="<?php echo site_url("messages") ?>" alt="<?= lang('messages') ?>" title="<?= lang('messages') ?>" <?php if ($this->uri->segment(1) == "messages")
								echo 'class="active"'; ?> id="link_msg">
								<span  class="numbers">
									<?php echo  (isset($notificationCounter['new_message'])) ? $notificationCounter['new_message']: 0 ;?>
								</span>
								<span><?= lang('messages') ?></span>
							</a>
						</li>
						<li id="file" class="stopClick">
							<a href="<?php echo site_url("worksheet") ?> " title="<?= lang('worksheet') ?>" <?php if ($this->uri->segment(1) == "worksheet")
								echo 'class="active"'; ?>>
								<span  id="sheet_polling" class="numbers">
									<?php echo  (isset($notificationCounter['unmodified'])) ? $notificationCounter['unmodified']: 0 ;?>
								</span>
								<span><?= lang('worksheet') ?></span>
							</a>
						</li>
						<li id="worksheet">
							<a href="<?php echo site_url("file") ?>" title="<?= lang('file') ?>" <?php if ($this->uri->segment(1) == "file")
								echo 'class="active"'; ?>>
								<span><?= lang('file') ?></span>
							</a>
						</li>
						<?php
						$query = $this->db->query("select * from bip_user where id='" . $this->session->userdata("user_id") . "' and difficulty_id in (55,56) ");
						if ($query->num_rows == 0) {
						?>
						<li id="faq">
							<a href="<?php echo site_url("faq"); ?>" title="<?= lang('faq') ?>" <?php if ($this->uri->segment(1) == "faq")
								echo 'class="active"'; ?>>
								<span><?= lang('faq') ?></span>
							</a>
						</li>
						<?php } ?>
						<?php
						if ($user_type == 'Psychologist') {
						?>
						<li id="user">
							<a href="<?php echo site_url("user") ?>" alt="<?= lang('settings') ?>" title="<?= lang('settings') ?>" <?php if ($this->uri->segment(1) == "user")
								echo 'class="active"'; ?>>
								<span> <?= lang('settings') ?></span>
							</a>
						</li>
						<?php
						}
						?>
						<?php
						if ($user_type=='Psychologist') {
							$appShown = '';
						} else {
							$appShown = ($this->session->userdata('app_activated')=='no') ? 'style="display:none"': '';
						}
						?>
						<?php if ($language_code==1): ?>
						<li <?=$appShown?> id="minapp" class="stopClick">
							<a href="<?php echo $minappChangedLink ?>" alt="Inställningar" title="minapp" <?php if ($this->uri->segment(1) == "minapp")
								echo 'class="active"'; ?>>
								<span <?php if (!$this->session->userdata("bip_logged_in"))
								echo "style=display:none;" ?> id="polling_data" class="numbers">
								<?php echo $this->session->userdata('total_app_message_temp'); ?>
								</span>
								<span> <?= lang('my_app') ?></span>
							</a>
						</li>
						<?php endif ?>
					</ul>
				</div>
				<div class="clear"></div>
				<?php
				if ($user_type == 'Psychologist'){
					if ((($this->uri->segment(1) != "stage" || $this->uri->segment(2) == 'personal' || $this->uri->segment(2)=='user') && $this->uri->segment(1) != "login" && $this->uri->segment(1) != "minapp" && $this->uri->segment(1) != "faq" && $this->uri->segment(1) != "user") && ($this->uri->segment(1))){
						$this->load->view('stage/user_bar');
					}else{
						if ($this->uri->segment(1)=='minapp' && ($this->uri->segment(2)=='view' || $this->uri->segment(2)=='activityReport')) {
							$muser = $this->minapp_model->getUserByUsername($this->uri->segment(3));
                            $muser['first_name'] = $this->encryption->decrypt($muser['first_name']);
                            $muser['last_name'] = $this->encryption->decrypt($muser['last_name']);
							echo '<div class="min-bar"><span class="fulltext">Vald användare <strong>'.ucfirst($muser['first_name']).' '.$muser['last_name'].'</strong>, om du vill byta användare,  <a href="' . site_url("stage") . '">gå till Översikten </a></span></div>';
						}
					}
				}
				$html = '';
				?>
				<script type="text/javascript">
					$(document).ready(function() {
						$('.cross').click(function() {
				if (confirm($jsLang['u_have_not_finish']+" <?php echo $this->session->userdata('sess_stage_title'); ?>, "+$jsLang['are_u_sure_quit']+"\n"+$jsLang['information_no_save'])) {
				$.ajax({
				url: $sitePath + "/stage/close_stage",
				type: "post",
				success: function(response) {
				$("#del").hide();
				}
				});
				}
				});
				});
				</script>
