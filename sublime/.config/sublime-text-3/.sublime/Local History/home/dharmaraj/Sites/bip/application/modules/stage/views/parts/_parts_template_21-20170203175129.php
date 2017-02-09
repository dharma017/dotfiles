<link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css">
<script type="text/javascript" src="<?php echo base_url(); ?>bower_components/jquery-ui/jquery-ui.js"></script>

<!--begining of the content area-->
  <div id ="contentArea" class="imgMarginLeft pad10 row clear template_21">
        <?php  if ($description)
            echo '<div class="clear margin_bottom wrapper600">' . $description . '</div>';
        ?>

        <?php
				    if ($worksheet) {
				    		echo '<input type="hidden" id="isWorksheet" name="isWorksheet" value="1" />';
				        echo $worksheet;
				    } else {
				    		echo '<input type="hidden" id="isWorksheet" name="isWorksheet" value="0" />';
        ?>

<form method="post" name="frmBip"  id="frmBip">
							<div class="formentry21">
							<?php if ($templateData):
							  $neutral_color = $unserialize_data['neutral_color'];
							  $assign_column_to_word = $unserialize_data['assign_column'];
							  $assign_column_color = $unserialize_data['assign_column_color'];
							 ?>

							<input type="hidden" id="assign_column_to_word" value="<?php echo $assign_column_to_word; ?>">
							<input type="hidden" id="assign_column_color" value="<?php echo $assign_column_color; ?>">

							    <div class="frmwords">
							    <?php foreach ($templateData as $key => $row):
							    		if (!$assign_column_color) {
							    			$colorCode= $neutral_color;
							    		}else{
							          $colorCode = $this->stage_model->getFormSettingsColor($row->fld_name);
							    		}
							     ?>

							     <?php if (!empty($colorCode)): ?>
							     		<div data-dropId="<?php echo $row->fld_name;?>" data-dragId="<?php echo $row->id ?>" data-bool="<?php echo $row->fld_bool ?>" class="boxed" style="background-color:#<?php echo $colorCode?>"><span><?php echo $row->fld_label; ?></span></div>
							     <?php endif ?>

							    <?php endforeach ?>
							    </div>
							 <?php endif ?>

							 <?php if ($templateFormSettings): ?>
							   <div class="frmsettings frmsetting<?php echo count($templateFormSettings)?>">
							   <?php foreach ($templateFormSettings as $key => $row1): ?>
							       <div data-drop="<?php echo $row1['fld_name'] ?>" data-dropId="<?php echo $row1['id'] ?>" id="droppable<?php echo $key;?>" class="bigboxed">
							          <div class="innertitlebox" style="background-color:#<?php echo $row1['fld_name']?>">
							              <span> <?php echo $row1['fld_label'] ?> </span>
							          </div>
							        </div>
							   <?php endforeach ?>
							   </div>
							 <?php endif ?>

							</div>

</form>
					 <?php  } ?>

			        <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId?>" />
			        <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId?>" />

			        <?php
			            if($firstStep=="1" && $detailStart=="1")
			            echo '<div style="clear:both; margin-top:15px;">'.$firstTemplateData.'</div>';
			        ?>
			  </div>
  <!--end of the content area-->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/libs/soundjs-NEXT.min.js"></script>

