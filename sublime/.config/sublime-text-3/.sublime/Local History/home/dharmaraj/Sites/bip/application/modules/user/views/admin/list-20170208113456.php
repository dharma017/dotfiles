<script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<style type="text/css">
    label.labeltmplt3{ width:180px!important;}
    #fade { /*--Transparent background layer--*/
        display: none; /*--hidden by default--*/
        background: #000;
        position: fixed; left: 0; top: 0;
        width: 100%; height: 100%;
        opacity: .80;
        z-index: 9999;
    }
    .popup_block{
        display: none; /*--hidden by default--*/
        background: #fff;
        padding: 3px;
        border: 10px solid #fff;
        float: left;
        font-size: 1.2em;
        position: fixed;
        top: 50%; left: 50%;
        z-index: 99999;
        /*--CSS3 Box Shadows--*/

        box-shadow: 0px 0px 20px #000;
        /*--CSS3 Rounded Corners--*/

        border-radius: 10px;
    }
    img.btn_close {
        float: right;
        margin: -28px -23px 0 0;
    }
    /*--Making IE6 Understand Fixed Positioning--*/
    *html #fade {
        position: absolute;
    }
    *html .popup_block {
        position: absolute;
    }
    .bodypopup{
        background: url("../../images/bg.gif") repeat-x scroll 0 0 transparent;
        font: 12px/18px "Trebuchet MS",Arial,Helvetica,sans-serif;
        text-transform: none;

    }
    .inctiveuserfont{ color:#993333}

    .dataTables_length,.dataTables_info,.dataTables_paginate a{ display: none; }
    .dataTables_paginate span a{ display: block; float: left; align-self: center }
    .dataTables_filter,.dataTables_filter label{ display: none; }

</style>
<?php

$orderBy = $this->input->post('orderBy');
if (!$orderBy)
    $orderBy = "id asc";

$name_array = explode(' ', $orderBy);
/*
echo '<pre>';
print_r($name_array);
echo '</pre>';
*/
if (!$name_array[1]){
    if ($this->session->userdata('AlterBy') && $this->session->userdata('AlterBy') == 'desc'):
        $AlterBy = 'desc';
    else:
        $AlterBy = 'asc';
    endif;
    //$orderBy .= $AlterBy;
}
else
{
	$AlterBy  = $name_array[1];

	if(trim($AlterBy) != "asc" && trim($AlterBy) != "desc")
		$AlterBy = "asc";
	$orderBy = $name_array[0];
}

$this->session->set_userdata(array('AlterBy' => $AlterBy));

$this->session->set_userdata(array('userOrderBy' => $orderBy));

if($search_txt)
    $this->session->set_userdata(array('userSearch' => $search_txt));


$usertype = $this->session->userdata('logintype');
$psychologistid =  '';
$permission = $this->setting_model->getPermissionOfPsy();
$permission = json_decode($permission,true);
//print_r($permission);die();
if($usertype == "admin") //&& !empty($permission)) || $usertype=='Psychologist')
{

	if(!empty($permission) && $permission->psycho_manage != 1)
		$psychologistid = $this->session->userdata('user_id');
}
else if($usertype == "Psychologist")
{
	$psychologistid = $this->session->userdata('user_id');
}


$offset = (isset($_POST['offset'])) ? $_POST['offset'] : 0;
$this->session->set_userdata(array('offset' => $offset));
//$orderBy = (isset($_POST['orderBy'])) ? $_POST['orderBy'] : 'first_name';
//$search_txt =htmlspecialchars($_REQUEST['search_txt']);
// patch by sujeet
//$name_array = explode(' ', $orderBy);


$orderAlter = ($AlterBy == 'asc') ? 'desc' : 'asc';

$psychologist_id = (!empty($psychologist_id)) ? $psychologist_id: '';

$datalimit = 100;
// $result = $this->user_model->getAllUser222($offset, $datalimit, $orderBy);
//$result=$this->user_model->getAllUserOfPsy($offset, $datalimit, $orderBy);
// echo "<pre>";print_r($result);exit;

$allUser = $result[0];
$totalRows = $result[1];
$jsfn = array('listUser', '"' . $orderBy . '"');
//$paging = $this->paging_model->ajaxPaging($totalRows, $datalimit, $jsfn, $offset);

// to show ordering icon at the header of table.
$desc_icon = '<img src="'.base_url().'images/admin_icons/downarrow.png">';
$asc_icon = '<img src="'.base_url().'images/admin_icons/uparrow.png">';

if ($orderBy == "first_name")    $first_name_order_by = trim($AlterBy)=="desc"?$desc_icon:$asc_icon;
if ($orderBy == "email")    $email_order_by = trim($AlterBy)=="desc"?$desc_icon:$asc_icon;
if ($orderBy == "difficulty")    $difficulty_order_by = trim($AlterBy)=="desc"?$desc_icon:$asc_icon;
if ($orderBy == "group_name")    $group_name_order_by = trim($AlterBy)=="desc"?$desc_icon:$asc_icon;
if ($orderBy == "psychologist")    $psychologist_order_by = trim($AlterBy)=="desc"?$desc_icon:$asc_icon;
if ($orderBy == "join_date")     $join_date_order_by = trim($AlterBy)=="desc"?$desc_icon:$asc_icon;


    ?>
<?php
$user_role_type = $this->session->userdata('user_role_type');
$superadmin = ($usertype == "admin" && $user_role_type== "superadmin");
$difficulty = $this->user_model->getAllDifficultyByLang();
$this->db->freeDBResource();
$sessDifficulty = $this->session->userdata('difficulty');

$group = $this->user_model->getAllGroupByLang();
$this->db->freeDBResource();
$sessGroup = $this->session->userdata('group');

//$Psychology = $this->user_model->getAllpsychologyByLang();
$get_sorted_psychologist_list = $this->user_model->get_sorted_psychologist_list();
$this->db->freeDBResource();
$sessPsychology = $this->session->userdata('Psychology');

$user_role = $this->session->userdata('user_role');

if ($user_role==2) {
	$sessPsychology = $this->session->userdata('Psychology');
$selectListDiff = $this->user_model->getUserSelectListDiff();
$difficultyIDArr = $selectListDiff->difficulty_ids;
$selectListGrp = $this->user_model->getSelectListGrp();
$groupIDArr = $selectListGrp->group_ids;
$selectListPsy = $this->user_model->getUserSelectListPsy();
$psychologistIDArr = $selectListPsy->psychologist_ids;
}

?>
<div id="box" class="box box-100">
    <!-- box full-width -->
    <div class="boxin">
        <div class="header">
            <?php
            if ($usertype != 'Psychologist') {
                echo '<h3>Users</h3>';
            }
            ?>
            <?php
                        foreach ($permission['rights_per_group'] as $key => $grp_data) {
                            if($grp_data['manage_users']== 1 || $grp_data['create_psychologists']== 1 ){
                                $create = true;
                                break;
                            }else{
                                $create = false;
                            }
                        }

                        if ($create || ($usertype == "admin" && $user_role_type == "superadmin")) { ?>
            <a class="savebtns" href="#Add User" style="margin-left:10px; margin-top:0"  onclick="addUserForm();"><?php echo $this->lang->line("add_user"); ?>&nbsp;</a>
            <?php } ?>

            <div id="difficulty" style="float:right;margin: 0 5px 5px 5px ">
                <form id="search_form" class="search_form"  action="#" method="post" >
                    <input type="text" name="search_user" id="search_text" value="<?php echo $this->session->userdata('userSearch'); ?>" >
                    <input type="submit" value="Search" id="btn_search_user" class="btn_search_user"/>
                </form>

            </div>

        </div>

        <?php
        //echo $paging;
        // if($totalRows > 0) {
        ?>


        <div id="box1-tabular" class="content clear">

        <?php if($user_role==3)
                { ?>
        <div class="user_wrapper selectRight">
                    <select id="filter_difficulty" onchange="filterUserByParams('difficulty <?php echo $orderAlter; ?>','')" class="drop">
                     <option value="0" <?php if(!$sessDifficulty) echo 'selected="selected"';?>> Choose Difficulty</option>
                       <?php foreach($difficulty as $data){?>
                        <option value="<?php echo $data->id;?>" <?php if($sessDifficulty==$data->id) echo 'selected="selected"';?>><?php echo $data->difficulty; ?></option>
                        <?php }?>

                       </select>


             <select id="filter_psychologist" class="drop" onchange="filterUserByParams('psychologist <?php echo $orderAlter; ?>','')">
             <option value="0" <?php if(!$sessPsychology) echo 'selected="selected"';?> >Choose Psychologist</option>
               <?php foreach($get_sorted_psychologist_list as $id => $psy_name){
               	?>
                <option value="<?php echo $id;?>" <?php if($sessPsychology==$id) echo 'selected="selected"';?>><?php echo $psy_name; ?></option>
                <?php } ?>

               </select>


             <select id="filter_group" class="drop" onchange="filterUserByParams('first_name <?php echo $orderAlter; ?>','')">
             <option value="0" <?php if(!$sessGroup) echo 'selected="selected"';?>> Choose Group</option>
               <?php foreach($group as $data){?>
                <option value="<?php echo $data->id;?>" <?php if($sessGroup==$data->id) echo 'selected="selected"';?>><?php echo $data->group_name; ?></option>
                <?php }?>

               </select>
           </div>
          <?php } else{ ?>

                <div class="user_wrapper selectRight">
                    <!-- <select id="filter_difficulty" onchange="filterUserByParams('difficulty <?php echo $orderAlter; ?>','')" class="drop">
                     <option value="0" <?php if(!$sessDifficulty) echo 'selected="selected"';?>> Choose Difficulty</option>
                       <?php foreach($difficultyIDArr as $difficultyID){?>
                        <option value="<?php echo $difficultyID;?>" <?php if($sessDifficulty==$difficultyID) echo 'selected="selected"';?>><?php echo $this->user_model->getDifficultyNamebyId($difficultyID); ?></option>
                        <?php }?>

                       </select> -->
                       <select id="filter_difficulty" onchange="filterUserByParams('difficulty <?php echo $orderAlter; ?>','')" class="drop">
                     <option value="0" <?php if(!$sessDifficulty) echo 'selected="selected"';?>> Choose Difficulty</option>
                       <?php foreach($difficulty as $data){?>
                        <option value="<?php echo $data->id;?>" <?php if($sessDifficulty==$data->id) echo 'selected="selected"';?>><?php echo $data->difficulty; ?></option>
                        <?php }?>

                       </select>


             <!-- <select id="filter_psychologist" class="drop" onchange="filterUserByParams('psychologist <?php echo $orderAlter; ?>','')">
             <option value="0" <?php if(!$sessPsychology) echo 'selected="selected"';?> >Choose Psychologist</option>
               <?php foreach($psychologistIDArr as $data){?>
                <option value="<?php echo $data;?>" <?php if($sessPsychology==$data) echo 'selected="selected"';?>><?php echo $this->user_model->getUserFullName($data); ?></option>
                <?php }?>

               </select> -->
               <select id="filter_psychologist" class="drop" onchange="filterUserByParams('psychologist <?php echo $orderAlter; ?>','')">
             <option value="0" <?php if(!$sessPsychology) echo 'selected="selected"';?> >Choose Psychologist</option>
               <?php foreach($get_sorted_psychologist_list as $id => $psy_name){?>
                <option value="<?php echo $id;?>" <?php if($sessPsychology==$id) echo 'selected="selected"';?>><?php echo $psy_name; ?></option>
                <?php }?>

               </select>


             <select id="filter_group" class="drop" onchange="filterUserByParams('first_name <?php echo $orderAlter; ?>','')">
             <option value="0" <?php if(!$sessGroup) echo 'selected="selected"';?>> Choose Group</option>
               <?php foreach($groupIDArr as $data){?>
                <option value="<?php echo $data;?>" <?php if($sessGroup==$data) echo 'selected="selected"';?>><?php echo $this->user_model->getGroupNamebyId($data); ?></option>
                <?php }?>

               </select>
           </div>

                <?php }
                ?>

            <form class="plain" action="#" method="post" enctype="multipart/form-data">
                <?php
                if (isset($allUser)) {
                    $width = ($usertype == 'Psychologist') ? 712 : 960;
                    ?>
                    <table id="myTable" width="<?php echo $width; ?>" cellpadding="0" cellspacing="0" border="0"  class="grid">
                        <thead>
                            <tr>
                                <th width="20px"><?php echo $this->lang->line("sn"); ?></th>
                              <!-- <th width="120px"><a style="cursor:pointer"><?php echo $this->lang->line("username"); ?></a></th>  -->

                                <th width="120px"><a style="cursor:pointer">Full Name <?php echo $first_name_order_by?></a></th>

                                <!-- <th width="150px"><a style="cursor:pointer" onclick="listUser('email <?php echo $orderAlter; ?>','');"><?php echo $this->lang->line("email").' '.$email_order_by; ?></a></th> -->
                                <?php if ($usertype != 'Psychologist') { ?>
                                    <th width="150px"><a  style="cursor:pointer">Diagnosis <?php echo $difficulty_order_by?></a></th>
                                    <th width="120px"><a style="cursor:pointer");" >Group <?php echo $group_name_order_by?></a></th>
                                    <th width="140px"><a  style="cursor:pointer");" >Psychologist <?php echo $psychologist_order_by?></a></th>
                                <?php } ?>
                                   <th width="120px"><a style="cursor:pointer">Date Added<?php echo $join_date_order_by?></a></th>
  <!--   <th width="150px">Date Added</th> -->

                                                                            <!-- <th width="50px"><?php // echo $this->lang->line("published");                   ?></th>-->
                                <th width="120px"><?php echo $this->lang->line("setting_stage"); ?>Setting</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
							$psychologistId = $this->session->userdata('user_id');
                            foreach ($allUser as $rows) {
                                $rows->first_name = $this->encryption->decrypt($rows->first_name);
                                $rows->last_name = $this->encryption->decrypt($rows->last_name);
                                $rows->email = $this->encryption->decrypt($rows->email);
                                $rows->contact_number = $this->encryption->decrypt($rows->contact_number);
                                $rows->psychologist = $this->encryption->decrypt($rows->psychologist);

                                $pdfLink = site_url("user/createPdf/" . $rows->id);
                                if ($rows->status == "1")
                                    $pub_icon = '<img src="' . base_url() . 'images/admin_icons/right.png" alt="' . $this->lang->line("edit") . '">';
                                else
                                    $pub_icon = '<img src="' . base_url() . 'images/admin_icons/wrong.png" alt="' . $this->lang->line("edit") . '">';

                                $offset++;

                                $rowclass = ($rows->active_remaining_day <= '-14') ? 'inctiveuserfont' : '';
                                if($rows->editpermission ==1 || $superadmin)
                                {
                                    $editable = 'onclick="editUser(' . $rows->id . ');"';
                                    $class = "";
                                }
                            else{
                                $editable = '';
                                $class = "nohover";
                            }

                                $html.='<tr class="' . $rowclass . ' '.$class.'">';
                                $html.='<td>' . $offset . '</td>';
                                /* $html.='<td >'.$rows->username.'</a>'; */

                                $html.='<td '.$editable.'>' . $rows->first_name . '&nbsp;' . $rows->last_name . '</a>';
                                if ($rows->difficulty_id > 0)
                                    $total_stage_comp = $this->user_model->getAllStagewithdiff($rows->difficulty_id, $rows->id);

                                //$html.='<td>'.$rows->address.'</td>';
                                /* $html.='<td>'.$rows->difficulty.'&nbsp;</td>'; */
                                $strpw = $rows->password;
                                for ($i = 0; $i < 5; $i++) {
                                    $strpw = base64_decode(strrev($strpw)); //apply base64 first and then reverse the string}
                                }
                                // $html.='<td>' . $rows->email . '</td>';
                                if ($usertype != 'Psychologist') {
                                    $html.='<td '.$editable.'>' . (($rows->user_role!=2) ? $rows->difficulty : "")  . '</td>';
                                    $html.='<td '.$editable.'>' . (($rows->user_role!=2) ? $rows->group_name : "") . '</a>';
                                    $html.='<td '.$editable.'>' . $rows->psychologist . '</td>';
                                }
                                $date = $rows->join_date;
                                $html.='<td>'.date('Y-m-d',strtotime($date)).'</td>';
                                //$html.='<td>'.$rows->email.'</td>';
                                //$html.='<td>'.$strpw.'</td>';
                                //$html.='<td>'.$pub_icon.'</td>';
                                $html.='<td>';
								if($rows->editpermission ==1 || $superadmin || $rows->id == $psychologistid || $rows->psychologist_id == $psychologistid)
                                {
									$html.='<a href="#Edit User" class="linkStyle" onclick="editUser(' . $rows->id . ');" title="Edit User"><img src="' . base_url() . 'images/admin_icons/edit.png" alt="' . $this->lang->line("edit") . '"></a>
										&nbsp;';
								}
								else
								{
									$html .= '<label style="display: inline-block; width: 22px;"></label>';
								}
                                if($rows->editpermission ==1 && $rows->id != $psychologistid || $superadmin){
                                    $html .='<a href="#Delete User" class="linkStyle" onclick="checkdeleteUser(' . $rows->id . ');" title="Delete User"><img src="' . base_url() . 'images/admin_icons/delete.png" alt="' . $this->lang->line("delete") . '"></a>';
                                }
                                else{
                                    $html .= '<label style="display: inline-block; width: 16px;"></label>';
                                }
								$html .= '&nbsp;<a href="#?w=500" rel="popup_name' . $rows->id . '" class="poplight"><img alt="Delete" title="Detail" src="' . base_url() . 'images/admin_icons/detail.png"></a>&nbsp;';

                                /* $html .='<a href="' . $pdfLink . '" class="linkStyle"  target="_blank"><img alt="PDF" title="PDF" src="' . base_url() . 'images/admin_icons/icon_pdf.png"></a>';*/
                                $html.='</td></tr>';
                                ?>
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
                            <?php
                        }
                        echo $html;
                        ?>
                        </tbody>
                    </table>
                </form>
            </div>

            <!-- .content#box-1-holder -->
            <?php
            echo $paging;
                }
                else {
                    echo 'No user found.';
                }
        /*}
        else {
            echo '<h2 align="center" style="margin:10px 0; float:left; width:100%"> No User Available</h2>';
        }*/
        ?>

    </div>
