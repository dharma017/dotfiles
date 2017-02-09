<?php
   $fetch_tics_levels = $this->stage_model->fetchPatientsTicsLevelForPlay(2);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/shared/css/jquery-ui.css"/>
<script type="text/javascript"  src="<?php echo base_url(); ?>assets/shared/js/jquery-ui.min.js"></script>

<!--begining of the content area-->
<div id ="contentArea" class="imgMarginLeft pad10 row clear template_23 withTimer" >
        <?php  if ($description)
            echo '<div class="clear margin_bottom wrapper600">' . $description . '</div>';
            //echo "<pre>".print_r($this->session,true)."</pre>";
        ?>

        <div class="formentry22">

        <form method="post" name="frmBip"  id="frmBip">
            <div class="timer-container">
                <div class="grey_boxes fleft">
                    <div class="timer-heading"><?php echo lang("txt_tic_timing")?></div>
                    <div id="tic-timer-display" class="timer">00:00</div>
                    <div class="btn-holder">
                        <?php
                         if($this->session->userdata("user_role_type")=="psychologist"){
                        ?>
                                  <button type="button" class="colored-btn green fleft" disabled="disabled"><?php echo lang("txt_tic_start_btn")?></button>
                                <button type="button"  class="colored-btn orange fright" disabled="disabled"><?php echo lang("txt_tic_stop_btn")?></button>
                        <?php
                         }else{
                        ?>
                            <button type="button" id="tic-timer-start" class="colored-btn green fleft"><?php echo lang("txt_tic_start_btn")?></button>
                            <button type="button" id="tic-timer-stop" class="colored-btn orange fright" disabled="disabled"><?php echo lang("txt_tic_stop_btn")?></button>
                        <?php
                        }
                        ?>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="tic-rate-box fleft">
                    <div class="tic-inner-rate-box">
                        <div class="tic-rate-contents">
                            <div id="tics-rate-frequency" class="tics-rate">1,5</div>
                            <div id="tics-rate-label" class="tics-rate-label">Tics per minut</div>
                        </div>
                    </div>
                </div>
                <div class="grey_boxes fleft">
                    <div class="timer-heading"><?php echo lang("txt_no_of_tic")?></div>
                    <div class="div-tics-counter">
                        0
                    </div>
                    <div class="btn-holder">
                         <?php
                         if($this->session->userdata("user_role_type")=="psychologist"){
                        ?>
                            <button type="button" class="colored-btn green" disabled="disabled" style="width:90%">+ <?php echo lang("txt_tics_add")?></button>
                        <?php
                         }else{
                        ?>
                            <button type="button" id="increase-tic-counts" class="colored-btn green" style="width:90%">+ <?php echo lang("txt_tics_add")?></button>
                        <?php
                        }
                        ?>

                    </div>
                </div>
                <div style="clear:both;"></div>
                <div class="tics_level_selector">
                        <div class="level-nav fleft left-nav"></div>
                        <div class="level-names-holder fleft">
                            <?php
                                if($fetch_tics_levels){
                                    $cnt = 1;
                                    foreach($fetch_tics_levels as $levels){
                                        $bestScore = $this->stage_model->getTicsBestScoreV2($templateId, $stepId, $levels->level_id);
                            ?>
                            <span class="hide" data-bestscore="<?php echo $bestScore?>" data-levelid="<?php echo $levels->level_id?>"><?php echo lang("txt_tics_level")." ".$cnt.": "?><?php echo $levels->level_name?></span>
                            <?php
                                        $cnt++;
                                    }
                                }
                              ?>
                        </div>
                        <div class="level-nav fleft right-nav"></div>
                        <div class="clear"></div>
                </div>
                <div class="navigation-links">
                    <span class="best-score fl"><?php echo lang("txt_tic_best_score")?></span><span id="user-tics-best-score">0 <?php echo lang("txt_tics_per_min_symbol")?></span>.
                    <span class="top-10-link fr" style="margin-bottom: 5px">
                        <a href="javascript:void(0);" class="view-top-10-score grayb-blue-buttons"><?php echo lang("txt_tic_view_toplist")?></a>
                        <a href="<?php echo site_url()?>/minapp/manageTicLevels/2" class="manage-tics-level grayb-blue-buttons"><?php echo lang("txt_tics_other_levels")?></a>
                    </span>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="ratingv1_wrapper">

            </div>
            <input type="hidden" id="user_rating" name="user_rating" value="">
            <input type="hidden" id="current-level-id" name="current_level_id" value="0" />
            <input type="hidden" id="tic-counter" name="tic_counter" value="0" />
            <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId?>" />
            <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId?>" />
            <input type="hidden" id="timeelapsed" name="timeelapsed" data-minuteseconds="00:00" value="0">

        </form>
  </div>
