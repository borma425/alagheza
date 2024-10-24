<?php
// Add the meta box
function add_specifications_meta_box() {
    add_meta_box(
        'device_specifications', // ID of the meta box
        'جدول المواصفات', // Title of the meta box
        'device_specifications_callback', // Callback function
        'post', // Post type
        'normal', // Context
        'default' // Priority
    );
}
add_action('add_meta_boxes', 'add_specifications_meta_box');

function device_specifications_callback($post) {
    $device_specs = get_post_meta($post->ID, 'device_specs', true);
    $device_specifications_main_title = get_post_meta($post->ID, 'device_specifications_main_title', true);
    $product_thumbnail = get_post_meta($post->ID, 'product_thumbnail', true); // New Thumbnail Field

    wp_nonce_field(basename(__FILE__), 'custom_meta_box_nonce');
    ?>
    <style>
    /* Your existing styles */
    </style>

    <h3>جدول المواصفات</h3>

    <div>
        <label style="font-size:medium;">العنوان الرئيسي</label>
        <input type="text" name="device_specifications_main_title" value="<?php echo esc_attr($device_specifications_main_title); ?>" class="qa-input" placeholder="العنوان الرئيسي" />
    </div>
    <br><br>

    <!-- New Thumbnail Field -->
    <div>
        <label style="font-size:medium;">صورة المنتج</label>
        <input type="hidden" name="product_thumbnail" value="<?php echo esc_url($product_thumbnail); ?>" class="product-thumbnail-url" />
        <img src="<?php echo esc_url($product_thumbnail); ?>" class="product-thumbnail-preview" style="max-width:100px; max-height:100px; display:<?php echo empty($product_thumbnail) ? 'none' : 'block'; ?>" />
        <button type="button" class="upload-thumbnail-button button">رفع صورة المنتج</button>
    </div>
    <br><br>

    <div id="device-specifications-container">
        <?php
        if (!empty($device_specs)) {
            foreach ($device_specs as $key => $spec) {
                ?>
                <div class="specification-item">
                    <input type="text" name="device_specs[<?php echo esc_attr($key); ?>][name]" value="<?php echo esc_attr($spec['name']); ?>" placeholder="العنوان" />
                    <textarea name="device_specs[<?php echo esc_attr($key); ?>][description]" placeholder="الوصف"><?php echo esc_attr($spec['description']); ?></textarea>
                    <input type="hidden" name="device_specs[<?php echo esc_attr($key); ?>][icon]" value="<?php echo esc_url($spec['icon']); ?>" class="device-icon-url" />
                    <img src="<?php echo esc_url($spec['icon']); ?>" class="device-icon-preview" style="max-width:100px; max-height:100px; display:<?php echo empty($spec['icon']) ? 'none' : 'block'; ?>" />
                    <button type="button" class="upload-icon-button button">رفع الأيقونه</button>
                    <button type="button" class="remove-specification button">الحذف</button>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <button type="button" id="add-specification" class="button">اضافة</button>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        function createMediaUploader(button) {
            var mediaUploader = wp.media({
                title: 'اختر الايقونة',
                button: {
                    text: 'اختر هذة الايقونه'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                var container = button.closest('.specification-item');
                container.find('.device-icon-url').val(attachment.url);
                container.find('.device-icon-preview').attr('src', attachment.url).show();
            });

            return mediaUploader;
        }

        // Image uploader for product thumbnail
        function createThumbnailUploader(button) {
            var mediaUploader = wp.media({
                title: 'اختر صورة المنتج',
                button: {
                    text: 'اختر هذه الصورة'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('.product-thumbnail-url').val(attachment.url);
                $('.product-thumbnail-preview').attr('src', attachment.url).show();
            });

            return mediaUploader;
        }

        // Add new specification
        $('#add-specification').click(function() {
            var newIndex = Date.now(); // Unique index
            $('#device-specifications-container').append(`
                <div class="specification-item">
                    <input type="text" name="device_specs[` + newIndex + `][name]" placeholder="العنوان" />
                    <textarea name="device_specs[` + newIndex + `][description]" placeholder="الوصف"></textarea>
                    <input type="hidden" name="device_specs[` + newIndex + `][icon]" class="device-icon-url" />
                    <img src="" class="device-icon-preview" style="max-width:100px; max-height:100px; display:none;" />
                    <button type="button" class="upload-icon-button button">رفع الأيقونه</button>
                    <button type="button" class="remove-specification button">الحذف</button>
                </div>
            `);
        });

        // Remove specification
        $(document).on('click', '.remove-specification', function() {
            $(this).closest('.specification-item').remove();
        });

        // Handle image upload for each button separately
        $(document).on('click', '.upload-icon-button', function(e) {
            e.preventDefault();
            var button = $(this);
            var mediaUploader = createMediaUploader(button);
            mediaUploader.open();
        });

        // Handle product thumbnail upload
        $(document).on('click', '.upload-thumbnail-button', function(e) {
            e.preventDefault();
            var mediaUploader = createThumbnailUploader($(this));
            mediaUploader.open();
        });
    });
    </script>
    <?php
}

// Save the meta box data
function save_device_specs_meta_box_data($post_id) {
    // Check if the user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['device_specs'])) {
        update_post_meta($post_id, 'device_specs', $_POST['device_specs']);
    }
    if (isset($_POST['device_specifications_main_title'])) {
        update_post_meta($post_id, 'device_specifications_main_title', $_POST['device_specifications_main_title']);
    }

    // Save product thumbnail
    if (isset($_POST['product_thumbnail'])) {
        update_post_meta($post_id, 'product_thumbnail', $_POST['product_thumbnail']);
    }
}
add_action('save_post', 'save_device_specs_meta_box_data');
?>
