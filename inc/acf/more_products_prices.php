<?php

function add_product_meta_box() {
      add_meta_box(
          'product_meta_box',           // Meta box ID
          'خطط الأسعار',            // Meta box title
          'display_product_meta_box',   // Callback function
          'post',                       // Post type
          'normal',                     // Context
          'high'                        // Priority
      );
  }
  add_action('add_meta_boxes', 'add_product_meta_box');



  function display_product_meta_box($post) {
      $product_data = get_post_meta($post->ID, '_product_meta_key', true);
      $product_data = is_array($product_data) ? $product_data : [];
  echo '
  <style>.product-meta-box {
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 8px;
      margin-top: 15px;
      border: 1px solid #ddd;
  }
  
  .product-pair {
      background-color: #fff;
      padding: 15px;
      border-radius: 6px;
      border: 1px solid #ddd;
      margin-bottom: 20px;
      position: relative;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .product-image {
      margin-bottom: 10px;
  }
  
  .image-preview-wrapper {
      margin-bottom: 10px;
      border: 1px solid #ddd;
      padding: 5px;
      border-radius: 4px;
      text-align: center;
  }
  
  .image-preview {
      max-width: 100%;
      height: auto;
      display: block;
      margin: 0 auto;
  }
  
  .product-input,
  .product-textarea,
  .product-image-url {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      margin-bottom: 10px;
  }
  
  .add-product-btn,
  .upload-image-button,
  .remove-product {
      background-color: #007cba;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 10px;
      text-align: center;
  }
  
  .upload-image-button {
      margin-top: 5px;
  }
  
  .remove-product {
      background-color: #dc3545;
      position: absolute;
      top: -25px;
      right: 10px;
      padding: 5px;
  }
  
  .add-product-btn:hover,
  .upload-image-button:hover,
  .remove-product:hover {
      background-color: #005a9c;
  }
  
  .remove-product:hover {
      background-color: #c82333;
  }
  </style>';
      wp_nonce_field(basename(__FILE__), 'product_meta_nonce');
    $prices_main_title = get_post_meta($post->ID, 'prices_main_title', true);

      ?>
      <div class="product-meta-box">

      <div>
<label style="font-size:medium;">العنوان الرئيسي</label>
    <input type="text" name="prices_main_title" value="<?php echo esc_attr( $prices_main_title ); ?>" class="qa-input" placeholder="العنوان الرئيسي"/>

</div><br><br>


          <div id="product-fields-container">
              <?php if (!empty($product_data)) : ?>
                  <?php foreach ($product_data as $index => $product) : ?>
                      <div class="product-pair">
                          <div class="product-image">
                              <label>الصورة للمنتج</label>
                              <div class="image-preview-wrapper">
                                  <img src="<?php echo esc_url($product['image']); ?>" class="image-preview" />
                              </div>
                              <input type="hidden" name="product_images[]" value="<?php echo esc_url($product['image']); ?>" class="product-image-url" />
                              <button type="button" class="upload-image-button">رفع الصورة</button>
                          </div>
                          <div class="product-name">
                              <label>الأسم</label>
                              <input type="text" name="product_names[]" value="<?php echo esc_attr($product['name']); ?>" class="product-input" />
                          </div>
                          <div class="product-desc">
                              <label>الوصف</label>
                              <textarea name="product_descs[]" class="product-textarea"><?php echo esc_textarea($product['desc']); ?></textarea>
                          </div>
                          <div class="product-price">
                              <label>السعر</label>
                              <input type="text"  name="product_prices[]" value="<?php echo esc_attr($product['price']); ?>" class="product-input" />
                          </div>
                          <div class="product-link">
                          <label>التوجه لرابط</label>
                          <input placeholder="إختياري" type="text"  name="product_links[]" value="<?php echo esc_textarea($product['link']); ?>" class="product-input" />
                      </div>
                          <button type="button" class="remove-product">الحذف</button>
                      </div>
                  <?php endforeach; ?>
              <?php else : ?>
                  <div class="product-pair">
                      <div class="product-image">
                          <label>الصورة للمنتج</label>
                          <div class="image-preview-wrapper">
                              <img src="" class="image-preview" style="display: none;" />
                          </div>
                          <input type="hidden" name="product_images[]" class="product-image-url" />
                          <button type="button" class="upload-image-button">رفع الصورة</button>
                      </div>
                      <div class="product-name">
                          <label>الأسم</label>
                          <input type="text" name="product_names[]" class="product-input" />
                      </div>
                      <div class="product-desc">
                          <label>الوصف</label>
                          <textarea name="product_descs[]" class="product-textarea"></textarea>
                      </div>
                      <div class="product-price">
                          <label>السعر</label>
                          <input type="text"  name="product_prices[]" class="product-input" />
                      </div>
                      <div class="product-link">
                          <label>التوجه لرابط</label>
                          <input placeholder="إختياري" type="text"  name="product_links[]" class="product-input" />
                      </div>
                      <button type="button" class="remove-product">الحذف</button>
                  </div>
              <?php endif; ?>
          </div>
  
          <button type="button" id="add-product-button" class="add-product-btn">اضافة منتج أخر</button>
      </div>
  
      <script type="text/javascript">
          jQuery(document).ready(function($) {
              $('#add-product-button').on('click', function() {
                  $('#product-fields-container').append(`
                      <div class="product-pair">
                          <div class="product-image">
                              <label>الصورة</label>
                              <div class="image-preview-wrapper">
                                  <img src="" class="image-preview" style="display: none;" />
                              </div>
                              <input type="hidden" name="product_images[]" class="product-image-url" />
                              <button type="button" class="upload-image-button">رفع الصورة</button>
                          </div>
                          <div class="product-name">
                              <label>الأسم</label>
                              <input type="text" name="product_names[]" class="product-input" />
                          </div>
                          <div class="product-desc">
                              <label>الوصف</label>
                              <textarea name="product_descs[]" class="product-textarea"></textarea>
                          </div>
                          <div class="product-price">
                              <label>السعر</label>
                              <input type="text"  name="product_prices[]" class="product-input" />
                          </div>
                                                <div class="product-link">
                          <label>التوجه لرابط</label>
                          <input placeholder="إختياري" type="text"  name="product_links[]" class="product-input" />
                      </div>
                          <button type="button" class="remove-product">الحذف</button>
                      </div>
                  `);
              });
  
              // Handle image uploading with preview
              $('.product-meta-box').on('click', '.upload-image-button', function(e) {
                  e.preventDefault();
                  var button = $(this);
                  var file_frame = wp.media.frames.file_frame = wp.media({
                      title: 'حدد او ارفع صورة',
                      button: {
                          text: 'اختيار هذة الصورة'
                      },
                      multiple: false
                  });
  
                  file_frame.on('select', function() {
                      var attachment = file_frame.state().get('selection').first().toJSON();
                      button.prev('.product-image-url').val(attachment.url);
                      button.siblings('.image-preview-wrapper').find('.image-preview').attr('src', attachment.url).show();
                  });
  
                  file_frame.open();
              });
  
              $('#product-fields-container').on('click', '.remove-product', function() {
                  $(this).closest('.product-pair').remove();
              });
          });
      </script>
      <?php
  }
  


  function save_product_meta_data($post_id) {
      // Check nonce for security

      // Check if the post is being autosaved
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
          return $post_id;
      }
  
      // Check user permissions
      if (!current_user_can('edit_post', $post_id)) {
          return $post_id;
      }
  

      
      
      if (isset($_POST['prices_main_title'])) {
        update_post_meta($post_id, 'prices_main_title', $_POST['prices_main_title']);
    }



      // Save products
      if (isset($_POST['product_names']) && isset($_POST['product_descs']) && isset($_POST['product_prices']) && isset($_POST['product_images'])) {
          $product_data = [];
          $names = $_POST['product_names'];
          $descs = $_POST['product_descs'];
          $prices = $_POST['product_prices'];
          $links = $_POST['product_links'];
          $images = $_POST['product_images'];
  
          for ($i = 0; $i < count($names); $i++) {
              if (!empty($names[$i]) && !empty($descs[$i]) && !empty($prices[$i]) && !empty($images[$i])) {
                  $product_data[] = [
                      'name'  => sanitize_text_field($names[$i]),
                      'desc'  => sanitize_textarea_field($descs[$i]),
                      'price' => $prices[$i],
                      'link' => $links[$i],
                      'image' => esc_url_raw($images[$i])
                  ];
              }
          }

          update_post_meta($post_id, '_product_meta_key', $product_data);
      } else {
          delete_post_meta($post_id, '_product_meta_key');
      }
  }
  add_action('save_post', 'save_product_meta_data');
