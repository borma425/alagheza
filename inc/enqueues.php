<?php
function enqueues() {

$ver = DEV_MODE ? time() : false;


if( !is_admin() ) {
    // Parent style
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', [], $ver, 'all' );

    // Main JS script (deferred)
    wp_enqueue_script("parent-js", asset_url('main.js','/js/'), [], $ver, true );

    // Defer the main JS script
    add_filter('script_loader_tag', function($tag, $handle) {
        if ($handle === 'parent-js') {
            return str_replace('src', 'defer src', $tag);
        }
        return $tag;
    }, 10, 2);

    // Home-specific scripts and styles
    if( !is_singular("post") ) {
        wp_enqueue_script("home-js", asset_url('home.js','/js/'), [], $ver, true );
        wp_enqueue_style('home-style', asset_url('home.css','/css/'), [], $ver, 'all' );

        // Defer home JS script
        add_filter('script_loader_tag', function($tag, $handle) {
            if ($handle === 'home-js') {
                return str_replace('src', 'defer src', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    // Content-specific styles
    if (is_singular("post") || is_page()) {
        wp_enqueue_style('content-css', asset_url('content.css','/css/'), [], $ver, 'all');
    }
}
}

add_action('wp_enqueue_scripts', 'enqueues', 999);