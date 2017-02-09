<?php $loginReport = $this->worksheet_model->getLoginReport(); ?>
<div id="box1-tabular">
    <?php if ($loginReport) { ?>
       <table class="gridtable" cellspacing="0" cellpadding="0" border="0" width="782px">
            <thead>
                <tr class="chnge">
                    <th width="30px"><?= lang('txt_sn') ?></th>
                    <th width="20px">&nbsp;</th>
                    <th width="250px">Option</th>
                    <th width="125px" ><?= lang('created_on') ?></th>
                    <th width="125px"><?= lang('last_saved') ?></th>
                    <th width="15px">&nbsp;</th>
                    <?php if ($this->session->userdata('logintype')=='admin'): ?>
                        <th width="60px"><?= lang('mark_as_unread') ?></th>
                    <?php endif ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                $offset = 0;
                $html = "";
                foreach ($loginReport as $rows) {
                    $last_updated = $rows->last_updated;

                    $this->db->freeDBResource();
				    				$total_comments_results = $this->worksheet_model->getAllCommentByWorksheetId($rows->id);
				    				$total_comments_count = count($total_comments_results);

				    				$this->db->freeDBResource();
				    				$total_unread_comments = $this->worksheet_model->getAllCommentUnreadByWorksheetId($rows->id);
				    				$total_new_comment = count($total_unread_comments);
                    //echo "</prev>";print_r($total_unread_comments);

                    if (!$last_updated || $last_updated == "0000-00-00")
                        $last_updated = ' '; //Ej uppdaterad
                    else
                        $last_updated = format_date($last_updated);
                    if (($rows->status == "1" && $rows->user_id != $this->session->userdata("user_id")))
                        $new = ' new ';
                    else
                        $new = '';
                    if ($total_new_comment > 0) :
                        $comment_status = '<img src="'.base_url().'assets/public/css/images/green_dot.png"> ';
                    elseif ($total_comments_count) :
                        $comment_status ='<img src="'.base_url().'assets/public/css/images/grey_dot.png"> ';
                    else:
                        $comment_status = '&nbsp;';
                    endif;
                    $haveAnswer = true;
                    $this->db->freeDBResource();
                    $parseForDelete = $this->worksheet_model->getFormDataById($rows->id);
                    $this->db->freeDBResource();
                    $decodedMessage = json_decode($parseForDelete->message);
                    if (count($decodedMessage) > 0) {
                        foreach ($decodedMessage as $key => $formMessage) {
                            $formMessage = trim($formMessage);
                            if ($key == 'ladder')
                                continue;
                            if (!empty($formMessage)) {
                                $haveAnswer = false;
                                break;
                            }
                        }
                    }
                    $updater = $rows->updater;
                    if ($updater)
                        $postedDate = $postedDate . ' ' . $updater;
                    if ($count == 1)
                        $first_tr_class = "firstRowGridTable";
                    else
                        $first_tr_class = "";
                    $offset++;
                    $login_message = json_decode($rows->message,true);
                    if($login_message['option'] == 0){
                        $msg="I did not get the SMS-code";
                    }else if($login_message['option'] == 1)
                    {
                        $msg="Got the SMS-code, but it did not work";
                    }else{
                        $msg="Other";
                    }
                    $html.='<tr class="login_tr" id="sheet_chkbx_'.$rows->id.'">';
                    $html.='<td  class="' . $first_tr_class . $new . '" onclick="openWS(\'' . site_url('worksheet/viewLogFormData/' . $rows->id.'/report') . '\')">' . $offset . '</td>';
                    $html.='<td  class="' . $first_tr_class . $new . '" onclick="openWS(\'' . site_url('worksheet/viewFormData/' . $rows->id.'/report') . '\')">'.$comment_status.' </td>';
                     $html.='<td class="' . $first_tr_class . $new . '" onclick="openWS(\'' . site_url('worksheet/viewFormData/' . $rows->id.'/report') . '\')">'. $msg. '</td>';
                    $html.='<td class="' . $first_tr_class . $new . '" onclick="openWS(\'' . site_url('worksheet/viewFormData/' . $rows->id.'/report') . '\')">' . format_date($rows->send_date) . '</td>';
                    $html.='<td class="' . $first_tr_class . $new . '" onclick="openWS(\'' . site_url('worksheet/viewFormData/' . $rows->id.'/report') . '\')">' . $last_updated . '</td>';
                    $html.='<td align="center" class="' . $first_tr_class . $new . '">';
                    if ($haveAnswer && (getUserType() == 'Psychologist')) {
                        $html.='<a href="' . site_url('worksheet/removewFormData/' . $rows->id) . '" onclick="return confirm(\'Are you sure you want to delete?\')"><img src="' . base_url() . 'images/admin_icons/delete.png" alt="' . $this->lang->line("delete") . '"></a>';
                    }
                    $html.='</td>';
                    if ($this->session->userdata('logintype')=='admin'){
                        $state= ($rows->status==1) ? 'checked="checked"': '';
                        $html.='<td align="center" class="' . $first_tr_class . $new . '"> <input style="width:16px;height:16px;" type="checkbox" name="chkbx_'.$rows->id.'" class="onoffswitch-checkbox" id="chkbx_'.$rows->id.'" '.$state.' /> </td>';
                    }
                    $html.='</tr>';
                }
                $rows = "";
                echo $html;
                ?>
            </tbody>
        </table>
        <?php
        /*if (isset($paging) && trim($paging) != '') {
            echo $paging;
            echo '</div>';
            echo '</div>';
        }*/
        ?>
        <?php
    } else {
        echo lang('see_other_parts_ans');
    }
    ?>
</div>

