  <?php   if($allFormData){	  ?>
        <table class="gridtable" cellspacing="0" cellpadding="0" border="0" width="782px">
          <thead>
            <tr>
              <th width="30px"><?php //echo $this->lang->line("sn");?>Nr.</th>             
            
              <th width="230px"><?php //echo $this->lang->line("stage"); ?>Del</th>
              <th width="250px"><?php // echo $this->lang->line("steps");?>Steg</th>
               <th width="100px">Typ </th>
              <th width="100px">Senast Sparad</th>
            
            </tr>
          </thead>
          <tbody>
            <?php 
		//	print_r($allFormData);
$count=1;
foreach($allFormData as $rows)
{
	$offset++;	
	$html.='<tr>';
	$html.='<td  onclick="showFormData('.$rows->id.')">'.$offset.'</td>';
	if($userType=="2")
	$html.='<td>'.$rows->first_name.'&nbsp;'.$rows->last_name.'</a></td>';
	
	$html.='<td><a href="'.site_url('worksheet/viewFormData/'.$rows->id).'">'.$rows->stage.'</a></td>';
	/*$html.='<td>'.$rows->difficulty.'&nbsp;</td>';*/
	$html.='<td>'.$rows->step.'</td>';
	$html.='<td>HTML</td>';
	$html.='<td>'.date("j M Y",(strtotime($rows->send_date))).'</td>';
	$html.='</tr>';
}

$rows="";
// to load pdfs/other files.
/*
foreach($allFiles as $rows)
{
	$offset++;	
	$html.='<tr>';
	$html.='<td>'.$offset.'</td>';	
	$html.='<td><a href="'.base_url().'open_file.php?folder=page&file_name='.$rows->upload_file.'">'.$rows->upload_title.'</a></td>';
	/*$html.='<td>'.$rows->difficulty.'&nbsp;</td>';
	$html.='<td>-</td>';
	$html.='<td>'.$rows->upload_type.'</td>';
	$html.='<td>'.date("j M Y",($rows->upload_date)).'</td>';
	$html.='</tr>';
	
}
*/
echo $html;
?>

          </tbody>
        </table>

    <!-- .content#box-1-holder -->
    <?php
	//echo $paging;
	?>
    <?php } 
      else
		{
	if($usertype !='Psychologist')
	echo '<h2 align="center" style=" margin-top:30px;">Filen finns inte i arbetsdokumentet</h3>';
		}
    ?>
