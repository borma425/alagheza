<?php


$context = Timber::context();

$context['is_front_page'] = false;




Timber::render('pages/page.twig', $context);
