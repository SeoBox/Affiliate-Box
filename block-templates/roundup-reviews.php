<?php
if ( $is_preview ) {
	echo "This block will be replaced with a list of product reviews";

	return;
}

if (!function_exists("get_elementor_template_id")) {
	function get_elementor_template_id( $acf_fields ) {
		$description_type = $acf_fields['description_type'] ?? "none";
		$pros_cons_type   = $acf_fields['pros_cons_type'] ?? "none";
		$features_type    = $acf_fields['features_type'] ?? "none";
		$review_link      = $acf_fields['review_link'] ?? "none";

		$fields = array();
		if ( $description_type != "none" ) {
			array_push( $fields, $description_type );
		}

		if ( $pros_cons_type != "none" ) {
			array_push( $fields, $pros_cons_type );
		}
		if ( $features_type != "none" ) {
			array_push( $fields, $features_type );
		}

		if ( $review_link != "none" ) {
			array_push( $fields, $review_link );
		}

		return implode( "_", $fields );
	}
}

$template_id = get_elementor_template_id( get_fields() );

$elementor_templates = get_field( "elementor_templates", 'option' );
foreach ( $elementor_templates as $template ) {
	if ( $template_id == get_elementor_template_id( $template ) ) {
		echo do_shortcode( $template['shortcode'] );
	}
}
?>
