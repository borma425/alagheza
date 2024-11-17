
<?php

$context = Timber::context();

$context['is_front_page'] = true;




$args = array(
    'category_name' => 'home-electronic-devices-advices', // Slug of the category
    'posts_per_page' => 8, // Adjust the number of posts as needed
);

$context['advice_posts'] = Timber::get_posts($args);

$context['aqsam'] = get_sorted_posts_by_taxonomy('category', 'sections', 6);

$context['brands'] = get_sorted_posts_by_taxonomy('post_tag', 'brands', 6);


// Helper function to get one post per category
function get_one_post_per_category($category_ids) {
    $posts = [];
    foreach ($category_ids as $category_id) {
        $post = Timber::get_posts(array(
            'post_type' => 'post',
            'category__in' => array($category_id),
            'posts_per_page' => 1,
            'orderby' => 'rand',
        ));
        if (!empty($post)) {
            $posts[] = $post[0]; // Only add the first (unique) post
        }
    }
    return $posts;
}



// Define the contexts with category names and their posts
$context['post_contexts'] = [
    'Home_Appliances' => get_one_post_per_category([12, 10, 11, 9]),
    'Electronic_Devices' => get_one_post_per_category([494, 504, 521, 381]),
    'Kitchen_Appliances' => get_one_post_per_category([88, 278, 386, 272]),
    'Other_Devices' => Timber::get_posts(array(
        'post_type' => 'post',
        'category__in' => array(592),
        'posts_per_page' => 4,
        'orderby' => 'rand',
    )),

];


Timber::render('index-home.twig', $context);
