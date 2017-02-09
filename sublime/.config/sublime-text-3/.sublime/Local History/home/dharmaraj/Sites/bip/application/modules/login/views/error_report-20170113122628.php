<style type="text/css">
.error-container {
	margin: 10px;
}
.error-container h4 {
	background-color: transparent;
	font-size: 24px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}
.error-container p {
	font-size: 24px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}
.success_msg {
	text-align: left;
	color: green;
	font-size: 14px;
	font-weight: bold;
	margin-left: 20px;
}
.validation_msg {
	text-align: left;
	color: red;
	font-size: 14px;
	font-weight: bold;
	margin-left: 20px;
}
</style>

<div class="error-container">

<div id="box" class="box box-100">
	  <div class="boxin">
	    <div class="content clear">
        	<h4> <?php echo lang('user_error_report'); ?> </h4>

            <?php
            if ($this->session->flashdata('msg')){
             echo "<div class='success_msg'>";
             echo $this->session->flashdata('msg');
             echo "</div>";
             }
            ?>
			<?php echo validation_errors('<div class="validation_msg">','</div>'); ?>
			<form method="post" name="report_error_msg" id="report_error_msg" action="<?php echo site_url('login/notify_all_errors'); ?>">
	            <div id="message_compose" class="fleft">

	                    <div class="input-group fleft">
	                        <label class="label fleft">
	                            <?= lang('user_error_report_subject') ?>:
	                        </label>

	                        <input type="text" name="subject" id="subject" required class="inputs" value="<?php echo set_value('subject'); ?>" />
	                    </div>
	                    <div class="input-group fleft">
	                     <label class="label fleft"><?= lang('user_error_report_message') ?>:</label>
	                        <textarea cols="70" rows="8" name="message" id="message" class="textbox"><?php echo set_value('message'); ?></textarea>

	                    </div>

	                    <div class="input-group fleft">
	                        <label class="label fleft"><?= lang('user_error_report_attachement') ?>: <?php echo 'log-'.date('Y-m-d'); ?></label>
	                    </div>

	                    	<input style="margin-bottom:20px;" type="submit" name="submit"  class="button" value="<?= lang('send'); ?>" />

		            </div>
		    </form>

		  </div>
		</div>
	</div>
</div>


