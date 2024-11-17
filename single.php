<?php

$context = Timber::context();

$context['is_front_page'] = false;
$current_post_id = get_the_ID();
$current_post_categories = wp_get_post_categories($current_post_id);
global $paged;

if (!isset($paged) || !$paged) {
    $paged = 1;
}

$related_posts = [];

if (!empty($current_post_categories)) {
    // Query posts with matching categories
    $related_posts_data = Timber::get_posts([
        'post_type'      => 'post',
        'posts_per_page' => 4,
        'paged'          => $paged,
        'category__in'   => $current_post_categories, // Use 'category__in' for categories
        'post__not_in'   => [$current_post_id],
    ]);

    foreach ($related_posts_data as $post) {
        $post_categories = get_the_category($post->ID);
        $first_category = !empty($post_categories) ? $post_categories[0]->name : '';

        $meta_key = 'wp_review_schema_options';
        $meta_value = get_post_meta($post->ID, $meta_key, true);
        if (!is_array($meta_value)) {
            $decoded = json_decode($meta_value, true);
            $meta_value = json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : [];
        }

        $product_price = $meta_value['Product']['price'] ?? '';
        $related_posts[] = [
            'title'         => $post->title,
            'link'          => $post->link,
            'id'            => $post->ID,
            'thumbnail'     => get_the_post_thumbnail_url($post->ID, 'full'),
            'description'   => get_the_excerpt($post->ID),
            'first_category'=> $first_category,
            'price'         => $product_price,
            'total_rating'  => get_post_meta($post->ID, "wp_review_total", true),
        ];
    }
}

$context['related_posts'] = $related_posts;

// Check for shortcodes
$post_content = get_post_field('post_content', $current_post_id);

$context['has_shortcode_specifications_section'] = has_shortcode($post_content, "specifications_section");
$context['has_shortcode_pros_section'] = has_shortcode($post_content, "pros_section");
$context['has_shortcode_review_section'] = has_shortcode($post_content, "review_section");
$context['has_shortcode_more_products_prices_section'] = has_shortcode($post_content, "more_products_prices_section");
$context['has_shortcode_qa_section'] = has_shortcode($post_content, "qa_section");

// Function to get review items
function wp_review_get_review_items($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    return apply_filters('wp_review_items', get_post_meta($post_id, 'wp_review_item', true));
}

// Function to get comparable products
function get_comparable_products($current_product_id) {
    $categories = wp_get_post_terms($current_product_id, 'category', ['fields' => 'ids']);
    $first_category = $categories ? $categories[0] : null;

    $current_price = floatval(get_post_meta($current_product_id, 'wp_review_product_price', true));
    $current_rating = floatval(get_post_meta($current_product_id, 'wp_review_total', true));

    if (!$current_price || !$current_rating) {
        return new WP_Query();
    }

    return new WP_Query([
        'post_type' => 'post',
        'post__not_in' => [$current_product_id],
        'posts_per_page' => 3,
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
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $first_category,
            ],
        ],
    ]);
}

// Function to get comparison table data
function get_comparison_table_data($current_product_id) {
    $comparable_products = get_comparable_products($current_product_id);

    if (!$comparable_products->have_posts()) {
        return [];
    }

    $devices = [];
    while ($comparable_products->have_posts()) {
        $comparable_products->the_post();
        $device_id = get_the_ID();

        $device_post = Timber::get_post($device_id);
        $device_specs = get_post_meta($device_id, 'device_specs', true);
        $device_specifications_main_title = get_post_meta($device_id, 'device_specifications_main_title', true);
        $product_thumbnail = get_post_meta($device_id, 'product_thumbnail', true);

        $meta_key = 'wp_review_schema_options';
        $meta_value = get_post_meta($device_id, $meta_key, true);
        $meta_value = is_array($meta_value) ? $meta_value : json_decode($meta_value, true);
        $product_price = $meta_value['Product']['price'] ?? '';

        $total_review = get_post_meta($device_id, 'wp_review_total', true);
        $items = wp_review_get_review_items($device_id);
        $transformed_array = [];

        if (is_array($items)) {
            foreach ($items as $item) {
                $transformed_array[] = [
                    'key' => $item['wp_review_item_title'],
                    'value' => $item['wp_review_item_star'],
                ];
            }
        }

        $devices[] = [
            'name' => $device_specifications_main_title ?: $device_post->post_title,
            'image' => $product_thumbnail ?: get_the_post_thumbnail_url($device_id, 'medium'),
            'specs' => $device_specs,
            'total_review' => $total_review,
            'price' => $product_price,
            'link' => $device_post->link,
            'ratings' => $transformed_array,
            'id' => $device_id,
            'brand'         => $meta_value['Product']['brand'] ?? '',
            'description'         => $meta_value['Product']['description'] ?? '',

        ];
    }

    wp_reset_postdata();

    return $devices;
}

$context['comparison_table'] = get_comparison_table_data(get_the_ID());

$context['sidebar_sections'] = Timber::get_posts([
    'post_type' => 'sections',
    'posts_per_page' => 2,
    'tax_query' => [
        [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => 'article',
        ]
    ]
]);

$context['sidebar_brands'] = Timber::get_posts([
    'post_type' => 'brands',
    'posts_per_page' => 2,
    'tax_query' => [
        [
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => 'article',
        ]
    ]
]);






Timber::render('content/single.twig', $context);


require(get_theme_file_path('inc/schema_gen.php'));

?>