</div>

<script type="text/javascript">
    function checkdeleteUser(userid)
    {
        $path=$sitePath+"/user/admin/checkdeleteUser";
        $.ajax({
            url:$path,
            type:'post',
            data:{'userid':userid},
            async: true,
            success : function(response) {
                if(response == 1){
                    //if(confirm("Sure, do you want to delete this user."))
                    deleteUser(userid);}
                else
                {
                    alert('Psychologist are linked with users So You can not delete.');
                }
            }

        });


    }
    function Exportexcel()
    {
        $path=$sitePath+"/user/admin/FormExportExcel";

        $.ajax({
            url:$path,
            type:'post',
            data:{},
            async: true,
            success : function(response) {
                $('#content').html(response);
            }

        });
    }
</script>
<?php

function secondsToHMS($seconds, $padHours = false) {
    // start with a blank string
    $hms = "";

    // do the hours first: there are 3600 seconds in an hour, so if we divide
    // the total number of seconds by 3600 and throw away the remainder, we're
    // left with the number of hours in those seconds
    $hours = intval(intval($seconds) / 3600);

    // add hours to $hms (with a leading 0 if asked for)
    $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" : $hours . ":";

    // dividing the total seconds by 60 will give us the number of minutes
    // in total, but we're interested in *minutes past the hour* and to get
    // this, we have to divide by 60 again and then use the remainder
    $minutes = intval(($seconds / 60) % 60);

    // add minutes to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":";

    // seconds past the minute are found by dividing the total number of seconds
    // by 60 and using the remainder
    $seconds = intval($seconds % 60);

    // add seconds to $hms (with a leading 0 if needed)
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    // done!
    return $hms;
}
?>

