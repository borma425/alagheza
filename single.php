<?php


$context = Timber::context();

$context['is_front_page'] = false;



$current_post_id = get_the_ID();
$current_post_categories = wp_get_post_categories($current_post_id);
global $paged;
if (!isset($paged) || !$paged){
    $paged = 1;
}

if (!empty($current_post_categories)) {
    // Query posts with matching categories
    $related_posts_data = Timber::get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => 4,
        'paged'          => $paged,
        'category__in'   => $current_post_categories, // Use 'category__in' for categories
        'post__not_in'   => array($current_post_id),
    ));

    // Initialize an array to hold related posts with additional data
    $related_posts = [];

    foreach ($related_posts_data as $post) {

        $post_categories = get_the_category($post->ID);
        $first_category = !empty($post_categories) ? $post_categories[0]->name : ''; // Get the first category name

        // Create an array to hold post details
        $post_details = [
            'title'       => $post->title,
            'link'        => $post->link,
            'id'          => $post->ID,
            'thumbnail'   => get_the_post_thumbnail_url($post->ID, 'full'), // Adjust size as needed
            'description' => get_the_excerpt($post->ID), // or get_post_field('post_content', $post->ID) for full content
            'first_category' => $first_category, // Store the first category name
        ];

        // Retrieve schema options for the current related post
        $meta_key   = 'wp_review_schema_options';
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

    // Store related posts in the context for rendering
    $context['related_posts'] = $related_posts;



}

$post_content = $post->post_content;

$context['has_shortcode_specifications_section'] = has_shortcode($post_content, "specifications_section");
$context['has_shortcode_pros_section'] = has_shortcode($post_content, "pros_section");
$context['has_shortcode_review_section'] = has_shortcode($post_content, "review_section");
$context['has_shortcode_more_products_prices_section'] = has_shortcode($post_content, "more_products_prices_section");
$context['has_shortcode_qa_section'] = has_shortcode($post_content, "qa_section");














function get_comparable_products($current_post_id) {
    $tags = wp_get_post_terms($current_post_id, 'post_tag', ['fields' => 'ids']);
    $first_tag = !empty($tags) ? $tags[0] : null;

    $current_price = floatval(get_post_meta($current_post_id, 'wp_review_product_price', true));
    $current_rating = floatval(get_post_meta($current_post_id, 'wp_review_total', true));
    // Check if the values are being retrieved correctly
    if (!$current_price || !$current_rating) {
        echo 'Price or rating is missing for the current product.';
        return new WP_Query();
    }

    $args = [
        'post_type' => 'post',
        'post__not_in' => [$current_post_id],
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
$current_post_id = get_the_ID();
$comparable_products = get_comparable_products($current_post_id);

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



Timber::render('content/single.twig', $context);
