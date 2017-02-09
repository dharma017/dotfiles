<ul class="adm-form">
	<form name="frmAddDifficulty" id="frmAddDifficulty" method="post">
		<li>
			<span><label><strong>Source Difficulty: </strong></label> <?php echo $difficultyName; ?></span>
			
		</li>
		<li>
          <label><strong>Destination Difficulty:</strong></label>
	        <select name="destinationDifficultyId" id="difficulty" >
	            <?php
					$result_diff = $this->stage_model->getAllDifficultyByLang();
					foreach($result_diff as $rows_diff)
					{
						if($rows->difficulty_id==$rows_diff->id) $do_select = 'selected="selected"';
						else $do_select = "";
						echo '<option '.$do_select.' value='.$rows_diff->id.'>'.$rows_diff->difficulty.'</option>';
					}
				?>
	          </select>
		</li>

		<li>
			<input type="button" name="btnSave" id="btnSave" value="<?php echo $this->lang->line("save");?>"  onclick="addOthersDifficulty();" class="button" />
			<input type="button" name="btnCancel" id="btnCancel" value="<?php echo $this->lang->line("cancel");?>" onclick="listOthersDifficulty();" class="button" />
		</li>
		<input type="hidden" value="<?php echo $sourceDifficultyId;?>" name="sourceDifficultyId" id="sourceDifficultyId" />
	</form>
</ul>