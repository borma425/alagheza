<?php


// Add Pros and Cons Meta Box
function pros_cons_meta_box() {
      add_meta_box(
          'pros_cons_meta_box', // ID
          'جداول المميزات والعيوب',      // Title
          'display_pros_cons_meta_box',  // Callback function
          'post',               // Post type
          'normal',             // Context
          'high'                // Priority
      );
  }
  add_action('add_meta_boxes', 'pros_cons_meta_box');
  
  // Display the Pros and Cons Meta Box
  function display_pros_cons_meta_box($post) {
      // Retrieve current meta data for 'Pros' and 'Cons'
      $pros = get_post_meta($post->ID, 'single_pros', true);
      $cons = get_post_meta($post->ID, 'single_cons', true);
  
      // Nonce field for security
      wp_nonce_field(basename(__FILE__), 'pros_cons_nonce');
      $single_pros_main_title = get_post_meta($post->ID, 'single_pros_main_title', true);
      $single_cons_main_title = get_post_meta($post->ID, 'single_cons_main_title', true);
  

 

      ?>
<div>
<label style="font-size:medium;">العنوان الرئيسي</label>
    <input type="text" name="single_pros_main_title" value="<?php echo esc_attr( $single_pros_main_title ); ?>" class="qa-input" placeholder="العنوان الرئيسي"/>

</div><br><br>

<?php
      echo '     ';
      // Display the WP Editor for Pros
      echo '<label for="single_pros">المميزات</label>';
      wp_editor(
          $pros,
          'single_pros_editor',
          array(
              'tinymce'       => array(
                  'toolbar1' => 'bold,italic,underline,bullist,numlist,separator,separator,link,unlink,undo,redo,removeformat',
                  'toolbar2' => '',
                  'toolbar3' => '',
              ),
              'quicktags'     => true,
              'media_buttons' => false,
              'textarea_rows' => 6,
          )
      );
  ?>

<div>
<label style="font-size:medium;">العنوان الرئيسي</label>
    <input type="text" name="single_cons_main_title" value="<?php echo esc_attr( $single_cons_main_title ); ?>" class="qa-input" placeholder="العنوان الرئيسي"/>

</div><br><br>

<?php
      // Display the WP Editor for Cons
      echo '<label for="single_cons" style="margin-top:20px; display:block;">العيوب</label>';
      wp_editor(
          $cons,
          'single_cons_editor',
          array(
              'tinymce'       => array(
                  'toolbar1' => 'bold,italic,underline,bullist,numlist,separator,separator,link,unlink,undo,redo,removeformat',
                  'toolbar2' => '',
                  'toolbar3' => '',
              ),
              'quicktags'     => true,
              'media_buttons' => false,
              'textarea_rows' => 6,
          )
      );
  }
  
  // Save the Pros and Cons Meta Box Data
  function save_pros_cons_meta_box_data($post_id) {

      // Check if the user has permission to edit the post
      if (!current_user_can('edit_post', $post_id)) {
          return;
      }


      if (isset($_POST['single_pros_main_title'])) {
        update_post_meta($post_id, 'single_pros_main_title', $_POST['single_pros_main_title']);
    }

    if (isset($_POST['single_cons_main_title'])) {
        update_post_meta($post_id, 'single_cons_main_title', $_POST['single_cons_main_title']);
    }


    
    
// Save 'Pros'
if (isset($_POST['single_pros_editor'])) {
    $pros_content = wp_kses_post($_POST['single_pros_editor']);

    // Check if the content does not already contain <li>
    if (stripos($pros_content, '<li>') === false) {
        $pros_lines = explode(PHP_EOL, $pros_content);
        $pros_list = '<ul><li>' . implode('</li><li>', array_filter(array_map('trim', $pros_lines))) . '</li></ul>';
    } else {
        $pros_list = $pros_content; // Keep the original content if it has <li>
    }

    $pros_list = preg_replace_callback(
        '/<ul([^>]*?)>/', // This pattern matches the opening <ul> tag with any attributes
        function ($matches) {
            // Check if the class 'feature-list pros' is already in the <ul> tag
            if (strpos($matches[1], 'feature-list pros') === false) {
                // Add the 'feature-list pros' class if it's not already present
                return '<ul class="feature-list pros" ' . $matches[1] . '>';
            }
            // If class is already present, return the tag as is
            return '<ul ' . $matches[1] . '>';
        },
        $pros_list
    );
    
    // Now add the 'feature-item' class to each <li> tag
    $pros_list = preg_replace('/<li([^>]*?)>/', '<li class="feature-item" $1>', $pros_list);

    update_post_meta($post_id, 'single_pros', $pros_list);
}

// Save 'Cons'
if (isset($_POST['single_cons_editor'])) {
    $cons_content = wp_kses_post($_POST['single_cons_editor']);

    // Check if the content does not already contain <li>
    if (stripos($cons_content, '<li>') === false) {
        $cons_lines = explode(PHP_EOL, $cons_content);
        $cons_list = '<ul><li>' . implode('</li><li>', array_filter(array_map('trim', $cons_lines))) . '</li></ul>';
    } else {
        $cons_list = $cons_content; // Keep the original content if it has <li>
    }

    $cons_list = preg_replace_callback(
        '/<ul([^>]*?)>/', // This pattern matches the opening <ul> tag with any attributes
        function ($matches) {
            // Check if the class 'feature-list cons' is already in the <ul> tag
            if (strpos($matches[1], 'feature-list cons') === false) {
                // Add the 'feature-list cons' class if it's not already present
                return '<ul class="feature-list cons" ' . $matches[1] . '>';
            }
            // If class is already present, return the tag as is
            return '<ul ' . $matches[1] . '>';
        },
        $cons_list
    );
    
    // Now add the 'feature-item' class to each <li> tag
    $cons_list = preg_replace('/<li([^>]*?)>/', '<li class="feature-item" $1>', $cons_list);
    
    update_post_meta($post_id, 'single_cons', $cons_list);
}

  }
  add_action('save_post', 'save_pros_cons_meta_box_data');
  