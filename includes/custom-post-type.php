<?php
/**
 * Custom Post Type Registration.
 */
function simple_slideshow_setup_post_type() {
    add_action( 'admin_enqueue_scripts', 'simple_slideshow_add_media_script' );

    function simple_slideshow_add_media_script( $hook_suffix ) {
        wp_enqueue_media();
    }

    register_post_type( 'simple_slideshow',
        array(
            'labels'      => array(
                'name'          => __( 'Simple Slideshow', 'simple-slideshow' ),
                'singular_name' => __( 'Simple Slideshow', 'simple-slideshow' ),
            ),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array( 'title' ),
            'rewrite'     => array( 'slug' => 'slideshow' ),
            'menu_icon'   => 'dashicons-format-gallery',
        )
    );

    add_action( 'add_meta_boxes', 'simple_slideshow_meta_box_add' );

    function simple_slideshow_meta_box_add() {
        add_meta_box(
            'simple_slideshow_feat_img_slideshow', // meta box ID
            __( 'Featured Image Gallery', 'simple-slideshow' ), // meta box title
            'simple_slideshow_print_box', // callback function that prints the meta box HTML
            'simple_slideshow', // post type where to add it
            'normal', // priority
            'default' // position
        );
        add_meta_box(
            'simple_slideshow_shortcode', // meta box ID
            __( 'Shortcode', 'simple-slideshow' ), // meta box title
            'simple_slideshow_shorcode_box', // callback function that prints the meta box HTML
            'simple_slideshow', // post type where to add it
            'side', // priority
            'default' // position
        );
    }

    function simple_slideshow_image_uploader_field( $name, $value = '' ) {
        global $post;

        $image       = 'Upload Image';
        $button      = 'button';
        $image_size  = 'full'; // it would be better to use thumbnail size here (150x150 or so)
        $display     = 'none'; // display state of the "Remove image" button
        $height      = get_post_meta( $post->ID, 'slideshow_height', true );
        $heightunits = get_post_meta( $post->ID, 'height_units', true );
        $speed       = get_post_meta( $post->ID, 'slideshow_speed', true );
        $arrow       = get_post_meta( $post->ID, 'slideshow_arrow', true );
        $bullet      = get_post_meta( $post->ID, 'slideshow_bullet', true );
        ?>
        <p>
            <label for="slideshow-height"><?php _e( 'Slideshow Height', 'simple-slideshow' ); ?></label>
            <input name="slideshow_height" type="number" id="slideshow-height"
                value="<?php echo esc_attr( ( $height > 0 ) ? $height : '250' ); ?>">
            <select name="height_units" id="height-units">
                <option value="px" <?php selected( $heightunits, 'px' ); ?>><?php _e( 'px', 'simple-slideshow' ); ?></option>
                <option value="%" <?php selected( $heightunits, '%' ); ?>><?php _e( '%', 'simple-slideshow' ); ?></option>
            </select>
        </p>
        <p>
            <label for="slideshow-speed"><?php _e( 'Slideshow Speed', 'simple-slideshow' ); ?></label>
            <input name="slideshow_speed" type="number" id="slideshow-speed"
                value="<?php echo esc_attr( ( $speed > 0 ) ? $speed : '4000' ); ?>">
        </p>
        <p>
            <label for="slideshow-arrow"><?php _e( 'Arrows', 'simple-slideshow' ); ?></label>
            <select name="slideshow_arrow" id="slideshow-arrow">
                <option value="1" <?php selected( $arrow, 1 ); ?>><?php _e( 'Yes', 'simple-slideshow' ); ?></option>
                <option value="2" <?php selected( $arrow, 2 ); ?>><?php _e( 'No', 'simple-slideshow' ); ?></option>
            </select>
        </p>
        <p>
            <label for="slideshow-bullet"><?php _e( 'Bullets', 'simple-slideshow' ); ?></label>
            <select name="slideshow_bullet" id="slideshow-bullet">
                <option value="1" <?php selected( $bullet, 1 ); ?>><?php _e( 'Yes', 'simple-slideshow' ); ?></option>
                <option value="2" <?php selected( $bullet, 2 ); ?>><?php _e( 'No', 'simple-slideshow' ); ?></option>
            </select>
        </p>
        <p><?php _e( '<i>Set Images for Featured Image Gallery</i>', 'simple-slideshow' ); ?></p>

        <label>
            <div class="gallery-screenshot clearfix">
                <?php
                $ids = explode( ',', $value );
                foreach ( $ids as $attachment_id ) {
                    $img = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
                    echo '<div class="screen-thumb"><img src="' . esc_url( $img[0] ) . '" /></div>';
                }
                ?>
            </div>

            <input id="edit-gallery" class="button upload_gallery_button" type="button"
                value="<?php esc_attr_e( 'Add/Edit Gallery', 'simple-slideshow' ) ?>" />
            <input id="clear-gallery" class="button upload_gallery_button" type="button"
                value="<?php esc_attr_e( 'Clear', 'simple-slideshow' ) ?>" />
            <input type="hidden" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>"
                class="gallery_values" value="<?php echo esc_attr( $value ); ?>">
        </label>
        <?php
    }

    /*
     * Meta Box HTML
     */
    function simple_slideshow_print_box( $post ) {
        wp_nonce_field( 'save_feat_gallery', 'simple_slideshow_feat_gallery_nonce' );

        $meta_key = 'simple_slideshow_img';
        echo simple_slideshow_image_uploader_field( $meta_key, get_post_meta( $post->ID, $meta_key, true ) ); //phpcs:ignore
    }

    function simple_slideshow_shorcode_box( $post ) {
        $shortcode = '[simple_slideshow id="' . $post->ID . '"]';
        ?>
        <div id="slideshow-shortcode">
            <?php echo esc_attr( $shortcode ); ?>
        </div>
        <?php
    }

    /*
     * Save Meta Box data
     */
    add_action( 'save_post', 'simple_slideshow_img_gallery_save' );

    function simple_slideshow_img_gallery_save( $post_id ) {
        // Check if the current user is authorized to edit this post.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        // Verify the nonce for security.
        $nonce_name = 'simple_slideshow_feat_gallery_nonce';
        $nonce_value = filter_input( INPUT_POST, $nonce_name, FILTER_SANITIZE_STRING );
        $nonce_value = isset( $_POST[ $nonce_name ] ) ? $nonce_value : '';

        // Sanitize the height_units value using filter_input.
        $height_units = filter_input( INPUT_POST, 'height_units', FILTER_SANITIZE_STRING );

        if ( ! wp_verify_nonce( $nonce_value, 'save_feat_gallery' ) ) {
            return $post_id;
        }

        if ( isset( $_POST["slideshow_height"] ) ) {
            
            $height = intval( $height_units );
            if ( ! $height ) {
                $height = 150;
            }
            update_post_meta( $post_id, 'slideshow_height', $height );
        }
        if ( isset( $_POST["height_units"] ) ) {
            $heightunits = esc_attr( $height_units );
            update_post_meta( $post_id, 'height_units', $heightunits );
        }

        if ( isset( $_POST["slideshow_speed"] ) ) {
            $speed = intval( $_POST["slideshow_speed"] );
            if ( ! $speed ) {
                $speed = 4000;
            }
            update_post_meta( $post_id, 'slideshow_speed', $speed );
        }

        if ( isset( $_POST["slideshow_arrow"] ) ) {
            $arrow = intval( $_POST["slideshow_arrow"] );
            if ( ! $arrow ) {
                $arrow = 1;
            }

            if ( $arrow < 1 || $arrow > 2 ) {
                $arrow = 1;
            }

            update_post_meta( $post_id, 'slideshow_arrow', $arrow );
        }

        if ( isset( $_POST["slideshow_bullet"] ) ) {
            $bullet = intval( $_POST["slideshow_bullet"] );
            if ( ! $bullet ) {
                $bullet = 1;
            }

            if ( $bullet < 1 || $bullet > 2 ) {
                $bullet = 1;
            }
            update_post_meta( $post_id, 'slideshow_bullet', $bullet );
        }

        if ( ! isset( $_POST['simple_slideshow_feat_gallery_nonce'] ) ) {
            return $post_id;
        }

        if ( ! wp_verify_nonce( $nonce_value, 'save_feat_gallery' ) ) {
            return $post_id;
        }

        if ( isset( $_POST[ 'simple_slideshow_img' ] ) ) {

            // Sanitize the simple_slideshow_img value using filter_input.
            $gallery = filter_input( INPUT_POST, 'simple_slideshow_img', FILTER_SANITIZE_STRING );
            if ( preg_match( "/^(?:\d\,?)+\d$/", $gallery ) ) {
                update_post_meta( $post_id, 'simple_slideshow_img', $gallery );
            } else {
                update_post_meta( $post_id, 'simple_slideshow_img', '' );
            }

        } else {
            update_post_meta( $post_id, 'simple_slideshow_img', '' );
        }
    }
}

add_action( 'init', 'simple_slideshow_setup_post_type' );