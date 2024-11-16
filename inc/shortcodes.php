<?php
function get_c_meta($meta_key) {
    global $post; // Declare global $post here
    return $post ? get_post_meta($post->ID, $meta_key, true) : '';
}


add_shortcode('specifications_section', 'display_device_specifications');
add_shortcode('qa_section', 'display_qa_section');
add_shortcode('pros_section', 'display_pros_section');
add_shortcode('cons_section', 'display_cons_section');

add_shortcode('more_products_prices_section', 'display_more_products_prices_section');
add_shortcode('review_section', 'display_review_section');



function display_device_specifications() {
    global $post;

    // Retrieve meta data
    $specifications_main_title = get_post_meta($post->ID, 'device_specifications_main_title', true);
    $product_thumbnail = get_post_meta($post->ID, 'product_thumbnail', true);
    $device_specs = get_post_meta($post->ID, 'device_specs', true);

    // Start building the HTML for the specifications section
    ob_start();
    ?>
    <section id="specifications" class="specifications">
        <h2 class="section-title"><?php echo esc_html($specifications_main_title); ?></h2>
        <div class="holder">
            <?php if ($product_thumbnail): ?>
                <img src="<?php echo esc_url($product_thumbnail); ?>" alt="<?php echo esc_attr($specifications_main_title); ?>">
            <?php endif; ?>
            <ul class="feature-list">
                <?php if (!empty($device_specs)) : ?>
                    <?php foreach ($device_specs as $spec): ?>
                        <li class="feature-item">
                            <?php if (!empty($spec['icon'])): ?>
                                <img src="<?php echo esc_url($spec['icon']); ?>" alt="<?php echo esc_attr($spec['name']); ?>" class="feature-icon">
                            <?php endif; ?>
                            <span><b><?php echo esc_html($spec['name']); ?>:</b> <?php echo esc_html($spec['description']); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </section>
    <?php

    // Return the output buffer content
    return ob_get_clean();
}




function display_qa_section() {
    if (!is_singular('post')) {
        return ''; // Only display on single posts
    }

    global $post;

    // Retrieve saved meta data
    $qa_title = get_post_meta($post->ID, 'qa_title', true);
    $qa_data = get_post_meta($post->ID, '_qa_meta_key', true);
    $qa_data = is_array($qa_data) ? $qa_data : [];

    // Fallback title if no custom title is set
    $qa_title = !empty($qa_title) ? esc_html($qa_title) : 'أسئلة شائعة';

    // If there's no Q&A data, display a message or return empty
    if (empty($qa_data)) {
        return '<section id="faq"><h2 class="section-title">' . $qa_title . '</h2><p>لا توجد أسئلة وأجوبة مضافة.</p></section>';
    }

    // Start building the output
    $output = '<section id="faq">';
    $output .= '<h2 class="section-title">' . esc_html($qa_title) . '</h2>';
    $output .= '<div class="accordion">';

    // Loop through the Q&A data and create accordion items
    foreach ($qa_data as $qa) {
        $question = esc_html($qa['question']);
        $answer = esc_html($qa['answer']);

        $output .= '
        <div class="accordion-item">
            <div class="accordion-header">
                <span>' . $question . '</span>
                <span class="accordion-icon">+</span>
            </div>
            <div class="accordion-content">
                <p>' . $answer . '</p>
            </div>
        </div>';
    }

    $output .= '</div>';
    $output .= '</section>';

    return $output;
}





function display_pros_section() {
    if (!is_singular('post')) {
        return ''; // Only display in single posts
    }

    global $post;
    $pros_main_title = get_post_meta($post->ID, 'single_pros_main_title', true);
    $pros_content = get_post_meta($post->ID, 'single_pros', true);

    // Fallbacks if no data is set
    $pros_main_title = $pros_main_title ? esc_html($pros_main_title) : 'مميزات';
    $pros_content = $pros_content ?: '<li>لا توجد مميزات مدخلة</li>';

    // Add classes to ul and li elements
    $pros_content = str_replace('<ul>', '<ul class="feature-list pros">', $pros_content);
    $pros_content = str_replace('<li>', '<li >', $pros_content);

    // Render the Pros section
    return '
    <section id="pros">
        <div>
            <h2 class="section-title">' . esc_html($pros_main_title) . '</h2>
            ' . $pros_content . '
        </div>
    </section>';
}


