<?php


if (  get_query_var('s')  ) {


$search    = strip_tags( (string) wp_unslash( get_query_var('s') ) );

$args = array(
    'post_type'      => array( 'roms','emulators'),
    's' => $search,
    'posts_per_page' => 10,
    'order' => 'ASC',
    'paged' => $paged,
);






if ( isset($_GET['k']) && isset($_GET['v'])  ) {

    // Get the current URL parameter tag Name
    $key = $_GET['k'];
    $key    = strip_tags( (string) wp_unslash( $key ) );
    $value = $_GET['v'];
    $value    = strip_tags( (string) wp_unslash( $value ) );


    $array_acf = array(
        'meta_query' => array(
            array(
                'key'     => $key,  // Meta key to filter by
                'value'   => $value,       // Value to match
                'compare' => 'LIKE',           // Comparison operator
            ),
        ),
    );

$args = array(
        's' => "",
    'post_type'      => array( 'roms','emulators'),
        'posts_per_page' => 10,
        'order' => 'ASC',
$array_acf
    );


    $args = array_merge( $args, $array_acf );



    }











$context = Timber::context();
$context["posts"] = Timber::get_posts( $args  );

$context["count_results"] = count($context["posts"]);
$context["search_word"] = $search ;




Timber::render('search/results.twig', $context);




}else{

echo esc_html("Some Error Here 425 But Not XSS Hahaaa");


}