
<?php

$context = Timber::context();

$context['is_front_page'] = true;







$args = array(
    'category_name' => 'home-electronic-devices-advices', // Slug of the category
    'posts_per_page' => 6, // Adjust the number of posts as needed
);

$context['advice_posts'] = Timber::get_posts($args);



$context['aqsam'] = get_sorted_posts_by_taxonomy('category', 'sections', 6);


$context['brands'] = get_sorted_posts_by_taxonomy('post_tag', 'brands', 6);

$context['related_posts'] = Timber::get_posts(array(
    'post_type' => 'post', // Slug of the category
    'posts_per_page' => 8, // Adjust the number of posts as needed
));







Timber::render('index-home.twig', $context);
