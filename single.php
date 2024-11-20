<?php

$context = Timber::context();

$context['is_front_page'] = false;
$post_id = get_the_ID();


$current_post_categories = wp_get_post_categories($post_id);
global $paged;

if (!isset($paged) || !$paged) {
    $paged = 1;
}



// Ensure categories exist to avoid errors
if (!empty($current_post_categories)) {
    $context['related_posts'] = Timber::get_posts([
        'post_type' => 'post', // Replace with your custom post type if needed
        'posts_per_page' => 4, // Number of related posts to display
        'post__not_in' => [$post_id], // Exclude the current post
        'tax_query' => [
            [
                'taxonomy' => 'category', // Use category taxonomy
                'field' => 'term_id', // Match by term ID
                'terms' => $current_post_categories, // Use current post's categories
            ]
        ]
    ]);
} else {
    $context['related_posts'] = []; // No related posts
}


// Check for shortcodes
$post_content = get_post_field('post_content', $post_id);

$has_shortcode_review_section = has_shortcode($post_content, "review_section");
$context['has_shortcode_specifications_section'] = has_shortcode($post_content, "specifications_section");
$context['has_shortcode_pros_section'] = has_shortcode($post_content, "pros_section");
$context['has_shortcode_review_section'] = $has_shortcode_review_section;
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

if ($has_shortcode_review_section ) {

$context['comparison_table'] = get_comparison_table_data(get_the_ID());

}


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



        $adsense_data = get_option('adsense_data', []);

        // Get the post content
        $post = Timber::get_post();
        $content = $post->content;

        // Split content into paragraphs
        $paragraphs = explode('</p>', $content);

        // Insert AdSense codes after the specified paragraphs
        foreach ($adsense_data as $data) {
            $position = $data['position'];
            $code = $data['code'];

            // Add the AdSense code at the specified position
            foreach ($paragraphs as $index => $paragraph) {
                $paragraphs[$index] .= '</p>'; // Re-add closing </p>
                if ($index + 1 == $position) {
                    $paragraphs[$index] .= $code; // Insert AdSense code
                }
            }
        }

        // Rebuild content with ads inserted
        $modified_content = implode('', $paragraphs);

        // Pass the modified content to the Timber context
        $context = Timber::context();
        $context['content'] = $modified_content;

Timber::render('content/single.twig', $context);


require(get_theme_file_path('inc/schema_gen.php'));

?>


