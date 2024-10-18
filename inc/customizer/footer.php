<?php

class footerPlus {
	public function __construct() {
		add_action( 'customize_register', array( $this, 'register_customize_footerPlus_sections' ) );
	}
	public function register_customize_footerPlus_sections( $wp_customize ) {
        /*
        * Add settings to sections.
        */
        $this->footerPlus_callout_section( $wp_customize );

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

    /* footerPlus Section */
    private function footerPlus_callout_section( $wp_customize ) {
		// New panel for "Layout".
        $wp_customize->add_section('basic-footerPlus-callout-section', array(
            'title' => 'footerPlus',
            'priority' => 2,
            'description' => __(' footerPlus  section is  displayed on the Blog footerPlus.', 'Borma'),
        ));




/*  ------Social Media links------ */



$social_media = ["linkedin","tiktok","youtube","twitter","instagram"];


foreach ($social_media as $brand) {

$wp_customize->add_setting("basic-footerPlus-callout-$brand", array(
    'default' => '',
    'sanitize_callback' => array( $this, 'sanitize_custom_url' )
));
$wp_customize->add_control(new WP_Customize_Control($wp_customize, "basic-footerPlus-callout-$brand", array(
    'label' => "$brand",
    'section' => 'basic-footerPlus-callout-section',
    'settings' => "basic-footerPlus-callout-$brand",
    'type' => 'text'
)));

}










    }





}