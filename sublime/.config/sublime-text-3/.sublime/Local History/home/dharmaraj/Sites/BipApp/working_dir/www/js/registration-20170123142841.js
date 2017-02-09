var currentRegStepID = "";
var Registration = {
    modulesSaved:0,
    oldSelectedOptions: [],
    testVar:"",
    renderRegistrationNav: function(hasRegistration) {
        if (hasRegistration == "true") {
            var $html = "<li data-role='' class='registration-nav-main'><a href='javascript:void(0)' onclick='Registration.home();'>Registration</a></li>";
            $('#lstTraining3').html($html);
            $('#lstTraining3').listview('refresh');
            refreshScroll('TrainingList');
        } else {
            var $html = "<li data-role=''>No Registrations</li>";
            $('#lstTraining3').html($html);
            $('#lstTraining3').listview('refresh'); 
            refreshScroll('TrainingList');
        }
    },

   

    home: function() {
          //download files
        if(navigator.onLine==true){
            offlinehelper.downloadAudioFiles();
        }

        BipAppVersion2.ShowHideModules();
       

        Registration.elementClick();
        changepage("RegistrationTask");
        
        $("#RegistrationTask").find(".played_time").remove(); //.played_time element was added by sujendra, but we don't need this in registration.
        $(".txt-custom-answers").bind("focus",function(){
          //  $(".reg-navigation-holder").addClass("hide");
            $('#popupAddAnswer').popup("reposition",{ y: (window.innerHeight-$('#popupAddAnswer').height())/2});
            $('#popupAddAnswerCat').popup("reposition",{ y: (window.innerHeight-$('#popupAddAnswerCat').height())/2});
        }).bind("blur",function(){
          /*  $('#popupAddAnswer').popup("reposition",{ y: (window.innerHeight-$('#popupAddAnswer').height())/2});
            $('#popupAddAnswerCat').popup("reposition",{ y: (window.innerHeight-$('#popupAddAnswerCat').height())/2});*/
           /* var b = setTimeout(function(){
                $(".reg-navigation-holder").removeClass("hide");
            },700);*/
        });

        /*
        $(".ui-collapsible-set").find('.ui-collapsible').bind('expand', function () {
            alert('Expanded');
        }).bind('collapse', function () {
            alert('Collapsed');
        });
        */

       

         


        $('#popupAddAnswerCat').popup({
                beforeposition: function() {
                    $(this).css({
                        width: window.innerWidth - 20
                    });
                },
                positionTo:"window",
                theme: "d",
                transition: "pop",
                shadow: false,
                tolerance: "2,2"
        });

        $('#popupAddAnswer').popup({
            beforeposition: function() {
                $(this).css({
                    width: window.innerWidth - 20
                });
            }, 
            positionTo: "window",
            theme: "d",
            transition: "pop",
            shadow: false,
            tolerance: "2,2"
        });


       
    },

    fetchRegistrations: function(show) {
        var show = typeof show==="undefined" ? "" : show;
       
       if(show=="old"){
            if($(".earlier-registration").hasClass("desaturate")){
                return false; // we don't want to render empty page if there are no old registrations.
            }
            $mainPage = "RegistrationTask_List_Old";
        }else{
            $mainPage = "RegistrationTask_List";
        }


        var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","show":"'+show+'"}';
        callWebService('fetchRegistrations', json, function(response) {
           
            if (response.status == "ok") {
                if (typeof response.data!="undefined" && response.data != "No Registrations") {
                    
                    $html = "";
                    
                   

                    $(response.data).each(function() {
                        $answered_date = "";
                        $class="";
                        $assignment_id = 0;
                        $app_assignment_id = 0;
                        if(show=="old"){
                            $answered_date = "<div class='date-answered'>"+this.registration_name+"</div>";
                            $class="old-registration-list";
                            $assignment_id = this.assignment_id;
                            $app_assignment_id = this.app_assignment_id;
                             $html += "<li class=''>" +
                                "<a href='javascript:void(0);' data-assignmentid='"+$assignment_id+"'data-appassignmentid='"+$app_assignment_id+"' data-showwhat='"+show+"' onclick='Registration.fetchRegistrationSteps($(this))' data-flowtype='" + this.flow_type + "' data-regid='" + this.registration_id + "' class='"+$class+"'>" + this.formatted_answer_date + $answered_date +"</a>" + 
                                "</li>";
                        }else{
                             $html += "<li class=''>" +
                                "<a href='javascript:void(0);' data-assignmentid='"+$assignment_id+"'data-appassignmentid='"+$app_assignment_id+"' data-showwhat='"+show+"' onclick='Registration.fetchRegistrationSteps($(this))' data-flowtype='" + this.flow_type + "' data-regid='" + this.registration_id + "' class='"+$class+"'>" + this.registration_name + $answered_date +"</a>" + 
                                "</li>";
                         }


                         if(response.old_registrations===0){ //if there is no old registration disable the link
                                $("#RegistrationTask_List").find(".earlier-registration").addClass("desaturate");
                         }  
                       /* if(show=="old"){
                            $answered_date = "<div class='date-answered'>"+this.formatted_answer_date+"</div>";
                            $class="old-registration-list";
                            $assignment_id = this.assignment_id;
                            $app_assignment_id = this.app_assignment_id;
                        }
                        if (this.flow_type == 2) {

                            $html += "<li class=''>" +
                                "<a href='javascript:void(0);' data-assignmentid='"+$assignment_id+"' data-showwhat='"+show+"' onclick='Registration.fetchRegistrationDetails($(this))' data-flowtype='" + this.flow_type + "' data-regid='" + this.registration_id + "' class='"+$class+"'>" + this.registration_name + $answered_date + "</a>" + 
                                "</li>";
                        } else {
                            $html += "<li class=''>" +
                                "<a href='javascript:void(0);' data-assignmentid='"+$assignment_id+"'data-appassignmentid='"+$app_assignment_id+"' data-showwhat='"+show+"' onclick='Registration.fetchRegistrationSteps($(this))' data-flowtype='" + this.flow_type + "' data-regid='" + this.registration_id + "' class='"+$class+"'>" + this.registration_name + $answered_date +"</a>" + 
                                "</li>";
                        }*/
                    });
                    $("#"+$mainPage).find(".list_registrations").html($html);
                    $("#"+$mainPage).find(".list_registrations").listview('refresh');

                    $(".regtaskicon").trigger("blur"); //Used to remove focus from Circular registration button.
                   
                    changepage($mainPage);
                    enableIScroll();
                    refreshScroll($mainPage);
                    scrolls[$mainPage].refresh();
                  
                }else{
                    return false;
                }
            } else {
                msgBox(MESSAGE.NO_INTERNET);
            }
        });
    },

    //Registration detail is for flowtype 2 and not used in system. Not removed for future reference
    fetchRegistrationDetails: function(obj) {
        var userdetails = $.jStorage.get('userdetails');
        var FlowType = obj.attr("data-flowtype");
        var RegID = obj.attr("data-regid");
        var show = obj.attr("data-showwhat");
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","flow_type":"' + FlowType + '","registration_id":"' + RegID + '","show":"'+show+'"}';
        callWebService(' ', json, function(response) {
            if (response.status == "ok") {
                if (response.data != "No Registration Details" && response.data.length>0) {
                    if (FlowType == 2) //Registration with multiple flows
                    {
                        $pageTitle = "";
                        $selector = $(".registration-flows");

                        $html = "";
                        $(response.data).each(function() {
                            $html += "<li class=''>" +
                                "<a href='javascript:void(0);' onclick='Registration.fetchRegistrationSteps($(this))' data-flowtype='" + FlowType + "' data-regid='" + this.registration_id + "' data-flowid='" + this.flow_id + "' data-flowpageid='" + this.flow_page_id + "' class=''>" + this.flow_name + "</a>" +
                                "</li>";
                            $pageTitle = this.flow_page_title;
                        });

                        $selector.find("h1").html($pageTitle);

                        $selector.find("ul").html($html);
                        $selector.find("ul").listview("refresh");

                        //$(".registration-flows");
                        changepage("RegistrationTask_Flows");
                    } else {

                    }
                }
            } else {
                msgBox(MESSAGE.NO_INTERNET);
            }
        });
    },

    fetchRegistrationSteps: function(obj) {
        var userdetails = $.jStorage.get('userdetails');
        var showWhat = obj.attr("data-showwhat");
        var FlowType = obj.attr("data-flowtype");
        var RegID = obj.attr("data-regid");
        var FlowID = obj.attr("data-flowid");
        var assignment_id = obj.attr("data-assignmentid");
        var app_assignment_id = obj.attr("data-appassignmentid");
        $("#reg_assignment_id").val(assignment_id);
        $("#app_reg_assignment_id").val(app_assignment_id);

        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","flow_type":"' + FlowType + '","registration_id":"' + RegID + '","flow_id":"' + FlowID + '","assignment_id":"'+assignment_id+'","app_assignment_id":"'+app_assignment_id+'"}';
        callWebService('fetchRegistrationSteps', json, function(response) {

            if (response.status == "ok") {
                if (response.data != "No Registration Steps") {
                    //registration-steps-holder
                    $("#registration_id").val(RegID);
                    if (FlowID > 0) $("#reg_flow_id").val(FlowID);
                    var datetime = false;
                    var stepMinute = 3;
                    $html = "";

                    $old_assignments = response.data.old_assignment!=undefined ? response.data.old_assignment : null;

                    
                  //  $old_assignments = response.data.old_assignment;
                   // $checkAnswer = new Array();
                   Registration.testVar = response.data.old_assignments;

                    if($old_assignments!=null && typeof $old_assignments.details!="undefined"){ //View earlier registrations
                      /*  $($old_assignments.details).each(function(){
                                Registration.oldSelectedOptions.push(this.step_id+":"+this.answer_id);
                        });*/
                        //new one
                        $sort_order = 1;
                        $str = "";
                       $ass = $old_assignments.assignment;
                     
                       $($old_assignments.details).each(function(i,e){
                             $total_steps = $old_assignments.details.length;
                             if($ass.incident_date!="0000-00-00" && typeof $old_assignments.datetime!="undefined"){
                                $total_steps = $total_steps+1;
                             }

                             if($ass.date_only!="0000-00-00" && typeof $old_assignments.dateonly!="undefined"){
                                $total_steps = $total_steps+1;
                             }

                             $strAnswer = e.str_answer;
                             $arrAnswer = $strAnswer.split("^");
                             $arrCat = "";
                             $selected_answer="";

                             if(e.str_cat_name!=null){
                                $strCat = e.str_cat_name;
                                $arrCat = $strCat.split("^");
                             }
                             for(var x=0;x<$arrAnswer.length;x++){
                                if($arrCat!=""){
                                    $selected_answer += $arrCat[x]+" > "+$arrAnswer[x]+"<br />";
                                }else{
                                    $selected_answer += $arrAnswer[x]+"<br />";
                                }
                             }
                             $str += "<li data-order='"+$sort_order+"'><div class='fl'>" +
                                    "<a href='#'><h2>" + e.str_step_name + "</h2>" +
                                    "<p>" + $selected_answer + "</p></a>" +
                                    "</div>"+
                                    "<div class='fr'>"+"<div class='step-head'>Steg "+$sort_order+" av "+$total_steps+"</div><div class='summary-link'></div>" +
                                    "</div><div class='clear'></div></li>";
                            $sort_order_add = $sort_order+1;

                            $sort_order++;
                       });

                        //Now render date time template or date only template here start
                        if($ass.incident_date!="0000-00-00" && typeof $old_assignments.datetime!="undefined"){ //for datetime template
                         
                                $assdt = $old_assignments.datetime;
                               
                                $ordr = $("li",$str).andSelf().length+1;
                                
                                
                                $sdate = moment($ass.incident_date).format("D MMMM YYYY").toLowerCase()
                                $incident_date = "Datum: " + Registration.localizeMonth($sdate);
                                $incident_time = "Tid: " + "Kl "+moment("1970-01-01 "+$ass.incident_time).format("H"); 
                                //using 1970-01-01 above because moment js needs date to format but we just need to format time.

                                $selected_answer_dt = $incident_date + "<br />" + $incident_time;

                               
                                $str += "<li><div class='fl'>" +
                                        "<a href='#'><h2>" + $assdt.step_name + "</h2>" +
                                        "<p>" + $selected_answer_dt + "</p></a>" +
                                        "</div>"+
                                        "<div class='fr'>"+"<div class='step-head'>Steg "+$ordr+" av "+$ordr+"</div><div class='summary-link'></div>" +
                                        "</div><div class='clear'></div></li>";
                            
                            
                           }

                           if($ass.date_only!="0000-00-00" && typeof $old_assignments.dateonly!="undefined"){ //for dateonly template
                                $assdt = $old_assignments.dateonly;
                               
                                $ordr = $("li",$str).andSelf().length+1;

                                //if($sort_order_add==$sort_order_dt){
                                $sdate = moment($ass.date_only).format("D MMMM YYYY").toLowerCase()
                                $dateonly = "Datum: " + Registration.localizeMonth($sdate);
                                //using 1970-01-01 above because moment js needs date to format but we just need to format time.

                                $selected_answer_dt = $dateonly;

                                $str += "<li><div class='fl'>" +
                                        "<a href='#'><h2>" + $assdt.step_name + "</h2>" +
                                        "<p>" + $selected_answer_dt + "</p></a>" +
                                        "</div>"+
                                        "<div class='fr'>"+"<div class='step-head'>Steg "+$ordr+" av "+$ordr+"</div><div class='summary-link'></div>" +
                                        "</div><div class='clear'></div></li>";
                               // }
                                
                           }
                        //Now render date time template or date only template here end
                                               
                      $("#RegistrationTask_Old_Details").find(".step-title").html($ass.answered_date);

                       $("#my_old_reg_summary_list").html($str);

                       $("#my_old_reg_summary_list").listview().listview("refresh");
                       changepage("RegistrationTask_Old_Details");

                       enableIScroll();
                       scrolls["RegistrationTask_Old_Details"].refresh();
                        
                       return false;
                    }
                   // console.warn($checkAnswer);
                    $(response.data.steps).each(function() {
                        if (this.template == "steps_text") {
                            $html += "<div id='step_" + this.step_id + "' data-template='" + this.template + "' class='step-registration-contents step-" + this.show_order + "'>";
                            $html += "<div class='step-title'>" + this.step_name + "</div><div class='step-btn-option-holder'>";
                            $html += $("<div class='media-contents'></div>").append(this.answer_text).find("iframe,img").removeAttr("height").attr("width", "100%").attr("height", "260").end().html();
                            $html += "</div>";
                            $html += "<div class='text-step-button step-text-btn-holder'><a href='javascript:void(0);' onclick='Registration.showHideSteps(" + (this.show_order + 1) + "); Registration.StopYoutubeVideo();' class='step-txt-btn border-radius'>" + this.button_text + "</a></div>";
                            $html += "</div>";
                        } else if (this.template == "steps_summary") {
                            $html += "<div id='step_" + this.step_id + "' data-template='" + this.template + "' class='step-registration-contents step-" + this.show_order + "'>";
                            $html += "<div class='step-title'>" + this.step_name + "</div>";
                            $html += "<div id='show_summary' class='options-holder template-summary'>";
                            $html += "</div>";
                            $html += "</div>";
                        } else if (this.template == "steps_datetime") {
                            datetime = true;
                            stepMinute = this.time_format;
                            $html += "<div id='step_" + this.step_id + "' data-sortorder='"+this.sort_order+"' data-template='" + this.template + "' class='step-registration-contents step-" + this.show_order + "'>";
                            $html += "<div class='step-title'>" + this.step_name + "</div>";
                             $html += "<div class='step-btn-option-holder for-date-time'>";
                            $html += "<div class='options-holder template-datetime'>";
                            
                            if (this.show_date == 1) {
                                $current_date = Registration.localizeMonth(moment().format("D MMMM YYYY").toLowerCase()); //moment().format("D MMM YYYY");
                                $hid_date = moment().format("YYYY-MM-DD");
                                
                                if($old_assignments!=null && typeof $old_assignments.assignment!="undefined"){
                                    $current_date = moment($old_assignments.assignment.incident_date).format("D MMMM YYYY");
                                    $current_date = Registration.localizeMonth($current_date.toLowerCase());
                                    $hid_date  = moment($old_assignments.assignment.incident_date).format("YYYY-MM-DD");
                                }

                                $todaytext = "";
                                if(Registration.localizeMonth(moment().format("D MMMM YYYY").toLowerCase())==$current_date){
                                    $todaytext = "Idag, ";
                                }

                                $html += "<div class='date-holder'><input name='incident_date_formatted' id='picker-selected-date' type='text' class='picker-date' readonly='readonly' value='" + $todaytext + $current_date + "' /><input type='hidden' name='incident_date' value='" + $hid_date + "' /></div>";
                                $html += "<div><a id='change-incident-date' href='javascript:void(0)' class='date-time-btn border-radius ui-btn'>Ändra dag</a></div>";
                            }

                            if (this.show_time == 1) {
                                $current_time = "Kl "+moment().format("HH");
                                $hid_time = moment().format("HH:mm:ss");
                               
                                if($old_assignments!=null && typeof $old_assignments.assignment !="undefined"){
                                    $current_time = moment("2013-01-01 "+$old_assignments.assignment.incident_time).format("HH:mm");
                                    $hid_time = moment("2013-01-01 "+$old_assignments.assignment.incident_time).format("HH:mm:ss");
                                }

                                $html += "<div class='date-holder time-holder'><input  name='incident_time_formatted' id='picker-selected-time' type='text' class='picker-date' readonly='readonly' value='" + $current_time + "' /><input type='hidden' name='incident_time' value='" + $hid_time + "' /></div>";
                                $html += "<div><a id='change-incident-time' href='javascript:void(0)' class='date-time-btn border-radius ui-btn'>Ändra tid</a></div>";
                            }
                            $html += "</div>";
                             $html += "</div>";
                            $html += "</div>";
                        }else if (this.template == "steps_date") {
                            datetime = true;
                            /*stepMinute = this.time_format;*/
                            $html += "<div id='step_" + this.step_id + "' data-sortorder='"+this.sort_order+"' data-template='" + this.template + "' class='step-registration-contents step-" + this.show_order + "'>";
                            $html += "<div class='step-title'>" + this.step_name + "</div>";
                             $html += "<div class='step-btn-option-holder for-date-only'>";
                            $html += "<div class='options-holder template-datetime'>";
                            $current_date = Registration.localizeMonth(moment().format("D MMMM YYYY").toLowerCase()); //moment().format("D MMM YYYY");
                                $hid_date = moment().format("YYYY-MM-DD");
                                
                                if($old_assignments!=null && typeof $old_assignments.assignment!="undefined"){
                                    $current_date = moment($old_assignments.assignment.date_only).format("D MMMM YYYY");
                                    $current_date = Registration.localizeMonth($current_date.toLowerCase());
                                    $hid_date  = moment($old_assignments.assignment.date_only).format("YYYY-MM-DD");
                                }

                                $todaytext = "";
                                if(Registration.localizeMonth(moment().format("D MMMM YYYY").toLowerCase())==$current_date){
                                    $todaytext = "Idag, ";
                                }

                                $html += "<div class='date-holder'><input name='date_only_formatted' id='picker-selected-date-only' type='text' class='picker-date' readonly='readonly' value='" + $todaytext + $current_date + "' /><input type='hidden' name='date_only' value='" + $hid_date + "' /></div>";
                                $html += "<div><a id='change-date' href='javascript:void(0)' class='date-time-btn border-radius ui-btn'>Ändra dag</a></div>";
                            
                            
                            $html += "</div>";
                             $html += "</div>";
                            $html += "</div>";
                        } else {

                            $html += "<div data-specialcase='"+this.special_case+"' id='step_" + this.step_id + "' data-sortorder='" + this.sort_order + "' data-template='" + this.template + "' class='step-registration-contents step-" + this.show_order + "'>";
                            if (this.template == "steps_sentence" || this.template == "steps_keywords") {
                                $name = this.template == "steps_sentence" ? "sentence" : "keyword";
                                $html += "<div class='step-title'>" + this.step_name + "</div>";
                                $html += "<div class='step-btn-option-holder'>";
                                $html += "<div class='options-holder template-" + $name + "'>";
                                $step_id = this.step_id;
                                $is_multiple_choice = this.is_multiple_choice;


                                $dataopt = $is_multiple_choice + "-" + this.max_selection_allowed;
                                $(this.answers).each(function() {
                                    if ($is_multiple_choice == 1) {
                                        $type = "checkbox";
                                    } else {
                                        $type = "radio";
                                    }
                                    $ans = {
                                        "step_id": $step_id,
                                        "is_custom_answer": 0,
                                        "is_answer_category": 0,
                                        "answer_id": this.answer_id,
                                        "app_answer_id": this.app_answer_id
                                    };

                                    $html += "<label><input onclick='Registration.eventOptOnClick($(this))' data-options='" + $dataopt + "' data-mini='true' data-stepid='" + $step_id + "' data-answerid='" + this.answer_id + "' data-answer='" + this.answer + "' value='" + JSON.stringify($ans) + "' type='" + $type + "'  class='" + $name + "_answer' name='step_answers["+$step_id+"][]'>" + this.answer + "</label>";
                                });
                                $html += "</div>";
                                if (this.allow_custom_answer == 1) {
                                    $html += "<div class='text-step-button'><a data-specialcase='"+this.special_case+"' data-options='" + $dataopt + "' data-template='" + this.template + "' data-choicetype='" + $is_multiple_choice + "' data-holder='template-" + $name + "' data-class='" + $name + "_answer' data-stepid='" + $step_id + "'  href='#popupAddAnswer' class='show-popup border-radius ui-btn ui-btn-inline ui-my-icon-plus'  data-role='button' data-rel='popup'>Lägg till svar</a><a  href='javascript:void(0)' data-identifier='custom-" + this.template + "' class='delete-options border-radius ui-btn ui-btn-up-a hide ui-btn-inline ui-my-icon-delete'  data-role='button' onclick='Registration.deleteOptions($(this))'>Radera eget svar</a></div>";
                                }
                                $html += "</div>";

                            } else if (this.template == "steps_expand_collapse") {
                                $html += "<div class='step-title'>" + this.step_name + "</div>";
                                $html += "<div class='step-btn-option-holder-ec'>";
                                $html += "<div class='options-holder template-expand-collapse'>";
                                $step_id = this.step_id;
                                $is_multiple_choice = this.is_multiple_choice;
                                $allow_add_answer = this.allow_custom_answer;

                                $html += "<div data-role='collapsibleset' data-theme='c' data-content-theme='c' data-corners='false'>";
                                $dataopt = $is_multiple_choice + "-" + this.max_selection_allowed;
                                $(this.category).each(function() {
                                    $answer_cat_id = this.answer_cat_id;
                                    $app_answer_cat_id = this.app_answer_cat_id;
                                    $answer_cat_name = this.answer_cat_name;
                                    $html += "<div id='collapsible_" + $answer_cat_id + "' data-role='collapsible'>";
                                    $html += "<h3>" + $answer_cat_name + "</h3>";
                                    $(this.answers).each(function() {
                                        if ($is_multiple_choice == 1) {
                                            $type = "checkbox";
                                        } else {
                                            $type = "radio";
                                        }
                                        $ans = {
                                            "step_id": $step_id,
                                            "is_custom_answer": 0,
                                            "is_answer_category": 0,
                                            "answer_id": this.answer_id,
                                            "app_answer_id": this.app_answer_id,
                                            "app_answer_cat_id": this.app_answer_cat_id,
                                            "answer_cat_id": $answer_cat_id
                                        };
                                        $html += "<div class='ec-answers answer-for-cat-" + $answer_cat_id + "'><label><input  onclick='Registration.eventOptOnClick($(this))' data-options='" + $dataopt + "' data-mini='true' data-stepid='" + $step_id + "' data-answercatid='" + $answer_cat_id + "' data-appanswercatid='" + $app_answer_cat_id + "' data-answerid='" + this.answer_id + "' data-answer='" + this.answer + "' value='" + JSON.stringify($ans) + "' type='" + $type + "'  class='expand_collapse_answer' name='step_answers["+$step_id+"]["+$answer_cat_id+"][]'>" + this.answer + "</label></div>";
                                    });
                                    $html += "<div class='add-more-holder'>";
                                    if($allow_add_answer==1){
                                        $html += "<button data-options='" + $dataopt + "' data-answercatid='" + $answer_cat_id + "' data-appanswercatid='" + $app_answer_cat_id + "' data-role='none' data-choicetype='" + $is_multiple_choice + "' data-theme='d' data-template='steps_expand_collapse' data-stepid='" + $step_id + "' data-holder='answer-for-cat-" + $answer_cat_id + "' class='show-popup ui-btn  ui-corner-all ui-my-icon-plus'>Lägg till</button>";
                                    }
                                    $html += "<a  href='javascript:void(0)' data-iscustomcat='no' data-answercat='"+$answer_cat_id+"' data-identifier='custom-steps_expand_collapse' class='delete-options border-radius ui-btn ui-btn-up-a hide ui-btn-inline ui-my-icon-delete'  data-role='button'  onclick='Registration.deleteOptions($(this))'>Radera eget svar</a>";
                                    $html += "</div>";
                                    $html += "</div>";
                                });
                                $html += "</div></div>";
                                if (this.allow_to_add_answer_category == 1) {
                                    $html += "<div class='text-step-button'><a data-allowaddanswer='"+$allow_add_answer+"' data-options='" + $dataopt + "' data-choicetype='" + $is_multiple_choice + "' data-template='" + this.template + "'  data-holder='template-expand-collapse' data-class='expand_collapse_answer' data-stepid='" + $step_id + "'  href='#popupAddAnswerCat' class='show-popup-cat border-radius ui-btn ui-btn-inline ui-my-icon-plus'  data-role='button' data-rel='popup'>Lägg till svar</a></div>";
                                }else if(this.allow_to_add_answer_category != 1 && $allow_add_answer==1){
                                   // $html += "<div class='text-step-button'><a  href='javascript:void(0)' data-identifier='custom-" + this.template + "' class='delete-options border-radius ui-btn ui-btn-up-a hide ui-btn-inline ui-my-icon-delete'  data-role='button'  onclick='Registration.deleteOptions($(this))'>Radera eget svar</a></div>";
                                }
                                $html +="</div>";
                            }
                            $html += "</div>";
                        }
                    });
                    
                    $("#RegistrationTask_Steps").find("div.registration-steps-holder").empty().html($html);

                   

                  //  Registration.preCheckOldRegistrationAnswers($checkAnswer);
                   // Registration.preCheckOldRegistrationAnswers(Registration.oldSelectedOptions); //uncomment this if user can edit old registration

                    if($old_assignments==null){
                        Registration.resetControls();
                    }

                    //make sure user cannot select more than the allowed options
                    $(".expand_collapse_answer,.sentence_answer,.keyword_answer").on("click", function() {
                       /* var obj = $(this);
                        $answerCatID = obj.attr("data-answercatid");
                        $rawoptions = obj.attr("data-options");
                        $id = "step_" + obj.attr("data-stepid");
                        $options = $rawoptions.split("-");
                        $is_multiple_choice = $options[0];
                        $max_selection_allowed = $options[1]; 
                        if ($answerCatID > 0) {
                            if ($is_multiple_choice == 1) {
                                $chkdlen = $("#" + $id).find("#collapsible_" + $answerCatID).find(".expand_collapse_answer:checked").size();
                                if ($chkdlen > $max_selection_allowed) {
                                    obj.prop("checked", false);
                                    return false;
                                }
                            }else{

                                console.clear();
                                console.warn("Single choice expand collapse step "+$id);
                                //in single choice, allow only to use one option irrespective of the category
                                $("#" + $id).find("[data-role='collapsible']").find(".expand_collapse_answer").prop("checked",false);
                                obj.prop("checked",true);
                                Registration.refreshControls();
                            }
                        } else {
                            $name = obj.hasClass("sentence_answer") ? "sentence" : "keyword";
                            if ($is_multiple_choice == 1) {
                                $chkdlen = $("#" + $id).find("." + $name + "_answer:checked").size();
                                if ($chkdlen > $max_selection_allowed) {
                                    obj.prop("checked", false);
                                    return false;
                                }
                            }
                        }*/
                    });

                     

                    //If step is date time type, bind date and time picker widget to respective button
                    if (datetime === true) {
                        //change-incident-date
                        var myDate = new Date();
                        var curYear = myDate.getFullYear();
                        $('#picker-selected-date').mobiscroll().date({
                            theme: 'ios7',
                            display: 'bottom',
                            dateOrder: 'dd M yy',
                            dateFormat: "d MMMM yy",
                            setText: "Lägg till",
                            cancelText: "Avbryt",
                            maxDate: new Date(),
                            dayNames: ['Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag'],
                            monthNames:['januari', 'februari', 'mars', 'april', 'maj', 'juni', 'juli', 'augusti', 'september', 'oktober', 'november', 'december'],
                            monthNamesShort: ['jan','feb','mars','apr','maj','juni','juli','aug','sept','okt','nov','dec'],
                            minDate:new Date(curYear-2,1,1),
                            onSelect: function(obj){
                               var selectedDate = "";
                               var objd = Registration.monthToEnglish(obj);
                               $("input[name='incident_date']").val(dateFormat(objd,"yyyy-mm-dd"));
                            /*   console.clear();
                               console.warn("Cdate = "+Registration.localizeMonth(dateFormat(new Date(), "yyyy-mm-dd"))+", Seldate = "+dateFormat(objd, "yyyy-mm-dd"))
                              */ if(Registration.localizeMonth(dateFormat(new Date(), "yyyy-mm-dd"))==dateFormat(objd, "yyyy-mm-dd")){
                                    selectedDate = "Idag, "+dateFormat(objd,"d mmmm yyyy");
                                    $('#picker-selected-time').mobiscroll('option', 'maxDate', new Date());
                               }else{
                                    selectedDate = dateFormat(objd,"d mmmm yyyy");
                                     $('#picker-selected-time').mobiscroll('option', 'maxDate', null);
                               }
                               $date = Registration.localizeMonth(selectedDate);

                               $("#picker-selected-date").val($date);
                            }
                        });

                        $('#picker-selected-date-only').mobiscroll().date({ //for date only template
                            theme: 'ios7',
                            display: 'bottom',
                            dateOrder: 'dd M yy',
                            dateFormat: "d MMMM yy",
                            setText: "Lägg till",
                            cancelText: "Avbryt",
                            maxDate: new Date(),
                            dayNames: ['Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag'],
                            monthNames:['januari', 'februari', 'mars', 'april', 'maj', 'juni', 'juli', 'augusti', 'september', 'oktober', 'november', 'december'],
                            monthNamesShort: ['jan','feb','mars','apr','maj','juni','juli','aug','sept','okt','nov','dec'],
                            minDate:new Date(curYear-2,1,1),
                            onSelect: function(obj){
                               var selectedDate = "";
                               var objd = Registration.monthToEnglish(obj);
                               $("input[name='date_only']").val(dateFormat(objd,"yyyy-mm-dd"));
                            /*   console.clear();
                               console.warn("Cdate = "+Registration.localizeMonth(dateFormat(new Date(), "yyyy-mm-dd"))+", Seldate = "+dateFormat(objd, "yyyy-mm-dd"))
                              */ if(Registration.localizeMonth(dateFormat(new Date(), "yyyy-mm-dd"))==dateFormat(objd, "yyyy-mm-dd")){
                                    selectedDate = "Idag, "+dateFormat(objd,"d mmmm yyyy");
                                   
                               }else{
                                    selectedDate = dateFormat(objd,"d mmmm yyyy");
                               }
                               $date = Registration.localizeMonth(selectedDate);

                               $("#picker-selected-date-only").val($date);
                            }
                        });
                       

                        //// 1= Hours, 2 = Hours + 30 Minute intervals, 3 = Hours + Minutes
                        //$stepsMin = stepMinute == 1 ? 60 : (stepMinute == 2 ? 30 : 1);
                        $stepsMin =  60; //as per recent requirement patient can select hour only

                        $('#picker-selected-time').mobiscroll("setValue","13").time({
                            theme: 'ios7',
                            display: 'bottom',
                            stepMinute: $stepsMin,
                            setText: "Lägg till",
                            cancelText: "Avbryt",
                            timeWheels: "ii:HH",
                            timeFormat: "hh:ii a",
                            maxDate: new Date(),
                            onShow: function(){
                                $("div[aria-label='Minutes']").find(".dw-i").html("Kl");
                                $(this).mobiscroll("setValue",$("input[name='incident_time']").val());
                            },
                            onSelect: function(obj){
                                $("input[name='incident_time']").val(Registration.convertDateTo24Hour(obj));
                                $("#picker-selected-time").val(Registration.convertDateTo24Hour(obj,1));
                            }
                        });

                        $("#change-incident-date").on("click", function() {
                            $('#picker-selected-date').trigger("focus");
                        });

                         $("#change-date").on("click", function() {
                            $('#picker-selected-date-only').trigger("focus");
                        });

                        $("#change-incident-time").on("click", function() {
                            $('#picker-selected-time').trigger("focus");
                        });
                    }


                    //open popup to add answer category
                    $(document).on("click",".show-popup-cat", function() {
                        $stepid = $(this).attr("data-stepid");
                        $destination = $(this).attr("data-holder");
                        $template = $(this).attr("data-template");
                        $controltype = $(this).attr("data-choicetype"); //radio or checkbox
                        $dataopt = $(this).attr("data-options"); //radio or checkbox
                        $allowcustomanswer = $(this).attr("data-allowaddanswer");

                        $answercat = "answer_cat";

                        $('#popupAddAnswerCat').popup("open").on("popupafteropen",function(event,ui){
                            $("#add_custom_answer_cat").focus();
                            filehelper.showHideKeyBoard("show");
                        });

                                               
                        /*$('#popupAddAnswerCat').popup("option",{
                            beforeposition: function() {
                                $(this).css({
                                    width: window.innerWidth - 20
                                });
                            },
                            x: (window.innerWidth- $('#popupAddAnswerCat').width())/2,
                            y: (window.innerHeight- $('#popupAddAnswerCat').height())/2,
                            theme: "d",
                            transition: "pop",
                            shadow: false,
                            tolerance: "2,2"
                        }).popup("open").on("popupafteropen",function(event,ui){
                             $("#add_custom_answer_cat").focus();
                             $("#add_custom_answer_cat").on("focus",function(){
                             $('#popupAddAnswerCat').popup("reposition",{
                                      x: (window.innerWidth-$('#popupAddAnswerCat').width())/2,
                                      y: (window.innerHeight-$('#popupAddAnswerCat').height())/2
                                });
                            });
                        });*/

                       

                        $(".add-custom-answers-cat").on("click", function() {
                            $choicetype = $controltype == 1 ? "checkbox" : "radio";
                            $custom_answer_cat = $.trim($("#add_custom_answer_cat").val());
                            if ($custom_answer_cat == "") return false;

                            $("#add_custom_answer_cat").val("");
                            $newc = parseInt($("#step_" + $stepid).find(".answer_cat").length) + 1;
                            $str = "";
                            $str += "<div id='collapsible_" + $newc + "' data-role='collapsible' style='position:relative;'>";

                            $ans_cat = {
                                "step_id": $stepid,
                                "temp_id": $newc,
                                "is_custom_answer": 1,
                                "is_answer_category": 1,
                                "custom_answer_cat": $custom_answer_cat
                            };
                            $str += "<h3>" + $custom_answer_cat;
                            $str += "<span data-role='none' onclick='Registration.deleteAnswerCategory($(this),event)' class='delete-category ui-icon-delete ui-corner-all'>&nbsp;</span>";
                            $str += "</h3>";
                            $str += "<input type='hidden' class='answer_cat' name='step_answers["+$stepid+"][]' value='" + JSON.stringify($ans_cat) + "'/>";

                            $str += "<div class='ec-answers temp-answer-for-cat-" + $newc + "'></div>";

                            //   $str +="<div class='fl'><button data-choicetype='"+$controltype+"' data-theme='d' data-template='steps_expand_collapse' data-stepid='"+$stepid+"' data-holder='temp-answer-for-cat-"+$newc+"' class='show-popup  ui-btn ui-btn-up-d ui-icon-plus ui-btn-icon-notext ui-corner-all ui-icon-addmore'>+</button></div>"; 
                            $str += "<div class='add-more-holder'>";
                            if($allowcustomanswer==1){
                                    $str += "<button data-options='" + $dataopt + "' data-newlyadded='1' data-answercatid='" + $newc + "' data-role='none' data-choicetype='" + $controltype + "' data-theme='d' data-template='steps_expand_collapse' data-stepid='" + $stepid + "' data-holder='temp-answer-for-cat-" + $newc + "' class='show-popup ui-btn ui-corner-all ui-my-icon-plus'>Lägg till</button>";                                
                            }
                            $str += "<a  href='javascript:void(0)' data-iscustomcat='yes' data-answercat='"+$custom_answer_cat+"' data-identifier='custom-steps_expand_collapse' class='delete-options border-radius ui-btn ui-btn-up-a hide ui-btn-inline ui-my-icon-delete'  data-role='button'  onclick='Registration.deleteOptions($(this))'>Radera eget svar</a>";
                            $str += "</div>";
                            //  $str +="<div class='fl'><a href='#popupAddAnswer' data-choicetype='"+$controltype+"' data-template='steps_expand_collapse' data-stepid='"+$stepid+"' data-holder='temp-answer-for-cat-"+$newc+"' class='show-popup ui-btn ui-icon-plus ui-btn-icon-notext'>Icon only</a></div>";              
                            $str += "</div>";
                            // $str ="<label><input data-mini='true' data-stepid='"+$stepid+"' data-answer='"+$custom_answer_cat+"' data-answerid='0' value='checked' type='"+$choicetype+"' class='"+$control+"' name='"+$control+"[]'>"+$custom_answer+"</label>";
                            var existingOptions = [];
                            $("#step_" + $stepid).find(".ui-collapsible-set").find("h3").each(function() {
                                $existinganswrs = $(this).text();
                                $existinganswrs = $existinganswrs.toLowerCase();
                                existingOptions.push($existinganswrs);
                            });

                            if ($.inArray($custom_answer_cat.toLowerCase(), existingOptions) !== -1) {
                                msgBox("Detta alternativet finns redan");
                                return false;
                            } else {
                                $("#step_" + $stepid).find(".ui-collapsible-set").append($str);
                                 
                            }

                           filehelper.showHideKeyBoard("hide");

                            $('#popupAddAnswerCat').popup("close");

                            Registration.refreshControls();
                         //   $("#step_" + $stepid).find(".temp-answer-for-cat-" + $newc).parent("div").prev("h3").parent("div").before("<span data-role='none' onclick='Registration.deleteAnswerCategory($(this),event)' class='delete-category ui-icon-delete ui-corner-all'>&nbsp;</span>");

                             Registration.floatKeywords("steps_expand_collapse");

                           

                            scrolls["RegistrationTask_Steps"].refresh();

                        });
                    });

                    $(document).on("click", ".show-popup", function(e) {
                        $triggerer = $(this);
                        e.stopImmediatePropagation();
                        e.preventDefault();
                        $stepid = $(this).attr("data-stepid");
                        $control = $(this).attr("data-class");
                        $destination = $(this).attr("data-holder");
                        $template = $(this).attr("data-template");
                        $answer_cat_id = $(this).attr("data-answercatid") || 0;
                        $app_answer_cat_id = $(this).attr("data-appanswercatid") || 0;
                        $controltype = $(this).attr("data-choicetype"); //radio or checkbox
                        $dataopt = $(this).attr("data-options");
                        $is_new_cats_answer = $(this).attr("data-newlyadded") || 0;
                        $specialcase = $(this).attr("data-specialcase");
                        console.log( $specialcase);
                      
                        $('#popupAddAnswer').popup("open").on("popupafteropen",function(event,ui){
                                $("#add_custom_answer").focus();
                                filehelper.showHideKeyBoard("show");
                        });

                         /*$('#popupAddAnswer').popup("option",{
                            beforeposition: function() {
                                $(this).css({
                                    width: window.innerWidth - 20
                                });
                            }, 
                            positionTo: "window",
                            theme: "d",
                            transition: "pop",
                            shadow: false,
                            tolerance: "2,2"
                        }).popup("open").on("popupafteropen",function(event,ui){
                            $("#add_custom_answer").focus();
                        });

                        $("#add_custom_answer").on("focus",function(){
                                $('#popupAddAnswer').popup("reposition",{
                                    y: (window.innerHeight-$('#popupAddAnswer').height())/2
                                });
                        });*/
                        

                        $(".add-custom-answers").on("click", function() {
                            $custom_answer = $.trim($("#add_custom_answer").val());
                            $template = $("#step_" + $stepid).attr("data-template");

                            if ($custom_answer == "") return false;

                            $("#add_custom_answer").val("");
                            $choicetype = $controltype == 1 ? "checkbox" : "radio";

                            if($choicetype=="radio"){//reset other options before selecting newly added option
                                  $("#step_" + $stepid).find("input[type='radio']").prop("checked",false);
                                  $("#step_" + $stepid).find("input[type='radio']").removeAttr("checked");
                            }

                            if ($answer_cat_id > 0 || $app_answer_cat_id>0) {
                                var d = $dataopt.split("-");
                                $checked_length = $("#step_" + $stepid).find("#collapsible_"+$answer_cat_id).find(".expand_collapse_answer:checked").size();



                                if(d[0]==0 || d[1]>$checked_length){
                                    $checked = " checked='checked'";
                                }else{
                                    $checked = "";
                                }


                                if($is_new_cats_answer==1)
                                {
                                    $ans = {
                                        "step_id": $stepid,
                                        "is_custom_answer": 1,
                                        "is_answer_category": 0,
                                        "temp_id": $answer_cat_id,
                                        "is_member_of_new_cat": $is_new_cats_answer,
                                        "custom_answer": $custom_answer, 
                                        "specialcase": $specialcase 
                                    };
                                    $anshidden = {
                                        "step_id": $stepid,
                                        "is_custom_answer": 1,
                                        "is_answer_category": 0,
                                        "temp_id": $answer_cat_id,
                                        "is_member_of_new_cat": $is_new_cats_answer,
                                        "custom_answer": $custom_answer,
                                        "is_checked": $.trim($checked)=="" ? false : true, 
                                        "specialcase": $specialcase 
                                    };
                                }else{
                                    $ans = {
                                        "step_id": $stepid,
                                        "is_custom_answer": 1,
                                        "is_answer_category": 0,
                                        "answer_cat_id": $answer_cat_id,
                                        "app_answer_cat_id": $app_answer_cat_id,
                                        "app_answer_cat_id": $app_answer_cat_id,
                                        "is_member_of_new_cat": $is_new_cats_answer,
                                        "custom_answer": $custom_answer, 
                                        "specialcase": $specialcase 
                                    };
                                    $anshidden = {
                                        "step_id": $stepid,
                                        "is_custom_answer": 1,
                                        "is_answer_category": 0,
                                        "answer_cat_id": $answer_cat_id,
                                        "app_answer_cat_id": $app_answer_cat_id,
                                        "is_member_of_new_cat": $is_new_cats_answer,
                                        "custom_answer": $custom_answer,
                                        "is_checked": $.trim($checked)=="" ? false : true , 
                                        "specialcase": $specialcase 
                                    };
                                }
                                //" + JSON.stringify($ans) + "
                                //$str = "<div class='ec-answers answer-for-cat-" + $answer_cat_id + "'><label><input type='hidden'  name='step_answers["+$stepid+"][]' value='"+JSON.stringify($anshidden)+"'><input data-options='" + $dataopt + "' data-delidentifier='custom-" + $template + "' data-mini='true' data-stepid='" + $stepid + "' data-answercatid='" + $answer_cat_id + "' data-appanswercatid='" + $app_answer_cat_id + "' data-answerid='0' data-answer='" + $custom_answer + "' onclick='Registration.updateCustomAnswers();' value='' data-customoption='true' type='" + $choicetype + "'  class='expand_collapse_answer' name='step_answers_temp["+$stepid+"][]'>" + $custom_answer + "</label></div>";
                               

                                $str = "<div class='ec-answers answer-for-cat-" + $answer_cat_id + "'><label><input type='hidden'  name='step_answers["+$stepid+"][]' value='"+JSON.stringify($anshidden)+"'><input  onclick='Registration.eventOptOnClick($(this))' data-options='" + $dataopt + "' data-delidentifier='custom-" + $template + "' data-mini='true' data-stepid='" + $stepid + "' data-answercatid='" + $answer_cat_id + "' data-appanswercatid='" + $app_answer_cat_id + "' data-answerid='0' data-answer='" + $custom_answer + "' onclick='Registration.updateCustomAnswers();' value='' data-customoption='true' type='" + $choicetype + "'  class='expand_collapse_answer' name='step_answers["+$stepid+"]["+$answer_cat_id+"][]' "+$checked+" >" + $custom_answer + "</label></div>";
                            } else {
                                $ans = {
                                    "step_id": $stepid,
                                    "is_custom_answer": 1,
                                    "is_answer_category": 0,
                                    "custom_answer": $custom_answer, 
                                    "specialcase": $specialcase 
                                };

                                
                                //" + JSON.stringify($ans) + "

                                var d = $dataopt.split("-");
                                $checked_length = $("#step_" + $stepid).find("." + $control+":checked").size();
                                if(d[0]==0 || d[1]>$checked_length){
                                    $checked = " checked='checked'";
                                }else{
                                    $checked = "";
                                }

                                $anshidden = {
                                    "step_id": $stepid,
                                    "is_custom_answer": 1,
                                    "is_answer_category": 0,
                                    "custom_answer": $custom_answer,
                                    "is_checked": $.trim($checked)=="" ? false : true, 
                                    "specialcase": $specialcase 
                                };

                                $str = "<label><input type='hidden'  name='step_answers["+$stepid+"][]' value='"+JSON.stringify($anshidden)+"' /><input  onclick='Registration.eventOptOnClick($(this))' data-options='" + $dataopt + "' data-delidentifier='custom-" + $template + "' data-mini='true' data-stepid='" + $stepid + "' data-answer='" + $custom_answer + "' data-answerid='0' onclick='Registration.updateCustomAnswers();' value='' type='" + $choicetype + "'  data-customoption='true' class='" + $control + "' name='step_answers["+$stepid+"][]' "+$checked+">" + $custom_answer + "</label>";
                            }
                            var existingOptions = [];
                            $("#step_" + $stepid).find("." + $control).each(function() {
                                $existinganswrs = $(this).attr("data-answer");
                                $existinganswrs = $existinganswrs.toLowerCase();
                                existingOptions.push($existinganswrs);
                            });

                            if ($.inArray($custom_answer.toLowerCase(), existingOptions) !== -1) {
                                msgBox("Detta alternativet finns redan");
                                return false;
                            } else {
                                if ($template == "steps_expand_collapse") {
                                    $triggerer.parent("div.add-more-holder ").before($str);
                                    $triggerer.parent("div.add-more-holder ").find("a[data-identifier='custom-steps_expand_collapse']").removeClass("hide");
                                    //custom-steps_expand_collapse
                                     //now if options in category is checked, don't allow use collapse it
                                    $triggerer.parents(".ui-collapsible-content").prev("h3").find("a.ui-collapsible-heading-toggle").on("click", function(){
                                        var t = $("#step_" + $stepid).find("#collapsible_" + $answer_cat_id).find(".expand_collapse_answer:checked").size();
                                        if(t>0){
                                            return false;
                                        }else{
                                            return true;
                                        }
                                    })
                                } else {
                                    $("#step_" + $stepid).find("." + $destination).append($str);
                                    $("#step_" + $stepid).find("a[data-identifier='custom-" + $template + "']").removeClass("hide");

                                }

                               
                                //now preselect newly added option only if this doesnot exceeds the maximum allowed selection
                            }

                            
                            filehelper.showHideKeyBoard("hide");

                            $('#popupAddAnswer').popup("close");
                            
                            Registration.refreshControls();

                            Registration.floatKeywords($template);

                            

                            $(".delete-options").removeClass("disabled");

                            scrolls["RegistrationTask_Steps"].refresh();
                        });
                    });

                    Registration.showHideSteps(1);
                    Registration.refreshControls();
                    changepage("RegistrationTask_Steps");

                    enableIScroll();
                    scrolls["RegistrationTask_Steps"].refresh();

                    Registration.floatKeywords("steps_keywords");
                    Registration.floatKeywords("steps_expand_collapse");

                    //refresh scroll when expand collapse is done
                    
                    $(document).on( "collapse", ".ui-collapsible", function( event, ui ){
                        scrolls["RegistrationTask_Steps"].refresh();
                    }).on( "expand", ".ui-collapsible", function( event, ui ){
                        scrolls["RegistrationTask_Steps"].refresh();
                    }); 

                }
            } else {
                msgBox(MESSAGE.NO_INTERNET);
            }
        });
    },
    
    refreshControls: function() {
        if ($("input[type='checkbox'],input[type='radio']").length > 0) {
            $("input[type='checkbox'],input[type='radio']").checkboxradio().checkboxradio("refresh");
        }

        if ($("div[data-role='collapsibleset']").length > 0) {
            $("div[data-role='collapsibleset']").collapsibleset().collapsibleset("refresh");
        }

        if ($("button[data-role='button']").length > 0) {
            $("button[data-role='button']").button().button("refresh");
        }
    },

    showHideSteps: function(sort_order) {


        $sel = $("#RegistrationTask_Steps").find("div.registration-steps-holder");
        //<div class="fl steps-counter">Step 1 of 16</div>
        $totalSteps = $sel.find(".step-registration-contents").length;

        if(sort_order>$totalSteps) sort_order = $totalSteps;

        $("#RegistrationTask_Steps").find(".steps-counter").html("Steg " + sort_order + " av " + $totalSteps);
        $sel.find(".step-registration-contents").addClass("hide");
        $sel.find(".step-" + sort_order).removeClass("hide");

        $template = $sel.find(".step-" + sort_order).attr("data-template");
        currentRegStepID = $sel.find(".step-" + sort_order).attr("id");

        if(sort_order==$totalSteps){
            $(".reg-navigation-holder").removeClass("hide").addClass("hide");
            $(".save-nav-holder").removeClass("hide");
        }else{
            $(".reg-navigation-holder").removeClass("hide");
            $(".save-nav-holder").addClass("hide");
        }

        var fromSummary = 0;
        if(arguments.length>1)
        {
            fromSummary = 1;
        }

        if($template=="steps_summary"){
            Registration.generateSummary();
        }else if($template=="steps_text"){
            $(".reg-navigation-holder").addClass("hide");
            $(".save-nav-holder").addClass("hide");
            if(sort_order==$totalSteps){
                $(".step-txt-btn").attr("onclick","Registration.SaveRegistration()");
            }
        }

       /* if ($template != "steps_summary" && $template != "steps_text") {
            $(".reg-navigation-holder").removeClass("hide");
            $(".save-nav-holder").addClass("hide");
        } else {
            $(".reg-navigation-holder").removeClass("hide").addClass("hide");
            if ($template == "steps_summary") {
                $(".save-nav-holder").removeClass("hide");
                Registration.generateSummary();
            }
        }*/

        $(".reg-navigation-holder").attr("data-currentstep", sort_order);
        $(".reg-navigation-holder").attr("data-totalsteps", $totalSteps);
        $(".reg-navigation-holder").attr("data-currenttemplate", $template);
        $(".reg-navigation-holder").attr("data-fromsummary", fromSummary);



        Registration.adjustHeights();
        Registration.floatKeywords($template);
        if($("#RegistrationTask_Steps").is(":visible")){
            scrolls["RegistrationTask_Steps"].refresh();
        }
    },

    floatKeywords: function(template) {
        if (template == "steps_keywords") {
            $(".template-keyword").find(".ui-checkbox,.ui-radio").addClass("ui-keywords");
            $(".template-keyword").after("<div class='clear'></div>");
        } else if (template == "steps_expand_collapse") {
            $(".ec-answers").find(".ui-checkbox,.ui-radio").addClass("ui-keywords");
            $(".ui-collapsible-set").find("div.ui-collapsible-content").after("<div class='clear'></div>");
        }
    },

    adjustHeights: function() {

       /* $windowHeight = window.innerHeight;
        $headerHeight = 74;
        $bottomButtonHeight = 68;
        $statusHeight = 20;
        $contentHeight = parseInt($windowHeight) - parseInt($headerHeight) - parseInt($bottomButtonHeight)-parseInt($statusHeight);
        $(".registration-steps-holder").css("min-height", $contentHeight + "px");*/

    },

    navigateStep: function(direction) {
        
        $currentStep = $(".reg-navigation-holder").attr("data-currentstep");
        $totalSteps = $(".reg-navigation-holder").attr("data-totalsteps");
        $current_template = $(".reg-navigation-holder").attr("data-currenttemplate");
        $fromSummary = $(".reg-navigation-holder").attr("data-fromsummary");

        if (direction == "prev" && $currentStep == 1) {
            Registration.fetchRegistrations();
            return false;
        } else if (direction == "next" && $currentStep == $totalSteps) {
            return false;
        } else {

            //check if user has selected option in the current step, if not warn them, if yes navigate to next step
            if ($current_template == "steps_sentence" && direction != "prev") {
                if ($("#" + currentRegStepID).find(".sentence_answer:checked").length == 0) {
                    msgBox("Välj minst ett alternativ");
                    return false;
                }
            } else if ($current_template == "steps_keywords" && direction != "prev") {
                if ($("#" + currentRegStepID).find(".keyword_answer:checked").length == 0) {
                    msgBox("Välj minst ett alternativ");
                    return false;
                }
            } else if ($current_template == "steps_expand_collapse" && direction != "prev") {
                if ($("#" + currentRegStepID).find(".expand_collapse_answer:checked").length == 0) {
                    msgBox("Välj minst ett alternativ");
                    return false;
                }
            }

            $newStep = direction == "prev" ? parseInt($currentStep) - 1 : parseInt($currentStep) + 1;
         
            if($fromSummary==true){
                $item = $(".registration-steps-holder").find("[data-template='steps_summary']").index();
                $newStep = parseInt($item)+1;
            }
            Registration.showHideSteps($newStep);
        }

        scrolls["RegistrationTask_Steps"].scrollTo(0,0,100);
    },

    deleteOptions: function(obj) {
        $identifier = obj.attr("data-identifier");
        if ($identifier == "custom-steps_expand_collapse") {
            $chklength = obj.parent("div.add-more-holder").parent("div").find("input[data-delidentifier='" + $identifier + "']:checked");
           
            if($chklength.length>1){
                $confirmMessage = "Vill du ta bort de markerade svaren?";
            }else if($chklength.length==1){
                $ans = $chklength.attr("data-answer");
                $confirmMessage = "Vill du ta bort '"+$ans+"'?";
            }else{
                return false;
            }
        }else{
            $chklength = $("input[data-delidentifier='" + $identifier + "']:checked").length;
            if($chklength>1){
                $confirmMessage = "Vill du ta bort de markerade svaren?";
            }else if($chklength==1){
                $ans = $("input[data-delidentifier='" + $identifier + "']:checked").attr("data-answer");
                $confirmMessage = "Vill du ta bort '"+$ans+"'?";
            }else{
                return false;
            }
        }

        confirmBox($confirmMessage, function(button) {

            if (button == 1) {
                if ($identifier == "custom-steps_expand_collapse") {
                    var t =  obj.parent("div.add-more-holder").parent("div").find("input[data-delidentifier='" + $identifier + "']:checked");
                    t.each(function() {
                        $(this).parent("div").remove();;
                        
                        $length =obj.parent("div.add-more-holder").parent("div").find("input[data-delidentifier='" + $identifier + "']:checked").length;
                        if ($length == 0) {
                            obj.addClass("hide");
                        }
                    });
                }else{
                    $("input[data-delidentifier='" + $identifier + "']:checked").each(function() {
                
                        
                        $(this).parent("div").remove();;
                        
                        $length = $("input[data-delidentifier='" + $identifier + "']").length;
                        if ($length == 0) {
                            obj.addClass("hide");
                        }
                    });
                }
            }
        });
        
    },

    deleteAnswerCategory: function(obj, e) {
        e.stopImmediatePropagation();
        e.preventDefault();
        obj.parents("div.ui-collapsible").remove();
        obj.remove();
    },

    generateSummary: function() {
        //show_summary
        //$step_length = $(".step-registration-contents").length;
        $str = "<ul id='summary_list' class='summary_list' data-role='listview' data-split-icon='edit' data-split-theme='f' data-inset='true'>";
        $(".step-registration-contents").each(function() {
            $tmpl = $(this).attr("data-template");
            if ($tmpl !== "steps_text" && $tmpl !== "steps_summary") {

                $id = $(this).attr("id");
                $selector = $("#" + $id);
               // $sort_order = $selector.attr("data-sortorder");
                $get_class = $selector.attr("class");
                $sort_order = $get_class.replace(/[^0-9.]/g, "");
                $answers = [];
                $title = $selector.find(".step-title").html();

                if ($tmpl == "steps_datetime") {
                    $iTime = $("#picker-selected-time").val();
                    $iDate = $("#picker-selected-date").val();

                    if($iDate!=undefined){
                        $incident_date = "Datum: " + $("#picker-selected-date").val();
                    }else{
                        $incident_date = "Datum: Idag, " + moment().format("D MMMM YYYY").toLowerCase();
                    }
                    if($iTime!=undefined){
                        $incident_time = "Tid: " + $("#picker-selected-time").val();
                    }else{
                        $incident_time = "";
                    }
                    $selected_answer = $incident_date + "<br />" + $incident_time;
                }else if ($tmpl == "steps_date") {
                    $date_only = "Datum: " + $("#picker-selected-date-only").val();
                    $selected_answer = $date_only;
                } else {
                    $selector.find(".options-holder").find("input[name^='step_answers']:checked").each(function() {
                        $answer_cat = "";
                        if ($tmpl == "steps_expand_collapse") {
                            $answer_cat = $(this).parent("div").parent("div").parent("div").prev("h3").find(".ui-btn-text span")[0].previousSibling.nodeValue + " - ";
                            $answers.push($answer_cat + "" + $(this).attr("data-answer") + "<br/>");
                        } else {
                            $answers.push($(this).attr("data-answer"));
                        }
                    });
                    if ($tmpl == "steps_expand_collapse") {
                        $selected_answer = $answers.join("");
                    } else {
                        $selected_answer = $answers.join(", ");
                    }
                }


                /*$str += "<li>" +
                    "<a href='#'><h2>" + $title + "</h2>" +
                    "<p>" + $selected_answer + "</p></a>" +
                    "<a href='javascript:void(0)' onclick='Registration.showHideSteps(" + $sort_order + ")' data-rel='popup' data-position-to='window' data-theme='f' data-transition='pop'>Edit</a>" +
                    "</li>";*/

              
        //<div class="fl steps-counter">Step 1 of 16</div>
                $total_steps = $("#RegistrationTask_Steps").find("div.registration-steps-holder").find(".step-registration-contents").length;
                $str += "<li><div class='fl'>" +
                    "<a href='#'><h2>" + $title + "</h2>" +
                    "<p>" + $selected_answer + "</p></a>" +
                    "</div>"+
                    "<div class='fr'>"+"<div class='step-head'>Steg "+$sort_order+" av "+$total_steps+"</div><div class='summary-link'><a href='javascript:void(0)' onclick='Registration.showHideSteps(" + $sort_order + ",true)' data-fromsummary='1'  data-theme='f' class='date-time-btn border-radius ui-btn'>Ändra</a></div>" +
                    "</div><div class='clear'></div></li>";
            }
        });

        $str += "</ul>";
        $("#show_summary").html($str);

        $("#summary_list").listview().listview("refresh");
    },
    validateUserSelection: function(){
        var container = $("#frmRegistrationSteps").find(".registration-steps-holder");
        var error = 0;

        container.find(".step-registration-contents").each(function(){
            $radio_length = $(this).find("input[type='radio']").length;
            $check_length = $(this).find("input[type='checkbox']").length;
            if($radio_length>0){
                $checked_radio = $(this).find("input[type='radio']:checked").length;
                if($checked_radio==0){
                    error++;
                }
            }
            if($check_length>0){
                $checked_chkbox = $(this).find("input[type='checkbox']:checked").length;
                if($checked_chkbox==0){
                    error++;
                }
            }
        });

        if(error>0){
            msgBox("Välj minst ett alternativ för varje steg.");
            throw "Välj minst ett alternativ för varje steg.";
        }
    },


    SaveRegistration: function() {
        Registration.validateUserSelection();

        Registration.modulesSaved=0;
        offlinehelper.syncIntervalOnRegistration=setInterval(function(){
            console.log("Total count is "+Registration.modulesSaved);
            if(Registration.modulesSaved==3){
                        clearInterval(offlinehelper.syncIntervalOnRegistration);
                        msgBox("Registreringen sparades");
                        Registration.resetControls();
                        changepage("RegistrationTask"); //RegistrationTask_List
                        $(".earlier-registration").removeClass("desaturate");
                        offlinehelper.prepareForSync(false);
            }
                
        },100)

        var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","form_data":"' + $("#frmRegistrationSteps").serialize() + '"}';

        var regid=$('#registration_id').val();

        var assignment_id=$('#reg_assignment_id').val();
        var app_reg_assignment_id=$('#app_reg_assignment_id').val();
        if(assignment_id==0 && app_reg_assignment_id==0){
            var data={
                  'assignment_id':0,
                  'assignment_code':0,
                  'registration_id':regid,
                  'flow_id':0,
                  'patient_id':userdetails.user_id,
                  'incident_date':moment($("input[name='incident_date']").val()).format("YYYY-MM-DD"),
                  'incident_time':$("input[name='incident_time']").val(),
                  'date_only':$("input[name='date_only']").val(),
                  'answered_date':moment().format("YYYY-MM-DD HH:mm:ss"),
                  'stage_id':(userdetails.stage_number=="null") ? 0 : userdetails.stage_number
              };
          sqlhelper.insertJSONData('tbl_patient_assignments',data,function(){
                    sqlhelper.db.transaction(function(tx) {
                        tx.executeSql("SELECT MAX(app_assignment_id) as app_assignment_id FROM tbl_patient_assignments;", [], function(txs,results){
                            if(results.rows.length>0){ 
                                    var new_app_assignment_id=results.rows.item(0).app_assignment_id;
                                    Registration.InsertRegistrations(new_app_assignment_id,0);
                            }else{  
                                 msgBox("Error in saving registration");
                            }
                                     
                        });
                    });
          });

        }else{
             var toupdate={
                    where:{
                        'app_assignment_id': app_reg_assignment_id,
                    },
                    fields:{
                          'incident_date':moment().format("YYYY-MM-DD"),
                          'incident_time':moment().format("HH:mm:ss"),
                          'date_only':moment().format("YYYY-MM-DD"),
                          'answered_date':moment().format("YYYY-MM-DD HH:mm:ss"),
                          'edited':1
                     }
                };

           
              sqlhelper.updateData('tbl_patient_assignments',toupdate,function(){
                                            
                                 sqlhelper.db.transaction(function(tx) {
                                    tx.executeSql("DELETE FROM tbl_patient_assignment_details where app_assignment_id='"+app_reg_assignment_id+"'  OR assignment_id='"+assignment_id+"'", [], function(txs,results){
                                        console.log("All assignments are deleted. Ready to insert new assignment.");
                                    });
                                  });
                        
                                    //lastid=app_reg_assignment_id;

                                  Registration.InsertRegistrations(app_reg_assignment_id,0);


                            
                });
          }
 
        },
        InsertRegistrations:function(new_app_assignment_id,assignment_id){
        var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","form_data":"' + $("#frmRegistrationSteps").serialize() + '"}';

        var regid=$('#registration_id').val();

        var assignment_id=$('#reg_assignment_id').val();
        var app_reg_assignment_id=$('#app_reg_assignment_id').val();
        var answers=[];
        var categories=[];
        console.log("Inserting assignment detail");
        if($('.registration-steps-holder input').length>0){
            var anslengthcount=1;
            var anslength=$('.registration-steps-holder input').length;
        $('.registration-steps-holder input').each(function(i,e){
               if($(this).attr('type')=='radio' || $(this).attr('type')=='checkbox'){
                    if($(this).is( ":checked" )){
                        //console.log($(this).val());
                        if($(this).val()!="" && $(this).attr('data-customoption')!='true'){
                            var tosave= $.parseJSON($(this).val());
                               var data={
                                          'assignment_details_id':0,
                                          'app_assignment_id':new_app_assignment_id,
                                          'assignment_id':assignment_id,
                                          'registration_id':regid,
                                          'flow_id':0,
                                          'step_id':tosave.step_id,
                                          'answer_id':tosave.answer_id,
                                          'app_answer_id':tosave.app_answer_id,
                                          'assignment_code':0
                                      };
                            sqlhelper.insertJSONData('tbl_patient_assignment_details',data);
                             if(anslengthcount==anslength){
                                console.log("Returned from tbl_patient_assignment_details : modules increased 1");
                                Registration.modulesSaved++;
                            }
                             anslengthcount++;
                        }else{
                           
                             if(anslengthcount==anslength){
                                console.log("Reeturned from tbl_patient_assignment_details  : modules increased 1");
                                Registration.modulesSaved++;
                            }
                             anslengthcount++;
                        }
                    }else{
                        
                             if(anslengthcount==anslength){
                                console.log("Reeturned from tbl_patient_assignment_details  : modules increased 1");
                                Registration.modulesSaved++;
                            }
                             anslengthcount++;
                    }
               }else{
             
                             if(anslengthcount==anslength){
                                console.log("Reeturned from tbl_patient_assignment_details  : modules increased 1");
                                Registration.modulesSaved++;
                            }
                             anslengthcount++;
               }
               
        })
        }else{
             console.log("Reeturned from tbl_patient_assignment_details  : modules increased 1");
             Registration.modulesSaved++;
        }
        $ans_cat_arr={};
        console.log("Inserting Custom answers");
        if($('.registration-steps-holder input[type="hidden"]').length>0){
             var anshiddentlengthcount=1;
            var anshiddentlength=$('.registration-steps-holder input[type="hidden"]').length;
            $('.registration-steps-holder input[type="hidden"]').each(function(i,e){
                $self_input=$(this);
                    var json=$(this).val();
                    if(json!=""){
                        try {
                            json=$.parseJSON(json);
                            if(json.is_custom_answer==1 && json.is_answer_category==0 && json.temp_id==undefined){     
                                sqlhelper.db.transaction(function(tx) {
                                    tx.executeSql("SELECT MAX(sort_order) as sort_id FROM tbl_answers;", [], function(txs,results){
                                                 lastid=results.rows.item(0).sort_id;
                                                 if(json.specialcase==1){
                                                            var data={
                                                                            'answer_id':0,
                                                                            'answer':json.custom_answer,
                                                                            'step_id':0,
                                                                            'answer_cat_id':json.answer_cat_id,
                                                                            'app_answer_cat_id':json.app_answer_cat_id,
                                                                            'added_date':moment().format("YYYY-MM-DD"),
                                                                            'last_updated':moment().format("YYYY-MM-DD"),
                                                                            'answer_status':1,
                                                                            'sort_order':lastid+1,
                                                                            'answer_type':'custom',
                                                                            'mapped_answer_id':0,
                                                                            'special_answer':1
                                                                        };
                                                 }else{
                                                        var data={
                                                                            'answer_id':0,
                                                                            'answer':json.custom_answer,
                                                                            'step_id':json.step_id,
                                                                            'answer_cat_id':json.answer_cat_id,
                                                                            'app_answer_cat_id':json.app_answer_cat_id,
                                                                            'added_date':moment().format("YYYY-MM-DD"),
                                                                            'last_updated':moment().format("YYYY-MM-DD"),
                                                                            'answer_status':1,
                                                                            'sort_order':lastid+1,
                                                                            'answer_type':'custom',
                                                                            'mapped_answer_id':0,
                                                                            'special_answer':0
                                                                        };
                                                }

                                                
                                                sqlhelper.insertJSONData('tbl_answers',data,function(){

                                                     sqlhelper.db.transaction(function(tx) { 
                                                        tx.executeSql("SELECT MAX(app_answer_id) as app_answer_id FROM tbl_answers where answer like '%"+json.custom_answer+"%';", [], function(txs,results){
                                                                app_answer_id=results.rows.item(0).app_answer_id;
                                                                         
                                                                          if(json.is_checked==true){


                                                                                 var data={
                                                                                      'assignment_details_id':0,
                                                                                      'app_assignment_id':new_app_assignment_id,
                                                                                      'assignment_id':assignment_id,
                                                                                      'registration_id':regid,
                                                                                      'flow_id':0,
                                                                                      'step_id':json.step_id,
                                                                                      'answer_id':0,
                                                                                      'app_answer_id':app_answer_id,
                                                                                      'assignment_code':0
                                                                                 };

                                                                                sqlhelper.insertJSONData('tbl_patient_assignment_details',data,function(){
                                                                                        if(anshiddentlengthcount==anshiddentlength){
                                                                                                console.log("Reeturned from tbl_answers  : modules increased 2");
                                                                                                Registration.modulesSaved++;
                                                                                        }
                                                                                        anshiddentlengthcount++;
                                                                                });
                                                                            }else{
                                                                                 if(anshiddentlengthcount==anshiddentlength){
                                                                                                console.log("Reeturned from tbl_answers  : modules increased 2");
                                                                                                Registration.modulesSaved++;
                                                                                        }
                                                                                        anshiddentlengthcount++;
                                                                            }
                                                                           
                                                                            if(json.specialcase==1){
                                                                                    var userdetails = $.jStorage.get('userdetails');
                                                                                    userdetails.specialAnswers=userdetails.specialAnswers+","+app_answer_id;

                                                                                    userdetails.tokenkey=$.jStorage.get('bip_jwt');
                                                                                    
                                                                                    $.jStorage.set('userdetails',userdetails);
                                                                                    var toupdate={
                                                                                                where:{
                                                                                                    'user_id': userdetails.user_id,
                                                                                                },
                                                                                                fields:{
                                                                                                      'specialAnswers':userdetails.specialAnswers
                                                                                                      
                                                                                                 }
                                                                                            };
                                                                                    sqlhelper.updateData('tbl_user',toupdate);
                                                                        


                                                                            }
                                                               

                                                                });

                                                        });

                                                    });

                                              
                                            
                                    });
                                });
                            } else{
                                if(anshiddentlengthcount==anshiddentlength){
                                 console.log("Reeturned from tbl_answers  : modules increased 2");
                                Registration.modulesSaved++;
                            }
                            anshiddentlengthcount++;
                            }
                        }
                        catch(err) {
                           if(anshiddentlengthcount==anshiddentlength){
                                 console.log("Reeturned from tbl_answers  : modules increased 2");
                                Registration.modulesSaved++;
                            }
                            anshiddentlengthcount++;
                        }
                        
                    }else{
                           if(anshiddentlengthcount==anshiddentlength){
                                 console.log("Reeturned from tbl_answers  : modules increased 2");
                                Registration.modulesSaved++;
                            }
                            anshiddentlengthcount++;
                    }
            });
        }else{
             console.log("Reeturned from tbl_answers  : modules increased 2");
            Registration.modulesSaved++;
        }
        // $cat_arr=[];
        console.log("Inserting Custom answers with categories");
        if($('.answer_cat').length>0){
                var totalanscat=$('.answer_cat').length;
                var nowtotal=1;
                $('.answer_cat').each(function(){
                        self=$(this);
                        var data=$.parseJSON($(this).val());

                        sqlhelper.db.transaction(function(tx) {
                                tx.executeSql("SELECT MAX(sort_order) as cat_sort_order FROM tbl_answer_category;", [], function(txs,resultss){
                                        app_lastid=resultss.rows.item(0).app_answer_cat_id;
                                        var dataa={
                                            'answer_cat_id':0,
                                            'answer_cat_name':data.custom_answer_cat,
                                            'step_id':data.step_id,
                                            'added_date':moment().format("YYYY-MM-DD"),
                                            'last_updated':moment().format("YYYY-MM-DD"),
                                            'answer_cat_status':1,
                                            'sort_order':app_lastid,
                                            'answer_type':'custom',
                                            'mapp_cat_id':0,
                                        }; 
                                        
                                        sqlhelper.insertJSONData('tbl_answer_category',dataa,function(){

                                            sqlhelper.db.transaction(function(tx) {
                                                tx.executeSql("SELECT MAX(app_answer_cat_id) as app_answer_cat_id FROM tbl_answer_category where answer_cat_name like '%"+data.custom_answer_cat+"%';", [], function(txs,resultss){   
                                                     lastid=resultss.rows.item(0).app_answer_cat_id;
                                                     console.log("Last inserted category is "+lastid);
                                                    sqlhelper.db.transaction(function(tx) {
                                                        tx.executeSql("SELECT MAX(sort_order) as sort_id FROM tbl_answers;", [], function(txs,results){
                                                           lastsort=results.rows.item(0).sort_id;
                                                           $tempLength = self.parent('div').find('.answer-for-cat-'+data.temp_id+' input').length
                                                           if($tempLength>0){
                                                                self.parent('div').find('.answer-for-cat-'+data.temp_id+' input').each(function(){
                                                                  $self_input=$(this);
                                                                    var json=$(this).val();
                                                                    if(json!=""){
                                                                        try {
                                                                              json=$.parseJSON(json);
                                                                                if(json.is_custom_answer==1){
                                                                                        var data={
                                                                                            'answer_id':0,
                                                                                            'answer':json.custom_answer,
                                                                                            'step_id':json.step_id,
                                                                                            'app_answer_cat_id':lastid,
                                                                                            'added_date':moment().format("YYYY-MM-DD"),
                                                                                            'last_updated':moment().format("YYYY-MM-DD"),
                                                                                            'answer_status':1,
                                                                                            'sort_order':lastsort+1,
                                                                                            'answer_type':'custom',
                                                                                            'mapped_answer_id':0,
                                                                                            'special_answer':0
                                                                                        };

                                                                                        sqlhelper.insertJSONData('tbl_answers',data,function(){
                                                                                                sqlhelper.db.transaction(function(tx) {
                                                                                                     tx.executeSql("SELECT MAX(app_answer_id) as app_answer_id FROM tbl_answers where answer like '%"+json.custom_answer+"%';", [], function(txs,results){
                                                                                                            app_answer_id=results.rows.item(0).app_answer_id;
                                                                                                             if(json.is_checked==true){
                                                                                                                    var data={
                                                                                                                          'assignment_details_id':0,
                                                                                                                          'app_assignment_id':new_app_assignment_id,
                                                                                                                          'assignment_id':assignment_id,
                                                                                                                          'registration_id':regid,
                                                                                                                          'flow_id':0,
                                                                                                                          'step_id':json.step_id,
                                                                                                                          'answer_id':0,
                                                                                                                          'app_answer_id':app_answer_id,
                                                                                                                          'assignment_code':0
                                                                                                                      };
                                                                                                                      sqlhelper.insertJSONData('tbl_patient_assignment_details',data,function(){
                                                                                                                         if(nowtotal==totalanscat){
                                                                                                                             console.log("Reeturned from tbl_patient_assignment_details  : modules increased 3");
                                                                                                                             Registration.modulesSaved++;
                                                                                                                            // msgBox("Registration saved successfully");
                                                                                                                            //  if(navigator.onLine &&  offlinehelper.syncstarted==false)
                                                                                                                            //     offlinehelper.prepareForSync();
                                                                                                                            // changepage("RegistrationTask_List");
                                                                                                                        }
                                                                                                                        nowtotal++;
                                                                                                                      });
                                                                                                              }
                                                                                                    });
                                                                                                });

                                                                                        });
                                                                                       
                                                                                }
                                                                        }
                                                                        catch(err) {
                                                                            if(nowtotal==totalanscat){
                                                                                             console.log("Reeturned from tbl_patient_assignment_details  : modules increased 3");
                                                                                             Registration.modulesSaved++;
                                                                                            // msgBox("Registration saved successfully");
                                                                                            //  if(navigator.onLine &&  offlinehelper.syncstarted==false)
                                                                                            //     offlinehelper.prepareForSync();
                                                                                            // changepage("RegistrationTask_List");
                                                                                        }
                                                                                        nowtotal++;
                                                                        }
                                                                      
                                                                    }else{
                                                                         if(nowtotal==totalanscat){
                                                                                             console.log("Reeturned from tbl_patient_assignment_details  : modules increased 3");
                                                                                             Registration.modulesSaved++;
                                                                                            // msgBox("Registration saved successfully");
                                                                                            //  if(navigator.onLine &&  offlinehelper.syncstarted==false)
                                                                                            //     offlinehelper.prepareForSync();
                                                                                            // changepage("RegistrationTask_List");
                                                                                        }
                                                                                        nowtotal++;
                                                                    }

                                                                    
                                                                });
                                                            }else{
                                                                Registration.modulesSaved++;
                                                            }
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                });
                        });

                        // $cat_arr.push(dataa);

                 });
            }else{
                 Registration.modulesSaved++;
                  console.log("Reeturned from tbl_patient_assignment_details  : modules increased 3");
                 // if(navigator.onLine &&  offlinehelper.syncstarted==false && offlinehelper.savedans==true)
                 //        offlinehelper.prepareForSync();
                 //    msgBox("Registration saved successfully");
                 //    changepage("RegistrationTask_List");
            }




// callWebService('saveRegistration', json, function(response) {
//     if (response.status == "ok") {
//         if (response.data == "success") {
//             $(".save-nav-holder").addClass("hide");
//             msgBox("Registration saved successfully");
//             changepage("RegistrationTask_List");
//             Registration.fetchRegistrations();
//         } else {
//             msgBox("Error in saving registration");
//         }
//     } else {
//         msgBox(MESSAGE.NO_INTERNET);
//     }
// });
},

    updateCustomAnswers: function(){
        
        $customselector = $("input[data-customoption='true']");
        $customselector.each(function(){
             if($(this).attr("type")=="radio"){

             }
             $hiddenF = $(this).prev("label").find("span.ui-btn-text").find("input[type='hidden']");
             $hiddenValue = $hiddenF.val();
             $valObject = $.parseJSON($hiddenValue);
             if($(this).prop("checked")===true){
                $valObject.is_checked = true;
             }else{
                $valObject.is_checked = false;
             }

             $newVal = JSON.stringify($valObject);
             $hiddenF.val($newVal);
        });
    },

    /**
     * Method to stop youtube video when page is navigated away
     */
    StopYoutubeVideo: function(){
        var element = document;
        var iframe = element.querySelector( 'iframe');
        var video = element.querySelector( 'video' );
        if ( iframe ) {
            var iframeSrc = iframe.src;
            iframe.src = iframeSrc;
        }
        if ( video ) {
            video.pause();
        }
    },

    preCheckOldRegistrationAnswers: function(ans){

      /*  $("input[type='checkbox'], input[type='radio']").each(function(){
            $answerid = $(this).attr("data-answerid");
            if($.inArray(parseInt($answerid),ans)!=-1){
                $(this).prop("checked",true);
            }
        });*/

        var i;
        for (i = 0; i < ans.length; ++i) {
            $rawans = ans[i];
            $splitem = $rawans.split(":");
            $step_id = $splitem[0];
            $answer_id = $splitem[1];
            $("#step_"+$step_id).find("input[data-answerid='"+$answer_id+"']").prop("checked",true);
        }
       // Registration.refreshControls();
    },

    formatTime: function(strTime){
        //12:53 PM
        $sepAmPm = strTime.split(" ");
        $ap = $sepAmPm[1];
        $time = $sepAmPm[0];
        $splitTime = $time.split(":");
        $hour = $splitTime[0];
        
    },

    convertDateTo24Hour: function(date){
        var elem = date.split(" ");
        var stSplit = elem[0].split(":");// alert(stSplit);
        var stHour = stSplit[0];
        var stMin = stSplit[1];
        var stAmPm = elem[1].toUpperCase();
        var newhr = 0;
        var ampm = '';
        var newtime = '';
      
        if (stAmPm=='PM')
        { 
            if (stHour!=12)
            {
                stHour=stHour*1+12;
            }
           
        }else if(stAmPm=='AM' && stHour=='12'){
           stHour = stHour -12;
        }else{
            stHour=stHour;
          }
        if(arguments.length==2){
           // return stHour+':'+stMin;
            return "Kl "+stHour;
        }else{
            return stHour+':'+stMin+":00";
        }
    },
    resetControls: function(){
        $("#frmRegistrationSteps").find("input[type='radio'], input[type='checkbox']").each(function(){
                $(this).prop("checked", false);
                $(this).removeAttr("checked");
        })

        //Registration.refreshControls();
    },
    resetExpandCollapseAnswer: function(){ //method to reset selected answer for expand collapse if user can select only one option..
            $container = $("#frmRegistrationSteps").find(".registration-steps-holder").find("div[data-template='steps_expand_collapse']");
            $container.find("input[type='radio']").each(function(){
                    $(this).prop("checked", false);
                    $(this).removeAttr("checked");
            })
    },
    eventOptOnClick: function(obj){
        //alert("click click");
        $isCustomAnswer = obj.attr("data-customoption");
        $id = "step_" + obj.attr("data-stepid");

        $answerCatID = obj.attr("data-answercatid");



        
        $rawoptions = obj.attr("data-options");
       
        $options = $rawoptions.split("-");
        $is_multiple_choice = $options[0];
        $max_selection_allowed = $options[1]; 
        if ($answerCatID > 0) {
            if ($is_multiple_choice == 1) {
                $chkdlen = $("#" + $id).find("#collapsible_" + $answerCatID).find(".expand_collapse_answer:checked").size();
                if ($chkdlen > $max_selection_allowed) {
                    obj.prop("checked", false);
                    return false;
                }
            }else{

                //in single choice, allow only to use one option irrespective of the category
                $("#" + $id).find("[data-role='collapsible']").find(".expand_collapse_answer").prop("checked",false);
                obj.prop("checked",true);
                Registration.refreshControls();
            }

            $("#"+$id).find(".ui-collapsible-set").find(".ui-collapsible").each(function(){
                var o = $(this);
                var l = o.find(".expand_collapse_answer:checked").size();
                if(l>0){
                    $(this).attr("data-isdisabled","yes");
                }else{
                    $(this).attr("data-isdisabled","no");
                }
            })

            //now if options in category is checked, don't allow use collapse it
            obj.parents(".ui-collapsible-content").prev("h3").find("a.ui-collapsible-heading-toggle").on("click", function(){
                /*var l = obj.parents(".ui-collapsible-content").find(".ui-collapsible-content").find(".expand_collapse_answer:checked")
                var t = $("#" + $id).find("#collapsible_" + $answerCatID).find(".expand_collapse_answer:checked").size();
                if(t>0){
                    return false;
                }else{
                    return true;
                }*/
              //  $chk =;
                var c =   $(this).parent("h3").parent(".ui-collapsible").attr("data-isdisabled");
                if(c=="yes"){
                    return false;
                }else{
                    return true;
                }
            })

        } else {
            $name = obj.hasClass("sentence_answer") ? "sentence" : "keyword";
            if ($is_multiple_choice == 1) {
                $chkdlen = $("#" + $id).find("." + $name + "_answer:checked").size();
                if ($chkdlen > $max_selection_allowed) {
                    obj.prop("checked", false);
                    return false;
                }
            }
        }

        //enable disable delete custom answer button
        $mainContainer = $("#frmRegistrationSteps").find("#"+$id);
        if($answerCatID >0){ //Expand collapse
            $customcheckedItem = $mainContainer.find("#collapsible_" + $answerCatID).find("[data-customoption='true']:checked").length;
        }else{
            $customcheckedItem = $mainContainer.find("[data-customoption='true']:checked").length;
        }
        
        console.warn("count custom = "+$customcheckedItem);
        
        if($customcheckedItem>0){
            $mainContainer.find(".delete-options").removeClass("disabled");
        }else{
            $mainContainer.find(".delete-options").addClass("disabled");
        }
    },
    localizeMonth: function(strDate){
      
        var stDate = strDate.toLowerCase();
        stDate  = stDate.replace("idag","Idag");

        stDate  = stDate.replace("january","januari");
        stDate  = stDate.replace("february","februari");
        stDate  = stDate.replace("march","mars");
        stDate  = stDate.replace("may","maj");
        stDate  = stDate.replace("june","juni");
        stDate  = stDate.replace("july","juli");
        stDate  = stDate.replace("august","augusti");
        stDate  = stDate.replace("october","oktober");

        return stDate;

    },
    monthToEnglish: function(strDate){
       /* januari, februari, mars, april, maj, juni, juli, augusti,
september, oktober, november, december*/
        var stDate = strDate.toLowerCase();
        stDate  = stDate.replace("idag","Idag");

        stDate  = stDate.replace("januari","january");
        stDate  = stDate.replace("februari","february");
        stDate  = stDate.replace("mars","march");
        stDate  = stDate.replace("maj","may");
        stDate  = stDate.replace("juni","june");
        stDate  = stDate.replace("juli","july");
        stDate  = stDate.replace("augusti","august");
        stDate  = stDate.replace("oktober","october");

        return stDate;

    },
    elementClick: function(){
        $(document).on("mousedown touchstart","[data-activeclass]", function(e){
            var obj = $(this);
            var newClass = obj.attr("data-activeclass");
            obj.addClass(newClass);
            
            var b= setTimeout(function(){
                obj.removeClass(newClass);
                console.warn("hello class removed - "+newClass);
                clearTimeout(b);
            },200);
        })
    }


};
