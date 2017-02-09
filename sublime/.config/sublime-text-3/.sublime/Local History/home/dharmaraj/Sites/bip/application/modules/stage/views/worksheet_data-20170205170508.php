<div id="box1-tabular">
   <?php   if($allFormData){	  ?>
        <table class="gridtable" cellspacing="0" cellpadding="0" border="0" width="782px">
          <thead>
          <tr class="chnge">
              <th width="30px"><?php //echo $this->lang->line("sn");?>Nr.</th>

              <th width="265px"><?php // echo $this->lang->line("steps");?>Steg</th>
              <th width="265px"><?php //echo $this->lang->line("stage"); ?>Del</th>
             <!--  <th width="100px">Typ </th>-->
                <th width="135px" >Skapad den</th>
              <th width="155px">Senast Sparad</th>

            </tr>
          </thead>
          <tbody>
            <?php
$count=1;
$offset=0;
$html = "";

foreach($allFormData as $rows)
{
	if($rows->unread_comments > 0) $new	=	' new';
	else $new=	'';

	$notification = $this->worksheet_model->checkStat($rows->id);
	//$today = date("j") . " " . $month[(date("n")-1)] . " " . date("Y");
	$swedishMonth = $this->config->item('swedishMonth');
	$postedOn = strtotime($rows->posted_on);
	if($postedOn && $postedOn!="0000-00-00")	{
		$postedDate = date("j",$postedOn) . " " . $swedishMonth[(date("n",$postedOn)-1)] . " " . date("Y",$postedOn)." ".date("H",$postedOn).":".date("i",$postedOn);
	}elseif($notification)
		{
			$postedDate = "Ej uppdaterad ";
	}
	else{
		$postedDate = "Ej uppdaterad "; $new	=	' new';
	}


	$updater = $rows->updater;
	if($updater) $postedDate = $postedDate.' '.$updater;

	$sendDate = strtotime($rows->send_date);
	$sendDate = date("j",$sendDate) . " " . $swedishMonth[(date("n",$sendDate)-1)] . " " . date("Y",$sendDate)." ".date("H",$sendDate).":".date("i",$sendDate);

	if($count==1) $first_tr_class="firstRowGridTable";	else  $first_tr_class="";
	$offset++;
	$html.='<tr>';
	$html.='<td  class="'.$first_tr_class.$new.'" onclick="openWS(\''.site_url('worksheet/viewFormData/'.$rows->id).'\')">'.$offset.'</td>';
	//if($userType=="2")
	//$html.='<td class="'.$first_tr_class.'">'.$rows->first_name.'&nbsp;'.$rows->last_name.'</a></td>';
	$html.='<td  class="'.$first_tr_class.$new.'"><a href="'.site_url('worksheet/viewFormData/'.$rows->id).'">'.$rows->step.'</a></td>';
	$html.='<td class="'.$first_tr_class.$new.'" onclick="openWS(\''.site_url('worksheet/viewFormData/'.$rows->id).'\')">'.$rows->stage.'</td>';
	/*$html.='<td>'.$rows->difficulty.'&nbsp;</td>';*/

	//$html.='<td class="'.$first_tr_class.'">HTML</td>';
	$html.='<td class="'.$first_tr_class.$new.'" onclick="openWS(\''.site_url('worksheet/viewFormData/'.$rows->id).'\')">'.$sendDate.'</td>';
	$html.='<td class="'.$first_tr_class.$new.'" onclick="openWS(\''.site_url('worksheet/viewFormData/'.$rows->id).'\')">'.$postedDate.'</td>';
	$html.='</tr>';
}

$rows="";

echo $html;
?>

          </tbody>
        </table>
  <!-- .content#box-1-holder -->
    <?php

	if(isset($paging) && trim($paging) !=''){
		echo $paging;
		echo '</div>';
		echo '</div>';
	}
	?>
    <?php }
      else
		{
		echo 'Här kommer du att kunna se dina svar från delarna i programmet';
		}
    ?>

 </div>
