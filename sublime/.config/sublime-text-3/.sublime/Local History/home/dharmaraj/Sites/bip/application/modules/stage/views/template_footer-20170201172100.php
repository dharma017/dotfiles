<?php
if (!$colour)
  $colour = "a8d975";
$array_template_for_worksheet = array('4', '5', '6', '7', '9', '10', '11', '12');
if ($this->uri->segment(2) != "pdfVersion") :
  ?>
<div id="footer" class="clear row">
  <?php
  /**
      CONDITION   : FORM BEING EDITED FROM SUMMARY PAGE.
        TYPE      : SAVE BUTTON
      REDIRECT    : SUMMARY PAGE
  */
      
      $usertype = getUserType();
      
      if ($src == "summary") {
        $site_link = site_url("stage/stageSummary/$stageId");
        echo '<a style="cursor:pointer" onclick="emailForm(\'1\',\'' . $site_link . '\')" class="btnMidall col3 marginbtnsr" >Spara</a>';
  } else if ($this->uri->segment(2) == "stageSummary") { // @CONFUSED: NEED TO CHECK THIS..
    $site_link = site_url("stage");
  }
  /**
      CONDITION   : THANK YOU FORM
        TYPE      : FINISH BUTTON
      REDIRECT    : HOME PAGE
  */ else if ($this->uri->segment(5) == "thankyou") {
      echo '<a href="' . site_url("stage") . '"  id="last"  class="col3">'.lang('last').'</a>';
    } else {
  /**
    CONDITION   : FIRST STEP
      TYPE    : PREVIOUS BUTTON
    REDIRECT  : SUMMARY PAGE
  */
    if ($firstStep == "1") {
      echo '<a href="' . site_url("stage") . '"  id="previous" class="col1">'.lang('back').'</a>';
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
    echo '<a onclick="emailForm(\'1\',\'' . site_url("stage/viewStep/$stageId/$next_step") . '\')" href="#"  id="start"  class="col3">'.lang('starta').'</a>';
        } else { //redirected to next step directly
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
        echo '<a onclick="emailForm(\'1\',\'\')" href="#"  id="' . $next_button_label . '"  class="col3">'.lang($next_button_label).'</a>';
      } else {
        echo '<a onclick="summaryPage(' . $stageId . ')"  id="' . $next_button_label . '" href="#" class="col3">'.lang($next_button_label).'</a>';
      }
    }
  /**
      CONDITION     : NOT A FIRST STEP AND NO NEXT STEP EXISTS [LAST STEP]
        TYPE      : NEXT BUTTON
      REDIRECT    : SUBMIT AND SUMMARY PAGE ELSE HOME PAGE
  */ else if (!$next_step && $firstStep == "0") {
      $next_button_label = $thankYouSlide ? "next" : "last";
      if ((in_array($templateId, $array_template_for_worksheet)) && ($usertype != 'Psychologist')) {
        echo '<a onclick="emailForm(\'1\',\'' . site_url("stage/summaryPage/$stageId") . '\')" href="#"  id="next"  class="col3">'.lang('next').'</a>';
      } else {
        echo '<a onclick="summaryPage(' . $stageId . ')"  id="' . $next_button_label . '" href="#" class="col3">'.lang($next_button_label).'</a>';
      }
    }
  /**
      CONDITION     : NOT A FIRST STEP AND O NEXT STEP EXISTS // middle steps
        TYPE      : NEXT BUTTON
      REDIRECT    : SUBMIT > SUMMARY PAGE ELSE NEXT STEP
  */ else if ($next_step && $firstStep == "0") {
      if ((in_array($templateId, $array_template_for_worksheet)) && ($usertype != 'Psychologist')) {
        echo '<a onclick="emailForm(\'1\',\'' . site_url("stage/viewStep/$stageId/$next_step") . '\')" href="#"  id="next"  class="col3">'.lang('next').'</a>';
      } else {
        echo '<a href="' . site_url("stage/viewStep/$stageId/$next_step") . '"  id="next"  class="col3">'.lang('next').'</a>';
      }
    }
  }
  ?>
  <?php
  echo '</div>';
  else:
    echo '<a href="' . site_url("stage/viewStep/$stageId/$next_step") . '" class="col3"></a>';
  endif;
  ?>
</div>
</div>
<div class="bottomStructure" id="bottom" style="background:url(<?php echo base_url() ?>assets/public/css/images/<?php echo $colour; ?>/Bottom.png) no-repeat left top;">
</div>
</div>
<div>
</div>
<script lanaguage="javascript">
/*
var warning = false;
window.onbeforeunload = function() {

if (!warning) {
return 'You have unsaved changes. By santosh';
}
}

var editor_form = document.getElementById("frmBip");
for (var i = 0; i < editor_form.length; i++) {
editor_form[i].onchange = function() {
warning = true;
      console.log('new changes in the form');
}
}
/*

if($('#next').length) {
document.getElementById("next").onclick = function() {
warning = false;
}
}
if($('#last').length) {
document.getElementById("last").onclick = function() {
warning = false;
}
}
//*/
</script>