<?php

function add_new_product_meta_box() {
    add_meta_box(
        'new_product_meta_box',               // Meta box ID
        'خطط الأسعار الجديدة',               // Meta box title
        'display_new_product_meta_box',       // Callback function
        'post',                                // Post type
        'normal',                              // Context
        'high'                                 // Priority
    );
}
add_action('add_meta_boxes', 'add_new_product_meta_box');

function display_new_product_meta_box($post) {
    $new_pricses_2_data = get_post_meta($post->ID, '_new_pricses_2_meta_key', true);
    $new_pricses_2_data = is_array($new_pricses_2_data) ? $new_pricses_2_data : [];
    echo '
    <style>
    .new-pricses-2-meta-box {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-top: 15px;
        border: 1px solid #ddd;
    }
    
    .new-pricses-2-pair {
        background-color: #fff;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .new-pricses-2-image {
        margin-bottom: 10px;
    }
    
    .new-image-preview-wrapper {
        margin-bottom: 10px;
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 4px;
        text-align: center;
    }
    
    .new-image-preview {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    
    .new-pricses-2-input,
    .new-pricses-2-textarea,
    .new-pricses-2-image-url {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
    }
    
    .add-new-pricses-2-btn,
    .upload-new-image-button,
    .remove-new-pricses-2 {
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
    
    .upload-new-image-button {
        margin-top: 5px;
    }
    
    .remove-new-pricses-2 {
        background-color: #dc3545;
        position: absolute;
        top: -25px;
        right: 10px;
        padding: 5px;
    }
    
    .add-new-pricses-2-btn:hover,
    .upload-new-image-button:hover,
    .remove-new-pricses-2:hover {
        background-color: #005a9c;
    }
    
    .remove-new-pricses-2:hover {
        background-color: #c82333;
    }
    </style>';
    
    wp_nonce_field(basename(__FILE__), 'new_pricses_2_meta_nonce');
    $new_pricses_2_main_title = get_post_meta($post->ID, 'new_pricses_2_main_title', true);
    ?>
    <div class="new-pricses-2-meta-box">
        <div>
            <label style="font-size:medium;">العنوان الرئيسي الجديد</label>
            <input type="text" name="new_pricses_2_main_title" value="<?php echo esc_attr($new_pricses_2_main_title); ?>" class="new-pricses-2-input" placeholder="العنوان الرئيسي"/>
        </div><br><br>

        <div id="new-pricses-2-fields-container">
            <?php if (!empty($new_pricses_2_data)) : ?>
                <?php foreach ($new_pricses_2_data as $index => $new_pricses_2) : ?>
                    <div class="new-pricses-2-pair">
                        <div class="new-pricses-2-image">
                            <label>الصورة للمنتج الجديد</label>
                            <div class="new-image-preview-wrapper">
                                <img src="<?php echo esc_url($new_pricses_2['image']); ?>" class="new-image-preview" />
                            </div>
                            <input type="hidden" name="new_pricses_2_images[]" value="<?php echo esc_url($new_pricses_2['image']); ?>" class="new-pricses-2-image-url" />
                            <button type="button" class="upload-new-image-button">رفع الصورة</button>
                        </div>
                        <div class="new-pricses-2-name">
                            <label>الأسم الجديد</label>
                            <input type="text" name="new_pricses_2_names[]" value="<?php echo esc_attr($new_pricses_2['name']); ?>" class="new-pricses-2-input" />
                        </div>
                        <div class="new-pricses-2-desc">
                            <label>الوصف الجديد</label>
                            <textarea name="new_pricses_2_descs[]" class="new-pricses-2-textarea"><?php echo esc_textarea($new_pricses_2['desc']); ?></textarea>
                        </div>
                        <div class="new-pricses-2-price">
                            <label>السعر الجديد</label>
                            <input type="text" name="new_pricses_2_prices[]" value="<?php echo esc_attr($new_pricses_2['price']); ?>" class="new-pricses-2-input" />
                        </div>
                        <div class="new-pricses-2-link">
                            <label>التوجه لرابط جديد</label>
                            <input placeholder="إختياري" type="text" name="new_pricses_2_links[]" value="<?php echo esc_textarea($new_pricses_2['link']); ?>" class="new-pricses-2-input" />
                        </div>
                        <button type="button" class="remove-new-pricses-2">الحذف</button>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="new-pricses-2-pair">
                    <div class="new-pricses-2-image">
                        <label>الصورة للمنتج الجديد</label>
                        <div class="new-image-preview-wrapper">
                            <img src="" class="new-image-preview" style="display: none;" />
                        </div>
                        <input type="hidden" name="new_pricses_2_images[]" class="new-pricses-2-image-url" />
                        <button type="button" class="upload-new-image-button">رفع الصورة</button>
                    </div>
                    <div class="new-pricses-2-name">
                        <label>الأسم الجديد</label>
                        <input type="text" name="new_pricses_2_names[]" class="new-pricses-2-input" />
                    </div>
                    <div class="new-pricses-2-desc">
                        <label>الوصف الجديد</label>
                        <textarea name="new_pricses_2_descs[]" class="new-pricses-2-textarea"></textarea>
                    </div>
                    <div class="new-pricses-2-price">
                        <label>السعر الجديد</label>
                        <input type="text" name="new_pricses_2_prices[]" class="new-pricses-2-input" />
                    </div>
                    <div class="new-pricses-2-link">
                        <label>التوجه لرابط جديد</label>
                        <input placeholder="إختياري" type="text" name="new_pricses_2_links[]" class="new-pricses-2-input" />
                    </div>
                    <button type="button" class="remove-new-pricses-2">الحذف</button>
                </div>
            <?php endif; ?>
        </div>

        <button type="button" id="add-new-pricses-2-button" class="add-new-pricses-2-btn">اضافة منتج جديد</button>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#add-new-pricses-2-button').on('click', function() {
                $('#new-pricses-2-fields-container').append(`
                    <div class="new-pricses-2-pair">
                        <div class="new-pricses-2-image">
                            <label>الصورة</label>
                            <div class="new-image-preview-wrapper">
                                <img src="" class="new-image-preview" style="display: none;" />
                            </div>
                            <input type="hidden" name="new_pricses_2_images[]" class="new-pricses-2-image-url" />
                            <button type="button" class="upload-new-image-button">رفع الصورة</button>
                        </div>
                        <div class="new-pricses-2-name">
                            <label>الأسم الجديد</label>
                            <input type="text" name="new_pricses_2_names[]" class="new-pricses-2-input" />
                        </div>
                        <div class="new-pricses-2-desc">
                            <label>الوصف الجديد</label>
                            <textarea name="new_pricses_2_descs[]" class="new-pricses-2-textarea"></textarea>
                        </div>
                        <div class="new-pricses-2-price">
                            <label>السعر الجديد</label>
                            <input type="text" name="new_pricses_2_prices[]" class="new-pricses-2-input" />
                        </div>
                        <div class="new-pricses-2-link">
                            <label>التوجه لرابط جديد</label>
                            <input placeholder="إختياري" type="text" name="new_pricses_2_links[]" class="new-pricses-2-input" />
                        </div>
                        <button type="button" class="remove-new-pricses-2">الحذف</button>
                    </div>
                `);
            });

            // Handle image upload
            $(document).on('click', '.upload-new-image-button', function(e) {
                e.preventDefault();
                var button = $(this);
                var file_frame = wp.media({
                    title: 'اختر صورة',
                    button: {
                        text: 'استخدم هذه الصورة'
                    },
                    multiple: false
                });

                file_frame.on('select', function() {
                    var attachment = file_frame.state().get('selection').first().toJSON();
                    button.prev('.new-image-preview-wrapper').find('.new-image-preview').attr('src', attachment.url).show();
                    button.prev('.new-pricses-2-image-url').val(attachment.url);
                });

                file_frame.open();
            });

            // Handle removing product fields
            $(document).on('click', '.remove-new-pricses-2', function() {
                $(this).closest('.new-pricses-2-pair').remove();
            });
        });
    </script>
    <?php
}

