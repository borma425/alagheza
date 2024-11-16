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
        'posts_per_page' => 40,
        'paged'          => $paged,
        'tag__in'        => $tag_ids,
        'post__not_in'   => array($current_post_id),
    ));

    // Initialize an array to hold related posts with prices
    $related_posts = [];
    
    foreach ($related_posts_data as $post) {


        $post_categories = get_the_category($post->ID);
        $first_category = !empty($post_categories) ? $post_categories[0]->name : ''; // Get the first category name

        // Create a new array to hold post details
        $post_details = [
            'title' => $post->title,
            'link'  => $post->link,
            'id'    => $post->ID,
            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'full'), // Adjust size as needed
            'description' => get_the_excerpt($post->ID), // or get_post_field('post_content', $post->ID) for full content
            'first_category' => $first_category, // Store the first category name
     
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
        $post_details['total_rating'] = get_post_meta($post->ID, "wp_review_total", true);

        // Add the post details to the related posts array
        $related_posts[] = $post_details;
    }

    // Store related posts in the context
    $context['posts'] = $related_posts;
}

Timber::render('single-brand.twig', $context);
