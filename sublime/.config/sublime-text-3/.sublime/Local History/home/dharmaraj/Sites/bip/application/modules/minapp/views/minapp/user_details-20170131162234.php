<div id="main_sent_message">
    <div  class="heading col1">
        <h1 class="mainsubsheading">
	        <?php if ($uertype=='Psychologist'): ?>
	        	<?=$user['first_name'].' '.$user['last_name']?>
	        <?php else: ?>
	        	<?=lang('txt_manage_app')?>
	        <?php endif ?>
        </h1>
    </div>
<?php $this->load->view('minapp/minapp/training_ajax',$data); ?>
