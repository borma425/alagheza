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












    function add_inline_editor_script() {
        // Check if we are on the post editor page
        if (get_current_screen()->base === 'post' || get_current_screen()->base === 'post_page') {
            ?>
            <script type="text/javascript">
jQuery(document).ready(function($) {
    // Create a new div with a border style

    const ratings_meta_box = document.getElementById('ratings_meta_box');
    const custom_meta_box = document.getElementById('custom_meta_box');

    // Create a new div with the specified ID
    const newDiv = document.createElement('div');
    newDiv.id = 'our_meta_border1';
    newDiv.style.border = '2px solid #D9E1D9';
    newDiv.style.padding = '20px';
    newDiv.style.borderRadius = '10px';
    newDiv.style.backgroundColor = '#f9f9f9';


    // Insert the new div before the custom meta box
    ratings_meta_box.parentNode.insertBefore(newDiv, ratings_meta_box);
    custom_meta_box.parentNode.insertBefore(newDiv, custom_meta_box);

    // Move the custom meta box inside the new div
    newDiv.appendChild(ratings_meta_box);
    newDiv.appendChild(custom_meta_box);


});


            </script>
            <?php
        }
    }
    add_action('admin_footer', 'add_inline_editor_script');