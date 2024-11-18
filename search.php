<?php

if (get_query_var('s')) {

    $search = strip_tags((string) wp_unslash(get_query_var('s')));

    $args = array(
        'post_type'      => array('post'),
        's' => $search,
        'posts_per_page' => 30,
        'order' => 'ASC',
        'paged' => $paged,
    );

    $context = Timber::context();
    $context["count_results"] = count($context["posts"]);
    $context["search_word"] = $search;

    // Check if all required parameters are set
  #  if (isset($_GET['brand'], $_GET['category'], $_GET['price'], $_GET['rating'])) {
    if (isset($_GET['brand']) || isset($_GET['category']) || isset($_GET['price']) || isset($_GET['rating'])) {

        $args = array(
            'post_type'      => array('post'),
            's' => '',
            'posts_per_page' => 30,
            'order' => 'ASC',
            'paged' => $paged,
        );
    
        // Price range function
        function get_price_range($value) {
            if ($value <= 5000) {
                return [0, 5000]; // From 0 to 5,000
            } elseif ($value <= 15000) {
                return [5001, 15000]; // From 5,001 to 15,000
            } elseif ($value <= 30000) {
                return [15001, 30000]; // From 15,001 to 30,000
            } elseif ($value <= 50000) {
                return [30001, 50000]; // From 30,001 to 50,000
            } elseif ($value <= 80000) {
                return [50001, 80000]; // From 50,001 to 80,000
            } elseif ($value <= 120000) {
                return [80001, 120000]; // From 80,001 to 120,000
            } else {
                return [120001, PHP_INT_MAX]; // From 120,001 and above
            }
        }

        // Sanitize and retrieve each parameter
        $brand = isset($_GET['brand']) ? json_decode(strip_tags((string) wp_unslash($_GET['brand']) ), true) : null;
        $category = isset($_GET['category']) ? json_decode(strip_tags((string) wp_unslash($_GET['category']) ), true)  : null;
        $price = isset($_GET['price']) ? strip_tags((string) wp_unslash($_GET['price'])) : null;
        $rating = isset($_GET['rating']) ? strip_tags((string) wp_unslash($_GET['rating'])) : null;

        // Check and set price range if a price filter is set
        if ($price) {
            list($min_price, $max_price) = get_price_range($price);
            $args['meta_query'][] = array(
                'key'     => 'wp_review_product_price', // Replace with the actual ACF field key for price
                'value'   => [$min_price, $max_price],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC', // Ensure it's treated as a number
            );
        }



        if ($rating) {
            // Handle rating range
            if ($rating == '1') {
                $args['meta_query'][] = array(
                    'key'     => 'wp_review_total', // Replace with the actual ACF field key for rating
                    'value'   => [1, 2],
                    'compare' => 'BETWEEN',
                    'type'    => 'NUMERIC',
                );
            } elseif ($rating == '2') {
                $args['meta_query'][] = array(
                    'key'     => 'wp_review_total', // Replace with the actual ACF field key for rating
                    'value'   => [2, 3],
                    'compare' => 'BETWEEN',
                    'type'    => 'NUMERIC',
                );
            } elseif ($rating == '3') {
                $args['meta_query'][] = array(
                    'key'     => 'wp_review_total', // Replace with the actual ACF field key for rating
                    'value'   => [3, 4],
                    'compare' => 'BETWEEN',
                    'type'    => 'NUMERIC',
                );
            } elseif ($rating == '4') {
                $args['meta_query'][] = array(
                    'key'     => 'wp_review_total', // Replace with the actual ACF field key for rating
                    'value'   => [4, 5],
                    'compare' => 'BETWEEN',
                    'type'    => 'NUMERIC',
                );
            }

        }




        if ($category && is_array($category)) {
            // Add tax_query for filtering posts by category IDs
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'category', // Replace 'category' with your custom taxonomy if it's different
                    'field'    => 'id',        // Use 'id' to filter by category IDs
                    'terms'    => $category,   // Array of category IDs
                    'operator' => 'IN',        // Match posts that have any of the specified categories
                ),
            );
        }





        if ($brand && is_array($brand)) {
            // Add tax_query for filtering posts by category IDs
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'post_tag',  // Use 'post_tag' for tags
                    'field'    => 'id',        // Use 'id' to filter by tag IDs
                    'terms'    => $brand,       // Array of tag IDs
                    'operator' => 'IN',        // Match posts that have any of the specified tags
                ),
            );
        }












    }


    // Get the posts based on the constructed arguments
    $posts = Timber::get_posts($args);

    // Initialize an array to hold related posts with prices
    $related_posts = [];

    foreach ($posts as $post) {


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

        $total_ratingg = get_post_meta($post->ID, "wp_review_total", true);
        // Get the product price from the schema options
        $product_price = isset(  $meta_value['Product']['price']) && $total_ratingg ? preg_replace('/\D/', '', $meta_value['Product']['price']) . " جنية مصري " : '';

        // Add the price to the post details
        $post_details['price'] = $product_price;
        $post_details['total_rating'] = $total_ratingg;

        // Add the post details to the related posts array
        $related_posts[] = $post_details;
    }


    $context = Timber::context([
        'posts' => $related_posts,
        'pagination' => $posts,

    ]);
    // Render the template with the context
    Timber::render('search/results.twig', $context);

} else {
    echo esc_html("Some Error Here 425 But Not XSS Hahaaa");
}
