<?php

$context = Timber::context();

$context['is_front_page'] = false;

// Check if 'items' parameter is set in the URL
if (isset($_GET['items'])) {
    // Decode the JSON string and sanitize it
    $devices = json_decode(strip_tags((string) wp_unslash($_GET['items'])), true);

    // Initialize an array to store the selected device posts
    $selected_devices = [];

    // Loop through each device ID and get the post details
    foreach ($devices as $device_id) {
        // Get the post by ID
        $device_post = Timber::get_post($device_id);

        // Ensure the post exists before adding to the array
        if ($device_post) {
            // Retrieve meta data for each device
            $device_specs = get_post_meta($device_id, 'device_specs', true);
            $device_specifications_main_title = get_post_meta($device_id, 'device_specifications_main_title', true);
            $product_thumbnail = get_post_meta($device_id, 'product_thumbnail', true);

            // Prepare specifications array for each device
            $specifications = [];
            if (!empty($device_specs)) {
                foreach ($device_specs as $spec) {
                    $specifications[] = [
                        'name' => $spec['name'] ?? '',
                        'description' => $spec['description'] ?? '',
                        'icon' => $spec['icon'] ?? '',
                    ];
                }
            }

            // Structure data for each device
            $selected_devices[] = [
                'name' => $device_specifications_main_title ?: $device_post->post_title,
                'image' => $product_thumbnail,
                'specs' => $specifications
            ];
        }
    }

    // Add the selected devices to the context
    $context['devices'] = $selected_devices;
}

// Render the compare.twig template with the context
Timber::render('compare.twig', $context);
