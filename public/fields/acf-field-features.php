<?php

// exit if accessed directly
if (!defined('ABSPATH')) exit;


// check if class already exists
if (!class_exists('acf_product_reviews_field_features')) :


    class acf_product_reviews_field_features extends acf_field
    {


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

        function __construct()
        {

            /*
            *  name (string) Single word, no spaces. Underscores allowed
            */

            $this->name = 'features';


            /*
            *  label (string) Multiple words, can include spaces, visible when selecting a field type
            */

            $this->label = __('Features', 'acf-features');


            /*
            *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
            */

            $this->category = 'Product Review';


            /*
            *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
            */

            $this->defaults = array();


            // do not delete!
            parent::__construct();

        }

        function render_field_settings($field)
        {

            /*
            *  acf_render_field_setting
            *
            *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
            *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
            *
            *  More than one setting can be added by copy/paste the above code.
            *  Please note that you must also have a matching $defaults value for the field name (font_size)
            */

            acf_render_field_setting($field, array(
                'label' => __('Features', 'acf-features'),
                'instructions' => __('Features of the product', 'acf-features'),
                'type' => 'textarea',
                'name' => 'features',
            ));

            acf_render_field_setting($field, array(
                'label' => __('Color', 'acf-features'),
                'instructions' => __('Hex Color of the bullet point icons', 'acf-features'),
                'type' => 'color_picker',
                'name' => 'color',
            ));

            acf_render_field_setting($field, array(
                'label' => __('Format', 'acf-features'),
                'instructions' => __('Structure format of features', 'acf-features'),
                'name' => 'structure',
                'type' => 'radio',
                'default_value' => 'lines',
                'layout' => 'horizontal',
                'choices' => array(
                    'lines' => __("Lines", 'acf-product-review'),
                    'separated' => __("Separated", 'acf-product-review'),
                )

            ));

        }

        function render_field($field)
        {
            $atts = array();
            $keys = array('id', 'class', 'name', 'value', 'placeholder', 'rows', 'maxlength');
            $keys2 = array('readonly', 'disabled', 'required');


            // rows
            if (!isset($field['rows'])) {
                $field['rows'] = 8;
            }


            // atts (value="123")
            foreach ($keys as $k) {
                if (isset($field[$k])) $atts[$k] = $field[$k];
            }


            // atts2 (disabled="disabled")
            foreach ($keys2 as $k) {
                if (!empty($field[$k])) $atts[$k] = $k;
            }


            // remove empty atts
            $atts = acf_clean_atts($atts);


            // return
            acf_textarea_input($atts);
        }


        function startsWith($string, $startString)
        {
            $len = strlen($startString);
            return (substr($string, 0, $len) === $startString);
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

        function format_value($value, $post_id, $field)
        {
            $features = explode("\n", $value);
            if ($field['structure'] == "lines") {
                $color = get_field("{$field['_name']}_settings", 'option')["{$field['_name']}_features_color"] ?? '';
                $icon = get_field("{$field['_name']}_settings", 'option')["{$field['_name']}_icon"] ?? '';

                if (!$this->startsWith($color, "#")) {
                    $color = "#" . $color;
                }
                $html = '<ul class="elementor-icon-list-items">';
                foreach ($features as $feature) {
                    $feature = trim($feature);
                    if (empty($feature)) {
                        continue;
                    }
                    $chevron = <<<CHE
<span class="elementor-icon-list-icon">
    <i aria-hidden="true" class="$icon" style='color: $color'></i>
</span>
CHE;
                    $html .= "<li class='elementor-icon-list-item'>" . $chevron . $feature . "</li>";
                }
                $html .= "</ul>";
                return $html;
            }

            if ($field['structure'] == "separated") {
                $html = "<div class='flex-container space-around wrap'>";

                foreach ($features as $feature) {
                    $feature = trim($feature, ". \t\n\r\0\x0B");
                    if (empty($feature)) {
                        continue;
                    }
                    list($name, $value) = explode(":", $feature);
                    if ($value){
                        $html .= "<span class=\"flex-item\"><b>$name</b>: $value</span>";
                    } else {
                        $html .= "<span class=\"flex-item\"><b>$name</b></span>";
                    }
                }
                $html .= "</div>";
                return $html;
            }

        }
    }


// initialize
    new acf_product_reviews_field_features();


// class_exists check
endif;

?>