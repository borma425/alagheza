<?php

$context = Timber::context();
$context['is_front_page'] = false;

$current_post_id = get_the_ID();
$current_post_tags = wp_get_post_tags($current_post_id);

if (!empty($current_post_tags)) {
    $tag_ids = array_map(function($tag) {
        return $tag->term_id;
    }, $current_post_tags);

    // Query posts with matching tags
    $related_posts_data = Timber::get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => 8,
        'paged'          => $paged,
        'tag__in'        => $tag_ids,
        'post__not_in'   => array($current_post_id),
    ));

    // Initialize an array to hold related posts with prices
    $related_posts = [];
    
    foreach ($related_posts_data as $post) {
        // Create a new array to hold post details
        $post_details = [
            'title' => $post->title,
            'link'  => $post->link,
            'id'    => $post->ID,
        ];

        // Retrieve schema options for the current related post
        $meta_key = 'wp_review_schema_options';
        $meta_value = get_post_meta($post->ID, $meta_key, true);

        // Ensure $meta_value is an array
        if (!is_array($meta_value)) {
            // Attempt to decode if it's JSON
            $decoded = json_decode($meta_value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $meta_value = $decoded;
            } else {
                // Handle the error appropriately
                $meta_value = [];
                error_log("Meta key '{$meta_key}' for post ID {$post->ID} is not an array or valid JSON.");
            }
        }

        // Get the product price from the schema options
        $product_price = isset($meta_value['Product']['price']) ? $meta_value['Product']['price'] : '';

        // Add the price to the post details
        $post_details['price'] = $product_price;

        // Add the post details to the related posts array
        $related_posts[] = $post_details;
    }

    // Store related posts in the context
    $context['related_posts'] = $related_posts;
}

Timber::render('single-brand.twig', $context);