function save_new_product_meta($post_id) {
    if (!isset($_POST['new_pricses_2_meta_nonce']) || !wp_verify_nonce($_POST['new_pricses_2_meta_nonce'], basename(__FILE__))) {
        return;
    }

    // Save main title
    if (isset($_POST['new_pricses_2_main_title'])) {
        update_post_meta($post_id, 'new_pricses_2_main_title', sanitize_text_field($_POST['new_pricses_2_main_title']));
    }

    // Save product data
    $new_pricses_2_names = $_POST['new_pricses_2_names'];
    $new_pricses_2_descs = $_POST['new_pricses_2_descs'];
    $new_pricses_2_prices = $_POST['new_pricses_2_prices'];
    $new_pricses_2_links = $_POST['new_pricses_2_links'];
    $new_pricses_2_images = $_POST['new_pricses_2_images'];

    $new_pricses_2_info = [];
    if (!empty($new_pricses_2_names)) {
        foreach ($new_pricses_2_names as $index => $name) {
            $new_pricses_2_info[] = [
                'name' => sanitize_text_field($name),
                'desc' => sanitize_textarea_field($new_pricses_2_descs[$index]),
                'price' => sanitize_text_field($new_pricses_2_prices[$index]),
                'link' => sanitize_text_field($new_pricses_2_links[$index]),
                'image' => esc_url($new_pricses_2_images[$index]),
            ];
        }
    }
    update_post_meta($post_id, '_new_pricses_2_meta_key', $new_pricses_2_info);
}
add_action('save_post', 'save_new_product_meta');

?>
