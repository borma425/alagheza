<?php
function get_c_meta($meta_key) {
    global $post; // Declare global $post here
    return $post ? get_post_meta($post->ID, $meta_key, true) : '';
}


add_shortcode('specifications_section', 'display_device_specifications');
add_shortcode('qa_section', 'display_qa_section');
add_shortcode('pros_section', 'display_pros_section');
add_shortcode('cons_section', 'display_cons_section');

add_shortcode('more_products_prices_section', 'display_more_products_prices_section');
add_shortcode('review_section', 'display_review_section');


function display_device_specifications() {

    $html_stored = '';

        $device_specs = get_c_meta('device_specs');
        $device_specifications_main_title = get_c_meta('device_specifications_main_title') ;
        if (!empty($device_specs)) {
            foreach ($device_specs as $key => $spec) {
                $html_stored .=  $device_specifications_main_title . ' ';
                $html_stored .= esc_attr($spec['name']) . ' ';
                $html_stored .= esc_attr($spec['icon']) . ' ';
                $html_stored .= esc_attr($spec['description']) . ' ';

            }
        }


    return $html_stored;
}




function display_qa_section() {

}



function display_pros_section() {

}

function display_cons_section() {

}


function display_more_products_prices_section() {

}

function review_section() {

}