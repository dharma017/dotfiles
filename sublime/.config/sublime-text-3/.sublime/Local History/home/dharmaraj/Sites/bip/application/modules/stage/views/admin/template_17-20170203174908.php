<?php
$readonly = ($task=="editStep") ? '': '';

$limit = array(
		'correct_answer'=>55,
		'circle_text'=>120,
		'message_title'=>20,
		'message_content'=>250,
		'left_box_text'=>21,
		'right_box_text'=>40,
	);


$jsonLimit = json_encode((array)$limit);
 ?>
<div class="box box-100">
  <!-- box full-width -->
  <div class="boxin">
    <div class="header">
      <h3><?php echo $templateHeading;	?> </h3>
    </div>

      <form name="frmAddStep" id="frmAddStep" method="post">
        <?php $this->load->view("stage/admin/template_common_form");?>
          </ul>
        <div class="preview_circle"> <img width="400px" src="<?php echo base_url().'assets/admin/images/preview_circle.png' ?>" alt=""> </div>
        <div class="preview_rectangle" style="display:none;"> <img width="400px" src="<?php echo base_url().'assets/admin/images/preview_rectangle.png' ?>" alt=""> </div>

      <div style="clear:both"></div>
	   <br />

        <div class="optbtn">
			<label><strong>Template Style</strong></label>

			<span><input <?php echo (!empty($unserialize_data['group1']) && $unserialize_data['group1']=='opt1') ? 'checked="checked"': ''; ?> type="radio" name="group1" value="opt1" id="rd1" checked> <label style="float:none;" for="rd1">Sortera</label></span>

			<span><input <?php echo (!empty($unserialize_data['group1']) && $unserialize_data['group1']=='opt2') ? 'checked="checked"': ''; ?> type="radio" name="group1" value="opt2" id="rd2"><label style="float:none;" for="rd2">Para ihop</label></span>
		</div>

		<div style="clear:both"></div>
	   <br />

		<div id="container_fields">

			<div class="opt1 desc">
				<div class="box_form">
					<ul class="lists" style="width: 300px; float:left;clear:both;">
					    <li>
					        <label><strong>Left Circle Text</strong><span><?php echo $limit['correct_answer']; ?> characters</span></label>
					        <textarea maxlength="<?php echo $limit['correct_answer']; ?>" id="circle_text_1" name="category1[]" class="texts circle1 " rows="2" cols="50"><?php echo (!empty( $unserialize_data['category1'][0])) ? $unserialize_data['category1'][0]: ''; ?></textarea>
					    </li>
					    <li>
					    	<label><strong>Left Circle Font Size (&nbsp;px)</strong></label>
					    	<input onchange="handleChange(this);" type="text" name="left_circle_font_size" value="<?php echo (!empty( $unserialize_data['left_circle_font_size'])) ? $unserialize_data['left_circle_font_size']: 35; ?>">
					    </li>
					    <li>
					        <label><strong>Right Circle Text</strong><span><?php echo $limit['correct_answer']; ?> characters</span></label>
					        <textarea maxlength="<?php echo $limit['correct_answer']; ?>" id="circle_text_2" name="category1[]" class="texts circle1" rows="2" cols="50"><?php echo (!empty( $unserialize_data['category1'][1])) ? $unserialize_data['category1'][1]: ''; ?></textarea>
					    </li>
					    <li>
					    	<label><strong>Right Circle Font Size (&nbsp;px)</strong></label>
					    	<input onchange="handleChange(this);" type="text" name="right_circle_font_size" value="<?php echo (!empty( $unserialize_data['right_circle_font_size'])) ? $unserialize_data['right_circle_font_size']: 35; ?>">
					    </li>
					</ul>

					<ul style="width: 400px; float:right;">
					    	<li style="width: 100%; float:left;">
						    	<label style="width: 100%; float:left;"><strong>Message Title:</strong><span><?php echo $limit['message_title']; ?> characters</span></label>
								<textarea maxlength="<?php echo $limit['message_title']; ?>" <?php echo $readonly; ?> id="circle_message_title" name="circle_message_title" class="texts" rows="2" cols="50"><?php echo (!empty( $unserialize_data['circle_message_title'])) ? $unserialize_data['circle_message_title']: ''; ?></textarea>
					    	</li>
					    	<li style="width: 100%; float:left;">
						       <label style="width: 100%; float:left;"><strong>Message Content</strong><span><?php echo $limit['message_content']; ?> characters</span></label>
							   <textarea maxlength="<?php echo $limit['message_content']; ?>" <?php echo $readonly; ?> id="circle_message_content" name="circle_message_content" class="texts" rows="2" cols="50"><?php echo (!empty( $unserialize_data['circle_message_content'])) ? $unserialize_data['circle_message_content']: ''; ?></textarea>
					        </li>
				    </ul>

				</div>

				<div>

					<?php

						$count = 0;

						if (!empty($unserialize_data)) {
							$total = count($unserialize_data['fldText1']);
						}else{
							$total = 1;
						}

						for ($i=0; $i < $total ; $i++) {

						$count++;

					?>

				    <div class="box_form" id="row<?php echo $count;?>">

					    <ul class="lists" style="width: 300px; float:left;">

					    	<li class="box-text">
						    	<label><strong>Text:</strong><span><?php echo $limit['circle_text']; ?> characters</span></label>
				                <textarea maxlength="<?php echo $limit['circle_text']; ?>" name="fldText1[]" class="texts" rows="1" cols="50"><?php echo (!empty( $unserialize_data['fldText1'][$i])) ? $unserialize_data['fldText1'][$i]: ''; ?></textarea>
					    	</li>

					    	<li>
						       <label><strong>Correct answer:</strong></label>

						        <select name="fldOption1[]">

						        <option <?php echo ($unserialize_data['fldOption1'][$i]==$unserialize_data['category1'][0]) ? 'selected="selected"': ''; ?> value="<?php echo $unserialize_data['category1'][0]; ?>" class="circle_text_opt1" ><?php echo (!empty($unserialize_data['category1'][0])) ? $unserialize_data['category1'][0]: 'True'; ?></option>

						        <option <?php echo ($unserialize_data['fldOption1'][$i]==$unserialize_data['category1'][1]) ? 'selected="selected"': ''; ?> value="<?php echo $unserialize_data['category1'][1]; ?>" class="circle_text_opt2" ><?php echo (!empty($unserialize_data['category1'][1])) ? $unserialize_data['category1'][1]: 'False'; ?></option>

						        </select>

					        </li>


					    </ul>

					    <ul style="width: 400px; float:right;">

					    	<li style="width: 100%; float:left;">
						    	<label style="width: 100%; float:left;"><strong>Message Title:</strong><span><?php echo $limit['message_title']; ?> characters</span></label>
								<textarea maxlength="<?php echo $limit['message_title']; ?>" name="fldTitle1[]" class="texts fldTitle1" rows="2" cols="50"><?php echo (!empty( $unserialize_data['fldTitle1'][$i])) ? $unserialize_data['fldTitle1'][$i]: ''; ?></textarea>
					    	</li>

					    	<li style="width: 100%; float:left;">
						    	<label style="width: 100%; float:left;"><strong>Message Content:</strong><span><?php echo $limit['message_content']; ?> characters</span></label>
				                <textarea maxlength="<?php echo $limit['message_content']; ?>" name="fldMessage1[]" class="texts fldMessage1" rows="1" cols="50"><?php echo (!empty( $unserialize_data['fldMessage1'][$i])) ? $unserialize_data['fldMessage1'][$i]: ''; ?></textarea>
					    	</li>

					    </ul>

					    <?php if($count>1) echo '<a href="#" onClick="removeFormField1(\'#row'.$count.'\'); return false;" class="remover">Remove</a>';?>


				    </div>

				    <?php } ?>

				  </div>

	       </div>

	       <div class="opt2 desc">
				<div class="box_form">

					<ul>
				    	<li style="width: 100%; float:left;">
					    	<label style="width: 100%; float:left;"><strong>Message Title:</strong><span><?php echo $limit['message_title']; ?> characters</span></label>
							<textarea maxlength="<?php echo $limit['message_title']; ?>" <?php echo $readonly; ?> id="rect_message_title" name="rect_message_title" class="texts" rows="2" cols="50"><?php echo (!empty( $unserialize_data['rect_message_title'])) ? $unserialize_data['rect_message_title']: ''; ?></textarea>
				    	</li>
				    	<li style="width: 100%; float:left;">
					       <label style="width: 100%; float:left;"><strong>Message Content</strong><span><?php echo $limit['message_content']; ?> characters</span></label>
						   <textarea maxlength="<?php echo $limit['message_content']; ?>" <?php echo $readonly; ?> id="rect_message_content" name="rect_message_content" class="texts" rows="2" cols="50"><?php echo (!empty( $unserialize_data['rect_message_content'])) ? $unserialize_data['rect_message_content']: ''; ?></textarea>
				        </li>
				    </ul>

				</div>

		        <?php

						$count = 0;

						if (!empty($unserialize_data)) {
							$total = count($unserialize_data['fldText2']);
						}else{
							$total = 1;
						}

						for ($i=0; $i < $total ; $i++) {

						$count++;

					?>

				    <div class="box_form" id="row<?php echo $count;?>">


					    <ul class="lists" style="width: 300px; float:left;">

					    	<li>
					        	<label><strong>Left Box Text</strong><span><?php echo $limit['left_box_text']; ?> characters</span></label>
					        	<textarea maxlength="<?php echo $limit['left_box_text']; ?>" name="category2[]" id="rcategory1" class="texts" rows="2" cols="50"><?php echo (!empty( $unserialize_data['category2'][$i])) ? $unserialize_data['category2'][$i]: ''; ?></textarea>
					        </li>

					    	<li>
						    	<label><strong>Right Box Text</strong><span><?php echo $limit['right_box_text']; ?> characters</span></label>
						    	<textarea maxlength="<?php echo $limit['right_box_text']; ?>" name="fldText2[]" class="texts" rows="1" cols="50"><?php echo (!empty( $unserialize_data['fldText2'][$i])) ? $unserialize_data['fldText2'][$i]: ''; ?></textarea>
					    	</li>

					    </ul>

					    <ul style="width: 400px; float:right;">

					    	<li style="width: 100%; float:left;">
						    	<label style="width: 100%; float:left;"><strong>Message Title:</strong><span><?php echo $limit['message_title']; ?> characters</span></label>
								<textarea maxlength="<?php echo $limit['message_title']; ?>" name="fldTitle2[]" class="texts fldTitle2" rows="2" cols="50"><?php echo (!empty( $unserialize_data['fldTitle2'][$i])) ? $unserialize_data['fldTitle2'][$i]: ''; ?></textarea>
					    	</li>

					    	<li style="width: 100%; float:left;">
						    	<label style="width: 100%; float:left;"><strong>Message Content:</strong><span><?php echo $limit['message_content']; ?> characters</span></label>
						    	<textarea maxlength="<?php echo $limit['message_content']; ?>" name="fldMessage2[]" class="texts fldMessage2" rows="1" cols="50"><?php echo (!empty( $unserialize_data['fldMessage2'][$i])) ? $unserialize_data['fldMessage2'][$i]: ''; ?></textarea>
					    	</li>

					    </ul>

				    	<?php if($count>1) echo '<a href="#" onClick="removeFormField2(\'#row'.$count.'\'); return false;" class="remover">Remove</a>';?>

				    </div>

		        <?php } ?>

	       </div>

		</div>

	<input type="hidden" id="field_id1" value="<?php if($task=="editStep") echo count($unserialize_data['fldTitle1']); else echo '1'?> ">
	<input type="hidden" id="field_id2" value="<?php if($task=="editStep") echo count($unserialize_data['fldTitle2']); else echo '1'?> ">

	<input type="button" value="Add More Field &raquo;" id="addFields" class="savebtns"  style=" margin-left:0;" onClick="addFormFields()" />

    <div class="clear"></div>

    <?php $this->load->view("stage/admin/template_form_footer"); ?>

      </form>

  </div>
