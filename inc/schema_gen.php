<?php

    // Generate Article schema
    function generate_article_schema($post_id) {
        $author_id = get_post_field('post_author', $post_id);
        return [
            "@context" => "https://schema.org",
            "@type" => "Article",
            "headline" => get_the_title($post_id),
            "description" => wp_trim_words(get_the_excerpt($post_id), 20),
            "datePublished" => get_the_date('c', $post_id),
            "dateModified" => get_the_modified_date('c', $post_id),
            "author" => [
                "@type" => "Person",
                "name" => get_the_author_meta('display_name', $author_id),
                "url" => get_author_posts_url($author_id)
            ],
            "articleSection" => get_the_category($post_id)[0]->name ?? '',
            "keywords" => implode(', ', wp_list_pluck(get_the_tags($post_id), 'name') ?? []),
            "image" => [
                "@type" => "ImageObject",
                "url" => get_the_post_thumbnail_url($post_id, 'full')
            ],
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id" => get_permalink($post_id)
            ],
            "articleBody" =>wp_kses(
                do_shortcode(apply_filters('the_content', get_post_field('post_content', $post_id))),
                [
                    'strong' => [],
                    'em' => [],
                    'a' => ['href' => true, 'title' => true, 'target' => true, 'rel' => true],
                    'p' => [],
                    'ul' => [],
                    'li' => [],
                    'ol' => [],
                    'br' => [],
                    'span' => ['class' => true, 'style' => true],
                    'div' => ['class' => true, 'style' => true],
                    'h1' => [],
                    'h2' => [],
                    'h3' => [],
                    'h4' => [],
                    'h5' => [],
                    'h6' => [],
                    'blockquote' => ['cite' => true],
                    'img' => ['src' => true, 'alt' => true, 'title' => true, 'width' => true, 'height' => true, 'class' => true],
                    'code' => [],
                    'pre' => ['class' => true],
                    'figure' => ['class' => true],
                    'figcaption' => [],
                    'table' => ['class' => true, 'border' => true],
                    'thead' => [],
                    'tbody' => [],
                    'tr' => [],
                    'th' => ['scope' => true, 'colspan' => true, 'rowspan' => true],
                    'td' => ['colspan' => true, 'rowspan' => true],
                ]
            ),

        ];
    }

    
if ( $has_shortcode_review_section ) {
    // Function to safely decode JSON or fallback to array
    function safe_json_decode($data) {
        $decoded = json_decode($data, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    // Function to extract list items (pros/cons)
    function extract_list_items($list_content) {
        preg_match_all('/<li.*?>(.*?)<\/li>/i', $list_content, $matches);
        return array_map('trim', $matches[1] ?? []);
    }

    // Function to generate an ItemList schema
    function generate_item_list_schema($items) {
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

    // Fetch product data
    function get_product_data($post_id) {
        $raw_data = get_post_meta($post_id, 'wp_review_schema_options', true);
        $data = is_array($raw_data) ? $raw_data : safe_json_decode($raw_data);
        return [
            'name' => $data['Product']['name'] ?? get_the_title($post_id),
            'description' => $data['Product']['description'] ?? get_the_excerpt($post_id),
            'brand' => $data['Product']['brand'] ?? '',
            'sku' => $data['Product']['sku'] ?? '',
            'price' => $data['Product']['price'] ?? '',
            "priceCurrency" => "EGP",
            'review_title' => $data['Product']['review_title'] ?? '',
            'review_desc' => $data['Product']['review_desc'] ?? ''
        ];
    }

    // Generate Product schema
    function generate_product_schema($post_id) {
        $product = get_product_data($post_id);
        $pros = extract_list_items(get_post_meta($post_id, 'wp_review_pros', true));
        $cons = extract_list_items(get_post_meta($post_id, 'wp_review_cons', true));
        $author_id = get_post_field('post_author', $post_id);
        $totalreview = get_post_meta($post_id, 'wp_review_total', true);

        return [
            "@context" => "https://schema.org",
            "@type" => "Product",
            "name" => $product['name'],
            "image" => get_the_post_thumbnail_url($post_id, 'full'),
            "description" => $product['description'],
            "brand" => ["@type" => "Brand", "name" => $product['brand']],
            "sku" => $product['sku'],
            "aggregateRating" => [
                "@type" => "AggregateRating",
                "ratingValue" => $totalreview,
                "bestRating" => "5",
                "worstRating" => "2",
                "reviewCount" =>$totalreview / $totalreview ?: 0
            ],
            "offers" => [
                "@type" => "Offer",
                "priceCurrency" => "EGP",
                "price" => $product['price'],
                "url" => get_permalink($post_id)
            ],
            "review" => [
                "@type" => "Review",
                "name" => $product['review_title'],
                "reviewBody" => $product['review_desc'],
                "author" => [
                    "@type" => "Person",
                    "name" => get_the_author_meta('display_name', $author_id),
                    "url" => get_author_posts_url($author_id)
                ],
                "positiveNotes" => generate_item_list_schema($pros),
                "negativeNotes" => generate_item_list_schema($cons)
            ]
        ];
    }

    // Generate Comparable Products schema
    function generate_comparable_products_schema($post_id) {
        $comparable_products = get_comparison_table_data($post_id);
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "name" => "جدول المقارنه",
            "itemListElement" => []
        ];

        foreach (array_slice($comparable_products, 0, 3) as $product) {
            $pros = extract_list_items(get_post_meta($product['id'], 'wp_review_pros', true));
            $cons = extract_list_items(get_post_meta($product['id'], 'wp_review_cons', true));
            $totalreview = get_post_meta($post_id, 'wp_review_total', true);

            $schema['itemListElement'][] = [
                "@type" => "Product",
                "name" => $product['name'],
                "image" => $product['image'],
                "description" => $product['description'],
                "brand" => ["@type" => "Brand", "name" => $product['brand']],
                "aggregateRating" => [
                    "@type" => "AggregateRating",
                    "ratingValue" => $product['total_review'],
                    "bestRating" => "5",
                  "worstRating" => "2",
                  "reviewCount" =>$totalreview / $totalreview ?: 0
                ],
                "review" => [
                    "@type" => "Review",
                    "reviewBody" => $product['description'],
                    "reviewRating" => [
                        "@type" => "Rating",
                        "ratingValue" => $product['total_review'],
                        "bestRating" => "5",
                        "worstRating" => "2"
                    ],
                    "positiveNotes" => generate_item_list_schema($pros),
                    "negativeNotes" => generate_item_list_schema($cons)
                ]
            ];
        }

        return $schema;
    }



    // Render JSON-LD scripts
    ?>
    <script type="application/ld+json">
        <?php echo wp_json_encode(generate_product_schema($post_id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>
    <script type="application/ld+json">
        <?php echo wp_json_encode(generate_comparable_products_schema($post_id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>

    <?php
}

?>
    <script type="application/ld+json">
        <?php echo wp_json_encode(generate_article_schema($post_id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
    </script>