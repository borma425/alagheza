<?php

global $paged;

if (!isset($paged) || !$paged) {
    $paged = 1;
}


function setup_theme(){

  register_nav_menu('header_menu_1','Primary Header Menu');
  register_nav_menu('footer_menu_1','Primary Footer Menu');

add_theme_support( 'post-thumbnails' );

}




add_action('after_setup_theme','setup_theme');

add_filter( 'show_admin_bar', '__return_false' );
add_filter( 'rest_allow_anonymous_comments', '__return_true' );



/* function post_remove ()      //creating functions post_remove for removing menu item
{
remove_menu_page('edit.php');
}

add_action('admin_menu', 'post_remove');   //adding action for triggering function call

 */





 function block_comments_with_links( $commentdata ) {
  // Check if the comment contains a URL (HTTP or HTTPS)
  if ( preg_match( '/https?:\/\/[^\s]+/i', $commentdata['comment_content'] ) ) {
      // Escape the error message to prevent XSS
      $error_message = __( 'عذرا، التعليقات التي تحتوي على روابط غير مسموح بها.', 'alagheza Theme' );

      // Custom HTML for the error message, avoiding inline styles for security
      $styled_message = sprintf(
          '<div class="comment-error-message">%s</div>',
          esc_html( $error_message )
      );
      
      // Enqueue custom CSS (optional but modern practice)
      add_action('wp_footer', function() {
          echo '<style>
              .comment-error-message {
                  background-color: #f8d7da;
                  color: #721c24;
                  border: 1px solid #f5c6cb;
                  padding: 15px;
                  margin: 20px 0;
                  border-radius: 5px;
                  font-family: Arial, sans-serif;
                  text-align: center;
              }
          </style>';
      });

      // Display the styled message and stop comment submission
      wp_die( $styled_message );
  }

  return $commentdata;
}
add_filter( 'preprocess_comment', 'block_comments_with_links' );
add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
  if (is_paged() && is_single()) {
      return false; // Prevent redirect on paginated single pages.
  }
  return $redirect_url;
}, 10, 2);





add_filter( 'timber/context', function( $context ) {

  global $paged , $post;

    $context['header_menu_1']            =  Timber::get_menu('header_menu_1');
    $context['footer_menu']              =  Timber::get_menu('footer_menu_1');


  $CPT        = CPT_Redirect_link();

  $context['current_cpt_link']   =  $CPT["link"];
  $context['current_cpt_type']   =  $CPT["type"];


    $context['current_url'] = Timber\URLHelper::get_current_url();




    $adsense_data = get_option('adsense_data', []);

    // Pass the AdSense data to the Twig context
    $context['ads'] = $adsense_data;
    $context['custom_code_head']   = get_option('custom_code_textarea');
    $context['custom_code_footer'] = get_option('custom_code_footer_textarea');






    return $context;

} );







# extract full path of IMAGE dir
function asset_url( $filename ,  $path="/images/" ){

  $Path_url =  get_template_directory_uri() . '/assets' . $path ;

  $result   = esc_url( $Path_url . $filename ) ;

  return   $result;

};




/* Get Link Of current Custom Post Time  */

function CPT_Redirect_link($cpt_slug=""){


  $cpt_slug         = ( !empty($cpt_slug) ) ? $cpt_slug : get_post_type( get_queried_object_id() );
  $cpt_slug         = $cpt_slug ?? "";
  
  
      switch ($cpt_slug) {
  
  
          case "sections":
          $path = "sections/";
            break;
          case "brands":
          $path = "brands/";
            break;
          case "advices":
          $path = "/category/home-electronic-devices-advices/";
            break;
          case "ttttttttttt":
          $path = "tttttttttttt/";
            break;
          case "article":
          $path = "article/";
            break;
            default:
            $path = "/";
  
      }
  
  $link = esc_url ( home_url('/' . ($path)) );
  
  $CPT_info        = json_decode('[]', TRUE);
  $CPT_info[]      = [
  "link"=> $link,
  "status"=> ( $path == "/" ) ? false : true,
  "type"=> $cpt_slug,
  
  ];
  
  return current($CPT_info);
  
  
  }







# get current tag of tags option list with index number

function get_tag_name_with_index($i){

  $tag_choices = array();
  $tags = get_tags();
  foreach ($tags as $index => $tag) {
      $tag_choices[$tag->slug] = urldecode($tag->name);
  }


return esc_html__( $tag_choices[$i] );

}












  function get_sorted_posts_by_taxonomy($taxonomy = 'post_tag', $post_type = 'sections', $per_page = 6) {
    // Ensure that the taxonomy is either 'post_tag' or 'category'
    if (!in_array($taxonomy, ['post_tag', 'category'])) {
        return; // Invalid taxonomy
    }

    // Step 1: Get the most-used terms (tags or categories) in the 'post' post type
    $top_terms = get_terms([
        'taxonomy'   => $taxonomy,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'fields'     => 'ids',
        'hide_empty' => true
    ]);

    if (!empty($top_terms) && !is_wp_error($top_terms)) {
        // Step 2: Query all posts from the custom post type
        $sections_query = new WP_Query([
            'post_type'      => $post_type,
            'posts_per_page' => -1
        ]);

        if ($sections_query->have_posts()) {
            // Step 3: Sort posts by term popularity
            $sorted_posts = [];

            while ($sections_query->have_posts()) {
                $sections_query->the_post();

                // Get the terms (tags or categories) for each post
                $post_terms = wp_get_post_terms(get_the_ID(), $taxonomy, ['fields' => 'ids']);

                // Calculate a score for each post based on term usage in 'post'
                $score = 0;
                foreach ($post_terms as $term_id) {
                    $term_index = array_search($term_id, $top_terms);
                    if ($term_index !== false) {
                        // Higher score for terms higher in popularity (top_terms is ordered)
                        $score += (count($top_terms) - $term_index);
                    }
                }

                // Store the post and its score
                $sorted_posts[] = [
                    'post'  => get_post(),
                    'score' => $score
                ];
            }

            // Sort posts by score in descending order
            usort($sorted_posts, function($a, $b) {
                return $b['score'] - $a['score'];
            });

            // Limit to 6 posts and convert to Timber\Post objects
            $timber_posts = array_slice(array_map(function($item) {
                return Timber::get_post($item['post']->ID);
            }, $sorted_posts), 0, $per_page)
            
            ;

            
/*             // Pass the sorted posts to Timber context
            $context[$context_name] = $timber_posts;
 */
            // Restore original post data
            wp_reset_postdata();
        } else {
           # $context[$context_name] = [];
        }
    } else {
       # $context[$context_name] = [];
    }

    // Return the context for rendering
    return $timber_posts;
}












