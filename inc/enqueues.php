<?php
function enqueues() {

$ver = DEV_MODE ? time() : false;


if( !is_admin() ) {

    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', [], $ver, 'all' );
    wp_enqueue_script("parent-js", asset_url('main.js','/js/'), [], $ver, true );

if( !is_singular("post") ) {
    wp_enqueue_script("home-js", asset_url('home.js','/js/'), [], $ver, true );
    wp_enqueue_style( 'home-style', asset_url('home.css','/css/'), [], $ver, 'all' );
}

if (is_singular("post") || is_page() ) {
    wp_enqueue_style('content-css', asset_url('content.css','/css/') ,[],$ver );

}




    }

}


add_action('wp_enqueue_scripts', 'enqueues', 999);








