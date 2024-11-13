<?php

// Define the meta key to be updated
$meta_key = 'wp_review_product_price';

// Query all posts with the meta key 'wp_review_product_price'
$posts = new WP_Query(array(
    'post_type' => 'post', // Change this to your post type if it's not 'post'
    'meta_key' => $meta_key,
    'posts_per_page' => -1, // Get all posts with this meta key
    'fields' => 'ids' // Only get post IDs to optimize performance
));

if ($posts->have_posts()) {
    foreach ($posts->posts as $post_id) {
        // Get the current meta value
        $meta_value = get_post_meta($post_id, $meta_key, true);

        // Extract only the numbers from the meta value
        $numeric_value = preg_replace('/\D/', '', $meta_value); // Removes all non-numeric characters

        // Update the meta field with the numeric value
        update_post_meta($post_id, $meta_key, $numeric_value);

        // Output the original value for reference (optional)
        echo 'Original value for post ID ' . $post_id . ': ' . $meta_value . '<br>';
        echo 'Updated value for post ID ' . $post_id . ': ' . $numeric_value . '<br>';
    }
} else {
    echo 'No posts found with the meta key: ' . $meta_key;
}

// Reset post data to avoid conflicts with other queries
wp_reset_postdata();
