</div>
<div class="wrapperNew">
<?php

    if (!$colour) $colour = "a8d975";

    $array_template_for_worksheet = array('4', '5', '6', '7', '9', '10', '11', '12','13','14','16','18','21');

    if ($this->uri->segment(2) != "pdfVersion") :

      echo '<div id="footer" class="clear row">';

            if ($preview=='edit_worksheet'):
            elseif ($preview=='preview'):

            else:

              /**
              CONDITION   : FORM BEING EDITED FROM SUMMARY PAGE.
              TYPE      : SAVE BUTTON
              REDIRECT    : SUMMARY PAGE
             */
              $usertype = getUserType();

              if ($src == "summary") {
                  $site_link = site_url("stage/stageSummary/$stageId");
                  echo '<a style="cursor:pointer" href="'.$site_link.'" class="emailForm btnMidall col3 marginbtnsr" >Spara</a>';
              } else if ($this->uri->segment(2) == "stageSummary") { // @CONFUSED: NEED TO CHECK THIS..
                  $site_link = site_url("stage");
              } else if ($this->uri->segment(5) == "thankyou") { //thank you form , finish button
                    if ($this->session->userdata('logintype')=='user') {
                      echo '<a href="' . site_url("stage") . '"  id="last"  class="col3">'.lang('last').'</a>';
                    } else {
                      echo '<a href="' . site_url("stage/personal") . '"  id="last"  class="col3">'.lang('last').'</a>';
                    }

              } else {
                  if ($firstStep == "1") { //first step , previous button
                  /**
                    CONDITION   : FIRST STEP
                    TYPE    : PREVIOUS BUTTON
                    REDIRECT  : SUMMARY PAGE
                   */
                    if ($this->session->userdata('logintype')=='user') {
                      echo '<a href="' . site_url("stage") . '"  id="previous" class="col1">'.lang('back').'</a>';
                    } else {
                      echo '<a href="' . site_url("stage/personal") . '"  id="previous" class="col1">'.lang('back').'</a>';
                    }
                  }

                  /**
                    CONDITION     : IF PREVIOUS STEP EXISTS
                    TYPE      : PREVIOUS BUTTON
                    REDIRECT    : PREVIOUS STEP
                   */ else if ($previous_step) {
                      echo '<a href="' . site_url("stage/viewStep/$stageId/$previous_step") . '"  id="previous" class="col1">'.lang('back').'</a>';
                  }

                  /**
                    CONDITION     : IF FIRST STEP AND NEXT STEP EXISTS
                    TYPE      : PREVIOUS BUTTON
                    REDIRECT    : NEXT STEP
                   */
                  if ($firstStep == "1" && $next_step) {
                      if ((in_array($templateId, $array_template_for_worksheet)) && ($usertype != 'Psychologist')) { //If step is form , it will be submitted and redirected to next step
                          echo '<a href="'.site_url("stage/viewStep/$stageId/$next_step").'"  id="start"  class="emailForm col3">'.lang('starta').'</a>';
                      } else { //redirected to next step directly
                          if ($show_next)
                            echo '<a href="' . site_url("stage/viewStep/$stageId/$next_step") . '"  id="start" class="col3">'.lang('starta').'</a>';
                      }
                  }

                  /**
                    CONDITION     : IF FIRST STEP AND NEXT STEP DON'T EXITS
                    TYPE      : NEXT BUTTON
                    REDIRECT    : SUBMIT AND SUMMARY PAGE ELSE HOME PAGE
                   */ else if ($firstStep == "1" && !$next_step) { // first step with single step
                      $next_button_label = $thankYouSlide ? "next" : "last";
                      if ((in_array($templateId, $array_template_for_worksheet)) && ($usertype != 'Psychologist')) {
                          echo '<a href="'.site_url().'"  id="' . $next_button_label . '"  class="emailForm col3">'.lang($next_button_label).'</a>';
                      } else {
                      	$summaryPage =  $sitePath+"/stage/summaryPage/"+$stageId;
                          echo '<a id="' . $next_button_label . '" href="'.$summaryPage.'" class="col3">'.lang($next_button_label).'</a>';
                      }
                  }

                  /**
                    CONDITION     : NOT A FIRST STEP AND NO NEXT STEP EXISTS [LAST STEP]
                    TYPE      : NEXT BUTTON
                    REDIRECT    : SUBMIT AND SUMMARY PAGE ELSE HOME PAGE
                   */ else if (!$next_step && $firstStep == "0") {
                      $next_button_label = $thankYouSlide ? "next" : "last";
                      if ((in_array($templateId, $array_template_for_worksheet)) && ($usertype != 'Psychologist')) {
                          echo '<a href="'.site_url("stage/summaryPage/$stageId").'"  id="next"  class="emailForm col3">'.lang('next').'</a>';
                      } else {
                      	$summaryPage =  $sitePath+"/stage/summaryPage/"+$stageId;
                          echo '<a id="' . $next_button_label . '" href="'.$summaryPage.'" class="col3">'.lang($next_button_label).'</a>';
                      }
                  }

                  /**
                    CONDITION     : NOT A FIRST STEP AND O NEXT STEP EXISTS // middle steps
                    TYPE      : NEXT BUTTON
                    REDIRECT    : SUBMIT > SUMMARY PAGE ELSE NEXT STEP
                   */ else if ($next_step && $firstStep == "0") {
                      if ((in_array($templateId, $array_template_for_worksheet)) && ($usertype != 'Psychologist')) {
                          echo '<a href="'.site_url("stage/viewStep/$stageId/$next_step").'"  id="next"  class="emailForm col3">'.lang('next').'</a>';
                      } else {
                        if ($show_next)
                          echo '<a href="' . site_url("stage/viewStep/$stageId/$next_step") . '"  id="next"  class="col3">'.lang('next').'</a>';
                      }
                  }
              }

              endif;

      echo '</div>';

    else:

      if ($show_next) echo '<a href="' . site_url("stage/viewStep/$stageId/$next_step") . '" class="col3"></a>';

    endif;
