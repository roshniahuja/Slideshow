<?php
// Enqueue styles and scripts for the frontend.
function simple_slideshow_register_styles() {

    wp_enqueue_style( 'sclick_slideshow_style', plugin_dir_url( __DIR__ ) . 'js/slick/slick.css' );

    wp_enqueue_style( 'sclick_slideshow_theme_style', plugin_dir_url( __DIR__ ) . 'js/slick/slick-theme.css' );

    wp_enqueue_style( 'simple_slideshow_style', plugin_dir_url( __DIR__ ) . 'css/simple-slideshow.css' );

    wp_enqueue_script( 'sclick_slideshow_script', plugin_dir_url( __DIR__ ) . 'js/slick/slick.min.js', array('jquery'), true );

    wp_enqueue_script( 'resize_sensor_script', plugin_dir_url( __DIR__ ) . 'js/resize-sensor.js', array('jquery'), true );

    wp_enqueue_script( 'simple_slideshow_script', plugin_dir_url( __DIR__ ) . 'js/simple-slideshow.js', array('jquery'), true );

}

add_action( 'wp_enqueue_scripts', 'simple_slideshow_register_styles' );

// Enqueue styles and scripts for the admin area.
function simple_slideshow_admin_scripts() {

    wp_enqueue_style( 'simple_slideshow_admin_style', plugin_dir_url( __DIR__ ) . 'css/simple-slideshow-admin.css' );
 
    wp_enqueue_script( 'simple_slideshow_admin_script', plugin_dir_url( __DIR__ ) . 'js/simple-slideshow-admin.js', array('jquery'), true );
     
}
add_action( 'admin_enqueue_scripts','simple_slideshow_admin_scripts' );