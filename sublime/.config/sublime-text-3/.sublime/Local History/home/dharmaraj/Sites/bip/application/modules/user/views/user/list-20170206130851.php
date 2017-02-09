<script type="text/javascript">
$(document).ready(function() {

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
    $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#" class="close"><img src="<?php echo base_url();?>images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');

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
</script>
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
</style>
<?php
$usertype = getUserType();
$psychologistid = ($usertype=='Psychologist') ? $this->session->userdata('user_id') : '';
$offset		= (isset($_POST['offset']))?$_POST['offset']:0;
$orderBy	= (isset($_POST['orderBy']))?$_POST['orderBy']:'first_name';
$orderAlter	= ($orderBy=='asc')?'desc':'asc';
$datalimit	= DATALIMIT;
$result=$this->user_model->getAllUser222($offset,$datalimit,$orderBy,$psychologistid);
$allUser   =  $result[0];
$totalRows	=  $result[1];
$jsfn=array('listUser','"'.$orderBy.'"');
//echo "ajaxPaging($totalRows,$datalimit,$jsfn,$offset)";
$paging	=	 $this->paging_model->ajaxPaging($totalRows,$datalimit,$jsfn,$offset);
?>
<div  class="heading col1">
<?php $result=$this->stage_model->getPageContent(3);
//$usertype=$this->session->userdata('logintype');
?>
   <h1 class="mainsubsheading"><?php echo $result->page_title?$result->page_title:$this->bipPageTitle;?></h1>

</div>
<div id="box" class="box box-100">
  <!-- box full-width -->
  <div class="boxin">
    <div class="header">
	<span id="topleaveforedit">
      <a href="#Add User" style=" margin:0 0 10px"  onclick="addUserForm();"><div class="btnMidallactive  marginbtnsr col3">Lägg till användare </div></a>
	</span>

	  </div>
    <div id="box1-tabular" class="content clear">
	 <form class="plain" action="#" method="post" enctype="multipart/form-data">
	   <?php   if(isset($allUser))
	   {
	?>
       <table width="741" cellpadding="0" cellspacing="0" border="0"  class="gridtable">
          <thead>
            <tr>
              <th width="20px"><?php //echo $this->lang->line("sn");?>Nr.</th>
              <th width="120px"><a style="cursor:pointer" onclick="listUser('first_name ','');"><?php echo $this->lang->line("username");?></a></th>
              <th width="120px"><a style="cursor:pointer" onclick="listUser('first_name ','');"><?php echo $this->lang->line("full_name");?>Fullst&#228;ndiga namn </a></th>

              <th width="150px"><a style="cursor:pointer" onclick="listUser('email ','');"><?php echo $this->lang->line("email");?></a></th>

              <th width="150px"><a  style="cursor:pointer" onclick="listUser('group_id ','');">Grupp</a></th>
              <!--<th width="150px">Acitve To </th>-->

             <!-- <th width="50px"><?php// echo $this->lang->line("published");?></th>-->
              <th width="100px">&nbsp;</th>
            </tr>
          </thead>
        <tbody>
		<?php
$count=1;
foreach($allUser as $rows)
{

    $rows->first_name = $this->encryption->decrypt($rows->first_name);
    $rows->last_name = $this->encryption->decrypt($rows->last_name);

	if($count==1) $first_tr_class="firstRowGridTable";	else  $first_tr_class="";
	$pdfLink = site_url("user/createPdf/".$rows->id);
	if($rows->status=="1")
	$pub_icon = '<img src="'.base_url().'images/admin_icons/right.png" alt="'.$this->lang->line("edit").'">';
	else
	$pub_icon = '<img src="'.base_url().'images/admin_icons/wrong.png" alt="'.$this->lang->line("edit").'">';

	$offset++;

	$rowclass=($rows->active_remaining_day <= '-14') ? 'inctiveuserfont' :'';

	$html.='<tr class="'.$rowclass.'">';
	$html.='<td class="'.$first_tr_class.'">'.$offset.'</td>';
		$html.='<td class="'.$first_tr_class.'">'.$rows->username.'</td>';
	$html.='<td class="'.$first_tr_class.'" >'.$rows->first_name.'&nbsp;'.$rows->last_name.'</a>';

	if($rows->difficulty_id>0)
	$total_stage_comp=$this->user_model->getAllStagewithdiff($rows->difficulty_id,$rows->id);

	//$html.='<td>'.$rows->address.'</td>';
	/*$html.='<td>'.$rows->difficulty.'&nbsp;</td>';*/
	$strpw=$rows->password;
	for($i=0; $i<5;$i++)
 	 {
    $strpw=base64_decode(strrev($strpw)); //apply base64 first and then reverse the string}
 	 }
	$html.='<td class="'.$first_tr_class.'">'.$rows->email.'</td>';
	if($usertype !='Psychologist')
	{
	$html.='<td class="'.$first_tr_class.'">'.$rows->difficulty.'</td>';
	$html.='<td class="'.$first_tr_class.'">'.$rows->psychologist.'</td>';
	}
	$html.='<td class="'.$first_tr_class.'">'.$rows->group_name.'</td>';

	//$html.='<td>'.$rows->email.'</td>';
	//$html.='<td>'.$strpw.'</td>';
	//$html.='<td>'.$pub_icon.'</td>';
	$html.='<td class="'.$first_tr_class.'" align="right">';
	$html.='<a href="#Edit User" class="linkStyle" onclick="editUser('.$rows->id.');" title="Edit User"><img src="'.base_url().'images/admin_icons/edit.png" alt="'.$this->lang->line("edit").'"></a>
	 &nbsp; <a href="#Delete User" class="linkStyle" onclick="deleteUser('.$rows->id.');" title="Delete User"><img src="'.base_url().'images/admin_icons/delete.png" alt="'.$this->lang->line("delete").'"></a>&nbsp <a href="#?w=500" rel="popup_name'.$rows->id.'" class="poplight"><img alt="Delete" title="Detail" src="'.base_url().'images/admin_icons/detail.png"></a>&nbsp;<a href="'.$pdfLink.'" class="linkStyle"  target="_blank"><img alt="pdF" title="PDF" src="'.base_url().'images/admin_icons/icon_pdf.png"></a>';
	$html.='</td></tr>';
	?>
	<div id="popup_name<?php echo $rows->id;?>" class="popup_block">
		<div class="bodypopup" id="bodypopup1">
			<h2 class="iframes">User Details</h2><!--<input type="button" value="Print Div" onclick="PrintElem('#bodypopup1')" />-->

			<div class="menuAdd">
				<ul class="adm-form">
						<li><label class="labeltmplt3"><strong> Name:</strong></label><?php echo $rows->first_name.'&nbsp;'.$rows->last_name;?></li>
						<li><label class="labeltmplt3"><strong> Email:</strong></label><?php echo $rows->email;?></li>
						 <li><label class="labeltmplt3"><strong>Password:</strong></label><?php echo $strpw;?></li>
						 <li><label class="labeltmplt3"><strong>Total No. Logins:</strong></label><?php echo $rows->no_of_login;?></li>
						 <li><label class="labeltmplt3"><strong>No. of days from first to last login:</strong></label><?php echo $rows->days;?></li>
						 <?php
						 $hours = floor($rows->total_time_in_system / 3600);
						  $minutes = floor($rows->total_time_in_system % 3600 / 60);
						 ?>
						<li> <label class="labeltmplt3"><strong>Total time in the system</strong></label>:&nbsp;<?php echo $hours.' : '.$minutes;?></li>
						<?php
						if($rows->user_role !=2)
						{
						?>
						<li> <label class="labeltmplt3"><strong>Completed Stage:</strong></label>&nbsp;<?php echo $total_stage_comp;?></li>
						<?php } ?>
						<li><label class="labeltmplt3"><strong> Active From:</strong></label><?php echo $rows->active_from;?></li>
						<li><label class="labeltmplt3"><strong> Active To:</strong></label><?php echo $rows->active_to;?></li>
						<li> <label class="labeltmplt3"><strong>Status:</strong></label><?php echo ($rows->status==1) ? 'Active' : 'InActive';?></li>
						<?php $remainingactiveday=($rows->active_remaining_day >= 0) ? $rows->active_remaining_day : 'You have no Active days.' ?>
						<li><label class="labeltmplt3"><strong> Remaining Active days:</strong></label><?php echo $remainingactiveday;?>
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
if($paging)
{
echo $paging;
echo '</div>';
echo '</div>';
}
?>
    <?php }
      else
	{
	echo '<h2 align="center" style=" margin-top:30px;">Anv&#228;ndaren finns inte</h2>';
	}
    ?>

  </div>
</div>
