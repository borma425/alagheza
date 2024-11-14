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



#Timber::render('content/single.twig', $context);


function get_comparable_products($current_product_id) {
    $tags = wp_get_post_terms($current_product_id, 'post_tag', ['fields' => 'ids']);
    $first_tag = !empty($tags) ? $tags[0] : null;

    $current_price = floatval(get_post_meta($current_product_id, 'wp_review_product_price', true));
    $current_rating = floatval(get_post_meta($current_product_id, 'wp_review_total', true));
    // Check if the values are being retrieved correctly
    if (!$current_price || !$current_rating) {
        echo 'Price or rating is missing for the current product.';
        return new WP_Query();
    }

    $args = [
        'post_type' => 'post',
        'post__not_in' => [$current_product_id],
        'posts_per_page' => 2,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'wp_review_product_price',
                'value' => [$current_price - 10000, $current_price + 10000],
                'type' => 'NUMERIC',
                'compare' => 'BETWEEN',
            ],
            [
                'key' => 'wp_review_total',
                'value' => [$current_rating - 2, $current_rating + 2],
                'type' => 'DECIMAL',
                'compare' => 'BETWEEN',
            ],
        ],
        'tax_query' => [
            [
                'taxonomy' => 'post_tag',
                'field' => 'term_id',
                'terms' => $first_tag,
            ],
        ],
    ];

    return new WP_Query($args);
}

// Display as table
$current_product_id = get_the_ID();
$comparable_products = get_comparable_products($current_product_id);

if ($comparable_products->have_posts()) {
    echo '<table border="1" cellpadding="10" cellspacing="0">';
    echo '<tr>';
    echo '<th>Product Name</th>';
    echo '<th>Price (in pounds)</th>';
    echo '<th>Rating</th>';
    echo '</tr>';
    
    // Loop through the comparable products
    while ($comparable_products->have_posts()) : $comparable_products->the_post();
        $product_price = get_post_meta(get_the_ID(), 'wp_review_product_price', true);
        $product_rating = get_post_meta(get_the_ID(), 'wp_review_total', true);

        echo '<tr>';
        echo '<td>' . get_the_title() . '</td>';
        echo '<td>' . esc_html($product_price) . '</td>';
        echo '<td>' . esc_html($product_rating) . '</td>';
        echo '</tr>';
    endwhile;
    
    echo '</table>';
    wp_reset_postdata();
} else {
    echo '<p>No comparable products found.</p>';
}
