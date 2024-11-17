
<?php

// Define the meta keys to be updated
$pros_meta_key = 'single_pros';
$cons_meta_key = 'single_cons';

// Query all posts with either 'single_pros_editor' or 'single_cons_editor' meta keys
$posts = new WP_Query(array(
    'post_type' => 'post', // Change this if your post type is different
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => $pros_meta_key,
            'value' => '',
            'compare' => '!=', // Non-empty pros content
        ),
        array(
            'key' => $cons_meta_key,
            'value' => '',
            'compare' => '!=', // Non-empty cons content
        ),
    ),
    'posts_per_page' => -1, // Get all matching posts
    'fields' => 'ids', // Only get post IDs to optimize performance
));

if ($posts->have_posts()) {
    foreach ($posts->posts as $post_id) {
        // Handle pros content
        $pros_content = get_post_meta($post_id, $pros_meta_key, true);

        if ($pros_content) {
            // Add 'feature-list pros' to <ul> and 'feature-item' to <li>
            $pros_content = preg_replace_callback(
                '/<ul([^>]*?)>/',
                function ($matches) {
                    if (strpos($matches[1], 'feature-list pros') === false) {
                        return '<ul class="feature-list pros" ' . $matches[1] . '>';
                    }
                    return '<ul ' . $matches[1] . '>';
                },
                $pros_content
            );
            $pros_content = preg_replace('/<li([^>]*?)>/', '<li class="feature-item" $1>', $pros_content);

            // Update the meta field with the updated content
            update_post_meta($post_id, $pros_meta_key, $pros_content);

            // Optional: Output original and updated values for reference
            echo 'Updated pros content for post ID ' . $post_id . '<br>';
        }

        // Handle cons content
        $cons_content = get_post_meta($post_id, $cons_meta_key, true);

        if ($cons_content) {
            // Add 'feature-list cons' to <ul> and 'feature-item' to <li>
            $cons_content = preg_replace_callback(
                '/<ul([^>]*?)>/',
                function ($matches) {
                    if (strpos($matches[1], 'feature-list cons') === false) {
                        return '<ul class="feature-list cons" ' . $matches[1] . '>';
                    }
                    return '<ul ' . $matches[1] . '>';
                },
                $cons_content
            );
            $cons_content = preg_replace('/<li([^>]*?)>/', '<li class="feature-item" $1>', $cons_content);

            // Update the meta field with the updated content
            update_post_meta($post_id, $cons_meta_key, $cons_content);

            // Optional: Output original and updated values for reference
            echo 'Updated cons content for post ID ' . $post_id . '<br>';
        }
    }
} else {
    echo 'No posts found with the specified meta keys.';
}

// Reset post data to avoid conflicts with other queries
wp_reset_postdata();