</div>
  <!--end of the content area-->
<style type="text/css">

</style>
<script>
    var timer = 0;
    var timeelapsed = 0; //in seconds
    var userRoleType = "<?php echo $this->session->userdata('user_role_type')?>";

    function showHideActivity(doWhat){
        if(typeof $.fancybox!="undefined"){
             if(doWhat=="show"){
                $.fancybox.showActivity();
             }else{
                $.fancybox.hideActivity();
             }
        }else{
            if($("#preLoader").length>0){
                if(doWhat=="show"){
                    $("#preLoader").show();
                }else{
                    $("#preLoader").hide();
                }
            }
        }
    }

    function startTimer(){
        if(userRoleType=="psychologist") return false;

        $("#tic-timer-stop").removeAttr("disabled");
        $("#tic-timer-start").attr("disabled","disabled");
        resetTicsTimerData();
        $("#increase-tic-counts").focus();
        timer = setInterval(function(){
            timeelapsed++;
            $("#timeelapsed").val(timeelapsed);
            //write the value below in div or span to show timer to the user
            $timer = secondsToTimeFormat(timeelapsed);
            $("#tic-timer-display").html($timer);
            $("#timeelapsed").attr("data-minuteseconds",$timer);
        },1000);
    }

    function calculateTicsPerMinute(){
        $timeelapsed = parseInt($("#timeelapsed").val());
        $no_of_tics = parseInt($("#tic-counter").val());


        $totalMinutes = $timeelapsed/60;
        $req_minute = $totalMinutes/$no_of_tics;
        $rounded_min = Math.round($req_minute);

        if($timeelapsed>0 && $no_of_tics>0)
        {
            if($timeelapsed>60 && $rounded_min>1){
                $tic_per_min = "1,0";
                $tics_per_min_text = "<?php echo ucwords(lang('txt_tics_every_x_minutes'))?>";
                $tics_per_min_text = $tics_per_min_text.replace("##","");
                $tics_per_min_text = $tics_per_min_text.replace("@@",$rounded_min);

            }else{

                if($timeelapsed>60){
                    $tic_per_min = ($no_of_tics/$timeelapsed)*60;
                    $tic_per_min = Math.round($tic_per_min);
                }else{
                    $tic_per_min = $no_of_tics;
                }

                $tic_per_min = $tic_per_min.toFixed(1);
                $tic_per_min = $tic_per_min.replace(".",",");
                $tics_per_min_text ="<?php echo lang('txt_tics_per_minute')?>";

            }
        }else{
            $tic_per_min = "0,0";
            $tics_per_min_text ="<?php echo lang('txt_tics_per_minute')?>";
        }


         $("#tics-rate-frequency").html($tic_per_min);
         $("#tics-rate-label").html($tics_per_min_text);
    }

    function showLevel(index){

        if(timer>0) return false; //don't left change level if its running
        $(".level-names-holder").find("span").removeClass("show").addClass("hide");
        $(".level-names-holder").find("span:eq("+index+")").removeClass("hide").addClass("show");
        $current_level = $(".level-names-holder").find("span:eq("+index+")").attr("data-levelid");
        $("#current-level-id").val($current_level);
        $("#current-level-id").attr("data-leveltext",$(".level-names-holder").find("span:eq("+index+")").text());
        var bscore = $(".level-names-holder").find("span:eq("+index+")").attr("data-bestscore");

        if(bscore=="norecords"){
            var bestScoreText = "<?php echo lang('txt_tics_no_record')?>";
            var bestScoreText = bestScoreText.replace("##", (index+1));
            $(".navigation-links").find(".best-score").html(bestScoreText);
            $(".navigation-links").find("#user-tics-best-score").empty();
            //$(".view-top-10-score").hide();
        }else{
            var bestScoreText = "<?php echo lang('txt_tics_best_record_level')?>";
            var bestScoreText = bestScoreText.replace("##", (index+1));
            $(".navigation-links").find(".best-score").html(bestScoreText);
            $(".navigation-links").find("#user-tics-best-score").html($(".level-names-holder").find("span:eq("+index+")").attr("data-bestscore"));
            //$(".view-top-10-score").show();
            calculateTicsPerMinute();
        }

         // reset form initial stage for serialization check
        worksheetDefaultForm = $worksheet_form.serialize();

    }

    function secondsToTimeFormat(time){
        var minutes = Math.floor(time / 60);
        var seconds = time-minutes*60;
        minutes = minutes<10 ? "0"+minutes : minutes;
        seconds = seconds<10 ? "0"+seconds : seconds;
        var finalTime = minutes+":"+seconds;
        return finalTime;
    }

    function showRatingBox(triggerer){
        if(userRoleType=="psychologist") return false;

         $("#tic-rating-block").find(".ratings").removeClass("active");
         $("#tic-rating-block").find(".ratings.class5").addClass("active");
        $("#save-rating-tics").attr("data-trigger",triggerer);

        $.fancybox({
            href: '#tic-rating-block',
            autoDimensions: false,
            width:718,
            height:400,
            onClosed: function(){
                $("#tic-timer-display").html("00:00");
                $("#timeelapsed").val(0);
                $("#timeelapsed").attr("data-minuteseconds","00:00");
            }
        });
    }

    function resetButtons(){
        $("#tic-timer-stop").attr("disabled","disabled");
        $("#tic-timer-start").removeAttr("disabled");
        $("#increase-tic-counts").removeAttr("disabled");
    }

    function resetTicsTimerData(){
        clearInterval(timer);
        timer = 0;
        timeelapsed = 0;
        $("#timeelapsed").val(0);
        $("#timeelapsed").attr("data-minuteseconds","00:00");
        $("#tic-counter").val(0);
        $("#tics_id").val(0);
        $("#tic-timer-display").html("00:00");
        $(".div-tics-counter").html("0");
        $("#user_rating").val("");

        calculateTicsPerMinute();
    }


    function stopTimer(){
        if(timer>0){
            $no_of_seconds_past = parseInt($("#timeelapsed").val());
            if($no_of_seconds_past<60){
                if(confirm("<?php echo lang('txt_tic_early_timer_stop_msg')?>")){
                    resetTicsTimerData();
                    $("#tic-timer-start").removeAttr("disabled");
                    $("#increase-tic-counts").removeAttr("disabled");
                }
                //we are returning false because -
                //Case 1: User stops timer in less than a minute and upon warning user confirms he want to stop.
                //Case 2: warning comes up but he changes up mind and wants to continue.
                return false;
            }

            clearInterval(timer);
            timer = 0;
            timeelapsed = 0;
            $("#tic-timer-stop").attr("disabled","disabled");
            $("#tic-timer-start").attr("disabled","disabled");
            $("#increase-tic-counts").attr("disabled","disabled");
            if($("#tic-counter").val()==0) $("#tic-counter").val(1); //if user stops the timer without having any tics, we consider that user has first tic

            //If user stops the timer quickly (less than a minute), then don't save anything and show them a alert message.




            calculateTicsPerMinute();

            //show rating start
             var b = setTimeout(function(){
                    //reset rating to default 5, incase user plays it again
                    $("#tic-rating-block").find(".ratings").removeClass("active");
                    $("#tic-rating-block").find(".ratings.class5").addClass("active");
                    showHideActivity("hide");
                    $.fancybox({
                        href: $sitePath + '/stage/ticRatingBox',
                        ajax:{
                            type: "POST",
                            data: {
                                "rating_desc": "<?php echo $tics_data['rating_desc']?>",
                                "rating_title": "<?php echo $tics_data['rating_title']?>",
                                "rate_min_text": "<?php echo $tics_data['rate_min_text']?>",
                                "rate_max_text": "<?php echo $tics_data['rate_max_text']?>"
                            }
                        },
                        autoDimensions: false,
                        width:718,
                        height:400,
                        onClosed: function(){
                            resetTicsTimerData();
                            $("#tic-timer-start").removeAttr("disabled");
                            $("#increase-tic-counts").removeAttr("disabled");
                        }
                    });
                    clearTimeout(b);
                    b = 0;
                },1000);
            //show rating end






            /*var b = setTimeout(function(){
                //reset rating to default 5, incase user plays it again

                clearTimeout(b);
                b = 0;
            },1000);*/
        }


    }


    $(document).ready(function(){
        timer = 0;
        timeelapsed = 0; //in seconds
        showLevel(0);

        $(".manage-tics-level").fancybox({
          ajax : {
              type  : "POST"
          }
        });

        $(".left-nav").click(function(){
             if(userRoleType=="psychologist") return false;
             var switch_level = false;
             if(timer>0)//user tried to switch level when the timer is running, so confirm them first
             {
                if(confirm("<?php echo lang('txt_tic_switch_level_msg')?>")){
                    switch_level = true;
                }else{
                    switch_level = false;
                }
             }else{
                switch_level = true;
             }

             if(switch_level==true){
                 resetTicsTimerData(false);
                 $index = $(".level-names-holder").find("span.show").index();
                 if($index>0){
                    $new_index = $index-1;
                 }else{
                    $new_index = 0;
                 }

                 showLevel($new_index);
                 resetButtons();
             }
        });


        $(".right-nav").click(function(){
            if(userRoleType=="psychologist") return false;
             var switch_level = false;
             if(timer>0)//user tried to switch level when the timer is running, so confirm them first
             {
                if(confirm("<?php echo lang('txt_tic_switch_level_msg')?>")){
                    switch_level = true;
                }else{
                    switch_level = false;
                }
             }else{
                switch_level = true;
             }

             if(switch_level==true){
                 resetTicsTimerData(false);
                 $index = $(".level-names-holder").find("span.show").index();
                 $len = $(".level-names-holder").find("span").length-1;
                 if($index<$len){
                    $new_index = $index+1;
                 }else{
                    $new_index = $len;
                 }

                 showLevel($new_index);
                 resetButtons();
             }
        });

        $("#tic-timer-start").click(function(){
            startTimer();
        });

        $("#tic-timer-stop").click(function(){
            stopTimer();
        });


        $("#save-rating-tics").live("click",function(){
            if(userRoleType=="psychologist") return false;

            showHideActivity("show");
            $data = {
                "time_elapsed_in_sec" : $("#timeelapsed").val(),
                "time_elapsed" : $("#timeelapsed").attr("data-minuteseconds"),
                "no_of_tics" : $("#tic-counter").val(),
                "level_id" : $("#current-level-id").val(),
                "step_id" : $("#stepId").val(),
                "template_id" : $("#templateId").val(),
                "rating" : $("#user_rating").val()
             };

             $.ajax({
                url: "<?=site_url()?>/stage/saveTicV2",
                data: $data,
                dataType:"json",
                type: "post",
                beforeSend: function(){

                },
                success: function(data){
                    if(data.status=="success"){
                        resetTicsTimerData();
                        $(".view-top-10-score").trigger("click",["highlightlatest"]);
                        //update the best score;
                        $("#user-tics-best-score").html(data.best_score);
                        $(".level-names-holder").find("span[data-levelid='"+$("#current-level-id").val()+"']").attr("data-bestscore",data.best_score);
                        $("#tic-timer-start").removeAttr("disabled");
                        $("#increase-tic-counts").removeAttr("disabled");
                    }
                    showHideActivity("hide");
                },
                error: function(){
                    resetTicsTimerData();
                    showHideActivity("hide");
                }
             });
        });

        $(document).keyup(function(e){
           //37left
           //39 right
            e.preventDefault();
            if(e.keyCode==37){ //left arrow button in keyboard press
                $fancy_content = $.trim($("#fancybox-content").html());
                if($fancy_content!="") return false; //if any fancybox popup is opened, disable space bar or other button events.
                if(userRoleType=="psychologist") return false;
                $(".left-nav").trigger("click");
            }else if(e.keyCode==39){
                $fancy_content = $.trim($("#fancybox-content").html());
                if($fancy_content!="") return false; //if any fancybox popup is opened, disable space bar or other button events.
                if(userRoleType=="psychologist") return false;
                 $(".right-nav").trigger("click");
            }else if(e.keyCode==13){
                 e.preventDefault();
                 $(".view-top-10-score").trigger("click");
            }else if(e.keyCode==32){
                $fancy_content = $.trim($("#fancybox-content").html());
                if($fancy_content!="") return false; //if any fancybox popup is opened, disable space bar or other button events.
                if(userRoleType=="psychologist") return false;
                $("#increase-tic-counts").trigger("click");
            }
        });

        $("#increase-tic-counts").click(function(){
              if(timer>0){ //If timer is running then only allow increaseing
                    $count = $("#tic-counter").val();
                    $count = parseInt($count)+1;
                    $("#tic-counter").val($count);
                    $(".div-tics-counter").html($count);
                    calculateTicsPerMinute();
              }

        });

        //bind fancybox for top 10 scores
        $(".view-top-10-score").click(function(e,p1){
                $highlightlatest = 0;
                if(p1=="highlightlatest") $highlightlatest = 1;
                $title = $("#current-level-id").attr("data-leveltext");
                $data = {
                    "step_id" : $("#stepId").val(),
                    "template_id" : $("#templateId").val(),
                    "level_id": $("#current-level-id").val(),
                    "highlightlatest": $highlightlatest
                };
                $.ajax({
                    url:  "<?=site_url()?>/stage/fetchTop10TicsV2",
                    data: $data,
                    type: "post",
                    beforeSend: function(){},
                    success: function(data){
                        $.fancybox(data);
                        $(".tics-top10-title").html("<?php echo lang('txt_tic_top_ten')?> - "+$title);
                    }
                });
        });

        //select the rating
        $("#tic-rating-block").find(".ratings").click(function(){
                $("#tic-rating-block").find(".ratings").removeClass("active");
                $(this).addClass("active");
                $(this).parent(".rating-box").find("#user_rating").val($(this).attr("data-rating"));
        });

        //save the task
        $("#save-rating-tics").click(function(){
             showHideActivity("show");
             $data = {
                "tic_id" : $("#tics_id").val(),
                "ratings" : $("#user_rating").val()
             };

             $triggerer = $(this).attr("data-trigger");

             $.ajax({
                url:  "<?=site_url()?>/stage/saveTicV2Ratings",
                data: $data,
                type: "post",
                beforeSend: function(){},
                success: function(data){


                    if($.trim(data)=="success"){
                        $("#tics_id").val(0);
                        var b = setTimeout(function(){
                            showHideActivity("hide");

                            if($triggerer=="last"){
                                $("#"+$triggerer).trigger("click",["ratedalready"]);
                            }else{
                                var location = $("#"+$triggerer).attr("href");
                                document.location.href = location;
                            }

                            clearTimeout(b);
                            b = null;
                        },1000);

                    }
                },
                error: function(){
                    showHideActivity("hide");
                }

             });



        });
    });
</script>
