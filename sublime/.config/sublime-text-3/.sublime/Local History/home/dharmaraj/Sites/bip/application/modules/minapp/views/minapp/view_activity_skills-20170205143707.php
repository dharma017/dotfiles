<div class="boxin addstage" id="box-tasks">
    <div class="reg-header"><?=lang("txt_activity_skills")?></div>
    <div id="box1-tabular" class="content">   
    <table cellpadding="0" cellspacing="0" width="450px" class="gridtable compact">
      <thead>
        <tr>
            <th width="80%"><?=lang("name")?></th>
            <th width="20%"><?=lang("txt_no_of_times")?></th>
     </tr>
      </thead>
      <tbody>
        <?php
        if($activity_skills){
            $sn = 1;
            $last_module_id = 0;
            foreach($activity_skills as $skills){
                
                $module_name = $this->minapp_model->getModuleNameById($skills->module_id);

                if($last_module_id!=$skills->module_id){
                    echo "<tr class='sub-header'><td colspan='2'>".$module_name."</td></tr>";
                }

                echo "<tr>";
                echo "<td>".$skills->skill_name."</td>";
                echo "<td>".$skills->occurrences."</td>";
                echo "</tr>";
                $last_module_id = $skills->module_id;
                $sn++;
            }
        }else{
            echo "<tr><td colspan='3' align='center'>".lang("no_task_avail")."</td></tr>";
        }
        ?>
        
      </tbody>
</table>
<!-- .content#box-1-holder -->

</div>
</div>