<?php


$context = Timber::context();

$context['is_front_page'] = false;

// Assuming you're within the loop or have access to the current post ID
$current_post_id = get_the_ID();

// Get all tags for the current post
$current_post_tags = wp_get_post_tags($current_post_id);

if (!empty($current_post_tags)) {
    // Extract the tag IDs into an array
    $tag_ids = array_map(function($tag) {
        return $tag->term_id;
    }, $current_post_tags);

    foreach ($related_posts as $post) {
        $post->price = get_post_meta($post->ID, 'wp_review_product_price', true);
    }
    
    // Query posts with matching tags
    $context['related_posts'] = Timber::get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'paged'          => $paged,
        'tag__in'        => $tag_ids,   // Query by the tag IDs
        'post__not_in'   => array($current_post_id),  // Exclude current post
    ));
}

Timber::render('single-brand.twig', $context);
