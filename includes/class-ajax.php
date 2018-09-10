<?php
namespace WeDevs\FBAccountKit;

/**
 * Ajax Handler
 */
class Ajax {

    function __construct() {
        add_action( 'wp_ajax_nopriv_fb_account_kit_login', [ $this, 'process_login' ] );

        // admin profile
        add_action( 'wp_ajax_fb_account_kit_associate', [ $this, 'associate_phone' ] );
        add_action( 'wp_ajax_fb_account_kit_disconnect', [ $this, 'disconnect_phone' ] );
    }

    /**
     * Send a GET request to the API
     *
     * @param  string $url
     *
     * @return array
     */
    public function send_request( $url ) {
        $response = wp_remote_get( $url );
        $result   = wp_remote_retrieve_body( $response );

        return json_decode( $result, true );
    }

    /**
     * Authorize accountkit with a authorization code
     *
     * @param  string $code
     *
     * @return array
     */
    private function authorize( $code ) {
        $app_id  = fbak_get_fb_app_id();
        $secret  = fbak_get_fb_app_secret();
        $version = fbak_get_fb_app_version();

        $token_exchange_url = 'https://graph.accountkit.com/' . $version . '/access_token?' .
          'grant_type=authorization_code'.
          '&code=' . $code .
          "&access_token=AA|$app_id|$secret";
        $data              = $this->send_request( $token_exchange_url );
        $user_id           = $data['id'];
        $access_token = $data['access_token'];
        $refresh_interval  = $data['token_refresh_interval_sec'];
        $appsecret_proof   = hash_hmac('sha256', $access_token, $secret);

        // Get Account Kit information
        $me_endpoint_url = 'https://graph.accountkit.com/'.$version.'/me?'.
          'access_token=' . $access_token . '&appsecret_proof=' . $appsecret_proof;
        $me_data = $this->send_request($me_endpoint_url);

        return $me_data;
    }

    /**
     * Process user login
     *
     * @return void
     */
    public function process_login() {
        $me_data = $this->authorize( $_POST['code'] );

        $phone = isset($me_data['phone']) ? $me_data['phone']['number'] : '';
        $email = isset($me_data['email']) ? $me_data['email']['address'] : '';
        $id    = isset($me_data['id']) ? $me_data['id'] : 0;
        $user  = false;

        if ( $email ) {
            $user = $this->handle_email( $email );
        }

        if ( $phone ) {
            $user = $this->handle_phone( $phone, $id );

            // update the account kit reference
            update_user_meta( $user->ID, '_fb_accountkit_id', $id );
        }

        wp_set_auth_cookie( $user->ID, true );

        wp_send_json_success( array(
            'redirect' => home_url( '/' )
        ) );
    }

    /**
     * Associate phone number with a account
     *
     * @since 1.1.0
     *
     * @return void
     */
    public function associate_phone() {
        $me_data = $this->authorize( $_POST['code'] );

        $phone = isset($me_data['phone']) ? $me_data['phone']['number'] : '';
        $id    = isset($me_data['id']) ? $me_data['id'] : 0;

        if ( $id ) {
            update_user_meta( get_current_user_id(), '_fb_accountkit_id', $id );
        }

        wp_send_json_success();
    }

    /**
     * Disconnect a phone number
     *
     * @since 1.1.0
     *
     * @return void
     */
    public function disconnect_phone() {
        delete_user_meta( get_current_user_id(), '_fb_accountkit_id' );

        wp_send_json_success();
    }

    /**
     * Handle the user email response
     *
     * @param  string $email
     *
     * @return \WP_User
     */
    public function handle_email( $email ) {
        $user = get_user_by( 'email', $email );

        if ( ! $user ) {
            $username  = fbak_guess_username( $email );
            $user_pass = wp_generate_password( 12, false );

            $user_id   = wp_create_user( $username, $user_pass, $email );
            $user      = get_user_by( 'id', $user_id );
        }

        return $user;
    }

    /**
     * Handle the phone authentication response
     *
     * @param  string $phone
     * @param  integer $account_id
     *
     * @return \WP_User
     */
    public function handle_phone( $phone, $account_id ) {
        $user = fbak_get_user_by_ak_id( $account_id );

        if ( ! $user ) {
            $phone     = substr( $phone, 1 ); // remove the '+' sign
            $username  = $phone;
            $user_pass = wp_generate_password( 12, false );
            $email     = $phone . '@accountkit.com'; // generate a fake email address

            $user_id   = wp_create_user( $username, $user_pass, $email );

            update_user_meta( $user_id, 'phone_number', $phone );

            $user = get_user_by( 'id', $user_id );
        }

        return $user;
    }
}
