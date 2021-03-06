<?php

// exit if accessed directly
if (!defined('ABSPATH')) exit;


// check if class already exists
if (!class_exists('affiliate_box_field_asin')) :


    class affiliate_box_field_asin extends acf_field
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

            $this->name = 'product_review';


            /*
            *  label (string) Multiple words, can include spaces, visible when selecting a field type
            */

            $this->label = __('Product', 'acf-product-review');


            /*
            *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
            */

            $this->category = 'Product Review';


            /*
            *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
            */

            $this->defaults = array(
                'return_format' => 'ASIN',
                'asin-field' => 'asin',
                'asin-repeater-field' => 'reviews',
            );


            $this->associatesTag = get_field('amazon_affiliate_settings', 'option')['associate_id'] ?? '';

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
                'label' => __('ASIN', 'acf-product-review'),
                'instructions' => __('ASIN for an Amazon product', 'acf-product-review'),
                'type' => 'text',
                'name' => 'asin',
            ));

            acf_render_field_setting($field, array(
                'label' => __('ASIN Field Name', 'acf-product-review'),
                'instructions' => __('ACF Field Name containing ASIN', 'acf-product-review'),
                'type' => 'text',
                'name' => 'asin-field',
            ));

            acf_render_field_setting($field, array(
                'label' => __('Repeater Field Name', 'acf-product-review-repeater-name'),
                'instructions' => __('ACF Repeater Field Name', 'acf-product-review-repeater-name'),
                'type' => 'text',
                'name' => 'asin-repeater-field',
            ));

            acf_render_field_setting($field, array(
                'label' => __('Return Format', 'acf-product-review'),
                'instructions' => '',
                'type' => 'radio',
                'name' => 'return_format',
                'layout' => 'horizontal',
                'choices' => array(
                    'ASIN' => __("ASIN", 'acf-product-review'),
                    'image_html' => __("Image HTML", 'acf-product-review'),
                    'page_link' => __("Page Link", 'acf-product-review'),
                    'title' => __("Title", 'acf-product-review'),
                    'button' => __("Checkout button", 'acf-product-review')
                )
            ));

        }


        function render_asin_field($field)
        {
            $html = '';
            $input_attrs = array();
            foreach (array('value', 'required', "name") as $k) {
                if (isset($field[$k])) {
                    $input_attrs[$k] = $field[$k];
                }
            }
            $html .= '<div class="acf-input-wrap">' . acf_get_text_input(acf_filter_attrs($input_attrs)) . '</div>';
            echo $html;
        }

        public function render_image_field($field)
        {
            preg_match('/.*row-(\d+)/', $field['prefix'], $matches);
            if (sizeof($matches) < 2) {
                return;
            }
            $repeater_row_index = $matches[1];

            $field_name = $field['asin-repeater-field'] . "_" . $repeater_row_index . "_" . $field['asin-field'];
            $asin = get_field($field_name, false, false);
            if ($field['value']) {
                $this->render_asin_field($field);
                echo '<br/><img src="' . $field['value'] . '" style="display: block; margin: 0 auto;"/>';
            } elseif (!empty($asin) && Amazon::isAsin($asin)) {
                $images = Amazon::get_images($asin);
                if ($images) {
                    echo '<img src="' . $images['small'] . '" style="display: block; margin: 0 auto;"/>';
                }
            } else {
                $this->render_asin_field($field);
            }
        }


        function render_title_field($field)
        {
            return $this->render_asin_field($field);
        }


        function render_field($field)
        {
            if ($field['return_format'] == "ASIN") {
                $this->render_asin_field($field);
            }

            if ($field['return_format'] == "image_html") {
                $this->render_image_field($field);
            }

            if ($field['return_format'] == "title") {
                $this->render_title_field($field);
            }
            if ($field['return_format'] == "button") {
                $cta_text = get_field('amazon_affiliate_settings', 'option')['cta_text'] ?? 'Show Me Price';;
                echo '<a class="acf-button button button-primary" href="#">' . $cta_text . '</a>';
            }
        }

        function load_value($value, $post_id, $field)
        {
            return $value;

        }

        function format_value($value, $post_id, $field)
        {
//            var_dump("format_value", $field['name'], $field['return_format']);
            if ($field['return_format'] == "ASIN") {
                return $value;
            }

            preg_match('/(.*_\d+_).*/', $field['name'], $matches);
            $field_name = $matches[1] . $field['asin-field'];
            $asin = acf_get_metadata($post_id, $field_name);
            $isAsin = Amazon::isAsin($asin);

            if ($field['return_format'] == "image_html") {
                $title = acf_get_metadata($post_id, $matches[1] . "title");
	            $image_height = get_field('image_height');
	            $image_width = get_field('image_width');
	            $img_attributes = "";
	            if ($image_height) {
		            $img_attributes .= 'max-height:' . $image_height . 'px;';
	            }
	            if ($image_width) {
		            $img_attributes .= 'max-width:' . $image_width . 'px;';
	            }

                if ($isAsin) {
                    $images = Amazon::get_images($asin);
                    $url = Amazon::get_amazon_url($asin);
                    if ($images) {
                        return '<a rel="nofollow" href="' . $url . '"><img alt="' . $title . '" src="' . $images['medium'] . '" srcset="' . $images['large'] . '" style="'.$img_attributes.'display: block; margin: 0 auto;"/></a>';
                    }
                } else {
                    $url = $asin;
                    return '<a rel="nofollow" href="' . $url . '"><img '.$img_attributes.' alt="' . $title . '" src="' . $value . '" srcset="' . $value . '" style="'.$img_attributes.'display: block; margin: 0 auto;"/></a>';
                }
            }

            if ($field['return_format'] == "title") {
                if ($isAsin) {
                    $url = Amazon::get_amazon_url($asin);
                } else {
                    $url = $asin;
                }

                $heading = "<a rel=\"nofollow\" href=\"$url\">$value</a>";
                return $heading;
            }

            if ($field['return_format'] == "button") {
                if ($isAsin) {
                    preg_match('/(.*_\d+_).*/', $field['name'], $matches);
                    $field_name = $matches[1] . $field['asin-field'];
                    $url = Amazon::get_amazon_url(acf_get_metadata($post_id, $field_name));
                    $cta_text = get_field('amazon_affiliate_settings', 'option')['cta_text'] ?? 'Show Me Price';;
                } else {
                    $url = $asin;
                    $cta_text = get_field('affiliate_settings', 'option')['cta_text'] ?? 'Show Me Price';;
                }
                $text_color = get_field("button_settings", 'option')["text_color"] ?? '#fff';
                $bg_color = get_field("button_settings", 'option')["background_color"] ?? '';
                $css_classes = "btn-danger";
                if (!empty($bg_color)) {
                    $css_classes = "";
                }


                $value = <<<EOL
<div type="button" class="elementor-element elementor-button-danger elementor-align-center btn $css_classes" style="background: $bg_color;">
			<a rel="nofollow" href="$url" class="elementor-button">
                <span style="color: $text_color;">$cta_text</span>
            </a>
</div>
EOL;
                return $value;
            }
        }
    }


    new affiliate_box_field_asin();


endif;

?>