<script type="text/javascript">
    $(document).ready(function() {

        //$('tbody tr:even').addClass('even');
        $(".fancybox").fancybox({
            'width'				: '85%',
            'height'			: '108%',
            'autoScale'			: false,
            'transitionIn'		: 'elastic',
            'transitionOut'		: 'elastic',
            'type'				: 'iframe'
        });

        $(".grid tbody tr").mouseover(
        function(){
            $('.this').css({"background-color":"#ddd","cursor":"pointer"});
        });

        $(".grid tbody tr").mouseout(
        function(){
            $(this).removeAttr("style");
        });

        $('.grid tbody tr:even').addClass('even');
        $('.grid tbody tr:odd').addClass('odd');
        $('.grid tbody tr:last-child').addClass('last');
        $('.grid tbody tr th:first-child, tr td:first-child').addClass('first');
        $('.grid tbody tr th:last-child, tr td:last-child').addClass('last');

        //When you click on a link with class of poplight and the href starts with a #
        $('a.poplight').click(function() {
            var popID = $(this).attr('rel'); //Get Popup Name
            var popURL = $(this).attr('href'); //Get Popup href to define size

            //Pull Query & Variables from href URL
            var query= popURL.split('?');
            var dim= query[1].split('&');
            var popWidth = dim[0].split('=')[1]; //Gets the first query string value
            //Fade in the Popup and add close button
            $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#" class="close"><img src="<?php echo base_url(); ?>images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');

            var popMargTop = ($('#' + popID).height() + 80) / 2;
            var popMargLeft = ($('#' + popID).width() + 80) / 2;

            //Apply Margin to Popup
            $('#' + popID).css({
                'margin-top' : -popMargTop,
                'margin-left' : -popMargLeft
            });

            //Fade in Background
            $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
            $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies

            return false;
        });

        //Close Popups and Fade Layer
        $('a.close, #fade').live('click', function() { //When clicking on the close or fade layer...
            $('#fade , .popup_block').fadeOut(function() {
                $('#fade, a.close').remove();  //fade them both out
            });
            return false;
        });


    });
    function PrintElem(elem)
    {
        Popup($(elem).text());
    }

    function Popup(data)
    {
        var mywindow = window.open('', 'bodypopup1', 'height=400,width=600');
        mywindow.document.write('<html><head><title>my div</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.document.close();
        mywindow.print();
        return true;
    }



    var searchFunction = function () {

        var search_txt = jQuery("#search_text").val();

        if(search_txt){


            if($usertype=='admin')
            {
                $path=$sitePath+"/user/admin/listAllUser";
            }
            else
            {
                $path=$sitePath+"/user/user/listAllUser";
            }



            $.ajax(
            {
                type:'post',
                url:$path,
                async: true,
                data:{
                    "search_txt":search_txt
                },
                success: function(response)
                {
                    if(response)
                    {
                        //    alert(response);

                        if($usertype=='Psychologist')
                        {
                            $('#tab1').html('');
                            $('#tab1').html(response);
                            $("#topleaveforedit").addClass("topleaveforedit");
                            $('#search_text').val(search_txt);
                        }
                        else
                            $('#content').html(response);

                        $("#topleaveforedit").addClass("topleaveforedit");
                    }


                }


            });
        }

    }


    jQuery("#btn_search_user").click(function(e){

        searchFunction();
        e.preventDefault();
    });

    jQuery("#text_search").bind('blur keypress',function(){
        searchFunction();
    });
</script>
<script>
//   $(function() {
//     // add new widget called myTable
//     $.tablesorter.addWidget({
//         // give the widget a id
//         id: "myTable",
//         // format is called when the on init and when a sorting has finished
//         format: function(table) {
//             // loop all tr elements and set the value for the first column
//             for(var i=0; i < table.tBodies[0].rows.length; i++) {
//                 $("tbody tr:eq(" + i + ") td:first",table).html(i+1);
//             }
//         }
//     });

//     $("table").tablesorter({
//         widgets: ['zebra','myTable']
//     });

// });
$(document).ready(function() {
    $.fn.DataTable.ext.pager.numbers_length = 600;
    var t = $('#myTable').DataTable( {
        "lengthMenu": [[100], [100]],
        "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": 0
        } ],
        "order": [[ 1, 'asc' ]]

        } );
    t.on( 'order.dt search.dt', function () {
    t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
    cell.innerHTML = i+1;
    } );
    } ).draw();
} );
</script>
