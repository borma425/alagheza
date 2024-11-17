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
?>



<script type="application/ld+json">
<?php
// Handle lists safely
function prepare_list_items($list_content) {
    $dom = new DOMDocument('1.0', 'UTF-8');
    $html_content = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $list_content . '</body></html>';
    @$dom->loadHTML($html_content, LIBXML_NOERROR | LIBXML_NOWARNING);
    $list_items = $dom->getElementsByTagName('li');
    $result = [];
    foreach ($list_items as $item) {
        $result[] = trim($item->textContent);
    }
    return $result;
}



  
$pros       = get_post_meta($post->ID, 'wp_review_pros', true);
  $cons       = get_post_meta($post->ID, 'wp_review_cons', true);
  $post_id    = get_the_ID();
  
  // For top container title
  $desc_title = get_post_meta($post_id, 'wp_review_desc_title', true);
  if (!$desc_title) {
      $desc_title = __('الملخص', 'wp-review');
  }
  
  $heading = get_post_meta($post_id, 'wp_review_heading', true);
  
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
  
  // Access product details safely
  if (isset($meta_value['Product']) && is_array($meta_value['Product'])) {
      $product_name            = isset($meta_value['Product']['name']) ? $meta_value['Product']['name'] : '';
      $product_desc            = isset($meta_value['Product']['description']) ? $meta_value['Product']['description'] : '';
      $product_brand           = isset($meta_value['Product']['brand']) ? $meta_value['Product']['brand'] : '';
      $product_sku             = isset($meta_value['Product']['sku']) ? $meta_value['Product']['sku'] : '';
      $product_price           = isset($meta_value['Product']['price']) ? $meta_value['Product']['price'] : '';
      $product_priceCurrency   = isset($meta_value['Product']['priceCurrency']) ? $meta_value['Product']['priceCurrency'] : '';
  } else {
      // Set default values or handle the absence of 'Product' key
      $product_name            = '';
      $product_desc            = '';
      $product_brand           = '';
      $product_sku             = '';
      $product_price           = '';
      $product_priceCurrency   = '';
      error_log("Key 'Product' does not exist in meta key '{$meta_key}' for post ID {$post_id}.");
  }
  
  // Continue with the rest of your code
  $wp_review_desc_title = get_post_meta($post_id, 'wp_review_desc_title', true);
  $wp_review_desc_desc  = get_post_meta($post->ID, 'wp_review_desc', true);
  
  
// Extract lists
$pros_list = prepare_list_items($pros);
$cons_list = prepare_list_items($cons);

// JSON-LD schema preparation
$schema_data = [
    "@context" => "https://schema.org",
    "@type" => "Product",
    "name" => $product_name ?: get_the_title($post_id),
    "image" => get_the_post_thumbnail_url($post_id, 'full'),
    "description" => $product_desc ?: $wp_review_desc_desc,
    "brand" => [
        "@type" => "Brand",
        "name" => $product_brand,
    ],
    "sku" => $product_sku,
    "offers" => [
        "@type" => "Offer",
        "priceCurrency" => $product_priceCurrency ?: "EGP",
        "price" => $product_price,
        "url" => get_permalink($post_id),
    ],
    "review" => [
        "@type" => "Review",
        "name" => $wp_review_desc_title ?: $desc_title,
        "reviewBody" => $wp_review_desc_desc,
        "positiveNotes" => [
            "@type" => "ItemList",
            "itemListElement" => array_map(function ($item, $index) {
                return [
                    "@type" => "ListItem",
                    "position" => $index + 1,
                    "name" => $item,
                ];
            }, $pros_list, array_keys($pros_list)),
        ],
        "negativeNotes" => [
            "@type" => "ItemList",
            "itemListElement" => array_map(function ($item, $index) {
                return [
                    "@type" => "ListItem",
                    "position" => $index + 1,
                    "name" => $item,
                ];
            }, $cons_list, array_keys($cons_list)),
        ],
    ],
];

// Output JSON-LD
echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
</script>
