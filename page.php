<?php


$context = Timber::context();



$context['is_front_page'] = false;



Timber::render('content/single-article.twig', $context);









