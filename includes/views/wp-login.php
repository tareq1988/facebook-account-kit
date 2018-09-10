<div class="fb-ackit-wrap">
    <div class="fb-ackit-login-forms">

        <p class="fb-ackit-desc">
            <?php echo fbak_description(); ?>
        </p>

        <?php if ( fbak_phone_displayed() ) : ?>
            <a href="#" onclick="smsLogin(); return false;" class="button button-primary"><span class="dashicons dashicons-testimonial"></span> <?php echo fbak_phone_label(); ?></a>
        <?php endif; ?>

        <?php if ( fbak_email_displayed() ) : ?>
            <a href="#" onclick="emailLogin(); return false;" class="button"><span class="dashicons dashicons-email"></span> <?php echo fbak_email_label(); ?></a>
        <?php endif; ?>
    </div>

    <div class="fb-ackit-or">
        <span><?php _e( 'Or', 'fb-account-kit' ); ?></span>
    </div>

    <div class="fb-ackit-toggle">
        <a href="#" class="default"><?php _e( 'Login with Username and Password', 'fb-account-kit' ); ?></a>
        <a href="#" class="ackit"><?php _e( 'Login with SMS or Email', 'fb-account-kit' ); ?></a>
    </div>
</div>
