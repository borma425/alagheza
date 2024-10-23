<?php

function add_new_product_meta_box() {
    add_meta_box(
        'new_product_meta_box',          // New Meta box ID
        'تفاصيل المنتج',                // New Meta box title
        'display_new_product_meta_box',  // New Callback function
        'post',                          // Post type
        'normal',                        // Context
        'high'                           // Priority
    );
}
add_action('add_meta_boxes', 'add_new_product_meta_box');

function display_new_product_meta_box($post) {
    $new_product_data = get_post_meta($post->ID, '_new_product_meta_key', true);
    $new_product_data = is_array($new_product_data) ? $new_product_data : [];
    
    echo '
    <style>
        .new-product-meta-box {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px solid #ddd;
        }

        .new-product-pair {
            background-color: #fff;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .new-product-image {
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

        .new-product-input,
        .new-product-textarea,
        .new-product-image-url {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .add-new-product-btn,
        .upload-new-image-button,
        .remove-new-product {
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

        .remove-new-product {
            background-color: #dc3545;
            position: absolute;
            top: -25px;
            right: 10px;
            padding: 5px;
        }

        .add-new-product-btn:hover,
        .upload-new-image-button:hover,
        .remove-new-product:hover {
            background-color: #005a9c;
        }

        .remove-new-product:hover {
            background-color: #c82333;
        }
    </style>';

    wp_nonce_field(basename(__FILE__), 'new_product_meta_nonce');
    $new_prices_main_title = get_post_meta($post->ID, 'new_prices_main_title', true);

    ?>
    <div class="new-product-meta-box">
        <div>
            <label style="font-size:medium;">العنوان الرئيسي</label>
            <input type="text" name="new_prices_main_title" value="<?php echo esc_attr($new_prices_main_title); ?>" class="new-qa-input" placeholder="العنوان الرئيسي"/>
        </div>
        <br><br>
        <div id="new-product-fields-container">
            <?php if (!empty($new_product_data)) : ?>
                <?php foreach ($new_product_data as $index => $product) : ?>
                    <div class="new-product-pair">
                        <div class="new-product-image">
                            <label>الصورة للمنتج</label>
                            <div class="new-image-preview-wrapper">
                                <img src="<?php echo esc_url($product['image']); ?>" class="new-image-preview" />
                            </div>
                            <input type="hidden" name="new_product_images[]" value="<?php echo esc_url($product['image']); ?>" class="new-product-image-url" />
                            <button type="button" class="upload-new-image-button">رفع الصورة</button>
                        </div>
                        <div class="new-product-name">
                            <label>الأسم</label>
                            <input type="text" name="new_product_names[]" value="<?php echo esc_attr($product['name']); ?>" class="new-product-input" />
                        </div>
                        <div class="new-product-desc">
                            <label>الوصف</label>
                            <textarea name="new_product_descs[]" class="new-product-textarea"><?php echo esc_textarea($product['desc']); ?></textarea>
                        </div>
                        <div class="new-product-price">
                            <label>السعر</label>
                            <input type="text" name="new_product_prices[]" value="<?php echo esc_attr($product['price']); ?>" class="new-product-input" />
                        </div>
                        <div class="new-product-link">
                            <label>التوجه لرابط</label>
                            <input placeholder="إختياري" type="text" name="new_product_links[]" value="<?php echo esc_textarea($product['link']); ?>" class="new-product-input" />
                        </div>
                        <button type="button" class="remove-new-product">الحذف</button>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="new-product-pair">
                    <div class="new-product-image">
                        <label>الصورة للمنتج</label>
                        <div class="new-image-preview-wrapper">
                            <img src="" class="new-image-preview" style="display: none;" />
                        </div>
                        <input type="hidden" name="new_product_images[]" class="new-product-image-url" />
                        <button type="button" class="upload-new-image-button">رفع الصورة</button>
                    </div>
                    <div class="new-product-name">
                        <label>الأسم</label>
                        <input type="text" name="new_product_names[]" class="new-product-input" />
                    </div>
                    <div class="new-product-desc">
                        <label>الوصف</label>
                        <textarea name="new_product_descs[]" class="new-product-textarea"></textarea>
                    </div>
                    <div class="new-product-price">
                        <label>السعر</label>
                        <input type="text" name="new_product_prices[]" class="new-product-input" />
                    </div>
                    <div class="new-product-link">
                        <label>التوجه لرابط</label>
                        <input placeholder="إختياري" type="text" name="new_product_links[]" class="new-product-input" />
                    </div>
                    <button type="button" class="remove-new-product">الحذف</button>
                </div>
            <?php endif; ?>
        </div>
        <button type="button" id="add-new-product-button" class="add-new-product-btn">اضافة منتج أخر</button>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#add-new-product-button').on('click', function() {
                $('#new-product-fields-container').append(`
                    <div class="new-product-pair">
                        <div class="new-product-image">
                            <label>الصورة</label>
                            <div class="new-image-preview-wrapper">
                                <img src="" class="new-image-preview" style="display: none;" />
                            </div>
                            <input type="hidden" name="new_product_images[]" class="new-product-image-url" />
                            <button type="button" class="upload-new-image-button">رفع الصورة</button>
                        </div>
                        <div class="new-product-name">
                            <label>الأسم</label>
                            <input type="text" name="new_product_names[]" class="new-product-input" />
                        </div>
                        <div class="new-product-desc">
                            <label>الوصف</label>
                            <textarea name="new_product_descs[]" class="new-product-textarea"></textarea>
                        </div>
                        <div class="new-product-price">
                            <label>السعر</label>
                            <input type="text" name="new_product_prices[]" class="new-product-input" />
                        </div>
                        <div class="new-product-link">
                            <label>التوجه لرابط</label>
                            <input placeholder="إختياري" type="text" name="new_product_links[]" class="new-product-input" />
                        </div>
                        <button type="button" class="remove-new-product">الحذف</button>
                    </div>
                `);
            });

            // Handle image uploading with preview
            $('.new-product-meta-box').on('click', '.upload-new-image-button', function(e) {
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
                    button.prev('.new-image-preview-wrapper').find('.new-image-preview').attr('src', attachment.url).show();
                    button.prev('.new-product-image-url').val(attachment.url);
                });

                file_frame.open();
            });

            // Handle removing product fields
            $('.new-product-meta-box').on('click', '.remove-new-product', function() {
                $(this).closest('.new-product-pair').remove();
            });
        });
    </script>
    <?php
}

