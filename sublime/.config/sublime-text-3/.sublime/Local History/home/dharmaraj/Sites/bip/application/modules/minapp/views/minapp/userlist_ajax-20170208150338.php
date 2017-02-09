<div id ="contentArea" class="pad10 row clear">      
    <?php if (!empty($app_users)) { ?>
        <table class="gridtable" cellspacing="0" cellpadding="0" border="0" width="782px">
            <thead>
                <tr>
                    <th width="2%"><?=lang('txt_sn')?></th>       
                    <th width="2%">&nbsp;</th>      
                    <th width="30%"><?=lang('txt_user')?></th>
                    <th width="20%"><?=lang('txt_treatment')?></th>
                    <th width="15%"><?=lang('txt_category')?></th>
                    <th width="15%"><?=lang('txt_started_app')?></th>
                    <th width="15%"><?=lang('txt_total_trainings')?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($app_users as $ak => $appuser):
                $countNewComment = $this->minapp_model->countNewCommentForUser($appuser->id);
                if (isset($countNewComment) && $countNewComment > 0) :
                    $comment_status = '<img style="height: 20px;margin-top:3px;" src="' . base_url() . 'assets/public/css/images/counter_red.png"> ';                
                else:
                    $comment_status = '&nbsp;';
                endif;
             ?>
                <tr onclick="view('<?=$appuser->username?>')">
                    <td style="text-align:center"><?=$ak+1?></td>
                    <td><?=$comment_status?></td>
                    <td><?=$appuser->first_name.' '.$appuser->last_name?></td>
                    <td><?=$appuser->difficulty?></td>
                    <td><?=$appuser->problem?></td>
                    <td><?=$appuser->created_at?></td>
                    <td><?=$appuser->total?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php
    } else {
        echo lang('no_user_activity');
    }
    ?>
</div>