//Page Navigation
var PageFollowStack = new Array();
var scrolls = [];
//var currentPageId='';
// common message box
function msgBox(message) {
    if (navigator.notification != undefined && navigator.notification != null) {
        navigator.notification.alert(message, alertDismissed, 'BIP', 'Ok');
        alert(message);
    }
    else {
        alert(message);
    }
}

function confirmBox(message, cCallback) {
    if (navigator.notification != undefined && navigator.notification != null) {
        navigator.notification.confirm(
			 	message,  // message
	            cCallback,        // callback to invoke with index of button pressed
	            'BIP',           // title
	            'Yes,No'          // buttonLabels
	        );
    }
    else {
        if (confirm(message)) {
            return cCallback(1);
        }
        else {
            return cCallback(2);
        }
    }
}
function alertDismissed() {
    // do something
}

function CheckSession() {
    if ($.jStorage.get('TokenKey') == null) {
        gotoLoginPage();
    }
    else {
        gotoTargetPage();
    }
}

// validation for login page
function checkLoginValues() {
    if ($("#txtEmail").val().trim() == ''){
        msgBox('Vänligen fyll i e-post');
    }
    else if ($("#txtPwd").val().trim() == ''){
        msgBox('Vänligen fyll i lösenord');
    }
    else{

    	return true;
    }

}

// call login function
function fnLogin() {
    if (checkLoginValues()) {

    	//For Push Details Save With Login, reduce extra request
        //changepage('TrainingList');
        //setTimeout(function(){enableIScroll();},1000);
        //return;

        var json = '';
	    	if (IsAndroid || IsIDevice){
                if(AppId==='' || AppId===null)
	    			AppId=$.jStorage.get('AppId')===null?'':$.jStorage.get('AppId');
	    			if(DeviceID==='' || DeviceID===null)
	    			DeviceID=$.jStorage.get('DeviceID')===null?'':$.jStorage.get('DeviceID');
	    			if(DeviceType==='' || DeviceType===null)
	    			DeviceType=$.jStorage.get('DeviceType')===null?'':$.jStorage.get('DeviceType');
	    			if(DeviceName==='' || DeviceName===null)
	    			DeviceName=$.jStorage.get('DeviceName')===null?'':$.jStorage.get('DeviceName');
	    	}
    	DeviceID="ABBBSBS";//"appid":"'+AppId+'",
        //alert(DeviceID);
        json = '{"username":"' + $("#txtEmail").val().trim() + '","password":"' + $("#txtPwd").val().trim() + '","deviceId":"'+DeviceID+'","TokenKey":"' + (new Date().getTimezoneOffset() * -1) + '"}';
        var webmethodname = 'validateuser';
        callWebService(webmethodname, json, function (response) {
            if (response.status === "ok") {
                if (response.data !== "invalid") {
                    //changepage('TrainingList');
                    $.jStorage.set('userdetails', response.data);
                    if (response.data.sms == "true") {
                        $("#smserror").hide();
                        changepage("smsLogin");
                    } else {
                        Training.setTrainings();
                    }
                } else {
                    msgBox('Either Anvandarnamn or Losenord is incorrect');
                }
            } else if (response.status === "error") {
                msgBox(response.message);
            }
            else {
                msgBox('Error occured Try again.');
            }
        });
    }

}

// call LogOut function
function fnLogOut() {
    confirmBox('Are you sure you want to log out?', function (button) {

        if (button == 1) {
            LogoutServer();
        }
    });
}

// Destroy session from server
function LogoutServer() {
    if(IsAndroid || IsIDevice){
		if(DeviceID==='' || DeviceID===null)
			DeviceID=$.jStorage.get('DeviceID')===null?'':$.jStorage.get('DeviceID');
    	var json = '{"TokenKey":"' + $.jStorage.get('TokenKey') + '","DeviceID":"'+DeviceID+'"}';

    }
    else
    var json = '{"TokenKey":"' + $.jStorage.get('TokenKey') + '"}';

    var webmethodname = 'Login/Logout';
    callWebService(webmethodname, json, function (response) {
        //if (response.status == "ok") {
        localStorage.clear();
        //$.jStorage.flush();
        gotoLoginPage();
        //}
    });
}



