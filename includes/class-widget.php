<?php
namespace WeDevs\FBAccountKit;

class Widget extends \WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        parent::__construct(
            'fb-account-kit', // Base ID
            esc_html__( 'Facebook Account Kit', 'facebook-account-kit' ), // Name
            array( 'description' => esc_html__( 'Facebook Account Kit login widget.', 'facebook-account-kit' ), ) // Args
        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {

        // don't display if already logged in
        if ( is_user_logged_in() ) {
            return;
        }

        wp_enqueue_style( 'dashicons' );
        wp_enqueue_style( 'fb-account-kit' );

        wp_enqueue_script( 'fb-account-kit' );
        wp_enqueue_script( 'fb-account-kit-script' );

        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        ?>
        <div class="fb-ackit-wrap">

            <div class="fb-ackit-desc"><?php echo fbak_description(); ?></div>

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
        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => __( 'Account Login', 'facebook-account-kit' ),
        ) );

        $title = sanitize_text_field( $instance['title'] );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'facebook-account-kit' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance     = $old_instance;
        $new_instance = wp_parse_args( (array) $new_instance, array(
            'title' => __( 'Account Login', 'facebook-account-kit' ),
        ) );

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }
}
