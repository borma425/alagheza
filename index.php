
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
        $main_title = null;

        // Scrape for 'مميزات' (Pros)
        foreach ($headings as $heading) {
            if (strpos($heading->text(), 'مميزات') !== false) {
                $main_title = $heading->text(); // Get the <h2> content
                $next_element = $heading->nextSibling('ul'); // Get the next <ul>

                if ($next_element) {
                    $pros = $next_element->html(); // Get the <ul> content
                }

                break; // Stop after finding the first 'مميزات' section
            }
        }

        // Scrape for 'عيوب' (Cons)
        foreach ($headings as $heading) {
            if (strpos($heading->text(), 'عيوب') !== false) {
                $next_element = $heading->nextSibling('ul'); // Get the next <ul>

                if ($next_element) {
                    $cons = $next_element->html(); // Get the <ul> content
                }

                break; // Stop after finding the first 'عيوب' section
            }
        }

        // Update the custom meta fields for this post
        if ($main_title) {
            update_post_meta($post_id, 'single_pros_cons_main_title', sanitize_text_field($main_title));
        }

        if ($pros) {
            update_post_meta($post_id, 'single_pros', sanitize_textarea_field($pros));
        }

        if ($cons) {
            update_post_meta($post_id, 'single_cons', sanitize_textarea_field($cons));
        }
    } else {
        // Log or handle the case where post content is empty
        echo "Post content is empty for post ID: $post_id<br>";
    }
}
