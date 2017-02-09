<div id="popup_name<?php echo $rows->id; ?>" class="popup_block">
                                <div class="bodypopup" id="bodypopup1">
                                        <h2 class="iframes">User Details</h2><!--<input type="button" value="Print Div" onclick="PrintElem('#bodypopup1')" />-->

                                    <div class="menuAdd">
                                        <ul class="adm-form">
                                            <li><label class="labeltmplt3"><strong> Username</strong></label>:<?php echo $rows->username; ?></li>

                                           <!--  <li><label class="labeltmplt3"><strong>Password</strong></label>:<?php echo $strpw; ?></li> -->
                                            <li><label class="labeltmplt3"><strong>Full Name</strong></label>:<?php echo $rows->first_name . '&nbsp;' . $rows->last_name; ?></li>
                                            <li><label class="labeltmplt3"><strong> Email</strong></label>:<?php echo $rows->email; ?></li>
                                            <li><label class="labeltmplt3"><strong>Total No. Logins</strong></label>:<?php echo $rows->no_of_login; ?></li>
                                            <li><label class="labeltmplt3"><strong>No. of days from first to last login</strong></label>:<?php echo $rows->days; ?></li>
                                            <?php
                                            $hours = floor($rows->total_time_in_system / 3600);
                                            $minutes = floor($rows->total_time_in_system % 3600 / 60);
                                            ?>
                                            <li> <label class="labeltmplt3"><strong>Total time in the system</strong></label>:&nbsp;<?php echo secondsToHMS($rows->total_time_in_system, true); ?> [ HH:MM:SS ]</li>
                                            <?php
                                            if ($rows->user_role != 2) {
                                                ?>
                                                <li> <label class="labeltmplt3"><strong>Completed Stage</strong></label>:&nbsp;<?php echo $total_stage_comp; ?></li>
                                            <?php } ?>
                                            <li><label class="labeltmplt3"><strong> Active From</strong></label>:<?php echo $rows->active_from; ?></li>
                                            <li><label class="labeltmplt3"><strong> Active To</strong></label>:<?php echo $rows->active_to; ?></li>
                                            <li> <label class="labeltmplt3"><strong>Status</strong></label>:<?php echo ($rows->status == 1) ? 'Active' : 'InActive'; ?></li>
                                            <?php $remainingactiveday = ($rows->active_remaining_day >= 0) ? $rows->active_remaining_day : 'You have no Active days.' ?>
                                            <li><label class="labeltmplt3"><strong> Remaining Active days</strong></label>:<?php echo $remainingactiveday; ?>
                                            <li><label class="labeltmplt3"><strong>Last Login</strong></label>:<?php
                                    if ($rows->last_login)
                                        echo date("Y-m-j", strtotime($rows->last_login)); else
                                        echo 'Not Available';
                                            ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
