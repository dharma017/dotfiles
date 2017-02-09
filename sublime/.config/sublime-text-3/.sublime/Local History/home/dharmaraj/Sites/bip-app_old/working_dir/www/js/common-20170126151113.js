    //Page Navigation
    var PageFollowStack = new Array();
    var scrolls = [];
    var installationid="";
    var parse_app_id=PARSE_APP_ID;
    var parse_client_id=PARSE_CLIENT_KEY;
    var parseApiAddress=PARSE_API_ADDRESS+"angular.bind(self, function)";
    var updateTimer;
    var updatetime=1000*60;
        idleTimer = null;
    idleState = false;
    idleWait = 1000*60*15; //sabin 20151027
    var activateTaskTimer = null; //sabin 112412
    var displayedInvalidLoginDialog = 0; //added by sabin
    var displayedNoModulesDialog = 0; //added by sabin
    var displayedLogoutAlert = 0;
    if(location.host==""){
        var isWebVersion = false; //if true then load images, audio from remote url as app is running on web
    }else{
        var isWebVersion = true;
    }
   
    //Function to adjust height of the content, without is iscroll won't work properly.
    function adjustContentHeight(){
        if($(".page-app-v2").length>0){
            $height = $(window).innerHeight();
            console.info("HEIGHTWA = "+$height);
            $(".page-app-v2").find(".ui-content").height(parseFloat($height)-20);
        }
    }


    String.prototype.capitalizeFirstLetter = function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }

    String.prototype.smallFirstLetter = function() {
        return this.charAt(0).toLowerCase() + this.slice(1);
    }

     //var currentPageId='';
    // common message box
    function msgBox(message) {
        if (navigator.notification != undefined && navigator.notification != null) {
            navigator.notification.alert(message, alertDismissed, 'BIP', 'Ok');
            //alert(message);
        } else {
            alert(message);
        }
    }

    function confirmBox(message, cCallback) {

        /*if (confirm(message)) {
                return cCallback(1);
        } else {
            return cCallback(2);
        }*/
        if (navigator.notification != undefined && navigator.notification != null) {
            navigator.notification.confirm(
                message, // message
                cCallback, // callback to invoke with index of button pressed
                'BIP', // title
                'Ja,Nej' // buttonLabels
            );
        } else {
            if (confirm(message)) {
                return cCallback(1);
            } else {
                return cCallback(2);
            }
        }
    }

    function alertDismissed() {
        // do something
    }

    function CheckSession() {
        if ($.jStorage.get('tokenkey') == null) {
            gotoLoginPage();
        } else {
            gotoTargetPage();
        }
    }

    // validation for login page
    function checkLoginValues() {
        if ($("#txtEmail").val().trim() == '') {
            msgBox('Vänligen fyll i e-post');
        } else if ($("#txtPwd").val().trim() == '') {
            msgBox('Vänligen fyll i lösenord');
        } else {
            return true;
        }

    }

    var successCallback=function(e){
      //  alert("Success call back "+e);
      
    }


    var installationid=function(e){
       // alert("id is "+e);
        installationid=e;
    }

    var failureCallback=function(){

        //alert("Connection with Parse failed");
    }
    // call login function
    function fnLogin() {

        isValidUser(); //added by sabin
        displayedNoModulesDialog = 0;
        document.removeEventListener("backbutton", backKeyDown, false);
        document.addEventListener("backbutton", backKeyDown, false);
        offlinehelper.checkOnlineStatus(); //added by sabin
        
        if (checkLoginValues()) {

            //For Push Details Save With Login, reduce extra request
            //changepage('TrainingList');
            //setTimeout(function(){enableIScroll();},1000);
            //return;

            var json = '';
            var deviceinstallationid="";
            if (IsAndroid || IsIDevice) {
                if (AppId === '' || AppId === null)
                    AppId = $.jStorage.get('AppId') === null ? '' : $.jStorage.get('AppId');
                if (DeviceID === '' || DeviceID === null)
                    DeviceID = $.jStorage.get('DeviceID') === null ? '' : $.jStorage.get('DeviceID');
                if (DeviceType === '' || DeviceType === null)
                    DeviceType = $.jStorage.get('DeviceType') === null ? '' : $.jStorage.get('DeviceType');
                if (DeviceName === '' || DeviceName === null)
                    DeviceName = $.jStorage.get('DeviceName') === null ? '' : $.jStorage.get('DeviceName'); //new Date().getTimezoneOffset() * -1
            }
            var dtype="";
            DeviceID = "ABBBSBS";
            dtoken=  1;
            if(IsAndroid){
                    dtype="Android";
                   
                     dtoken="";
                    
            }else if (IsIDevice){
                    dtype="IOS";
                    //cordova.exec.setJsToNativeBridgeMode(cordova.exec.jsToNativeModes.XHR_NO_PAYLOAD);
                    
                        //DeviceID = $.jStorage.get('DeviceID') === null ? '' : $.jStorage.get('DeviceID');
                  
            }
             deviceinstallationid=$.jStorage.get('DeviceID');

             //"appid":"'+AppId+'",
            json = '{"username":"' + $("#txtEmail").val().trim() + '","password":"' + $("#txtPwd").val().trim() + '","deviceId":"' + DeviceID + '","tokenkey":"' + dtoken + '","identificationumber":"'+ deviceinstallationid +'","devicetype":"'+dtype+'"}';
           console.log(json);
            var webmethodname = 'validateuser';


            offlinehelper.checkFirstTime(function(data){
                
                if(data=='error'){
                    msgBox("Ingen internetuppkoppling");
                    $.mobile.hidePageLoadingMsg();   
                    return;
                }
                if(data==false){
                    //checkAvailableModules();
                    console.info("IS NOT FIRST LOGIN");
                   //added by sabin
                   
                   if(navigator.onLine && offlinehelper.syncstarted==false){ //  && offlinehelper.loginstarted==false:: sync only if user is not logging in for first time, for first time we already have synced
                        console.warn("NOW SYNCING THE RECORDS");
                        offlinehelper.prepareForSync(true);
                   }

                   if(navigator.onLine==false && offlinehelper.hidealert==false){
                        $('.offline_dialogue').show();
                   }
                   
                   checkAvailableModules();

                    callWebService(webmethodname, json, function(response) {
                     console.warn("Check how how many times called");
                    if (response.status === "ok") {
                        if (response.data !== "invalid") {
                            $(".download-content-msg").hide();
                            $(".download-overlay").hide();

                            //$(".sync-btn-holder").show(); //added by sabin
                            console.warn("Logging in with offline data ");
                            //$(".sync-btn-holder").show(); //added by sabin
                           // sqlhelper.initiateDatabase(response);
                            //changepage('TrainingList');
                            //also remember the username
                            
                           
                            response.data.tokenkey=$.jStorage.get('bip_jwt');
                            $.jStorage.set('userEmail', $("#txtEmail").val().trim());
                            $.jStorage.set('userdetails', response.data);
                            
                            Training.reminders=$.parseJSON(response.data.reminders);
                            Training.feedback=$.parseJSON(response.data.feedbackMessage);
                            
                            

                            if(response.data.availableModules!="undefined"){
                                offlinehelper.EnabledModules = JSON.parse(response.data.availableModules);
                            }

                            if (response.data.sms == "true") {
                                $("#smserror").hide();
                                changepage("smsLogin");
                            }else if(response.data.new_start_page==3){ //==1 added by sabin 290
                                if(typeof response.data.availableModules!="undefined"){ //Sabin 23Nov2015
                                    if(response.data.availableModules!="undefined"){
                                        //if all modules are disabled just give the message
                                        if(offlinehelper.ShowHideModules("registration")==0 && (offlinehelper.ShowHideModules("homework_module")==0 || (offlinehelper.ShowHideModules("homework_module")==1 && offlinehelper.ShowHideModules("homework_id").length==0)) && offlinehelper.ShowHideModules("crisis_plan")==0 && offlinehelper.ShowHideModules("my_skills")==0){
                                            $('.btn_logout').attr("data-autologout",1); // sabin 20151027
                                            $('.btn_logout').trigger('click');

                                            if(displayedNoModulesDialog==0){
                                                msgBox("Din app är inte aktiverad. Var god kontakta din psykolog");
                                                displayedNoModulesDialog=1;
                                            }
                                            
                                           // return false;
                                        }
                                    }
                                }

                                //render self harm homepage
                                callWebService("checkModulesEnabled",json, function(response){
                                    if(response.status=="ok"){
                                           
                                            var resV2 = {
                                                hasRegistration:    response.data.hasRegistration,
                                                homeworks:          response.data.homeworks,
                                                crisisplans:        response.data.crisisplans
                                            };

                                            offlinehelper.isSelfHarm = true; 
                                            BipAppVersion2.renderStartPage(resV2);
                                            $('#RegistrationTask div:first a:first').hide();
                                            $('.btn_logout').attr("data-autologout",0);
                                            Registration.home();
                                            checkNewSettings();
                                    }
                                })

                                
                            }else {
                                checkNewSettings();
                                offlinehelper.isSelfHarm = false; 
                                if(navigator.onLine==true){
                                    offlinehelper.downloadAudioFiles();
                                }

                                Training.setTrainings();

                                
                                //Added by sabin @ 16th Apr 2015. 
                               // BipAppVersion2.renderStartPage(resV2);
                                //Registration.renderRegistrationNav(response.data.hasRegistration);
                                //Commented above 2 lines at 6th oct 2015 by sabin 290
                                
                                //added by sabin the offline banner displaying code removed from here and put it into offlinehelper.js in method checkOnlineStatus
                                //
                                //
                                //

                                            updateTimer = setInterval(function () { 
                                            console.log("Getting called");
                                            if(navigator.onLine==true){
                                                 var userdetails = $.jStorage.get('userdetails');
                                                 var json_new = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '"}';
                                                    callWebServiceLiveSilently('reminder', json_new, function(response) {
                                                            if(JSON.stringify(Training.reminders) !=JSON.stringify(response.data)){
                                                                  Training.reminders=response.data;


                                                                   var toadd={
                                                                                where:{
                                                                                    'user_id': userdetails.user_id
                                                                                },
                                                                                fields:{
                                                                                    'reminders':JSON.stringify(response.data)
                                                                                 }
                                                                            };
                                                                    callWebService('saveReminder', toadd, function(response) {
                                                                            
                                                                    },"",false);
                                                            }
                                                            /*if($('.checkregister').hasClass('Smartmatning')) //sabin 112412
                                                                Training.activateDeactivateTask();*/


                                                    },"",false);
                                                   if(offlinehelper.syncstarted==false && (offlinehelper.currentpage=="TrainingList" || offlinehelper.currentpage=="RegistrationTask") )
                                                        offlinehelper.prepareForSync();
                                            }else{
                                               /* if($('.checkregister').hasClass('Smartmatning')) //sabin 112412
                                                    Training.activateDeactivateTask();*/
                                            }
                                            //console.log(Training.TaskLists);
                                        },updatetime);

                                    //sabin 112412
                                    
                                    /*activateTaskTimer = setInterval(function(){
                                            
                                            if($('.checkregister').hasClass('Smartmatning') && navigator.onLine==true){
                                                   //Training.activateDeactivateTask();
                                                   if($.trim($(".pagename").html())=="Register"){
                                                        callWebServiceLiveSilently("getservertime", "{}", function(response) {
                                                            var d =  {
                                                                hour: response.data.hour,
                                                                minute: response.data.minute
                                                            };
                                                            Training.activateDeactivateTask(d); // Activate deactivate task only if task is of tag2
                                                        },"",false);
                                                    };
                                            }
                                    },3000);*/

                                    activateTaskTimer = setInterval(function(){
                                            if($('.checkregister').hasClass('Smartmatning')){
                                                   Training.activateDeactivateTask();
                                            }
                                    },1000);
                               

                            }

                             (function ($) { //Sabin 2412 // put this function outside if condition.

                                $(document).ready(function () {
                                
                                    $('*').bind('mousemove keydown scroll touchend', function () {
                                        
                                        clearTimeout(idleTimer);
                                                
                                        if (idleState == true) { 
                                            
                                            // Reactivated event
                                            //$("body").append("<p>Welcome Back.</p>");            
                                        }
                                        
                                        
                                        idleState = false;
                                      
                                        idleTimer = setTimeout(function () { 
                                            $('.btn_logout').attr("data-autologout",1); // sabin 20151027
                                            $('.btn_logout').trigger('click');
                                            // Idle Event

                                           // $("body").append("<p>You've been idle for " + idleWait/1000 + " seconds.</p>");
                                            console.error("You've been idle for " + idleWait/1000 + " seconds.");
                                            idleState = true; 
                                            }, 
                                        idleWait);
                                    });
                                    
                                    $("body").trigger("mousemove");
                                
                                });
                            }) (jQuery)

                            var abcd = setTimeout(function() {
                                //make a slight delay so that the password field empty
                                //is not visible just after the login
                                //until the slide transition has been completed
                                //3 seconds should be enough
                                $('#txtPwd').val('');
                                clearTimeout(abcd);
                            }, 3000);

                        } else {
                            msgBox('Either Anvandarnamn or Losenord is incorrect');
                            offlinehelper.loginstarted=false;
                        }
                    } else if (response.status === "error") {
                        msgBox("Error::: "+response.message);
                        offlinehelper.loginstarted=false;
                    } else {
                        /*msgBox('Ett fel har inträffat, försök igen.');*/
                        if(response==true){
                             msgBox("Användarnamn och lösenord matchar inte."); //sabin 2412
                        }else{
                            msgBox("Det gick inte att logga in. Vänligen kontakta din behandlare."); //Nov29
                        }
                        offlinehelper.loginstarted=false;
                    }
                });
                }else{
                    console.info("IS FIRST LOGIN");
                    callWebServiceLive(webmethodname, json, function(response) {
                        if (response.status === "ok") {
                            if (response.data !== "invalid") {
                                console.log("Everything");
                                offlinehelper.syncTable(response.data);
                            }
                        }else{ // provided invalid login information in the fist time login //added by sabin
                            if(response.message=="patient_inactive"){
                                 msgBox("Det gick inte att logga in. Vänligen kontakta din behandlare."); //sabin 2412
                            }else{
                                if(displayedInvalidLoginDialog==0){
                                    msgBox("Det gick inte att logga in. Kontrollera användarnamn och lösenord."); //sabin 2412
                                    displayedInvalidLoginDialog = 1;
                                }
                            }
                            
                            $.jStorage.flush();
                            $(".download-content-msg").hide();
                            $(".download-overlay").hide();
                            $(".ui-loader").hide();
                            sqlhelper.dropTables(offlinehelper.createdTables);
                        }
                    });
                   
                }
            });
          
        }

    }

    // call LogOut function
    function fnLogOut() {
        confirmBox('Är du säker på att du vill logga ut?', function(button) {

            if (button == 1) {
                LogoutServer();
              
                console.log("All interval cleared");
                $('#txtPwd').val('');
                clearInterval(updateTimer);
                clearTimeout(idleTimer);
            }
        });
    }

    // Destroy session from server
    function LogoutServer() {
        if (IsAndroid || IsIDevice) {
            if (DeviceID === '' || DeviceID === null)
                DeviceID = $.jStorage.get('DeviceID') === null ? '' : $.jStorage.get('DeviceID');
            var json = '{"tokenkey":"' + $.jStorage.get('tokenkey') + '","DeviceID":"' + DeviceID + '"}';

        } else
            var json = '{"tokenkey":"' + $.jStorage.get('tokenkey') + '"}';

        var webmethodname = 'Login/Logout';
        $('#txtPwd').val('');
        callWebService(webmethodname, json, function(response) {
            //if (response.status == "ok") {
            localStorage.clear();

            //$.jStorage.flush();
            gotoLoginPage();
            //}
        });
    }

    function checkAvailableModules(){
        if(DeviceID === '' || DeviceID === null){
            DeviceID = $.jStorage.get('DeviceID') === null ? '' : $.jStorage.get('DeviceID');
        }

        var userdetails = $.jStorage.get('userdetails');
        
        if(userdetails!==null){
            var json_new = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '"}';
            callWebServiceLive("getavailablemodules", json_new, function(response) {
                var d =  response.data;
                offlinehelper.EnabledModules = JSON.parse(d.available_modules);
                offlinehelper.UpdateAvailableModules(d.available_modules);
            });
        }
    }


    function checkNewSettings(){
        if(DeviceID === '' || DeviceID === null){
            DeviceID = $.jStorage.get('DeviceID') === null ? '' : $.jStorage.get('DeviceID');
        }

        var userdetails = $.jStorage.get('userdetails');
        Training.hideGraph = userdetails.hide_graph;
        var json_new = '{"userid":"' + userdetails.user_id + '","tokenkey":"' + userdetails.tokenkey + '","deviceId":"' + DeviceID + '"}';
        callWebServiceLive("newsettings", json_new, function(response) {
           var d =  response.data;
           var enable_msg_alert = parseInt(d.enable_msg_alert);
           Training.hideGraph = d.hide_graph;
           userdetails.hide_graph = d.hide_graph;
           var msgs =  parseInt(d.new_messages);
           if(msgs>0 && enable_msg_alert==1){

                var st = setTimeout(function(){
                    $(".has-alert-message").slideDown("slow", function(){
                            clearTimeout(st);
                            st = null;
                    });
                },500); 
           }
        });
        
    }



    // call login page
    function gotoLoginPage() {
        //changepage('pageLogin');
        $.jStorage.set('gid', null);
        $.jStorage.set('tokenkey', null);
        window.location = window.location.href.replace(window.location.hash, "");
        PageFollowStack = new Array();
    }
    // common function for change page
    
    function changepage(pagename, transition) {
        console.log(pagename);
        transition = transition || 'slide';
        //$.mobile.defaultPageTransition = 'slide';
        //$.mobile.showPageLoadingMsg('a','Loading...', false);
        //, {transition:"slide"}
        //$(document).ready(function() {
        $('.pagename').text(pagename);
        offlinehelper.currentpage=pagename;
        if (pagename == "RegistrationTask_List") {
            transition = "none";
        }
        $.mobile.changePage("#" + pagename, {
            transition: "none"
        });

    }

    /*function backpage(pagename, e, pageTransition) {

        if(pagename=="Login"){
                clearInterval(updateTimer);
                clearTimeout(idleTimer);

        }

        pageTransition = pageTransition || $.mobile.defaultPageTransition;
        var cpageid = $($.mobile.activePage).attr("id");
        if (cpageid == "popUp1" || cpageid == "popUp2" || cpageid == "popUp3") {
            if (Training.editedTrainingID > 0) {
                pagename = 'Tidigare';
                Training.editedTrainingID = 0;
            }
        }
        $.mobile.showPageLoadingMsg("");
        if (pagename == "TrainingZone_1_1") {
            pageTransition = "none";
        }

        $.mobile.changePage("#" + pagename, {
            changeHash: true,
            reverse: true,
            transition: pageTransition
        });

        $.mobile.hidePageLoadingMsg();
        if (e != null && e != undefined) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (pagename == "TrainingList") {
            Training.setTrainings();
        }
        return false;
    }*/

    function backpage(pagename, e, pageTransition) { //sabin 20151027
        //stop audios in teh case of thought module 
       filehelper.killAllSound(); //sabin 20121101

        var autologout = $('.btn_logout').attr("data-autologout");

        if(pagename=="Login"){
            //offlinehelper.loggingOut = true;
           offlinehelper.isLoggedOut = true;

            if(autologout==0 && displayedLogoutAlert==0){
                 confirmBox('Vill du logga ut?', function(button) { //sabin 2412
                    if (button === 1) {
                        offlinehelper.prepareForSync(false); //Added to solve not syncing crisis plans on next login .added by sabin 290
                        localStorage.clear();
                        //$.jStorage.flush();
                        clearInterval(updateTimer);
                        clearTimeout(idleTimer);
                        clearInterval(activateTaskTimer);
                        offlinehelper.hidealert = false; //added by sabin
                        offlinehelper.loginstarted=false; //added by sabin
                        displayedLogoutAlert = 1;
                        $(".sync-btn-holder").hide(); //added by sabin
                        $(".has-alert-message").hide();
                        offlinehelper.loginstarted = false;
                        pageChangeContents(pagename, e, pageTransition);
                    }
                });
             }else{
                    offlinehelper.prepareForSync(false); //Added to solve not syncing crisis plans on next login .added by sabin 290
                    localStorage.clear();
                    //$.jStorage.flush();
                    clearInterval(updateTimer);
                    clearTimeout(idleTimer);
                    clearInterval(activateTaskTimer);
                    offlinehelper.hidealert = false; //added by sabin
                    offlinehelper.loginstarted=false; //added by sabin
                    $(".sync-btn-holder").hide(); //added by sabin
                    $(".has-alert-message").hide();
                     offlinehelper.loginstarted = false;
                    pageChangeContents(pagename, e, pageTransition);
             }
             //location.reload();
        }else{
            
            if (e != null && e != undefined) {
                 $tillbaka = $(e.target).text();
                 if($tillbaka=="Avbryt" && $(e.target).closest(".fixedtop").length==0){ //show msgbox after user clicks back button except in pain reporting (it has already there)
                    confirmBox("Vill du avbryta?", function(button){
                        if(button===1){
                            pageChangeContents(pagename, e, pageTransition);
                        }
                    });
                 }else{
                     pageChangeContents(pagename, e, pageTransition);
                 }
            }else{
                 pageChangeContents(pagename, e, pageTransition);
            }
        }
    }

    function pageChangeContents(pagename, e, pageTransition){ //Sabin 20151027
        offlinehelper.currentpage=pagename;
        displayedLogoutAlert = 0;    
        pageTransition = pageTransition || $.mobile.defaultPageTransition;
        var cpageid = $($.mobile.activePage).attr("id");
        if (cpageid == "popUp1" || cpageid == "popUp2" || cpageid == "popUp3") {
            if (Training.editedTrainingID > 0) {
                pagename = 'Tidigare';
                Training.editedTrainingID = 0;
            }
        }
        $.mobile.showPageLoadingMsg("");
        if (pagename == "TrainingZone_1_1") {
             $('.pagename').text("TrainingZone_1_1");
            pageTransition = "none";
        }
        $('.pagename').text(pagename);
        $.mobile.changePage("#" + pagename, {

            transition: "none",
            reloadPage :false
        });
        
      
        if (e != null && e != undefined) {
            e.preventDefault();
            e.stopPropagation();
        }

        e.preventDefault();
    }

    // Input Check funtions
    function checkinput(str) {
        return str.replace(/\n/g, '\\n').replace(/"/g, '\\"');
    }

    //
    //modify Date String
    function modifyDateString(SelDateString) {
        return SelDateString.replace(/-/g, '/');
    }
    // to check page
    //Extract Image Encoded data
    function GetEncodedImageData(dataURL) {
        if (dataURL != "") {
            return dataURL.substring(dataURL.indexOf(',') + 1);
        } else {
            return "";
        }
    }

    function displayHtml(data) {
        if (data)
            return data.replace(/\n/g, '<br/>');
        else
            return '';
    }
    // getNumber of lines in textbox
    function ApplyLineBreaks(strTextAreaId) {
        var oTextarea = document.getElementById(strTextAreaId);
        if (oTextarea.wrap) {
            oTextarea.setAttribute("wrap", "off");
        } else {
            oTextarea.setAttribute("wrap", "off");
            var newArea = oTextarea.cloneNode(true);
            newArea.value = oTextarea.value;
            oTextarea.parentNode.replaceChild(newArea, oTextarea);
            oTextarea = newArea;
        }

        var strRawValue = oTextarea.value;
        oTextarea.value = "";
        var nEmptyWidth = oTextarea.scrollWidth;
        var nLastWrappingIndex = -1;
        for (var i = 0; i < strRawValue.length; i++) {
            var curChar = strRawValue.charAt(i);
            if (curChar == ' ' || curChar == '-' || curChar == '+')
                nLastWrappingIndex = i;
            oTextarea.value += curChar;
            if (oTextarea.scrollWidth > nEmptyWidth) {
                var buffer = "";
                if (nLastWrappingIndex >= 0) {
                    for (var j = nLastWrappingIndex + 1; j < i; j++)
                        buffer += strRawValue.charAt(j);
                    nLastWrappingIndex = -1;
                }
                buffer += curChar;
                oTextarea.value = oTextarea.value.substr(0, oTextarea.value.length - buffer.length);
                oTextarea.value += "\n" + buffer;
            }
        }
        oTextarea.setAttribute("wrap", "");
    }

    /********************************** Jquery Addtional Functions ***************************/
    jQuery.fn.compare = function(t) {
        if (this.length != t.length) {
            return false;
        }
        var a = this.sort(),
            b = t.sort();
        for (var i = 0; t[i]; i++) {
            if (a[i] !== b[i]) {
                return false;
            }
        }
        return true;
    };



    function checkloginDetails() {
        var rememberedUsername = $.jStorage.get('userEmail');
        if (!!rememberedUsername && rememberedUsername.length > 0) {
            $("#txtEmail").val(rememberedUsername);
        }


        if (localStorage.getItem("pwd") != null && localStorage.getItem("pwd") != '' && localStorage.getItem("pwd") != "undefined" && localStorage.getItem("Email") != null && localStorage.getItem("Email") != '' && localStorage.getItem("Email") != "undefined") {
            $("#txtPwd").val(localStorage.getItem("pwd"));
            $("#txtEmail").val(localStorage.getItem("Email"));
            fnLogin();
            //deviceheight=$($.mobile.activePage).css("min-height").replace("px","");
        }
    }

    function onDeviceReady() {
        console.log("Device ready");

        if(StatusBar){
            StatusBar.backgroundColorByHexString("#ffcc00");
        }

         Parse.serverURL=parseApiAddress;
        if(typeof cordova.plugins!="undefined"){ //sabin 112412
            console.warn("cordova.plugins is defined");
            if(typeof cordova.plugins.backgroundMode!="undefined"){
                console.warn("cordova.plugins.backgroundMode is defined");
                cordova.plugins.backgroundMode.setDefaults({ text:'Tap to open.'});
                cordova.plugins.backgroundMode.enable();
            }else{
                console.warn("cordova.plugins.backgroundMode is NOT defined");
            }
        }

       if(typeof parsePlugin!="undefined"){
            if(IsIDevice){ //In android to avoid crash Parse is already initialized from Java. Look platforms\android\src\com\bupsll\bip\bipapp.java
                try{

                    parsePlugin.initialize(parse_app_id, parse_client_id, function() {
                         console.warn("Parse initialized successfully");
                    }, function(e) {
                        alert('error');
                    });
                }catch(e){
                    console.warn("Can't be initialized");
                }
            }
            parsePlugin.getInstallationId(function(id) {
                console.warn("Installation id is "+id);
                $.jStorage.set('DeviceID', id);

            }, function(e) {
                alert('error');
            });
        }
        
       
        
       // $.mobile.defaultPageTransition = 'slide';
       // console.log("Device is ready");
        /**********For Push Alywas call First************
        if (IsAndroid || IsIDevice)
        {
            if(IsAndroid){
        window.plugins.pushnotification.list(
                function(r){printResult(r);},
                function(e){printError(e);}
            );
            }else{
                window.plugins.pushNotification.startNotify();
                registerAPN();
            }
        */
       // StatusBar.hide();
       // console.log("Now initializing parse with "+parse_app_id+" "+ parse_client_id);
        DeviceID = device.uuid;
        DeviceName = device.name;
     
        $.jStorage.set('DeviceName', DeviceName);
        // console.log("Parse initializing");
        // alert("Device is now ready");
      
 
        // var query = new Parse.Query(Parse.User);
        // query.find({
        //   success: function(users) {
        //     for (var i = 0; i < users.length; ++i) {
        //       console.log(users[i].get('username'));
        //     }
        //   }
        // });
        
        /**********For Push Alywas call First*************/
        document.removeEventListener("backbutton", backKeyDown, false);
        document.addEventListener("backbutton", backKeyDown, false);
    }


    function onBodyLoad() {
         //offlinehelper.loggingOut = false;
       //Parse.initialize("yRMU9gqMRJiIM8bsnwnu3BKyiOdrBhSvKGwbnDMg", "O2y5fjM2qmJxAp9dSJbthqrfq6latbWyzkoTgwZU");
        document.addEventListener("deviceready",onDeviceReady,function(){ console.log('Device ready not fired'); });
        
        if(isWebVersion==true){
            $("html").attr("id","webapp");
        }

        if (window.location.hash != "") {
            enableIScroll();
        }
         if($(window).height() < 481){
          $("body").addClass("smallSizedBody");
        }


        
        /*if(typeof parsePlugin!="undefined" && IsAndroid){
            parsePlugin.initialize(parse_app_id, parse_client_id, function() {
                 console.warn("Parse for Android initialized successfully");
                   
            }, function(e) {
                alert('error');
            });

             parsePlugin.getInstallationId(function(id) {
                console.warn("Installation id is "+id);
                $.jStorage.set('DeviceID', id);

            }, function(e) {
                alert('error');
            });
        }*/
       /*if (typeof window.MyCls !== "undefined") {
           //   alert(window.MyCls.getIdentificationNumber());  
              $.jStorage.set('DeviceID', window.MyCls.getIdentificationNumber());
        }
        if (IsIDevice){
            //alert("I am running from iphone");
                try{
                 
                     cordova.exec(
                    successCallback, errorCallback,
                    'ParsePushPlugin', 'initialize',[parse_app_id,parse_client_id]
                    );
                   
                cordova.exec(
                    installationid, errorCallback,
                    'ParsePushPlugin', 'getInstallationId',[]
                );
                }catch(error){

                        
                }

        }*/
      //  offlinehelper.checkIfRunningFirstTime(); //uncomment this line only after implementing one phone one user functionality. added by sabin 290
        $("#wipe-cache").click(offlinehelper.clearAppCache); //added by sabin 290

        filehelper.getFileVersion();
        adjustContentHeight(); //added by sabin on 28th June. See function declaration for details
    }

    var errorCallback=function(e){
        //alert('error'+e);
    }

    

    function backKeyDown(e) {
        window.scrollTo(0, 1);
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    function fnQueryStringParam(name) {
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regexS = "[\\?&]" + name + "=([^&#]*)";
        var regex = new RegExp(regexS);
        var results = regex.exec(window.location.href);
        if (results == null)
            return null;
        else
            return results[1];
    }

    function fnQueryStringParamUrl(name, url) {
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regexS = "[\\?&]" + name + "=([^&#]*)";
        var regex = new RegExp(regexS);
        var results = regex.exec(url);
        if (results == null)
            return null;
        else
            return results[1];
    }

    function iOSversion() {
        if (/iP(hone|od|ad)/.test(navigator.platform)) {
            // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
            var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
            return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
        }
    }

    function didUserLeaveRating(stepNum, type) {

        //type = 2: check stepNum: 3,
        //type = 1: check stepNum 2
        if (type === 1 && stepNum === 2) {
            return $("#rangeslider_1_1 div.ui-slider-bg").attr("style") == 'display:none';
            //return parseInt($('#slider-fill').val(), 10) === 0;
        }

        if (type === 2 && stepNum === 3) {
            return $("#rangeslider_2_2 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style") == 'display:none';
            //return parseInt($('#slider-fill_2_2').val(), 10) === 0;

        }

        if (type === 2 && stepNum === 5) {
            return $("#rangeslider_2_4 div.ui-slider-bg.ui-btn-active.ui-btn-corner-all").attr("style") == 'display:none';
            //return parseInt($('#slider-fill_2_4').val(), 10) === 0;
        }

        return false;

    }
    var showDropdown = function(element) {
        var event;
        event = document.createEvent('MouseEvents');
        event.initMouseEvent('mousedown', true, true, window);
        element.dispatchEvent(event);
    };

    $(window).bind('load', function() {
        //master changes

        $("#btnLogin").click(fnLogin);
        $("#btnVerify").click(verifyDevice); //added by sabin 290
       

        checkloginDetails();

    });
    var sliderpercentage = [7.5, 18.5, 27.5, 36.5, 45.5, 55.5, 64.5, 74, 82, 91, 105.5];
    var sliderFillpercentage = [-10.0, -2.0, 8.0, 19.0, 29.0, 39.0, 49.0, 58.0, 67.0, 75.0, 86.0];

    function updateUserTrainingTime() {
        var totalSeconds = Training.process.getTrainingTime().total,
            sec = Training.process.getTrainingTime().actualSeconds,
            min = Training.process.getTrainingTime().actualMinutes;

        $('#training_span').val(('0' + min).slice(-2) + ':' + ('0' + sec).slice(-2));

        $('#training_span').scroller(mobiscrollTimeOptions);
        $('#defaultTimeSpent').addClass('bip_hidden');
        $('#editTimeSpent').removeClass('bip_hidden');
    }


    //added by sabin 6th September >> 
    function secondToTimeformat(seconds){
         var sec_num = parseInt(seconds, 10); // don't forget the second param
         var duration = moment.duration(sec_num,"seconds");
         var hours = duration.hours();
         var minutes = duration.minutes();
         var seconds = duration.seconds();

        if (hours   < 10) {hours   = "0"+hours;}
        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}

        var time    = hours+':'+minutes+':'+seconds;

        return time;
    }

    //added by sabin
    function isValidUser(){
         var loginUser =  $("#txtEmail").val().trim();
         var userdetails= $.jStorage.get('userdetails');
         if(userdetails!==null){
             if(userdetails.username.toLowerCase()!=loginUser.toLowerCase()){ //Nov 29
                msgBox("Du kan inte logga in med mer än en användare på samma telefon. För att byta användare avinstallera applikationen och installera om igen.");
                throw "stop execution";
             }
         }
            
    }
    //added by sabin 6th September <<
    
    //added by sabin 290
    function verifyDevice(){

        var userdetails = $.jStorage.get('userdetails');
        debugger;
        
        $vCode = $("#verification_code").val();
        $vPwd =  $.trim($("#txtPwd").val());
        var regex = /[0-9]{6}/;
        var str = $.trim($vCode);
        var errors= "";

        if(regex.test(str)===false && $vPwd==""){
            msgBox("Invalid verification code and password");
            throw "Invalid verification code and password";
        }else if(regex.test(str)===false && $vPwd!=""){
            msgBox("Invalid verification code");
            throw "Invalid verification code";
        }else if($vPwd==""){
            msgBox("Invalid password");
            throw "Invalid password";
        }else{
              device_id = window.device.uuid;
              json = '{"verification_code":"' + $("#verification_code").val().trim() + '","password":"' + $("#txtPwd").val().trim() + '","deviceId":"' + device_id + '","tokenkey":"' + dtoken + '","identificationumber":"'+ deviceinstallationid +'","devicetype":"'+dtype+'"}';
         
        }

       
    }
