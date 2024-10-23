<?php
// Add Meta Box
function product_sections_meta_box() {
    add_meta_box(
        'product_sections', // ID of the meta box
        'خطط الأسعار',            // Meta box title
        'render_product_sections_meta_box', // Callback to display the fields
        'post', // Post type where the meta box should appear ('post' for custom post type)
        'normal', // Context (normal, side, etc.)
        'high' // Priority
    );
}
add_action('add_meta_boxes', 'product_sections_meta_box');

// Render Meta Box
function render_product_sections_meta_box($post) {

    echo '<style>


/* Meta Box Container */
#product-sections-container {
    margin-bottom: 20px;
    padding: 20px;
    background-color: #f4f7fa;
    border: 1px solid #d0d7e1;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Section Container */
.product-section {
    margin-bottom: 25px;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #e0e6f0;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    position: relative;
    transition: all 0.3s ease;
}

.product-section:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

/* Section Title */
.product-section h2 {
    margin-bottom: 20px;
    font-size: 20px;
    color: #333;
    border-bottom: 2px solid #d0d7e1;
    padding-bottom: 10px;
    font-weight: 600;
}

/* Input Fields */
.product-section input[type="text"],
.product-section input[type="url"],
.product-section textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccd2da;
    border-radius: 6px;
    font-size: 14px;
    color: #333;
    background-color: #f9f9f9;
    transition: border-color 0.3s ease;
}

.product-section input:focus,
.product-section textarea:focus {
    border-color: #0073aa;
    background-color: #fff;
}

/* Textarea for Description */
.product-section textarea {
    height: 100px;
    resize: vertical;
}

/* Subsection Container */
.subsection {
    padding: 20px;
    background-color: #f8f9fc;
    border: 1px solid #d0d7e1;
    border-radius: 8px;
    margin-bottom: 20px;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

/* Subsection Title */
.subsection h3 {
    margin: 0 0 15px;
    font-size: 16px;
    color: #444;
    font-weight: 500;
}

/* Image Preview Box */
.subsection .image-preview-box {
    position: relative;
    margin-bottom: 15px;
    max-width: 150px;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.subsection .image-preview-box img {
    display: block;
    width: 100%;
    height: auto;
    border-bottom: 1px solid #ddd;
}

.subsection .remove-image-button {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: rgba(255, 255, 255, 0.8);
    color: #d63638;
    font-size: 14px;
    border: none;
    border-radius: 50%;
    padding: 5px;
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}

.subsection .remove-image-button:hover {
    background-color: rgba(255, 255, 255, 1);
}

/* Buttons */
.product-section .remove-section,
.product-section .add-subsection,
.subsection .remove-subsection,
.subsection .upload-image-button {
    display: inline-block;
    padding: 10px 20px;
    font-size: 14px;
    color: #fff;
    background-color: #0073aa;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-right: 10px;
    margin-bottom: 10px;
    transition: background-color 0.3s ease;
}

.product-section .remove-section {
    background-color: #d63638;
}

.product-section .remove-section:hover,
.subsection .remove-subsection:hover {
    background-color: #b92d2f;
}

.product-section .add-subsection:hover,
.subsection .upload-image-button:hover {
    background-color: #005c8f;
}

/* Add Section Button */
#add-section {
    display: inline-block;
    padding: 12px 25px;
    font-size: 16px;
    color: #fff;
    background-color: #0073aa;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

#add-section:hover {
    background-color: #005c8f;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .product-section, .subsection {
        padding: 15px;
    }

    .product-section h2, .subsection h3 {
        font-size: 16px;
    }

    .product-section input[type="text"],
    .product-section input[type="url"],
    .product-section textarea {
        font-size: 12px;
    }

    #add-section {
        font-size: 14px;
    }
}

</style>';
    // Nonce field for security
    wp_nonce_field('save_product_sections', 'product_sections_nonce');

    // Get the saved values if any
    $sections = get_post_meta($post->ID, '_product_sections', true);

    // Output the sections container
    echo '<div id="product-sections-container">';

    if (!empty($sections)) {
        foreach ($sections as $section_index => $section) {
            echo '<div class="product-section">';
            echo '<h2>القسم</h2>';
            echo '<input type="text" name="product_sections[' . $section_index . '][title]" value="' . esc_attr($section['title']) . '" placeholder="العنوان الرئيسي" />';
            echo '<button type="button" class="remove-section">حذف القسم بلكامل</button>';
            
            // Render subsections
            if (!empty($section['subsections'])) {
                foreach ($section['subsections'] as $sub_index => $subsection) {
                    render_subsection($section_index, $sub_index, $subsection);
                }
            }

            echo '<button type="button" class="add-subsection" data-section-index="' . $section_index . '">إضافة منتج أخر</button>';
            echo '</div>';
        }
    }

    echo '</div>';
    echo '<button type="button" id="add-section">إضافة قسم جديد</button>';
}

