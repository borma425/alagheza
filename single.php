<?php
use Timber\Timber;


function convertArabicDateToEnglish($arabicDate) {
    // Create a DateTime object from the Arabic date string
    $date = DateTime::createFromFormat('!d F Y', $arabicDate);
    if ($date) {
        // Return the date in a standard format (e.g., ISO 8601)
        return $date->format('Y-m-d\TH:i:sP');
    }
    return null; // Return null if conversion fails
}



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




$context['product_name'] = isset($meta_value['Product']['name']) ? $meta_value['Product']['name'] : '';

if (is_array($meta_value) && isset($meta_value['Product']['image'])) {
    $image_data = $meta_value['Product']['image'];
    $image_id = isset($image_data['id']) ? $image_data['id'] : '';
    $image_url = isset($image_data['url']) ? $image_data['url'] : '';

    // Check if image URL exists and output it
    if (!empty($image_url)) {
          $reviewed_item_image = $image_url; // Get existing image URL

    } else {
    $reviewed_item_image = "No_imagerM"; // Get existing image URL
    $reviewed_item_image = get_post_meta($post_id, 'wp_review_hello_bar_bg_image_url', true);

    }
} else {
    echo 'Product image data is not set correctly.';
    $reviewed_item_image = "No_imagerTT"; // Get existing image URL
}



$context['product_image'] = $reviewed_item_image;
$context['product_desc'] =  isset($meta_value['Product']['description']) ? $meta_value['Product']['description'] : '';
$context['product_brand'] =  isset($meta_value['Product']['brand']) ? $meta_value['Product']['brand'] : '';
$context['product_sku'] =   isset($meta_value['Product']['sku']) ? $meta_value['Product']['sku'] : '';
$context['product_price'] =   isset($meta_value['Product']['price']) ? $meta_value['Product']['price'] : '';
$context['product_total_rating'] =   get_post_meta( $post->ID, 'wp_review_total', true );
$context['product_reviewBody'] =    get_post_meta($post->ID, 'wp_review_desc', true);

// Optionally enable caching
$cache_key = $post->ID;
$context['post'] = Timber::get_post($cache_key);



Timber::render('content/single.twig', $context);