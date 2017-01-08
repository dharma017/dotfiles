//BIP homeworks and crisisplan module
//Test
var Homeworks = {
	fetchHomeworks: function(){
       $mainPage = "MyHomework_List";
		var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '"}';
        callWebService('fetchHomeworks', json, function(response) {
           
            if (response.status == "ok") {
                if (response.data != "No Homeworks") {
                    
                    $html = "";
                    
                    $(response.data).each(function() {
                    	$publish_date = "<div class='published-date'>"+this.published_date+"</div>";
                    	if(this.already_viewed==0){
                    		$newitem = "<span><h1 class='red-dot'>&nbsp;</h1></span>";
                    	}else{
                    		$newitem = "";
                    	}
                       
                       /* $html += "<li class=''>" +
                            "<a href='javascript:void(0);' data-viewed='"+this.already_viewed+"' data-headline='"+this.headline+"' onClick='Homeworks.showHomeworkDetails($(this))' data-assignmentid='"+this.assignment_id+"'>" + this.headline +$newitem+ $publish_date +"</a>" + 
                            "<div class='contents' style='display:none;'>"+this.contents+"</div></li>";*/
                        $html += "<li class=''>"+
                            "<a href='javascript:void(0);'  data-viewed='"+this.already_viewed+"' data-headline='"+this.headline+"' onClick='Homeworks.showHomeworkDetails($(this))' data-assignmentid='"+this.assignment_id+"' class='my-list-icon v2-sub-links'>"+$newitem+this.headline+$publish_date+"</a>"+
                            "<div class='contents' style='display:none;'>"+this.contents+"</div></li>";
                    });
                    $("#"+$mainPage).find(".list_homeworks").html($html);
                    $("#"+$mainPage).find(".list_homeworks").listview('refresh');
                   
                    changepage($mainPage);
                    
                    enableIScroll();
                    refreshScroll($mainPage);

                }
            } else {
                msgBox(MESSAGE.NO_INTERNET);
            }
        });
	},

	showHomeworkDetails: function(obj){
		$mainPage = "MyHomework_Contents";
		var userdetails = $.jStorage.get('userdetails');
		var ass_id = obj.attr("data-assignmentid");
		obj.next(".contents").find("p").removeClass("ui-li-desc");
		var contents = obj.next(".contents").html();
		var headline = obj.attr("data-headline");
		var already_viewed = obj.attr("data-viewed");

		if(already_viewed==0)
        {
            $counter = $("#v2-my-homework").find(".red-counter");
            $newcount = parseInt($counter.html())-1;
            obj.attr("data-viewed","1");
            obj.find(".red-dot").remove();

            if($newcount<=0){
                $newcount = 0;
                $counter.html($newcount).hide();
            }else{
                $counter.html($newcount);
            }
            if($newcount>0){
                $counter.show();
            }else{
                $counter.hide();   
            }

            

        	var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","assignmentId":"' + ass_id + '"}';
	        callWebService('markHomeworkRead', json, function(response) {
	            if (response.status == "ok") {
	               
	                    
	                    $("#"+$mainPage).find(".hw-heading").html(headline);
	                    $("#"+$mainPage).find(".hw-contents").html(contents);
	                   
	                    changepage($mainPage);
	                    
	                    enableIScroll();
	                    refreshScroll($mainPage);

	            } else {
	                msgBox(MESSAGE.NO_INTERNET);
	            }
	        });
        }else{
        	$("#"+$mainPage).find(".hw-heading").html(headline);
            $("#"+$mainPage).find(".hw-contents").html(contents);
            changepage($mainPage);
           	enableIScroll();
            refreshScroll($mainPage);
        }
	}
};


var Crisisplans = {
    fetchCrisisplans: function(){
         $mainPage = "MyCrisisplan_List";
        var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '"}';
        callWebService('fetchCrisisplans', json, function(response) {
            if (response.status == "ok") {
                if (response.data != "No Crisis Plans") {
                    
                    $html = "";
                    
                    $(response.data).each(function() {
                       // $publish_date = "<div class='published-date'>"+this.published_date+"</div>";
                        if(this.already_read==0){
                            $newitem = "<span><h1 class='red-dot'>&nbsp;</h1></span>";
                        }else{
                            $newitem = "";
                        }
                        var data = {
                            headline: this.headline,
                            viewed: this.already_read,
                            planid: this.plan_id,
                            contents: this.contents
                        }
                        Crisisplans.showCrisisplanDetails(data,1)
                      
                     /*   $html += "<li class=''>"+
                            "<a href='javascript:void(0);'  data-viewed='"+this.already_read+"' data-headline='"+this.headline+"' onClick='Crisisplans.showCrisisplanDetails($(this))' data-planid='"+this.plan_id+"' class='my-list-icon v2-sub-links'>"+$newitem+this.headline+"</a>"+
                            "<div class='contents' style='display:none;'>"+this.contents+"</div></li>";*/
                    });
                   /* $("#"+$mainPage).find(".list_crisisplans").html($html);
                    $("#"+$mainPage).find(".list_crisisplans").listview('refresh');
                   
                    changepage($mainPage);
                    
                    enableIScroll();
                    refreshScroll($mainPage);*/

                }
            } else {
                msgBox(MESSAGE.NO_INTERNET);
            }
        });
    },
    showCrisisplanDetails: function(obj){
        $mainPage = "MyCrisisplan_Contents";
        var userdetails = $.jStorage.get('userdetails');
       /* var plan_id = obj.attr("data-planid");
        obj.next(".contents").find("p").removeClass("ui-li-desc");
        var contents = obj.next(".contents").html();
        var headline = obj.attr("data-headline");
        var already_viewed = obj.attr("data-viewed");*/
         var plan_id = obj.planid;
      //  obj.next(".contents").find("p").removeClass("ui-li-desc");
        var contents = obj.contents;
        var headline = obj.headline;
        var already_viewed = obj.viewed;

        if(already_viewed==0)
        {
            /*$counter = $("#v2-my-crisisplan").find(".red-counter");
            $newcount = parseInt($counter.html())-1;
            obj.attr("data-viewed","1");
            obj.find(".red-dot").remove();

            if($newcount<=0){
                $newcount = 0;
                $counter.html($newcount).hide();
            }else{
                $counter.html($newcount);
            }*/

            
            var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","planId":"' + plan_id + '"}';
            callWebService('markCrisisplanRead', json, function(response) {
                if (response.status == "ok") {
                   
                        
                        $("#"+$mainPage).find(".cp-heading").html(headline);
                        $("#"+$mainPage).find(".cp-contents").html(contents);
                       
                        changepage($mainPage);
                        
                        enableIScroll();
                        refreshScroll($mainPage);

                } else {
                    msgBox(MESSAGE.NO_INTERNET);
                }
            });
        }else{
            $("#"+$mainPage).find(".cp-heading").html(headline);
            $("#"+$mainPage).find(".cp-contents").html(contents);
            changepage($mainPage);
            enableIScroll();
            refreshScroll($mainPage);
        }
    }
};


var BipAppVersion2 = {
	 renderStartPage: function(res){
        res.homeworks=$.parseJSON(res.homeworks);
        res.crisisplans=$.parseJSON(res.crisisplans);
        if(typeof res.homeworks.total_homeworks!="undefined" && parseInt(res.homeworks.total_homeworks)>0){
            $("#v2-my-homework").removeClass("desaturate");
            $("#v2-my-homework").attr("onclick","Homeworks.fetchHomeworks()");
        }else{
            $("#v2-my-homework").addClass("desaturate");
            $("#v2-my-homework").removeAttr("onclick");
        }

        if(typeof res.crisisplans.total_crisis_plans!="undefined" && parseInt(res.crisisplans.total_crisis_plans)>0){
            $("#v2-my-crisisplan").removeClass("desaturate");
            $("#v2-my-crisisplan").attr("onclick","Crisisplans.fetchCrisisplans()");
        }else{
            $("#v2-my-crisisplan").addClass("desaturate");
            $("#v2-my-crisisplan").removeAttr("onclick");
        }

        //show notifications
        if(typeof res.homeworks.new_homeworks!="undefined" && res.homeworks.new_homeworks>0){
            $("#v2-my-homework").find(".red-counter").html(res.homeworks.new_homeworks).show();
        }else{
            $("#v2-my-homework").find(".red-counter").html(0).hide();
        }

        if(res.hasRegistration=="false"){
            $(".regtaskicon").addClass("desaturate");
            $(".regtaskicon").attr("onclick","return false;");
        }else{
            $(".regtaskicon").removeClass("desaturate");
            $(".regtaskicon").attr("onclick","Registration.fetchRegistrations();");
        }

        if(typeof res.crisisplans.new_crisis_plans!="undefined" && res.crisisplans.new_crisis_plans>0){
            $("#v2-my-crisisplan").find(".red-counter").html(res.crisisplans.new_crisis_plans).show();
        }else{
            $("#v2-my-crisisplan").find(".red-counter").html(0).hide();
        }
    },

    ShowHideModules: function(){

        //SHOW HIDE REGISTRATION BUTTON
        if(offlinehelper.ShowHideModules("registration")==1 || offlinehelper.ShowHideModules("registration")=="all"){
            $("#RegistrationTask").find(".registrera-btn").removeClass("hide");
            $("#RegistrationTask").find(".registrera-btn").parent("div.scroller").css("margin-top","");
        }else{
            $("#RegistrationTask").find(".registrera-btn").addClass("hide");
            $("#RegistrationTask").find(".registrera-btn").parent("div.scroller").css("margin-top","54px");
        }

        //SHOW HIDE HOMEWORKS
        if(offlinehelper.ShowHideModules("homework_module")==1 || offlinehelper.ShowHideModules("homework_module")=="all"){
            $("#li-v2-homeworks").removeClass("hide");
            $array_hw = offlinehelper.ShowHideModules("homework_id");
            if($array_hw.length==0 && offlinehelper.ShowHideModules("homework_module")!="all"){
                $("#li-v2-homeworks").addClass("hide");
            }
        }else{
            $("#li-v2-homeworks").addClass("hide");
        }

        //SHOW HIDE CRISIS PLANS
        if(offlinehelper.ShowHideModules("crisis_plan")==1 || offlinehelper.ShowHideModules("crisis_plan")=="all"){
             $("#li-v2-crisisplan").removeClass("hide");
        }else{
            $("#li-v2-crisisplan").addClass("hide");
        }

        //SHOW HIDE MY SKILL MENU
        if(offlinehelper.ShowHideModules("my_skills")==1 || offlinehelper.ShowHideModules("my_skills")=="all"){
             $("#li-v2-skills").removeClass("hide");
        }else{
            $("#li-v2-skills").addClass("hide");
        }

    //if all modules are disabled just give the message
       /* if(offlinehelper.ShowHideModules("registration")==0 && (offlinehelper.ShowHideModules("homework_module")==0 || (offlinehelper.ShowHideModules("homework_module")==1 && offlinehelper.ShowHideModules("homework_id").length==0)) && offlinehelper.ShowHideModules("crisis_plan")==0 && offlinehelper.ShowHideModules("my_skills")==0){
            msgBox("No modules are enabled for you. Please contact your psychologist");
            return false;
        }*/

    }
}


