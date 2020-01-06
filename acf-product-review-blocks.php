<?
add_action( 'acf/init', 'acfpr_acf_init' );
function acfpr_acf_init() {

	if ( function_exists( 'acf_register_block' ) ) {

		acf_register_block( array(
			'name'            => 'roundup-summary',
			'title'           => 'Roundup Review Summary',
			'description'     => 'A small block to summarize reviewed products',
			'render_template' => plugin_dir_path( __FILE__ ) . 'block-templates/roundup-summary.php',
			'category'        => 'acfpr-blocks',
			'icon'            => 'text',
			'post_types'      => array( 'post', 'page' ),
			'keywords'        => array( 'roundup', 'review', "product" ),
		) );

		acf_register_block( array(
			'name'            => 'roundup-reviews',
			'title'           => 'Roundup Reviews',
			'description'     => 'Shows products reviews',
			'render_template' => plugin_dir_path( __FILE__ ) . 'block-templates/roundup-reviews.php',
			'category'        => 'acfpr-blocks',
			'icon'            => 'star-half',
			'post_types'      => array( 'post', 'page' ),
			'keywords'        => array( 'roundup', 'review', "product" ),
		) );
	}
}

// Add Custom Blocks Panel in Gutenberg
function acfpr_block_categories( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'acfpr-blocks',
				'title' => __( 'Product Reviews', 'acfpr-blocks-master' ),
			),
		)
	);
}
add_filter( 'block_categories', 'acfpr_block_categories', 10, 2 );


add_filter( 'acf/settings/save_json', 'acf_product_review_json_save_point' );
function acf_product_review_json_save_point( $acf_product_review_path ) {

	// update path
	$acf_product_review_path = plugin_dir_path( __FILE__ ) . 'acf-json';

	// return
	return $acf_product_review_path;

}

add_filter( 'acf/settings/load_json', 'acf_product_review_json_load_point' );
function acf_product_review_json_load_point( $acf_product_review_path ) {
	// remove original path (optional)
	unset( $acf_product_review_path[0] );

	// append path
	$acf_product_review_path[] = plugin_dir_path( __FILE__ ) . 'acf-json';
//	var_dump($acf_product_review_path);

	// return
	return $acf_product_review_path;

}
?>