<script>

		equalheight = function(container){

		var currentTallest = 0,
		     currentRowStart = 0,
		     rowDivs = new Array(),
		     $el,
		     topPosition = 0;
		 $(container).each(function() {

		   $el = $(this);
		   $($el).height('auto')
		   topPostion = $el.position().top;

		   if (currentRowStart != topPostion) {
		     for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
		       rowDivs[currentDiv].height(currentTallest);
		     }
		     rowDivs.length = 0; // empty the array
		     currentRowStart = topPostion;
		     currentTallest = $el.height();
		     rowDivs.push($el);
		   } else {
		     rowDivs.push($el);
		     currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
		  }
		   for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
		     rowDivs[currentDiv].height(currentTallest+50);
		   }
		 });
		}

    $(document).ready(function() {

    		equalheight('.bigboxed');

        $( ".boxed" ).draggable({
          appendTo: "body",
          cursor:'move',
          zIndex: 1000,
          revert: true,
        });

        if ($('#isWorksheet').val()==1){
					$(".boxed").draggable("disable");
        }
				else{
					$(".boxed").draggable("enable");
				}

        var assetsPath = "<?php echo base_url() ?>" + "/assets/libs/";
				var sounds = [{
					src: "correct.mp3",
					id: 'correct'
				}, {
					src: "wrong.mp3",
					id: 'wrong'
				},{
					src: "popup.mp3",
					id: 'popup'
				}, ];

				createjs.Sound.registerSounds(sounds, assetsPath);

				var assign_column_to_word = parseInt($("#assign_column_to_word").val()),
						assign_column_color = parseInt($("#assign_column_color").val());

						// console.log('assign_column_to_word',assign_column_to_word)

        $('.bigboxed').each(function(index, el) {
            $( "#droppable"+index ).droppable({
              activeClass: "ui-state-highlight",
              hoverClass: "ui-state-hover",
              drop: function( event, ui ) {

                if (assign_column_to_word) {
                	// console.log('0')
                	// need to match column with word if assign
                	if (ui.draggable.attr('data-dropId') == $(this).attr('data-dropId')) {
										//Play the sound: play (src, interrupt, delay, offset, loop, volume, pan)
										createjs.Sound.play('correct', createjs.Sound.INTERRUPT_NONE, 0, 0, false, 1);

                    var color = ui.draggable.attr('data-drag'),
                    	dragTextId = ui.draggable.attr('data-dragId'),
                    	dropTextId = $(this).attr('data-dropId');

                    $('<p id="ptag_'+dragTextId+'" style="color:#'+color+'">'+ui.draggable.text()+'<a href="#" onclick="removeOptionFromBox(\''+dragTextId+'\');return false;">x</a></p>').appendTo( this );
                    $('<input type="hidden" name="fld_data['+dropTextId+'][]" value="'+dragTextId+'">').appendTo( this );
                    // ui.draggable.remove();
                    $('.frmwords div').filter('[data-dragId="'+dragTextId+'"]').hide();

                    equalheight('.bigboxed');

                	}else{
                		createjs.Sound.play('wrong', createjs.Sound.INTERRUPT_NONE, 0, 0, false, 1);
                	}

                }else{

                	//Play the sound: play (src, interrupt, delay, offset, loop, volume, pan)
										createjs.Sound.play('correct', createjs.Sound.INTERRUPT_NONE, 0, 0, false, 1);

                    var color = ui.draggable.attr('data-drag'),
                    	dragTextId = ui.draggable.attr('data-dragId'),
                    	dropTextId = $(this).attr('data-dropId');

                    $('<p id="ptag_'+dragTextId+'" style="color:#'+color+'">'+ui.draggable.text()+'<a href="#" onclick="removeOptionFromBox(\''+dragTextId+'\');return false;">x</a></p>').appendTo( this );
                    $('<input type="hidden" name="fld_data['+dropTextId+'][]" value="'+dragTextId+'">').appendTo( this );
                    // ui.draggable.remove();
                    $('.frmwords div').filter('[data-dragId="'+dragTextId+'"]').hide();

                    equalheight('.bigboxed');

                }
              }
            });
        });
    });

	function removeOptionFromBox (id) {

		if ($('#isWorksheet').val()==1){
			if ($('#form_editable').val()!=1) return;
		}

		$('#ptag_'+id).remove();

		$('.frmwords div').filter('[data-dragId="'+id+'"]').show();

		$('input[type=hidden]').each(function() {
	        if ($(this).val() === id) {
	            $(this).remove();
	        }
	    });
		equalheight('.bigboxed');
	}

</script>

