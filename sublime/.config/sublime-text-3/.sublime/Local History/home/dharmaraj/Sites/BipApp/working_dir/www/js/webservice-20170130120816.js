// var webServiceURL = 'http://bip.websearchpro.net/index.php/minapp/api/';
var webServiceURL = 'http://bip.local/index.php/minapp/api/';

var webServiceURLProxy = "";
var IsInternetExplorer = false;

var webServiceCallStack = [];

function callWebService(webmethodname, json, callback, completeCallback,showloader) {
    completeCallback = completeCallback || $.noop;
    console.log(showloader);
    if(showloader==undefined)
       // $.mobile.showPageLoadingMsg("");
    
    
        switch(webmethodname){
            case 'validateuser':
                console.log("Going to validate user");
                offlinehelper.ValidateUser(json,callback);
                break;
            case 'activeTasks':
                console.log("Going to Get active tasks"); 
                offlinehelper.activeTasks(json,callback);
                break;  
            case 'getOldTrainings':
                console.log("Going to Get old trainings");  
                offlinehelper.getOldTrainings(json,callback);
                break;  
            case 'GetActivityperweek':
                console.log("Going to Get old trainings");  
                offlinehelper.GetActivityperweek(json,callback);
                break;     
                case 'GetActivityperday':
                console.log("Going to Get old trainings");  
                offlinehelper.GetActivityperday(json,callback);
                break;    
            case 'Getestimatesfromstart':
                console.log("Going to Get Getestimatesfromstart");  
                offlinehelper.Getestimatesfromstart(json,callback);
                break;   
            case 'saveTraining':
                var response={status:'ok'};
                console.log(json);
                offlinehelper.saveTraining(json,callback);
                console.log("Going to Save training");  

                callback(response);
                break;  
            case 'saveReviewedTraining':
                var response={status:'ok'};
              
                console.log(json);
                offlinehelper.saveReviewedTraining(json,callback);
                console.log("Going to Save training");  
                callback(response);
                break;   
            case 'saveReminder':
                var response={status:'ok'};
              
                console.log(json);
                offlinehelper.saveReminder(json,callback);
                console.log("Going to Save training");  
                callback(response);
                break;   
            case 'fetchRegistrations':
                var response={status:'ok'};
              
                console.log(json);
                offlinehelper.fetchRegistrations(json,callback);
                console.log("Going to fetch registrations");  
                callback(response);
                break;   
            case 'fetchRegistrationSteps':
                var response={status:'ok'};
              
                console.log(json);
                offlinehelper.fetchRegistrationSteps(json,callback);
                console.log("Going to fetch registrations");  
               
                break;  
            case 'fetchHomeworks':
                var response={status:'ok'};
                offlinehelper.fetchHomeworks(json,callback);
                console.log("Going to fetch registrations");  
               
                break;
            case 'checkModulesEnabled':  
                var response={status:'ok'};
                offlinehelper.checkModulesEnabled(json,callback);
                console.log("Going to check enabled modules");  
               
            case 'fetchCrisisplans':
                var response={status:'ok'};
              
                console.log(json);
                offlinehelper.fetchCrisisplans (json,callback);
                console.log("Going to fetch registrations");  
               
                break;  
            case 'markHomeworkRead':
                var response={status:'ok'};
              
                console.log(json);
                offlinehelper.markHomeworkRead(json,callback);
                console.log("Going to fetch registrations");  
               
                break;   
            case 'markCrisisplanRead':
                var response={status:'ok'};
              
                console.log(json);
                offlinehelper.markCrisisplanRead(json,callback);
                console.log("Going to fetch registrations");  
               
                break;
            case 'listModules':
                var response = {status: 'ok'};
                offlinehelper.listModules(json,callback);
                console.log("fetching and listing modules");
                break;
            case 'checkIfModuleHasSkills':
                var response = {status: 'ok'};
                offlinehelper.checkIfModuleHasSkills(json,callback);
                console.log("checking what types of skills available for the module.");
                break;
            case 'listSkillsItems':
                var response = {status: 'ok'};
                offlinehelper.listSkillsItems(json,callback);
                console.log("Listing the skills");
                break;
            case 'getSKillDetails':
                var response = {status: 'ok'};
                offlinehelper.getSKillDetails(json,callback);
                console.log("Getting detail of selected skill whether it be skill, thoughts or exposure");
                break;
            case 'feelingStatistics':
                var response = {status: 'ok'};
                offlinehelper.feelingStatistics(json,callback);
                console.log("Fetching feeling Statistics");
                break;
            case 'feelingLists':
                var response = {status: 'ok'};
                offlinehelper.feelingLists(json,callback);
                console.log("Fetching feelings with its details");
                break;
            case 'showFeelingDefinitions':
                 var response = {status: 'ok'};
                offlinehelper.showFeelingDefinitions(json,callback);
                console.log("Fetching feelings definitions");
                break;
            case 'fetchExposureSteps':
                var response = {status: 'ok'};
                offlinehelper.fetchExposureSkillsSteps("exposure",json, callback);
                console.log("Fetching exposure steps");
                break;
            case 'fetchSkillsSteps':
                var response = {status: 'ok'};
                offlinehelper.fetchExposureSkillsSteps("skills",json, callback);
                console.log("Fetching skills steps");
                break;
            default:
                console.log(webmethodname+" is not found. Invalid webservice name");

        }

    
    return;
}


