<?php


$context = Timber::context();

$context['is_front_page'] = false;


$context['sections'] = get_sorted_posts_by_taxonomy('category', 'sections', -1);

Timber::render('archive-aqsam.twig', $context);
