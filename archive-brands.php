<?php


$context = Timber::context();

$context['is_front_page'] = false;

$context['brands'] = get_sorted_posts_by_taxonomy('post_tag', 'brands', -1);

Timber::render('archive-brands.twig', $context);
