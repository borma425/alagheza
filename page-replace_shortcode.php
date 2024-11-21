<?php
// Query all posts to search for the shortcode
$posts = new WP_Query(array(
    'post_type' => 'post', // Change if you're working with custom post types
    'posts_per_page' => -1, // Get all posts
    'fields' => 'ids' // Only fetch post IDs for performance
));

if ($posts->have_posts()) {
    foreach ($posts->posts as $post_id) {
        // Get the post content
        $content = get_post_field('post_content', $post_id);

        // Use regex to find and replace the [wp-review id="..."] shortcode
        $updated_content = preg_replace(
            '/\[wp-review id="[^"]+"\]/', // Match [wp-review id="..."]
            '[review_section]', // Replace with [review_section]
            $content
        );

        // Only update if there are changes
        if ($content !== $updated_content) {
            // Update the post content
            wp_update_post(array(
                'ID' => $post_id,
                'post_content' => $updated_content,
            ));

            // Output the update info (optional)
            echo 'Updated post ID ' . $post_id . '<br>';
        }
    }
} else {
    echo 'No posts found to update.';
}

// Reset post data to avoid conflicts
wp_reset_postdata();
?>
