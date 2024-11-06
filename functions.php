<?php

require_once __DIR__ . '/vendor/autoload.php';

define('DEV_MODE', true);

// Initialize Timber.
Timber\Timber::init();

require(get_theme_file_path('inc/view-page-source.php'));

require(get_theme_file_path('inc/setup.php'));
require(get_theme_file_path('inc/enqueues.php'));
require(get_theme_file_path('inc/custom_post_type.php'));
require(get_theme_file_path('inc/acf/main.php'));
require(get_theme_file_path('inc/customizer/main.php'));

