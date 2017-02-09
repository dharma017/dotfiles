<?php
    $sender_detail->first_name = $this->encryption->decrypt($sender_detail->first_name);
    $sender_detail->last_name = $this->encryption->decrypt($sender_detail->last_name);

    $receiver_detail->first_name = $this->encryption->decrypt($receiver_detail->first_name);
    $receiver_detail->last_name = $this->encryption->decrypt($receiver_detail->last_name);

    $rows->msg_subject = $this->encryption->decrypt($rows->msg_subject);
    $rows->message = $this->encryption->decrypt($rows->message);
?>
<div  class="heading col1">
    <?php $result = $this->stage_model->getPageContent(2); ?>
    <h1 class="mainsubsheading"><?php echo $result->page_title ? $result->page_title : lang('messages'); ?></h1>
</div>
<div class="col3 ">
    <a href="<?php echo site_url("messages/getEmailTransactions"); ?>" target="_blank"><img src="<?php echo base_url() ?>images/print_32.png" alt="<?= lang('print_all_messages') ?>"  title="<?= lang('print_all_messages') ?>"></a>
</div>
<div id ="contentArea" class="pad10 row clear inboxTemplate margin_top">
    <div class="usercontent">
        <p>
            <?php echo nl2br(stripslashes(html_entity_decode($result->content))); ?>
        </p>
    </div>
    <?php $this->load->view("messages/messages_menu"); ?>
    <div>
        <?php
        $sent_on = format_date($rows->sent_on);
        
       if ($rows->sms_notify>0 && $rows->email_notify == 0) {
                    $message_title_type = 'SMS';
                }
                elseif ($rows->sms_notify == 0 && $rows->email_notify>0) {
                    $message_title_type = 'Email';
                }elseif ($rows->sms_notify>0 && $rows->email_notify>0){
                    $message_title_type = 'SMS/Email';
                }
                else{
                    $message_title_type = 'Ordinary';
                }
                $usertype = getUserType();
                //echo $usertype;
                if($usertype == 'Psychologist'){
                if($message_title_type == 'Ordinary'){
                $reciver_status = $rows->status_receiver;
                if ($rows->sender_id != $this->session->userdata("user_id")){
                $status = '';
                }
                elseif ($reciver_status != 0) {
                $status = lang('seen').'&nbsp;den&nbsp;' .format_date($rows->read_on);
                }
                else{
                $status = lang('unseen');
                }
            }
                else{
                    $status= '-';
                }
            }
        echo '
        <div class="curvesection clear">
                <div class="curveTop"></div>
                <div class="curveMiddle">
                        <div class="innersections">
                                <div class="messageHeader">'.lang('reply_sender').': ' . $sender_detail->first_name . " " . $sender_detail->last_name . '<br>
                                    '.lang('title_reciever').': ' . $receiver_detail->first_name . " " . $receiver_detail->last_name . '<br>
                                    '.lang('reply_subject').': ' . $rows->msg_subject . '<br>
                                    '.lang('date').': ' . $sent_on . '<br>';
                        if($status != ''){
                        echo lang('msg_status'). ': ' .$status; 
                        }
                    echo '</div>
                                <div class="clear" style="margin-bottom:8px;"></div>
                                <hr />
                                <div class="mailspacing">';
                        $message = html_entity_decode($rows->message, ENT_NOQUOTES, 'UTF-8');
                        $message = $message;
                        echo str_replace("�", "", $message);
                        echo '<br/><br/>
                                            </div><a class="btnsikall col1 marginbtnsr"  href="' . site_url("messages/replyMessage/" . $encryptId) . '" ">'.lang('btn_reply').'</a>
                            </div>
                    </div>
                    <div class="curveBottom ">
                    </div>
            </div>';
            ?>
        </div>