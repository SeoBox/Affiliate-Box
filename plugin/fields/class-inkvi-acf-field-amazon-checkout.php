<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('inkvi_acf_field_amazon_checkout') ) :


class inkvi_acf_field_amazon_checkout extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct( $settings ) {
		
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		
		$this->name = 'amazon_checkout';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('Amazon Checkout', 'acf-amazon-checkout');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'basic';
		
		
		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		
		$this->defaults = array(
		);
		
		
		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('amazon_image', 'error');
		*/
		
		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'acf-amazon-checkout'),
		);
		
		
		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/
		
		$this->settings = $settings;
		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field_settings( $field ) {
		
		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/


	}
	
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		/*
		*  Review the data of $field.
		*  This will show what data is available
		*/
//         preg_match('/.*row-(\d+)/', $field['prefix'], $matches);
//         $repeater_row_index = $matches[1];
//
//         $field_name = "asins_".$repeater_row_index."_asin";
// 		echo do_shortcode("[amazon_link asins='".get_field($field_name)."' template='DetailPageLink' store='camping023-20' marketplace='US']");
	}
	

	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
		
	function format_value( $value, $post_id, $field ) {
        preg_match('/(.*_\d+_).*/', $field['name'], $matches);
        $field_name = $matches[1]."asin";
		$url = do_shortcode("[amazon_link asins='".acf_get_metadata($post_id, $field_name)."' template='DetailPageLink' store='camping023-20' marketplace='US']");

        # amazon plugin inserts an img tag as a pixel
        preg_match('/<img.*>(.*)/', $url, $matches);
        $url = trim($matches[1]);

$value = <<<EOL
<div class="elementor-element elementor-button-danger elementor-align-center elementor-widget elementor-widget-button" data-element_type="widget" data-widget_type="button.default">
    <div class="elementor-widget-container">
        <div class="elementor-button-wrapper">
			<a href="$url" class="elementor-button-link elementor-button elementor-size-sm" role="button">
                <span class="elementor-button-content-wrapper">
                    <span class="elementor-button-text" style="color: #fff;">Show me price</span>
		        </span>
            </a>
		</div>
    </div>
</div>
EOL;

		return $value;

    }


	
}


// initialize
new inkvi_acf_field_amazon_checkout( $this->settings );


// class_exists check
endif;

?>