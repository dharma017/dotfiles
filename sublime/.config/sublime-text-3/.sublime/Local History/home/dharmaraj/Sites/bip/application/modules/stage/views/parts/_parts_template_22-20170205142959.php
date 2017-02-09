<!--begining of the content area-->
<?php
   $fetch_tics_levels = $this->stage_model->fetchPatientsTicsLevelForPlay(1);
   //echo '<pre>'; print_r($fetch_tics_levels);
?>
<div id ="contentArea" class="imgMarginLeft pad10 row clear template_22 withTimer TicVersion1" >
        <?php  if ($description)
            echo '<div class="clear margin_bottom wrapper600">' . $description . '</div>';
            //echo "<pre>".print_r($this->session,true)."</pre>";
        ?>

        <div class="formentry22">

        <form method="post" name="frmBipTicsV1"  id="frmBip">
            <div class="timer-container">
                <div class="grey_boxes fleft">
                    <div id="tic-timer-display" class="timer">00:00</div>
                    <div class="btn-holder">
                        <?php
                        if($this->session->userdata("user_role_type")=="psychologist"){
                        ?>
                            <button type="button"  class="green" disabled="disabled"><?php echo lang("txt_tic_start_btn")?></button>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" class="orange" disabled="disabled"><?php echo lang("txt_tic_stop_btn")?></button>
                        <?php
                        }else{
                        ?>
                            <button type="button" id="tic-timer-start" class="green"><?php echo lang("txt_tic_start_btn")?></button>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" id="tic-timer-stop" class="orange" disabled="disabled"><?php echo lang("txt_tic_stop_btn")?></button>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="grey_boxes fleft">
                    <div class="tic-message">
                        <?php echo $tics_data["timer_stop_msg"]?>
                    </div>
                    <div class="spacebar-img">
                    <img src="<?php echo base_url()?>images/timer_tic_spacebar.jpg" width="184" />
                    </div>
                </div>

                <div style="clear:both;"></div>
                 <?php
                    $classshownav = "hide";
                    if($fetch_tics_levels){
                        $classshownav = "";
                    }
                 ?>
                <div class="tics_level_selector <?php echo $classshownav?>">

                        <div class="level-nav fleft left-nav"></div>

                        <div class="level-names-holder fleft">
                            <?php
                                if($fetch_tics_levels){
                                    $cnt = 1;
                                    foreach($fetch_tics_levels as $levels){
                                        $bestScore = $this->stage_model->getTicsBestScoreV1($templateId, $stepId, $levels->level_id);
                            ?>
                            <span class="hide" data-bestscoreseconds="<?php echo $bestScore['recorded_time_in_seconds']?>" data-bestscore="<?php echo $bestScore['recorded_time']?>" data-levelid="<?php echo $levels->level_id?>"><?php echo lang("txt_tics_level")." ".$cnt.": "?><?php echo $levels->level_name?></span>
                            <?php
                                        $cnt++;
                                    }
                                }//if conditions
                              ?>
                        </div>
                        <div class="level-nav fleft right-nav"></div>

                        <div class="clear"></div>
                </div>


                <div style="clear:both;"></div>
                <div class="navigation-links">
                    <span class="best-score fl"><span><?php echo lang("txt_tic_best_score")?></span> <span id="user-tics-best-score">
                    <?php echo $tics_best_score?></span>.</span>
                    <span class="top-10-link fr" style="position:relative";>
                    <div class="overlay-btns-tics hide" style="position:absolute;background-color:#f3f3f3;top:-6px;width:100%;height:40px;opacity:0.7"></div>
                    <?php
                    if($this->session->userdata("user_role_type")=="patient" || $this->session->userdata("user_role_type")=="psychologist"){
                    ?>
                    <a href="javascript:void(0);" class="view-top-10-score grayb-blue-buttons"><?php echo lang("txt_tic_view_toplist")?></a>
                    <?php
                    }
                    ?>
                    <?php
                    if($this->session->userdata("user_role_type")=="patient"){
                    ?>
                    <a href="<?php echo site_url()?>/minapp/manageTicLevels/1" class="manage-tics-level grayb-blue-buttons"><?php echo lang("txt_tics_other_levels")?></a>
                    <?php
                    }
                    ?>
                    </span>
                    <div class="clear"></div>
                </div>
                <div class="ClosestRecordBanner dontshow"></div>
                <div class="NewRecordBanner dontshow"><?php echo lang("txt_tics_new_record")?></div>
            </div>
            <div class="ratingv1_wrapper">

            </div>
            <div id="hidden-field-holder">
                <input type="hidden" id="user_rating" name="user_rating" value="">
                <input type="hidden" id="current-level-id" name="current_level_id" value="0" />
                <input type="hidden" id="stepId" name="stepId" value="<?php echo $stepId?>" />
                <input type="hidden" id="templateId" name="templateId" value="<?php echo $templateId?>" />
                <input type="hidden" id="timeelapsed" name="timeelapsed" data-minuteseconds="00:00" value="0">
                <input type="hidden" id="rating_type" name="rating_type" value="<?php echo $tics_data['rating_type']?>">
                <input type="hidden" id="rate_interval" name="rate_interval" value="<?php echo $tics_data['rate_interval']?>">
            </div>

        </form>
  </div>
</div>
  <!--end of the content area-->
<style type="text/css">

</style>
<script>
    var timer = 0;
    var timeelapsed = 0; //in seconds
    var bestscore = 0;
    var rating_box_show_interval = 0;
    var isTimerStarted = false;
    var sessionTimer = null;
    var userRoleType = "<?php echo $this->session->userdata('user_role_type')?>";

    function resetExercise(){
        isTimerStarted = false;
        clearInterval(timer);
        timeelapsed = 0;
        timer = 0;
        rating_box_show_interval = 0;
        $("#tic-timer-display").html("00:00");
        $("#timeelapsed").val(0);

        $("#user_rating").val("");
        $("#timeelapsed").attr("data-minuteseconds","00:00");

        $("input.rating-input").remove();

        $(".NewRecordBanner").addClass("dontshow");
        $(".ClosestRecordBanner").addClass("dontshow");
        $(".navigation-links").removeClass("dontshow");

        $clevel = $("#current-level-id").val();
        $idx = $("span[data-levelid='"+$clevel+"']").index();

        if(arguments.length==0) showLevel($idx);
    }

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

    function sessionAlive(){
        sessionTimer = setInterval(function(){
            keepSessionAlive();
        },600000); //every 10 minutes the the keepSessionAlive function is called, which makes an ajax request to update the session.
        keepSessionAlive();
    }

    function keepSessionAlive(){
        $.ajax({
            url: $sitePath + '/stage/sessiontimeout/0',
            headers: {"Connection" : "keep-alive"},
            beforeSend: function(){},
            success: function(data){
                console.info(data);
            }
        })
    }

    function sessionTimeoutDefault(){
        clearInterval(sessionTimer);
        //$.get($sitePath + '/stage/sessiontimeout/3600');
    }

    function startTimer(){
        isTimerStarted = true;

        if(userRoleType=="psychologist") return false;

        sessionAlive();
        $(".overlay-btns-tics").removeClass("hide");
        $("#tic-timer-stop").removeAttr("disabled").focus();
        $("#tic-timer-start").attr("disabled","disabled");
        timer = setInterval(function(){
            timeelapsed++;
            rating_box_show_interval++;

            $("#timeelapsed").val(timeelapsed);
            //write the value below in div or span to show timer to the user
            $timer = secondsToTimeFormat(timeelapsed);
            $("#tic-timer-display").html($timer);
            if($(".secondary-timer").length>0) $(".secondary-timer").html($timer);
            $("#timeelapsed").attr("data-minuteseconds",$timer);
            checkIfNewRecord(timeelapsed);
            showRatingBox(rating_box_show_interval);
        },1000);
    }

    function secondsToTimeFormat(time){
        var minutes = Math.floor(time / 60);
        var seconds = time-minutes*60;
        minutes = minutes<10 ? "0"+minutes : minutes;
        seconds = seconds<10 ? "0"+seconds : seconds;
        var finalTime = minutes+":"+seconds;
        return finalTime;
    }

    function checkIfNewRecord(currentTime){
        //now select besttime
         $current_level = $("#current-level-id").val();
         $element = $(".level-names-holder").find("span[data-levelid='"+$current_level+"']");

         $bestScore = $element.attr("data-bestscore");
         $bestScoreSeconds = $element.attr("data-bestscoreseconds");
         if($bestScoreSeconds>0){
             $eighty_pc_of_best_record = Math.round(($bestScoreSeconds*80)/100);

             if(currentTime>$bestScoreSeconds){//new-record
                $(".NewRecordBanner").removeClass("dontshow");
                $(".ClosestRecordBanner").addClass("dontshow");
                $(".navigation-links").addClass("dontshow");
             }else if(currentTime>=$eighty_pc_of_best_record && currentTime<= $bestScoreSeconds){
                $(".NewRecordBanner").addClass("dontshow");
                $(".navigation-links").addClass("dontshow");
                $(".ClosestRecordBanner").removeClass("dontshow");
                $message = "<?php echo lang("txt_tics_closer_to_record")?> "+$bestScore;
                $(".ClosestRecordBanner").html($message);
             }
         }
    }

    function getBestRecordForCurrentLevel(){
        $current_level = $("#current-level-id").val();
        $element = $(".level-names-holder").find("span[data-levelid='"+$current_level+"']");

        $bestScore = $element.attr("data-bestscore");
        $bestScoreSeconds = $element.attr("data-bestscoreseconds");

        return parseInt($bestScoreSeconds);
    }

    function showRatingBox(interval){
         var rate_interval = $("#rate_interval").val();
         var rating_type = $("#rating_type").val();
         var tic_interval = timeelapsed;

         if(rating_box_show_interval==rate_interval && rating_type>1){//rating type either of interval rating or combined rating.
                 $.fancybox({
                    href: $sitePath + '/stage/ticRatingBox',
                    ajax:{
                        type: "POST",
                        data: {
                            "tic_version": 1,
                            "tic_interval": tic_interval,
                            "save_tics": false,
                            "rating_desc": "<?php echo $tics_data['rating_desc']?>",
                            "rating_title": "<?php echo $tics_data['rate_interval_title']?>",
                            "rate_min_text": "<?php echo $tics_data['rate_min_text']?>",
                            "rate_max_text": "<?php echo $tics_data['rate_max_text']?>"
                        }
                    },
                    modal: true,
                    autoDimensions: false,
                    width:718,
                    onClosed: function(){}
                });

                $("#hidden-field-holder").append("<input type='hidden' class='rating-input' name='tic_rating[]' value='x^"+tic_interval+"^0^"+getCurrentDateTime()+"' />");
                rating_box_show_interval = 0; //reset interval timer
         }
    }

    function getCurrentDateTime(){
        var currentdate = new Date();
        $year = currentdate.getFullYear();
        $month = (currentdate.getMonth()+1);
        $month = $month < 10 ? "0"+$month : $month;
        $day = currentdate.getDate();
        $day = $day < 10 ? "0"+$day : $day;

        var datetime = $year  + "-"
                + $month + "-"
                + $day + " "
                + currentdate.getHours() + ":"
                + currentdate.getMinutes() + ":"
                + currentdate.getSeconds();

        return datetime;
    }

    function stopTimer(){
        if(timer>0){
               sessionTimeoutDefault();
               $(".overlay-btns-tics").addClass("hide");
                showHideActivity("show");
                $("#tic-timer-stop").attr("disabled","disabled");
                $("#tic-timer-start").removeAttr("disabled");
                clearInterval(timer);
                timer = 0;
                timeelapsed = 0;
                var rating_type = $("#rating_type").val();


                $newtime = $("#timeelapsed").val();
                $bestScore = getBestRecordForCurrentLevel();


                if($newtime>$bestScore){//new record
                    $message_title = "<?php echo $tics_data['new_record_title']?>";
                    $message_text = "<?php echo preg_replace("/\r\n|\r|\n/",'<br/>',$tics_data['new_record_message'])?>";
                    $record_time = secondsToTimeFormat($newtime);
                }else{
                    $message_title = "<?php echo $tics_data['no_new_record_title']?>";
                    $message_text = "<?php echo preg_replace("/\r\n|\r|\n/",'<br/>',$tics_data['no_new_record_message'])?>";
                    $record_time = "";
                }



                var b = setTimeout(function(){
                    //reset rating to default 5, incase user plays it again
                     $("#tic-rating-block").find(".ratings").removeClass("active");
                     $("#tic-rating-block").find(".ratings.class5").addClass("active");
                     showHideActivity("hide");

                     $("#hidden-field-holder").append("<input type='hidden' class='rating-input' name='tic_rating[]' value='' />");

                    if(rating_type==1 || rating_type==3){
                        $.fancybox({
                            href: $sitePath + '/stage/ticRatingBox',
                            ajax:{
                                type: "POST",
                                data: {
                                    "tic_version": 1,
                                    "tic_interval": $("#timeelapsed").val(),
                                    "save_tics": true,
                                    "message": $message_text,
                                    "message_title" : $message_title,
                                    "record_time": $record_time,
                                    "rating_desc": "<?php echo $tics_data['rating_desc']?>",
                                    "rating_title": "<?php echo $tics_data['rating_title']?>",
                                    "rate_min_text": "<?php echo $tics_data['rate_min_text']?>",
                                    "rate_max_text": "<?php echo $tics_data['rate_max_text']?>"
                                }
                            },
                            modal: true,
                            autoDimensions: false,
                            width:718,
                            /*'onComplete' : function(){
                                $('#fancybox-content, #fancybox-content > div').css({width:'550px', height:'auto'});
                                $("#fancybox-wrap").css({width:'570px', height:'auto'});
                                $.fancybox.resize();
                            },*/
                            onClosed: function(){
                                resetExercise();
                            }
                        });
                    }else{//rating type is interval rating only, so we willnot show rating box, instead we save the tics
                        //just show new record or no new record popup

                        saveTics();

                        $.fancybox({
                            href: $sitePath + '/stage/ticRatingBox',
                            ajax:{
                                type: "POST",
                                data: {
                                    "tic_version": 1,
                                    "tic_interval": $("#timeelapsed").val(),
                                    "save_tics": true,
                                    "message": $message_text,
                                    "message_title" : $message_title,
                                    "record_time": $record_time,
                                    "record_msg_only": 1
                                }
                            },
                            modal: true,
                            autoDimensions: false,
                            width:718,
                           /* 'onComplete' : function(){
                                $('#fancybox-content, #fancybox-content > div').css({height:'300px',width:'550px'});
                                $("#fancybox-wrap").css({height:'300px',width:'570px'});
                                $.fancybox.resize();
                            },*/
                            onClosed: function(){
                                $(".view-top-10-score").trigger("click",["highlightlatest"]);
                                resetExercise();
                            }
                        });

                    }

                    clearTimeout(b);
                    b = 0;
                },1000);
        }

    }


    function showLevel(index){
        if(timer>0) return false; //don't left change level if its running
        $(".level-names-holder").find("span").removeClass("show").addClass("hide");
        $(".level-names-holder").find("span:eq("+index+")").removeClass("hide").addClass("show");
        $current_level = $(".level-names-holder").find("span:eq("+index+")").attr("data-levelid");
        $("#current-level-id").val($current_level);
        $("#current-level-id").attr("data-leveltext",$(".level-names-holder").find("span:eq("+index+")").text());
        $score = $(".level-names-holder").find("span:eq("+index+")").attr("data-bestscore");
        $score_seconds = $(".level-names-holder").find("span:eq("+index+")").attr("data-bestscoreseconds");

        if(parseInt($score_seconds)==0){
            var bestScoreText = "<?php echo lang('txt_tics_no_record')?>";
            var bestScoreText = bestScoreText.replace("##", (index+1));
            $(".navigation-links").find(".best-score").html("<span>"+bestScoreText+"</span><span id='user-tics-best-score' style='margin-left:10px'></span>");
            $(".view-top-10-score").hide();
        }else{
            var bestScoreText = "<?php echo lang('txt_tics_best_record_level')?>";
            var bestScoreText = bestScoreText.replace("##", (index+1));
             $(".navigation-links").find(".best-score").find("span:eq(0)").html(bestScoreText);
            if(typeof $score=="undefined"){
                $score = "";
                $("#user-tics-best-score").parent("span").empty();
            }
            $(".navigation-links").find("#user-tics-best-score").html($score);
            $(".view-top-10-score").show();
        }

        // reset form initial stage for serialization check
        worksheetDefaultForm = $worksheet_form.serialize();
    }

    function resetButtons(){
        $("#tic-timer-stop").attr("disabled","disabled");
        $("#tic-timer-start").removeAttr("disabled");
    }

    function saveTics(){
        if(userRoleType=="psychologist") return false;

        showHideActivity("show");
             /*$data = {
                "time_elapsed_in_sec" : $("#timeelapsed").val(),
                "time_elapsed" : $("#timeelapsed").attr("data-minuteseconds"),
                "rating" : $("#user_rating").val(),
                "step_id" : $("#stepId").val(),
                "template_id" : $("#templateId").val()
             };*/

             $data = $("form[name='frmBipTicsV1']").serialize()+"&time_elapsed="+$("#timeelapsed").attr("data-minuteseconds");

             $.ajax({
                url:  "<?=site_url()?>/stage/saveTicV1",
                data: $data,
                dataType:"json",
                type: "post",
                beforeSend: function(){},
                success: function(data){
                    if(data.status=="success"){
                        /*$("#tic-timer-display").html("00:00");
                        $("#timeelapsed").val(0);
                        $("#user_rating").val("");
                        $("#timeelapsed").attr("data-minuteseconds","00:00");*/

                        $(".view-top-10-score").trigger("click",["highlightlatest"]);
                        showHideActivity("hide");
                        //update the best score;
                     //   $("#user-tics-best-score").html(data.best_score);
                        $levelid = $("#current-level-id").val();

                        console.warn("Best score = "+data.best_score.recorded_time);
                        console.warn("Best score seconds = "+data.best_score.recorded_time_in_seconds);

                        $(".level-names-holder").find("span[data-levelid='"+$levelid+"']").attr("data-bestscore",data.best_score.recorded_time);
                        $(".level-names-holder").find("span[data-levelid='"+$levelid+"']").attr("data-bestscoreseconds",data.best_score.recorded_time_in_seconds);



                        resetExercise();
                    }
                }

             });
    }

    var checktest;
    $(document).ready(function(){
        showLevel(0);

        $(document).keydown(function(e) {
            if (e.which == 32) { //disable scroll when spacebar is pressed
                if(e.target.tagName!=="INPUT"){
                    return false;
                }
            }
        });

        $(".manage-tics-level").fancybox({
          ajax : {
              type  : "POST"
          }
        });

        $("#tic-timer-start").click(function(){
            startTimer();
        });

        $("#tic-timer-stop").click(function(){
            stopTimer();
        });

        $(".left-nav").click(function(){
            //if(userRoleType=="psychologist") return false;
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
                 resetExercise(false);

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
            //if(userRoleType=="psychologist") return false;
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
                 resetExercise(false);

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


        $(document).keyup(function(e){
            e.preventDefault();

            if(e.keyCode==32){
                $fancy_content = $.trim($("#fancybox-content").html());
                //if($fancy_content!="") return false; //if any fancybox popup is opened, disable space bar event.
                if(userRoleType=="psychologist") return false;

                if(isTimerStarted==true && $fancy_content==""){
                    stopTimer();
                }else{
                    if($fancy_content=="") startTimer();
                }
            }else if(e.keyCode==13){
                e.preventDefault();
                $(".view-top-10-score").trigger("click");
            }

        });

        //bind fancybox for top 10 scores
        /*$(".view-top-10-score").click(function(e,p1){
                $highlightlatest = 0;
                if(p1=="highlightlatest") $highlightlatest = 1;
                $data = {
                    "step_id" : $("#stepId").val(),
                    "template_id" : $("#templateId").val(),
                    "highlightlatest": $highlightlatest
                };
                $.ajax({
                    url:  "<?=site_url()?>/stage/fetchTop10Tics",
                    data: $data,
                    type: "post",
                    beforeSend: function(){},
                    success: function(data){
                        $.fancybox(data);
                    }
                });
        });*/


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
                    url:  "<?=site_url()?>/stage/fetchTop10Tics",
                    data: $data,
                    type: "post",
                    beforeSend: function(){},
                    success: function(data){
                        $.fancybox(data);
                        $(".tics-top10-title").html("<?php echo lang('txt_tic_top_ten')?> - "+$title);
                    }
                });
        });



        //save the task
        $("#save-rating-tics").live("click",function(){
            saveTics();
        });
    });
</script>
