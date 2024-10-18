
<?php

$context = Timber::context();

$context['is_front_page'] = true;

$args = array(
    'category_name' => 'home-electronic-devices-advices', // Slug of the category
    'posts_per_page' => 5, // Adjust the number of posts as needed
);

$context['advice_posts'] = Timber::get_posts($args);



#Timber::render('index-home.twig', $context);

require 'vendor/autoload.php'; // Load DiDom

use DiDom\Document;

// Get all published posts
$args = array(
    'post_type' => 'post', // or your custom post type if needed
    'post_status' => 'publish',
    'posts_per_page' => -1, // Get all posts
);
$posts = get_posts($args);

foreach ($posts as $post) {
    $post_id = $post->ID;
    $post_title = $post->post_title;

    // Apply content filters like shortcodes
    $post_content = apply_filters('the_content', $post->post_content);

    // Check if post content is not empty before processing
    if (!empty($post_content)) {
        // Load the content into DiDom Document
        $document = new Document($post_content);

        // Find all <h2> elements
        $headings = $document->find('h2');

        $pros = null;
        $cons = null;
        $pros_main_h2 = null;
        $cons_main_h2 = null;

        // Scrape for 'مميزات' (Pros)
        foreach ($headings as $heading) {
            if (strpos($heading->text(), 'مميزات') !== false) {
                $pros_main_h2 = $heading->text(); // Get the <h2> content for Pros
                $next_element = $heading->nextSibling('ul'); // Get the next <ul>

                if ($next_element) {
                    $pros = $next_element->html(); // Get the <ul> content for Pros
                }

                break; // Stop after finding the first 'مميزات' section
            }
        }

        // Scrape for 'عيوب' (Cons)
        foreach ($headings as $heading) {
            if (strpos($heading->text(), 'عيوب') !== false) {
                $cons_main_h2 = $heading->text(); // Get the <h2> content for Cons
                $next_element = $heading->nextSibling('ul'); // Get the next <ul>

                if ($next_element) {
                    $cons = $next_element->html(); // Get the <ul> content for Cons
                }

                break; // Stop after finding the first 'عيوب' section
            }
        }

        // Update the custom meta fields for this post
        if ($pros_main_h2) {
            update_post_meta($post_id, 'single_pros_main_title', $pros_main_h2);
        }

        if ($pros) {
            update_post_meta($post_id, 'single_pros', $pros);
        }

        if ($cons_main_h2) {
            update_post_meta($post_id, 'single_cons_main_title', $cons_main_h2);
        }

        if ($cons) {
            update_post_meta($post_id, 'single_cons', $cons);
        }
    } else {
        // Log or handle the case where post content is empty
        echo "Post content is empty for post ID: $post_title<br>";
    }
}