function save_new_product_meta($post_id) {
    if (!isset($_POST['new_product_meta_nonce']) || !wp_verify_nonce($_POST['new_product_meta_nonce'], basename(__FILE__))) {
        return;
    }

    // Save main title
    if (isset($_POST['new_prices_main_title'])) {
        update_post_meta($post_id, 'new_prices_main_title', sanitize_text_field($_POST['new_prices_main_title']));
    }

    // Save product data
    $new_product_names = $_POST['new_product_names'];
    $new_product_descs = $_POST['new_product_descs'];
    $new_product_prices = $_POST['new_product_prices'];
    $new_product_links = $_POST['new_product_links'];
    $new_product_images = $_POST['new_product_images'];

    $new_product_data = [];
    if (!empty($new_product_names)) {
        foreach ($new_product_names as $index => $name) {
            $new_product_data[] = [
                'name' => sanitize_text_field($name),
                'desc' => sanitize_textarea_field($new_product_descs[$index]),
                'price' => sanitize_text_field($new_product_prices[$index]),
                'link' => sanitize_text_field($new_product_links[$index]),
                'image' => esc_url($new_product_images[$index]),
            ];
        }
    }
    update_post_meta($post_id, '_new_product_meta_key', $new_product_data);
}
add_action('save_post', 'save_new_product_meta');

?>
