<?php
// Add the meta box
function add_custom_meta_box() {


      add_meta_box(
          'custom_meta_box', // ID
          'ملخص المراجعة', // Title
          'render_custom_meta_box', // Callback function
          'post', // Post type
          'normal', // Context
          'high' // Priority
      );
  
      add_meta_box(
          'ratings_meta_box', // ID
          'تقييمات ملخص المراجعة', // Title
          'render_ratings_meta_box', // Callback function
          'post', // Post type
          'normal', // Context
          'default' // Priority
      );
  

  
  }
  
  
  
  add_action('add_meta_boxes', 'add_custom_meta_box');
  
  
  
  
  
  function render_ratings_meta_box($post) {
      wp_nonce_field(basename(__FILE__), 'custom_meta_box_nonce');
  
  
      $wp_review_total  = get_post_meta( $post->ID, 'wp_review_total', true );
  
      function wp_review_get_review_items( $post_id = null ) {
          if ( ! $post_id ) {
              $post_id = get_the_ID();
          }
      
          $items = get_post_meta( $post_id, 'wp_review_item', true );
      
          return apply_filters( 'wp_review_items', $items );
      }
      
          $items = wp_review_get_review_items( $post->ID );
      
          $transformed_array = [];
    if (is_array($items)) {

          // Loop through the original array to build the new format
          foreach ($items as $item) {
              // Generate a unique key based on the current timestamp (you can use another unique identifier if needed)
              $unique_key = (string) time() . uniqid(); // or any other method to generate unique keys
          
              // Assign the title as key and star rating as value
              $transformed_array[$unique_key] = [
                  'key' => $item['wp_review_item_title'],
                  'value' => $item['wp_review_item_star'],
              ];
          }
    }
      
          
          $index =0;
      
          // Multi Ratings Fields
          echo '<h3>التقييمات</h3>';
          echo '<div id="multi-ratings-container">';
          foreach ($transformed_array as $key => $rating) {
              echo '<div class="rating-item">';
              echo '<input type="text" id="wpr-review-item-title-' .++$index.'" name="wp_review_item_title[]" value="' . esc_attr($rating['key']) . '" placeholder="اسم التقييم" />';
              echo '<input type="number" id="wpr-review-item-star-' .++$index.'"  name="wp_review_item_star[]" value="' . esc_attr($rating['value']) . '" placeholder="(0-5)" />';
              echo '<button type="button" class="remove-rating button">الحذف</button>';
              echo '</div>';
          }
          echo '</div>';
          echo '<button type="button" id="add-rating" class="button">اضافة تقييم</button>';
      
          // Overall Rating
          echo '<h3>التقييم العام</h3>';
          echo '<input class="tinyinput" type="text" name="wp_review_total" value="' . esc_attr($wp_review_total) . '" /><br />';
      
      
  
  }
  
  
  // Render the meta box
  function render_custom_meta_box($post) {
      // Nonce field for security
      wp_nonce_field(basename(__FILE__), 'custom_meta_box_nonce');
  
      echo '    <style>
      #custom_meta_box {
          background-color: #f9f9f9;
          padding: 20px;
          border: 1px solid #ddd;
          border-radius: 5px;
      }
      #custom_meta_box h3 {
          color: #b33939;
          font-weight: bold;
          font-size: 18px;
      }
      #custom_meta_box label {
          font-weight: bold;
          display: block;
          margin-bottom: 8px;
      }
      #custom_meta_box input[type="text"],
      #custom_meta_box textarea {
          width: 100%;
          padding: 10px;
          border: 1px solid #ccc;
          border-radius: 5px;
          margin-bottom: 20px;
      }
      #upload_image_button {
          background-color: #ff6b6b;
          color: #fff;
          border: none;
          padding: 8px 12px;
          cursor: pointer;
          border-radius: 5px;
      }
      .tinyinput{
  
        height: 31px !important;
      }
  
  
  
  
  
  
  
  
  
  
  </style>';
  
  
  
  $pros       = get_post_meta($post->ID, 'wp_review_pros', true);
  $cons       = get_post_meta($post->ID, 'wp_review_cons', true);
  $post_id    = get_the_ID();
  
  // For top container title
  $desc_title = get_post_meta($post_id, 'wp_review_desc_title', true);
  if (!$desc_title) {
      $desc_title = __('الملخص', 'wp-review');
  }
  
  $heading = get_post_meta($post_id, 'wp_review_heading', true);
  
  // Retrieve schema options
  $meta_key   = 'wp_review_schema_options';
  $meta_value = get_post_meta($post_id, $meta_key, true);
  
  // Ensure $meta_value is an array
  if (!is_array($meta_value)) {
      // Attempt to decode if it's JSON
      $decoded = json_decode($meta_value, true);
      if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
          $meta_value = $decoded;
      } else {
          // Handle the error appropriately
          $meta_value = [];
          error_log("Meta key '{$meta_key}' for post ID {$post_id} is not an array or valid JSON.");
      }
  }
  
  // Access product details safely
  if (isset($meta_value['Product']) && is_array($meta_value['Product'])) {
      $product_name            = isset($meta_value['Product']['name']) ? $meta_value['Product']['name'] : '';
      $product_desc            = isset($meta_value['Product']['description']) ? $meta_value['Product']['description'] : '';
      $product_brand           = isset($meta_value['Product']['brand']) ? $meta_value['Product']['brand'] : '';
      $product_sku             = isset($meta_value['Product']['sku']) ? $meta_value['Product']['sku'] : '';
      $product_price           = isset($meta_value['Product']['price']) ? $meta_value['Product']['price'] : '';
      $product_priceCurrency   = isset($meta_value['Product']['priceCurrency']) ? $meta_value['Product']['priceCurrency'] : '';
  } else {
      // Set default values or handle the absence of 'Product' key
      $product_name            = '';
      $product_desc            = '';
      $product_brand           = '';
      $product_sku             = '';
      $product_price           = '';
      $product_priceCurrency   = '';
      error_log("Key 'Product' does not exist in meta key '{$meta_key}' for post ID {$post_id}.");
  }
  
  // Continue with the rest of your code
  $wp_review_desc_title = get_post_meta($post_id, 'wp_review_desc_title', true);
  $wp_review_desc_desc  = get_post_meta($post->ID, 'wp_review_desc', true);
  
  
      // Product Information
      echo '<h3>تفاصيل المراجعة</h3>';
      echo '<label> العنوان الرئيسي</label><input class="tinyinput" type="text" name="wp_review_heading" value="' . esc_attr($heading) . '" /><br />';
  

      echo '<label>اسم المنتج:</label><input class="tinyinput" type="text" name="wp_review_schema_options[Product][name]" value="' . esc_attr($product_name) . '" /><br />';
      echo '<label>وصف المنتج:</label><textarea name="wp_review_schema_options[Product][description]">' . esc_textarea($product_desc) . '</textarea><br />';
      echo '<label>الماركة:</label><input class="tinyinput" type="text" name="wp_review_schema_options[Product][brand]" value="' . esc_attr($product_brand) . '" /><br />';
      echo '<label>كود المنتج:</label><input class="tinyinput" type="text" name="wp_review_schema_options[Product][sku]" value="' . esc_attr($product_sku) . '" /><br />';
      echo '<label>السعر:</label><input class="tinyinput" type="text" name="wp_review_schema_options[Product][price]" value="' . esc_attr($product_price) . '" /><br />';
      echo '<label>العملة:</label><input class="tinyinput" type="text" name="wp_review_schema_options[Product][priceCurrency]" value="' . esc_attr($product_priceCurrency) . '" /><br />';
  
  
  
  // Ensure the structure is correct
  if (is_array($meta_value) && isset($meta_value['Product']['image'])) {
        $image_data = $meta_value['Product']['image'];
        $image_id = isset($image_data['id']) ? $image_data['id'] : '';
        $image_url = isset($image_data['url']) ? $image_data['url'] : '';
    
        // Check if image URL exists and output it
        if (!empty($image_url)) {
              $reviewed_item_image = $image_url; // Get existing image URL
  
        } else {
        $reviewed_item_image = "No_imagerM"; // Get existing image URL
        $reviewed_item_image = get_post_meta($post_id, 'wp_review_hello_bar_bg_image_url', true);

        }
    } else {
        echo 'Product image data is not set correctly.';
        $reviewed_item_image = "No_imagerTT"; // Get existing image URL
    }
  
      // Reviewed Item Image
      echo '<h3>الصورة </h3>';
      echo '<input type="hidden" name="wp_review_schema_options[Product][image][url]" id="reviewed_item_image" value="' . esc_attr($reviewed_item_image) . '" />';
      echo '<img id="reviewed_item_image_preview" src="' . esc_url($reviewed_item_image) . '" style="max-width: 100%; height: auto; margin-bottom: 10px;" />';
      echo '<button type="button" class="button" id="upload_image_button">رفع صورة</button>';
  
  
      // Summary Title
      echo '<h3>العنوان الثانوي</h3>';
      echo '<input class="tinyinput" type="text" name="wp_review_desc_title" value="' . esc_attr($wp_review_desc_title) . '" style="width: 100%;" placeholder="ملخص العنوان" />';
  
      // Summary Description
      echo '<h3>الوصف الثانوي </h3>';
      echo '<textarea name="wp_review_desc" style="width: 100%; height: 100px;" placeholder="ملخص الوصف">' . esc_textarea(wp_strip_all_tags($wp_review_desc_desc)) . '</textarea>';
      ?>
  
      <?php
  
  
  
  
      // Unified Flexbox Container for Pros and Cons
      echo '<h3> إيجابيات وسلبيات    </h3>';
      echo '<div id="pros-cons-container" style="display: flex; gap: 20px;">';
  
      // Pros Field
      echo '<div style="flex: 1;">'; // Flex column for Pros
      echo '<h4>إيجابيات</h4>';
      wp_editor(
        $pros,
        'wp_review_pros',
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
  
      echo '<input type="hidden" name="pros" id="pros" value="' . esc_attr($pros) . '" />';
      echo '</div>'; // End of Pros column
  
      // Cons Field
      echo '<div style="flex: 1;">'; // Flex column for Cons
      echo '<h4>سلبيات</h4>';
      wp_editor(
        $cons,
        'wp_review_cons',
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
      echo '<input type="hidden" name="cons" id="cons" value="' . esc_attr($cons) . '" />';
      echo '</div>'; // End of Cons column
  
      echo '</div>'; // End of unified container
  
      ?>

      <?php
  }
  
  
  // Enqueue admin scripts
  function enqueue_admin_scripts() {
      // Ensure jQuery is loaded
      wp_enqueue_script('jquery');
  
      // Include media uploader scripts
      wp_enqueue_media();
  
      // Your custom script
      wp_add_inline_script('jquery', '
          jQuery(document).ready(function($) {


              // Add Multi Rating
              var newRatingIndex = 0;
              $("#add-rating").click(function() {
                  newRatingIndex++; // Increment the index
                  $("#multi-ratings-container").append("<div class=\'rating-item\'><input type=\'text\' id=\'wpr-review-item-title-" + newRatingIndex + "\' name=\'wp_review_item_title[]\' placeholder=\'اسم التقييم\' /><input type=\'number\' id=\'wpr-review-item-star-" + newRatingIndex + "\' name=\'wp_review_item_star[]\' placeholder=\'(0-5)\' /><button type=\'button\' class=\'remove-rating button\'>الحذف</button></div>");
              });
  
              // Remove Rating
              $(document).on("click", ".remove-rating", function() {
                  $(this).closest(".rating-item").remove();
              });
  
              // Upload image
              var imageUploader;
              $("#upload_image_button").click(function(e) {
                  e.preventDefault();
                  if (imageUploader) {
                      imageUploader.open();
                      return;
                  }
                  imageUploader = wp.media({
                      title: "اختيار صورة",
                      button: {
                          text: "اختر هذة الصورة"
                      },
                      multiple: false // Set to true to allow multiple images to be selected
                  });
  
                  imageUploader.on("select", function() {
                      var attachment = imageUploader.state().get("selection").first().toJSON();
                      $("#reviewed_item_image").val(attachment.url);
                      $("#reviewed_item_image_preview").attr("src", attachment.url).show();
                  });
  
                  imageUploader.open();
              });
          });
      ');
  }
  add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
  
  
  
  
  // Save the meta box data
  function save_custom_meta_box_data($post_id) {


      // Check for autosave
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
          return;
      }
      // Check user permissions
      if (!current_user_can('edit_post', $post_id)) {
          return;
      }
  
  
      // Sanitize and save product information
      if (isset($_POST['wp_review_schema_options'])) {

        
          update_post_meta($post_id, 'wp_review_schema_options', $_POST['wp_review_schema_options']);

          $meta_key   = 'wp_review_schema_options';
          $meta_value = get_post_meta($post_id, $meta_key, true);
          
          // Ensure $meta_value is an array
          if (!is_array($meta_value)) {
              // Attempt to decode if it's JSON
              $decoded = json_decode($meta_value, true);
              if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                  $meta_value = $decoded;
              } 


          }

          update_post_meta($post_id, 'wp_review_product_price',$meta_value['Product']['price']);
          



          
    
    
        }
  



  
      // Save ratings
      // Save multi ratings
        $old             = get_post_meta( $post_id, 'wp_review_item', true );
  
        if ( ! empty( $_POST['wp_review_item_title'] ) ) {
              $title          = $_POST['wp_review_item_title'];
              $new   = array();
              $count = count( $title );
              $ids            = $_POST['wp_review_item_id'];
              $star           = $_POST['wp_review_item_star'];
  
              for ( $i = 0; $i < $count; $i++ ) {
                    /* if ( empty( $star[ $i ] ) ) {
                          continue; // Prevent item without score.
                    } */
  
                    $new[ $i ] = array();
  
                    $new[ $i ]['wp_review_item_star'] = floatval( $star[ $i ] );
  
                    if ( ! empty( $ids[ $i ] ) ) {
                          $new[ $i ]['id'] = sanitize_text_field( wp_unslash( $ids[ $i ] ) );
                    }
  
                    if ( ! empty( $title[ $i ] ) ) {
                          $new[ $i ]['wp_review_item_title'] = sanitize_text_field( wp_unslash( $title[ $i ] ) );
                    }
  
  
              }
  
              if ( ! empty( $new ) && $new != $old ) {
                    update_post_meta( $post_id, 'wp_review_item', $new );
              } elseif ( empty( $new ) && $old ) {
                    delete_post_meta( $post_id, 'wp_review_item', $old );
              }
        } 
  
  
  
  
  
  
  
      // Save overall rating
      if (isset($_POST['wp_review_total'])) {
          // Use wp_kses_post to allow HTML content including line breaks
          update_post_meta($post_id, 'wp_review_total', wp_kses_post($_POST['wp_review_total']));
      }
      
  
      if (isset($_POST['wp_review_pros'])) {
          // Use wp_kses_post to allow HTML content including line breaks
          update_post_meta($post_id, 'wp_review_pros', wp_kses_post($_POST['wp_review_pros']));
      }
  
      // Save cons
      if (isset($_POST['wp_review_cons'])) {
          // Use wp_kses_post to allow HTML content including line breaks
          update_post_meta($post_id, 'wp_review_cons', wp_kses_post($_POST['wp_review_cons']));
      }
  
  
      if (isset($_POST['wp_review_desc_title'])) {
          // Use wp_kses_post to allow HTML content including line breaks
          update_post_meta($post_id, 'wp_review_desc_title', wp_kses_post($_POST['wp_review_desc_title']));
      }
      if (isset($_POST['wp_review_heading'])) {
          // Use wp_kses_post to allow HTML content including line breaks
          update_post_meta($post_id, 'wp_review_heading', wp_kses_post($_POST['wp_review_heading']));
      }
      if (isset($_POST['wp_review_desc'])) {
          // Use wp_kses_post to allow HTML content including line breaks
          update_post_meta($post_id, 'wp_review_desc', wp_kses_post($_POST['wp_review_desc']));
      }
  
      
      
      
  
  
  
  
  }
  add_action('save_post', 'save_custom_meta_box_data');
  