</div>

<style type="text/css">
	.box_form{width:90%;}
	.remover{margin-top: 0;  margin-right: -400px;}
	.optbtn{
		float: none;
		padding-left: 0;
	}
	#container_fields span{
		margin-left: 10px;
	  	font-weight: normal;
	  	font-size: 10px;
	  	color: gray;
	}
	#container_fields select{
	margin-left: 0;
	}
	label.error{
		display: none !important;
	}

</style>

<script type="text/javascript">

function handleChange(input) {

    if (input.value < 0) input.value = 0;

    if (input.value > 35) {
    	alert("max font size supported is 35")
    	input.value = 35;
    }
}

function toggleAddButton () {

	if ($("input[type='radio'][name='group1']:checked").val()=='opt1'){
		$(".opt1").show();

		$('.preview_rectangle').hide();
		$('.preview_circle').show();

		if (document.getElementById("field_id1").value>=10)
			$('#addFields').hide();
		else
			$('#addFields').show();

	}
	else{
		$(".opt2").show();
		$('.preview_circle').hide();
		$('.preview_rectangle').show();

		if (document.getElementById("field_id2").value>=10)
			$('#addFields').hide();
		else
			$('#addFields').show();
	}
}

$(document).ready(function(){

 	var editStep = '<?php echo $task; ?>';

  	$(".desc").hide();

  	toggleAddButton();

  	$("input[name=group1]").change(function() {

        var test = $(this).val();

        $(".desc").hide();

        $("."+test).show();

        $('#frmAddStep').removeData('validator');

        $('#addFields').show();

        toggleAddButton();

    });

    $('#circle_text_1').keyup(function () {
	  $('.circle_text_opt1').text(this.value);
	  $('.circle_text_opt1').val(this.value);
	});

	$('#circle_text_2').keyup(function () {
	  $('.circle_text_opt2').text(this.value);
	  $('.circle_text_opt2').val(this.value);
	});

	if (editStep!=='editStep') { //only for new step

		$('#circle_message_title').keyup(function () {
		  $('.fldTitle1').text(this.value);
		});

		$('#circle_message_content').keyup(function () {
		  $('.fldMessage1').text(this.value);
		});

		$('#rect_message_title').keyup(function () {
		  $('.fldTitle2').text(this.value);
		});

		$('#rect_message_content').keyup(function () {
		  $('.fldMessage2').text(this.value);
		});

	};


});

