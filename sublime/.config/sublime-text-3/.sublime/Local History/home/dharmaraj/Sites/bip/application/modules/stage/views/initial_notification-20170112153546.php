<?php
$this->session->set_userdata(array('popup_once' => 1));
$total_message_temp = $this->session->flashdata('total_message_temp');
$totalNewSheet = $this->session->flashdata('totalNewSheet');
$total_app_message_temp = $this->session->userdata('total_app_message_temp');
if ($total_message_temp || $totalNewSheet || $total_app_message_temp) :
    ?>
    <style>
        #fancybox-wrap #fancybox-outer{
            background: none !important;
            -webkit-border-radius: 12px;
            border-radius: 12px;
            -moz-border-radius: 12px;
            -moz-box-shadow: 0 0 5px 5px #666;
            -webkit-box-shadow: 0 0 5px 5px#666;
            box-shadow: 0 0 5px 5px #666;
        }
        #fancybox-wrap #fancybox-outer  .fancybox-bg{background:none!important}
    </style>
    <script type='application/javascript'>
        $(document).ready(function() {
            $('#initial_message').fancybox({
                'autoScale': true,
                'transitionIn': 'elastic',
                'transitionOut': 'elastic',
                'speedIn': 500,
                'speedOut': 300,
                'autoDimensions': true,
                'padding'       : 0,
                'margin'        : 0,
                'centerOnScroll': true
            });
            $('#initial_message').click();
        });
    </script>
    <a id="initial_message" href="#pop-up-skiss"></a>
    <div style="display:none">
            <div id="pop-up-skiss">
                <ul class="skiss-list">
                    <?php if ($total_message_temp) : ?>
                    <li>
                        <span class="icon-bx"><img src="<?php echo base_url(); ?>images/mail-icon.png" /></span>
                        <span><?php
                            if($total_message_temp == 1 )
                                echo ($this->session->userdata('logintype')!='admin') ? stripslashes(lang('an_unread_msg_therapist')): stripslashes(lang('a_new_msg_bip'));
                            else
                                echo ($this->session->userdata('logintype')!='admin') ? stripslashes(lang('more_unread_msg_therapist')): stripslashes(lang('more_unread_msg_bip'));
                        ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($totalNewSheet) : ?>
                        <?php if ($total_message_temp): ?>
                            <li class="separator"></li>
                        <?php endif ?>
                    <li>
                        <span class="icon-bx"><img src="<?php echo base_url(); ?>images/note-icon.png" /></span>
                        <span><?php
                            if($totalNewSheet == 1 )
                                echo stripslashes(lang('an_unread_comment_worksheet'));
                            else
                                echo stripslashes(lang('more_unread_comment_worksheet'));
                        ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($total_app_message_temp) : ?>
                        <?php if ($total_message_temp || $totalNewSheet): ?>
                                <li class="separator"></li>
                        <?php endif ?>
                    <li style="height:89px;">
                        <span class="icon-bx"><img src="<?php echo base_url(); ?>images/forapp.png" /></span>
                        <span><?php
                            if($total_app_message_temp == 1 )
                                echo stripslashes(lang('an_unread_comment_myapp'));
                            else
                                echo stripslashes(lang('more_unread_comment_myapp'));
                        ?></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
    </div>
<?php endif; ?>
