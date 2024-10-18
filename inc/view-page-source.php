<?php
/**
 * Plugin Name: Site Optimizations
 * Description: Performance optimizations to reduce SQL queries and enhance site speed.
 * Version: 1.0
 * Author: Your Name
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * REMOVE GUTENBERG BLOCK LIBRARY CSS FROM LOADING ON FRONTEND
 */
function so_remove_wp_block_library_css() {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-block-style' ); // REMOVE WOOCOMMERCE BLOCK CSS
    wp_dequeue_style( 'global-styles' ); // REMOVE THEME.JSON
    wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'so_remove_wp_block_library_css', 100 );

/**
 * Remove Global Styles and SVG Filters from WP
 */
function so_remove_global_styles_and_svg_filters() {
    remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
    remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
}
add_action( 'init', 'so_remove_global_styles_and_svg_filters' );

/**
 * Disable REST API Link Tags
 */
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );

/**
 * Disable oEmbed Discovery Links
 */
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'wp_custom_css_cb', 101 );


/**
 * Disable REST API Link in HTTP Headers
 */
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );

/**
 * REMOVE WP EMOJI
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_site_icon', 10 );

/**
 * Remove Shortlink From HTTP Header
 */
function so_remove_shortlink() {
    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
    remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
}
add_filter( 'after_setup_theme', 'so_remove_shortlink' );

/**
 * Remove Meta Tags that are Unneeded
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'wlwmanifest_link' );


/**
 * Remove Unused Dashboard Widgets (Optional)
 */
function so_remove_dashboard_widgets() {
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' ); // Activity
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' ); // Quick Draft
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' ); // WordPress News
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' ); // Other News
}
add_action( 'wp_dashboard_setup', 'so_remove_dashboard_widgets' );

/**
 * Disable Gutenberg Block Styles (Optional)
 */
function so_disable_gutenberg_block_styles() {
    remove_theme_support( 'wp-block-styles' );
}
add_action( 'after_setup_theme', 'so_disable_gutenberg_block_styles' );

/**
 * Use Transients API to Cache Expensive Queries
 */
function so_get_cached_custom_data() {
    $cache_key = 'so_custom_data';
    $cached_data = get_transient( $cache_key );

    if ( false === $cached_data ) {
        global $wpdb;
        $query = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_status = 'publish'";
        $cached_data = $wpdb->get_results( $query );

        // Cache for 12 hours
        set_transient( $cache_key, $cached_data, 12 * HOUR_IN_SECONDS );
    }

    return $cached_data;
}



/**
 * Disable XML-RPC (Optional)
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Disable Self-Pingbacks (Optional)
 */
function so_disable_self_pingbacks( &$links ) {
    foreach ( $links as $key => $link ) {
        if ( 0 === strpos( $link, get_option( 'home' ) ) ) {
            unset( $links[ $key ] );
        }
    }
}
add_action( 'pre_ping', 'so_disable_self_pingbacks' );

