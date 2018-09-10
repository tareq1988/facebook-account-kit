;(function($) {

    AccountKit_OnInteractive = function(){
        AccountKit.init({
            appId: FBAccountKit.app_id,
            state: FBAccountKit.nonce,
            version: FBAccountKit.version,
            fbAppEventsEnabled: true
        });
    };

    // login callback
    function loginCallback(response) {
        if (response.status === "PARTIALLY_AUTHENTICATED") {
            var data = {
                code: response.code,
                csrf: response.csrf,
                action: 'fb_account_kit_associate'
            };

            // Send code to server to exchange for access token
            $.post(FBAccountKit.ajaxurl, data, function() {
                window.location.reload();
            });
        }
    }

    // phone form submission handler
    window.smsLogin = function() {
        AccountKit.login('PHONE',{},loginCallback);
    }

    window.fbAcDisconnect = function() {

        if ( confirm( 'Are you sure?' )) {
            var data = {
                action: 'fb_account_kit_disconnect'
            };

            $.post(FBAccountKit.ajaxurl, data, function() {
                window.location.reload();
            });
        }
    }

})(jQuery);
