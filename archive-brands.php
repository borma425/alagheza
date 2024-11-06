<?php


$context = Timber::context();

$context['is_front_page'] = false;

$context["brands"] = Timber::get_posts( array(
    'post_type'      => "brands",
    'posts_per_page' => -1,
    'paged' => $paged,
)  );

Timber::render('archive-brands.twig', $context);
