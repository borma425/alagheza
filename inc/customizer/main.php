<?php


$currentFile = basename(__FILE__);
// Path to the directory containing PHP files
$directory = get_theme_file_path('inc/customizer/') . '*.php';
// Use glob to get all PHP files in the directory
$phpFiles = glob($directory);
// Loop through each file
foreach ($phpFiles as $file) {
    // Skip the current file
    if (basename($file) !== $currentFile) {
        include_once $file;
    }
}



new hero_header();
new logos();
new footerPlus();

