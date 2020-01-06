<?php
if ( $is_preview ) {
	echo "This block will be replaced with a list of product reviews";

	return;
}
$description_type = get_field( "description_type" );
$pros_cons_type   = get_field( "pros_cons_type" );
$features_type    = get_field( "features_type" );

$option_page_field_name = $description_type . "_" . $pros_cons_type;
if ( $features_type != "none" ) {
	$option_page_field_name .= "_{$features_type}";
}
$elementor_templates = get_field( "elementor_templates", 'option' );
if ( $elementor_templates ) {
	echo do_shortcode( $elementor_templates[ $option_page_field_name ] );
}
?>
