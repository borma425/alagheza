<?php


// Add a custom meta box for the 'post' post type
function my_custom_meta_boxes() {
    add_meta_box(
        'my_gallery_meta_box', // ID
        'جاليري الصور', // Title
        'my_gallery_meta_box_callback', // Callback function
        'post', // Post type
        'normal' // Context
    );
}
add_action('add_meta_boxes', 'my_custom_meta_boxes');

// Callback function for the meta box
function my_gallery_meta_box_callback($post) {
    // Retrieve existing image IDs
    $images = get_post_meta($post->ID, 'gallery_images', true);
    ?>
    <div id="my-gallery" style="display: flex; flex-wrap: wrap;">
        <?php if ($images): ?>
            <?php foreach ($images as $image_id): ?>
                <div class="gallery-image" style="margin: 5px; position: relative;">
                    <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                    <input type="hidden" name="gallery_images[]" value="<?php echo esc_attr($image_id); ?>">
                    <button class="remove-image button" style="position: absolute; top: 0; right: 0;">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" class="button" id="upload_gallery_image">اضافة صورة</button>
    <script>
        jQuery(document).ready(function($) {
            var file_frame;

            $('#upload_gallery_image').on('click', function(event) {
                event.preventDefault();
                
                // If the media frame already exists, reopen it.
                if (file_frame) {
                    file_frame.open();
                    return;
                }
                
                // Create a new media frame.
                file_frame = wp.media({
                    title: 'حدد او ارفع صورة',
                    button: {
                        text: 'اختيار هذة الصورة'
                    },
                    multiple: true // Set to true to allow multiple images to be selected
                });

                // When an image is selected, run a callback.
                file_frame.on('select', function() {
                    var attachments = file_frame.state().get('selection').toJSON();
                    attachments.forEach(function(attachment) {
                        // Use the full image URL if the thumbnail is not available
                        var imgSrc = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        $('#my-gallery').append('<div class="gallery-image" style="margin: 5px; position: relative;"><img src="' + imgSrc + '" style="width: 100px; height: auto;"/><input type="hidden" name="gallery_images[]" value="' + attachment.id + '"><button class="remove-image button" style="position: absolute; top: 0; right: 0;">&times;</button></div>');
                    });
                });

                // Finally, open the modal on click.
                file_frame.open();
            });

            // Remove image functionality
            $(document).on('click', '.remove-image', function() {
                $(this).closest('.gallery-image').remove();
            });
        });
    </script>
    <?php
}

// Save the meta box data with removed images properly handled
function my_save_gallery_meta_box_data($post_id) {
    // Ensure that gallery_images exists and is set in the POST data
    if (isset($_POST['gallery_images'])) {
        // Sanitize and process the gallery image IDs
        $gallery_images = array_map('intval', $_POST['gallery_images']); // Ensure the image IDs are integers

        // If no images are left (empty array), delete the gallery meta field
        if (empty($gallery_images)) {
            delete_post_meta($post_id, 'gallery_images');
        } else {
            // Otherwise, update the gallery meta field with the new image array
            update_post_meta($post_id, 'gallery_images', $gallery_images);
        }
    } else {
        // If the 'gallery_images' field is not set, delete the gallery meta field
        delete_post_meta($post_id, 'gallery_images');
    }
}
add_action('save_post', 'my_save_gallery_meta_box_data');

// Enqueue necessary scripts
function my_enqueue_scripts() {
    wp_enqueue_media(); // Enqueue WordPress media
    wp_enqueue_script('jquery'); // Enqueue jQuery
}
add_action('admin_enqueue_scripts', 'my_enqueue_scripts');
