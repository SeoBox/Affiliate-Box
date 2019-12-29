<?php

/*
Plugin Name: Advanced Custom Fields: Amazon Image
Plugin URI: https://github.com/Inkvi/acf-amazon-image
Description: SHORT_DESCRIPTION
Version: 1.0.0
Author: Alexabder Eliseev
Author URI: AUTHOR_URL
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('inkvi_acf_plugin_amazon_image') ) :

function my_acf_load_field( $field ) {
    print_r("my_acf_load_field");
    var_dump($field);
    return $field;
}
function my_acf_load_value( $field ) {
    print_r("my_acf_load_value");
    return $field;
}
function my_acf_prepare_field( $field ) {
    print_r("my_acf_prepare_field");
    return $field;
}

class inkvi_acf_plugin_amazon_image {
	
	// vars
	var $settings;
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	void
	*  @return	void
	*/
	
	function __construct() {
		
		// settings
		// - these will be passed into the field class.
		$this->settings = array(
			'version'	=> '1.0.0',
			'url'		=> plugin_dir_url( __FILE__ ),
			'path'		=> plugin_dir_path( __FILE__ )
		);
		
		
		// include field
		add_action('acf/include_field_types', 	array($this, 'include_field')); // v5
		add_action('acf/register_fields', 		array($this, 'include_field')); // v4




// add_filter('acf/load_field', 'my_acf_load_field');
// add_filter('acf/load_value  ', 'my_acf_load_value');
// add_filter('acf/prepare_field', 'my_acf_prepare_field');

	}
	
	
	/*
	*  include_field
	*
	*  This function will include the field type class
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	$version (int) major ACF version. Defaults to false
	*  @return	void
	*/
	
	function include_field( $version = false ) {
		
		// load acf-amazon-image
		load_plugin_textdomain( 'acf-amazon-image', false, false);
		
		
		// include
		include_once('fields/class-inkvi-acf-field-asin.php');
		include_once('fields/class-inkvi-acf-field-amazon-image-html.php');
		include_once('fields/class-inkvi-acf-field-amazon-checkout.php');
		include_once('fields/class-inkvi-acf-field-features.php');
	}
	
}


// initialize
new inkvi_acf_plugin_amazon_image();


// class_exists check
endif;
	
?>