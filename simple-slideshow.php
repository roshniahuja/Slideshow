<?php
/**
 * Plugin Name: Simple Slideshow
 * Description: A simple plugin for creating image sliders.
 * Version: 1.0.0
 * Author: Roshni Ahuja
 * Author URI: https://about.me/roshniahuja
 * Text Domain: simple-slider
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include necessary files.
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';

// Activation hook.
register_activation_hook(__FILE__, 'simple_slideshow_activate');

function simple_slideshow_activate() {
    // Trigger our function that registers the custom post type plugin.
    simple_slideshow_setup_post_type();

    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules(); //phpcs:ignore
}