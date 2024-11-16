<?php


$context = Timber::context();

$post = Timber::get_post();
$context['post'] = $post;

$args = [
    'post_type'      => 'post',
    'posts_per_page' => 4, // عدد المقالات ذات الصلة
    'post__not_in'   => [$post->ID], // استبعاد المقال الحالي
    'category__in'   => wp_get_post_categories($post->ID), // استهداف الفئات المشتركة
];

$context['related_posts'] = Timber::get_posts($args);

$context['is_front_page'] = false;

Timber::render('content/single.twig', $context);
