<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('inkvi_acf_field_features') ) :


class inkvi_acf_field_features extends acf_field {
	
	
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
		
		$this->name = 'features';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('Product Features', 'acf-features');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'Review';
		
		
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
			'error'	=> __('Error! Please enter a higher value', 'acf-features'),
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

		acf_render_field_setting( $field, array(
			'label'			=> __('Features','acf-features'),
			'instructions'	=> __('Features of the product','acf-features'),
			'type'			=> 'textarea',
			'name'			=> 'features',
		));

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
		$atts = array();
		$keys = array( 'id', 'class', 'name', 'value', 'placeholder', 'rows', 'maxlength' );
		$keys2 = array( 'readonly', 'disabled', 'required' );


		// rows
		if( !$field['rows'] ) {
			$field['rows'] = 8;
		}


		// atts (value="123")
		foreach( $keys as $k ) {
			if( isset($field[ $k ]) ) $atts[ $k ] = $field[ $k ];
		}


		// atts2 (disabled="disabled")
		foreach( $keys2 as $k ) {
			if( !empty($field[ $k ]) ) $atts[ $k ] = $k;
		}


		// remove empty atts
		$atts = acf_clean_atts( $atts );


		// return
		acf_textarea_input( $atts );
	}


	
	function load_value( $value, $post_id, $field ) {
		return $value;
		
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
        $features = explode("\n", $value);
        $color = $field['_name'] == "pros" ? "23A455": "CE4325";
        $html = '<ul class="elementor-icon-list-items">';
        foreach($features as $feature) {
            $features = trim($feature);
            if (empty($feature)) {
                continue;
            }
// possible chevrons: fa-chevron-circle-right fa-chevron-right
$chevron = <<<CHE
<span class="elementor-icon-list-icon">
    <i aria-hidden="true" class="fas fa-angle-double-right" style='color: #$color'></i>
</span>
CHE;
            $html .= "<li class='elementor-icon-list-item'>".$chevron.$feature."</li>";
        }
        $html .= "</ul>";
		return $html;
	}
}


// initialize
new inkvi_acf_field_features( $this->settings );


// class_exists check
endif;

?>