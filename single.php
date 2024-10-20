<?php
use Timber\Timber;




$context = Timber::context();


$post_id    = get_the_ID();
  // Retrieve schema options
  $meta_key   = 'wp_review_schema_options';
  $meta_value = get_post_meta($post_id, $meta_key, true);
  
  // Ensure $meta_value is an array
  if (!is_array($meta_value)) {
    // Attempt to decode if it's JSON
    $decoded = json_decode($meta_value, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $meta_value = $decoded;
    } else {
        // Handle the error appropriately
        $meta_value = [];
        error_log("Meta key '{$meta_key}' for post ID {$post_id} is not an array or valid JSON.");
    }
}


$product_name            = isset($meta_value['Product']['name']) ? $meta_value['Product']['name'] : '';

$context['product_name'] = $product_name ;




// Optionally enable caching
$cache_key = $post->ID;
$context['post'] = Timber::get_post($cache_key);



Timber::render('content/single.twig', $context);