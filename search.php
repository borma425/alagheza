<?php


if (  get_query_var('s')  ) {

$search    = strip_tags( (string) wp_unslash( get_query_var('s') ) );

$args = array(
    'post_type'      => array( 'post'),
    's' => $search,
    'posts_per_page' => 30,
    'order' => 'ASC',
    'paged' => $paged,
);

$context = Timber::context();
$context["posts"] = Timber::get_posts( $args  );

$context["count_results"] = count($context["posts"]);
$context["search_word"] = $search ;




Timber::render('search/results.twig', $context);




}else{

echo esc_html("Some Error Here 425 But Not XSS Hahaaa");


}