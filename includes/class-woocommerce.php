<?php
namespace WeDevs\FBAccountKit;

/**
 * WooComemrce Class
 */
class WooCommerce {

    function __construct() {
        add_action( 'woocommerce_login_form_end', [ $this, 'login_form' ] );
    }

    public function login_form() {
        wp_enqueue_style( 'dashicons' );
        wp_enqueue_style( 'fb-account-kit' );

        wp_enqueue_script( 'fb-account-kit' );
        wp_enqueue_script( 'fb-account-kit-script' );
        ?>
        <div class="fb-ackit-wrap">

            <div class="fb-ackit-or">
                <span><?php _e( 'Or', 'fb-account-kit' ); ?></span>
            </div>

            <div class="fb-ackit-buttons">
                <?php if ( fbak_phone_displayed() ) : ?>
                    <button href="#" onclick="smsLogin(); return false;" class="button"><span class="dashicons dashicons-testimonial"></span> <?php echo fbak_phone_label(); ?></button>
                <?php endif; ?>

                <?php if ( fbak_email_displayed() ) : ?>
                    <button href="#" onclick="emailLogin(); return false;" class="button"><span class="dashicons dashicons-email"></span> <?php echo fbak_email_label(); ?></button>
                <?php endif; ?>
            </div>

        </div>
        <?php
    }
}
