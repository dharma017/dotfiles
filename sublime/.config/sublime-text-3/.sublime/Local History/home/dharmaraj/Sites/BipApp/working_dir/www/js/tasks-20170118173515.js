/********* Task Related Functions  ---- Training List page--******/
var Training = {
    TaskLists: [],
    TrainingLists: [],
    editedTrainingID: 0,
    editedTaskid: 0,
    reminders:[],
    feedback:[],
    hideGraph:1,
    isSaveonProgess: false,

    checksmsCode: function() {
        var userdetails = $.jStorage.get('userdetails');
        if (userdetails.code === $('#smsCode').val()) {
            Training.setTrainings();
        } else {
            $('#smserror').show();
        }
    },
    setTrainings: function() {
        //console.log('here 2');
        
        Training.isSaveonProgess = false;
        var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '"}';

        ////console.log(json);
        callWebService('activeTasks', json, function(response) {
            // alert(response.status);
            //console.log('active 3');

            if (response.status === 'ok') {
                if (response.data !== 'no data found') {
                    
                    if (response.data.length == 1) {
                        //changepage('showTraining',response.data.);
                        var taskid = "";
                        var tagtype = "";
                        var tagid = "";
                        $(response.data).each(function() {
                            taskid = this.taskid;
                            //console.log(this.tag);
                            if (this.tag == 1)
                                tagtype = "Traningsuppgift";
                            else
                                tagtype = "Smartmatning";
                            tagid = this.tag;
                        });
                        //console.log("Change page now");
                        enableIScroll();
                        Training.TaskLists = response.data;
                        // changepage('Register');
                        // 
                        //console.log("tag id is " + tagtype);

                        if (tagid == 2)

                            Training.showTraining(taskid, tagtype);

                        else
                            changepage('TrainingList');

                        $('#Register #headerTop a:first').hide()
                    } else {
                        //console.log('active tasks next');
                        changepage('TrainingList');
                        $('#Register #headerTop a:first').show()
                    }


                    enableIScroll();
                    var setTaskListsTimer = setTimeout(function(){
                        Training.TaskLists = response.data;
                        $('#lstTraining').html(Training.getTraningListHtml(1));
                        $('#lstTraining').listview('refresh');
                        $('#lstTraining2').html(Training.getTraningListHtml(2));
                        $('#lstTraining2').listview('refresh');
                        clearTimeout(setTaskListsTimer);
                        setTaskListsTimer = 0;
                    },100);
                    
                   
                    refreshScroll('TrainingList');

                } else {
                    //console.log('no data found!');
                }
            } else {
                msgBox(MESSAGE.NO_INTERNET);
            }
        });
    },
    getTraningListHtml: function(tagid) {
        var trainigHtml = [];
        var traningSlot = 5;
        var count = 0;

        console.info("getTraningListHtml called with tag id: "+tagid);

        $(Training.TaskLists).each(function() {
           //console.log(Training.getpracticeslotnumber(this.practice));
            traningSlot = Training.getpracticeslotnumber(this.practice);
            console.info("THIS = "+JSON.stringify(this));
            if (this.tag == tagid || this.tag == 3) {
                count++;
                if (tagid == 1)
                    tagtype = "Traningsuppgift";
                else
                    tagtype = "Smartmatning";
                trainigHtml.push('<li data-role="" data-taskid="' + this.taskid + '" onclick="Training.showTraining(' + this.taskid + ',\'' + tagtype + '\');">');
                trainigHtml.push('<a href="javascript:void(0);">' + this.heading + '</a>');
                trainigHtml.push('<span class="sectioned box-size-border">');
                var i;
               
                for (i = 1; i <= traningSlot; i++) {

                    if (i <= this.practice) {
                        trainigHtml.push('<span class="section green">&nbsp;</span>');
                    } else {
                        trainigHtml.push('<span class="section ">&nbsp;</span>');
                    }
                }

                trainigHtml.push('</span>');
                trainigHtml.push('</li>');
            }
        });

        if (tagid == 1 && count != 0) {
            $('#otherLst').show();
        }

        if (tagid == 2 && count == 0) {

            $('.tag2').hide();
            $('.tag1 .hidethis').css('display', 'block');

        } else if (tagid == 2 && count != 0) {
            $('#otherLst').hide();
            $('.tag2').show();
            $('.tag1 .hidethis').css('display', 'none');
        }

        if (tagid == 1 && count == 0) {
            $('.tag1').hide();


        } else if (tagid == 1 && count != 0) {
            $('.tag1').show();

        }
        $('tag3').hide();

        return trainigHtml.join('');
    },
    getpracticeslotnumber: function(practice) {
        var traningSlot = 5;
        if (practice >= 5) {
            if (practice < 20) {
                traningSlot = (parseInt(practice / 10, 10) + 1) * 10;
            } else if (practice >= 20 && practice < 40) {
                traningSlot = 40;
            } else if (practice >= 40 && practice < 100) {
                traningSlot = 100;
            } else if (practice >= 100) {
                traningSlot = 145;
            }
        }
        return traningSlot;
    },
    showTraining: function(taskid, tagtype) {
        //Disable play button in pain reporting at startup, later its enabled accordingly later.
        //Training.activateDeactivateTask({"hour":"00", "minute":"00"});


        console.log("u are here " + taskid + " " + tagtype);
        Training.isSaveonProgess = false;
        $('.rangeslider a').hide();

        // $('#slider-fill_2_2').change();
        //$('#slider-fill_2_4').change();

        $('#txtComments').val('');
        Training.editedTaskid = taskid;
        Training.editedTrainingID = 0;
        ////console.log(Training.TaskLists);
        ////console.log("taskid is " + taskid);
        // //console.log("Array is " + Training.TaskLists);
        var tasks = $.grep(Training.TaskLists, function(e) {
            return parseInt(e.taskid, 10) === parseInt(taskid);
        });

        var trainingData = $.parseJSON($.jStorage.get('userdetails').training);
        
        switch (parseInt(trainingData.type, 10)) {

            //case 1:
            //case 2:
            //    //console.log('executed for common');

            case 1:
                $('#training_type_1_anxiety').text(trainingData.anxiety);
                $('#training_type_1_next').text(trainingData.txt_button);
                $('#Register .checkicon').removeClass('start');

                break;

            case 2:
                //start button has different image
                $('#Register .checkicon').addClass('start');
                var trainingStep, popupID;
                for (trainingStep = 1; trainingStep < 6; trainingStep++) {

                    $popupIDSelector = $('#TrainingZone_2_' + trainingStep);

                    if (trainingData[trainingStep].hasOwnProperty('headline')) {

                        $popupIDSelector.find('.popupTitle').text('' + trainingStep + '. ' + trainingData[trainingStep].headline);

                    }


                    if (trainingData[trainingStep].hasOwnProperty('image')) {
                        $aniImage = trainingData[trainingStep].image;
                        if(window.cordova){
                            $imgFile = $aniImage.split("/").pop();
                           
                            if(filehelper.checkFileExist(cordova.file.dataDirectory + $imgFile)){
                                $aniImage = cordova.file.dataDirectory + $imgFile+"?rand="+Math.random();
                            }
                        }else{
                             $aniImage = trainingData[trainingStep].image;
                        }
                        $popupIDSelector.find('.popUpimg img').attr('src', $aniImage);

                    }

                    if ((trainingStep == 2 || trainingStep == 4)) {

                        $popupIDSelector.find('#subTitle').text(trainingData[trainingStep].text);

                        if (trainingData[trainingStep].hasOwnProperty('ten')) {

                            $popupIDSelector.find('.range_label_zero').text('0 = ' + trainingData[trainingStep].zero);

                        }
                        if (trainingData[trainingStep].hasOwnProperty('zero')) {
                            $popupIDSelector.find('.range_label_ten').text('10 = ' + trainingData[trainingStep].ten);
                        }

                    }

                    if (trainingData[trainingStep].hasOwnProperty('text')) {
                        //console.log(trainingData[trainingStep].text);
                        $popupIDSelector.find('.trainingDContent').html(displayHtml(trainingData[trainingStep].text));

                    }

                    if (trainingData[trainingStep].hasOwnProperty('button')) {

                        $popupIDSelector.find('.next').text(trainingData[trainingStep].button);

                    }


                }


                break;

        }

        $('.checkicon').removeClass('deactivateTask');


        if (navigator.userAgent.toLowerCase().indexOf("android") > -1) {
            $(".checkicon")
                .bind("touchstart", function() {
                    $(this).addClass("fake-active");
                })
                .bind("touchend", function() {
                    $(this).removeClass("fake-active");
                })
                .bind("touchcancel", function() {
                    // sometimes Android fires a touchcancel event rather than a touchend. Handle this too.
                    $(this).removeClass("fake-active");
                });
        }

        $('.checkicon div span').text("");
        ////console.log('Working');
        // //console.log(tasks.length);
        if (tasks.length > 0) {

            var task = tasks[0];
           
            task.reminder=Training.reminders;
            task.first_reminder=Training.reminders[0];

            var userdetails = $.jStorage.get('userdetails');

          //  if (task.hide_graph == "true") {
            if (Training.hideGraph == 1) {
                ////console.log("true result");
                $('.taskgraph').css('display', 'none');
            } else {
                ////console.log("false result");
                $('.taskgraph').css('display', 'block');
            }

            ////console.log(task.reminder);
            // //console.log("tag type is word "+tagtype);
            if (task.tag == 2 || task.tag == 3) {
                //console.log("task tag type is" + task.tag);
                $('#Register .checkregister').addClass(tagtype);
                $('#TrainingZone_1_2 .trainingZoneContent .popupContent .popUpsectioned:nth-child(3)').hide();
                $('#TrainingZone_1_2 .trainingZoneContent .popupContent .popUpsectioned:nth-child(5)').hide();
            } else {
                $('#Register .checkregister').removeClass("Smartmatning");
                $('#TrainingZone_1_2 .trainingZoneContent .popupContent .popUpsectioned:nth-child(3)').show();
                $('#TrainingZone_1_2 .trainingZoneContent .popupContent .popUpsectioned:nth-child(5)').show();
            }

            $('.checkicon').attr("data-tag",task.tag);

            //if (task.hide_graph == "true") {
           if (userdetails.hide_graph == 1) {
                ////console.log("true result");
                $('.taskgraph').css('display', 'none');
            } else {
                ////console.log("false result");
                $('.taskgraph').css('display', 'block');
            }

            if (tagtype == 'Smartmatning') {
                //console.log("Hide number " + task.hide_number);
                var userdetails = $.jStorage.get('userdetails');

                /*console.warn(task.Estimates);
                console.warn("TASK = "+JSON.stringify(task));*/
                var totalestimates =0;
                if(task.training!=undefined && task.training!="" && task.training.length>0){
                    totalestimates = task.training.length;
                }
                var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","taskid":"' + task.taskid + '","lastupdatedate":""}';
                // //console.log(task.Estimates);
                ////console.log(task.Estimates.length);
                $('#estimated_smartning span:last').text(totalestimates);
                $('#noofdays_smartning span:last').text(task.TodayDays);

                if (task.hide_number == "true") {
                    ////console.log("true result");
                    $('.smartning_stat').css('display', 'none');
                } else {
                    ////console.log("false result");
                    $('.smartning_stat').css('display', 'block');
                }

                $('.Traningsuppgift_show').hide();
                // d:
                $('.major-content-box').addClass('smattningdesign');

               /* callWebServiceLive("getservertime", "{}", function(response) {
                   
                   var d =  {
                        hour: response.data.hour,
                        minute: response.data.minute
                   };

                   Training.activateDeactivateTask(d); // Activate deactivate task only if task is of tag2
                });*/
                this.activateDeactivateTask();
                

            } else {
                //$('.taskgraph').css('display', 'block');
                $('.Traningsuppgift_show').show();
                $('.smartning_stat').hide();
                $('.major-content-box').removeClass('smattningdesign');

            }
           
            //console.log('Working');
            var traningSlot = 5;
            $('#TaskHeading,#taskTitle,#perweekgraphHeading,#trainingRating').html(task.heading);
            //$('#divpopupTitle').html(task.anxiety);
            $('#divpopuprange').html('<span class="range_label_ten">10 =  ' + trainingData.ten + '</span> <span class="range_label_zero">0 = ' + trainingData.zero + '</span>');
            // $('#divpopuprange').html('10 = ' + trainingData.ten + ' 0 = ' + trainingData.zero);
            $('#subTitle').text(trainingData.text);
            var progressHtml = [];

            traningSlot = Training.getpracticeslotnumber(task.practice);

            /*var i;
            for (i = 1; i <= traningSlot; i++) {
                if (i <= task.practice) {
                    progressHtml.push('<span class="section green">&nbsp;</span>');
                } else {
                    progressHtml.push('<span class="section">&nbsp;</span>');
                }
            }*/

            $('#progressbar').html(progressHtml.join(''));

            $('#Register .checkicon').removeClass('full');
            $('#Register .checkicon').addClass('unchecked');


            Training.editedTrainingID = 0;
            $('#savetraining').unbind('click');
            $('#savetraining').click(function() {
                Training.saveTraining(taskid);
            });

            $('#savetraining2').unbind('click');
            $('#savetraining2').click(function() {
                Training.saveTraining(taskid);
            });

            changepage('Register');
            enableIScroll();
            refreshScroll('Register');
            setTimeout(function() {
                refreshScroll('Register');
            }, 10);
        }
    },
    activateDeactivateTask:function(){
                var played_count=0;
                 // Initializing date to check the status of task
                var d = new Date();
                var hour = d.getHours();
                var minute = d.getMinutes();
               /* var hour = serverTime.hour;
                var minute = serverTime.minute;*/

                var prevrecordedtime = "";
                var activatetask = false;
                var activatedtime = "";
                var lastplayed=Training.getTodaysLastTrainedTime();
                var reviewdReminders=[];
                /*$(Training.reminders).each(function(){

                    console.log(this);

                })*/
                for(i=0;i<Training.reminders.length;i++){

                        var time=Training.reminders[i];
                        splitTime=time.split(":");
                        if(parseInt(splitTime[0])>parseInt(lastplayed.h))
                            reviewdReminders.push(time);
                        else if(parseInt(splitTime[0])==parseInt(lastplayed.h) && parseInt(splitTime[1])>parseInt(lastplayed.m))
                            reviewdReminders.push(time);
                        else
                            played_count++;
                }
                
                // Set first reminder time as the first time to activate task
                var nextTimeToActivate = reviewdReminders[0];

                //Get the first reminder of task from reminder json -- Required because there may be null or undefined in reminder list 
                if (reviewdReminders != null) {

                    var cc = 0;
                    $(reviewdReminders).each(function(i, e) {
                        
                        if (cc == 0 && e != undefined) {

                            nextTimeToActivate = e;
                            cc++;
                        }
                    });
                

                }
  
                var activatecount = 0;
                

                $(reviewdReminders).each(function(i, e) { // Loop over task reminder
                    var time = e;
                    
                    if (time == null || time == "")// No need to check if time is null or blank
                        return true;

                    var timesplittednow = time.split(":"); // Split hour and minute of time

                    if (activatecount == 1) { //set activate count to 0 if it is 1
                        activatecount = 0;
                    }

                    // Comparing current hour and minute with task reminder
                    if (hour > timesplittednow[0]) {  //if current hour is greater than current reminder activate task  
                        activatetask = true;
                        activatedtime = time;
                        //activatecount=1;
                    } else if (hour == timesplittednow[0] && minute >= timesplittednow[1]) { // If hour is same and minute is greater then also activate task
                        activatetask = true;
                        activatedtime = time;

                    }else{ //else set the the time as next time to activate
                        if(nextTimeToActivate=="")
                            nextTimeToActivate=time;
                    }
                    
                    var timesplittedprev = prevrecordedtime.split(":");

                    prevrecordedtime = time;
                })
               

                if (activatetask == true) { // If task got activated from above mechanism show activated status and page
                    if ($('.played_time').length == 0)
                        $('.checkregister').after("<span class='played_time' style='display:none'>" + activatedtime + "</span>");
                    else
                        $('.played_time').text(activatedtime);
                }
                if (activatetask == false) { // Else show deactivated statsus and page
                    if (activatecount == 1 && activatedtime != "")
                        nextTimeToActivate = activatedtime;
                    // nextTimeToActivate=$('.nextTimeToActivate').text();
                    $('.checkicon').addClass('deactivateTask');
                    if(reviewdReminders.length==0)
                        nextTimeToActivate=Training.reminders[0];
                    else
                        nextTimeToActivate=reviewdReminders[0];
                    $('.deactivateTask div span').html("<p>Du har gjort <br />skattning " + played_count + " </p><p>Nästa skattning<br /> kl " + nextTimeToActivate + "</p>");// Give information to user when this task is going to activate next
                    // $('.checkregister').css('background-image','../images/grey_button_BIPapp.png');
                } else {
                    $('.checkicon div span').text('');
                    $('.checkicon').removeClass('deactivateTask');
                }


    },
     getTodaysLastTrainedTime:function(){
        nowhigh={m:0,h:0};
        today=moment().format('YYYY-MM-DD');
        tommorow=moment().add(1,"days").format('YYYY-MM-DD');
        
        $(Training.TaskLists).each(function(){
            if(this.tag==2){
                $self=this;
                if($self.training!=undefined && $self.training.length>0){
                  
                   /* $($self.training).each(function(){
                        trainingdatetime=moment(this.trainingdatetime).format('YYYY-MM-DD');
                        if(trainingdatetime==today){
                            m=moment(this.trainingdatetime).format("mm");
                            h=moment(this.trainingdatetime).format("HH");
                            if(h>nowhigh.h)
                            {
                                nowhigh.m=m;
                                nowhigh.h=h;
                            }else if(h==nowhigh.h && m>nowhigh.m){
                                nowhigh.m=m;
                                nowhigh.h=h;
                            }
                        }

                    })*/
                    
                      var trn=$self.training[0];
//                      console.warn("TRAINING: "+JSON.stringify(trn));
                        trainingdatetime=moment(trn.trainingdatetime).format('YYYY-MM-DD');
                        if(trainingdatetime==today){
                            m=moment(trn.trainingdatetime).format("mm");
                            h=moment(trn.trainingdatetime).format("HH");
                            if(h>nowhigh.h)
                            {
                                nowhigh.m=m;
                                nowhigh.h=h;
                            }else if(h==nowhigh.h && m>nowhigh.m){
                                nowhigh.m=m;
                                nowhigh.h=h;
                            }
                        }
                 }
               
            }

        })
        //console.log(nowhigh);
        return(nowhigh);
    },
    saveTraining: function(taskid) {
        if (Training.isSaveonProgess === false) {
            Training.isSaveonProgess = true;
            var userdetails = $.jStorage.get('userdetails');

            /*var json = '{"userid":"' + userdetails.user_id + '","":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","taskid":"' + taskid + '",';
            json += '"trainingId":"' + Training.editedTrainingID + '","trainingdatetime":"' + $("#practicedate").val() + '","estimatedvalue":"' + $('#slider-fill').val() + '",';
            json += '"comment":"' + checkinput($("#txtComments").val()) + '"}';*/
            played_time = "";
            if ($('.played_time').length > 0) {

                played_time = $('.played_time').text();
            }
            var submit_object = {
                userid: userdetails.user_id,
                tokenkey: userdetails.tokenkey,
                deviceId: DeviceID,
                taskid: taskid,
                trainingId: Training.editedTrainingID,
                type: Training.process.type(),
                played_time: played_time

            };

            switch (submit_object.type) {
                case 1:
                    submit_object.trainingdatetime = $("#practicedate").val();
                    submit_object.estimatedvalue = $('#slider-fill').val();
                    submit_object.comment = checkinput($("#txtComments").val());
                    submit_object.training_duration="";
                    submit_object.estimatedvalue_end="";

                    break;
                case 2:
                    submit_object.estimatedvalue = $('#slider-fill_2_2').val();
                    submit_object.estimatedvalue_end = $('#slider-fill_2_4').val();
                    submit_object.training_duration = Training.process.sec;
                    submit_object.trainingdatetime = Training.process.completedTime;
                    submit_object.comment="";
                    break;
            }


            var json = JSON.stringify(submit_object);
            var trainingcount=0;
            // //console.log(json);

                Training.isSaveonProgess = false;
                //if (response.status === "ok") {
                  
                    var tag=0;
                     $(Training.TaskLists).each(function(index) {
                       
                        if (parseInt(this.taskid) == taskid) {
                            //console.log("Hitting");
                            tag=this.tag;
                        }else{
                            
                        }
                    });
                   
                     if(tag==2){
                        $(Training.TaskLists).each(function(index) {
                              //  //console.log("loop task id " + parseInt(this.taskid, 10) + " Real taskid " + taskid);

                                if (this.taskid == taskid) {
                                    ////console.log("Task id is " + this.taskid + " Current estimate is " + this.Estimates);
                                    this.Estimates++;
                                }

                                var count = 0;
                                $self = this;
                              //  //console.log(this);
                                var reminderfound = 0;
                                var remcount = 0;
                      

                        //         $($self.reminder).each(function(i, e) {
                        //             ////console.log("REminder "+e);
                        //             if (played_time != "" && reminderfound == 0 && e != played_time) {
                        //                 //$self.reminder[count] = null;
                        //                 remcount++;
                        //                 delete $self.reminder[count];
                        //             }
                        //             if (e == played_time) {
                        //                 //console.log(e);
                        //                 remcount++;
                        //                 delete $self.reminder[count];
                        //                 //$self.reminder[count] = null;
                        //                 reminderfound = 1;

                        //             }
                        //             count++;
                        //              ////console.log($self.reminder);
                        //         });
                        //         console.log($self.reminder);
                        // ////console.log(remcount);
                        //         $self.played_count += remcount;

                        });


                     }
                     var lasttrainingid=0;
                     var newtrainingid="";
                   ////console.log(Training.TaskLists);
                    if (Training.editedTrainingID === 0) {
                        var totalcount=Training.TaskLists.length;
                        $(Training.TaskLists).each(function(index) {
                            var count = 0;
                            $self = this;
                            //console.log(" HIT1 " + this);

                            // $self.Estimates++;

                            if (parseInt($self.taskid, 10) === taskid) {
                                trainingcount=parseInt(this.training.length)+1;
                                var lastinserted="";
                               
                                trainingid=0;
                                if(this.training.length!=undefined && this.training.length!=0)
                                    lasttrainingid=this.training[(this.training.length-1)].app_training_id;
                                else
                                    lasttrainingid=0;
                                newtrainingid=trainingid;
                                var nowtime=moment().format("YYYY-MM-DD");
                                var jsond={ 'app_training_id':(lasttrainingid+1),"trainingId":trainingid,"trainingdatetime":submit_object.trainingdatetime,"estimatedvalue":submit_object.estimatedvalue,"estimatedvalue_end":submit_object.estimatedvalue_end,"training_duration":null,"type":"1","comment":submit_object.comment,"edited":"0"};
                                if($self.training==null)
                                        $self.training=[];
                                $self.practice += 1;
                                 if(this.training.length!=undefined && this.training.length!=0)
                                        $self.training.unshift(jsond);
                                else{
                                    $self.training=[];
                                    $self.training.push(jsond);
                                }
                                traningSlot = Training.getpracticeslotnumber($self.training.length);
                                var progressHtml = new Array();
                                for (var i = 1; i <= traningSlot; i++) {
                                    if (i <= $self.training.length) {
                                        progressHtml.push('<span class="section green">&nbsp;</span>');
                                    } else {
                                        progressHtml.push('<span class="section">&nbsp;</span>');
                                    }
                                }
                                $("#progressbar").html(progressHtml.join(''));

                                $("#lstTraining li[data-taskid=" + taskid + "] .sectioned.box-size-border").html(progressHtml.join(''));
                                $("#lstTraining2 li[data-taskid=" + taskid + "] .sectioned.box-size-border").html(progressHtml.join(''));
                                //console.log(" HIT2 " + this);
                                // return false;
                            }
                            if (--totalcount==0) {
                                    //active_tasks.data.task=Training.TaskLists;
                                    console.log("Active task data are : ");
                                  //  console.log(active_tasks);
                                    // updateDB('active_tasks',active_tasks,function(){  console.log("Success updating activetask"); });

                            };
                        });
                    }else{
                           
                            //active_tasks.data.task=Training.TaskLists;
                            console.log("Active task else data are : ");
                            //console.log(active_tasks);
                            // updateDB('active_tasks',active_tasks,function(){  console.log("Success updating activetask"); });

                    }



                    var toadd={
                               
                                'training_id':newtrainingid,
                                'task_id':taskid,
                                'trainingDateTime':submit_object.trainingdatetime,
                                'estimatedValue':submit_object.estimatedvalue,
                                'trainingDuration':submit_object.training_duration,
                                'type':Training.process.type(),
                                'comment':submit_object.comment,
                                'edited':0,
                                'estimatedValueEnd':submit_object.estimatedvalue_end
                            };

                    callWebService('saveTraining', toadd, function(response) {
                        console.log("Training saved in local database");
                    });


                    //active_tasks.data.task=Training.TaskLists;
                    console.log("Active task data are : ");
                  //  console.log(active_tasks);
                    // updateDB('active_tasks',active_tasks,function(){  console.log("Success updating activetask"); });

                    var estimate=submit_object.estimatedvalue;
                    var msg="";
                    console.log(trainingcount);
                    for(i=0;i<Training.feedback.rating.length;i++){
                        if((i+1)==Training.feedback.rating.length)
                                msg=Training.feedback.message[i];
                        else if(trainingcount<=Training.feedback.rating[i]){
                            console.log("yeah");
                                msg=Training.feedback.message[i];
                                break;
                        }

                    }
                    console.log(i);
                    if (($('#TrainingList #bodyContent1 li').length - 2) == 1) {
                       // $('#TrainingList #bodyContent1 li:first').trigger('click')
                        showalert(msg,'clicktrigger');
                        //showalert(response.data.message);

                    } else{
                     //   changepage('TrainingList');
                        showalert(msg,'TrainingList');
                    }


                // } else {
                //     msgBox(MESSAGE.NO_INTERNET);
                // }
           
        }
    },
    buildTrainingList: function(trainings) {
        Training.TrainingLists = trainings;
        var htmlArray = [];
        $(trainings).each(function() {
            //htmlArray.push('<li data-onclick="Training.editTraining(' + this.trainingId + ');"><a data-href="javascript:void(0);" href="#tidigare_review" class="calendar-icon">' + this.trainingdatetime.replace(' ', ', ') + '</a></li>');
            var trainingid=this.trainingId;
            htmlArray.push('<li onclick="Training.reviewTraining(\'' + this.app_training_id + '\');"><a href="javascript:void(0);" class="calendar-icon">' + this.trainingdatetime.replace(' ', ', ') + '</a></li>');
        });
        htmlArray.push('<li style="visibility:hidden;"><a href="javascript:void(0);" class="calendar-icon">2013-07-12, 14:54</a></li>');
        htmlArray.push('<li style="visibility:hidden;"><a href="javascript:void(0);" class="calendar-icon">2013-07-12, 14:54</a></li>');
        htmlArray.push('<li style="visibility:hidden;"><a href="javascript:void(0);" class="calendar-icon">2013-07-12, 14:54</a></li>');
        $('#Tidigare .mar-top-12').html(htmlArray.join(''));
        $('#Tidigare .mar-top-12').listview('refresh');

        refreshScroll('Tidigare');
    },
    showTrainings: function() {
        Training.isSaveonProgess = false;

        // var jobAlreadyStarted = Training.showTrainings_doing || false;
        // if (jobAlreadyStarted) {
        //     ////console.log('fired twice');
        //     //lock for multiple
        //     return false;
        // }
        Training.showTrainings_doing = true;



        var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","taskid":"' + Training.editedTaskid + '","lastupdatedate":""}';
        //console.log(json);
        callWebService('getOldTrainings', json, function(response) {
            if (response.status === 'ok') {
                changepage('Tidigare');
                enableIScroll();

                Training.buildTrainingList(response.data);

            } else if (response.status === 'error') {
                msgBox(response.message);
            } else {
                msgBox(MESSAGE.NO_INTERNET);
            }
        }, function() {

            Training.showTrainings_doing = false;
        });
    },
    editTraining: function(trainingId) {
        Training.editedTrainingID = trainingId;
        Training.isSaveonProgess = false;
        $('#Register .checkicon').click();
    },

    process: {

        type: function() {

            if ($.jStorage.get('userdetails').training) {

                return parseInt($.parseJSON($.jStorage.get('userdetails').training).type, 10);

            } else {
                //junk for testing only!!!
                var random = Math.floor(Math.random() * 2) + 1;
                var data = $.jStorage.get('userdetails');
                data.rating = random;

                data.tokenkey=$.jStorage.get('bip_jwt');
                
                $.jStorage.set('userdetails', data);
                return random;
            }

        },

        bindEvents: function() {
            $('.trainingZoneContent').on('click', '.abort', Training.process.abort);
            $('.trainingZoneContent').on('click', '.next', Training.process.nextStep);
            //$('#defaultTimeSpent').removeClass('bip_hidden');
            //$('#editTimeSpent').addClass('bip_hidden');
            $(window).on('BIP.abortstep', Training.process.onAbortProcess);

        },
        unbindEvents: function() {
            $('.trainingZoneContent').off('click', '.abort', Training.process.abort);
            $('.trainingZoneContent').off('click', '.next', Training.process.nextStep);
            $(window).off('BIP.abortstep', Training.process.onAbortProcess);
        },
        scrolls: [],
        init: function() {
            Training.adminSettings = $.jStorage.get('userdetails');
            Training.process.stopwatch = new Stopwatch();


            Training.process.sec = 0;
            Training.process.unbindEvents();
            Training.process.bindEvents();
            Training.process.currentStep = 1;
            Training.process.gotoStep(1);
            $('#showstopwatch').text('00:00');



        },

        html: {

            type1: function(stepNum) {
                var popHtml = '<div class="popupContent box-size-border border-radius">';
                switch (stepNum) {
                    case 1:
                        popHtml += '<div class="fixedtop">\
                    <span class="steps">1 av 3</span>\
                    <a href="javascript:void(0);" class="abort border-radius red-gradient">X</a>\
                    </div>\
                    <div class="popupTitle" id="divpopupTitle">Hur kändes det?</div>\
                    <div class="range" id="divpopuprange">0 =inget (ångest, rädsla, obehag)<br />10=maximalt</div>\
                    <div class="rangeslider">\
                    <div class="numtext">\
                    <div class="textbar">10</div>\
                    <div class="textbar">9</div>\
                    <div class="textbar">8</div>\
                    <div class="textbar">7</div>\
                    <div class="textbar">6</div>\
                    <div class="textbar">5</div>\
                    <div class="textbar">4</div>\
                    <div class="textbar">3</div>\
                    <div class="textbar">2</div>\
                    <div class="textbar">1</div>\
                    <div class="textbar">0</div>\
                    </div>\
                    <input type="range" name="slider-fill" id="slider-fill" value="0" min="0" max="10" data-highlight="true" sliderorientation="verticalInverted" step="1" />\
                    <span id="slider-fill-span"></span>\
                    </div>\
                    <div class="popupbuttons">\
                    <a href="javascript:void(0);" class="next border-radius green-gradient">Nästa</a>\
                    </div>';
                        break;

                    case 2:
                        popHtml += '<div class="fixedtop">\
                    <span class="steps">2 av 3</span>\
                    <a href="javascript:void(0);"  data-onclick="Training.process.abort();" class="abort border-radius red-gradient">X</a>\
                    </div>\
                    <div class="popupTitle" id="divpopupTitle">title 2 </div>\
                    <p>RatingType1: content 2</p>\
                    <div class="popupbuttons">\
                    <a href="javascript:void(0);" data-onclick="Training.process.nextStep();" class="next border-radius green-gradient">Nästa</a>\
                    </div>\
                    </div>';
                        break;

                    case 3:
                        popHtml += '<div class="fixedtop">\
                    <span class="steps">3 av 3</span>\
                    <a href="javascript:void(0);" data-onclick="Training.process.abort();" class="abort border-radius red-gradient">X</a>\
                    </div>\
                    <div class="popupTitle" id="divpopupTitle">title3 </div>\
                    <p>RatingType1: content 3</p>\
                    <div class="popupbuttons">\
                    <a href="javascript:void(0);" data-onclick="Training.process.nextStep();" class="next border-radius green-gradient">Nästa</a>\
                    </div>\
                    </div>';
                        break;
                }
                popHtml += '</div>';
                return popHtml;

            },
            type2: function(stepNum) {
                var popHtml = '<div class="popupContent box-size-border border-radius">';
                switch (stepNum) {
                    case 1:
                        popHtml = '<div class="popupContent box-size-border border-radius"  style="height: 360px">\
                    <div class="fixedtop">\
                    <span class="steps">1 av 5</span>\
                    <a href="#Register" class="abort border-radius red-gradient">X</a>\
                    </div>\
                    <div class="popupTitle newTitle" id="divpopupTitle">1. Förnbered dig</div>\
                    <p>RatingType2: content 1</p>\
                    <div class="popupbuttons">\
                    <a href="javascript:void(0);" data-onclick="Training.process.nextStep();" class="next border-radius green-gradient">Fortsätt</a>\
                    </div>\
                    </div>';
                        break;

                    case 2:
                        popHtml += '<div class="fixedtop">\
                    <span class="steps">2 av 5</span>\
                    <a href="#Register" class="abort border-radius red-gradient">X</a>\
                    </div>\
                    <div class="popupTitle newTitle" id="divpopupTitle">2. Skatta start</div>\
                    <div class="range" id="divpopuprange">0 =inget (ångest, rädsla, obehag)<br />10=maximalt</div>\
                    <div class="rangeslider">\
                    <div class="numtext">\
                    <div class="textbar">10</div>\
                    <div class="textbar">9</div>\
                    <div class="textbar">8</div>\
                    <div class="textbar">7</div>\
                    <div class="textbar">6</div>\
                    <div class="textbar">5</div>\
                    <div class="textbar">4</div>\
                    <div class="textbar">3</div>\
                    <div class="textbar">2</div>\
                    <div class="textbar">1</div>\
                    <div class="textbar">0</div>\
                    </div>\
                    <input type="range" name="slider-fill" id="slider-fill" value="0" min="0" max="10" data-highlight="true" sliderorientation="verticalInverted" step="1" />\
                    <span id="slider-fill-span"></span>\
                    </div>\
                    <div class="popupbuttons">\
                    <a href="javascript:void(0);" class="next border-radius green-gradient">Börja träna</a>\
                    </div>';
                        break;

                    case 3:
                        popHtml = '<div class="popupContent box-size-border border-radius"  style="height: 360px">\
                    <div class="fixedtop">\
                    <span class="steps">3 av 5</span>\
                    <a href="#Register" class="abort border-radius red-gradient">X</a>\
                    </div>\
                    <div class="popupTitle newTitle" id="divpopupTitle">3. Träna nu</div>\
                    <div class="popUptext">\
                    <p class="popUpimg"><img style="width:200px;" src="http://192.168.1.117/bip/gif/training_animation_1.gif" alt="" /></p>\
                    <p>Troligtvis känns det rätt besvärligt just nu. Men tänk på att ångesten sjunker efter ett tag, som i ”löken”-modellen.Försök att träna på din uppgift till ångesten har sjunkit till hälften.\
                    </p>\
                    </div>\
                    <div class="popupbuttons">\
                    <a href="javascript:void(0);" class="next border-radius green-gradient">Avsluta träning</a>\
                    </div>\
                    </div>';
                        break;

                    case 4:
                        popHtml += '<div class="fixedtop">\
                    <span class="steps">4 av 5</span>\
                    <a href="#Register" class="abort border-radius red-gradient">X</a>\
                    </div>\
                    <div class="popupTitle newTitle" id="divpopupTitle">4. Skatta slut</div>\
                    <div class="range" id="divpopuprange">0 =inget (ångest, rädsla, obehag)<br />10=maximalt</div>\
                    <div class="rangeslider">\
                    <div class="numtext">\
                    <div class="textbar">10</div>\
                    <div class="textbar">9</div>\
                    <div class="textbar">8</div>\
                    <div class="textbar">7</div>\
                    <div class="textbar">6</div>\
                    <div class="textbar">5</div>\
                    <div class="textbar">4</div>\
                    <div class="textbar">3</div>\
                    <div class="textbar">2</div>\
                    <div class="textbar">1</div>\
                    <div class="textbar">0</div>\
                    </div>\
                    <input type="range" name="slider-fill" id="slider-fill" value="0" min="0" max="10" data-highlight="true" sliderorientation="verticalInverted" step="1" />\
                    <span id="slider-fill-span"></span>\
                    </div>\
                    <div class="popupbuttons">\
                    <a href="javascript:void(0);" class="next border-radius green-gradient">Godkänn tid</a>\
                    </div>';
                        break;

                    case 5:
                        popHtml = '<div class="popupContent box-size-border border-radius"  style="height: 360px">\
                    <div class="fixedtop">\
                    <span class="steps">5 av 5</span>\
                    <a href="#Register" class="abort border-radius red-gradient">X</a>\
                    </div>\
                    <div class="popupTitle newTitle" id="divpopupTitle">5. Godkänn tid</div>\
                    <div class="popUptext">\
                    <p>Troligtvis känns det rätt besvärligt just nu. Men tänk på att ångesten sjunker efter ett tag, som i ”löken”-modellen. Försök att träna på din uppgift till ångesten har sjunkit till hälften.\
                    </p>\
                    <p class="popUptid">\
                    <span>17 min</span>\
                    <br />\
                    <a href="#popUp7" class="border-radius"> Ändra tid</a>\
                    </p>\
                    </div>\
                    <div class="popupbuttons">\
                    <a href="javascript:void(0);" class="next border-radius green-gradient">Klart</a>\
                    </div>\
                    </div>';
                        break;
                }
                popHtml += '</div>';
                return popHtml;
            }

        },


        gotoStep: function(stepNum, reverse) {
            if ($('.checkicon').hasClass('deactivateTask'))
                return false;
            var ratingType = Training.process.type(),
                reverse = reverse || false;
            isValidStep = false;

            if (didUserLeaveRating(stepNum, ratingType)) {
                msgBox('Du måste göra en skattning innan du kan gå vidare');
                return false;
            }


            var newPage = 'TrainingZone_' + ratingType + '_' + stepNum;

            if (1 === ratingType) {
                if (stepNum >= 1 && stepNum <= 3) {
                    isValidStep = true;
                    //$('#trainingZoneContent').html(Training.process.html.type1(stepNum));
                    reverse ? backpage(newPage) : changepage(newPage, "none");

                }
            } else {
                if (stepNum >= 1 && stepNum <= 5) {
                    isValidStep = true;
                    //$('#trainingZoneContent').html(Training.process.html.type2(stepNum));
                    reverse ? backpage(newPage) : changepage(newPage, "none");
                }
            }

            if (isValidStep) {

                Training.process.currentStep = stepNum;


                if (stepNum == 3) {
                    //enableIScroll();
                    setTimeout(function() {
                        //console.log(Training)
                        if (typeof Training.process.scrolls['2_3'] == 'object') {

                            Training.process.scrolls['2_3'].refresh();


                        } else {

                            Training.process.scrolls['2_3'] = new iScroll('itextScroll');
                        }
                        //console.log('iscroll enabled');

                    }, 100);
                    //var myScroll = new iScroll($('#TrainingZone_2_3 .scrollwrapper')[0]);
                }

                //$('#trainingZoneContent [data-role=button]').button();
                //$('#trainingZoneContent  input[type="range"]').slider();//.slider("refresh");
            }
        },

        nextStep: function() {
            var cpageid = $($.mobile.activePage).attr("id"),
                currentStep = cpageid.split('_')[2];
            Training.process.currentStep = parseInt(currentStep, 10) + 1;
            Training.process.gotoStep(Training.process.currentStep);

        },

        previousStep: function(event) {
            //console.log('previous step fired');
            //Training.process.currentStep -= 1;

            var cpageid = $($.mobile.activePage).attr("id");
            var currentStep = cpageid.split('_')[2];

            Training.process.currentStep = parseInt(currentStep, 10) - 1;
            Training.process.gotoStep(Training.process.currentStep, true);

        },

        abortTidigare: function(event) {
            Training.editedTrainingID = 0;
            backpage('Tidigare', event, 'slide');
        },

        abort: function(event) {

            confirmBox('Är du säker på att du vill avbryta?', function(button) {

                if (button == 1) {
                    var returnpage;
                    if (Training.editedTrainingID > 0) {
                        returnpage = 'Tidigare';
                        Training.editedTrainingID = 0;
                    } else {
                        returnpage = 'Register';
                    }
                    $(window).trigger('BIP.abortstep', Training.process.currentStep);
                    backpage(returnpage, event);
                } else {
                    event.preventDefault();
                }
            });

        },

        /**
         * Function fired when the abort (x) is clicked on training steps
         * @param  {[type]} event   [description]
         * @param  {[type]} stepNum [description]
         * @return {[type]}         [description]
         */
        onAbortProcess: function(event, stepNum, trainingType) {
            trainingType = trainingType || Training.process.type();

            //if we are on the step animating gif and stopwatch
            if (stepNum === 3 && trainingType === 2) {
                //if the stopwatch is already running then stop it
                //also stop the render function of the stopwatch as it is giving some bugs on android phones

                if (Training.process.stopwatch.isRunning()) {
                    Training.process.stopwatch.stop();
                    //if show timing option is on for this task
                    var showTiming = Training.process.stopWatchToBeDisplayed();

                    if (showTiming) {

                        Training.process.stopwatch.stopDisplay(document.getElementById('showstopwatch'));
                        //hide it so that when showing on next time it won't display old reading before starting


                    }
                    $('#showstopwatch').addClass('bip_hidden');

                }

            }

        },
        setTrainingSeconds: function(sec) {
            Training.process.sec = sec;
            return sec;
        },
        getTrainingTime: function() {

            var totalSeconds, sec, min;

            //if (Training.editedTrainingID === 0) { //new training

            //totalSeconds = Math.floor(Training.process.stopwatch.read() / 1000);

            //} else { //old trainings


            //}

            totalSeconds = parseInt(Training.process.sec, 10);

            // if (totalSeconds < 60) { //less than a minute
            //     sec = totalSeconds;
            //     min = 0;
            // } else {
            //     sec = 0;
            //     min = Math.ceil(totalSeconds / 60);
            // }

            //new logic

            if (totalSeconds < 60) { //less than a minute
                sec = 0;
                min = 1;
            } else {

                sec = 0;
                min = Math.ceil(totalSeconds / 60);

                if ((totalSeconds % 60) >= 30) {
                    min += 1;
                }

            }

            var aMin = Math.floor(totalSeconds / 60);
            var aSec = totalSeconds % 60;
            //
            //
            //return total seconds along with the calculated seconds and minutes
            //
            var retVar = {
                total: totalSeconds,
                seconds: sec,
                minutes: min,
                actualMinutes: aMin,
                actualSeconds: aSec
            };

            console.warn(JSON.stringify(retVar));

            return retVar;


        },
        /**
         * [stopWatchToBeDisplayed read the setting from admin whether the stopwatch need to be displayed or not]
         * @return {[type]} [boolean]
         */
        stopWatchToBeDisplayed: function() {
            var showTiming = false;
            
            if (Training.adminSettings && Training.adminSettings.training) {
                var TrainingTest = JSON.parse(Training.adminSettings.training);
                if(TrainingTest[3] && TrainingTest[3].timing){
                    showTiming = parseInt(TrainingTest[3].timing, 10);
                }
            }
            return showTiming;
        },
        /**
         * [startClock fired when the the clock need to be started]
         * @return {[type]} [description]
         */
        startClock: function() {
            if (Training.editedTrainingID === 0) { //new training

                Training.process.sec = 0;
                if (Training.process.stopwatch.isRunning()) {
                    Training.process.stopwatch.restart();
                } else {
                    Training.process.stopwatch.start();
                }
                var showTiming = Training.process.stopWatchToBeDisplayed();

                if (showTiming) {
                    $('#showstopwatch').text('00:00');
                    //start the clock and then display
                    Training.process.stopwatch.display(document.getElementById('showstopwatch'), 1000, function(ms) {
                        if (ms <= 0) {
                            return "00:00";
                        }

                        //milliseconds = (ms % 1000).toString(),
                        var seconds = Math.floor((ms / 1000) % 60).toString(),
                            //minutes = Math.floor((ms / (60 * 1000)) % 60).toString();
                            minutes = Math.floor((ms / (60 * 1000))).toString();

                        // if (milliseconds.length === 1) {
                        //     milliseconds = '00' + milliseconds;
                        // } else if (milliseconds.length === 2) {
                        //     milliseconds = '0' + milliseconds;
                        // }
                        if (seconds.length === 1) {
                            seconds = '0' + seconds;
                        }
                        if (minutes.length === 1) {
                            minutes = '0' + minutes;
                        }
                        return minutes + ":" + seconds; //+ "." + milliseconds;
                        //return parseInt(ms / 1000);
                    });
                    //setTimeout(function() {
                    $('#showstopwatch').removeClass('bip_hidden');
                    //}, 100);


                } else {

                    $('#showstopwatch').addClass('bip_hidden');

                }



                return Training.process.stopclock;
                //return Training.process.clock;

            } else {
                //edit(review) old  training do not start the timer
                $('#showstopwatch').addClass('bip_hidden');
                var trainings = $.grep(Training.TrainingLists, function(e) {
                    return parseInt(e.trainingId) === parseInt(Training.editedTrainingID);
                });

                if (trainings.length > 0) {
                    Training.process.sec = parseInt(trainings[0].training_duration, 10);
                    Training.process.completedTime = trainings[0].trainingdatetime;
                }


            }
        },
        /**
         * [stopClock tasks that need to be done when the stopclock has to be stopped]
         * @return {[type]} [description]
         */
        stopClock: function() {

            if (Training.editedTrainingID === 0) {
                //stop timer for new training
                //msgBox('new training setting time');
                Training.process.stopwatch.stop();

                var showTiming = Training.process.stopWatchToBeDisplayed();

                if (showTiming) {

                    Training.process.stopwatch.stopDisplay(document.getElementById('showstopwatch'));
                    setTimeout(function() {

                        $('#showstopwatch').text('00:00');
                        //hide it so that when showing on next time it won't display old reading before starting
                        $('#showstopwatch').addClass('bip_hidden');
                    })


                }

                //window.clearInterval(Training.process.clock);

                //only for  new training calculate the completed time
                //for old training this data has been set in startClocl method since old training data is accessed there
              /*  var d = new Date,
                    dformat = [d.getFullYear(), d.getMonth() + 1, d.getDate()].join('-') +
                    ' ' + [d.getHours(),
                        d.getMinutes(), d.getSeconds()
                    ].join(':'); */


                //Training.process.completedTime = dformat;
                //Get server time instead
                callWebServiceLiveSilently("getservertime", "{}", function(response) {
                    var d = response.data;
                    Training.process.completedTime = d.datetime;
                },"",false);

                //var ticktock = Training.process.stopwatch.read();

                Training.process.setTrainingSeconds(Math.floor(Training.process.stopwatch.read() / 1000));


                //save total seconds in Traninig.process.sec
                //Training.process.sec = Math.floor(ticktock / 1000);
                //
                //var totalSeconds = Training.getTrainingSeconds();
                //var ms = (ticktock % 1000),
                //    sec = Math.floor((ticktock / 1000) % 60),
                //    min = Math.floor((ticktock / (60 * 1000)) % 60);
            } else {
                //old training data already is in variable Training.process.sec
                //var sec = Math.floor(Training.process.sec % 60),
                //    min = Math.floor(Training.process.sec / 60);
            }

            var totalSeconds = Training.process.getTrainingTime().totalSeconds,
                sec = Training.process.getTrainingTime().actualSeconds,
                min = Training.process.getTrainingTime().actualMinutes;



            //if time is greater than 59 seconds always display in round off minutes and if not never show minutes
            // if (min > 0) {

            //     min = Math.ceil(Training.process.sec / 60); //round of to next greater minute
            //     sec = 0;
            //     Training.process.sec = min * 60; //update the entire timings to snipto the round off minute

            // }


            //var hour = Math.ceil(min/60);
            var displayTime = '';

            var grammer = function(value, singular, plural) {
                return parseInt(value, 10) > 1 ? plural : singular;
            }

            //if( hour > 0) {
            //    displayTime += '' + hour + ' '+  grammer(hour,'hour','hours') + ' ';
            //}else
            if (min > 0) {
                displayTime += '' + min + ' ' + grammer(min, 'Minuter', 'Minuter') + ' ';
            } else {
                displayTime += '' + min + ' ' + grammer(min, 'Minuter', 'Minuter') + ' ';
                displayTime += '' + sec + ' ' + grammer(sec, 'Sekunder', 'Sekunder') + ' ';

            }

            $('#TrainingZone_2_5 .popUptid span').text(displayTime);

            //show conditional text according to the time
            var popupData_2_5 = $.jStorage.get('userdetails').training[5];
            var conditional_text = ';'
            if (popupData_2_5 && popupData_2_5.hasOwnProperty('compare') && popupData_2_5.hasOwnProperty('conditional_text')) {

                if (min < parseInt(popupData_2_5.compare.x, 10)) {

                    conditional_text = popupData_2_5.conditional_text[1];

                } else if (min < parseInt(popupData_2_5.compare.y, 10)) {
                    conditional_text = popupData_2_5.conditional_text[2];
                } else {

                    conditional_text = popupData_2_5.conditional_text[3];
                }

                $('#trainingCndnlContent').html(conditional_text);


            }

            $('#trainingTimeSpent').text(displayTime);
            $("#dintid").removeClass("bip_hidden");

            updateUserTrainingTime();



            return Training.process.stopwatch;
            ////console.log(' ' + Training.process.sec + 'sec')
            //return Training.process.sec;
        },

        editTimeSpent: function() {

            var totalSeconds = Training.process.getTrainingTime().total,
                sec = Training.process.getTrainingTime().seconds,
                min = Training.process.getTrainingTime().minutes;

            //var sec = Math.floor(Training.process.sec % 60);

            //var min = Math.floor(Training.process.sec / 60);

            //if time is greater than 59 seconds always display in round off minutes and if not never show minutes
            //if (min > 0) {

            //    min = Math.ceil(Training.process.sec / 60); //round of to next greater minute
            //}





            $('#training_span_minutes').val(min);
            $('#training_span_seconds').val(sec);

            /*
            if (min > 0) {
                //only show minutes edit box
                $('#editTimeSpent .second_edit').hide();
                $('#editTimeSpent .minute_edit').show();

            } else {
                //only show second edit box
                $('#editTimeSpent .second_edit').show();
                $('#editTimeSpent .minute_edit').hide();
            }
            */

            //$('#editTimeSpent .minute_edit').show();
            //$('#editTimeSpent .minute_edit').removeClass('bip_hidden');

            /*  if (min > 0) {
                //show minute only
                $('#editTimeSpent .second_edit').hide();
                $('#editTimeSpent .minute_edit').show();
                //#editTimeSpent .second_edit,
            } else {
                //show both minute and second
                $('#editTimeSpent .second_edit').show();
                $('#editTimeSpent .minute_edit').show();
            }*/

            $('#defaultTimeSpent').hide();
            $('#editTimeSpent').show(function() {
                showDropdown($('#training_span_minutes')[0]);
            });

            $('#trainingTimeSpentWrapper').on('change', 'select', function() {
                //console.log('input value is changing');
                var newSec = parseInt($('#training_span_seconds').val(), 10) || 0;

                var newMin = parseInt($('#training_span_minutes').val(), 10) || 0;
                if (newMin === 0) {
                    //$('#training_span_seconds').fadeIn();
                    $('#editTimeSpent .second_edit').show();

                } else {
                    $('#training_span_seconds').val(0);
                    $('#editTimeSpent .second_edit').hide();
                    //$('#training_span_seconds').fadeOut();

                    //     //$('#training_span_seconds').removeAttr('disabled');
                    //     $('#training_span_seconds').val(0);
                    //     $('#editTimeSpent .second_edit .ui-btn-text span').text(0);
                    //     newSec = 0;
                }
                var totalSeconds = newMin * 60 + newSec;
                Training.process.sec = totalSeconds;
                //console.log(totalSeconds);

            });

            $('#trainingTimeSpentWrapper select').change();

        }



        /**
            Training type 1 or 2
            if 1
                it is a single step rating (short process)
            if 2
                it is a longer training with two step rating

            */




    },
     reviewTraining: function(trainingId) {
        var valueReviewChanged = function(event) {
            //console.log(event);
            //console.log(event.data);


            //event.data.id.find('.bip_edit').removeClass('bip_hidden');
            //event.data.id.find('select').addClass('bip_hidden');
            var newValue = '';


            if (event.data.type.time) {
                //if of time edit type update accordingly
                var isMinute = $(this).hasClass('min'),
                    isSecond = !isMinute,
                    minutes = 0,
                    seconds = 0;
                if (isMinute) {
                    minutes = '' + $(this).val();
                    if (minutes > 0) {
                        //if minute is greater than 0 hide seconds
                        event.data.id.find('select.sec').val(0).addClass('bip_hidden'); //hide();
                        seconds = '0';

                    } else {
                        event.data.id.find('select.sec').removeClass('bip_hidden');
                        seconds = '' + event.data.id.find('select.sec').val();
                    }

                } else {
                    seconds = '' + $(this).val();
                    minutes = '' + event.data.id.find('select.min').val();
                }

                minutes = minutes.length === 1 ? '0' + minutes : minutes;
                seconds = seconds.length === 1 ? '0' + seconds : seconds;

                newValue = minutes + ':' + seconds;

            } else if (event.data.type.rating) {
                newValue = $(this).val();

            }

            event.data.id.find('.bip_value').text(newValue);




            ////console.log($id);
            //alert($(this).val());
        };

        var valueReviewFocused = function() {
            //console.log('focused');
            //console.log('value is ' + $(this).val());
            $(this).data('initialValue', $(this).val());
            //console.log('old data is' + $(this).data('initialValue'));
        };

        var valueReviewBlurred = function() {
            //console.log('blurred');

            var oldData = $(this).data('initialValue');
            if (oldData == $(this).val()) {
                $(this).change();
                //console.log('jabarjasti change triggered');
            }
            //console.log(oldData);
            //console.log($(this).val());

        };

        var onBipReviewEdit = function() {
            var id = $(this).parents('.bip_review_item').attr('id');

            //what type of review edit?
            var isOfRatingType = new RegExp("review_rating_").test(id);
            var isOfTimeEditType = new RegExp("review_time_taken").test(id);


            //in case of time edit either show second edit or minute edit not both

            var $id = $('#' + id);

            var editType = {
                rating: false,
                time: false
            };
            if (isOfRatingType) {
                var $editBox = $id.find('select');
                editType.rating = true;
            }

            if (isOfTimeEditType) {
                //read the old time taken value for the training
                //and accordingly set the correct selectbox for edit
                if ($id.find('select.min').val() > 0) {
                    var $editBox = $id.find('select.min'); //select.showme
                } else {
                    var $editBox = $id.find('select'); //select.showme
                }
                editType.time = true;
            }



            $(this).addClass('bip_hidden');
            $editBox.removeClass('bip_hidden');
            $editBox.trigger('click');

            showDropdown($editBox[0]); //auto trigger selectbox edit

            //$editBox.off('change', valueReviewChanged);
            //$editBox.on('change', valueReviewChanged);

            // var sVal = '';
            // $editBox.focus(function() {
            //     alert('focused');
            //     sVal = $(this).val();
            // }).blur(function() {
            //     alert('blurred');
            //     if (sVal == $(this).val()) {
            //         $(this).change();
            //     }
            // });

            // $editBox.off('focus', valueReviewFocused);
            $editBox.off('focus').on('focus', valueReviewFocused);
            // $editBox.off('blur', valueReviewBlurred);
            $editBox.off('blur').on('blur', valueReviewBlurred);





            $editBox.off('change').on('change', {
                id: $id,
                type: editType
            }, valueReviewChanged);
        };

        var onBipReviewEditRating = function(editData) {
            var id = $(this).parents('.bip_review_item').attr('id');


            var $id = $('#' + id);
            var $editBox = $id.find('select');
            var value = $editBox.val();

            $editBox.off('change').on('change', {
                id: $id,
                type: {
                    rating: false,
                    time: false
                }
            }, valueReviewChanged);

            $('#slider-fill_reveiw_rating').attr('data-updateid', id);

            changepage('tidigare_review_rating', 'none');


            $("#slider-fill_reveiw_rating").hide();

            $(".rangeslider_reveiw_rating div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[value] + "%");


            $('#slider-fill_reveiw_rating').unbind("change");
            //var slide_bottom,slide2_2_bottom;
            $('#slider-fill_reveiw_rating').change(function() {
                var newValue = $(this).val();
                var id = $(this).attr('data-updateid');
                var $id = $('#' + id);
                var $editBox = $id.find('select');
                $editBox.val(newValue);
                $id.find('.bip_value').text(newValue);




                $(".rangeslider_reveiw_rating div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style", "display:block");
                $(".rangeslider_reveiw_rating div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[newValue] + "%");
                $("#slider-fill-span_reveiw_rating").attr("style", "display:block").html(newValue).css('bottom', sliderpercentage[newValue] - 20 + "%");

            });

            $('#slider-fill_reveiw_rating').val(value).css('bottom', sliderFillpercentage[value] + '%');



            $('#slider-fill_reveiw_rating').change();

            //console.log(value);

            //Related to issue posted on Teamwork https://websearchpro.teamworkpm.net/tasks/1045394

            trainingInfo = $.jStorage.get('userdetails');
            currentStep = 2;
            if (id == "review_rating_2_4")
                currentStep = 4;


            var rangeSelector = $('#tidigare_review_rating').find('#divpopuprange');
            var buttonSelector = $('#tidigare_review_rating').find('.popupbuttons');



            var parseTrainingInfo = JSON.parse(trainingInfo.training);
            if (parseTrainingInfo.type == 2) {
               /* $('#tidigare_review_rating').find('.popupTitle').text(trainingInfo.training[currentStep].headline);
                rangeSelector.find('.range_label_ten').text('10 = ' + trainingInfo.training[currentStep].ten);
                rangeSelector.find('.range_label_zero').text('0 = ' + trainingInfo.training[currentStep].zero);
                buttonSelector.find('.next').text(trainingInfo.training[currentStep].button);*/
                $('#tidigare_review_rating').find('.popupTitle').text(parseTrainingInfo[currentStep].headline);
                rangeSelector.find('.range_label_ten').text('10 = ' + parseTrainingInfo[currentStep].ten);
                rangeSelector.find('.range_label_zero').text('0 = ' + parseTrainingInfo[currentStep].zero);
                buttonSelector.find('.next').text(parseTrainingInfo[currentStep].button);
            } else {
                
              /*  $('#tidigare_review_rating').find('.popupTitle').text(trainingInfo.training.anxiety);
                rangeSelector.find('.range_label_ten').text('10 = ' + trainingInfo.training.ten);
                rangeSelector.find('.range_label_zero').text('0 = ' + trainingInfo.training.zero);
                buttonSelector.find('.next').text(trainingInfo.training.txt_button);*/
                 $('#tidigare_review_rating').find('.popupTitle').text(parseTrainingInfo.anxiety);
                rangeSelector.find('.range_label_ten').text('10 = ' + parseTrainingInfo.ten);
                rangeSelector.find('.range_label_zero').text('0 = ' + parseTrainingInfo.zero);
                buttonSelector.find('.next').text(parseTrainingInfo.txt_button);
            }

        };

        $('#editminute').addClass('bip_hidden');
        $('.bip_edit ').removeClass('bip_hidden');
        // $('#tidigare_review').off('click').on('click', '.bip_edit', onBipReviewEdit);

        Training.editedTrainingID = trainingId;
        Training.isSaveonProgess = false;
        var type = Training.process.type();

        var trainings = $.grep(Training.TrainingLists, function(e) {
            return e.app_training_id == Training.editedTrainingID;
        });

        if (trainings.length === 0) {
            showalert('Sorry,this trainig do not exist anymore!');
            return false;
        }


        var training = trainings[0];
        var editData = {};

        $('.bip_review_item select').addClass('bip_hidden');
        $('.bip_review_item .bip_edit').removeClass('bip_hidden');

        $('#tidigare_review_rating').find('.trainingZoneContent').off('click').on('click', '.abort', Training.process.abortTidigare);


        if (type === 1) { //single rating
            //populate data
            editData.rating_1_1 = training.estimatedvalue;
            //update dom
            $('#review_rating_1_1 select').val(training.estimatedvalue);
            $('#review_rating_1_1 .bip_value').text(training.estimatedvalue);
            $('#review_date_1_2 input#tidigare_date_1_2').attr("value", training.trainingdatetime);
            //console.log(training.trainingdatetime)
                //$("#tidigare_date_1_2").change();

            //$('#review_datetime').val('trainingdatetime');
            //$('#review_datetime .bip_value').val(training.trainingdatetime);

            //show / hide
            $('#review_rating_2_2,#review_rating_2_4,#review_time_taken').addClass('bip_hidden');
            $('#review_rating_1_1').removeClass('bip_hidden'); //#review_comment,//#review_datetime
            $('#review_date_1_2').removeClass('bip_hidden');

        } else if (type == 2) { //double rating
            //populate data
            editData.training_duration = parseInt(training.trainingDuration, 10); //parseInt(training.trainingduration, 10);
            editData.rating_2_2 = training.estimatedValue;//training.estimatedvalue;
            editData.rating_2_4 = training.estimatedValueEnd;//training.estimatedvalue_end;

            console.clear();
            console.warn("Training = "+JSON.stringify(training));
           
            //update dom
            //$('#review_rating_2_2 select').val(training.estimatedvalue);
            $('#review_rating_2_2 select').val(training.estimatedValue);
            //$('#review_rating_2_2 .bip_value').text(training.estimatedvalue);
            $('#review_rating_2_2 .bip_value').text(training.estimatedValue);

          //  $('#review_rating_2_4 select').val(training.estimatedvalue_end);
            $('#review_rating_2_4 select').val(training.estimatedValueEnd);
          //  $('#review_rating_2_4 .bip_value').text(training.estimatedvalue_end);
            $('#review_rating_2_4 .bip_value').text(training.estimatedValueEnd);


            $('#editminute').removeClass('bip_hidden');
            //set time
           
            Training.process.setTrainingSeconds(editData.training_duration);
            var totalSeconds = Training.process.getTrainingTime().total,
                sec = Training.process.getTrainingTime().actualSeconds || "00",
                min = Training.process.getTrainingTime().actualMinutes || "00";
            // min = parseInt(totalSeconds / 60, 10),
            //sec = totalSeconds % 60;

            // $('#review_time_taken select.sec').val(sec);
            //$('#review_time_taken select.min').val(min);

            $('#review_time_taken select').addClass('bip_hidden').removeClass('showme'); //add this class to each select box

            $('#review_time_taken .bip_value').text(('0' + min + '').slice(-2) + ':' + ('0' + sec + '').slice(-2));
            $('#tidigareTimeEdit').val(('0' + min).slice(-2) + ':' + ('0' + sec).slice(-2));
          
           
            /*
            if (min > 0) {
                $('#review_time_taken select.min').addClass('showme');
                //$('#review_time_taken .bip_value').text('' + min + 'Minuter');

            } else {
                $('#review_time_taken select.sec').addClass('showme');
                //$('#review_time_taken .bip_value').text('' + sec + 'Sekunder');
            }
            */




            //show / hide
            $('#review_rating_2_2,#review_rating_2_4,#review_time_taken').removeClass('bip_hidden');
            $('#review_rating_1_1').addClass('bip_hidden');
            $('#review_date_1_2').addClass('bip_hidden');
        }

        //common for both
        var oldDateTIme = training.trainingdatetime;

        $('#training_review_datetime').text(oldDateTIme.replace(' ', ', '));
        $('#training_review_datetime').attr('data-value', oldDateTIme);

        $('#review_comment textarea').val(training.comment);

        //bind the edit button for action
        //$('#tidigare_review').off('click').on('click', '.bip_edit', editData, onBipReviewEdit);
        $('#tidigare_review').on('click', '.bip_edit_rating', editData, onBipReviewEditRating);


        changepage('tidigare_review');




    },
    saveReviewedTraining: function() {
        var trainingId=Training.editedTrainingID;
        var taskid = Training.editedTaskid;
         var submit_object={};
        if (Training.isSaveonProgess === false) {
            Training.isSaveonProgess = true;
            var userdetails = $.jStorage.get('userdetails');
            submit_object = {
                userid: userdetails.user_id,
                tokenkey: userdetails.tokenkey,
                deviceId: DeviceID,
                taskid: taskid,
                trainingId: Training.editedTrainingID,
                type: Training.process.type(),
                trainingdatetime: $('#training_review_datetime').attr('data-value')

            };

            if (submit_object.type === 2) {

                var times = $('#review_time_taken input').val().split(':');

                totSeconds = parseInt(times[0], 10) * 60 + parseInt(times[1], 10);

                /*var minute = parseInt($('#review_time_taken input').val(), 10) || 0,
                sec = parseInt($('#review_time_taken select.sec').val(), 10) || 0,
                totSeconds = minute * 60 + sec;*/


            }

            switch (submit_object.type) {
                case 1:
                    submit_object.estimatedvalue = $('#review_rating_1_1 select').val();
                    submit_object.comment = checkinput($("#review_comment textarea").val());
                    submit_object.trainingdatetime = $("#tidigare_date_1_2").val();
                    submit_object.estimatedvalue_end="";
                    submit_object.training_duration="";
                    break;
                case 2:
                    submit_object.estimatedvalue = $('#review_rating_2_2 select').val();
                    submit_object.estimatedvalue_end = $('#review_rating_2_4 select').val();

                    submit_object.training_duration = totSeconds;

                    break;
            }

            var json = JSON.stringify(submit_object);

            //console.log(json);


            $(Training.TaskLists).each(function(index) {
                if (parseInt(this.taskid, 10) === taskid) {
                    $self=this;
                    $($self.training).each(function(){
                         if (this.app_training_id == trainingId) {
                                this.edited=1;
                                this.estimatedvalue=submit_object.estimatedvalue;
                                this.estimatedvalue_end=submit_object.estimatedvalue_end;
                                this.training_duration=submit_object.training_duration;
                                this.trainingdatetime=submit_object.trainingdatetime                    ;
                        }

                    })

                }
            });

            //active_tasks.data.task=Training.TaskLists;
           // updateDB('active_tasks',active_tasks,function(){  console.log("Success updating activetask"); }); 

            var toadd={
                        where:{
                            'app_training_id': trainingId,
                        },
                        fields:{
                            'trainingDateTime':submit_object.trainingdatetime,
                            'estimatedValue':submit_object.estimatedvalue,
                            'trainingDuration':submit_object.training_duration,
                            'edited':1,
                            'estimatedValueEnd':submit_object.estimatedvalue_end
                         }
                    };
            callWebService('saveReviewedTraining', toadd, function(response) {
                console.log("Training saved in local database");
            });
            //callWebServiceLive('saveTraining', json, function(response) {
                Training.isSaveonProgess = false;
               // if (response.status === "ok") {
                    //update do not give the feedback message
                    //msgBox(response.data.message);
                    //
                    //Training.showTraining(Training.editedTaskid);
                    changepage('TrainingList');
                    // enableIScroll();
                    //Training.buildTrainingList(response.old_trainings);
                    if (Training.editedTrainingID === 0) {
                        $(Training.TaskLists).each(function(index) {
                            if (parseInt(this.taskid, 10) === taskid) {
                                this.practice += 1;
                                traningSlot = Training.getpracticeslotnumber(this.practice);
                                var progressHtml = new Array();
                                for (var i = 1; i <= traningSlot; i++) {
                                    if (i <= this.practice) {
                                        progressHtml.push('<span class="section green">&nbsp;</span>');
                                    } else {
                                        progressHtml.push('<span class="section">&nbsp;</span>');
                                    }
                                }
                                $("#progressbar").html(progressHtml.join(''));
                                $("#lstTraining li[data-taskid=" + taskid + "] .sectioned.box-size-border").html(progressHtml.join(''));
                                return false;
                            }
                        });
                           //active_tasks.data.task=Training.TaskLists;
                          //updateDB('active_tasks',active_tasks,function(){  console.log("Success updating activetask"); });
                    }   

                // } else {
                //     msgBox(MESSAGE.NO_INTERNET);
                // }
            // });
        }
    }
};



// $('#review_rating_1_1 select').on('focus', valueReviewFocused);
// $('#review_rating_2_2 select').on('focus', valueReviewFocused);
// $('#review_rating_2_4 select').on('focus', valueReviewFocused);

// $('#review_rating_1_1 select').on('blur', valueReviewBlurred);
// $('#review_rating_2_2 select').on('blur', valueReviewBlurred);
// $('#review_rating_2_4 select').on('blur', valueReviewBlurred);


//$('#tidigare_review').off('click', '.bip_edit', onBipReviewEdit);
