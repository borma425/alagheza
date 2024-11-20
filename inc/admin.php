<?php

// Add a new column header to the admin posts table
add_filter('manage_posts_columns', 'add_custom_post_column');
function add_custom_post_column($columns) {
    $columns['custom_column'] = 'نوع المقال'; // Add a new column with a label
    return $columns;
}

// Populate the new column with data
add_action('manage_posts_custom_column', 'populate_custom_post_column', 10, 2);
function populate_custom_post_column($column_name, $post_id) {
    if ($column_name === 'custom_column') {
        // Add your custom data here
        $meta_value = get_post_meta($post_id, 'wp_review_total', true);
        echo !empty($meta_value) ? '<div style="display: flex; font-size: 17px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="yellow" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="12 17.27 18.18 21 15.54 13.97 21 9.24 13.81 8.63 12 2 10.19 8.63 3 9.24 8.46 13.97 5.82 21 12 17.27" />
      </svg>
       <span>مراجعة</span></div>' : '<div style="display: flex; font-size: 17px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
       <path d="M6 2H18C18.55 2 19 2.45 19 3V21C19 21.55 18.55 22 18 22H6C5.45 22 5 21.55 5 21V3C5 2.45 5.45 2 6 2Z" />
       <path d="M6 2V21M18 2V21" />
       <path d="M6 10H18M6 14H18M6 18H18" />
     </svg>
      <span>مقال</span></div>';

    }
}
