var host = window.location.hostname;
var webServiceURL;

if (host==='barninternetprojektet.se') {
	webServiceURL = "https://barninternetprojektet.se/index.php/minapp/user/";
}else if(host==='bip.websearchpro.net'){
	webServiceURL = "https://bip.websearchpro.net/index.php/minapp/user/";
}else if(host==='dev.websearchpro.net'){
	webServiceURL = "http://dev.websearchpro.net/bipv4/index.php/minapp/user/";
}else{
	webServiceURL = "http://localhost/bip/index.php/minapp/user/";
}
// console.log(webServiceURL);

var webServiceURLProxy = "";
var IsInternetExplorer = false;

var webServiceCallStack = [];

function callWebService(webmethodname, json, callback) {
	$.mobile.showPageLoadingMsg("");
	var req = $.ajax({
		url: ((IsInternetExplorer) ? webServiceURLProxy : webServiceURL) + webmethodname,
		type: 'POST',
		//dataType: 'json',
		data: json,
		//contentType: 'application/json; charset=utf-8',
		//headers: { "cache-control": "no-cache" },
		success: function(response) {
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
			$.mobile.hidePageLoadingMsg();
		},
		error: function(xhr, message) {
			if (message !== 'abort')

				msgBox('Server not responding! Please try later.');
			$.mobile.hidePageLoadingMsg();
		},
		complete: function(xhr) {
			//Remove from callstack
			webServiceCallStack = $.grep(webServiceCallStack, function(val) {
				return val !== xhr;
			});
		}
	});

	webServiceCallStack.push(req);
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
