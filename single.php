<?php
use Timber\Timber;


$context = Timber::context();

// Optionally enable caching
$cache_key = $post->ID;
$context['post'] = Timber::get_post($cache_key);

Timber::render('content/single.twig', $context);