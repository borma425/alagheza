<?php

global $paged;

if (!isset($paged) || !$paged){
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




add_filter( 'timber/context', function( $context ) {

  global $paged , $post;

    $context['header_menu_1']            =  Timber::get_menu('header_menu_1');
    $context['footer_menu']              =  Timber::get_menu('footer_menu_1');


  $CPT        = CPT_Redirect_link();

  $context['current_cpt_link']   =  $CPT["link"];
  $context['current_cpt_type']   =  $CPT["type"];


    $context['current_url'] = Timber\URLHelper::get_current_url();











    return $context;

} );







# extract full path of IMAGE dir
function asset_url( $filename ,  $path="/images/" ){

  $Path_url =  get_template_directory_uri() . '/assets/' . $path ;

  if(is_array($filename) && count($filename) > 1){

  $IMGarray = [];

  foreach( $filename as $item){
  array_push( $IMGarray, esc_url( $Path_url . $item ) );
  }

  $result   = $IMGarray;

  } else{

  $result   = esc_url( $Path_url . $filename ) ;

  }

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
          case "emulators":
          $path = "emulators/";
            break;
          case "help_center":
          $path = "help_center/";
            break;
          case "requires_rom":
          $path = "requires-rom/";
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


