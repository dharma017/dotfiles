<style type="text/css">
.translategrid textarea{
    width:259px;
    height:50px;
    padding:2px 3px;
}
</style>
<div id="box" class="box box-100">
    <!-- box full-width -->
    <div class="boxin">
        <div class="header">
            <?php
            if ($usertype != 'Psychologist') {
                echo '<h3>Translation</h3>';
            }
            ?>

        </div>
        <?php
        // echo $paging;
        if ($totalRows > 0): ?>
            <div id="box1-tabular" class="content clear">
                <?php echo form_open(site_url('/translation/admin/save_language_file'),array('autocomplete'=>"off"));?>
                <?php if($this->session->flashdata('file_error')){ ?>
                    <div class="error">
                        <?php echo $this->session->flashdata('file_error');?>
                    </div>
                <?php }elseif($this->session->flashdata('msg_success')){ ?>
                    <div class="msg">
                        <?php echo $this->session->flashdata('msg_success');?>
                    </div>
                <?php } ?>
                <input type="hidden" name="filename" value="<?php echo $file;?>" />
                <input type="hidden" name="language" value="<?php echo $language;?>" />
                <table cellpadding="0" cellspacing="0" border="0"  class="grid translategrid">
                        <thead>
                            <tr>
                                <th style="width:5px;"><?php echo $this->lang->line("sn"); ?></th>
                                <!-- <th style="width:10px;">Keys</th> -->
                                <th style="width:60px;">Swedish</th>
                                <th style="widht:60px">English</th>
                                <th style="widht:60px">Norwegian</th>
                                <th style="widht:5px">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($keys)&&!empty($keys)): ?>
                            <?php foreach ($keys as $i => $key): ?>
                            <tr>
                                <td style="cursor:default !important;"><?=$i+1?></td>
                                <!-- <td style="cursor:default !important;"> <?php echo $key;?> </td> -->
                                <td><textarea name="<?php echo $key;?>" cols="60" rows="3"><?php echo (is_array($lang) && array_key_exists($key,$lang)) ? htmlspecialchars(stripslashes($lang[$key])):'';?></textarea></td>
                                <td><textarea class="pattern" name="pattern_<?php echo $key;?>" cols="60" rows="3"><?php echo (isset($pattern) && array_key_exists($key,$pattern)) ? htmlspecialchars(stripslashes($pattern[$key])):'';?></textarea></td>
                                 <td><textarea class="pattern_ng" name="pattern_ng_<?php echo $key;?>" cols="60" rows="3"><?php echo (isset($pattern_ng) && array_key_exists($key,$pattern_ng)) ? htmlspecialchars(stripslashes($pattern_ng[$key])):'';?></textarea></td>
                                <td><a href="javascript:;" onClick="$(this).closest('form').submit(); return false;" title="<?=$this->lang->line("save")?>"><img src="<?=base_url()?>images/admin_icons/save.png" alt="<?=$this->lang->line("save")?>"></a></td>
                            </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                        </tbody>
                </table>
                <input type="hidden" id="offset" name="offset" value="<?=$offset+$limit?>">
                <input type="hidden" name="change" class="buttonlng" value="Update Changes"/>
                </form>
            </div>
        <?php endif ?>
    </div>
</div>
