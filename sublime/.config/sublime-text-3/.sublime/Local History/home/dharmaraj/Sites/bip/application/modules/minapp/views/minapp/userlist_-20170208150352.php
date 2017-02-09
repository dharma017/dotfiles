<?php
$userId = $this->session->userdata("p_id");
if(!$userId)
{
    echo '<div class="clear"></div> Du har inte valt någon användare! ';
}

$usertype = getUserType();
$difficulties=$this->setting_model->getAllDifficultyByLang();
?>
<script type="text/javascript" src="<?=base_url()?>assets/public/js/jQuery.download.js"></script>
<div id="main_sent_message">
    <div  class="heading col1">
        <h1 class="mainsubsheading"><?=lang('txt_user_settings')?></h1>
    </div>
    <div id="contentBtn">
        <div>
            <select name="difficulty" id="selTreatment" onchange="filterUserByType(this.value,'treatment')">
                <option value="0"><?=lang('sel_choose_treat')?></option>
                <?php foreach ($difficulties as $dk => $difficulty):
                 ?>
                    <option value="<?=$difficulty->id?>"><?=$difficulty->difficulty?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <select name="selProblem" id="selProblem"  onchange="filterUserByType(this.value,'problem')">
                <option value="0"><?=lang('sel_choose_cat')?></option>
            </select>
        </div>
        <!-- <div style="margin-top:7px;">
            <a class="btnMidall col1 marginbtnsr dwnldxls" href="javascript:void(0);" onclick="generatexls();return false;"><?=lang('txt_dwnld_excel_stats')?></a>
            <a href="javascript:void(0);" onclick="generatexls();return false;"><span style="color: #0579bc;">Flervalssvar</span> <img style="vertical-align: text-bottom;" src="<?= base_url().'images/admin_icons/icon_xls.png' ?>"></a>
             <span id="preLoader" style="display:none;"><img src="<?php echo base_url ();?>images/loading.gif" id="loading"/></span>
        </div> -->
    </div>
<?php $this->load->view('minapp/minapp/userlist_ajax',$data); ?>