// Render Subsections
function render_subsection($section_index, $sub_index, $subsection = null) {
    $image = $subsection['image'] ?? '';
    $name = $subsection['name'] ?? '';
    $desc = $subsection['desc'] ?? '';
    $price = $subsection['price'] ?? '';
    $link = $subsection['link'] ?? '';

    echo '<div class="subsection">';
    echo '<h3>المنتج ' . ($sub_index + 1) . '</h3>';
    echo '<input type="text" name="product_sections[' . $section_index . '][subsections][' . $sub_index . '][name]" value="' . esc_attr($name) . '" placeholder="الإسم" />';
    echo '<textarea name="product_sections[' . $section_index . '][subsections][' . $sub_index . '][desc]" placeholder="الوصف">' . esc_textarea($desc) . '</textarea>';
    echo '<input type="text" name="product_sections[' . $section_index . '][subsections][' . $sub_index . '][price]" value="' . esc_attr($price) . '" placeholder="السعر" />';
    echo '<input type="url" name="product_sections[' . $section_index . '][subsections][' . $sub_index . '][link]" value="' . esc_attr($link) . '" placeholder="التوجه لرابط" />';
    
    // Image upload field with preview
    echo '<input type="hidden" name="product_sections[' . $section_index . '][subsections][' . $sub_index . '][image]" value="' . esc_attr($image) . '" class="image-input" />';
    echo '<button type="button" class="upload-image-button">رفع صورة</button>';
    if ($image) {
        echo '<img src="' . esc_url($image) . '" class="image-preview" style="max-width:150px;display:block;margin-top:10px;" />';
    } else {
        echo '<img src="" class="image-preview" style="max-width:150px;display:none;margin-top:10px;" />';
    }
    
    echo '<button type="button" class="remove-subsection">حذف المنتج</button>';
    echo '</div>';
}

// Save Meta Box Data
function save_product_sections($post_id) {
    // Verify nonce
    if (!isset($_POST['product_sections_nonce']) || !wp_verify_nonce($_POST['product_sections_nonce'], 'save_product_sections')) {
        return;
    }

    // Check post type and autosave
    if ('post' !== $_POST['post_type'] || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Sanitize and save the sections
    if (isset($_POST['product_sections'])) {
        $sections = array_map(function($section) {
            return [
                'title' => sanitize_text_field($section['title']),
                'subsections' => array_map(function($subsection) {
                    return [
                        'image' => esc_url_raw($subsection['image']),
                        'name' => sanitize_text_field($subsection['name']),
                        'desc' => sanitize_textarea_field($subsection['desc']),
                        'price' => sanitize_text_field($subsection['price']),
                        'link' => esc_url_raw($subsection['link']),
                    ];
                }, $section['subsections'] ?? [])
            ];
        }, $_POST['product_sections']);

        update_post_meta($post_id, '_product_sections', $sections);
    }
}
add_action('save_post', 'save_product_sections');

// JavaScript for dynamic add/remove and image preview
function product_sections_admin_scripts() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        var sectionIndex = $('#product-sections-container .product-section').length;

        // Add new section
        $('#add-section').on('click', function() {
            sectionIndex++;
            var sectionHtml = '<div class="product-section">' +
                              '<h2>القسم</h2>' +
                              '<input type="text" name="product_sections[' + sectionIndex + '][title]" placeholder="العنوان الرئيسي" />' +
                              '<button type="button" class="remove-section">حذف القسم بلكامل</button>' +
                              '<button type="button" class="add-subsection" data-section-index="' + sectionIndex + '">اضافة  منتج اخر</button>' +
                              '</div>';
            $('#product-sections-container').append(sectionHtml);
        });

        // Add new subsection
        $(document).on('click', '.add-subsection', function() {
            var sectionIndex = $(this).data('section-index');
            var subsectionIndex = $(this).closest('.product-section').find('.subsection').length;
            var subsectionHtml = '<div class="subsection">' +
                                 '<h3>المنتج ' + (subsectionIndex + 1) + '</h3>' +
                                 '<input type="text" name="product_sections[' + sectionIndex + '][subsections][' + subsectionIndex + '][name]" placeholder="الاسم" />' +
                                 '<textarea name="product_sections[' + sectionIndex + '][subsections][' + subsectionIndex + '][desc]" placeholder="الوصف"></textarea>' +
                                 '<input type="text" name="product_sections[' + sectionIndex + '][subsections][' + subsectionIndex + '][price]" placeholder="السعر" />' +
                                 '<input type="url" name="product_sections[' + sectionIndex + '][subsections][' + subsectionIndex + '][link]" placeholder="التوجه لرابط" />' +
                                 '<input type="hidden" name="product_sections[' + sectionIndex + '][subsections][' + subsectionIndex + '][image]" class="image-input" />' +
                                 '<button type="button" class="upload-image-button">رفع صورة</button>' +
                                 '<img src="" class="image-preview" style="max-width:150px;display:none;margin-top:10px;" />' +
                                 '<button type="button" class="remove-subsection">حذف المنتج</button>' +
                                 '</div>';
            $(this).before(subsectionHtml);
        });

        // Remove section
        $(document).on('click', '.remove-section', function() {
            $(this).closest('.product-section').remove();
        });

        // Remove subsection
        $(document).on('click', '.remove-subsection', function() {
            $(this).closest('.subsection').remove();
        });

        // Image upload and preview
        $(document).on('click', '.upload-image-button', function(e) {
            e.preventDefault();
            var button = $(this);
            var customUploader = wp.media({
                title: 'اختيار صورة',
                button: {
                    text: 'استخدام هذة الصورة '
                },
                multiple: false
            }).on('select', function() {
                var attachment = customUploader.state().get('selection').first().toJSON();
                button.prev('.image-input').val(attachment.url);
                button.next('.image-preview').attr('src', attachment.url).show();
            }).open();
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'product_sections_admin_scripts');
