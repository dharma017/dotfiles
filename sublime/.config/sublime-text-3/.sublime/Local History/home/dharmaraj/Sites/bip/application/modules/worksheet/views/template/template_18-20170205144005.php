<div class="formentry11">
  <div id="stepLadder_ws">
        <?php
        $count = count($templateForm);
        $i = count($templateForm);
  			$templateDataCount = count($templateForm);
        $templateForm = array_reverse($templateForm);
        foreach($templateForm as $rows)    {

            $id=$rows->id;
            $data = $templateData->$id;

              if(!empty($rows->fld_label))
              {
                echo '<div class="textBoxABC_ws textBoxABC-'.$count.'"><span class="roundClass">'.$count.'</span>';
                echo '<input type="hidden" name="fld_label[]" id="fld_label_'.$i.'" value="'.$rows->id.'" />';
                echo '<textarea placeholder="'.$rows->fld_label.'" name="fld_data[]" id="fld_data_'.$i.'" class="inputs form_texarea" rows="3"  />'.$data.'</textarea>';
                echo '</div>';

              }

              $count --;
              $i--;

            }

        ?>

  </div>
</div>
