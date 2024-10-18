
<?php

$context = Timber::context();

$context['is_front_page'] = true;

$args = array(
    'category_name' => 'home-electronic-devices-advices', // Slug of the category
    'posts_per_page' => 5, // Adjust the number of posts as needed
);

$context['advice_posts'] = Timber::get_posts($args);



Timber::render('index-home.twig', $context);
