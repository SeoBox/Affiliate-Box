<?php

/*
Plugin Name: Advanced Custom Fields: Product Reviews
Plugin URI: https://github.com/Inkvi/acf-amazon-image
Description: ACF Custom Field Type for Product Reviews
Version: 1.0.5
Author: Alexander Eliseev
*/

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/Inkvi/acf-product-review/',
	__FILE__,
	'acf-product-review'
);

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('accb0e2a7631eb94e30bc4d4ec540e37cce3dca1');

//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');


// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_product_review') ) :

class acf_product_review {
	
	// vars
	var $settings;
	
	
	function __construct() {
		
		// settings
		// - these will be passed into the field class.
		$this->settings = array(
			'version'	=> '1.0.4',
			'url'		=> plugin_dir_url( __FILE__ ),
			'path'		=> plugin_dir_path( __FILE__ )
		);
		
		
		// include field
		add_action('acf/include_field_types', 	array($this, 'include_field')); // v5
	}
	
	function include_field( $version = false ) {
		
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