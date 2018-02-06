;(function($) {

    var body = $( 'body' ),
        loginForm = $( '#loginform' ),
        toggle    = $( '.fb-ackit-toggle' );

    loginForm.append( $( '.fb-ackit-wrap' ) );

    toggle.on('click', 'a', function(e) {
        e.preventDefault();

        body.toggleClass( 'fb-ackit-form-display' );
    });

    // trigger to default
    body.toggleClass( 'fb-ackit-form-display' );

})(jQuery);
