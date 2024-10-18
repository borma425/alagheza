<?php

// Hook to add the meta box
add_action('add_meta_boxes', 'add_tips_meta_box');

function add_tips_meta_box() {
    add_meta_box(
        'tips_meta_box', // Unique ID for the meta box
        'نصائج مفيدة', // Title of the meta box
        'render_tips_meta_box', // Callback function to render the meta box
        'post', // Post type where the meta box should appear
        'side', // Context (side, normal, advanced)
        'high' // Priority (high, default, low)
    );
}

function render_tips_meta_box($post) {

    $style = '
    <style>
        .tips-meta-box {
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            color: #333;
        }
        .tips-meta-box h4 {
            font-size: 1.2em;
            margin-bottom: 10px;
            color: #0073aa; /* WordPress blue */
        }
        .tips-meta-box ul {
            list-style: none;
            padding: 0;
        }
        .tips-meta-box li {
            margin: 8px 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .tips-meta-box strong {
            background-color: #e7f3fe; /* Light blue background */
            color: #31708f; /* Darker blue text */
            padding: 4px 6px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }
        .tips-meta-box strong:hover {
            background-color: #d9edf7; /* Slightly darker on hover */
            color: #245269; /* Darker text on hover */
        }
    </style>
';

// Output the styles and tips content
echo $style;


    // Add your tips or information here
    echo '<div class="tips-meta-box">';
    echo '<h4>مساعد الإحنصارات</h4>';
    echo '<ul>';
    echo '<li>استخدم <strong onclick="copyToClipboard(this)">[specifications_section]</strong> لإظهار جدول المواصفات </li>';
    echo '<br><li>استخدم <strong onclick="copyToClipboard(this)">[qa_section]</strong> لإظهار جدول  أسئلة & أجوبة</li>';
    echo '<br><li>استخدم <strong onclick="copyToClipboard(this)">[more_products_prices_section]</strong> لإظهار جدول  خطط الأسعار</li>';
    echo '<br><li>استخدم <strong onclick="copyToClipboard(this)">[pros_cons_section]</strong> لإظهار جدول المميزات والعيوب</li>';
    echo '<br><li>استخدم <strong onclick="copyToClipboard(this)">[review_section]</strong> لإظهار جدول  ملخص المراجعة</li>';

    echo '</ul>';
    echo '</div>';

    // Inline JavaScript to handle copying to clipboard
    echo '<script>
        function copyToClipboard(element) {
            // Create a range and select the text
            const range = document.createRange();
            range.selectNode(element);
            window.getSelection().removeAllRanges(); // Clear current selections
            window.getSelection().addRange(range); // Select the text
            try {
                // Copy the text
                document.execCommand("copy");
                alert("تم نسخ: " + element.innerText); // Optional: show a message
            } catch (err) {
                console.error("Could not copy text: ", err);
            }
            window.getSelection().removeAllRanges(); // Deselect the text
        }
    </script>';
}

