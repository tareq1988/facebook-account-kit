<?php
namespace WeDevs\FBAccountKit;

/**
 * Admin Class
 */
class Admin {

    private $settings_api;

    function __construct() {
        $this->settings_api = new \WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );

        add_filter( 'plugin_action_links_' . plugin_basename( FB_ACCOUNT_KIT_FILE ), array( $this, 'plugin_action_links' ) );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( __( 'Account Kit', 'facebook-account-kit' ), __( 'Account Kit', 'facebook-account-kit' ), 'manage_options', 'account-kit', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'fb_account_kit',
                'title' => __( 'Account Kit Settings', 'facebook-account-kit' )
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'fb_account_kit' => array(
                array(
                    'name'              => 'app_id',
                    'label'             => __( 'Facebook App ID', 'facebook-account-kit' ),
                    'desc'              => sprintf( __( 'The app ID of your created Facebook app. <a href="%s" target="_blank">Create a new app</a>.', 'facebook-account-kit' ), 'https://developers.facebook.com/apps/' ),
                    'placeholder'       => __( 'Facebook App ID', 'facebook-account-kit' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'app_secret',
                    'label'             => __( 'Account Kit App Secret', 'facebook-account-kit' ),
                    'desc'              => __( 'The <strong>Account Kit</strong> app secret. This is different than the Facebook app secret.', 'facebook-account-kit' ),
                    'placeholder'       => __( 'Account Kit App Secret', 'facebook-account-kit' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'    => 'methods',
                    'label'   => __( 'Login Options', 'wedevs' ),
                    'desc'    => __( 'Which methods the users should be able to login/register.', 'wedevs' ),
                    'type'    => 'multicheck',
                    'default' => array('phone' => 'phone', 'email' => 'email'),
                    'options' => array(
                        'phone'   => __( 'Login with Phone', 'facebook-account-kit' ),
                        'email'   => __( 'Login with Email', 'facebook-account-kit' ),
                    )
                ),
                array(
                    'name'              => 'description',
                    'label'             => __( 'Description', 'facebook-account-kit' ),
                    'type'              => 'textarea',
                    'default'           => __( 'Save time by logging-in with your Phone number or Email address, no password is needed.', 'facebook-account-kit' )
                ),
                array(
                    'name'              => 'phone_label',
                    'label'             => __( 'Phone Label', 'facebook-account-kit' ),
                    'type'              => 'text',
                    'default'           => __( 'Login with SMS', 'facebook-account-kit' ),
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'email_label',
                    'label'             => __( 'Email Label', 'facebook-account-kit' ),
                    'default'           => __( 'Login with Email', 'facebook-account-kit' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
            ),
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Plugin action links
     *
     * @param  array $links
     *
     * @return array
     */
    function plugin_action_links( $links ) {

        $links[] = '<a href="' . admin_url( 'options-general.php?page=account-kit' ) . '">' . __( 'Settings', 'facebook-account-kit' ) . '</a>';

        return $links;
    }
}
