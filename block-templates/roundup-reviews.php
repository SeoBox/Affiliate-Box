<?php
if ( $is_preview ) {
	echo "This block will be replaced with a list of product reviews";

	return;
}

$description_type = get_field( "description_type" );
$pros_cons_type   = get_field( "pros_cons_type" );
$features_type    = get_field( "features_type" );

function get_template_id( $description_type, $pros_cons_type, $features_type ) {

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

	return implode( "_", $fields );
}

$template_id = get_template_id( $description_type, $pros_cons_type, $features_type );

$elementor_templates = get_field( "elementor_templates", 'option' );
foreach ( $elementor_templates as $template ) {

	if ( $template_id == get_template_id( $template['description_type'], $template['pros_cons_type'], $template['features_type'] ) ) {
		echo do_shortcode( $template['shortcode'] );
	}
}
?>
