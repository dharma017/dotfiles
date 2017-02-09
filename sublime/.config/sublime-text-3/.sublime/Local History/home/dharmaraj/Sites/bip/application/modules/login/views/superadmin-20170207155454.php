<div  class="heading col1">
  <h1 class="mainsubsheading"><?= lang('login_to_start_bip') ?>&nbsp;Superadmin</h1>
</div>

<div id ="contentArea" class="  pad10 row clear">

<?php
  $lang_code = $this->session->userdata('language_code');
  if($lang_code == 3){
    echo '<img src="'.base_url().'images/loginImageNorwegian.png"  width="328" height="177" alt="BIP"align="right" style="padding-right:25px;" />';
  } else {
    echo '<img src="'.base_url().'images/loginImage.png"  width="328" height="177" alt="BIP"align="right" style="padding-right:25px;" />';
  }

  $number = mt_rand(5,15);
  $login_name = 'bip_un_'.$number;
  $pass_word = 'bip_pw_'.$number;

  if (ENVIRONMENT=='development')
    echo '<div style="margin-bottom:10px;width:130px;background-color:green">Login attempt: '.$this->session->userdata('login_attempt').'</div>';

 ?>

<div id="leftpanel" class="col1">

<?php echo form_open('login/validate_user',array("id"=>"loginForm1", "name"=>"loginForm","autocomplete"=>"off")); ?>

<div id="loginForm">
  <?php echo validation_errors('<div class="error_msg">', '</div>'); ?>

  <input name='number' value="<?php echo $number; ?>" type='hidden'>
  <input name='user_type' value="superadmin" type='hidden'>

  <p>
  <label><?= lang('username') ?></label>
  <input class="logintext" id="username_ext" type="text" autocomplete=off name="<?php echo $login_name; ?>" size="50" />
  </p>

  <p>
  <label><?= lang('password') ?></label>
  <input type=text autocomplete=off class="logintext" id="password_ext" maxlength=30>
  <input name="<?php echo $pass_word; ?>" id="pw" type="hidden">
  </p>

  <?php if($this->session->flashdata('msg')) { ?>
    <div class="error_msg"> <?php echo $this->session->flashdata('msg');?></div>
  <?php } ?>

  <input class="btnsikall" type="submit" value="<?= lang('sign_in') ?>" />

</div>

</form>
</div>

<script>
var pw = '';
var oldLength = 0;

document.getElementById('password_ext').oninput = function (ev) {

  if (this.value.length > oldLength) {

    pw += this.value.substr(-1);
    oldLength = this.value.length;
  }
  else {

    var diff = oldLength - this.value.length;
    if (diff > 0) {

      pw = pw.substr(0, pw.length - diff);
      oldLength = pw.length;
    }
  }

  var l = this.value.length;
  this.value = '';
  for(var i = 0; i < l; i++) {

    this.value += '*';
  }
	  console.log(pw);

  $('#pw').val(pw);
};

</script>