function callWebServiceLive(webmethodname, json, callback, completeCallback,showloader) {

    // sqlhelper.db.transaction(function(tx,json) {
    //     tx.executeSql("SELECT * FROM tbl_user WHERE app_user_id = ?", [json.app_user_id], function(tx,res){
    //         console.log('Record count (expected to be 13): ');
    //     });
    // }, function(err){
    //     alert(err.message);
    // });    
   
    completeCallback = completeCallback || $.noop;
    // console.log(showloader);
    if(showloader==undefined)
        $.mobile.showPageLoadingMsg("");
    

    
    // var checdata=sqlhelper.checkLocal(webmethodname,json,function(data){

    //     if(data!=false){

    //     }else{
                var req = $.ajax({
                        url: ((IsInternetExplorer) ? webServiceURLProxy : webServiceURL) + webmethodname,
                        type: 'POST',
                        //dataType: 'json',
                        data: json,
                        //contentType: 'application/json; charset=utf-8',
                        //headers: { "cache-control": "no-cache" },
                        success: function(response) {
                           // console.log(response);
                            if (response) {

                               
                                response = $.parseJSON(response); // Remove once in mobile
                                if (response.data === "NotAuthorized") {
                                    //msgBox('Your session has been expired. Please login again!');
                                    gotoLoginPage();
                                    return;
                                }
                                if (callback) {
                                    callback(response);
                                }
                            } else {
                                callback(null);
                            }
                        
                        },
                        error: function(xhr, message) {
                          //  showalert("Ingen internetuppkoppling");
                           /* if (message !== 'abort')
                                msgBox(MESSAGE.NO_INTERNET);*/

                            offlinehelper.syncstarted=false;
                            $.mobile.hidePageLoadingMsg();
                        },
                        complete: function(xhr) {
                            //Remove from callstack
                            webServiceCallStack = $.grep(webServiceCallStack, function(val) {
                                return val !== xhr;
                            });
                            completeCallback();
                            //console.log(webServiceCallStack);
                        }
                    });

    //     }
       
    // });
  
   
    return;
}


//this method doesnt give any type of error if webservice is not available i.e error part is commented out
function callWebServiceLiveSilently(webmethodname, json, callback, completeCallback,showloader) {
   
    completeCallback = completeCallback || $.noop;
    console.log(showloader);
    if(showloader==undefined)
        $.mobile.showPageLoadingMsg("");
    
    // var checdata=sqlhelper.checkLocal(webmethodname,json,function(data){

    //     if(data!=false){

    //     }else{
   // console.log("Checking if app is running for first time");
    //offlinehelper.checkFirstTime();
    var req = $.ajax({
            url: ((IsInternetExplorer) ? webServiceURLProxy : webServiceURL) + webmethodname,
            type: 'POST',
            data: json,
            //contentType: 'application/json; charset=utf-8',
            //headers: { "cache-control": "no-cache" },
            success: function(response) {
               // console.log(response);
                if (response) {

                   response.webservicename=webmethodname;
                    response = $.parseJSON(response); // Remove once in mobile
                    if (response.data === "NotAuthorized") {
                        //msgBox('Your session has been expired. Please login again!');
                        gotoLoginPage();
                        return;
                    }
                    if (callback) {
                        callback(response,webmethodname);
                    }
                } else {
                    callback(null,webmethodname);
                }
                if(showloader==undefined)
                    $.mobile.hidePageLoadingMsg();
            },
            error: function(xhr, message) {
              // //  showalert("Ingen internetuppkoppling");
              //   if (message !== 'abort')
              //       msgBox(MESSAGE.NO_INTERNET);
              //   $.mobile.hidePageLoadingMsg();
              $.mobile.hidePageLoadingMsg();
            },
            complete: function(xhr) {
                //Remove from callstack
                webServiceCallStack = $.grep(webServiceCallStack, function(val) {
                    return val !== xhr;
                });
                completeCallback();
                //console.log(webServiceCallStack);
            }
        });

  
   
    return;
}


function callWebServicewithImage(webmethodname, json, callback) {
    $.mobile.showPageLoadingMsg('a', fnGetValue('Page_LoadingMsg'), false);
    if (imgui !== null) {
        var options = new FileUploadOptions();
        options.fileKey = "file";
        options.mimeType = "image/jpeg";
        options.chunkedMode = false;
        var params = new Object();
        params.jsonString = json;
        options.params = params;

        var ft = new FileTransfer();
        //alert('imageURI = ' + imgui);
        ft.upload(imgui, webServiceURL + webmethodname, callback, fail, options);
        //$.mobile.hidePageLoadingMsg();
    } else {
        callWebService(webmethodname, json, callback);
    }
}

function fail(error) {
    $.mobile.hidePageLoadingMsg();
    msgBox("FileErrorCode=" + error.code);
}

function ClearWebServiceCallStack() {
    $.each(webServiceCallStack, function(i) {
        var req = webServiceCallStack[i]
        if (req && req.abort) {
            req.abort();
        }
    });

    webServiceCallStack = [];
}
