var ParsePushPlugin = {
    register: function(regParams, successCallback, errorCallback) {
        cordova.exec(
            successCallback, errorCallback,
            'ParsePushPlugin', 'register',
            [regParams]
        );
    },

    getInstallationId: function(successCallback, errorCallback) {
        cordova.exec(
            successCallback, errorCallback,
            'ParsePushPlugin', 'getInstallationId',
            []
        );
    },

    getInstallationObjectId: function(successCallback, errorCallback) {
        cordova.exec(
            successCallback, errorCallback,
            'ParsePushPlugin', 'getInstallationObjectId',
            []
        );
    },

    getSubscriptions: function(successCallback, errorCallback) {
        cordova.exec(
            successCallback, errorCallback,
            'ParsePushPlugin', 'getSubscriptions',
            []
        );
    },

    subscribe: function(channel, successCallback, errorCallback) {
        cordova.exec(
            successCallback,
            errorCallback,
            'ParsePushPlugin',
            'subscribe',
            [ channel ]
        );
    },

    unsubscribe: function(channel, successCallback, errorCallback) {
        cordova.exec(
            successCallback,
            errorCallback,
            'ParsePushPlugin',
            'unsubscribe',
            [ channel ]
        );
    }
};


cordova.addConstructor(function() {


cordova.addPlugin("ParsePushPlugin", new PushNotification());
//Register the native class of plugin with PhoneGap
//PluginManager.addService("PushNotificationPlugin","com.appstockholm.zoud.PushNotificationPlugin");

});