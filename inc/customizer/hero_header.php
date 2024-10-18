<?php

class hero_header {
	public function __construct() {
		add_action( 'customize_register', array( $this, 'register_customize_hero_header_sections' ) );
	}
	public function register_customize_hero_header_sections( $wp_customize ) {
        /*
        * Add settings to sections.
        */
        $this->hero_header_callout_section( $wp_customize );
    }

    /* Sanitize Inputs */
    public function sanitize_custom_option($input) {
        return ( $input === "No" ) ? "No" : "Yes";
    }
    public function sanitize_custom_text($input) {
        return filter_var($input, FILTER_SANITIZE_STRING);
    }
    public function sanitize_custom_url($input) {
        return filter_var($input, FILTER_SANITIZE_URL);
    }
    public function sanitize_custom_email($input) {
        return filter_var($input, FILTER_SANITIZE_EMAIL);
    }
    public function sanitize_hex_color( $color ) {
        if ( '' === $color ) {
            return '';
        }

        // 3 or 6 hex digits, or the empty string.
        if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
            return $color;
        }
    }

    /* hero_header Section */
    private function hero_header_callout_section( $wp_customize ) {
		// New panel for "Layout".
        $wp_customize->add_section('basic-hero_header-callout-section', array(
            'title' => 'hero header',
            'priority' => 2,
            'description' => __('hero header  section is  displayed on the Blog Hero header.', 'Borma'),
        ));




#  Image
$wp_customize->add_setting("basic-hero_header-callout-image", array(
    'default' => 'red',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => array( $this, 'sanitize_custom_url' )
));

$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "basic-hero_header-callout-image", array(
    'label' => "background",
    'section' => 'basic-hero_header-callout-section',
    'settings' => 'basic-hero_header-callout-image',

)));



/* Title */

$wp_customize->add_setting('basic-hero_header-callout-title', array(
    'default' => '',
    'sanitize_callback' => array( $this, 'sanitize_custom_text' )
));
$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'basic-hero_header-callout-title', array(
    'label' => 'title',
    'section' => 'basic-hero_header-callout-section',
    'settings' => 'basic-hero_header-callout-title',
    'type' => 'text'
)));







/* description */

$wp_customize->add_setting('basic-hero_header-callout-description', array(
    'default' => '',
    'sanitize_callback' => array( $this, 'sanitize_custom_text' )
));
$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'basic-hero_header-callout-description', array(
    'label' => 'description',
    'section' => 'basic-hero_header-callout-section',
    'settings' => 'basic-hero_header-callout-description',
    'type' => 'textarea'
)));












    }




}