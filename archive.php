<?php


$context = Timber::context();

$context['is_front_page'] = false;




// Get the current category object
$category = get_queried_object();

// Check if the object is a category and then use it
if (is_category()) {
    // Get category ID
    $category_id = $category->term_id;
    // Get category name
    $category_name = $category->name;
    // Get category URL
    $category_link = get_category_link($category_id);


$context['cat_name'] = esc_html($category_name);

$context['posts'] = Timber::get_posts(array(
    'post_type' => 'post',
    'posts_per_page' => 8, // Adjust the number of posts as needed
    'cat' => $category_id, // Query posts by current category ID
));

Timber::render('archive.twig', $context);

}