function addFormFields() {

	var limit = JSON.parse('<?php echo $jsonLimit ?>');
	// console.log(limit)

	if ($("input[type='radio'][name='group1']:checked").val()=='opt1') {

		var field_id = document.getElementById("field_id1").value;
		id = (field_id*1)+1;

		$('#addFields').show();
		if (id>=10) {
			$('#addFields').hide();
		}

		var circle_text_opt1 =  $('#circle_text_1').val(),
			circle_text_opt2 =  $('#circle_text_2').val(),
		 	message_title =  $('#circle_message_title').val(),
		 	message_content =  $('#circle_message_content').val();

		$(".opt1").append('<div class="box_form"  id="row' + id + '" style="display:block;"> <ul class="lists" style="width: 300px; float:left;"> <li class="box-text"> <label><strong>Text:</strong><span>'+limit.circle_text+' characters</span></label> <textarea maxlength="'+limit.circle_text+'" name="fldText1[]" class="texts" rows="1" cols="50"></textarea> </li> <li> <label><strong>Correct answer:</strong></label> <select name="fldOption1[]"> <option value="'+circle_text_opt1+'" class="circle_text_opt1" >'+circle_text_opt1+'</option>; <option value="'+circle_text_opt2+'" class="circle_text_opt2" >'+circle_text_opt2+'</option>; </select> </li> </ul> <ul style="width: 400px; float:right;"> <li style="width: 100%; float:left;"> <label style="width: 100%; float:left;"><strong>Message Title:</strong><span>'+limit.message_title+' characters</span></label> <textarea maxlength="'+limit.message_title+'" name="fldTitle1[]" class="texts fldTitle1" rows="2" cols="50">'+message_title+'</textarea> </li> <li style="width: 100%; float:left;"> <label style="width: 100%; float:left;"><strong>Message Content:</strong><span>'+limit.message_content+' characters</span></label> <textarea maxlength="'+limit.message_content+'"  name="fldMessage1[]" class="texts fldMessage1" rows="1" cols="50">'+message_content+'</textarea> </li> </ul> <a href="#" onClick="removeFormField1(\'#row' + id + '\'); return false;" class="remover">Remove</a> </div>');

		document.getElementById("field_id1").value = id;

	}else{

		var field_id = document.getElementById("field_id2").value;
		id = (field_id*1)+1;

		$('#addFields').show();
		if (id>=10) {
			$('#addFields').hide();
		}

		var message_title =  $('#rect_message_title').val(),
		 	message_content =  $('#rect_message_content').val();

		$(".opt2").append('<div class="box_form"  id="row' + id + '" style="display:block;"> <ul class="lists" style="width: 300px; float:left;"> <li><label><strong>Left Box Text</strong><span>'+limit.left_box_text+' characters</span></label> <textarea maxlength="'+limit.left_box_text+'" name="category2[]" class="texts" rows="2" cols="50"></textarea></li><li> <label><strong>Right Box Text:</strong><span>'+limit.right_box_text+' characters</span></label> <textarea  maxlength="'+limit.right_box_text+'" name="fldText2[]" class="texts" rows="1" cols="50"></textarea> </li></ul><ul style="width: 400px; float:right;"> <li style="width: 100%; float:left;"> <label style="width: 100%; float:left;"><strong>Message Title:</strong><span>'+limit.message_title+' characters</span></label> <textarea maxlength="'+limit.message_title+'" name="fldTitle2[]" class="texts fldTitle2" rows="2" cols="50">'+message_title+'</textarea> </li> <li style="width: 100%; float:left;"> <label style="width: 100%; float:left;"><strong>Message Content:</strong><span>'+limit.message_content+' characters</span></label> <textarea maxlength="'+limit.message_content+'" name="fldMessage2[]" class="texts fldMessage2" rows="1" cols="50">'+message_content+'</textarea> </li> </ul> <a href="#" onClick="removeFormField2(\'#row' + id + '\'); return false;" class="remover">Remove</a> </div>');

		document.getElementById("field_id2").value = id;
	}

}

function removeFormField1(id) {

	var field_id = document.getElementById("field_id1").value;
	if(field_id>1)
	{
		field_id = (field_id - 1);
		document.getElementById("field_id1").value = field_id;
		$(id).remove();
		$(id).fadeOut();
		$('#addFields').show();

	}

}

function removeFormField2(id) {

	var field_id = document.getElementById("field_id2").value;
	if(field_id>1)
	{
		field_id = (field_id - 1);
		document.getElementById("field_id2").value = field_id;
		$(id).remove();
		$(id).fadeOut();
		$('#addFields').show();
	}

}


</script>

