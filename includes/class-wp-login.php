<?php
namespace WeDevs\FBAccountKit;

/**
 * WP Login
 */
class WP_Login {

    function __construct() {
        add_action( 'login_form', [ $this, 'login_form' ] );
        add_action( 'login_head', [ $this, 'login_scripts' ] );
    }

    public function login_form() {
        include __DIR__ . '/views/wp-login.php';
    }

    public function login_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'fb-account-kit' );
        wp_enqueue_script( 'fb-account-kit-script' );
        wp_enqueue_script( 'fb-account-kit-login', FB_ACCOUNT_KIT_ASSETS . '/js/wp-login.js', array( 'jquery', 'fb-account-kit' ) );

        wp_enqueue_style( 'fb-account-kit-style', FB_ACCOUNT_KIT_ASSETS . '/css/wp-login.css' );
    }
}
