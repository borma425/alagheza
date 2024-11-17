<?php
  
  function wp_review_get_review_items( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $items = get_post_meta( $post_id, 'wp_review_item', true );

    return apply_filters( 'wp_review_items', $items );
}

$context = Timber::context();

// Check if 'items' parameter is set in the URL
if (isset($_GET['items'])) {
    $devices = json_decode(strip_tags((string) wp_unslash($_GET['items'])), true);

    $selected_devices = [];

    foreach ($devices as $device_id) {
        $device_post = Timber::get_post($device_id);

        if ($device_post) {
            // Get device meta data
            $device_specs = get_post_meta($device_id, 'device_specs', true);
            $device_specifications_main_title = get_post_meta($device_id, 'device_specifications_main_title', true);
            $product_thumbnail = get_post_meta($device_id, 'product_thumbnail', true);

            // Get the review data
            $total_review = get_post_meta($device_id, 'wp_review_total', true);
            $items = wp_review_get_review_items($device_id);
            $transformed_array = [];

            if (is_array($items)) {
                foreach ($items as $item) {
                    $unique_key = (string) time() . uniqid();
                    $transformed_array[$unique_key] = [
                        'key' => $item['wp_review_item_title'],
                        'value' => $item['wp_review_item_star'],
                    ];
                }
            }

            // Get the price
            $meta_key   = 'wp_review_schema_options';
            $meta_value = get_post_meta($device_id, $meta_key, true);
            if (!is_array($meta_value)) {
                $decoded = json_decode($meta_value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $meta_value = $decoded;
                } else {
                    $meta_value = [];
                }
            }

            $product_price = isset($meta_value['Product']['price']) && $total_review ? preg_replace('/\D/', '', $meta_value['Product']['price']) . " جنية مصري " : '';

            // Prepare device data
            $selected_devices[] = [
                'name' => $device_specifications_main_title ?: $device_post->post_title,
                'image' => $product_thumbnail,
                'specs' => $device_specs,
                'total_review' => $total_review,
                'ratings' => $transformed_array,
                'price' => $product_price,
            ];
        }
    }

    $context['devices'] = $selected_devices;
}

Timber::render('compare.twig', $context);

