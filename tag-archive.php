
function get_price_range($value) {
    echo "<br><h1>السعر المحدد $value</h1>";

    if ($value <= 10000) {
        return [0, 10000]; // From 0 to 10,000
    } elseif ($value <= 20000) {
        return [10001, 20000]; // From 10,001 to 20,000
    } elseif ($value <= 40000) {
        return [20001, 40000]; // From 20,001 to 40,000
    } elseif ($value <= 60000) {
        return [40001, 60000]; // From 40,001 to 60,000
    } elseif ($value <= 80000) {
        return [60001, 80000]; // From 60,001 to 80,000
    } elseif ($value <= 100000) {
        return [80001, 100000]; // From 80,001 to 100,000
    } else {
        return [100001, PHP_INT_MAX]; // From 100,001 and above
    }
}


function wp_review_get_product_price( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    return get_post_meta( $post_id, 'wp_review_product_price', true );
}

$selected_value = 30000; // Change this based on your needs

// Get the price range for the selected value
list($min_price, $max_price) = get_price_range($selected_value);


// Create a meta query to find posts where the product price matches
$args = [
    'post_type' => 'post', // Replace with your actual post type
    'meta_query' => [
        [
            'key' => 'wp_review_product_price',
            'value' => [$min_price, $max_price],
            'compare' => 'BETWEEN',
            'type' => 'NUMERIC', // Ensure it's treated as a number
        ],
    ],
];




    $context["posts"] = Timber::get_posts( $args );
