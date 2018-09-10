<?php
namespace WeDevs\FBAccountKit;

/**
 * Profile Class
 *
 * @since 1.1.0
 */
class Profile {

    /**
     * Init hooks
     */
    public function __construct() {
        add_action( 'personal_options', [ $this, 'show_connect_button' ], 5 );

        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Enqueue script for profile edit page
     *
     * @param  string $hook
     *
     * @return void
     */
    public function enqueue_scripts( $hook ) {
        if ( 'profile.php' !== $hook ) {
            return;
        }

        wp_enqueue_script( 'fb-account-kit' );
        wp_enqueue_script( 'fb-account-kit-admin', FB_ACCOUNT_KIT_ASSETS . '/js/admin-account-kit.js', array( 'jquery', 'fb-account-kit' ), FB_ACCOUNT_KIT_VERSION, true );
    }

    /**
     * Is the account is connected
     *
     * @param  int  $user_id
     *
     * @return boolean
     */
    private function is_connected( $user_id ) {
        $account_id = get_user_meta( $user_id, '_fb_accountkit_id', true );

        return $account_id != '';
    }

    /**
     * Show the connect/disconnect button
     *
     * @param  \WP_User $user
     *
     * @return void
     */
    public function show_connect_button( $user ) {
        $connected = $this->is_connected( $user->ID );
            ?>

            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php _e( 'Facebook Account Kit', 'fb-account-kit' ); ?></th>
                        <td>
                            <?php if ( ! $connected ) { ?>

                                <button class="button button-primary" onclick="smsLogin(); return false;"><span class="dashicons dashicons-testimonial" style="margin-top:3px;"></span> <?php _e( 'Associate Phone Number', 'fb-account-kit' ); ?></button>

                            <?php } else { ?>

                                <button class="button" disabled><span class="dashicons dashicons-testimonial" style="margin-top:3px;"></span> <?php _e( 'Connected', 'fb-account-kit' ); ?></button>
                                <button class="button button-danger" onclick="fbAcDisconnect(); return false;"><span class="dashicons dashicons-trash" style="margin-top:3px;"></span> <?php _e( 'Disconnect', 'fb-account-kit' ); ?></button>

                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php

    }

}
