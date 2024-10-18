<?php

class logos {
	public function __construct() {
		add_action( 'customize_register', array( $this, 'register_customize_logos_sections' ) );
	}
	public function register_customize_logos_sections( $wp_customize ) {
        /*
        * Add settings to sections.
        */
        $this->logos_callout_section( $wp_customize );
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

    /* logos Section */
    private function logos_callout_section( $wp_customize ) {
		// New panel for "Layout".
        $wp_customize->add_section('basic-logos-callout-section', array(
            'title' => 'logos size',
            'priority' => 2,
            'description' => __('logos size  section is  displayed on the Blog logos size.', 'Borma'),
        ));



#   smaller logo 100 x 100
$wp_customize->add_setting("basic-logos-callout-logo_100_x_100", array(
    'default' => 'red',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => array( $this, 'sanitize_custom_url' )
));

$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "basic-logos-callout-logo_100_x_100", array(
    'label' => "smaller logo 100 x 100",
    'section' => 'basic-logos-callout-section',
    'settings' => 'basic-logos-callout-logo_100_x_100',

)));



#   medium logo
$wp_customize->add_setting("basic-logos-callout-medium_logo", array(
    'default' => 'red',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'sanitize_callback' => array( $this, 'sanitize_custom_url' )
));

$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "basic-logos-callout-medium_logo", array(
    'label' => "medium logo  300 x 400",
    'section' => 'basic-logos-callout-section',
    'settings' => 'basic-logos-callout-medium_logo',

)));




    }




}