<?php

function remove_section_tags_from_all_posts() {
    // Query for all posts
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,  // Fetch all posts
        'post_status' => 'publish',  // Only get published posts
    );

    $query = new WP_Query($args);

    // Loop through each post and remove <section> tags
    while ($query->have_posts()) {
        $query->the_post();

        // Get current post content
        $content = get_the_content();

        // Remove <section> tags
        $updated_content = preg_replace('/<section[^>]*>/', '', $content);
        $updated_content = preg_replace('/<\/section>/', '', $updated_content);

        // Update post content if there is a change
        if ($content !== $updated_content) {
            wp_update_post(array(
                'ID' => get_the_ID(),
                'post_content' => $updated_content
            ));
        }
    }

    wp_reset_postdata();  // Reset post data after the query
}

// Uncomment the line below to run the function when needed
remove_section_tags_from_all_posts();
