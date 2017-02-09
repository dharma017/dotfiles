
    <div class="formentry11">
            <?php
            $count = 1;
            $i = 0;
			$templateDataCount = 0;
      $templateDataArr = json_decode(json_encode($templateData), true);
$newTemplateDataArr = array_values($templateDataArr);
//echo "<pre>";print_r($newTemplateDataArr);echo "</pre>";
            foreach($templateForm  as $p => $rows)    {
               // echo "<pre>";print_r($newTemplateDataArr);echo "</pre>";
                //$data = $userDataPost[$i-1]?$userDataPost[$i-1]:$oldData[$rows->id];
				//$id=$rows->id;
				$data = $newTemplateDataArr[$p];
                if($rows->fld_abc_top=="1"){
                      echo
                      '<div class="textPanelABC_ws">
                        <p>'.$rows->fld_label.'</p>
                           <input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$rows->id.'" />
                        <textarea name="fld_data[]" id="fld_data_'.$i.'"  class="textAreaABC_ws" readonly="readonly"/>'.$data.'</textarea>


                       </div>';
                     if($count<3)
                              echo '<div class="arrowABC_ws" style="float:left;width:28px;position:relative;"><img class="templateEightImg" src="'.base_url().'/assets/public/css/images/arrow_abc.png" style="margin-left:0px;position:absolute;bottom:14px;"></div>';
                        if($count=="3") echo "<div style='clear:both'></div><br />";
                }
                else {
					if(!empty($rows->fld_label))
					{
						echo '<div class="textBoxABC_ws prolonged"><p>'.$rows->fld_label.'</p>
							<input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$rows->id.'" />';
						if($rows->fld_row==1 || !$rows->fld_row)
						{
						   echo '<input name="fld_data[]"  style="width:608px" id="fld_data_'.$i.'" value="'.$data.'" />';
						}
						else if($rows->fld_row>1)
						{
						   echo '<textarea name="fld_data[]" style="width:608px"  id="fld_data_'.$i.'" class="form_texarea" rows="'.$rows->fld_row.'" readonly="readonly"  />'.$data.'</textarea>';
						}

						echo '</div>';
					}
                }
                $count ++;
$i++ ;
            }

            ?>

    </div>
   <script type="text/javascript">
  $(document).ready(function(){

    $('.formentry11').each(function(){
        var highestBox = 0;

        $(this).find('.textPanelABC_ws p').each(function(){
            if($(this).height() > highestBox){
                highestBox = $(this).height();
            }
        });

        $(this).find('.textPanelABC_ws p').css({"height":highestBox});
        $(this).find('.arrowABC_ws').css({"height":highestBox+98});
        // $(this).find('.arrowABC').css({"margin-top":$('.textPanelABC').height()/2});
    });
});
  </script>