// call login page
function gotoLoginPage() {
    //changepage('pageLogin');
    $.jStorage.set('gid', null);
    $.jStorage.set('TokenKey', null);
    window.location = window.location.href.replace(window.location.hash, "");
    PageFollowStack = new Array();
}
// common function for change page
function changepage(pagename) {
    $.mobile.defaultPageTransition = 'slide';
    //$.mobile.showPageLoadingMsg('a','Loading...', false);
    $.mobile.changePage("#" + pagename);
    //$.mobile.hidePageLoadingMsg();
}

function backpage(pagename, e) {
    var cpageid = $($.mobile.activePage).attr("id");
    if(cpageid=="popUp1" || cpageid=="popUp2" || cpageid=="popUp3"){
        if(Training.editedTrainingID>0){
            pagename='Tidigare';
            Training.editedTrainingID=0;
        }
    }
    $.mobile.showPageLoadingMsg("");
    $.mobile.changePage("#" + pagename, { changeHash: true,reverse: true });
    $.mobile.hidePageLoadingMsg();
    if (e != null && e != undefined) {
        e.preventDefault();
        e.stopPropagation();
    }
    if (pagename == "TrainingList") {
        Training.setTrainings();
    }
    return false;
}
// Input Check funtions
function checkinput(str) {
    return str.replace(/\n/g,'\\n').replace(/"/g, '\\"');
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
    }
    else {
        return "";
    }
}
function displayHtml(data) {
	if(data)
		return data.replace(/\n/g, '<br/>');
	else
		return '';
}
// getNumber of lines in textbox
function ApplyLineBreaks(strTextAreaId) {
    var oTextarea = document.getElementById(strTextAreaId);
    if (oTextarea.wrap) {
        oTextarea.setAttribute("wrap", "off");
    }
    else {
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
jQuery.fn.compare = function (t) {
    if (this.length != t.length) { return false; }
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
    if (localStorage.getItem("pwd") != null && localStorage.getItem("pwd") != '' && localStorage.getItem("pwd") != "undefined"
    		&& localStorage.getItem("Email") != null && localStorage.getItem("Email") != '' && localStorage.getItem("Email") != "undefined") {
        $("#txtPwd").val(localStorage.getItem("pwd"));
        $("#txtEmail").val(localStorage.getItem("Email"));
        fnLogin();
        //deviceheight=$($.mobile.activePage).css("min-height").replace("px","");
    }
}


function onBodyLoad() {
                        //alert('1');
    document.addEventListener("deviceready", onDeviceReady, false);
    if (window.location.hash != "") {
        enableIScroll();
    }
}

function onDeviceReady() {
                       $.mobile.defaultPageTransition = 'slide';
                       console.log('ondeviceReady');
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
	DeviceID=device.uuid;
	DeviceName=device.name;
	$.jStorage.set('DeviceID', DeviceID);
	$.jStorage.set('DeviceName', DeviceName);


	/**********For Push Alywas call First*************/
    document.removeEventListener("backbutton", backKeyDown, false);
    document.addEventListener("backbutton", backKeyDown, false);


}

function backKeyDown(e) {
    window.scrollTo(0, 1);
    e.preventDefault();
    e.stopPropagation();
    return false;
}

function fnQueryStringParam(name)
{
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec(window.location.href);
	if( results == null )
		return null;
	else
		return results[1];
}
function fnQueryStringParamUrl(name,url)
{
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec(url);
	if( results == null )
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

$(window).bind('load',function () {
    //End
    $("#btnLogin").click(function (e) {
        fnLogin();
    });

    checkloginDetails();

});
var sliderpercentage = [7.5, 18.5, 27.5, 36.5, 45.5, 55.5, 64.5, 74, 81.5, 90.5, 105.5];
var sliderFillpercentage = [-10.0, -2.0, 8.0, 19.0, 29.0, 39.0, 49.0, 58.0, 67.0, 75.0, 86.0];