var MySkills = {
    MyAudio: new Audio(),
    isPlaying: 0,
    isSeeking:false,
    audioDuration: 0,
    feelingModVersion: 1,
    feelingsSaved:0,
    exposureSteps:"",
    intervalID:0,
    modulesSaved:0,
    init: function(){
       

        $('#audio-seeker').rangeslider({

            // Feature detection the default is `true`.
            // Set this to `false` if you want to use
            // the polyfill also in Browsers which support
            // the native <input type="range"> element.
            polyfill: false,

            // Default CSS classes
            rangeClass: 'rangeslider',
            fillClass: 'rangeslider__fill',
            handleClass: 'rangeslider__handle',

            // Callback function
            onInit: function() {
                $(".rangeslider__handle").on("touchstart",function(){
                    MySkills.isSeeking = true;
                    console.log("Mouse Press");
                });

                $(".rangeslider__handle").on("touchend",function(){
                    MySkills.isSeeking = false;
                    console.log("Mouse Released");
                });
            },

            // Callback function
            onSlide: function(position, value) {},
           
            // Callback function
            onSlideEnd: function(position, value) {
                MySkills.MyAudio.currentTime = value;
                MySkills.isSeeking = false;
            }
        });
    },
    listModules: function(){
        var userdetails = $.jStorage.get("userdetails");
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '"}';
        callWebService('listModules', json, function(response) {

                if(response.status=="ok"){
                    ;
                    var totalModules = response.data.length;
                    var cssClass = "";
                    var html = "";
                    var  module_image="";
                    
                    if(totalModules>=4 && totalModules<=6){ // 2 items in each row
                       cssClass = "two-items-row";
                    }else{ //3 items in each row
                       cssClass = "three-items-row";
                    }


              

                    $(response.data).each(function(i,e){
                            var emptyModuleClass = "";
                            if(e.total_thoughts==0 && e.total_skills==0 && e.total_exposures==0 && e.module_icon!="my_feelings.png"){
                                emptyModuleClass = "desaturate";
                            }

                            if(e.module_icon=="my_feelings.png"){
                                module_image = "images/my_feelings.png";
                            }else{
                                if(isWebVersion==false){
                                    if(window.cordova){
                                        if($.trim(e.module_icon)!=""){
                                            if(filehelper.checkFileExist(cordova.file.dataDirectory + e.module_icon)){
                                                module_image = cordova.file.dataDirectory + e.module_icon;
                                            }else{
                                                module_image = "images/module_default.png";
                                            }
                                        }else{
                                            module_image = "images/module_default.png";    
                                        }
                                    }else{
                                        module_image = "images/module_default.png";
                                    }
                                }else{
                                    console.clear();
                                    console.info("URL")
                                    module_image = filehelper.getRemotePath()+"images/uploads/module_icons/"+e.module_icon;
                                }
                                
                            }

                            if(emptyModuleClass!=""){
                                html +="<div class='module-icon-holder "+emptyModuleClass+" "+cssClass+"' data-moduleid='"+e.module_id+"' data-modulename='"+e.module_name+"'>"
                            }else{
                                html +="<div class='module-icon-holder "+cssClass+"' data-activeclass='full' id='module-content-"+e.module_id+"' data-moduleid='"+e.module_id+"' data-modulename='"+e.module_name+"' onClick='MySkills.listModuleSkillTypes($(this))'>"
                            }

                            html += "<div class='module-icon'><img src='"+module_image+"' width='79'/></div>"+
                                        "<div class='module-name'>"+e.module_name+"</div>"+
                                    "</div>";
                            
                    });
                    

                    $("#MySkills_Module").find(".module-icons-stuffs").html(html);
                    
                    changepage("MySkills_Module");
                    enableIScroll();
                    //refreshScroll("MySkills_Module");

                   var b= setTimeout(function(){
                        scrolls["MySkills_Module"].refresh();
                        clearTimeout(b);
                   },200);

                }
        });
    },

    listModuleSkillTypes: function(obj){
        var moduleID    = obj.attr("data-moduleid"),
            moduleName  = obj.attr("data-modulename"),
            userdetails = $.jStorage.get("userdetails"),
            html = "", 
            itms = 0;
       
        $("#MyModule_AvailSkills").find(".page-title-v2").html(moduleName);

        if(moduleID>0){
            var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '", "moduleId":"'+moduleID+'"}';

            callWebService('checkIfModuleHasSkills', json, function(response) {
                    
                    if(response.status == "ok"){
                            $total_thoughts = response.data.total_thoughts;
                            $total_skills = response.data.total_skills;
                            $total_exposures = response.data.total_exposures;
                            var directSkills = 0;

                            if($total_thoughts>0 && $total_skills==0 && $total_exposures==0){
                                $data = {
                                    "module_name": moduleName,
                                    "module_id": moduleID,
                                    "skills_type": "thoughts"
                                }
                                directSkills = 1;
                            }else if($total_thoughts==0 && $total_skills>0 && $total_exposures==0){
                                $data = {
                                    "module_name": moduleName,
                                    "module_id": moduleID,
                                    "skills_type": "skills"
                                }
                                directSkills = 1;
                            }else if($total_thoughts==0 && $total_skills==0 && $total_exposures>0){
                                $data = {
                                    "module_name": moduleName,
                                    "module_id": moduleID,
                                    "skills_type": "exposure"
                                }
                                directSkills = 1;
                            }

                            if(directSkills==1){
                                MySkills.listSkills($data,1);
                            }else{
                                if($total_thoughts>0){
                                    html += "<li class=''><a href='javascript:void(0);' onclick='MySkills.listSkills($(this))' data-modulename='"+moduleName+"' data-moduleid='"+moduleID+"' data-type='thoughts'  class='my-new-list thought-type v2-sub-links'>Thoughts</a></li>";
                                }

                                if($total_skills>0){
                                    html += "<li class=''><a id='skills-module' href='javascript:void(0);' onclick='MySkills.listSkills($(this))' data-modulename='"+moduleName+"' data-moduleid='"+moduleID+"' data-type='skills'  class='my-new-list skill-type v2-sub-links'>Skills</a></li>";
                                }

                                if($total_exposures>0){
                                    html += "<li class=''><a id='exposure-module' href='javascript:void(0);' onclick='MySkills.listSkills($(this))' data-modulename='"+moduleName+"' data-moduleid='"+moduleID+"' data-type='exposure'  class='my-new-list exposure-type v2-sub-links'>Exponera</a></li>";
                                }
                                $("#MyModule_AvailSkills").find(".list-module-available-skills").html(html).show();
                                $("#MyModule_AvailSkills").find(".skills-container-feelings").hide();
                                $("#MyModule_AvailSkills").find(".list-module-available-skills").listview('refresh');
                                changepage("MyModule_AvailSkills");
                                enableIScroll();
                                refreshScroll("MyModule_AvailSkills");
                            }
                            
                    }
            });
        }else{
            callWebService('feelingStatistics', json, function(response) {

                if(response.status == "ok"){
                    
                    MySkills.feelingModVersion = offlinehelper.ShowHideModules("my_feelings");

                    if(MySkills.feelingModVersion==1){
                        html +="<div class='text-step-button feelings-btn-holder'><a href='javascript:void(0)' onclick='MySkills.showFeelingDetails()' class='proceed-to-feelings inline-blocked border-radius ui-btn ui-btn-up-b'  data-role='button'>Lägg till ny känsla</a></div>";
                        html +="<div class='counter-holder v1'>";
                        html +="<div class='feeling-circle times-count'><div class='counter-content c1'><div class='counter'>"+response.data.total_counts_v1+"</div><div class='text'>KÄNSLOR</div></div></div>";
                        html +="<div class='feeling-circle days-count'><div class='counter-content c1'><div class='counter'>"+response.data.total_days_v1+"</div><div class='text'>DAGAR</div></div></div>";
                        html +="</div>";
                    }else if(MySkills.feelingModVersion==2){
                        html +="<div class='text-step-button feelings-btn-holder'><a href='javascript:void(0)' onclick='MySkills.showFeelingDetails()' class='proceed-to-feelings inline-blocked border-radius ui-btn ui-btn-up-b'  data-role='button'>Lägg till ny känsla</a></div>";
                        html +="<div class='counter-holder v2'>";
                        html +="<div class='feeling-circle times-count'><div class='counter-content c1'><div class='counter'>"+response.data.total_counts_v2+"</div><div class='text'>KÄNSLOR</div></div></div>";
                        html +="<div class='feeling-circle days-count'><div class='counter-content c1'><div class='counter'>"+response.data.total_days_v2+"</div><div class='text'>DAGAR</div></div></div>";
                        html +="<div class='feeling-circle times-count'><div class='counter-content c2'><div class='counter'>"+response.data.total_primary_feelings+"</div><div class='text'>PRIMÄRA</div></div></div>";
                        html +="<div class='feeling-circle times-count'><div class='counter-content c2'><div class='counter'>"+response.data.total_secondary_feelings+"</div><div class='text'>SEKUNDÄRA</div></div></div>";
                        html +="</div>";
                    }
                    $("#MyModule_AvailSkills").find(".skills-container-feelings").html(html).show();
                    $("#MyModule_AvailSkills").find(".list-module-available-skills").hide();
                    changepage("MyModule_AvailSkills");
                    enableIScroll();
                    refreshScroll("MyModule_AvailSkills");
                }
            });
           
        }
    },
    
    showFeelingDetails: function(){
        var  userdetails = $.jStorage.get("userdetails");
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '"}';
        var getCounters;
        callWebService('feelingLists', json, function(response){
            $mainPage = "MyModule_FeelingsDetails";

            var step = 1;

            if(response.status == "ok"){
                var html = "";

              
                html +='<div class="step-one-holder my-feelings first">';
                if(MySkills.feelingModVersion==1){
                    html +='<div class="mini-links"><div class="steps-counter">Steg 1 av 2</div></div>';
                }else if(MySkills.feelingModVersion==2){
                    html +='<div class="mini-links"><div class="steps-counter">Steg 1 av 3</div></div>';
                }
                html += '<div class="registrera-btn page-title-v2">Vilken beskrivning stämmer bäst med din känsla?</div>';
                html += '<div class="options-holder">';
                html += '<div data-role="collapsibleset" data-theme="c" data-content-theme="c" data-corners="false">';
                $(response.data).each(function(i,e){
                    var feelingName = e.feeling_name;
                    feelingName = feelingName.replace("\\","");
                    html += '<div id="feelling-collapse-'+e.feeling_id+'" class="feeling-collapsible"  data-role="collapsible">'+
                            '<h3>'+feelingName+'</h3>'+
                            '<div class="feeling-description-holder"><div>'+e.description+'</div>'+
                            '<div><div class=""><label><input class="feelings-radio" type="radio" name="chk-feelings" data-parent="MyModule_FeelingsDetails" onClick="MySkills.controlExpandCollapse($(this))" data-feeling="'+e.feeling_name+'" data-mini="true" value="'+e.feeling_id+'">Välj</label></div></div>'+
                            '</div>'+
                            '</div>';
                    //html +="<div data-role='collapsible'><h3>Hello</h3><div class='contents'>This is test descriptino</div></div>";
                });
                html += "</div>";
                html += "</div>";
                html += "</div>";

                callWebService('feelingStatistics', json, function(response) {
                    if(response.status == "ok"){
                        getCounters = response.data;
                        //step 2
                        if(MySkills.feelingModVersion==1){ //Normal version of feelings
                            // var newCount = parseInt(getCounters.total_counts_v1)+1;
                             var html2="";
                             html2 +='<div class="step-two-holder  my-feelings hide last">';
                             html2 +='<div class="mini-links"><div class="steps-counter">Steg 2 av 2</div></div>';
                             html2 += '<div class="registrera-btn page-title-v2">Du har lagt till en känsla</div>';
                             html2 += '<div class="options-holder bg-white">';
                             html2 += "<div class='selected-feelings'>Jag kande ilska</div>";
                            /* html2 +="<div class='counter-holder'>";
                             html2 +="<div class='feeling-circle-add times-count'><div class='counter-content'><div class='counter'>"+newCount+"</div><div class='text'>KÄNSLOR</div></div></div>";
                             html2 +="</div>";*/
                             html2 += "</div>";
                             html2 += "</div>";
                             
                             $("#"+$mainPage).find(".feelings-save-btn").attr("data-modversion",1);
                             $("#"+$mainPage).find(".feelings-save-btn").attr("data-feelingtype",0);
                             $("#"+$mainPage).find(".feeling-steps-holder").append(html2);
                        }else if(MySkills.feelingModVersion==2){ //Primary/Secondary Version of feelings
                             
                          //  var newCount = parseInt(getCounters.total_counts_v2)+1;
                         //   var primaryCount = parseInt(getCounters.total_primary_feelings)+1;
                           // var secondaryCount = parseInt(getCounters.total_secondary_feelings)+1;
                             //step 2
                             var html2 = "";
                             html2 +='<div class="step-two-holder  my-feelings second hide">';
                             html2 +='<div class="mini-links"><div class="steps-counter">Steg 2 av 3</div></div>';
                             html2 += '<div class="registrera-btn page-title-v2">Vad tror du utlöste den här känslan?</div>';
                             html2 += '<div class="options-holder">';
                             html2 += "<div class='select-feeling-type feeling-v2-radio'>";
                             html2 += '<label><input type="radio" data-iconpos="left" name="chk-feelings-v2" data-mini="true" value="1">Min känsla stämmer med situationen som utlöste den.<br/><strong>En primär känsla</strong></label>';
                             html2 += '<label><input type="radio" data-iconpos="left" name="chk-feelings-v2" data-mini="true" value="2">Min känsla utlöstes av dömande av en primär känsla.<br/><strong>En sekundär känsla</strong></label>';
                             html2 += "</div>";
                             html2 +="<div class='definition-btn-holder white-btn'>";
                             html2 +="<a href='javascript:void(0)' onclick='MySkills.showFeelingDefinitions()' class='border-radius ui-btn ui-btn-up-b'  data-role='button'>Läs mer om primära och sekundära känslor</a>";
                             html2 +="</div>";
                             html2 += "</div>";
                             html2 += "</div>";

                             //step 3
                             var html3="";
                             html3 +='<div class="step-three-holder  my-feelings last hide">';
                             html3 +='<div class="mini-links"><div class="steps-counter">Steg 3 av 3</div></div>';
                             html3 += '<div class="registrera-btn page-title-v2">Du har lagt till en känsla</div>';
                             html3 += '<div class="options-holder bg-white">';
                             html3 += "<div class='selected-feelings'></div>";
                             html3 += "<br><div class='selected-feeling-type'></div>";
                             /*html3 +="<div class='counter-holder'>";
                             html3 +="<div class='feeling-circle-add times-count'><div class='counter-content'><div class='counter'>"+newCount+"</div><div class='text'>KÄNSLOR</div></div></div>";
                             html3 +="<div class='feeling-circle-add times-count primary-count'><div class='counter-content'><div class='counter'>"+primaryCount+"</div><div class='text'>PRIMÄRA</div></div></div>";
                             html3 +="<div class='feeling-circle-add times-count secondary-count'><div class='counter-content'><div class='counter'>"+secondaryCount+"</div><div class='text'>SEKUNDÄRA</div></div></div>";
                             html3 +="</div>";*/
                             html3 += "</div>";
                             html3 += "</div>";

                             //definition popup for primary and secondary feelings
                             var def = "";
                             def += '<div id="popupFeelingsDefinition" data-role="popup"  data-overlay-theme="a" class="ui-corner-all" data-position-to="window">'+
                                    '<div class="close-popup-holder"><a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right close-popup"></a></div>'+
                                    '<div class="def-contents"></div>'+
                                    '</div>';

                            $("#"+$mainPage).find(".feeling-steps-holder").append(html2);
                            $("#"+$mainPage).find(".feeling-steps-holder").append(html3);
                            $("#"+$mainPage).find(".feeling-steps-holder").append(def);
                            $("#popupFeelingsDefinition").popup({
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

                            $("#"+$mainPage).find(".feelings-save-btn").attr("data-modversion",2);
                            $("#"+$mainPage).find(".feelings-save-btn").attr("data-feelingtype",0);

                            Registration.refreshControls();
                        }
                    }
                });
                

                $("#"+$mainPage).find(".feeling-steps-holder").html(html);
                $("#"+$mainPage).find(".feelings-navigation-holder").removeClass("hide");
                $("#"+$mainPage).find(".feelings-save-nav-holder").addClass("hide");


                Registration.refreshControls();
                changepage("MyModule_FeelingsDetails");
                enableIScroll();
                refreshScroll("MyModule_FeelingsDetails");
                scrolls["MyModule_FeelingsDetails"].refresh();



                $(document).on( "collapse", ".ui-collapsible", function( event, ui ){
                    scrolls["MyModule_FeelingsDetails"].refresh();
                }).on( "expand", ".ui-collapsible", function( event, ui ){
                    scrolls["MyModule_FeelingsDetails"].refresh();
                }); 
            }
        });
    },
    showFeelingDefinitions: function(){
        var  userdetails = $.jStorage.get("userdetails");
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '"}';
        callWebService('showFeelingDefinitions', json, function(response){
            if(response.status=="ok"){
                $html = "<div class='def-heading'><h2>Primära och sekundära känslor</h2></div><div class='def-primary'>";
                $html += "<p>"+response.data.primary+"</p>";
                $html += "</div>"
                $html += "<div class='def-secondary'>";
                $html += "<p>"+response.data.secondary+"</p>";
                $html += "</div>";
                $html +="<div class='white-btn' style='margin-top:20px;'>";
                $html +="<a href='javascript:void(0)' onclick='$(\"#popupFeelingsDefinition\").popup(\"close\");' class='border-radius ui-btn ui-btn-up-b'  data-role='button'>Stäng</a>";
                $html +="</div>";
                $("#popupFeelingsDefinition").find(".def-contents").html($html);
                $("#popupFeelingsDefinition").popup("open");
            }
        });
    },
    saveFeelingAssignment: function(obj){
        MySkills.feelingsSaved = 0;
        $selectedFeeling = $("input[name='chk-feelings']:checked").val();
        var userdetails = $.jStorage.get("userdetails");
        var data = {
                        'feeling_id': $selectedFeeling,
                        'assignment_id':0,
                        'patient_id': userdetails.user_id,
                        'answered_date': moment().format("YYYY-MM-DD HH:mm:ss"),
                        'module_version':obj.attr("data-modversion"),
                        'feeling_type':obj.attr("data-feelingtype")
                    }
        sqlhelper.insertJSONData("tbl_v2_feelings_assignments",data,function(){
                msgBox("Känslospaning sparades.");
              //  $("[data-modulename='Känslospaning']").trigger("click");
                MySkills.listModules();
                offlinehelper.prepareForSync(false);
               
        });
    },
    navigateFeelingSteps: function(direction){
        var idCount = $(".my-feelings").length;
        var curItemIdx = $(".my-feelings").not(".hide").index();
        var lastBound = idCount-1;
        
        if(direction=="next"){

            $selected_feelings = $("input[name='chk-feelings']:checked").attr("data-feeling");
            if(typeof $selected_feelings!="undefined"){
                $selected_feelings = $selected_feelings.replace("\\","'");
                $selected_feelings = $selected_feelings.replace("''","'");
            }

            $(".my-feelings").find(".selected-feelings").html($selected_feelings);

            if($(".selected-feeling-type").length>0){
                  $ftv = $("input[name='chk-feelings-v2']:checked").val();
                  $selected_feelings_type = "";
                  if($ftv==1){
                    $selected_feelings_type = "En primär känsla";
                    $(".my-feelings").find(".secondary-count").hide();
                  }else if($ftv==2){
                    $selected_feelings_type = "En sekundär känsla";
                    $(".my-feelings").find(".primary-count").hide();
                  }
                $(".my-feelings").find(".selected-feeling-type").html($selected_feelings_type);

            }

            if($("input[name='chk-feelings']:checked").length==0){
                    msgBox("Vänligen välj en känsla.");
                    return false;
            }

            if(MySkills.feelingModVersion==2 && curItemIdx==1){
                if($("input[name='chk-feelings-v2']:checked").length==0)
                {
                    msgBox("Vänligen välj typ av känsla.");
                    return false;
                }

                $("#MyModule_FeelingsDetails").find(".feelings-save-btn").attr("data-feelingtype",$("input[name='chk-feelings-v2']:checked").val());
            }

            if(curItemIdx>=0 && curItemIdx<lastBound){
                $(".my-feelings").addClass("hide");
                $(".my-feelings").eq(curItemIdx+1).removeClass("hide");

                if($(".my-feelings").eq(curItemIdx+1).hasClass("last") && $(".my-feelings").eq(curItemIdx+1).attr("data-template")!="step_countdown"){
                    $("#"+$mainPage).find(".feelings-navigation-holder").addClass("hide");
                    $("#"+$mainPage).find(".feelings-save-nav-holder").removeClass("hide");
                }else{
                    $("#"+$mainPage).find(".feelings-navigation-holder").removeClass("hide");
                    $("#"+$mainPage).find(".feelings-save-nav-holder").addClass("hide");   
                }

            }



        }else{
           
            if(curItemIdx==lastBound || curItemIdx>0){
                $(".my-feelings").addClass("hide");
                $(".my-feelings").eq(curItemIdx-1).removeClass("hide");
            }else if(curItemIdx==0){
                //return  backpage('MyModule_AvailSkills',event,'slide');
                  backpage('MyModule_AvailSkills', event);
                  return;
            }
        }

       
         refreshScroll("MyModule_FeelingsDetails");

    },
    listSkills: function(){
       
        var obj = arguments[0];
        var fnArguments = arguments.length;

        if(arguments.length==2)
        {
            var skillType   = obj.skills_type,
            moduleID    = obj.module_id,
            moduleName  = obj.module_name;
            
        }else{
            var skillType   = obj.attr("data-type"),
            moduleID    = obj.attr("data-moduleid"),
            moduleName  = obj.attr("data-modulename");
        }


        var userdetails = $.jStorage.get("userdetails"),
            totalSlot = 5,
            json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '", "moduleId":"'+moduleID+'","skillType":"'+skillType+'"}'
            html = "";

        if(skillType=="exposure"){
            $mainPage = "MyModule_ExposureList";
        }else if(skillType=="skills"){
            $mainPage = "MyModule_SkillList";
        }else if(skillType=="thoughts"){
            $mainPage = "MyModule_ThoughtList";
        }

        callWebService('listSkillsItems', json, function(response) {
            if(response.status == "ok"){
                   
                   var dataLength = response.data.length;
                   
                    $(response.data).each(function(i,e){
                        if(skillType=="thoughts"){
                            if(dataLength==1){
                                    var dt = {
                                        modulename: moduleName,
                                        skillid: e.skill_id,
                                        moduleid: moduleID,
                                        type: skillType,
                                        thoughttype: e.thought_type
                                    }

                                    MySkills.skillsDetails(dt,1);
                                    return false;
                            }
                            html += "<li class=''><a href='javascript:void(0);' onclick='MySkills.skillsDetails($(this))' data-modulename='"+moduleName+"' data-skillid='"+e.skill_id+"' data-moduleid='"+moduleID+"' data-type='"+skillType+"' data-thoughttype='"+e.thought_type+"'  class='my-new-list "+e.thought_type+" v2-sub-links'>"+e.skill_name+"</a></li>";
                        }else if(skillType=="exposure"){
                            moduleName = e.skill_name;
                            html +="";
                        }
                    });

                    
                    var exposureDataLength = response.patientExposure.length;
                    if(exposureDataLength>0){
                      
                        $(response.patientExposure).each(function(idx,exp){
                            if(exposureDataLength==1){
                                var dtx = {
                                                type: "exposure",
                                                skillid: exp.skill_id,
                                                moduleid: exp.moduleID,
                                                exposureid: exp.exposure_id,
                                                itemname: exp.exposure_name
                                            };

                                MySkills.ShowExposurePreSteps(dtx,1);
                                return false;
                            }

                            totalSlot = MySkills.getNoOfSlotsForGreenGraph(exp.total_assignments);

                            html += "<li data-role='' data-type='exposure' data-skillid='"+exp.skill_id+"' data-moduleid='"+exp.moduleID+"' data-exposureid='"+exp.exposure_id+"' data-itemname='"+exp.exposure_name+"' onclick=''>"+
                                    "<a href='javascript:void(0)' data-type='exposure' data-skillid='"+exp.skill_id+"' data-moduleid='"+exp.moduleID+"' data-exposureid='"+exp.exposure_id+"' data-itemname='"+exp.exposure_name+"' onclick='MySkills.ShowExposurePreSteps($(this))'>"+exp.exposure_name+"</a>"+
                                    "<span class='sectioned box-size-border'>";
                            for (var i = 1; i <= totalSlot; i++) {
                                if (i <= exp.total_assignments) {
                                    html += "<span class='section green'>&nbsp;</span>";
                                } else {
                                    html += "<span class='section'>&nbsp;</span>";
                                }
                            }
                          
                            html += "</span>"+
                                "</li>";
                        });
                    }


                    var skillDataLength = response.skillStats.length;
                    if(skillDataLength>0){
                        $(response.skillStats).each(function(idx,sk){

                            if(skillDataLength==1){

                                var dts = {
                                            type: "skills",
                                            skillid: sk.skill_id,
                                            moduleid: sk.module_id,
                                            itemname:sk.skill_name
                                        };  

                                MySkills.ShowExposurePreSteps(dts,1);
                                return false;
                            }

                            totalSlot = MySkills.getNoOfSlotsForGreenGraph(sk.total_assignments);

                            html += "<li data-role='' data-type='skills' data-skillid='"+sk.skill_id+"' data-moduleid='"+sk.module_id+"'  data-itemname='"+sk.skill_name+"' onclick='MySkills.ShowExposurePreSteps($(this))'>"+
                                    "<a href='javascript:void(0)' data-type='skills' data-skillid='"+sk.skill_id+"' data-moduleid='"+sk.module_id+"' data-itemname='"+sk.skill_name+"' onclick='MySkills.ShowExposurePreSteps($(this))'>"+sk.skill_name+"</a>"+
                                    "<span class='sectioned box-size-border'>";
                            for (var i = 1; i <= totalSlot; i++) {
                                if (i <= sk.total_assignments) {
                                    html += "<span class='section green'>&nbsp;</span>";
                                } else {
                                    html += "<span class='section'>&nbsp;</span>";
                                }
                            }
                          
                            html += "</span>"+
                                "</li>";
                        });
                    }


                    $("#"+$mainPage).find(".page-title-v2").html(moduleName);
                    $("#"+$mainPage).find(".list-module-available-skills").html(html);
                    $("#"+$mainPage).find(".list-module-available-skills").listview('refresh');
                    changepage($mainPage);
                    enableIScroll();
                    refreshScroll($mainPage);
                    if(fnArguments==2){
                        $(".my-all-skills:visible").find("[data-rel='goback']").attr("onclick","return backpage('MySkills_Module', event);");
                    }else{
                        $(".my-all-skills:visible").find("[data-rel='goback']").attr("onclick","return backpage('MyModule_AvailSkills', event);");
                    }

            }


        });

         if(fnArguments==2){
            console.log("needed this if there is only one skill list. If there is thoughts only, we should directly go to thoughts listing page when module icon is clicked");
            return false;
        }
    },
    ShowExposurePreSteps: function(obj){

        userdetails = $.jStorage.get("userdetails"),
        $(".ui-loader").show();
        var goDirect = false;

        if(arguments.length==2){
            var moduleID = obj.moduleid,
                taskType = obj.type,
                skillID = obj.skillid;
            goDirect = true; 
        }else{
            var moduleID = obj.attr("data-moduleid"),
                taskType = obj.attr("data-type"),
                skillID = obj.attr("data-skillid");
            goDirect = false;
        }
       
 
        json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '", "moduleId":"'+moduleID+'","skillType":"'+taskType+'","skillId":"'+skillID+'"}'
      
        if(taskType=="skills"){
            callWebService("fetchSkillsSteps", json, function(response){
                MySkills.exposureSkillsSteps =  response;
                MySkills.ShowExposureSteps(obj, goDirect);
                $(".ui-loader").hide();
            });
        }else{
            callWebService("fetchExposureSteps", json, function(response){
                MySkills.exposureSkillsSteps =  response;
                MySkills.ShowExposureSteps(obj, goDirect);
                $(".ui-loader").hide();
            });
        }
        
    },
    ShowExposureSteps: function(obj, goDirect){
        console.clear();
        console.warn("RENDER STEPS");
        if(goDirect==true){ //normal object
            var moduleID = obj.moduleid,
                taskType = obj.type,
                skillID = obj.skillid,
                exposureID = obj.exposureid || "",
                itemName = obj.itemname || "";

        }else{ //html element object
            var moduleID = obj.attr("data-moduleid"),
                taskType = obj.attr("data-type"),
                skillID = obj.attr("data-skillid"),
                exposureID = obj.attr("data-exposureid"),
                itemName = obj.attr("data-itemname");
        }
        
        if(taskType=="exposure"){
            $mainPage = "MyModule_ExposureSteps";
            var formID= "frmExposureTask";
        }else{
            $mainPage = "MyModule_SkillsSteps";
            var formID= "frmSkillTask";
        }
        
        var html="";

        $("#"+$mainPage).find(".feelings-navigation-holder").removeClass("hide");
        $("#"+$mainPage).find(".feelings-save-nav-holder").addClass("hide"); 

       // return false;

        if(typeof MySkills.exposureSkillsSteps.data.steps!="undefined"){
            var steps  = MySkills.exposureSkillsSteps.data.steps;
          
            if(taskType=="exposure"){
                html += "<input type='hidden' id='exposure_id' name='exposure_id' value='"+exposureID+"'/>";
            }else{
                html += "<input type='hidden' id='skill_id' name='skill_id' value='"+skillID+"'/>";
            }
            html += "<input type='hidden' id='module_id' name='module_id' value='"+moduleID+"'/>";
            html += "<input type='hidden' id='item_name' name='item_name' value='"+itemName+"'/>";

            html +="<div class='exposure-steps-holder'>";
            var cnt = 1;
           
            $(steps).each(function(i,e){
                if(cnt==steps.length){
                    var lastclass = "last";
                }else{
                    var lastclass = "";
                }
               
                html += "<div data-hascountdown='"+e.enable_countdown+"' id='step_"+e.step_id+"' class='steps-holder "+lastclass+" hide' data-sortorder='"+e.sort_order+"' data-template='"+e.template+"'>";
                html +='<div class="mini-links"><div class="steps-counter">Steg '+cnt+' av '+steps.length+'</div></div>';
                if(e.template!="step_confirmation" && e.template!="step_checking_task"){
                    html += "<div class='regular-title-holder'>";
                    html += "<div class='step-title'>"+e.step_name+"</div>";
                        if($.trim(e.alternate_text)!=""){
                            html += "<div class='step-sub-title' style='text-align:center'>"+e.alternate_text+"</div>";   
                        }
                    html +="</div>";
                }

                html += "<div class='countdown-step2-title-holder hide'>";
                    html += "<div class='step-title'>"+e.countdown_title+"</div>";
                    if($.trim(e.countdown_desc)!=""){
                        html += "<div class='step-sub-title' style='text-align:center'>"+e.countdown_desc+"</div>";   
                    }
                html +="</div>";



                if(e.template=="step_graph"){
                    html +="<div class='graph-text-holder'>"
                    html +="<div class='graph-text'>10 = "+e.step_label_10+"</div>";
                    html +="<div class='graph-text'>0 = "+e.step_label_0+"</div>";
                    html +="</div>";
                    html +="<div class='rangeslider' id='rangeslider-exposure'>"+
                            "<input type='range' name='graph-rating' id='slider-fill-exp' value='0' min='0' max='10' data-highlight='true' sliderorientation='verticalInverted' step='1' />"+
                            "<span class='slider-fill-span' id='slider-fill-exp-span'></span>"+
                            "</div>";
                }else if(e.template=="step_text"){
                    html +="<div class='html-contents text-contents'>";
                    html += e.answer_text;
                    html +="</div>";
                }else if(e.template=="step_ec_words" || e.template=="step_ec_sentences"){
                    $is_multiple_choice = e.is_multiple_choice;
                    $max_selection_allowed = e.max_selection_allowed;
                    $dataopt = $is_multiple_choice + "-" + $max_selection_allowed;

                     if(e.enable_countdown==1 && e.is_multiple_choice==0){
                        html +="<div id='countdown-step-"+e.step_id+"' data-refid='step_"+e.step_id+"' class='countdown-step-show hide'>";
                            html += "<div class='countdown-start'>";
                                    html += "<div class='step-title' style='text-align:center'>"
                                    html += e.cntdown_start_title;
                                    html += "</div>";
                                    html += "<div class='step-sub-title' style='text-align:center'>";
                                    html += e.cntdown_start_desc;
                                    html += "</div>";
                            html += "</div>";
                                
                            html += "<div class='countdown-running hide'>";
                                html += "<div class='step-title' style='text-align:center'></div>";
                                html += "<div class='step-sub-title' style='text-align:center'>";
                                html += e.cntdown_countdown_desc;
                                html += "</div>";
                            html += "</div>";
                            html += "<div class='time-picker-holder'>";
                                 html += "<div class='time-picker'>";
                                    html += "<div class='time-picker-minus set-time' data-minminute='"+e.cntdown_min_minutes+"' data-maxminute='"+e.cntdown_max_minutes+"' data-stepid='"+e.step_id+"' data-dowhat='minus'></div>";
                                    html += "<div class='input-holder'>0</div>";
                                    html += "<div class='input-holder-timer hide'>00:00</div>";
                                    html += "<input type='hidden' class='countdown_time' name='countdown_time[]' value='0' /><input type='hidden' class='selected_minute' name='selected_minute[]' value='0' />";
                                    html += "<input type='hidden' class='current_time_position' name='current_time_position[]' value='00:00' />";
                                    html += "<div class='time-picker-plus set-time' data-minminute='"+e.cntdown_min_minutes+"' data-maxminute='"+e.cntdown_max_minutes+"'  data-stepid='"+e.step_id+"' data-dowhat='plus'></div>";
                                html += "</div>";
                            html += "</div>";
                            //now navigation
                            html += "<div class='countdown-navigations'>";
                            html +='<div class="fl text-step-button"><a href="javascript:void(0)" data-tasktype="'+taskType+'" data-stepid="'+e.step_id+'" data-direction="previous" class="reg-prev-step countdown-nav border-radius ui-btn ui-icon-previous" data-role="button">Tillbaka</a></div>';
                            html +='<div class="fr text-step-button"><a href="javascript:void(0)" data-tasktype="'+taskType+'" data-stepid="'+e.step_id+'" data-direction="next"  class="reg-next-step countdown-nav border-radius ui-btn  ui-icon-next" data-role="button">Nästa</a></div>';
                            html +='<div class="clear"></div>';
                            html += "</div>";
                        html +="</div>";
                    }

                    if(typeof e.category!="undefined" && e.category.length>0){
                        html += "<div class='options-holder'>";
                            html += "<div data-role='collapsibleset' data-theme='c' data-content-theme='c' data-corners='false'>";
                                $(e.category).each(function(){
                                        html +="<div class='collapsible-"+this.answer_cat_id+" feeling-collapsible' data-role='collapsible'>";
                                            html += "<h3>"+this.answer_cat_name+"</h3>";
                                            html += "<div class='option-holder "+e.template+"'>";
                                            $answer_cat_id = this.answer_cat_id;
                                            if(typeof this.answers!="undefined"){
                                                if(this.answers.length>0){
                                                    $(this.answers).each(function(){
                                                        $type = ($is_multiple_choice == 1) ? "checkbox" : "radio";
                                                        $ans = {
                                                            "step_id": e.step_id,
                                                            "is_custom_answer": 0,
                                                            "is_answer_category": 0,
                                                            "answer_id": this.answer_id,
                                                            "app_answer_id": this.app_answer_id,
                                                            "app_answer_cat_id": this.app_answer_cat_id,
                                                            "answer_cat_id": $answer_cat_id
                                                        };

                                                        html += "<div class='ec-answers answer-for-cat-" + $answer_cat_id + "'><label><input data-options='" + $dataopt + "' data-mini='true' data-stepid='" + e.step_id + "' data-answercatid='" + $answer_cat_id+ "' data-appanswercatid='" + this.app_answer_cat_id + "' data-answerid='" + this.answer_id + "' data-answer='" + this.answer + "' value='" + JSON.stringify($ans) + "' type='" + $type + "' data-parent='"+formID+"' onclick='MySkills.controlExpandCollapse($(this))'  class='expand_collapse_answer' name='step_answers["+e.step_id+"]["+$answer_cat_id+"][]'>" + this.answer + "</label></div>";
                                                    });
                                                }
                                            }
                                            html += "</div>";
                                        html += "</div>";
                                });
                            html += "</div>";  
                               
                        html += "</div>";
                    }
                }else if(e.template=="step_ec_descriptions"){
                    if(typeof e.category!="undefined" && e.category.length>0){
                        html += "<div class='options-holder'>";
                            html += "<div data-role='collapsibleset' data-theme='c' data-content-theme='c' data-corners='false'>";
                                $(e.category).each(function(){
                                        html +="<div class='collapsible-"+this.answer_cat_id+" feeling-collapsible' data-role='collapsible'>";
                                            html += "<h3>"+this.answer_cat_name+"</h3>";
                                            html += "<div class='option-holder "+e.template+"'>";
                                            $answer_cat_id = this.answer_cat_id;
                                            if(typeof this.answers!="undefined"){
                                                if(this.answers.length>0){
                                                    $answers = this.answers[0];
                                                    html +="<p>"+$answers.answer+"</p>";
                                                    $ans = {
                                                            "step_id": e.step_id,
                                                            "is_custom_answer": 0,
                                                            "is_answer_category": 0,
                                                            "answer_id": $answers.answer_id,
                                                            "app_answer_id": $answers.app_answer_id,
                                                            "app_answer_cat_id": $answers.app_answer_cat_id,
                                                            "answer_cat_id": $answer_cat_id
                                                    };

                                                    html += "<div class='answer-for-cat-" + $answer_cat_id + "'><label><input data-mini='true' data-parent='"+formID+"' onclick='MySkills.controlExpandCollapse($(this))'  data-stepid='" + e.step_id + "' data-answercatid='" + $answer_cat_id+ "' data-appanswercatid='" + $answers.app_answer_cat_id + "' data-answerid='" + $answers.answer_id + "' value='" + JSON.stringify($ans) + "' type='radio' class='expand_collapse_description'  name='check-matched-description-"+e.step_id+"'>Markera som vald</label></div>";

                                                }
                                            }
                                            html += "</div>";
                                        html += "</div>";
                                });
                            html += "</div>";  
                               
                        html += "</div>";
                    }
                }else if(e.template=="step_keywords" || e.template=="step_sentences"){
                   
                    $name = e.template=="step_keywords" ? "keyword" : "sentence";
                    
                    if(e.enable_countdown==1 && e.is_multiple_choice==0){
                        html +="<div id='countdown-step-"+e.step_id+"' data-refid='step_"+e.step_id+"' class='countdown-step-show hide'>";
                            html += "<div class='countdown-start'>";
                                    html += "<div class='step-title'>"
                                    html += e.cntdown_start_title;
                                    html += "</div>";
                                    html += "<div class='step-sub-title'>";
                                    html += e.cntdown_start_desc;
                                    html += "</div>";
                            html += "</div>";
                                
                            html += "<div class='countdown-running hide'>";
                                html += "<div class='step-title'></div>";
                                html += "<div class='step-sub-title'>";
                                html += e.cntdown_countdown_desc;
                                html += "</div>";
                            html += "</div>";
                            html += "<div class='time-picker-holder'>";
                                 html += "<div class='time-picker'>";
                                    html += "<div class='time-picker-minus set-time' data-minminute='"+e.cntdown_min_minutes+"' data-maxminute='"+e.cntdown_max_minutes+"' data-stepid='"+e.step_id+"' data-dowhat='minus'></div>";
                                    html += "<div class='input-holder'>0</div>";
                                    html += "<div class='input-holder-timer hide'>00:00</div>";
                                    html += "<input type='hidden' class='countdown_time' name='countdown_time[]' value='0' /><input type='hidden' class='selected_minute' name='selected_minute[]' value='0' />";
                                    html += "<input type='hidden' class='current_time_position' name='current_time_position[]' value='00:00' />";
                                    html += "<div class='time-picker-plus set-time' data-minminute='"+e.cntdown_min_minutes+"' data-maxminute='"+e.cntdown_max_minutes+"'  data-stepid='"+e.step_id+"' data-dowhat='plus'></div>";
                                html += "</div>";
                            html += "</div>";
                            //now navigation
                            html += "<div class='countdown-navigations'>";
                            html +='<div class="fl text-step-button"><a href="javascript:void(0)" data-tasktype="'+taskType+'" data-stepid="'+e.step_id+'" data-direction="previous" class="reg-prev-step countdown-nav border-radius ui-btn ui-icon-previous" data-role="button">Tillbaka</a></div>';
                            html +='<div class="fr text-step-button"><a href="javascript:void(0)" data-tasktype="'+taskType+'" data-stepid="'+e.step_id+'" data-direction="next"  class="reg-next-step countdown-nav border-radius ui-btn  ui-icon-next" data-role="button">Nästa</a></div>';
                            html +='<div class="clear"></div>';
                            html += "</div>";
                        html +="</div>";
                    }

                    html +="<div class='step-btn-option-holder'>";
                    if(typeof e.answers!="undefined" && e.answers.length>0){
                        $step_id = e.step_id;
                        $is_multiple_choice = e.is_multiple_choice;
                        $max_selection_allowed = e.max_selection_allowed;
                        $dataopt = $is_multiple_choice + "-" + $max_selection_allowed;

                        html += "<div class='options-holder template-"+$name+"'>";
                         $(e.answers).each(function(){
                                $type = ($is_multiple_choice == 1) ? "checkbox" : "radio";
                                $ans = {
                                        "step_id": $step_id,
                                        "is_custom_answer": 0,
                                        "is_answer_category": 0,
                                        "answer_id": this.answer_id,
                                        "app_answer_id": this.app_answer_id
                                };
                                html += "<label><input data-options='" + $dataopt + "' data-mini='true' data-stepid='" + $step_id + "' data-answerid='" + this.answer_id + "' data-answer='" + this.answer + "' value='" + JSON.stringify($ans) + "' type='" + $type + "'  class='" + $name + "_answer' name='step_answers["+$step_id+"][]'>" + this.answer + "</label>";
                         });
                         html +="</div>";
                    }
                    html +="</div>";

                }else if(e.template=="step_countdown"){
                    html += "<div class='time-picker-holder'>";
                   
                        html += "<div class='time-picker'>";
                            html += "<div class='time-picker-minus set-time' data-dowhat='minus'></div>";
                            html += "<div class='input-holder'>0</div>";
                            html += "<div class='input-holder-timer hide'>00:00</div>";
                            html += "<input type='hidden' id='countdown_time' name='countdown_time' value='0' /><input type='hidden' id='selected_minute' name='selected_minute' value='0' />";
                            html += "<input type='hidden' id='current_time_position' name='current_time_position' value='00:00' />";
                            html += "<div class='time-picker-plus set-time' data-dowhat='plus'></div>";
                        html += "</div>";
                    html += "</div>";
                }else if(e.template=="step_confirmation"){
                    html +="<div class='check-done-holder'><div class='checked-done'></div></div>";
                     html += "<div class='regular-title-holder'>";
                    html += "<div class='step-title'>"+e.step_name+"</div>";
                    if($.trim(e.alternate_text)!=""){
                        html += "<div class='step-sub-title' style='text-align:center'>"+e.alternate_text+"</div>";   
                    }
                    html +="</div>";
                }else if(e.template=="step_checking_task"){
                    html += "<div class='regular-title-holder'>";
                    $step_name = "";
                    if(e.title_same_as_skill_ex_name==1 || e.step_name==""){
                        $step_name = itemName;
                    }else{
                        $step_name = e.step_name;
                    }

                    html += "<div class='step-title'>"+$step_name+"</div>";
                    if($.trim(e.alternate_text)!=""){
                        html += "<div class='step-sub-title' style='text-align:center'>"+e.alternate_text+"</div>";   
                    }
                    html +="</div>";
                    html +="<div class='check-task-holder'><div id='check-task-template-ele' class='check-task off' data-status='off' onclick='MySkills.checkTaskDoneUndone($(this))'></div></div>";
                }


                html += "</div>";
                cnt++;
            });

            html += "</div>";
        }

        $("#"+$mainPage).find("#"+formID).find(".skill-contents").html(html);

        if(goDirect==true){
             $("#"+$mainPage).find("[data-rel='goback']").attr("onclick","return backpage('MySkills_Module', event);");
        }

        $("#"+$mainPage).find("[data-template='step_graph']").find(".step-sub-title").css("margin-bottom","10px"); //incase of step_graph there is another text so maintain margin between them
        $("#"+$mainPage).find("[data-template='step_countdown']").find(".step-sub-title").css("text-align","center"); //incase of step_countdown align another text to center

        if($("#"+$mainPage).find("#rangeslider-exposure").length>0) $("#"+$mainPage).find("#rangeslider-exposure").trigger("create"); //just create the rangeslider

        $("#"+$mainPage).find(".skill-contents").find(".steps-holder:eq(0)").removeClass("hide"); // by default we want to show first step, so remove class hide from it
       
        changepage($mainPage); // change page to Steps detail
        enableIScroll(); // enable i-scroll
        $("#slider-fill-exp").hide();
        $('#slider-fill-exp').unbind("change");
        $('#slider-fill-exp').val('0').css('bottom', sliderFillpercentage[$('#slider-fill').val()] + '%');
         $('#slider-fill-exp').change(function() {
            $("div.rangeslider div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style", "display:block");
            $("div.rangeslider div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[$(this).val()] + "%");
            if($(this).val()==0){
                $("#slider-fill-exp-span").attr("style", "display:block").html($(this).val()).css('bottom', "-7.5%");
            }else{
                $("#slider-fill-exp-span").attr("style", "display:block").html($(this).val()).css('bottom', sliderpercentage[$(this).val()] - 20 + "%");
            }

           
        });
        Registration.refreshControls(); //refresh jquery mobile widgets

        //now handling checkbox/radio click event to make sure patient doesn't select more than allowed options
         $(".sentence_answer,.keyword_answer,.expand_collapse_answer").on("click", function() {
            $answerCatID = $(this).attr("data-answercatid");
            $rawoptions = $(this).attr("data-options");

            $id = "step_" + $(this).attr("data-stepid");
            $options = $rawoptions.split("-");
            $is_multiple_choice = $options[0];
            $max_selection_allowed = $options[1];

            if ($answerCatID > 0) {
                if ($is_multiple_choice == 1) {
                    $chkdlen = $("#" + $id).find(".collapsible-" + $answerCatID).find(".expand_collapse_answer:checked").size();
                    if ($chkdlen > $max_selection_allowed) {
                        $(this).prop("checked", false);
                        return false;
                    }
                }else{
                     obj = $(this);
                     $chkdlen = $("#" + $id).find(".expand_collapse_answer").not($(this)).prop("checked",false);
                     obj.prop("checked",true);
                     $("#" + $id).find(".expand_collapse_answer").checkboxradio().checkboxradio("refresh");
                }
            } else {
                $name = $(this).hasClass("sentence_answer") ? "sentence" : "keyword";
                if ($is_multiple_choice == 1) {
                    $chkdlen = $("#" + $id).find("." + $name + "_answer:checked").size();
                    if ($chkdlen > $max_selection_allowed) {
                        $(this).prop("checked", false);
                        return false;
                    }
                }
            }

            if($is_multiple_choice == 0){ //now set countdown running screen title if the step has countdown enabled
                $countdown_running = $("#" + $id).find(".countdown-running");
                if($countdown_running.length>0){
                    $cntrl_text = $(this).prev("label").find("span.ui-btn-inner").find("span.ui-btn-text").text();
                    $countdown_running.find(".step-title").html($cntrl_text);
                }
            }
        });

       // var value = $("#slider-fill").val() || 0;

        //ranger buddy >>
       /* $('#slider-fill').change(function() {
            var newValue = $(this).val();
            $("#rangeslider-exposure div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[newValue] + "%");
        });*/
       /*  $("#rangeslider-exposure div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[value] + "%");


            $('#slider-fill').unbind("change");

            //var slide_bottom,slide2_2_bottom;
            $('#slider-fill').change(function() {
                var newValue = $(this).val();
                $("#rangeslider-exposure div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style", "display:block");
                $("#rangeslider-exposure div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").height(sliderpercentage[newValue] + "%");
                $("#slider-fill-span").attr("style", "display:none").html(newValue).css('bottom', sliderpercentage[newValue] - 20 + "%");

            });

            $('#slider-fill').val(value).css('bottom', sliderFillpercentage[value] + '%');



            $('#slider-fill').change();*/
        //ranger buddy <<

        $(".time-picker-minus,.time-picker-plus").on("click",function(){
            $main_holder = $("#countdown-step-"+$(this).attr("data-stepid"));

            $dowhat = $(this).attr("data-dowhat");
           
            $seldiv = $main_holder.find(".input-holder");
            $selinput = $main_holder.find(".countdown_time");
            $selected_minute = $main_holder.find(".selected_minute");

            $minValue = $(this).attr("data-minminute") || 1;
            $maxValue = $(this).attr("data-maxminute") || 10;

            if($dowhat=="minus"){
                $newtime = parseInt($selinput.val())-1;
                if($newtime<$minValue) $newtime = $minValue;
            }else{
                $inputVal = $selinput.val()>=$minValue ? $selinput.val() : $minValue-1;

                $newtime = parseInt($inputVal)+1;
                if($newtime>$maxValue) $newtime = $maxValue;
            }

            $seldiv.html($newtime);
            $selinput.val($newtime);
            $selected_minute.val($newtime);

        });

        $(".countdown-nav").on("click", function(){
            $stepname = "#countdown-step-"+$(this).attr("data-stepid");
            $mainholder = $("#countdown-step-"+$(this).attr("data-stepid"));
            $direction = $(this).attr("data-direction");
            $task_type = $(this).attr("data-tasktype");
            $mainstepid = $(this).attr("data-stepid");

            if($direction=="previous"){
                
                confirmBox('Vill du avbryta?', function(button) 
                {
                    if (button == 1) {
                            $mainholder.find(".countdown-navigations").find(".reg-next-step").html("Nästa");
                            clearInterval(MySkills.intervalID);
                            MySkills.intervalID = 0;
                            MySkills.MyAudio.pause();
                            $mainholder.find(".countdown_time").val(0);

                            $("#step_"+$mainstepid).find(".expand_collapse_answer:checked").parents(".ui-collapsible").trigger("expand");

                            if($mainholder.find(".input-holder").hasClass("hide")===false){ //since the countdown is not running, hide coundown window if previous is clicked
                                
                                $mainholder.addClass("hide");
                                $mainholder.find(".input-holder").html("0").removeClass("hide");
                                $mainholder.find(".time-picker-minus").removeClass("hide");
                                $mainholder.find(".time-picker-plus").removeClass("hide");
                                $mainholder.find(".countdown-start").removeClass("hide");
                                $mainholder.find(".countdown-running").addClass("hide");
                                $mainholder.find(".input-holder-timer").addClass("hide");
                            }else{ //user is currently in countdown running screen so get user back to countdown start page
                                $mainholder.find(".input-holder").html("0").removeClass("hide");
                                $mainholder.find(".time-picker-minus").removeClass("hide");
                                $mainholder.find(".time-picker-plus").removeClass("hide");
                                $mainholder.find(".countdown-start").removeClass("hide");
                                $mainholder.find(".countdown-running").addClass("hide");
                                $mainholder.find(".input-holder-timer").addClass("hide");
                            }
                    }
                });
                 
                

            }else{
                 if($(this).html()!="Klar"){
                    $countdownVal = $mainholder.find(".countdown_time").val();
                    if($countdownVal>0){
                        var timer_sound;
                        if(isWebVersion==false){
                            if(window.cordova){
                                if(filehelper.checkFileExist(cordova.file.dataDirectory + "countdown_alert.mp3")){
                                    timer_sound = cordova.file.dataDirectory + "countdown_alert.mp3?rand="+Math.random();
                                }else{
                                    timer_sound = "audios/default_alert.mp3?rand="+Math.random();
                                }
                            }else{
                                timer_sound = "audios/default_alert.mp3?rand="+Math.random();
                            }
                        }else{//web version
                            timer_sound = filehelper.getRemotePath() + "assets/sound_files/misc/default_alert.mp3?rand="+Math.random();
                        }
                        
                        MySkills.MyAudio.src = timer_sound;
                        MySkills.intervalID = 0;

                        $minute = parseInt($mainholder.find(".countdown_time").val());
                        $timer = ($minute<10 ? "0" + $minute : $minute) + ":00";
                        var timer = $minute*60-1, minutes, seconds ;
                        $mainholder.find(".input-holder-timer").html($timer);
                        $mainholder.find(".current_time_position").val($timer);
                        MySkills.intervalID = setInterval(function () {
                                minutes = parseInt(timer / 60, 10);
                                seconds = parseInt(timer % 60, 10);

                                if(minutes==0 && seconds==0){ //if counter reaches 0, then clear the interval
                                    clearInterval(MySkills.intervalID);
                                    
                                    MySkills.MyAudio.addEventListener('ended', function() {
                                        this.play();
                                    }, false);
                                    MySkills.MyAudio.play();
                                }

                                minutes = minutes < 10 ? "0" + minutes : minutes;
                                seconds = seconds < 10 ? "0" + seconds : seconds;

                                $mainholder.find(".input-holder-timer").html(minutes + ":" + seconds);
                                $mainholder.find(".current_time_position").val(minutes + ":" + seconds);

                                if (--timer < 0) {
                                    timer = $minute*60;
                                }
                        }, 1000);
                    }else{
                        msgBox("Välj tid i minuter");
                        return false;
                    }

                    $(this).html("Klar");
                    $mainholder.find(".input-holder").addClass("hide");
                    $mainholder.find(".time-picker-minus").addClass("hide");
                    $mainholder.find(".time-picker-plus").addClass("hide");
                    $mainholder.find(".countdown-start").addClass("hide");
                    $mainholder.find(".countdown-running").removeClass("hide");
                    $mainholder.find(".input-holder-timer").removeClass("hide");
                    console.warn("Next IF");
                 }else{
                    clearInterval(MySkills.intervalID);
                    MySkills.intervalID = 0;
                    MySkills.MyAudio.pause();
                    $mainholder.find(".countdown_time").val(0);
                    $mainholder.find(".input-holder").html("0").removeClass("hide");
                    $mainholder.find(".time-picker-minus").removeClass("hide");
                    $mainholder.find(".time-picker-plus").removeClass("hide");
                    $mainholder.find(".countdown-start").removeClass("hide");
                    $mainholder.find(".countdown-running").addClass("hide");
                    $mainholder.find(".input-holder-timer").addClass("hide");
                    $mainholder.addClass("hide");
                    $(this).html("Nästa");
                    MySkills.exposureNavigation('next',$task_type,1); 
                    //adding 1 as third parameter just because calling this method here we now dont want to show countdown step because its already been shown
                 }

            }
        });
    },
    checkTaskDoneUndone: function(obj){
            $st = obj.attr("data-status");
            if($st=="off"){
                obj.attr("data-status","on");
                obj.removeClass("off").addClass("on");
            }else{
                obj.attr("data-status","off");
                obj.removeClass("on").addClass("off");
            }
    },
    floatExposureKeywords: function(mainContainer) {
        $container = $("#"+mainContainer);
        var template = $container.find(".steps-holder:not(.hide)").attr("data-template");
        

        if (template == "step_keywords") {
            $(".template-keyword").find(".ui-checkbox,.ui-radio").addClass("ui-keywords");
            $(".template-keyword").after("<div class='clear'></div>");
        } else if (template == "step_ec_words") {
            $("."+template).find(".ui-checkbox,.ui-radio").addClass("ui-keywords");
            $(".ui-collapsible-set").find("div.ui-collapsible-content").after("<div class='clear'></div>");
        }
    },
    exposureNavigation: function(direction,type){
        MySkills.MyAudio.pause();
       /* MySkills.MyAudio.currentTime = 0;*/

         if(type=="exposure"){
            $mainPage = "MyModule_ExposureSteps";
            $parentPage = "MyModule_ExposureList";
        }else{ //skills
            $mainPage = "MyModule_SkillsSteps";
            $parentPage = "MyModule_SkillList";
        }

        //find current step and check if current step has checkbox/radios and check if at least one of them are checked 
        var current_step = $("#"+$mainPage).find(".exposure-steps-holder").find(".steps-holder:not(.hide)");
        var current_step_id = $("#"+$mainPage).find(".exposure-steps-holder").find(".steps-holder:not(.hide)").attr("id");
        var template = current_step.attr("data-template");

        var has_countdown = current_step.attr("data-hascountdown");
        if(has_countdown==1 && direction=="next" && arguments.length!=3){
            $pid = current_step_id.replace("step_","");
            //$countdownmain = $("#countdown-step-"+$pid).find("input[type='radio'],input[type='checkbox']")
             if((template=="step_ec_words" || template=="step_ec_sentences") && direction!="prev"){
                if ($("#"+$mainPage).find("#" + current_step_id).find(".expand_collapse_answer:checked").length == 0) {
                    msgBox("Välj minst ett alternativ");
                    return false;
                }
            }else if((template=="step_keywords" || template=="step_sentences")  && direction!="prev"){
                $name = template=="step_keywords" ? "keyword_answer" : "sentence_answer";
                if ($("#"+$mainPage).find("#" + current_step_id).find("."+$name+":checked").length == 0) {
                    msgBox("Välj minst ett alternativ");
                    return false;
                }
            }
            $("#countdown-step-"+$pid).removeClass("hide");
            
            $collapsibleset = $("#countdown-step-"+$pid).parent(".steps-holder").find(".options-holder").find("[data-role='collapsibleset']"); 

            $collapsibleset.children().trigger("collapse"); //collapse all collapsibles to avoid unnecessary scrollbar on countdown page.

           
             scrolls[$mainPage].scrollTo(0,0);
            return false;
        }
        
        if(template=="step_checking_task" && direction=="next"){
                
                $ischecked = current_step.find("#check-task-template-ele").attr("data-status");
                if($ischecked=="off"){
                    msgBox("Du måste kryssa i knappen ovan för att fortsätta");
                    return false;
                }
        }



        if(template=="step_countdown" && direction=="next" && $.trim($("#"+$mainPage).find(".exposure-navigation").find(".reg-next-step").html())!="Klar"){
            
            

            var st = $("#"+$mainPage).find("[data-template='step_countdown']").find("#countdown_time").val();

            if(st>0){ //Patient has chosen more than 0 minute so activate countdown
                var timer_sound;
                if(isWebVersion==false){
                    if(window.cordova){
                        if(filehelper.checkFileExist(cordova.file.dataDirectory + "countdown_alert.mp3")){
                            timer_sound = cordova.file.dataDirectory + "countdown_alert.mp3";
                        }else{
                            timer_sound = "audios/default_alert.mp3";
                        }
                    }else{
                        timer_sound = "audios/default_alert.mp3";
                    }
                }else{//web version
                    timer_sound = filehelper.getRemotePath() + "assets/sound_files/misc/default_alert.mp3?rand="+Math.random();
                }
                
                MySkills.MyAudio.src = timer_sound;

                $("#"+$mainPage).find(".regular-title-holder").addClass("hide");
                $("#"+$mainPage).find(".time-picker-holder").find(".set-time").addClass("hide");
                $("#"+$mainPage).find(".time-picker-holder").find(".input-holder").addClass("hide");
                
                $("#"+$mainPage).find(".countdown-step2-title-holder").removeClass("hide");
                $("#"+$mainPage).find(".time-picker-holder").find(".input-holder-timer").removeClass("hide");

                $("#"+$mainPage).find(".exposure-navigation").find(".reg-next-step").html("Klar").addClass("ui-icon-klar");

                MySkills.intervalID = 0;

                $minute = parseInt($("#"+$mainPage).find(".time-picker-holder").find("#countdown_time").val());
                $timer = ($minute<10 ? "0" + $minute : $minute) + ":00";

                var timer = $minute*60, minutes, seconds ;

                $("#"+$mainPage).find(".time-picker-holder").find(".input-holder-timer").html($timer);
                $("#"+$mainPage).find(".time-picker-holder").find("#current_time_position").val($timer);

                //begin countdown timer
                MySkills.intervalID = setInterval(function () {
                    minutes = parseInt(timer / 60, 10);
                    seconds = parseInt(timer % 60, 10);

                    if(minutes==0 && seconds==0){ //if counter reaches 0, then clear the interval
                        clearInterval(MySkills.intervalID);
                        
                        MySkills.MyAudio.addEventListener('ended', function() {
                            this.currentTime = 0;
                            this.play();
                        }, false);
                        MySkills.MyAudio.play();
                    }

                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    $("#"+$mainPage).find(".time-picker-holder").find(".input-holder-timer").html(minutes + ":" + seconds);
                    $("#"+$mainPage).find(".time-picker-holder").find("#current_time_position").val(minutes + ":" + seconds);

                    if (--timer < 0) {
                        timer = $minute*60;
                    }
                }, 1000);

                return false;
            }else{
                $c = $("#"+$mainPage).find(".exposure-steps-holder").find(".steps-holder:not(.hide)");
               
                if($c.hasClass("last")){
                    MySkills.saveExposureTask();
                    return false;
                }
            }
            
        }else if(template=="step_countdown" && direction=="next" && $.trim($("#"+$mainPage).find(".exposure-navigation").find(".reg-next-step").html())=="Klar"){
            
            $c = $("#"+$mainPage).find(".exposure-steps-holder").find(".steps-holder:not(.hide)");
               
            if($c.hasClass("last")){
                MySkills.saveExposureTask();
                return false;
            }
        }else if(template!="step_countdown" && direction=="next"){
            
            $c = $("#"+$mainPage).find(".exposure-steps-holder").find(".steps-holder:not(.hide)");
               
            if($c.hasClass("last")){
                MySkills.saveExposureTask();
                return false;
            }
        }

        if($.trim($("#"+$mainPage).find(".exposure-navigation").find(".reg-next-step").html())=="Klar"){
            
            clearInterval(MySkills.intervalID);
            MySkills.intervalID = 0;

            $("#"+$mainPage).find(".regular-title-holder").removeClass("hide");
            $("#"+$mainPage).find(".time-picker-holder").find(".set-time").removeClass("hide");
            $("#"+$mainPage).find(".time-picker-holder").find(".input-holder").html("0").removeClass("hide");
            $("#"+$mainPage).find(".time-picker-holder").find("#countdown_time").val(0);
            
            $("#"+$mainPage).find(".countdown-step2-title-holder").addClass("hide");
            $("#"+$mainPage).find(".time-picker-holder").find(".input-holder-timer").addClass("hide");

            $("#"+$mainPage).find(".exposure-navigation").find(".reg-next-step").html("Nästa").removeClass("ui-icon-klar");
            if(direction=="prev") return false;
        }


        if((template=="step_ec_words" || template=="step_ec_sentences") && direction!="prev"){
            if ($("#"+$mainPage).find("#" + current_step_id).find(".expand_collapse_answer:checked").length == 0) {
                msgBox("Välj minst ett alternativ");
                return false;
            }
        }else if(template=="step_ec_descriptions"  && direction!="prev"){
            if ($("#"+$mainPage).find("#" + current_step_id).find(".expand_collapse_description:checked").length == 0) {
                msgBox("Välj minst ett alternativ");
                return false;
            }
        }else if((template=="step_keywords" || template=="step_sentences")  && direction!="prev"){
            $name = template=="step_keywords" ? "keyword_answer" : "sentence_answer";
            if ($("#"+$mainPage).find("#" + current_step_id).find("."+$name+":checked").length == 0) {
                msgBox("Välj minst ett alternativ");
                return false;
            }
        }
        
        var selector = $("#"+$mainPage).find(".exposure-steps-holder").find(".steps-holder");

        var idCount = selector.length;
        var curItemIdx = selector.not(".hide").index();
        var lastBound = idCount-1;
        
        if(direction=="next"){
            console.warn("curItemIdx = "+curItemIdx+", lastBound = "+lastBound);
            if(curItemIdx>=0 && curItemIdx<lastBound){
                
                selector.addClass("hide");
                selector.eq(curItemIdx+1).removeClass("hide");

                if(selector.eq(curItemIdx+1).hasClass("last") && selector.eq(curItemIdx+1).attr("data-template")!="step_countdown"){
                    
                    $("#"+$mainPage).find(".feelings-navigation-holder").addClass("hide");
                    $("#"+$mainPage).find(".feelings-save-nav-holder").removeClass("hide");
                }else if(selector.eq(curItemIdx+1).hasClass("last") && selector.eq(curItemIdx+1).attr("data-template")=="step_countdown"){
                    
                    $("#"+$mainPage).find(".feelings-navigation-holder").removeClass("hide");
                    $("#"+$mainPage).find(".feelings-save-nav-holder").addClass("hide");  

                }else{
                    
                    $("#"+$mainPage).find(".feelings-navigation-holder").removeClass("hide");
                    $("#"+$mainPage).find(".feelings-save-nav-holder").addClass("hide");   
                }
            }

        }else{
            if(curItemIdx==lastBound || curItemIdx>0){
                
                selector.addClass("hide");
                selector.eq(curItemIdx-1).removeClass("hide");
            }else if(curItemIdx==0){
                
                //backpage($parentPage,event,'slide');
                $chkIfempty = $("#"+$parentPage).find("ul[data-role='listview']").find("li").length;
                if($chkIfempty==0){ //if there are no other skills then show main skill start page
                    $parentPage = "MyModule_AvailSkills";

                    $chkagainIfEmpty = $("#"+$parentPage).find("ul[data-role='listview']").find("li").length;
                    if($chkagainIfEmpty==0){
                        $parentPage = "MySkills_Module";
                    }
                }
                backpage($parentPage , event);
            }
        }

        MySkills.floatExposureKeywords($mainPage);
        $(document).on( "collapse", ".ui-collapsible", function( event, ui ){ //Refresh the scroll when collapsible set is expanded or collapsed
           if(typeof scrolls[$mainPage]!="undefined") scrolls[$mainPage].refresh();
        }).on( "expand", ".ui-collapsible", function( event, ui ){
           if(typeof scrolls[$mainPage]!="undefined") scrolls[$mainPage].refresh();
        }); 
        
         var b= setTimeout(function(){ //dirty fix for i-scroll bug that the scroll was not refreshed
            scrolls[$mainPage].refresh();
            $(".ui-loader").hide();
            clearTimeout(b);
       },200);
    },
    getNoOfSlotsForGreenGraph: function(totalAssignments) {
        var totalSlot = 5;
        if (totalAssignments >= 5) {
            if (totalAssignments < 20) {
                totalSlot = (parseInt(totalAssignments / 10, 10) + 1) * 10;
            } else if (totalAssignments >= 20 && totalAssignments < 40) {
                totalSlot = 40;
            } else if (totalAssignments >= 40 && totalAssignments < 100) {
                totalSlot = 100;
            } else if (totalAssignments >= 100) {
                totalSlot = 145;
            }
        }
        return totalSlot;
    },
    saveThoughtAssignments: function(data){
         //get previous count
        data["times_used"] = 1;
        sqlhelper.insertJSONData("tbl_v2_sk_thoughts_assignments",data, function(){
                offlinehelper.prepareForSync(false);
        });
         
    },

    skillsDetails:function(obj){
        MySkills.MyAudio = new Audio();
        var argLength = arguments.length;
        if(arguments.length==2){
            var skillId         = obj.skillid,
                moduleId        = obj.moduleid,
                moduleName      = obj.modulename,
                skillType       = obj.type,
                thought_type    = obj.thoughttype;
        }else{
            var skillId         = obj.attr("data-skillid"),
                moduleId        = obj.attr("data-moduleid"),
                moduleName      = obj.attr("data-modulename"),
                skillType       = obj.attr("data-type"),
                thought_type    = obj.attr("data-thoughttype");
        }
        
        var userdetails = $.jStorage.get("userdetails"),
            json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '", "moduleId":"'+moduleId+'","skillType":"'+skillType+'","skillId":"'+skillId+'"}'
            html = "";

        $mainPage = "MyModule_SkillDetails";
         callWebService('getSKillDetails', json, function(response) {
                if(skillType=="thoughts"){
                        
                        if(response.status == "ok"){
                                $("#"+$mainPage).find(".page-title-v2").html(response.data.headline);
                                
                                if(response.data.thought_type == "text"){
                                       html +="<div class='html-contents thought-contents'>"+response.data.thought_text+"</div>";
                                       html +='<div class="text-step-button thoughts-btn-holder"><a href="javascript:void(0)" onclick="filehelper.killAllSound(); MySkills.listModules(); " class="proceed-to-feelings border-radius ui-btn ui-btn-up-b" data-role="button">Klar</a></div>';
                                       $("#"+$mainPage).find(".skill-contents").html(html);


                                       MySkills.saveThoughtAssignments({
                                            'assignment_id': 0,
                                            'thought_id': response.data.thought_id,
                                            'skill_id': skillId,
                                            'patient_id': userdetails.user_id
                                       });

                                }else{ //audio
                                       console.error("AUDIO URL = "+response.data.sound_url);
                                       if(isWebVersion==false){
                                            if(window.cordova){
                                                if($.trim(response.data.thought_sound_file)!=""){
                                                    if(filehelper.checkFileExist(cordova.file.dataDirectory + response.data.thought_sound_file)){
                                                        audioURL = cordova.file.dataDirectory + response.data.thought_sound_file;
                                                    }else{
                                                        audioURL = response.data.sound_url;
                                                    }
                                                }else{
                                                    audioURL = response.data.sound_url;
                                                }
                                           }else{
                                                audioURL = response.data.sound_url;
                                           }
                                       }else{//web version
                                            audioURL = filehelper.getRemotePath()+"assets/sound_files/thoughts/"+response.data.thought_sound_file;
                                       }
                                       
                                       //var audioURL = response.data.sound_url;
                                       MySkills.MyAudio.src = audioURL;
                                       MySkills.getAudioDuration();
                                       html += "<div class='audio-background' style='background-color:#"+response.data.sound_background_color+"'>";
                                       html += "<div class='play-pause-holder'>";
                                      /* html += "<a href='javascript:void(0)' data-audiourl='"+audioURL+"' data-dowhat='1' class='audio-playback-play' onclick='MySkills.audioPlayback($(this));'><img src='images/play_button_up.png' width='117'></a>";
                                       html += "<a href='javascript:void(0)' data-audiourl='"+audioURL+"' data-dowhat='0' class='audio-playback-pause' onclick='MySkills.audioPlayback($(this));' style='display:none'><img src='images/pause_button_up.png' width='117'></a>";
*/                                     html += "<div class='pb-ctrl-wrapper'>";
                                       html += "<div class='audio-playback-play' data-thoughtid='"+response.data.thought_id+"' data-skillid='"+skillId+"' data-patientid='"+userdetails.user_id+"' onclick='MySkills.audioPlayback($(this));'></div>";
                                       html += "<div class='audio-playback-pause'  data-thoughtid='"+response.data.thought_id+"' data-skillid='"+skillId+"' data-patientid='"+userdetails.user_id+"' onclick='MySkills.audioPlayback($(this));' style='display:none'></div>";
                                       html += "</div>";

                                       
                                       html += "</div><div class='slider-holder'>";
                                       html += '<div class="fl current-position">00:00:00</div><div class="fr audio-length">00:00:00</div>';
                                       html += '<div class="clear"></div><div class="slider"><input type="range" id="audio-seeker" value="0" min="0" max="1000" step="1" data-rangeslider></div>';
                                       html += "</div>";
                                       html += "</div>";
                                       html +='<div class="text-step-button thoughts-btn-holder"><a href="javascript:void(0)" onclick="filehelper.killAllSound(); MySkills.listModules()" class="proceed-to-feelings border-radius ui-btn ui-btn-up-b" data-role="button">Klar</a></div>';
                                       $("#"+$mainPage).find(".skill-contents").html(html);
                                }

                                if(argLength==2){
                                    $("#"+$mainPage).find("[data-rel='goback']").attr("onclick","return backpage('MySkills_Module', event);");
                                }

                                MySkills.init();
                                changepage($mainPage);
                                enableIScroll();
                                refreshScroll($mainPage);
                        }

                }
         });
    },
    audioPlayback: function(obj){
        if(navigator.onLine==false){
            /*msgBox("Offline läge");
            return false;*/
        }
        if(MySkills.isPlaying==0){
            MySkills.isPlaying =1;
            $("div.audio-playback-pause").show();
            $("div.audio-playback-play").hide();
            MySkills.MyAudio.play();
        }else{
            MySkills.isPlaying = 0;
            $("div.audio-playback-pause").hide();
            $("div.audio-playback-play").show();
            MySkills.MyAudio.pause();
        }

        MySkills.MyAudio.addEventListener('ended',function(){
            MySkills.isPlaying = 0;
            $("#audio-seeker").val(0).change();
            $("div.audio-playback-pause").hide();
            $("div.audio-playback-play").show();
            MySkills.saveThoughtAssignments({
                'assignment_id': 0,
                'thought_id': obj.attr("data-thoughtid"),
                'skill_id': obj.attr("data-skillid"),
                'patient_id': obj.attr("data-patientid")
            });
        });

        MySkills.MyAudio.addEventListener('timeupdate', function() {
            var currentTimeSecs = MySkills.MyAudio.currentTime;
            $currentTime=secondToTimeformat(currentTimeSecs);
            $(".slider-holder").find(".current-position").html($currentTime);
            if(MySkills.isSeeking==false){
                $("#audio-seeker").val(Math.floor(currentTimeSecs)).change();
            }

        });

       
         
    }, 
    getAudioDuration: function(){
        MySkills.MyAudio.addEventListener('loadedmetadata', function(_event) {
            MySkills.audioDuration = Math.floor(MySkills.MyAudio.duration);
            $("#audio-seeker").attr("max",MySkills.audioDuration);
            $('#audio-seeker').rangeslider('update', true);
            $totalTime=secondToTimeformat(MySkills.MyAudio.duration)
            $(".slider-holder").find(".audio-length").html($totalTime);

        });
    },
    saveExposureTask: function(obj){
        MySkills.validateUserSelection($("#frmExposureTask").find(".exposure-steps-holder"));
        MySkills.modulesSaved = 0;
        var container = $("#frmExposureTask");

        offlinehelper.syncIntervalOnRegistration=setInterval(function(){
             console.log("Total count is "+MySkills.modulesSaved);
            if(MySkills.modulesSaved==1){
                        msgBox("Exponeringen sparades");
                        clearInterval(offlinehelper.syncIntervalOnRegistration);
                        
                        /*if($("#exposure-module[data-moduleid='"+container.find("#module_id").val()+"']").length>0){
                            $("#exposure-module[data-moduleid='"+container.find("#module_id").val()+"']").trigger("click");
                        }else{
                            $("#module-content-"+container.find("#module_id").val()).trigger("click");
                        }*/
                        MySkills.listModules();
                       // $("#module-content-"+container.find("#module_id").val()).trigger("click");
                        offlinehelper.prepareForSync(false);

            }
                
        },100);


       
        var userdetails = $.jStorage.get('userdetails');

        var data = {
            "assignment_id": 0,
            "exposure_id": container.find("#exposure_id").val(),
            "date_answered": moment().format("YYYY-MM-DD HH:mm:ss"),
            "patient_id": userdetails.user_id,
            "rating": container.find("input[name='graph-rating']").val(),
            "countdown_timer": container.find("#selected_minute").val(),
            "countdown_completed": container.find("#current_time_position").val()
        };

       /* console.clear();
        console.warn("SAVING DATA... "+JSON.stringify(data));
        return false;*/

        sqlhelper.insertJSONData('tbl_v2_sk_exposure_patients_assignments', data, function(){
            sqlhelper.db.transaction(function(tx){
                    tx.executeSql("SELECT MAX(app_assignment_id) as app_assignment_id FROM tbl_v2_sk_exposure_patients_assignments;", [], function(txs, results){
                            if(results.rows.length>0){
                                var new_app_assignment_id = results.rows.item(0).app_assignment_id;
                                MySkills.saveExposureDetails(new_app_assignment_id, 0);
                            }else{
                                msgBox("Error in saving Exposure");
                            }
                    });

            });
        });
    },
    saveExposureDetails: function(new_app_assignment_id,assignment_id){
        var container = $("#frmExposureTask");
        var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","form_data":"' + $("#frmExposureTask").serialize() + '"}';
        var exposure_id = container.find("#exposure_id").val();

        var assignment_id= 0; //$('#reg_assignment_id').val();
        var app_exp_assignment_id= 0; //$('#app_reg_assignment_id').val();
        var holder = container.find(".exposure-steps-holder");

        if(holder.find("input").length>0){
            var anslengthcount=1;
            var anslength = holder.find("input").length;

            $(holder.find("input")).each(function(i,e){
                var obj = $(this);
                if(obj.attr("type")=="radio" || obj.attr("type")=="checkbox"){
                    if(obj.is(":checked")){
                        if(obj.val()!=""){
                            var tosave = $.parseJSON(obj.val());
                            var data={
                                  'assignment_details_id':0,
                                  'app_assignment_id':new_app_assignment_id,
                                  'assignment_id':assignment_id,
                                  'exposure_id':exposure_id,
                                  'step_id':tosave.step_id,
                                  'answer_id':tosave.answer_id,
                                  'app_answer_id':tosave.app_answer_id
                            };

                            sqlhelper.insertJSONData("tbl_v2_sk_exposure_patients_assignments_details", data);
                            if(anslengthcount==anslength){
                                console.log("Reeturned from tbl_v2_sk_exposure_patients_assignments_details");
                                MySkills.modulesSaved++;
                            }
                            anslengthcount++;
                        }else{
                            if(anslengthcount==anslength){
                                console.log("Reeturned from tbl_v2_sk_exposure_patients_assignments_details");
                                MySkills.modulesSaved++;
                            }
                            anslengthcount++;
                        }
                    }else{
                        if(anslengthcount==anslength){
                            console.log("Reeturned from tbl_v2_sk_exposure_patients_assignments_details");
                            MySkills.modulesSaved++;
                        }
                        anslengthcount++;
                    }
                }else{
                    if(anslengthcount==anslength){
                        console.log("Reeturned from tbl_v2_sk_exposure_patients_assignments_details");
                        MySkills.modulesSaved++;
                    }
                    anslengthcount++;
                }
            });
        }


    },
    saveSkillTask: function(obj){
        MySkills.validateUserSelection($("#frmSkillTask").find(".exposure-steps-holder"));
        MySkills.modulesSaved = 0;
        var container = $("#frmSkillTask");

        offlinehelper.syncIntervalOnRegistration=setInterval(function(){
             console.log("Total count is "+MySkills.modulesSaved);
            if(MySkills.modulesSaved==1){
                        clearInterval(offlinehelper.syncIntervalOnRegistration);
                        msgBox("Färdigheter sparades");
                       // changepage("MyModule_SkillList");
                        
                        /*if($("#skills-module[data-moduleid='"+container.find("#module_id").val()+"']").length>0){
                            $("#skills-module[data-moduleid='"+container.find("#module_id").val()+"']").trigger("click");
                        }else{
                            $("#module-content-"+container.find("#module_id").val()).trigger("click");
                        }*/
                        MySkills.listModules();

                         //$("#module-content-"+container.find("#module_id").val()).trigger("click");

                        offlinehelper.prepareForSync(false);
            }
                
        },100);


      
        var userdetails = $.jStorage.get('userdetails');

        var data = {
            "assignment_id": 0,
            "skill_id": container.find("#skill_id").val(),
            "date_answered": moment().format("YYYY-MM-DD HH:mm:ss"),
            "patient_id": userdetails.user_id,
            "rating": container.find("input[name='graph-rating']").val(),
            "countdown_timer": container.find("#selected_minute").val(),
            "countdown_completed": container.find("#current_time_position").val()
        };

        sqlhelper.insertJSONData('tbl_v2_sk_skills_assignments', data, function(){
            sqlhelper.db.transaction(function(tx){
                    tx.executeSql("SELECT MAX(app_assignment_id) as app_assignment_id FROM tbl_v2_sk_skills_assignments;", [], function(txs, results){
                            if(results.rows.length>0){
                                var new_app_assignment_id = results.rows.item(0).app_assignment_id;
                                MySkills.saveSkillDetails(new_app_assignment_id, 0);
                            }else{
                                msgBox("Error in saving Skill task");
                            }
                    });

            });
        });
    },
    saveSkillDetails: function(new_app_assignment_id,assignment_id){
        var container = $("#frmSkillTask");
        var userdetails = $.jStorage.get('userdetails');
        var json = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '","form_data":"' + $("#frmSkillTask").serialize() + '"}';
        var skill_id = container.find("#skill_id").val();

        var assignment_id= 0; //$('#reg_assignment_id').val();
        var app_exp_assignment_id= 0; //$('#app_reg_assignment_id').val();
        var holder = container.find(".exposure-steps-holder");

        if(holder.find("input").length>0){
            var anslengthcount=1;
            var anslength = holder.find("input").length;

            $(holder.find("input")).each(function(i,e){
                var obj = $(this);
                if(obj.attr("type")=="radio" || obj.attr("type")=="checkbox"){
                    if(obj.is(":checked")){
                        if(obj.val()!=""){
                            var tosave = $.parseJSON(obj.val());
                            var data={
                                  'assignment_details_id':0,
                                  'app_assignment_id':new_app_assignment_id,
                                  'assignment_id':assignment_id,
                                  'skill_id':skill_id,
                                  'step_id':tosave.step_id,
                                  'answer_id':tosave.answer_id,
                                  'app_answer_id':tosave.app_answer_id
                            };

                            sqlhelper.insertJSONData("tbl_v2_sk_skills_assignments_details", data);
                            if(anslengthcount==anslength){
                                console.log("Reeturned from tbl_v2_sk_skills_assignments_details");
                                MySkills.modulesSaved++;
                            }
                            anslengthcount++;
                        }else{
                            if(anslengthcount==anslength){
                                console.log("Reeturned from tbl_v2_sk_skills_assignments_details");
                                MySkills.modulesSaved++;
                            }
                            anslengthcount++;
                        }
                    }else{
                        if(anslengthcount==anslength){
                            console.log("Reeturned from tbl_v2_sk_skills_assignments_details");
                            MySkills.modulesSaved++;
                        }
                        anslengthcount++;
                    }
                }else{
                    if(anslengthcount==anslength){
                        console.log("Reeturned from tbl_v2_sk_skills_assignments_details");
                        MySkills.modulesSaved++;
                    }
                    anslengthcount++;
                }
            });
        }


    },
     validateUserSelection: function(container){
        var error = 0;
        var errorChk = 0;


        container.find(".steps-holder").each(function(){
            if($(this).hasClass("last") && $(this).attr("data-template")=="step_checking_task"){
                 $ischecked = $(this).find("#check-task-template-ele").attr("data-status");
                if($ischecked=="off"){
                     errorChk=1;
                }
            }

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

        if(error>0 || errorChk==1){
            if(errorChk==1){
                 $emptyLength = "Du måste kryssa i knappen ovan för att fortsätta.";
            }else{
                 $emptyLength = "Välj minst ett alternativ för varje steg.";
            }
            msgBox($emptyLength);
            throw $emptyLength;
        }
    },
    controlExpandCollapse: function(obj){
        $parent = obj.attr("data-parent");
        if(typeof obj.attr("data-stepid")!="undefined"){
            $main = $("#"+$parent).find("#step_"+obj.attr("data-stepid")).find("div[data-role='collapsibleset']");
        }else{
            $main = $("#"+$parent).find("div[data-role='collapsibleset']");
        }
        $main.find("div[data-role='collapsible']").each(function(){
            $size = $(this).find("input[type='radio']:checked, input[type='checkbox']:checked").size();
            console.warn("CHECKED SIZE = "+$size);
            if($size>0){
                $(this).attr("data-isdisabled","yes");
            }else{
                $(this).attr("data-isdisabled","no");
            }
        });

        $main.find("div[data-role='collapsible']").find("h3").find("a.ui-collapsible-heading-toggle").click(function(){
            var c =   $(this).parent("h3").parent(".ui-collapsible").attr("data-isdisabled");
           
            if(c=="yes"){
                return false;
            }else{
                return true;
            }
            
        });
    }
};