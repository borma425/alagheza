<?php

$context = Timber::context();
$context['is_front_page'] = false;

// Default device data (you can replace this with a real data source)
$devices_data = [
    44 => [
        'name' => 'Samsung Galaxy S24',
        'image' => 'https://mellow-douhua-911077.netlify.app/includes/images/m1.jpg',
        'screen' => '6.2 بوصة، Dynamic AMOLED 2X',
        'processor' => 'Exynos 2400',
        'camera' => '50 ميجابكسل',
        'battery' => '4000 مللي أمبير',
        'os' => 'Android 14 مع One UI 6.1',
    ],
    33 => [
        'name' => 'iPhone 15',
        'image' => 'https://mellow-douhua-911077.netlify.app/includes/images/m1.jpg',
        'screen' => '6.1 بوصة، Super Retina XDR OLED',
        'processor' => 'A16 Bionic',
        'camera' => '48 ميجابكسل',
        'battery' => '3349 مللي أمبير',
        'os' => 'iOS 17',
    ]
];

// Check if 'items' parameter exists and is valid
if (isset($_GET['items'])) {
    // Get the items parameter and decode the JSON string
    $device_ids = json_decode(strip_tags((string) wp_unslash($_GET['items'])), true);

    // Validate and retrieve device data based on IDs
    $selected_devices = [];
    foreach ($device_ids as $device_id) {
        if (isset($devices_data[$device_id])) {
            $selected_devices[] = $devices_data[$device_id];  // Add device data to the list
        }
    }

    // Pass the selected devices to the context
    $context['devices'] = $selected_devices;
}

// Render the template
Timber::render('compare.twig', $context);
