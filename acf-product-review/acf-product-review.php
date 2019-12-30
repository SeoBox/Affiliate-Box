<?php

/*
Plugin Name: Advanced Custom Fields: Product Reviews
Plugin URI: https://github.com/Inkvi/acf-amazon-image
Description: ACF Custom Field Type for Product Reviews
Version: 1.0.1
Author: Alexander Eliseev
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_product_review') ) :

class acf_product_review {
	
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
			'version'	=> '1.0.1',
			'url'		=> plugin_dir_url( __FILE__ ),
			'path'		=> plugin_dir_path( __FILE__ )
		);
		
		
		// include field
		add_action('acf/include_field_types', 	array($this, 'include_field')); // v5
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
// 		load_plugin_textdomain( 'acf-amazon-image', false, false);
		
		
		// include
		include_once('fields/acf-field-product-review.php');
		include_once('fields/acf-field-features.php');
	}
	
}


// initialize
new acf_product_review();


// class_exists check
endif;
	
?>