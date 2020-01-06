<?php

/*
Plugin Name: Advanced Custom Fields: Product Reviews
Plugin URI: https://github.com/Inkvi/acf-amazon-image
Description: ACF Custom Field Type for Product Reviews
Version: 1.0.8
Author: Alexander Eliseev
*/

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/Inkvi/acf-product-review/',
	__FILE__,
	'acf-product-review'
);

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication( 'accb0e2a7631eb94e30bc4d4ec540e37cce3dca1' );

//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch( 'master' );

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function get_amazon_url( $asin ) {
	$tag = get_field( 'amazon_affiliate_settings.associate_id', 'option' );
	$url = "http://www.amazon.com/dp/" . $asin . "/ref=nosim?tag=$tag";
	return $url;
}

include_once( 'acf-product-review-blocks.php' );


/**
 * Copy predefined AALB templates to a WP upload folder.
 */
function aalb_copy_templates_to_uploads_dir() {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	try {
		WP_Filesystem();
		global $wp_filesystem;

		$upload_dir = wp_upload_dir();
		$upload_dir = $wp_filesystem->find_folder( $upload_dir['basedir'] );

		require_once ABSPATH . "wp-content/plugins/amazon-associates-link-builder/amazon-associates-link-builder.php";
		$template_upload_path = $upload_dir . AALB_TEMPLATE_UPLOADS_FOLDER;

		if ( ! $wp_filesystem->is_dir( $template_upload_path ) && ! aalb_create_dir( $template_upload_path ) ) {
			return false;
		}
		copy_dir(plugin_dir_path( __FILE__ )."aalb-templates", $template_upload_path);
	} catch ( Exception $e ) {
		error_log( 'Unable to remove templates uploads directory. Failed with the Exception ' . $e->getMessage() );
	}
}

// check if class already exists
if ( ! class_exists( 'acf_product_review' ) ) :

	class acf_product_review {

		// vars
		var $settings;


		function __construct() {

			// settings
			// - these will be passed into the field class.
			$this->settings = array(
				'version' => '1.0.6',
				'url'     => plugin_dir_url( __FILE__ ),
				'path'    => plugin_dir_path( __FILE__ )
			);

//			TODO: throws warnings in prod
//			include_once( 'includes/plugin.php' );
//			rest api needs a different path
//			include_once( 'wp-admin/includes/plugin.php' );
//			if ( is_plugin_inactive( "amazon-associates-link-builder/amazon-associates-link-builder.php" ) ) {
//				return;
//			}

			aalb_copy_templates_to_uploads_dir();


			// include field
			add_action( 'acf/include_field_types', array( $this, 'include_field' ) );
			add_action( 'acf/init', array( $this, 'acf_init' ) );

		}


		function acf_init() {
			if ( function_exists( 'acf_add_options_page' ) ) {

				$parent = acf_add_options_page( array(
					'menu_title' => 'Product Review Settings',
					'menu_slug'  => 'product-review-settings',
					'capability' => 'manage_options',
					'redirect'   => true,
				) );

				acf_add_options_sub_page( array(
					'page_title'  => 'General Settings',
					'menu_title'  => 'General Settings',
					'parent_slug' => $parent['menu_slug'],
				) );
				acf_add_options_sub_page( array(
					'page_title'  => 'Affiliate Settings',
					'menu_title'  => 'Affiliate Settings',
					'parent_slug' => $parent['menu_slug'],
				) );
			}
		}

		function include_field( $version = false ) {
			include_once( 'fields/acf-field-product-review.php' );
			include_once( 'fields/acf-field-features.php' );
		}

	}


// initialize
	new acf_product_review();

// class_exists check
endif;

?>