;(function($) {

    AccountKit_OnInteractive = function(){
        AccountKit.init({
            appId: FBAccountKit.app_id,
            state: FBAccountKit.nonce,
            version: FBAccountKit.version,
            fbAppEventsEnabled: true,
            redirect: FBAccountKit.redirect
        });
    };

    // login callback
    function loginCallback(response) {
        if (response.status === "PARTIALLY_AUTHENTICATED") {
            var data = {
                    code: response.code,
                    csrf: response.csrf,
                    action: 'fb_account_kit_login'
            };

            $('.fb-ackit-wrap').addClass('loading');

            // Send code to server to exchange for access token
            $.post(FBAccountKit.ajaxurl, data, function(response, textStatus, xhr) {
                console.log(response);
                window.location.href = response.data.redirect;
            });
        }
    }

    // phone form submission handler
    window.smsLogin = function() {
        AccountKit.login('PHONE',{},loginCallback);
    }

    // email form submission handler
    window.emailLogin = function() {
        AccountKit.login('EMAIL',{},loginCallback);
    }

})(jQuery);
