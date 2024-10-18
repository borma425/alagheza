<?php






function create_post_type(  
string $singular = 'Customer', 
string $plural = 'Customers', 
string $menu_icon = 'dashicons-carrot',
bool $hierarchical = FALSE, 
bool $has_archive = TRUE,
string $description = '' ) {

$slug = strtolower( str_replace(' ', '-', $plural));
$supports = array(
    'title', // post title
    'editor', // post content
    'author', // post author
    'thumbnail', // featured images
    'excerpt', // post excerpt
    'custom-fields', // custom fields
    'comments', // post comments
    'revisions', // post revisions
    'post-formats', // post formats
    'page-attributes',
    );
        //Here, the default post type if no argument is passed to create_post_type() will be Customer CPT

        register_post_type( $singular, array(
            'public'            => TRUE,
            'query_var' => TRUE,
            'show_in_rest'      => TRUE,
            'can_export'      => TRUE,
            'description'       => $description,
            'menu_icon'         => $menu_icon,
            'hierarchical'      => $hierarchical,
            'has_archive'       => $has_archive,
            'supports'          => $supports,
            'labels' => array(
                'name'                      => $plural,
                'singular_name'             => $singular,
                'add_new'                   => 'Add New '.$singular,
                'add_new_item'              => 'New '.$singular,
                'edit_item'                 => 'Edit '.$singular,
                'view_item'                 => 'View '.$singular,
                'view_items'                => 'View '.$plural,
                'search_items'              => 'Search '.$plural,
                'not_found'                 => 'No '.$plural.' Found',
                'all_items'                 => 'All '.$plural,
                'parent_item' => null,
                'parent_item_colon' => null,
                'archives'                  => $plural.' Archives',
                'attributes'                => $singular.' Attributes',
                'insert_into_item'          => 'Insert into '.$singular,
                'uploaded_to_this_item'     => 'Uploaded to this '.$singular,
                'featured_image'            => $singular.' Featured image',
                'set_featured_image'        => 'Set '.$singular.' featured image',
                'remove_featured_image'     => 'Remove '.$singular.' featured image',
                'use_featured_image'        => 'Use as '.$singular.' featured image',
                'filter_items_list'         => 'Filter '.$plural.' list',
                'filter_by_date'            => 'Filter '.$plural.' by date',
                'items_list_navigation'     => $plural.' list navigation',
                'items_list'                => $plural.' list',
                'item_published'            => $singular.' published.',
                'item_published_privately'  => $singular.' published privately.',
                'item_reverted_to_draft'    => $singular.' reverted to draft.',
                'item_scheduled'            => $singular.' scheduled.',
                'item_updated'              => $singular.' updated.',
                'item_link'                 => $singular.' link',
               # 'menu_name' => __( 'Tags' ),
               'choose_from_most_used' => __( 'Choose from the most used tags' ),

            ),
            'rewrite'           => array(
                'slug'                      => $slug,
                'with_front' => false,

                ) ,         
            'taxonomies'          => array( "category","post_tag" ),
        ));
        

    flush_rewrite_rules();

    }

    function custom_cpts() {
      /*   create_post_type('article', 'article','dashicons-admin-post'); */
      create_post_type('brands', 'brands','dashicons-groups');
      create_post_type('sections', 'sections','dashicons-groups');


    }

add_action('init', 'custom_cpts');




