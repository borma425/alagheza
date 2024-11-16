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




function wp_review_get_review_items( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $items = get_post_meta( $post_id, 'wp_review_item', true );

    return apply_filters( 'wp_review_items', $items );
}







function get_comparable_products($current_product_id) {
    // جلب التصنيفات المرتبطة بالمقال الحالي
    $categories = wp_get_post_terms($current_product_id, 'category', ['fields' => 'ids']);
    $first_category = !empty($categories) ? $categories[0] : null;

    // جلب بيانات السعر والتقييم
    $current_price = floatval(get_post_meta($current_product_id, 'wp_review_product_price', true));
    $current_rating = floatval(get_post_meta($current_product_id, 'wp_review_total', true));

    // التحقق من أن القيم موجودة
    if (!$current_price || !$current_rating) {
        echo 'Price or rating is missing for the current product.';
        return new WP_Query();
    }

    // إعداد استعلام WP_Query
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
                'taxonomy' => 'category', // استخدام التصنيفات بدلاً من الوسوم
                'field' => 'term_id',
                'terms' => $first_category, // التصنيف الأول
            ],
        ],
    ];

    return new WP_Query($args);
}










function get_comparison_table_data($current_product_id) {
    // استدعاء المنتجات القابلة للمقارنة
    $comparable_products = get_comparable_products($current_product_id);

    if (!$comparable_products->have_posts()) {
        return [];
    }

    $devices = [];
    while ($comparable_products->have_posts()) {
        $comparable_products->the_post();
        $device_id = get_the_ID();

        // جلب البيانات
        $device_post = Timber::get_post($device_id);
        $device_specs = get_post_meta($device_id, 'device_specs', true);
        $device_specifications_main_title = get_post_meta($device_id, 'device_specifications_main_title', true);
        $product_thumbnail = get_post_meta($device_id, 'product_thumbnail', true);
        $total_review = get_post_meta($device_id, 'wp_review_total', true);

        // جلب السعر
        $meta_key = 'wp_review_schema_options';
        $meta_value = get_post_meta($device_id, $meta_key, true);
        $meta_value = is_array($meta_value) ? $meta_value : json_decode($meta_value, true);
        $product_price = $meta_value['Product']['price'] ?? '';

        // تجهيز البيانات
        $devices[] = [
            'name' => $device_specifications_main_title ?: $device_post->post_title,
            'image' => $product_thumbnail ?: get_the_post_thumbnail_url($device_id, 'medium'),
            'specs' => $device_specs,
            'total_review' => $total_review,
            'price' => $product_price,
        ];
    }
    wp_reset_postdata();

    return $devices;
}

$context['comparison_table'] = get_comparison_table_data(get_the_ID());

Timber::render('content/single.twig', $context);