function display_cons_section() {
    if (!is_singular('post')) {
        return ''; // Only display in single posts
    }

    global $post;
    $cons_main_title = get_post_meta($post->ID, 'single_cons_main_title', true);
    $cons_content = get_post_meta($post->ID, 'single_cons', true);

    // Fallbacks if no data is set
    $cons_main_title = $cons_main_title ? esc_html($cons_main_title) : 'عيوب';
    $cons_content = $cons_content ?: '<li>لا توجد عيوب مدخلة</li>';

    // Add classes to ul and li elements
    $cons_content = str_replace('<ul>', '<ul class="feature-list cons">', $cons_content);
    $cons_content = str_replace('<li>', '<li class="feature-item">', $cons_content);

    // Render the Cons section
    return '
    <section id="cons">
        <div>
            <h2 class="section-title">' . esc_html($cons_main_title) . '</h2>
            ' . $cons_content . '
        </div>
    </section>';
}



function display_more_products_prices_section() {
    global $post; // Make sure to use the current post object to get the data

    // Fetch the product sections meta field
    $sections = get_post_meta($post->ID, '_product_sections', true);

    // Check if the sections exist
    if (empty($sections)) {
        return ''; // Return empty if no sections are available
    }

    // Start the section container
    $output = '';

    // Loop through each section
    foreach ($sections as $section) {
        // Section title
        $section_title = isset($section['title']) ? esc_html($section['title']) : 'القسم';

        // Start the HTML structure for each section
        $output .= '<section id="pricing" class="pricing-section">
                        <h2 class="section-title">' . $section_title . '</h2>
                        <div class="table-container">
                            <button class="scroll-button scroll-left" aria-label="Scroll left" style="display: block;"></button>
                            <button class="scroll-button scroll-right" aria-label="Scroll right" style="display: none;"></button>
                            <div class="scroll-container" style="direction: rtl;">
                                <table class="price-table">
                                    <thead>
                                        <tr>
                                            <th>الصوره</th>
                                            <th>اسم الجهاز</th>
                                            <th>الوصف</th>
                                            <th>السعر</th>
                                            <th>التفاصيل</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

        // Loop through each subsection (product) in the current section
        if (!empty($section['subsections'])) {
            foreach ($section['subsections'] as $subsection) {
                $image = isset($subsection['image']) ? esc_url($subsection['image']) : '';
                $name = isset($subsection['name']) ? esc_html($subsection['name']) : '';
                $desc = isset($subsection['desc']) ? esc_html($subsection['desc']) : '';
                $price = isset($subsection['price']) ? esc_html($subsection['price']) : '';
                $link = isset($subsection['link']) ? esc_url($subsection['link']) : '';

                // Generate a table row for each subsection (product)
                $output .= '<tr>';
                $output .= '<td><img src="' . $image . '" width="100" alt="' . $name . '"></td>';
                $output .= '<td>' . $name . '</td>';
                $output .= '<td>' . $desc . '</td>';
                $output .= '<td>' . $price . '</td>';
                if (!empty($link)) {
                    $output .= '<td><a href="' . esc_url($link) . '">اعرف اكتر</a></td>';
                } else {
                    $output .= '<td></td>'; // Empty cell if no link
                }

                $output .= '</tr>';
            }
        }

        // Close the table and section container for this section
        $output .= '</tbody>
                    </table>
                </div>
            </div>
        </section>';
    }

    // Return the generated HTML
    return $output;
}

