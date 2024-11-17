<script type="application/ld+json">
<?php

// Helper function to process lists of pros and cons into ItemList
function prepare_list_items($list_content) {
    // Use a simple regex to extract items directly, avoiding DOMDocument overhead
    preg_match_all('/<li.*?>(.*?)<\/li>/i', $list_content, $matches);
    return array_map('trim', $matches[1]);
}

// Function to fetch product schema data
function get_product_schema_data($post_id) {
    $product_data = get_post_meta($post_id, 'wp_review_schema_options', true);
    // Safely decode the product schema options
    $product_data = is_array($product_data) ? $product_data : json_decode($product_data, true);

    // Return product details with fallback values
    return [
        'name' => $product_data['Product']['name'] ?? get_the_title($post_id),
        'description' => $product_data['Product']['description'] ?? get_the_content($post_id),
        'brand' => $product_data['Product']['brand'] ?? '',
        'sku' => $product_data['Product']['sku'] ?? '',
        'price' => $product_data['Product']['price'] ?? '',
        'priceCurrency' => $product_data['Product']['priceCurrency'] ?? 'EGP',
        'review_title' => $product_data['Product']['review_title'] ?? 'Product Review',
        'review_desc' => $product_data['Product']['review_desc'] ?? 'No description available.'
    ];
}

// Generate the main product schema
function generate_product_schema($post_id, $product) {
    // Fetch pros and cons lists
    $pros = prepare_list_items(get_post_meta($post_id, 'wp_review_pros', true));
    $cons = prepare_list_items(get_post_meta($post_id, 'wp_review_cons', true));

    // Create the JSON-LD schema for the product
    return [
        "@context" => "https://schema.org",
        "@type" => "Product",
        "name" => $product['name'],
        "image" => get_the_post_thumbnail_url($post_id, 'full'),
        "description" => $product['description'],
        "brand" => ["@type" => "Brand", "name" => $product['brand']],
        "sku" => $product['sku'],
        "offers" => [
            "@type" => "Offer",
            "priceCurrency" => $product['priceCurrency'],
            "price" => $product['price'],
            "url" => get_permalink($post_id)
        ],
        "review" => [
            "@type" => "Review",
            "name" => $product['review_title'],
            "reviewBody" => $product['review_desc'],
            "positiveNotes" => generate_item_list($pros),
            "negativeNotes" => generate_item_list($cons)
        ]
    ];
}

// Generate ItemList for pros/cons
function generate_item_list($items) {
    return [
        "@type" => "ItemList",
        "itemListElement" => array_map(function ($item, $index) {
            return [
                "@type" => "ListItem",
                "position" => $index + 1,
                "name" => $item
            ];
        }, $items, array_keys($items))
    ];
}

// Fetch post ID and common post data
$post_id = get_the_ID();
$product = get_product_schema_data($post_id);

// Output the product schema in JSON-LD format
echo wp_json_encode(generate_product_schema($post_id, $product), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
</script>

<script type="application/ld+json">
<?php

// Function to generate comparable products schema data
function get_comparable_products_schema($post_id) {
    $comparable_products = get_comparison_table_data($post_id);
    $comparable_schema = [
        "@context" => "https://schema.org",
        "@type" => "ItemList",
        "name" => "المقارنه بين الأجهزة",
        "itemListElement" => []
    ];

    // Loop over the first 3 comparable products
    foreach (array_slice($comparable_products, 0, 3) as $device) {
        // Process each device
        $pros_list = prepare_list_items(get_post_meta($device['id'], 'wp_review_pros', true));
        $cons_list = prepare_list_items(get_post_meta($device['id'], 'wp_review_cons', true));

        $comparable_schema['itemListElement'][] = [
            "@type" => "Product",
            "name" => $device['name'],
            "image" => $device['image'],
            "description" => $device['description'],
            "brand" => ["@type" => "Brand", "name" => $device['brand']],
            "aggregateRating" => [
                "@type" => "AggregateRating",
                "ratingValue" => $device['total_review'],
                "bestRating" => "5",
                "worstRating" => "2"
            ],
            "review" => [
                "@type" => "Review",
                "reviewBody" => $device['description'] ?: 'No review available.',
                "reviewRating" => [
                    "@type" => "Rating",
                    "ratingValue" => $device['total_review'],
                    "bestRating" => "5",
                    "worstRating" => "2"
                ],
                "positiveNotes" => generate_item_list($pros_list),
                "negativeNotes" => generate_item_list($cons_list)
            ]
        ];
    }

    return $comparable_schema;
}

// Output the comparable products schema in JSON-LD format
echo wp_json_encode(get_comparable_products_schema($post_id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
</script>

<script type="application/ld+json">
<?php

// Article Schema
function generate_article_schema($post_id) {
    $post_title = get_the_title($post_id);
    $post_content = get_the_content($post_id);
    $post_date_published = get_the_date('c', $post_id);
    $post_date_modified = get_the_modified_date('c', $post_id);
    $post_author_name = get_the_author_meta('display_name', get_post_field('post_author', $post_id));
    $post_author_link = get_author_posts_url(get_post_field('post_author', $post_id));
    $post_category = get_the_category($post_id);
    $post_category_name = $post_category ? $post_category[0]->name : '';
    $post_tags = get_the_tags($post_id);
    $post_tags_names = $post_tags ? implode(', ', wp_list_pluck($post_tags, 'name')) : '';
    $post_thumbnail = get_the_post_thumbnail_url($post_id, 'full');

    return [
        "@context" => "https://schema.org",
        "@type" => "Article",
        "headline" => $post_title,
        "description" => wp_trim_words($post_content, 20, '...'),
        "datePublished" => $post_date_published,
        "dateModified" => $post_date_modified,
        "inLanguage" => "ar",
        "image" => [
            "@type" => "ImageObject",
            "url" => $post_thumbnail
        ],
        "author" => [
            "@type" => "Person",
            "name" => $post_author_name,
            "url" => $post_author_link
        ],
        "articleSection" => $post_category_name,
        "keywords" => $post_tags_names,
        "articleBody" => str_replace('"', "'", $post_content),
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => get_permalink($post_id)
        ]
    ];
}

// Output the article schema in JSON-LD format
echo wp_json_encode(generate_article_schema($post_id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
</script>
