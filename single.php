<?php
use Timber\Timber;


$context = Timber::context();


Timber::render('content/single.twig', $context);