function display_review_section($post_id) {
    global $post;

    // Retrieve custom meta fields
    $wp_review_heading = get_post_meta($post->ID, 'wp_review_heading', true);
    $wp_review_total = get_post_meta($post->ID, 'wp_review_total', true);
    $wp_review_pros = get_post_meta($post->ID, 'wp_review_pros', true);
    $wp_review_cons = get_post_meta($post->ID, 'wp_review_cons', true);
    $wp_review_desc = get_post_meta($post->ID, 'wp_review_desc', true);
    $wp_review_desc_title = get_post_meta($post->ID, 'wp_review_desc_title', true);

    // Get schema product details if available
    $meta_key = 'wp_review_schema_options';
    $meta_value = get_post_meta($post->ID, $meta_key, true);
    $product_name = isset($meta_value['Product']['name']) ? $meta_value['Product']['name'] : '';
    $product_desc = isset($meta_value['Product']['description']) ? $meta_value['Product']['description'] : '';
    $product_price = isset($meta_value['Product']['price']) ? $meta_value['Product']['price'] : '';
    $product_priceCurrency = isset($meta_value['Product']['priceCurrency']) ? $meta_value['Product']['priceCurrency'] : '';
    $product_brand = isset($meta_value['Product']['brand']) ? $meta_value['Product']['brand'] : '';
    $product_sku = isset($meta_value['Product']['sku']) ? $meta_value['Product']['sku'] : '';

    ob_start();
    ?>
    <section id="review-summary" class="review-summary">
							<div class="review-header">
								<!-- Product Info Section -->
								<div class="product-info">
									<div class="brand-info">
<svg 
    xmlns="http://www.w3.org/2000/svg" 
    width="20" 
    height="15" 
    viewBox="0 0 20 15" 
    class="brand-logo">
  <rect x="0" y="0" width="20" height="4" fill="#333" stroke="#1a73e8" stroke-width="1" />
  <rect x="0" y="6" width="15" height="4" fill="#666" stroke="#1a73e8" stroke-width="1" />
  <rect x="0" y="12" width="10" height="3" fill="#999" stroke="#1a73e8" stroke-width="1" />
</svg>

									<span class="brand-name"><?php echo !empty(get_the_category()) ? get_the_category()[0]->name : 'No categories found'; ?></span>
									</div>

									<h6 class="product-title">

                                    <?php echo esc_attr($wp_review_heading); ?>
                                </h6>

									<div class="product-meta">

                                    <div class="meta-item">
     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 6h18M3 12h18M3 18h18"></path>
    </svg>
    <span class="meta-label">اسم المنتج:</span>
    <span class="meta-value"><?= $product_name ?></span>
  </div>


  <div class="meta-item">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10"></circle>
      <path d="M12 16v-4"></path>
      <path d="M12 8h.01"></path>
    </svg>
    <span class="meta-label">الماركة:</span>
    <span class="meta-value"><?= $product_brand ?></span>
  </div>

  <div class="meta-item">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <rect x="2" y="4" width="20" height="16" fill="none" stroke="#6b7280"></rect>
      <line x1="4" y1="6" x2="4" y2="18" stroke-width="1.5"></line>
      <line x1="6" y1="6" x2="6" y2="18" stroke-width="1"></line>
      <line x1="8" y1="6" x2="8" y2="18" stroke-width="2"></line>
      <line x1="10" y1="6" x2="10" y2="18" stroke-width="1"></line>
      <line x1="12" y1="6" x2="12" y2="18" stroke-width="1.5"></line>
      <line x1="14" y1="6" x2="14" y2="18" stroke-width="2"></line>
      <line x1="16" y1="6" x2="16" y2="18" stroke-width="1"></line>
      <line x1="18" y1="6" x2="18" y2="18" stroke-width="1.5"></line>
    </svg>
    <span class="meta-label">كود المنتج:</span>
    <span class="meta-value"><?= $product_sku ?></span>
  </div>

  <div class="meta-item">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<line x1="12" y1="1" x2="12" y2="23"></line>
												<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
											</svg>
    <span class="meta-label">السعر:</span>
    <span class="meta-value"><?= $product_price ?></span>
  </div>

  <div class="meta-item">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 2l4 4h-3v10h-2V6H8l4-4z"></path>
    </svg>
    <span class="meta-label">العملة:</span>
    <span class="meta-value"><?= $product_priceCurrency ?></span>
  </div>
									</div>

									<div class="rating-box">
										<div class="rating-score"><?php echo $wp_review_total ?></div>
										<div class="rating-label">التقييم النهائي</div>
									</div>
								</div>

								<!-- Product Details Section -->
								<div class="product-details">
									<p class="product-description">
<?= $product_desc ?>
									</p>

									<?php

                                    // Ensure the structure is correct
if (is_array($meta_value) && isset($meta_value['Product']['image'])) {
    $image_data = $meta_value['Product']['image'];
    $image_url = isset($image_data['url']) ? $image_data['url'] : ''; // Get image URL

    // Check if image URL exists and output it
    if (!empty($image_url)) {
        $reviewed_item_image = $image_url; // Set image URL
    } else {
        $reviewed_item_image = "No_image_URL"; // Fallback when image is missing
    }
} else {
    echo 'Product image data is not set correctly.';
    $reviewed_item_image = "No_image_URL"; // Fallback image
}

// Output the image as an <img> tag
echo '<img src="' . esc_url($reviewed_item_image) . '" alt="'.$product_desc.'" class="product-image" />';

?>

									<div class="holder">
										<div class="price-tag">
											<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a73e8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<line x1="12" y1="1" x2="12" y2="23"></line>
												<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
											</svg>
											<span class="price-value"><?php echo $product_price ?> </span>
										</div>
										<div class="price-tag">
											<span class="price-value"><?php echo $product_priceCurrency ?></span>
										</div>
									</div>

<p class="product-description">
<?= $wp_review_desc ?>
	</p>


								</div>
							</div>





<?php
        $items = get_post_meta( $post->ID, 'wp_review_item', true );
    
        $items = apply_filters( 'wp_review_items', $items );
        $transformed_array = [];

if (is_array($items)) {
    // Loop through the items to build the new array
    foreach ($items as $item) {
        $unique_key = (string) time() . uniqid(); // Generate a unique key for each item
        
        // Assign the title and star rating for each item
        $transformed_array[$unique_key] = [
            'key' => $item['wp_review_item_title'],
            'value' => $item['wp_review_item_star'],
        ];
    }
}

echo '<div class="ratings-card">';
echo '<h2 class="ratings-title">'.$wp_review_desc_title .'</h2>';

foreach ($transformed_array as $key => $rating) {
    $rating_name = esc_html($rating['key']);
    $rating_value = esc_html($rating['value']);
    $rating_percent = ($rating_value / 5) * 100; // Assuming rating is out of 5 stars

    // Display each rating item
    echo '<div class="rating-item">';
    echo '<div class="rating-header">';
    echo '<span class="rating-name">' . $rating_name . '</span>';
    echo '<span class="rating-amount">' . $rating_value . '/5</span>'; 
    echo '</div>';
    echo '<div class="rating-bar">';
    echo '<div class="rating-fill" data-progress="' . $rating_percent . '" style="width: ' . $rating_percent . '%;"></div>';
    echo '</div>';
    echo '</div>';
}

echo '</div>';
?>






<?php
// Get pros and cons from the post meta
$wp_review_pros = get_post_meta($post->ID, 'wp_review_pros', true);
$wp_review_cons = get_post_meta($post->ID, 'wp_review_cons', true);
?>

<!-- Pros Section -->
<div class="props-and-cons pros">
    <div class="props-and-cons-header">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <h2>مميزات</h2>
    </div>
    <?php if (!empty($wp_review_pros)) : ?>
        <?php echo $wp_review_pros; // Output the pros HTML directly ?>
    <?php else : ?>
        <p>لا توجد مميزات مضافة.</p>
    <?php endif; ?>
</div>

<!-- Cons Section -->
<div class="props-and-cons cons">
    <div class="props-and-cons-header">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
        <h2>عيوب</h2>
    </div>
    <?php if (!empty($wp_review_cons)) : ?>
        <?php echo $wp_review_cons; // Output the cons HTML directly ?>
    <?php else : ?>
        <p>لا توجد عيوب مضافة.</p>
    <?php endif; ?>
</div>

						</section>
    <?php
    return ob_get_clean();
}
