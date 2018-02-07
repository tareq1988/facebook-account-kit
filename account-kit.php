<?php
/*
Plugin Name: Facebook Account Kit
Plugin URI: https://github.com/tareq1988/facebook-account-kit
Description: Facebook Account Kit integration for WordPress
Version: 1.0
Author: Tareq Hasan
Author URI: https://tareq.co/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: facebook-account-kit
Domain Path: /languages
*/

/**
 * Copyright (c) 2018 Tareq Hasan (email: tareq@wedevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * FB_Account_Kit class
 *
 * @class FB_Account_Kit The class that holds the entire FB_Account_Kit plugin
 */
class FB_Account_Kit {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0';

    /**
     * Constructor for the FB_Account_Kit class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {

        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'FB_ACCOUNT_KIT_VERSION', $this->version );
        define( 'FB_ACCOUNT_KIT_FILE', __FILE__ );
        define( 'FB_ACCOUNT_KIT_PATH', dirname( FB_ACCOUNT_KIT_FILE ) );
        define( 'FB_ACCOUNT_KIT_INCLUDES', FB_ACCOUNT_KIT_PATH . '/includes' );
        define( 'FB_ACCOUNT_KIT_URL', plugins_url( '', FB_ACCOUNT_KIT_FILE ) );
        define( 'FB_ACCOUNT_KIT_ASSETS', FB_ACCOUNT_KIT_URL . '/assets' );
    }

    /**
     * Initializes the FB_Account_Kit() class
     *
     * Checks for an existing FB_Account_Kit() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FB_Account_Kit();
        }

        return $instance;
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

        $installed = get_option( 'fb_account_kit_installed' );

        if ( ! $installed ) {
            update_option( 'fb_account_kit_installed', time() );
        }

        update_option( 'fb_account_kit_version', FB_ACCOUNT_KIT_VERSION );
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {
        require_once FB_ACCOUNT_KIT_INCLUDES . '/class-ajax.php';
        require_once FB_ACCOUNT_KIT_INCLUDES . '/class-widget.php';

        if ( ! is_admin() ) {
            require_once FB_ACCOUNT_KIT_INCLUDES . '/class-woocommerce.php';
            require_once FB_ACCOUNT_KIT_INCLUDES . '/class-wp-login.php';
        }

        if ( is_admin() ) {
            require_once FB_ACCOUNT_KIT_INCLUDES . '/lib/class-settings-api.php';
            require_once FB_ACCOUNT_KIT_INCLUDES . '/class-admin.php';
        }

        require_once FB_ACCOUNT_KIT_INCLUDES . '/functions.php';
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'plugins_loaded', array( $this, 'init_classes' ) );

        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );

        add_action( 'widgets_init', array( $this, 'init_widgets' ) );

        // Loads frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ) );
        add_action( 'login_enqueue_scripts', array( $this, 'register_script' ) );
    }

    /**
     * Init the classes
     *
     * @return void
     */
    public function init_classes() {
        new \WeDevs\FBAccountKit\Ajax();

        if ( ! is_admin() ) {
            new \WeDevs\FBAccountKit\WooCommerce();
            new \WeDevs\FBAccountKit\WP_Login();
        }

        if ( is_admin() ) {
            new \WeDevs\FBAccountKit\Admin();
        }
    }

    /**
     * Register widgets
     *
     * @return void
     */
    public function init_widgets() {
        register_widget( '\WeDevs\FBAccountKit\Widget' );
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'facebook-account-kit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Enqueue admin scripts
     *
     * Allows plugin assets to be loaded.
     */
    public function register_script() {

        wp_register_style( 'fb-account-kit', FB_ACCOUNT_KIT_ASSETS . '/css/style.css', false, FB_ACCOUNT_KIT_VERSION );

        wp_register_script( 'fb-account-kit', 'https://sdk.accountkit.com/en_US/sdk.js', false, FB_ACCOUNT_KIT_VERSION, true );
        wp_register_script( 'fb-account-kit-script', FB_ACCOUNT_KIT_ASSETS . '/js/account-kit.js', array( 'fb-account-kit' ), FB_ACCOUNT_KIT_VERSION, true );
        wp_localize_script( 'fb-account-kit', 'FBAccountKit', array(
            'ajaxurl'  => admin_url( 'admin-ajax.php' ),
            'app_id'   => fbak_get_fb_app_id(),
            'version'  => fbak_get_fb_app_version(),
            'nonce'    => wp_create_nonce( 'fb_account_kit' ),
            'redirect' => home_url( '/?fb_account_kit_redir=true'),
        ) );
    }

} // FB_Account_Kit

FB_Account_Kit::init();
