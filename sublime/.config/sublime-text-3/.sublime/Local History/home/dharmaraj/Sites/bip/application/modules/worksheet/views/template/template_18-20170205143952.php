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
<style>
   /*#stepLadder_ws textarea{
        width: 185px;
        background: #FFF;
        margin-left: 15px;
        padding:8px;
        color: #333;
        font-size: 14px;
        font-family: "Trebuchet MS", sans-serif;
        border:1px solid #8a96a2;
    }
    .history_container #stepLadder_ws textarea{
        width: 180px;
    }
    #stepLadder_ws .roundClass{
        width: 40px;
        height: 40px;
        background:#667988;
        -webkit-border-radius:50%;
        border-radius:50%;
        display: inline-block;
        line-height: 40px;
        text-align: center;
        color: #FFF;
        font-size: 24px;
        font-family: "Trebuchet MS", sans-serif;
        vertical-align: top;
        margin-top: 15px;

    }
    #stepLadder_ws .textBoxABC-5{margin-left: 365px;}
    #stepLadder_ws .textBoxABC-4{margin-left: 285px;}
    #stepLadder_ws .textBoxABC-3{margin-left: 205px;}
    #stepLadder_ws .textBoxABC-2{margin-left: 125px;}
    #stepLadder_ws .textBoxABC-1{margin-left: 45px;}*/
</style>
