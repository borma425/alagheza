<?php


$context = Timber::context();

$context['is_front_page'] = false;

$context["posts"] = Timber::get_posts( array(
    'post_type'      => "sections",
    'posts_per_page' => -1,
    'paged' => $paged,
)  );

Timber::render('articles.twig', $context);
