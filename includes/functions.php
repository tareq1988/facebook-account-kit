<?php

/**
 * Get Facebook APP ID
 *
 * @return string
 */
function fbak_get_fb_app_id() {
    return fbak_get_option( 'app_id', 'fb_account_kit' );
}

/**
 * Get Facebook APP Secret
 *
 * @return string
 */
function fbak_get_fb_app_secret() {
    return fbak_get_option( 'app_secret', 'fb_account_kit' );
}

/**
 * Get Facebook APP version
 *
 * @return string
 */
function fbak_get_fb_app_version() {
    return 'v1.1';
}

/**
 * Should the phone login be displayed
 *
 * @return boolean
 */
function fbak_phone_displayed() {
    $option = fbak_get_option( 'methods', 'fb_account_kit', array('phone' => 'phone', 'email' => 'email') );

    return isset( $option['phone'] );
}

/**
 * Should the email login be displayed
 *
 * @return boolean
 */
function fbak_email_displayed() {
    $option = fbak_get_option( 'methods', 'fb_account_kit', array('phone' => 'phone', 'email' => 'email') );

    return isset( $option['email'] );
}

/**
 * Should the email login be displayed
 *
 * @return string
 */
function fbak_phone_label() {
    return fbak_get_option( 'phone_label', 'fb_account_kit', __( 'Login with SMS', 'fb-account-kit' ) );
}

/**
 * Should the email login be displayed
 *
 * @return string
 */
function fbak_email_label() {
    return fbak_get_option( 'email_label', 'fb_account_kit', __( 'Login with Email', 'fb-account-kit' ) );
}

/**
 * Description instructions
 *
 * @return string
 */
function fbak_description() {
    return fbak_get_option( 'description', 'fb_account_kit', __( 'Save time by logging-in with your Phone number or Email address, no password is needed.', 'fb-account-kit' ) );
}

/**
 * Get an user by facebook account kit id
 *
 * @param  integer $account_kit_id
 *
 * @return \WP_user|false
 */
function fbak_get_user_by_ak_id( $account_kit_id ) {
    global $wpdb;

    $user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_fb_accountkit_id' AND meta_value = %d", $account_kit_id ) );

    if ( $user_id ) {
        return new WP_User( $user_id );
    }

    return false;
}

/**
 * Guess an username from email address
 *
 * @param  string $email
 *
 * @return string
 */
function fbak_guess_username( $email ) {
    $username = sanitize_user( current( explode( '@', $email ) ), true );

    // Ensure username is unique.
    $append     = 1;
    $o_username = $username;

    while ( username_exists( $username ) ) {
        $username = $o_username . $append;
        $append++;
    }

    return $username;
}

/**
 * Get the value of a settings field
 *
 * @param string $option settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function fbak_get_option( $option, $section, $default = '' ) {

    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}
