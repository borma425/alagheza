
<?php

$context = Timber::context();

$context['is_front_page'] = true;

$args = array(
    'category_name' => 'home-electronic-devices-advices', // Slug of the category
    'posts_per_page' => 5, // Adjust the number of posts as needed
);

$context['advice_posts'] = Timber::get_posts($args);



$context['aqsam'] = Timber::get_posts(array(
    'post_type' => 'sections', // Slug of the category
    'posts_per_page' => 6, // Adjust the number of posts as needed
));


$context['brands'] = Timber::get_posts(array(
    'post_type' => 'brands', // Slug of the category
    'posts_per_page' => 5, // Adjust the number of posts as needed
));

$context['related_posts'] = Timber::get_posts(array(
    'post_type' => 'post', // Slug of the category
    'posts_per_page' => 8, // Adjust the number of posts as needed
));


Timber::render('index-home.twig', $context);
