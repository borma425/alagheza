<?php

require_once __DIR__ . '/vendor/autoload.php';

define('DEV_MODE', false);

// Initialize Timber.
Timber\Timber::init();

require(get_theme_file_path('inc/view-page-source.php'));

require(get_theme_file_path('inc/setup.php'));
require(get_theme_file_path('inc/enqueues.php'));
require(get_theme_file_path('inc/shortcodes.php'));
require(get_theme_file_path('inc/custom_post_type.php'));


if( is_admin() ) {
    require(get_theme_file_path('inc/customizer/main.php'));
    require(get_theme_file_path('inc/acf/main.php'));

}










function block_comments_with_links( $commentdata ) {
    // Check if the comment contains a URL (HTTP or HTTPS)
    if ( preg_match( '/https?:\/\/[^\s]+/i', $commentdata['comment_content'] ) ) {
        // Escape the error message to prevent XSS
        $error_message = __( 'عذرا، التعليقات التي تحتوي على روابط غير مسموح بها.', 'alagheza Theme' );

        // Custom HTML for the error message, avoiding inline styles for security
        $styled_message = sprintf(
            '<div class="comment-error-message">%s</div>',
            esc_html( $error_message )
        );
        
        // Enqueue custom CSS (optional but modern practice)
        add_action('wp_footer', function() {
            echo '<style>
                .comment-error-message {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 5px;
                    font-family: Arial, sans-serif;
                    text-align: center;
                }
            </style>';
        });

        // Display the styled message and stop comment submission
        wp_die( $styled_message );
    }

    return $commentdata;
}
add_filter( 'preprocess_comment', 'block_comments_with_links' );
add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
    if (is_paged() && is_single()) {
        return false; // Prevent redirect on paginated single pages.
    }
    return $redirect_url;
}, 10, 2);