?>
</div>

</div>
</div>
<div class="bottomStructure" id="bottom" style="background:url(<?php echo base_url() ?>assets/public/css/images/<?php echo $colour; ?>/Bottom.png) no-repeat left top;">
</div>

</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/js/jquery-ui.min.js"></script>
<script lanaguage="javascript">

	  var warning = false;

	  var editor_form = document.getElementById("frmBip");
		if($usertype != 'user')
			editor_form = false;
		if(editor_form)
	    for (var i = 0; i < editor_form.length; i++) {
	        editor_form[i].onchange = function() {
	            warning = true;
	        }
	    }

		if($('#preview').val()=='preview')
		{
			$('#previous').attr('href','');
			$('#previous').attr('onclick','');
			$('#next').attr('href','');
			$('#next').attr('onclick','');
			$('#editMenu').attr('onclick','');
			//$("input").not('#send_comment').attr('disabled','disabled');
			$(".commentContent > a").attr('onclick','');
		}
		$('.wrapper600 a').each(function(){
			var $href = $(this).attr('href');
			if($href)
			if($href.length > 3)
			{
				$href =  $href.replace(/^\s+|\s+$/g,'');
				if($href.indexOf('http') != 0 && $href.indexOf('mailto') != 0)
				{
					$href = "http://"+$href;
					$(this).attr('href',$href);
				}
			}
		});
    $(document).on('click','a',function(){
		/*$('a').click(function(){*/
			if($('#preview').val()=='preview')
			{
				return false;
			}

			if($(this).attr('target') == '_self'  || $(this).closest(".textwrapp").length>0)
			{
        var internal_link = $(this).attr('href');

        if ($(this).hasClass('edit_worksheet'))
          internal_link = internal_link.replace('?preview=preview','?preview=edit_worksheet');

				$.fancybox({
                    'href' 		: internal_link,
                    'width'		: '85%',
                    'height'		: '100%',
                    'autoScale'		: false,
                    'type'		: 'iframe'
                });
				return false;
			}
			else
			{
				return true;
			}
		});
</script>
