<?php
function remove_tags_meta_box_banner() {

/*  remove_post_type_support("post", 'editor'); */
 remove_meta_box('revisionsdiv', 'post', 'normal');
 remove_meta_box('commentsdiv', 'post', 'normal');

}

    add_action('admin_menu', 'remove_tags_meta_box_banner');


function remove_tags_meta_boxbrands() {
    $cpt_id = ['brands', 'sections'];

    foreach ($cpt_id as $post_type) {
        remove_meta_box('postcustom', $post_type, 'normal');
        remove_meta_box('authordiv', $post_type, 'normal');
        remove_meta_box('commentsdiv', $post_type, 'normal');
        remove_meta_box('commentstatusdiv', $post_type, 'normal');
        remove_meta_box('pageparentdiv', $post_type, 'normal');
        remove_meta_box('formatdiv', $post_type, 'normal');
        // Remove editor support for the post types
        remove_post_type_support($post_type, 'editor');
    }
}

add_action('admin_menu', 'remove_tags_meta_boxbrands');







    $currentFile = basename(__FILE__);
    // Path to the directory containing PHP files
    $directory = get_theme_file_path('inc/acf/') . '*.php';
    // Use glob to get all PHP files in the directory
    $phpFiles = glob($directory);
    // Loop through each file
    foreach ($phpFiles as $file) {
        // Skip the current file
        if (basename($file) !== $currentFile) {
            include_once $file;
        }
    }