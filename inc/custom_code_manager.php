<?php
// Add admin menu for custom code
function custom_code_menu() {
    add_menu_page(
        'Head & Footer',       // Page title
        'Head & Footer',       // Menu title
        'administrator',     // Capability
        'custom_code',       // Menu slug
        'custom_code_page',  // Function to display the settings page
        'dashicons-editor-code'  // Icon
    );
}
add_action('admin_menu', 'custom_code_menu');

// Create the custom code settings page
function custom_code_page() {
    ?>
    <div class="wrap">
        <h1>Custom Code for Head and Footer</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_code_options_group');
            do_settings_sections('custom_code');
            ?>
            <h2>Head Section Code</h2>
            <textarea name="custom_code_textarea" rows="10" cols="50"><?php echo esc_textarea(get_option('custom_code_textarea')); ?></textarea>
            
            <h2>Footer Section Code</h2>
            <textarea name="custom_code_footer_textarea" rows="10" cols="50"><?php echo esc_textarea(get_option('custom_code_footer_textarea')); ?></textarea>
            
            <p class="submit">
                <input type="submit" name="submit" class="button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}

// Register settings to save custom code
function custom_code_settings() {
    register_setting('custom_code_options_group', 'custom_code_textarea'); // For head
    register_setting('custom_code_options_group', 'custom_code_footer_textarea'); // For footer
}
add_action('admin_init', 'custom_code_settings');

// Output the custom code in the head section
function insert_custom_code_in_head() {
    $custom_code = get_option('custom_code_textarea');
    if ($custom_code) {
        echo $custom_code; // Output the custom code in the head section
    }
}
add_action('wp_head', 'insert_custom_code_in_head');

// Output the custom code in the footer section
function insert_custom_code_in_footer() {
    $custom_footer_code = get_option('custom_code_footer_textarea');
    if ($custom_footer_code) {
        echo $custom_footer_code; // Output the custom code in the footer section
    }
}
add_action('wp_footer', 'insert_custom_code_in_footer');
