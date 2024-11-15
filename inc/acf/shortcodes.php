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
    $pros_content = str_replace('<li>', '<li class="feature-item">', $pros_content);

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

}

function display_review_section($post_id) {
    // Ensure $post_id is provided or fallback to current post
    if (!$post_id) {
        $post_id = get_the_ID();
    }

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

    ob_start();
    ?>
    <section id="review-summary" class="review-summary">
							<div class="review-header">
								<!-- Product Info Section -->
								<div class="product-info">
									<div class="brand-info">
										<svg class="brand-logo" viewBox="0 0 24 24" fill="none" stroke="#1a73e8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect>
											<polyline points="17 2 12 7 7 2"></polyline>
										</svg>
										<span class="brand-name">SAMSUNG</span>
									</div>
									<h6 class="product-title">

                                    <?php echo esc_attr($wp_review_heading); ?>
                                </h6>

									<div class="product-meta">
										<div class="meta-item">
											<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
												<polyline points="22,6 12,13 2,6"></polyline>
											</svg>
											<span class="meta-label">رمز المنتج:</span>
											<span class="meta-value">SM-S924</span>
										</div>
										<div class="meta-item">
											<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
												<line x1="7" y1="7" x2="7.01" y2="7"></line>
											</svg>
											<span class="meta-label">الفئة:</span>
											<span class="meta-value">هواتف ذكية</span>
										</div>
									</div>

									<div class="rating-box">
										<div class="rating-score">9.0</div>
										<div class="rating-label">التقييم النهائي</div>
									</div>
								</div>

								<!-- Product Details Section -->
								<div class="product-details">
									<p class="product-description">
										يعتبر هاتف Samsung Galaxy S24 أحدث إصدارات سامسونج في فئة
										الهواتف الرائدة. يتميز بشاشة Dynamic AMOLED 2X مقاس 6.8 بوصة
										بدقة QHD+ ومعدل تحديث 120 هرتز، مع معالج Snapdragon 8 Gen 3
										الأحدث من كوالكوم. يقدم الهاتف تجربة تصوير متميزة مع نظام
										كاميرات متطور يدعم التصوير بدقة 200 ميجابكسل.
									</p>

									<img src="https://mellow-douhua-911077.netlify.app/includes/images/m3.jpg" alt="Samsung Galaxy S24" class="product-image">

									<div class="holder">
										<div class="price-tag">
											<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a73e8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<line x1="12" y1="1" x2="12" y2="23"></line>
												<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
											</svg>
											<span class="price-value">4,999</span>
										</div>
										<div class="price-tag">
											<span class="price-value">ريال سعودي</span>
										</div>
									</div>
								</div>
							</div>
							<div class="ratings-card">
								<h2 class="ratings-title">التقييمات التفصيلية</h2>

								<div class="rating-item">
									<div class="rating-header">
										<span class="rating-name">الشاشة</span>
										<span class="rating-amount">10/10</span>
									</div>
									<div class="rating-bar">
										<div class="rating-fill" data-progress="90" style="width: 90%;"></div>
									</div>
								</div>

								<div class="rating-item">
									<div class="rating-header">
										<span class="rating-name">البطارية</span>
										<span class="rating-amount">9/10</span>
									</div>
									<div class="rating-bar">
										<div class="rating-fill" data-progress="100" style="width: 100%;"></div>
									</div>
								</div>

								<div class="rating-item">
									<div class="rating-header">
										<span class="rating-name">المعالج ونظام التشغيل</span>
										<span class="rating-amount">10/10</span>
									</div>
									<div class="rating-bar">
										<div class="rating-fill" data-progress="95" style="width: 95%;"></div>
									</div>
								</div>

								<div class="rating-item">
									<div class="rating-header">
										<span class="rating-name">التصميم</span>
										<span class="rating-amount">9.5/10</span>
									</div>
									<div class="rating-bar">
										<div class="rating-fill" data-progress="70" style="width: 70%;"></div>
									</div>
								</div>

								<div class="rating-item">
									<div class="rating-header">
										<span class="rating-name">التصوير</span>
										<span class="rating-amount">9.5/10</span>
									</div>
									<div class="rating-bar">
										<div class="rating-fill" data-progress="90" style="width: 90%;"></div>
									</div>
								</div>

								<div class="rating-item">
									<div class="rating-header">
										<span class="rating-name">التخزين</span>
										<span class="rating-amount">9.5/10</span>
									</div>
									<div class="rating-bar">
										<div class="rating-fill" data-progress="90" style="width: 90%;"></div>
									</div>
								</div>
							</div>
							<div class="props-and-cons pros">
								<div class="props-and-cons-header">
									<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<polyline points="20 6 9 17 4 12"></polyline>
									</svg>
									<h2>مميزات</h2>
								</div>
								<ul>
									<li>
										<span>شاشة AMOLED بدقة عالية وألوان زاهية</span>
									</li>
									<li>
										<span>معالج قوي يوفر أداءً سريعًا وسلسًا</span>
									</li>
									<li>
										<span>كاميرا متقدمة مع دعم تصوير الفيديو بدقة 8K</span>
									</li>
									<li>
										<span>بطارية كبيرة تدعم الشحن السريع والشحن اللاسلكي</span>
									</li>
									<li>
										<span>مقاومة للماء والغبار بمعيار IP68</span>
									</li>
								</ul>
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
								<ul>
									<li>
										<span>سعر مرتفع مقارنة بالمنافسين</span>
									</li>
									<li>
										<span>عدم وجود منفذ سماعات 3.5 ملم</span>
									</li>
									<li>
										<span>حجم الهاتف قد يكون كبيرًا لبعض المستخدمين</span>
									</li>
									<li>
										<span>لا يوجد تحسين كبير في عمر البطارية مقارنة بالإصدارات
											السابقة</span>
									</li>
								</ul>
							</div>
						</section>
    <?php
    return ob_get_clean();
}
